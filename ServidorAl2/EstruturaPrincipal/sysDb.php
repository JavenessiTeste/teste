<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../ProcessoDinamico/geradorRelatorios.php');

$dadosInput['par'] = str_replace("\\'", "'",$dadosInput['par']);

if($dadosInput['tipo'] =='SysDB_A')
{
	if($_SESSION['SYSDB']===false){
		header("HTTP/1.0 403 Forbidden");
		exit;		
	}
	$dadosGrid = array();
	$infoGrid = array();
	
	$queryTabela = utf8_decode($dadosInput['par']);
		
	$resTabela = jn_query($queryTabela);
	$i = 0;
	while($rowTabela = jn_fetch_object($resTabela)){
		
		
		foreach ($rowTabela as $key => $value){
			if($i==0){
				$item['NOME_CAMPO'] = $key;
				$item['LABEL'] = $key;
				$item['TIPO_CAMPO'] = "";
				$item['GRID'] = 'S';
				$infoGrid[] = $item;
			}
			//$dadoLinha[$key] = jn_utf8_encode($value);

			if ($value instanceof DateTime) {
				$dadoLinha[$key] = jn_utf8_encode($value->format('Y-m-d'));
			}elseif(is_object($value)){						
				$dadoLinha[$key] = jn_utf8_encode($value->format('Y-m-d'));
			}else{
				$dadoLinha[$key] = jn_utf8_encode($value);
			}



		}
		$dadosGrid[] = $dadoLinha;
		$i++;
	}

	$retorno['DADOS_GRID']  = $dadosGrid;	
	$retorno['INFO_GRID']  = $infoGrid;
		
						   	
}
else if($dadosInput['tipo'] =='SysDB_E')
{
	if($_SESSION['SYSDB']===false){
		header("HTTP/1.0 403 Forbidden");
		exit;		
	}
	$queryTabela = utf8_decode($dadosInput['par']);
		
	$resTabela = jn_query($queryTabela);
	$retorno['LINHAS']  = 0;
	if($resTabela){
		$retorno['LINHAS'] = jn_affected_rows($resTabela);
	}
	
	
}
else if($dadosInput['tipo'] =='SysDB_CE')
{
	if($_SESSION['SYSDB']===false){
		header("HTTP/1.0 403 Forbidden");
		exit;		
	}
	
	$queryTabela = "select * from LOG_OPERACOES_ERRO where  NUMERO_REGISTRO = ".aspas($dadosInput['par']);
	$resTabela = jn_query($queryTabela);
	
	$retorno['MSG'] = '';
	if($rowTabela = jn_fetch_object($resTabela)){
		$retorno['MSG'] = jn_utf8_encode($rowTabela->ERRO). ' Select -> '. jn_utf8_encode($rowTabela->INSTRUCAO_SQL); 
	}
	
}
else if($dadosInput['tipo'] =='SysDB_VS')
{
	if($_SESSION['SYSDB']=== true){
		$retorno['VS'] = true;
	}else{
		$retorno['VS'] = false;
	}
}
else if($dadosInput['tipo'] =='SysDB_AU')
{

	//SENHA PROVISORIA @Jave.2023.SQL@

	if (strtoupper($dadosInput['par'])==strtoupper('@Jave.2023.SQL@'))
	{
		$_SESSION['SYSDB'] = true;
		$retorno['AU'] = true;
	}
	else
	{
		$retorno['AU'] = false;
		$_SESSION['SYSDB'] = false;
		header("HTTP/1.0 403 Forbidden");
		exit;
	}
	
}






