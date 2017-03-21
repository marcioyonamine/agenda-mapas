<?php
/*
Plugin Name: Agenda - Mapas Culturais
Plugin URI: https://github.com/marcioyonamine/agenda-mapas/
Description: Plugin para importação e atualização de eventos das plataformas Mapas Culturais para o Wordpress.
Author: Marcio Yonamine
Version: 0.1 beta
Author URI:  Marcio Yonamine
*/
function _mapas_set_constants() {
	/*
 	* Defines the plug-in directory url
 	* <code>url:http://mysite.com/wp-content/plugins/event-organiser</code>
	*/
	if ( ! defined( 'MAPAS_URL' ) ) {
		define( 'MAPAS_URL', plugin_dir_url( __FILE__ ) );
	}
}

/*
 * Defines the plug-in directory path
 * <code>/home/mysite/public_html/wp-content/plugins/event-organiser</code>
*/
define( 'MAPAS_URL', plugin_dir_path( __FILE__ ) );


// Criando telas no admin
function plw_paginas_admin() {

add_menu_page( 
	'Agenda - Mapas Culturais', 
	'Agenda Mapas', 
	'manage_options', 
	'agenda-mapas', 
	'plw_menu_mapas'
);

add_submenu_page('agenda-mapas', 'Configurações', 'Configurações', 'manage_options', 'config','plw_submenu_config');

add_submenu_page('agenda-mapas', 'Atualizar', 'Atualizar', 'manage_options', 'atualizar', 'plw_submenu_atualizar');

add_submenu_page('agenda-mapas', 'Listar Eventos', 'Listar Eventos', 'manage_options', 'lista-eventos', 'plw_submenu_lista_eventos');

add_submenu_page('agenda-mapas', 'Equivalências', 'Equivalências', 'manage_options', 'equivalencias', 'plw_submenu_equivalencias');

add_submenu_page('agenda-mapas',FALSE,FALSE,'manage_options','editar-cadastro-email','plw_submenu_editar_cadastros');
}

add_action('admin_menu', 'plw_paginas_admin');


function plw_menu_mapas() {
	include_once( plugin_dir_path( __FILE__ ) . 'agenda-mapas-front.php' );
}

function plw_submenu_config() {
	include_once( plugin_dir_path( __FILE__ ) . 'agenda-mapas-config.php' );
}

function plw_submenu_atualizar() {
	include_once( plugin_dir_path( __FILE__ ) . 'agenda-mapas-atualizar.php' );
}

function plw_submenu_lista_eventos() {
	include_once( plugin_dir_path( __FILE__ ) . 'agenda-mapas-lista-eventos.php' );
}

function plw_submenu_editar_cadastros() {
	include_once( plugin_dir_path( __FILE__ ) . 'editar-cadastro.php' );
}

function plw_submenu_equivalencias() {
	include_once( plugin_dir_path( __FILE__ ) . 'agenda-mapas-equivalencias.php' );
}
?>
