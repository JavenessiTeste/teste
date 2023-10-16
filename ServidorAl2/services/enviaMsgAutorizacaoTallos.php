<?php
require_once('../lib/base.php');
require_once('../lib/tallos.php');


//print_r(enviaMsgAutorizacaoTallos('014000218758000','111','N'));

function enviaMsgAutorizacaoTallos($codigoAssociado,$numeroAutorizacao,$status){
	$sql = 'select  coalesce(CELULAR_CONFIRMADO,cast(PS1006.CODIGO_AREA as varchar(2))+Ps1006.NUMERO_TELEFONE) CELULAR_CONFIRMADO, DATA_CONFIRMACAO_CELULAR, NOME_ASSOCIADO from PS1000
	left join PS1006 on PS1006.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
	WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($codigoAssociado);

	$res  = jn_query($sql);

	$celular = '';
	$nome = '';
	if($row = jn_fetch_object($res)){	
		$celular =$row->CELULAR_CONFIRMADO;
		$nome =$row->NOME_ASSOCIADO;
	}
	
	$link = retornaValorConfiguracao('LINK_PERSISTENCIA');

	if($celular== ''){
		$retorno = array();
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'Sem celular';
		return $retorno;
	}else{
		$variaveis = array();
		$variaveis[] = saudacao();
  	    $variaveis[] = $nome;
		
		$sql = "select 

			CASE 
				WHEN PS6500.TIPO_GUIA = 'S' THEN CONCAT('".$link."ProcessoDinamico/guiaSpsadtPng.php?numero=',ps6500.NUMERO_AUTORIZACAO)
				WHEN PS6500.TIPO_GUIA = 'I' THEN CONCAT('".$link."ProcessoDinamico/guiaSolicitacaoInternacaoPng.php?numero=',ps6500.NUMERO_AUTORIZACAO)
				WHEN PS6500.TIPO_GUIA = 'A' THEN CONCAT('".$link."ProcessoDinamico/guiaSolicitacaoInternacaoPng.php?numero=',ps6500.NUMERO_AUTORIZACAO)
				ELSE ''
			END AS LINK,
			PS5210.NOME_PROCEDIMENTO

		 
		 from PS6500
		 inner join PS6510 on ps6500.NUMERO_AUTORIZACAO = PS6510.NUMERO_AUTORIZACAO
		 inner join PS5210 on PS6510.CODIGO_PROCEDIMENTO = PS5210.CODIGO_PROCEDIMENTO  WHERE PS6500.NUMERO_AUTORIZACAO = ".aspas($numeroAutorizacao);

		$res  = jn_query($sql);	
		$row = jn_fetch_object($res);		
		
		if($status=='A'){
			$variaveis[] = $numeroAutorizacao.' - '.$row->LINK;
			$idTemplate = 	retornaValorConfiguracao('ID_TEMPLATE_APROVADA_TALLOS');
		}else if($status=='N'){
			$variaveis[] = $numeroAutorizacao;
			$idTemplate = 	retornaValorConfiguracao('ID_TEMPLATE_NEGADA_TALLOS');
		}
		$variaveis[] = $row->NOME_PROCEDIMENTO;
		$retorno = EnviaMsgWhatsAppTemplateTallos($idTemplate,$celular, $variaveis);
		return $retorno;
	}


}



function saudacao() {
	$horaAtual = date("H");

	if ($horaAtual >= 5 && $horaAtual < 12) {
			return "Bom Dia";
	} elseif ($horaAtual >= 12 && $horaAtual < 18) {
			return "Boa Tarde";
	} else {
			return "Boa Noite";
	}
}

?>