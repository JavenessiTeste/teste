<?php
require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='filtrar'){
	
	$dadosInput["dataInicial"] = substr($dadosInput["dataInicial"], 0, 10);
	$dadosInput["dataFinal"] = substr($dadosInput["dataFinal"], 0, 10);
	
	$query = 'SELECT * FROM VW_AUTORIZACOES_GUIAS_AL2 WHERE CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']);	
	
	if ($data["numeroAutorizacao"] > 0)
		$query  .= ' And (VW_AUTORIZACOES_GUIAS_AL2.NUMERO_AUTORIZACAO = '. aspas($dadosInput["numeroAutorizacao"] ) . ') ';

	if ($dadosInput["dataInicial"] != "" and $dadosInput["dataInicial"] != " "){			
			$query  .= ' And (VW_AUTORIZACOES_GUIAS_AL2.DATA_SOLICITACAO >= ' . aspas($dadosInput["dataInicial"]) . ') ';
	}
		
	if ($dadosInput["dataFinal"] != "" and $dadosInput["dataFinal"] != " ")
		$query  .= ' And (VW_AUTORIZACOES_GUIAS_AL2.DATA_SOLICITACAO <= ' . aspas($dadosInput["dataFinal"]) . ') ';
	
	

	if ($dadosInput['tipoGuia'] != ''){
		$query  .= " And (VW_AUTORIZACOES_GUIAS_AL2.TIPO_GUIA =". aspas($dadosInput['tipoGuia']) . ") ";
	}
	
	if ($dadosInput['statusProcedimento'] != ''){
		$query  .= " And (VW_AUTORIZACOES_GUIAS_AL2.STATUS_PROCEDIMENTO =". aspas($dadosInput['statusProcedimento']) . ") ";
	}
	
	$query  .= " ORDER BY VW_AUTORIZACOES_GUIAS_AL2.DATA_SOLICITACAO ";
		
	
	$res = jn_query($query);
	
	while($row = jn_fetch_object($res)) {
		$linha = null;
		
		if (trim($row->STATUS_PROCEDIMENTO) == 'REALIZADO'){
	   	   $cor  = '';
		   $corf = ''; 
		}elseif (trim($row->STATUS_PROCEDIMENTO) =="AUTORIZADO"){
	   	   $cor  = '<font color="green">';
		   $corf = '</font>'; 
		}elseif(trim($row->STATUS_PROCEDIMENTO) =="EM AUDITORIA MÉDICA"){
			$cor  = '<font color="blue">';
		   $corf = '</font>'; 
		}elseif(trim($row->STATUS_PROCEDIMENTO) =="NEGADO"){
			$cor  = '<font color="red">';
		   $corf = '</font>'; 
		}elseif(trim($row->STATUS_PROCEDIMENTO) =="EM AUDITORIA"){
			$cor  = '<font color="CornflowerBlue">';
		   $corf = '</font>'; 
		}else{
			$cor  = '';
			$corf = ''; 	   
		}
	
		$linha['NUMERO_AUTORIZACAO'] = $cor . $row->NUMERO_AUTORIZACAO . $corf;
		$linha['DATA_SOLICITACAO'] = $cor . sqlToData($row->DATA_SOLICITACAO) . $corf;
		$linha['NOME_ASSOCIADO'] = $cor . $row->NOME_ASSOCIADO . $corf;
		$linha['PROCEDIMENTO'] = $cor . $row->CODIGO_PROCEDIMENTO . $corf;
		$linha['NOME_PROCEDIMENTO'] = $cor . $row->NOME_PROCEDIMENTO . $corf;
		$linha['NOME_PRESTADOR'] = $cor . $row->NOME_PRESTADOR . $corf;
		$linha['NOME_PRESTADOR_EXECUTANTE'] = $cor . $row->NOME_PRESTADOR_EXECUTANTE . $corf;
		$linha['TIPO_GUIA'] = $cor . $row->TIPO_GUIA . $corf;
		$linha['STATUS_PROCEDIMENTO'] = $cor . $row->STATUS_PROCEDIMENTO . $corf;
		$linha['IMPRIMIR_GUIA'] = $row->LINK_IMPRIMIR_GUIA ;
		$linha['VALOR_PREVISAO'] = $cor . toMoeda($row->VALOR_GERADO) . $corf;
		$linha['DATA_CONCLUSAO'] = $cor . sqlToData($row->DATA_CONCLUSAO) . $corf;
		$linha['EXCLUIR'] = '';
		
		$retorno['GRID'][] = $linha;
		
	}
	
	echo json_encode($retorno);

}

?>