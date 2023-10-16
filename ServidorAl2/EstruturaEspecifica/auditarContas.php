<?php
require('../lib/base.php');

require('../private/autentica.php');


if($dadosInput['tipo'] =='carregaDados'){

	$query  = ' Select A.NUMERO_SOLICITACAO, PS7010.DESCRICAO_CENTRO_CUSTO, A.STATUS_AUDITORIA, A.DATA_SOLICITACAO,  ';
	$query .= '  VALOR_SOLICITADO, OPERADOR.NOME_USUAL AS NOME_OPERADOR, COALESCE(FORNECEDOR.NOME_USUAL, PRESTADOR.NOME_PRESTADOR) AS NOME_FORNECEDOR ';
	$query .= ' FROM ESP_AUDITORIA_PAGAMENTOS_NET A ';
	$query .= ' INNER JOIN PS1100 AS OPERADOR ON (OPERADOR.CODIGO_IDENTIFICACAO = A.CODIGO_OPERADOR_SOLICITACAO) ';	
	$query .= ' LEFT OUTER JOIN PS1100 AS FORNECEDOR ON (FORNECEDOR.CODIGO_IDENTIFICACAO = A.CODIGO_FORNECEDOR) ';	
	$query .= ' LEFT OUTER JOIN PS5000 AS PRESTADOR ON (PRESTADOR.CODIGO_PRESTADOR = A.CODIGO_PRESTADOR) ';	
	$query .= ' LEFT OUTER JOIN PS7010 ON (PS7010.CODIGO_CENTRO_CUSTO = A.CODIGO_CENTRO_CUSTO) ';	
	$query .= ' WHERE TIPO_AUDITORIA = ' . aspas('P');
	$query .= ' And A.STATUS_AUDITORIA <> "A"  ';
	$query .= ' And A.STATUS_AUDITORIA <> "N"  ';
	$query .= ' And A.CODIGO_OPERADOR_AUTORIZACAO IS NULL  ';
	$query .= ' And A.CODIGO_CENTRO_CUSTO IS NOT NULL ';
	$query .= ' And A.DATA_SOLICITACAO >= "01.01.2020" ';
	
	if ($_POST["pesquisa_operador"]){
		$query  .= ' And (A.Codigo_Operador_Solicitacao = '  . aspas($_POST["pesquisa_operador"]) . ') ';
		$filtros .= '&Codigo_Operador_Solicitacao='  . $_POST["pesquisa_operador"];
	}
	
	if ($_POST["pesquisa_fornecedor"]){
		$query  .= ' And (A.Codigo_Fornecedor = '  . aspas($_POST["pesquisa_fornecedor"]) . ') ';
		$filtros .= '&Codigo_Fornecedor='  . $_POST["pesquisa_fornecedor"];
	}
/*
	if ($_POST["data_busca_inicio"] != "" and $_POST["data_busca_inicio"] != " "){			
		$query  .= ' And (A.Data_Solicitacao >= ' . DataToSql($_POST["data_busca_inicio"]) . ') ';
		$filtros .= '&Data_Solicitacao_Inicial=' . $_POST["data_busca_inicio"];
	}
		
	if ($_POST["data_busca_fim"] != "" and $_POST["data_busca_fim"] != " "){
		$query  .= ' And (A.Data_Solicitacao <= ' . DataToSql($_POST["data_busca_fim"]) . ') ';
		$filtros .= '&Data_Solicitacao_Final=' . $_POST["data_busca_fim"];
		
	}
	*/
	if ($_POST['status_auditoria'] != 'Todos' && $_POST['status_auditoria'] != ''){
		$query  .= " And (A.Status_Auditoria = ". aspas($_POST['status_auditoria']) . ") ";
	}
	
	if ($_POST["departamento"]){
		$query  .= ' And (A.Departamento = '  . aspas($_POST["departamento"]) . ') ';
		$filtros .= '&Departamento='  . $_POST["departamento"];
	}
	
	$query  .= " ORDER BY A.DATA_SOLICITACAO DESC ";
	
	$res = jn_query($query);	
	
	$dadosGrid = array();
	

	$item['NOME_CAMPO'] = "CHECK";
	$item['LABEL'] = 'Selecionar';
	$item['TIPO_CAMPO'] = "CHECK";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	
	$item['NOME_CAMPO'] = "NUMERO_SOLICITACAO";
	$item['LABEL'] = 'Numero Solicitação';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "STATUS_AUDITORIA";
	$item['LABEL'] = 'Situação';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	/*
	$item['NOME_CAMPO'] = "DATA_SOLICITACAO";
	$item['LABEL'] = 'Data Solicitação';
	$item['TIPO_CAMPO'] = "DATE";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	*/
	
	$item['NOME_CAMPO'] = "NOME_OPERADOR";
	$item['LABEL'] = 'Operador';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NOME_FORNECEDOR";
	$item['LABEL'] = 'Fornecedor';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "DESCRICAO_CENTRO_CUSTO";
	$item['LABEL'] = 'Centro de Custos';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "VALOR_SOLICITADO";
	$item['LABEL'] = 'Valor Solicitado';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "APROVAR";
	$item['LABEL'] = 'Aprovar';
	$item['TIPO_CAMPO'] = "BUTTON";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "PARECER";
	$item['LABEL'] = 'Ver Parecer';
	$item['TIPO_CAMPO'] = "BUTTON";
	$item['GRID'] = 'S';	
	$infoGrid[] = $item;
	
	
	
	while ($row = jn_fetch_object($res)){		
		$dadoLinha = array();
	
		foreach ($row as $key => $value){
			if($value != ''){				
				$dadoLinha[$key] = jn_utf8_encode($value);
			}else{
				$dadoLinha[$key] = $value;				
			}
		}		
				
		$dadoLinha['CHECK'] = 'N';
		$dadoLinha['APROVAR'] = 'ACAO';
		$dadoLinha['PARECER'] = 'ACAO';
		$dadosGrid[] = $dadoLinha;		
	}
	
	//pr($dadosGrid,true);
	$retorno['DADOS_GRID']  = $dadosGrid;
	$retorno['INFO_GRID']  = $infoGrid;
	
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='aprovarVarias'){
	
	$erro = '';
	foreach($dadosInput['contas'] as $solicitacao){		
		$query  = " UPDATE ESP_AUDITORIA_PAGAMENTOS_NET SET ";
		$query .= " STATUS_AUDITORIA = 'A', ";
		$query .= " CODIGO_OPERADOR_AUTORIZACAO = " . aspas($_SESSION['codigoIdentificacao']);
		$query .= " WHERE NUMERO_SOLICITACAO = " . aspas($solicitacao);
		if(!jn_query($query)){
			$erro .= 'ERRO AO IMPORTAR SOLICITACAO: ' . $solicitacao;
		}
	}	
	
	if($erro == ""){
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = 'REGISTROS APROVADOS';
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = $erro;		
	}				
	
	echo json_encode($retorno);
	
}else if($dadosInput['tipo'] =='aprovar'){
	$query  = " UPDATE ESP_AUDITORIA_PAGAMENTOS_NET SET ";
	$query .= " STATUS_AUDITORIA = 'A', ";
	$query .= " CODIGO_OPERADOR_AUTORIZACAO = " . aspas($_SESSION['codigoIdentificacao']);
	$query .= " WHERE NUMERO_SOLICITACAO = " . aspas($dadosInput['solicitacao']);
	if(jn_query($query)){
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = 'REGISTROS APROVADOS';
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'ERRO AO IMPORTAR SOLICITACAO: ' . $dadosInput['solicitacao'];		
	}
	
	echo json_encode($retorno);
}






?>