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


	if (($dadosInput['campoPesquisar']=='ENDERECO') or ($dadosInput['campoPesquisar']=='CIDADE') or 
	    ($dadosInput['campoPesquisar']=='BAIRRO') or ($dadosInput['campoPesquisar']=='ESTADO') or
	    ($dadosInput['campoPesquisar']=='TELEFONE_01') or ($dadosInput['campoPesquisar']=='TELEFONE_02'))
		$dadosInput['mostrarEnderecosPrestador'] = 'S';

	if (($dadosInput['campoPesquisar']=='NOME_ESPECIALIDADE'))
	     $dadosInput['mostrarEspecialidadesPrestador'] = 'S';


	if (($dadosInput['campoPesquisar']=='ENDERECO') or ($dadosInput['campoPesquisar']=='CIDADE') or 
	    ($dadosInput['campoPesquisar']=='BAIRRO') or ($dadosInput['campoPesquisar']=='ESTADO') or
	    ($dadosInput['campoPesquisar']=='TELEFONE_01') or ($dadosInput['campoPesquisar']=='TELEFONE_02'))
		$dadosInput['campoPesquisar'] = 'PS5001.' . $dadosInput['campoPesquisar'];
	else if (($dadosInput['campoPesquisar']=='NOME_ESPECIALIDADE'))
		$dadosInput['campoPesquisar'] = 'PS5100.' . $dadosInput['campoPesquisar'];
	else if ($dadosInput['campoPesquisar']=='INFORMACOES_ADICIONAIS')
		$dadosInput['campoPesquisar'] = 'PS5000.NOME_PRESTADOR'; // SÓ PARA NÃO DAR ERRO
	else if ($dadosInput['campoPesquisar']=='')
		$dadosInput['campoPesquisar'] = 'PS5000.NOME_PRESTADOR'; // SÓ PARA NÃO DAR ERRO
	else
		$dadosInput['campoPesquisar'] = 'PS5000.' . $dadosInput['campoPesquisar'];

	$query    = 'Select top ' . $dadosInput['quantidadeResultados'] . ' PS5000.CODIGO_PRESTADOR , 
	                          PS5000.NOME_PRESTADOR, PS5000.DATA_INCLUSAO, PS5000.DATA_DESCREDENCIAMENTO, PS5000.TIPO_PRESTADOR, 
	                          PS5000.TIPO_PESSOA, PS5000.RAZAO_SOCIAL_NM_COMPLETO, null INFORMACOES_ADICIONAIS ';

	if ($dadosInput['mostrarEnderecosPrestador']=='S')
	{
		$query  .= ' , PS5001.ENDERECO, PS5001.BAIRRO, PS5001.CIDADE, PS5001.ESTADO, PS5001.CEP, PS5001.ENDERECO_EMAIL 
		             , PS5001.TELEFONE_01, PS5001.TELEFONE_02 ';
	}

	if ($dadosInput['mostrarEspecialidadesPrestador']=='S')
	{
		$query  .= ' , PS5100.NOME_ESPECIALIDADE, PS5001ESP.ENDERECO ENDERECO_ESPECIALIDADE ';
	}

	$query  .= 'From PS5000 ';

	if ($dadosInput['mostrarEnderecosPrestador']=='S')
	{
		$query  .= ' Left Outer Join Ps5001 On (Ps5000.codigo_prestador = Ps5001.Codigo_Prestador)  ';
	}

	if ($dadosInput['mostrarEspecialidadesPrestador']=='S')
	{
		$query  .= ' Left Outer Join Ps5003 On (Ps5000.codigo_prestador = Ps5003.Codigo_Prestador)  
		             Left Outer Join Ps5100 On (Ps5003.Codigo_Especialidade = Ps5100.codigo_especialidade) 
		             Left Outer Join Ps5001 PS5001ESP On (Ps5003.Numero_Registro_Endereco = PS5001ESP.Numero_Registro_Endereco) ';
	}

	if ($dadosInput['campoPesquisar']!='')
	    $query    .= ' Where ' . $dadosInput['campoPesquisar'] . $tipoPesquisa . $valorPesquisar;

	if ($dadosInput['apresentarRegistrosAtivos']=='NAO')
		$query    .= ' and PS5000.DATA_DESCREDENCIAMENTO is not null';

	if ($dadosInput['apresentarRegistrosExcluidos']=='NAO')
		$query    .= ' and PS5000.DATA_DESCREDENCIAMENTO is null';

	if ($dadosInput['cidadePesquisa']!='')
		$query    .= ' and PS5000.CODIGO_PRESTADOR IN (SELECT PS5001.CODIGO_PRESTADOR FROM PS5001 WHERE 
	                                                           PS5001.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR AND 
	                                                           PS5001.CIDADE LIKE ' . aspas('%' . $dadosInput['cidadePesquisa'] . '%') . ')';

	if ($dadosInput['bairroPesquisa']!='')
		$query    .= ' and PS5000.CODIGO_PRESTADOR IN (SELECT PS5001.CODIGO_PRESTADOR FROM PS5001 WHERE 
	                                                           PS5001.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR AND 
	                                                           PS5001.BAIRRO LIKE ' . aspas('%' . $dadosInput['bairroPesquisa'] . '%') . ')';

	if ($dadosInput['tipoPrestadorPesquisa']!='')
		$query    .= ' and PS5000.TIPO_PRESTADOR = ' . $dadosInput['tipoPrestadorPesquisa'];

	if ($dadosInput['autoCompleteEspecialidade']['VALOR']!='')
		$query    .= ' and PS5000.CODIGO_PRESTADOR IN (SELECT PS5003.CODIGO_PRESTADOR FROM PS5003 WHERE 
	                                                           PS5003.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR AND 
	                                                           PS5003.CODIGO_ESPECIALIDADE = ' . aspas($dadosInput['autoCompleteEspecialidade']['VALOR']['VALOR']) . ')';

	if ($dadosInput['autoCompleteProcedimento']['VALOR']!='')
		$query    .= ' and PS5000.CODIGO_PRESTADOR IN (SELECT PS5005.CODIGO_PRESTADOR FROM PS5005 WHERE 
	                                                           PS5005.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR AND 
	                                                           PS5005.CODIGO_PROCEDIMENTO_INICIAL <= ' . aspas($dadosInput['autoCompleteProcedimento']['VALOR']['VALOR']) . ' AND 
	                                                           PS5005.CODIGO_PROCEDIMENTO_FINAL >= ' . aspas($dadosInput['autoCompleteProcedimento']['VALOR']['VALOR']) . ')  ';

	if ($dadosInput['autoCompleteServicoPesquisa']['VALOR']!='')
		$query    .= ' and PS5000.CODIGO_PRESTADOR IN (SELECT PS5006.CODIGO_PRESTADOR FROM PS5006 WHERE 
	                                                           PS5006.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR AND 
	                                                           PS5006.CODIGO_SERVICO = ' . aspas($dadosInput['autoCompleteServicoPesquisa']['VALOR']['VALOR']) . ')';

	if ($dadosInput['OrderBy']=='')
		$query    .= ' Order By PS5000.CODIGO_PRESTADOR';
	else
		$query    .= ' Order By ' . $dadosInput['OrderBy'];

	$res = jn_query($query);

	while ($rowPrinc = jn_fetch_object($res))
	{

		$linha = Array();

		$linha['MARCADO'] 	 	          = 'S';
		$linha['CODIGO_PRESTADOR']         = jn_utf8_encode_AscII($rowPrinc->CODIGO_PRESTADOR);
		$linha['NOME_PRESTADOR']           = jn_utf8_encode_AscII($rowPrinc->NOME_PRESTADOR);
		$linha['RAZAO_SOCIAL_NM_COMPLETO'] = jn_utf8_encode_AscII($rowPrinc->RAZAO_SOCIAL_NM_COMPLETO);
		$linha['DATA_INCLUSAO'] 	          = SqlToData($rowPrinc->DATA_INCLUSAO);
		$linha['DATA_DESCREDENCIAMENTO']   = SqlToData($rowPrinc->DATA_DESCREDENCIAMENTO);
		$linha['TIPO_PRESTADOR']           = jn_utf8_encode_AscII($rowPrinc->TIPO_PRESTADOR);
		$linha['INFORMACOES_ADICIONAIS']   = jn_utf8_encode_AscII($rowPrinc->INFORMACOES_ADICIONAIS);
		$linha['TIPO_PESSOA']              = jn_utf8_encode_AscII($rowPrinc->TIPO_PESSOA);
		$linha['ENDERECO']                 = jn_utf8_encode_AscII($rowPrinc->ENDERECO);
		$linha['BAIRRO']                   = jn_utf8_encode_AscII($rowPrinc->BAIRRO);
		$linha['CIDADE']                   = jn_utf8_encode_AscII($rowPrinc->CIDADE);
		$linha['ESTADO']                   = jn_utf8_encode_AscII($rowPrinc->ESTADO);
		$linha['CEP']                      = jn_utf8_encode_AscII($rowPrinc->CEP);
		$linha['ENDERECO_EMAIL']           = jn_utf8_encode_AscII($rowPrinc->ENDERECO_EMAIL);
		$linha['TELEFONE_01']              = jn_utf8_encode_AscII($rowPrinc->TELEFONE_01);
		$linha['TELEFONE_02']              = jn_utf8_encode_AscII($rowPrinc->TELEFONE_02);
		$linha['NOME_ESPECIALIDADE']       = jn_utf8_encode_AscII($rowPrinc->NOME_ESPECIALIDADE);
		$linha['ENDERECO_ESPECIALIDADE']   = jn_utf8_encode_AscII($rowPrinc->ENDERECO_ESPECIALIDADE);

		$retorno[] = $linha;

	}

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='dadosRegistroSelecionado')
{
	
	$titulos    = array();
	$titulos[0] = 'Campo de informacao';
	$titulos[1] = 'Valor';

	/* Dados principais do prestador */

	$query    = 'Select PS5000.CODIGO_PRESTADOR , 
	                          PS5000.NOME_PRESTADOR, PS5000.DATA_INCLUSAO, PS5000.DATA_DESCREDENCIAMENTO, PS5000.TIPO_PRESTADOR, 
	                          PS5000.TIPO_PESSOA, 
	                          Case 
	                              When TIPO_CONTRATO = "1" Then "Rede Contratada/referenciada ou credenciada" 
	                              When TIPO_CONTRATO = "2" Then "Rede Própria - Cooperados"
	                              When TIPO_CONTRATO = "3" Then "Rede Própria - Demais prestadores"
	                              When TIPO_CONTRATO = "4" Then "Reembolso ao beneficiário"
	                              When TIPO_CONTRATO = "5" Then "Parceiros externos" 
	                              When TIPO_CONTRATO = "6" Then "Sem vinculo"
	                          End Tipo_Prestador,
	                          Case 
	                              When TIPO_PAGAMENTO = "P" Then "CALCULO DOS PROCEDIMENTOS REALIZADOS" 
	                              When TIPO_PAGAMENTO = "S" Then "SALARIO FIXO MENSAL"
		                     End Tipo_Pagamento,
	                          DESCRICAO_OBSERVACAO, TIPOS_GUIAS_AUTORIZADAS,
	                          TIPO_CONTRATUALIZACAO_RPS, VERSAO_TISS_MONITORAMENTO, RESPONSAVEL_TECNICO, 
	                          PS5000.RAZAO_SOCIAL_NM_COMPLETO, null INFORMACOES_ADICIONAIS 
						 From PS5000 
					      where (Ps5000.Codigo_Prestador = ' . aspas($dadosInput['codigo']) . ')  ';

	$res   = jn_query($query);
	$linha = Array();

	$linha['INFORMACOES_CADASTRAIS']   = iniciaTabelaVertical(2,$titulos);

	$qtCamposConsulta = jn_num_fields($res);

	while ($row = jn_fetch_object($res))
	{
	    $linha['INFORMACOES_CADASTRAIS'] .= retornaValoresEmTabelaVertical($qtCamposConsulta,$res, $row);
	}

	$linha['INFORMACOES_CADASTRAIS'] .= finalizaTabelaVertical();

	$retorno[] = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='enderecosRegistroSelecionado')
{

	/* ---------------------------------------------------------------- */

	/* Dados endereço do prestador */

	$query    = 'Select PS5001.ENDERECO, PS5001.BAIRRO, PS5001.CIDADE, PS5001.ESTADO, PS5001.CEP, PS5001.ENDERECO_EMAIL, 
		               PS5001.TELEFONE_01, PS5001.TELEFONE_02
					From PS5001
					where (Ps5001.Codigo_Prestador = ' . aspas($dadosInput['codigo']) . ') Order By Cidade, Endereco  ';

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query);
	$retorno[]		     = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='especialidadesRegistroSelecionado')
{

	/* ---------------------------------------------------------------- */

	/* Dados endereço do prestador */

	$query    = 'Select PS5100.CODIGO_ESPECIALIDADE, PS5100.NOME_ESPECIALIDADE, 
					COALESCE(PS5001ESP.ENDERECO, Ps5001Padrao.ENDERECO) ENDERECO, 
					COALESCE(PS5001ESP.BAIRRO, Ps5001Padrao.BAIRRO) BAIRRO, 
	                    COALESCE(PS5001ESP.CIDADE, Ps5001Padrao.CIDADE) CIDADE, 
	                    COALESCE(PS5001ESP.TELEFONE_01, Ps5001Padrao.TELEFONE_01) TELEFONE_01, 
	                    COALESCE(PS5001ESP.TELEFONE_02,Ps5001Padrao.TELEFONE_02) TELEFONE_02 
					From PS5003
					Inner Join Ps5100 On (Ps5003.Codigo_Especialidade = Ps5100.Codigo_Especialidade)
					Left Outer Join Ps5001 Ps5001Esp On (Ps5003.Numero_Registro_Endereco = Ps5001Esp.Numero_Registro_Endereco)
					Left Outer Join Ps5001 Ps5001Padrao On (Ps5003.Codigo_Prestador = Ps5001Padrao.Codigo_Prestador)
					where (Ps5003.Codigo_Prestador = ' . aspas($dadosInput['codigo']) . ') Order By ps5100.NOME_ESPECIALIDADE, Cidade, Endereco ';

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query);
	$retorno[]		     = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_CAPAS_PROCESSO')
{

	$query  = 'Select TOP 100 PS5800.Numero_Registro, PS5800.NUMERO_PROCESSAMENTO, PS5800.DATA_VENCIMENTO, 
				   COALESCE(VALOR_TOTAL_GLOSADO,0) VALOR_TOTAL_GLOSADO, COALESCE(VALOR_GERADO_TOTAL,0) VALOR_GERADO_TOTAL, 
				   COALESCE(VALOR_LIQUIDO_PAGAR,0) VALOR_LIQUIDO_PAGAR, COALESCE(VALOR_TOTAL_GLOSADO,0) VALOR_TOTAL_GLOSADO, 
				   COALESCE(VALOR_TOTAL_GLOSADO,0) VALOR_TOTAL_GLOSADO, COALESCE(VALOR_LIQUIDO_PAGAR,0) VALOR_LIQUIDO_PAGAR,  			
				   PS5800.NUMERO_LOTE_TISS  	
				   from PS5800
				   where (PS5800.CODIGO_PRESTADOR = ' . aspas($dadosInput['codigo']) . ') Order By PS5800.Data_Vencimento Desc';

	$totalizar                        = Array();
	$totalizar['VALOR_GERADO_TOTAL']  = 0;
	$totalizar['VALOR_TOTAL_GLOSADO'] = 0;
	$totalizar['VALOR_LIQUIDO_PAGAR'] = 0;

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'',$totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta dos últimos 100 processamentos e capas de processo';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_FINANCEIRO')
{

	$query  = 'Select TOP 100 COALESCE(PS7200.CODIGO_IDENTIFICACAO, PS7200.CODIGO_PRESTADOR) CODIGO_IDENTIFICACAO, ps7200.DESCRICAO_CONTA, 
	                  PS7201.VALOR_CONTA, PS7201.DATA_VENCIMENTO, 
	                  PS7201.DATA_PREVISAO, PS7201.DATA_PAGAMENTO, PS7201.VALOR_PAGO, PS7201.OBSERVACAO_SIMPLES, 
	                  PS7201.NUMERO_NOTA_FISCAL, PS7201.MES_ANO_REFERENCIA, PS7201.DATA_EMISSAO 
				   from PS7200
				   INNER JOIN PS7201 ON (PS7200.CODIGO_CONTA = PS7201.CODIGO_CONTA)
				   where (PS7200.CODIGO_PRESTADOR = ' . aspas($dadosInput['codigo']) . ') Order By PS7201.Data_Vencimento Desc';

	$totalizar                        = Array();
	$totalizar['VALOR_CONTA']  = 0;
	$totalizar['VALOR_PAGO'] = 0;

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'',$totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta dos últimos 100 Pagamentos e financeiro do prestador';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if ($dadosInput['tipo'] =='HTML_UTILIZACOES')
{

	$totalizar                          = Array();
	$totalizar['VALOR_TOTAL_ITEM']      = 0;
	$totalizar['VALOR_TOTAL_COPART']    = 0;

	$query  = 'Select top 1000 PS5900.NUMERO_GUIA, PS5900.TIPO_GUIA, PS5900.NOME_PESSOA, Ps5000.Nome_Prestador,  
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
	 				  where (Ps5910.CODIGO_PRESTADOR_ITEM = ' . aspas($dadosInput['codigo']) . ') 
					  Order By Ps5900.Codigo_Associado, Ps5900.data_procedimento desc, ps5910.numero_registro asc';
	 
	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'', $totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta das últimas 1000 guias do prestador';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_AUTORIZACOES')
{

	$query  = 'Select top 1000 PS6500.NUMERO_AUTORIZACAO, PS6500.TIPO_GUIA, PS6500.NOME_PESSOA, Ps5000.Nome_Prestador,  
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
	 				  where (Ps6500.CODIGO_PRESTADOR = ' . aspas($dadosInput['codigo']) . ') 
					  Order By Ps6500.Codigo_Associado, Ps6500.data_AUTORIZACAO desc, ps6510.numero_registro asc';
	 
	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query);
	$linha['TITULO_RETORNO'] = 'Consulta das últimas 1000 autorizações do prestador';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_OBSERVACOES')
{

	$query  = 'Select OBSERVACAO_ATENDIMENTO, OBSERVACAO_SOBRE_FATURAMENTO, OBSERVACAO_CONTATOS, OBSERVACAO_CARACTERISTICA, 
	                  OBSERVACAO_GERAL 
				   FROM PS5007
   				   where Ps5007.CODIGO_PRESTADOR = ' . aspas($dadosInput['codigo']);

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