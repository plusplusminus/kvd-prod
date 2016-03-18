<?php


/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * WooCommerce_Ultimate_Multi_Currency_Suite_Admin class is a backend-class (admin) for the plugin.
 *
 * This class is responsible for all WP-Admin actions - adding plugin configuration UI to WooCommerce Settings page,
 * modifying the database (when applying settings), checking PHP, WooCommerce and WordPress compatibility (along with
 * making sure WooCommerce is active).
 *
 */

// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}

class WooCommerce_Ultimate_Multi_Currency_Suite_Admin {
	
	
	private $settings; // We're going to assign Settings object to this property	
	private $id; // Settings tab name
	private $label; // Settings tab label
	
	
	/**
	 * Initiate object: set properties, add appropriate WP and WC filters, register callbacks
	 */
	public function __construct($settings){ // $settings = instance of WooCommerce_Ultimate_Multi_Currency_Suite_Settings
		
		// Assign settings object to class property, so we can easily access it from other methods:
		$this->settings = $settings;
		$this->id = 'multi_currency_suite'; // new settings tab name
		
		// WordPress hooks:
		add_action('admin_enqueue_scripts', array($this, 'add_scripts')); // Add css and js files (only for admin dashboard)
		add_action('woocommerce_settings_save_general', array($this, 'check_base_currency'), 9999); // check to see if user hasnt changed his base currency (action after WC general settings save)
		add_filter('plugin_action_links_' . plugin_basename($this->settings->get_plugin_path()), array($this, 'add_settings_link')); // Add settings link on plugins page
		add_action('admin_init', array($this, 'plugin_update'), 9999); // update notification - check for updates
		add_action('add_meta_boxes', array($this, 'add_meta_box_to_order_page')); // add plugin's meta box to order page
		add_action('save_post', array($this, 'save_meta_box_from_order_page')); // used to store currency meta box in the database
		
		// Check if WooCommerce is active:
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
			// If WC active, add WC filters/hooks/actions:
			add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 9999); // Add new WooCommerce settings tab
			add_action('woocommerce_settings_' . $this->id, array($this, 'output'), 9999); // Output settings page
			add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'), 9999); // Save current section
			add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'), 9999); // Display section list
			add_filter('woocommerce_currency_symbol', array($this, 'custom_currency_symbol'), 9999, 2); // switch currency symbol
			add_filter('wc_price_args', array($this, 'price_formatting'), 1); // price formatting 
			// additional price fields (only if option enabled in settings and using checkout conversion):
			if (true == $this->settings->predefined_prices() && 'checkout' == $this->settings->get_conversion_method()){
				add_action('woocommerce_coupon_options', array($this, 'add_additional_coupon_fields'), 9999); // add additional value fields to coupon edit pages
				add_action('woocommerce_coupon_options_save', array($this, 'save_additional_coupon_fields'), 9999, 1); // save additional value fields on coupon edit pages
				add_action('woocommerce_product_options_pricing', array($this, 'add_additional_price_fields_product'), 9999); // add additional prices fields on product edit page
				add_action('woocommerce_process_product_meta', array($this, 'save_additional_price_fields_product'), 9999, 1);  // save additional prices fields on product edit page
				add_action('woocommerce_product_after_variable_attributes', array($this, 'add_additional_price_fields_variation'), 9999, 3); // add additional prices fields on variation edit page
				add_action('woocommerce_process_product_meta_variable', array($this, 'save_additional_price_fields_variation'), 9999, 1); // save additional prices fields on variation edit page
				add_action('woocommerce_save_product_variation', array($this, 'save_additional_price_fields_variation'), 9999, 1); // save additional prices fields on variation edit page using AJAX
				// Shipping:
				add_filter('woocommerce_settings_api_form_fields_flat_rate', array($this, 'add_additional_flat_rate_fields'), 9999, 1); // add additional cost fields for flat rate
				add_filter('woocommerce_settings_api_form_fields_international_delivery', array($this, 'add_additional_flat_rate_fields'), 9999, 1); // same method for international flat rate

			}
			
		}
		
	}
	
	
	/**
	 * Add plugin CSS and JS files for admin dashboard
	 */
	public function add_scripts(){

		// Add main admin CSS file:
		if (defined('WCUMCS_DEBUG')){
			wp_enqueue_style('wcumcs-admin-style-handle', $this->settings->get_plugin_dir_url() . 'css/wcumcs-admin.css');
		} else {
			wp_enqueue_style('wcumcs-admin-style-handle', $this->settings->get_plugin_dir_url() . 'css/wcumcs-admin.min.css');
		}

		// Add main admin JS file (make sure jquery and jquery-ui-sortable are enqueued as well):
		if (defined('WCUMCS_DEBUG')){
			wp_enqueue_script('wcumcs-admin-script-handle', $this->settings->get_plugin_dir_url() . 'js/wcumcs-admin.js', array('jquery', 'jquery-ui-sortable', 'jquery-effects-fade'));
		} else {
			wp_enqueue_script('wcumcs-admin-script-handle', $this->settings->get_plugin_dir_url() . 'js/wcumcs-admin.min.js', array('jquery', 'jquery-ui-sortable', 'jquery-effects-fade'));
		}
		
		// this is probably the safest way to check if WooCommerce is active. We need this function, so if it exsists, we can continue with other stuff:
		if (function_exists('get_woocommerce_currency')){
			// Add variables to be accessed in JS files:
			$js_variables = array(
				'default_currency' => get_woocommerce_currency(),			
				'default_currency_name' => $this->settings->get_currency_name(),
				'default_currency_symbol' => get_woocommerce_currency_symbol(),
				'default_currency_position' => get_option('woocommerce_currency_pos'),
				'default_currency_thousand_separator' => wc_get_price_thousand_separator(),
				'default_currency_decimal_separator' => wc_get_price_decimal_separator(),
				'default_currency_number_decimals' => wc_get_price_decimals(),			
				'default_exchange_rate' => $this->settings->get_exchange_rate(),
				'default_exchange_api' => $this->settings->get_exchange_api(),
				'too_many_currencies' => __('You cannot use more than 40 currencies.', 'woocommerce-ultimate-multi-currency-suite'),
				'too_few_currencies' => __('You must use at least one currency.', 'woocommerce-ultimate-multi-currency-suite'),
				'currency_already_on_the_list' => __('This currency is already on the currencies list.', 'woocommerce-ultimate-multi-currency-suite'),
				'is_default_currency' => __('Default currency', 'woocommerce-ultimate-multi-currency-suite'),
				'add_currency_to_list' => __('+ Add selected currency to the list', 'woocommerce-ultimate-multi-currency-suite'),
				'currency_code' => __('Currency code', 'woocommerce-ultimate-multi-currency-suite'),
				'currency_name' => __('Currency name', 'woocommerce-ultimate-multi-currency-suite'),
				'currency_symbol' => __('Currency symbol', 'woocommerce-ultimate-multi-currency-suite'),
				'currency_position' => __('Currency position', 'woocommerce-ultimate-multi-currency-suite'),
				'thousand_separator' => __('Thousand separator', 'woocommerce-ultimate-multi-currency-suite'),
				'decimal_separator' => __('Decimal separator', 'woocommerce-ultimate-multi-currency-suite'),
				'number_decimals' => __('Number of decimals', 'woocommerce-ultimate-multi-currency-suite'),	
				'remove_currency' => __('Remove currency', 'woocommerce-ultimate-multi-currency-suite'),				
				'left' => __('Left', 'woocommerce-ultimate-multi-currency-suite'),
				'right' => __('Right', 'woocommerce-ultimate-multi-currency-suite'),
				'left_space' => __('Left with space', 'woocommerce-ultimate-multi-currency-suite'),
				'right_space' => __('Right with space', 'woocommerce-ultimate-multi-currency-suite'),
				'exchange_api' => __('Currency exchange rate API', 'woocommerce-ultimate-multi-currency-suite'),
				'exchange_rate' => __('Currency exchange rate', 'woocommerce-ultimate-multi-currency-suite'),
				'update_rate' => __('Update using API', 'woocommerce-ultimate-multi-currency-suite'),
				'update_label' => __('Update now &nbsp;&#8635;', 'woocommerce-ultimate-multi-currency-suite'),
				'update_all_label' => __('Update all exchange rates &nbsp;&#8635;', 'woocommerce-ultimate-multi-currency-suite'),
				'restore_defaults_button' => __('Restore default settings', 'woocommerce-ultimate-multi-currency-suite'),
				'change_currencies_button' => __('Change currencies', 'woocommerce-ultimate-multi-currency-suite'),
				'save_changes_button' => __('Save changes', 'woocommerce-ultimate-multi-currency-suite'), 
				'country_iso_code' => __('2-letter ISO 3166-1 country code', 'woocommerce-ultimate-multi-currency-suite'), 
				'currency_iso_code' => __('3-digit ISO 4217 currency code', 'woocommerce-ultimate-multi-currency-suite'), 
				'add_country_button' => __('Add country', 'woocommerce-ultimate-multi-currency-suite'), 
				'confirm_restore_defaults_msg' => __('Are you sure you want to restore Multi Currency Suite to its default configuration? This will erase all your plugin data!', 'woocommerce-ultimate-multi-currency-suite'),
				'operation_could_not_be_completed_msg' => __('Server error occured - operation could not be completed. Please try again later.',  'woocommerce-ultimate-multi-currency-suite'),
				'error_currency_exchange_rate_update_msg' => __('Error occured while updating the currency exchange rate. Please try using different API(s). The following currencies were not updated: ',  'woocommerce-ultimate-multi-currency-suite'),
				'success_currency_exchange_rate_update_msg' => __('Exchange rates have been updated successfully.',  'woocommerce-ultimate-multi-currency-suite'),
				'plugin_dir_url' => $this->settings->get_plugin_dir_url(),
				'wp_nonce' => wp_create_nonce('woocommerce-ultimate-multi-currency-suite-nonce') // create nonce to verify ajax request
			);
			wp_localize_script('wcumcs-admin-script-handle', 'wcumcs_vars_data', $js_variables);
		}
	}
	
	
	/**
	 * Update/update notification system
	 */
	public function plugin_update(){
		
		if (!$this->settings->is_plugin_activated()){
			return false;
		} else {
			$plugin_dir = dirname($this->settings->get_plugin_path()); // this gives us full path to our plugin directory
			$dir_name = basename($plugin_dir); // this gives us only last directory in path (plugin directory - woocommerce-ultimate-multi-currency-suite)
			$file_name = $dir_name . '.php'; 
			$plugin_slug = "$dir_name/$file_name"; // just plugin dir and filename
			$plugin_location = "$plugin_dir/$file_name"; // full absolute path
			
			$plugin_data = get_plugin_data($plugin_location);
			$version = $plugin_data['Version'];
			
			$update_api = 'http://api.dev49.net/edge/get/product/woocommerce-ultimate-multi-currency-suite/update_data/';	
			$license_key = $this->settings->get_activation_code();
			
			new WooCommerce_Ultimate_Multi_Currency_Suite_Update_Notifier($version, $update_api, $plugin_slug, $license_key);			
		}		
		
	}
	
	
	/**
	 * Add plugin's meta box to edit order page
	 */
	public function add_meta_box_to_order_page(){
		
		add_meta_box('wcumcs_currency_meta_box', __('Order currency', 'woocommerce-ultimate-multi-currency-suite'), 
			array($this, 'display_order_page_meta_box'), 'shop_order', 'side', 'high'
		);		
		
	}
	
	
	/**
	 * Display plugin's meta box on order edit page
	 */
	public function display_order_page_meta_box($post){

		wp_nonce_field('wcumcs_order_meta_box_save', 'wcumcs_order_meta_box_nonce');
		
		$order = new WC_Order($post);
		$order_currency_code = $order->get_order_currency();
		if (empty($order_currency_code)){ // if we are creating the order, let's set it to base currency
			$order_currency_code = get_woocommerce_currency();
		}
		$order_id = $order->id; // this is the post id for this order - NOT THE ORDER NUMBER. Order number can be different than its ID!

		$currency_code_options = get_woocommerce_currencies();
		foreach ($currency_code_options as $code => $name) { // form the list similarily to how WooCommerce does it in General settings section
			$currency_code_options[$code] = $code . ' - ' . $name . ' (' . get_woocommerce_currency_symbol($code) . ')';
		}

		if (empty($currency_code_options[$order_currency_code])){ // if currency used for this order is not among WC currencies, let's just add it manually (without name)
			$currency_code_options[$code] = $code;
		}
		
		$currency_list_html = '<select name="wcumcs_order_currency" id="wcumcs_order_currency">';

		foreach ($currency_code_options as $code => $name){
			$currency_selected_text = '';
			if ($code == $order_currency_code){
				$currency_selected_text = ' selected';
			}			
			$currency_list_html .= '<option value="' . $code . '"' . $currency_selected_text . '>' . $name . '</option>';
		}

		$currency_list_html .= '</select>';

		$meta_box_content_html = '<h4>';
		$meta_box_content_html .= __('Choose order currency', 'woocommerce-ultimate-multi-currency-suite');
		$meta_box_content_html .= '<span class="woocommerce-help-tip" data-tip="';
		$meta_box_content_html .= __("Select currency here and click Save Order for change to take effect. Please keep in mind that changing currency WILL NOT convert order items prices.", 'woocommerce-ultimate-multi-currency-suite');
		$meta_box_content_html .= '"></span></h4>';
		$meta_box_content_html .= $currency_list_html;

		echo $meta_box_content_html;
		
	}
	
	
	/**
	 * Saves data from meta box to DB
	 */
	public function save_meta_box_from_order_page($post_id){

		if (!isset($_POST['wcumcs_order_meta_box_nonce'])){ // nonce not in POST data
			return;
		}	
		
		$nonce = $_POST['wcumcs_order_meta_box_nonce'];

		if (!wp_verify_nonce($nonce, 'wcumcs_order_meta_box_save')){ // verify nonce
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return;
		}

		if (empty($_POST['post_type']) || $_POST['post_type'] != 'shop_order'){
			return;
		}

		if (!current_user_can('edit_shop_orders', $post_id)){
			return;
		}

		if (empty($_POST['wcumcs_order_currency'])){
			return;
		}

		$new_order_currency = sanitize_text_field($_POST['wcumcs_order_currency']);
		
		update_post_meta($post_id, '_order_currency', $new_order_currency);

	}
	
	
	/**
	 * Check to see if user hasnt changed his base currency - if he has, update exchange rates
	 */
	public function check_base_currency(){
		
		$woocommerce_currency = get_option('woocommerce_currency');
		$currency = $this->settings->get_option('woocommerce_base_currency');
		
		if ($woocommerce_currency != $currency){
			
			$this->settings->update_option('woocommerce_base_currency', $woocommerce_currency); // update plugin option
			$currencies_data = $this->settings->get_currency_data();
			$currencies_list = array();
			
			foreach ($currencies_data as $code => $data){
				$currencies_list[$code] = array();
				$currencies_list[$code]['api'] = $this->settings->get_exchange_api();
			}
			
			$update_results = $this->settings->update_exchange_rates($currencies_list); // update exchange rates
			
			$failed_currencies = array(); // array containing currencies code which were not updated
			foreach ($update_results as $currency_code => $currency_data){				
				// First we need to check if there were problems; if there were - dont update
				if ($currency_data['error'] != 0){
					$failed_currencies[] = $currency_code; // add currency code to failed_currencies array
				}
			}
			
			if (empty($failed_currencies)){
				$message = __('WooCommerce base currency has been modified. Exchange rates of your currencies have been automatically updated using default API.',  'woocommerce-ultimate-multi-currency-suite');
				$message_class = 'updated';
			} else {
				$failed_currencies_txt = '';
				foreach ($failed_currencies as $failed_currency){
					$failed_currencies_txt .= $failed_currency . ', ';
				}
				$failed_currencies_txt = substr($failed_currencies_txt, 0, -2);
				$message = sprintf(__('WooCommerce base currency has been modified. Exchange rates of your currencies have been automatically updated using default API. There were problems with updating the following currencies: %s.',  'woocommerce-ultimate-multi-currency-suite'), $failed_currencies_txt);
				$message_class = 'error';
			}			
			echo '<div id="wcumcs_message" class="' . $message_class . ' fade"><p><strong>' . esc_html($message) . '</strong></p></div>';
		}
		
	}
	
	
	/**
	 * Add additional value fields to coupon edit pages
	 */
	public function add_additional_coupon_fields(){
		
		$currencies = $this->settings->get_currency_data();
		
		if (count($currencies) < 1){
			return false; // cancel execution if no currencies are specified
		}
		
		if (count($currencies) == 1 && !empty($currencies[get_woocommerce_currency()])){
			return false; // cancel execution if base currency is the only currency
		}
		
		?>
			<div class="wcumcs_additional_coupon_values_wrapper">
				<h6 class="wcumcs_additional_coupon_values_header"><?php _e("Coupon amounts (alternative currencies)", 'woocommerce-ultimate-multi-currency-suite'); ?></h6>
				<p class="wcumcs_additional_coupon_values_not_available"><?php _e("Coupon amounts for alternative currencies are available only for non-percent based coupons.", 'woocommerce-ultimate-multi-currency-suite'); ?></p>
				
				<div class="wcumcs_additional_coupon_values_available_wrapper">
					<p class="wcumcs_additional_coupon_values_description"><?php _e("If specified, values entered below will be used instead of automatically calculated one for alternative currencies. If you want to have amounts converted automatically, leave the field empty.", 'woocommerce-ultimate-multi-currency-suite'); ?></p>

					<?php		
						foreach ($currencies as $code => $currency_data){			
							if ($code == get_woocommerce_currency()){
								continue; // if we are looping through base currency, skip it
							}		
							?>
								<div class="wcumcs_additional_coupon_values_currency">
									<?php
										woocommerce_wp_text_input(
											array( 
												'id' => '_wcumcs_coupon_amount_' . $code, 
												'label' => __('Coupon amount', 'woocommerce') . ' (' . $code . '):', 
												'placeholder' => __("Auto", 'woocommerce-ultimate-multi-currency-suite'),
												'description' => sprintf(__('Enter coupon amount in %s or leave the field empty if you want it to be calculated automatically.', 'woocommerce-ultimate-multi-currency-suite'), $code),
												'data_type' => 'price', 
												'class' => 'wc_input_price', // WC class to enable automatic price format validation
												'desc_tip' => true 
											)
										);
									?>
								</div>
							<?php				
						}					
					?>
				</div>
			</div>
		<?php		
		
	}
	
	
	/**
	 * Save additional value fields on coupon edit pages
	 */
	public function save_additional_coupon_fields($post_id){
		
		$currencies = $this->settings->get_currency_data();
		
		foreach ($currencies as $code => $currency_data){ // loop through all currencies to save specified ones	
			
			if ($code == get_woocommerce_currency()){
				continue; // if we are looping through base currency, skip it
			} else {
				if (isset($_POST['_wcumcs_coupon_amount_' . $code])){
					$coupon_amount = $_POST['_wcumcs_coupon_amount_' . $code];
					if ($coupon_amount !== ''){
						$coupon_amount = floatval($coupon_amount); // make sure it is a correct float
					}
					update_post_meta($post_id, '_wcumcs_coupon_amount_' . $code, esc_attr($coupon_amount)); // update data
				} 															
			}
			
		}
		
	}
	
	
	/**
	 * Add additonal multi-currency price fields for product (non-variable)
	 */
	public function add_additional_price_fields_product(){
		
		$currencies = $this->settings->get_currency_data();
		
		if (count($currencies) < 1){
			return false; // cancel execution if no additional currencies are specified
		}
		
		if (count($currencies) == 1 && !empty($currencies[get_woocommerce_currency()])){
			return false; // cancel execution if base currency is the only currency
		}
		
		?>
			<div class="wcumcs_additional_prices_product_wrapper">
				<h6 class="wcumcs_additional_prices_product_header"><?php _e("Product multi-currency prices", 'woocommerce-ultimate-multi-currency-suite'); ?></h6>
				<p class="wcumcs_additional_prices_product_description"><?php _e("If specified, prices entered below will be used instead of automatically calculated ones. If you want to have prices converted automatically, leave the field empty.", 'woocommerce-ultimate-multi-currency-suite'); ?></p>

				<?php		
					foreach ($currencies as $code => $currency_data){			
						if ($code == get_woocommerce_currency()){
							continue; // if we are looping through base currency, skip it
						}		
						?>
							<div class="wcumcs_additional_prices_product_currency">
								<?php
									woocommerce_wp_text_input(
										array(
											'id' => '_wcumcs_regular_price_' . $code, 
											'label' => __('Regular Price', 'woocommerce') . ' (' . $code . '):',
											'desc_tip' => 'true',
											'description' => sprintf(__('Enter regular price in %s or leave the field empty if you want it to be calculated automatically.', 'woocommerce-ultimate-multi-currency-suite'), $code),
											'class' => 'wc_input_price', // WC class to enable automatic price format validation
											'placeholder' => __("Auto", 'woocommerce-ultimate-multi-currency-suite')
										)
									);
								?>
							</div>
							<div class="wcumcs_additional_prices_product_currency">
								<?php
									woocommerce_wp_text_input(
										array(
											'id' => '_wcumcs_sale_price_' . $code, 
											'label' => __('Sale Price', 'woocommerce') . ' (' . $code . '):',
											'desc_tip' => 'true',
											'description' => sprintf(__('Enter sale price in %s or leave the field empty if you want it to be calculated automatically. Please note that this price will only be used if sale has been activated for this product - sale must be activated for all currencies.', 'woocommerce-ultimate-multi-currency-suite'), $code),
											'class' => 'wc_input_price', // WC class to enable automatic price format validation
											'placeholder' => __("Auto", 'woocommerce-ultimate-multi-currency-suite')
										)
									);
								?>
							</div>
						<?php				
					}					
				?>
			</div>
		<?php
		
	}
	
	
	/**
	 * Save additonal multi-currency price fields for product (non-variable)
	 */
	public function save_additional_price_fields_product($post_id){
		
		$currencies = $this->settings->get_currency_data();
		
		foreach ($currencies as $code => $currency_data){ // loop through all currencies to save specified ones	
			
			if ($code == get_woocommerce_currency()){
				continue; // if we are looping through base currency, skip it
			} else {
				if (isset($_POST['_wcumcs_regular_price_' . $code]) && isset($_POST['_wcumcs_sale_price_' . $code])){
					$regular_price = $_POST['_wcumcs_regular_price_' . $code];
					$sale_price = $_POST['_wcumcs_sale_price_' . $code];
					if ($regular_price !== ''){
						$regular_price = floatval($regular_price); // make sure it is a correct float
					}
					if ($sale_price !== ''){
						$sale_price = floatval($sale_price); // make sure it is a correct float
					}
					update_post_meta($post_id, '_wcumcs_regular_price_' . $code, esc_attr($regular_price)); // update data
					update_post_meta($post_id, '_wcumcs_sale_price_' . $code, esc_attr($sale_price)); // update data	
				} 															
			}
			
		}
		
	}
	
	
	/**
	 * Add additonal multi-currency price fields for variations of a product
	 */
	public function add_additional_price_fields_variation($loop, $variation_data, $variation){
		
		$currencies = $this->settings->get_currency_data();
		
		if (count($currencies) < 1){
			return false; // cancel execution if no additional currencies are specified
		}
		
		if (count($currencies) == 1 && !empty($currencies[get_woocommerce_currency()])){
			return false; // cancel execution if base currency is the only currency
		}
		
		?>
			<div class="wcumcs_additional_prices_variation_wrapper">
				<h6 class="wcumcs_additional_prices_variation_header"><?php _e("Variation multi-currency prices", 'woocommerce-ultimate-multi-currency-suite'); ?></h6>
				<p class="wcumcs_additional_prices_variation_description"><?php _e("If specified, prices entered below will be used instead of automatically calculated ones. If you want to have prices converted automatically, leave the field empty.", 'woocommerce-ultimate-multi-currency-suite'); ?></p>

				<?php		
					foreach ($currencies as $code => $currency_data){			
						if ($code == get_woocommerce_currency()){
							continue; // if we are looping through base currency, skip it
						}		
						?>
							<div class="wcumcs_additional_prices_variation_currency">
								<?php
									woocommerce_wp_text_input(
										array(
											'id' => '_wcumcs_regular_price_' . $code . '[' . $loop . ']', 
											'label' => __('Regular Price', 'woocommerce') . ' (' . $code . '):',
											'desc_tip' => 'true',
											'description' => sprintf(__('Enter regular price in %s or leave the field empty if you want it to be calculated automatically.', 'woocommerce-ultimate-multi-currency-suite'), $code),
											'class' => 'wc_input_price', // WC class to enable automatic price format validation
											'placeholder' => __("Auto", 'woocommerce-ultimate-multi-currency-suite'),
											'value' => get_post_meta($variation->ID, '_wcumcs_regular_price_' . $code, true)
										)
									);
								?>
							</div>
							<div class="wcumcs_additional_prices_variation_currency">
								<?php
									woocommerce_wp_text_input(
										array(
											'id' => '_wcumcs_sale_price_' . $code . '[' . $loop . ']', 
											'label' => __('Sale Price', 'woocommerce') . ' (' . $code . '):',
											'desc_tip' => 'true',
											'description' => sprintf(__('Enter sale price in %s or leave the field empty if you want it to be calculated automatically. Please note that this price will only be used if sale has been activated for this variation - sale must be activated for all currencies.', 'woocommerce-ultimate-multi-currency-suite'), $code),
											'class' => 'wc_input_price', // WC class to enable automatic price format validation
											'placeholder' => __("Auto", 'woocommerce-ultimate-multi-currency-suite'),
											'value' => get_post_meta($variation->ID, '_wcumcs_sale_price_' . $code, true)
										)
									);
								?>
							</div>
						<?php				
					}					
				?>
			</div>
		<?php
						
	}
	
	
	/**
	 * Save additonal multi-currency price fields for variations of a product
	 */
	public function save_additional_price_fields_variation($post_id){
		
		if (isset($_POST['variable_sku'])){ // this is to make sure that we are saving variation
			
			$currencies = $this->settings->get_currency_data(); // array of all available currencies
			$variable_post_ids = $_POST['variable_post_id']; // this is an array of all variations that are being modified (it may return one or more variations for AJAX-based
															 // update and all variations for regular product Update. 
			
			foreach ($currencies as $code => $currency_data){ // loop through all currencies to save specified ones	(we only need currency code)
				if ($code == get_woocommerce_currency()){
					continue; // if we are looping through base currency, skip it
				} else {
					if (isset($_POST['_wcumcs_regular_price_' . $code]) && isset($_POST['_wcumcs_sale_price_' . $code])){ // this currency prices are in POST data
						$regular_prices = $_POST['_wcumcs_regular_price_' . $code]; // this array contains all variations' regular prices for this currency
						$sale_prices = $_POST['_wcumcs_sale_price_' . $code]; // this array contains all variations' sale prices for this currency
						$number_of_processed_variations = sizeof($variable_post_ids); // by measuring the size of array, we can find out how many variations are being processed
						foreach ($variable_post_ids as $array_key => $current_variation_id){ // we loop through all processed variations
							$current_variation_id = intval($current_variation_id); // just make sure current variation ID is a corrent int	
							update_post_meta($current_variation_id, '_wcumcs_regular_price_' . $code, esc_attr($regular_prices[$array_key])); // update data
							update_post_meta($current_variation_id, '_wcumcs_sale_price_' . $code, esc_attr($sale_prices[$array_key])); // update data							
						}
					}
				}
			}
			
		}
		
	}
	
	
	/**
	 * Add additional cost fields to flat rate
	 */
	public function add_additional_flat_rate_fields($form_fields){	

		$default_currency = get_woocommerce_currency();

		$available_currencies_json = $this->settings->get_option('available_currencies');
		$available_currencies = json_decode($available_currencies_json, true);
		$requested_currencies_data = array();

		foreach ($available_currencies as $code => $currency_data){ // we only need code

			if ($code == $default_currency){ // if we are looping through base currency, let's skip that
				continue;
			}

			$form_fields['wcumcs_cost_' . $code] = array(
				'title' => __('Cost', 'woocommerce') . ' (' . $code . ')',
				'type' => 'text',
				'placeholder' => __("Auto", 'woocommerce-ultimate-multi-currency-suite'),
				'description' => sprintf(__('Enter shipping cost in %s or leave the field empty if you want it to be calculated automatically.', 'woocommerce-ultimate-multi-currency-suite'), $code),
				'default' => '',
				'desc_tip' => true
			);

		}

		return $form_fields;
		
	}
	
	
	/**
	 * Switch currency symbol to the pre-configured one
	 */
	public function custom_currency_symbol($currency_symbol, $currency){
		
		if (get_woocommerce_currency() == $currency){ // if we are filtering default currency, return default settings
			return $currency_symbol;
		}
		
		$currency_data = $this->settings->get_currency_data(); // get all currency data
		
		if (empty($currency_data[$currency])){ // if we do not have this currency data in our array, return default settings
			return $currency_symbol;
		}
		
		$currency_symbol = $currency_data[$currency]['symbol'];
			
		return $currency_symbol;		
		
	}
	
	
	/**
	 * Price formatting (currency position, decimal/thousand separator etc.)
	 */
	public function price_formatting($default_args){
		
		$currency = $default_args['currency'];
			
		if (get_woocommerce_currency() == $currency){ // if we are filtering default currency, return default settings
			return $default_args;
		}
		
		$currency_data = $this->settings->get_currency_data(); // get all currency data
		
		if (empty($currency_data[$currency])){ // if we do not have this currency data in our array, return default settings
			return $default_args;
		}			
			
		$args = array();
			
		switch ($currency_data[$currency]['position']){
			case 'left':
				$format = '%1$s%2$s';
				break;
			case 'right':
				$format = '%2$s%1$s';
				break;
			case 'left_space':
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space':
				$format = '%2$s&nbsp;%1$s';
				break;
			default:
				$format = $default_args['price_format'];
				break;
		}
			
		$args['ex_tax_label'] = $default_args['ex_tax_label'];
		$args['currency'] = $currency;
		$args['decimal_separator'] = $currency_data[$currency]['decimal_separator'];
		$args['thousand_separator'] = $currency_data[$currency]['thousand_separator'];
		$args['decimals'] = $currency_data[$currency]['number_decimals'];
		$args['price_format'] = $format;	
		
		return $args;
		
	}
	
	
	/**
	 * Add settings link under plugin name on Plugins page
	 */
	public function add_settings_link($links){
		
		// add localised Settings link do plugin settings on plugins page:
		$settings_link = '<a href="' . admin_url('admin.php?page=wc-settings&tab=multi_currency_suite') . '">' . __('Settings', 'woocommerce-ultimate-multi-currency-suite') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
		
    }	
	
	
	/**
	 * Get tab sections
	 */
	public function get_sections(){

		$sections = array(
			'' => __('General', 'woocommerce-ultimate-multi-currency-suite'),
			'currencies' => __('Currencies', 'woocommerce-ultimate-multi-currency-suite'),
			'exchange_rates' => __('Exchange rates', 'woocommerce-ultimate-multi-currency-suite'),
			'display' => __('Display', 'woocommerce-ultimate-multi-currency-suite'),
			'advanced' => __('Advanced', 'woocommerce-ultimate-multi-currency-suite')
		);
		
		// Add activation section only if plugin has not been activated yet:
		 if (!$this->settings->is_plugin_activated()){
			$sections['plugin_activation'] = __('<strong style="text-decoration:underline">Plugin activation</strong>', 'woocommerce-ultimate-multi-currency-suite');
		} 

		return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
		
    }
	
	
	/**
	 * Display current section
	 */
	public function output(){
		
		global $current_section;
		$configuration = $this->get_settings($current_section);
		woocommerce_admin_fields($configuration);
		
    }
	
	
	/**
	 * Save current section settings
	 */
	public function save(){
		
		global $current_section;
		$configuration = $this->get_settings($current_section);
		woocommerce_update_options($configuration);
		
    }
	
	
	/**
	 * Get tab fields for specified section
	 */
	public function get_settings($current_section = ''){
		
		$configuration = null; // initialize empty $configuration var
		
		if ($current_section == 'plugin_activation'){
			
			$activation_code = $this->settings->get_option('plugin_activation_code');
			if (!empty($activation_code)){ // activation code has been entered - let's verify it
				if ($this->settings->verify_activation_code($activation_code)){ // code is correct
					$this->settings->activate_plugin($activation_code);
					echo '<div id="message" class="updated fade"><p><strong>' . __('Plugin has been activated successfully.', 'woocommerce-ultimate-multi-currency-suite') . '</strong></p></div>';
				} else { // code is incorrect
					$this->settings->deactivate_plugin();
					echo '<div id="message" class="error fade"><p><strong>' . __('Purchase code incorrect. Please try again.', 'woocommerce-ultimate-multi-currency-suite') . '</strong></p></div>';
				}	
			}
			
			$configuration = apply_filters('woocommerce_' . $this->id . 'plugin_activation', array(
					
					array(
						'title' => __('Plugin activation', 'woocommerce-ultimate-multi-currency-suite'),
						'type' => 'title',
						'desc' => __('Please activate this plugin using your Envato Marketplace purchase code. You can find your purchase code in Downloads section of your Envato profile and in the e-mail you received right after purchasing this item. Activating the plugin gives you access to free automatic updates with new features as well as security fixes.<br />See <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Can-I-Find-my-Purchase-Code-">Where Can I Find My Purchase Code?</a> article on Envato.', 'woocommerce-ultimate-multi-currency-suite'),
						'id' => $this->settings->get_prefix() . 'plugin_activation_options'
					),

					array(
						'title' => __('Your purchase code', 'woocommerce-ultimate-multi-currency-suite'),
						'type' => 'text',
						'css' => 'min-width:450px;',
						'default' => '',
						'desc_tip' => __('Paste your plugin purchase code here.', 'woocommerce-ultimate-multi-currency-suite'),
						'id' => $this->settings->get_prefix() . 'plugin_activation_code'	
					),	
					
					array(
						'type' => 'sectionend',
						'id' => $this->settings->get_prefix() . 'plugin_activation'
					)
					
				));	
			
		}
		
		else if ($current_section == 'advanced'){		
			
			add_thickbox(); // for countries' currencies
			
			// if json in DB is empty, fill it with default data:
			$json_countries_currencies = $this->settings->get_option('country_list_data');
			if (empty($json_countries_currencies) || $json_countries_currencies == '[]' || $json_countries_currencies == '{}'){
				update_option($this->settings->get_prefix() . 'country_list_data', json_encode($this->settings->countries_currencies));
			} else { // convert json from DB to array, sort by key alphabetically to keep it clean, search for duplicates (and remove) and save back to DB (only if it has been changed):
				$json_countries_currencies_uppercased = strtoupper($json_countries_currencies); // make sure it's uppercase
				$array_countries_currencies = json_decode($json_countries_currencies_uppercased, true);
				ksort($array_countries_currencies, SORT_NATURAL); // sort alphabetically by key	
				$previous_key = '';
				foreach ($array_countries_currencies as $key => $value){ // search for duplicate keys and convert to uppercase
					if ($key === $previous_key){
						unset($array_countries_currencies[$key]); // remove if found
					}
					$previous_key = $key;
				}
				$json_countries_currencies_sorted = json_encode($array_countries_currencies);
				if ($json_countries_currencies_sorted != $json_countries_currencies){
					update_option($this->settings->get_prefix() . 'country_list_data', $json_countries_currencies_sorted);
				}			
			}
						
			$configuration = apply_filters('woocommerce_' . $this->id . '_advanced_settings', array(
				
				array(
					'title' => __('Advanced configuration', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'title',
					'desc' => __('This section is intended to be used by advanced users.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'advanced_options'		
				),

				array(
					'type' => 'sectionend',
					'id' => $this->settings->get_prefix() . 'advanced_options'
				),
				
				array(
					'title' => __('Country currency data', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'title',
					'id' => $this->settings->get_prefix() . 'advanced_country_data_options'		
				),
				
				array(
					'title' => __('Change currencies', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'textarea',
					'default' => json_encode($this->settings->countries_currencies),
					'css' => 'display:none',
					'desc_tip' => __("Use this option if you want to specify different default currencies for specific countries. This option is only used when geolocation feature is active.", 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'country_list_data'
				),

				array(
					'type' => 'sectionend',
					'id' => $this->settings->get_prefix() . 'advanced_country_data_options'
				),
				
				array(
					'title' => __('Additional options', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'title',
					'id' => $this->settings->get_prefix() . 'advanced_additional_options'		
				),
				
				array(
					'title' => __('Static page cache', 'woocommerce-ultimate-multi-currency-suite'),
					'desc' => __("Currency switching with page caching support", 'woocommerce-ultimate-multi-currency-suite'),
					'desc_tip' => __('If you are using a static caching plugin, currency switching may not be working properly. Enabling this feature appends additional query string at the end of the URL (depending on the active currency), which causes cache invalidation.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'page_cache_support',
					'type' => 'checkbox',
					'default' => 'no'
				),

				array(
					'title' => __('Restore default settings', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'text',
					'css' => 'display:none',
					'desc_tip' => __("If you experience significant problems with this Multi Currency plugin, you can restore plugin to its default settings. Please keep in mind that this function completely erases all your Multi Currency Suite settings!", 'woocommerce-ultimate-multi-currency-suite'),
					'class' => $this->settings->get_prefix() . 'advanced_additional_restore_defaults_input'	
				),
				
				array(
					'css' => 'display:none',
					'type' => 'text',
				),

				array(
					'type' => 'sectionend',
					'id' => $this->settings->get_prefix() . 'advanced_additional_options'
				)

			));	
			
		}
		
		else if ($current_section == 'display'){
			
			$configuration = apply_filters('woocommerce_' . $this->id . 'display_settings', array(
				
				array(
					'title' => __('Display options', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'title',
					'desc' => __('Configure how your users see this plugin.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'display_options'
				),

				array(
					'title' => __('Currency switcher text', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'text',
					'css' => 'min-width:330px;',
					'default' => __('Choose your preferred currency', 'woocommerce-ultimate-multi-currency-suite'),
					'desc_tip' => __('Type in the text that will appear above the currency switcher. You can leave this field empty if you do not want any text to appear above the switcher.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'currency_switcher_text'	
				),	
				
				array(
					'title' => __('Currency switcher theme', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'radio',
					'default' => 'dropdown',
					'options' => array(
						'dropdown' => __("Dropdown list",  'woocommerce-ultimate-multi-currency-suite') . '
							<span class="wcumcs_switcher_dropdown wcumcs_switcher_theme_option"><img src="' . $this->settings->get_plugin_dir_url() . 'img/switcher_themes/dropdown.png" alt="" /></span>',
						'buttons' => __("Buttons",  'woocommerce-ultimate-multi-currency-suite') . '
							<span class="wcumcs_switcher_buttons wcumcs_switcher_theme_option"><img src="' . $this->settings->get_plugin_dir_url() . 'img/switcher_themes/buttons.png" alt="" /></span>'
					),
					'desc_tip' => __("Select your preferred method of displaying currency switcher. To insert the currency switcher on your page you can use a widget, shortcode or PHP function. See plugin documentation for more information.", 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'currency_switcher_theme'
				),

				array(
					'title' => __('Custom currency template', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'text',
					'css' => 'min-width:330px;',
					'default' => '',
					'desc_tip' => __('Type in the custom template of how the currency should be displayed in currency switcher. Use the following tags: %code%, %name%, %symbol%. Leave empty if you want to use default settings. See plugin documentation for more information.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'currency_switcher_display_template'	
				),	

				array(
					'title' => __('Custom CSS rules', 'woocommerce-ultimate-multi-currency-suite'),
					'desc_tip' => __('Type in additional CSS code if you would like to change how the currency switcher is displayed. Please see plugin documentation for more information.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'currency_switcher_css',
					'css' => 'min-width: 450px; min-height: 100px; font-family: monospace;',
					'type' => 'textarea',
					'default' => ''
				),

				array(
					'title' => __('Custom JavaScript code', 'woocommerce-ultimate-multi-currency-suite'),
					'desc_tip' => __('Type in additional JavaScript code if you whish to increase plugin functionality. This JavaScript code will be injected at the bottom of your site.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'currency_switcher_js',
					'css' => 'min-width: 450px; min-height: 100px; font-family: monospace;',
					'type' => 'textarea',
					'default' => ''
				),
				
				array(
					'type' => 'sectionend',
					'id' => $this->settings->get_prefix() . 'display_options'
				)
				
			));	
			
		}
		
		else if ($current_section == 'exchange_rates'){
			
			// Get our currency settings so we can list all currencies currently in use:
			$currencies_data_string = get_option($this->settings->get_prefix() . 'available_currencies');
			$currencies_data = json_decode($currencies_data_string, true);
			$currencies_in_use = array();
			$currency_codes_list = array();
			foreach ($currencies_data as $currency_code => $currency_data){
				$currencies_in_use[$currency_code] = array();
				$currencies_in_use[$currency_code]['name'] = $currency_data['name'];
				$currencies_in_use[$currency_code]['symbol'] = $currency_data['symbol'];
				$currency_codes_list[] = $currency_code;
			}
			$currencies_in_use_json = json_encode($currencies_in_use, JSON_UNESCAPED_UNICODE);
			
			// save it to option:
			update_option($this->settings->get_prefix() . 'inner_use_currencies_in_use', $currencies_in_use_json);
			
			$configuration_array = array(				
				array(
					'title' => __('Currency exchange rates', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'title',
					'desc' => __('Configure exchange rates for your currencies.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'exchange_rates_options'		
				),
				
				array(
					'title' => __('Exchange rates update', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'radio',
					'default' => 'update',
					'options' => array(
						'automatic' => __("Automatically update exchange rates once a day using chosen services (schedules WordPress Cron job)",  'woocommerce-ultimate-multi-currency-suite'),
						'manual' => __("Do not automatically update exchange rates - use fixed exchange rates specified below",  'woocommerce-ultimate-multi-currency-suite')
					),
					'desc_tip' => __('Choose whether you want to have daily updated exchange rates or fixed exchange rates.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'exchange_rates_update_method'
				),
				
				array(
					'title' => __('Your currencies', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'text',
					'css' => 'display:none;',
					'desc_tip' => __("Specify the exchange rate manually or click Update to update exchange rate using one of provided third party services. Exchange rate value is in relation to your store's default currency.", 'woocommerce-ultimate-multi-currency-suite'),
					'class' => $this->settings->get_prefix() . 'exchange_rates'	
				),	

				array(
					'type' => 'sectionend',
					'id' => $this->settings->get_prefix() . 'exchange_rates_options'
				),
				
				array( // this one lists all currencies semicolon-delimited (with names and symbols)
					'type' => 'text',
					'css' => 'display:none;',
					'id' => $this->settings->get_prefix() . 'inner_use_currencies_in_use'
				)

			);
			
			foreach ($currency_codes_list as $currency_code){
				$configuration_array[] = 
					array(
						'type' => 'text',
						'css' => 'display:none;',
						'default' => '1',
						'id' => $this->settings->get_prefix() . 'exchange_rate_' . $currency_code
					);
				$configuration_array[] = 
					array(
						'type' => 'text',
						'css' => 'display:none;',
						'default' => $this->settings->get_exchange_api(),
						'id' => $this->settings->get_prefix() . 'exchange_api_' . $currency_code
					);
			}
			
			$configuration = apply_filters('woocommerce_' . $this->id . '_exchange_rates_settings', $configuration_array);	
			
		}
		
		else if ($current_section == 'currencies'){
			
			// Get all currencies for our New currency select box:
			$currencies_list = get_woocommerce_currencies();
			$currencies_options_list = array(); // create an array that will be used in settings select field	
			$currency_position = get_option('woocommerce_currency_pos');
			$thousand_separator = wc_get_price_thousand_separator();
			$decimal_separator = wc_get_price_decimal_separator();
			$number_decimals = wc_get_price_decimals();

			foreach ($currencies_list as $code => $name){ // loop through all currencies
				$symbol = get_woocommerce_currency_symbol($code);				
				$option_value = 
					$code . '#data_sep#' . 
					$name . '#data_sep#' . 
					$symbol . '#data_sep#' . 
					$currency_position . '#data_sep#' . 
					$thousand_separator . '#data_sep#' . 
					$decimal_separator. '#data_sep#' . 
					$number_decimals;
				$option_text = $code . ' - ' . $name . ' (' . $symbol . ')';
				$currencies_options_list[$option_value] = $option_text;
			}
			
			$configuration = apply_filters('woocommerce_' . $this->id . '_currency_settings', array(
				
				array(
					'title' => __('Currencies list', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'title',
					'desc' => __('Configure list of currencies available to your users.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'currencies_options'		
				),		

				array(
					'title' => __('Available currencies', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'text',
					'css' => 'display:none;',
					'desc_tip' => __('These are currencies available to your users. Drag and drop currencies to control their display order on the frontend.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'available_currencies'	
				),	

				array(
					'title' => __('Add new currency', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'select',
					'options' => $currencies_options_list, // assign options from the array extracted from XML file
					'class' => 'wc-enhanced-select-nostd ' . $this->settings->get_prefix() . 'new_currency',
					'css' => 'width:320px;',
					'desc_tip' => __('Choose new currency from the list', 'woocommerce-ultimate-multi-currency-suite')
				),

				array(
					'type' => 'sectionend',
					'id' => $this->settings->get_prefix() . 'currencies_options'
				)			

			));	
		
		} 
		
		else {
		
			$configuration = apply_filters('woocommerce_' . $this->id . 'general_settings', array(
				
				array(
					'title' => __('General settings', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'title',
					'desc' => __('Configure general Multi Currency Suite settings.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'general_options'
				),
				
				array(
					'title' => __('Alternative currencies', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'radio',
					'default' => 'checkout',
					'options' => array(
						'reference' => __("Use additional currencies for user's reference only (payment in store's <strong>base currency</strong>)",  'woocommerce-ultimate-multi-currency-suite'),
						'checkout' => __("Use additional currencies for user's reference and for checkout (payment in <strong>alternative currency</strong>)",  'woocommerce-ultimate-multi-currency-suite')
					),
					'desc_tip' => __('Choose whether payment should happen in base currency or in alternative currency. Please keep in mind that some currencies may NOT be supported by certain payment gateways. If you are not sure which option to choose, please select the first one.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'conversion_method'
				),
				
				 array(
					'title' => __('Display options', 'woocommerce-ultimate-multi-currency-suite'),
					'desc' => __("Show checkout total in store's base currency (only for payments in <strong>base currency</strong>)", 'woocommerce-ultimate-multi-currency-suite'),
					'desc_tip' => __("In checkout, right before the payment is made, user will see the final amount he will have to pay in store's base currency.", 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'checkout_total_in_base_currency',
					'type' => 'checkbox',
					'default' => 'yes',
					'css' => 'display:none;'
				), 

				array(
					'title' => __('Checkout total text', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'text',
					'css' => 'min-width:330px;',
					'default' => __('Total payment:', 'woocommerce-ultimate-multi-currency-suite'),
					'desc_tip' => __('If you decided to show total payment amount on checkout page before the actual payment, you can define this text here.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'checkout_total_payment_text'	
				),	

				array(
					'title' => __('Your email address', 'woocommerce-ultimate-multi-currency-suite'),
					'type' => 'text',
					'css' => 'min-width:330px;',
					'default' => get_option('admin_email'),
					'desc_tip' => __('If automatic currency exchange rates update fails, a notification will be sent to this email address. If you do not want to receive the notification, leave this field empty.', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'email_address'	
				),	
				
				array(
					'title' => __('Additional options', 'woocommerce-ultimate-multi-currency-suite'),
					'desc' => __("Remember visitor's currency choice", 'woocommerce-ultimate-multi-currency-suite'),
					'desc_tip' => __('The next time user visits your website, his currency will be automatically changed to the one he has chosen previously (creates a cookie).', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'remember_user_chosen_currency',
					'type' => 'checkbox',
					'default' => 'no',
					'checkboxgroup' => 'start'
				),

				array(
					'desc' => __('Enable IP geolocation', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'geolocation',
					'type' => 'checkbox',
					'default' => 'yes',
					'desc_tip' => sprintf(__("When user visits your website, his currency will be automatically switched to currency used in his country. This option will only work if you have enabled this currency in Currencies section. Otherwise, store's base currency will be used. Currency for every country can be changed in <a href=\"%s\">Advanced</a> section.", 'woocommerce-ultimate-multi-currency-suite'), admin_url('admin.php?page=wc-settings&tab=multi_currency_suite&section=advanced')),
					'checkboxgroup' => ''
				),

				array(
					'desc' => __('Allow predefined product and variation prices, coupon amounts and shipping costs in available currencies (only for payments in <strong>alternative currency</strong>)', 'woocommerce-ultimate-multi-currency-suite'),
					'id' => $this->settings->get_prefix() . 'predefined_prices',
					'type' => 'checkbox',
					'default' => 'yes',
					'desc_tip' => __("You can set prices in various currencies for each product and its variations on WooCommerce product edit pages, as well as coupon amounts on coupon edit pages.", 'woocommerce-ultimate-multi-currency-suite'),
					'checkboxgroup' => 'end'
				),
				
				array(
					'type' => 'sectionend',
					'id' => $this->settings->get_prefix() . 'general_options'
				)
				
			));	
		
		}
		
		return apply_filters('woocommerce_get_settings_' . $this->id, $configuration, $current_section);
		
    }
	
	
	/**
	 * Add new tab (page) to WooCommerce Settings page
	 */
	public function add_settings_page($pages){		
		
		// Tab name:
		$this->label = __('Multi Currency Suite', 'woocommerce-ultimate-multi-currency-suite');
		
		$pages[$this->id] = $this->label;
		return $pages;
		
	}	
	

	/**
	 * Output section list
	 */
	public function output_sections(){
		
		global $current_section;
		
		$sections = $this->get_sections();

		if (empty($sections)){
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys($sections);

		foreach ($sections as $id => $label){ // Display tab sections
			echo '<li><a href="' . admin_url('admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title($id)) . '" class="' 
					. ($current_section == $id ? 'current' : '') . '">' . $label . '</a> ' . (end($array_keys) == $id ? '' : '|') . ' </li>';
		}

		echo '</ul><br class="clear" />';
		
	}
	
	
}
