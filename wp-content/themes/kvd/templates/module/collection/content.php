<?php
$thecontent = get_the_content();
if(!empty($thecontent)) { ?>

	<div class="page-content__content">
		<?php global $post; ?>
		<div class="content-description">
			<div class="content-description__text">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>    
						<?php the_content(); ?>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php // Content Block with CTA ?>

<?php } ?>