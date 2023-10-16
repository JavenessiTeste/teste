<?php
require('../lib/base.php');

$jsonStr = file_get_contents("php://input");
$dados = json_decode($jsonStr);

/***** - Inicio processo para criacao do log - *****/
$name  = 'logAPI_Adesao_PF_GerenciarSC.log';
$text .= '\n'.date('d/m/Y');	
$text .= '-------------------INICIO-JSON-------------------\n';	
$text .= $jsonStr;	
$text .= "\n".date('d/m/Y');	
$text .= '-------------------FIM--------------------\n';

$file = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);
/***** - Fim processo para criacao do log - *****/


/***** - Inicio processo de busca das informacoes para utilizar nas funcoes especificas - *****/
global $codPlano, $codTitular, $linkProposta;

$retorno 				= '';
$codigoEmpresa 			= '400';
$codPlano				= null;
$codTitular				= null;
$dataAssinatura 		= $dados->data_assinatura;
$numeroContrato 		= $dados->numero_proposta;
$linkProposta 			= $dados->proposta_url;

$dadosContrato			= Array();
$dadosContrato 			= $dados->contrato;

$dadosAssociados		= Array();
$dadosAssociados		= $dados->beneficiarios;

$dadosEmpresa			= Array();
$dadosEmpresa			= $dados->empresa;

$numRegTmp1000_net		= '';
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

if($token != retornaValorConfiguracao('TOKEN_API_GERENCIAR_SC')){	
	responseAPI(2);
	return false;
}

/***** - Final validacao do header, para verificar token e rotinas de autorizacao - *****/

/***** --- Foi criada a regra para validar se os dados enviados são PF ou PJ, para não precisar de duas APIs */

if(!$dados){	
	responseAPI(0,'Dados não encontrados, favor verificar se o arquivo JSON está no padrão correto.');
	return false;
}


if($dadosEmpresa){
	if($dadosEmpresa->cnpj == ''){
		echo 'Erro: Nao foi encontrado o CNPJ da empresa.';	
		return false;
	}else{
		salvaEmpresa();
	}	
}	

foreach($dadosAssociados as $benef){		
	salvaAssociado($benef);		
}

//Se chegou até aqui, não acorreu nenhum erro na importação - Retorno 200
responseAPI(200);			




 /********* Fim da validação PF - PJ */




