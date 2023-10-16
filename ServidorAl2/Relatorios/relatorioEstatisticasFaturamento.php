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
/*  Data de Implementação: 28/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');

/* --------------------------------------------------------------------------------------------------------- */

function retornaQueryPrincipal()

{   

    $groupByPagamento = ' GROUP BY MONTH(PS1020.DATA_PAGAMENTO), YEAR(PS1020.DATA_PAGAMENTO)';
    $orderByPagamento = ' ORDER BY MONTH(PS1020.DATA_PAGAMENTO), YEAR(PS1020.DATA_PAGAMENTO)';

    $groupByVencimento = ' GROUP BY MONTH(PS1020.DATA_VENCIMENTO), YEAR(PS1020.DATA_VENCIMENTO)';
    $orderByVencimento = ' ORDER BY MONTH(PS1020.DATA_VENCIMENTO), YEAR(PS1020.DATA_VENCIMENTO)';
    
    if ($_POST['DATA_INICIAL'] != '' && $_POST['DATA_FINAL'] != '') {
        $filtraPorData = ' BETWEEN ' . dataAngularToSql($_POST['DATA_INICIAL']) . ' AND ' . dataAngularToSql($_POST['DATA_FINAL']) . ')';

        //teste
        $dataInicial = dataAngularToSql($_POST['DATA_INICIAL']);
        $dataFinal = dataAngularToSql($_POST['DATA_FINAL']);
        $filtraDataTeste = " BETWEEN $dataInicial AND $dataFinal";
    }

    if ($_POST['MES_ANO_REF'] != '') {
        $filtraPorMesAnoRef = ' AND (PS1020.MES_ANO_REFERENCIA = ' . aspas($_POST['MES_ANO_REF']) . ')';
    }

    if ($_POST['CODIGO_GRUPO_INICIAL'] != '' && $_POST['CODIGO_GRUPO_FINAL'] != '') {
        $codigoGrupoFiltro = ' AND (PS1000.CODIGO_GRUPO_CONTRATO BETWEEN ' . $_POST['CODIGO_GRUPO_INICIAL'] . ' AND ' . $_POST['CODIGO_GRUPO_FINAL'] . ')';
        $codigoGrupoInner = 'INNER JOIN PS1000 ON (PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)';
    }

    

    
    if ($_POST['DADOS_RELATORIO'] == 'VENC') {
        $query      =  'SELECT
                            count(*) AS QUANTIDADE_FATURAS,
	                        sum(PS1020.VALOR_FATURA) AS VALOR_TOTAL
                        FROM PS1020 '
                        . $codigoGrupoInner .
                        'WHERE PS1020.VALOR_FATURA > 0
                        AND (PS1020.DATA_VENCIMENTO' . $filtraPorData . $filtraPorMesAnoRef . $codigoGrupoFiltro;
    }
    if ($_POST['DADOS_RELATORIO'] == 'EMIS') {
        $query      =  'SELECT
                            count(*) AS QUANTIDADE_EMITIDAS,
	                        sum(PS1020.VALOR_FATURA) AS VALOR_EMITIDO
                        FROM PS1020 '
                        . $codigoGrupoInner .
                        'WHERE (PS1020.DATA_EMISSAO' . $filtraPorData . $filtraPorMesAnoRef . $codigoGrupoFiltro;
    }
    if ($_POST['DADOS_RELATORIO'] == 'PAGA') {
        $query      =  'SELECT
                            (MONTH(PS1020.DATA_PAGAMENTO)) as MES,
	                        (YEAR(PS1020.DATA_PAGAMENTO)) AS ANO,
                            COUNT(*) AS QUANTIDADE,
	                        SUM(PS1020.VALOR_PAGO) AS VALOR
                        FROM PS1020 '
                        . $codigoGrupoInner .
                        'WHERE (PS1020.VALOR_PAGO > 0)
                        AND (PS1020.DATA_VENCIMENTO' . $filtraPorData . $filtraPorMesAnoRef . $codigoGrupoFiltro . $groupByPagamento . $orderByPagamento;
    }
    if ($_POST['DADOS_RELATORIO'] == 'INAD') {
        $dataValidacao = $_POST['DATA_FINAL'] != '' ? $_POST['DATA_FINAL'] : date('Y-m-d');
        $filtraDataVencimento = ' AND (PS1020.DATA_VENCIMENTO <= ' . dataAngularToSql($dataValidacao) . ')';

        $query      =  'SELECT
                            MONTH(PS1020.DATA_VENCIMENTO) AS MES,
	                        YEAR(PS1020.DATA_VENCIMENTO) AS ANO,
                            COUNT(*) AS QUANTIDADE_ABERTO,
	                        SUM(PS1020.VALOR_FATURA) AS VALOR_INADIMP
                        FROM PS1020 '
                        . $codigoGrupoInner .
                        'WHERE (PS1020.DATA_PAGAMENTO IS NULL) ' . $filtraDataVencimento . $filtraPorMesAnoRef . $codigoGrupoFiltro . $groupByVencimento . $orderByVencimento;
    }

    return $query;
}

/* --------------------------------------------------------------------------------------------------------- */

?>