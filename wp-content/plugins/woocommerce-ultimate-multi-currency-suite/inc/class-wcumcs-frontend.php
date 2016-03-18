<?php


/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * WooCommerce_Ultimate_Multi_Currency_Suite_Frontend class is a frontend-class for the plugin.
 *
 * This class is responsible for all actions by the user on the frontend. It converts currencies,
 * adds frontend WC filters.
 *
 */

// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}

class WooCommerce_Ultimate_Multi_Currency_Suite_Frontend {
	
	
	private $settings; // we're going to assign Settings object to this property
	private $currency; // chosen currency (3-letter code)
	private $currency_data; // all data on chosen currency (order, name, symbol, position, thousand sep, decimal sep, number decimals, rate, api)
	private $default_currency_data; // all data on default currency (code, name, symbol, position, thousand sep, decimal sep, number decimals)
	private $default_currency = true; // true if default currency is used (no conversion)
	private $conversion_method = 'checkout'; // reference or checkout
	private $products_final_prices = array(); // this array contains final prices of already converted items - so that they are not converted more than once
	private $currency_changed = false; // true if currency has just been changed
	private $page_cache_enabled = false; // is currency caching support enabled?
	
	
	/**
	 * Initiate object: add appropriate WP and WC filters, register callbacks
	 */
	public function __construct($settings){ // $settings = instance of WooCommerce_Ultimate_Multi_Currency_Suite_Settings
		
		// Assign settings object to class property, so we can easily access it from other methods:
		$this->settings = $settings;
		
		$this->conversion_method = $this->settings->get_conversion_method();
		
		$this->page_cache_enabled = $this->settings->page_cache_enabled();
		
		// Check if WooCommerce is active:
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
			
			// WP/WC hooks:
			add_action('init', array($this, 'init_session'), 1);
			add_action('wp_enqueue_scripts', array($this, 'add_scripts')); // Add css and js files (only for frontend)
			add_action('wp_footer', array($this, 'add_inline_js'), 9999); // Add inline JavaScript code (defined in admin dashboard)
			add_action('parse_request', array($this, 'parse_request'), -1); // we use this to see if WC API is not being called (-1 priority is NECESSARY to make sure we run this before WooCommerce API hook)
			add_action('template_redirect', array($this, 'currency_page_cache_ajax_redirect'), 9999); // this is used only when static page cache support is enabled
			
			if ($this->conversion_method == 'reference'){ // conversion only for user reference
				
				add_filter('woocommerce_currency_symbol', array($this, 'custom_currency_symbol'), 9999, 2); // switch currency symbol	
				add_filter('wc_price_args', array($this, 'price_formatting'), 9999); // price formatting 
				add_filter('woocommerce_price_format', array($this, 'custom_price_format'), 9999); // right/left etc. - this is not really needed, because wc_price_args does much more, but WC Composite Products need this
				add_filter('raw_woocommerce_price', array($this, 'custom_reference_price'), 9999); // convert all displayed prices to new amount		
				add_filter('woocommerce_cart_totals_order_total_html', array($this, 'show_total_in_base_currency'), 9999); // show full payment amount in base currency on checkout
				add_action('woocommerce_email_before_order_table', array($this, 'reset_currency_before_placing_order'), 9999); // this makes sure that if we are in reference mode, emails are not sent in converted values
		
			} else { // converting also for payment - change every price (checkout method)
				
				// Currency data:
				add_filter('woocommerce_currency_symbol', array($this, 'custom_currency_symbol'), 9999, 2); // switch currency symbol	
				add_filter('wc_price_args', array($this, 'price_formatting'), 9999); // price formatting 
				add_filter('woocommerce_price_format', array($this, 'custom_price_format'), 9999); // right/left etc. - this is not really needed, because wc_price_args does much more, but WC Composite Products need this
				add_filter('woocommerce_currency', array($this, 'custom_currency'), 9999); // change currency (3-letter code)
				// Prices:
				add_filter('woocommerce_get_regular_price', array($this, 'custom_item_price'), 9999, 2); // convert product regular price to new amount
				add_filter('woocommerce_get_sale_price', array($this, 'custom_item_price'), 9999, 2); // convert product sale price to new amount	
				add_filter('woocommerce_variation_prices_price', array($this, 'custom_item_price'), 9999, 3); // convert variation price to new amount 
				add_filter('woocommerce_variation_prices_regular_price', array($this, 'custom_item_price'), 9999, 3); // convert variation regular price to new amount 
				add_filter('woocommerce_variation_prices_sale_price', array($this, 'custom_item_price'), 9999, 3); // convert variation sale price to new amount 		
				add_filter('woocommerce_variation_prices', array($this, 'custom_variation_price'), 9999, 3); // convert variation price
				add_filter('woocommerce_get_variation_prices_hash', array($this, 'custom_variation_prices_hash'), 9999); // we need to filter the variation prices hash, to make sure WC displays correct price range
				add_filter('woocommerce_get_price', array($this, 'custom_item_price_final'), 9999, 2); // convert active product price to new amount
				add_filter('woocommerce_grouped_price_html', array($this, 'custom_group_price_html'), 9999, 2); // this is used to change display price of grouped products
				// Below 3 filters are never used since WC 2.4.x, but it's a good idea to keep them if, for example, other plugins want to use them (or these WC functions):
				add_filter('woocommerce_get_variation_regular_price', array($this, 'custom_item_price'), 9999, 3); // convert variation regular price to new amount 
				add_filter('woocommerce_get_variation_sale_price', array($this, 'custom_item_price'), 9999, 3); // convert variation sale price to new amount 	
				add_filter('woocommerce_get_variation_price', array($this, 'custom_item_price'), 9999, 3); // convert variation price to new amount 	
				// Coupons:
				add_filter('woocommerce_get_shop_coupon_data', array($this, 'custom_coupon_discount'), 9999, 2); // convert coupon discount and minimum and maximum order amount for discount	
				// Shipping:
				add_filter('woocommerce_package_rates', array($this, 'custom_shipping_price'), 9999, 2); // convert shipping costs or use predefined ones
				add_filter('woocommerce_shipping_free_shipping_is_available', array($this, 'custom_free_shipping_minimum'), 9999, 2); // convert minimum order amount to allow free shipping
				// Price filter:
				add_filter('woocommerce_price_filter_widget_min_amount', array($this, 'custom_price_filter_min'), 9999); // convert the minimum value that can be set by the price filter slider
				add_filter('woocommerce_price_filter_widget_max_amount', array($this, 'custom_price_filter_max'), 9999); // convert the maxiumum value that can be set by the price filter slider
				add_filter('woocommerce_price_filter_results', array($this, 'custom_price_filter_query'), 9999, 3); // convert prices used for price filter query
				// Mangohour Table Rate Shipping compatibility:
				add_filter('mh_table_rate_shipping_min_value', array($this, 'custom_mh_table_rate_shipping_min_or_max_value'), 9999, 2); // convert minimum value required for selected table rate shipping method
				add_filter('mh_table_rate_shipping_max_value', array($this, 'custom_mh_table_rate_shipping_min_or_max_value'), 9999, 2); // convert maximum value required for selected table rate shipping method
				// Composite products compatibility:
				add_filter('woocommerce_composite_get_base_price', array($this, 'custom_item_price_final'), 9999, 2); // convert composite product base price
				add_filter('woocommerce_composite_get_base_regular_price', array($this, 'custom_item_price'), 9999, 2);	// convert composite product regular base price
				add_filter('woocommerce_composite_get_base_sale_price', array($this, 'custom_item_price'), 9999, 2); // convert composite product sale base price

			}		
			
		}
		
	}
	
	
	/**
	 * Start session and get chosen (or remembered or geolocated) currency
	 */
	public function init_session(){
		
		$default_currency = get_woocommerce_currency(); // we set default currency as main one first		
		
		$default_currency_data = array();
		$default_currency_data['code'] = $default_currency;
		$default_currency_data['name'] = $this->settings->get_currency_name();
		$default_currency_data['symbol'] = get_woocommerce_currency_symbol();
		$default_currency_data['position'] = get_option('woocommerce_currency_pos');
		$default_currency_data['thousand_separator'] = wc_get_price_thousand_separator();
		$default_currency_data['decimal_separator'] = wc_get_price_decimal_separator();
		$default_currency_data['number_decimals'] = wc_get_price_decimals();
		$this->default_currency_data = $default_currency_data;
		
		if (!empty($_POST['wcumcs_change_currency_code'])){ // user have manually switched the currency
			
			$new_currency = $_POST['wcumcs_change_currency_code'];
			if ($this->settings->remember_user_currency()){ // if user currency should be remembered
				wc_setcookie('wcumcs_user_currency_cookie', $new_currency, time() + 7776000); // store it in a cookie for 90 days
			}
			$this->currency_changed = true;
			
		} else if (!empty($_COOKIE['wcumcs_user_currency_session'])){ // currency already in session data
			
			$new_currency = $_COOKIE['wcumcs_user_currency_session']; 
		
		} else if ($this->settings->remember_user_currency() && !empty($_COOKIE['wcumcs_user_currency_cookie'])){ // user currency from cookie (remember)
		
			$new_currency = $_COOKIE['wcumcs_user_currency_cookie'];
		
		} else if ($this->settings->ip_geolocation_enabled()){ // if IP geolocation enabled
			
			$location_info = WC_Geolocation::geolocate_ip(WC_Geolocation::get_ip_address()); // array with country code and state
			$country_code = apply_filters('wcumcs_visitor_country_code', $location_info['country']);
			if (false != $this->settings->get_country_currency($country_code)){
				$new_currency = $this->settings->get_country_currency($country_code);
			} else {
				$new_currency = $default_currency;
			}
			
		} else {
			
			$new_currency = $default_currency; // nothing specified - use default currency
			
		}
		
		$new_currency = strtoupper($new_currency); // make sure it's uppercased
		
		if (3 == strlen($new_currency) && ctype_alpha($new_currency)){ // make sure currency is 3-letter long and only alphabetic chars
			if (false == $this->settings->get_currency_data($new_currency)){ // this currency is not used
				$new_currency = $default_currency; // set to default currency
			}
		} else { // some problem with currency code...
			$new_currency = $default_currency; // set to default currency			
		}
		
		if (empty($_COOKIE['wcumcs_user_currency_session']) || $_COOKIE['wcumcs_user_currency_session'] != $new_currency){ // if transient cookie empty or different from new currency			
			wc_setcookie('wcumcs_user_currency_session', $new_currency, 0); // save transient cookie
			$this->currency_changed = true;
		}
		
		$this->currency = $new_currency; // save it in currency class property	
		$this->settings->session_currency = $this->currency; // save in settings property, so that switcher can access it later
		$this->currency_data = $this->settings->get_currency_data($new_currency); // assign currency_data to property	
		$this->settings->session_currency_data = $this->currency_data;

		if ($default_currency == $new_currency){
			$this->default_currency = true;
		} else {
			$this->default_currency = false;
		}
		
		if ($this->currency_changed == true){ // if user has changed the currency
			add_action('wp_loaded', array($this, 'recalculate_cart'), 9999); // recalculate the cart
		}
		
	}
	
	
	/**
	 * Recalculate WooCommerce cart
	 */
	public function recalculate_cart(){

		global $woocommerce;			
		if (!empty($woocommerce->cart)){ // check if cart exists (gives error sometimes if we dont check this)
			$woocommerce->cart->calculate_totals();		
		}
		
	}
	
	
	/**
	 * Add plugin CSS and JS files for frontend
	 */
	public function add_scripts(){

		// Add main admin CSS file:
		if (defined('WCUMCS_DEBUG')){
			wp_enqueue_style('wcumcs-frontend-style-handle', $this->settings->get_plugin_dir_url() . 'css/wcumcs-frontend.css');		
		} else {
			wp_enqueue_style('wcumcs-frontend-style-handle', $this->settings->get_plugin_dir_url() . 'css/wcumcs-frontend.min.css');	
		}
		
		wp_add_inline_style('wcumcs-frontend-style-handle', $this->settings->get_switcher_data('currency_switcher_css')); // add user custom css rules

		// Add main frontend JS file (make sure jquery is enqueued as well):
		if (defined('WCUMCS_DEBUG')){
			wp_enqueue_script('wcumcs-frontend-script-handle', $this->settings->get_plugin_dir_url() . 'js/wcumcs-frontend.js', array('jquery'));
		} else {
			wp_enqueue_script('wcumcs-frontend-script-handle', $this->settings->get_plugin_dir_url() . 'js/wcumcs-frontend.min.js', array('jquery'));
		}
		
		// [[<<<- INLINE JS IS LOCATED IN WP_FOOTER ACTION ->>>]]
		
		// Add variables to be accessed in JS files:
		$js_variables = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'currency_change_key' => 'wcumcs_change_currency_code', // $_POST array key used to change currency by user
			'currency_changed' => $this->currency_changed, // has currency been changed? This will be used by JS cart refresh after switching currency
			'conversion_method' => $this->settings->get_conversion_method(), // reference or checkout
			'base_currency' => $this->default_currency_data['code'], // base currency code
			'base_currency_data' => $this->default_currency_data, // base currency data
			'currency' => $this->settings->session_currency, // active currency code
			'currency_data' => $this->settings->session_currency_data, // active currency data
			'wp_nonce' => wp_create_nonce('woocommerce-ultimate-multi-currency-suite-nonce') // create nonce to verify ajax request
		);
		
		if ($this->page_cache_enabled == true){ // if we are using page cache support
			$page_cache_support_data = array(
				'home_url' => home_url(),
				'is_cart' => is_cart() ? '1' : '0',
				'is_account_page' => is_account_page() ? '1' : '0',
				'is_checkout' => is_checkout() ? '1' : '0',
				'is_customize' => is_customize_preview() ? '1' : '0',
				'hash' => isset($_GET['c']) ? wc_clean($_GET['c']) : ''
			);
			$js_variables['page_cache_support_data'] = $page_cache_support_data;
		}		
		
		wp_localize_script('wcumcs-frontend-script-handle', 'wcumcs_vars_data', $js_variables);
		
	}
	
	
	/**
	 * We're parsing request - we need to see if it is not WC API being called, as we don't want to convert 
	 * currencies when API is called (for example by PayPal IPN)
	 */
	public function parse_request(){
		
		global $wp;
		
		$api_url = home_url('/wc-api/'); // for example: http://mysite.com/wc-api/
		$current_url = "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
		while (strpos($current_url, '//') !== false){ // remove double slashes
			$current_url = str_replace('//', '/', $current_url);
		}
		$current_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $current_url; // add protocol
		
		// We need to see if it is WC API being called:
		if (!empty($_GET['wc-api'])){
			$wp->query_vars['wc-api'] = $_GET['wc-api'];
		}

		// wc-api endpoint requests (we look in query_vars or in the url just to be sure)
		if (!empty($wp->query_vars['wc-api']) || substr($current_url, 0, strlen($api_url)) === $api_url){
			$this->default_currency = true; 
			$this->currency_changed = false;			
			$currency = $this->default_currency_data['code'];			
			$this->settings->session_currency = $currency; // save in settings property
			$this->currency = $currency; // and in currency class property		
		}
		
		return;
		
	}
	
	
	/**
	 * Redirect if the location hash does not equal the querystring. This prevents caching of the wrong data for this request.
	 */
	public function currency_page_cache_ajax_redirect(){
		
		if ($this->page_cache_enabled == true){
			
			if (!is_checkout() && !is_cart() && !is_account_page() && !is_ajax() && empty($_POST)){
				
				$currency_hash = $this->settings->get_currency_hash();
				$current_currency_hash = isset($_GET['c']) ? wc_clean($_GET['c']) : '';
				
				if (empty($current_currency_hash) || $current_currency_hash !== $currency_hash){
					
					global $wp;
					$redirect_url = trailingslashit(home_url($wp->request));

					if (!empty($_SERVER['QUERY_STRING'])){
						$redirect_url = add_query_arg($_SERVER['QUERY_STRING'], '', $redirect_url);
					}

					if (!get_option('permalink_structure')){
						$redirect_url = add_query_arg($wp->query_string, '', $redirect_url);
					}
					
					$redirect_url = add_query_arg('c', $currency_hash, remove_query_arg('c', $redirect_url));
					wp_safe_redirect(esc_url_raw($redirect_url), 307);	
					
					exit;	
					
				}
				
			}
			
		}
		
	}
	
	
	/**
	 * Convert variation price 
	 * $prices_array = array( 'price' => array(), 'regular_price' => array(), 'sale_price' => array() )
	 */
	public function custom_variation_price($prices_array, $product_variation, $display){
		
		unset($prices_array); // we are rewriting the whole method anyway, so we can clear this array
		
		// We need to rewrite WC get_variation_prices() method from class-wc-product-variable.php to prevent variation price cache (and convert to new prices at the same time)
		$prices = array();
		$regular_prices = array();
		$sale_prices = array();
		$tax_display_mode = get_option('woocommerce_tax_display_shop');

		foreach ($product_variation->get_children(true) as $variation_id){
			if ($variation = $product_variation->get_child($variation_id)){
				$price = $variation->price;
				$regular_price = $variation->regular_price;
				$sale_price = $variation->sale_price;

				// If sale price does not equal price, the product is not yet on sale
				if (!$variation->is_on_sale()){
					$sale_price = $regular_price;
				}

				// If we are getting prices for display, we need to account for taxes
				if ($display){
					$price = $tax_display_mode == 'incl' ? $variation->get_price_including_tax(1, $price) : $variation->get_price_excluding_tax(1, $price);
					$regular_price = $tax_display_mode == 'incl' ? $variation->get_price_including_tax(1, $regular_price) : $variation->get_price_excluding_tax(1, $regular_price);
					$sale_price = $tax_display_mode == 'incl' ? $variation->get_price_including_tax(1, $sale_price) : $variation->get_price_excluding_tax(1, $sale_price);
				}
				
				// Convert prices: 
				$price = $this->custom_item_price($price, $variation);
				$regular_price = $this->custom_item_price($regular_price, $variation);
				$sale_price = $this->custom_item_price($sale_price, $variation);

				$prices[$variation_id] = $price;
				$regular_prices[$variation_id] = $regular_price;
				$sale_prices[$variation_id] = $sale_price;
				
			}
		}

		asort($prices);
		asort($regular_prices);
		asort($sale_prices);

		$converted_prices_array = array(
			'price' => $prices,
			'regular_price' => $regular_prices,
			'sale_price' => $sale_prices
		);
		
		return $converted_prices_array;
	
	}
	
	
	/**
	 * WC creates hash for each product, so that it can cache its min and max variation prices
	 * Unfortunately, because of constantly changing exchange rates/currencies/prices, it's just
	 * safer to invalidate the hash, so that WC has to calculate these prices using our filters. This
	 * does not put too much load on server, though (that's how WC used to do it prior to WC 2.5).
	 */
	public function custom_variation_prices_hash($price_hash){

		$price_hash = null; // empty the array;
		return $price_hash; // return empty array, so that WC has to figure out the prices
		
	}
	
	
	/**
	 * Add plugin inline JS code
	 */
	public function add_inline_js(){
		
		$js_template = '<script>%s</script>'; // template for custom JS code
		$js = $this->settings->get_switcher_data('currency_switcher_js'); // get custom JS code from DB
		$js = (string)$js; // just to make sure, cast to string
		
		if (!empty($js)){
			$output_code = sprintf($js_template, $js); //place JS code inside a template
			echo $output_code; // print it out
		}
		
	}	
	
	
	/**
	 * Changes display of grouped products prices
	 */
	public function custom_group_price_html($price_html, $product){
		
		if ($this->default_currency == true){ // return if we are using default currency
			return $price_html;
		}	
		
		$price = $price_html; // just to make sure our $price var has something assigned to it
		
		// Below is adapted from woocommerce/includes/class-wc-product-gruped.php		
		
		$tax_display_mode = get_option('woocommerce_tax_display_shop');
		$child_prices = array();
		
		$product_factory = new WC_Product_Factory();  // used to create product object from product (child) id
		foreach ($product->get_children() as $child_id){
			$temp_product = $product_factory->get_product($child_id);
			$temp_price = get_post_meta($child_id, '_price', true);	
			$child_prices[] = $this->custom_item_price_final($temp_price, $temp_product);
		}

		$child_prices = array_unique($child_prices);
		$get_price_method = 'get_price_' . $tax_display_mode . 'uding_tax';

		if (!empty($child_prices)){
			$min_price = min($child_prices);
			$max_price = max($child_prices);
		} else {
			$min_price = '';
			$max_price = '';
		}

		if ($min_price){
			if ($min_price == $max_price){
				$display_price = wc_price($product->$get_price_method(1, $min_price));
			} else {
				$from = wc_price($product->$get_price_method(1, $min_price));
				$to = wc_price($product->$get_price_method(1, $max_price));
				$display_price = sprintf(_x('%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce'), $from, $to);
			}

			$price = $display_price . $product->get_price_suffix();			
			
		} else {
			
			$price = $price_html;
			
		}
		
		return $price;
		
	}	

		
	/**
	 * Convert minimum order amount to allow free shipping
	 */
	public function custom_free_shipping_minimum($is_available, $package){
		
		if ($this->default_currency == true){ // return if we are using default currency
			return $is_available;
		}		

		// This is copied from WC_Shipping_Free_Shipping class, but we are using different (converted) min_amount:
		
		// Enabled logic
		$is_available = false;
		$has_coupon = false;
		$has_met_min_amount = false;		
		
		$woocommerce_settings = get_option('woocommerce_free_shipping_settings', null);
		
		$requires = $woocommerce_settings['requires']; // what is required by free shipping to be active
		$min_amount = $woocommerce_settings['min_amount']; // minimum order amount for free shipping to be active
		$min_amount = $this->convert_price($min_amount); // convert minimum order amount to active currency

		if (in_array($requires, array('coupon', 'either', 'both'))){
			if ($coupons = WC()->cart->get_coupons()){
				foreach ($coupons as $code => $coupon){
					if ($coupon->is_valid() && $coupon->enable_free_shipping()){
						$has_coupon = true;
					}
				}
			}
		}

		if (in_array($requires, array('min_amount', 'either', 'both')) && isset(WC()->cart->cart_contents_total)){
			if (WC()->cart->prices_include_tax){
				$total = WC()->cart->cart_contents_total + array_sum(WC()->cart->taxes);
			} else {
				$total = WC()->cart->cart_contents_total;
			}
			if ($total >= $min_amount){
				$has_met_min_amount = true;
			}
		}

		switch ($requires){
			case 'min_amount' :
				if ($has_met_min_amount){
					$is_available = true;
				}
				break;
			case 'coupon' :
				if ($has_coupon){
					$is_available = true;
				}
				break;
			case 'both' :
				if ($has_met_min_amount && $has_coupon){
					$is_available = true;
				}
				break;
			case 'either' :
				if ($has_met_min_amount || $has_coupon) {
					$is_available = true;
				}
				break;
			default :
				$is_available = true;
				break;
		}
		
		return $is_available;
		
	}
	
	
	/**
	 * Convert minimum value that can be set on price filter slider
	 */
	public function custom_price_filter_min($min){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $min;
		}
		
		if ($min == 0){ // return input parameter if 0 set as min
			return $min_or_max;
		}
		
		$converted_min = $this->convert_price($min);
		
		$converted_min = floor($converted_min); // because we are processing min price, we have to round it down		

		return $converted_min;
		
	}
	
	
	/**
	 * Convert maximum value that can be set on price filter slider
	 */
	public function custom_price_filter_max($max){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $max;
		}
		
		if ($max == 0){ // return input parameter if 0 set as max
			return $max;
		}
		
		$converted_max = $this->convert_price($max);
		
		$converted_max = ceil($converted_max); // because we are processing max price, we have to round it up

		return $converted_max;
		
	}
	
	
	/**
	 * Convert values used in query by price filter (min and max values)
	 */
	public function custom_price_filter_query($query, $min, $max){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $query;
		}
		
		if ($min != 0){ // convert only if different than 0
			$min = $this->unconvert_price($min);
			$min = floatval($min);
		}
		
		if ($max != 0){ // convert only if different than 0
			$max = $this->unconvert_price($max);
			$max = floatval($max);
		}
		
		// this is copied from WC_Query class, but we are using different $min and $max values here:
		global $wpdb;
		
		$query = $wpdb->get_results($wpdb->prepare('
			SELECT DISTINCT ID, post_parent, post_type FROM %1$s
			INNER JOIN %2$s ON ID = post_id
			WHERE post_type IN ( "product", "product_variation" )
			AND post_status = "publish"
			AND meta_key IN ("' . implode('","', apply_filters('woocommerce_price_filter_meta_keys', array('_price'))) . '")
			AND meta_value BETWEEN %3$f AND %4$f
			', $wpdb->posts, $wpdb->postmeta, $min, $max), OBJECT_K);
		
		return $query;	
		
	}
	
	
	/**
	 * This is called right before placing the order, only when in reference conversion mode
	 */
	public function reset_currency_before_placing_order(){
		
		// temporarily set it to default currency, so that no conversion or custom formatting is happening in emails in reference mode
		$this->default_currency = true; 
			
	}
	
	
	/**
	 * Show checkout total in store's base currency
	 */
	public function show_total_in_base_currency($html = ''){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			
			return $html;
		
		} else if (!$this->settings->total_in_base_currency()){ // return input parameter if user does not want to display total in base currency
			
			return $html;
		
		} else {		
			
			global $woocommerce;			
			$price = $woocommerce->cart->total; // get price in store's base currency			
			$price =  // begin formatting it
				number_format(
					$price, 
					$this->default_currency_data['number_decimals'], 
					$this->default_currency_data['decimal_separator'], 
					$this->default_currency_data['thousand_separator']
				);
			
			$symbol = $this->default_currency_data['symbol'];
			
			switch ($this->default_currency_data['position']){
				case 'left':					
					$price = $symbol . $price;
					break;
				case 'right':
					$price = $price . $symbol;
					break;
				case 'left_space':
					$price = $symbol . '&nbsp;' . $price;
					break;
				case 'right_space':
					$price = $price . '&nbsp;' . $symbol;
					break;
				default:
					break;
			}
			
			$price_template = apply_filters('wcumcs_checkout_total_in_base_currency_html', '<br /><span class="wcumcs_checkout_total_in_base_currency">%s %s</span>');
			
			$total_text = $this->settings->get_option('checkout_total_payment_text');
			if (empty($total_text)){
				$total_text = __('Total payment:', 'woocommerce-ultimate-multi-currency-suite');
			}
						
			$html .= sprintf($price_template, $total_text, $price);			
			
			return $html;
			
		}
			
	}
	
	
	/**
	 * Price formatting (currency position, decimal/thousand separator etc.)
	 */
	public function price_formatting($default_args){
		
		if ($this->should_modification_be_performed() == false){
			return $default_args;
		}
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			
			return $default_args;
		
		} else {				
			
			$args = array();
			
			switch ($this->currency_data['position']){
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
			$args['currency'] = $this->currency;
			$args['decimal_separator'] = $this->currency_data['decimal_separator'];
			$args['thousand_separator'] = $this->currency_data['thousand_separator'];
			$args['decimals'] = $this->currency_data['number_decimals'];
			$args['price_format'] = $format;	
			
			return $args;
		
		}
			
	}
	
	
	/**
	 * Return custom formatting style
	 */
	public function custom_price_format($format = '%1$s%2$s'){
		
		if ($this->should_modification_be_performed() == false){
			return $format;
		}
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $format;
		} else {
			switch ($this->currency_data['position']){
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
					break;
			}
			return $format;
		}
		
	}
	
	
	/**
	 * Switch currency code
	 */
	public function custom_currency($currency){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $currency;
		} else {
			$currency = $this->currency; 
			return $currency;
		}
		
	}
	
	
	/**
	 * Convert any price in reference mode
	 */
	public function custom_reference_price($price){
		
		if ($this->should_modification_be_performed() == false){
			return $price;
		}
		
		$final_price = $this->convert_price($price);
		
		return $final_price;
		
	}
	
	
	/**
	 * Convert products, variations and sales prices for checkout conversion method
	 */
	public function custom_item_price($price, $product, $min_or_max = null){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			
			return $price;
			
		} else {
			
			if (true == $this->settings->predefined_prices()){ // if we are using predefined prices
				
				$id = 0; // product or variation id
				$processing_sale_price = false; // is the currently processed price a sale price?
				
				if (!empty($product->variation_id)){ // we are processing variation
					$id = $product->variation_id; // use variation ID
				} else { // we are not processing variation - it is a regular product
					$id = $product->id; // use product id
					if ($product->is_type('variable') && !empty($min_or_max)){
						$variation_id = get_post_meta($id, '_' . $min_or_max . '_regular_price_variation_id', true);
						if (!empty($variation_id)){
							$id = $variation_id;
						}
					}
				}
				
				$sale_price = get_post_meta($id, '_sale_price', true); // get product/variation sale price
				$regular_price = get_post_meta($id, '_regular_price', true); // get product/variation regular price
				
				if (!empty($sale_price)){ // sale price is not empty, so it is POSSIBLE that we are processing sale price
					if ($price == $sale_price){ // currently processed price is the same as our sale price
						$processing_sale_price = true; // ...which means we are processing sale price
					} else if ($price == $regular_price){ // currently processed prise is the same as our regular price
						$processing_sale_price = false; // ...which means we are not processing sale price - it is a regular price
					} else { // something wrong - just convert it using normal conversion
						$final_price = $this->convert_price($price);	
						return $final_price; // and return cancelling the function
					}
				} else { // sale price is empty, so we are definitely NOT processing sale price
					$processing_sale_price = false; 
				}
				
				if ($processing_sale_price == true){ // if we are processing sale price
					$custom_price = $this->get_additional_price($id, 'sale'); // get data from sale price meta field for the current currency
				} else { // if we are not processing sale price, it means that we are processing regular price
					$custom_price = $this->get_additional_price($id, 'regular'); // so get its value from the current currency meta field
				}
				
				if (empty($custom_price)){ // if not specified additional price, use the converted supplied one
					$final_price = $this->convert_price($price);
				} else { // additional priice was specified, so let's use it
					$final_price = $custom_price;
				}
					
			} else { // not using predefined prices - just convert the price
				
				$final_price = $this->convert_price($price);				
				
			}
			
			return $final_price;
			
		}
		
	}
	
	
	/**
	 * Convert final product price
	 */
	public function custom_item_price_final($price, $product){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency			
			return $price;			
		}
		
		$id = 0; // product or variation id
		
		if (!empty($product->variation_id)){ // we are processing variation
			$id = $product->variation_id; // use variation ID
		} else { // we are not processing variation - it is a regular product
			$id = $product->id; // use product id			
		}
			
		if (!isset($this->products_final_prices[$id])){ // first time this product is being converted
			$final_price = $this->custom_item_price($price, $product); // calculate
			$this->products_final_prices[$id] = $final_price; // save to property
			return $final_price; // and return to filter
		} else { // has been converted before
			return $this->products_final_prices[$id]; // return what has been calculated before
		}
			
	}
		
		
	/**
	 * Convert specified amount to new currency
	 */
	public function convert_price($price){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $price;
		} else {
			// Below is used to create a clean float/int of whatever has been supplied (it figures out the decimal/thousand separators etc.).
			// Solution taken from here: http://php.net/manual/en/function.floatval.php#114486 Thanks!
			$dot_pos = strrpos($price, '.');
			$comma_pos = strrpos($price, ',');
			$sep = (($dot_pos > $comma_pos) && $dot_pos) ? $dot_pos : 
				((($comma_pos > $dot_pos) && $comma_pos) ? $comma_pos : false);
   
			if (!$sep) { // there are no separators in price - let's just make sure it is a correct int/float be removing antything that is not a number
				$price = floatval(preg_replace("/[^0-9]/", "", $price));
			} else { // there is a dot or comma - 
				$price = floatval(
					preg_replace("/[^0-9]/", "", substr($price, 0, $sep)) . '.' .
					preg_replace("/[^0-9]/", "", substr($price, $sep + 1, strlen($price)))
				);
			}

			// At this point we have very clean float value, which can be converted:
			$price = $price * $this->currency_data['rate']; // convert based on currency exchange rate
			$price = floatval(number_format($price, $this->currency_data['number_decimals'], '.', ''));

			return $price;

			// Old solution, causing some trouble when comma was used as a decimal separator:
			/* $price = str_replace($this->default_currency_data['thousand_separator'], '', $price); // remove thousand separator, so we can operate on standard float/int
			$price = floatval($price) * $this->currency_data['rate']; // convert based on currency exchange rate
			$price = number_format($price, $this->currency_data['number_decimals'], '.', '');
			$price = floatval($price);
			return $price; */
		}
		
	}
	
	
	/**
	 * Unconvert specified amount from new currency to base currency
	 */
	public function unconvert_price($price){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $price;
		} else {
			$price = str_replace($this->default_currency_data['thousand_separator'], '', $price); // remove thousand separator, so we can operate on standard float/int
			$price = floatval($price) / $this->currency_data['rate']; // convert based on currency exchange rate
			$price = number_format($price, wc_get_price_decimals(), '.', '');
			$price = floatval($price);
			return $price;
		}
		
	}
	
	
	/**
	 * Convert shipping costs
	 */
	protected $shipping_fee_cost = ''; // this is pretty ugly, but there is no other way to pass this to shortcode callback function
	public function custom_shipping_price($rates, $package){

		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $rates;
		}

		$order_total = $package['contents_cost']; // order total amount (float)
		$quantity = 0; // total number of items in cart
		$active_currency = $this->currency;

		foreach ($package['contents'] as $cart_item){ // loop through all cart items...
			$quantity += $cart_item['quantity']; // and add their quantities to form a total quantity
		}

		foreach ($rates as $rate){ // loop through each rate
			
			$id = $rate->id;

			if ($this->settings->predefined_prices()){ // using predefined prices
				$shipping_method_settings = get_option('woocommerce_' . $id . '_settings', null);
				$array_key = 'wcumcs_cost_' . $active_currency;
				if (isset($shipping_method_settings[$array_key])){ // if custom price is set

					$sum = $shipping_method_settings[$array_key];
					$locale = localeconv();
					$decimals = array(wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point']);
					$this->shipping_fee_cost = $order_total;

					add_shortcode('fee', array($this, 'fee'));
					$sum = do_shortcode(str_replace(array('[qty]', '[cost]'), array($quantity, $order_total), $sum));
					remove_shortcode('fee', array($this, 'fee'));

					// Remove whitespace from string
					$sum = preg_replace('/\s+/', '', $sum);
					// Remove locale from string
					$sum = str_replace($decimals, '.', $sum);
					// Trim invalid start/end characters
					$sum = rtrim(ltrim($sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/");
					// Do the math
					$rate->cost = $sum ? WC_Eval_Math::evaluate($sum) : 0;
				
				} else { // predefined price empty - just convert it
					$shipping_cost = $rate->cost; // store the previous cost in $shipping_cost
					$rate->cost = $this->convert_price($shipping_cost); // convert to different currency	
				}
			} else { // not using predefined prices - just convert it
				$shipping_cost = $rate->cost; // store the previous cost in $shipping_cost
				$rate->cost = $this->convert_price($shipping_cost); // convert to different currency	
			}

			$this->shipping_fee_cost = '';
						
		}			

		return $rates;		
		
	}
	
	
	/**
	 * Fee shortcode for alternative shipping cost
	 */
	public function fee($atts) {
		$atts = shortcode_atts( array(
			'percent' => '',
			'min_fee' => ''
		), $atts);

		$calculated_fee = 0;

		if ($atts['percent']){
			$calculated_fee = $this->shipping_fee_cost * (floatval($atts['percent']) / 100);
		}

		if ($atts['min_fee'] && $calculated_fee < $atts['min_fee']){
			$calculated_fee = $atts['min_fee'];
		}

		return $calculated_fee;
	}
	
	
	/**
	 * Convert minimum or maximum required value for Mangohour Table Rate Shipping plugin
	 */
	public function custom_mh_table_rate_shipping_min_or_max_value($value, $table_rate_basis){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $value;
		} 

		if ($table_rate_basis != 'price'){ // return input parameter if this calculation basis is, for example, weight (we only convert price)
			return $value;
		} else { // we are using table rate calculation method based on price, so we can convert it
			$converted_value = $this->convert_price($value);
			return $converted_value;
		}
		
	}
	
	
	/**
	 * Convert coupon required minimum and maximum order amount and coupon amount and return new coupon data
	 */
	public function custom_coupon_discount($is_coupon_custom, $coupon_code){
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $is_coupon_custom;
		} 
		
		// get coupon ID (post ID) from coupon code:
		global $wpdb;
		$coupon_id = absint($wpdb->get_var(
			$wpdb->prepare(
				apply_filters('woocommerce_coupon_code_query', 
					"SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish'"
				), $coupon_code
			)
		));
		
		$coupon_data_raw = array();		
		$coupon_data_raw = get_post_meta($coupon_id); // get all coupon data
		
		if (empty($coupon_data_raw)){ // no post meta found for this id 
			return false;
		}
		
		if ($coupon_code !== get_the_title($coupon_id)){ // if coupon does not exist
			return false;
		}
		
		// every meta data is stored in 1-element array inside $coupon_data_raw, so we need to extract this to array of strings:
		$coupon_data = array();
		foreach ($coupon_data_raw as $key => $value){
			$coupon_data[$key] = $value[0];
		}		
		
		// convert get coupon amounts:
		$minimum = $coupon_data['minimum_amount']; 
		$maximum = $coupon_data['maximum_amount'];
		$amount = $coupon_data['coupon_amount'];
		
		// convert minimum and maximum order amount, but only if different than 0 and not empty:
		if (!empty($minimum)){
			$minimum = $this->convert_price($minimum);
		}
		if (!empty($maximum)){
			$maximum = $this->convert_price($maximum);
		}
		
		// convert coupon amount, but only if not a percentage discount:
		if ($coupon_data['discount_type'] != 'percent_product' && $coupon_data['discount_type'] != 'percent'){			
			if ($this->settings->predefined_prices() == true){ // we are using predefined coupon amounts						
				$custom_coupon_value = $this->get_additional_coupon_amount($coupon_id);
				if (empty($custom_coupon_value) && $custom_coupon_value !== 0){ // alternative coupon amount not specified
					$amount = $this->convert_price($amount); // convert to different currency
				} else { // predefined coupon amount specified
					$amount = $custom_coupon_value; // use predefined value
				}						
			} else { // we are not using predefined coupon amounts, so simple conversion will do the trick			
				$amount = $this->convert_price($amount); // convert to different currency						
			}				
		}
		
		// assign converted amounts to coupon data:
		$coupon_data['minimum_amount'] = $minimum;
		$coupon_data['maximum_amount'] = $maximum; 
		$coupon_data['coupon_amount'] = $amount;
		
		return $coupon_data;		
		
	}
	
	
	/**
	 * Switch currency symbol to the new one
	 */
	public function custom_currency_symbol($currency_symbol, $currency){
		
		if ($this->should_modification_be_performed() == false){
			return $currency_symbol;
		}
		
		if ($this->default_currency == true){ // return input parameter if we are using default currency
			return $currency_symbol;
		} else {
			$currency_symbol = $this->currency_data['symbol']; 
			return $currency_symbol;
		}
		
	}
	
	
	/**
	 * Return predefined price of product/variation in supplied currency (or currently active currency, if not specified)
	 */
	private function get_additional_price($id, $type = 'regular', $currency = null){
		
		if (empty($currency)){
			$currency = $this->currency;
		}
		
		switch ($type){
		
			case 'regular' :
				$price = get_post_meta($id, '_wcumcs_regular_price_' . $currency, true);
				break;
			case 'sale' :
				$price = get_post_meta($id, '_wcumcs_sale_price_' . $currency, true);
				break;
			default :
				return false;		
		}
		
		$price = floatval($price);
				
		return $price;
				
	}
	
	
	/**
	 * Return predefined coupon amount in supplied currency (or currently active currency, if not specified)
	 */
	private function get_additional_coupon_amount($id, $currency = null){
		
		if (empty($currency)){
			$currency = $this->currency;
		}
		
		$amount = get_post_meta($id, '_wcumcs_coupon_amount_' . $currency, true);
		
		$amount = floatval($amount);
		
		return $amount;
		
	}
	
	
	/**
	 * Sometimes we do not want any conversion happening (for example when viewing order in My Account) -
	 * the order has already been placed and we don't want to tweak it. This method returns true if we can
	 * tweak the currencies (symbol, format etc.) or returns false if we should not do that.
	 */
	private function should_modification_be_performed(){
		
		// if we are inside order-received page (after payment) or view-order page (on viewing order from My account) or My account page:
		if (is_wc_endpoint_url('order-received') || is_wc_endpoint_url('view-order')){ 
			return false; // do not tweak currency
		} else if (did_action('setup_theme') > 0){ // we need to check if setup_theme action was performed, as this is the first time wc_get_page_permalink can be used
			if (wc_get_page_permalink('myaccount') == get_permalink()){ // if we are in my account page
				return false; // do not tweak the currency
			}
		}

		if (did_action('before_woocommerce_pay') > did_action('after_woocommerce_pay')){ // if we are currently running through order-pay page, but the form has not been fully outputted yet
			return false;
		}

		return true; // if none of the exceptions happened, return true - we can tweak the currencies
		
	}
	
	
}
