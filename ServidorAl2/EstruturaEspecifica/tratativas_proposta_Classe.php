<?php
require('../lib/base.php');

$codAssociadoTmp = trim($_GET['codAssociado']);

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, NUMERO_CPF, NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, ';
$queryAssociado .= ' 	CODIGO_ESTADO_CIVIL, VND1000_ON.ORGAO_EMISSOR_RG, NUMERO_PIS, CODIGO_TABELA_PRECO, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, PROTOCOLO_GERAL_PS6450, ';
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
}

$dtNascTit = $rowAssociado->DATA_NASCIMENTO;
$idadeTit = calcularIdade($dtNascTit);
$diaNascTit = '';
$mesNascTit = '';
$anoNascTit = '';
$alturaTit = $rowAssociado->ALTURA;
$pesoTit = $rowAssociado->PESO;

$listNasc = list($anoNascTit, $mesNascTit, $diaNascTit) = explode('-', $dtNascTit);
$diaNascTit = explode(' ', $diaNascTit);
$diaNascTit = $diaNascTit[0];

$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;	
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
$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep1 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
$queryDep1 .= ' FROM VND1000_ON ';
$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep1 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

$resDep1 = jn_query($queryDep1);
if($rowDep1 = jn_fetch_object($resDep1)){
	$dtNascDep1 = $rowDep1->DATA_NASCIMENTO;
	$idadeDep1 = calcularIdade($dtNascDep1);
	
	$diaNascDep1 = '';
	$mesNascDep1 = '';
	$anoNascDep1 = '';
	
	$listNasc = list($anoNascDep1, $mesNascDep1, $diaNascDep1) = explode('-', $dtNascDep1);
	$diaNascDep1 = explode(' ', $diaNascDep1);
	$diaNascDep1 = $diaNascDep1[0];

	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;	
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep1->CODIGO_TABELA_PRECO);
			
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep1 = $rowValores->VALOR_PLANO;
	
	$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
	$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
	$numeroRGDep1 = $rowDep1->NUMERO_RG;
	$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
	$sexoDep1 = $rowDep1->SEXO;
	$nomeMaeDep1 = $rowDep1->NOME_MAE;
	$numeroDecNasc = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
	$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
	$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
	$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
	$alturaDep1 = $rowDep1->ALTURA;
	$pesoDep1 = $rowDep1->PESO;
}

//Dependente 2
$codigoDep2 = explode('.',$codAssociadoTmp);
$codigoDep2 = $codigoDep2[0] . '.2';

$queryDep2  = ' SELECT ';
$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep2 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
$queryDep2 .= ' FROM VND1000_ON ';
$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep2 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
	
$resDep2 = jn_query($queryDep2);
if($rowDep2 = jn_fetch_object($resDep2)){
	
	$dtNascDep2 = $rowDep2->DATA_NASCIMENTO;
	$idadeDep2 = calcularIdade($dtNascDep2);
	
	$diaNascDep2 = '';
	$mesNascDep2 = '';
	$anoNascDep2 = '';
	
	$listNasc = list($anoNascDep2, $mesNascDep2, $diaNascDep2) = explode('-', $dtNascDep2);
	$diaNascDep2 = explode(' ', $diaNascDep2);
	$diaNascDep2 = $diaNascDep2[0];
	
	$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
	$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
	$numeroRGDep2 = $rowDep2->NUMERO_RG;
	$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
	$sexoDep2 = $rowDep2->SEXO;
	$nomeMaeDep2 = $rowDep2->NOME_MAE;
	$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
	$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
	$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
	$alturaDep2 = $rowDep2->ALTURA;
	$pesoDep2 = $rowDep2->PESO;
}

//Dependente 3
$codigoDep3 = explode('.',$codAssociadoTmp);
$codigoDep3 = $codigoDep3[0] . '.3';

