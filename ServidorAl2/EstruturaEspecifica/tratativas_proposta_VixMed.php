<?php
require('../lib/base.php');

$codAssociadoTmp = trim($_GET['codAssociado']);

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, CODIGO_CNS, ';
$queryAssociado .= ' 	CODIGO_ESTADO_CIVIL, NOME_ESTADO_CIVIL, VND1000_ON.ORGAO_EMISSOR_RG, NUMERO_PIS, CODIGO_TABELA_PRECO, CODIGO_PAIS_EMISSOR, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, COALESCE(PS1102.NUMERO_CPF,PS1102.NUMERO_CNPJ) AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, replace(replace(PS1030.CODIGO_CADASTRO_ANS,".",""),"-","") CODIGO_CADASTRO_ANS, PS1030.FLAG_COPARTICIPACAO, PS1030.CODIGO_TIPO_ABRANGENCIA,  ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NOME_CONTRATANTE, VND1000_ON.NOME_ASSOCIADO) AS NOME_CONTRATANTE, COALESCE(VND1001_ON.NUMERO_CPF_CONTRATANTE, VND1000_ON.NUMERO_CPF) AS CPF_CONTRATANTE, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NUMERO_RG_CONTRATANTE, VND1000_ON.NUMERO_RG) AS RG_CONTRATANTE, PS1014.NOME_GRUPO_PESSOAS, PS1014.NUMERO_CNPJ CNPJ_ENTIDADE, PS1014.CIDADE AS CIDADE_GP_PESSOAS, PS1014.ENDERECO AS ENDERECO_GP_PESSOAS, PS1014.ESTADO AS ESTADO_GP_PESSOAS, PS1014.BAIRRO AS BAIRRO_GP_PESSOAS, PS1014.CEP AS CEP_GP_PESSOAS,VND1000_ON.FORMA_PAGAMENTO  ';
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

$listNascTit = list($anoNascTit, $mesNascTit, $diaNascTit) = explode('-', $dtNascTit);
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
	
	$listNascDep1 = list($anoNascDep1, $mesNascDep1, $diaNascDep1) = explode('-', $dtNascDep1);
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
	
	$listNascDep2 = list($anoNascDep2, $mesNascDep2, $diaNascDep2) = explode('-', $dtNascDep2);
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
	
	$listNascDep3 = list($anoNascDep3, $mesNascDep3, $diaNascDep3) = explode('-', $dtNascDep3);
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
	
	$listNascDep4 = list($anoNascDep4, $mesNascDep4, $diaNascDep4) = explode('-', $dtNascDep4);
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
	
	$listNascDep5 = list($anoNascDep5, $mesNascDep5, $diaNascDep5) = explode('-', $dtNascDep5);
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

$codModelo = '1';

