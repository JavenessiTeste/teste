<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] =='info'){
	

	
	$queryTabela = 'Select * from  cfgtabelas_sis where nome_tabela ='.aspas($dadosInput['tab']);
		
	$resTabela = jn_query($queryTabela);
		$retorno['CAMPOS'] = array();
	if($rowTabela = jn_fetch_object($resTabela)){
		$retorno['DESCRICAO'] = jn_utf8_encode($rowTabela->DESCRICAO_TABELA);
	}
	
	$queryColunas = 'select * from cfgcampos_sis where nome_campo is not null and nome_tabela = '.aspas($dadosInput['tab']).' order by numero_ordem_criacao';
			
	$resColunas = jn_query($queryColunas);

	while($rowColunas = jn_fetch_object($resColunas)){
		$dadoColuna['CAMPO']         = jn_utf8_encode($rowColunas->NOME_CAMPO);
		$dadoColuna['LABEL']         = jn_utf8_encode($rowColunas->LABEL_CAMPO);
		$dadoColuna['TIPO']          = jn_utf8_encode(strtoupper($rowColunas->TIPO_CAMPO));
		$retorno['CAMPOS'][] = $dadoColuna; 
	}
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] =='dados'){
	$filtro = '';
	foreach ($dadosInput['dados'] as $item){
		if($item['TIPO']== 'CAMPO'){
			$filtro = $filtro . ' '.$item['CAMPO']['CAMPO'] . ' '. $item['TIPOFILTRO']['VALOR'];
			
			if($item['TIPOFILTRO']['VALOR']=='LIKE')
				$item['VALORA'] = '%'.$item['VALORA'].'%';
			
			if($item['CAMPO']['TIPO']=='DATE'){
				if($item['TIPOFILTRO']['TIPO']=='SIMPLES'){
					$filtro = $filtro . ' '. dataToSql($item['VALORA']).' ';
				}else if($item['TIPOFILTRO']['TIPO']=='DUPLO'){
					$filtro = $filtro . ' '. dataToSql($item['VALORA']).' and  '.dataToSql($item['VALORB']).' ';
				}
			}else{
				if($item['TIPOFILTRO']['TIPO']=='SIMPLES'){
					$filtro = $filtro . ' '. aspas($item['VALORA']).' ';
				}else if($item['TIPOFILTRO']['TIPO']=='DUPLO'){
					$filtro = $filtro . ' '. aspas($item['VALORA']).' and  '.aspas($item['VALORB']).' ';
				}
			}
		}else{
			$filtro = $filtro . ' '.$item['VALOR'];
		}
		
	}
	//print($filtro);
	$queryTabela = 'select * from cfgcampos_sis where nome_campo is not null and nome_tabela = '.aspas($dadosInput['tab']).' and FLAG_CHAVEPRIMARIA='.aspas('S');
	$resTabela = jn_query($queryTabela);
	$chave = '';
	if($rowTabela = jn_fetch_object($resTabela)){
		$chave = jn_utf8_encode($rowTabela->NOME_CAMPO);
	}
	
	$retorno['VALORES'] = "";
	$sql = "select ". $chave." from ". $dadosInput['tab']. " where ".$filtro;
	$resTabela = jn_query($sql);
	
	while($rowTabela = jn_fetch_object($resTabela)){
		
		$rowTabela =  (array) $rowTabela;
		
		if($retorno['VALORES']=="")
			$retorno['VALORES'] = "'".$rowTabela[$chave]."'";
		else
			$retorno['VALORES'] .= ",'".$rowTabela[$chave]."'";
	}
	
	echo json_encode($retorno);
	

}





?>