<?php

require_once('../lib/base.php');
//require('../private/autentica.php');
require_once('../EstruturaPrincipal/processoPx.php');
//require('../lib/sysutils01.php');
require_once('../lib/sysutilsAlianca.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

//	$_GET['Teste'] = 'OK';
//  prDebug($_POST['ID_PROCESSO']);

set_time_limit(0);






echo apresentaMensagemInicioProcesso('PROC_RAPIDO');
$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));


/* --------------------------------------------------------------------------------------------------------- */
/*	PROCESSO 7003
/*  Função: Calculo de faturamento a partir do formulario de processamento
/*  Data de Implementação: 02/08/2023  				Desenvolvedor: Silvio 
/*	Ultima manutenção:								Desenvolvedor:
/* --------------------------------------------------------------------------------------------------------- */

if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7003') 
{

	calculaFaturamentoConformeParametrosFormProcessamento();

}


 


function calculaFaturamentoConformeParametrosFormProcessamento()
{

		$nomearquivoLogProcesso       = escreveArquivoLogProcesso('C',$_POST['ID_PROCESSO']);
		$linhaArquivo      		  	  = '';
		$linhaLogProcesso  			  = '';
		$referenciaCalculo     		  = rand() . '_' . date('d/m/Y') . '_' . date('H:i');

		echo apresentaMensagemInicioProcesso();

		//

		$linhaLogProcesso .= adicionaCampoLinhaLog('Registro',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Codigo',25);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Nome do pagador',40);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vencimento',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl fatura',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Convênio',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Correção',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Adicional',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Outros',14);

		escreveArquivoLogProcesso('L',$linhaLogProcesso);

		//

		$linhaLogProcesso 	      = '';
		$resBeneficiarios         = selecionaBeneficiariosCalcularFaturamento();
		$tipoEmpresaAlianca       = retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA');

		$valoresConvenio          = array();
		$valoresCorrecao          = array();

		$familiaValorConvenio     = 0;
		$familiaValorCorrecao     = 0;
		$familiaValorAdicional    = 0;

		$contratoValorConvenio    = 0;
		$contratoValorCorrecao    = 0;
		$contratoValorAdicional   = 0;

		$totalValorConvenio       = 0;
		$totalValorCorrecao       = 0;
		$totalValorAdicional      = 0;
		$contador                 = 0;
		$rowCodigoRegistroGerado  = '';
		$pessoasCalculadas        = 0;
		$faturasGeradas           = 0;

		while ($rowBeneficiarios = jn_fetch_object($resBeneficiarios))
		{

			$contador++;

			if ((($familiaValorConvenio+$familiaValorCorrecao+$familiaValorAdicional)>0) and ($rowBeneficiarios->TIPO_ASSOCIADO=='T'))
			{
				registraLogFaturaCalculada('TOTFAM',$registroCalculado,null,$familiaValorConvenio,$familiaValorCorrecao,$familiaValorAdicional,
					                       'OPERACAO: '. $tipoEdicao,$numeroRegistroPs1020);

				$familiaValorConvenio = 0;
				$familiaValorCorrecao = 0;
				$familiaValorAdicional= 0;
			}

			if ((($contratoValorConvenio+$contratoValorCorrecao+$contratoValorAdicional)>0) and ($registroCalculado->CODIGO_EMPRESA != $rowBeneficiarios->CODIGO_EMPRESA))
			{
				if ($_POST['TIPOS_EMPRESAS']=='PJ')
				{
					registraLogFaturaCalculada('TOTEMP',$registroCalculado,null,$contratoValorConvenio,$contratoValorCorrecao,$contratoValorAdicional,
						                       'OPERACAO: '. $tipoEdicao,$numeroRegistroPs1020);

					$contratoValorConvenio = 0;
					$contratoValorCorrecao = 0;
					$contratoValorAdicional= 0;
				}
			}

			if (($rowBeneficiarios->FLAG_COBRA_FAMILIA=='S') and ($rowBeneficiarios->TIPO_ASSOCIADO=='D'))
			{
				$observacaoNaoGeracao = 'Registro ignorado, validação: FLAG_COBRA_FAMILIA'; 
				registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,$observacaoNaoGeracao,'');
				continue;
			}
			
			if ($rowBeneficiarios->FLAG_TRAVA_FATURAMENTO=='S')
			{
				$observacaoNaoGeracao = 'Registro ignorado, validação: FLAG_TRAVA_FATURAMENTO'; 
				registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,$observacaoNaoGeracao,'');
				continue;
			}

		    if ($_POST['FLAG_VALIDA_REGRAS_GRUPO_FATURAMENTO']=='S')
		    {
		        $percentualGrupoFaturamento   = 100;

		         $rowPs1051 = qryUmRegistro('Select Ps1051.Codigo_Grupo_faturamento, Ps1057.* From Ps1051 
		                                     Inner Join Ps1057 on (Ps1051.Codigo_Grupo_faturamento = Ps1057.Codigo_Grupo_Faturamento) 
		                                     Where (Ps1051.Codigo_Grupo_Faturamento = ' . numSql($rowBeneficiarios->CODIGO_GRUPO_FATURAMENTO) . ') And 
		                                           (Ps1057.Data_Vigencia_Inicial <= ' . DataToSql(sqlToData($dataVencimento)) . ') 
		                                           Order By Ps1057.Data_Vigencia_Inicial Desc');

		         $percentualGrupoFaturamento = $rowPs1051->PERCENTUAL_APLICACAO;

		         if ($percentualGrupoFaturamento == 0)
		         {
					$observacaoNaoGeracao = 'Registro ignorado, validação: FLAG_VALIDA_REGRAS_GRUPO_FATURAMENTO'; 
					registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,$observacaoNaoGeracao,'');
		            continue;
		         }
		    }

		    if (($_POST['MES_VENCIMENTO'] == 2) and ($rowBeneficiario->DIA_VENCIMENTO >= 29))
		       	$dataVencimento    = getMontaData(strzero($_POST['ANO_VENCIMENTO'],4) . '/' . strzero($_POST['MES_VENCIMENTO'],2) . '/' . strzero('28',2));
		    else
		    {
		        if ($rowBeneficiarios->DIA_VENCIMENTO >= 31) 
		          	$dataVencimento    = getMontaData(strzero($_POST['ANO_VENCIMENTO'],4) . '/' . strzero($_POST['MES_VENCIMENTO'],2) . '/' . strzero('30',2));
		        else
					$dataVencimento    = getMontaData(strzero($_POST['ANO_VENCIMENTO'],4) . '/' . strzero($_POST['MES_VENCIMENTO'],2) . '/' . strzero($rowBeneficiarios->DIA_VENCIMENTO,2));
		    }

			$mesAnoReferencia    = extraiMesAnoData($dataVencimento);

		    if (retornaValorConfiguracao('TRAVAR_FATURAS_FECHADAS')== 'SIM') 
		    {
				if (!podeModificarFaturamento($mesAnoReferencia,'CALCULO_FAT'))
				{
					registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,'A fatura deste beneficiário/empresa não será calculada, pois o mês de referência já está fechado',0);
					continue;
				}
		    }


			if ((($_POST['TIPOS_EMPRESAS']=='PJ') and ($rowCodigoRegistroGerado!=$rowBeneficiarios->CODIGO_EMPRESA)) or //PJ-Empresas PJ (PLANOS EMPRESARIAIS);
			    (($_POST['TIPOS_EMPRESAS']=='PF') and ($rowBeneficiarios->TIPO_ASSOCIADO=='T')) or //PF-Beneficiários PF (PLANOS FAMILIARES PESSOA FÍSICA);
			    (($_POST['TIPOS_EMPRESAS']=='BE') and ($rowBeneficiarios->TIPO_ASSOCIADO=='T'))) //BE-Beneficiários empresariais (Fatur. individualizado de vl. adicionais)
			{

			    if ($rowBeneficiarios->MESES_REALIZACAO_FATURAM != '')
			    {
			    	if (strpos($rowBeneficiarios->MESES_REALIZACAO_FATURAM,$_POST['MES_VENCIMENTO'])!==false)
			    	{
			    	   $observacaoNaoGeracao = 'Registro ignorado, validação: MESES_REALIZACAO_FATURAM'; 
					   registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,$observacaoNaoGeracao,'');
			           continue;
			    	}
			    }

				if (($_POST['TIPOS_EMPRESAS']=='PF') or //PF-Beneficiários PF (PLANOS FAMILIARES PESSOA FÍSICA);
					($_POST['TIPOS_EMPRESAS']=='BE')) //BE-Beneficiários empresariais (Fatur. individualizado de vl. adicionais)
				{
				    $qryTmp         = 'Select COALESCE(max(NUMERO_PARCELA),0) ULTIMA from ps1020 where (CODIGO_ASSOCIADO = ' . aspas($rowBeneficiarios->CODIGO_ASSOCIADO) . ') ';
				}
				else
				{
				    $qryTmp         = 'Select COALESCE(max(NUMERO_PARCELA),0) ULTIMA from ps1020 where (CODIGO_EMPRESA = ' . aspas($rowBeneficiarios->CODIGO_EMPRESA) . ') ';
				}

				$rowTmp         = qryUmRegistro($qryTmp);	
				$numeroParcela  = $rowTmp->ULTIMA + 1;
				$dataEmissao    = dataHoje();

				//

				if (($_POST['TIPOS_EMPRESAS']=='PF') or //PF-Beneficiários PF (PLANOS FAMILIARES PESSOA FÍSICA);
					($_POST['TIPOS_EMPRESAS']=='BE')) //BE-Beneficiários empresariais (Fatur. individualizado de vl. adicionais)
				{
					$qryTmp = 'Select NUMERO_REGISTRO, NUMERO_NOTA_FISCAL, DATA_PAGAMENTO from ps1020 where (CODIGO_ASSOCIADO = ' . aspas($rowBeneficiarios->CODIGO_ASSOCIADO) . ') ';
				}
				else
				{
					$qryTmp = 'Select NUMERO_REGISTRO, NUMERO_NOTA_FISCAL, DATA_PAGAMENTO from ps1020 where (CODIGO_EMPRESA = ' . aspas($rowBeneficiarios->CODIGO_EMPRESA) . ') ';
				}

				if ($_POST['FLAG_VALIDA_EXISTENCIA_VENCIMENTO']=='S')
				   $qryTmp .= ' and DATA_VENCIMENTO = ' . dataToSql($dataVencimento);
				else
				   $qryTmp .= ' and MES_ANO_REFERENCIA = ' . aspas($mesAnoReferencia);

				$rowFaturaPreCalculada         = qryUmRegistro($qryTmp);	

				if ($rowFaturaPreCalculada->NUMERO_NOTA_FISCAL!='')
				{
					registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,
						                       'A fatura deste beneficiário não será calculada, pois pertence a nota fiscal número: ' . 
						                       $rowFaturaPreCalculada->NUMERO_NOTA_FISCAL,$rowFaturaPreCalculada->NUMERO_REGISTRO);
					continue;
				}

				if (($_POST['FLAG_SOBREPOE_FATURA_EXISTENTE']!='S') && ($rowFaturaPreCalculada->NUMERO_REGISTRO >= 1))
				{
					registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,
						                       'A fatura deste beneficiário/empresa não será calculada, pois já foi calculada e a opção de "Sobrepor" não foi marcada',
						                       $rowFaturaPreCalculada->NUMERO_REGISTRO);
					continue;
				}
				else if ((retornaValorConfiguracao('TRAVAR_EDICAO_FATURAS_BAIXADAS')=='SIM') and ($rowFaturaPreCalculada->DATA_PAGAMENTO != ''))
				{
					registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,
						                       'A fatura deste beneficiário/empresa não será calculada, pois já foi baixada (paga) anteriormente',
						                       $rowFaturaPreCalculada->NUMERO_REGISTRO);
					continue;
				}

				//

				if (retornaValorConfiguracao('TRAVAR_FATURAS_FECHADAS') =='SIM')
				{
					$qryTmp = 'Select MES_ANO_REFERENCIA From Ps1067 Where (Flag_Travar_Edicao = ' . aspas('S') . ')';

					 if (retornaValorConfiguracao('FLAG_TRAVAR_FAT_P_EMISSAO')== 'S')
					 {
					 	$qryTmp .= ' and (Mes_Ano_Referencia = ' . aspas(extraiMesAnoData(dataHoje())) . ')';
					 }
					 else
					 {
						$qryTmp .= ' and (Mes_Ano_Referencia = ' . aspas(extraiMesAnoData($dataVencimento)) . ')';
					 }

					 $rowTmp = qryUmRegistro($qryTmp);

					 if ($rowTmp->MES_ANO_REFERENCIA!='')
					 {
						registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,'A fatura deste beneficiário/empresa não será calculada, 
							                       pois a competência já está fechada',$rowFaturaPreCalculada->NUMERO_REGISTRO);
						continue;
					 }
				}

				//

			    if (($rowBeneficiarios->DATA_PRIMEIRO_FATURAMENTO!='') and
			        ($dataVencimento < $rowBeneficiarios->DATA_PRIMEIRO_FATURAMENTO))
				{
					registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,
						                       'A fatura deste beneficiário/empresa não será calculada, pois tem o vencimento antes do primeiro faturamento preenchido',
						                       $rowFaturaPreCalculada->NUMERO_REGISTRO);
					continue;
				}

				$rowCodigoRegistroGerado = $rowBeneficiarios->CODIGO_EMPRESA;

				if ($rowFaturaPreCalculada->NUMERO_REGISTRO!='')
				{
			           $numeroRegistroPs1020  = $rowFaturaPreCalculada->NUMERO_REGISTRO;
			           $tipoEdicao            = 'A';
			           $criterioWhereGravacao = ' NUMERO_REGISTRO = ' . $numeroRegistroPs1020;

			           excluiDetalhamentosEstornaFatura($numeroRegistroPs1020);
				}
				else
				{
					$numeroRegistroPs1020  = jn_gerasequencial('PS1020');
			        $tipoEdicao            = 'I';
			        $criterioWhereGravacao = '';
				}	


				if ($registroCalculado->CODIGO_EMPRESA != $rowBeneficiarios->CODIGO_EMPRESA)
				{
					registraLogFaturaCalculada('CABEC_EMP',$rowBeneficiarios,null,0,0,0,'',$numeroRegistroPs1020);
				}

				/* -----------------------------------------------------------------------------------------------------------
				*	GRAVA O CABECALHO DA FATURA PARA QUE EU POSSA IR GRAVANDO OS DETALHES
				*	DEPOIS AO FINAL DO PROCESSO VOU ATUALIZAR O VALOR TOTAL DO TOTAL DE PESSOAS EXISTENTES
				* ----------------------------------------------------------------------------------------------------------- */

				if ($rowBeneficiarios->TIPO_ASSOCIADO=='T')
				{
					$sqlEdicao  = '';

					if ($tipoEdicao=='I')
					{

						if (($_POST['TIPOS_EMPRESAS']=='PF') or //PF-Beneficiários PF (PLANOS FAMILIARES PESSOA FÍSICA);
							($_POST['TIPOS_EMPRESAS']=='BE')) //BE-Beneficiários empresariais (Fatur. individualizado de vl. adicionais)
						    $sqlEdicao 	.= linhaJsonEdicao('Codigo_Associado', $rowBeneficiarios->CODIGO_ASSOCIADO);
						else
							$sqlEdicao 	.= linhaJsonEdicao('Codigo_empresa', $rowBeneficiarios->CODIGO_EMPRESA);

						$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro', $numeroRegistroPs1020);
						$sqlEdicao 	.= linhaJsonEdicao('Tipo_Registro', 'F');
						$sqlEdicao 	.= linhaJsonEdicao('Data_Emissao', $dataEmissao,'D');
						$sqlEdicao 	.= linhaJsonEdicao('Numero_Parcela', $numeroParcela);
						$sqlEdicao 	.= linhaJsonEdicao('Informacoes_Geracao', 'PRE-CALCULO-TMP');
						$sqlEdicao 	.= linhaJsonEdicao('Mes_Ano_Referencia',$mesAnoReferencia);
						$sqlEdicao 	.= linhaJsonEdicao('Descricao_Observacao', 'NOVO_CALCULO');
					}
					else
					{
						$sqlEdicao 	.= linhaJsonEdicao('Descricao_Observacao', 'ALTERACAO_CALCULO');
					}

					$sqlEdicao 	.= linhaJsonEdicao('Data_Vencimento', $dataVencimento,'D');

					if (($_POST['TIPOS_EMPRESAS']=='PF') or //PF-Beneficiários PF (PLANOS FAMILIARES PESSOA FÍSICA);
						($_POST['TIPOS_EMPRESAS']=='BE')) //BE-Beneficiários empresariais (Fatur. individualizado de vl. adicionais)
					    $sqlEdicao 	.= linhaJsonEdicao('Codigo_Sequencial', $rowBeneficiarios->CODIGO_SEQUENCIAL);

					$sqlEdicao 	.= linhaJsonEdicao('Data_Validacao', dataAngularToSql($_POST['DATA_COMPETENCIA']));
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura', 0,'N');
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Convenio', 0,'N');
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Adicional', 0,'N');
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Correcao', 0,'N');
					$sqlEdicao 	.= linhaJsonEdicao('Percentual_Correcao', 0,'N');
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Prorrata', 0,'N');
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura_Bruto', 0,'N');

					gravaEdicao('PS1020', $sqlEdicao, $tipoEdicao, $criterioWhereGravacao);

					$faturasGeradas++;

				}

			}

			/* -----------------------------------------------------------------------------------------------------------
			*	AGORA COMEÇA O CALCULO EFETIVO, BENEFICIARIO POR BENEFICIARIO
			*	AO FINAL DO PROCESSO, O VALOR TOTAL DA PS1020 SERÁ GRAVADO
			* ----------------------------------------------------------------------------------------------------------- */

			$valoresConvenio        = calculaValorConvenio($rowBeneficiarios,$dataVencimento);
			$valorConvenio          = $valoresConvenio['CONVENIO'];
			$valorNet               = $valoresConvenio['NET'];

		    if (($rowBeneficiarios->FATOR_TAXA_CALCULO!=0) && ($rowBeneficiarios->FATOR_TAXA_CALCULO!=''))
		    {
		        if (($rowBeneficiarios->DATA_LIMITE_TAXA!='') and ($rowBeneficiarios->DATA_LIMITE_TAXA >= $dataVencimento))
		        {
					$valorConvenio          = $valorConvenio * $rowBeneficiarios->FATOR_TAXA_CALCULO;
					$valorNet               = $valorNet * $rowBeneficiarios->FATOR_TAXA_CALCULO;
				}
		    }

		   	//

		   	if ($_POST['FLAG_CALCULA_FATURAMENTO_PROPORCIONAL']=='S')
		   	{
			   	$valorConvenio = calculaFaturamentoProporcional($rowBeneficiarios->CODIGO_ASSOCIADO,$dataVencimento,$valorConvenio, $rowBeneficiarios->DATA_ADMISSAO_PS1000BENEF, $rowBeneficiarios->DATA_EXCLUSAO_PS1000BENEF);
			}

		   	//

			$valoresCorrecao        = calculaReajusteAnual($rowBeneficiarios, $valorConvenio, $valorNet, $dataVencimento);
			$valorCorrecaoConvenio  = $valoresCorrecao['CONVENIO'];
			$valorCorrecaoNet       = $valoresCorrecao['NET'];

		    if (($rowBeneficiarios->FATOR_TAXA_CALCULO != '') && ($rowBeneficiarios->FATOR_TAXA_CALCULO != '0'))
		    {
				$valorConvenio         = $valorConvenio * $rowBeneficiarios->FATOR_TAXA_CALCULO;
				$valorNet              = $valorNet  * $rowBeneficiarios->FATOR_TAXA_CALCULO;
				$valorCorrecaoConvenio = $valorCorrecaoConvenio  * $rowBeneficiarios->FATOR_TAXA_CALCULO;
				$valorCorrecaoNet      = $valorCorrecaoNet  * $rowBeneficiarios->FATOR_TAXA_CALCULO;
		    }

			//

			$valorAdicional         = calculaValorAdicional($rowBeneficiarios, $valorConvenio, $valorCorrecaoConvenio, 'FATURAMENTO_NORMAL', $dataVencimento,$numeroRegistroPs1020);

			$valorConvenio          = round($valorConvenio, 2);
			$valorNet               = round($valorNet, 2);
			$valorCorrecaoConvenio  = round($valorCorrecaoConvenio, 2);
			$valorCorrecaoNet       = round($valorCorrecaoNet, 2);
			$valorAdicional         = round($valorAdicional, 2);

			//

			prDebug($contador . ' - ' . $rowBeneficiarios->CODIGO_ASSOCIADO . ' - ' . $rowBeneficiarios->NOME_ASSOCIADO . ' - Vl Convenio ' . $valorConvenio . 
			                    ' - Vl Correcao ' . $valorCorrecaoConvenio . ' - Vl Adicional ' . $valorAdicional);

			/* -----------------------------------------------------------------------------------------------------------
			*	GRAVO OS DADOS DA PS1021 DO BENEFICIÁRIO POSICIONADO
			* ----------------------------------------------------------------------------------------------------------- */

			$sqlEdicao   = '';
			$sqlEdicao 	.= linhaJsonEdicao('Flag_Principal_Substituida','P');
			$sqlEdicao 	.= linhaJsonEdicao('Codigo_Empresa',$rowBeneficiarios->CODIGO_EMPRESA);
			$sqlEdicao 	.= linhaJsonEdicao('Codigo_Associado', $rowBeneficiarios->CODIGO_ASSOCIADO);
			$sqlEdicao 	.= linhaJsonEdicao('Codigo_Titular', $rowBeneficiarios->CODIGO_TITULAR);
			$sqlEdicao 	.= linhaJsonEdicao('Mes_Ano_Vencimento',extraiMesAnoData($dataVencimento));
			$sqlEdicao 	.= linhaJsonEdicao('Numero_Parcela_Beneficiario', $numeroParcela);
			$sqlEdicao 	.= linhaJsonEdicao('Codigo_Plano',$rowBeneficiarios->CODIGO_PLANO);
			$sqlEdicao 	.= linhaJsonEdicao('Data_Emissao', $dataEmissao,'D');
			$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020',$numeroRegistroPs1020);
			$sqlEdicao 	.= linhaJsonEdicao('Codigo_Tabela_Preco',$rowBeneficiarios->CODIGO_TABELA_PRECO);
			$sqlEdicao 	.= linhaJsonEdicao('Valor_Convenio',$valorConvenio,'N');
			$sqlEdicao 	.= linhaJsonEdicao('Valor_Adicional',$valorAdicional ,'N');
			$sqlEdicao 	.= linhaJsonEdicao('Valor_Correcao', $valorCorrecaoConvenio,'N');
			$sqlEdicao 	.= linhaJsonEdicao('VALOR_CALCULO_PROPORCIONAL', 0,'N');
			$sqlEdicao 	.= linhaJsonEdicao('Valor_Outros',0,'N');

			if ($tipoEmpresaAlianca=='ADMINISTRADORA') 
			    $sqlEdicao 	.= linhaJsonEdicao('Valor_Net_Corrigido',$valorNet + $valorCorrecaoNet,'N');

			if ($rowBeneficiarios->TIPO_ASSOCIADO=='T')
		 	   $sqlEdicao 	.= linhaJsonEdicao('Valor_Prorrata',0,'N');

			$sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura',$valorConvenio + $valorCorrecaoConvenio + $valorAdicional,'N');

			gravaEdicao('PS1021', $sqlEdicao, 'I');


			/* -----------------------------------------------------------------------------------------------------------
			*	GRAVO OS DADOS DA PS1068, APROPRIAÇÃO DE RECEITAS DO BENEFICIÁRIO POSICIONADO
			*   SÓ APROPRIA RECEITAS SE FOR OPERADORA. SE FOR ADMINISTRADORA OU CARTÃO DE DESCONTO ELE NÃO APROPRIA
			* ----------------------------------------------------------------------------------------------------------- */

			if (($tipoEmpresaAlianca=='OPERADORA_SAUDE') or
			    ($tipoEmpresaAlianca=='OPERADORA_ODONTOLOGIA') or
			    ($tipoEmpresaAlianca=='OPERADORA_MISTA') or
			    ($tipoEmpresaAlianca=='AUTOGESTAO_ODONTO') or
			    ($tipoEmpresaAlianca=='AUTOGESTAO_SAUDE') or
			    ($tipoEmpresaAlianca=='AUTOGESTAO_MISTA'))
			{

				$diaAdmissao = day($rowBeneficiarios->DATA_ADMISSAO_PS1000BENEF);
				$valorBase   = $valorConvenio + $valorCorrecaoConvenio;

				$sqlEdicao   = '';
				$sqlEdicao 	.= linhaJsonEdicao('Codigo_Associado', $rowBeneficiarios->CODIGO_ASSOCIADO);
				$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020',$numeroRegistroPs1020);
				$sqlEdicao 	.= linhaJsonEdicao('Valor_Base',$valorBase,'N');
				$sqlEdicao 	.= linhaJsonEdicao('Valor_Adicional',$valorAdicional,'N');
				$sqlEdicao 	.= linhaJsonEdicao('Dia_Admissao',$diaAdmissao,'N');

			    if (strZero(month($dataEmissao),2) == copyDelphi($mesAnoReferencia,1,2)) // então não é faturamento antecipado
			       $sqlEdicao 	.= linhaJsonEdicao('Valor_Faturamento_Antecip',0,'N');
			    else
			       $sqlEdicao 	.= linhaJsonEdicao('Valor_Faturamento_Antecip',$valorConvenio + $valorCorrecaoConvenio,'N');

			    $fatorDia       	= (100 / 30);
			    $percentualMes  	= $fatorDia * ((30 - $diaAdmissao) + 1);
			    $percentualPPNG 	= 100 - $percentualMes;
			    $valorReceitaCobMes = (($valorBase * $percentualMes) / 100);
			    $valorReceitaPPNG   = (($valorBase * $percentualPPNG) / 100);

				$sqlEdicao 	.= linhaJsonEdicao('Valor_Receita_Cob_Mes',$valorReceitaCobMes,'N');
				$sqlEdicao 	.= linhaJsonEdicao('Percentual_Cob_Mes',$percentualMes,'N');
				$sqlEdicao 	.= linhaJsonEdicao('Percentual_PPNG',$percentualPPNG,'N');

				if (($valorReceitaCobMes + $valorReceitaPPNG) <> $valorBase)
				{
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Receita_PPNG',$valorReceitaPPNG + ($valorBase - ($valorReceitaCobMes + $valorReceitaPPNG)),'N');
				}
				else
				{
					$sqlEdicao 	.= linhaJsonEdicao('Valor_Receita_PPNG',$valorReceitaPPNG,'N');
				}

				gravaEdicao('PS1068', $sqlEdicao, 'I');

			}

			/* -----------------------------------------------------------------------------------------------------------
			*	GRAVO OS DADOS DA PS1083, VALORES POR PLANO PARA O BENEFICIARIO POSICIONADO
			*   MAIS ABAIXO QUANDO CALCULO OS VALORES ADICIONAIS, EU INCLUO OS VALORES ADICIONAIS NA PS1083
			* ----------------------------------------------------------------------------------------------------------- */

			$diaAdmissao = day($rowBeneficiarios->DATA_ADMISSAO_PS1000BENEF);
			$valorBase   = $valorConvenio + $valorCorrecaoConvenio;

			$sqlEdicao   = '';
			$sqlEdicao 	.= linhaJsonEdicao('Codigo_Associado', $rowBeneficiarios->CODIGO_ASSOCIADO);
			$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020',$numeroRegistroPs1020);
			$sqlEdicao 	.= linhaJsonEdicao('Codigo_Plano', $rowBeneficiarios->CODIGO_PLANO);
			$sqlEdicao 	.= linhaJsonEdicao('Valor_Plano_Evento',$valorConvenio + $valorCorrecaoConvenio,'N');

			gravaEdicao('PS1083', $sqlEdicao, 'I');

			//

			registraLogFaturaCalculada('BENEF',$rowBeneficiarios,$dataVencimento,$valorConvenio,$valorCorrecaoConvenio,$valorAdicional,
				                       '',$numeroRegistroPs1020);

			$pessoasCalculadas++;

			$familiaValorConvenio += $valorConvenio;
			$familiaValorCorrecao += $valorCorrecaoConvenio;
			$familiaValorAdicional+= $valorAdicional;
					             
			$contratoValorConvenio += $valorConvenio;
			$contratoValorCorrecao += $valorCorrecaoConvenio;
			$contratoValorAdicional+= $valorAdicional;

			$totalValorConvenio    += $valorConvenio;
			$totalValorCorrecao    += $valorCorrecaoConvenio;
			$totalValorAdicional   += $valorAdicional;

			$registroCalculado     = $rowBeneficiarios;

		}

		// 

		if ((($familiaValorConvenio+$familiaValorCorrecao+$familiaValorAdicional)>0))
		{
			registraLogFaturaCalculada('TOTFAM',$registroCalculado,null,$familiaValorConvenio,$familiaValorCorrecao,$familiaValorAdicional,
				                       'OPERACAO: '. $tipoEdicao,$numeroRegistroPs1020);

			$familiaValorConvenio = 0;
			$familiaValorCorrecao = 0;
			$familiaValorAdicional= 0;
		}

		if (($contratoValorConvenio+$contratoValorCorrecao+$contratoValorAdicional)>0)
		{
			if ($_POST['TIPOS_EMPRESAS']=='PJ')
			{
				registraLogFaturaCalculada('TOTEMP',$registroCalculado,null,$contratoValorConvenio,$contratoValorCorrecao,$contratoValorAdicional,
					                       'OPERACAO: '. $tipoEdicao,$numeroRegistroPs1020);
				$contratoValorConvenio = 0;
				$contratoValorCorrecao = 0;
				$contratoValorAdicional= 0;
			}
		}

		$detalheConclusao = '';
		$detalheConclusao = 'Finalizado, foram geradas: ' . $faturasGeradas . ' faturas ref a: ' . $pessoasCalculadas . ' beneficiarios de um total de: ' . $totalRegistrosAProcessar;

		registraLogFaturaCalculada('OBS',$rowBeneficiarios,null,0,0,0,$detalheConclusao,0);

		/*$queryRelatorioProcessamento = 'Select Coalesce(Ps1020.Codigo_Associado, Cast(Ps1010.Codigo_Empresa as varchar(15))) CODIGO_PAGADOR, 
											   Coalesce(Ps1000.Nome_Associado,Ps1010.Nome_Empresa) Nome_Pagador,  
										       Ps1020.Valor_Convenio, Ps1020.Valor_Correcao,
		                                       Ps1020.Valor_Adicional, Ps1020.Valor_Outros, Ps1020.Valor_Fatura, Ps1020.Data_Emissao, Ps1020.Data_Vencimento, 
		                                       Ps1020.Descricao_Observacao   
		                                       From Ps1020
								               Left Outer Join Ps1010 On (Coalesce(Ps1020.Codigo_Empresa, 400) = Ps1010.Codigo_Empresa) 
								               Left Outer join Ps1000 On (Ps1020.Codigo_Associado = Ps1000.Codigo_Associado)
		                                       Where Ps1020.ID_INSTANCIA_PROCESSO = ' . aspas($_POST['ID_PROCESSO']);

		$nomearquivoRelatorioProcesso = geraRelatorioAutomaticoProcessamento($_POST['ID_PROCESSO'],$queryRelatorioProcessamento);*/

		$nomearquivoRelatorioProcesso = $nomearquivoLogProcesso;

		registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',$detalheConclusao,$nomeArquivoGeradoProcesso,$nomearquivoLogProcesso, $nomearquivoRelatorioProcesso);
		escreveArquivoLogProcesso('F','');


}



