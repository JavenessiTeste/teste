<?php 
require('../lib/base.php');
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

$i = 0;
$quantidade = 500;

$retornoGet = getopt('j:');
$codigoIdProcesso = $retornoGet['j'];
$teste = '';
while($i < $quantidade){
    
    $sqlEdicao   = linhaJsonEdicao('CODIGO_ASSOCIADO', $codigoIdProcesso);        
    $sqlEdicao  .= linhaJsonEdicao('HORA_REGISTRO', date('h:i'));
    $sqlEdicao  .= linhaJsonEdicao('NUMERO_INDICE_INSERT', $i);   
    
    $teste .= $sqlEdicao;
    gravaEdicao('ESP_TESTE_NEW', $sqlEdicao, 'I', '');

    $i++;
}


?>