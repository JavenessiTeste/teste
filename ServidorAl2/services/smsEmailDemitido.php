<?php

require('../lib/base.php');
require('../lib/smsPointer.php');

$queryEmp = 'SELECT CODIGO_SMART FROM CFGEMPRESA ';
$resEmp = jn_query($queryEmp);
$rowEmp= jn_fetch_object($resEmp);




if(strtoupper(md5($_GET['op']. $_GET['cod'].$rowEmp->CODIGO_SMART)) !=strtoupper($_GET['vf'])){
	echo 'ERRO';
	exit;
}

$codigoAssociado = $_GET['cod'];
$operador= $dadosInput['op'];
$hash= $dadosInput['vf'];

$query = 'Select  PS1000.NOME_ASSOCIADO,PS1010.NOME_EMPRESA  from PS1000
		  inner join ps1010 on ps1010.CODIGO_EMPRESA = ps1000.CODIGO_EMPRESA
		  where PS1000.CODIGO_ASSOCIADO  = '.aspas($codigoAssociado);
$res  = jn_query($query);
$row  = jn_fetch_object($res);

$nomeBeneficiario = $row->NOME_ASSOCIADO;
$aux = explode(' ',$nomeBeneficiario);
$primeiroNome = $aux[0];
$nomeEmpresa =  $row->NOME_EMPRESA;

$query = 'Select  MENSAGEM  from ESP_MENSAGENS where TIPO_MENSAGEM = '.aspas('EMAIL_FUNCIONARIO');
$res  = jn_query($query);
$row  = jn_fetch_object($res);

$textoEmail = $row->MENSAGEM;


$query = 'Select  MENSAGEM  from ESP_MENSAGENS where TIPO_MENSAGEM = '.aspas('SMS_FUNCIONARIO');
$res  = jn_query($query);
$row  = jn_fetch_object($res);

$textoSms = $row->MENSAGEM;

$query = 'Select  MENSAGEM  from ESP_MENSAGENS where TIPO_MENSAGEM = '.aspas('PUSH_FUNCIONARIO');
$res  = jn_query($query);
$row  = jn_fetch_object($res);

$textoPUSH = $row->MENSAGEM;


$textoEmail = str_replace('[PRIMEIRO_NOME]',$primeiroNome,$textoEmail);
$textoEmail = str_replace('[NOME_COMPLETO]',$nomeBeneficiario,$textoEmail);
$textoEmail = str_replace('[NOME_EMPRESA]',$nomeEmpresa,$textoEmail);
$textoEmail = str_replace("\n", "<br>", $textoEmail);
$textoEmail = str_replace("\r", "", $textoEmail);
$textoEmail = preg_replace('/\s/',' ',$textoEmail);

$textoEmail = utf8_encode($textoEmail);
//echo disparaEmailFuncionario('diego2607@gmail.com', 'Continuidade Plena', $textoEmail);
//exit;

$textoSms = str_replace('[PRIMEIRO_NOME]',$primeiroNome,$textoSms);
$textoSms = str_replace('[NOME_COMPLETO]',$nomeBeneficiario,$textoSms);
$textoSms = str_replace('[NOME_EMPRESA]',$nomeEmpresa,$textoSms);

$textoSms = str_replace("\n", " ", $textoSms);
$textoSms = str_replace("\r", "", $textoSms);
$textoSms = preg_replace('/\s/',' ',$textoSms);

$textoSms = utf8_encode($textoSms);

$textoPUSH = str_replace('[PRIMEIRO_NOME]',$primeiroNome,$textoPUSH);
$textoPUSH = str_replace('[NOME_COMPLETO]',$nomeBeneficiario,$textoPUSH);
$textoPUSH = str_replace('[NOME_EMPRESA]',$nomeEmpresa,$textoPUSH);
$textoPUSH = str_replace("\n", "<br>", $textoPUSH);
$textoPUSH = str_replace("\r", "", $textoPUSH);
$textoPUSH = preg_replace('/\s/',' ',$textoPUSH);