function salvaAssociado($dadosAssoc){	
	global $codigoEmpresa;	
	global $dadosContrato;		
	global $dataAssinatura;		
	global $codPlano;
	global $codTitular;	
	global $retorno;	
	
	//Se for dependente, pode usar o plano do titular
	if($dadosAssoc->produtos->saude->codigo_plano)
		$codPlano = $dadosAssoc->produtos->saude->codigo_plano;

	if(!$codPlano){
		responseAPI(0, 'Não foi possível gravar o associado - Cód Plano não informado!');
		return false; // saio retornando false
	}

	$queryPlano = 'SELECT CODIGO_PLANO FROM PS1030 WHERE CODIGO_PLANO = ' . aspas($codPlano);
	$resPlano = jn_query($queryPlano);
	if(!$rowPlano = jn_fetch_object($resPlano)){
		responseAPI(0, 'Não foi possível gravar o associado - Cód Plano não existe na plataforma do Aliança!');
		return false; // saio retornando false
	}
	
	$dataNasc = SqlToData($dadosAssoc->data_nascimento);
	$dataNasc = str_replace("'",'',$dataNasc);

	$flagPlanoFamiliar = 'S';			
		
	$numRegTmp1000_net = jn_gerasequencial('TMP1000_NET');
	$numRegTmp1000_net *= -1;
			
	$tpAssoc = '';
	$codParentesco = $dadosAssoc->parentesco;
	if($dadosAssoc->tipo == '1'){		
		$codTitular = $numRegTmp1000_net;
		$codParentesco = 2;
		$tpAssoc = 'T';
	}else{

		$queryTit = 'SELECT CODIGO_ASSOCIADO FROM TMP1000_NET WHERE CODIGO_TITULAR =' . aspas($codTitular);
		$resTit = jn_query($queryTit);
		$rowTit = jn_fetch_object($resTit);
		$codTitular = $rowTit->CODIGO_ASSOCIADO;
		
		$tpAssoc = 'D';
	}	

	$dataAss = explode('-',$dataAssinatura);
	$montaDataAss = $dataAss[2] . '/' . $dataAss[1] . '/' . $dataAss[0];
	$dataVigencia = $montaDataAss;
	$dataPrimeitoFat = $montaDataAss;	

	$query  = ' INSERT INTO VND1000_ON ( ';
	$query .= ' CODIGO_PLANO, CODIGO_CARENCIA, CODIGO_TABELA_PRECO, CODIGO_MOTIVO_INCLUSAO, NOME_ASSOCIADO, UUID, TITULAR_UUID, DATA_VALIDA_CARENCIA, DATA_PRIMEIRO_FATURAMENTO, ';
	$query .= ' SEXO, DATA_NASCIMENTO, DATA_DIGITACAO, DATA_ADMISSAO, DATA_EMISSAO_RG, CODIGO_EMPRESA, FLAG_PLANOFAMILIAR, DESCRICAO_OBSERVACAO, NUMERO_REGISTRO_NIVEL1, ULTIMO_STATUS, ';
	$query .= ' NOME_MAE, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG, NATUREZA_RG, CODIGO_ESTADO_CIVIL, CODIGO_PARENTESCO, CODIGO_ASSOCIADO, CODIGO_TITULAR, TIPO_ASSOCIADO, CODIGO_CNS, SITUACAO_CADASTRAL, NUMERO_DECLARACAO_NASC_VIVO ';
	$query .= ' )VALUES ( ';
	$query .= aspas($codPlano) . ", ";
	$query .= aspas($dadosAssoc->codigo_carencia) . ", ";
	$query .= aspas($dadosAssoc->codigo_tabela_preco) . ", ";
	$query .= aspasNull($dadosAssoc->codigo_motivo_inclusao) . ", ";
	$query .= aspas(remove_caracteres($dadosAssoc->nome)) . ", ";
	$query .= aspas($dadosAssoc->uuid) . ", ";
	$query .= aspas($idTit) . ", ";
	$query .= " GETDATE(), ";
	$query .= " GETDATE(), ";
	$query .= aspas($dadosAssoc->sexo) . ", ";
	$query .= dataToSql($dataNasc) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql($dadosAssoc->data_emissao_rg) . ", ";
	$query .= aspas($codigoEmpresa) . ", ";	
	$query .= aspasNull('S') . ", ";	
	$query .= aspasNull($descObservacao) . ", ";	
	$query .= aspasNull('1') . ", ";	
	$query .= aspasNull('AGUARDANDO_AVALIACAO') . ", ";
	$query .= aspasNull($dadosAssoc->nome_mae) . ", ";	
	$query .= aspasNull($dadosAssoc->cpf) . ", ";
	$query .= aspasNull($dadosAssoc->rg_numero) . ", ";
	$query .= aspasNull($dadosAssoc->orgao_emissor) . ", ";
	$query .= aspasNull($dadosAssoc->natureza_rg) . ", ";	
	$query .= aspasNull($dadosAssoc->estado_civil) . ", ";
	$query .= aspasNull($codParentesco) . ", ";
	$query .= aspas($numRegTmp1000_net) . ", ";
	$query .= aspas($codTitular) . ", ";
	$query .= aspas($tpAssoc) . ", ";	
	$query .= aspasNull($dadosAssoc->cns) . ",";
	$query .= aspas('Pendente Para Analise') . ",";
	$query .= aspasNull($_POST['numero_declaracao_nasc_vivo']);
	$query .= " )";	

	if (!jn_query($query)) {			
		responseAPI(0, 'Não foi possível gravar o associado!');
		return false; // saio retornando false
	}elseif($dadosAssoc->titular_uuid == '' && $codigoEmpresa == '400'){		
		salvaDadosContato($dadosAssoc, $numRegTmp1000_net);
	}elseif($dadosAssoc->titular_uuid == '' && $codigoEmpresa != '400'){
		salvaStatus($dadosAssoc, $numRegTmp1000_net);
	}
}