/* ----------------------------------------------------------------------------------------------------------------------------------------- */

/* FUNCÕES PARA TRATAMENTO DAS INFORMACOES */

/* ----------------------------------------------------------------------------------------------------------------------------------------- */


function selecionaBeneficiariosCalcularFaturamento($criterioEspecifico='')
{

	global $totalRegistrosAProcessar;

	$queryPs1000 = 'Select ';

	//$queryPs1000 .= ' top 200000 ';

	$queryPs1000 .= '
					PS1010.CODIGO_SITUACAO_ATENDIMENTO SITUACAO_ATENDIMENTO_PS1010, PS1010.DATA_ADMISSAO DATA_ADMISSAO_PS1010, 
					PS1010.DATA_EXCLUSAO DATA_EXCLUSAO_PS1010, PS1010.FLAG_EMITE_BOLETO, PS1010.FLAG_PERM_VARIOS_REAJ_ANUAIS, 
					PS1010.FLAG_DESCONTA_ISS, PS1010.FLAG_DESCONTA_PISCOFINSCSLL, PS1010.FLAG_ISENTO_PAGTO, PS1010.NOME_EMPRESA, 

					PS1000BENEF.CODIGO_ASSOCIADO, PS1000BENEF.NOME_ASSOCIADO, PS1000BENEF.CODIGO_TABELA_PRECO, PS1000BENEF.CODIGO_EMPRESA, 
					PS1000BENEF.CODIGO_PLANO, PS1000BENEF.DATA_NASCIMENTO, PS1000BENEF.FATOR_CALCULO,  PS1000BENEF.DATA_ADMISSAO DATA_ADMISSAO_PS1000BENEF, 
					PS1000BENEF.DATA_EXCLUSAO DATA_EXCLUSAO_PS1000BENEF, PS1000BENEF.VALOR_NOMINAL, 
					PS1000BENEF.FLAG_ISENTO_PAGTO, PS1000BENEF.TIPO_ASSOCIADO, PS1000BENEF.FLAG_PLANOFAMILIAR FLAG_PLANOFAMILIAR_PS1000BENEF, 
					PS1000BENEF.NUMERO_DEPENDENTE, PS1000BENEF.CODIGO_TITULAR,
 					PS1000BENEF.CODIGO_GRUPO_FATURAMENTO, PS1000BENEF.CODIGO_ASSOCIADO_REG_PRINC, 
					PS1000BENEF.FLAG_ISENTO_PAGTO, PS1000BENEF.CODIGO_SEQUENCIAL, PS1000BENEF.CODIGO_SITUACAO_ATENDIMENTO, 

 					PS1002.FLAG_MUDA_FAIXA_PERCENTUAL, PS1002.DATA_PRIMEIRO_FATURAMENTO, PS1002.DIA_VENCIMENTO,  
 					PS1002.FATOR_TAXA_CALCULO, PS1002.DATA_LIMITE_TAXA, PS1002.FLAG_MUDA_FAIXA_PERCENTUAL,  PS1002.DATA_LIMITE_TAXA, 
 					PS1002.DATA_PRIMEIRO_FATURAMENTO, PS1002.DIA_VENCIMENTO, PS1002.FLAG_COBRA_FAMILIA, PS1002.FATOR_TAXA_CALCULO, 
 					PS1002.DATA_LIMITE_TAXA, PS1002.CODIGO_BANCO, PS1002.MESES_REALIZACAO_FATURAM, 

 					PS1041.FLAG_TRAVA_FATURAMENTO, PS1045.TIPO_RELACAO_DEPENDENCIA, 
 					PS1051.DATA_ADMIN_CONSID_REAJ, PS1014.DATA_ADMIN_CONSID_REAJ, 

 					PS1000TIT.DATA_ADMISSAO DATA_ADMISSAO_TITULAR, PS1061.NUMERO_REGISTRO NUMERO_REGISTRO_PS1061, 
 					PS1000BENEF.NOME_ASSOCIADO NOME_PAGADOR ';

					/*if( $_SESSION['type_db'] == 'firebird' )
					{
						$queryPs1000 .=	','; // COLOCAR AQUI O CALCULO NO FIREBIRD
					}
					else
					{
						$queryPs1000 .=	', Cast(FLOOR(DATEDIFF(DAY, PS1000BENEF.DATA_NASCIMENTO, GETDATE()) / 365.25) as varchar(7)) as IDADE_BENEFICIARIO ';
					}*/

					if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA') 
						$queryPs1000 .=	', PS1000BENEF.VALOR_NOMINAL_NET ';

	$queryPs1000 .=	'From Ps1010
  					 inner join Ps1000 PS1000BENEF on (Ps1010.Codigo_Empresa = Ps1000BENEF.Codigo_Empresa)
 					 inner join Ps1000 PS1000TIT on (Ps1000BENEF.CODIGO_TITULAR = Ps1000TIT.CODIGO_ASSOCIADO) ';

   	if ($_POST['TIPOS_EMPRESAS']=='PF') 
   	{
		$queryPs1000 .=	' left outer join PS1002 ON (PS1000BENEF.CODIGO_TITULAR = PS1002.CODIGO_ASSOCIADO) ';					 
	}
	else				 
   	{
		$queryPs1000 .=	' left outer join PS1002 ON (PS1010.CODIGO_EMPRESA = PS1002.CODIGO_EMPRESA) ';					 
	}

	$queryPs1000 .=	'Left Outer Join Ps1041 On (PS1000BENEF.Codigo_Situacao_Atendimento = Ps1041.Codigo_Situacao_Atendimento) 
                     Left Outer Join Ps1045 On (PS1000BENEF.Codigo_Parentesco = Ps1045.Codigo_Parentesco) 
                     Left Outer Join Ps1051 On (PS1000BENEF.Codigo_Grupo_Faturamento = Ps1051.Codigo_Grupo_Faturamento) 
                     Left Outer Join Ps1014 On (PS1000BENEF.Codigo_Grupo_Pessoas = Ps1014.Codigo_Grupo_Pessoas) 
                     Left Outer Join Ps1061 On (PS1000BENEF.Codigo_Associado = Ps1061.Codigo_Associado) ';
   	


	$criterioPs1000 = 'WHERE (PS1010.CODIGO_EMPRESA >= 1) '; 

	if ($criterioEspecifico!='')
	{
		$criterioPs1000 .= $criterioEspecifico;
	}
	else
	{

			//$criterioPs1000 .= ' and (PS1000TIT.CODIGO_ASSOCIADO in ("014002413909000","014002442029000","004007879060000","014002441351000","014002423286000","014002423772000")) ';
			//$criterioPs1000 .= ' and (PS1010.CODIGO_EMPRESA >= 396 AND PS1010.CODIGO_EMPRESA <= 487) ';

		    $criterioPs1000 .= ' and (coalesce(PS1000BENEF.FLAG_ISENTO_PAGTO,"N") = ' . aspas('N') . ')';

			// Filtros Ps1010

		   	if ($_POST['ASSISTENTE_FILTRO_PS1010']!='') 
		       $criterioPs1000 .= ' and ' . $_POST['ASSISTENTE_FILTRO_PS1010'] . ' ';

			// Filtros Ps1000

		   	if ($_POST['TIPOS_EMPRESAS']=='PF') // PF-Beneficiários PF (PLANOS FAMILIARES PESSOA FÍSICA);
		       $criterioPs1000 .= ' and (PS1000TIT.FLAG_PLANOFAMILIAR = ' . aspas('S') . ')';

		   	if (($_POST['TIPOS_EMPRESAS']=='PJ') or //PJ-Empresas PJ (PLANOS EMPRESARIAIS);
		   	    ($_POST['TIPOS_EMPRESAS']=='BE'))   //BE-Beneficiários empresariais (Fatur. individualizado de vl. adicionais) 
		       $criterioPs1000 .= ' and (PS1000TIT.FLAG_PLANOFAMILIAR = ' . aspas('N') . ')';

		   	if ($_POST['AUTOCOMP_CODIGO_GRUPO_CONTRATO']!='') 
		       $criterioPs1000 .= ' and (PS1000TIT.Codigo_Grupo_Contrato = ' . aspas($_POST['AUTOCOMP_CODIGO_GRUPO_CONTRATO']) . ') ';

		   	if ($_POST['AUTOCOMP_CODIGO_GRUPO_PESSOAS']!='') 
		       $criterioPs1000 .= ' and (PS1000TIT.Codigo_Grupo_pessoas = ' . aspas($_POST['AUTOCOMP_CODIGO_GRUPO_PESSOAS']) . ') ';

		   	if ($_POST['AUTOCOMP_CODIGO_GRUPO_FATURAMENTO']!='') 
		       $criterioPs1000 .= ' and (PS1000TIT.Codigo_Grupo_faturamento = ' . aspas($_POST['AUTOCOMP_CODIGO_GRUPO_FATURAMENTO']) . ') ';

		   	if ($_POST['LISTA_CODIGOS_BENEFICIARIOS']!='') 
		       $criterioPs1000 .= ' and (PS1000BENEF.CODIGO_TITULAR IN (' . $_POST['LISTA_CODIGOS_BENEFICIARIOS'] . ')) ';

		   	if ($_POST['LISTA_CODIGOS_EMPRESAS']!='') 
		       $criterioPs1000 .= ' and (PS1010.CODIGO_EMPRESA IN (' . $_POST['LISTA_CODIGOS_EMPRESAS'] . ')) ';

		    $criterioPs1000 .= ' 
		                       ';

		   	if ($_POST['FLAG_PERMITIR_EXCLUIDOS']!='N') 
		       $criterioPs1000 .= ' and ((PS1000BENEF.DATA_EXCLUSAO IS NULL) OR (PS1000BENEF.DATA_EXCLUSAO > ' . dataAngularToSql($_POST['DATA_COMPETENCIA']) . ')) ';

		    $criterioPs1000 .= ' 
		                       ';

		   	if ($_POST['ASSISTENTE_FILTRO_PS1000']!='') 
		   	{
		   	   $_POST['ASSISTENTE_FILTRO_PS1000'] = str_replace('PS1000.','PS1000TIT.',$_POST['ASSISTENTE_FILTRO_PS1000']);
		       $criterioPs1000 .= ' AND ' . $_POST['ASSISTENTE_FILTRO_PS1000'];
		   	}


		    $criterioPs1000 .= ' 
		                       ';
		    $criterioPs1000 .= ' and (PS1000BENEF.DATA_ADMISSAO <= ' . dataAngularToSql($_POST['DATA_COMPETENCIA']) . ') ';


			// Filtros Ps1002

		    $criterioPs1000 .= ' 
		                       ';
		    $criterioPs1000 .= ' and (Ps1002.DIA_VENCIMENTO BETWEEN ' . aspas($_POST['DIA_VENCIMENTO_INICIAL']) . ' and ' . aspas($_POST['DIA_VENCIMENTO_FINAL']) . ') ';


		    // Ordem
		    $criterioPs1000 .= ' 
		                       ';

	}		                       
    
    $ordemPs1000 	 = ' Order By PS1010.CODIGO_EMPRESA, PS1000TIT.CODIGO_TITULAR, PS1000BENEF.CODIGO_ASSOCIADO';

    // Executa querys

	$totalRegistrosAProcessar = totalRegistrosResult($queryPs1000 . $criterioPs1000);

	prDebug('Total de registros: ' . $totalRegistrosAProcessar);

    return jn_query($queryPs1000 . $criterioPs1000 . $ordemPs1000);
	
}





