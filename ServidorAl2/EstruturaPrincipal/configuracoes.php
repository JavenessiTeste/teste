<?php

require('../lib/base.php');

if($dadosInput['tipo']== 'configuracoes'){
	$retorno = Array();	
	
	$configuracaoRodape = retornaValorConfiguracao('APRESENTAR_LOGO_RODAPE');
	if($configuracaoRodape){
		$retorno['APRESENTAR_LOGO_RODAPE'] = $configuracaoRodape;
	}
	
	$configuracaoInicial = retornaValorConfiguracao('APRESENTAR_LOGO_PAG_INICIAL');
	if($configuracaoInicial){
		$retorno['APRESENTAR_LOGO_PAG_INICIAL'] = $configuracaoInicial;
	}
	
	$configuracaoLGPD = retornaValorConfiguracao('MENSAGEM_LGPD');
	if($configuracaoLGPD){
		$retorno['MENSAGEM_LGPD'] = jn_utf8_encode($configuracaoLGPD);
	}

	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'configuracoesEspecificas')
{
	if($dadosInput['config']== 'caminhoProcessoDinamico'){
		$configProcessoDinamico = retornaValorConfiguracao('CAMINHO_PROCESSO_DINAMICO');
		if($configProcessoDinamico){
			$retorno = jn_utf8_encode($configProcessoDinamico);
		}
	}
	
	echo json_encode($retorno);
}


if ($dadosInput['tipo']== 'retornaValorConfiguracao')
{

	$configProcessoDinamico = retornaValorConfiguracao($dadosInput['identificacaoConfiguracao']);
	
	if($configProcessoDinamico)
	{
		$retorno = jn_utf8_encode($configProcessoDinamico);
	}
	
	echo json_encode($retorno);
}


?>