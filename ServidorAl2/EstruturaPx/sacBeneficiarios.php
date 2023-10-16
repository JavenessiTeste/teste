<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../lib/sysutilsAlianca.php');

//pr($dadosInput['tipo'] . 'aaaaa');
//pr($dadosInput);


if($dadosInput['tipo'] =='dadosGridBeneficiarios')
{
	
	if ($dadosInput['pesquisarApenasValoresExatos'] == 'SIM')
	{
		$tipoPesquisa = ' = ';
		$valorPesquisar = aspas($dadosInput['valorPesquisar']);
	}
	else
	{
		$tipoPesquisa   = ' Like ';
		$valorPesquisar = aspas('%' . $dadosInput['valorPesquisar'] . '%');
	}


	if ($dadosInput['campoPesquisar']=='NOME_EMPRESA')
		$dadosInput['campoPesquisar'] = 'PS1010.NOME_EMPRESA';
	else if ($dadosInput['campoPesquisar']=='NOME_PLANO')
		$dadosInput['campoPesquisar'] = 'PS1030.NOME_PLANO_FAMILIARES';
	else if ($dadosInput['campoPesquisar']=='NOME_TITULAR')
		$dadosInput['campoPesquisar'] = 'PS1000Tit.NOME_ASSOCIADO';
	else if ($dadosInput['campoPesquisar']=='CODIGO_TITULAR')
		$dadosInput['campoPesquisar'] = 'PS1000Tit.CODIGO_TITULAR';
	else if ($dadosInput['campoPesquisar']=='NOME_GRUPO_PESSOAS')
		$dadosInput['campoPesquisar'] = 'PS1014.NOME_GRUPO_PESSOAS';
	else if ($dadosInput['campoPesquisar']=='NOME_GRUPO_CONTRATO')
		$dadosInput['campoPesquisar'] = 'ESP0002.NOME_GRUPO_CONTRATO';
	else if ($dadosInput['campoPesquisar']=='CODIGO_GRUPO_CONTRATO')
		$dadosInput['campoPesquisar'] = 'ESP0002.CODIGO_GRUPO_CONTRATO';
	else if ($dadosInput['campoPesquisar']=='INFORMACOES_ADICIONAIS')
		$dadosInput['campoPesquisar'] = 'Ps1000Ben.CODIGO_ASSOCIADO'; // SÓ PARA NÃO DAR ERRO
	else if ($dadosInput['campoPesquisar']=='')
		$dadosInput['campoPesquisar'] = 'Ps1000Ben.CODIGO_ASSOCIADO'; // SÓ PARA NÃO DAR ERRO
	else
		$dadosInput['campoPesquisar'] = 'Ps1000Ben.' . $dadosInput['campoPesquisar'];


	$queryPs1020    = 'Select top ' . $dadosInput['quantidadeResultados'] . ' PS1000Ben.CODIGO_ASSOCIADO , 
	                          PS1000Ben.NOME_ASSOCIADO, PS1000Ben.DATA_ADMISSAO, PS1000Ben.DATA_EXCLUSAO, 
	                          PS1010.NOME_EMPRESA, PS1030.NOME_PLANO_FAMILIARES NOME_PLANO, PS1000Ben.TIPO_ASSOCIADO, PS1000TIT.NOME_ASSOCIADO NOME_TITULAR,
	                          PS1000Ben.CODIGO_TITULAR, PS1014.NOME_GRUPO_PESSOAS, PS1000Ben.CODIGO_EMPRESA, PS1030.CODIGO_PLANO, null INFORMACOES_ADICIONAIS ';

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$queryPs1020    .= ', ESP0002.CODIGO_GRUPO_CONTRATO, ESP0002.NOME_GRUPO_CONTRATO ';

	$queryPs1020    .= ' From Ps1000 Ps1000Ben
						 Inner Join Ps1010 On (Ps1000Ben.Codigo_Empresa = Ps1010.Codigo_Empresa) 
						 Inner Join Ps1030 On (Ps1000Ben.Codigo_Plano = Ps1030.Codigo_Plano) 
						 Left Outer Join Ps1000 Ps1000Tit On (Ps1000Ben.Codigo_Titular = Ps1000Tit.Codigo_Associado)
						 Left Outer Join Ps1014 on (Ps1000Ben.CODIGO_GRUPO_PESSOAS = Ps1014.Codigo_grupo_pessoas) '; 

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$queryPs1020    .= ' Left Outer Join Esp0002 On (Ps1000Ben.CODIGO_GRUPO_CONTRATO = esp0002.CODIGO_GRUPO_CONTRATO) ';

	if ($dadosInput['campoPesquisar']!='')
	    $queryPs1020    .= ' Where ' . $dadosInput['campoPesquisar'] . $tipoPesquisa . $valorPesquisar;

	if ($dadosInput['apresentarRegistrosAtivos']=='NAO')
		$queryPs1020    .= ' and Ps1000Ben.Data_Exclusao is not null';

	if ($dadosInput['apresentarRegistrosExcluidos']=='NAO')
		$queryPs1020    .= ' and Ps1000Ben.Data_Exclusao is null';

	if ($dadosInput['OrderBy']=='')
		$queryPs1020    .= ' Order By Ps1000Ben.Codigo_Associado';
	else
		$queryPs1020    .= ' Order By ' . $dadosInput['OrderBy'];

	$resPs1020 = jn_query($queryPs1020);

	while ($rowPs1020 = jn_fetch_object($resPs1020))
	{

		$linha = Array();

		$linha['MARCADO'] 	 	         = 'S';
		$linha['CODIGO_ASSOCIADO']       = jn_utf8_encode_AscII($rowPs1020->CODIGO_ASSOCIADO);
		$linha['NOME_ASSOCIADO']         = jn_utf8_encode_AscII($rowPs1020->NOME_ASSOCIADO);
		$linha['DATA_ADMISSAO'] 	     = SqlToData($rowPs1020->DATA_ADMISSAO);
		$linha['DATA_EXCLUSAO'] 	     = SqlToData($rowPs1020->DATA_EXCLUSAO);
		$linha['TIPO_ASSOCIADO']         = $rowPs1020->TIPO_ASSOCIADO;
		$linha['CODIGO_TITULAR']      	 = $rowPs1020->CODIGO_TITULAR;
		$linha['CODIGO_EMPRESA']         = $rowPs1020->CODIGO_EMPRESA;
		$linha['CODIGO_PLANO']           = $rowPs1020->MES_ANO_REFERENCIA;
		$linha['NOME_EMPRESA']           = jn_utf8_encode_AscII($rowPs1020->NOME_EMPRESA);
		$linha['NOME_PLANO']             = jn_utf8_encode_AscII($rowPs1020->NOME_PLANO);
		$linha['NOME_TITULAR']           = jn_utf8_encode_AscII($rowPs1020->NOME_TITULAR);
		$linha['NOME_GRUPO_PESSOAS']     = jn_utf8_encode_AscII($rowPs1020->NOME_GRUPO_PESSOAS);
		$linha['INFORMACOES_ADICIONAIS'] = jn_utf8_encode_AscII($rowPs1020->INFORMACOES_ADICIONAIS);

		$retorno[] = $linha;

	}

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='dadosBeneficiarioSelecionado')
{
	
	//$dadosInput['codigoTitular'] = '014000465107007';

	$query    = 'Select PS1000Ben.CODIGO_ASSOCIADO , PS1000Ben.NOME_ASSOCIADO,  Ps1000Ben.NOME_MAE, PS1000Ben.DATA_ADMISSAO, 
	                        PS1000Ben.DATA_EXCLUSAO, PS1000Ben.DATA_NASCIMENTO, 
	                          case 
	                              When PS1000Ben.TIPO_ASSOCIADO = "T" then "Titular"
	                              else "Dependente" 
	                          End Tipo_Associado, 
	                          PS1000Ben.SEXO SEXO_BENEFICIARIO, VW_FILTRO_BENEFICIARIOS_IDADE.IDADE IDADE_BENEFICIARIO, 
	                          Ps1006_1.numero_telefone Telefone_01, Ps1006_2.Numero_Telefone Telefone_02, 
	                          Coalesce(Ps1001.Endereco,Ps1015.Endereco) Endereco,  Coalesce(Ps1001.Bairro,Ps1015.Bairro) Bairro,  
	                          Coalesce(Ps1001.Cidade,Ps1015.Cidade) Cidade,  Coalesce(Ps1001.Estado,Ps1015.Estado) Estado,  
	                          Coalesce(Ps1001.Cep,Ps1015.Cep) Cep,  Coalesce(Ps1001.Endereco,Ps1015.Endereco) Endereco,  
	                          PS1000Ben.CODIGO_TITULAR, PS1000TIT.NOME_ASSOCIADO NOME_TITULAR,
	                          Ps1002.Numero_Contrato, Ps1002.Dia_Vencimento, Ps1002.NOME_CONTRATANTE, PS1002.NUMERO_CPF_CONTRATANTE, 
							  VW_RESUMO_BENEFICIARIO.Nome_empresa, Ps1000Ben.Codigo_plano, VW_RESUMO_BENEFICIARIO.Nome_plano, 
							  VW_RESUMO_BENEFICIARIO.Nome_tipo_caracteristica,      
							  VW_RESUMO_BENEFICIARIO.Admissao_empresa Data_Admissao_Empresa, VW_RESUMO_BENEFICIARIO.Nome_grupo_pessoas, 
							  null INFORMACOES_ADICIONAIS,
	                          Case 
	                              When Ps1000Ben.DATA_EXCLUSAO IS NULL THEN "Ativo"
	                              else "Excluido" 
	                          end SITUACAO_CADASTRAL,
							  VW_RESUMO_BENEFICIARIO.Nome_motivo_exclusao, VW_RESUMO_BENEFICIARIO.Descricao_situacao, 
							  VW_RESUMO_BENEFICIARIO.Quantidade_total_faturas, VW_RESUMO_BENEFICIARIO.Quantidade_faturas_em_aberto,
							  VW_RESUMO_BENEFICIARIO.Quantidade_faturas_pagas, VW_RESUMO_BENEFICIARIO.Valor_total_em_aberto,
							  VW_RESUMO_BENEFICIARIO.Valor_total_pago ';

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$query    .= ', ESP0002.CODIGO_GRUPO_CONTRATO, ESP0002.NOME_GRUPO_CONTRATO ';

	$query    .= ' From Ps1000 Ps1000Ben
						 Inner Join VW_RESUMO_BENEFICIARIO On (Ps1000Ben.Codigo_Associado = VW_RESUMO_BENEFICIARIO.Codigo_Associado)
						 Left Outer Join VW_FILTRO_BENEFICIARIOS_IDADE on (Ps1000Ben.Codigo_Associado = VW_FILTRO_BENEFICIARIOS_IDADE.Codigo_Associado)
						 Left Outer Join Ps1001 On (Ps1000Ben.Codigo_Titular = Ps1001.Codigo_Associado)
						 Left Outer Join Ps1002 On (Ps1000Ben.Codigo_Titular = Ps1002.Codigo_Associado)
						 Left Outer Join Ps1015 On (Ps1000Ben.Codigo_Titular = Ps1015.Codigo_Associado)
						 Left Outer Join Ps1006 Ps1006_1 On (Ps1000Ben.Codigo_Titular = Ps1006_1.Codigo_Associado) and (Ps1006_1.INDICE_TELEFONE = 1)
						 Left Outer Join Ps1006 Ps1006_2 On (Ps1000Ben.Codigo_Titular = Ps1006_2.Codigo_Associado) and (Ps1006_2.INDICE_TELEFONE = 2)
						 Left Outer Join Ps1000 Ps1000Tit On (Ps1000Ben.Codigo_Titular = Ps1000Tit.Codigo_Associado)'; 

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$query    .= ' Left Outer Join Esp0002 On (Ps1000Ben.CODIGO_GRUPO_CONTRATO = esp0002.CODIGO_GRUPO_CONTRATO) ';

	$query    .= ' where (Ps1000Ben.Codigo_Titular = ' . aspas($dadosInput['codigoTitular']) . ') Order By Ps1000Ben.Codigo_Associado ';

	$res = jn_query($query);

	$linha = Array();

	$titulos = array();
	$titulos[0] = 'Campo de informacao';
	$titulos[1] = 'Valor';

	$linha['INFORMACOES_BENEFICIARIO'] = iniciaTabelaVertical(2,$titulos);
	$linha['SITUACAO_CADASTRAL']       = iniciaTabelaVertical(2,$titulos);

	$titulos[0] = 'Tipo';
	$titulos[1] = 'Codigo do beneficiario';
	$titulos[2] = 'Nome do beneficiario';
	$titulos[3] = 'Nascimento';
	$titulos[4] = 'Idade';
	$titulos[5] = 'Admissao';
	$titulos[6] = 'Exclusao';
	$titulos[7] = 'Plano';
	$titulos[8] = 'Tabela';

	$linha['INFORMACOES_FAMILIA'] = iniciaTabelaVertical(9,$titulos);

	$qtCamposConsulta = jn_num_fields($res);

	//pr('$qtCamposConsulta' . $qtCamposConsulta);

	while ($row = jn_fetch_object($res))
	{

		if ($dadosInput['codigoAssociado']==$row->CODIGO_ASSOCIADO)
		{
		   $linha['INFORMACOES_BENEFICIARIO'] .= retornaValoresEmTabelaVertical($qtCamposConsulta,$res, $row);
		   $linha['SITUACAO_CADASTRAL']       .= '<tr class="alturaLinha corPar">
			                                            <td class="alturaLinha">Situacao cadastral</td> 
			                                            <td class="alturaLinha">' . jn_utf8_encode_AscII($row->SITUACAO_CADASTRAL) . '</td> 
			                                      </tr>
			                                      <tr class="alturaLinha corImpar">
			                                            <td class="alturaLinha">Motivo exclusao</td> 
			                                            <td class="alturaLinha">' . jn_utf8_encode_AscII($row->NOME_MOTIVO_EXCLUSAO) . '</td> 
			                                      </tr>
			                                      <tr class="alturaLinha corPar">
			                                            <td class="alturaLinha">Total faturas</td> 
			                                            <td class="alturaLinha">' . $row->QUANTIDADE_TOTAL_FATURAS . '</td> 
			                                      </tr>
			                                      <tr class="alturaLinha corImpar">
			                                            <td class="alturaLinha">Faturas em aberto</td> 
			                                            <td class="alturaLinha">' . $row->QUANTIDADE_FATURAS_EM_ABERTO . '</td> 
			                                      </tr>
			                                      <tr class="alturaLinha corPar">
			                                            <td class="alturaLinha">Faturas pagas</td> 
			                                            <td class="alturaLinha">' . $row->QUANTIDADE_FATURAS_PAGAS . '</td> 
			                                      </tr>
			                                      <tr class="alturaLinha corImpar">
			                                            <td class="alturaLinha">Total em aberto</td> 
			                                            <td class="alturaLinha">' . $row->VALOR_TOTAL_EM_ABERTO . '</td> 
			                                      </tr>
			                                      <tr class="alturaLinha corPar">
			                                            <td class="alturaLinha">Total pago</td> 
			                                            <td class="alturaLinha">' . $row->VALOR_TOTAL_PAGO . '</td> 
			                                      </tr>';

		}

		$linha['INFORMACOES_FAMILIA']      .= '<tr class="alturaLinha ' . $corLinha . '">
		                                            <td class="alturaLinha">' . jn_utf8_encode_AscII($row->TIPO_ASSOCIADO) . '</td> 
		                                            <td class="alturaLinha">' . $row->CODIGO_ASSOCIADO . '</td>
		                                            <td class="alturaLinha">' . jn_utf8_encode_AscII($row->NOME_ASSOCIADO) . '</td>
		                                            <td class="alturaLinha">' . SqlToData($row->DATA_NASCIMENTO) . '</td>
		                                            <td class="alturaLinha">' . $row->IDADE_BENEFICIARIO . '</td>
		                                            <td class="alturaLinha">' . SqlToData($row->DATA_ADMISSAO) . '</td>
		                                            <td class="alturaLinha">' . SqlToData($row->DATA_EXCLUSAO) . '</td>
		                                            <td class="alturaLinha">' . $row->CODIGO_PLANO . '</td>
		                                            <td class="alturaLinha">' . $row->CODIGO_TABELA_PRECO . '</td>
	                                            </tr>';

	}

	$linha['INFORMACOES_BENEFICIARIO'] .= finalizaTabelaVertical();
	$linha['INFORMACOES_FAMILIA']      .= finalizaTabelaVertical();
	$linha['SITUACAO_CADASTRAL']      .= finalizaTabelaVertical();


	/* ---------------------------------------------------------------- */

	$Benef['carencias'] = getCarencias($dadosInput['codigoAssociado'], null, 'B');

	//pr($Benef['carencias']);

	$titulos[0] = 'Descricao da carencia';
	$titulos[1] = 'Data';

	$linha['INFORMACOES_CARENCIA'] = iniciaTabelaVertical(2,$titulos);
	$corLinha = 'corImpar';

    foreach ($Benef['carencias'] as $item) 
    {

		$linha['INFORMACOES_CARENCIA'] .= '<tr class="alturaLinha ' . $corLinha . '"><td class="alturaLinha">' . jn_utf8_encode_AscII(strtolower($item->RESULTADO_DESCRICAO_GRUPO)) . 
		                                  '</td><td class="alturaLinha">' . SqlToData($item->RESULTADO_DATA_CARENCIA) . '</td></tr>';

        if ($corLinha=='corPar')
        	$corLinha = 'corImpar';
        else
        	$corLinha = 'corPar';
	}

	$linha['INFORMACOES_CARENCIA'] .= finalizaTabelaVertical();

	$retorno[] = $linha;

	//pr($retorno);

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_FATURAMENTO')
{

	$query  = 'Select Ps1020.Numero_Registro, Ps1020.Data_Emissao, Ps1020.Data_Vencimento, Ps1020.Valor_Fatura, Ps1020.Data_Pagamento, 
						Ps1020.Valor_Pago, Ps1020.Data_Cancelamento
				 from ps1020
				 where (Ps1020.Codigo_Associado = ' . aspas($dadosInput['codigoTitular']) . ') Order By Ps1020.Data_Vencimento Desc';

	$totalizar                        = Array();
	$totalizar['VALOR_FATURA']        = 0;
	$totalizar['VALOR_PAGO']          = 0;

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'',$totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta de faturamento';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_COM_JUROS')
{

	$Percentual_Mora_Diaria  = retornaValorConfiguracao('PERCENTUAL_MORA_DIARIA');
	$Percentual_Multa_Padrao = retornaValorConfiguracao('PERCENTUAL_MULTA_PADRAO');
	$dataHoje                = DataToSql(dataHoje_Date());

	$totalizar                          = Array();
	$totalizar['VALOR_ORIGINAL']        = 0;
	$totalizar['VALOR_JUROS']           = 0;
	$totalizar['VALOR_MULTA']           = 0;
	$totalizar['VALOR_TOTAL_CALCULADO'] = 0;

	if ($_SESSION['type_db'] == 'sqlsrv')
	{
		$query  = 'Select Ps1020.Numero_Registro, Ps1020.Data_Emissao, Ps1020.Data_Vencimento, Ps1020.Valor_Fatura Valor_Original, 
						Case 
						   When Ps1020.Data_Vencimento >= ' . $dataHoje . ' then 0
						   else
			                  Cast((Ps1020.Valor_Fatura * ' . $Percentual_Mora_Diaria . ') * DATEDIFF(day,Ps1020.data_vencimento,' . $dataHoje . ') as Numeric(14,2))   
			            end Valor_Juros,   
			            Case
						   When Ps1020.Data_Vencimento >= ' . $dataHoje . ' then 0
						   else
		                      Cast((Ps1020.Valor_Fatura * ' . $Percentual_Multa_Padrao . ') as Numeric(14,2)) 
		                end Valor_Multa,  
			            Case
						   When Ps1020.Data_Vencimento >= ' . $dataHoje . ' then Ps1020.Valor_Fatura 
						   else	
						      Cast((Valor_Fatura + 
		                          ((Ps1020.Valor_Fatura * ' . $Percentual_Mora_Diaria . ') * DATEDIFF(day,Ps1020.data_vencimento,' . $dataHoje . ')) + 
		                          (Ps1020.Valor_Fatura * ' . $Percentual_Multa_Padrao . ')) as Numeric(14,2)) 
		                end Valor_Total_Calculado,
		                Case When Ps1020.Data_Vencimento >= ' . $dataHoje . ' then 0
		                else
                            DATEDIFF(day,Ps1020.data_vencimento,' . $dataHoje . ') 
                        end Quantidade_Dias_Atraso
					 from ps1020
					 where (Ps1020.Codigo_Associado = ' . aspas($dadosInput['codigoTitular']) . ') and 
					       (Ps1020.Data_Cancelamento is null) and 
					       (Ps1020.Data_Pagamento Is null)
					 Order By Ps1020.Data_Vencimento Desc';
	}
	else
	{

		/* PROVISÓRIO */ 
		$linha['TITULO_RETORNO'] = 'Implementar a mesma consulta ajustada para Firebird';
		$retorno[] 				 = $linha;
		echo json_encode($retorno);
		return;

	}

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'',$totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta de faturamento com juros de faturas ativas e em aberto';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_FATURAMENTO_DETALHADO')
{

	$query  = 'Select Ps1020.Numero_Registro, Ps1020.Data_Emissao, Ps1020.Data_Vencimento, Ps1020.Valor_Fatura Total_Fatura, Ps1020.Data_Pagamento, 
						Ps1020.Valor_Pago Total_Pago, Ps1020.Data_Cancelamento, 
						Ps1000.Nome_Associado Beneficiario, Ps1021.Valor_Convenio, Ps1021.Valor_Correcao, Ps1021.Valor_Adicional,
						Ps1021.Valor_Fatura
				 from ps1020
				 Left Outer join Ps1021 on (ps1020.Numero_Registro = Ps1021.Numero_Registro_Ps1020)
				 Left Outer ps1000 on (Ps1021.Codigo_Associado = Ps1000.Codigo_Associado)
				 where (Ps1020.Codigo_Associado = ' . aspas($dadosInput['codigoTitular']) . ') 
				 Order By Ps1020.Data_Vencimento Desc, Ps1000.Codigo_Associado Asc';

	$totalizar                        = Array();
	$totalizar['TOTAL_FATURA']        = 0;
	$totalizar['TOTAL_PAGO']          = 0;

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'NUMERO_REGISTRO',$totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta de faturamento detalhado';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if ($dadosInput['tipo'] =='HTML_UTILIZACOES')
{

	$totalizar                          = Array();
	$totalizar['VALOR_TOTAL_ITEM']      = 0;
	$totalizar['VALOR_TOTAL_COPART']    = 0;

	$query  = 'Select PS5900.NUMERO_GUIA, PS5900.TIPO_GUIA, PS5900.NOME_PESSOA, Ps5000.Nome_Prestador,  
					  PS5900.DATA_PROCEDIMENTO,
					  Coalesce(PS5910.CODIGO_PROCEDIMENTO, PS5910.CODIGO_MEDICAMENTO_MATERIAL,	PS5910.CODIGO_SERVICO) Codigo_Item_Tabela,  
					  Coalesce(PS5210.Nome_Procedimento, Ps5203.Nome_Medicamento_Material, Ps5200.Nome_Servico) Nome_Item, 
					  PS5910.QUANTIDADE_ITENS, 
					  (PS5910.VALOR_UNITARIO_GERADO_ITEM * PS5910.QUANTIDADE_ITENS) VALOR_TOTAL_ITEM, 
					  (PS5910.VALOR_UNITARIO_COPART_ITEM * PS5910.QUANTIDADE_ITENS) VALOR_TOTAL_COPART 
					  FROM PS5900
					  INNER JOIN PS5910 ON (PS5900.NUMERO_GUIA = PS5910.NUMERO_GUIA)
					  INNER JOIN PS5000 ON (PS5910.CODIGO_PRESTADOR_ITEM = PS5000.CODIGO_PRESTADOR)
					  LEFT OUTER JOIN PS5210 ON (PS5910.CODIGO_PROCEDIMENTO = PS5210.CODIGO_PROCEDIMENTO)
					  LEFT OUTER JOIN PS5203 ON (PS5910.CODIGO_MEDICAMENTO_MATERIAL = PS5203.CODIGO_MEDICAMENTO_MATERIAL)
					  LEFT OUTER JOIN PS5200 ON (PS5910.CODIGO_SERVICO = PS5200.CODIGO_SERVICO)
	 				  where (Ps5900.Codigo_Associado = ' . aspas($dadosInput['codigoAssociado']) . ') 
					  Order By Ps5900.data_procedimento desc, ps5910.numero_registro asc';
	 
	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'', $totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta de utilizações do beneficiário';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_AUTORIZACOES')
{

	$query  = 'Select PS6500.NUMERO_AUTORIZACAO, PS6500.TIPO_GUIA, PS6500.NOME_PESSOA, Ps5000.Nome_Prestador,  
					  PS6500.DATA_AUTORIZACAO,
					  Coalesce(PS6510.CODIGO_PROCEDIMENTO, PS6510.CODIGO_MEDICAMENTO_MATERIAL) Codigo_Item_Tabela,  
					  Coalesce(PS5210.Nome_Procedimento, PS5203.Nome_Medicamento_Material) Nome_Item, 
					  PS6510.QUANTIDADE_PROCEDIMENTOS
					  FROM PS6500
					  INNER JOIN PS6510 ON (PS6500.NUMERO_AUTORIZACAO = PS6510.NUMERO_AUTORIZACAO)
					  INNER JOIN PS5000 ON (PS6500.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR)
					  LEFT OUTER JOIN PS5210 ON (PS6510.CODIGO_PROCEDIMENTO = PS5210.CODIGO_PROCEDIMENTO)
					  LEFT OUTER JOIN PS5203 ON (PS6510.CODIGO_MEDICAMENTO_MATERIAL = PS5203.CODIGO_MEDICAMENTO_MATERIAL)
	 				  where (Ps6500.Codigo_Associado = ' . aspas($dadosInput['codigoAssociado']) . ') 
					  Order By Ps6500.data_AUTORIZACAO desc, ps6510.numero_registro asc';
	 
	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query);
	$linha['TITULO_RETORNO'] = 'Consulta de autorizações do beneficiário';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_DECLARACAO_SAUDE')
{

	$query  = 'Select PS1039.DESCRICAO_PERGUNTA, PS1005.RESPOSTA_DIGITADA, PS1005.DESCRICAO_OBSERVACAO
					FROM PS1000
					INNER JOIN PS1005 ON (PS1000.CODIGO_ASSOCIADO = PS1005.CODIGO_ASSOCIADO)
					INNER JOIN PS1039 ON (PS1000.CODIGO_PLANO = PS1039.CODIGO_PLANO) AND (PS1005.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA)
					where (Ps1000.Codigo_Associado = ' . aspas($dadosInput['codigoAssociado']) . ') 
					  Order By Ps1005.NUMERO_PERGUNTA';
	 
	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query);
	$linha['TITULO_RETORNO'] = 'Consulta da desclaração de saúde';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_PRONTUARIO_SAUDE')
{

	$query  = 'Select ps1008.texto_prontuario, ps1008.descricao_observacao
					  FROM PS1000
					  INNER JOIN PS1008 ON (PS1000.CODIGO_ASSOCIADO = PS1008.CODIGO_ASSOCIADO)
	 				  where (Ps1000.Codigo_Associado = ' . aspas($dadosInput['codigoAssociado']) . ') 
					  Order By ps1008.Numero_prontuario';
	 
	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query);
	$linha['TITULO_RETORNO'] = 'Consulta de prontuário';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_OBSERVACOES')
{

	$query  = 'Select ps1007.DESCRICAO_OBSERVACAO, PS1007.OBSERVACAO_OUVIDORIA, PS1007.OBSERVACAO_COBRANCA, PS1007.OBSERVACAO_CADASTRO
				FROM PS1000
				INNER JOIN PS1007 ON (PS1000.CODIGO_ASSOCIADO = PS1007.CODIGO_ASSOCIADO)
				where (Ps1000.Codigo_Associado = ' . aspas($dadosInput['codigoAssociado']) . ') ';

	$res                   = jn_query($query);
	 
	$linha                 = Array();
	$titulos               = array();
	$titulos[0]            = 'Tipo de observação';
	$titulos[1]            = 'Informações cadastradas';

	$linha['HTML_RETORNO'] = iniciaTabelaVertical(2,$titulos);

	$qtCamposConsulta = jn_num_fields($res);

	while ($row = jn_fetch_object($res))
	{
	    $linha['HTML_RETORNO'] .= retornaValoresEmTabelaVertical($qtCamposConsulta,$res, $row);
	}

	$linha['HTML_RETORNO']  .= finalizaTabelaVertical();
	$linha['TITULO_RETORNO'] = 'Consulta de observações';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='GERA_PROTOCOLO_ATENDIMENTO')
{
	
	$numeroProtocoloGerado     = abreProtocoloAtendimentoPs6450($dadosInput['codigoAssociado']);	

	$linha['NUMERO_PROTOCOLO'] = $numeroProtocoloGerado;
	$retorno[] 				   = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='CONCLUI_PROTOCOLO_ATENDIMENTO')
{
	
	$resultado     = concluiProtocoloAtendimentoPs6450($dadosInput['numeroProtocolo']);	

	$linha['MSG']  = $resultado;
	$retorno[] 	   = $linha;

	echo json_encode($retorno);

}






?>