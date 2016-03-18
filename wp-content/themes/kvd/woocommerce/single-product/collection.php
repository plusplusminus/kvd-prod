<?php
/**
 * Single Product Info
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;


$collection = get_post_meta($product->id,'_kvd_collection_name',1);

if (empty($collection)) return;
?>

<li class="collection-section">
	<span class="product-info-label subTitleB">Collection</span> 
	<span class="product-info-value">
		<a href="<?php echo get_permalink($collection); ?>"><?php echo get_the_title($collection); ?></a>
	</span>
</li>
