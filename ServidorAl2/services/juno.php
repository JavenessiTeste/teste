<?php

$clientId     = retornaValorCFG0003('JUNO_CLIENTE_ID');
$clientSecret = retornaValorCFG0003('JUNO_CLIENTE_SECRET');
$tokePrivado  = retornaValorCFG0003('JUNO_TOKEN_PRIVADO');
$tokePublico  = retornaValorCFG0003('JUNO_TOKEN_PUBLICO');
$chavePix     = retornaValorCFG0003('JUNO_CHAVE_PIX');
$producao  	  = retornaValorCFG0003('JUNO_PRODUCAO') == 'SIM';


SetClientId($clientId);
SetClientSecret($clientSecret);
SetTokenPrivado($tokePrivado);
SetChavePix($chavePix);
SetProducao($producao);


//print_r(CriarWebhook($tokePrivado,'PROPRIO','https://javenessi.com.br/temp/RetornoJuno/retornoJuno.php'));




function getLinkApi($autenticacao){
	global $ambienteProducaoJuno;
	if($ambienteProducaoJuno == true and $autenticacao== true)
		return 'https://api.juno.com.br/authorization-server';
	if($ambienteProducaoJuno == false and $autenticacao== true)
		return 'https://sandbox.boletobancario.com/authorization-server';
	if($ambienteProducaoJuno == true and $autenticacao== false)
		return 'https://api.juno.com.br';
	if($ambienteProducaoJuno == false and $autenticacao== false)
		return 'https://sandbox.boletobancario.com/api-integration';
}

function SetChavePix($valor){
	global $chavePixJuno;
	$chavePixJuno = $valor;
}

function SetTokenPrivado($valor){
	global $tokenPrivadoJuno;
	$tokenPrivadoJuno = $valor;
}

function SetProducao($valor){
	global $ambienteProducaoJuno;
	$ambienteProducaoJuno = $valor;
}


function SetClientId($id){
	global $clientIdJuno;
	$clientIdJuno = $id;
}

function SetClientSecret($secret){
	global $clientSecretJuno;
	$clientSecretJuno = $secret;
}



function SalvaArquivoJuno(){
	global $tokenJuno,$expiracaoTokenJuno;
	
	$conteudo = $tokenJuno.'__::__'.$expiracaoTokenJuno;
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/juno.tk","w");
	fwrite($fp,$conteudo);
	fclose($fp);
}
function LeArquivoArquivoJuno(){
	global $tokenJuno,$expiracaoTokenJuno;
	
	$caminhoArquivo = $_SERVER['DOCUMENT_ROOT'] . "/juno.tk";
	$conteudo ='';
	if (file_exists($caminhoArquivo)) {
		$fp = fopen($caminhoArquivo,"r+");
		
		if(filesize($caminhoArquivo)>0){
			$conteudo =  fread ($fp, filesize($caminhoArquivo));
		}	
		fclose($fp);
		
		if(trim($conteudo)!=''){
			$conteudo = explode('__::__',trim($conteudo));
			$tokenJuno =$conteudo[0];
			$expiracaoTokenJuno =$conteudo[1];	
		}
	}
}

function GeraToken(){
	global $clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	$urlJuno =	getLinkApi(true);

	if($tokenJuno==''){
		LeArquivoArquivoJuno();
	}

	if(time()<$expiracaoTokenJuno){
		$retorno['STATUS'] = 'OK';
		
	}else{
		$data_string = 'grant_type=client_credentials';


		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $urlJuno.'/oauth/token' );    
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_POST, true);                                                                   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(  
			'Content-Type: application/x-www-form-urlencoded',
			'authorization: Basic '.base64_encode($clientIdJuno.':'.$clientSecretJuno))                                                           
		);             

		$errors = curl_error($ch);                                                                                                            
		$result = curl_exec($ch);
		


		$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);  
		
		$resultado = json_decode($result, true);
		//print_r($resultado);
		
		if($returnCode==200){
			$retorno['STATUS'] = 'OK';
			$tokenJuno = $resultado['access_token']; 
			$expiracaoTokenJuno =  time()+($resultado['expires_in']-100);
			SalvaArquivoJuno();
			return $retorno;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['ERROS'] = $resultado;
			return $retorno;
		}
	}	
}


