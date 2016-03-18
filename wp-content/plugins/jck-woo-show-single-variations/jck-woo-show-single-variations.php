<?php
/*
Plugin Name: WooCommerce Show Single Variations
Plugin URI: http://www.jckemp.com
Description: Show product variations in the main product loops
Version: 1.0.4
Author: James Kemp
Author URI: http://www.jckemp.com
Text Domain: jck-wssv
*/

defined('JCK_WSSV_PATH') or define('JCK_WSSV_PATH', plugin_dir_path( __FILE__ ));
defined('JCK_WSSV_URL') or define('JCK_WSSV_URL', plugin_dir_url( __FILE__ ));

class JCK_WSSV {

    public $name = 'WooCommerce Show Single Variations';
    public $shortname = 'Single Variations';
    public $slug = 'jck-wssv';
    public $version = "1.0.4";
    public $plugin_path;
    public $plugin_url;

/** =============================
    *
    * Construct the plugin
    *
    ============================= */

    public function __construct() {

        $this->set_constants();

        add_action( 'init',             array( $this, 'initiate_hook' ) );

    }

/** =============================
    *
    * Setup Constants for this class
    *
    ============================= */

    public function set_constants() {

        $this->plugin_path = JCK_WSSV_PATH;
        $this->plugin_url = JCK_WSSV_URL;

    }

/** =============================
    *
    * Run after the current user is set (http://codex.wordpress.org/Plugin_API/Action_Reference)
    *
    ============================= */

	public function initiate_hook() {

        if(is_admin()) {

            add_action( 'woocommerce_variation_options',                  array( $this, 'add_variation_checkboxes' ), 10, 3 );
            add_action( 'woocommerce_product_after_variable_attributes',  array( $this, 'add_variation_additional_fields' ), 10, 3 );
            add_action( 'woocommerce_variable_product_bulk_edit_actions', array( $this, 'add_variation_bulk_edit_actions' ), 10 );
            add_action( 'woocommerce_bulk_edit_variations_default',       array( $this, 'bulk_edit_variations' ), 10, 4 );
            add_action( 'woocommerce_save_product_variation',             array( $this, 'save_product_variation' ), 10, 2 );

            add_action( 'wp_ajax_jck_wssv_add_to_cart',                   array( $this, 'add_to_cart' ) );
            add_action( 'wp_ajax_nopriv_jck_wssv_add_to_cart',            array( $this, 'add_to_cart' ) );

            add_action( 'woocommerce_save_product_variation',             array( $this, 'add_variation_to_categories' ), 10, 2 );

            add_action(  'transition_post_status',                        array( $this, 'transition_variation_status' ), 10, 3 );

        } else {

            add_action( 'wp_enqueue_scripts',                             array( $this, 'frontend_scripts' ) );
            add_action( 'wp_enqueue_scripts',                             array( $this, 'frontend_styles' ) );

            add_action( 'woocommerce_product_query',                      array( $this, 'add_variations_to_product_query' ), 50, 2 );
            add_filter( 'woocommerce_shortcode_products_query',           array( $this, 'add_variations_to_shortcode_query' ), 10, 2 );

            add_filter( 'post_class',                                     array( $this, 'add_post_classes_in_loop' ) );
            add_filter( 'woocommerce_product_is_visible',                 array( $this, 'filter_variation_visibility' ), 10, 2 );

            add_filter( 'the_title',                                      array( $this, 'change_variation_title' ), 10, 2 );
            add_filter( 'post_type_link',                                 array( $this, 'change_variation_permalink' ), 10, 2 );
            add_filter( 'woocommerce_loop_add_to_cart_link',              array( $this, 'change_variation_add_to_cart_link' ), 10, 2 );

            add_filter( 'woocommerce_product_add_to_cart_text',           array( $this, 'add_to_cart_text' ), 10, 2 );
            add_filter( 'woocommerce_product_add_to_cart_url',            array( $this, 'add_to_cart_url' ), 10, 2 );

        }

        add_filter( 'woocommerce_taxonomy_objects_product_cat',           array( $this, 'add_product_categories_to_variations' ) );

	}

/**	=============================
    *
    * Load plugin textdomain
    *
    ============================= */

