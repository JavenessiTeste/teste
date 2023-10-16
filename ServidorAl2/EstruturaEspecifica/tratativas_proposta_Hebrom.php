<?php
require('../lib/base.php');

$codAssociadoTmp = trim($_GET['codAssociado']);

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, NUMERO_CPF, NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, DATA_ADMISSAO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, ';
$queryAssociado .= ' 	CODIGO_ESTADO_CIVIL, VND1000_ON.ORGAO_EMISSOR_RG, NUMERO_PIS, CODIGO_TABELA_PRECO, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, PROTOCOLO_GERAL_PS6450, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR,  PS1102.NUMERO_CNPJ AS CNPJ_CORRETORA, PS1100.TIPO_CADASTRO, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NOME_CONTRATANTE, VND1000_ON.NOME_ASSOCIADO) AS NOME_CONTRATANTE, PS1030.CODIGO_CADASTRO_ANS, COALESCE(VND1001_ON.NUMERO_CPF_CONTRATANTE, VND1000_ON.NUMERO_CPF) AS CPF_CONTRATANTE, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NUMERO_RG_CONTRATANTE, VND1000_ON.NUMERO_RG) AS RG_CONTRATANTE ';
$queryAssociado .= ' FROM VND1000_ON ';
$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
$resAssociado = jn_query($queryAssociado);

if(!$rowAssociado = jn_fetch_object($resAssociado)){
	echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
	exit;
}

$codAns    = str_replace('-','',$rowAssociado->CODIGO_CADASTRO_ANS);
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
$queryDep1 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, NUMERO_PIS, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
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
	$nascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
	$numeroPisDep1 = $rowDep1->NUMERO_PIS;
}

//Dependente 2
$codigoDep2 = explode('.',$codAssociadoTmp);
$codigoDep2 = $codigoDep2[0] . '.2';

$queryDep2  = ' SELECT ';
$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep2 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, NUMERO_PIS, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
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
	$nascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
	$numeroPisDep2 = $rowDep2->NUMERO_PIS;
}

//Dependente 3
$codigoDep3 = explode('.',$codAssociadoTmp);
$codigoDep3 = $codigoDep3[0] . '.3';

$queryDep3  = ' SELECT ';
$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep3 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, NUMERO_PIS, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
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
	$nascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
	$numeroPisDep3 = $rowDep3->NUMERO_PIS;
}

//Dependente 4
$codigoDep4 = explode('.',$codAssociadoTmp);
$codigoDep4 = $codigoDep4[0] . '.4';

$queryDep4  = ' SELECT ';
$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep4 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, NUMERO_PIS, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
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
	$nascVivoDep4 = $rowDep4->NUMERO_DECLARACAO_NASC_VIVO;
	$numeroPisDep4 = $rowDep4->NUMERO_PIS;
}

//Dependente 5
$codigoDep5 = explode('.',$codAssociadoTmp);
$codigoDep5 = $codigoDep5[0] . '.5';

$queryDep5  = ' SELECT ';
$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO,  ';
$queryDep5 .= ' 	NUMERO_DECLARACAO_NASC_VIVO, NUMERO_PIS, CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_TABELA_PRECO, PESO, ALTURA ';
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
	$nascVivoDep4 = $rowDep4->NUMERO_DECLARACAO_NASC_VIVO;
	$numeroPisDep4 = $rowDep4->NUMERO_PIS;
}

$valorTotal = $valorTit + $valorDep1 + $valorDep2 + $valorDep3 + $valorDep4;

