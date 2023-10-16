<?php

			
function antesMostrarCampo($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,&$campo, $campos = null, $parametroPrompt = '', $rowDadoSub = null){
	global $dadosInput;
	$tabela = strtoupper($tabela);
	
	// Vou tratar no formulário, se o retorno for "IGNORAR_CAMPO", forço subir o loop do formulário sem adicionar o campo. 
	//Isto para que quando for apenas um campo comportamento 3 na pasta, a pasta nem seja criada.
	$retorno = 'PADRAO';


	if ($_SESSION['AliancaPx4Net']=='S') // Se for o ERP
	{

		require_once('../EstruturaPx/antesMostrarCampos_ERPPx.php');
		$retorno = antesMostrarCampo_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,$campo, $campos, $parametroPrompt, $rowDadoSub);

	}

	/* ------------------------------------------------------------------------------------------------------------- */
	else // se for o portal (não o ERP)
	/* ------------------------------------------------------------------------------------------------------------- */

	{

			if($tipo=='INC')
			{

				if(($tabela=='OP_SERVICOS_ACOLHIMENTO')and($campo['NOME_CAMPO']=='CODIGO_ENTIDADE_MANTENEDORA'))
				{ 
					
					$sql = "select codigos_mantenedor from cfgusuario_perfil_cd where codigo_pessoa_diversa = ".aspas($_SESSION['codigoIdentificacao']);
					//$sql = " and codigo_entidade_mantenedora = 1 "; 
					$res = jn_query($sql);
					if($row = jn_fetch_object($res)){
						if(trim($row->codigos_mantenedor) != ''){
							$quantidade = count(explode(',',trim($row->codigos_mantenedor))); 
							if($quantidade == 1){
								$campo['VALOR'] =  trim($row->codigos_mantenedor);
								$campo['COMPORTAMENTO'] = 2;
							}
						}
					}
				}

				
				if ($tabela == 'ESP_PRORROGACAO_INTERNACAO' && $_SESSION['perfilOperador'] == 'PRESTADOR'){ 
					if($campo['NOME_CAMPO'] == 'CODIGO_OPERADOR'){				
						$campo['VALOR'] = $_SESSION['codigoIdentificacao'];
						$campo['COMPORTAMENTO'] = 2;									
					}
				}
				
				if ($tabela == 'ESP_PLANO_TRATAMENTO' && $_SESSION['perfilOperador'] == 'PRESTADOR'){ 
					if($campo['NOME_CAMPO'] == 'CODIGO_PRESTADOR'){				
						$campo['VALOR'] = $_SESSION['codigoIdentificacao'];
						$campo['COMPORTAMENTO'] = 2;									
					}
				}
				
				if($tabela == 'PS1100' && $_SESSION['perfilOperador'] == 'CORRETOR'){
					if($campo['NOME_CAMPO'] == 'CODIGO_ID_SUPERIOR'){				
						$campo['VALOR'] = $_SESSION['codigoIdentificacao'];
						$campo['COMPORTAMENTO'] = 2;									
					}
					
					if($campo['NOME_CAMPO'] == 'TIPO_CADASTRO'){
						$campo['VALOR'] = 'Cadastro_Vendedores';
					}
				}
				
				if($tabela == 'PS6110' && $_SESSION['perfilOperador'] == 'BENEFICIARIO'){
					if($campo['NOME_CAMPO'] == 'CODIGO_ASSOCIADO'){
						
						$sql = "SELECT CODIGO_ASSOCIADO, NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_TITULAR = ".aspas($_SESSION['codigoIdentificacao']);				
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_ASSOCIADO . ' - ' . $row->NOME_ASSOCIADO;					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}
				
				if($tabela == 'PS6120' && $_SESSION['perfilOperador'] == 'BENEFICIARIO'){			
					if($campo['NOME_CAMPO'] == 'NOME_PESSOA'){
						
						$sql = "SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_TITULAR = ".aspas($_SESSION['codigoIdentificacao']);				
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->NOME_ASSOCIADO;					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
					
					if($campo['NOME_CAMPO'] == 'RETORNO_OPERADORA'){
						$campo['COMPORTAMENTO'] = 3;
					}
					
				}
							
				if($tabela == 'ESP_AGENDAMENTO_CIRURGICO' && $_SESSION['perfilOperador'] == 'OPERADOR'){	
					
					if($campo['NOME_CAMPO'] == 'CODIGO_OPERADOR_CAD'){
						$campo['VALOR'] = $_SESSION['codigoIdentificacao'];
					}

					if($campo['NOME_CAMPO'] == 'PROTOCOLO_GERAL_PS6450'){
						$campo['COMPORTAMENTO'] = 3;
					}
					
					if($campo['NOME_CAMPO'] == 'CODIGO_AUDITOR'){
						$campo['COMPORTAMENTO'] = 3;
					}

					if($campo['NOME_CAMPO'] == 'STATUS_AGENDAMENTO'){
						$campo['VALOR'] = 'PE';
					}
				}
				
				if($_SESSION['codigoSmart'] == '3419'){
					if($tabela == 'PS6110' && $_SESSION['perfilOperador'] == 'BENEFICIARIO'){
						if($campo['NOME_CAMPO'] == 'CODIGO_RECLAMACAO_SUGESTAO'){
							
							$sql = "SELECT CODIGO_RECLAMACAO_SUGESTAO, DESCRICAO_RECLAMACAO_SUGESTAO FROM PS6100 WHERE CODIGO_RECLAMACAO_SUGESTAO in (1091, 1094 , 1090 )";				
							$res = jn_query($sql);
							$opcoesCombo = '';
							while($row = jn_fetch_object($res)){
								if($opcoesCombo != '')
										$opcoesCombo .= ';';
								
								$opcoesCombo .= $row->CODIGO_RECLAMACAO_SUGESTAO . ' - ' . $row->DESCRICAO_RECLAMACAO_SUGESTAO;					
							}
							
							$campo['TIPO'] =  'COMBOBOX';
							$campo['OPCOES_COMBO'] =  $opcoesCombo;				
						}
					}
				}
			}
				
			if($tabela=='VND1000_ON'){
				$tipoAssociado = '';		
				$codAssociado = '';		
				$codTitular = '';		
				$indiceTipoAssociado = 0;
				$indiceCodigoParentesco = 0;
				
				for($i=0;$i<count($campos);$i++){

					if($campos[$i]['NOME_CAMPO']=='TIPO_ASSOCIADO'){
						$tipoAssociado = $campos[$i]['VALOR'];
					}

					if($campos[$i]['NOME_CAMPO']=='CODIGO_TITULAR'){
						$codTitular = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['NOME_CAMPO']=='CODIGO_ASSOCIADO'){
						$codAssociado = $campos[$i]['VALOR'];
					}	

					if($campos[$i]['NOME_CAMPO']=='TIPO_ASSOCIADO'){
						$indiceTipoAssociado = $i;
					}	

					if($campos[$i]['NOME_CAMPO']=='CODIGO_PARENTESCO'){
						$indiceCodigoParentesco = $i;
					}	
				}
				
				if($tipo=='INC'){
					if($_SESSION['codigoSmart'] == '4246'){ //MV2C
						if($campos[$i]['CAMPO']=='DATA_ADMISSAO'){
							$dataAdmissao = '';
							if(date('d') <= 10){
								$dataAdmissao = date('01/m/Y', strtotime('+1 month'));
							}else{
								$dataAdmissao = date('01/m/Y', strtotime('+2 month'));
							}

							$campos[$i]['VALOR'] = $dataAdmissao;
						}
					}
				}
				
				if(($_SESSION['codigoSmart'] == '4206')){//VIXMED
					if($campo['NOME_CAMPO']=='FORMA_PAGAMENTO'){
						for($i=0;$i<count($dadosInput['filtros']);$i++){
							if(($dadosInput['filtros'][$i]['CAMPO']=='TIPO_ASSOCIADO')and ($dadosInput['filtros'][$i]['VALOR']=='D')){
								$campo['COMPORTAMENTO'] = 3;
							}
						}
						
					}	
				}
				
				if(($campo['NOME_CAMPO']=='VALOR_TAXA_ADESAO') and ($tipoAssociado == 'D')){
					$campo['COMPORTAMENTO'] = 3;
				}							
			
				if(($_SESSION['codigoSmart'] == '4206')){//VIXMED
					if($campo['NOME_CAMPO']=='FORMA_PAGAMENTO'){
						for($i=0;$i<count($dadosInput['filtros']);$i++){
							if(($dadosInput['filtros'][$i]['CAMPO']=='TIPO_ASSOCIADO')and ($dadosInput['filtros'][$i]['VALOR']=='D')){
								$campo['COMPORTAMENTO'] = 3;
							}
						}
						
					}	
				}
				
				if(($campo['NOME_CAMPO']=='CODIGO_PARENTESCO') and ($codAssociado == $codTitular)  and ($codAssociado != '')){
					$query = 'SELECT FIRST 1 CODIGO_PARENTESCO FROM PS1045 WHERE DATA_INUTILIZ_REGISTRO IS NULL AND TIPO_RELACAO_DEPENDENCIA = "T"';
					
					$res = jn_query($query);
					if($row = jn_fetch_object($res)){
						$campo['COMPORTAMENTO'] =  '3';
						$campo['TIPO'] =  'TEXT';
						$campo['VALOR'] = $row->CODIGO_PARENTESCO;
					}
				}
				
				if($tipo=='INC' && $_SESSION['codigoSmart'] == '4246'){//MV2C	
					$dataAdmissao = '';
					if($campo['NOME_CAMPO'] == 'DATA_ADMISSAO'){
						if(date('d') <= 10){
							$dataAdmissao = date('Y-m-01', strtotime(date('Y-m-01').'+1 month'));
						}else{
							$dataAdmissao = date('Y-m-01', strtotime(date('Y-m-01').'+2 month'));
						}
						$campo['VALOR'] = $dataAdmissao;
					}
				}
				
				if(($tipo=='INC')and($_SESSION['codigoSmart'] == '4206')){
					
					if(($campo['NOME_CAMPO']=='CODIGO_GRUPO_PESSOAS')or($campo['NOME_CAMPO']=='CODIGO_VND1030CONFIG')){
						$tipoAssociadoFiltro = '';
						$codigoTitularFiltro = '';
						for($i=0;$i<count($dadosInput['filtros']);$i++){
							if($dadosInput['filtros'][$i]['CAMPO']=='TIPO_ASSOCIADO'){
								$tipoAssociadoFiltro =  $dadosInput['filtros'][$i]['VALOR'];
							}
							if($dadosInput['filtros'][$i]['CAMPO']=='CODIGO_TITULAR'){
								$codigoTitularFiltro =  $dadosInput['filtros'][$i]['VALOR'];
							}
						}
						if($tipoAssociadoFiltro=='D'){
							if($campo['NOME_CAMPO']=='CODIGO_GRUPO_PESSOAS'){
								$query = 'SELECT CODIGO_GRUPO_PESSOAS from VND1000_ON where codigo_Associado ='.aspas($codigoTitularFiltro);
								$res = jn_query($query);
								$row = jn_fetch_object($res);
								$campo['VALOR'] = $row->CODIGO_GRUPO_PESSOAS;
							}
							if($campo['NOME_CAMPO']=='CODIGO_VND1030CONFIG'){
								$query = 'SELECT CODIGO_VND1030CONFIG from VND1000_ON where codigo_Associado ='.aspas($codigoTitularFiltro);
								$res = jn_query($query);
								$row = jn_fetch_object($res);
								$campo['VALOR'] = $row->CODIGO_VND1030CONFIG;
								
							}
						}
					}
					
					
				}
				
				if(($tipo=='INC')and($_SESSION['codigoSmart'] == '3389')){//Vidamax
						
					if($campo['NOME_CAMPO']=='DATA_ADMISSAO'){
						$tipoAssociadoFiltro = '';
						$codigoTitularFiltro = '';
						for($i=0;$i<count($dadosInput['filtros']);$i++){
							if($dadosInput['filtros'][$i]['CAMPO']=='TIPO_ASSOCIADO'){
								$tipoAssociadoFiltro =  $dadosInput['filtros'][$i]['VALOR'];
							}
							if($dadosInput['filtros'][$i]['CAMPO']=='CODIGO_TITULAR'){
								$codigoTitularFiltro =  $dadosInput['filtros'][$i]['VALOR'];
							}
						}
									
						if($tipoAssociadoFiltro == 'D'){
							$query = 'SELECT DATA_ADMISSAO FROM VND1000_ON WHERE CODIGO_ASSOCIADO ='.aspas($codigoTitularFiltro);
							$res = jn_query($query);
							$row = jn_fetch_object($res);
							$data = SqlToData($row->DATA_ADMISSAO);

							$campo['VALOR'] = $data;
							$campo['TIPO'] = 'TEXT';
							$campo['COMPORTAMENTO'] = 3;
						}			
					}
				}

				if($tabelaOrigem == 'VW_VND1000_CAAPSML'){
					$codigoTitularFiltro = '';
					for($i=0;$i<count($dadosInput['filtros']);$i++){				
						if($dadosInput['filtros'][$i]['CAMPO']=='CODIGO_TITULAR'){
							$codigoTitularFiltro =  $dadosInput['filtros'][$i]['VALOR'];
						}
					}									

					if($campo['NOME_CAMPO']=='CODIGO_GRUPO_CONTRATO'){
						$campo['VALOR'] = '28';
					}

					if($campo['NOME_CAMPO']=='VALOR_TAXA_ADESAO'){
						$campo['COMPORTAMENTO'] = 3;
					}

					if($tipo == 'INC' and $campo['NOME_CAMPO']=='TIPO_ASSOCIADO'){
						$campo['VALOR'] = 'D';
						$campo['COMPORTAMENTO'] = 2;
					}

					if($campo['NOME_CAMPO']=='CODIGO_PLANO'){				
						$campo['COMPORTAMENTO'] = 3;
					}

					if($campo['NOME_CAMPO']=='CODIGO_GRUPO_CONTRATO'){				
						$campo['COMPORTAMENTO'] = 3;
					}

					if($campo['NOME_CAMPO']=='FLAG_PORTABILIDADE'){				
						$campo['COMPORTAMENTO'] = 3;
					}

					if($campo['NOME_CAMPO']=='NUMERO_PIS'){				
						$campo['COMPORTAMENTO'] = 3;
					}
					
					if($campo['NOME_CAMPO']=='CODIGO_CNS'){				
						$campo['COMPORTAMENTO'] = 3;
					}

					if($campo['NOME_CAMPO']=='NUMERO_DECLARACAO_NASC_VIVO'){				
						$campo['COMPORTAMENTO'] = 3;
					}

					if($campo['NOME_CAMPO']=='DESCRICAO_OBSERVACAO'){				
						$campo['COMPORTAMENTO'] = 2;
					}

					if($campo['NOME_CAMPO']=='NUMERO_CPF'){				
						$campo['OBRIGATORIO'] = true;
					}

					if($tipo == 'INC' && $campo['NOME_CAMPO']=='DATA_ADMISSAO'){												
						$campo['VALOR'] = '2022-08-01';
						$campo['COMPORTAMENTO'] = 2;
					}

					if($campo['NOME_CAMPO']=='CODIGO_PAIS_EMISSOR'){												
						$campo['VALOR'] = '32';
						$campo['COMPORTAMENTO'] = 2;
					}
					
					if($tipo == 'INC' && $campo['TIPO']=='ANEXO'){
						$campo['OBRIGATORIO'] = true;
						$campo['LABEL'] = 'Insira documento comprobatório: RG, CPF, Certidão de Nascimento ou Casamento.';
					}

					if($campo['NOME_CAMPO']=='CODIGO_ESTADO_CIVIL'){
						$queryEstCivil  = ' SELECT CODIGO_ESTADO_CIVIL, NOME_ESTADO_CIVIL FROM PS1044  ';								
						$queryEstCivil .= ' ORDER BY NOME_ESTADO_CIVIL ';				
						$resEstCivil = jn_query($queryEstCivil);
						$opcoesCombo = '';
						while($rowEstCivil = jn_fetch_object($resEstCivil)){					
							$opcoesCombo .= ';';					
							$opcoesCombo .= $rowEstCivil->CODIGO_ESTADO_CIVIL . ' - ' . jn_utf8_encode($rowEstCivil->NOME_ESTADO_CIVIL);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}

					if($campo['NOME_CAMPO']=='CODIGO_PARENTESCO'){
						$queryParentesco  = ' SELECT CODIGO_PARENTESCO, NOME_PARENTESCO FROM PS1045  ';								
						$queryParentesco .= ' WHERE CODIGO_PARENTESCO IN (0, 1,2,3,4,6,7,11,12) ';				
						$queryParentesco .= ' ORDER BY NOME_PARENTESCO ';				
						$resParentesco = jn_query($queryParentesco);
						$opcoesCombo = '';
						while($rowParentesco = jn_fetch_object($resParentesco)){					
							$opcoesCombo .= ';';					
							$opcoesCombo .= $rowParentesco->CODIGO_PARENTESCO . ' - ' . jn_utf8_encode($rowParentesco->NOME_PARENTESCO);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}

					if($tipo == 'INC' and $campo['NOME_CAMPO']=='CODIGO_ASSOCIADO'){				
						$queryDep = 'SELECT count(*) AS CODIGO_DEP FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codigoTitularFiltro);
						$resDep = jn_query($queryDep);
						$rowDep = jn_fetch_object($resDep);	
						
						$codigoDep = explode('.',$codigoTitularFiltro);
						$codigoDep = $codigoDep[0] . '.' . $rowDep->CODIGO_DEP;

						$campo['COMPORTAMENTO'] = '2';
						$campo['VALOR'] = $codigoDep;

					}
				}

				if(retornaValorConfiguracao('CODIGO_PLANO_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_PLANO'){
						
						$sql  = ' SELECT CODIGO_PLANO, NOME_PLANO_FAMILIARES ';
						$sql .= ' FROM VW_PLANOS_VND_NET2';				
						$sql .= ' ORDER BY NOME_PLANO_FAMILIARES ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_PLANO . ' - ' . jn_utf8_encode($row->NOME_PLANO_FAMILIARES);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}

				if(retornaValorConfiguracao('CODIGO_ENTIDADE_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_GRUPO_CONTRATO'){
						
						$sql  = ' SELECT CODIGO_GRUPO_CONTRATO, DESCRICAO_GRUPO_CONTRATO ';
						$sql .= ' FROM ESP0002';				
						$sql .= ' ORDER BY DESCRICAO_GRUPO_CONTRATO ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_GRUPO_CONTRATO . ' - ' . jn_utf8_encode($row->DESCRICAO_GRUPO_CONTRATO);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}

				if($campo['NOME_CAMPO']=='TIPO_ASSOCIADO' and $codAssociado == $codTitular){			
					$campo[$indiceTipoAssociado]['VALOR'] =  'T';
					$campo[$indiceTipoAssociado]['TIPO'] =  'TEXT';
				}
			}
			
			
			
			if($tabela=='VND1001_ON'){

				$codigoEmpresa = '400';
				
				for($i=0;$i<count($campos);$i++){
					if($campos[$i]['NOME_CAMPO']=='CODIGO_EMPRESA'){
						$codigoEmpresa = $campos[$i]['VALOR'];
					}			
				}
				
				if ($codigoEmpresa != '400')
				{
					if ($campo['NOME_CAMPO']=='NUMERO_CPF_CONTRATANTE'){
						$campo['COMPORTAMENTO'] = 3;
					}
					if ($campo['NOME_CAMPO']=='NUMERO_RG_CONTRATANTE'){
						$campo['COMPORTAMENTO'] = 3;
					}
					if ($campo['NOME_CAMPO']=='NOME_CONTRATANTE'){
						$campo['COMPORTAMENTO'] = 3;
					}
					if ($campo['NOME_CAMPO']=='CODIGO_BANCO'){
						$campo['COMPORTAMENTO'] = 3;
					}
					if ($campo['NOME_CAMPO']=='NUMERO_CONTA'){
						$campo['COMPORTAMENTO'] = 3;
					}
				}

			}
			
			if($tabela=='TMP1000_NET'){
				
				if($_SESSION['codigoSmart'] == '3423'){//Plena
				
					global $tipoAssociadoTmp1000Net;
					
					if($campo['NOME_CAMPO']=='TIPO_ASSOCIADO'){
						$tipoAssociadoTmp1000Net = $campo['VALOR'];				
					}		
					
					if ($campo['NOME_CAMPO']=='CODIGO_PARENTESCO' && $tabelaOrigem != 'VW_TMP1000NET_DEP_AL2' && $tabelaOrigem != 'TMP1000_NET'){				
						if($tipoAssociadoTmp1000Net == 'T'){
							$campo['TIPO'] = 'TEXT';
							$campo['VALOR'] = 29;
							$campo['COMPORTAMENTO'] = 3;
						}
					}
					
					if ($campo['NOME_CAMPO']=='CODIGO_TITULAR' && $tabelaOrigem != 'VW_TMP1000NET_DEP_AL2' && $tabelaOrigem != 'TMP1000_NET'){
						if($tipoAssociadoTmp1000Net == 'T'){
							$campo['COMPORTAMENTO'] = 3;
						}
					}
					
					if($campo['TIPO']=='ANEXO_VINCULO' && ($tabelaOrigem == 'VW_TMP1000NET_DEP_AL2' || $tabelaOrigem == 'TMP1000_NET')){
						$campo['LABEL'] = 'Comprovante de vínculo com o titular (Certidão de nascimento/Certidão de casamento)';
					}

					if($campo['NOME_CAMPO']=='CODIGO_TITULAR'){
						$codigoTitularFiltro =  $campo['VALOR'];				

					}

					if($_SESSION['perfilOperador'] == 'EMPRESA'){
						$queryTbPreco  = ' SELECT DISTINCT CODIGO_TABELA_PRECO FROM PS1059 ';
						$queryTbPreco .= ' WHERE CODIGO_EMPRESA = ' . aspas($_SESSION['codigoIdentificacao']);				
						$resTbPreco = jn_query($queryTbPreco);
						$i=0;
						$tabelaPreco ='';
						while($rowTbPreco = jn_fetch_object($resTbPreco)){
							$tabelaPreco = $rowTbPreco->CODIGO_TABELA_PRECO;
							$i++;
						}
										
						if($i == 1 && $campo['NOME_CAMPO']=='CODIGO_TABELA_PRECO'){
							$campo['VALOR'] = $tabelaPreco;
							$campo['COMPORTAMENTO'] = 2;
							$campo['TIPO'] = 'TEXT';
						}
						
					}

					if (($campo['NOME_CAMPO']=='CODIGO_PLANO' || $campo['NOME_CAMPO']=='CODIGO_TABELA_PRECO') && $tabelaOrigem == 'VW_TMP1000NET_DEP_AL2'){
						$codigoTitular = $dadosInput['subprocesso']['chave'];
						$queryTit = 'SELECT CODIGO_PLANO, CODIGO_TABELA_PRECO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoTitular);
						$resTit = jn_query($queryTit);
						if($rowTit = jn_fetch_object($resTit)){
							if($campo['NOME_CAMPO']=='CODIGO_PLANO'){
								$campo['VALOR'] = $rowTit->CODIGO_PLANO;	
							}
							if($campo['NOME_CAMPO']=='CODIGO_TABELA_PRECO'){
								$campo['VALOR'] = $rowTit->CODIGO_TABELA_PRECO;	
							}
							
							$campo['COMPORTAMENTO'] = 2;
							$campo['TIPO'] = 'TEXT';
						}								
					}
				}elseif($_SESSION['codigoSmart'] == '4316'){//Regras MedHealth
					if($campo['NOME_CAMPO']=='FLAG_CADASTRO_VALIDADO' && $_SESSION['perfilOperador'] == 'OPERADOR'){
						$campo['COMPORTAMENTO'] = 1;
					}
				}
			}
			
			
			if($tabela == 'PS5750'){
				
				if(retornaValorConfiguracao('VALIDAR_ESPECIALIDADES_PREST') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_CBO'){
						
						if( $_SESSION['type_db'] == 'firebird' ){
							$sql  = ' SELECT DISTINCT ESPEC.CODIGO_ESPECIALIDADE, (select retorno from tira_acentos(ESPEC.nome_especialidade)) as NOME_ESPECIALIDADE ';
						}else{
							$sql  = ' SELECT DISTINCT ESPEC.CODIGO_ESPECIALIDADE, ESPEC. NOME_ESPECIALIDADE ';
						}
						
						$sql .= ' FROM PS5003 ESPECPREST ';
						$sql .= ' INNER JOIN PS5100 ESPEC ON ESPEC.CODIGO_ESPECIALIDADE = ESPECPREST.CODIGO_ESPECIALIDADE ';
						$sql .= ' WHERE ESPECPREST.CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']);
						$sql .= ' ORDER BY ESPEC.NOME_ESPECIALIDADE ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_ESPECIALIDADE . ' - ' . jn_utf8_encode($row->NOME_ESPECIALIDADE);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}
				
				if(retornaValorConfiguracao('TIPO_ATENDIMENTO_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_TIPO_ATENDIMENTO'){
						
						$sql  = ' SELECT CODIGO_TIPO_ATENDIMENTO, DESCRICAO_TIPO_ATENDIMENTO ';
						$sql .= ' FROM PS5266';
						$sql .= ' WHERE 1 = 1 ';
						if(retornaValorConfiguracao('OPCOES_TP_ATENDIMENTO_APRESENT')){
							$sql .= ' AND CODIGO_TIPO_ATENDIMENTO IN (' . retornaValorConfiguracao('OPCOES_TP_ATENDIMENTO_APRESENT') . ') ';
						
						}
						$sql .= ' ORDER BY DESCRICAO_TIPO_ATENDIMENTO ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_TIPO_ATENDIMENTO . ' - ' . jn_utf8_encode($row->DESCRICAO_TIPO_ATENDIMENTO);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}

				if(retornaValorConfiguracao('MOTIVO_ENCERRAMENTO_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_MOTIVO_ENCERRAMENTO'){
						
						if( $_SESSION['type_db'] == 'firebird' ){
							$sql  = ' SELECT CODIGO_MOTIVO_ENCERRAMENTO, (select osem_acentos from RETIRAACENTOS(DESCRICAO_MOTIVO_ENCERRAMENTO)) DESCRICAO_MOTIVO_ENCERRAMENTO ';
						}else{
							$sql  = ' SELECT CODIGO_MOTIVO_ENCERRAMENTO, DESCRICAO_MOTIVO_ENCERRAMENTO ';
						}
						
						$sql .= ' FROM PS5280';
						$sql .= ' WHERE 1 = 1 ';
						if(retornaValorConfiguracao('OPCOES_MOT_ENCERRAMENTO')){
							$sql .= ' AND CODIGO_MOTIVO_ENCERRAMENTO IN (' . retornaValorConfiguracao('OPCOES_MOT_ENCERRAMENTO') . ') ';
						
						}
						$sql .= ' ORDER BY DESCRICAO_MOTIVO_ENCERRAMENTO ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_MOTIVO_ENCERRAMENTO . ' - ' . jn_utf8_encode($row->DESCRICAO_MOTIVO_ENCERRAMENTO);
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}

				if(retornaValorConfiguracao('MOTIVO_INTERNACAO_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_TIPO_INTERNACAO'){
						
						$sql  = ' SELECT CODIGO_TIPO_INTERNACAO, DESCRICAO_TIPO_INTERNACAO FROM PS5262';
						$sql .= ' ORDER BY DESCRICAO_TIPO_INTERNACAO ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_TIPO_INTERNACAO . ' - ' . jn_utf8_encode($row->DESCRICAO_TIPO_INTERNACAO);
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}
			}
			
			if(($tabela == 'PS6500') or ($tabela == 'PS5750')) {
				
				if(retornaValorConfiguracao('VALIDAR_ESPECIALIDADES_PREST') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_ESPECIALIDADE'){
						
						$sql = '';
						if( $_SESSION['type_db'] == 'firebird' ){
							$sql  = ' SELECT DISTINCT ESPEC.CODIGO_ESPECIALIDADE, (select retorno from tira_acentos(ESPEC.nome_especialidade)) as NOME_ESPECIALIDADE ';
						}else{
							$sql  = ' SELECT DISTINCT ESPEC.CODIGO_ESPECIALIDADE, ESPEC.nome_especialidade as NOME_ESPECIALIDADE ';
						}

						$sql .= ' FROM PS5003 ESPECPREST ';
						$sql .= ' INNER JOIN PS5100 ESPEC ON ESPEC.CODIGO_ESPECIALIDADE = ESPECPREST.CODIGO_ESPECIALIDADE ';
						$sql .= ' WHERE ESPECPREST.CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']);
						$sql .= ' ORDER BY ESPEC.NOME_ESPECIALIDADE ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_ESPECIALIDADE . ' - ' . jn_utf8_encode($row->NOME_ESPECIALIDADE);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}
				
				if(retornaValorConfiguracao('TIPO_ATENDIMENTO_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_TIPO_ATENDIMENTO'){
						
						$sql  = ' SELECT CODIGO_TIPO_ATENDIMENTO, DESCRICAO_TIPO_ATENDIMENTO ';
						$sql .= ' FROM PS5266';
						$sql .= ' WHERE 1 = 1 ';
						if(retornaValorConfiguracao('OPCOES_TP_ATENDIMENTO_APRESENT')){
							$sql .= ' AND CODIGO_TIPO_ATENDIMENTO IN (' . retornaValorConfiguracao('OPCOES_TP_ATENDIMENTO_APRESENT') . ') ';
						
						}
						$sql .= ' ORDER BY DESCRICAO_TIPO_ATENDIMENTO ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_TIPO_ATENDIMENTO . ' - ' . jn_utf8_encode($row->DESCRICAO_TIPO_ATENDIMENTO);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}
				
				if(retornaValorConfiguracao('TIPO_SAIDA_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_TIPO_SAIDA'){
						
						$sql  = ' SELECT CODIGO_TIPO_SAIDA, DESCRICAO_TIPO_SAIDA ';
						$sql .= ' FROM PS5267';
						$sql .= ' WHERE 1 = 1 ';
						if(retornaValorConfiguracao('OPCOES_TP_SAIDA_APRESENT')){
							$sql .= ' AND CODIGO_TIPO_SAIDA IN (' . retornaValorConfiguracao('OPCOES_TP_SAIDA_APRESENT') . ') ';
						
						}
						$sql .= ' ORDER BY DESCRICAO_TIPO_SAIDA ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_TIPO_SAIDA . ' - ' . jn_utf8_encode($row->DESCRICAO_TIPO_SAIDA);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}

				if($campo['NOME_CAMPO'] == 'CODIGO_PRESTADOR_EXECUTANTE'){
			
					$apenasPrestLogado = true;
						
					if(retornaValorConfiguracao('HABILITA_PRESTADOR_EXE')) {

						$prestadorLogado = explode(',',retornaValorConfiguracao('HABILITA_PRESTADOR_EXE'));
						
						if(in_array($_SESSION['codigoIdentificacao'], $prestadorLogado))
							$apenasPrestLogado = false;
					}

					if($apenasPrestLogado){

						$sql  = ' SELECT CODIGO_PRESTADOR, NOME_PRESTADOR FROM PS5000 ';
						$sql .= ' WHERE PS5000.DATA_DESCREDENCIAMENTO IS NULL AND COALESCE(PS5000.FLAG_ATENDIMENTO_SUSPENSO, "N") = "N" ';
						$sql .= ' AND (
										(CODIGO_PRESTADOR_PRINCIPAL = ' . aspas($_SESSION['codigoIdentificacao']) . ') 
									OR 	(CODIGO_PRESTADOR_HOSPITAL = ' . aspas($_SESSION['codigoIdentificacao']) . ') 
									OR  (CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']) . ') )';			
						
						$sql .= ' ORDER BY NOME_PRESTADOR ';
			
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->CODIGO_PRESTADOR . ' - ' . jn_utf8_encode($row->NOME_PRESTADOR);					
						}			
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;	

						if($_SESSION['perfilOperador'] == 'PRESTADOR'){
							$campo['VALOR'] = $_SESSION['codigoIdentificacao'];			
						}

					}			
				}
			}
			
			if($tabela == 'PS5760'){
				if(retornaValorConfiguracao('TABELAS_TISS_COMBO') == 'SIM'){
					if($campo['NOME_CAMPO'] == 'CODIGO_TABELA'){
						
						$sql  = ' SELECT REFERENCIA_TABELA, COALESCE(REFERENCIA_TABELA,DESCRICAO_NA_TISS) AS DESCRICAO_NA_TISS  ';
						$sql .= ' FROM PS5211';
						$sql .= ' WHERE 1 = 1 ';
						if($_SESSION['perfilOperador']== 'PRESTADOR' && retornaValorConfiguracao('VALIDA_TABELAS_PRESTADOR') == 'SIM') {
							$sql .= ' and REFERENCIA_TABELA IN (
												SELECT 
													COALESCE(REFERENCIA_TABELA_EXAMES, COALESCE(REFERENCIA_TABELA_CONSULTA, REFERENCIA_TABELA_ODONTO))
												FROM PS5002
												WHERE CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']) . ') ';
						}				
						$sql .= ' ORDER BY DESCRICAO_NA_TISS ';
						
						$res = jn_query($sql);
						$opcoesCombo = '';
						while($row = jn_fetch_object($res)){
							if($opcoesCombo != '')
									$opcoesCombo .= ';';
							
							$opcoesCombo .= $row->REFERENCIA_TABELA . ' - ' . jn_utf8_encode($row->DESCRICAO_NA_TISS);					
						}
						
						$campo['TIPO'] =  'COMBOBOX';
						$campo['OPCOES_COMBO'] =  $opcoesCombo;				
					}
				}

				$chavePS5750 = 	$dadosInput['subprocesso']['chave'];
				if(!$chavePS5750){
					$chavePS5750 = 	$dadosInput['chave'];
				}

				$queryTpGuia = 'SELECT TIPO_GUIA, CODIGO_PRESTADOR, CODIGO_ASSOCIADO FROM PS5750 WHERE NUMERO_REGISTRO = ' . aspas($chavePS5750);
				$resTpGuia = jn_query($queryTpGuia);
				$rowTpGuia = jn_fetch_object($resTpGuia);	

				if(retornaValorConfiguracao('APRESENTA_MATMED_PS5760') == 'SIM'){							
					
					if($rowTpGuia->TIPO_GUIA == 'A' && $campo['NOME_CAMPO'] == 'CODIGO_MATMED'){
						$campo['COMPORTAMENTO'] = 1;
					}

					if($rowTpGuia->TIPO_GUIA == 'A' && $campo['NOME_CAMPO'] == 'CODIGO_TUSS'){
						$campo['OBRIGATORIO'] = false;
					}
				}

				if(retornaValorConfiguracao('APRESENTA_SERVICO_PS5760') == 'SIM'){
					
					if($rowTpGuia->TIPO_GUIA == 'A' && $campo['NOME_CAMPO'] == 'CODIGO_SERVICO'){
						$campo['COMPORTAMENTO'] = 1;
					}
				}

				if($rowTpGuia->TIPO_GUIA == 'C' && $campo['NOME_CAMPO'] == 'CODIGO_TUSS'){
					$campo['VALOR'] = '10101012';
				}

				if($rowTpGuia->TIPO_GUIA == 'O' && $campo['NOME_CAMPO'] == 'CODIGO_UNIDADE_MEDIDA'){
					$campo['COMPORTAMENTO'] = 3;
				}
				
			}
			
			if($tipo=='ALT'){
				if($tabela == 'PS6120'){
					if($_SESSION['perfilOperador'] == 'OPERADOR'){
						if ($campo['NOME_CAMPO']=='TIPO_MANIFESTACAO'){
							$campo['COMPORTAMENTO'] = 2;
						}
						
						if ($campo['NOME_CAMPO']=='CODIGO_ASSOCIADO'){
							$campo['COMPORTAMENTO'] = 2;
						}
						
						if ($campo['NOME_CAMPO']=='NOME_PESSOA'){
							$campo['COMPORTAMENTO'] = 2;
						}
						
						if ($campo['NOME_CAMPO']=='TELEFONE_CONTATO_01'){
							$campo['COMPORTAMENTO'] = 2;
						}
						
						if ($campo['NOME_CAMPO']=='TELEFONE_CONTATO_02'){
							$campo['COMPORTAMENTO'] = 2;
						}
						
						if ($campo['NOME_CAMPO']=='EMAIL'){
							$campo['COMPORTAMENTO'] = 2;
						}
						
						if ($campo['NOME_CAMPO']=='DATA_ABERTURA'){
							$campo['COMPORTAMENTO'] = 2;
						}
						
						if ($campo['NOME_CAMPO']=='DATA_CONCLUSAO'){
							$campo['COMPORTAMENTO'] = 1;
						}
						
						if ($campo['NOME_CAMPO']=='DESCRICAO_OCORRENCIA'){
							$campo['COMPORTAMENTO'] = 2;
						}				
					}
					
				}
			}

			if($tabela == 'PS6510'){
				if(retornaValorConfiguracao('APRESENTA_MATMED_PS5760') == 'SIM'){						
					$chaveAutorizacao = 	$dadosInput['subprocesso']['chave'];
					if(!$chaveAutorizacao){
						$chaveAutorizacao = 	$dadosInput['chave'];
					}
					
					$queryTpGuia = 'SELECT TIPO_GUIA FROM PS6500 WHERE NUMERO_AUTORIZACAO = ' . aspas($chaveAutorizacao);
					$resTpGuia = jn_query($queryTpGuia);
					$rowTpGuia = jn_fetch_object($resTpGuia);			
					
					if($rowTpGuia->TIPO_GUIA == 'A' && $campo['NOME_CAMPO'] == 'CODIGO_MEDICAMENTO_MATERIAL'){
						$campo['COMPORTAMENTO'] = 1;
					}

					if($rowTpGuia->TIPO_GUIA == 'A' && $campo['NOME_CAMPO'] == 'CODIGO_PROCEDIMENTO'){
						$campo['OBRIGATORIO'] = false;
					}
				}	
			}
			
			if($tabela == 'ESP_REEMBOLSO'){
				if($campo['NOME_CAMPO'] == 'CODIGO_ASSOCIADO' and $_SESSION['perfilOperador'] == 'BENEFICIARIO'){
					
					$sql = "SELECT CODIGO_ASSOCIADO, NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_TITULAR = ".aspas($_SESSION['codigoIdentificacao']);				
					$res = jn_query($sql);
					$opcoesCombo = '';
					while($row = jn_fetch_object($res)){
						if($opcoesCombo != '')
								$opcoesCombo .= ';';
						
						$opcoesCombo .= $row->CODIGO_ASSOCIADO . ' - ' . $row->NOME_ASSOCIADO;					
					}
					
					$campo['TIPO'] =  'COMBOBOX';
					$campo['OPCOES_COMBO'] =  $opcoesCombo;				
				}
				
				if((($campo['NOME_CAMPO'] == 'STATUS_SOLICITACAO') || ($campo['NOME_CAMPO'] == 'VALOR_CALCULADO')) && ($_SESSION['perfilOperador'] == 'BENEFICIARIO')){
					$campo['COMPORTAMENTO'] = 2;

				}

			}

			if($tabela == 'PS1000' and $tipo=='ALT' and $tabelaOrigem =='VW_PS1000_EXC_AL2' and $_SESSION['codigoSmart'] == '3423'){
				if($campo['NOME_CAMPO'] == 'CODIGO_AUXILIAR'){
					$campo['COMPORTAMENTO'] = 1;
				}
			}
			
			if($tipo=='INC'){
				if(($tabela == 'PS6500')and ($campo['NOME_CAMPO'] == 'TOKEN')){
					if(retornaValorCFG0003('VALIDA_TOKEN_AUTORIZACAO')=='SIM'){
						$campo['COMPORTAMENTO'] = 1;	
					}
				}
				
				if(($tabela == 'PS6550')and ($campo['NOME_CAMPO'] == 'TOKEN')){
					if(retornaValorCFG0003('VALIDA_TOKEN_SOLICITACAO')=='SIM' and $_SESSION['perfilOperador'] == 'PRESTADOR'){
						$campo['COMPORTAMENTO'] = 1;	
					}
				}

				if($tabela == 'PS6550' and $campo['NOME_CAMPO'] == 'CARATER_SOLICITACAO' and $_SESSION['perfilOperador'] == 'OPERADOR' and ($_SESSION['AUDITOR'] == 'N' or $_SESSION['AUDITOR'] == '')){
					$campo['TIPO'] =  'TEXT';
					$campo['VALOR'] = 'E';
					$campo['COMPORTAMENTO'] = 2;
				}
			}

			if($tabela == 'PS1095'){
				if($campo['NOME_CAMPO'] == 'CODIGO_MOTIVO_EXCLUSAO'){
					
					$sql  = ' SELECT CODIGO_MOTIVO_EXCLUSAO, NOME_MOTIVO_EXCLUSAO ';
					$sql .= ' FROM PS1047';
					$sql .= ' WHERE 1 = 1 ';
					if(retornaValorConfiguracao('OPCOES_MOTIVO_EXCLUSAO')){
						$sql .= ' AND CODIGO_MOTIVO_EXCLUSAO IN (' . retornaValorConfiguracao('OPCOES_MOTIVO_EXCLUSAO') . ') ';
					}

					$sql .= ' ORDER BY NOME_MOTIVO_EXCLUSAO ';
					
					$res = jn_query($sql);
					$opcoesCombo = '';
					while($row = jn_fetch_object($res)){
						if($opcoesCombo != '')
								$opcoesCombo .= ';';
						
						$opcoesCombo .= $row->CODIGO_MOTIVO_EXCLUSAO . ' - ' . jn_utf8_encode($row->NOME_MOTIVO_EXCLUSAO);
					}
					
					$campo['TIPO'] =  'COMBOBOX';
					$campo['OPCOES_COMBO'] =  $opcoesCombo;				
				}
			}

			if($tabela == 'ESP_TRANSFERENCIA_CAD'){
				global $codigoGrupoPessoas;
				
				if($campo['NOME_CAMPO'] == 'CODIGO_ASSOCIADO_ANTIGO'){

					$queryPs1000  = ' SELECT PS1010.CODIGO_GRUPO_PESSOAS FROM PS1000 ';
					$queryPs1000 .= ' INNER JOIN PS1010 ON (PS1010.CODIGO_EMPRESA = PS1000.CODIGO_EMPRESA) ';
					$queryPs1000 .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($campo['VALOR']);
					$resPs1000 = jn_query($queryPs1000);
					$rowPs1000 = jn_fetch_object($resPs1000);
					$codigoGrupoPessoas = $rowPs1000->CODIGO_GRUPO_PESSOAS;
				}

				if($campo['NOME_CAMPO'] == 'CODIGO_EMPRESA'){
					$sql  = ' SELECT CODIGO_EMPRESA, NOME_EMPRESA, NUMERO_CNPJ ';
					$sql .= ' FROM PS1010';
					$sql .= ' WHERE DATA_EXCLUSAO IS NULL ';
					$sql .= ' AND CODIGO_GRUPO_PESSOAS = ' . aspas($codigoGrupoPessoas);;
					

					$sql .= ' ORDER BY NOME_EMPRESA ';
					
					$res = jn_query($sql);
					$opcoesCombo = '';
					while($row = jn_fetch_object($res)){
						if($opcoesCombo != '')
							$opcoesCombo .= ';';
						
						$opcoesCombo .= $row->CODIGO_EMPRESA . ' - ' . jn_utf8_encode($row->CODIGO_EMPRESA . ' | ' . $row->NUMERO_CNPJ. ' | ' . $row->NOME_EMPRESA);
					}
					
					$campo['TIPO'] =  'COMBOBOX';
					$campo['OPCOES_COMBO'] =  $opcoesCombo;				
				}
			}

			if ($tabela == 'ESP_CAD_EMPRESAS_TMP' and $_SESSION['perfilOperador'] == 'VENDEDOR' and $tipo=='INC'){ 
				if($campo['NOME_CAMPO'] == 'CODIGO_VENDEDOR'){
					$campo['VALOR'] = $_SESSION['codigoIdentificacao'];			
				}
			}

			if($tabela == 'PS6110'){
				if($campo['NOME_CAMPO']=='CODIGO_RECLAMACAO_SUGESTAO' and retornaValorConfiguracao('OPCOES_SUGESTAO_REC_COMBO') == 'SIM'){
					$queryPs6100  = ' SELECT CODIGO_RECLAMACAO_SUGESTAO, DESCRICAO_RECLAMACAO_SUGESTAO FROM PS6100  ';								
					$queryPs6100 .= ' ORDER BY DESCRICAO_RECLAMACAO_SUGESTAO ';				
					$resPs6100 = jn_query($queryPs6100);
					$opcoesCombo = '';
					while($rowPs6100 = jn_fetch_object($resPs6100)){					
						$opcoesCombo .= ';';					
						$opcoesCombo .= $rowPs6100->CODIGO_RECLAMACAO_SUGESTAO . ' - ' . jn_utf8_encode($rowPs6100->DESCRICAO_RECLAMACAO_SUGESTAO);					
					}
					
					$campo['TIPO'] =  'COMBOBOX';
					$campo['OPCOES_COMBO'] =  $opcoesCombo;				
				}

				if($_SESSION['perfilOperador'] != 'BENEFICIARIO'){
					if($campo['NOME_CAMPO'] == 'CODIGO_ASSOCIADO'){
						$campo['COMPORTAMENTO'] = 3;
					}

					if($campo['NOME_CAMPO'] == 'NOME_PESSOA'){
						$campo['COMPORTAMENTO'] = 1;
					}
				}
			}

	} // Fim do IF se não for o ERP, neste caso apenas o portal


	/* --------------------------------------------------------------------------------------------------------------------- */
	/* --------------------------------------------------------------------------------------------------------------------- */
	/* --------------------------------------------------------------------------------------------------------------------- */

	// A partir daqui ocorrerá tanto se for o AliancaPx4Net (ERP) quanto no portal

	if($campo['NOME_CAMPO'] == 'INFORMACOES_LOG_I')
	{				
		$campo['COMPORTAMENTO'] = 3;		
		$retorno                = 'IGNORAR_CAMPO';							
	}

	if($campo['NOME_CAMPO'] == 'INFORMACOES_LOG_A')
	{				
		$campo['COMPORTAMENTO'] = 3;									
		$retorno                = 'IGNORAR_CAMPO';							
	}


	if ($tabela == 'PS1000') 
	{
		if ($tipo=='INC')
		{
			if (($campo['NOME_CAMPO'] == 'CODIGO_EMPRESA') or 
		        ($campo['NOME_CAMPO'] == 'CODIGO_PLANO'))
			{
				$campo['COMPORTAMENTO'] = 1;	
			}
		}
		else if ($tipo=='ALT')
		{
			if (($campo['NOME_CAMPO'] == 'CODIGO_EMPRESA') or 
		        ($campo['NOME_CAMPO'] == 'CODIGO_PLANO'))
			{
				$campo['COMPORTAMENTO'] = 2;	
			}
		}
	}	
		
	
	return 	$retorno;

}





function botoesOpcoesAdicionaisEspecificas($nomeTabela, $nomeTabelaOriginal, $chave, $nomeChave)
{

	$retorno                         = array();
	$retorno['OPCOESADICIONAIS']     = array();
	$retorno['TEMOPCOESADICIONAIS']  = 'N';

	if ($nomeTabelaOriginal=='PS1000')
	{

		$rowTmp = qryUmRegistro('Select PS1000.NOME_ASSOCIADO, PS1039.NUMERO_REGISTRO FROM PS1000 
			                     INNER JOIN PS1039 ON (PS1000.CODIGO_PLANO = PS1039.CODIGO_PLANO)
			                     WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($chave));

		if ($rowTmp->NUMERO_REGISTRO!='')
		{
			$opcoesAdicionais = array();
			$opcoesAdicionais['LABEL_MENU']          = 'Declaração de Saúde';
			$opcoesAdicionais['NOME_CHAVE_PROCURA']  = $nomeChave;
			$opcoesAdicionais['VALOR_CHAVE_PROCURA'] = $chave;
			$opcoesAdicionais['NOME_TABELA_ORIGEM']  = 'PS1000';
			$opcoesAdicionais['NOME_TABELA_DESTINO'] = 'PS1005';
			$opcoesAdicionais['ABRIR_SEQ_CADASTRO']  = 'S';
			$opcoesAdicionais['MSG_SEQ_CADASTRO']    = 'Preeencha a declaração do beneficiário: '. $chave . ' ' . $rowTmp->NOME_ASSOCIADO;
			$opcoesAdicionais['DESTINO_ANGULAR']     = 'site/declaracaoSaude';
			$opcoesAdicionais['PARAMETROS_ANGULAR']  = '{"CODIGO_PESQUISA":"' . $chave . '"}';

			$retorno['OPCOESADICIONAIS'][]           = $opcoesAdicionais;		
			$retorno['TEMOPCOESADICIONAIS']  		 = 'S';
		}

		/*$rowTmp = qryUmRegistro('Select PS1000.NOME_ASSOCIADO, PS1002.NUMERO_CONTRATO FROM PS1000 
			                     INNER JOIN PS1002 ON (PS1000.CODIGO_ASSOCIADO = PS1002.CODIGO_ASSOCIADO)
			                     WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($chave));


		if ($rowTmp->NUMERO_CONTRATO!='')
		{
			$opcoesAdicionais = array();
			$opcoesAdicionais['LABEL_MENU']          = 'Contratos de vendas';
			$opcoesAdicionais['NOME_CHAVE_PROCURA']  = 'NUMERO_CONTRATO';
			$opcoesAdicionais['VALOR_CHAVE_PROCURA'] = $rowTmp->NUMERO_CONTRATO;
			$opcoesAdicionais['NOME_TABELA_DESTINO'] = 'PS3100';
			$opcoesAdicionais['ABRIR_SEQ_CADASTRO']  = 'S';
			$opcoesAdicionais['DESTINO_ANGULAR']     = 'site/processoDinamico';
			$opcoesAdicionais['PARAMETROS_ANGULAR']  = '{"reg":"2201"}';
			$retorno['OPCOESADICIONAIS'][]           = $opcoesAdicionais;	
			$retorno['TEMOPCOESADICIONAIS']  		 = 'S';

		}*/
	}
	else if ($nomeTabelaOriginal=='PS1100')
	{

		$opcoesAdicionais = array();
		$opcoesAdicionais['LABEL_MENU']          = 'Permissões do operador';
		$opcoesAdicionais['NOME_CHAVE_PROCURA']  = $nomeChave;
		$opcoesAdicionais['VALOR_CHAVE_PROCURA'] = $chave;
		$opcoesAdicionais['NOME_TABELA_ORIGEM']  = 'PS1100';
		$opcoesAdicionais['NOME_TABELA_DESTINO'] = 'CFGENTIDADES_SIS_PERMISS';
		$opcoesAdicionais['ABRIR_SEQ_CADASTRO']  = 'S';
		$opcoesAdicionais['MSG_SEQ_CADASTRO']    = 'Informe as permissões específicas deste operador';
		$opcoesAdicionais['DESTINO_ANGULAR']     = 'site/permissao';
		$opcoesAdicionais['PARAMETROS_ANGULAR']  = '{"codigo":"' . $chave . '","tipo":"total"}';

		$retorno['OPCOESADICIONAIS'][]           = $opcoesAdicionais;		
		$retorno['TEMOPCOESADICIONAIS']  		 = 'S';

	}



	return $retorno;
}



?>