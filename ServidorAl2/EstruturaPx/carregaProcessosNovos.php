<?php

header("Content-Type: text/html; charset=UTF-8",true);

	//$_GET['Teste'] = 'OK';
	//  prDebug($_POST['ID_PROCESSO']);

	set_time_limit(0);

	global $ordemCriacao;


/* ---------------------------------------------------------------------------------------------------- 

	Padrões para criação de menus e processos:

	* Cadastros
		Menus de 2000 à 2999

	* Faturamento contas a receber
		Menus de 3000 à 3499
		Processos de 3000 à 3499

	* SAC 
		Menus de 3500 à 3999
		Processos de 3500 à 3999

	* Atendimentos e guias
		Menus de 4000 à 4499
		Processos de 4000 à 4499

	* Comercial
		Menus de 4500 à 4999
		Processos de 4500 à 4999

	* Financeiro
		Menus de 5000 à 5499
		Processos de 5000 à 5499

	* Sinistros e coberturas
		Menus de 5500 à 5899
		Processos de 5500 à 5899

	* ANS
		Menus de 5900 à 5999
		Processos de 5500 à 5999

	* Utilitários e configurações
		Menus de 6000 à 6599
		Processos de 6000 à 6599
		
	* Menus específicos do cliente (Customizações)
		Menus de 9000 à 9999
		Processos de 9000 à 9999

/* ---------------------------------------------------------------------------------------------------- */



function validaCarregamentoNovosProcessos()
{

	/* Relatório de exemplo, 31/08/2023 */ 
	criaProcesso7016();

	criaProcesso3018();

	criaProcesso2204();

	criaProcesso2205();

	criaProcesso2206();
	
	criaProcesso2207();

	criaProcesso2208();

	//Troca de titularidade
	criaProcesso2210();

	//Transferencia de histórico
	criaProcesso2211();

	//Aplicação de correção de valor nominal
	criaProcesso2212();

	//Mudança de categoria
	criaProcesso2213();

	criaProcesso2301();

	criaProcesso2302();

	criaProcesso2303();

	criaProcesso3102();

	criaProcesso3104();

	criaProcesso3105();

	criaProcesso3107();

	criaProcesso3108();

	criaProcesso3109();

	criaProcesso3110();

	criaProcesso4601();

	criaProcesso4602();

	criaProcesso4603();

	criaProcesso5201();

}



