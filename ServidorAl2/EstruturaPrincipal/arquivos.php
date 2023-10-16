<?php
require('../lib/base.php');

if(($_GET['tipo'] !='V') or ($_GET['reg'] =='')){
	require('../private/autentica.php');
}

require('../EstruturaEspecifica/complementoSql.php');
require('exclusao.php');
require('../EstruturaEspecifica/antesDaGravacao.php');
require('../EstruturaEspecifica/depoisDaGravacao.php');
require('../EstruturaEspecifica/azureStorage.php');


$retorno['STATUS'] = 'OK';


if($_POST['tipo'] =='salvar'){
	//print_r($_FILES);
	//print_r($_POST);
	foreach ($_FILES as $arquivo){
		
		//print_r($arquivo);
		
		$tabela = strtoupper($_POST['tab']);
		
		$chave  = $_POST['chave'];
		
		$nomeArquivo =  $arquivo['name'];
		
		$auxNome     = explode('__', $arquivo['name']);

		$nomeArquivoOriginal      =  $auxNome[0];
		$nomeComponenteEspecifico =  $auxNome[1];
		
		$nomeArquivo = pathinfo($nomeArquivoOriginal);
		
		$extensao = $nomeArquivo['extension'];		
		
		if($_SESSION['codigoSmart'] == '3423' or retornaValorConfiguracao('ACEITA_MULTIPLOS_ANEXOS')){
			$campoComponenteEspecifico = 'nome_componente_espefico, ';
			$valorComponenteEspecifico = aspas($nomeComponenteEspecifico).",";
		}
		
		$valorChavePrimaria = jn_gerasequencial('CONTROLE_ARQUIVOS');
		
		if(!utilizaBlobStorage()){
			
			if(!file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela))){
				mkdir('../../UploadArquivos/server/files/'. strtoupper($tabela), 0777, true);
			}
			
			if(!file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave)){
				mkdir('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave, 0777, true);
			}
		}
		
		$i = 1;
		$rand = rand();
		
		
		if(!utilizaBlobStorage()){
			while(file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao)){
				$i++;
			}
		}else{
			while(existeFileBlogStorage('UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao)){
				$i++;
			}
		}
		if(!utilizaBlobStorage()){	
			move_uploaded_file($arquivo['tmp_name'],'../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao);
		}else{
			uploadFileBlogStorage('UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave,'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao,fopen($arquivo['tmp_name'] , "r"),mime_content_type($arquivo['tmp_name']));
		}
		//file_put_contents('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao, base64_decode($valor));
		
		if((file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao)) or
			((utilizaBlobStorage())and(existeFileBlogStorage('UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao)))
		  ){
			
			$query 	="INSERT INTO controle_arquivos(
						caminho_arquivo_armazenado,
						nome_arquivo_armazenado,
						data_upload,
						hora_upload,
						codigo_identificacao_arquivo,
						nome_arquivo_original,
						endereco_origem_in,
						historico_arquivo,
						nome_tabela," .
						$campoComponenteEspecifico .
						"chave_registro
					)VALUES(".
						aspas(strtoupper($tabela) . "/C".$chave.'/').",".
						aspas('REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao).",".
						dataToSql(date('d/m/Y')).",".
						aspas(date('H:i:s')).",".
						aspas($tabela.'_'.$chave).",".
						aspas($arquivo['name']).",".
						aspas($_SERVER["REMOTE_ADDR"]).",".
						aspas('IN:'.$_SESSION['codigoIdentificacao']).",".
						aspas(strtoupper($tabela)).",".
						$valorComponenteEspecifico .
						aspas($chave)."
					);";
			$res = jn_query($query);
			
			if($tabela == 'TMP1000_NET' && retornaValorConfiguracao('ARQUIVOS_TMP1000_NET') == 'SIM'){
				$queryArquivos  = 'INSERT INTO CFGARQUIVOS_BENEF_NET (CODIGO_ASSOCIADO_TMP, CAMINHO_ARQUIVO, NOME_ARQUIVO) VALUES ';
				$queryArquivos .= '(' . aspas($chave) . ', ' . aspas(retornaValorConfiguracao('CAMINHO_UPLOAD_ARQUIVOS') . strtoupper($tabela) . "/C".$chave) . ', ' . aspas('REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao) . ') ';				
				jn_query($queryArquivos);

				if(retornaValorConfiguracao('PERMITIR_VIGENCIA_IMEDIATA') == 'SIM'){
					$queryCod = 'SELECT CODIGO_ASSOCIADO FROM PS1000 WHERE CODIGO_ANTIGO = ' . aspas($chave);
					$resCod = jn_query($queryCod);
					if($rowCod = jn_fetch_object($resCod)){
						$atualizaAnexos  = ' UPDATE CFGARQUIVOS_BENEF_NET SET CODIGO_ASSOCIADO = ' . aspas($rowCod->CODIGO_ASSOCIADO);
						$atualizaAnexos .= ' WHERE  CODIGO_ASSOCIADO IS NULL AND ';
						$atualizaAnexos .= ' CODIGO_ASSOCIADO_TMP = ' . aspas($chave);
						jn_query($atualizaAnexos);
					}
				}
			}
			
			if($tabela == 'PS6550'){
				$numeroReg = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');
			
				$queryAssoc = 'SELECT CODIGO_ASSOCIADO FROM PS6550 WHERE NUMERO_SOLICITACAO = ' . aspas($chave);
				$resAssoc = jn_query($queryAssoc); 			
				$rowAssoc  = jn_fetch_object($resAssoc);
				
				$queryArquivos  = "INSERT INTO CFGARQUIVOS_PROCESSOS_NET(CODIGO_ASSOCIADO, NUMERO_REGISTRO, NUMERO_SOLICITACAO,";
				$queryArquivos .= "TIPO_ARQUIVO,NOME_ARQUIVO, DATA_ENVIO)";		
				$queryArquivos .= "Values(" . aspas($rowAssoc->CODIGO_ASSOCIADO) . ", ";
				$queryArquivos .= aspas($numeroReg) . ", ";
				$queryArquivos .= aspas($chave) . ", ";
				$queryArquivos .= aspas('PEDIDO') . ", ";
				$queryArquivos .= aspas('REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao) . ", ";
				$queryArquivos .= dataToSql(date('d/m/Y')) . ")";				
			
				jn_query($queryArquivos);
			}

			if($tabela == 'ESP_TRANSFERENCIA_CAD'){

				$queryAssoc = 'SELECT CODIGO_ASSOCIADO FROM ESP_TRANSFERENCIA_CAD  WHERE NUMERO_REGISTRO = ' . aspas($chave);
				$resAssoc = jn_query($queryAssoc); 			
				$rowAssoc  = jn_fetch_object($resAssoc);

				$queryArquivos  = 'INSERT INTO CFGARQUIVOS_BENEF_NET (CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO) VALUES ';
				$queryArquivos .= '(' . aspas($rowAssoc->CODIGO_ASSOCIADO) . ', ' . aspas(retornaValorConfiguracao('CAMINHO_UPLOAD_ARQUIVOS') . strtoupper($tabela) . "/C".$chave) . ', ' . aspas('REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao) . ') ';				
				jn_query($queryArquivos);			
			}
			
		}		
	}
}else if($_POST['tipo'] =='excluir'){
	$queryImagem = "SELECT CAMINHO_ARQUIVO_ARMAZENADO||NOME_ARQUIVO_ARMAZENADO IMAGEM, NOME_ARQUIVO_ORIGINAL,NOME_TABELA FROM controle_arquivos
								WHERE numero_registro = ".aspas($_POST['reg']);
	$resultImagem = jn_query($queryImagem); 
			
	if($rowImagem  = jn_fetch_object($resultImagem)){
				
		$permissoes = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$_SESSION['perfilOperador'],$rowImagem->NOME_TABELA);		
		if($permissoes['ALT']){
			
			if(!utilizaBlobStorage()){
			
				if(file_exists('../../UploadArquivos/server/files/'. $rowImagem->IMAGEM)){
					unlink('../../UploadArquivos/server/files/'. $rowImagem->IMAGEM);
					$queryUpdate = " update controle_arquivos set data_exclusao = ". dataToSql(date('d/m/Y')). ' , hora_exclusao = '. aspas(date('H:i:s')).", historico_arquivo = (historico_arquivo||".aspas(' EX:'.$_SESSION['codigoIdentificacao']).")".
								   " where numero_registro = ".aspas($_POST['reg']);
					jn_query($queryUpdate); 	
					
					$retorno['MSG']    = 'O arquivo ' .$rowImagem->NOME_ARQUIVO_ORIGINAL.' foi excluido.';
				}else{
				
				}
			}else{
				if(existeFileBlogStorage('UploadArquivos/server/files/'. $rowImagem->IMAGEM)){
					detelaFileBlogStorage('UploadArquivos/server/files/'. $rowImagem->IMAGEM);
					$queryUpdate = " update controle_arquivos set data_exclusao = ". dataToSql(date('d/m/Y')). ' , hora_exclusao = '. aspas(date('H:i:s')).", historico_arquivo = (historico_arquivo||".aspas(' EX:'.$_SESSION['codigoIdentificacao']).")".
								   " where numero_registro = ".aspas($_POST['reg']);
					jn_query($queryUpdate); 	
				}
			}
		
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'Você não tem permissão de alteração nesta tabela.';
		}
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Arquivo não encontrado.';
	}

}else if(($_GET['tipo'] =='V')and($_GET['reg'] !='')){
	//$_GET['reg']   
	//header('Content-type: application/json');
	$queryImagem = "SELECT (CAMINHO_ARQUIVO_ARMAZENADO||NOME_ARQUIVO_ARMAZENADO) IMAGEM, NOME_ARQUIVO_ORIGINAL,NOME_TABELA FROM controle_arquivos
								WHERE numero_registro = ".aspas($_GET['reg']);
	$resultImagem = jn_query($queryImagem); 
			
	if($rowImagem  = jn_fetch_object($resultImagem)){
		
		//$permissoes = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$rowImagem->nome_tabela);
		$permissoes = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$_SESSION['perfilOperador'],$rowImagem->NOME_TABELA);
		if($permissoes['VIS']){
			if(file_exists("../../UploadArquivos/server/files/".$rowImagem->IMAGEM)){
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$type = finfo_file($finfo, "../../UploadArquivos/server/files/".$rowImagem->IMAGEM);
				header('Content-type: '.$type);
				if($_GET['D']=='OK'){
					header('Content-Disposition: attachment; filename="'.$rowImagem->NOME_ARQUIVO_ORIGINAL.'"');
				}
				readfile("../../UploadArquivos/server/files/".$rowImagem->IMAGEM);
			}else{
				if(utilizaBlobStorage()){
					if(existeFileBlogStorage('UploadArquivos/server/files/'.$rowImagem->IMAGEM)){
						abreFileBlogStorage('UploadArquivos/server/files/'.$rowImagem->IMAGEM,$rowImagem->NOME_ARQUIVO_ORIGINAL);
					}
				}	
			}
		}else{
			header('HTTP/1.0 401 Unauthorized');
		}
	}else{
		header('HTTP/1.0 401 Unauthorized');
	}
	
	exit;
}


echo json_encode($retorno);

