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

// campoResultado($numeroRegistroProcesso,'DATA_SOLICITACAO','DATE','Data solicitação');
// campoResultado($numeroRegistroProcesso,'NOME_PESSOA','VARCHAR','Nome');
// campoResultado($numeroRegistroProcesso,'EMAIL_CONTATO','VARCHAR','E-mail');
// campoResultado($numeroRegistroProcesso,'TELEFONE_CONTATO','VARCHAR','Telefone');
// campoResultado($numeroRegistroProcesso,'OBSERVACOES','VARCHAR','Observação','50');
// campoResultado($numeroRegistroProcesso,'PLANO_DE_INTERESSE','VARCHAR','Plano de interesse','50');

{
	$query      =  'SELECT 
                        PS3500.DATA_SOLICITACAO AS DATA_SOLICITACAO, 
                        PS3500.NOME_PESSOA AS NOME_PESSOA, 
                        PS3500.EMAIL_CONTATO AS EMAIL_CONTATO, 
                        PS3500.TELEFONE_CONTATO_01 AS TELEFONE_CONTATO, 
                        PS3500.OBSERVACOES AS OBSERVACOES, 
                        PS3500.PLANO_DE_INTERESSE AS PLANO_DE_INTERESSE
                    FROM PS3500 ';

    $criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');

    if ($_POST['DATA_SOLICIT_INICIAL'] != '' && $_POST['DATA_SOLICIT_FINAL'] != '') {
        $criterios .= ' AND (PS3500.DATA_SOLICITACAO BETWEEN '. dataAngularToSql($_POST['DATA_SOLICIT_INICIAL']) . ' AND ' . dataAngularToSql($_POST['DATA_SOLICIT_FINAL']) .  ')';
    }

    $orderBy = ' ORDER BY PS3500.DATA_SOLICITACAO';

	return $query . $criterios . $orderBy;

}

/* --------------------------------------------------------------------------------------------------------- */

?>