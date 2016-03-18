<?php 

/* Template Name: Home */

get_header();


?>
<main class="page-main">

<?php get_template_part('templates/module/post/post','header'); ?>

<?php get_template_part('templates/module/page/content'); ?>



</main>


<?php get_template_part('templates/module/collection/related','collections-single'); ?>

<?php
get_footer();

?>