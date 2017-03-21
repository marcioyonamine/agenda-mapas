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
echo "<h1>Plugin Agenda Mapas Culturais</h1>

<p>Para utilizar o plugin é necessário que seja fornecido o endereço do Mapa Cultural sem o \"http://\"<br />
(ex: <i>spcultural.prefeitura.sp.gov.br)</i></p>";
if($url_api == "" OR $url_api == NULL){
	echo "Não há endereço da instalação dos Mapas Culturais. Por favor, preencha.";

}else{
	echo "O endereço fornecido é:<br />
	<strong>$url_api .</strong><br/><br />
	";

	if($agente == "" OR $url_api == NULL){
		echo "Não há agente cadastrado. Sem o cadastro, o sistema buscará todos os eventos sem filtrar por agente responsável.<br />";
	}else{
		echo "O agente fornecido possui o ID $agente<br />
		Verifique se é o agente de quem exibirá os eventos <a href='http://$url_api/agente/$agente' target='_blank'> clicando aqui.
		</a>";
		
	}
	echo "<br /><br />";

	if($espaco == "" OR $url_api == NULL){
		echo "Não há espaço cadastrado. Sem o cadastro, o sistema buscará todos os eventos sem filtrar por Espaço.<br />";
	}else{
		echo "O espaço cadastrado possui o ID $espaco<br />
		Verifique se o espaço está correto <a href='http://$url_api/espaco/$espaco' target='_blank'> clicando aqui.</a> <br />
		Obs: O plugin importa e atualiza os espaços e seus espaços filhos.<br />";
	}



}



?>