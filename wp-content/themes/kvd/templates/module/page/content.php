
<div class="page-content__content">
	<?php global $post; ?>
	<?php $heading = get_post_meta( get_the_ID(), '_kvd_about_faq_title', true ); ?>
	<?php $entries = get_post_meta( get_the_ID(), '_kvd_about_repeat_group', true ); ?>
	
	<div class="content-description">
		<div class="content-description__text">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>    
					<?php the_content(); ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>

		<?php if ($post->ID === 626) { ?>
			<p class="lead">
				<?php echo$heading; ?>
			</p>
		
			<div class="content-description__faq">
				
				<div id="accordion" role="tablist" aria-multiselectable="true">
					<?php $count = 0; ?>
					<?php foreach ( (array) $entries as $key => $entry ) { $count++;

					    $img = $title = $answer = '';

					    if ( isset( $entry['title'] ) )
					        $title = esc_html( $entry['title'] );

					    if ( isset( $entry['answer'] ) )
					        $answer = wpautop( $entry['answer'] );

						if ( isset( $entry['title'] ) ) { ?>

						  <div class="panel panel-default">
						    <div class="panel-heading" role="tab">
						      <h5 class="panel-title">
						        <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#acc<?php echo $count; ?>" aria-expanded="true" aria-controls="<?php echo $title; ?>">
						          <?php echo $title; ?><i class="icon up icon-accordionarrow"></i>
						        </a>
						      </h5>
						    </div>
						    <div id="acc<?php echo $count; ?>" class="panel-collapse collapse" role="tabpanel">
						      	<?php echo $answer; ?>
						    </div>
						  </div>
					    <?php };
					} ?>

				</div>
			</div>
		<?php } ?>
	</div>
</div>
<?php // Content Block with CTA ?>