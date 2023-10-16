<?php
require('../lib/base.php');

$codAssociadoTmp = $dadosInput['codAssociado'] ? $dadosInput['codAssociado'] : $_SESSION['codigoIdentificacao'];

if($dadosInput['tipo']== 'dados'){

	$queryContratos  = ' SELECT ';	
	$queryContratos .= ' 	A.CODIGO_MODELO, TITULO_MODELO, TEXTO_CONTRATO, DESCRICAO_MODELO, NOME_ARQUIVO ';
	$queryContratos .= ' FROM VND1002_ON A ';	
	$queryContratos .= ' INNER JOIN VND1030MODELOS_ON B ON (A.CODIGO_MODELO = B.CODIGO_MODELO) ';
	$queryContratos .= ' WHERE A.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);			

	$resContratos = jn_query($queryContratos);

	$contratos = Array();
	$i = 0;
	while($rowContratos = jn_fetch_object($resContratos)){
		$contratos[$i]['titulo'] = jn_utf8_encode($rowContratos->TITULO_MODELO);
		$contratos[$i]['codigoModelo'] = jn_utf8_encode($rowContratos->CODIGO_MODELO);
		$contratos[$i]['descricaoModelo'] = jn_utf8_encode($rowAssocirowContratosado->DESCRICAO_MODELO);
		$contratos[$i]['linkArquivo'] = $rowContratos->NOME_ARQUIVO;	
			
		$i++;
	}
	foreach ($contratos as $value){
		$retorno[]=$value;
	} 
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'salvar'){
	$retorno = Array();
	if(jn_query('UPDATE VND1002_ON SET FLAG_CONTRATO_ACEITO = "S" WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp))){
		
		$queryAssocCont = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$resAssocCont = jn_query($queryAssocCont);
		while($rowAssocCont = jn_fetch_object($resAssocCont)){			
			$updateStatus  = ' UPDATE VND1000STATUS_ON SET TIPO_STATUS = ' . aspas('CONTRATO_OK');
			$updateStatus .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssocCont->CODIGO_ASSOCIADO);
			$updateStatus .= ' AND TIPO_STATUS = ' . aspas('AGUARDANDO_ACEITE_CONTRATO');
			jn_query($updateStatus);
			
			$updateContrato  = ' UPDATE VND1002_ON SET ';
			$updateContrato .= ' 	DATA_ACEITE_CONTRATO = ' 	. dataToSql(date('d/m/Y')) . ',';
			$updateContrato .= ' 	HORA_ACEITE_CONTRATO = ' 	. aspas(date('H:i')) . ',';
			$updateContrato .= ' 	IP_ACEITE_CONTRATO = ' 		. aspas($_SESSION['IpUsuario']);
			$updateContrato .= ' WHERE CODIGO_ASSOCIADO = ' 	. aspas($rowAssocCont->CODIGO_ASSOCIADO);
			jn_query($updateContrato);	
			
			$updateStatus  = ' UPDATE VND1000_ON SET ULTIMO_STATUS = ' . aspas('AGUARDANDO_AVALIACAO');
			$updateStatus .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssocCont->CODIGO_ASSOCIADO);
			jn_query($updateStatus);
			
			$insertStatus  = ' INSERT INTO VND1000STATUS_ON (CODIGO_ASSOCIADO, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS, ';
			$insertStatus .= ' REMETENTE_STATUS, DESTINATARIO_STATUS) VALUES (';
			$insertStatus .= aspas($rowAssocCont->CODIGO_ASSOCIADO) . ',' . aspas('AGUARDANDO_AVALIACAO') . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas(date('H:i')) . ', ';
			$insertStatus .= aspas('BENEFICIARIO') . ',' . aspas('BENEFICIARIO') . ')';
		
			if(jn_query($insertStatus)){
				$contatoOperadora = retornaValorConfiguracao('EMAIL_CADASTRO');
				if($contatoOperadora != ''){
					enviarEmailContrato();
				}
				
				if(retornaValorConfiguracao('EFETIVAR_CADASTRO_VND_ACEITE') == 'SIM'){
					require_once('../services/efetivaAssociadosVendas.php');					
					importaTitular($codAssociadoTmp);					
				}

				$retorno['STATUS'] = 'OK';
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = 'Não foi possivel gravar o status CONTRATO';
			}
		}
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Nao foi possivel marcar o flag contrato aceito.';				
	}
	
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'configuracoes'){
	$retorno = Array();
	
	$retorno['LINK_SITE'] = retornaValorConfiguracao('LINK_SITE_INSTITUCIONAL');
	$retorno['POSSUI_ASSINATURA_CONTRATO'] = retornaValorConfiguracao('POSSUI_ASSINATURA_CONTRATO');
	$retorno['CAMINHO_AVISO_CONTRATO'] = retornaValorConfiguracao('CAMINHO_AVISO_CONTRATO');
	$retorno['APRESENTAR_DADOS_BANCARIOS'] = false;
	
	if($_SESSION['codigoSmart'] == 3389){//Vidamax		
		$queryModelo  = ' SELECT ';	
		$queryModelo .= ' 	CODIGO_MODELO ';
		$queryModelo .= ' FROM VND1002_ON ';		
		$queryModelo .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$resModelo = jn_query($queryModelo);
		$rowModelo = jn_fetch_object($resModelo);
		
		if($rowModelo->CODIGO_MODELO == 12){
			$retorno['APRESENTAR_DADOS_BANCARIOS'] = true;
		}

		if($rowModelo->CODIGO_MODELO == 36){
			$retorno['APRESENTAR_DADOS_CAAPSML'] = true;
		}
		
	}
	
			
	$queryConfigDec  = ' SELECT COALESCE(FLAG_EXIGIR_DECL_SAUDE, "N") AS FLAG_EXIGIR_DECL_SAUDE FROM VND1030CONFIG_ON ';
	$queryConfigDec .= ' WHERE CODIGO_PLANO IN (SELECT VND1000_ON.CODIGO_PLANO FROM VND1000_ON WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp) . ') ';			
	$resConfigDec = jn_query($queryConfigDec);
	$rowConfigDec = jn_fetch_object($resConfigDec);	
	
	if($rowConfigDec->FLAG_EXIGIR_DECL_SAUDE == 'S' or $_SESSION['perfilOperador'] == 'VENDEDOR' or $_SESSION['perfilOperador'] == 'OPERADOR'){
		$retorno['CAMINHO_AVISO_CONTRATO'] = '';
	}
	
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'assinatura'){
	$retorno = Array();
	$email = '';
	$telefone = '';
	$nomeAssociado = '';
	$numeroCPF = '';
	$aniversario = '';
	$vendedor = '';	
	$chaveClickSign = retornaValorConfiguracao('CHAVE_CLICKSIGN');
	$homolClickSign = retornaValorConfiguracao('HOMOLOGACAO_CLICKSIGN');
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	NOME_ASSOCIADO, NUMERO_CPF, NUMERO_RG, DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';	
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssociado = jn_query($queryAssociado);
	$rowAssociado = jn_fetch_object($resAssociado);
			
	$email = $rowAssociado->ENDERECO_EMAIL;
	$telefone = $rowAssociado->NUMERO_TELEFONE_01;
	$nomeAssociado = jn_utf8_encode($rowAssociado->NOME_ASSOCIADO);
	$numeroCPF = $rowAssociado->NUMERO_CPF;	
	$vendedor = $rowAssociado->CODIGO_VENDEDOR;	
	$aniversario = SqlToData($rowAssociado->DATA_NASCIMENTO);		
	
	$queryContrato  = ' SELECT ';	
	$queryContrato .= ' 	A.CODIGO_MODELO, TITULO_MODELO, TEXTO_CONTRATO, DESCRICAO_MODELO, NOME_ARQUIVO ';
	$queryContrato .= ' FROM VND1002_ON A ';	
	$queryContrato .= ' INNER JOIN VND1030MODELOS_ON B ON (A.CODIGO_MODELO = B.CODIGO_MODELO) ';
	$queryContrato .= ' WHERE A.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resContrato = jn_query($queryContrato);

	$contratos = Array();
	$i = 0;
	while($rowContrato = jn_fetch_object($resContrato)){
		$contratos[$i]['titulo'] = jn_utf8_encode($rowContrato->TITULO_MODELO);
		$contratos[$i]['codigoModelo'] = jn_utf8_encode($rowContrato->CODIGO_MODELO);
		$contratos[$i]['descricaoModelo'] = jn_utf8_encode($rowContrato->DESCRICAO_MODELO);
		$contratos[$i]['linkArquivo'] = $rowContrato->NOME_ARQUIVO;	
			
		$i++;
	}	
	
	/*inicia criação signatario*/
	$headers = array("Content-Type: application/json","Accept:application/json");
	
	if($homolClickSign == 'SIM'){		
		$url = 'https://sandbox.clicksign.com/api/v1/signers?access_token=' . $chaveClickSign;
	}else{		
		$url = 'https://app.clicksign.com/api/v1/signers?access_token=' . $chaveClickSign;
	}
	
	$data = '{
			  "signer": {
				"email": "' . $email . '",
				"phone_number": "' . $telefone . '",
				"auths": [
				  "email"
				],
				"name": "' . $nomeAssociado . '",
				"documentation": "' . $numeroCPF . '",
				"birthday": "' . $aniversario . '",
				"has_documentation": true
			  }
			}';
	
	$ch = CURL_INIT();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);
	$body = json_decode($body);	
	$chaveSignatario = $body->signer->key;
	//pr(' Chave Signatario - ' . $chaveSignatario);	
	/*fim criação signatario*/
	
	
	/*Inicia criação do documento*/
		
	$headers = array("Content-Type: application/json","Accept:application/json");	
	
	if($homolClickSign == 'SIM'){		
		$url = 'https://sandbox.clicksign.com/api/v1/documents?access_token=' . $chaveClickSign;	
	}else{		
		$url = 'https://app.clicksign.com/api/v1/documents?access_token=' . $chaveClickSign;	
	}
	
	$caminho = explode('https://vidamax.com.br/AliancaAppNet2/ServidorCliente/', $contratos[0]['linkArquivo']);
	$caminho = str_replace ('/','\\',$caminho[1]);
	$path = 'https://vidamax.com.br/AliancaAppNet2/ServidorCliente/' . $caminho;		
	$path = str_replace ('/','\\',$path);
	$type = pathinfo($path, PATHINFO_EXTENSION);
	
	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	); 
	
	$data = file_get_contents($contratos[0]['linkArquivo'], false, stream_context_create($arrContextOptions));
	//$data = file_get_contents($contratos[0]['linkArquivo']);	
	$base64 = 'data:application/pdf;base64,' . base64_encode($data);
	$deadline_at = date('Y-m-d', strtotime('+5 days')) . 'T14:30:59-03:00';
	
	$data = '{
			  "document": {
				"filename": "Contrato Vidamax - ' . $nomeAssociado . '.pdf",
				"path": "/' . $path . '",
				"content_base64": "' . $base64 . '",
				"deadline_at": "' . $deadline_at . '",
				"auto_close": true,
				"locale": "pt-BR",
				"sequence_enabled": false
			  }
			}';
	
	$ch = CURL_INIT();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);
	$body = json_decode($body);
	$chaveDocumento = $body->document->key;	
	
	jn_query('UPDATE VND1002_ON SET CHAVE_DOC_CLICKSIGN = ' . aspas($chaveDocumento) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp)); 	
	//pr(' Chave Documento - ' . $chaveDocumento);	
	/*Fim criação do documento*/
	

	
	/*Inicia vinculo usuário e documento*/
	
	$headers = array("Content-Type: application/json","Accept:application/json");
	
	if($homolClickSign == 'SIM'){		
		$url = 'https://sandbox.clicksign.com/api/v1/lists?access_token=' . $chaveClickSign;	
	}else{		
		$url = 'https://app.clicksign.com/api/v1/lists?access_token=' . $chaveClickSign;
	}
	
	$data = '{
			  "list": {
				"document_key": "' . $chaveDocumento . '",
				"signer_key": "' . $chaveSignatario . '",
				"sign_as": "sign"
			  }
			}';
	
	$ch = CURL_INIT();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);
	$body = json_decode($body);
	$chaveVinculo = $body->list->request_signature_key;
	//pr(' Chave Lista (request_signature_key) - ' . $chaveVinculo);	
	/*Fim vinculo usuário e documento*/
	
	/*Inicia notificação*/
	
	$headers = array("Content-Type: application/json","Accept:application/json");
	
	if($homolClickSign == 'SIM'){
		$url = 'https://sandbox.clicksign.com/api/v1/notifications?access_token=' . $chaveClickSign;
	}else{		
		$url = 'https://app.clicksign.com/api/v1/notifications?access_token=' . $chaveClickSign;
	}
	
	$data = '{
			  
				"request_signature_key": "' . $chaveVinculo . '",
				"message": "Prezado,\n Por favor assine o documento. \n Atenciosamente,\n Vidamax "
			  
			}';	
	
	$ch = CURL_INIT();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);
	$body = json_decode($body);
	
	/*Fim notificação*/
	
	
	$queryConfContrato  = ' SELECT COALESCE(B.FLAG_IGNORAR_ASS_VENDEDOR,"N") AS FLAG_IGNORAR_ASS_VENDEDOR FROM VND1002_ON A ';	
	$queryConfContrato .= ' INNER JOIN VND1030MODELOS_ON B ON (A.CODIGO_MODELO = B.CODIGO_MODELO) ';
	$queryConfContrato .= ' WHERE A.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resConfContrato = jn_query($queryConfContrato);
	$rowConfContrato = jn_fetch_object($resConfContrato);
	
	if($rowConfContrato->FLAG_IGNORAR_ASS_VENDEDOR != 'S'){

		/*Inicio Chave Vendedor*/

		$queryVendedor  = ' SELECT PS1100.NOME_COMPLETO, PS1101.NUMERO_CELULAR, PS1101.ENDERECO_EMAIL, PS1102.NUMERO_CPF, PS1102.DATA_NASCIMENTO ';
		$queryVendedor .= ' FROM PS1100 ';
		$queryVendedor .= ' INNER JOIN PS1101 ON PS1100.CODIGO_IDENTIFICACAO = PS1101.CODIGO_IDENTIFICACAO ';
		$queryVendedor .= ' INNER JOIN PS1102 ON PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO ';		
		$queryVendedor .= ' WHERE PS1100.CODIGO_IDENTIFICACAO = ' . aspas($vendedor);
		$resVendedor = jn_query($queryVendedor);
		$rowVendedor = jn_fetch_object($resVendedor);
		$nomeVendedor = jn_utf8_encode($rowVendedor->NOME_COMPLETO);
		$emailVendedor = $rowVendedor->ENDERECO_EMAIL;
		$cpfVendedor = $rowVendedor->NUMERO_CPF;
		$telefoneVendedor = $rowVendedor->NUMERO_CELULAR;
		$aniversarioVendedor = SqlToData($rowVendedor->DATA_NASCIMENTO);
		
		$headers = array("Content-Type: application/json","Accept:application/json");
		
		if($homolClickSign == 'SIM'){	
			$url = 'https://sandbox.clicksign.com/api/v1/signers?access_token=' . $chaveClickSign;
		}else{
			$url = 'https://app.clicksign.com/api/v1/signers?access_token=' . $chaveClickSign;
		}
		
		$data = '{
				  "signer": {
					"email": "' . $emailVendedor . '",
					"phone_number": "' . $telefoneVendedor . '",
					"auths": [
					  "email"
					],
					"name": "' . $nomeVendedor . '",
					"documentation": "' . $cpfVendedor . '",
					"birthday": "' . $aniversarioVendedor . '",
					"has_documentation": true
				  }
				}';
		
		$ch = CURL_INIT();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);	
		$start = $info['header_size'];
		$body = substr($result, $start, strlen($result) - $start);
		curl_close($ch);
		$body = json_decode($body);	
		$chaveSignatarioVend = $body->signer->key;			
		
		/*Fim Chave Vendedor*/
			
		/*Inicia vinculo vendedor e documento*/

		$headers = array("Content-Type: application/json","Accept:application/json");
		
		if($homolClickSign == 'SIM'){
			$url = 'https://sandbox.clicksign.com/api/v1/lists?access_token=' . $chaveClickSign;
		}else{		
			$url = 'https://app.clicksign.com/api/v1/lists?access_token=' . $chaveClickSign;
		}
		
		$data = '{
				  "list": {
					"document_key": "' . $chaveDocumento . '",
					"signer_key": "' . $chaveSignatarioVend . '",
					"sign_as": "sign"
				  }
				}';
		
		$ch = CURL_INIT();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);	
		$start = $info['header_size'];
		$body = substr($result, $start, strlen($result) - $start);
		curl_close($ch);
		$body = json_decode($body);
		$chaveVinculoVend = $body->list->request_signature_key;
		
		/*Fim vinculo vendedor e documento*/
		
		/*Inicia notificação Vendedor*/

		$headers = array("Content-Type: application/json","Accept:application/json");
		
		if($homolClickSign == 'SIM'){		
			$url = 'https://sandbox.clicksign.com/api/v1/notifications?access_token=' . $chaveClickSign;
		}else{		
			$url = 'https://app.clicksign.com/api/v1/notifications?access_token=' . $chaveClickSign;
		}
		
		$data = '{
				  
					"request_signature_key": "' . $chaveVinculoVend . '",
					"message": "Prezado,\n Por favor assine o documento. \n Atenciosamente,\n Vidamax "
				  
				}';	
		
		$ch = CURL_INIT();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);	
		$start = $info['header_size'];
		$body = substr($result, $start, strlen($result) - $start);
		curl_close($ch);
		$body = json_decode($body);
		
		/*Fim notificação vendedor*/
		
	}
	
	$retorno = Array();
	if(jn_query('UPDATE VND1002_ON SET FLAG_CONTRATO_ACEITO = "S" WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp))){
		
		$queryAssocCont = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$resAssocCont = jn_query($queryAssocCont);
		while($rowAssocCont = jn_fetch_object($resAssocCont)){			
			$updateStatus  = ' UPDATE VND1000STATUS_ON SET TIPO_STATUS = ' . aspas('CONTRATO_OK');
			$updateStatus .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssocCont->CODIGO_ASSOCIADO);
			$updateStatus .= ' AND TIPO_STATUS = ' . aspas('AGUARDANDO_ACEITE_CONTRATO');
			jn_query($updateStatus);
		
			$updateStatus  = ' UPDATE VND1000_ON SET ULTIMO_STATUS = ' . aspas('AGUARDANDO_AVALIACAO');
			$updateStatus .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssocCont->CODIGO_ASSOCIADO);
			jn_query($updateStatus);
			
			$insertStatus  = ' INSERT INTO VND1000STATUS_ON (CODIGO_ASSOCIADO, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS, ';
			$insertStatus .= ' REMETENTE_STATUS, DESTINATARIO_STATUS) VALUES (';
			$insertStatus .= aspas($rowAssocCont->CODIGO_ASSOCIADO) . ',' . aspas('AGUARDANDO_AVALIACAO') . ', current_timestamp, ' . aspas(date('H:i')) . ', ';
			$insertStatus .= aspas('BENEFICIARIO') . ',' . aspas('BENEFICIARIO') . ')';
		
			if(jn_query($insertStatus)){
				$retorno['STATUS'] = 'OK';
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = 'Não foi possivel gravar o status CONTRATO';
			}
		}
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Nao foi possivel marcar o flag contrato aceito.';				
	}	
	
	if($retorno['STATUS'] == 'OK'){		
		$retorno['MSG']  = jn_utf8_encode('Para dar continuidade no processo de assinatura, favor acessar o e-mail: ' . $email);	
	}	

	echo json_encode($retorno);
}

