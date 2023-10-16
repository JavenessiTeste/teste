<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../EstruturaEspecifica/azureStorage.php');

if($_POST){	
	$numeroSequencial = $_POST['protocoloAtendimento'];
	
	
	$arquivoCNS = isset($_FILES["arquivoCNS"]) ? $_FILES["arquivoCNS"] : FALSE;
    $arquivoCNS_temp_name = $_FILES['arquivoCNS']['tmp_name'];
    $nomefinalCNS = strtolower(remove_caracteres($arquivoCNS['name'] ) );
	$nomefinalCNS = str_replace(' ', '', $nomefinalCNS);
    $nomefinalCNS = $numeroSequencial . '_CNS_' . $nomefinalCNS;
    
    if (isset($arquivoCNS))
	{
		//ADICIONO NA TABELA DE ANEXOS DO BENEF		
		$caminhoArqBenef = 'https://app.plenasaude.com.br/AliancaNet/html/arquivosPF';
		$dir="../../../AliancaNet/html/arquivosPF/";           
		
		if(!utilizaBlobStorage()){
			if (move_uploaded_file($arquivoCNS_temp_name, $dir.$nomefinalCNS)) 
			{
			   @chmod($dir.$nomefinalCNS, 0777);
			   $nomeArquivo = $nomefinalCNS;		   
			   $query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
						(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
						VALUES
						(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';		   
			   jn_query($query);		   
			}
		}else{
			$caminhoArqBenef = 'https://app.plenasaude.com.br/UploadArquivos/arquivosPF/';			
			
			uploadFileBlogStorage('UploadArquivos/arquivosPF',$nomefinalCNS,fopen($arquivoCNS_temp_name , "r"),mime_content_type($arquivoCNS_temp_name));
			$nomeArquivo = $nomefinalCNS;		   
			$query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
					(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
					VALUES
					(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';		   
		   jn_query($query);	
		}
    }
	
	$arquivoCertidao = isset($_FILES["arquivoCertidao"]) ? $_FILES["arquivoCertidao"] : FALSE;
    $arquivoCert_temp_name = $_FILES['arquivoCertidao']['tmp_name'];
    $nomefinalCertidao = strtolower(remove_caracteres($arquivoCertidao['name'] ) );
	$nomefinalCertidao = str_replace(' ', '', $nomefinalCertidao);
    $nomefinalCertidao = $numeroSequencial . '_Certidao_' . $nomefinalCertidao;
    
    if (isset($arquivoCertidao))
	{	
		//ADICIONO NA TABELA DE ANEXOS DO BENEF
		$caminhoArqBenef = 'https://app.plenasaude.com.br/AliancaNet/html/arquivosPF';
		$dir="../../../AliancaNet/html/arquivosPF/";           
		
		
		if(!utilizaBlobStorage()){
			if (move_uploaded_file($arquivoCert_temp_name, $dir.$nomefinalCertidao)) 
			{
			   @chmod($dir.$nomefinalCertidao, 0777);
			   $nomeArquivo = $nomefinalCertidao;		   

			   $query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
						(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
						VALUES
						(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';
			   jn_query($query);
			}
		}else{
			$caminhoArqBenef = 'https://app.plenasaude.com.br/UploadArquivos/arquivosPF/';			
			
			uploadFileBlogStorage('UploadArquivos/arquivosPF',$nomefinalCertidao,fopen($arquivoCert_temp_name , "r"),mime_content_type($arquivoCert_temp_name));
			$nomeArquivo = $nomefinalCertidao;		   
			$query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
					(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
					VALUES
					(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';		   
		   jn_query($query);	
		}
    }
	
	
	$arquivoCPF = isset($_FILES["arquivoCPF"]) ? $_FILES["arquivoCPF"] : FALSE;
    $arquivoCPF_temp_name = $_FILES['arquivoCPF']['tmp_name'];
    $nomefinalCPF = strtolower(remove_caracteres($arquivoCPF['name'] ) );
	$nomefinalCPF = str_replace(' ', '', $nomefinalCPF);
    $nomefinalCPF = $numeroSequencial . '_CPF_' . $nomefinalCPF;
    
    if (isset($arquivoCPF))
	{		
		//ADICIONO NA TABELA DE ANEXOS DO BENEF
		$caminhoArqBenef = 'https://app.plenasaude.com.br/AliancaNet/html/arquivosPF';
		$dir="../../../AliancaNet/html/arquivosPF/";                 
		
		if(!utilizaBlobStorage()){
			if (move_uploaded_file($arquivoCPF_temp_name, $dir.$nomefinalCPF)) 
			{
			   @chmod($dir.$nomefinalCPF, 0777);
			   $nomeArquivo = $nomefinalCPF;		   

			   $query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
						(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
						VALUES
						(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';
			   jn_query($query);
			}	
		}else{
			$caminhoArqBenef = 'https://app.plenasaude.com.br/UploadArquivos/arquivosPF/';			
			
			uploadFileBlogStorage('UploadArquivos/arquivosPF',$nomefinalCPF,fopen($arquivoCPF_temp_name , "r"),mime_content_type($arquivoCPF_temp_name));
			$nomeArquivo = $nomefinalCPF;		   
			$query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
					(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
					VALUES
					(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';		   
		   jn_query($query);	
		}
    }
	
	
	$arquivoRG = isset($_FILES["arquivoRG"]) ? $_FILES["arquivoRG"] : FALSE;
    $arquivoRG_temp_name = $_FILES['arquivoRG']['tmp_name'];
    $nomefinalRG = strtolower(remove_caracteres($arquivoRG['name'] ) );
	$nomefinalRG = str_replace(' ', '', $nomefinalRG);
    $nomefinalRG = $numeroSequencial . '_RG_' . $nomefinalRG;
    
    if (isset($arquivoRG))
	{		
		//ADICIONO NA TABELA DE ANEXOS DO BENEF
		$caminhoArqBenef = 'https://app.plenasaude.com.br/AliancaNet/html/arquivosPF';
		$dir="../../../AliancaNet/html/arquivosPF/";                 
		
		if(!utilizaBlobStorage()){
			if (move_uploaded_file($arquivoRG_temp_name, $dir.$nomefinalRG)) 
			{
			   @chmod($dir.$nomefinalRG, 0777);
			   $nomeArquivo = $nomefinalRG;		   

			   $query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
						(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
						VALUES
						(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';
			   jn_query($query);
			}	
		}else{
			$caminhoArqBenef = 'https://app.plenasaude.com.br/UploadArquivos/arquivosPF/';			
			
			uploadFileBlogStorage('UploadArquivos/arquivosPF',$nomefinalRG,fopen($arquivoRG_temp_name , "r"),mime_content_type($arquivoRG_temp_name));
			$nomeArquivo = $nomefinalRG;		   
			$query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
					(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
					VALUES
					(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomeArquivo) . ')';		   
		   jn_query($query);	
		}	
    }
}

?>