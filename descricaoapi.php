<h1>descrição da api mapas</h1>
<?php 

function jsonMapas($get_addr){

$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
return $page;
	
}
$get_addr = "http://spcultura.prefeitura.sp.gov.br/api/event/describe";

$evento = json_decode(jsonMapas($get_addr));

echo "<pre>";
var_dump($evento);
echo "</pre>";

?>