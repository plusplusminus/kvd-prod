<?php


/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * WooCommerce_Ultimate_Multi_Currency_Suite_Callbacks class is a class for handling callbacks/ajax.
 *
 * This class is responsible for receiving and executing actions called by ajax requests or from cron or from other classes.
 *
 */

// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}

class WooCommerce_Ultimate_Multi_Currency_Suite_Callbacks {
	
	
	private $settings; // we're going to assign Settings object to this property
	
	
	/**
	 * Initiate object: add appropriate WP filters, register callbacks
	 */
	public function __construct($settings){ // $settings = instance of WooCommerce_Ultimate_Multi_Currency_Suite_Settings
		
		// Assign settings object to class property, so we can easily access it from other methods:
		$this->settings = $settings;
		
		// Add WP hooks:		
		if (is_admin()){
			add_action('wp_ajax_wcumcs_ajax', array($this, 'ajax_request'));
			add_action('wp_ajax_nopriv_wcumcs_ajax', array($this, 'ajax_request'));
		}
		
	}
	
	
	/**
	 * This method receives all callback requests (ajax only)
	 */
	public function ajax_request(){
		
		check_ajax_referer('woocommerce-ultimate-multi-currency-suite-nonce', 'verification', true); // die if nonce incorrect
		
		$action = ''; // action to be performed
		
		// make sure it's our own ajax request:
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			// if we got here, it means that the ajax request is valid
			$action = $_POST['execute'];	
		}
		
		$action_data = array();
		
		if (empty($action)){ // die - empty request for some reason
			exit;
		} else if ($action == 'restore_defaults'){ // execute restore defaults
			$action_data['name'] = 'restore_defaults'; 
		} else if ($action == 'update_exchange_rates'){ // update one or more exchange rates
			$action_data['name'] = 'update_exchange_rates';
			$action_data['currency_data'] = $_POST['currency_data'];
		} else if ($action == 'get_currency_hash'){
			$action_data['name'] = 'get_currency_hash';
		} else {
			exit;
		}
		
		$action_result = $this->execute_action($action_data);
		
		echo $action_result;
		exit;
		
	}
	
	
	/**
	 * This method executes all actions (from cron and ajax)
	 */
	public function execute_action($action_data, $cron_execution = false){
		
		if (empty($action_data)){
		
			exit;
		
		} else if ($action_data['name'] == 'restore_defaults'){
			
			if (!current_user_can('edit_shop_orders')){ // check permissions first
				exit;
			}
			
			if ($this->settings->restore_defaults() == true){ // let's run restore_defaults method in settings class
				// Default restored, let's inform user about it:
				$action_result = __("Default settings have been successfully restored.", 'woocommerce-ultimate-multi-currency-suite');
				return $action_result;		
			} else {
				exit; // true wasn't returned for some reason - let's exit
			}			
			
		} else if ($action_data['name'] == 'update_exchange_rates') {		
			
			if (!current_user_can('edit_shop_orders')){ // check permissions first
				exit;
			}	
			
			$currency_data = $action_data['currency_data']; // assign to an easier to use array name
			$action_result = $this->settings->update_exchange_rates($currency_data, $cron_execution); // execute update and save to $action_result what this method returned (array)
			$action_result_json = json_encode($action_result);
			
			return $action_result_json;
		
		} else if ($action_data['name'] == 'get_currency_hash'){
			
			$currency_hash = $this->settings->get_currency_hash();
			return $currency_hash;
		
		} else {
		
			exit;
		
		}
		
	}
	
	
}