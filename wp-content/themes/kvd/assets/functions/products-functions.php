<?php
class kvdProducts {

	public function __construct() {
		global $product;

		$this->prefix = '_kvd_'; 
		$this->_cache = array();

		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

		remove_action('woocommerce_before_main_content','woocommerce_breadcrumb',20);
		remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10);


		remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_images',20);
		add_action('woocommerce_after_single_product_summary','woocommerce_show_product_images',5);
		add_action('woocommerce_before_single_product_summary',array($this,'woocommerce_feature_image_mobile'),20);

		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		add_action( 'woocommerce_single_variation', array($this,'kvd_single_variation_add_to_cart_button'), 20 );
		
		
		add_action('woocommerce_before_main_content', array($this,'kvd_wrapper_start'), 10);
		add_action('woocommerce_after_main_content',array($this, 'kvd_wrapper_end'), 10);

		add_filter( 'woocommerce_product_single_add_to_cart_text', array($this,'woo_custom_cart_button_text' ));


		remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs',10);
		remove_action('woocommerce_after_single_product_summary','woocommerce_upsell_display',15);
		remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20);

		add_action('woocommerce_before_variations_form',array($this, 'kvd_quantity'), 10);
		add_action('woocommerce_before_variations_form',array($this, 'kvd_product_info'), 20);

		add_action('woocommerce_after_add_to_cart_button_simple',array($this, 'kvd_product_info'), 20);

		remove_action('woocommerce_single_product_summary','woocommerce_template_single_meta',40);
		add_action('woocommerce_after_add_to_cart_button_simple','woocommerce_template_single_meta', 30);
		add_action('woocommerce_after_variations_form','woocommerce_template_single_meta', 10);
		
		add_action('woocommerce_after_add_to_cart_button_simple',array($this, 'kvd_quantity'), 10);
		

		add_action('woocommerce_after_single_product',array($this,'kvd_personalize_form'), 10);

		remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
		add_action( 'woocommerce_single_product_summary', 'wc_print_notices', 2 );



		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		add_action( 'woocommerce_before_shop_loop', array($this,'kvd_woocommerce_archive_bc'), 20 );

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		//remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		add_action( 'woocommerce_before_main_content', array($this,'kvd_woocommerce_archive_header'), 5 );

		add_filter( 'woocommerce_show_page_title', function() { return false; } );


		add_action( 'woocommerce_cart_calculate_fees',array($this,'kvd_cites_costs'));

		add_action( 'woocommerce_share',array($this,'kvd_sharing'),1);

		add_action('woocommerce_after_single_product','woocommerce_output_related_products',20);



	}

	public function kvd_sharing() {

		global $post;

		$title = get_the_title();
		
		$fib= "";
		$base_tweet = "Check this product from Kat van Duinen";


		echo 	'<a class="share-hover__cta" data-placement="top" data-toggle="popover" data-html="true"><i class="icon icon-icomoon"></i> Share</a>';
		echo 	'<div id="social-content" class="share-hover">
				  	<div class="share-hover__content">
					    <div class="share-hover__icons">
					      <a class="share-hover__link js-popup" href="https://www.facebook.com/dialog/share?app_id='.$fbid.'&amp;display=popup&amp;caption='.$caption.'&amp;href='.$link.'&amp;redirect_uri='.$link.'">
					        <i class="icon icon-facebook"></i>
					      </a>
					      <a class="share-hover__link js-popup" href="https://twitter.com/intent/tweet?text='.$base_tweet.'&amp;url='.$link.'">
					        <i class="icon icon-twitter"></i>
					      </a>

					      <a class="share-hover__link js-popup" href="https://www.pinterest.com/pin/create/button/?description='.$title.'&amp;url='.$link.'&amp;media='.$image.'">
					        <i class="icon icon-pinterest-p"></i>
					      </a>

					      <a class="share-hover__link" href="mailto:?body='.$email_body.'%0D%0A%20'.$link.'&amp;subject='.$title.'">
					        <i class="icon icon-envelope"></i>
					      </a>
					    </div>
					</div>
				</div>';
		}

	public function featured_products_query($atts = array('per_page'=>3,'orderby'=>'name','order'=>'desc')) {

		$wc = WC();

		$meta_query   = $wc->query->get_meta_query();
		
		$meta_query[] = array(
			'key'   => '_featured',
			'value' => 'yes'
		);

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts['per_page'],
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'meta_query'          => $meta_query
		);

		if (empty($this->_cache['featured-products'])) {
			$this->_cache['featured-products'] = new WP_Query( $query_args );
		}

		return $this->_cache['featured-products'];


	}

	public function kvd_wrapper_start() {
	  echo '<div class="container">';
	}
	 
	public function kvd_wrapper_end() {
	  echo '</div>';
	}

	public function woocommerce_feature_image_mobile() {

		get_template_part('woocommerce/single-product/feature-image');
	}

	public function kvd_single_variation_add_to_cart_button() {
		global $product;
		?>
		<div class="variations_button add-product-button cf">
			<button type="submit" class="single_add_to_cart_button gen-btn addtobag"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
			<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->id ); ?>" />
			<input type="hidden" name="product_id" value="<?php echo absint( $product->id ); ?>" />
			<input type="hidden" name="variation_id" class="variation_id" value="" />
		</div>
		<?php
	}

 
	function woo_custom_cart_button_text() {
	 
	        return __( 'Add to Bag', 'woocommerce' );
	 
	}

	public function kvd_quantity() {
		woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) );
	}

	public function kvd_product_info() {

		get_template_part('woocommerce/single-product/product-fit');

		
		
	}

	public function kvd_personalize_form() {
		global $product;

		$personalise = $product->get_attribute( 'pa_personalisable' );
		$override = get_post_meta($product->id,'_product_addons_exclude_global',true);
		
		if ($personalise === "Yes" && !$override)
			echo '<script type="text/javascript">jQuery(document).ready(function() { jQuery( ".single_add_to_cart_button" ).before( "<button class=\'gen-btn personalise\'>Personalise</button>");});</script>';
	}

	public function kvd_woocommerce_archive_bc() {

		get_template_part('woocommerce/loop/bc');

	}


	public function kvd_woocommerce_archive_header() {

		if (is_product_category()) 
			get_template_part('woocommerce/loop/archive-header');

	}

	public function kvd_cites_costs() {

		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		$county = array('ZA');
		$required = false;

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

			$cites = get_post_meta($values['product_id'],'_cites_option',true);

			if ($cites) {
				$required = true;
			}

		}

		if ($required) {	
			$percentage = 0.01;
			if ( !in_array( $woocommerce->customer->get_shipping_country(), $county ) ) :
				$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;
				$woocommerce->cart->add_fee( 'CITES Cost', $surcharge, true, '' );
			endif;

		} else {
			return;
		 
		}
	}

}

