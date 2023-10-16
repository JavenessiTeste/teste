<?php


if(@$_GET['CELULAR']=='OK')
{
	$celular= true;	
}
else
{
	$celular= false;
	require('../lib/base.php');
}



if ($_SESSION['codigoIdentificacao']=='')
{
	$_SESSION['AliancaPx4Net'] 		   = 'N';
	$_SESSION['CFGCONFIGURACOES_NET']  = array();
	$_SESSION['CFG0003']  		       = array();

	//jn_query('insert into teste(campo) values("bb")',false,true,true);
}
else
{
	//jn_query('insert into teste(campo) values("cc")',false,true,true);
}

// Aqui eu limpo o protolo, pq se o cara navegou é porque ele mudou de usuário.
$_SESSION['NUMERO_PROTOCOLO_ATIVO'] = '';



if($dadosInput['tipo']=='login'){
    $_SESSION['SYSDB'] = false;

	$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : $dadosInput['dados']['usuario'];
	$dadosInput['dados']['perfil'] = isset($_POST['perfil_operador']) ? $_POST['perfil_operador'] : $dadosInput['dados']['perfil'];
	$dadosInput['dados']['senha'] = isset($_POST['password']) ? $_POST['password'] : $dadosInput['dados']['senha'];

	$numeroCpf = $dadosInput['dados']['cpf'];
	$numeroCarteirinha = $dadosInput['dados']['carteirinha']; 
	$auditor = false;
	$auditorAgendamento = false;
	$processoReativacao = false;

	if($dadosInput['dados']['perfil'] == 'OPERADOR_AUDITOR'){
		$auditor = true;
		$dadosInput['dados']['perfil'] = 'OPERADOR';		
	}else if($dadosInput['dados']['perfil'] == 'OPERADOR_AUDITOR_AG'){
		$auditorAgendamento = true;
		$dadosInput['dados']['perfil'] = 'OPERADOR';		
	}

	if(($dadosInput['dados']['perfil']!='')and($dadosInput['dados']['perfil']!='undefined')and($dadosInput['dados']['perfil']!=null)){
		if (retornaValorConfiguracao('VALIDA_VENDEDOR_CORRETOR') == 'SIM' and $dadosInput['dados']['perfil'] == 'VENDEDOR') { 
			$perfil = ' and (PERFIL_OPERADOR ='.aspas($dadosInput['dados']['perfil']).' OR (PERFIL_OPERADOR = "CORRETOR")) ';			
		}else if (retornaValorConfiguracao('VALIDA_CORRETOR_VENDEDOR') == 'SIM' and $dadosInput['dados']['perfil'] == 'CORRETOR') { 
			$perfil = ' and (PERFIL_OPERADOR ='.aspas($dadosInput['dados']['perfil']).' OR (PERFIL_OPERADOR = "VENDEDOR")) ';
		}else{			
			$perfil = ' and PERFIL_OPERADOR ='.aspas($dadosInput['dados']['perfil']).' ';
		}		
	}else{
		$perfil = '';
	}
	
	//Caso queira imagem Perfil Colocar o link da imagem nesse campo
	//$resultado['DADOS']['imagemPerfil'] = '';

	$_SESSION['senhaPadrao'] = 'N';

	if (!rvc('VALIDA_SENHA_CRIPTOGRAFADA', 'SIM')) { 
		$pesquisaSenha = 'SENHA_ACESSO';	
		//$pesquisaSenha = '(SENHA_ACESSO)';
		
	} else { 	
		$pesquisaSenha = 'SENHA_CRIPTOGRAFADA';	
	} 

	//regra para validar SQL Injection
	$offset =0;
	while (($pos = strpos($dadosInput['dados']['senha'], "or ", $offset)) !== false) {		
		$dadosInput['dados']['senha'] = '*(--**&¨%(*$$$$$$$99&¨%';
	}

	while (($pos = strpos($dadosInput['dados']['senha'], "1=1", $offset)) !== false) {		
		$dadosInput['dados']['senha'] = '*(--**&¨%(*$$$$$$$99&¨%';
	}


	if (!rvc('VALIDA_SENHA_CRIPTOGRAFADA', 'SIM')) { 
		$senha = $dadosInput['dados']['senha'];
		$senha = ($senha);
	} else { 
		$senha = $dadosInput['dados']['senha'];
		$senha = md5($senha);
	} 

	if (!rvc('VALIDA_SENHA_CRIPTOGRAFADA', 'SIM')) { 
		$validacaoAdicional = ' or (UPPER(SENHA_ACESSO) = ' . aspas(strtoupper($senha)) . ')) ';
	}
	else
	{
		$validacaoAdicional = ' ) ';
	}
	
	//regra para validar SQL Injection
	$offset =0;
	while (($pos = strpos($usuario, "or ", $offset)) !== false) {
		$usuario = '*(--**&¨%(*$$$$$$$99&¨%';
	}
	
	while (($pos = strpos($usuario, "1=1", $offset)) !== false) {
		$usuario = '*(--**&¨%(*$$$$$$$99&¨%';
	}	

	
	$loginWeb['flagSucesso'] = 'N';
	$resultado['LOGADO'] = true;
	$resultado['MSG']    = 'Você foi logado com sucesso.';

	// Busco informações sobre o usuriario que esta tentando se logar

	if($usuario){		
		if($dadosInput['dados']['perfil'] == 'BENEFICIARIO' && retornaValorConfiguracao('UTILIZA_VW_LOGIN') == 'SIM'){
			$query = 'SELECT TOP 1 USUARIO, CODIGO_IDENTIFICACAO, DATA_ALTERACAO_SENHA, PERFIL_OPERADOR FROM VW_DADOS_LOGIN_NET WHERE ((UPPER(USUARIO) = ' . strtoupper(aspas($usuario)) . ') OR(CODIGO_IDENTIFICACAO = ' . strtoupper(aspas($usuario)) . ')) AND UPPER(SENHA) = ' . strtoupper(aspas($dadosInput['dados']['senha']))  . ' AND UPPER(PERFIL_OPERADOR) = ' . aspas($dadosInput['dados']['perfil']) . ' ORDER BY DATA_EXCLUSAO ';			
		}else{		
			$query = 'SELECT  NUMERO_REGISTRO, USUARIO, CODIGO_IDENTIFICACAO, DATA_ALTERACAO_SENHA, PERFIL_OPERADOR FROM CFGLOGIN_DINAMICO_NET WHERE UPPER(USUARIO) = ' . strtoupper(aspas($usuario)) . ' AND (' . $pesquisaSenha . ' = ' . aspas($senha) . $validacaoAdicional . $perfil ;
		}		
		$res = jn_query($query);

		// Array que armazena os dados vindo da tabela de logins da web
		$quantidadeUsuarios = 0;

		while($row = jn_fetch_object($res)){
			$loginWeb['perfilOperador']         		= $row->PERFIL_OPERADOR;
			$loginWeb['codigoIdentificacao']        	= $row->CODIGO_IDENTIFICACAO;
			$loginWeb['flagSucesso']		   			= 'S';
			$_SESSION['dtAltSenha']		   				= $row->DATA_ALTERACAO_SENHA;
			
			if(strtoupper($senha) == 'PLENA123'){
				$_SESSION['senhaPadrao'] = 'S';
			}
			$quantidadeUsuarios++;
		}
		

		if ($loginWeb['flagSucesso'] != 'N'){
			switch($loginWeb['perfilOperador']){
			   case 'PRESTADOR':
					//$query = 'SELECT CODIGO_PRESTADOR ,NOME_PRESTADOR FROM PS5000 WHERE CODIGO_PRESTADOR = ' . aspas($loginWeb['codigoIdentificacao']);
				$query  = ' SELECT CODIGO_PRESTADOR ,NOME_PRESTADOR FROM PS5000 ';
				$query .= ' WHERE ((DATA_DESCREDENCIAMENTO IS NULL) or (DATA_DESCREDENCIAMENTO >= current_timestamp)) AND CODIGO_PRESTADOR = ' . aspas($loginWeb['codigoIdentificacao']);
				
					$res = jn_query($query);
					if($row = jn_fetch_object($res)){
					   $codigoIdentificacao     = $row->CODIGO_PRESTADOR;
					   $nomeUsuario             = $row->NOME_PRESTADOR;
					}else{
						
						$resultado['LOGADO'] = False;
						$resultado['MSG']    = 'Prestador não encontrado ou excluído, reveja os dados digitados e tente novamente!';	
					}
					break;
				case 'EMPRESA':
				$query  = 'SELECT CODIGO_EMPRESA, Nome_Empresa FROM ps1010 ';
				$query .= ' WHERE (DATA_EXCLUSAO IS NULL or (DATA_EXCLUSAO >= current_timestamp)) AND CODIGO_EMPRESA = ' . aspas($loginWeb['codigoIdentificacao']);
					$res = jn_query($query);
					if($row = jn_fetch_object($res)){
					   $codigoIdentificacao     = $row->CODIGO_EMPRESA;
					   $nomeUsuario             = $row->NOME_EMPRESA;
					}else{
						$resultado['LOGADO'] = False;
						$resultado['MSG']    = 'Empresa não encontrada ou excluída, reveja os dados digitados e tente novamente!';	
					}
					break;
				case 'BENEFICIARIO':			
					$queryEmpresa = ' SELECT CFGEMPRESA.CODIGO_SMART FROM CFGEMPRESA ';			
					$resEmpresa  = jn_query($queryEmpresa);
					$rowEmpresa = jn_fetch_object($resEmpresa);			
					$EmpresaCfg = $rowEmpresa->CODIGO_SMART;
					
					$query = 'SELECT CODIGO_ASSOCIADO ,NOME_ASSOCIADO, CODIGO_TITULAR FROM PS1000'; 
					if ($EmpresaCfg == '3419'){//Medical 
						$query .= '	INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO)
									WHERE DATA_EXCLUSAO IS NULL AND PS1030.CODIGO_TIPO_COBERTURA <> "3" AND CODIGO_ASSOCIADO = ' . aspas($loginWeb['codigoIdentificacao']);
					}else{
						$query .= ' WHERE (DATA_EXCLUSAO IS NULL or (DATA_EXCLUSAO >= current_timestamp)) AND CODIGO_ASSOCIADO = ' . aspas($loginWeb['codigoIdentificacao']);
					}
									
					$res = jn_query($query);
					if($row = jn_fetch_object($res)){
					   $codigoIdentificacao         = $row->CODIGO_ASSOCIADO;
					   $codigoIdentificacaoTitular  = $row->CODIGO_TITULAR;
					   $nomeUsuario                 = $row->NOME_ASSOCIADO;
					}else{

						if (retornaValorConfiguracao('PROCESSO_REATIVACAO') == 'SIM'){
							$queryReativ  = 'SELECT * FROM SP_REATIVACAO_PF(' . aspas($loginWeb['codigoIdentificacao'])  . ')';
							
							$resReativ = jn_query($queryReativ);
							if($rowReativ = jn_fetch_object($resReativ)){
								if($rowReativ->PERMITE_REATIVACAO == 'S'){
									$query = 'SELECT CODIGO_ASSOCIADO, NOME_ASSOCIADO, CODIGO_TITULAR FROM PS1000'; 								
									$query .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($loginWeb['codigoIdentificacao']);
									$res = jn_query($query);
									$row = jn_fetch_object($res);
									$codigoIdentificacao         = $row->CODIGO_ASSOCIADO;
									$codigoIdentificacaoTitular  = $row->CODIGO_TITULAR;
									$nomeUsuario                 = $row->NOME_ASSOCIADO;
									$processoReativacao = true;

									jn_query('exec SP_GERA_BOLETO_REAT @ACodigoAssociado = ' . aspas($codigoIdentificacao));
								}
							}
						}
						
						
						if(!$processoReativacao){

							$mensagemReativacao = false;

							if (retornaValorConfiguracao('PROCESSO_REATIVACAO') == 'SIM'){
								$queryExc  = ' SELECT CODIGO_MOTIVO_EXCLUSAO FROM PS1000 '; 								
								$queryExc .= ' WHERE (CODIGO_MOTIVO_EXCLUSAO = "4" OR CODIGO_MOTIVO_EXCLUSAO = "1") AND CODIGO_ASSOCIADO = ' . aspas($loginWeb['codigoIdentificacao']);
								$resExc = jn_query($queryExc);
								if($rowExc = jn_fetch_object($resExc)){
									$mensagemReativacao = true;
								}
								
							}

							if($mensagemReativacao){								
								$resultado['LOGADO'] = False;
								$resultado['MSG']    = 'Caro beneficiário, por gentileza entre em contato com nossa central de atendimento através dos telefones 11 4445-9080 ou pelo WhatsApp 11 91348-2236. ';
							}else{
								$resultado['LOGADO'] = False;
								$resultado['MSG']    = 'Beneficiário não encontrado ou excluído, reveja os dados digitados e tente novamente!';						
							}							
						}
					}
					break;
				case 'CORRETOR':
					$query = 'SELECT CODIGO_IDENTIFICACAO ,NOME_USUAL FROM PS1100 WHERE DATA_EXCLUSAO IS NULL AND CODIGO_IDENTIFICACAO = ' . aspas($loginWeb['codigoIdentificacao']);
					$res = jn_query($query);
					if($row = jn_fetch_object($res)){
					   $codigoIdentificacao     = $row->CODIGO_IDENTIFICACAO;
					   $nomeUsuario             = $row->NOME_USUAL;
					}else{
						$resultado['LOGADO'] = False;
						$resultado['MSG']    = 'Corretor não encontrado ou excluído, reveja os dados digitados e tente novamente!';											
					}
					break;	
				case 'OPERADOR':
					$query = 'SELECT CODIGO_IDENTIFICACAO ,NOME_USUAL FROM PS1100 WHERE DATA_EXCLUSAO IS NULL AND CODIGO_IDENTIFICACAO = ' . aspas($loginWeb['codigoIdentificacao']);
					$res = jn_query($query);
					if($row = jn_fetch_object($res)){
					   $codigoIdentificacao     = $row->CODIGO_IDENTIFICACAO;
					   $nomeUsuario             = $row->NOME_USUAL;
					}else{
						$resultado['LOGADO'] = False;
						$resultado['MSG']    = 'Operador não encontrado ou excluído, reveja os dados digitados e tente novamente!';											
					}
					break;	
				case 'VENDEDOR':
					$query = 'SELECT CODIGO_IDENTIFICACAO ,NOME_USUAL FROM PS1100 WHERE DATA_EXCLUSAO IS NULL AND CODIGO_IDENTIFICACAO = ' . aspas($loginWeb['codigoIdentificacao']);			
					$res = jn_query($query);
					if($row = jn_fetch_object($res)){
					   $codigoIdentificacao     = $row->CODIGO_IDENTIFICACAO;
					   $nomeUsuario             = $row->NOME_USUAL;
					}else{
						$resultado['LOGADO'] = False;
						$resultado['MSG']    = 'Vendedor não encontrado ou excluído, reveja os dados digitados e tente novamente!';					
					}
					break;		
			}

					

			
			$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
			$resEmp = jn_query($queryEmp);
			$rowEmp = jn_fetch_object($resEmp);
			
			$_SESSION['codigoIdentificacao']          = $codigoIdentificacao;
			$_SESSION['codigoIdentificacaoTitular']   = $codigoIdentificacaoTitular;
			$_SESSION['nomeUsuario']           = jn_utf8_encode($nomeUsuario);
			$_SESSION['perfilOperador']        = $loginWeb['perfilOperador'];
			$_SESSION['versaoAplicacao']       = '1.0.0';
			$_SESSION['ErrorList']             = array();
			$_SESSION['UrlAcesso']             = $_SERVER['HTTP_REFERER'];
			$_SESSION['HorarioAcesso']         = time();
			$_SESSION['IpUsuario']             = $_SERVER['REMOTE_ADDR'];
			$_SESSION['nomeEmpresa']           = $rowEmp->NOME_EMPRESA;
			$_SESSION['codigoSmart']           = $rowEmp->CODIGO_SMART;
			$_SESSION['SESSAO_ID'] 			   = jn_gerasequencial('CFGLOG_NET');
			$_SESSION['AliancaPx4Net']     	   = 'N';

			if ($_SESSION['perfilOperador']=='OPERADOR')
			{
				$queryTmp = 'SELECT FLAG_OPERADOR_ERP FROM PS1100 WHERE CODIGO_IDENTIFICACAO = ' . aspas($loginWeb['codigoIdentificacao']);
				$resTmp   = jn_query($queryTmp, false, true, true);

				if ($resTmp!='')
				{
					$rowTmp   = jn_fetch_object($resTmp);

				    if ($rowTmp->FLAG_OPERADOR_ERP == 'S')
					    $_SESSION['AliancaPx4Net']  = 'S';
				}
			}


			if($auditor == true){
				$_SESSION['AUDITOR'] 		   = 'S';				
			}else if($auditorAgendamento == true){
				$_SESSION['AUDITOR_AGENDAMENTO']	= 'S';				
			}
			
			if($resultado['LOGADO'] and $_SESSION['APP']){
				$query2 = "SELECT CODIGO_INTERNO  FROM app_usuario_interno WHERE codigo_contratante = ". aspas('2'). " and codigo_usuario =" . aspas($_SESSION['codigoIdentificacao']). " and perfil_usuario = ".  aspas($_SESSION['perfilOperador']);
							
				//echo $query2;
				
				$resultQuery2 = jn_query($query2);

				if ($objResult2 = jn_fetch_object($resultQuery2)){
					$dados['CODIGO_INTERNO'] = $objResult2->CODIGO_INTERNO;
				}else{
					$dados['CODIGO_INTERNO'] = jn_gerasequencial('APP_USUARIO_INTERNO');
					$query3 = "INSERT INTO app_usuario_interno(CODIGO_INTERNO,CODIGO_CONTRATANTE,CODIGO_USUARIO,PERFIL_USUARIO)VALUES(".aspas($dados['CODIGO_INTERNO']).",".aspas('2').",".aspas($_SESSION['codigoIdentificacao']).",".aspas($_SESSION['perfilOperador']).")";
					$resultQuery3 = jn_query($query3);
				}
				$valorSequencial = jn_gerasequencial('APP_LOGIN_AUTOMATICO');
				$queryPrincipal =	"INSERT INTO app_login_automatico (NUMERO_REGISTRO,CODIGO_INTERNO, DATA_ULTIMO_LOGIN, CODIGO_DEVICE, FLAG_CONECTAR_AUTOMATICO, TIPO_ASSOCIADO, FLAG_LOGIN_DIGITAL) " .
									" VALUES (".aspas($valorSequencial)."," . aspas($dados['CODIGO_INTERNO']) . ", " . dataToSql(date("d/m/Y")) .
									", " . aspas($_SESSION['idDevice']) . ", " . aspas('S') . ", " . aspas('1') . ", " . aspas('S') . ")";					
				$resultQuery = jn_query($queryPrincipal);
			}

			$tipoLogin = 'Aliança Net';
			if($_SESSION['APP']){
				$tipoLogin = 'Aliança App';
			}
			$browser = explode(';',$_POST['navegador']);
			$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
												USUARIO,
												IDENTIFICACAO,
												DIA_HORA,
												ORIGEM,                                        
												PAGINA_ACESSADA,
												VERSAO_WEB,
												FINALIZADO_AUTO,
												FLAG_SUCESSO)
					  VALUES (
								\''. $_SESSION['SESSAO_ID'] .'\', 
								\''. $_SESSION['perfilOperador'] .'\', 
								\''.$_SESSION['codigoIdentificacao'].'\',
								 current_timestamp, 
								\''. 'login.php' .'\',                        
								\''.$_POST['pagina'].'\',
								\''. $tipoLogin
								.'\',                        
								\'N\',
								\''. $loginWeb['flagSucesso'] .'\' )';							
			jn_query($query);
			
			if($_SESSION['perfilOperador'] == 'OPERADOR'){				
				jn_query('UPDATE PS1100 SET DATA_ULTIMO_LOGIN_NET = CURRENT_TIMESTAMP WHERE CODIGO_IDENTIFICACAO = ' . aspas($_SESSION['codigoIdentificacao']));
			}
			
			$res = jn_query("SELECT NOME_EMPRESA, RAZAO_SOCIAL FROM CfgEmpresa" );
			$row = jn_fetch_object($res);       
			$_SESSION['razaoSocialOperadora'] = $row->RAZAO_SOCIAL;
			$_SESSION['NomeOperadora'] = $row->NOME_EMPRESA;
			
			$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
			$resultado['DADOS']['codigoIdentificacaoTitular'] = $_SESSION['codigoIdentificacaoTitular'];
			$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
			$resultado['DADOS']['nomeUsuario'] = $_SESSION['nomeUsuario']; 
			
			if($processoReativacao == true){
				$resultado['DADOS']['alterarSenha'] = false;
			}elseif(retornaValorConfiguracao('DIAS_ALTERAR_SENHA')>0){
				$data_inicial = $_SESSION['dtAltSenha'];
				if(is_object($data_inicial)){
					$data_inicial = $data_inicial->format('Y-m-d');
				}
				$data_final =  date("Y-m-d");
				$diferenca = strtotime($data_final) - strtotime($data_inicial);
				$dias = floor($diferenca / (60 * 60 * 24));
				if($dias>=retornaValorConfiguracao('DIAS_ALTERAR_SENHA'))
					$resultado['DADOS']['alterarSenha'] = true;
				else
					$resultado['DADOS']['alterarSenha'] = false;
				
			}elseif(retornaValorConfiguracao('ALTERAR_SENHA_APOS_LOGIN') == 'SIM'){				
				$queryFlagSenha = 'SELECT FORCA_ALTERAR_SENHA FROM APP_VW_DADOS_LOGIN_V3 WHERE CODIGO_USUARIO = ' . aspas($_SESSION['codigoIdentificacao']);
				$resFlagSenha = jn_query($queryFlagSenha);
				$rowFlagSenha = jn_fetch_object($resFlagSenha);
				
				if($rowFlagSenha->FORCA_ALTERAR_SENHA == 'S'){
					$resultado['DADOS']['alterarSenha'] = true;
				}
			}else{				
				$resultado['DADOS']['alterarSenha'] = false;
			}
						
			$quantAssocContrato = 0;
			if($_SESSION['codigoSmart'] == '3423') {//Plena
				$queryCpf = 'SELECT NUMERO_CPF FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
				$resCpf = jn_query($queryCpf);
				$rowCpf = jn_fetch_object($resCpf);	
				
				$queryCadastros  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES FROM PS1000 ';
				$queryCadastros .= ' INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
				$queryCadastros .= ' WHERE DATA_EXCLUSAO IS NULL AND ((CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']) . ') ';
				$queryCadastros .= ' OR (CODIGO_TITULAR = ' . aspas($_SESSION['codigoIdentificacao']) . ') ';
				$queryCadastros .= ' OR (NUMERO_CPF = ' . aspas($rowCpf->NUMERO_CPF) . ')) ';
				$resCadastros = jn_query($queryCadastros);
								
				while($rowCadastros = jn_fetch_object($resCadastros)){
					$quantAssocContrato++;	
				}
			}
			
			// Redireciono para a pagina inicial configurada ou para a pagina Localiza Rede
			$PaginaInicial = retornaValorConfiguracao('PAGINA_INICIAL_APOS_LOGIN');
			
			if($processoReativacao){
				/*
				$resultado['DESTINO']			 		= 'site/gridDinamico';				
				$resultado['DESTINO_JSON']['tabela'] 	= 'VW_REATIVACAO_AL2';
				$resultado['DESTINO_JSON']['titulo'] 	= 'Cobrança Reativação';
				*/

				$resultado['DESTINO']			 		= 'site/faturasReativacao';	
				$resultado['DESTINO_JSON']['filtros'] 	= '[]';
				$resultado['DADOS']['tipoMenu'] 		= 'OCULTO';
				$_SESSION['tipoMenu']					= 'OCULTO'; 
			}
			elseif ($loginWeb['perfilOperador'] == 'BENEFICIARIO' && $quantAssocContrato > 1 && $_SESSION['codigoSmart'] == '3423') {//Plena
				$resultado['DESTINO'] = 'site/trocaAssociado';				
			}
			elseif ($loginWeb['perfilOperador'] == 'BENEFICIARIO' && $quantidadeUsuarios == 1) {
				//header('Location: frm_principal.php');
				$resultado['DESTINO'] = 'site/paginaInicial';
				$resultado['DESTINO_JSON']['tabela'] = 'VW_COMUNICACAO_NET_AL2';
				$resultado['DESTINO_JSON']['titulo'] = 'Mensagens e Noticias';
				$resultado['DESTINO_JSON']['filtros'] = '[]';
			}			
			elseif ($loginWeb['perfilOperador'] == 'BENEFICIARIO' && $quantidadeUsuarios > 1) {
				//header('Location: frm_verificaPlano.php');  
				$resultado['DESTINO'] = 'site/paginaInicial';
				$resultado['DESTINO_JSON']['tabela'] = 'VW_COMUNICACAO_NET_AL2';
				$resultado['DESTINO_JSON']['titulo'] = 'Mensagens e Noticias';
				$resultado['DESTINO_JSON']['filtros'] = '[]';
			} 
			elseif ($loginWeb['perfilOperador'] == 'PRESTADOR' ) {
				//header('Location: frm_principal.php');
				$resultado['DESTINO'] = 'site/paginaInicial';
				$resultado['DESTINO_JSON']['tabela'] = 'VW_COMUNICACAO_NET_AL2';
				$resultado['DESTINO_JSON']['titulo'] = 'Mensagens e Noticias';
				$resultado['DESTINO_JSON']['filtros'] = '[]';
			}else{
				//header('Location: frm_principal.php');     
					
				$resultado['DESTINO'] = 'site/paginaInicial';
				$resultado['DESTINO_JSON']['tabela'] = 'VW_COMUNICACAO_NET_AL2';
				$resultado['DESTINO_JSON']['titulo'] = 'Mensagens e Noticias';
				$resultado['DESTINO_JSON']['filtros'] = '[]';
			}

			$resultado['DESTINO_JSON']['mostraLogoExibicoes'] = 'SIM';
			$resultado['DADOS']['imagemPerfil'] = '';
		}else{
			$resultado['LOGADO'] = False;
			$resultado['MSG']    = 'Usuario ou senha invalidos, reveja os dados digitados e tente novamente!!';						
		}
	}elseif($numeroCpf != '' || $numeroCarteirinha != ''){
		
		$perfil = '';
		if(strlen(sanitizeString($numeroCpf)) > 12){		
			$query = 'SELECT CODIGO_EMPRESA ,NOME_EMPRESA FROM PS1010 ';
			$query .= 'WHERE 1=1';
			
			if($dadosInput['dados']['parametroAdicional'] == 'odonto'){
				$query .= ' AND CODIGO_TIPO_CARACTERISTICA = ' . aspas('10');			
			}

			if($dadosInput['dados']['parametroAdicional'] == 'saude'){
				$query .= ' AND CODIGO_TIPO_CARACTERISTICA <> ' . aspas('10');			
			}
			
			if($numeroCpf){			
				$query .= ' AND NUMERO_CNPJ =' . aspas($numeroCpf);						
			}
			
			if($numeroCarteirinha){
				$query .= ' AND CODIGO_EMPRESA = ' . aspas($numeroCarteirinha);			
			}
					
			$res = @jn_query($query);
			if($row = @jn_fetch_object($res)){
				$codigoIdentificacao         = $row->CODIGO_EMPRESA;
				$codigoIdentificacaoTitular  = '';
				$nomeUsuario                 = $row->NOME_EMPRESA;
				$perfil 					 = 'EMPRESA';
			}	
		}else{			
			if($dadosInput['dados']['parametroAdicional'] == 'apuel'){
				registraAssociadoAPUEL($dadosInput);
			}

			if($dadosInput['dados']['parametroAdicional'] == 'caapsml'){
				registraAssociadoCaapsML($dadosInput);
			}

			if($dadosInput['dados']['parametroAdicional'] == 'caapsMlAlteracao'){
				abreProcessoCaapsML($dadosInput);
			}
			
			$query  = ' SELECT CODIGO_ASSOCIADO ,NOME_ASSOCIADO, CODIGO_TITULAR FROM PS1000 ';

			if($dadosInput['dados']['parametroAdicional'] == 'reajusteVidamax'){
				$query .= ' WHERE 	((PS1000.CODIGO_GRUPO_CONTRATO IN (5,14) ';
				$query .= ' 	AND PS1000.CODIGO_GRUPO_PESSOAS IN (17,918,910,919,920,535,9199)) OR (PS1000.CODIGO_GRUPO_CONTRATO = 22)) ';
			}elseif($dadosInput['dados']['parametroAdicional'] == 'reativacaoOdonto' || $dadosInput['dados']['parametroAdicional'] == 'reativacaoSaude'){//Se for reativacao, nao pode retornar associado nessa query
				$query .= ' WHERE 1 <> 1 ';	
			}else{
				$query .= ' WHERE (DATA_EXCLUSAO IS NULL or (DATA_EXCLUSAO >= current_timestamp)) ';				
			}
				
			
			if($dadosInput['dados']['parametroAdicional'] == 'pagamentoVendas'){//Associados da rotina de vendas
				$query  = ' SELECT CODIGO_ASSOCIADO ,NOME_ASSOCIADO, CODIGO_TITULAR FROM VND1000_ON  ';				
				$query .= ' WHERE 1 = 1 ';
			}
			
			if($numeroCpf){			
				$query .= ' AND ((NUMERO_CPF =' . aspas($numeroCpf) . ') OR (NUMERO_CPF =' . aspas(sanitizeString($numeroCpf)) . ' ) )';						
			}
			
			if($numeroCarteirinha){
				$query .= ' AND CODIGO_ASSOCIADO = ' . aspas($numeroCarteirinha);			
			}
			
			if($dadosInput['dados']['parametroAdicional'] == 'odonto'){
				$query .= ' AND CODIGO_TIPO_CARACTERISTICA = ' . aspas('10');			
			}
			
			if($dadosInput['dados']['parametroAdicional'] == 'saude'){
				$query .= ' AND CODIGO_TIPO_CARACTERISTICA <> ' . aspas('10');			
			}
			
			$query .= ' ORDER BY COALESCE(DATA_EXCLUSAO, "01.01.2060") DESC ';
			$res = @jn_query($query);
			if($row = @jn_fetch_object($res)){
				$codigoIdentificacao         = $row->CODIGO_ASSOCIADO;
				$codigoIdentificacaoTitular  = $row->CODIGO_ASSOCIADO;
				$nomeUsuario                 = $row->NOME_ASSOCIADO;
				$perfil 					 = 'BENEFICIARIO';
			}			
		}

		if (retornaValorConfiguracao('PROCESSO_REATIVACAO') == 'SIM' and $codigoIdentificacao == ''){

			$processoReativacao = false;
			$codAssocReat = '';

			$query  = ' SELECT CODIGO_ASSOCIADO ,NOME_ASSOCIADO, CODIGO_TITULAR FROM PS1000 ';
			$query .= ' WHERE 1 = 1 ';	
			
			if($dadosInput['dados']['parametroAdicional'] == 'reativacaoSaude'){
				$query .= ' AND (CODIGO_MOTIVO_EXCLUSAO = "4" OR CODIGO_MOTIVO_EXCLUSAO = "1") AND CODIGO_TIPO_CARACTERISTICA <> ' . aspas('10');
			}

			if($dadosInput['dados']['parametroAdicional'] == 'reativacaoOdonto'){
				$query .= ' AND (CODIGO_MOTIVO_EXCLUSAO = "4" OR CODIGO_MOTIVO_EXCLUSAO = "1") AND CODIGO_TIPO_CARACTERISTICA = ' . aspas('10');
			}
			
			if($numeroCpf){			
				$query .= ' AND NUMERO_CPF =' . aspas($numeroCpf);						
			}
			
			if($numeroCarteirinha){
				$query .= ' AND CODIGO_ASSOCIADO = ' . aspas($numeroCarteirinha);			
			}
			
			if($dadosInput['dados']['parametroAdicional'] == 'odonto'){
				$query .= ' AND CODIGO_TIPO_CARACTERISTICA = ' . aspas('10');			
			}
			
			if($dadosInput['dados']['parametroAdicional'] == 'saude'){
				$query .= ' AND CODIGO_TIPO_CARACTERISTICA <> ' . aspas('10');			
			}
			
			$query .= ' ORDER BY COALESCE(DATA_EXCLUSAO, "01.01.2060") DESC ';
			$res = @jn_query($query);
			if($row = @jn_fetch_object($res)){
				$codigoIdentificacao         = $row->CODIGO_ASSOCIADO;
				$codigoIdentificacaoTitular  = $row->CODIGO_ASSOCIADO;
				$nomeUsuario                 = $row->NOME_ASSOCIADO;
				$perfil 					 = 'BENEFICIARIO';
			}

			$queryReativ  = 'SELECT * FROM SP_REATIVACAO_PF(' . aspas($codigoIdentificacao)  . ')';
			$resReativ = jn_query($queryReativ);
			if($rowReativ = jn_fetch_object($resReativ)){
				if($rowReativ->PERMITE_REATIVACAO == 'S'){					
					$processoReativacao = true;
					jn_query('exec SP_GERA_BOLETO_REAT @ACodigoAssociado = ' . aspas($codigoIdentificacao));							
				}else{
					$codAssocReat				 = $codigoIdentificacao;
					$codigoIdentificacao         = '';
					$codigoIdentificacaoTitular  = '';
					$nomeUsuario                 = '';
					$perfil 					 = '';
				}
			}			
		}
		
		$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
		$resEmp = jn_query($queryEmp);
		$rowEmp = jn_fetch_object($resEmp);	
			
		$_SESSION['codigoIdentificacao']          	= $codigoIdentificacao;
		$_SESSION['codigoIdentificacaoTitular']   	= $codigoIdentificacaoTitular;
		$_SESSION['nomeUsuario']           			= $nomeUsuario;
		$_SESSION['perfilOperador']        			= $perfil;
		$_SESSION['versaoAplicacao']       			= '1.0.0';
		$_SESSION['ErrorList']             			= array();
		$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         			= time();
		$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           			= $rowEmp->NOME_EMPRESA;
		$_SESSION['codigoSmart']           			= $rowEmp->CODIGO_SMART;
		$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
		$_SESSION['tipoMenu'] 			   			= 'OCULTO'; 
		
		$browser = explode(';',$_POST['navegador']);
		$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
											USUARIO,
											IDENTIFICACAO,
											DIA_HORA,
											ORIGEM,                                        
											PAGINA_ACESSADA,
											VERSAO_WEB,
											FINALIZADO_AUTO,
											FLAG_SUCESSO)
				  VALUES (
							\''. $_SESSION['SESSAO_ID'] .'\', 
							\''. $_SESSION['perfilOperador'] .'\', 
							\''.$_SESSION['codigoIdentificacao'].'\',
							 current_timestamp, 
							\''. 'login.php' .'\',                        
							\''.$_POST['pagina'].'\',
							\''. 'Aliança Net ' .'\',                        
							\'N\',
							\''. $loginWeb['flagSucesso'] .'\' )';							
		jn_query($query);
		
		$res = jn_query("SELECT NOME_EMPRESA, RAZAO_SOCIAL FROM CfgEmpresa" );
		$row = jn_fetch_object($res);       
		$_SESSION['razaoSocialOperadora'] = $row->RAZAO_SOCIAL;
		$_SESSION['NomeOperadora'] = $row->NOME_EMPRESA;
		
		if (rvc('OCULTAR_MENU_CPF', 'SIM')) 
			$resultado['DADOS']['tipoMenu'] = $_SESSION['tipoMenu'];
		
		if($processoReativacao == false and $codigoIdentificacao == ''){

			$mensagemReativacao = false;

			if (retornaValorConfiguracao('PROCESSO_REATIVACAO') == 'SIM'){
				$queryExc  = ' SELECT CODIGO_MOTIVO_EXCLUSAO FROM PS1000 '; 								
				$queryExc .= ' WHERE (CODIGO_MOTIVO_EXCLUSAO = "4" OR CODIGO_MOTIVO_EXCLUSAO = "1") AND CODIGO_ASSOCIADO = ' . aspas($codAssocReat);
				$resExc = jn_query($queryExc);
				if($rowExc = jn_fetch_object($resExc)){
					$mensagemReativacao = true;
				}
				
			}

			if($dadosInput['dados']['parametroAdicional'] == 'reativacaoOdonto' || $dadosInput['dados']['parametroAdicional'] == 'reativacaoSaude'){
				$mensagemReativacao = true;
			}

			if($mensagemReativacao){								
				$resultado['LOGADO'] = False;
				$resultado['MSG']    = 'Caro beneficiário, por gentileza entre em contato com nossa central de atendimento através dos telefones 11 4445-9080 ou pelo WhatsApp 11 91348-2236. ';
			}elseif($dadosInput['dados']['parametroAdicional'] == 'reajusteVidamax'){
				$resultado['LOGADO'] = False;
				$resultado['MSG']    = 'Associado não encontrado ou contrato sem acesso ao documento.';
			}else{
				$resultado['LOGADO'] = False;
				$resultado['MSG']    = 'Beneficiário não encontrado ou excluído, reveja os dados digitados e tente novamente!';						
			}							
		}elseif(!$codigoIdentificacao){
			$resultado['LOGADO'] = False;
			$resultado['MSG']    = 'CPF ou Número de Carteirinha inválidos, reveja os dados digitados e tente novamente!';		
				
		}else{
			$resultado['LOGADO'] = true;
			$resultado['MSG']    = 'Você foi logado com sucesso.';
			$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
			$resultado['DADOS']['codigoIdentificacaoTitular'] = $_SESSION['codigoIdentificacaoTitular'];
			if($_SESSION['perfilOperador'] == 'EMPRESA'){
				$_SESSION['perfilOperador'] = $_SESSION['perfilOperador'].'_CNPJ';			
			}else{				
				$_SESSION['perfilOperador'] = $_SESSION['perfilOperador'].'_CPF';
			}
			$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
			$resultado['DADOS']['nomeUsuario'] = jn_utf8_encode($_SESSION['nomeUsuario']); 
			$resultado['DADOS']['imagemPerfil'] = '';

			if($processoReativacao){
				$resultado['DESTINO']			 		= 'site/faturasReativacao';	
				$resultado['DESTINO_JSON']['filtros'] 	= '[]';
				$resultado['DADOS']['tipoMenu'] 		= 'OCULTO';
				$_SESSION['tipoMenu']					= 'OCULTO'; 
			}elseif($dadosInput['dados']['parametroAdicional'] == 'carteirinha'){
				$resultado['DESTINO'] = 'site/cadastroDinamico';
				$resultado['DESTINO_JSON']['tabela'] = 'VW_PS6360_ALIANCANET2';
			}elseif($dadosInput['dados']['parametroAdicional'] == 'pagamento'){
				$resultado['DESTINO'] = 'site/pagamento';
			}elseif($dadosInput['dados']['parametroAdicional'] == 'saude' || $dadosInput['dados']['parametroAdicional'] == 'odonto'){
				$resultado['DESTINO'] = 'site/faturasAgrupadas';
			}elseif($dadosInput['dados']['parametroAdicional'] == 'reajusteVidamax'){
				$resultado['DESTINO'] = 'site/gridDinamico';
				$resultado['DESTINO_JSON']['tabela'] = 'VW_DOCUMENTOS_REAJUSTE_AL2';
				$resultado['DADOS']['tipoMenu'] 		= 'OCULTO';
				$_SESSION['tipoMenu']					= 'OCULTO'; 
			}else{
				$resultado['DESTINO'] = 'site/gridDinamico';
				$resultado['DESTINO_JSON']['tabela'] = 'VW_SEGUNDA_VIA_AL2';
			}
			
				
		}
	}
	
	if($_SESSION['APP'] and ($resultado['LOGADO']) ){
		$query2 = "SELECT CODIGO_INTERNO  FROM app_usuario_interno WHERE codigo_usuario =" . aspas($_SESSION['codigoIdentificacao']);
						
		$resultQuery2 = jn_query($query2);

		if ($objResult2 = jn_fetch_object($resultQuery2)){
			$_SESSION['CODIGO_INTERNO'] = $objResult2->CODIGO_INTERNO;
		}else{
			$_SESSION['CODIGO_INTERNO'] = jn_gerasequencial('APP_USUARIO_INTERNO');
			$query3 = "INSERT INTO app_usuario_interno(CODIGO_INTERNO,CODIGO_CONTRATANTE,CODIGO_USUARIO,PERFIL_USUARIO)VALUES(".aspas($_SESSION['CODIGO_INTERNO']).",".aspas('1').",".aspas($_SESSION['codigoIdentificacao']).",".aspas('BENEFICIARIO').")";
			$resultQuery3 = jn_query($query3);
		}
		
		$qry = "SELECT * FROM APP_LOGIN_AUTOMATICO
			    WHERE APP_LOGIN_AUTOMATICO.FLAG_CONECTAR_AUTOMATICO = 'S' AND APP_LOGIN_AUTOMATICO.CODIGO_DEVICE = " . aspas($_SESSION['idDevice'])." and CODIGO_INTERNO = ".aspas($_SESSION['CODIGO_INTERNO']);

		$resQuery = jn_query($qry);
							
		if ($objResult = jn_fetch_object($resQuery)){
			//Ja logado	
		}else{
			$valorSequencial = jn_gerasequencial('APP_LOGIN_AUTOMATICO');
			$queryPrincipal =	"INSERT INTO app_login_automatico (NUMERO_REGISTRO,CODIGO_INTERNO, DATA_ULTIMO_LOGIN, CODIGO_DEVICE, FLAG_CONECTAR_AUTOMATICO, TIPO_ASSOCIADO, FLAG_LOGIN_DIGITAL) " .
								" VALUES (".aspas($valorSequencial)."," . aspas($_SESSION['CODIGO_INTERNO']) . ", " . dataToSql(date("d/m/Y")) .
								", " . aspas($_SESSION['idDevice']) . ", " . aspas('S') . ", " . aspas('S') . ", " . aspas('S') . ")";					
			$resultQuery = jn_query($queryPrincipal);
		}
	}


	echo json_encode($resultado);


}else if($dadosInput['tipo']=='logout'){

	$_SESSION = null;
	session_destroy();
	$resultado['LOGADO'] = false;
	$resultado['MSG']    = 'Você foi deslogado com sucesso.';
	echo json_encode($resultado);

}else if($dadosInput['tipo']=='vendaOnline' || $_GET['tipo'] == 'vendaOnline'){
	$numeroCpf = ($dadosInput['dados']['cpf'] ? $dadosInput['dados']['cpf'] : $_GET['cpf']);
	$numeroCarteirinha = ($dadosInput['dados']['id'] ? $dadosInput['dados']['id'] : $_GET['id']);
		
	$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);
	
	$query  = ' SELECT CODIGO_ASSOCIADO ,NOME_ASSOCIADO, CODIGO_TITULAR, ULTIMO_STATUS, CODIGO_ASSOCIADO_PS1000, ';
	$query .= ' (SELECT FIRST 1 ST.CODIGO_ASSOCIADO FROM VND1000STATUS_ON ST WHERE VND1000_ON.CODIGO_ASSOCIADO = ST.CODIGO_ASSOCIADO AND TIPO_STATUS = "CONTRATO_OK") AS COD_DECLARACAO ';
	$query .= ' FROM VND1000_ON ';
	
	if($_GET['entidade'] == 'caapsMlAlteracao'){
		$query .= ' WHERE MATRICULA = ' . aspas($numeroCpf);
	}else{
		$query .= ' WHERE (NUMERO_CPF = ' . aspas($numeroCpf) . ' or NUMERO_CPF = ' . aspas(formatCnpjCpf($numeroCpf)) . ') ';
		$query .= ' AND CODIGO_ASSOCIADO = ' . aspas($numeroCarteirinha);			
	}
	

	$res = @jn_query($query);

	if($row = @jn_fetch_object($res)){		
		if($row->COD_DECLARACAO != '' && $_GET['tipo'] != 'vendaOnline' && $rowEmp->CODIGO_SMART == '4022' && $row->CODIGO_ASSOCIADO_PS1000 != ''){//Odontomais permite alteracao se nao tiver cadastro na PS1000
			$resultado['LOGADO'] = false;
			$resultado['MSG']    = 'Não é possível acessar o link após o preenchimento da declaração de saúde e o aceite do contrato!';	
		}elseif($row->COD_DECLARACAO != '' && $_GET['tipo'] != 'vendaOnline'){
			$resultado['LOGADO'] = false;
			$resultado['MSG']    = 'Não é possível acessar o link após o preenchimento da declaração de saúde e o aceite do contrato!';	
		}elseif($row->ULTIMO_STATUS == 'CONCLUIDO' && $_GET['tipo'] == 'vendaOnline'){			
			$resultado['LOGADO'] = false;
			$resultado['MSG']    = 'Para inclusão de novos dependentes, favor entrar em contato com a VIDAMAX pelo e-mail unimedcaaspml@vidamax.com.br ou pelo telefone 0800-7731717.';	
		}elseif($row->ULTIMO_STATUS == 'AGUARDANDO_AVALIACAO' && $_GET['entidade'] == 'CAAPSML'){			
			$resultado['LOGADO'] = false;
			$resultado['MSG']    = 'Não é possível acessar o link após a assinatura do contrato!';	
		}else{
			$codigoIdentificacao         = $row->CODIGO_ASSOCIADO;
			$codigoIdentificacaoTitular  = $row->CODIGO_ASSOCIADO;
			$nomeUsuario                 = $row->NOME_ASSOCIADO;

			$_SESSION['codigoIdentificacao']          	= $codigoIdentificacao;
			$_SESSION['codigoIdentificacaoTitular']   	= $codigoIdentificacaoTitular;
			$_SESSION['nomeUsuario']           			= $nomeUsuario;
			$_SESSION['perfilOperador']        			= 'BENEFICIARIO';
			$_SESSION['perfilOperador']                 = $_SESSION['perfilOperador'].'_VO';
			$_SESSION['versaoAplicacao']       			= '1.0.0';
			$_SESSION['ErrorList']             			= array();
			$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
			$_SESSION['HorarioAcesso']         			= time();
			$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
			$_SESSION['nomeEmpresa']           			= $rowEmp->NOME_EMPRESA;
			$_SESSION['codigoSmart']           			= $rowEmp->CODIGO_SMART;
			$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
			$_SESSION['tipoMenu']	                    = 'OCULTO';	
			
			$resultado['LOGADO'] = true;
			$resultado['MSG']    = 'Você foi logado com sucesso.';
			$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
			$resultado['DADOS']['codigoIdentificacaoTitular'] = $_SESSION['codigoIdentificacaoTitular'];
			
			$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
			$resultado['DADOS']['nomeUsuario'] = $_SESSION['nomeUsuario']; 
			
			
			$resultado['DADOS']['tipoMenu'] = $_SESSION['tipoMenu'];
			
			if($_GET['entidade'] == 'CAAPSML'){
				$resultado['DESTINO'] = 'site/caapsMl';				
			}elseif($_GET['entidade'] == 'caapsMlAlteracao'){
				$resultado['DESTINO'] = 'site/caapsMl';			
				$resultado['DESTINO_JSON']['tipo'] = 'alteracao';	
			}elseif($_GET['tipo'] == 'vendaOnline'){
				$resultado['DESTINO'] = 'site/contrato';				
			}
			
			$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
												USUARIO,
												IDENTIFICACAO,
												DIA_HORA,
												ORIGEM,                                        
												PAGINA_ACESSADA,
												VERSAO_WEB,
												FINALIZADO_AUTO,
												FLAG_SUCESSO)
					  VALUES (
								\''. $_SESSION['SESSAO_ID'] .'\', 
								\''. $_SESSION['perfilOperador'] .'\', 
								\''.$_SESSION['codigoIdentificacao'].'\',
								 current_timestamp, 
								\''. 'login.php' .'\',                        
								\''.$_POST['pagina'].'\',
								\''. 'Aliança Net ' .'\',                        
								\'N\',
								\''. "S" .'\' )';							
			jn_query($query);	
		}		
	}else{
		$resultado['LOGADO'] = false;
		if($_GET['entidade'] == 'CAAPSML'){
			$resultado['MSG']    = 'Seu cadastro não foi possível por falta de dados obrigatórios...Envie um e-mail para <b>empresas@unimedlondrina.com.br </b>colocando no assunto código 2727 e informe o endereço completo do titular e o CPF e nome de mãe de todos os beneficiários ativos na CAAPSML.';	
		}else{
			$resultado['MSG']    = 'CPF não encontrado, reveja os dados digitados e tente novamente!';	
		}
		
	}
	
	echo json_encode($resultado);

}else if($dadosInput['tipo']=='celular'){

	$idDevice = $dadosInput['dados']['id'];
	
	$qry = "SELECT APP_USUARIO_INTERNO.CODIGO_INTERNO, APP_USUARIO_INTERNO.CODIGO_USUARIO, APP_USUARIO_INTERNO.PERFIL_USUARIO, APP_VW_DADOS_LOGIN_V3.QUANTIDADE_CONTRATOS, CFGEMPRESA.CODIGO_SMART, CFGEMPRESA.NOME_EMPRESA, APP_VW_DADOS_LOGIN_V3.CODIGO_TIPO_CARACTERISTICA,APP_VW_DADOS_LOGIN_V3.NOME_PESSOA FROM APP_LOGIN_AUTOMATICO
			INNER JOIN  APP_USUARIO_INTERNO ON APP_USUARIO_INTERNO.CODIGO_INTERNO = APP_LOGIN_AUTOMATICO.CODIGO_INTERNO 
			INNER JOIN  APP_VW_DADOS_LOGIN_V3  ON APP_VW_DADOS_LOGIN_V3.CODIGO_USUARIO = APP_USUARIO_INTERNO.CODIGO_USUARIO
			INNER JOIN  CFGEMPRESA ON (1=1)
			WHERE APP_LOGIN_AUTOMATICO.FLAG_CONECTAR_AUTOMATICO = 'S' AND APP_LOGIN_AUTOMATICO.CODIGO_DEVICE = " . aspas($idDevice);

	$resQuery = jn_query($qry);
					

	if ($objResult = jn_fetch_object($resQuery))
	{
		
		 
		$_SESSION['codigoIdentificacao']          	= $objResult->CODIGO_USUARIO;
		$_SESSION['nomeUsuario']           			= $objResult->NOME_PESSOA;
		$_SESSION['perfilOperador']        			= $objResult->PERFIL_USUARIO;
		$_SESSION['perfilOperador']                 = $_SESSION['perfilOperador'].'_APP';
		$_SESSION['versaoAplicacao']       			= '1.0.0';
		$_SESSION['ErrorList']             			= array();
		$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         			= time();
		$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           			= $objResult->NOME_EMPRESA;
		$_SESSION['codigoSmart']           			= $objResult->CODIGO_SMART;
		$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
		$_SESSION['tipoMenu']	                    = 'OCULTO';	
		$_SESSION['naoMostraVoltar']				= true;
		
		$resultado['LOGADO'] = true;
		
		$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
		
		$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
		$resultado['DADOS']['nomeUsuario'] = $_SESSION['nomeUsuario']; 
		
		$resultado['DADOS']['tipoMenu'] = $_SESSION['tipoMenu'];
		$resultado['DADOS']['naoMostraVoltar'] = $_SESSION['naoMostraVoltar'];
		
		$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
											USUARIO,
											IDENTIFICACAO,
											DIA_HORA,
											ORIGEM,                                        
											PAGINA_ACESSADA,
											VERSAO_WEB,
											FINALIZADO_AUTO,
											FLAG_SUCESSO)
				  VALUES (
							\''. $_SESSION['SESSAO_ID'] .'\', 
							\''. $_SESSION['perfilOperador'] .'\', 
							\''.$_SESSION['codigoIdentificacao'].'\',
							 current_timestamp, 
							\''. 'login.php' .'\',                        
							\''.$_POST['pagina'].'\',
							\''. 'Aliança Net ' .'\',                        
							\'N\',
							\''. "S" .'\' )';							
		jn_query($query);		 
	}else{
		$resultado['LOGADO'] = false;
		$resultado['MSG']    = 'Não foi possivel acessar a pagina!';	
	}
	
	
	echo json_encode($resultado);

}else if($dadosInput['tipo']=='autocontratacao'){

	$idDevice = $dadosInput['dados']['id'];
	
		$_SESSION['nomeUsuario']           			= '';
		$_SESSION['perfilOperador']        			= 'AUTOCONTRATACAO';
		$_SESSION['ErrorList']             			= array();
		$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         			= time();
		$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           			= '';
		$_SESSION['codigoSmart']           			= $objResult->CODIGO_SMART;  // FAZER SELECT PARA PEGAR
		$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
		$_SESSION['tipoMenu']	                    = 'OCULTO';	
		$_SESSION['naoMostraVoltar']				= true;
		$_SESSION['codigoIdentificacao']          	= $_SESSION['SESSAO_ID'];
		
		$resultado['LOGADO'] = true;
		
		$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
		
		$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
		$resultado['DADOS']['nomeUsuario'] = $_SESSION['nomeUsuario']; 
		
		$resultado['DADOS']['tipoMenu'] = $_SESSION['tipoMenu'];
		$resultado['DADOS']['naoMostraVoltar'] = $_SESSION['naoMostraVoltar'];
	
		$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
											USUARIO,
											IDENTIFICACAO,
											DIA_HORA,
											ORIGEM,                                        
											PAGINA_ACESSADA,
											VERSAO_WEB,
											FINALIZADO_AUTO,
											FLAG_SUCESSO)
				  VALUES (
							\''. $_SESSION['SESSAO_ID'] .'\', 
							\''. $_SESSION['perfilOperador'] .'\', 
							\''.$_SESSION['codigoIdentificacao'].'\',
							 current_timestamp, 
							\''. 'login.php' .'\',                        
							\''.$_POST['pagina'].'\',
							\''. 'Aliança Net ' .'\',                        
							\'N\',
							\''. "S" .'\' )';							
		jn_query($query);		 
	
	echo json_encode($resultado);

}
else if($dadosInput['tipo']=='contratacao_servicos_login')
{

	$idDevice = $dadosInput['dados']['id'];

	$query    = ' Select CFGLOGIN_DINAMICO_NET.*, ps1000.nome_associado, ps1000.codigo_associado 
			 	  From CFGLOGIN_DINAMICO_NET
				  inner join ps1000 on (CFGLOGIN_DINAMICO_NET.codigo_identificacao = ps1000.codigo_associado) and ps1000.data_exclusao is null
				  where usuario = ' . aspas($dadosInput['cpfLogin']) . ' and senha_acesso = ' . aspas($dadosInput['senha']);

	$res      = @jn_query($query);

	if($objResult = @jn_fetch_object($res))
	{

		$_SESSION['nomeUsuario']           			= $objResult->NOME_ASSOCIADO;
		$_SESSION['perfilOperador']        			= 'CONTRATSERVLOGIN';
		$_SESSION['ErrorList']             			= array();
		$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         			= time();
		$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           			= '';
		$_SESSION['codigoSmart']           			= $objResult->CODIGO_SMART;  // FAZER SELECT PARA PEGAR
		$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
		$_SESSION['tipoMenu']	                    = 'OCULTO';	
		$_SESSION['naoMostraVoltar']				= true;
		$_SESSION['codigoIdentificacao']          	= $_SESSION['SESSAO_ID'];
		$resultado['LOGADO']                        = true;
		$resultado['DADOS']['codigoIdentificacao']  =  $_SESSION['codigoIdentificacao'];
		$resultado['DADOS']['codigoAssociado']      =  $objResult->CODIGO_ASSOCIADO;
		
		$resultado['DADOS']['perfilOperador']       = $_SESSION['perfilOperador'];
		$resultado['DADOS']['nomeUsuario']          = $_SESSION['nomeUsuario']; 
		
		$resultado['DADOS']['tipoMenu']             = $_SESSION['tipoMenu'];
		$resultado['DADOS']['naoMostraVoltar']      = $_SESSION['naoMostraVoltar'];

		$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
											USUARIO,
											IDENTIFICACAO,
											DIA_HORA,
											ORIGEM,                                        
											PAGINA_ACESSADA,
											VERSAO_WEB,
											FINALIZADO_AUTO,
											FLAG_SUCESSO)
				  VALUES (
							\''. $_SESSION['SESSAO_ID'] .'\', 
							\''. $_SESSION['perfilOperador'] .'\', 
							\''.$_SESSION['codigoIdentificacao'].'\',
							 current_timestamp, 
							\''. 'login.php' .'\',                        
							\''.$_POST['pagina'].'\',
							\''. 'Aliança Net ' .'\',                        
							\'N\',
							\''. "S" .'\' )';							
		jn_query($query);		 

		$resultado['LOGADO'] = true;
		$resultado['MSG']    = 'Você foi logado com sucesso.';

	}
	else
	{
		$resultado['LOGADO'] = false;
	    $resultado['MSG']    = 'Informações incorretas para login.';
	}

	echo json_encode($resultado);

}
else if($dadosInput['tipo']=='contratacao_servicos_cadastro')
{

	$idDevice = $dadosInput['dados']['id'];

	$query    = ' Select CFGLOGIN_DINAMICO_NET.*, ps1000.nome_associado 
			 	  From CFGLOGIN_DINAMICO_NET
				  inner join ps1000 on (CFGLOGIN_DINAMICO_NET.codigo_identificacao = ps1000.codigo_associado) and ps1000.data_exclusao is null
				  where usuario = ' . aspas($dadosInput['numeroCnpjCpf']);

	$res      = @jn_query($query);

	if($objResult = @jn_fetch_object($res))
	{
		$resultado['LOGADO'] = false;
	    $resultado['MSG']    = 'Já existe um usuário com este CPF, caso não lembre sua senha clique em "esqueci minha senha".';
	}
	else
	{	

		$query    = ' Select * from ps1040 where cep = ' . aspas($dadosInput['numeroCep']);
		$res      = @jn_query($query);

		if($objResult = @jn_fetch_object($res))
		{
			$endereco = $objResult->LOGRADOURO . ', ' . $dadosInput['numeroEndereco'];

			if ($dadosInput['complementoEndereco']!='')
				$endereco = $endereco . ' - ' . $dadosInput['complementoEndereco'];

			$cidade   = $objResult->CIDADE;
			$bairro   = $objResult->BAIRRO;
			$estado   = $objResult->ESTADO;
		}
		else
		{
			$resultado['LOGADO'] = false;
		    $resultado['MSG']    = 'O número do Cep informado não foi encontrado';
			echo json_encode($resultado);
		    exit;
		}

		$queryInsert = "SELECT * FROM GERASEQUENCIAL ('PS1000') ";
		$res         = @jn_query($queryInsert);
		$objResult   = @jn_fetch_object($res);

		$codigoIdentificacao = str_pad(400,3,'0',STR_PAD_LEFT) . str_pad($objResult->ATUAL,7,'0',STR_PAD_LEFT) . '00';

		//

		$queryInsert = " Insert into ps1000(codigo_associado, codigo_plano, codigo_empresa, nome_associado, data_nascimento, 
		                   codigo_estado_civil, codigo_parentesco, sexo, numero_cpf, 
					       data_admissao, data_digitacao, codigo_sequencial, 
					       codigo_titular, tipo_associado, flag_planofamiliar, numero_dependente)
					       values(" . aspas($codigoIdentificacao) . ',1,400, ' . aspas($dadosInput['nomePessoa']) . ', ' . aspas('01/01/2000') . ', ' .
					                  aspas('1') . ', ' . aspas('1') . ', ' . aspas('M') . ', ' . aspas($dadosInput['numeroCnpjCpf']) . ', ' .
					                  'current_timestamp, current_timestamp, ' . aspas($objResult->ATUAL) . ', ' . aspas($codigoIdentificacao) . ', ' .
					                  aspas('T') . ', ' . aspas('S') . ',0)';

		if (!jn_query($queryInsert))
		{
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Erro ao cadastrar o beneficiário [Ref-01].';	
			return;
		}
				   
		//

		$queryInsert = " Insert into ps1001(codigo_associado, Endereco, Bairro, Cidade, Cep, endereco_email, Estado)
					       values(" . aspas($codigoIdentificacao) . ', ' . aspas($endereco) . ', ' . aspas($bairro) . ', ' .
					                  aspas($cidade) . ', ' . aspas($dadosInput['numeroCep']) . ', ' . aspas($dadosInput['enderecoEmail']) . ', ' . 
					                  aspas($estado) . ')';

		if (!jn_query($queryInsert))
		{
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Erro ao cadastrar o beneficiário [Ref-02].';	
			return;
		}

		//

		$queryInsert = " Insert into ps1006(codigo_associado, numero_telefone, indice_telefone)
					     values(" . aspas($codigoIdentificacao) . ', ' . aspas($dadosInput['numeroTelefone']) . ', 1)';

		if (!jn_query($queryInsert))
		{
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Erro ao cadastrar o beneficiário [Ref-03].';	
			return;
		}

		//

		$queryInsert = " INSERT INTO CFGLOGIN_DINAMICO_NET (USUARIO, SENHA_ACESSO, CODIGO_IDENTIFICACAO, PERFIL_OPERADOR, DATA_ALTERACAO_SENHA) VALUES (" . 
		                 aspas($dadosInput['numeroCnpjCpf']) . ", " . aspas($dadosInput['senhaCad']) . ", " . aspas($codigoIdentificacao) . ", " . 
		                 aspas('BENEFICIARIO') .", current_timestamp)";

		if (!jn_query($queryInsert))
		{
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Erro ao cadastrar o beneficiário [Ref-04].';	
			return;
		}

		$_SESSION['nomeUsuario']           			= $dadosInput['nomePessoa'];
		$_SESSION['perfilOperador']        			= 'CONTRATSERVLOGIN';
		$_SESSION['ErrorList']             			= array();
		$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         			= time();
		$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           			= '';
		$_SESSION['codigoSmart']           			= $objResult->CODIGO_SMART;  // FAZER SELECT PARA PEGAR
		$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
		$_SESSION['tipoMenu']	                    = 'OCULTO';	
		$_SESSION['naoMostraVoltar']				= true;
		$_SESSION['codigoIdentificacao']          	= $_SESSION['SESSAO_ID'];
		$resultado['LOGADO']                        = true;
		$resultado['DADOS']['codigoIdentificacao']  =  $_SESSION['codigoIdentificacao'];
		$resultado['DADOS']['codigoAssociado']      =  $codigoIdentificacao;
		
		$resultado['DADOS']['perfilOperador']       = $_SESSION['perfilOperador'];
		$resultado['DADOS']['nomeUsuario']          = $_SESSION['nomeUsuario']; 
		
		$resultado['DADOS']['tipoMenu']             = $_SESSION['tipoMenu'];
		$resultado['DADOS']['naoMostraVoltar']      = $_SESSION['naoMostraVoltar'];
	
		$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
											USUARIO,
											IDENTIFICACAO,
											DIA_HORA,
											ORIGEM,                                        
											PAGINA_ACESSADA,
											VERSAO_WEB,
											FINALIZADO_AUTO,
											FLAG_SUCESSO)
				  VALUES (
							\''. $_SESSION['SESSAO_ID'] .'\', 
							\''. $_SESSION['perfilOperador'] .'\', 
							\''.$_SESSION['codigoIdentificacao'].'\',
							 current_timestamp, 
							\''. 'login.php' .'\',                        
							\''.$_POST['pagina'].'\',
							\''. 'Aliança Net ' .'\',                        
							\'N\',
							\''. "S" .'\' )';							
		jn_query($query);		 

		$resultado['LOGADO'] = true;
		$resultado['MSG']    = 'Você foi logado com sucesso.';

	}

	echo json_encode($resultado);

}
else if($dadosInput['tipo']=='redecredenciada' || $dadosInput['tipo']=='redecredenciadaRS')
{

	
		$_SESSION['nomeUsuario']           			= 'REDE_CREDENCIADA';
		$_SESSION['perfilOperador']        			= 'REDECREDENCIADA';
		$_SESSION['ErrorList']             			= array();
		$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         			= time();
		$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           			= '';
		$_SESSION['codigoSmart']           			= $objResult->CODIGO_SMART;  // FAZER SELECT PARA PEGAR
		$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
		$_SESSION['tipoMenu']	                    = 'OCULTO';	
		$_SESSION['naoMostraVoltar']				= true;
		$_SESSION['codigoIdentificacao']          	= $_SESSION['SESSAO_ID'];
		
		$resultado['LOGADO'] = true;
		
		$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
		
		$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
		$resultado['DADOS']['nomeUsuario'] = $_SESSION['nomeUsuario']; 
		
		$resultado['DADOS']['tipoMenu'] = $_SESSION['tipoMenu'];
		$resultado['DADOS']['naoMostraVoltar'] = $_SESSION['naoMostraVoltar'];
	
		$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
											USUARIO,
											IDENTIFICACAO,
											DIA_HORA,
											ORIGEM,                                        
											PAGINA_ACESSADA,
											VERSAO_WEB,
											FINALIZADO_AUTO,
											FLAG_SUCESSO)
				  VALUES (
							\''. $_SESSION['SESSAO_ID'] .'\', 
							\''. $_SESSION['perfilOperador'] .'\', 
							\''.$_SESSION['codigoIdentificacao'].'\',
							 current_timestamp, 
							\''. 'login.php' .'\',                        
							\''.$_POST['pagina'].'\',
							\''. 'Aliança Net ' .'\',                        
							\'N\',
							\''. "S" .'\' )';							
		jn_query($query);		 
	
	echo json_encode($resultado);

}
else if($dadosInput['tipo']=='contratacaoservicos'){

	
		$_SESSION['nomeUsuario']           			= 'REDE_CREDENCIADA';
		$_SESSION['perfilOperador']        			= 'REDECREDENCIADA';
		$_SESSION['ErrorList']             			= array();
		$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         			= time();
		$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           			= '';
		$_SESSION['codigoSmart']           			= $objResult->CODIGO_SMART;  // FAZER SELECT PARA PEGAR
		$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
		$_SESSION['tipoMenu']	                    = 'OCULTO';	
		$_SESSION['naoMostraVoltar']				= true;
		$_SESSION['codigoIdentificacao']          	= $_SESSION['SESSAO_ID'];
		
		$resultado['LOGADO'] = true;
		
		$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
		
		$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
		$resultado['DADOS']['nomeUsuario'] = $_SESSION['nomeUsuario']; 
		
		$resultado['DADOS']['tipoMenu'] = $_SESSION['tipoMenu'];
		$resultado['DADOS']['naoMostraVoltar'] = $_SESSION['naoMostraVoltar'];
	
		$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
											USUARIO,
											IDENTIFICACAO,
											DIA_HORA,
											ORIGEM,                                        
											PAGINA_ACESSADA,
											VERSAO_WEB,
											FINALIZADO_AUTO,
											FLAG_SUCESSO)
				  VALUES (
							\''. $_SESSION['SESSAO_ID'] .'\', 
							\''. $_SESSION['perfilOperador'] .'\', 
							\''.$_SESSION['codigoIdentificacao'].'\',
							 current_timestamp, 
							\''. 'login.php' .'\',                        
							\''.$_POST['pagina'].'\',
							\''. 'Aliança Net ' .'\',                        
							\'N\',
							\''. "S" .'\' )';							
		jn_query($query);		 
	
	echo json_encode($resultado);

}
else if($dadosInput['tipo']=='alterarlogin'){
	
	$resultado['STATUS'] = 'OK';
	$resultado['MSG']    = 'Senha Alterada.';
	
	$senhaAtual = trim($dadosInput['sa']);
	$novaSenha = trim($dadosInput['ns']);
	$pesquisaSenha = '';
	$updateSenha = '';
	$senhaCriptografada = false;
	
	
	if (!rvc('VALIDA_SENHA_CRIPTOGRAFADA', 'SIM')) {
		$senhaDigitada = $novaSenha;		
		$senhaAtual = ($senhaAtual);
		$novaSenha = ($novaSenha);
		$pesquisaSenha = '(SENHA_ACESSO)';
		$senhaCriptografada = false;
	} else { 
		$senhaDigitada = $novaSenha;
		$senhaAtual = md5($senhaAtual);
		$novaSenha = md5($novaSenha);
		$pesquisaSenha = 'SENHA_CRIPTOGRAFADA';	
		$senhaCriptografada = true;
	}
	
	
	if($_SESSION['perfilOperador'] == 'BENEFICIARIO' && retornaValorConfiguracao('UTILIZA_VW_LOGIN') == 'SIM'){		
		$query = 'SELECT TOP 1 USUARIO, CODIGO_IDENTIFICACAO, DATA_ALTERACAO_SENHA, PERFIL_OPERADOR FROM VW_DADOS_LOGIN_NET WHERE CODIGO_IDENTIFICACAO ='. aspas($_SESSION['codigoIdentificacao']).' AND UPPER(SENHA) = ' . strtoupper(aspas($senhaAtual))  . ' AND UPPER(PERFIL_OPERADOR) = ' . aspas('BENEFICIARIO') . ' ORDER BY DATA_EXCLUSAO ';					
	}else{		
		$query = 'SELECT NUMERO_REGISTRO, USUARIO, CODIGO_IDENTIFICACAO, DATA_ALTERACAO_SENHA, PERFIL_OPERADOR FROM CFGLOGIN_DINAMICO_NET WHERE ' . $pesquisaSenha . ' = ' . aspas($senhaAtual). ' and CODIGO_IDENTIFICACAO ='. aspas($_SESSION['codigoIdentificacao']).' and PERFIL_OPERADOR = '.aspas($_SESSION['perfilOperador'])  ;
	}	
	
			
	$res = jn_query($query);
	
	if($row = jn_fetch_object($res)){
		if(!senhaValida($senhaDigitada)){
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Nova senha inválida. A senha deve atender aos requisitos:<br>
									Ter pelo menos uma letra minúscula<br>
									Ter pelo menos uma letra maiúscula<br>
									Ter pelo menos um um número<br>
									Ter 6 ou mais caracteres';
			echo json_encode($resultado);
			exit;
		}else if($novaSenha==$senhaAtual){
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'A nova senha deve ser diferente da senha atual.';
			echo json_encode($resultado);
			exit;
		}
		
		$queryLoginExist  = ' SELECT * FROM CFGLOGIN_DINAMICO_NET ';
		$queryLoginExist .= ' WHERE CODIGO_IDENTIFICACAO = ' . aspas($_SESSION['codigoIdentificacao']) . ' AND PERFIL_OPERADOR = ' . aspas($_SESSION['perfilOperador']);
		$resLoginExist = jn_query($queryLoginExist);
		if($rowLoginExist = jn_fetch_object($resLoginExist)){
			if($senhaCriptografada){
				$queryLogin = 'update CFGLOGIN_DINAMICO_NET set SENHA_ACESSO='.aspas('******').', SENHA_CRIPTOGRAFADA='.aspas($novaSenha).',DATA_ALTERACAO_SENHA=current_timestamp where numero_registro='. aspas($rowLoginExist->NUMERO_REGISTRO);
			}else{
				$queryLogin = 'update CFGLOGIN_DINAMICO_NET set SENHA_ACESSO='.aspas($novaSenha).',DATA_ALTERACAO_SENHA=current_timestamp where numero_registro='. aspas($rowLoginExist->NUMERO_REGISTRO);	
			}
		}else{		
			if($senhaCriptografada){		
				$queryLogin = " INSERT INTO CFGLOGIN_DINAMICO_NET (USUARIO, SENHA_ACESSO, CODIGO_IDENTIFICACAO, PERFIL_OPERADOR, SENHA_CRIPTOGRAFADA, DATA_ALTERACAO_SENHA) VALUES (" . aspas($_SESSION['codigoIdentificacao']) . ", " . aspas('******') . ", " . aspas($_SESSION['codigoIdentificacao']) . ", " . aspas($_SESSION['perfilOperador']) .", " . aspas($novaSenha) . ", current_timestamp)";
			}else{
				$queryLogin = " INSERT INTO CFGLOGIN_DINAMICO_NET (USUARIO, SENHA_ACESSO, CODIGO_IDENTIFICACAO, PERFIL_OPERADOR, DATA_ALTERACAO_SENHA) VALUES (" . aspas($_SESSION['codigoIdentificacao']) . ", " . aspas($novaSenha) . ", " . aspas($_SESSION['codigoIdentificacao']) . ", " . aspas($_SESSION['perfilOperador']) .", current_timestamp)";
			}
		}
					
		$res = jn_query($queryLogin);
	}else{
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Senha Atual não foi informada corretamente.';	
	}
	
	echo json_encode($resultado);

}else if($dadosInput['tipo']=='enviarEmail'){
	
	$resultado['STATUS'] = 'OK';
	$resultado['MSG']    = 'Email enviado.';
	
	$email = strtoupper($dadosInput['email']);

	$senhaCriptografada = false;
	
	if (!rvc('VALIDA_SENHA_CRIPTOGRAFADA', 'SIM')) { 
		$senhaCriptografada = false;
	} else { 
		$senhaCriptografada = true;
	}
	
	$query = "select distinct VW_ENDERECOEMAIL_AL2.*,cfglogin_dinamico_net.NUMERO_REGISTRO,cfglogin_dinamico_net.USUARIO,cfglogin_dinamico_net.SENHA_ACESSO from VW_ENDERECOEMAIL_AL2
			  inner join cfglogin_dinamico_net on CAST(cfglogin_dinamico_net.codigo_identificacao AS VARCHAR(15)) = VW_ENDERECOEMAIL_AL2.codigo_identificacao and  cfglogin_dinamico_net.perfil_operador = VW_ENDERECOEMAIL_AL2.perfil_operador
			  where UPPER(VW_ENDERECOEMAIL_AL2.endereco_email)=".aspas($email);
			
			
	$res = jn_query($query);
	
	$msgEmail = '';
	$endEmail = '';
	if(rvc('ENVIAR_SMS_ZENVIA_SENHA')=='SIM'){
			//require('../EstruturaEspecifica/smsZenvia.php');
			require('../lib/smsPointer.php');
	}
	$i = 0;
	while($row = jn_fetch_object($res)){
		$i++;
		if($msgEmail==''){
			$msgEmail = 'Ol&aacute;, '.$row->NOME.'<br><br>Recebemos uma solicita&ccedil;&atilde;o para reenvio da senha associada a este e-mail e usu&aacute;rio '.$row->CODIGO.'<br><br>Abaixo dados para acesso e na sequ&ecirc;ncia altere sua senha:<br><br>';
	
		}
		$updateSenha= 'update cfglogin_dinamico_net set DATA_ALTERACAO_SENHA = null where NUMERO_REGISTRO='.aspas($row->NUMERO_REGISTRO);
		jn_query($updateSenha);
		$msgEmail .= '<br>';
		$msgEmail .= '<b>Perfil  :</b> '.$row->PERFIL_OPERADOR.'<br>';
		$msgEmail .= '<b>Usuário :</b> '.$row->USUARIO.'<br>';
		$msgSms = '\n';
		$msgSms .= 'Perfil  : '.$row->PERFIL_OPERADOR.'\n';
		$msgSms .= 'Usu&aacute;rio : '.$row->USUARIO.'\n';
		if($senhaCriptografada){
			$chave = md5(time().$row->NUMERO_REGISTRO);
			$insertAlterarSenha = "insert into CFGALTER_SENHA_CRIPTO_AL2(TIME_GERACAO,NUMERO_REGISTRO_SENHA,CHAVE_ALTERACAO,FLAG_UTILIZADO)
									VALUES(".aspas(time()).",".aspas($row->NUMERO_REGISTRO).",".aspas($chave).",'N')";
			jn_query($insertAlterarSenha);						
			$msgEmail .= '<b>Link Alterar Senha   :</b> '. rvc('LINK_SITE').'/autenticacao/login?key='.$chave.'<br><br>';
			$msgSms   .= 'Link Alterar Senha   : '. rvc('LINK_SITE').'/autenticacao/login?key='.$chave;
		}else{
			$msgEmail .= '<b>Senha   :</b> '.$row->SENHA_ACESSO.'<br><br>';
			$msgSms   .= 'Senha   : '.$row->SENHA_ACESSO;
		}
		
		$msgSms   .= '\n https://abre.ai/eMl';
		if((rvc('ENVIAR_SMS_ZENVIA_SENHA')=='SIM')and (trim($row->TELEFONE)!='')){
			//enviaSmsZenvia('55'.trim($row->TELEFONE),$msgSms,$chave.$i.rand(1,1000));
			$retornoSms = enviaSmsPointer(remove_caracteresT(trim($row->TELEFONE)),$msgSms);
		}
	
		$endEmail = $row->ENDERECO_EMAIL;
	}
	
	$complementoEmail = '';
	
	$queryEmp = 'SELECT CODIGO_SMART FROM CFGEMPRESA ';
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);	
	
	if($rowEmp->CODIGO_SMART == '3423'){//Plena
		$complementoEmail .= '<p><img src="https://app.plenasaude.com.br/senhaPlena.jpg" alt="img_plena"></p>';
		$complementoEmail .= '<p><a href="https://app.plenasaude.com.br/AliancaAppNet2/Site/site">Criar nova Senha</a></p>';
		$complementoEmail .= '<p>Para sua segurança o link expira em 24 horas</p>';
	}
	

	if($msgEmail==''){
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Nenhum usuario cadastrado com esse email.';		
	}else{
		$msgEmailFinal = " 	<!DOCTYPE html>
							<html>
							<body>
								<p>$msgEmail</p>
								$complementoEmail
							</body>
							</html>
						 ";	
		$msgEmailFinal = utf8_decode($msgEmailFinal);
		
		if($rowEmp->CODIGO_SMART == '3423'){//Plena
			$assunto = utf8_decode('Recuperação de senha Plena Saúde');
		}else{
			$assunto = utf8_decode('Recuperação de senha');
		}
		
		if(disparaEmailFunc(strtolower(trim($endEmail)), $assunto, $msgEmailFinal)){
			$resultado['STATUS'] = 'OK';
			$resultado['MSG']    = 'Email enviado.';
		}else{
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Erro ao tentar enviar o email.';
		}
	}
	
	echo json_encode($resultado);

}else if($dadosInput['tipo']=='alterarCripto'){
	
	$resultado['STATUS'] = 'OK';
	$resultado['MSG']    = 'Senha Alterada.';
	
	$novaSenha = md5($dadosInput['ns']);
	
	$key = $dadosInput['key'];
	
	$query = 'SELECT * from CFGALTER_SENHA_CRIPTO_AL2 WHERE  CHAVE_ALTERACAO ='. aspas($key)  ;
			
			
	$res = jn_query($query);
	
	if($row = jn_fetch_object($res)){
		
		$time = ($row->TIME_GERACAO * ( 24 * 60 * 60));
		
		if($row->FLAG_UTILIZADO=='S'){
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Esse link já foi utilizado.';
		}else if(time()>$time){
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Esse link está expirado.';
		}else{	
			$sqlUpdate = 'update CFGLOGIN_DINAMICO_NET set SENHA_ACESSO='.aspas('******').', SENHA_CRIPTOGRAFADA='.aspas($novaSenha).',DATA_ALTERACAO_SENHA=current_timestamp where numero_registro='. aspas($row->NUMERO_REGISTRO_SENHA);
			$res = jn_query($sqlUpdate);
			jn_query('update CFGALTER_SENHA_CRIPTO_AL2 set FLAG_UTILIZADO ='.aspas('S'). ' where NUMERO_REGISTRO = '.$row->NUMERO_REGISTRO);
		}
	}else{
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Link de alteração inválido.';	
	}
	

	echo json_encode($resultado);

}else if($dadosInput['tipo']=='comboPerfil'){ 
	$queryEmp = 'SELECT CODIGO_SMART FROM CFGEMPRESA ';
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);	
	
	$query = 'select DISTINCT PERFIL_OPERADOR from CFGLOGIN_DINAMICO_NET WHERE 1 = 1 '  ;
			
	if($rowEmp->CODIGO_SMART == '3423'){//Plena
		$query .= ' AND PERFIL_OPERADOR <> "VENDEDOR" AND PERFIL_OPERADOR <> "FORNECEDOR"';
	}
	
	if($dadosInput['perfil']){
		$query .= ' AND PERFIL_OPERADOR = ' . aspas($dadosInput['perfil']);
	}
	
	$query .= ' ORDER BY PERFIL_OPERADOR';
	$res = jn_query($query);
	
	$retorno['PERFIS'] = array();
	while($row = jn_fetch_object($res)){
		$linha['VALOR'] = jn_utf8_encode($row->PERFIL_OPERADOR);
		$linha['DESC']  = primeiraMaiuscula(jn_utf8_encode($row->PERFIL_OPERADOR)); 	
		$retorno['PERFIS'][] =$linha; 		
	}
		
	if($rowEmp->CODIGO_SMART == '3423' && $dadosInput['perfil'] == ''){//Plena
		$linha['VALOR'] = jn_utf8_encode('OPERADOR');
		$linha['DESC']  = primeiraMaiuscula(jn_utf8_encode('MEDICO')); 	
		$retorno['PERFIS'][] =$linha; 
		$linha['VALOR'] = jn_utf8_encode('OPERADOR_AUDITOR');
		$linha['DESC']  = primeiraMaiuscula(jn_utf8_encode('AUDITOR')); 	
		$retorno['PERFIS'][] =$linha;	
		$linha['VALOR'] = jn_utf8_encode('OPERADOR_AUDITOR_AG');
		$linha['DESC']  = primeiraMaiuscula(jn_utf8_encode('AUDITOR AGENDAMENTO')); 	
		$retorno['PERFIS'][] =$linha;		
	}	
		
	if(count($retorno['PERFIS'])<2 and $dadosInput['perfil'] == ''){
		$retorno['PERFIS'] = array();
	}
	
	// caso queira desabilitar  combo de perfil para algum cliente fazer o $retorno['PERFIS'] = array(); na linha abaixo para o codigoSmart; 
	// $retorno['PERFIS'] = array();
	// caso queira renomear algum perfil, renomenar o DESC com um codigo smart dentro do while;
	
	echo json_encode($retorno);

}else if($dadosInput['tipo']=='alteracaoSenha'){
	
	$resultado['STATUS'] = 'OK';
	$resultado['MSG']    = 'Senha Alterada.';
	
	$novaSenha = trim($dadosInput['ns']);
	$codigoIdentificacao = trim($dadosInput['codIdent']);
	$perfilOperador = trim($dadosInput['perfil']);
	$cpfcnpj = trim($dadosInput['cpfcnpj']);
	$dataNascimento = trim($dadosInput['dataNascimento']);
	$pesquisaSenha = '';
	$updateSenha = '';
	$senhaCriptografada = false;
	
	$queryValidacao = '';
	if($perfilOperador == 'BENEFICIARIO'){
		$queryValidacao  = ' SELECT CODIGO_ASSOCIADO AS CODIGO_IDENTIFICACAO FROM PS1000 ';
		$queryValidacao .= ' WHERE NUMERO_CPF = ' . aspas($cpfcnpj);
		$queryValidacao .= ' AND CODIGO_ASSOCIADO = ' . aspas($codigoIdentificacao);
		
		if($dataNascimento != '')
			$queryValidacao .= ' AND DATA_NASCIMENTO = ' . dataToSql($dataNascimento);		
		
	}elseif($perfilOperador == 'EMPRESA'){
		$queryValidacao = 'SELECT CODIGO_EMPRESA AS CODIGO_IDENTIFICACAO FROM PS1010 WHERE NUMERO_CNPJ = ' . aspas($cpfcnpj) . ' AND CODIGO_EMPRESA = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'PRESTADOR'){
		$queryValidacao = 'SELECT CODIGO_PRESTADOR AS CODIGO_IDENTIFICACAO FROM PS5002 WHERE COALESCE(NUMERO_CNPJ,NUMERO_CPF) = ' . aspas($cpfcnpj) . ' AND CODIGO_PRESTADOR = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'OPERADOR'){
		$queryValidacao = 'SELECT CODIGO_IDENTIFICACAO FROM PS1102 WHERE NUMERO_CPF = ' . aspas($cpfcnpj) . ' AND CODIGO_IDENTIFICACAO = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'VENDEDOR'){
		$queryValidacao = 'SELECT CODIGO_IDENTIFICACAO FROM PS1102 WHERE NUMERO_CPF = ' . aspas($cpfcnpj) . ' AND CODIGO_IDENTIFICACAO = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'CORRETOR'){
		$queryValidacao = 'SELECT CODIGO_IDENTIFICACAO FROM PS1102 WHERE NUMERO_CNPJ = ' . aspas($cpfcnpj) . ' AND CODIGO_IDENTIFICACAO = ' . aspas($codigoIdentificacao);
	}
	
	$resValidacao = jn_query($queryValidacao);
	$rowValidacao = jn_fetch_object($resValidacao);
	
	if($rowValidacao->CODIGO_IDENTIFICACAO == ''){
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Usuário não encontrado, valide as informações inseridas.';
		echo json_encode($resultado);
		exit;
	}
	
	if (!rvc('VALIDA_SENHA_CRIPTOGRAFADA', 'SIM')) { 
		$senhaDigitada = $novaSenha;
		$novaSenha = ($novaSenha);
		$pesquisaSenha = '(SENHA_ACESSO)';
		$senhaCriptografada = false;
	} else { 
		$senhaDigitada = $novaSenha;
		$novaSenha = md5($novaSenha);
		$pesquisaSenha = 'SENHA_CRIPTOGRAFADA';	
		$senhaCriptografada = true;
	}
	
			
	$query = 'SELECT NUMERO_REGISTRO, USUARIO, CODIGO_IDENTIFICACAO, DATA_ALTERACAO_SENHA, PERFIL_OPERADOR FROM CFGLOGIN_DINAMICO_NET WHERE CODIGO_IDENTIFICACAO ='. aspas($codigoIdentificacao).' and PERFIL_OPERADOR = '.aspas($perfilOperador)  ;
			
	$res = jn_query($query);
	
	if($row = jn_fetch_object($res)){
		if(!senhaValida($senhaDigitada)){
			$resultado['STATUS'] = 'ERRO';
			$resultado['MSG']    = 'Nova senha inválida. A senha deve atender aos requisitos:<br>
									Ter pelo menos uma letra minúscula<br>
									Ter pelo menos uma letra maiúscula<br>
									Ter pelo menos um um número<br>
									Ter 6 ou mais caracteres';
			echo json_encode($resultado);
			exit;
		}
		
		if($senhaCriptografada){
			$sqlUpdate = 'update CFGLOGIN_DINAMICO_NET set SENHA_ACESSO='.aspas('******').', SENHA_CRIPTOGRAFADA='.aspas($novaSenha).',DATA_ALTERACAO_SENHA=current_timestamp where numero_registro='. aspas($row->NUMERO_REGISTRO);
		}else{
			$sqlUpdate = 'update CFGLOGIN_DINAMICO_NET set SENHA_ACESSO='.aspas($novaSenha).',DATA_ALTERACAO_SENHA=current_timestamp where numero_registro='. aspas($row->NUMERO_REGISTRO);	
		}
		$res = jn_query($sqlUpdate);
	}else{
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Não foi encontrada senha criada para este usuário.';	
	}
	
	echo json_encode($resultado);

}else if($dadosInput['tipo']=='cadastrarSenha'){
	
	$resultado['STATUS'] = 'OK';
	$resultado['MSG']    = 'Senha Cadastrada.';
	
	$novaSenha = trim($dadosInput['ns']);
	$codigoIdentificacao = trim($dadosInput['codIdent']);
	$perfilOperador = trim($dadosInput['perfil']);
	$cpfcnpj = trim($dadosInput['cpfcnpj']);
	$pesquisaSenha = '';
	$updateSenha = '';
	$senhaCriptografada = false;
	
	$queryValidacao = '';
	if($perfilOperador == 'BENEFICIARIO'){
		$queryValidacao = 'SELECT CODIGO_ASSOCIADO AS CODIGO_IDENTIFICACAO FROM PS1000 WHERE NUMERO_CPF = ' . aspas($cpfcnpj) . ' AND CODIGO_ASSOCIADO = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'EMPRESA'){
		$queryValidacao = 'SELECT CODIGO_EMPRESA AS CODIGO_IDENTIFICACAO FROM PS1010 WHERE NUMERO_CNPJ = ' . aspas($cpfcnpj) . ' AND CODIGO_EMPRESA = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'PRESTADOR'){
		$queryValidacao = 'SELECT CODIGO_PRESTADOR AS CODIGO_IDENTIFICACAO FROM PS5002 WHERE ((NUMERO_CNPJ = ' . aspas($cpfcnpj) . ') OR (NUMERO_CPF = ' . aspas($cpfcnpj) . ')) AND CODIGO_PRESTADOR = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'OPERADOR'){
		$queryValidacao = 'SELECT CODIGO_IDENTIFICACAO FROM PS1102 WHERE NUMERO_CPF = ' . aspas($cpfcnpj) . ' AND CODIGO_IDENTIFICACAO = ' . aspas($codigoIdentificacao);
	}elseif($perfilOperador == 'VENDEDOR'){
		$queryValidacao = 'SELECT CODIGO_IDENTIFICACAO FROM PS1102 WHERE NUMERO_CPF = ' . aspas($cpfcnpj) . ' AND CODIGO_IDENTIFICACAO = ' . aspas($codigoIdentificacao);
	}

	$resValidacao = jn_query($queryValidacao);
	$rowValidacao = jn_fetch_object($resValidacao);
	
	if($rowValidacao->CODIGO_IDENTIFICACAO == ''){
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Usuário não encontrado, valide as informações inseridas.';
		echo json_encode($resultado);
		exit;
	}
	
	$queryLoginExist  = ' SELECT * FROM CFGLOGIN_DINAMICO_NET ';
	$queryLoginExist .= ' WHERE CODIGO_IDENTIFICACAO = ' . aspas($codigoIdentificacao) . ' AND PERFIL_OPERADOR = ' . aspas($perfilOperador);
	$resLoginExist = jn_query($queryLoginExist);
	if($rowLoginExist = jn_fetch_object($resLoginExist)){
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Usuário já possui login criado, favor utilizar a rotina de alteração de senha.';
		echo json_encode($resultado);
		exit;
	}
	
	if (!rvc('VALIDA_SENHA_CRIPTOGRAFADA', 'SIM')) {		
		$senhaDigitada = $novaSenha;
		$novaSenha = ($novaSenha);
		$pesquisaSenha = '(SENHA_ACESSO)';
		$senhaCriptografada = false;
	} else { 
		$senhaDigitada = $novaSenha;
		$novaSenha = md5($novaSenha);
		$pesquisaSenha = 'SENHA_CRIPTOGRAFADA';	
		$senhaCriptografada = true;
	}
	
	if(!senhaValida($senhaDigitada)){
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Senha inválida. A senha deve atender aos requisitos:<br>
								Ter pelo menos uma letra minúscula<br>
								Ter pelo menos uma letra maiúscula<br>
								Ter pelo menos um um número<br>
								Ter 6 ou mais caracteres';
		echo json_encode($resultado);
		exit;
	}
	
	if($senhaCriptografada){		
		$queryInsert = " INSERT INTO CFGLOGIN_DINAMICO_NET (USUARIO, SENHA_ACESSO, CODIGO_IDENTIFICACAO, PERFIL_OPERADOR, SENHA_CRIPTOGRAFADA, DATA_ALTERACAO_SENHA) VALUES (" . aspas($codigoIdentificacao) . ", " . aspas('******') . ", " . aspas($codigoIdentificacao) . ", " . aspas($perfilOperador) .", " . aspas($novaSenha) . ", current_timestamp)";
	}else{
		$queryInsert = " INSERT INTO CFGLOGIN_DINAMICO_NET (USUARIO, SENHA_ACESSO, CODIGO_IDENTIFICACAO, PERFIL_OPERADOR, DATA_ALTERACAO_SENHA) VALUES (" . aspas($codigoIdentificacao) . ", " . aspas($novaSenha) . ", " . aspas($codigoIdentificacao) . ", " . aspas($perfilOperador) .", current_timestamp)";
	}

	if(!jn_query($queryInsert)){
		$resultado['STATUS'] = 'ERRO';
		$resultado['MSG']    = 'Não foi encontrada senha criada para este usuário.';	
	}
	
	echo json_encode($resultado);
}
else if($dadosInput['tipo']=='getCodigoSmart')
{

	if ($_SESSION['codigoSmart']!='')
		$resultado['DADOS']['CODIGOSMART'] = $_SESSION['codigoSmart'];
	else
		$resultado['DADOS']['CODIGOSMART'] = getCodigoSmart();

	echo json_encode($resultado);
	
}
else if($dadosInput['tipo']=='getPerfilOperador')
{
	
	$resultado['DADOS']['PERFILOPERADOR'] = $_SESSION['perfilOperador'];
	echo json_encode($resultado);
	
}
else if($dadosInput['tipo']=='getAliancaPx')
{
	
	$resultado['DADOS']['ALIANCAPX4NET'] = $_SESSION['AliancaPx4Net'];
	echo json_encode($resultado);
	
}
else if($dadosInput['tipo'] =='retornaValorConfiguracao')
{
	if(($host=='https://localhost:4200/') and ($dadosInput['idConfiguracao']=='CAPTCHA_KEY')){
		$retorno['DADOS'][] = '';
		echo json_encode($retorno);
		exit;
	}	
    $res   = jn_query("SELECT coalesce(Valor_Configuracao,'') as VALOR_CONFIGURACAO, coalesce(VALOR_COMPLEMENTO,'') as VALOR_COMPLEMENTO From CFGCONFIGURACOES_NET Where (Identificacao_Validacao = " . aspas($dadosInput['idConfiguracao']) . ")");
	
    if ($row=jn_fetch_object($res))
        $retorno['DADOS'][] = trim($row->VALOR_CONFIGURACAO) . trim($row->VALOR_COMPLEMENTO);
    else
        $retorno['DADOS'][] = '';

	echo json_encode($retorno);
	
}else if($dadosInput['tipo']=='caapsMlAlteracao' || $_GET['tipo'] == 'caapsMlAlteracao'){
	$matricula = ($dadosInput['dados']['cpf'] ? $dadosInput['dados']['cpf'] : $_GET['cpf']);	
		
	$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);
	
	$query  = ' SELECT CODIGO_ASSOCIADO_TMP ,NOME_ASSOCIADO, CODIGO_TITULAR_TMP ';
	$query .= ' FROM TMP_BENEF_CAAPSML ';
	$query .= ' WHERE MATRICULA = ' . aspas($matricula);		
	$res = jn_query($query);

	if($row = jn_fetch_object($res)){		
		if($row->ULTIMO_STATUS == 'CONCLUIDO'){
			$resultado['LOGADO'] = false;
			$resultado['MSG']    = 'Não é possível acessar o link após a assinatura do contrato!';	
		}else{
			$codigoIdentificacao         = $row->CODIGO_ASSOCIADO_TMP;
			$codigoIdentificacaoTitular  = $row->CODIGO_ASSOCIADO_TMP;
			$nomeUsuario                 = $row->NOME_ASSOCIADO;

			$_SESSION['codigoIdentificacao']          	= $codigoIdentificacao;
			$_SESSION['codigoIdentificacaoTitular']   	= $codigoIdentificacaoTitular;
			$_SESSION['nomeUsuario']           			= $nomeUsuario;
			$_SESSION['perfilOperador']        			= 'BENEFICIARIO_TMP';			
			$_SESSION['versaoAplicacao']       			= '1.0.0';
			$_SESSION['ErrorList']             			= array();
			$_SESSION['UrlAcesso']             			= $_SERVER['HTTP_REFERER'];
			$_SESSION['HorarioAcesso']         			= time();
			$_SESSION['IpUsuario']             			= $_SERVER['REMOTE_ADDR'];
			$_SESSION['nomeEmpresa']           			= $rowEmp->NOME_EMPRESA;
			$_SESSION['codigoSmart']           			= $rowEmp->CODIGO_SMART;
			$_SESSION['SESSAO_ID'] 			   			= jn_gerasequencial('CFGLOG_NET');
			$_SESSION['tipoMenu']	                    = 'OCULTO';	
			
			$resultado['LOGADO'] = true;
			$resultado['MSG']    = 'Você foi logado com sucesso.';
			$resultado['DADOS']['codigoIdentificacao'] =  $_SESSION['codigoIdentificacao'];
			$resultado['DADOS']['codigoIdentificacaoTitular'] = $_SESSION['codigoIdentificacaoTitular'];
			
			$resultado['DADOS']['perfilOperador'] = $_SESSION['perfilOperador'];
			$resultado['DADOS']['nomeUsuario'] = $_SESSION['nomeUsuario']; 
						
			$resultado['DADOS']['tipoMenu'] = $_SESSION['tipoMenu'];
						
			$resultado['DESTINO'] = 'site/contrato';
			$resultado['DESTINO_JSON']['tabela'] = 'TMP_BENEF_CAAPSML';
			$resultado['DESTINO_JSON']['titulo'] = 'Beneficiários CAAPSML';
			$resultado['DESTINO_JSON']['filtros'] = '[]';
			
			$query = 'INSERT INTO CFGLOG_NET (	SESSAO_ID,
												USUARIO,
												IDENTIFICACAO,
												DIA_HORA,
												ORIGEM,                                        
												PAGINA_ACESSADA,
												VERSAO_WEB,
												FINALIZADO_AUTO,
												FLAG_SUCESSO)
					  VALUES (
								\''. $_SESSION['SESSAO_ID'] .'\', 
								\''. $_SESSION['perfilOperador'] .'\', 
								\''.$_SESSION['codigoIdentificacao'].'\',
								 current_timestamp, 
								\''. 'login.php' .'\',                        
								\''.$_POST['pagina'].'\',
								\''. 'Aliança Net ' .'\',                        
								\'N\',
								\''. "S" .'\' )';							
			jn_query($query);	
		}		
	}else{
		$resultado['LOGADO'] = false;
		$resultado['MSG']    = 'Matricula não encontrada, reveja os dados digitados e tente novamente!';	
	}
	
	echo json_encode($resultado);

}else if($dadosInput['tipo']=='css'){
	
	$resultado = array();
	$query = "SELECT * FROM APP_CSS";
	$res = jn_query($query);
	while($row = jn_fetch_object($res)){
		$item = array();
		$item['CLASSE_CSS'] = $row->CLASSE_CSS;
		$item['CSS'] = $row->CSS;
		//$item['TIPO'] = $row->TIPO;
		$item['FILTRO'] = $row->FILTRO;
		$item['PERFIL'] = $row->PERFIL;
		$resultado['CSS'][$row->TIPO][] = $item;
		
	}
	
	echo json_encode($resultado);
}
else if($dadosInput['tipo']=='session')
{
	
	$resultado = array();
	$resultado = $_SESSION;
	
	echo json_encode($resultado);
}




