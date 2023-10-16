<?php
require('../lib/base.php');

$jsonStr = file_get_contents("php://input");
$dados = json_decode($jsonStr);


/***** - Inicio processo para criacao do log - *****/
$name  = 'logAPIAssinaturaVileve.log';
$text .= '\n'.date('d/m/Y');	
$text .= '-------------------INICIO-JSON-------------------\n';	
$text .= $jsonStr;	
$text .= "\n".date('d/m/Y');	
$text .= '-------------------FIM--------------------\n';

$file = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);
/***** - Fim processo para criacao do log - *****/



global $numeroContrato, $codigoAssociado, $dataAssinatura, $retorno;

/***** - Inicio processo de busca das informacoes para utilizar nas funcoes especificas - *****/
$retorno 				= '';
$numeroContrato 		= $dados->numeroContrato;
$codigoAssociado 		= $dados->codigoAssociado;
$dataAssinatura 		= $dados->dataAssinatura;
/***** - Fim processo de busca das informacoes para utilizar nas funcoes especificas - *****/


/***** - Inicio validacao do header, para verificar token e rotinas de autorizacao - *****/
$headers = getallheaders();
$token = null;

foreach ($headers as $header => $value){
	if(strtoupper($header) == 'AUTHORIZATION'){
		$token = $value;
		break;
	}	
}

if(!$token){	
	responseAPI(1);
	return false;
}

if($token != retornaValorConfiguracao('TOKEN_API_ASSOC_PF')){	
	responseAPI(2);
	return false;
}

/***** - Final validacao do header, para verificar token e rotinas de autorizacao - *****/


if(!$dados){	
	responseAPI(0,'Dados não encontrados, favor verificar se o arquivo JSON está no padrão correto.');
	return false;
}else{
	atualizaAssinatura($dadosAssoc);
}


function atualizaAssinatura($dadosAssoc){	
	
	global $numeroContrato, $codigoAssociado, $dataAssinatura, $retorno;

	$query  = ' UPDATE VND1001_ON SET ';
	$query .= ' 	DATA_ASSINATURA_CONTRATO = ' . aspas($dataAssinatura);
	$query .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	$query .= ' AND NUMERO_CONTRATO = ' . aspas($numeroContrato);
	
	if (!jn_query($query)) {		
		responseAPI(0, 'Não foi possível cadastrar a assinatura do associado!');
		return false; // saio retornando false
	}else{		
		responseAPI(200);
	}	
}

function responseAPI($codErro, $mensagem = ''){

	$mensagemApresentar = '';
	$retornoResponse 	= Array();	
	$retornoResponse['Sucesso'] = false;

	if($codErro == 0){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = $mensagem;
	}elseif($codErro == 1 || $codErro == 2){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = 'Acesso Negado';	
	}elseif($codErro == 200){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = 'Processo realizado com sucesso';	
	}


	if($codErro != 200){
		header('HTTP/1.0 401 Unauthorized');
		$retornoResponse['Sucesso'] = false;
	}else{
		$retornoResponse['Sucesso'] = true;
	}

	echo json_encode($retornoResponse);
}


if (!function_exists('getallheaders')) { 
    function getallheaders() 
    { 
        $headers = ''; 
       	foreach ($_SERVER as $name => $value) 
       	{ 
           	if (substr($name, 0, 5) == 'HTTP_'){ 
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
           	}
       	} 
       	return $headers; 
    } 
} 

?>