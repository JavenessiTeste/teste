<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../EstruturaEspecifica/azureStorage.php');


if($dadosInput['tipo']== 'dados'){
	$numeroCapa = $dadosInput['numeroCapa'];
	
	if($_SESSION['perfilOperador'] == 'PRESTADOR'){
		$codigoPrest = $_SESSION['codigoIdentificacao'];
				
		$queryUpdate  = ' UPDATE ESP_PENDENCIAS_PRESTADOR_NET SET DATA_VISUALIZACAO = CURRENT_TIMESTAMP ';
		$queryUpdate .= ' WHERE NUMERO_CAPA = ' .aspas($numeroCapa);
		$queryUpdate .= ' AND CODIGO_PRESTADOR = ' .aspas($codigoPrest);
		jn_query($queryUpdate);

	}else{
		$codigoPrest = $dadosInput['codPrestador'];
	}
	
	$queryPrest = 'SELECT CODIGO_PRESTADOR, NOME_PRESTADOR FROM PS5000 WHERE CODIGO_PRESTADOR = ' . aspas($codigoPrest);
	$resPrest = jn_query($queryPrest);
	$rowPrest = jn_fetch_object($resPrest);
	
	$nomePrestador = $rowPrest->NOME_PRESTADOR;
	
	
	$query  = ' SELECT A.CODIGO_PRESTADOR, A.NOME_PRESTADOR, B.NUMERO_REGISTRO as NUMERO_CAPA, B.DATA_VENCIMENTO, B.MES_ANO_REFERENCIA, B.NUMERO_NOTA_FISCAL ';		
	$query .= ' FROM PS5000 AS A ';    
	$query .= ' INNER JOIN PS5800 B ON (A.CODIGO_PRESTADOR = B.CODIGO_PRESTADOR) ';    
	$query .= ' WHERE A.DATA_DESCREDENCIAMENTO IS NULL ';    	
	$query .= ' AND B.DATA_ENTREGA > "01.01.2020"';    
	$query .= ' AND B.NUMERO_REGISTRO = ' . aspas($numeroCapa);      

	$res = jn_query($query);
	$row = jn_fetch_object($res);
	
	$dadosPendencia['PERFIL'] 				= $_SESSION['perfilOperador'];
	$dadosPendencia['CODIGO_PRESTADOR'] 	= $codigoPrest;
	$dadosPendencia['NOME_PRESTADOR'] 		= jn_utf8_encode($nomePrestador);
	$dadosPendencia['NUMERO_CAPA'] 			= $row->NUMERO_CAPA;
	$dadosPendencia['DATA_VENCIMENTO'] 		= SqlToData($row->DATA_VENCIMENTO);
	$dadosPendencia['COMPETENCIA'] 			= $row->MES_ANO_REFERENCIA;	
	$dadosPendencia['NUMERO_NOTA_FISCAL'] 	= $row->NUMERO_NOTA_FISCAL;	
	
	$queryPendencias  = ' SELECT * FROM ESP_PENDENCIAS_PRESTADOR_NET ';
	$queryPendencias .= ' WHERE NUMERO_CAPA = ' . aspas($numeroCapa);
	$queryPendencias .= ' AND CODIGO_PRESTADOR = ' . aspas($codigoPrest);
	$resPendencias = jn_query($queryPendencias);
	
	$i = 0;
	while($rowPendencias = jn_fetch_object($resPendencias)){
		
		if($rowPendencias->ITEM_PENDENTE == 'NF'){
			$dadosPendencia['PENDENCIAS'][$i]['ITENS_FALTANTES'] = 'Falta envio de Nota';
		}elseif($rowPendencias->ITEM_PENDENTE == 'BB'){
			$dadosPendencia['PENDENCIAS'][$i]['ITENS_FALTANTES'] = 'Falta envio de Boleto';
		}elseif($rowPendencias->ITEM_PENDENTE == 'XML'){
			$dadosPendencia['PENDENCIAS'][$i]['ITENS_FALTANTES'] = 'Falta envio de Boleto';			
		}elseif($rowPendencias->ITEM_PENDENTE == 'TISS'){
			$dadosPendencia['PENDENCIAS'][$i]['ITENS_FALTANTES'] = 'Não enviou Guia TISS';
		}elseif($rowPendencias->ITEM_PENDENTE == 'PP'){
			$dadosPendencia['PENDENCIAS'][$i]['ITENS_FALTANTES'] = 'Pagamento prorrogado devido a data de entrega';
		}
		
		if($rowPendencias->OBSERVACAO_PENDENCIA){
			$dadosPendencia['PENDENCIAS'][$i]['OBSERVACAO_PENDENCIA'] = jn_utf8_encode($rowPendencias->OBSERVACAO_PENDENCIA);
		}			

		$i++;
	}
	
	$queryArqPendencias  = ' SELECT * FROM CFGARQUIVOS_PROCESSOS_NET ';
	$queryArqPendencias .= ' WHERE NUMERO_CAPA = ' . aspas($numeroCapa);
	$queryArqPendencias .= ' AND CODIGO_PRESTADOR = ' . aspas($codigoPrest);
	$queryArqPendencias .= ' AND TIPO_ARQUIVO IN ("PENDENCIA_NF","PENDENCIA_BB","PENDENCIA_XML","PENDENCIA_TISS","PENDENCIA_PP","PENDENCIA_DF") ';
	$resArqPendencias = jn_query($queryArqPendencias);
	
	$i = 0;
	while($rowArqPendencias = jn_fetch_object($resArqPendencias)){
		
		if($rowArqPendencias->TIPO_ARQUIVO == 'PENDENCIA_NF'){
			$dadosPendencia['ARQUIVOS_ENVIADOS'][$i]['TIPO_ARQUIVO'] = 'Nota Fiscal';
		}elseif($rowArqPendencias->TIPO_ARQUIVO == 'PENDENCIA_BB'){
			$dadosPendencia['ARQUIVOS_ENVIADOS'][$i]['TIPO_ARQUIVO'] = 'Boleto';
		}elseif($rowArqPendencias->TIPO_ARQUIVO == 'PENDENCIA_XML'){
			$dadosPendencia['ARQUIVOS_ENVIADOS'][$i]['TIPO_ARQUIVO'] = 'Arquivo XML';			
		}elseif($rowArqPendencias->TIPO_ARQUIVO == 'PENDENCIA_TISS'){
			$dadosPendencia['ARQUIVOS_ENVIADOS'][$i]['TIPO_ARQUIVO'] = 'Arquivo TISS';
		}elseif($rowArqPendencias->TIPO_ARQUIVO == 'PENDENCIA_PP'){
			$dadosPendencia['ARQUIVOS_ENVIADOS'][$i]['TIPO_ARQUIVO'] = 'Pagamento prorrogado devido a data de entrega';			
		}elseif($rowArqPendencias->TIPO_ARQUIVO == 'PENDENCIA_DF'){
			$dadosPendencia['ARQUIVOS_ENVIADOS'][$i]['TIPO_ARQUIVO'] = 'Devolução de Faturamento Total';			
		}
				
		$dadosPendencia['ARQUIVOS_ENVIADOS'][$i]['ARQUIVO'] = jn_utf8_encode($rowArqPendencias->NOME_ARQUIVO);
	

		$i++;
	}
	
	echo json_encode($dadosPendencia);
}

