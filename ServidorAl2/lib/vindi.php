<?php

$chave = retornaValorConfiguracao('CHAVE_VINDI');
$url = retornaValorConfiguracao('URL_VINDI');

//$idProdutoFaturaAvulsa = 879363;
//$idProdutoMensalidade  = 879365;
//$idPlano = 244657;

SetChaveVindi($chave);
SetUrlVindi($url);


//print_r(CadastraUsuarioVindi('Leonardo Witzel','leo@javenessi.com.br','1','06435031967','','RUA PEDRO GUSSO','12','APT606','80200050','NOVO MUNDO','CURITIBA','PR','041996120443','041996120443'));
//Array ( [STATUS] => OK [ID] => 22629457 ) 
//print_r(AtualizaUsuarioVindi('22629457','Diego Ribeiro da Roza','diego2607@gmail.com','5','00206410107','','RUA SETE DE SETEMBRO','366','APT10','79904682','CENTRO','PONTA PORÃ','MS','041998061407','041998061407'));
//Array ( [STATUS] => OK [ID] => 22629457 ) 
//print_r(ListaUsuarioPorDocumento('00206410107'));
//Array ( [STATUS] => OK [ID] => Array ( [0] => Array ( [id] => 22629457 [name] => Diego Ribeiro da Roza [email] => diego2607@gmail.com [registry_code] => 00206410107 [code] => 5 [notes] => [status] => active [created_at] => 2021-09-08T12:06:00.000-03:00 [updated_at] => 2021-09-09T14:21:51.000-03:00 [metadata] => Array ( ) [address] => Array ( [street] => RUA SETE DE SETEMBRO [number] => 366 [additional_details] => APT10 [zipcode] => 79904682 [neighborhood] => CENTRO [city] => PONTA PORÃ [state] => MS [country] => BR ) [phones] => Array ( [0] => Array ( [id] => 8683892 [phone_type] => landline [number] => 5541998061407 [extension] => ) [1] => Array ( [id] => 8683893 [phone_type] => mobile [number] => 5541998061407 [extension] => ) ) ) [1] => Array ( [id] => 22629876 [name] => Diego Ribeiro da ROza [email] => diego2607@gmail.com [registry_code] => 00206410107 [code] => 2 [notes] => [status] => inactive [created_at] => 2021-09-08T12:32:55.000-03:00 [updated_at] => 2021-09-08T12:32:55.000-03:00 [metadata] => Array ( ) [address] => Array ( [street] => RUA SETE DE SETEMBRO [number] => 366 [additional_details] => APT10 [zipcode] => 79904682 [neighborhood] => CENTRO [city] => PONTA PORÃ [state] => MS [country] => BR ) [phones] => Array ( [0] => Array ( [id] => 8684062 [phone_type] => landline [number] => 41998061407 [extension] => ) [1] => Array ( [id] => 8684063 [phone_type] => mobile [number] => 41998061407 [extension] => ) ) ) [2] => Array ( [id] => 22629898 [name] => Diego Ribeiro da ROza [email] => diego2607@gmail.com [registry_code] => 00206410107 [code] => 3 [notes] => [status] => inactive [created_at] => 2021-09-08T12:34:14.000-03:00 [updated_at] => 2021-09-08T12:34:14.000-03:00 [metadata] => Array ( ) [address] => Array ( [street] => RUA SETE DE SETEMBRO [number] => 366 [additional_details] => APT10 [zipcode] => 79904682 [neighborhood] => CENTRO [city] => PONTA PORÃ [state] => MS [country] => BR ) [phones] => Array ( [0] => Array ( [id] => 8684073 [phone_type] => landline [number] => 41998061407 [extension] => ) [1] => Array ( [id] => 8684074 [phone_type] => mobile [number] => 41998061407 [extension] => ) ) ) [3] => Array ( [id] => 22629939 [name] => Diego Ribeiro da ROza [email] => diego2607@gmail.com [registry_code] => 00206410107 [code] => 4 [notes] => [status] => inactive [created_at] => 2021-09-08T12:37:27.000-03:00 [updated_at] => 2021-09-08T12:37:27.000-03:00 [metadata] => Array ( ) [address] => Array ( [street] => RUA SETE DE SETEMBRO [number] => 366 [additional_details] => APT10 [zipcode] => 79904682 [neighborhood] => CENTRO [city] => PONTA PORÃ [state] => MS [country] => BR ) [phones] => Array ( [0] => Array ( [id] => 8684085 [phone_type] => landline [number] => 41998061407 [extension] => ) [1] => Array ( [id] => 8684086 [phone_type] => mobile [number] => 41998061407 [extension] => ) ) ) ) ) 
//print_r(SalvaCartaoPerfil(22629457,'Diego Ribeiro da Roza','00206410107','12/2021','5555555555555557','123','mastercard'));
//Array ( [STATUS] => OK [ID] => 41157373 ) 
//print_r(ListaCartaoPerfil(22629457));
//Array ( [STATUS] => OK [PERFIS_PAGAMENTO] => Array ( [0] => Array ( [id] => 41155859 [status] => active [holder_name] => DIEGO RIBEIRO DA ROZA [registry_code] => 00206410107 [bank_branch] => [bank_account] => [card_expiration] => 2021-12-31T23:59:59.000-03:00 [allow_as_fallback] => [card_number_first_six] => 555555 [card_number_last_four] => 5557 [token] => 62f194fd-d0a0-49e5-9cb0-27396a6fb83a [gateway_token] => ba6de8ba-bd1e-4a36-9a72-8dc5c2a603c2 [type] => PaymentProfile::CreditCard [created_at] => 2021-09-08T16:17:32.000-03:00 [updated_at] => 2021-09-08T16:17:32.000-03:00 [payment_company] => Array ( [id] => 1 [name] => MasterCard [code] => mastercard ) [payment_method] => Array ( [id] => 52558 [public_name] => Cartão de crédito [name] => Cartão de crédito [code] => credit_card [type] => PaymentMethod::CreditCard ) [customer] => Array ( [id] => 22629457 [name] => Diego Ribeiro da Roza [email] => diego2607@gmail.com [code] => 5 ) ) ) ) 
//print_r(DeletaCartaoPerfil(41155859));
//print_r(GeraFaturaAvulsa(22629457,41157373,$idProdutoFaturaAvulsa,18.99,'00003a5_TEMP',1));
//Array ( [STATUS] => OK [ID] => 122170206 ) 
//print_r(CriaNovaAssinatura('01000400123659',22629457,41157373,$idPlano,$idProdutoMensalidade,25.90,'09/09/2021',10));
//Array ( [STATUS] => OK [ID] => 17872160  )
//print_r(CancelaAssinatura(23536880 ,true,'Teste Cancelamento'));
//Array ( [STATUS] => OK [ID] => 17867097 ) 
//print_r(AtualizaAssinatura(17872160,'01000400123652_12347','41157373'));
//print_r(ListaAssinaturasAtivasCliente(22629457));
//Array ( [STATUS] => OK [ID] => Array ( [0] => Array ( [id] => 17872160 [status] => active [start_at] => 2021-09-09T00:00:00.000-03:00 [end_at] => 2022-07-08T23:59:59.000-03:00 [next_billing_at] => 2021-10-09T00:00:00.000-03:00 [overdue_since] => [code] => 01000400123652_12347 [cancel_at] => [interval] => months [interval_count] => 1 [billing_trigger_type] => beginning_of_period [billing_trigger_day] => 0 [billing_cycles] => 10 [installments] => 1 [created_at] => 2021-09-09T14:21:50.000-03:00 [updated_at] => 2021-09-09T14:47:11.000-03:00 [customer] => Array ( [id] => 22629457 [name] => Diego Ribeiro da Roza [email] => diego2607@gmail.com [code] => 5 ) [plan] => Array ( [id] => 244657 [name] => Assinatura [code] => ) [product_items] => Array ( [0] => Array ( [id] => 24592455 [status] => active [uses] => 1 [cycles] => [quantity] => 1 [created_at] => 2021-09-09T14:21:50.000-03:00 [updated_at] => 2021-09-09T14:21:50.000-03:00 [product] => Array ( [id] => 879365 [name] => Mensalidade [code] => ) [pricing_schema] => Array ( [id] => 20449043 [short_format] => R$ 25,90 [price] => 25.9 [minimum_price] => [schema_type] => flat [pricing_ranges] => Array ( ) [created_at] => 2021-09-09T14:21:50.000-03:00 ) [discounts] => Array ( ) ) ) [payment_method] => Array ( [id] => 52558 [public_name] => Cartão de crédito [name] => Cartão de crédito [code] => credit_card [type] => PaymentMethod::CreditCard ) [current_period] => Array ( [id] => 68556392 [billing_at] => 2021-09-09T00:00:00.000-03:00 [cycle] => 1 [start_at] => 2021-09-09T00:00:00.000-03:00 [end_at] => 2021-10-08T23:59:59.000-03:00 [duration] => 2591999 ) [metadata] => Array ( ) [payment_profile] => Array ( [id] => 41157373 [holder_name] => DIEGO RIBEIRO DA ROZA [registry_code] => 00206410107 [bank_branch] => [bank_account] => [card_expiration] => 2021-12-31T23:59:59.000-03:00 [allow_as_fallback] => [card_number_first_six] => 555555 [card_number_last_four] => 5557 [token] => 668aceb0-893b-40a2-8730-e2ff72900e20 [created_at] => 2021-09-08T16:51:33.000-03:00 [payment_company] => Array ( [id] => 1 [name] => MasterCard [code] => mastercard ) ) [invoice_split] => ) ) ) 
//Exemplo retorno erro
//Array ( [STATUS] => ERRO [ERROS] => Array ( [0] => Array ( [id] => invalid_parameter [parameter] => code [message] => já está em uso ) ) ) 
function SetChaveVindi($chave){
	global $chaveVindi;
	$chaveVindi = $chave;
}

