<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<?php global $kvd; ?>
		<?php global $kvd_theme; ?>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <title><?php wp_title(''); ?></title>
	    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
	    
	    <!-- Favicons -->
		<link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/favicon-194x194.png" sizes="194x194">
		<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/manifest.json">
		<link rel="mask-icon" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/safari-pinned-tab.svg" color="#222222">
		<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/favicon.ico">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-TileImage" content="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/mstile-144x144.png">
		<meta name="msapplication-config" content="<?php echo get_stylesheet_directory_uri();?>/assets/img/favicons/browserconfig.xml">
		<meta name="theme-color" content="#ffffff">
	    
		<!-- Facebook APP -->
		<script>
		  window.fbAsyncInit = function() {
		    FB.init({
		      appId      : '1788251904737075',
		      xfbml      : true,
		      version    : 'v2.5'
		    });
		  };

		  (function(d, s, id){
		     var js, fjs = d.getElementsByTagName(s)[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement(s); js.id = id;
		     js.src = "//connect.facebook.net/en_US/sdk.js";
		     fjs.parentNode.insertBefore(js, fjs);
		   }(document, 'script', 'facebook-jssdk'));
		</script>
	    <?php wp_head(); ?>

	    

		<script>
		 (function(d) {
		   var config = {
		     kitId: 'fju0vxd',
		     scriptTimeout: 3000,
		     async: true
		   },
		   h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
		 })(document);
		</script>

	</head>

	<body <?php body_class('home'); ?>>
		<div id="page" class="site">
			<header id="header" class="header-top">
				<div class="nav-logo">
					<div class="container-fluid">
						<div class="logo">
							<?php if ( ( '' != $kvd['site_logo']['url'] ) ) {
						 		$logo_url = $kvd['site_logo']['url'];
						 		$logo_small = $kvd['site_mini_logo']['url'];
								$site_url = get_bloginfo('url');
								$site_name = get_bloginfo('name');
								$site_description = get_bloginfo('description');
								if ( is_ssl() ) $logo_url = str_replace( 'http://', 'https://', $logo_url );
								echo '<a class="big-logo" href="' . esc_url( $site_url ) . '" title="' . esc_attr( $site_description ) . '"><img data-pin-no-hover="true" src="'.$logo_url.'" alt="'.esc_attr($site_name).'"/></a>' . "\n";
								echo '<a class="small-logo" href="' . esc_url( $site_url ) . '" title="' . esc_attr( $site_description ) . '"><img data-pin-no-hover="true" src="'.$logo_small.'" alt="'.esc_attr($site_name).'"/></a>' . "\n";
								
							} // End IF Statement */
							?>
							<div class="menu-icon">Menu</div>
						</div>
						<div class="right-header">
							<nav class="enumenu_container">
								
								<div class="small-menu-container">
									<div class="close-btn"><a href="#" title="close">Menu</a></div>
									<div class="menu-one">
										<?php main_nav('main-nav'); ?>
									</div>

									<?php main_nav('secondary-nav','sm-menulink-gen sm-menulink cf mobile-only'); ?>

								</div>
								<div class="mobile-search-currency cf">
									<div class="mobile-search">
										<div class="head-search"><a class="open-search" title="Search" href="javascript:void(0)">Search</a></div>
									</div>
									<div class="mobile-currency">
										<div class="head-currency open-currency">
											<div class="dis-table-cell"><a class="link" href="javascript:void(0)" title="<?php $kvd_theme->get_current_currency(); ?>"><?php $kvd_theme->get_current_currency(); ?></a></div>
										</div>
									</div>
								</div>
							</nav>
							<div class="cart-search">
								<?php main_nav('secondary-nav','sm-menulink-gen sm-menulink cf desktop-only'); ?>
								<div class="head-cart">
									<ul class="sm-menulink cf">
										<li><a id="show-cart" class="js-cart" href="javascript:{}" title="My Bag">My Bag <?php echo sprintf (_n( '(%d)', '(%d)', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ); ?></a></li>
									</ul>
								</div>
								<div class="head-search"><a class="open-search" href="javascript:void(0)" title="Search">Search</a></div>
								<div class="head-currency"><a class="link open-currency" href="javascript:void(0)" title="<?php $kvd_theme->get_current_currency(); ?>"><?php $kvd_theme->get_current_currency(); ?></a> </div>
								
								<?php the_widget( 'WooCommerce_Ultimate_Multi_Currency_Suite_Widget', 'title=' ); ?>
									
								<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>

								
								<!-- Search Hidden -->
								<div class="shopping-cart-search search-cart-hidden cf">
									<div class="search-input-close">
										<form method="post" action="javascript:{}" class="search-small-cart cf">
											<input type="text" id="search-input" class="search-input" placeholder="Search...">
											<input type="submit" value="" class="search-submit">
										</form>
										<a id="close-search" class="close-search close-currency" href="javascript:{}" title="Close"></a> 
									</div>
									<div id="search-loader" class="search-results-found"></div>
									<div class="search-container"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>


	    