class Custom_WC_Widget_Layered_Nav extends WC_Widget_Layered_Nav {

 	public function widget_start( $args, $instance ) {
		//echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	public function widget_end( $args ) {
		//echo $args['after_widget'];
	}
	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $_chosen_attributes;

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$current_term = is_tax() ? get_queried_object()->term_id : '';
		$current_tax  = is_tax() ? get_queried_object()->taxonomy : '';
		$taxonomy     = isset( $instance['attribute'] ) ? wc_attribute_taxonomy_name( $instance['attribute'] ) : $this->settings['attribute']['std'];
		$query_type   = isset( $instance['query_type'] ) ? $instance['query_type'] : $this->settings['query_type']['std'];
		$display_type = isset( $instance['display_type'] ) ? $instance['display_type'] : $this->settings['display_type']['std'];

		if ( ! taxonomy_exists( $taxonomy ) ) {
			return;
		}

		$get_terms_args = array( 'hide_empty' => '1' );

		$orderby = wc_attribute_orderby( $taxonomy );

		switch ( $orderby ) {
			case 'name' :
				$get_terms_args['orderby']    = 'name';
				$get_terms_args['menu_order'] = false;
			break;
			case 'id' :
				$get_terms_args['orderby']    = 'id';
				$get_terms_args['order']      = 'ASC';
				$get_terms_args['menu_order'] = false;
			break;
			case 'menu_order' :
				$get_terms_args['menu_order'] = 'ASC';
			break;
		}