function SetUrlVindi($url){
	global $urlVindi;
	$urlVindi = $url;
}

function CadastraUsuarioVindi($nome,$email,$codigoCliente,$documento,$observacoes,$rua,$numero,$complemento,$cep,$bairro,$cidade,$estado,$telefone,$celular=''){
	global $chaveVindi,$urlVindi;

	$cep = str_replace('.', '', $cep);
	$cep = str_replace('-', '', $cep);
	
	$data_string = '{
					  "name": "'.$nome.'",
					  "email": "'.$email.'",
					  "registry_code": "'.$documento.'",
					  "code": "'.$codigoCliente.'",
					  "notes": "'.$observacoes.'",
					  "metadata": {"origem":"ALIANCA"},
					  "address": {
						"street": "'.$rua.'",
						"number": "'.$numero.'",
						"additional_details": "'.$complemento.'",
						"zipcode": "'.$cep.'",
						"neighborhood": "'.$bairro.'",
						"city": "'.$cidade.'",
						"state": "'.$estado.'",
						"country": "BR"
					  },
					  "phones": [
						{
						  "phone_type": "landline",
						  "number": "55'.$telefone.'",
						  "extension": ""
						}';
		if($celular!=''){
			$data_string .=',{
						  "phone_type": "mobile",
						  "number": "55'.$celular.'",
						  "extension": ""
						}' ;
		}		
		
		$data_string .= ']
					}';

	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'customers' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==201){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['customer']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}	
}

