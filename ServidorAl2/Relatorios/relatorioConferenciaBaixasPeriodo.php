<?php

	require('../lib/base.php');
	require('../EstruturaPrincipal/processoPx.php');
	require('../lib/sysutilsAlianca.php');
	require('../ProcessoDinamico/geradorRelatorios.php');

	header("Content-Type: text/html; charset=ISO-8859-1",true);

	//$_GET['Teste'] = 'OK';
	//  prDebug($_POST['ID_PROCESSO']);

	set_time_limit(0);

/* --------------------------------------------------------------------------------------------------------- */
/*	INICIA REGISTROS E INFORMAÇÕES DO PROCESSO
/*  Função: Seleciona dados e configurações do relatório
/*  Data de Implementação: 28/09/2023  				Desenvolvedor: Tavares
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');


/* --------------------------------------------------------------------------------------------------------- */


function retornaQueryPrincipal()
{

	$dataPagamentoInicial = dataAngularToSql($_POST['DATA_PAGTO_INICIAL']);
    $dataPagamentoFinal = dataAngularToSql($_POST['DATA_PAGTO_FINAL']);
	$dataEmissFatInicial = dataAngularToSql($_POST['DATA_EMISS_FAT_INICIAL']);
    $dataEmissFatFinal = dataAngularToSql($_POST['DATA_EMISS_FAT_FINAL']);
	$codigoEmpresaInicial = $_POST['CODIGO_EMPRESA_INICIAL'];
    $codigoEmpresaFinal = $_POST['CODIGO_EMPRESA_FINAL'];

	if ($_POST['MOSTRAR_DADOS_ADICIONAIS'] == 'VIDAS') {
		if ($_POST['TIPO_FATURAS'] == 'PF') {
			$dadosAdicionaisVidas = '(SELECT COUNT(*)
									FROM PS1000 PS1000_1
									WHERE ((PS1000_1.DATA_EXCLUSAO IS NULL) OR (PS1000_1.DATA_EXCLUSAO >= PS1020.DATA_VENCIMENTO)) 
										AND (PS1000_1.DATA_ADMISSAO <= PS1020.DATA_VENCIMENTO) 
										AND (PS1000_1.CODIGO_TITULAR = PS1000.CODIGO_TITULAR)) AS QUANTIDADE_VIDAS,';
			};
		if ($_POST['TIPO_FATURAS'] == 'PJ') {
			$dadosAdicionaisVidas = '(SELECT COUNT(*)
									FROM PS1000 PS1000_1
									WHERE ((PS1000_1.DATA_EXCLUSAO IS NULL) OR (PS1000_1.DATA_EXCLUSAO >= PS1020.DATA_VENCIMENTO)) 
										AND (PS1000_1.DATA_ADMISSAO <= PS1020.DATA_VENCIMENTO) 
										AND (PS1000_1.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA)) AS QUANTIDADE_VIDAS,';
		};
		if ($_POST['TIPO_FATURAS'] == 'AMB') {

			$dadosAdicionaisVidas = array(
				'(SELECT COUNT(*)
					FROM PS1000 PS1000_1
					WHERE ((PS1000_1.DATA_EXCLUSAO IS NULL) OR (PS1000_1.DATA_EXCLUSAO >= PS1020.DATA_VENCIMENTO)) 
						AND (PS1000_1.DATA_ADMISSAO <= PS1020.DATA_VENCIMENTO) 
						AND (PS1000_1.CODIGO_TITULAR = PS1000.CODIGO_TITULAR)) AS QUANTIDADE_VIDAS,',
				'(SELECT COUNT(*)
				FROM PS1000 PS1000_1
				WHERE ((PS1000_1.DATA_EXCLUSAO IS NULL) OR (PS1000_1.DATA_EXCLUSAO >= PS1020.DATA_VENCIMENTO)) 
					AND (PS1000_1.DATA_ADMISSAO <= PS1020.DATA_VENCIMENTO) 
					AND (PS1000_1.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA)) AS QUANTIDADE_VIDAS,'
			);			
			
		};
	}

	if ($_POST['MOSTRAR_DADOS_ADICIONAIS'] == 'BANCO') {
		$dadosAdicionaisBanco = 'PS1020.CODIGO_BANCO_BAIXA AS CODIGO_BANCO_BAIXA, PS1020.NUMERO_CONTA_BAIXA AS NUMERO_CONTA_BAIXA,';
	}
	
	if ($_POST['APENAS_TIPO_BAIXA'] != '') {
		if ($_POST['APENAS_TIPO_BAIXA'] == 'A') $tipoBaixa = " AND (PS1020.TIPO_BAIXA = 'A') ";
		if ($_POST['APENAS_TIPO_BAIXA'] == 'M') $tipoBaixa = " AND (PS1020.TIPO_BAIXA = 'M') ";
		if ($_POST['APENAS_TIPO_BAIXA'] == 'R') $tipoBaixa = " AND (PS1020.TIPO_BAIXA = 'R') ";
		if ($_POST['APENAS_TIPO_BAIXA'] == 'L') $tipoBaixa = " AND (PS1020.TIPO_BAIXA = 'L') ";
		if ($_POST['APENAS_TIPO_BAIXA'] == 'D') $tipoBaixa = " AND (PS1020.TIPO_BAIXA = 'D') ";
		if ($_POST['APENAS_TIPO_BAIXA'] == 'P') $tipoBaixa = " AND (PS1020.TIPO_BAIXA = 'P') ";
	}
	
	if ($_POST['APENAS_TIPO_FATURA'] != '') {
		if ($_POST['APENAS_TIPO_FATURA'] == 'F') $tipoFatura = " AND (PS1020.TIPO_REGISTRO = 'F') ";
		if ($_POST['APENAS_TIPO_FATURA'] == 'P') $tipoFatura = " AND (PS1020.TIPO_REGISTRO = 'P') ";
		if ($_POST['APENAS_TIPO_FATURA'] == 'C') $tipoFatura = " AND (PS1020.TIPO_REGISTRO = 'C') ";
		if ($_POST['APENAS_TIPO_FATURA'] == 'N') $tipoFatura = " AND (PS1020.TIPO_REGISTRO = 'N') ";
		if ($_POST['APENAS_TIPO_FATURA'] == 'A') $tipoFatura = " AND (PS1020.TIPO_REGISTRO = 'A') ";
		if ($_POST['APENAS_TIPO_FATURA'] == 'Q') $tipoFatura = " AND (PS1020.TIPO_REGISTRO = 'Q') ";
		if ($_POST['APENAS_TIPO_FATURA'] == 'O') $tipoFatura = " AND (PS1020.TIPO_REGISTRO = 'O') ";
	}	

	if ($_POST['MES_ANO_REFERENCIA'] != '') {
		$mesAnoReferencia = ' AND (PS1020.MES_ANO_REFERENCIA = ' . aspas($_POST['MES_ANO_REFERENCIA']) . ')';
	}

	if ($_POST['CODIGO_BANCO_INICIAL'] != '' && $_POST['CODIGO_BANCO_FINAL'] != '') {
		$filtroCodigoBanco = ' AND (PS1020.CODIGO_BANCO_BAIXA BETWEEN ' . $_POST['CODIGO_BANCO_INICIAL'] . ' AND ' . $_POST['CODIGO_BANCO_FINAL'] . ')';
	}

	if ($_POST['CODIGO_OPERADOR_BAIXA'] != '') {
		$filtroOperadorBaixa = ' AND (PS1020.CODIGO_OPERADOR_BAIXA = ' . $_POST['CODIGO_OPERADOR_BAIXA'] . ')';
	}

	if ($_POST['ORDENACAO_DADOS'] == 'PAG') {
        $orderCriteria = ' ORDER BY PS1020.DATA_PAGAMENTO';
	}
    if ($_POST['ORDENACAO_DADOS'] == 'ALFA') {
		if ($_POST['TIPO_FATURAS'] == 'PF') $orderCriteria = ' ORDER BY PS1000.NOME_ASSOCIADO';
		if ($_POST['TIPO_FATURAS'] == 'PJ') $orderCriteria = ' ORDER BY PS1010.NOME_EMPRESA';
		if ($_POST['TIPO_FATURAS'] == 'AMB') $orderCriteria = ' ORDER BY NOME_ASSOCIADO';
	}

	if ($_POST['TIPO_FATURAS'] == 'PF') {
		$query      =  'SELECT
							PS1020.CODIGO_ASSOCIADO AS CODIGO_ASSOCIADO,
							PS1000.NOME_ASSOCIADO AS NOME_ASSOCIADO,
                            PS1020.VALOR_FATURA AS VALOR_FATURA,
							PS1020.DATA_VENCIMENTO AS DATA_VENCIMENTO,
							COALESCE(PS1020.VALOR_MULTA,0) AS VALOR_MULTA,
							PS1020.VALOR_PAGO AS VALOR_PAGO,
							PS1020.TIPO_BAIXA AS TIPO_BAIXA,
							PS1020.DATA_PAGAMENTO AS DATA_PAGAMENTO,
							VW_CONTRATOS_VENDAS.NUMERO_CONTRATO AS NUMERO_CONTRATO,
							PS1020.NUMERO_PARCELA AS NUMERO_PARCELA,
							PS1020.DATA_EMISSAO AS DATA_EMISSAO,
							PS1020.DESCRICAO_OBSERVACAO AS OBSERVACOES_COBRANCA,
							' . $dadosAdicionaisBanco . $dadosAdicionaisVidas . '
							PS1020.MES_ANO_REFERENCIA AS MES_ANO_REFERENCIA
                        FROM PS1020
						INNER JOIN PS1000 ON ((PS1000.CODIGO_ASSOCIADO = PS1020.CODIGO_ASSOCIADO) AND (PS1000.FLAG_PLANOFAMILIAR = ' . aspas("S") . '))
						LEFT OUTER JOIN VW_CONTRATOS_VENDAS ON (VW_CONTRATOS_VENDAS.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)
                        WHERE (PS1020.DATA_PAGAMENTO BETWEEN ' . $dataPagamentoInicial . ' AND ' . $dataPagamentoFinal . ')
							 AND (PS1020.DATA_EMISSAO BETWEEN ' . $dataEmissFatInicial . ' AND ' . $dataEmissFatFinal . ')
							 AND (PS1020.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ') '
							 . $tipoBaixa . $tipoFatura . $mesAnoReferencia . $filtroCodigoBanco . $filtroOperadorBaixa . $orderCriteria;
	}
	if ($_POST['TIPO_FATURAS'] == 'PJ') {
		$query      =  'SELECT
							PS1020.CODIGO_EMPRESA AS CODIGO_EMPRESA,
							PS1010.NOME_EMPRESA AS NOME_EMPRESA,
                            PS1020.VALOR_FATURA AS VALOR_FATURA,
							PS1020.DATA_VENCIMENTO AS DATA_VENCIMENTO,
							COALESCE(PS1020.VALOR_MULTA,0) AS VALOR_MULTA,
							PS1020.VALOR_PAGO AS VALOR_PAGO,
							PS1020.TIPO_BAIXA AS TIPO_BAIXA,
							PS1020.DATA_PAGAMENTO AS DATA_PAGAMENTO,
							PS1002.NUMERO_CONTRATO AS NUMERO_CONTRATO,
							PS1020.NUMERO_PARCELA AS NUMERO_PARCELA,
							PS1020.DATA_EMISSAO AS DATA_EMISSAO,
							PS1020.DESCRICAO_OBSERVACAO AS OBSERVACOES_COBRANCA,
							' . $dadosAdicionaisBanco . $dadosAdicionaisVidas . '
							PS1020.MES_ANO_REFERENCIA AS MES_ANO_REFERENCIA
                        FROM PS1020
						INNER JOIN PS1010 ON ((PS1010.CODIGO_EMPRESA = PS1020.CODIGO_EMPRESA) AND (PS1010.FLAG_PLANOFAMILIAR != ' . aspas("S") . '))
						INNER JOIN PS1002 ON (PS1002.CODIGO_EMPRESA = PS1020.CODIGO_EMPRESA)
                        WHERE (PS1020.DATA_PAGAMENTO BETWEEN ' . $dataPagamentoInicial . ' AND ' . $dataPagamentoFinal . ')
							 AND (PS1020.DATA_EMISSAO BETWEEN ' . $dataEmissFatInicial . ' AND ' . $dataEmissFatFinal . ')
							 AND (PS1020.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ') '
							 . $tipoBaixa . $tipoFatura . $mesAnoReferencia . $filtroCodigoBanco . $filtroOperadorBaixa . $orderCriteria;
	}
	if ($_POST['TIPO_FATURAS'] == 'AMB') {
		$query      =  'SELECT
							CAST(PS1020.CODIGO_ASSOCIADO AS VARCHAR) AS CODIGO_ASSOCIADO,
							PS1000.NOME_ASSOCIADO AS NOME_ASSOCIADO,
							PS1020.VALOR_FATURA AS VALOR_FATURA,
							PS1020.DATA_VENCIMENTO AS DATA_VENCIMENTO,
							COALESCE(PS1020.VALOR_MULTA,0) AS VALOR_MULTA,
							PS1020.VALOR_PAGO AS VALOR_PAGO,
							PS1020.TIPO_BAIXA AS TIPO_BAIXA,
							PS1020.DATA_PAGAMENTO AS DATA_PAGAMENTO,
							VW_CONTRATOS_VENDAS.NUMERO_CONTRATO AS NUMERO_CONTRATO,
							PS1020.NUMERO_PARCELA AS NUMERO_PARCELA,
							PS1020.DATA_EMISSAO AS DATA_EMISSAO,
							PS1020.DESCRICAO_OBSERVACAO AS OBSERVACOES_COBRANCA,
							' . $dadosAdicionaisBanco . $dadosAdicionaisVidas[0] . '
							PS1020.MES_ANO_REFERENCIA AS MES_ANO_REFERENCIA
                        FROM PS1020
						INNER JOIN PS1000 ON ((PS1000.CODIGO_ASSOCIADO = PS1020.CODIGO_ASSOCIADO) AND (PS1000.FLAG_PLANOFAMILIAR = ' . aspas("S") . '))
						LEFT OUTER JOIN VW_CONTRATOS_VENDAS ON (VW_CONTRATOS_VENDAS.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)
                        WHERE (PS1020.DATA_PAGAMENTO BETWEEN ' . $dataPagamentoInicial . ' AND ' . $dataPagamentoFinal . ')
							 AND (PS1020.DATA_EMISSAO BETWEEN ' . $dataEmissFatInicial . ' AND ' . $dataEmissFatFinal . ')
							 AND (PS1020.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ') '
							 . $tipoBaixa . $tipoFatura . $mesAnoReferencia . $filtroCodigoBanco . $filtroOperadorBaixa . '
						UNION ALL
						SELECT
							CAST(PS1020.CODIGO_EMPRESA AS VARCHAR) AS CODIGO_EMPRESA,
							PS1010.NOME_EMPRESA AS NOME_EMPRESA,
                            PS1020.VALOR_FATURA AS VALOR_FATURA,
							PS1020.DATA_VENCIMENTO AS DATA_VENCIMENTO,
							COALESCE(PS1020.VALOR_MULTA,0) AS VALOR_MULTA,
							PS1020.VALOR_PAGO AS VALOR_PAGO,
							PS1020.TIPO_BAIXA AS TIPO_BAIXA,
							PS1020.DATA_PAGAMENTO AS DATA_PAGAMENTO,
							PS1002.NUMERO_CONTRATO AS NUMERO_CONTRATO,
							PS1020.NUMERO_PARCELA AS NUMERO_PARCELA,
							PS1020.DATA_EMISSAO AS DATA_EMISSAO,
							PS1020.DESCRICAO_OBSERVACAO AS OBSERVACOES_COBRANCA,
							' . $dadosAdicionaisBanco . $dadosAdicionaisVidas[1] . '
							PS1020.MES_ANO_REFERENCIA AS MES_ANO_REFERENCIA
                        FROM PS1020
						INNER JOIN PS1010 ON ((PS1010.CODIGO_EMPRESA = PS1020.CODIGO_EMPRESA) AND (PS1010.FLAG_PLANOFAMILIAR != ' . aspas("S") . '))
						INNER JOIN PS1002 ON (PS1002.CODIGO_EMPRESA = PS1020.CODIGO_EMPRESA)
                        WHERE (PS1020.DATA_PAGAMENTO BETWEEN ' . $dataPagamentoInicial . ' AND ' . $dataPagamentoFinal . ')
							 AND (PS1020.DATA_EMISSAO BETWEEN ' . $dataEmissFatInicial . ' AND ' . $dataEmissFatFinal . ')
							 AND (PS1020.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ') '
							 . $tipoBaixa . $tipoFatura . $mesAnoReferencia . $filtroCodigoBanco . $filtroOperadorBaixa . $orderCriteria;
	}

	// pr($query, true);

	return $query;
	

}

/* --------------------------------------------------------------------------------------------------------- */

?>