		$terms = get_terms( $taxonomy, $get_terms_args );

		if ( 0 < count( $terms ) ) {

			ob_start();

			$found = false;

			$this->widget_start( $args, $instance );

			// Force found when option is selected - do not force found on taxonomy attributes
			if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) ) {
				$found = true;
			}

			if ( 'dropdown' == $display_type ) {

				// skip when viewing the taxonomy
				if ( $current_tax && $taxonomy == $current_tax ) {

					$found = false;

				} else {

					$taxonomy_filter = str_replace( 'pa_', '', $taxonomy );

					$found = false;
					echo '<div class="select-box '.$taxonomy_filter.'-select">';

					echo '<select class="selectbox dropdown_layered_nav_' . $taxonomy_filter . '">';

					echo '<option value="">' . sprintf( __( 'Any %s', 'woocommerce' ), wc_attribute_label( $taxonomy ) ) . '</option>';

					foreach ( $terms as $term ) {

						// If on a term page, skip that term in widget list
						if ( $term->term_id == $current_term ) {
							continue;
						}

						// Get count based on current view - uses transients
						$transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_taxonomy_id ) );

						if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

							$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

							set_transient( $transient_name, $_products_in_term, DAY_IN_SECONDS * 30 );
						}

						$option_is_set = ( isset( $_chosen_attributes[ $taxonomy ] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) );

						// If this is an AND query, only show options with count > 0
						if ( 'and' == $query_type ) {

							$count = sizeof( array_intersect( $_products_in_term, WC()->query->filtered_product_ids ) );

							if ( 0 < $count ) {
								$found = true;
							}

							if ( 0 == $count && ! $option_is_set ) {
								continue;
							}

						// If this is an OR query, show all options so search can be expanded
						} else {

							$count = sizeof( array_intersect( $_products_in_term, WC()->query->unfiltered_product_ids ) );

							if ( 0 < $count ) {
								$found = true;
							}

						}

						echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( isset( $_GET[ 'filter_' . $taxonomy_filter ] ) ? $_GET[ 'filter_' . $taxonomy_filter ] : '' , $term->term_id, false ) . '>' . esc_html( $term->name ) . '</option>';
					}

					echo '</select>';

					echo '</div>';

					wc_enqueue_js( "
						jQuery( '.dropdown_layered_nav_$taxonomy_filter' ).change( function() {
							var term_id = parseInt( jQuery( this ).val(), 10 );
							location.href = '" . preg_replace( '%\/page\/[0-9]+%', '', str_replace( array( '&amp;', '%2C' ), array( '&', ',' ), esc_js( add_query_arg( 'filtering', '1', remove_query_arg( array( 'page', 'filter_' . $taxonomy_filter ) ) ) ) ) ) . "&filter_$taxonomy_filter=' + ( isNaN( term_id ) ? '' : term_id );
						});
					" );

				}

			} else {

				// List display
				echo '<ul>';

				foreach ( $terms as $term ) {

					// Get count based on current view - uses transients
					$transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_taxonomy_id ) );

					if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

						$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

						set_transient( $transient_name, $_products_in_term );
					}

					$option_is_set = ( isset( $_chosen_attributes[ $taxonomy ] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) );

					// skip the term for the current archive
					if ( $current_term == $term->term_id ) {
						continue;
					}

					// If this is an AND query, only show options with count > 0
					if ( 'and' == $query_type ) {

						$count = sizeof( array_intersect( $_products_in_term, WC()->query->filtered_product_ids ) );

						if ( 0 < $count && $current_term !== $term->term_id ) {
							$found = true;
						}

						if ( 0 == $count && ! $option_is_set ) {
							continue;
						}

					// If this is an OR query, show all options so search can be expanded
					} else {

						$count = sizeof( array_intersect( $_products_in_term, WC()->query->unfiltered_product_ids ) );

						if ( 0 < $count ) {
							$found = true;
						}
					}

					$arg = 'filter_' . sanitize_title( $instance['attribute'] );

					$current_filter = ( isset( $_GET[ $arg ] ) ) ? explode( ',', $_GET[ $arg ] ) : array();

					if ( ! is_array( $current_filter ) ) {
						$current_filter = array();
					}

					$current_filter = array_map( 'esc_attr', $current_filter );

					if ( ! in_array( $term->term_id, $current_filter ) ) {
						$current_filter[] = $term->term_id;
					}

					// Base Link decided by current page
					if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
						$link = home_url();
					} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id('shop') ) ) {
						$link = get_post_type_archive_link( 'product' );
					} else {
						$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
					}

					// All current filters
					if ( $_chosen_attributes ) {
						foreach ( $_chosen_attributes as $name => $data ) {
							if ( $name !== $taxonomy ) {

								// Exclude query arg for current term archive term
								while ( in_array( $current_term, $data['terms'] ) ) {
									$key = array_search( $current_term, $data );
									unset( $data['terms'][$key] );
								}

								// Remove pa_ and sanitize
								$filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

								if ( ! empty( $data['terms'] ) ) {
									$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
								}

								if ( 'or' == $data['query_type'] ) {
									$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
								}
							}
						}
					}

					// Min/Max
					if ( isset( $_GET['min_price'] ) ) {
						$link = add_query_arg( 'min_price', $_GET['min_price'], $link );
					}

					if ( isset( $_GET['max_price'] ) ) {
						$link = add_query_arg( 'max_price', $_GET['max_price'], $link );
					}

					// Orderby
					if ( isset( $_GET['orderby'] ) ) {
						$link = add_query_arg( 'orderby', $_GET['orderby'], $link );
					}

					// Current Filter = this widget
					if ( isset( $_chosen_attributes[ $taxonomy ] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) ) {

						$class = 'class="chosen"';

						// Remove this term is $current_filter has more than 1 term filtered
						if ( sizeof( $current_filter ) > 1 ) {
							$current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
							$link = add_query_arg( $arg, implode( ',', $current_filter_without_this ), $link );
						}

					} else {

						$class = '';
						$link = add_query_arg( $arg, implode( ',', $current_filter ), $link );

					}

					// Search Arg
					if ( get_search_query() ) {
						$link = add_query_arg( 's', get_search_query(), $link );
					}

					// Post Type Arg
					if ( isset( $_GET['post_type'] ) ) {
						$link = add_query_arg( 'post_type', $_GET['post_type'], $link );
					}

					// Query type Arg
					if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[ $taxonomy ]['terms'] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) ) ) {
						$link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
					}

					echo '<li ' . $class . '>';

					echo ( $count > 0 || $option_is_set ) ? '<a href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '">' : '<span>';

					echo $term->name;

					echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';

					echo ' <span class="count">(' . $count . ')</span></li>';

				}

				echo '</ul>';

			} // End display type conditional

			$this->widget_end( $args );

			if ( ! $found ) {
				ob_end_clean();
			} else {
				echo ob_get_clean();
			}
		}
	}
 
}

