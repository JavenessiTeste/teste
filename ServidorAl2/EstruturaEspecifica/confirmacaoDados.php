<?php

require('../lib/base.php');



if($dadosInput['tipo']== 'pesquisaCep'){
	
	
	$json_file = file_get_contents('http://viacep.com.br/ws/'. retiraCaractere($dadosInput['cep']) .'/json');   
	$json_str = json_decode($json_file, true);	
	
	$dadosEndereco['cep'] = $json_str['cep'];
	$dadosEndereco['endereco'] 	= jn_utf8_encode(strToUpper(retiraCaractere($json_str['logradouro'])));
	$dadosEndereco['bairro'] 	= jn_utf8_encode(strToUpper(retiraCaractere($json_str['bairro'])));
	$dadosEndereco['cidade'] 	= jn_utf8_encode(strToUpper(retiraCaractere($json_str['localidade'])));
	$dadosEndereco['estado'] 	= jn_utf8_encode(strToUpper($json_str['uf']));	


	echo json_encode($dadosEndereco);

}
if($dadosInput['tipo']== 'dados'){
	if($dadosInput['tipoDado']=='cep'){
		$selectEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1000 WHERE PS1000.CODIGO_ASSOCIADO= '.aspas($_SESSION['codigoIdentificacaoTitular']);
		$resEmpresa  = jn_query($selectEmpresa);
		$dadosEndereco = array();
		$dadosEndereco['cep'] = '';
		$dadosEndereco['endereco'] = '';
		$dadosEndereco['numero'] = '';
		$dadosEndereco['complemento'] = '';
		$dadosEndereco['bairro'] = '';
		$dadosEndereco['cidade'] = '';
		$dadosEndereco['estado'] = '';
		//$dadosEndereco['codigo']     = '';
		
		if($rowEmpresa = jn_fetch_object($resEmpresa)){	
			if($rowEmpresa->CODIGO_EMPRESA == '400' )
				$queryEnd = 'select * from ps1001 where Ps1001.codigo_associado = '.aspas($_SESSION['codigoIdentificacaoTitular']);
			else
				$queryEnd = 'select * from ps1015 where Ps1015.codigo_associado = '.aspas($_SESSION['codigoIdentificacaoTitular']);
			
			$resEnd  = jn_query($queryEnd);
			if($rowEnd = jn_fetch_object($resEnd)){	
				$endereco = $rowEnd->ENDERECO.' ';
				$numero = '';
				$complemento = '';
				$auxEndereco = explode(',',$endereco);
				$endereco = $auxEndereco[0];
				if(count($auxEndereco)>1){
					$auxEndereco = explode('-',$auxEndereco[1]); 
					$numero = $auxEndereco[0];
					if(count($auxEndereco)>1){
						$complemento = $auxEndereco[1];
					}
				}
			}
				$dadosEndereco['cep'] = $rowEnd->CEP;
				$dadosEndereco['endereco'] = jn_utf8_encode($endereco);
				$dadosEndereco['numero'] = jn_utf8_encode($numero);
				$dadosEndereco['complemento'] = jn_utf8_encode($complemento);
				$dadosEndereco['bairro'] = jn_utf8_encode($rowEnd->BAIRRO);
				$dadosEndereco['cidade'] = jn_utf8_encode($rowEnd->CIDADE);
				$dadosEndereco['estado'] = jn_utf8_encode($rowEnd->ESTADO);
		}
		
		echo json_encode($dadosEndereco);
	}
	if($dadosInput['tipoDado']=='email'){
		$dadosEmail = array();
		$dadosEmail['email']      = '';
		$dadosEmail['confirmado'] = false;
		$dadosEmail['codigo']     = '';
		
		$selectEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1000 WHERE PS1000.CODIGO_ASSOCIADO= '.aspas($_SESSION['codigoIdentificacao']);
		$resEmpresa  = jn_query($selectEmpresa);
		if($rowEmpresa = jn_fetch_object($resEmpresa)){	
			if($rowEmpresa->CODIGO_EMPRESA == '400' ){
				$tabela = 'PS1001';
			}else{
				$tabela = 'PS1015';
			}
			
			$sqlEmail = 'SELECT COALESCE(EMAIL_CONFIRMADO,'.$tabela.'.ENDERECO_EMAIL) EMAIL_CONFIRMADO, DATA_CONFIRMACAO_EMAIL FROM PS1000
						 LEFT JOIN '.$tabela.' ON '.$tabela.'.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
						 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($_SESSION['codigoIdentificacao']);
		
			$resEmail  = jn_query($sqlEmail);
			
			if($rowEmail = jn_fetch_object($resEmail)){	
				$dadosEmail['email'] = $rowEmail->EMAIL_CONFIRMADO;
				if($rowEmail->DATA_CONFIRMACAO_EMAIL != '')
					$dadosEmail['confirmado'] = true;				
			}
		}
		echo json_encode($dadosEmail);
	}
	if($dadosInput['tipoDado']=='celular'){
		$dadosCelular = array();
		$dadosCelular['email']      = '';
		$dadosCelular['confirmado'] = false;
		$dadosCelular['codigo']     = '';
		
			
			$sqlEmail = 'select  coalesce(CELULAR_CONFIRMADO,cast(PS1006.CODIGO_AREA as varchar(2))+Ps1006.NUMERO_TELEFONE) CELULAR_CONFIRMADO, DATA_CONFIRMACAO_CELULAR from PS1000
						 left join PS1006 on PS1006.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
						 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($_SESSION['codigoIdentificacao']);
		
			$resEmail  = jn_query($sqlEmail);
			
			if($rowEmail = jn_fetch_object($resEmail)){	
				$dadosCelular['celular'] = $rowEmail->CELULAR_CONFIRMADO;
				if($rowEmail->DATA_CONFIRMACAO_CELULAR != '')
					$dadosCelular['confirmado'] = true;				
			}
		
		echo json_encode($dadosCelular);
	}
}

