<?php
 
function antesGravacao($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,&$campos,&$retorno){
//$tipo = INC ALT EXC 
//campos apenas para INC ALT
//$retorno['STATUS'] = 'OK';
//$retorno['STATUS'] = 'ERRO'; para processo
//$retorno['MSG']    .= ''; mensagem que ira aparecer operador quando der erro.
//$retorno['MSG']    .= jn_utf8_encode('usar esssa função quando tiver acentuação');
//$campos[$i]['CAMPO']
//$campos[$i]['VALOR']
$retorno['STATUS'] = 'OK'; 
	/*
	if(($tipo == 'EXC')and($tabela=='XXX')){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    .= 'Não pode excluir';
	}


	if($tabela=="XXX"){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    .= 'TESTE ERRO';
	}
	
	for($i=0;$i<count($campos);$i++){
		if($campos[$i]['CAMPO']=='CODIGO_OPPROV'){
			$campos[$i]['VALOR'] = 1;
		}
	}
	*/
	
	/* AQUI CARREGO UM ARRAY PARA FACILITAR VALIDAÇÃO DOS CAMPOS */

	//pr($chave);
	//pr($nomeChave);
	//pr($campos);
	//pr($retorno);
	//pr('---------------------------------');

	$camposValores = array();

	if ($tipo != 'EXC')
	{
			for($i=0;$i<count($campos);$i++)
			{
				$nomeCampo = $campos[$i]['CAMPO'];
				$camposValores[$nomeCampo] = $campos[$i]['VALOR'];
			}
	}	


	if ($_SESSION['AliancaPx4Net']=='S') // Se for o ERP AliancaPx4Net
	{

		  require_once('../EstruturaPx/antesDaGravacao_ERPPx.php');
			$retorno = antesGravacao_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,$campos,$retorno);

	}
	else // então não é o ERP AliancaPX4Net, é o portal
	{

	
				if(($tipo == 'INC')and($tabelaOrigem=='VND1000_ON')){
					$codigoTitular = '';
					$codigoDependente = '';
					$tipoAssociado = '';
					$indexAssociado = 0;		
					$dataNascimento = '';				
					$numeroCPF = '';
					$numeroDeclaracao = '';
					$codigoPlano = '';
					$indexCodigoPlano = 0;
					$codigoTabelaPreco = 0;
					$indexTabelaPreco = 0;
					$indexGpContrato = 0;
					$codigoGrupoPessoas = '';
					$codigoGrupoContrato = '';
					$indexDataAdmissao = 0;
					$codigoCNS = '';
					
					for($i=0;$i<count($campos);$i++){
						
						if($campos[$i]['CAMPO']=='TIPO_ASSOCIADO'){
							$tipoAssociado = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_TITULAR'){
							$codigoTitular = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
							$indexAssociado = $i;
							$codigoAssociado = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='DATA_NASCIMENTO'){
							$dataNascimento = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='NUMERO_CPF'){				
							$numeroCPF = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_PLANO'){				
							$codigoPlano = $campos[$i]['VALOR'];
							$indexCodigoPlano = $i;
						}
						
						if($campos[$i]['CAMPO']=='NUMERO_DECLARACAO_NASC_VIVO'){
							$numeroDeclaracao = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_TABELA_PRECO'){
							$codigoTabelaPreco = $campos[$i]['VALOR'];
							$indexTabelaPreco = $i;
						}
						
						if($campos[$i]['CAMPO']=='NOME_ASSOCIADO'){
							 $campos[$i]['VALOR'] = strtoupper($campos[$i]['VALOR']);				 
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_GRUPO_CONTRATO'){
							$codigoGrupoContrato = $campos[$i]['VALOR'];
							$indexGpContrato = $i;
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_GRUPO_PESSOAS'){
							$codigoGrupoPessoas = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='DATA_ADMISSAO'){
							$indexDataAdmissao = $i;
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_VENDEDOR'){
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];
						}

						if($campos[$i]['CAMPO']=='CODIGO_CNS'){
							$codigoCNS = $campos[$i]['VALOR'];
						}
					}
					
					$idade = calcularIdade($dataNascimento);				
					
					if(retornaValorConfiguracao('VND_VALIDA_DEC_NASC_VIVO') == '' || retornaValorConfiguracao('VND_VALIDA_DEC_NASC_VIVO') == 'SIM'){			
						if($numeroDeclaracao == '' && $idade < 10){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'O campo Número Declaração Nascido Vivo é obrigatório.';
						}
					}
					
					if(retornaValorConfiguracao('VND_VALIDAR_CPF_DUPLICADO') == '' || retornaValorConfiguracao('VND_VALIDAR_CPF_DUPLICADO') == 'SIM'){
						$queryCPF = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE NUMERO_CPF IS NOT NULL AND NUMERO_CPF <> "" AND NUMERO_CPF =' . aspas($numeroCPF);
						
						if(retornaValorConfiguracao('VND_REGRA_PLANO_VALIDA_CPF') == 'SIM'){
							$queryCPF .= ' AND CODIGO_PLANO =' . aspas($codigoPlano);
						}
						
						$resCPF = jn_query($queryCPF);
						
						
						if($rowCPF = jn_fetch_object($resCPF)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Já existe um cadastro com este CPF(1).';
						}
					}
					
					if($tipoAssociado == 'T'){			
						if(!validaCPF($numeroCPF)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Número CPF inválido.';
						}
					}
					
					if ($codigoPlano == '')
						$codigoPlano = '0000';
					
					$queryConf = 'SELECT TABELA_PRECO_AUTOC FROM VND1030CONFIG_ON WHERE CODIGO_PLANO =' . aspas($codigoPlano);
					
					if(retornaValorConfiguracao('VALIDAR_GP_PESSOAS_TAB_PREC') == 'SIM'){
						$queryConf .= ' AND (CODIGOS_GRUPOS_PESSOAS_ESP = ' . aspas($codigoGrupoPessoas) . ' OR CODIGOS_GRUPOS_PESSOAS_ESP LIKE "%,' . $codigoGrupoPessoas . ',%")';
					}
					
					if($codigoTabelaPreco){
						$queryConf .= ' AND ((TABELA_PRECO_AUTOC = ' . aspas($codigoTabelaPreco) . ') OR (TABELA_PRECO_AUTOC IS NULL))';			
					}
					
					$resConf = jn_query($queryConf);
					$rowConf = jn_fetch_object($resConf);
					
					if($rowConf->TABELA_PRECO_AUTOC == '' && $_SESSION['codigoSmart'] == '4246'){//MV2C	
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    .= 'Esta entidade não está parametrizada para o plano informado.';
					}
					
					if($rowConf->TABELA_PRECO_AUTOC){
						$campos[$indexTabelaPreco]['VALOR'] = $rowConf->TABELA_PRECO_AUTOC;
					}
					
					if($tipoAssociado == 'D' && $codigoAssociado == 'VND_TMP'){
						
						$query = "SELECT count(*) QUANTIDADE from VND1000_ON where CODIGO_TITULAR = " . aspas($codigoTitular);
						$res  = jn_query($query);
						
						if ($row = jn_fetch_object($res)) 
							$numeroDependente = $row->QUANTIDADE;
						
						$codDep = explode('.',$codigoTitular);
						$codDep = $codDep[0] . '.' . $numeroDependente;
						
						$codigoDependente = $codDep;
						$campos[$indexAssociado]['VALOR'] = $codigoDependente;
					
						if($idade > 16){
							if($numeroCPF == ''){
								$retorno['STATUS'] = 'ERRO';
								$retorno['MSG']    .= 'O campo Número CPF é obrigatório.';
							}
						}
						
					}elseif($tipoAssociado == 'T'){
						
						if($numeroCPF == ''){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'O campo Número CPF é obrigatório.';
						}
					}elseif($tipoAssociado == 'D'){

						if($codigoPlano == '' || $codigoPlano == '0000'){
							$queryPlanoDep = 'SELECT CODIGO_PLANO, CODIGO_TABELA_PRECO FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codigoTitular);
							$resPlanoDep = jn_query($queryPlanoDep);
							$rowPlanoDep = jn_fetch_object($resPlanoDep);		
							
							$queryEmp = 'SELECT CODIGO_SMART FROM CFGEMPRESA ';
							$resEmp = jn_query($queryEmp);
							$rowEmp= jn_fetch_object($resEmp);

							if($rowEmp->CODIGO_SMART == '4200' && $idade <= 12){
								$queryPlKids = 'SELECT CODIGO_PLANO_KIDS FROM PS1030 WHERE CODIGO_PLANO = ' . aspas($rowPlanoDep->CODIGO_PLANO);
								$resPlKids = jn_query($queryPlKids);
								$rowPlKids = jn_fetch_object($resPlKids);
								
								$campos[$indexCodigoPlano]['VALOR'] = $rowPlKids->CODIGO_PLANO_KIDS;					
								$campos[$indexTabelaPreco]['VALOR'] = $rowPlanoDep->CODIGO_TABELA_PRECO;					
							}else{
								$campos[$indexCodigoPlano]['VALOR'] = $rowPlanoDep->CODIGO_PLANO;
								$campos[$indexTabelaPreco]['VALOR'] = $rowPlanoDep->CODIGO_TABELA_PRECO;					
							}
							
						}			
					}
					
					if(retornaValorConfiguracao('VALIDAR_GP_CONTRATO_PLANO_VND') == 'SIM'){
						if($codigoGrupoContrato == ''){
							$queryGpPlano = 'SELECT CODIGO_GRUPO_CONTRATO FROM PS1030 WHERE CODIGO_PLANO = ' . aspas($codigoPlano);
							$resGpPlano = jn_query($queryGpPlano);
							$rowGpPlano = jn_fetch_object($resGpPlano);
							if($rowGpPlano->CODIGO_GRUPO_CONTRATO != ''){
								$campos[$indexGpContrato]['VALOR'] = $rowGpPlano->CODIGO_GRUPO_CONTRATO;					
							}
						}			
					}
					
					if($campos[$indexGpContrato]['VALOR'] == '4' && $_SESSION['codigoSmart'] == '4246'){//MV2C	
						$dataAdmissao = '';			
						if($campos[$indexDataAdmissao]['CAMPO'] == 'DATA_ADMISSAO'){
							if(date('d') >= 26 and date('d') <= 10){
								$dataAdmissao = date('Y-m-01', strtotime(date('Y-m-01').'+2 month'));
							}else{
								$dataAdmissao = date('Y-m-16', strtotime(date('Y-m-01').'+1 month'));
							}
							$campos[$indexDataAdmissao]['VALOR'] = $dataAdmissao;				
						}			
					}

					if ($_SESSION['codigoSmart'] == '3389'){//Vidamax 
						$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1030CONFIG_ON ';
						$queryModelo .= ' WHERE CODIGO_PLANO = ' . aspas($codigoPlano);
						$resModelo = jn_query($queryModelo);
						$rowModelo = jn_fetch_object($resModelo);

						if ($rowModelo->CODIGOS_MODELO_CONTRATO == 13 && $codigoCNS == ''){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'O campo Código CNS é obrigatório.';
						}
					}
					
				}


				if(($tipo == 'INC')and($tabela=='PS6110')){

					if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
						for($i=0;$i<count($campos);$i++)
						{
							if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO')
							{
								$codigoAssociado = $campos[$i]['VALOR'];
							}
						}

						if (($codigoAssociado=='') || ($codigoAssociado==null))
						    $codigoAssociado = $_SESSION['codigoIdentificacao'];

						$query = "SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = " . aspas($codigoAssociado);
						$res  = jn_query($query);
						
						$row = jn_fetch_object($res);

						for($i=0;$i<count($campos);$i++)
						{
						
							if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO')
							{
								$campos[$i]['VALOR'] = $codigoAssociado;
							}

							if($campos[$i]['CAMPO']=='NOME_PESSOA')
							{
								$campos[$i]['VALOR'] = substr($row->NOME_ASSOCIADO,0,38);
							}
						}
					}elseif($_SESSION['perfilOperador'] == 'VENDEDOR'){
						for($i=0;$i<count($campos);$i++)
						{
							if($campos[$i]['CAMPO']=='CODIGO_VENDEDOR')
								$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];				
						}
					}elseif($_SESSION['perfilOperador'] == 'EMPRESA'){
						for($i=0;$i<count($campos);$i++)
						{
							if($campos[$i]['CAMPO']=='CODIGO_EMPRESA')
								$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];				
						}
					}
					
				}
				
				if(($tipo == 'INC')and($tabela=='PS1095')){

					$queryProt = '	
								SELECT
			                        MAX(NUMERO_REGISTRO) + 1 AS PROTOCOLO_ATENDIMENTO
			                    FROM PS1095';
					
					$resProt  = jn_query($queryProt);
					$rowProt = jn_fetch_object($resProt);
					
					for($i=0;$i<count($campos);$i++)
					{

						if($campos[$i]['CAMPO']=='NUMERO_PROTOCOLO_EXCLUSAO')
						{
							$campos[$i]['VALOR'] = $rowProt->PROTOCOLO_ATENDIMENTO;
						}
					}
					
					if($tabela == 'PS1095' && $_SESSION['codigoSmart'] == '3389'){//Vidamax			
						$retorno['MSG_ANTES']    = '<img src="https://vidamax.com.br/AliancaAppNet2/Site/assets/img/termo_cancelamento.jpg">';
					}
				
				}
				
				if(($tipo == 'INC')and($tabelaOrigem=='VND1001_ON')){
					
					$codigoAssociado 	= '';
					$codigoEmpresa   	= '';
					$estado 			= '';
					
					for($i=0;$i<count($campos);$i++){
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
							$codigoAssociado = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='ENDERECO'){
							 $campos[$i]['VALOR'] = substr(strtoupper($campos[$i]['VALOR']),0,45);
						}
						
						if($campos[$i]['CAMPO']=='ESTADO'){
							 $campos[$i]['VALOR'] = strtoupper($campos[$i]['VALOR']);
						}
						
						if($campos[$i]['CAMPO']=='BAIRRO'){
							 $campos[$i]['VALOR'] = strtoupper(substr($campos[$i]['VALOR'], 0, 25));
						}
						
						if($campos[$i]['CAMPO']=='CIDADE'){
							 $campos[$i]['VALOR'] = strtoupper($campos[$i]['VALOR']);
						}
						
						if($campos[$i]['CAMPO']=='ENDERECO_EMAIL'){
							 $campos[$i]['VALOR'] = strtoupper($campos[$i]['VALOR']);
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_EMPRESA'){
							$codigoEmpresa = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='ESTADO'){
							$estado = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='NUMERO_CONTRATO' && retornaValorConfiguracao('GERA_CONTRATO_AUTOMATICO') == 'SIM'){
							$campos[$i]['VALOR'] = jn_gerasequencial('VND1001_ON');
						}
					}
					
					if($_SESSION['codigoSmart'] == '4010'){//Tratativa específica Classe
						$queryCfg  = ' SELECT * FROM VND1000_ON ';
						$queryCfg .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
						$queryCfg .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
						$queryCfg .= ' AND VND1030CONFIG_ON.ESTADO = ' . aspas($estado);
						$resCfg = jn_query($queryCfg);
						if($rowCfg = jn_fetch_object($resCfg)){				
							jn_query('UPDATE VND1000_ON SET CODIGO_TABELA_PRECO = ' . aspas($rowCfg->TABELA_PRECO_AUTOC) . ' WHERE CODIGO_TITULAR = ' . aspas($codigoAssociado));
						}
					}
					

					if ($codigoAssociado!='') // Só faz a validação se for pessoa física
					{
						if($_SESSION['type_db'] == 'sqlsrv'){
							if(retornaValorConfiguracao('VALIDA_VENCIMENTO_GPCONTRATO') == 'SIM'){
								$queryAssoc  =  ' SELECT COALESCE(ESP0002.DIA_VENCIMENTO,DAY(VND1000_ON.DATA_ADMISSAO)) AS DIA_VENCIMENTO FROM VND1000_ON ';
								$queryAssoc .=  ' INNER JOIN ESP0002 ON (ESP0002.CODIGO_GRUPO_CONTRATO = VND1000_ON.CODIGO_GRUPO_CONTRATO) ';
								$queryAssoc .=  ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
							}else{
								$queryAssoc = 'SELECT DAY(DATA_ADMISSAO) AS DIA_VENCIMENTO, DATA_NASCIMENTO FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
							}
						}else{
							$queryAssoc = 'SELECT EXTRACT(DAY FROM DATA_ADMISSAO) AS DIA_VENCIMENTO, DATA_NASCIMENTO FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
						}
						
						$resAssoc = jn_query($queryAssoc);
						$rowAssoc = jn_fetch_object($resAssoc);
						
						$idade = calcularIdade($rowAssoc->DATA_NASCIMENTO);	
						if (($codigoEmpresa == '') or ($codigoEmpresa == '400'))
						{
							if($idade < 18){
								for($i=0;$i<count($campos);$i++){
									if($campos[$i]['CAMPO']=='NOME_CONTRATANTE' and $campos[$i]['VALOR'] == ''){
										$retorno['STATUS'] = 'ERRO';
										$retorno['MSG']    .= 'Para titular menor de idade, o nome do contratante é obrigatório.';
									}
									
									if($campos[$i]['CAMPO']=='NUMERO_CPF_CONTRATANTE' and $campos[$i]['VALOR'] == ''){
										$retorno['STATUS'] = 'ERRO';
										$retorno['MSG']    .= 'Para titular menor de idade, o CPF do contratante é obrigatório.';
									}
									
									if($campos[$i]['CAMPO']=='NUMERO_CPF_CONTRATANTE' and $campos[$i]['VALOR'] != ''){
										if(!validaCPF($campos[$i]['VALOR'])){
											$retorno['STATUS'] = 'ERRO';
											$retorno['MSG']    .= 'Número CPF do contratante inválido.';
										}
									}
									
									if($campos[$i]['CAMPO']=='NUMERO_RG_CONTRATANTE' and $campos[$i]['VALOR'] == ''){
										$retorno['STATUS'] = 'ERRO';
										$retorno['MSG']    .= 'Para titular menor de idade, o RG do contratante é obrigatório.';
									}
								}
							}
						}
					}

					if($_SESSION['perfilOperador'] == 'BENEFICIARIO__VO'){
						$numeroProtocoloGeral = GeraProtocoloGeralPs6450($codigoAssociado);			
					}else{
						$numeroProtocoloGeral = GeraProtocoloGeralPs6450($codigoAssociado, $_SESSION['codigoIdentificacao']);		
					}		
					
					for($i=0;$i<count($campos);$i++){
						if($campos[$i]['CAMPO']=='DIA_VENCIMENTO')
						{
							$campos[$i]['VALOR'] = $rowAssoc->DIA_VENCIMENTO;
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_VENDEDOR')
						{
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];
						}
						
						if($campos[$i]['CAMPO']=='PROTOCOLO_GERAL_PS6450')
						{
							$campos[$i]['VALOR'] = $numeroProtocoloGeral;
						}
					}
				}
				
				if(($tipo == 'INC')and($tabela=='PS6500')){			
						
					for($i=0;$i<count($campos);$i++)
					{			
						if($campos[$i]['CAMPO']=='CODIGO_PRESTADOR' and $_SESSION['perfilOperador']=='PRESTADOR'){
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];
							$prestador = $campos[$i]['VALOR'];
						}

						if(($campos[$i]['CAMPO']=='CODIGO_PRESTADOR_EXECUTANTE') and ($_SESSION['perfilOperador']=='PRESTADOR') and (retornaValorConfiguracao('PREENCH_PREST_EXEC') == 'SIM')){	
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];
						}
								
						if ($campos[$i]['CAMPO']=='AUTORIZADO_POR' and $_SESSION['perfilOperador']=='PRESTADOR'){
							$campos[$i]['VALOR'] = '1';
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){				
						   $codigoAssociado = $campos[$i]['VALOR'];
						}
						
						if ($campos[$i]['CAMPO']=='NOME_PESSOA'){	
							$indiceNome = $i;
						}	
						
						if($campos[$i]['CAMPO']=='PROCEDIMENTO_PRINCIPAL'){				
						   $procedimento = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='TIPO_GUIA'){				
						   $tipoGuia = $campos[$i]['VALOR'];
						}	
						
						if($campos[$i]['CAMPO']=='DATA_AUTORIZACAO'){				
						   $dataAutorizacao = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_ESPECIALIDADE'){				
						   $especialidade = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='CODIGO_CID'){				
						   $cid = $campos[$i]['VALOR'];
						}	
					
						if($campos[$i]['CAMPO']=='NUMERO_AUTORIZACAO'){				
						   $autorizacao = $campos[$i]['VALOR'];
						}
						
						if (retornaValorConfiguracao('FLAG_VALIDA_PLANO_TRATAMENTO') == 'SIM'){
							if($campos[$i]['CAMPO']=='CODIGO_PLANO_TRATAMENTO'){
								$numeroRegistro = $campos[$i]['VALOR'];
							}
						}
					}
					
					
					$query = "SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = " . aspas($codigoAssociado);
					$res  = jn_query($query);
					$row = jn_fetch_object($res);
						
					$campos[$indiceNome]['VALOR'] = substr($row->NOME_ASSOCIADO,0,38);
							
					if (!ValidarPreAutorizacao($codigoAssociado, $prestador, $procedimento, $tipoGuia, 1, $dataAutorizacao, 'W', $especialidade, $cid, $autorizacao, $result)) {
					
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    .= '';
						foreach($result as $item)
							$retorno['MSG']    .= jn_utf8_encode($item).'<br>';
						}
						
					if ((retornaValorConfiguracao('VALIDA_PLANO_TRATAMENTO') == 'SIM') AND ($numeroRegistro != '')){
						$queryPlanoTratamento = 'SELECT NUMERO_REGISTRO FROM ESP_PLANO_TRATAMENTO WHERE DATA_ENCERRAMENTO IS NULL AND NUMERO_REGISTRO =' . aspas($numeroRegistro);		
						
						$resPlanoTratamento = jn_query($queryPlanoTratamento);
						
						if(!$rowPlanoTratamento = jn_fetch_object($resPlanoTratamento)){		
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Plano de tratamento encerrado pela APS, ou inixistente.';
						}		
					}
				}
					
				
				if(($tipo == 'INC')and($tabela=='PS6510')){
							
					for($i=0;$i<count($campos);$i++){
						if($campos[$i]['CAMPO']=='NUMERO_AUTORIZACAO'){				
							$autorizacao = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='CODIGO_PROCEDIMENTO'){				
							$procedimento = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='QUANTIDADE_PROCEDIMENTOS'){	
							if(trim($campos[$i]['VALOR'])=='')
								$campos[$i]['VALOR'] = 1;

							$qte = $campos[$i]['VALOR'];
						}	

						if ($campos[$i]['CAMPO']=='SITUACAO'){	
							$indiceAuditoria = $i;
						}				
						
						if($campos[$i]['CAMPO']=='CODIGO_MEDICAMENTO_MATERIAL'){
							$codigoMatMed = $campos[$i]['VALOR'];
						}
					}
					
					$query = "Select TIPO_AUTORIZACAO From Ps5210 Where Codigo_Procedimento =  " . aspas($procedimento);
					$res  = jn_query($query);
					$row = jn_fetch_object($res);
						
					$row->TIPO_AUTORIZACAO = substr($row->TIPO_AUTORIZACAO, 0, 1);
						
					if ($row->TIPO_AUTORIZACAO == 'A'){
						$auditoria = 'P';  // Pendente de auditoria
					}	
					else{
						$auditoria = 'A'; //Autorizado
					}
					
					$campos[$indiceAuditoria]['VALOR'] = $auditoria;
							
					
					$queryAutorizacao = "select * from ps6500 where numero_autorizacao =".aspas($autorizacao);
					$resAutorizacao = jn_query($queryAutorizacao);
					$rowAutorizacao = jn_fetch_object($resAutorizacao);

					if($rowAutorizacao->TIPO_GUIA == 'S'){
						$queryQuantProc = "select count(*) as QUANT_PROC from ps6510 where numero_autorizacao =".aspas($autorizacao);
						$resQuantProc = jn_query($queryQuantProc);
						$rowQuantProc = jn_fetch_object($resQuantProc);

						if($rowQuantProc->QUANT_PROC > 4){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'A guia SADT tem o limite de cinco procedimentos. Favor cadastrar uma nova autorização.';
						}

						if(retornaValorConfiguracao('NAO_APRESENTAR_CODIGO_10101012') == 'SIM'){				
						    if ($procedimento == '10101012'){
							   	$retorno['STATUS'] = 'ERRO';
								$retorno['MSG']    .= 'Não é possivel cadastrar este procedimento 10101012 para este tipo de guia!';
							}
						}
					}
					
					if (!ValidarAutorizacaoProcedimento($procedimento, $qte, $rowAutorizacao->CODIGO_ASSOCIADO,
						$rowAutorizacao->CODIGO_PRESTADOR, $rowAutorizacao->CODIGO_PROFISSIONAL_SOLIC, substr($rowAutorizacao->TIPO_GUIA, 0, 1), $rowAutorizacao->DATA_PREVISAO_INTERNACAO, $rowAutorizacao->DATA_PROCEDIMENTO, $rowAutorizacao->CODIGO_ESPECIALIDADE, $result)) {
						
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    .= '';
						foreach($result as $item)
							$retorno['MSG']    .= jn_utf8_encode($item).'<br>';
					}

					if(retornaValorConfiguracao('APRESENTA_MATMED_PS5760') == 'SIM'){			
						if($procedimento == '' && $codigoMatMed == ''){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'É necessário o preenchimento do campo codigo procedimento ou do material medicamento.';
						}
					}
					if(retornaValorCFG0003('CALCULA_COPART_AUT_WEB')=='SIM'){
						$query = "Select Tipo_Autorizacao From Ps5210 Where Codigo_Procedimento =  " . aspas($procedimento);
						$res  = jn_query($query);
						$row = jn_fetch_object($res);
						if($row->FLAG_1_PROC_AUT_WEB == 'S'){
							$queryQuantProc = "select  coalesce(sum(coalesce(PS6510.quantidade_procedimentos,1)),0) as QUANT_PROC from ps6510 where numero_autorizacao =".aspas($autorizacao)." and codigo_procedimento = ".aspas($procedimento);
							$resQuantProc = jn_query($queryQuantProc);
							$rowQuantProc = jn_fetch_object($resQuantProc);
							if(($rowQuantProc->QUANT_PROC+$qte) > 1){
								$retorno['STATUS'] = 'ERRO';
								$retorno['MSG']    .= 'Este procedimento so pode ser adicionado uma vez por autorização.';
							}
						}
					}
				}

				if(($tipo == 'INC')and($tabela=='PS2500')){
					
					$NumGuiaTiss = '';		
					if(retornaValorConfiguracao('GERA_GUIA_TISS_PL_TRAT') == 'SIM'){
						$query  = SP_GERA_NUMERO_GUIA_TISS( 'TRATAMENTO ODONTOLOGICO', date('y'), $APlanoTratamento['numero_plano_tratamento'] );
						$res = jn_query($query);
						$row    = jn_fetch_assoc($res);
						$NumGuiaTiss = $row['NUMERO_GUIA'];
					}
					
					
					for($i=0;$i<count($campos);$i++)
					{
						if($campos[$i]['CAMPO']=='CODIGO_PRESTADOR' and $_SESSION['perfilOperador']=='PRESTADOR'){
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];				
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){				
						   $codigoAssociado = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='NUMERO_GUIA'){				
						   $campos[$i]['VALOR'] = $NumGuiaTiss;
						}
							
						$query = "SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = " . aspas($codigoAssociado);
						$res  = jn_query($query);
						$row = jn_fetch_object($res);
							
						if($campos[$i]['CAMPO']=='NOME_PESSOA'){
						   $campos[$i]['VALOR'] = substr($row->NOME_ASSOCIADO,0,38);
						}
						
						if($campos[$i]['CAMPO']=='DATA_VALIDADE'){
						   $campos[$i]['VALOR'] = DataToSql(date('d/m/Y', strtotime('+30 days')));
						}
									
					}
					if(retornaValorConfiguracao('VERIFICA_INADIMPLENCIA_2510') == 'SIM'){
						$queryInadimplente = ' Select first 1 QUANTIDADE_FATURAS_EM_ABERTO
															from SP_PARAM_RESUMO_BENEFICIARIO ('.aspas($codigoAssociado).')'; 
						$resInadimplente  = jn_query($queryInadimplente);
						$rowInadimplente = jn_fetch_object($resInadimplente);
						if($rowInadimplente->QUANTIDADE_FATURAS_EM_ABERTO>0){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= '<font color="red">BENEFICIÁRIO FAVOR ENTRAR EM CONTATO COM A CENTRAL DE ATENDIMENTO</font>';
						}
					}
					
					if(retornaValorConfiguracao('ATUALIZA_TELEFONE_PS2500') == 'SIM'){			
						$retorno['CODIGO_ASSOCIADO_TEL']  = $codigoAssociado;
					}

				}
				
				if(($tipo == 'INC')and($tabela=='PS6451')){			

					$tabelaRemetente = '';
					$tabelaDestinatario = '';
					$perfilDestinatario = '';

					for($i=0;$i<count($campos);$i++)
					{			

						if (($campos[$i]['CAMPO']=='NUMERO_REGISTRO') and ($campos[$i]['VALOR'] == '0')) {
							$campos[$i]['VALOR'] = '';				
						}

						if($campos[$i]['CAMPO']=='CODIGO_DESTINATARIO') {
							$campos[$i]['VALOR'] = '';				
						}

						if($campos[$i]['CAMPO']=='CODIGO_REMETENTE') {
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];				
						}

						if($campos[$i]['CAMPO']=='PERFIL_REMETENTE') {
							$campos[$i]['VALOR'] = $_SESSION['perfilOperador'];				
						}

						if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
							$tabelaRemetente = 'PS1000';
							$tabelaDestinatario = 'PS1100';
							$perfilDestinatario = 'OPERADOR';
						}elseif($_SESSION['perfilOperador'] == 'OPERADOR'){
							$tabelaRemetente = 'PS1100';
							$tabelaDestinatario = 'PS1000';
							$perfilDestinatario = 'BENEFICIARIO';
						}
						
						if($campos[$i]['CAMPO']=='TABELA_REMETENTE') {
							$campos[$i]['VALOR'] = $tabelaRemetente;				
						}

						if($campos[$i]['CAMPO']=='TABELA_DESTINATARIO') {
							$campos[$i]['VALOR'] = $tabelaDestinatario;				
						}

						if($campos[$i]['CAMPO']=='PERFIL_DESTINATARIO') {
							$campos[$i]['VALOR'] = $perfilDestinatario;				
						}

					}
				}
					
				if(($tabela=='PS2510')){
					
					if($tipo == 'ALT'){

						for($i=0;$i<count($campos);$i++){
							if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){				
								$registroItem = $campos[$i]['VALOR'];
							}
							
							if (retornaValorConfiguracao('VALIDAR_CONCLUSAO_PL_TRATAMENT') == 'SIM' || retornaValorConfiguracao('VALIDAR_CONCLUSAO_PL_TRATAMENT') == ''){
								$queryConcluido = "select DATA_CONCLUSAO_PROCEDIMENTO from ps2510 where numero_registro =".aspas($registroItem);
								$resConcluido = jn_query($queryConcluido);
								$rowConcluido = jn_fetch_object($resConcluido);
								
								if ($rowConcluido->DATA_CONCLUSAO_PROCEDIMENTO != ''){  //diferente de vazio
									$retorno['STATUS'] = 'ERRO';
									$retorno['MSG']    .= 'Este procedimento já foi transformado em sinistro e nao pode ser alterado';
								}
							}
						}
					}
					
					if (($retorno['STATUS'] == 'OK')and ($tipo != 'EXC')){
						
						for($i=0;$i<count($campos);$i++){
							if($campos[$i]['CAMPO']=='NUMERO_PLANO_TRATAMENTO'){				
								$planoTratamento = $campos[$i]['VALOR'];
							}
							if($campos[$i]['CAMPO']=='CODIGO_PROCEDIMENTO'){				
								$procedimento = $campos[$i]['VALOR'];
							}
							if($campos[$i]['CAMPO']=='QUANTIDADE_PROCEDIMENTOS'){	
								if(trim($campos[$i]['VALOR'])=='')
									$campos[$i]['VALOR'] = 1;
								$qte = $campos[$i]['VALOR'];
							}
							
							if($campos[$i]['CAMPO']=='CODIGO_PRESTADOR' and $_SESSION['perfilOperador']=='PRESTADOR'){
								$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];
								$prestador = $campos[$i]['VALOR'];
							}		
										
							if($campos[$i]['CAMPO']=='NUMERO_DENTE_SEGMENTO'){	
								$denteSegmento = $campos[$i]['VALOR'];
							}
							
							if($campos[$i]['CAMPO']=='FACES'){
								$faces = '';
								$facesMarcadas = substr($campos[$i]['VALOR'], 2);
								$facesMarcadas = explode(';',$facesMarcadas);
								foreach($facesMarcadas as $face){
									$face = explode('-',$face);
									$faces .= $face[0];
								}
							}
						}

						if($tipo == 'ALT'){
							$validar = true;
											
							$queryConcluido = "select * from ps2510 where numero_registro =".aspas($registroItem);
							$resConcluido = jn_query($queryConcluido);
							$rowConcluido = jn_fetch_object($resConcluido);
							
							if( $rowConcluido->CODIGO_PROCEDIMENTO == $procedimento && 
								$rowConcluido->QUANTIDADE_PROCEDIMENTOS == $qte && 
								$rowConcluido->NUMERO_DENTE_SEGMENTO == $denteSegmento){
								$validar = false;
							}
						}
						
						if($tipo == 'INC' || $validar == true){
							$queryPlanoTratamento = "select * from ps2500 where numero_plano_tratamento =".aspas($planoTratamento);
							$resPlanoTratamento = jn_query($queryPlanoTratamento);
							$rowPlanoTratamento = jn_fetch_object($resPlanoTratamento);
							
							$resultado = @ValidarAutorizacaoProcedimentoOdonto(
									$procedimento,
									$qte,
									$rowPlanoTratamento->CODIGO_ASSOCIADO,
									$prestador,
									'O',
									$denteSegmento,
									$rowPlanoTratamento->TIPO_ATENDIMENTO_ODONTO,
									$faces
								);
							
							for($i=0;$i<count($campos);$i++){
								if($campos[$i]['CAMPO']=='SITUACAO'){
									$campos[$i]['VALOR'] = $resultado['SITUACAO'];	
								}
							}
							
							// verifico se foi NEGADO
							
							
							if ($resultado['SITUACAO'] == 'N'){
								$retorno['STATUS'] = 'ERRO';
								$retorno['MSG']    .= sprintf('O procedimento %s não pode ser incluído. Motivo: %s', $ProcSolic[$i]['CodProc'], jn_utf8_encode($resultado['RESULTADO']));
							}
						}
					}
				}
					
				if(($tipo == 'INC')and($tabela=='PS5750')){			

					$codigoAssociado = '';
					$indexPessoa = '';
					for($i=0;$i<count($campos);$i++)
					{			
						if($campos[$i]['CAMPO']=='CODIGO_PRESTADOR' and $_SESSION['perfilOperador']=='PRESTADOR'){
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];				
						}
							
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){				
							$codigoAssociado = $campos[$i]['VALOR'];
						}		
						
						if($campos[$i]['CAMPO']=='NOME_PESSOA'){				
							$indexPessoa = $i;
						}
					}
					
					$query = "SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = " . aspas($codigoAssociado);
					$res  = jn_query($query);
					$row = jn_fetch_object($res);

					$campos[$indexPessoa]['VALOR'] = substr($row->NOME_ASSOCIADO,0,38);
					
				}
				
				if(($tabela=='PS5760')){
					for($i=0;$i<count($campos);$i++){
						if($campos[$i]['CAMPO']=='NUMERO_REGISTRO_PS5750'){				
							$planoTratamento = $campos[$i]['VALOR'];
						}
						if($campos[$i]['CAMPO']=='CODIGO_PRESTADOR'){	
							$prestador = $campos[$i]['VALOR'];
						}	

						if($campos[$i]['CAMPO']=='VALOR_COBRADO'){
							$campos[$i]['VALOR'] = str_replace(',','.',$campos[$i]['VALOR']);
						}			

						if($campos[$i]['CAMPO']=='VALOR_COBRADO'){
							$ArrValorCob = explode('.', $campos[$i]['VALOR']);	
							if(count($ArrValorCob) > 2){
								$campos[$i]['VALOR'] = $ArrValorCob[0] . $ArrValorCob[1] . '.' . $ArrValorCob[2];
							}
						}

						if($campos[$i]['CAMPO']=='CODIGO_TUSS'){				
							$procedimento = $campos[$i]['VALOR'];
						}			
						
						if($campos[$i]['CAMPO']=='CODIGO_MATMED'){
							$codigoMatMed = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='CODIGO_SERVICO'){
							$codigoServico = $campos[$i]['VALOR'];
						}

					}

					if((retornaValorConfiguracao('APRESENTA_MATMED_PS5760') == 'SIM') or (retornaValorConfiguracao('APRESENTA_SERVICO_PS5760') == 'SIM')) {
						if($procedimento == '' && $codigoMatMed == '' && $codigoServico == ''){
							
							$mensagem = 'É necessário o preenchimento do campo codigo procedimento ';

							if(retornaValorConfiguracao('APRESENTA_MATMED_PS5760'))
								$mensagem .= ' ou material e medicamento ';
							
							if(retornaValorConfiguracao('APRESENTA_SERVICO_PS5760'))
								$mensagem .= ' ou serviço ';

							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= $mensagem . '.';
						}
					}

					$codigosPreenchidos = 0;

					if((retornaValorConfiguracao('APRESENTA_MATMED_PS5760') == 'SIM') or (retornaValorConfiguracao('APRESENTA_SERVICO_PS5760') == 'SIM')){
						if($procedimento != ''){
							$codigosPreenchidos++;
						}
						if($codigoMatMed != ''){
							$codigosPreenchidos++;
						}
						if($codigoServico != ''){
							$codigosPreenchidos++;
						}
						if($codigosPreenchidos >= 2){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'É necessário preencher o código procedimento OU o material e medicamento OU o código serviço. Não é possivel cadastrar todos juntos!';
						}
					}

					if((retornaValorConfiguracao('VALIDA_VALORCOBRADO_PS5760') == 'SIM')){
						if($ArrValorCob[0] < 1){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Valor inválido! É necessário informar um valor maior que ZERO.';
						}
					}


				}	
				
				if(($tipo == 'INC')and($tabela=='TMP1000_NET')){
					$codigoAssociado 		= '';
					$codigoTitular 			= '';
					$indexCodigoTitular 	= 0;
					$tipoAssociado			= '';		
					$numeroCPF 				= '';		
					$codigoEmpresa 			= '';		
					$codigoPlano 			= '';		
					$codigoCNS 				= '';		
					$indexTabelaPreco 		= 0;
					$indexCodigoCarencia 	= 0;
					$indexDataAdmissao 		= 0;
					$dataAdmissaoEmpresa 	= 0;
					$dataNascimento 		= '';	
					$nomeAssociado 			= '';
					$nomeMae 				= '';	
					$numeroRG				= '';
					$codigoParentesco 		= '';	
					$dataRegistroCasamento	= '';

					for($i=0;$i<count($campos);$i++)
					{			
						if($campos[$i]['CAMPO']=='CODIGO_EMPRESA' and $_SESSION['perfilOperador']=='EMPRESA'){
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];				
						}
									
						if($campos[$i]['CAMPO']=='CODIGO_EMPRESA' and $_SESSION['perfilOperador']=='VENDEDOR'){
							$campos[$i]['VALOR'] = '400';				
						}

						if($campos[$i]['CAMPO']=='CODIGO_VENDEDOR' and $_SESSION['perfilOperador']=='VENDEDOR'){
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];				
						}

						if ($campos[$i]['CAMPO']=='FLAG_PLANOFAMILIAR' and $_SESSION['perfilOperador']=='EMPRESA'){
							$campos[$i]['VALOR'] = 'N';				
						}
						
						if ($campos[$i]['CAMPO']=='FLAG_ISENTO_PAGTO'){
							$campos[$i]['VALOR'] = 'N';
						}			
						
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO' ){
							$codigoAssociado = jn_gerasequencial('TMP1000_NET');
							$codigoAssociado *= -1;				
							$campos[$i]['VALOR'] = $codigoAssociado;
						}
						
						if($campos[$i]['CAMPO']== 'TIPO_ASSOCIADO'){
							$indexTipoAssociado = $i;
							$tipoAssociado = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_TITULAR'){
							$indexCodigoTitular = $i;				
							$codigoTitular =  $campos[$i]['VALOR'];				
						}
						
						if($campos[$i]['CAMPO']=='NOME_ASSOCIADO'){
							$nomeAssociado = $campos[$i]['VALOR'];
							$campos[$i]['VALOR'] = strtoupper(sanitizeString($campos[$i]['VALOR']));
							 
						}
						
						if($campos[$i]['CAMPO']=='NUMERO_CPF'){
							$campos[$i]['VALOR'] = sanitizeString($campos[$i]['VALOR']);
							$numeroCPF = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_EMPRESA'){				
							$codigoEmpresa = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_PLANO'){				
							$codigoPlano = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_CNS'){				
							$codigoCNS = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_TABELA_PRECO'){
							$indexTabelaPreco = $i;
						}
						
						if($campos[$i]['CAMPO']=='CODIGO_CARENCIA'){
							$indexCodigoCarencia = $i;
						}
						
						if($campos[$i]['CAMPO']=='DATA_ADMISSAO'){
							$indexDataAdmissao = $i;
						}
						
						if($campos[$i]['CAMPO']=='DATA_ADMISSAO_EMPRESA'){				
							$dataAdmissaoEmpresa = $campos[$i]['VALOR'];
						}
						
						if($campos[$i]['CAMPO']=='DATA_NASCIMENTO'){
							$dataNascimento = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='NOME_MAE'){
							$nomeMae = $campos[$i]['VALOR'];
							$campos[$i]['VALOR'] = strtoupper(sanitizeString($campos[$i]['VALOR']));				
					    }

					    if($campos[$i]['CAMPO']=='NUMERO_RG'){				
							$numeroRG = $campos[$i]['VALOR'];
					    }

						if($campos[$i]['CAMPO']=='CODIGO_PARENTESCO'){				
							$codigoParentesco = $campos[$i]['VALOR'];
						}

						if($campos[$i]['CAMPO']=='DATA_REGISTRO_CASAMENTO'){
							$dataRegistroCasamento = $campos[$i]['VALOR'];
						}
						
					}		
					
					if($codigoTitular != '') {
						$tipoAssociado = 'D';
						$campos[$indexTipoAssociado]['VALOR'] = $tipoAssociado;		
					}
					
					if($tipoAssociado == 'T'){
						$campos[$indexCodigoTitular]['VALOR'] = $codigoAssociado;
					}
					
					$queryCPF  = ' SELECT CODIGO_ASSOCIADO FROM TMP1000_NET ';
					$queryCPF .= " WHERE 	DATA_EXCLUSAO IS NULL AND REPLACE(REPLACE(NUMERO_CPF,'.',''),'-','') =" . aspas(sanitizeString($numeroCPF));
					$queryCPF .= ' 		AND CODIGO_EMPRESA =' . aspas($codigoEmpresa);
					$queryCPF .= ' 		AND CODIGO_PLANO =' . aspas($codigoPlano);
					$queryCPF .= ' 		AND ((FLAG_IMPORTADO IS NULL) or (FLAG_IMPORTADO <> "S")) ';
					$resCPF = jn_query($queryCPF);
					
					if($rowCPF = jn_fetch_object($resCPF)){
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    .= 'Já existe um cadastro com este CPF(2).';
					}
					
					$queryCPF  = ' SELECT CODIGO_ASSOCIADO FROM PS1000 ';
					$queryCPF .= " WHERE 	DATA_EXCLUSAO IS NULL AND REPLACE(REPLACE(NUMERO_CPF,'.',''),'-','') =" . aspas(sanitizeString($numeroCPF));
					$queryCPF .= ' 		AND CODIGO_EMPRESA =' . aspas($codigoEmpresa);
					$queryCPF .= ' 		AND CODIGO_PLANO =' . aspas($codigoPlano);
					$resCPF = jn_query($queryCPF);
					
					if($rowCPF = jn_fetch_object($resCPF)){
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    .= 'Já existe um cadastro com este CPF(3).';
					}
					
					if($numeroCPF != ''){
						if(!validaCPF($numeroCPF)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Número CPF inválido.';
						}
					}
					
					if(trim($codigoCNS) != ''){
					
						$queryCNS  = ' SELECT CODIGO_CNS FROM TMP1000_NET ';
						$queryCNS .= ' WHERE 	DATA_EXCLUSAO IS NULL AND CODIGO_CNS = ' . aspas($codigoCNS);;
						$queryCNS .= ' 		AND CODIGO_EMPRESA =' . aspas($codigoEmpresa);
						$queryCNS .= ' 		AND CODIGO_PLANO =' . aspas($codigoPlano);
						$queryCNS .= ' 		AND ((FLAG_IMPORTADO IS NULL) or (FLAG_IMPORTADO <> "S")) ';
						$resCNS = jn_query($queryCNS);
						
						if($rowCNS = jn_fetch_object($resCNS)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Já existe um cadastro com este CNS vinculado a esta empresa.';
						}
						
						$queryCNS  = ' SELECT CODIGO_CNS FROM PS1000 ';
						$queryCNS .= ' WHERE 	DATA_EXCLUSAO IS NULL AND CODIGO_CNS = ' . aspas($codigoCNS);;
						$queryCNS .= ' 		AND CODIGO_EMPRESA =' . aspas($codigoEmpresa);
						$queryCNS .= ' 		AND CODIGO_PLANO =' . aspas($codigoPlano);		
						$resCNS = jn_query($queryCNS);
						
						if($rowCNS = jn_fetch_object($resCNS)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Já existe um cadastro com este CNS vinculado a esta empresa.';
						}

						if(strlen($codigoCNS) != '15' and retornaValorConfiguracao('VALIDAR_CNS')){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'O Código CNS está incorreto, favor corrigir.';
						}

						if(retornaValorConfiguracao('VALIDAR_CNS') == 'SIM'){
							if(!validaCNS($codigoCNS)){
								$retorno['STATUS'] = 'ERRO';
								$retorno['MSG']    .= 'Número CNS inválido.';
							}
						}
					}

					if(retornaValorConfiguracao('VALIDAR_NOMES_CADASTRO') == 'SIM'){			
						if(!validaNome($nomeAssociado)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Nome do Associado inválido.';
						}
						
						if(!validaNome($nomeMae)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Nome da mãe inválido.';
						}
					}

					if(retornaValorConfiguracao('VALIDAR_RG') == 'SIM'){
						if(!validaRG($numeroRG)){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Número RG inválido.';
						}
					}
					
					$idade = calcularIdade($dataNascimento);		

					if(retornaValorConfiguracao('BLOQUEAR_TITULAR_MENOR') == 'SIM' && $idade < 18 && $tipoAssociado == 'T'){
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    .= 'Não é permitido o cadastro de titular menor de idade.';
					}		

					if($idade < 0){			
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    .= 'A data de nascimento está incorreta, favor corrigir.';
					}


					if($_SESSION['codigoSmart'] == '3423'){//Plena
						$queryDadosEmpresa	= ' SELECT TOP 1 * FROM PS1059';
						$queryDadosEmpresa .= ' WHERE CODIGO_EMPRESA = ' . aspas($codigoEmpresa);
						$queryDadosEmpresa .= ' AND CODIGO_PLANO = ' . aspas($codigoPlano);		

						$resDadosEmpresa = jn_query($queryDadosEmpresa);
						$rowDadosEmpresa = jn_fetch_object($resDadosEmpresa);		

						$campos[$indexTabelaPreco]['VALOR'] = $rowDadosEmpresa->CODIGO_TABELA_PRECO;
									
						
						$quantidadeDias 	= $rowDadosEmpresa->QUANTIDADE_DIAS_CARENCIA;
						$codigoComCarencia 	= $rowDadosEmpresa->CODIGO_COM_CARENCIA;
						$codigoSemCarencia 	= $rowDadosEmpresa->CODIGO_SEM_CARENCIA;
						$diaAdmissao 		= $rowDadosEmpresa->DIA_ADMISSAO;
						
						$dataAdmissaoEmpresa    = SqlToData($dataAdmissaoEmpresa);
						$dataAdmissaoEmpresa    = explode("/",$dataAdmissaoEmpresa); 
						$dataAdmissaoEmpresa	= mktime(0,0,0,$dataAdmissaoEmpresa[1],$dataAdmissaoEmpresa[0],$dataAdmissaoEmpresa[2]);						
						$dataAtual     = date('d/m/Y');
						$dataAtual     = explode("/",$dataAtual); 
						$dataAtual    = mktime(0,0,0,$dataAtual[1],$dataAtual[0],$dataAtual[2]);						
						$dias       = ($dataAtual-$dataAdmissaoEmpresa)/86400;
						$validaDias = ceil($dias);
									
						$codigoCarencia = '';
						if($validaDias >= $quantidadeDias){
							$codigoCarencia = $codigoComCarencia;
						}else{
							$codigoCarencia = $codigoSemCarencia;
						}
						
						$campos[$indexCodigoCarencia]['VALOR'] = $codigoCarencia;
								
						if($diaAdmissao){
							$dataAdmissao = date('d/m/Y');
							$diaAdmissao = str_pad($diaAdmissao, 2, 0, STR_PAD_LEFT);
							
							if(date('m') == '12'){
								$mes = '01';
								$mes = str_pad($mes, 2, 0, STR_PAD_LEFT);
								$ano = (date('Y') + 1);
								$dataAdmissao = $ano . '-' . $mes . '-' . $diaAdmissao;					
							}else{
								$mes = (date('m') + 1);
								$mes = str_pad($mes, 2, 0, STR_PAD_LEFT);
								$ano = date('Y');					
								$dataAdmissao = $ano . '-' . $mes . '-' . $diaAdmissao;										
							}

							if (($diaAdmissao >= '28') and ($mes == '02')){
								$diasNoMes = date("t", mktime(0, 0, 0, $mes, 1, $ano));
							    $dataAdmissao = $ano . '-' . $mes . '-' . $diasNoMes;	
							}

							$campos[$indexDataAdmissao]['VALOR'] = $dataAdmissao;					
						}

						
						if($codigoParentesco == 1 or $codigoParentesco == 3){//Filho ou Filha							
							
							$retornoDifDatas = (object) Array();

							$dataAtual = new DateTime(date('d-m-Y'));
							$dataNascimento = new DateTime(date($dataNascimento));   

							$retornoDifDatas = ($dataAtual->diff($dataNascimento));				
							
							$anos = $retornoDifDatas->format('%Y%');
							$meses = $retornoDifDatas->format('%m%');
							$dias = $retornoDifDatas->format('%d%');				

							
							if($anos == 0 and $meses == 0 and $dias < 30)
								$codigoCarencia = $codigoSemCarencia;				

							$campos[$indexCodigoCarencia]['VALOR'] = $codigoCarencia;				
							
						}
						
						if($codigoParentesco == 2 or $codigoParentesco == 8){//Conjuge ou Companheiro

							$retornoDifDatas = (object) Array();

							$dataAtual = new DateTime(date('d-m-Y'));
							$dataRegistroCasamento = new DateTime(date($dataRegistroCasamento));    
							
							$retornoDifDatas = ($dataAtual->diff($dataRegistroCasamento));

							$anos = $retornoDifDatas->format('%Y%');
							$meses = $retornoDifDatas->format('%m%');
							$dias = $retornoDifDatas->format('%d%');

							if($anos == 0 and $meses == 0 and $dias < 30)
								$codigoCarencia = $codigoSemCarencia;
							
							$campos[$indexCodigoCarencia]['VALOR'] = $codigoCarencia;				
						}
					}
					
				}

				if(($tabelaOrigem=='VND1000_ON')and($_SESSION['codigoSmart'] == '4206')){
					
					for($i=0;$i<count($campos);$i++){
						if($campos[$i]['CAMPO']=='CODIGO_VND1030CONFIG'){
							$registroPS1030Config = $campos[$i]['VALOR'];
							break;				
						}
					}
					
					$query = "SELECT * FROM VND1030CONFIG_ON WHERE NUMERO_REGISTRO = " . aspas($registroPS1030Config);
					$res   = jn_query($query);
					
					if($row = jn_fetch_object($res)){
						for($i=0;$i<count($campos);$i++){
							if($campos[$i]['CAMPO']=='CODIGO_PLANO'){
								$campos[$i]['VALOR'] = $row->CODIGO_PLANO;		
							}else if($campos[$i]['CAMPO']=='CODIGO_GRUPO_PESSOAS'){
								//$campos[$i]['VALOR'] = $row->CODIGO_GRUPO_PESSOAS_AUTOC;		
							}elseif($campos[$i]['CAMPO']=='CODIGO_GRUPO_CONTRATO'){
								$campos[$i]['VALOR'] = $row->CODIGO_GRUPO_CONTRATO_AUTOC;		
							}elseif($campos[$i]['CAMPO']=='TABELA_PRECO'){
								$campos[$i]['VALOR'] = $row->TABELA_PRECO_AUTOC;		
							}
						}
					}
								
					
				}	

				if(($tipo == 'INC')and($tabela=='TABELA_AVO')){			
						
					for($i=0;$i<count($campos);$i++)
					{			
						if($campos[$i]['CAMPO']=='CODIGO_ID_USUARIO'){				
						   $campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];
						}
					}
				}	


				if(($tipo == 'INC')and($tabela=='ESP_REEMBOLSO')){			
					
					$cpfCnpj = '';
					$nomePrestador = '';	
					for($i=0;$i<count($campos);$i++)
					{			
						if($campos[$i]['CAMPO']=='CNPJ_PRESTADOR'){
							$cpfCnpj = sanitizeString($campos[$i]['VALOR']);
						}else if($campos[$i]['CAMPO']=='NOME_PRESTADOR'){
							$nomePrestador = $campos[$i]['VALOR'];
						}
				
					}
						
					$queryPrest  = ' SELECT NOME_PROFISSIONAL FROM ESP_PROFISSIONAL_REEMBOLSO ';
					$queryPrest .= ' WHERE  ((ESP_PROFISSIONAL_REEMBOLSO.NUMERO_CNPJ = ' . aspas($cpfCnpj) . ') OR (ESP_PROFISSIONAL_REEMBOLSO.NUMERO_CPF = ' . aspas($cpfCnpj) . '))';
					$resPrest = jn_query($queryPrest);
					$campoTabela = '';
					if (!$rowPrest = jn_fetch_object($resPrest)){
						 if (strlen($cpfCnpj) == '11'){
						 	$campoTabela = 'NUMERO_CPF';
						 }else if(strlen($cpfCnpj) == '14'){
						 	$campoTabela = 'NUMERO_CNPJ';
						 }

						if($campoTabela != ''){
							jn_query('INSERT INTO ESP_PROFISSIONAL_REEMBOLSO (NOME_PROFISSIONAL, '. $campoTabela . ') VALUES ('. aspas($nomePrestador) . ',' . aspas($cpfCnpj) . ')');
							$retorno['STATUS'] = 'OK'; 
						}else{
						 	$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    .= 'Quantidade de caracteres para CPF/CNPJ está inconsistente!';				
						 }			 
						
					} 
			    } 
				if(($tipo=='INC') and ($retorno['STATUS'] == 'OK')){
					if(($tabela == 'PS6500')){
						if(retornaValorCFG0003('VALIDA_TOKEN_AUTORIZACAO')=='SIM' and $_SESSION['perfilOperador']=='PRESTADOR'){
							for($i=0;$i<count($campos);$i++){			
								if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
									$codigoAssociado = sanitizeString($campos[$i]['VALOR']);
								}
								if($campos[$i]['CAMPO']=='TOKEN'){
									$token = sanitizeString($campos[$i]['VALOR']);
								}
						
							}
							$queryPrincipal = "select * From ESP_TOKEN  WHERE DATA_UTILIZACAO is null and DATA_EXPIRACAO > GETDATE() and CODIGO_ASSOCIADO =" . aspas($codigoAssociado).' and TOKEN = '.aspas($token);
							$resultQuery    = jn_query($queryPrincipal);
							if($objResult      = jn_fetch_object($resultQuery)){
								$update = 'UPDATE ESP_TOKEN SET DATA_UTILIZACAO = GETDATE(),TABELA='.aspas($tabela).' WHERE NUMERO_REGISTRO ='. aspas($objResult->NUMERO_REGISTRO);
								jn_query($update);
							}else{
								validaTokenInvalido($codigoAssociado, $token, $tabela);

								$retorno['STATUS'] = 'ERRO';
								$retorno['MSG']    .= 'Token Invalido!';	
							}
						}
					}
					
					if(($tabela == 'PS6550')){
						if(retornaValorCFG0003('VALIDA_TOKEN_SOLICITACAO')=='SIM' and $_SESSION['perfilOperador']=='PRESTADOR'){
							for($i=0;$i<count($campos);$i++){			
								if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
									$codigoAssociado = sanitizeString($campos[$i]['VALOR']);
								}
								if($campos[$i]['CAMPO']=='TOKEN'){
									$token = sanitizeString($campos[$i]['VALOR']);
								}
						
							}
							$queryPrincipal = "select * From ESP_TOKEN  WHERE DATA_UTILIZACAO is null and DATA_EXPIRACAO > GETDATE() and CODIGO_ASSOCIADO =" . aspas($codigoAssociado).' and TOKEN = '.aspas($token);
							$resultQuery    = jn_query($queryPrincipal);
							if($objResult      = jn_fetch_object($resultQuery)){
								$update = 'UPDATE ESP_TOKEN SET DATA_UTILIZACAO = GETDATE(),TABELA='.aspas($tabela).' WHERE NUMERO_REGISTRO ='. aspas($objResult->NUMERO_REGISTRO);
								jn_query($update);
							}else{
								validaTokenInvalido($codigoAssociado, $token, $tabela);

								$retorno['STATUS'] = 'ERRO';
								$retorno['MSG']    .= 'Token Invalido!';	
							}
						}
					}	
				}

				if(($tipo == 'INC')and($tabela=='PS6130')){
				
					for($i=0;$i<count($campos);$i++){			

						if($campos[$i]['CAMPO']=='CODIGO_OPERADOR'){
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];
						}			
					}
				}

	}

}

