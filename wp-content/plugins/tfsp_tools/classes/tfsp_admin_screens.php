<?php
/**
 * Admin Screens
 *
 * All admin area associated code, including admin screens.
 *
 *
 * @package TFSP_Tools
 * @subpackage ADMIN
 * @since 2.3.0
 */

 /**
 * Generic admin tools Class
 * Providing a generic bucket of helper tools for admin
 * Provides admin screens for tool settings.
 *
 * @since 2.3.0
 */
class tfsp_admin_screens{
  /*
   * Plugin Ban list
   *
   * @var array
   * @since 2.3.1
   */
  public $banlist = array('Very Simple Splash Page','WP Staging');

  /**
   * Hooks associated with Admin Init
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function admin_init(){
      add_action( 'admin_notices', array(&$this, 'admin_notice'));
      add_action('activate_plugin', array(&$this, 'check_plugin_blocklist'));
      add_action('admin_enqueue_scripts', array(&$this, 'setup_scripts'));
      add_action( 'wp_ajax_tfsp_handle_postback', array(&$this, 'tfsp_handle_postback'));
      remove_action( 'admin_notices', 'update_nag', 3 );
      remove_action( 'network_admin_notices', 'update_nag', 3 );
      remove_action( 'admin_notices', 'maintenance_nag' );
      remove_action( 'network_admin_notices', 'maintenance_nag' );
      add_filter('pre_site_transient_update_core',array( $this, 'remove_core_updates'));
      add_filter('site_status_tests', array( $this, 'remove_site_status_tests'));
      add_settings_section(
          'tfsp_update_section',
          '',
          null,
          'tfsp_update_settings'
      );
      add_settings_field(
          "tfsp_update_settings",
          "Update Schedule",
          array(&$this, 'draw_schedule_dropdown'),
          'tfsp_update_settings',
          'tfsp_update_section'
      );
      add_settings_section(
          'tfsp_core_update_section',
          '',
          null,
          'tfsp_core_update_settings'
      );
      add_settings_field(
          "tfsp_core_update_settings",
          "Update Schedule",
          array(&$this, 'draw_core_schedule_dropdown'),
          'tfsp_core_update_settings',
          'tfsp_core_update_section'
      );
      add_settings_section(
          'tfsp_update_notifications_section',
          '',
          null,
          'tfsp_update_notifications_settings'
      );
      add_settings_field(
          "tfsp_update_notifications_settings",
          "Update Notifications",
          array(&$this, 'draw_notifications_dropdown'),
          'tfsp_update_notifications_settings',
          'tfsp_update_notifications_section'
      );
      register_setting( 'tfsp_update_section', 'tfsp_update_schedule', array(&$this, 'update_schedule_callback'));
      register_setting( 'tfsp_core_update_section', 'tfsp_core_update_schedule', array(&$this, 'update_core_schedule_callback'));
      register_setting( 'tfsp_update_notifications_section', 'tfsp_update_notifications', array(&$this, 'update_notifications_callback'));
      global $pagenow;
      if( current_user_can('activate_plugins') && $pagenow == 'plugins.php' ){
        if ( ! function_exists( 'get_plugins' ) ) {
          require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if( defined( 'AUTOMATIC_UPDATER_DISABLED' ) && AUTOMATIC_UPDATER_DISABLED ) {
          $all_plugins = get_plugins();
          foreach($all_plugins as $key => $value){
            add_filter( 'plugin_action_links_'.$key, array(&$this,'add_plugin_links'), 10,5);
          }

          $this->update_plugin_list();
        }else{
          add_action( 'admin_enqueue_scripts', [$this,'enqueue_plugin_screen'] );

        }

      }
      add_filter( 'all_plugins', array(&$this,'hide_plugins'), 10,5);
  }

  /**
   * Hide Plugins from plugin page
   *
   * @access public
   * @since 2.3.0
   * @return array
   */
  public function hide_plugins($list){
    unset($list['tfsp_tools/thirty_four_sp_tools.php']);
    return $list;
  }

