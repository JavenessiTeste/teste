<?php
require_once('../lib/base.php');

$queryEmpresa = 'SELECT * FROM CFGEMPRESA ';
$resEmpresa = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

$queryFaturas  =	" SELECT PS1020.*, PS1000.*, PS1030.*, PS5268.DESCRICAO_TIPO_ACOMODACAO, ";
$queryFaturas .=	" CASE WHEN (PS1000.DATA_EXCLUSAO IS NULL OR (PS1000.DATA_EXCLUSAO >= CURRENT_TIMESTAMP)) THEN 'ATIVO' ELSE 'EXCLUIDO' END STATUS FROM PS1020 ";
$queryFaturas .=	" INNER JOIN PS1000 ON (PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)";	
$queryFaturas .=	" INNER JOIN PS1030 ON (PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO)";	
$queryFaturas .=	" LEFT OUTER JOIN PS5268 ON (PS1030.CODIGO_TIPO_ACOMODACAO = PS5268.CODIGO_TIPO_ACOMODACAO)";	
$queryFaturas .=	" WHERE PS1020.CODIGO_ASSOCIADO =  " . aspas($_SESSION['codigoIdentificacao']);	
$queryFaturas .=	" ORDER BY PS1020.DATA_VENCIMENTO DESC ";
				
$resFaturas = jn_query($queryFaturas); 
		

if($rowFaturas   = jn_fetch_object($resFaturas)){
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/declaracaoVinculo.jpg");	
	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$nomePlano = $rowFaturas->NOME_PLANO_FAMILIARES;
	if(!$nomePlano)
		$nomePlano = $rowFaturas->NOME_PLANO_EMPRESAS;	
	
	$tipoAbrangencia = '';
	if($rowFaturas->CODIGO_TIPO_ABRANGENCIA == '1'){
		$tipoAbrangencia = 'NACIONAL';
	}elseif($rowFaturas->CODIGO_TIPO_ABRANGENCIA == '2'){
		$tipoAbrangencia = 'GRUPO DE ESTADOS';
	}elseif($rowFaturas->CODIGO_TIPO_ABRANGENCIA == '3'){
		$tipoAbrangencia = 'ESTADUAL';
	}elseif($rowFaturas->CODIGO_TIPO_ABRANGENCIA == '4'){
		$tipoAbrangencia = 'GRUPO DE MUNIC&Iacute;PIOS';
	}elseif($rowFaturas->CODIGO_TIPO_ABRANGENCIA == '5'){
		$tipoAbrangencia = 'MUNICIPAL';
	}elseif($rowFaturas->CODIGO_TIPO_ABRANGENCIA == '6'){
		$tipoAbrangencia = 'OUTROS';
	}
	
	
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataH = strftime(' %d de %B de %Y', strtotime('today'));
	
	imagettftext($imagem, 14, 0, 165, 531, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->STATUS);
	imagettftext($imagem, 14, 0, 170, 563, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->CODIGO_ASSOCIADO);
	imagettftext($imagem, 14, 0, 150, 593, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NOME_ASSOCIADO);
	imagettftext($imagem, 14, 0, 145, 626, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_CPF);
	imagettftext($imagem, 14, 0, 270, 655, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_NASCIMENTO));
	imagettftext($imagem, 14, 0, 230, 686, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->CODIGO_CNS);
	imagettftext($imagem, 14, 0, 305, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePlano));
	imagettftext($imagem, 14, 0, 260, 840, $cor,"../../Site/assets/img/arial.ttf",$rowEmpresa->NOME_EMPRESA);
	imagettftext($imagem, 14, 0, 270, 932, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePlano));
	imagettftext($imagem, 14, 0, 270, 932, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePlano));
	imagettftext($imagem, 14, 0, 365, 963, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->CODIGO_PLANO_ANS_SPCA);
	imagettftext($imagem, 14, 0, 355, 996, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($tipoAbrangencia));
	imagettftext($imagem, 14, 0, 316, 1090, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));
	imagettftext($imagem, 14, 0, 375, 1212, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_ADMISSAO));
	imagettftext($imagem, 14, 0, 275, 1243, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EXCLUSAO));
	imagettftext($imagem, 14, 0, 550, 1550, $cor,"../../Site/assets/img/arial.ttf",$dataH);
	
	
	header( 'Content-type: image/jpeg' );
	imagejpeg( $imagem, NULL, 100 );

	
}

?>