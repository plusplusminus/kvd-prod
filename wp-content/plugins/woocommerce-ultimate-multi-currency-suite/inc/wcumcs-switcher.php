<?php

/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * This file contains code responsible for displaying currency switcher
 *
 */

// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}

// Display currency switcher:
function wcumcs_switcher($shortcode_atts = null, $shortcode_content = null){ // parameters only for shortcode
	
	if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){ // if woocommerce not running
		return; // exit
	}
		
	global $woocommerce_ultimate_multi_currency_suite;
	$default_currency = get_woocommerce_currency();
	
	if (empty($woocommerce_ultimate_multi_currency_suite->settings->session_currency)){ // if no currency stored in session
		$currency = $default_currency;
	} else {
		$currency = $woocommerce_ultimate_multi_currency_suite->settings->session_currency;
	}
	
	$currency_data = $woocommerce_ultimate_multi_currency_suite->settings->get_currency_data(); // get all data on all currencies
	$currency_switcher_text = $woocommerce_ultimate_multi_currency_suite->settings->get_switcher_data('currency_switcher_text'); // get text above currency switcher
	if (function_exists('icl_register_string')){ // register for WPML
		icl_register_string('woocommerce-ultimate-multi-currency-suite', 'Currency switcher text', $currency_switcher_text);
	}
	$currency_switcher_theme = $woocommerce_ultimate_multi_currency_suite->settings->get_switcher_data('currency_switcher_theme'); // get switcher theme
	$currency_switcher_display_template = $woocommerce_ultimate_multi_currency_suite->settings->get_option('currency_switcher_display_template'); // get currency template
	
	$available_currencies = array(); // list of all available currencies
	foreach ($currency_data as $currency_code => $data){ // add all currency codes to this array
		$available_currencies[] = $currency_code;
	}
	
	ob_start(); // buffer the HTML code (later it's being outputted by echoing it or just returning it, depending on what called the function)	
	?>
	
	<?php if ($currency_switcher_theme == 'dropdown'): ?>
		
		<div class="wcumcs-switcher-dropdown wcumcs-container">
			<p class="wcumcs-text"><?php echo $currency_switcher_text; ?></p> 
			<select class="wcumcs-select" name="wcumcs_change_currency_code">
				<?php 
					foreach ($available_currencies as $currency_code):
						$currency_option = '<option class="wcumcs-option" value="' . $currency_code . '" ';
						if ($currency == $currency_code){
								$currency_option .='selected'; // if this is active currency, select it in dropdown list
						}
						$currency_option .= '>';
						if (empty($currency_switcher_display_template)){ // if user didnt specify a template...
							$currency_option .= $currency_data[$currency_code]['name'] . ' (' . $currency_code . ')'; // ...use default one					
						} else { // otherwise, use the one specified by the user
							$custom_currency_display = $currency_switcher_display_template;
							$custom_currency_display = str_replace('%code%', $currency_code, $custom_currency_display);
							$custom_currency_display = str_replace('%name%', $currency_data[$currency_code]['name'], $custom_currency_display);
							$custom_currency_display = str_replace('%symbol%', $currency_data[$currency_code]['symbol'], $custom_currency_display);
							$currency_option .= $custom_currency_display;
						}
						$currency_option .= '</option>';
						echo $currency_option;	
					endforeach; 
				?>
			</select>
		</div>
		
	<?php elseif ($currency_switcher_theme == 'buttons'): ?>
	
		<div class="wcumcs-switcher-buttons wcumcs-container">
			<p class="wcumcs-text"><?php echo $currency_switcher_text; ?></p> 
			<ul class="wcumcs-list">
				<?php 
					foreach ($available_currencies as $currency_code):
						$currency_option = '<li class="wcumcs-list-item">';
						$currency_option .= '<a data-wcumcs-currency="' . $currency_code . '" class="wcumcs-list-item-link ';
						if ($currency == $currency_code){ // if this is active currency, add selected class to it
							$currency_option .= 'selected';
						}
						$currency_option .= '" href="" onclick="return false;">';
						if (empty($currency_switcher_display_template)){ // if user didnt specify a template...
							$currency_option .= $currency_code; // ...use default one	
						} else { // otherwise, use the one specified by the user
							$custom_currency_display = $currency_switcher_display_template;
							$custom_currency_display = str_replace('%code%', $currency_code, $custom_currency_display);
							$custom_currency_display = str_replace('%name%', $currency_data[$currency_code]['name'], $custom_currency_display);
							$custom_currency_display = str_replace('%symbol%', $currency_data[$currency_code]['symbol'], $custom_currency_display);
							$currency_option .= $custom_currency_display;
						}
						$currency_option .= '</a></li>';
						echo $currency_option;	
					endforeach;
				?>
			</ul>
		</div>
					
	<?php endif; 
	
	$html_output = ob_get_clean();
	
	$currency_switcher_html = apply_filters(
		'wcumcs_currency_switcher_html', 
		$html_output, 
		$currency, 
		$default_currency, 
		$available_currencies, 
		$currency_data, 
		$currency_switcher_text, 
		$currency_switcher_theme
	);
	
	if ($shortcode_atts === null && $shortcode_content === null){ // if function WAS NOT called by a shortcode (arguments not set)...
		echo $currency_switcher_html; // ... just echo the switcher out
		return true; // and end the function
	} else { // if function was called by shortcode (we know this because some arguments were supplied)...
		return $currency_switcher_html; // ... return the switcher instead of echoing it (shortcodes need that)
	}
	
}
