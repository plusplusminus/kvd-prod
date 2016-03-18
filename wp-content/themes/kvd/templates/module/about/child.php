<?php /* About Blocks  */ ?>

<div class="about__child">
	<div class="child__image">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail('large',array('class','img-responsive')); ?>
		</a>
	</div>
	<div class="child__header">
		<a href="<?php the_permalink(); ?>">
			<h3 class="child__title"><?php the_title(); ?></h3>
		</a>
	</div>
</div>

