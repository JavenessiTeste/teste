<?php
require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='carregaDados'){
	
	$query  = 'SELECT NUMERO_REGISTRO,CODIGO_ASSOCIADO, CODIGO_EMPRESA, (CAST(REPLICATE("0", 2 - LEN(DAY(DATA_VENCIMENTO))) + RTrim(DAY(DATA_VENCIMENTO)) AS CHAR(2)) + "/" + CAST(REPLICATE("0", 2 - LEN(MONTH(DATA_VENCIMENTO))) + RTrim(MONTH(DATA_VENCIMENTO)) AS CHAR(2)) + "/" + CAST(YEAR(DATA_VENCIMENTO) AS VARCHAR(4))) AS DATA_VENCIMENTO, VALOR_FATURA, DATA_PAGAMENTO, ';
	$query .= 'MES_REFERENCIA, "https://app.plenasaude.com.br/AliancaAppNet2/ServidorAl2/" || LINK_BOLETO as LINK_BOLETO FROM VW_BOLETOS_NET ';
	$query .= 'WHERE 1 = 1 ';
	$query .= " and data_pagamento is null ";


	if ($_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF')
		$query .= 'AND (Codigo_Associado = ' . aspas($_SESSION['codigoIdentificacao']) . ') ';
	else
		$query .= 'AND (Codigo_Empresa = ' . aspas($_SESSION['codigoIdentificacao']) . ') ';

	$query .= 'ORDER BY NUMERO_REGISTRO ';
	$res = jn_query($query);
	
	$dadosGrid = array();

	$item['NOME_CAMPO'] = "CHECK";
	$item['LABEL'] = 'Selecionar';
	$item['TIPO_CAMPO'] = "CHECK";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	
	$item['NOME_CAMPO'] = "DATA_VENCIMENTO";
	$item['LABEL'] = 'Data Vencimento';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "VALOR_FATURA";
	$item['LABEL'] = 'Valor Fatura';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;

	$item['NOME_CAMPO'] = "MES_REFERENCIA";
	$item['LABEL'] = 'Mês Referência';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
		
	$item['NOME_CAMPO'] = "LINK_BOLETO";
	$item['LABEL'] = 'Visualiza Boleto';
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
		$dadosGrid[] = $dadoLinha;		
	}
	
	$retorno['DADOS_GRID']  = $dadosGrid;
	$retorno['INFO_GRID']  = $infoGrid;
	
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='pagamento'){		
	$quantFaturas = count($dadosInput['faturas']);
	
	if($quantFaturas > 1){
		$identificacao = md5($_SESSION['codigoIdentificacao'].time());
		
		$erro = '';
		$i = 0;
		foreach($dadosInput['faturas'] as $faturas){
			$selectFatura = "Select NUMERO_REGISTRO from VW_BOLETOS_NET where 1=1 ";
			$selectFatura .= " and data_pagamento is null ";
			
			if ($_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF'){
				$selectFatura .= " and CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacao']);
			}else if($_SESSION['perfilOperador'] == 'EMPRESA'){
				$selectFatura .= " and CODIGO_EMPRESA = ".aspas($_SESSION['codigoIdentificacao']);
			}
			
			$selectFatura .= " and numero_registro = ". aspas($dadosInput['faturas'][$i]);
			
			$resFatura = jn_query($selectFatura);
			
			if($rowFatura = jn_fetch_object($resFatura)){
				$chave = jn_gerasequencial('ESP_FATURAS_AGRUPADAS');
				$insert  = " INSERT INTO ESP_FATURAS_AGRUPADAS(NUMERO_REGISTRO,";
				
				if ($_SESSION['perfilOperador'] == 'BENEFICIARIO' || $_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF')
					$insert .= "CODIGO_ASSOCIADO,";	
				if ($_SESSION['perfilOperador'] == 'EMPRESA' || $_SESSION['perfilOperador'] == 'EMPRESA_CNPJ')
					$insert .= "CODIGO_EMPRESA,";	
				
				$insert .= " NUMERO_AGRUPAMENTO,NUMERO_REGISTRO_PS1020)
							 VALUES(".aspas($chave).",".
							          aspas($_SESSION['codigoIdentificacao']).",".
									  aspas($identificacao).",".
									  aspas($rowFatura->NUMERO_REGISTRO).")";
				jn_query($insert);
 			
			}
			$i++;
		}	
		
		if($erro == ""){
			$retorno['STATUS'] = 'OK';
			$retorno['LINK'] = 'https://app.plenasaude.com.br/AliancaNet/services/efetuarPagamento.php?tipo=FA&id='.$identificacao;			
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG'] = $erro;		
		}	
	}else{
		$retorno['STATUS'] = 'OK';
		$retorno['LINK'] = 'https://app.plenasaude.com.br/AliancaNet/services/efetuarPagamento.php?tipo=F&reg='.$dadosInput["faturas"][0];			
	}
				
	
	echo json_encode($retorno);
	
}


?>