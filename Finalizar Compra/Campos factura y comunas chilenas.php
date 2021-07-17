<?php
add_filter( 'woocommerce_default_address_fields', 'ordena_datos_cliente' );
function ordena_datos_cliente( $fields)
{
	
	$fields['address_1']['priority'] = 60;
	$fields['address_1']['class'] = array('form-row-wide', 'address-field');
	$fields['state']['priority'] = 70;
	$fields['state']['class'] = array('form-row-first', 'address-field');
	$fields['city']['priority'] = 80;
	$fields['city']['class'] = array('form-row-last', 'address-field');
	
	return $fields;
}

//Alinear campos email y teléfono
add_filter( 'woocommerce_checkout_fields' , 'diagramacion_campos_checkout', 9999 );
function diagramacion_campos_checkout( $fields )
{
	unset($fields['billing']['billing_postcode']);
	unset($fields['shipping']['shipping_postcode']);
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_address_2']);
	
	$fields['billing']['billing_phone']['class'] = array('form-row-first');
	$fields['billing']['billing_phone']['priority'] = 30;
	$fields['billing']['billing_email']['class'] = array('form-row-last');
	$fields['billing']['billing_email']['label'] = 'Email';
	$fields['billing']['billing_email']['priority'] = 40;
	
	$fields['billing']['billing_country']['priority'] = 50;
	$fields['billing']['billing_country']['class'] = array('form-row-wide', 'address-field');
	
	return $fields;
}

