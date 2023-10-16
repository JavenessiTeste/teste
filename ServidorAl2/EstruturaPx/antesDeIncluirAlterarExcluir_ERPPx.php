<?php
 
function antesDeIncluirAlterarExcluir_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,$parametroPrompt='')
{
	//$tipo = INC ALT EXC 
	//campos apenas para INC ALT
	//$retorno['STATUS'] = 'OK';
	//$retorno['STATUS'] = 'ERRO'; para processo
	//$retorno['MSG']    = ''; mensagem que ira aparecer operador quando der erro.
	//$retorno['MSG']    = jn_utf8_encode('usar esssa função quando tiver acentuação');
	//$campos[$i]['CAMPO']
	//$campos[$i]['VALOR']

	$retorno['STATUS'] = 'OK'; 

	/*pr($tipo);
	pr($tabela);
	pr($tabelaOrigem);
	pr($chave);
	pr($nomeChave);*/

	if ($tipo == 'ALT') 
	{


		if ($tabela=='PS1000')
		{
				$row = qryUmRegistro('Select Ps1010.Mensagem_Cad_Beneficiarios, Ps1010.Nome_Empresa, Ps1000.CODIGO_ASSOCIADO_REG_PRINC From Ps1010 
										Inner Join Ps1000 On (Ps1010.Codigo_Empresa = Ps1000.Codigo_Empresa)
										Where (Ps1000.Codigo_Associado = ' . aspas($chave) . ') ');

				$retorno['MSG']     = '';

				if ($row->MENSAGEM_CAD_BENEFICIARIOS!='')
					$retorno['MSG'] .= 'Atenção, ao cadastrar beneficiários da empresa : ' . $row->NOME_EMPRESA . 
										' você deve observar as seguintes orientações : <br>' . $row->MENSAGEM_CAD_BENEFICIARIOS . '<br>';

				if ($row->CODIGO_ASSOCIADO_REG_PRINC!='') 
				{
					$retorno['STATUS'] = 'ABRIR_CONFIRMACAO';
					$retorno['MSG']   .= 'Este registro é derivado do processo de cópia de beneficiários. 
											É recomendado que você faça a alteração pelo registro principal do beneficiário e não o registro derivado.';
				}
		}



		if ($tabela=='PS1020')
		{
				if (retornaValorConfiguracao('PERMITIR_MANUTENCAO_MANUAL_FATURA')=='NAO')
				{
					$retorno['STATUS'] = 'NAO_PERMITIR';
					$retorno['MSG']   .= 'Conforme configurações do sistema, a edição de dados de faturas não é permitida.';
				}
		}



		if ($tabela=='PS1020')
		{
				$qryTmp = qryUmRegistro('Select  Numero_Nota_Fiscal, Mes_Ano_Referencia, Data_Pagamento, Data_Emissao, Data_Vencimento from ps1020
											where numero_registro = ' . aspas($chave));

				if ((retornaValorConfiguracao('TRAVAR_EDICAO_FATURAS_COM_NF')=='SIM') and ($qryTmp->NUMERO_NOTA_FISCAL!=''))
				{
					$retorno['STATUS'] = 'NAO_PERMITIR';
					$retorno['MSG']   .= 'A Nota fiscal desta fatura já foi emitida, a fatura não pode ser alterada.';
				}	
				else if (retornaValorConfiguracao('TRAVAR_FATURAS_FECHADAS')=='SIM')
				{
					$qryTmp1 = qryUmRegistro('Select Mes_Ano_Referencia From Ps1067 Where Mes_Ano_Referencia = ' . aspas($qryTmp->MES_ANO_REFERENCIA)); 

					if ($qryTmp1->FLAG_TRAVAR_EDICAO=='S')
					{
						$retorno['STATUS'] = 'NAO_PERMITIR';
						$retorno['MSG']   .= 'A competência desta fatura já está fechada, esta fatura não pode ser alterada.';
					}
				}	
				else if ((retornaValorConfiguracao('TRAVAR_FATURAS_BAIXADAS')=='SIM') and ($qryTmp->DATA_PAGAMENTO!= ''))
				{
					$retorno['STATUS'] = 'NAO_PERMITIR';
					$retorno['MSG']   .= 'Esta fatura já foi baixada e conforme configuraçõe do sistema, não é permitir alterar faturas baixadas.';
				}
		}


		if ($tabela == 'PS6120')
		{
				$qryTmp = qryUmRegistro('Select  data_conclusao from ps6120 where numero_registro = ' . aspas($chave));

				if ($qryTmp->DATA_CONCLUSAO!='')
				{
					$retorno['STATUS'] = 'NAO_PERMITIR';
					$retorno['MSG']   .= 'Registro não pode ser alterado, já possui data de conclusão!';
				}
		}


		if ($tabela == 'PS6530')
		{
			$retorno['STATUS'] = 'NAO_PERMITIR';
			$retorno['MSG']   .= 'Não é possível alterar pareceres já emitidos.';
		}


		if ($tabela == 'PS7400')
		{
			$row = qryUmRegistro('Select Flag_Saldo From Ps7400 Where Numero_Registro = ' . aspas($chave) . ') ');

			if ($row->FLAG_SALDO == 'S')
			{
				$retorno['STATUS'] = 'NAO_PERMITIR';
				$retorno['MSG']   .= 'Este movimento já foi processado, não pode ser alterado.';
			}
		}


		if ($tabela == 'PS7510')
		{
			$row = qryUmRegistro('Select Codigo_Operador_Fechamento From Ps7500 Where (Numero_Registro_Caixa = ' . aspas($chave) . ') 
								And (Codigo_Operador_Fechamento Is Null)');

			if ($row->CODIGO_OPERADOR_FECHAMENTO!='')
			{
				$retorno['STATUS'] = 'NAO_PERMITIR';
				$retorno['MSG']   .= 'O registro deste caixa já está fechado, portanto, este movimento não poderá ser editado!';
			}
		}


		if ($tabela=='PS7210')
		{
			$qryTmp = qryUmRegistro('Select  Data_Pagamento from ps7210 where codigo_bordero = ' . aspas($chave));

			if ($qryTmp->DATA_PAGAMENTO!='')
			{
				$retorno['STATUS'] = 'NAO_PERMITIR';
				$retorno['MSG']   .= 'Este borderô não pode ser alterado (cancelado) porque já consta como baixado.';
			}

			$qryTmp = qryUmRegistro('Select  numero_registro from ps7201 where codigo_bordero = ' . aspas($chave));

			if ($qryTmp->NUMERO_REGISTRO!='')
			{
				$retorno['STATUS'] = 'NAO_PERMITIR';
				$retorno['MSG']   .= 'Este borderô não pode ser alterado (cancelado) porque possui contas relacionadas.';
			}
		}

	} // Fim, tipo Alt



	/* --------------------------------------------------------------------------------------------------------------------- */



	if ($tipo == 'EXC')
	{

		if ($tabela=='PS1020')
		{
				$qryTmp = qryUmRegistro('Select  Numero_Nota_Fiscal, Mes_Ano_Referencia, Data_Pagamento, Data_Emissao, Data_Vencimento from ps1020
											where numero_registro = ' . aspas($chave));

				if ((retornaValorConfiguracao('TRAVAR_EDICAO_FATURAS_COM_NF')=='SIM') and ($qryTmp->NUMERO_NOTA_FISCAL!=''))
				{
					$retorno['STATUS'] = 'NAO_PERMITIR';
					$retorno['MSG']   .= 'A Nota fiscal desta fatura já foi emitida, a fatura não pode ser excluída.';
				}	
				else if (retornaValorConfiguracao('TRAVAR_FATURAS_FECHADAS')=='SIM')
				{
					$qryTmp1 = qryUmRegistro('Select Mes_Ano_Referencia From Ps1067 Where Mes_Ano_Referencia = ' . aspas($qryTmp->MES_ANO_REFERENCIA)); 

					if ($qryTmp1->FLAG_TRAVAR_EDICAO=='S')
					{
						$retorno['STATUS'] = 'NAO_PERMITIR';
						$retorno['MSG']   .= 'A competência desta fatura já está fechada, esta fatura não pode ser alterada/excluída.';
					}
				}	
				else if ((retornaValorConfiguracao('TRAVAR_FATURAS_BAIXADAS')=='SIM') and ($qryTmp->DATA_PAGAMENTO!= ''))
				{
					$retorno['STATUS'] = 'NAO_PERMITIR';
					$retorno['MSG']   .= 'Esta fatura já foi baixada e conforme configuraçõe do sistema, não é permitir alterar/excluir faturas baixadas.';
				}
		}


		if ($tabela=='PS7210')
		{

			$qryTmp = qryUmRegistro('Select  Data_Pagamento from ps7210 where codigo_bordero = ' . aspas($chave));

			if ($qryTmp->DATA_PAGAMENTO!='')
			{
				$retorno['STATUS'] = 'NAO_PERMITIR';
				$retorno['MSG']   .= 'Este borderô não pode ser excluído (cancelado) porque já consta como baixado.';
			}

			$qryTmp = qryUmRegistro('Select  numero_registro from ps7201 where codigo_bordero = ' . aspas($chave));

			if ($qryTmp->NUMERO_REGISTRO!='')
			{
				$retorno['STATUS'] = 'NAO_PERMITIR';
				$retorno['MSG']   .= 'Este borderô não pode ser excluído (cancelado) porque possui contas relacionadas.';
			}

		}

	} // Fim tipo EXC


	return $retorno;

}	

?>