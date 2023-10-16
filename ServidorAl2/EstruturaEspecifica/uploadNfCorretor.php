<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../EstruturaEspecifica/azureStorage.php');

if($_POST['tipo'] =='enviar'){	
	$arquivoNF = isset($_FILES["arquivoNF"]) ? $_FILES["arquivoNF"] : FALSE;
	$arquivoNF_name = $_FILES['arquivoNF']['name'];
	$arquivoNF_type = $_FILES['arquivoNF']['type'];
	$arquivoNF_size = $_FILES['arquivoNF']['size'];
	$arquivoNF_temp_name = $_FILES['arquivoNF']['tmp_name'];
	$nomefinalNF = strtolower( remove_caracteres( $arquivoNF['name'] ) );
	$nomefinalNF = str_replace(' ','_',$nomefinalNF);
	
	if (isset($arquivoNF))
	{
		if(!utilizaBlobStorage()){			
			$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');			

			$nomefinalNF = $_SESSION['codigoIdentificacao'] . '_' . $numeroRegistro . '_' . $nomefinalNF;		
			$retornoImg = salvarImagem('CFGARQUIVOS_PROCESSOS_NET',$numeroRegistro,$arquivoNF);		
			
			$queryId = 'SELECT NUMERO_REGISTRO FROM CONTROLE_ARQUIVOS WHERE NOME_TABELA = "CFGARQUIVOS_PROCESSOS_NET" AND CHAVE_REGISTRO = ' . aspas($retornoImg['id']);
			$resId = jn_query($queryId);
			$rowId = jn_fetch_object($resId);		
			
			$nomefinalNF =  'EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowId->NUMERO_REGISTRO;	   
			
			$mesAnoReferencia = substr($_POST['mesAnoReferencia'], 0, 2) . '/' . substr($_POST['mesAnoReferencia'], 2, 4);		
			$nomeArquivo = $nomefinalNF;
			$Upload = array(
			   'Arquivo'   	=> $nomeArquivo,		   
			   'MesAno'  	=> $mesAnoReferencia
			);

			$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
					(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_CORRETOR, MES_ANO)
					VALUES
					(' . aspas($numeroRegistro) . ',' . aspas('NOTA_CORRETOR') . ',' . aspas($Upload['Arquivo']) .', getDate(),'. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['MesAno']) . ')';
			if(jn_query($query)){
				disparaEmailNF($nomeArquivo);
			}
		}else{
			
			$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');			
			$nomefinalNF = $_SESSION['codigoIdentificacao'] . '_' . $numeroRegistro . '_' . $nomefinalNF;		   
			uploadFileBlogStorage('UploadArquivos/NfCorretor',$nomefinalNF,fopen($arquivoNF_temp_name , "r"),mime_content_type($arquivoNF_temp_name));
			
			$mesAnoReferencia = substr($_POST['mesAnoReferencia'], 0, 2) . '/' . substr($_POST['mesAnoReferencia'], 2, 4);		
			$caminhoNFCorretor = 'https://app.plenasaude.com.br/UploadArquivos/NfCorretor/';	
			$nomeArquivo = $caminhoNFCorretor . $nomefinalNF;
			$Upload = array(
			   'Arquivo'   	=> $nomeArquivo,		   
			   'MesAno'  	=> $mesAnoReferencia
			);

			$numeroProtocolo = retornaValorConfiguracao('INICIO_PROTOCOLO_NF_CORRETOR') . date('Ymd') . $_SESSION['codigoIdentificacao'] . $numeroRegistro;

			$query = 'INSERT INTO CFGARQUIVOS_PROCESSOS_NET
					(NUMERO_REGISTRO, TIPO_ARQUIVO, NOME_ARQUIVO, DATA_ENVIO, CODIGO_CORRETOR, MES_ANO, NUMERO_PROTOCOLO)
					VALUES
					(' . aspas($numeroRegistro) . ',' . aspas('NOTA_CORRETOR') . ',' . aspas($Upload['Arquivo']) .', getDate(),'. aspas($_SESSION['codigoIdentificacao']) .','. aspas($Upload['MesAno']) . ', ' . aspasNull($numeroProtocolo) . ')';
			if(jn_query($query)){
				disparaEmailNF($nomeArquivo);
			}
		}
		
	}
	$retorno['STATUS'] = 'OK';
	$retorno['MSG'] = 'Arquivo Enviado';
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] == 'listar'){
	
	$query 	 = " SELECT NUMERO_REGISTRO, DATA_ENVIO, NOME_ARQUIVO, CODIGO_CORRETOR, MES_ANO, NUMERO_PROTOCOLO FROM CFGARQUIVOS_PROCESSOS_NET ";
	$query 	.= " WHERE CODIGO_CORRETOR = " . aspas($_SESSION['codigoIdentificacao']);
	$query 	.= " ORDER BY DATA_ENVIO DESC ";
	$res 	= jn_query($query);
	
	$retorno = array();
	$linha = '';
	while($row = jn_fetch_object($res)){
		if(!utilizaBlobStorage()){
			$caminhoNFCorretor = retornaValorConfiguracao('CAMINHO_NF_CORRETOR');  
			$caminhoNFCorretorOld = retornaValorConfiguracao('CAMINHO_NF_CORRETOR_OLD');  
			if(strpos($row->NOME_ARQUIVO,"UploadArquivos/NfCorretor/") == 0){
				$linha['NOME_ARQUIVO'] = $caminhoNFCorretor . $row->NOME_ARQUIVO;
			}else{			
				$linha['NOME_ARQUIVO'] = $caminhoNFCorretorOld . $row->NOME_ARQUIVO;
			}
		}else{
			$linha['NOME_ARQUIVO'] = $row->NOME_ARQUIVO;
		}
		$linha['MES_ANO'] = $row->MES_ANO;
		
		if($row->DATA_ENVIO)
			$linha['DATA_ENVIO'] = SqlToData($row->DATA_ENVIO);   
		
		
		$linha['ABRIR_PROTOCOLO'] = retornaValorConfiguracao('CAMINHO_PROT_NF_CORRETOR') . $row->NUMERO_PROTOCOLO;
		

		$retorno[] = $linha;
	}  
	echo json_encode($retorno);
}else if($dadosInput['tipo'] =='retornaValorConfiguracao'){

	if(retornaValorConfiguracao($dadosInput['idConfiguracao']))
		$retorno['DADOS'][] = jn_utf8_encode(retornaValorConfiguracao($dadosInput['idConfiguracao']));
    else
        $retorno['DADOS'][] = '';

	echo json_encode($retorno);
	
}