    public function textdomain() {

		load_plugin_textdomain( 'jck-wssv', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

/**	=============================
    *
    * Frontend Styles
    *
    * @access public
    *
    ============================= */

    public function frontend_styles() {

        wp_register_style( $this->slug.'_styles', $this->plugin_url . 'assets/frontend/css/main.min.css', array(), $this->version );

        wp_enqueue_style( $this->slug.'_styles' );

    }

/**	=============================
    *
    * Frontend Scripts
    *
    * @access public
    *
    ============================= */

    public function frontend_scripts() {

        wp_register_script( $this->slug.'_scripts', $this->plugin_url . 'assets/frontend/js/main.min.js', array( 'jquery', 'wc-add-to-cart' ), $this->version, true);

        wp_enqueue_script( $this->slug.'_scripts' );

        $vars = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( $this->slug ),
			'pluginSlug' => $this->slug
		);

		wp_localize_script( $this->slug.'_scripts', 'jck_wssv_vars', $vars );

    }

/** =============================
    *
    * Frontend: Add variaitons to product query, similar to pre_get_posts
    *
    * @param  [obj] [$q] The current query
    *
    ============================= */

    public function add_variations_to_product_query( $q, $wc_query ) {

        if( !is_admin() && is_woocommerce() && $q->is_main_query() && isset( $q->query_vars['wc_query'] ) ) {

            global $_chosen_attributes;

            // Add product variations to the query

            $post_type = (array) $q->get('post_type');
            $post_type[] = 'product_variation';
            if( !in_array('product', $post_type) ) $post_type[] = 'product';
            $q->set('post_type', $post_type );

            // update the meta query to include our variations

            $meta_query = (array) $q->get('meta_query');
            $meta_query = $this->update_meta_query( $meta_query );

            $q->set('meta_query', $meta_query );

            // if we've filtered the products using layered nav

            if( is_filtered() ) {

                $current_post__in = (array) $q->get('post__in');
                $filtered_variation_ids = $this->get_filtered_variation_ids();
                $post__in = array_merge($current_post__in, $filtered_variation_ids);

                $q->set( 'post__in', $post__in );
                $wc_query->post__in = $post__in;

            }

            add_action( 'wp', array( $this, 'get_products_in_view' ), 5);

        }

    }

/** =============================
    *
    * Frontend: Add variaitons to shortcode queries
    *
    * @param arr $query_args
    * @param arr $shortcode_args
    *
    ============================= */

    public function add_variations_to_shortcode_query( $query_args, $shortcode_args ) {

        // Add product variations to the query

        $post_type = (array) $query_args['post_type'];
        $post_type[] = 'product_variation';

        $query_args['post_type'] = $post_type;

        // update the meta query to include our variations

        $meta_query = (array) $query_args['meta_query'];
        $meta_query = $this->update_meta_query( $meta_query );

        $query_args['meta_query'] = $meta_query;

        return $query_args;

    }

/** =============================
    *
    * Frontend: Update unfiltered_product_ids to include variations
    *
    * This means the layered nav count will be correct
    *
    ============================= */

    public function get_products_in_view() {

        global $wp_the_query;

        // Get main query
		$current_wp_query = $wp_the_query->query;

		// Get WP Query for current page (without 'paged')
		unset( $current_wp_query['paged'] );

		// Generate a transient name based on current query
		$transient_name = 'jck_wssv_uf_pid_' . md5( http_build_query( $current_wp_query ) . WC_Cache_Helper::get_transient_version( 'product_query' ) );
		$transient_name = ( is_search() ) ? $transient_name . '_s' : $transient_name;

		if ( false === ( $unfiltered_product_ids = get_transient( $transient_name ) ) ) {

            $current_unfiltered_product_ids = WC()->query->unfiltered_product_ids;

            // Get all visible posts, regardless of filters
    		$unfiltered_product_ids = get_posts(
    			array_merge(
    				$current_wp_query,
    				array(
    					'post_type'              => 'product_variation',
    					'numberposts'            => -1,
    					'post_status'            => 'publish',
    					'meta_query'             => $wp_the_query->meta_query->queries,
    					'fields'                 => 'ids',
    					'no_found_rows'          => true,
    					'update_post_meta_cache' => false,
    					'update_post_term_cache' => false,
    					'pagename'               => '',
    					'wc_query'               => 'get_product_variations_in_view'
    				)
    			)
    		);

    		$unfiltered_product_ids = array_merge($unfiltered_product_ids, $current_unfiltered_product_ids);

    		set_transient( $transient_name, $unfiltered_product_ids, DAY_IN_SECONDS * 30 );

		}

        WC()->query->unfiltered_product_ids = $unfiltered_product_ids;

        // Also store filtered posts ids...
		if ( sizeof( WC()->query->post__in ) > 0 ) {
			WC()->query->filtered_product_ids = array_intersect( WC()->query->unfiltered_product_ids, WC()->query->post__in );
		} else {
			WC()->query->filtered_product_ids = WC()->query->unfiltered_product_ids;
		}

		// And filtered post ids which just take layered nav into consideration (to find max price in the price widget)
		if ( sizeof( WC()->query->layered_nav_post__in ) > 0 ) {
			WC()->query->layered_nav_product_ids = array_intersect( WC()->query->unfiltered_product_ids, WC()->query->layered_nav_post__in );
		} else {
			WC()->query->layered_nav_product_ids = WC()->query->unfiltered_product_ids;
		}

    }

/** =============================
    *
    * Helper: Update meta query
    *
    * Add OR parameters to also search for variations with specific visibility
    *
    * @param  [arr] [$meta_query]
    * @return [arr]
    *
    ============================= */

    public function update_meta_query( $meta_query ) {

        if( !empty($meta_query) ) {
            foreach( $meta_query as $index => $meta_query_item ) {
                if( $meta_query_item['key'] == "_visibility" ) {

                    $meta_query[$index] = array();
                    $meta_query[$index]['relation'] = 'OR';

                    $meta_query[$index][] = array(
                        'key' => '_visibility',
                        'value' => 'visible',
                        'compare' => 'LIKE'
                    );

                    if( is_search() ) {

                        $meta_query[$index][] = array(
                            'key' => '_visibility',
                            'value' => 'search',
                            'compare' => 'LIKE'
                        );

                    } else {

                        $meta_query[$index][] = array(
                            'key' => '_visibility',
                            'value' => 'catalog',
                            'compare' => 'LIKE'
                        );

                    }

                    if( is_filtered() ) {

                        $meta_query[$index][] = array(
                            'key' => '_visibility',
                            'value' => 'filtered',
                            'compare' => 'LIKE'
                        );

                    }

                }
            }
        }

        return $meta_query;

    }

/** =============================
    *
    * Helper: Get filtered variation ids
    *
    * @return [arr]
    *
    ============================= */

    public function get_filtered_variation_ids() {

        global $_chosen_attributes;

        $variation_ids = array();

        $args = array(
            'post_type'  => 'product_variation',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key'     => '_visibility',
                    'value'   => 'filtered',
                    'compare' => 'LIKE',
                )
            )
        );

