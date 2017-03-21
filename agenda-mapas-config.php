<?php
//Exibe erros PHP
@ini_set('display_errors', '1');
error_reporting(E_ALL); 
	global $wpdb;


 
if(isset($_POST['url'])){
	$url = $_POST['url'];
	$agente = $_POST['agente'];
	$espaco = $_POST['espaco'];
	$sql_update = "UPDATE wp_mapas SET mapas = '$url' WHERE type = 'url-api'";
	$update = $wpdb->query($sql_update);	
	if($update){
		$men = "Endereço atualizado.<br />";	
	}else{
		$men = "Erro ao atualizar o endereço.<br />";	
	}
	$sql_update = "UPDATE wp_mapas SET mapas = '$agente' WHERE type = 'agente'";
	$update = $wpdb->query($sql_update);	
	if($update){
		$men = "Agente atualizado.<br />";	
	}else{
		$men = "Erro ao atualizar o agente.<br />";	
	}
	$sql_update = "UPDATE wp_mapas SET mapas = '$espaco' WHERE type = 'espaco'";
	$update = $wpdb->query($sql_update);	
	if($update){
		$men = "Espaço atualizado.<br />.<br />";	
	}else{
		$men = "Erro ao atualizar o espaço.<br />";	
	}	

}


$my_db = new wpdb('ccspsite','lic54eca', 'ccspsite', 'ccspsite.mysql.dbaas.com.br');

$sql_sel_url = "SELECT * FROM wp_mapas WHERE type = 'url-api'";
$sql_sel_agente = "SELECT * FROM wp_mapas WHERE type = 'agente'";
$sql_sel_espaco = "SELECT * FROM wp_mapas WHERE type = 'espaco'";
$sql_sel_periodo = "SELECT * FROM wp_mapas WHERE type = 'periodo'";
$url = $my_db->get_results($sql_sel_url,ARRAY_A);
$agente = $my_db->get_results($sql_sel_agente,ARRAY_A);
$espaco = $my_db->get_results($sql_sel_espaco,ARRAY_A);
$periodo = $my_db->get_results($sql_sel_periodo,ARRAY_A);


?>
<div class="wrap"	>
<h1>Configuração do Mapas Culturais</h1>
<p><?php if(isset($men)){echo $men;} ?></p>
<p><?php if(isset($resultado)){echo $resultado;} ?></p>

<br /><br />
<form action="?page=config" method="post">
<p>Endereço de instalação do Mapas Culturais</p>
<p><input type='text' name='url' size='60'  value="<?php echo $url[0]['mapas']; ?>"/></p><br /><br />
<p>ID do Agente</p>
<p><input type='text' name='agente'   value="<?php echo $agente[0]['mapas']; ?>"/></p><br /><br />
<p>ID do Espaço</p>
<p><input type='text' name='espaco'  value="<?php echo $espaco[0]['mapas']; ?>"/></p><br /><br />

<input type="submit" value='salvar'/></p>
</form>
</div>
