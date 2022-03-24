<?php 
global $wpdb;

$existe = $wpdb->get_results("SELECT * FROM wp_mapas WHERE type IN('url-api','espaco','agente') ",ARRAY_A);

for($i = 0; $i < sizeof($existe); $i++){
	switch($existe[$i]['type']){

	case 'url-api':
		$url_api = $existe[$i]['mapas'];	
	break;

	case 'agente':
		$agente = $existe[$i]['mapas'];
	break;
	
	case 'espaco':
		$espaco = $existe[$i]['mapas'];
	break;
	}
}

echo "<div class='wrap'>";


echo "<h1>Agenda - Mapas Culturais</h1>


<div class='card'>
<p>O Plugin <strong>Agenda - Mapas Culturais</strong> funciona em conjunto com o plugin  <strong>Event Organizer</strong> (<a href='https://wordpress.org/plugins/event-organiser/'>https://wordpress.org/plugins/event-organiser/)</a>. </p>

<p>Para utilizar o plugin é necessário que seja fornecido o endereço do Mapa Cultural sem o \"http://\"<br />
(ex: <i>spcultural.prefeitura.sp.gov.br)</i></p>";
	if($url_api == "" OR $url_api == NULL){
		echo "Não há endereço da instalação dos Mapas Culturais. Por favor, preencha.";

	}else{
		echo "O endereço fornecido é:<br />
		<strong>$url_api .</strong><br/><br />
		";
	}

echo "
<p>O shortcode [mapas-locais] gera uma lista com os espaços-pai e suas informações para ser usado em páginas que listem endereços, contatos,links etc</p>
</div>
";
require_once(ABSPATH . '/wp-admin/includes/plugin.php');

// Verifica se o plugin está ativado...
if (is_plugin_active('event-organiser/event-organiser.php')) {
    echo "Está ativo";
} else {
    echo "Não está ativo.";

}

echo "</div>";

?>