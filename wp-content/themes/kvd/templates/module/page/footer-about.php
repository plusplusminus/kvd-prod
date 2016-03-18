<?php global $kvd_theme; ?>

<footer class="page-footer--about">
	<?php if ($post->post_parent == 0) : ?>
		<?php $kvd_theme->get_child_pages(); ?>

		<?php $kvd_theme->get_child_meet(); ?>
	<?php else : ?>
		<?php $kvd_theme->get_discover_pages($post->ID); ?>
	<?php endif; ?>


</footer>