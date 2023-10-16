<?php
require('../lib/base.php');
	//'944777b4f8d5b584003df9c41efce4d0'
	$diretorio = '../../NFSe';
	if (!is_dir($diretorio)) {
		mkdir($diretorio, 0755, true);
	}
	
	
	$hash = $_GET['id'];
	$producao = $_GET['p'];
	
	if($producao){
		$query = 'SELECT VALOR_COMPLEM_CONFIGURACAO FROM CFG0003 WHERE IDENTIFICADOR_CONFIGURACAO ='.aspas('LINK_NFSE_PRODUCAO_PINHAS');
		$res = jn_query($query);
		if ($row = jn_fetch_object($res)) {
			$url = $row->VALOR_COMPLEM_CONFIGURACAO;
		}
		$query = 'SELECT VALOR_CONFIGURACAO FROM CFG0003 WHERE IDENTIFICADOR_CONFIGURACAO ='.aspas('USUARIO_NFSE_PRODUCAO_PINHAS');
		$res = jn_query($query);
		if ($row = jn_fetch_object($res)) {
			$usuario = $row->VALOR_CONFIGURACAO;
		}
		$query = 'SELECT VALOR_CONFIGURACAO FROM CFG0003 WHERE IDENTIFICADOR_CONFIGURACAO ='.aspas('SENHA_NFSE_PRODUCAO_PINHAS');
		$res = jn_query($query);
		if ($row = jn_fetch_object($res)) {
			$senha = $row->VALOR_CONFIGURACAO;
		}
	}else{
		$query = 'SELECT VALOR_COMPLEM_CONFIGURACAO FROM CFG0003 WHERE IDENTIFICADOR_CONFIGURACAO ='.aspas('LINK_NFSE_HOMOLOGACAO_PINHAS');
		$res = jn_query($query);
		if ($row = jn_fetch_object($res)) {
			$url = $row->VALOR_COMPLEM_CONFIGURACAO;
		}
		$query = 'SELECT VALOR_CONFIGURACAO FROM CFG0003 WHERE IDENTIFICADOR_CONFIGURACAO ='.aspas('USUARIO_NFSE_HOMOLOGACAO_PINHAS');
		$res = jn_query($query);
		if ($row = jn_fetch_object($res)) {
			$usuario = $row->VALOR_CONFIGURACAO;
		}
		$query = 'SELECT VALOR_CONFIGURACAO FROM CFG0003 WHERE IDENTIFICADOR_CONFIGURACAO ='.aspas('SENHA_NFSE_HOMOLOGACAO_PINHAS');
		$res = jn_query($query);
		if ($row = jn_fetch_object($res)) {
			$senha = $row->VALOR_CONFIGURACAO;
		}	
	}


	$query = 'select * from PS1056 where HASH_LOCALIZACAO ='.aspas($hash);
	if ($res = jn_query($query)) {
         if ($row = jn_fetch_object($res)) {
			
			$conteudo = $row->XML_ENVIO;
			$fp = fopen($diretorio."/ENVIO_".$row->NOME_ARQUIVO_NFSE,"wb");
			fwrite($fp,$conteudo);
			fclose($fp);

		 }
	}


	
	$arquivoXml = curl_file_create(realpath($diretorio."/ENVIO_".$row->NOME_ARQUIVO_NFSE));
	$xmldata = array('xml'=>$arquivoXml);


	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url);    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Authorization: Basic '.base64_encode($usuario.':'.$senha),   
		'Content-Type: multipart/form-data'
		                                                        
	));          
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);

	$errors = curl_error($ch);                                                                                                            
	$resultado = curl_exec($ch);
	
	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$xml = simplexml_load_string($resultado, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	
	if($returnCode==200){
		
		$conteudo = $resultado;
		$fp = fopen($diretorio."/RETORNO_".$row->NOME_ARQUIVO_NFSE,"wb");
		fwrite($fp,$conteudo);
		fclose($fp);
		
		
		$update = 'UPDATE PS1056 set XML_RETORNO ='.aspas($resultado) .' where HASH_LOCALIZACAO ='.aspas($hash);
		$res = jn_query($update);
		
		if(trim($array['mensagem']['codigo'])=='00001 - Sucesso'){
			$update = 'UPDATE PS1056 set LINK_NFSE ='.aspas($array['link_nfse']) .',CODIGO_VERIFICACAO='.aspas($array['cod_verificador_autenticidade']).' where HASH_LOCALIZACAO ='.aspas($hash);
			$res = jn_query($update);
		}
		
		echo $array['mensagem']['codigo'];
	}else{
		echo 'Erro ao tentar conectar ao webservice';

	}	


?>

