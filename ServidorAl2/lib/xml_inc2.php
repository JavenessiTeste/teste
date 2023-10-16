<?php

function getDadosXml($nomeXml){
	
	
	libxml_use_internal_errors(true);
	
	$xml = new DOMDocument();
	
	$xml->formatOutput=true;
	$xml->enconding='ISO-8859-1';
	$xml->load($nomeXml);

	$dados = $xml->getElementsByTagName('versaoPadrao');

	$versao = $dados->item(0)->nodeValue;
	
	if(!$versao){
		$dados = $xml->getElementsByTagName('Padrao');
		$versao = $dados->item(0)->nodeValue;
	}
	
	

	if($versao){
	
		$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
		$dados = $dados->getElementsByTagName('cabecalho')->item(0);
		$dados = $dados->getElementsByTagName('identificacaoTransacao')->item(0);
		$tipoTransacao = $dados->getElementsByTagName('tipoTransacao')->item(0)->nodeValue;

		
		$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
		$dados = $dados->getElementsByTagName('prestadorParaOperadora')->item(0);
		$dados = $dados->getElementsByTagName('loteGuias')->item(0);
		$numeroLote = $dados->getElementsByTagName('numeroLote')->item(0)->nodeValue;

		$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
		$dados = $dados->getElementsByTagName('cabecalho')->item(0);
		$dados = $dados->getElementsByTagName('origem')->item(0);				
		
		$versaoCompara = substr($versao,0,1);
		
		if($versaoCompara == "3" || $versaoCompara == "4")	
			$dados = $dados->getElementsByTagName('identificacaoPrestador')->item(0);
		else		
			$dados = $dados->getElementsByTagName('codigoPrestadorNaOperadora')->item(0);
		
		
		$cnpj = $dados->getElementsByTagName('CNPJ')->item(0)->nodeValue;

		$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
		$dados = $dados->getElementsByTagName('cabecalho')->item(0);
		$dados = $dados->getElementsByTagName('origem')->item(0);
		
		if($versaoCompara == "3" || $versaoCompara == "4")
			$dados = $dados->getElementsByTagName('identificacaoPrestador')->item(0);
		else
			$dados = $dados->getElementsByTagName('codigoPrestadorNaOperadora')->item(0);
		
		$codigoPrestadorNaOperadora = $dados->getElementsByTagName('codigoPrestadorNaOperadora')->item(0)->nodeValue;

		$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
		$dados = $dados->getElementsByTagName('cabecalho')->item(0);
		$dados = $dados->getElementsByTagName('origem')->item(0);

		if($versaoCompara == "3" || $versaoCompara == "4")	
			$dados = $dados->getElementsByTagName('identificacaoPrestador')->item(0);
		else
			$dados = $dados->getElementsByTagName('codigoPrestadorNaOperadora')->item(0);
		
		
		if($versao == "2.01.02"){
			$cpf = $dados->getElementsByTagName('cpf')->item(0)->nodeValue;
		}else{
			$cpf = $dados->getElementsByTagName('CPF')->item(0)->nodeValue;
		}
				
		$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
		$dados = $dados->getElementsByTagName('cabecalho')->item(0);
		$dados = $dados->getElementsByTagName('identificacaoTransacao')->item(0);
		$sequencialTransacao = $dados->getElementsByTagName('sequencialTransacao')->item(0)->nodeValue;
		
		$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
		$dados = $dados->getElementsByTagName('epilogo')->item(0);
		$hashArquivo = $dados->getElementsByTagName('hash')->item(0)->nodeValue;

		$versao = str_replace('.','_',$versao);
		
		if($_SESSION['codigoSmart'] == '3423' || retornaValorConfiguracao('VALIDAR_REGRAS_XML') == 'SIM'){
			
			$xml->load($nomeXml);		
			$i = 0;
			$associadoExcluido = '';
			$erroEncontrado = false;
			
			
			global $arquivo_name;
			global $mesAnoCompetencia;
			
			$numeroRegistroPS5297 = jn_gerasequencial('PS5297');
			$dataAtualBD = 'CURRENT_TIMESTAMP ';
			
			if(($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv'))
				$dataAtualBD = 'GETDATE() ';							
			

			$insertErro = '';

			if((($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv')) and (retornaValorConfiguracao('IDENTITY_PS5797') != 'NAO'))
				$insertErro .= ' SET IDENTITY_INSERT PS5297 ON ';

			$insertErro .= ' INSERT INTO PS5297 (NUMERO_REGISTRO, NOME_ARQUIVO, CODIGO_PRESTADOR, MES_ANO_COMPETENCIA, DATA_ENVIO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas($arquivo_name) . ',' . $_SESSION['codigoIdentificacao'] . ',' . aspas($mesAnoCompetencia) . ', ' . $dataAtualBD . '); ';			
			
			if((($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv')) and (retornaValorConfiguracao('IDENTITY_PS5797') != 'NAO'))
				$insertErro .= ' SET IDENTITY_INSERT PS5297 OFF ';

			jn_query($insertErro);
			
			
			for($i = 0; $i < 100; $i++){
				if($xml->getElementsByTagName('numeroCarteira')->item($i)){
					$queryAssoc  = ' SELECT CODIGO_ASSOCIADO, DATA_EXCLUSAO FROM PS1000 ';
					$queryAssoc .= ' WHERE DATA_EXCLUSAO IS NOT NULL AND DATA_EXCLUSAO < CURRENT_TIMESTAMP AND CODIGO_ASSOCIADO = ' . aspas($xml->getElementsByTagName('numeroCarteira')->item($i)->nodeValue);
					$resAssoc = jn_query($queryAssoc);
					$rowAssoc = jn_fetch_object($resAssoc);
					
					
					if($rowAssoc->CODIGO_ASSOCIADO){												  
						$dataExc     = SqlToData($rowAssoc->DATA_EXCLUSAO);
						$dataExc     = explode("/",$dataExc); 
						$dataExc    = mktime(0,0,0,$dataExc[1],$dataExc[0],$dataExc[2]);						
						$dataAut     = SqlToData($xml->getElementsByTagName('dataAutorizacao')->item($i)->nodeValue);

						if($dataAut == ''){
							$dataAut     = SqlToData($xml->getElementsByTagName('dataAtendimento')->item($i)->nodeValue);
						}
						
						$validaDias = 0;
						
						if($dataAut != ''){
							$dataAut     = explode("/",$dataAut); 
							
							$dataAut    = mktime(0,0,0,$dataAut[1],$dataAut[0],$dataAut[2]);						
							$dias       = ($dataAut-$dataExc)/86400;
							$validaDias = ceil($dias);
						}
						
						if($validaDias > 0){
							$associadoExcluido = $xml->getElementsByTagName('numeroCarteira')->item($i)->nodeValue;
							$numeroGuia = $xml->getElementsByTagName('numeroGuiaPrestador')->item($i)->nodeValue;
							$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- O associado ' . $associadoExcluido . ' esta excluido, portanto, o arquivo nao será aceito.') . '); ';
							jn_query($insertPS5298);
							$erroEncontrado = true;
						}
					}
					
					
					$queryAssoc  = ' SELECT CODIGO_ASSOCIADO, DATA_EXCLUSAO FROM PS1000 ';
					$queryAssoc .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($xml->getElementsByTagName('numeroCarteira')->item($i)->nodeValue);
					$resAssoc = jn_query($queryAssoc);
					$rowAssoc = jn_fetch_object($resAssoc);
					
					if(!$rowAssoc->CODIGO_ASSOCIADO){
						$associadoInexistente = $xml->getElementsByTagName('numeroCarteira')->item($i)->nodeValue;
						$numeroGuia = $xml->getElementsByTagName('numeroGuiaPrestador')->item($i)->nodeValue;
						$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- O associado ' . $associadoInexistente . ' nao esta cadastrado no sistema.') . '); ';			
						jn_query($insertPS5298);
						$erroEncontrado = true;
					}
					
					$queryAssoc  = ' SELECT CODIGO_ASSOCIADO FROM PS1000 ';
					$queryAssoc .= ' WHERE CODIGO_TIPO_CARACTERISTICA = 10 AND CODIGO_ASSOCIADO = ' . aspas($xml->getElementsByTagName('numeroCarteira')->item($i)->nodeValue);
					$resAssoc = jn_query($queryAssoc);
					$rowAssoc = jn_fetch_object($resAssoc);
					
					if($rowAssoc->CODIGO_ASSOCIADO){
						$associadoOdonto = $xml->getElementsByTagName('numeroCarteira')->item($i)->nodeValue;
						$numeroGuia = $xml->getElementsByTagName('numeroGuiaPrestador')->item($i)->nodeValue;
						$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- O associado ' . $associadoOdonto . ' esta vinculado ao plano odontológico.') . '); ';			
						jn_query($insertPS5298);
						$erroEncontrado = true;
					}
					
					
				}else{
					break;
				}
				
			}
			
			for($i = 0; $i < 10000; $i++){
				if($xml->getElementsByTagName('codigoProcedimento')->item($i) != '' and $xml->getElementsByTagName('codigoTabela')->item($i) == '22'){					
					
					$queryProc  = ' SELECT CODIGO_PROCEDIMENTO FROM PS5210 ';
					$queryProc .= ' WHERE CODIGO_PROCEDIMENTO = ' . aspas($xml->getElementsByTagName('codigoProcedimento')->item($i)->nodeValue);
					
					$resProc = jn_query($queryProc);
					$rowProc = jn_fetch_object($resProc);
					
					if(!$rowProc->CODIGO_PROCEDIMENTO){
						$produtoNaoContratado = $xml->getElementsByTagName('codigoProcedimento')->item($i)->nodeValue;
						$numeroGuia = $xml->getElementsByTagName('numeroGuiaPrestador')->item($i)->nodeValue;
						$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- O procedimento ' . $produtoNaoContratado . ' nao esta cadastrado na base de dados.') . '); ';			
						jn_query($insertPS5298);
						$erroEncontrado = true;
					}
					
					
				}else{
					break;
				}
				
			}
			
			
			$tipoGuia = '';
			if($xml->getElementsByTagName('guiaSP-SADT')->item(0) != null){
				$tipoGuia = 'SADT';
			}elseif($xml->getElementsByTagName('guiaResumoInternacao')->item(0) != null){
				$tipoGuia = 'INTERNACAO';				
			}
			
			for($i = 0; $i < 10000; $i++){
				
				if($tipoGuia == 'SADT'){
					$guia = $xml->getElementsByTagName('guiaSP-SADT')->item($i);					
				}elseif($tipoGuia == 'INTERNACAO'){
					$guia = $xml->getElementsByTagName('guiaResumoInternacao')->item($i);					
				}else{
					$guia = null;
				}
				
				if($guia == null){
					break;
				}				
				
				if($guia->getElementsByTagName('numeroGuiaOperadora')->item(0)->nodeValue > 0 || $guia->getElementsByTagName('numeroGuiaPrestador')->item(0)->nodeValue > 0){
					$dataAtual = date('d/m/Y');
					$dataAtual     = explode("/",$dataAtual);
					$dataAtual    = mktime(0,0,0,$dataAtual[1],$dataAtual[0],$dataAtual[2]);
					$dataInternacao = '';

					if($tipoGuia == 'INTERNACAO'){
						$dataInternacao = SqlToData($guia->getElementsByTagName('dataFinalFaturamento')->item(0)->nodeValue);
					}

					$dataExe     = SqlToData($guia->getElementsByTagName('dataExecucao')->item(0)->nodeValue);

					if($dataExe == ''){
						$dataExe     = SqlToData($guia->getElementsByTagName('dataAutorizacao')->item(0)->nodeValue);
					}

					if($dataExe == ''){
						$dataExe     = SqlToData($guia->getElementsByTagName('dataAtendimento')->item(0)->nodeValue);
					}
					
					if($dataInternacao != ''){						
						$dataInternacaoValid    = explode("/",$dataInternacao);
						$dataInternacaoValid    = mktime(0,0,0,$dataInternacaoValid[1],$dataInternacaoValid[0],$dataInternacaoValid[2]);	

						$dataExeValid    = explode("/",$dataExe);
						$dataExeValid    = mktime(0,0,0,$dataExeValid[1],$dataExeValid[0],$dataExeValid[2]);	
						$diasValid       = ($dataInternacaoValid - $dataExeValid)/86400;
						
						if($diasValid > 0){
							$dataExe = $dataInternacao;
						}
					}

					$validaDias = 0;

					if($dataExe != ''){						
						$dataExe     = explode("/",$dataExe);
						$dataExe    = mktime(0,0,0,$dataExe[1],$dataExe[0],$dataExe[2]);						
						$dias       = ($dataAtual - $dataExe)/86400;
						$validaDias = ceil($dias);
					}
							
					$quantDias = 60;
					if(retornaValorConfiguracao('QUANT_DIAS_VALIDAR_XML') > 0){
						$quantDias = retornaValorConfiguracao('QUANT_DIAS_VALIDAR_XML');
					}

					if($validaDias > $quantDias){
						$numeroGuia = $guia->getElementsByTagName('numeroGuiaPrestador')->item(0)->nodeValue;					
						
						$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- Procedimento realizado fora do prazo aceito pela operadora, portanto, o arquivo nao sera aceito.') . '); ';
						jn_query($insertPS5298);
						$erroEncontrado = true;
					}					
					
				}else{
					break;
				}
				
			}
						
			if(retornaValorConfiguracao('VALIDAR_PROC_PREST_XML') == 'SIM'){
				for($i = 0; $i < 10000; $i++){
					if($xml->getElementsByTagName('codigoProcedimento')->item($i)){					
						
						$queryProcPrest  = ' SELECT CODIGO_PROCEDIMENTO_INICIAL FROM PS5005 ';
						$queryProcPrest .= ' WHERE CODIGO_PROCEDIMENTO_INICIAL = ' . aspas($xml->getElementsByTagName('codigoProcedimento')->item($i)->nodeValue);
						$queryProcPrest .= ' AND CODIGO_PRESTADOR = ' . aspas($codigoPrestadorNaOperadora);
						$resProcPrest = jn_query($queryProcPrest);
						$rowProcPrest = jn_fetch_object($resProcPrest);
						if(!$rowProcPrest->CODIGO_PROCEDIMENTO_INICIAL){
							$produtoNaoContratado = $xml->getElementsByTagName('codigoProcedimento')->item($i)->nodeValue;
							$numeroGuia = $xml->getElementsByTagName('numeroGuiaPrestador')->item($i)->nodeValue;						
							$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- O procedimento ' . $produtoNaoContratado . ' nao esta parametrizado para este prestador.') . ') ';			
							jn_query($insertPS5298);
							$erroEncontrado = true;
						}
						
						
					}else{
						break;
					}			
				}	
			}

			if(retornaValorConfiguracao('VALIDAR_AUTORIZACAO_XML') == 'SIM'){
				for($i = 0; $i < 10000; $i++){
					if($xml->getElementsByTagName('senha')->item($i)){					
						
						$queryAutoriz  = ' SELECT NUMERO_AUTORIZACAO FROM PS6500 ';						
						$queryAutoriz .= ' WHERE NUMERO_AUTORIZACAO = ' . aspas($xml->getElementsByTagName('senha')->item($i)->nodeValue);
						$queryAutoriz .= ' AND CODIGO_PRESTADOR = ' . aspas($codigoPrestadorNaOperadora);
						$queryAutoriz .= ' AND CODIGO_ASSOCIADO = ' . aspas($xml->getElementsByTagName('numeroCarteira')->item($i)->nodeValue);
						$resAutoriz = jn_query($queryAutoriz);
						$rowAutoriz = jn_fetch_object($resAutoriz);
						
						if(!$rowAutoriz->NUMERO_AUTORIZACAO){
							$autNaoEncontrada = $xml->getElementsByTagName('senha')->item($i)->nodeValue;
							$numeroGuia = $xml->getElementsByTagName('numeroGuiaPrestador')->item($i)->nodeValue;
							$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- A autorizacao ' . $autNaoEncontrada . ' nao foi encontrada.') . '); ';			
							jn_query($insertPS5298);
							$erroEncontrado = true;
						}
						
						
					}else{
						break;
					}			
				}	
			}


			if(retornaValorConfiguracao('VALIDAR_DT_EXECUCAO_XML') == 'SIM' and retornaValorConfiguracao('VALIDAR_REGRAS_XML') != 'SIM'){

				for($i = 0; $i < 10000; $i++){

					if(SqlToData($guia->getElementsByTagName('dataExecucao')->item(0)->nodeValue)){
						$dataAtual = date('d/m/Y');
						$dataAtual = explode("/",$dataAtual);
						$dataAtual = mktime(0,0,0,$dataAtual[1],$dataAtual[0],$dataAtual[2]);


						$dataExe = SqlToData($guia->getElementsByTagName('dataExecucao')->item(0)->nodeValue);										
						$dataExeValid    = explode("/",$dataExe);
						$dataExeValid    = mktime(0,0,0,$dataExeValid[1],$dataExeValid[0],$dataExeValid[2]);	
						
						
						$diasValid       = ($dataExeValid - $dataAtual)/86400;
						
						if($diasValid > 180){	

							global $mesAnoCompetencia;
							$numeroRegistroPS5297 = jn_gerasequencial('PS5297');
							$dataAtualBD = 'CURRENT_TIMESTAMP ';
							
							if(($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv'))
								$dataAtualBD = 'GETDATE() ';

							$insertErro = '';

							if((($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv')) and (retornaValorConfiguracao('IDENTITY_PS5797') != 'NAO'))
								$insertErro .= ' SET IDENTITY_INSERT PS5297 ON ';

							$insertErro .= ' INSERT INTO PS5297 (NUMERO_REGISTRO, NOME_ARQUIVO, CODIGO_PRESTADOR, MES_ANO_COMPETENCIA, DATA_ENVIO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas($arquivo_name) . ',' . $_SESSION['codigoIdentificacao'] . ',' . aspas($mesAnoCompetencia) . ', ' . $dataAtualBD . '); ';			
							
							if((($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv')) and (retornaValorConfiguracao('IDENTITY_PS5797') != 'NAO'))
								$insertErro .= ' SET IDENTITY_INSERT PS5297 OFF ';

							jn_query($insertErro);
							
							$numeroGuia = $xml->getElementsByTagName('numeroGuiaPrestador')->item($i)->nodeValue;
							$insertPS5298  = ' INSERT INTO PS5298 (NUMERO_REGISTRO_PS5297, DESCRICAO_ERRO) VALUES (' . aspas($numeroRegistroPS5297) . ',' .  aspas('Guia: ' . $numeroGuia . ' -- A data ' . $dataExe . ' nao foi aceita.') . '); ';			
							jn_query($insertPS5298);
							$erroEncontrado = true;
							
						}
					}
					
				}	

				
			}
			
		}
	}
	
	$msg    = getErrosXml();
	libxml_use_internal_errors(false);
	
	if($versao){
	return array(
					'versao' => $versao,
					'tipoTransacao' => $tipoTransacao,
					'numeroLote' => $numeroLote,
					'cnpj' => $cnpj,
					'codigoPrestadorNaOperadora' => $codigoPrestadorNaOperadora,
					'cpf' => $cpf,
					'sequencialTransacao' => $sequencialTransacao,
					'msg' => $msg,
					'associadoExcluido' => $associadoExcluido,
					'erroEncontrado' => $erroEncontrado,
					'hashArquivo' => $hashArquivo,
					'hashCalculado' => @hashTiss($xml)
						
				);
	}else{
		return false;
	}	
		
}

function validaXmlTiss($caminhoXml, $versao){
	
	libxml_use_internal_errors(true);	
	
	$xml = new DOMDocument();
	$xml->load($caminhoXml);
	
	
	
	if (!file_exists("../schemas/tissV$versao.xsd")){

		$msg    = '<b>Schema nao encontrado.</b>';
		$validado = false;	
	
	}else{
		if (!(@$xml->schemaValidate("../schemas/tissV$versao.xsd"))){
			$msg    = '<b>Arquivo inválido</b>';
			$msg    .= getErrosXml();
			$validado = false;
		}
		else{
			$msg    = '<b>Arquivo validado</b>';
			$validado = true;
		}
	}
	libxml_use_internal_errors(false);
	return array(
				'validado' => $validado,
				'msg' => $msg 
			);
	
}

function validacoesEspecificasDados($dados, $codigoprestadorLogado){

	if($dados['tipoTransacao']!='ENVIO_LOTE_GUIAS' or $dados['numeroLote'] == '')
		return 'Arquivo nao é de Faturamento, favor verificar.';
	if($dados['codigoPrestadorNaOperadora']!='' and $dados['codigoPrestadorNaOperadora'] != $codigoprestador){
		return "Código informado no arquivo: " . $dados['codigoPrestadorNaOperadora'] . "<br>" . "Código do Credenciado: " . $codigoprestadorLogado ;	
	}
	if($dados['hashArquivo'] != $dados['hashCalculado']){
		return "<br><b><font color=red>Hash Informado é diferente do Calculado.</font></b>";
    }                         
}

function validaNomeArquivo($nome, $dados){

}

function montarTipoErro($error){

	$return = "<br/>\n";
	
	switch ($error->level) {
	case LIBXML_ERR_WARNING:
		$return .= "<b>Atenção $error->code</b>: ";
		break;
	case LIBXML_ERR_ERROR:
		$return .= "<b>Erro $error->code</b>: ";
		break;
	case LIBXML_ERR_FATAL:
		$return .= "<b>Erro Fatal $error->code</b>: ";
		break;
	}
	
	$return .= trim($error->message);

	//$return .= " Linha : <b>$error->line</b>.\n";

	return $return;
	
}

function getErrosXml() {
	
	$msg = '';
	
	$errors = libxml_get_errors();
		foreach ($errors as $error) {
			$msg .= montarTipoErro($error);
	}
	libxml_clear_errors();
	return $msg;
}

function xmlToString($elemento) 
{ 
    $innerHTML = ""; 
    $children = $elemento->childNodes; 
    foreach ($children as $child) 
    { 
        $tmp_dom = new DOMDocument(); 
        $tmp_dom->appendChild($tmp_dom->importNode($child, true));
        $innerHTML.= trim($tmp_dom->saveHTML()); 

    } 
    return $innerHTML; 
} 

function xmlToString2($elemento) 
{ 
    $innerHTML = "áéíóú";
    $children = $elemento->childNodes;
    foreach ($children as $child)
    {
        $tmp_dom = new DOMDocument('1.0', 'ISO-8859-1');
		$tmp_dom->formatOutput=true;
		$tmp_dom->enconding='ISO-8859-1';
        $tmp_dom->appendChild($tmp_dom->importNode($child, true));
		
        $innerHTML.= trim($tmp_dom->saveHTML()); 				
    }
	
	//$enconding = Encoding::toISO8859($innerHTML);		
	$innerHTML = str_replace("áéíóú","",$innerHTML);				
	//pr($innerHTML,true);
	
    return $innerHTML; 
} 

function hashTiss($xml){
	$stringHash = "";
	
	$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
	$dados = $dados->getElementsByTagName('cabecalho')->item(0);

	$stringHash .= xmlToString($dados);
	$dados = $xml->getElementsByTagName('mensagemTISS')->item(0);
	
	$dados = $dados->getElementsByTagName('prestadorParaOperadora')->item(0);

	$stringHash .= xmlToString2($dados);
	
	$stringHash = trim(str_replace("\t", '',str_replace("\n", '',str_replace("\r", '',$stringHash))));
	$stringHash = preg_replace('/<.+?>/i', "\n", $stringHash);
	$stringHash = preg_replace('/^[ \t]*[\r\n]+/m', '', $stringHash); 
	$stringHash = str_replace("\n", '',$stringHash);				
	
	
	$stringHash = htmlentities($stringHash, ENT_QUOTES, "ISO-8859-1");
	$stringHash = html_entity_decode($stringHash);
	$stringHash = html_entity_decode($stringHash);	
	
	$stringHash = str_replace("&#039;","'",$stringHash);
	//pr($stringHash,true);
	$hashCalculado = md5($stringHash);	
	
	return strtoupper($hashCalculado);
}

function get_encoding ($input) {
    $encondings = array("ASCII", "UTF-8", "ISO-8859-1");
    $input_md5 = md5($input);
    foreach($encondings as $enconding) {
        $sample = iconv($enconding, $enconding, $input);
        if(md5($sample) == $input_md5) {
            return($enconding);
        }
    }
    return(false);
}

?>