  /**
   * Setup JS & CSS scripts used in backend
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function setup_scripts($hook){
      //check if we are within our own plugin or just return
      if (!in_array($hook, array('tools_page_tfsp_cache', 'tools_page_tfsp_updates', 'tools_page_tfsp_setup'))){
          return;
      }
      $TFSP_URL = plugin_dir_url(TFSP_TOOLs_DIR.'thirty_four_sp_tools.php');
      // Namespaced Function to include Scripts in WP-admin
      wp_enqueue_style('TFSP_Tools_cache', $TFSP_URL.'css/cache.css', array(), "2.0.0", 'all');
      wp_enqueue_script( 'TFSP_Tools_global', $TFSP_URL.'js/tfsp.js', array(), "2.0.0", 'all');
  }

  /**
   * Setup Pages used to generate our hosting tools admin area
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function setup_developer_menu(){
      if ( !defined( 'TFSP_STAGING' ) || !TFSP_STAGING ){
          add_submenu_page(
              'tools.php',
              'Developer Tools',
              'Hosting Tools',
              'manage_options',
              'tfsp_cache',
              array(&$this, 'draw_cache_page')
          );

          add_submenu_page(
              'tools.php',
              'Developer Tools',
              'Hosting Tools',
              'manage_options',
              'tfsp_setup',
              array(&$this, 'draw_setup_page')
          );
          add_action( 'admin_head', array(&$this, 'remove_menus'));
      }
      add_submenu_page(
          'tools.php',
          'Developer Tools',
          'Hosting Tools',
          'manage_options',
          'tfsp_updates',
          array(&$this, 'draw_updates_page')
      );
      add_submenu_page(
          'tools.php',
          'Developer Tools',
          'Hosting Tools',
          'manage_options',
          'tfsp_cron',
          array(&$this, 'draw_cron_page')
      );
  }

  public function remove_menus() {
      if ( !defined( 'TFSP_STAGING' ) || !TFSP_STAGING ){
          remove_submenu_page( 'tools.php', 'tfsp_updates' );
          remove_submenu_page( 'tools.php', 'tfsp_setup' );
          remove_submenu_page( 'tools.php', 'tfsp_cron' );
      }
      else{
          remove_submenu_page( 'tools.php', 'tfsp_cache' );
          remove_submenu_page( 'tools.php', 'tfsp_setup' );
          remove_submenu_page( 'tools.php', 'tfsp_cron' );
      }
  }

  /**
   * render the Cache Settings Page
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function draw_cache_page(){
      if(!current_user_can('manage_options')){
          wp_die(__('You do not have sufficient privileges.'));
      }
      $functions = new TFSP_Tools_Socket();
      $cache_status = $functions -> get_nginx_cache_status(false,true);
      $wl_urls = $functions -> get_whitelist();
      include(sprintf(TFSP_TOOLs_DIR."/templates/cache.php", dirname(__FILE__)));
      unset($functions);
  }

  /**
   * render the cron Log Page
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function draw_cron_page(){
      if(!current_user_can('manage_options')){
          wp_die(__('You do not have sufficient privileges.'));
      }
      $render_recent_crons = $this->render_recent_crons();
      include(sprintf(TFSP_TOOLs_DIR."/templates/cron.php", dirname(__FILE__)));
      unset($functions);
  }

  /**
   * render the Let's Encrypt settings Page
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function draw_setup_page(){
      if(!current_user_can('manage_options')){
          wp_die(__('You do not have sufficient privileges.'));
      }
      if (!is_ssl()){
          $functions = new TFSP_Tools_socket();
          $cert_status = $functions -> get_letsencrypt_status(false);
          unset($functions);
      }
      include(sprintf(TFSP_TOOLs_DIR."/templates/setup.php", dirname(__FILE__)));
  }

  /**
   * render Update Settings Page
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function draw_updates_page(){
      // Check this is a user with privileges
      if(!current_user_can('manage_options')){
          wp_die(__('You do not have sufficient privileges.'));
      }
      $schedule = get_option('tfsp_update_schedule', '0');
      if ($schedule == '0'){
          $delay_string = "run immediately";
      }
      if ($schedule == '1'){
          $delay_string = "delay for 24 hours";
      }
      if ($schedule == '7'){
          $delay_string = "delay for 7 days";
      }
      $core_schedule = get_option('tfsp_core_update_schedule', '0');
      if ($core_schedule == '0'){
          $core_delay_string = "run immediately";
      }
      if ($core_schedule == '1'){
          $core_delay_string = "delay for 24 hours";
      }
      if ($core_schedule == '7'){
          $core_delay_string = "delay for 7 days";
      }
      include(sprintf(TFSP_TOOLs_DIR."/templates/updates.php", dirname(__FILE__)));
  }

  /**
   * Render Updates page Plugin dropdown schedule
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function draw_schedule_dropdown(){
      $current_schedule = get_option( 'tfsp_update_schedule', '0' );
      echo '<select name="tfsp_update_schedule" id="tfsp_update_schedule">';
      echo '<option value="0"';
      if ($current_schedule == '0'){ echo ' selected="selected"'; }
      echo'>Run updates Immediately</option>';
      echo '<option value="1"';
      if ($current_schedule == '1'){ echo ' selected="selected"'; }
      echo'>Delay updates for 24 hours</option>';
      echo '<option value="7"';
      if ($current_schedule == '7'){ echo ' selected="selected"'; }
      echo'>Delay updates for 7 days</option> </select>';
  }

  /**
   * Render Updates page Cron dropdown schedule
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function draw_core_schedule_dropdown(){
      $current_schedule = get_option( 'tfsp_core_update_schedule', '0' );
      echo '<select name="tfsp_core_update_schedule" id="tfsp_update_schedule">';

      echo '<option value="0"';
      if ($current_schedule == '0'){ echo ' selected="selected"'; }
      echo'>Run updates Immediately</option>';

      echo '<option value="1"';
      if ($current_schedule == '1'){ echo ' selected="selected"'; }
      echo'>Delay updates for 24 hours</option>';

      echo '<option value="7"';
      if ($current_schedule == '7'){ echo ' selected="selected"'; }
      echo'>Delay updates for 7 days</option> </select>';
  }

  /**
   * Render Updates page contacts Dropdown
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function draw_notifications_dropdown(){
      $current_schedule = get_option( 'tfsp_update_notifications', 'admins' );
      echo '<select name="tfsp_update_notifications" id="tfsp_update_notifications">';
      echo '<option value="none"';
      if ($current_schedule == 'none'){ echo ' selected="selected"'; }
      echo '>None</option>';
      echo '<option value="admins"';
      if ($current_schedule == 'admins'){ echo ' selected="selected"'; }
      echo '>Notify WordPress Administrators</option>';
      // echo '<option value="contacts"';
      // if ($current_schedule == 'contacts'){ echo ' selected="selected"'; }
      // echo'>Notify Site Contacts</option>';
      // echo '<option value="settings_email"';
      // if ($current_schedule == 'settings_email'){ echo ' selected="selected"'; }
      // echo '>Notify Site Email</option>';
      echo '</select>';
  }

  /**
   * Set Updates plugin schedule from updates page
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function update_schedule_callback($input) {
      if (!in_array($input, array('0', '1', '7'))){
          add_settings_error(
              'tfsp_update_schedule',
              'tfsp_update_error',
              'Please choose a schedule option',
              'error'
          );
      }
      else{
          add_settings_error(
              'tfsp_update_schedule',
              'tfsp_update_success',
              "Settings Updated",
              'updated'
          );
      }
      return $input;
  }

  /**
   * Set Updates core schedule from updates page
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function update_core_schedule_callback($input) {
      if (!in_array($input, array('0', '1', '7'))){
          add_settings_error(
              'tfsp_core_update_schedule',
              'tfsp_update_error',
              'Please choose a schedule option',
              'error'
          );
      }
      else{
          add_settings_error(
              'tfsp_core_update_schedule',
              'tfsp_update_success',
              "Settings Updated",
              'updated'
          );
      }
      return $input;
  }

  /**
   * Set Updates contacts on updates page
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  public function update_notifications_callback($input) {
      if (!in_array($input, array('admins', 'contacts', 'none', 'settings_email'))){
          add_settings_error(
              'tfsp_update_notifications',
              'tfsp_update_error',
              'Please choose a contact option',
              'error'
          );
      }
      else{
          add_settings_error(
              'tfsp_update_notifications',
              'tfsp_update_success',
              "Contact Notification Preferences Updated",
              'updated'
          );
      }
      return $input;
  }

  /**
   * Render recent cron list
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
  function render_recent_crons(){
    $return = '';
    $report = [];
    $cron_log = get_option('tfsp_cron_log');
    if(empty($cron_log) || !is_array($cron_log)){
      $return = 'No recent crons';
    }
    else{
      $cron_log = array_reverse($cron_log);
      $return .='<table class="wp-list-table widefat fixed">
                  <thead>
                  <tr>
                  <th>Cron Name</th><th>Status</th><th>Last run</th><th>Next Run</th>
                  </tr>
                  </thead>
      ';
      foreach( $cron_log as $log => $details){
        $next = wp_next_scheduled($log);
        if(isset($next) && is_numeric($next)){
          $next = date('Y-m-d H:i:s',$next);
        }else{
          $next = 'Non-Repeating';
        }
        $return .= '
          <tr>
            <th>'.$log.'</th><th>'.$details[0].'</th><th>'.date('Y-m-d H:i:s', $details[1]).'</th><th>'.$next.'</th>
          </tr>';
      }
      $return .='</table>';
    }
    return $return;
  }

  /**
   * Manage Ajax request
   *
   * @access public
   * @since 2.3.0
   * @return null
   */
    public function tfsp_handle_postback(){
      $functions = new TFSP_Tools_Socket();
        switch ($_POST['function']){
          case 'enable_lets_encrypt':
              $api_response =  $functions -> enable_lets_encrypt();
          break;
          case 'clear_nginx_cache_url':
              $api_response =  $functions -> clear_nginx_cache_url();
          break;
          case 'clear_nginx_cache_site':
              $api_response =  $functions -> clear_nginx_cache_site();
          break;
          case 'set_nginx_cache_status':
              $api_response =  $functions -> set_nginx_cache_status();
          break;
          case 'get_nginx_cache_status':
              $api_response =  $functions -> get_nginx_cache_status();
          break;
          case 'set_whitelist':
              $api_response =  $functions -> set_whitelist();
          break;
          default:
              $api_response =  array(false, "Unknown function call");
        }

        if ($api_response[0] == true){
            $response_type = 'updated';
        }
        else {
            $response_type = 'error';
        }
        add_settings_error(
              '',
              'api_callback_response',
              $api_response[1],
              $response_type
        );
        if (isset($_POST['json'])  && $_POST['json'] == true){
                  ob_start();
                  include(sprintf(TFSP_TOOLs_DIR."/templates/ajax_response.php", dirname(__FILE__)));
                  $message = ob_get_contents();
                  ob_end_clean();
                  echo json_encode(array('success'=> 1, 'message'=> $message, 'param'=> $api_response[2]));
        }
        else{
            include(sprintf(TFSP_TOOLs_DIR."/templates/ajax_response.php", dirname(__FILE__)));
        }
        exit;
      }