function ValidarPreAutorizacao($ACodigoAssociado, $APrestador, $AProcedimento, $ATipoGuia, $AQte, $ADataAutorizacao, $AFonte, $AEspecialidade, 
	$ACid, $AAutorizacao, &$result) {

	/* Em 17/07/2020 conversei com o Leo, Diego e Karen e o Leo explicou que esta procedure é específica do sistema e NÃO DEVE SER UTILIZADA NA WEB, pois na web utiliza-se a procedure "SP_VALIDAPROCEDIMENTOS_NET" (logo abaixo)
	   então eu comentei o código e estou retornando "true".
	   A Karen também informou que ela só inseriu esta procedure para compatibilizar com o Delphi, mas como ela não trata restrições específicas de urgência/emergência, final de semana, etc... é melhor tirar.

    $result = array();
   
	$query = "SELECT VALIDACAO FROM SP_VALIDAPREAUTORIZACAO" . $versaoProcedure . "('$ACodigoAssociado', '$APrestador', '$AProcedimento', '$ATipoGuia', '$AQte','$ADataAutorizacao', '$AFonte', '$AEspecialidade', '$ACid', '$APrestador', '$AAutorizacao')";
		
    $resValidacao = jn_query($query);
    if($rowValidacao = @jn_fetch_object($resValidacao)){
		
		
		$resposta       = explode(';', $rowValidacao->VALIDACAO);
		
		$resposta[0]    = str_replace('<BR>', '', $resposta[0]);
		$result = $resposta;
		
		if($result[0]=='OK')
			return true;
		else{
			//$result[0] = 'Erro ao tentar validar o beneficiario.(01)';
			$result;
			return false;
		}
	}else{
		$result[0] = 'Erro ao tentar validar o beneficiario.(02) ';
		return false;	
	}
	
	*/
	
	return true;
	
	
}


