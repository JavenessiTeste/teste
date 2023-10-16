<?php
require('../lib/base.php');

$codAssociadoTmp = $dadosInput['codAssociado'];

if($dadosInput['tipo']== 'dados'){
	$queryAssociado  = ' SELECT ';	
	$queryAssociado .= ' 	A.CODIGO_ASSOCIADO, B.ENDERECO, A.CODIGO_PLANO, A.NOME_ASSOCIADO, A.DATA_ADMISSAO, COALESCE(C.NOME_PLANO_FAMILIARES, C.NOME_PLANO_EMPRESAS) AS NOME_PLANO, ';
	$queryAssociado .= ' 	A.NUMERO_CPF, A.DATA_NASCIMENTO, A.CODIGO_CNS, A.NOME_MAE, A.SEXO, B.BAIRRO, B.CIDADE, B.ESTADO, B.CEP, B.ENDERECO, B.ENDERECO_EMAIL, B.NUMERO_TELEFONE_01, ';
	$queryAssociado .= ' 	B.DIA_VENCIMENTO  ';
	$queryAssociado .= ' FROM VND1000_ON A ';		
	$queryAssociado .= ' INNER JOIN VND1001_ON B ON (A.CODIGO_ASSOCIADO = B.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' INNER JOIN PS1030 C ON (A.CODIGO_PLANO = C.CODIGO_PLANO) ';
	$queryAssociado .= ' WHERE A.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssociado = jn_query($queryAssociado);
	$rowAssociado = jn_fetch_object($resAssociado);

	$idade = calcularIdade($rowAssociado->DATA_NASCIMENTO);
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);

	$retorno['ASSOCIADO'] 			= $rowAssociado->CODIGO_ASSOCIADO;	
	$retorno['PLANO_ASSOCIADO'] 	= jn_utf8_encode($rowAssociado->NOME_PLANO);	
	$retorno['NOME_ASSOCIADO'] 		= jn_utf8_encode($rowAssociado->NOME_ASSOCIADO);
	$retorno['DATA_ADMISSAO'] 		= SqlToData($rowAssociado->DATA_ADMISSAO);	
	$retorno['SEXO'] 				= $rowAssociado->SEXO;	
	$retorno['NUMERO_CPF']			= $rowAssociado->NUMERO_CPF;
	$retorno['DATA_NASCIMENTO'] 	= SqlToData($rowAssociado->DATA_NASCIMENTO);	
	$retorno['CODIGO_CNS']			= $rowAssociado->CODIGO_CNS;
	$retorno['NOME_MAE'] 			= jn_utf8_encode($rowAssociado->NOME_MAE);
	$retorno['ENDERECO'] 			= jn_utf8_encode($rowAssociado->ENDERECO);
	$retorno['BAIRRO'] 				= jn_utf8_encode($rowAssociado->BAIRRO);
	$retorno['CIDADE'] 				= jn_utf8_encode($rowAssociado->CIDADE);
	$retorno['ESTADO'] 				= jn_utf8_encode($rowAssociado->ESTADO);
	$retorno['CEP'] 				= jn_utf8_encode($rowAssociado->CEP);
	$retorno['EMAIL'] 				= jn_utf8_encode($rowAssociado->ENDERECO_EMAIL);
	$retorno['CELULAR'] 			= jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01);
	$retorno['DIA_VENCIMENTO'] 		= $rowAssociado->DIA_VENCIMENTO;
	$retorno['VALOR_MENSALIDADE'] 	= toMoeda($rowValores->VALOR_PLANO);


	
	echo json_encode($retorno);
}

function calcularIdade($date){	
	if(!$date){
		return null;
	}
	
	$date = SqlToData($date);
	$date = dataToSql($date);
	$date = str_replace("'",'',$date);

    // separando yyyy, mm, ddd
    list($ano, $dia, $mes) = explode('-', $date);
	$dia = substr($dia, 0, 2);
	
    // data atual
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
	
    // cálculo
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
}
?>