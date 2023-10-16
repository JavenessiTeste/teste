<?php
require('../lib/base.php');

if($_GET['modelo'] == 1){
    $codAssociadoTmp = $_GET['codAssociado'];

    $queryAssociado  = ' SELECT ';
    $queryAssociado .= ' 	VND1000_ON.DATA_ADMISSAO, VND1000_ON.NOME_ASSOCIADO, VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.NOME_MAE, VND1000_ON.NUMERO_CPF, ';
    $queryAssociado .= ' 	VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, VND1000_ON.CODIGO_CNS, VND1000_ON.SEXO, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1001_ON.DIA_VENCIMENTO, ';
    $queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
    $queryAssociado .= '    VND1001_ON.ENDERECO_EMAIL, VND1001_ON.DIA_VENCIMENTO, VND1001_ON.NUMERO_CONTRATO, PS1044.NOME_ESTADO_CIVIL, PS1045.NOME_PARENTESCO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_CADASTRO_ANS, ';
    $queryAssociado .= '    VND1000_ON.PESO, VND1000_ON.ALTURA, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1100.NOME_COMPLETO AS NOME_COMPLETO_VENDEDOR, ';
    $queryAssociado .= '    PS1030.CODIGO_PLANO ';
    $queryAssociado .= ' FROM VND1000_ON ';
    $queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
    $queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
    $queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
    $queryAssociado .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
    $queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
    $queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
    $resAssociado = jn_query($queryAssociado);
    if(!$rowAssociado = jn_fetch_object($resAssociado)){
        echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
        exit;
    }else{
        jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
    }

    $enderecoAssociado = explode(',',$rowAssociado->ENDERECO);
    $endereco = $enderecoAssociado[0];
    $numeroEndereco = explode ('-',$enderecoAssociado[1]);
    $numeroEnd = $numeroEndereco[0];
    $complemento = $numeroEndereco[1];

    $idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
    $queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE PS1032.CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);

	$valorTit = $rowValores->VALOR_PLANO;
    $valorTotalMensal = $valorTit;

    
    //Tratativas Dependentes

    //Dependente 1
    $codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';

	$queryDep1  = ' SELECT ';
    $queryDep1 .= ' VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NOME_MAE, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, ';
    $queryDep1 .= ' VND1000_ON.CODIGO_CNS, VND1000_ON.SEXO, PS1044.NOME_ESTADO_CIVIL, PS1045.NOME_PARENTESCO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_CADASTRO_ANS, ';
    $queryDep1 .= ' VND1000_ON.PESO, VND1000_ON.ALTURA, PS1030.CODIGO_PLANO ';
    $queryDep1 .= ' FROM VND1000_ON ';
    $queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep1 .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
    $queryDep1 .= ' WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
    $queryDep1 .= ' AND  VND1000_ON.TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY VND1000_ON.CODIGO_ASSOCIADO ';
    $resDep1 = jn_query($queryDep1);
	$rowDep1 = jn_fetch_object($resDep1);

    $idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
    $queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE PS1032.CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
    $valorDep1 = $rowValores->VALOR_PLANO;
    
    //Dependente 2
    $codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';

	$queryDep2  = ' SELECT ';
    $queryDep2 .= ' VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NOME_MAE, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, ';
    $queryDep2 .= ' VND1000_ON.CODIGO_CNS, VND1000_ON.SEXO, PS1044.NOME_ESTADO_CIVIL, PS1045.NOME_PARENTESCO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_CADASTRO_ANS, ';
    $queryDep2 .= ' VND1000_ON.PESO, VND1000_ON.ALTURA, PS1030.CODIGO_PLANO ';
    $queryDep2 .= ' FROM VND1000_ON ';
    $queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep2 .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
    $queryDep2 .= ' WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
    $queryDep2 .= ' AND  VND1000_ON.TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$queryDep2 .= ' ORDER BY VND1000_ON.CODIGO_ASSOCIADO ';
    $resDep2 = jn_query($queryDep2);
	$rowDep2 = jn_fetch_object($resDep2);

    $idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
    $queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE PS1032.CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
    $valorDep2 = $rowValores->VALOR_PLANO;

    //Dependente 3
    $codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';

	$queryDep3  = ' SELECT ';
    $queryDep3  .= ' VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NOME_MAE, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, ';
    $queryDep3 .= ' VND1000_ON.CODIGO_CNS, VND1000_ON.SEXO, PS1044.NOME_ESTADO_CIVIL, PS1045.NOME_PARENTESCO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_CADASTRO_ANS, ';
    $queryDep3 .= ' VND1000_ON.PESO, VND1000_ON.ALTURA, PS1030.CODIGO_PLANO ';
    $queryDep3 .= ' FROM VND1000_ON ';
    $queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep3 .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
    $queryDep3 .= ' WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
    $queryDep3 .= ' AND  VND1000_ON.TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	$queryDep3 .= ' ORDER BY VND1000_ON.CODIGO_ASSOCIADO ';
    $resDep3 = jn_query($queryDep3);
	$rowDep3 = jn_fetch_object($resDep3);
    
    $idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
    $queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE PS1032.CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
    $valorDep3 = $rowValores->VALOR_PLANO;

    $valorTotalMensal = ($valorTit + $valorDep1 + $valorDep2 + $valorDep3);

    
    if($_GET['pagina'] == '1'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve1_PF.jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );

        //Responsavel Financeiro
        imagettftext($imagem, 14, 0, 698, 117, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
        imagettftext($imagem, 14, 0, 310, 165, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
        imagettftext($imagem, 14, 0, 490, 213, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 558, 242, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
        imagettftext($imagem, 14, 0, 220, 266, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
        imagettftext($imagem, 14, 0, 486, 266, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
        imagettftext($imagem, 14, 0, 1000, 266, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 280, 298, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($endereco));
        imagettftext($imagem, 14, 0, 850, 295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroEnd));
        imagettftext($imagem, 10, 0, 1036, 295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($complemento));
        imagettftext($imagem, 14, 0, 245, 324, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
        imagettftext($imagem, 14, 0, 625, 324, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
        imagettftext($imagem, 14, 0, 900, 324, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
        imagettftext($imagem, 14, 0, 1010, 324, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
        imagettftext($imagem, 14, 0, 302, 352, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
        imagettftext($imagem, 14, 0, 578, 352, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
        imagettftext($imagem, 11, 0, 836, 352, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
        imagettftext($imagem, 14, 0, 410, 380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));

        if($rowAssociado->SEXO == 'F'){
            imagettftext($imagem, 14, 0, 715, 380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
        }else{
            imagettftext($imagem, 14, 0, 876, 380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));		
        }
            imagettftext($imagem, 14, 0, 1042, 380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));


        //Titular do contrato
        imagettftext($imagem, 14, 0, 490, 475, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 545, 505, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
        imagettftext($imagem, 14, 0, 220, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
        imagettftext($imagem, 14, 0, 385, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
        imagettftext($imagem, 14, 0, 905, 530, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 410, 557, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));

        if($rowAssociado->SEXO == 'F'){
			imagettftext($imagem, 14, 0, 774, 557, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}else{
			imagettftext($imagem, 14, 0, 896, 557, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));		
		}

        imagettftext($imagem, 14, 0, 1042, 557, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
        imagettftext($imagem, 11, 0, 359, 588, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_PLANO_FAMILIARES,0,21)));
        imagettftext($imagem, 14, 0, 677, 588, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CADASTRO_ANS));
        imagettftext($imagem, 14, 0, 1042, 588, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PARENTESCO));
        imagettftext($imagem, 14, 0, 1042, 588, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PARENTESCO));
        imagettftext($imagem, 14, 0, 359, 623, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
        

        //Dependente 1
        imagettftext($imagem, 14, 0, 490, 684, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 545, 716, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NOME_MAE));
        imagettftext($imagem, 14, 0, 220, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NUMERO_CPF));
        imagettftext($imagem, 14, 0, 555, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NUMERO_RG));
        imagettftext($imagem, 14, 0, 910, 740, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowDep1->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 410, 770, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->CODIGO_CNS));

        if($rowDep1->SEXO == 'F'){
            imagettftext($imagem, 14, 0, 741, 768, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
        }else{
            imagettftext($imagem, 14, 0, 860, 768, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));		
        }

        imagettftext($imagem, 14, 0, 1058, 768, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NOME_ESTADO_CIVIL));
        imagettftext($imagem, 11, 0, 359, 798, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowDep1->NOME_PLANO_FAMILIARES,0,19)));
        imagettftext($imagem, 14, 0, 630, 798, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->CODIGO_CADASTRO_ANS));
        imagettftext($imagem, 14, 0, 970, 798, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NOME_PARENTESCO));
        imagettftext($imagem, 14, 0, 359, 832, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));

        //Dependente 2
        imagettftext($imagem, 14, 0, 490, 898, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 545, 928, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NOME_MAE));
        imagettftext($imagem, 14, 0, 225, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NUMERO_CPF));
        imagettftext($imagem, 14, 0, 440, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NUMERO_RG));
        imagettftext($imagem, 14, 0, 920, 950, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowDep2->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 420, 980, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->CODIGO_CNS));

        if($rowDep2->SEXO == 'F'){
            imagettftext($imagem, 13, 0, 732, 977, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
        }else{
            imagettftext($imagem, 13, 0, 843, 977, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));		
        }

        imagettftext($imagem, 14, 0, 1028, 980, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NOME_ESTADO_CIVIL));
        imagettftext($imagem, 11, 0, 363, 1008, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowDep2->NOME_PLANO_FAMILIARES,0,19)));
        imagettftext($imagem, 14, 0, 685, 1008, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->CODIGO_CADASTRO_ANS));
        imagettftext($imagem, 14, 0, 1015, 1008, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NOME_PARENTESCO));
        imagettftext($imagem, 14, 0, 359, 1042, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));

        //Dependente 3
        imagettftext($imagem, 14, 0, 490, 1108, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 549, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NOME_MAE));
        imagettftext($imagem, 14, 0, 225, 1163, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NUMERO_CPF));
        imagettftext($imagem, 14, 0, 530, 1163, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NUMERO_RG));
        imagettftext($imagem, 14, 0, 830, 1163, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowDep3->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 420, 1190, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->CODIGO_CNS));

        if($rowDep3->SEXO == 'F'){
            imagettftext($imagem, 13, 0, 778, 1188, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
        }else{
            imagettftext($imagem, 13, 0, 888, 1188, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));		
        }

        imagettftext($imagem, 14, 0, 1028, 1190, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NOME_ESTADO_CIVIL));
        imagettftext($imagem, 11, 0, 363, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowDep3->NOME_PLANO_FAMILIARES,0,19)));
        imagettftext($imagem, 14, 0, 630, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->CODIGO_CADASTRO_ANS));
        imagettftext($imagem, 14, 0, 970, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NOME_PARENTESCO));
        imagettftext($imagem, 14, 0, 359, 1258, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));

        //Rodapé 
        imagettftext($imagem, 14, 0, 990, 1319, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->DIA_VENCIMENTO));
        imagettftext($imagem, 11, 0, 320, 1403, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO)); // Assinatura_Titular
        imagettftext($imagem, 11, 0, 745, 1403, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_COMPLETO_VENDEDOR)); //Assinatura_Vendedor
        imagettftext($imagem, 14, 0, 618, 1319, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotalMensal));


        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );

            

    }

    if($_GET['pagina'] == '2'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve2_PF.jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );

        imagettftext($imagem, 14, 0, 687, 134, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
        imagettftext($imagem, 10, 0, 265, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));;
        imagettftext($imagem, 10, 0, 398, 1240, $cor,"../../Site/assets/img/arial.ttf",date('d'));
        imagettftext($imagem, 10, 0, 454, 1240, $cor,"../../Site/assets/img/arial.ttf",date('m'));
        imagettftext($imagem, 10, 0, 503, 1240, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
        imagettftext($imagem, 10, 0, 703, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	   
        
        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );
    }

    if($_GET['pagina'] == '3'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve3_PF.jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );

        imagettftext($imagem, 14, 0, 687, 134, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
        //Titular
        imagettftext($imagem, 11, 0, 183, 1181, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
        imagettftext($imagem, 11, 0, 180, 1206, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
        imagettftext($imagem, 11, 0, 270, 1224, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));;
        imagettftext($imagem, 11, 0, 450, 1222, $cor,"../../Site/assets/img/arial.ttf",date('d'));
        imagettftext($imagem, 11, 0, 486, 1222, $cor,"../../Site/assets/img/arial.ttf",date('m'));
        imagettftext($imagem, 11, 0, 515, 1222, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
        imagettftext($imagem, 11, 0, 210, 1287, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));;
        
        //Vendedor
        imagettftext($imagem, 11, 0, 270, 1386, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
        imagettftext($imagem, 11, 0, 450, 1387, $cor,"../../Site/assets/img/arial.ttf",date('d'));
        imagettftext($imagem, 11, 0, 486, 1387, $cor,"../../Site/assets/img/arial.ttf",date('m'));
        imagettftext($imagem, 11, 0, 515, 1387, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
        imagettftext($imagem, 11, 0, 210, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_COMPLETO_VENDEDOR));;
        imagettftext($imagem, 11, 0, 180, 1355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
        

        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );
    }

    if($_GET['pagina'] == '4'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve4.jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );

        imagettftext($imagem, 14, 0, 971, 134, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));

        //Titular
        imagettftext($imagem, 14, 0, 345, 295, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 350, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
        imagettftext($imagem, 14, 0, 348, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->PESO));
        imagettftext($imagem, 14, 0, 342, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ALTURA));
        imagettftext($imagem, 14, 0, 482, 1525, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
        
        //Dependente 1
        imagettftext($imagem, 14, 0, 485, 295, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowDep1->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 490, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->SEXO));
        imagettftext($imagem, 14, 0, 488, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->PESO));
        imagettftext($imagem, 14, 0, 482, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->ALTURA));

        //Dependente 2
        imagettftext($imagem, 14, 0, 625, 295, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowDep2->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 630, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->SEXO));
        imagettftext($imagem, 14, 0, 628, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->PESO));
        imagettftext($imagem, 14, 0, 622, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->ALTURA));

        //Dependente 3
        imagettftext($imagem, 14, 0, 765, 295, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowDep3->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 770, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->SEXO));
        imagettftext($imagem, 14, 0, 768, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->PESO));
        imagettftext($imagem, 14, 0, 762, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->ALTURA));

        //Dependente 4
        imagettftext($imagem, 14, 0, 930, 295, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowDep4->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 933, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep4->SEXO));
        imagettftext($imagem, 14, 0, 931, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep4->PESO));
        imagettftext($imagem, 14, 0, 925, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep4->ALTURA));

        //Dependente 5
        imagettftext($imagem, 14, 0, 1065, 295, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowDep5->DATA_NASCIMENTO));
        imagettftext($imagem, 14, 0, 1068, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep5->SEXO));
        imagettftext($imagem, 14, 0, 1066, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep5->PESO));
        imagettftext($imagem, 14, 0, 1063, 400, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep5->ALTURA));
        
        //Questionário
        $queryDecTit  = ' SELECT ';
        $queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
        $queryDecTit .= ' FROM VND1000_ON ';
        $queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
        $queryDecTit .= ' LEFT JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
        $queryDecTit .= ' WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
        $queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
        $resDecTit = jn_query($queryDecTit); 
        while ($rowDecTit = jn_fetch_object($resDecTit)){	
            $coluna = '';
            $codigoTit = $codAssociadoTmp;
        
            //pr($codigoDep3, true);
        
            if($rowDecTit->TIPO_ASSOCIADO == 'T'){
				$coluna = 862;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
				$coluna = 916;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
				$coluna = 967;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
				$coluna = 1019;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
				$coluna = 1040;
			}
             elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep5){
				$coluna = 1125;
			}
           

            if($rowDecTit->NUMERO_PERGUNTA == '1'){
				imagettftext($imagem, 14, 0, $coluna, 539, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
				imagettftext($imagem, 14, 0, $coluna, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
				imagettftext($imagem, 14, 0, $coluna, 620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
				imagettftext($imagem, 14, 0, $coluna, 660, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '5'){
				imagettftext($imagem, 14, 0, $coluna, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
				imagettftext($imagem, 14, 0, $coluna, 743, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
				imagettftext($imagem, 14, 0, $coluna, 783, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
				imagettftext($imagem, 14, 0, $coluna, 823, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
				imagettftext($imagem, 14, 0, $coluna, 865, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
				imagettftext($imagem, 14, 0, $coluna, 905, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '11'){
				imagettftext($imagem, 14, 0, $coluna, 947, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
				imagettftext($imagem, 14, 0, $coluna, 989, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
				imagettftext($imagem, 14, 0, $coluna, 1029, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
				imagettftext($imagem, 14, 0, $coluna, 1069, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '15'){
				imagettftext($imagem, 14, 0, $coluna, 1111, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '16'){
				imagettftext($imagem, 14, 0, $coluna, 1153, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '17'){
				imagettftext($imagem, 14, 0, $coluna, 1193, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '18'){
				imagettftext($imagem, 14, 0, $coluna, 1234, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '19'){
				imagettftext($imagem, 14, 0, $coluna, 1276, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '20'){
				imagettftext($imagem, 14, 0, $coluna, 1316, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '21'){
				imagettftext($imagem, 14, 0, $coluna, 1357, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '22'){
				imagettftext($imagem, 14, 0, $coluna, 1397, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '23'){
				imagettftext($imagem, 14, 0, $coluna, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '24'){
				imagettftext($imagem, 14, 0, $coluna, 1482, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }
            
        
        }
            

        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );
    }

    if($_GET['pagina'] == '5'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve5.jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );

        imagettftext($imagem, 14, 0, 971, 134, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
        //imagettftext($imagem, 14, 0, 159, 1380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
        //imagettftext($imagem, 14, 0, 159, 1408, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
        imagettftext($imagem, 14, 0, 159, 1446, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
        imagettftext($imagem, 14, 0, 482, 1525, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

         
        //Questionário
        $queryDecTit  = ' SELECT ';
        $queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
        $queryDecTit .= ' FROM VND1000_ON ';
        $queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
        $queryDecTit .= ' LEFT JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
        $queryDecTit .= ' WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp); 	
        $queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
        $resDecTit = jn_query($queryDecTit); 
        while ($rowDecTit = jn_fetch_object($resDecTit)){	
            $coluna = '';
            $codigoTit = $codAssociadoTmp;
        
            //pr($rowDecTit, true);
        
            if($rowDecTit->TIPO_ASSOCIADO == 'T'){
				$coluna = 862;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
				$coluna = 920;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
				$coluna = 967;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
				$coluna = 1019;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
				$coluna = 1040;
			}
             elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep5){
				$coluna = 1125;
			}
           

            if($rowDecTit->NUMERO_PERGUNTA == '25'){
				imagettftext($imagem, 14, 0, $coluna, 327, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '26'){
				imagettftext($imagem, 14, 0, $coluna, 370, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '27'){
				imagettftext($imagem, 14, 0, $coluna, 410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '28'){
				imagettftext($imagem, 14, 0, $coluna, 452, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '29'){
				imagettftext($imagem, 14, 0, $coluna, 492, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '30'){
				imagettftext($imagem, 14, 0, $coluna, 537, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '31'){
				imagettftext($imagem, 14, 0, $coluna, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '32'){
				imagettftext($imagem, 14, 0, $coluna, 625, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }elseif($rowDecTit->NUMERO_PERGUNTA == '33'){
				imagettftext($imagem, 14, 0, $coluna, 666, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));
            }
        }


        $queryDecRespostas  = ' SELECT '; 
        $queryDecRespostas .= ' VND1005_ON.NUMERO_PERGUNTA, VND1005_ON.RESPOSTA_DIGITADA, VND1000_ON.NOME_ASSOCIADO, VND1005_ON.DESCRICAO_OBSERVACAO ';
        $queryDecRespostas .= ' FROM VND1005_ON ';
        $queryDecRespostas .= ' INNER JOIN VND1000_ON ON (VND1005_ON.CODIGO_ASSOCIADO = VND1000_ON.CODIGO_ASSOCIADO) ';
        $queryDecRespostas .= ' WHERE VND1005_ON.RESPOSTA_DIGITADA = "S" AND VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
        $queryDecRespostas .= ' ORDER BY VND1005_ON.NUMERO_PERGUNTA ';
        $resDecRespostas = jn_query($queryDecRespostas);
        

        $gridDecRespostas = Array();
		$i = 0;
		
		while($rowDecRespostas = jn_fetch_object($resDecRespostas)){		
			$gridDecRespostas[$i]['NUMERO_PERGUNTA'] = $rowDecRespostas->NUMERO_PERGUNTA;
			$gridDecRespostas[$i]['NOME_ASSOCIADO'] = explode(' ', $rowDecRespostas->NOME_ASSOCIADO);
			$gridDecRespostas[$i]['DESCRICAO_OBSERVACAO'] = jn_utf8_encode($rowDecRespostas->DESCRICAO_OBSERVACAO);
			$i++;
		}
        //pr($gridDecRespostas, true);
        imagettftext($imagem, 14, 0, 150, 825, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[0]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 825, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[0]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 825, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[0]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 855, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[1]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 855, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[1]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 855, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[1]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 885, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[2]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 885, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[2]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 885, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[2]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 920, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[3]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 920, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[3]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 920, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[3]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 955, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[4]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 955, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[4]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 955, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[4]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 990, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[5]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 990, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[5]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 990, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[5]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1021, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[6]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1021, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[6]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1021, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[6]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1055, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[7]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1055, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[7]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1055, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[7]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1088, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[8]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1088, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[8]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1088, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[8]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1120, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[9]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1120, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[9]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1120, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[9]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1154, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[10]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1154, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[10]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1154, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[10]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1187, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[11]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1187, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[11]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1187, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[11]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1219, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[12]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1219, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[12]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1219, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[12]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1252, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[13]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1252, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[13]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1252, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[13]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1285, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[14]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1285, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[14]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1285, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[14]['DESCRICAO_OBSERVACAO']);
        imagettftext($imagem, 14, 0, 150, 1316, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[15]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 12, 0, 200, 1316, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[15]['NOME_ASSOCIADO'][0]);
		imagettftext($imagem, 14, 0, 500, 1316, $cor,"../../Site/assets/img/arial.ttf",$gridDecRespostas[15]['DESCRICAO_OBSERVACAO']);
        


        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );
    }

    if($_GET['pagina'] == '6'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve6.jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );

        imagettftext($imagem, 14, 0, 971, 134, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));

        //Titular
        imagettftext($imagem, 11, 0, 188, 1141, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
        imagettftext($imagem, 11, 0, 180, 1312, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
        imagettftext($imagem, 11, 0, 145, 1364, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE . ','));;
        imagettftext($imagem, 11, 0, 270, 1364, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
        imagettftext($imagem, 11, 0, 145, 1405, $cor,"../../Site/assets/img/arial.ttf",($rowAssociado->NOME_ASSOCIADO));
        
         //Vendedor
         imagettftext($imagem, 11, 0, 720, 1141, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_COMPLETO_VENDEDOR));
         imagettftext($imagem, 11, 0, 712, 1312, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
         imagettftext($imagem, 11, 0, 682, 1364, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE . ','));
         imagettftext($imagem, 11, 0, 820, 1364, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
         imagettftext($imagem, 11, 0, 682, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_COMPLETO_VENDEDOR));
        
        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );
    }

    if($_GET['pagina'] == '7'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve7.jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        imagettftext($imagem, 14, 0, 741, 164, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
        imagettftext($imagem, 14, 0, 140, 1525, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 600, 1490, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
        imagettftext($imagem, 12, 0, 850, 1490, $cor,"../../Site/assets/img/arial.ttf",date('d'));
        imagettftext($imagem, 14, 0, 930, 1491, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));
        imagettftext($imagem, 12, 0, 1107, 1491, $cor,"../../Site/assets/img/arial.ttf",date('y'));
        

        //Titular 
        imagettftext($imagem, 14, 0, 200, 260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 700, 260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
        //Dependente1
        imagettftext($imagem, 14, 0, 200, 289, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 700, 289, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep1->NUMERO_CPF));
        //Dependente2
        imagettftext($imagem, 14, 0, 200, 317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 700, 317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep2->NUMERO_CPF));
        //Dependente3
        imagettftext($imagem, 14, 0, 200, 344, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NOME_ASSOCIADO));
        imagettftext($imagem, 14, 0, 700, 344, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDep3->NUMERO_CPF));
        
        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );
    }  

    if($_GET['pagina'] >= '8'){
        $imagem = imagecreatefromjpeg("../../Site/assets/img/Proposta_Vileve" . $_GET['pagina'] . ".jpg");	
        $cor = imagecolorallocate($imagem, 0, 0, 240 );


        
        $image_p = imagecreatetruecolor(1275, 1650);
        imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1275, 1650, 1275, 1650);
        header( "Content-type: image/jpeg" );
        return imagejpeg( $image_p, NULL, 80 );
    }  
    
    
}

    function calcularIdade($date){	
        if(!$date){
            return null;
        }
        
        // separando yyyy, mm, ddd
        list($ano, $mes, $dia) = explode('-', $date);
        $dia = substr($dia, 0, 2);
        
        // data atual
        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        // Descobre a unix timestamp da data de nascimento do fulano
        $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
        
        // cálculo
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
        return $idade;

    }
?>