function ValidarAutorizacaoProcedimento($ACodigoProcedimento, $AQtdeProcedimento, $ACodigoBenef,
    $ACodigoPrestador, $ACodigoPrestadorSol, $ATipoAutorizacao, $ADataInternacao, $ADataProcedimento, $AEspecialidadePrestador, &$result) {

    global $TotalCoParticipacao;
    $result = array();
   	
   	$ACid = '';

    if ($_SESSION['AliancaPx4Net']=='S')
    {
				$query = "SELECT VALIDACAO FROM SP_VALIDAPREAUTORIZACAO(" . aspas($ACodigoBenef) . ", " . aspas($ACodigoPrestador) . ", " 
					                                                        . aspas($ACodigoProcedimento) . " , " . aspas($ATipoAutorizacao) . ", "
					                                                        . aspas($AQtdeProcedimento) . "," . dataToSql(date('d/m/Y')) . ", "
					                                                        . aspas('D') . ", " . aspas($AEspecialidadePrestador) . ", "  
					                                                        . aspas($ACid) . ", " . aspas($ACodigoPrestadorSol) . ")";
    }
    else if (retornaValorConfiguracao('FLAG_PAR_SOLIC_VALIDAPROCED') == 'SIM') // Se configurado, passará para a procedure o código do solicitante no ultimo parâmetro.
	  {
	       $query = "SELECT VALIDACAO FROM SP_VALIDAPROCEDIMENTOS_NET" . $versaoProcedure . "('$ACodigoBenef','$ACodigoProcedimento', " . integerNull($ACodigoPrestador) . ", '$ATipoAutorizacao', " . integerNull($ACodigoPrestadorSol) . ")";
	  }elseif (retornaValorConfiguracao('VALIDAR_QNT_PROC_AUT') == 'SIM'){ // Se configurado, irá validar a quantidade de procedimenos solicitados		
		     $query = "SELECT VALIDACAO FROM SP_VALIDAPROCEDIMENTOS_NET" . $versaoProcedure . "('$ACodigoBenef','$ACodigoProcedimento', " . integerNull($ACodigoPrestador) . ", '$ATipoAutorizacao', " . integerNull($ACodigoPrestadorSol) . ", " . integerNull($AQtdeProcedimento) .")";
	  }
	  else
	  {
	      $query = "SELECT VALIDACAO FROM SP_VALIDAPROCEDIMENTOS_NET" . $versaoProcedure . "('$ACodigoBenef','$ACodigoProcedimento', " . integerNull($ACodigoPrestador) . ", '$ATipoAutorizacao')";
	  }

		if($_SESSION['codigoSmart'] == '3419'){
			
			$query = "SELECT VALIDACAO FROM SP_VALIDAPROCEDIMENTOS_NET" . $versaoProcedure . "('$ACodigoBenef','$ACodigoProcedimento', " . integerNull($ACodigoPrestador) . ", '$ATipoAutorizacao', " . integerNull($ACodigoPrestadorSol) . ", " . integerNull($AQtdeProcedimento) .")";
		}	
		
    $resValidacao = jn_query($query);
    if($rowValidacao = @jn_fetch_object($resValidacao)){
		
		
		$resposta       = explode(';', $rowValidacao->VALIDACAO);
		
		$resposta[0]    = str_replace('<BR>', '', $resposta[0]);
		$result = $resposta;

		if ($result[1] > 0) {
			$TotalCoParticipacao = $TotalCoParticipacao + $result[1];
		}
		if($result[0]=='OK')
			return true;
		else{
			$result[0] = 'Erro ao tentar validar o procedimento[1].  ' . $rowValidacao->VALIDACAO;
			return false;
		}
	}else{
		$result[0] = 'Erro ao tentar validar o procedimento[2].  ' . $rowValidacao->VALIDACAO;
		return false;	
	}

    
}