/**
* Add the field to the checkout
*/
add_action( 'woocommerce_after_checkout_billing_form', 'my_custom_checkout_field' );
function my_custom_checkout_field( $checkout )
{
	echo '<div id="seccion_opcion_boleta_o_factura"><h3>' . __('Documento') . '</h3>';
	echo '<div id="externo_radio_opcion_boleta_o_factura">';
	woocommerce_form_field( 'radio_opcion_boleta_o_factura', array(
		'type'          => 'radio',
		'class'         => array('my-field-class form-row-wide'),
		'label'         => __('Seleccione boleta o factura'),
		'options'   => array( 'boleta' => 'Boleta', 'factura' =>'Factura', ),
		'default'   => 'boleta',
		'required'    => true,
	), $checkout->get_value( 'radio_opcion_boleta_o_factura' ) );
	echo '</div>';
	
	echo('<div id="campos_opcion_factura">');
	woocommerce_form_field( 'razon_social_factura', array(
		'type'          => 'text',
		'class'         => array('my-field-class form-row-wide'),
		'label'         => __('Razón social'),
		'required'    => true,
	), $checkout->get_value( 'razon_social_factura' ) );
	woocommerce_form_field( 'rut_factura', array(
		'type'          => 'text',
		'class'         => array('my-field-class form-row-first'),
		'label'         => __('RUT'),
		'required'    => true,
	), $checkout->get_value( 'rut_factura' ) );
	woocommerce_form_field( 'giro_factura', array(
		'type'          => 'text',
		'class'         => array('my-field-class form-row-last'),
		'label'         => __('Giro'),
		'required'    => true,
	), $checkout->get_value( 'giro_factura' ) );
	woocommerce_form_field( 'direccion_factura', array(
		'type'          => 'text',
		'class'         => array('my-field-class form-row-wide'),
		'label'         => __('Dirección de facturación'),
		'required'    => true,
	), $checkout->get_value( 'direccion_factura' ) );
	woocommerce_form_field( 'comuna_factura', array(
		'type'          => 'text',
		'class'         => array('my-field-class form-row-first'),
		'label'         => __('Comuna'),
		'required'    => true,
	), $checkout->get_value( 'comuna_factura' ) );
	woocommerce_form_field( 'ciudad_factura', array(
		'type'          => 'text',
		'class'         => array('my-field-class form-row-last'),
		'label'         => __('Ciudad'),
		'required'    => true,
	), $checkout->get_value( 'ciudad_factura' ) );
	echo '</div>';
	echo '</div>';
}
/**
* Process the checkout
*/
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');
function my_custom_checkout_field_process($data)
{
	// Check if set, if its not set add an error.
	if( $_POST['radio_opcion_boleta_o_factura']!='boleta'&&!($_POST['razon_social_factura'] || $_POST['rut_factura']|| $_POST['giro_factura']))
	wc_add_notice( __( 'Por favor, llene los datos de facturación o seleccione "Boleta"' ), 'error' );
	return $data;
}
/**
* Update the order meta with field value
*/
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );
function my_custom_checkout_field_update_order_meta( $order_id )
{
	update_post_meta( $order_id, 'Documento', sanitize_text_field( $_POST['radio_opcion_boleta_o_factura'] ) );
	
	if ( ! empty( $_POST['razon_social_factura'] ) ) update_post_meta( $order_id, 'Razón social', sanitize_text_field( $_POST['razon_social_factura'] ) );
	if ( ! empty( $_POST['rut_factura'] ) ) update_post_meta( $order_id, 'RUT', sanitize_text_field( $_POST['rut_factura'] ) );
	if ( ! empty( $_POST['giro_factura'] ) ) update_post_meta( $order_id, 'Giro', sanitize_text_field( $_POST['giro_factura'] ) );
	if ( ! empty( $_POST['direccion_factura'] ) ) update_post_meta( $order_id, 'Dirección de facturación', sanitize_text_field( $_POST['direccion_factura'] ) );
	if ( ! empty( $_POST['comuna_factura'] ) ) update_post_meta( $order_id, 'Comuna de facturación', sanitize_text_field( $_POST['comuna_factura'] ) );
	if ( ! empty( $_POST['ciudad_factura'] ) ) update_post_meta( $order_id, 'Ciudad de facturación', sanitize_text_field( $_POST['ciudad_factura'] ) );
}
/**
* Display field value on the order edit page
*/
add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta($order)
{
	echo '<p><strong>'.__('Documento').':</strong> ' . get_post_meta( $order->get_id(), 'Documento', true ) . '</p>';
	echo '<p><strong>'.__('Razón social').':</strong> ' . get_post_meta( $order->get_id(), 'Razón social', true ) . '</p>';
	echo '<p><strong>'.__('RUT').':</strong> ' . get_post_meta( $order->get_id(), 'RUT', true ) . '</p>';
	echo '<p><strong>'.__('Giro').':</strong> ' . get_post_meta( $order->get_id(), 'Giro', true ) . '</p>';
	echo '<p><strong>'.__('Dirección de facturación').':</strong> ' . get_post_meta( $order->get_id(), 'Dirección de facturación', true ) . '</p>';
	echo '<p><strong>'.__('Comuna de facturación').':</strong> ' . get_post_meta( $order->get_id(), 'Comuna de facturación', true ) . '</p>';
	echo '<p><strong>'.__('Ciudad de facturación').':</strong> ' . get_post_meta( $order->get_id(), 'Ciudad de facturación', true ) . '</p>';
}

//Script para que aprezca o desaparezca el formulario de factura
add_action( 'wp_footer', 'jquery_opcion_factura');
function jquery_opcion_factura()
{
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		var muestra_o_oculta = function ()
		{
			if($('#radio_opcion_boleta_o_factura_factura').is(":checked"))
			{
				$('#campos_opcion_factura').show();
				console.log('factura');
			}
			else
			{
				$('#campos_opcion_factura').hide();
				console.log('boleta');
			}
		}
		muestra_o_oculta();
		$('input:radio[name="radio_opcion_boleta_o_factura"]').change(function()
		{
			muestra_o_oculta();
		});
	});
	</script>
	<?php
}

