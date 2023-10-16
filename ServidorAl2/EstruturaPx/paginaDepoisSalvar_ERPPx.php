<?php

function paginaDepoisSalvar_ERPPx($chave,$nomeChave, $tabela, $tabelaOriginal,$tipo)
{
	
	$retorno['DESTINO'] = '';
	$retorno['MENSAGEM_CONFIRMACAO'] = '';
	$retorno['NOME'] = '';
	

	if ($tabela=='PS1000')
	{
		$retorno['MENSAGEM'] = ' Finalizado o cadastro.<br>Código do titular gerado: ' . $chave;
	}
	else if ($tabela=='PS1010')
	{
		$retorno['MENSAGEM'] = ' Finalizado o cadastro.<br>Código da empresa gerada: ' . $chave;
	}
	
	return $retorno;
	
	
}	



?>