<?php

function autentica_epharma($dadosAuth, $homolEpharma){	

	$headers = Array();

	$headers['Content-Type'] = 'application/x-www-form-urlencoded';	
	
	$data  = 'grant_type=password&';
	$data .= 'client_id=' . $dadosAuth['clientId'] . '&';
	$data .= 'client_secret=' . $dadosAuth['clientSecret'] . '&';
	$data .= 'username=' . $dadosAuth['username'] . '&';
	$data .= 'password=' . $dadosAuth['password'];

	$url = 'https://rest.epharma.com.br';

	if($homolEpharma)
		$url = 'https://restqa.epharma.com.br';	

	$url .= '/oauth/token';

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
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';		
		$retorno['token'] = $resultado['access_token'];		
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		//$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}

function movimentacaoCartaoCliente($token, $homolEpharma, $codigoEpharma, $dadosCliente){

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $token);

	$data = '
			{
				"beneficiario": [
				{
					"planoCodigo":"' . $codigoEpharma . '",
					"inicioVigencia": "' . $dadosCliente['dtInicioVigencia'] . '",
					"fimVigencia": "",
					"matricula": "' . $dadosCliente['numeroCpf'] . '",
					"tipoBeneficiario": "' . $dadosCliente['tipoAssociado'] . '",
					"cartaoTitular": "' . $dadosCliente['numeroCpf'] . '",
					"cartaoUsuario": "' . $dadosCliente['numeroCpf'] . '",
					"dadosBeneficiario": {
						"nomeBeneficiario": "' . $dadosCliente['nome'] . '",
						"cpf": "' . $dadosCliente['numeroCpf'] . '",
						"dataNascimento": "' . $dadosCliente['dtNascimento'] . '",
						"sexo": "' . $dadosCliente['sexo'] . '"
					},
					"endereco": {
					  "cep": "",
					  "logradouro": "",
					  "numero": "",
					  "complemento": "",
					  "bairro": "",
					  "cidade": "",
					  "uf": ""
					},
					"telefones": {
					  "celular": "",
					  "residencial": "",
					  "comercial": ""
					}
				}]
			}';

	$data = utf8_encode($data);

	$url = 'https://rest.epharma.com.br';

	if($homolEpharma)
		$url = 'https://restqa.epharma.com.br';	

	$url .= '/api/ManutencaoBeneficiario/BeneficiariosCartaoCliente';
	
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
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['PLANO'] = $resultado['data'][0]['plano'];	
		$retorno['BENEFICIARIO'] = $resultado['data'][0]['beneficiario'];					
		$retorno['MSG'] = $resultado['data'][0]['mensagem'];	
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['message'];		
	}

	return $retorno;
}

function movimentacaoCartaoEpharma($token, $homolEpharma, $codigoEpharma, $dadosCliente){

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $token);

	$data = '
			{
				"beneficiario": [
				{
					"planoCodigo":"137526",
					"inicioVigencia": "' . $dadosCliente['$dtInicioVigencia'] . '",					
					"matricula": "' . $dadosCliente['codigoEpharma'] . '",
					"tipoBeneficiario": "' . $dadosCliente['tipoAssociado'] . '",
					"cartaoTitular": "' . $dadosCliente['codigoEpharma'] . '",
					"cartaoUsuario": "' . $dadosCliente['codigoEpharma'] . '",
					"dadosBeneficiario": {
						"nomeBeneficiario": "' . $dadosCliente['nome'] . '",
						"cpf": "' . $dadosCliente['numeroCpf'] . '",					
						"dataNascimento": "' . $dadosCliente['dtNascimento'] . '",
						"sexo": "' . $dadosCliente['sexo'] . '"
					}
				}]
			}';

	$data = utf8_encode($data);

	$url = 'https://rest.epharma.com.br';

	if($homolEpharma)
		$url = 'https://restqa.epharma.com.br';	

	$url .= '/api/ManutencaoBeneficiario/BeneficiariosCartaoePharma';	

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
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['PLANO'] = $resultado['plano'];	
		$retorno['BENEFICIARIO'] = $resultado['beneficiario'];					
		$retorno['MSG'] = $resultado['mensagem'];	
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}


?>
