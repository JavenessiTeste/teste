<?php
require('../lib/base.php');

if($_POST['tipo'] =='enviar'){
		
	$arquivo = isset($_FILES["file1"]) ? $_FILES["file1"] : FALSE;
	$arquivo_name = $_FILES['file1']['name'];
	$arquivo_type = $_FILES['file1']['type'];
	$ext = explode('.',$arquivo_name);
	$nomeA = $ext[(count($ext)-2)];
	$ext = $ext[(count($ext)-1)];		
	$arquivo_size = $_FILES['file1']['size'];
	$arquivo_temp_name = $_FILES['file1']['tmp_name'];
	
	$numeroRand = rand(0,1000);
	$data = date('Ymd');
	$hora = date('His');
	$numeroProtocolo = $data . $hora . $numeroRand . $_SESSION['codigoIdentificacao'];
	
	$nome_arquivo_atualizado = $data . '_' . $_SESSION['codigoIdentificacao'] . '_' . $numeroRand .  '_' . $arquivo['name'];
	
	if (isset($arquivo)){
		$dir="../../ServidorCliente/planilhasImportarFinanceiro/Sagehosp/";

		if (move_uploaded_file($arquivo_temp_name, $dir.$nome_arquivo_atualizado)) {

			chmod($dir.$nome_arquivo_atualizado, 0777);

			$DataAtual = time();						
			$Upload = array(
				'CodigoOperador'   => $_SESSION['codigoIdentificacao'],
				'NomeArquivo'       => $nome_arquivo_atualizado,
				'DataUpload'        => date('Y-m-d', $DataAtual)
			);
			
			$numeroReg = jn_gerasequencial('CFGARQUIVOS_PROCESSOS_NET');
			
			$query  = 'INSERT INTO ';
			$query .= 'CFGARQUIVOS_PROCESSOS_NET ';
			$query .= '(NUMERO_REGISTRO, CODIGO_OPERADOR, NOME_ARQUIVO, ';
			$query .= 'TIPO_ARQUIVO, DATA_ENVIO)';
			$query .= 'VALUES (';						
			$query .= aspas($numeroReg) . ', ';
			$query .= $Upload['CodigoOperador'] . ', ';
			$query .= aspas($Upload['NomeArquivo']) . ', ';
			$query .= aspas('PLANILHA_SAGEHOSP') . ', ';			
			$query .= aspas($Upload['DataUpload']);
			$query .= ')';
			
			if (jn_query($query)){				
				Header ('Location: importacaoSagehosp.php?nomeArquivo=' . $nome_arquivo_atualizado);
			}
			
		}
		else {
			$erro= "Erro no envio - Permissão;";
		}
	}
	
	$retorno['ERRO'] = $erro;	
	echo json_encode($retorno);

}

?>