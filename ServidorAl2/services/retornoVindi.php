<?php
require('../lib/base.php');

$dados = file_get_contents("php://input");
$dados = json_decode($dados, True);



if($dados['event']['type'] == 'bill_paid'){
	
	$valor = $dados['event']['data']['bill']['amount'];//Valor
	
	
	if(count($dados['event']['data']['bill']['subscription'])>0){//ASSINATURA
		//Deixei a estrutura aqui para se precisar de algum dado 
		//{"event":{"type":"bill_paid","created_at":"2021-09-09T17:46:33.926-03:00","data":{"bill":{"id":122294153,"code":null,"amount":"25.9","installments":1,"status":"paid","seen_at":null,"billing_at":null,"due_at":null,"url":"https:\/\/app.vindi.com.br\/customer\/bills\/122294153?token=acf06ed9-0ff1-4cb5-9ef0-338a956f3bac","created_at":"2021-09-09T17:46:33.000-03:00","updated_at":"2021-09-09T17:46:33.000-03:00","bill_items":[{"id":150260801,"amount":"25.9","quantity":1,"pricing_range_id":null,"description":null,"pricing_schema":{"id":20452046,"short_format":"R$ 25,90","price":"25.9","minimum_price":null,"schema_type":"flat","pricing_ranges":[],"created_at":"2021-09-09T17:46:33.000-03:00"},"product":{"id":879365,"name":"Mensalidade","code":null},"product_item":{"id":24597238,"product":{"id":879365,"name":"Mensalidade","code":null}},"discount":null}],"charges":[{"id":133042179,"amount":"25.9","status":"paid","due_at":"2021-09-09T23:59:59.000-03:00","paid_at":"2021-09-09T17:46:33.000-03:00","installments":1,"attempt_count":1,"next_attempt":null,"print_url":null,"created_at":"2021-09-09T17:46:33.000-03:00","updated_at":"2021-09-09T17:46:33.000-03:00","last_transaction":{"id":230504367,"transaction_type":"charge","status":"success","amount":"25.9","installments":1,"gateway_message":"Transacao aprovada","gateway_response_code":null,"gateway_authorization":"83908EF644074C6F853DACD7877EA719","gateway_transaction_id":"4150a5ed-4637-4d77-be52-24869703cbc3","gateway_response_fields":{"nsu":"37CED138D789C8A9A347E9C499429942"},"fraud_detector_score":null,"fraud_detector_status":null,"fraud_detector_id":null,"created_at":"2021-09-09T17:46:33.000-03:00","gateway":{"id":51224,"connector":"fake"},"payment_profile":{"id":41157373,"holder_name":"DIEGO RIBEIRO DA ROZA","registry_code":"00206410107","bank_branch":null,"bank_account":null,"card_expiration":"2021-12-31T23:59:59.000-03:00","allow_as_fallback":null,"card_number_first_six":"555555","card_number_last_four":"5557","token":"668aceb0-893b-40a2-8730-e2ff72900e20","created_at":"2021-09-08T16:51:33.000-03:00","payment_company":{"id":1,"name":"MasterCard","code":"mastercard"}}},"payment_method":{"id":52558,"public_name":"Cart\u00e3o de cr\u00e9dito","name":"Cart\u00e3o de cr\u00e9dito","code":"credit_card","type":"PaymentMethod::CreditCard"}}],"customer":{"id":22629457,"name":"Diego Ribeiro da Roza","email":"diego2607@gmail.com","code":"5"},"period":{"id":68560163,"billing_at":"2021-09-09T00:00:00.000-03:00","cycle":1,"start_at":"2021-09-09T00:00:00.000-03:00","end_at":"2021-10-08T23:59:59.000-03:00","duration":2591999},"subscription":{"id":17875524,"code":"01000400123652_12349","plan":{"id":244657,"name":"Assinatura","code":null},"customer":{"id":22629457,"name":"Diego Ribeiro da Roza","email":"diego2607@gmail.com","code":"5"}},"metadata":[],"payment_profile":{"id":41157373,"holder_name":"DIEGO RIBEIRO DA ROZA","registry_code":"00206410107","bank_branch":null,"bank_account":null,"card_expiration":"2021-12-31T23:59:59.000-03:00","allow_as_fallback":null,"card_number_first_six":"555555","card_number_last_four":"5557","token":"668aceb0-893b-40a2-8730-e2ff72900e20","created_at":"2021-09-08T16:51:33.000-03:00","payment_company":{"id":1,"name":"MasterCard","code":"mastercard"}},"payment_condition":null}}}}

		$identificacaoFatura = $dados['event']['data']['bill']['subscription']['code'];//ASSINATURA_01400036596
		$identificacaoFatura = explode('_',$identificacaoFatura);
		$dataVencimento = substr($dados['event']['data']['bill']['period']['billing_at'],0,10);
		
		$text  .= 'Assinatura'."\n";
		$text  .= $valor."\n";
		$text  .= $identificacaoFatura[1]."\n";
		$text  .= $dataVencimento."\n";
	
		
	}else{
		//Deixei a estrutura aqui para se precisar de algum dado 
		//{"event":{"type":"bill_paid","created_at":"2021-09-09T16:29:12.637-03:00","data":{"bill":{"id":122289257,"code":"TMP_12302312","amount":"18.99","installments":1,"status":"paid","seen_at":null,"billing_at":null,"due_at":null,"url":"https:\/\/app.vindi.com.br\/customer\/bills\/122289257?token=df4dc73d-a89f-48be-ab53-54bef248accd","created_at":"2021-09-09T16:29:11.000-03:00","updated_at":"2021-09-09T16:29:12.000-03:00","bill_items":[{"id":150254901,"amount":"18.99","quantity":null,"pricing_range_id":null,"description":null,"pricing_schema":null,"product":{"id":879363,"name":"Taxa Adess\u00e3o","code":null},"product_item":null,"discount":null}],"charges":[{"id":133037171,"amount":"18.99","status":"paid","due_at":"2021-09-09T23:59:59.000-03:00","paid_at":"2021-09-09T16:29:12.000-03:00","installments":1,"attempt_count":1,"next_attempt":null,"print_url":null,"created_at":"2021-09-09T16:29:12.000-03:00","updated_at":"2021-09-09T16:29:12.000-03:00","last_transaction":{"id":230494971,"transaction_type":"charge","status":"success","amount":"18.99","installments":1,"gateway_message":"Transacao aprovada","gateway_response_code":null,"gateway_authorization":"B6A36FEF44B0D08CFEAF82F47EF69E1E","gateway_transaction_id":"c43c9f76-88e4-492e-9871-b242c01f89dc","gateway_response_fields":{"nsu":"469531CC14113B6172DA99F0D6207869"},"fraud_detector_score":null,"fraud_detector_status":null,"fraud_detector_id":null,"created_at":"2021-09-09T16:29:12.000-03:00","gateway":{"id":51224,"connector":"fake"},"payment_profile":{"id":41157373,"holder_name":"DIEGO RIBEIRO DA ROZA","registry_code":"00206410107","bank_branch":null,"bank_account":null,"card_expiration":"2021-12-31T23:59:59.000-03:00","allow_as_fallback":null,"card_number_first_six":"555555","card_number_last_four":"5557","token":"668aceb0-893b-40a2-8730-e2ff72900e20","created_at":"2021-09-08T16:51:33.000-03:00","payment_company":{"id":1,"name":"MasterCard","code":"mastercard"}}},"payment_method":{"id":52558,"public_name":"Cart\u00e3o de cr\u00e9dito","name":"Cart\u00e3o de cr\u00e9dito","code":"credit_card","type":"PaymentMethod::CreditCard"}}],"customer":{"id":22629457,"name":"Diego Ribeiro da Roza","email":"diego2607@gmail.com","code":"5"},"period":null,"subscription":null,"metadata":[],"payment_profile":{"id":41157373,"holder_name":"DIEGO RIBEIRO DA ROZA","registry_code":"00206410107","bank_branch":null,"bank_account":null,"card_expiration":"2021-12-31T23:59:59.000-03:00","allow_as_fallback":null,"card_number_first_six":"555555","card_number_last_four":"5557","token":"668aceb0-893b-40a2-8730-e2ff72900e20","created_at":"2021-09-08T16:51:33.000-03:00","payment_company":{"id":1,"name":"MasterCard","code":"mastercard"}},"payment_condition":null}}}}

		$identificacaoFatura = $dados['event']['data']['bill']['code']; //FATURA_124 Exemplo de como insiro na Api
		$identificacaoFatura = explode('_',$identificacaoFatura);
		$text  .= 'Fatura'."\n";
		$text  .= $valor."\n";
		$text  .= $identificacaoFatura[1]."\n";

		//$identificacaoFatura[1]
		//Colocar aqui Regra de como identificar uma fatura na temporaria.
		//fazer a logica que deve ser feita se for uma fatura na temporaria
	}
	
	

}



?>