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
		if($_POST['TIPO_PLANO'] == 'PF'){

			$faturasAbertas = '';
			$faturasPagasComAtraso = '';

			$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
			
			if($_POST['SITUACAO_FATURA']=='ABERTO'){
				$criterios .= ' and (Ps1020.Data_Pagamento is null and valor_pago is null) ';
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','>=',$_POST['CODIGO_SEQ_BENEF_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','<=',$_POST['CODIGO_SEQ_BENEF_FINAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Current_Timestamp)','>=',$_POST['QTD_DIAS_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Current_Timestamp)','<=',$_POST['QTD_DIAS_FINAL'],'TEXT','N');
				$faturasAbertas = ' Datediff(Day,Ps1020.Data_Vencimento,Current_Timestamp) as DIAS_ACUMULADOS ';	
			}
				
			else{
				$criterios .= ' and (Ps1020.Data_Pagamento > Ps1020.Data_Vencimento) ';
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','>=',$_POST['CODIGO_SEQ_BENEF_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1000.CODIGO_SEQUENCIAL','<=',$_POST['CODIGO_SEQ_BENEF_FINAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Data_Pagamento)','>=',$_POST['QTD_DIAS_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Data_Pagamento)','<=',$_POST['QTD_DIAS_FINAL'],'TEXT','N');
				$faturasPagasComAtraso = ' Datediff(Day,Ps1020.Data_Vencimento,Data_Pagamento) AS DIAS_ACUMULADOS ';
			}
		

			$query    = 'Select Ps1020.Codigo_Associado, Nome_Associado, Data_Vencimento, Data_Pagamento, Numero_Registro, ' . $faturasAbertas . $faturasPagasComAtraso . ' from Ps1020
					Inner join Ps1000 on (Ps1000.Codigo_Associado = Ps1020.Codigo_Associado)'; 

			$criterios .= ' and Ps1000.Codigo_Empresa = 400 and Tipo_associado = "T" ';

			if('FLAG_EXIBIR_BENEF_EMP_EXC' == 'N'){
				$criterios .= ' and Ps1000.Data_Exclusao is null ';
			}

			if('FLAG_CONSIDERA_FAT_CANC' == 'N'){
				$criterios .= ' and Ps1020.Data_Cancelamento is null ';
			}

		

			$orderBy = ' Order By Ps1000.Codigo_Associado ';
			

			if($_POST['ORDENAR_RELATORIO'] == 'NOME'){
				$orderBy = ' Order By Ps1000.Nome_Associado ';
			}

		}

		if($_POST['TIPO_PLANO'] == 'PJ'){
			
			$faturasAbertas = '';
			$faturasPagasComAtraso = '';

			$criterios  = retornaCriterioRelatorio('INICIA_CRITERIOS');
			
			if($_POST['SITUACAO_FATURA']=='ABERTO'){
				$criterios .= ' and (Ps1020.Data_Pagamento is null and valor_pago is null) ';
				$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Current_Timestamp)','>=',$_POST['QTD_DIAS_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Current_Timestamp)','<=',$_POST['QTD_DIAS_FINAL'],'TEXT','N');
				$faturasAbertas = ' Datediff(Day,Ps1020.Data_Vencimento,Current_Timestamp) as DIAS_ACUMULADOS ';	
			}
				
			else{
				$criterios .= ' and (Ps1020.Data_Pagamento > Ps1020.Data_Vencimento) ';
				$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','>=',$_POST['CODIGO_EMPRESA_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('PS1010.CODIGO_EMPRESA','<=',$_POST['CODIGO_EMPRESA_FINAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Data_Pagamento)','>=',$_POST['QTD_DIAS_INICIAL'],'TEXT','N');
				$criterios .= retornaCriterioRelatorio('Datediff(Day,Ps1020.Data_Vencimento,Data_Pagamento)','<=',$_POST['QTD_DIAS_FINAL'],'TEXT','N');
				$faturasPagasComAtraso = ' Datediff(Day,Ps1020.Data_Vencimento,Data_Pagamento) AS DIAS_ACUMULADOS ';
			}
		

			$query    = 'Select Ps1020.Codigo_Empresa, Nome_Empresa, Data_Vencimento, Data_Pagamento, Numero_Registro, ' . $faturasAbertas . $faturasPagasComAtraso . ' from Ps1020
					Inner join Ps1010 on (Ps1010.Codigo_Empresa = Ps1020.Codigo_Empresa)'; 

			$criterios .= ' and Ps1010.Codigo_Empresa <> 400 ';

			if('FLAG_EXIBIR_BENEF_EMP_EXC' == 'N'){
				$criterios .= ' and Ps1010.Data_Exclusao is null ';
			}

			if('FLAG_CONSIDERA_FAT_CANC' == 'N'){
				$criterios .= ' and Ps1020.Data_Cancelamento is null ';
			}

		

			$orderBy = ' Order By Ps1010.Codigo_Empresa ';
			

			if($_POST['ORDENAR_RELATORIO'] == 'NOME'){
				$orderBy = ' Order By Ps1010.Nome_Empresa ';
			}


		}
		//pr($query . $criterios . $orderBy,true);
		return $query . $criterios . $orderBy;	  

}



/* --------------------------------------------------------------------------------------------------------- */

?>
