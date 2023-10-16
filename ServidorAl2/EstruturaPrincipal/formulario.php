<?php
require('../lib/base.php');

require('../private/autentica.php');

require('../EstruturaEspecifica/complementoSql.php');
require('exclusao.php');
require('../EstruturaEspecifica/antesDaGravacao.php');
require('../EstruturaEspecifica/depoisDaGravacao.php');
require('../EstruturaEspecifica/tratamentoErroSql.php');
require('../EstruturaEspecifica/antesMostrarCampos.php');
require('../EstruturaEspecifica/saidaCampo.php');
require('../EstruturaEspecifica/azureStorage.php');
require('../EstruturaEspecifica/ignoraSubProcesso.php');
require('../EstruturaEspecifica/apresentarMensagemEspecifica.php');
require('../EstruturaEspecifica/antesDeIncluirAlterarExcluir.php');



if($dadosInput['tipo'] =='dados'){
	
	$permissoes      = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$_SESSION['perfilOperador'],$dadosInput['tab'],$dadosInput['chave'],$dadosInput['tabelaOriginal']);
	$parametroPrompt = $dadosInput['parametroPrompt'];
	
	if($dadosInput['chave'] != ''){
		
		$queryInformacoes = 'Select * from '.$dadosInput['tab'].' where '. $dadosInput['nomeChave'].' = '. aspas($dadosInput['chave']);
		$resInformacoes = jn_query($queryInformacoes);
		$rowInformacoes = jn_fetch_object($resInformacoes);
		if($rowInformacoes)
			$rowInformacoes = (array)$rowInformacoes;
		
		//pr($rowInformacoes);
	}
	
	
	if((trim($dadosInput['chave'] == ''))and($dadosInput['acao']=='VIS')){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Não existe registro para ser visualizado.';	
	}else if(! $permissoes[$dadosInput['acao']]){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Você nao tem permissão de '.$dadosInput['acao'].' a tabela :'.$dadosInput['tab'] . ' [Ref1]';
	
	}else{
	
		$retorno['STATUS'] = 'OK';
		
		
		$queryTabela = 'Select DESCRICAO_TABELA,FLAG_2COLUNAS_AL2 from  cfgtabelas_sis where nome_tabela ='.aspas($dadosInput['tab']);
		
		$resTabela = jn_query($queryTabela);
		
		if($rowTabela = jn_fetch_object($resTabela)){
			$retorno['DESCRICAO'] = jn_utf8_encode($rowTabela->DESCRICAO_TABELA);
			if($rowTabela->FLAG_2COLUNAS_AL2 == 'S'){
				$retorno['TAMANHO_FLEX'] = '49';
			}else{
				$retorno['TAMANHO_FLEX'] = '100';
			}
		}
		
		$retorno['DESCRICAO_ARVORE'] = $retorno['DESCRICAO'];
		$retorno['CHAVE']            = $dadosInput['chave'];
		$retorno['TABELA']           = $dadosInput['tab'];
		
		$querySubProcesso = 'SELECT NOME_TABELA_FILHA,LABEL_ATALHO,HINT_ATALHO,NUMERO_REGISTRO,CAMPO_CONDICAO_ABERTURA01,VALOR_CONDICAO_ABERTURA01,CAMPO_CONDICAO_ABERTURA02,VALOR_CONDICAO_ABERTURA02   
							 FROM cfgtabelas_subprocessos_cd 
							 inner join cfgtabelas_sis on cfgtabelas_subprocessos_cd.nome_tabela_filha= cfgtabelas_sis.nome_tabela
							 WHERE comportamento_frm_edicao <> 3 and nome_tabela_principal = '.aspas($dadosInput['tab']).' order by ordem_atalho';
		$resSubProcesso = jn_query($querySubProcesso);
		$retorno['SUBPROCESSOS'] = array();
		//echo $querySubProcesso; 
		while($rowSubProcesso = jn_fetch_object($resSubProcesso))
		{
			if(ignoraSubProcesso($dadosInput,$rowSubProcesso->NOME_TABELA_FILHA))
			{
				continue;
			}
			
			if($rowSubProcesso->CAMPO_CONDICAO_ABERTURA01!='')
			{
				if (strposDelphi('IN(',$rowSubProcesso->VALOR_CONDICAO_ABERTURA01)!=-1)
				{
					if (strposDelphi(strtoupper($rowInformacoes[$rowSubProcesso->CAMPO_CONDICAO_ABERTURA01]),strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA01))==-1)
					{
						continue;
					}
				}
				else if(strtoupper($rowInformacoes[($rowSubProcesso->CAMPO_CONDICAO_ABERTURA01)]) != strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA01))
				{
					continue;
				}
			}
			
			if($rowSubProcesso->CAMPO_CONDICAO_ABERTURA02!='')
			{
				if (strposDelphi('IN(',$rowSubProcesso->VALOR_CONDICAO_ABERTURA02)!=-1)
				{
					if (strposDelphi(strtoupper($rowInformacoes[$rowSubProcesso->CAMPO_CONDICAO_ABERTURA02]),strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA02))==-1)
					{
						continue;
					}
				}
				else if(strtoupper($rowInformacoes[($rowSubProcesso->CAMPO_CONDICAO_ABERTURA02)]) != strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA02))
				{
					continue;
				}
			}
			
			$item['TABELAFILHA'] = jn_utf8_encode($rowSubProcesso->NOME_TABELA_FILHA);
			$item['LABEL']		 = jn_utf8_encode($rowSubProcesso->LABEL_ATALHO); 
			$item['HINT']        = jn_utf8_encode($rowSubProcesso->HINT_ATALHO);
			$item['REGISTRO']    = jn_utf8_encode($rowSubProcesso->NUMERO_REGISTRO);
			$retorno['SUBPROCESSOS'][] = $item;
		}	



		if(count($dadosInput['subprocesso'])>0){
			$querySubProcesso = 'SELECT 
								cfgtabelas_subprocessos_cd.*
							FROM cfgtabelas_subprocessos_cd where numero_registro ='.aspas($dadosInput['subprocesso']['registro']);
			
			$resSubProcesso = jn_query($querySubProcesso);
			
			if($rowSubProcesso = jn_fetch_object($resSubProcesso)){

				$camposSub = "";
				$retorno['TIPO_ADICIONAR']  = $rowSubProcesso->TIPO_ADICIONAR;
				if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01 != ''){
					$camposSub  =  ($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01).' CAMPO01 ';
				}
				if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02 != ''){
					$camposSub .=  ' , '.($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02).' CAMPO02';
				}
				
				$queryDadoSub = "Select ".$camposSub." from ".($rowSubProcesso->NOME_TABELA_PRINCIPAL)."
									 where ". ($rowSubProcesso->NOME_TABELA_PRINCIPAL) .".".($dadosInput['subprocesso']['nomeChave'])." = ".aspas($dadosInput['subprocesso']['chave']);
				$resDadoSub = jn_query($queryDadoSub);
				$rowDadoSub = jn_fetch_object($resDadoSub);
				//strtolower($rowSubProcesso->campo_ligacao_filha_01)
			}		
		}
		
		
		if($retorno['TIPO_ADICIONAR']==''){
			$retorno['TIPO_ADICIONAR'] = 'N';
		}
		
		$queryGrupos = 'SELECT PASTA_APRESENTACAO FROM cfgcampos_sis_cd WHERE nome_tabela = '.aspas($dadosInput['tab']).' GROUP BY pasta_apresentacao ORDER BY pasta_apresentacao ';
		$resGrupos   = jn_query($queryGrupos);
		//echo $queryGrupos;

		while($rowGrupos = jn_fetch_object($resGrupos))
		{
			$campos = Array();
			$queryCampos = 'SELECT * FROM cfgcampos_sis_cd WHERE nome_tabela = '.aspas($dadosInput['tab']).' and pasta_apresentacao = '.aspas(jn_utf8_encode($rowGrupos->PASTA_APRESENTACAO)).' ORDER by numero_ordem_criacao ';
			//print_r($rowGrupos);
			$resCampos = jn_query($queryCampos);
			//echo $queryCampos;
			
			while($rowCampos = jn_fetch_object($resCampos))
			{
				
				$itemCampos = Array();

				$itemCampos['NOME_CAMPO']  = jn_utf8_encode($rowCampos->NOME_CAMPO); 
				$itemCampos['LABEL']       = jn_utf8_encode($rowCampos->LABEL_CAMPO); 
				$itemCampos['TIPO']        = jn_utf8_encode(strtoupper($rowCampos->COMPONENTE_FORMULARIO));
				$itemCampos['HINT']        = jn_utf8_encode($rowCampos->HINT_EXPLICACAO);
				$itemCampos['VALOR']       = ''; // auto VALOR: { 'VALOR': '2', 'DESC': 'AAACC' }
				
								
				if(($rowCampos->FLAG_DESCRICAO_ARVORE == 'S') and (($rowInformacoes[($itemCampos['NOME_CAMPO'])])!='')){
					$retorno['DESCRICAO_ARVORE'] = jn_utf8_encode($rowInformacoes[($itemCampos['NOME_CAMPO'])]);
				}				
				
				$itemCampos['CLASS']       = jn_utf8_encode($rowCampos->CLASSE_CAMPO);
				
				if(trim($itemCampos['CLASS'])=='')
					$itemCampos['CLASS']       = '430px';
				//$itemCampos['CLASS'] = '{max-width: '.$itemCampos['CLASS'].'px;}';
				
				if($rowCampos->FLAG_NOTNULL=="S")
					$itemCampos['OBRIGATORIO'] = true;
				else
					$itemCampos['OBRIGATORIO'] = false;
				
				$itemCampos['TAMANHO']     = jn_utf8_encode($rowCampos->TAMANHO_CAMPO);
				
				if($itemCampos['TAMANHO'] == 0)
					$itemCampos['TAMANHO'] = '';
				
				$itemCampos['MASK']        = jn_utf8_encode($rowCampos->TIPO_MASCARA);
				$itemCampos['TABELA_AUTO'] = jn_utf8_encode($rowCampos->NOME_TABELA_RELACIONADA);
				$itemCampos['DESC_AUTO']   = jn_utf8_encode($rowCampos->CAMPO_PESQUISA_TABELA_RELAC);
				$itemCampos['CHAVE_AUTO']  = jn_utf8_encode($rowCampos->CAMPO_ID_TABELA_RELAC);
				$itemCampos['CAMPOS_PESQUISA_AUTO']  = json_decode($rowCampos->CAMPOS_PESQUISA_AUTO);
				if($itemCampos['CAMPOS_PESQUISA_AUTO']== null)
					$itemCampos['CAMPOS_PESQUISA_AUTO'] = '';
				$itemCampos['SAIDA_CAMPO']  = json_decode($rowCampos->SAIDA_CAMPO);
				if($itemCampos['SAIDA_CAMPO']== null)
					$itemCampos['SAIDA_CAMPO'] = '';
				$itemCampos['CAMPOS_PESQUISA_AUX_AUTO']  = json_decode($rowCampos->CAMPOS_PESQUISA_AUX_AUTO);
				if($itemCampos['CAMPOS_PESQUISA_AUX_AUTO']== null)
					$itemCampos['CAMPOS_PESQUISA_AUX_AUTO'] = '';
				
				$itemCampos['COMPORTAMENTO']  = jn_utf8_encode($rowCampos->COMPORTAMENTO_FRM_EDICAO);
				$itemCampos['CHAVE']  = jn_utf8_encode($rowCampos->FLAG_CHAVEPRIMARIA);
				$itemCampos['LINK_ADD_AUTO']  = jn_utf8_encode($rowCampos->LINK_ADD_AUTO);
						
				if($dadosInput['acao'] != 'INC'){					
					if(is_object($rowInformacoes[($itemCampos['NOME_CAMPO'])])){						
						$itemCampos['VALOR'] = $rowInformacoes[($itemCampos['NOME_CAMPO'])]->format('Y-m-d');
					}else{
						$itemCampos['VALOR'] = ($rowInformacoes[($itemCampos['NOME_CAMPO'])]);
					}
				}else{
					//default
					$itemCampos['VALOR'] = jn_utf8_encode($rowCampos->VALOR_PADRAO);
					
					if($itemCampos['TIPO'] == 'DATE'){
						$itemCampos['VALOR'] = strtoupper($itemCampos['VALOR']);
						$auxData = explode('|',trim($itemCampos['VALOR'])); 
						
						
						if($auxData[0] =='[DATE]'){
							if(count($auxData)>1){
								$itemCampos['VALOR'] = date('Y-m-d',strtotime($auxData[1]." day"));
							}else{
								$itemCampos['VALOR'] = date('Y-m-d');
							}
						}
					}
					if($itemCampos['TIPO'] == 'TIME'){
						$itemCampos['VALOR'] = strtoupper($itemCampos['VALOR']);
						if(trim($itemCampos['VALOR']) =='[NOW]'){
							$itemCampos['VALOR'] = date('H:i'); 						
						}
					}
					for($i=0;$i<Count($dadosInput['filtros']);$i++){
						if($dadosInput['filtros'][$i]['CAMPO']== $itemCampos['NOME_CAMPO']){
							$itemCampos['VALOR'] = jn_utf8_decode($dadosInput['filtros'][$i]['VALOR']);
						}
					}
				}
				
				if(($itemCampos['TIPO'] =='NUMBER')and ($itemCampos['VALOR']!='')){
				
					$pos = strpos($itemCampos['MASK'] , ',');
					if ($pos === false) {
						
					} else {
						$itemCampos['VALOR'] = str_replace('.',',',$itemCampos['VALOR']);

					}
				}
				
				//antesMostrarCampo($dadosInput['acao'],$dadosInput['tab'],$dadosInput['tabelaOriginal'],$dadosInput['chave'],$dadosInput['nomeChave'],$itemCampos,$campos);

				if (antesMostrarCampo($dadosInput['acao'],$dadosInput['tab'],$dadosInput['tabelaOriginal'],$dadosInput['chave'],$dadosInput['nomeChave'],$itemCampos,$campos, $parametroPrompt, $rowDadoSub) == 'IGNORAR_CAMPO')
				{
					$itemCampos = Array();
					continue;
				}

				
				if(count($dadosInput['subprocesso'])>0)
				{
					
					trataCampoEspecialSubCadastro($dadosInput,$itemCampos);

					/*if($itemCampos['NOME_CAMPO']=='TIPO_ASSOCIADO')
					{
						$itemCampos['VALOR'] = 'D';
					}*/

					if(($itemCampos['NOME_CAMPO'])==($rowSubProcesso->CAMPO_LIGACAO_FILHA_01)){
						if($itemCampos['COMPORTAMENTO']  == 1)
							$itemCampos['COMPORTAMENTO']  = 2;
						if($dadosInput['acao'] == 'INC'){
							$itemCampos['VALOR'] = jn_utf8_encode($rowDadoSub->CAMPO01); 
						}
					}
					if(($itemCampos['NOME_CAMPO'])==($rowSubProcesso->CAMPO_LIGACAO_FILHA_02)){
						if($itemCampos['COMPORTAMENTO']  == 1)
							$itemCampos['COMPORTAMENTO']  = 2;
						if($dadosInput['acao'] == 'INC'){
							$itemCampos['VALOR'] = jn_utf8_encode($rowDadoSub->CAMPO02); 
						}
					}
					
						
				}				
				
				
				
				if(($itemCampos['TIPO'] == 'AUTOCOMPLETE')and($itemCampos['VALOR']!='')){
						//pr($rowCampos); 
						$queryAuto = 'select '.($rowCampos->CAMPO_ID_TABELA_RELAC).' AS valor, '.($rowCampos->CAMPO_PESQUISA_TABELA_RELAC).' AS descr from '.($rowCampos->NOME_TABELA_RELACIONADA).' where '.($rowCampos->CAMPO_ID_TABELA_RELAC).' = '.aspas($itemCampos['VALOR']);
	
						$resAuto = jn_query($queryAuto);

							//echo $queryColunas;
							
						if($rowAuto = jn_fetch_object($resAuto)){
							$itemCampos['VALOR'] = array();
							$itemCampos['VALOR']['VALOR'] = jn_utf8_encode($rowAuto->VALOR);
							$itemCampos['VALOR']['DESC']  = jn_utf8_encode($rowAuto->DESCR);
							
						}else{
							$itemCampos['VALOR'] = array();
							$itemCampos['VALOR']['VALOR'] = '';
							$itemCampos['VALOR']['DESC']  = '';
						}
				}if(($itemCampos['TIPO'] == 'AUTOCOMPLETE')and($itemCampos['VALOR']=='')){
							$itemCampos['VALOR'] = array();
							$itemCampos['VALOR']['VALOR'] = '';
							$itemCampos['VALOR']['DESC']  = '';					
				}else if($itemCampos['TIPO']=='GROUPCHECKBOX'){
					$dado = $rowCampos->OPCOES_COMBO;
					$dado = explode(';',$dado);
					$linhas = array();
					foreach ($dado as $value){
						$linha['VALOR'] = $value;
						$linha['CHECKED'] = 'N';
						if($itemCampos['VALOR']!=''){
							$selecionados = explode(';',$itemCampos['VALOR']);
							foreach ($selecionados as $itemSelecionado){
								$itemSelecionadoSemColchete = explode('[',$itemSelecionado);
								$itemSemColchete = explode('[',$linha['VALOR']);
								//pr($itemSemColchete.' --'.$itemSelecionadoSemColchete);
								if ($itemSemColchete[0] === $itemSelecionadoSemColchete[0]) {
									$linha['CHECKED'] = 'S';
									if(count($itemSelecionadoSemColchete)>1){
											$linha['VALOR'] = $itemSelecionado;
									}
									break;
								}else{
									$linha['CHECKED'] = 'N';
								}
							}
							/*
							$pos = strpos(';'.$itemCampos['VALOR'].';', ';'.$linha['VALOR'].';');
							if ($pos === false) {
								$linha['CHECKED'] = 'N';
							}else{
								$linha['CHECKED'] = 'S';
							}
							*/
						}
						$linha['VALOR'] = jn_utf8_encode($linha['VALOR']);
						
						$linhas[] = $linha; 
					}
					$itemCampos['VALOR'] = jn_utf8_encode($itemCampos['VALOR']);
					$itemCampos['GRUPO_DADOS'] = $linhas;
				
				}else if(($itemCampos['TIPO'] == 'COMBOBOX')or($itemCampos['TIPO'] == 'RADIO')){
					$dado = $rowCampos->OPCOES_COMBO;
					
					if($dado == '' and $itemCampos['OPCOES_COMBO'] != ''){
						$dado = $itemCampos['OPCOES_COMBO'];
					}
					
					$dado = explode(';',$dado);
					$linhas = array();
					foreach ($dado as $value){
						if(strpos($value, '-')>0){
							$value = explode('-',$value);
							$linha['VALOR']   = jn_utf8_encode(trim($value[0]));
							$linha['LABEL']   = jn_utf8_encode(trim($value[1]));
						}else{
							$linha['VALOR']   = jn_utf8_encode(trim($value));
							$linha['LABEL']   = jn_utf8_encode(trim($value));
						}
						$linhas[] = $linha; 
					}
					//pr($itemCampos['VALOR']);
					//pr(jn_utf8_encode($itemCampos['VALOR']));
					$itemCampos['VALOR'] = jn_utf8_encode($itemCampos['VALOR']);
					$itemCampos['GRUPO_DADOS'] = $linhas;
				}else if(strpos($itemCampos['TIPO'],'ANEXO') === 0){
					$itemCampos['EXT'] = $rowCampos->OPCOES_COMBO;
					
					$queryImagem = "SELECT CAMINHO_ARQUIVO_ARMAZENADO||NOME_ARQUIVO_ARMAZENADO IMAGEM, NUMERO_REGISTRO,NOME_ARQUIVO_ORIGINAL FROM controle_arquivos
								WHERE nome_tabela = ".aspas(($dadosInput['tab']))." AND chave_registro = ".aspas($dadosInput['chave'])." AND data_exclusao IS NULL ";
					
					if(($_SESSION['codigoSmart'] == '3423' or retornaValorConfiguracao('ACEITA_MULTIPLOS_ANEXOS')) && $dadosInput['tab'] == 'TMP1000_NET'){//Plena ou Configuracao
						$queryImagem .= " AND nome_componente_espefico = ".aspasNull($itemCampos['TIPO']);
					}
					$resultImagem = jn_query($queryImagem); 
					$itemCampos['VALOR'] = Array();
					
					while($rowImagem  = jn_fetch_object($resultImagem)){
						
						if(file_exists('../../UploadArquivos/server/files/'. $rowImagem->IMAGEM)){
							// pathinfo($file, PATHINFO_EXTENSION);
							//if(strrpos(mime_content_type('../../UploadArquivos/server/files/'. $rowImagem->imagem), 'image') === false) {
							$finfo = finfo_open(FILEINFO_MIME_TYPE);
							$type = finfo_file($finfo, '../../UploadArquivos/server/files/'. $rowImagem->IMAGEM);	
							if(strrpos($type, 'image') === false) {								
								$nomeArquivo = pathinfo('../../UploadArquivos/server/files/'. $rowImagem->IMAGEM);
		
								$extensao = $nomeArquivo['extension'];
		
								$itemImagem['src'] = 'EstruturaPrincipal/imagens/tipoImagem.php?tipo='.$extensao;
							}else{
								$itemImagem['src'] = 'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowImagem->NUMERO_REGISTRO;
							}
							$itemImagem['tipo']  = 'web';

							$itemValorImagem['name'] = $rowImagem->NOME_ARQUIVO_ORIGINAL;
							$itemValorImagem['link'] = 'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowImagem->NUMERO_REGISTRO;;
							$itemValorImagem['reg'] = $rowImagem->NUMERO_REGISTRO;
							$itemImagem['valor'] = $itemValorImagem;	
							$itemCampos['VALOR'][] = $itemImagem;		
						}else{
							if(utilizaBlobStorage()){
								if(existeFileBlogStorage('UploadArquivos/server/files/'. $rowImagem->IMAGEM)){
									$type = tipoMineBlogStorage('UploadArquivos/server/files/'. $rowImagem->IMAGEM);
									
									if(strrpos($type, 'image') === false) {								
										//$nomeArquivo = pathinfo('../../UploadArquivos/server/files/'. $rowImagem->IMAGEM);
										$extensao = ltrim(substr( $rowImagem->IMAGEM, strrpos( $rowImagem->IMAGEM, '.' ) ), '.' );
										
										//$extensao = $nomeArquivo['extension'];
				
										$itemImagem['src'] = 'EstruturaPrincipal/imagens/tipoImagem.php?tipo='.$extensao;
									}else{
										$itemImagem['src'] = 'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowImagem->NUMERO_REGISTRO;
									}
									$itemImagem['tipo']  = 'web';

									$itemValorImagem['name'] = $rowImagem->NOME_ARQUIVO_ORIGINAL;
									$itemValorImagem['link'] = 'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowImagem->NUMERO_REGISTRO;;
									$itemValorImagem['reg'] = $rowImagem->NUMERO_REGISTRO;
									$itemImagem['valor'] = $itemValorImagem;	
									$itemCampos['VALOR'][] = $itemImagem;	
								}
							}	
						}
					}
				}else if($itemCampos['TIPO'] == 'AUTOCOMPLETE'){
					
				}else{
					$itemCampos['VALOR'] = jn_utf8_encode($itemCampos['VALOR']);
				}
				
				$campos[] = $itemCampos; 
				
			}
			
			if (count($campos) >= 1)
			{
				$dadosGrupo =  explode('-',$rowGrupos->PASTA_APRESENTACAO);
				$grupo['INDICE'] = jn_utf8_encode(trim($dadosGrupo[0]));
				$grupo['DESC']   = jn_utf8_encode(trim($dadosGrupo[1]));
				$grupo['CAMPOS'] = $campos;
				$retorno['GRUPOS'][] = $grupo;
			}

			//pr($retorno['GRUPOS']);
			//pr(count($campos));
		}
	}
	
	
	$mensagem = apresentarMensagemEspecifica($dadosInput['acao'],$dadosInput['tab']);

	if($mensagem){
		$$retorno['STATUS'] = 'OK';
		$retorno['MSG']    = $mensagem;
	}


	
}else if($dadosInput['tipo'] =='salvar'){
	//$dadosInput['tipo']
	//$dadosInput['tab']
	//$dadosInput['acao'] INC ALT
	//$dadosInput['campos']
	
	
	$campos  ='';//Somente Insert
	$valores ='';//Insert Update
	
	$permissoes = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$_SESSION['perfilOperador'],$dadosInput['tab']);
	
	if(! $permissoes[$dadosInput['acao']]){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Você nao tem permissão de '.$dadosInput['acao'].' a tabela :'.$dadosInput['tab'] . ' [Ref2]';
	
	}else{
		
		
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
					$camposSub  =  ($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01).' CAMPO01 ';
				}
				if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02 != ''){
					$camposSub .=  ' , '.($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02).' CAMPO02';
				}
				
				$queryDadoSub = "Select ".$camposSub." from ".($rowSubProcesso->NOME_TABELA_PRINCIPAL)."
									 where ". ($rowSubProcesso->NOME_TABELA_PRINCIPAL) .".".($dadosInput['subprocesso']['nomeChave'])." = ".aspas($dadosInput['subprocesso']['chave']);
				$resDadoSub = jn_query($queryDadoSub);
				$rowDadoSub = jn_fetch_object($resDadoSub);
				//strtolower($rowSubProcesso->campo_ligacao_filha_01)
				for($i=0;$i<Count($dadosInput['campos']);$i++){
					if(($dadosInput['campos'][$i]['CAMPO']) ==($rowSubProcesso->CAMPO_LIGACAO_FILHA_01))
						$dadosInput['campos'][$i]['VALOR'] = $rowDadoSub->CAMPO01;
					if(($dadosInput['campos'][$i]['CAMPO']) ==($rowSubProcesso->CAMPO_LIGACAO_FILHA_02))
						$dadosInput['campos'][$i]['VALOR'] = $rowDadoSub->CAMPO02;
				}
				
			}		
		}
		
		
		antesGravacao($dadosInput['acao'],$dadosInput['tab'],$dadosInput['tabelaOriginal'],$chave,$nomeChave,$dadosInput['campos'],$retorno);

		// Se a msg for de validação e o usuário tiver mandado ignorar a validacao, ele tranformará a validação em OK, isto se for apenas validações.
		if (($dadosInput['ignoraMsgsValidacao']=='S') and ($retorno['STATUS']=='VALIDACAO'))
		   $retorno['STATUS'] = 'OK';
		
		
		if($retorno['STATUS']=='OK'){
		
			if($dadosInput['acao']=='INC'){
				$sqlEdicao 	= '';

				$geraChaveR = geraChave($dadosInput['tab'], $dadosInput['campos']);
				
				$dadosBlob = array();
				
				for($i=0;$i<Count($dadosInput['campos']);$i++){
					
					if($dadosInput['campos'][$i]['CHAVE']=='S'){
						$nomeChave = $dadosInput['campos'][$i]['CAMPO'];
					}
					
					
					if(strpos($dadosInput['campos'][$i]['CAMPO'], 'CP__EX_')=== 0){
						continue;
					}
					
					if(($dadosInput['campos'][$i]['CHAVE']=='S')and (!$geraChaveR)){
						//pr($dadosInput['campos'][$i]);
						continue;
					}
					if($dadosInput['campos'][$i]['CHAVE']=='S'){
						$retorno['CHAVE'] = $dadosInput['campos'][$i]['VALOR'];
					}
					
					//if($campos!=''){
					//	$campos  .=' , ';
					//	$valores .=' , ';
					//}
					
					//$campos   .=$dadosInput['campos'][$i]['CAMPO'];
					if(strlen($dadosInput['campos'][$i]['VALOR'])>30000){
						$dadosBlob[$i] = $dadosInput['campos'][$i];
						$auxBlob = jn_utf8_decode($dadosInput['campos'][$i]['VALOR']);
						$dadosInput['campos'][$i]['VALOR'] = substr($auxBlob,0,30000); 
						$auxBlob = substr($auxBlob,30000,strlen($auxBlob)-30000); 
						$dadosBlob[$i]['VALOR'] = $auxBlob;
						//$valores  .= aspasNull(($dadosInput['campos'][$i]['VALOR']));
					}//else{
					//	$valores  .= aspasNull(jn_utf8_decode($dadosInput['campos'][$i]['VALOR']));
					//}
					$sqlEdicao 	.= linhaJsonEdicao($dadosInput['campos'][$i]['CAMPO'], $dadosInput['campos'][$i]['VALOR'],'ANULL');
					
					
				}
				//$sql = 'INSERT INTO '. ($dadosInput['tab']).'('.$campos.')VALUES('.$valores.')';
				$retornoGrava = gravaEdicao($dadosInput['tab'], $sqlEdicao, 'I', '',$nomeChave);

					
				//if( $_SESSION['type_db'] == 'firebird' ){
				//	$sql = $sql . ' returning '.$nomeChave;
				//}

			//	$sqlEdicao 	.= linhaJsonEdicao('Valor_Fatura_Bruto', 0,'N');

			//	gravaEdicao('PS1020', $sqlEdicao, $tipoEdicao, $criterioWhereGravacao);

				//$resInsert = jn_query($sql);
				

				//if($resInsert){
				if($retornoGrava['RES']){
					$retorno['STATUS'] = 'OK';
					//if( $_SESSION['type_db'] == 'firebird' ){
					//	$dadosRetornoInsert = jn_fetch_object($resInsert);
					//	$dadosRetornoInsert=(array) $dadosRetornoInsert;
						
					//}	
					
						
					for($i=0;$i<Count($dadosInput['campos']);$i++){
						if($dadosInput['campos'][$i]['CHAVE']=='S'){
							if(!$geraChaveR){
								$dadosInput['campos'][$i]['VALOR'] =$retornoGrava['ID'];
								//if( $_SESSION['type_db'] == 'firebird' ){
								//	$dadosInput['campos'][$i]['VALOR'] = $dadosRetornoInsert[$nomeChave];
								//}else{
								//	$dadosInput['campos'][$i]['VALOR'] = jn_insert_id();
								//}
							}
							$retorno['CHAVE'] = $dadosInput['campos'][$i]['VALOR'];
							$retorno['NOMECHAVE'] = $dadosInput['campos'][$i]['CAMPO'];
							updateBlob($dadosBlob,$dadosInput['tab'],$retorno['NOMECHAVE'],$retorno['CHAVE']);
							break;
						}
					}
								
					
				}else{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = jn_utf8_encode(erroSql(jn_GetErroSql()));
				}
			}else if($dadosInput['acao']=='ALT'){
				
				//$valores = '';
				$dadosBlob = array();
				$sqlEdicao 	= '';
				for($i=0;$i<Count($dadosInput['campos']);$i++){
					
					if(strpos($dadosInput['campos'][$i]['CAMPO'], 'CP__EX_')=== 0){
						continue;
					}
					
					if($dadosInput['campos'][$i]['CHAVE'] == 'S'){
						$nomeChave  = $dadosInput['campos'][$i]['CAMPO'];
						$valorChave = $dadosInput['campos'][$i]['VALOR'];
						continue;
					}
					//if($valores!=''){
					//	$valores .=' , ';
					//}
					if(strlen($dadosInput['campos'][$i]['VALOR'])>30000){
						$dadosBlob[$i] = $dadosInput['campos'][$i];
						$auxBlob = jn_utf8_decode($dadosInput['campos'][$i]['VALOR']);
						$dadosInput['campos'][$i]['VALOR'] = substr($auxBlob,0,30000); 
						$auxBlob = substr($auxBlob,30000,strlen($auxBlob)-30000); 
						$dadosBlob[$i]['VALOR'] = $auxBlob;
						//$valores  .= $dadosInput['campos'][$i]['CAMPO'].'='. aspasNull(($dadosInput['campos'][$i]['VALOR']));
					}else{
						//$valores  .= $dadosInput['campos'][$i]['CAMPO'].'='. aspasNull(jn_utf8_decode($dadosInput['campos'][$i]['VALOR']));
					}
					$sqlEdicao 	.= linhaJsonEdicao($dadosInput['campos'][$i]['CAMPO'], $dadosInput['campos'][$i]['VALOR'],'ANULL');
				}
				
				//$sql = 'UPDATE '.($dadosInput['tab']).' SET '.$valores.' WHERE '.$nomeChave.'='.aspas($valorChave);
				//pr($sql);
				
				$resUpdate =  gravaEdicao($dadosInput['tab'], $sqlEdicao, 'A', $nomeChave.'='.aspas($valorChave));
				
				if($resUpdate){
					$retorno['STATUS'] = 'OK';
					$retorno['CHAVE'] = $valorChave;
					$retorno['NOMECHAVE'] = $nomeChave;	
					updateBlob($dadosBlob,$dadosInput['tab'],$retorno['NOMECHAVE'],$retorno['CHAVE']);					
				}else{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = jn_utf8_encode(erroSql(jn_GetErroSql()));
				}
			
			}
			
			depoisGravacao($dadosInput['acao'],$dadosInput['tab'],$dadosInput['tabelaOriginal'],$retorno['CHAVE'],$retorno['NOMECHAVE'],$dadosInput['campos'],$retorno);
			
		}
	}
	
	
}else if($dadosInput['tipo'] =='excluir'){
	$permissoes = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$_SESSION['perfilOperador'],$dadosInput['tab'],$dadosInput['chave'],$dadosInput['tabelaOriginal']);
	
	if(!$permissoes['EXC']){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Você nao tem permissão de Exclusão na tabela :'.$dadosInput['tab'];
	
	}else{ 
		antesGravacao('EXC',$dadosInput['tab'],$dadosInput['tabelaOriginal'],$dadosInput['chave'],$dadosInput['nomeChave'],$dados,$retorno);
		
		if($retorno['STATUS']=='OK'){	
			$retorno = excluirRegistro($dadosInput['tab'],$dadosInput['chave'],$dadosInput['nomeChave'],$dadosInput['reativar']);
		}
		
		depoisGravacao('EXC',$dadosInput['tab'],$dadosInput['tabelaOriginal'],$dadosInput['chave'],$dadosInput['nomeChave'],$dados,$retorno);
	}

}else if($dadosInput['tipo'] =='saidaCampo'){

	$retorno = saidaCampo($dadosInput['tipoFormulario'],$dadosInput['tabela'],$dadosInput['campo'],$dadosInput['valor'],$dadosInput['camposForm']);

}else if($dadosInput['tipo'] =='buscarTelefone'){
		
	$queryTel  = ' SELECT FIRST 1 CODIGO_AREA, NUMERO_TELEFONE, NUMERO_REGISTRO FROM PS1006 ';
	$queryTel .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($dadosInput['codigoAssociado']);
	$queryTel .= ' ORDER BY INDICE_TELEFONE, NUMERO_REGISTRO ';
	$resTel = jn_query($queryTel);
	$rowTel = jn_fetch_object($resTel);
	
	$retorno['NUMERO_TELEFONE'] = '(' . $rowTel->CODIGO_AREA . ')' . $rowTel->NUMERO_TELEFONE;
	$retorno['NUMERO_REGISTRO'] = $rowTel->NUMERO_REGISTRO;

}else if($dadosInput['tipo'] =='salvarTelefone'){
	$codArea = substr($dadosInput['numeroTelefone'], 0,2);
	$telefone = substr($dadosInput['numeroTelefone'], 2,12);
	
	if($dadosInput['numeroRegistro'] != ''){
		jn_query('UPDATE PS1006 SET CODIGO_AREA = ' . aspas($codArea) . ',' . 
								' 	NUMERO_TELEFONE = ' . aspas($telefone) . 
					' WHERE NUMERO_REGISTRO = ' . aspas($dadosInput['numeroRegistro']) . 
					' AND CODIGO_ASSOCIADO = ' . aspas($dadosInput['codigoAssociado'])
				);
	}else{
		jn_query('INSERT INTO PS1006 (CODIGO_ASSOCIADO, CODIGO_AREA, NUMERO_TELEFONE) VALUES ( ' . 
								 aspas($dadosInput['codigoAssociado']) . ',' . 
								 aspas($codArea) . ',' . 
								 aspas($telefone) . ' )');
	}
}
else if($dadosInput['tipo'] =='antesDeIncluirAlterarExcluir')
{

	$retorno = antesDeIncluirAlterarExcluir($dadosInput['acao'],$dadosInput['tabela'],$dadosInput['tabelaOriginal'],$dadosInput['chave'],
		                                    $dadosInput['nomeChave'], $dadosInput['parametroPrompt']);

}
else if($dadosInput['tipo'] =='botoesOpcoesAdicionaisEspecificas')
{

	$retorno = botoesOpcoesAdicionaisEspecificas($dadosInput['tabela'],$dadosInput['tabelaOriginal'],
										         $dadosInput['chave'], $dadosInput['nomeChave']);

}
else if($dadosInput['tipo'] =='solicitaParametroInicial')
{
			
	if (($dadosInput['tabela']=='PS1001') and ($dadosInput['acao']=='INC'))
	{	
		$retorno['TITULO']            = 'Busque pelo CEP';
		$retorno['MSG']               = 'Informe o número do CEP para busca do endereço';
		$retorno['VLDEFAULT']         = '';
		$retorno['OBRIGATORIO']       = 'NAO';
		$retorno['PARAMETRO_VALIDAR'] = 'CEP';
		$retorno['ABRIR_PROMPT']      = 'SIM';
	}
	else
	{
		$retorno['ABRIR_PROMPT']      = 'NAO';
	}

}


