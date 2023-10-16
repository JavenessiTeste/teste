<?php

if(isset($_GET)){
	if($_GET['testeImagem'] == 'SIM'){	
		convertImagemBase64(0, 0, true);
	}
}


function convertImagemBase64($registro, $tabela, $teste = false){

	if($tabela == 'PS1095' and $_SESSION['codigoSmart'] == '3389'){//Vidamax
	
		$query  = ' SELECT NOME_ASSOCIADO, PROTOCOLO_GERAL_PS6450 FROM PS1095 ';
		$query .= ' INNER JOIN PS1000 ON (PS1095.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)';
		$query .= ' WHERE NUMERO_REGISTRO = ' . aspas($registro);
		$res = jn_query($query);
		$row = jn_fetch_object($res);
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/emailCancelamento.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 9, 0, 218, 190, $cor,"../../Site/assets/img/arialbd.ttf",$row->NOME_ASSOCIADO);
		imagettftext($imagem, 9, 0, 260, 245, $cor,"../../Site/assets/img/arialbd.ttf",$row->PROTOCOLO_GERAL_PS6450);
		$image_p = imagecreatetruecolor(520, 700);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1000, 3950, 1250, 7700);
	}
	
	if($teste){

		$imagem = imagecreatefromjpeg("../../Site/assets/img/emailCancelamento.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 9, 0, 218, 190, $cor,"../../Site/assets/img/arialbd.ttf",'NOME DO ASSOCIADO TESTE');
		imagettftext($imagem, 9, 0, 260, 245, $cor,"../../Site/assets/img/arialbd.ttf",'00000000 - TESTE');
		$image_p = imagecreatetruecolor(520, 700);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1000, 3950, 1250, 7700);

		header('Content-type: image/jpeg');
		imagejpeg( $image_p, NULL, 1580 );
	}else{
		ob_start(); 
		imagejpeg($imagem, NULL, 100 ); 
		imagedestroy( $imagem ); 
		$i = ob_get_clean();
		return  base64_encode($i);
	}
	
}
?>