      /**
       * Render admin notice if applicable
       *
       * @access public
       * @since 2.3.0
       * @return null
       */
       public function admin_notice() {
           global $pagenow;
           if( $pagenow == 'plugins.php' ){
               if ( current_user_can( 'install_plugins' ) ) {
                   if ( isset($_GET['plugin_blocked']) ) {
                           echo '<div id="error" class="error notice is-dismissible"><p>Plugin ' . esc_attr( $_GET['plugin_name'] ) . ' cannot be activated as it is on the blocked plugin list.</div>';
                   }
                 }
           }
       }

       /**
        * Add Links on plugin page to enable or disable updates of individual
        * plugins
        *
        * @access public
        * @since 2.3.0
        * @return array
        */
       public function add_plugin_links($actions, $plugin_file )
       {
         $no_update_plugins = get_option( '34sp_no_update_plugins', array() );
         if( in_array( $plugin_file, $no_update_plugins ) ){
           $url = self_admin_url('plugins.php?plugin_updater=enable&plugin='.$plugin_file);
           return array_merge( array( 'allow_updates' => '<a href="'. wp_nonce_url( $url, 'plugin_updater' ).'">Enable Updates' ),  $actions );
         }
         else{
           $url = self_admin_url('plugins.php?plugin_updater=disable&plugin='.$plugin_file);
           return array_merge( array( 'allow_updates' => '<a href="'. wp_nonce_url( $url, 'plugin_updater' ).'">Disable Updates' ),  $actions );
         }
       }

