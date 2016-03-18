<?php
/*
Author: Sergio Pellegrini
URL: htp://www.plusplusminus.co.za

*/

require_once( 'library/navwalker.php' ); 

if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/library/admin/ReduxCore/framework.php' ) ) {
	require_once( dirname( __FILE__ ) . '/library/admin/ReduxCore/framework.php' );
}
if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/library/option-config.php' ) ) {
	require_once( dirname( __FILE__ ) . '/library/option-config.php' );
}

add_action( 'init', 'be_initialize_cmb_meta_boxes', 9999 );

function be_initialize_cmb_meta_boxes() {
  if ( !class_exists( 'cmb_Meta_Box' ) ) {
    require_once( 'library/metabox/init.php' );
  }
}

require_once( 'library/bones.php' ); // if you remove this, bones will break
require_once( 'library/admin.php' ); // this comes turned off by default

require_once( 'assets/functions/admin-functions.php' ); 

require_once( 'assets/functions/products-functions.php' ); 
require_once( 'assets/functions/kvd-functions.php' ); 


$kvd_products = new kvdProducts();
$kvd_theme = new kvdTheme();

function main_nav($nav = 'secondary-nav',$class='enumenu_ul cf') {
    // display the wp3 menu if available
    wp_nav_menu(array(
        'container' => false,                                       // remove nav container
        'container_class' => 'menu clearfix',                       // class of container (should you choose to use it)
        'menu' => __( 'The Secondary Menu', 'bonestheme' ),              // nav name
        'menu_class' => $class,              // adding custom nav class
        'theme_location' => $nav,                             // where it's located in the theme
        'before' => '',                                             // before the menu
        'after' => '',                                            // after the menu
        'link_before' => '',                                      // before each link
        'link_after' => '',                                       // after each link
        'depth' => 2,                                             // limit the depth of the nav
        'fallback_cb' => 'wp_bootstrap_navwalker::fallback',  // fallback               // for bootstrap nav
    ));
} /* end bones main nav */

add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Main Sidebar', 'theme-slug' ),
        'id' => 'sidebar-1',
        'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'theme-slug' ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => '</li>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name' => __( 'Filter Sidebar', 'theme-slug' ),
        'id' => 'sidebar-filter',
        'description' => __( 'Widgets in this area will be shown on all lodge pages.', 'theme-slug' ),
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ) );

    
}

register_nav_menus(
        array(
            'main-nav' => __( 'The Main Menu', 'bonestheme' ),   // main nav in header
            'secondary-nav' => __( 'The Secondary Menu', 'bonestheme' ),   // main nav in header
            'about-nav' => __( 'The About Menu', 'bonestheme' ),   // main nav in header
            'contact-nav' => __( 'The Contact Menu', 'bonestheme' ),   // main nav in header
            'more-nav' => __( 'The More Info Menu', 'bonestheme' ),   // main nav in header
            'connect-nav' => __( 'The Connect Menu', 'bonestheme' ),   // main nav in header
        )
    );


function short_name_options($arr) {

        $arr['menu_order'] = 'Default';
        $arr['popularity'] = 'Popularity';
        $arr['rating' ]    = 'Average rating';
        $arr['date' ]      = 'Latest';
        $arr['price']      = 'Price: low to high';
        $arr['price-desc'] = 'Price: high to low';
        return $arr;
}
add_filter( 'woocommerce_catalog_orderby', 'short_name_options' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'short_name_options' );