/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de exemplo para ser utilizado
/* Data: 31/08/2023 					
/* Responsável: Silvio
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso7016()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2011';
		$numeroRegistroPai		= '2010';
		$numeroRegistroProcesso = '7016';
		$labelProcesso          = 'Relatório de exemplo XXXX';
		$descricaoProcesso      = 'Explicação do relatório XXXX';
		$iconeProcesso          = 'domain';
		$destinoProcesso        = 'Relatorios/relatorioBeneficiariosTEMPLATE.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_ASSOCIADO_INICIAL','Código beneficiário inicial: ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroManual($numeroRegistroProcesso,'1-Opções principais','BENEFICIARIOS_SELECIONADOS', 'Beneficiários selecionados: ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_PRESTADOR', 'Tipo de prestador: ','01-MÉDICO;02-CLÍNICA;03-LABORATÓRIO');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_VENCIMENTO_INICIAL','Data de vencimento inicial: ','DATE','[DATE]');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','PARCELA_INICIAL','Número da parcela inicial: ','TEXT','01');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_APENAS_ATIVOS','Exibir apenas beneficiários ativos ','CHECKBOX','S');
			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'NOME_PLANO_FAMILIARES','VARCHAR','Nome do plano',30,'N','S');
			 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código do beneficiário');
			 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome do beneficiário',30);
			 campoResultado($numeroRegistroProcesso,'DATA_VENCIMENTO','DATE');
			 campoResultado($numeroRegistroProcesso,'VALOR_FATURA','NUMERIC','','','S');
			 campoResultado($numeroRegistroProcesso,'DATA_PAGAMENTO','DATE');
			 campoResultado($numeroRegistroProcesso,'VALOR_PAGO','NUMERIC','','','S');

		}

}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de conferencia de faturas
/* Data: 31/08/2023 					
/* Responsável: Silvio
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3018()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '3018';
		$numeroRegistroPai		= '3100';
		$numeroRegistroProcesso = '3018';
		$labelProcesso          = 'Relatório de conferencia de faturamento';
		$descricaoProcesso      = 'Relatorio que ajuda o usuario a conferir o faturamento';
		$iconeProcesso          = 'subtitles';
		$destinoProcesso        = 'Relatorios/relatorioConferenciaFaturamento.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_ASSOCIADO_INICIAL','Código beneficiário inicial: ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_FATURAMENTO', 'Tipo de faturamento (PF ou PJ): ','PF-PLANOS PESSOA FISICA;PJ-PLANOS PESSOA JURIDICA;AB-AMBOS');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_VENCIMENTO_INICIAL','Data de vencimento inicial: ','DATE','[DATE]');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','PARCELA_INICIAL','Número da parcela inicial: ','TEXT','01');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_APENAS_ATIVOS','Exibir apenas beneficiários ativos ','CHECKBOX','S');
			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome do associado',30,'N','S');
			 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código do beneficiário');
			 campoResultado($numeroRegistroProcesso,'DATA_VENCIMENTO','DATE');
			 campoResultado($numeroRegistroProcesso,'VALOR_FATURA','NUMERIC','','','S');
			 campoResultado($numeroRegistroProcesso,'DATA_PAGAMENTO','DATE');
			 campoResultado($numeroRegistroProcesso,'VALOR_PAGO','NUMERIC','','','S');

		}

}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: 'Relatório de Beneficiarios completando "X" anos
/* Data: 20/09/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2204()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2204';
		$numeroRegistroPai		= '2200';
		$numeroRegistroProcesso = '2204';
		$labelProcesso          = 'Relatório de Beneficiarios completando "X" anos';
		$descricaoProcesso      = 'Relatorio que mostra quais beneficiários estão fazendo aniversário no período selecionado';
		$iconeProcesso          = '';
		$destinoProcesso        = 'Relatorios/relatorioBeneficiariosCompletandoXAnos.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código da empresa inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código da empresa final: ','TEXT','9999');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_SEQ_BENEF_INICIAL','Código seq. beneficiário inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_SEQ_BENEF_FINAL','Código seq. beneficiário final: ','TEXT','99999');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_INICIAL','Código do plano inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_FINAL','Código do plano final: ','TEXT','9999');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_ADMISSAO_INICIAL','Data de admissão inicial: ','DATE','1900-01-01');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_ADMISSAO_FINAL','Data de admissão final: ','DATE','2099-12-31');
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','ORDENACAO_DADOS', 'Ordem dos dados: ', 'NUM-Ordem numérica;ALFA-Ordem alfabética;NASC-Data de nascimento', 'NUM');
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','LISTAGEM_BENEFICIARIOS', 'Listar beneficiários: ','TD-Titulares e dependentes;T-Apenas os titulares;D-Apenas os dependentes', 'TD');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_VALIDACAO','Data para validação: ','DATE','[DATE]');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','DATA_ANIVERSARIO_INICIAL','Data de aniversário inicial: ','DATE','1900-01-01');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','DATA_ANIVERSARIO_FINAL','Data de aniversário final: ','DATE','2099-12-31');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','IDADE_INICIAL','Idade inicial: ','TEXT','0');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','IDADE_FINAL','Idade final: ','TEXT','150');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','FLAG_NAO_MOSTRAR_EXCLUIDOS','Não mostrar associados excluídos','CHECKBOX', 'N');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','FLAG_IMPRIMIR_GRUPO_CONTRATO','Imprimir Código Grupo Contrato','CHECKBOX','N');
			
			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'CODIGO_SEQUENCIAL_BENEF','VARCHAR','Cód. Sequencial do associado',30,'N','N');
			 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome do associado',30,'N','N');
			 campoResultado($numeroRegistroProcesso,'DATA_NASCIMENTO','DATE');
			 campoResultado($numeroRegistroProcesso,'IDADE','NUMERIC','Idade');
			 campoResultado($numeroRegistroProcesso,'DATA_EXCLUSAO','DATE');
			 campoResultado($numeroRegistroProcesso,'CODIGO_SEQUENCIAL_TIT','VARCHAR','Cód. Sequencial do titular',30,'N','N');
			 campoResultado($numeroRegistroProcesso,'NOME_TITULAR','VARCHAR','Nome do titular',30,'N','N');
		
		}

}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de excluídos agrupado por motivo de exclusão
/* Data: 21/09/2023 					
/* Responsável: Ricardo
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2205()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2205';
		$numeroRegistroPai		= '2200';
		$numeroRegistroProcesso = '2205';
		$labelProcesso          = 'Relatório de excluídos agrupado por motivo de exclusão';
		$descricaoProcesso      = 'Relatório de excluídos agrupado por motivo de exclusão';
		$iconeProcesso          = 'subtitles';
		$destinoProcesso        = 'Relatorios/relatorioBenef_AgrupadoPorMotivoExclusao.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_INICIAL_EXCLUSAO','Data inicial de Exclusão: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_FINAL_EXCLUSAO','Data final de Exclusão: ','TEXT','1');
			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'CODIGO_MOTIVO_EXCLUSAO','VARCHAR','Código',10,'N','S');
			 campoResultado($numeroRegistroProcesso,'NOME_MOTIVO_EXCLUSAO','VARCHAR','Nome do Motivo',40);
			 campoResultado($numeroRegistroProcesso,'NUMERO_VIDAS','NUMERIC','Vidas',8);
			 campoResultado($numeroRegistroProcesso,'CONTRATOS','NUMERIC','Contratos',8);
			 campoResultado($numeroRegistroProcesso,'TOTAL_FATURADO','NUMERIC','Valor faturado no mês da exclusão');

		}

}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de Contratos em aniversário
/* Data: 22/09/2023 					
/* Responsável: Ricardo
/* ---------------------------------------------------------------------------------------------------- */
function criaProcesso2206()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2206';
		$numeroRegistroPai		= '2200';
		$numeroRegistroProcesso = '2206';
		$labelProcesso          = 'Contratos em aniversário';
		$descricaoProcesso      = 'Contratos em aniversário';
		$iconeProcesso          = 'subtitles';
		$destinoProcesso        = 'Relatorios/relatorioContratosEmAniversario.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_PLANO', 'Planos ', 'FAM-Familiares;EMP-Empresariais', 'FAM');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','ANO_IGNORADO','Ano a ser ignorado: ','TEXT');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código da empresa inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código da empresa final: ','TEXT','9999');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_REFERENCIA_INICIAL','Mês de referência inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_REFERENCIA_FINAL','Mês de referência inicial: ','TEXT','1'); 
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_DATA_REAJUSTE','Mostrar Data de Reajuste e Mensalidade','CHECKBOX', 'N');
	
			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código');
			 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome',30,'N','N');
			 campoResultado($numeroRegistroProcesso,'CODIGO_EMPRESA','VARCHAR','Código',14,'N','N');
			 campoResultado($numeroRegistroProcesso,'NOME_EMPRESA','VARCHAR','Nome',52,'N','N');
			 campoResultado($numeroRegistroProcesso,'DATA_ADMISSAO_BENEF','DATE');
			 campoResultado($numeroRegistroProcesso,'DATA_ADMISSAO_EMP','DATE');
			 campoResultado($numeroRegistroProcesso,'DATA_REAJUSTE_BENEF','DATE');
			 campoResultado($numeroRegistroProcesso,'DATA_REAJUSTE_EMP','DATE');
			 campoResultado($numeroRegistroProcesso,'QTD_TITULARES','NUMERIC','Qt. Titulares',8);
			 campoResultado($numeroRegistroProcesso,'QTD_DEPENDENTES','NUMERIC','Qt. Depend.',8);
			 campoResultado($numeroRegistroProcesso,'QTD_VIDAS','NUMERIC','Qt. Vidas',8);
			 campoResultado($numeroRegistroProcesso,'ULTIMA_FATURA','NUMERIC','Ultima Fatura');
			 campoResultado($numeroRegistroProcesso,'DIA_VENCIMENTO','NUMERIC','Dia Vencimento');

		}

}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório geral de beneficiários
/* Data: 21/09/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2207()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2207';
		$numeroRegistroPai		= '2200';
		$numeroRegistroProcesso = '2207';
		$labelProcesso          = 'Relatório geral de beneficiários';
		$descricaoProcesso      = 'Relatório geral de beneficiários';
		$iconeProcesso          = '';
		$destinoProcesso        = 'Relatorios/relatorioGeralBeneficiarios.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','LISTAGEM_BENEFICIARIOS', 'Listar beneficiários: ', 'ATIV-Ativos por Produto;CANC-Cancelados por Produto;INCL-Incluídos por Produto', 'ATIV');
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','ORDENACAO_DADOS', 'Ordem dos dados: ', 'NUM-Ordem numérica;ALFA-Ordem alfabética', 'NUM');
			 
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_INICIAL','Código do plano inicial: ','TEXT','000000001', 'S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_FINAL','Código do plano final: ','TEXT','999999999', 'S');
			 			 
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_INICIAL','Data inicial: ','DATE','1900-01-01', 'S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_FINAL','Data final: ','DATE','2099-12-31', 'S');
			 
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_USA_DATA_ADMISSAO','Considerar data de admissão para beneficiários incluídos ','CHECKBOX','N');

			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'CODIGO_PLANO','VARCHAR','Código',10);
			 campoResultado($numeroRegistroProcesso,'NOME_PLANO','VARCHAR','Descrição do Produto',30);
			 campoResultado($numeroRegistroProcesso,'CODIGO_CADASTRO_ANS','VARCHAR','Registro do Produto',30);
			 campoResultado($numeroRegistroProcesso,'QUANTIDADE_PF','NUMERIC', 'Qtde Física', 14, 'S');
			 campoResultado($numeroRegistroProcesso,'QUANTIDADE_PJ','NUMERIC', 'Qtde Jurídica', 14, 'S');
			 campoResultado($numeroRegistroProcesso,'MEDIA_IDADE','NUMERIC', 'Média Idade', 14);
			 campoResultado($numeroRegistroProcesso,'QUANTIDADE_TOTAL','NUMERIC', 'Total', 14, 'S');
		}

}

/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de Fluxo de Beneficiários
/* Data: 26/09/2023 					
/* Responsável: Ricardo
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2208()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2208';
		$numeroRegistroPai		= '2200';
		$numeroRegistroProcesso = '2208';
		$labelProcesso          = 'Relatório de fluxo de beneficiários';
		$descricaoProcesso      = 'Relatório de fluxo de beneficiários';
		$iconeProcesso          = '';
		$destinoProcesso        = 'Relatorios/relatorioFluxoBeneficiarios.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código da empresa inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código da empresa final: ','TEXT','9999');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_INICIAL','Código do plano inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_FINAL','Código do plano final: ','TEXT','9999');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_IDENTIFICACAO','Listar apenas do vendedor: ','PS1100','CODIGO_IDENTIFICACAO','NOME_USUAL');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_INICIAL','Data inicial: ','DATE','1900-01-01', 'S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_FINAL','Data final: ','DATE','2099-12-31', 'S');

			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'CODIGO_EMPRESA','VARCHAR','Cód. Emp',14,'N','N');
			 campoResultado($numeroRegistroProcesso,'NOME_EMPRESA','VARCHAR','Nome da empresa',52,'N','N');
			 campoResultado($numeroRegistroProcesso,'SALDO_ANTERIOR','NUMERIC','Saldo Anterior',8,'S');
			 campoResultado($numeroRegistroProcesso,'ENTRADA_BENEF','NUMERIC','Entrada',8,'S');
			 campoResultado($numeroRegistroProcesso,'SAIDA_BENEF','NUMERIC','Saída',8,'S');
			 campoResultado($numeroRegistroProcesso,'SALDO_ATUAL','NUMERIC','Saldo Atual',8,'S');
			 campoResultado($numeroRegistroProcesso,'SALDO_MOVIMENTACAO','NUMERIC','Saldo Movimentação',8,'S');
		}

}



/* ---------------------------------------------------------------------------------------------------- */
/* Processo: 'Troca de titularidade
/* Data: 10/10/2023 					
/* Responsável: Silvio
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2210()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2210';
		$numeroRegistroPai		= '2200';
		$numeroRegistroProcesso = '2210';
		$labelProcesso          = 'Troca de titularidade';
		$descricaoProcesso      = 'Troca de titularidade entre beneficiários';
		$iconeProcesso          = '';
		$destinoProcesso        = 'ProcessoDinamico/processosDiversos01.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_ASSOCIADO_TITULAR','Código do beneficiário titular: ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_ASSOCIADO_DEPENDENTE','Código do dependente que será convertido em titular: ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','GRAU_PARENTESCO_NOVO_TITULAR','Código do parentesco do novo titular em relação ao dependente: ','PS1045','CODIGO_PARENTESCO','NOME_PARENTESCO');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_EXCLUIR_TITULAR_ANTIGO','Após converter, excluir o antigo titular','CHECKBOX','N');

		}

}




/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Transferência de Histórico 
/* Data: 11/10/2023 					
/* Responsável: Silvio
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2211()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2211';
		$numeroRegistroPai		= '2180';
		$numeroRegistroProcesso = '2211';
		$labelProcesso          = 'Transferencia de histórico';
		$descricaoProcesso      = 'Transferencia de histórico';
		$iconeProcesso          = '';
		$destinoProcesso        = 'ProcessoDinamico/processosDiversos01.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_ANTIGO_ASSOCIADO','Código antigo do beneficiário (origem): ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_NOVO_ASSOCIADO','Código novo do beneficiário (destino): ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_TRANSFERIR_AUTORIZACOES','Transferir autorizações do beneficiário','CHECKBOX','S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_TRANSFERIR_CONTAS_MEDICAS','Transferir contas médicas do beneficiário','CHECKBOX','S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_TRANSFERIR_FATURAMENTO','Transferir faturamento do beneficiário','CHECKBOX','S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_TRANSFERIR_AGENDAS','Transferir agendas do beneficiário','CHECKBOX','S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_TRANSFERIR_ODONTO','Transferir guias de odonto do beneficiário','CHECKBOX','S');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_TRANSFERIR_PRONTUARIO','Transferir prontuário do beneficiário','CHECKBOX','S');

		}

}



/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Aplicação de correção de valor nominal
/* Data: 11/10/2023 					
/* Responsável: Silvio
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2212()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2212';
		$numeroRegistroPai		= '2180';
		$numeroRegistroProcesso = '2212';
		$labelProcesso          = 'Aplicação de correção de valor nominal';
		$descricaoProcesso      = 'Aplicação de correção de valor nominal';
		$iconeProcesso          = '';
		$destinoProcesso        = 'ProcessoDinamico/processosDiversos01.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			 $ordemCriacao = 0;

			 filtroAutoComplete($numeroRegistroProcesso,'1-Filtros a aplicar','CODIGO_EMPRESA_INICIAL','Código da empresa inicial: ','PS1010','CODIGO_EMPRESA','NOME_EMPRESA');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Filtros a aplicar','CODIGO_EMPRESA_FINAL','Código da empresa final: ','PS1010','CODIGO_EMPRESA','NOME_EMPRESA');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Filtros a aplicar','CODIGO_PLANO_INICIAL','Código do plano inicial: ','PS1030','CODIGO_PLANO','NOME_PLANO_FAMILIARES');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Filtros a aplicar','CODIGO_PLANO_FINAL','Código do plano final: ','PS1030','CODIGO_PLANO','NOME_PLANO_FAMILIARES');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Filtros a aplicar','CODIGO_GRUPO_PESSOAS','Apenas do seguinte grupo de pessoas: ','PS1014','CODIGO_GRUPO_PESSOAS','NOME_GRUPO_PESSOAS');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Filtros a aplicar','CODIGO_GRUPO_FATURAMENTO','Apenas do seguinte grupo de faturamento: ','PS1051','CODIGO_GRUPO_FATURAMENTO','DESCRICAO_GP_FATURAMENTO');

			 filtroGeral($numeroRegistroProcesso,'1-Filtros a aplicar','DATA_ADMISSAO_INICIAL','Data de admissão inicial:','DATE','1900-01-01', 'S');
			 filtroGeral($numeroRegistroProcesso,'1-Filtros a aplicar','DATA_ADMISSAO_FINAL','Data de admissão final:','DATE','2099-12-31', 'S');

			 filtroGeral($numeroRegistroProcesso,'2-Parametros do processo','TAXA_CALCULO','Taxa de cálculo a aplicar (multiplicador)','TEXT','','N','0*,00');
			 filtroGeral($numeroRegistroProcesso,'2-Parametros do processo','FLAG_APENAS_SIMULAR_NAO_APLICAR','Apenas simular e exibir no relatório, não gravar','CHECKBOX','S');
		}

}




/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Mudança de categoria
/* Data: 11/10/2023 					
/* Responsável: Silvio
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2213()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2213';
		$numeroRegistroPai		= '2180';
		$numeroRegistroProcesso = '2213';
		$labelProcesso          = 'Mudança de categoria';
		$descricaoProcesso      = 'Mudança de categoria';
		$iconeProcesso          = '';
		$destinoProcesso        = 'ProcessoDinamico/processosDiversos01.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		{
			 $ordemCriacao = 0;

			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_ASSOCIADO','Código do beneficiário: ','PS1000','CODIGO_ASSOCIADO','NOME_ASSOCIADO');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_ANTIGO','Migrar do plano: ','PS1030','CODIGO_PLANO','NOME_PLANO_FAMILIARES');
			 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_PLANO_NOVO','Migrar para o plano: ','PS1030','CODIGO_PLANO','NOME_PLANO_FAMILIARES');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_MIGRACAO','Data da migração:','DATE','', 'S');
		}

}



/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de dados das empresas
/* Data: 26/09/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2301()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '2301';
	$numeroRegistroPai		= '2300';
	$numeroRegistroProcesso = '2301';
	$labelProcesso          = 'Relatório de dados das empresas';
	$descricaoProcesso      = 'Relatório de dados das empresas';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioDadosEmpresas.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código inicial da empresa: ','TEXT','00001', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código final da empresa: ','TEXT','99999', 'S');

		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','DADOS_RELATORIO', 'Dados do relatório:', 'END-Dados de endereço;FAT-Dados de faturamento;DOC-Dados de documentação;SEG-Quantidade de segurados;PRE-Tabela de preços especiais', 'END');		 
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_SITUACAO_ATEND','Código situação atendimento:','TEXT');
		 
		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','ORDENACAO_DADOS', 'Ordem dos dados: ', 'NUM-Ordem numérica;ALFA-Ordem alfabética', 'NUM');		 
		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_ASSOCIADOS', 'Associados:', 'FAM-Plano Familiar;EMP-Plano Empresarial;AFA-Afastados', 'EMP');		 

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_NAO_EMPR_EXCLUIDA','Não mostrar empresas excluídas','CHECKBOX','N');

		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_EMPRESA','VARCHAR','Código',10);
		 campoResultado($numeroRegistroProcesso,'NOME_EMPRESA','VARCHAR','Nome Empresa',30);
		 campoResultado($numeroRegistroProcesso,'ENDERECO_EMPRESA','VARCHAR','Endereço',30);
		 campoResultado($numeroRegistroProcesso,'BAIRRO_EMPRESA','VARCHAR','Bairro');
		 campoResultado($numeroRegistroProcesso,'CIDADE_EMPRESA','VARCHAR','Cidade');
		 campoResultado($numeroRegistroProcesso,'TELEFONE_1','VARCHAR','Fone 1');
		 campoResultado($numeroRegistroProcesso,'TELEFONE_2','VARCHAR','Fone 2');
		 campoResultado($numeroRegistroProcesso,'CEP_EMPRESA','VARCHAR','CEP');
		 campoResultado($numeroRegistroProcesso,'NOME_PLANO','VARCHAR','Categoria');
		 campoResultado($numeroRegistroProcesso,'DATA_INICIO_CONTRATO','DATE','Início Contrato');
		 campoResultado($numeroRegistroProcesso,'PLANO_REGULAMENTADO','VARCHAR','Regulamentado');

		 campoResultado($numeroRegistroProcesso,'DIA_VENCIMENTO','VARCHAR','Dia de vencimento');
		 //taxa de desconto ???
		 campoResultado($numeroRegistroProcesso,'CODIGO_BANCO','VARCHAR','Banco');
		 campoResultado($numeroRegistroProcesso,'ISENTO_PAGAMENTO','VARCHAR','Isento Pagto');
		 campoResultado($numeroRegistroProcesso,'COBRA_FAMILIA','VARCHAR','Cob p/ Familia');
		 campoResultado($numeroRegistroProcesso,'NUMERO_CONTRATO','VARCHAR','Contrato');
		 campoResultado($numeroRegistroProcesso,'TEM_VALOR_PARTICULAR','VARCHAR','Preço Especial');
		 campoResultado($numeroRegistroProcesso,'GERA_NOTA_FISCAL','VARCHAR','Gera Nota?');
		 campoResultado($numeroRegistroProcesso,'GERA_BOLETO','VARCHAR','Gera Boleto?');

		 campoResultado($numeroRegistroProcesso,'NUMERO_CNPJ','VARCHAR','CNPJ');
		 campoResultado($numeroRegistroProcesso,'NUMERO_INSC_ESTADUAL','VARCHAR','Insc. Estadual');
		 //nome do contato???

		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_TITULAR','NUMERIC', 'Qtde Titulares', 14, 'S');
		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_DEPENDENTE','NUMERIC', 'Qtde Dependentes', 14, 'S');
		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_TOTAL','NUMERIC', 'Qtde Total', 14, 'S');

		 campoResultado($numeroRegistroProcesso,'IDADE_MINIMA','NUMERIC', 'Idade Min.');
		 campoResultado($numeroRegistroProcesso,'IDADE_MAXIMA','NUMERIC', 'Idade Max.');
		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_MINIMA','NUMERIC', 'Quant. Min.');
		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_MAXIMA','NUMERIC', 'Quant. Max.');
		 campoResultado($numeroRegistroProcesso,'VALOR_PLANO','NUMERIC', 'Valor');
	}
}

/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de Contratos em aniversário - Empresas
/* Data: 27/09/2023 					
/* Responsável: Ricardo
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2302()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2302';
		$numeroRegistroPai		= '2300';
		$numeroRegistroProcesso = '2302';
		$labelProcesso          = 'Contratos em aniversário';
		$descricaoProcesso      = 'Contratos em aniversário';
		$iconeProcesso          = 'subtitles';
		$destinoProcesso        = 'Relatorios/relatorioContratosEmAniversarioEmpresa.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_PLANO', 'Planos ', 'FAM-Familiares;EMP-Empresariais', 'EMP');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','ANO_IGNORADO','Ano a ser ignorado: ','TEXT');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código da empresa inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código da empresa final: ','TEXT','9999');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_REFERENCIA_INICIAL','Mês de referência inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_REFERENCIA_FINAL','Mês de referência inicial: ','TEXT','1'); 
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','FLAG_DATA_REAJUSTE','Mostrar Data de Reajuste e Mensalidade','CHECKBOX', 'N');
	
			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código');
			 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome',30,'N','N');
			 campoResultado($numeroRegistroProcesso,'CODIGO_EMPRESA','VARCHAR','Código',14,'N','N');
			 campoResultado($numeroRegistroProcesso,'NOME_EMPRESA','VARCHAR','Nome',52,'N','N');
			 campoResultado($numeroRegistroProcesso,'DATA_ADMISSAO_BENEF','DATE');
			 campoResultado($numeroRegistroProcesso,'DATA_ADMISSAO_EMP','DATE');
			 campoResultado($numeroRegistroProcesso,'DATA_REAJUSTE_BENEF','DATE');
			 campoResultado($numeroRegistroProcesso,'DATA_REAJUSTE_EMP','DATE');
			 campoResultado($numeroRegistroProcesso,'QTD_TITULARES','NUMERIC','Qt. Titulares',8);
			 campoResultado($numeroRegistroProcesso,'QTD_DEPENDENTES','NUMERIC','Qt. Depend.',8);
			 campoResultado($numeroRegistroProcesso,'QTD_VIDAS','NUMERIC','Qt. Vidas',8);
			 campoResultado($numeroRegistroProcesso,'ULTIMA_FATURA','NUMERIC','Ultima Fatura');
			 campoResultado($numeroRegistroProcesso,'DIA_VENCIMENTO','NUMERIC','Dia Vencimento');

		}

}

/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de Empresas Reajustadas
/* Data: 27/09/2023 					
/* Responsável: Ricardo
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso2303()
{

	   global $ordemCriacao;

		$numeroRegistroMenu     = '2303';
		$numeroRegistroPai		= '2300';
		$numeroRegistroProcesso = '2303';
		$labelProcesso          = 'Relat. Empresas Reajustadas';
		$descricaoProcesso      = 'Relat. Empresas Reajustadas';
		$iconeProcesso          = 'subtitles';
		$destinoProcesso        = 'Relatorios/relatorioEmpresasReajustadas.php';

		$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

		if ($resultado=='INCLUIDO')
		
		{
			/* Filtros a serem questionados ao usuário */	

			 $ordemCriacao = 0;

			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código da empresa inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código da empresa final: ','TEXT','9999');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_ANO_INICIAL','Mês/ano de referência inicial: ','TEXT','1');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_ANO_FINAL','Mês/ano de referência inicial: ','TEXT','1'); 
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_ADMISSAO_INICIAL','Data Admissão inicial: ','DATE','1900-01-01');
			 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_ADMISSAO_FINAL','Data Admissão final: ','DATE','2099-12-31');
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','ORDENACAO_DADOS', 'Ordem dos dados: ', 'NUM-Ordem numérica;ALFA-Ordem alfabética', 'NUM');
			 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_TABELA', 'Tipo de tabela utilizada: ', 'EMP-Especifica na empresa;PLANO-Padrão no cadastro de plano', 'EMP');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','FLAG_NAO_EMPR_EXCLUIDA','Não mostrar empresas excluídas','CHECKBOX','S');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','FLAG_ULTIMO_FATOR_VALOR','Apresentar apenas último fator e último valor válido','CHECKBOX');
			 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','FLAG_ORDENAR_MESANO_FATOR','Forçar ordenação pelo mês/ano de referência do fator de conversão','CHECKBOX');

			 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

			/* Campos a serem exibidos no relatorio */
			 
			 $ordemCriacao = 0;

			 campoResultado($numeroRegistroProcesso,'CODIGO_NOMEEMPRESA','VARCHAR','Empresa',61,'N','S');
			 campoResultado($numeroRegistroProcesso,'NUMERO_CONTRATO','VARCHAR','Contrato',14,'N','N');
			 campoResultado($numeroRegistroProcesso,'CODIGO_NOMEPLANO','VARCHAR','Produto',40,'N','N');
			 campoResultado($numeroRegistroProcesso,'TIPO_LANCAMENTO','VARCHAR','Tipo Lançamento',20,'N','N');
			 campoResultado($numeroRegistroProcesso,'INDICE','NUMERIC','Indice(%)',8);
			 campoResultado($numeroRegistroProcesso,'MES_ANO_REFERENCIA','VARCHAR','Mes/Ano',9,'N','N');
			 campoResultado($numeroRegistroProcesso,'CODIGO_TABELA_PRECO','VARCHAR','Cod.Tabela',14,'N','N');
			 campoResultado($numeroRegistroProcesso,'IDADE_MINIMA_MAXIMA','VARCHAR','Faixa Etária',20,'N','N');
			 campoResultado($numeroRegistroProcesso,'VALOR_ANTIGO','NUMERIC','Vlr. Antigo');
			 campoResultado($numeroRegistroProcesso,'VALOR_ATUAL','NUMERIC','Vlr. Atual');

		}

}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de conferência de baixas
/* Data: 28/09/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3102()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '3102';
	$numeroRegistroPai		= '3100';
	$numeroRegistroProcesso = '3102';
	$labelProcesso          = 'Conferência de baixas por período';
	$descricaoProcesso      = 'Relatório de conferência de baixas por período';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioConferenciaBaixasPeriodo.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código inicial da empresa:','TEXT','00001', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código final da empresa:','TEXT','99999', 'S');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_PAGTO_INICIAL','Data inicial dos pagamentos:','DATE','1900-01-01', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_PAGTO_FINAL','Data final dos pagamentos:','DATE','2099-12-31', 'S');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_EMISS_FAT_INICIAL','Data inicial da emissão de faturam.:','DATE','1900-01-01', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_EMISS_FAT_FINAL','Data final da emissão de faturam.:','DATE','2099-12-31', 'S');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_BANCO_INICIAL','Código inicial do banco:','TEXT','001');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_BANCO_FINAL','Código final do banco:','TEXT','999');

		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_FATURAS', 'Tipo das faturas:', 'PF-Planos Familiares;PJ-Planos Empresariais;AMB-Ambos', 'PF');		
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_ANO_REFERENCIA','Apenas mês/ano de referência (em branco p/ todos):','TEXT'); 
		 
		 filtroCombo($numeroRegistroProcesso,'2-Filtros auxiliares','ORDENACAO_DADOS', 'Ordem dos dados:', 'PAG-Data de pagamento;ALFA-Ordem alfabética', 'PAG');		 
		 filtroCombo($numeroRegistroProcesso,'2-Filtros auxiliares','APENAS_TIPO_BAIXA', 'Apenas baixa do tipo:', 'T-Todas as baixas;A-Baixas automáticas;M-Baixas manuais;R-Baixas via recibo;L-Baixas via autenticador;D-Débito automático;P-Baixas parciais');		 

		 filtroCombo($numeroRegistroProcesso,'2-Filtros auxiliares','APENAS_TIPO_FATURA', 'Apenas faturas do tipo:', 'T-Todas as faturas;F-Faturamento;P-Pós pagamento;C-Coparticipação;N-Negociação;A-Adicionais;Q-Franquia;O-Outros');	
		 filtroAutoComplete($numeroRegistroProcesso,'2-Filtros auxiliares','CODIGO_OPERADOR_BAIXA','Apenas baixas do operador:','PS1100','CODIGO_IDENTIFICACAO','NOME_USUAL');

		 filtroCombo($numeroRegistroProcesso,'2-Filtros auxiliares','MOSTRAR_DADOS_ADICIONAIS', 'Exibir informações adicionais:', 'VIDAS-Mostrar quantidade de vidas por contrato;BANCO-Mostrar dados do banco de baixa');	

		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código');
		 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome do Sacado',30);
		 campoResultado($numeroRegistroProcesso,'CODIGO_EMPRESA','VARCHAR','Código');
		 campoResultado($numeroRegistroProcesso,'NOME_EMPRESA','VARCHAR','Nome do Sacado',30);

		 campoResultado($numeroRegistroProcesso,'VALOR_FATURA','NUMERIC','Valor Fatura',14,'S');
		 campoResultado($numeroRegistroProcesso,'DATA_VENCIMENTO','DATE','Vencimento');
		 campoResultado($numeroRegistroProcesso,'VALOR_MULTA','NUMERIC','Valor Multa',14,'S');
		 campoResultado($numeroRegistroProcesso,'VALOR_PAGO','NUMERIC','Valor Pago',14,'S');
		 campoResultado($numeroRegistroProcesso,'TIPO_BAIXA','VARCHAR','Baixa');
		 campoResultado($numeroRegistroProcesso,'DATA_PAGAMENTO','DATE','Pagamento');
		 campoResultado($numeroRegistroProcesso,'NUMERO_CONTRATO','VARCHAR','Contrato');
		 campoResultado($numeroRegistroProcesso,'NUMERO_PARCELA','NUMERIC','Parcela');
		 campoResultado($numeroRegistroProcesso,'DATA_EMISSAO','DATE','Data Emissão');
		 campoResultado($numeroRegistroProcesso,'OBSERVACOES_COBRANCA','VARCHAR','Observações');

		 campoResultado($numeroRegistroProcesso,'CODIGO_BANCO_BAIXA','VARCHAR','Cód Banco Baixa');
		 campoResultado($numeroRegistroProcesso,'NUMERO_CONTA_BAIXA','VARCHAR','Num Conta Baixa');

		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_VIDAS','NUMERIC','Qtde Vidas');

		 campoResultado($numeroRegistroProcesso,'MES_ANO_REFERENCIA','VARCHAR','Mês/Ano Referência');		 

	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de previsão de receitas
/* Data: 04/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3104()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '3104';
	$numeroRegistroPai		= '3100';
	$numeroRegistroProcesso = '3104';
	$labelProcesso          = 'Relatório de previsão de receitas';
	$descricaoProcesso      = 'Relatório de previsão de receitas';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioPrevisaoReceitas.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_INICIAL_PREVISAO','Data inicial para previsão (vencimento):','DATE','2000-01-01', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_FINAL_PREVISAO','Data final para previsão (vencimento):','DATE','2099-12-31', 'S');

		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','LISTAGEM_BENEFICIARIOS', 'Listar beneficiários: ', 'ATIV-Apenas os ativos;EXCL-Apenas os excluídos;AMB-Ambos (ativos e excluídos)', 'AMB');

		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'DATA_VENCIMENTO','DATE','Data');
		 campoResultado($numeroRegistroProcesso,'PREVISAO_FAMILIAR','NUMERIC','Previsão Familiar',14,'S');
		 campoResultado($numeroRegistroProcesso,'PREVISAO_EMPRESARIAL','NUMERIC','Previsão Empresarial',14,'S');
		 campoResultado($numeroRegistroProcesso,'JA_PAGO_FAMILIAR','NUMERIC','Já Pago Familiar',14,'S');
		 campoResultado($numeroRegistroProcesso,'JA_PAGO_EMPRESARIAL','NUMERIC','Já Pago Empresarial',14,'S');

	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de Recibos Emitidos
/* Data: 04/10/2023 					
/* Responsável: Ricardo
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3105()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '3105';
	$numeroRegistroPai		= '3100';
	$numeroRegistroProcesso = '3105';
	$labelProcesso          = 'Relatório de Recibos Emitidos';
	$descricaoProcesso      = 'Relatório de Recibos Emitidos';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioRecibosEmitidos.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_INICIAL_EMISSAO','Data inicial de emissão :','DATE','1900-01-01', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_FINAL_EMISSAO','Data final de emissão :','DATE','2099-12-31', 'S');
		 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_OPERADOR','Código do operador:','PS1100','CODIGO_IDENTIFICACAO','NOME_USUAL');
		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','SITUACAO_RECIBO', 'Mostrar recibos: ', 'TODOS-Todos os recibos;ATIVOS-Apenas recibos sem data de cancelamento;CANCELADOS-Apenas recibos cancelados', 'TODOS');


		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'NUMERO_RECIBO','VARCHAR','N. Recibo');
		 campoResultado($numeroRegistroProcesso,'CODIGO_NOME_BENEF_EMP','VARCHAR','Código e Nome do Sacado',60);
		 campoResultado($numeroRegistroProcesso,'REFERENCIA_RECIBO','VARCHAR','Ref.',40);
		 campoResultado($numeroRegistroProcesso,'VALOR_TOTAL_RECIBO','NUMERIC','Vl.Pago',14,'S');
		 campoResultado($numeroRegistroProcesso,'DATA_EMISSAO','DATE','Emissão');
		 campoResultado($numeroRegistroProcesso,'DATA_PAGAMENTO','DATE','Dt.Pagto');
		 campoResultado($numeroRegistroProcesso,'NOME_USUAL','VARCHAR','Operador',30);
		 campoResultado($numeroRegistroProcesso,'OBSERVACAO_RECIBO','VARCHAR','Reg.Fatura');
		 campoResultado($numeroRegistroProcesso,'TIPO_ESPECIE','VARCHAR','Especie');


	}
}

/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de Inadimplentes com dias acumulados
/* Data: 05/10/2023 					
/* Responsável: Ricardo
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3107()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '3107';
	$numeroRegistroPai		= '3100';
	$numeroRegistroProcesso = '3107';
	$labelProcesso          = 'Inadimplentes com dias acumulados';
	$descricaoProcesso      = 'Inadimplentes com dias acumulados';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioInadimplentesDiasAcumulados.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código inicial da empresa:','TEXT','400', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código final da empresa:','TEXT','400', 'S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_SEQ_BENEF_INICIAL','Código seq. beneficiário inicial:','TEXT','1');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_SEQ_BENEF_FINAL','Código seq. beneficiário final:','TEXT','999999');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','QTD_DIAS_INICIAL','Quantidade de dias de atraso (Início):','TEXT','1');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','QTD_DIAS_FINAL','Quantidade de dias de atraso (Fim):','TEXT','60');
		 filtroCombo($numeroRegistroProcesso,'2-Filtros auxiliares','TIPO_PLANO', 'Gerar para PF ou PJ:', 'PF-Faturas de pessoa física;PJ-Faturas de pessoa jurídica', 'PF');
		 filtroCombo($numeroRegistroProcesso,'2-Filtros auxiliares','ORDENAR_RELATORIO', 'Ordenar relatório por:', 'CODIGO-Código;NOME-Nome', 'CODIGO');
		 filtroCombo($numeroRegistroProcesso,'2-Filtros auxiliares','SITUACAO_FATURA', 'Faturas a serem consideradas:', 'ABERTO-Somente faturas em aberto;PAGAS-Somente faturas pagas em atraso', 'ABERTO');
		 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','FLAG_EXIBIR_BENEF_EMP_EXC','Exibir beneficiários/empresas excluídos(a)','CHECKBOX','S');
		 filtroGeral($numeroRegistroProcesso,'2-Filtros auxiliares','FLAG_CONSIDERA_FAT_CANC','Considerar faturas mesmo que estejam canceladas','CHECKBOX','S');
		 
		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código do Associado',14,'N','S');
		 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome do Associado',30);
		 campoResultado($numeroRegistroProcesso,'CODIGO_EMPRESA','VARCHAR','Código',14,'N','S');
		 campoResultado($numeroRegistroProcesso,'NOME_EMPRESA','VARCHAR','Nome',52,'N','N');
		 campoResultado($numeroRegistroProcesso,'DIAS_ACUMULADOS','NUMERIC','Dias Acumulados de atraso',6);
		 campoResultado($numeroRegistroProcesso,'NUMERO_REGISTRO','NUMERIC','Registro da Fatura',10);
		 campoResultado($numeroRegistroProcesso,'DATA_VENCIMENTO','DATE','Data vencimento');
		 campoResultado($numeroRegistroProcesso,'DATA_PAGAMENTO','DATE','Data pagamento');


	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de estatísticas sobre faturamento
/* Data: 05/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3108()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '3108';
	$numeroRegistroPai		= '3100';
	$numeroRegistroProcesso = '3108';
	$labelProcesso          = 'Estatísticas sobre faturamento';
	$descricaoProcesso      = 'Relatório de estatísticas sobre faturamento';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioEstatisticasFaturamento.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_INICIAL','Data inicial:','DATE','1900-01-01');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_FINAL','Data final:','DATE','2099-12-31');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_ANO_REF','Mês/ano de referência:','TEXT','','N');

		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','DADOS_RELATORIO', 'Dados a exibir:', 'VENC-Faturas por vencimento;EMIS-Faturas por emissão;PAGA-Faturas pagas;INAD-Inadimplentes','VENC','S');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_GRUPO_INICIAL','Código grupo contrato inicial: ','TEXT');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_GRUPO_FINAL','Código grupo contrato final: ','TEXT');

		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_FATURAS','NUMERIC','Quantidade de faturas',30);
		 campoResultado($numeroRegistroProcesso,'VALOR_TOTAL','NUMERIC','Valor total',15);

		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_EMITIDAS','NUMERIC','Quantidade de emissões',30);
		 campoResultado($numeroRegistroProcesso,'VALOR_EMITIDO','NUMERIC','Valor emitido',15);

		 campoResultado($numeroRegistroProcesso,'MES','VARCHAR','Mês');
		 campoResultado($numeroRegistroProcesso,'ANO','VARCHAR','Ano');
		 campoResultado($numeroRegistroProcesso,'QUANTIDADE','VARCHAR','Quantidade');
		 campoResultado($numeroRegistroProcesso,'VALOR','NUMERIC','Valor');

		 campoResultado($numeroRegistroProcesso,'QUANTIDADE_ABERTO','VARCHAR','Quant em aberto');
		 campoResultado($numeroRegistroProcesso,'VALOR_INADIMP','NUMERIC','Valor inadimplência');

	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de programação de eventos adicionais
/* Data: 06/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3109()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '3109';
	$numeroRegistroPai		= '3100';
	$numeroRegistroProcesso = '3109';
	$labelProcesso          = 'Programação de eventos adicionais';
	$descricaoProcesso      = 'Relatório de programação de eventos adicionais';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioProgramEventosAdicionais.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código inicial da empresa: ','TEXT');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código final da empresa: ','TEXT');

		 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_EVENTO','Código do evento adicional:','PS1024','CODIGO_EVENTO','NOME_EVENTO','','S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_ANO_REFERENCIA','Mês/Ano de referência (em branco p/ todos):','TEXT'); 

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_EXCLUSAO_INICIAL','Data de exclusão inicial:','DATE');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_EXCLUSAO_FINAL','Data de exclusão final:','DATE');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','NUMERO_PARCELA','Número parcela:','TEXT');
		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','ORDENACAO_DADOS', 'Ordem dos dados:', 'NUM-Ordem Numérica;ALFA-Ordem alfabética', 'NUM');		 

		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código beneficiário');
		 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome do beneficiário','30');
		 campoResultado($numeroRegistroProcesso,'DATA_EVENTO','DATE','Data do evento');
		 campoResultado($numeroRegistroProcesso,'VALOR_EVENTO','NUMERIC','Valor do evento','14','S');		 
		 campoResultado($numeroRegistroProcesso,'DATA_EXCLUSAO','DATE','Data de exclusão');
		 campoResultado($numeroRegistroProcesso,'CODIGO_ANTIGO','VARCHAR','Código antigo');
		 campoResultado($numeroRegistroProcesso,'DATA_NASCIMENTO','DATE','Data de nascimento');	

		 campoResultado($numeroRegistroProcesso,'NOME_EMPRESA','VARCHAR','Nome da empresa','14','N','S');

	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de valores de coparticipação
/* Data: 09/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */

