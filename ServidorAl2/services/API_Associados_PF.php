<?php
require('../lib/base.php');

$jsonStr = file_get_contents("php://input");
$dados = json_decode($jsonStr);


/***** - Inicio processo para criacao do log - *****/
$name  = 'logAPIAssocPF.log';
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
$retorno 				= '';
$codigoEmpresa 			= '400';
$dataAssinatura 		= date('d/m/Y');
$numeroContrato 		= $dados->numeroContrato;
$cpfCnpjVendedor 		= $dados->cpf_cnpj_vendedor;
$dadosContrato 			= $dados->contrato;
$dadosAssociados		= $dados->beneficiarios;
$numRegTmp1000_net		= '';
/***** - Fim processo de busca das informacoes para utilizar nas funcoes especificas - *****/


global $codigoAssociado, $codigoVendedor;


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
}

if(retornaValorConfiguracao('VALIDA_CPFCNPJ_VENDEDOR') == 'SIM'){
	if(!$cpfCnpjVendedor){
		responseAPI(0,'Os dados do vendedor da proposta não foram enviados.');
		return false;
	}else{

		$queryVendedor = 'SELECT CODIGO_IDENTIFICACAO FROM PS1102 WHERE REPLACE(REPLACE(REPLACE(COALESCE(NUMERO_CPF, NUMERO_CNPJ),\'.\', \'\'),\'-\', \'\'),\'/\', \'\') = ' . aspas($cpfCnpjVendedor);		
		$resVendedor = jn_query($queryVendedor);
		$rowVendedor = jn_fetch_object($resVendedor);		
		$codigoVendedor = $rowVendedor->CODIGO_IDENTIFICACAO;

		if(!isset($codigoVendedor)){
			responseAPI(0,'Os dados do vendedor da proposta não foram encontrados no ambiente do cliente.');
			return false;
		}
	}
}

foreach($dadosAssociados as $contrato){	
	foreach($contrato as $benef){
		salvaAssociado($benef);
	}
}

function salvaAssociado($dadosAssoc){	
	global $codigoEmpresa, $codigoAssociado, $dadosContrato, $dataAssinatura, $retorno;
	
	
	$dataNasc = SqlToData($dadosAssoc->data_nascimento);
	$dataNasc = str_replace("'",'',$dataNasc);	
	
	$dataVigencia = SqlToData($dadosContrato->data_vigencia);
	$dataVigencia = str_replace("'",'',$dataVigencia);	

	$flagPlanoFamiliar = 'S';			
		
	$numRegTmp1000_net = jn_gerasequencial('VND1000_ON');
	$numRegTmp1000_net *= -1;
	
	$idTit = '';
	$codTitular = '';
	$tpAssoc = '';
	if($dadosAssoc->titular_uuid){
		$idTit = $dadosAssoc->titular_uuid;
		$tpAssoc = 'D';
		
		$queryTit = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE UUID =' . aspas($dadosAssoc->titular_uuid);
		$resTit = jn_query($queryTit);
		$rowTit = jn_fetch_object($resTit);
		$codTitular = $rowTit->CODIGO_ASSOCIADO;
	}else{
		$idTit = $dadosAssoc->uuid;
		$codTitular = $numRegTmp1000_net;
		$tpAssoc = 'T';
	}	

	$descObservacao = '';
	if($dadosAssoc->pagamento->numero_beneficio)
		$descObservacao .= ' --Beneficio: ' . $dadosAssoc->pagamento->numero_beneficio;
	
	if($dadosAssoc->pagamento->especie)
	$descObservacao .= ' --Especie: ' . $dadosAssoc->pagamento->especie;

	if($dadosAssoc->pagamento->salario)
	$descObservacao .= ' --Salario: ' . $dadosAssoc->pagamento->salario;

	$codigoAssociado = $numRegTmp1000_net;

	$query  = ' INSERT INTO VND1000_ON ( ';
	$query .= ' CODIGO_PLANO, CODIGO_CARENCIA, CODIGO_TABELA_PRECO, CODIGO_MOTIVO_INCLUSAO, NOME_ASSOCIADO, UUID, TITULAR_UUID, DATA_VALIDA_CARENCIA, DATA_PRIMEIRO_FATURAMENTO, ';
	$query .= ' SEXO, DATA_NASCIMENTO, DATA_DIGITACAO, DATA_ADMISSAO, DATA_EMISSAO_RG, CODIGO_EMPRESA, FLAG_PLANOFAMILIAR, DESCRICAO_OBSERVACAO, NUMERO_REGISTRO_NIVEL1, ULTIMO_STATUS, ';
	$query .= ' NOME_MAE, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG, NATUREZA_RG, CODIGO_ESTADO_CIVIL, CODIGO_PARENTESCO, CODIGO_ASSOCIADO, CODIGO_TITULAR, TIPO_ASSOCIADO, CODIGO_CNS, SITUACAO_CADASTRAL, NUMERO_DECLARACAO_NASC_VIVO ';
	$query .= ' )VALUES ( ';
	$query .= aspas($dadosAssoc->codigo_plano) . ", ";
	$query .= aspas($dadosAssoc->codigo_carencia) . ", ";
	$query .= aspas($dadosAssoc->codigo_tabela_preco) . ", ";
	$query .= aspasNull($dadosAssoc->codigo_motivo_inclusao) . ", ";
	$query .= aspas(remove_caracteres($dadosAssoc->nome)) . ", ";
	$query .= aspas($dadosAssoc->uuid) . ", ";
	$query .= aspas($idTit) . ", ";
	$query .= dataToSql($dataVigencia) . ", ";
	$query .= dataToSql($dataAssinatura) . ", ";
	$query .= aspas($dadosAssoc->sexo) . ", ";
	$query .= dataToSql($dataNasc) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql($dadosAssoc->data_emissao_rg) . ", ";
	$query .= aspas('400') . ", ";	
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
	$query .= aspasNull($dadosAssoc->parentesco) . ", ";
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
	}elseif($dadosAssoc->titular_uuid == ''){		
		salvaDadosContato($dadosAssoc, $numRegTmp1000_net);
	}	
}