        $min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : false;
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : false;

		if( $min_price !== false && $max_price !== false ) {

    		$args['meta_query'][] = array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            );

		}

		if( $_chosen_attributes && !empty( $_chosen_attributes ) ) {

            $i = 10; foreach( $_chosen_attributes as $attribute_key => $attribute_data ) {

                $attribute_meta_key = sprintf('attribute_%s', $attribute_key);

                $attribute_term_slugs = array();

                foreach( $attribute_data['terms'] as $attribute_term_id ) {
                    $attribute_term = get_term_by('id', $attribute_term_id, $attribute_key);
                    $attribute_term_slugs[] = $attribute_term->slug;
                }

                if( $attribute_data['query_type'] == "or" ) {

                    $args['meta_query'][$i] = array(
                        'key'     => $attribute_meta_key,
                        'value'   => $attribute_term_slugs,
                        'compare' => 'IN',
                    );

                } else {

                    $args['meta_query'][$i] = array(
                        'relation' => 'AND'
                    );

                    foreach( $attribute_term_slugs as $attribute_term_slug ) {
                        $args['meta_query'][$i][] = array(
                            'key'     => $attribute_meta_key,
                            'value'   => $attribute_term_slug,
                            'compare' => '=',
                        );
                    }

                }

            $i++; }

        }

        $variations = new WP_Query( $args );

        if ( $variations->have_posts() ) {

        	while ( $variations->have_posts() ) {
        		$variations->the_post();

        		$variation_ids[] = get_the_id();
        	}

        }

        wp_reset_postdata();

        return $variation_ids;

    }

