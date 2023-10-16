<?php
include_once('efetivaBeneficiario.php');
global $link, $sellerId, $clientId, $clientSecret,$chave; 



$link = "https://api-sandbox.getnet.com.br";
		 
$sellerId     = "511e8900-36d5-4c38-acf0-2e44f4b68527";
$clientId     = "9e532e2d-f173-499f-983b-ba515de6dee0";
$clientSecret = "428e22ae-e5d7-43b6-a73f-c4e576b9d17e";


/*
$link = "https://api.getnet.com.br/";
		 
$sellerId     = "1f76c887-f79e-435f-a78d-fc631d58a89a";
$clientId     = "04881815-430b-401f-a35c-bfdb92389208";
$clientSecret = "b6ba2b73-7c54-47c7-bab1-6a018f7a6bb8";
*/

function Autentica(){

	global $link, $sellerId, $clientId, $clientSecret; 
	
	$authorization = base64_encode($clientId.':'.$clientSecret);

	$data_string = 'scope=oob&grant_type=client_credentials';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $link.'/auth/oauth/v2/token' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.$authorization,
		'Content-Type: application/x-www-form-urlencoded')                                                           
	);             

	                                                                                                     
	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  

	$retorno = json_decode($result, true);
	$retorno['status_code'] = $returnCode;
	$retorno['tk']   = $retorno['token_type'].' '.$retorno['access_token'];
	if($returnCode==200)
		return $retorno['tk'];	
	else
		return '';
}

function TokenCartao($numeroCartao,$chave){

	global $link, $sellerId, $clientId, $clientSecret; 

	$data_string = '{"card_number": "'.$numeroCartao.'"}';

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $link.'/v1/tokens/card' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: '.$chave,
		'content-type: application/json')                                                           
	);             

	                                                                                                     
	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);

	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$retorno = json_decode($result, true);
	$retorno['status_code'] = $returnCode;
	
	//if($returnCode==201)
		//return $retorno['number_token'];	
	return $retorno;
	//else
	//	return '';
}

function VerificaCartao($tokenCartao,$bandeira,$nomeCartao,$mesVencimento,$anoVencimento,$codigoSeguranca,$chave){
	//"status": "VERIFIED", //"VERIFIED" "NOT VERIFIED"
	//"verification_id": "ae267804-503c-4163-b1b1-f5da5120b74e",
	//"authorization_code": "6964722471672911"	
	//Bandeiras  "Mastercard" "Visa" "Amex" "Elo" "Hipercard"
	global $link, $sellerId, $clientId, $clientSecret; 

	$data_string = '{"number_token": "'.$tokenCartao.'","brand":"'.$bandeira.'","cardholder_name": "'.$nomeCartao.'","expiration_month" : "'.$mesVencimento.'","expiration_year" : "'.$anoVencimento.'","security_code" : "'.$codigoSeguranca.'"}';

	$ch = curl_init(); 
	
	curl_setopt($ch, CURLOPT_URL, $link.'/v1/cards/verification');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: '.$chave,
		'content-type: application/json')                                                           
	);             

	                                                                                                     
	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);

	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$retorno = json_decode($result, true);
	
	if($returnCode==200)
		return $retorno;	
	else
		return '';
}

