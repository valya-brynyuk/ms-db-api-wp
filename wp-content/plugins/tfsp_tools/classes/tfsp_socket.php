<?php
/**
 * Socket API Functions
 *
 * All socket API related calls.
 *
 *
 * @package TFSP_Tools
 * @subpackage SOCKET
 * @since 2.0.0
 */

 /**
 * Socket API Class
 * Replaces TFSP_tools_functions in 2.2.0
 * Provides Access to the 34SP.com unix socket.
 *
 * @since 2.2.0
 */
class TFSP_Tools_Socket{
  /**
   * Clear Nginx cache URL
   *
   * @access public
   * @since 1.0.0
   * @return tfsp_purger:process_site_purge
   */
    public function clear_nginx_cache_url(){
      $purger = new TFSP_Purger();
      $domain = TFSP_HOME_DOMAIN;
      $domain_url = "http://127.0.0.1/purge_site/$domain";
      return($purger->process_site_purge($domain_url));
    }
    /**
     * Clear Nginx cache entire domain
     *
     * @access public
     * @since 1.0.0
     * @return tfsp_purger:process_site_purge
     */
    public function clear_nginx_cache_site(){
        $purger = new TFSP_Purger();
        $domain = TFSP_HOME_DOMAIN;
        $domain_url = "http://127.0.0.1/purge_site/$domain";
        return($purger->process_site_purge($domain_url));
    }
    /**
     * Enable Lets Encrypt Certificate
     *
     * @access public
     * @since 1.0.0
     * @return array
     */
    public function enable_lets_encrypt(){
        $response = $this->call_api(array(
            "domain" => TFSP_HOME_DOMAIN,
            "command" => "certificate",
            "subcommand" => "order_lets_encrypt"
        ));
        $response = json_decode($response, true);
        return array($response['success'], $response['message']);
    }
    /**
     * Set Nginx FASTCGI cache status
     *
     * @access public
     * @since 1.0.0
     * @return array
     */
    public function set_nginx_cache_status(){
        $new_status = $_POST['status'];
        $response = $this->call_api(array(
            "domain" => TFSP_HOME_DOMAIN,
            "command" => "cache",
            "subcommand" => "set_status",
            "status" => ($new_status == 0 ? 'disabled' : 'enabled')
        ));
        $response = json_decode($response, true);
        // format to response handler
        $array = array($response['success'], $response['message'], $response['status']);
        //Cache the response
        wp_cache_set( 'tfsp_nginx_status', $array );
        return $array;
    }
    /**
     * Get Nginx FASTCGI cache status
     *
     * @access public
     * @since 1.0.0
     * @return array
     */
    public function get_nginx_cache_status($json=true,$skip_cache=null){
        $array = wp_cache_get( 'tfsp_nginx_status' );
        if(!is_array($array || isset($skip_cache)) ){
          $response = $this->call_api(array(
              "domain" => TFSP_HOME_DOMAIN,
              "command" => "cache",
              "subcommand" => "get_status"
          ));
          $response = json_decode($response, true);
          if(is_array($response)){
            $array =  array($response['success'], $response['status']);
          }
          if(is_array($array)){
            wp_cache_set( 'tfsp_nginx_status', $array );
          }else{
            $array = array();
          }
        }
          if (!$json){
              if ($array[1] == 1){
                  return true;
              }
              return false;
          }
        return $array;
    }

    /**
     * Get Lets Encrypt status
     *
     * @access public
     * @since 1.0.0
     * @return array
     */
    public function get_letsencrypt_status(){
        $response = $this->call_api(array(
            "domain" => TFSP_HOME_DOMAIN,
            "command" => "certificate",
            "subcommand" => "lets_encrypt_status"
        ));

        $response = json_decode($response, true);
        if ($response['message'] == true){
            return true;
        }
        return false;
    }

    /**
     * Get If site is staging
     *
     * @access public
     * @since 1.0.0
     * @return bool
     */
    public function get_staging_status(){
        $response = $this->call_api(array(
            "domain" => TFSP_HOME_DOMAIN,
            "command" => "staging",
            "subcommand" => "get_status"
        ));

        $response = json_decode($response, true);
        if ($response['success'] == true){
            return true;
        }
        return false;
    }

