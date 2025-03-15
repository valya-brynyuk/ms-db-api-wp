<?php
/*
Plugin Name: Hosting Tools
Description: Manage your Server options through WordPress, and control backend functionality
Version: 2.4.4
Author: 34SP.com
Author URI: https://www.34SP.com
*/

/**
* Main TFSP Tools Class
*
* @since 1.0.0
*/
class TFSP_Tools{

    /**
     * __construct
     *
     * @access public
     * @since 1.00
     * @return null
     */
    public function __construct(){
        defined( 'ABSPATH' ) or die();

        // Global parameter for socket connection in seconds:
        define("TFSP_SOCKET_TIMEOUT", 5);
        preg_match("/^\/var\/www\/vhosts\/(?P<domain>.+)\/httpdocs\/wp-content/A", __DIR__, $tfsp_domain_regexp);
        define ('TFSP_HOME_DOMAIN', $tfsp_domain_regexp['domain']);
        define('TFSP_TOOLs_DIR', plugin_dir_path(__FILE__));
        // include dependancies from classes dir
        foreach (glob(TFSP_TOOLs_DIR ."classes/*.php") as $filename)
        {
            require_once $filename;
        }
        // include cli.php if using wp-cli and register command
        if( defined( 'WP_CLI' ) && WP_CLI == true )
        {
          //Require CLI if required
          $cli_path = TFSP_TOOLs_DIR .'cli.php';
       	  require_once $cli_path;
          WP_CLI::add_command('hosting', 'Hosting');
        }

        add_action('init', array(&$this, 'init'));

    }

    /**
     * Load Plugin hooks
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function init(){
        $admin = new tfsp_admin_screens();

        add_action('admin_init', array(&$admin, 'admin_init'));

        // Add WP Hooks specifically for menu page
        add_action('admin_menu', array(&$admin, 'setup_developer_menu'));

        // Add nocache headers to whitelisted URLS
        add_action( 'send_headers', array(&$this, 'tfsp_headers'));

        // Add fail2ban action:
        add_action('wp_login_failed', array(&$this, 'fail2ban_login_failed'));

        // Add temp link warning
        add_action( 'admin_bar_menu', array(&$this, 'templink_notification'));

        // Add mail fixer filter
        add_filter( 'wp_mail_from', array(&$this, 'from_mail') );

        // Disable email for updates
        add_filter( 'auto_core_update_send_email', '__return_false' );

        //Admin Menu Bar but only if user is an admin role
        $can_use_admin_cache = apply_filters('tfsp_enable_admin_bar',false);
        if(is_admin() && (current_user_can('manage_options') || $can_use_admin_cache)) {
          $functions = new TFSP_Tools_Socket();
          if($functions->get_nginx_cache_status(false)){
            add_action( 'admin_bar_menu', array($admin, 'admin_bar_menu'), 105 );
            add_action( 'admin_footer', array($admin, 'admin_bar_footer_js') );
            add_action( 'wp_ajax_tfsp-cache-menu-all', array($admin, 'cache_menu_callback') );
          }
        }
        // Add Nginx Cache purge Actions
        $purger = new TFSP_purger();
        //add_action( 'save_post', array( $purger, 'clear_cache_from_post_id') );
        add_action( 'edit_post', array( $purger, 'clear_cache_from_post_id') );
        add_action( 'delete_post', array( $purger, 'clear_cache_from_post_id') );
        add_action( 'wp_trash_post', array( $purger, 'clear_cache_from_post_id') );
        add_action( 'comment_post', array( $purger, 'clear_cache_from_comment_id') );
        add_action( 'edit_comment', array( $purger, 'clear_cache_from_comment_id') );
        add_action( 'delete_comment', array( $purger, 'clear_cache_from_comment_id') );
        add_action( 'edit_terms', array( $purger, 'clear_cache_from_term_id'));
        add_action( 'delete_term', array( $purger, 'clear_cache_from_term_id'));
        add_action( 'wp_set_comment_status', array( $purger, 'clear_cache_from_comment_id') );
        add_action( 'wp_update_nav_menu', array( $purger, 'clear_cache_from_menu_update') );
        add_action('tfsp_purge', array($purger, 'process_site_purge'));


	//Auto Update on Plugin Activation
        add_action( 'activated_plugin', function($plugin, $network){
          $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
          $auto_updates[] = $plugin;
	        $auto_updates   = array_unique( $auto_updates );
          $all_items = apply_filters( 'all_plugins', get_plugins() );
          $auto_updates = array_intersect( $auto_updates, array_keys( $all_items ) );
          update_site_option( 'auto_update_plugins', $auto_updates );
        }, 10,2);

       //Trigger redirects for Multisite
        $this->redirect_multisite_admin();

    }

    /**
     * Set Admin & Front facing HTTP Headers
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function tfsp_headers(){
        if(is_admin()){
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        }
        $functions = new TFSP_Tools_Socket();
        $wl_urls = $functions -> get_whitelist();
        if (!empty($wl_urls) && is_array($wl_urls)){
          $path = $_SERVER["REQUEST_URI"];
          $url = wp_parse_url($path);
          $url = $url['path'];
            foreach ($wl_urls as $url_reg){
                if (fnmatch($url_reg, $url)){
                    header( 'X-Tfsp-Override: BYPASS' );
                }
            }
        }
    }

    /**
     * Modify the Email address when sending Mail
     *
     * @access public
     * @since 1.0.0
     * @return string
     */
    public function from_mail( $email ) {
        // Use home url rather than constant,
        // to avoid sites that were built with www.
        if($email == 'wordpress@'){
            $domain = get_option('home');
            $domain = str_ireplace('www.', '', parse_url($domain, PHP_URL_HOST));
            $email .= $domain;
        }
        return $email;
    }

