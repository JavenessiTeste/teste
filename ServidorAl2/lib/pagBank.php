<?php

$tokenPagBank = '34987806-042b-4b76-acd3-d03b73da8c6f6614449f4a83a6bb6719323d31175b19f2c2-a484-4754-bdf2-fa427ad86d01';

global $tokenPagBank;

function cria_conta_pagbank($dadosEndereco,$categoria,$client_id,$client_secret){

	global $tokenPagBank;
	
	$headers = array("Content-Type: application/json","Authorization: Bearer $tokenPagBank", "x-client-id: $client_id", "x-client-secret: $client_secret");
	
	$data = '{
				"type": "SELLER",
				"business_category": "'.$categoria.'",
				"email": "' . $dadosEndereco['email'] . '",
				"person": {
					"birth_date": "' . $dadosEndereco['dtNascimento'] . '",
					"name": "'.$dadosEndereco['nome'].'",
					"tax_id": "'.$dadosEndereco['cpf'].'",
					"mother_name": "'.$dadosEndereco['nomeMae'].'",
					"address": {
						"region_code": "' . $dadosEndereco['estado'] . '",
						"city": "' . $dadosEndereco['cidade'] . '",
						"postal_code": "' . $dadosEndereco['cep'] . '",
						"street": "' . $dadosEndereco['endereco'] . '",
						"number": "' . $dadosEndereco['numero'] . '",
						"complement": "' . $dadosEndereco['complemento'] . '",
						"locality": "'.$dadosEndereco['bairro'].'",
						"country": "BRA"
					},
				"phones": [
					{
						"area": "' . $dadosEndereco['area'] . '",
						"country" : "55",
						"number": "'.$dadosEndereco['telefone'].'"
					}
				]	
			},
				"tos_acceptance": {
					"user_ip": "127.0.0.1",
					"date": "2023-09-21T20:07:07.002-02"
			  }
			}';
	
	$data = utf8_encode($data);
	
	$url = 'https://api.pagseguro.com/accounts';	
	
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

	if($returnCode==200 ){

		$retorno['CODE'] 	= $returnCode;
		$retorno['STATUS']  = 'OK';
		$retorno['ID'] 		= $resultado['id'];			

	}elseif($returnCode==201){

		$retorno['CODE'] 	= $returnCode;
		$retorno['STATUS']  = 'SUCESSO';
		$retorno['ID'] 		= $resultado['id'];	

	}else{

		$retorno['CODE'] 	= $returnCode;
		$retorno['STATUS']  = 'ERRO';
		$retorno['ERROS'] 	= $resultado['error_messages'][0]['errors'];	

	}

	return $retorno;
	
}

function cria_aplicacao_pagbank($nome){
	global $tokenPagBank ;

	$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer " . $tokenPagBank);
	
	$data = '{"name": "'. $nome .'"}';
	
	$data = utf8_encode($data);
	$url  = 'https://api.pagseguro.com/oauth2/application';

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
		if($returnCode==201){
			$retorno['STATUS'] = 'OK';
		}else{
			$retorno['STATUS'] = 'ERRO';
			
		}

		return $resultado;

}

function cria_pedido($idReferencia,$nome, $email, $cpf, $valor, $area, $numero,$nomeItem ){

	global $tokenPagBank;
	
	$headers = array("Content-Type: application/json","Authorization: Bearer ". $tokenPagBank);

	$data = '{
		"reference_id": "'.$idReferencia.'",
		"customer": {
			"name": "'.$nome.'",
			"email": "'.$email.'",
			"tax_id": "'.$cpf.'",
			"phones": [
				{
					"country": "55",
					"area": "'.$area.'",
					"number": "'.$numero.'",
					"type": "MOBILE"
				}
			]
		},
		"items": [
			{
				"reference_id": "referencia do item",
				"name": "'.$nomeItem.'",
				"quantity": 1,
				"unit_amount": '.$valor.'
			}
		],
		"qr_codes": [
			{
				"amount": {
					"value": '.$valor.'
				}
			}
		]
	}';
	$data = utf8_encode($data);
	
	$url = 'https://api.pagseguro.com/orders';
	
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

	return $resultado;

	$retorno = Array();
	if($returnCode==201){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = 'SUCESSO';
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['error_messages'][0]['errors'];		
	}

	return $retorno;
}

