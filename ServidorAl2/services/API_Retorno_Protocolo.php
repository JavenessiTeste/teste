<?php

require('../lib/base.php');

$headers = getallheaders();
$tokenApi = retornaValorConfiguracao('TOKEN_API_PROTOCOLO');

foreach ($headers as $header => $value){
	if(strtoupper($header) == 'AUTHORIZATION'){
		$token = $value;
		break;
	}	
}

if(!$token){	
	responseAPI(404,'Chave não enviada');
	return false;
}

if($token != $tokenApi){	
	responseAPI(401,'Chave informada incorreta');
	return false;
}

$dados = file_get_contents("php://input");
$dados = json_decode($dados, True);

$numeroProtocolo = $dados['numero_protocolo'];
$codigoAssociado = $dados['codigo_associado'];

$queryProtocolo = "SELECT NUMERO_PROTOCOLO_GERAL, CODIGO_CADASTRO_CONTATO FROM PS6450 WHERE NUMERO_PROTOCOLO_GERAL =". aspas($numeroProtocolo);
$resultadoProtocolo = qryUmRegistro($queryProtocolo);

$protocolo = $resultadoProtocolo->NUMERO_PROTOCOLO_GERAL;
$codProtocolo = $resultadoProtocolo->CODIGO_CADASTRO_CONTATO;

if($resultadoProtocolo == false){
    responseAPI(404,"Número de protocolo não encontrado, entre em contato com a central de atendimento através do (11) 4445-9080 ou (11)91349-2236");
    return false;
}

if($codigoAssociado != $codProtocolo){
	responseAPI(404,'Carteirinha incorreta ou inexistente, reveja os dados e tente novamente!');
	return false;
}

$queryNome = "SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO =". aspas($codigoAssociado);
$resultadoNome = qryUmRegistro($queryNome);

responseAPI(200,'Processo realizado com sucesso',$resultadoNome->NOME_ASSOCIADO);

function responseAPI($codErro, $mensagem = '', $nomeAssociado = null){

	$retornoResponse 	= Array();	
	$retornoResponse['Sucesso'] = false;

	if($codErro == 404){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = $mensagem;
	}elseif($codErro == 401){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = $mensagem;
	}elseif($codErro == 200){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['nomeAssociado'] = $nomeAssociado;
		$retornoResponse['ObjErros']['mensagem'] = $mensagem;
	}

	if($codErro != 200){
		header('HTTP/1.0 '.$codErro.'Unauthorized');
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