/** =============================
    *
    * Frontend: Add relevant product classes to loop item
    *
    * @param  [arr] [$classes]
    * @return [arr]
    *
    ============================= */

    public function add_post_classes_in_loop( $classes ) {

        global $post, $product;

        if( $product && $post->post_type === "product_variation" ) {

            $classes = array_diff($classes, array('hentry', 'post'));

            $classes[] = "product";
            // add other classes here, find woocommerce function

        }

        return $classes;

    }

/** =============================
    *
    * Admin: Add variation checkboxes
    *
    * @param  [str] [$loop]
    * @param  [arr] [$variation_data]
    * @param  [obj] [$variation]
    *
    ============================= */

    public function add_variation_checkboxes( $loop, $variation_data, $variation ) {

        include('inc/admin/variation-checkboxes.php');

    }

/** =============================
    *
    * Admin: Add variation options
    *
    * @param  [str] [$loop]
    * @param  [arr] [$variation_data]
    * @param  [obj] [$variation]
    *
    ============================= */

    public function add_variation_additional_fields( $loop, $variation_data, $variation ) {

        include('inc/admin/variation-additional-fields.php');

    }

/** =============================
    *
    * Admin: Add variation bulk edit actions
    *
    ============================= */

    public function add_variation_bulk_edit_actions() {

        include('inc/admin/variation-bulk-edit-actions.php');

    }

/** =============================
    *
    * Admin: Bulk edit actions
    *
    * @param  [str] [$bulk_action]
    * @param  [arr] [$data]
    * @param  [int] [$product_id]
    * @param  [arr] [$variations]
    *
    ============================= */

    public function bulk_edit_variations( $bulk_action, $data, $product_id, $variations ) {

        if ( method_exists( $this, "variation_bulk_action_$bulk_action" ) ) {
			call_user_func( array( $this, "variation_bulk_action_$bulk_action" ), $variations );
		}

    }