function PagamentoCredito($valor,$registroPagamento,$codigoCliente,$nome,$email,$cpf,$telefone,$endereco,$numero,$complemento,$bairro,$cidade,$estado,$cep,$tokenCartao,$bandeira,$nomeCartao,$mesVencimento,$anoVencimento,$codigoSeguranca,$chave,$parcelas=1){

	global $link, $sellerId, $clientId, $clientSecret; 

	$nomeSeparado = explode(" ", trim($nome));
	
	$transaction_type = "FULL";
	$number_installments = 1;
	
	if($parcelas>1){
		$transaction_type = "INSTALL_NO_INTEREST";
		$number_installments = $parcelas;
		if($parcelas>6){
			$number_installments = 6;
		}
		$valor = substr($valor, 0, -2).'.'.substr($valor, -2);
		$valor = number_format(($valor)*pow(1.05,$number_installments),2);
		$valor = str_replace('.','',($valor));
		$valor = str_replace(',','',($valor));
	}
	
 
	$primeiroNome = $nomeSeparado[0];
	$ultimoNome   = $nomeSeparado[count($nomeSeparado)-1];
	//pr($primeiroNome);
	//pr($ultimoNome);
	//exit;

	if(strlen($cpf)==11){
		$tipoDocumento = 'CPF';  
	}else{
		$tipoDocumento = 'CNPJ'; 
	}
	
	$data_string = '{
					  "seller_id": "'.$sellerId.'",
					  "amount": '.$valor.',
					  "currency": "BRL",
					  "order": {
						"order_id": "'.$registroPagamento.'",
						"sales_tax": 0,
						"product_type": "service"
					  },
					  "customer": {
						"customer_id": "'.$codigoCliente.'",
						"first_name": "'.$primeiroNome.'",
						"last_name": "'.$ultimoNome.'",
						"name": "'.$nome.'",
						';
	if($email!=''){
		$data_string .='"email": "'.$email.'",
					   ';
	}
						
		$data_string .='"document_type": "'.$tipoDocumento.'",
						"document_number":"'.$cpf.'",
						"phone_number": "'.$telefone.'",
						"billing_address": {
						  "street": "'.$endereco.'",
						  "number": "'.$numero.'",
						  "complement": "'.$complemento.'",
						  "district": "'.$bairro.'",
						  "city": "'.$cidade.'",
						  "state": "'.$estado.'",
						  "country": "Brasil",
						  "postal_code": "'.$cep.'"
						}
					  },
					  "device":{
						  "ip_address":"'.$_SERVER["REMOTE_ADDR"].'",
						  "device_id" :"'.date("Ymd").($registroPagamento).'"
					  },
					  
					  
					  "shippings": [{
								"first_name": "'.$primeiroNome.'",
								"name": "'.$nome.'",
								';
		if($email!=''){
			$data_string .='"email": "'.$email.'",
						   ';
		}
											
		$data_string .='        "phone_number": "'.$telefone.'",
								"shipping_amount": 0,
								"address": {
								  "street": "'.$endereco.'",
								  "number": "'.$numero.'",
								  "complement": "'.$complemento.'",
								  "district": "'.$bairro.'",
								  "city": "'.$cidade.'",
								  "state": "'.$estado.'",
								  "country": "Brasil",
								  "postal_code": "'.$cep.'"
								}
	}],
					  "credit": {
						"delayed": false,
						"save_card_data": false,
						"transaction_type": "'.$transaction_type.'",
						"number_installments": '.$number_installments.',
						"card": {
						  "number_token": "'.$tokenCartao.'",
						  "cardholder_name":"'.$nomeCartao.'",
						  "security_code": "'.$codigoSeguranca.'",
						  "brand": "'.$bandeira.'",
						  "expiration_month": "'.$mesVencimento.'",
						  "expiration_year": "'.$anoVencimento.'"
						}
					  }
					}';

	$ch = curl_init(); 
	
	curl_setopt($ch, CURLOPT_URL, $link.'/v1/payments/credit');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: '.$chave,
		'content-type: application/json')                                                           
	);             

	                                                                                                     
	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	//var_dump($result);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	
	$retorno = json_decode($result, true);
	$retorno['status_code'] = $returnCode;
	
	$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC,DADOS_ENVIO)
				values(".aspas('PAGAMENTOCREDITO').",".aspas($codigoCliente).",".aspas('pagamento.php').",".aspas($returnCode).",".aspas($result).",".aspas('').",".aspas($registroPagamento).",GETDATE(),".aspas($data_string).")";
	
	$res  = jn_query($insert,false,false);
	
	//if($returnCode==200)
		return $retorno;	
	//else
	//	return '';
}

