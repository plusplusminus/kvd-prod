<?php
class kvdTheme {

	public function __construct() {

		$this->prefix = '_kvd_'; 
		$this->_cache = array();
		$this->invType = array();
		$this->type = '';

		add_action( 'wp_ajax_kvd_ajax_search_products', array($this,'kvd_ajax_search_products'));
		add_action( 'wp_ajax_nopriv_kvd_ajax_search_products', array($this,'kvd_ajax_search_products') );

		
		
	}

	public function get_current_currency() {
		
		global $woocommerce_ultimate_multi_currency_suite;
		$default_currency = get_woocommerce_currency();

		
		if (empty($woocommerce_ultimate_multi_currency_suite->settings->session_currency)){ // if no currency stored in session
			$currency = $default_currency;
		} else {
			$currency = $woocommerce_ultimate_multi_currency_suite->settings->session_currency;
		}
		
		$currency_data = $woocommerce_ultimate_multi_currency_suite->settings->get_currency_data(); // get all data on all currencies
		echo $currency.' '.$currency_data[$currency]['symbol'];

	}

	public function get_current_country_currency($code) {


		
		global $woocommerce_ultimate_multi_currency_suite;
		
		$new_currency = $woocommerce_ultimate_multi_currency_suite->settings->get_country_currency($code);

		$currency_data = $woocommerce_ultimate_multi_currency_suite->settings->get_currency_data(); // get all data on all currencies
		
		return array('code'=>$new_currency,'symbol'=>$currency_data[$new_currency]['symbol']);

	}

	public function country_modal() {

		?>
		<div id="country-modal" class="modal fade country-modal">
		  	<div class="modal-dialog modal-sm" role="document">
			  	
			    <div class="modal-content">
			      <div class="js-content modal-body">
			      		<button type="button" class="modal__close--top" data-dismiss="modal" aria-hidden="true"><i class="icon icon-largeclose"></i></button>
				      	<h3 class="country-modal__title">Select your country</h3>
				      	<?php $location_info = WC_Geolocation::geolocate_ip(WC_Geolocation::get_ip_address()); ?>
				      	<?php $country_name = WC()->countries->countries[ $location_info["country"] ]; ?>
				      	<?php $currency = $this->get_current_country_currency($location_info["country"]); ?>
				      	
						
						<span class="country__current js-country-reveal">You are visiting from <span class="country__current--active"><?php echo $country_name; ?> (<?php echo $currency['code'].' '.$currency['symbol'] ?>) <i class="icon icon-caret"></i></span></span>
						<div class="country__select">
							<ul class="country__list-group">
								<li data-currency="<?php echo $currency['code']; ?>" class="country__list-group__item selected js-country-select"><?php echo $country_name; ?> (<?php echo $currency['code'].' '.$currency['symbol'] ?>)</li>
								<?php 
									$currencies_data_string = get_option('wcumcs_available_currencies');
									$currencies_data = json_decode($currencies_data_string, true);
									foreach ($currencies_data as $currency_code => $currency_data) {
										if ($currency_code != $currency['code'])
											echo sprintf('<li data-currency="%s" class="country__list-group__item js-country-select"> %s (%s %s)</li>',$currency_code,$currency_data['name'],$currency_code,$currency_data['symbol']);
									}
								?>
							</ul>
						</div>
						<div class="country-modal__footer modal-footer">
							<button type="button" class="modal__close--link" data-dismiss="modal" aria-hidden="true">No Thanks</button>
							<button type="button" class="modal__close--btn js-country-save">Save</button>
						</div>
			      	</div>
			    </div>
		  	</div>
		</div>

		<?php
	}

	public function kvd_ajax_search_products() {

		global $swiftype_plugin;

		/* get the search terms entered into the search box */
		$search = sanitize_text_field( $_POST[ 'search' ] );

		$swiftype_plugin->search_swiftype(stripslashes( $search ),array());

		$results = $swiftype_plugin->results();
		$post_ids = array();
		$records = $results['records']['posts'];


		foreach( $records as $record ) {
			$post_ids[] = $record['external_id'];
		}

		/* run a new query including the search string */
		$q = new WP_Query(
			array(
				'post_type'			=> 'product',
				'posts_per_page'	=> 20,
				'post__in'			=> $post_ids
			)
		);
		
		/* store all returned output in here */
		$output = '';
		
		/* check whether any search results are found */
		if( $q->have_posts() ) {
			echo '<div class="search-results-found">'.$q->post_count.' Results Found</div>';
			/* loop through each result */
			while( $q->have_posts() ) : $q->the_post();
			
				/* add result and link to post to output */
				?>
				<div class="header-cart-col-main">
					<div class="product-sm-img">
						<a href="<?php the_permalink(); ?>" title="View <?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
					</div>
					<div class="header-cart-col1">
						<div class="cart-prod-name"><a href="<?php the_permalink(); ?>" title="View <?php the_title(); ?>"><?php the_title(); ?></a></div>
					</div>
				</div>


				<?php
			
			/* end loop */
			endwhile;
		
		/* no search results found */	
		} else {
			
			/* add no results message to output */
			echo '<div class="search-results-found">No Results Found</div>';
			
		} // end if have posts
		
		/* reset query */
		wp_reset_query();
		
		die();
	
	}

