<?php

require('../lib/base.php');

//require('../private/autentica.php');

if ($_SESSION['type_db'] == 'firebird')
{
	$palavraFirstTop = ' first ';
}
else
{
	$palavraFirstTop = ' Top ';
}


if ($dadosInput['tipo'] =='especialidades')
{
	
	if ($dadosInput['descricao'] != '')
		$complementoWhere = " and PS5100.NOME_ESPECIALIDADE like " . aspas(strToUpper($dadosInput['descricao']) . '%') . " ";

	$queryRetorno = 'select ' . $palavraFirstTop .  ' 200 PS5100.CODIGO_ESPECIALIDADE, PS5100.NOME_ESPECIALIDADE,  
					 coalesce((select count(*) from ps5003 
					         INNER JOIN PS5000 ON (PS5003.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR)
					         WHERE (ps5100.CODIGO_ESPECIALIDADE = ps5003.CODIGO_ESPECIALIDADE) AND 
					               (PS5000.DATA_DESCREDENCIAMENTO IS NULL)),0) QUANTIDADE_PRESTADORES	
	                 from PS5100 WHERE 
	                 coalesce(PS5100.NOME_ESPECIALIDADE,"") <> "" and PS5100.DATA_INUTILIZ_REGISTRO IS NULL ' . $complementoWhere . 
	               ' ORDER BY PS5100.NOME_ESPECIALIDADE';
		
	$resRetorno = jn_query($queryRetorno);
	$retorno    = null;
		
	while ($rowRetorno = jn_fetch_object($resRetorno))
	{
		if ($rowRetorno->QUANTIDADE_PRESTADORES!=0)
		{
			$retorno['VALOR']     = jn_utf8_encode($rowRetorno->CODIGO_ESPECIALIDADE);
			$retorno['DESC']      = jn_utf8_encode($rowRetorno->NOME_ESPECIALIDADE);
			$retorno['QTDPREST']  = $rowRetorno->QUANTIDADE_PRESTADORES;
			$retornos[]           = $retorno;
		}
	}	
			
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);
}
else if ($dadosInput['tipo'] =='procedimentos')
{


	if ($dadosInput['descricaoProcedimento'] != '')
		$complementoWhere = " and PS5210.NOME_PROCEDIMENTO like " . aspas('%' . strToUpper($dadosInput['descricaoProcedimento']) . '%') . " ";

	$queryRetorno = 'select  ' . $palavraFirstTop .  ' 200 PS5210.CODIGO_PROCEDIMENTO, PS5210.NOME_PROCEDIMENTO, PS5294.CODIGO_SERVICO_ACS, 
	                 COALESCE((Select count(*) From PS5296 
	                                  INNER JOIN PS5000 ON (PS5296.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR)
	                 				  Where PS5294.CODIGO_SERVICO_ACS = PS5296.CODIGO_SERVICO_ACS AND PS5000.DATA_DESCREDENCIAMENTO IS NULL),0) QUANTIDADE_PRESTADORES
	                 FROM PS5294 
	                 inner join PS5210 On (PS5294.CODIGO_PROCEDIMENTO = Ps5210.CODIGO_PROCEDIMENTO)
	                 WHERE  Ps5210.DATA_INUTILIZ_REGISTRO IS NULL ' . $complementoWhere . 
		            ' ORDER BY PS5210.NOME_PROCEDIMENTO ';
		
	$resRetorno = jn_query($queryRetorno);
	$retorno    = null;
		
	while ($rowRetorno = jn_fetch_object($resRetorno))
	{
		if ($rowRetorno->QUANTIDADE_PRESTADORES!=0)
		{
			$retorno['VALOR']          = jn_utf8_encode($rowRetorno->CODIGO_SERVICO_ACS);
			$retorno['DESC']           = jn_utf8_encode(substr($rowRetorno->NOME_PROCEDIMENTO,0,50));
			$retorno['TIPO_VARIACAO']  = $rowRetorno->VARIACAO_PROCEDIMENTO; // SE É PADRÃO, SE TEM CONTRASTE, SE É SEM CONTRASTE, ETC.
			$retorno['QTDPREST']       = $rowRetorno->QUANTIDADE_PRESTADORES;
			$retornos[]                = $retorno;
		}
	}	
			
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);
}
else if ($dadosInput['tipo'] =='prestadores')
{

	if ($dadosInput['tpPesquisa'] =='ESP')
	{	

		if ($dadosInput['nomePrestadorPesquisa'] != '')
			$complementoWhere = " and NOME_PRESTADOR like " . aspas(strToUpper($dadosInput['nomePrestadorPesquisa']) . '%') . " ";

		$codigoConsulta = retornaCodigoProcedimentoConsulta();

		$queryRetorno = 'select  ' . $palavraFirstTop .  ' 200 PS5000.CODIGO_PRESTADOR, PS5000.NOME_PRESTADOR, PS5001.* 
		                 FROM PS5000 
		                 INNER JOIN PS5003 ON (PS5000.CODIGO_PRESTADOR = PS5003.CODIGO_PRESTADOR)
		                 LEFT OUTER JOIN PS5001 ON (PS5000.CODIGO_PRESTADOR = PS5001.CODIGO_PRESTADOR)
		                 WHERE PS5003.CODIGO_ESPECIALIDADE = ' . aspas($dadosInput['codigoPesquisar']) . 
		                ' AND PS5000.DATA_DESCREDENCIAMENTO IS NULL ' . 
		                 $complementoWhere . 
		               ' ORDER BY PS5000.NOME_PRESTADOR ';
	}	          
	else if (($dadosInput['tpPesquisa'] =='PROC')||($dadosInput['tpPesquisa'] =='EXAM'))
	{	

		if ($dadosInput['nomePrestadorPesquisa'] != '')
			$complementoWhere = " and NOME_PRESTADOR like " . aspas(strToUpper($dadosInput['nomePrestadorPesquisa']) . '%') . " ";

		$queryRetorno = 'select  ' . $palavraFirstTop .  ' 200 PS5000.CODIGO_PRESTADOR, PS5000.NOME_PRESTADOR, PS5296.*, 
		                 PS5294.CODIGO_PROCEDIMENTO, PS5294.CODIGO_SERVICO_ACS, PS5001.* 
		                 FROM PS5000 
		                 INNER JOIN PS5296 ON (PS5000.CODIGO_PRESTADOR = PS5296.CODIGO_PRESTADOR)
		                 INNER JOIN PS5294 ON (PS5296.CODIGO_SERVICO_ACS = PS5294.CODIGO_SERVICO_ACS)
		                 LEFT OUTER JOIN PS5001 ON (PS5000.CODIGO_PRESTADOR = PS5001.CODIGO_PRESTADOR)
		                 WHERE PS5296.CODIGO_SERVICO_ACS = ' . aspas($dadosInput['codigoPesquisar']) . $complementoWhere . 
		               ' ORDER BY PS5000.NOME_PRESTADOR ';
	}	          
		
	$resRetorno = jn_query($queryRetorno);
	$retorno    = null;
		
	while ($rowRetorno = jn_fetch_object($resRetorno))
	{
		$htmlMontado = jn_utf8_encode(strtolower($rowRetorno->ENDERECO)) . ' <br> ' . 
		               jn_utf8_encode(strtolower($rowRetorno->BAIRRO)) . ' - ' . jn_utf8_encode(strtolower($rowRetorno->CIDADE)) . '<br>'  . 
		               jn_utf8_encode($rowRetorno->TELEFONE_01);

		$retorno['CODIGO_PRESTADOR']     = jn_utf8_encode($rowRetorno->CODIGO_PRESTADOR);
		$retorno['NOME_PRESTADOR']       = jn_utf8_encode($rowRetorno->NOME_PRESTADOR);
		$retorno['DESCRICAO_HTML']       = $htmlMontado;

		if ($dadosInput['tpPesquisa'] =='ESP')
		{
  		   $retorno['VALOR_TOTAL_COBRANCA']          = retornaValorCobranca($codigoConsulta, $rowRetorno->CODIGO_PRESTADOR);
   		   $retorno['CODIGO_PROCEDIMENTO_CONSULTA']  = $codigoConsulta;
		}
  		else
  		{
  		   $retorno['VALOR_TOTAL_COBRANCA'] = retornaValorCobranca($rowRetorno->CODIGO_SERVICO_ACS, $rowRetorno->CODIGO_PRESTADOR);
  		}

 		$retornos[]                      = $retorno;
	}	
			
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);
}
else if ($dadosInput['tipo'] =='dadosBeneficiario')
{

	$queryRetorno = 'select NOME_ASSOCIADO, PS1010.FLAG_CUSTEIO 
	                 FROM PS1000 
	                 INNER JOIN PS1010 ON (PS1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA)
	                 WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($dadosInput['codigoAssociado']);
		
	$resRetorno = jn_query($queryRetorno);
	$retorno    = null;
		
	while ($rowRetorno = jn_fetch_object($resRetorno))
	{
		$retorno['NOME_ASSOCIADO']                 = jn_utf8_encode($rowRetorno->NOME_ASSOCIADO);

		if ($rowRetorno->FLAG_CUSTEIO=='S')
		    $retorno['PERMITIR_FATURAMENTO_POSTERIOR'] = 'SIM';
		else
		    $retorno['PERMITIR_FATURAMENTO_POSTERIOR'] = 'NAO';

		$retornos[]                               = $retorno;
	}	
			
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);
}
else if ($dadosInput['tipo'] =='horariosprestador')
{

	$dataCriterio = date('d/m/Y');

	$queryRetorno = 'select ' . $palavraFirstTop . ' 40 PS6010.NUMERO_REGISTRO, PS6010.HORA_MARCACAO, DATA_MARCACAO 
	                 FROM PS6010
	                 WHERE PS6010.CODIGO_PRESTADOR = ' . aspas($dadosInput['codigoPrestador']) . 
	               ' AND PS6010.DATA_MARCACAO >= ' . dataToSql($dataCriterio) .   
	               ' AND COALESCE(PS6010.TIPO_SITUACAO,"LIVRE") = "LIVRE" ' . 
	               ' AND PS6010.NUMERO_REGISTRO NOT IN (SELECT PS6013.NUMERO_REGISTRO_PS6010 FROM PS6013 WHERE PS6013.NUMERO_REGISTRO_PS6010 = PS6010.NUMERO_REGISTRO)                 
	               ORDER BY PS6010.DATA_MARCACAO, PS6010.HORA_MARCACAO ';
		
	$resRetorno = jn_query($queryRetorno);
	$retorno    = null;
	$diasemana = array('Domingo', 'Segunda-feira', 'Terca-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');		

	while ($rowRetorno = jn_fetch_object($resRetorno))
	{

		$diasemana_numero = date('w', strtotime($rowRetorno->DATA_MARCACAO));

		$retorno['NUMERO_REGISTRO']    = jn_utf8_encode($rowRetorno->NUMERO_REGISTRO);
		$retorno['DATA_MARCACAO']      = SqlToData($rowRetorno->DATA_MARCACAO);
		$retorno['DIA_SEMANA']         = jn_utf8_encode($diasemana[$diasemana_numero]);
		$retorno['HORARIO_PRESTADOR']  = jn_utf8_encode($rowRetorno->HORA_MARCACAO);
		$retornos[]                    = $retorno;
	}	
			
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);
}
else if ($dadosInput['tipo'] =='prereservaagenda')
{

	$diaAtual = date('d/m/Y');

	$queryRetorno = 'insert into ps6013(NUMERO_REGISTRO_PS6010, CODIGO_IDENTIFICACAO, DATA_RESERVA, HORA_RESERVA) 
	                 VALUES(' . aspas($dadosInput['numeroRegistro']) . ', ' . 
	                 '1, ' . 
 	                 dataToSql($diaAtual) . ' , ' . 
 	                 aspas(date('H:m')) . ')';

	if (!jn_query($queryRetorno,false,true,true))
	{
		$retorno['CONSEGUIURESERVAR']  = 'NAO';
	}	
	else
	{
		$retorno['CONSEGUIURESERVAR']  = 'SIM';
	}	
			
	$retornos[]           = $retorno;
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);

}
else if ($dadosInput['tipo'] =='excluiprereservaagenda')
{

	$queryRetorno = 'delete from ps6013 where NUMERO_REGISTRO_PS6010 = ' . aspas($dadosInput['numeroRegistro']);

	jn_query($queryRetorno);

	$retorno['CONSEGUIURESERVAR']  = 'SIM';
	$retornos[]           = $retorno;
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);

}
else if ($dadosInput['tipo'] =='registraservicoscomprados')
{

	$numeroIdentificacao = date('Y/m/d') . date('H:m') . jn_gerasequencial('PS5720');
	$numeroIdentificacao = str_replace("/", "", $numeroIdentificacao);
	$numeroIdentificacao = str_replace(":", "", $numeroIdentificacao);

	$queryRetorno = 'Insert Into PS5720(Numero_Identificacao_Compra, Codigo_Associado, Data_Compra, Hora_Compra) 
	                 Values(' . aspas($numeroIdentificacao) . ', ' .
	                            aspas($dadosInput['codigoAssociado']) . ', ' . 
 	                 			dataToSql(date('d/m/Y')) . ' , ' . 
 	                 			aspas(date('H:m')) . ')';

	jn_query($queryRetorno);

	//

	$registrosServicoACS      = explode(';',$dadosInput['registrosServicoACS']);

	foreach($registrosServicoACS as $value)
	{
		if (trim($value)!='')
		{
			$valoresSeparados 	= explode('|',$value);		
			$valuesSql          = '';

			foreach($valoresSeparados as $valorSeparado)
			{
				$valuesSql.= ', ' . aspasNull($valorSeparado);
			}		

			if ($valuesSql!='')
			{
				$valuesSql       = 'Values(' .  aspas($numeroIdentificacao) . $valuesSql;
				$queryRetorno    = 'Insert Into PS5721(Numero_Identificacao_Compra, Codigo_Prestador_Executante, Codigo_Servico_Acs, 
			                       Numero_Registro_Ps6010, Valor_Cobranca_Cliente) ' .  
				                   $valuesSql . ')'; 
				jn_query($queryRetorno);
			}
		}
	}

	//

	if ($dadosInput['numerosAgendamento']!='')
	{
		$queryRetorno    = 'Update PS6010 SET CODIGO_ASSOCIADO = ' . aspas($dadosInput['codigoAssociado']) . ', TIPO_SITUACAO = ' . aspas('AGENDADO') . 
				           'WHERE NUMERO_REGISTRO IN (' . $dadosInput['numerosAgendamento'] . ')';                 
		jn_query($queryRetorno);
	}

	//

	$queryRetorno = 'select NUMERO_REGISTRO, CODIGO_SERVICO_ACS, CODIGO_PRESTADOR_EXECUTANTE From PS5721 WHERE Numero_Identificacao_Compra = ' . aspas($numeroIdentificacao);
	$resRetorno   = jn_query($queryRetorno);

	while ($rowRetorno = jn_fetch_object($resRetorno))
	{
		$queryRetorno = 'Insert into PS5722(Numero_Identificacao_Compra, Numero_Registro_Ps5721, Codigo_Prestador, Posicao_Prestador, Valor_Prestador) 
						 SELECT ' . aspas($numeroIdentificacao) . ', ' . 
						            aspas($rowRetorno->NUMERO_REGISTRO) . ', 
						            COALESCE(ps5295.CODIGO_PRESTADOR,' . $rowRetorno->CODIGO_PRESTADOR_EXECUTANTE . '), POSICAO_PRESTADOR, VALOR_REMUNERACAO 
						            FROM PS5295 
						            WHERE PS5295.CODIGO_SERVICO_ACS = ' . aspas($rowRetorno->CODIGO_SERVICO_ACS);
		jn_query($queryRetorno);
	}

	$retorno['NUMEROIDENTIFICACAOCOMPRA']  = $numeroIdentificacao;
	$retornos[]           = $retorno;
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);

}