$queryDep3  = ' SELECT ';
$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep3 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
$queryDep3 .= ' FROM VND1000_ON ';
$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep3 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
	
$resDep3 = jn_query($queryDep3);
if($rowDep3 = jn_fetch_object($resDep3)){
	
	$dtNascDep3 = $rowDep3->DATA_NASCIMENTO;
	$idadeDep3 = calcularIdade($dtNascDep3);
	$diaNascDep3 = '';
	$mesNascDep3 = '';
	$anoNascDep3 = '';
	
	$listNasc = list($anoNascDep3, $mesNascDep3, $diaNascDep3) = explode('-', $dtNascDep3);
	$diaNascDep3 = explode(' ', $diaNascDep3);
	$diaNascDep3 = $diaNascDep3[0];
	
	$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
	$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
	$numeroRGDep3 = $rowDep3->NUMERO_RG;
	$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
	$sexoDep3 = $rowDep3->SEXO;
	$nomeMaeDep3 = $rowDep3->NOME_MAE;
	$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
	$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
	$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
	$alturaDep3 = $rowDep3->ALTURA;
	$pesoDep3 = $rowDep3->PESO;
}

//Dependente 4
$codigoDep4 = explode('.',$codAssociadoTmp);
$codigoDep4 = $codigoDep4[0] . '.4';

$queryDep4  = ' SELECT ';
$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep4 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
$queryDep4 .= ' FROM VND1000_ON ';
$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep4 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
	
$resDep4 = jn_query($queryDep4);
if($rowDep4 = jn_fetch_object($resDep4)){
	
	$dtNascDep4 = $rowDep4->DATA_NASCIMENTO;
	$idadeDep4 = calcularIdade($dtNascDep4);
	
	$diaNascDep4 = '';
	$mesNascDep4 = '';
	$anoNascDep4 = '';
	
	$listNasc = list($anoNascDep4, $mesNascDep4, $diaNascDep4) = explode('-', $dtNascDep4);
	$diaNascDep4 = explode(' ', $diaNascDep4);
	$diaNascDep4 = $diaNascDep4[0];
	
	$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
	$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
	$numeroRGDep4 = $rowDep4->NUMERO_RG;
	$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
	$sexoDep4 = $rowDep4->SEXO;
	$nomeMaeDep4 = $rowDep4->NOME_MAE;
	$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
	$parentescoDep4 = $rowDep4->CODIGO_PARENTESCO;
	$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
	$alturaDep4 = $rowDep4->ALTURA;
	$pesoDep4 = $rowDep4->PESO;
}

//Dependente 5
$codigoDep5 = explode('.',$codAssociadoTmp);
$codigoDep5 = $codigoDep5[0] . '.5';

$queryDep5  = ' SELECT ';
$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep5 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
$queryDep5 .= ' FROM VND1000_ON ';
$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
$queryDep5 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
	
$resDep5 = jn_query($queryDep5);
if($rowDep5 = jn_fetch_object($resDep5)){
	
	$dtNascDep5 = $rowDep5->DATA_NASCIMENTO;
	$idadeDep5 = calcularIdade($dtNascDep5);
	
	$diaNascDep5 = '';
	$mesNascDep5 = '';
	$anoNascDep5 = '';
	
	$listNasc = list($anoNascDep5, $mesNascDep5, $diaNascDep5) = explode('-', $dtNascDep5);
	$diaNascDep5 = explode(' ', $diaNascDep5);
	$diaNascDep5 = $diaNascDep5[0];
	
	$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
	$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
	$numeroRGDep5 = $rowDep5->NUMERO_RG;
	$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
	$sexoDep5 = $rowDep5->SEXO;
	$nomeMaeDep5 = $rowDep5->NOME_MAE;
	$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
	$parentescoDep5 = $rowDep5->CODIGO_PARENTESCO;
	$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
	$alturaDep4 = $rowDep5->ALTURA;
	$pesoDep4 = $rowDep5->PESO;
}

