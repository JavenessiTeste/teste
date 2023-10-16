<?php

function saidaCampoPD($IdProcesso,$nomeProcesso,$campo,$valor,$campos){

	$retorno=array();
	$retorno['MSG'] ='';
	$retorno['MSG_CONFIRMA'] ='';
	$retorno['CAMPOS'] =array();
	
	//pr($IdProcesso);
	//pr('$campo' . $campo);
	//pr('$valor' . $valor);
	//pr($campos);

	if($IdProcesso=='7003')
	{
		if($campo=='DIA_VENCIMENTO_FINAL')
		{
			if($valor<$campos['DIA_VENCIMENTO_INICIAL']){
				$item = alteraComportamentoCampo('DIA_VENCIMENTO_INICIAL', '1');
				$retorno['MSG'] ='Dia Vencimento Inicial deve ser menor igual a Dia Vencimento Final';
				$item['VALOR'] = $valor;
				$retorno['CAMPOS'][] = $item;
			}
		}
		
		if($campo=='DIA_VENCIMENTO_INICIAL')
		{
			if($valor>$campos['DIA_VENCIMENTO_FINAL'])
			{
				$item = alteraComportamentoCampo('DIA_VENCIMENTO_FINAL', '1');
				$retorno['MSG'] ='Dia Vencimento Inicial deve ser menor igual a Dia Vencimento Final';
				$item['VALOR'] = $valor;
				$retorno['CAMPOS'][] = $item;
			}
			
		}
		
	}
	


	if($IdProcesso=='7010')
	{
		if($campo=='COMBO_TIPO_PROCESSO')
		{
			// Aqui, se for estorno eu removo da tela o campo tipo do arquivo. Se não for, eu removo da tela o numero do lote
			if (($valor=='ESTORNO_MANTER') or 
			    ($valor=='ESTORNO_VOLTAR'))
			{
				$retorno['MSG']      = 'Para este tipo de processo você precisa informar o número do lote';

				$item = alteraComportamentoCampo('COMBO_TIPO_ARQUIVO', '3');
				$item['VALOR']       = '';
				$retorno['CAMPOS'][] = $item;

				$item = alteraComportamentoCampo('NUMERO_LOTE_NFSE', '1');
				$item['VALOR']       = '';
				$retorno['CAMPOS'][] = $item;
			}
			else
			{
				$retorno['MSG']      = 'Para este tipo de processo você precisa informar o tipo de arquivo a gerar';

				$item = alteraComportamentoCampo('NUMERO_LOTE_NFSE', '3');
				$item['VALOR']       = '';
				$retorno['CAMPOS'][] = $item;

				$item = alteraComportamentoCampo('COMBO_TIPO_ARQUIVO', '1');
				$item['VALOR']       = '';
				$retorno['CAMPOS'][] = $item;
			}
		}
	}


	return $retorno;
}

function alteraComportamentoCampo($nomeCampo, $comportamento){

	$campo = array();
	$campo['NOME_CAMPO']   = $nomeCampo;
	$campo['COMPORTAMENTO']= $comportamento;		
	return $campo;	
}