<?php $googlemap = get_post_meta( get_the_ID(), '_kvd_about_google_map', true ); ?>

<div class="content-description">
	<div class="content-description__text">

		<div class="flagship__row">
			<div class="flagship__img">
				<?php the_post_thumbnail('large',array('class','img-responsive')); ?>
			</div>
			<div class="flagship__content">
				<?php the_content(); ?>
				<a target="_blank" href="<?php echo $googlemap ?>" class="flagship__link link-underline read-more">View Map</a>
			</div>
		</div>
		
	</div>
</div>