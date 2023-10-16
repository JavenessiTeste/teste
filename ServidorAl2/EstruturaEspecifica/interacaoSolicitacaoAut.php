<?php
require('../lib/base.php');

$associado 			= $dadosInput['codAssociado'];
$numeroSolicitacao 	= $dadosInput['numeroSolicitacao'];

if($dadosInput['tipo']== 'mensagens'){
	
	$query  = '	SELECT PS6550.CODIGO_ASSOCIADO, PS6551.NUMERO_REGISTRO, PS6551.NUMERO_SOLICITACAO, PS6551.DATA_MENSAGEM, ';
	$query .= '	HORA_MENSAGEM, TEXTO_MENSAGEM, PS6551.CODIGO_IDENTIFICACAO, COALESCE(PS6551.CODIGO_PRESTADOR, PS6550.CODIGO_SOLICITANTE) AS CODIGO_PRESTADOR ';
	$query .= '	FROM PS6550 ';
	$query .= '	LEFT JOIN PS6551 ON (PS6550.NUMERO_SOLICITACAO = PS6551.NUMERO_SOLICITACAO)';
	$query .= '	WHERE PS6550.NUMERO_SOLICITACAO = ' . aspas($numeroSolicitacao);

	if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
		$query .= '	AND PS6551.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){
		$query .= '	AND PS6551.CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']);
	}else{
		$query .= '	AND (PS6551.CODIGO_IDENTIFICACAO IS NULL OR PS6551.CODIGO_IDENTIFICACAO = ' . aspas($_SESSION['codigoIdentificacao']) . ')';
	}
	
	$query .= ' ORDER BY PS6551.NUMERO_REGISTRO ';
	
	$res  = jn_query($query);
	$ArrInteracoes = Array();
	$i = 0;
    while($row = jn_fetch_object($res)){
		$ArrInteracoes[$i]['codigoAssociado'] 		= $row->CODIGO_ASSOCIADO;
		$ArrInteracoes[$i]['codigoPrestador'] 		= $row->CODIGO_PRESTADOR;
		$ArrInteracoes[$i]['numeroSolicitacao'] 	= $row->NUMERO_SOLICITACAO;
		$ArrInteracoes[$i]['dataMensagem'] 			= $row->DATA_MENSAGEM;
		$ArrInteracoes[$i]['horaMensagem'] 			= $row->HORA_MENSAGEM;
		$ArrInteracoes[$i]['textoMensagem'] 		= jn_utf8_encode($row->TEXTO_MENSAGEM);
		$ArrInteracoes[$i]['codigoIdentificacao'] 	= $row->CODIGO_IDENTIFICACAO;
		$i++;
	}
	
	echo json_encode($ArrInteracoes);
	
	
	if($ArrInteracoes){
		$queryUpdate  = ' UPDATE PS6551 SET DATA_VISUALIZACAO = CURRENT_TIMESTAMP ';
		$queryUpdate .= ' WHERE DATA_VISUALIZACAO IS NULL ';
		if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
			$queryUpdate .= ' AND CODIGO_IDENTIFICACAO IS NOT NULL AND CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){
			$queryUpdate .= ' AND CODIGO_IDENTIFICACAO IS NOT NULL AND CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']);
		}else{
			$queryUpdate .= ' AND CODIGO_IDENTIFICACAO = ' . aspas($_SESSION['codigoIdentificacao']);
		}
		$queryUpdate .= ' AND NUMERO_SOLICITACAO = ' . aspas($numeroSolicitacao);		
		jn_query($queryUpdate);
	}
	
}

if($dadosInput['tipo']== 'salvarMensagem'){
	
	$queryMensagem = '';
	
	if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
		$queryMensagem  = ' INSERT INTO PS6551 (NUMERO_SOLICITACAO, CODIGO_ASSOCIADO, CODIGO_IDENTIFICACAO, DATA_MENSAGEM, HORA_MENSAGEM, TEXTO_MENSAGEM) VALUES ';
		$queryMensagem .= ' (' . aspas($dadosInput['numeroSolicitacao']) . ', ' . aspas($_SESSION['codigoIdentificacao']) . ', null, current_timestamp, DATENAME(HOUR,SYSDATETIME()) + ":" + DATENAME(MINUTE,SYSDATETIME()), ' . aspas($dadosInput['novaMensagem']) . ')';
	}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){
		$queryMensagem  = ' INSERT INTO PS6551 (NUMERO_SOLICITACAO, CODIGO_ASSOCIADO, CODIGO_PRESTADOR, CODIGO_IDENTIFICACAO, DATA_MENSAGEM, HORA_MENSAGEM, TEXTO_MENSAGEM) VALUES ';
		$queryMensagem .= ' (' . aspas($dadosInput['numeroSolicitacao']) . ', ' . aspas($dadosInput['codigoAssociado']) . ', ' . aspas($_SESSION['codigoIdentificacao']) . ', null, current_timestamp, DATENAME(HOUR,SYSDATETIME()) + ":" + DATENAME(MINUTE,SYSDATETIME()), ' . aspas($dadosInput['novaMensagem']) . ')';
	}elseif($_SESSION['perfilOperador'] == 'OPERADOR'){
		$queryMensagem  = ' INSERT INTO PS6551 (NUMERO_SOLICITACAO, CODIGO_ASSOCIADO, CODIGO_IDENTIFICACAO, DATA_MENSAGEM, HORA_MENSAGEM, TEXTO_MENSAGEM) VALUES ';
		$queryMensagem .= ' (' . aspas($dadosInput['numeroSolicitacao']) . ', ' . aspas($dadosInput['codigoAssociado']) . ', ' . aspas($_SESSION['codigoIdentificacao']) . ', current_timestamp, DATENAME(HOUR,SYSDATETIME()) + ":" + DATENAME(MINUTE,SYSDATETIME()), ' . aspas($dadosInput['novaMensagem']) . ')';
	}
		
	if (!jn_query($queryMensagem)){
		$retorno['STATUS'] = 'ERRO';			
	}else{
		$retorno['STATUS'] = 'OK';						
	}
	
}

?>