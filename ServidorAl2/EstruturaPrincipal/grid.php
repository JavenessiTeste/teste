<?php
require('../lib/base.php');

require('../private/autentica.php');

require('../EstruturaEspecifica/complementoSql.php');
require('../EstruturaEspecifica/ordemGrid.php');
require('../EstruturaEspecifica/configuracoesGrid.php');


if($dadosInput['tipo'] =='info'){
	$retorno;
	$chave = '';
	
	$permissoes = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$_SESSION['perfilOperador'],$dadosInput['tab']);
	
	if((!$permissoes['VIS'])and(!$permissoes['EXC'])and(!$permissoes['ALT'])and(!$permissoes['INC']) ){
		
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Você não tem permissão a tabela :'.$dadosInput['tab'];
	
	}else{
		
		
	
		$queryTabela = 'Select * from  cfgtabelas_sis where nome_tabela ='.aspas($dadosInput['tab']);
		
		$resTabela = jn_query($queryTabela);
		
		if($rowTabela = jn_fetch_object($resTabela)){
			if($rowTabela->TABELA_ORIGINAL == '')
				$retorno['TABELA_ORIGINAL'] = $rowTabela->NOME_TABELA;
			else
				$retorno['TABELA_ORIGINAL'] = $rowTabela->TABELA_ORIGINAL;
			
			$retorno['TABELA']          = $rowTabela->NOME_TABELA;
			$retorno['DESCRICAO']       = jn_utf8_encode($rowTabela->DESCRICAO_TABELA);
			$retorno['CONFIGURACOES']   = configuracoesGrid($rowTabela->NOME_TABELA);
			
			$dadosInput['tab'] = tabelaGrid($dadosInput['tab']);
			
			$queryColunas = 'select * from cfgcampos_sis_cd where nome_campo is not null and nome_tabela = '.aspas($dadosInput['tab']).' order by numero_ordem_criacao';
			
			$resColunas = jn_query($queryColunas);
			
			$dadoColuna['NOME_CAMPO'] = 'ACOES';
			$dadoColuna['NOME_CHAVE'] = $chave;
			$dadoColuna['GRID'] = 'S';
			$retorno['COLUNAS'][] = $dadoColuna;
			//$colocarOrdenacao = true;
			while($rowColunas = jn_fetch_object($resColunas)){
				if(trim($rowColunas->NOME_CAMPO)<>''){
					$dadoColuna['NOME_CAMPO']          = jn_utf8_encode($rowColunas->NOME_CAMPO);
					$dadoColuna['NOME_CAMPO_MASCARADO']= mascaraNomeCampo($rowColunas->NOME_CAMPO);
					$dadoColuna['DESCRICAO']           = jn_utf8_encode($rowColunas->LABEL_CAMPO);
					$dadoColuna['TIPO_CAMPO']          = jn_utf8_encode(strtoupper($rowColunas->TIPO_CAMPO));
					$dadoColuna['ESCONDER480']         = false;
					$dadoColuna['ORDENACAO']           = "SEM";	
					$dadoColuna['GRID']                = jn_utf8_encode($rowColunas->FLAG_EXIBIR_GRID);
					/*if(($colocarOrdenacao)and($dadoColuna['GRID']=='S')){
						$colocarOrdenacao = false;
						$dadoColuna['ORDENACAO'] = 'ASC';
					}*/
					$dadoColuna['CHAVE']			   = jn_utf8_encode($rowColunas->FLAG_CHAVEPRIMARIA);
					if($dadoColuna['CHAVE']=='S'){
						$chave = $dadoColuna['NOME_CAMPO'];
					}
					$retorno['COLUNAS'][] = $dadoColuna; 
				}
				
			}
			
			$retorno['COLUNAS'][0]['NOME_CHAVE'] = $chave;
			
			$retorno['PERMISSOES'] = $permissoes;
			$retorno['STATUS'] = 'OK';
		}
	}
	
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='dados'){
	

	$retorno;
	
	$dadosInput['tab'] = tabelaGrid($dadosInput['tab']);
	
	if($dadosInput['ordem']==''){
		$dadosInput['ordem'] = ordemGrid($dadosInput['tab']);
	}
	
	$filtros = '';
	if($dadosInput['tipoR']=='R'){
		$filtros = ' AND TIPO_CAMPO <> "BUTTON" ';
	}

	$queryColunas = 'select NOME_CAMPO,LABEL_CAMPO,FLAG_EXIBIR_GRID,TIPO_CAMPO from cfgcampos_sis_cd where nome_campo is not null ' . $filtros . ' and nome_tabela = '.aspas($dadosInput['tab']).' order by numero_ordem_criacao';
	
	$resColunas = jn_query($queryColunas);
	
	$campos  = ''; 
	$retorno  = array(); 
	$limite= '';
	
	$filtros = ' WHERE 1 = 1 ';
	
	$filtros .= CompSql($dadosInput['tab'],'GRID');
	
	$labelCampo = array();
	$tipoCampo = array();

	while($rowColunas = jn_fetch_object($resColunas))
	{
		
		if (($rowColunas->NOME_CAMPO=='DATA_EXCLUSAO') or 
 		    ($rowColunas->NOME_CAMPO=='DATA_INUTILIZACAO_REGISTRO') or 
		    ($rowColunas->NOME_CAMPO=='DATA_INUTILIZ_REGISTRO') or 
		    ($rowColunas->NOME_CAMPO=='DATA_DESCREDENCIAMENTO')) 
		{
			$campoDataExclusao = $rowColunas->NOME_CAMPO;
		}

        if(trim($rowColunas->NOME_CAMPO)<>''){
			if($campos != '')
				$campos = $campos.',';
			
			$campos  = $campos . $rowColunas->NOME_CAMPO;
		}
		if($rowColunas->FLAG_EXIBIR_GRID=='S'){
			$labelCampo[$rowColunas->NOME_CAMPO]= $rowColunas->LABEL_CAMPO;
			$tipoCampo[$rowColunas->NOME_CAMPO]= $rowColunas->TIPO_CAMPO;
		}
	}

	if(count($dadosInput['filtros'])>0)
	{
		for($i=0;$i<count($dadosInput['filtros']);$i++)
		{
			$upper = '';
			if($dadosInput['filtros'][$i]['VALOR']<>strtoupper($dadosInput['filtros'][$i]['VALOR'])){
				$upper = 'upper';
				$dadosInput['filtros'][$i]['VALOR'] = strtoupper($dadosInput['filtros'][$i]['VALOR']);
			}
			
			//pr($dadosInput['filtros'][$i]['CAMPO'],true);

			if ($dadosInput['filtros'][$i]['CAMPO'] == "WHERELITERAL")
				$filtros .=' and '. $dadosInput['filtros'][$i]['VALOR'] . ' ';	
			else if($dadosInput['filtros'][$i]['TIPO'] == "L")
				$filtros .=' and '.$upper.' ('. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ') Like '. aspas('%'.($dadosInput['filtros'][$i]['VALOR']).'%').' ';	
			else if($dadosInput['filtros'][$i]['TIPO'] == "D")
				$filtros .=' and '.$upper.' ('. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ') = ' . dataToSql($dadosInput['filtros'][$i]['VALOR']).' ';	
			else
				$filtros .=' and '.$upper.' ('. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ') = ' . aspas(($dadosInput['filtros'][$i]['VALOR'])).' ';		
		}
	}

	if ($dadosInput['criterioWhere']!='')
	{
	   $criterioWhere = stringReplace_Delphi_All($dadosInput['criterioWhere'],'\\','');
	   //pr($criterioWhere);

	   if ($filtros != '')
	   	   $filtros .= ' and ';
	   	
 	   $filtros .= $criterioWhere;
	}
		

	if (($dadosInput['apenasRegistrosAtivos']=='S') and ($campoDataExclusao!=''))
	{
	   if ($filtros != '')
	   	   $filtros .= ' and ';
	   	
 	   $filtros .= $campoDataExclusao . ' is null';
	}


	if(count($dadosInput['subprocesso'])>0){
		$querySubProcesso = 'SELECT 
							NOME_TABELA_PRINCIPAL,CAMPO_LIGACAO_PRINCIPAL_01,CAMPO_LIGACAO_PRINCIPAL_01,
							NOME_TABELA_FILHA,CAMPO_LIGACAO_FILHA_01,CAMPO_LIGACAO_FILHA_02,
							TIPO_RELACAO,FLAG_ABRE_SEQUENCIA_INCLUS
						FROM cfgtabelas_subprocessos_cd where numero_registro ='.aspas($dadosInput['subprocesso']['registro']);
		
		$resSubProcesso = jn_query($querySubProcesso);
		
		if($rowSubProcesso = jn_fetch_object($resSubProcesso)){

			$camposSub = "";
			
			if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01 != ''){
				$camposSub  =  strtolower($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01).' CAMPO01 ';
			}
			if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02 != ''){
				$camposSub .=  ' , '.strtolower($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02).' CAMPO02';
			}
			
			$queryQteRegistro = "Select ".$camposSub." from ".strtolower($rowSubProcesso->NOME_TABELA_PRINCIPAL)."
								 where ". strtolower($rowSubProcesso->NOME_TABELA_PRINCIPAL) .".".strtolower($dadosInput['subprocesso']['nomeChave'])." = ".aspas($dadosInput['subprocesso']['chave']);
			$resQteRegistro = jn_query($queryQteRegistro);
			if($rowQteRegistro = jn_fetch_object($resQteRegistro)){
					$auxFiltro = "";
					
					if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01 != ''){
						$auxFiltro  = ' '.strtolower($rowSubProcesso->CAMPO_LIGACAO_FILHA_01).' = '. aspas($rowQteRegistro->CAMPO01);
					}
					if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02 != ''){
						$auxFiltro .=  ' and  '.strtolower($rowSubProcesso->CAMPO_LIGACAO_FILHA_02).' = '. aspas($rowQteRegistro->CAMPO02);
					}
			}
			
			if($auxFiltro != ''){
				$filtros .=' and '.$auxFiltro;
			}
			
			
		}		
	}
	

	$queryColunas = 'select '.$campos.' from ' . strtolower($dadosInput['tab']).$filtros;
	
	if($dadosInput['ordem']!=''){
		$queryColunas= $queryColunas . ' order by '. $dadosInput['ordem'].' ';
	}	
	
	

	if($_SESSION['type_db'] =='mysql'){
		
		if($dadosInput['numpag']>0){
			$limite= ' limit '.(($dadosInput['pag']-1)*$dadosInput['numpag']).','.$dadosInput['numpag'];
		}
		$queryColunas = $queryColunas.$limite;
		
	}else if (($_SESSION['type_db'] =='mssqlserver') or ($_SESSION['type_db'] =='sqlsrv')){
			$queryColunas =	"		WITH paginacao AS
										(
											SELECT ".$campos.",
											indice = ROW_NUMBER() OVER (ORDER BY ". $dadosInput['ordem'] .") 
											FROM ".$dadosInput['tab']." " . $filtros  ."
										)
										SELECT ".$campos."
										FROM paginacao
										WHERE indice BETWEEN ".(($dadosInput['pag']-1)*$dadosInput['numpag'])." AND ".(((($dadosInput['pag']-1)*$dadosInput['numpag'])+$dadosInput['numpag'])-1) ."
										ORDER BY " . $dadosInput['ordem'];
	}else if($_SESSION['type_db'] =='firebird'){
		
		$queryColunas = 'select first '.$dadosInput['numpag'].' skip '.(($dadosInput['pag']-1)*$dadosInput['numpag']).' '.$campos.' from ' . strtolower($dadosInput['tab']).$filtros;
	
		if($dadosInput['ordem']!=''){
			$queryColunas= $queryColunas . ' order by '. $dadosInput['ordem'].' ';
		}	

	}
	
	
	
	$retorno['PAGINAS'] = 0;
	
	if(($dadosInput['pag'] == 1) and ($dadosInput['numpag']>0)){
		$queryCount = 'select count(*) REGISTROS from ' . strtolower($dadosInput['tab']).$filtros;
		$resCount = jn_query($queryCount);
		if($rowCount = jn_fetch_object($resCount)){
			$retorno['PAGINAS'] =  ceil($rowCount->REGISTROS);
		}
	}else if($dadosInput['pag'] == 1){
		$retorno['PAGINAS'] = 1;
	}
	
	if($retorno['PAGINAS'] == 0){
		$retorno['PAGINAS'] = 1;
	}
	$resColunas = jn_query($queryColunas);
	
	$retorno['DADOS'] = array();
	
	while($rowColunas = jn_fetch_object($resColunas)){
		$linha = null;
		//if($rowColunas->CODIGO_ASSOCIADO == '014000100306017'){
		//
		//pr($rowColunas);
		//}
		foreach ($rowColunas as $key => $value){
			
			//if($rowColunas->CODIGO_ASSOCIADO == '014000100306017'){
			//	pr($key.'->'.$value);
			//}
			
			if($tipoCampo[$key]=='NUMERIC'){
				$value =  number_format($value, 2, ',', '.');				
			}
			if($dadosInput['tipoR']=='R'){
				if($labelCampo[$key]!=''){
					$pos = strpos(strtoupper($labelCampo[$key]), 'DATA');

					if ($pos === false) {
						$pos = strpos(strtoupper($labelCampo[$key]), 'IMPRIMIR');
						if ($pos === false) {
							$dado[jn_utf8_encode($labelCampo[$key])] = jn_utf8_encode($value);
						}
						
					}else{
						if(is_object($value))
							$dado[jn_utf8_encode($labelCampo[$key])] = sqlToData($value->format('Y-m-d'));
						else
							$dado[jn_utf8_encode($labelCampo[$key])] = sqlToData($value);
					}
					
				}
			}else{
				if(is_object($value))
					$dado[$key] = jn_utf8_encode($value->format('Y-m-d')); 
				else
				{
					if( $_SESSION['type_db'] == 'mssqlserver')
					{
						$pos = strpos(strtoupper($labelCampo[$key]), 'DATA');

						if (($pos === false) || (trim($value) === ''))
						{
							$dado[$key] = jn_utf8_encode($value);
						}
						else
						{
							//pr('aaaaa' . sqlToData($value));
							
							$value      = sqlToData($value);
							$stringData = substr($value,6,4) . '-' . substr($value,3,2) . "-" . substr($value,0,2);; 
							//pr($stringData);

							$dado[$key] = $stringData;
						}
					}
					else
    					$dado[$key] = jn_utf8_encode($value);
				}
			}
			
			//$linha[]       = $dado;
		}
		
		$retorno['DADOS'][] = $dado;
	}
	//pr($retorno);
    //exit;
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='registro'){
	
	$retorno = array();
	
	$dadosInput['tab'] = tabelaGrid($dadosInput['tab']);
	
	
	$queryColunas = 'select NOME_CAMPO from cfgcampos_sis_cd where nome_campo is not null and nome_tabela = '.aspas($dadosInput['tab']).' order by numero_ordem_criacao';
	
	$resColunas = jn_query($queryColunas);
	
	$campos  = ''; 
	//$retorno  = ''; 
	$limite= '';
	
	$filtros = ' WHERE '.$dadosInput['nomeChave'].'='.aspas($dadosInput['chave']).' ';
	
	
	
	while($rowColunas = jn_fetch_object($resColunas)){
        if(trim($rowColunas->NOME_CAMPO)<>''){
			if($campos != '')
				$campos = $campos.',';
			
			$campos  = $campos . $rowColunas->NOME_CAMPO;
		}
	}
		
	

	$queryColunas = 'select '.$campos.' from ' . strtolower($dadosInput['tab']).$filtros;
		
	$resColunas = jn_query($queryColunas);
	
	$retorno['DADOS'] = array();

	if($rowColunas = jn_fetch_object($resColunas)){
		$linha = null;
		$dado = array();

		foreach ($rowColunas as $key => $value){	
			if ($value instanceof DateTime) {
				$dado[$key] = jn_utf8_encode($value->format('Y-m-d'));
			}elseif(is_object($rowInformacoes[($itemCampos['NOME_CAMPO'])])){						
				$dado[$key] = jn_utf8_encode($value->format('Y-m-d'));
			}else{
				$dado[$key] = jn_utf8_encode($value);
			}
			
		}
		
		$retorno['DADOS'][] = $dado;
	}
	
	echo json_encode($retorno);

}





?>