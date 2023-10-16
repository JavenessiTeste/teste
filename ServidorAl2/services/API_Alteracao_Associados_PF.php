<?php
require('../lib/base.php');

$jsonStr = file_get_contents("php://input");
$dados = json_decode($jsonStr);

/***** - Inicio processo para criacao do log - *****/
$name  = '../../ServidorCliente/API_AlteracaoAssocPF/logAPIAlteracaoAssocPF.log';
$text .= '\n'.date('d/m/Y');	
$text .= '-------------------INICIO-JSON-------------------\n';	
$text .= $jsonStr;	
$text .= "\n".date('d/m/Y');	
$text .= '-------------------FIM--------------------\n';

$file = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);
/***** - Fim processo para criacao do log - *****/


$codigoEmpresa 			= '400';
$dadosAssociado			= $dados->beneficiario;

global $codigoAssociado, $codigoEmpresa;


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
	responseAPI(401);
	return false;
}

if($token != retornaValorConfiguracao('TOKEN_API_ASSOC_PF')){	
	responseAPI(401);
	return false;
}

/***** - Final validacao do header, para verificar token e rotinas de autorizacao - *****/


if(!$dados){	
	responseAPI(401,'Dados não encontrados, favor verificar se o arquivo JSON está no padrão correto.');
	return false;
}

alteraAssociado($dadosAssociado);

function alteraAssociado($dadosAssociado){
	global $codigoAssociado;

	$query  = ' SELECT CODIGO_ASSOCIADO FROM PS1000 WHERE NUMERO_CPF = '. aspas($dadosAssociado->cpf);
	$resultado = qryUmRegistro($query);
	
	$codigoAssociado = $resultado->CODIGO_ASSOCIADO;

	$criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	$sqlEdicaoPs1000   = linhaJsonEdicao('NOME_MAE',$dadosAssociado->nome_mae);
	$sqlEdicaoPs1000   = linhaJsonEdicao('NUMERO_RG',$dadosAssociado->rg_numero);

	gravaEdicao('PS1000', $sqlEdicaoPs1000, 'A', $criterioWhereGravacao);
	
	if (!jn_query($query)) {		
		responseAPI(401, 'Não foi possível alterar os dados do associado!');
		return false; // saio retornando false
	}else{
		salvaDadosContato($dadosAssociado);
	}	
}

function salvaDadosContato($dadosAssoc){	
	global $codigoAssociado, $codigoEmpresa;	
	
	$dadosEndereco = $dadosAssoc->endereco;		
	$codigoAssociado = $associado;

	$logradouroEndereco = $dadosEndereco->logradouro;
	if($dadosEndereco->numero)
		$logradouroEndereco .= ', ' . $dadosEndereco->numero;
	
	if($dadosEndereco->complemento)
		$logradouroEndereco .= ' - ' . $dadosEndereco->complemento;

	$criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	$sqlEdicaoPs1001   = linhaJsonEdicao('ENDERECO_EMAIL',$dadosAssoc->email);
	$sqlEdicaoPs1001   = linhaJsonEdicao('ENDERECO',$logradouroEndereco);
	$sqlEdicaoPs1001   = linhaJsonEdicao('BAIRRO',$dadosEndereco->bairro);
	$sqlEdicaoPs1001   = linhaJsonEdicao('CIDADE',$dadosEndereco->cidade);
	$sqlEdicaoPs1001   = linhaJsonEdicao('ESTADO',$dadosEndereco->uf);
	$sqlEdicaoPs1001   = linhaJsonEdicao('CEP',$dadosEndereco->cep);

	gravaEdicao('PS1001', $sqlEdicaoPs1001, 'A', $criterioWhereGravacao);
	
	if (! jn_query($query)) {
		responseAPI(401, 'Não foi possível gravar os dados de contato ou contrato!');
		return false; // saio retornando false
	}else{
		responseAPI(200, 'Alteração realizada com sucesso');
	}
}

function responseAPI($codErro, $mensagem = ''){

	global $codigoAssociado;

	$mensagemApresentar = '';
	$retornoResponse 	= Array();	
	$retornoResponse['Sucesso'] = false;

	if($codErro == 401){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = $mensagem;
	}elseif($codErro == 200){
		$retornoResponse['ObjErros']['codErro'] = $codErro;		
		$retornoResponse['ObjErros']['mensagem'] = $mensagem;	
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