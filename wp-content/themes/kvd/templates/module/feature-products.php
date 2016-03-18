<?php global $kvd_products; ?>

<?php if ( $kvd_products->featured_products_query()->have_posts() ) { ?>
  <section class="featured-products">
    <div class="main">
      <div class="product-container">
        <h4 class="tac">Featured Products</h4>
        <div class="product-list-container cf">
  
          <?php while ( $kvd_products->featured_products_query()->have_posts() ) { $kvd_products->featured_products_query()->the_post(); ?>
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
          <?php } ?> 
        </div>
      </div>
    </div>
  </section>
<?php } else { ?>
    // no posts found
<?php } wp_reset_postdata(); ?>

     