<?php
require_once('../lib/base.php');

if($_SESSION['codigoSmart'] == '3423'){//Plena
	if($_GET['tp'] == 'termoFacultativoCobrancaCorreios'){
		$queryDadosAssoc = 'SELECT * FROM VW_DADOS_BENEFICIARIOS WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$resDadosAssoc = jn_query($queryDadosAssoc);
		$rowDadosAssoc = jn_fetch_object($resDadosAssoc);

		$imagem = imagecreatefromjpeg("C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/termoFacultativoCobrancaCorreios.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 14, 0, 350, 308, $cor,"C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/arial.ttf",$rowDadosAssoc->NOME_ASSOCIADO);
		imagettftext($imagem, 14, 0, 230, 350, $cor,"C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/arial.ttf",$rowDadosAssoc->CODIGO_ASSOCIADO);
		imagettftext($imagem, 20, 0, 165, 985, $cor,"C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/arial.ttf",'X');
		imagettftext($imagem, 14, 0, 310, 1020, $cor,"C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/arial.ttf",$rowDadosAssoc->ENDERECO_EMAIL);
		imagettftext($imagem, 14, 0, 375, 1060, $cor,"C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/arial.ttf",$rowDadosAssoc->CODIGO_AREA01);
		imagettftext($imagem, 14, 0, 455, 1060, $cor,"C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/arial.ttf",$rowDadosAssoc->NUMERO_TELEFONE01);
		imagettftext($imagem, 20, 0, 165, 1150, $cor,"C:\JaveNessi_Producao\AliancaNet2\Site\assets\img/arial.ttf",'X');
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
}

?>