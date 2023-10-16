<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../lib/sysutilsAlianca.php');


if($dadosInput['tipo'] =='faturasNegociacao')
{

	
	$rowTemp        = qryUmRegistro('Select DATA_NEGOCIACAO from ps1085 Where Codigo_Cobranca = ' . aspas($dadosInput['numeroNegociacao']));

	if ($rowTemp->DATA_NEGOCIACAO=='')
	{
		$joinPs1020 = ' Inner Join Ps1020 On (Ps1085.CODIGO_COBRANCA = Ps1020.codigo_cobranca_atraso) ';
	}
	else
	{
		$joinPs1020 = ' Inner Join Ps1020 On (Ps1085.Codigo_Cobranca = Ps1020.CODIGO_COBRANCA_NEGOCIADA) ';
	}

	$queryPs1020    = 'Select Coalesce(Ps1000.Codigo_Empresa,Ps1010.Codigo_Empresa) Codigo_Empresa, Ps1100.Nome_Usual, Ps1020.Valor_Fatura, 	
					    Ps1020.Data_Vencimento, Ps1020.Data_Pagamento, PS1020.MES_ANO_REFERENCIA, PS1020.DESCRICAO_OBSERVACAO, 
						Ps1020.Data_Emissao, Ps1020.Data_Cancelamento, Ps1020.Numero_Registro, Ps1085.DATA_NEGOCIACAO, 
						Ps1006_01.NUMERO_TELEFONE NUMERO_TELEFONE_01,	PS1006_02.NUMERO_TELEFONE NUMERO_TELEFONE_02,	PS1001.ENDERECO_EMAIL, 
						Case
						   When Ps1020.Codigo_Associado Is Not Null Then Ps1000.Nome_Associado 
						   Else Ps1010.Nome_Empresa 
						End Nome_Pagador
						From Ps1085 ' . 
						$joinPs1020 . 
					'	Left Outer Join Ps1100 On (Ps1085.Codigo_Identificacao = Ps1100.Codigo_Identificacao) 
						Left Outer Join Ps1000 On (Ps1085.Codigo_Associado = Ps1000.Codigo_Associado) 
						Left Outer Join Ps1010 On (Ps1085.Codigo_Empresa = Ps1010.Codigo_Empresa) 
                        Left Outer Join PS1006 PS1006_01 ON (PS1085.CODIGO_ASSOCIADO = PS1006_01.CODIGO_ASSOCIADO) AND (PS1006_01.INDICE_TELEFONE = 1) 
                        Left Outer Join PS1006 PS1006_02 ON (PS1085.CODIGO_ASSOCIADO = PS1006_02.CODIGO_ASSOCIADO) AND (PS1006_02.INDICE_TELEFONE = 2) 
                        Left Outer Join PS1001 ON (PS1085.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) 
						Where Ps1085.Codigo_Cobranca = ' . aspas($dadosInput['numeroNegociacao']) . '
						and Ps1020.Data_Cancelamento is null 
						Order By Ps1020.NUMERO_REGISTRO ';

	$resPs1020 = jn_query($queryPs1020);

	while ($rowPs1020 = jn_fetch_object($resPs1020))
	{

		$linha = Array();

		$linha['MARCADO'] 	 	         = 'S';
		$linha['DATA_EMISSAO'] 	         = SqlToData($rowPs1020->DATA_EMISSAO);
		$linha['DATA_VENCIMENTO'] 	     = SqlToData($rowPs1020->DATA_VENCIMENTO);
		$linha['QUANT_DIAS_INADIMP']     = calculaDiferencaDatas(dataHoje(), $rowPs1020->DATA_VENCIMENTO);
		$linha['VALOR_JUROS'] 		     = round($rowPs1020->VALOR_FATURA * (retornaValorConfiguracao('PERCENTUAL_MORA_DIARIA') * $linha['QUANT_DIAS_INADIMP']));
		$linha['VALOR_MULTA'] 		     = round($rowPs1020->VALOR_FATURA * retornaValorConfiguracao('PERCENTUAL_MULTA_PADRAO'));
		$linha['VALOR_FATURA'] 		     = round($rowPs1020->VALOR_FATURA);
		$linha['VALOR_TOTAL']		     = round($rowPs1020->VALOR_FATURA) + 
		                                   round($rowPs1020->VALOR_FATURA * (retornaValorConfiguracao('PERCENTUAL_MORA_DIARIA') * $linha['QUANT_DIAS_INADIMP'])) + 
		                                   round($rowPs1020->VALOR_FATURA * retornaValorConfiguracao('PERCENTUAL_MULTA_PADRAO'));
		$linha['MES_ANO_REFERENCIA']     = $rowPs1020->MES_ANO_REFERENCIA;
		$linha['DESCRICAO_OBSERVACAO']   = jn_utf8_encode($rowPs1020->DESCRICAO_OBSERVACAO);
		$linha['NOME_PAGADOR']           = jn_utf8_encode($rowPs1020->NOME_PAGADOR);
		$linha['NUMERO_REGISTRO']        = $rowPs1020->NUMERO_REGISTRO;
		$linha['DATA_HOJE']              = date('Y-m-d');
		$linha['PERCENTUAL_MORA_DIARIA'] = retornaValorConfiguracao('PERCENTUAL_MORA_DIARIA');
		$linha['PERCENTUAL_MULTA_PADRAO']= retornaValorConfiguracao('PERCENTUAL_MULTA_PADRAO');
		$linha['INFORMACOES_BENEFICIARIO']  = '<b>' . jn_utf8_encode($rowPs1020->NOME_PAGADOR) . '<br>' . 
											  'Telefones: ' . $rowPs1020->NUMERO_TELEFONE_01 . ', ' . $rowPs1020->NUMERO_TELEFONE_02 . '<br>' . 
											  'E-mail: ' . $rowPs1020->ENDERECO_EMAIL . '</b>';

		if ($rowPs1020->DATA_NEGOCIACAO!='')
		{
			$linha['DATA_NEGOCIACAO'] 	     = SqlToData($rowPs1020->DATA_NEGOCIACAO);
			$linha['BOLETO'] 	             = $linkBoleto;
		}
		else
		{
			$linha['DATA_NEGOCIACAO'] 	     = '';
			$linha['BOLETO'] 	             = '';
		}
			
		$retorno['GRID'][] = $linha;

	}

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='gerarFaturasNegociacao')
{

	//pr($dadosInput['numeroNegociacao']);
	//pr($dadosInput['parcelasGeradas']);

	$registrosSelecionados = $dadosInput['registrosSelecionados'];

	foreach ($dadosInput['parcelasGeradas'] as $parcelasGeradas)
	{

		$qryNegociacao = qryUmRegistro('SELECT PS1085.CODIGO_ASSOCIADO, PS1000.CODIGO_SEQUENCIAL FROM PS1085 
			                            INNER JOIN PS1000 ON (PS1085.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)
			                            WHERE PS1085.CODIGO_COBRANCA = ' . aspas($dadosInput['numeroNegociacao']));


		$numeroRegistroPs1020 = jn_gerasequencial('PS1020');

		$sqlEdicao   = '';
	    $sqlEdicao 	.= linhaJsonEdicao('Codigo_Associado', $qryNegociacao->CODIGO_ASSOCIADO);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro', $numeroRegistroPs1020);
		$sqlEdicao 	.= linhaJsonEdicao('Tipo_Registro', 'N');
		$sqlEdicao 	.= linhaJsonEdicao('Data_Emissao', dataHoje(),'D');
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Parcela', '0');
		$sqlEdicao 	.= linhaJsonEdicao('Informacoes_Geracao', 'NEGOCIACAO');
		$sqlEdicao 	.= linhaJsonEdicao('Mes_Ano_Referencia',extraiMesAnoData($parcelasGeradas['vencimento']));
		$sqlEdicao 	.= linhaJsonEdicao('Data_Vencimento', $parcelasGeradas['vencimento'],'D');
	    $sqlEdicao 	.= linhaJsonEdicao('Codigo_Sequencial', $qryNegociacao->CODIGO_SEQUENCIAL);
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura', $parcelasGeradas['valorParcela'],'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Convenio', 0,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Adicional', 0,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Correcao', 0,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Percentual_Correcao', 0,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Prorrata', 0,'N');
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura_Bruto', 0,'N');
		$sqlEdicao 	.= linhaJsonEdicao('CODIGO_COBRANCA_NEGOCIADA',$dadosInput['numeroNegociacao']);

		gravaEdicao('PS1020', $sqlEdicao, 'I');


		//

	    $qrySubCalc  = 'Select ps1021.codigo_associado,sum(Ps1021.valor_convenio) valor_beneficiario,Ps1021.codigo_titular,Ps1021.codigo_empresa from ps1021 
                        where ps1021.numero_registro_ps1020 in (' . $registrosSelecionados . ') 
                        group by ps1021.codigo_associado,Ps1021.codigo_titular,Ps1021.codigo_empresa';

	    $resSubCalc  = jn_query($qrySubCalc);

		while ($rowSubCalc = jn_fetch_object($resSubCalc))
		{
               $somatoriaTotais = 0;
               $registroAtual   = 0;
               $registroAtual   = $registroAtual + 1;

               $calculoValorBeneficiario1 =0;
               $calculoValorBeneficiario2 =0;

               $calculoValorBeneficiario1 = (($rowSubCalc->VALOR_BENEFICIARIO*100)/($dadosInput['valorTotalFaturas']));
               $calculoValorBeneficiario2 = ($parcelasGeradas['valorParcela'] * $calculoValorBeneficiario1)/100;

               $somatoriaTotais = $somatoriaTotais + $calculoValorBeneficiario2;

			   $sqlEdicao    = '';
			   $sqlEdicao 	.= linhaJsonEdicao('Codigo_Associado', $rowSubCalc->CODIGO_ASSOCIADO);
			   $sqlEdicao 	.= linhaJsonEdicao('Codigo_Titular', $rowSubCalc->CODIGO_TITULAR);
			   $sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020', $numeroRegistroPs1020);
			   $sqlEdicao 	.= linhaJsonEdicao('Data_Emissao', dataHoje(),'D');
			   $sqlEdicao 	.= linhaJsonEdicao('Numero_Parcela_Beneficiario', '0');
			   $sqlEdicao 	.= linhaJsonEdicao('Mes_Ano_Vencimento', extraiMesAnoData($parcelasGeradas['vencimento']));
			   $sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura', $calculoValorBeneficiario2,'N');
			   $sqlEdicao 	.= linhaJsonEdicao('Valor_Convenio', $calculoValorBeneficiario2,'N');
			   $sqlEdicao 	.= linhaJsonEdicao('Valor_Adicional', 0,'N');
			   $sqlEdicao 	.= linhaJsonEdicao('Valor_Correcao', 0,'N');

			gravaEdicao('PS1021', $sqlEdicao, 'I');
		}

		$linha = Array();
		$linha['NUMERO_REGISTRO_GERADO']  = $numeroRegistroPs1020;
		$retorno['GRID'][] = $linha;

	}

	jn_query('Update Ps1020 Set data_cancelamento = ' . dataToSql( date("d/m/Y")) . ' where numero_registro in (' . $registrosSelecionados . ')');
	jn_query('Update Ps1085 Set DATA_NEGOCIACAO = ' . dataToSql( date("d/m/Y")) . ' where CODIGO_COBRANCA = ' . $dadosInput['numeroNegociacao']);

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='cancelaFaturasNegociacao')
{


	$rowTemp = qryUmRegistro('Select data_pagamento From Ps1020 Where CODIGO_COBRANCA_NEGOCIADA = ' . $dadosInput['numeroNegociacao']);

	if ($rowTemp->DATA_PAGAMENTO!='')
	{
		$resultado  = 'A negociação não pode ser cancelada, porque já existem faturas pagas nesta negociação.';
	}
	else
	{
		jn_query('Delete From Ps1021 Where Ps1021.Numero_Registro_Ps1020 in (Select Ps1020.Numero_Registro From Ps1020 
			                                Where (Ps1020.Numero_Registro = Ps1021.Numero_Registro_ps1020) And 
			                                      (Ps1020.CODIGO_COBRANCA_NEGOCIADA = ' . $dadosInput['numeroNegociacao'] . '))');
		jn_query('Delete From Ps1020 Where CODIGO_COBRANCA_NEGOCIADA = ' . $dadosInput['numeroNegociacao']);
		jn_query('Update Ps1020 set data_cancelamento = null Where codigo_cobranca_atraso = ' . $dadosInput['numeroNegociacao']);

		jn_query('Update Ps1085 Set DATA_NEGOCIACAO = Null where CODIGO_COBRANCA = ' . $dadosInput['numeroNegociacao']);

		$resultado  = 'OK';
	}

	$retorno['MSG'] = $resultado;

	echo json_encode($retorno);

}








?>