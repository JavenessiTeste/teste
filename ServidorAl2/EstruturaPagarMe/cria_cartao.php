<?php
require('../lib/base.php');
require("vendor/autoload.php");

$empresa = $dadosInput['codigoEmpresa'];
$associado = $dadosInput['codigoTitular'];
$numeroCartao = $dadosInput['dadosCartao']['Numero'];
$nomeCartao = $dadosInput['dadosCartao']['Nome'];
$validadeCartao = $dadosInput['dadosCartao']['Vencimento'];
$codigoSeguranca = $dadosInput['dadosCartao']['CodigoSeg'];


$queryPlano = 'SELECT CODIGO_PAGARME FROM PS1030 WHERE CODIGO_PLANO = ' . aspas($rowAssoc->CODIGO_PLANO);
$resPlano = jn_query($queryPlano);
$rowPlano = jn_fetch_object($resPlano);
$idPlano = $rowPlano->CODIGO_PAGARME;

$token = retornaValorConfiguracao('CHAVE_PAGARME');

geraCartao();	

function geraCartao(){
	global $token;
	global $associado;
	
	$pagarme = new PagarMe\Client($token);

	$card = $pagarme->cards()->create([
		'holder_name' => $nomeCartao,
		'number' => $numeroCartao,
		'expiration_date' => $validadeCartao,
		'cvv' => $codigoSeguranca
	]);

	$queryCard = 'UPDATE VND1000_ON SET CARTAO_PAGARME = ' . aspas($card->id) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($associado);
	jn_query($queryCard);
	
}



?>