<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

$related = $product->get_related( $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => 3,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->id )
) );

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>
</div><div class="bt"></div><div class="container">
<section class="featured-products">
	<div class="main">
	    <div class="product-container">
	        <h4 class="tac"><?php _e( 'Related Products', 'woocommerce' ); ?></h4>
	        <div class="product-list-container cf">

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php
		              $product = new WC_Product( get_the_ID() );
		              $terms = get_the_terms( get_the_ID(), 'product_cat' );
		              $term_link = get_term_link( $terms[0] );
		              // If there was an error, continue to the next term.
		              if ( is_wp_error( $term_link ) ) {
		                  continue;
		              }
		            ?>
		            <div class="kvd-product"> 
		              <div class="product-img dis-table">
		                <div class="dis-table-cell">
		                  <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
		                    <?php the_post_thumbnail('large'); ?>
		                  </a>
		                </div>
		              </div>
		              <div class="product-desc-link">
		                <div class="js-cat-link product-dtl-link">
		                  <a href="<?php echo esc_url( $term_link ); ?>" data-type="<?php echo $terms[0]->name; ?>" title="View all <?php echo $terms[0]->name; ?>">
		                    <?php echo $terms[0]->name; ?><span class="arrow-right"></span>
		                  </a>
		                </div>
		                <div class="product-name">
		                  <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
		                </div>
		                <div class="product-price"><?php echo $product->get_price_html(); ?></div>
		              </div>
		            </div>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			</div>
		</div>
	</div>
</section>

<?php endif;

wp_reset_postdata();