function token_cartao(){
	global $tokenPagBank;
	
	$headers = array("Content-Type: application/json","Authorization: Bearer ". $tokenPagBank);
	
	$data = '{"type":"card"}';

	$data = utf8_encode($data);
	
	$url = 'https://api.pagseguro.com/public-keys';
	
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

	return $resultado;

}

function retorna_token_cartao($publicKey,$numero,$mesExp,$anoExp,$cvv,$nomeCartao){
	
	$headers = array("Content-Type: application/json");

	$data = '{"public_key" : "'.$publicKey.'",
			"holder" : "'.$nomeCartao.'",
			"number" : "'.$numero.'",
			"expMonth" : "'.$mesExp.'",
			"expYear" : "'.$anoExp.'",
			"securityCode" : "'.$cvv.'"
	}';
	
	$url = 'http://aliancanet2/ServidorAl2/EstruturaEspecifica/encrypted.php';
		
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

	return $result;
}

function pagando_pedido($refCobranca, $descricao, $valor, $parcelas ,$cartao, $mesExp, $anoExp, $cvv, $nome, $orderId){
	global $tokenPagBank;
	
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"charges": [
			{
				"reference_id": "'.$refCobranca.'",
				"description": "'.$descricao.'",
				"amount": {
					"value": '.$valor.',
					"currency": "BRL"
				},
				"payment_method": {
					"type": "CREDIT_CARD",
					"installments": '.$parcelas.',
					"card": {
						"number": "'.$cartao.'",
						"exp_month": "'.$mesExp.'",
						"exp_year": "'.$anoExp.'",
						"security_code": "'.$cvv.'",
						"holder": {
							"name": "'.$nome.'"
						},
						"store": false
					}
				}';
		$data .= ']
	}';
	
	$data = utf8_encode($data);
	$url = "https://asandbox.api.pagseguro.com/orders/$orderId/pay";

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

function cria_cobranca_boleto($refBoleto, $descricao, $valor, $dataValidade,$nome,$cpf,$email,$rua,$numeroRua,$cidade,$regiao,$estado,$cep){

	global $tokenPagBank;
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);
	

	$data = '{
		"reference_id": "'.$refBoleto.'",
		"description": "'.$descricao.'",
		"amount": {
		  "value": '.$valor.',
		  "currency": "BRL"
		},
		"payment_method": {
		  "type": "BOLETO",
		  "boleto": {
			"due_date": "'.$dataValidade.'",
			"instruction_lines": {
			  "line_1": "Pagamento processado para DESC Fatura",
			  "line_2": "Via PagSeguro"
			},
			"holder": {
			  "name": "'.$nome.'",
			  "tax_id": "'.$cpf.'",
			  "email": "'.$email.'",
			  "address": {
				"street": "'.$rua.'",
				"number": "'.$numeroRua.'",
				"locality": "Boa vista",
				"city": "'.$cidade.'",
				"region": "'.$regiao.'",
				"region_code": "'.$estado.'",
				"country": "Brasil",
				"postal_code": "'.$cep.'"
			  }
			}
		  }
		},
		"notification_urls": [
		  "https://yourserver.com/nas_ecommerce/277be731-3b7c-4dac-8c4e-4c3f4a1fdc46/"
		]
	  }';
	
	$data = utf8_encode($data);
	
	$url = 'https://api.pagseguro.com/charges';
	
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
	if($returnCode==201){
		$retorno['STATUS'] = 'OK';
		$retorno['HREF'] = $resultado['links']['0']['href'];			
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'Erro ao criar boleto';
	}

	return $retorno;
	

}

