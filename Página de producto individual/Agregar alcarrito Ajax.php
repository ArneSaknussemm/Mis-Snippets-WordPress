<?php
/**
* Sobreescribe el enlace de agregar al carrito para incrustar un control de cantidad
*/
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_loop_ajax_add_to_cart', 10, 2 );
function quantity_inputs_for_loop_ajax_add_to_cart( $html, $product )
{
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		// Obtiene las clases necesarias
		$class = implode( ' ', array_filter( array(
			'button',
			'product_type_' . $product->get_type(),
			$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
		) ) );

		// Incrusta el control de cantidad al botón Ajax de agregar al carrito
		$html = sprintf( '<div class="control-qty">%s<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a></div>',
			woocommerce_quantity_input( array(), $product, false ),
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $quantity ) ? $quantity : 1 ),
			esc_attr( $product->get_id() ),
			esc_attr( $product->get_sku() ),
			esc_attr( isset( $class ) ? $class : 'button' ),
			esc_html( $product->add_to_cart_text() )
		);
	}
	return $html;
}

//Este script mejor encolarlo con wp_enqueue_script
add_action( 'wp_footer' , 'archives_quantity_fields_script' );
function archives_quantity_fields_script(){
	?>
	<script type='text/javascript'>
		jQuery(function($){
			// Update data-quantity
			$(document.body).on('click input', 'input.qty', function() {
				$(this).parent().parent().find('a.ajax_add_to_cart').attr('data-quantity', $(this).val());
				$(".added_to_cart").remove(); // Optional: Removing other previous "view cart" buttons
			}).on('click', '.add_to_cart_button', function(){
				var button = $(this);
				setTimeout(function(){
					button.parent().find('.quantity > input.qty').val(1); // reset quantity to 1
				}, 1000); // After 1 second

			});
		});
	</script>
	<?php
}