	public function set_type($type) {
		$this->type = $type;

		$type == 'collections' ? $this->invType['posttype'] = 'collaborations' : $this->invType['posttype']  = 'collections';
		$type == 'collections' ? $this->invType['type'] = 'collobarations_page' : $this->invType['type']  = 'collections_page';
		$type == 'collections' ? $this->invType['title'] = 'Collaborations' : $this->invType['title']  = 'Collections';
	}


	public function latest_content_type($post_type = 'post',$tag='',$number=1) {


		$query_args = array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $number,
			'tag' => $tag
		);

		if (empty($this->_cache[$post_type.'-latest'])) {
			$this->_cache[$post_type.'-latest'] = new WP_Query( $query_args );
		}

		return $this->_cache[$post_type.'-latest'];


	}

	public function get_child_pages() {
		global $post;
		global $kvd;

		$args = array(
		    'post_type'      => 'page',
		    'posts_per_page' => -1,
		    'post_parent'    => $post->ID,
		    'order'          => 'ASC',
		    'orderby'        => 'menu_order',
		    'post__not_in'   => array($kvd['meet_page'],$kvd['brand_page'])
		 );


		$parent = new WP_Query( $args );

		if ( $parent->have_posts() ) : 
			echo '<div class="section__title"><h4>Continue to discover</h4></div>';
			echo '<div class="about__children">';
		    while ( $parent->have_posts() ) : $parent->the_post();

		      	get_template_part('templates/module/about/child');

		    endwhile;

		    echo '</div>';

		endif; 

		wp_reset_query(); 

	}

	public function kvd_about_slider() {
		global $post;
		global $kvd;

		$args = array(
		    'post_type'      => 'page',
		    'posts_per_page' => -1,
		    'post_parent'    => $kvd['about_page'],
		    'order'          => 'ASC',
		    'orderby'        => 'menu_order',
		    'post__not_in'   => array($kvd['meet_page'],$kvd['brand_page'])
		);


		$parent = new WP_Query( $args );

		if ( $parent->have_posts() ) : 
			
		    while ( $parent->have_posts() ) : $parent->the_post();

		      	get_template_part('templates/module/about/slide');

		    endwhile;

		    

		endif; 

		wp_reset_query(); 

	}

	public function get_discover_pages($current) {
		global $post;
		global $kvd;

		$args = array(
		    'post_type'      => 'page',
		    'posts_per_page' => -1,
		    'post_parent'    => $post->post_parent,
		    'order'          => 'ASC',
		    'orderby'        => 'menu_order',
		    'post__not_in'   => array($current,$kvd['brand_page'])
		 );


		$parent = new WP_Query( $args );

		if ( $parent->have_posts() ) : 
			echo '<div class="section__title"><h4>Continue to discover</h4></div>';
			echo '<div class="about__children">';

		    while ( $parent->have_posts() ) : $parent->the_post();

		      	get_template_part('templates/module/about/child');

		    endwhile;

		    echo '</div>';

		endif; 

		wp_reset_query(); 

	}

	public function get_child_meet() {
		global $post;
		global $kvd;

		$args = array(
		    'post_type'      => 'page',
		    'posts_per_page' => 1,
		    'order'          => 'ASC',
		    'orderby'        => 'menu_order',
		    'post__in' 		 => array($kvd['meet_page'])
		 );


		$parent = new WP_Query( $args );

		if ( $parent->have_posts() ) : 
			echo '<div class="about_children">';
		    while ( $parent->have_posts() ) : $parent->the_post();

		      	get_template_part('templates/module/about/meet');

		    endwhile;

		    echo '</div>';

		endif; 

		wp_reset_query(); 


	}


	public function footer_menu($menu_name='regions-nav',$class="regions-menu") {
	    if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
		$menu_items = wp_get_nav_menu_items($menu->term_id);

			$menu_heading = '<h3>'.$menu->name.'</h3>';
			$menu_list_start = '<ul>';
			$menu_list_end = '</ul>';
			$menu_list = '';

			foreach ( (array) $menu_items as $key => $menu_item ) {
					$title = $menu_item->title;
					$link = $menu_item->url;
					$target = $menu_item->target;


					if ($menu_item->attr_title) {
						$social = '<i class="icon icon-'.$menu_item->attr_title.'"></i>	';
					} else {
						$social = '';
					}

					$menu_list .= sprintf('<li><a target="%s" href="%s" title="%s">%s%s</a></li>',$target,$link,$title,$social,$title);
			}

			return sprintf("%s%s%s%s",$menu_heading,$menu_list_start,$menu_list,$menu_list_end);
		}
		
	}

}