class Custom_WC_Widget_Product_Categories extends WC_Widget_Product_Categories {
 
  function widget_start( $args, $instance ) {
    echo '<div class="select-box category-select">';
  }

  function widget_end( $args) {
    echo '</div>';
  }
 
}



add_action( 'widgets_init', 'override_woocommerce_widgets', 15 );
 
function override_woocommerce_widgets() {
  // Ensure our parent class exists to avoid fatal error (thanks Wilgert!)
 
  if ( class_exists( 'WC_Widget_Layered_Nav' ) ) {
    unregister_widget( 'WC_Widget_Layered_Nav' );

 
    register_widget( 'Custom_WC_Widget_Layered_Nav' );
  }

  if ( class_exists( 'WC_Widget_Product_Categories' ) ) {
    unregister_widget( 'WC_Widget_Product_Categories' );
 
    register_widget( 'Custom_WC_Widget_Product_Categories' );
  }
 
}

function woo_add_custom_general_fields() {

  global $woocommerce, $post;
  
  echo '<div class="options_group">';
 
  	woocommerce_wp_checkbox( 
		array( 
			'id'            => '_cites_option', 
			'label'         => __('CITES Product', 'woocommerce' ), 
			'description'   => __( 'Select whether a CITES certificate is required for this product', 'woocommerce' ) 
		)
	);
  
  echo '</div>';
	
}

