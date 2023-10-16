<?php
header('Content-Type: text/html');

function gera_token_doc24($clientId, $clientSecret){	

	global $homolDoc24;	

	$data = '	{
					"client_id": "' . $clientId . '",
					"client_secret": "' . $clientSecret . '"	
				}';

	$headers = array("Content-Type: application/json");

	$url = '';
	if($homolDoc24 == 'NAO'){
		$url = 'https://api.doc24.com.ar/ws/api/v2/authentication';
	}else{
		$url = 'https://tapi.doc24.com.ar/ws/api/v2/authentication';
	}	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);  
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);

	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  

	$resultado = json_decode($result, true);

	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['TOKEN'] = $resultado['token'];
	}elseif($returnCode==401){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}


	return $retorno;
}

function cria_sessao_usuario_doc24($nomeCliente, $documento, $email, $clientId, $clientSecret){	

	global $tokenDoc24, $homolDoc24;	

	if(!isset($tokenDoc24)){		
		$returnToken = gera_token_doc24($clientId, $clientSecret);		
		$tokenDoc24 = $returnToken['TOKEN'];
	}

	$urlDoc24 = '';
	if($homolDoc24 == 'NAO'){
		$urlDoc24 = 'https://api.doc24.com.ar/ws/api/v2/sesion';
	}else{
		$urlDoc24 = 'https://tapi.doc24.com.ar/ws/api/v2/sesion';
	}

	$data = '	{
					"id_tipo_de_identificacion": "9",
					"valor_identificacion": "' . $documento . '",
					"nombre": "' . $nomeCliente . '",
					"apellido": "' . $nomeCliente . '",
					"genero": "",
					"fecha_de_nacimiento": "1990-01-01",					
					"email": "' . $email . '",
					"telefono":"",
					"credencial": "",
					"plan": ""			
				}';

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenDoc24);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urlDoc24);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);  
	
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);  
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);

	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);	

	$resultado = json_decode($result, true);
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['ID_SESSION'] = $resultado['data']['id_sesion'];			
		$retorno['LINK'] = $resultado['data']['deeplink'];
		$retorno['HORARIO'] = $resultado['data']['timestamp'];	
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['mensaje'];		
	}

	return $retorno;
}

?>