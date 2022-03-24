<?php
//Exibe erros PHP
@ini_set('display_errors', '1');
error_reporting(E_ALL); 
	global $wpdb;

if(isset($_GET['p'])){
	$p = $_GET['p'];	
}else{
	$p = "inicio";
	
}	

switch($p){	
case "inicio":
?>

<div class="wrap">
<h1>Equivalências</h1>
<p><?php if(isset($men)){echo $men;} ?></p>
<p><?php if(isset($resultado)){echo $resultado;} ?></p>

<br /><br /><br />
<p> Selecione o tipo de equivalência</p>
<p><a href="?page=equivalencias&p=espaco">Espaços / Venue</a></p>
<p><a href="?page=equivalencias&p=linguagem">Linguagens / Categorias </a></p>
<p><a href="?page=equivalencias&p=tags">Tags / Projetos </a></p>
</div>

<?php 
break;
case "espaco":
?>

<h1>Equivalências de Espaço / Venue</h1>

<?php 
break;
case "linguagem":

if(isset($_POST['atualizar'])){
	global $wpdb;
	
	$arr = array_keys($_POST);
	for($k = 0; $k < count($arr)-1; $k++){
		$mapas = $_POST[$arr[$k]];
		$wp = str_replace("linguagem_","",$arr[$k]);
		$existe = mapasWp($wp,"wp","equi_categoria");
		if($existe == false){
			$ins = "INSERT INTO ".$wpdb->prefix."mapas (`wp`, `mapas`, `type`) VALUES ('$wp','$mapas','equi_categoria')";
			$wpdb->query($ins);
		
		}else{
			$update = "UPDATE ".$wpdb->prefix."mapas SET `mapas` = '$mapas' WHERE wp = '$wp' AND type = 'equi_categoria'";
			$wpdb->query($update);

		}
	}
	
	
}



?>

<h1>Equivalências de Linguagem / Categorias</h1>

<?php
$category = get_terms('event-category',array('hide_empty'=>0));//custom category name 
?>
<form action="?page=equivalencias&p=linguagem" method="post">
<table border='1'>
	<tr>
		<th>Categoria Event Organiser WP</th>
		<th>Linguagem Mapas Culturais</th>
	</tr>

<?php for($i = 0; $i < count($category); $i++){ ?>
	<tr>
		<td><?php echo $category[$i]->name ?> </td>
		<td> 
		
		<select name="linguagem_<?php echo $category[$i]->slug ?>">
		<option value="false">Não há equivalência de linguagem</option>
		<?php $lista = listaLinguagensMapas(); ?>
		<?php for($j = 0; $j < count($lista); $j++){ 
			$wp_termo = mapasWp($category[$i]->slug,"wp","equi_categoria");
			
			if($wp_termo[0]['mapas'] == $lista[$j]['mapas']){
				$selected = "selected";
			}else{
				$selected = "";
			}	
		
		?>	
			
			<option value="<?php echo $lista[$j]['mapas'] ?>" <?php echo $selected;  ?> ><?php echo $lista[$j]['mapas'] ?></option>
		
		<?php


		} ?>
		
		</select>
		
		</td>

	</tr>


<?php } ?>

</table>
	
<p>
<input type="submit" name="atualizar" value="Salvar">
</p>
</form>

<?php
/*
$data = array(
'@select' => 'name,project,occurrences.{id,space.{name},rule},terms,longDescription,shortDescription',
 	   	'id' => 'eq(4034)'
  	);

//var_dump(mapasChamaAPI("https://culturaz.santoandre.sp.gov.br/api/event/find?",$data));

echo http_build_query($data);

echo "vardump da categoria slug: ";
var_dump(get_term_by('slug','cinema','event-category'));


	echo $update;
echo "<pre>";
var_dump($_POST);
echo "</pre>";

echo "<pre>";
var_dump($wp_termo);
echo "</pre>";

echo "<pre>";
var_dump($category);
echo "</pre>";

//foreach ($category as $catVal) {
  //  echo '<h2>'.$catVal->name.'</h2>'; 
 //}
*/

break;
case "tags":

if(isset($_POST['atualizar'])){
	global $wpdb;
	
	$arr = array_keys($_POST);
	for($k = 0; $k < count($arr)-1; $k++){
		$mapas = $_POST[$arr[$k]];
		$wp = str_replace("tag_","",$arr[$k]);
		$existe = mapasWp($wp,"wp","equi_tags");
		if($existe == false){
			$ins = "INSERT INTO ".$wpdb->prefix."mapas (`wp`, `mapas`, `type`) VALUES ('$wp','$mapas','equi_tags')";
			$wpdb->query($ins);
		
		}else{
			$update = "UPDATE ".$wpdb->prefix."mapas SET `mapas` = '$mapas' WHERE wp = '$wp' AND type = 'equi_tags'";
			$wpdb->query($update);

		}
	}
	
	
}


?>
<h1>Equivalências de Tags / Categorias</h1>



<?php
$sql_cat_tags = "SELECT * FROM ".$wpdb->prefix."mapas WHERE type LIKE 'equi_categoria' AND mapas = 'false'";
$ex = $wpdb->get_results($sql_cat_tags,ARRAY_A);
echo $sql_cat_tags;
?>
<form action="?page=equivalencias&p=tags" method="post">
<table border='1'>
	<tr>
		<th>Categoria Event Organiser WP</th>
		<th>Tags Mapas Culturais</th>
	</tr>

<?php for($i = 0; $i < count($ex); $i++){ ?>
	

	<tr>
		<td><?php $name = get_term_by( 'slug', $ex[$i]['wp'],  'event-category' ); echo $name->name  ?> </td>
		<td> 
		<?php 
		
		 $equi = mapasWp($ex[$i]['wp'],"wp","equi_tags");
		?>
		<input type="text" name="tag_<?php echo $ex[$i]['wp'] ?>" value="<?php if(isset($equi[0]['mapas'])){echo $equi[0]['mapas'];} ?>">
		
		</td>

	</tr>


<?php } ?>

</table>
	
<p>
<input type="submit" name="atualizar" value="Salvar">
</p>
</form>

<?php

break; //final
} 
?>