function criaProcesso3110()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '3110';
	$numeroRegistroPai		= '3100';
	$numeroRegistroProcesso = '3110';
	$labelProcesso          = 'Relatório de valores de coparticipação';
	$descricaoProcesso      = 'Relatório de valores de coparticipação';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioValoresCoparticipacao.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0;

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_INICIAL','Código inicial da empresa: ','TEXT');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_EMPRESA_FINAL','Código final da empresa: ','TEXT');

		 filtroAutoComplete($numeroRegistroProcesso,'1-Opções principais','CODIGO_EVENTO','Código da coparticipação:','PS1024','CODIGO_EVENTO','NOME_EVENTO','','S');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','MES_ANO_REFERENCIA','Mês/Ano de referência (em branco p/ todos):','TEXT'); 

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_EXCLUSAO_INICIAL','Data de exclusão inicial:','DATE');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_EXCLUSAO_FINAL','Data de exclusão final:','DATE');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','NUMERO_PARCELA','Número parcela:','TEXT');
		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','ORDENACAO_DADOS', 'Ordem dos dados:', 'NUM-Ordem Numérica;ALFA-Ordem alfabética', 'NUM');		 

		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_ASSOCIADO','VARCHAR','Código beneficiário');
		 campoResultado($numeroRegistroProcesso,'NOME_ASSOCIADO','VARCHAR','Nome do beneficiário','30');
		 campoResultado($numeroRegistroProcesso,'DATA_EXCLUSAO','DATE','Data exclusão');
		 campoResultado($numeroRegistroProcesso,'DATA_EVENTO','DATE','Data do evento');
		 campoResultado($numeroRegistroProcesso,'VALOR_COPARTICIPACAO','NUMERIC','Valor coparticipação','14','S');		 
		 campoResultado($numeroRegistroProcesso,'NOME_PRESTADOR','VARCHAR','Nome do prestador');
		 campoResultado($numeroRegistroProcesso,'NOME_PROCEDIMENTO','VARCHAR','Nome do procedimento');
		 campoResultado($numeroRegistroProcesso,'MES_ANO_REFERENCIA','VARCHAR','Mês/Ano de referência');
		 campoResultado($numeroRegistroProcesso,'NUMERO_GUIA','VARCHAR','Número guia');
		 campoResultado($numeroRegistroProcesso,'TIPO_GUIA','VARCHAR','Tipo guia');

		 campoResultado($numeroRegistroProcesso,'CODIGO_EMPRESA','VARCHAR','Código da empresa','14','N','S');

	}
}




