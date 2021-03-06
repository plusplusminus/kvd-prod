<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();

$classes[] = 'col-md-4';
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] ) {
	$classes[] = 'first';
}
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	$classes[] = 'last';
}

?>
<div <?php post_class( $classes ); ?>>

	<?php //do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<div class="kvd-product"> 
	  <div class="product-img dis-table">
	    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="dis-table-cell">
	        <?php the_post_thumbnail('large'); ?>
	    </a>
	  </div>
	  <div class="product-desc-link">
	    <div class="product-name">
	      <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
	    </div>
	    <div class="product-price"><?php echo $product->get_price_html(); ?></div>
	  </div>
	</div>

	<?php

		/**
		 * woocommerce_after_shop_loop_item hook
		 *
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
	//	do_action( 'woocommerce_after_shop_loop_item' );

	?>

</div>