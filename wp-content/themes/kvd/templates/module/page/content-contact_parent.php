
<div class="page-content__content">
	<?php global $post; ?>
	
	<div class="content-description">
		<div class="content-description__text">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>  
					<?php the_content(); ?>
				<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_query(); ?>
		</div>
	</div>

	<div class="content-children">
		<?php $args = array('post_type'=>'page','post_parent'=>$post->ID,'orderby'=>'menu_order', 'order' => 'asc'); ?>
		<?php $query = new WP_Query($args); ?>

		<div id="accordion" role="tablist" aria-multiselectable="true">
		<?php if (  $query->have_posts() ) : ?>

			<?php while (  $query->have_posts() ) :  $query->the_post(); ?>  
				
				<?php 
				if ($post->ID === 660 ) { ?>

					<div class="panel panel-default">
						<?php get_template_part('templates/module/page/header_child'); ?>
					
						<div id="acc<?php echo $post->ID; ?>" class="panel-collapse collapse" role="tabpanel">
							<?php get_template_part('templates/module/page/flagship'); ?>
						</div>
					</div>		

				<?php } else if($post->ID === 856) { ?>

					<div class="panel panel-default">
						<?php get_template_part('templates/module/page/header_child'); ?>
					
						<div id="acc<?php echo $post->ID; ?>" class="panel-collapse collapse" role="tabpanel">
							<?php get_template_part('templates/module/page/flagship'); ?>
						</div>
					</div>	

				<?php } else if ($post->ID === 665) { ?>

					<div class="panel panel-default">
						<?php get_template_part('templates/module/page/header_child'); ?>
					
						<div id="acc<?php echo $post->ID; ?>" class="panel-collapse collapse" role="tabpanel">
							<?php get_template_part('templates/module/page/stockists'); ?>
						</div>
					</div>

				<?php } else { ?>

					<div class="panel panel-default">
						<?php get_template_part('templates/module/page/header_child'); ?>
						<div id="acc<?php echo $post->ID; ?>" class="panel-collapse collapse" role="tabpanel">
							<?php get_template_part('templates/module/page/contact-us'); ?>
						</div>
					</div>

				<?php }	?>


			<?php endwhile; ?>
		<?php endif; ?>
		</div><!--/#accordian-->


	</div>

</div>
<?php // Content Block with CTA ?>