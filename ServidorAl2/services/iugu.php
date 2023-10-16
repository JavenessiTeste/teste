<?php
global $tokenIugu;
global $linkIugu;

$tokenIugu     = retornaValorConfiguracao('TOKEN_IUGU'); 
$linkIugu      = 'https://api.iugu.com/v1';


//print_r(AlteraAssinatura('B72AE829BC3B49149B24C38EBDA84232','15/03/2023',60.00));
//Array ( [STATUS] => OK [DADOS] => Array ( [id] => B72AE829BC3B49149B24C38EBDA84232 [suspended] => [plan_identifier] => Assinatura_6000 [price_cents] => 6000 [currency] => BRL [features] => Array ( ) [expires_at] => [created_at] => 2022-02-24T21:01:02-03:00 [updated_at] => 2022-02-24T21:14:00-03:00 [customer_name] => MATEUS GABRIEL T [customer_email] => diego2607@gmail.com [cycled_at] => 2022-02-24T21:14:00-03:00 [credits_min] => 0 [credits_cycle] => [payable_with] => Array ( [0] => credit_card [1] => bank_slip ) [ignore_due_email] => [max_cycles] => 0 [cycles_count] => 0 [customer_id] => 695729080B8A44B7B1EE3ADCB37D2007 [plan_name] => Assinatura 60.00 [customer_ref] => MATEUS GABRIEL T [plan_ref] => Assinatura 60.00 [active] => 1 [two_step] => [suspend_on_invoice_expired] => 1 [in_trial] => [credits] => 0 [credits_based] => [recent_invoices] => Array ( [0] => Array ( [id] => B786BFBD6A394FEA8BA006F820C2CDF9 [due_date] => 2022-02-24 [status] => pending [total] => 30.10 BRL [secure_url] => https://faturas.iugu.com/b786bfbd-6a39-4fea-8ba0-06f820c2cdf9-13d6 ) [1] => Array ( [id] => 20919096C875425EA8EAA4186FEDD833 [due_date] => 2022-03-01 [status] => paid [total] => 29.90 BRL [secure_url] => https://faturas.iugu.com/20919096-c875-425e-a8ea-a4186fedd833-cc3f ) ) [subitems] => Array ( ) [logs] => Array ( [0] => Array ( [id] => B6E6C7FC851844EAAE8A1C13BF61D69A [description] => Invoice created [notes] => Invoice created with items: 1x Plan Change: Assinatura 60.00 = 30.10 BRL; [subscription_changes] => {"expires_at":["2023-03-15",null],"plan_identifier":["Assinatura_2990","Assinatura_6000"],"price_cents":[2990,6000],"updated_at":["2022-02-24T21:13:58-03:00","2022-02-24T21:13:59-03:00"]} [created_at] => 2022-02-24T21:14:00-03:00 ) [1] => Array ( [id] => 24DB8FC8668A468CAC4F91C0E6CE60FC [description] => Subscription Updated [notes] => Subscription Updated [subscription_changes] => {"cycled_at":["2022-02-25","2022-02-24T21:14:00-03:00"],"updated_at":["2022-02-24T21:13:59-03:00","2022-02-24T21:14:00-03:00"]} [created_at] => 2022-02-24T21:14:00-03:00 ) [2] => Array ( [id] => 8A6B29E10B8F484EB52F94824C5DC9BB [description] => Invoice created [notes] => Invoice created with items: 1x Subscription Activation: Assinatura 29.90 = 29.90 BRL; [subscription_changes] => {"plan_identifier":[null,"Assinatura_2990"],"customer_id":[null,"695729080B8A44B7B1EE3ADCB37D2007"],"expires_at":[null,"2022-03-01"],"ignore_due_email":[null,false],"two_step":[true,false],"price_cents":[0,2990],"interval":[null,1],"interval_type":[null,"months"],"payable_with":[null,"credit_card,bank_slip"],"id":[null,"B72AE829BC3B49149B24C38EBDA84232"],"created_at":[null,"2022-02-24T21:01:02-03:00"],"updated_at":[null,"2022-02-24T21:01:02-03:00"]} [created_at] => 2022-02-24T21:01:04-03:00 ) [3] => Array ( [id] => 798D7671983A42D095E8F78E9FC94C5A [description] => Subscription Created [notes] => Subscription Created [subscription_changes] => {"plan_identifier":[null,"Assinatura_2990"],"customer_id":[null,"695729080B8A44B7B1EE3ADCB37D2007"],"expires_at":[null,"2022-03-01"],"ignore_due_email":[null,false],"two_step":[true,false],"price_cents":[0,2990],"interval":[null,1],"interval_type":[null,"months"],"payable_with":[null,"credit_card,bank_slip"],"id":[null,"B72AE829BC3B49149B24C38EBDA84232"],"created_at":[null,"2022-02-24T21:01:02-03:00"],"updated_at":[null,"2022-02-24T21:01:02-03:00"]} [created_at] => 2022-02-24T21:01:02-03:00 ) ) [custom_variables] => Array ( ) ) )