function salvaDadosContato($dadosAssoc, $associado){
	global $retorno;
	global $codigoEmpresa;	
	global $numeroContrato;
	global $dadosContrato;
	
	$dadosEndereco = $dadosAssoc->endereco1;		
	
	$codigoAssociado = $associado;
	$enderecoEmail = $dadosAssoc->email;
	$numeroCep = $dadosEndereco->cep;
	$logradouroEndereco = $dadosEndereco->logradouro;

	if($dadosEndereco->numero){
		$logradouroEndereco .= ', ' . $dadosEndereco->numero;
	}
	
	if($dadosEndereco->complemento){
		$logradouroEndereco .= ' - ' . $dadosEndereco->complemento;
	}

	$bairro = $dadosEndereco->bairro;
	$cidade = $dadosEndereco->cidade;
	$estado = $dadosEndereco->uf;

	$diaVencimento = $dadosContrato->dia_vencimento;
	$dataVigencia = $dadosContrato->data_vigencia;	
	
	$query  = 'INSERT INTO VND1001_ON (CODIGO_ASSOCIADO, NUMERO_TELEFONE_01, ENDERECO, BAIRRO, ';
	$query .= ' CIDADE, CEP, ESTADO, ENDERECO_EMAIL, NUMERO_CONTRATO, DATA_PRIMEIRO_FATURAMENTO, DIA_VENCIMENTO, FORMA_PAGAMENTO, SEGMENTACAO) ';
	$query .= 'VALUES ( ';        
	$query .= aspasNull($associado) . ", ";		
	$query .= aspas($dadosAssoc->numero_telefone)  . ", ";
	$query .= aspas(substr($logradouroEndereco,0,45)) . ", ";
	$query .= aspas(substr($bairro,0,24)) . ", ";
	$query .= aspas($cidade) . ", ";
	$query .= aspas($numeroCep) . ", ";
	$query .= aspas($estado). ",";
	$query .= aspasNull($enderecoEmail) . ", ";
	$query .= aspas($numeroContrato) . ", ";
	$query .= aspas($dataVigencia) . ", ";
	$query .= aspas($diaVencimento) . ", ";
	$query .= aspas(jn_utf8_decode($dadosContrato->forma_pagamento->tipo)) . ", ";
	$query .= aspas($dadosContrato->saude->segmentacao);
	$query .= " )";		
	
	if (! jn_query($query)) {
		responseAPI(0, 'Não foi possível gravar os dados de contato ou contrato!');			
		return false; // saio retornando false
	}else{
		salvaStatus($dadosAssoc, $associado);
	}

}


function salvaStatus($dadosAssoc, $associado){

	global $linkProposta;

	$query  = ' INSERT INTO VND1000STATUS_ON (CODIGO_ASSOCIADO, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS,  ';
	$query .= '  REMETENTE_STATUS, DESTINATARIO_STATUS) ';
	$query .= ' VALUES ( ';        
	$query .= aspas($associado) . ", ";		
	$query .= aspas('AGUARDANDO_AVALIACAO')  . ", ";
	$query .= DataToSql(date('d/m/Y')) . ", ";
	$query .= aspas(date('h:i')) . ", ";
	$query .= aspas('BENEFICIARIO') . ", ";
	$query .= aspas('BENEFICIARIO');
	$query .= " )";		

	if (! jn_query($query)) {
		responseAPI(0, 'Não foi possível gravar o status');			
		return false; // saio retornando false
	}elseif($linkProposta){
		salvaLinkContrato($dadosAssoc, $associado);
	}elseif($dadosAssoc->links){
		responseAPI(0, 'Não foi implementado o recebimento de links.');			
		salvaLinksArquivos($dadosAssoc, $associado);
	}else{
		responseAPI(200);
	}
}

function salvaEmpresa(){	
	global $dadosEmpresa;
	global $codigoEmpresa;	
	
	$codigoEmpresa = jn_gerasequencial('VND1010_ON');
	
	$nome = substr($dadosEmpresa->razao_social,0,50);
	$nomeFantasia = substr($dadosEmpresa->nome_fantasia,0,50);
	
	$insertEmpresa  = ' INSERT INTO VND1010_ON ( ';
	$insertEmpresa .= ' 	CODIGO_EMPRESA, DATA_ADMISSAO, NOME_EMPRESA, NUMERO_CNPJ, ULTIMO_STATUS ';
	$insertEmpresa .= ' ) VALUES ( ';
	$insertEmpresa .= aspas($codigoEmpresa) . ',  getdate(), ' . aspas($nomeFantasia) . ',' . aspas($dadosEmpresa->cnpj) . ', "AGUARDANDO_AVALIACAO" )';	

	if (! jn_query($insertEmpresa)) {
		responseAPI(0, 'Erro: Nao foi possivel cadastrar a empresa');			
		return false; // saio retornando false
	}else{
		salvaContratoPJ();
	}

}