function disparaEmailNF($arquivo){
	
	if(!utilizaBlobStorage()){
		$caminhoNFCorretor = retornaValorConfiguracao('CAMINHO_NF_CORRETOR');  
		$caminhoNFCorretorOld = retornaValorConfiguracao('CAMINHO_NF_CORRETOR_OLD');  
		if(strpos($row->NOME_ARQUIVO,"EstruturaPrincipal/") == 0){
			$arquivo = $caminhoNFCorretor . $arquivo;
		}else{			
			$arquivo = $caminhoNFCorretorOld . $arquivo;
		}
	}
	
	if($_SESSION['codigoSmart'] == '3423'){
		$mesAnoReferencia = substr($_POST['mesAnoReferencia'], 0, 2) . '/' . substr($_POST['mesAnoReferencia'], 2, 4);		
		$assunto = 'Plena Saude';	
		$corpoMSG  = '	<!doctype html>
						<html>
							<head>
								<meta charset="utf-8">
								<meta name="viewport" content="width=device-width, initial-scale=1">							
							</head>
							<body >
								Prezado(a), <br>
								O corretor ' . $_SESSION['nomeUsuario'] . ' enviou a nota fiscal referente ao mes ' . $mesAnoReferencia . '. <br>
								Se desejar abrir o conteudo enviado, <a href=' . $arquivo . ' target="_blank">clique aqui</a>
								<br><br>
								
								Ats,<br>
								Plena Saude

							</body>
						</html>';
		//pr($corpoMSG,true);
	}
					
	if($assunto == '')
		$assunto = 'Assunto Teste';	
	
	if($corpoMSG == '')
		$corpoMSG = 'Mensagem Teste';	

	require_once('../lib/class.phpmailer.php');
	require_once("../lib/PHPMailerAutoload.php");

	$mail   = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');	
	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));	
		
	if($_SESSION['codigoSmart'] == '3423'){
		$mail->AddAddress('relatorios.corretoras@plenasaude.com.br', 'Plena Saude');
		$mail->AddCC('financeiro@plenasaude.com.br', "Plena Saude");	
		$mail->AddCC('fiscal@plenasaude.com.br', 'Plena Saude');
	}
	
	$mail->Subject = $assunto;		
	$mail->MsgHTML($corpoMSG);
	$mail->Send();
}

?>