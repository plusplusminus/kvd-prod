<?php $entries = get_post_meta( get_the_ID(), '_kvd_stockists_repeat_group', true ); ?>

<div class="content-description__stockists">
			
	<?php foreach ( (array) $entries as $key => $entry ) { ;

	    $img = $name = $country = $link = '';

	    if ( isset( $entry['name'] ) )
	        $name = esc_html( $entry['name'] );

	    if ( isset( $entry['details'] ) )
	        $details = wpautop( $entry['details'] );

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
					<span class="stockist__country"><?php echo $country; ?></span>
					<div class="stockist__details"><?php echo $details; ?></div>
				</div><!--/.stockist__header-->
			<div class="clearfix"></div>
		</div><!--/.stockist-->
	    <?php };
	} ?>

</div>