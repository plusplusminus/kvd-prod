<?php global $post; ?>
<?php $image = get_post_meta( get_the_ID(), '_kvd_about_slide_img_id', true ); ?>
<?php $image_url = wp_get_attachment_image_src( $image , 'full');?>
<div class="item"> 

  <div class="carousel-image" style="background-image: url('<?php echo $image_url[0]; ?>')"></div>
    <div class="ethical-approach">
      <div class="common-text">
        <h2><?php the_title(); ?></h2>
        <?php the_excerpt(); ?>
        <a href="<?php the_permalink(); ?>" class="link-underline read-more" title="Read More">Read More</a>
      </div>
    </div>
</div>