function custom_breadcrumb() {
    global $post;
  if(!is_front_page() && !is_home() ) {
  
    echo '<ol class="breadcrumb">';
    echo '<li><a href="'.get_option('home').'">Home</a></li>';
    if (is_single()) {
        if (is_singular('products')) {
            $page = get_page_by_title('Shop');
            echo '<li><a href="'.get_permalink($page->ID).'">Shop</a></li> <li> ';
            echo get_the_term_list( $post->ID, 'product-category', '', ', ' );
            echo '</li>';
        } else {
            echo '<li>';
                the_category(', ');
            echo '</li>';
        }
     
      if (is_single()) {
        echo '<li>';
        the_title();
        echo '</li>';
      }
    } elseif (is_tax('product-category')) {
        $page = get_page_by_title('Shop');
      echo '<li><a href="'.get_permalink($page->ID).'">Shop</a></li> <li> ';
      single_cat_title();
      echo '</li>';
    } elseif (is_tax('product-tag')) {
        $page = get_page_by_title('Shop');
      echo '<li><a href="'.get_permalink($page->ID).'">Shop</a></li> <li> ';
      single_cat_title();
      echo '</li>';
    } elseif (is_category()) {
      echo '<li>';
      single_cat_title();
      echo '</li>';
    } elseif (is_page() && (!is_front_page())) {
      echo '<li>';
      the_title();
      echo '</li>';
    } elseif (is_tag()) {
      echo '<li>Tag: ';
      single_tag_title();
      echo '</li>';
    } elseif (is_day()) {
      echo'<li>Archive for ';
      the_time('F jS, Y');
      echo'</li>';
    } elseif (is_month()) {
      echo'<li>Archive for ';
      the_time('F, Y');
      echo'</li>';
    } elseif (is_year()) {
      echo'<li>Archive for ';
      the_time('Y');
      echo'</li>';
    } elseif (is_author()) {
      echo'<li>Author Archives';
      echo'</li>';
    } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
      echo '<li>Blog Archives';
      echo'</li>';
    } elseif (is_search()) {
      echo'<li>Search Results';
      echo'</li>';
    }
    echo '</ol>';
  }

}