// Cambia el nombre del dato state a comuna
add_filter('woocommerce_get_country_locale', 'reemplaza_state_por_comuna');
function reemplaza_state_por_comuna($locale)
{
	$locale['CL']['state']['label'] = __('Comuna', 'woocommerce');
	return $locale;
}
//Agrega las comunas al listado de WooComerce
add_filter('woocommerce_states', 'comunas_de_chile');
function comunas_de_chile($states) {
	$states['CL'] = array(	//	Comunas of Chile Ref.:https://es.wikipedia.org/wiki/Anexo:Comunas_de_Chile.
		'01101' => __( 'Iquique', 'woocommerce' ),
		'01107' => __( 'Alto Hospicio', 'woocommerce' ),
		'01401' => __( 'Pozo Almonte', 'woocommerce' ),
		'01402' => __( 'Camiña', 'woocommerce' ),
		'01403' => __( 'Colchane', 'woocommerce' ),
		'01404' => __( 'Huara', 'woocommerce' ),
		'01405' => __( 'Pica', 'woocommerce' ),
		'02101' => __( 'Antofagasta', 'woocommerce' ),
		'02102' => __( 'Mejillones', 'woocommerce' ),
		'02103' => __( 'Sierra Gorda', 'woocommerce' ),
		'02104' => __( 'Taltal', 'woocommerce' ),
		'02201' => __( 'Calama', 'woocommerce' ),
		'02202' => __( 'Ollagüe', 'woocommerce' ),
		'02203' => __( 'San Pedro de Atacama', 'woocommerce' ),
		'02301' => __( 'Tocopilla', 'woocommerce' ),
		'02302' => __( 'María Elena', 'woocommerce' ),
		'03101' => __( 'Copiapó', 'woocommerce' ),
		'03102' => __( 'Caldera', 'woocommerce' ),
		'03103' => __( 'Tierra Amarilla', 'woocommerce' ),
		'03201' => __( 'Chañaral', 'woocommerce' ),
		'03202' => __( 'Diego de Almagro', 'woocommerce' ),
		'03301' => __( 'Vallenar', 'woocommerce' ),
		'03302' => __( 'Alto del Carmen', 'woocommerce' ),
		'03303' => __( 'Freirina', 'woocommerce' ),
		'03304' => __( 'Huasco', 'woocommerce' ),
		'04101' => __( 'La Serena', 'woocommerce' ),
		'04102' => __( 'Coquimbo', 'woocommerce' ),
		'04103' => __( 'Andacollo', 'woocommerce' ),
		'04104' => __( 'La Higuera', 'woocommerce' ),
		'04105' => __( 'Paihuano', 'woocommerce' ),
		'04106' => __( 'Vicuña', 'woocommerce' ),
		'04201' => __( 'Illapel', 'woocommerce' ),
		'04202' => __( 'Canela', 'woocommerce' ),
		'04203' => __( 'Los Vilos', 'woocommerce' ),
		'04204' => __( 'Salamanca', 'woocommerce' ),
		'04301' => __( 'Ovalle', 'woocommerce' ),
		'04302' => __( 'Combarbalá', 'woocommerce' ),
		'04303' => __( 'Monte Patria', 'woocommerce' ),
		'04304' => __( 'Punitaqui', 'woocommerce' ),
		'04305' => __( 'Río Hurtado', 'woocommerce' ),
		'05101' => __( 'Valparaíso', 'woocommerce' ),
		'05102' => __( 'Casablanca', 'woocommerce' ),
		'05103' => __( 'Concón', 'woocommerce' ),
		'05104' => __( 'Juan Fernández', 'woocommerce' ),
		'05105' => __( 'Puchuncaví', 'woocommerce' ),
		'05107' => __( 'Quintero', 'woocommerce' ),
		'05109' => __( 'Viña del Mar', 'woocommerce' ),
		'05201' => __( 'Isla de Pascua', 'woocommerce' ),
		'05301' => __( 'Los Andes', 'woocommerce' ),
		'05302' => __( 'Calle Larga', 'woocommerce' ),
		'05303' => __( 'Rinconada', 'woocommerce' ),
		'05304' => __( 'San Esteban', 'woocommerce' ),
		'05401' => __( 'La Ligua', 'woocommerce' ),
		'05402' => __( 'Cabildo', 'woocommerce' ),
		'05403' => __( 'Papudo', 'woocommerce' ),
		'05404' => __( 'Petorca', 'woocommerce' ),
		'05405' => __( 'Zapallar', 'woocommerce' ),
		'05501' => __( 'Quillota', 'woocommerce' ),
		'05502' => __( 'La Calera', 'woocommerce' ),
		'05503' => __( 'Hijuelas', 'woocommerce' ),
		'05504' => __( 'La Cruz', 'woocommerce' ),
		'05506' => __( 'Nogales', 'woocommerce' ),
		'05601' => __( 'San Antonio', 'woocommerce' ),
		'05602' => __( 'Algarrobo', 'woocommerce' ),
		'05603' => __( 'Cartagena', 'woocommerce' ),
		'05604' => __( 'El Quisco', 'woocommerce' ),
		'05605' => __( 'El Tabo', 'woocommerce' ),
		'05606' => __( 'Santo Domingo', 'woocommerce' ),
		'05701' => __( 'San Felipe', 'woocommerce' ),
		'05702' => __( 'Catemu', 'woocommerce' ),
		'05703' => __( 'Llay-Llay', 'woocommerce' ),
		'05704' => __( 'Panquehue', 'woocommerce' ),
		'05705' => __( 'Putaendo', 'woocommerce' ),
		'05706' => __( 'Santa María', 'woocommerce' ),
		'05801' => __( 'Quilpué', 'woocommerce' ),
		'05802' => __( 'Limache', 'woocommerce' ),
		'05803' => __( 'Olmué', 'woocommerce' ),
		'05804' => __( 'Villa Alemana', 'woocommerce' ),
		'06101' => __( 'Rancagua', 'woocommerce' ),
		'06102' => __( 'Codegua', 'woocommerce' ),
		'06103' => __( 'Coinco', 'woocommerce' ),
		'06104' => __( 'Coltauco', 'woocommerce' ),
		'06105' => __( 'Doñihue', 'woocommerce' ),
		'06106' => __( 'Graneros', 'woocommerce' ),
		'06107' => __( 'Las Cabras', 'woocommerce' ),
		'06108' => __( 'Machalí', 'woocommerce' ),
		'06109' => __( 'Malloa', 'woocommerce' ),
		'06110' => __( 'Mostazal', 'woocommerce' ),
		'06111' => __( 'Olivar', 'woocommerce' ),
		'06112' => __( 'Peumo', 'woocommerce' ),
		'06113' => __( 'Pichidegua', 'woocommerce' ),
		'06114' => __( 'Quinta de Tilcoco', 'woocommerce' ),
		'06115' => __( 'Rengo', 'woocommerce' ),
		'06116' => __( 'Requínoa', 'woocommerce' ),
		'06117' => __( 'San Vicente', 'woocommerce' ),
		'06201' => __( 'Pichilemu', 'woocommerce' ),
		'06202' => __( 'La Estrella', 'woocommerce' ),
		'06203' => __( 'Litueche', 'woocommerce' ),
		'06204' => __( 'Marchihue', 'woocommerce' ),
		'06205' => __( 'Navidad', 'woocommerce' ),
		'06206' => __( 'Paredones', 'woocommerce' ),
		'06301' => __( 'San Fernando', 'woocommerce' ),
		'06302' => __( 'Chépica', 'woocommerce' ),
		'06303' => __( 'Chimbarongo', 'woocommerce' ),
		'06304' => __( 'Lolol', 'woocommerce' ),
		'06305' => __( 'Nancagua', 'woocommerce' ),
		'06306' => __( 'Palmilla', 'woocommerce' ),
		'06307' => __( 'Peralillo', 'woocommerce' ),
		'06308' => __( 'Placilla', 'woocommerce' ),
		'06309' => __( 'Pumanque', 'woocommerce' ),
		'06310' => __( 'Santa Cruz', 'woocommerce' ),
		'07101' => __( 'Talca', 'woocommerce' ),
		'07102' => __( 'Constitución', 'woocommerce' ),
		'07103' => __( 'Curepto', 'woocommerce' ),
		'07104' => __( 'Empedrado', 'woocommerce' ),
		'07105' => __( 'Maule', 'woocommerce' ),
		'07106' => __( 'Pelarco', 'woocommerce' ),
		'07107' => __( 'Pencahue', 'woocommerce' ),
		'07108' => __( 'Río Claro', 'woocommerce' ),
		'07109' => __( 'San Clemente', 'woocommerce' ),
		'07110' => __( 'San Rafael', 'woocommerce' ),
		'07201' => __( 'Cauquenes', 'woocommerce' ),
		'07202' => __( 'Chanco', 'woocommerce' ),
		'07203' => __( 'Pelluhue', 'woocommerce' ),
		'07301' => __( 'Curicó', 'woocommerce' ),
		'07302' => __( 'Hualañé', 'woocommerce' ),
		'07303' => __( 'Licantén', 'woocommerce' ),
		'07304' => __( 'Molina', 'woocommerce' ),
		'07305' => __( 'Rauco', 'woocommerce' ),
		'07306' => __( 'Romeral', 'woocommerce' ),
		'07307' => __( 'Sagrada Familia', 'woocommerce' ),
		'07308' => __( 'Teno', 'woocommerce' ),
		'07309' => __( 'Vichuquén', 'woocommerce' ),
		'07401' => __( 'Linares', 'woocommerce' ),
		'07402' => __( 'Colbún', 'woocommerce' ),
		'07403' => __( 'Longaví', 'woocommerce' ),
		'07404' => __( 'Parral', 'woocommerce' ),
		'07405' => __( 'Retiro', 'woocommerce' ),
		'07406' => __( 'San Javier', 'woocommerce' ),
		'07407' => __( 'Villa Alegre', 'woocommerce' ),
		'07408' => __( 'Yerbas Buenas', 'woocommerce' ),
		'08101' => __( 'Concepción', 'woocommerce' ),
		'08102' => __( 'Coronel', 'woocommerce' ),
		'08103' => __( 'Chiguayante', 'woocommerce' ),
		'08104' => __( 'Florida', 'woocommerce' ),
		'08105' => __( 'Hualqui', 'woocommerce' ),
		'08106' => __( 'Lota', 'woocommerce' ),
		'08107' => __( 'Penco', 'woocommerce' ),
		'08108' => __( 'San Pedro de La Paz', 'woocommerce' ),
		'08109' => __( 'Santa Juana', 'woocommerce' ),
		'08110' => __( 'Talcahuano', 'woocommerce' ),
		'08111' => __( 'Tomé', 'woocommerce' ),
		'08112' => __( 'Hualpén', 'woocommerce' ),
		'08201' => __( 'Lebu', 'woocommerce' ),
		'08202' => __( 'Arauco', 'woocommerce' ),
		'08203' => __( 'Cañete', 'woocommerce' ),
		'08204' => __( 'Contulmo', 'woocommerce' ),
		'08205' => __( 'Curanilahue', 'woocommerce' ),
		'08206' => __( 'Los Álamos', 'woocommerce' ),
		'08207' => __( 'Tirúa', 'woocommerce' ),
		'08301' => __( 'Los Ángeles', 'woocommerce' ),
		'08302' => __( 'Antuco', 'woocommerce' ),
		'08303' => __( 'Cabrero', 'woocommerce' ),
		'08304' => __( 'Laja', 'woocommerce' ),
		'08305' => __( 'Mulchén', 'woocommerce' ),
		'08306' => __( 'Nacimiento', 'woocommerce' ),
		'08307' => __( 'Negrete', 'woocommerce' ),
		'08308' => __( 'Quilaco', 'woocommerce' ),
		'08309' => __( 'Quilleco', 'woocommerce' ),
		'08310' => __( 'San Rosendo', 'woocommerce' ),
		'08311' => __( 'Santa Bárbara', 'woocommerce' ),
		'08312' => __( 'Tucapel', 'woocommerce' ),
		'08313' => __( 'Yumbel', 'woocommerce' ),
		'08314' => __( 'Alto Biobío', 'woocommerce' ),
		'09101' => __( 'Temuco', 'woocommerce' ),
		'09102' => __( 'Carahue', 'woocommerce' ),
		'09103' => __( 'Cunco', 'woocommerce' ),
		'09104' => __( 'Curarrehue', 'woocommerce' ),
		'09105' => __( 'Freire', 'woocommerce' ),
		'09106' => __( 'Galvarino', 'woocommerce' ),
		'09107' => __( 'Gorbea', 'woocommerce' ),
		'09108' => __( 'Lautaro', 'woocommerce' ),
		'09109' => __( 'Loncoche', 'woocommerce' ),
		'09110' => __( 'Melipeuco', 'woocommerce' ),
		'09111' => __( 'Nueva Imperial', 'woocommerce' ),
		'09112' => __( 'Padre Las Casas', 'woocommerce' ),
		'09113' => __( 'Perquenco', 'woocommerce' ),
		'09114' => __( 'Pitrufquén', 'woocommerce' ),
		'09115' => __( 'Pucón', 'woocommerce' ),
		'09116' => __( 'Saavedra', 'woocommerce' ),
		'09117' => __( 'Teodoro Schmidt', 'woocommerce' ),
		'09118' => __( 'Toltén', 'woocommerce' ),
		'09119' => __( 'Vilcún', 'woocommerce' ),
		'09120' => __( 'Villarrica', 'woocommerce' ),
		'09121' => __( 'Cholchol', 'woocommerce' ),
		'09201' => __( 'Angol', 'woocommerce' ),
		'09202' => __( 'Collipulli', 'woocommerce' ),
		'09203' => __( 'Curacautín', 'woocommerce' ),
		'09204' => __( 'Ercilla', 'woocommerce' ),
		'09205' => __( 'Lonquimay', 'woocommerce' ),
		'09206' => __( 'Los Sauces', 'woocommerce' ),
		'09207' => __( 'Lumaco', 'woocommerce' ),
		'09208' => __( 'Purén', 'woocommerce' ),
		'09209' => __( 'Renaico', 'woocommerce' ),
		'09210' => __( 'Traiguén', 'woocommerce' ),
		'09211' => __( 'Victoria', 'woocommerce' ),
		'10101' => __( 'Puerto Montt', 'woocommerce' ),
		'10102' => __( 'Calbuco', 'woocommerce' ),
		'10103' => __( 'Cochamó', 'woocommerce' ),
		'10104' => __( 'Fresia', 'woocommerce' ),
		'10105' => __( 'Frutillar', 'woocommerce' ),
		'10106' => __( 'Los Muermos', 'woocommerce' ),
		'10107' => __( 'Llanquihue', 'woocommerce' ),
		'10108' => __( 'Maullín', 'woocommerce' ),
		'10109' => __( 'Puerto Varas', 'woocommerce' ),
		'10201' => __( 'Castro', 'woocommerce' ),
		'10202' => __( 'Ancud', 'woocommerce' ),
		'10203' => __( 'Chonchi', 'woocommerce' ),
		'10204' => __( 'Curaco de Vélez', 'woocommerce' ),
		'10205' => __( 'Dalcahue', 'woocommerce' ),
		'10206' => __( 'Puqueldón', 'woocommerce' ),
		'10207' => __( 'Queilén', 'woocommerce' ),
		'10208' => __( 'Quellón', 'woocommerce' ),
		'10209' => __( 'Quemchi', 'woocommerce' ),
		'10210' => __( 'Quinchao', 'woocommerce' ),
		'10301' => __( 'Osorno', 'woocommerce' ),
		'10302' => __( 'Puerto Octay', 'woocommerce' ),
		'10303' => __( 'Purranque', 'woocommerce' ),
		'10304' => __( 'Puyehue', 'woocommerce' ),
		'10305' => __( 'Río Negro', 'woocommerce' ),
		'10306' => __( 'San Juan de la Costa', 'woocommerce' ),
		'10307' => __( 'San Pablo', 'woocommerce' ),
		'10401' => __( 'Chaitén', 'woocommerce' ),
		'10402' => __( 'Futaleufú', 'woocommerce' ),
		'10403' => __( 'Hualaihué', 'woocommerce' ),
		'10404' => __( 'Palena', 'woocommerce' ),
		'11101' => __( 'Coyhaique', 'woocommerce' ),
		'11102' => __( 'Lago Verde', 'woocommerce' ),
		'11201' => __( 'Aysén', 'woocommerce' ),
		'11202' => __( 'Cisnes', 'woocommerce' ),
		'11203' => __( 'Guaitecas', 'woocommerce' ),
		'11301' => __( 'Cochrane', 'woocommerce' ),
		'11302' => __( "O'Higgins", 'woocommerce' ),
		'11303' => __( 'Tortel', 'woocommerce' ),
		'11401' => __( 'Chile Chico', 'woocommerce' ),
		'11402' => __( 'Río Ibáñez', 'woocommerce' ),
		'12101' => __( 'Punta Arenas', 'woocommerce' ),
		'12102' => __( 'Laguna Blanca', 'woocommerce' ),
		'12103' => __( 'Río Verde', 'woocommerce' ),
		'12104' => __( 'San Gregorio', 'woocommerce' ),
		'12201' => __( 'Cabo de Hornos', 'woocommerce' ),
		'12202' => __( 'Antártica', 'woocommerce' ),
		'12301' => __( 'Porvenir', 'woocommerce' ),
		'12302' => __( 'Primavera', 'woocommerce' ),
		'12303' => __( 'Timaukel', 'woocommerce' ),
		'12401' => __( 'Natales', 'woocommerce' ),
		'12402' => __( 'Torres del Paine', 'woocommerce' ),
		'13101' => __( 'Santiago', 'woocommerce' ),
		'13102' => __( 'Cerrillos', 'woocommerce' ),
		'13103' => __( 'Cerro Navia', 'woocommerce' ),
		'13104' => __( 'Conchalí', 'woocommerce' ),
		'13105' => __( 'El Bosque', 'woocommerce' ),
		'13106' => __( 'Estación Central', 'woocommerce' ),
		'13107' => __( 'Huechuraba', 'woocommerce' ),
		'13108' => __( 'Independencia', 'woocommerce' ),
		'13109' => __( 'La Cisterna', 'woocommerce' ),
		'13110' => __( 'La Florida', 'woocommerce' ),
		'13111' => __( 'La Granja', 'woocommerce' ),
		'13112' => __( 'La Pintana', 'woocommerce' ),
		'13113' => __( 'La Reina', 'woocommerce' ),
		'13114' => __( 'Las Condes', 'woocommerce' ),
		'13115' => __( 'Lo Barnechea', 'woocommerce' ),
		'13116' => __( 'Lo Espejo', 'woocommerce' ),
		'13117' => __( 'Lo Prado', 'woocommerce' ),
		'13118' => __( 'Macul', 'woocommerce' ),
		'13119' => __( 'Maipú', 'woocommerce' ),
		'13120' => __( 'Ñuñoa', 'woocommerce' ),
		'13121' => __( 'Pedro Aguirre Cerda', 'woocommerce' ),
		'13122' => __( 'Peñalolén', 'woocommerce' ),
		'13123' => __( 'Providencia', 'woocommerce' ),
		'13124' => __( 'Pudahuel', 'woocommerce' ),
		'13125' => __( 'Quilicura', 'woocommerce' ),
		'13126' => __( 'Quinta Normal', 'woocommerce' ),
		'13127' => __( 'Recoleta', 'woocommerce' ),
		'13128' => __( 'Renca', 'woocommerce' ),
		'13129' => __( 'San Joaquín', 'woocommerce' ),
		'13130' => __( 'San Miguel', 'woocommerce' ),
		'13131' => __( 'San Ramón', 'woocommerce' ),
		'13132' => __( 'Vitacura', 'woocommerce' ),
		'13201' => __( 'Puente Alto', 'woocommerce' ),
		'13202' => __( 'Pirque', 'woocommerce' ),
		'13203' => __( 'San José de Maipo', 'woocommerce' ),
		'13301' => __( 'Colina', 'woocommerce' ),
		'13302' => __( 'Lampa', 'woocommerce' ),
		'13303' => __( 'Til Til', 'woocommerce' ),
		'13401' => __( 'San Bernardo', 'woocommerce' ),
		'13402' => __( 'Buin', 'woocommerce' ),
		'13403' => __( 'Calera de Tango', 'woocommerce' ),
		'13404' => __( 'Paine', 'woocommerce' ),
		'13501' => __( 'Melipilla', 'woocommerce' ),
		'13502' => __( 'Alhué', 'woocommerce' ),
		'13503' => __( 'Curacaví', 'woocommerce' ),
		'13504' => __( 'María Pinto', 'woocommerce' ),
		'13505' => __( 'San Pedro', 'woocommerce' ),
		'13601' => __( 'Talagante', 'woocommerce' ),
		'13602' => __( 'El Monte', 'woocommerce' ),
		'13603' => __( 'Isla de Maipo', 'woocommerce' ),
		'13604' => __( 'Padre Hurtado', 'woocommerce' ),
		'13605' => __( 'Peñaflor', 'woocommerce' ),
		'14101' => __( 'Valdivia', 'woocommerce' ),
		'14102' => __( 'Corral', 'woocommerce' ),
		'14103' => __( 'Lanco', 'woocommerce' ),
		'14104' => __( 'Los Lagos', 'woocommerce' ),
		'14105' => __( 'Máfil', 'woocommerce' ),
		'14106' => __( 'Mariquina', 'woocommerce' ),
		'14107' => __( 'Paillaco', 'woocommerce' ),
		'14108' => __( 'Panguipulli', 'woocommerce' ),
		'14201' => __( 'La Unión', 'woocommerce' ),
		'14202' => __( 'Futrono', 'woocommerce' ),
		'14203' => __( 'Lago Ranco', 'woocommerce' ),
		'14204' => __( 'Río Bueno', 'woocommerce' ),
		'15101' => __( 'Arica', 'woocommerce' ),
		'15102' => __( 'Camarones', 'woocommerce' ),
		'15201' => __( 'Putre', 'woocommerce' ),
		'15202' => __( 'General Lagos', 'woocommerce' ),
		'16101' => __( 'Chillán', 'woocommerce' ),
		'16102' => __( 'Bulnes', 'woocommerce' ),
		'16103' => __( 'Chillán Viejo', 'woocommerce' ),
		'16104' => __( 'El Carmen', 'woocommerce' ),
		'16105' => __( 'Pemuco', 'woocommerce' ),
		'16106' => __( 'Pinto', 'woocommerce' ),
		'16107' => __( 'Quillón', 'woocommerce' ),
		'16108' => __( 'San Ignacio', 'woocommerce' ),
		'16109' => __( 'Yungay', 'woocommerce' ),
		'16201' => __( 'Quirihue', 'woocommerce' ),
		'16202' => __( 'Cobquecura', 'woocommerce' ),
		'16203' => __( 'Coelemu', 'woocommerce' ),
		'16204' => __( 'Ninhue', 'woocommerce' ),
		'16205' => __( 'Portezuelo', 'woocommerce' ),
		'16206' => __( 'Ránquil', 'woocommerce' ),
		'16207' => __( 'Treguaco', 'woocommerce' ),
		'16301' => __( 'San Carlos', 'woocommerce' ),
		'16302' => __( 'Coihueco', 'woocommerce' ),
		'16303' => __( 'Ñiquén', 'woocommerce' ),
		'16304' => __( 'San Fabián', 'woocommerce' ),
		'16305' => __( 'San Nicolás', 'woocommerce' ),
	)		
);
return $states;
}
