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
$dadosContrato 			= $dados->contrato;
$dadosAssociados		= $dados->beneficiarios;
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

if($token != retornaValorConfiguracao('TOKEN_API_ASSOC_PF')){	
	responseAPI(2);
	return false;
}

/***** - Final validacao do header, para verificar token e rotinas de autorizacao - *****/


if(!$dados){	
	responseAPI(0,'Dados não encontrados, favor verificar se o arquivo JSON está no padrão correto.');
	return false;
}

foreach($dadosAssociados as $contrato){	
	foreach($contrato as $benef){
		salvaAssociado($benef);
	}
}

function salvaAssociado($dadosAssoc){	
	global $codigoEmpresa;	
	global $dadosContrato;		
	global $dataAssinatura;			
	global $retorno;
	
	
	$dataNasc = SqlToData($dadosAssoc->data_nascimento);
	$dataNasc = str_replace("'",'',$dataNasc);	
	
	$dataVigencia = SqlToData($dadosContrato->data_vigencia);
	$dataVigencia = str_replace("'",'',$dataVigencia);	

	$flagPlanoFamiliar = 'S';			
		
	$numRegTmp1000_net = jn_gerasequencial('TMP1000_NET');
	$numRegTmp1000_net *= -1;
	
	$idTit = '';
	$codTitular = '';
	$tpAssoc = '';
	if($dadosAssoc->titular_uuid){
		$idTit = $dadosAssoc->titular_uuid;
		$tpAssoc = 'D';
		
		$queryTit = 'SELECT CODIGO_ASSOCIADO FROM TMP1000_NET WHERE UUID =' . aspas($dadosAssoc->titular_uuid);
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

	$query  = ' INSERT INTO TMP1000_NET ( ';
	$query .= ' CODIGO_PLANO, CODIGO_CARENCIA, CODIGO_TABELA_PRECO, CODIGO_MOTIVO_INCLUSAO, NOME_ASSOCIADO, UUID, TITULAR_UUID, DATA_VALIDA_CARENCIA, DATA_PRIMEIRO_FATURAMENTO, ';
	$query .= ' SEXO, DATA_NASCIMENTO, DATA_DIGITACAO, DATA_ADMISSAO, DATA_EMISSAO_RG, CODIGO_EMPRESA, FLAG_PLANOFAMILIAR, DESCRICAO_OBSERVACAO, ';
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
		salvaTelefone($dadosAssoc, $numRegTmp1000_net);
	}	
}

function salvaTelefone($dadosAssoc, $associado){
	global $retorno;
	
	$codAreaTel 	= substr($dadosAssoc->numero_telefone,0,2);
	$numeroTel 		= substr($dadosAssoc->numero_telefone,2,11);
	
	// salvo os dados do telefone
    if ($numeroTel != '') {
        $query  = 'INSERT INTO TMP1006_NET (CODIGO_ASSOCIADO, INDICE_TELEFONE, CODIGO_AREA, NUMERO_TELEFONE) ';
        $query .= 'VALUES ( ';        
        $query .= aspasNull($associado) . ", ";
		$query .= aspas('1') . ", ";
        $query .= aspasNull($codAreaTel) . ", ";
        $query .= aspas($numeroTel);
        $query .= " )";		
		
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
    }
	
	salvaEndereco($dadosAssoc,$associado);
}

function salvaEndereco($dadosAssoc, $associado){	
	global $codigoEmpresa;	
	global $retorno;	
	$codigoAssociado = '';
	$enderecoEmail = '';	
	
	$queryEnd  = ' SELECT NUMERO_REGISTRO FROM TMP1001_NET ';
	$queryEnd .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);	
			
	$resEnd = jn_query($queryEnd);
	$rowEnd = jn_fetch_object($resEnd);

	$dadosEndereco = $dadosAssoc->endereco;		
	$codigoAssociado = $associado;
	$enderecoEmail = $dadosAssoc->email;
	
	if(!$rowEnd->NUMERO_REGISTRO){	
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

		$query  = 'INSERT INTO TMP1001_NET (CODIGO_EMPRESA, CODIGO_ASSOCIADO, ENDERECO, BAIRRO, ';
		$query .= 'CIDADE, CEP, ESTADO, ENDERECO_EMAIL) ';
		$query .= 'VALUES ( ';

		$query .= integerNull($codigoEmpresa) . ", ";
		$query .= aspasNull($codigoAssociado) . ", ";
		$query .= aspas(substr($logradouroEndereco,0,45)) . ", ";
		$query .= aspas(substr($bairro,0,24)) . ", ";
		$query .= aspas($cidade) . ", ";
		$query .= aspas($numeroCep) . ", ";
		$query .= aspas($estado). ",";
		$query .= aspasNull($enderecoEmail);
		$query .= " )";

		if (! jn_query($query)) {
			responseAPI(0, 'Não foi possível gravar o endereço!');			
			return false; // saio retornando false
		}else{
			salvaContrato($dadosAssoc, $associado);		
		}
	}		
}

function salvaContrato($dadosAssoc, $associado){	
	global $numeroContrato;	
	global $dadosContrato;	
	global $retorno;
	
	$diaVencimento = $dadosContrato->dia_vencimento;
	$dataVigencia = $dadosContrato->data_vigencia;		
	
	$queryContrato  = 'INSERT INTO TMP1002_NET ( ';
	$queryContrato .= ' CODIGO_ASSOCIADO, NUMERO_CONTRATO, DATA_PRIMEIRO_FATURAMENTO, DIA_VENCIMENTO ) ';	
	$queryContrato .= 'VALUES ( ';	
	$queryContrato .= aspas($associado) . ", ";	
	$queryContrato .= aspas($numeroContrato) . ", ";
	$queryContrato .= aspas($dataVigencia) . ", ";
	$queryContrato .= aspas($diaVencimento);
	$queryContrato .= " ) ";
	
	
	if (! jn_query($queryContrato)) {
		responseAPI(0, 'Não foi possível gravar o contrato!');			
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