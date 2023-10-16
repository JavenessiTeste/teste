<?php

			
function apresentarMensagemEspecifica($tipo,$tabela){	
	$tabela = strtoupper($tabela);
	$mensagem = '';

	if($tipo=='INC'){
		if($tabela == 'PS6500' and $_SESSION['codigoSmart'] == '4316') {			
			$mensagem = 'Antes de cadastrar a autorização, verifique se o beneficiário possui um código pano de tratamento para esta Consulta/Procedimento!';
		}
	}

	
	return $mensagem;
	
}

?>