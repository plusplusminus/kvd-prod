	<?php global $kvd; ?>
	<?php global $kvd_theme; ?>
	
	<footer class="site-footer">
	  	<div class="main">
	   		<div class="footer-logo">
	   			<?php if ( ( '' != $kvd['site_white_logo']['url'] ) ) {
					$logo_url = $kvd['site_white_logo']['url'];
					$site_url = get_bloginfo('url');
					$site_name = get_bloginfo('name');
					$site_description = get_bloginfo('description');
					if ( is_ssl() ) $logo_url = str_replace( 'http://', 'https://', $logo_url );
					echo '<a href="' . esc_url( $site_url ) . '" title="' . esc_attr( $site_description ) . '"><img data-pin-no-hover="true" src="'.$logo_url.'" alt="'.esc_attr($site_name).'"/></a>' . "\n";
					
				} // End IF Statement */
				?> 
			</div>
	   		<div class="footer-nav-social cf row">
	   			<?php $menus = array("about-nav","contact-nav","more-nav","connect-nav"); ?>

				<?php foreach ($menus as $menu) { ?>
					<div class="about-nav footer-site-link footer-nav-sociallist col-md-3">
						<?php echo $kvd_theme->footer_menu($menu,'footer_menu'); ?>
					</div>
				<?php } ?>

	   		</div>
	   		
	   		<div class="footer-sign-up">
	   		<div id="mc_embed_signup">
		    	<form class="validate footer-subscribe cf" id="mc-embedded-subscribe-form" action="http://katvanduinen.us2.list-manage.com/subscribe/post?u=2524449608a2d50a7658028c6&amp;id=4c1d53161a" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" target="_blank" novalidate>
		     		<label for="mce-EMAIL">Sign up for our newsletter</label>
		     		<input type="email" id="mce-EMAIL" class="email subscribe-input" placeholder="Enter your email address" name="EMAIL">
		     		<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_2524449608a2d50a7658028c6_4c1d53161a" tabindex="-1" value=""></div>
		     		<input type="submit" class="sm-submit" value="" name="subscribe" id="mc-embedded-subscribe">
		    	</form>
		    	</div>
	   		</div>
	   		<div class="successmsg">Thank you for Subscribing!</div>
	   		<div class="footer-bottom cf">
	    		<?php $footer_text1 =  $kvd['footer_link_1_text']; ?>
	    		<?php $footer_link1 =  $kvd['footer_link_1']; ?>
	    		<?php $footer_text2 =  $kvd['footer_link_2_text']; ?>
	    		<?php $footer_link2 =  $kvd['footer_link_2']; ?>
	    		<?php $footer_icon_text =  $kvd['footer_icon_text']; ?>
	    		<?php $footer_icon_link =  $kvd['footer_icon_link']; ?>
	    		<div class="footer-col3 footer-bottom-link"> 
	    			<a href="<?php echo get_page_link($footer_link1); ?>" title="<?php echo $footer_text1; ?>"><?php echo $footer_text1; ?></a> &nbsp;|&nbsp; <a target="_blank" href="<?php echo get_page_link($footer_link2); ?>" title="<?php echo $footer_text2; ?>"><?php echo $footer_text2; ?></a> 
	    		</div>
	    		<div class="footer-col3 footer-africa-map"> <a data-toggle="modal" data-target="#country-modal" href="<?php echo get_page_link($footer_icon_link); ?>"><?php echo $footer_icon_text; ?></a></div>
	    		<div class="footer-col3 footer-copyright"> All rights reserved &copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.</div>
	   		</div>
	  	</div>
	 </footer>

	 <div id="product-modal" class="modal fade">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	 	 	<button type="button" class="modal-close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-largeclose"></i></button>
	      <div class="js-content modal-body"></div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- Button trigger modal -->

	<?php $kvd_theme->country_modal(); ?>

		 <div id="home-video-modal" class="modal fade">
		  <button type="button" class="modal-close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-largeclose"></i></button>
		  <div class="modal-dialog modal-lg" role="document">
		    <div class="modal-content">
		      <div class="modal-body">
		      <?php $entries = get_post_meta( get_the_ID(), '_kvd_slides_repeat_group', true );

				foreach ( (array) $entries as $key => $entry ) { ;

				    $embed = '';

				    if ( isset( $entry['embed'] ) )
				    	$embed = ( $entry['embed'] );

					echo wp_oembed_get($embed);

				} ?>

		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->


    <?php wp_footer(); ?>
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/ScrollMagic.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.css"/>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick-theme.css"/>
	<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.min.js"></script>
	<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script>
	
  </body>

</html>
