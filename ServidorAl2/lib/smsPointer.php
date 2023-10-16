<?php

if($_GET['testeMensagem'] == 'OK'){
	require_once('base.php');

	$cel = $_GET['celular'];
	$mensagem = $_GET['mensagem'];	

	enviaSmsPointer($cel,$mensagem);
}


function enviaSmsPointer($telefone,$mensagem){

	$LINK_POINTER = retornaValorConfiguracao('LINK_POINTER'); 
	$API_POINTER = base64_encode(retornaValorConfiguracao('USER_POINTER') . ':' . retornaValorConfiguracao('CHAVE_POINTER'));	
	
	$headers = array("Content-Type: application/json","Authorization: Basic ".$API_POINTER,'Accept: application/json', 'Cache-Control:no-cache');
	$url = $LINK_POINTER;
	
	$data = '{	
				"to": "' . $telefone . '",
				"message": "' . $mensagem . '"	
			}';

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
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);
	$body = json_decode($result);	
	
	if($_GET['testeMensagem'] == 'OK'){
		pr($body,true);
	}
	
	if($body->status==0)
		return '';
    else
		return $body->error->message;	

	
}

?>