function salvaContratoPJ(){
	global $retorno;	
	global $codigoEmpresa;		
	global $numeroContrato;
	global $dadosEmpresa;		

	$dadosEndereco = $dadosEmpresa->endereco;		
	$codigoAssociado = $associado;
	$enderecoEmail = $dadosEmpresa->email_responsavel;
	$numeroCep = $dadosEndereco->cep;
	$logradouroEndereco = $dadosEndereco->logradouro;

	if($dadosEndereco->numero){
		$logradouroEndereco .= ', ' . $dadosEndereco->numero;
	}
	
	if($dadosEndereco->complemento){
		$logradouroEndereco .= ' - ' . $dadosEndereco->complemento;
	}

	$bairro = $dadosEndereco->bairro;
	$cidade = $dadosEndereco->cidade;
	$estado = $dadosEndereco->uf;

	$diaVencimento = $dadosEmpresa->dia_vencimento;
	
	$query  = 'INSERT INTO VND1001_ON (CODIGO_EMPRESA, NUMERO_TELEFONE_01, ENDERECO, BAIRRO, ';
	$query .= ' CIDADE, CEP, ESTADO, ENDERECO_EMAIL, NUMERO_CONTRATO, DIA_VENCIMENTO) ';
	$query .= 'VALUES ( ';        
	$query .= aspasNull($codigoEmpresa) . ", ";		
	$query .= aspas($dadosEmpresa->telefone1)  . ", ";
	$query .= aspas(substr($logradouroEndereco,0,45)) . ", ";
	$query .= aspas(substr($bairro,0,24)) . ", ";
	$query .= aspas($cidade) . ", ";
	$query .= aspas($numeroCep) . ", ";
	$query .= aspas($estado). ",";
	$query .= aspasNull($enderecoEmail) . ", ";
	$query .= aspas($numeroContrato) . ", ";	
	$query .= aspas($diaVencimento);
	$query .= " )";			

	if (! jn_query($query)) {
		responseAPI(0, 'Não foi possível gravar os dados de contato ou contrato!');			
		return false; // saio retornando false
	}else{
		salvaStatusPJ($codigoEmpresa);
	}
}

function salvaStatusPJ($codigoEmpresa){

	$query  = ' INSERT INTO VND1010STATUS_ON (CODIGO_EMPRESA, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS,  ';
	$query .= '  REMETENTE_STATUS, DESTINATARIO_STATUS) ';
	$query .= ' VALUES ( ';
	$query .= aspas($codigoEmpresa) . ", ";		
	$query .= aspas('AGUARDANDO_AVALIACAO')  . ", ";
	$query .= DataToSql(date('d/m/Y')) . ", ";
	$query .= aspas(date('h:i')) . ", ";
	$query .= aspas('EMPRESA') . ", ";
	$query .= aspas('EMPRESA');
	$query .= " )";		

	if (! jn_query($query)) {
		responseAPI(0, 'Não foi possível gravar o status da empresa');			
		return false; // saio retornando false
	}
}


function salvaLinkContrato($dadosAssoc, $associado){

	global $linkProposta;

	$query  = ' INSERT INTO VND1002_ON (CODIGO_ASSOCIADO, CODIGO_MODELO, NOME_ARQUIVO) VALUES ( ';        
	$query .= aspas($associado) . ", ";		
	$query .= aspas('1')  . ", ";	
	$query .= aspas($linkProposta);
	$query .= " )";		

	if (! jn_query($query)) {
		responseAPI(0, 'Não foi possível gravar o link do contrato');			
		return false; // saio retornando false
	}elseif($dadosAssoc->links){
		salvaLinksArquivos($dadosAssoc, $associado);
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
	exit;
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