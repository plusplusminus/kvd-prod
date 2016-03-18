<?php


/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * WooCommerce_Ultimate_Multi_Currency_Suite_Settings class is a class responsible for retrieving and 
 * verifying plugin settings and storing default values.
 *
 * This class reads settings, checks if data is valid and stores default plugin settings and data and does
 * the exchange rates update.
 *
 */

// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}

class WooCommerce_Ultimate_Multi_Currency_Suite_Settings {
	
	
	// Data:
	public $countries_currencies = 
		array(
			'AD' => 'EUR',
			'AE' => 'AED',
			'AF' => 'AFN',
			'AG' => 'XCD',
			'AI' => 'XCD',
			'AL' => 'ALL',
			'AM' => 'AMD',
			'AO' => 'AOA',
			'AR' => 'ARS',
			'AS' => 'USD',
			'AT' => 'EUR',
			'AU' => 'AUD',
			'AW' => 'AWG',
			'AX' => 'EUR',
			'AZ' => 'AZN',
			'BA' => 'BAM',
			'BB' => 'BBD',
			'BD' => 'BDT',
			'BE' => 'EUR',
			'BF' => 'XOF',
			'BG' => 'BGN',
			'BH' => 'BHD',
			'BI' => 'BIF',
			'BJ' => 'XOF',
			'BL' => 'EUR',
			'BM' => 'BMD',
			'BN' => 'BND',
			'BO' => 'BOB',
			'BQ' => 'USD',
			'BR' => 'BRL',
			'BS' => 'BSD',
			'BT' => 'BTN',
			'BV' => 'NOK',
			'BW' => 'BWP',
			'BY' => 'BYR',
			'BZ' => 'BZD',
			'CA' => 'CAD',
			'CC' => 'AUD',
			'CD' => 'CDF',
			'CF' => 'XAF',
			'CG' => 'XAF',
			'CH' => 'CHE',
			'CI' => 'XOF',
			'CK' => 'NZD',
			'CL' => 'CLF',
			'CM' => 'XAF',
			'CN' => 'CNY',
			'CO' => 'COP',
			'CR' => 'CRC',
			'CU' => 'CUC',
			'CV' => 'CVE',
			'CW' => 'ANG',
			'CX' => 'AUD',
			'CY' => 'EUR',
			'CZ' => 'CZK',
			'DE' => 'EUR',
			'DJ' => 'DJF',
			'DK' => 'DKK',
			'DM' => 'XCD',
			'DO' => 'DOP',
			'DZ' => 'DZD',
			'EC' => 'USD',
			'EE' => 'EUR',
			'EG' => 'EGP',
			'EH' => 'MAD',
			'ER' => 'ERN',
			'ES' => 'EUR',
			'ET' => 'ETB',
			'EU' => 'EUR',
			'FI' => 'EUR',
			'FJ' => 'FJD',
			'FK' => 'FKP',
			'FM' => 'USD',
			'FO' => 'DKK',
			'FR' => 'EUR',
			'GA' => 'XAF',
			'GB' => 'GBP',
			'GD' => 'XCD',
			'GE' => 'GEL',
			'GF' => 'EUR',
			'GG' => 'GBP',
			'GH' => 'GHS',
			'GI' => 'GIP',
			'GL' => 'DKK',
			'GM' => 'GMD',
			'GN' => 'GNF',
			'GP' => 'EUR',
			'GQ' => 'XAF',
			'GR' => 'EUR',
			'GS' => 'GBP',
			'GT' => 'GTQ',
			'GU' => 'USD',
			'GW' => 'XOF',
			'GY' => 'GYD',
			'HK' => 'HKD',
			'HM' => 'AUD',
			'HN' => 'HNL',
			'HR' => 'HRK',
			'HT' => 'HTG',
			'HU' => 'HUF',
			'ID' => 'IDR',
			'IE' => 'EUR',
			'IL' => 'ILS',
			'IM' => 'GBP',
			'IN' => 'INR',
			'IO' => 'USD',
			'IQ' => 'IQD',
			'IR' => 'IRR',
			'IS' => 'ISK',
			'IT' => 'EUR',
			'JE' => 'GBP',
			'JM' => 'JMD',
			'JO' => 'JOD',
			'JP' => 'JPY',
			'KE' => 'KES',
			'KG' => 'KGS',
			'KH' => 'KHR',
			'KI' => 'AUD',
			'KM' => 'KMF',
			'KN' => 'XCD',
			'KP' => 'KPW',
			'KR' => 'KRW',
			'KW' => 'KWD',
			'KY' => 'KYD',
			'KZ' => 'KZT',
			'LA' => 'LAK',
			'LB' => 'LBP',
			'LC' => 'XCD',
			'LI' => 'CHF',
			'LK' => 'LKR',
			'LR' => 'LRD',
			'LS' => 'LSL',
			'LT' => 'EUR',
			'LU' => 'EUR',
			'LV' => 'EUR',
			'LY' => 'LYD',
			'MA' => 'MAD',
			'MC' => 'EUR',
			'MD' => 'MDL',
			'ME' => 'EUR',
			'MF' => 'EUR',
			'MG' => 'MGA',
			'MH' => 'USD',
			'MK' => 'MKD',
			'ML' => 'XOF',
			'MM' => 'MMK',
			'MN' => 'MNT',
			'MO' => 'MOP',
			'MP' => 'USD',
			'MQ' => 'EUR',
			'MR' => 'MRO',
			'MS' => 'XCD',
			'MT' => 'EUR',
			'MU' => 'MUR',
			'MV' => 'MVR',
			'MW' => 'MWK',
			'MX' => 'MXN',
			'MY' => 'MYR',
			'MZ' => 'MZN',
			'NA' => 'NAD',
			'NC' => 'XPF',
			'NE' => 'XOF',
			'NF' => 'AUD',
			'NG' => 'NGN',
			'NI' => 'NIO',
			'NL' => 'EUR',
			'NO' => 'NOK',
			'NP' => 'NPR',
			'NR' => 'AUD',
			'NU' => 'NZD',
			'NZ' => 'NZD',
			'OM' => 'OMR',
			'PA' => 'PAB',
			'PE' => 'PEN',
			'PF' => 'XPF',
			'PG' => 'PGK',
			'PH' => 'PHP',
			'PK' => 'PKR',
			'PL' => 'PLN',
			'PM' => 'EUR',
			'PN' => 'NZD',
			'PR' => 'USD',
			'PS' => 'ILS',
			'PT' => 'EUR',
			'PW' => 'USD',
			'PY' => 'PYG',
			'QA' => 'QAR',
			'RE' => 'EUR',
			'RO' => 'RON',
			'RS' => 'RSD',
			'RU' => 'RUB',
			'RW' => 'RWF',
			'SA' => 'SAR',
			'SB' => 'SDB',
			'SC' => 'SCR',
			'SD' => 'SDG',
			'SE' => 'SEK',
			'SG' => 'SGD',
			'SH' => 'SHP',
			'SI' => 'EUR',
			'SJ' => 'NOK',
			'SK' => 'EUR',
			'SL' => 'SLL',
			'SM' => 'EUR',
			'SN' => 'XOF',
			'SO' => 'SOS',
			'SR' => 'SRD',
			'SS' => 'SSP',
			'ST' => 'STD',
			'SV' => 'SVC',
			'SX' => 'ANG',
			'SY' => 'SYP',
			'SZ' => 'SZL',
			'TC' => 'USD',
			'TD' => 'XAF',
			'TF' => 'EUR',
			'TG' => 'XOF',
			'TH' => 'THB',
			'TJ' => 'TJS',
			'TK' => 'NZD',
			'TL' => 'USD',
			'TM' => 'TMT',
			'TN' => 'TND',
			'TO' => 'TOP',
			'TR' => 'TRY',
			'TT' => 'TTD',
			'TV' => 'AUD',
			'TW' => 'TWD',
			'TZ' => 'TZS',
			'UA' => 'UAH',
			'UG' => 'UGX',
			'UM' => 'USD',
			'US' => 'USD',
			'UY' => 'UYI',
			'UZ' => 'UZS',
			'VA' => 'EUR',
			'VC' => 'XCD',
			'VE' => 'VEF',
			'VG' => 'USD',
			'VI' => 'USD',
			'VN' => 'VND',
			'VU' => 'VUV',
			'WF' => 'XPF',
			'WS' => 'WST',
			'YE' => 'YER',
			'YT' => 'EUR',
			'ZA' => 'ZAR',
			'ZM' => 'ZMK',
			'ZW' => 'USD',
		);
	
