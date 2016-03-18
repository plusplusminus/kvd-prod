<?php


/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 * WooCommerce_Ultimate_Multi_Currency_Suite_Widget class is a currency switcher widget class.
 *
 */

// Exit if accessed directly:
if (!defined('ABSPATH')){ 
    exit; 
}

class WooCommerce_Ultimate_Multi_Currency_Suite_Widget extends WP_Widget {
	
	
	private $settings; // we're going to assign Settings object to this property
	

	/**
	 * Register widget with WordPress
	 */
	function __construct() {
		
		global $woocommerce_ultimate_multi_currency_suite;
		$this->settings = $woocommerce_ultimate_multi_currency_suite->settings;
		
		parent::__construct(
			'wcumcs_widget', // Base ID
			__('Currency Switcher', 'woocommerce-ultimate-multi-currency-suite'), // Name
			array( // Args
				'description' => __('WooCommerce Ultimate Multi Currency Suite widget', 'woocommerce-ultimate-multi-currency-suite'),
			) 
		);
		
	}
	

	/**
	 * Front-end display of widget
	 */
	public function widget($args, $instance){
		
		echo $args['before_widget'];
		
		if (!empty($instance['title'])){
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}
		
		wcumcs_switcher();
		
		echo $args['after_widget'];
		
	}

	
	/**
	 * Back-end widget form
	 */
	public function form($instance){
		
		$title =  __('Change currency', 'woocommerce-ultimate-multi-currency-suite');
		if (isset($instance['title'])){			
			$title = $instance['title'];
		}		
		
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'woocommerce-ultimate-multi-currency-suite'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
			</p>
			<p>
				<?php 
					echo sprintf(
						__(
							'Currency switcher will appear in this widget. To change the way switcher is displayed, please go to WooCommerce Ultimate Multi Currency Suite <a href="%s">Currency switcher display settings</a>.', 
							'woocommerce-ultimate-multi-currency-suite'
						), 
						admin_url(
							'admin.php?page=wc-settings&tab=multi_currency_suite&section=display'
						)
					); 
				?>
			</p>
		<?php 
		
	}

	
	/**
	 * Sanitize widget form values as they are saved
	 */
	public function update($new_instance, $old_instance){
		
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

		return $instance;
		
	}
	

}