function new_excerpt_more( $more ) {
  return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

function swiftype_search_params_filter( $params ) {
  $params['search_fields[posts]'] = array('terms^10', 'title^1');
  return $params;
}

add_filter( 'swiftype_search_params', 'swiftype_search_params_filter');
function swiftype_document_builder_filter( $document, $post ) {
  $term_names = array();
  $taxonomy_names = get_object_taxonomies( $post );
  foreach ( $taxonomy_names as $taxonomy ) {
    $terms = get_the_terms( $post->ID, $taxonomy );
    if ( is_array( $terms ) ) {
      foreach ( $terms as $term ) {
        array_push( $term_names, $term->name );
      }
    }
  }
  $document['fields'][] = array( 'name' => 'terms', 'type' => 'string', 'value' => $term_names );
  return $document;
}
add_filter( 'swiftype_document_builder', 'swiftype_document_builder_filter', 8, 2 );

function kvd_order_totals( $ouput,$order_id ) {
  global $woocommerce;

  if (!$order_id) return; 

  $order = new WC_Order( $order_id );

  $order_item_totals = $order->get_order_item_totals();

  unset( $order_item_totals['payment_method'] );

  $output = '';

  foreach ( $order_item_totals as $order_item_total ) {

    $output .=  '<tr class="border-bottom">' .
        '<td align="left">' .
        '<strong>' . $order_item_total['label'] . '</strong></td>' .
        '<td align="right"><strong>' . $order_item_total['value'] . '</strong></td>' .
        '</tr>' ;

  }

  return $output;

}

add_filter('pdf_template_order_totals','kvd_order_totals',10,2);



function kvd_get_woocommerce_pdf_order_details( $pdflines, $order_id) {
  global $woocommerce;
  $PDF =  new WC_send_pdf();
  $order   = new WC_Order( $order_id );

  $country = $order->billing_country;
  $payment_method = $order->payment_method_title;
  $PDFINVOICENUM = $PDF->get_woocommerce_pdf_invoice_num( $order_id );
  $PDFINVOICEDATE = $PDF->get_woocommerce_pdf_date( $order_id,'ordered' );
  $PDFORDERENUM = $order_id;
  $PDFORDERDATE = $PDF->get_woocommerce_pdf_date( $order_id,'ordered' );

  if (!in_array( $country, array('ZA') )) {

      $pdflines =  '<table class="shop_table orderdetails" width="100%">' . 
            '<thead>' .
            '<tr><th colspan="4" align="left" valign="top"><h3 style="text-transform:uppercase; margin-bottom: 0px;">' . esc_html__('Commercial Invoice', 'woocommerce-pdf-invoice') . '</h3></th></tr>' .
            '<tr class="border-bottom-thick"><td style="padding-bottom:10px;" colspan="4"><strong>Invoice Nr:</strong>' . $PDFINVOICENUM . ' <strong>Invoice Date:</strong> '.$PDFINVOICEDATE.' <br><strong>Order Nr:</strong> '.$PDFORDERENUM.' <strong>Order Date:</strong> '.$PDFORDERDATE.' <br><strong>Payment Method:</strong> ' . $payment_method . '</td></tr><tr>' .
            '<th width="10%" valign="top" align="left">'  . esc_html__( 'Qty', 'woocommerce-pdf-invoice' )    . '</th>' .           
            '<th width="50%" valign="top" align="left">'  . esc_html__( 'Description', 'woocommerce-pdf-invoice' )  . '</th>' .
            '<th width="20%" valign="top" align="left">'  . esc_html__( 'Unit', 'woocommerce-pdf-invoice' )  . '</th>' .
            '<th width="20%" valign="top" align="right">' . esc_html__( 'Total', 'woocommerce-pdf-invoice' )  . '</th>' .
            '</tr>' .
            '</thead>';
      $pdflines .= '<tbody>';
      
      if ( sizeof( $order->get_items() ) > 0 ) : 

        foreach ( $order->get_items() as $item ) {
          
          if ( $item['qty'] ) {
            
            $line = '';
            // $item_loop++;

            $_product   = $order->get_product_from_item( $item );
            $item_name  = $item['name'];



            $item_meta  = new WC_Order_Item_Meta( $item, $_product );

            
            if ( $meta = $item_meta->display( true, true ) ) {
              $meta_output   = apply_filters( 'pdf_invoice_meta_output', ' ( ' . $meta . ' ) ' );
              $item_name    .= $meta_output;
            }
            
            $line =   '<tr>' .
                  '<td valign="top" width="10%" align="left">' . $item['qty'] . ' x</td>' .
                  '<td valign="top" width="50%" align="left">' .  $item_name . '</td>' .
                  '<td valign="top" width="20%" align="left">' .  wc_price( $item['line_subtotal'] / $item['qty'], array( 'currency' => $order->get_order_currency() ) ) . '</td>' .
                  '<td valign="top" width="20%" align="right">' .  wc_price( $item['line_subtotal'] + $item['line_subtotal_tax'], array( 'currency' => $order->get_order_currency() ) ). '</td>' .
                  '</tr>';
            
            $pdflines .= $line;
          }
        }

      endif;

      $pdflines .=  '</tbody>';
      $pdflines .=  '</table>';
  } else {

    $pdflines =  '<table class="shop_table orderdetails" width="100%">' . 
              '<thead>' .
              '<tr><th colspan="4" align="left" valign="top"><h3 style="text-transform:uppercase; margin-bottom: 0px;">' . esc_html__('Tax Invoice', 'woocommerce-pdf-invoice') . '</h3></th></tr>' .
              '<tr class="border-bottom-thick"><td style="padding-bottom: 10px;" colspan="4"><strong>Invoice Nr:</strong>' . $PDFINVOICENUM . ' <strong>Invoice Date:</strong> '.$PDFINVOICEDATE.' <br><strong>Order Nr:</strong> '.$PDFORDERENUM.' <strong>Order Date:</strong> '.$PDFORDERDATE.' <br><strong>Payment Method:</strong> ' . $payment_method . '</td></tr><tr>' .
              '<th width="10%" valign="top" align="left">'  . esc_html__( 'Qty', 'woocommerce-pdf-invoice' )    . '</th>' .           
              '<th width="50%" valign="top" align="left">'  . esc_html__( 'Description', 'woocommerce-pdf-invoice' )  . '</th>' .
              '<th width="20%" valign="top" align="left">'  . esc_html__( 'Unit ex. VAT', 'woocommerce-pdf-invoice' )   . '</th>' .
              '<th width="20%" valign="top" align="right">'  . esc_html__( 'Total ex. VAT', 'woocommerce-pdf-invoice' )  . '</th>' .
              '</tr>' .
              '</thead>' .
              '</table>';

    $pdflines  .= '<table width="100%">';
    $pdflines .= '<tbody>';
    
    if ( sizeof( $order->get_items() ) > 0 ) : 

      foreach ( $order->get_items() as $item ) {
        
        if ( $item['qty'] ) {
          
          $line = '';
          // $item_loop++;

          $_product   = $order->get_product_from_item( $item );
          $item_name  = $item['name'];

          
          $item_meta  = new WC_Order_Item_Meta( $item, $_product );

        

          if ( $meta = $item_meta->display( true, true ) ) {
            $meta_output   = apply_filters( 'pdf_invoice_meta_output', ' ( ' . $meta . ' ) ' );
            $item_name    .= $meta_output;
          }
          
          $line =   '<tr>' .
                '<td valign="top" width="10%" align="left">' . $item['qty'] . ' x</td>' .
                '<td valign="top" width="50%" align="left">' .  $item_name . '</td>' .
                '<td valign="top" width="20%" align="left">'  .  wc_price( $item['line_subtotal'] / $item['qty'], array( 'currency' => $order->get_order_currency() ) ) . '</td>' .             
                '<td valign="top" width="20%" align="right">'  .  wc_price( $item['line_subtotal'], array( 'currency' => $order->get_order_currency() ) ) . '</td>' .  
                '</tr>';
          
          $pdflines .= $line;
        }
      }
  
    endif;

    $pdflines .=  '</tbody>';
    $pdflines .=  '</table>';
  }
  
  
  return $pdflines;
}

add_filter( 'pdf_template_line_output', 'kvd_get_woocommerce_pdf_order_details',1,2 );

function kvd_get_pdf_order_totals( $output,$order_id ) {
  global $woocommerce;

  if (!$order_id) return; 
  $order = new WC_Order( $order_id );

  $country = $order->billing_country;

  if (in_array( $country, array('ZA') )) {

    return $output;

  } else {

    $order_item_totals = $order->get_order_item_totals();

    unset( $order_item_totals['payment_method'] );

    $output = '';

    $output .=  '<tr class="border-bottom">' .
            '<td align="left">' .
            '<strong>Shipping:</strong></td>' .
            '<td align="right"><strong>' . wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_order_currency() ) ) . ' via '.$order->get_shipping_method().'</strong></td>' .
            '</tr>' ;

    $output .=  '<tr class="border-bottom">' .
            '<td align="left">' .
            '<strong>Total:</strong></td>' .
            '<td align="right"><strong>' . wc_price( $order->get_total(), array( 'currency' => $order->get_order_currency() ) ) .'</strong></td>' .
            '</tr>' ;

    if( $order->get_total_refunded() > 0 ) {

      $output .=  '<tr class="border-bottom">' .
            '<td align="right">' .
            '<strong>Amount Refunded:</strong></td>' .
            '<td align="right"><strong>' . wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_order_currency() ) ) . '</strong></td>' .
            '</tr>' ;
            
    }

    return $output;

  }

}



