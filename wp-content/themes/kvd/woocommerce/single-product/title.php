<?php
/**
 * Single Product title
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$terms = get_the_terms( get_the_ID(), 'product_cat' );
$term_link = get_term_link( $terms[0] );
// If there was an error, continue to the next term.
if ( is_wp_error( $term_link ) ) {
  return;
}

?>

<div class="category-name">
	<a href="<?php echo esc_url( $term_link ); ?>" data-type="<?php echo $terms[0]->name; ?>" title="View all <?php echo $terms[0]->name; ?>"><?php echo $terms[0]->name; ?></a>
</div>
<div class="kvd-prodname-price cf">
    <h1 itemprop="name" class="kvd-product-name"><?php the_title(); ?></h1>

	<div class="price-div" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

		<?php echo $product->get_price_html(); ?>

		<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
		<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
		<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

	</div>
</div>


