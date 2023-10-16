<?php
require_once('../lib/base.php');

$chave = retornaValorConfiguracao('CHAVE_TALLOS');

SetChaveTallos($chave);
SetUrlTallos("https://kong.tallos.com.br/megasac-api/api/");



function SetChaveTallos($chave){
	global $chaveTallos;
	$chaveTallos = $chave;
}

function SetUrlTallos($url){
	global $urlTallos;
	$urlTallos = $url;
}

//$variaveis = array();
//$variaveis[] = 'Boa Tarde';
//$variaveis[] = 'Diego';
//print_r(EnviaMsgWhatsAppTemplateTallos('6501b998e31c4d001269a4c0','41998061407', $variaveis));


function EnviaMsgWhatsAppTemplateTallos($idTemplate,$celular, $variaveis){
	global $chaveTallos,$urlTallos;

	$celular = remove_caracteres($celular);
	
	$data_string = '{
		"template_message_id": "'.$idTemplate.'",
		"recipient_number": "55'.$celular.'",
		"country_code": "55",
		"variables": '.json_encode($variaveis).',
		"sent_by": "bot"
	}';

	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlTallos.'v3/message/template/send' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Authorization: Bearer '.$chaveTallos,
		'Content-Type: application/json')                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);

	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
	if($returnCode==201){
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = $resultado['message'];
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = $resultado['message'];
		return $retorno;
	}	
}
?>