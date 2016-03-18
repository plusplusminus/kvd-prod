<article class="collection-unit clearfix">
	<div class="collection-unit__row">
		<div class="collection-unit__info">
			<div class="collection-unit__heading">
				<h2 class="heading__title"><?php the_title(); ?></h2>
			</div>
			<div class="collection-unit__content">
				<?php the_excerpt(); ?>

				<a href="<?php the_permalink(); ?>" class="link-underline read-more">View</a>

			</div>
		</div>
		<div class="collection-unit__image">
			<?php the_post_thumbnail('full',array("class"=>"img-responsive")); ?>
		</div>
		
	</div>
</article>