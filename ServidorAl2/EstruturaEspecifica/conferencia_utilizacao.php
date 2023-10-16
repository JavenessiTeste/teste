<?php
require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='carregaDados'){
	$codigoAssociado = $dadosInput['codAssociado'];
	$mesAno = $dadosInput['mesAno'];
	
	$query_proc  = " SELECT * FROM VW_UTILIZACAO_WEB ";	
	$query_proc .= " WHERE 1 = 1 ";
	
	if($codigoAssociado != ''){		
		$query_proc .= " AND CODIGO_ASSOCIADO = " . aspas($codigoAssociado);							
	}else{
		$query_proc .= " AND CODIGO_TITULAR = " . aspas($_SESSION['codigoIdentificacao']);									
	}
	
	if($mesAno != ''){		
		$query_proc .= " AND MES_ANO = " . aspas($mesAno);							
	}
	
	$query_proc .= " ORDER BY DATA_PROCEDIMENTO DESC";							
	$resultQuery    = jn_query($query_proc);		
	
	$dadosGrid = array();	
	
	$item['NOME_CAMPO'] = "NUMERO_GUIA";
	$item['LABEL'] = 'Numero Guia';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	
	$item['NOME_CAMPO'] = "DATA_PROCEDIMENTO";
	$item['LABEL'] = 'Data Procedimento';
	$item['TIPO_CAMPO'] = "DATE";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	
	
	$item['NOME_CAMPO'] = "NOME_ASSOCIADO";
	$item['LABEL'] = 'Nome Contratante';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NOME_PROCEDIMENTO";
	$item['LABEL'] = 'Nome Procedimento';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "VALOR_GERADO";
	$item['LABEL'] = 'Valor Gerado';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "TIPO_GUIA";
	$item['LABEL'] = 'Tipo Guia';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "VALOR_COPARTICIPACAO";
	$item['LABEL'] = 'Valor Coparticipacao';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "CODIGO_ASSOCIADO";
	$item['LABEL'] = 'Codigo Associado';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "CODIGO_TITULAR";
	$item['LABEL'] = 'Codigo Titular';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "CODIGO_AUXILIAR";
	$item['LABEL'] = 'Codigo Auxiliar';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	if(rvc('OCULTAR_ANO_MES_UTILIZACAO') == '' || rvc('OCULTAR_ANO_MES_UTILIZACAO') != 'SIM'){
		$item['NOME_CAMPO'] = "ANO_MES";
		$item['LABEL'] = 'Ano Mes';
		$item['TIPO_CAMPO'] = "";
		$item['GRID'] = 'S';
		$infoGrid[] = $item;
	}
	
	$item['NOME_CAMPO'] = "MES_ANO";
	$item['LABEL'] = 'Mes Ano';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NOME_PRESTADOR";
	$item['LABEL'] = 'Nome Prestador';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NUMERO_PROCESSAMENTO";
	$item['LABEL'] = 'Numero Processamento';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	if(rvc('OCULTAR_LOTE_UTILIZACAO') == '' || rvc('OCULTAR_LOTE_UTILIZACAO') != 'SIM'){
		$item['NOME_CAMPO'] = "NUMERO_LOTE";
		$item['LABEL'] = 'Numero Lote';
		$item['TIPO_CAMPO'] = "";
		$item['GRID'] = 'S';
		$infoGrid[] = $item;
	}
	
	$item['NOME_CAMPO'] = "QUANTIDADE_PROCEDIMENTOS";
	$item['LABEL'] = 'Quantidade Procedimentos';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$valorGerado = '';
	$valorCoparticipacao = '';
	$i = 0;

	while ($rowPrincipal = jn_fetch_object($resultQuery)){

		$valorGerado          +=  $rowPrincipal->VALOR_GERADO;
		$valorCoparticipacao  +=  $rowPrincipal->VALOR_COPARTICIPACAO;

		$dadoLinha = array();
						
		foreach ($rowPrincipal as $key => $value){
			
			if(is_object($value)){
				$dadoLinha[$key] = $value->format('Y-m-d');
			}elseif($key == 'VALOR_GERADO' || $key == 'VALOR_COPARTICIPACAO'){
				$dadoLinha[$key] = toMoeda($value);				
			}else{				
				$dadoLinha[$key] = jn_utf8_encode($value);
			}
		}

		$dadoLinha['ALTERAR'] = '';
		$dadoLinha['EXCLUIR'] = '';
		$dadoLinha['CHECK'] = 'N';
		$dadosGrid[] = $dadoLinha;	

		$i++;
	}
		
		
	$dadosGrid[$i]['TIPO_GUIA'] = '';
	$dadosGrid[$i]['NUMERO_GUIA'] = '';
	$dadosGrid[$i]['CODIGO_EMPRESA'] = '';
	$dadosGrid[$i]['CODIGO_ASSOCIADO'] = '';
	$dadosGrid[$i]['CODIGO_TITULAR'] = '';
	$dadosGrid[$i]['CODIGO_AUXILIAR'] = '';
	$dadosGrid[$i]['ANO_MES'] = '';
	$dadosGrid[$i]['MES_ANO'] = '';
	$dadosGrid[$i]['NUMERO_LOTE'] = '';
	$dadosGrid[$i]['QUANTIDADE_PROCEDIMENTOS'] = '';
	$dadosGrid[$i]['NOME_ASSOCIADO'] = '';
	$dadosGrid[$i]['CODIGO_PRESTADOR'] = '';
	$dadosGrid[$i]['NOME_PRESTADOR'] = '';
	$dadosGrid[$i]['DATA_PROCEDIMENTO'] = '';
	$dadosGrid[$i]['NUMERO_PROCESSAMENTO'] = '';
	$dadosGrid[$i]['VALOR_GERADO'] = '';
	$dadosGrid[$i]['VALOR_COPARTICIPACAO'] = '';
	$dadosGrid[$i]['CODIGO_PROCEDIMENTO'] = '';
	$dadosGrid[$i]['NOME_PROCEDIMENTO'] = '';
	$dadosGrid[$i]['MES_ANO_REFERENCIA'] = '';
	$dadosGrid[$i]['QUANTIDADE'] = '';
	$dadosGrid[$i]['CODIGO_ESPECIALIDADE'] = '';
	$dadosGrid[$i]['NOME_ESPECIALIDADE'] = '';
	$dadosGrid[$i]['ALTERAR'] = '';
	$dadosGrid[$i]['EXCLUIR'] = '';
	$dadosGrid[$i]['CHECK'] = '';
	
	$i++;
	
	$dadosGrid[$i]['TIPO_GUIA'] = 'Total Coparticipacao: ';
	$dadosGrid[$i]['NUMERO_GUIA'] = '';
	$dadosGrid[$i]['CODIGO_EMPRESA'] = '';
	$dadosGrid[$i]['CODIGO_ASSOCIADO'] = '';
	$dadosGrid[$i]['CODIGO_TITULAR'] = '';
	$dadosGrid[$i]['CODIGO_AUXILIAR'] = '';
	$dadosGrid[$i]['ANO_MES'] = '';
	$dadosGrid[$i]['MES_ANO'] = '';
	$dadosGrid[$i]['NUMERO_LOTE'] = '';
	$dadosGrid[$i]['QUANTIDADE_PROCEDIMENTOS'] = '';
	$dadosGrid[$i]['NOME_ASSOCIADO'] = '';
	$dadosGrid[$i]['CODIGO_PRESTADOR'] = '';
	$dadosGrid[$i]['NOME_PRESTADOR'] = '';
	$dadosGrid[$i]['DATA_PROCEDIMENTO'] = '';
	$dadosGrid[$i]['NUMERO_PROCESSAMENTO'] = '';
	$dadosGrid[$i]['VALOR_GERADO'] = toMoeda($valorGerado);
	$dadosGrid[$i]['VALOR_COPARTICIPACAO'] = toMoeda($valorCoparticipacao);
	$dadosGrid[$i]['CODIGO_PROCEDIMENTO'] = '';
	$dadosGrid[$i]['NOME_PROCEDIMENTO'] = 'Total Gerado: ';
	$dadosGrid[$i]['MES_ANO_REFERENCIA'] = '';
	$dadosGrid[$i]['QUANTIDADE'] = '';
	$dadosGrid[$i]['CODIGO_ESPECIALIDADE'] = '';
	$dadosGrid[$i]['NOME_ESPECIALIDADE'] = '';
	$dadosGrid[$i]['ALTERAR'] = '';
	$dadosGrid[$i]['EXCLUIR'] = '';
	$dadosGrid[$i]['CHECK'] = '';		
	
	
	$retorno['DADOS_GRID']  = $dadosGrid;	
	$retorno['INFO_GRID']  = $infoGrid;
	$retorno['MENSAGEM_PAGAMENTO']  = '';
	
	if($_SESSION['codigoSmart'] == '3808'){//Dana
		$retorno['MENSAGEM_PAGAMENTO']  = 'Atenção! Os valores de descontos são liberados a partir do dia 20 de cada mês.';
	}
	
	echo json_encode($retorno);

}



