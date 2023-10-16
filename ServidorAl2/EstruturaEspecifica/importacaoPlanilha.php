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
}

global $numeroTabela;
global $codigoTitular; 
global $codigoEmpresa; 
global $erro;

$numeroTabela = 0;
$codigoTitular = '';

function array2Table($data) {
	global $numeroTabela;

	if($numeroTabela == 0){
		$dadosEmpresa = Array();
		$numeroData = 0;
		
		foreach($data as $row) {
			$numeroCelula = 0;
			foreach($row as $cell) {
				if(escape($cell)){
					if($numeroCelula == 1){
						$dadosEmpresa[$numeroData] = escape($cell);
					}
					$numeroCelula++;
				}
			}
			$numeroData++;
		}

		gravaEmpresa($dadosEmpresa);
		
	}elseif($numeroTabela == 1){
		
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
					}
					$numeroCelula++;
				}
			}
			
			$numeroLinha++;
		}
		
		percorreBeneficiarios($ArrBeneficiario);
	}
	
	$numeroTabela++;	
}

function debug($data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function escape($string) {
	return htmlspecialchars($string, ENT_QUOTES);
}


function gravaEmpresa($dadosEmpresa){
	global $codigoEmpresa;
	/*
	razão social, cnpj, nome fantasia, inscrição estadual, ramo de atividade, 
	telefone, celular, email, nome completo do representante legal, nome completo do contato da empresa, 
	endereço, número, complemento, bairro, cidade, estado, cep
	*/
	
	$queryEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1010 WHERE DATA_EXCLUSAO IS NULL AND NUMERO_CNPJ = '. aspas($dadosEmpresa[4]);
	$resEmpresa = jn_query($queryEmpresa);
	$rowEmpresa = jn_fetch_object($resEmpresa);
	
	if($rowEmpresa->CODIGO_EMPRESA){
		$codigoEmpresa = $rowEmpresa->CODIGO_EMPRESA;
	}else{
		$codigoEmpresa = jn_gerasequencial('PS1010');
		$numeroCNPJ = $dadosEmpresa[4];
		
		if (strlen($numeroCNPJ) != 14) {
			$numeroCNPJ = str_pad($numeroCNPJ , 14 , '0' , STR_PAD_LEFT);
		}		
				
		$dataAtual = date('d/m/Y');
		$insertEmpresa  = ' INSERT INTO PS1010 ';
		$insertEmpresa .= ' (CODIGO_EMPRESA, CODIGO_SITUACAO_ATENDIMENTO, FLAG_PLANOFAMILIAR, DATA_DIGITACAO, DATA_ADMISSAO, NOME_EMPRESA, NUMERO_CNPJ, NOME_USUAL_FANTASIA, NUMERO_INSC_ESTADUAL, DESCRICAO_OBSERVACAO_EMP) VALUES ';
		$insertEmpresa .= ' ( '. $codigoEmpresa . ', 6, "N", ' . dataToSql($dataAtual) . ', ' . dataToSql($dataAtual) . ', ' . aspas(jn_utf8_encode(strtoupper($dadosEmpresa[3]))) . ', '. aspas(jn_utf8_encode($numeroCNPJ)) . ', ' . aspas($dadosEmpresa[5]) . ', ' . aspas($dadosEmpresa[6]) . ', ' . aspas($dadosEmpresa[7]) . ' )' ;
		if(!jn_query($insertEmpresa)){
			$erro = 'Erro ao Salvar a Empresa; ';
			echo $erro;
			return false;
		}else{
			$numeroContrato = jn_gerasequencial('PS1002');
			$diaVencimento = '05';
			$diaAtual = date('d');
			
			if($diaAtual > '05' && $diaAtual <= '20'){
				$diaVencimento = '20';
			}else{
				$diaVencimento = '05';
			}
			
			$insertContratoEmp  = ' INSERT INTO PS1002 ';
			$insertContratoEmp .= ' (CODIGO_EMPRESA, DIA_VENCIMENTO, NUMERO_CONTRATO) VALUES ';
			$insertContratoEmp .= ' ( '. $codigoEmpresa . ', ' . aspas($diaVencimento) . ', ' . aspas($numeroContrato) . ' )' ;
			
			if(!jn_query($insertContratoEmp)){
				$erro = 'Erro ao Salvar contrato da Empresa; ';
				echo $erro;
				return false;
			}else{
				$estado = '';
				if($dadosEmpresa[18] == 'PARANA'){
					$estado = 'PR';
				}else{
					$estado = substr($dadosEmpresa[18], 0, 2);
				}
				
				$endereco = $dadosEmpresa[13] . ', ' . $dadosEmpresa[14];
				
				$complemento = trim($dadosEmpresa[15]);
		
				if($complemento != 'CASA'){
					$endereco = $dadosEmpresa[13] . ', ' . $dadosEmpresa[14] . ' - ' . $dadosEmpresa[15];
				}

				
				$dadosEmpresa[19] = str_replace('.','',$dadosEmpresa[19]);	
				$dadosEmpresa[19] = str_replace('-','',$dadosEmpresa[19]);
		
				$insertEnderecoEmp  = ' INSERT INTO PS1001 ';
				$insertEnderecoEmp .= ' (CODIGO_EMPRESA, ENDERECO_EMAIL, ENDERECO, BAIRRO, CIDADE, ESTADO, CEP) VALUES ';
				$insertEnderecoEmp .= ' ( '. $codigoEmpresa . ', ' . aspas($dadosEmpresa[10]) . ', ' . aspas($endereco) . ', ' . aspas($dadosEmpresa[16]) . ', ';
				$insertEnderecoEmp .= aspas($dadosEmpresa[17]) . ', ' . aspas($estado) . ', ' . aspas($dadosEmpresa[19]) . ' )' ;
				
				if(!jn_query($insertEnderecoEmp)){
					echo 'Erro ao Salvar endereço da Empresa;';
					return false;
				
				}
			}
		}
	}
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
	global $codigoEmpresa;
	global $codigoTitular; 
	global $codigoPlano; 
	
	if(!$dadosBeneficiario[20]){
		$retorno['ERRO'] = 'A planilha não está completa. Todas as informações são obrigatórias.';	
		echo json_encode($retorno);		
		return false;
	}
	
	$tipoAssociado = '';
	$codigoParentesco = '';
	$sexo = '';
	$codigoAssociado  = jn_gerasequencial('TMP1000_NET');
	$codigoAssociado *= -1;
	
	if($dadosBeneficiario[0] == 'Titular'){
		$codigoTitular = $codigoAssociado;
		$tipoAssociado = 'T';
		$codigoParentesco = 1;
	}else{
		$tipoAssociado = 'D';
		$codigoParentesco = 10;
	}
	
	if($dadosBeneficiario[4] == 'MASCULINO'){
		$sexo = 'M';
	}else{
		$sexo = 'F';
	}
	
	$dadosBeneficiario[20] = str_replace('.','',$dadosBeneficiario[20]);	
	$dadosBeneficiario[20] = str_replace('-','',$dadosBeneficiario[20]);
	
	$dataNascimento = DateTimeImmutable::
			createFromFormat('U', XLSXReader::toUnixTimeStamp($dadosBeneficiario[5]))
			->format('d/m/Y');
	
	if (($dadosBeneficiario[22] != '') and ($dadosBeneficiario[22] != null))
		$codigoPlano = $dadosBeneficiario[22];
	
	$insertAssociado  = ' INSERT INTO TMP1000_NET ';
	$insertAssociado .= ' (CODIGO_ASSOCIADO, CODIGO_TITULAR, CODIGO_PARENTESCO, CODIGO_TABELA_PRECO, FLAG_PLANOFAMILIAR, TIPO_ASSOCIADO, CODIGO_PLANO, CODIGO_EMPRESA, CODIGO_VENDEDOR, DATA_DIGITACAO, DATA_ADMISSAO, NOME_ASSOCIADO,  ';
	$insertAssociado .= ' NOME_MAE, SEXO, DATA_NASCIMENTO, NUMERO_CPF, NUMERO_DECLARACAO_NASC_VIVO, CODIGO_CNS,  NUMERO_RG) VALUES ';
	$insertAssociado .= ' ( '. $codigoAssociado . ', ' . $codigoTitular . ', ' . $codigoParentesco . ', 1, "N", ' . aspas($tipoAssociado) . ', ' . $codigoPlano . ', ' . $codigoEmpresa . ', ' . $_SESSION['codigoIdentificacao'] . ', current_timestamp, current_timestamp, ' . aspas(jn_utf8_encode($dadosBeneficiario[3])) . ', ';
	$insertAssociado .= aspas($dadosBeneficiario[12]) . ', ' . aspas($sexo) . ', ' . dataToSql($dataNascimento) . ', ' . aspas($dadosBeneficiario[6]) . ', ' . aspasNull($dadosBeneficiario[21]) . ', ' . aspas($dadosBeneficiario[20]) . ', ' . aspas($dadosBeneficiario[7]) . ' )' ;
	if(!jn_query($insertAssociado)){		
		$erro = 'Erro ao salvar o Associado - ' . $dadosBeneficiario[2];
		$retorno['ERRO'] = $erro;
		echo json_encode($retorno);		
		return false;
	}else{
		
		$estado = '';
		if($dadosBeneficiario[18] == 'PARANA'){
			$estado = 'PR';
		}else{
			$estado = substr($dadosBeneficiario[18], 0, 2);
		}

		$endereco = trim($dadosBeneficiario[13]) . ', ' . trim($dadosBeneficiario[14]);
		
		$complemento = trim($dadosBeneficiario[15]);
		if($complemento != 'CASA'){
			$endereco = trim($dadosBeneficiario[13]) . ', ' . trim($dadosBeneficiario[14]) . ' - ' . trim($dadosBeneficiario[15]);
		}
		
		$dadosBeneficiario[19] = str_replace('.','',$dadosBeneficiario[19]);	
		$dadosBeneficiario[19] = str_replace('-','',$dadosBeneficiario[19]);
		
		$insertEnderecoBenef  = ' INSERT INTO TMP1001_NET ';
		$insertEnderecoBenef .= ' (CODIGO_EMPRESA, CODIGO_ASSOCIADO, ENDERECO_EMAIL, ENDERECO, BAIRRO, CIDADE, ESTADO, CEP) VALUES ';
		$insertEnderecoBenef .= ' ( '. $codigoEmpresa . ', ' . $codigoAssociado . ',' . aspas($dadosBeneficiario[9]) . ', ' . aspas($endereco) . ', ' . aspas($dadosBeneficiario[16]) . ', ';
		$insertEnderecoBenef .= aspas($dadosBeneficiario[17]) . ', ' . aspas($estado) . ', ' . aspas($dadosBeneficiario[19]) . ' )' ;
		
		if(!jn_query($insertEnderecoBenef)){
			echo 'Erro ao Salvar endereço do associado;';
			return false;
		
		}
	}
}

function analisaPlanilha($dadosBeneficiario){

	if(!$dadosBeneficiario[20]){		
		$retorno['ERRO'] = 'A planilha não está completa. Todas as informações são obrigatórias.';	
		echo json_encode($retorno);		
		return false;
	}	
}