<?php
require('../lib/base.php');

if($dadosInput['tipo']== 'pesquisaTitulos'){
	
	$queryAssoc = 'SELECT CODIGO_TIPO_CARACTERISTICA , FLAG_PLANOFAMILIAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	$resAssoc = jn_query($queryAssoc);
	$tipoPlano = '';
	if($rowAssoc = jn_fetch_object($resAssoc)){
		if($rowAssoc->CODIGO_TIPO_CARACTERISTICA == '10' && $rowAssoc->FLAG_PLANOFAMILIAR == 'N'){
			$tipoPlano = 'ODONTOPJ';
		}elseif($rowAssoc->CODIGO_TIPO_CARACTERISTICA == '10' && $rowAssoc->FLAG_PLANOFAMILIAR == 'S'){
			$tipoPlano = 'ODONTOPF';		
		}elseif($rowAssoc->CODIGO_TIPO_CARACTERISTICA != '10' && $rowAssoc->FLAG_PLANOFAMILIAR == 'N'){
			$tipoPlano = 'SAUDEPJ';
		}elseif($rowAssoc->CODIGO_TIPO_CARACTERISTICA != '10' && $rowAssoc->FLAG_PLANOFAMILIAR == 'S'){
			$tipoPlano = 'SAUDEPF';		
		}
	}
		
	$queryPerguntas = ' SELECT * FROM ESP_TITULO_FAQ ';

	if($tipoPlano == ''){
		if($_GET['tipo'] != 'AMBOS'){
			$queryPerguntas .= ' WHERE ((TIPO_PRODUTO IS NULL) OR (TIPO_PRODUTO = "TODOS") OR (TIPO_PRODUTO = ' . aspas($_GET['tipo']) . ' ))';
		}
	}elseif($tipoPlano == 'ODONTOPJ'){
		$queryPerguntas .= ' WHERE ((TIPO_PRODUTO IS NULL) OR (TIPO_PRODUTO = "AMBOS") OR (TIPO_PRODUTO = "ODONTOPJ") OR (TIPO_PRODUTO = "ODONTO"))';	
	}elseif($tipoPlano == 'ODONTOPF'){
		$queryPerguntas .= ' WHERE ((TIPO_PRODUTO IS NULL) OR (TIPO_PRODUTO = "AMBOS") OR (TIPO_PRODUTO = "ODONTOPF") OR (TIPO_PRODUTO = "ODONTO"))';	
	}elseif($tipoPlano == 'SAUDEPJ'){
		$queryPerguntas .= ' WHERE ((TIPO_PRODUTO IS NULL) OR (TIPO_PRODUTO = "AMBOS") OR (TIPO_PRODUTO = "SAUDEPJ") OR (TIPO_PRODUTO = "SAUDE"))';	
	}elseif($tipoPlano == 'SAUDEPF'){
		$queryPerguntas .= ' WHERE ((TIPO_PRODUTO IS NULL) OR (TIPO_PRODUTO = "AMBOS") OR (TIPO_PRODUTO = "SAUDEPF") OR (TIPO_PRODUTO = "SAUDE"))';	
	}

	$resPerguntas = jn_query($queryPerguntas);

	$retorno = array();
	$linha = '';
	while($rowPerguntas = jn_fetch_object($resPerguntas)){
				
		$linha['REGISTRO'] = $rowPerguntas->NUMERO_REGISTRO;
		$linha['TITULO'] = jn_utf8_encode($rowPerguntas->TITULO_FAQ);
		$linha['DESCRICAO'] = jn_utf8_encode($rowPerguntas->DESCRICAO_FAQ);
		
		$retorno[] = $linha;
	}  
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'pesquisaPerguntas'){
	$linha = '';
	
	$queryTitulo  = ' SELECT * FROM ESP_TITULO_FAQ';
	$queryTitulo .= ' WHERE NUMERO_REGISTRO = ' . aspas($dadosInput['numeroTitulo']);
	$resTitulo = jn_query($queryTitulo);
	$rowTitulo = jn_fetch_object($resTitulo);
	$linha['TITULO_PERGUNTA'] = jn_utf8_encode($rowTitulo->TITULO_FAQ);
	
	$queryPerguntas  = ' SELECT * FROM ESP_PERGUNTAS_FAQ ';
	$queryPerguntas .= ' WHERE NUMERO_TITULO = ' . aspas($dadosInput['numeroTitulo']);
	$resPerguntas = jn_query($queryPerguntas);

	$retorno = array();
	
	while($rowPerguntas = jn_fetch_object($resPerguntas)){
		
		$linha['TITULO'] = jn_utf8_encode($rowPerguntas->TITULO_POSTAGEM);
		$linha['DESCRICAO'] = jn_utf8_encode($rowPerguntas->DESCRICAO_POSTAGEM);
		
		$retorno[] = $linha;
	}  
	echo json_encode($retorno);
}

?>