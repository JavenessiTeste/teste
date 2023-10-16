<?php
require('../lib/base.php');


if($_GET['cod']!=''){


	$qry = "SELECT  PS1000.CODIGO_ASSOCIADO CODIGO_USUARIO, CFGEMPRESA.CODIGO_SMART, PS1030.REDE_INDICADA, PS1000.CODIGO_PLANO,PS1000.CODIGO_TITULAR,Ps1000.NOME_ASSOCIADO FROM PS1000
			LEFT OUTER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) 
			INNER JOIN  CFGEMPRESA ON (1=1)
			WHERE PS1000.CODIGO_ASSOCIADO = " . aspas($_GET['cod']);

	$resQuery = jn_query($qry);
					

					
	if ($objResult = jn_fetch_object($resQuery)){
		
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
		
			$_SESSION['CODIGO_USUARIO']             =  $objResult->CODIGO_USUARIO;
			$_SESSION['PERFIL_USUARIO']             =  'BENEFICIARIO';
			$_SESSION['CODIGO_CONTRATANTE']         =  $objResult->CODIGO_CONTRATANTE;	 
			$_SESSION['QUANTIDADE_CONTRATOS']       =  '1';
			$_SESSION['CODIGO_TIPO_CARACTERISTICA'] =  '1'; 
			$_SESSION['CODIGO_SMART']               =  $objResult->CODIGO_SMART;
			$_SESSION['REDE_INDICADA']			    =  $objResult->REDE_INDICADA;
			$_SESSION['CODIGO_PLANO']               =  $objResult->CODIGO_PLANO; 
		    $_GET['PX'] = 'OK';
	}
}else{
	exit;
}

	$queryOp = 'select * from PS1100 where CODIGO_IDENTIFICACAO ='.aspas($_GET['op']);
	$resOp = jn_query($queryOp);
	$rowOp = jn_fetch_object($resOp);
	
	$_GET['opd'] = $_GET['op'].' - '.$rowOp->NOME_COMPLETO;
	
	if(strtoupper(md5($_GET['op']. $_GET['cod'])) !=strtoupper($_GET['vf'])){
		$retorno['STATUS'] = 'ERRO';
		$retorno['HTML']   = 'Erro Verificação.';	
		echo json_encode($retorno);
		exit;
	}




	if($_GET['t'] == '1'){
		$dadosInput['tipo'] ='consultaCancelamento';
	}else if($_GET['t'] == '2'){
		$dadosInput['tipo'] ='ConfirmaCancelamento';
		$_GET['nascimentoTitular'] = str_replace("'","", DataToSql($_GET['nascimentoTitular']).'T04:00:00.000Z');
		
		$dadosInput['dado'] =$_GET;
	}

	
	include("../EstruturaEspecifica/multaprorata.php");


?>