if($dadosInput['tipo']== 'salva'){
	$retorno = array();
	$retorno['STATUS'] = 'OK';
	//$retorno['MSG']    = 'Dados Alterados';
	
	
	if($dadosInput['tipoDado']=='cep'){
		
		$endereco = $dadosInput['dado']['endereco'].', '.$dadosInput['dado']['numero'];
		if(trim($dadosInput['dado']['complemento'])!=''){
			$endereco .=' - '.$dadosInput['dado']['complemento'];
		}
		
		$endereco = substr($endereco,0,45);
		
		$selectEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1000 WHERE PS1000.CODIGO_ASSOCIADO= '.aspas($_SESSION['codigoIdentificacaoTitular']);
		$resEmpresa  = jn_query($selectEmpresa);
		if($rowEmpresa = jn_fetch_object($resEmpresa)){	
			if($rowEmpresa->CODIGO_EMPRESA == '400' )
				$queryEnd = 'select NUMERO_REGISTRO from ps1001 where Ps1001.codigo_associado = '.aspas($_SESSION['codigoIdentificacaoTitular']);
			else
				$queryEnd = 'select NUMERO_REGISTRO from ps1015 where Ps1015.codigo_associado = '.aspas($_SESSION['codigoIdentificacaoTitular']);
			
			$resEnd  = jn_query($queryEnd);
				if($rowEmpresa->CODIGO_EMPRESA == '400' ){
					$tabela = 'PS1001';
				}else{
					$tabela = 'PS1015';
				}
			if($rowEnd = jn_fetch_object($resEnd)){
					$sql =  'UPDATE  '.$tabela.' set 
													ENDERECO = '.aspasNull(sanitizeString($endereco)).',
													BAIRRO= '.aspasNull(sanitizeString($dadosInput['dado']['bairro'])).',
													CIDADE= '.aspasNull(sanitizeString($dadosInput['dado']['cidade'])).',
													ESTADO= '.aspasNull($dadosInput['dado']['estado']).',
													CEP= '.aspasNull($dadosInput['dado']['cep']).',
													DATA_ALTERACAO_CADASTRAL = '.dataToSql(date("d/m/Y")).'
													where numero_registro ='. aspas($rowEnd->NUMERO_REGISTRO);
				
			}else{
				if($rowEmpresa->CODIGO_EMPRESA == '400' ){
					$sql = 'INSERT INTO '.$tabela.'(CODIGO_ASSOCIADO,ENDERECO,BAIRRO,CIDADE,ESTADO,CEP,DATA_ALTERACAO_CADASTRAL)
							   VALUES('.aspasNull($_SESSION['codigoIdentificacaoTitular']).','.
							   			aspasNull(sanitizeString($endereco)).','.
										aspasNull(sanitizeString($dadosInput['dado']['bairro'])).','.
										aspasNull(sanitizeString($dadosInput['dado']['cidade'])).','.
										aspasNull($dadosInput['dado']['estado']).','.
										aspasNull($dadosInput['dado']['cep']).','.
										dataToSql(date("d/m/Y")).')';
				}else{
					$sql = 'INSERT INTO '.$tabela.'(CODIGO_ASSOCIADO,CODIGO_EMPRESA,ENDERECO,BAIRRO,CIDADE,ESTADO,CEP,DATA_ALTERACAO_CADASTRAL)
							   VALUES('.aspasNull($_SESSION['codigoIdentificacaoTitular']).','.
										aspasNull($rowEmpresa->CODIGO_EMPRESA).','.
										aspasNull(sanitizeString($endereco)).','.
										aspasNull(sanitizeString($dadosInput['dado']['bairro'])).','.
										aspasNull(sanitizeString($dadosInput['dado']['cidade'])).','.
										aspasNull($dadosInput['dado']['estado']).','.
										aspasNull($dadosInput['dado']['cep']).','.
										dataToSql(date("d/m/Y")).')';				
				}
			}
			$enderecoRes  = jn_query($sql);
		}
	}
	
	if($dadosInput['tipoDado']=='email'){
			$fazerUpdate = true;
			if(!$dadosInput['dado']['confirmado']){
				$select = '	select * from ESP_GERACAO_CODIGO where CODIGO_GERADO= '.aspas($dadosInput['dado']['codigo']).' AND DADO_VERIFICACAO ='.aspas($dadosInput['dado']['email']).' and DATA_GERACAO =  '.dataToSql(date("d/m/Y"));
				$res  = jn_query($select);
				if($row = jn_fetch_object($res)){
					$fazerUpdate = true;
				}else{
					$fazerUpdate = false;
				}
			}
			if($fazerUpdate){
				$update = 'UPDATE PS1000 SET EMAIL_CONFIRMADO ='.aspas($dadosInput['dado']['email']).', DATA_CONFIRMACAO_EMAIL = '.dataToSql(date("d/m/Y")).' where CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']);
				$resUpdate = jn_query($update);
				$update = 'UPDATE PS1001 SET ENDERECO_EMAIL='.aspas($dadosInput['dado']['email']).' where CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']);
				$resUpdate = jn_query($update);
				$update = 'UPDATE PS1015 SET ENDERECO_EMAIL ='.aspas($dadosInput['dado']['email']).' where CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']);
				$resUpdate = jn_query($update);
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = 'Código inválido.';
			}
			
	}
	if($dadosInput['tipoDado']=='celular'){
			$fazerUpdate = true;
			if(!$dadosInput['dado']['confirmado']){
				$select = '	select * from ESP_GERACAO_CODIGO where CODIGO_GERADO= '.aspas($dadosInput['dado']['codigo']).' AND DADO_VERIFICACAO ='.aspas($dadosInput['dado']['celular']).' and DATA_GERACAO =  '.dataToSql(date("d/m/Y"));
				$res  = jn_query($select);
				if($row = jn_fetch_object($res)){
					$fazerUpdate = true;
				}else{
					$fazerUpdate = false;
				}
			}
			if($fazerUpdate){
				$update = 'UPDATE PS1000 SET CELULAR_CONFIRMADO ='.aspas($dadosInput['dado']['celular']).', DATA_CONFIRMACAO_CELULAR = '.dataToSql(date("d/m/Y")).' where CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']);
				$resUpdate = jn_query($update);
				$update = 'UPDATE PS1006 SET NUMERO_TELEFONE ='.aspas($dadosInput['dado']['celular']).' where TIPO_TELEFONE='.aspas('C').' AND CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']);
				$resUpdate = jn_query($update);
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = 'Código inválido.';
			}
			
	}
	
	echo json_encode($retorno);
}	

