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
	$query      =  'SELECT PS1100.CODIGO_IDENTIFICACAO AS CODIGO_IDENTIFICACAO,
                        PS1100.NOME_USUAL AS NOME_USUAL,
                        PS3100.NUMERO_CONTRATO AS NUMERO_CONTRATO, 
                        PS3100.DATA_CONTRATO AS DATA_CONTRATO, 
                        PS3100.NOME_CONTRATANTE AS NOME_CONTRATANTE, 
                        PS3100.VALOR_CONTRATO AS VALOR_CONTRATO,
                        PS3100.CODIGO_TIPO_COMISSAO AS CODIGO_TIPO_COMISSAO, 
                        VW_VIDASPORVENDEDOR.VIDAS AS QTDE_VIDAS
                    FROM PS1100
                    INNER JOIN PS3100 ON (PS3100.CODIGO_IDENTIFICACAO = PS1100.CODIGO_IDENTIFICACAO)
                    INNER JOIN VW_VIDASPORVENDEDOR VW_VIDASPORVENDEDOR ON (VW_VIDASPORVENDEDOR.NUMERO_CONTRATO = PS3100.NUMERO_CONTRATO) ';

    $criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');

    if ($_POST['CODIGO_ID_INICIAL'] != '' && $_POST['CODIGO_ID_FINAL'] != '') {
        $criterios .= ' AND (PS1100.CODIGO_IDENTIFICACAO BETWEEN '. $_POST['CODIGO_ID_INICIAL'] . ' AND ' . $_POST['CODIGO_ID_FINAL'] .  ')';
    }

    if ($_POST['DATA_CONTRATO_INICIAL'] != '' && $_POST['DATA_CONTRATO_FINAL'] != '') {
        $criterios .= ' AND (PS3100.DATA_CONTRATO BETWEEN '. dataAngularToSql($_POST['DATA_CONTRATO_INICIAL']) . ' AND ' . dataAngularToSql($_POST['DATA_CONTRATO_FINAL']) .  ')';
    }

    $orderBy = ' ORDER BY PS3100.CODIGO_IDENTIFICACAO, PS3100.DATA_CONTRATO';

	return $query . $criterios . $orderBy;

}

/* --------------------------------------------------------------------------------------------------------- */

?>