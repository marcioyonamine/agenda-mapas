<?php
//Exibe erros PHP
@ini_set('display_errors', '1');
error_reporting(E_ALL); 
	global $wpdb;
	$tabela = $wpdb->prefix."mapas";
//verifica se existem as configurações de mapa e selo
$ver_urlapi = "SELECT * FROM $tabela WHERE type = 'url-api'";
$ver_selo = "SELECT * FROM $tabela WHERE type = 'selo'";
$agora = date('Y-m-d H:i:s');
if($wpdb->query($ver_urlapi) == false){
	$urlapi_insert = "INSERT INTO `$tabela` (`id`, `wp`, `mapas`, `type`, `edit`) VALUES (NULL, '', '', 'url-api', '')";
	$wpdb->query($urlapi_insert);
}

if($wpdb->query($ver_selo) == false){
	$urlapi_insert = "INSERT INTO `$tabela` (`id`, `wp`, `mapas`, `type`, `edit`) VALUES (NULL, '', '', 'selo', '')";
	$wpdb->query($urlapi_insert);
}

 $men = "";
if(isset($_POST['url']) AND $_POST['url'] != "" AND $_POST['selo'] != ""){
	$url = $_POST['url'];
	$selo = $_POST['selo'];
	$sql_update = "UPDATE $tabela SET mapas = '$url' WHERE type = 'url-api'";
	$update = $wpdb->query($sql_update);	
	if($update){
		$men .= "Endereço atualizado.<br />";	
	}else{
		$men .= "Erro ao atualizar o endereço.<br />";	
	}
	$sql_update = "UPDATE $tabela SET mapas = '$selo' WHERE type = 'selo'";
	$update = $wpdb->query($sql_update);	
	if($update){
		$men .= "Selo atualizado.<br />";	
	}else{
		$men .= "Erro ao atualizar o selo.<br />";	
	}

}

$url = $wpdb->get_results("SELECT * FROM $tabela WHERE type = 'url-api'",ARRAY_A);
$selo = $wpdb->get_results("SELECT * FROM $tabela WHERE type = 'selo'",ARRAY_A);

//var_dump($url);
//var_dump($selo);

?>
<div class="wrap"	>
<h1>Configuração do Mapas Culturais</h1>
<p><?php if(isset($men)){echo $men;} ?></p>
<p><?php if(isset($resultado)){echo $resultado;} ?></p>

<br /><br />
<form action="?page=config" method="post">
<p>Endereço de instalação do Mapas Culturais</p>
<p><input type='text' name='url' size='60'  value="<?php echo $url[0]['mapas']; ?>"/></p><br /><br />
<p>ID do Selo</p>
<p><input type='text' name='selo'   value="<?php echo $selo[0]['mapas'];?>"/></p><br /><br />

<input type="submit" value='salvar'/></p>
</form>
</div>