function PagamentoBoleto($valor,$registroPagamento,$codigoCliente,$nome,$email,$cpf,$telefone,$endereco,$numero,$complemento,$bairro,$cidade,$estado,$cep,$tokenCartao,$bandeira,$nomeCartao,$mesVencimento,$anoVencimento,$codigoSeguranca,$dataVencimento,$chave){

	global $link, $sellerId, $clientId, $clientSecret; 

	$valor = str_replace(".", "", $valor);
	$valor = str_replace(",", "", $valor);

	$primeiroNome = explode(" ", $nome);
 
	$primeiroNome = $primeiroNome[0];

	if(strlen($cpf)==11){
		$tipoDocumento = 'CPF';  
	}else{
		$tipoDocumento = 'CNPJ'; 
	}
	
	
	$documento = explode('-',$registroPagamento);
	
	$documento = $documento[1];
	
	$data_string = '{
					  "seller_id": "'.$sellerId.'",
					  "amount": '.$valor.',
					  "currency": "BRL",
					  "order": {
						"order_id": "'.$registroPagamento.'",
						"sales_tax": 0,
						"product_type": "service"
					  },
					  "customer": {
						"customer_id": "'.$codigoCliente.'",
						"first_name": "'.$primeiroNome.'",
						"name": "'.$nome.'",';
	if($email!=''){
		$data_string .='"email": "'.$email.'",';
	}
						
		$data_string .='"document_type": "'.$tipoDocumento.'",
						"document_number":"'.$cpf.'",
						"phone_number": "'.$telefone.'",
						"billing_address": {
						  "street": "'.$endereco.'",
						  "number": "'.$numero.'",
						  "complement": "'.$complemento.'",
						  "district": "'.$bairro.'",
						  "city": "'.$cidade.'",
						  "state": "'.$estado.'",
						  "country": "Brasil",
						  "postal_code": "'.$cep.'"
						}
					  },
					  "boleto": {
						"document_number": "'.$documento.'",
						"instructions": "Pagar até o vencimento",
						"provider": "santander",
						"expiration_date": "'.$dataVencimento.'"
					  }
					}';

	$ch = curl_init(); 
	
	//pr($data_string);
	
	curl_setopt($ch, CURLOPT_URL, $link.'/v1/payments/boleto');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: '.$chave,
		'content-type: application/json')                                                           
	);             

	                                                                                                     
	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);

	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	
	curl_close($ch);
	
	$retorno = json_decode($result, true);
    $retorno['status_code'] = $returnCode;
	$retorno['lk'] = $link;
	
	$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC)
				values(".aspas('PAGAMENTOBOLETO').",".aspas($codigoCliente).",".aspas('pagamento.php').",".aspas($returnCode).",".aspas($result).",".aspas($retorno['boleto']['our_number']).",".aspas($registroPagamento).",GETDATE())";
	
	$res  = jn_query($insert,false,false);
	
	//if($returnCode==201)
		return $retorno;	
	//else
	//	return '';
}
		 
