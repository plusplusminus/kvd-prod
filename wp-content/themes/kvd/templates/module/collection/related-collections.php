<?php global $kvd_theme; ?>
<?php global $kvd; ?>

<?php $class="collections-sec--dark"; ?>

<div class="collections-sec <?php echo $class; ?>">
  <div class="main cf">
      <?php if ( $kvd_theme->latest_content_type('collections','',2)->have_posts() ) { ?>
        
        
        <?php while ( $kvd_theme->latest_content_type('collections','',2)->have_posts()) { $kvd_theme->latest_content_type('collections','',2)->the_post(); ?>

          <div class="collections-sec-list tac">
            
              <div class="collections-sec-img">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                  <?php the_post_thumbnail('large',array('class'=>'img-responsive')); ?>
                </a>
              </div>
              <div class="coll-link-sect">
                <div class="product-dtl-link js-cat-link">
                  <a href="<?php echo get_permalink($kvd['collobarations_page']); ?>" data-type="Collections" title="View all Collections">
                    Collections <span class="arrow-right"></span>
                  </a>
                </div>
                
                <a class="link-underline" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                </div>
            </div>
         
        <?php } ?> 

             
      <?php } else { ?>

      <?php } ?>
  </div>
</div>