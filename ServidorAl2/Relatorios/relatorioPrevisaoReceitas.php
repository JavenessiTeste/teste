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
    $groupBy = ' GROUP BY PS1020.DATA_VENCIMENTO';

    $orderBy = ' ORDER BY PS1020.DATA_VENCIMENTO';

    $filtraPorData = ' PS1020.DATA_VENCIMENTO BETWEEN ' . dataAngularToSql($_POST['DATA_INICIAL_PREVISAO']) . ' AND ' . dataAngularToSql($_POST['DATA_FINAL_PREVISAO']);

    if($_POST['LISTAGEM_BENEFICIARIOS'] == 'ATIV') {
        $query    = 'SELECT
                        PS1020.DATA_VENCIMENTO AS DATA_VENCIMENTO,
                        (COALESCE((SELECT
                            SUM(PS1020_PF.VALOR_FATURA)
                        FROM PS1020 PS1020_PF
                        LEFT OUTER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1020_PF.CODIGO_ASSOCIADO)
                        WHERE (PS1020_PF.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PF.CODIGO_EMPRESA = 400 AND (PS1000.DATA_EXCLUSAO IS NULL OR PS1000.DATA_EXCLUSAO > ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PF.DATA_VENCIMENTO),0)) AS PREVISAO_FAMILIAR,
                        (COALESCE((SELECT
                            SUM(PS1020_PJ.VALOR_FATURA)
                        FROM PS1020 PS1020_PJ
                        LEFT OUTER JOIN PS1010 ON (PS1010.CODIGO_EMPRESA = PS1020_PJ.CODIGO_EMPRESA)
	                    WHERE (PS1020_PJ.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PJ.CODIGO_EMPRESA != 400 AND (PS1010.DATA_EXCLUSAO IS NULL OR PS1010.DATA_EXCLUSAO > ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PJ.DATA_VENCIMENTO),0)) AS PREVISAO_EMPRESARIAL,
                        (COALESCE((SELECT
                            SUM(PS1020_PF.VALOR_PAGO)
                        FROM PS1020 PS1020_PF
                        LEFT OUTER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1020_PF.CODIGO_ASSOCIADO)
                        WHERE (PS1020_PF.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PF.CODIGO_EMPRESA = 400 AND (PS1000.DATA_EXCLUSAO IS NULL OR PS1000.DATA_EXCLUSAO > ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PF.DATA_VENCIMENTO),0)) AS JA_PAGO_FAMILIAR,
                        (COALESCE((SELECT
                            SUM(PS1020_PJ.VALOR_PAGO)
                        FROM PS1020 PS1020_PJ
                        LEFT OUTER JOIN PS1010 ON (PS1010.CODIGO_EMPRESA = PS1020_PJ.CODIGO_EMPRESA)
	                    WHERE (PS1020_PJ.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PJ.CODIGO_EMPRESA != 400 AND (PS1010.DATA_EXCLUSAO IS NULL OR PS1010.DATA_EXCLUSAO > ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PJ.DATA_VENCIMENTO),0)) AS JA_PAGO_EMPRESARIAL
                    FROM PS1020
                    WHERE ' . $filtraPorData . $groupBy . $orderBy;
    }
    if ($_POST['LISTAGEM_BENEFICIARIOS'] == 'EXCL') {
        $query    = 'SELECT
                        PS1020.DATA_VENCIMENTO AS DATA_VENCIMENTO,
                        (COALESCE((SELECT
                            SUM(PS1020_PF.VALOR_FATURA)
                        FROM PS1020 PS1020_PF
                        LEFT OUTER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1020_PF.CODIGO_ASSOCIADO)
                        WHERE (PS1020_PF.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PF.CODIGO_EMPRESA = 400 AND (PS1000.DATA_EXCLUSAO IS NOT NULL OR PS1000.DATA_EXCLUSAO <= ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PF.DATA_VENCIMENTO),0)) AS PREVISAO_FAMILIAR,
                        (COALESCE((SELECT
                            SUM(PS1020_PJ.VALOR_FATURA)
                        FROM PS1020 PS1020_PJ
                        LEFT OUTER JOIN PS1010 ON (PS1010.CODIGO_EMPRESA = PS1020_PJ.CODIGO_EMPRESA)
	                    WHERE (PS1020_PJ.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PJ.CODIGO_EMPRESA != 400 AND (PS1010.DATA_EXCLUSAO IS NOT NULL OR PS1010.DATA_EXCLUSAO <= ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PJ.DATA_VENCIMENTO),0)) AS PREVISAO_EMPRESARIAL,
                        (COALESCE((SELECT
                            SUM(PS1020_PF.VALOR_PAGO)
                        FROM PS1020 PS1020_PF
                        LEFT OUTER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1020_PF.CODIGO_ASSOCIADO)
                        WHERE (PS1020_PF.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PF.CODIGO_EMPRESA = 400 AND (PS1000.DATA_EXCLUSAO IS NOT NULL OR PS1000.DATA_EXCLUSAO <= ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PF.DATA_VENCIMENTO),0)) AS JA_PAGO_FAMILIAR,
                        (COALESCE((SELECT
                            SUM(PS1020_PJ.VALOR_PAGO)
                        FROM PS1020 PS1020_PJ
                        LEFT OUTER JOIN PS1010 ON (PS1010.CODIGO_EMPRESA = PS1020_PJ.CODIGO_EMPRESA)
	                    WHERE (PS1020_PJ.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PJ.CODIGO_EMPRESA != 400 AND (PS1010.DATA_EXCLUSAO IS NOT NULL OR PS1010.DATA_EXCLUSAO <= ' . dataAngularToSql(date('Y-m-d')) . '))
                        GROUP BY PS1020_PJ.DATA_VENCIMENTO),0)) AS JA_PAGO_EMPRESARIAL
                    FROM PS1020
                    WHERE ' . $filtraPorData . $groupBy . $orderBy;
    } 
    if ($_POST['LISTAGEM_BENEFICIARIOS'] == 'AMB') {
        $query    = 'SELECT
                        PS1020.DATA_VENCIMENTO AS DATA_VENCIMENTO,
                        (COALESCE((SELECT
                            SUM(PS1020_PF.VALOR_FATURA)
                        FROM PS1020 PS1020_PF
                        WHERE (PS1020_PF.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PF.CODIGO_EMPRESA = 400)
                        GROUP BY PS1020_PF.DATA_VENCIMENTO),0)) AS PREVISAO_FAMILIAR,
                        (COALESCE((SELECT
                            SUM(PS1020_PJ.VALOR_FATURA)
                        FROM PS1020 PS1020_PJ
                        WHERE (PS1020_PJ.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PJ.CODIGO_EMPRESA != 400)
                        GROUP BY PS1020_PJ.DATA_VENCIMENTO),0)) AS PREVISAO_EMPRESARIAL,
                        (COALESCE((SELECT
                            SUM(PS1020_PF.VALOR_PAGO)
                        FROM PS1020 PS1020_PF
                        WHERE (PS1020_PF.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PF.CODIGO_EMPRESA = 400)
                        GROUP BY PS1020_PF.DATA_VENCIMENTO),0)) AS JA_PAGO_FAMILIAR,
                        (COALESCE((SELECT
                            SUM(PS1020_PJ.VALOR_PAGO)
                        FROM PS1020 PS1020_PJ
                        WHERE (PS1020_PJ.DATA_VENCIMENTO = PS1020.DATA_VENCIMENTO AND PS1020_PJ.CODIGO_EMPRESA != 400)
                        GROUP BY PS1020_PJ.DATA_VENCIMENTO),0)) AS JA_PAGO_EMPRESARIAL
                    FROM PS1020
                    WHERE ' . $filtraPorData . $groupBy . $orderBy;
    }

    return $query;
}

/* --------------------------------------------------------------------------------------------------------- */

?>