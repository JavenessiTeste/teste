<?php
require('../lib/base.php');

date_default_timezone_set('UTC');
require('XLSXReader.php');
$arquivoImportar = '../../ServidorCliente/uploadsPropostas/' . $_GET['nomeArquivo'];
$xlsx = new XLSXReader($arquivoImportar);
$sheetNames = $xlsx->getSheetNames();

$queryPlanilha = 'SELECT CODIGO_PLANO FROM CFGARQUIVOS_PROCESSOS_NET WHERE NOME_ARQUIVO = ' . aspas($_GET['nomeArquivo']);
$resPlanilha = jn_query($queryPlanilha);
$rowPlanilha = jn_fetch_object($resPlanilha);
$codigoPlano = $rowPlanilha->CODIGO_PLANO;

foreach($sheetNames as $sheetName) {
	$sheet = $xlsx->getSheet($sheetName);
	array2Table($sheet->getData());
	break;
}

global $codigoTitular; 
global $erro;

$codigoTitular = '';

function array2Table($data) {
		/*
		TIPO - PARENTESCO - TITULAR	- NOME COMPLETO	- SEXO - DATA DE NASCIMENTO - CPF - RG
		ESTADO CIVIL - EMAIL - TELEFONE - CELULAR - NOME DA MÃE	
		ENDEREÇO - NÚMERO - COMPLEMENTO	- BAIRRO - CIDADE - ESTADO - CEP
		*/
		
		$ArrBeneficiario = Array();
		$numeroLinha = 0;		
		foreach($data as $row) {
			$numeroCelula = 0;
			if($numeroLinha > 0){
				foreach($row as $cell) {
					if(escape($cell)){
						$ArrBeneficiario[$numeroLinha][$numeroCelula] = escape($cell);
						$numeroCelula++;
					}
				}
			}
			
			$numeroLinha++;
		}
		
		percorreBeneficiarios($ArrBeneficiario);
	
}

