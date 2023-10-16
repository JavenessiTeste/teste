<?php

function ordemGrid($tabela){
	$retorno;
	
	if(($tabela=='VW_CONSULTA_MENSALIDADES_AL2') || ($tabela=='VW_SEGUNDA_VIA_AL2')) {
		$retorno = ' DATA_VENCIMENTO DESC';
	}
	if($tabela=='VW_PROTOCOLO_GERAL_SIS'){
		$retorno = ' DATA_ABERTURA_PROTOCOLO DESC';
	}
	if($tabela=='VW_PS5750_CD_AL2'){
		$retorno = ' DATA_CADASTRAMENTO DESC, NUMERO_REGISTRO DESC';
	}
	if($tabela=='VW_COMISSOES'){
		$retorno = ' NUMERO_CONTRATO DESC';
	}
	if($tabela=='VW_GLOSA_EXCEL_AL2'){
		$retorno = ' NUMERO_PROTOCOLO DESC';
	}
	if($tabela=='VW_GUIAS_DIGITADAS'){
		$retorno = ' DATA_CADASTRAMENTO DESC';
	}
	if($tabela=='VW_LOTE_CAB'){
		$retorno = ' DATA_ENTREGA DESC';
	}
	if($tabela=='VW_PAGAMENTOS_EFETUADOS'){
		$retorno = ' DATA_VENCIMENTO DESC';
	}

	if($tabela=='VW_VND1000_INCONSISTENCIAS'){
		$retorno = ' CODIGO_TITULAR, CODIGO_ASSOCIADO';
	}
	
	if($tabela=='VW_PS1095_CD_AL2'){
		$retorno = ' DATA_SOLICITACAO DESC';
	}
	if($tabela=='VW_PS6550_CD_AL2'){
		$retorno = ' DATA_SOLICITACAO DESC';
	}
	if($tabela=='VW_PS6360_ALIANCANET2'){
		$retorno = ' DATA_SOLICITACAO DESC';
	}

	
	if($tabela=='VW_PROTOCOLOS_GUIAS_AL2'){
		$retorno = ' NUMERO_PROTOCOLO DESC';
	}	
	if($tabela=='VW_PS1063_AL2'){
		$retorno = ' DATA_SOLICITACAO desc';
	}	
	if($tabela=='VW_PS5804_AL2'){
		$retorno = ' NUMERO_REGISTRO DESC';
	}
	if($tabela=='VW_PS6110_ALIANCANET2'){
		$retorno = ' data_reclamacao_sugestao desc';
	}			
	if (($tabela=='VW_PS6500_CD_AL2') || ($tabela=='PS6500')){
		$retorno = ' DATA_AUTORIZACAO DESC, NUMERO_AUTORIZACAO desc';
	}
	if($tabela=='VW_RELATORIO_GLOSA_CAB'){
		$retorno = ' NUMERO_PROTOCOLO DESC';
	}
	if($tabela=='VW_PS2500_CD_AL2'){
		$retorno = ' DATA_CADASTRAMENTO desc, NUMERO_PLANO_TRATAMENTO DESC';
	}
	if($tabela=='PS2510'){
		$retorno = ' DATA_PROCEDIMENTO desc, NUMERO_REGISTRO DESC';
	}
	if($tabela=='VW_COPARTICIPACAO'){
		$retorno = ' DATA_PROCEDIMENTO desc';
	}
	if($tabela=='VW_CONFERENCIA_UTILIZACAO'){
		$retorno = ' DATA_PROCEDIMENTO desc';
	}
	if($tabela=='VW_REDECREDENCIADA_AL2'){
		$retorno = ' NOME_PRESTADOR ';
	}
	
	if($tabela=='VW_PENDENCIAS_PRESTADOR_AL2'){
		$retorno = ' DATA_PENDENCIA DESC ';
	}
	
	if($tabela=='PROTOCOLOS_PS5750_AL2'){
		$retorno = ' DATA_CADASTRAMENTO DESC ';
	}
	
	if($tabela=='VW_ARQ_NF_PREST_AL2'){
		$retorno = ' DATA_ENVIO DESC ';
	}
	
	if($tabela=='VW_NOVAS_PENDENCIAS_PREST_AL2'){
		$retorno = ' DATA_VENCIMENTO DESC ';
	}
	
	if($tabela=='VW_PS5294_AL2'){
		$retorno = ' DATA_ENVIO DESC ';
	}
	
	if($tabela=='VW_UPLOAD_ARQUIVOS_XML_AL2'){
		$retorno = ' DATA_UPLOAD DESC, NUMERO_REGISTRO DESC ';
	}
	
	if($tabela=='VW_OUVIDORIA_AL2'){
		$retorno = ' NUMERO_REGISTRO DESC ';
	}
	
	if($tabela=='VW_OUVIDORIA_RESP_AL2' || $tabela == 'VW_PS5297_AL2' || $tabela == 'VW_ARQUIVOS_XML_DASH_AL2'){
		$retorno = ' NUMERO_REGISTRO DESC ';
	}

	if($tabela=='VW_AGENDA_PLENA_AL2'){
		$retorno = ' NUMERO_REGISTRO DESC ';
	}


	if($tabela =='VW_PS5722_GRID'){
		$retorno = ' DATA_PAGAMENTO_PRESTADOR DESC ';
	}
	
	if($tabela =='VW_VND1000_CAAPSML'){
		$retorno = ' CODIGO_ASSOCIADO ';
	}

	if($tabela =='VW_COPARTICIPACAO_AL2'){
		$retorno = ' DATA_EVENTO ';
	}

	if($tabela =='VW_CFGCOMUNICACAO_NET_AL2'){
		$retorno = ' NUMERO_REGISTRO DESC ';
	}
	

	if($tabela =='VW_COMUNICACAO_NET_AL2'){
		$retorno = ' NUMERO_REGISTRO DESC ';
	}

	if($tabela =='VW_GUIAS_ENVIADAS_AL2'){
		$retorno = ' NUMERO_PROTOCOLO DESC ';
	}

	if($tabela =='VW_TOTALIZAR_ARQ_XML_AL2'){
		$retorno = ' DATA_UPLOAD DESC ';
	}

	if($tabela =='VW_NF_SOLICITADAS_AL2'){
		$retorno = ' DATA_CADASTRO DESC ';
	}

	if($tabela =='VW_COPART_ADIANTADA_PJ_AL2'){
		$retorno = ' DATA_PROCEDIMENTO DESC ';
	}

	if($tabela =='VW_LINKS_VENDAS_AL2'){
		$retorno = ' NOME_PLANO, NOME_VENDEDOR ';
	}

	if($tabela =='VW_COBERTURA_PLANO_AL2'){
		$retorno = ' NOME_PROCEDIMENTO ';
	}

	if($tabela =='ESP_HIST_CARTOES_ASSOCIADOS'){
		$retorno = ' NUMERO_REGISTRO DESC ';
	}
	
	if ($retorno==''){
		$queryColunas = 'select first 1 NOME_CAMPO from cfgcampos_sis_cd where nome_campo is not null and nome_tabela = '.aspas($tabela).' order by numero_ordem_criacao';
			
		$resColunas = jn_query($queryColunas);
		
		if($rowColunas = jn_fetch_object($resColunas)){
			$retorno = ' '.$rowColunas->NOME_CAMPO.' ';
		}else{
			$retorno = ' 1 ';
		}
	}
	return $retorno;
}


?>