<?php 

/* Template Name: About */

get_header();


?>
<?php if (is_page( 'about' ))  { get_template_part('templates/module/about/header'); } ?>

<main class="page-main--about">

<?php get_template_part('templates/module/page/header'); ?>

<?php get_template_part('templates/module/page/content'); ?>

<?php get_template_part('templates/module/page/footer','about'); ?>

</main>

<?php get_footer(); ?>