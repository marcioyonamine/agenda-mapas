<?php

/*
Estudos para o uso da API dos mapas culturais com base no SPCultura
Documentação da API: https://github.com/hacklabr/mapasculturais/blob/master/doc/api.md
*/
/* 
  */
  
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

echo "<h1>Entidade Evento</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/event/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

echo "<h1>Entidade Espaço</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/space/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

echo "<h1>Entidade Agente</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/agent/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

echo "<h1>Entidade Projeto</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/project/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

echo "<h1>Entidade Ocorrências</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/eventOccurrence/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

echo "<h1>Entidade Selo</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/seal/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

echo "<h1>Entidade Inscricao</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/registration/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

echo "<h1>Entidade Inscricao</h1>";
$url = "https://culturaz.santoandre.sp.gov.br/api/event/createSealRelation/describe";
$get_addr = $url;
$ch = curl_init($get_addr);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($ch);
$evento = json_decode($page);
$ccsp = converterObjParaArray($evento);
echo "<pre>";
print_r($ccsp);
echo "</pre>";

?>