if($dadosInput['tipo']== 'enviarCodigo'){
	
	$retorno = array();
	$retorno['STATUS'] = 'OK';
	$retorno['MSG']    = 'Código enviado.';
	
	$select = '	select * from ESP_GERACAO_CODIGO where DADO_VERIFICACAO ='.aspas($dadosInput['dado']).' and DATA_GERACAO =  '.dataToSql(date("d/m/Y"));
	$res  = jn_query($select);
	if($row = jn_fetch_object($res)){	
		$retorno['STATUS'] = 'OK';
		$retorno['MSG']    = 'Codigo já foi enviado.';
	}else{
		$codigo = rand(100000,999999);
		$insert = 'INSERT INTO ESP_GERACAO_CODIGO(DADO_VERIFICACAO,CODIGO_GERADO,DATA_GERACAO,TIPO_GERACAO)VALUES('.aspas($dadosInput['dado']).','.aspas($codigo).','.dataToSql(date("d/m/Y")).','.aspas($dadosInput['tipoEnvio']).')';
		$res  = jn_query($insert);
		if($dadosInput['tipoEnvio']=='email'){
			$corpoMSG  = '	<!doctype html>
					<html>
						<head>
							<meta charset="utf-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">							
						</head>
						<body >
							Prezado, ' . $_SESSION['nomeUsuario'] . '<br>
							
							Seu codigo de verificação é: <b>'.$codigo.'</b>

						</body>
					</html>';


		
			$retornoEmail = disparaEmail($dadosInput['dado'], 'Codigo Verificação Email', $corpoMSG);
			
			if($retornoEmail != ''){
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = $retornoEmail;
			}
		}
		if($dadosInput['tipoEnvio']=='celular'){
			//require('../EstruturaEspecifica/smsZenvia.php');
			require('../lib/smsPointer.php');
			$msgSms .= utf8_encode('Codigo de verificacao  : '.$codigo);
			//$retornoSms = enviaSmsZenvia('55'.trim($dadosInput['dado']),$msgSms,$codigo.rand(0,100));
			$retornoSms = enviaSmsPointer((trim($dadosInput['dado'])),$msgSms);
			if($retornoSms!=''){
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = $retornoSms;
			}
			
		}
	}
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'verifica'){
	$retorno['email'] = false;
	$retorno['celular'] = false;
	$retorno['cep'] = false;
	$retorno['pdf'] = false;
	$retorno['forcar'] = false;
	
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		
		$selectEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1000 WHERE PS1000.CODIGO_ASSOCIADO= '.aspas($_SESSION['codigoIdentificacaoTitular']);
		$resEmpresa  = jn_query($selectEmpresa);
		if($rowEmpresa = jn_fetch_object($resEmpresa)){	
			if($rowEmpresa->CODIGO_EMPRESA == '400' ){
				$tabela = 'PS1001';
			}else{
				$tabela = 'PS1015';
			}
		}
		
		$query = 'select * from PS1000 
				 left join '.$tabela.' on  '.$tabela.'.CODIGO_ASSOCIADO=Ps1000.CODIGO_ASSOCIADO 
				 where Ps1000.CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']).' and ('.$tabela.'.DATA_ALTERACAO_CADASTRAL is null or DATEDIFF ( day , '.$tabela.'.DATA_ALTERACAO_CADASTRAL , GETDATE() ) >=90)';  
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){	
			$retorno['cep'] = true;
		}
		
		$query = 'select * from PS1000 where CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']).' and (DATA_CONFIRMACAO_CELULAR is null  or DATEDIFF ( day , DATA_CONFIRMACAO_CELULAR , GETDATE() ) >=90)';  
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){	
			$retorno['celular'] = true;
		}
		
		$query = 'select * from PS1000 where CODIGO_ASSOCIADO='.aspas($_SESSION['codigoIdentificacao']).' and (DATA_CONFIRMACAO_EMAIL is null  or DATEDIFF ( day , DATA_CONFIRMACAO_EMAIL , GETDATE() ) >=90) '; 
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){	
			$retorno['email'] = true;
		}
	}
	
	
	if(false){
	
		$selectEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1000 WHERE PS1000.CODIGO_ASSOCIADO= '.aspas($_SESSION['codigoIdentificacaoTitular']);
		$resEmpresa  = jn_query($selectEmpresa);
		if($rowEmpresa = jn_fetch_object($resEmpresa)){	
			if($rowEmpresa->CODIGO_EMPRESA == '400' ){
				$select = 'SELECT Count(*) REGISTROS FROM ESP_ASSINATURA_DOCUMENTO WHERE ESP_ASSINATURA_DOCUMENTO.FILTRO_DOCUMENTO= '.aspas($_SESSION['codigoIdentificacaoTitular']).' and ESP_ASSINATURA_DOCUMENTO.TIPO_DOCUMENTO='.aspas('BOLETAGEM');
				$res  = jn_query($select);
				if($row = jn_fetch_object($res)){	
					if($row->REGISTROS == 0 ){
						$retorno['pdf'] = true;
						$retorno['tituloPdf']  = 'Aditivo Contrato';
						$retorno['botaoPdf']   = 'Concordo';
						$retorno['linkPdf']    = 'ProcessoDinamico/pdfAssinaturaDocumento.php?tipo=BOLETAGEM';					
					}
				}
			}
		}
		
	}
	
	if($retorno['email']or $retorno['celular']or $retorno['cep']or $retorno['pdf'])
		$retorno['forcar'] = false;
	
	
	
	echo json_encode($retorno);
}

function retiraCaractere($str) {
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);
	$str = preg_replace('/[,(),;:.|!"#$%&?~^><ªº-]/', '', $str);
    //$str = preg_replace('/[^a-z0-9]/i', '', $str);
    $str = preg_replace('/_+/', '', $str);
    
    return $str;
}

function disparaEmail($emailAssociado, $assunto = '', $corpoMSG = ''){
	
	if($assunto == '')
		$assunto = 'Assunto Teste';	
	
	if($corpoMSG == '')
		$corpoMSG = 'Mensagem Teste';	
	
	$corpoMSG = utf8_decode($corpoMSG);
	$assunto = utf8_decode($assunto);

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
	$mail->AddAddress($emailAssociado, $emailAssociado);
	

	
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoMSG);
	
	if(!$mail->Send()) {		
		return $mail->ErrorInfo;
	}else{
		return '';
	}
}
?>