function cria_venda_cartao_credito($idReferencia,$descricao,$valor,$parcelas,$nomeLoja,$cartao,$mesExp,$anoExp,$cvv,$nome){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);
	
	$data = '{
		"reference_id": "'.$idReferencia.'",
		"customer": {
			"name": "'.$nome.'",
			"email": "'.$email.'",
			"tax_id": "'.$cpf.'",
			"phones": [
				{
					"country": "55",
					"area": "'.$area.'",
					"number": "'.$telefone.'",
					"type": "MOBILE"
				}
			]
		},
		"items": [
			{
				"reference_id": "Plano",
				"name": "nome do item",
				"quantity": 1,
				"unit_amount": '.$valor.'
			}
		],
		"charges": [
			{
				"reference_id": "referencia da cobranca",
				"description": "descricao da cobranca",
				"amount": {
					"value": '.$valor.',
					"currency": "BRL"
				},
				"payment_method": {
					"type": "CREDIT_CARD",
					"installments": 1,
					"capture": true,
					"card": {
						"encrypted": "4111111111111111",
						"exp_month": "12",
						"exp_year": "2026",
						"security_code": "123",
						"holder": {
							"name": "Jose da Silva"
						},
						"store": false
					}
				},
				"notification_urls": [
					"https://meusite.com/notificacoes"
				]
			}
		]
	}';

	  $data = utf8_encode($data);
	  return $data;
	  $url = 'https://api.pagseguro.com/charges';
	  
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
	  if($returnCode == 201){
		  $retorno['STATUS'] = 'OK';
		  $retorno['ID'] = $resultado['id'];

	  }else{
		  $retorno['STATUS'] = 'ERRO';
		  $retorno['ERROS'] = $mensagemErro->error->message;		
	  }

	  return $retorno;
	  
}

function cria_cobranca_pix_pagbank($idReferencia,$valor,$nome,$email,$cpf,$area,$telefone){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);
	
	$data = '{
		"reference_id": "'.$idReferencia.'",
		"customer": {
			"name": "'.$nome.'",
			"email": "'.$email.'",
			"tax_id": "'.$cpf.'",
			"phones": [
				{
					"country": "55",
					"area": "'.$area.'",
					"number": "'.$telefone.'",
					"type": "MOBILE"
				}
			]
		},
		"items": [
			{
				"name": "nome do item",
				"quantity": 1,
				"unit_amount": '.$valor.'
			}
		],
		"qr_codes": [
			{
				"amount": {
					"value": '.$valor.'
				}
			}
		],
		"notification_urls": [
			"https://meusite.com/notificacoes"
		]
	}';

	  $data = utf8_encode($data);
	
	  $url = 'https://api.pagseguro.com/orders';
	  
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
	  if($returnCode == 201){
		  $retorno['STATUS'] = 'OK';
		  $retorno['ID'] = $resultado['id'];
		  $retorno['QRCODE_PNG'] = $resultado['qr_codes'][0]['links'][0]['href'];
		  $retorno['COPIA_COLA'] = $resultado['qr_codes'][0]['text'];

	  }else{
		  $retorno['STATUS'] = 'ERRO';	
	  }

	  return $retorno;
	  
}

function cancela_transacao($idReferencia,$valor){
	
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"amount": {
		  "value": '.$valor.'
		}
	  }';

	  $data = utf8_encode($data);
	
	  $url = 'https://api.pagseguro.com/charges/'.$idReferencia.'/cancel';
	  
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
	  return $resultado;
	  
	  $mensagemErro = '';
	  if(isset($mensagem[1]))
		  $mensagemErro = json_decode($mensagem[1]);
	  
	  $retorno = Array();
	  if($returnCode==200 and isset($mensagem[1]) == false){
		  $retorno['STATUS'] = 'OK';			
	  }else{
		  $retorno['STATUS'] = 'ERRO';
	  }
  
}