function PagamentoDebito($valor,$registroPagamento,$codigoCliente,$nome,$email,$cpf,$telefone,$endereco,$numero,$complemento,$bairro,$cidade,$estado,$cep,$tokenCartao,$bandeira,$nomeCartao,$mesVencimento,$anoVencimento,$codigoSeguranca,$telefoneTitularCartao,$chave){

	global $link, $sellerId, $clientId, $clientSecret; 

	$nomeSeparado = explode(" ", $nome);
 
	$primeiroNome = $nomeSeparado[0];
	$ultimoNome   = $nomeSeparado[count($nomeSeparado)-1];

	if(strlen($cpf)==11){
		$tipoDocumento = 'CPF';  
	}else{
		$tipoDocumento = 'CNPJ'; 
	}
	
	$data_string = '{
					  "seller_id": "'.$sellerId.'",
					  "amount": '.$valor.',
					  "currency": "BRL",
					  "order": {
						"order_id": "'.$registroPagamento.'",
						"sales_tax": 0,
						"product_type": "service"
					  },
					  "customer": {
						"customer_id": "'.$codigoCliente.'",
						"first_name": "'.$primeiroNome.'",
						"last_name": "'.$ultimoNome.'",
						"name": "'.$nome.'",';
	if($email!=''){
		$data_string .='"email": "'.$email.'",';
	}
						
		$data_string .='"document_type": "'.$tipoDocumento.'",
						"document_number":"'.$cpf.'",
						"phone_number": "'.$telefone.'",
						"billing_address": {
						  "street": "'.$endereco.'",
						  "number": "'.$numero.'",
						  "complement": "'.$complemento.'",
						  "district": "'.$bairro.'",
						  "city": "'.$cidade.'",
						  "state": "'.$estado.'",
						  "country": "Brasil",
						  "postal_code": "'.$cep.'"
						}
					  },
					   "device":{
						  "ip_address":"'.$_SERVER["REMOTE_ADDR"].'",
						  "device_id" :"'.date("Ymd").($registroPagamento).'"
					  },
					  "shippings": [{
								"first_name": "'.$primeiroNome.'",
								"name": "'.$nome.'",
								';
						if($email!=''){
							$data_string .='"email": "'.$email.'",
										   ';
						}
															
						$data_string .='        "phone_number": "'.$telefone.'",
												"shipping_amount": 0,
												"address": {
												  "street": "'.$endereco.'",
												  "number": "'.$numero.'",
												  "complement": "'.$complemento.'",
												  "district": "'.$bairro.'",
												  "city": "'.$cidade.'",
												  "state": "'.$estado.'",
												  "country": "Brasil",
												  "postal_code": "'.$cep.'"
												}
					}],
					  "debit": {
						"cardholder_mobile":"'.$telefoneTitularCartao.'",
						"card": {
						  "number_token": "'.$tokenCartao.'",
						  "cardholder_name":"'.$nomeCartao.'",
						  "security_code": "'.$codigoSeguranca.'",
						  "brand": "'.$bandeira.'",
						  "expiration_month": "'.$mesVencimento.'",
						  "expiration_year": "'.$anoVencimento.'"
						}
					  }
					}';

	//return json_decode(json_encode($data_string));
	//exit;
	
	$ch = curl_init(); 
	
	curl_setopt($ch, CURLOPT_URL, $link.'/v1/payments/debit');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: '.$chave,
		'content-type: application/json')                                                           
	);             

	//echo  $link.'/v1/payments/debit';                                                                                                  
	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	//var_dump($result);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	
	$retorno = json_decode($result, true);
	
	$retorno['status_code'] = $returnCode;
	
	
	
	$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC,DADOS_ENVIO)
				values(".aspas('PAGAMENTODEBITO').",".aspas($codigoCliente).",".aspas('pagamento.php').",".aspas($returnCode).",".aspas($result).",".aspas($retorno['post_data']['issuer_payment_id']).",".aspas($registroPagamento).",GETDATE(),".aspas($data_string).")";
	
	$res  = jn_query($insert,false,false);
	
	//if($returnCode==201)
		return $retorno;	
	//else
	//	return '';
}
/*
function FinalizaPagamentoDebito($retorno,$idPagamento,$chave){

	global $link, $sellerId, $clientId, $clientSecret; 
	                 
	//$data_string = '{payer_authentication_response: "'. $retorno.'"}';
	$data_string['payer_authentication_response']= $retorno;
	$data_string = json_encode($data_string);
	$ch = curl_init(); 
	
	curl_setopt($ch, CURLOPT_URL, $link.'/v1/payments/debit/'.$idPagamento.'/authenticated/finalize');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	//curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	//curl_setopt($ch, CURLOPT_ENCODING , "gzip");
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(  
		'authorization: '.$chave,
		'content-type : application/json')                                                           
	);             

	echo 'authorization:'. $chave.'<br>';
	echo 'Link:'. $link.'/v1/payments/debit/'.$idPagamento.'/authenticated/finalize'.'<br>';          
    echo 'Dados Enviados :'. $data_string.'<br>';          	
	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	//echo '<br><br><br><br>';
	var_dump($result);
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	
	$retorno = json_decode($result, true);
	$retorno['status_code'] = $returnCode;
	
	$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC,DADOS_ENVIO)
				values(".aspas('FINALIZAPAGAMENTODEBITO').",".aspas('').",".aspas('pagamento.php').",".aspas($returnCode).",".aspas($result).",".aspas('').",".aspas('').",GETDATE(),".aspas($data_string).")";
	
	$res  = jn_query($insert,false,false);
	
	//if($returnCode==201)
		return $retorno;	
	//else
	//	return '';
}
*/

