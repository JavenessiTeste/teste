<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../EstruturaEspecifica/azureStorage.php');

if($_POST['tipo'] =='enviar'){
	$extValida = false;	
    $arquivoXML = isset($_FILES["arquivoXML"]) ? $_FILES["arquivoXML"] : FALSE;
	
	if($arquivoXML){
		$arquivoXML_name = $_FILES['arquivoXML']['name'];
		$arquivoXML_type = $_FILES['arquivoXML']['type'];
		$arquivoXML_size = $_FILES['arquivoXML']['size'];
		$arquivoXML_temp_name = $_FILES['arquivoXML']['tmp_name'];
		$ext = explode('.',$arquivoXML_name);	
		$ext = $ext[(count($ext)-1)];
		//pr('Formulário em manutenção, favor retornar em 5 minutos.');
		
		if(strtoupper($ext) == 'XML'){
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG'] = 'Arquivos da extensão XML não são aceitos neste formulário.'; 
		}else{
			$extValida = true;
		}
		
		if($extValida){
			$nomefinalXML = strtolower( remove_caracteres( $arquivoXML['name'] ) );
			
			if (isset($arquivoXML))
			{
				if(!utilizaBlobStorage()){
					
					$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');					
					$retornoImg = salvarImagem('CFGARQUIVOS_PROCESSOS_NET',$numeroRegistro,$arquivoXML);

					$DataAtual = dataToSql(date('d/m/Y'));
					if($_SESSION['type_db'] == 'sqlsrv'){
						$DataAtual = 'getDate() ';
					}

					$nomeArquivo = 'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$retornoImg['id'];

					$Upload = array(
					   'Arquivo'   	=> $nomeArquivo,
					   'Data'      	=> $DataAtual,
					   'MesAno'  	=> $_POST['mesAnoReferencia'],
					   'numeroNF'  	=> $_POST['numeroNF']
					);

					$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
							(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_NF, MES_ANO)
							VALUES
							(' . aspas($numeroRegistro) . ',' . aspas('ARQUIVO_GUIAS') . ','. aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['numeroNF']) .','. aspas($Upload['MesAno']) . ')';
					jn_query($query);
				}else{
					$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');			
					$nomefinalArq = strtolower(remove_caracteres($_FILES['arquivoXML']['name']));	   
					$nomefinalArq = $_SESSION['codigoIdentificacao'] . '_' . $numeroRegistro . '_' . $nomefinalArq;		   
					uploadFileBlogStorage('UploadArquivos/ArquivoGuiasPrest',$nomefinalArq,fopen($arquivoXML_temp_name , "r"),mime_content_type($arquivoXML_temp_name));
					
					$DataAtual = dataToSql(date('d/m/Y'));
					if($_SESSION['type_db'] == 'sqlsrv'){
						$DataAtual = 'getDate() ';
					}
					
					$mesAnoReferencia = substr($_POST['mesAnoReferencia'], 0, 2) . '/' . substr($_POST['mesAnoReferencia'], 2, 4);		
					$caminhoArquivGuiasPrest = 'https://app.plenasaude.com.br/UploadArquivos/ArquivoGuiasPrest/';	
					$nomeArquivo = $caminhoArquivGuiasPrest . $nomefinalArq;
					$Upload = array(
						'Arquivo'   	=> $nomeArquivo,
						'Data'      	=> $DataAtual,					   
						'MesAno'  		=> $_POST['mesAnoReferencia'],
						'numeroNF'  	=> $_POST['numeroNF']
					);


					if($_POST['protocolo']){
						$Upload['protocolo'] = $_POST['protocolo'];

						$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
							(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_NF, NUMERO_PROTOCOLO, MES_ANO)
							VALUES
							(' . aspas($numeroRegistro) . ',' . aspas('ARQUIVO_GUIAS') . ','. aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['numeroNF']) .','.aspas($Upload['protocolo']) .','. aspas($Upload['MesAno']) . ')';

					}else{
						$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
							(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_NF, MES_ANO)
							VALUES
							(' . aspas($numeroRegistro) . ',' . aspas('ARQUIVO_GUIAS') . ','. aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['numeroNF']) .','. aspas($Upload['MesAno']) . ')';
					}
					
					jn_query($query);
				}
			}
		}
	}
		
		
	$arquivoNF = isset($_FILES["arquivoNF"]) ? $_FILES["arquivoNF"] : FALSE;
	$arquivoNF_name = $_FILES['arquivoNF']['name'];
	$arquivoNF_type = $_FILES['arquivoNF']['type'];
	$arquivoNF_size = $_FILES['arquivoNF']['size'];
	$arquivoNF_temp_name = $_FILES['arquivoNF']['tmp_name'];
	$nomefinalNF = strtolower( remove_caracteres( $arquivoNF['name'] ) );
	
	if (isset($arquivoNF))
	{
		if(!utilizaBlobStorage()){
			
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
			   'numeroNF'  	=> $_POST['numeroNF']
			);

			$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
					(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_NF, MES_ANO)
					VALUES
					(' . aspas($numeroRegistro) . ',' . aspas('NOTA_PRESTADOR') . ',' . aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['numeroNF']) .','. aspas($Upload['MesAno']) . ')';
			jn_query($query);		
		}else{
			$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');			
			$nomefinalNF = $_SESSION['codigoIdentificacao'] . '_' . $numeroRegistro . '_' . $nomefinalNF;		   
			uploadFileBlogStorage('UploadArquivos/NfPrestador',$nomefinalNF,fopen($arquivoNF_temp_name , "r"),mime_content_type($arquivoNF_temp_name));
			
			$DataAtual = dataToSql(date('d/m/Y'));
			
			if($_SESSION['type_db'] == 'sqlsrv'){
				$DataAtual = 'getDate() ';
			}
			$mesAnoReferencia = substr($_POST['mesAnoReferencia'], 0, 2) . '/' . substr($_POST['mesAnoReferencia'], 2, 4);		
			$caminhoNFPrestador = 'https://app.plenasaude.com.br/UploadArquivos/NfPrestador/';	
			$nomeArquivo = $caminhoNFPrestador . $nomefinalNF;
			$Upload = array(
			   'Arquivo'   	=> $nomeArquivo,
			   'Data'     	=> $DataAtual,
			   'MesAno'  	=> $_POST['mesAnoReferencia'],
			   'numeroNF'  	=> $_POST['numeroNF']
			);

			if($_POST['protocolo']){
				$Upload['protocolo'] = $_POST['protocolo'];

				$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
					(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_NF, NUMERO_PROTOCOLO, MES_ANO)
					VALUES
					(' . aspas($numeroRegistro) . ',' . aspas('NOTA_PRESTADOR') . ',' . aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['numeroNF']) .','.aspas($Upload['protocolo']) .','. aspas($Upload['MesAno']) . ')';
			}else{
				$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
				(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_PRESTADOR, NUMERO_NF, MES_ANO)
				VALUES
				(' . aspas($numeroRegistro) . ',' . aspas('NOTA_PRESTADOR') . ',' . aspas($Upload['Arquivo']) .', '. $Upload['Data'] .','. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['numeroNF']) .','. aspas($Upload['MesAno']) . ')';
			}
			
			jn_query($query);
		}
		
	}
	$retorno['STATUS'] = 'OK';
	$retorno['MSG'] = 'Arquivo Enviado';
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] == 'listar'){
	$caminhoArquivo = retornaValorConfiguracao('CAMINHO_UPLOAD_ARQUIVOS');  
	$caminhoArquivoAL1 = retornaValorConfiguracao('CAMINHO_UPLOAD_ARQUIVOS_AL1');  		

	if( $_SESSION['type_db'] == 'firebird' ){
		$query 	 = " select ";
		$query 	.= " NUMERO_REGISTRO, ";
		$query 	.= " DATA_ENVIO, ";
		$query 	.= "  NOME_ARQUIVO, ";
		$query 	.= " CODIGO_PRESTADOR, ";
		$query 	.= " MES_ANO, ";
		$query 	.= " TIPO_ARQUIVO, ";
		$query 	.= " NUMERO_NF ";
		$query 	.= " FROM (  ";
		$query 	.= " SELECT  ";
		$query 	.= " 	CONTROLE_ARQUIVOS.NUMERO_REGISTRO, ";
		$query 	.= " 	DATA_ENVIO, ";
		$query 	.= " 	'$caminhoArquivo' || CAMINHO_ARQUIVO_ARMAZENADO || NOME_ARQUIVO_ARMAZENADO AS NOME_ARQUIVO, ";
		$query 	.= " 	CODIGO_PRESTADOR, ";
		$query 	.= " 	MES_ANO, ";
		$query 	.= " 	TIPO_ARQUIVO, ";
		$query 	.= " 	NUMERO_NF ";
		$query 	.= " FROM CONTROLE_ARQUIVOS ";
		$query 	.= " INNER JOIN CFGARQUIVOS_PROCESSOS_NET ON (CONTROLE_ARQUIVOS.CHAVE_REGISTRO = CFGARQUIVOS_PROCESSOS_NET.NUMERO_REGISTRO) ";
		$query 	.= " WHERE CFGARQUIVOS_PROCESSOS_NET.TIPO_ARQUIVO = 'NOTA_PRESTADOR' ";
		$query 	.= " AND CONTROLE_ARQUIVOS.NOME_TABELA = 'CFGARQUIVOS_PROCESSOS_NET' ";
		$query 	.= " AND CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR = " . aspas($_SESSION['codigoIdentificacao']);
		$query 	.= " UNION ALL ";
		$query 	.= " SELECT  ";
		$query 	.= " 	NUMERO_REGISTRO, ";
		$query 	.= " 	DATA_ENVIO, ";	
		$query 	.= " 	'$caminhoArquivoAL1' || NOME_ARQUIVO, ";	
		$query 	.= " 	CODIGO_PRESTADOR, ";	
		$query 	.= " 	MES_ANO, ";	
		$query 	.= " 	TIPO_ARQUIVO, ";	
		$query 	.= " 	NUMERO_NF ";	
		$query 	.= "  FROM CFGARQUIVOS_PROCESSOS_NET ";
		$query 	.= " WHERE CODIGO_PRESTADOR = " . aspas($_SESSION['codigoIdentificacao']);
		$query 	.= " ) ";
		$query 	.= " ORDER BY DATA_ENVIO DESC";
	}else{
		$query 	= " SELECT  ";
		$query 	.= " 	CONTROLE_ARQUIVOS.NUMERO_REGISTRO, ";
		$query 	.= " 	DATA_ENVIO, ";
		$query 	.= " 	'$caminhoArquivo' + CAMINHO_ARQUIVO_ARMAZENADO + NOME_ARQUIVO_ARMAZENADO AS NOME_ARQUIVO, ";
		$query 	.= " 	CODIGO_PRESTADOR, ";
		$query 	.= " 	MES_ANO, ";
		$query 	.= " 	TIPO_ARQUIVO, ";
		$query 	.= " 	NUMERO_NF ";
		$query 	.= " FROM CONTROLE_ARQUIVOS ";
		$query 	.= " INNER JOIN CFGARQUIVOS_PROCESSOS_NET ON (CONTROLE_ARQUIVOS.CHAVE_REGISTRO = CFGARQUIVOS_PROCESSOS_NET.NUMERO_REGISTRO) ";
		$query 	.= " WHERE CFGARQUIVOS_PROCESSOS_NET.TIPO_ARQUIVO = 'NOTA_PRESTADOR' ";
		$query 	.= " AND CONTROLE_ARQUIVOS.NOME_TABELA = 'CFGARQUIVOS_PROCESSOS_NET' ";
		$query 	.= " AND CFGARQUIVOS_PROCESSOS_NET.CODIGO_PRESTADOR = " . aspas($_SESSION['codigoIdentificacao']);
		
		if($dadosInput['protocolo'])
			$query 	.= " AND CFGARQUIVOS_PROCESSOS_NET.NUMERO_PROTOCOLO = " . aspas($dadosInput['protocolo']);

		$query 	.= " UNION ALL ";

		$query 	.= " SELECT  ";
		$query 	.= " 	NUMERO_REGISTRO, ";
		$query 	.= " 	DATA_ENVIO, ";	
		$query 	.= " 	'$caminhoArquivoAL1' + NOME_ARQUIVO, ";	
		$query 	.= " 	CODIGO_PRESTADOR, ";	
		$query 	.= " 	MES_ANO, ";	
		$query 	.= " 	TIPO_ARQUIVO, ";	
		$query 	.= " 	NUMERO_NF ";	
		$query 	.= "  FROM CFGARQUIVOS_PROCESSOS_NET ";
		$query 	.= " WHERE CODIGO_PRESTADOR = " . aspas($_SESSION['codigoIdentificacao']);
		
		if($dadosInput['protocolo'])
			$query 	.= " AND CFGARQUIVOS_PROCESSOS_NET.NUMERO_PROTOCOLO = " . aspas($dadosInput['protocolo']);
	}

	$res 	= jn_query($query);

	$retorno = array();
	$linha = Array();
	
	while($row = jn_fetch_object($res)){		
		$linha['NOME_ARQUIVO'] = $row->NOME_ARQUIVO;
		$linha['MES_ANO'] = $row->MES_ANO;
		$linha['DATA_ENVIO'] = SqlToData($row->DATA_ENVIO);   
		$linha['TIPO_ARQUIVO'] = $row->TIPO_ARQUIVO;
		$linha['NUMERO_NF'] = $row->NUMERO_NF;
		$retorno[] = $linha;
	}  
	echo json_encode($retorno);
}else if($dadosInput['tipo'] == 'configuracoes'){
	$retorno = array();
	
	$retorno['OCULTAR_ARQUIVO_GUIAS'] = retornaValorConfiguracao('OCULTAR_ARQUIVO_GUIAS');  
	$retorno['PERMITIR_UPLOAD_APENAS_GUIA'] = retornaValorConfiguracao('PERMITIR_UPLOAD_APENAS_GUIA');  
	
	echo json_encode($retorno);
}

?>