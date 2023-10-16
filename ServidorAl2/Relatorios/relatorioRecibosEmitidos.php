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


		$query    = ' Select Ps1022.Numero_Recibo, Concat(Coalesce(Cast(Ps1022.Codigo_Empresa as varchar (5)),Ps1022.Codigo_Associado), " - ",Coalesce(Ps1010.Nome_Empresa, Ps1000.Nome_Associado)) CODIGO_NOME_BENEF_EMP,
					Concat(Ps1022.Tipo_Recibo,"-", Ps1022.Referencia_Recibo) Referencia_Recibo, Ps1022.Valor_Total_Recibo, Ps1022.Data_Emissao,   Ps1022.Data_Pagamento, Ps1100.NOME_USUAL, Ps1022.Observacao_Recibo, PS1022.Tipo_Especie
					from Ps1022
					Left outer join Ps1020 on Ps1022.Numero_Recibo = Ps1020.Numero_Recibo
					Left outer join Ps1010 on Ps1010.Codigo_Empresa = Ps1022.Codigo_Empresa
					Left outer join Ps1000 on Ps1000.Codigo_Associado = Ps1022.Codigo_Associado
					Left outer join Ps1100 on Ps1022.Codigo_Operador = Ps1100.Codigo_Identificacao '; 
		
		
		$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
		
		$criterios .= retornaCriterioRelatorio('PS1022.DATA_EMISSAO','>=',$_POST['DATA_INICIAL_EMISSAO'],'DATE','N');
		$criterios .= retornaCriterioRelatorio('PS1022.DATA_EMISSAO','<=',$_POST['DATA_FINAL_EMISSAO'],'DATE','N');
		$criterios .= retornaCriterioRelatorio('PS1022.CODIGO_OPERADOR','=',$_POST['CODIGO_OPERADOR'],'TEXT','N');

		if ($_POST['SITUACAO_RECIBO'] == 'ATIVOS'){
			$criterios .= ' AND (PS1022.DATA_CANCELAMENTO IS NULL OR PS1022.DATA_CANCELAMENTO = "" ) '; 
		}

		elseif($_POST['SITUACAO_RECIBO'] == 'CANCELADOS'){
			$criterios .= ' AND (PS1022.DATA_CANCELAMENTO IS NOT NULL OR PS1022.DATA_CANCELAMENTO <> "" ) ';
		}

	

		
		$orderBy = ' Order By Ps1022.Numero_Recibo ';

	
	

	//pr($query . $criterios . $orderBy, true);
	return $query . $criterios . $orderBy;	 	  


}


/* --------------------------------------------------------------------------------------------------------- */









?>

