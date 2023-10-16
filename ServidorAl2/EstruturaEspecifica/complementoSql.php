<?php


function CompSql($tabela,$origem,$camposComplementares=array()){
	//$origem = AUTO = AUTOCOMPLETE
	//$origem = GRID = GRID
	//$_SESSION['codigoIdentificacao'] // Usuario da secao
	//camposComplementares retorno dos campos cadastrados no campo CAMPOS_PESQUISA_AUX_AUTO = [{"CAMPO":"NUMERO_AUTORIZACAO"}]
	// para pegar os dados $camposComplementares['NUMERO_AUTORIZACAO'] 

	$retorno = '';
	
	
	if($tabela =='VW_SEGUNDA_VIA_AL2' || $tabela =='VW_REATIVACAO_AL2'){		
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if(($_SESSION['perfilOperador']=='EMPRESA')or($_SESSION['perfilOperador']=='EMPRESA_CNPJ'))
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_PS6110_ALIANCANET2'){
		if(($_SESSION['perfilOperador'] == 'BENEFICIARIO') or ($_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if ($_SESSION['perfilOperador'] == 'EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else if ($_SESSION['perfilOperador'] == 'VENDEDOR')
			$retorno = ' and CODIGO_VENDEDOR ='. aspas($_SESSION['codigoIdentificacao']);
		else if ($_SESSION['perfilOperador'] != 'OPERADOR')			
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_CONSULTA_MENSALIDADES_AL2'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO') or ($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}

	if (($tabela =='CFGCOMUNICACAO_NET') and ($_SESSION['perfilOperador'] <> 'OPERADOR')) {
		$retorno = " AND ((','||PERFIS_VISIVEL||',' LIKE ". aspas('%'.$_SESSION['perfilOperador'].'%'). ") OR (PERFIS_VISIVEL  LIKE '%TODOS%'))" ;
		$retorno .=" AND ((','||FILTRO_CODIGOS||',' LIKE ". aspas('%'.$_SESSION['codigoIdentificacao'].'%'). ") OR (FILTRO_CODIGOS  LIKE '%TODOS%'))" ;
		if(retornaValorConfiguracao('VALIDA_DT_MENSAGEM') == 'SIM'){			
			$retorno .=" AND ((DATA_MENSAGEM is null)  OR (DATA_MENSAGEM  <= current_timestamp))" ;			
		}
	}

	if($tabela =='VW_COMUNICACAO_NET_AL2' and ($_SESSION['perfilOperador'] <> 'OPERADOR')){
		$retorno = " AND ((','||PERFIS_VISIVEL||',' LIKE ". aspas('%'.$_SESSION['perfilOperador'].'%'). ") OR (PERFIS_VISIVEL  LIKE '%TODOS%'))" ;
		$retorno .=" AND ((','||FILTRO_CODIGOS||',' LIKE ". aspas('%'.$_SESSION['codigoIdentificacao'].'%'). ") OR (FILTRO_CODIGOS  LIKE '%TODOS%') OR (FILTRO_CODIGOS IS NULL))" ;
		if(retornaValorConfiguracao('VALIDA_DT_MENSAGEM') == 'SIM'){
			$retorno .=" AND ((DATA_MENSAGEM is null)  OR (DATA_MENSAGEM  <= current_timestamp))" ;			
		}
		
		if($_SESSION['codigoSmart'] == '3423' and $_SESSION['perfilOperador'] == 'BENEFICIARIO'){//Plena
			$queryTpProduto = ' SELECT FLAG_PLANOFAMILIAR, CODIGO_TIPO_CARACTERISTICA FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
			$resTpProduto = jn_query($queryTpProduto);
			$rowTpProduto = jn_fetch_object($resTpProduto);
			
			$tpProduto = '';
			
			if($rowTpProduto->FLAG_PLANOFAMILIAR == 'S' and $rowTpProduto->CODIGO_TIPO_CARACTERISTICA == '10'){
				$tpProduto = " OR (TIPO_PRODUTO = 'O') OR (TIPO_PRODUTO = 'OPF') ";
			}elseif($rowTpProduto->FLAG_PLANOFAMILIAR == 'N' and $rowTpProduto->CODIGO_TIPO_CARACTERISTICA == '10'){
				$tpProduto = " OR (TIPO_PRODUTO = 'O') OR (TIPO_PRODUTO = 'OPJ') ";
			}elseif($rowTpProduto->FLAG_PLANOFAMILIAR == 'S' and $rowTpProduto->CODIGO_TIPO_CARACTERISTICA != '10'){
				$tpProduto = " OR (TIPO_PRODUTO = 'S') OR (TIPO_PRODUTO = 'SPF') ";
			}elseif($rowTpProduto->FLAG_PLANOFAMILIAR == 'N' and $rowTpProduto->CODIGO_TIPO_CARACTERISTICA != '10'){
				$tpProduto = " OR (TIPO_PRODUTO = 'S') OR (TIPO_PRODUTO = 'SPJ') ";
			}
			
			$retorno .=" AND ((TIPO_PRODUTO IS NULL) OR (TIPO_PRODUTO = 'A') " . $tpProduto . ") ";
		}
	}
		
		
	if($tabela =='VW_PROTOCOLO_GERAL_SIS'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO') or ($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_CADASTRO_CONTATO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='OPERADOR')
			$retorno = ' and (CODIGO_OPERADOR IS NULL or CODIGO_OPERADOR ='. aspas($_SESSION['codigoIdentificacao']) . ' ) ';
		else
			$retorno = ' and 1 <> 1 ';
	}
		
		
	if($tabela =='VW_COPARTICIPACAO'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_ESPECIALIDADES_PREST_SUBST'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = '  and 1 = 1 ';
		else if($_SESSION['perfilOperador']=='EMPRESA') 
			$retorno = ' and CODIGO_EMPRESA = '. aspas($_SESSION['codigoIdentificacao']);		
	}
	
	if($tabela =='VW_REAJUSTE_EMPRESAS' || $tabela =='VW_SOLIC_EXC_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_GUIAS_DIGITADAS'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_GTO_NEGADA'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_GUIAS_DIGITADAS' || $tabela =='VW_NF_SOLICITADAS_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_MENSAGENS'){
		$retorno = " AND ((','||PERFIS_VISIVEL||',' LIKE ". aspas('%'.$_SESSION['perfilOperador'].'%'). ") OR (PERFIS_VISIVEL  LIKE '%TODOS%'))" ;
		if(retornaValorConfiguracao('VALIDA_DT_MENSAGEM') == 'SIM'){
			$retorno .=" AND ((DATA_MENSAGEM is null)  OR (DATA_MENSAGEM  <= current_timestamp))" ;			
		}
	}
	
	if($tabela =='VW_PAGAMENTOS_EFETUADOS'){
		if(($_SESSION['perfilOperador']=='VENDEDOR')or($_SESSION['perfilOperador']=='CORRETOR'))
			$retorno = ' and CODIGO_IDENTIFICACAO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else	
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_PRESTADOR_SUBSTITUTO'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and 1 = 1 ';
		else if($_SESSION['perfilOperador']=='EMPRESA') 
			$retorno = ' and CODIGO_EMPRESA = '. aspas($_SESSION['codigoIdentificacao']);
		
	}
	if($tabela =='VW_PROCEDIMENTOS_LIBERADOS' || $tabela =='VW_MATMED_LIBERADOS' || $tabela =='VW_SERVICOS_LIBERADOS' || $tabela =='VW_PROCED_LIBERADOS_ODONTO_AL2') {
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_ESPECIALIDADESPRESTADOR'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_CBO_PRESTADOR'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}	
	
	if($tabela =='VW_CONFERENCIA_UTILIZACAO'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_UTILIZACAO_AL2' || $tabela =='VW_CARTEIRINHA_AL2' || $tabela =='VW_TOKENS_ASSOC_AL2' || $tabela =='VW_TOKENS_VALIDOS_ASSOC_AL2'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and CODIGO_TITULAR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_UTILIZACAO_DESPESA_NET'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_UTILIZACAO_ASSOCIADO'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_LOTE_CAB'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_LOTE_GUIAS'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_ANS_NET'){
		$retorno = ' and 1 = 1 ';
	}

	if($tabela =='VW_COMISSOES'){
		if(($_SESSION['perfilOperador']=='VENDEDOR')or($_SESSION['perfilOperador']=='CORRETOR'))
			$retorno = ' and CODIGO_IDENTIFICACAO ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_VND1000_ON'){
		if(($_SESSION['perfilOperador']=='VENDEDOR')or($_SESSION['perfilOperador']=='CORRETOR'))
			$retorno = ' and ((CODIGO_VENDEDOR ='. aspas($_SESSION['codigoIdentificacao']) . ') or (CODIGO_VENDEDOR IS NULL)) ';
		elseif($_SESSION['perfilOperador']!='OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_RELATORIO_GLOSA_CAB'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_RELATORIO_GLOSA_DET'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_PS1100_CD_AL2'){
		if($_SESSION['perfilOperador']=='CORRETOR') 
			$retorno = ' and CODIGO_ID_SUPERIOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if (($tabela =='PS1000') or ($tabela =='VW_BENEF_CARTEIRINHA_AL2'))
	{
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
		{
			$queryColunas = 'select CODIGO_TITULAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
			$resColunas   = jn_query($queryColunas);
			$rowColunas   = jn_fetch_object($resColunas);
			
			if(retornaValorConfiguracao('APRES_TODOS_ASSOC_DEP') == 'SIM'){
				$retorno      = ' and CODIGO_TITULAR ='. aspas($rowColunas->CODIGO_TITULAR);
			}else{				
				$retorno      = ' and ((CODIGO_TITULAR ='. aspas($_SESSION['codigoIdentificacao']) . ') or (CODIGO_ASSOCIADO = '. aspas($_SESSION['codigoIdentificacao']) . '))';				
			}		
		}elseif($_SESSION['perfilOperador']=='EMPRESA'){
			$retorno      = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		}elseif($_SESSION['perfilOperador']=='PRESTADOR' and retornaValorConfiguracao('VALIDA_ASSOC_REDE_INDICADA') != 'SIM'){

		}elseif($_SESSION['perfilOperador']!='OPERADOR' and $_SESSION['perfilOperador'] !='PRESTADOR'){
			$retorno      = ' and 1 <> 1';
		}
	}

	
	if($tabela =='VW_PS6500_CD_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if (($tabela =='VW_PS1063_AL2') or ($tabela == 'VW_PS6360_ALIANCANET2')) {
		if($_SESSION['perfilOperador']=='BENEFICIARIO'){
			if($_SESSION['codigoIdentificacao'] == $_SESSION['codigoIdentificacaoTitular']){
				$retorno = ' and CODIGO_TITULAR ='. aspas($_SESSION['codigoIdentificacaoTitular']);	
			}else{
				$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);	
			}	
		}else{
			$retorno = ' and 1 <> 1 ';
		}
	}	
	
	
	if($tabela =='VW_PS2500_CD_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_TRANSFERENCIA_CADASTRO'){
		if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_PS1095_CD_AL2'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and ((CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']) . ' ) OR (CODIGO_TITULAR = ' . aspas($_SESSION['codigoIdentificacao']) . '))';
		else
			$retorno = ' and 1 <> 1 ';
	}	
		
	if($tabela =='VW_TMP1000_CD_AL2'){
		if($_SESSION['perfilOperador']=='VENDEDOR')
			$retorno = ' and CODIGO_IDENTIFICACAO ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
		
	if($tabela =='VW_TMP1000NET_EMPRESA_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_TMP1000NET_EMPRESA_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_PS1000_EMPRESA_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
		
	if($tabela =='VW_PS6550_CD_AL2'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='EMPRESA') 
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';		
	}
	
	
	if($tabela =='VW_PS5750_CD_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}	

	if($tabela =='PS1000' || $tabela =='VW_BENEFICIARIOS_ATIVOS_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA') 
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		
		if($_SESSION['perfilOperador']=='PRESTADOR' && retornaValorConfiguracao('VALIDA_ASSOC_REDE_INDICADA') == 'SIM') {
			$retorno = ' and CODIGO_PLANO IN (
								SELECT 
									CODIGO_PLANO 
								FROM PS1030 
								WHERE CODIGO_REDE_INDICADA IN (
									SELECT 
										CODIGO_REDE_INDICADA 
									FROM PS5013
									WHERE CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']) . '
								)
							) ';
			
		}
			
	}

	if($tabela =='VW_GLOSA_EXCEL_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);		
		else
			$retorno = ' and 1 <> 1 ';		
	}

	
	if($tabela =='VW_PS5804_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = " and TIPO_GUIA <> '' and CODIGO_PRESTADOR =". aspas($_SESSION['codigoIdentificacao']);		
		else
			$retorno = ' and 1 <> 1 ';		
	}
	
	if($tabela =='VW_AGENDA_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA') 
			$retorno = " and CODIGO_EMPRESA =". aspas($_SESSION['codigoIdentificacao']);		
		else if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = " and CODIGO_ASSOCIADO =". aspas($_SESSION['codigoIdentificacao']);		
		else if($_SESSION['perfilOperador']=='OPERADOR') 
			$retorno = " and 1 = 1 ";		
		else
			$retorno = ' and 1 <> 1 ';		
		
		$retorno .= ' and data_marcacao >= '.dataToSql(date("d/m/Y")).' ';
	}
	
	if (($tabela == 'PS1000') and ($origem == 'AUTO') and (retornaValorConfiguracao('APRESENTA_ASSOC_EXC') != 'SIM'))
	{
		
		$retorno = $retorno . ' and (DATA_EXCLUSAO IS NULL or (DATA_EXCLUSAO >= current_timestamp)) ';
	}

	if (($tabela == 'PS6100') and ($origem == 'AUTO'))
	{
		$retorno = $retorno . ' and ps6100.data_inutilizacao is null ';
	}
	
	if($tabela =='VW_PROTOCOLOS_GUIAS_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR') 
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);		
		elseif($_SESSION['perfilOperador'] != 'OPERADOR')
			$retorno = ' and 1 <> 1 ';		
	}
			

	if($tabela =='TABELA_AVO'){
			$retorno = ' and CODIGO_ID_USUARIO ='. aspas($_SESSION['codigoIdentificacao']);
	}


	if($tabela =='VW_PROCURACEP'){
			$retorno = ' and CEP <> '. aspas('--');
	}
	
	if($tabela =='VW_DETALHE_FATURAMENTO_NET'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela=='VW_DADOS_TELEFONE_AL2' || $tabela=='VW_DADOS_ENDERECO_AL2'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);		
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela=='VW_VND_CORRETOR_AL2' || $tabela=='VW_CONTRATOS_AL2'){
		if($_SESSION['perfilOperador']=='CORRETOR')
			$retorno = ' and CODIGO_CORRETOR ='. aspas($_SESSION['codigoIdentificacao']);		
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($_SESSION['codigoSmart'] == '4206'){
		if($tabela=='VND1030CONFIG_ON'){
			if(trim($camposComplementares['CODIGO_GRUPO_PESSOAS'])!=''){
				$retorno = " and ((CODIGO_GRUPO_PESSOAS_AUTOC=" .aspas($camposComplementares['CODIGO_GRUPO_PESSOAS']).") or ((CODIGO_GRUPO_PESSOAS_AUTOC is null) or (CODIGO_GRUPO_PESSOAS_AUTOC = ''))) ";
			}else{
				$retorno = " and ((CODIGO_GRUPO_PESSOAS_AUTOC is null) or (CODIGO_GRUPO_PESSOAS_AUTOC = '')) ";
			}
		}
	}
	
	if($tabela=='VW_CFGCOMUNICACAO_NET_AL2'){
		if($_SESSION['perfilOperador'] != 'OPERADOR'){
			$retorno .= ' AND (filtro_codigos LIKE \'%' . $_SESSION['codigoIdentificacao'] . '%\' or filtro_codigos = "TODOS"  or FILTRO_CODIGOS is null ) and UPPER(perfis_visivel) LIKE \'%' . $_SESSION['perfilOperador'] . '%\' ';		
		}
	}
	
	if($tabela=='VW_PS1069_AL2'){
		if($_SESSION['perfilOperador'] != 'OPERADOR'){
			$retorno .= " AND PERFIS_VISIVEL LIKE " . aspas($_SESSION['perfilOperador']);
		}
		if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
			$retorno .= " AND (CODIGO_EMPRESA IS NULL OR (CODIGO_EMPRESA = '') OR CODIGO_EMPRESA LIKE ('%'||cast((SELECT PS1000.CODIGO_EMPRESA FROM PS1000 WHERE CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']) . ") as varchar(15))||'%')) ";
		}		
	}
	
	if($tabela=='VW_DW_REGISTROS_RELATORIOS'){
		if(($_SESSION['perfilOperador']=='CORRETOR') or ($_SESSION['perfilOperador']=='VENDEDOR')){
			$retorno .= ' AND TABELA_CHAVE_PRIMARIA = ' . aspas('PS1100');
			$retorno .= ' AND CHAVE_PRIMARIA_FILTRO ='. aspas($_SESSION['codigoIdentificacao']);				
		}else{
			$retorno = ' and 1 <> 1 ';		
		}
	}

	if($tabela =='VW_AGENDA_PLENA_AL2'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO') 
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_PENDENCIAS_PRESTADOR_AL2' || $tabela =='VW_UPLOAD_ARQUIVOS_XML_AL2' || $tabela =='VW_PS5294_AL2' || $tabela =='VW_PS5297_AL2' || $tabela == 'VW_ARQUIVOS_XML_DASH_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR'){
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);			
		}elseif($_SESSION['perfilOperador'] != 'OPERADOR'){						
			$retorno = ' and 1 <> 1 ';
		}
	}
	
	if($tabela =='VW_OUVIDORIA_AL2'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO'){
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		}elseif($_SESSION['perfilOperador'] == 'OPERADOR'){
			$retorno = ' and DATA_CONCLUSAO IS NULL ';
		}else{			
			$retorno = ' and 1 <> 1 ';
		}
	}
	
	if($tabela =='VW_OUVIDORIA_RESP_AL2'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO'){
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		}elseif($_SESSION['perfilOperador'] == 'OPERADOR'){
			$retorno = ' and CODIGO_OPERADOR ='. aspas($_SESSION['codigoIdentificacao']);
		}else{			
			$retorno = ' and 1 <> 1 ';
		}
	}
	
	if($tabela == 'PS1030' && $_SESSION['perfilOperador'] == 'EMPRESA' && retornaValorConfiguracao('VALIDAR_PLANOS_ESP_EMPRESA') == 'SIM'){
		$retorno .= " AND PS1030.CODIGO_PLANO IN (SELECT PS1059.CODIGO_PLANO FROM PS1059 WHERE CODIGO_EMPRESA = " . aspas($_SESSION['codigoIdentificacao']) . ") ";			
	}
	
	if($tabela =='VW_BENEF_EMPR_NET' || $tabela =='VW_TOTALIZADOR_ASSOCIADOS_NET' || $tabela =='VW_CABECALHO_COPART_NET' || $tabela =='VW_ASSOC_EXC_EMP_AL2' || $tabela =='VW_PS1000_EXC_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA'){
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		}else{			
			$retorno = ' and 1 <> 1 ';
		}
	}
	
	if (($tabela == 'PS5000') and ($origem == 'AUTO'))
	{
		$retorno .= ' AND PS5000.DATA_DESCREDENCIAMENTO IS NULL ';
		
		if($_SESSION['perfilOperador']=='BENEFICIARIO' && retornaValorConfiguracao('VALIDA_ASSOC_REDE_INDICADA') == 'SIM') {
			$retorno .= ' and CODIGO_PRESTADOR IN (
								SELECT 
									CODIGO_PRESTADOR
								FROM PS5013								
								WHERE CODIGO_REDE_INDICADA IN (
									SELECT 
										CODIGO_REDE_INDICADA 
									FROM PS1000
									INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO)
									WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']) . '
								)
							) ';
			
		}

		
		$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
		$resEmp = jn_query($queryEmp);
		$rowEmp = jn_fetch_object($resEmp);	

		if($rowEmp->CODIGO_SMART =='3808'){//RS Saude
			$retorno .= ' AND ((PS5000.flag_rede_indicada is null) or (PS5000.flag_rede_indicada = "S")) ';
		}		
	}
	
	if($tabela =='VW_RELATORIO_COPART_NET'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela =='VW_SOLICIT_AUTORIZADAS_AL2'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO')
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador']!= 'OPERADOR')			
			$retorno = ' and 1 <> 1 ';		
	}
	
	if($tabela == 'VW_VND1000_OPERADORES'){
		if($_SESSION['perfilOperador']!= 'OPERADOR')			
			$retorno = ' and 1 <> 1 ';	
	}
	
	if($tabela =='VW_INADIMP_AL2'){
		$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
	}
	
	if($tabela =='PS5100'){		
		
		if($_SESSION['perfilOperador']== 'PRESTADOR' && retornaValorConfiguracao('VALIDA_ESPEC_PRESTADOR') == 'SIM') {
			$retorno = ' and CODIGO_ESPECIALIDADE IN (
								SELECT 
									CODIGO_ESPECIALIDADE 
								FROM PS5003
								WHERE CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']) . ') ';
		}
			
	}
	
	if($tabela == 'VW_NOMES_PLANOS' && $_SESSION['perfilOperador'] == 'EMPRESA' && retornaValorConfiguracao('VALIDAR_PLANOS_ESP_EMPRESA') == 'SIM'){
		$retorno .= " AND VW_NOMES_PLANOS.CODIGO_EMPRESA =  " . aspas($_SESSION['codigoIdentificacao']);			
	}

	if($tabela == 'VW_TABELAS_PRECO' && $_SESSION['perfilOperador'] == 'EMPRESA' && retornaValorConfiguracao('VALIDAR_PLANOS_ESP_EMPRESA') == 'SIM'){
		$retorno .= " AND VW_TABELAS_PRECO.CODIGO_EMPRESA =  " . aspas($_SESSION['codigoIdentificacao']);			
	}

	if($tabela == 'VW_PS1000_OPERADOR_AL2'){
		if($_SESSION['perfilOperador'] != 'OPERADOR')			
			$retorno = ' and 1 <> 1 ';	
	}

	if($tabela =='VW_REEMBOLSO_ALIANCANET2'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_TITULAR ='. aspas($_SESSION['codigoIdentificacao']);		
		elseif($_SESSION['perfilOperador']!='OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}


	if($tabela =='VW_PS5721_CONFIRMACAO'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR_EXECUTANTE ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_PS5722_GRID'){
		if($_SESSION['perfilOperador']=='PRESTADOR'){
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		}else{
			$retorno = ' and 1 <> 1 ';
		}
	}

	if($tabela =='VW_PS5721_GRID'){		
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if(($_SESSION['perfilOperador']=='EMPRESA')or($_SESSION['perfilOperador']=='EMPRESA_CNPJ'))
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	
	if($tabela =='VW_VOUCHERS_AL2' or $tabela == 'VW_DET_VOUCHER_AL2' or $tabela == 'VW_COBERTURA_PLANO_AL2'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador'] != 'OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_ESP0003_AL2'){
		if($_SESSION['perfilOperador']=='PRESTADOR'){
			$retorno = ' and CODIGO_PRESTADOR_VINCULADO ='. aspas($_SESSION['codigoIdentificacao']);
		}else{
			$retorno = ' and 1 <> 1 ';
		}
	}
	
	if($tabela =='VW_MANUAIS_BENEF'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO'){
			$retorno = ' and CODIGO_PLANO IN (SELECT PS1000.CODIGO_PLANO FROM PS1000 WHERE CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']) . ')';
		}else{
			$retorno = ' and 1 <> 1 ';
		}
	}

	if($tabela =='VW_COPARTICIPACAO_AL2'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_TITULAR ='. aspas($_SESSION['codigoIdentificacao']);		
		else
			$retorno = ' and 1 <> 1 ';
	}

	if(($tabela=='VW_RECURSO_CARENCIA_AL2') or ($tabela=='VW_RECURSO_GLOSA_AL2') or ($tabela=='VW_GUIAS_ENVIADAS_AL2')){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);		
		else if($_SESSION['perfilOperador'] != 'OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}

	if($tabela =='VW_DOCUMENTOS_REAJUSTE_AL2'){
		if(($_SESSION['perfilOperador']=='BENEFICIARIO')or($_SESSION['perfilOperador']=='BENEFICIARIO_CPF'))
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);		
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if($tabela=='VW_ESP_PLANO_TRATAMENTO'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);		
		else 
			$retorno = ' and 1 <> 1 ';
	}
	
	if( $tabela =='VW_PS6500_PRORROGACAO_CD_AL2' or $tabela =='VW_SOLICITACAO_PRORROGACAO' or 
		$tabela =='VW_PRORROGACOES_APROVADAS' or $tabela =='VW_PRORROGACOES_NEGADAS'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador'] != 'OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}

	if( $tabela =='ESP_CAD_EMPRESAS_TMP' or $tabela =='VW_LINKS_VENDAS_AL2' ){
		if($_SESSION['perfilOperador']=='VENDEDOR')
			$retorno = ' and CODIGO_VENDEDOR ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador'] != 'OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}	

	if( $tabela =='VW_COPART_ADIANTADA_PJ_AL2'){
		if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador'] != 'OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}

	if( $tabela =='ESP_SOLIC_SINISTRALIDADE'){
		if($_SESSION['perfilOperador']=='EMPRESA')
			$retorno = ' and CODIGO_EMPRESA ='. aspas($_SESSION['codigoIdentificacao']);
		else if($_SESSION['perfilOperador'] != 'OPERADOR')
			$retorno = ' and 1 <> 1 ';
	}

	if( $tabela =='ESP_HIST_CARTOES_ASSOCIADOS'){
		if($_SESSION['perfilOperador']=='BENEFICIARIO')
			$retorno = ' and CODIGO_ASSOCIADO ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	
	if( trim($tabela) == 'VW_PROFISSIONAL_EXEC'){
		if($_SESSION['perfilOperador']=='PRESTADOR')
			$retorno = ' and CODIGO_PRESTADOR ='. aspas($_SESSION['codigoIdentificacao']);
		else
			$retorno = ' and 1 <> 1 ';
	}
	return $retorno;

}
?>