if($codModelo == '1'){
	if($_GET['pagina'] == '1'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed1.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$diaAdmissao = '';
		$mesAdmissao = '';
		$anoAdmissao = '';
		$dtAdmissao = $rowAssociado->DATA_ADMISSAO;
		$listAdmis = list($anoAdmissao, $mesAdmissao, $diaAdmissao) = explode('-', $dtAdmissao);
		$diaAdmissao = explode(' ', $diaAdmissao);
		$diaAdmissao = $diaAdmissao[0];

		imagettftext($imagem, 15, 0, 370, 240, $cor,"../../Site/assets/img/arial.ttf",'X');			
		imagettftext($imagem, 12, 0, 90, 280, $cor,"../../Site/assets/img/arial.ttf",$diaAdmissao);
		imagettftext($imagem, 12, 0, 150, 280, $cor,"../../Site/assets/img/arial.ttf",$mesAdmissao);
		imagettftext($imagem, 12, 0, 220, 280, $cor,"../../Site/assets/img/arial.ttf",$anoAdmissao);
		imagettftext($imagem, 10, 0, 740, 240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_GRUPO_PESSOAS));
		imagettftext($imagem, 10, 0, 710, 285, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CNPJ_ENTIDADE));
		imagettftext($imagem, 10, 0, 80, 380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 100, 420, $cor,"../../Site/assets/img/arial.ttf",$diaNascTit);
		imagettftext($imagem, 12, 0, 160, 420, $cor,"../../Site/assets/img/arial.ttf",$listNascTit[1]);
		imagettftext($imagem, 12, 0, 230, 420, $cor,"../../Site/assets/img/arial.ttf",$listNascTit[0]);
		imagettftext($imagem, 10, 0, 350, 420, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
		imagettftext($imagem, 10, 0, 650, 420, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
		imagettftext($imagem, 10, 0, 950, 420, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_CNS);
		
		if($rowAssociado->SEXO == 'M'){
			imagettftext($imagem, 15, 0, 55, 470, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}else{
			imagettftext($imagem, 15, 0, 128, 470, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}
		
		imagettftext($imagem, 12, 0, 260, 470, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NOME_ESTADO_CIVIL);
		imagettftext($imagem, 12, 0, 480, 470, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_02);
		imagettftext($imagem, 12, 0, 750, 470, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
		imagettftext($imagem, 10, 0, 70, 510, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 10, 0, 70, 555, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->ENDERECO_EMAIL);
		imagettftext($imagem, 12, 0, 70, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 12, 0, 70, 645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 12, 0, 460, 645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 12, 0, 740, 645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));	
		imagettftext($imagem, 12, 0, 800, 645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 12, 0, 1000, 645, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));

		//Dados Dep1
		imagettftext($imagem, 12, 0, 70, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
		imagettftext($imagem, 12, 0, 875, 730, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep1);
		imagettftext($imagem, 12, 0, 920, 730, $cor,"../../Site/assets/img/arial.ttf",$listNascDep1[1]);
		imagettftext($imagem, 12, 0, 970, 730, $cor,"../../Site/assets/img/arial.ttf",$listNascDep1[0]);
		
		if($sexoDep1 == 'M'){
			imagettftext($imagem, 15, 0, 1055, 735, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}elseif($sexoDep1 == 'F'){
			imagettftext($imagem, 15, 0, 1130, 735, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}
		
		imagettftext($imagem, 12, 0, 200, 775, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
		imagettftext($imagem, 12, 0, 500, 775, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
		imagettftext($imagem, 12, 0, 780, 775, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
		imagettftext($imagem, 12, 0, 70, 822, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
		imagettftext($imagem, 12, 0, 1000, 822, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
		
		
		//Dados Dep2
		imagettftext($imagem, 12, 0, 70, 860, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
		imagettftext($imagem, 12, 0, 875, 860, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep2);
		imagettftext($imagem, 12, 0, 920, 860, $cor,"../../Site/assets/img/arial.ttf",$listNascDep2[1]);
		imagettftext($imagem, 12, 0, 970, 860, $cor,"../../Site/assets/img/arial.ttf",$listNascDep2[0]);		
		
		if($sexoDep2 == 'M'){
			imagettftext($imagem, 15, 0, 1055, 870, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}elseif($sexoDep2 == 'F'){
			imagettftext($imagem, 15, 0, 1130, 870, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}
		
		imagettftext($imagem, 12, 0, 200, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
		imagettftext($imagem, 12, 0, 500, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
		imagettftext($imagem, 12, 0, 780, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
		imagettftext($imagem, 12, 0, 70, 958, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
		imagettftext($imagem, 12, 0, 1000, 958, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
		
		
		//Dados Dep3
		imagettftext($imagem, 12, 0, 70, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
		imagettftext($imagem, 12, 0, 875, 995, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep3);
		imagettftext($imagem, 12, 0, 920, 995, $cor,"../../Site/assets/img/arial.ttf",$listNascDep3[1]);
		imagettftext($imagem, 12, 0, 970, 995, $cor,"../../Site/assets/img/arial.ttf",$listNascDep3[0]);		
		
		if($sexoDep3 == 'M'){
			imagettftext($imagem, 15, 0, 1055, 995, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}elseif($sexoDep3 == 'F'){
			imagettftext($imagem, 15, 0, 1130, 995, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}
		
		imagettftext($imagem, 12, 0, 200, 1045, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
		imagettftext($imagem, 12, 0, 500, 1045, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
		imagettftext($imagem, 12, 0, 780, 1055, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
		imagettftext($imagem, 12, 0, 70, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
		imagettftext($imagem, 12, 0, 1000, 1090, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));

		//Dados Dep4
		imagettftext($imagem, 12, 0, 70, 1130, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
		imagettftext($imagem, 12, 0, 875, 1130, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep4);
		imagettftext($imagem, 12, 0, 920, 1130, $cor,"../../Site/assets/img/arial.ttf",$listNascDep4[1]);
		imagettftext($imagem, 12, 0, 970, 1130, $cor,"../../Site/assets/img/arial.ttf",$listNascDep4[0]);
		
		if($sexoDep4 == 'M'){
			imagettftext($imagem, 15, 0, 1055, 1135, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}elseif($sexoDep4 == 'F'){
			imagettftext($imagem, 15, 0, 1130, 1135, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}
		
		imagettftext($imagem, 12, 0, 200, 1180, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
		imagettftext($imagem, 12, 0, 500, 1180, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
		imagettftext($imagem, 12, 0, 780, 1180, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
		imagettftext($imagem, 12, 0, 70, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
		imagettftext($imagem, 12, 0, 1000, 1220, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
		
		
		//Dados Dep5
		imagettftext($imagem, 12, 0, 70, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep5));
		imagettftext($imagem, 12, 0, 875, 1260, $cor,"../../Site/assets/img/arial.ttf",$diaNascDep5);
		imagettftext($imagem, 12, 0, 920, 1260, $cor,"../../Site/assets/img/arial.ttf",$listNascDep5[1]);
		imagettftext($imagem, 12, 0, 970, 1260, $cor,"../../Site/assets/img/arial.ttf",$listNascDep5[0]);
		
		if($sexoDep5 == 'M'){
			imagettftext($imagem, 15, 0, 1055, 1260, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}elseif($sexoDep5 == 'F'){
			imagettftext($imagem, 15, 0, 1130, 1260, $cor,"../../Site/assets/img/arial.ttf",'X');			
		}
		
		imagettftext($imagem, 12, 0, 200, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep5));
		imagettftext($imagem, 12, 0, 500, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep5));
		imagettftext($imagem, 12, 0, 780, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep5));
		imagettftext($imagem, 12, 0, 70, 1350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep5));
		imagettftext($imagem, 12, 0, 1000, 1350, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep5));

		imagettftext($imagem, 11, 0, 80, 1483, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_CORRETORA, 0, 50)));
		imagettftext($imagem, 11, 0, 80, 1530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_VENDEDOR, 0, 50)));
		imagettftext($imagem, 11, 0, 950, 1530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->CPF_VENDEDOR, 0, 50)));
		
		imagettftext($imagem, 11, 0, 840, 1635, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_VENDEDOR, 0, 50)));
		
		imagettftext($imagem, 12, 0, 142, 1635, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE. '  '.date('d/m/Y')));
		
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['pagina'] == '2'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed2.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		if($rowAssociado->CODIGO_CADASTRO_ANS == '484887200'){
			imagettftext($imagem, 15, 0, 90, 380, $cor,"../../Site/assets/img/arial.ttf",'X');	
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484889206'){
			imagettftext($imagem, 15, 0, 90, 440, $cor,"../../Site/assets/img/arial.ttf",'X');	
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484888208'){
			imagettftext($imagem, 15, 0, 90, 495, $cor,"../../Site/assets/img/arial.ttf",'X');	
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484890200'){
			imagettftext($imagem, 15, 0, 90, 550, $cor,"../../Site/assets/img/arial.ttf",'X');	
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484885203'){
			imagettftext($imagem, 15, 0, 90, 610, $cor,"../../Site/assets/img/arial.ttf",'X');	
		}
		
		if($rowAssociado->FORMA_PAGAMENTO == 'F'){
			imagettftext($imagem, 15, 0, 66, 1053, $cor,"../../Site/assets/img/arial.ttf",'X');	
		}elseif($rowAssociado->FORMA_PAGAMENTO == 'B'){
			imagettftext($imagem, 15, 0, 208, 1053, $cor,"../../Site/assets/img/arial.ttf",'X');	
		}

		imagettftext($imagem, 11, 0, 840, 1670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));		
		imagettftext($imagem, 11, 0, 135, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));		
		imagettftext($imagem, 11, 0, 200, 835, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));		
		imagettftext($imagem, 11, 0, 135, 875, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));		
		imagettftext($imagem, 11, 0, 200, 875, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
		imagettftext($imagem, 11, 0, 520, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));		
		imagettftext($imagem, 11, 0, 575, 835, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));	
		imagettftext($imagem, 11, 0, 520, 875, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));		
		imagettftext($imagem, 11, 0, 575, 875, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));	
		imagettftext($imagem, 11, 0, 935, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep4));		
		imagettftext($imagem, 11, 0, 990, 835, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));	
		imagettftext($imagem, 11, 0, 935, 875, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep5));		
		imagettftext($imagem, 11, 0, 990, 875, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep5));
		imagettftext($imagem, 11, 0, 220, 940, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
		
		imagettftext($imagem, 12, 0, 142, 1670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE. '  '.date('d/m/Y')));
		
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['pagina'] == '3'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed3.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );

		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['pagina'] == '4'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed4.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );

		imagettftext($imagem, 11, 0, 840, 1580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));		
		imagettftext($imagem, 12, 0, 711, 1395, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE. '  '.date('d/m/Y')));
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['pagina'] == '5'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed5.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		
		imagettftext($imagem, 12, 0, 180, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 180, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 12, 0, 220, 1650, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		//imagettftext($imagem, 12, 0, 840, 570, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 12, 0, 106, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 12, 0, 393, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(date('d')));
		imagettftext($imagem, 12, 0, 456, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(date('m')));
		imagettftext($imagem, 12, 0, 525, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(date('Y')));
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '6'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed6.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '7'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed7.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 128);
		
		imagettftext($imagem, 11, 0, 135, 150, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));
		
				
				
		$queryDecTit  = ' SELECT ';
		$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
		$queryDecTit .= ' FROM VND1000_ON ';
		$queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
		$queryDecTit .= ' LEFT JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
		$queryDecTit .= ' WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
		$queryDecTit .= ' AND VND1000_ON.ULTIMO_STATUS <> "AGUARDANDO_DECL_SAUDE"'; 	
		$queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
		$resDecTit = jn_query($queryDecTit); 
	
			
		while($rowDecTit = jn_fetch_object($resDecTit)){	
			$coluna = '';
			$codigoTit = $codAssociadoTmp;
			
			if($rowDecTit->TIPO_ASSOCIADO == 'T'){
				$coluna = 900;				
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
				$coluna = 950;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
				$coluna = 1000;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
				$coluna = 1055;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
				$coluna = 1100;
			}
			
			if($rowDecTit->NUMERO_PERGUNTA == '1'){
				imagettftext($imagem, 14, 0, $coluna, 345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
				imagettftext($imagem, 14, 0, $coluna, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
				imagettftext($imagem, 14, 0, $coluna, 440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
				imagettftext($imagem, 14, 0, $coluna, 500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '5'){
				imagettftext($imagem, 14, 0, $coluna, 560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
				imagettftext($imagem, 14, 0, $coluna, 620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
				imagettftext($imagem, 14, 0, $coluna, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
				imagettftext($imagem, 14, 0, $coluna, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
				imagettftext($imagem, 14, 0, $coluna, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
				imagettftext($imagem, 14, 0, $coluna, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '11'){
				imagettftext($imagem, 14, 0, $coluna, 875, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
				imagettftext($imagem, 14, 0, $coluna, 930, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
				imagettftext($imagem, 14, 0, $coluna, 980, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
				imagettftext($imagem, 14, 0, $coluna, 1020, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '15'){
				imagettftext($imagem, 14, 0, $coluna, 1080, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '16'){
				imagettftext($imagem, 14, 0, $coluna, 1120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '17'){
				imagettftext($imagem, 14, 0, $coluna, 1168, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '18'){
				imagettftext($imagem, 14, 0, $coluna, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '19'){
				imagettftext($imagem, 14, 0, $coluna, 1265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '20'){
				imagettftext($imagem, 14, 0, $coluna, 1310, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '21'){
				imagettftext($imagem, 14, 0, $coluna, 1345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '22'){
				imagettftext($imagem, 14, 0, $coluna, 1395, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '23'){
				imagettftext($imagem, 14, 0, $coluna, 1435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}
		}
		
		imagettftext($imagem, 14, 0, 220, 1575, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoTit));
		imagettftext($imagem, 14, 0, 220, 1605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaTit));
		
		imagettftext($imagem, 14, 0, 320, 1575, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep1));
		imagettftext($imagem, 14, 0, 320, 1605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep1));
		
		imagettftext($imagem, 14, 0, 480, 1575, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep2));
		imagettftext($imagem, 14, 0, 480, 1605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep2));
		
		imagettftext($imagem, 14, 0, 660, 1575, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep3));
		imagettftext($imagem, 14, 0, 660, 1605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep3));
		
		imagettftext($imagem, 14, 0, 850, 1575, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep4));
		imagettftext($imagem, 14, 0, 850, 1605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep4));
		
		imagettftext($imagem, 11, 0, 840, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 50)));
		imagettftext($imagem, 12, 0, 142, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE. '  '.date('d/m/Y')));
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '8'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed8.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		
		imagettftext($imagem, 15, 0, 52, 1153, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		imagettftext($imagem, 12, 0, 120, 1240, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 12, 0, 230, 1240, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 12, 0, 330, 1240, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
		imagettftext($imagem, 12, 0, 490, 1540, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 510, 1595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '9'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed9.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		
		imagettftext($imagem, 12, 0, 200, 410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 500, 1320, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ',' . date('d/m/Y'));
		imagettftext($imagem, 12, 0, 420, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '10'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed10.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$valorTotalAlterado = str_replace(',','   ',toMoeda($valorTotal));
		$valorTotalAlterado = str_replace('R$','',$valorTotalAlterado);
		$auxiliar = 0;
		
		if($valorTotal>9.99)
			$auxiliar = 7;
		if($valorTotal>99.99)
			$auxiliar = 17;
		if($valorTotal>999.99)
			$auxiliar = 35;
		
		
		
		if($rowAssociado->CODIGO_CADASTRO_ANS == '484887200'){
			imagettftext($imagem, 10, 0, 40, 553, $cor,"../../Site/assets/img/arial.ttf",'X');	
			
			imagettftext($imagem, 15, 0, 1110-$auxiliar, 575, $cor,"../../Site/assets/img/arial.ttf",$valorTotalAlterado);
			
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484889206'){
			imagettftext($imagem, 10, 0, 40, 624, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 15, 0, 1115-$auxiliar, 646, $cor,"../../Site/assets/img/arial.ttf",$valorTotalAlterado);			
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484888208'){
			imagettftext($imagem, 10, 0, 40, 686, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 15, 0, 1115-$auxiliar, 707, $cor,"../../Site/assets/img/arial.ttf",$valorTotalAlterado);			
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484890200'){
			imagettftext($imagem, 10, 0, 40, 753, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 15, 0, 1115-$auxiliar, 778, $cor,"../../Site/assets/img/arial.ttf",$valorTotalAlterado);	
		}elseif($rowAssociado->CODIGO_CADASTRO_ANS == '484885203'){
			imagettftext($imagem, 10, 0, 40, 820, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 15, 0, 1115-$auxiliar, 842, $cor,"../../Site/assets/img/arial.ttf",$valorTotalAlterado);	
		}
		
		
		imagettftext($imagem, 12, 0, 240, 1035, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 180, 1160, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 620, 1160, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 12, 0, 950, 1160, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 12, 0, 1030, 1160, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 12, 0, 1100, 1160, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
		
				
		imagettftext($imagem, 12, 0, 240, 1463, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 12, 0, 935, 1463, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
		imagettftext($imagem, 12, 0, 180, 1580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 12, 0, 620, 1580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 12, 0, 950, 1580, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 12, 0, 1030, 1580, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 12, 0, 1100, 1580, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '11'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed11.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '12'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta_VixMed12.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		
		imagettftext($imagem, 12, 0, 180, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 800, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1242, 1755);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1242, 1755, 1242, 1755);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

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