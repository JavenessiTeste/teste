<?php
require('../lib/base.php');

$codAssociadoTmp = trim($_GET['codAssociado']);

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, NUMERO_CPF, NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_TABELA_PRECO, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NOME_CONTRATANTE, VND1000_ON.NOME_ASSOCIADO) AS NOME_CONTRATANTE, COALESCE(VND1001_ON.NUMERO_CPF_CONTRATANTE, VND1000_ON.NUMERO_CPF) AS CPF_CONTRATANTE, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NUMERO_RG_CONTRATANTE, VND1000_ON.NUMERO_RG) AS RG_CONTRATANTE ';
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

$date = new DateTime($rowAssociado->DATA_NASCIMENTO);
$interval = $date->diff( new DateTime( date('Y-m-d') ) );
$idade = $interval->format('%Y');

$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;	
$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowAssociado->CODIGO_TABELA_PRECO);
		
$resValores = jn_query($queryValores);
$rowValores = jn_fetch_object($resValores);
$valorTit = $rowValores->VALOR_PLANO;
			
$queryModelo = 'SELECT * FROM VND1030CONFIG_ON WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
$resModelo = jn_query($queryModelo);
$rowModelo = jn_fetch_object($resModelo);
$codModelo = $rowModelo->CODIGOS_MODELO_CONTRATO;

//Tratativas para dependentes

//Dependente 1
$codigoDep1 = explode('.',$codAssociadoTmp);
$codigoDep1 = $codigoDep1[0] . '.1';

$queryDep1  = ' SELECT ';
$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO ';
$queryDep1 .= ' FROM VND1000_ON ';
$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep1 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

$resDep1 = jn_query($queryDep1);
if($rowDep1 = jn_fetch_object($resDep1)){
	$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
	$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
	$numeroRGDep1 = $rowDep1->NUMERO_RG;
	$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
	$sexoDep1 = $rowDep1->SEXO;
	$nomeMaeDep1 = $rowDep1->NOME_MAE;
	$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
	$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
	$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
	
	$date = new DateTime($rowDep1->DATA_NASCIMENTO);
	$interval = $date->diff( new DateTime( date('Y-m-d') ) );
	$idade = $interval->format('%Y');

	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep1->CODIGO_TABELA_PRECO);
	
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep1 = $rowValores->VALOR_PLANO;
}

//Dependente 2
$codigoDep2 = explode('.',$codAssociadoTmp);
$codigoDep2 = $codigoDep2[0] . '.2';

$queryDep2  = ' SELECT ';
$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO ';
$queryDep2 .= ' FROM VND1000_ON ';
$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep2 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
	
$resDep2 = jn_query($queryDep2);
if($rowDep2 = jn_fetch_object($resDep2)){
	$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
	$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
	$numeroRGDep2 = $rowDep2->NUMERO_RG;
	$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
	$sexoDep2 = $rowDep2->SEXO;
	$nomeMaeDep2 = $rowDep2->NOME_MAE;
	$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
	$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
	$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
	
	$date = new DateTime($rowDep2->DATA_NASCIMENTO);
	$interval = $date->diff( new DateTime( date('Y-m-d') ) );
	$idade = $interval->format('%Y');

	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep2->CODIGO_TABELA_PRECO);
	
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep2 = $rowValores->VALOR_PLANO;
}

//Dependente 3
$codigoDep3 = explode('.',$codAssociadoTmp);
$codigoDep3 = $codigoDep3[0] . '.3';

$queryDep3  = ' SELECT ';
$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO ';
$queryDep3 .= ' FROM VND1000_ON ';
$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep3 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
	
$resDep3 = jn_query($queryDep3);
if($rowDep3 = jn_fetch_object($resDep3)){
	$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
	$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
	$numeroRGDep3 = $rowDep3->NUMERO_RG;
	$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
	$sexoDep3 = $rowDep3->SEXO;
	$nomeMaeDep3 = $rowDep3->NOME_MAE;
	$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
	$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
	$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
	
	$date = new DateTime($rowDep3->DATA_NASCIMENTO);
	$interval = $date->diff( new DateTime( date('Y-m-d') ) );
	$idade = $interval->format('%Y');

	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep3->CODIGO_TABELA_PRECO);
	
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep3 = $rowValores->VALOR_PLANO;
}

//Dependente 4
$codigoDep4 = explode('.',$codAssociadoTmp);
$codigoDep4 = $codigoDep4[0] . '.4';

$queryDep4  = ' SELECT ';
$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO ';
$queryDep4 .= ' FROM VND1000_ON ';
$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep4 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
	
