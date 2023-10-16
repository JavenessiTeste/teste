<?php
require('../lib/base.php');
require('../private/autentica.php');

if($_POST['tipo'] =='enviar'){

	$extValida = false;	
    $arquivoGuias = isset($_FILES["arquivoGuias"]) ? $_FILES["arquivoGuias"] : FALSE;
	
	if($arquivoGuias){

		$tamanho = strlen($_POST['valorTotal']);
		if($tamanho > 2){
			$valor = sanitizeString(substr($_POST['valorTotal'], 0, $tamanho -2));
			$decimais = substr($_POST['valorTotal'], -2);
			$_POST['valorTotal'] = $valor . '.' . $decimais;
		}

		$arquivoGuias_name = $_FILES['arquivoGuias']['name'];
		$arquivoGuias_type = $_FILES['arquivoGuias']['type'];
		$arquivoGuias_size = $_FILES['arquivoGuias']['size'];
		$arquivoGuias_temp_name = $_FILES['arquivoGuias']['tmp_name'];
		$ext = explode('.',$arquivoGuias_name);	
		$ext = $ext[(count($ext)-1)];
		
		if(strtoupper($ext) != 'PDF' ){
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG'] = 'Apenas arquivos com extensão PDF são aceitos neste formulário.'; 
		}else{
			$extValida = true;
		}
		
		if($extValida){
			$nomefinalXML = strtolower( remove_caracteres( $arquivoGuias['name'] ) );
			
			if (isset($arquivoGuias))
			{
				
				$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');					
				$retornoImg = salvarImagem('CFGARQUIVOS_PROCESSOS_NET',$numeroRegistro,$arquivoGuias);

				$DataAtual = dataToSql(date('d/m/Y'));
				if($_SESSION['type_db'] == 'sqlsrv'){
					$DataAtual = 'getDate() ';
				}

				$nomeArquivo = 'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$retornoImg['id'];

				$Upload = array(
				   'Arquivo'   	=> $nomeArquivo,
				   'Data'      	=> $DataAtual,
				   'MesAno'  	=> $_POST['mesAnoReferencia'],
				   'Protocolo'  => $_POST['protocolo'],
				   'numeroNF'  	=> $_POST['numeroNF'],
				   'valorTotal' => $_POST['valorTotal']
				);

				$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
						(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_NF, VALOR_TOTAL, NUMERO_PROTOCOLO, MES_ANO)
						VALUES
						(' . aspas($numeroRegistro) . ',' . aspas('ARQUIVO_GUIAS') . ','. aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspasNull($Upload['numeroNF']) . ',' . aspasNull(str_replace(',', '.', $_POST['valorTotal'])) . ',' . aspas($Upload['Protocolo']) .','. aspas($Upload['MesAno']) . ')';
				jn_query($query);

				$queryCaminho  = ' SELECT	CAMINHO_ARQUIVO_ARMAZENADO || NOME_ARQUIVO_ARMAZENADO AS NOME_ARQUIVO FROM CONTROLE_ARQUIVOS ';
				$queryCaminho .= ' WHERE CONTROLE_ARQUIVOS.CHAVE_REGISTRO = ' . aspas($numeroRegistro);
				$queryCaminho .= ' AND CONTROLE_ARQUIVOS.NOME_TABELA = ' . aspas('CFGARQUIVOS_PROCESSOS_NET');
				$resCaminho 	= jn_query($queryCaminho);
				$rowCaminho = jn_fetch_object($resCaminho);

				$caminhoArquivo = retornaValorConfiguracao('CAMINHO_UPLOAD_ARQUIVOS');  
				$caminhoGuias = $caminhoArquivo . $rowCaminho->NOME_ARQUIVO;

				if($_SESSION['type_db'] =='sqlsrv'){
					$queryProc  = ' SELECT NUMERO_PROCESSAMENTO FROM ESP_PRAZOS_AG_AL2 ';
					$queryProc .= ' WHERE   DATA_INICIO <=  CONVERT(date, GETDATE()) ';
					$queryProc .= ' 	AND DATA_FINAL >=  CONVERT(date, GETDATE()) ';
					$queryProc .= ' 	AND NUMERO_PROCESSAMENTO IS NOT NULL ';
					$resProc 	= jn_query($queryProc);
					$rowProc = jn_fetch_object($resProc);
				}else{
					$queryProc  = ' SELECT NUMERO_PROCESSAMENTO FROM ESP_PRAZOS_AG_AL2 ';
					$queryProc .= " WHERE   DATA_INICIO <= CAST('TODAY' AS DATE) ";
					$queryProc .= " 	AND DATA_FINAL >= CAST('TODAY' AS DATE) ";
					$queryProc .= ' 	AND NUMERO_PROCESSAMENTO IS NOT NULL ';
					$resProc 	= jn_query($queryProc);
					$rowProc = jn_fetch_object($resProc);
				}


				if($_SESSION['type_db'] =='sqlsrv'){
					$queryEsp  = '	INSERT INTO ESP_NF_ARQ_GUIAS ';
					$queryEsp  .= ' 		(CODIGO_PRESTADOR, DATA_CADASTRO, DATA_ENVIO, MES_ANO_REFERENCIA, VALOR_TOTAL, TIPO_ARQUIVO, FLAG_BAIXADO, CAMINHO_ARQUIVO, NUMERO_PROCESSAMENTO, NUMERO_PROTOCOLO) VALUES ';
					$queryEsp  .= '		(' . aspas($_SESSION['codigoIdentificacao'])  . ', CONVERT(date, GETDATE()), CONVERT(date, GETDATE()), ' . aspas($_POST['mesAnoReferencia']) . ', ' . aspasNull(str_replace(',', '.', $_POST['valorTotal'])) . ', ';
					$queryEsp  .= '		 ' . aspas('AG') . ', ' . aspas('N') . ', ' . aspas($caminhoGuias) . ', ' . aspas($rowProc->NUMERO_PROCESSAMENTO) . ', ' . aspas($_POST['protocolo']) . ')';
				}else{
					$queryEsp  = '	INSERT INTO ESP_NF_ARQ_GUIAS ';
					$queryEsp  .= ' 		(CODIGO_PRESTADOR, DATA_CADASTRO, DATA_ENVIO, MES_ANO_REFERENCIA, VALOR_TOTAL, TIPO_ARQUIVO, FLAG_BAIXADO, CAMINHO_ARQUIVO, NUMERO_PROCESSAMENTO, NUMERO_PROTOCOLO) VALUES ';
					$queryEsp  .= '		(' . aspas($_SESSION['codigoIdentificacao'])  . ', current_timestamp, current_timestamp, ' . aspas($_POST['mesAnoReferencia']) . ', ' . aspasNull(str_replace(',', '.', $_POST['valorTotal'])) . ', ';
					$queryEsp  .= '		 ' . aspas('AG') . ', ' . aspas('N') . ', ' . aspas($caminhoGuias) . ', ' . aspas($rowProc->NUMERO_PROCESSAMENTO) . ', ' . aspas($_POST['protocolo']) . ')';
				}
				
				jn_query($queryEsp);
			}
		}
	}
		
		
	$arquivoNF = isset($_FILES["arquivoNF"]) ? $_FILES["arquivoNF"] : FALSE;
	$arquivoNF_name = $_FILES['arquivoNF']['name'];
	$arquivoNF_type = $_FILES['arquivoNF']['type'];
	$arquivoNF_size = $_FILES['arquivoNF']['size'];
	$arquivoNF_temp_name = $_FILES['arquivoNF']['tmp_name'];
	$nomefinalNF = strtolower( remove_caracteres( $arquivoNF['name'] ) );
	$ext = explode('.',$arquivoNF_name);	
	$ext = $ext[(count($ext)-1)];
	
	if(strtoupper($ext) != 'PDF' ){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'Apenas arquivos com extensão PDF são aceitos neste formulário.'; 
	}else{
		$extValida = true;
	}

	if (isset($arquivoNF) && $_POST['numeroNF'] != '' && $extValida == true)
	{
		$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');	
		$nomefinalNF = $_SESSION['codigoIdentificacao'] . '_' . $numeroRegistro . '_' . $nomefinalNF;
		
		$retornoImg = salvarImagem('CFGARQUIVOS_PROCESSOS_NET',$numeroRegistro,$arquivoNF);
		$nomefinalNF =  'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$retornoImg['id'];

		$DataAtual = dataToSql(date('d/m/Y'));
		
		if($_SESSION['type_db'] == 'sqlsrv'){
			$DataAtual = 'getDate() ';
		}
		$nomeArquivo = $nomefinalNF;
		$Upload = array(
		   'Arquivo'   	=> $nomeArquivo,
		   'Data'     	=> $DataAtual,
		   'MesAno'  	=> $_POST['mesAnoReferencia'],
		   'numeroNF'  	=> $_POST['numeroNF'],
		   'valorTotal' => $_POST['valorTotal'],
		);

		if($_POST['numeroProcessamento'] != ''){
			$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
			(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_PROCESSAMENTO, VALOR_TOTAL, NUMERO_NF, MES_ANO)
			VALUES
			(' . aspas($numeroRegistro) . ',' . aspas('NOTA_PRESTADOR') . ',' . aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspas($_POST['numeroProcessamento']) .','. aspasNull(str_replace(',', '.', $_POST['valorTotal'])) . ', ' . aspas($Upload['numeroNF']) .','. aspas($Upload['MesAno']) . ')';
		}else{
			$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
				(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, VALOR_TOTAL, NUMERO_NF, MES_ANO)
				VALUES
				(' . aspas($numeroRegistro) . ',' . aspas('NOTA_PRESTADOR') . ',' . aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspasNull(str_replace(',', '.', $_POST['valorTotal'])) . ',' . aspas($Upload['numeroNF']) .','. aspas($Upload['MesAno']) . ')';
		}

		jn_query($query);

		$queryCaminho  = ' SELECT	CAMINHO_ARQUIVO_ARMAZENADO || NOME_ARQUIVO_ARMAZENADO AS NOME_ARQUIVO FROM CONTROLE_ARQUIVOS ';
		$queryCaminho .= ' WHERE CONTROLE_ARQUIVOS.CHAVE_REGISTRO = ' . aspas($numeroRegistro);
		$queryCaminho .= ' AND CONTROLE_ARQUIVOS.NOME_TABELA = ' . aspas('CFGARQUIVOS_PROCESSOS_NET');
		$resCaminho 	= jn_query($queryCaminho);
		$rowCaminho = jn_fetch_object($resCaminho);

		$caminhoArquivo = retornaValorConfiguracao('CAMINHO_UPLOAD_ARQUIVOS');  
		$caminhoNF = $caminhoArquivo . $rowCaminho->NOME_ARQUIVO;

		if($_POST['numeroRegistroNF'] != '' and $_POST['numeroRegistroNF'] != 'undefined'){
			$updateEsp = '	UPDATE ESP_NF_ARQ_GUIAS SET DATA_ENVIO = current_timestamp, CAMINHO_ARQUIVO = ' . aspas($caminhoNF) . ', NUMERO_NF = ' . aspas($_POST['numeroNF']) . ' WHERE NUMERO_REGISTRO = ' . aspas($_POST['numeroRegistroNF']);
			jn_query($updateEsp);
		}
	}

	if($extValida){
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] = 'Arquivo Enviado';
	}
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] == 'listar'){

	$caminhoArquivo = retornaValorConfiguracao('CAMINHO_UPLOAD_ARQUIVOS');  

	$codigoPrest = '';
	if($_SESSION['perfilOperador'] == 'PRESTADOR'){
		$codigoPrest = $_SESSION['codigoIdentificacao'];
	}else{
		$codigoPrest = $dadosInput['codigoPrestador'];
	}
	
	$query 	= " SELECT  ";
	$query 	.= " 	CONTROLE_ARQUIVOS.NUMERO_REGISTRO, ";
	$query 	.= " 	DATA_ENVIO, ";
	$query 	.= " 	'$caminhoArquivo' || CAMINHO_ARQUIVO_ARMAZENADO || NOME_ARQUIVO_ARMAZENADO AS NOME_ARQUIVO, ";
	$query 	.= " 	NOME_ARQUIVO_ORIGINAL AS DESCRICAO_ARQUIVO, ";
	$query 	.= " 	CODIGO_PRESTADOR, ";
	$query 	.= " 	MES_ANO, ";
	$query 	.= " 	TIPO_ARQUIVO, ";
	$query 	.= " 	NUMERO_NF, ";
	$query 	.= " 	VALOR_TOTAL ";
	$query 	.= " FROM CONTROLE_ARQUIVOS ";
	$query 	.= " INNER JOIN CFGARQUIVOS_PROCESSOS_NET ON (CONTROLE_ARQUIVOS.CHAVE_REGISTRO = CFGARQUIVOS_PROCESSOS_NET.NUMERO_REGISTRO) ";
	$query 	.= " WHERE CONTROLE_ARQUIVOS.NOME_TABELA = 'CFGARQUIVOS_PROCESSOS_NET' ";
	$query 	.= " AND CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR = " . aspas($codigoPrest);
	$query 	.= " AND ((CFGARQUIVOS_PROCESSOS_NET.NUMERO_PROTOCOLO = " . aspas($dadosInput['numeroProtocolo']) . ") or (TIPO_ARQUIVO = 'NOTA_PRESTADOR'))";
	$query 	.= " ORDER BY DATA_ENVIO DESC ";

	$res 	= jn_query($query);

	$retorno = array();
	$i = 0;
	
	while($row = jn_fetch_object($res)){
		$retorno[$i]['NOME_ARQUIVO'] 		= $row->NOME_ARQUIVO;
		$retorno[$i]['DESCRICAO_ARQUIVO'] 	= $row->DESCRICAO_ARQUIVO;
		$retorno[$i]['MES_ANO'] 			= $row->MES_ANO;
		$retorno[$i]['DATA_ENVIO'] 			= SqlToData($row->DATA_ENVIO);   
		$retorno[$i]['TIPO_ARQUIVO'] 		= $row->TIPO_ARQUIVO;
		$retorno[$i]['NUMERO_NF'] 			= $row->NUMERO_NF;
		$retorno[$i]['VALOR_TOTAL'] 		= toMoeda($row->VALOR_TOTAL);
		$i++;
	} 

	echo json_encode($retorno);
}

?>