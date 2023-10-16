<?php
require('../lib/base.php');

$numeroRegistro = $_GET['numeroRegistro'];

if($numeroRegistro){

	$query  = ' SELECT ';
	$query .= ' 	ENDERECO_EMAIL, NOME_ASSOCIADO ';
	$query .= ' FROM PS1020 ';
	$query .= ' INNER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1020.CODIGO_ASSOCIADO) ';
	$query .= ' INNER JOIN PS1001 ON (PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ';
	$query .= ' WHERE PS1020.NUMERO_REGISTRO = ' . aspas($numeroRegistro);
	$res = jn_query($query);
	$row = jn_fetch_object($res);

	$assuntoEmail = '';
	$corpoEmail = '';
	$emailRemetente = $row->ENDERECO_EMAIL;	

	if($_GET['Teste'] == 'OK'){
		$emailRemetente = 'leonardo@javenessi.com.br';
	}	

	$queryEmpresa = ' SELECT CFGEMPRESA.CODIGO_SMART FROM CFGEMPRESA ';			
	$resEmpresa  = jn_query($queryEmpresa);
	$rowEmpresa = jn_fetch_object($resEmpresa);			
	$codigoSmart = $rowEmpresa->CODIGO_SMART;
	

	if($codigoSmart == '4022'){//Odontomais			

		$assuntoEmail = 'Fatura Odontomais - ' . $row->NOME_ASSOCIADO;
		
		$corpoEmail  = 	"<html>";
		$corpoEmail .= "   Ol&aacute;, " . $row->NOME_ASSOCIADO . "<br>";
		$corpoEmail .= 	"  A OdontoMais trouxe mais uma facilidade para voc&ecirc;: o boleto digital. Para acess&aacute;-lo, basta ";
		$corpoEmail .= 	"  <a href='https://www.operadoraodontomais.com.br/AliancaAppNet2/ServidorAl2/boletos/boleto_itau.php?numeroRegistro=" . $numeroRegistro. "'>clicar aqui. <\a> <br>";
		$corpoEmail .= "   Caso j&aacute; tenha efetuado o pagamento desconsidere este email. <br>";
		$corpoEmail .= "   Muito Obrigado! <br>";
		$corpoEmail .= "   Equipe OdontoMais";
		$corpoEmail .= 	"</html>";		
	}
	
	disparoEmailAPILocaWeb($emailRemetente, $assuntoEmail, $corpoEmail);	
}

function disparoEmailAPILocaWeb($emailAssociado, $assunto, $corpoEmail){	
				
	$token = retornaValorConfiguracao('TOKEN_API_LOCAWEB');
	$emailRemetente = retornaValorConfiguracao('ENDERECO_EMAIL_LOCAWEB');	

	$ch = curl_init();
	$headers = array("content-type:application/json", "x-auth-token:$token");
	$url = 'https://api.smtplw.com.br/v1/messages';	

	$data = '	{
					"subject": "'.$assunto.'",
					"body": "'.$corpoEmail.'",
					"from": "'.$emailRemetente.'",
					"to": "'.$emailAssociado.'",
					"headers": {
						"Content-Type": "text/plain"
					}
				}';
	
	$data = utf8_encode($data);

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);	
	$info = curl_getinfo($ch);
		
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);		
	curl_close($ch);
	$body = json_decode($body);
	print_r($body);

	
}

?>