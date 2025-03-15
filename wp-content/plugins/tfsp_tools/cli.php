<?php
/**
 * CLI Commands
 *
 * CLI commands available using wp hosting.
 *
 *
 * @package TFSP_Tools
 * @subpackage WP-CLI
 * @since 2.0.0
 */

 /**
 * WP-CLI functions Class
 * Provides WP-CLI support
 *
 * @since 2.0.0
 */
class Hosting extends WP_CLI_Command{

  /**
   * @subcommand trigger
   *
   * Usage wp hosting trigger
   * Accepts and passes arguments
   *
   **/
  function build_trigger( $args, $assoc_args)
  {
    /*
     * Passes assoc_args to filter for example --source=staging|git --repo=gitrepo
     */
    do_action('build_trigger', $assoc_args);
    return WP_CLI::success('1');
  }

  /**
   *
   * Usage wp cache [flush|clear] status
   * Allows flushing of the Nginx Full Page caching and validating status
   *
   **/
  function cache( $args, $assoc_args)
  {
    //Flush the cache
    if($args[0] === 'flush' || $args[0] === 'clear')
    {
      //Clear the nginx Site Cache
      $purger = new TFSP_purger();
      $purger->process_site_purge();
      return WP_CLI::success('Full Page Cache Cleared');
    }
    if($args[0] === 'status')
    {
      $nginx = new TFSP_Tools_socket();
      $response = $nginx->get_nginx_cache_status();
      if($response === true){
        return WP_CLI::success('Caching Enabled');
      }
      else{
        return WP_CLI::success('Caching Disabled');
      }

    }
  }

  /**
   *
   * @subcommand redirect
   * Usage wp redirects list | add | remove
   * Adds, removes and list associated redirects for the domain
   *
   **/
  function redirects( $args, $assoc_args)
  {
    $nginx = new TFSP_Tools_socket();

    if($args[0] === 'list')
    {
      $response = $nginx->get_nginx_rules();
      return WP_CLI\Utils\format_items( 'table', $response, ['type', 'url_path','endpoint'] );
    }
    if($args[0] === 'add')
    {
      if(isset($args[1]) && isset($args[2])){
        $response = $nginx->add_nginx_redirect($args[1],$args[2]);
        if($response === true){
          WP_CLI::success('Rule successfully added');
        }
        else{
          return WP_CLI::warning('Rule was not successfully added');
        }
      }
      else{
        return WP_CLI::error( 'format should be: wp hosting redirects add location endpoint', true);
      }
    }
    if($args[0] === 'remove')
    {
      if(isset($args[1])){
        $response = $nginx->remove_nginx_redirect($args[1]);
        if($response === true){
          return WP_CLI::success('Rule successfully removed');
        }
        else{
          return WP_CLI::warning('Rule was not successfully removed');
        }
      }
      else{
        return WP_CLI::error( 'format should be: wp hosting redirects remove location ', true);
      }
    }
  }

  /**
   *
   * Usage wp multisite enable | disable | status
   * Enables & Disables the Nginx Multisite rules
   *
   **/
  function multisite( $args, $assoc_args)
  {
    $nginx = new TFSP_Tools_socket();
    if($args[0] == 'enable')
    {
        $response = $nginx->enable_nginx_multisite();
        if($response === true){
          WP_CLI::line("To use Multisite please add define('WP_ALLOW_MULTISITE', true); to your my-config.php");
          return WP_CLI::success('Multisite Rules Added');
        }
        else{
          return WP_CLI::warning('Multisite Rules not added');
        }
    }
    if($args[0] == 'disable')
    {
      $response = $nginx->remove_nginx_redirect('1');
      if($response === true){
        return WP_CLI::success('Multisite rules removed');
      }
      else{
          return WP_CLI::warning('Multisite rules not successfully removed');
      }
    }
    if($args[0] === 'status')
    {
      $response = $nginx->get_nginx_rules();
      if(array_search('multisite', array_column($response, 'type'))){
          return WP_CLI::success('Multisite is Enabled');
      }
      else{
          return WP_CLI::success('Multisite is Disabled');
      }
    }
    else{
        return WP_CLI::error('format should be wp multisite status | enable | disable');
    }
  }
}
