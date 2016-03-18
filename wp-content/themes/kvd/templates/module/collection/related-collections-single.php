<?php global $kvd_theme; ?>
<?php global $kvd; ?>

<footer class="page-footer--singlecoll">
  
  <div class="section__title"><h4>Discover More</h4></div>

    <div class="singlecoll__children">

    <?php if ( $kvd_theme->latest_content_type('collections','',3)->have_posts() ) { ?>
      
      <?php while ( $kvd_theme->latest_content_type('collections','',3)->have_posts()) { $kvd_theme->latest_content_type('collections','',3)->the_post(); ?>

        <div class="singlecoll__child">
          
          <div class="child__image">
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
              <?php the_post_thumbnail('large',array('class'=>'img-responsive')); ?>
            </a>
          </div>
          
          <div class="child__header">                
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><h3 class="child__title"><?php the_title(); ?></h3></a>
          </div>
        
        </div>
      
      <?php } ?> 
           
    <?php } ?>

    </div>

  </div>

</footer>