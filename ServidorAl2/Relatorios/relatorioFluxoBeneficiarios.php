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
/*  Data de Implementação: 26/09/2023  				Desenvolvedor: Ricardo 
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
	$planoInicial = ($_POST['CODIGO_PLANO_INICIAL']);
	$planoFinal = ($_POST['CODIGO_PLANO_FINAL']);


	$query    = ' Select Ps1010.Codigo_Empresa, Ps1010.Nome_Empresa,

				(Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa
				and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ') 
				and Ps1000.Data_Admissao < ' . $dataInicial . ' 
				and (Ps1000.Data_Exclusao Is Null or Ps1000.Data_Exclusao > ' . $dataInicial . ')) SALDO_ANTERIOR,


				(Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa 
				and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ') 
				and Ps1000.Data_Admissao between ' . $dataInicial . ' and ' . $dataFinal . ') ENTRADA_BENEF,


				(Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa 
				and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ') 
				and Ps1000.Data_Exclusao between ' . $dataInicial . ' and ' . $dataFinal . ') SAIDA_BENEF,


				(Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa
				and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ')  
				and Ps1000.Data_Admissao < ' . $dataInicial . ' and (Ps1000.Data_Exclusao Is Null or Ps1000.Data_Exclusao > ' . $dataInicial . ')) 
				+ 
				((Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa 
				and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ') 
				and Ps1000.Data_Admissao between ' . $dataInicial . ' and ' . $dataFinal . ')
				- 
				(Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa 
				and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ') 
				and Ps1000.Data_Exclusao between ' . $dataInicial . ' and ' . $dataFinal . ')) SALDO_ATUAL,


				((Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa 
				and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ')
				and Ps1000.Data_Admissao between ' . $dataInicial . ' and ' . $dataFinal . ') 
				-  
			 	(Select Count(*) From Ps1000 Where Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa 
				 and (Ps1000.Codigo_Plano >= ' . $planoInicial . ' and Ps1000.Codigo_Plano <= ' . $planoFinal . ')
				and Ps1000.Data_Exclusao between ' . $dataInicial . ' and ' . $dataFinal . ')) SALDO_MOVIMENTACAO
		

				From Ps1010 
				
				Left join Ps1002 on (Ps1010.Codigo_Empresa = Ps1002.Codigo_Empresa and (Ps1002.codigo_associado is null))
				Left join Ps3100 on (Ps1002.Numero_Contrato = Ps3100.Numero_Contrato) 
				Left join Ps1100 on (Ps3100.Codigo_Identificacao = Ps1100.Codigo_Identificacao ) ';  


	$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
	$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS3100.CODIGO_IDENTIFICACAO','=',$_POST['CODIGO_IDENTIFICACAO'],'NUM','N');


	$orderBy = ' Order By Ps1010.Codigo_Empresa ';


	return $query . $criterios . $orderBy;	  


}

/* --------------------------------------------------------------------------------------------------------- */




?>

