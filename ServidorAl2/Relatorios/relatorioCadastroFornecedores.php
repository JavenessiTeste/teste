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
	$query      =  'SELECT 
                        PS1100.CODIGO_IDENTIFICACAO AS CODIGO_IDENTIFICACAO, 
                        PS1100.NOME_USUAL AS NOME_USUAL, 
                        PS1100.DATA_CADASTRAMENTO AS DATA_CADASTRO, 
                        PS1100.TIPO_PESSOA AS TIPO_PESSOA, 
                        PS1101.ENDERECO AS ENDERECO, 
                        PS1101.BAIRRO AS BAIRRO, 
                        PS1101.CIDADE AS CIDADE, 
                        PS1101.CEP AS CEP,
                        PS1101.ESTADO AS ESTADO, 
                        PS1101.TELEFONE_PRINCIPAL AS TELEFONE_PRINCIPAL, 
                        PS1101.PESSOA_CONTATO AS PESSOA_CONTATO
                    FROM PS1100
                    LEFT OUTER JOIN PS1101 PS1101 ON (PS1101.CODIGO_IDENTIFICACAO = PS1100.CODIGO_IDENTIFICACAO) ';

    $criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
    
    $criterios .= retornaCriterioRelatorio('PS1100.TIPO_CADASTRO','=',aspas("Cadastro_Fornecedores"),'','N');

    if ($_POST['CODIGO_ID_INICIAL'] != '' && $_POST['CODIGO_ID_FINAL'] != '') {
        $criterios .= ' AND (PS1100.CODIGO_IDENTIFICACAO BETWEEN '. $_POST['CODIGO_ID_INICIAL'] . ' AND ' . $_POST['CODIGO_ID_FINAL'] .  ')';
    }

    if ($_POST['DATA_CADASTR_INICIAL'] != '' && $_POST['DATA_CADASTR_FINAL'] != '') {
        $criterios .= ' AND (PS1100.DATA_CADASTRAMENTO BETWEEN '. dataAngularToSql($_POST['DATA_CADASTR_INICIAL']) . ' AND ' . dataAngularToSql($_POST['DATA_CADASTR_FINAL']) .  ')';
    }

    $orderBy = ' ORDER BY PS1100.CODIGO_IDENTIFICACAO, PS1100.DATA_CADASTRAMENTO';

	return $query . $criterios . $orderBy;

}

/* --------------------------------------------------------------------------------------------------------- */

?>