<?php

function ignoraSubProcesso($dadosInput,$tabelaFilha){
	$retorno = false;
	
	if($_SESSION['codigoSmart'] == '3423' && $tabelaFilha == 'TMP1001_NET' && ($dadosInput['tab'] == 'TMP1000_NET' or ($dadosInput['dados']['origem'] == 'TMP1000_NET'))){		
		$queryEmp = 'SELECT FLAG_CADASTRA_ENDER_FUNC FROM PS1010 WHERE CODIGO_EMPRESA = ' . aspas($_SESSION['codigoIdentificacao']);		
		$resEmp = jn_query($queryEmp);
		$rowEmp = jn_fetch_object($resEmp);
		if($rowEmp->FLAG_CADASTRA_ENDER_FUNC == '' or ($rowEmp->FLAG_CADASTRA_ENDER_FUNC == 'N')){
			$retorno = true;
		}
	}
	
	if($_SESSION['codigoSmart'] == '3423' && $tabelaFilha == 'TMP1006_NET' &&  ($dadosInput['tab'] == 'TMP1000_NET' or ($dadosInput['dados']['origem'] == 'TMP1000_NET'))){		
		$queryEmp = 'SELECT FLAG_CAD_TELEFONES_FUNC FROM PS1010 WHERE CODIGO_EMPRESA = ' . aspas($_SESSION['codigoIdentificacao']);		
		$resEmp = jn_query($queryEmp);
		$rowEmp = jn_fetch_object($resEmp);
		if($rowEmp->FLAG_CAD_TELEFONES_FUNC == '' or ($rowEmp->FLAG_CAD_TELEFONES_FUNC == 'N')){
			$retorno = true;
		}
	}
	
	return $retorno;
}
?>