function ValidarAutorizacaoProcedimentoOdonto($ACodigoProcedimento, $AQtdeProcedimento, $ACodigoBenef,
    $ACodigoPrestador, $ATipoAutorizacao, $ANumeroDente, $ATipoAtend, $AFace) {

    $resultado = array(
        'SITUACAO'  => '',
        'RESULTADO' => ''
    );

    /**
     * Exemplos de chamado da procedure:
     * Select * from SP_ValidaProcedimentosOdontoWeb("000060101","120",10,"O", 32)
     * Select * from SP_ValidaProcedimentosOdontoWeb(código_beneficiario, código_procedimento, código_prestador, tipo_guia, numero_dente)
     * --
     * Nome da coluna retornada: VALIDACAO
     * --
     * Valores de retorno da procedure:
     * OK;1150.220000000000;NECESSITA PERICIA
     * OK;1150.220000000000;OUTROS
     * OK;0.0000000000000000;OUTROS
     * Ou
     * Erros
     */

    $query = "SELECT VALIDACAO FROM SP_ValidaProcedimentosOdontoWeb('$ACodigoBenef','$ACodigoProcedimento', $ACodigoPrestador, '$ATipoAutorizacao', $ANumeroDente, '$ATipoAtend', '$AQtdeProcedimento', '$AFace')";
    //$query = "SELECT VALIDACAO FROM SP_ValidaProcedimentosOdontoWeb('$ACodigoBenef','$ACodigoProcedimento', $ACodigoPrestador, '$ATipoAutorizacao', $ANumeroDente)";
    //echo $query; exit();
    $resValidacao = jn_query($query);
    $rowValidacao = jn_fetch_object($resValidacao);

    $validacao = $rowValidacao->VALIDACAO;

    // separo os elementos do resultado pelo caracter ';'
    $arrValidacao = array();
    $arrValidacao = explode(';', $validacao);	
    
    if ($arrValidacao[0] == "OK") {
        // verifico se aprovado ou aguardando...
        if ($arrValidacao[2] != 'NECESSITA PERICIA') {			
            $resultado['SITUACAO'] = 'A'; // aprovado
        }
        else {			
			$resultado['SITUACAO'] = 'G'; // aguardando            
        }

        $resultado['RESULTADO'] = empty($arrValidacao[1]) ? 0 : ($arrValidacao[1] * 1); // converto para inteiro...
    }
    else {
        $resultado['SITUACAO'] = 'N';
        $resultado['RESULTADO'] = $arrValidacao[0];
    }
		
    return $resultado;
}

function calcularIdade($dataNascimento){	

	if(is_object($dataNascimento)){		
		$dataAtual = new DateTime(date('d-m-Y'));
		$retornoDifDatas = ($dataAtual->diff($dataNascimento));
		$idade = $retornoDifDatas->format('%Y%');
	}else{		
		// separando yyyy, mm, ddd
		list($ano, $mes, $dia) = explode('-', $dataNascimento);

		if($ano > date('Y')){		
			return -1;
		}else{				
			// data atual
			$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			// Descobre a unix timestamp da data de nascimento do fulano
			$nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

			// cálculo
			$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);			
		}
	}
	
    return $idade;
}
?>
