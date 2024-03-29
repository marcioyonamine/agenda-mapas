<?php
/*
Plugin Name: Agenda - Mapas Culturais
Plugin URI: https://estudioamarelinha.com.br/agenda-mapas
Description: Plugin para importação e atualização de eventos das plataformas Mapas Culturais para o Wordpress.
Author: Estúdio Amarelinha
Version: 0.1 beta
Author URI:  https://estudioamarelinha.com.br
*/
function _mapas_set_constants() {

	if ( ! defined( 'MAPAS_URL' ) ) {
		define( 'MAPAS_URL', plugin_dir_url( __FILE__ ) );
	}
}

define( 'MAPAS_URL', plugin_dir_path( __FILE__ ) );




// Cria a tabela _mapas 
function mapas_create_db() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$tabela = $wpdb->prefix."mapas";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if($wpdb->get_var("SHOW TABLES LIKE '$tabela'") != '$tabela'){		

		$sql = "CREATE TABLE `$tabela` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `wp` longtext NOT NULL,
		  `mapas` longtext NOT NULL,
		  `type` varchar(120) NOT NULL,
		  `atualizacao` datetime NOT NULL,
		  `atualizacao_mapas` datetime NOT NULL,
  		`edit` tinyint(4) NOT NULL,
		  PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql );
	}
}
register_activation_hook( __FILE__, 'mapas_create_db' );

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




function mapas_locais($atts){
	extract(shortcode_atts(array("selo" => NULL,"parente"=> NULL), $atts));
	
		global $wpdb;

	$tabela = $wpdb->prefix."mapas";

	//var_dump($atts);

	// carrega as variáveis de configuração
	$existe = $wpdb->get_results("SELECT * FROM $tabela WHERE type IN('url-api','selo') ",ARRAY_A);

	for($i = 0; $i < count($existe); $i++){
		switch($existe[$i]['type']){

		case 'url-api':
			global $url_api;	
			$url_api = $existe[$i]['mapas'];
		break;

		case 'selo':
			global $selo;
			$selo = $existe[$i]['mapas'];
		break;
		
		}
	}
	
	// trata o selo
	if(!isset($atts['selo'])){
		$seal_treat = '';
	}else{
		$seal_treat = $atts['selo'];
	}

	if(!isset($atts['parente'])){
		$parente = '';
	}else{
		$parente = $atts['parente'];
	}
	
	//trata o relacionamento
	
	$url_mapas = "http://".$url_api."/api/";
	$url_space = $url_mapas."space/find";
	$data = array(
		"@select" => "id, name,shortDescription,longDescription,updateTimestamp,parent,En_Nome_Logradouro,En_Num, En_Complemento, En_Bairro, En_Municipio, En_Estado, En_CEP, location, terms, seals, createTimestamp",
		"@seals" => $seal_treat
		);

	$loc = mapasChamaAPI($url_space,$data);
	
	$k = 0;
	for($i = 0; $i < count($loc); $i++){
		if($parente == 'pai' AND $loc[$i]['parent'] == NULL){
			echo $loc[$i]['name']."<br />";	
			$k++;
		}
		if($parente != 'pai'){
			echo $loc[$i]['name']."<br />";	
			$k++;
		}


	}
	
	echo $k;

}
add_shortcode('mapas-locais', 'mapas_locais');

///////////////////// Funcoes	

function mapasChamaAPI($url,$data){
	$url = 
	$get_addr = $url.'?'.http_build_query($data);
	$ch = curl_init($get_addr);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$page = curl_exec($ch);
	$evento = json_decode($page);
	$ccsp = mapasConverterObjParaArray($evento);
	$ccsp['url'] = $ch;
	return $ccsp;
}

function mapasConverterObjParaArray($data) { //função que transforma objeto vindo do json em array
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    }
    else {
        return $data;
    }
}

function jsonMapas($get_addr){

$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
return $page;
	
}

function postName($string){
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z',
        'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
        'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
        'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
        'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
        'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
        'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
        'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
    );
    // Traduz os caracteres em $string, baseado no vetor $table
    $string = strtr($string, $table);
    // converte para minúsculo
    $string = strtolower($string);
    // remove caracteres indesejáveis (que não estão no padrão)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    // Remove múltiplas ocorrências de hífens ou espaços
    $string = preg_replace("/[\s-]+/", " ", $string);
    // Transforma espaços e underscores em hífens
    $string = preg_replace("/[\s_]/", "-", $string);
    // retorna a string
    return $string;
}