if($dadosInput['tipo'] =='buscaBeneficiarios'){
	$queryColunas = 'select CODIGO_TITULAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	$resColunas   = jn_query($queryColunas);
	$rowColunas   = jn_fetch_object($resColunas);
	
	$queryBenef  = " SELECT DISTINCT CODIGO_ASSOCIADO, NOME_ASSOCIADO FROM PS1000 ";	
	
	if(retornaValorConfiguracao('APRES_TODOS_ASSOC_DEP') == 'SIM'){
		$queryBenef .= ' WHERE CODIGO_TITULAR ='. aspas($rowColunas->CODIGO_TITULAR);
	}else{				
		$queryBenef .= ' WHERE ((CODIGO_TITULAR ='. aspas($_SESSION['codigoIdentificacao']) . ') or (CODIGO_ASSOCIADO = '. aspas($_SESSION['codigoIdentificacao']) . '))';				
	}
	
	$queryBenef .= " ORDER BY NOME_ASSOCIADO ";							
	$resultBenef    = jn_query($queryBenef);
	
	$ArrBenef = Array();	

	$i = 0;
	while($rowBenef    = jn_fetch_object($resultBenef)){
		$ArrBenef[$i]['codigoAssociado'] = $rowBenef->CODIGO_ASSOCIADO;
		$ArrBenef[$i]['nomeAssociado'] = $rowBenef->NOME_ASSOCIADO;
		$i++;
	}
	
	$ArrBenef[$i]['codigoAssociado'] = '';
	$ArrBenef[$i]['nomeAssociado'] = 'TODOS';
	
	$retorno = $ArrBenef;
	
	echo json_encode($retorno);
}