	// Default plugin data:
	const PREFIX = 'wcumcs_';
	const NAME = 'WooCommerce Ultimate Multi Currency Suite';
	const MINIMUM_PHP = '5.4'; // minimum PHP version
	const MINIMUM_WP = '4.0'; // minimum WordPress version
	
	// Default plugin settings:
	const EXCHANGE_API = 'yahoo';
	const EXCHANGE_RATE = 1;
	
	// Variables:
	private $plugin_dir_url; // we're going to assign plugin dir url to this property
	private $plugin_path; // we're going to assign plugin path to this property
	private $conversion_method = ''; // added to settings properties, so it can be accessed by other plugins (by get_conversion_method() method)
	private $predefined_prices = '';
	public $session_currency;
	public $session_currency_data;
	
	
	/**
	 * Initiate object: set object properties
	 */
	public function __construct($plugin_path){
		
		// assign plugin dir url to class property, so we can easily access it from other methods:
		$this->plugin_path = $plugin_path;
		$this->plugin_dir_url = plugin_dir_url($this->plugin_path);
		
	}
	
	
	/**
	 * Return plugin name
	 */
	public function get_plugin_name(){
		
		$plugin_name = self::NAME;
		
		return $plugin_name;
		
	}
	
	
	/**
	 * Return the array containing currency switcher theme data or just a string with specific field if parameter is supplied
	 */
	public function get_switcher_data($option = null){
		
		if (empty($option)){ // return everything in an array
			$switcher_data = array(			
				'text' => $this->get_option('currency_switcher_text'),
				'theme' => $this->get_option('currency_switcher_theme'),
				'css' => $this->get_option('currency_switcher_css'),
				'js' => $this->get_option('currency_switcher_js')			
			);		
		} else {
			$switcher_data = $this->get_option($option);
		}
		
		return $switcher_data;
		
	}
	
	
	/**
	 * Return name of specified currency (for example: Euros); If no parameters, returns default currency name
	 */
	public function get_currency_name($currency_code = null){
		
		if ($currency_code === null) $currency_code = get_woocommerce_currency(); // if function parameter is empty, use default (base) currency
		
		$currencies_list = get_woocommerce_currencies(); // get currencies list

		$currency_name = $currencies_list[$currency_code]; // get name of specified currency
		
		return $currency_name;
		
	}
	
	
	/**
	 * Return currency code for provided country code
	 */
	public function get_country_currency($country_code){
		
		$json_data = $this->get_option('country_list_data');
		$countries_array = json_decode($json_data, true);
		if (empty($countries_array[$country_code])){
			return false;
		} else {
			$currency = $countries_array[$country_code];			
			return $currency;			
		}
		
	}
	
	
	/**
	 * Return boolean whether page caching support is enabled
	 */
	public function page_cache_enabled(){
		
		$page_cache = $this->get_option('page_cache_support');
		if (!empty($page_cache) && $page_cache == 'yes'){
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Return hash of selected currency data
	 */
	public function get_currency_hash(){
		
		$selected_currency_data = array( // this is used to create hash for currency
			$this->session_currency, // first item is currency code
			$this->session_currency_data['decimal_separator'],
			$this->session_currency_data['name'],
			$this->session_currency_data['number_decimals'],
			$this->session_currency_data['position'],
			$this->session_currency_data['symbol'],
			$this->session_currency_data['thousand_separator']
		);	
		
		$selected_currency_data_serialized = json_encode($selected_currency_data); // encoding array to json, so we can easily create hash
		$selected_currency_data_hash = md5($selected_currency_data_serialized);
		$selected_currency_data_hash = substr($selected_currency_data_hash, 0, 12); // limit hash length to 12 chars
		
		return $selected_currency_data_hash;
		
	}
	
	
	/**
	 * Return boolean whether user should be geolocated with IP
	 */
	public function ip_geolocation_enabled(){
		
		$geolocation = $this->get_option('geolocation');
		if (!empty($geolocation) && $geolocation == 'yes'){
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Return boolean whether user choice should be remembered with cookie or not
	 */
	public function remember_user_currency(){
		
		$remember_currency = $this->get_option('remember_user_chosen_currency');
		if (!empty($remember_currency) && $remember_currency == 'yes'){
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Return boolean whether products and variations should use predefined prices
	 */
	public function predefined_prices(){

		if (empty($this->predefined_prices)){
			$this->predefined_prices = $this->get_option('predefined_prices');
		}
		
		if (!empty($this->predefined_prices) && $this->predefined_prices == 'yes'){
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Return boolean whether in reference mode checkout total should be displayed in stores base curency
	 */
	public function total_in_base_currency(){
		
		$total_in_base_currency = $this->get_option('checkout_total_in_base_currency');
		if (!empty($total_in_base_currency) && $total_in_base_currency == 'yes'){
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Return string with conversion method
	 */
	public function get_conversion_method(){
		
		$conversion_method = $this->get_option('conversion_method');
		
		$this->conversion_method = $conversion_method;
		
		return $conversion_method;
		
	}
	
	
	/**
	 * Return plugin DB options prefix
	 */
	public function get_prefix(){
		
		return self::PREFIX;
		
	}
	
	
	/**
	 * Return plugin dir url
	 */
	public function get_plugin_dir_url(){
		
		return $this->plugin_dir_url;
		
	}
	
	
	/**
	 * Return plugin path
	 */
	public function get_plugin_path(){
		
		return $this->plugin_path;
		
	}
	
	
	/**
	 * Return default exchange rate
	 */
	public function get_exchange_rate(){
		
		return self::EXCHANGE_RATE;
		
	}
	
	
	/**
	 * Return default exchange API
	 */
	public function get_exchange_api(){
		
		return self::EXCHANGE_API;
		
	}
	
	
	/**
	 * Returns array with all information on specified currency or array of every currency (if no parameter specified)
	 */
	public function get_currency_data($currency_code = ''){
		
		$available_currencies_json = $this->get_option('available_currencies');
		$available_currencies = json_decode($available_currencies_json, true);
		$requested_currencies_data = array();
		if (empty($available_currencies)){
			return false;
		}
		
		if (empty($currency_code)){ // currency code not specified - we need to check all currencies
			foreach ($available_currencies as $code => $currency_data){
				$requested_currencies_data[$code] = array();
				$requested_currencies_data[$code] = $currency_data;
				$exchange_rate = $this->get_option('exchange_rate_' . $code);
				$exchange_api = $this->get_option('exchange_api_' . $code);
				$requested_currencies_data[$code]['rate'] = $exchange_rate;
				$requested_currencies_data[$code]['api'] = $exchange_api;
			}
		} else { // just one currency specified - check only this one
			if (empty($available_currencies[$currency_code])){ // currency is not used
				return false;
			} else {
				$requested_currencies_data = $available_currencies[$currency_code];			
				$exchange_rate = $this->get_option('exchange_rate_' . $currency_code);
				$exchange_api = $this->get_option('exchange_api_' . $currency_code);
				$requested_currencies_data['rate'] = $exchange_rate;
				$requested_currencies_data['api'] = $exchange_api;			
			}
			
		}
		
		return $requested_currencies_data;
		
	}
	
	
	/**
	 * Return argument's minimum required version
	 */
	public function get_minimum_required($parameter = 'wp'){ // We're passing what we need to check (WordPress by default)
		
		if ($parameter == 'wp'){
			return self::MINIMUM_WP;
		} else if ($parameter == 'php'){
			return self::MINIMUM_PHP;
		}
		
	}
	
	
	/**
	 * Adds cron job for updating currency rates
	 */
	public function add_cron_job(){
		
		if (!wp_next_scheduled('wcumcs_cron_currency_exchange_rate_update_event')){ // add only if not scheduled before
			wp_schedule_event(current_time('timestamp'), 'daily', 'wcumcs_cron_currency_exchange_rate_update_event');		
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Removes scheduled cron job
	 */
	public function remove_cron_job(){
		
		wp_clear_scheduled_hook('wcumcs_cron_currency_exchange_rate_update_event');
		return true;
		
	}
	
	
	/**
	 * Returns boolean whether activation code is correct
	 */
	public function verify_activation_code($code){
		
		$item = 'WooCommerce Ultimate Multi Currency Suite';		
		$code = trim($code); // strip whitespace from beginning and end
		$url = "http://api.dev49.net/edge/envato_verify/$code/";
		
		$params = array(
			'body' => array(
				'item_name' => $item
			)
		);
		
		$request = wp_remote_post($url, $params);
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
			$response = json_decode($request['body'], true); // convert to PHP array	
		} else {	
			return false;
		}
		
		if (empty($response) || empty($response['result'])){
			return false; // license not valid
		} else if ($response['result'] == '1'){
			return true; // license valid
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Removes activation data from DB
	 */
	public function deactivate_plugin(){
		
		$prefix = self::PREFIX;
		update_option($prefix . 'plugin_activation_code', '');
		update_option($prefix . 'plugin_activated', 'no');
		return true;
		
	}
	
	
	/**
	 * Activates plugin with provided code
	 */
	public function activate_plugin($code){
		
		$prefix = self::PREFIX;
		update_option($prefix . 'plugin_activation_code', $code);
		update_option($prefix . 'plugin_activated', 'yes');
		return true;
		
	}
	
	
	/**
	 * Return boolean whether plugin has been marked as activated
	 */
	public function is_plugin_activated(){
		
		$prefix = self::PREFIX; // easier to write $prefix :-)
		$plugin_activated = get_option($prefix . 'plugin_activated');
		$plugin_activation_code = get_option($prefix . 'plugin_activation_code');
		
		if (!empty($plugin_activation_code) && !empty($plugin_activated) && $plugin_activated == 'yes'){
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 * Return string with activation code
	 */
	public function get_activation_code(){
		
		$plugin_activation_code = $this->get_option('plugin_activation_code');
		
		return $plugin_activation_code;
		
	}
	
	
	/**
	 * Return boolean whether plugin data is in DB
	 */
	public function is_plugin_in_db(){
		
		$prefix = self::PREFIX; // easier to write $prefix :-)
		if (!get_option($prefix . 'conversion_method') || !get_option($prefix . 'available_currencies')){ // if key option(s) missing
			return false; // plugin not in DB
		} else {
			return true; // otherwise, plugin in DB
		}
		
	}
	
	
	/**
	 * Return currency exchange update method
	 */
	public function get_exchange_rates_update_method(){
		
		$value = get_option(self::PREFIX . 'exchange_rates_update_method');
		return $value;
		
	}
	
	
	/**
	 * Return a value for specified option
	 */
	public function get_option($option_name = ''){
		
		$value = get_option(self::PREFIX . $option_name);
		return $value;
		
	}
	
	
	/**
	 * Save value to DB
	 */
	public function update_option($option_name = '', $option_value = ''){
		
		update_option(self::PREFIX . $option_name, $option_value);
		return true;
		
	}
	
	
	/**
	 * Restore default plugin settings (or initialize plugin options after first activation)
	 */
	public function restore_defaults(){
		
		$this->remove_cron_job(); // remove plugin cron job
		
		$options_to_remove = array(); // these options will be deleted
		$all_available_currencies = $this->get_currency_data();

		// remove exchange apis and rates:
		if (!empty($all_available_currencies)){
			foreach ($all_available_currencies as $currency_code => $currency_data){
				$options_to_remove[] = self::PREFIX . 'exchange_api_' . $currency_code; 
				$options_to_remove[] = self::PREFIX . 'exchange_rate_' . $currency_code; 
			}
		}
		
		// remove other plugin options here:
		$options_to_remove[] = self::PREFIX . 'woocommerce_base_currency';
		$options_to_remove[] = self::PREFIX . 'remember_user_chosen_currency';
		$options_to_remove[] = self::PREFIX . 'predefined_prices';
		$options_to_remove[] = self::PREFIX . 'geolocation';
		$options_to_remove[] = self::PREFIX . 'exchange_rates_update_method';
		$options_to_remove[] = self::PREFIX . 'email_address';
		$options_to_remove[] = self::PREFIX . 'country_list_data';
		$options_to_remove[] = self::PREFIX . 'conversion_method';
		$options_to_remove[] = self::PREFIX . 'plugin_activation_code';
		$options_to_remove[] = self::PREFIX . 'plugin_activated';
		$options_to_remove[] = self::PREFIX . 'checkout_total_in_base_currency';
		$options_to_remove[] = self::PREFIX . 'checkout_total_payment_text';
		$options_to_remove[] = self::PREFIX . 'currency_switcher_text';
		$options_to_remove[] = self::PREFIX . 'currency_switcher_theme';
		$options_to_remove[] = self::PREFIX . 'currency_switcher_css';
		$options_to_remove[] = self::PREFIX . 'currency_switcher_js';
		$options_to_remove[] = self::PREFIX . 'currency_switcher_display_template';		
		$options_to_remove[] = self::PREFIX . 'inner_use_currencies_in_use';
		$options_to_remove[] = self::PREFIX . 'available_currencies';
		$options_to_remove[] = self::PREFIX . 'page_cache_support';
		
		// When we have all option names in a fancy array, we can start removing each one of them:
		foreach ($options_to_remove as $option_to_remove_name){			
			if (is_multisite()){
				delete_site_option($option_to_remove_name); // delete multisite options
				delete_option($option_to_remove_name); // delete standard WP options
			} else {
				delete_option($option_to_remove_name); // delete standard WP options
			}
		}		
		
		// recreate new options based on default settings here:
		update_option(self::PREFIX . 'woocommerce_base_currency', get_option('woocommerce_currency'));
		update_option(self::PREFIX . 'remember_user_chosen_currency', 'no');
		update_option(self::PREFIX . 'predefined_prices', 'yes');		
		update_option(self::PREFIX . 'geolocation', 'yes');
		update_option(self::PREFIX . 'exchange_rates_update_method', 'automatic');
		update_option(self::PREFIX . 'email_address', get_option('admin_email'));
		update_option(self::PREFIX . 'country_list_data', json_encode($this->countries_currencies));
		update_option(self::PREFIX . 'conversion_method', 'checkout');
		update_option(self::PREFIX . 'plugin_activation_code', '');
		update_option(self::PREFIX . 'plugin_activated', 'no');
		update_option(self::PREFIX . 'checkout_total_in_base_currency', 'yes');
		update_option(self::PREFIX . 'checkout_total_payment_text', __('Total payment:', 'woocommerce-ultimate-multi-currency-suite'));
		update_option(self::PREFIX . 'currency_switcher_text', __('Choose your preferred currency', 'woocommerce-ultimate-multi-currency-suite'));
		update_option(self::PREFIX . 'currency_switcher_theme', 'dropdown');
		update_option(self::PREFIX . 'currency_switcher_css', '');
		update_option(self::PREFIX . 'currency_switcher_js', '');
		update_option(self::PREFIX . 'currency_switcher_display_template', '');	
		update_option(self::PREFIX . 'page_cache_support', 'no');
				
		$this->add_cron_job(); // we add cron job because by default it is switched on
		
		// create currencies list (only default one)
		$default_currency = get_option('woocommerce_currency');
		$available_currencies = array();
		$available_currencies[$default_currency] = array();
		$available_currencies[$default_currency]['order'] = 1;
		$available_currencies[$default_currency]['name'] = $this->get_currency_name($default_currency);
		$available_currencies[$default_currency]['symbol'] = get_woocommerce_currency_symbol($default_currency);
		$available_currencies[$default_currency]['position'] = get_option('woocommerce_currency_pos');
		$available_currencies[$default_currency]['thousand_separator'] = stripslashes(get_option('woocommerce_price_thousand_sep'));
		$available_currencies[$default_currency]['decimal_separator'] = stripslashes(get_option('woocommerce_price_decimal_sep')) ? stripslashes(get_option('woocommerce_price_decimal_sep')) : '.';
		$available_currencies[$default_currency]['number_decimals'] = absint(get_option('woocommerce_price_num_decimals', 2));
		
		$available_currencies_json = json_encode($available_currencies, JSON_UNESCAPED_UNICODE); // convert array to json
		
		// update available currencies:
		update_option(self::PREFIX . 'available_currencies', $available_currencies_json);		
		
		// set default currency as main one in available currencies and set 1:1 rate and default api:
		update_option(self::PREFIX . 'exchange_api_' . $default_currency, self::EXCHANGE_API);
		update_option(self::PREFIX . 'exchange_rate_' . $default_currency, self::EXCHANGE_RATE);
		
		return true; // operation complete
		
	}
	
	
	/**
	 * Update exchange rates of one or more currencies, return new rates and possible problems encountered
	 */
	public function update_exchange_rates($currencies, $cron_execution = false){
		
		if (empty($currencies)){ // no data passed for some reason, so exit
			exit;			
		} 
		
		$update_results = array();		
		
		$default_currency = get_option('woocommerce_currency'); // WooCommerce base currency
		
		// Send every currency to update via API:
		foreach ($currencies as $currency_to_update => $currency_data){
		
			$api = $currency_data['api'];
			$update_results[$currency_to_update] = $this->get_currency_exchange_rate($default_currency, $currency_to_update, $api); 			
			
		}
			
		// Now we can update DB with new rates:
		$failed_currencies = array(); // array containing currencies code which were not updated
		foreach ($update_results as $currency_code => $currency_data){				
			// First we need to check if there were problems; if there were - dont update
			if ($currency_data['error'] != 0){
				$failed_currencies[] = $currency_code; // add currency code to failed_currencies array
			} else {
				$option_name = self::PREFIX . 'exchange_rate_' . $currency_code; // option to update
				$option_value = $currency_data['value']; // new exchange rate
				if (!empty($option_value)){ // update only when value exist (just in case...)
					$option_value = floatval($option_value); // make sure it's float
					update_option($option_name, $option_value);
				}
			}				
		}	
		
		if ($cron_execution == true){
			
			// perform email notification if cron exchange rates update failed:
			$notification_email = get_option(self::PREFIX . 'email_address'); // to email specified in wcumcs settings
			if (!empty($failed_currencies) && !empty($notification_email)){ // if some currencies have failed AND user specified an email address in settings
				$from_email = get_option('admin_email'); // from blog admin
				$to_email = $notification_email;
				$subject = __('Currency exchange rate update failed', 'woocommerce-ultimate-multi-currency-suite');
				$content = __('Error occured while updating your currencies exchange rates. The following currencies were not updated:', 'woocommerce-ultimate-multi-currency-suite');
				foreach ($failed_currencies as $failed_currency){
					$content .= ' ' . $failed_currency . ',';
				}
				$content = rtrim($content, ','); // remove trailing colon
				$content .= __('. Please go to WooCommerce Ultimate Multi Currency Suite settings and try chaging the API for listed currencies.', 'woocommerce-ultimate-multi-currency-suite');
				$headers = 'From: [WordPress] Multi Currency Suite <' . $from_email . '>' . "\r\n";
				wp_mail($to_email, $subject, $content, $headers);			
			}
		
		}
		
		return $update_results; // for example: $update_results['EUR']['value'] = 4; $update_results['EUR']['error'] = 0; (0 = no error)
		
	}
	
	
	/**
	 * Execute correct action based on specified API
	 */
	private function get_currency_exchange_rate($from, $to, $api){
		
		$return_data = array();
		$return_data['value'] = 1;
		$return_data['error'] = 1;
		
		switch ($api) {
		
			case 'yahoo':
				
				$url = "http://download.finance.yahoo.com/d/quotes.csv?s={$from}{$to}=X&f=l1&e=.csv"; // API URL
				$rate = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is the exchange rate
				
				if (empty($rate)){ // just in case the result is empty, try the same thing one more time
					$rate = wp_remote_retrieve_body(wp_remote_get($url));
				}
				
				if (empty($rate)){ // and for the 3rd time just to be perfectly sure
					$rate = wp_remote_retrieve_body(wp_remote_get($url));
				}
				
				if ($rate) { // we managed to get rate successfully; assign it to array:			
					$return_data['value'] = $rate;
					$return_data['error'] = 0;
				} else { // no luck getting rate:
					$return_data['error'] = 1;
				}	
				
				break;
			
			case 'ecb':
				
				$url = "http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml"; // API URL
				$currency_data_xml = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is the XML data
				
				if (empty($currency_data_xml)){ // just in case the result is empty, try the same thing one more time
					$currency_data_xml = wp_remote_retrieve_body(wp_remote_get($url));
				}
				
				if (empty($currency_data_xml)){ // and for the 3rd time just to be perfectly sure
					$currency_data_xml = wp_remote_retrieve_body(wp_remote_get($url));
				}

				$currency_data = simplexml_load_string($currency_data_xml);
				$currencies = array(); // array of currencies and their rates downloaded from ECB
				
				if (empty($currency_data->Cube->Cube)){
					$return_data['error'] = 1;
					break;
				}
				
				foreach ($currency_data->Cube->Cube->Cube as $rate){ // Yep, that's how ECB XML tree looks like...
					$currency_code = (string)$rate['currency']; // make sure the types are correct - string...
					$currency_rate = (float)$rate['rate']; // ...and float
					$currencies[$currency_code] = $currency_rate; 
				} 
				
				// ECB rates are in relation to EUR, so we need to make sure both of our currencies
				// ($from and $to) exist in ECB response; otherwise, we will not be able to make a conversion.
				// EUR rate is not in the response, so we'll going to add it first. EUR to EUR rate is ofc equal to 1:
				$currencies['EUR'] = 1;
				if (!array_key_exists($from, $currencies) || !array_key_exists($to, $currencies)) { // if one or both currencies are not in response...
					$return_data['error'] = 1; // give error - we cannot get exchange rate
				} else { // both currencies are in response
					$base_in_eur = 1 / $currencies[$from]; // convert base currency ($from) to EUR
					$rate_pre_round = $base_in_eur * $currencies[$to]; // convert base (in EUR) to target currency ($to)	
					$rate = round($rate_pre_round, 4); // round rate to 4 decimal digits 
					$return_data['value'] = $rate;
					$return_data['error'] = 0;						
				}
				
				break;			
			
			case 'fixer':
				
				$url = "http://api.fixer.io/latest?base={$from}&symbols={$to}"; // API URL
				$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is JSON data
				
				if (empty($currency_data_json)){ // just in case the result is empty, try the same thing one more time
					$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url));
				}
				
				if (empty($currency_data_json)){ // and for the 3rd time just to be perfectly sure
					$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url));
				}

				$currency_data = json_decode($currency_data_json, true);
				
				if (!empty($currency_data['rates'][$to])){
					$rate = $currency_data['rates'][$to];
					$return_data['value'] = $rate;
					$return_data['error'] = 0;
				} else {
					$return_data['error'] = 1;
				}	
				
				break;
				
			case 'google':
				
				$url = "https://www.google.com/finance/converter?a=1&from={$from}&to={$to}"; // URL
				$currency_data_html = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is the XML data
				
				if (empty($currency_data_html)){ // just in case the result is empty, try the same thing one more time
					$currency_data_html = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is the XML data
				}
				
				if (empty($currency_data_html)){ // and for the 3rd time just to be perfectly sure
					$currency_data_html = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is the XML data
				}

				$regex_rule = '#\<span class=bld\>(.+?)\<\/span\>#s'; // Google Finance Converter only returns full HTML page. Regex works best here.
				preg_match($regex_rule, $currency_data_html, $regex_match);
				
				if (!empty($regex_match) && !empty($regex_match[0])){
					$rate = $regex_match[0];
					$rate = substr($rate, 0, strrpos($rate, ' ') - 1); // we have a whitespace and a currency code and </span> at the end, so we need to get rid of them
					$rate = strrchr($rate, '>'); // remove <span class=...> from the beginning - find the last occurence of '>' and strip everything until that
					$rate = ltrim($rate, '>'); // and finally remove the > char from the beginning
					if (!empty($rate)){
						$return_data['value'] = $rate;
						$return_data['error'] = 0;
					} else {
						$return_data['error'] = 1;	
					}
				} else {
					$return_data['error'] = 1;
				}				
				
				break;
				
			case 'techunits':
				
				$url = "https://rate-exchange.herokuapp.com/fetchRate?from={$from}&to={$to}"; // API URL
				$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is JSON data
				
				if (empty($currency_data_json)){ // just in case the result is empty, try the same thing one more time
					$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url));
				}
				
				if (empty($currency_data_json)){ // and for the 3rd time just to be perfectly sure
					$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url));
				}

				$currency_data = json_decode($currency_data_json, true);
				
				if (!empty($currency_data['Rate'])){
					$rate = $currency_data['Rate'];
					$return_data['value'] = $rate;
					$return_data['error'] = 0;
				} else {
					$return_data['error'] = 1;
				}	
				
				break;
			
			case 'fcca':
				
				$url = "http://free.currencyconverterapi.com/api/v3/convert?q={$from}_{$to}&compact=y"; // API URL
				$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url)); // Use WP HTTP API to send GET request and then retrieve the body of the response, which is JSON data
				
				if (empty($currency_data_json)){ // just in case the result is empty, try the same thing one more time
					$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url));
				}

				if (empty($currency_data_json)){ // and for the 3rd time just to be perfectly sure
					$currency_data_json = wp_remote_retrieve_body(wp_remote_get($url)); // and for the 3rd time just to be perfectly sure
				}
				
				$currency_data = json_decode($currency_data_json, true);
				
				if (!empty($currency_data["{$from}_{$to}"]['val'])){
					$rate = $currency_data["{$from}_{$to}"]['val'];
					$return_data['value'] = $rate;
					$return_data['error'] = 0;
				} else {
					$return_data['error'] = 1;
				}	
				
				break;
			
			default:
				
				break;		
			
		}
		
		if ($to == get_option('woocommerce_currency')){ // if we were converting base currency
			$return_data['value'] = 1;
			$return_data['error'] = 0;
		}
		
		$return_data['value'] = trim($return_data['value']); // Yahoo API returns exchange rate with a newline char at the end, so we need to make sure to trim all of that
		
		$return_data['value'] = floatval($return_data['value']); // cast to float to make sure the trailing decimal zeros are trimmed
		
		if (!is_numeric($return_data['value']) || $return_data['value'] <= 0){ // if value is not int or float (in unusual situation where API responds with an error page, instead of numeric exchange rate)
			$return_data['value'] = 1;											// or value is lower or equal to 0
			$return_data['error'] = 1;
		}
		
		return $return_data;	
		
	}
	
	
}