//print_r(CancelaAssinatura('B72AE829BC3B49149B24C38EBDA84232'));
//Array ( [STATUS] => OK [DADOS] => Array ( [id] => B72AE829BC3B49149B24C38EBDA84232 [suspended] => [plan_identifier] => Assinatura_6000 [price_cents] => 6000 [currency] => BRL [features] => Array ( ) [expires_at] => [created_at] => 2022-02-24T21:01:02-03:00 [updated_at] => 2022-02-24T21:14:00-03:00 [customer_name] => MATEUS GABRIEL T [customer_email] => diego2607@gmail.com [cycled_at] => 2022-02-25 [credits_min] => 0 [credits_cycle] => [payable_with] => Array ( [0] => credit_card [1] => bank_slip ) [ignore_due_email] => [max_cycles] => 0 [cycles_count] => 0 [customer_id] => 695729080B8A44B7B1EE3ADCB37D2007 [plan_name] => Assinatura 60.00 [customer_ref] => MATEUS GABRIEL T [plan_ref] => Assinatura 60.00 [active] => 1 [two_step] => [suspend_on_invoice_expired] => 1 [in_trial] => [credits] => 0 [credits_based] => [recent_invoices] => [subitems] => Array ( ) [logs] => Array ( [0] => Array ( [id] => B6E6C7FC851844EAAE8A1C13BF61D69A [description] => Fatura criada [notes] => Fatura criada com os items: 1x Plan Change: Assinatura 60.00 = 30.10 BRL; [subscription_changes] => {"expires_at":["2023-03-15",null],"plan_identifier":["Assinatura_2990","Assinatura_6000"],"price_cents":[2990,6000],"updated_at":["2022-02-24T21:13:58-03:00","2022-02-24T21:13:59-03:00"]} [created_at] => 2022-02-24T21:14:00-03:00 ) [1] => Array ( [id] => 24DB8FC8668A468CAC4F91C0E6CE60FC [description] => Assinatura Modificada [notes] => Assinatura Modificada [subscription_changes] => {"cycled_at":["2022-02-25","2022-02-24T21:14:00-03:00"],"updated_at":["2022-02-24T21:13:59-03:00","2022-02-24T21:14:00-03:00"]} [created_at] => 2022-02-24T21:14:00-03:00 ) [2] => Array ( [id] => 8A6B29E10B8F484EB52F94824C5DC9BB [description] => Fatura criada [notes] => Fatura criada com os items: 1x Subscription Activation: Assinatura 29.90 = 29.90 BRL; [subscription_changes] => {"plan_identifier":[null,"Assinatura_2990"],"customer_id":[null,"695729080B8A44B7B1EE3ADCB37D2007"],"expires_at":[null,"2022-03-01"],"ignore_due_email":[null,false],"two_step":[true,false],"price_cents":[0,2990],"interval":[null,1],"interval_type":[null,"months"],"payable_with":[null,"credit_card,bank_slip"],"id":[null,"B72AE829BC3B49149B24C38EBDA84232"],"created_at":[null,"2022-02-24T21:01:02-03:00"],"updated_at":[null,"2022-02-24T21:01:02-03:00"]} [created_at] => 2022-02-24T21:01:04-03:00 ) [3] => Array ( [id] => 798D7671983A42D095E8F78E9FC94C5A [description] => Assinatura Criada [notes] => Assinatura Criada [subscription_changes] => {"plan_identifier":[null,"Assinatura_2990"],"customer_id":[null,"695729080B8A44B7B1EE3ADCB37D2007"],"expires_at":[null,"2022-03-01"],"ignore_due_email":[null,false],"two_step":[true,false],"price_cents":[0,2990],"interval":[null,1],"interval_type":[null,"months"],"payable_with":[null,"credit_card,bank_slip"],"id":[null,"B72AE829BC3B49149B24C38EBDA84232"],"created_at":[null,"2022-02-24T21:01:02-03:00"],"updated_at":[null,"2022-02-24T21:01:02-03:00"]} [created_at] => 2022-02-24T21:01:02-03:00 ) ) [custom_variables] => Array ( ) ) )

