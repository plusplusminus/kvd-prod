<?php /* Template Name: News  */ ?>

<?php get_header(); ?>

<header class="about-header">
	<div class="about-header__image">
		<img src="<?php bloginfo('stylesheet_directory')?>/assets/img/header-about.jpg"/>
	</div>
	<div class="about-header__content">
		<h1 class="about-header__title">
			    <?php printf(single_cat_title( '', false )); ?>
		</h1>
	</div>
</header>



<section class="container_content">

	<div class="container">

		<section class="archive-info">
			<div class="row">
				<div class="col-sm-6 col-md-4">
					<div class="breadcrumb-div">
						<?php custom_breadcrumb(); ?>
					</div>
				</div>
			</div>
		</section>
		

		<main class="container_grid">
			

			<?php if (have_posts()) : $count = 0; while (have_posts()) : the_post(); $count++; ?>
				<?php $css = 'css-'.$post->post_type; ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'article grid_article clearfix '.$css ); ?> role="article">
					

					<?php if ( has_post_thumbnail() ) { ?>
							<figure class="article_image">
								<a href="<?php the_permalink();?>" class="js-overlay-image"><?php the_post_thumbnail('full',array('class'=>'img-responsive')); ?></a>
							</figure>
							<?php } else {
								echo '<figure class="article_image--none"></figure>';
							}
					?>

					<ul class="page-header__meta">
						<li class="page-header__item"><?php the_category(); ?></li>
						<li class="page-header__item">/</li>
						<li class="page-header__item"><?php the_time( get_option( 'date_format' ) ); ?></li>
					</ul>

					<header class="article_header">
						<a href="<?php the_permalink();?>" class="link-underline js-overlay-link"><?php the_title(); ?></a>
					</header>


				</article> <?php // end article ?>

				<?php endwhile; ?>

					


				<?php else : ?>

						<article id="post-not-found" class="hentry clearfix">
							<header class="article-header">
								<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
							</header>
							<section class="entry-content">
								<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
							</section>
							<footer class="article-footer">
									<p><?php _e( 'This is the error message in the archive.php template.', 'bonestheme' ); ?></p>
							</footer>
						</article>

				<?php endif; ?>

		</main><!--/.container_grid-->

	</div><!--/.container-->

	<div class="container">
		<nav class="wp-prev-next">
			<ul class="clearfix">
				<li class="prev-link"><?php next_posts_link( __( '<i class="icon icon-angle-left"></i> Older Entries', 'bonestheme' )) ?></li>
				<li class="next-link"><?php previous_posts_link( __( 'Newer Entries <i class="icon icon-angle-right"></i>', 'bonestheme' )) ?></li>
			</ul>
		</nav>
	</div>

</section>

<div id="content-modal" class="modal fade">
  <button type="button" class="modal-close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-largeclose"></i></button>
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="js-content"></div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<?php get_footer(); ?>