<?php
class kvdAdminFunctions {

	public function __construct() {

		add_action( 'cmb2_init', array($this,'kvd_custom_meta'));
		add_action('init',array($this,'kvd_custom_posts'));
		add_action('init',array($this,'kvd_taxonomies'));

		add_action( 'p2p_init', array($this,'kvd_connection_types'));

	}

	public function kvd_taxonomies() {

		
	}

    public function kvd_custom_posts()
	{
		register_post_type(	'collections', 
			array(	
				'label' 			=> __('Collections'),
				'labels' 			=> array(	'name' 					=> __('Collections'),
												'singular_name' 		=> __('Collection'),
												'add_new' 				=> __('Add New'),
												'add_new_item' 			=> __('Add New Collection'),
												'edit' 					=> __('Edit'),
												'edit_item' 			=> __('Edit Collection'),
												'new_item' 				=> __('New Collection'),
												'view_item'				=> __('View Collection'),
												'search_items' 			=> __('Search Collections'),
												'not_found' 			=> __('No Collection found'),
												'not_found_in_trash' 	=> __('No Collection found in trash')	),
				'public' 			=> true,
				'can_export'		=> true,
				'show_ui' 			=> true, 
				'_builtin' 			=> false, 
				'_edit_link' 		=> 'post.php?post=%d',
				'capability_type' 	=> 'post',
				'menu_icon' 		=> 'dashicons-admin-home',
				'hierarchical' 		=> false,
				'has_archive' 		=> true,
				'rewrite' 			=> array(	"slug" => "collection"	), 
				'query_var' 		=> "collection", 
				'supports' 			=> array(	'title',																
												'editor',
												'excerpt',
												'thumbnail'
												),
				'show_in_nav_menus'	=> true ,
				'taxonomies'		=> array()
			)
		);
		
		register_post_type(	'collaborations', 
			array(	
				'label' 			=> __('Collaborations'),
				'labels' 			=> array(	'name' 					=> __('Collaborations'),
												'singular_name' 		=> __('Collaboration'),
												'add_new' 				=> __('Add New'),
												'add_new_item' 			=> __('Add New Collaboration'),
												'edit' 					=> __('Edit'),
												'edit_item' 			=> __('Edit Collaboration'),
												'new_item' 				=> __('New Collaboration'),
												'view_item'				=> __('View Collaboration'),
												'search_items' 			=> __('Search Collaborations'),
												'not_found' 			=> __('No Collaboration found'),
												'not_found_in_trash' 	=> __('No Collaboration found in trash')	),
				'public' 			=> true,
				'can_export'		=> true,
				'show_ui' 			=> true, 
				'_builtin' 			=> false, 
				'_edit_link' 		=> 'post.php?post=%d',
				'capability_type' 	=> 'post',
				'menu_icon' 		=> 'dashicons-admin-home',
				'hierarchical' 		=> false,
				'has_archive' 		=> true,
				'rewrite' 			=> array(	"slug" => "collaboration"	), 
				'query_var' 		=> "collaboration", 
				'supports' 			=> array(	'title',																
												'editor',
												'thumbnail'
												),
				'show_in_nav_menus'	=> false ,
				'taxonomies'		=> array()
			)
		);

		

	}

