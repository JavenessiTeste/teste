<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] =='dados'){
	$codigoPrestador = $dadosInput['codigoPrestador'];
	$numeroProtocolo = $dadosInput['numeroProtocolo'];
	
	$queryProtocolo  = ' SELECT * FROM VW_PROTOCOLOS_GUIAS_AL2 ';
	$queryProtocolo .= ' WHERE CODIGO_PRESTADOR = ' . aspas($codigoPrestador);
	$queryProtocolo .= ' AND NUMERO_PROTOCOLO = ' . aspas($numeroProtocolo);
	$resProtocolo = jn_query($queryProtocolo); 
	
	while($rowProtocolo    = jn_fetch_object($resProtocolo)){		
		
		$item['NUMERO_PROTOCOLO']  	= $rowProtocolo->NUMERO_PROTOCOLO;
		$item['DATA_ENVIO']  		= SqlToData($rowProtocolo->DATA_ENVIO);
		$item['MES_ANO']  			= $rowProtocolo->MES_ANO;
		$item['QUANT_CONSULTAS'] 	= $rowProtocolo->QUANT_CONSULTAS;		
		$item['VALOR_CONSULTAS'] 	= toMoeda($rowProtocolo->VALOR_CONSULTAS);
		$item['QUANT_SADT'] 		= $rowProtocolo->QUANT_SADT;		
		$item['VALOR_SADT'] 		= toMoeda($rowProtocolo->VALOR_SADT);
		$item['QUANT_INTERNACOES'] 	= $rowProtocolo->QUANT_INTERNACOES;		
		$item['VALOR_INTERNACOES'] 	= toMoeda($rowProtocolo->VALOR_INTERNACOES);
		

	}
	
	$retorno['DADOS_PROTOCOLO'][] = $item;
	
	echo json_encode($retorno);
}

