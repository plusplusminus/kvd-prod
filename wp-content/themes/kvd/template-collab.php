<?php /* Template Name: Collaboration  */ ?>

<?php get_header(); ?>

<header class="about-header">
	<div class="about-header__image">
		<?php the_post_thumbnail('full'); ?>
	</div>
	<div class="about-header__content">
		<h1 class="about-header__title"><?php the_title(); ?></h1>
	</div>
</header>



<section class="container_archive">

	<div class="container">


		<?php get_template_part('templates/module/collection/content'); ?>

		<?php get_template_part('templates/module/collection/archive','collab'); ?>
		

	</div><!--/.container-->

</section>

<?php get_template_part('templates/module/collection/related','collections'); ?>


<?php get_footer(); ?>