$textoPUSH = utf8_encode($textoPUSH);

//echo $textoSms;
//exit;

$sqlEmail = 'SELECT COALESCE(EMAIL_CONFIRMADO,PS1015.ENDERECO_EMAIL) EMAIL_CONFIRMADO, DATA_CONFIRMACAO_EMAIL FROM PS1000
			 LEFT JOIN PS1015 ON PS1015.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
			 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($_GET['cod']);

$resEmail  = jn_query($sqlEmail);
$retorno = 'Email : ';
if($rowEmail = jn_fetch_object($resEmail)){	
	if(trim($rowEmail->EMAIL_CONFIRMADO)!=''){
		//$rowEmail->EMAIL_CONFIRMADO = 'diego2607@gmail.com';
		$retorno .= disparaEmailFuncionario($rowEmail->EMAIL_CONFIRMADO, 'Continuidade Plena', $textoEmail);
	}else{
		$retorno .= 'Sem Email';
	}			
}else{
	$retorno .= 'Sem Email';
}
$retorno .= '|Sms : ';
$sqlEmail = 'Select  coalesce(CELULAR_CONFIRMADO,cast(PS1006.CODIGO_AREA as varchar(2))+Ps1006.NUMERO_TELEFONE) CELULAR_CONFIRMADO, DATA_CONFIRMACAO_CELULAR from PS1000
			 left join PS1006 on PS1006.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
			 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($_GET['cod']);

$resEmail  = jn_query($sqlEmail);

if($rowEmail = jn_fetch_object($resEmail)){	
	if(trim($rowEmail->CELULAR_CONFIRMADO)!=''){
		//$rowEmail->CELULAR_CONFIRMADO = '41998061407';
		$retornoSms = enviaSmsPointer(trim($rowEmail->CELULAR_CONFIRMADO),$textoSms);
		if($retornoSms==''){
			$retorno .='OK';
		}else{
			$retorno .= $retornoSms;
		}
	}else{
		$retorno .= 'Sem Celular';
	}
			
}else{
	$retorno .= 'Sem Celular';
}

$retorno .= '|Push : ';
$sqlPush = ' Select * from app_usuario_interno 
							inner join Ps1000 on (Ps1000.Codigo_Associado = app_usuario_interno.Codigo_Usuario) 
							where Ps1000.Codigo_Associado =  '.aspas($_GET['cod']);

$resPush  = jn_query($sqlPush);

if($rowPush = jn_fetch_object($resPush)){	
	$retorno .='OK';		
	$insert = 'Insert into APP_CONTROLE_PUSH( 
							TITULO_MENSAGEM,              
							DESCRICAO_MENSAGEM,           
							CODIGO_ASSOCIADO,             
							CODIGO_INTERNO,               
							PRIORIDADE_PUSH               
							)
							 VALUES (' . aspas('Continuidade').
										 ',' . aspas($textoPUSH).
										 ',' . aspas($rowPush->CODIGO_USUARIO).
										 ',' . aspas($rowPush->CODIGO_INTERNO).
										 ',' . aspas('5') . ');';	
	jn_query($insert);
}else{
	$retorno .= 'Nunca Entrou Celular ';
}

echo $retorno;

function disparaEmailFuncionario($emailAssociado, $assunto = '', $corpoMSG = ''){
	
	if($assunto == '')
		$assunto = 'Assunto Teste';	
	
	if($corpoMSG == '')
		$corpoMSG = 'Mensagem Teste';	
	
	$corpoMSG = utf8_decode($corpoMSG);
	$assunto = utf8_decode($assunto);

	require_once('../lib/class.phpmailer.php');
	require_once("../lib/PHPMailerAutoload.php");

	$mail   = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	$mail->charSet = "UTF-8";
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');	
	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));	
	$mail->AddAddress($emailAssociado, $emailAssociado);
	
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoMSG);
	
	if(!$mail->Send()) {		
		return  "Erro: " . $mail->ErrorInfo;
	}else{
		return "OK";
	}
}

?>