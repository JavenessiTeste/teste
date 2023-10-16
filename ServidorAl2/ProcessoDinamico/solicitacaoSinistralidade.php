<?php 

require('../lib/base.php');
require('../private/autentica.php');
require_once('../lib/mandrill-api-php/src/Mandrill.php');


$retorno['MSG']  = 'Solicitação Enviada.';

$sqlInsert 	.= linhaJsonEdicao('CODIGO_EMPRESA',$_SESSION['codigoIdentificacao']);
$sqlInsert 	.= linhaJsonEdicao('DATA_SOLICITACAO',dataToSql(date('d/m/Y')));
$sqlInsert 	.= linhaJsonEdicao('OBSERVACAO',$_POST['OBS']);
$sqlInsert 	.= linhaJsonEdicao('STATUS','AGUARDANDO');
gravaEdicao('ESP_SOLIC_SINISTRALIDADE', $sqlInsert, 'I', '');

$retorno['DESTINO']  = 'site/gridDinamico';
$retorno['DADOS_DESTINO']['tabela'] ='ESP_SOLIC_SINISTRALIDADE';

$html .= "A empresa ". $_SESSION['codigoIdentificacao'] ." - ".  $_SESSION['nomeUsuario'] . ' solicitou o relaório de sinistralida <br><br>';
$html .= "A observação inserida foi:  ". $_POST['OBS'];

$envio = efetuaDisparoEmail( "Operador", 'tania.infante@plenasaude.com.br', 'Solicitação de relatório de Sinistralidade', $html);


echo json_encode($retorno);


function efetuaDisparoEmail($nomeAssociado, $enderecoEmail, $assunto, $html){

	$base64Html  = '<html lang="pt-br"> ';
	$base64Html .= '	<br><br> ';
	$base64Html .= '	<body> ';
	$base64Html .= $html;
	$base64Html .= '	</body> ';
	$base64Html .= '</html> ';

	$nomeOperadora = 'Plena Saúde';
	$emailOperadora = 'naoresponder@plenasaude.com.br';

	$base64 = '';
	$attachments = '';
  $retorno = true;
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

	} catch(Mandrill_Error $e) {    
    $retorno = false;
	}
  return $retorno;
}
?>