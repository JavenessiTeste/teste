<?php
require('../lib/base.php');

$codAssociadoTmp = trim($_GET['codAssociado']);

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, ';
$queryAssociado .= ' 	CODIGO_ESTADO_CIVIL, NOME_ESTADO_CIVIL, VND1000_ON.ORGAO_EMISSOR_RG, NUMERO_PIS, CODIGO_TABELA_PRECO, CODIGO_PAIS_EMISSOR, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, PROTOCOLO_GERAL_PS6450, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, COALESCE(PS1102.NUMERO_CPF,PS1102.NUMERO_CNPJ) AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_CADASTRO_ANS, PS1030.FLAG_COPARTICIPACAO, PS1030.CODIGO_TIPO_ABRANGENCIA, PS1030.CODIGO_TIPO_COBERTURA,  ';
$queryAssociado .= " 	CASE (PS1030.CODIGO_TIPO_COBERTURA) 
							WHEN '01' THEN 'AMBULATORIAL'
							WHEN '02' THEN 'HOSPITALAR'
							WHEN '03' THEN 'ODONTOLOGICO'
							WHEN '04' THEN 'OBSTETRICIA'
							WHEN '05' THEN 'AMBULATORIAL + HOSPITALAR'
							WHEN '06' THEN 'AMBULATORIAL + HOSPITALAR + ODONTOLOGICO'
							WHEN '07' THEN 'AMBULATORIAL + HOSPITALAR + OBSTETRICIA'
							WHEN '08' THEN 'AMBULATORIAL + HOSPITALAR + ODONTOLOGICO + OBSTETRICIA'
							WHEN '09' THEN 'AMBULATORIAL + ODONTOLOGICO'
							WHEN '10' THEN 'HOSPITALAR + ODONTOLOGICO'
							WHEN '11' THEN 'HOSPITALAR + OBSTETRICIA'
							WHEN '12' THEN 'HOSPITALAR + OBSTETRICIA + ODONTOLOGICO'
							ELSE 'OUTRAS'
						END AS NOME_TIPO_COBERTURA,  ";
$queryAssociado .= ' 	COALESCE(VND1001_ON.NOME_CONTRATANTE, VND1000_ON.NOME_ASSOCIADO) AS NOME_CONTRATANTE, COALESCE(VND1001_ON.NUMERO_CPF_CONTRATANTE, VND1000_ON.NUMERO_CPF) AS CPF_CONTRATANTE, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NOME_CONTRATANTE, VND1000_ON.NOME_ASSOCIADO) AS NOME_CONTRATANTE, COALESCE(VND1001_ON.NUMERO_CPF_CONTRATANTE, VND1000_ON.NUMERO_CPF) AS CPF_CONTRATANTE, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NUMERO_RG_CONTRATANTE, VND1000_ON.NUMERO_RG) AS RG_CONTRATANTE, PS1014.NOME_GRUPO_PESSOAS, PS1014.NUMERO_CNPJ CNPJ_ENTIDADE, PS1014.CIDADE AS CIDADE_GP_PESSOAS, PS1014.ENDERECO AS ENDERECO_GP_PESSOAS, PS1014.ESTADO AS ESTADO_GP_PESSOAS, PS1014.BAIRRO AS BAIRRO_GP_PESSOAS, PS1014.CEP AS CEP_GP_PESSOAS  ';
$queryAssociado .= ' FROM VND1000_ON ';
$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
$queryAssociado .= ' INNER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1014 ON (VND1000_ON.CODIGO_GRUPO_PESSOAS = PS1014.CODIGO_GRUPO_PESSOAS) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1044 ON (VND1000_ON.CODIGO_ESTADO_CIVIL = PS1044.CODIGO_ESTADO_CIVIL) ';
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
	
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;	
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep2->CODIGO_TABELA_PRECO);
			
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep2 = $rowValores->VALOR_PLANO;
	
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
	
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;	
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep3->CODIGO_TABELA_PRECO);
			
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep3 = $rowValores->VALOR_PLANO;
	
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
	
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;	
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep4->CODIGO_TABELA_PRECO);
			
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep4 = $rowValores->VALOR_PLANO;
	
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
	
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep5->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep5;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep5;	
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowDep5->CODIGO_TABELA_PRECO);
			
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	$valorDep5 = $rowValores->VALOR_PLANO;
	
	$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
	$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
	$numeroRGDep5 = $rowDep5->NUMERO_RG;
	$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
	$sexoDep5 = $rowDep5->SEXO;
	$nomeMaeDep5 = $rowDep5->NOME_MAE;
	$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
	$parentescoDep5 = $rowDep5->CODIGO_PARENTESCO;
	$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
	$alturaDep5 = $rowDep5->ALTURA;
	$pesoDep5 = $rowDep5->PESO;
}

