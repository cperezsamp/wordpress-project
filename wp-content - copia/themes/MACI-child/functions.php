<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 20 );

// END ENQUEUE PARENT ACTION
add_filter('the_content', 'dcms_add_custom_content');


////LECTURA DE DATOS DESDE APIN Y MOSTRAR ESTOS

// Agregamos contenido sólo a la página con el título "Contenido Vinos"
function dcms_add_custom_content($content){
    
	if ( ! is_page('nuestros-actos') ) return $content;

	$html = get_data_api();
	return $content.$html;
}

// Función que se encarga de recuperar los datos de la API externa
function get_data_api(){
	$url = 'http://localhost:8002/api/acto/getAll';   //aqui la url de la api
    //return $url;
	$response = wp_remote_get($url);  //no devulve nada, es por ejecutarlo en docker, no ve artisan
	if (is_wp_error($response)) {
		error_log("Error: ". $response->get_error_message());
		return false;
	}

	$body = wp_remote_retrieve_body($response);

	$data = json_decode($body);

	$template = '<table class="table-data">
					<tr>
						<th>Nombre acto</th>
						<th>Fecha acto</th>
						<th>Hora acto</th>
						<th>Inscribirse</th>
					</tr>
					{data}
				</table>';

	if ( $data ){
		$str = '';
		foreach ($data as $acto) {
			$str .= "<tr>";
			$str .= "<td>{$acto->Titulo}</td>";
			$str .= "<td>{$acto->Fecha}</td>";
			$str .= "<td>{$acto->Hora}</td>";
			$str .= "<td><a href=#>Inscribirse</a></td>";
			$str .= "</tr>";
		}
	}

	$html = str_replace('{data}', $str, $template);

	return $html;
}