function CadastraCobranca($nome,$documento,$email,$rua,$numero,$bairro,$complemento,$cidade,$estado,$cep,$telefone,$dataNascimento,$referenciaFatura,$descricao,$valor,$dataVencimento,$splitFatura,$diasAposVencimento=0,$multa=0,$mora=0,$notificacaoJuno=False){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);
	
	$cep = str_replace('.', '', $cep);
	$cep = str_replace('-', '', $cep);
	
	$dataVencimento = explode('/',$dataVencimento);
	
	$dataVencimento = $dataVencimento[2].'-'.$dataVencimento[1].'-'.$dataVencimento[0];

	$dataNascimento = explode('/',$dataNascimento);
	
	$dataNascimento = $dataNascimento[2].'-'.$dataNascimento[1].'-'.$dataNascimento[0];

	$JsonSplitFatura = '';
	$primeiro = 'true';
	foreach ($splitFatura as $itemFatura){
		if($JsonSplitFatura !== ''){
			$JsonSplitFatura .= ',';
		}
		$JsonSplitFatura.='{"recipientToken": "'.$itemFatura['TOKEN'].'","amount":'.$itemFatura['VALOR'].',"amountRemainder":'.$primeiro.',"chargeFee": true}';
		
		$primeiro = 'false';
	}


	if($notificacaoJuno)
		$notificacaoJuno = 'true';
	else
		$notificacaoJuno = 'false';

	$data_string = '{
					  "charge": {
						"pixKey": "'.$chavePixJuno.'",
						"pixIncludeImage": true,
						"description": "'.$descricao.'",
						"references": [
						  "'.$referenciaFatura.'"
						],
						"amount": '.$valor.',
						"dueDate": "'.$dataVencimento.'",
						"installments": 1,
						"maxOverdueDays": '.$diasAposVencimento.',
						"fine": '.$multa.',
						"interest": "'.$mora.'",
						"discountAmount": "0.00",
						"discountDays": -1,
						"paymentTypes": [
						  "BOLETO_PIX","CREDIT_CARD"
						],
						"paymentAdvance": false,
						
						"split": [
							'.$JsonSplitFatura.'
						]
					  },
					  "billing": {
						"name": "'.$nome.'",
						"document": "'.$documento.'",
						"email": "'.$email.'",
						"address": {
						  "street": "'.$rua.'",
						  "number": "'.$numero.'",
						  "complement": "'.$complemento.'",
						  "neighborhood": "'.$bairro.'",
						  "city": "'.$cidade.'",
						  "state": "'.$estado.'",
						  "postCode": "'.$cep.'"
						},
						"phone": "'.$telefone.'",
						"birthDate": "'.$dataNascimento.'",
						"notify": '.$notificacaoJuno.'
					  }
					}';

	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/charges' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$tokenPrivadoJuno.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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

