
<div class="page-content__content">
	<?php global $post; ?>
	<?php $entries = get_post_meta( get_the_ID(), '_kvd_stockists_repeat_group', true ); ?>
	<?php $googlemap = get_post_meta( get_the_ID(), '_kvd_about_google_map', true ); ?>
	
	<div class="content-description">
		<div class="content-description__text">
			<?php if ( have_posts() ) : ?>
				<?php $count = 0 ?>
				<?php while ( have_posts() ) : the_post(); $count++?>    
					<?php if (is_page( array( 'our-flagship-store', 'johannesburg-store' ) ) ) { ?>
					<div class="flagship__row">
						<div class="flagship__img">
							<?php the_post_thumbnail('large',array('class','img-responsive')); ?>
						</div>
						<div class="flagship__content">
							<?php the_content(); ?>
							<a target="_blank" href="<?php echo $googlemap ?>" class="flagship__link link-underline read-more">View Map</a>
						</div>
					</div>
					<?php } else { ?>
						<?php the_content(); ?>
					<?php } ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>

		<?php if (is_page( 'stockists' ) ) { ?>
		<div class="content-description__stockists">
			
			<?php foreach ( (array) $entries as $key => $entry ) { ;

			    $img = $name = $country = $link = '';

			    if ( isset( $entry['name'] ) )
			        $name = esc_html( $entry['name'] );

			    if ( isset( $entry['details'] ) )
			        $details = esc_html( $entry['details'] );

			    if ( isset( $entry['img'] ) ) {
				    $img = wp_get_attachment_image( $entry['img_id'], 'thumb', null, array(
				        'class' => 'stockist--img',
				    ) );
			    }

			    if ( isset( $entry['country'] ) )
			        $country = esc_html( $entry['country'] );

			    if ( isset( $entry['link'] ) )
			        $link = $entry['link'];

				if ( isset( $entry['name'] ) ) { ?>

				<div class="stockist">
						<?php echo $img ?>
						<div class="stockist__header">
							<h5 class="stockist__title"><a class="stockist__title--link" href="<?php echo $link; ?>" target="_blank"><?php echo $name; ?></a></h5>
							<span class="stockist__details"><?php echo $details; ?></span>
							<span class="stockist__country"><?php echo $country; ?></span>
						</div><!--/.stockist__header-->
					<div class="clearfix"></div>
				</div><!--/.stockist-->
			    <?php };
			} ?>

		</div>
		<?php } ?>
	</div>
</div>
<?php // Content Block with CTA ?>