/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de cadastro de comissionáveis
/* Data: 10/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */
function criaProcesso4601()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '4601';
	$numeroRegistroPai		= '4600';
	$numeroRegistroProcesso = '4601';
	$labelProcesso          = 'Relatório de cadastro de comissionáveis';
	$descricaoProcesso      = 'Relatório de cadastro de comissionáveis';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioCadastroComissionaveis.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0; 

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_ID_INICIAL','Código inicial de identificação:','TEXT','1');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_ID_FINAL','Código final de identificação:','TEXT','99999');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_CADASTR_INICIAL','Data de cadastramento inicial:','DATE','1900-01-01');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_CADASTR_FINAL','Data de cadastramento final:','DATE','2099-12-31');
		 
		 filtroCombo($numeroRegistroProcesso,'1-Opções principais','TIPO_CADASTRO', 'Tipo do cadastro:', 'VEND-Vendedores;CORR-Corretoras;OUTR-Demais comissionáveis', 'VEND');			 

		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_IDENTIFICACAO','VARCHAR','Código');
		 campoResultado($numeroRegistroProcesso,'NOME_USUAL','VARCHAR','Nome','30');
		 campoResultado($numeroRegistroProcesso,'DATA_CADASTRO','DATE','Data cadastro');
		 campoResultado($numeroRegistroProcesso,'TIPO_PESSOA','VARCHAR','Tipo pessoa');
		 campoResultado($numeroRegistroProcesso,'ENDERECO','VARCHAR','Endereço','30');
		 campoResultado($numeroRegistroProcesso,'BAIRRO','VARCHAR','Bairro');
		 campoResultado($numeroRegistroProcesso,'CIDADE','VARCHAR','Cidade');
		 campoResultado($numeroRegistroProcesso,'CEP','VARCHAR','CEP');
		 campoResultado($numeroRegistroProcesso,'ESTADO','VARCHAR','UF');
		 campoResultado($numeroRegistroProcesso,'TELEFONE_PRINCIPAL','VARCHAR','Telefone');
		 campoResultado($numeroRegistroProcesso,'PESSOA_CONTATO','VARCHAR','Pessoa de contato');

	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Vidas por vendedor/Contrato