else if ($dadosInput['tipo'] =='registraValorPs1023')
{

	$codigoEvento     = '1';
	$mesAnoVencimento = '12/2021';

	$queryRetorno = 'INSERT INTO PS1023(CODIGO_EVENTO, CODIGO_EMPRESA, CODIGO_ASSOCIADO, NOME_PESSOA, DATA_EVENTO, VALOR_EVENTO, 
		                                QUANTIDADE_EVENTOS, MES_ANO_VENCIMENTO, DESCRICAO_HISTORICO, TIPO_GUIA, REFERENCIA_GERACAO)
                     Select ' . aspas($codigoEvento) . ', PS1000.CODIGO_EMPRESA, PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS5720.DATA_COMPRA, 
                            SUM(COALESCE(PS5721.VALOR_COBRANCA_CLIENTE,0)) VALOR_COBRAR, 1, ' . aspas($mesAnoVencimento) . ',  PS5720.NUMERO_IDENTIFICACAO_COMPRA, 
                            "V", PS5720.NUMERO_IDENTIFICACAO_COMPRA
					 from ps5720
					 INNER JOIN PS5721 ON (PS5720.NUMERO_IDENTIFICACAO_COMPRA = PS5721.NUMERO_IDENTIFICACAO_COMPRA)
					 INNER JOIN PS1000 ON (PS5720.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)
					 INNER JOIN PS1010 ON (PS1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA)
					 WHERE PS5720.NUMERO_IDENTIFICACAO_COMPRA = ' . aspas($dadosInput['numeroCompraGerada']) . 
				   ' GROUP BY PS1000.CODIGO_EMPRESA, PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS5720.DATA_COMPRA, 
					       PS5720.NUMERO_IDENTIFICACAO_COMPRA, PS5720.NUMERO_IDENTIFICACAO_COMPRA';

	jn_query($queryRetorno);

	//

	$queryRetorno = 'UPDATE PS5720 SET DATA_PAGAMENTO_CLIENTE = ' . dataToSql(date('d/m/Y')) . ' , ID_PAGAMENTO_CLIENTE = ' . aspas('VL_ADICIONAL_PS1023') . 
					'WHERE PS5720.NUMERO_IDENTIFICACAO_COMPRA = ' . aspas($dadosInput['numeroCompraGerada']);

	jn_query($queryRetorno);

	//
	
	$retorno['NUMEROIDENTIFICACAOCOMPRA']  = $dadosInput['numeroCompraGerada'];
	$retornos[]           = $retorno;
	$retorno['DADOS']     = $retornos;

	echo json_encode($retorno);

}




