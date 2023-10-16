<?php
require_once('../lib/base.php');

$queryPrincipal =	"select * from ESP_ASSINATURA_DOCUMENTO
					 where ESP_ASSINATURA_DOCUMENTO.HASH=  " . aspas($_GET['cod']);	
				
$resultQuery = jn_query($queryPrincipal); 
		

if($rowPrincipal   = jn_fetch_object($resultQuery)){
	
	$dados = $rowPrincipal->CAMPOS;
	$dados = str_replace('_|_','"',$dados);
	$dados = json_decode($dados);




	$imagem = imagecreatefrompng("../../Site/assets/img/PlenaMulta.png");	
	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	imagettftext($imagem, 14, 0, 253, 412, $cor,"../../Site/assets/img/arialbd.ttf",$dados->CODIGO_ASSOCIADO);
	imagettftext($imagem, 14, 0, 232, 434, $cor,"../../Site/assets/img/arialbd.ttf",$dados->NOME_ASSOCIADO);
	imagettftext($imagem, 14, 0, 134, 455, $cor,"../../Site/assets/img/arialbd.ttf",$dados->NUMERO_CPF);
	imagettftext($imagem, 14, 0, 237, 477, $cor,"../../Site/assets/img/arialbd.ttf",$dados->DATA_NASCIMENTO);
	imagettftext($imagem, 14, 0, 218, 499, $cor,"../../Site/assets/img/arialbd.ttf",$dados->DATA_ADMISSAO);
	imagettftext($imagem, 14, 0, 405, 520, $cor,"../../Site/assets/img/arialbd.ttf",$dados->NOME_CONTRATANTE);
	
	imagettftext($imagem, 14, 0, 372, 542, $cor,"../../Site/assets/img/arialbd.ttf",$dados->NUMERO_CPF_CONTRATANTE);
	imagettftext($imagem, 14, 0, 227, 635, $cor,"../../Site/assets/img/arialbd.ttf",$dados->nomeCompleto);
	imagettftext($imagem, 14, 0, 134, 657, $cor,"../../Site/assets/img/arialbd.ttf",$dados->cpf);
	imagettftext($imagem, 14, 0, 164, 682, $cor,"../../Site/assets/img/arialbd.ttf",$dados->telefone);
	
	imagettftext($imagem, 14, 0, 866, 657, $cor,"../../Site/assets/img/arialbd.ttf",$dados->Parentesco);
	imagettftext($imagem, 14, 0, 207, 803, $cor,"../../Site/assets/img/arialbd.ttf",$dados->motivo);
	
	imagettftext($imagem, 14, 0, 339, 1207, $cor,"../../Site/assets/img/arialbd.ttf",$dados->PROTOCOLO);
	imagettftext($imagem, 14, 0, 192, 1233, $cor,"../../Site/assets/img/arialbd.ttf",$dados->ATENDENTE);
	imagettftext($imagem, 14, 0, 140, 1261, $cor,"../../Site/assets/img/arialbd.ttf",$dados->DATA);
	imagettftext($imagem, 14, 0, 135, 1288, $cor,"../../Site/assets/img/arialbd.ttf",$dados->HORA);
	
	imagettftext($imagem, 12, 0, 80, 1764, $cor,"../../Site/assets/img/arialbd.ttf",$dados->ASSINATURA);
	
	
	header( 'Content-type: image/jpeg' );
	imagejpeg( $imagem, NULL, 100 );

	
}

?>