/* Data: 10/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */
function criaProcesso4602()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '4602';
	$numeroRegistroPai		= '4600';
	$numeroRegistroProcesso = '4602';
	$labelProcesso          = 'Vidas por Vendedor/Contrato';
	$descricaoProcesso      = 'Vidas por Vendedor/Contrato';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioVidasPorVendedor.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0; 

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_ID_INICIAL','Código inicial de identificação:','TEXT','1');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_ID_FINAL','Código final de identificação:','TEXT','99999');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_CONTRATO_INICIAL','Data do contrato inicial:','DATE','1900-01-01');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_CONTRATO_FINAL','Data do contrato final:','DATE','2099-12-31');
		 
		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_IDENTIFICACAO','VARCHAR','Código','14','N','S');
		 campoResultado($numeroRegistroProcesso,'NOME_USUAL','VARCHAR','Nome');

		 campoResultado($numeroRegistroProcesso,'NUMERO_CONTRATO','VARCHAR','Contrato');
		 campoResultado($numeroRegistroProcesso,'DATA_CONTRATO','DATE','Data contrato');
		 campoResultado($numeroRegistroProcesso,'NOME_CONTRATANTE','VARCHAR','Nome Contratante');
		 campoResultado($numeroRegistroProcesso,'VALOR_CONTRATO','NUMERIC','Valor contrato','14','S');		 
		 campoResultado($numeroRegistroProcesso,'CODIGO_TIPO_COMISSAO','VARCHAR','Tabela');
		 campoResultado($numeroRegistroProcesso,'QTDE_VIDAS','NUMERIC','Vidas','14','S');

	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de cadastro de contatos comerciais
