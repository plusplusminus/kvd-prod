
<div class="page-content__content">
	<?php global $post; ?>
	<?php $entries = get_post_meta( get_the_ID(), '_kvd_page_repeat_group', true ); ?>
	
	<div class="content-description">
		<div class="content-description__text">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>    
					<?php the_content(); ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
		
			<div class="content-description__faq">
				
				<div id="accordion" role="tablist" aria-multiselectable="true">
					<?php $count = 0; ?>
					<?php foreach ( (array) $entries as $key => $entry ) { $count++;

					    $heading = $content = '';

					    if ( isset( $entry['heading'] ) )
					        $heading = esc_html( $entry['heading'] );

					    if ( isset( $entry['content'] ) )
					        $content = wpautop( $entry['content'] );

						if ( isset( $entry['heading'] ) ) { ?>

						  <div class="panel panel-default">
						    <div class="panel-heading" role="tab">
						      <h5 class="panel-title">
						        <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#acc<?php echo $count; ?>" aria-expanded="true" aria-controls="<?php echo $heading; ?>">
						          <?php echo $heading; ?><i class="icon up icon-accordionarrow"></i>
						        </a>
						      </h5>
						    </div>
						    <div id="acc<?php echo $count; ?>" class="panel-collapse collapse" role="tabpanel">
						      	<?php echo $content; ?>
						    </div>
						  </div>
					    <?php };
					} ?>

				</div>
			</div>
	</div>
</div>
<?php // Content Block with CTA ?>