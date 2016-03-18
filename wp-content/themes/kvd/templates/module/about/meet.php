<?php /* About Blocks  */ ?>

<?php global $post; ?>
<?php $meetimg = get_post_meta( get_the_ID(), '_kvd_about_meet_img', true ); ?>

<div class="about__meet">
	<div class="meet__image">
		<div class="child__image">
			<a href="<?php the_permalink(); ?>"><img src="<?php echo $meetimg;?>" class=""></a>
		</div>
	</div>
	<div class="meet__content">
		<div class="meet__header">
			<h3 class="meet__title"><?php the_title(); ?></h3>			
		</div>
		<div class="meet__text">
			<?php the_excerpt(); ?>
			<a href="<?php the_permalink(); ?>" class="meet__link link-underline read-more">Read More</a>
		</div>

	</div>
</div>

