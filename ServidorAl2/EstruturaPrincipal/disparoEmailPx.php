<?php
require_once('../lib/base.php');
require_once('../lib/class.phpmailer.php');
require_once('../lib/class.smtp.php');
require_once('../lib/PHPMailerAutoload.php');
//header ('Content-type: text/html; charset=ISO-8859-1');

disparaEmail('diego2607@gmail.com', 'Teste', 'Teste');

function disparaEmail($enderecoEmail, $assunto, $corpoEmail){
	
	//print_r('$enderecoEmail' . $enderecoEmail);
	//print_r('$assunto' . $assunto);
	//print_r('$corpoEmail' . $corpoEmail);
	
	$resEmpresa = jn_query($queryEmpresa = 'SELECT NOME_EMPRESA FROM CFGEMPRESA');
	$rowEmpresa = jn_fetch_object($resEmpresa);
	
	$mail = new PHPMailer();
	$mail->SMTPDebug = 3 ;
	$mail->isSMTP();
	//$mail->SMTPSecure = "tls";
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	$mail->Host = 'mail.medicalhealth.com.br';//retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = true;//retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = 'teste@medicalhealth.com.br';//retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password ='Test@#88' ;//retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = 587;//retornaValorConfiguracao('PORT_EMAIL');
	$mail->SetFrom('teste@medicalhealth.com.br','Teste');//retornaValorConfiguracao('EMAIL_PADRAO'), retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));
	$mail->AddAddress($enderecoEmail, $enderecoEmail);
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoEmail);

	if(!$mail->Send()) {				
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = $mail->ErrorInfo . ' (disparoEmail.php) ' . $enderecoEmail;
		
		echo json_encode($retorno);
	}else{		
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = jn_utf8_encode('E-mail enviado para ' . $enderecoEmail);

		echo json_encode($retorno);
	}	
	
}

?>