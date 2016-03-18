<?php
/**
 * Show options for ordering
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<form class="filters" method="get">
	<div class="filter-div cf">
	    <div class="accordion-mobile">
	     	<div class="filter-left">
	     		<div class="mobile-filter"> Filters <span class="downarrow-css"></span> </div>
	     	</div>
	     	<div class="filter-right"> 
		     	<span>Sort by</span>
		      	<div class="select-box sort-by">
			       	<select name="orderby" class="orderby selectbox">
						<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
							<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
						// Keep query string vars intact
						foreach ( $_GET as $key => $val ) {
							if ( 'orderby' === $key || 'submit' === $key ) {
								continue;
							}
							if ( is_array( $val ) ) {
								foreach( $val as $innerVal ) {
									echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
								}
							} else {
								echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
							}
						}
					?>
		     	</div>
	     	</div>
	    </div>
	    <div class="mobile-filter-panel cf">
	    	<?php get_sidebar('filters'); ?>
	    </div>
	</div>
</form>