if($_GET['pagina'] == '1' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta1_Hebrom1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 15, 0, 200, 210, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 15, 0, 245, 210, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 15, 0, 305, 210, $cor,"../../Site/assets/img/arial.ttf",date('y'));

	if($rowAssociado->TIPO_CADASTRO == 'Cadastro_Vendedores'){
		imagettftext($imagem, 15, 0, 70, 510, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 15, 0, 980, 510, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
	}else{
		imagettftext($imagem, 15, 0, 70, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 15, 0, 980, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CNPJ_CORRETORA));
	}

	imagettftext($imagem, 15, 0, 70, 680, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 15, 0, 70, 725, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 15, 0, 370, 725, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 15, 0, 600, 725, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ORGAO_EMISSOR_RG);
	imagettftext($imagem, 15, 0, 760, 725, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 15, 0, 70, 775, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	
	if($rowAssociado->SEXO == 'M'){
		imagettftext($imagem, 15, 0, 269, 772, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	}else{
		imagettftext($imagem, 15, 0, 325, 773, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	}
	
	imagettftext($imagem, 15, 0, 70, 825, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_DECLARACAO_NASC_VIVO);
	imagettftext($imagem, 15, 0, 650, 825, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_PIS);
	imagettftext($imagem, 15, 0, 70, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	
	imagettftext($imagem, 15, 0, 70, 920, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 15, 0, 260, 920, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 15, 0, 350, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 15, 0, 740, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 15, 0, 1150, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	
	imagettftext($imagem, 15, 0, 70, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 15, 0, 260, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 15, 0, 350, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 15, 0, 740, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 15, 0, 1150, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	
	imagettftext($imagem, 15, 0, 70, 1105, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	imagettftext($imagem, 15, 0, 450, 1105, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_02);
	imagettftext($imagem, 15, 0, 70, 1155, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);

	//DEPENDENTRE 1

	if ($nomeDep1 != ''){

		imagettftext($imagem, 15, 0, 100, 1244, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
		imagettftext($imagem, 15, 0, 100, 1289, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
		imagettftext($imagem, 15, 0, 400, 1289, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
		imagettftext($imagem, 15, 0, 810, 1289, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
		imagettftext($imagem, 15, 0, 100, 1335, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);

		if($rowDep1->SEXO == 'M'){
			imagettftext($imagem, 15, 0, 317, 1332, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}else{
			imagettftext($imagem, 15, 0, 375, 1335, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}
		imagettftext($imagem, 15, 0, 100, 1386, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nascVivoDep1));
		imagettftext($imagem, 15, 0, 680, 1386, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroPisDep1));
		imagettftext($imagem, 15, 0, 100, 1429, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	}

	//DEPENDENTE 2

	if ($nomeDep2 != ''){

		imagettftext($imagem, 15, 0, 100, 1489, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
		imagettftext($imagem, 15, 0, 100, 1539, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
		imagettftext($imagem, 15, 0, 400, 1539, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
		imagettftext($imagem, 15, 0, 810, 1539, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
		imagettftext($imagem, 15, 0, 100, 1585, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
		
		if($rowDep2->SEXO == 'M'){
			imagettftext($imagem, 15, 0, 317, 1582, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}else{
			imagettftext($imagem, 15, 0, 375, 1582, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}

		imagettftext($imagem, 15, 0, 100, 1640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nascVivoDep2));
		imagettftext($imagem, 15, 0, 680, 1640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroPisDep2));
		imagettftext($imagem, 15, 0, 100, 1679, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2' && $codModelo == 1){		
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta1_Hebrom2.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	// DEPENDENTE 3

	if ($nomeDep3 != ''){
	
		imagettftext($imagem, 15, 0, 100, 250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
		imagettftext($imagem, 15, 0, 100, 300, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
		imagettftext($imagem, 15, 0, 400, 300, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
		imagettftext($imagem, 15, 0, 810, 300, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
		imagettftext($imagem, 15, 0, 100, 350, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);

		if($rowDep3->SEXO == 'M'){
			imagettftext($imagem, 15, 0, 316, 342, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}else{
			imagettftext($imagem, 15, 0, 374, 342, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}

		imagettftext($imagem, 15, 0, 100, 398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nascVivoDep3));
		imagettftext($imagem, 15, 0, 680, 398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroPisDep3));
		imagettftext($imagem, 15, 0, 100, 440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	}
		
	// DEPENDENTE 4

	if ($nomeDep4 != ''){

		imagettftext($imagem, 15, 0, 100, 499, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
		imagettftext($imagem, 15, 0, 100, 549, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
		imagettftext($imagem, 15, 0, 400, 549, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
		imagettftext($imagem, 15, 0, 810, 549, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
		imagettftext($imagem, 15, 0, 100, 595, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);

		if($rowDep4->SEXO == 'M'){
			imagettftext($imagem, 15, 0, 316, 590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}else{
			imagettftext($imagem, 15, 0, 374, 590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}

		imagettftext($imagem, 15, 0, 100, 646, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nascVivoDep4));
		imagettftext($imagem, 15, 0, 680, 646, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroPisDep4));
		imagettftext($imagem, 15, 0, 100, 689, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));

	}
	
	//DEPENDENTE 5

	if ($nomeDep5 != ''){

		imagettftext($imagem, 15, 0, 100, 750, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep5));
		imagettftext($imagem, 15, 0, 100, 800, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep5));
		imagettftext($imagem, 15, 0, 400, 800, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep5));
		imagettftext($imagem, 15, 0, 810, 800, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep5));
		imagettftext($imagem, 15, 0, 100, 850, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep5);

		
		if($rowDep5->SEXO == 'M'){
			imagettftext($imagem, 15, 0, 316, 843, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}else{
			imagettftext($imagem, 15, 0, 374, 843, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}

		imagettftext($imagem, 15, 0, 100, 900, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nascVivoDep4));
		imagettftext($imagem, 15, 0, 680, 900, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroPisDep4));
		imagettftext($imagem, 15, 0, 100, 940, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep5));
	}

	imagettftext($imagem, 15, 0, 70, 1030, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_CONTRATANTE));
	imagettftext($imagem, 15, 0, 70, 1075, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_CONTRATANTE));
	imagettftext($imagem, 15, 0, 350, 1075, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->RG_CONTRATANTE));

	if ($codAns  == '490570219'){
		imagettftext($imagem, 15, 0, 93, 1188, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	}else if ($codAns  == '490568217'){
		imagettftext($imagem, 15, 0, 93, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	}else if($codAns  == '490571217'){
		imagettftext($imagem, 15, 0, 93, 1272, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	}else if ($codAns  == '490566211'){
		imagettftext($imagem, 15, 0, 93, 1314, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	}else if ($codAns  == '477891170'){
		imagettftext($imagem, 15, 0, 100, 1460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	}


	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '3' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta1_Hebrom3.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 15, 0, 230, 280, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
	imagettftext($imagem, 15, 0, 320, 280, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
	imagettftext($imagem, 15, 0, 445, 280, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
	
	imagettftext($imagem, 15, 0, 230, 315, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
	imagettftext($imagem, 15, 0, 320, 315, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
	imagettftext($imagem, 15, 0, 445, 315, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
	
	imagettftext($imagem, 15, 0, 230, 350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
	imagettftext($imagem, 15, 0, 320, 350, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
	imagettftext($imagem, 15, 0, 445, 350, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
	
	imagettftext($imagem, 15, 0, 230, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
	imagettftext($imagem, 15, 0, 320, 390, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));
	imagettftext($imagem, 15, 0, 445, 390, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));
	
	imagettftext($imagem, 15, 0, 230, 420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep4));
	imagettftext($imagem, 15, 0, 320, 420, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
	imagettftext($imagem, 15, 0, 445, 420, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
	
	imagettftext($imagem, 15, 0, 230, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep5));
	imagettftext($imagem, 15, 0, 320, 455, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep5));
	imagettftext($imagem, 15, 0, 445, 455, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep5));
	
	imagettftext($imagem, 15, 0, 900, 445, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
	
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '4' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom4.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '5' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom5.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '6' && $codModelo == 1){
	global $codAssociadoTmp;
	global $codigoDep1;
	global $codigoDep2;
	global $codigoDep3;
	global $codigoDep4;
	global $codigoDep5;
	
	$peso = '';
	$altura = '';
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta1_Hebrom6.jpg");
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
			$coluna = 980;
			$peso = $rowDecTit->PESO;
			$altura = $rowDecTit->ALTURA;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
			$coluna = 1020;
			$peso = $pesoDep1;
			$altura = $alturaDep1;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
			$coluna = 1060;
			$peso = $pesoDep2;
			$altura = $alturaDep2;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
			$coluna = 1100;
			$peso = $pesoDep3;
			$altura = $alturaDep3;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
			$coluna = 1140;
			$peso = $pesoDep4;
			$altura = $alturaDep4;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep5){
			$coluna = 1175;
			$peso = $pesoDep4;
			$altura = $alturaDep4;
		}
		
		if($rowDecTit->NUMERO_PERGUNTA == '1'){
			imagettftext($imagem, 14, 0, $coluna, 340, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
			imagettftext($imagem, 14, 0, $coluna, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
			imagettftext($imagem, 14, 0, $coluna, 395, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
			imagettftext($imagem, 14, 0, $coluna, 420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '5'){
			imagettftext($imagem, 14, 0, $coluna, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
			imagettftext($imagem, 14, 0, $coluna, 495, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
			imagettftext($imagem, 14, 0, $coluna, 525, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
			imagettftext($imagem, 14, 0, $coluna, 555, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
			imagettftext($imagem, 14, 0, $coluna, 585, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
			imagettftext($imagem, 14, 0, $coluna, 610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '11'){
			imagettftext($imagem, 14, 0, $coluna, 630, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
			imagettftext($imagem, 14, 0, $coluna, 660, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
			imagettftext($imagem, 14, 0, $coluna, 687, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
			imagettftext($imagem, 14, 0, $coluna, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '15'){
			imagettftext($imagem, 14, 0, $coluna, 738, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '16'){
			imagettftext($imagem, 14, 0, $coluna, 760, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '17'){
			imagettftext($imagem, 14, 0, $coluna, 790, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '18'){
			imagettftext($imagem, 14, 0, $coluna, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '19'){
			imagettftext($imagem, 14, 0, $coluna, 860, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '20'){
			imagettftext($imagem, 14, 0, $coluna, 900, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '21'){
			imagettftext($imagem, 14, 0, $coluna, 928, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '22'){
			imagettftext($imagem, 14, 0, $coluna, 955, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '23'){
			imagettftext($imagem, 14, 0, $coluna, 980, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '24'){
			imagettftext($imagem, 14, 0, $coluna, 1003, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '25'){
			imagettftext($imagem, 14, 0, $coluna, 1027, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '26'){
			imagettftext($imagem, 14, 0, $coluna, 1047, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '27'){
			imagettftext($imagem, 14, 0, $coluna, 1068, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '28'){
			imagettftext($imagem, 14, 0, $coluna, 1092, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}
	}
	
	imagettftext($imagem, 16, 0, 900, 85, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->PROTOCOLO_GERAL_PS6450));	
	imagettftext($imagem, 16, 0, 900, 190, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );	
}

if($_GET['pagina'] == '7' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta1_Hebrom7.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	imagettftext($imagem, 15, 0, 70, 232, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 15, 0, 640, 232, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));

	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );	
}

if($_GET['pagina'] == '8' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom8.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '9' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom9.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '10' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom10.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '11' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom11.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '12' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom12.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '13' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom13.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '14' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom14.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '15' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta1_Hebrom15.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
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