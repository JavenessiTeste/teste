<?php
require('../lib/base.php');

$codAssociadoTmp = trim($_GET['codAssociado']);

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, NUMERO_CPF, NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, CODIGO_CNS, VND1000_ON.CODIGO_TABELA_PRECO, ';
$queryAssociado .= ' 	NOME_EMPRESA, RAZAO_SOCIAL, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NOME_CONTRATANTE, VND1000_ON.NOME_ASSOCIADO) AS NOME_CONTRATANTE, COALESCE(VND1001_ON.NUMERO_CPF_CONTRATANTE, VND1000_ON.NUMERO_CPF) AS CPF_CONTRATANTE, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NUMERO_RG_CONTRATANTE, VND1000_ON.NUMERO_RG) AS RG_CONTRATANTE ';
$queryAssociado .= ' FROM VND1000_ON ';
$queryAssociado .= ' INNER JOIN VND1010_ON ON (VND1010_ON.CODIGO_EMPRESA = VND1000_ON.CODIGO_EMPRESA) ';
$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
$resAssociado = jn_query($queryAssociado);
if(!$rowAssociado = jn_fetch_object($resAssociado)){
	echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
	exit;
}else{
	jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
}



if($_GET['pagina'] == '1'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_Propulsao1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 12, 0, 70, 280, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->RAZAO_SOCIAL));
	imagettftext($imagem, 12, 0, 70, 325, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_EMPRESA));	
	imagettftext($imagem, 12, 0, 220, 1410, $cor,"../../Site/assets/img/arial.ttf",'CURITIBA');
	imagettftext($imagem, 12, 0, 550, 1410, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 12, 0, 720, 1410, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 12, 0, 1000, 1410, $cor,"../../Site/assets/img/arial.ttf",date('Y'));	
	
	$image_p = imagecreatetruecolor(1240, 1754);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}
?>