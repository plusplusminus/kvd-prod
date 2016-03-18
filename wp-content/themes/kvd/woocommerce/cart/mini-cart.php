<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="cart_list product_list_widget <?php echo $args['list_class']; ?> shopping-cart-list search-cart-hidden cf">
	<div class="shopping-cart-list-scroll">
		<div class="shopping-cart-list-inner cf">
			

			<?php if ( ! WC()->cart->is_empty() ) : ?>

				<div class="head-cart">
					<a class="close-cart" id="close-cart" href="javascript:{}" title="My Bag"></a>
				</div>

				<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

							$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
							$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
							$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							?>
							<div class="header-cart-col-main <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
								<div class="product-sm-img">
									<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
								</div>
								<div class="header-cart-col1">
									<div class="cart-prod-name">
										<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>" title="<?php echo $product_name; ?>"><?php echo $product_name; ?></a> 
										<span><?php echo WC()->cart->get_item_data( $cart_item ); ?></span> 
									</div>
									<div class="cf">
										<div class="header-cart-minus">
											<?php
											echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
												'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
												esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
												__( 'Remove this item', 'woocommerce' ),
												esc_attr( $product_id ),
												esc_attr( $_product->get_sku() )
											), $cart_item_key );
											?>
										</div>
										<div class="header-cart-price"><?php echo $product_price; ?></div>
									</div>
								</div>
														
							</div>
							<?php
						}
					}
				?>

			<?php else : ?>

				<div class="head-cart">
					<a class="close-cart" id="close-cart" href="javascript:{}" title="My Bag"></a>
					<div class="header-cart-col-main mini_cart_item">
						<p>No products in your bag.</p>			
					</div>
				</div>

			<?php endif; ?>

		</div>
	</div>
	<div class="cart-total-checkout">
		<div class="header-cart-col-main footer-total">
			<div class="total-txt">Sub Total</div>
			<div class="total-amount header-cart-price"><?php echo WC()->cart->get_cart_subtotal(); ?></div>
		</div>
		<a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="checkout-btn button"><?php _e( 'Checkout', 'woocommerce' ); ?></a>
	</div>
</div>


<?php do_action( 'woocommerce_after_mini_cart' ); ?>
