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
/*  Data de Implementação: 21/09/2023  				Desenvolvedor: Tavares 
/*	Ultima manutenção:							  	Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	$queryPrincipal = retornaQueryPrincipal();
	$formato        = $_POST['FORMATO_SAIDA'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,$rowDadosProcesso->IDENTIFICACAO_PROCESSO, $_POST['ID_PROCESSO'],'S');

/* --------------------------------------------------------------------------------------------------------- */

function retornaQueryPrincipal()
{

	$query    = "SELECT
					ps1000_assoc.CODIGO_SEQUENCIAL AS CODIGO_SEQUENCIAL_BENEF,
					ps1000_assoc.NOME_ASSOCIADO AS NOME_ASSOCIADO,
					ps1000_assoc.DATA_NASCIMENTO AS DATA_NASCIMENTO,
					VW_FILTRO_BENEFICIARIOS_IDADE.Idade AS IDADE,
					ps1000_assoc.DATA_EXCLUSAO AS DATA_EXCLUSAO,
					ps1000_tit.CODIGO_SEQUENCIAL AS CODIGO_SEQUENCIAL_TIT,
					ps1000_tit.NOME_ASSOCIADO AS NOME_TITULAR
				FROM PS1000 ps1000_assoc
				INNER JOIN PS1000 ps1000_tit ON (ps1000_assoc.CODIGO_TITULAR = ps1000_tit.CODIGO_ASSOCIADO)
				Inner join VW_FILTRO_BENEFICIARIOS_IDADE on (ps1000_assoc.codigo_associado = VW_FILTRO_BENEFICIARIOS_IDADE.codigo_associado) ";

	$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
	$criterios .= retornaCriterioRelatorio('ps1000_assoc.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('ps1000_assoc.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');
	
	$criterios .= retornaCriterioRelatorio('ps1000_assoc.CODIGO_SEQUENCIAL','>=',$_POST['CODIGO_SEQ_BENEF_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('ps1000_assoc.CODIGO_SEQUENCIAL','<=',$_POST['CODIGO_SEQ_BENEF_FINAL'],'NUM','N');

	$criterios .= retornaCriterioRelatorio('ps1000_assoc.CODIGO_PLANO','>=',$_POST['CODIGO_PLANO_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('ps1000_assoc.CODIGO_PLANO','<=',$_POST['CODIGO_PLANO_FINAL'],'NUM','N');

	$criterios .= retornaCriterioRelatorio('ps1000_assoc.DATA_ADMISSAO','>=',$_POST['DATA_ADMISSAO_INICIAL'],'DATE','N');
	$criterios .= retornaCriterioRelatorio('ps1000_assoc.DATA_ADMISSAO','<=',$_POST['DATA_ADMISSAO_FINAL'],'DATE','N');

	if ($_POST['LISTAGEM_BENEFICIARIOS'] != 'TD') {
		$criterios .= retornaCriterioRelatorio('ps1000_assoc.TIPO_ASSOCIADO','=',$_POST['LISTAGEM_BENEFICIARIOS'],'ASPAS','N');	
	}	
	
	if ($_POST['DATA_VALIDACAO'] != '') {
		$criterios .= retornaCriterioRelatorio('ps1000_assoc.DATA_ADMISSAO','<=',$_POST['DATA_VALIDACAO'],'DATE','N');
		$criterios   .= ' and ((ps1000_assoc.Data_Exclusao is null) or (ps1000_assoc.Data_Exclusao > ' . dataAngularToSql($_POST['DATA_VALIDACAO']) . ')) ';
	}

	if ($_POST['DATA_ANIVERSARIO_INICIAL'] != '') {
		$criterios .= retornaCriterioRelatorio('ps1000_assoc.DATA_NASCIMENTO','>=',$_POST['DATA_ANIVERSARIO_INICIAL'],'DATE','N');		
	}	
	if ($_POST['DATA_ANIVERSARIO_FINAL'] != '') {
		$criterios .= retornaCriterioRelatorio('ps1000_assoc.DATA_NASCIMENTO','<=',$_POST['DATA_ANIVERSARIO_FINAL'],'DATE','N');		
	}

	if ($_POST['IDADE_INICIAL'] != '') {
		$criterios .= retornaCriterioRelatorio('VW_FILTRO_BENEFICIARIOS_IDADE.Idade','>=',$_POST['IDADE_INICIAL'],'NUM','N');
	}
	if ($_POST['IDADE_FINAL'] != '') {
		$criterios .= retornaCriterioRelatorio('VW_FILTRO_BENEFICIARIOS_IDADE.Idade','<=',$_POST['IDADE_FINAL'],'NUM','N');
	}

	if ($_POST['FLAG_NAO_MOSTRAR_EXCLUIDOS'] == 'S') {
		$dataExc = $_POST['DATA_VALIDACAO'] != '' ? $_POST['DATA_VALIDACAO'] : date('Y-m-d');
		$criterios   .= ' and ((ps1000_assoc.Data_Exclusao is null) or (ps1000_assoc.Data_Exclusao > ' . dataAngularToSql($dataExc) . ')) ';
	}     
      
    if ($_POST['ORDENACAO_DADOS'] == 'NUM') {
        $orderCriteria = 'ps1000_assoc.CODIGO_SEQUENCIAL';
	}
    if ($_POST['ORDENACAO_DADOS'] == 'ALFA') {
        $orderCriteria = 'ps1000_assoc.NOME_ASSOCIADO';
	}
    if ($_POST['ORDENACAO_DADOS'] == 'NASC') {
        $orderCriteria = 'ps1000_assoc.DATA_NASCIMENTO';
	}

	$orderBy    = ' order by ' . $orderCriteria ;

	return $query . $criterios . $orderBy;	  

}
/* --------------------------------------------------------------------------------------------------------- */
?>

