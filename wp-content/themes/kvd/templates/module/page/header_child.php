<header class="page-header panel-heading">
	<div class="page-header__content panel-title">
		<?php $post->ID == 660 ? $class="down" : $class="up"; ?>
		<a data-toggle="collapse" class="collapsed" href="#acc<?php echo $post->ID; ?>" aria-expanded="true" aria-controls="<?php // echo $title; ?>">
			<h3 class="page-header__title"><?php the_title(); ?> <i class="icon <?php echo $class; ?> icon-accordionarrow"></i></h3>
		</a>
	</div>
</header>