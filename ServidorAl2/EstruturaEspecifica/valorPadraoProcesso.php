<?php
//$retorno['AUX_PADRAO'],$itemCampos['NOME_CAMPO'],$rowCampos->VALOR_PADRAO
function valorPadraoProcesso($padrao,$campo,$valor){
	$retorno = $valor;
	if(($padrao=='INFORMEPAGAMENTOS') and ($campo=='ANO')){
		$retorno = (@date('Y'))-1;
	}
	if(($padrao=='DECLARACAOPERMANENCIA') and ($campo=='ANO')){
		$retorno = (@date('Y'))-1;
	}
	if(($padrao=='GERARGUIAS') and ($campo=='ANO_GUIA')){
		$retorno = (@date('Y'));
	}
	return $retorno;
}

function valorOpcoesComboProcesso($padrao,$campo,$valor){
	
	//pr($padrao);
	//pr($campo);
	//pr($valor);

	$retorno = $valor;
	
	if(($padrao=='INFORMEPAGAMENTOS') and ($campo=='ANO')){
		$retorno = '';
		
		$query = 'SELECT EXTRACT(YEAR FROM DATA_ADMISSAO) AS ANO FROM PS1000 WHERE CODIGO_ASSOCIADO = '.aspas($_SESSION['codigoIdentificacao']).'';
		
		$res = jn_query($query);
		$row = jn_fetch_assoc($res);
		$ano_atual = @date('Y');
		
		for($Pano = $row['ANO']; $Pano < $ano_atual; $Pano++ ){
			$retorno .= $Pano.';'; 
		}


	}
	else if(($padrao=='ARQ_REMESSA') and ($campo=='COMBO_TIPO_ARQUIVO'))
	{

		$retorno = '';
		$retorno.= '01 - Padrão Bradesco 400 Bytes;';
		$retorno.= '02 - Padrao Itaú 400 Bytes;';
		$retorno.= '03 - Padrao Banco do Brasil 400 Bytes;';
		$retorno.= '04 - Padrao Bradesco 240 Bytes;';
		$retorno.= '05 - Padrao Bradesco 240 Bytes;';
		$retorno.= '06 - Padrao Bradesco 240 Bytes;';
		$retorno.= '07 - Padrao Santander 240 Bytes;';

		$retorno = utf8_decode($retorno);

	}	
	elseif(($padrao=='DECLARACAOPERMANENCIA') and ($campo=='ANO')){
		$retorno = '';
		
		$query = 'SELECT DISTINCT YEAR(DATA_VENCIMENTO) AS ANO FROM VW_DECLARACAO_PERM_PF WHERE CODIGO_ASSOCIADO = '.aspas($_SESSION['codigoIdentificacao']).'';
		
		$res = jn_query($query);
		$retorno = '';

		$i = 0;
		while($row = jn_fetch_object($res)){
			if($i > 0)
				$retorno .= ';';

			$retorno .= $row->ANO; 

			$i++;
		}
		
	}else if(($padrao=='EVENTOSADICIONAIS') and ($campo=='MESANO')){
		
		if($_SESSION['codigoSmart'] == '3389'){//Vidamax
			$query  = " SELECT DISTINCT MES_ANO_VENCIMENTO, cast('01/' + MES_ANO_VENCIMENTO as date) FROM PS1023 ";
			$query .= " INNER JOIN PS1000 ON (PS1023.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
			$query .= " WHERE PS1000.CODIGO_TITULAR = " . aspas($_SESSION['codigoIdentificacao']) . " OR PS1000.CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']);
			$query .= " ORDER BY cast('01/' + MES_ANO_VENCIMENTO as date) DESC ";
		}else if($_SESSION['type_db'] == 'sqlsrv'){
			$query  = " SELECT DISTINCT MES_ANO_VENCIMENTO, cast('01/' + MES_ANO_VENCIMENTO as date) FROM PS1023 ";
			$query .= " WHERE CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']);
			$query .= " ORDER BY cast('01/' + MES_ANO_VENCIMENTO as date) DESC ";
		}else{
			$query  = " SELECT DISTINCT MES_ANO_VENCIMENTO, cast('01/' || MES_ANO_VENCIMENTO as date) FROM PS1023 ";
			$query .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
			$query .= " ORDER BY cast('01/' || MES_ANO_VENCIMENTO as date) DESC ";
		}
		

		$res = jn_query($query);
		$retorno = '';
		while($row = jn_fetch_assoc($res)){
			
			if($retorno!=='')
				$retorno .= ';';
			
			$retorno .= $row['MES_ANO_VENCIMENTO']; 
		}	
	}else if(($padrao=='CONFERENCIAUTITILIZACAO') and ($campo=='MESANO')){
		
		$query  =  " SELECT DISTINCT MES_ANO, ANO_MES FROM VW_UTILIZACAO_WEB ";
		$query .=	" WHERE ANO_MES IS NOT NULL AND MES_ANO IS NOT NULL AND CODIGO_TITULAR = " . aspas($_SESSION['codigoIdentificacao']);
		$query .=	" ORDER BY ANO_MES DESC ";
				
		$res = jn_query($query);
		$retorno = '';
		while($row = jn_fetch_object($res)){
			if(rvc('REGRA_ESPEC_MES_UTILIZACAO') == '' || rvc('REGRA_ESPEC_MES_UTILIZACAO') == 'SIM'){			
				if($retorno!=='')
					$retorno .= ';';
				
				$mesAlterar = explode("/", $row->MES_ANO);
				$mesApresentar = '';
				if($mesAlterar[0] == '12'){
					$mesApresentar = '01/' . ($mesAlterar[1] + 1);
				}else{
					$mesApresentar = str_pad(($mesAlterar[0] + 1),2, "0", STR_PAD_LEFT) . '/' . $mesAlterar[1];
				}
				$retorno .= $mesApresentar;
			}else{
				if($retorno!=='')
					$retorno .= ';';
				
				$retorno .= $row->MES_ANO; 
			}
		}		
	}else if(($padrao=='CARTEIRINHA') and ($campo=='CODIGO_ASSOCIADO') and retornaValorConfiguracao('COMBO_CARTEIRINHA') == 'SIM'){
		$retorno = '';
		
		$queryColunas = 'select CODIGO_TITULAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$resColunas   = jn_query($queryColunas);
		$rowColunas   = jn_fetch_object($resColunas);		
		
		$queryAssociados = 'SELECT CODIGO_ASSOCIADO, NOME_ASSOCIADO FROM PS1000 ';
		if(retornaValorConfiguracao('APRES_TODOS_ASSOC_DEP') == 'SIM'){
			$queryAssociados .= ' WHERE CODIGO_TITULAR ='. aspas($rowColunas->CODIGO_TITULAR);
		}else{
			$queryAssociados .= ' WHERE ((CODIGO_TITULAR ='. aspas($_SESSION['codigoIdentificacao']) . ') or (CODIGO_ASSOCIADO = '. aspas($_SESSION['codigoIdentificacao']) . '))';				
		}
		
		$queryAssociados .= ' AND ((DATA_EXCLUSAO IS NULL) or (DATA_EXCLUSAO >= CURRENT_TIMESTAMP)) ';		

		$resAssociados   = jn_query($queryAssociados);		
		while($rowAssociados   = jn_fetch_object($resAssociados)){
			if($retorno  != '')
				$retorno .= ';';
			
			$retorno .= $rowAssociados->CODIGO_ASSOCIADO . ' - ' . $rowAssociados->NOME_ASSOCIADO ; 			
		}
	
	}

	//pr($retorno);

	return $retorno;
}		
	
	
?>