function debug($data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function escape($string) {
	return htmlspecialchars($string, ENT_QUOTES);
}

function percorreBeneficiarios($ArrBeneficiario){


	foreach($ArrBeneficiario as $dadosBeneficiario){
		analisaPlanilha($dadosBeneficiario);
	}
	
	foreach($ArrBeneficiario as $dadosBeneficiario){
		gravaBeneficiario($dadosBeneficiario);
	}
	
	if(!$erro){
		$retorno['STATUS'] = 'OK';	
		echo json_encode($retorno);		
		return false;
	}else{
		$retorno['STATUS'] = 'ERRO';	
		echo json_encode($retorno);		
		return false;
	}
}

function gravaBeneficiario($dadosBeneficiario){
	global $codigoTitular; 
	global $codigoPlano; 
	
	$codigoEmpresa = $dadosBeneficiario[0]; 
	
	
	if(!$dadosBeneficiario[21]){
		$retorno['ERRO'] = 'A planilha não está completa. Todas as informações são obrigatórias.' . $dadosBeneficiario[3];	
		echo json_encode($retorno);		
		return false;
	}
	
	$tipoAssociado = '';
	$codigoParentesco = '';
	$sexo = '';
	$codigoAssociado  = jn_gerasequencial('TMP1000_NET');
	$codigoAssociado *= -1;
	
	if(strtoupper($dadosBeneficiario[1]) == 'TITULAR'){
		$codigoTitular = $codigoAssociado;
		$tipoAssociado = 'T';
		$codigoParentesco = 1;
	}else{
		$tipoAssociado = 'D';
		$codigoParentesco = 10;
	}
	
	if($dadosBeneficiario[5] == 'MASCULINO'){
		$sexo = 'M';
	}else{
		$sexo = 'F';
	}
	
	$dadosBeneficiario[21] = str_replace('.','',$dadosBeneficiario[21]);	
	$dadosBeneficiario[21] = str_replace('-','',$dadosBeneficiario[21]);
	
	$dadosBeneficiario[7] = str_replace('.','',$dadosBeneficiario[7]);	
	$dadosBeneficiario[7] = str_replace('-','',$dadosBeneficiario[7]);
	
	$dataNascimento = DateTimeImmutable::
			createFromFormat('U', XLSXReader::toUnixTimeStamp($dadosBeneficiario[6]))
			->format('d/m/Y');
	
	$insertAssociado  = ' INSERT INTO TMP1000_NET ';
	$insertAssociado .= ' (CODIGO_ASSOCIADO, CODIGO_TITULAR, CODIGO_PARENTESCO, CODIGO_TABELA_PRECO, FLAG_PLANOFAMILIAR, TIPO_ASSOCIADO, CODIGO_PLANO, CODIGO_EMPRESA, DATA_DIGITACAO, DATA_ADMISSAO, NOME_ASSOCIADO,  ';
	$insertAssociado .= ' NOME_MAE, SEXO, DATA_NASCIMENTO, NUMERO_CPF, NUMERO_DECLARACAO_NASC_VIVO, CODIGO_CNS,  NUMERO_RG) VALUES ';
	$insertAssociado .= ' ( '. $codigoAssociado . ', ' . $codigoTitular . ', ' . $codigoParentesco . ', 1, "N", ' . aspas($tipoAssociado) . ', ' . $codigoPlano . ', ' . $codigoEmpresa . ', current_timestamp, current_timestamp, ' . aspas(jn_utf8_encode($dadosBeneficiario[4])) . ', ';
	$insertAssociado .= aspas($dadosBeneficiario[13]) . ', ' . aspas($sexo) . ', ' . dataToSql($dataNascimento) . ', ' . aspas($dadosBeneficiario[7]) . ', ' . aspasNull($dadosBeneficiario[22]) . ', ' . aspas($dadosBeneficiario[21]) . ', ' . aspas($dadosBeneficiario[8]) . ' )' ;
	if(!jn_query($insertAssociado)){		
		$erro = 'Erro ao salvar o Associado - ' . $dadosBeneficiario[2];
		$retorno['ERRO'] = $erro;
		echo json_encode($retorno);		
		return false;
	}else{
		
		$estado = '';
		if($dadosBeneficiario[19] == 'PARANA'){
			$estado = 'PR';
		}else{
			$estado = substr($dadosBeneficiario[19], 0, 2);
		}

		$endereco = trim($dadosBeneficiario[14]) . ', ' . trim($dadosBeneficiario[15]);
		
		$complemento = trim($dadosBeneficiario[16]);
		if($complemento != 'CASA'){
			$endereco = trim($dadosBeneficiario[14]) . ', ' . trim($dadosBeneficiario[15]) . ' - ' . trim($dadosBeneficiario[16]);
		}
		
		$dadosBeneficiario[20] = str_replace('.','',$dadosBeneficiario[20]);	
		$dadosBeneficiario[20] = str_replace('-','',$dadosBeneficiario[20]);
		
		$insertEnderecoBenef  = ' INSERT INTO TMP1001_NET ';
		$insertEnderecoBenef .= ' (CODIGO_EMPRESA, CODIGO_ASSOCIADO, ENDERECO_EMAIL, ENDERECO, BAIRRO, CIDADE, ESTADO, CEP) VALUES ';
		$insertEnderecoBenef .= ' ( '. $codigoEmpresa . ', ' . $codigoAssociado . ',' . aspas($dadosBeneficiario[10]) . ', ' . aspas($endereco) . ', ' . aspas($dadosBeneficiario[17]) . ', ';
		$insertEnderecoBenef .= aspas($dadosBeneficiario[18]) . ', ' . aspas($estado) . ', ' . aspas($dadosBeneficiario[20]) . ' )' ;
		
		if(!jn_query($insertEnderecoBenef)){
			echo 'Erro ao Salvar endereço do associado;';
			return false;
		
		}
	}
}

function analisaPlanilha($dadosBeneficiario){
	if(!$dadosBeneficiario[21]){		
		$retorno['ERRO'] = 'A planilha não está completa. Todas as informações são obrigatórias. --- parado no analisa' . $dadosBeneficiario[3];	
		echo json_encode($retorno);		
		return false;
	}	
}