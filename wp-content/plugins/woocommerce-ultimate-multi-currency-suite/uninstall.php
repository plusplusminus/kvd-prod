<?php


/*
 *
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * PHP file containing uninstall procedure.
 * Plugin prefix - 'wcumcs'
 *
*/


// delete plugin options:


if (!defined('WP_UNINSTALL_PLUGIN')){
	exit;
}


wp_clear_scheduled_hook('wcumcs_cron_currency_exchange_rate_update_event'); // remove cron job

$options_list =  // list all plugin DB options (without prefix)
	array(
		'woocommerce_base_currency',
		'remember_user_chosen_currency',
		'predefined_prices',
		'geolocation',
		'exchange_rates_update_method',
		'email_address',
		'country_list_data',
		'conversion_method',
		'plugin_activation_code',
		'plugin_activated',
		'checkout_total_in_base_currency',
		'checkout_total_payment_text',
		'currency_switcher_text',
		'currency_switcher_theme',
		'currency_switcher_css',
		'currency_switcher_js',
		'currency_switcher_display_template',
		'inner_use_currencies_in_use',
		'available_currencies',
		'page_cache_support'
	);



foreach ($options_list as $option_name){ // go through all plugin options

	$option_name = 'wcumcs_' . $option_name; // add prefix
	
	if (is_multisite()){
		delete_site_option($option_name); // delete multisite options
		delete_option($option_name); // delete standard WP options
	} else {
		delete_option($option_name); // delete standard WP options
	}
	
}