function retornaValorCobranca($codigoProcedimento, $codigoPrestador)
{

	$valorTotal              = 0;
	$complementoSegundaQuery = '';

	// Primeiro tento achar o valor especifico para o prestador do parâmetro
	$queryRetorno = 'select PS5295.VALOR_REMUNERACAO, PS5295.POSICAO_PRESTADOR
	                 from PS5295 
					 inner join PS5294 on (PS5295.CODIGO_SERVICO_ACS = PS5294.CODIGO_SERVICO_ACS)
	                 WHERE PS5294.CODIGO_SERVICO_ACS = ' . aspas($codigoProcedimento) . 
	               ' and PS5295.CODIGO_PRESTADOR IS NOT NULL AND PS5295.CODIGO_PRESTADOR = ' . aspas($codigoPrestador) .
	               ' and coalesce(PS5295.VALOR_REMUNERACAO,0) <> 0';
	
	$resRetorno     = jn_query($queryRetorno);

	if ($rowRetorno = jn_fetch_object($resRetorno))
	{
		$valorTotal = $rowRetorno->VALOR_REMUNERACAO;
		$complementoSegundaQuery = ' and POSICAO_PRESTADOR <> ' . aspas($rowRetorno->POSICAO_PRESTADOR);
	}

	// Agora busco para todos os demais registros, e se encontrar um valor na consulta anterior eu não uso o mesmo registro nesta.

	$queryRetorno = 'select PS5295.VALOR_REMUNERACAO, PS5295.POSICAO_PRESTADOR
	                 from PS5295 
					 inner join PS5294 on (PS5295.CODIGO_SERVICO_ACS = PS5294.CODIGO_SERVICO_ACS)
	                 WHERE PS5294.CODIGO_SERVICO_ACS = ' . aspas($codigoProcedimento) . 
	                 $complementoSegundaQuery . 
	               ' and PS5295.CODIGO_PRESTADOR IS NULL';
	
	$resRetorno        = jn_query($queryRetorno);

	while ($rowRetorno = jn_fetch_object($resRetorno))
	{
		$valorTotal += $rowRetorno->VALOR_REMUNERACAO;
	}	

	$valorTotal = $valorTotal + retornaValorApenasMarketPlace($codigoProcedimento, $valorTotal);

	return $valorTotal;

}




function retornaValorApenasMarketPlace($codigoProcedimento,$valorTotalProcedimento)
{

	$queryRetorno = 'select PS5294.REMUNERACAO_MARKETPLACE, PS5294.TIPO_CALCULO_REMUNERACAO
	                 from PS5294
	                 WHERE PS5294.CODIGO_SERVICO_ACS = ' . aspas($codigoProcedimento);
	
	$resRetorno   = jn_query($queryRetorno);
	$rowRetorno   = jn_fetch_object($resRetorno);

	if ($rowRetorno->TIPO_CALCULO_REMUNERACAO == 'VALOR_FINAL')
	    $valorTotalProcedimento = $rowRetorno->REMUNERACAO_MARKETPLACE;
	else
	    $valorTotalProcedimento = ($valorTotalProcedimento * ($rowRetorno->REMUNERACAO_MARKETPLACE / 100));

	return round($valorTotalProcedimento,2);

}




function retornaCodigoProcedimentoConsulta()
{
	return "00010014";
}

?>