<?php
 
function antesDeIncluirAlterarExcluir($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,$parametroPrompt=''){
//$tipo = INC ALT EXC 
//campos apenas para INC ALT
//$retorno['STATUS'] = 'OK';
//$retorno['STATUS'] = 'ERRO'; para processo
//$retorno['MSG']    = ''; mensagem que ira aparecer operador quando der erro.
//$retorno['MSG']    = jn_utf8_encode('usar esssa função quando tiver acentuação');
//$campos[$i]['CAMPO']
//$campos[$i]['VALOR']

	$retorno['STATUS'] = 'OK'; 

	/*pr($tipo);
	pr($tabela);
	pr($tabelaOrigem);
	pr($chave);
	pr($nomeChave);*/

	if ($_SESSION['AliancaPx4Net']=='S') // Se for o ERP
	{

		  require_once('../EstruturaPx/antesDeIncluirAlterarExcluir_ERPPx.php');
		  $retorno = antesDeIncluirAlterarExcluir_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,$parametroPrompt);

	} // Fim se for o AliancaPX4Net (ERP)
	else
	{


	}



	/*
	if($tabela=="XXX"){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'TESTE ERRO';
	}
	
	for($i=0;$i<count($campos);$i++){
		if($campos[$i]['CAMPO']=='CODIGO_OPPROV'){
			$campos[$i]['VALOR'] = 1;
		}
	}
	*/

	return $retorno;
	
}

?>