/** =============================
    *
    * Helper: Unset array item by the value
    *
    * @param  [arr] [$array]
    * @param  [str] [$value]
    * @return [arr]
    *
    ============================= */

    public function unset_item_by_value( $array, $value ) {

        if(($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }

        return $array;

    }

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in (x)
    *
    * @param  [arr] [$variations]
    * @param  [arr] [$show]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in( $variations, $show ) {

        foreach ( $variations as $variation_id ) {

            $visibility = (array)get_post_meta( $variation_id, '_visibility', true );

            if( in_array( $show, $visibility ) ) {

                $visibility = $this->unset_item_by_value( $visibility, $show );

            } else {
                $visibility[] = $show;
            }

            update_post_meta( $variation_id, '_visibility', $visibility );
        }

    }

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in Search
    *
    * @param  [arr] [$variations]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in_search( $variations ) {

        $this->variation_bulk_action_toggle_show_in( $variations, 'search' );

	}

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in Filtered
    *
    * @param  [arr] [$variations]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in_filtered( $variations ) {

        $this->variation_bulk_action_toggle_show_in( $variations, 'filtered' );

	}

/** =============================
    *
    * Admin: Bulk Action - Toggle Show in Catalog
    *
    * @param  [arr] [$variations]
    *
    ============================= */

    private function variation_bulk_action_toggle_show_in_catalog( $variations ) {

        $this->variation_bulk_action_toggle_show_in( $variations, 'catalog' );

	}

/** =============================
    *
    * Admin: Save variation options
    *
    * @param  [int] [$variation_id]
    * @param  [int] [$i]
    *
    ============================= */

    public function save_product_variation( $variation_id, $i ) {

        // setup posted data

        $visibility = array();
        $title = isset( $_POST['jck_wssv_display_title'] ) ? $_POST['jck_wssv_display_title'][ $i ] : false;

        if( isset( $_POST['jck_wssv_variable_show_search'][$i] ) )
            $visibility[] = "search";

        if( isset( $_POST['jck_wssv_variable_show_filtered'][$i] ) )
            $visibility[] = "filtered";

        if( isset( $_POST['jck_wssv_variable_show_catalog'][$i] ) )
            $visibility[] = "catalog";

        // set visibility

        if( !empty( $visibility ) ) {
			update_post_meta( $variation_id, '_visibility', $visibility );
		} else {
    		delete_post_meta( $variation_id, '_visibility' );
		}

		// set display title

		if( $title )
    		update_post_meta( $variation_id, '_jck_wssv_display_title', $title );

    }

/** =============================
    *
    * Frontend: Change variation title
    *
    * @param  [str] [$title]
    * @param  [int] [$id]
    * @return [str]
    *
    ============================= */

    public function change_variation_title( $title, $id ) {

        if( $this->is_product_variation( $id ) ) {
            $title = $this->get_variation_title( $id );
        }

        return $title;

    }

/** =============================
    *
    * Helper: Get default variation title
    *
    * @param  [int] [$variation_id]
    * @return [str]
    *
    ============================= */

    public function get_variation_title( $variation_id ) {

        if( !$variation_id || $variation_id == "" )
            return "";

        $variation = wc_get_product( absint( $variation_id ), array( 'product_type' => 'variable' ) );
        $variation_title = ( $variation->get_title() != "Auto Draft" ) ? $variation->get_title() : "";
        $variation_custom_title = get_post_meta($variation->variation_id, '_jck_wssv_display_title', true);

        return ( $variation_custom_title ) ? $variation_custom_title : $variation_title;

    }

/** =============================
    *
    * Frontend: Change variation permalink
    *
    * @param  [str] [$url]
    * @param  [str] [$post]
    * @return [str]
    *
    ============================= */

    public function change_variation_permalink( $url, $post ) {

        if ( 'product_variation' == get_post_type( $post ) ) {

            global $product;

            return $this->get_variation_url( $product );

        }

        return $url;

    }

/** =============================
    *
    * Helper: Get variation URL
    *
    * @param  [str] [$variation]
    * @return [str]
    *
    ============================= */

    public function get_variation_url( $variation ) {

        $url = "";

        if( $variation->variation_id ) {

            $variation_data = array_filter( wc_get_product_variation_attributes( $variation->variation_id ) );
            $parent_product_id = $variation->parent->post->ID;
            $parent_product_url = get_the_permalink( $parent_product_id );

            $url = add_query_arg( $variation_data, $parent_product_url );

        }

        return $url;

    }

/** =============================
    *
    * Frontend: Change variation add to cart link
    *
    * @param  [str] [$anchor]
    * @param  [str] [$product]
    * @return [str]
    *
    ============================= */

    public function change_variation_add_to_cart_link( $anchor, $product ) {

        if( $product->variation_id ) {

            $anchor = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s" data-variation_id="%s">%s</a>',
                esc_url( $product->add_to_cart_url() ),
                esc_attr( $product->id ),
                esc_attr( $product->get_sku() ),
                esc_attr( isset( $quantity ) ? $quantity : 1 ),
                $this->is_purchasable( $product ) && $product->is_in_stock() ? 'add_to_cart_button jck_wssv_add_to_cart' : '',
                esc_attr( $product->product_type ),
                esc_html( $product->variation_id ),
                esc_html( $product->add_to_cart_text() )
            );

        }

        return $anchor;

    }

