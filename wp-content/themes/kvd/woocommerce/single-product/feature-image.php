<?php global $product; ?>

<?php  $image_attributes_large = wp_get_attachment_image_src( $product->get_image_id(),'full' ); ?>

<div class="top-image alt-only">
  <div style="background-image: url('<?php echo $image_attributes_large[0]; ?>');" data-mobile-ratio="0.8" data-tablet-ratio="1.66233766233766" data-ratio="none" class="product-image active"></div>
</div>
