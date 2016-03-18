<?php
// Fix for the WordPress 3.0 "paged" bug.
$paged = 1;
if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
if ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
$paged = intval( $paged );

// Exclude categories on the homepage.

global $kvd_theme;

$kvd_theme->set_type('collections');

$query_args = array('post_type' => $kvd_theme->type,'orderby'=>'date', 'order'=>'desc');

query_posts( $query_args );

if (have_posts()) : $count = 0; while (have_posts()) : the_post(); $count++;

	get_template_part('templates/module/collection/archive','block');

endwhile; endif; wp_reset_query();