function calculaValorConvenio($rowBenef,$dataVencimento)
{

    if (($_POST['TIPO_CALCULO_FATURAMENTO'] == '3') or // 3-Apenas valores adicionais, ignorando valores de mensalidades dos planos;
        ($_POST['TIPO_CALCULO_FATURAMENTO'] == '4'))   // 4-Faturamento Empresial apenas de coparticipações e programações de eventos adicionais;
    {
		$arrayValor = array();

		$arrayValor['CONVENIO'] = 0;
		$arrayValor['NET'] 	    = 0;

		return $arrayValor;
    }

    $idadeNaAdmissao      = year($rowBenef->DATA_ADMISSAO_PS1000BENEF) - year($rowBenef->DATA_NASCIMENTO);
    $vencimentoReferencia = $dataVencimento; // dataToSql(date("d/m/Y"));

    if (month($rowBenef->DATA_ADMISSAO_PS1000BENEF) < month($rowBenef->DATA_NASCIMENTO))
       $idadeNaAdmissao = $idadeNaAdmissao -1;

	if (retornaValorConfiguracao('FLAG_MUDA_FAIXA_PERCENTUAL')=='S')
	{
        $idadeValidacao    = year($rowBenef->DATA_ADMISSAO_PS1000BENEF) - year($rowBenef->DATA_NASCIMENTO);
 
        if (month($rowBenef->DATA_ADMISSAO_PS1000BENEF) < month($rowBenef->DATA_NASCIMENTO))
           $idadeValidacao = $idadeValidacao - 1;
	} 
	else
	{
        $idadeValidacao    = year($vencimentoReferencia) - year($rowBenef->DATA_NASCIMENTO);

        if (month($vencimentoReferencia) < month($rowBenef->DATA_NASCIMENTO))
           $idadeValidacao = $idadeValidacao - 1;
	}

	prDebug($rowBenef->NOME_ASSOCIADO . ' - DATA_ADMISSAO_PS1000BENEF ' . sqlToData($rowBenef->DATA_ADMISSAO_PS1000BENEF) . ' - DATA_NASCIMENTO ' . sqlToData($rowBenef->DATA_NASCIMENTO) . 
		                                ' - $idadeNaAdmissao ' . $idadeNaAdmissao . ' - $idadeValidacao ' . $idadeValidacao . ' - $vencimentoReferencia' . sqlToData($vencimentoReferencia));

	if ($rowBenef->VALOR_NOMINAL >= 1)
	{
		$valorConvenio  = $rowBenef->VALOR_NOMINAL;
		$valorNet       = 0;
	    $faixaUtilizada = 'VALOR_NOMINAL';
	}
	else
	{
		$queryPs1011    = 'Select VALOR_PLANO, IDADE_MINIMA, IDADE_MAXIMA ';

		if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA') 
			$queryPs1011 .=	', VALOR_NET ';

		$queryPs1011  .= ' from PS1011 where ';	
		$queryPs1011  .= ' CODIGO_EMPRESA = ' . aspas($rowBenef->CODIGO_EMPRESA) . ' and CODIGO_PLANO = ' . aspas($rowBenef->CODIGO_PLANO); 
		$queryPs1011  .= ' and CODIGO_TABELA_PRECO = ' . aspas($rowBenef->CODIGO_TABELA_PRECO); 
		$queryPs1011  .= ' and IDADE_MINIMA <= ' . aspas($idadeValidacao) . ' and IDADE_MAXIMA >= ' . aspas($idadeValidacao);
		$queryPs1011  .= ' and (coalesce(TIPO_RELACAO_DEPENDENCIA,' . aspas($rowBenef->TIPO_ASSOCIADO) . ') = ' . aspas($rowBenef->TIPO_ASSOCIADO) . 
		                       ' OR TIPO_RELACAO_DEPENDENCIA = ' . aspas('I') . ')'; // I = indiferente

		$resValorPs1011 = jn_query($queryPs1011);

	    if (!$rowPs1011 = jn_fetch_object($resValorPs1011))
	    {
			$queryPs1011    = 'Select VALOR_PLANO, IDADE_MINIMA, IDADE_MAXIMA, 0 VALOR_NET from PS1032 where ';	
			$queryPs1011  .= ' CODIGO_PLANO = ' . aspas($rowBenef->CODIGO_PLANO); 
			$queryPs1011  .= ' and CODIGO_TABELA_PRECO = ' . aspas($rowBenef->CODIGO_TABELA_PRECO); 
			$queryPs1011  .= ' and IDADE_MINIMA <= ' . aspas($idadeValidacao) . ' and IDADE_MAXIMA >= ' . aspas($idadeValidacao);
			$queryPs1011  .= ' and (coalesce(TIPO_RELACAO_DEPENDENCIA,' . aspas($rowBenef->TIPO_ASSOCIADO) . ') = ' . aspas($rowBenef->TIPO_ASSOCIADO) . 
			                       ' OR TIPO_RELACAO_DEPENDENCIA = ' . aspas('I') . ')'; // I = indiferente
			$resValorPs1011 = jn_query($queryPs1011);
	        $rowPs1011      = jn_fetch_object($resValorPs1011);
	    }

		$valorConvenio  = $rowPs1011->VALOR_PLANO;
		$valorNet       = $rowPs1011->VALOR_NET;
	    $faixaUtilizada = $rowPs1011->IDADE_MINIMA . '-' . $rowPs1011->IDADE_MAXIMA;
	}

	//

	if (retornaValorConfiguracao('FLAG_MUDA_FAIXA_PERCENTUAL')=='S')
	{

		$vencimentoReferencia = $dataVencimento; //getObjetoDate();
        $idadeValidacao       = year($vencimentoReferencia) - year($rowBenef->DATA_NASCIMENTO);

		prDebug('X1-$vencimentoReferencia' . sqlToData($vencimentoReferencia) . ' - $idadeValidacao ' . $idadeValidacao . ' $rowBenef->DATA_NASCIMENTO ' . sqlToData($rowBenef->DATA_NASCIMENTO));

        if (month($vencimentoReferencia) < month($rowBenef->DATA_NASCIMENTO))
           $idadeValidacao = $idadeValidacao - 1;

		prDebug('X2-$idadeValidacao' . $idadeValidacao);

		$queryPs1035    = 'Select PERCENTUAL_REAJUSTE, IDADE_MINIMA, IDADE_MAXIMA ';
		$queryPs1035   .= ' from PS1035 ';	// OS REGISTROS DA PS1032 SERÃO MIGRADOS PARA A PS1011
		$queryPs1035   .= ' where CODIGO_PLANO = ' . aspas($rowBenef->CODIGO_PLANO); 
		$queryPs1035   .= ' and CODIGO_TABELA_PRECO = ' . aspas($rowBenef->CODIGO_TABELA_PRECO); 
		$queryPs1035   .= ' and IDADE_MINIMA > ' . aspas($idadeNaAdmissao) . ' Order By Idade_Minima, Idade_Maxima';
		$resPs1035      = jn_query($queryPs1035);

		while ($rowPs1035      = jn_fetch_object($resPs1035))
		{

			if (($faixaUtilizada== $rowPs1035->IDADE_MINIMA . '-' . $rowPs1035->IDADE_MAXIMA) or 
				($rowPs1035->PERCENTUAL_REAJUSTE=='') or 
			    ($rowPs1035->PERCENTUAL_REAJUSTE=='0'))
			{
				continue;
			}

			if ($rowPs1035->IDADE_MINIMA > $idadeValidacao)
			{
				break;
			}

			prDebug('X3- $valorConvenio ' . $valorConvenio . ' - Percentual reajuste: ' . $rowPs1035->PERCENTUAL_REAJUSTE);

            $valorConvenio = $valorConvenio * (($rowPs1035->PERCENTUAL_REAJUSTE / 100) + 1);
            $valorNet      = $valorNet * (($rowPs1035->PERCENTUAL_REAJUSTE / 100) + 1);

		}
	}

	$arrayValor = array();

	$arrayValor['CONVENIO'] = $valorConvenio;
	$arrayValor['NET'] 	    = $valorNet;

	return $arrayValor;
    	
}