/* Data: 10/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */
function criaProcesso4603()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '4603';
	$numeroRegistroPai		= '4600';
	$numeroRegistroProcesso = '4603';
	$labelProcesso          = 'Cadastro de contatos comerciais';
	$descricaoProcesso      = 'Relatório de cadastro de contatos comerciais';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioContatosComerciais.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0; 

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_SOLICIT_INICIAL','Data da solicitação inicial:','DATE','1900-01-01');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_SOLICIT_FINAL','Data da solicitação final:','DATE','2099-12-31');
		 
		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'DATA_SOLICITACAO','DATE','Data solicitação');
		 campoResultado($numeroRegistroProcesso,'NOME_PESSOA','VARCHAR','Nome');
		 campoResultado($numeroRegistroProcesso,'EMAIL_CONTATO','VARCHAR','E-mail');
		 campoResultado($numeroRegistroProcesso,'TELEFONE_CONTATO','VARCHAR','Telefone');
		 campoResultado($numeroRegistroProcesso,'OBSERVACOES','VARCHAR','Observação','50');
		 campoResultado($numeroRegistroProcesso,'PLANO_DE_INTERESSE','VARCHAR','Plano de interesse','50');

	}
}


/* ---------------------------------------------------------------------------------------------------- */
/* Processo: Relatório de cadastro de fornecedores
/* Data: 10/10/2023 					
/* Responsável: Tavares
/* ---------------------------------------------------------------------------------------------------- */
function criaProcesso5201()
{

	global $ordemCriacao;

	$numeroRegistroMenu     = '5201';
	$numeroRegistroPai		= '5200';
	$numeroRegistroProcesso = '5201';
	$labelProcesso          = 'Relatório de cadastro de fornecedores';
	$descricaoProcesso      = 'Relatório de cadastro de fornecedores';
	$iconeProcesso          = '';
	$destinoProcesso        = 'Relatorios/relatorioCadastroFornecedores.php';

	$resultado = CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso);

	if ($resultado=='INCLUIDO')
	{
		/* Filtros a serem questionados ao usuário */	

		 $ordemCriacao = 0; 

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_ID_INICIAL','Código inicial de identificação:','TEXT','1');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','CODIGO_ID_FINAL','Código final de identificação:','TEXT','99999');

		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_CADASTR_INICIAL','Data de cadastramento inicial:','DATE','1900-01-01');
		 filtroGeral($numeroRegistroProcesso,'1-Opções principais','DATA_CADASTR_FINAL','Data de cadastramento final:','DATE','2099-12-31');
		 
		 criaOpcoesPadraoRelatorio($numeroRegistroProcesso);

		/* Campos a serem exibidos no relatorio */
		 
		 $ordemCriacao = 0;

		 campoResultado($numeroRegistroProcesso,'CODIGO_IDENTIFICACAO','VARCHAR','Código');
		 campoResultado($numeroRegistroProcesso,'NOME_USUAL','VARCHAR','Nome','30');
		 campoResultado($numeroRegistroProcesso,'DATA_CADASTRO','DATE','Data cadastro');
		 campoResultado($numeroRegistroProcesso,'TIPO_PESSOA','VARCHAR','Tipo pessoa');
		 campoResultado($numeroRegistroProcesso,'ENDERECO','VARCHAR','Endereço','30');
		 campoResultado($numeroRegistroProcesso,'BAIRRO','VARCHAR','Bairro');
		 campoResultado($numeroRegistroProcesso,'CIDADE','VARCHAR','Cidade');
		 campoResultado($numeroRegistroProcesso,'CEP','VARCHAR','CEP');
		 campoResultado($numeroRegistroProcesso,'ESTADO','VARCHAR','UF');
		 campoResultado($numeroRegistroProcesso,'TELEFONE_PRINCIPAL','VARCHAR','Telefone');
		 campoResultado($numeroRegistroProcesso,'PESSOA_CONTATO','VARCHAR','Pessoa de contato');

	}
}