/** =============================
    *
    * Helper: Is product variation?
    *
    * @param  [int] [$id]
    * @return [bool]
    *
    ============================= */

    public function is_product_variation( $id ) {

        $post_type = get_post_type( $id );

        return $post_type == "product_variation" ? true : false;

    }

/** =============================
    *
    * Admin: Get variation checkboxes
    *
    * @param  [obj] [$variation]
    * @param  [int] [$index]
    * @return [arr]
    *
    ============================= */

    public function get_variation_checkboxes( $variation, $index ) {

        $visibility = get_post_meta($variation->ID, '_visibility', true);

        $checkboxes = array(
            array(
                'class' => 'jck_wssv_variable_show_search',
                'name' => sprintf('jck_wssv_variable_show_search[%d]', $index),
                'checked' => is_array( $visibility ) && in_array('search', $visibility) ? true : false,
                'label' => __( 'Show in Search Results?', 'jck-wssv' )
            ),
            array(
                'class' => 'jck_wssv_variable_show_filtered',
                'name' => sprintf('jck_wssv_variable_show_filtered[%d]', $index),
                'checked' => is_array( $visibility ) && in_array('filtered', $visibility) ? true : false,
                'label' => __( 'Show in Filtered Results?', 'jck-wssv' )
            ),
            array(
                'class' => 'jck_wssv_variable_show_catalog',
                'name' => sprintf('jck_wssv_variable_show_catalog[%d]', $index),
                'checked' => is_array( $visibility ) && in_array('catalog', $visibility) ? true : false,
                'label' => __( 'Show in Catalog?', 'jck-wssv' )
            ),
        );

        return $checkboxes;

    }

/** =============================
    *
    * Helper: Filter variaiton visibility
    *
    * Set variation to is_visible() if the options are selected
    *
    * @param  [bool] [$visible]
    * @param  [bool] [$id]
    * @return [bool]
    *
    ============================= */

    public function filter_variation_visibility( $visible, $id ) {

        global $product;

        if( isset( $product->variation_id ) ) {

            $visibility = get_post_meta($product->variation_id, '_visibility', true);

            if( is_array( $visibility ) ) {

                // visible in search

                if( $this->is_visible_when('search', $product->variation_id) ) {
                    $visible = true;
                }

                // visible in filtered

                if( $this->is_visible_when('filtered', $product->variation_id) ) {
                    $visible = true;
                }

                // visible in catalog

                if( $this->is_visible_when('catalog', $product->variation_id) ) {
                    $visible = true;
                }


            }

        }

        return $visible;

    }

/** =============================
    *
    * Helper: Is visible when...
    *
    * Check if a variation is visible when search, filtered, catalog
    *
    * @param  [str] [$when]
    * @param  [int] [$id]
    * @return [bool]
    *
    ============================= */

    public function is_visible_when( $when = false, $id ) {

        $visibility = get_post_meta($id, '_visibility', true);

        if( is_array( $visibility ) ) {

            // visible in search

            if( is_search() && in_array($when, $visibility) ) {
                return true;
            }

            // visible in filtered

            if( is_filtered() && in_array($when, $visibility) ) {
                return true;
            }

            // visible in catalog

            if( !is_filtered() && !is_search() && in_array($when, $visibility) ) {
                return true;
            }


        }

        return false;

    }

/** =============================
    *
    * Ajax: Add to cart
    *
    ============================= */

    public static function add_to_cart() {

		ob_start();

		$product_id           = apply_filters( 'jck_wssv_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$variation_id         = apply_filters( 'jck_wssv_add_to_cart_variation_id', absint( $_POST['variation_id'] ) );
		$quantity             = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
		$passed_validation    = apply_filters( 'jck_wssv_add_to_cart_validation', true, $variation_id, $quantity );
		$product_status       = get_post_status( $variation_id );
		$variations           = array();
		$variation            = wc_get_product( $variation_id, array( 'product_type' => 'variable' ) );
		$variation_attributes = $variation->get_variation_attributes();

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation_attributes ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $variation_id );
			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $product_id );
			}

			$wc_ajax = new WC_AJAX();

			// Return fragments
			$wc_ajax->get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
			);

			wp_send_json( $data );

		}

		wp_die();
	}

