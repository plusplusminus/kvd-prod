<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();

$feature = $product->get_image_id();

if ( $attachment_ids ) {
	?>
	<?php

		foreach ( $attachment_ids as $attachment_id ) {

			if ($attachment_id === $feature) continue;
			
			$image = wp_get_attachment_image_src( $attachment_id,'full' );
			

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="product-image"><img src="%s" class="img-responsive"/></div>', $image[0]), $attachment_id, $post->ID);


		}

	?>
	<?php
}
