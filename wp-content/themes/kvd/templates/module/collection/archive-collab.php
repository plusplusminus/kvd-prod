<?php

$query_args = array(
	'post_type' => 'collaborations',
	'posts_per_page' => -1
);

query_posts( $query_args );

if (have_posts()) : $count = 0; while (have_posts()) : the_post(); $count++;

	get_template_part('templates/module/collection/archive','block');

endwhile;

endif; wp_reset_query();