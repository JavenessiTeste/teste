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

	  $innerOpcional = '';

	  if ($_POST['TIPO_CAMPO_IMPRIMIR']=='DATA_VENCIMENTO')
	      $campoOpcional = 'PS1020.DATA_VENCIMENTO, ';	
	  else if ($_POST['TIPO_CAMPO_IMPRIMIR']=='NOME_MAE')
	      $campoOpcional = 'PS1000.NOME_MAE, ';	
	  else if ($_POST['TIPO_CAMPO_IMPRIMIR']=='NUMERO_TELEFONE')
	  {
		  $campoOpcional = 'PS1006.NUMERO_TELEFONE, ';	
          $innerOpcional = 'left outer join ps1006 on (ps1000.codigo_associado = ps1006.codigo_associado)';
	  }

	  $query    = 'Select TOP 200 Ps1000.Codigo_Associado, Ps1000.Nome_Associado, Ps1020.Valor_Fatura, ' . $campoOpcional . 
	                    ' Ps1020.Data_Pagamento, Ps1020.Valor_Pago, 
	                      Ps1030.Nome_Plano_Familiares NOME_PLANO, Ps1010.Nome_Empresa, Ps1000.Codigo_Empresa, Ps1000.Codigo_Plano 
	               From Ps1000
	               Inner join Ps1010 On (Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa)
	               Inner join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ' . 
	               $innerOpcional . '
	               Left Outer join Ps1020 On (Ps1000.Codigo_Titular = Ps1020.codigo_Associado) ';

	  $criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
	  $criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'NUM','N');
	  $criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'NUM','N');
	  $criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','>=',$_POST['DATA_ADMISSAO_INICIAL'],'DATE','N');
	  $criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','<=',$_POST['DATA_ADMISSAO_FINAL'],'DATE','N');
	  $criterios .= retornaCriterioRelatorio('PS1010.NOME_EMPRESA','Like',$_POST['NOME_EMPRESA'],'ASPAS','N');
	  $criterios .= retornaCriterioRelatorio('PS1020.DATA_VENCIMENTO','>=',$_POST['DATA_VENCIMENTO_MAIOR_QUE'],'DATE','N');

	  $criterios .= retornaCriterioRelatorio('PS1000.DATA_ADMISSAO','<=',$_POST['DATA_VALIDACAO_BENEFICARIO'],'DATE','N');
	  $criterios   .= ' and ((Ps1000.Data_Exclusao is null) or (Ps1000.Data_Exclusao > ' . dataAngularToSql($_POST['DATA_VALIDACAO_BENEFICARIO']) . ')) ';

	  $orderBy    = 'Order by PS1000.CODIGO_EMPRESA, PS1000.CODIGO_PLANO, PS1020.DATA_VENCIMENTO ';

	  if ($_POST['ORDEM_DADOS_01']!='')
		   $orderBy    .= ' , ' . $_POST['ORDEM_DADOS_01'];

	  if ($_POST['ORDEM_DADOS_02'])
		   $orderBy    .= ' , ' . $_POST['ORDEM_DADOS_02'];

	  if ($_POST['ORDEM_DADOS_03'])
		   $orderBy    .= ' , ' . $_POST['ORDEM_DADOS_03'];

		return $query . $criterios . $orderBy;	  

}



/* --------------------------------------------------------------------------------------------------------- */









?>

