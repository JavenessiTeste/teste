<?php
require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='carregaDados'){
	
	$query  = ' SELECT NUMERO_REGISTRO, NUMERO_CONTA, DESCRICAO_CONTA, VALOR_CONTA,  ';
	$query .= ' (CAST(REPLICATE("0", 2 - LEN(DAY(DATA_VENCIMENTO))) + RTrim(DAY(DATA_VENCIMENTO)) AS CHAR(2)) + "/" + CAST(REPLICATE("0", 2 - LEN(MONTH(DATA_VENCIMENTO))) + RTrim(MONTH(DATA_VENCIMENTO)) AS CHAR(2)) + "/" + CAST(YEAR(DATA_VENCIMENTO) AS VARCHAR(4))) AS DATA_VENCIMENTO ';
	$query .= ' FROM VW_CONTAS_IMPORTAR_AL2 ';
	$query .= ' WHERE 1 = 1 ';
	$query .= ' ORDER BY NUMERO_REGISTRO';
	$res = jn_query($query);
	
	$dadosGrid = array();

	$item['NOME_CAMPO'] = "CHECK";
	$item['LABEL'] = 'Selecionar';
	$item['TIPO_CAMPO'] = "CHECK";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	
	$item['NOME_CAMPO'] = "NUMERO_CONTA";
	$item['LABEL'] = 'Número Conta';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "DESCRICAO_CONTA";
	$item['LABEL'] = 'Descrição Conta';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;

	$item['NOME_CAMPO'] = "VALOR_CONTA";
	$item['LABEL'] = 'Valor Conta';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;

	$item['NOME_CAMPO'] = "DATA_VENCIMENTO";
	$item['LABEL'] = 'Data Vencimento';
	$item['TIPO_CAMPO'] = "";
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
		$dadosGrid[] = $dadoLinha;		
	}
	
	$retorno['DADOS_GRID']  = $dadosGrid;
	$retorno['INFO_GRID']  = $infoGrid;
	
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='enviar'){

	$erro = false;
	foreach($dadosInput['contasEnviadas'] as $registroPs7201){	
		
		$queryPs7201  = ' SELECT PS7200.CODIGO_IDENTIFICACAO, PS7200.CODIGO_PRESTADOR, PS7201.VALOR_CONTA, PS7200.CODIGO_CENTRO_CUSTO FROM PS7201 ';
		$queryPs7201 .= ' INNER JOIN PS7200 ON (PS7201.CODIGO_CONTA = PS7200.CODIGO_CONTA)';
		$queryPs7201 .= ' WHERE PS7201.NUMERO_REGISTRO = ' . aspas($registroPs7201);
		$resPs7201 = jn_query($queryPs7201);
		$rowPs7201 = jn_fetch_object($resPs7201);		

		$NumSolicitacao =  jn_gerasequencial('ESP_AUDITORIA_PAGAMENTOS_NET');	
		$date = date('d/m/Y');
		$query  = " INSERT INTO ESP_AUDITORIA_PAGAMENTOS_NET(NUMERO_SOLICITACAO, CODIGO_OPERADOR_SOLICITACAO, CODIGO_FORNECEDOR, CODIGO_PRESTADOR, ";
		$query .= " VALOR_SOLICITADO, TIPO_AUDITORIA, DATA_SOLICITACAO, STATUS_AUDITORIA, CODIGO_CENTRO_CUSTO, DEPARTAMENTO, OBSERVACAO, NUMERO_REGISTRO_PS7201) ";
		$query .= " Values( ";
		$query .= aspas($NumSolicitacao) . ", ";
		$query .= aspas($_SESSION['codigoIdentificacao']) . ", ";
		$query .= aspas($rowPs7201->CODIGO_IDENTIFICACAO) . ", ";
		$query .= aspas($rowPs7201->CODIGO_PRESTADOR) . ", ";
		$query .= aspas($rowPs7201->VALOR_CONTA) . ", ";
		$query .= aspas('P') . ", ";
		$query .= dataToSql($date) . ", ";
		$query .= aspas('P') . ", ";
		$query .= aspas($rowPs7201->CODIGO_CENTRO_CUSTO) . ", ";
		$query .= aspas('F') . ", ";
		$query .= aspas('REGISTRO IMPORTADO') . ", ";
		$query .= aspas($registroPs7201) .") ";	

		if(!jn_query($query)){
			$erro = true;
		}
	}				
	
	if($erro){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'Erro ao importar algum registro';
	}else{
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = 'Registros importados';
	}
	

	echo json_encode($retorno);
	
}


?>