       /**
        * Set if plugin should be recieving automated updates
        *
        * @access public
        * @since 2.3.0
        * @return bool
        */
       public function update_plugin_list()
       {
         global $pagenow;
         if(!current_user_can('activate_plugins') || $pagenow != 'plugins.php') return;

         if(!isset($_GET['plugin_updater'])) return;

           if(check_admin_referer('plugin_updater') && isset($_GET['plugin'])){
             $plugin = $_GET['plugin'];
             $disabled_plugins = get_option( '34sp_no_update_plugins', array());
             if($_GET['plugin_updater'] == 'enable'){
               $disabled_plugins = array_diff( $disabled_plugins, array($plugin));

             }
             elseif( $_GET['plugin_updater'] == 'disable' ){
               $disabled_plugins[] = $plugin;

             }else {
               wp_die(__('Plugin Updater has no data'));
             }
             add_action('pre_current_active_plugins', array(&$this, 'update_notice'));
            return update_option( '34sp_no_update_plugins', $disabled_plugins);

           }
       }

       /**
        * Render Update notice
        *
        * @access public
        * @since 2.3.0
        * @return null
        */
       public function update_notice()
       {
         echo '<div class="updated">
                    <p>Plugin update preferences changed</p>
                </div>';
       }

       /**
        * Render Admin Bar Menu
        *
        * @access public
        * @since 2.3.0
        * @return null
        */
       public function admin_bar_menu($admin_bar){
         global $wp_admin_bar;
         $wp_admin_bar->add_menu( array(
     			'id'    => 'tfsp-cache-menu',
     			'title' => 'Hosting Tools',
     			'href'  => '',
     			'meta'  => array( 'tabindex' => '' ),
     		) );
         $wp_admin_bar->add_menu( array(
           'parent' => 'tfsp-cache-menu',
           'id'     => 'tfsp-cache-menu-all',
           'title'  => 'Clear All Caches',
           'href'   => '#',
           'meta'   => array( 'tabindex' => false ),
         ) );
         $wp_admin_bar->add_menu( array(
           'parent' => 'tfsp-cache-menu',
           'id'     => 'tfsp-cache-menu-settings',
           'title'  => 'Settings',
           'href'   => get_admin_url().'tools.php?page=tfsp_cache',
           'meta'   => array( 'tabindex' => false ),
         ) );
       }

