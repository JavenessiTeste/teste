<?php
$imagem = imagecreatefromjpeg("../../Site/assets/img/cabecalho_proposta_vidamax.jpg");	
$cor = imagecolorallocate($imagem, 0, 0, 0 );
imagettftext($imagem, 10, 0, 340, 75, $cor,"../../Site/assets/img/arial.ttf",$_GET['numeroContrato']);
$image_p = imagecreatetruecolor(1300, 180);
imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 2325, 850, 1600, 600);
header( "Content-type: image/jpeg" );
return imagejpeg( $image_p, NULL, 80 );
?>