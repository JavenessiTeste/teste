<?php
require_once('../lib/base.php');
$_SESSION['codigoSmart'] = 3389; //Foi passado o smart fixo, porque será usado no gerencia contrato.

global $associado;
$associado = $_GET['paramBenef'];

if($associado){
	$queryAssoc = 'SELECT VND1000_ON.ULTIMO_STATUS, VND1000_ON.* FROM VND1000_ON WHERE CODIGO_ANTIGO = ' . aspas($associado);
	$resAssoc = jn_query($queryAssoc);
	if($rowAssoc = jn_fetch_object($resAssoc)){
		if($rowAssoc->ULTIMO_STATUS == 'CONCLUIDO'){		
			return false;
		}else{
			geraContrato($rowAssoc->CODIGO_TITULAR);
			return true;		
		}
	}else{
		cadastraAssocVnd($associado);		
		atualizaFormulario();
	}
}else{
	header('Content-Type: text/html; charset=UTF-8');
	echo 'Parâmetro não encontrado.';
}


function cadastraAssocVnd($associado){
	$query = ' SELECT * FROM TMP_BENEF_APUEL WHERE CHAVE_UNIMED = ' . aspas($associado);
	$query .= ' ORDER BY TIPO_ASSOCIADO DESC ';
	$res = jn_query($query);

	$codigoTitular = 0;
	$i = 0;
	while($row = jn_fetch_object($res)){
		if($codigoTitular == 0){
			$codigoTitular = '0012' . substr($row->CHAVE_UNIMED, 0, 7) . '.0';		
		}
		$codigoAssociado = '0012' . substr($row->CHAVE_UNIMED, 0, 7) . '.' .$i ;
		
		$insertVnd1000  = ' INSERT INTO VND1000_ON (CODIGO_ASSOCIADO, CODIGO_TITULAR, CODIGO_EMPRESA, NOME_ASSOCIADO, CODIGO_PLANO, CODIGO_ANTIGO, ';
		$insertVnd1000 .= ' 	DATA_NASCIMENTO, DATA_ADMISSAO, DATA_DIGITACAO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, TIPO_ASSOCIADO, NUMERO_CPF, ';
		$insertVnd1000 .= ' 	NUMERO_RG, ORGAO_EMISSOR_RG, NATUREZA_RG, DATA_EMISSAO_RG, CODIGO_CNS, NUMERO_DECLARACAO_NASC_VIVO, DESCRICAO_OBSERVACAO ';
		$insertVnd1000 .= ' ) VALUES ( ';
		$insertVnd1000 .= aspas($codigoAssociado) . ',' . aspas($codigoTitular) . ', 400, ' . aspas($row->NOME_ASSOCIADO) . ',' . aspas($row->CODIGO_PLANO) . ',' . aspas($row->CHAVE_UNIMED) . ',';
		$insertVnd1000 .= aspas($row->DATA_NASCIMENTO) . ',' . DataToSql('01/10/2021') . ',' . DataToSql('01/10/2021') . ',' . aspas($row->SEXO) . ',' . aspas(substr($row->NOME_MAE, 0, 39)) . ',' . aspas($row->CODIGO_PARENTESCO) . ',' . aspas($row->CODIGO_ESTADO_CIVIL) . ',' . aspas($row->TIPO_ASSOCIADO) . ',';
		$insertVnd1000 .= aspas($row->NUMERO_CPF) . ',' . aspas($row->NUMERO_RG) . ',' . aspasNull($row->ORGAO_EMISSOR_RG) . ',' . aspasNull($row->NATUREZA_RG) . ',' . aspasNull($row->DATA_EMISSAO_RG) . ',';
		$insertVnd1000 .= aspasNull($row->CODIGO_CNS) . ',' . aspasNull($row->NUMERO_DECLARACAO_NASC_VIVO) . ',' . aspas('ASSOCIADO APUEL - IMPORTAÇÃO VIA REQUEST LOGIN') . ') ';			
		if(jn_query($insertVnd1000)){
			$insertVnd1001  = ' INSERT INTO VND1001_ON (CODIGO_ASSOCIADO, ENDERECO, BAIRRO, CIDADE, CEP, ESTADO, ENDERECO_EMAIL, NUMERO_TELEFONE_01) VALUES ';
			$insertVnd1001 .= ' ( ' . aspas($codigoAssociado) . ',' . aspas(substr($row->ENDERECO, 0, 44)) . ',' . aspas(substr($row->BAIRRO, 0, 24)) . ',' . aspas($row->CIDADE) . ',';
			$insertVnd1001 .= aspas($row->CEP) . ',' . aspas($row->ESTADO) . ',' . aspasNull($row->ENDERECO_EMAIL) . ',' . aspasNull($row->NUMERO_TELEFONE_01) . ') ';		
			jn_query($insertVnd1001);
		}
		
		if($row->PACOTE_MASTER == 'SIM'){			
			insereEvento($codigoAssociado);
		}
		
		$i++;
	}
	
	return $codigoTitular;
}

function direcionaLoginVnd($codigoTitular){
	header("Location:" . "https://vidamax.com.br/AliancaAppNet2/Site/autenticacao/login?t=vo&id=" . $codigoTitular . "&d=site/contrato");
}

function geraContrato($codigoTitular){
	$_GET['codAssociado'] = $codigoTitular;	
	require('gerencia_contratos.php');	
}

function atualizaFormulario(){
	global $associado;
	header("Location:" . "https://vidamax.com.br/AliancaAppNet2/ServidorAl2/EstruturaEspecifica/requestLoginAPUEL.php?paramBenef=" . $associado);
}

function insereEvento($associado){
	$queryInsert  = ' INSERT INTO VND1003_ON (CODIGO_ASSOCIADO, CODIGO_EVENTO, QUANTIDADE_EVENTOS, TIPO_CALCULO, VALOR_FATOR, FLAG_COBRA_DEPENDENTE, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA) VALUES ';
	$queryInsert .= '( ' . aspas($associado) . ', "1064",1,"V","11.15","N",current_timestamp, "31.12.2099" )';
	
	if(jn_query($queryInsert)){		
		return true;
	}
	
}
?>
