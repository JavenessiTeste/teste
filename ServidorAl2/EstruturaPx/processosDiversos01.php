<?php

	require('../lib/base.php');
	require('../EstruturaPrincipal/processoPx.php');
	require('../lib/sysutilsAlianca.php');
	require('./calculoFaturamento.php');

	header("Content-Type: text/html; charset=ISO-8859-1",true);

	//	$_GET['Teste'] = 'OK';
	//  prDebug($_POST['ID_PROCESSO']);

	set_time_limit(0);

/* --------------------------------------------------------------------------------------------------------- */
/*	INICIA REGISTROS E INFORMAÇÕES DO PROCESSO
/*  Função: Apresenta mensagem para o usuário e dá inicio ao processo
/*  Data de Implementação: 01/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$linhaArquivo      	   	  	        = '';
	$linhaLogProcesso  			        = '';
	$resultadoProcesso                  = array();
	$resultadoProcesso['MSG_RESULTADO'] = '';
	$resultadoProcesso['ID_RESULTADO']  = 'OK'; //ERRO, ERRO_VALIDACAO, AVISO_VALIDACAO;
	$nomeArquivoProcesso                = '';
	$nomearquivoLogProcesso             = '';
	$referenciaIdProcesso      	        = rand() . '_' . date('d/m/Y') . '_' . date('H:i');

	echo apresentaMensagemInicioProcesso('PROC_RAPIDO');
	$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

	//pr($_POST['ID_PROCESSO']);
	//pr($_POST);
	//pr($rowDadosProcesso->IDENTIFICACAO_PROCESSO);



/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 2210
/*  Função: Troca de titualidade
/*  Data de Implementação: 10/10/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='2210') 
	{

	   $codigoTitularOriginal      = $_POST['CODIGO_ASSOCIADO_TITULAR'];
	   $codigoDependenteAConverter = $_POST['CODIGO_ASSOCIADO_DEPENDENTE'];

	   $rowTmp       = qryUmRegistro('Select CODIGO_ASSOCIADO, TIPO_ASSOCIADO, DATA_EXCLUSAO From Ps1000 Where (Codigo_Associado = ' . aspas($codigoTitularOriginal) . ')');

	   if ($rowTmp->TIPO_ASSOCIADO!='T')
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'O Código titular informado não é válido, este código pertence a um dependente.';
			$resultadoProcesso['ID_RESULTADO']  = 'ERRO_VALIDACAO';
	   }
	   else if ($rowTmp->DATA_EXCLUSAO != '')
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'O titular selecionado está excluído, esta rotina apenas pode ser executada com titulares ativos.';
			$resultadoProcesso['ID_RESULTADO']  = 'ERRO_VALIDACAO';
	   }

	   $rowTmp       = qryUmRegistro('Select CODIGO_ASSOCIADO, CODIGO_TITULAR, TIPO_ASSOCIADO, DATA_EXCLUSAO From Ps1000 Where (Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ')');

	   if ($rowTmp->TIPO_ASSOCIADO!='D')
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'O Código do dependente informado não é válido, este código pertence a um titular.';
			$resultadoProcesso['ID_RESULTADO']  = 'ERRO_VALIDACAO';
	   }
	   else if ($rowTmp->DATA_EXCLUSAO != '')
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'O dependente selecionado está excluído, esta rotina apenas pode ser executada com dependentes ativos.';
			$resultadoProcesso['ID_RESULTADO']  = 'ERRO_VALIDACAO';
	   }
	   else if ($rowTmp->CODIGO_TITULAR!=$codigoTitularOriginal)
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'O dependente selecionado não está no mesmo grupo familiar do titular.';
			$resultadoProcesso['ID_RESULTADO']  = 'ERRO_VALIDACAO';
	   }

	   //

	   if ($resultadoProcesso['MSG_RESULTADO']=='')
	   {

			$sqlExclusao = '';

		    if ($_POST['FLAG_EXCLUIR_TITULAR_ANTIGO']=='S')
				$sqlExclusao = ' , DATA_EXCLUSAO = ' . DataToSql(sqlToData(dataHoje()));

			$rowTit       = qryUmRegistro('select * from PS1000 where Codigo_Associado=' . aspas($codigoTitularOriginal));
			$rowDep       = qryUmRegistro('select * from PS1000 where Codigo_Associado=' . aspas($codigoDependenteAConverter));

			// armazena os dados do novo titular em variaveis
			$NomeAssociado     = $rowDep->NOME_ASSOCIADO;
			$Sexo              = $rowDep->SEXO;
			$DataNascimento    = $rowDep->DATA_NASCIMENTO;
			$DataAdmissao      = $rowDep->DATA_ADMISSAO;
			$DataDigitacao     = $rowDep->DATA_DIGITACAO;
			$DataExclusao      = $rowDep->DATA_EXCLUSAO;
			$CodigoExclusao    = $rowDep->CODIGO_MOTIVO_EXCLUSAO;
			$ValorNominal      = $rowDep->VALOR_NOMINAL;
			$NomeAbreviado     = $rowDep->NOME_ABREVIADO;
			$CodigoANS         = $rowDep->CODIGO_CADASTRO_ANS;
			$NumeroCPF         = $rowDep->NUMERO_CPF;
			$NumeroRG          = $rowDep->NUMERO_RG;
			$OrgaoEmissorRG    = $rowDep->ORGAO_EMISSOR_RG;
			$NomeMae           = $rowDep->NOME_MAE;
			$NomePai           = $rowDep->NOME_PAI;
			$CodigoCarencia    = $rowDep->CODIGO_CARENCIA;
			$CodigoTabelaPreco = $rowDep->CODIGO_TABELA_PRECO;
			$CodigoEstadoCivil = $rowDep->CODIGO_ESTADO_CIVIL;
			$CodigoPaisEmissor = $rowDep->CODIGO_PAIS_EMISSOR;
			$NumeroCCO         = $rowDep->NUMERO_CCO;

			jn_query('update Ps1000' .
					' set Nome_Associado     = ' . aspas($NomeAssociado)          .
					',Sexo                   = ' . aspas($Sexo)                   .
					',Data_Nascimento        = ' . DataToSql(sqlToData($DataNascimento))       .
					',Data_Admissao          = ' . DataToSql(sqlToData($DataAdmissao))         .
					',Data_Digitacao         = ' . DataToSql(sqlToData($DataDigitacao))        .
					',Codigo_Motivo_Exclusao = ' . integerNull($CodigoExclusao)    .
					',Valor_Nominal          = ' . valSql($ValorNominal)      .
					',Nome_Abreviado         = ' . aspas($NomeAbreviado)          .
					',Codigo_Cadastro_ANS    = ' . aspas($CodigoANS)              .
					',Tipo_Associado         = ' . aspas('T')                     .
					',Numero_CPF             = ' . aspas($NumeroCPF)              .
					',Numero_RG              = ' . aspas($NumeroRG)               .
					',Orgao_Emissor_RG       = ' . aspas($OrgaoEmissorRG)         .
					',Nome_Mae               = ' . aspas($NomeMae)                .
					',Nome_Pai               = ' . aspas($NomePai)                .
					',Codigo_Carencia        = ' . integerNull($CodigoCarencia)    .
					',Codigo_Tabela_Preco    = ' . integerNull($CodigoTabelaPreco) .
					',Codigo_Pais_Emissor    = ' . integerNull($CodigoPaisEmissor) .
					',Numero_CCO             = ' . aspas($NumeroCCO)              .
					' where Codigo_Associado = ' . aspas($codigoTitularOriginal));

			jn_query('update Ps1000' .
					' set Nome_Associado      = ' . aspas($rowTit->NOME_ASSOCIADO)           .
					', Sexo                   = ' . aspas($rowTit->SEXO)                     .
					', Data_Nascimento        = ' . DataToSql(sqlToData($rowTit->DATA_NASCIMENTO))        .
					', Data_Admissao          = ' . DataToSql(sqlToData($rowTit->DATA_ADMISSAO))          .
					', Data_Digitacao         = ' . DataToSql(sqlToData($rowTit->DATA_DIGITACAO))         .
					', Valor_Nominal          = ' . valSql($rowTit->VALOR_NOMINAL) .
					', Nome_Abreviado         = ' . aspas($rowTit->NOME_ABREVIADO)           .
					', Codigo_Cadastro_ANS    = ' . aspas($rowTit->CODIGO_CADASTRO_ANS)      .
					', Tipo_Associado         = ' . aspas('D')                                                         .
					', Numero_CPF             = ' . aspas($rowTit->NUMERO_CPF)               .
					', Numero_RG              = ' . aspas($rowTit->NUMERO_RG)                .
					', Orgao_Emissor_RG       = ' . aspas($rowTit->ORGAO_EMISSOR_RG)         .
					', Nome_Mae               = ' . aspas($rowTit->NOME_MAE)                 .
					', Nome_Pai               = ' . aspas($rowTit->NOME_PAI)                 .
					', Codigo_Carencia        = ' . integerNull($rowTit->CODIGO_CARENCIA)     .
					', Codigo_Tabela_Preco    = ' . integerNull($rowTit->CODIGO_TABELA_PRECO) .
					', Codigo_Pais_Emissor    = ' . integerNull($rowTit->CODIGO_PAIS_EMISSOR) .
					', Numero_CCO             = ' . aspas($rowTit->NUMERO_CCO)               .
					$sqlExclusao . 
					'  where Codigo_Associado = ' . aspas($codigoDependenteAConverter));


			jn_query('Update Ps5900 Set Referencia_Importacao = Null Where Codigo_Associado in 
                      (Select Ps1000.Codigo_Associado From Ps1000 Where (Codigo_Titular = '. aspas($codigoTitularOriginal) . '))');

			jn_query('Update Ps6500 Set Descricao_Observacao  = Null Where Codigo_Associado Like '. aspas(copyEstruturaFamilia($codigoTitularOriginal).'%'));

			// transferencia de historico do novo titular

			jn_query( 'Update Ps5900 set Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ', Referencia_Importacao = ' . aspas('TROCA TITULAR') .
												' where Codigo_Associado = ' . aspas($codigoTitularOriginal));
			jn_query( 'Update Ps6500 set Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ', Descricao_Observacao  = ' . aspas('TROCA TITULAR') .
												' where Codigo_Associado = ' . aspas($codigoTitularOriginal));
			jn_query( 'update Ps1004 set Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ', Referencia_Importacao = ' . aspas('TROCA TITULAR') .
												' where Codigo_Associado = ' . aspas($codigoTitularOriginal));
			jn_query( 'update Ps1005 set Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ', Referencia_Importacao = ' . aspas('TROCA TITULAR') .
												' where Codigo_Associado = ' . aspas($codigoTitularOriginal));
			jn_query( 'update Ps1008 set Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ', Descricao_Observacao = ' . aspas('TROCA TITULAR') .
												' where Codigo_Associado = ' . aspas($codigoTitularOriginal));
			jn_query( 'update Ps1009 set Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ', Referencia_Importacao = ' . aspas('TROCA TITULAR') .
												' where Codigo_Associado = ' . aspas($codigoTitularOriginal));
			jn_query( 'update Ps1053 set Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ', Referencia_Importacao = ' . aspas('TROCA TITULAR') .
												' where Codigo_Associado = ' . aspas($codigoTitularOriginal));

			// transferencia de historico do antigo titular

			jn_query( 'Update Ps5300 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Referencia_Importacao is null');
			jn_query( 'Update Ps5400 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Referencia_Importacao is null');
			jn_query( 'Update Ps5500 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Referencia_Importacao is null');
			jn_query( 'Update Ps6500 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Descricao_Observacao is null');
			jn_query( 'update Ps1004 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Referencia_Importacao is null');
			jn_query( 'update Ps1005 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Referencia_Importacao is null');
			jn_query( 'update Ps1008 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Descricao_Observacao is null');
			jn_query( 'update Ps1009 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Referencia_Importacao is null');
			jn_query( 'update Ps1053 set Codigo_Associado = ' . aspas($codigoTitularOriginal) .
												' where Codigo_Associado = ' . aspas($codigoDependenteAConverter) . ' and Referencia_Importacao is null');


		$resultadoProcesso['MSG_RESULTADO'] = 'Ok processo realizado, conversão realizada';
		$resultadoProcesso['ID_RESULTADO']  = 'OK';

	   }

	}




/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 2211
/*  Função: Transferencia de histórico entre códigos
/*  Data de Implementação: 11/10/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='2211') 
	{

	   $codigoAntigo      = $_POST['CODIGO_ANTIGO_ASSOCIADO'];
	   $codigoNovo        = $_POST['CODIGO_NOVO_ASSOCIADO'];

	   if ($_POST['FLAG_TRANSFERIR_AUTORIZACOES']=='S')
	   {
			jn_query('Update Ps6500 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
	   }

	   if ($_POST['FLAG_TRANSFERIR_CONTAS_MEDICAS']=='S')
	   {
			jn_query('Update Ps5300 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps5400 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps5500 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps5900 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
	   }

	   if ($_POST['FLAG_TRANSFERIR_FATURAMENTO']=='S')
	   {
			jn_query('Update Ps1020 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps1021 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps1022 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps1023 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps1025 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update Ps1028 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
	   }

	   if ($_POST['FLAG_TRANSFERIR_AGENDAS']=='S')
	   {
			jn_query('Update Ps6010 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
	   }

	   if ($_POST['FLAG_TRANSFERIR_ODONTO']=='S')
	   {
			jn_query('Update ps2042 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update ps2100 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update ps2500 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update ps2600 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
	   }

	   if ($_POST['FLAG_TRANSFERIR_PRONTUARIO']=='S')
	   {
			jn_query('Update ps1098 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update ps1007 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
			jn_query('Update ps1008 Set Codigo_Associado = ' . aspas($codigoNovo) . ' Where Codigo_Associado = ' . aspas($codigoAntigo));
	   }

		$resultadoProcesso['MSG_RESULTADO'] = 'Ok processo realizado, conversão realizada';
		$resultadoProcesso['ID_RESULTADO']  = 'OK';

   }


/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 2212
/*  Função: Aplicação de valor nominal
/*  Data de Implementação: 11/10/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='2212') 
	{

	   $filtros = ' where (Coalesce(Valor_Nominal,0) <> 0) 
                    and DATA_ADMISSAO between ' . dataAngularToSql($_POST['DATA_ADMISSAO_INICIAL']) . ' and ' . dataAngularToSql($_POST['DATA_ADMISSAO_FINAL']) . 
                  ' and data_exclusao is null ';

	   if (($_POST['CODIGO_EMPRESA_INICIAL']!='') and ($_POST['CODIGO_EMPRESA_FINAL']!=''))
	   {
			$filtros .= ' and Codigo_Empresa between ' . aspas($_POST['CODIGO_EMPRESA_INICIAL']) . ' and ' . aspas($_POST['CODIGO_EMPRESA_FINAL']);
	   }

	   if (($_POST['CODIGO_PLANO_INICIAL']!='') and ($_POST['CODIGO_PLANO_FINAL']!=''))
	   {
			$filtros .= ' and Codigo_Plano between ' . aspas($_POST['CODIGO_PLANO_INICIAL']) . ' and ' . aspas($_POST['CODIGO_PLANO_FINAL']);
	   }

	   if ($_POST['CODIGO_GRUPO_PESSOAS']!='')
	   {
			$filtros .= ' and CODIGO_GRUPO_PESSOAS = ' . aspas($_POST['CODIGO_GRUPO_PESSOAS']);
	   }

	   if ($_POST['CODIGO_GRUPO_FATURAMENTO']!='')
	   {
			$filtros .= ' and CODIGO_GRUPO_FATURAMENTO = ' . aspas($_POST['CODIGO_GRUPO_FATURAMENTO']);
	   }

		$queryPrincipal = 'Select Codigo_Associado, Nome_Associado, data_admissao, Valor_Nominal Valor_Nominal_Original, 
                           (Valor_Nominal * ' . valSql($_POST['TAXA_CALCULO']) . ') Valor_Corrigido From Ps1000 ' . $filtros;

		$nomearquivoRelatorioProcesso        = geraRelatorioAutomaticoProcessamento($_POST['ID_PROCESSO'],$queryPrincipal);	                                       

	    if ($_POST['FLAG_APENAS_SIMULAR_NAO_APLICAR']=='S')
	    {
			$queryExecucao = 'Update Ps1000 Set Valor_Nominal = (Valor_Nominal * ' . valSql($_POST['TAXA_CALCULO']) . ') ' . $filtros;
			jn_query($queryExecucao);
	    }	

		$resultadoProcesso['MSG']            = 'OK';
		$resultadoProcesso['ARQUIVO_GERADO'] = $nomearquivoRelatorioProcesso;
		$resultadoProcesso['MSG_RESULTADO']  = 'Ok processo realizado, conversão realizada: ' . $nomearquivoRelatorioProcesso;
		$resultadoProcesso['ID_RESULTADO']   = 'OK';

   }



/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 2213
/*  Função: Mudança de categoria
/*  Data de Implementação: 11/10/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='2213') 
	{

	   jn_query(' Update Ps1000 Set Codigo_Plano = ' . aspas($_POST['CODIGO_PLANO_NOVO']) . ', ' .
                                '   Data_Migracao_Plano = ' . dataAngularToSql($_POST['DATA_MIGRACAO']) . 
                                '   Where (Codigo_Titular = ' . aspas($_POST['CODIGO_ASSOCIADO']) . ')');

	   $resTmp = jn_query('Select Codigo_Associado From Ps1000 Where (Codigo_Titular = ' . aspas($_POST['CODIGO_ASSOCIADO']) . ')');	
	   
	   while ($rowTmp = jn_fetch_object($resTmp))
	   {
           jn_query('Insert Into Ps1070(Codigo_Associado, Codigo_Plano_Anterior, Data_Mudanca_Categoria, Flag_Valida_Carenc_Cobert) ' . 
                                      ' Values(' . aspas($rowTmp->CODIGO_ASSOCIADO) . ',' .
                                                   aspas($rowTmp->CODIGO_PLANO) . ', ' .
                                                   dataAngularToSql($_POST['DATA_MIGRACAO']) . ', ' . 
                                                   aspas('N') . ')');
	   }	

		$resultadoProcesso['MSG']            = 'OK';
		$resultadoProcesso['MSG_RESULTADO']  = 'Ok processo realizado. ';
		$resultadoProcesso['ID_RESULTADO']   = 'OK';

   }

/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7004
/*  Função: Sobreposição de identificação do registro
/*  Data de Implementação: 02/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7004') 
	{

	   $numeroRegistro = $_POST['NUMERO_REGISTRO_ORIGINAL'];
	   $rowPs1020      = qryUmRegistro('Select * From Ps1020 Where (Numero_Registro = ' . aspas($numeroRegistro) . ')');

	   if ($rowPs1020->NUMERO_REGISTRO=='')
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'Fatura não localizada';
			$resultadoProcesso['ID_RESULTADO']  = 'ERRO_VALIDACAO';
	   }
	   else if (date_diff(dataHoje(),$rowPs1020->DATA_VENCIMENTO)->days <= 60)
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'Apenas faturas vencidas a mais de 60 dias podem ser sobrepostas';
			$resultadoProcesso['ID_RESULTADO']  = 'AVISO_VALIDACAO';
	   }
	   else if ($rowPs1020->DATA_PAGAMENTO != '')
	   {
			$resultadoProcesso['MSG_RESULTADO'] = 'Apenas faturas em aberto podem ser sobrepostas';
			$resultadoProcesso['ID_RESULTADO']  = 'AVISO_VALIDACAO';
	   }

	   if ($resultadoProcesso['MSG_RESULTADO']=='')
	   {

		   $novoRegistro = jn_gerasequencial('PS1020');

		   $retornoQry   = jn_query('insert into PS1020(NUMERO_REGISTRO, CODIGO_EMPRESA, CODIGO_ASSOCIADO, DATA_VENCIMENTO, VALOR_CONVENIO, VALOR_ADICIONAL, VALOR_PRORRATA, 
		   							 VALOR_OUTROS, VALOR_CORRECAO, VALOR_FATURA, DATA_EMISSAO, VALOR_PAGO, VALOR_MULTA, VALOR_DESCONTO, DATA_PAGAMENTO, NUMERO_NOTA_FISCAL,
		   							 NUMERO_PARCELA, TIPO_BAIXA, CODIGO_BANCO, DESCRICAO_OBSERVACAO, FLAG_MARCADO, CODIGO_SEQUENCIAL, DATA_CANCELAMENTO, 
		                             DATA_RESTITUICAO, DATA_VALIDACAO, PERCENTUAL_CORRECAO, TIPO_REGISTRO, DATA_NEGOCIACAO, MES_ANO_REFERENCIA, VALOR_FATURA_BRUTO, 
		                             VALOR_DESC_IR, VALOR_CSLL_PIS_COFINS, VALOR_DESC_ISS, VALOR_CSLL, VALOR_PIS, VALOR_COFINS, NUMERO_CARTA_INADIMPLENCIA, 
		                             TIPO_SITUACAO_FATURA, NUMERO_NOTA_EMISSAO, NUMERO_NOTA_CANCELAMENTO, IDENTIFICACAO_GERACAO, FLAG_REAJUSTE_APLICADO, 
		                             NUMERO_CONTA_COBRANCA, VALOR_DESC_ARQ_REMESSA, INFORMACOES_GERACAO, OBSERVACOES_COBRANCA, CODIGO_CARTEIRA, 
		                             CODIGO_COBRANCA_ATRASO, CODIGO_COBRANCA_NEGOCIADA, VALOR_FATURA_AGRUPADA_ORIG, DATA_PRORROGACAO) 
		                             Select ' . aspas($novoRegistro) . ', CODIGO_EMPRESA, CODIGO_ASSOCIADO, DATA_VENCIMENTO, VALOR_CONVENIO, VALOR_ADICIONAL, 
		                             VALOR_PRORRATA, VALOR_OUTROS, VALOR_CORRECAO, VALOR_FATURA, DATA_EMISSAO, VALOR_PAGO, 
		                             VALOR_MULTA, VALOR_DESCONTO, DATA_PAGAMENTO, NUMERO_NOTA_FISCAL,
		                             NUMERO_PARCELA, TIPO_BAIXA, CODIGO_BANCO, DESCRICAO_OBSERVACAO, FLAG_MARCADO, CODIGO_SEQUENCIAL, DATA_CANCELAMENTO,  
		                             DATA_RESTITUICAO, DATA_VALIDACAO, PERCENTUAL_CORRECAO, TIPO_REGISTRO, DATA_NEGOCIACAO, MES_ANO_REFERENCIA, VALOR_FATURA_BRUTO, 
		                             VALOR_DESC_IR, VALOR_CSLL_PIS_COFINS, VALOR_DESC_ISS, VALOR_CSLL, VALOR_PIS, VALOR_COFINS, NUMERO_CARTA_INADIMPLENCIA, 
		                             TIPO_SITUACAO_FATURA, NUMERO_NOTA_EMISSAO, NUMERO_NOTA_CANCELAMENTO, IDENTIFICACAO_GERACAO, FLAG_REAJUSTE_APLICADO, 
		                             NUMERO_CONTA_COBRANCA, VALOR_DESC_ARQ_REMESSA, INFORMACOES_GERACAO, OBSERVACOES_COBRANCA, CODIGO_CARTEIRA, 
		                             CODIGO_COBRANCA_ATRASO, CODIGO_COBRANCA_NEGOCIADA, VALOR_FATURA_AGRUPADA_ORIG, DATA_PRORROGACAO from ps1020 
		                             where Numero_Registro = ' . aspas($numeroRegistro));

		   $retornoQry   = jn_query('Update Ps1023 Set Numero_Registro_ps1020 = ' . aspas($novoRegistro) . ' Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistro));
		   $retornoQry   = jn_query('Update Ps1021 Set Numero_Registro_ps1020 = ' . aspas($novoRegistro) . ' Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistro));
		   $retornoQry   = jn_query('Update Ps1068 Set Numero_Registro_ps1020 = ' . aspas($novoRegistro) . ' Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistro));
		   $retornoQry   = jn_query('Update Ps1029 Set Numero_Registro_ps1020 = ' . aspas($novoRegistro) . ' Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistro));
		   $retornoQry   = jn_query('Update Ps1074 Set Numero_Registro_ps1020 = ' . aspas($novoRegistro) . ' Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistro));
		   //$retornoQry = jn_query(''Update Ps1020 Set Data_Cancelamento = ' . DataSql(DateToStr(Date)) . ' Where Numero_Registro = ' . aspas($numeroRegistro),'E');
		   $retornoQry   = jn_query('Delete from Ps1020 Where Numero_Registro = ' . aspas($numeroRegistro));

		   $resultadoProcesso['MSG_RESULTADO'] = 'Ok processo realizado, a fatura de número do registro: ' . $numeroRegistro . ' foi substituída pela fatura número: ' . $novoRegistro;
		   $resultadoProcesso['ID_RESULTADO']  = 'OK';
	   }

	}

/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7005
/*  Função: Estorno de cálculo de faturas
/*  Data de Implementação: 02/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7005') 
	{

	   $criterio       = ' And PS1020.Numero_Registro between ' . aspas($_POST['NUMERO_REGISTRO_INICIAL']) . ' and ' . aspas($_POST['NUMERO_REGISTRO_FINAL']);
	   $criterio      .= ' And PS1020.Data_Emissao between ' . dataAngularToSql($_POST['DATA_EMISSAO_INICIAL']) . ' and ' . dataAngularToSql($_POST['DATA_EMISSAO_FINAL']);

	   if ($_POST['TIPOS_FATURAMENTO']=='2') // APENAS PF
	       $criterio  .= ' And (PS1020.CODIGO_ASSOCIADO IS NOT NULL) ';

	   if ($_POST['TIPOS_FATURAMENTO']=='3') // APENAS PJ
	       $criterio  .= ' And (PS1020.CODIGO_ASSOCIADO IS NULL) ';

	   $retornoQry   = jn_query('DELETE FROM PS1029 WHERE NUMERO_REGISTRO_PS1020 IN (SELECT PS1020.NUMERO_REGISTRO FROM PS1020 WHERE 
	   	                                                 (PS1020.NUMERO_REGISTRO = PS1029.NUMERO_REGISTRO_PS1020) ' . $criterio . ')');

	   $retornoQry   = jn_query('DELETE FROM PS1021 WHERE NUMERO_REGISTRO_PS1020 IN (SELECT PS1020.NUMERO_REGISTRO FROM PS1020 WHERE 
	   	                                                 (PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020) ' . $criterio . ')');

	   $retornoQry   = jn_query('DELETE FROM PS1068 WHERE NUMERO_REGISTRO_PS1020 IN (SELECT PS1020.NUMERO_REGISTRO FROM PS1020 WHERE 
	   	                                                 (PS1020.NUMERO_REGISTRO = PS1068.NUMERO_REGISTRO_PS1020) ' . $criterio . ')');

	   $retornoQry   = jn_query('DELETE FROM PS1083 WHERE NUMERO_REGISTRO_PS1020 IN (SELECT PS1020.NUMERO_REGISTRO FROM PS1020 WHERE 
	   	                                                 (PS1020.NUMERO_REGISTRO = PS1083.NUMERO_REGISTRO_PS1020) ' . $criterio . ')');

	   $retornoQry   = jn_query('DELETE from ps1020 where NUMERO_REGISTRO >= 1 ' . $criterio);

	   $resultadoProcesso['MSG_RESULTADO'] = 'Ok, faturas estornadas (excluídas) com sucesso. ' . $retornoQry;
	   $resultadoProcesso['ID_RESULTADO']  = 'OK';

	}

/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7006
/*  Função: Programação automática de vl. adicional referente a reajuste retroativo
/*  Data de Implementação: 03/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7006') 
	{

		$refGeracao = getDataHoraAtual();

		$qryBeneficiarios = 'Select ' . aspas($_POST['CODIGO_EVENTO_GERAR']) . ' CODIGO_EVENTO, Ps1000.Codigo_Empresa CODIGO_EMPRESA, 
                            Ps1000.Codigo_Associado CODIGO_ASSOCIADO, Substring(Ps1000.Nome_Associado from 1 for 30) NOME_ASSOCIADO, ' . 
                            DataToSql(sqlToData(dataHoje())) . ' DATA_HOJE, 
                            (Coalesce(Ps1020.Valor_Convenio,0) + Coalesce(Ps1020.Valor_Correcao,0)) * ' . $_POST['PERCENUTAL_PROGRAMAR'] . ' VALOR_PROGRAMACAO, 
                            1 QUANTIDADE, ' . aspas($_POST['PROGRAMAR_PARA_MES'] . '/' . $_POST['PROGRAMAR_PARA_ANO']) . ' MES_ANO, ' . 
                            aspas('COBRANCA RETROATIVA ' . $_POST['PROGRAMAR_PARA_ANO']) . ' REF1, ' .
                            aspas('INCLUSAO AUTOMATICA ' . $refGeracao) . ' REF2,  ' . 
                            aspas('MES REF.:' . $_POST['MES_BASE_CALCULO'] . '/' . $_POST['ANO_BASE_CALCULO']) . ' REF3 ' .
                           ' from ps1000 
                            inner join ps1020 on (ps1000.codigo_associado = ps1020.codigo_associado) 
                            left Outer Join Ps1002 On (ps1000.Codigo_Associado = Ps1002.Codigo_Associado) 
                            where 
                             (
                               (extract(month from Coalesce(Ps1002.Data_Admin_Consid_Reaj,Ps1000.data_admissao)) = ' . numSql($_POST['PRIMEIRO_MES']) . ') ' . 
                                iif($_POST['SEGUNDO_MES'] == 'IGNORAR','',' Or (extract(month from Coalesce(Ps1002.Data_Admin_Consid_Reaj,ps1000.data_admissao)) = ' . numSql($_POST['SEGUNDO_MES']) . ') ') . 
                                iif($_POST['TERCEIRO_MES'] == 'IGNORAR','',' Or (extract(month from Coalesce(Ps1002.Data_Admin_Consid_Reaj,ps1000.data_admissao)) = ' . numSql($_POST['TERCEIRO_MES']) . ') ') . 
                            ') 
                            and (extract(year from Coalesce(Ps1002.Data_Admin_Consid_Reaj,ps1000.data_admissao)) < ' . numSql($_POST['PROGRAMAR_PARA_ANO']) . ') 
                            and (Ps1020.Numero_Parcela >= 1) 
                            and (extract(Month from ps1020.data_Vencimento) = ' . numSql($_POST['MES_BASE_CALCULO']) . ') 
                            and (extract(year from ps1020.data_Vencimento) = ' . numSql($_POST['ANO_BASE_CALCULO']) . ') 
                            and (Codigo_Plano Between ' . numSql($_POST['CODIGO_PLANO_INICIAL']) . ' and ' . numSql($_POST['CODIGO_PLANO_FINAL']) . ') ' .

                            iif((($_POST['GRUPO_PESSOAS_INICIAL']!='') and ($_POST['GRUPO_PESSOAS_FINAL']!='')),' and (Ps1000.Codigo_Grupo_Pessoas Between ' . numSql($_POST['GRUPO_PESSOAS_INICIAL']) . ' and ' . numSql($_POST['GRUPO_PESSOAS_FINAL']) . ') ','') .

                            $queryGrupoContrato .  // O grupo de contrato é uma customização da Vidamax, quando colocar o sistema na Vidamax criar os campos para filtro por grupo de contrato.

                            ' and (ps1000.data_exclusao is null) and (PS1020.DATA_CANCELAMENTO IS NULL) order by PS1000.CODIGO_ASSOCIADO';

	  $qryBeneficiarios        = trataQuerySqlServer($qryBeneficiarios);
		$resBeneficiarios        = jn_query($qryBeneficiarios);
		$qtRegistos              = 0;

		while ($rowBeneficiarios = jn_fetch_object($resBeneficiarios))
		{
				$sqlEdicao   = '';
				$sqlEdicao 	.= linhaJsonEdicao('NUMERO_REGISTRO',jn_gerasequencial('PS1023'));
				$sqlEdicao 	.= linhaJsonEdicao('CODIGO_EVENTO', $rowBeneficiarios->CODIGO_EVENTO);
				$sqlEdicao 	.= linhaJsonEdicao('CODIGO_EMPRESA', $rowBeneficiarios->CODIGO_EMPRESA);
				$sqlEdicao 	.= linhaJsonEdicao('CODIGO_ASSOCIADO',$rowBeneficiarios->CODIGO_ASSOCIADO);
				$sqlEdicao 	.= linhaJsonEdicao('NOME_PESSOA', copyDelphi($rowBeneficiarios->CODIGO_ASSOCIADO,1,30));
				$sqlEdicao 	.= linhaJsonEdicao('DATA_EVENTO',$rowBeneficiarios->DATA_HOJE,'D');
				$sqlEdicao 	.= linhaJsonEdicao('VALOR_EVENTO', $rowBeneficiarios->VALOR_PROGRAMACAO,'N');
				$sqlEdicao 	.= linhaJsonEdicao('QUANTIDADE_EVENTOS',1);
				$sqlEdicao 	.= linhaJsonEdicao('MES_ANO_VENCIMENTO',$rowBeneficiarios->MES_ANO);
				$sqlEdicao 	.= linhaJsonEdicao('DESCRICAO_HISTORICO',$rowBeneficiarios->REF3);
				$sqlEdicao 	.= linhaJsonEdicao('DESCRICAO_OBSERVACAO',$rowBeneficiarios->REF1);

				gravaEdicao('PS1023', $sqlEdicao, 'I');
				$qtRegistos++;
     }

	   $resultadoProcesso['MSG_RESULTADO'] = 'Processo concluído, foram incluídas: ' . $qtRegistos . ' programações de eventos';
	   $resultadoProcesso['ID_RESULTADO']  = 'OK';

  }


/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7007
/*  Função: Baixa manual de faturas
/*  Data de Implementação: 04/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7007') 
	{

		if ($_POST['CHECK_PERMITE_ALTERAR_BAIXA']!='S')
		{
				$resultadoProcesso['MSG_RESULTADO'] = '';
				$row = qryUmRegistro('Select DATA_PAGAMENTO from ps1020 where numero_registro = ' . aspas($_POST['CAMPO_LOCALIZAR']));

				if ($row->DATA_PAGAMENTO!='')
				{
			  	  $resultadoProcesso['MSG_RESULTADO'] = 'A fatura já foi baixada. Caso queira atualizar a baixa marque a opção "Permitir alteração/complemento de baixas" ';
			    	$resultadoProcesso['ID_RESULTADO']  = 'OK';
				}
		}


		if ($resultadoProcesso['MSG_RESULTADO']=='')
		{
				$sqlEdicao   = '';
				$sqlEdicao 	.= linhaJsonEdicao('DATA_BAIXA',dataHoje(),'D');
				$sqlEdicao 	.= linhaJsonEdicao('DATA_PAGAMENTO', $_POST['DATA_PAGAMENTO'],'D');
				$sqlEdicao 	.= linhaJsonEdicao('VALOR_PAGO', $_POST['VALOR_PAGO'],'N');

				if ($_POST['OBSERVACAO_BAIXA']!='')
				{
 					  $sqlEdicao 	.= linhaJsonEdicao('DESCRICAO_OBSERVACAO', $_POST['OBSERVACAO_BAIXA']);
				}

				gravaEdicao('PS1020', $sqlEdicao, 'A', ' Numero_Registro = ' . $_POST['CAMPO_LOCALIZAR']);

			  $resultadoProcesso['MSG_RESULTADO'] = 'Processo concluído, o registro: ' . $_POST['CAMPO_LOCALIZAR'] . ' foi baixado.';
			  $resultadoProcesso['ID_RESULTADO']  = 'OK';

			  if (($_POST['CHECK_LANCAR_MOV_BANCARIO']=='S') and ($_POST['NUMERO_CONTA_CORRENTE']!=''))
			  {
						$sqlEdicao   = '';
						$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro',jn_gerasequencial('PS7400'));
						$sqlEdicao 	.= linhaJsonEdicao('Numero_Conta_Corrente',$_POST['NUMERO_CONTA_CORRENTE']);
						$sqlEdicao 	.= linhaJsonEdicao('Descricao_Referencia',copyDelphi('BX.MAN-REG:' . $_POST['CAMPO_LOCALIZAR'],1,50));
						$sqlEdicao 	.= linhaJsonEdicao('Data_Movimento',dataHoje(),'D');
						$sqlEdicao 	.= linhaJsonEdicao('Nome_Favorecido',retornaValorConfiguracao('NOME_EMPRESA'));
						$sqlEdicao 	.= linhaJsonEdicao('Flag_Saldo','N');
						$sqlEdicao 	.= linhaJsonEdicao('Flag_Conciliado','N');
						$sqlEdicao 	.= linhaJsonEdicao('Valor_Adicional',0,'N');
						$sqlEdicao 	.= linhaJsonEdicao('Numero_Documento_Bancario','CRED.COBRANCA');
						$sqlEdicao 	.= linhaJsonEdicao('Tipo_Documento','CR');
						$sqlEdicao 	.= linhaJsonEdicao('Valor_Movimento', $_POST['VALOR_PAGO'],'N');
							
						gravaEdicao('PS7400', $sqlEdicao, 'I');

						$row         = qryUmRegistro('Select Numero_Registro From Ps7400 Where Descricao_Referencia = ' . aspas('BX.MAN-REG:' . $_POST['CAMPO_LOCALIZAR']));

						$sqlEdicao   = '';
						$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps7400',$row->NUMERO_REGISTRO);

						gravaEdicao('PS1020', $sqlEdicao, 'A', ' Numero_Registro = ' . $_POST['CAMPO_LOCALIZAR']);
			  }

	  	  $resultadoProcesso['MSG_RESULTADO'] .= '<br>O registro bancário também foi lançado.';

		}

  }


/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7008
/*  Função: Baixa automática de arquivos retorno
/*  Data de Implementação: 04/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7008') 
	{

		$nomearquivoLogProcesso = escreveArquivoLogProcesso('C',$_POST['ID_PROCESSO']);
		$linhaArquivo      		  	  = '';

		$linhaLogProcesso  = '';
		$linhaLogProcesso .= adicionaCampoLinhaLog('Registro',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Codigo',20);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Nome do pagador',40);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vencimento',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl fatura',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Pago',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Juros',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Multa',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Tp Retorno',12);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Tp Registro',12);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Observações',70);

		escreveArquivoLogProcesso('L',$linhaLogProcesso);

		//

		$rowDadosLayout     		  = qryUmRegistro('Select * from PS7301 where PADRAO_ARQUIVO_BANCO = ' . aspas($_POST['PADRAO_ARQUIVO']));

		/* 	$rowDadosLayout->VALORES_RETORNO_BAIXAR
		    $rowDadosLayout->VALORES_REGISTRO_IGNORAR
		    $rowDadosLayout->TIPO_ARQUIVO
		    $rowDadosLayout->PADRAO_ARQUIVO_BANCO
		*/

		$nomeCaminhoArquivo = 'C:\Temp\retornos\CB260504.RET';

		try
		{
			$arquivo        = fopen($nomeCaminhoArquivo, "r"); 
		}
		catch (Exception $e) 
		{
   		    $resultadoProcesso['MSG_RESULTADO'] = 'Erro ao tentar abrir o arquivo, msg: ' . $e->getMessage();
		    $resultadoProcesso['ID_RESULTADO']  = 'ERRO';
		    pr($resultadoProcesso['MSG_RESULTADO']);
		    pr('VER COM DIEGO PORQUE NÃO CAPTURA A EXCESSÃO');
		}


		if (!$arquivo) 
		{
		    $resultadoProcesso['MSG_RESULTADO'] = 'Falha ao ler o arquivo, o arquivo não pôde ser lido.';
		    $resultadoProcesso['ID_RESULTADO']  = 'ERRO';
		}

		if ($resultadoProcesso['ID_RESULTADO']=='OK')
		{
			while (($linha = fgets($arquivo)) !== false) 
			{
			    //pr($linha);

			    $seuNumero           = leInformacaoCampoLayout($linha, $rowDadosLayout, 'SEU_NUMERO');
			    $nossoNumero         = leInformacaoCampoLayout($linha, $rowDadosLayout, 'NOSSO_NUMERO');
			    $dataVencimento      = leInformacaoCampoLayout($linha, $rowDadosLayout, 'VENCIMENTO');
			    $valorFatura         = leInformacaoCampoLayout($linha, $rowDadosLayout, 'VALOR_FATURA');
			    $valorPago           = leInformacaoCampoLayout($linha, $rowDadosLayout, 'VALOR_PAGO');
			    $valorMora           = leInformacaoCampoLayout($linha, $rowDadosLayout, 'VALOR_MORA');
			    $valorMulta          = leInformacaoCampoLayout($linha, $rowDadosLayout, 'VALOR_MULTA');
			    $dataOcorrencia      = leInformacaoCampoLayout($linha, $rowDadosLayout, 'DATA_OCORRENCIA');
			    $tipoRetorno         = leInformacaoCampoLayout($linha, $rowDadosLayout, 'TIPO_RETORNO');
			    $tipoRegistro        = leInformacaoCampoLayout($linha, $rowDadosLayout, 'TIPO_REGISTRO');
			    $valorTaxa           = leInformacaoCampoLayout($linha, $rowDadosLayout, 'VALOR_TAXA');
			    $valorDesconto       = leInformacaoCampoLayout($linha, $rowDadosLayout, 'VALOR_DESCONTO');

			    if (($rowDadosLayout->TIPO_REGISTRO_INICIAL != '') and ($rowDadosLayout->TIPO_REGISTRO_FINAL != ''))
			    {
				    if (($tipoRegistro < $rowDadosLayout->TIPO_REGISTRO_INICIAL) or 
				        ($tipoRegistro > $rowDadosLayout->TIPO_REGISTRO_FINAL))
				    {
				    	continue;
				    }
				}

			    $observacao            = 'Ok, baixa efetuada';

			    // Esta função abaixo serve para tratar quando precisar fazer pesquisas por outros campos, ou tratar dados... para trazer o numero do registro
			    // Por padrão ela vai retornar direto o seu numero. Mas caso seja necessário, ela vai pesquisar por outros campos na Ps1020 para retornar
			    // o numero do registro a ser baixado.

			    $numeroRegistroPs1020  = retornaNumeroRegistroPs1020Baixa($seuNumero,$nossoNumero);

		    	$rowPs1020 		       = qryUmRegistro('Select PS1020.CODIGO_ASSOCIADO, PS1020.CODIGO_EMPRESA, PS1020.NUMERO_REGISTRO, PS1020.DATA_PAGAMENTO, 
		    	                                      case 
		    	                                      	When (Ps1000.flag_planoFamiliar = ' . aspas('S') . ') then PS1000.NOME_ASSOCIADO
		    	                                      	else PS1010.NOME_EMPRESA
		    	                                      end NOME_PAGADOR, 
		    	                                      CASE 
		    	                                      	When (Ps1000.flag_planoFamiliar = ' . aspas('S') . ') then PS1000.CODIGO_ASSOCIADO
		    	                                      	else CAST(PS1010.CODIGO_EMPRESA AS VARCHAR(15))
		    	                                      end CODIGO_SISTEMA 
		    		                                  from ps1020 
		    		                                  left outer join Ps1010 on (ps1020.codigo_empresa = ps1010.codigo_empresa)
		    		                                  left outer join ps1000 on (ps1020.codigo_associado = ps1000.codigo_associado)
		    		                                  where PS1020.Numero_registro = ' . numSql($numeroRegistroPs1020));

		    	$efetuarBaixaPagamento = 'SIM';

		    	if ($tipoRetorno=='02')
		    	{
					$sqlEdicao             = '';
					$sqlEdicao 	          .= linhaJsonEdicao('Data_Pagamento',$dataOcorrencia,'D');
					$sqlEdicao 	          .= linhaJsonEdicao('Valor_Pago',$valorPago);
					$sqlEdicao 	          .= linhaJsonEdicao('Padrao_Arquivo_Retorno',$_POST['PADRAO_ARQUIVO']);
					$sqlEdicao 	          .= linhaJsonEdicao('Codigo_Retorno_Remessa',$tipoRetorno);
					$sqlEdicao 	          .= linhaJsonEdicao('Codigo_Identificacao_Fat',$NossoNumero);

					gravaEdicao('PS1020', $sqlEdicao, 'A', ' Numero_registro = ' . numSql($numeroRegistroPs1020));

					$rowMotivo             = qryUmRegistro('select Descricao_Motivo from PS1027 where Codigo_Motivo = ' . numSql($tipoRetorno));
					$observacao  		   = 'Retorno do tipo: ' . $tipoRetorno . ', registro confirmado.' . $rowMotivo->DESCRICAO_MOTIVO;

					$efetuarBaixaPagamento = 'NAO';
		    	}
		    	else if (strpos($rowDadosLayout->VALORES_RETORNO_BAIXAR,$tipoRetorno)===false)
		    	{
		    		$observacao            = 'Retorno do tipo: ' . $tipoRetorno . ' não refere-se a baixa de pagto.' ;
					$efetuarBaixaPagamento = 'NAO';
		    	}
		    	else if ($rowPs1020->NUMERO_REGISTRO=='')
		    	{
		    		$observacao            = 'Fatura não localizada';
					$efetuarBaixaPagamento = 'NAO';
		    	}
		    	else if ($_POST['CHECK_SOBREPOR_BAIXA'] != 'S')
			    {
			    	if ($rowPs1020->DATA_PAGAMENTO!='')
			    	{
			    		$observacao  		   = 'Fatura já baixada';
						$efetuarBaixaPagamento = 'NAO';
			    	}
			    }

			    //

		    	if ($efetuarBaixaPagamento=='SIM')
		    	{
				    if (retornaValorConfiguracao('TRAVAR_FATURAS_FECHADAS')== 'SIM') 
				    {
						if (!podeModificarFaturamento($mesAnoReferencia,'BAIXA_PAGTO'))
						{
							$observacao  		   = 'A fatura não será baixada, pois o mês de referência já está fechado';
							$efetuarBaixaPagamento = 'NAO';
						}
				    }
				}

		    	if ($efetuarBaixaPagamento=='SIM')
		    	{

					    $dataOcorrenciaTratada = getMontaData('20' . copyDelphi($dataOcorrencia,5,2) . '/' . copyDelphi($dataOcorrencia,3,2) . '/' . copyDelphi($dataOcorrencia,1,2));

						$sqlEdicao             = '';
						$sqlEdicao 	          .= linhaJsonEdicao('Data_Pagamento',$dataOcorrencia,'D');
						$sqlEdicao 	          .= linhaJsonEdicao('Valor_Pago',$valorPago);
						$sqlEdicao 	          .= linhaJsonEdicao('Tipo_Baixa','A');
						$sqlEdicao 	          .= linhaJsonEdicao('Codigo_Identificacao_Fat',$nossoNumero);
						$sqlEdicao 	          .= linhaJsonEdicao('Data_Baixa',dataHoje(),'D');
						$sqlEdicao 	          .= linhaJsonEdicao('Valor_Multa',$valorMulta,'N');
						$sqlEdicao 	          .= linhaJsonEdicao('Codigo_Operador_Baixa',$_SESSION['codigoIdentificacao']);

		                if (($rowDadosLayout->CODIGO_BANCO) and (trim($rowDadosLayout->CODIGO_BANCO) != ''))
							$sqlEdicao 	      .= linhaJsonEdicao('Codigo_Banco_Baixa = ' + Aspas(_QryPs7301.FieldByName('Codigo_Banco').AsString));

		                if (trim($_POST['CMB_NUMEROCONTACORRENTE']) != '') 
							$sqlEdicao 	      .= linhaJsonEdicao('Numero_Conta_Baixa',$_POST['CMB_NUMEROCONTACORRENTE']);

		                if  ($_POST['CHECK_SOMARJUROS']=='S')
							$sqlEdicao 	      .= linhaJsonEdicao('Valor_Outros',$valorTaxa,'N');

						if  ($_POST['CHECK_REGISTRARDESCONTO']=='S')
							$sqlEdicao 	      .= linhaJsonEdicao('Valor_Desconto',$valorDesconto,'N');

						if  ($_POST['CHECK_REGISTRARMULTA']=='S')
		                {
							$sqlEdicao 	      .= linhaJsonEdicao('Valor_Mora_Remessa',$valorMora,'N');
							$sqlEdicao 	      .= linhaJsonEdicao('Valor_Multa_Remessa',$valorMulta,'N');
		                }

						gravaEdicao('PS1020', $sqlEdicao, 'A', ' Numero_registro = ' . numSql($numeroRegistroPs1020));

		                if  ($_POST['CHECK_REGISTRA_SIT_ATENDIMENTO']=='S')
		                {
							ajustaSituacaoAtendimentoPosBaixaFaturas($numeroRegistroPs1020);
		                }

				}

				//

				$linhaLogProcesso  = '';
				$linhaLogProcesso .= adicionaCampoLinhaLog($numeroRegistroPs1020,14);
				$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->CODIGO_SISTEMA,20);
				$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->NOME_PAGADOR,40);
				$linhaLogProcesso .= adicionaCampoLinhaLog($dataVencimento,14);
				$linhaLogProcesso .= adicionaCampoLinhaLog($valorFatura,14);
				$linhaLogProcesso .= adicionaCampoLinhaLog($valorPago,14);
				$linhaLogProcesso .= adicionaCampoLinhaLog($valorMora,14);
				$linhaLogProcesso .= adicionaCampoLinhaLog($valorMulta,14);
				$linhaLogProcesso .= adicionaCampoLinhaLog($tipoRetorno,12);
				$linhaLogProcesso .= adicionaCampoLinhaLog($tipoRegistro,12);
				$linhaLogProcesso .= adicionaCampoLinhaLog($observacao,70);

				escreveArquivoLogProcesso('L',$linhaLogProcesso);

			}

			if (!feof($arquivo)) 
			{
			    $resultadoProcesso['MSG_RESULTADO'] = 'Falha durante a leitura (função fgets()).';
			    $resultadoProcesso['ID_RESULTADO']  = 'ERRO';
			}
			else
			{
			    $resultadoProcesso['MSG_RESULTADO'] = 'Ok, arquivo processado com sucesso!';
			}
		}

		fclose($arquivo);
		
    }