function vat_14() {
  return 'VAT at 14%';
}
add_filter( 'woocommerce_countries_tax_or_vat', 'vat_14',1 );

function sv_change_email_tax_label( $label ) {
    $label = '';
    return $label;
}
add_filter( 'woocommerce_countries_ex_tax_or_vat', 'sv_change_email_tax_label' );


add_filter( 'pdf_template_order_totals', 'kvd_get_pdf_order_totals',999,2 );


add_filter( 'wc_additional_variation_images_main_images_class', 'variation_swap_main_image_class' );

function variation_swap_main_image_class() {
  return '#product-img-slider ul.slides';
}

add_filter( 'wc_additional_variation_images_gallery_images_class', 'variation_swap_gallery_image_class' );

function variation_swap_gallery_image_class() {
  return '#product-img-nav ul.slides';
}

add_filter( 'wc_additional_variation_images_custom_swap', '__return_true' );
add_filter( 'wc_additional_variation_images_custom_reset_swap', '__return_true' );
add_filter( 'wc_additional_variation_images_custom_original_swap', '__return_true' );
add_filter( 'wc_additional_variation_images_get_first_image', '__return_true' );


// Allow SVG's
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

// Page Excerpts
add_action( 'init', 'my_add_excerpts_to_pages' );
  function my_add_excerpts_to_pages() {
    add_post_type_support( 'page', 'excerpt' );
} 

?>