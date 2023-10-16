<?php

global $tokenRedeMais, $homolRedeMais, $idCliente, $idClienteRedeMais, $tipoPlanoRMS;

function adesao_rede_mais($codigo, $nome, $numeroCpf, $cpfTitular, $dtNascimento, $email, $telefone, $sexo, $tipoAssociado, $dadosEndereco){

	global $tokenRedeMais, $homolRedeMais, $idCliente, $idClienteRedeMais, $tipoPlanoRMS;

	$idBeneficiarioTipo = '';
	if($tipoAssociado == 'T'){
		$idBeneficiarioTipo = 1;
	}else{
		$idBeneficiarioTipo = 3;
	}


	$headers = array("Content-Type: application/json","x-api-key:" . $tokenRedeMais);

	$data = '{
				"idClienteContrato": ' . $idClienteRedeMais . ',
				"idBeneficiarioTipo": ' . $idBeneficiarioTipo . ',
				"idCliente":' . $idCliente . ',
				"nome": "' . $nome . '",
				"codigoExterno": "' . $codigo . '",
				"cpfTitular": "' . $cpfTitular . '",
				"cpf": "' . $numeroCpf . '",
				"dataNascimento": "' . $dtNascimento . '",
				"email": "' . $email . '",
				"celular": "' . $telefone . '",
				"sexo": "' . $sexo . '",				
				"logradouro": "' . $dadosEndereco['endereco'] . '",
				"numero": "' . $dadosEndereco['numero'] . '",
				"complemento": "' . $dadosEndereco['complemento'] . '",
				"cep": "' . $dadosEndereco['cep'] . '",
				"cidade": "' . $dadosEndereco['cidade'] . '",
				"bairro": "' . $dadosEndereco['bairro'] . '",
				"uf": "' . $dadosEndereco['estado'] . '",
				"tipoPlano": "' . $tipoPlanoRMS . '"
			}';
	
	$data = utf8_encode($data);
	
	if($homolRedeMais){
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/hml-v1/rms1/adesao';	
	}else{
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/prd-v1/rms1/adesao';
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
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  

	$resultado = json_decode($result, true);	
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = $resultado['mensagem'];			
	}else{
		$retorno['CODE'] = $resultado['codigoErro'];
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['mensagem'];		
	}

	return $retorno;
}


function cancelamento_rede_mais($numeroCpf){

	global $tokenRedeMais, $homolRedeMais, $idCliente, $idClienteRedeMais;

	$headers = array("Content-Type: application/json","x-api-key:" . $tokenRedeMais);

	$data = '{
				"idClienteContrato": ' . $idClienteRedeMais . ',
				"idCliente": ' . $idCliente . ',
				"cpf": "' . $numeroCpf . '"
			}';

	$data = utf8_encode($data);
	
	$url = '';
	if($homolRedeMais){
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/hml-v1/rms1/cancelamento';
	}else{
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/prd-v1/rms1/cancelamento';
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
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  

	$resultado = json_decode($result, true);	
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['MENSAGEM'] = $resultado['mensagem'];			
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}

function rede_credenciada_rede_mais($numeroCpf, $tipoSolicitacao, $callback){

	global $tokenRedeMais, $homolRedeMais, $idClienteRedeMais;

	$headers = array(
						"Content-Type: application/json",
						"x-api-key:" . $tokenRedeMais, 
						"tipoSolicitacao:1",
						"modoIframe:1", 
						"idContratoPlano:" . $idClienteRedeMais,
						"cpf:" . $numeroCpf
					);
	
	$url = '';

	if($homolRedeMais){
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/hml-v1/rms1/rede-credenciada/url';
	}else{
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/prd-v1/rms1/rede-credenciada/url';
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	  
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
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
		$retorno['URL'] = $resultado['url'];			
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}

function teletriagem_rede_mais($numeroCpf){

	global $tokenRedeMais, $homolRedeMais, $idCliente, $idClienteRedeMais;

	$headers = array("Content-Type: application/json","x-api-key:" . $tokenRedeMais);

	$data = '{
				"idClienteContrato": ' . $idClienteRedeMais . ',
				"idCliente": ' . $idCliente . ',
				"cpf": "' . $numeroCpf . '"
			}';

	$data = utf8_encode($data);
	
	$url = '';

	if($homolRedeMais){
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/hml-v1/rms1/teletriagem';
	}else{
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/prd-v1/rms1/teletriagem';
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
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  

	$resultado = json_decode($result, true);	
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['MENSAGEM'] = $resultado['mensagem'];			
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}


function inativacao_rede_mais($numeroCpf, $codigoAssociado){

	global $tokenRedeMais, $homolRedeMais, $idClienteRedeMais;

	$headers = array("Content-Type: application/json","x-api-key:" . $tokenRedeMais);

	$data = '{
				"idClienteContrato": ' . $idClienteRedeMais . ',
				"codigoExterno": "' . $codigoAssociado . '",
				"cpf": "' . $numeroCpf . '"
			}';

	$data = utf8_encode($data);
	
	$url = '';
	if($homolRedeMais){
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/hml-v1/rms1/beneficiarios/inativar';
	}else{
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/prd-v1/rms1/beneficiarios/inativar';
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);  
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
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
		$retorno['MENSAGEM'] = $resultado['mensagem'];			
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['codigoErro'] . ' -- ' . $resultado['mensagem'];		
	}

	return $retorno;
}

function ativacao_rede_mais($numeroCpf, $codigoAssociado){

	global $tokenRedeMais, $homolRedeMais, $idClienteRedeMais;

	$headers = array("Content-Type: application/json","x-api-key:" . $tokenRedeMais);

	$data = '{
				"idClienteContrato": ' . $idClienteRedeMais . ',
				"codigoExterno": "' . $codigoAssociado . '",
				"cpf": "' . $numeroCpf . '"
			}';

	$data = utf8_encode($data);
	
	$url = '';
	if($homolRedeMais){
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/hml-v1/rms1/beneficiarios/ativar';
	}else{
		$url = 'https://ddt8urmaeb.execute-api.us-east-1.amazonaws.com/prd-v1/rms1/beneficiarios/ativar';
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);  
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
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
		$retorno['MENSAGEM'] = $resultado['mensagem'];			
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['codigoErro'] . ' -- ' . $resultado['mensagem'];		
	}

	return $retorno;
}

?>