function ListaAreasdeNegocios(){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/data/business-areas' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$tokenPrivadoJuno.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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

function CriaContaDigitalPF($nomeContaDigital80,$nomeMae,$cpf,$email,$dataNascimentoBr,$telefone,$rua,$numero,$bairro,$complemento,$cidade,$estado,$cep,$rendaMensal,$pessoaPoliticamenteExposta=false,$codigoBanco,$agenciaBanco,$contaBanco,$nomeTitularConta,$cpfTitularConta,$poupanca=false,$complementoNumeroCaiaxa=''){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	

	$cep = str_replace('.', '', $cep);
	$cep = str_replace('-', '', $cep);

	$nomeContaDigital80 = substr($nomeContaDigital80,0,80);
	$cpf = str_replace('.', '', $cpf);
	$cpf = str_replace('-', '', $cpf);
	$cpf = str_replace('/', '', $cpf);
	$cpf = str_replace('\\', '', $cpf);
	$dataNascimentoBr = explode('/',$dataNascimentoBr);
	$dataNascimentoBr = $dataNascimentoBr[2].'-'.$dataNascimentoBr[1].'-'.$dataNascimentoBr[0];
	
	if($dataNascimentoBr=='--')
		$dataNascimentoBr = '';	
	
	if($poupanca)
		$poupanca = 'SAVINGS';
	else
		$poupanca = 'CHECKING';
	if($complementoNumeroCaiaxa!='')
		$complementoNumeroCaiaxa = '"accountComplementNumber": "'.$complementoNumeroCaiaxa.'",';
	
	if($pessoaPoliticamenteExposta)
		$pessoaPoliticamenteExposta = 'true';
	else
		$pessoaPoliticamenteExposta = 'false';	
	
	

	$data_string = '{
						"type": "PAYMENT",
						"name": "'.$nomeContaDigital80.'",
						"document": "'.$cpf.'",
						"motherName": "'.$nomeMae.'",
						"email": "'.$email.'",
						"birthDate": "'.$dataNascimentoBr.'",
						"phone": "'.$telefone.'",
						"businessArea": 2023,
						"linesOfBusiness": "Profissionais da saúde",
						"address": {
						"street": "'.$rua.'",
						"number": "'.$numero.'",
						"complement": "'.$complemento.'",
						"neighborhood": "'.$bairro.'",
						"city": "'.$cidade.'",
						"state": "'.$estado.'",
						"postCode": "'.$cep.'"
						},
						"bankAccount": {
						"bankNumber": "'.$codigoBanco.'",
						"agencyNumber": "'.$agenciaBanco.'",
						"accountNumber": "'.$contaBanco.'",
						'.$complementoNumeroCaiaxa.'
						"accountType": "'.$poupanca.'",
						"accountHolder": {
							"name":"'.$nomeTitularConta.'",
							"document":"'.$cpfTitularConta.'"}
						},
						"emailOptOut": false,
						"monthlyIncomeOrRevenue": '.floatval($rendaMensal).',
						"pep": '.$pessoaPoliticamenteExposta.'
						}';

	//print_r($data_string);
	//echo '<br><br><br>';
	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/digital-accounts' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$tokenPrivadoJuno.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	
	
	//print_r($data_string);

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
//$tipoEMpresa  "MEI" "EI" "EIRELI" "LTDA" "SA" "INSTITUITION_NGO_ASSOCIATION"
//
/*
$tipoRepresentanteLegal
required
any
Enum: "INDIVIDUAL" "ATTORNEY" "DESIGNEE" "MEMBER" "DIRECTOR" "PRESIDENT"
Como o parâmetro companyType, define a natureza de negócio. Para consultar os valores permitidos, consulte a seção dados adicionais.

Atenção a regra para o campo companyType vs type:
INDIVIDUAL: Empresário/ME Individual, somente para EI, MEI, EIRELI;
ATTORNEY: Procurador, somente para EI, MEI, EIRELI, LTDA, SA, INSTITUTION_NGO_ASSOCIATION;
DESIGNEE: Mandatário", somente para EI, MEI, EIRELI, LTDA, SA, INSTITUTION_NGO_ASSOCIATION;
MEMBER: Sócio, somente para LTDA, SA;
DIRECTOR: Diretor, somente para INSTITUTION_NGO_ASSOCIATION;
PRESIDENT: Presidente, somente para INSTITUTION_NGO_ASSOCIATION
*/
//$mebrosEmpresa Obrigatório para contas PJ de tipoEMpresa SA e LTDA.

function CriaContaDigitalPJ($nomeContaDigital80,$cnpj,$CNAE,$dataAberturaEmpresa,$nomeRepresentanteLegal,$cpfRepresentanteLegal,$dataNascimentoBrRepresentanteLegal,$nomeMaeRepresentanteLegal,$tipoRepresentanteLegal,$mebrosEmpresa,$email,$telefone,$rua,$numero,$bairro,$complemento,$cidade,$estado,$cep,$rendaMensal,$pessoaPoliticamenteExposta=false,$tipoEmpresa,$codigoBanco,$agenciaBanco,$contaBanco,$nomeTitularConta,$cpfTitularConta,$poupanca=false,$complementoNumeroCaiaxa=''){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$cep = str_replace('.', '', $cep);
	$cep = str_replace('-', '', $cep);
	
	$nomeContaDigital80 = substr($nomeContaDigital80,0,80);
	$cnpj = str_replace('.', '', $cnpj);
	$cnpj = str_replace('-', '', $cnpj);
	$cnpj = str_replace('/', '', $cnpj);
	$cnpj = str_replace('\\', '', $cnpj);
	
	$cpfRepresentanteLegal = str_replace('.', '', $cpfRepresentanteLegal);
	$cpfRepresentanteLegal = str_replace('-', '', $cpfRepresentanteLegal);
	$cpfRepresentanteLegal = str_replace('/', '', $cpfRepresentanteLegal);
	$cpfRepresentanteLegal = str_replace('\\', '', $cpfRepresentanteLegal);
	
	$dataNascimentoBrRepresentanteLegal = explode('/',$dataNascimentoBrRepresentanteLegal);
	$dataNascimentoBrRepresentanteLegal = $dataNascimentoBrRepresentanteLegal[2].'-'.$dataNascimentoBrRepresentanteLegal[1].'-'.$dataNascimentoBrRepresentanteLegal[0];
	if($dataNascimentoBrRepresentanteLegal=='--')
		$dataNascimentoBrRepresentanteLegal = '';	
	$dataAberturaEmpresa = explode('/',$dataAberturaEmpresa);
	$dataAberturaEmpresa = $dataAberturaEmpresa[2].'-'.$dataAberturaEmpresa[1].'-'.$dataAberturaEmpresa[0];
	if($dataAberturaEmpresa=='--')
		$dataAberturaEmpresa = '';
	if($poupanca)
		$poupanca = 'SAVINGS';
	else
		$poupanca = 'CHECKING';
	if($complementoNumeroCaiaxa!='')
		$complementoNumeroCaiaxa = '"accountComplementNumber": "'.$complementoNumeroCaiaxa.'",';
	
	if($pessoaPoliticamenteExposta)
		$pessoaPoliticamenteExposta = 'true';
	else
		$pessoaPoliticamenteExposta = 'false';	
	
	$JsonMebrosEmpresa = '';

	if($tipoEmpresa=='SA' or $tipoEmpresa=='LTDA'){
		foreach ($mebrosEmpresa as $membro){
			if($JsonMebrosEmpresa !== ''){
				$JsonMebrosEmpresa .= ',';
			}
			
			$membro['DOCUMENTO'] = str_replace('.', '', $membro['DOCUMENTO']);
			$membro['DOCUMENTO'] = str_replace('-', '', $membro['DOCUMENTO']);
			$membro['DOCUMENTO'] = str_replace('/', '', $membro['DOCUMENTO']);
			$membro['DOCUMENTO'] = str_replace('\\', '', $membro['DOCUMENTO']);
				
			$membro['DATA_NASCIMENTO'] = explode('/',$membro['DATA_NASCIMENTO']);
			$membro['DATA_NASCIMENTO'] = $membro['DATA_NASCIMENTO'][2].'-'.$membro['DATA_NASCIMENTO'][1].'-'.$membro['DATA_NASCIMENTO'][0];
				
			
			$JsonMebrosEmpresa.='{"name": "'.$membro['NOME'].'","document":'.$membro['DOCUMENTO'].',"birthDate":'.$membro['DATA_NASCIMENTO'].'}';
			

		}
		$JsonMebrosEmpresa = ',"companyMembers": ['.$JsonMebrosEmpresa.']';
	}


	$data_string = '{
						"type": "PAYMENT",
						"name": "'.$nomeContaDigital80.'",
						"document": "'.$cnpj.'",
						"email": "'.$email.'",
						"phone": "'.$telefone.'",
						"businessArea": 2023,
						"linesOfBusiness": "Profissionais da saúde",
						"companyType":"'.$tipoEmpresa.'",
						"legalRepresentative": {
							"name": "'.$nomeRepresentanteLegal.'",
							"document": "'.$cpfRepresentanteLegal.'",
							"birthDate": "'.$dataNascimentoBrRepresentanteLegal.'",
							"motherName": "'.$nomeMaeRepresentanteLegal.'",
							"type": "'.$tipoRepresentanteLegal.'"
						},
						"address": {
						"street": "'.$rua.'",
						"number": "'.$numero.'",
						"complement": "'.$complemento.'",
						"neighborhood": "'.$bairro.'",
						"city": "'.$cidade.'",
						"state": "'.$estado.'",
						"postCode": "'.$cep.'"
						},
						"bankAccount": {
						"bankNumber": "'.$codigoBanco.'",
						"agencyNumber": "'.$agenciaBanco.'",
						"accountNumber": "'.$contaBanco.'",
						'.$complementoNumeroCaiaxa.'
						"accountType": "'.$poupanca.'",
						"accountHolder": {
							"name":"'.$nomeTitularConta.'",
							"document":"'.$cpfTitularConta.'"}
						},
						"emailOptOut": false,
						"monthlyIncomeOrRevenue": '.floatval($rendaMensal).',
						"cnae":"'.$CNAE.'",
						"establishmentDate":"'.$dataAberturaEmpresa.'",
						"pep": '.$pessoaPoliticamenteExposta.'
						'.$JsonMebrosEmpresa.'
					}';
	$data_string = utf8_encode($data_string);
	//exit;
	//$data_string = utf8_encode($data_string);
	//print_r($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/digital-accounts' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$tokenPrivadoJuno.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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

function ConsultaContaDigital($resourceToken){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/digital-accounts' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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
function CriarWebhook($resourceToken,$id,$link){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$data_string = '{
				  "url": "'.$link.'?id='.$id.'",
				  "eventTypes": [
					"DOCUMENT_STATUS_CHANGED","DIGITAL_ACCOUNT_CREATED","DIGITAL_ACCOUNT_STATUS_CHANGED","TRANSFER_STATUS_CHANGED","P2P_TRANSFER_STATUS_CHANGED","PAYMENT_NOTIFICATION","CHARGE_STATUS_CHANGED"
				  ]
				}';



	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/notifications/webhooks' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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

function ListaDocumentosEnvio($resourceToken){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/documents' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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
function ConsultarDocumento($resourceToken,$idDocumento){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/documents/'.$idDocumento);    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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

function EnviarDocumentos($resourceToken,$idDocumento,$CaminhoImagem){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	//$filenames = array("teste.png");

	//$files = array();
	//foreach ($filenames as $f){
	//	$files[$f] = file_get_contents($f);
	//}

	// more fields for POST request
	//$fields = array("files"=>"file");

	////$boundary = uniqid();
	//$delimiter = '-------------' . $boundary;

	//$post_data = build_data_files($boundary, $fields, $files);	  

	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/documents/'.$idDocumento.'/files' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
	);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('files'=> new CURLFILE($CaminhoImagem)));
	
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
function ConsultarConta($resourceToken){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/digital-accounts');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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

function EfetuaPagamentoCartao($idFaturaJuno,$hashCartao,$email,$rua,$numero,$bairro,$complemento,$cidade,$estado,$cep){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);
	
	$cep = str_replace('.', '', $cep);
	$cep = str_replace('-', '', $cep);

	$data_string = '{
					  "chargeId": "'.$idFaturaJuno.'",
					  "billing": {
						"email": "'.$email.'",
						"address": {
						  "street": "'.$rua.'",
						  "number": "'.$numero.'",
						  "complement": "'.$complemento.'",
						  "neighborhood": "'.$bairro.'",
						  "city": "'.$cidade.'",
						  "state": "'.$estado.'",
						  "postCode": "'.$cep.'"
						},
						"delayed": false
					  },
					  "creditCardDetails": {
						"creditCardHash": "'.$hashCartao.'"
					  }
					}';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/payments' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$tokenPrivadoJuno.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	
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

function EstornaPagamentoCartao($idPagamento){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);
	


	$data_string = '{}';


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/payments/'.$idPagamento.'/refunds');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$tokenPrivadoJuno.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
	);             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);

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

function SaldoConta($resourceToken){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/balance' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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

function SolicitaTranferencia($resourceToken,$valor){
	global  $chavePixJuno,$tokenPrivadoJuno,$clientIdJuno,$clientSecretJuno,$tokenJuno,$expiracaoTokenJuno;

	GeraToken();
	
	$urlJuno =	getLinkApi(false);	
	
	$data_string = '{
				  "type": "DEFAULT_BANK_ACCOUNT"';
	if($valor==0)			  
			$data_string .=',"amount":'.$valor.' ';
	$data_string .='	}';



	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $urlJuno.'/transfers' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json;charset=utf-8' ,
		'X-Api-Version: 2',   
		'X-Resource-Token: '.$resourceToken.'',
		'Authorization: Bearer  '.$tokenJuno  )                                                           
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


function deParaCampoJuno($campo){
	
	if($campo=='phone'){
		$campo = 'Numero Telefone';
	}else if($campo=='email'){
		$campo = 'Endereço E-mail';
	}else if($campo=='document'){
		$campo = 'Numero Cpf/Numero Cnpj';
	}else if($campo=='address'){
		$campo = 'Endereco';
	}else if($campo=='email'){
		$campo = 'Endereço E-mail';
	}else if($campo=='bankAccount.accountHolder.document'){
		$campo = 'Documento Titular Conta';
	}else if($campo=='bankAccount.accountHolder.name'){
		$campo = 'Nome Titular Conta';
	}else if($campo=='monthlyIncomeOrRevenue'){
		$campo = 'Renda Mensal';
	}else if($campo=='address.postCode'){
		$campo = 'CEP';
	}else if($campo=='legalRepresentative.birthDate'){
		$campo = 'Data Nascimento Representante Legal ';
	}else if($campo=='companyType'){
		$campo = 'Tipo Empresa';
	}else if($campo=='legalRepresentative.document'){
		$campo = 'Cpf Representante Legal';
	}else if($campo=='cnae'){
		$campo = 'Numero CNAE ';
	}else if($campo=='legalRepresentative.type'){
		$campo = ' Tipo Representante Legal ';
	}else if($campo=='legalRepresentative.name'){
		$campo = 'Nome Representante Legal ';
	}else if($campo=='legalRepresentative.motherName'){
		$campo = 'Nome Mãe Representante Legal';
	}else if($campo=='billing.address.state'){
		$campo = 'Estado';
	}else if($campo=='billing.document'){
		$campo = 'Numero Cpf';
	}else if($campo=='billing.address.postCode'){
		$campo = 'CEP';
	}else if($campo=='number'){
		$campo = 'O número do endereço não está separado com virgula';
	}

	return $campo;

}

?>