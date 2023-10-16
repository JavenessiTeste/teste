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
	$query      =  'SELECT 
                        NOME_EMPRESA,
                        PS1023.CODIGO_ASSOCIADO AS CODIGO_ASSOCIADO,
                        PS1000.NOME_ASSOCIADO AS NOME_ASSOCIADO,
                        PS1000.DATA_NASCIMENTO AS DATA_NASCIMENTO,
                        PS1000.DATA_EXCLUSAO AS DATA_EXCLUSAO,
                        PS1000.CODIGO_ANTIGO AS CODIGO_ANTIGO,
                        PS1023.DATA_EVENTO AS DATA_EVENTO,
                        PS1023.VALOR_EVENTO AS VALOR_EVENTO
					FROM PS1023
                    LEFT JOIN PS1020 ON (PS1023.NUMERO_REGISTRO_PS1020 = PS1020.NUMERO_REGISTRO)
                    INNER JOIN PS1000 ON PS1000.CODIGO_ASSOCIADO = PS1023.CODIGO_ASSOCIADO
                    LEFT JOIN PS1010 ON PS1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA ';

    $criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
    $criterios .= retornaCriterioRelatorio('PS1023.CODIGO_EVENTO','=',$_POST['CODIGO_EVENTO'],'NUM','N');

    if ($_POST['CODIGO_EMPRESA_INICIAL'] != '' && $_POST['CODIGO_EMPRESA_FINAL'] != '') {
        $criterios .= ' AND (PS1010.CODIGO_EMPRESA BETWEEN '. $_POST['CODIGO_EMPRESA_INICIAL'] . ' AND ' . $_POST['CODIGO_EMPRESA_FINAL'] .  ')';
    }


    if ($_POST['MES_ANO_REFERENCIA'] != '') {
		$criterios .= retornaCriterioRelatorio('PS1020.MES_ANO_REFERENCIA','=',$_POST['MES_ANO_REFERENCIA'],'ASPAS','N');
	}
    if ($_POST['NUMERO_PARCELA'] != '') {
		$criterios .= retornaCriterioRelatorio('PS1020.NUMERO_PARCELA','=',$_POST['NUMERO_PARCELA'],'ASPAS','N');
	}

    if ($_POST['DATA_EXCLUSAO_INICIAL'] != '' && $_POST['DATA_EXCLUSAO_FINAL'] != '') {
        $criterios .= ' AND (PS1000.DATA_EXCLUSAO BETWEEN '. dataAngularToSql($_POST['DATA_EXCLUSAO_INICIAL']) . ' AND ' . dataAngularToSql($_POST['DATA_EXCLUSAO_FINAL']) .  ')';
    }

    $orderBy = $_POST['ORDENACAO_DADOS'] == 'NUM' ? ' ORDER BY PS1010.CODIGO_EMPRESA, PS1020.NUMERO_PARCELA' : ' ORDER BY NOME_EMPRESA,PS1020.NUMERO_PARCELA';

	return $query . $criterios . $orderBy;

}

/* --------------------------------------------------------------------------------------------------------- */

?>