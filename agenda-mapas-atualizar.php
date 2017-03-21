<?php

	global $wpdb;

	// carrega as variáveis de configuração
	$existe = $wpdb->get_results("SELECT * FROM wp_mapas WHERE type IN('url-api','espaco','agente','periodo') ",ARRAY_A);

	for($i = 0; $i < sizeof($existe); $i++){
		switch($existe[$i]['type']){

		case 'url-api':
			global $url_api;	
			$url_api = $existe[$i]['mapas'];
		break;

		case 'agente':
			global $agente;
			$agente = $existe[$i]['mapas'];
		break;
		
		case 'espaco':
			global $espaco;
			$espaco = $existe[$i]['mapas'];
		break;
		
		case 'periodo':
			global $periodo;
			$periodo = $existe[$i]['mapas'];
		break;
		}
	}
	
///////////////////// Funcoes	
function converterObjParaArray($data) { //função que transforma objeto vindo do json em array
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
$loc = converterObjParaArray($locais);
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
	$sql = "SELECT * FROM wp_mapas WHERE mapas = '".$id."' AND type = '".$type."'";
		$ex = $wpdb->get_results($sql,ARRAY_A);
		return $ex;
	break;
	case "wp":
		$sql = "SELECT * FROM wp_mapas WHERE wp = '".$id."' AND type = '".$type."'";
		$ex = $wpdb->get_results($sql,ARRAY_A);
		return $ex;
	break;
	}

	
}

