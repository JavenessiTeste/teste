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
/*  Data de Implementação: 22/09/2023  				Desenvolvedor: Ricardo 
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');


/* --------------------------------------------------------------------------------------------------------- */

function retornaQueryPrincipal()
{
	
	if ($_POST['TIPO_PLANO'] == 'FAM'){
		
		if($_POST['FLAG_DATA_REAJUSTE']=='S'){
			$dataAdmissaoReajuste = 'PS1002.DATA_ADMIN_CONSID_REAJ AS DATA_REAJUSTE_BENEF';
		}
		else{
			$dataAdmissaoReajuste = 'PS1000.DATA_ADMISSAO as DATA_ADMISSAO_BENEF';
		}
		
		$query 		= ' Select PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, ' . $dataAdmissaoReajuste . ' , Ps1002.DIA_VENCIMENTO,

					(Select Count(a.Codigo_associado) From Ps1000 A Where A.Codigo_Titular = PS1000.Codigo_Associado and A.tipo_associado = "T" ) QTD_TITULARES,
					
					(Select Count(a.Codigo_associado) From Ps1000 A Where A.Codigo_Titular = PS1000.Codigo_Associado and A.tipo_associado = "D" ) QTD_DEPENDENTES,
					
					(Select Count(a.Codigo_associado) From Ps1000 A Where A.Codigo_Titular = PS1000.Codigo_Associado) QTD_VIDAS,
					
					(SELECT top 1(VALOR_CONVENIO + VALOR_CORRECAO)
					FROM PS1020 WHERE CODIGO_ASSOCIADO = Ps1000.Codigo_Associado
					ORDER BY DATA_VENCIMENTO DESC) as ULTIMA_FATURA
					
					FROM PS1000 
					INNER JOIN PS1002 ON (PS1000.CODIGO_ASSOCIADO = PS1002.CODIGO_ASSOCIADO) ';  


		$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');

		if($_POST['FLAG_DATA_REAJUSTE']=='S'){
			$criterios .= retornaCriterioRelatorio('MONTH(PS1002.DATA_ADMIN_CONSID_REAJ)','>=',$_POST['MES_REFERENCIA_INICIAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('MONTH(PS1002.DATA_ADMIN_CONSID_REAJ)','<=',$_POST['MES_REFERENCIA_FINAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('YEAR(PS1002.DATA_ADMIN_CONSID_REAJ)','<>',$_POST['ANO_IGNORADO'],'NUM','N');
		}

		else{
			$criterios .= retornaCriterioRelatorio('MONTH(PS1000.DATA_ADMISSAO)','>=',$_POST['MES_REFERENCIA_INICIAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('MONTH(PS1000.DATA_ADMISSAO)','<=',$_POST['MES_REFERENCIA_FINAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('YEAR(PS1000.DATA_ADMISSAO)','<>',$_POST['ANO_IGNORADO'],'NUM','N');
			$criterios .= ' And PS1000.Data_Exclusao IS NULL And PS1000.Tipo_Associado = "T" And PS1000.Flag_Planofamiliar = "S"';
		}

		$orderBy = ' Order By Ps1000.Data_Admissao ';

	}
	
	elseif ($_POST['TIPO_PLANO'] == 'EMP'){

		if($_POST['FLAG_DATA_REAJUSTE']=='S'){
			$dataAdmissaoReajuste = 'PS1002.DATA_ADMIN_CONSID_REAJ AS DATA_REAJUSTE_EMP';
		}
		else{
			$dataAdmissaoReajuste = 'PS1010.DATA_ADMISSAO as DATA_ADMISSAO_EMP';
		}
		
		$query 		= ' Select PS1010.CODIGO_EMPRESA, PS1010.NOME_EMPRESA,' . $dataAdmissaoReajuste . ', Ps1002.DIA_VENCIMENTO,

					(Select Count(Ps1000.Codigo_Empresa) From Ps1000 Where Ps1000.Codigo_Empresa = PS1010.Codigo_Empresa and Ps1000.tipo_associado = "T" 
					and Ps1000.Data_exclusao is null) QTD_TITULARES,
		
		
					(Select Count(Ps1000.Codigo_Empresa) From Ps1000 Where Ps1000.Codigo_Empresa = PS1010.Codigo_Empresa and 
					Ps1000.tipo_associado = "D" and Ps1000.Data_exclusao is null) QTD_DEPENDENTES,
					
					(Select Count(Ps1000.Codigo_Empresa) From Ps1000 Where Ps1000.Codigo_Empresa = PS1010.Codigo_Empresa and Ps1000.Data_exclusao is null) QTD_VIDAS,

					(SELECT top 1(VALOR_CONVENIO + VALOR_CORRECAO)
					FROM PS1020 WHERE PS1020.Codigo_Empresa = Ps1010.Codigo_Empresa
					ORDER BY DATA_VENCIMENTO DESC) as ULTIMA_FATURA
		
					FROM PS1010 
					INNER JOIN PS1002 ON (PS1010.CODIGO_EMPRESA = PS1002.CODIGO_EMPRESA) ';

		$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
		$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
		$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');

		if($_POST['FLAG_DATA_REAJUSTE']=='S'){
			$criterios .= retornaCriterioRelatorio('MONTH(PS1010.DATA_ADMISSAO)','>=',$_POST['MES_REFERENCIA_INICIAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('MONTH(PS1010.DATA_ADMISSAO)','<=',$_POST['MES_REFERENCIA_FINAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('YEAR(PS1010.DATA_ADMISSAO)','<>',$_POST['ANO_IGNORADO'],'NUM','N');
		}

		else{
			$criterios .= retornaCriterioRelatorio('MONTH(PS1010.DATA_ADMISSAO)','>=',$_POST['MES_REFERENCIA_INICIAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('MONTH(PS1010.DATA_ADMISSAO)','<=',$_POST['MES_REFERENCIA_FINAL'],'NUM','N');
			$criterios .= retornaCriterioRelatorio('YEAR(PS1010.DATA_ADMISSAO)','<>',$_POST['ANO_IGNORADO'],'NUM','N');
		}

		$criterios .= ' And PS1010.Data_Exclusao IS NULL And PS1010.Codigo_Empresa <> 400 ';
					           
	
		$orderBy = ' Order By Ps1010.Codigo_Empresa ';

	}


	return $query . $criterios . $orderBy;	  


}

/* --------------------------------------------------------------------------------------------------------- */




?>

