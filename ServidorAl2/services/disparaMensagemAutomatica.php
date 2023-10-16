<?php
require('../lib/base.php');
require_once('../lib/mandrill-api-php/src/Mandrill.php');
require_once('../ProcessoDinamico/retornaImagem.php');

$tipoMensagem 	= $_GET['tpMensagem'];
$dia 			= $_GET['dia'];
$mes 			= $_GET['mes'];

if($tipoMensagem == '')
	$tipoMensagem = 'aniversario';

if($dia == '')
	$dia = date('d');

if($mes == '')
	$mes = date('m');


if($tipoMensagem == 'aniversario'){
	
	$enviado = false;
	$queryAssoc  = ' SELECT TOP 4 PS1000.CODIGO_ASSOCIADO, NOME_ASSOCIADO, COALESCE(PS1001.ENDERECO_EMAIL, PS1015.ENDERECO_EMAIL) AS ENDERECO_EMAIL  FROM PS1000 ';
	$queryAssoc .= ' LEFT OUTER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
	$queryAssoc .= ' LEFT OUTER JOIN PS1015 ON (PS1000.CODIGO_ASSOCIADO = PS1015.CODIGO_ASSOCIADO) ';
	$queryAssoc .= ' WHERE DATA_EXCLUSAO IS NULL ';
	$queryAssoc .= '	AND COALESCE(PS1001.ENDERECO_EMAIL, PS1015.ENDERECO_EMAIL) IS NOT NULL '; 
	$queryAssoc .= '	AND COALESCE(PS1001.ENDERECO_EMAIL, PS1015.ENDERECO_EMAIL) <> ' . aspas('');
	$queryAssoc .= ' 	AND DAY(DATA_NASCIMENTO) = ' . date('d');
	$queryAssoc .= ' 	AND MONTH(DATA_NASCIMENTO) = ' . date('m');
	$queryAssoc .= '	AND PS1000.CODIGO_ASSOCIADO NOT IN ( ';
	$queryAssoc .= '			SELECT CODIGO_ASSOCIADO FROM ESP_MENSAGENS_AUTOMATICAS ';
	$queryAssoc .= '			WHERE	DATA_ENVIO = CONVERT(date, GETDATE()) ';
	$queryAssoc .= '				AND TIPO_MENSAGEM = "aniversario") ';	
	$resAssoc  = jn_query($queryAssoc);
	
	while($rowAssoc  = jn_fetch_object($resAssoc)){
		$enderecoEmail = 'leonardo@javenessi.com.br';
		$linkImagem = 'https://app.plenasaude.com.br/AliancaAppNet2/Site/';
		efetuaDisparoEmail($rowAssoc->CODIGO_ASSOCIADO, $rowAssoc->NOME_ASSOCIADO, $enderecoEmail, 'Feliz Aniversário', $tipoMensagem, $linkImagem);
		$enviado = true;		
	}
	
	if($enviado)	
		header('Refresh:0');
}

function efetuaDisparoEmail($codigoAssociado, $nomeAssociado, $enderecoEmail, $assunto, $tipoMensagem, $linkImagem){

	$base64Html  = '<html lang="pt-br"> ';
	$base64Html .= '	<br><br> ';
	$base64Html .= '	<body> ';
	$base64Html .= '		<a href="'.$linkImagem.'"><img src="data:image/png;base64, ' . geraImagemExterna('imagemAniversarioPlena', $nomeAssociado, $linkImagem)  . '"  /></a> ';
	$base64Html .= '	</body> ';
	$base64Html .= '</html> ';

	$nomeOperadora = 'Plena Saúde';
	$emailOperadora = 'naoresponder@plenasaude.com.br';

	$base64 = '';
	$attachments = '';

	try {    
		$mandrill = new Mandrill('_vAKHqH9JMdb4LAuAn4opw');
		$message = array(
			'html' => $base64Html,
			'text' => $texto,
			'subject' => $assunto,
			'from_email' => $emailOperadora,
			'from_name' => $nomeOperadora,
			'to' => array(
				array(
					'email' => $enderecoEmail,
					'name' => $nomeAssociado,
					'type' => 'to'
				)
			),
			'important' => false,
			'track_opens' => null,
			'track_clicks' => null,
			'auto_text' => null,
			'auto_html' => null,
			'inline_css' => null,
			'url_strip_qs' => null,
			'preserve_recipients' => null,
			'view_content_link' => null,        
			'tracking_domain' => null,
			'signing_domain' => null,
			'return_path_domain' => null,
			'merge' => true,
			'merge_language' => 'mailchimp',        
			'attachments' => $ArrayAttachments
			
		);
		$async = false;
		$ip_pool = 'Main Pool';
		$result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);

		gravaRegistroEnvio($tipoMensagem, $codigoAssociado, $nomeAssociado, $enderecoEmail);

	} catch(Mandrill_Error $e) {    
		echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();    
		throw $e;
	}

}

function gravaRegistroEnvio($tipoMensagem, $codigoAssociado, $nomeAssociado, $enderecoEmail){	
	$insertMensagem  = ' INSERT INTO ESP_MENSAGENS_AUTOMATICAS (TIPO_MENSAGEM, CODIGO_ASSOCIADO, EMAIL_CLIENTE, DATA_ENVIO) VALUES ';
	$insertMensagem .= ' ( ' . aspas($tipoMensagem) . ', ' . aspas($codigoAssociado) . ', ' . aspas($enderecoEmail) . ', CONVERT(date, GETDATE()) ) ';
	jn_query($insertMensagem);
}

?>