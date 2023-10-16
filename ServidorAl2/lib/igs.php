<?php

header('Content-Type: text/html');

function autentica_igs($dadosAuth){

	global $tokenIGS, $homolIGS;

	$headers = Array();

	$headers[] = 'Content-Type: application/json';
	$headers[] = 'service: ' . $dadosAuth['service'];
	$headers[] = 'auth_key: ' .$dadosAuth['auth_key'];
	$headers[] = 'username: ' .$dadosAuth['username'];
	$headers[] = 'password: ' .$dadosAuth['password'];
	
	
	$url = 'http://200.212.48.98/api-apolo/v1';

	if($homolIGS)
		$url = 'http://200.212.48.98/api-hml/v1';	

	$url .= '/auth/login';		
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);  
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);

	$resultado = json_decode($result, true);	
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['user_id'] = $resultado['user_id'];
		$retorno['token'] = $resultado['token'];		
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}

function adesao_customer_igs($dadosAuth, $dadosAuthToken, $dadosCliente, $telefone, $dadosEndereco){

	global $homolIGS;

	$headers = Array();

	$headers[] = 'Content-Type: application/json';
	$headers[] = 'service: ' . $dadosAuth['service'];
	$headers[] = 'auth_key: ' .$dadosAuth['auth_key'];
	$headers[] = 'username: ' .$dadosAuth['username'];
	$headers[] = 'password: ' .$dadosAuth['password'];
	$headers[] = 'user_id: ' . $dadosAuthToken['user_id'];
	$headers[] = 'token: ' . $dadosAuthToken['token'];	

	$data = '[{
				"action": "1",
				"cnpjcpf": "' . $dadosCliente['numeroCpf'] . '",
				"nombre": "' . $dadosCliente['nome'] . '",
				"apellido": "' . $dadosCliente['apelido'] . '",
				"email": "' . $dadosEndereco['email'] . '",
				"iniciovigencia": "' . $dadosCliente['dtInicioVigencia'] . '",
				"finvigencia": "' . $dadosCliente['dtIFimVigencia'] . '",
				"telefono": "' . $telefone . '",
				"codigo": "' . $dadosEndereco['cep'] . '",
				"calle": "' . $dadosEndereco['endereco'] . '",
				"numero": "' . $dadosEndereco['numero'] . '",
				"complemento": "' . $dadosEndereco['complemento'] . '",
				"barrio": "' . $dadosEndereco['bairro'] . '",
				"ciudad": "' . $dadosEndereco['cidade'] . '",
				"provincia": "' . $dadosEndereco['estado'] . '",
				"producto": "' . $dadosCliente['produto'] . '",
				"fechanascimiento": "' . $dadosCliente['dtNascimento'] . '"
			}]';

	$data = utf8_encode($data);
	$url = 'http://200.212.48.98/api-apolo/v1';

	if($homolIGS)
		$url = 'http://200.212.48.98/api-hml/v1';	

	$url .= '/customers';	

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
	
	$result = curl_exec($ch);	
	
	curl_close($ch);  	

	$ArrResult = explode('[', $result);
	$ArrResult = explode(']', $ArrResult[1]);
	$result = $ArrResult[0];	

	$resultado = json_decode($result, true);	
		
	$retorno = Array();
	if($resultado['status']==201){
		$retorno['CODE'] = $resultado['status'];
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = $resultado['message'];					
	}else{
		$retorno['CODE'] = $resultado['status'];
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['message'];		
	}

	return $retorno;
}

function cancelamento_customer_igs($dadosAuthToken, $dadosCliente){

	$headers['Content-Type'] = 'application/json';
	$headers['service'] 	= $dadosAuthToken['service'];
	$headers['auth_key'] 	= $dadosAuthToken['auth_key'];
	$headers['user_id'] 	= $dadosAuthToken['user_id'];
	$headers['token'] 		= $dadosAuthToken['token'];

	$data = '{
				"action": "3",
				"cnpjcpf": "' . $dadosCliente['numeroCpf'] . '",				
				"producto": "' . $dadosCliente['produto'] . '"
			}';

	$data = utf8_encode($data);
	$url = 'http://200.212.48.98/api-apolo/v1';

	if($homolIGS)
		$url = 'http://200.212.48.98/api-hml/v1';	

	$url .= '/customers';

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
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  

	$resultado = json_decode($result, true);	
	
	$retorno = Array();
	if($returnCode==201){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = $resultado['message'];					
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}


?>