function woo_add_custom_general_fields_save( $post_id ){
	
	// Checkbox
	$woocommerce_checkbox = isset( $_POST['_cites_option'] ) ? true : false;
	update_post_meta( $post_id, '_cites_option', $woocommerce_checkbox );
	
	
}

// Display Fields
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );
// Save Fields
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

function  wc_uk_counties_add_counties( $states ) {
    $states['GB'] = array(
        'AV' => 'Avon',
        'BE' => 'Bedfordshire',
        'BK' => 'Berkshire',
        'BU' => 'Buckinghamshire',
        'CA' => 'Cambridgeshire',
        'CH' => 'Cheshire',
        'CL' => 'Cleveland',
        'CO' => 'Cornwall',
        'CD' => 'County Durham',
        'CU' => 'Cumbria',
        'DE' => 'Derbyshire',
        'DV' => 'Devon',
        'DO' => 'Dorset',
        'ES' => 'East Sussex',
        'EX' => 'Essex',
        'GL' => 'Gloucestershire',
        'HA' => 'Hampshire',
        'HE' => 'Herefordshire',
        'HT' => 'Hertfordshire',
        'IW' => 'Isle of Wight',
        'KE' => 'Kent',
        'LA' => 'Lancashire',
        'LE' => 'Leicestershire',
        'LI' => 'Lincolnshire',
        'LO' => 'London',
        'ME' => 'Merseyside',
        'MI' => 'Middlesex',
        'NO' => 'Norfolk',
        'NH' => 'North Humberside',
        'NY' => 'North Yorkshire',
        'NS' => 'Northamptonshire',
        'NL' => 'Northumberland',
        'NT' => 'Nottinghamshire',
        'OX' => 'Oxfordshire',
        'SH' => 'Shropshire',
        'SO' => 'Somerset',
        'SM' => 'South Humberside',
        'SY' => 'South Yorkshire',
        'SF' => 'Staffordshire',
        'SU' => 'Suffolk',
        'SR' => 'Surrey',
        'TW' => 'Tyne and Wear',
        'WA' => 'Warwickshire',
        'WM' => 'West Midlands',
        'WS' => 'West Sussex',
        'WY' => 'West Yorkshire',
        'WI' => 'Wiltshire',
        'WO' => 'Worcestershire',
        'ABD' => 'Scotland / Aberdeenshire',
        'ANS' => 'Scotland / Angus',
        'ARL' => 'Scotland / Argyle & Bute',
        'AYR' => 'Scotland / Ayrshire',
        'CLK' => 'Scotland / Clackmannanshire',
        'DGY' => 'Scotland / Dumfries & Galloway',
        'DNB' => 'Scotland / Dunbartonshire',
        'DDE' => 'Scotland / Dundee',
        'ELN' => 'Scotland / East Lothian',
        'EDB' => 'Scotland / Edinburgh',
        'FIF' => 'Scotland / Fife',
        'GGW' => 'Scotland / Glasgow',
        'HLD' => 'Scotland / Highland',
        'LKS' => 'Scotland / Lanarkshire',
        'MLN' => 'Scotland / Midlothian',
        'MOR' => 'Scotland / Moray',
        'OKI' => 'Scotland / Orkney',
        'PER' => 'Scotland / Perth and Kinross',
        'RFW' => 'Scotland / Renfrewshire',
        'SB' => 'Scotland / Scottish Borders',
        'SHI' => 'Scotland / Shetland Isles',
        'STI' => 'Scotland / Stirling',
        'WLN' => 'Scotland / West Lothian',
        'WIS' => 'Scotland / Western Isles',
        'AGY' => 'Wales / Anglesey',
        'GNT' => 'Wales / Blaenau Gwent',
        'CP' => 'Wales / Caerphilly',
        'CF' => 'Wales / Cardiff',
        'CAE' => 'Wales / Carmarthenshire',
        'CR' => 'Wales / Ceredigion',
        'CW' => 'Wales / Conwy',
        'DEN' => 'Wales / Denbighshire',
        'FLN' => 'Wales / Flintshire',
        'GLA' => 'Wales / Glamorgan',
        'GWN' => 'Wales / Gwynedd',
        'HAM' => 'Wales / Hampshire',
        'MT' => 'Wales / Merthyr Tydfil',
        'MON' => 'Wales / Monmouthshire',
        'PT' => 'Wales / Neath Port Talbot',
        'NP' => 'Wales / Newport',
        'PEM' => 'Wales / Pembrokeshire',
        'POW' => 'Wales / Powys',
        'RT' => 'Wales / Rhondda Cynon Taff',
        'SS' => 'Wales / Swansea',
        'TF' => 'Wales / Torfaen',
        'WX' => 'Wales / Wrexham',
        'ANT' => 'Northern Ireland / County Antrim',
        'ARM' => 'Northern Ireland / County Armagh',
        'DOW' => 'Northern Ireland / County Down',
        'FER' => 'Northern Ireland / County Fermanagh',
        'LDY' => 'Northern Ireland / County Londonderry',
        'TYR' => 'Northern Ireland / County Tyrone',
       );
    return $states;
}
add_filter( 'woocommerce_states', 'wc_uk_counties_add_counties' );



