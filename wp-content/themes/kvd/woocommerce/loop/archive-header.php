<header class="archive-header">
	<div class="archive-header__image">
		<?php if ( is_product_category() ){
		    global $wp_query;
		    $cat = $wp_query->get_queried_object();
		    $thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
		    $image = wp_get_attachment_url( $thumbnail_id,'full');

		    if ( !empty($image) ) { 
				echo '<img class="img-responsive" src="' . $image . '" alt="" />';
			} else { ?>
		    	<img src="<?php bloginfo('stylesheet_directory')?>/assets/img/header-about.jpg"/>
			<?php }
		}
		?>
	</div>
	<div class="archive-header__content">
		<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
	</div>
</header>