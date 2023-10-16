<?php
require('../lib/base.php');

$codAssociadoTmp = $_SESSION['codigoIdentificacao'];

if($dadosInput['tipo']== 'salvarDadosCaaps'){
	jn_query('	UPDATE VND1000_ON SET
					CODIGO_PLANO 		= ' . aspas($dadosInput['codigoPlano']) . ', 
					CODIGO_TABELA_PRECO = ' . aspas($dadosInput['codigoPlano']) . '					
				WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp));
	
	$queryEnd = 'SELECT CODIGO_ASSOCIADO FROM VND1001_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resEnd= jn_query($queryEnd);
	if($rowEnd = jn_fetch_object($resEnd)){
		jn_query('	UPDATE VND1001_ON SET
					ENDERECO_EMAIL = ' . aspas($dadosInput['email']) . '					
				WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));	
	}else{
		jn_query('	INSERT INTO VND1001_ON (CODIGO_ASSOCIADO, ENDERECO_EMAIL) VALUES (' . aspas($codAssociadoTmp) . ', ' .   aspas($dadosInput['email']) . ')');				
	}
	

	if($dadosInput['flagEvento'] == 'S'){
		insereEvento($codAssociadoTmp);
	}else{
		$queryAssoc = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$resAssoc = jn_query($queryAssoc);
		while($rowAssoc = jn_fetch_object($resAssoc)){
			jn_query('DELETE FROM VND1003_ON WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssoc->CODIGO_ASSOCIADO));
		}
	}

	$retorno['CODIGO_ASSOCIADO'] = $codAssociadoTmp;
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'planosCaapsML'){
	$query  = ' SELECT CODIGO_PLANO,  NOME_PLANO_FAMILIARES FROM PS1030 WHERE 1 = 1 '  ;
	$query .= ' AND PS1030.CODIGO_PLANO IN (SELECT VND1030CONFIG_ON.CODIGO_PLANO FROM VND1030CONFIG_ON WHERE VND1030CONFIG_ON.codigos_modelo_contrato ="36")';
	
	if($dadosInput['codigoPlano'] == '506'){
		$query .= ' AND PS1030.CODIGO_PLANO = "506" ';
	}else{
		$query .= ' AND PS1030.CODIGO_PLANO <> "506" ';
	}

	$query .= ' ORDER BY NOME_PLANO_FAMILIARES';
	$res = jn_query($query);
	
	$retorno['PLANOS'] = array();
	while($row = jn_fetch_object($res)){
		$linha['CODIGO'] = jn_utf8_encode($row->CODIGO_PLANO);
		$linha['DESC']  = jn_utf8_encode(ucwords($row->NOME_PLANO_FAMILIARES)); 	
		$retorno['PLANOS'][] =$linha; 		
	}

	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'buscaPlanoAtual'){
	$query  = ' SELECT CODIGO_PLANO FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);	
	$res = jn_query($query);		
	$row = jn_fetch_object($res);
	$retorno['CODIGO_PLANO'] = $row->CODIGO_PLANO; 
	
	$query  = ' SELECT CODIGO_EVENTO FROM VND1003_ON WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);	
	$res = jn_query($query);		
	$row = jn_fetch_object($res);
	$retorno['CODIGO_EVENTO'] = $row->CODIGO_EVENTO; 

	$query  = ' SELECT ENDERECO_EMAIL FROM VND1001_ON WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);	
	$res = jn_query($query);		
	$row = jn_fetch_object($res);
	$retorno['ENDERECO_EMAIL'] = $row->ENDERECO_EMAIL; 
	
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'validacoesCampos'){
	$queryValidacao  = ' SELECT * FROM VND1000_ON ';
	$queryValidacao .= ' LEFT OUTER JOIN VND1001_ON ON (VND1000_ON.CODIGO_TITULAR = VND1001_ON.CODIGO_ASSOCIADO ) ';
	$queryValidacao .= ' WHERE CODIGO_TITULAR = ' . aspas($_SESSION['codigoIdentificacao']);
	
	$resValidacao = jn_query($queryValidacao);
	$retorno['ERRO'] = false;
	while($rowValidacao = jn_fetch_object($resValidacao)){
		if(!$rowValidacao->NUMERO_TELEFONE_01){
			$retorno['ERRO_TELEFONE'] = 'O titular não está com o telefone cadastrado. <br>';
			$retorno['ERRO'] = true;
		}

		if(!$rowValidacao->ENDERECO_EMAIL){
			$retorno['ERRO_EMAIL'] = 'O titular não está com o e-mail cadastrado. <br>';
			$retorno['ERRO'] = true;
		}

		if(!$rowValidacao->ENDERECO){
			$retorno['ERRO_ENDERECO'] = 'O titular não está com o endereço cadastrado. <br>';
			$retorno['ERRO'] = true;
		}

		if(!$rowValidacao->NUMERO_CPF){
			$retorno['ERRO_CPF'] = 'O associado ' . $rowValidacao->NOME_ASSOCIADO . ' não está com o CPF cadastrado. <br>';
			$retorno['ERRO'] = true;
		}

		if(SqlToData($rowValidacao->DATA_NASCIMENTO) == '01/01/1990'){
			$retorno['ERRO_DT_NASCIMENTO'] = 'O associado ' . $rowValidacao->NOME_ASSOCIADO . ' está com a data de nascimento incorreta. <br>';
			$retorno['ERRO'] = true;
		}

		if(!$rowValidacao->NOME_MAE){
			$retorno['ERRO_NOME_MAE'] = 'O associado ' . $rowValidacao->NOME_ASSOCIADO . ' não está com o nome da mãe cadastrado. <br>';
			$retorno['ERRO'] = true;
		}
	}

	echo json_encode($retorno);
}


function insereEvento($associado){
	$queryAssoc = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($associado);
	$resAssoc = jn_query($queryAssoc);
	while($rowAssoc = jn_fetch_object($resAssoc)){
		jn_query('UPDATE TMP_BENEF_CAAPSML SET PACOTE_MASTER = "SIM" WHERE CHAVE = ' . aspas($rowAssoc->CODIGO_ANTIGO));
		
		jn_query('DELETE FROM VND1003_ON WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssoc->CODIGO_ASSOCIADO));

		$queryInsert  = ' INSERT INTO VND1003_ON (CODIGO_ASSOCIADO, CODIGO_EVENTO, QUANTIDADE_EVENTOS, TIPO_CALCULO, VALOR_FATOR, FLAG_COBRA_DEPENDENTE, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA) VALUES ';
		$queryInsert .= '( ' . aspas($rowAssoc->CODIGO_ASSOCIADO) . ', "1064",1,"V","9.50","N",current_timestamp, "31.12.2099" )';
		
		jn_query($queryInsert);
	}	
}
?>