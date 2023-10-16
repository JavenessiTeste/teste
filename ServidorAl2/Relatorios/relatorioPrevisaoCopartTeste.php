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
/*  Data de Implementação: 15/09/2023  				Desenvolvedor: Tavares
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');


/* --------------------------------------------------------------------------------------------------------- */


function retornaQueryPrincipal()
{
	  $query    =  'SELECT CODIGO_EMPRESA, NOME_EMPRESA, CODIGO_ASSOCIADO, NOME_ASSOCIADO, TIPO_GUIA, DATA_PROCEDIMENTO, 
	  					VALOR_COPARTICIPACAO, NOME_ESPECIALIDADE
					FROM VW_COPART_ADIANTADA_PJ_AL2 ';

	  $criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
	  $criterios .= retornaCriterioRelatorio('CODIGO_ASSOCIADO','=',$_POST['CODIGO_ASSOCIADO'],'NUM','N');
	  $criterios .= retornaCriterioRelatorio('NOME_ESPECIALIDADE','=',$_POST['NOME_ESPECIALIDADE'],'ASPAS','N');
	  $criterios .= retornaCriterioRelatorio('DATA_PROCEDIMENTO','>=',$_POST['DATA_INICIAL'],'DATE','N');
	  $criterios .= retornaCriterioRelatorio('DATA_PROCEDIMENTO','<=',$_POST['DATA_FINAL'],'DATE','N');
	  $criterios .= retornaCriterioRelatorio('TIPO_GUIA','=',$_POST['TIPO_GUIA'],'TEXT','N');

	  $orderBy    = 'Order by CODIGO_EMPRESA, NOME_EMPRESA, NOME_ASSOCIADO ';

	  if ($_POST['ORDEM_DADOS_01'])
		   $orderBy    = ' , ' . $_POST['ORDEM_DADOS_01'];

	  if ($_POST['ORDEM_DADOS_02'])
		   $orderBy    = ' , ' . $_POST['ORDEM_DADOS_02'];

	  if ($_POST['ORDEM_DADOS_03'])
		   $orderBy    = ' , ' . $_POST['ORDEM_DADOS_03'];

		return $query . $criterios . $orderBy;	  

}



/* --------------------------------------------------------------------------------------------------------- */









?>

