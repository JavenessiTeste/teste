<?php
require('../lib/base.php');

date_default_timezone_set('UTC');
require('XLSXReader.php');
$arquivoImportar = '../../ServidorCliente/planilhasImportarFinanceiro/Sagehosp/' . $_GET['nomeArquivo'];
$xlsx = new XLSXReader($arquivoImportar);
$sheetNames = $xlsx->getSheetNames();

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
		
		$ArrConta = Array();
		$numeroLinha = 0;		
		foreach($data as $row) {
			$numeroCelula = 0;

			if($numeroLinha > 0){
				foreach($row as $cell) {
					if(escape($cell)){
						$ArrConta[$numeroLinha][$numeroCelula] = escape($cell);
						$numeroCelula++;
					}
				}
			}
			
			$numeroLinha++;
		}
		
		percorreContas($ArrConta);
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


function percorreContas($ArrConta){
	$i = 0;
	foreach($ArrConta as $dadosConta){		
		gravaConta($dadosConta);		
		$i++;
	}
	
	$retorno['STATUS'] = 'OK';
	echo json_encode($retorno);	
}

function gravaConta($dadosConta){	
	
	$valor = $dadosConta[6];
	$observacao  = '';
	$observacao .= ' Vencimento: ' . $dadosConta[0] . ' /n ';
	$observacao .= ' Nota: ' . $dadosConta[1] . ' /n ';
	$observacao .= ' Fornecedor: ' . $dadosConta[2] . ' /n ';
	$observacao .= ' Conta: ' . $dadosConta[3] . ' /n ';
	$observacao .= ' Banco: ' . $dadosConta[4] . ' /n ';
	$observacao .= ' Histórico: ' . $dadosConta[5];
	
	if($dadosConta[3]){
		
		$NumSolicitacao =  jn_gerasequencial('ESP_AUDITORIA_PAGAMENTOS_NET');	
		$date = date('d/m/Y');
		$query  = " INSERT INTO ESP_AUDITORIA_PAGAMENTOS_NET(NUMERO_SOLICITACAO, CODIGO_OPERADOR_SOLICITACAO, CODIGO_FORNECEDOR, ";
		$query .= " VALOR_SOLICITADO, TIPO_AUDITORIA, DATA_SOLICITACAO, STATUS_AUDITORIA, CODIGO_CENTRO_CUSTO, DEPARTAMENTO, OBSERVACAO, NUMERO_REGISTRO_PS7201) ";
		$query .= " Values( ";
		$query .= aspas($NumSolicitacao) . ", ";
		$query .= aspas($_SESSION['codigoIdentificacao']) . ", ";
		$query .= aspas('10035') . ", ";
		$query .= aspas($valor) . ", ";
		$query .= aspas('P') . ", ";
		$query .= dataToSql($date) . ", ";
		$query .= aspas('P') . ", ";
		$query .= aspas('') . ", ";
		$query .= aspas('F') . ", ";
		$query .= aspas($observacao . '  -- IMPORTADO VIA PLANILHA SAGEHOSP') . ", ";
		$query .= aspas('') .") ";
		if(!jn_query($query)){			
			$retorno['ERRO'] = 'Erro ao importar';	
			echo json_encode($retorno);		
			return false;
		}		
	}
}