<?php /* Template Name: Collections  */ ?>

<?php get_header(); ?>

<header class="about-header">
	<div class="about-header__image">
		<?php the_post_thumbnail('large',array('class','img-responsive')); ?>
	</div>
	<div class="about-header__content">
		<h1 class="about-header__title"><?php the_title(); ?></h1>
	</div>
</header>


<section class="container_content container_content--collections">

	<div class="container">
		<?php get_template_part('templates/module/collection/content'); ?>
		<?php get_template_part('templates/module/collection/archive'); ?>

	</div><!--/.container-->

</section>

<?php get_template_part('templates/module/collection/related','collab'); ?>

<?php get_footer(); ?>