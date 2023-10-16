<?php
require_once('../lib/base.php');

$registro  = $_GET['registro'];
$codigo    = $_GET['codigo'];

$sqlFatura = "select * from PS1020 where data_pagamento is null and  numero_registro = ".aspas($registro);
	
$resFatura  = jn_query($sqlFatura);
if($rowFatura = jn_fetch_object($resFatura)) {
	$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
	$dataVencimento = $rowFatura->DATA_VENCIMENTO;//->format('AAAA-MM-DD');
	$valorFatura 	= str_replace('.','',str_replace(',','',number_format($rowFatura->VALOR_FATURA,2)));
	$valorFatura    = str_pad($valorFatura, 15, '0', STR_PAD_LEFT );
	$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
	$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
	
	if($codigoAssociado != '' && $codigoAssociado!=$codigo){				
		echo 'ERRO: CODIGO ASSOCIADO ERRADO';
		exit;		
	}elseif($codigoEmpresa != '' && $codigoEmpresa!=$codigo){		
		echo 'ERRO: CODIGO EMPRESA ERRADO';
		exit;	
	}else{
		echo 'ERRO: CODIGO NAO ENCONTRADO';
		exit;
	}
		
	$retorno = integracaoBoleto($codigoAssociado,$codigoEmpresa,$nome,$documento,$dataVencimento,$valorFatura);
	echo json_encode($retorno);
	
}

function integracaoBoleto($codigoAssociado,$codigoEmpresa,$nome,$documento,$dataVencimento,$valorFatura){
	
	$unixTime = time();

	$url = 'https://mtlst-api.planiumbank.io/test/cobranca/v1';
	$headers = array("Content-Type: application/json","Accept:application/json");

	$data = 	'{
					"operacao": "criar_boleto",
					"timestamp": ' . $unixTime . ',
					"cedente": {
						"doc": "' . $documento . '"
					},
					"sacado": {
					"doc": "' . $documento . '",
					"nome": "' . $nome . '"
					},
					"valor": ' . $valorFatura . ',
					"vencimento": "' . $dataVencimento . '",
					"generico_txt": "Mensalidade"
				}';

	$ch = CURL_INIT();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);

	//$body = json_decode($body);		
	return $body;
	
}
?>