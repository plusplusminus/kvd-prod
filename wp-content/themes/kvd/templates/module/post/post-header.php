<?php global $kvd_products; ?>
<header class="page-header">
	<ul class="page-header__meta">
		<li class="page-header__item"><?php the_category(); ?></li>
		<li class="page-header__item">/</li>
		<li class="page-header__item"><?php the_time( get_option( 'date_format' ) ); ?></li>
	</ul>
	<div class="page-header__content">
		<h1 class="page-header__title"><?php the_title(); ?></h1>
	</div>
	<?php $kvd_products->kvd_sharing(); ?>
</header>