function enviarEmailContrato(){
	$codAssociado = $_SESSION['codigoIdentificacao'];
	$url = retornaValorConfiguracao('LINK_PASTA_CONTRATOS') . 'ServidorAl2/EstruturaPrincipal/disparoEmail.php?codigoModelo=10&codigoAssociado='.$codAssociado;
			
	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);
}

if($_GET['tipo'] == 'arquivoAssinado'){
	$codigoAssociado = $_GET['codAssociado'];
	
	$queryDoc = 'SELECT CHAVE_DOC_CLICKSIGN FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado); 
	$resDoc = jn_query($queryDoc);
	$rowDoc = jn_fetch_object($resDoc);
	$chaveDocumento = $rowDoc->CHAVE_DOC_CLICKSIGN;
	
	$chaveClickSign = retornaValorConfiguracao('CHAVE_CLICKSIGN');
	$homolClickSign = retornaValorConfiguracao('HOMOLOGACAO_CLICKSIGN');
	$headers = array("Content-Type: application/json","Accept:application/json");
	
	if($homolClickSign == 'SIM'){
		$url = 'https://sandbox.clicksign.com/api/v1/documents/'.$chaveDocumento.'?access_token=' . $chaveClickSign;
	}else{		
		$url = 'https://app.clicksign.com/api/v1/documents/'.$chaveDocumento.'?access_token=' . $chaveClickSign;
	}
	
	$data = '{}';	
	
	$ch = CURL_INIT();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);
	$body = json_decode($body);
	
	pr($body->downloads->signed_file_url);	
}


if($dadosInput['tipo']== 'salvarDadosBancarios'){			
	jn_query('	UPDATE VND1001_ON SET 
					CODIGO_BANCO = ' . aspas($dadosInput['dadosBancarios'][0]['CODIGO_BANCO']) . ',
					NUMERO_AGENCIA = ' . aspas($dadosInput['dadosBancarios'][0]['NUMERO_AGENCIA']) . ',
					NUMERO_CONTA = ' . aspas($dadosInput['dadosBancarios'][0]['NUMERO_CONTA']) . ',
					FLAG_DEBITO_AUTOMATICO = ' . aspas('S') . '
				WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
}

?>