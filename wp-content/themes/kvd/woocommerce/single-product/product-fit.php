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

$description = get_post_meta($post->ID,'_kvd_product_info_description',1);
$title = get_post_meta($post->ID,'_kvd_product_info_title',1);

if (empty($description)) return;
?>

<li class="fit-section">
	<span class="product-info-label subTitleB"><?php echo $title; ?></span> 
	<span id="like-accordion" class="product-info-value" tabindex="3">
		<span>Show more</span> 
		<i></i>
	</span>
 	<div class="fit-content" style="display: none;">
  		<?php echo apply_filters( 'the_content', $description ) ?>
 	</div>
</li>