/* ---------------------------------------------------------------------------------------------------- */
/* Funções estruturais e utilitárias para o processo
/* Data: 31/08/2023 					
/* Responsável: Silvio
/* ---------------------------------------------------------------------------------------------------- */

function CriaMenuEProcesso($numeroRegistroMenu, $numeroRegistroPai, $numeroRegistroProcesso, $labelProcesso, $descricaoProcesso, $iconeProcesso, $destinoProcesso)
{

	$rowTemp = qryUmRegistro('Select Numero_Registro From cfgprocessos_pd Where Numero_Registro = ' . numSql($numeroRegistroProcesso));

	if ($rowTemp->NUMERO_REGISTRO=='')
	{

		$labelProcesso = copyDelphi($labelProcesso,1,40);

		/* Cria registro do menu */ 

		$sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('NUMERO_REGISTRO',$numeroRegistroMenu);
		$sqlEdicao 	.= linhaJsonEdicao('NUMERO_REGISTRO_PAI', $numeroRegistroPai);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','LABEL_MENU', $labelProcesso);
		$sqlEdicao 	.= linhaJsonEdicao('FLAG_HABILITADO','S');
		$sqlEdicao 	.= linhaJsonEdicao('PERFIS_VISIVEL', 'OPERADOR');
		$sqlEdicao 	.= linhaJsonEdicao('ICONE',$iconeProcesso);
		$sqlEdicao 	.= linhaJsonEdicao('LINK_PAGINA', 'site/processoDinamico');
		$sqlEdicao 	.= linhaJsonEdicao('DADOS_LINK_PAGINA','{/"reg/":/"' . $numeroRegistroProcesso . '/"}');
		$sqlEdicao 	.= linhaJsonEdicao('TIPO_FILTRO_CLIENTE','TODOS');
		$sqlEdicao 	.= linhaJsonEdicao('FLAG_MENU_SUPERIOR','N');
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','DESCRICAO_MENU',$descricaoProcesso);
			
		gravaEdicao('CFGMENU_DINAMICO_NET_AL2', $sqlEdicao, 'I');


		/* Cria registro da tabela de processos */ 

		$sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('NUMERO_REGISTRO',$numeroRegistroProcesso);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','NOME_PROCESSO', $labelProcesso);
		$sqlEdicao 	.= linhaJsonEdicao('TIPO_PROCESSO','DES');
		$sqlEdicao 	.= linhaJsonEdicao('DESTINO_PROCESSO', $destinoProcesso);
		$sqlEdicao 	.= linhaJsonEdicao('TEMPO_VERIFICACAO','0');
		$sqlEdicao 	.= linhaJsonEdicao('MENSAGEM_CONFIRMA', 'Confirma o processamento?');
		$sqlEdicao 	.= linhaJsonEdicao('AUX_PADRAO','RELATORIO');
		$sqlEdicao 	.= linhaJsonEdicao('BOTAO_IMPRIMIR','S');
		$sqlEdicao 	.= linhaJsonEdicao('FLAG_2COLUNAS_AL2','S');
			
		gravaEdicao('CFGPROCESSOS_PD', $sqlEdicao, 'I');

		return 'INCLUIDO';

	}
	else
	{

		return 'EXISTENTE';

	}

}



