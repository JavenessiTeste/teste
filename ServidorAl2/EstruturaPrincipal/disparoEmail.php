<?php
require_once('../lib/base.php');
require_once('../lib/class.phpmailer.php');
require_once('../lib/class.smtp.php');
require_once('../lib/PHPMailerAutoload.php');

if($_GET['codigoModelo']){
	header ('Content-type: text/html; charset=ISO-8859-1');
	$codigoAssociado = $_GET['codigoAssociado'];
	$codigoVendedor = $_GET['codigoVendedor'];
	$codigoModelo = $_GET['codigoModelo'];
	$codObs = $_GET['obs'];	
	
	$contatoOperadora = retornaValorConfiguracao('EMAIL_CADASTRO');
	$nomeAssociado = '';
	
	if(retornaValorConfiguracao('PAGINA_INICIAL') == 'CONTRATO'){
		$linkContratos = retornaValorConfiguracao('LINK_LOGIN_EXTERNO') . $codigoAssociado . '&d=site/contrato';			
	}else{
		
		if($_GET['vnd']){				
			$queryConfigDec  = ' SELECT COALESCE(FLAG_EXIGIR_DECL_SAUDE, "N") AS FLAG_EXIGIR_DECL_SAUDE, COALESCE(FLAG_PORTABILIDADE, "N") AS FLAG_PORTABILIDADE FROM VND1000_ON ';
			$queryConfigDec .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
			$queryConfigDec .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
			
			$resConfigDec = jn_query($queryConfigDec);
			$rowConfigDec = jn_fetch_object($resConfigDec);
		}
		
		if($rowConfigDec->FLAG_EXIGIR_DECL_SAUDE == 'N' || $rowConfigDec->FLAG_PORTABILIDADE == 'S'){
			$linkContratos = retornaValorConfiguracao('LINK_LOGIN_EXTERNO') . $codigoAssociado . '&d=site/contrato';			
		}else{			
			$linkContratos = retornaValorConfiguracao('LINK_LOGIN_EXTERNO') . $codigoAssociado . '&d=site/declaracaoSaude';	
		}
	}
	
	$tabelaAssociado = $_GET['vnd'] ? 'VND1000_ON' : '';
	if(!$tabelaAssociado){
		$tabelaAssociado = $_GET['tmp'] ? 'TMP1000_NET' : 'PS1000';	
	}
	
	$tabelaEnd = $_GET['vnd'] ? 'VND1001_ON' : '';
	if(!$tabelaEnd){
		$tabelaEnd = $_GET['tmp'] ? 'TMP1001_NET' : 'PS1001';	
	}
	
	$queryAssociado  = ' SELECT NOME_ASSOCIADO, ENDERECO_EMAIL FROM ' . $tabelaAssociado;
	$queryAssociado .= ' INNER JOIN ' . $tabelaEnd  . ' ON ' . $tabelaAssociado . '.CODIGO_ASSOCIADO = ' . $tabelaEnd . '.CODIGO_ASSOCIADO '; 
	$queryAssociado .= ' WHERE ' . $tabelaAssociado . '.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);	
	$resAssociado = jn_query($queryAssociado);
	$rowAssociado = jn_fetch_object($resAssociado);
	$enderecoEmail = $rowAssociado->ENDERECO_EMAIL;
	$nomeAssociado = $rowAssociado->NOME_ASSOCIADO;		
	
	if($codigoVendedor){
		$enderecoEmail = '';
		$queryVend  = ' SELECT ENDERECO_EMAIL FROM PS1101';
		$queryVend .= ' WHERE CODIGO_IDENTIFICACAO = ' . aspas($codigoVendedor);
		$resVend = jn_query($queryVend);
		$rowVend = jn_fetch_object($resVend);
		$enderecoEmail = $rowVend->ENDERECO_EMAIL;
	}
	
	if($codObs){
		$descObservacao = '';
		$queryObs  = ' SELECT DESCRICAO_STATUS FROM VND1000STATUS_ON ';
		$queryObs .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
		$resObs = jn_query($queryObs);
		$rowObs = jn_fetch_object($resObs);
		$descObservacao = $rowObs->DESCRICAO_STATUS;
	}
	
	if($codigoModelo == 10){
		$enderecoEmail = $contatoOperadora;
	}
	
	$linkAlteraBenef = retornaValorConfiguracao('LINK_PORTAL_AL2')  .'?t=A&d=site/autoContratacao&ben='. $codigoAssociado;

	$queryModelo  = ' SELECT ';
	$queryModelo .= ' 	ASSUNTO_EMAIL, CORPO_EMAIL ';
	$queryModelo .= ' FROM CFGMODELOS_EMAIL ';
	$queryModelo .= ' WHERE CODIGO_MODELO = ' . aspas($codigoModelo);
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_row($resModelo);	
	$assunto = $rowModelo[0];
	$corpoEmail = $rowModelo[1];
	$corpoEmail = str_replace('__**__CODIGO_ASSOCIADO__**__', $codigoAssociado, $corpoEmail);
	$corpoEmail = str_replace('__**__NOME_ASSOCIADO__**__', $nomeAssociado, $corpoEmail);
	$corpoEmail = str_replace('__**__LINK_DOCUMENTACAO__**__', $linkContratos , $corpoEmail);
	$corpoEmail = str_replace('__**__LINK_BENEFICIARIO_ALT__**__', $linkAlteraBenef , $corpoEmail);
	
	if($descObservacao){
		$corpoEmail .= ' <br> ' . $descObservacao;
	}
	
	disparaEmail($enderecoEmail, $assunto, $corpoEmail, retornaValorConfiguracao('EMAIL_OCULTO_VENDAS'));
}

