
<?php $entries = get_post_meta( get_the_ID(), '_kvd_slides_repeat_group', true ); ?>

<div class="banner-section">

 	<div class="owl-carousel">

		

				<?php foreach ( (array) $entries as $key => $entry ) { ;

				    $img = $name = $country = $video_slide = $embed = $link = '';

				    if ( isset( $entry['title'] ) )
				        $title = esc_html( $entry['title'] );

				    if ( isset( $entry['label'] ) )
				        $label = ( $entry['label'] );

				    if ( isset( $entry['url'] ) )
				        $url = ( $entry['url'] );

				    if ( isset( $entry['embed'] ) )
				    	$embed = ( $entry['embed'] );

				    if ( isset( $entry['image'] ) ) {
					    $img = ($entry['image_id'] );
					    $img_url = wp_get_attachment_image_src( $img , 'full');
					}?>
					<div class="slider-item" style="background-image:url(<?php echo($img_url[0]); ?>)">
						<div class="dis-table">
							<div class="dis-table-cell">
								<div class="banner-text">
									<?php if ( !empty($embed)){ ?>
										<p><?php echo $title; ?></p>
										<?php 
											echo sprintf('<div class="wistia_embed wistia_async_%s popover=true popoverContent=html" style="display:inline-block; white-space:nowrap;">
															<a href="#" class="link-underline video__btn js-watch" >%s<i class="icon icon-play-small"></i></a>
														</div>',$embed,$label);
										?>
									<?php } else { ?>
										<p><?php echo $title; ?></p>
										<a class="link-underline" title="<?php echo $label; ?>" href="<?php echo $url; ?>"><?php echo $label; ?></a>
									<?php }; ?>
								</div>
							</div>
						</div>
					</div>

				<?php } ?>

		</div>

</div>