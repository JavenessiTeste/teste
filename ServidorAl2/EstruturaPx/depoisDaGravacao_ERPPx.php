<?php


function depoisGravacao_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,&$campos,&$retorno)
{
	//$tipo = INC ALT EXC 
	//campos apenas para INC ALT
	//$retorno['STATUS'] = 'OK';
	//$retorno['STATUS'] = 'ERRO'; para processo
	//$retorno['MSG']    = ''; mensagem que ira aparecer operador quando der erro.
	//$retorno['MSG']    = jn_utf8_encode('usar esssa função quando tiver acentuação');

	//pr($chave);
	//pr($nomeChave);
	
	$retorno['MSG'] = 'Ok, registro gravado com sucesso!!!';

    return $retorno;

}



?>