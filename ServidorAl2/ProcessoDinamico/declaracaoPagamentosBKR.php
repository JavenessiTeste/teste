<?php
require_once('../lib/base.php');

$queryEmpresa = 'SELECT * FROM CFGEMPRESA ';
$resEmpresa = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

$queryFaturas  =	" SELECT FIRST 12 * FROM PS1020 ";
$queryFaturas .=	" INNER JOIN PS1000 ON (PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)";	
$queryFaturas .=	" INNER JOIN PS1030 ON (PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO)";	
$queryFaturas .=	" WHERE PS1020.CODIGO_ASSOCIADO =  " . aspas($_SESSION['codigoIdentificacao']);	
//$queryFaturas .=	" AND DATA_PAGAMENTO IS NOT NULL ";
$queryFaturas .=	" ORDER BY PS1020.DATA_VENCIMENTO DESC ";
				
$resFaturas = jn_query($queryFaturas); 
		
$i = 0;
$imagem = imagecreatefromjpeg("../../Site/assets/img/declaracaoPagamento.jpg");	
$cor = imagecolorallocate($imagem, 0, 0, 0 );

while($rowFaturas   = jn_fetch_object($resFaturas)){

	$linha = 1133;
	$coluna01 = 180;
	$coluna02 = 280;
	$coluna03 = 445;
	$coluna04 = 630;
	$coluna05 = 780;
	$coluna06 = 920;	

	if($i == 0){

		//cabecalho
		imagettftext($imagem, 14, 0, 150, 580, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NOME_ASSOCIADO);
		imagettftext($imagem, 14, 0, 150, 610, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_CPF);
		imagettftext($imagem, 14, 0, 300, 641, $cor,"../../Site/assets/img/arial.ttf",$rowEmpresa->NOME_EMPRESA);
		imagettftext($imagem, 14, 0, 252, 671, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NOME_PLANO_FAMILIARES);
		imagettftext($imagem, 14, 0, 360, 704, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->CODIGO_PLANO_ANS_SPCA);
		imagettftext($imagem, 14, 0, 365, 734, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_ADMISSAO));
		imagettftext($imagem, 14, 0, 316, 765, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));

	
		//Linha 01
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 1){
		
		$linha += 35;
		//Linha 02
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 2){
		
		$linha += 70;
		//Linha 03
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 3){
		
		$linha += 105;
		//Linha 04
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 4){
		
		$linha += 140;
		//Linha 05
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 5){
		
		$linha += 175;
		//Linha 06
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 6){
		
		$linha += 210;
		//Linha 07
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 7){
		
		$linha += 245;
		//Linha 08
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 8){
		
		$linha += 280;
		//Linha 09
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 9){
		
		$linha += 315;
		//Linha 10
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 10){
		
		$linha += 340;
		//Linha 11
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}elseif($i == 11){
		
		$linha += 375;
		//Linha 12
		imagettftext($imagem, 14, 0, $coluna01, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->NUMERO_PARCELA);		
		imagettftext($imagem, 14, 0, $coluna02, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_VENCIMENTO));
		imagettftext($imagem, 14, 0, $coluna03, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_EMISSAO));		
		imagettftext($imagem, 14, 0, $coluna04, $linha, $cor,"../../Site/assets/img/arial.ttf",$rowFaturas->MES_ANO_REFERENCIA);	
		imagettftext($imagem, 14, 0, $coluna05, $linha, $cor,"../../Site/assets/img/arial.ttf",ToMoeda($rowFaturas->VALOR_FATURA));	
		imagettftext($imagem, 14, 0, $coluna06, $linha, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowFaturas->DATA_PAGAMENTO));	
	}
	
	
	

	$i++;
}

header( 'Content-type: image/jpeg' );
imagejpeg( $imagem, NULL, 100 );

?>