if($dadosInput['tipo'] =='buscaMes'){
	$associado = $dadosInput['codigoAssociado'];
	
	$queryMesAno  = " SELECT DISTINCT MES_ANO, ANO_MES FROM VW_UTILIZACAO_WEB ";	
	$queryMesAno .= " WHERE MES_ANO IS NOT NULL ";
	
	if(($associado == '') or ($associado == 'TODOS')){
		$queryMesAno .= " AND CODIGO_TITULAR = " . aspas($_SESSION['codigoIdentificacao']);	
	}else{
		$queryMesAno .= " AND CODIGO_ASSOCIADO = " . aspas($associado);	
	}
	
	$queryMesAno .= " ORDER BY ANO_MES DESC";							
	$resultMesAno    = jn_query($queryMesAno);	
	
	$ArrMesAno = Array();
	$i = 0;
	while($rowMesAno    = jn_fetch_object($resultMesAno)){
		
		if($_SESSION['codigoSmart'] == '4012'){//RBS - Acrescenta um mês na visualização, para compatibilizar com registros internos
			$mesAlterar = explode("/", $rowMesAno->MES_ANO);			
			if($mesAlterar[0] == '12'){
				$ArrMesAno[$i]['mesAnoApresentar'] = '01/' . ($mesAlterar[1] + 1);
			}else{
				$ArrMesAno[$i]['mesAnoApresentar'] = str_pad(($mesAlterar[0] + 1),2, "0", STR_PAD_LEFT) . '/' . $mesAlterar[1];
			}
		}else{
			$ArrMesAno[$i]['mesAnoApresentar'] = $rowMesAno->MES_ANO;		
		}
		
		$ArrMesAno[$i]['mesAno'] = $rowMesAno->MES_ANO;
		$i++;
	}
	
	$retorno = $ArrMesAno;
	
	echo json_encode($retorno);
}

?>