<?php global $post; ?>

<div class="brand-container common-text">
	<div class="main cf">
		<div class="brand-left"> <span><?php echo get_post_meta($post->ID,'_kvd_page_brand_subtitle',1); ?></span>
		<h2><?php echo get_post_meta($post->ID,'_kvd_page_brand_title',1); ?></h2>
	</div>
	<div class="brand-right">
		<?php echo wpautop(get_post_meta($post->ID,'_kvd_page_brand_description',1)); ?>
		<a href="<?php echo get_post_meta($post->ID,'_kvd_page_brand_link',1); ?>" class="link-underline read-more" title="Read More">Read More</a> </div>
	</div>
</div>