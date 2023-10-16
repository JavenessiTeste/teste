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
/*  Data de Implementação: 18/08/2023  				Desenvolvedor: Tavares 
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');


/* --------------------------------------------------------------------------------------------------------- */


function retornaQueryPrincipal()
{

	$query    =     "SELECT 
                        ps1005.CODIGO_ASSOCIADO,
                        ps1000.NOME_ASSOCIADO,
                        ps1000.CODIGO_PLANO,
                        ps1005.NUMERO_PERGUNTA,
                        ps1039.DESCRICAO_PERGUNTA,
                        ps1005.RESPOSTA_DIGITADA,
                        ps1005.DESCRICAO_OBSERVACAO
                    from ps1039
                    inner join ps1005 on (ps1005.NUMERO_PERGUNTA = ps1039.NUMERO_PERGUNTA )
                    inner join ps1000 on (ps1005.CODIGO_ASSOCIADO = ps1000.CODIGO_ASSOCIADO and ps1039.CODIGO_PLANO = ps1000.CODIGO_PLANO) ";

	$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');

	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_PLANO','>=',$_POST['CODIGO_PLANO_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_PLANO','<=',$_POST['CODIGO_PLANO_FINAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','>=',$_POST['DATA_ADMISSAO_INICIAL'],'DATE','N');
	$criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','<=',$_POST['DATA_ADMISSAO_FINAL'],'DATE','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','>=',$_POST['CODIGO_SEQUENCIAL_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','<=',$_POST['CODIGO_SEQUENCIAL_FINAL'],'NUM','N');

	$criterios .= retornaCriterioRelatorio('PS1005.RESPOSTA_DIGITADA','=',$_POST['APENAS_RESPOSTAS_POSITIVAS'],'ASPAS','N');

	if ($_POST['LISTAGEM_BENEFICIARIOS'] == 'T') {
		$criterios .= retornaCriterioRelatorio('PS1000.TIPO_ASSOCIADO','=',$_POST['LISTAGEM_BENEFICIARIOS'],'ASPAS','N');
	}

	$orderBy    = 'order by ps1005.CODIGO_ASSOCIADO';

	return $query . $criterios . $orderBy;	  

}

/* --------------------------------------------------------------------------------------------------------- */

?>

