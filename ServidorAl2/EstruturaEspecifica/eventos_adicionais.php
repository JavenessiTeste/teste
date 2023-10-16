<?php
require('../lib/base.php');

$associado = $dadosInput['codAssociado'];

if($dadosInput['tipo']== 'dados'){
	
	$queryPrincipal  = " select VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.NOME_ASSOCIADO, VND1000_ON.CODIGO_PLANO, VND1000_ON.TIPO_ASSOCIADO, VND1030CONFIG_ON.CODIGOS_EVENTO_OFERECER_TIT, VND1030CONFIG_ON.CODIGOS_EVENTO_OFERECER_DEP
						 from VND1000_ON
						 inner join VND1030CONFIG_ON on (VND1000_ON.codigo_plano = VND1030CONFIG_ON.codigo_plano) 
						 where COALESCE(VND1000_ON.CODIGO_TITULAR, VND1000_ON.CODIGO_ASSOCIADO) = " . aspas($associado);									

	$resultQuery    = jn_query($queryPrincipal);		
	$retornoJson    = '[';
	
	while ($rowPrincipal = jn_fetch_object($resultQuery)) 
	{
		if (($rowPrincipal->TIPO_ASSOCIADO == 'T') and ($rowPrincipal->CODIGOS_EVENTO_OFERECER_TIT != ''))
		{
			if ($retornoJson != '[')
				$retornoJson .= ',';
					
			$retornoJson .= '{"CODIGO_ASSOCIADO":"' . $rowPrincipal->CODIGO_ASSOCIADO . '","NOME_ASSOCIADO":"' . jn_utf8_encode($rowPrincipal->NOME_ASSOCIADO) . '", "EVENTOS":[';
				
			$queryAux  = " select * FROM PS1024 where CODIGO_EVENTO IN(" . $rowPrincipal->CODIGOS_EVENTO_OFERECER_TIT . ")";									
			$resultAux = jn_query($queryAux);		
			
			$i = 0;
			while ($rowAux = jn_fetch_object($resultAux)) 
			{
				if($i > 0)
					$retornoJson .= ',';
				
				$queryAux2     = " select * FROM VND1003_ON  where CODIGO_ASSOCIADO = " . aspas($rowPrincipal->CODIGO_ASSOCIADO) . " and CODIGO_EVENTO = " . $rowAux->CODIGO_EVENTO;									
				$resultAux2    = jn_query($queryAux2);		
				$valorCheckBox = "N";
				
				if ($rowAux2 = jn_fetch_object($resultAux2)) 
					$valorCheckBox = "S";
				
				$retornoJson .= '{"CODIGO_EVENTO":"' . $rowAux->CODIGO_EVENTO . '","NOME_EVENTO":"' . strtoupper(jn_utf8_encode($rowAux->NOME_EVENTO)) . ' - ' . toMoeda($rowAux->VALOR_SUGERIDO) . '","VALOR_EVENTO":"' . $rowAux->VALOR_SUGERIDO . '","FLAG_MARCADO":"' . $valorCheckBox . '"} ';
				
				$i++;
			}
			
			$retornoJson .= ' 	] ';
			$retornoJson .= ' } ';

		}
		else if (($rowPrincipal->TIPO_ASSOCIADO == 'D') and ($rowPrincipal->CODIGOS_EVENTO_OFERECER_DEP != ''))
		{
		
			
			$retornoJson .= ', {"CODIGO_ASSOCIADO":"' . $rowPrincipal->CODIGO_ASSOCIADO . '","NOME_ASSOCIADO":"' . jn_utf8_encode($rowPrincipal->NOME_ASSOCIADO) . '", "EVENTOS":[';	
			
			$queryAux  = " select * FROM PS1024 where CODIGO_EVENTO IN(" . $rowPrincipal->CODIGOS_EVENTO_OFERECER_DEP . ")";									
			$resultAux = jn_query($queryAux);		
			
			$i = 0;
			while ($rowAux = jn_fetch_object($resultAux)) 
			{
				if($i > 0)
					$retornoJson .= ',';
				
				$queryAux2     = " select * FROM VND1003_ON  where CODIGO_ASSOCIADO = " . aspas($rowPrincipal->CODIGO_ASSOCIADO) . " and CODIGO_EVENTO = " . $rowAux->CODIGO_EVENTO;									
				$resultAux2    = jn_query($queryAux2);		
				$valorCheckBox = "N";
				
				if ($rowAux2 = jn_fetch_object($resultAux2)) 
					$valorCheckBox = "S";
				
				$retornoJson .= '{"CODIGO_EVENTO":"' . $rowAux->CODIGO_EVENTO . '","NOME_EVENTO":"' . strtoupper(jn_utf8_encode($rowAux->NOME_EVENTO)) . ' - ' . toMoeda($rowAux->VALOR_SUGERIDO) . '","VALOR_EVENTO":"' . $rowAux->VALOR_SUGERIDO . '","FLAG_MARCADO":"' . $valorCheckBox . '"} ';
				$i++;
			}
			
			$retornoJson .= ' 	] ';
			$retornoJson .= ' } ';
		}
	}
	
	$retornoJson .=  ']';
	
	echo $retornoJson;
}

if($dadosInput['tipo']== 'salvar'){
	
	
	$resTit = jn_query('SELECT CODIGO_TITULAR FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ' . aspas($dadosInput['dadosSalvar'][0]['CODIGO_ASSOCIADO']));
	$rowTit = jn_fetch_object($resTit);
	
	jn_query('DELETE FROM VND1003_ON WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($rowTit->CODIGO_TITULAR) . ')');
	
	$retorno = Array();

	foreach($dadosInput['dadosSalvar'] as $dadosEvento){
		
		$query 	= 	'Insert Into VND1003_ON(CODIGO_EVENTO, CODIGO_EMPRESA, CODIGO_ASSOCIADO, QUANTIDADE_EVENTOS, TIPO_CALCULO, VALOR_FATOR, FLAG_COBRA_DEPENDENTE, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA) ' .
					'Select ' . $dadosEvento['CODIGO_EVENTO'] . ', 400, "' . $dadosEvento['CODIGO_ASSOCIADO'] . '", 1, coalesce(TIPO_CALCULO,"V"), VALOR_SUGERIDO, "N", ' .  aspas(date("d.m.Y")) . ', "31.12.2099" FROM PS1024 WHERE CODIGO_EVENTO = ' . $dadosEvento['CODIGO_EVENTO'];
			
		
		if (!jn_query($query)) {
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'Nao foi possivel gravar a pergunta -' . $dadosPergunta['NUMERO_PERGUNTA'];				
			return false; // saio retornando false
		}
	}
}

?>