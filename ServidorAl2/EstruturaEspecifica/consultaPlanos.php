<?php

require('../lib/base.php');

require('../private/autentica.php');


if($dadosInput['tipo'] =='dados'){
	
	$plano = '';
	if($_SESSION['perfilOperador']==='BENEFICIARIO'){
		$queryPlano = 'SELECT CODIGO_PLANO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$resPlano  = jn_query($queryPlano);
		$rowPlano = jn_fetch_object($resPlano);

		$plano = $rowPlano->CODIGO_PLANO;
	}
	
	$queryPrincipal =	"select CODIGO_PLANO,NOME_PLANO_FAMILIARES, CODIGO_CADASTRO_ANS ,NOME_TIPO_ABRANGENCIA from VW_PLANOS_NET 
						 where  (FLAG_OMITE_PROCESSOS_ANS IS NULL or FLAG_OMITE_PROCESSOS_ANS =".aspas('N').") and STATUS_COMERCIALIZACAO_PLANO = ".aspas('ATIVO');	
	
	$indisponiveis = rvc('LISTA_PLANOS_INDISPONIVEISWEB');
	
	if($indisponiveis!==''){
		$queryPrincipal .= " and CODIGO_PLANO NOT IN (" . $indisponiveis . ") ";	
	}
	
	if(retornaValorConfiguracao('APENAS_PLANO_BENEF') == 'SIM' && $_SESSION['perfilOperador'] == 'BENEFICIARIO'){
		$queryPrincipal .= " and CODIGO_PLANO = " . aspas($plano);
	}
	
	if($_SESSION['perfilOperador'] == 'EMPRESA' && retornaValorConfiguracao('VALIDAR_PLANOS_ESP_EMPRESA') == 'SIM'){
		$queryPrincipal .= " AND VW_PLANOS_NET.CODIGO_PLANO IN (SELECT PS1059.CODIGO_PLANO FROM PS1059 WHERE CODIGO_EMPRESA = " . aspas($_SESSION['codigoIdentificacao']) . ") ";			
	}
	
	$queryPrincipal .= " order by CODIGO_PLANO";	
	$resultQuery = jn_query($queryPrincipal); 
	
	while($rowPrincipal    = jn_fetch_object($resultQuery)){
		$item['CODIGO_PLANO']  = $rowPrincipal->CODIGO_PLANO;
		$item['NOME_PLANO'] = jn_utf8_encode($rowPrincipal->NOME_PLANO_FAMILIARES);
		$item['NUMERO_REGISTRO'] = $rowPrincipal->CODIGO_CADASTRO_ANS;
		$item['ABRANGENCIA_GEOGRAFICA'] = jn_utf8_encode(trim($rowPrincipal->NOME_TIPO_ABRANGENCIA));
		$item['SELECIONADO'] = $rowPrincipal->CODIGO_PLANO == $plano;
		

		$retorno['PLANOS'][] = $item;
	}
	
	$ocultarDadosOperadora = retornaValorConfiguracao('OCULTAR_INFO_DADOS_PLANO');
	
	if(isset($ocultarDadosOperadora)){
		$retorno['PLANOS'][0]['OCULTAR_INFO_OPERADORA'] = $ocultarDadosOperadora;
	}
	
	echo json_encode($retorno);
}



?>