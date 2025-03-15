<?php
/**
 * Nginx Cache purge
 *
 * Calls internal to container to the nginx cache.
 *
 *
 * @package TFSP_Tools
 * @subpackage FASTCGI
 * @since 2.0.0
 */

 /**
 * Nginx Purger
 *
 * @since 1.0.0
 */
class TFSP_purger{
    /**
     * Pass a List of URLs to be purge
     *
     * @access public
     * @since 1.0.0
     * @return @process_site_purge
     */
    public function process_url_list($urls){
			return $this->process_site_purge();
    }

    /**
     * Process Site Purge list
     *
     * @access public
     * @since 1.0.0
     * @return @__call
     */
    public function process_site_purge(){
      $functions = new TFSP_Tools_socket();
      if(!$functions -> get_nginx_cache_status(false)) return;
        // for url in urls, format and purge url
        $domain = TFSP_HOME_DOMAIN;
        $static_endpoint = "http://127.0.0.1/purge_site/$domain";
				//clear cache
        return $this->_call(trailingslashit($static_endpoint));
    }

    /**
     * Clear cache by Post ID
     *
     * @access public
     * @since 1.0.0
     * @deprecated
     * @return @schedule_purge
     */
    public function clear_cache_from_post_id($_id){
      return $this->schedule_purge();
    }
    /**
     * Clear cache by Term ID
     *
     * @access public
     * @since 1.0.0
     * @return @schedule_purge
     */
    public function clear_cache_from_term_id($_id){
      if(defined('34SP_TERMS_CACHE_OFF')) return;
      return $this->schedule_purge();
    }
    /**
     * Clear cache by Menu Update
     *
     * @access public
     * @since 1.0.0
     * @return @schedule_purge
     */
    public function clear_cache_from_menu_update(){
        if(defined('34SP_MENU_CACHE_OFF')) return;
        return $this->schedule_purge();
    }
    /**
     * Clear cache by Comment ID
     *
     * @access public
     * @since 1.0.0
     * @return @schedule_purge
     */
    public function clear_cache_from_comment_id(){
      if(defined('34SP_COMMENT_CACHE_OFF')) return;
      return $this->schedule_purge();
    }

    /**
     * Schedule Purging Cache via WP Cron
     *
     * @access private
     * @since 2.0.0
     * @return multiple
     *
     */
    private function schedule_purge()
    {
      if(defined('34SP_CACHE_SCHEDULE_OFF')) {
        $this->process_site_purge();
      }
      //check if a purge is going to happen
      if( !wp_next_scheduled( 'tfsp_purge' ) ) {
        //Set Purge for Now
        wp_schedule_single_event( time(), 'tfsp_purge' );
      }
    }

    /**
     * Call Nginx EndPoint to purge cache
     *
     * @access private
     * @since 1.0.0
     * @return array
     */
      private function _call($url){
          $curl = curl_init();

          curl_setopt ($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
          curl_setopt($curl, CURLOPT_TIMEOUT, 4);
          $result = curl_exec ($curl);
          $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
          curl_close ($curl);

          switch ($httpcode){
              case"200":
                  return array(true, "Cleared the cache successfully");
              break;
              case "404":
                  return array(false, "Unable to find page in cache");
              break;
              default:
                  return array(false, "An error occurred");
          }
      }
}
