<?php


/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * WooCommerce_Ultimate_Multi_Currency_Suite_Main class is a plugin main wrapper class.
 *
 * This class is responsible for initializing correct classes (frontend/admin/settings etc) and
 * running their methods and adding textdomain.
 *
 */

// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}

class WooCommerce_Ultimate_Multi_Currency_Suite_Main {
	
	
	public $settings; // we're going to assign Settings object to this property (public, so widget can get access to it)
	public $callbacks;  // we're going to assign Callbacks object to this property (public, so cron function can access it from outside)
	private $mode = ''; // this is the flag to set appropriate plugin mode (admin mode or frontend mode)
	
	
	/**
	 * Constructor method checks whether admin or non-admin page is loaded and instantiates
	 * appropriate class. It also adds basic hooks universal for the whole plugin.
	 */
	public function __construct($plugin_path){
		
		// initiate settings wrapper (we're going to need it in admin frontend modes (+ callbacks):		
		$this->settings = new WooCommerce_Ultimate_Multi_Currency_Suite_Settings($plugin_path); 
		
		// run callbacks-handling class:		
		$this->callbacks = new WooCommerce_Ultimate_Multi_Currency_Suite_Callbacks($this->settings); // run callbacks class constructor and pass settings object to it
		
		// schedule or unschedule cron job:
		if ('automatic' == $this->settings->get_exchange_rates_update_method()){
			$this->settings->add_cron_job(); // schedule cron job if option this option is active in db
			add_action('wcumcs_cron_currency_exchange_rate_update_event', 'wcumcs_cron_currency_exchange_rate_update');
		} else { // unschedule
			$this->settings->remove_cron_job(); 
		}
		
		// WP hooks:		
		register_activation_hook($this->settings->get_plugin_path(), array($this, 'plugin_activate')); // Add plugin activation hook
		register_deactivation_hook($this->settings->get_plugin_path(), array($this, 'plugin_deactivate')); // Add plugin deactivation hook
		add_shortcode('wcumcs_switcher', 'wcumcs_switcher'); // register switcher shortcode
		add_action('widgets_init', array($this, 'add_widget')); // register switcher widget
		add_action('plugins_loaded', array($this, 'load_plugin_textdomain')); // Add translation files
		
		// run appropriate plugin mode: 	
		if (is_admin() && !defined('DOING_AJAX')){ // if admin and not doing AJAX - regular admin mode...
			$this->mode = 'admin';
		} else if (!is_admin()){ // if not admin - regular frontend mode...			
			$this->mode = 'frontend';
		} else if (is_admin() && defined('DOING_AJAX')){ // admin mode and doing AJAX - it can be either frontend request or backend request - we have to verify this			
			if (!empty($_REQUEST['action'])){
				// if one of these ajax events is fired, it means that we are in admin mode
				$admin_woocommerce_ajax_requests = array(
					'woocommerce_save_variations',
					'woocommerce_load_variations',
					'woocommerce_bulk_edit_variations',
					'woocommerce_json_search_products_and_variations',
					'woocommerce_add_order_item',
					'woocommerce_save_order_items',
					'woocommerce_calc_line_taxes',
					'woocommerce_add_new_attribute',
					'woocommerce_remove_variation',
					'woocommerce_remove_variations',
					'woocommerce_save_attributes',
					'woocommerce_add_variation',
					'woocommerce_refund_line_items',
					'woocommerce_load_order_items',
					'woocommerce_mark_order_status',
					'woocommerce_feature_product',
					'inline-save',
					'heartbeat'
				);				
				$this->mode = 'frontend'; // by default let's assume the mode is frontend. If one of the admin requests is called, this property will be changed to admin				
				foreach ($admin_woocommerce_ajax_requests as $ajax_request){
					if ($_REQUEST['action'] == $ajax_request){
						$this->mode = 'admin';
						break; // we've already found which mode to use, loop should not be continued
					}
				}				
			} else {
				$this->mode = 'frontend';
			}
		}
		
		switch ($this->mode){
			case 'admin':
				new WooCommerce_Ultimate_Multi_Currency_Suite_Admin($this->settings); // run admin mode class constructor and pass settings object
				break;
			case 'frontend':
				new WooCommerce_Ultimate_Multi_Currency_Suite_Frontend($this->settings); // run frontend mode class constructor and pass settings object		
				break;
			default :
				return false;
		}
		
	}
	
	
	/**
	 * Add plugin widget
	 */
	public function add_widget(){
		
		register_widget('WooCommerce_Ultimate_Multi_Currency_Suite_Widget');
		
	}
	
	
	/**
	 * Plugin activation callback (check environment compatibilty)
	 */
	public function plugin_activate(){
		
		if (!is_admin()){ // plugin activated outsite admin dashboard (?)
			deactivate_plugins(plugin_basename($this->settings->get_plugin_path())); // deactivate this plugin
			return;
		}

		// check PHP, WP and WC compatibility:
		$php = $this->settings->get_minimum_required('php');
		$wp = $this->settings->get_minimum_required('wp');
		
		global $wp_version; // WordPress version
		
		if (version_compare(PHP_VERSION, $php, '<')){
			$flag = 'PHP'; // PHP outdated
		}
		else if	(version_compare($wp_version, $wp, '<')){
			$flag = 'WordPress'; // WordPress outdated	
		}

		if (!empty($flag)){
			
			$version = null; // initialize $version var to be sure it exists
			
			switch ($flag){
				case 'PHP' :
					$version = $php;
					break;
				case 'WordPress' :
					$version = $wp;
					break;
				default :
					break;
			}
			
			deactivate_plugins(plugin_basename($this->settings->get_plugin_path())); // deactivate this plugin
			
			wp_die(
				sprintf(__('<p><strong>%s</strong> plugin requires %s version %s or greater.</p>', 'woocommerce-ultimate-multi-currency-suite'), 
					'WooCommerce Ultimate Multi Currency Suite', $flag, $version
				), 'Plugin Activation Error', array('response' => 200, 'back_link' => TRUE)
			);
			
		}
		
		if ($this->settings->is_plugin_in_db()){ // plugin in DB
			$this->settings->remove_cron_job(); // remove cron job
			if ('automatic' == $this->settings->get_exchange_rates_update_method()){
				$this->settings->add_cron_job(); // schedule cron job if option this option is active in db
			}			
		} else { // plugin not in DB
			$this->settings->restore_defaults(); // set up default plugin data in DB
		}
		
    }
	
	
	/**
	 * Plugin deactivation callback (remoe cron job)
	 */
	public function plugin_deactivate(){
		
		if (!is_admin()){ // plugin deactivated outsite admin dashboard (?)
			return;
		}
		
		$this->settings->remove_cron_job(); // remove cron job
		
	}
	
	
	/**
	 * Load plugin translation
	 */
	public function load_plugin_textdomain(){
		
		$dir_name = dirname($this->settings->get_plugin_path()); // this gives us full path to our plugin directory
		$dir_name = basename($dir_name); // this gives us only last directory in path (plugin directory - woocommerce-ultimate-multi-currency-suite)
		$dir_name .= '/languages'; // this gives us 'woocommerce-ultimate-multi-currency-suite/languages'
		load_plugin_textdomain('woocommerce-ultimate-multi-currency-suite', false, $dir_name);
		
	}
	
	
}