function criar_plano($idCliente, $valor,$intervalo,$pagamento,$nome,$descricao = 'Plano Mensal'){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"amount": {
		  "currency": "BRL",
		  "value": '.$valor.'
		},
		"interval": {
		  "unit": "'.$intervalo.'",
		  "length": 1
		},
		"trial": {
		  "enabled": false,
		  "hold_setup_fee": false
		},
		"payment_method": [
		  "'.$pagamento.'"
		],
		"reference_id": "'.$idCliente.'",
		"name": "'.$nome.'",
		"description": "'.$descricao.'",
		"billing_cycles": 1
	  }';
	  
	
	  $data = utf8_encode($data);
	  $url = 'https://sandbox.api.assinaturas.pagseguro.com/plans';
	  
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
	  if($returnCode==201){
		  $retorno['STATUS'] = 'OK';
		  $retorno['id']	 = $resultado['id'];

	  }else{
		  $retorno['STATUS'] = 'ERRO';
	  }
	  return $retorno;
}

function inativa_plano($idPlano,$situacao,$valor,$intervalo,$nome){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '
	{
	  "amount": {
		"currency": "BRL",
		"value": '.$valor.'
	  },
	  "interval": {
		"unit": "'.$intervalo.'",
		"length": 1
	  },
	  "reference_id": "11",
	  "status": "'.$situacao.'",
	  "name": "'.$nome.'",
	  "billing_cycles": 1,
	  "limit_subscription": 3,
	  "payment_methods": [
		"BOLETO"
	  ]
	}';

	$url = 'https://sandbox.api.assinaturas.pagseguro.com/plans/'.$idPlano;

}

function cria_cliente_pagbank($nome,$email,$cpf,$nascimento,$area,$complemento,$numero,$rua,$numeroTel,$bairro,$cidade,$estado,$cep,$idCliente){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"address": {
		  "street": "'.$rua.'",
		  "number": "'.$numero.'",
		  "complement": "'.$complemento.'",
		  "locality": "'.$bairro.'",
		  "city": "'.$cidade.'",
		  "region_code": "'.$estado.'",
		  "country": "BRA",
		  "postal_code": "'.$cep.'"
		},
		"name": "'.$nome.'",
		"email": "'.$email.'",
		"reference_id": "'.$idCliente.'",
		"tax_id": "'.$cpf.'",
		"phones": [
		  {
			"country": "55",
			"area": "'.$area.'",
			"number": "'.$numeroTel.'"
		  }
		],
		"birth_date": "'.$nascimento.'"
	  }';

	  $data = utf8_encode($data);
	//   return $data;
	  $url = 'https://sandbox.api.assinaturas.pagseguro.com/customers';
	  
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
	  
	  
	  $mensagemErro = '';
	  if(isset($mensagem[1]))
		  $mensagemErro = json_decode($mensagem[1]);
	  
	  $retorno = Array();
	  if($returnCode == 200 and isset($mensagem[1]) == false){
		  $retorno['STATUS'] = 'OK';			
	  }else{
		  $retorno['STATUS'] = 'ERRO';
	  }
}

function altera_cliente($idCliente,$nome,$email,$cpf,$nascimento,$area,$numero){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"name": "'.$nome.'",
		"email": "'.$email.'",
		"tax_id": "'.$cpf.'",
		"birth_date": "'.$nascimento.'",
		"phones": [
			{
			  "country": "55",
			  "area": "'.$area.'",
			  "number": "'.$numero.'"
			}
		  ]
	  }';

	  $data = utf8_encode($data);
	//   return $data;
	  $url = 'https://sandbox.api.assinaturas.pagseguro.com/customers/'.$idCliente.'/billing_info';
	  
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
	  return $resultado;
	  
	  $retorno = Array();
	  if($returnCode==200 and isset($mensagem[1]) == false){
		  $retorno['STATUS'] = 'OK';			
	  }else{
		  $retorno['STATUS'] = 'ERRO';
	  }
}

function cria_assinatura($idCliente,$idPlano,$valor,$idReferencia,$ccv){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"plan":{
			"id":"'.$idPlano.'"
		},
		"customer":{
			"id":"'.$idCliente.'"
		},
		"amount":{
			"currency":"BRL",
			"value":'.$valor.'
		},
		"reference_id":"'.$idReferencia.'",
		"payment_method":[
			{
				"type":"CREDIT_CARD",
				"card": {
					"security_code": "'.$ccv.'"
				}
			}
		]
	}';

	$data = utf8_encode($data);
	
	$url = "https://api.assinaturas.pagseguro.com/subscriptions";

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
	if($returnCode == 201 ){
		$retorno['STATUS'] = 'OK';		
		$retorno['id']	   = $resultado['id'];
	}else{
		$retorno['STATUS'] = 'ERRO';
	}

	return $resultado;

}