/** =============================
    *
    * Attach product categories to variations
    *
    * @param  [arr] [$post_types]
    * @return [arr]
    *
    ============================= */

    public function add_product_categories_to_variations( $post_types ) {

        $post_types[] = "product_variation";

        return $post_types;

    }

/** =============================
    *
    * Admin: Save variation hook
    *
    * @param  [int] [$variation_id]
    * @param  [int] [$i]
    *
    ============================= */

    public function add_variation_to_categories( $variation_id, $i ) {

        $parent_product_id = wp_get_post_parent_id( $variation_id );

        if( $parent_product_id ) {

            // add categories to variation

            $terms = (array) wp_get_post_terms( $parent_product_id, 'product_cat', array("fields" => "ids") );
            wp_set_post_terms( $variation_id, $terms, 'product_cat' );

            // add attributes to variation so it shows in the layered nav count

            $variation_attributes = wc_get_product_variation_attributes( $variation_id );

            if( $variation_attributes && !empty( $variation_attributes ) ) {
                foreach( $variation_attributes as $attribute_name => $attribute_value ) {
                    if( strpos( $attribute_name, 'attribute_pa_' ) !== false ) {
                        if( isset( $_POST['jck_wssv_variable_show_filtered'] ) ) {
                            wp_set_object_terms( $variation_id, $attribute_value, str_replace('attribute_', '', $attribute_name) );
                        } else {
                            wp_set_object_terms( $variation_id, "", str_replace('attribute_', '', $attribute_name) );
                        }
                    }
                }
            }

        }

    }

/** =============================
    *
    * Frontend: is_purchasable
    *
    * @param  [obj] [$product]
    * @return [bool]
    *
    ============================= */

    public function is_purchasable( $product ) {

        $purchasable = $product->is_purchasable();

        if( $product->variation_id ) {

            if( $product->variation_data && !empty( $product->variation_data ) ) {
                foreach( $product->variation_data as $value ) {
                    if( $value == "" ) {
                        $purchasable = false;
                    }
                }
            }

        }

        return $purchasable;

    }

/** =============================
    *
    * Frontend: Add to Cart Text
    *
    * @param  [str] [$text]
    * @param  [obg] [$product]
    * @return [str]
    *
    ============================= */

    public function add_to_cart_text( $text, $product ) {

        if( $product->variation_id ) {

            $text = $this->is_purchasable( $product ) && $product->is_in_stock() ? $text : __( 'Select options', 'woocommerce' );

        }

        return $text;

    }

/** =============================
    *
    * Frontend: Add to Cart URL
    *
    * @param  [str] [$url]
    * @param  [obg] [$product]
    * @return [str]
    *
    ============================= */

    public function add_to_cart_url( $url, $product ) {

        if( $product->variation_id ) {

            $url = $this->is_purchasable( $product ) && $product->is_in_stock() ? $url : $this->get_variation_url( $product );

        }

        return $url;

    }

/**	=============================
    *
    * Get Woo Version Number
    *
    * @return mixed bool/str NULL or Woo version number
    *
    ============================= */

    public function get_woo_version_number() {

        // If get_plugins() isn't available, require it
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';

        // If the plugin version number is set, return it
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];

        } else {
            // Otherwise return null
            return NULL;
        }

    }

/**	=============================
    *
    * Admin: Transition variation post_status when parent changes
    *
    ============================= */

    public function transition_variation_status( $new_status, $old_status, $post ) {

        global $wpdb;

        if( $post->post_type == "product" ) {

            $wpdb->update(
                "{$wpdb->prefix}posts",
                array( 'post_status' => $new_status ),
                array(
                    'post_parent' => $post->ID,
                    'post_type' => 'product_variation'
                ),
                array( '%s' ),
                array(
                    '%d',
                    '%s'
                )
            );

        }

    }

}

$jck_wssv = new JCK_WSSV();