function FinalizaPagamentoDebito3($retorno,$idPagamento,$chave){

	global $link, $sellerId, $clientId, $clientSecret; 
	                 
	$data_string['payer_authentication_response']= $retorno;
	
	$arrContextOptions=array(
		  "ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
			'http' => array(
			'method'  => 'POST',
			'content' => json_encode( $data_string ),
			'header'=>  "Content-Type: application/json\r\n" .
						"authorization: ".$chave."\r\n"
			),
		);  

	$response = file_get_contents($link.'/v1/payments/debit/'.$idPagamento.'/authenticated/finalize', false, stream_context_create($arrContextOptions));
	return json_decode($response,true);
	//print_r($response); 
}
function FinalizaPagamentoDebito($retorno,$idPagamento,$chave){

	global $link, $sellerId, $clientId, $clientSecret; 
	 

	 
	$data_string['payer_authentication_response']= $retorno;
	
	$arrContextOptions=array(
		  "ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
			'http' => array(
			'method'  => 'POST',
			'content' => json_encode( $data_string ),
			'header'=>  "Content-Type: application/json\r\n" .
						"authorization: ".$chave."\r\n"
			),
		);  
	//print_r($arrContextOptions); 
	
	$headers = array("Content-Type: application/json","authorization: ".$chave);
	
	
	$data = '{"payer_authentication_response":"'.$retorno.'"}';
	
	$url = $link.'/v1/payments/debit/'.$idPagamento.'/authenticated/finalize';
	
	//pr($url);
	//pr($data);
	
	//exit;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");

		$result = curl_exec($ch);
		if($result === false)
		{
			echo "Erro : " . curl_error($ch);
			exit;
		}

		curl_close($ch);
	//pr("********************");	
    //pr($result); 
		$body = json_decode($result);
		$body = (array)$body;
		if(!isset($body['status'])){
				$body=(array)$body['details'][0];
		}
	//pr("====================");			
	//pr($body); 
	//$response = file_get_contents($link.'/v1/payments/debit/'.$idPagamento.'/authenticated/finalize', false, stream_context_create($arrContextOptions));
	return $body;
	//print_r($response); 
}