function disparaEmail($enderecoEmail, $assunto, $corpoEmail, $copiaOculta = ''){
	
	$resEmpresa = jn_query($queryEmpresa = 'SELECT NOME_EMPRESA FROM CFGEMPRESA');
	$rowEmpresa = jn_fetch_object($resEmpresa);
	
	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');

	$nomeEmpresaEmail = retornaValorConfiguracao('NOME_EMPRESA_EMAIL');
	if(retornaValorConfiguracao('UTF8_EMAIL') == 'SIM')
		$nomeEmpresaEmail = utf8_decode(retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));

	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), $nomeEmpresaEmail);
	$mail->AddAddress($enderecoEmail, $enderecoEmail);
	if($copiaOculta){
		$mail->AddBCC($copiaOculta, "");
	}
	
	if(retornaValorConfiguracao('UTF8_EMAIL') == 'SIM'){
		$mail->Subject = utf8_decode($assunto);
		$mail->MsgHTML(utf8_decode($corpoEmail));
	}else{
		$mail->Subject = $assunto;
		$mail->MsgHTML($corpoEmail);
	}

	if(!$mail->Send()) {				
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = $mail->ErrorInfo . ' (disparoEmail.php) ' . $enderecoEmail;
		
		echo json_encode($retorno);
	}elseif($_GET['retornaMensagem']){		
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = jn_utf8_encode('E-mail enviado para ' . $enderecoEmail);

		echo json_encode($retorno);
	}	
	
}
function disparaEmailRetorno($enderecoEmail, $assunto, $corpoEmail, $copiaOculta = ''){
	
	//print_r('$enderecoEmail' . $enderecoEmail);
	//print_r('$assunto' . $assunto);
	//print_r('$corpoEmail' . $corpoEmail);
	
	$resEmpresa = jn_query($queryEmpresa = 'SELECT NOME_EMPRESA FROM CFGEMPRESA');
	$rowEmpresa = jn_fetch_object($resEmpresa);
	
	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPSecure = "tsl";
	$mail->AuthType='LOGIN';
	//$mail->SMTPDebug = SMTP::DEBUG_CONNECTION ;
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

		 
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');
	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));
	$mail->AddAddress($enderecoEmail, $enderecoEmail);
	if($copiaOculta){
		$mail->AddBCC($copiaOculta, "");
	}
	$mail->Subject = utf8_decode($assunto);
	$mail->MsgHTML(($corpoEmail));

	if(!$mail->Send()) {				
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = $mail->ErrorInfo . ' (disparoEmail.php) ' . $enderecoEmail;
		
		return ($retorno);
	}elseif($_GET['retornaMensagem']){		
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = jn_utf8_encode('E-mail enviado para ' . $enderecoEmail);

		return ($retorno);
	}	
	
}

?>