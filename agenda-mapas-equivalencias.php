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
<p><a href="?page=equivalencia&p=espaco">Espaços / Venue</a></p>
<p><a href="?page=equivalencia&p=linguagem">Linguagens / Categorias </a></p>
<p><a href="?page=equivalencia&p=tags">Tags / Projetos </a></p>
</div>

<?php 
break;
case "espaco":
?>

<h1>Equivalências de Espaço / Venue</h1>

<?php 
break;
case "linguagem":
?>

<h1>Equivalências de Linguagem / Categorias</h1>


<?php 
break;
case "tags":
?>
<h1>Equivalências de Tags / Projetos</h1>


<?php

break; //final
} 
?>