function efetuaBaixa($tipoPagamento,$tipoFatura,$registro,$valor,$autorizacao){
	
	if($tipoPagamento == 'CC'){
		$tipoPagamento = '112';
	}else if($tipoPagamento == 'CD'){
		$tipoPagamento = '101';
	}
	
	if($tipoFatura == 'F'){
		
		$sql = "update ps1020 set OBSERVACOES_COBRANCA = coalesce(OBSERVACOES_COBRANCA,'')+'PAGTEFPAGGETNET',TIPO_PAGAMENTO_TEF=".aspas($tipoPagamento).",data_pagamento = getdate(),valor_pago = ".aspas($valor/100).",tipo_baixa='A',NUMERO_AUTORIZACAO_TEF =".aspas($autorizacao)." where numero_registro = ".aspas($registro);
		jn_query($sql);
		
		//valida reativacao
		$queryReat  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.TIPO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1002.DIA_VENCIMENTO FROM PS1000 ';
		$queryReat .= ' INNER JOIN PS1020 ON (PS1000.CODIGO_ASSOCIADO = PS1020.CODIGO_ASSOCIADO) ';
		$queryReat .= ' INNER JOIN PS1002 ON (PS1000.CODIGO_ASSOCIADO = PS1002.CODIGO_ASSOCIADO) ';
		$queryReat .= ' WHERE PS1000.FLAG_ASSOC_REATIVACAO = "S" AND PS1020.NUMERO_REGISTRO = ' .aspas($registro);
		$resReat = jn_query($queryReat);
		if($rowReat = jn_fetch_object($resReat)){
			
			$queryFatReat  = ' SELECT NUMERO_REGISTRO, CODIGO_ASSOCIADO, DATA_PAGAMENTO FROM PS1020 ';
			$queryFatReat .= ' WHERE ((INFORMACOES_GERACAO = "FATURA_PORTAL_PROP") or (VALOR_FAT_ORIGINAL_PROP > 0)) ';
			$queryFatReat .= ' AND CODIGO_ASSOCIADO = ' . aspas($rowReat->CODIGO_ASSOCIADO);
			$queryFatReat .= ' ORDER BY DATA_PAGAMENTO DESC ';
			$resFatReat = jn_query($queryFatReat);
			if($rowFatReat = jn_fetch_object($resFatReat)){
				
				//Se a fatura proporcional está paga, reativa o associado
				if($rowFatReat->DATA_PAGAMENTO){
					$numeroProtocoloGeral = GeraProtocoloGeralPs6450($rowReat->CODIGO_ASSOCIADO, '');	
					
					$queryInsertProt  = ' INSERT INTO PS6110 (CODIGO_RECLAMACAO_SUGESTAO, NOME_PESSOA, CODIGO_ASSOCIADO, DATA_RECLAMACAO_SUGESTAO, DATA_EVENTO, FLAG_PENDENTE, PROTOCOLO_GERAL_PS6450, DEPARTAMENTO_RESPONSAVEL, FONE_CONTATO, FONE_CONTATO_02, EMAIL_CONTATO, INFORMACOES_LOG_I) VALUES ';
					$queryInsertProt .= ' (' . aspas('26') . ', ' . aspas($rowReat->NOME_ASSOCIADO) . ', ' . aspas($rowReat->CODIGO_ASSOCIADO) . ', getdate(), getdate(), ' . aspas('S') . ', ' . aspas($numeroProtocoloGeral) . ', ' . aspas('CEN') . ', ' . aspas('**') . ', ' . aspas('**') . ', ' . aspas('**') .  ',' . aspas('REATIVACAO_PORTAL') . ')';									
					jn_query($queryInsertProt);	

					$atualizaReat  = ' UPDATE PS1000 SET DATA_EXCLUSAO = NULL, CODIGO_MOTIVO_EXCLUSAO = NULL, ';
					$atualizaReat .= " DESCRICAO_OBSERVACAO = substring(coalesce(DESCRICAO_OBSERVACAO,''), 0, 70) + 'PAGTEFPAGGETNET_REATIVACAO', ";
					$atualizaReat .= " FLAG_ASSOC_REATIVACAO = 'N', ";
					$atualizaReat .= " DATA_ULTIMA_REATIVACAO = CONVERT(date, GETDATE()) ";
					if($rowReat->TIPO_ASSOCIADO == 'T'){
						$atualizaReat .= ' WHERE CODIGO_TITULAR = ' . aspas($rowReat->CODIGO_ASSOCIADO);
					}else{			
						$atualizaReat .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowReat->CODIGO_ASSOCIADO);
					}
					jn_query($atualizaReat);
					
					$atualizaFatNew  = ' UPDATE PS1020 SET DATA_CANCELAMENTO = NULL ';
					$atualizaFatNew .= ' WHERE	DATA_VENCIMENTO > GETDATE() ';
					$atualizaFatNew .= ' 	AND DATA_CANCELAMENTO IS NOT NULL ';
					$atualizaFatNew .= ' 	AND CODIGO_ASSOCIADO = ' . aspas($rowReat->CODIGO_ASSOCIADO);
					jn_query($atualizaFatNew);
					
				}
			}
		}
	}elseif($tipoFatura == 'FA'){		
		
		$sqlTotalFatura ="select SUM(VALOR_FATURA) TOTAL_FATURAS from PS1020 where NUMERO_REGISTRO IN(
			select B.NUMERO_REGISTRO_PS1020 from ESP_FATURAS_AGRUPADAS A 
			inner join ESP_FATURAS_AGRUPADAS B on A.NUMERO_AGRUPAMENTO = B.NUMERO_AGRUPAMENTO 
			where A.numero_registro =".aspas($registro)."
			)";
			
		$resTotalFatura = jn_query($sqlTotalFatura);

		$rowTotalFatura = jn_fetch_object($resTotalFatura);
		
		$totalFaturas = $rowTotalFatura->TOTAL_FATURAS;
        $valorTotalPago = $valor/100;			
		
		$sqlFaturas ="select PS1020.NUMERO_REGISTRO,PS1020.VALOR_FATURA,PS1020.DATA_VENCIMENTO from PS1020 where NUMERO_REGISTRO IN(
							select B.NUMERO_REGISTRO_PS1020 from ESP_FATURAS_AGRUPADAS A 
							inner join ESP_FATURAS_AGRUPADAS B on A.NUMERO_AGRUPAMENTO = B.NUMERO_AGRUPAMENTO 
							where A.numero_registro =".aspas($registro)."
							)";
		$resFatura  = jn_query($sqlFaturas);
		
		$valorTotalUpdate = 0;
		$ultimaFatura = '';
		
		while($rowFatura = jn_fetch_object($resFatura)) {
			
			$ultimaFatura = $rowFatura->NUMERO_REGISTRO;
			
			if($totalFaturas==$valorTotalPago){
				$valorUpdate =$rowFatura->VALOR_FATURA;
			}else{
				
				$dataVencimento = $rowFatura->DATA_VENCIMENTO;
				$valorFatura 	= $rowFatura->VALOR_FATURA;				
				
				$databd     = $dataVencimento->format('d/m/Y');
				$databd     = explode("/",$databd); 
				$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
				$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
				$dias       = ($data_atual-$dataBol)/86400;
				$diasAtrazo = ceil($dias);
				
				if($diasAtrazo>0){
					$valor_boleto    = str_replace(",", ".", $valorFatura);
					$multa = 0.02;
					$mora  = 0.0003333; 

					$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
					$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
					$valorBoleto = explode('.',$valor_boleto);
					$val1 = $valorBoleto[0];
					$val2 = substr($valorBoleto[1],0,2);		
					$valorAtual = $val1 . '.' . $val2;		
					$valor_boleto          =  number_format($valorAtual, 2, '.', '');
					$valorFatura = $valor_boleto;
					$data_venc=date('d/m/Y');
					
				}
				$valorUpdate = $valorFatura;
			}
			
			$valorTotalUpdate = $valorTotalUpdate + $valorUpdate;
			
			$sql = "update ps1020 set OBSERVACOES_COBRANCA = coalesce(OBSERVACOES_COBRANCA,'')+'PAGTEFPAGGETNET',TIPO_PAGAMENTO_TEF=".aspas($tipoPagamento).",data_pagamento = CONVERT(date, GETDATE()), valor_pago = ".aspas($valorUpdate).",tipo_baixa='A',NUMERO_AUTORIZACAO_TEF =".aspas($autorizacao)." where numero_registro = ".aspas($rowFatura->NUMERO_REGISTRO);
			//pr($sql);
			jn_query($sql);
					
		}
		
		if($valorTotalUpdate < $valorTotalPago){
			$valorDiferenca = ($valorTotalPago - $valorTotalUpdate);
			$sqlDiferenca = "update ps1020 set valor_pago = (valor_pago + ".aspas($valorDiferenca).") where numero_registro = ".aspas($ultimaFatura);
			jn_query($sqlDiferenca);
		}
		
		$aux = '';
		if($valorTotalUpdate>$valorTotalPago){
			$sql = "update ps1020 set OBSERVACOES_COBRANCA = coalesce(OBSERVACOES_COBRANCA,'')+'(FA>)',TIPO_PAGAMENTO_TEF=".aspas($tipoPagamento)." where NUMERO_AUTORIZACAO_TEF = ".aspas($autorizacao);
			jn_query($sql);
		}else if($valorTotalUpdate<$valorTotalPago){
			$sql = "update ps1020 set OBSERVACOES_COBRANCA = coalesce(OBSERVACOES_COBRANCA,'')+'(FA<)',TIPO_PAGAMENTO_TEF=".aspas($tipoPagamento)." where NUMERO_AUTORIZACAO_TEF = ".aspas($autorizacao);
			jn_query($sql);		
		}else if($valorTotalUpdate==$valorTotalPago){
			$sql = "update ps1020 set OBSERVACOES_COBRANCA = coalesce(OBSERVACOES_COBRANCA,'')+'(FA=)',TIPO_PAGAMENTO_TEF=".aspas($tipoPagamento)." where NUMERO_AUTORIZACAO_TEF = ".aspas($autorizacao);
			jn_query($sql);	
		}
	}else if(($tipoFatura == 'PF')||($tipoFatura == 'PJ')){	
		$sql = "update TMP1020_NET set OBSERVACOES_COBRANCA = coalesce(OBSERVACOES_COBRANCA,'')+'PAGTEFPAGGETNET',TIPO_PAGAMENTO_TEF=".aspas($tipoPagamento).",data_pagamento = CONVERT(date, GETDATE()), valor_pago = ".aspas($valor/100).",tipo_baixa='A',NUMERO_AUTORIZACAO_TEF =".aspas($autorizacao)." where numero_registro = ".aspas($registro);
		jn_query($sql);
		efetivaBeneficiario($registro);
	}


	//valida reativacao
	$queryReat  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.TIPO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1002.DIA_VENCIMENTO FROM PS1000 ';
	$queryReat .= ' INNER JOIN ESP_FATURAS_AGRUPADAS ON (PS1000.CODIGO_ASSOCIADO = ESP_FATURAS_AGRUPADAS.CODIGO_ASSOCIADO) ';
	$queryReat .= ' INNER JOIN PS1002 ON (PS1000.CODIGO_ASSOCIADO = PS1002.CODIGO_ASSOCIADO) ';
	$queryReat .= ' WHERE PS1000.FLAG_ASSOC_REATIVACAO = "S" AND ESP_FATURAS_AGRUPADAS.NUMERO_REGISTRO = ' .aspas($registro);
	$resReat = jn_query($queryReat);
	if($rowReat = jn_fetch_object($resReat)){
		
		$queryFatReat  = ' SELECT NUMERO_REGISTRO, CODIGO_ASSOCIADO, DATA_PAGAMENTO FROM PS1020 ';
		$queryFatReat .= ' WHERE ((INFORMACOES_GERACAO = "FATURA_PORTAL_PROP") or (VALOR_FAT_ORIGINAL_PROP > 0))  ';
		$queryFatReat .= ' AND CODIGO_ASSOCIADO = ' . aspas($rowReat->CODIGO_ASSOCIADO);
		$queryFatReat .= ' ORDER BY DATA_PAGAMENTO DESC ';
		$resFatReat = jn_query($queryFatReat);
		if($rowFatReat = jn_fetch_object($resFatReat)){
			
			//Se a fatura proporcional está paga, reativa o associado
			if($rowFatReat->DATA_PAGAMENTO){
				
				$numeroProtocoloGeral = GeraProtocoloGeralPs6450($rowReat->CODIGO_ASSOCIADO, '');	
					
				$queryInsertProt  = ' INSERT INTO PS6110 (CODIGO_RECLAMACAO_SUGESTAO, NOME_PESSOA, CODIGO_ASSOCIADO, DATA_RECLAMACAO_SUGESTAO, DATA_EVENTO, FLAG_PENDENTE, PROTOCOLO_GERAL_PS6450, DEPARTAMENTO_RESPONSAVEL, FONE_CONTATO, FONE_CONTATO_02, EMAIL_CONTATO, INFORMACOES_LOG_I) VALUES ';
				$queryInsertProt .= ' (' . aspas('26') . ', ' . aspas($rowReat->NOME_ASSOCIADO) . ', ' . aspas($rowReat->CODIGO_ASSOCIADO) . ', getdate(), getdate(), ' . aspas('S') . ', ' . aspas($numeroProtocoloGeral) . ', ' . aspas('CEN') . ', ' . aspas('**') . ', ' . aspas('**') . ', ' . aspas('**') .  ',' . aspas('REATIVACAO_PORTAL') . ')';									
				jn_query($queryInsertProt);	

				$atualizaReat  = ' UPDATE PS1000 SET DATA_EXCLUSAO = NULL, CODIGO_MOTIVO_EXCLUSAO = NULL, ';
				$atualizaReat .= " DESCRICAO_OBSERVACAO = substring(coalesce(DESCRICAO_OBSERVACAO,''), 0, 70) + 'PAGTEFPAGGETNET_REATIVACAO', ";
				$atualizaReat .= " FLAG_ASSOC_REATIVACAO = 'N', ";
				$atualizaReat .= " DATA_ULTIMA_REATIVACAO = CONVERT(date, GETDATE()) ";
				if($rowReat->TIPO_ASSOCIADO == 'T'){
					$atualizaReat .= ' WHERE CODIGO_TITULAR = ' . aspas($rowReat->CODIGO_ASSOCIADO);
				}else{			
					$atualizaReat .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowReat->CODIGO_ASSOCIADO);
				}
				jn_query($atualizaReat);
				
				$atualizaFatNew  = ' UPDATE PS1020 SET DATA_CANCELAMENTO = NULL ';
				$atualizaFatNew .= ' WHERE	DATA_VENCIMENTO > GETDATE() ';
				$atualizaFatNew .= ' 	AND DATA_CANCELAMENTO IS NOT NULL ';
				$atualizaFatNew .= ' 	AND CODIGO_ASSOCIADO = ' . aspas($rowReat->CODIGO_ASSOCIADO);
				jn_query($atualizaFatNew);
			}
		}
	}
	
}


?>