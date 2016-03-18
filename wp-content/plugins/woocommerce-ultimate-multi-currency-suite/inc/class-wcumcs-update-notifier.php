<?php


/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * WooCommerce_Ultimate_Multi_Currency_Suite_Update_Notifier class is a class for handling plugin update/update notifications.
 * 
 * This class was inspired by Abid Omar work (http://omarabid.com/).
 *
 */


// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}


class WooCommerce_Ultimate_Multi_Currency_Suite_Update_Notifier {
	
	
	private $current_version; // The plugin current version
	private $latest_version_data = array(); //array with latest version data
	private $update_api; // The plugin remote update path 
	private $plugin_slug; // Plugin slug with directory and extension (plugin-directory/plugin-file.php)
	private $slug; // Plugin slug (plugin-file)
	private $license_key; // License Key 
	
	
	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 */
	public function __construct($current_version, $update_api, $plugin_slug, $license_key = ''){
		
		// Set the class public variables:
		$this->current_version = $current_version;
		$this->update_api = $update_api;
		
		// Set the License:
		$this->license_key = $license_key;
		
		// Set the Plugin Slug:
		$this->plugin_slug = $plugin_slug;
		list($t1, $t2) = explode('/', $plugin_slug);
		$this->slug = str_replace('.php', '', $t2);		
		
		// WP hooks:
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'), 9999); // plugin update available/not available
		add_filter('plugins_api', array($this, 'check_details'), 9999, 3); // plugin extended info
		
	}
	
	
	/**
	 * Add plugin update data
	 */
	public function check_update($transient){
		
		if (empty($this->latest_version_data)){ // if we have not checked latest_version data before...
			if (!$this->get_remote_data()){ // get current data
				return $transient; // if current version data cannot be retrieved - return $transient
			}
		}		
		
		if (empty($this->latest_version_data)){ // if still no data from remote server
			return $transient; // return $transient
		} else {
			// If a newer version is available, add the update
			if (version_compare($this->current_version, $this->latest_version_data['new_version'], '<')){
				$obj = new stdClass();
				$obj->slug = $this->slug;
				$obj->plugin = $this->plugin_slug;
				$obj->new_version = $this->latest_version_data['new_version'];
				$obj->url = $this->latest_version_data['url'];
				$obj->package = $this->latest_version_data['package'];
				$obj->upgrade_notice = $this->latest_version_data['upgrade_notice'];
				$transient->response[$this->plugin_slug] = $obj;
			}
		}
		
		return $transient;
		
	}
	
	
	/**
	 * Add plugin details
	 */
	public function check_details($result, $action, $arg){
		
		if (isset($arg->slug) && $arg->slug === $this->slug){
			
			if (empty($this->latest_version_data)){ // if we have not checked latest_version data before...
				if (!$this->get_remote_data()){ // get current data
					return false; // if current version data cannot be retrieved - return false
				}
			}	
			
			$obj = (object)$this->latest_version_data;
			return $obj;
			
		} else {
			
			return false;
			
		}
		
	}
	
	
	/**
	 * Get data from server
	 */
	public function get_remote_data(){
		
		$params = array(
			'body' => array(
				'license_key' => $this->license_key
			)
		);
		
		$request = wp_remote_post($this->update_api, $params);
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
			$this->latest_version_data = json_decode($request['body'], true);
			return true; // success;
		} else {
			return false;
		}
		
	}
	
	
}
