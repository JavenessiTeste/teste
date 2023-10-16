<?php
require('../lib/base.php');
require_once('../lib/class.phpmailer.php');
require_once('../lib/class.smtp.php');
require_once('../lib/PHPMailerAutoload.php');
header ('Content-type: text/html; charset=ISO-8859-1');

if($_GET['numeroRegistro']){
	
	$enderecoEmail = '';
	$nomeDestinatario = '';
	$faturaTecnica = '';
	$linkNFSe = '';
	
	$queryFat  = ' SELECT CODIGO_ASSOCIADO, CODIGO_EMPRESA FROM PS1020 ';
	$queryFat .= ' WHERE PS1020.NUMERO_REGISTRO = ' . aspas($_GET['numeroRegistro']);
	$resFat = jn_query($queryFat);
	$rowFat = jn_fetch_object($resFat);
	
	if($rowFat->CODIGO_ASSOCIADO != ''){
		$query  = ' SELECT NOME_ASSOCIADO AS NOME, ENDERECO_EMAIL FROM PS1000 ';
		$query .= ' INNER JOIN PS1001 ON (PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ';
		$query .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($rowFat->CODIGO_ASSOCIADO);
		$res = jn_query($query);
		$row = jn_fetch_object($res);
		
		$nomeDestinatario 	= $row->NOME;
		$enderecoEmail 		= $row->ENDERECO_EMAIL;
	}elseif($rowFat->CODIGO_EMPRESA != ''){
		$query  = ' SELECT NOME_EMPRESA AS NOME, ENDERECO_EMAIL FROM PS1010 ';
		$query .= ' INNER JOIN PS1001 ON (PS1001.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA) ';
		$query .= ' WHERE PS1010.CODIGO_EMPRESA = ' . aspas($rowFat->CODIGO_EMPRESA);
		$res = jn_query($query);
		$row = jn_fetch_object($res);
		$faturaTecnica = ' <a href="http://portal.propulsaodental.com.br/AliancaAppNet2/ServidorAl2/ProcessoDinamico/relatorio_detalhamento_fatura.php?numeroRegistro=' . $_GET['numeroRegistro'] . '"> Clique aqui para abrir o detalhamento da fatura </a> <br><br> ';
	}else{
		exit;
	}
	
	$queryNF  = ' SELECT LINK_NFSE FROM PS1056 ';
	$queryNF .= ' WHERE LINK_NFSE IS NOT NULL AND NUMERO_REGISTRO_PS1020 = ' . aspas($_GET['numeroRegistro']);
	$resNF = jn_query($queryNF);
	if($rowNF = jn_fetch_object($resNF)){
		$linkNFSe = ' <a href="' . $rowNF->LINK_NFSE . '"> Clique aqui para abrir a nota fiscal </a> <br><br> ';		
	}
	
	
	
	$nomeDestinatario 	= $row->NOME;
	$enderecoEmail 		= $row->ENDERECO_EMAIL;
		
	
	$assunto = ' Boleto para pagamento';
	
	$corpoEmail  = ' Prezado(a) <b>' . $nomeDestinatario . '</b>, <br><br>';
	$corpoEmail .= ' Para sua comodidade segue boleto para Pagamento <b>Propulsão Planos Odontológicos</b>. <br><br>';
	$corpoEmail .= ' <a href="http://portal.propulsaodental.com.br/AliancaAppNet2/ServidorAl2/boletos/boleto_sicoob_Propulsao.php?numeroRegistro=' . $_GET['numeroRegistro'] . '"> Clique aqui para abrir o boleto </a> <br><br> ';
	$corpoEmail .= $faturaTecnica;
	$corpoEmail .= $linkNFSe;
	$corpoEmail .= ' Lembramos que, caso o pagamento não seja identificado no prazo de 10 dias a partir do vencimento, seus atendimentos ficarão suspensos. <br><br> ';
	$corpoEmail .= ' Qualquer duvida ou divergência contate nossa matriz (41) 3059-0080 em horário comercial. <br><br> ';
	$corpoEmail .= ' Consulte o endereço e telefone de nossas unidades próprias através do site: <a href="www.propulsaodental.com.br" >www.propulsaodental.com.br </a> <br><br><br> ';
	$corpoEmail .= ' <img src="http://portal.propulsaodental.com.br/AliancaAppNet2/Site/assets/img/logo_operadoraEmail.png" alt="logo_propulsao"> ';
	
	disparaEmail($enderecoEmail, $assunto, $corpoEmail);
}else if($_GET['email']){
	
	$assunto = 'Teste Envio Email: '.$_GET['email'];
	
	$corpoEmail  = ' Prezado(a) Teste 0104 <b>' . $nomeDestinatario . '</b>, <br><br>';
	$corpoEmail .= ' Para sua comodidade segue boleto para Pagamento <b>Propulsão Planos Odontológicos</b>. <br><br>';
	$corpoEmail .= ' <a href="http://portal.propulsaodental.com.br/AliancaAppNet2/ServidorAl2/boletos/boleto_sicoob_Propulsao.php?numeroRegistro=' . $_GET['numeroRegistro'] . '"> Clique aqui para abrir o boleto </a> <br><br> ';
	$corpoEmail .= $faturaTecnica;
	$corpoEmail .= $linkNFSe;
	$corpoEmail .= ' Lembramos que, caso o pagamento não seja identificado no prazo de 10 dias a partir do vencimento, seus atendimentos ficarão suspensos. <br><br> ';
	$corpoEmail .= ' Qualquer duvida ou divergência contate nossa matriz (41) 3059-0080 em horário comercial. <br><br> ';
	$corpoEmail .= ' Consulte o endereço e telefone de nossas unidades próprias através do site: <a href="www.propulsaodental.com.br" >www.propulsaodental.com.br </a> <br><br><br> ';
	$corpoEmail .= ' <img src="http://portal.propulsaodental.com.br/AliancaAppNet2/Site/assets/img/logo_operadoraEmail.png" alt="logo_propulsao"> ';
	
	disparaEmail($_GET['email'], $assunto, $corpoEmail);
	

}


