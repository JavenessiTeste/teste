<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../lib/mpdf60/mpdf.php');

function base64_to_jpeg($base64_string, $nomeImagem, $nomePdf) {    
    $ifp = fopen($nomeImagem, 'wb');
    $data = explode(',', $base64_string);
    fwrite($ifp, base64_decode($data[1]));    
    fclose($ifp); 

	$mpdf = new mPDF();
	$file = $nomeImagem;
	$size =  getimagesize($file);
	$width = $size[0];
	$height = $size[1];
	$mpdf->WriteHTML("");
	$mpdf->Image($file,60,50,$width,$height,"jpg","",true, true);
	$mpdf->Output($nomePdf);

    return $nomeImagem;
}

if($_POST['type'] != 'boletoEmail' || $_POST['type'] == ''){
	$base64_string = str_replace(' ', '+', $_POST['dados']);
	$final = $_POST['type'];

	$nomeImagem = '../../../AliancaNet/html/uploadCancelamento/'.$_SESSION['codigoIdentificacao'] . '_' . date('YmdHi') . "_" . $final . ".jpeg";
	$nomePdf = '../../../AliancaNet/html/uploadCancelamento/'.$_SESSION['codigoIdentificacao'] . '_' . date('YmdHi') . "_" . $final . ".pdf";
	base64_to_jpeg($base64_string, $nomeImagem, $nomePdf);
	$nomePdfCaminho = 'uploadCancelamento/'.$_SESSION['codigoIdentificacao'] . '_' . date('YmdHi') . "_" . $final . ".pdf";

	$caminhoArqBenef = 'https://app.plenasaude.com.br/AliancaNet/html/';
	$query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
				(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
				VALUES
				(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomePdfCaminho) . ')';
	
	jn_query($query);
}elseif($_POST['type'] == 'boletoEmail'){
	$base64_string = str_replace(' ', '+', $_POST['dados']);
	$final = $_POST['type'];

	$nomeImagem = '../../ServidorCliente/uploadSolBoletoEmail/'.$_SESSION['codigoIdentificacao'] . '_' . date('YmdHi') . "_" . $final . ".jpeg";
	$nomeImagem = '../../ServidorCliente/uploadSolBoletoEmail/'.$_SESSION['codigoIdentificacao'] . '_' . date('YmdHi') . "_" . $final . ".jpeg";
	base64_to_jpeg($base64_string, $nomeImagem, $nomePdf);
	$nomePdfCaminho = 'uploadSolBoletoEmail/'.$_SESSION['codigoIdentificacao'] . '_' . date('YmdHi') . "_" . $final . ".jpeg";

	$caminhoArqBenef = 'https://app.plenasaude.com.br/AliancaAppNet2/ServidorCliente/';
	$query = 'INSERT INTO CFGARQUIVOS_BENEF_NET
				(CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO)
				VALUES
				(' . aspas($_SESSION['codigoIdentificacao']) . ','.  aspas($caminhoArqBenef). ', ' . aspas($nomePdfCaminho) . ')';		
	jn_query($query);	
}
?>