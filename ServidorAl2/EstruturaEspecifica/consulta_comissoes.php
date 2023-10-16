<?php
require('../lib/base.php');

require('../private/autentica.php');


if($dadosInput['tipo'] =='carregaDados'){

	$queryContratos  = ' select PS1000.CODIGO_ASSOCIADO from ps3100 ';
	$queryContratos .= ' inner join ps1002 on (ps3100.numero_contrato = ps1002.numero_contrato) ';
	$queryContratos .= ' inner join ps1000 on (ps1000.codigo_associado = ps1002.codigo_associado) ';
	$queryContratos .= ' where ps3100.codigo_identificacao = ' . aspas($_SESSION['codigoIdentificacao']);
	$resContratos = jn_query($queryContratos);
	$i = 0;
	$adimplente = 0;
	$inadimplente = 0;
	/*
	while($rowContratos = jn_fetch_object($resContratos)){
		$queryAssoc  = ' SELECT FIRST 1 DATA_PAGAMENTO FROM PS1020 ';
		$queryAssoc .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowContratos->CODIGO_ASSOCIADO);
		$queryAssoc .= ' AND DATA_VENCIMENTO <= CURRENT_TIMESTAMP ';
		$resAssoc = jn_query($queryAssoc);
		$rowAssoc = jn_fetch_object($resAssoc);

		if($rowAssoc->DATA_PAGAMENTO){
			$adimplente++;
		}else{
			$inadimplente++;
		}

		$i++;
	}
	*/
	$mensagem = '';

	if($adimplente >= $inadimplente){		
		$mensagem = 'POSITIVO';	
	}else{		
		$mensagem = 'NEGATIVO';	
	}

	$queryPrincipal  = " SELECT ";
	$queryPrincipal .= " 	NUMERO_CONTRATO, NOME_ASSOCIADO, CODIGO_IDENTIFICACAO, VALOR_CONTRATO, DATA_CONTRATO "; 
	$queryPrincipal .= " FROM VW_COMISSOES_NET ";
	$queryPrincipal .= " WHERE VW_COMISSOES_NET.CODIGO_IDENTIFICACAO = " . aspas($_SESSION['codigoIdentificacao']);								
	$resultQuery    = jn_query($queryPrincipal);		
	
	$dadosGrid = array();	
	
	$item['NOME_CAMPO'] = "NUMERO_CONTRATO";
	$item['LABEL'] = 'Nº Contrato';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NOME_ASSOCIADO";
	$item['LABEL'] = 'Nome Contratante';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "DATA_CONTRATO";
	$item['LABEL'] = 'Data Contrato';
	$item['TIPO_CAMPO'] = "DATE";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "CODIGO_IDENTIFICACAO";
	$item['LABEL'] = 'Vendedor';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "VALOR_CONTRATO";
	$item['LABEL'] = 'Valor Contrato';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	
	while ($rowPrincipal = jn_fetch_object($resultQuery)){
		$dadoLinha = array();
		
		foreach ($rowPrincipal as $key => $value){
			$dadoLinha[$key] = jn_utf8_encode($value);
		}
		
		$dadoLinha['ALTERAR'] = '';
		$dadoLinha['EXCLUIR'] = '';
		$dadoLinha['CHECK'] = 'N';
		$dadosGrid[] = $dadoLinha;		
	}
	$retorno['DADOS_GRID']  = $dadosGrid;	
	$retorno['INFO_GRID']  = $infoGrid;
	$retorno['STATUS_CARTEIRA']  = $mensagem;
	
	echo json_encode($retorno);

}






?>