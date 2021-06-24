<?php
	//Standard Plan Template

	global $post;
	global $pdf_output;
	global $pdf_header;
	global $pdf_footer;

	global $pdf_template_pdfpage;
	global $pdf_template_pdfpage_page;
	global $pdf_template_pdfdoc;

	global $pdf_html_header;
	global $pdf_html_footer;

	global $wp_query;
	//Set a pdf template. if both are set the pdfdoc is used. (You didn't need a pdf template)
	$pdf_template_pdfpage 		= ''; //The filename off the pdf file (you need this for a page template)
	$pdf_template_pdfpage_page 	= 1;  //The page off this page (you need this for a page template)

	$pdf_template_pdfdoc  		= ''; //The filename off the complete pdf document (you need only this for a document template)

	$pdf_html_header 			= false; //If this is ture you can write instead of the array a html string on the var $pdf_header
	$pdf_html_footer 			= false; //If this is ture you can write instead of the array a html string on the var $pdf_footer

	//Set the Footer and the Header
	$pdf_header = array (
  		'odd' =>
  			array (
				'R' =>
   					array (
						'content' => 'Teléfono: (+56)2 2682 0333 | Email: ventasonline@immaval.cl',
						'font-size' => 8,
						'font-style' => 'B',
						'font-family' => 'DejaVuSansCondensed',
					),
					'line' => 1,
  				),
  		'even' =>
  			array (
				'R' =>
					array (
						'content' => '{PAGENO}',
						'font-size' => 8,
						'font-style' => 'B',
						'font-family' => 'DejaVuSansCondensed',
					),
					'line' => 1,
  			),
	);
	$pdf_footer = array (
	  	'odd' =>
	 	 	array (
				'R' =>
					array (
						'content' => date_i18n(get_option('date_format')),
						'font-size' => 8,
						'font-style' => 'BI',
						'font-family' => 'DejaVuSansCondensed',
					),
				'C' =>
					array (
		  				'content' => '- {PAGENO} / {nb} -',
		  				'font-size' => 8,
		  				'font-style' => '',
		  				'font-family' => '',
					),
				'L' =>
					array (
		  				'content' => get_bloginfo('name'),
		  				'font-size' => 8,
		  				'font-style' => 'BI',
		  				'font-family' => 'DejaVuSansCondensed',
					),
				'line' => 1,
	  		),
	  	'even' =>
			array (
				'R' =>
					array (
						'content' => date_i18n(get_option('date_format')),
						'font-size' => 8,
						'font-style' => 'BI',
						'font-family' => 'DejaVuSansCondensed',
					),
				'C' =>
					array (
		  				'content' => '- {PAGENO} / {nb} -',
		  				'font-size' => 8,
		  				'font-style' => '',
		  				'font-family' => '',
					),
				'L' =>
					array (
		  				'content' => get_bloginfo('name'),
		  				'font-size' => 8,
		  				'font-style' => 'BI',
		  				'font-family' => 'DejaVuSansCondensed',
					),
				'line' => 1,
	  		),
	);

$blog_info_special = htmlspecialchars(get_bloginfo('name'), ENT_QUOTES);
$blog_info =  get_bloginfo();
$logo = get_custom_logo();
$fecha = date_i18n(get_option('date_format'));

$pdf_output =
"
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<html xml:lang='es'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<title>$blog_info</title>
	</head>
	<body xml:lang='es'>
		<bookmark content='$blog_info_special' level='0' />
		<tocentry content='$blog_info_special' level='0' />
		<div id='header'>
			<div id='headerimg'>
				<div>$logo</div>
			</div>
		</div>
		<div id='content' class='widecolumn'>
";

			if(is_product_category())
			{
				$categoria = get_queried_object();
				$categoria_nombre = $categoria->name;
				$categoria_ancestros = get_ancestors( $categoria->term_id, 'product_cat' );
				$categoria_padre_id = end($categoria_ancestros);
				$categoria_padre = get_term($categoria_padre_id, 'product_cat');
				$categoria_padre_nombre = $categoria_padre->name;
				$categoria_padre_descripcion = $categoria_padre->description;
				$categoria_padre_imagen_url = wp_get_attachment_url(get_term_meta( $categoria_padre_id, 'thumbnail_id', true ));
				$pdf_output .=
"
				<div class='imagen-categoria'><img src='$categoria_padre_imagen_url' alt='$categoria_padre_nombre' width='50%' /></div>
				<div class='post'><h1 class='pagetitle'>$categoria_padre_nombre</h1></div>
				<p>$categoria_padre_descripcion</p>
				<div class='post'><h2 class='pagetitle'>$categoria_nombre</h2></div>
";
			}

			$parametros = $wp_query->query_vars;
			$parametros['nopaging']=true;
			$parametros['paged']=null;
			$parametros['posts_per_page']=-1;
			$parametros['posts_per_archive_page']=-1;
			$parametros['page_id']=null;
			wp_reset_postdata();
			$loop = new WP_Query( $parametros );
			while ($loop->have_posts())
			{
				the_post();
				$producto = wc_get_product();
				$sku = $producto->get_sku();
				$nombre = get_the_title();
				$imagen = woocommerce_get_product_thumbnail();
				$descripcion = wpautop(get_the_content(), true);
				$descripcion_corta = get_the_excerpt();
				$enlace = 	get_permalink();
				$nombre_atributos = the_title_attribute(array('before' => 'Enlace a '), '', 0);
				$pdf_output .=
"
				<bookmark content='$nombre' level='1' />
				<tocentry content='$nombre' level='1' />
				<div class='post'>
					<table>
						<tbody>
							<tr>
								<td>$imagen</td>
								<td>
									<h2 class='alignright'><a href='$enlace' rel='bookmark' title='$nombre_atributos'>$nombre</a></h2>
									<p class='sku'>SKU: $sku</p>
									<div class='entry'>$descripcion_corta</div>
									<div>
";
										if (is_user_logged_in() && in_array('wwp_wholesaler', wp_get_current_user()->roles) && $producto->get_meta('_wwp_enable_wholesale_item')=='yes')
										{
											$pdf_output .= '<b>Precio Comerciante: '.wc_price($producto->get_meta('_wwp_wholesale_amount')). ' neto</b>';
										}
										else
										{
											$pdf_output .= '<b>Precio: '. wc_price($producto->get_price()).'</b>';
										}
										$pdf_output .= do_shortcode('[pwb-brand product_id="'.get_the_ID().'" image_size="thumbnail"]');
										$pdf_output .=
"
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div> <!-- post -->
";
			}
			$pdf_output .=
"
		</div> <!--content-->
	</body>
</html>
";
		wp_reset_postdata();
//$pdf_output = ob_get_clean($pdf_output);
//$pdf_output =  str_replace(array("\r", "\n"), '', trim(ob_get_clean()));
//$pdf_output =  preg_replace("/\r?\n/m", "", ob_get_clean());
