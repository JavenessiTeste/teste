<?php
require('../lib/base.php');

require('../private/autentica.php');


if($dadosInput['tipo'] =='dados'){

	$carencias = getCarencias($dadosInput['associado']);
	
	if($carencias != ''){
		foreach((array) $carencias as $carencia) {
			$linha = Array();
			$tipoImagem = (compareData(SqlToData($carencia->RESULTADO_DATA_CARENCIA)) >= 0) ? 1 : 0;
			
			$linha['NUMERO_GRUPO'] 		= $carencia->RESULTADO_NUMERO_GRUPO;
			$linha['DESCRICAO'] 		= jn_utf8_encode($carencia->RESULTADO_DESCRICAO_GRUPO);
			$linha['DATA'] 			    = SqlToData($carencia->RESULTADO_DATA_CARENCIA);
			$linha['SITUACAO'] 			= ($tipoImagem == 0) ? 'LIBERADO' : 'EM CARÊNCIA';
			
			if($tipoImagem == 0){
				$linha['ICONE'] = '<img src="assets/img/liberado.png" width="16" height="16" />'; 
			}else{
				$linha['ICONE'] = '<img src="assets/img/carencia.png" width="16" height="16" />'; 
			}
			
			$retorno['GRID'][] = $linha;
		}
	}
	echo json_encode($retorno);

}







?>