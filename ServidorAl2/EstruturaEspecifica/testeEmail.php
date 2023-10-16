<?php 
require_once('../lib/base.php');
require_once('../lib/class.phpmailer.php');
require_once('../lib/class.smtp.php');
require_once('../lib/PHPMailerAutoload.php');


echo disparaEmailFuncTeste('leonardo@javenessi.com.br', 'Assunto Teste Disparo', 'Disparo de Email realizado com sucesso.');
exit;


function disparaEmailFuncTeste($emailAssociado, $assunto, $corpoEmail)
{
	
	$mail = new PHPMailer();
	$mail->isSMTP();
	
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	
	$mail->SMTPAutoTLS = true;
	$mail->Host     = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port     = retornaValorConfiguracao('PORT_EMAIL');	
	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), 'teste1234567');
	$mail->AddAddress($emailAssociado, $emailAssociado);
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoEmail);
	$mail->SMTPDebug = 2;       // Debugar: 1 = erros e mensagens, 2 = mensagens apenas


		
	if(!$mail->Send()) {
		echo "Não";
		return false;		
	}else{
		echo "Sim";
		return true;
	}
}

?>