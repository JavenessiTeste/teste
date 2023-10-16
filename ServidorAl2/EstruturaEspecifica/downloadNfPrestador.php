<?php

require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] == 'listar'){	
	
	$query   =	'	SELECT ';
	$query  .=	'		CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR, ';
	$query  .=	'		PS5000.NOME_PRESTADOR, ';
	$query  .=	'		SUM(CASE WHEN DATA_DOWNLOAD IS NULL THEN 1 ELSE 0 END) AS QTDE_PENDENTE, ';
	$query  .=	'		SUM(CASE WHEN DATA_DOWNLOAD IS NOT NULL THEN 1 ELSE 0 END) AS QTDE_BAIXADA, ';
	$query  .=	'		COUNT(*) AS QTDE_GERAL ';
	$query  .=	'	FROM CFGARQUIVOS_PROCESSOS_NET ';
	$query  .=	'	INNER JOIN PS5000 ON (PS5000.CODIGO_PRESTADOR = CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR) ';
	$query  .=	'	WHERE CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR IS NOT NULL ';
	$query  .=	'	AND ((CFGARQUIVOS_PROCESSOS_NET.TIPO_ARQUIVO = "NOTA_PRESTADOR") OR (CFGARQUIVOS_PROCESSOS_NET.TIPO_ARQUIVO = "ARQUIVO_GUIAS")) ';
	
	if($dadosInput['prestador']){
		$query  .=	'	AND CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR = ' . aspas($dadosInput['prestador']);
	}
	
	if($dadosInput['statusNf']){
		if($dadosInput['statusNf'] == 'B')
			$query  .=	'	AND CFGARQUIVOS_PROCESSOS_NET.DATA_DOWNLOAD IS NOT NULL ';
		
		if($dadosInput['statusNf'] == 'P')
			$query  .=	'	AND CFGARQUIVOS_PROCESSOS_NET.DATA_DOWNLOAD IS NULL ';
	}
	
	$query  .=	'	GROUP BY CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR, PS5000.NOME_PRESTADOR ';
	$query  .=	'	ORDER BY PS5000.NOME_PRESTADOR ';	
	
	$res = jn_query($query);					
	
	$retorno = array();
	$linha = '';		
	while($row = jn_fetch_object($res)) {
		$linha['CODIGO_PRESTADOR']  	= $row->CODIGO_PRESTADOR;
		$linha['NOME_PRESTADOR']  		= jn_utf8_encode($row->NOME_PRESTADOR);
		$linha['QTD_NOTAS_PENDENTES'] 	= $row->QTDE_PENDENTE;
		$linha['QTD_NOTAS_BAIXADAS'] 	= $row->QTDE_BAIXADA;
		$linha['QTD_NOTAS_GERAL'] 		= $row->QTDE_GERAL;					
		
		$retorno[] = $linha;
	}	
	
	echo json_encode($retorno);
}

?>