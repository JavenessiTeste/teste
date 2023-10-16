<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../lib/sysutilsAlianca.php');


if($dadosInput['tipo'] =='dadosGridEmpresas')
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


	if ($dadosInput['campoPesquisar']=='NOME_GRUPO_PESSOAS')
		$dadosInput['campoPesquisar'] = 'PS1014.NOME_GRUPO_PESSOAS';
	else if ($dadosInput['campoPesquisar']=='NOME_GRUPO_CONTRATO')
		$dadosInput['campoPesquisar'] = 'ESP0002.NOME_GRUPO_CONTRATO';
	else if ($dadosInput['campoPesquisar']=='CODIGO_GRUPO_CONTRATO')
		$dadosInput['campoPesquisar'] = 'ESP0002.CODIGO_GRUPO_CONTRATO';
	else if ($dadosInput['campoPesquisar']=='INFORMACOES_ADICIONAIS')
		$dadosInput['campoPesquisar'] = 'Ps1010.NOME_EMPRESA'; // SÓ PARA NÃO DAR ERRO
	else if ($dadosInput['campoPesquisar']=='')
		$dadosInput['campoPesquisar'] = 'Ps1010.NOME_EMPRESA'; // SÓ PARA NÃO DAR ERRO
	else
		$dadosInput['campoPesquisar'] = 'Ps1010.' . $dadosInput['campoPesquisar'];


	$queryPs1020    = 'Select top ' . $dadosInput['quantidadeResultados'] . ' PS1010.CODIGO_EMPRESA , 
	                          PS1010.NOME_EMPRESA, PS1010.DATA_ADMISSAO, PS1010.DATA_EXCLUSAO, PS1010.FLAG_PLANOFAMILIAR, 
	                          PS1014.NOME_GRUPO_PESSOAS, null INFORMACOES_ADICIONAIS ';

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$queryPs1020    .= ', ESP0002.CODIGO_GRUPO_CONTRATO, ESP0002.NOME_GRUPO_CONTRATO ';

	$queryPs1020    .= ' From Ps1010 
						 Left Outer Join Ps1014 on (Ps1010.CODIGO_GRUPO_PESSOAS = Ps1014.Codigo_grupo_pessoas) '; 

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$queryPs1020    .= ' Left Outer Join Esp0002 On (Ps10100.CODIGO_GRUPO_CONTRATO = esp0002.CODIGO_GRUPO_CONTRATO) ';

	if ($dadosInput['campoPesquisar']!='')
	    $queryPs1020    .= ' Where ' . $dadosInput['campoPesquisar'] . $tipoPesquisa . $valorPesquisar;

	if ($dadosInput['apresentarRegistrosAtivos']=='NAO')
		$queryPs1020    .= ' and Ps1010.Data_Exclusao is not null';

	if ($dadosInput['apresentarRegistrosExcluidos']=='NAO')
		$queryPs1020    .= ' and Ps1010.Data_Exclusao is null';

	if ($dadosInput['OrderBy']=='')
		$queryPs1020    .= ' Order By Ps1010.Codigo_Empresa';
	else
		$queryPs1020    .= ' Order By ' . $dadosInput['OrderBy'];

	$resPs1020 = jn_query($queryPs1020);

	while ($rowPrinc = jn_fetch_object($resPs1020))
	{

		$linha = Array();

		$linha['MARCADO'] 	 	         = 'S';
		$linha['CODIGO_EMPRESA']       = jn_utf8_encode_AscII($rowPrinc->CODIGO_EMPRESA);
		$linha['NOME_EMPRESA']         = jn_utf8_encode_AscII($rowPrinc->NOME_EMPRESA);
		$linha['DATA_ADMISSAO'] 	     = SqlToData($rowPrinc->DATA_ADMISSAO);
		$linha['DATA_EXCLUSAO'] 	     = SqlToData($rowPrinc->DATA_EXCLUSAO);
		$linha['NOME_GRUPO_PESSOAS']     = jn_utf8_encode_AscII($rowPrinc->NOME_GRUPO_PESSOAS);
		$linha['INFORMACOES_ADICIONAIS'] = jn_utf8_encode_AscII($rowPrinc->INFORMACOES_ADICIONAIS);
		$linha['FLAG_PLANOFAMILIAR']     = jn_utf8_encode_AscII($rowPrinc->FLAG_PLANOFAMILIAR);

		$retorno[] = $linha;

	}

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='dadosRegistroSelecionado')
{
	
	$query    = 'Select PS1010.CODIGO_EMPRESA, PS1010.NOME_EMPRESA, PS1010.DATA_ADMISSAO, PS1010.DATA_EXCLUSAO, PS1010.FLAG_PLANOFAMILIAR, 
	                    PS1014.NOME_GRUPO_PESSOAS, 
                        Ps1006_1.numero_telefone Telefone_01, Ps1006_2.Numero_Telefone Telefone_02, 
	                    Ps1001.Endereco,  Ps1001.Bairro,  
	                    Ps1001.Cidade,  Ps1001.Estado,  
	                    Ps1001.Cep,  Ps1001.Endereco,  
	                    Ps1002.Numero_Contrato, Ps1002.Dia_Vencimento, 
                        Case 
                             When Ps1010.DATA_EXCLUSAO IS NULL THEN "Ativo"
                             else "Excluido" 
                        end SITUACAO_CADASTRAL ';

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$query    .= ', ESP0002.CODIGO_GRUPO_CONTRATO, ESP0002.NOME_GRUPO_CONTRATO ';

	$query    .= ' From Ps1010 
						 Left Outer Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa)
						 Left Outer Join Ps1002 On (Ps1010.Codigo_Empresa = Ps1002.Codigo_Empresa)
						 Left Outer Join Ps1006 Ps1006_1 On (Ps1010.Codigo_Empresa = Ps1006_1.Codigo_Empresa) and (Ps1006_1.INDICE_TELEFONE = 1)
						 Left Outer Join Ps1006 Ps1006_2 On (Ps1010.Codigo_Empresa = Ps1006_2.Codigo_Empresa) and (Ps1006_2.INDICE_TELEFONE = 2) 
						 Left Outer Join Ps1014 on (Ps1010.CODIGO_GRUPO_PESSOAS = Ps1014.Codigo_grupo_pessoas) '; 

	if (retornaValorConfiguracao('TIPO_CLIENTE_ALIANCA')=='ADMINISTRADORA')
		$query    .= ' Left Outer Join Esp0002 On (Ps10100.CODIGO_GRUPO_CONTRATO = esp0002.CODIGO_GRUPO_CONTRATO) ';

	$query    .= ' where (Ps1010.Codigo_Empresa = ' . aspas($dadosInput['codigo']) . ')  ';

	$res = jn_query($query);

	$linha = Array();

	$titulos = array();
	$titulos[0] = 'Campo de informacao';
	$titulos[1] = 'Valor';

	$linha['INFORMACOES_CADASTRAIS']   = iniciaTabelaVertical(2,$titulos);

	$qtCamposConsulta = jn_num_fields($res);

	while ($row = jn_fetch_object($res))
	{
	    $linha['INFORMACOES_CADASTRAIS'] .= retornaValoresEmTabelaVertical($qtCamposConsulta,$res, $row);
	}

	$linha['INFORMACOES_CADASTRAIS'] .= finalizaTabelaVertical();

	/* ---------------------------------------------------------------- */

	$retorno[] = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_FATURAMENTO')
{

	$query  = 'Select Ps1020.Numero_Registro, Ps1020.Data_Emissao, Ps1020.Data_Vencimento, Ps1020.Valor_Fatura, Ps1020.Data_Pagamento, 
						Ps1020.Valor_Pago, Ps1020.Data_Cancelamento
				 from ps1020
				 where (Ps1020.Codigo_Empresa = ' . aspas($dadosInput['codigo']) . ') Order By Ps1020.Data_Vencimento Desc';

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
					 where (Ps1020.Codigo_Empresa = ' . aspas($dadosInput['codigo']) . ') and 
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
				 Left Outer join ps1000 on (Ps1021.Codigo_Associado = Ps1000.Codigo_Associado)
				 where (Ps1020.Codigo_Empresa = ' . aspas($dadosInput['codigo']) . ') 
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
					  INNER JOIN PS1000 ON (PS5900.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)
					  INNER JOIN PS5910 ON (PS5900.NUMERO_GUIA = PS5910.NUMERO_GUIA)
					  INNER JOIN PS5000 ON (PS5910.CODIGO_PRESTADOR_ITEM = PS5000.CODIGO_PRESTADOR)
					  LEFT OUTER JOIN PS5210 ON (PS5910.CODIGO_PROCEDIMENTO = PS5210.CODIGO_PROCEDIMENTO)
					  LEFT OUTER JOIN PS5203 ON (PS5910.CODIGO_MEDICAMENTO_MATERIAL = PS5203.CODIGO_MEDICAMENTO_MATERIAL)
					  LEFT OUTER JOIN PS5200 ON (PS5910.CODIGO_SERVICO = PS5200.CODIGO_SERVICO)
	 				  where (Ps1000.Codigo_Empresa = ' . aspas($dadosInput['codigo']) . ') 
					  Order By Ps5900.Codigo_Associado, Ps5900.data_procedimento desc, ps5910.numero_registro asc';
	 
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
					  INNER JOIN PS1000 ON (PS6500.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)
					  INNER JOIN PS6510 ON (PS6500.NUMERO_AUTORIZACAO = PS6510.NUMERO_AUTORIZACAO)
					  INNER JOIN PS5000 ON (PS6500.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR)
					  LEFT OUTER JOIN PS5210 ON (PS6510.CODIGO_PROCEDIMENTO = PS5210.CODIGO_PROCEDIMENTO)
					  LEFT OUTER JOIN PS5203 ON (PS6510.CODIGO_MEDICAMENTO_MATERIAL = PS5203.CODIGO_MEDICAMENTO_MATERIAL)
	 				  where (Ps1000.Codigo_Empresa = ' . aspas($dadosInput['codigo']) . ') 
					  Order By Ps6500.Codigo_Associado, Ps6500.data_AUTORIZACAO desc, ps6510.numero_registro asc';
	 
	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query);
	$linha['TITULO_RETORNO'] = 'Consulta de autorizações do beneficiário';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_OBSERVACOES')
{

	$query  = 'Select ps1007.DESCRICAO_OBSERVACAO, PS1007.OBSERVACAO_OUVIDORIA, PS1007.OBSERVACAO_COBRANCA, PS1007.OBSERVACAO_CADASTRO
				FROM PS1000
				INNER JOIN PS1007 ON (PS1000.CODIGO_ASSOCIADO = PS1007.CODIGO_ASSOCIADO)
				where Ps1007.Codigo_Empresa = ' . aspas($dadosInput['codigo']);

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