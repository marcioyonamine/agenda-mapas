<?php 
wp_enqueue_script("jquery");  
wp_head();

global $wpdb;

if(isset($_POST['id'])){
	$valor = $_POST['valor'];
	$id = $_POST['id'];
	$update = $wpdb->query("UPDATE `wp_mapas` SET `edit` = '$valor' WHERE `wp` = '$id'");	
	if($update){
		$mensagem = "Base atualizada";	
	}else{
		$mensagem = "Erro ao atualizar a base";	
	}
}

$events = $wpdb->get_results("SELECT * FROM wp_mapas WHERE type = 'event'",ARRAY_A);



?>

<h1>Lista de eventos SPCultura / WordPress</h1>
<p><?php if(isset($mensagem)){echo $mensagem;}?></p>
<?php var_dump($_POST);?>
<table>
<tr>
<td>IDSPCultura</td>
<td>IDWP</td>
<td>Título</td>
<td>Sinc/Mapas</td>
</tr>
<?php 
for($i = 0; $i < count($events); $i++){
?>
<tr>
<td><?php echo $events[$i]['mapas']?></td><td><?php echo $events[$i]['wp']?></td><td><?php echo get_post_field('post_title', $events[$i]['wp']); ?></td>
<td>
<?php 
if($events[$i]['edit'] == 1){
?>
<form action="?page=lista-eventos" method="post">
<input type="hidden" name="id" value="<?php echo $events[$i]['wp']?>" />
<input type="hidden" name="valor" value="0">
<input type="submit" value='Sim' />
</form>
<?php 
}else{
?>
<form action="?page=lista-eventos" method="post">
<input type="hidden" name="id" value="<?php echo $events[$i]['wp']?>" />
<input type="hidden" name="valor" value="1">
<input type="submit" value="Não" />
</form>

<?php 
}
?>
</td>
</tr>

<?php
}
?>

</table>

<?php 
global $wpdb;
$events = $wpdb->get_results("SELECT * FROM wp_mapas WHERE type = 'event'",ARRAY_A);
echo "<pre>";
var_dump($events);
echo "</pre>";
?>