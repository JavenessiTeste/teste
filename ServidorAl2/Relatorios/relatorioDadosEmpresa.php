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
/*  Data de Implementação: 13/09/2023  				Desenvolvedor: Ricardo 
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');


/* --------------------------------------------------------------------------------------------------------- */


function retornaQueryPrincipal()
{
	
	$query    = ' Select distinct ps1010.codigo_empresa, Ps1010.Nome_Empresa, Ps1001.Endereco, Ps1001.Bairro, Ps1001.Cidade, Ps1001.Cep, Ps1010.Data_Admissao From Ps1010
				Left Join Ps1001 on (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa)
				Left Join Ps1006 on (Ps1010.Codigo_Empresa = Ps1006.Codigo_Empresa) ';

	$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
	$criterios .= retornaCriterioRelatorio('PS1010.FLAG_PLANOFAMILIAR','=','N');
	$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_SITUACAO','=',$_POST['CODIGO_SITUACAO_ATENDIMENTO'],'NUM','N');
	
	//pr($criterios, true);

	$orderBy    = 'Order by PS1010.CODIGO_EMPRESA ';

	return $query . $criterios . $orderBy;	  

}



/* --------------------------------------------------------------------------------------------------------- */









?>

