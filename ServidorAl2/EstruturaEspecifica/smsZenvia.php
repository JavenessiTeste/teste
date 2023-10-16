<?php
require_once('../lib/base.php');
//require_once('../private/autentica.php');

global $API_NETPACS,$LINK_NETPACS;

$API_ZENVIA = retornaValorConfiguracao('CHAVE_ZENVIA');

$LINK_ZENVIA = retornaValorConfiguracao('LINK_ZENVIA'); 

echo enviaSmsZenvia('5541998061407','teste','AG3');

function enviaSmsZenvia($telefone,$mensagem,$id){
	global $API_ZENVIA,$LINK_ZENVIA;
	
	if($LINK_ZENVIA=='https://api.zenvia.com/v2/channels/sms/messages'){
		$headers = array("Content-Type: application/json","Authorization: Basic ".$API_ZENVIA,'Accept: application/json');
		$url = $LINK_ZENVIA;
		
				$data = ' {
							"from": "'.$telefone.'",
							"to": "'.$telefone.'",
							"contents": [
							{
							"type": "text",
							"text": "'.$mensagem.'"
							}
							]
							}';
		
		
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return 'OK';
		}

	}else{

		$headers = array("Content-Type: application/json","Authorization: Basic ".$API_ZENVIA,'Accept: application/json');
		$url = $LINK_ZENVIA;
		
		$data = ' {
					"sendSmsRequest": {
							"from": "'. retornaValorConfiguracao('NOME_EMPRESA_ZENVIA').'",
							"to": "'.$telefone.'",
							"schedule": "",
							"msg": "'.$mensagem.'",
							"callbackOption": "NONE",
							"id": "'.$id.'",
							"aggregateId": "",
							"flashSms": false
					}
			}';
		
		//echo($url);
		//exit; 

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		if($result === false)
		{
			//echo "Erro : " . curl_error($ch);
			//exit;
		}

		curl_close($ch);
	//print_r($result);
	//echo '<--------------';
		$body = json_decode($result);
		
		$resultados = array();
			
		//print_r($body);

		if($body->sendSmsResponse->statusCode=='00')
			return '';
		else
			return $body->sendSmsResponse->detailDescription;	
	
	}

}






?>