if($_POST['tipo'] == 'salvar'){
	
	if($_SESSION['perfilOperador'] == 'OPERADOR'){
		$query = "INSERT INTO ESP_PENDENCIAS_PRESTADOR_NET 
					(NUMERO_CAPA,
					 CODIGO_OPERADOR,
					 CODIGO_PRESTADOR,
					 OBSERVACAO_PENDENCIA,
					 DATA_PENDENCIA,				 
					 ITEM_PENDENTE) 
				 VALUES 
					(" . aspas($_POST["registro"]) . ", 
					 " . aspas($_SESSION['codigoIdentificacao']) . ",
					 " . aspas($_POST['codigoPrestador']) . ",
					 " . aspas(utf8_decode($_POST['observacao'])) . ", 
					 current_timestamp,
					 " . aspas($_POST["status"]) . ")";				
		
		if (!jn_query($query)){
			$retorno['STATUS'] = 'ERRO';			
		}else{
			$retorno['STATUS'] = 'OK';						
		}
	}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){			
		$anexo1 = '';
		$ext = strtolower(substr($_FILES['file1']['name'],-4));
		$nomeArquivo = $_POST["registro"].'_' . $_POST["status"] . '_'.date("Y.m.d-H.i.s") . $ext;;		   
		uploadFileBlogStorage('UploadArquivos/uploadPendenciasPrest',$nomeArquivo,fopen($_FILES['file1']['tmp_name'], "r"),mime_content_type($_FILES['file1']['tmp_name']));
		
		$caminhoPendencias = 'https://app.plenasaude.com.br/UploadArquivos/uploadPendenciasPrest/';	
		$caminhoCompleto = $caminhoPendencias . $nomeArquivo;

		if ($_FILES['file1'] != ''){
			$registro = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');
			$query  = "INSERT INTO CFGARQUIVOS_PROCESSOS_NET(NUMERO_REGISTRO, CODIGO_PRESTADOR, NUMERO_CAPA, ";
			$query .= "TIPO_ARQUIVO,NOME_ARQUIVO, DATA_ENVIO)";		
			$query .= "Values(" . aspas($registro) . ",";
			$query .= aspas($_SESSION['codigoIdentificacao']) . ", ";
		    $query .= aspas($_POST['registro'] ) . ", ";			
			$query .= aspas('PENDENCIA_'.$_POST["status"]) . ", ";
			$query .= aspas($caminhoCompleto) . ", ";			
			$query .= dataToSql(date('d-m-Y')) . ")";
			
			if (! jn_query($query)) {				
				$retorno['STATUS'] = 'ERRO';				
			}else{				
				$retorno['STATUS'] = 'OK';						
			}
		}
	}
}
?>