function insereEvento($idMapas){
	set_time_limit(0);
	global $wpdb; //carrega a superglobal do WP
	global $url_api;
	$data = array(
 	   '@select' => 'name,project,occurrences,terms,longDescription,shortDescription',
		'id' => 'eq('.$idMapas.')'
  	);
	$get_addr = 'http://'.$url_api.'/api/event/find?'.http_build_query($data);
	$evento = json_decode(jsonMapas($get_addr));
	$e =  converterObjParaArray($evento);
	
	for($i = 0; $i < sizeof($e); $i++){
		for($k = 0; $k < count($e[$i]['occurrences']); $k++){
		$descricao = $e[$i]['longDescription'];
			if($descricao == ""){
				$descricao = $e[$i]['shortDescription'];	
			}
		$name = $e[$i]['name'];

		$start = $e[$i]['occurrences'][$k]['rule']['startsOn']." ".$e[$i]['occurrences'][$k]['rule']['startsAt'];
		$end = $e[$i]['occurrences'][$k]['rule']['startsOn']." ".$e[$i]['occurrences'][$k]['rule']['endsAt'];
		$until = $e[$i]['occurrences'][$k]['rule']['until']." 00:00";		
		$frequency = $e[$i]['occurrences'][$k]['rule']['frequency'];
		$espaco = $e[$i]['occurrences'][$k]['space']['name'];
		
		$semana = "";
		for($d = 0; $d <= 7; $d++){
			if($e[$i]['occurrences'][$k]['rule']['day'][$d] == "on"){
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
		
		$categoria = ""; //precisa converter para WP
		for($l = 0; $l < sizeof($e[$i]['terms']['linguagem']); $l++){
			$converte = mapasWp($e[$i]['terms']['linguagem'][$l],"mapas","linguagem");
			$categoria = $categoria.$converte[0]['wp'].",";
		}
		$categoria = substr($categoria,0,-1);
		$tags = "";
		for($t = 0; $t < sizeof($e[$i]['terms']['tag']); $t++){
			$tags = $tags.$e[$i]['terms']['tag'][$t].",";
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
	
		//construir o post_data	
		$post_data = array(
			'post_title'=>$name,
			'post_content'=>$descricao,
			'tax_input'=>array(
				'event-venue'=>$espaco,
				'event-category'=>array($categoria),
				'event-tag' => array($tags)
			),
			'post_status'   => 'publish'
		);
		
		$post = eo_insert_event($event_data,$post_data);
		
		return $categoria;
	}
	}
}

////////////////////////////Fim das Funcoes
	

if(isset($_POST['importar'])){

	$hoje = $_POST['dataInicio'];
	$cem_dias = $_POST['dataFinal'];
	$url_space = "http://$url_api/api/space/getChildrenIds/$espaco";
	$locais = json_decode(jsonMapas($url_space));
	$loc = converterObjParaArray($locais);
	$var_loc = $espaco.",";
	for($k = 0; $k < sizeof($loc); $k++){
		$var_loc .= $loc[$k].",";
	}
	$url = "http://".$url_api."/api/event/findByLocation";
	//$url = "http://$url_api/api/event/find";

	$data = array(
		"space"=> "IN(".substr($var_loc,0,-1).")",
   		"@from" => $hoje,
		"@to" => $cem_dias,
		"@select" => "id", 
		"isVerified" => "EQ(TRUE)"
	);

	$get_addr = $url.'?'.http_build_query($data);
	$evento = json_decode(jsonMapas($get_addr));

	echo "Foram encontrados ".sizeof($evento)." resultados.<br><br>";

	$ccsp = converterObjParaArray($evento);

	// Retorna os eventos de hoje (converter a string date para date e fazer a comparação de datas)
	$hoje = date('Y-m-d');
	$semana = date('w');
	$today = date('y-m-d H:i:s');
	echo "Hoje é $hoje<br>";
	echo $get_addr;
	$repeat = 0;
	
	
	for($i = 0; $i < sizeof($ccsp); $i++){
		$id = $ccsp[$i]['id'];	
		echo $id."<br />";	
		
		//verifica se existe o evento na base
		$evento_mapas = mapasWp($id,"mapas","event");
		if($evento_mapas == NULL){
			$insert = insereEvento($id);
			$mapas = $wpdb->query("INSERT INTO `wp_mapas` (`id`, `wp`, `mapas`, `type`,`edit` ) VALUES (NULL, '".$insert."', '".$id."', 'event', '1');");
			if($mapas == TRUE){
				echo $name." inserido com sucesso.<br />";	
			}else{
				echo "Erro ao inserir o evento $name. (4)<br />";	
			}
				
		}else{
			echo "Há, somente atualize";
			
			
		}
		

	}
	
	
}


if(isset($_POST['espacos'])){

$url_space = "http://$url_api/api/space/getChildrenIds/$espaco";
$locais = json_decode(jsonMapas($url_space));
$loc = converterObjParaArray($locais);
$var_loc = $espaco.",";
for($k = 0; $k < sizeof($loc); $k++){

	$url = "http://$url_api/api/space/find/";
	$data = array(
		"@select" => "name", 
		//"@limit" => "10", //tirar depois do teste
		"id" => "EQ(".$loc[$k].")"
		);
	$get_addr = $url.'?'.http_build_query($data);
	$espaco_json = json_decode(jsonMapas($get_addr));	
	$espaco_array = converterObjParaArray($espaco_json);
	$id_mapas = $loc[$k];
	$nome_espaco = $espaco_array[0]['name'];
	$slug_espaco = postName($nome_espaco);
	
	//verifica se o espaço existe na tabela wp_mapas
	$ex = $wpdb->get_results("SELECT * FROM wp_mapas WHERE mapas = '$id_mapas' AND type = 'space'",ARRAY_A);

	if($ex == TRUE){
		//Atualiza
		
		$idterm = $ex[0]['wp'];
		$query = "UPDATE `wp_terms` SET `name` = '$nome_espaco', `slug` = '$slug_espaco' WHERE `wp_terms`.`term_id` = $idterm";
		$update = $wpdb->query($query);
		if($update == TRUE){
			echo "Espaço <b>$nome_espaco</b> atualizado com sucesso.<br >";	
		}else{
			echo "Erro ao atualizar o espaço <b>$nome_espaco.</b><br />";	
		}

	
	}else{
		//Insere	
		//1 insere na tabela term
		//2 pega o id da 1 e insere na tabela term_taxonomy event-venue
		$insert = $wpdb->query("
			INSERT INTO wp_terms
			(name, slug, term_group)
			VALUES
			('$nome_espaco','$slug_espaco','0')"
		);
		if($insert == TRUE){
			$ultimo = $wpdb->insert_id;
			$tax = $wpdb->query("INSERT INTO `wp_term_taxonomy` (`term_id`, `taxonomy`, `description`) VALUES ('$ultimo', 'event-venue','Espaço');");
			if($tax == TRUE){
				echo "Taxonomia <b>$nome_espaco</b> inserido com sucesso.<br >";	
			}else{
				echo "Erro ao inserir taxonomia <b>$nome_espaco (2)</b><br />";	
			}			
			
			
			$mapas = $wpdb->query("INSERT INTO `wp_mapas` (`id`, `wp`, `mapas`, `type`) VALUES (NULL, '$ultimo', '$id_mapas', 'space');");
			if($mapas == TRUE){
				echo "Espaço <b>$nome_espaco</b> inserido com sucesso.<br >";	
			}else{
				echo "Erro ao inserir <b>$nome_espaco (2)</b><br />";	
			}
		}else{
			echo "Erro ao inserir <b>$nome_espaco (1)</b><br />";	
			
		}
	}
	
	/*
	echo "<pre>";
	var_dump($espaco_array);
	echo "</pre>";
	echo "<br />";
	*/
}



if(isset($_POST['projetos'])){

// recupera os projetos
$url = "http://$url_api/api/project/find";
$data = array(
	"owner" => "EQ($agente)",
	"isVerified" => "EQ(TRUE)",
	"@SELECT" => "id,name"
	);
$get_addr = $url."?".http_build_query($data);
$evento = json_decode(jsonMapas($get_addr));

//verifica se o projeto já é uma tag

//se não, insere na tabela termos (wp_terms) e pega o id

// cria a taxonomia event-tag (wp_taxonomia) e pega o id

// insere na tabela wp_mapas (idMapas, id_wp_taxonomia, project)

// cria uma pagina com a tag


}




}




//verifica se o espaço existe




	
?>

<h1>Importador/Atualizador de eventos</h1>
<p><?php if(isset($men)){echo $men;} ?></p>
<p><?php if(isset($resultado)){echo $resultado;} ?></p>
<br /><br />


<h2>Importar/Atualizar eventos e ocorrências</h2>
<p>As ocorrências dos eventos importados dos Mapas Culturais não podem ser editados no plugin "Event Organiser".</p>
<p>Para corrigir ou atualizar datas, é preciso que se atualize corretamente o evento na instalação dos Mapas Culturais e volte a atualizar no botão abaixo.</p>
<p>Eventos excluídos na instalação dos Mapas Culturais não são excluídos automaticamente por este plugin. É preciso apagar no "Event Organiser também".</p>
<br />
<form action="?page=atualizar" method="post">

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
<script>
$(function() {
    $( ".calendario" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
        changeYear: true
		
	});
});
</script>
<p>Atualizar/Importar eventos de 
<input type="text" class="calendario" name="dataInicio" size="10" maxlength="10" value="<?php $hoje = date('Y-m-d'); echo $hoje; ?>">
a <input type="text" class="calendario" name="dataFinal" size="10" maxlength="10" value="<?php echo date('Y-m-d', strtotime("+90 days",strtotime($hoje))) ?>">. (O formato de data é 'Y-m-d')</p> 
<input type="hidden" name="importar" />
<p><input type="submit" value='Importar/Atualizar eventos e ocorrências' /></p>
</form>
<br />
<ln></ln>
<h2>Importar/Atualizar espaços</h2>

<br />
<form action="?page=atualizar" method="post">
<input type="hidden" name="espacos" />
<p><input type="submit" value='Importar/Atualizar espaços'/></p>
</form>
<br />
<ln></ln>

<h2>Importar/Atualizar projetos</h2>

<br />
<form action="?page=atualizar" method="post">
<input type="hidden" name="projetos" />
<p><input type="submit" value='Importar/Atualizar projetos'/></p>
</form>
<?php 
if(isset($_POST['projetos'])){

$url = "http://$url_api/api/project/find";
$data = array(
	"owner" => "EQ($agente)",
	"isVerified" => "EQ(TRUE)",
	"@SELECT" => "id,name,type,event"
	);

$get_addr = $url."?".http_build_query($data);

$evento = json_decode(jsonMapas($get_addr));

echo "<pre>";
var_dump($evento);
echo "</pre>";

}
	
	?>

<h1>Teste Importar com Função EO</h1>
<?php /*

    $event_data = array(
	     'start'     => new DateTime('2016-12-03 15:00', eo_get_blog_timezone() ),
	     'end'       => new DateTime('2016-12-04 15:00', eo_get_blog_timezone() ),
	     'until'     => new DateTime('2016-12-25 15:00', eo_get_blog_timezone() ),
	     'frequency' => 4,
	     'all_day'   => 0,
	     'schedule'  => 'weekly',
		 'schedule_meta' =>array('SU','SA')
    );
     $post_data = array(
	     'post_title'=>'Teste com categorias, e por semana publicado',
	     'post_content'=>'My event content',
	 	'tax_input'=>array(
			'event-venue'=>array(58),
			'event-category'=>1556,
        'non_hierarchical_tax' => $non_hierarchical_tax,
		),
	'post_status'   => 'publish'
    );

    $e = eo_insert_event($post_data,$event_data);

echo "<pre>";
var_dump($e);
echo "</pre>";

*/
?>