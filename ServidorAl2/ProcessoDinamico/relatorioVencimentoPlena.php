<?php
require_once('../lib/base.php');

$queryPrincipal =	"select * from ESP_PROTOCOLO_VENCIMENTO
					 where ESP_PROTOCOLO_VENCIMENTO.HASH=  " . aspas($_GET['cod']);	
				
$resultQuery = jn_query($queryPrincipal); 
//pr($queryPrincipal,false);				
				
				
if($rowPrincipal   = jn_fetch_object($resultQuery)){



	if($rowPrincipal->TIPO=='F'){

		$imagem = imagecreatefrompng("../../Site/assets/img/vencimentoPF.png");	
		
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 15, 0, 266, 285, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->NOME);
		imagettftext($imagem, 15, 0, 119, 322, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DOCUMENTO);
		imagettftext($imagem, 15, 0, 178, 344, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->ENDERECO);
		imagettftext($imagem, 15, 0, 676, 344, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->BAIRRO);
		imagettftext($imagem, 15, 0, 150, 365, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->CIDADE);
		imagettftext($imagem, 15, 0, 128, 388, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->CEP);
		imagettftext($imagem, 15, 0, 226, 409, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->PROTOCOLO);
		imagettftext($imagem, 15, 0, 680, 471, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DATA_CONTRATO->format('d/m/Y'));
		imagettftext($imagem, 15, 0, 650, 497, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DIA_VENCIMENTO_ANTIGO);
		imagettftext($imagem, 15, 0, 610, 540, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DIA_VENCIMENTO_NOVO);
		imagettftext($imagem, 15, 0, 152, 652, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DIA_VENCIMENTO_NOVO);
		imagettftext($imagem, 15, 0, 800, 1221, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DATA_ALTERACAO->format('d/m/Y').' '.$rowPrincipal->HORA_ALTERACAO);
		imagettftext($imagem, 10, 0, 53, 1494, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->ASSINATURA);

		header( 'Content-type: image/jpeg' );
		imagejpeg( $imagem, NULL, 100 );
	}
	if($rowPrincipal->TIPO=='J'){

		$imagem = imagecreatefrompng("../../Site/assets/img/vencimentoPJ.png");	
		
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 15, 0, 200, 285, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->NOME);
		imagettftext($imagem, 15, 0, 130, 322, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DOCUMENTO);
		imagettftext($imagem, 15, 0, 178, 344, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->ENDERECO);
		imagettftext($imagem, 15, 0, 650, 344, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->BAIRRO);
		imagettftext($imagem, 15, 0, 150, 365, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->CIDADE);
		imagettftext($imagem, 15, 0, 128, 388, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->CEP);
		imagettftext($imagem, 15, 0, 226, 409, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->PROTOCOLO);
		imagettftext($imagem, 15, 0, 680, 471, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DATA_ALTERACAO->format('d/m/Y'));
		imagettftext($imagem, 15, 0, 650, 497, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DIA_VENCIMENTO_ANTIGO);
		imagettftext($imagem, 15, 0, 610, 540, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DIA_VENCIMENTO_NOVO);
		imagettftext($imagem, 15, 0, 152, 652, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DIA_VENCIMENTO_NOVO);
		imagettftext($imagem, 15, 0, 800, 1221, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->DATA_ALTERACAO->format('d/m/Y').' '.$rowPrincipal->HORA_ALTERACAO);
		imagettftext($imagem, 10, 0, 53, 1494, $cor,"../../Site/assets/img/arialbd.ttf",$rowPrincipal->ASSINATURA);

		header( 'Content-type: image/jpeg' );
		imagejpeg( $imagem, NULL, 100 );
	}
}

?>