function cancelar_assinatura($idAssinatura){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"name": "matheus ghotme",
		"email": "grafit933@gmail.com",
		"tax_id": "09460447910",
		"birth_date": "1996-05-05",
		"phones": [
			{
			  "country": "55",
			  "area": "41",
			  "number": "996357037"
			}
		  ]
	  }';

	  $data = utf8_encode($data);
	//   return $data;
	  $url = 'https://api.assinaturas.pagseguro.com/subscriptions/'.$idAssinatura.'/cancel';
	  
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
	  return $resultado;
	  
	  $mensagemErro = '';
	  if(isset($mensagem[1]))
		  $mensagemErro = json_decode($mensagem[1]);
	  
	  $retorno = Array();
	  if($returnCode==200 and isset($mensagem[1]) == false){
		  $retorno['STATUS'] = 'OK';			
	  }else{
		  $retorno['STATUS'] = 'ERRO';
	  }
}

function suspender_assinatura($idAssinatura){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"name": "matheus ghotme",
		"email": "grafit933@gmail.com",
		"tax_id": "09460447910",
		"birth_date": "1996-05-05",
		"phones": [
			{
			  "country": "55",
			  "area": "41",
			  "number": "996357037"
			}
		  ]
	  }';

	  $data = utf8_encode($data);
	//   return $data;
	  $url = 'https://api.assinaturas.pagseguro.com/subscriptions/'.$idAssinatura.'/suspend';
	  
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
  
	  $errors = curl_error($ch);                                                                                                            
	  $result = curl_exec($ch);	
	  
	  $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  
	  curl_close($ch);  
	  
	  $resultado = json_decode($result, true);
	  return $resultado;
	  
	  $mensagemErro = '';
	  if(isset($mensagem[1]))
		  $mensagemErro = json_decode($mensagem[1]);
	  
	  $retorno = Array();
	  if($returnCode==200 and isset($mensagem[1]) == false){
		  $retorno['STATUS'] = 'OK';			
	  }else{
		  $retorno['STATUS'] = 'ERRO';
	  }
}

function ativar_assinatura($idAssinatura){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"name": "matheus ghotme",
		"email": "grafit933@gmail.com",
		"tax_id": "09460447910",
		"birth_date": "1996-05-05",
		"phones": [
			{
			  "country": "55",
			  "area": "41",
			  "number": "996357037"
			}
		  ]
	  }';

	  $data = utf8_encode($data);
	//   return $data;
	  $url = 'https://api.assinaturas.pagseguro.com/subscriptions/'.$idAssinatura.'/activate';
	  
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
  
	  $errors = curl_error($ch);                                                                                                            
	  $result = curl_exec($ch);	
	  
	  $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  
	  curl_close($ch);  
	  
	  $resultado = json_decode($result, true);
	  
	  
	  
	  $retorno = Array();
	  if($returnCode==200 and isset($mensagem[1]) == false){
		  $retorno['STATUS'] = 'OK';			
	  }else{
		  $retorno['STATUS'] = 'ERRO';
	  }
	  return $retorno;
}

