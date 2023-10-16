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
/*  Data de Implementação: 26/09/2023  				Desenvolvedor: Tavares 
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');

/* --------------------------------------------------------------------------------------------------------- */

function retornaQueryPrincipal()
{

    $codigoEmpresaInicial = $_POST['CODIGO_EMPRESA_INICIAL'];
    $codigoEmpresaFinal = $_POST['CODIGO_EMPRESA_FINAL'];

    if ($_POST['TIPO_ASSOCIADOS'] == 'FAM') {
        $tipoAssociado = ' AND PS1030.FLAG_PLANOFAMILIAR = ' . aspas("S");
    }
    if ($_POST['TIPO_ASSOCIADOS'] == 'EMP') {
        $tipoAssociado = ' AND PS1030.FLAG_PLANOFAMILIAR = ' . aspas("N");
    }
    if ($_POST['TIPO_ASSOCIADOS'] == 'AFA') {
        $tipoAssociado = ' AND PS1030.DATA_INUTILIZ_REGISTRO IS NOT NULL';
    }

   
    if ($_POST['ORDENACAO_DADOS'] == 'NUM') {
        $orderCriteria = ' ORDER BY ps1010.codigo_empresa';
	}
    if ($_POST['ORDENACAO_DADOS'] == 'ALFA') {
        $orderCriteria = ' ORDER BY ps1010.nome_empresa';
	}


    if ($_POST['CODIGO_SITUACAO_ATEND'] != '') {
        $situacaoAtendimento = ' AND PS1010.CODIGO_SITUACAO_ATENDIMENTO = ' . $_POST['CODIGO_SITUACAO_ATEND'];
    } 

    if ($_POST['FLAG_NAO_EMPR_EXCLUIDA'] == 'S') {
        $naoExibeEmpresaExcluida = ' AND ((PS1010.DATA_EXCLUSAO IS NULL) OR (PS1010.DATA_EXCLUSAO > ' . dataAngularToSql(date('Y-m-d')) . '))';
    } 


    if ($_POST['DADOS_RELATORIO'] == 'END') {
        $query      =  'SELECT DISTINCT 
                            PS1010.CODIGO_EMPRESA AS CODIGO_EMPRESA,
                            ps1010.nome_empresa AS NOME_EMPRESA,
                            ps1001.endereco AS ENDERECO_EMPRESA,
                            ps1001.bairro AS BAIRRO_EMPRESA,
                            ps1001.cidade AS CIDADE_EMPRESA,
                            ps1001.cep AS CEP_EMPRESA,
                            CASE WHEN PS1006_1.CODIGO_AREA IS NOT NULL THEN CONCAT("(", PS1006_1.CODIGO_AREA, ") ", PS1006_1.NUMERO_TELEFONE) ELSE PS1006_1.NUMERO_TELEFONE END AS TELEFONE_1,
                            CASE WHEN PS1006_2.CODIGO_AREA IS NOT NULL THEN CONCAT("(", PS1006_2.CODIGO_AREA, ") ", PS1006_2.NUMERO_TELEFONE) ELSE PS1006_2.NUMERO_TELEFONE END AS TELEFONE_2,
                            ps1030.nome_plano_abreviado AS NOME_PLANO,
                            ps1010.data_admissao AS DATA_INICIO_CONTRATO,
                            CASE WHEN ps1030.flag_plano_regulamentado = ' . aspas("S") . ' THEN ' . aspas("Sim") . ' ELSE ' . aspas("Nao") . ' END AS PLANO_REGULAMENTADO
                        FROM PS1010
                        Left Outer Join ps1001 On (ps1010.codigo_empresa = ps1001.codigo_empresa)
                        LEFT JOIN PS1006 PS1006_1 ON (PS1010.CODIGO_EMPRESA = PS1006_1.CODIGO_EMPRESA AND PS1006_1.CODIGO_ASSOCIADO IS NULL AND COALESCE(PS1006_1.INDICE_TELEFONE, 1) = 1)
                        LEFT JOIN PS1006 PS1006_2 ON (PS1010.CODIGO_EMPRESA = PS1006_2.CODIGO_EMPRESA AND PS1006_2.CODIGO_ASSOCIADO IS NULL AND PS1006_2.INDICE_TELEFONE = 2)
                        Left Outer Join ps1000 On (ps1000.codigo_empresa = ps1001.codigo_empresa and ps1000.codigo_plano != 400)
                        left outer join ps1030 on (ps1030.codigo_plano = ps1000.codigo_plano)
                        WHERE (PS1010.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ')
                              AND Ps1001.Codigo_Associado Is Null
                              AND PS1010.FLAG_PLANOFAMILIAR != ' . aspas("S") . $tipoAssociado . $situacaoAtendimento . $naoExibeEmpresaExcluida . $orderCriteria;
    }

    if ($_POST['DADOS_RELATORIO'] == 'FAT') {
        $query      =  'SELECT DISTINCT 
                            PS1010.CODIGO_EMPRESA AS CODIGO_EMPRESA,
                            ps1010.nome_empresa AS NOME_EMPRESA,
                            ps1002.dia_vencimento AS DIA_VENCIMENTO,
                            ps1002.codigo_banco AS CODIGO_BANCO,
                            CASE WHEN (ps1010.flag_isento_pagto IS NULL OR ps1010.flag_isento_pagto = ' . aspas("N") . ') THEN ' . aspas("Nao") . ' ELSE ' . aspas("Sim") . ' END AS ISENTO_PAGAMENTO,
                            CASE WHEN (ps1002.flag_cobra_familia IS NULL OR ps1002.flag_cobra_familia = ' . aspas("N") . ') THEN ' . aspas("Nao") . ' ELSE ' . aspas("Sim") . ' END AS COBRA_FAMILIA,
                            ps1002.Numero_contrato AS NUMERO_CONTRATO,                            
                            CASE WHEN (ps1010.flag_valor_particular IS NULL OR ps1010.flag_valor_particular = ' . aspas("N") . ') THEN ' . aspas("Nao") . ' ELSE ' . aspas("Sim") . ' END AS TEM_VALOR_PARTICULAR,
                            CASE WHEN (ps1010.Flag_nota_fiscal IS NULL OR ps1010.Flag_nota_fiscal = ' . aspas("N") . ') THEN ' . aspas("Nao") . ' ELSE ' . aspas("Sim") . ' END AS GERA_NOTA_FISCAL,
                            CASE WHEN (ps1010.flag_Emite_boleto IS NULL OR ps1010.flag_Emite_boleto = ' . aspas("N") . ') THEN ' . aspas("Nao") . ' ELSE ' . aspas("Sim") . ' END AS GERA_BOLETO,
                            ps1030.nome_plano_abreviado AS NOME_PLANO,
                            ps1010.data_admissao AS DATA_INICIO_CONTRATO,
                            CASE WHEN ps1030.flag_plano_regulamentado = ' . aspas("S") . ' THEN ' . aspas("Sim") . ' ELSE ' . aspas("Nao") . ' END AS PLANO_REGULAMENTADO
                        FROM PS1010
                        INNER JOIN PS1002 ON (PS1010.CODIGO_EMPRESA = PS1002.CODIGO_EMPRESA)
                        INNER JOIN PS1001 ON (PS1010.CODIGO_EMPRESA = PS1001.CODIGO_EMPRESA)
                        INNER JOIN PS1000 ON (PS1001.CODIGO_EMPRESA = PS1000.CODIGO_EMPRESA AND PS1000.codigo_plano != 400)
                        INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO)
                        WHERE (PS1010.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ')
                            AND PS1010.FLAG_PLANOFAMILIAR != ' . aspas("S") . $tipoAssociado . $situacaoAtendimento . $naoExibeEmpresaExcluida . $orderCriteria;
    }

    if ($_POST['DADOS_RELATORIO'] == 'DOC') {
        $query      =  'SELECT DISTINCT 
                            PS1010.CODIGO_EMPRESA AS CODIGO_EMPRESA,
                            ps1010.nome_empresa AS NOME_EMPRESA,
                            PS1010.NUMERO_CNPJ AS NUMERO_CNPJ,
                            PS1010.NUMERO_INSC_ESTADUAL AS NUMERO_INSC_ESTADUAL,
                            CASE WHEN PS1006_1.CODIGO_AREA IS NOT NULL THEN CONCAT("(", PS1006_1.CODIGO_AREA, ") ", PS1006_1.NUMERO_TELEFONE) ELSE PS1006_1.NUMERO_TELEFONE END AS TELEFONE_1,
                            CASE WHEN PS1006_2.CODIGO_AREA IS NOT NULL THEN CONCAT("(", PS1006_2.CODIGO_AREA, ") ", PS1006_2.NUMERO_TELEFONE) ELSE PS1006_2.NUMERO_TELEFONE END AS TELEFONE_2,
                            ps1030.nome_plano_abreviado AS NOME_PLANO,
                            ps1010.data_admissao AS DATA_INICIO_CONTRATO,
                            CASE WHEN ps1030.flag_plano_regulamentado = ' . aspas("S") . ' THEN ' . aspas("Sim") . ' ELSE ' . aspas("Nao") . ' END AS PLANO_REGULAMENTADO
                        FROM PS1010
                        INNER JOIN PS1002 ON (PS1010.CODIGO_EMPRESA = PS1002.CODIGO_EMPRESA)
                        INNER JOIN PS1001 ON (PS1010.CODIGO_EMPRESA = PS1001.CODIGO_EMPRESA)
                        INNER JOIN PS1000 ON (PS1000.CODIGO_EMPRESA = PS1001.CODIGO_EMPRESA AND PS1000.CODIGO_PLANO != 400)
                        INNER JOIN PS1030 ON (PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO)
                        LEFT JOIN PS1006 PS1006_1 ON (PS1010.CODIGO_EMPRESA = PS1006_1.CODIGO_EMPRESA AND PS1006_1.CODIGO_ASSOCIADO IS NULL AND COALESCE(PS1006_1.INDICE_TELEFONE, 1) = 1)
                        LEFT JOIN PS1006 PS1006_2 ON (PS1010.CODIGO_EMPRESA = PS1006_2.CODIGO_EMPRESA AND PS1006_2.CODIGO_ASSOCIADO IS NULL AND PS1006_2.INDICE_TELEFONE = 2)
                        WHERE (PS1010.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ')
                            AND Ps1001.Codigo_Associado Is Null
                            AND PS1010.FLAG_PLANOFAMILIAR != ' . aspas("S") . $tipoAssociado . $situacaoAtendimento . $naoExibeEmpresaExcluida . $orderCriteria;
    }

    if ($_POST['DADOS_RELATORIO'] == 'SEG') {
        $query      =  'SELECT DISTINCT 
                            PS1010.CODIGO_EMPRESA AS CODIGO_EMPRESA,
                            ps1010.nome_empresa AS NOME_EMPRESA,
                            (SELECT COUNT(*)
                            FROM PS1010 PS1010_2
                            INNER JOIN PS1000 PS1000_2 ON (PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA AND PS1000_2.CODIGO_PLANO != 400)
                            WHERE PS1010_2.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA AND PS1000_2.TIPO_ASSOCIADO = ' . aspas("T") . ') AS QUANTIDADE_TITULAR,
                            (SELECT COUNT(*)
                            FROM PS1010 PS1010_2
                            INNER JOIN PS1000 PS1000_2 ON (PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA AND PS1000_2.CODIGO_PLANO != 400)
                            WHERE PS1010_2.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA AND PS1000_2.TIPO_ASSOCIADO = ' . aspas("D") . ') AS QUANTIDADE_DEPENDENTE,
                            (SELECT COUNT(*)
                            FROM PS1010 PS1010_2
                            INNER JOIN PS1000 PS1000_2 ON (PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA AND PS1000_2.CODIGO_PLANO != 400)
                            WHERE PS1010_2.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA) AS QUANTIDADE_TOTAL
                        FROM PS1010
                        INNER JOIN PS1000 ON (PS1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA AND PS1000.CODIGO_PLANO != 400)
                        INNER JOIN PS1030 ON (PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO)
                        WHERE (PS1010.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ') '
                            . $tipoAssociado . $situacaoAtendimento . $naoExibeEmpresaExcluida . $orderCriteria;
    }

    if ($_POST['DADOS_RELATORIO'] == 'PRE') {
        $query      =  'SELECT DISTINCT 
                            PS1010.CODIGO_EMPRESA AS CODIGO_EMPRESA,
                            ps1010.nome_empresa AS NOME_EMPRESA,
                            PS1011.IDADE_MINIMA AS IDADE_MINIMA,
                            PS1011.IDADE_MAXIMA AS IDADE_MAXIMA,
                            PS1011.QUANTIDADE_MINIMA AS QUANTIDADE_MINIMA,
                            PS1011.QUANTIDADE_MAXIMA AS QUANTIDADE_MAXIMA,
                            PS1011.VALOR_PLANO AS VALOR_PLANO
                        FROM PS1010
                        INNER JOIN PS1000 ON (PS1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA AND PS1000.CODIGO_PLANO != 400)
                        INNER JOIN PS1030 ON (PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO)
                        INNER JOIN PS1011 ON (PS1011.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA)
                        WHERE (PS1010.CODIGO_EMPRESA BETWEEN ' . $codigoEmpresaInicial . ' AND ' . $codigoEmpresaFinal . ') '
                            . $tipoAssociado . $situacaoAtendimento . $naoExibeEmpresaExcluida . $orderCriteria;
    }

    // pr($query, true);
    
    return $query;

}
/* --------------------------------------------------------------------------------------------------------- */
?>