function calculaReajusteAnual($rowCalculo, $valorConvenio, $valorNet, $dataVencimento)
{

	prDebug('CORRECAO_VL_FATURA_DATA_GPFATURAMENTO->' . retornaValorConfiguracao('CORRECAO_VL_FATURA_DATA_GPFATURAMENTO'));
	prDebug('CORRECAO_VL_FATURA_DATA_GPPESSOAS->' . retornaValorConfiguracao('CORRECAO_VL_FATURA_DATA_GPPESSOAS'));
	prDebug('CORRECAO_VL_ADICION_PELA_DT_EVENTO->' . retornaValorConfiguracao('CORRECAO_VL_ADICION_PELA_DT_EVENTO'));
	prDebug('FLAG_CORRECAO_ANODEP_MESTIT->' . retornaValorConfiguracao('FLAG_CORRECAO_ANODEP_MESTIT'));
	prDebug('FORCAR_VALIDACAO_DT_REAJUSTE_CONTRATO->' . retornaValorConfiguracao('FORCAR_VALIDACAO_DT_REAJUSTE_CONTRATO'));
	
    if ((retornaValorConfiguracao('FLAG_CORRECAO_ADMEMP') == 'S') and ($rowCalculo->FLAG_PLANOFAMILIAR_PS1000BENEF!='S'))
    {
		$dataAdmissaoConsiderar = $rowCalculo->DATA_ADMISSAO_PS1010;
		prDebug('Reaj-A');
    }
    else if (retornaValorConfiguracao('FLAG_CORRECAO_ADMTIT') == 'S')
    {
		$dataAdmissaoConsiderar = $rowCalculo->DATA_ADMISSAO_TITULAR;
		prDebug('Reaj-B');
    }
    else if (retornaValorConfiguracao('CORRECAO_VL_FATURA_DATA_GPFATURAMENTO') == 'SIM')
    {
		$dataAdmissaoConsiderar = $rowCalculo->DATA_ADMISSAO_GPFATURAMENTO;
		prDebug('Reaj-1');
    }
    else if (retornaValorConfiguracao('CORRECAO_VL_FATURA_DATA_GPPESSOAS') == 'SIM')	
    {
		$dataAdmissaoConsiderar = $rowCalculo->DATA_ADMISSAO_GPESSOAS;
		prDebug('Reaj-2');
    }
    else if (retornaValorConfiguracao('CORRECAO_VL_ADICION_PELA_DT_EVENTO') == 'SIM')		
    {
		$dataAdmissaoConsiderar = $dataEvento;
		prDebug('Reaj-3');
    }
    else if ((retornaValorConfiguracao('FLAG_CORRECAO_ANODEP_MESTIT') == 'S') && ($rowCalculo->FLAG_PLANOFAMILIAR_PS1000BENEF == 'S'))			
    {
		$dataMontada = getMontaData(strzero(year($rowCalculo->DATA_ADMISSAO_PS1000BENEF),4) . '/' . strzero(month($rowCalculo->DATA_ADMISSAO_TITULAR),2) . '/' . strzero('01',2));    	
		$dataAdmissaoConsiderar = $dataMontada;
		prDebug('Reaj-4');
    }
    else if ($rowCalculo->DATA_ADMIN_CONSID_REAJUSTE != '')			
    {
		$dataMontada 			= '01/' . strzero(month($rowCalculo->DATA_ADMIN_CONSID_REAJUSTE),2) . '/' . strzero(year($rowCalculo->DATA_ADMIN_CONSID_REAJUSTE),4);
		$dataAdmissaoConsiderar = SqlToData($dataMontada);
		prDebug('Reaj-5');
    }
    else 
    {
		$dataAdmissaoConsiderar = $rowCalculo->DATA_ADMISSAO_PS1000BENEF;
		prDebug('Reaj-6');
		prDebug($dataAdmissaoConsiderar);
    }

	//

    if (retornaValorConfiguracao('FORCAR_VALIDACAO_DT_REAJUSTE_CONTRATO')== 'SIM')
    {
		$dataMontada 			= '01/' . strzero(month($rowCalculo->DATA_ADMIN_CONSID_REAJUSTE),2) . '/' . strzero(year($rowCalculo->DATA_ADMIN_CONSID_REAJUSTE),4);
		$dataAdmissaoConsiderar = SqlToData($dataMontada);
		prDebug('Reaj-7');
    }

	//

	$mesAnoReferencia = extraiMesAnoData($dataAdmissaoConsiderar);

	prDebug('MESANOREF' . $mesAnoReferencia);

	// Criei uma view VW_PS1026_NET onde os valores nulos de empresa, plano, grupos quando nulos são 31777
	// Assim posso pesquisar pelo valor do dado efetivo do campo do beneficiário ou deste valor "default" e uso o order by para que ele ordene 
	// os registros. Desta forma

	$queryPs1026  = 'Select PRIORIDADE, TABELA, MES_ANO_REFERENCIA, FATOR_CONVERSAO, TIPO_BENEFICIARIO, CODIGO_PLANO
	                 from VW_PS1026_NET where ';	

	if ($rowCalculo->FLAG_PERM_VARIOS_REAJ_ANUAIS!='S')
	{
		$queryPs1026 .= ' MES_ANO_REFERENCIA like ' . aspas(copyDelphi($mesAnoReferencia,1,3) . '%') . ' AND ';
		$flagCorrecaoEmpresarialIndiferenteAoMes = false;
	}
	else
	{
		$flagCorrecaoEmpresarialIndiferenteAoMes = true;
	}

	$queryPs1026  .= '((TIPO_BENEFICIARIO = ' . aspas($rowCalculo->TIPO_ASSOCIADO) . ') OR (TIPO_BENEFICIARIO = ' . aspas('A') . ')) AND ';
	$queryPs1026  .= '((CODIGO_EMPRESA = ' . aspasNull($rowCalculo->CODIGO_EMPRESA) . ') OR (CODIGO_EMPRESA = 31777)) AND ';
	$queryPs1026  .= '((CODIGO_GRUPO_FATURAMENTO = ' . aspasNull($rowCalculo->CODIGO_GRUPO_FATURAMENTO) . ') OR (CODIGO_GRUPO_FATURAMENTO = 31777)) AND ';
	$queryPs1026  .= '((CODIGO_GRUPO_PESSOAS = ' . aspasNull($rowCalculo->CODIGO_GRUPO_PESSOAS) . ') OR (CODIGO_GRUPO_PESSOAS = 31777)) AND ';
	$queryPs1026  .= '((CODIGO_PLANO = ' . aspasNull($rowCalculo->CODIGO_PLANO) . ') OR (CODIGO_PLANO = 31777)) ';
	$queryPs1026  .= 'ORDER BY PRIORIDADE, MES_ANO_REFERENCIA, TIPO_BENEFICIARIO, CODIGO_EMPRESA, CODIGO_PLANO, CODIGO_GRUPO_FATURAMENTO, CODIGO_GRUPO_PESSOAS';

    $resPs1026             = jn_query($queryPs1026);
    $tabelaFator           = '-';
    $valoresCorrecao       = array();

    $valoresCorrecao['CONVENIO'] = 0;
    $valoresCorrecao['NET']      = 0;

	while ($rowPs1026 = jn_fetch_object($resPs1026))
	{

		if (copyDelphi($rowPs1026->MES_ANO_REFERENCIA,4,4) <= year($dataAdmissaoConsiderar))
		{
			continue;
		}

		if (copyDelphi($rowPs1026->MES_ANO_REFERENCIA,4,4) > year($dataVencimento))
		{
			continue;
		}

		if ((copyDelphi($rowPs1026->MES_ANO_REFERENCIA,4,4) == year($dataVencimento)) and
		    (!$flagCorrecaoEmpresarialIndiferenteAoMes) and (copyDelphi($rowPs1026->MES_ANO_REFERENCIA,1,2) > month($dataVencimento)))
		{
			continue;
		}

		if ($tabelaFator == '-')
		{
			$tabelaFator = $rowPs1026->TABELA;	
		}
		else if (($tabelaFator != '-') && ($tabelaFator != $rowPs1026->TABELA))
		{
			break;
		}	

		if ($rowCalculo->NUMERO_REGISTRO_PS1061!='')
		{
			$rowTmp = qryUmRegistro('Select Ps1061.TIPO_PROCESSO From Ps1061 Where (Codigo_Associado = ' . aspas($rowCalculo->CODIGO_ASSOCIADO) . ') And ' .
                                          '(Data_Inicial <= ' . DataToSql(sqlToData(getMontaDataDMY('01/' . $rowPs1026->MES_ANO_REFERENCIA))) . ') And ' .
                                          '(Data_Final >= ' . DataToSql(sqlToData(getMontaDataDMY('28/' . $rowPs1026->MES_ANO_REFERENCIA))) . ') And ' .
                                          '(Tipo_Processo = ' . Aspas('ICP') . ') ');

			if ($rowTmp->TIPO_PROCESSO=='ICP')
			{
                  continue;
            }
        }

        $valorCorrecao     = $valorCorrecao + ($valorConvenio * ($rowPs1026->FATOR_CONVERSAO / 100));
        $percentual        = $rowPs1026->FATOR_CONVERSAO; // só gravo o último mesmo

        if (retornaValorConfiguracao('FLAG_CALC_CORR_EXPONEN') == 'S')
        {
            $valorConvenio     = $valorConvenio + $valorCorrecao;
        }
        else
        {
            $valorConvenio     = $valorConvenio + ($valorConvenio * ($rowPs1026->FATOR_CONVERSAO / 100));
        }

	}

	$valoresCorrecao['CONVENIO'] = $valorCorrecao;
	$valoresCorrecao['NET']      = $valorNet;
	
	return $valoresCorrecao;

}