function filtroAutoComplete($numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $nomeTabelaRelacionada, $campoIdTabelaRelacionada, $campoPesquisaTabelaRelacionada, $valorPadrao = '', $flagNotNull = 'N', $tipoMascara='')
{
	 return gravaFiltro('AUTOCOMPLETE', $numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $nomeTabelaRelacionada, $campoIdTabelaRelacionada, $campoPesquisaTabelaRelacionada, $opcoesCombo, $valorPadrao, $flagNotNull, $tipoMascara);
}

function filtroManual($numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $nomeTabelaRelacionada, $campoIdTabelaRelacionada, $campoPesquisaTabelaRelacionada, $valorPadrao = '', $flagNotNull = 'N', $tipoMascara='')
{
	 return gravaFiltro('FILTROMANUAL', $numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $nomeTabelaRelacionada, $campoIdTabelaRelacionada, $campoPesquisaTabelaRelacionada, $opcoesCombo, $valorPadrao, $flagNotNull, $tipoMascara);
}

function filtroCombo($numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $opcoesCombo, $valorPadrao = '', $flagNotNull = 'N', $tipoMascara='')
{
	 return gravaFiltro('COMBOBOX', $numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $nomeTabelaRelacionada, $campoIdTabelaRelacionada, $campoPesquisaTabelaRelacionada, $opcoesCombo, $valorPadrao, $flagNotNull, $tipoMascara);
}

function filtroGeral($numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $tipoCampo, $valorPadrao = '', $flagNotNull = 'N', $tipoMascara='')
{
	 return gravaFiltro($tipoCampo, $numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $nomeTabelaRelacionada, $campoIdTabelaRelacionada, $campoPesquisaTabelaRelacionada, $opcoesCombo, $valorPadrao, $flagNotNull, $tipoMascara);
}


function gravaFiltro($componenteFormulario, $numeroRegistroProcesso, $pastaApresentacao, $nomeCampo, $labelCampo, $nomeTabelaRelacionada, $campoIdTabelaRelacionada, $campoPesquisaTabelaRelacionada, $opcoesCombo, $valorPadrao = '', $flagNotNull = 'N', $tipoMascara='')
{

	   global $ordemCriacao;

		$sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('NUMERO_REGISTRO_PROCESSO', $numeroRegistroProcesso);
		$sqlEdicao 	.= linhaJsonEdicao('NOME_CAMPO',$nomeCampo);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','LABEL_CAMPO', $labelCampo);
		$sqlEdicao 	.= linhaJsonEdicao('FLAG_NOTNULL',$flagNotNull);
		$sqlEdicao 	.= linhaJsonEdicao('COMPONENTE_FORMULARIO', $componenteFormulario);
		$sqlEdicao 	.= linhaJsonEdicao('COMPORTAMENTO_FRM_EDICAO', '1');
		$sqlEdicao 	.= linhaJsonEdicao('NUMERO_ORDEM_CRIACAO',$ordemCriacao,'N');
		$sqlEdicao 	.= linhaJsonEdicao('NOME_TABELA_RELACIONADA',$nomeTabelaRelacionada);
		$sqlEdicao 	.= linhaJsonEdicao('CAMPO_ID_TABELA_RELAC',$campoIdTabelaRelacionada);
		$sqlEdicao 	.= linhaJsonEdicao('CAMPO_PESQUISA_TABELA_RELAC',$campoPesquisaTabelaRelacionada);
		$sqlEdicao 	.= linhaJsonEdicao('TIPO_MASCARA',$tipoMascara);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','HINT_EXPLICACAO',$hintExplicacao);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','OPCOES_COMBO',$opcoesCombo);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','VALOR_PADRAO',$valorPadrao);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','PASTA_APRESENTACAO',$pastaApresentacao);
		$sqlEdicao 	.= linhaJsonEdicao('CLASSE_CAMPO','400px');
			
		gravaEdicao('CFGCAMPOS_PD', $sqlEdicao, 'I');

		$ordemCriacao += 10;
}


function campoResultado($numeroRegistroProcesso,$nomeCampo,$tipoCampo='',$labelCampo='',$tamanhoExibicao='14',$totalizarCampo='N',$agruparCampo='N',$tipoMascara='')
{

	   global $ordemCriacao;

		if (($tipoCampo=='DATE') and ($tamanhoExibicao==''))
			$tamanhoExibicao = '12';

		if (($tipoCampo=='NUMERIC') and ($tamanhoExibicao==''))
			$tamanhoExibicao = '14';

		if ($labelCampo=='')
		{
			$labelCampo = mascaraNomeCampo($nomeCampo);
		}

		$sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('NUMERO_REGISTRO_PROCESSO', $numeroRegistroProcesso);
		$sqlEdicao 	.= linhaJsonEdicao('NOME_CAMPO',$nomeCampo);
		$sqlEdicao 	.= linhaJsonEspecial('SEM_ACENTO','LABEL_CAMPO', $labelCampo);
		$sqlEdicao 	.= linhaJsonEdicao('TIPO_CAMPO', $tipoCampo);
		$sqlEdicao 	.= linhaJsonEdicao('TAMANHO_EXIBICAO',$tamanhoExibicao);
		$sqlEdicao 	.= linhaJsonEdicao('MASCARA_CAMPO',$tipoMascara);
		$sqlEdicao 	.= linhaJsonEdicao('ORDEM_CAMPO',$ordemCampo);
		$sqlEdicao 	.= linhaJsonEdicao('FLAG_EXIBIR_CAMPO','S');
		$sqlEdicao 	.= linhaJsonEdicao('FLAG_TOTALIZAR_CAMPO',$totalizarCampo);
		$sqlEdicao 	.= linhaJsonEdicao('FLAG_AGRUPAR_CAMPO',$agruparCampo);

		gravaEdicao('CFGRELATORIOS_CAMPOS_PD', $sqlEdicao, 'I');

		$ordemCriacao += 10;
}


function criaOpcoesPadraoRelatorio($numeroRegistroProcesso)
{			 

	filtroGeral($numeroRegistroProcesso,'2-Opcoes adicionais','FLAG_EXIBIR_FILTROS_UTILIZADOS','Exibir filtros utilizados no final do relatorio ','CHECKBOX','N');
	filtroCombo($numeroRegistroProcesso,'2-Opcoes adicionais','FORMATO_SAIDA', 'Formato para emissao do relatorio: ','HTML-PAGINA HTML;TXT-ARQUIVO TEXTO;PDF-ARQUIVO PDF;XLS-ARQUIVO EXCEL;CSV-ARQUIVO CSV;JSON-ARQUIVO JSON','HTML');
	filtroGeral($numeroRegistroProcesso,'2-Opcoes adicionais','FLAG_REPETIR_CABECALHOS','Repetir cabeçalhos nas quebras do relatório','CHECKBOX','S');
	filtroCombo($numeroRegistroProcesso,'2-Opcoes adicionais','TIPO_LAYOUT_RELATORIO', 'Layout de aparência do relatório: ','PADRAO-LAYOUT PADRAO;MODELO_01-MODELO 01;MODELO_02-MODELO 02','PADRAO');

}

?>