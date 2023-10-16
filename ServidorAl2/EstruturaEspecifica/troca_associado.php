<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] =='dados'){
	
	$queryCpf = 'SELECT NUMERO_CPF FROM PS1000 ';
	$queryCpf .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	$resCpf = jn_query($queryCpf);
	$rowCpf = jn_fetch_object($resCpf);		
	
	$queryCadastros  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES FROM PS1000 ';
	$queryCadastros .= ' INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryCadastros .= ' INNER JOIN PS1000 T ON (T.CODIGO_ASSOCIADO = PS1000.CODIGO_TITULAR) ';
	$queryCadastros .= ' WHERE ((PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']) . ') ';
	$queryCadastros .= ' OR (PS1000.CODIGO_TITULAR = ' . aspas($_SESSION['codigoIdentificacaoTitular']) . ') ';
	$queryCadastros .= ' OR (PS1000.NUMERO_CPF = ' . aspas($rowCpf->NUMERO_CPF) . ')) ';

	if(retornaValorConfiguracao('ACEITA_EXC_12MESES') == 'SIM'){
		$queryCadastros .= ' AND (
									(T.DATA_EXCLUSAO IS NULL) OR  
									(EXTRACT(YEAR FROM T.DATA_EXCLUSAO) = ' . aspas(date('Y')) . ' ) OR 
									(EXTRACT(YEAR FROM T.DATA_EXCLUSAO) = ' . aspas(date('Y') - 1) . ' )
								 ) ';
		
		$queryCadastros .= ' AND (
									(PS1000.DATA_EXCLUSAO IS NULL) OR  
									(EXTRACT(YEAR FROM PS1000.DATA_EXCLUSAO) = ' . aspas(date('Y')) . ' ) OR 
									(EXTRACT(YEAR FROM PS1000.DATA_EXCLUSAO) = ' . aspas(date('Y') - 1) . ' )
								 ) ';
	}else{
		$queryCadastros .= ' AND T.DATA_EXCLUSAO IS NULL AND PS1000.DATA_EXCLUSAO IS NULL ';
	}

	$resCadastros = jn_query($queryCadastros); 
	
	while($rowCadastros    = jn_fetch_object($resCadastros)){		
		
		$item['CODIGO_ASSOCIADO']  	= $rowCadastros->CODIGO_ASSOCIADO;
		$item['NOME_ASSOCIADO']  	= $rowCadastros->NOME_ASSOCIADO;
		$item['CODIGO_PLANO']  		= $rowCadastros->CODIGO_PLANO;
		$item['NOME_PLANO'] 		= $rowCadastros->NOME_PLANO_FAMILIARES;		
		$item['SELECIONADO'] 		= $rowCadastros->CODIGO_ASSOCIADO == $_SESSION['codigoIdentificacao'];
		

		$retorno['ASSOCIADOS'][] = $item;
	}
	
	echo json_encode($retorno);
}

if($dadosInput['tipo'] =='associadoAlterar'){
	$queryAssociado = 'SELECT * FROM PS1000 WHERE CODIGO_ASSOCIADO = ' .aspas($dadosInput['codAssociado']);
	$resAssociado = jn_query($queryAssociado);
	
	if($rowAssociado = jn_fetch_object($resAssociado)){
		$codigoAssociado = $rowAssociado->CODIGO_ASSOCIADO;
		$codigoTitular = $rowAssociado->CODIGO_TITULAR;
		$nomeAssociado = jn_utf8_encode($rowAssociado->NOME_ASSOCIADO);
	}
			
	$_SESSION['codigoIdentificacao'] = $codigoAssociado;
	$_SESSION['codigoIdentificacaoTitular'] = $codigoTitular;
	$_SESSION['nomeUsuario'] = $nomeAssociado;	

	$resultado['MSG'] =  'Alterado para associado(a): ' . $nomeAssociado;
	$resultado['DADOS']['codigoIdentificacao'] 			=  $_SESSION['codigoIdentificacao'];
	$resultado['DADOS']['codigoIdentificacaoTitular'] 	= $_SESSION['codigoIdentificacaoTitular'];	
	$resultado['DADOS']['nomeUsuario'] 					= $_SESSION['nomeUsuario']; 
	$resultado['DESTINO'] 								= 'site/paginaInicial';
	$resultado['DESTINO_JSON']['tabela'] 				= 'VW_COMUNICACAO_NET_AL2';
	$resultado['DESTINO_JSON']['titulo'] 				= 'Mensagens e Noticias';
	$resultado['DESTINO_JSON']['filtros'] 				= '[]';
	
	echo json_encode($resultado);
}





?>