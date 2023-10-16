<?php
require('../lib/base.php');

$codigoAssociado = $_SESSION['codigoIdentificacao'];

if($dadosInput['tipo']== 'dados'){
	
	$queryAssociados  = ' SELECT ';
	$queryAssociados .= '	TMP1000_NET.CODIGO_ASSOCIADO, TMP1000_NET.NOME_ASSOCIADO ';
	$queryAssociados .= ' FROM TMP1000_NET ';
	$queryAssociados .= ' WHERE TMP1000_NET.CODIGO_TITULAR = ' . aspas($codigoAssociado); 	
	$resAssociados = jn_query($queryAssociados); 
	$retornoJson    = '[';
	
	while($rowAssociados = jn_fetch_object($resAssociados)){
		if ($retornoJson != '[')
				$retornoJson .= ',';
			
		$queryPerguntas  = ' SELECT ';		
		$queryPerguntas .= '	PS1039.NUMERO_PERGUNTA, PS1039.DESCRICAO_PERGUNTA, TMP1005_NET.RESPOSTA_DIGITADA, ';
		$queryPerguntas .= '	TMP1005_NET.DESCRICAO_OBSERVACAO ';
		$queryPerguntas .= ' FROM TMP1000_NET ';
		$queryPerguntas .= ' INNER JOIN PS1039 ON TMP1000_NET.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
		$queryPerguntas .= ' LEFT  JOIN TMP1005_NET ON (TMP1005_NET.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA AND TMP1005_NET.CODIGO_ASSOCIADO = TMP1000_NET.CODIGO_ASSOCIADO) ';
		$queryPerguntas .= ' WHERE TMP1000_NET.CODIGO_ASSOCIADO = ' . aspas($rowAssociados->CODIGO_ASSOCIADO); 	
		$queryPerguntas .= ' ORDER BY PS1039.NUMERO_PERGUNTA '; 	
		$resPerguntas = jn_query($queryPerguntas); 
		
		$retornoJson .= '{"CODIGO_ASSOCIADO":"' . $rowAssociados->CODIGO_ASSOCIADO . '","NOME_ASSOCIADO":"' . jn_utf8_encode($rowAssociados->NOME_ASSOCIADO) . '", "PERGUNTAS":[';
		
		$i = 0;
		while($rowPerguntas = jn_fetch_object($resPerguntas)){
			if($i > 0)
				$retornoJson .= ',';
			
			$respostaNao = '';
			if($rowPerguntas->RESPOSTA_DIGITADA != 'S'){
				$respostaNao = 'S';
			}
			
			$retornoJson .= '{"NUMERO_PERGUNTA":"' . $rowPerguntas->NUMERO_PERGUNTA . '","DESCRICAO_PERGUNTA":"' . strtoupper(jn_utf8_encode($rowPerguntas->DESCRICAO_PERGUNTA)) . '","RESPOSTA_SIM":"' . $rowPerguntas->RESPOSTA_DIGITADA . '","RESPOSTA_NAO":"' . $respostaNao . '","DESCRICAO_OBSERVACAO":"' . strtoupper(jn_utf8_encode($rowPerguntas->DESCRICAO_OBSERVACAO)) . '"} ';
			$i++;
		}
		
		$retornoJson .= ' 	] ';
		$retornoJson .= ' } ';
	}
	
	$retornoJson .=  ']';
	
	echo $retornoJson;
}

if($dadosInput['tipo']== 'dadosBenef'){
	$queryBenef  = ' SELECT ';
	$queryBenef .= '	TMP1000_NET.NOME_ASSOCIADO, PS1030.NOME_PLANO_FAMILIARES ';
	$queryBenef .= ' FROM TMP1000_NET ';
	$queryBenef .= ' INNER JOIN PS1030  ON TMP1000_NET.CODIGO_PLANO = PS1030.CODIGO_PLANO ';	
	$queryBenef .= ' WHERE TMP1000_NET.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado); 		
	$resBenef = jn_query($queryBenef); 
		
	$rowBenef = jn_fetch_object($resBenef);
	$perguntas['NOME_ASSOCIADO'] 	= jn_utf8_encode($rowBenef->NOME_ASSOCIADO);
	$perguntas['NOME_PLANO'] 		= jn_utf8_encode($rowBenef->NOME_PLANO_FAMILIARES);	
		
	echo json_encode($perguntas);
}

if($dadosInput['tipo']== 'salvar'){
	jn_query('DELETE FROM TMP1005_NET WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM TMP1000_NET WHERE CODIGO_TITULAR = ' . aspas($codigoAssociado) . ')');
	
	$retorno = Array();
	foreach($dadosInput['dadosSalvar'] as $dadosPergunta){			
		
		$query  = 'INSERT INTO TMP1005_NET (CODIGO_ASSOCIADO, NUMERO_PERGUNTA, ';
		$query .= 'RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO) ';
		$query .= 'VALUES ( ';
		$query .= aspas($dadosPergunta['CODIGO_ASSOCIADO']) . ", ";
		$query .= aspas($dadosPergunta['NUMERO_PERGUNTA']) . ', ';
		$query .= aspas('S') . ', ';
		$query .= aspas(utf8_decode($dadosPergunta['DESCRICAO_OBSERVACAO']));
		$query .= ' ) ';
		
		if (!jn_query($query)) {
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'Nao foi possivel gravar a pergunta -' . $dadosPergunta['NUMERO_PERGUNTA'];				
			return false; // saio retornando false
		}
	}

	$retorno['STATUS'] = 'OK';
	echo json_encode($retorno);	
}
?>