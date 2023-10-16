<?php
require_once('../lib/base.php');

if($_GET['tp'] == 'imagemBoletoPlena'){	
	require_once('../private/autentica.php');

	$email = $_GET['email'];
	$codArea = $_GET['codArea'];
	$telefone = $_GET['telefone'];
	$comecoTelefone = substr($telefone, 0, -4);
	$finalTelefone = substr($telefone, -4, 4);
	
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$tipoModelo = substr($_SERVER['HTTP_USER_AGENT'],0,100); 
	$assinatura = "Assinado eletronicamente mediante login/senha por ".$_SESSION['nomeUsuario']. ", "."em ".strftime('%A, %d de %B de %Y as %H:%M:%S', strtotime('now'))."\n"."através  do ".$tipoModelo." - IP:".$_SERVER["REMOTE_ADDR"];

	$imagem = imagecreatefromjpeg("../../Site/assets/img/SolicitacaoBoletoEmailPlena.jpg");	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 15, 0, 350, 305, $cor,"../../Site/assets/img/arialbd.ttf",$_SESSION['nomeUsuario']);
	imagettftext($imagem, 15, 0, 230, 350, $cor,"../../Site/assets/img/arialbd.ttf",$_SESSION['codigoIdentificacao']);
	imagettftext($imagem, 20, 0, 165, 980, $cor,"../../Site/assets/img/arialbd.ttf",'X');
	imagettftext($imagem, 15, 0, 290, 1020, $cor,"../../Site/assets/img/arialbd.ttf",$email);
	imagettftext($imagem, 15, 0, 382, 1060, $cor,"../../Site/assets/img/arialbd.ttf",$codArea);
	imagettftext($imagem, 15, 0, 435, 1060, $cor,"../../Site/assets/img/arialbd.ttf",$comecoTelefone);	
	imagettftext($imagem, 15, 0, 535, 1060, $cor,"../../Site/assets/img/arialbd.ttf",$finalTelefone);
	imagettftext($imagem, 20, 0, 165, 1110, $cor,"../../Site/assets/img/arialbd.ttf",'X');
	imagettftext($imagem, 20, 0, 165, 1150, $cor,"../../Site/assets/img/arialbd.ttf",'X');
	if($_GET['assinado'] == 'SIM'){
		imagettftext($imagem, 13, 0,  80, 1430, $cor,"../../Site/assets/img/arialbd.ttf",$assinatura);
	}elseif($_GET['assinado'] == 'JaAssinado'){
		$queryAss = 'SELECT ASS_BOLETO_EMAIL FROM PS1002 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$resAss = jn_query($queryAss);
		$rowAss = jn_fetch_object($resAss);
		imagettftext($imagem, 13, 0,  80, 1430, $cor,"../../Site/assets/img/arialbd.ttf",$rowAss->ASS_BOLETO_EMAIL);
	}
	$image_p = imagecreatetruecolor(1240, 1754);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
	header( "Content-type: image/jpeg" );
	$imagempadrao = imagejpeg( $image_p, NULL, 80 );
	
}elseif($_GET['tp'] == 'imagemBoletoBKR'){
	$cliente 		= $_GET['cliente'];	
	$vencimento 	= $_GET['vencimento'];
	$valor 			= $_GET['valor'];
	$linhaDigitavel = $_GET['linhaDigitavel'];

	$imagem = imagecreatefromjpeg("../../Site/assets/img/email_boleto.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 13, 0, 18, 720, $cor,"../../Site/assets/img/arial.ttf",'Beneficiário:');
	imagettftext($imagem, 13, 0, 125, 720, $cor,"../../Site/assets/img/arial.ttf",$cliente);
	imagettftext($imagem, 13, 0, 18, 750, $cor,"../../Site/assets/img/arial.ttf",'Vencimento:');
	imagettftext($imagem, 13, 0, 125, 750, $cor,"../../Site/assets/img/arial.ttf",$vencimento);
	imagettftext($imagem, 13, 0, 18, 780, $cor,"../../Site/assets/img/arial.ttf",'Valor:');
	imagettftext($imagem, 13, 0, 70, 780, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valor));
	imagettftext($imagem, 13, 0, 18, 810, $cor,"../../Site/assets/img/arial.ttf",'Linha digitavel:');
	imagettftext($imagem, 13, 0, 145, 810, $cor,"../../Site/assets/img/arial.ttf",$linhaDigitavel);

	$image_p = imagecreatetruecolor(661, 1067);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
	header( "Content-type: image/jpeg" );
	$imagempadrao = imagejpeg( $image_p, NULL, 80 );

}


function geraImagemExterna($tpImagem, $nomeAssociado, $linkImagem){	

	if($tpImagem == 'imagemAniversarioPlena'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/imagemAniversarioPlena.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 102 );
		
		imagettftext($imagem, 17, 0, 50, 450, $cor,"../../Site/assets/img/arialbd.ttf",$nomeAssociado);
		imagettftext($imagem, 15, 0, 45, 1180, $cor,"../../Site/assets/img/arialbd.ttf",$linkImagem);

		$image_p = imagecreatetruecolor(750, 1550);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1060, 1550, 850, 1700);		

		ob_start(); 
		imagejpeg( $imagem, NULL, 100 ); 
		imagedestroy( $imagem ); 
		$i = ob_get_clean();
		return  base64_encode($i);

	}
}
?>