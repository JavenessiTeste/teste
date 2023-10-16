<?php

if($_GET['PKID']){
	$_SESSION['idDevice']  = $_GET['PKID'];
	$_SESSION['APP']	   = true;
	
	$qry = "SELECT APP_USUARIO_INTERNO.CODIGO_INTERNO, APP_USUARIO_INTERNO.CODIGO_USUARIO, APP_USUARIO_INTERNO.PERFIL_USUARIO, APP_VW_DADOS_LOGIN_V3.QUANTIDADE_CONTRATOS, CFGEMPRESA.CODIGO_SMART, APP_VW_DADOS_LOGIN_V3.CODIGO_TIPO_CARACTERISTICA, PS1030.REDE_INDICADA, PS1000.CODIGO_PLANO,PS1000.CODIGO_TITULAR,Ps1000.NOME_ASSOCIADO FROM APP_LOGIN_AUTOMATICO
			INNER JOIN  APP_USUARIO_INTERNO ON APP_USUARIO_INTERNO.CODIGO_INTERNO = APP_LOGIN_AUTOMATICO.CODIGO_INTERNO 
			INNER JOIN  APP_VW_DADOS_LOGIN_V3  ON APP_VW_DADOS_LOGIN_V3.CODIGO_USUARIO = APP_USUARIO_INTERNO.CODIGO_USUARIO
			LEFT OUTER JOIN PS1000 ON (APP_VW_DADOS_LOGIN_V3.CODIGO_USUARIO = PS1000.CODIGO_ASSOCIADO) 
			LEFT OUTER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) 
			INNER JOIN  CFGEMPRESA ON (1=1)
			WHERE APP_LOGIN_AUTOMATICO.FLAG_CONECTAR_AUTOMATICO = 'S' AND APP_LOGIN_AUTOMATICO.CODIGO_DEVICE = " . aspas($_SESSION['idDevice']);

	$resQuery = jn_query($qry);
					

					
	if ($objResult = jn_fetch_object($resQuery))
	{
			$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
			$resEmp = jn_query($queryEmp);
			$rowEmp = jn_fetch_object($resEmp);
			
			$_SESSION['codigoIdentificacao']          = $objResult->CODIGO_USUARIO;
			$_SESSION['codigoIdentificacaoTitular']   = $objResult->CODIGO_TITULAR;
			$_SESSION['nomeUsuario']           = jn_utf8_encode($objResult->NOME_ASSOCIADO);
			$_SESSION['perfilOperador']        = $loginWeb['perfilOperador'];
			$_SESSION['versaoAplicacao']       = '1.0.0';
			$_SESSION['ErrorList']             = array();
			$_SESSION['UrlAcesso']             = $_SERVER['HTTP_REFERER'];
			$_SESSION['HorarioAcesso']         = @mktime();
			$_SESSION['IpUsuario']             = $_SERVER['REMOTE_ADDR'];
			$_SESSION['nomeEmpresa']           = $rowEmp->NOME_EMPRESA;
			$_SESSION['codigoSmart']           = $rowEmp->CODIGO_SMART;
			//$_SESSION['SESSAO_ID'] 			   = jn_gerasequencial('CFGLOG_NET');
		
			$_SESSION['CODIGO_INTERNO']             =  $objResult->CODIGO_INTERNO;
			$_SESSION['CODIGO_USUARIO']             =  $objResult->CODIGO_USUARIO;
			$_SESSION['PERFIL_USUARIO']             =  $objResult->PERFIL_USUARIO;
			$_SESSION['CODIGO_CONTRATANTE']         =  $objResult->CODIGO_CONTRATANTE;	 
			$_SESSION['QUANTIDADE_CONTRATOS']       =  $objResult->QUANTIDADE_CONTRATOS;
			$_SESSION['CODIGO_TIPO_CARACTERISTICA'] =  $objResult->CODIGO_TIPO_CARACTERISTICA; 
			$_SESSION['CODIGO_SMART']               =  $objResult->CODIGO_SMART;
			$_SESSION['REDE_INDICADA']			    =  $objResult->REDE_INDICADA;
			$_SESSION['CODIGO_PLANO']               =  $objResult->CODIGO_PLANO; 
		 
	}
}

?>
