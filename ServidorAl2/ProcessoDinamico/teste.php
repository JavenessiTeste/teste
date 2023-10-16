<?php 

require('../lib/base.php');
require('../private/autentica.php');


$retorno['HTML'] ='POST: <br>';

foreach ($_POST as $key => $value){
$retorno['HTML'] .=$key.'=>'. $value.'<br>';
}

$retorno['HTML'] .='<br><br><br>';
$retorno['HTML'] .='<b>FILES:</b> <br>';

foreach ($_FILES as $key => $value){
$retorno['HTML'] .=$key.'=>'. $value.'<br>';
}


$retorno['MSG']  = 'Teste';

//$retorno['VOLTAR_FILTROS']  = true;

//$retorno['DESTINO']  = 'site/cadastroDinamico';
//$retorno['DADOS_DESTINO']['tabela'] ='PS5000';

echo json_encode($retorno);

?>