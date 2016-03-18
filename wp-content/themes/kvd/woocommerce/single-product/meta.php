<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

?>

<?php do_action( 'woocommerce_product_meta_start' ); ?>

<?php get_template_part('woocommerce/single-product/collection'); ?>

<li class="collestion-auto-fill">
<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

	<span class="sku_wrapper product-info-label subTitleB"><?php _e( 'SKU:', 'woocommerce' ); ?></span>
	<span class="sku product-info-value" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></span>

<?php endif; ?>
</li>

<?php do_action( 'woocommerce_product_meta_end' ); ?>