if($dadosInput['tipo'] == 'guiasProtocolo'){
	$codigoPrestador 	= $dadosInput['codigoPrestador'] ? $dadosInput['codigoPrestador'] : $_SESSION['codigoIdentificacao'];
	$numeroProtocolo 	= $dadosInput['numeroProtocolo'];	
	$tipoPrestador 		= '';
	
	$queryTpPrest = 'SELECT REFERENCIA_TABELA_ODONTO FROM PS5002 WHERE CODIGO_PRESTADOR = ' . aspas($codigoPrestador);
	$resTpPrest = jn_query($queryTpPrest);
	$rowTpPrest = jn_fetch_object($resTpPrest);	
	
	if($rowTpPrest->REFERENCIA_TABELA_ODONTO != ''){
		$tipoPrestador = 'ODONTO';
		
		$queryProtocolo  = ' SELECT  ';
		$queryProtocolo .= '  PS5750.CODIGO_PRESTADOR, ';
		$queryProtocolo .= '  PS5000.NOME_PRESTADOR, ';
		$queryProtocolo .= '  PS5750.DATA_ENVIO, ';
		$queryProtocolo .= '  PS5750.MES_ANO, ';
		$queryProtocolo .= '  COUNT(PS5750.NUMERO_REGISTRO) AS QUANT_ODONTO, ';
		$queryProtocolo .= '  SUM(VALOR_COBRADO_TOTAL) AS VALOR_ODONTO ';
		$queryProtocolo .= ' FROM PS5750';
		$queryProtocolo .= ' INNER JOIN PS5000 ON (PS5750.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR) ';
		$queryProtocolo .= ' INNER JOIN VW_SOMA_PS5760_AL2 ON (VW_SOMA_PS5760_AL2.NUMERO_REGISTRO_PS5750 = PS5750.NUMERO_REGISTRO) ';
		$queryProtocolo .= ' WHERE PS5750.CODIGO_PRESTADOR = ' . aspas($codigoPrestador);
		$queryProtocolo .= ' AND PS5750.NUMERO_PROTOCOLO = ' . aspas($numeroProtocolo);
		$queryProtocolo .= ' GROUP BY PS5750.CODIGO_PRESTADOR, PS5000.NOME_PRESTADOR, PS5750.DATA_ENVIO, PS5750.MES_ANO';		


	}else{
		$tipoPrestador = 'MEDICINA';	
		
		$queryProtocolo  = ' SELECT * FROM VW_PROTOCOLOS_GUIAS_AL2 A ';
		$queryProtocolo .= ' INNER JOIN PS5000 ON (A.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR)';
		$queryProtocolo .= ' WHERE A.CODIGO_PRESTADOR = ' . aspas($codigoPrestador);
		$queryProtocolo .= ' AND A.NUMERO_PROTOCOLO = ' . aspas($numeroProtocolo);
	}
	
	
	$resProtocolo = jn_query($queryProtocolo); 
	
	if($rowProtocolo    = jn_fetch_object($resProtocolo)){
		$item['CODIGO_PRESTADOR']  	= $rowProtocolo->CODIGO_PRESTADOR;
		$item['NOME_PRESTADOR']  	= $rowProtocolo->NOME_PRESTADOR;
		$item['TIPO_PRESTADOR']  	= $tipoPrestador;
		$item['NUMERO_PROTOCOLO']  	= ($rowProtocolo->NUMERO_PROTOCOLO ? $rowProtocolo->NUMERO_PROTOCOLO : $dadosInput['numeroProtocolo']);
		$item['DATA_ENVIO']  		= SqlToData($rowProtocolo->DATA_ENVIO);
		$item['MES_ANO']  			= $rowProtocolo->MES_ANO;
		$item['QUANT_CONSULTAS'] 	= $rowProtocolo->QUANT_CONSULTAS;		
		$item['VALOR_CONSULTAS'] 	= toMoeda($rowProtocolo->VALOR_CONSULTAS);
		$item['QUANT_SADT'] 		= $rowProtocolo->QUANT_SADT;		
		$item['VALOR_SADT'] 		= toMoeda($rowProtocolo->VALOR_SADT);
		$item['QUANT_INTERNACOES'] 	= $rowProtocolo->QUANT_INTERNACOES;		
		$item['VALOR_INTERNACOES'] 	= toMoeda($rowProtocolo->VALOR_INTERNACOES);
		$item['QUANT_ODONTO'] 		= $rowProtocolo->QUANT_ODONTO;		
		$item['VALOR_ODONTO'] 		= toMoeda($rowProtocolo->VALOR_ODONTO);
		$item['QUANT_TOTAL'] 		= $rowProtocolo->QUANT_TOTAL;		
		$item['VALOR_TOTAL'] 		= toMoeda($rowProtocolo->VALOR_TOTAL);
	}
	
	$retorno['DADOS_PROTOCOLO'] = $item;	
	
	$queryGuias  = ' SELECT NUMERO_REGISTRO,NUMERO_GUIA_OPERADORA, TIPO_GUIA, CODIGO_ASSOCIADO, NOME_PESSOA, DATA_CADASTRAMENTO  ';						
	$queryGuias .= ' FROM PS5750 WHERE PS5750.NUMERO_PROTOCOLO = '. aspas($numeroProtocolo) . '  AND PS5750.CODIGO_PRESTADOR = ' . aspas($codigoPrestador);		
	$resGuias = jn_query($queryGuias);	
	$i = 0;
	while($rowGuias = jn_fetch_object($resGuias)) {
		$queryValor  = "SELECT SUM(COALESCE(PS5760.VALOR_COBRADO,0)) AS VALOR_COBRADO FROM PS5760 WHERE PS5760.NUMERO_REGISTRO_PS5750 =". aspas($rowGuias->NUMERO_REGISTRO);
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
		
		$valor = $rowValor->VALOR_COBRADO;
		
		if (trim($valor) == '')
			$valor = 0;
			
		$Guias[$i]['NUMERO_REGISTRO']		= $rowGuias->NUMERO_REGISTRO;
		$Guias[$i]['TIPO_GUIA']			= $rowGuias->TIPO_GUIA;
		$Guias[$i]['CODIGO_ASSOCIADO']	= $rowGuias->CODIGO_ASSOCIADO;
		$Guias[$i]['NOME_PESSOA']			= $rowGuias->NOME_PESSOA;
		$Guias[$i]['DATA_CADASTRAMENTO']	= SqlToData($rowGuias->DATA_CADASTRAMENTO);	
		$Guias[$i]['VALOR_COBRADO']		= toMoeda($valor,false);	
		$Guias[$i]['NUMERO_GUIA']			= $rowGuias->NUMERO_GUIA_OPERADORA;
		$i++;
	}

	$retorno['DADOS_GUIAS'] = $Guias;

	echo json_encode($retorno);
	
}

?>