/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7012
/*  Função: Programação de cobrança de inclusões conforme parâmetros das empresas
/*  Data de Implementação: 25/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:												Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7012') 
	{

	    $qryPrincipal = 'Select Ps1000.Codigo_Empresa, Ps1000.Codigo_Associado, Ps1000.Nome_Associado, Ps1082.*, 
	                     Ps1045.Tipo_Relacao_Dependencia, Tipo_Associado, 
	                     Ps1000.Codigo_Empresa, Ps1000.Flag_PlanoFamiliar, Ps1000.Nome_Associado, Ps1000.Codigo_Associado
	                     From Ps1000 
	                     Inner Join Ps1082 On (Ps1000.Codigo_Empresa = Ps1082.Codigo_Empresa) 
	                     Left Outer Join Ps1045 On (Ps1000.Codigo_Parentesco = Ps1045.Codigo_Parentesco) 
	                     Where ';

	    if (($_POST['CODIGO_EMPRESA_INICIAL']!='') and ($_POST['CODIGO_EMPRESA_FINAL']!=''))
	       $qryPrincipal .= ' (Ps1000.Codigo_Empresa Between ' . aspas($_POST['CODIGO_EMPRESA_INICIAL']) . ' And ' . aspas($_POST['CODIGO_EMPRESA_FINAL']) . ') And ';

	    $qryPrincipal .= ' (Ps1000.Data_Admissao Between ' . dataAngularToSql($_POST['DATA_ADMISSAO_INICIAL']) . ' And ' . 
	                                                         dataAngularToSql($_POST['DATA_ADMISSAO_FINAL']) . ') And 
	                       (Ps1082.Tipo_Geracao Like ' . aspas('1%') . ') 
	                       Order By Ps1000.Codigo_Empresa, Ps1000.Nome_Associado';

			$resPrincipal    = jn_query($qryPrincipal);
			$qtRegistos      = 0;

			$tipoGeracao     = '1';

	    if ($tipoGeracao == '1')
	         $referencia = 'AUTOMATICO NOVO CADASTRO';
	    else if ($tipoGeracao == '2')
	         $referencia = 'AUTOMATICO 1 CARTEIRINHA';
	    else if ($tipoGeracao == '3')
	         $referencia = 'AUTOMATICO 2 CARTEIRINHA';

			while ($rowPrincipal = jn_fetch_object($resPrincipal))
			{

		      if ($rowPrincipal->TIPO_ASSOCIADO == 'T')
	  	       $tipoBeneficiario = 'T';
	    	  else if ($rowPrincipal->TIPO_RELACAO_DEPENDENCIA != '') 
	      	   $tipoBeneficiario = $rowPrincipal->TIPO_RELACAO_DEPENDENCIA;

	        $rowPs1082 = qryUmRegistro('Select Ps1082.* From Ps1082 
	                                    Where ((Ps1082.Tipo_Beneficiario = ' . aspas($tipoBeneficiario) . ') Or (Ps1082.Tipo_Beneficiario = ' . aspas('G') . ')) And 
	                                           (Ps1082.Tipo_Geracao Like ' . aspas($tipoGeracao) . ')');

	        if ($rowPs1082->NUMERO_REGISTRO!='')
	        {
	        		if ($rowPs1082->TIPO_COBRANCA == 'P') 
	        		{
		              $valorEvento      = $rowPs1082->VALOR_EVENTO;
	                $valorCobranca    = calculaEstimativaFaturaSemRegistrar($rowPrincipal->CODIGO_ASSOCIADO, 'I', 'F');
	                $valorCobranca 	  = $valorCobranca * ($valorEvento / 100);
	        		}
	        		else
	        		{
	                $valorCobranca 	  = $rowPs1082->VALOR_EVENTO;
	            }
	        }

					$sqlEdicao   = '';
					$sqlEdicao 	.= linhaJsonEdicao('NUMERO_REGISTRO',jn_gerasequencial('PS1023'));
					$sqlEdicao 	.= linhaJsonEdicao('CODIGO_EVENTO', $rowPrincipal->CODIGO_EVENTO);
					$sqlEdicao 	.= linhaJsonEdicao('CODIGO_EMPRESA', $rowPrincipal->CODIGO_EMPRESA);
					$sqlEdicao 	.= linhaJsonEdicao('CODIGO_ASSOCIADO',$rowPrincipal->CODIGO_ASSOCIADO);
					$sqlEdicao 	.= linhaJsonEdicao('NOME_PESSOA', copyDelphi($rowPrincipal->NOME_ASSOCIADO,1,30));
					$sqlEdicao 	.= linhaJsonEdicao('DATA_EVENTO',dataHoje(),'D');
					$sqlEdicao 	.= linhaJsonEdicao('VALOR_EVENTO', $valorCobranca,'N');
					$sqlEdicao 	.= linhaJsonEdicao('QUANTIDADE_EVENTOS',1);
					$sqlEdicao 	.= linhaJsonEdicao('MES_ANO_VENCIMENTO',RetornaProximoMesAnoDisponivelFaturamento('',$rowPrincipal->CODIGO_ASSOCIADO));
					$sqlEdicao 	.= linhaJsonEdicao('DESCRICAO_HISTORICO','PROG_AUTOMATICA_INCLUSOES');
					$sqlEdicao 	.= linhaJsonEdicao('DESCRICAO_OBSERVACAO','PROG_AUTOMATICA_INCLUSOES');

					$criterioWhere = '      CODIGO_ASSOCIADO = ' . aspas($rowPrincipal->CODIGO_ASSOCIADO) . 
					                 ' and CODIGO_EVENTO = ' . aspas($rowPrincipal->CODIGO_EVENTO) . 
					                 ' and DESCRICAO_HISTORICO = ' . aspas('PROG_AUTOMATICA_INCLUSOES');

					gravaEdicao('PS1023', $sqlEdicao, 'V', $criterioWhere);

			}

  	  $resultadoProcesso['MSG_RESULTADO'] .= '<br>As programações foram realizadas.';


  }


/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7013
/*  Função: Alteração em lote de valores de eventos adicionais vinculados ao cadastro de beneficiários
/*  Data de Implementação: 28/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:												Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	else if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7013') 
	{

	    $qryPrincipal    = 'Select * From Ps1024 Where Codigo_Evento between ' . numSql($_POST['CODIGO_EVENTO_INICIAL']) . ' and ' . numSql($_POST['CODIGO_EVENTO_FINAL']);
			$resPrincipal    = jn_query($qryPrincipal);
			$qtRegistos      = 0;

			while ($rowPrincipal = jn_fetch_object($resPrincipal))
			{

					$sqlEdicao     = '';
					$sqlEdicao 	  .= linhaJsonEdicao('Valor_Fator', $rowPrincipal->VALOR_SUGERIDO,'N');

					$criterioWhere = ' Codigo_Empresa between ' . numSql($_POST['CODIGO_EMPRESA_INICIAL']) . ' and ' . numSql($_POST['CODIGO_EMPRESA_FINAL']);

					gravaEdicao('PS1003', $sqlEdicao, 'A', $criterioWhere);

          if (($_POST['CODIGO_EMPRESA_INICIAL'] <= 400) and ($_POST['CODIGO_EMPRESA_FINAL'] >= 400))
          {
							$sqlEdicao     = '';
							$sqlEdicao 	  .= linhaJsonEdicao('Valor_Fator', $rowPrincipal->VALOR_SUGERIDO,'N');

							$criterioWhere = ' Codigo_Evento = ' . numSql($rowPrincipal->CODIGO_EVENTO) . 
                               ' and Ps1003.Codigo_Associado in (Select Ps1000.Codigo_Associado From Ps1000 ' . 
                               ' Where (Ps1000.Codigo_Associado = Ps1003.Codigo_Associado) And ' . 
                               '       (Ps1000.Codigo_Empresa between ' . numSql($_POST['CODIGO_EMPRESA_INICIAL']) . ' and ' . numSql($_POST['CODIGO_EMPRESA_FINAL']) . '))';

							gravaEdicao('PS1003', $sqlEdicao, 'A', $criterioWhere);
					}
			}

  	  $resultadoProcesso['MSG_RESULTADO'] .= '<br>Atualização de valores realizada com sucesso!';

  }



/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSOS FINAIS PARA REGISTRAR CONCLUSÃO, MANTER COMO ULTIMAS LINHAS DO ARQUIVO
/*  Função: Mostrar mensagem de conclusão e registrar conclusão do processo
/*  Data de Implementação: 01/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

	$resultado = registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',$resultadoProcesso['MSG_RESULTADO'],$nomeArquivoProcesso, $nomearquivoLogProcesso);




function copyEstruturaFamilia($camposCodigoAssociado)
{

	$camposCodigoAssociado = retornaValorConfiguracao('CAMPOS_CODIGO_ASSOCIADO');

    if ($camposCodigoAssociado ==  'CODIGO_SEQUENCIAL,6,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,6);
    else if ($camposCodigoAssociado ==  'LITERAL,5561,ANO_CADASTRO,2,CODIGO_SEQUENCIAL,6,NUMERO_DEPENDENTE,2,DIGITO,1')
       return  copyDelphi($codigoBeneficiario,1,12);
    else if ($camposCodigoAssociado ==  'LITERAL,01,CODIGO_EMPRESA,3,CODIGO_SEQUENCIAL,7,NUMERO_DEPENDENTE,2,DIGITO,1') 
       return  copyDelphi($codigoBeneficiario,1,12);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,5,CODIGO_PLANO,2,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2,DIGITO,1') 
       return  copyDelphi($codigoBeneficiario,1,12);
    else if ($camposCodigoAssociado ==  'CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,5);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,4,CODIGO_SEQUENCIAL,7,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,11);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,3,CODIGO_PLANO,3,CODIGO_SEQUENCIAL,6,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,12);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,5,CODIGO_SEQUENCIAL,4,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,9);
    else if ($camposCodigoAssociado ==  'CODIGO_SEQUENCIAL,7,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,7);
    else if ($camposCodigoAssociado ==  'CODIGO_PLANO,2,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,7);
    else if ($camposCodigoAssociado ==  'LITERAL,1,CODIGO_EMPRESA,4,CODIGO_SEQUENCIAL,7,NUMERO_DEPENDENTE,2,DIGITO,1') 
       return  copyDelphi($codigoBeneficiario,1,12);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,3,CODIGO_PLANO,2,CODIGO_SEQUENCIAL,7,NUMERO_DEPENDENTE,2,DIGITO,1') 
       return  copyDelphi($codigoBeneficiario,1,12);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,4,CODIGO_PLANO,2,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2,DIGITO,1') 
       return  copyDelphi($codigoBeneficiario,1,11);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,4,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,9);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,3,CODIGO_SEQUENCIAL,4,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,7);
    else if ($camposCodigoAssociado ==  'CODIGO_EMPRESA,3,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2') 
       return  copyDelphi($codigoBeneficiario,1,8);
    else
       return  copyDelphi($codigoBeneficiario,1,12);

}




?>