function disparaEmail($enderecoEmail, $assunto, $corpoEmail){
	$contaEnvio 	= '';
	$senhaConta 	= '';
	
	$resEmpresa = jn_query($queryEmpresa = 'SELECT NOME_EMPRESA FROM CFGEMPRESA');
	$rowEmpresa = jn_fetch_object($resEmpresa);
	
	$queryConfig = 'SELECT IDENTIFICADOR_CONFIGURACAO, VALOR_CONFIGURACAO FROM CFG0003';
	$resConfig = jn_query($queryConfig);
	while($rowConfig = jn_fetch_object($resConfig)){
		if($rowConfig->IDENTIFICADOR_CONFIGURACAO == 'SMTP_ACCOUNT'){
			$contaEnvio = $rowConfig->VALOR_CONFIGURACAO;
		}elseif($rowConfig->IDENTIFICADOR_CONFIGURACAO == 'SMTP_PASSWORD'){
			$senhaConta = $rowConfig->VALOR_CONFIGURACAO;
		}
	}

	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->setLanguage('br');
	$mail->CharSet='UTF-8';	
	$mail->Host = 'smtplw.com.br';
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = 'tls';
	$mail->Username = $contaEnvio;
	$mail->Password = $senhaConta;
	$mail->Port = 587;
	$mail->From = 'atendimento@propulsaodental.com.br';
	$mail->FromName = jn_utf8_encode($rowEmpresa->NOME_EMPRESA);
	//$mail->SetFrom($contaEnvio, $rowEmpresa->NOME_EMPRESA);
	$mail->AddAddress($enderecoEmail, $enderecoEmail);
	$mail->isHTML(true);
	$mail->Subject = $assunto;
	$mail->Body    = $corpoEmail;

	if(!$mail->Send()) {				
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = $mail->ErrorInfo;
		
		echo json_encode($retorno);
	}else{		
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = jn_utf8_encode('E-mail enviado para ' . $enderecoEmail);

		echo json_encode($retorno);
	}	
	
}

?>