function formatCnpjCpf($value)
{
  $cnpj_cpf = preg_replace("/\D/", '', $value);
  
  if (strlen($cnpj_cpf) === 11) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
  } 
  
  return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);

}

function senhaValida($senha) {

    return preg_match('/[a-z]/', $senha) // tem pelo menos uma letra minúscula
     && preg_match('/[A-Z]/', $senha) // tem pelo menos uma letra maiúscula
     && preg_match('/[0-9]/', $senha) // tem pelo menos um número
     && preg_match('/^[\w$*@+]{6,}$/', $senha); // tem 6 ou mais caracteres	 
}


function getCodigoSmart(){

	$queryEmpresa = 'Select CODIGO_SMART from cfgempresa';
	$resEmpresa = jn_query($queryEmpresa);
	$rowEmpresa = jn_fetch_object($resEmpresa);	

	return	$rowEmpresa->CODIGO_SMART;

}

function registraAssociadoAPUEL($ArrDados){//Funcao temporaria, para Vidamax		
	$_GET['paramBenef'] = $ArrDados['dados']['cpf'];
	require('../EstruturaEspecifica/requestLoginAPUEL.php');
	
	$queryAssoc = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE NUMERO_CPF = ' . aspas($ArrDados['dados']['cpf']) . ' AND TIPO_ASSOCIADO ="T"';
	$resAssoc = jn_query($queryAssoc);
	$rowAssoc = jn_fetch_object($resAssoc);	
		
	header('Location: login.php?cpf='.$ArrDados['dados']['cpf'].'&id='.$rowAssoc->CODIGO_ASSOCIADO.'&tipo=vendaOnline');	
}