    /**
     * Return a 401 Status Header, on Failed login
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function fail2ban_login_failed() {
        status_header(401);
    }

    /**
     * Setup Internal WordPress redirects for handling Multisite weirdness
     *
     * @access private
     * @since 1.8.0
     * @return string
     */
    private function redirect_multisite_admin()
    {
      global $wp;
      $url = add_query_arg($wp->query_string, '', home_url($wp->request));
      $path = parse_url($url, PHP_URL_PASS);
      if(defined('SUBDOMAIN_INSTALL')){
        if(SUBDOMAIN_INSTALL == true && $path == '/wp-admin/network/site-new.php')
        {
          //force to the correct location
          wp_redirect( '/wp/wp-admin/network/site-new.php' );
          exit;
        }
      }
      if(!is_multisite() && !defined( 'WP_CLI' ))
      {
        $redirect = true;
        if(defined('TFSP_APPLY_REDIRECT')){
          $redirect = TFSP_APPLY_REDIRECT;
        }
        $redirect = apply_filters( 'tfsp_apply_redirects', $redirect);
        if($_SERVER['HTTP_HOST'] != parse_url(home_url(), PHP_URL_HOST))
        {
          //We should by default redirect
          if($redirect === true )
          {
            wp_redirect(home_url( $_SERVER['REQUEST_URI'] ));
          }
        }
      }
      return $url;
    }

    /**
     * Add warning notification on temp.link
     *
     * @access public
     * @since 2.5.0
     * return void
     */  
 
    public function templink_notification( $wp_admin_bar )
    {
      if ( substr( $_SERVER['HTTP_HOST'], -strlen( 'temp.link' ) ) === 'temp.link' ) {
        ?> <p style="margin: auto; width: 100%; background-color: #DB2929; color: #FFFFFF; padding: 10px; margin-top: 5px; margin-bottom: 5px; text-align: center; justify-content: center;">
          <?php
          echo 'Using preview domain.  Development on this URL may cause issues.  Use a <a style="color: #FFF" href="https://www.34sp.com/kb/creating-and-accessing-a-staging-area" target="_blank">staging site</a> instead.';
          ?> </p> <?php
      }
    }
}

// Initialise plugin:
if(class_exists('TFSP_Tools')){
	$TFSP_Tools = new TFSP_Tools();
}

//setup the whitelist
function tfsptools_setup_whitelist() {
  add_option( 'tfsp_cache_whitelist', array(0 => '/cart', 1 => '/checkout') );
}

register_activation_hook( __FILE__, 'tfsptools_setup_whitelist' );
