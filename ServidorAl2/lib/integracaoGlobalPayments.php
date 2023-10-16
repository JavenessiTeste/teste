<?php

if($_GET['testePagamento'] == 'OK'){
	require_once('base.php');

	$valor = 0;//Valor zero reconhece como trasação válida sem valor. 
	$order = rand(1000,9999);
	$codigoOperadora = '012005789644001';
	$terminal = '01';
	$numeroCartao = '4548812049400004';
	$vencimentoCartao = '12/2034';
	$codSeguranca = '123';
	$descricaoCompra = 'Teste Pagamento Jave';
	$nomeCartao = 'Pagador de teste';

	primeiroPagamentoGlobalPayments($valor,$order, $codigoOperadora, $terminal, $numeroCartao, $vencimentoCartao, $codSeguranca, $descricaoCompra, $nomeCartao);
}


function primeiroPagamentoGlobalPayments($valor,$order, $codigoOperadora, $terminal, $numeroCartao, $vencimentoCartao, $codSeguranca, $descricaoCompra, $nomeCartao){

	//$LINK_GLOBALPAYMENTS = retornaValorConfiguracao('LINK_GLOBALPAYMENTS'); 
	//$API_GLOBALPAYMENTS = base64_encode(retornaValorConfiguracao('USER_GLOBALPAYMENTS') . ':' . retornaValorConfiguracao('CHAVE_GLOBALPAYMENTS'));		
	//$headers = array("Content-Type: application/json","Authorization: Basic ".$API_GLOBALPAYMENTS,'Accept: application/json', 'Cache-Control:no-cache');
	
	$LINK_GLOBALPAYMENTS = 'https://sis-t.redsys.es:25443/sis/rest/iniciaPeticionREST';
	$headers = array("Content-Type: application/json",'Accept: application/json', 'Cache-Control:no-cache');
	
	$url = $LINK_GLOBALPAYMENTS;
	
	$data = '{
				"Ds_SignatureVersion":"T23V1",
				"Ds_MerchantParameters":[
					"DS_MERCHANT_AMOUNT":"' . $valor . '",
					"DS_MERCHANT_ORDER":"' . $order . '",
					"DS_MERCHANT_MERCHANTCODE":"' . $codigoOperadora . '",
					"DS_MERCHANT_TERMINAL":"' . $terminal . '",
					"DS_MERCHANT_CURRENCY":"986",
					"DS_MERCHANT_PAN":"' . $numeroCartao . '",
					"DS_MERCHANT_EXPIRYDATE":"' . $vencimentoCartao . '",
					"DS_MERCHANT_CVV2":"' . $codSeguranca . '",
					"DS_MERCHANT_TRANSACTIONTYPE":"A",
					"DS_MERCHANT_ACCOUNTTYPE":"01",
					"DS_MERCHANT_PLANTYPE":"01",
					"DS_MERCHANT_PLANINSTALLMENTSNUMBER":"01",
					"DS_MERCHANT_PRODUCTDESCRIPTION":"' . $descricaoCompra . '",
					"DS_MERCHANT_TITULAR":"' . $nomeCartao . '",
					"DS_MERCHANT_RECURRINGPAYMENT":"N"
				]
			}';

	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
	$result = curl_exec($ch);	
	pr($result);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);
	$body = json_decode($result);	
	
	if($_GET['testePagamento'] == 'OK'){
		pr('entrou aqui');
		pr($body,true);
	}
	
	if($body->status==0)
		return '';
    else
		return $body->error->message;	

	
}




?>