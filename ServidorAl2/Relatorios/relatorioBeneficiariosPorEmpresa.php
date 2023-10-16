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
	if ($_POST['MOSTRAR_CPF'] == 'S'){
		$Cpf_TabelaPreco = 'PS1000.Numero_Cpf'; 
	}else{
		$Cpf_TabelaPreco = 'PS1000.Codigo_Tabela_Preco';
	}

	$query    = ' Select Ps1000.Codigo_Associado, Ps1000.Nome_Associado, Ps1000.Data_Nascimento, Ps1000.Data_Admissao, Concat(Ps1000.Codigo_Plano, " - ", Ps1030.Nome_Plano_Abreviado) 
				as Plano, (Select top 01 Ps1021.Valor_Fatura From Ps1021 Where (Ps1021.Codigo_Associado = Ps1000.Codigo_Associado) Order By Numero_Registro desc)
				Valor_Ultima_Fatura, VW_FILTRO_BENEFICIARIOS_IDADE.Idade, ' . $Cpf_TabelaPreco . ' ,Ps1010.Codigo_Empresa,
				Ps1010.Nome_Empresa, Ps1000.Tipo_Associado
				From Ps1000
				Inner join Ps1010 On (Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa) 
				Inner join Ps1030 On (Ps1030.Codigo_Plano = Ps1000.Codigo_Plano)
				Inner join VW_FILTRO_BENEFICIARIOS_IDADE on (Ps1000.codigo_associado = VW_FILTRO_BENEFICIARIOS_IDADE.codigo_associado) ';

	

	$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','<=',$_POST['DATA_LIMITE_VALIDACAO'],'DATE','N');
	$criterios .= ' and ((PS1000.DATA_EXCLUSAO IS NULL) OR (PS1000.DATA_EXCLUSAO > ' . dataAngularToSql($_POST['DATA_LIMITE_VALIDACAO']) . ')) ';
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_PLANO','>=',$_POST['CODIGO_PLANO_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_PLANO','<=',$_POST['CODIGO_PLANO_FINAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','>=',$_POST['DATA_ADMISSAO_INICIAL'],'DATE','N');
	$criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','<=',$_POST['DATA_ADMISSAO_FINAL'],'DATE','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','>=',$_POST['CODIGO_SEQUENCIAL_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','<=',$_POST['CODIGO_SEQUENCIAL_FINAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.GRUPO_PESSOAS','>=',$_POST['GRUPO_PESSOAS_INICIAL'],'NUM','N');
	$criterios .= retornaCriterioRelatorio('PS1000.GRUPO_PESSOAS','<=',$_POST['GRUPO_PESSOAS_FINAL'],'NUM','N');
	

	/* VALIDAÇÃO DE FLAGS */

	if ($_POST['MOSTRAR_BENEFICIARIOS'] == 'Apenas_Titulares'){
		$criterios .= retornaCriterioRelatorio('PS1000.TIPO_ASSOCIADO','=','T','TEXT','N'); 
	}

	$orderBy = 'ORDER BY PS1000.CODIGO_EMPRESA ';

	/*  ORDENAÇÃO DE DADOS  */	

	if ($_POST['ORDEM_DADOS_01'] == 'Numérica'){
		$orderBy    .= ' , ' . 'PS1000.CODIGO_ASSOCIADO';

	}else{
		$orderBy    = 'ORDER BY PS1010.NOME_EMPRESA, PS1000.NOME_ASSOCIADO ';
	}
	

		return $query . $criterios . $orderBy;	  

}



/* --------------------------------------------------------------------------------------------------------- */




?>

