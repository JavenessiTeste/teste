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
/*  Data de Implementação: 21/09/2023  				Desenvolvedor: Tavares 
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');

/* --------------------------------------------------------------------------------------------------------- */

function retornaQueryPrincipal()
{
    $dataInicial = dataAngularToSql($_POST['DATA_INICIAL']);
    $dataFinal = dataAngularToSql($_POST['DATA_FINAL']);
    $codigoPlanoInicial = $_POST['CODIGO_PLANO_INICIAL'];
    $codigoPlanoFinal = $_POST['CODIGO_PLANO_FINAL'];
    $situacaoBeneficiario = $_POST['LISTAGEM_BENEFICIARIOS'];
    $usaDataAdmissao = $_POST['FLAG_USA_DATA_ADMISSAO'];

    if ($situacaoBeneficiario == 'ATIV') {
        $queryAdicional = ' AND PS1000_2.DATA_EXCLUSAO IS NULL AND PS1000_2.DATA_DIGITACAO < ' . $dataFinal;
    }
    if ($situacaoBeneficiario == 'CANC') {
        $queryAdicional = ' AND PS1000_2.DATA_EXCLUSAO BETWEEN ' . $dataInicial . ' AND ' . $dataFinal;
    }
    if ($situacaoBeneficiario == 'INCL') {
        if ($usaDataAdmissao == 'S') {
            $queryAdicional = ' AND PS1000_2.DATA_ADMISSAO BETWEEN ' . $dataInicial . ' AND ' . $dataFinal;
        } else {
            $queryAdicional = ' AND PS1000_2.DATA_DIGITACAO BETWEEN ' . $dataInicial . ' AND ' . $dataFinal;
        }
    }

    if ($_POST['ORDENACAO_DADOS'] == 'NUM') {
        $orderCriteria = 'PS1000_1.CODIGO_PLANO';
	}
    if ($_POST['ORDENACAO_DADOS'] == 'ALFA') {
        $orderCriteria = 'PS1030_1.NOME_PLANO_FAMILIARES';
	}

	$query    = 'SELECT 
                    PS1000_1.CODIGO_PLANO AS CODIGO_PLANO,
                    PS1030_1.NOME_PLANO_FAMILIARES AS NOME_PLANO,
                    PS1030_1.CODIGO_CADASTRO_ANS AS CODIGO_CADASTRO_ANS,
                    (SELECT COUNT(*)
                     FROM PS1000 PS1000_2
                     INNER JOIN PS1010 PS1010_2 ON PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA
                     WHERE PS1000_2.FLAG_PLANOFAMILIAR = ' . aspas("S") . '
                     AND PS1000_2.CODIGO_PLANO = PS1000_1.CODIGO_PLANO ' . $queryAdicional . ') AS QUANTIDADE_PF,
                    (SELECT COUNT(*)
                     FROM PS1000 PS1000_2
                     INNER JOIN PS1010 PS1010_2  ON PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA
                     WHERE PS1000_2.FLAG_PLANOFAMILIAR = ' . aspas("N") . '
                     AND PS1000_2.CODIGO_PLANO = PS1000_1.CODIGO_PLANO ' . $queryAdicional . ') AS QUANTIDADE_PJ,
                    (SELECT COALESCE(((SELECT COALESCE(SUM(CAST(VW_FILTRO_BENEFICIARIOS_IDADE.Idade AS INT)), 0)
					 FROM PS1000 PS1000_2
				 	 INNER JOIN PS1010 PS1010_2 ON PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA
					 INNER JOIN VW_FILTRO_BENEFICIARIOS_IDADE on PS1000_2.CODIGO_ASSOCIADO = VW_FILTRO_BENEFICIARIOS_IDADE.CODIGO_ASSOCIADO
					 WHERE PS1000_2.CODIGO_PLANO = PS1000_1.CODIGO_PLANO ' . $queryAdicional . ') / NULLIF((SELECT COUNT(*)
					 FROM PS1000 PS1000_2
					 INNER JOIN PS1010 PS1010_2  ON PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA
					 WHERE PS1000_2.CODIGO_PLANO = PS1000_1.CODIGO_PLANO ' . $queryAdicional . '), 0)), 0))  AS MEDIA_IDADE,                   
                    (SELECT COUNT(*)
                     FROM PS1000 PS1000_2
                     INNER JOIN PS1010 PS1010_2 ON PS1000_2.CODIGO_EMPRESA = PS1010_2.CODIGO_EMPRESA
                     WHERE PS1000_2.CODIGO_PLANO = PS1000_1.CODIGO_PLANO ' . $queryAdicional . ') AS QUANTIDADE_TOTAL                                        
                FROM PS1000 PS1000_1
                INNER JOIN PS1010 PS1010_1 ON PS1000_1.CODIGO_EMPRESA = PS1010_1.CODIGO_EMPRESA
                INNER JOIN PS1030 PS1030_1 ON PS1030_1.CODIGO_PLANO = PS1000_1.CODIGO_PLANO
                WHERE PS1000_1.CODIGO_PLANO BETWEEN ' . $codigoPlanoInicial . ' AND ' . $codigoPlanoFinal . '
                GROUP BY PS1000_1.CODIGO_PLANO, PS1030_1.NOME_PLANO_FAMILIARES, PS1030_1.CODIGO_CADASTRO_ANS
                ORDER BY ' . $orderCriteria;

    return $query;	  

}
/* --------------------------------------------------------------------------------------------------------- */?>