function AtualizaUsuarioVindi($idVindi,$nome,$email,$codigoCliente,$documento,$observacoes,$rua,$numero,$complemento,$cep,$bairro,$cidade,$estado,$telefone,$celular=''){
	global $chaveVindi,$urlVindi;

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'customers/'.$idVindi );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);

	if($returnCode!=200){
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;	
	}
	$idTelefone = '';
	$idCelular  = '';
	if(count($resultado['customer']['phones'])>0){
		$idTelefone = $resultado['customer']['phones'][0]['id'];
	}
	if(count($resultado['customer']['phones'])>1){
		$idCelular  = $resultado['customer']['phones'][1]['id'];
	}
	
	
	$data_string = '{
					  "name": "'.$nome.'",
					  "email": "'.$email.'",
					  "registry_code": "'.$documento.'",
					  "code": "'.$codigoCliente.'",
					  "notes": "'.$observacoes.'",
					  "metadata": {"origem":"ALIANCA"},
					  "address": {
						"street": "'.$rua.'",
						"number": "'.$numero.'",
						"additional_details": "'.$complemento.'",
						"zipcode": "'.$cep.'",
						"neighborhood": "'.$bairro.'",
						"city": "'.$cidade.'",
						"state": "'.$estado.'",
						"country": "BR"
					  },
					  "phones": [
						{';
		if($idTelefone!=''){
					$data_string .= '"id":"'.$idTelefone.'",';	
		}
		$data_string .= '  "phone_type": "landline",
						  "number": "55'.$telefone.'",
						  "extension": ""
						}';
		
		if($celular!=''){
			$data_string .=',{';
			if($idCelular!=''){
				$data_string .= '"id":"'.$idCelular.'",';
			}
			$data_string .= ' "phone_type": "mobile",
							  "number": "55'.$celular.'",
							  "extension": ""
							}' ;
		}		
		
		$data_string .= ']
					}';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'customers/'.$idVindi );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['customer']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}	
}

function SalvaCartaoPerfil($idVindi,$nome,$documeno,$vencimento,$numeroCartao,$codigoCartao,$bandeira){
	global $chaveVindi,$urlVindi;


	$data_string = '{
					  "holder_name": "'.$nome.'",
					  "registry_code": "'.$documeno.'",
					  "card_expiration": "'.$vencimento.'",
					  "allow_as_fallback": true,
					  "card_number": "'.$numeroCartao.'",
					  "card_cvv": "'.$codigoCartao.'",
					  "payment_method_code": "credit_card",
					  "payment_company_code": "'.$bandeira.'",
					  "customer_id": '.$idVindi.'
					}';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'payment_profiles' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($resultado);
	if($returnCode==201){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['payment_profile']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}	
}

