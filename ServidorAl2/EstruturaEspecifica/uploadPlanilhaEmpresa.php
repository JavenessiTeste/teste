<?php
require('../lib/base.php');

if($_POST['tipo'] =='enviar'){
			
	$arquivo = isset($_FILES["file2"]) ? $_FILES["file2"] : FALSE;
	$arquivo_name = $_FILES['file2']['name'];
	$arquivo_type = $_FILES['file2']['type'];
	$ext = explode('.',$arquivo_name);
	$nomeA = $ext[(count($ext)-2)];
	$ext = $ext[(count($ext)-1)];
	$tiposLiberados = explode(';','xls;Xls;XLS,xlsx;Xlsx;XLSX' );
	$controle_tipos = false;
	
	if( count($tiposLiberados) > 0 ){
		foreach($tiposLiberados as $item){
			if(strtolower($item) == strtolower($ext) )
				$controle_tipos = true;
		}
		if(!$controle_tipos)
			$erro = 'Upload de arquivos com a extenção '.$ext.' não é permitido.';
	}else{
		$controle_tipos = true;
	}
	
	$arquivo_size = $_FILES['file2']['size'];
	$arquivo_temp_name = $_FILES['file2']['tmp_name'];
	
	$numeroRand = rand(0,1000);
	$data = date('Ymd');
	$nome_arquivo_atualizado = $data . '_' . $_SESSION['codigoIdentificacao'] . '_' . $numeroRand .  '_' . $arquivo['name'];
	
	if ((isset($arquivo))&& ($controle_tipos)){
		$dir="../../ServidorCliente/uploadsPropostas/";

		if (move_uploaded_file($arquivo_temp_name, $dir.$nome_arquivo_atualizado)) {

			chmod($dir.$nome_arquivo_atualizado, 0777);

			$DataAtual = time();						
			$Upload = array(
				'CodigoEmpresa'   => $_SESSION['codigoIdentificacao'],
				'NomeArquivo'       => $nome_arquivo_atualizado,
				'DataUpload'        => date('Y-m-d', $DataAtual)
			);

			$query  = 'INSERT INTO ';
			$query .= 'CFGARQUIVOS_PROCESSOS_NET ';
			$query .= '(CODIGO_EMPRESA, NOME_ARQUIVO, ';
			$query .= 'TIPO_ARQUIVO, NUMERO_PROTOCOLO, CODIGO_PLANO, DATA_ENVIO)';
			$query .= 'VALUES (';						
			$query .= $Upload['CodigoEmpresa'] . ', ';
			$query .= aspas($Upload['NomeArquivo']) . ', ';
			$query .= aspas('PLANILHA_ASSOCIADOS_EMP') . ', ';
			$query .= aspas($numeroProtocolo) . ', ';
			$query .= aspas($_POST['plano']) . ', ';
			$query .= aspas($Upload['DataUpload']);
			$query .= ')';

			if (jn_query($query)){				
				Header ('Location: importacaoPlanilhaEmpresa.php?nomeArquivo=' . $nome_arquivo_atualizado);
			}
			
		}
		else {
			$erro= "Erro no envio - Permissão;"; // Caso ocorra algum erro, imprimi na tela "erro"
		}
	}
	
	$retorno['ERRO'] = $erro;	
	echo json_encode($retorno);

}

?>