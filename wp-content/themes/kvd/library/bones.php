<?php
/* Welcome to Bones :)
This is the core Bones file where most of the
main functions & features reside. If you have
any custom functions, it's best to put them
in the functions.php file.

Developed by: Eddie Machado
URL: http://themble.com/bones/
*/

/*********************
LAUNCH BONES
Let's fire off all the functions
and tools. I put it up here so it's
right up top and clean.
*********************/

// we're firing all out initial functions at the start
add_action( 'after_setup_theme', 'bones_ahoy', 16 );

function bones_ahoy() {

    // launching operation cleanup
    add_action( 'init', 'bones_head_cleanup' );
    // remove WP version from RSS
    add_filter( 'the_generator', 'bones_rss_version' );
    // remove pesky injected css for recent comments widget
    add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
    // clean up comment styles in the head
    add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
    // clean up gallery output in wp
    add_filter( 'gallery_style', 'bones_gallery_style' );

    // ie conditional wrapper

    // launching this stuff after theme setup
    bones_theme_support();

    add_filter( 'the_content', 'bones_filter_ptags_on_images' );

} /* end bones ahoy */

/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function bones_head_cleanup() {
	// category feeds
	// remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	// remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );

} /* end bones head cleanup */

// remove WP version from RSS
function bones_rss_version() { return ''; }

// remove injected CSS for recent comments widget
function bones_remove_wp_widget_recent_comments_style() {
   if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
      remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
   }
}

// remove injected CSS from recent comments widget
function bones_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
  }
}

// remove injected CSS from gallery
function bones_gallery_style($css) {
  return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function bones_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// rss thingy
	add_theme_support('automatic-feed-links');

	// to add header image support go here: http://themble.com/support/adding-header-background-image-support/

	// adding post format support
	add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	);

	// wp menus
	add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'bonestheme' ),   // main nav in header
		)
	);
} /* end bones theme support */


/*********************
MENUS & NAVIGATION
*********************/

// the main menu
function bones_main_nav() {
	// display the wp3 menu if available
    wp_nav_menu(array(
    	'container' => false,                           			// remove nav container
    	'container_class' => 'menu clearfix',           			// class of container (should you choose to use it)
    	'menu' => __( 'The Main Menu', 'bonestheme' ),  			// nav name
    	'menu_class' => 'nav_primary nav navbar-nav navbar-right',				  			// adding custom nav class
    	'theme_location' => 'main-nav',                 			// where it's located in the theme
    	'before' => '',                                 			// before the menu
      'after' => '',                                  			// after the menu
      'link_before' => '',                            			// before each link
      'link_after' => '',                             			// after each link
      'depth' => 2,                                   			// limit the depth of the nav
      'fallback_cb' => 'wp_bootstrap_navwalker::fallback',	// fallback
    	'walker' => new wp_bootstrap_navwalker()        			// for bootstrap nav
	));
} /* end bones main nav */

// this is the fallback for header menu
function bones_main_nav_fallback() {
	wp_page_menu( array(
		'show_home' => true,
    	'menu_class' => 'nav top-nav clearfix',      // adding custom nav class
		'include'     => '',
		'exclude'     => '',
		'echo'        => true,
        'link_before' => '',                            // before each link
        'link_after' => ''                             // after each link
	) );
}

// this is the fallback for footer menu
function bones_footer_links_fallback() {
	/* you can put a default here if you like */
}

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function bones_filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

/*
 * This is a modified the_author_posts_link() which just returns the link.
 *
 * This is necessary to allow usage of the usual l10n process with printf().
 */
function bones_get_the_author_posts_link() {
	global $authordata;
	if ( !is_object( $authordata ) )
		return false;
	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ), // No further l10n needed, core will take care of this one
		get_the_author()
	);
	return $link;
}

/**
 *
 * Scripts and Styles
 *
 * @since  1.0.0
 *
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }
/**
 *
 * Scripts: Frontend with no conditions, Add Custom Scripts to wp_head
 *
 * @since  1.0.0
 *
 */
add_action('wp_enqueue_scripts', 'aa_scripts');
function aa_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
        global $kvd;

    	wp_enqueue_script('jquery'); // Enqueue it!
        //wp_deregister_script('jquery'); // Deregister WordPress jQuery
        //wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', array(), '1.11.2');
        /**
         *
         * Minified and concatenated scripts
         *
         *     @vendors     plugins.min,js
         *     @custom      scripts.min.js
         *
         *     Order is important
         *
         */
        wp_register_script('aa_vendorsJs', get_template_directory_uri() . '/assets/js/vendors.js',array(),'',true); // Custom scripts
        


        wp_register_script('aa_customJs', get_template_directory_uri() . '/assets/js/custom.js',array('aa_vendorsJs'),'',true); // Custom scripts
        
        $urls = array(
            'sizing' => get_permalink($kvd['sizing_page']),
            'personalise' => get_permalink($kvd['personalisation_page'])
        );

        if (isset($_REQUEST['add-to-cart'])) {
            $urls['addtocart'] = true;
        }
        wp_localize_script( 'aa_customJs', 'kvd_urls', $urls );

        wp_enqueue_script('aa_customJs'); // Enqueue it!


        if (is_product()) {
            wp_register_script('aa_productsJs', get_template_directory_uri() . '/library/js/products.js',array('aa_vendorsJs','aa_customJs'),'',true); // Custom scripts
            wp_enqueue_script('aa_productsJs'); // Enqueue it!
        }

        /**
         *
         * Non-minifies scripts
         *
         */
        // wp_register_script('aa_abc_js', get_template_directory_uri() . '/assets/js/vendor/abc.min.js' ); // Conditional script(s)
        // wp_enqueue_script('aa_abc_js'); // Enqueue it!
        /**
         *
         * Enqueue HTML5Shiv and Respond.js IE less than 9
         *
         */
        wp_register_style( 'ie_html5shiv', get_template_directory_uri() . '/js/html5shiv.js' );
        wp_enqueue_style( 'ie_html5shiv');
        wp_style_add_data( 'ie_html5shiv', 'conditional', 'lt IE 9' );
        wp_register_style( 'ie_respond', get_template_directory_uri() . '/js/respond.min.js' );
        wp_enqueue_style( 'ie_respond');
        wp_style_add_data( 'ie_respond', 'conditional', 'lt IE 9' );
    }
}
/**
 *
 * Styles: Frontend with no conditions, Add Custom styles to wp_head
 *
 * @since  1.0
 *
 */
add_action('wp_enqueue_scripts', 'aa_styles'); // Add Theme Stylesheet
function aa_styles()
{
    /**
     *
     * Minified and Concatenated styles
     *
     */
    wp_register_style('aa_style', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
    wp_enqueue_style('aa_style'); // Enqueue it!
    /**
     *
     * Google fonts
     *     Must be included this way to avoid Firefox issues
     *
     */
    // wp_register_style('aa_gfonts', 'http://fonts.googleapis.com/css?family=Open+Sans:300,800,400', array(), '1.0', 'all');
    // wp_enqueue_style('aa_gfonts'); // Enqueue it!
    /**
     *
     * Non-minified or non-concatenated styles
     *
     */
    // wp_register_style('aa_xyz_css', get_template_directory_uri() . '/assets/css/vendor/xyz.css', array(), '1.0', 'all');
    // wp_enqueue_style('aa_xyz_css'); // Enqueue it!
}

/**
 *
 * Comment Reply js to load only when thread_comments is active
 *
 * @since  1.0.0
 *
 */
add_action( 'wp_enqueue_scripts', 'aa_enqueue_comments_reply' );
function aa_enqueue_comments_reply() {
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

?>
