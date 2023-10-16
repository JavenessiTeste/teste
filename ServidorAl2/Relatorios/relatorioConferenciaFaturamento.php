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

	  $query    = 'Select TOP 200 Ps1000.Codigo_Associado, Ps1000.Nome_Associado, Ps1020.Data_Vencimento, Ps1020.Valor_Fatura, 
	                      Ps1020.Data_Emissao, Ps1030.Nome_Plano_Familiares, Ps1010.Nome_Empresa, Ps1000.Data_Admissao, Ps1000.Sexo
	               From Ps1000
	               Inner join Ps1010 On (Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa)
	               Inner join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano)
	               Left Outer join Ps1020 On (Ps1000.Codigo_Titular = Ps1020.codigo_Associado) ';

	  $criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');

	  if ($_POST['TIPO_FATURAMENTO']=='PF')
	     $criterios .= retornaCriterioRelatorio('PS1000.FLAG_PLANOFAMILIAR','=','S','','N');

	  if ($_POST['TIPO_FATURAMENTO']=='PJ')
	     $criterios .= retornaCriterioRelatorio('PS1000.FLAG_PLANOFAMILIAR','=','N','','N');

	  $criterios .= retornaCriterioRelatorio('PS1020.DATA_VENCIMENTO','>=',$_POST['DATA_VENCIMENTO_INICIAL'],'DATE','N');
	  
	  $orderBy    = 'Order by PS1000.CODIGO_EMPRESA, PS1000.CODIGO_PLANO, PS1020.DATA_VENCIMENTO ';

	  return $query . $criterios . $orderBy;	  

}



/* --------------------------------------------------------------------------------------------------------- */









?>

