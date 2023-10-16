<?php
require('../lib/base.php');

$codAssociadoTmp = $_GET['codAssociado'];

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.DATA_ADMISSAO, VND1000_ON.CODIGO_CNS, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.CIDADE ';
$queryAssociado .= ' FROM VND1000_ON ';
$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);

$resAssociado = jn_query($queryAssociado);
if(!$rowAssociado = jn_fetch_object($resAssociado)){
	echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
	exit;
}else{
	jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
}

if($_GET['pagina'] == '1'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_staCasa1.jpg");	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 15, 0, 530, 705, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 15, 0, 1023, 705, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 15, 0, 256, 744, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 15, 0, 190, 781, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
	imagettftext($imagem, 15, 0, 188, 821, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
	imagettftext($imagem, 15, 0, 591, 821, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	
	$image_p = imagecreatetruecolor(2480, 3509);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 10225, 4050, 5430, 2650);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_staCasa2.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

?>