function calculaValorAdicional($rowPs1000, $valorConvenio, $valorCorrecao, $fonteCalculo, $dataVencimento,$numeroRegistroPs1020){


    if ($_POST['TIPO_CALCULO_FATURAMENTO'] == '2') // Apenas mensalidade
    {
        return 0;
    }

	if ($_POST['FLAG_ADICIONAIS_CONV_CORRECAO']=='S') // Aplicar correção sobre o valor do convênio para calcular adicionais.
	   $valorConvenio   = $valorConvenio + $valorCorrecao;

    $valorAdicional     = 0;
    $qtPessoasContrato  = 0;
    $criterioAdicional  = '';

    if ($rowPs1000->FLAG_PLANOFAMILIAR_PS1000BENEF!= 'S')
    {
        if (strtoupper(retornaValorConfiguracao('FLAG_VALIDAR_OPC_FAT_INDIVID_ADIC_PJ','NAO','Validar Configuração do evento adicional se em caso de PJ deve-se cobrar em fatura individualizada.')) == 'SIM')
        {
            if ($fonteCalculo == 'CALC_FATURA_INDIV_VLADICIONAL_PJ') // Antiga validação do cd128
               $criterioAdicional  = ' AND PS1024.FLAG_FATURA_ADIC_PJPF_INDIVID = ' . aspas('S');
            else
               $criterioAdicional  = ' AND COALESCE(PS1024.FLAG_FATURA_ADIC_PJPF_INDIVID,"N") = ' . aspas('N');
        }
    }

    //

    $qrySubCalc  = 'SELECT PS1003.*, PS1024.FLAG_APLICA_CORRECAO_EVENTO FROM PS1003 
                    Inner join ps1024 on (ps1003.codigo_evento = ps1024.codigo_evento) 
                    Where (ps1003.Codigo_Associado = ' . Aspas($rowPs1000->CODIGO_ASSOCIADO) . ') And 
                          (ps1003.Data_Inicio_Cobranca <= ' . DataToSql(sqlToData($dataVencimento)) . ') and 
                          (ps1003.Data_Fim_Cobranca >= ' . DataToSql(sqlToData($dataVencimento)) . ') ' .
                          $criterioAdicional;

    $resSubCalc        = jn_query($qrySubCalc);

	while ($rowSubCalc = jn_fetch_object($resSubCalc))
	{
    
		if ($_POST['LISTA_CODIGOS_EVENTOS_NAO_GERAR']!='') 
		{
		 	if (strpos($rowSubCalc->CODIGO_EVENTO,$_POST['LISTA_CODIGOS_EVENTOS_NAO_GERAR'])!==false)
		 	    continue;
		}

		if ($_POST['LISTA_CODIGOS_EVENTOS_GERAR']!='') 
		{
		 	if (strpos($rowSubCalc->CODIGO_EVENTO,$_POST['LISTA_CODIGOS_EVENTOS_GERAR'])==false)
		 	    continue;
		}

		if ($_POST['FLAG_ADICIONAIS_MENSAIS_SE_EXCLUIDO']!='S') 
		{
            if ($rowPs1000->DATA_EXCLUSAO != '') 
            {
                continue;
            }
        }

        if ($rowSubCalc->FLAG_COBRA_DEPENDENTE == 'S')
        {
        	$rowTmp            = qryUmRegistro('Select Count(*) QUANTIDADE from ps1000 where CODIGO_TITULAR = ' . aspas($rowPs1000->CODIGO_TITULAR) . 
        		                                iif($_POST['FLAG_ADICIONAIS_MENSAIS_SE_EXCLUIDO']!='S',' and data_exclusao is null',''));
            $qtPessoasContrato = $rowTmp->QUANTIDADE;
        }
        else
           $qtPessoasContrato  = 1;

        if ($rowSubCalc->TIPO_CALCULO == 'V')
        {
           $valorDeste = ($rowSubCalc->VALOR_FATOR * $rowSubCalc->QUANTIDADE_EVENTOS * $qtPessoasContrato);
        }
        else
        {
           $valorDeste = (($valorConvenio * (($rowSubCalc->VALOR_FATOR / 100) * $rowSubCalc->QUANTIDADE_EVENTOS)) * $qtPessoasContrato);
        }

        if ((retornaValorConfiguracao('FLAG_APLICA_CORRECAO_VL_ADICIONAL') == 'SIM') And
            ($rowSubCalc->FLAG_APLICA_CORRECAO_EVENTO == 'S'))
        {
            $valorDeste = $valorDeste + calculaReajusteAnual($rowPs1000, $valorDeste, 0);
        }

        $valorAdicional = $valorAdicional + $valorDeste;

        //

		$sqlEdicao 	 = linhaJsonEdicao('Codigo_Associado', $rowBeneficiarios->CODIGO_ASSOCIADO);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020',$numeroRegistroPs1020);
		$sqlEdicao 	.= linhaJsonEdicao('Codigo_Evento', $rowBeneficiarios->CODIGO_EVENTO);
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Plano_Evento',$valorDeste,'N');

		gravaEdicao('PS1083', $sqlEdicao, 'I');
    }

    //

    $qrySubCalc  = 'Select PS1023.*, PS1024.FLAG_APLICA_CORRECAO_EVENTO, 
                             CASE 
                               WHEN PS1000.CODIGO_EMPRESA = 400 THEN PS1000.DATA_ADMISSAO 
                               ELSE PS1010.DATA_ADMISSAO 
                             END DATA_INICIO_COBRANCA 
                    FROM PS1023 
                    INNER JOIN PS1000 ON (PS1023.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)  
                    INNER JOIN PS1010 ON (PS1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA)  
                    INNER JOIN PS1024 ON (PS1023.CODIGO_EVENTO = PS1024.CODIGO_EVENTO) 
                    Where (Ps1023.Codigo_Associado = ' . aspas($rowPs1000->CODIGO_ASSOCIADO) . ') And 
                          (Ps1023.Mes_Ano_Vencimento = ' . aspas(retornaMesAno($dataVencimento)) . ') ' . 
                          $criterioAdicional;

    $resSubCalc        = jn_query($qrySubCalc);

	while ($rowSubCalc = jn_fetch_object($resSubCalc))
	{

		if ($_POST['LISTA_CODIGOS_EVENTOS_NAO_GERAR']!='') 
		{
		 	if (strpos($rowSubCalc->CODIGO_EVENTO,$_POST['LISTA_CODIGOS_EVENTOS_NAO_GERAR'])!==false)
		 	    continue;
		}

		if ($_POST['LISTA_CODIGOS_EVENTOS_GERAR']!='') 
		{
		 	if (strpos($rowSubCalc->CODIGO_EVENTO,$_POST['LISTA_CODIGOS_EVENTOS_GERAR'])==false)
		 	    continue;
		}

        $valorDeste         = $rowSubCalc->VALOR_EVENTO * $rowSubCalc->QUANTIDADE_EVENTOS;

        if ((retornaValorConfiguracao('FLAG_APLICA_CORRECAO_VL_ADICIONAL') == 'SIM') And
            ($rowSubCalc->FLAG_APLICA_CORRECAO_EVENTO == 'S'))
        {
            $valorDeste = $valorDeste + calculaReajusteAnual($rowPs1000, $valorDeste, 0);
        }

        $valorAdicional += $valorDeste;

        //

		$sqlEdicao 	 = linhaJsonEdicao('Codigo_Associado', $rowBeneficiarios->CODIGO_ASSOCIADO);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020',$numeroRegistroPs1020);
		$sqlEdicao 	.= linhaJsonEdicao('Codigo_Evento', $rowBeneficiarios->CODIGO_EVENTO);
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Plano_Evento',$valorDeste,'N');

		gravaEdicao('PS1083', $sqlEdicao, 'I');

    }

    return $valorAdicional;

}





