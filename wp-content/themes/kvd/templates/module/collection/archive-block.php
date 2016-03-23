<article class="collection-unit clearfix">
	<div class="collection-unit__row">
		<div class="collection-unit__info">
			<div class="collection-unit__heading">
				<h2 class="heading__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			</div>
			<div class="collection-unit__content">
				<?php the_excerpt(); ?>

				<a href="<?php the_permalink(); ?>" class="link-underline read-more">View</a>

			</div>
		</div>
		<div class="collection-unit__image">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('full',array("class"=>"img-responsive")); ?></a>
		</div>
		
	</div>
</article>