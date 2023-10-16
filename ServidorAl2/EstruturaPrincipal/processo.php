<?php
require('../lib/base.php');

require('../private/autentica.php');
require('../EstruturaEspecifica/valorPadraoProcesso.php');
require('../EstruturaEspecifica/saidaCampoPD.php');


if($dadosInput['tipo'] =='dados'){
		
		$queryTabela = "Select COALESCE(MENSAGEM_ALERTA,'')MENSAGEM_ALERTA,COALESCE(MENSAGEM_CONFIRMA,'') MENSAGEM_CONFIRMA, cfgprocessos_pd.* from  cfgprocessos_pd where numero_registro =".aspas($dadosInput['reg']);
		
		$resTabela = jn_query($queryTabela);
		
		if($rowTabela = jn_fetch_object($resTabela)){
			$retorno['NOME_PROCESSO']   = jn_utf8_encode($rowTabela->NOME_PROCESSO);
			$retorno['DESCRICAO_AJUDA'] = jn_utf8_encode($rowTabela->DESCRICAO_AJUDA);
			$retorno['MENSAGEM_ALERTA'] = jn_utf8_encode($rowTabela->MENSAGEM_ALERTA);
			$retorno['MENSAGEM_CONFIRMA'] = jn_utf8_encode($rowTabela->MENSAGEM_CONFIRMA);
			$retorno['TIPO_PROCESSO'] = jn_utf8_encode($rowTabela->TIPO_PROCESSO);
			$retorno['DESTINO_PROCESSO'] = jn_utf8_encode($rowTabela->DESTINO_PROCESSO);
			$retorno['BOTAO_IMPRIMIR'] = jn_utf8_encode($rowTabela->BOTAO_IMPRIMIR);
			$retorno['AUX_PADRAO'] = jn_utf8_encode($rowTabela->AUX_PADRAO);
			$retorno['TEMPO_VERIFICACAO'] = jn_utf8_encode($rowTabela->TEMPO_VERIFICACAO);


			if($rowTabela->FLAG_2COLUNAS_AL2 == 'S'){
				$retorno['TAMANHO_FLEX'] = '49';
			}else{
				$retorno['TAMANHO_FLEX'] = '100';
			}
			
		}
	
		if(($rowTabela->PERMISSAO_TABELA!='')and($rowTabela->PERMISSAO_NIVEL!='')){
			permissaoPx($rowTabela->PERMISSAO_TABELA,$rowTabela->PERMISSAO_NIVEL,true);
		}
			
		
		$queryGrupos = 'SELECT PASTA_APRESENTACAO FROM cfgcampos_pd WHERE numero_registro_processo = '.aspas($dadosInput['reg']).' GROUP BY pasta_apresentacao ORDER BY pasta_apresentacao ';
		$resGrupos = jn_query($queryGrupos);
		//echo $queryGrupos;
		while($rowGrupos = jn_fetch_object($resGrupos)){
			
			$campos = null;
			$queryCampos = 'SELECT * FROM cfgcampos_pd WHERE numero_registro_processo = '.aspas($dadosInput['reg']).
			               ' and pasta_apresentacao = '.aspas($rowGrupos->PASTA_APRESENTACAO).' ORDER by numero_ordem_criacao ';
			//print_r($rowGrupos);
			$resCampos = jn_query($queryCampos);
			//echo $queryCampos;
			
			$zebrado = true;
			
			while($rowCampos = jn_fetch_object($resCampos)){
				
				$itemCampos = null;
				$itemCampos['NOME_CAMPO']  = jn_utf8_encode($rowCampos->NOME_CAMPO); 
				$itemCampos['LABEL']       = jn_utf8_encode($rowCampos->LABEL_CAMPO); 
				$itemCampos['TIPO']        = jn_utf8_encode(strtoupper($rowCampos->COMPONENTE_FORMULARIO));
				$itemCampos['HINT']        = jn_utf8_encode($rowCampos->HINT_EXPLICACAO);
				$itemCampos['VALOR']       = ''; // auto VALOR: { 'VALOR': '2', 'DESC': 'AAACC' }
				
				
				$itemCampos['CLASS']       = jn_utf8_encode($rowCampos->CLASSE_CAMPO);
				
				if(trim($itemCampos['CLASS'])=='')
					$itemCampos['CLASS']       = '100%';
				
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
				
				$itemCampos['COMPORTAMENTO']  = jn_utf8_encode($rowCampos->COMPORTAMENTO_FRM_EDICAO);
				$itemCampos['CHAVE']  = jn_utf8_encode($rowCampos->FLAG_CHAVEPRIMARIA);
				$itemCampos['LINK_ADD_AUTO']  = jn_utf8_encode($rowCampos->LINK_ADD_AUTO);
					
				
					
				$itemCampos['VALOR'] = (valorPadraoProcesso($retorno['AUX_PADRAO'],$itemCampos['NOME_CAMPO'],$rowCampos->VALOR_PADRAO));

				$itemCampos['SAIDA_CAMPO']  = json_decode($rowCampos->SAIDA_CAMPO);
				if($itemCampos['SAIDA_CAMPO']== null)
					$itemCampos['SAIDA_CAMPO'] = '';

				$itemCampos['ENTRADA_CAMPO']  = json_decode($rowCampos->ENTRADA_CAMPO);
				if($itemCampos['ENTRADA_CAMPO']== null)
					$itemCampos['ENTRADA_CAMPO'] = '';
				
					
					
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
				
		
				
				
				if (($itemCampos['TIPO'] == 'AUTOCOMPLETE')and($itemCampos['VALOR']!='')and($rowCampos->CAMPO_ID_TABELA_RELAC!='')and
			        ($rowCampos->CAMPO_PESQUISA_TABELA_RELAC!='')and($rowCampos->NOME_TABELA_RELACIONADA!=''))
				{
						$queryAuto = 'select '.($rowCampos->CAMPO_ID_TABELA_RELAC).' AS VALOR, '.($rowCampos->CAMPO_PESQUISA_TABELA_RELAC).' AS DESCR from '.($rowCampos->NOME_TABELA_RELACIONADA).' where '.($rowCampos->CAMPO_ID_TABELA_RELAC).' = '.aspas($itemCampos['VALOR']);
	
						$resAuto = jn_query($queryAuto);

							//echo $queryColunas;
							
						if($rowAuto = jn_fetch_object($resAuto)){
							$itemCampos['VALOR'] = array();
							$itemCampos['VALOR']['VALOR'] = jn_utf8_encode($rowAuto->VALOR);
							$itemCampos['VALOR']['DESC']  = jn_utf8_encode($rowAuto->DESCR);
							
						}
				}
				else if($itemCampos['TIPO']=='GROUPCHECKBOX')
				{
					$dado = valorOpcoesComboProcesso($retorno['AUX_PADRAO'],$itemCampos['NOME_CAMPO'],$rowCampos->OPCOES_COMBO);
					$dado = explode(';',$dado);
					$linhas = array();
					foreach ($dado as $value){
						$linha['VALOR'] = $value;
						$linha['CHECKED'] = 'N';
						if($itemCampos['VALOR']!=''){
							$pos = strpos(';'.$itemCampos['VALOR'].';', ';'.$linha['VALOR'].';');
							if ($pos === false) {
								$linha['CHECKED'] = 'N';
							}else{
								$linha['CHECKED'] = 'S';
							}
						}
						$linha['VALOR'] = jn_utf8_encode($linha['VALOR']);
						
						$linhas[] = $linha; 
					}
					$itemCampos['VALOR'] = jn_utf8_encode($itemCampos['VALOR']);
					$itemCampos['GRUPO_DADOS'] = $linhas;
				
				}else if(($itemCampos['TIPO'] == 'COMBOBOX')or($itemCampos['TIPO'] == 'RADIO')){
					$dado = valorOpcoesComboProcesso($retorno['AUX_PADRAO'],$itemCampos['NOME_CAMPO'],$rowCampos->OPCOES_COMBO);
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
					
					$itemCampos['VALOR'] = jn_utf8_encode($itemCampos['VALOR']);
					$itemCampos['GRUPO_DADOS'] = $linhas;
				}else{
					$itemCampos['VALOR'] = jn_utf8_encode($itemCampos['VALOR']);
				}
				
				
				if($itemCampos['TIPO'] !='DIVISORIA'){
					
					if($zebrado){
						$itemCampos['ZEBRA']  = true;
					}else{
						$itemCampos['ZEBRA']  = false;
					}
					
					$zebrado = !$zebrado;
					
					$auxLabel = explode('|',$itemCampos['LABEL'] );
					if(count($auxLabel)>1){
						$nomeOriginal = $itemCampos['NOME_CAMPO'];
						$itemCampos['NOME_CAMPO']  = $nomeOriginal.'_1'; 
						$itemCampos['LABEL']       = $auxLabel[0]; 
						$itemCampos['GRUPO']       = true;
						$itemCampos['INDICEGRUPO'] = 1;
						$auxPadrao = explode('|',$itemCampos['VALOR']);
						$itemCampos['VALOR'] = $auxPadrao[0];
						$campos[] = $itemCampos; 
						
						$itemCampos['NOME_CAMPO']  = $nomeOriginal.'_2'; 
						$itemCampos['LABEL']       = $auxLabel[1]; 
						$itemCampos['GRUPO']       = true;
						$itemCampos['INDICEGRUPO'] = 2;
						if(count($auxPadrao)>1){
							$itemCampos['VALOR'] = $auxPadrao[1];
						}
					}
				}
				

				
				
				$campos[] = $itemCampos; 
				
			}
			
			$dadosGrupo =  explode('-',$rowGrupos->PASTA_APRESENTACAO);
			$grupo['INDICE'] = jn_utf8_encode(trim($dadosGrupo[0]));
			$grupo['DESC']   = jn_utf8_encode(trim($dadosGrupo[1]));
			$grupo['CAMPOS'] = $campos;
			$retorno['GRUPOS'][] = $grupo;
			
			
	}
	
	echo json_encode($retorno);
}
else if($dadosInput['tipo'] =='saidaCampo')
{

	$retorno = saidaCampoPD($dadosInput['idProcesso'],$dadosInput['nomeProcesso'],$dadosInput['campo'],$dadosInput['valor'],$dadosInput['camposForm']);
	echo json_encode($retorno);
}


