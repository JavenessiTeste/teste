<?php


function CompSqlPD($tabela,$idProcesso,$camposComplementares=array()){

	
	$retorno = '';
	
	//pr('$tabela:' . $tabela);
	//pr('$idProcesso:' . $idProcesso);
	//pr($camposComplementares);

	/* Removi daqui por enquanto, pq coloquei uma opção no formulário
	   Mas deixei como exemplo para saber como usar

	if($idProcesso=='7003'){
			if($tabela == 'PS1010'){
				if($camposComplementares['FLAG_PERMITIR_EXCLUIDOS'] != 'S'){
					$retorno .= ' and PS1010.DATA_EXCLUSAO IS NULL ';
 				}
			}
	}
	
	*/



	if ($idProcesso=='7003') // Calculo de faturamento
	{
		if ($tabela == 'PS1000')
		{
			$retorno .= ' and PS1000.TIPO_ASSOCIADO = ' . aspas('T');

			if($camposComplementares['TIPO_CALCULO_FATURAMENTO'] != '4') // 4-Faturamento individualizado de valores adicionais para beneficiários PJ
			{
				$retorno .= ' and PS1000.FLAG_PLANOFAMILIAR = ' . aspas('S');
 			}
		}
	}

	return $retorno;

}

?>