function verificaEspaco($idEspaco,$url_api,$espaco){

$url_space = "http://$url_api/api/space/getChildrenIds/$espaco";
$locais = json_decode(jsonMapas($url_space));
$loc = mapasConverterObjParaArray($locais);
$var_loc = $espaco.",";
for($k = 0; $k < sizeof($loc); $k++){
	$var_loc .= $loc[$k].",";
}

$pos = strpos($var_loc, $idEspaco); 
return $pos;
	
}

function mapasWp($id,$base,$type){
	global $wpdb;
	switch($base){
	case "mapas":
	$sql = "SELECT * FROM ".$wpdb->prefix."mapas WHERE mapas = '".$id."' AND type = '".$type."'";
		$ex = $wpdb->get_results($sql,ARRAY_A);
		return $ex;
	break;
	case "wp":
		$sql = "SELECT * FROM ".$wpdb->prefix."mapas WHERE wp = '".$id."' AND type = '".$type."'";
		$ex = $wpdb->get_results($sql,ARRAY_A);
		return $ex;
	break;
	}
}




function insereEvento($idMapas, $atualiza = NULL){
	
	
	
	
	set_time_limit(0);
	global $wpdb; //carrega a superglobal do WP
	global $url_api;
	$data = array(
 	   '@select' => 'name,project,occurrences.{id,space.{name},rule},terms,longDescription,shortDescription',
       '@files' => '(header.header,avatar,avatar.avatarBig,avatar.avatarEvent,downloads):url',
		'id' => 'eq('.$idMapas.')'
  	);
	$get_addr = 'https://'.$url_api.'/api/event/find?'.http_build_query($data);
	$evento = json_decode(jsonMapas($get_addr));
	$e =  mapasConverterObjParaArray($evento);
	
	echo "<pre>";
	var_dump($e);
	echo "</pre>";


	$evento_mapas = mapasWp($idMapas,"mapas","event");



	
	
	//eventos dos mapas
	for($i = 0; $i < count($e); $i++){ // eventos
		for($k = 0; $k < count($e[$i]['occurrences']); $k++){ // ocorrencias
		$descricao = $e[$i]['longDescription'];
			if($descricao == ""){
				$descricao = $e[$i]['shortDescription'];	
			}
		$name = $e[$i]['name'];

		$ocor = $e[$i]['occurrences'][$k]['id'];

		$start = $e[$i]['occurrences'][$k]['rule']['startsOn']." ".$e[$i]['occurrences'][$k]['rule']['startsAt'];
		$end = $e[$i]['occurrences'][$k]['rule']['startsOn']." ".$e[$i]['occurrences'][$k]['rule']['endsAt'];
		$until = $e[$i]['occurrences'][$k]['rule']['until']." 00:00";		
		$frequency = $e[$i]['occurrences'][$k]['rule']['frequency'];
		$espaco = $e[$i]['occurrences'][$k]['space']['name'];
		
		$semana = "";
		for($d = 0; $d <= 7; $d++){
			if(isset($e[$i]['occurrences'][$k]['rule']['day'][$d]) AND $e[$i]['occurrences'][$k]['rule']['day'][$d] == "on"){
				//SU,MO,TU,WE,TH,FR,SA
				switch($d){
					case 0:
						$semana = $semana."SU,";
					break;	
					case 1:
						$semana = $semana."MO,";
					break;	
					case 2:
						$semana = $semana."TU,";
					break;	
					case 3:
						$semana = $semana."WE,";
					break;	
					case 4:
						$semana = $semana."TH,";
					break;	
					case 5:
						$semana = $semana."FR,";
					break;	
					case 6:
						$semana = $semana."SA,";
					break;	
				}	
			}
		}
		$semana = substr($semana,0,-1);
		
		$categoria = array(); //precisa converter para WP
		for($l = 0; $l < sizeof($e[$i]['terms']['linguagem']); $l++){
			$converte = mapasWp($e[$i]['terms']['linguagem'][$l],"mapas","equi_categoria");
			$conv = get_term_by('slug',$converte[0]['wp'],'event-category');
			array_push($categoria,$conv->term_id);
		}
		
		
		
		//echo "Array da Categoria: (".$categoria." ) ";

		//$categoria = substr($categoria,0,-1);
		$tags = "";
		for($t = 0; $t < sizeof($e[$i]['terms']['tag']); $t++){
			$tags = $tags.$e[$i]['terms']['tag'][$t].",";
			$tag_cat = mapasWp($e[$i]['terms']['tag'][$t],"mapas","equi_tags");
			$conv = get_term_by('slug',$tag_cat[0]['wp'],'event-category');
			array_push($categoria,$conv->term_id);
			
		}
			$tags = $tags.$e[$i]['project']['name'].",";
		
		
		$tags = substr($tags,0,-1);		

		
		
		
		
		//construir o event_data
		$event_data = array(
			'start'     => new DateTime($start, eo_get_blog_timezone() ),
			'end'       => new DateTime($end, eo_get_blog_timezone() ),
			'until'     => new DateTime($until, eo_get_blog_timezone() ),
			'schedule'  => $frequency,
			'schedule_meta' =>array($semana)
	   );
	
	//echo "Horário: ".$start."<br />";
	
		//construir o post_data	
		$post_data = array(
			'post_title'=>$name,
			'post_content'=>$descricao,
			'tax_input'=>array(
				'event-venue'=>$espaco,
				'event-category'=>$categoria,
				'event-tag' => array($tags)
			),
			'post_status'   => 'publish'
		);
		
		
		if($evento_mapas == NULL){
			$post = eo_insert_event($event_data,$post_data);
			$query_insere = "INSERT INTO `".$wpdb->prefix."mapas` (`id`, `wp`, `mapas`, `type`,`ocor`,`edit` ) VALUES (NULL, '".$post."', '".$idMapas."', 'event','".$ocor."', '1')";
			$mapas = $wpdb->query($query_insere);
			if($mapas == TRUE){
				echo $name." inserido com sucesso.<br />";	
			}else{
				echo "Erro ao inserir o evento $name. $query_insere  (4)<br />";	
			}
			
			
			$getImageFile = $e[$i]["@files:header.header"]["url"];
			$wp_filetype = wp_check_filetype( $getImageFile, null );
			$attachment_data = array(	
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name( $getImageFile ),
			'post_content' => '',
			'post_status' => 'inherit'

		);	
			
		//$attach_id = wp_insert_attachment( $attachment_data, $getImageFile, $post );	
				Generate_Featured_Image( $getImageFile, $post  );
			
		}else{

			//echo "Atualiza".$atualiza."<br />";
			$lista_evento_ocorrencia = "SELECT * FROM `".$wpdb->prefix."mapas` WHERE type = 'event' AND ocor ='".$ocor."'";
				
			$res = $wpdb->get_results($lista_evento_ocorrencia,ARRAY_A);
			
			
			
			
			for($y = 0; $y < count($res); $y++){
				$id_wp = $res[$y]['wp'];
				$post = eo_update_event($id_wp, $event_data, $post_data );
				$getImageFile = $e[$i]["@files:header.header"]["url"];
				$wp_filetype = wp_check_filetype( $getImageFile, null );
				echo "<pre>";
				var_dump($post);
				echo "</pre>";
			if($post == TRUE){
				echo $name." atualizado com sucesso.<br />";	
			}else{
				echo "Erro ao atualizar o evento $name. $query_insere  (4)<br />";	
			}



				$attachment_data = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => sanitize_file_name($getImageFile),
					'post_content' => '',
					'post_status' => 'inherit',
				);	
				//$attach_id = wp_insert_attachment( $attachment_data, $getImageFile, $atualiza );	
		
				$y = Generate_Featured_Image( $getImageFile, $atualiza  );
	
		}
		
		
		//echo "url da imagem: ".$getImageFile;
		//var_dump($y);
		
		
		return $post;
	}
	}

}
}

function Generate_Featured_Image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))
      $file = $upload_dir['path'] . '/' . $filename;
    else
      $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
	$x = array();
	$x['attach_data'] = $attach_data;
	$x['res1'] = $res1;
	$x['res2'] = $res2;
	return $x;
}


function listaLinguagensMapas($linguagem = NULL){
	global $wpdb; 
	$sql = "SELECT * FROM ".$wpdb->prefix."mapas WHERE type LIKE '%linguagens%' ORDER BY type ASC"; 
	$ex = $wpdb->get_results($sql,ARRAY_A);
	return $ex;
	
}






////////////////////////////Fim das Funcoes



?>
