<?php
require('../lib/base.php');

$codAssociadoTmp = $_GET['codAssociado'];

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, NUMERO_CPF, NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES ';
$queryAssociado .= ' FROM VND1000_ON ';
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
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_planeseg_1.jpg");	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 25, 0, 1960, 2280, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	$image_p = imagecreatetruecolor(2300, 2650);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 10225, 4050, 5430, 2650);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_2.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '3'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_3.jpg');	
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '4'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_4.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '5'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_5.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '6'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_6.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '7'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_7.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '8'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_8.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '9'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg_9.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] =='10'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_planeseg2_1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 15, 0, 135, 663, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 15, 0, 245, 695, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 15, 0, 525, 695, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->SEXO);	
	imagettftext($imagem, 15, 0, 200, 725, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));	
	imagettftext($imagem, 15, 0, 115, 792, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 15, 0, 395, 792, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 15, 0, 395, 824, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 15, 0, 190, 891, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 15, 0, 150, 925, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));	
	imagettftext($imagem, 15, 0, 560, 925, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));	
	imagettftext($imagem, 15, 0, 840, 925, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ESTADO);	
	imagettftext($imagem, 15, 0, 945, 925, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CEP);	
	imagettftext($imagem, 15, 0, 140, 963, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
	
	$image_p = imagecreatetruecolor(2300, 2650);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 10225, 4050, 5430, 2650);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '11'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg2_2.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '12'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg2_3.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '13'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg2_4.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '14'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg2_5.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '15'){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_planeseg2_6.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

?>