if($dadosInput['tipo'] =='tabelasRelacionadas')
{

	if ($dadosInput['apenasATabelaInformada'] =='S')
	{
		$queryTabela = "select Nome_Tabela Tabela_Relacionada, Descricao_Tabela From CfgTabelas_sis 
		                Where Nome_Tabela = ".aspas($dadosInput['nomeTabela']) ;
	}
	else
	{
		$queryTabela = "select Distinct Tabela_Relacionada, Descricao_Tabela From CfgRelatorioCustomizado 
		                inner join CfgTabelas_sis on (CfgRelatorioCustomizado.Tabela_Relacionada = CfgTabelas_sis.nome_Tabela)
		                Where Tabela_Principal = ".aspas($dadosInput['nomeTabela']) . 
		              " or CfgTabelas_sis.nome_Tabela = " . aspas($dadosInput['nomeTabela']);
	}

	$resTabela   = jn_query($queryTabela);
	
	$valoresRetorno = Array();
	$i = 0;

	while($rowTabela = jn_fetch_object($resTabela))
	{
		$valoresRetorno[$i]['NOME_TABELA'] = jn_utf8_encode($rowTabela->TABELA_RELACIONADA);
		$valoresRetorno[$i]['DESCRICAO_TABELA'] = jn_utf8_encode(ucfirst(strtolower($rowTabela->DESCRICAO_TABELA)));
		$i++;
	}

	foreach ($valoresRetorno as $value)
	{
		$retorno[]=$value;
	} 

}
else if($dadosInput['tipo'] =='retornaCamposTabela')
{

	$camposSelecionar = selecionaCamposConformeTabela($dadosInput['nomeTabela']);

	$queryTabela = "select nome_campo, descricao_campo, tipo_campo from CfgCampos_sis Where nome_tabela = ".aspas($dadosInput['nomeTabela']) . 
	               " ORDER BY numero_ordem_criacao, nome_campo";
	$resTabela   = jn_query($queryTabela);
	
	$valoresRetorno = Array();
	$i = 0;

	while($rowTabela = jn_fetch_object($resTabela))
	{

		if ($camposSelecionar!='')
		{
			if (strpos($camposSelecionar,$rowTabela->NOME_CAMPO)==false)
				continue;
		}

		if ($dadosInput['apenasATabelaInformada'] =='S')
		{
			$valoresRetorno[$i]['NOME_CAMPO']      = jn_utf8_encode($rowTabela->NOME_CAMPO);
		}
		else
		{
			$valoresRetorno[$i]['NOME_CAMPO']      = jn_utf8_encode($dadosInput['nomeTabela'] . '.' . $rowTabela->NOME_CAMPO);
		}

		$valoresRetorno[$i]['DESCRICAO_CAMPO'] = jn_utf8_encode( $rowTabela->DESCRICAO_CAMPO);
		$valoresRetorno[$i]['TIPO_CAMPO']      = jn_utf8_encode( $rowTabela->TIPO_CAMPO);
		$valoresRetorno[$i]['MARCADO']         = 'N';
		$i++;
	}

	foreach ($valoresRetorno as $value)
	{
		$retorno[]=$value;
	} 

}
else if($dadosInput['tipo'] =='pesquisarRegistrosTabela')
{

	$campoInativado = '';
	$criterioAtivo  = '';

	if ((strtoupper($dadosInput['nomeTabela'])=='PS1010') OR 
	    (strtoupper($dadosInput['nomeTabela'])=='PS1000'))
	{
		$campoInativado = 'DATA_EXCLUSAO';

		if ($dadosInput['listarExcluidos']=='N')
		    $criterioAtivo  = ' and ' . $campoInativado . ' is null';
	}

	if ($dadosInput['camposSelect']=='')
	{
		$dadosInput['camposSelect'] = selecionaCamposConformeTabela($dadosInput['nomeTabela']);

		if ($dadosInput['camposSelect']=='')
		{
			$dadosInput['camposSelect'] = ' * ';
		}
	}

	$complementoSql = '';

	if($dadosInput['filtros']['ID_PROCESSO'] != '' and count($dadosInput['filtros']['CAMPOS'])>0)
	{
		require('../EstruturaEspecifica/complementoSqlPD.php');
		$complementoSql = CompSqlPD($dadosInput['nomeTabela'] ,$dadosInput['filtros']['ID_PROCESSO'],$dadosInput['filtros']['CAMPOS']);
	}

	$queryTabela = "select first 50 " . $dadosInput['camposSelect'] . iif($campoInativado!='','',',' . $campoInativado) . 
	               " from " . $dadosInput['nomeTabela'];

	if ($dadosInput['pesquisarValoresExatos']=='S')
	{
	    $queryTabela .= " Where " . $dadosInput['CampoPesquisar'] . " = " . aspas(strtoupper($dadosInput['valorPesquisar'])) . $criterioAtivo . $complementoSql;
	}
	else
	{
	    $queryTabela .= " Where " . $dadosInput['CampoPesquisar'] . " Like " . aspas('%' . strtoupper($dadosInput['valorPesquisar']) . '%') . $criterioAtivo . $complementoSql;
	}



	$resTabela   = jn_query($queryTabela);

	$chavePrimaria  = '';
	$valoresRetorno = Array();
	$i              = 0;
	$j              = 0;

	while($rowTabela = jn_fetch_object($resTabela))
	{
	
		$j = 0;

		//$valoresRetorno["DADOS"][] = $rowTabela;
		$itens = array();

		$itens['REGISTRO_ATIVO'] = 'S';

		while ($j < jn_num_fields($resTabela))
		{

			$nomeCampo                         = jn_field_metadata($resTabela,$j)['Name'];

			if ($i==0)
			{
			   $itemNome 						  = array();		

			   if (($chavePrimaria=='')&&
			   	   (((substr(strtoupper($nomeCampo),0,7))=='CODIGO_')||(strtoupper($nomeCampo)=='NUMERO_REGISTRO')))
			   {
			   		$chavePrimaria = $nomeCampo;
			   }

			   $itemNome["NOME_CAMPO"]			  = $nomeCampo;
			   $itemNome["LABEL_CAMPO"]			  = mascaraLabelCampo($nomeCampo);
			   $valoresRetorno["CAMPOS"][]        = $itemNome;
			}

			//$itens['REGISTRO_ATIVO'] = 'S';

			if (copyDelphi(strToUpper($nomeCampo),1,5)=='DATA_')
			{
				$itens[$nomeCampo] = sqlToData($rowTabela->$nomeCampo);

  			    if (strToUpper($nomeCampo)==strToUpper($campoInativado))
  			    {
  			    	if ($rowTabela->$nomeCampo!='')
  			    	{
						$itens['REGISTRO_ATIVO'] = 'N';  			    		
  			    	}
  			    }
			}
			else
			{
				$itens[$nomeCampo] = jn_utf8_encode($rowTabela->$nomeCampo);
			}



			if (strtoupper($nomeCampo)==(strtoupper($chavePrimaria)))
			{
			   $itens['CHAVE_PRIMARIA'] = jn_utf8_encode($rowTabela->$nomeCampo);
			}

			$j++;
		}

		if ($itens['CHAVE_PRIMARIA']=='') 
		{
		   $itens['CHAVE_PRIMARIA'] = jn_utf8_encode($rowTabela->$nomeCampo);
		}

		$itens['CHAVE_PRIMARIA']   = aspas($itens['CHAVE_PRIMARIA']);
		$valoresRetorno["DADOS"][] = $itens;

		$i++;
	}


	// Aqui caso não tenha encontrado registros, eu monto ao menos as colunas.
	if ($i==0)
	{
		while ($j < jn_num_fields($resTabela))
		{
			$nomeCampo                        = jn_field_info($resTabela,$j); //['Name'];
		    $itemNome 						  = array();		

			if (($chavePrimaria=='')&&
			   (((substr(strtoupper($nomeCampo),0,7))=='CODIGO_')||(strtoupper($nomeCampo)=='NUMERO_REGISTRO')))
			{
				$chavePrimaria = $nomeCampo;
			}

			$itemNome["NOME_CAMPO"]			  = $nomeCampo;
			$itemNome["LABEL_CAMPO"]		  = mascaraLabelCampo($nomeCampo);
			$valoresRetorno["CAMPOS"][]       = $itemNome;
		
 			$itens['CHAVE_PRIMARIA']          = '-1';
			$j++;
		}

		$valoresRetorno["DADOS"][] = $itens;

	}

	$retorno = $valoresRetorno;

}
/*else if($dadosInput['tipo'] =='retornaCamposCadastroDinamico')
{

	$camposSelecionar = ' select cfgcampos_sis_cd.numero_registro, cfgcampos_sis_cd.nome_tabela, cfgcampos_sis_cd.nome_campo, cfgcampos_sis_cd.label_campo, 
	                      cfgcampos_sis_cd.comportamento_frm_edicao, cfgcampos_sis_cd.flag_notnull, cfgcampos_sis_cd.componente_formulario, 
	                      cfgcampos_sis_cd.numero_ordem_criacao, cfgcampos_sis_cd.nome_tabela_relacionada, cfgcampos_sis_cd.campo_id_tabela_relac, 
	                      cfgcampos_sis_cd.campo_pesquisa_tabela_relac, cfgcampos_sis_cd.tipo_mascara, cfgcampos_sis_cd.hint_explicacao, 
	                      cfgcampos_sis_cd.quantidade_anexos, cfgcampos_sis_cd.tipo_chave_automatica, cfgcampos_sis_cd.opcoes_combo, cfgcampos_sis_cd.valor_padrao, 
	                      cfgcampos_sis_cd.pasta_apresentacao, cfgcampos_sis_cd.flag_exibir_grid, cfgcampos_sis_cd.tipo_campo, cfgcampos_sis_cd.tamanho_campo, 
	                      cfgcampos_sis_cd.flag_chaveprimaria, cfgcampos_sis_cd.flag_chaveestrangeira, cfgcampos_sis_cd.classe_campo, cfgcampos_sis_cd.link_add_auto, 
	                      cfgcampos_sis_cd.campos_pesquisa_auto, cfgcampos_sis_cd.saida_campo, cfgcampos_sis_cd.campos_pesquisa_aux_auto, 
	                      cfgcampos_sis_cd.id_instancia_processo
	                      from cfgcampos_sis_cd
	                      where nome_tabela = ' . aspas($dadosInput['nomeTabela']) . ' order by cfgcampos_sis_cd.pasta_apresentacao, cfgcampos_sis_cd.numero_ordem_criacao';

	$resTabela      = jn_query($camposSelecionar);
	
	$valoresRetorno = Array();
	$i = 0;

	while($rowTabela = jn_fetch_object($resTabela))
	{

		$valoresRetorno[$i]['NUMERO_REGISTRO']             = $rowTabela->NUMERO_REGISTRO;
		$valoresRetorno[$i]['NOME_TABELA']                 = $rowTabela->NOME_TABELA;
		$valoresRetorno[$i]['NOME_CAMPO']                  = $rowTabela->NOME_CAMPO;
		$valoresRetorno[$i]['LABEL_CAMPO']     			   = jn_utf8_encode($rowTabela->LABEL_CAMPO);
		$valoresRetorno[$i]['COMPORTAMENTO_FRM_EDICAO']    = $rowTabela->COMPORTAMENTO_FRM_EDICAO;
		$valoresRetorno[$i]['FLAG_NOTNULL']                = $rowTabela->FLAG_NOTNULL;
		$valoresRetorno[$i]['COMPONENTE_FORMULARIO']       = $rowTabela->COMPONENTE_FORMULARIO;
		$valoresRetorno[$i]['NUMERO_ORDEM_CRIACAO']        = $rowTabela->NUMERO_ORDEM_CRIACAO;
		$valoresRetorno[$i]['NOME_TABELA_RELACIONADA']     = $rowTabela->NOME_TABELA_RELACIONADA;
		$valoresRetorno[$i]['CAMPO_ID_TABELA_RELAC']       = $rowTabela->CAMPO_ID_TABELA_RELAC;
		$valoresRetorno[$i]['CAMPO_PESQUISA_TABELA_RELAC'] = $rowTabela->CAMPO_PESQUISA_TABELA_RELAC;
		$valoresRetorno[$i]['TIPO_MASCARA']                = jn_utf8_encode($rowTabela->TIPO_MASCARA);
		$valoresRetorno[$i]['HINT_EXPLICACAO']             = jn_utf8_encode($rowTabela->HINT_EXPLICACAO);
		$valoresRetorno[$i]['QUANTIDADE_ANEXOS']           = $rowTabela->QUANTIDADE_ANEXOS;
		$valoresRetorno[$i]['TIPO_CHAVE_AUTOMATICA']       = $rowTabela->TIPO_CHAVE_AUTOMATICA;
		$valoresRetorno[$i]['OPCOES_COMBO']                = jn_utf8_encode($rowTabela->OPCOES_COMBO);
		$valoresRetorno[$i]['VALOR_PADRAO']                = jn_utf8_encode($rowTabela->VALOR_PADRAO);
		$valoresRetorno[$i]['PASTA_APRESENTACAO']          = jn_utf8_encode($rowTabela->PASTA_APRESENTACAO);
		$valoresRetorno[$i]['FLAG_EXIBIR_GRID']            = $rowTabela->FLAG_EXIBIR_GRID;
		$valoresRetorno[$i]['TIPO_CAMPO']                  = $rowTabela->TIPO_CAMPO;
		$valoresRetorno[$i]['TAMANHO_CAMPO']               = $rowTabela->TAMANHO_CAMPO;
		$valoresRetorno[$i]['FLAG_CHAVEESTRANGEIRA']       = $rowTabela->FLAG_CHAVEESTRANGEIRA;
		$valoresRetorno[$i]['CLASSE_CAMPO']                = $rowTabela->CLASSE_CAMPO;
		$valoresRetorno[$i]['CAMPOS_PESQUISA_AUTO']        = jn_utf8_encode($rowTabela->CAMPOS_PESQUISA_AUTO);
		$valoresRetorno[$i]['SAIDA_CAMPO']                 = jn_utf8_encode($rowTabela->SAIDA_CAMPO);
		$valoresRetorno[$i]['CAMPOS_PESQUISA_AUX_AUTO']    = jn_utf8_encode($rowTabela->CAMPOS_PESQUISA_AUX_AUTO);

		$i++;
	}

	foreach ($valoresRetorno as $value)
	{
		$retorno[]=$value;
	} 

}*/
else if($dadosInput['tipo'] =='retornaCamposCadastroProcessoDinamico')
{

	$valoresRetorno = Array();

	if (permissaoPx('CFGCAMPOS_SIS_CD','4',false))
	{
		$valoresRetorno['MSG'] = 'OK';
	}
	else
	{
		$valoresRetorno['MSG'] = 'SEM_PERMISSAO';
	}



	if ($dadosInput['nomeTabela']!='')
	{
		$camposSelecionar = 'SELECT CFGCAMPOS_SIS_CD.NUMERO_REGISTRO, CFGCAMPOS_SIS_CD.PASTA_APRESENTACAO, CFGCAMPOS_SIS_CD.LABEL_CAMPO, 
							 CFGCAMPOS_SIS_CD.COMPORTAMENTO_FRM_EDICAO, CFGCAMPOS_SIS_CD.COMPONENTE_FORMULARIO,  
							 CFGCAMPOS_SIS_CD.NUMERO_ORDEM_CRIACAO, CFGCAMPOS_SIS_CD.FLAG_NOTNULL, 
                             CFGCAMPOS_SIS_CD.NOME_TABELA_RELACIONADA, CFGCAMPOS_SIS_CD.CAMPO_ID_TABELA_RELAC, 
	                         CFGCAMPOS_SIS_CD.CAMPO_PESQUISA_TABELA_RELAC, CFGCAMPOS_SIS_CD.TIPO_MASCARA, CFGCAMPOS_SIS_CD.HINT_EXPLICACAO, 
	                         CFGCAMPOS_SIS_CD.OPCOES_COMBO, CFGCAMPOS_SIS_CD.VALOR_PADRAO, 
	                         CFGCAMPOS_SIS_CD.FLAG_EXIBIR_GRID,  CFGCAMPOS_SIS_CD.CLASSE_CAMPO, 
                        	 CFGCAMPOS_SIS_CD.NOME_TABELA, CFGCAMPOS_SIS_CD.NOME_CAMPO
	                         FROM CFGCAMPOS_SIS_CD
	                         where nome_tabela = ' . aspas($dadosInput['nomeTabela']) . 
	                         ' order by cfgcampos_sis_cd.pasta_apresentacao, cfgcampos_sis_cd.numero_ordem_criacao';
	}
	else
	{
		$camposSelecionar = 'SELECT NUMERO_REGISTRO, PASTA_APRESENTACAO, LABEL_CAMPO, COMPORTAMENTO_FRM_EDICAO, NUMERO_ORDEM_CRIACAO,
		                             case
		                             when FLAG_NOTNULL = "S" then "SIM"
		                             ELSE "NAO"
		                             END FLAG_NOTNULL, COMPONENTE_FORMULARIO,  NOME_TABELA_RELACIONADA, 
		                             CAMPO_ID_TABELA_RELAC, CAMPO_PESQUISA_TABELA_RELAC, TIPO_MASCARA, HINT_EXPLICACAO, 
		                             OPCOES_COMBO, VALOR_PADRAO,  
		                             CLASSE_CAMPO, LINK_ADD_AUTO, COMPLEMENTO_SQL, ENTRADA_CAMPO, SAIDA_CAMPO, 
		                             NUMERO_REGISTRO_PROCESSO, NOME_CAMPO  
		                             FROM CFGCAMPOS_PD
		                      where NUMERO_REGISTRO_PROCESSO = ' . aspas($dadosInput['numeroRegistroProcesso']) . 
		                      ' order by pasta_apresentacao, numero_ordem_criacao';
	}	                     

	$resTabela      = jn_query($camposSelecionar);
	
	$itens          = array();
	$registros      = array();

	$i = 0;
	$j = 0;

	while ($j < jn_num_fields($resTabela))
	{

		$itens                        = array();
		$nomeCampo                    = jn_field_metadata($resTabela,$j)['Name'];
		$labelCampo					  = $nomeCampo;

		if ($nomeCampo=='OPCOES_COMBO')
		{
			$itens['LABEL_CAMPO']         = 'Opcoes combo';
			$itens['LABEL_EXPLICATIVA']   = ' (separe os elementos por ponto e vírgula)';
		}
		else if ($nomeCampo=='COMPORTAMENTO_FRM_EDICAO')
		{
			$itens['LABEL_CAMPO']         = 'Comportamento form';
			$itens['LABEL_EXPLICATIVA']   = ' (1-normal, 2-inabilitado, 3-invisível)';
		}
		else if ($nomeCampo=='CAMPO_ID_TABELA_RELAC')
		{	
			$itens['LABEL_CAMPO']         = 'Campo relacionado';
			$itens['LABEL_EXPLICATIVA']   = ' (Campo chave da tabela relacionada)';
		}
		else if ($nomeCampo=='CAMPO_PESQUISA_TABELA_RELAC')
		{
			$itens['LABEL_CAMPO']         = 'Campo pesquisa';
			$itens['LABEL_EXPLICATIVA']   = ' (Campo pesquisa da tabela relacionada)';
		}
		else if ($nomeCampo=='FLAG_NOTNULL')
		{
			$itens['LABEL_CAMPO']         = 'Obrigatório?';
			$itens['LABEL_EXPLICATIVA']   = ' (Campo de preenchimento obrigatório?)';
		}
		else
			$itens['LABEL_CAMPO']         = mascaraLabelCampo($labelCampo);

		$itens['NOME_CAMPO']          = $nomeCampo;

		if ($nomeCampo=='COMPORTAMENTO_FRM_EDICAO')
		{
		    $itens['TIPO_EDICAO'] = 'COMBOBOX';
		    $itens['OPCOES'][]    = jn_utf8_encode('1');
		    $itens['OPCOES'][]    = jn_utf8_encode('2');
		    $itens['OPCOES'][]    = jn_utf8_encode('3');
 		}
		else if ($nomeCampo=='PASTA_APRESENTACAO')
		{
		    $itens['TIPO_EDICAO'] = 'COMBOBOX';

	    	if ($dadosInput['nomeTabela']!='')
	    	{
			    $itens['OPCOES'][]    = '1-Dados principais';
			    $itens['OPCOES'][]    = '2-Informações auxiliares';
			    $itens['OPCOES'][]    = '3-Outras informações(1)';
			    $itens['OPCOES'][]    = '4-Outras informações(2)';
			    $itens['OPCOES'][]    = '5-Outras informações(3)';
			    $itens['OPCOES'][]    = '6-Campos específicos';
			    $itens['OPCOES'][]    = '7-Informações gerais';

				$sqlPastas = 'SELECT distinct PASTA_APRESENTACAO FROM CFGCAMPOS_SIS_CD
				                      where NOME_TABELA = ' . aspas($dadosInput['nomeTabela']) . 
				                      'order by pasta_apresentacao';

				$resPastas = jn_query($sqlPastas);

				while($rowPastas = jn_fetch_object($resPastas))
				{
				    $itens['OPCOES'][]    = jn_utf8_encode($rowPastas->PASTA_APRESENTACAO);
				}
			}
			else
			{
			    $itens['OPCOES'][]    = '1-Opcoes e parametros do processo';
			    $itens['OPCOES'][]    = '2-Filtros principais';
			    $itens['OPCOES'][]    = '3-Filtros secundarios';
			    $itens['OPCOES'][]    = '4-Outros filtros';
			    $itens['OPCOES'][]    = '5-Parametros principais';
			    $itens['OPCOES'][]    = '6-Parametros secundarios';
			    $itens['OPCOES'][]    = '7-Outros parametros';
			    $itens['OPCOES'][]    = '8-Opcoes e parametros adicionais';

				$sqlPastas = 'SELECT distinct PASTA_APRESENTACAO FROM CFGCAMPOS_PD
				                      where NUMERO_REGISTRO_PROCESSO = ' . aspas($dadosInput['numeroRegistroProcesso']) . 
				                      ' order by pasta_apresentacao';

				$resPastas = jn_query($sqlPastas);

				while($rowPastas = jn_fetch_object($resPastas))
				{
				    $itens['OPCOES'][]    = jn_utf8_encode($rowPastas->PASTA_APRESENTACAO);
				}
			}

 		}
		else if (($nomeCampo=='FLAG_NOTNULL')||($nomeCampo=='FLAG_EXIBIR_GRID'))
		{
		    $itens['TIPO_EDICAO'] = 'COMBOBOX';
		    $itens['OPCOES'][]    = jn_utf8_encode('SIM');
		    $itens['OPCOES'][]    = jn_utf8_encode('NAO');
 		}
		else if (($nomeCampo=='NOME_TABELA_RELACIONADA') ||
				 ($nomeCampo=='CAMPO_ID_TABELA_RELAC') ||
		 		($nomeCampo=='CAMPO_PESQUISA_TABELA_RELAC'))
		{
		    $itens['TIPO_EDICAO'] = 'AUTOCOMPLETE';
 			$itens['OPCOES']	  = '';	
		}
		else if ($nomeCampo=='COMPONENTE_FORMULARIO')
		{
		    $itens['TIPO_EDICAO'] = 'COMBOBOX';
		    $itens['OPCOES'][]    = jn_utf8_encode('DATE');
		    $itens['OPCOES'][]    = jn_utf8_encode('AUTOCOMPLETE');
		    $itens['OPCOES'][]    = jn_utf8_encode('FILTROMANUAL');
		    $itens['OPCOES'][]    = jn_utf8_encode('CHECKBOX');
		    $itens['OPCOES'][]    = jn_utf8_encode('TEXT');
		    $itens['OPCOES'][]    = jn_utf8_encode('TEXTAREA');
		    $itens['OPCOES'][]    = jn_utf8_encode('COMBOBOX');
		    $itens['OPCOES'][]    = jn_utf8_encode('DIVISORIA');
		    $itens['OPCOES'][]    = jn_utf8_encode('COMBOBOX');
		    $itens['OPCOES'][]    = jn_utf8_encode('EDITOR');
		    $itens['OPCOES'][]    = jn_utf8_encode('GROUPCHECKBOX');
		    $itens['OPCOES'][]    = jn_utf8_encode('RADIO');
		    $itens['OPCOES'][]    = jn_utf8_encode('ANEXO');
 		}
 		else
 		{
	    	$itens['TIPO_EDICAO'] = 'EDIT';
 			$itens['OPCOES']	  = '';	
  		}

		if (($nomeCampo=='NUMERO_REGISTRO')	||
			($nomeCampo=='NUMERO_REGISTRO_PROCESSO')||
			($nomeCampo=='NOME_CAMPO')||
			($nomeCampo=='LINK_ADD_AUTO')||
			($nomeCampo=='SAIDA_CAMPO')||
			($nomeCampo=='LINK_ADD_AUTO')||
			($nomeCampo=='COMPLEMENTO_SQL')||
			($nomeCampo=='NUMERO_ORDEM_CRIACAO')||
			($nomeCampo=='ENTRADA_CAMPO'))
			$valoresRetorno["CAMPOS"][]      = $itens;
		else
		{
			$valoresRetorno["CAMPOS"][]      = $itens;
			$valoresRetorno["CAMPOSEDIT"][]  = $itens;
		}

		$j++;
	}

	$itens['LABEL_CAMPO']         = 'Indice Ordem';
	$itens['NOME_CAMPO']          = 'INDICE';
	$valoresRetorno["CAMPOS"][]      = $itens;
	$valoresRetorno["CAMPOSEDIT"][]  = $itens;

	$i = 0;

	while($rowTabela = jn_fetch_object($resTabela))
	{

		$j = 0;

		while ($j < jn_num_fields($resTabela))
		{
			$nomeCampo                    = jn_field_metadata($resTabela,$j)['Name'];
			$registros[$nomeCampo]        = jn_utf8_encode($rowTabela->$nomeCampo);
			$j++;
		}

		$registros['INDICE']       = $i;
		$valoresRetorno["DADOS"][] = $registros;

		$i++;

	}

	$retorno = $valoresRetorno;

}
else if($dadosInput['tipo'] =='salvarCamposConfiguracao')
{

	$msgsValidacao = '';

	foreach ($dadosInput['arrayCamposDados'] as $chave => $valor) 
	{

		if (($dadosInput['arrayCamposDados'][$chave]['NOME_TABELA_RELACIONADA']!='') and 
		    (($dadosInput['arrayCamposDados'][$chave]['CAMPO_ID_TABELA_RELAC']=='') or
		    ($dadosInput['arrayCamposDados'][$chave]['CAMPO_PESQUISA_TABELA_RELAC']==''))) 
		    $msgValidacao .= 'No campo: ' . jn_utf8_encode($dadosInput['arrayCamposDados'][$chave]['NOME_CAMPO']) . ' voce selecionou a tabela relacionada, mas nao informou os campos chave e pesquisa.<br> ';

		if (($dadosInput['arrayCamposDados'][$chave]['COMPONENTE_FORMULARIO']=='AUTOCOMPLETE') and
			(($dadosInput['arrayCamposDados'][$chave]['NOME_TABELA_RELACIONADA']=='') or
		     ($dadosInput['arrayCamposDados'][$chave]['CAMPO_ID_TABELA_RELAC']=='') or
		     ($dadosInput['arrayCamposDados'][$chave]['CAMPO_PESQUISA_TABELA_RELAC']=='')))
		     $msgValidacao .= 'No campo: ' . jn_utf8_encode($dadosInput['arrayCamposDados'][$chave]['NOME_CAMPO']) . ' voce selecionou que seria um componente "AUTOCOMPLETE" neste caso e obrigado informar a tabela relacionada e os campos chave e pesquisa.<br> ';

		if (($dadosInput['arrayCamposDados'][$chave]['COMPONENTE_FORMULARIO']=='COMBOBOX') and
			($dadosInput['arrayCamposDados'][$chave]['OPCOES_COMBO']=='')) 
		     $msgValidacao .= 'No campo: ' . jn_utf8_encode($dadosInput['arrayCamposDados'][$chave]['NOME_CAMPO']) . ' voce selecionou que seria um componente "COMBOBOX" neste caso e obrigado informar os elementos da combo (separados por ponto e virgula)<br> ';

		if (($dadosInput['arrayCamposDados'][$chave]['COMPONENTE_FORMULARIO']=='AUTOCOMPLETE') and
			($dadosInput['arrayCamposDados'][$chave]['NOME_TABELA_RELACIONADA']!='') and
		    ($dadosInput['arrayCamposDados'][$chave]['CAMPO_ID_TABELA_RELAC']!='') and
		    ($dadosInput['arrayCamposDados'][$chave]['CAMPO_PESQUISA_TABELA_RELAC']!=''))
		{
			$rowTemp = qryUmRegistro('select NUMERO_REGISTRO from cfgcampos_sis where nome_tabela = ' . aspas($dadosInput['arrayCamposDados'][$chave]['NOME_TABELA_RELACIONADA']) . 
				                     'and nome_campo = ' . aspas($dadosInput['arrayCamposDados'][$chave]['CAMPO_ID_TABELA_RELAC']));

			if ($rowTemp->NUMERO_REGISTRO=='')
  		       $msgValidacao .= 'No campo: ' . jn_utf8_encode($dadosInput['arrayCamposDados'][$chave]['NOME_CAMPO']) . ' o campo selecionado para o autocomplete ' . $dadosInput['arrayCamposDados'][$chave]['CAMPO_ID_TABELA_RELAC'] . ' nao pertence a tabela relacionada: ' . $dadosInput['arrayCamposDados'][$chave]['NOME_TABELA_RELACIONADA'] . '<br>';

			$rowTemp = qryUmRegistro('select NUMERO_REGISTRO from cfgcampos_sis where nome_tabela = ' . aspas($dadosInput['arrayCamposDados'][$chave]['NOME_TABELA_RELACIONADA']) . 
				                     'and nome_campo = ' . aspas($dadosInput['arrayCamposDados'][$chave]['CAMPO_PESQUISA_TABELA_RELAC']));

			if ($rowTemp->NUMERO_REGISTRO=='')
  		       $msgValidacao .= 'No campo: ' . jn_utf8_encode($dadosInput['arrayCamposDados'][$chave]['NOME_CAMPO']) . ' o campo selecionado para o autocomplete ' . $dadosInput['arrayCamposDados'][$chave]['CAMPO_PESQUISA_TABELA_RELAC'] . ' nao pertence a tabela relacionada: ' . $dadosInput['arrayCamposDados'][$chave]['NOME_TABELA_RELACIONADA'] . '<br>';
		}

	}

	if ($msgValidacao!='')
	{
		$retorno['MSG'] = $msgValidacao;
	}
	else
	{
		foreach ($dadosInput['arrayCamposDados'] as $chave => $valor) 
		{

			$sqlEdicao  = '';
			$tipoEdicao = 'A';

			foreach ($valor as $chave1 => $valor1) 
			{

				if (($chave1=='NUMERO_REGISTRO') && ($valor1=='-1'))
				{
					$tipoEdicao = 'I';
				}

				if ($chave1=='FLAG_NOTNULL') 
				{
					$valor1 = copyDelphi($valor1,1,1);
				}

				if ($chave1=='INDICE') // O indice servirá para ajustar a ordem da criação dos campos 
				{
					$sqlEdicao 	.= linhaJsonEdicao('NUMERO_ORDEM_CRIACAO', $valor1);
				}
				else if (($chave1!='NUMERO_REGISTRO') and 
						 ($chave1!='NUMERO_REGISTRO_PROCESSO') and 
						 ($chave1!='NOME_CAMPO') and 
						 ($chave1!='NUMERO_ORDEM_CRIACAO') and 
						 ($chave1!='INDICE'))
				{
					$sqlEdicao 	.= linhaJsonEdicao($chave1, retiraAcentos($valor1),'IGVAZIO');
				}
				else if ((($chave1=='NUMERO_REGISTRO_PROCESSO') or ($chave1=='NOME_CAMPO'))  and 
					     ($tipoEdicao== 'I'))
				{
					$sqlEdicao 	.= linhaJsonEdicao($chave1, retiraAcentos($valor1),'IGVAZIO');
				}

				if ($chave1=='NUMERO_REGISTRO')
					$criterioWhere = ' Numero_registro = ' . aspas($valor1);

			}

			if ($dadosInput['nomeTabela']!='')
				gravaEdicao('CFGCAMPOS_SIS_CD', $sqlEdicao, $tipoEdicao, $criterioWhere);
			else
				gravaEdicao('CFGCAMPOS_PD', $sqlEdicao, $tipoEdicao, $criterioWhere);

		}

		$retorno['MSG'] = 'OK';

	}



}
else if($dadosInput['tipo'] =='relatoriosSalvos')
{

	$queryTabela = "Select Distinct NOME_RELATORIO From CfgRelatoriosSalvos
	                where nome_tabela_coluna like " . aspas($dadosInput['tabelaPrincipal'] . '%') . "
	                ORDER BY nome_relatorio";
	$resTabela   = jn_query($queryTabela);
	
	$valoresRetorno = Array();
	$i = 0;

	while($rowTabela = jn_fetch_object($resTabela))
	{
		$valoresRetorno[$i]['NOME_RELATORIO']  = jn_utf8_encode($rowTabela->NOME_RELATORIO);
		$i++;
	}

	$retorno=$valoresRetorno;
	
}
else if($dadosInput['tipo'] =='camposRelatoriosSalvos')
{


	$queryTabela = "Select CfgRelatoriosSalvos.*, CfgCampos_sis.nome_campo, CfgCampos_sis.descricao_campo, 
	                CfgCampos_sis.tipo_campo  
	                From CfgRelatoriosSalvos
	                inner join CfgCampos_sis on (CfgRelatoriosSalvos.NOME_TABELA_COLUNA = CFGCAMPOS_SIS.NOME_TABELA || '.' || CFGCAMPOS_SIS.NOME_CAMPO)
	                Where nome_relatorio = " . aspas($dadosInput['nomeRelatorio']) . " order by ORDEM_COLUNA";
	$resTabela   = jn_query($queryTabela);
	
	$valoresRetorno = Array();
	$i = 0;

	while($rowTabela = jn_fetch_object($resTabela))
	{
		$valoresRetorno[$i]['NOME_RELATORIO']            = jn_utf8_encode($rowTabela->NOME_RELATORIO);
		$valoresRetorno[$i]['NOME_TABELA_COLUNA']        = jn_utf8_encode($rowTabela->NOME_TABELA_COLUNA);
		$valoresRetorno[$i]['ORDEM_COLUNA']              = jn_utf8_encode($rowTabela->ORDEM_COLUNA);
		$valoresRetorno[$i]['FLAG_COLUNA_ORDENADA']      = jn_utf8_encode($rowTabela->FLAG_COLUNA_ORDENADA);
		$valoresRetorno[$i]['TIPO_RETRATO_PAISAGEM']     = jn_utf8_encode($rowTabela->TIPO_RETRATO_PAISAGEM);
		$valoresRetorno[$i]['PRIMEIRO_TITULO_RELATORIO'] = jn_utf8_encode($rowTabela->PRIMEIRO_TITULO_RELATORIO);
		$valoresRetorno[$i]['SEGUNDO_TITULO_RELATORIO']  = jn_utf8_encode($rowTabela->SEGUNDO_TITULO_RELATORIO);
		$valoresRetorno[$i]['FILTROS_ESPECIAIS']         = jn_utf8_encode($rowTabela->FILTROS_ESPECIAIS);
		$valoresRetorno[$i]['NOME_CAMPO']                = jn_utf8_encode($rowTabela->NOME_TABELA_COLUNA); //$dadosInput['nomeTabela'] . '.' . $rowTabela->NOME_CAMPO);
		$valoresRetorno[$i]['DESCRICAO_CAMPO']           = jn_utf8_encode( $rowTabela->DESCRICAO_CAMPO);
		$valoresRetorno[$i]['TIPO_CAMPO']                = jn_utf8_encode( $rowTabela->TIPO_CAMPO);

		$i++;
	}

	foreach ($valoresRetorno as $value)
	{
		$retorno[]=$value;
	} 


	
}
else if($dadosInput['tipo'] == 'emitirRelatorio')
{

	/*'camposSelecionados': camposSelecionados,
	'tituloRelatorio':tituloRelatorio, 
	'criterioWhere':criterioWhere, 
	'salvarRelatorio':salvarRelatorio, 
	'tipoRelatorio':tipoRelatorio,
	'nomeRelatorioSalvar': nomeRelatorioSalvar*/

	if ($dadosInput['criterioWhere']!='')
	   $criterioWhere = stringReplace_Delphi_All($dadosInput['criterioWhere'],'\\','');

	$queryRelatorio = '';
	$orderByUsuario = '';
	$orderByGrupo   = '';
	$join           = '';

	foreach ($dadosInput['camposSelecionados'] as $value)
	{
		if ($queryRelatorio!='')
			$queryRelatorio .= ', ';

		if ($value['MARCADO'] == 'S')
		{
			if ($orderByUsuario!='')
				$orderByUsuario .= ', ';

			$orderByUsuario .= $value['NOME_CAMPO'];
		}

		$queryRelatorio .= $value['NOME_CAMPO'];

		//

		$tabelaRelacionada = Trim(CopyDelphi($value['NOME_CAMPO'],1,strposDelphi('.',$value['NOME_CAMPO'])));

		if ((strposDelphi($tabelaRelacionada,$join) === false) and ($dadosInput['tabelaPrincipal'] != $tabelaRelacionada))
		{
			$queryTemp = 'Select * From CfgRelatorioCustomizado Where (Tabela_Principal = ' . aspas($dadosInput['tabelaPrincipal']) . ') And (Tabela_Relacionada = ' . aspas($tabelaRelacionada) . ')';
	        $rowRelac  = qryUmRegistro($queryTemp);

            if ($rowRelac->TIPO_RELACIONAMENTO == '')
               $join .= ' LEFT OUTER JOIN ' . $rowRelac->TABELA_RELACIONADA . ' ON (';
            else
               $join .= $rowRelac->TIPO_RELACIONAMENTO . ' ' . $rowRelac->TABELA_RELACIONADA . ' ON (';

            if (strposDelphi('.',$rowRelac->CAMPO_TABELA_PRINCIPAL) !== false)
               $join .= $rowRelac->CAMPO_TABELA_PRINCIPAL;
            else
               $join .= $rowRelac->TABELA_PRINCIPAL . '.' . $rowRelac->CAMPO_TABELA_PRINCIPAL;

            $join .= ' = ';

            if (strposDelphi('.',$rowRelac->CAMPO_TABELA_RELACIONADA) !== false)
               $join .= $rowRelac->CAMPO_TABELA_RELACIONADA;
            else
               $join .= $rowRelac->TABELA_RELACIONADA . '.' . $rowRelac->CAMPO_TABELA_RELACIONADA;

           	$join .= ') ' . PHP_EOL;
		}

	} 

	$queryRelatorio = 'Select ' . $queryRelatorio . ' from ' . $dadosInput['tabelaPrincipal'] . PHP_EOL;
	$queryRelatorio .= $join;

	if ($criterioWhere!='')
	   $queryRelatorio .= ' where ' . $criterioWhere;

	if ($dadosInput['primeiroAgrupamento'] != '')
	    $orderByGrupo = ' Order By ' . $dadosInput['primeiroAgrupamento'];		

	if ($dadosInput['segundoAgrupamento'] != '')
	    $orderByGrupo = $orderByGrupo . ' , ' . $dadosInput['segundoAgrupamento'];		

	if ($dadosInput['terceiroAgrupamento'] != '')
	    $orderByGrupo = $orderByGrupo . ' , ' . $dadosInput['terceiroAgrupamento'];		

	if (($orderByGrupo == '') and ($orderByUsuario!=''))
	    $orderByFinal = ' Order By ' . $orderByUsuario;		
	else
	{
	    $orderByFinal = $orderByGrupo;

	    if ($orderByUsuario!= '')
	    	$orderByFinal = $orderByFinal . ', ' . $orderByUsuario;		
	}

	$queryPrincipal = $queryRelatorio . $orderByFinal;
	$formato        = $dadosInput['tipoRelatorio'];

	$nomeArquivoProcesso = executaRelatorio($formato,$queryPrincipal,'RELATORIO_DINAMICO', 'RELATORIO_DINAMICO','S', 
		                                    $dadosInput, $dadosInput['tituloRelatorio'], $dadosInput['tituloSalvarRelatorio'],
		                                    $criterioWhere);

	$retorno['MSG']            = 'OK';
	$retorno['ARQUIVO_GERADO'] = $nomeArquivoProcesso;
}
else if($dadosInput['tipo'] == 'trataErro')
{


	if ($_SESSION['AliancaPx4Net']=='S') // Se for o ERP
	{

		$qryTmp  = qryUmRegistro('select * from LOG_OPERACOES_ERRO where numero_registro = ' . aspas($dadosInput['erro']));

		$msgErro     = jn_utf8_encode($qryTmp->ERRO);
		$msgOriginal = jn_utf8_encode($qryTmp->ERRO); 

		if (strposDelphi('PRIMARY KEY',$msgErro) >= 0)
		{
			$msgErro = 'Violação de chave primária. <br>
		                O código ou campo de identificação informado já existe, retorne e informe um novo código para poder gravar o registro.';

		    if (strposDelphi('Não é possível inserir',$msgOriginal)>=0)
		    	$msgErro .= '<br>' . copyDelphi($msgOriginal,strposDelphi('Não é possível inserir',$msgOriginal),100);

			$erroMsg = 'Houve um erro ao realizar a operação, a mensagem literal do erro é: <br>' . $msgErro;
		}
		else if (strposDelphi(strtoupper('Erro ao converter tipo de dados varchar em numeric'),strToUpper($msgErro)) >= 0)
		{
			$msgErro = 'Erro de preenchimento de campo. <br>
		                Um campo de valor numérico, foi preenchido com dado não numérico ou com algum caracter não numérico.<br>
		                Volte, corrija as informações e confirme novamente. ';
			$erroMsg = 'Houve um erro ao realizar a operação, a mensagem literal do erro é: <br>' . $msgErro;

		}
		else if (strposDelphi(strtoupper('restrição do CHECK'),strToUpper($msgErro)) >= 0)
		{
		    if (strposDelphi('O conflito ocorreu ',$msgOriginal)>=0)
		    	$msgErro = '<br>' . copyDelphi($msgOriginal,strposDelphi('O conflito ocorreu ',$msgOriginal),100);

			$erroMsg = 'Houve um erro ao realizar a operação, há uma "CONSTRAINT" que identificou um erro nos dados.<br><br>A mensagem literal do erro é:' . $msgErro;

		}
		else if (strposDelphi(strtoupper('Não é possível inserir'),strToUpper($msgErro)) >= 0)
		{
		    if (strposDelphi('Não é possível inserir o',$msgOriginal)>=0)
		    	$msgErro = '<br>' . copyDelphi($msgOriginal,strposDelphi('Não é possível inserir o',$msgOriginal)+1,170);

			$erroMsg = 'Houve um erro ao realizar a operação.<br><br>A mensagem literal do erro é:' . $msgErro;
		}
		else if ((strposDelphi(strtoupper('ELETE conflitou'),strToUpper($msgErro)) >= 0) and 
			     (strposDelphi(strtoupper('REFERENCE FK'),strToUpper($msgErro)) >= 0))
		{
			$erroMsg = 'Erro de chave estrangeira, a informação que você está tentando excluir já está sendo utilizada por tabelas relacionadas do sistema.<br>
		                Neste caso, o registro não pode ser excluído para manter a integridade referencial.<br><br>
		                A mensagem literal do erro é: <br>' . $msgOriginal;
		}
		else
		{
			$erroMsg = 'Houve um erro ao realizar a operação, a mensagem literal do erro é:<br>' . $msgOriginal;
		}
		

		$retorno['TITULO']   = 'Erro log(' . $dadosInput['erro'] . ')' ;
		$retorno['MENSAGEM'] = $erroMsg;
		$retorno['DIALOGO']  = 'SIM';
	}
	else
	{
		$retorno['DIALOGO']  = 'NAO';
	}


}

echo json_encode($retorno);







function mascaraLabelCampo($nomeCampo)
{

	$nomeCampo = str_replace("_", " ",$nomeCampo);
	return strtoupper(substr($nomeCampo,0,1)) . strtolower(substr($nomeCampo,1,40));

}


function selecionaCamposConformeTabela($tabela)
{

		if (strtoupper($tabela)=='PS1000')
		{
			return  ' NOME_ASSOCIADO, CODIGO_ASSOCIADO, NUMERO_CPF, TIPO_ASSOCIADO, DATA_ADMISSAO, DATA_EXCLUSAO, 
			                                FLAG_PLANOFAMILIAR, CODIGO_TITULAR, CODIGO_EMPRESA, CODIGO_PLANO ';
		}
		else if (strtoupper($tabela)=='PS1010')
		{
			return  ' NOME_EMPRESA, CODIGO_EMPRESA, NUMERO_CNPJ, DATA_ADMISSAO, DATA_EXCLUSAO, FLAG_PLANOFAMILIAR';
		}

}





