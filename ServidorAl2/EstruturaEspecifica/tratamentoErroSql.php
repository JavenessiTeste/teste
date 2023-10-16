<?php


function erroSql($erro){
	
	$pos = strpos($erro, 'delete or update a parent row');

	if ($pos) {
		$erro = 'Esse registro tem dependências e não pode ser excluído.';
	}
	
	return $erro;
}


?>