$valorTotal = $valorTit + $valorDep1 + $valorDep2 + $valorDep3 + $valorDep4;

if($_GET['pagina'] == '1' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_Classe1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 18, 0, 100, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	//imagettftext($imagem, 18, 0, 100, 525, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 18, 0, 100, 525, $cor,"../../Site/assets/img/arial.ttf",$diaNascTit);
	imagettftext($imagem, 18, 0, 170, 525, $cor,"../../Site/assets/img/arial.ttf",$mesNascTit);
	imagettftext($imagem, 18, 0, 240, 525, $cor,"../../Site/assets/img/arial.ttf",$anoNascTit);
	imagettftext($imagem, 18, 0, 110, 600, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 18, 0, 430, 600, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 18, 0, 450, 900, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	imagettftext($imagem, 18, 0, 110, 900, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_02);
	imagettftext($imagem, 18, 0, 110, 970, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);
	imagettftext($imagem, 18, 0, 100, 675, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	imagettftext($imagem, 18, 0, 1900, 570, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->SEXO);
	imagettftext($imagem, 18, 0, 1760, 1880, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 18, 0, 110, 750, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 18, 0, 110, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 18, 0, 420, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 18, 0, 820, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	imagettftext($imagem, 18, 0, 930, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 18, 0, 2260, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
	imagettftext($imagem, 18, 0, 110, 1460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_CONTRATANTE));
	imagettftext($imagem, 18, 0, 350, 1530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_CONTRATANTE));
	imagettftext($imagem, 18, 0, 710, 1530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->RG_CONTRATANTE));
	imagettftext($imagem, 18, 0, 100, 1530, $cor,"../../Site/assets/img/arial.ttf",$diaNascTit);
	imagettftext($imagem, 18, 0, 170, 1530, $cor,"../../Site/assets/img/arial.ttf",$mesNascTit);
	imagettftext($imagem, 18, 0, 240, 1530, $cor,"../../Site/assets/img/arial.ttf",$anoNascTit);
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2' && $codModelo == 1){		
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_Classe2.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 18, 0, 100, 350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 18, 0, 100, 410, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep1);
	imagettftext($imagem, 18, 0, 170, 410, $cor,"../../Site/assets/img/arial.ttf",$mesNascDep1);
	imagettftext($imagem, 18, 0, 250, 410, $cor,"../../Site/assets/img/arial.ttf",$anoNascDep1);
	imagettftext($imagem, 18, 0, 100, 470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 18, 0, 400, 470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 18, 0, 1950, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 18, 0, 2850, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
	imagettftext($imagem, 18, 0, 2950, 780, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
	imagettftext($imagem, 18, 0, 3050, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 18, 0, 3150, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
	imagettftext($imagem, 18, 0, 100, 525, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	
	imagettftext($imagem, 18, 0, 100, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 18, 0, 100, 650, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep2);
	imagettftext($imagem, 18, 0, 170, 650, $cor,"../../Site/assets/img/arial.ttf",$mesNascDep2);
	imagettftext($imagem, 18, 0, 250, 650, $cor,"../../Site/assets/img/arial.ttf",$anoNascDep2);
	imagettftext($imagem, 18, 0, 100, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 18, 0, 400, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 18, 0, 100, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	imagettftext($imagem, 18, 0, 100, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 18, 0, 100, 895, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep3);
	imagettftext($imagem, 18, 0, 170, 895, $cor,"../../Site/assets/img/arial.ttf",$mesNascDep3);
	imagettftext($imagem, 18, 0, 250, 895, $cor,"../../Site/assets/img/arial.ttf",$anoNascDep3);
	imagettftext($imagem, 18, 0, 100, 955, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
	imagettftext($imagem, 18, 0, 400, 955, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
	imagettftext($imagem, 18, 0, 100, 1010, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	
	imagettftext($imagem, 18, 0, 100, 1085, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 18, 0, 100, 1135, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep4);
	imagettftext($imagem, 18, 0, 170, 1135, $cor,"../../Site/assets/img/arial.ttf",$mesNascDep4);
	imagettftext($imagem, 18, 0, 250, 1135, $cor,"../../Site/assets/img/arial.ttf",$anoNascDep4);
	imagettftext($imagem, 18, 0, 100, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
	imagettftext($imagem, 18, 0, 400, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
	imagettftext($imagem, 18, 0, 100, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
	
	imagettftext($imagem, 18, 0, 100, 1335, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep5));
	imagettftext($imagem, 18, 0, 100, 1390, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep5);
	imagettftext($imagem, 18, 0, 170, 1390, $cor,"../../Site/assets/img/arial.ttf",$mesNascDep5);
	imagettftext($imagem, 18, 0, 250, 1390, $cor,"../../Site/assets/img/arial.ttf",$anoNascDep5);
	imagettftext($imagem, 18, 0, 100, 1450, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep5));
	imagettftext($imagem, 18, 0, 400, 1450, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep5));
	imagettftext($imagem, 18, 0, 100, 1500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep5));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '3' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_Classe3.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 18, 0, 280, 735, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
	imagettftext($imagem, 18, 0, 400, 735, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
	
	imagettftext($imagem, 18, 0, 280, 770, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
	imagettftext($imagem, 18, 0, 400, 770, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
	
	imagettftext($imagem, 18, 0, 280, 805, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
	imagettftext($imagem, 18, 0, 400, 805, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
	
	imagettftext($imagem, 18, 0, 280, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
	imagettftext($imagem, 18, 0, 400, 840, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));
	
	imagettftext($imagem, 18, 0, 280, 875, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep4));
	imagettftext($imagem, 18, 0, 400, 875, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
	
	imagettftext($imagem, 18, 0, 280, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep5));
	imagettftext($imagem, 18, 0, 400, 910, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep5));
	
	imagettftext($imagem, 18, 0, 900, 910, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
	
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '4' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_Classe4.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '5' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_Classe5.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '6' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_Classe6.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '7' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_Classe7.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '8' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_Classe8.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '9' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_Classe9.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}


if($_GET['pagina'] == '1' && $codModelo == 2){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Classe1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$data = '';
	$dia = date('d');
	
	if($dia <= 10){
		$data = somar_datas( 1, 'm');
	}else{
		$data = somar_datas( 2, 'm');
	}
	$dataVigencia = date('01/m/Y', strtotime($data));
	
	$nomeEntidade = '';
	$profissao = '';
	if($rowAssociado->CODIGO_PLANO == '42' || $rowAssociado->CODIGO_PLANO == '43'){
		$nomeEntidade = 'Asprenne';
		$profissao = 'Servidor Publico';
	}else{
		$nomeEntidade = 'AEC';
		$profissao = 'Empregado no Comercio';
	}
	
	$queryEstadoCivil = 'SELECT NOME_ESTADO_CIVIL FROM PS1044 WHERE CODIGO_ESTADO_CIVIL = ' . aspas($rowAssociado->CODIGO_ESTADO_CIVIL);
	$resEstadoCivil = jn_query($queryEstadoCivil);
	$rowEstadoCivil = jn_fetch_object($resEstadoCivil);
	
	imagettftext($imagem, 18, 0, 670, 210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->PROTOCOLO_GERAL_PS6450));
	imagettftext($imagem, 18, 0, 100, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataVigencia));
	imagettftext($imagem, 18, 0, 470, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('10'));
	imagettftext($imagem, 18, 0, 700, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeEntidade));
	imagettftext($imagem, 18, 0, 100, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 18, 0, 90, 780, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 18, 0, 110, 890, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 18, 0, 380, 890, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 18, 0, 660, 890, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ORGAO_EMISSOR_RG);
	imagettftext($imagem, 18, 0, 940, 890, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_PIS);
	imagettftext($imagem, 18, 0, 110, 1380, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	imagettftext($imagem, 18, 0, 470, 1380, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_02);
	imagettftext($imagem, 18, 0, 100, 1000, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	imagettftext($imagem, 18, 0, 280, 745, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->SEXO);
	imagettftext($imagem, 18, 0, 470, 745, $cor,"../../Site/assets/img/arial.ttf",$rowEstadoCivil->NOME_ESTADO_CIVIL);
	imagettftext($imagem, 18, 0, 110, 1490, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);
	imagettftext($imagem, 18, 0, 110, 1120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 18, 0, 110, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 18, 0, 470, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 18, 0, 810, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	imagettftext($imagem, 18, 0, 935, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 18, 0, 100, 1610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($profissao));
	imagettftext($imagem, 18, 0, 700, 1610, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	
		
	
	$image_p = imagecreatetruecolor(1240, 1754);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2' && $codModelo == 2){		
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Classe2.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 18, 0, 700, 160, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
	imagettftext($imagem, 18, 0, 100, 360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 18, 0, 100, 475, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
	imagettftext($imagem, 18, 0, 380, 475, $cor,"../../Site/assets/img/arial.ttf",$sexoDep1);
	imagettftext($imagem, 18, 0, 700, 475, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
	imagettftext($imagem, 18, 0, 950, 475, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
	imagettftext($imagem, 18, 0, 100, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 18, 0, 470, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 18, 0, 850, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNasc));
	imagettftext($imagem, 18, 0, 3050, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 18, 0, 100, 700, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	imagettftext($imagem, 18, 0, 330, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
	imagettftext($imagem, 18, 0, 450, 1210, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
	imagettftext($imagem, 18, 0, 330, 1265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
	imagettftext($imagem, 18, 0, 450, 1265, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
	imagettftext($imagem, 18, 0, 850, 1345, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '3' && $codModelo == 2){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta2_Classe3.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '4' && $codModelo == 2){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta2_Classe4.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '5' && $codModelo == 2){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Classe5.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 14, 0, 170, 1575, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));	
	imagettftext($imagem, 14, 0, 420, 1575, $cor,"../../Site/assets/img/arial.ttf",date('d'));	
	imagettftext($imagem, 14, 0, 470, 1575, $cor,"../../Site/assets/img/arial.ttf",date('m'));	
	imagettftext($imagem, 14, 0, 510, 1575, $cor,"../../Site/assets/img/arial.ttf",date('Y'));	
	imagettftext($imagem, 14, 0, 750, 1575, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));	
	imagettftext($imagem, 14, 0, 1000, 1575, $cor,"../../Site/assets/img/arial.ttf",date('d'));	
	imagettftext($imagem, 14, 0, 1045, 1575, $cor,"../../Site/assets/img/arial.ttf",date('m'));	
	imagettftext($imagem, 14, 0, 1090, 1575, $cor,"../../Site/assets/img/arial.ttf",date('Y'));	
	imagettftext($imagem, 14, 0, 170, 1635, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));	
	imagettftext($imagem, 14, 0, 170, 1665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));	
	imagettftext($imagem, 14, 0, 210, 1695, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));	
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '6' && $codModelo == 2){
	global $codAssociadoTmp;
	global $codigoDep1;
	global $codigoDep2;
	global $codigoDep3;
	global $codigoDep4;
	$peso = '';
	$altura = '';
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Classe6.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$queryDecTit  = ' SELECT ';
	$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.PESO, VND1000_ON.ALTURA, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
	$queryDecTit .= ' FROM VND1000_ON ';
	$queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
	$queryDecTit .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
	$queryDecTit .= ' WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
	$queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
	$resDecTit = jn_query($queryDecTit); 
		
	while($rowDecTit = jn_fetch_object($resDecTit)){	
		$coluna = '';
		$codigoTit = $codAssociadoTmp;
		
		if($rowDecTit->TIPO_ASSOCIADO == 'T'){
			$coluna = 740;
			$peso = $rowDecTit->PESO;
			$altura = $rowDecTit->ALTURA;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
			$coluna = 830;
			$peso = $pesoDep1;
			$altura = $alturaDep1;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
			$coluna = 930;
			$peso = $pesoDep2;
			$altura = $alturaDep2;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
			$coluna = 1030;
			$peso = $pesoDep3;
			$altura = $alturaDep3;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
			$coluna = 1130;
			$peso = $pesoDep4;
			$altura = $alturaDep4;
		}
		
		if($rowDecTit->NUMERO_PERGUNTA == '1'){
			imagettftext($imagem, 14, 0, $coluna, 895, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
			imagettftext($imagem, 14, 0, $coluna, 935, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
			imagettftext($imagem, 14, 0, $coluna, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
			imagettftext($imagem, 14, 0, $coluna, 1005, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '5'){
			imagettftext($imagem, 14, 0, $coluna, 1045, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
			imagettftext($imagem, 14, 0, $coluna, 1080, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
			imagettftext($imagem, 14, 0, $coluna, 1115, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
			imagettftext($imagem, 14, 0, $coluna, 1150, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
			imagettftext($imagem, 14, 0, $coluna, 1190, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
			imagettftext($imagem, 14, 0, $coluna, 1225, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '11'){
			imagettftext($imagem, 14, 0, $coluna, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
			imagettftext($imagem, 14, 0, $coluna, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
			imagettftext($imagem, 14, 0, $coluna, 1335, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
			imagettftext($imagem, 14, 0, $coluna, 1370, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '15'){
			imagettftext($imagem, 14, 0, $coluna, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '16'){
			imagettftext($imagem, 14, 0, $coluna, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '17'){
			imagettftext($imagem, 14, 0, $coluna, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '18'){
			imagettftext($imagem, 14, 0, $coluna, 1510, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '19'){
			imagettftext($imagem, 14, 0, $coluna, 1550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '20'){
			imagettftext($imagem, 14, 0, $coluna, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '21'){
			imagettftext($imagem, 14, 0, $coluna, 1625, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	

			if($peso){
				imagettftext($imagem, 14, 0, $coluna, 1660, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($peso));	
			}
				
			if($altura){
				imagettftext($imagem, 14, 0, $coluna, 1695, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($altura));	
			}		
		}
			
		
	}
	
	imagettftext($imagem, 16, 0, 900, 85, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->PROTOCOLO_GERAL_PS6450));	
	imagettftext($imagem, 16, 0, 900, 190, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '7' && $codModelo == 2){
	$queryObservacoes  = ' 	SELECT  ';	
	$queryObservacoes .= ' 		NUMERO_PERGUNTA, CODIGO_ASSOCIADO, VND1005_ON.DESCRICAO_OBSERVACAO, VND1000_ON.NOME_ASSOCIADO, ';
	$queryObservacoes .= ' 		CASE ';
	$queryObservacoes .= ' 			WHEN VND1005_ON.CODIGO_ASSOCIADO = ' . aspas($_GET['codAssociado']);
	$queryObservacoes .= ' 				THEN "TIT."  ';
	$queryObservacoes .= ' 			ELSE "DEP."  ';
	$queryObservacoes .= ' 		END  AS TIPO_ASSOCIADO';
	$queryObservacoes .= ' 	FROM VND1005_ON  ';
	$queryObservacoes .= ' 	INNER JOIN VND1000_ON ON (VND1005_ON.CODIGO_ASSOCIADO = VND1000_ON.CODIGO_ASSOCIADO) ';
	$queryObservacoes .= ' 	WHERE CODIGO_ASSOCIADO IN ( ';
	$queryObservacoes .= ' 		SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . $_GET['codAssociado'];	
	$queryObservacoes .= ' 	) ';	
	
	$resObservacoes = jn_query($queryObservacoes);
	
	$GridObservacoes = Array();
	$i = 0;
	
	while($rowObservacoes = jn_fetch_object($resObservacoes)){		
		$GridObservacoes[$i]['NUMERO_PERGUNTA'] = $rowObservacoes->NUMERO_PERGUNTA;
		$GridObservacoes[$i]['CODIGO_ASSOCIADO'] = $rowObservacoes->CODIGO_ASSOCIADO;
		$GridObservacoes[$i]['NOME_ASSOCIADO'] = $rowObservacoes->NOME_ASSOCIADO;
		$GridObservacoes[$i]['DESCRICAO_OBSERVACAO'] = jn_utf8_encode($rowObservacoes->DESCRICAO_OBSERVACAO);
		$GridObservacoes[$i]['TIPO_ASSOCIADO'] = jn_utf8_encode($rowObservacoes->TIPO_ASSOCIADO);
		$i++;
	}
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Classe7.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 14, 0, 75, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['NUMERO_PERGUNTA']);
	imagettftext($imagem, 14, 0, 200, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['TIPO_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 300, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['NOME_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 705, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['DESCRICAO_OBSERVACAO']);
	imagettftext($imagem, 14, 0, 75, 475,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['NUMERO_PERGUNTA']);
	imagettftext($imagem, 14, 0, 200, 475, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['TIPO_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 300, 475, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['NOME_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 705, 475, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['DESCRICAO_OBSERVACAO']);
	imagettftext($imagem, 14, 0, 75, 505,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['NUMERO_PERGUNTA']);
	imagettftext($imagem, 14, 0, 200, 505, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['TIPO_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 300, 505, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['NOME_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 705, 505, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['DESCRICAO_OBSERVACAO']);
	imagettftext($imagem, 14, 0, 75, 540,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['NUMERO_PERGUNTA']);
	imagettftext($imagem, 14, 0, 200, 540, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['TIPO_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 300, 540, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['NOME_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 705, 540, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['DESCRICAO_OBSERVACAO']);
	imagettftext($imagem, 14, 0, 75, 580,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['NUMERO_PERGUNTA']);
	imagettftext($imagem, 14, 0, 200, 580, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['TIPO_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 300, 580, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['NOME_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 705, 580, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['DESCRICAO_OBSERVACAO']);
	imagettftext($imagem, 14, 0, 75, 620,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['NUMERO_PERGUNTA']);
	imagettftext($imagem, 14, 0, 200, 620, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['TIPO_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 300, 620, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['NOME_ASSOCIADO']);
	imagettftext($imagem, 14, 0, 705, 620, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['DESCRICAO_OBSERVACAO']);
	imagettftext($imagem, 14, 0, 170, 1595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));	
	imagettftext($imagem, 14, 0, 420, 1595, $cor,"../../Site/assets/img/arial.ttf",date('d'));	
	imagettftext($imagem, 14, 0, 475, 1595, $cor,"../../Site/assets/img/arial.ttf",date('m'));	
	imagettftext($imagem, 14, 0, 520, 1595, $cor,"../../Site/assets/img/arial.ttf",date('Y'));	
	imagettftext($imagem, 14, 0, 850, 1595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 14, 0, 900, 190, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));	
	imagettftext($imagem, 16, 0, 900, 85, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->PROTOCOLO_GERAL_PS6450));	
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

function calcularIdade($date){	

    // separando yyyy, mm, ddd
    list($ano, $mes, $dia) = explode('-', $date);

    // data atual
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

    // cálculo
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
}

function somar_datas( $numero, $tipo ){
  switch ($tipo) {
    case 'd':
    	$tipo = ' day';
    	break;
    case 'm':
    	$tipo = ' month';
    	break;
    case 'y':
    	$tipo = ' year';
    	break;
    }	
    return "+".$numero.$tipo;
}
?>