function criar_assinante($dadosEndereco,$idRefencia,$encrypted,$cvv,$nomeCartao){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '{
		"address": {
		  "street": "'.$dadosEndereco['endereco'].'",
		  "number": "'.$dadosEndereco['numero'].'",
		  "complement": "'.$dadosEndereco['complemento'].'",
		  "locality": "'.$dadosEndereco['bairro'].'",
		  "city": "'.$dadosEndereco['cidade'].'",
		  "region_code": "'.$dadosEndereco['estado'].'",
		  "country": "BRA",
		  "postal_code": "'.$dadosEndereco['cep'].'"
		},
		"name": "'.$dadosEndereco['nome'].'",
		"email": "'.$dadosEndereco['email'].'",
		"reference_id": "'.$idRefencia.'",
		"tax_id": "'.$dadosEndereco['cpf'].'",
		"phones": [
		  {
			"country": "55",
			"area": "'.$dadosEndereco['area'].'",
			"number": "'.$dadosEndereco['telefone'].'"
		  }
		],
		"birth_date": "'.$dadosEndereco['dtNascimento'].'",
		"billing_info": [
			{
				"type": "CREDIT_CARD",
				"card": {
					"encrypted": "'.$encrypted.'",
					"security_code": "'.$cvv.'",					
					"holder": {
						"name": "'.$nomeCartao.'"
					}
				}
			}
		]
	  }';
	  
	  $data = utf8_encode($data);
	  
	  $url = 'https://api.assinaturas.pagseguro.com/customers';
	  
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
	  if($returnCode ==201 ) {
		  $retorno['STATUS'] = 'OK';	
		  $retorno['ID_TOKEN'] = $resultado['billing_info'][0]['card']['token'];
		  $retorno['DIG_FINAIS'] = $resultado['billing_info'][0]['card']['last_digits'];
		  $retorno['ID'] = $resultado['id'];
		  $retorno['NAME'] = $resultado['name'];
	  }else{
		  $retorno['STATUS'] = 'ERRO';
		  $retorno['MSG'] 	 = 'Erro ao cadastrar customer';
	  }
	  
	  return $retorno;
  
}

function editar_dados_pagamento($encrypted,$ccv,$nomeCartao,$idCliente){
	global $tokenPagBank;

	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$data = '[{
		"type": "CREDIT_CARD",
		"card": {
			"encrypted" : "'.$encrypted.'"
			"security_code": "'.$ccv.'",
			"holder": {
				"name": "'.$nomeCartao.'"
			}
		}
	}
	]';

	$data = utf8_encode($data);
	  
	  $url = 'https://api.assinaturas.pagseguro.com/customers/'.$idCliente.'/billing_info';
	  
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
  
	  $errors = curl_error($ch);                                                                                                            
	  $result = curl_exec($ch);	
	  
	  $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  
	  curl_close($ch);  
	  
	  $resultado = json_decode($result, true);
	  
	  $retorno = Array();
	  if($returnCode==201) {
		  $retorno['STATUS'] = 'OK';	
		  $retorno['ID_CARTAO'] = $resultado['id'];
		  $retorno['TOKEN_CARTAO'] = $resultado['billing_info'][0]['card']['token'];
		  $retorno['DIG_FINAIS'] = $resultado['billing_info'][0]['card']['last_digits'];
		  $retorno['NAME'] = $resultado['name'];
	  }else{
		  $retorno['STATUS'] = 'ERRO';
		  $retorno['MSG'] 	 = 'Erro ao editar cartao de crédito';
	  }

	  return $retorno;
}

function envia_dados_criptografia($dadosCartao){
	global $tokenPagBank;
	
	$headers = array("Content-Type: application/json","Authorization: Bearer " . $tokenPagBank);

	$urlToken = 'https://api.assinaturas.pagseguro.com/public-keys';

	$ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $urlToken);
	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
  
	  $errors = curl_error($ch);                                                                                                            
	  $result = curl_exec($ch);	
	  
	  $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  
	curl_close($ch);  
	  
	$resultado = json_decode($result, true);

	$data = '{ 
		"public_key": "'.$resultado['public_key'].'",
		"nomeCartao": "'.$dadosCartao['nomeCartao'].'",
		"numeroCartao": "'.$dadosCartao['numeroCartao'].'",
		"mesExp": "'.$dadosCartao['mesExp'].'",
		"anoExp": "'.$dadosCartao['anoExp'].'",
		"codSegCartao": "'.$dadosCartao['codSegCartao'].'"
	}';

	$data = utf8_encode($data);
	
	$url = 'http://aliancanet2/Servidoral2/services/CriptografiaPagBank.php';
	
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
	$result2 = curl_exec($ch);	
	
	$returnCode2 = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);  
	
	$resultado2 = json_decode($result2, true);
	return $resultado2;
}