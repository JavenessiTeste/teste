<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] =='dados'){
	
	$queryPagamento  = ' Select NUMERO_SOLICITACAO, CODIGO_OPERADOR_SOLICITACAO, CODIGO_OPERADOR_AUTORIZACAO, Coalesce(Codigo_Fornecedor, A.Codigo_Prestador) as CODIGO_FORNECEDOR, A.CODIGO_CENTRO_CUSTO, ';
	$queryPagamento .= ' A.FLAG_IMPORT_AUDITORIA, A.FLAG_APROVADO_NIVEL1, A.CODIGO_OPERADOR_AUTORIZACAO_N1,  ';
	$queryPagamento .= ' TIPO_AUDITORIA, VALOR_SOLICITADO, OPERADOR.NOME_USUAL AS NOME_OPERADOR, COALESCE(FORNECEDOR.NOME_USUAL, PRESTADOR.NOME_PRESTADOR) AS NOME_FORNECEDOR, DATA_SOLICITACAO, DATA_AUTORIZACAO, STATUS_AUDITORIA, OBSERVACAO ';
	$queryPagamento .= ' From Esp_Auditoria_Pagamentos_Net A ';
	$queryPagamento .= ' Inner Join Ps1100 as Operador On (Operador.Codigo_Identificacao = A.Codigo_Operador_Solicitacao) ';	
	$queryPagamento .= ' Left Outer Join Ps1100 as Fornecedor On (Fornecedor.Codigo_Identificacao = A.Codigo_Fornecedor) ';	
	$queryPagamento .= ' Left Outer Join Ps5000 as Prestador On (Prestador.Codigo_Prestador = A.Codigo_Prestador) ';	
	$queryPagamento .= ' Where Tipo_Auditoria = ' . aspas('P');
	$queryPagamento .= ' And A.Status_Auditoria = ' . aspas('P');
	
	$resPagamento = jn_query($queryPagamento); 
	
	while($rowPagamento    = jn_fetch_object($resPagamento)){		

		$fornecedorFinal = '';
		$forncedor2 = '';
		
		if($rowPagamento->CODIGO_FORNECEDOR == '10035'){
			$forn = explode('/n',$rowPagamento->OBSERVACAO);
			if($_SESSION['codigoIdentificacao'] == '2566'){				
				$forncedor2 = explode('Fornecedor:  ',$forn[2]);								
			}
				
		}

		if($forncedor2[1]){
			$fornecedorFinal = $forncedor2[1];
		}else{
			$fornecedorFinal = $rowPagamento->NOME_FORNECEDOR;		
		}

		$item['NUMERO_CONTA']  		= $rowPagamento->NUMERO_SOLICITACAO;
		$item['DATA_SOLICITACAO']  	= SqlToData($rowPagamento->DATA_SOLICITACAO);
		$item['OPERADOR']  			= jn_utf8_encode($rowPagamento->NOME_OPERADOR);
		$item['CENTRO_CUSTOS']  	= jn_utf8_encode($rowPagamento->CODIGO_CENTRO_CUSTO);
		$item['FORNECEDOR']  		= jn_utf8_encode($fornecedorFinal);
		$item['VALOR_SOLICITADO'] 	= toMoeda($rowPagamento->VALOR_SOLICITADO);

		$retorno['CONTAS'][] = $item;
	}
	
	echo json_encode($retorno);
}

if($dadosInput['tipo'] == 'aprovarConta'){
	$query  = " UPDATE ESP_AUDITORIA_PAGAMENTOS_NET SET ";
	$query .= " STATUS_AUDITORIA = 'A', ";
	$query .= " CODIGO_OPERADOR_AUTORIZACAO = " . aspas($_SESSION['codigoIdentificacao']);
	$query .= " WHERE NUMERO_SOLICITACAO = " . aspas($dadosInput['numeroConta']);
	
	if(jn_query($query)){
		$retorno['MSG'] = 'OK';
	}else{
		$retorno['MSG'] = 'ERRO';	
	}		
}

if($dadosInput['tipo'] == 'negarConta'){
	$query  = " UPDATE ESP_AUDITORIA_PAGAMENTOS_NET SET ";
	$query .= " STATUS_AUDITORIA = 'N', ";
	$query .= " CODIGO_OPERADOR_AUTORIZACAO = " . aspas($_SESSION['codigoIdentificacao']);
	$query .= " WHERE NUMERO_SOLICITACAO = " . aspas($dadosInput['numeroConta']);
	
	if(jn_query($query)){
		$retorno['MSG'] = 'OK';
	}else{
		$retorno['MSG'] = 'ERRO';
	}		
}

?>