    /**
     * Get Nginx Rules
     *
     * @access public
     * @since 2.2.0
     * @return array
     */
    public function get_nginx_rules()
    {
      $response = $this->call_api(array(
          "domain" => TFSP_HOME_DOMAIN,
          "command" => "nginx",
          "subcommand" => "get_rules"
      ));

      $response = json_decode($response, true);
      if ($response['success'] == true){
          return $response['rules'];
      }
      return false;
    }

    /**
     * Add a rewrite rule to Nginx Config
     *
     * @access public
     * @since 2.2.0
     * @return bool
     */
    public function add_nginx_redirect($url, $endpoint)
    {
      $response = $this->call_api(array(
          "domain" => TFSP_HOME_DOMAIN,
          "command" => "nginx",
          "subcommand" => "add_rule",
          "type" => 'redirect',
          'url' => $url,
          'endpoint' => $endpoint
      ),10);

      $response = json_decode($response, true);
      if ($response['message'] == true){
          return true;
      }
      return false;
    }

    /**
     * Remove Nginx Redirect
     *
     * @access public
     * @since 2.2.0
     * @return bool
     */
    public function remove_nginx_redirect($url)
    {
      $response = $this->call_api(array(
          "domain" => TFSP_HOME_DOMAIN,
          "command" => "nginx",
          "subcommand" => "remove_rule",
          'url' => $url,
      ),10);

      $response = json_decode($response, true);
      if ($response['message'] == true){
          return true;
      }
      return false;
    }

    /**
     * Enable Multisite
     *
     * @access public
     * @since 2.2.0
     * @return bool
     */
    public function enable_nginx_multisite()
    {
      $response = $this->call_api(array(
          "domain" => TFSP_HOME_DOMAIN,
          "command" => "nginx",
          "subcommand" => "add_rule",
          "type" => 'multisite',
          'url' => '1',
          'endpoint' => '1'
      ),10);

      $response = json_decode($response, true);
      if ($response['message'] == true){
          return true;
      }
      return false;
     }

     /**
      * Get Cache Whitelist
      *
      * @access public
      * @since 1.0.0
      * @return array
      */
     public function get_whitelist() {
        $whitelist = get_option( 'tfsp_cache_whitelist', '' );
        if ( !is_array($whitelist) ){
             $whitelist = array($whitelist);
        }
        return $whitelist;
     }

     /**
      * Set Cache Whitelist
      *
      * @access public
      * @since 1.0.0
      * @return array
      */
     public function set_whitelist() {
        add_option( 'tfsp_cache_whitelist', '', '' ,'yes');
        $whitelist = $_POST['whitelist_cache'];
        $new_whitelist = array();
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $whitelist) as $line){
             $line=trim($line);
             $line=wp_parse_url($line);
             $new_whitelist[] = $line['path'];
        }
        update_option( 'tfsp_cache_whitelist', $new_whitelist, 'yes');
        return array("1", "Updated whitelist");

     }

     /**
      * Call Socket API
      *
      * @access private
      * @since 1.0.0
      * @return array
      */
    public function call_api($parameters, $tfsp_timeout = TFSP_SOCKET_TIMEOUT){
        // convert assoc array -> json
        $parameters = json_encode($parameters);
        // Open socket with default timeout
        $client = stream_socket_client(
            "unix:///tmp/apisocket",
            $errno, $errmsg,
            $timeout = ini_get("default_socket_timeout")
        );

        // Check socket is open
        if (!$client){
            error_log("34SP API - Socket Error Occurred: $errmsg ($errno)");
            return "An Error Occurred: $errmsg ($errno). Please Retry.";
        }
        else{
            $data = "";
            // Set timeout for data transfer
            stream_set_timeout ($client , $tfsp_timeout);
            // Write and read 4k chunk. Limitation by design.
            fwrite($client, $parameters);
            $data = stream_get_contents($client);
            fclose($client);
            return $data;
        }
    }

}

?>