function ListaCartaoPerfil($IdVindi){
	global $chaveVindi,$urlVindi;



	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'payment_profiles?page=1&per_page=50&query=customer_id%3D'.$IdVindi.'%20status%3Dactive%20type%3DPaymentProfile%3A%3ACreditCard&sort_by=created_at&sort_order=asc');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                    
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($resultado);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['PERFIS_PAGAMENTO'] = $resultado['payment_profiles'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		

}

function DeletaCartaoPerfil($IdPerfil){
	global $chaveVindi,$urlVindi;



	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'payment_profiles/'.$IdPerfil);    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                    
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($resultado);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['payment_profile']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		

}

function GeraFaturaAvulsa($IdVindi,$idPerfilPagamento,$idProdutoFaturaAvulsaVindi,$valor,$identificacaoFaturaSistema,$quantParcelas){
	global $chaveVindi,$urlVindi;
	

	$data_string = '{
					  "customer_id": '.$IdVindi.',
					  "code": "FATURA_'.$identificacaoFaturaSistema.'_'.date("d-m-Y").'",
					  "installments": '.$quantParcelas.',
					  "payment_method_code": "credit_card",
					  "bill_items": [
						{
						  "product_id": '.$idProdutoFaturaAvulsaVindi.',
						  "amount": '.$valor.'
						}
					  ],
					  "metadata": "metadata",
					  "payment_profile": {
						"id": '.$idPerfilPagamento.'
					  }
					}';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'bills' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==201){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['bill']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		
}

function CriaNovaAssinatura($codigoAssociado,$IdVindi,$idPerfilPagamento,$idPlano,$idProdutoMensalidade,$valor,$dataInicialVencimentoBr,$meses){
	global $chaveVindi,$urlVindi;
	
	$data = explode('/',$dataInicialVencimentoBr);
	
	$data = $data[2].'-'.$data[1].'-'.$data[0];

	$data_string = '{
					  "start_at": "'.$data.'",
					  "plan_id": '.$idPlano.',
					  "customer_id": '.$IdVindi.',
					  "code": "'.'ASSINATURA_'.$codigoAssociado.'_'.date("d-m-Y").'",
					  "payment_method_code": "credit_card",
					  "billing_cycles": '.$meses.',
					  "metadata": "metadata",
					  "product_items": [
						{
						  "product_id": '.$idProdutoMensalidade.',
						  "pricing_schema": {
							"price": '.$valor.'
						  }
						}
					  ],
					  "payment_profile": {
						"id": '.$idPerfilPagamento.'
					  }
					}';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'subscriptions' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==201){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['subscription']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		
}

function CancelaAssinatura($IdAssinatura,$cancelaFaturasPendentes=false,$obs=''){
	global $chaveVindi,$urlVindi;

	$url = $IdAssinatura;
	
	if($cancelaFaturasPendentes)
		$url .= '?cancel_bills=true';
	else
		$url .= '?cancel_bills=false';
	
	$url .= '&comments='.urlencode($obs);
	

	echo($urlVindi.'subscriptions/'.$url);

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'subscriptions/'.$url);    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['subscription']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		
}

function AtualizaAssinatura($IdAssinatura,$identificadorUnicoAssinatura,$idPerfilPagamento){
	global $chaveVindi,$urlVindi;
	
		$data_string = '{
					  "code": "'.$identificadorUnicoAssinatura.'",
					  "payment_method_code": "credit_card",
					  "metadata": "metadata",
					  "payment_profile": {
						"id": '.$idPerfilPagamento.'
					  }
					}';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'subscriptions/'.$IdAssinatura );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['subscription']['id'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		
}

function ListaAssinaturasAtivasCliente($IdVindi){
	global $chaveVindi,$urlVindi;
	
	$ch = curl_init(); 	
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'subscriptions?page=1&per_page=50&query=customer_id%3D'. $IdVindi .'%20status%3Dactive&sort_by=created_at&sort_order=asc');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['subscriptions'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		
}

function ListaUsuarioPorDocumento($documento){
	global $chaveVindi,$urlVindi;
	
	$ch = curl_init(); 	
	curl_setopt($ch, CURLOPT_URL, $urlVindi.'customers?page=1&per_page=50&query=registry_code%3D'.$documento.'&sort_by=created_at&sort_order=asc');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'authorization: Basic '.base64_encode($chaveVindi),
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['ID'] = $resultado['customers'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado['errors'];
		return $retorno;
	}		
}


?>