       /**
        * Render Footer Javascript for Ajax Update
        *
        * @access public
        * @since 2.3.0
        * @return null
        */
       public function admin_bar_footer_js()
       {
         ?>
           <script type="text/javascript" >
              jQuery("li#wp-admin-bar-tfsp-cache-menu-all .ab-item").on( "click", function() {
                 var data = {
                               'action': 'tfsp-cache-menu-all',
                             };
                 jQuery.post(ajaxurl, data, function(response) {
                   jQuery('#wpbody-content').prepend('<div class="notice notice-success is-dismissible"><p>Hosting Tools - Cleared All Caches</p></div>');
                 });

               });
           </script>
         <?php
       }

       /**
        * Process cache menu callback
        *
        * @access public
        * @since 2.3.0
        * @return null
        */
       public function cache_menu_callback()
       {
         $functions = new TFSP_Tools_Socket();
         $functions->clear_nginx_cache_url();
         wp_cache_flush();
         $response = "Both Page and Object caches cleared!";
       echo $response;
         wp_die();
       }

       /**
        * Check if plugin is banned
        *
        * @access public
        * @since 2.3.0
        * @return bool
        */
       public function should_block_plugin($plugin_name) {
           $banlist = $this->banlist;
           if (in_array($plugin_name, $banlist)) {
               return true;
           }
           return false;
       }

       /**
        * Prevent activation of banned plugin
        *
        * @access public
        * @since 2.3.0
        * @return null
        */
       public function check_plugin_blocklist($plugin_file) {
           $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file);
           if ($this->should_block_plugin($plugin_data['Name'])) {
               wp_redirect( self_admin_url("plugins.php?plugin_blocked=true&plugin_name=" . urlencode($plugin_data['Name'])) );
           exit;
           }
       }

       /**
        * Prevent WordPress from looking for core updates
        *
        * @access public
        * @since 2.3.0
        * @return null
        */
       public function remove_core_updates(){
       	global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
       }

       /**
        * Remove site health checks we don't need to worry about
        *
        * @access public
        * @since 2.3.0
        * @return null
        */
       public function remove_site_status_tests( $tests)
       {
         unset( $tests['direct']['wordpress_version'] );
         unset( $tests['direct']['php_version'] );
         unset( $tests['direct']['php_extensions'] );
         unset( $tests['async']['background_updates'] );
         return $tests;
       }

       public function enqueue_plugin_screen( $hook ){
         $script = 'jQuery( document ).ready(function() { ';
           $auto_update_plugins = get_site_option( 'auto_update_plugins', array() );
           $schedule = get_option( 'tfsp_update_schedule', '0' );
           if($schedule == 0){
             $time_to_next_update = 'Automatic update scheduled this evening';
           }
           elseif($schedule == 1){
             $time_to_next_update = 'Automatic update scheduled tomorrow evening';
           }else{
             $time_to_next_update = 'Automatic update scheduled in '.$schedule.' days';
           }
           $script .= 'jQuery("[class*=\'auto-update-time\']").html("'.$time_to_next_update.'")';
         $script .= '});';
         wp_add_inline_script( 'jquery', $script );
       }
}