function CadastraCliente($nome,$documento,$email,$rua,$numero,$bairro,$complemento,$cidade,$estado,$cep,$areaTelefone,$telefone){
	global $tokenIugu;
	global $linkIugu;
	$data_string = '{
					   "email":"'.$email.'",
					   "name":"'.$nome.'",
					   "notes":"",
					   "phone":"'.$telefone.'",
					   "phone_prefix":"'.$areaTelefone.'",
					   "cpf_cnpj":"'.$documento.'",
					   "cc_emails":"",
					   "zip_code":"'.$cep.'",
					   "number":"'.$numero.'",
					   "street":"'.$rua.'",
					   "city":"'.$cidade.'",
					   "state":"'.$estado.'",
					   "district":"'.$bairro.'",
					   "complement":"'.$complemento.'",
					   "custom_variables":[
						  {
							 "name":"Origem",
							 "value":"AliancaNet2"
						  }
					   ]
					}';


	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkIugu .'/customers' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}

function AlterarCliente($idCliente,$nome,$documento,$email,$rua,$numero,$bairro,$complemento,$cidade,$estado,$cep,$areaTelefone,$telefone){
	global $tokenIugu;
	global $linkIugu;
	$data_string = '{
					   "email":"'.$email.'",
					   "name":"'.$nome.'",
					   "notes":"",
					   "phone":"'.$telefone.'",
					   "phone_prefix":"'.$areaTelefone.'",
					   "cpf_cnpj":"'.$documento.'",
					   "cc_emails":"",
					   "zip_code":"'.$cep.'",
					   "number":"'.$numero.'",
					   "street":"'.$rua.'",
					   "city":"'.$cidade.'",
					   "state":"'.$estado.'",
					   "district":"'.$bairro.'",
					   "complement":"'.$complemento.'"
					}';


	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkIugu .'/customers/'.$idCliente);    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}

function CadastraPlano($valor){
	global $tokenIugu;
	global $linkIugu;
	
	
	$data_string = '{
					   "payable_with":[
						  "all"
					   ],
					   "interval":1,
					   "interval_type":"months",
					   "value_cents":'. number_format($valor, 2, '', '').',
					   "name":"Assinatura '. number_format($valor, 2, '.', '').'",
					   "identifier":"Assinatura_'.number_format($valor, 2, '', '').'",
					   "billing_days":5,
					   "max_cycles":0
					}';

	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkIugu .'/plans' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}

function RetornaCadastraPlanoValor($valor){
	global $tokenIugu;
	global $linkIugu;
	
	$valorpesquisa = 'Assinatura%20'. number_format($valor, 2, '.', '');
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkIugu.'/plans?query='.$valorpesquisa );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
	);                

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($resultado);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		//$retorno['DADOS']  = $resultado;
		if($resultado['totalItems']==0){
			$retornoCadastro = CadastraPlano($valor);
			if($retornoCadastro['STATUS']=='OK'){
				$retorno['ID'] = $retornoCadastro['DADOS']['identifier'];
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['DADOS']  = $retornoCadastro['DADOS'];
			}
		}else{
			//print_r($resultado['items'][0]);
			$retorno['ID'] = $resultado['items'][0]['identifier'];
		}
		return $retorno;
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}


