<?php

require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='getDados'){
	$retorno['STATUS'] = 'OK'; 
	$retorno['NOME'] = '';
	$retorno['ULTIMO_REGISTRO'] = 0;
	
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		$query = "select Ps5000.nome_prestador NOME from esp_video_conferencia
				  left join Ps5000 on Ps5000.codigo_prestador = esp_video_conferencia.codigo_prestador
				  where  esp_video_conferencia.codigo_associado = ".aspas($_SESSION['codigoIdentificacao'])." and esp_video_conferencia.hash = ".aspas($dadosInput['chave']);
	}else if($_SESSION['perfilOperador']=='PRESTADOR'){
		$query = "select Ps1000.nome_associado NOME from esp_video_conferencia
				  left join Ps1000 on Ps1000.codigo_associado = esp_video_conferencia.codigo_associado
				  where  esp_video_conferencia.codigo_prestador = ".aspas($_SESSION['codigoIdentificacao'])." and esp_video_conferencia.hash = ".aspas($dadosInput['chave']);
	
	}	
	
	$res  = jn_query($query);
	
	if($row = jn_fetch_object($res)){
		$retorno['NOME'] = utf8_encode($row->NOME);
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Dados não encontrado.';
	}
	echo json_encode($retorno);

}
if($dadosInput['tipo'] =='novas'){

	$retorno['MENSAGENS'] = array();
	$retorno['ULTIMO_REGISTRO'] = $dadosInput['reg'];
	$query = "Select esp_video_conferencia_chat.* from  esp_video_conferencia
			  inner join esp_video_conferencia_chat on esp_video_conferencia.numero_registro = esp_video_conferencia_chat.numero_registro_video
			  where esp_video_conferencia_chat.NUMERO_REGISTRO > ".aspas($retorno['ULTIMO_REGISTRO'])." and esp_video_conferencia.hash = ".aspas($dadosInput['chave'])." and ((esp_video_conferencia.codigo_prestador = ".aspas($_SESSION['codigoIdentificacao']).")or(esp_video_conferencia.codigo_associado = ".aspas($_SESSION['codigoIdentificacao']).")) ORDER BY esp_video_conferencia_chat.NUMERO_REGISTRO";

	$res  = jn_query($query);
	
	while($row = jn_fetch_object($res)){
		$item = array();
		$item['mensagem'] =  utf8_encode($row->MENSAGEM);
		$item['idUsuario'] = $row->ID_USUARIO_MSG;
		$retorno['ULTIMO_REGISTRO'] = $row->NUMERO_REGISTRO;
		$retorno['MENSAGENS'][] = $item;
		
	}

	echo json_encode($retorno);

}

if($dadosInput['tipo'] =='chat'){

	$query = "Select esp_video_conferencia.* from  esp_video_conferencia
			  where esp_video_conferencia.hash = ".aspas($dadosInput['chave'])." and ((esp_video_conferencia.codigo_prestador = ".aspas($_SESSION['codigoIdentificacao']).")or(esp_video_conferencia.codigo_associado = ".aspas($_SESSION['codigoIdentificacao'])."))";

	$res  = jn_query($query);
	
	$retorno['STATUS'] = 'OK'; 
	
	if($row = jn_fetch_object($res)){
		$insert = "insert into esp_video_conferencia_chat(NUMERO_REGISTRO_VIDEO,ID_USUARIO_MSG,MENSAGEM)VALUES(".aspas($row->NUMERO_REGISTRO).",".aspas($_SESSION['codigoIdentificacao']).",".aspas($dadosInput['msg']).")";
		jn_query($insert);
	}else{
		$retorno['STATUS'] = 'Erro'; 
	}

	echo json_encode($retorno);

}
if($_POST['tipo'] =='arquivo'){
	
	$retorno['STATUS'] = 'OK'; 
	$retorno['MSG'] = ''; 
	
	require('../EstruturaEspecifica/azureStorage.php');
	
	$query = "Select esp_video_conferencia.* from  esp_video_conferencia
			  where esp_video_conferencia.hash = ".aspas($_POST['chave'])." and ((esp_video_conferencia.codigo_prestador = ".aspas($_SESSION['codigoIdentificacao']).")or(esp_video_conferencia.codigo_associado = ".aspas($_SESSION['codigoIdentificacao'])."))";

	$res  = jn_query($query);
	
	$retorno['STATUS'] = 'OK'; 
	
	if($row = jn_fetch_object($res)){
		$idtemp = md5($_SESSION['codigoIdentificacao'].rand(10000,99999).date('m-d-Y h:i:s a', time()));
		$insert = "insert into esp_video_conferencia_chat(NUMERO_REGISTRO_VIDEO,ID_USUARIO_MSG,MENSAGEM)VALUES(".aspas($row->NUMERO_REGISTRO).",".aspas($_SESSION['codigoIdentificacao']).",".aspas($idtemp).")";
		jn_query($insert);
		$selectId= 'select NUMERO_REGISTRO from esp_video_conferencia_chat where ID_USUARIO_MSG ='.aspas($_SESSION['codigoIdentificacao']).' and MENSAGEM='.aspas($idtemp);
		$resId  = jn_query($selectId);
		$rowId = jn_fetch_object($resId);
		$id = $rowId->NUMERO_REGISTRO;
	}else{
		$retorno['STATUS'] = 'Erro'; 
	}	
	
	if($retorno['STATUS'] =='OK'){
		foreach ($_FILES as $arquivo){
			
			//print_r($id);
			
			$tabela = 'ESP_VIDEO_CONFERENCIA_CHAT';
			
			$chave  = $id;
			
			$nomeArquivo =  $arquivo['name'];
			
			$auxNome     = explode('__', $arquivo['name']);

			$nomeArquivoOriginal      =  $auxNome[0];
			$nomeComponenteEspecifico =  $auxNome[1];
			
			$nomeArquivo = pathinfo($nomeArquivoOriginal);
			
			$extensao = $nomeArquivo['extension'];		
			
			if($_SESSION['codigoSmart'] == '3423'){
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
							aspas($id)."
						);";
				$res = jn_query($query);
				$selectId= 'select NUMERO_REGISTRO from controle_arquivos where  chave_registro='.aspas($id);
				$resId  = jn_query($selectId);
				$rowId = jn_fetch_object($resId);
				$idImagem = $rowId->NUMERO_REGISTRO;
				
				

				
			}		
		}
		
		$msg = "<a href='../ServidorAl2/EstruturaPrincipal/arquivos.php?tipo=V&reg=".$idImagem."' target='_blank'><font color='blue'>Arquivo ".$arquivo['name']."</font></a>";
		$update = "UPDATE esp_video_conferencia_chat set MENSAGEM=".aspas($msg)." where numero_registro = ".aspas($id);
		jn_query($update);
	}
	
	echo json_encode($retorno);

}	


?>