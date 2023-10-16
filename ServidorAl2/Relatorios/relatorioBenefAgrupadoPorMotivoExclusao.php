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
	
	$dataInicialExclusao = dataAngularToSql($_POST['DATA_INICIAL_EXCLUSAO']);
	$dataFinalExclusao = dataAngularToSql($_POST['DATA_FINAL_EXCLUSAO']);
	$mesAno_VencimentoInicial = aspas(extraiMesAnoData($_POST['DATA_INICIAL_EXCLUSAO']));
	$mesAno_VencimentoFinal = aspas(extraiMesAnoData($_POST['DATA_FINAL_EXCLUSAO']));
	$ano_DataExclusao = year($_POST['DATA_INICIAL_EXCLUSAO']);

	


	$query    = ' Select Ps1047.Codigo_Motivo_Exclusao, Ps1047.Nome_Motivo_Exclusao,

				(select Count(*) From Ps1000
				where Ps1000.Codigo_Motivo_Exclusao = Ps1047.Codigo_Motivo_Exclusao
				and Data_Exclusao Between ' . $dataInicialExclusao . ' and ' . $dataFinalExclusao . ') as Numero_Vidas,

				(select Count(*)
				from Ps1000 Inner Join Ps1002 on Ps1000.Codigo_Associado = Ps1002.Codigo_Associado
				where Ps1000.Codigo_Motivo_Exclusao = Ps1047.Codigo_Motivo_Exclusao
				and Data_Exclusao Between ' . $dataInicialExclusao . ' and ' . $dataFinalExclusao . ') +

				(select Count(*)
				from Ps1010 Inner Join Ps1002 on Ps1010.Codigo_Empresa = Ps1002.Codigo_Empresa
				where Ps1010.Codigo_Motivo_Exclusao = Ps1047.Codigo_Motivo_Exclusao
				and Data_Exclusao Between ' . $dataInicialExclusao . ' and ' . $dataFinalExclusao . ') as Contratos,

				(select sum(Valor_Fatura)
				from Ps1021 Inner Join Ps1000 on Ps1000.Codigo_Associado = Ps1021.Codigo_Associado
				where Ps1000.Codigo_Motivo_Exclusao = Ps1047.Codigo_Motivo_Exclusao
				and Ps1000.Data_Exclusao Between ' . $dataInicialExclusao . ' and ' . $dataFinalExclusao . '
				and Ps1021.Mes_Ano_Vencimento >= ' . $mesAno_VencimentoInicial . '
				and substring(Ps1021.Mes_Ano_Vencimento,4,4) = ' . $ano_DataExclusao . '
				and Ps1021.Mes_Ano_Vencimento <= ' . $mesAno_VencimentoFinal . ') as Total_Faturado

				from Ps1047 ';

	
	$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');

	$orderBy = 'ORDER BY PS1047.CODIGO_MOTIVO_EXCLUSAO ';


		return $query . $criterios . $orderBy;	  

}



/* --------------------------------------------------------------------------------------------------------- */




?>