function filter_switcher_html( 
    $html_output, 
    $currency, 
    $default_currency, 
    $available_currencies, 
    $currency_data, 
    $currency_switcher_text, 
    $currency_switcher_theme)
{
	ob_start();

	?>
	<div class="currency-div-hidden">
		<div class="currency-div-hidden-inner">
			<div class="head-currency"><a title="<?php echo $currency.' '.$currency_data[$currency]['symbol']; ?>" href="javascript:void(0)" class="link"><?php echo $currency.' '.$currency_data[$currency]['symbol']; ?></a> Currency </div>
			<a id="close-currency" class="close-currency" href="javascript:{}" title="Close"></a>
			<ul class="country-selector">
			<?php $count = 0; foreach ($available_currencies as $currency_code) { $count++;
				$selected = $extraclass = '';
				if ($currency == $currency_code){
					$selected ='is-selected';
				}
				if ($count > 5) $extraclass="country-selector-country_extra";
				echo sprintf('<li class="country-selector-country %s"><a class="%s js-toggle-currency" data-currency="%s" href="#" title="%s"> <i class="flag-icon flag-%s"></i> %s <span class="country-selector_locale-currency"> %s %s </span> </a> </li>',$extraclass,$selected,$currency_code,$currency_data[$currency_code]['name'],$currency_code,$currency_data[$currency_code]['name'],$currency_code,$currency_data[$currency_code]['symbol']);
			
			} ?>
			</ul>
		</div>
		<ul class="more-button">
			<li class="country-selector-country country-selector_show-more"> <a id="more-currency" href="javascript:{}" title="More"> More</a> </li>
		</ul>
	</div>   

	<?php $html_output = ob_get_clean();

	return $html_output;
}


add_filter( 'wcumcs_currency_switcher_html', 'filter_switcher_html', 10, 7 ); 