<?php
require('../lib/base.php');

$dados = file_get_contents("php://input");
$dados = json_decode($dados, True);

$name  = '../../ServidorCliente/RetornoZsPay/retornoZsPay.log';
$text .= 'Input : '.json_encode($dados)."\n";
$file  = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);

$tipoRetorno = $dados['type'];
$statusRetorno = $dados['status'];
$dadosRetorno = $dados['data'];

//Processos assinatura
if($tipoRetorno == 'invoice'){
	if($statusRetorno=='paid'){
		
		$queryAssoc  = ' SELECT VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.CODIGO_EMPRESA FROM VND1000_ON ';
		$queryAssoc .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssoc .= ' WHERE ID_ZSPAY_ASSINATURA = ' . aspas($dadosRetorno['assinatura_id']);
		$resAssoc = jn_query($queryAssoc);
		$rowAssoc = jn_fetch_object($resAssoc);
		
		$associado = $rowAssoc->CODIGO_ASSOCIADO;  
		$empresa   = $rowAssoc->CODIGO_EMPRESA;
		
		$valorFat1020 = 0;
		$valorFatura = 0;

		$queryCodigos = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($associado);
		$resCodigos = jn_query($queryCodigos);
		while($rowCodigos = jn_fetch_object($resCodigos)){
			$valorAssoc = retornaValorPrevisao($rowCodigos->CODIGO_ASSOCIADO);
			$valorFatura = $valorFatura + $valorAssoc;
		}

		$valorFat1020 = $valorFatura;		
		
		
		$queryVnd1020  = ' INSERT INTO VND1020_ON (CODIGO_ASSOCIADO, CODIGO_EMPRESA, DATA_VENCIMENTO, DATA_PAGAMENTO, VALOR_PAGO, VALOR_FATURA, DATA_EMISSAO, MES_ANO_REFERENCIA, IDENTIFICACAO_GERACAO, NUMERO_PARCELA) ';
		$queryVnd1020 .= ' VALUES ';
		$queryVnd1020 .= " (" . aspas($associado) .  ", " . aspas($empresa) . ", current_timestamp, current_timestamp, " . aspas($valorFat1020) . "," . aspas($valorFat1020) . ", current_timestamp, "; 
		$queryVnd1020 .= " EXTRACT(MONTH FROM current_timestamp) || '/' || EXTRACT(YEAR FROM current_timestamp), 'FAT_VND', " . aspas('1') . ")";		

		jn_query($queryVnd1020);
		
		$filtro = '';	
		
		if($empresa == '400'){
			$filtro = ' AND CODIGO_ASSOCIADO = ' . aspas($associado);
		}else{
			$filtro = ' AND CODIGO_EMPRESA = ' . aspas($empresa);
		}
		
		$query1020  = ' SELECT FIRST 1 NUMERO_REGISTRO FROM VND1020_ON ';	
		$query1020 .= ' WHERE 1=1 ';
		$query1020 .= $filtro;
		$query1020 .= ' ORDER BY NUMERO_REGISTRO DESC ';
		$res1020 = jn_query($query1020);
		$row1020 = jn_fetch_object($res1020);
		$numRegFat = $row1020->NUMERO_REGISTRO;	
		
		$queryCodigos = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($associado);
		$resCodigos = jn_query($queryCodigos);
		while($rowCodigos = jn_fetch_object($resCodigos)){
			$valorAssoc = retornaValorPrevisao($rowCodigos->CODIGO_ASSOCIADO);
			
			$queryVnd1021  = ' INSERT INTO VND1021_ON (CODIGO_ASSOCIADO, CODIGO_EMPRESA, NUMERO_REGISTRO_PS1020, DATA_EMISSAO, MES_ANO_VENCIMENTO, VALOR_FATURA) ';
			$queryVnd1021 .= ' VALUES ';
			$queryVnd1021 .= " (" . aspasNull($associado) .  ", " . aspas($empresa) . ", " . aspas($numRegFat) . ", current_timestamp, EXTRACT(MONTH FROM current_timestamp) || '/' || EXTRACT(YEAR FROM current_timestamp), " . aspas($valorAssoc) . ")";			
			jn_query($queryVnd1021);
		}	
	}
}

//Pagamentos boleto ou cartao
if($tipoRetorno == 'receivable'){
	if($statusRetorno=='paid'){
	}
}

?>