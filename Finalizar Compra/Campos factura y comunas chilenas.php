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
	$states['CL'] = array(
		'CL100' => __('Algarrobo', 'woocommerce'),
		'CL101' => __('Alhué', 'woocommerce'),
		'CL102' => __('Alto Biobío', 'woocommerce'),
		'CL103' => __('Alto del Carmen', 'woocommerce'),
		'CL104' => __('Alto Hospicio', 'woocommerce'),
		'CL105' => __('Ancud', 'woocommerce'),
		'CL106' => __('Andacollo', 'woocommerce'),
		'CL107' => __('Angol', 'woocommerce'),
		'CL108' => __('Antártica', 'woocommerce'),
		'CL109' => __('Antofagasta', 'woocommerce'),
		'CL110' => __('Antuco', 'woocommerce'),
		'CL111' => __('Arauco', 'woocommerce'),
		'CL112' => __('Arica', 'woocommerce'),
		'CL113' => __('Aysén', 'woocommerce'),
		'CL114' => __('Buin', 'woocommerce'),
		'CL115' => __('Bulnes', 'woocommerce'),
		'CL116' => __('Cabildo', 'woocommerce'),
		'CL117' => __('Cabo de Hornos', 'woocommerce'),
		'CL118' => __('Cabrero', 'woocommerce'),
		'CL119' => __('Calama', 'woocommerce'),
		'CL120' => __('Calbuco', 'woocommerce'),
		'CL121' => __('Caldera', 'woocommerce'),
		'CL122' => __('Calera de Tango', 'woocommerce'),
		'CL123' => __('Calle Larga', 'woocommerce'),
		'CL124' => __('Camarones', 'woocommerce'),
		'CL125' => __('Camiña', 'woocommerce'),
		'CL126' => __('Canela', 'woocommerce'),
		'CL127' => __('Cañete', 'woocommerce'),
		'CL128' => __('Carahue', 'woocommerce'),
		'CL129' => __('Cartagena', 'woocommerce'),
		'CL130' => __('Casablanca', 'woocommerce'),
		'CL131' => __('Castro', 'woocommerce'),
		'CL132' => __('Catemu', 'woocommerce'),
		'CL133' => __('Cauquenes', 'woocommerce'),
		'CL134' => __('Cerrillos', 'woocommerce'),
		'CL135' => __('Cerro Navia', 'woocommerce'),
		'CL136' => __('Chaitén', 'woocommerce'),
		'CL137' => __('Chanco', 'woocommerce'),
		'CL138' => __('Chañaral', 'woocommerce'),
		'CL139' => __('Chépica', 'woocommerce'),
		'CL140' => __('Chiguayante', 'woocommerce'),
		'CL141' => __('Chile Chico', 'woocommerce'),
		'CL142' => __('Chillán', 'woocommerce'),
		'CL143' => __('Chillán Viejo', 'woocommerce'),
		'CL144' => __('Chimbarongo', 'woocommerce'),
		'CL145' => __('Cholchol', 'woocommerce'),
		'CL146' => __('Chonchi', 'woocommerce'),
		'CL147' => __('Cisnes', 'woocommerce'),
		'CL148' => __('Cobquecura', 'woocommerce'),
		'CL149' => __('Cochamó', 'woocommerce'),
		'CL150' => __('Cochrane', 'woocommerce'),
		'CL151' => __('Codegua', 'woocommerce'),
		'CL152' => __('Coelemu', 'woocommerce'),
		'CL153' => __('Coihueco', 'woocommerce'),
		'CL154' => __('Coinco', 'woocommerce'),
		'CL155' => __('Colbún', 'woocommerce'),
		'CL156' => __('Colchane', 'woocommerce'),
		'CL157' => __('Colina', 'woocommerce'),
		'CL158' => __('Collipulli', 'woocommerce'),
		'CL159' => __('Coltauco', 'woocommerce'),
		'CL160' => __('Combarbalá', 'woocommerce'),
		'CL161' => __('Concepción', 'woocommerce'),
		'CL162' => __('Conchalí', 'woocommerce'),
		'CL163' => __('Concón', 'woocommerce'),
		'CL164' => __('Constitución', 'woocommerce'),
		'CL165' => __('Contulmo', 'woocommerce'),
		'CL166' => __('Copiapó', 'woocommerce'),
		'CL167' => __('Coquimbo', 'woocommerce'),
		'CL168' => __('Coronel', 'woocommerce'),
		'CL169' => __('Corral', 'woocommerce'),
		'CL170' => __('Coyhaique', 'woocommerce'),
		'CL171' => __('Cunco', 'woocommerce'),
		'CL172' => __('Curacautín', 'woocommerce'),
		'CL173' => __('Curacaví', 'woocommerce'),
		'CL174' => __('Curaco de Vélez', 'woocommerce'),
		'CL175' => __('Curanilahue', 'woocommerce'),
		'CL176' => __('Curarrehue', 'woocommerce'),
		'CL177' => __('Curepto', 'woocommerce'),
		'CL178' => __('Curicó', 'woocommerce'),
		'CL179' => __('Dalcahue', 'woocommerce'),
		'CL180' => __('Diego de Almagro', 'woocommerce'),
		'CL181' => __('Doñihue', 'woocommerce'),
		'CL182' => __('El Bosque', 'woocommerce'),
		'CL183' => __('El Carmen', 'woocommerce'),
		'CL184' => __('El Monte', 'woocommerce'),
		'CL185' => __('El Quisco', 'woocommerce'),
		'CL186' => __('El Tabo', 'woocommerce'),
		'CL187' => __('Empedrado', 'woocommerce'),
		'CL188' => __('Ercilla', 'woocommerce'),
		'CL189' => __('Estación Central', 'woocommerce'),
		'CL190' => __('Florida', 'woocommerce'),
		'CL191' => __('Freire', 'woocommerce'),
		'CL192' => __('Freirina', 'woocommerce'),
		'CL193' => __('Fresia', 'woocommerce'),
		'CL194' => __('Frutillar', 'woocommerce'),
		'CL195' => __('Futaleufú', 'woocommerce'),
		'CL196' => __('Futrono', 'woocommerce'),
		'CL197' => __('Galvarino', 'woocommerce'),
		'CL198' => __('General Lagos', 'woocommerce'),
		'CL199' => __('Gorbea', 'woocommerce'),
		'CL200' => __('Graneros', 'woocommerce'),
		'CL201' => __('Guaitecas', 'woocommerce'),
		'CL202' => __('Hijuelas', 'woocommerce'),
		'CL203' => __('Hualaihué', 'woocommerce'),
		'CL204' => __('Hualañé', 'woocommerce'),
		'CL205' => __('Hualpén', 'woocommerce'),
		'CL206' => __('Hualqui', 'woocommerce'),
		'CL207' => __('Huara', 'woocommerce'),
		'CL208' => __('Huasco', 'woocommerce'),
		'CL209' => __('Huechuraba', 'woocommerce'),
		'CL210' => __('Illapel', 'woocommerce'),
		'CL211' => __('Independencia', 'woocommerce'),
		'CL212' => __('Iquique', 'woocommerce'),
		'CL213' => __('Isla de Maipo', 'woocommerce'),
		'CL214' => __('Isla de Pascua', 'woocommerce'),
		'CL215' => __('Juan Fernández', 'woocommerce'),
		'CL216' => __('La Calera', 'woocommerce'),
		'CL217' => __('La Cisterna', 'woocommerce'),
		'CL218' => __('La Cruz', 'woocommerce'),
		'CL219' => __('La Estrella', 'woocommerce'),
		'CL220' => __('La Florida', 'woocommerce'),
		'CL221' => __('La Granja', 'woocommerce'),
		'CL222' => __('La Higuera', 'woocommerce'),
		'CL223' => __('La Ligua', 'woocommerce'),
		'CL224' => __('La Pintana', 'woocommerce'),
		'CL225' => __('La Reina', 'woocommerce'),
		'CL226' => __('La Serena', 'woocommerce'),
		'CL227' => __('La Unión', 'woocommerce'),
		'CL228' => __('Lago Ranco', 'woocommerce'),
		'CL229' => __('Lago Verde', 'woocommerce'),
		'CL230' => __('Laguna Blanca', 'woocommerce'),
		'CL231' => __('Laja', 'woocommerce'),
		'CL232' => __('Lampa', 'woocommerce'),
		'CL233' => __('Lanco', 'woocommerce'),
		'CL234' => __('Las Cabras', 'woocommerce'),
		'CL235' => __('Las Condes', 'woocommerce'),
		'CL236' => __('Lautaro', 'woocommerce'),
		'CL237' => __('Lebu', 'woocommerce'),
		'CL238' => __('Licantén', 'woocommerce'),
		'CL239' => __('Limache', 'woocommerce'),
		'CL240' => __('Linares', 'woocommerce'),
		'CL241' => __('Litueche', 'woocommerce'),
		'CL242' => __('Llanquihue', 'woocommerce'),
		'CL243' => __('Llay Llay', 'woocommerce'),
		'CL244' => __('Lo Barnechea', 'woocommerce'),
		'CL245' => __('Lo Espejo', 'woocommerce'),
		'CL246' => __('Lo Prado', 'woocommerce'),
		'CL247' => __('Lolol', 'woocommerce'),
		'CL248' => __('Loncoche', 'woocommerce'),
		'CL249' => __('Longaví', 'woocommerce'),
		'CL250' => __('Lonquimay', 'woocommerce'),
		'CL251' => __('Los Álamos', 'woocommerce'),
		'CL252' => __('Los Andes', 'woocommerce'),
		'CL253' => __('Los Ángeles', 'woocommerce'),
		'CL254' => __('Los Lagos', 'woocommerce'),
		'CL255' => __('Los Muermos', 'woocommerce'),
		'CL256' => __('Los Sauces', 'woocommerce'),
		'CL257' => __('Los Vilos', 'woocommerce'),
		'CL258' => __('Lota', 'woocommerce'),
		'CL259' => __('Lumaco', 'woocommerce'),
		'CL260' => __('Machalí', 'woocommerce'),
		'CL261' => __('Macul', 'woocommerce'),
		'CL262' => __('Máfil', 'woocommerce'),
		'CL263' => __('Maipú', 'woocommerce'),
		'CL264' => __('Malloa', 'woocommerce'),
		'CL265' => __('Marchihue', 'woocommerce'),
		'CL266' => __('María Elena', 'woocommerce'),
		'CL267' => __('María Pinto', 'woocommerce'),
		'CL268' => __('Mariquina', 'woocommerce'),
		'CL269' => __('Maule', 'woocommerce'),
		'CL270' => __('Maullín', 'woocommerce'),
		'CL271' => __('Mejillones', 'woocommerce'),
		'CL272' => __('Melipeuco', 'woocommerce'),
		'CL273' => __('Melipilla', 'woocommerce'),
		'CL274' => __('Molina', 'woocommerce'),
		'CL275' => __('Monte Patria', 'woocommerce'),
		'CL276' => __('Mostazal', 'woocommerce'),
		'CL277' => __('Mulchén', 'woocommerce'),
		'CL278' => __('Nacimiento', 'woocommerce'),
		'CL279' => __('Nancagua', 'woocommerce'),
		'CL280' => __('Natales', 'woocommerce'),
		'CL281' => __('Navidad', 'woocommerce'),
		'CL282' => __('Negrete', 'woocommerce'),
		'CL283' => __('Ninhue', 'woocommerce'),
		'CL284' => __('Nogales', 'woocommerce'),
		'CL285' => __('Nueva Imperial', 'woocommerce'),
		'CL286' => __('Ñiquén', 'woocommerce'),
		'CL287' => __('Ñuñoa', 'woocommerce'),
		'CL288' => __('O\'Higgins', 'woocommerce'),
		'CL289' => __('Olivar', 'woocommerce'),
		'CL290' => __('Ollagüe', 'woocommerce'),
		'CL291' => __('Olmué', 'woocommerce'),
		'CL292' => __('Osorno', 'woocommerce'),
		'CL293' => __('Ovalle', 'woocommerce'),
		'CL294' => __('Padre Hurtado', 'woocommerce'),
		'CL295' => __('Padre las Casas', 'woocommerce'),
		'CL296' => __('Paihuano', 'woocommerce'),
		'CL297' => __('Paillaco', 'woocommerce'),
		'CL298' => __('Paine', 'woocommerce'),
		'CL299' => __('Palena', 'woocommerce'),
		'CL300' => __('Palmilla', 'woocommerce'),
		'CL301' => __('Panguipulli', 'woocommerce'),
		'CL302' => __('Panquehue', 'woocommerce'),
		'CL303' => __('Papudo', 'woocommerce'),
		'CL304' => __('Paredones', 'woocommerce'),
		'CL305' => __('Parral', 'woocommerce'),
		'CL306' => __('Pedro Aguirre Cerda', 'woocommerce'),
		'CL307' => __('Pelarco', 'woocommerce'),
		'CL308' => __('Pelluhue', 'woocommerce'),
		'CL309' => __('Pemuco', 'woocommerce'),
		'CL310' => __('Pencahue', 'woocommerce'),
		'CL311' => __('Penco', 'woocommerce'),
		'CL312' => __('Peñaflor', 'woocommerce'),
		'CL313' => __('Peñalolén', 'woocommerce'),
		'CL314' => __('Peralillo', 'woocommerce'),
		'CL315' => __('Perquenco', 'woocommerce'),
		'CL316' => __('Petorca', 'woocommerce'),
		'CL317' => __('Peumo', 'woocommerce'),
		'CL318' => __('Pica', 'woocommerce'),
		'CL319' => __('Pichidegua', 'woocommerce'),
		'CL320' => __('Pichilemu', 'woocommerce'),
		'CL321' => __('Pinto', 'woocommerce'),
		'CL322' => __('Pirque', 'woocommerce'),
		'CL323' => __('Pitrufquén', 'woocommerce'),
		'CL324' => __('Placilla', 'woocommerce'),
		'CL325' => __('Portezuelo', 'woocommerce'),
		'CL326' => __('Porvenir', 'woocommerce'),
		'CL327' => __('Pozo Almonte', 'woocommerce'),
		'CL328' => __('Primavera', 'woocommerce'),
		'CL329' => __('Providencia', 'woocommerce'),
		'CL330' => __('Puchuncaví', 'woocommerce'),
		'CL331' => __('Pucón', 'woocommerce'),
		'CL332' => __('Pudahuel', 'woocommerce'),
		'CL333' => __('Puente Alto', 'woocommerce'),
		'CL334' => __('Puerto Montt', 'woocommerce'),
		'CL335' => __('Puerto Octay', 'woocommerce'),
		'CL336' => __('Puerto Varas', 'woocommerce'),
		'CL337' => __('Pumanque', 'woocommerce'),
		'CL338' => __('Punitaqui', 'woocommerce'),
		'CL339' => __('Punta Arenas', 'woocommerce'),
		'CL340' => __('Puqueldón', 'woocommerce'),
		'CL341' => __('Purén', 'woocommerce'),
		'CL342' => __('Purranque', 'woocommerce'),
		'CL343' => __('Putaendo', 'woocommerce'),
		'CL344' => __('Putre', 'woocommerce'),
		'CL345' => __('Puyehue', 'woocommerce'),
		'CL346' => __('Queilén', 'woocommerce'),
		'CL347' => __('Quellón', 'woocommerce'),
		'CL348' => __('Quemchi', 'woocommerce'),
		'CL349' => __('Quilaco', 'woocommerce'),
		'CL350' => __('Quilicura', 'woocommerce'),
		'CL351' => __('Quilleco', 'woocommerce'),
		'CL352' => __('Quillón', 'woocommerce'),
		'CL353' => __('Quillota', 'woocommerce'),
		'CL354' => __('Quilpué', 'woocommerce'),
		'CL355' => __('Quinchao', 'woocommerce'),
		'CL356' => __('Quinta de Tilcoco', 'woocommerce'),
		'CL357' => __('Quinta Normal', 'woocommerce'),
		'CL358' => __('Quintero', 'woocommerce'),
		'CL359' => __('Quirihue', 'woocommerce'),
		'CL360' => __('Rancagua', 'woocommerce'),
		'CL361' => __('Ránquil', 'woocommerce'),
		'CL362' => __('Rauco', 'woocommerce'),
		'CL363' => __('Recoleta', 'woocommerce'),
		'CL364' => __('Renaico', 'woocommerce'),
		'CL365' => __('Renca', 'woocommerce'),
		'CL366' => __('Rengo', 'woocommerce'),
		'CL367' => __('Requínoa', 'woocommerce'),
		'CL368' => __('Retiro', 'woocommerce'),
		'CL369' => __('Rinconada', 'woocommerce'),
		'CL370' => __('Río Bueno', 'woocommerce'),
		'CL371' => __('Río Claro', 'woocommerce'),
		'CL372' => __('Río Hurtado', 'woocommerce'),
		'CL373' => __('Río Ibáñez', 'woocommerce'),
		'CL374' => __('Río Negro', 'woocommerce'),
		'CL375' => __('Río Verde', 'woocommerce'),
		'CL376' => __('Romeral', 'woocommerce'),
		'CL377' => __('Saavedra', 'woocommerce'),
		'CL378' => __('Sagrada Familia', 'woocommerce'),
		'CL379' => __('Salamanca', 'woocommerce'),
		'CL380' => __('San Antonio', 'woocommerce'),
		'CL381' => __('San Bernardo', 'woocommerce'),
		'CL382' => __('San Carlos', 'woocommerce'),
		'CL383' => __('San Clemente', 'woocommerce'),
		'CL384' => __('San Esteban', 'woocommerce'),
		'CL385' => __('San Fabián', 'woocommerce'),
		'CL386' => __('San Felipe', 'woocommerce'),
		'CL387' => __('San Fernando', 'woocommerce'),
		'CL388' => __('San Gregorio', 'woocommerce'),
		'CL389' => __('San Ignacio', 'woocommerce'),
		'CL390' => __('San Javier', 'woocommerce'),
		'CL391' => __('San Joaquín', 'woocommerce'),
		'CL392' => __('San José de Maipo', 'woocommerce'),
		'CL393' => __('San Juan de la Costa', 'woocommerce'),
		'CL394' => __('San Miguel', 'woocommerce'),
		'CL395' => __('San Nicolás', 'woocommerce'),
		'CL396' => __('San Pablo', 'woocommerce'),
		'CL397' => __('San Pedro', 'woocommerce'),
		'CL398' => __('San Pedro de Atacama', 'woocommerce'),
		'CL399' => __('San Pedro de la Paz', 'woocommerce'),
		'CL400' => __('San Rafael', 'woocommerce'),
		'CL401' => __('San Ramón', 'woocommerce'),
		'CL402' => __('San Rosendo', 'woocommerce'),
		'CL403' => __('San Vicente', 'woocommerce'),
		'CL404' => __('Santa Bárbara', 'woocommerce'),
		'CL405' => __('Santa Cruz', 'woocommerce'),
		'CL406' => __('Santa Juana', 'woocommerce'),
		'CL407' => __('Santa María', 'woocommerce'),
		'CL408' => __('Santiago', 'woocommerce'),
		'CL409' => __('Santo Domingo', 'woocommerce'),
		'CL410' => __('Sierra Gorda', 'woocommerce'),
		'CL411' => __('Talagante', 'woocommerce'),
		'CL412' => __('Talca', 'woocommerce'),
		'CL413' => __('Talcahuano', 'woocommerce'),
		'CL414' => __('Taltal', 'woocommerce'),
		'CL415' => __('Temuco', 'woocommerce'),
		'CL416' => __('Teno', 'woocommerce'),
		'CL417' => __('Teodoro Schmidt', 'woocommerce'),
		'CL418' => __('Tierra Amarilla', 'woocommerce'),
		'CL419' => __('Tiltil', 'woocommerce'),
		'CL420' => __('Timaukel', 'woocommerce'),
		'CL421' => __('Tirúa', 'woocommerce'),
		'CL422' => __('Tocopilla', 'woocommerce'),
		'CL423' => __('Toltén', 'woocommerce'),
		'CL424' => __('Tomé', 'woocommerce'),
		'CL425' => __('Torres del Paine', 'woocommerce'),
		'CL426' => __('Tortel', 'woocommerce'),
		'CL427' => __('Traiguén', 'woocommerce'),
		'CL428' => __('Treguaco', 'woocommerce'),
		'CL429' => __('Tucapel', 'woocommerce'),
		'CL430' => __('Valdivia', 'woocommerce'),
		'CL431' => __('Vallenar', 'woocommerce'),
		'CL432' => __('Valparaíso', 'woocommerce'),
		'CL433' => __('Vichuquén', 'woocommerce'),
		'CL434' => __('Victoria', 'woocommerce'),
		'CL435' => __('Vicuña', 'woocommerce'),
		'CL436' => __('Vilcún', 'woocommerce'),
		'CL437' => __('Villa Alegre', 'woocommerce'),
		'CL438' => __('Villa Alemana', 'woocommerce'),
		'CL439' => __('Villarrica', 'woocommerce'),
		'CL440' => __('Viña del Mar', 'woocommerce'),
		'CL441' => __('Vitacura', 'woocommerce'),
		'CL442' => __('Yerbas Buenas', 'woocommerce'),
		'CL443' => __('Yumbel', 'woocommerce'),
		'CL444' => __('Yungay', 'woocommerce'),
		'CL445' => __('Zapallar', 'woocommerce'),
	);
	return $states;
}