	public function kvd_custom_meta() {
		$prefix = '_kvd_';

		$home_meta = new_cmb2_box( array(
			'id'            => $prefix . 'home_slider_metabox',
			'title'         => __( 'Home Slides', 'cmb2' ),
			'object_types'  => array( 'page' ), // Post type
			'show_on' => array( 'key' => 'front-page', 'value' => '' ),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$group_slides = $home_meta->add_field( array(
		    'id'          => $prefix . 'slides_repeat_group',
		    'type'        => 'group',
		    'description' => __( 'Add Slides', 'cmb' ),
		    'options'     => array(
		        'group_title'   => __( 'Slide {#}', 'cmb' ), // since version 1.1.4, {#} gets replaced by row number
		        'add_button'    => __( 'Add Another Slide', 'cmb' ),
		        'remove_button' => __( 'Remove Slide', 'cmb' ),
		        'sortable'      => true, // beta
		    ),
		) );

		$home_meta->add_group_field( $group_slides, array(
		    'name' => 'Image',
		    'id'   => 'image',
		    'type' => 'file',
		) );

		$home_meta->add_group_field( $group_slides, array(
		    'name' => 'Title',
		    'id'   => 'title',
		    'type' => 'text',
		) );

		$home_meta->add_group_field( $group_slides, array(
		    'name' => 'Button label',
		    'id'   => 'label',
		    'type' => 'text',
		) );

		$home_meta->add_group_field( $group_slides, array(
		    'name' => 'Button URL',
		    'id'   => 'url',
		    'type' => 'text',
		    'description' => 'NOT required for Video slides',
		) );

		$home_meta->add_group_field( $group_slides, array(
		    'name' => 'Wistia Video ID',
		    'id'   => 'embed',
		    'type' => 'text',
		    'description' => 'ONLY required for Video slides',
		) );

		$brand_meta = new_cmb2_box( array(
			'id'            => $prefix . 'brand_metabox',
			'title'         => __( 'Brand Metabox', 'cmb2' ),
			'object_types'  => array( 'page' ), // Post type
			'show_on' => array( 'key' => 'front-page', 'value' => '' ),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$brand_meta->add_field( array(
			'name'       => __( 'Brand Title', 'cmb2' ),
			'desc'       => __( 'Brand section title...', 'cmb2' ),
			'id'         => $prefix . 'page_brand_title',
			'type'       => 'text',
		) );

		$brand_meta->add_field( array(
			'name'       => __( 'Brand Sub Title', 'cmb2' ),
			'desc'       => __( 'Brand section subtitle', 'cmb2' ),
			'id'         => $prefix . 'page_brand_subtitle',
			'type'       => 'text',
		) );

		$brand_meta->add_field( array(
			'name'       => __( 'Brand Description', 'cmb2' ),
			'desc'       => __( 'Brand section description', 'cmb2' ),
			'id'         => $prefix . 'page_brand_description',
			'type'       => 'text',
		) );

		$brand_meta->add_field( array(
			'name'       => __( 'Brand Description', 'cmb2' ),
			'desc'       => __( 'Brand section description', 'cmb2' ),
			'id'         => $prefix . 'page_brand_description',
			'type'       => 'text',
		) );

		$brand_meta->add_field( array(
			'name'       => __( 'Brand Link', 'cmb2' ),
			'desc'       => __( 'Brand section read more link', 'cmb2' ),
			'id'         => $prefix . 'page_brand_link',
			'type'       => 'text',
		) );

		$product_meta = new_cmb2_box( array(
			'id'            => $prefix . 'product_metabox',
			'title'         => __( 'Extra Product Metabox', 'cmb2' ),
			'object_types'  => array( 'product' ), // Post type
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$product_meta->add_field( array(
			'name'       => __( 'Product Info Title', 'cmb2' ),
			'desc'       => __( 'Product section title...', 'cmb2' ),
			'id'         => $prefix . 'product_info_title',
			'type'       => 'text',
		) );

		$product_meta->add_field( array(
			'name'       => __( 'Product Info Description', 'cmb2' ),
			'desc'       => __( 'Brand section description', 'cmb2' ),
			'id'         => $prefix . 'product_info_description',
			'type'       => 'wysiwyg',
		) );

		$product_meta->add_field( array(
		    'name'    => __( 'Select collection', 'cmb2' ),
		    'id'      => $prefix . 'collection_name',
		    'type'    => 'select',
		    'options_cb' => 'cmb2_get_collection_options',
		) );

		$page_meta = new_cmb2_box( array(
			'id'            => $prefix . 'page_metabox',
			'title'         => __( 'Page Options', 'cmb2' ),
			'object_types'  => array( 'page' ), // Post type
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$group_collapse = $page_meta->add_field( array(
		    'id'          => $prefix . 'page_repeat_group',
		    'type'        => 'group',
		    'description' => __( 'Add repeatable entries below', 'cmb' ),
			'show_on'      => array( 'key' => 'page-template', 'value' => array('template-tc.php')),
		    'options'     => array(
		        'group_title'   => __( 'Entry {#}', 'cmb' ), // since version 1.1.4, {#} gets replaced by row number
		        'add_button'    => __( 'Add Another Entry', 'cmb' ),
		        'remove_button' => __( 'Remove Entry', 'cmb' ),
		        'sortable'      => true, // beta
		        // 'closed'     => true, // true to have the groups closed by default
		    ),
		) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$page_meta->add_group_field( $group_collapse, array(
		    'name' => 'Heading',
		    'id'   => 'heading',
		    'type' => 'text',
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );

		$page_meta->add_group_field( $group_collapse, array(
		    'name' => 'Content',
		    'description' => 'The content t be revealed',
		    'id'   => 'content',
		    'type' => 'wysiwyg',
		    'options' => array(
		        'wpautop' => true, // use wpautop?
		        'media_buttons' => false, // show insert/upload button(s)
		        'teeny' => true, // output the minimal editor config used in Press This
		        'textarea_rows' => get_option('default_post_edit_rows', 5), // rows="..."
		    ),
		) );

		$about_meta = new_cmb2_box( array(
			'id'            => $prefix . 'about_metabox',
			'title'         => __( 'About Metabox', 'cmb2' ),
			'object_types'  => array( 'page' ), // Post type
			'show_on'      => array( 'key' => 'page-template', 'value' => array('template-about.php','template-faq.php' )),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$about_meta->add_field( array(
			'name'       => __( 'Home Slider Image', 'cmb2' ),
			'id'         => $prefix . 'about_slide_img',
			'show_on_cb'  => 'cmb_id_not_meet',
			'type'       => 'file',
		) );
		
		$about_meta->add_field( array(
		    'name'    => __( 'Select your_post_type Posts', 'cmb2' ),
		    'desc'    => __( 'field description (optional)', 'cmb2' ),
		    'id'      => $prefix . 'post_multicheckbox',
		    'show_on_cb'  => 'cmb_id_only_about',
		    'type'    => 'multicheck',
		    'options_cb' => 'cmb2_get_your_post_type_post_options',
		) );

		$about_meta->add_field( array(
			'name'       => __( 'About FAQ Heading', 'cmb2' ),
			'id'         => $prefix . 'about_faq_title',
			'show_on_cb'  => 'cmb_id_only_about',
			'type'       => 'text',
		) );

		$group_collapse = $about_meta->add_field( array(
		    'id'          => $prefix . 'about_repeat_group',
		    'type'        => 'group',
		    'description' => __( 'Add FAQ entries below', 'cmb' ),
		    'show_on_cb'  => 'cmb_id_only',
		    'options'     => array(
		        'group_title'   => __( 'FAQ {#}', 'cmb' ), // since version 1.1.4, {#} gets replaced by row number
		        'add_button'    => __( 'Add Another FAQ', 'cmb' ),
		        'remove_button' => __( 'Remove FAQ', 'cmb' ),
		        'sortable'      => true, // beta
		        // 'closed'     => true, // true to have the groups closed by default
		    ),
		) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$about_meta->add_group_field( $group_collapse, array(
		    'name' => 'Question',
		    'id'   => 'title',
		    'type' => 'text',
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );

		$about_meta->add_group_field( $group_collapse, array(
		    'name' => 'Answer',
		    'description' => 'Write a short answer for this entry',
		    'id'   => 'answer',
		    'type' => 'wysiwyg',
		    'options' => array(
		        'wpautop' => true, // use wpautop?
		        'media_buttons' => false, // show insert/upload button(s)
		        'teeny' => true, // output the minimal editor config used in Press This
		        'textarea_rows' => get_option('default_post_edit_rows', 5), // rows="..."
		    ),
		) );

		$about_meta->add_field( array(
			'name'       => __( 'Meet Kat Portrait Image', 'cmb2' ),
			'id'         => $prefix . 'about_meet_img',
			'show_on_cb'  => 'cmb_id_only_meet',
			'type'       => 'file',
		) );


		$contact_meta = new_cmb2_box( array(
			'id'            => $prefix . 'stockists_metabox',
			'title'         => __( 'Contact Page Metabox', 'cmb2' ),
			'object_types'  => array( 'page' ), // Post type
			'show_on'      => array( 'key' => 'page-template', 'value' => 'template-contact.php' ),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$group_collapse = $contact_meta->add_field( array(
		    'id'          => $prefix . 'stockists_repeat_group',
		    'type'        => 'group',
		    'description' => __( 'Add Stockists info below', 'cmb' ),
		    'show_on_cb'  => 'cmb_id_only_stockists',
		    'options'     => array(
		        'group_title'   => __( 'Stockist {#}', 'cmb' ), // since version 1.1.4, {#} gets replaced by row number
		        'add_button'    => __( 'Add Another Stockist', 'cmb' ),
		        'remove_button' => __( 'Remove Stockists', 'cmb' ),
		        'sortable'      => true, // beta
		        // 'closed'     => true, // true to have the groups closed by default
		    ),
		) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$contact_meta->add_group_field( $group_collapse, array(
		    'name' => 'Name',
		    'id'   => 'name',
			'description'   => __( 'Name of Stockist', 'cmb' ),
		    'type' => 'text',
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );

		$contact_meta->add_group_field( $group_collapse, array(
		    'name'    => 'Image',
		    'desc'    => 'Upload an image.',
		    'id'      => 'img',
		    'type'    => 'file',
		    // Optional:
		    'options' => array(
		        'url' => false, // Hide the text input for the url
		        'add_upload_file_text' => 'Add File' // Change upload button text. Default: "Add or Upload File"
		    ),
		) );

		$contact_meta->add_group_field( $group_collapse, array(
		    'name' => 'URL',
		    'id'   => 'link',
			'description'   => __( 'Link to Stockist website. incl http://', 'cmb' ),
		    'type' => 'text',
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );

		$contact_meta->add_group_field( $group_collapse, array(
		    'name' => 'Country',
		    'id'   => 'country',
			'description'   => __( 'Country where Stockist is located', 'cmb' ),
		    'type' => 'text',
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );

		$contact_meta->add_group_field( $group_collapse, array(
		    'name' => 'Details',
		    'id'   => 'details',
			'description'   => __( 'Details of Stockist', 'cmb' ),
		    'type' => 'wysiwyg',
		    'options' => array(
		    	'teeny' => true,
		    	'textarea_rows' => get_option('default_post_edit_rows', 5),
		    	'media_buttons' => false
		    ),
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );

		$contact_meta->add_field( array(
			'name'       => __( 'Google Maps Address', 'cmb2' ),
			'id'         => $prefix . 'about_google_map',
			'show_on_cb'  => 'cmb_id_only_map',
			'description'   => __( 'link to Google Maps Address', 'cmb' ),
			'type'       => 'text',
		) );

	}
	public function kvd_connection_types() {

	    

	}


}

global $cpt; 
$cpt = new kvdAdminFunctions(); 

function ed_metabox_include_front_page( $display, $meta_box ) {
    if ( ! isset( $meta_box['show_on']['key'] ) ) {
        return $display;
    }

    if ( 'front-page' !== $meta_box['show_on']['key'] ) {
        return $display;
    }

    $post_id = 0;

    // If we're showing it based on ID, get the current ID
    if ( isset( $_GET['post'] ) ) {
        $post_id = $_GET['post'];
    } elseif ( isset( $_POST['post_ID'] ) ) {
        $post_id = $_POST['post_ID'];
    }

    if ( ! $post_id ) {
        return false;
    }

    // Get ID of page set as front page, 0 if there isn't one
    $front_page = get_option( 'page_on_front' );

    // there is a front page set and we're on it!
    return $post_id == $front_page;
}
add_filter( 'cmb2_show_on', 'ed_metabox_include_front_page', 10, 2 );

/**
 * Gets a number of posts and displays them as options
 * @param  array $query_args Optional. Overrides defaults.
 * @return array             An array of options that matches the CMB2 options array
 */
function cmb2_get_post_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(
        'post_type'   => 'post',
        'numberposts' => 10,
    ) );

    $posts = get_posts( $args );

    $post_options = array();
    if ( $posts ) {
        foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
        }
    }

    return $post_options;
}

/**
 * Gets 5 posts for your_post_type and displays them as options
 * @return array An array of options that matches the CMB2 options array
 */
function cmb2_get_your_post_type_post_options() {
    return cmb2_get_post_options( array( 'post_type' => 'page', 'numberposts' => 5 ) );
}

function cmb2_get_collection_options() {
    return cmb2_get_post_options( array( 'post_type' => 'collections', 'numberposts' => -1 ) );
}

//
// CMB2 Conditional Functions
//

// About FAQ's & FAQ's
function cmb_id_only($field) { global $post; return ($post->ID == 626 || $post->ID == 7 ) ; }
// About only
function cmb_id_only_about($field) { global $post; return $post->ID == 626; }
// About Meet kat
function cmb_id_only_meet($field) { global $post; return $post->ID == 631; }
// Not on Meet kat
function cmb_id_not_meet($field) { global $post; return $post->ID != 631; }

// Contact Stockists
function cmb_id_only_stockists($field) { global $post; return $post->ID == 665; }
// Contact Map
function cmb_id_only_map($field) { global $post; return $post->ID == 660 || 856; }