function salvaDadosContato($dadosAssoc, $associado){
	global $retorno;
	global $codigoEmpresa;	
	global $numeroContrato;
	global $dadosContrato;
	global $codigoVendedor;
	
	$dadosEndereco = $dadosAssoc->endereco;		
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
	
	if($codigoVendedor == '' and $dadosContrato->codigo_vendedor != '')
		$codigoVendedor = $dadosContrato->codigo_vendedor;	
	
	$query  = 'INSERT INTO VND1001_ON (CODIGO_ASSOCIADO, NUMERO_TELEFONE_01, ENDERECO, BAIRRO, ';
	$query .= ' CIDADE, CEP, ESTADO, ENDERECO_EMAIL, NUMERO_CONTRATO, DATA_PRIMEIRO_FATURAMENTO, CODIGO_VENDEDOR, DIA_VENCIMENTO) ';
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
	$query .= aspas($codigoVendedor) . ", ";
	$query .= aspas($diaVencimento);
	$query .= " )";		
	
	if (! jn_query($query)) {
		responseAPI(0, 'Não foi possível gravar os dados de contato ou contrato!');			
		return false; // saio retornando false
	}elseif($dadosAssoc->entidades){
		salvaEntidades($dadosAssoc, $associado);
	}else{
		salvaStatus($dadosAssoc, $associado);
	}

}


function salvaEntidades($dadosAssoc, $associado){

	global $retorno;
	global $codigoEmpresa;	
	global $numeroContrato;
	global $dadosContrato;
	
	$dadosEntidades = $dadosAssoc->entidades;		
	$erroEncontrado = false;

	foreach($dadosEntidades as $item){
		$query  = ' INSERT INTO ESP_ENTIDADES (CODIGO_ASSOCIADO, DATA_VINCULO, NOME_ENTIDADE, TIPO_ENTIDADE,  ';
		$query .= '  NUMERO_CNPJ, CODIGO_ENTIDADE, RESPONSAVEL_ENTIDADE) ';
		$query .= ' VALUES ( ';        
		$query .= aspas($associado) . ", ";			
		$query .= DataToSql(date('d/m/Y')) . ", ";	
		$query .= aspas(utf8_decode($item->nome)) . ", ";
		$query .= aspas($item->tipo) . ", ";
		$query .= aspas($item->cnpj) . ", ";
		$query .= aspas($item->codigo) . ", ";
		$query .= aspas(utf8_decode($item->responsavel));
		$query .= " )";		

		if (! jn_query($query)) {
			responseAPI(0, 'Não foi possível gravar as entidades');	
			$erroEncontrado = true;		
			return false; // saio retornando false
		}
	}
	
	if($erroEncontrado === false){
		salvaStatus($dadosAssoc, $associado);
	}
}

function salvaStatus($dadosAssoc, $associado){

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
	}elseif($dadosAssoc->link_contrato){
		salvaLinkContrato($dadosAssoc, $associado);
	}elseif($dadosAssoc->links){
		salvaLinksArquivos($dadosAssoc, $associado);
	}else{
		responseAPI(200);
	}
}

function salvaLinkContrato($dadosAssoc, $associado){

	$query  = ' INSERT INTO VND1002_ON (CODIGO_ASSOCIADO, CODIGO_MODELO, NOME_ARQUIVO) VALUES ( ';        
	$query .= aspas($associado) . ", ";		
	$query .= aspas('1')  . ", ";	
	$query .= aspas($dadosAssoc->link_contrato);
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

function salvaLinksArquivos($dadosAssoc, $associado){
	$erro = false;

	foreach($dadosAssoc->links as $item){		

		$query  = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET (CODIGO_ASSOCIADO, NOME_ARQUIVO, DATA_ENVIO, TIPO_ARQUIVO) VALUES ( ';		
		$query .=  aspas($associado) . ', ' . aspas($item->link) . ', GETDATE(), ' . aspas($item->tipo) . ' )';
		
		if (! jn_query($query)) 
			$erro = true;
		
	}

	if ($erro) {
		responseAPI(0, 'Não foi possível gravar o link do anexos');			
		return false; // saio retornando false
	}else{
		responseAPI(200);
	}
}

function responseAPI($codErro, $mensagem = ''){

	global $codigoAssociado;

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
		$retornoResponse['ObjErros']['codigoAssociado'] = $codigoAssociado;
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