else if($dadosInput['tipo'] =='consultaResultado')
{


	if ($dadosInput['tipoDisparoProcesso'] == 'RELATORIO_DINAMICO')
	{
		 $rowProcesso = qryUmRegistro('select "Ok relatorio gerado" RESULTADO_PROCESSO, ' . 
		 	                                  aspas($dadosInput['arquivoGerado']) . ' Arquivo_Relatorio_processo, 
			 	                              "Relatorio dinamico" nome_processo 
										      from Cfg0001');
	}
	else
	{
		$tentativas = 1;

		while ($tentativas <= 3)
		{
			 $rowProcesso = qryUmRegistro('select CFGDISPAROPROCESSOSCABECALHO.RESULTADO_PROCESSO, CFGDISPAROPROCESSOSCABECALHO.Arquivo_Relatorio_processo, 
			 	                           cfgprocessos_pd.nome_processo 
										   from CFGDISPAROPROCESSOSCABECALHO 
										   inner join cfgprocessos_pd on (CFGDISPAROPROCESSOSCABECALHO.identificacao_processo = cfgprocessos_pd.numero_registro)
			 	                           where CFGDISPAROPROCESSOSCABECALHO.NUMERO_REGISTRO_PROCESSO = ' . aspas($dadosInput['numeroRegistroProcesso']));

			 if ($rowProcesso->RESULTADO_PROCESSO!='')
			 {
			 	break;
			 }
			 //sleep(10);	 
			 $tentativas++;
		}
	}	

	if ($rowProcesso->ARQUIVO_RELATORIO_PROCESSO!='')
	{

		if (strpos(strtoupper($rowProcesso->ARQUIVO_RELATORIO_PROCESSO),'TXT') >= 1)
			$tipoExibicao = 'TXT';
		else if (strpos(strtoupper($rowProcesso->ARQUIVO_RELATORIO_PROCESSO),'XLS') >= 1)
			$tipoExibicao = 'EXCEL';
		else if (strpos(strtoupper($rowProcesso->ARQUIVO_RELATORIO_PROCESSO),'PDF') >= 1)
			$tipoExibicao = 'PDF';
		else if (strpos(strtoupper($rowProcesso->ARQUIVO_RELATORIO_PROCESSO),'CSV') >= 1)
			$tipoExibicao = 'CSV';
		else if (strpos(strtoupper($rowProcesso->ARQUIVO_RELATORIO_PROCESSO),'JSON') >= 1)
			$tipoExibicao = 'JSON';
		else
			$tipoExibicao = 'HTML';

		$nomeCaminhoArquivo = $rowProcesso->ARQUIVO_RELATORIO_PROCESSO;

		if (($tipoExibicao=='TXT')||($tipoExibicao=='HTML'))
		{
			$arquivo       = fopen($nomeCaminhoArquivo, "r"); 
			$conteudo      = '';
			$qtLinhas      = 0;

			while (($linha = fgets($arquivo)) !== false) 
			{
				$conteudo .= $linha;
				$qtLinhas ++;

				if ($qtLinhas >= 79999)
				{
					$conteudo = 'ERRO_EXCESSO_QT_LINHAS';
					break;
				}
			}

			fclose($arquivo);
		}
	}

	$retorno['NOME_PROCESSO']   = jn_utf8_encode($rowProcesso->NOME_PROCESSO);
	$retorno['MSG_RESULTADO']   = jn_utf8_encode($rowProcesso->RESULTADO_PROCESSO);
	$retorno['ARQ_RELATORIO']   = jn_utf8_encode($rowProcesso->ARQUIVO_RELATORIO_PROCESSO);
	$retorno['HTML_RELATORIO']  = jn_utf8_encode($conteudo);
	$retorno['TIPO_EXIBICAO']   = $tipoExibicao;

	echo json_encode($retorno);
}






?>