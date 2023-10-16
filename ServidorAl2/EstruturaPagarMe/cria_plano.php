<?php
require('../lib/base.php');
require("vendor/autoload.php");

$token = retornaValorConfiguracao('CHAVE_PAGARME');

function criaPlano($valor){
	global $token;
	
	$pagarme = new PagarMe\Client($token);

	$codPlano = $pagarme->plans()->create([
	  'amount' => $valor,
	  'days' => '30',
	  'name' => 'Plano ' . toMoeda($valor)
	]);
	
	$insertPlano  = 'INSERT INTO ESP_PLANOS_PAGARME (CODIGO_PLANO_PAGARME, DATA_CRIACAO, VALOR_PLANO) VALUES ';
	$insertPlano .= '( ' . aspas($codPlano->id) . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas($valor) . ' )';
	jn_query($insertPlano);
	
	return $codPlano->id;
}


