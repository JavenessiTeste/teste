<?php

use GuzzleHttp\Psr7\Query;

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

	if ($_POST['TIPO_TABELA'] == 'EMP'){

		$query    = '  Select Concat(Ps1010.Codigo_Empresa, " - ",  Ps1010.Nome_Empresa) as CODIGO_NOMEEMPRESA, Ps1002.numero_contrato, MES_ANO_REFERENCIA, Ps1019.Fator_Conversao as INDICE, 
		Concat(Ps1030.Codigo_Plano, " - ", Ps1030.nome_plano_empresas) CODIGO_NOMEPLANO, Ps1010.tipo_geracao_faturam_cobranca TIPO_LANCAMENTO,
		Concat(Ps1011.IDADE_MINIMA, " - ", Ps1011.IDADE_MAXIMA) IDADE_MINIMA_MAXIMA, Ps1011.CODIGO_TABELA_PRECO, Ps1011.Valor_Plano as Valor_Antigo,
		((Ps1019.FATOR_CONVERSAO / 100) * Ps1011.VALOR_PLANO) + PS1011.VALOR_PLANO as VALOR_ATUAL

		
		from ps1010
		
		inner join ps1002 on ps1010.codigo_empresa = ps1002.codigo_empresa 
		inner join ps1011 on ps1010.codigo_empresa = ps1011.codigo_empresa
		Inner join ps1019 on (ps1019.codigo_empresa = ps1011.codigo_empresa and Ps1019.Codigo_Plano = Ps1011.Codigo_Plano)
		inner join ps1030 on ps1011.codigo_plano = ps1030.codigo_plano '; 
		
		
		$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
		$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
		$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');
		$criterios .= retornaCriterioRelatorio('PS1010.DATA_ADMISSAO','>=',$_POST['DATA_ADMISSAO_INICIAL'],'DATE','N');
		$criterios .= retornaCriterioRelatorio('PS1010.DATA_ADMISSAO','<=',$_POST['DATA_ADMISSAO_FINAL'],'DATE','N');
		$criterios .= retornaCriterioRelatorio('PS1019.MES_ANO_REFERENCIA','>=',$_POST['MES_ANO_INICIAL'],'TEXT','N');
		$criterios .= retornaCriterioRelatorio('PS1019.MES_ANO_REFERENCIA','<=',$_POST['MES_ANO_FINAL'],'TEXT','N');

		

		if ($_POST['FLAG_NAO_EMPR_EXCLUIDA']){
			$criterios .= ' and (Ps1010.Data_Exclusao Is Null or Ps1010.Data_Exclusao > ' . date("Y-d-m") . ')';
		}
		


		$orderBy = ' Order By Ps1010.Codigo_Empresa, Ps1011.Codigo_Plano, Ps1019.Mes_Ano_Referencia, Ps1011.Idade_Minima ';

	
	}

	//pr($query . $criterios . $orderBy, true);
	return $query . $criterios . $orderBy;	 	  

}



/* --------------------------------------------------------------------------------------------------------- */









?>