function calculaFaturamentoProporcional($codigoAssociado,$dataVencimento,$valorConvenio, $dataAdmissao, $dataExclusao)
{

      if (($_POST['TIPO_CALCULO_FATURAMENTO'] == '3') or // 3-Apenas valores adicionais, ignorando valores de mensalidades dos planos;
          ($_POST['TIPO_CALCULO_FATURAMENTO'] == '4'))   // 4-Faturamento Empresial apenas de coparticipações e programações de eventos adicionais;
      {
		   return 0;
      }

      $rowTmp = qryUmRegistro('Select ps1021.NUMERO_REGISTRO From Ps1021 
		                         Inner Join Ps1020 On (ps1021.Numero_Registro_Ps1020 = Ps1020.Numero_Registro) 
		                         Where (Ps1021.Codigo_Associado = ' . aspas($codigoAssociado) . ') 
		                         And (Ps1020.Data_Vencimento < ' . DataToSql(sqlToData($dataVencimento)) . ') ');

      if ($rowTmp->NUMERO_REGISTRO>=1)
      {
      	 return $valorConvenio;
      }

      $dataAdmissaoValidar = strzero(day($dataAdmissao),2) . '/' . strzero($_POST['MES_VENCIMENTO'],2) . '/' . $_POST['ANO_VENCIMENTO'];

      $qtDiasProporcional  = $dataAdmissaoValidar - $dataAdmissao;

      if (($qtDiasProporcional >= 2) And
          ($qtDiasProporcional <> 29) And
          ($qtDiasProporcional <> 30) And
          ($qtDiasProporcional <> 31))
      {
          $valorCalculado       = ($valorConvenio) / 30;
          $valorCalculado       = $valorCalculado * $qtDiasProporcional;
	  }
      else if ($qtDiasProporcional <= -4) 
      {
          $valorCalculado       = ($valorConvenio) / 30;
          $valorCalculado       = $valorCalculado * $qtDiasProporcional;
          $valorCalculado       = $valorConvenio + $valorCalculado;
      }
      else
      {
          $valorCalculado       = $valorConvenio;
      }

      return $valorCalculado;

}





function registraLogFaturaCalculada($tipoLinha,$rowBeneficiarios,$dataVencimento,$valorConvenio,
	                                $valorCorrecaoConvenio,$valorAdicional,$textoObservacoes='',$numeroRegistroPs1020='')
{

	$linhaLogProcesso = '';

	if ($tipoLinha=='CABEC_EMP')
	{
		$linhaLogProcesso .= adicionaCampoLinhaLog($numeroRegistroPs1020,14);
        $linhaLogProcesso .= adicionaCampoLinhaLog($rowBeneficiarios->CODIGO_EMPRESA,15);
        $linhaLogProcesso .= adicionaCampoLinhaLog('EMPRESA: ',10);
		$linhaLogProcesso .= adicionaCampoLinhaLog(copyDelphi(jn_utf8_encode($rowBeneficiarios->NOME_EMPRESA),1,37) . ' ' . completaString('-',40,'-') . PHP_EOL,175);
	}
	else if (($tipoLinha=='BENEF') or ($tipoLinha=='OBS'))
	{
		$linhaLogProcesso .= adicionaCampoLinhaLog($numeroRegistroPs1020,14);

		if ($rowBeneficiarios->FLAG_PLANOFAMILIAR_PS1000BENEF=='S')
		{
	       $linhaLogProcesso .= adicionaCampoLinhaLog('PF',7);
		   $linhaLogProcesso .= adicionaCampoLinhaLog($rowBeneficiarios->CODIGO_ASSOCIADO,18);
		}
		else
		{
	       $linhaLogProcesso .= adicionaCampoLinhaLog($rowBeneficiarios->CODIGO_EMPRESA,7);
		   $linhaLogProcesso .= adicionaCampoLinhaLog('(' . $rowBeneficiarios->CODIGO_ASSOCIADO . ')',18);
		}

		$linhaLogProcesso .= adicionaCampoLinhaLog(copyDelphi($rowBeneficiarios->NOME_PAGADOR,1,37),40);
		$linhaLogProcesso .= adicionaCampoLinhaLog(sqlToData($dataVencimento),14);

  	    if ($tipoLinha=='OBS')
			$linhaLogProcesso .= $textoObservacoes . PHP_EOL;

	}
	else
	{
		if ($tipoLinha=='TOTFAM')
		{
		   $linhaLogProcesso .= adicionaCampoLinhaLog('',14);
		   $linhaLogProcesso .= adicionaCampoLinhaLog('',25);
		   $linhaLogProcesso .= adicionaCampoLinhaLog('Total da familia:',40);
		}
		else if ($tipoLinha=='TOTEMP') 
		{
		   $linhaLogProcesso .= adicionaCampoLinhaLog('',14);
		   $linhaLogProcesso .= adicionaCampoLinhaLog('',25);
		   $linhaLogProcesso .= adicionaCampoLinhaLog('Total da empresa:',40);
		}
		else
		{
		   $linhaLogProcesso .= adicionaCampoLinhaLog('',14);
		   $linhaLogProcesso .= adicionaCampoLinhaLog('',25);
		   $linhaLogProcesso .= adicionaCampoLinhaLog('Total geral:',40);
		}

		$linhaLogProcesso .= adicionaCampoLinhaLog('',14);
	}

	if (($tipoLinha!='OBS') and ($tipoLinha!='CABEC_EMP'))
	{
		$linhaLogProcesso .= adicionaCampoLinhaLog($valorConvenio+$valorCorrecaoConvenio+$valorAdicional,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($valorConvenio,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($valorCorrecaoConvenio,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($valorAdicional,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog(0,14);

		$linhaLogProcesso .= adicionaCampoLinhaLog($textoObservacoes,100);
	}

	registraLogEvolucaoProcesso($_POST['ID_PROCESSO'],$linhaLogProcesso);

	if ($tipoLinha=='TOTFAM')
		$linhaLogProcesso .= PHP_EOL;

	if ($tipoLinha=='TOTEMP')
		$linhaLogProcesso .= PHP_EOL . PHP_EOL;

	escreveArquivoLogProcesso('L',$linhaLogProcesso);

	if ((($tipoLinha=='TOTFAM') and 
	     (($_POST['TIPOS_EMPRESAS']=='PF') or //PF-Beneficiários PF (PLANOS FAMILIARES PESSOA FÍSICA);
	     ($_POST['TIPOS_EMPRESAS']=='BE'))) or //BE-Beneficiários empresariais (Fatur. individualizado de vl. adicionais)
	   ($tipoLinha=='TOTEMP'))
	{

		/* -----------------------------------------------------------------------------------------------------------
		*	NÃO VOU MAIS UTILIZAR A TABELA - PS1029
		*   NA NOVA VERSÃO VOU COLOCAR TODOS OS DADOS NA PS1083, OS DADOS SÃO MUITO PARECIDOS.
		* ----------------------------------------------------------------------------------------------------------- */

		/*$sqlEdicao  = '';
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020',$numeroRegistroPs1020);
		$sqlEdicao 	.= linhaJsonEdicao('Codigo_Plano', $rowBeneficiarios->CODIGO_PLANO);
		$sqlEdicao 	.= linhaJsonEdicao('Quantidade',1,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Total', $valorConvenio+$valorCorrecaoConvenio+$valorAdicional,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Observacoes_Adicionais', 'GERACAO VIA FATURAMENTO');

		gravaEdicao('PS1029', $sqlEdicao, 'I');*/

		/* -----------------------------------------------------------------------------------------------------------
		*	ATUALIZA O VALOR DA FATURA COM BASE EM TODO O CÁLCULO - UPDATE
		* ----------------------------------------------------------------------------------------------------------- */

		$sqlEdicao  = '';
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura', $valorConvenio+$valorCorrecaoConvenio+$valorAdicional,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Convenio', $valorConvenio,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Adicional', $valorAdicional,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Correcao', $valorCorrecaoConvenio,'N');

		gravaEdicao('PS1020', $sqlEdicao, 'A', ' NUMERO_REGISTRO = ' . $numeroRegistroPs1020);

	}

}



function excluiDetalhamentosEstornaFatura($numeroRegistroPs1020)
{

    jn_query('Delete From Ps1029 Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistroPs1020));
	jn_query('Delete From Ps1021 Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistroPs1020));
	jn_query('Delete From Ps1083 Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistroPs1020));
	jn_query('Delete From Ps1068 Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistroPs1020));
	jn_query('Update Ps1023 Set Numero_Registro_Ps1020 = Null Where Numero_Registro_Ps1020 = ' . aspas($numeroRegistroPs1020));

}






function calculaEstimativaFaturaSemRegistrar($codigoAssociado, $tipoCalculo, $tipoRetorno)
//	$codigoAssociado = Codigo do beneficiario ou do titular a ser calculado. Se for do titular o tipofor "C" (contrato seleciona titular e seus dependentes)
//  $tipoCalculo     = "C-contrato (titular e dependentes) ou I-individual (só o beneficiário passado)"
//  $tipoRetorno     = C-valor do convenio, A-valor adicional, F-total do faturamento
{

	if ($tipoCalculo=='C')
		$criterioAdicional = ' AND PS1000BENEF.CODIGO_TITULAR = ' . aspas($codigoAssociado);
	else
		$criterioAdicional = ' AND PS1000BENEF.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);

	pr('-------------------------------------------------------------------' . $criterioAdicional);

	$linhaLogProcesso 	      = '';
	$resBeneficiarios         = selecionaBeneficiariosCalcularFaturamento($criterioAdicional);
	$tipoEmpresaAlianca       = retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA');
	$familiaValorConvenio     = 0;
	$familiaValorCorrecao     = 0;
	$familiaValorAdicional    = 0;
    $dataVencimento           = getDataAtual();

	while ($rowBeneficiarios = jn_fetch_object($resBeneficiarios))
	{

		$valoresConvenio        = calculaValorConvenio($rowBeneficiarios,$dataVencimento);
		$valorConvenio          = $valoresConvenio['CONVENIO'];
		$valorNet               = $valoresConvenio['NET'];

    	if (($rowBeneficiarios->FATOR_TAXA_CALCULO!=0) && ($rowBeneficiarios->FATOR_TAXA_CALCULO!=''))
    	{
	        if (($rowBeneficiarios->DATA_LIMITE_TAXA!='') and ($rowBeneficiarios->DATA_LIMITE_TAXA >= $dataVencimento))
    	    {
				$valorConvenio          = $valorConvenio * $rowBeneficiarios->FATOR_TAXA_CALCULO;
				$valorNet               = $valorNet * $rowBeneficiarios->FATOR_TAXA_CALCULO;
			}
    	}

		$valoresCorrecao        = calculaReajusteAnual($rowBeneficiarios, $valorConvenio, $valorNet, $dataVencimento);
		$valorCorrecaoConvenio  = $valoresCorrecao['CONVENIO'];
		$valorCorrecaoNet       = $valoresCorrecao['NET'];

    	if (($rowBeneficiarios->FATOR_TAXA_CALCULO != '') && ($rowBeneficiarios->FATOR_TAXA_CALCULO != '0'))
    	{
			$valorConvenio         = $valorConvenio * $rowBeneficiarios->FATOR_TAXA_CALCULO;
			$valorNet              = $valorNet  * $rowBeneficiarios->FATOR_TAXA_CALCULO;
			$valorCorrecaoConvenio = $valorCorrecaoConvenio  * $rowBeneficiarios->FATOR_TAXA_CALCULO;
			$valorCorrecaoNet      = $valorCorrecaoNet  * $rowBeneficiarios->FATOR_TAXA_CALCULO;
    	}

		$valorAdicional         = calculaValorAdicional($rowBeneficiarios, $valorConvenio, $valorCorrecaoConvenio, 'FATURAMENTO_NORMAL', $dataVencimento,$numeroRegistroPs1020);

		$valorConvenio          = round($valorConvenio, 2);
		$valorNet               = round($valorNet, 2);
		$valorCorrecaoConvenio  = round($valorCorrecaoConvenio, 2);
		$valorCorrecaoNet       = round($valorCorrecaoNet, 2);
		$valorAdicional         = round($valorAdicional, 2);

		$familiaValorConvenio += $valorConvenio;
		$familiaValorCorrecao += $valorCorrecaoConvenio;
		$familiaValorAdicional+= $valorAdicional;
			             
	}

	if ($tipoRetorno == 'C') 
		return $familiaValorConvenio;
	else if ($tipoRetorno == 'A') 
		return $familiaValorAdicional;
	else if ($tipoRetorno == 'F') 
		return $familiaValorConvenio + $familiaValorCorrecao + $familiaValorAdicional;

}








function RetornaProximoMesAnoDisponivelFaturamento($codigoEmpresa='', $codigoAssociado='')
{

    $mesAno = extraiMesAnoData(dataHoje());

    if (($codigoEmpresa != '') and ($codigoEmpresa != '400'))
       $codigoAssociado = '';

    $tentativas = 0;

    while ($tentativas <= 24)
    {
	    $rowTemp = qryUmRegistro(' select numero_registro from ps1020 where ' . 
				                  iif($codigoAssociado != '',' codigo_associado = ' . aspas($codigoAssociado),' codigo_empresa = ' . numSql($codigoEmpresa)) . 
				                  ' and extract(month from data_vencimento) = ' . numSql(CopyDelphi($mesAno,1,2)) . 
				                  ' and extract(year from data_vencimento) = ' . numSql(CopyDelphi($mesAno,4,4)));

	    if ($rowTemp->NUMERO_REGISTRO=='')
	    	break;

	    $mesAno    = retornaProximoMesAno($mesAno);
	    $tentativas++;
	}

    return $mesAno;

}


?>

