<?php
//Elimina las opciones de envío del carrito
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'deshabilitar_envios_en_carrito', 99 );
function deshabilitar_envios_en_carrito( $muestra_envío )
{
	return  is_cart()?false:$muestra_envío;
}