function CriaAssinatura($idCliente,$dataVencimento,$valor,$tipo=''){
	global $tokenIugu;
	global $linkIugu;
	
	$Plano = RetornaCadastraPlanoValor($valor);
	//Array ( [STATUS] => OK [ID] => 8E8491A96B1D4E569F1CD8F50D07A6BD )
	
	$dataVencimento = explode('/',$dataVencimento);
	
	$dataVencimento = $dataVencimento[2].'-'.$dataVencimento[1].'-'.$dataVencimento[0];
	
	if($tipo=='BOLETO'){
		$tipo = 'bank_slip';
	}else if($tipo=='CARTAO'){
		$tipo = 'credit_card';
	}else{
		$tipo = 'all';
	}
	
	if($Plano['STATUS']=='OK'){
		$data_string = '{
						   "payable_with":[
								"'.$tipo.'"
						   ],
						   "two_step":false,
						   "suspend_on_invoice_expired":true,
						   "only_charge_on_due_date":false,
						   "plan_identifier":"'.$Plano['ID'].'",
						   "customer_id":"'.$idCliente.'",
						   "only_on_charge_success":false,
						   "ignore_due_email":false,
						   "expires_at":"'.$dataVencimento.'"
						}';

		$data_string = utf8_encode($data_string);
		
		//print_r($data_string);
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $linkIugu .'/subscriptions' );    
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_POST, true);                                                                   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
			'Content-Type: application/json' ,
			'Accept: application/json' ,
			'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
		);             

		$errors = curl_error($ch);                                                                                                            
		$result = curl_exec($ch);
		


		$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);  
		
		$resultado = json_decode($result, true);
		//print_r($data_string);
		if($returnCode==200){
			$retorno['STATUS'] = 'OK';
			$retorno['DADOS']  = $resultado;
			return $retorno;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['ERROS'] = $resultado;
			return $retorno;
		}	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS']  = 'Erro Plano';
	}
}

function AlteraAssinatura($idAssinatura,$dataVencimento,$valor,$tipo=''){
	global $tokenIugu;
	global $linkIugu;
	
	$Plano = RetornaCadastraPlanoValor($valor);
	//Array ( [STATUS] => OK [ID] => 8E8491A96B1D4E569F1CD8F50D07A6BD )
	
	$dataVencimento = explode('/',$dataVencimento);
	
	$dataVencimento = $dataVencimento[2].'-'.$dataVencimento[1].'-'.$dataVencimento[0];
	
		
	if($tipo=='BOLETO'){
		$tipo = 'bank_slip';
	}else if($tipo=='CARTAO'){
		$tipo = 'credit_card';
	}else{
		$tipo = 'all';
	}
	
	
	if($Plano['STATUS']=='OK'){
		$data_string = '{
						   "payable_with":[
								"'.$tipo.'"
						   ],
						   "two_step":false,
						   "suspend_on_invoice_expired":true,
						   "only_charge_on_due_date":false,
						   "plan_identifier":"'.$Plano['ID'].'",
						   "only_on_charge_success":false,
						   "ignore_due_email":false,
						   "expires_at":"'.$dataVencimento.'"
						}';

		$data_string = utf8_encode($data_string);
		
		print_r($data_string);
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $linkIugu .'/subscriptions/'.$idAssinatura);    
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  
		curl_setopt($ch, CURLOPT_POST, true);                                                                   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
			'Content-Type: application/json' ,
			'Accept: application/json' ,
			'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
		);             

		$errors = curl_error($ch);                                                                                                            
		$result = curl_exec($ch);
		


		$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);  
		
		$resultado = json_decode($result, true);
		print_r($resultado);
		if($returnCode==200){
			$retorno['STATUS'] = 'OK';
			$retorno['DADOS']  = $resultado;
			return $retorno;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['ERROS'] = $resultado;
			return $retorno;
		}	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS']  = 'Erro Plano';
	}
}

function BuscaAssinatura($idAssinatura){
	global $tokenIugu;
	global $linkIugu;
	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkIugu.'/subscriptions/'.$idAssinatura );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
	);                

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($resultado);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}

function DeletaAssinatura($idAssinatura){
	global $tokenIugu;
	global $linkIugu;
	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkIugu.'/subscriptions/'.$idAssinatura );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
	);                

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($resultado);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}

function CancelaAssinatura($idAssinatura){
	global $tokenIugu;
	global $linkIugu;
	

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkIugu .'/subscriptions/'.$idAssinatura);    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
			'Content-Type: application/json' ,
			'Accept: application/json' ,
			'Authorization: Bearer  '.base64_encode($tokenIugu.':')  )                                                           
		);             

	$result = curl_exec($ch);
		


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
	
}

?>