$resDep4 = jn_query($queryDep4);
if($rowDep4 = jn_fetch_object($resDep4)){
	$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
	$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
	$numeroRGDep4 = $rowDep4->NUMERO_RG;
	$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
	$sexoDep4 = $rowDep4->SEXO;
	$nomeMaeDep4 = $rowDep4->NOME_MAE;
	$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
	$parentescoDep4 = $rowDep4->CODIGO_PARENTESCO;
	$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
	
	$date = new DateTime($rowDep4->DATA_NASCIMENTO);
	$interval = $date->diff( new DateTime( date('Y-m-d') ) );
	$idade = $interval->format('%Y');

	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep4->CODIGO_TABELA_PRECO);
	
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep4 = $rowValores->VALOR_PLANO;
}

$valorTotal = $valorTit + $valorDep1 + $valorDep2 + $valorDep3 + $valorDep4;

jn_query('UPDATE VND1001_ON SET VALOR_ADESAO = ' . aspas($valorTotal) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));


$cidadeOperadora = 'NATAL';


if($_GET['pagina'] == '1' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_odontoMais1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 25, 0, 150, 485, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 25, 0, 1400, 485, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 25, 0, 1750, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 25, 0, 2300, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 25, 0, 2900, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	imagettftext($imagem, 25, 0, 150, 570, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	imagettftext($imagem, 25, 0, 1400, 570, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 25, 0, 1900, 570, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->SEXO);
	imagettftext($imagem, 25, 0, 620, 1520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 25, 0, 2500, 1520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 25, 0, 300, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 25, 0, 1550, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	imagettftext($imagem, 25, 0, 1760, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 25, 0, 2260, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
	imagettftext($imagem, 25, 0, 2460, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
	imagettftext($imagem, 25, 0, 250, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_CONTRATANTE));
	imagettftext($imagem, 25, 0, 1860, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_CONTRATANTE));
	imagettftext($imagem, 25, 0, 2400, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->RG_CONTRATANTE));
	imagettftext($imagem, 25, 0, 2900, 565, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
	imagettftext($imagem, 25, 0, 2900, 1440, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
	imagettftext($imagem, 25, 0, 900, 1950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($cidadeOperadora));
	imagettftext($imagem, 25, 0, 1140, 1950, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 25, 0, 1230, 1950, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 25, 0, 1330, 1950, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	
	
	imagettftext($imagem, 25, 0, 240, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 25, 0, 1950, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 25, 0, 1950, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 25, 0, 2550, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 25, 0, 2550, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
	imagettftext($imagem, 25, 0, 2850, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
	imagettftext($imagem, 25, 0, 2950, 780, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
	imagettftext($imagem, 25, 0, 3050, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 25, 0, 3150, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
	imagettftext($imagem, 25, 0, 240, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	
	imagettftext($imagem, 25, 0, 240, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 25, 0, 1950, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
	imagettftext($imagem, 25, 0, 1950, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
	imagettftext($imagem, 25, 0, 2550, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
	imagettftext($imagem, 25, 0, 2550, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
	imagettftext($imagem, 25, 0, 2850, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
	imagettftext($imagem, 25, 0, 3050, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
	imagettftext($imagem, 25, 0, 3150, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));
	imagettftext($imagem, 25, 0, 240, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
	imagettftext($imagem, 25, 0, 2950, 950, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
	
	imagettftext($imagem, 25, 0, 240, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 25, 0, 1950, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
	imagettftext($imagem, 25, 0, 1950, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
	imagettftext($imagem, 25, 0, 2550, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
	imagettftext($imagem, 25, 0, 2550, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
	imagettftext($imagem, 25, 0, 2850, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
	imagettftext($imagem, 25, 0, 3050, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
	imagettftext($imagem, 25, 0, 3150, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
	imagettftext($imagem, 25, 0, 240, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	imagettftext($imagem, 25, 0, 2950, 1135, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));
	
	
	imagettftext($imagem, 25, 0, 240, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 25, 0, 1950, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
	imagettftext($imagem, 25, 0, 1950, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
	imagettftext($imagem, 25, 0, 2550, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
	imagettftext($imagem, 25, 0, 2550, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep4));
	imagettftext($imagem, 25, 0, 2850, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep4));
	imagettftext($imagem, 25, 0, 3050, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
	imagettftext($imagem, 25, 0, 3150, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep4));
	imagettftext($imagem, 25, 0, 240, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
	imagettftext($imagem, 25, 0, 2950, 1305, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
	
	$image_p = imagecreatetruecolor(3300, 2550);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 3300, 2550, 3300, 2550);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, '../../ServidorCliente/EstruturaContratos/ImagensCriadas/' . $codAssociadoTmp . '_1.jpg', 80 );
}


if($_GET['pagina'] == '2' && $codModelo == 1){
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');

	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_odontoMais2.jpg");	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 25, 0, 2010, 2165, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($cidadeOperadora));
	imagettftext($imagem, 25, 0, 2310, 2165, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 25, 0, 2490, 2165, $cor,"../../Site/assets/img/arial.ttf",strtoUpper(strftime('%B', strtotime('today'))));	
	imagettftext($imagem, 25, 0, 2885, 2165, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	$image_p = imagecreatetruecolor(1400, 1000);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 2325, 1050, 5430, 2650);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, '../../ServidorCliente/EstruturaContratos/ImagensCriadas/' . $codAssociadoTmp . '_2.jpg', 80 );
}

if($_GET['pagina'] == '1' && $codModelo == 2){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_odontoMais1.jpg");	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 25, 0, 150, 485, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 25, 0, 1400, 485, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 25, 0, 1750, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 25, 0, 2300, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 25, 0, 2900, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	imagettftext($imagem, 25, 0, 150, 570, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	imagettftext($imagem, 25, 0, 1400, 570, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 25, 0, 1900, 570, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->SEXO);
	imagettftext($imagem, 25, 0, 620, 1520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 25, 0, 2500, 1520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 25, 0, 300, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 25, 0, 1550, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	imagettftext($imagem, 25, 0, 1760, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 25, 0, 2260, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
	imagettftext($imagem, 25, 0, 2460, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
	imagettftext($imagem, 25, 0, 250, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_CONTRATANTE));
	imagettftext($imagem, 25, 0, 1860, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_CONTRATANTE));
	imagettftext($imagem, 25, 0, 2400, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->RG_CONTRATANTE));
	imagettftext($imagem, 25, 0, 2900, 565, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
	imagettftext($imagem, 25, 0, 2900, 1440, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
	imagettftext($imagem, 25, 0, 900, 1950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($cidadeOperadora));
	imagettftext($imagem, 25, 0, 1140, 1950, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 25, 0, 1230, 1950, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 25, 0, 1330, 1950, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	
	
	imagettftext($imagem, 25, 0, 240, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 25, 0, 1950, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 25, 0, 1950, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 25, 0, 2550, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 25, 0, 2550, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
	imagettftext($imagem, 25, 0, 2850, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
	imagettftext($imagem, 25, 0, 2950, 780, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
	imagettftext($imagem, 25, 0, 3050, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 25, 0, 3150, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
	imagettftext($imagem, 25, 0, 240, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	
	imagettftext($imagem, 25, 0, 240, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 25, 0, 1950, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
	imagettftext($imagem, 25, 0, 1950, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
	imagettftext($imagem, 25, 0, 2550, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
	imagettftext($imagem, 25, 0, 2550, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
	imagettftext($imagem, 25, 0, 2850, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
	imagettftext($imagem, 25, 0, 3050, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
	imagettftext($imagem, 25, 0, 3150, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));
	imagettftext($imagem, 25, 0, 240, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
	imagettftext($imagem, 25, 0, 2950, 950, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
	
	imagettftext($imagem, 25, 0, 240, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 25, 0, 1950, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
	imagettftext($imagem, 25, 0, 1950, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
	imagettftext($imagem, 25, 0, 2550, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
	imagettftext($imagem, 25, 0, 2550, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
	imagettftext($imagem, 25, 0, 2850, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
	imagettftext($imagem, 25, 0, 3050, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
	imagettftext($imagem, 25, 0, 3150, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
	imagettftext($imagem, 25, 0, 240, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	imagettftext($imagem, 25, 0, 2950, 1135, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));
	
	
	imagettftext($imagem, 25, 0, 240, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 25, 0, 1950, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
	imagettftext($imagem, 25, 0, 1950, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
	imagettftext($imagem, 25, 0, 2550, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
	imagettftext($imagem, 25, 0, 2550, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep4));
	imagettftext($imagem, 25, 0, 2850, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep4));
	imagettftext($imagem, 25, 0, 3050, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
	imagettftext($imagem, 25, 0, 3150, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep4));
	imagettftext($imagem, 25, 0, 240, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
	imagettftext($imagem, 25, 0, 2950, 1305, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
	
	
	$image_p = imagecreatetruecolor(3300, 2550);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 3300, 2550, 3300, 2550);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2' && $codModelo == 2){
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_odontoMais2.jpg");	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 25, 0, 2010, 2165, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($cidadeOperadora));
	imagettftext($imagem, 25, 0, 2310, 2165, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 25, 0, 2490, 2165, $cor,"../../Site/assets/img/arial.ttf",strtoUpper(strftime('%B', strtotime('today'))));	
	imagettftext($imagem, 25, 0, 2885, 2165, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	$image_p = imagecreatetruecolor(1400, 1000);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 2325, 1050, 5430, 2650);	
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

?>