$valorTotal = $valorTit + $valorDep1 + $valorDep2 + $valorDep3 + $valorDep4 + $valorDep5;

if($_GET['pagina'] == '1' && $codModelo == '1'){
	
	$segmentacao = '';
	
	if($rowAssociado->CODIGO_PLANO == '2' || $rowAssociado->CODIGO_PLANO == '3'){
		$segmentacao = 'AMB + HOSP COM OBSTETRICIA';
	}elseif($rowAssociado->CODIGO_PLANO == '4'){
		$segmentacao = 'AMBULATORIAL';
	}
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 12, 0, 80, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
	imagettftext($imagem, 12, 0, 620, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CADASTRO_ANS));
	imagettftext($imagem, 12, 0, 920, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($segmentacao));
	imagettftext($imagem, 12, 0, 80, 345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_GRUPO_PESSOAS));
	imagettftext($imagem, 12, 0, 950, 345, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
	imagettftext($imagem, 12, 0, 100, 446, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 920, 446, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
	imagettftext($imagem, 12, 0, 1120, 446, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 12, 0, 100, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 12, 0, 450, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ORGAO_EMISSOR_RG);
	imagettftext($imagem, 12, 0, 600, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 12, 0, 350, 520, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 12, 0, 750, 520, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);
	imagettftext($imagem, 12, 0, 160, 560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
	imagettftext($imagem, 12, 0, 420, 560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	
	imagettftext($imagem, 12, 0, 130, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 12, 0, 1050, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 12, 0, 150, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 12, 0, 480, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 12, 0, 650, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));	
	imagettftext($imagem, 12, 0, 900, 640, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	

	imagettftext($imagem, 12, 0, 100, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 12, 0, 920, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 12, 0, 1120, 703, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
	imagettftext($imagem, 12, 0, 100, 738, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 12, 0, 600, 738, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 12, 0, 380, 775, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 12, 0, 415, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	imagettftext($imagem, 12, 0, 100, 960, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 12, 0, 920, 960, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
	imagettftext($imagem, 12, 0, 1120, 960, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
	imagettftext($imagem, 12, 0, 100, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
	imagettftext($imagem, 12, 0, 600, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
	imagettftext($imagem, 12, 0, 380, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
	imagettftext($imagem, 12, 0, 415, 1072, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
	
	imagettftext($imagem, 12, 0, 100, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 12, 0, 920, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
	imagettftext($imagem, 12, 0, 1120, 1215, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
	imagettftext($imagem, 12, 0, 100, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
	imagettftext($imagem, 12, 0, 600, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
	imagettftext($imagem, 12, 0, 380, 1287, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
	imagettftext($imagem, 12, 0, 415, 1327, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	
	imagettftext($imagem, 12, 0, 100, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 12, 0, 920, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
	imagettftext($imagem, 12, 0, 1120, 1442, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);
	imagettftext($imagem, 12, 0, 100, 1477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
	imagettftext($imagem, 12, 0, 600, 1477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
	imagettftext($imagem, 12, 0, 380, 1514, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
	imagettftext($imagem, 12, 0, 415, 1554, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
	
	imagettftext($imagem, 11, 0, 840, 1690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C2.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$nacionalidade = '';
	
	if($rowAssociado->CODIGO_PAIS_EMISSOR == '' || $rowAssociado->CODIGO_PAIS_EMISSOR == '51'){
		$nacionalidade = 'BRASIL';
	}
	
	imagettftext($imagem, 12, 0, 40, 57, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 600, 57, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nacionalidade));
	imagettftext($imagem, 12, 0, 850, 57, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
	imagettftext($imagem, 12, 0, 590, 81, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
	imagettftext($imagem, 11, 0, 995, 81, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
	imagettftext($imagem, 11, 0, 288, 108, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 12, 0, 527, 108, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	imagettftext($imagem, 11, 0, 618, 108, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->ENDERECO, 0, 45)));
	imagettftext($imagem, 11, 0, 1100, 108, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->BAIRRO, 0, 13)));
	imagettftext($imagem, 12, 0, 100, 130, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 12, 0, 40, 175, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_GRUPO_PESSOAS));
	imagettftext($imagem, 12, 0, 1020, 175, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CNPJ_ENTIDADE));
	imagettftext($imagem, 12, 0, 620, 200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->ENDERECO_GP_PESSOAS, 0, 25)));
	imagettftext($imagem, 12, 0, 280, 200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->CIDADE_GP_PESSOAS, 0, 25)));
	imagettftext($imagem, 12, 0, 530, 200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->ESTADO_GP_PESSOAS, 0, 25)));
	imagettftext($imagem, 12, 0, 930, 200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->BAIRRO_GP_PESSOAS, 0, 12)));
	imagettftext($imagem, 12, 0, 1120, 200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->CEP_GP_PESSOAS, 0, 12)));
	
	$queryValores  = ' SELECT VALOR_PLANO, IDADE_MINIMA FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);	
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowAssociado->CODIGO_TABELA_PRECO);
			
	$resValores = jn_query($queryValores);	
	while($rowValores = jn_fetch_object($resValores)){
		
		if($rowValores->IDADE_MINIMA == 0){
			imagettftext($imagem, 14, 0, 557, 955, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 19){
			imagettftext($imagem, 14, 0, 557, 980, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 24){
			imagettftext($imagem, 14, 0, 557, 1005, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 29){
			imagettftext($imagem, 14, 0, 557, 1030, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 34){
			imagettftext($imagem, 14, 0, 557, 1055, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 39){
			imagettftext($imagem, 14, 0, 557, 1080, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 44){
			imagettftext($imagem, 14, 0, 557, 1105, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 49){
			imagettftext($imagem, 14, 0, 557, 1130, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 54){
			imagettftext($imagem, 14, 0, 557, 1155, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 59){
			imagettftext($imagem, 14, 0, 557, 1180, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '3' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C3.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	if($rowAssociado->CODIGO_PLANO == '2'){
		imagettftext($imagem, 18, 0, 65, 190, $cor,"../../Site/assets/img/arial.ttf",'X');
	}elseif($rowAssociado->CODIGO_PLANO == '3'){
		imagettftext($imagem, 18, 0, 65, 225, $cor,"../../Site/assets/img/arial.ttf",'X');		
	}elseif($rowAssociado->CODIGO_PLANO == '4'){
		imagettftext($imagem, 18, 0, 65, 260, $cor,"../../Site/assets/img/arial.ttf",'X');		
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '4' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_MV2C4.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '5' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C5.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	imagettftext($imagem, 14, 0, 90, 510, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 14, 0, 280, 510, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 14, 0, 370, 510, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 14, 0, 470, 510, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '6' && $codModelo == 1){
	$img = imagecreatefromjpeg('../../Site/assets/img/proposta_MV2C6.jpg');
	header( "Content-type: image/jpeg" );
	return imagejpeg( $img, NULL);	
}

if($_GET['pagina'] == '7' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C7.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	imagettftext($imagem, 14, 0, 220, 1350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 14, 0, 475, 1350, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 14, 0, 520, 1350, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 14, 0, 565, 1350, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	
	imagettftext($imagem, 14, 0, 810, 1350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 14, 0, 1060, 1350, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 14, 0, 1110, 1350, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 14, 0, 1160, 1350, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	imagettftext($imagem, 14, 0, 810, 1415, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
	imagettftext($imagem, 14, 0, 810, 1450, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
	imagettftext($imagem, 14, 0, 830, 1488, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
	
	imagettftext($imagem, 12, 0, 220, 1410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 220, 1460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '8' && $codModelo == 1){
	global $codAssociadoTmp;
	global $codigoDep1;
	global $codigoDep2;
	global $codigoDep3;
	global $codigoDep4;
	global $codigoDep5;
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C8.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	imagettftext($imagem, 14, 0, 210, 265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 14, 0, 750, 265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->PESO));
	imagettftext($imagem, 14, 0, 1000, 265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ALTURA));
	imagettftext($imagem, 14, 0, 300, 300, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 14, 0, 750, 300, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep1));
	imagettftext($imagem, 14, 0, 1000, 300, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep1));
	imagettftext($imagem, 14, 0, 300, 332, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 14, 0, 750, 332, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep2));
	imagettftext($imagem, 14, 0, 1000, 332, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep2));
	imagettftext($imagem, 14, 0, 300, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 14, 0, 750, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep3));
	imagettftext($imagem, 14, 0, 1000, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep3));
	imagettftext($imagem, 14, 0, 300, 395, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 14, 0, 750, 395, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep4));
	imagettftext($imagem, 14, 0, 1000, 395, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep4));
	imagettftext($imagem, 14, 0, 300, 425, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep5));
	imagettftext($imagem, 14, 0, 750, 425, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep5));
	imagettftext($imagem, 14, 0, 1000, 425, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep5));
	

	$queryDecTit  = ' SELECT ';
	$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.PESO, VND1000_ON.ALTURA, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO ';
	$queryDecTit .= ' FROM VND1000_ON ';
	$queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
	$queryDecTit .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
	$queryDecTit .= ' WHERE PS1039.NUMERO_PERGUNTA BETWEEN "1" AND "4" AND VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
	$queryDecTit .= ' AND VND1000_ON.ULTIMO_STATUS <> "AGUARDANDO_DECL_SAUDE" '; 	
	$queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';	
	
	$resDecTit = jn_query($queryDecTit); 
		
	while($rowDecTit = jn_fetch_object($resDecTit)){	
		$coluna = '';
		$codigoTit = $codAssociadoTmp;
		
		if($rowDecTit->TIPO_ASSOCIADO == 'T'){
			$coluna = 740;
			$linhaDesc = 720;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
			$coluna = 810;
			$linhaDesc = 750;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
			$coluna = 880;
			$linhaDesc = 777;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
			$coluna = 950;
			$linhaDesc = 800;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
			$coluna = 1020;
			$linhaDesc = 825;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep5){
			$coluna = 1070;
			$linhaDesc = 850;
		}
		
		if($rowDecTit->NUMERO_PERGUNTA == '1'){
			imagettftext($imagem, 14, 0, $coluna, 680, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
			imagettftext($imagem, 14, 0, $coluna, 940, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));				
			$linhaDesc += 255;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
			imagettftext($imagem, 14, 0, $coluna, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));				
			$linhaDesc += 530;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
			imagettftext($imagem, 14, 0, $coluna, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 805;
		}
	
		imagettftext($imagem, 14, 0, 650, $linhaDesc, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->DESCRICAO_OBSERVACAO));	
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '9' && $codModelo == 1){
	global $codAssociadoTmp;
	global $codigoDep1;
	global $codigoDep2;
	global $codigoDep3;
	global $codigoDep4;
	global $codigoDep5;
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C9.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$queryDecTit  = ' SELECT ';
	$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.PESO, VND1000_ON.ALTURA, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO ';
	$queryDecTit .= ' FROM VND1000_ON ';
	$queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
	$queryDecTit .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
	$queryDecTit .= ' WHERE PS1039.NUMERO_PERGUNTA BETWEEN "5" AND "10" AND VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
	$queryDecTit .= ' AND VND1000_ON.ULTIMO_STATUS <> "AGUARDANDO_DECL_SAUDE" '; 	
	$queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
	$resDecTit = jn_query($queryDecTit); 
		
	while($rowDecTit = jn_fetch_object($resDecTit)){	
		$coluna = '';
		$codigoTit = $codAssociadoTmp;
		
		if($rowDecTit->TIPO_ASSOCIADO == 'T'){
			$coluna = 740;
			$linhaDesc = 185;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
			$coluna = 810;
			$linhaDesc = 215;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
			$coluna = 880;
			$linhaDesc = 240;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
			$coluna = 950;
			$linhaDesc = 265;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
			$coluna = 1020;
			$linhaDesc = 290;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep5){
			$coluna = 1070;
			$linhaDesc = 315;
		}
		
		
		if($rowDecTit->NUMERO_PERGUNTA == '5'){
			imagettftext($imagem, 14, 0, $coluna, 140, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
			imagettftext($imagem, 14, 0, $coluna, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 260;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
			imagettftext($imagem, 14, 0, $coluna, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 535;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
			imagettftext($imagem, 14, 0, $coluna, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 828;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
			imagettftext($imagem, 14, 0, $coluna, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 1088;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
			imagettftext($imagem, 14, 0, $coluna, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 1345;
		}
		
		imagettftext($imagem, 14, 0, 650, $linhaDesc, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->DESCRICAO_OBSERVACAO));	
		
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '10' && $codModelo == 1){
	global $codAssociadoTmp;
	global $codigoDep1;
	global $codigoDep2;
	global $codigoDep3;
	global $codigoDep4;
	global $codigoDep5;
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C10.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$queryDecTit  = ' SELECT ';
	$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.PESO, VND1000_ON.ALTURA, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO ';
	$queryDecTit .= ' FROM VND1000_ON ';
	$queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
	$queryDecTit .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
	$queryDecTit .= ' WHERE PS1039.NUMERO_PERGUNTA BETWEEN "11" AND "16" AND VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
	$queryDecTit .= ' AND VND1000_ON.ULTIMO_STATUS <> "AGUARDANDO_DECL_SAUDE" ';
	$queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
	$resDecTit = jn_query($queryDecTit); 
		
	while($rowDecTit = jn_fetch_object($resDecTit)){	
		$coluna = '';
		$codigoTit = $codAssociadoTmp;
		
		if($rowDecTit->TIPO_ASSOCIADO == 'T'){
			$coluna = 740;
			$linhaDesc = 193;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
			$coluna = 810;
			$linhaDesc = 220;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
			$coluna = 880;
			$linhaDesc = 248;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
			$coluna = 950;
			$linhaDesc = 273;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
			$coluna = 1020;
			$linhaDesc = 300;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep5){
			$coluna = 1070;
			$linhaDesc = 325;
		}
		
		
		if($rowDecTit->NUMERO_PERGUNTA == '11'){
			imagettftext($imagem, 14, 0, $coluna, 140, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
			imagettftext($imagem, 14, 0, $coluna, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 260;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
			imagettftext($imagem, 14, 0, $coluna, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 523;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
			imagettftext($imagem, 14, 0, $coluna, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 798;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '15'){
			imagettftext($imagem, 14, 0, $coluna, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 1070;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '16'){
			imagettftext($imagem, 14, 0, $coluna, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 1332;
		}
		
		imagettftext($imagem, 14, 0, 650, $linhaDesc, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->DESCRICAO_OBSERVACAO));	
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '11' && $codModelo == 1){
	global $codAssociadoTmp;
	global $codigoDep1;
	global $codigoDep2;
	global $codigoDep3;
	global $codigoDep4;
	global $codigoDep5;
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C11.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$queryDecTit  = ' SELECT ';
	$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.PESO, VND1000_ON.ALTURA, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO ';
	$queryDecTit .= ' FROM VND1000_ON ';
	$queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
	$queryDecTit .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
	$queryDecTit .= ' WHERE PS1039.NUMERO_PERGUNTA BETWEEN "17" AND "21" AND VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
	$queryDecTit .= ' AND VND1000_ON.ULTIMO_STATUS <> "AGUARDANDO_DECL_SAUDE" ';
	$queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
	$resDecTit = jn_query($queryDecTit); 
		
	while($rowDecTit = jn_fetch_object($resDecTit)){	
		$coluna = '';
		$codigoTit = $codAssociadoTmp;
		
		if($rowDecTit->TIPO_ASSOCIADO == 'T'){
			$coluna = 740;
			$linhaDesc = 188;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
			$coluna = 810;
			$linhaDesc = 212;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
			$coluna = 880;
			$linhaDesc = 238;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
			$coluna = 950;
			$linhaDesc = 263;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
			$coluna = 1020;
			$linhaDesc = 290;
		}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep5){
			$coluna = 1070;
			$linhaDesc = 315;
		}
		
		
		if($rowDecTit->NUMERO_PERGUNTA == '17'){
			imagettftext($imagem, 14, 0, $coluna, 140, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));				
		}elseif($rowDecTit->NUMERO_PERGUNTA == '18'){
			imagettftext($imagem, 14, 0, $coluna, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 260;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '19'){
			imagettftext($imagem, 14, 0, $coluna, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 530;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '20'){
			imagettftext($imagem, 14, 0, $coluna, 930, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			$linhaDesc += 790;
		}elseif($rowDecTit->NUMERO_PERGUNTA == '21'){
			$linhaDesc += 1050;
			imagettftext($imagem, 14, 0, $coluna, 1180, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
		}
		
		imagettftext($imagem, 14, 0, 650, $linhaDesc, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->DESCRICAO_OBSERVACAO));	
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '12' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_MV2C12.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	imagettftext($imagem, 18, 0, 87, 470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	imagettftext($imagem, 18, 0, 87, 1042, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	imagettftext($imagem, 14, 0, 220, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 14, 0, 440, 1210, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 14, 0, 520, 1210, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 14, 0, 660, 1210, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	imagettftext($imagem, 12, 0, 500, 1350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '1' && $codModelo == '2'){
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_MV2C1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 12, 0, 80, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
	imagettftext($imagem, 12, 0, 620, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CADASTRO_ANS));
	imagettftext($imagem, 12, 0, 930, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('ODONTOLOGICO'));
	imagettftext($imagem, 12, 0, 80, 345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_GRUPO_PESSOAS));
	imagettftext($imagem, 12, 0, 950, 345, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
	imagettftext($imagem, 12, 0, 100, 446, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 920, 446, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
	imagettftext($imagem, 12, 0, 1120, 446, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 12, 0, 100, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 12, 0, 450, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ORGAO_EMISSOR_RG);
	imagettftext($imagem, 12, 0, 600, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 12, 0, 350, 520, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 12, 0, 750, 520, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);
	imagettftext($imagem, 12, 0, 420, 560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	
	imagettftext($imagem, 12, 0, 130, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 12, 0, 1050, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 12, 0, 150, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 12, 0, 480, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 12, 0, 650, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));	
	imagettftext($imagem, 12, 0, 900, 640, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	

	imagettftext($imagem, 12, 0, 100, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 12, 0, 920, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 12, 0, 1120, 703, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
	imagettftext($imagem, 12, 0, 100, 738, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 12, 0, 600, 738, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 12, 0, 380, 775, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 12, 0, 415, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	imagettftext($imagem, 12, 0, 100, 960, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 12, 0, 920, 960, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
	imagettftext($imagem, 12, 0, 1120, 960, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
	imagettftext($imagem, 12, 0, 100, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
	imagettftext($imagem, 12, 0, 600, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
	imagettftext($imagem, 12, 0, 380, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
	imagettftext($imagem, 12, 0, 415, 1072, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
	
	imagettftext($imagem, 12, 0, 100, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 12, 0, 920, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
	imagettftext($imagem, 12, 0, 1120, 1215, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
	imagettftext($imagem, 12, 0, 100, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
	imagettftext($imagem, 12, 0, 600, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
	imagettftext($imagem, 12, 0, 380, 1287, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
	imagettftext($imagem, 12, 0, 415, 1327, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	
	imagettftext($imagem, 12, 0, 100, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 12, 0, 920, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
	imagettftext($imagem, 12, 0, 1120, 1442, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);
	imagettftext($imagem, 12, 0, 100, 1477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
	imagettftext($imagem, 12, 0, 600, 1477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
	imagettftext($imagem, 12, 0, 380, 1514, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
	imagettftext($imagem, 12, 0, 415, 1554, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
	
	imagettftext($imagem, 11, 0, 840, 1690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '2' && $codModelo == '2'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_MV2C2.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	imagettftext($imagem, 12, 0, 70, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_GRUPO_PESSOAS, 0, 50)));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '3' && $codModelo == '2'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_MV2C3.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '4' && $codModelo == '2'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_MV2C4.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '5' && $codModelo == '2'){
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_MV2C5.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$fatorModeracao = '';
	if($rowAssociado->FLAG_COPARTICIPACAO == 'S'){
		$fatorModeracao = 'SIM';		
	}else{		
		$fatorModeracao = 'NAO';		
	}
	
	$abrangencia = '';
	if($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '1'){
		$abrangencia = 'NACIONAL';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '2'){
		$abrangencia = 'GRUPO DE ESTADOS';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '3'){
		$abrangencia = 'ESTADUAL';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '4'){
		$abrangencia = 'GRUPO DE MUNICÍPIOS';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '5'){
		$abrangencia = 'MUNICIPAL';
	}else{
		$abrangencia = 'OUTROS';
	}
	
	imagettftext($imagem, 18, 0, 65, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	imagettftext($imagem, 12, 0, 250, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
	imagettftext($imagem, 12, 0, 130, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CADASTRO_ANS));
	imagettftext($imagem, 12, 0, 740, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($fatorModeracao));
	imagettftext($imagem, 12, 0, 880, 355, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
	imagettftext($imagem, 12, 0, 1040, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($abrangencia));
	imagettftext($imagem, 12, 0, 130, 570, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 840, 570, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}



if($_GET['pagina'] == '1' && $codModelo == '3'){
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_MV2C1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 12, 0, 80, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
	imagettftext($imagem, 12, 0, 620, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CADASTRO_ANS));
	imagettftext($imagem, 12, 0, 930, 281, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('ODONTOLOGICO'));
	imagettftext($imagem, 12, 0, 80, 345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_GRUPO_PESSOAS));
	imagettftext($imagem, 12, 0, 950, 345, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
	imagettftext($imagem, 12, 0, 100, 446, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 920, 446, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
	imagettftext($imagem, 12, 0, 1120, 446, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 12, 0, 100, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 12, 0, 450, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ORGAO_EMISSOR_RG);
	imagettftext($imagem, 12, 0, 600, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 12, 0, 350, 520, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 12, 0, 750, 520, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);
	imagettftext($imagem, 12, 0, 420, 560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	
	imagettftext($imagem, 12, 0, 130, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 12, 0, 1050, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 12, 0, 150, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 12, 0, 480, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 12, 0, 650, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));	
	imagettftext($imagem, 12, 0, 900, 640, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	

	imagettftext($imagem, 12, 0, 100, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 12, 0, 920, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 12, 0, 1120, 703, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
	imagettftext($imagem, 12, 0, 100, 738, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 12, 0, 600, 738, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 12, 0, 380, 775, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 12, 0, 415, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	imagettftext($imagem, 12, 0, 100, 960, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 12, 0, 920, 960, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
	imagettftext($imagem, 12, 0, 1120, 960, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
	imagettftext($imagem, 12, 0, 100, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
	imagettftext($imagem, 12, 0, 600, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
	imagettftext($imagem, 12, 0, 380, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
	imagettftext($imagem, 12, 0, 415, 1072, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
	
	imagettftext($imagem, 12, 0, 100, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 12, 0, 920, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
	imagettftext($imagem, 12, 0, 1120, 1215, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
	imagettftext($imagem, 12, 0, 100, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
	imagettftext($imagem, 12, 0, 600, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
	imagettftext($imagem, 12, 0, 380, 1287, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
	imagettftext($imagem, 12, 0, 415, 1327, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	
	imagettftext($imagem, 12, 0, 100, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 12, 0, 920, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
	imagettftext($imagem, 12, 0, 1120, 1442, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);
	imagettftext($imagem, 12, 0, 100, 1477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
	imagettftext($imagem, 12, 0, 600, 1477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
	imagettftext($imagem, 12, 0, 380, 1514, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
	imagettftext($imagem, 12, 0, 415, 1554, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
	
	imagettftext($imagem, 11, 0, 890, 1690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '2' && $codModelo == '3'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_MV2C2.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '3' && $codModelo == '3'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_MV2C3.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '4' && $codModelo == '3'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_MV2C4.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '5' && $codModelo == '3'){
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_MV2C5.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$fatorModeracao = '';
	if($rowAssociado->FLAG_COPARTICIPACAO == 'S'){
		$fatorModeracao = 'SIM';		
	}else{		
		$fatorModeracao = 'NAO';		
	}
	
	$abrangencia = '';
	if($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '1'){
		$abrangencia = 'NACIONAL';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '2'){
		$abrangencia = 'GRUPO DE ESTADOS';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '3'){
		$abrangencia = 'ESTADUAL';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '4'){
		$abrangencia = 'GRUPO DE MUNICÍPIOS';
	}elseif($rowAssociado->CODIGO_TIPO_ABRANGENCIA == '5'){
		$abrangencia = 'MUNICIPAL';
	}else{
		$abrangencia = 'OUTROS';
	}
	
	imagettftext($imagem, 18, 0, 65, 520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
	imagettftext($imagem, 12, 0, 250, 520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
	imagettftext($imagem, 12, 0, 130, 520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CADASTRO_ANS));
	imagettftext($imagem, 12, 0, 740, 520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($fatorModeracao));
	imagettftext($imagem, 12, 0, 880, 520, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
	imagettftext($imagem, 12, 0, 1040, 520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($abrangencia));
	imagettftext($imagem, 12, 0, 140, 805, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 840, 805, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '1' && $codModelo == '4'){
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta4_MV2C1.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 12, 0, 80, 278, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
	imagettftext($imagem, 12, 0, 620, 278, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CADASTRO_ANS));
	imagettftext($imagem, 10, 0, 870, 278, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_TIPO_COBERTURA));
	imagettftext($imagem, 12, 0, 80, 340, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_GRUPO_PESSOAS));
	imagettftext($imagem, 12, 0, 950, 340, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
	imagettftext($imagem, 12, 0, 105, 440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 900, 440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
	imagettftext($imagem, 12, 0, 1115, 440, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
	imagettftext($imagem, 12, 0, 100, 475, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 12, 0, 450, 475, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ORGAO_EMISSOR_RG);
	imagettftext($imagem, 12, 0, 603, 475, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 12, 0, 385, 515, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
	imagettftext($imagem, 12, 0, 750, 515, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);
	imagettftext($imagem, 12, 0, 180, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
	imagettftext($imagem, 12, 0, 425, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
	
	imagettftext($imagem, 12, 0, 140, 590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
	imagettftext($imagem, 12, 0, 1050, 590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
	imagettftext($imagem, 12, 0, 150, 630, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 12, 0, 480, 630, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 12, 0, 650, 630, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));	
	imagettftext($imagem, 12, 0, 900, 630, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	

	imagettftext($imagem, 12, 0, 105, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
	imagettftext($imagem, 12, 0, 920, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
	imagettftext($imagem, 11, 0, 1127, 690, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
	imagettftext($imagem, 12, 0, 100, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
	imagettftext($imagem, 12, 0, 600, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
	imagettftext($imagem, 12, 0, 380, 768, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
	imagettftext($imagem, 12, 0, 419, 805, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
	
	imagettftext($imagem, 12, 0, 105, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
	imagettftext($imagem, 12, 0, 920, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
	imagettftext($imagem, 11, 0, 1127, 950, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
	imagettftext($imagem, 12, 0, 100, 985, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
	imagettftext($imagem, 12, 0, 600, 985, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
	imagettftext($imagem, 12, 0, 380, 1020, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
	imagettftext($imagem, 12, 0, 419, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
	
	imagettftext($imagem, 12, 0, 105, 1205, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
	imagettftext($imagem, 12, 0, 920, 1205, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
	imagettftext($imagem, 11, 0, 1127, 1205, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
	imagettftext($imagem, 12, 0, 100, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
	imagettftext($imagem, 12, 0, 600, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
	imagettftext($imagem, 12, 0, 380, 1277, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
	imagettftext($imagem, 12, 0, 419, 1317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
	
	imagettftext($imagem, 12, 0, 105, 1432, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
	imagettftext($imagem, 12, 0, 920, 1432, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
	imagettftext($imagem, 11, 0, 1127, 1432, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);
	imagettftext($imagem, 12, 0, 100, 1470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
	imagettftext($imagem, 12, 0, 600, 1470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
	imagettftext($imagem, 12, 0, 380, 1504, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
	imagettftext($imagem, 12, 0, 419, 1544, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
	
	imagettftext($imagem, 11, 0, 890, 1690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '2' && $codModelo == '4'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta4_MV2C2.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$nacionalidade = '';
	
	if($rowAssociado->CODIGO_PAIS_EMISSOR == '' || $rowAssociado->CODIGO_PAIS_EMISSOR == '51'){
		$nacionalidade = 'BRASIL';
	}
	
	imagettftext($imagem, 12, 0, 40, 95, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	imagettftext($imagem, 12, 0, 600, 95, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nacionalidade));
	imagettftext($imagem, 12, 0, 850, 95, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
	imagettftext($imagem, 12, 0, 590, 125, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
	imagettftext($imagem, 11, 0, 995, 125, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
	imagettftext($imagem, 11, 0, 288, 150, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
	imagettftext($imagem, 12, 0, 527, 150, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	imagettftext($imagem, 11, 0, 618, 150, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->ENDERECO, 0, 45)));
	imagettftext($imagem, 11, 0, 1100, 150, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->BAIRRO, 0, 13)));
	imagettftext($imagem, 12, 0, 100, 175, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 12, 0, 40, 230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_GRUPO_PESSOAS));
	imagettftext($imagem, 12, 0, 1020, 230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CNPJ_ENTIDADE));
	imagettftext($imagem, 12, 0, 280, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->CIDADE_GP_PESSOAS, 0, 25)));
	imagettftext($imagem, 12, 0, 570, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->ESTADO_GP_PESSOAS, 0, 25)));
	imagettftext($imagem, 12, 0, 685, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->ENDERECO_GP_PESSOAS, 0, 25)));
	imagettftext($imagem, 12, 0, 1070, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->BAIRRO_GP_PESSOAS, 0, 12)));
	imagettftext($imagem, 12, 0, 95, 280, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->CEP_GP_PESSOAS, 0, 12)));
	
	$queryValores  = ' SELECT VALOR_PLANO, IDADE_MINIMA FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);	
	$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowAssociado->CODIGO_TABELA_PRECO);
			
	$resValores = jn_query($queryValores);	
	while($rowValores = jn_fetch_object($resValores)){
		
		if($rowValores->IDADE_MINIMA == 0){
			imagettftext($imagem, 14, 0, 557, 1160, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 19){
			imagettftext($imagem, 14, 0, 557, 1185, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 24){
			imagettftext($imagem, 14, 0, 557, 1210, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 29){
			imagettftext($imagem, 14, 0, 557, 1235, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 34){
			imagettftext($imagem, 14, 0, 557, 1260, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 39){
			imagettftext($imagem, 14, 0, 557, 1285, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 44){
			imagettftext($imagem, 14, 0, 557, 1310, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 49){
			imagettftext($imagem, 14, 0, 557, 1335, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 54){
			imagettftext($imagem, 14, 0, 557, 1360, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}elseif($rowValores->IDADE_MINIMA == 59){
			imagettftext($imagem, 14, 0, 557, 1385, $cor,"../../Site/assets/img/arial.ttf",number_format($rowValores->VALOR_PLANO, 2));
		}
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '3' && $codModelo == '4'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta4_MV2C3.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	$primeiroNome = explode(' ', $rowAssociado->NOME_ASSOCIADO);
	
	if($rowAssociado->CODIGO_CADASTRO_ANS == '487336200'){
		imagettftext($imagem, 12, 0, 40, 520, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($primeiroNome[0]));
	}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '487337208'){
		imagettftext($imagem, 12, 0, 40, 565, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($primeiroNome[0]));		
	}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '487334203'){
		imagettftext($imagem, 12, 0, 40, 610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($primeiroNome[0]));		
	}
	
	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '4' && $codModelo == '4'){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta4_MV2C4.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$image_p = imagecreatetruecolor(1242, 1755);
	imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
	header( "Content-type: image/jpeg" );
	return imagejpeg( $image_p, NULL, 80 );
}

if($_GET['pagina'] == '5' && $codModelo == '4'){
	
	$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta4_MV2C5.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
	imagettftext($imagem, 12, 0, 80, 1470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	
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