function registraAssociadoCaapsML($ArrDados){//Funcao temporaria, para Vidamax			
	$_GET['paramBenef'] = $ArrDados['dados']['cpf'];
	
	require('../EstruturaEspecifica/requestLoginCAAPSML.php');
	
	$queryAssoc = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE NUMERO_CPF = ' . aspas($ArrDados['dados']['cpf']) . ' AND TIPO_ASSOCIADO ="T"';
	$resAssoc = jn_query($queryAssoc);
	$rowAssoc = jn_fetch_object($resAssoc);	
		
	header('Location: login.php?cpf='.$ArrDados['dados']['cpf'].'&id='.$rowAssoc->CODIGO_ASSOCIADO.'&tipo=vendaOnline&entidade=CAAPSML');		
}

function abreProcessoCaapsML($ArrDados){//Funcao temporaria, para Vidamax			
	$queryAssoc = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE NUMERO_CPF = ' . aspas($ArrDados['dados']['cpf']) . ' AND TIPO_ASSOCIADO ="T"';
	$resAssoc = jn_query($queryAssoc);
	$rowAssoc = jn_fetch_object($resAssoc);	
		
	header('Location: login.php?cpf='.$ArrDados['dados']['cpf'].'&id='.$rowAssoc->CODIGO_ASSOCIADO.'&tipo=vendaOnline&entidade=caapsMlAlteracao');			
}


?>