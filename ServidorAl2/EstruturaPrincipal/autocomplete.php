<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../EstruturaEspecifica/complementoSql.php');


if($dadosInput['tipo'] =='dados')
{

	if (strtoupper($dadosInput['tab'])=='CFGCAMPOS_SIS')
	{
		$distinct = ' distinct ';
	}

	$retorno = array();
	
	$dadosInput['valor'] = troca_caracteres($dadosInput['valor'],'_');
	$dadosInput['valor'] = strtoupper(utf8_decode($dadosInput['valor']));
	
	$filtros = '';
	if(count($dadosInput['auxPesquisa'])>0){
		for($i=0;$i<count($dadosInput['auxPesquisa']);$i++){
			$filtros .= ' and UPPER(' . $dadosInput['auxPesquisa'][$i]['CAMPO'].')='.aspas(strtoupper($dadosInput['auxPesquisa'][$i]['VALOR']));	
		}
	}

	$validaPS1000 = '';
	if (($dadosInput['tab'] == 'PS1000') and (retornaValorConfiguracao('ASSOC_EXC_PESQ_CODIGO') == 'SIM') and ($_SESSION['perfilOperador']=='PRESTADOR')){		
		if(strlen($dadosInput['valor']) == 15){
			$validaPS1000  = ' and (CODIGO_ASSOCIADO = ' . aspas($dadosInput['valor']) . ') ';			
		}else{
			$validaPS1000  = ' and (DATA_EXCLUSAO IS NULL or (DATA_EXCLUSAO >= current_timestamp)) ';			
		}
	}	

	$queryColunas = 'select ' . $distinct . ' first 10 '.strtolower($dadosInput['codigo']).' AS VALOR, '.strtolower($dadosInput['desc']).' AS DESCRICAO from '.strtolower($dadosInput['tab']).
					' where ( (UPPER('.strtolower($dadosInput['desc']).') LIKE '.aspas('%'.strtoupper($dadosInput['valor']).'%'). 
					' )or (UPPER('.strtolower($dadosInput['codigo']).') LIKE '.aspas('%'.strtoupper($dadosInput['valor']).'%').')) ' . $validaPS1000 . CompSql($dadosInput['tab'],'AUTO',$dadosInput['complemento']).' '.$filtros;
	
	$resColunas = jn_query($queryColunas);

	while($rowColunas = jn_fetch_object($resColunas)){
        
		$linha['VALOR'] = jn_utf8_encode((string)$rowColunas->VALOR);
		$linha['DESC'] = jn_utf8_encode($rowColunas->DESCRICAO);
		$retorno[] = $linha;
	}	
	
	echo json_encode($retorno);

}	
if($dadosInput['tipo'] =='aux'){
	$retorno = array();
	$retorno['DADOS'] = '';
	
	if($dadosInput['tabela']=='PS1000'){
		$query = "select caminho_arquivo||caminho_arquivo_armazenado||nome_arquivo_armazenado IMAGEM FROM controle_arquivos
									INNER JOIN configuracoes_arq ON 1=1
									where NOME_TABELA = 'FOTO' and DATA_EXCLUSAO is null and CHAVE_REGISTRO =".aspas($dadosInput['codigo']);
		
		$res = jn_query($query);

		if($row = jn_fetch_object($res)){
			$retorno['DADOS'] = '<img src="'.$row->IMAGEM.'" >';
		}
	}
	
	echo json_encode($retorno);

}

if($dadosInput['tipo'] =='descricao'){
	$retorno = array();
	
	$queryColunas = 'select  '.strtolower($dadosInput['codigo']).' AS VALOR, '.strtolower($dadosInput['desc']).' AS DESCRICAO from '.strtolower($dadosInput['tab']).
					' where '.strtolower($dadosInput['codigo']).'  = '.aspas($dadosInput['valor']);
	$resColunas = jn_query($queryColunas);

	$retorno['VALOR'] = jn_utf8_encode($dadosInput['valor']);
	$retorno['DESC'] = "";


	if($rowColunas = jn_fetch_object($resColunas)){
        
		$retorno['VALOR'] = jn_utf8_encode((string)$rowColunas->VALOR);
		$retorno['DESC'] = jn_utf8_encode($rowColunas->DESCRICAO);

	}	
	
	echo json_encode($retorno);

}	


?>