echo json_encode($retorno);


function geraChave($tabela, &$dados){
	$retorno = false;//Falso se for autoincrement
		
		
	for($i=0;$i<Count($dados);$i++){
		if($dados[$i]['CHAVE'] == 'S'){
			
			if($dados[$i]['VALOR']!=''){
				$retorno = true;
				break;
			}
			
			$queryChave = "Select TIPO_CHAVE_AUTOMATICA FROM CFGCAMPOS_SIS_CD where nome_tabela = ".aspas(strtoupper($tabela))." and nome_campo =". aspas($dados[$i]['CAMPO']);
			$resChave = jn_query($queryChave);
			$rowChave = jn_fetch_object($resChave);
			
			
			if($rowChave->TIPO_CHAVE_AUTOMATICA == 1){
				break;
			}if($rowChave->TIPO_CHAVE_AUTOMATICA == 2){
				$dados[$i]['VALOR'] = jn_gerasequencial($tabela);
			}
			$retorno = true;
			break;
			
		}
	}
		
		
	return $retorno;
}


function updateBlob($campos,$tabela,$nomeChave,$chave){
	foreach ($campos as $campo){
		do {
			$auxValor = substr($campo['VALOR'],0,30000);
			$campo['VALOR'] = substr($campo['VALOR'],30000,strlen($campo['VALOR'])-30000);
			
			if($_SESSION['type_db'] == 'sqlsrv'){
				$sql = 'UPDATE '.$tabela.' SET '.$campo['CAMPO'].'= CONCAT('.$campo['CAMPO'].','.aspasNull($auxValor).') WHERE '.$nomeChave.'='.aspas($chave);
			}else{
				$sql = 'UPDATE '.$tabela.' SET '.$campo['CAMPO'].'='.$campo['CAMPO'].'||'.aspasNull($auxValor).' WHERE '.$nomeChave.'='.aspas($chave);				
			}
			
			$resUpdate = jn_query($sql);
			
		} while (strlen($campo['VALOR'])>0);
	}

}




function trataCampoEspecialSubCadastro($dadosInput,&$itemCampos)
{

	if ($dadosInput['tab']=='PS1000')
	{
		if($itemCampos['NOME_CAMPO']=='TIPO_ASSOCIADO')
		{
			$itemCampos['VALOR'] = 'D';
		}
		else if ($itemCampos['NOME_CAMPO']=='CODIGO_EMPRESA')
		{
			$itemCampos['COMPORTAMENTO'] = '3';
		}
	}

}





