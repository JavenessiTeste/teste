<?php

global $tokenZsPay;

function cria_cliente_zspay($nome, $documento, $dtNascimento, $email, $telefone, $sexo, $dadosEndereco, $retornarMensagemErro = false){

	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$data = '{
				"nome": "' . $nome . '",
				"documento": "' . $documento . '",
				"dataNascimento": "' . $dtNascimento . '",
				"email": "' . $email . '",
				"celular": "' . $telefone . '",
				"sexo": "' . $sexo . '",
				"endereco": {
					"logradouro": "' . $dadosEndereco['endereco'] . '",
					"numero": "' . $dadosEndereco['numero'] . '",
					"complemento": "' . $dadosEndereco['complemento'] . '",
					"cep": "' . $dadosEndereco['cep'] . '",
					"cidade": "' . $dadosEndereco['cidade'] . '",
					"estado": "' . $dadosEndereco['estado'] . '"
				}
			}';

	$data = utf8_encode($data);
	$url = 'https://api.zsystems.com.br/clientes';	

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
	
	if($retornarMensagemErro){
		print_r($resultado);
	}
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['cliente']['id'];			
	}elseif($returnCode==202){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];
		$retorno['ID'] = $resultado['cliente']['id'];	
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}

function vincula_cartao_zspay($idCliente, $numCartao, $nomeCartao, $validade, $cvv){

	global $tokenZsPay;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$data = '{
				"numero": "'. $numCartao .'",
				"titular": "'. $nomeCartao .'",
				"codigoSeguranca": "'. $cvv .'",
				"validade": "'. $validade .'"
			}';

	$data = utf8_encode($data);
	$url = 'https://api.zsystems.com.br/clientes/' . $idCliente . '/cartoes';
		
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
	
	$mensagem = explode(' - ', $resultado['message']);
	$codErroMensagem = $mensagem[0];
	$mensagemErro = '';
	if(isset($mensagem[1]))
		$mensagemErro = json_decode($mensagem[1]);
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['cartaoId']['id'];
		$retorno['DIG_FINAIS'] = $resultado['cartaoId']['ultimos_digitos'];		
	}else{

		$name  = '../../ServidorCliente/RetornoZsPay/retornoErroZsPay.log';
		$text  = 'Cliente : '. $idCliente ."\n";
		$text .= 'RetornoErro : '.$result."\n";		
		$file  = fopen($name, 'a');
		fwrite($file, $text,strlen($text));
		fclose($file);

		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $mensagemErro->error->message;		
	}

	return $retorno;
}

function cria_plano_zspay($valorPlano, $enderecoEmail, $frequency = 'monthly', $valorAdesao = 0){
	
	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$valorPlanoFat = $valorPlano * 100;
	$data = '{
				"name": "Plano Mensal ' . $valorPlano . '",
				"description": "Plano mensal no valor ' . $valorPlano . ' ",
				"email": "' . $enderecoEmail . '",				
				"amount": ' . $valorPlanoFat . ',
				"grace_period": "0",
				"tolerance_period": 0,
				"frequency": "' . $frequency . '",
				"interval": 1,
				"logo": false,
				"currency": "BRL",
				"payment_method": "credit",
				"plan_expiration_date": "2099-12-01T01:00:00.000Z",
				"has_expiration": false,
				"expire_subscriptions": false,
				"subscription_duration": null
			}';

	$data = utf8_encode($data);
	$url = 'https://api.zsystems.com.br/planos';

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
		$retorno['STATUS'] = 'OK';
		$retorno['planoId'] = $resultado['plano'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['erros'];
		return $retorno;
	}
}


function cria_assinatura_zspay($idPlano, $clienteId, $tokenCardId){

	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$data = '{
				"planoId": ' . $idPlano . ',
				"clienteId": "' . $clienteId . '",
				"tokenCardId": "' . $tokenCardId . '"
			}';

	$data = utf8_encode($data);
	$url = 'https://api.zsystems.com.br/planos/assinar';

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

	$mensagem = explode(' - ', $resultado['message']);
	
	$codErroMensagem = $mensagem[0];
	$mensagemErro = '';
	if(isset($mensagem[1]))
		$mensagemErro = json_decode($mensagem[1]);
	
	$retorno = Array();
	if($returnCode==200 and isset($mensagem[1]) == false){
		$retorno['STATUS'] = 'OK';
		$retorno['assinaturaId'] = $resultado['data']['id'];			
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $mensagemErro->error->message;		
	}

	return $retorno;

}

function cria_venda_cartao_credito($idCliente, $idCartao, $valor, $parcelas){

	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$data = '{
				"clienteId": ' . $idCliente . ',
				"cartaoId": ' . $idCartao . ',
				"tipoPagamentoId": 3,				
				"valor": ' . $valor . ',
				"parcelas": ' . $parcelas . ',
				"ip": "' . $_SERVER['REMOTE_ADDR'] . '"
			}';

	$data = utf8_encode($data);
	$url = 'https://api.zsystems.com.br/vendas';

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
	if($resultado['success']){
		$retorno['STATUS'] = 'OK';
		$retorno['idVenda'] = $resultado['pedido']['id'];		
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error']['message'];
	}

	return $retorno;

}

function cria_venda_boleto($idCliente, $vencimento, $valor, $descricao){	

	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$data = '{
				"clienteId": ' . $idCliente . ',				
				"tipoPagamentoId": 1,				
				"valor": ' . $valor . ',
				"dataVencimento": "' . $vencimento . '",
				"descricao": "'. $descricao . '"
			}';	

	$data = utf8_encode($data);
	$url = 'https://api.zsystems.com.br/vendas';

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
	if($resultado['success']){
		$retorno['STATUS'] = 'OK';
		$retorno['idVenda'] = $resultado['pedido']['id'];
		$retorno['urlBoleto'] = $resultado['pedido']['urlBoleto'];
	}else{		
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;	

}

function cria_webhook_zspay($url){
	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$data = '{
				"url": "' . $url . '"
			}';

	$url = 'https://api.zsystems.com.br/estabelecimentos/url-webhook';	

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
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		if(isset($resultado['error']))
			$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}


function listar_webhook_zspay(){
	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$url = 'https://api.zsystems.com.br/estabelecimentos/url-webhook';	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  
	
	$resultado = json_decode($result, true);	
	print_r($resultado);
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';		
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		if(isset($resultado['error']))
			$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}


function remover_webhook_zspay($idWebhook){
	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);

	$url = 'https://api.zsystems.com.br/estabelecimentos/url-webhook/'.$idWebhook;	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  
	
	$resultado = json_decode($result, true);	
	print_r($resultado);
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';		
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		if(isset($resultado['error']))
			$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}


function excluir_cliente_zspay($idCliente){
	global $tokenZsPay;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenZsPay);
	
	$url = 'https://api.zsystems.com.br/clientes/' . $idCliente . '/excluir';	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$result = curl_exec($ch);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  
	
	$resultado = json_decode($result, true);	
	
	$retorno = Array();
	if($returnCode==200){
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'OK';		
	}else{
		$retorno['CODE'] = $returnCode;
		$retorno['STATUS'] = 'ERRO';
		if(isset($resultado['error']))
			$retorno['ERROS'] = $resultado['error'];		
	}

	return $retorno;
}
?>
