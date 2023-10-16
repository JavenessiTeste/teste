<?php
require_once('../lib/base.php');

if(isset($_GET['tp'])){
	
	if($_GET['tp'] == 'Recibo'){		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/recibo_vidamax.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 10, 0, 618, 190, $cor,"../../Site/assets/img/arial.ttf",$_GET['valorAdesao']);
		imagettftext($imagem, 10, 0, 250, 280, $cor,"../../Site/assets/img/arial.ttf",$_GET['nomeVendedor']);
		$image_p = imagecreatetruecolor(1250, 600);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 2325, 850, 1600, 600);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['tp'] == 'TermoContrato'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 10, 0, 450, 56, $cor,"../../Site/assets/img/arial.ttf",$_GET['protocolo']);	
		$image_p = imagecreatetruecolor(840, 1145);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 840, 1145, 840, 1145);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['tp'] == 'TaxaAngariacao'){
		
		if($_GET['imagem'] == 1){
			$img = imagecreatefromjpeg('../../Site/assets/img/taxa_angariacao_vidamax.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['imagem'] == 2){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/recibo_vidamax.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 618, 190, $cor,"../../Site/assets/img/arial.ttf",$_GET['valorAdesao']);
			imagettftext($imagem, 10, 0, 250, 280, $cor,"../../Site/assets/img/arial.ttf",$_GET['nomeVendedor']);
			$image_p = imagecreatetruecolor(1250, 600);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 2325, 850, 1590, 600);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
	}

	if($_GET['tp'] == 'declaracaoSaude'){
		
		$queryObservacoes  = ' 	SELECT  ';	
		$queryObservacoes .= ' 		NUMERO_PERGUNTA, CODIGO_ASSOCIADO, DESCRICAO_OBSERVACAO, ';
		$queryObservacoes .= ' 		CASE ';
		$queryObservacoes .= ' 			WHEN VND1005_ON.CODIGO_ASSOCIADO = ' . aspas($_GET['codAssociado']);
		$queryObservacoes .= ' 				THEN "TIT."  ';
		$queryObservacoes .= ' 			ELSE "DEP."  ';
		$queryObservacoes .= ' 		END  AS TIPO_ASSOCIADO';
		$queryObservacoes .= ' 	FROM VND1005_ON  ';
		$queryObservacoes .= ' 	WHERE CODIGO_ASSOCIADO IN ( ';
		$queryObservacoes .= ' 		SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . $_GET['codAssociado'];	
		$queryObservacoes .= ' 	) ';	
		
		$resObservacoes = jn_query($queryObservacoes);
		
		$GridObservacoes = Array();
		$i = 0;
		
		while($rowObservacoes = jn_fetch_object($resObservacoes)){		
			$GridObservacoes[$i]['NUMERO_PERGUNTA'] = $rowObservacoes->NUMERO_PERGUNTA;
			$GridObservacoes[$i]['CODIGO_ASSOCIADO'] = $rowObservacoes->CODIGO_ASSOCIADO;
			$GridObservacoes[$i]['DESCRICAO_OBSERVACAO'] = jn_utf8_encode($rowObservacoes->DESCRICAO_OBSERVACAO);
			$GridObservacoes[$i]['TIPO_ASSOCIADO'] = jn_utf8_encode($rowObservacoes->TIPO_ASSOCIADO);
			$i++;
		}
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/grid_declaracao.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 10, 0, 55, 60, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 10, 0, 110, 60, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 10, 0, 200, 60, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 10, 0, 55, 80,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 10, 0, 110, 80, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 10, 0, 200, 80, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 10, 0, 110, 98, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 10, 0, 200, 98, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 10, 0, 55,  98,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 10, 0, 110, 115, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 10, 0, 200, 115, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 10, 0, 55,  115,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 10, 0, 110, 133, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 10, 0, 200, 133, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 10, 0, 55,  133,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 10, 0, 110, 151, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 10, 0, 200, 151, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 10, 0, 55,  151,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['NUMERO_PERGUNTA']);
		$image_p = imagecreatetruecolor(1250, 335);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 2325, 850, 1600, 600);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['tp'] == 'TermoAut'){
		$codAssociado = '';
		if($_GET['codAssociado']){
			$codAssociado = $_GET['codAssociado'];
		}else{
			$codAssociado = $_SESSION['codigoIdentificacao'];
		}
		
		$queryDadosCabecalho  = ' SELECT ';
		$queryDadosCabecalho .= '	PS1030.CODIGO_GRUPO_CONTRATO ';
		$queryDadosCabecalho .= ' FROM VND1000_ON ';
		$queryDadosCabecalho .= ' INNER JOIN PS1030 ON VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO ';	
		$queryDadosCabecalho .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociado); 		
		$resDadosCabecalho = jn_query($queryDadosCabecalho); 
		$rowDadosCabecalho = jn_fetch_object($resDadosCabecalho);
		//pr($rowDadosCabecalho,true);
		
		$queryProtocolo = 'SELECT PROTOCOLO_GERAL_PS6450 FROM VND1001_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociado);
		$resProtocolo = jn_query($queryProtocolo);
		$rowProtocolo = jn_fetch_object($resProtocolo);
		
		if($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 14){		
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_SINPRO.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 510, 35, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 13){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_OAB_CAMPINAS.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 450, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 16){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_OAB_SOROCABA.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 450, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 4){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_SINPRO_CAMPINAS.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 450, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 5){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_USPESP_CAMPINAS.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 510, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 10){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_APUEL.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 450, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 30){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_OAB_LIMEIRA.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 510, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 18){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_OAB_PIRACICABA.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 510, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}elseif($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO == 19){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_OAB_SAO_PEDRO.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 510, 46, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
		}else{
			$imagem = imagecreatefromjpeg("../../Site/assets/img/termos_contrato_vidamax_USPESP.jpg");
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 10, 0, 450, 56, $cor,"../../Site/assets/img/arial.ttf",$rowProtocolo->PROTOCOLO_GERAL_PS6450);
			
		}
		
		$image_p = imagecreatetruecolor(838, 939);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 838, 939, 838, 939);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

}

if($_GET['modelo'] == 2){
	if(($_GET['portabilidade'] == 'N') or ($_GET['portabilidade'] == '')){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.NUMERO_PIS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, PS1044.NOME_ESTADO_CIVIL,  ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) '; 
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}else{
			//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
		}
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
			
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;
		if ($rowAssociado->CODIGO_PLANO == '29') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
		}
		if ($rowAssociado->CODIGO_PLANO == '30') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
		}

		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);

		$valorTit = $rowValores->VALOR_PLANO;
		
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, PS1045.CODIGO_PARENTESCO ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;
			if ($rowDep1->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep1->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$nomeParentescoDep1 = $rowDep1->NOME_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
			$nomePaiDep1 = $rowDep1->NOME_PAI;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;	
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
		$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, PS1045.CODIGO_PARENTESCO ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;
			if ($rowDep2->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep2->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$nomeParentescoDep2 = $rowDep2->NOME_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
			$nomePaiDep2 = $rowDep2->NOME_PAI;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;	
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, PS1045.CODIGO_PARENTESCO ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;	
			if ($rowDep3->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep3->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
			$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$nomeParentescoDep3 = $rowDep3->NOME_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
			$nomePaiDep3 = $rowDep3->NOME_PAI;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;		
		}

		//Dependente 4
		$codigoDep4 = explode('.',$codAssociadoTmp);
		$codigoDep4 = $codigoDep4[0] . '.4';

		$queryDep4  = ' SELECT ';
		$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep4 .= ' 	PESO, ALTURA, PS1045.CODIGO_PARENTESCO ';
		$queryDep4 .= ' FROM VND1000_ON ';
		$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
		$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep4 = jn_query($queryDep4);
		if($rowDep4 = jn_fetch_object($resDep4)){
			
			$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
			
			$diaNascDep4 = '';
			$mesNascDep4 = '';
			$anoNascDep4 = '';
			
			$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
			$diaNascDep4 = explode(' ', $diaNascDep4);
			$diaNascDep4 = $diaNascDep4[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;
			if ($rowDep4->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep4->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep4 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep4);
			
			$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
			$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
			$numeroRGDep4 = $rowDep4->NUMERO_RG;
			$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$sexoDep4 = $rowDep4->SEXO;
			$nomeMaeDep4 = $rowDep4->NOME_MAE;
			$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
			$parentescoDep4 = $rowDep4->CODIGO_PARENTESCO;
			$nomeParentescoDep4 = $rowDep4->NOME_PARENTESCO;
			$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
			$pesoDep4 = $rowDep4->PESO;
			$alturaDep4= $rowDep4->ALTURA;
		}

		//Dependente 5
		$codigoDep5 = explode('.',$codAssociadoTmp);
		$codigoDep5 = $codigoDep5[0] . '.5';

		$queryDep5  = ' SELECT ';
		$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep5 .= ' 	PESO, ALTURA, PS1045.CODIGO_PARENTESCO ';
		$queryDep5 .= ' FROM VND1000_ON ';
		$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
		$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep5 = jn_query($queryDep5);
		if($rowDep5 = jn_fetch_object($resDep5)){
			
			$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
			
			$diaNascDep5 = '';
			$mesNascDep5 = '';
			$anoNascDep5 = '';
			
			$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
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
			$nomeParentescoDep5 = $rowDep5->NOME_PARENTESCO;
			$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
			$pesoDep5 = $rowDep5->PESO;
			$alturaDep5= $rowDep5->ALTURA;
		}	
		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 631, 327,  $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
	
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '2'){
			$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
			$admissao = explode('/',$data);
			$diaAdmissao = $admissao[0];
			$mesAdmissao = $admissao[1];
			$anoAdmissao = $admissao[2];

			$linhaPlano = 0;
		
			$queryPlano = 'SELECT * FROM PS1030 WHERE CODIGO_PLANO = '. aspas($rowAssociado->CODIGO_PLANO);
			$resPlano = jn_query($queryPlano);
		    $rowPlano = jn_fetch_object($resPlano);

			if($rowAssociado->CODIGO_PLANO == 29){
				$linhaPlano = 347;
			}elseif($rowAssociado->CODIGO_PLANO == 30){
				$linhaPlano = 380;
			}elseif($rowAssociado->CODIGO_PLANO == 105){
				$linhaPlano = 410;
			}elseif($rowAssociado->CODIGO_PLANO == 106){
				$linhaPlano = 440;
			}else{
				$linhaPlano = 0;
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
			imagettftext($imagem, 14, 0, 160, 290, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));	
			imagettftext($imagem, 14, 0, 390, 480, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));	
			imagettftext($imagem, 14, 0, 315, $linhaPlano, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X')); 	
			imagettftext($imagem, 10, 0, 137, 537, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 177, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 310, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 470, 577, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 695, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 810, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 880, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 10, 0, 960, 577, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 1120, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
			imagettftext($imagem, 10, 0, 160, 627, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 160, 667, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PAI));
			imagettftext($imagem, 10, 0, 160, 717, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 10, 0, 160, 757, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 160, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 680, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 870, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 1110, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 670, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			imagettftext($imagem, 10, 0, 160, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 390, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			
			
			//Dep1
			imagettftext($imagem, 10, 0, 180, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 970, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep1));
			imagettftext($imagem, 10, 0, 180, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 350, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
			imagettftext($imagem, 10, 0, 505, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep1));
			imagettftext($imagem, 10, 0, 660, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
			imagettftext($imagem, 8, 0, 720, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep1));
			imagettftext($imagem, 10, 0, 820, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
			imagettftext($imagem, 10, 0, 885, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 10, 0, 970, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 1120, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
			imagettftext($imagem, 10, 0, 180, 1000, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));	
			imagettftext($imagem, 10, 0, 180, 1045, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep1));	
			imagettftext($imagem, 10, 0, 200, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));		
			imagettftext($imagem, 10, 0, 780, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));	
			
			
			
			//Dep2
			imagettftext($imagem, 10, 0, 180, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 970, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep2));		
			imagettftext($imagem, 10, 0, 180, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));		
			imagettftext($imagem, 10, 0, 330, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));		
			imagettftext($imagem, 10, 0, 490, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep2));			
			imagettftext($imagem, 10, 0, 650, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));
			imagettftext($imagem, 8, 0, 720, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep2));		
			imagettftext($imagem, 10, 0, 820, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));		
			imagettftext($imagem, 10, 0, 885, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 10, 0, 970, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 1120, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
			imagettftext($imagem, 10, 0, 180, 1227, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));		
			imagettftext($imagem, 10, 0, 180, 1317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));	
			imagettftext($imagem, 10, 0, 780, 1317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));			
			imagettftext($imagem, 10, 0, 180, 1272, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep2));		
			
			
			//Dep3
			imagettftext($imagem, 10, 0, 180, 1360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 970, 1360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep3));		
			imagettftext($imagem, 10, 0, 180, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));	
			imagettftext($imagem, 10, 0, 360, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
			imagettftext($imagem, 10, 0, 505, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep3));
			imagettftext($imagem, 10, 0, 660, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
			imagettftext($imagem, 8, 0, 720, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep3));
			imagettftext($imagem, 10, 0, 820, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
			imagettftext($imagem, 10, 0, 885, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 10, 0, 970, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 1120, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
			imagettftext($imagem, 10, 0, 180, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));		
			imagettftext($imagem, 10, 0, 180, 1545, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
			imagettftext($imagem, 10, 0, 780, 1545, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));	
			imagettftext($imagem, 10, 0, 180, 1500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep3));			
			
			imagettftext($imagem, 10, 0, 122, 1615, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 10, 0, 320, 1615, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		

		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 240, 1489, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,' . date('d/m/Y'));
			imagettftext($imagem, 14, 0, 420, 1542, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '7'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 130, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 14, 0, 983, 1334, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->VALOR_TAXA_ADESAO));
			imagettftext($imagem, 12, 0, 130, 1378, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(valorPorExtenso(str_replace(',','.',$rowAssociado->VALOR_TAXA_ADESAO))));	
			imagettftext($imagem, 10, 0, 380, 1490, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 416, 1555, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 370, 1610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '8'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2_Vidamax8.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);		
		}
		
		if($_GET['pagina'] == '9'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2_Vidamax9.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '10'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2_Vidamax10.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '11'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2_Vidamax11.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}

	}elseif($_GET['portabilidade'] == 'S'){

		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}else{
			//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
		}
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;
		if ($rowAssociado->CODIGO_PLANO == '29') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
		}
		if ($rowAssociado->CODIGO_PLANO == '30') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
		}			
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;
			if ($rowDep1->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep1->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
			$parentescoDep1 = $rowDep1->NOME_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
			$nomePaiDep1 = $rowDep1->NOME_PAI;	
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
		$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;	
			if ($rowDep2->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep2->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->NOME_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
			$nomePaiDep2 = $rowDep2->NOME_PAI;	
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;	
			if ($rowDep3->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep3->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
			$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->NOME_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
			$nomePaiDep3 = $rowDep3->NOME_PAI;	
		}

		//Dependente 4
		$codigoDep4 = explode('.',$codAssociadoTmp);
		$codigoDep4 = $codigoDep4[0] . '.4';

		$queryDep4  = ' SELECT ';
		$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep4 .= ' 	PESO, ALTURA ';
		$queryDep4 .= ' FROM VND1000_ON ';
		$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
		$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep4 = jn_query($queryDep4);
		if($rowDep4 = jn_fetch_object($resDep4)){
			$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
			
			$diaNascDep4 = '';
			$mesNascDep4 = '';
			$anoNascDep4 = '';
			
			$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
			$diaNascDep4 = explode(' ', $diaNascDep4);
			$diaNascDep4 = $diaNascDep4[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;	
			if ($rowDep4->CODIGO_PLANO == '29') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('29');
			}
			if ($rowDep4->CODIGO_PLANO == '30') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('30');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep4 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep4);
			
			$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
			$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
			$numeroRGDep4 = $rowDep4->NUMERO_RG;
			$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$sexoDep4 = $rowDep4->SEXO;
			$nomeMaeDep4 = $rowDep4->NOME_MAE;
			$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
			$parentescoDep4 = $rowDep4->NOME_PARENTESCO;
			$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
			$pesoDep4 = $rowDep4->PESO;
			$alturaDep4= $rowDep4->ALTURA;
		}

		//Dependente 5
		$codigoDep5 = explode('.',$codAssociadoTmp);
		$codigoDep5 = $codigoDep5[0] . '.5';

		$queryDep5  = ' SELECT ';
		$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep5 .= ' 	PESO, ALTURA ';
		$queryDep5 .= ' FROM VND1000_ON ';
		$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
		$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep5 = jn_query($queryDep5);
		if($rowDep5 = jn_fetch_object($resDep5)){
			$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
			
			$diaNascDep5 = '';
			$mesNascDep5 = '';
			$anoNascDep5 = '';
			
			$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
			$diaNascDep5 = explode(' ', $diaNascDep5);
			$diaNascDep5 = $diaNascDep5[0];
			
			$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
			$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
			$numeroRGDep5 = $rowDep5->NUMERO_RG;
			$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$sexoDep5 = $rowDep5->SEXO;
			$nomeMaeDep5 = $rowDep5->NOME_MAE;
			$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
			$parentescoDep5 = $rowDep5->NOME_PARENTESCO;
			$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
			$pesoDep5 = $rowDep5->PESO;
			$alturaDep5= $rowDep5->ALTURA;
		}	
		

		if($_GET['pagina'] == '1'){
			$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
			$admissao = explode('/',$data);
			$diaAdmissao = $admissao[0];
			$mesAdmissao = $admissao[1];
			$anoAdmissao = $admissao[2];

			$colunaCoparticipacao = 0;
			$colunaAcomodacao = 0;

			$queryCopart = 'SELECT * FROM PS1030 WHERE CODIGO_PLANO = '. aspas($rowAssociado->CODIGO_PLANO);
			$resCopart = jn_query($queryCopart);
		    $rowCopart = jn_fetch_object($resCopart);

			if($rowCopart->FLAG_COPARTICIPACAO == 'N'){
				$colunaCoparticipacao = 445;
			}else{
				$colunaCoparticipacao = 797;
			}

			if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){//Apto
				$colunaAcomodacao = $colunaCoparticipacao+109;					 
			}else{
				$colunaAcomodacao = $colunaCoparticipacao;
			}

			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2port_Vidamax1.jpg");	

			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 935, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
			imagettftext($imagem, 14, 0, 170, 285, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 14, 0, 390, 380, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 344, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));		
			imagettftext($imagem, 10, 0, 177, 431, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 177, 477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 310, 477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 470, 477, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 695, 477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 810, 477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 880, 477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));

			imagettftext($imagem, 10, 0, 960, 477, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 1120, 477, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
			imagettftext($imagem, 10, 0, 160, 523, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 160, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PAI));
			imagettftext($imagem, 10, 0, 160, 619, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 10, 0, 160, 657, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 160, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 680, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 870, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 1120, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 670, 750, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			imagettftext($imagem, 10, 0, 160, 750, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 390, 750, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			
			
			//Dep1
			imagettftext($imagem, 10, 0, 180, 805, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 180, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 350, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
			imagettftext($imagem, 10, 0, 505, 850, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep1));
			imagettftext($imagem, 10, 0, 660, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
			imagettftext($imagem, 10, 0, 820, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
			imagettftext($imagem, 10, 0, 885, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 10, 0, 970, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 1120, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
			imagettftext($imagem, 10, 0, 200, 897, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));		
			imagettftext($imagem, 10, 0, 200, 990, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));		
			imagettftext($imagem, 10, 0, 780, 990, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));		
			imagettftext($imagem, 10, 0, 200, 940, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep1));
			
			
			//Dep2
			imagettftext($imagem, 10, 0, 180, 1031, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));		
			imagettftext($imagem, 10, 0, 180, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));		
			imagettftext($imagem, 10, 0, 360, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));		
			imagettftext($imagem, 10, 0, 505, 1077, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep2));		
			imagettftext($imagem, 10, 0, 660, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));		
			imagettftext($imagem, 10, 0, 820, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));		
			imagettftext($imagem, 10, 0, 885, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 10, 0, 970, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 1120, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
			imagettftext($imagem, 10, 0, 180, 1128, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));		
			imagettftext($imagem, 10, 0, 180, 1213, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));		
			imagettftext($imagem, 10, 0, 780, 1213, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));		
			imagettftext($imagem, 10, 0, 180, 1165, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep2));		
			
			
			//Dep3
			imagettftext($imagem, 10, 0, 180, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));		
			imagettftext($imagem, 10, 0, 180, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));		
			imagettftext($imagem, 10, 0, 360, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
			imagettftext($imagem, 10, 0, 505, 1305, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep3));
			imagettftext($imagem, 10, 0, 660, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
			imagettftext($imagem, 10, 0, 820, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
			imagettftext($imagem, 10, 0, 885, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 10, 0, 970, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 1120, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
			imagettftext($imagem, 10, 0, 180, 1350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));		
			imagettftext($imagem, 10, 0, 180, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
			imagettftext($imagem, 10, 0, 780, 1440, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));	
			imagettftext($imagem, 10, 0, 180, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep3));		
			
			imagettftext($imagem, 10, 0, 405, 1546, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 950, 1546, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
			imagettftext($imagem, 10, 0, 180, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 122, 1660, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 10, 0, 320, 1660, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '2'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2port_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 14, 0, 631, 327,  $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 14, 0, 225, 1415, $cor,"../../Site/assets/img/arial.ttf",date('d'));
			imagettftext($imagem, 14, 0, 385, 1415, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));
			imagettftext($imagem, 15, 0, 95, 1589, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));	
			
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2port_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2port_Vidamax4.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}

		if($_GET['pagina'] == '5'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2port_Vidamax5.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		
		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta2port_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 935, 110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 240, 1320, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,' . date('d/m/Y'));
			imagettftext($imagem, 10, 0, 420, 1375, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 110, 1430, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '7'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2port_Vidamax7.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}

		if($_GET['pagina'] == '8'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2port_Vidamax8.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}

		if($_GET['pagina'] == '9'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2port_Vidamax9.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}	

		if($_GET['pagina'] == '10'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta2port_Vidamax10.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
	}
}

if($_GET['modelo'] == 5){
	$codAssociadoTmp = $_GET['codAssociado'];
	$percentual = 0;
	$valorTotal = 0;
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, VND1000_ON.CODIGO_PARENTESCO,  VND1001_ON.CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, FLAG_DEBITO_AUTOMATICO, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, VND1001_ON.CODIGO_BANCO, VND1001_ON.NOME_BANCO, VND1001_ON.NUMERO_AGENCIA, VND1001_ON.NUMERO_CONTA, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, ';
	$queryAssociado .= ' 	ESP0002.DESCRICAO_GRUPO_CONTRATO, VND1000_ON.CODIGO_GRUPO_CONTRATO, PS1044.NOME_ESTADO_CIVIL ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN ESP0002 ON (VND1000_ON.CODIGO_GRUPO_CONTRATO = ESP0002.CODIGO_GRUPO_CONTRATO) ';
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssociado = jn_query($queryAssociado);
	if(!$rowAssociado = jn_fetch_object($resAssociado)){
		echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
		exit;
	}
	
	
	$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
	$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
	$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
	
	$resPerc = jn_query($queryPerc);
	while($rowPerc = jn_fetch_object($resPerc)){
		$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
	}
	
	$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	
	$valorTit = $rowValores->VALOR_PLANO;

	if($percentual > 0){
		$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
		$valorTit = ($rowValores->VALOR_PLANO + $calculo);
	}
	
	$valorTotal = ($valorTotal + $valorTit);

	//Tratativas para dependentes

	//Dependente 1
	$codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';

	$queryDep1  = ' SELECT ';
	$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, ';
	$queryDep1 .= ' 	PESO, ALTURA ';
	$queryDep1 .= ' FROM VND1000_ON ';
	$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

	$resDep1 = jn_query($queryDep1);
	if($rowDep1 = jn_fetch_object($resDep1)){
		$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
		
		$diaNascDep1 = '';
		$mesNascDep1 = '';
		$anoNascDep1 = '';
		
		$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
		$diaNascDep1 = explode(' ', $diaNascDep1);
		$diaNascDep1 = $diaNascDep1[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep1 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep1);

		
		$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
		$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
		$numeroRGDep1 = $rowDep1->NUMERO_RG;
		$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$sexoDep1 = $rowDep1->SEXO;
		$nomeMaeDep1 = $rowDep1->NOME_MAE;
		$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
		$parentescoDep1 = $rowDep1->NOME_PARENTESCO;
		$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
		$pesoDep1 = $rowDep1->PESO;
		$alturaDep1 = str_replace(',','.',$rowDep1->ALTURA);
	}

	//Dependente 2
	$codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';

	$queryDep2  = ' SELECT ';
	$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, ';
	$queryDep2 .= ' 	PESO, ALTURA ';
	$queryDep2 .= ' FROM VND1000_ON ';
	$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep2 = jn_query($queryDep2);
	if($rowDep2 = jn_fetch_object($resDep2)){
		$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
		$diaNascDep2 = '';
		$mesNascDep2 = '';
		$anoNascDep2 = '';
			
		$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
		$diaNascDep2 = explode(' ', $diaNascDep2);
		$diaNascDep2 = $diaNascDep2[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep2 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep2);
		
		$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
		$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
		$numeroRGDep2 = $rowDep2->NUMERO_RG;
		$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$sexoDep2 = $rowDep2->SEXO;
		$nomeMaeDep2 = $rowDep2->NOME_MAE;
		$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
		$parentescoDep2 = $rowDep2->NOME_PARENTESCO;
		$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
		$pesoDep2 = $rowDep2->PESO;
		$alturaDep2 = str_replace(',','.',$rowDep2->ALTURA);
	}

	//Dependente 3
	$codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';

	$queryDep3  = ' SELECT ';
	$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, ';
	$queryDep3 .= ' 	PESO, ALTURA ';
	$queryDep3 .= ' FROM VND1000_ON ';
	$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep3 = jn_query($queryDep3);
	if($rowDep3 = jn_fetch_object($resDep3)){
	
		$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
		
		$diaNascDep3 = '';
		$mesNascDep3 = '';
		$anoNascDep3 = '';
		
		$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
		$diaNascDep3 = explode(' ', $diaNascDep3);
		$diaNascDep3 = $diaNascDep3[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep3 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep3);
		
		$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
		$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
		$numeroRGDep3 = $rowDep3->NUMERO_RG;
		$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$sexoDep3 = $rowDep3->SEXO;
		$nomeMaeDep3 = $rowDep3->NOME_MAE;
		$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
		$parentescoDep3 = $rowDep3->NOME_PARENTESCO;
		$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
		$pesoDep3 = $rowDep3->PESO;
		$alturaDep3 = str_replace(',','.',$rowDep3->ALTURA);
	}

	//Dependente 4
	$codigoDep4 = explode('.',$codAssociadoTmp);
	$codigoDep4 = $codigoDep4[0] . '.4';

	$queryDep4  = ' SELECT ';
	$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, ';
	$queryDep4 .= ' 	PESO, ALTURA ';
	$queryDep4 .= ' FROM VND1000_ON ';
	$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
	$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep4 = jn_query($queryDep4);
	if($rowDep4 = jn_fetch_object($resDep4)){
		
		$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
		
		$diaNascDep4 = '';
		$mesNascDep4 = '';
		$anoNascDep4 = '';
		
		$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
		$diaNascDep4 = explode(' ', $diaNascDep4);
		$diaNascDep4 = $diaNascDep4[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep4 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep4);
		
		$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
		$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
		$numeroRGDep4 = $rowDep4->NUMERO_RG;
		$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$sexoDep4 = $rowDep4->SEXO;
		$nomeMaeDep4 = $rowDep4->NOME_MAE;
		$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
		$parentescoDep4 = $rowDep4->NOME_PARENTESCO;
		$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
		$pesoDep4 = $rowDep4->PESO;
		$alturaDep4 = str_replace(',','.',$rowDep4->ALTURA);
	}

	//Dependente 5
	$codigoDep5 = explode('.',$codAssociadoTmp);
	$codigoDep5 = $codigoDep5[0] . '.5';

	$queryDep5  = ' SELECT ';
	$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, ';
	$queryDep5 .= ' 	PESO, ALTURA ';
	$queryDep5 .= ' FROM VND1000_ON ';
	$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
	$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep5 = jn_query($queryDep5);
	if($rowDep5 = jn_fetch_object($resDep5)){
		
		$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
		$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
		
		$diaNascDep5 = '';
		$mesNascDep5 = '';
		$anoNascDep5 = '';
		
		$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
		$diaNascDep5 = explode(' ', $diaNascDep5);
		$diaNascDep5 = $diaNascDep5[0];
		
		$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
		$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
		$numeroRGDep5 = $rowDep5->NUMERO_RG;
		$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
		$sexoDep5 = $rowDep5->SEXO;
		$nomeMaeDep5 = $rowDep5->NOME_MAE;
		$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
		$parentescoDep5 = $rowDep5->NOME_PARENTESCO;
		$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
		$pesoDep5 = $rowDep5->PESO;
		$alturaDep5 = str_replace(',','.',$rowDep5->ALTURA);
	}	
	

	if($_GET['pagina'] == '1'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax1.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 15, 0, 625, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 240, 1420, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 14, 0, 450, 1420, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 10, 0, 130, 1595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}


	if($_GET['pagina'] == '2'){

		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax2.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 850, 250, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
		imagettftext($imagem, 14, 0, 120, 250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->DESCRICAO_GRUPO_CONTRATO));
		imagettftext($imagem, 14, 0, 120, 340, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 120, 385, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 14, 0, 120, 480, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 14, 0, 235, 480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
		imagettftext($imagem, 14, 0, 880, 385, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 14, 0, 250, 385, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
		imagettftext($imagem, 14, 0, 340, 480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
		imagettftext($imagem, 14, 0, 120, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 14, 0, 120, 625, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 14, 0, 120, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 14, 0, 120, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 14, 0, 120, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 14, 0, 420, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 14, 0, 420, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 14, 0, 820, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 14, 0, 820, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 14, 0, 1100, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 14, 0, 1100, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 14, 0, 120, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
		imagettftext($imagem, 14, 0, 120, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
		imagettftext($imagem, 14, 0, 420, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
		
		//Dependente 1
		imagettftext($imagem, 14, 0, 130, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
		imagettftext($imagem, 14, 0, 1100, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
		imagettftext($imagem, 14, 0, 130, 900, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
		imagettftext($imagem, 14, 0, 360, 900, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
		imagettftext($imagem, 14, 0, 468, 900, $cor,"../../Site/assets/img/arial.ttf",$parentescoDep1);
		imagettftext($imagem, 14, 0, 580, 900, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
		imagettftext($imagem, 14, 0, 850, 900, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
		imagettftext($imagem, 14, 0, 130, 950, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
		imagettftext($imagem, 14, 0, 130, 1000, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep1);
		imagettftext($imagem, 14, 0, 500, 1000, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
		
		//Dependente 2
		imagettftext($imagem, 14, 0, 130, 1040, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
		imagettftext($imagem, 14, 0, 1100, 1040, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
		imagettftext($imagem, 14, 0, 130, 1090, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
		imagettftext($imagem, 14, 0, 360, 1090, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
		imagettftext($imagem, 14, 0, 468, 1090, $cor,"../../Site/assets/img/arial.ttf",$parentescoDep2);
		imagettftext($imagem, 14, 0, 580, 1090, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
		imagettftext($imagem, 14, 0, 850, 1090, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
		imagettftext($imagem, 14, 0, 130, 1140, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
		imagettftext($imagem, 14, 0, 130, 1185, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep2);
		imagettftext($imagem, 14, 0, 500, 1185, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
		
		//Dependente 3
		imagettftext($imagem, 14, 0, 130, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
		imagettftext($imagem, 14, 0, 1100, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
		imagettftext($imagem, 14, 0, 130, 1280, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
		imagettftext($imagem, 14, 0, 360, 1280, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
		imagettftext($imagem, 14, 0, 468, 1280, $cor,"../../Site/assets/img/arial.ttf",$parentescoDep3);
		imagettftext($imagem, 14, 0, 580, 1280, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
		imagettftext($imagem, 14, 0, 850, 1280, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
		imagettftext($imagem, 14, 0, 130, 1330, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
		imagettftext($imagem, 14, 0, 130, 1375, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep3);
		imagettftext($imagem, 14, 0, 500, 1375, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
		
		//Dependente 4
		imagettftext($imagem, 14, 0, 130, 1420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
		imagettftext($imagem, 14, 0, 1100, 1420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
		imagettftext($imagem, 14, 0, 130, 1470, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);
		imagettftext($imagem, 14, 0, 360, 1470, $cor,"../../Site/assets/img/arial.ttf",$idadeDep4);
		imagettftext($imagem, 14, 0, 468, 1470, $cor,"../../Site/assets/img/arial.ttf",$parentescoDep4);
		imagettftext($imagem, 14, 0, 580, 1470, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep4);
		imagettftext($imagem, 14, 0, 850, 1470, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep4);
		imagettftext($imagem, 14, 0, 130, 1520, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep4);
		imagettftext($imagem, 14, 0, 130, 1565, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep4);
		imagettftext($imagem, 14, 0, 500, 1565, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep4);
		imagettftext($imagem, 10, 0, 130, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,');
		imagettftext($imagem, 10, 0, 270, 1655, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 10, 0, 750, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}


	if($_GET['pagina'] == '3'){
		$linha = 0;
		
		$queryPlano = 'SELECT CODIGO_CADASTRO_ANS FROM PS1030 WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$resPlano = jn_query($queryPlano);
		$rowPlano = jn_fetch_object($resPlano);
		$registroANS = $rowPlano->CODIGO_CADASTRO_ANS;
		
		
		if($registroANS == 408050995){
			$linha = 370;
		}elseif($registroANS == 481926188){
			$linha = 410;
		}elseif($registroANS == 481682180){
			$linha = 435;
		}elseif($registroANS == 477844178){
			$linha = 465;
		}elseif($registroANS == 486522207){
			$linha = 493;
		}elseif($registroANS == 477831176){
			$linha = 523;
		}elseif($registroANS == 474328158){
			$linha = 550;
		}elseif($registroANS == 480081188){
			$linha = 578;
		}elseif($registroANS == 474409158){
			$linha = 603;
		}elseif($registroANS == 477826170){
			$linha = 633;
		}elseif($registroANS == 486516202){
			$linha = 660;
		}elseif($registroANS == 474468153){
			$linha = 690;
		}elseif($registroANS == 474454153){
			$linha = 718;
		}elseif($registroANS == 474465159){
			$linha = 742;
		}elseif($registroANS == 474453155){
			$linha = 770;
		}elseif($registroANS == 474452157){
			$linha = 800;
		}elseif($registroANS == 474441151){
			$linha = 828;
		}elseif($registroANS == 474440153){
			$linha = 857;
		}elseif($registroANS == 474435157){
			$linha = 885;
		}elseif($registroANS == 474342153){
			$linha = 910;
		}elseif($registroANS == 474425150){
			$linha = 937;
		}elseif($registroANS == 481928184){
			$linha = 1105;
		}elseif($registroANS == 481681181){
			$linha = 1135;
		}elseif($registroANS == 477845176){
			$linha = 1163;
		}elseif($registroANS == 474408150){
			$linha = 1190;
		}elseif($registroANS == 477832174){
			$linha = 1220;
		}elseif($registroANS == 474403159){
			$linha = 1248;
		}elseif($registroANS == 480082186){
			$linha = 1275;
		}elseif($registroANS == 474329156){
			$linha = 1300;
		}elseif($registroANS == 477827178){
			$linha = 1328;
		}elseif($registroANS == 486517201){
			$linha = 1358;
		}elseif($registroANS == 486579201){
			$linha = 1386;
		}elseif($registroANS == 474464151){
			$linha = 1412;
		}elseif($registroANS == 474463152){
			$linha = 1438;
		}elseif($registroANS == 474451159){
			$linha = 1470;
		}elseif($registroANS == 474450151){
			$linha = 1498;
		}elseif($registroANS == 474337157){
			$linha = 1525;
		}elseif($registroANS == 474336159){
			$linha = 1553;
		}elseif($registroANS == 474343151){
			$linha = 1580;
		}elseif($registroANS == 474434159){
			$linha = 1608;
		}elseif($registroANS == 474353159){
			$linha = 1635;
		}		
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax3.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 90, $linha, $cor,"../../Site/assets/img/arial.ttf",'X');
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '4'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax4.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 10, 0, 130, 585, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,');
		imagettftext($imagem, 10, 0, 270, 585, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 10, 0, 580, 585, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '5'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax5.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 220, 730, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 14, 0, 340, 730, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
		imagettftext($imagem, 14, 0, 220, 775, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
		imagettftext($imagem, 14, 0, 340, 775, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
		imagettftext($imagem, 14, 0, 220, 810, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
		imagettftext($imagem, 14, 0, 340, 810, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
		imagettftext($imagem, 14, 0, 220, 850, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
		imagettftext($imagem, 14, 0, 340, 850, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));
		imagettftext($imagem, 14, 0, 220, 890, $cor,"../../Site/assets/img/arial.ttf",$idadeDep4);
		imagettftext($imagem, 14, 0, 340, 890, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
		imagettftext($imagem, 14, 0, 740, 890, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
		
		if($rowAssociado->FLAG_DEBITO_AUTOMATICO == 'S'){
			imagettftext($imagem, 20, 0, 90, 345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}
		
		imagettftext($imagem, 14, 0, 140, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_BANCO));
		imagettftext($imagem, 14, 0, 240, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_BANCO));
		imagettftext($imagem, 14, 0, 460, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_AGENCIA));
		imagettftext($imagem, 14, 0, 810, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTA));
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '6'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax6.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 890, 1080, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->VALOR_TAXA_ADESAO);
		imagettftext($imagem, 14, 0, 410, 1310, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NOME_VENDEDOR);
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '7'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax7.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '8'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta5_Vidamax8.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '9'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax9.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '10'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax10.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 180, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 180, 1310, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 14, 0, 200, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 14, 0, 970, 1210, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 14, 0, 1035, 1210, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 14, 0, 1100, 1210, $cor,"../../Site/assets/img/arial.ttf",date('y'));
		imagettftext($imagem, 14, 0, 180, 1550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 14, 0, 180, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
		imagettftext($imagem, 14, 0, 200, 1500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 14, 0, 970, 1500, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 14, 0, 1035, 1500, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 14, 0, 1100, 1500, $cor,"../../Site/assets/img/arial.ttf",date('y'));



		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '11'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax11.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$pesoTit = $rowAssociado->PESO;
		$alturaTit = str_replace(',','.',$rowAssociado->ALTURA);
		
		if($rowAssociado->PESO){		           	
			$imcTitular = ($rowAssociado->PESO / ($alturaTit * $alturaTit));
			$imcTitular = ($imcTitular * 10000);
			$imcTitular = number_format($imcTitular,1);				
		}
		
		if($pesoDep1){			
			$imcDep1 = ($pesoDep1 / ($alturaDep1 * $alturaDep1));
			$imcDep1 = ($imcDep1 * 10000);
			$imcDep1 = number_format($imcDep1,1);								
		}
		
		if($pesoDep2){			
			$imcDep2 = ($pesoDep2 / ($alturaDep2 * $alturaDep2));
			$imcDep2 = ($imcDep2 * 10000);
			$imcDep2 = number_format($imcDep2,1);
		}
		
		if($pesoDep3){			
			$imcDep3 = ($pesoDep3 / ($alturaDep3 * $alturaDep3));
			$imcDep3 = ($imcDep3 * 10000);
			$imcDep3 = number_format($imcDep3,1);
		}
		
		if($pesoDep4){			
			$imcDep4 = ($pesoDep4 / ($alturaDep4 * $alturaDep4));
			$imcDep4 = ($imcDep4 * 10000);
			$imcDep4 = number_format($imcDep4,1);
		}

		$queryDecTit  = ' SELECT ';
		$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
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
				$coluna = 1005;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
				$coluna = 1035;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
				$coluna = 1065;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
				$coluna = 1095;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
				$coluna = 1125;
			}
			
			if($rowDecTit->NUMERO_PERGUNTA == '1'){
				imagettftext($imagem, 14, 0, $coluna, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
				imagettftext($imagem, 14, 0, $coluna, 790, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
				imagettftext($imagem, 14, 0, $coluna, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
				imagettftext($imagem, 14, 0, $coluna, 845, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '5'){
				imagettftext($imagem, 14, 0, $coluna, 890, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
				imagettftext($imagem, 14, 0, $coluna, 918, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
				imagettftext($imagem, 14, 0, $coluna, 943, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
				imagettftext($imagem, 14, 0, $coluna, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
				imagettftext($imagem, 14, 0, $coluna, 992, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
				imagettftext($imagem, 14, 0, $coluna, 1025, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '11'){
				imagettftext($imagem, 14, 0, $coluna, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
				imagettftext($imagem, 14, 0, $coluna, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
				imagettftext($imagem, 14, 0, $coluna, 1115, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
				imagettftext($imagem, 14, 0, $coluna, 1140, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '15'){
				imagettftext($imagem, 14, 0, $coluna, 1170, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '16'){
				imagettftext($imagem, 14, 0, $coluna, 1205, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '17'){
				imagettftext($imagem, 14, 0, $coluna, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '18'){
				imagettftext($imagem, 14, 0, $coluna, 1265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '19'){
				imagettftext($imagem, 14, 0, $coluna, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '20'){
				imagettftext($imagem, 14, 0, $coluna, 1320, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '21'){
				imagettftext($imagem, 14, 0, $coluna, 1345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '22'){
				imagettftext($imagem, 14, 0, $coluna, 1380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '23'){
				imagettftext($imagem, 14, 0, $coluna, 1415, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '24'){
				imagettftext($imagem, 14, 0, $coluna, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '25'){
				imagettftext($imagem, 14, 0, $coluna, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}	
			
		}
		
		imagettftext($imagem, 14, 0, 255, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoTit));
		imagettftext($imagem, 14, 0, 250, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaTit));
		imagettftext($imagem, 14, 0, 255, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcTitular));
		imagettftext($imagem, 14, 0, 255, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
		
		imagettftext($imagem, 14, 0, 380, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep1));
		imagettftext($imagem, 14, 0, 380, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep1));
		imagettftext($imagem, 14, 0, 385, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep1));
		imagettftext($imagem, 14, 0, 380, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
		
		imagettftext($imagem, 14, 0, 510, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep2));
		imagettftext($imagem, 14, 0, 510, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep2));
		imagettftext($imagem, 14, 0, 515, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep2));
		imagettftext($imagem, 14, 0, 510, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
		
		imagettftext($imagem, 14, 0, 640, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep3));
		imagettftext($imagem, 14, 0, 640, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep3));
		imagettftext($imagem, 14, 0, 645, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep3));
		imagettftext($imagem, 14, 0, 640, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
		
		imagettftext($imagem, 14, 0, 770, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep4));
		imagettftext($imagem, 14, 0, 770, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep4));
		imagettftext($imagem, 14, 0, 775, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep4));
		imagettftext($imagem, 14, 0, 770, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep4));
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '12'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax12.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$queryObservacoes  = ' 	SELECT  ';	
		$queryObservacoes .= ' 		NUMERO_PERGUNTA, CODIGO_ASSOCIADO, DESCRICAO_OBSERVACAO, ';
		$queryObservacoes .= ' 		CASE ';
		$queryObservacoes .= ' 			WHEN VND1005_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$queryObservacoes .= ' 				THEN "TIT."  ';
		$queryObservacoes .= ' 			ELSE "DEP."  ';
		$queryObservacoes .= ' 		END  AS TIPO_ASSOCIADO';
		$queryObservacoes .= ' 	FROM VND1005_ON  ';
		$queryObservacoes .= ' 	WHERE CODIGO_ASSOCIADO IN ( ';
		$queryObservacoes .= ' 		SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);	
		$queryObservacoes .= ' 	) ';	
		
		$resObservacoes = jn_query($queryObservacoes);
		
		$GridObservacoes = Array();
		$i = 0;
		
		while($rowObservacoes = jn_fetch_object($resObservacoes)){		
			$GridObservacoes[$i]['NUMERO_PERGUNTA'] = $rowObservacoes->NUMERO_PERGUNTA;
			$GridObservacoes[$i]['CODIGO_ASSOCIADO'] = $rowObservacoes->CODIGO_ASSOCIADO;
			$GridObservacoes[$i]['DESCRICAO_OBSERVACAO'] = jn_utf8_encode($rowObservacoes->DESCRICAO_OBSERVACAO);
			$GridObservacoes[$i]['TIPO_ASSOCIADO'] = jn_utf8_encode($rowObservacoes->TIPO_ASSOCIADO);
			$i++;
		}
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 110, 340, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 340, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 340, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 375,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 375, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 375, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 405, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 405, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 405, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 440,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 480,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 480, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 480, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 510,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 510, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 510, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 10, 0, 130, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,');
		imagettftext($imagem, 10, 0, 270, 1645, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 10, 0, 750, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '13'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax13.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );	
	}
	
	if($_GET['pagina'] == '14'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta5_Vidamax14.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '15'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta5_Vidamax15.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '16'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta5_Vidamax16.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '17'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta5_Vidamax17.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '18'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax18.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '19'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax19.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '20'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax20.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );	
	}
	
	if($_GET['pagina'] == '21'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax21.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));

		imagettftext($imagem, 10, 0, 130, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,');
		imagettftext($imagem, 10, 0, 270, 1590, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 10, 0, 750, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );


	}
	
	if($_GET['pagina'] == '22'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta5_Vidamax22.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '23'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta5_Vidamax23.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '24'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax24.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );

		imagettftext($imagem, 14, 0, 220, 860, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,');
		imagettftext($imagem, 14, 0, 360, 860, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 14, 0, 360, 900, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 380, 935, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 950, 935, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));

		imagettftext($imagem, 14, 0, 295, 975, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 14, 0, 950, 975, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
		imagettftext($imagem, 10, 0, 250, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 10, 0, 750, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['pagina'] == '25'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta5_Vidamax25.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 550, 120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 220, 1570, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 14, 0, 350, 1570, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 14, 0, 600, 1570, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
		imagettftext($imagem, 10, 0, 210, 1660, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
}

if($_GET['modelo'] == 6){
	$codAssociadoTmp = $_GET['codAssociado'];
	$percentual = 0;
	$valorTotal = 0;
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, VND1000_ON.CODIGO_PARENTESCO,  VND1001_ON.CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, ';
	$queryAssociado .= ' 	VND1001_ON.FLAG_DEBITO_AUTOMATICO, VND1001_ON.CODIGO_BANCO, VND1001_ON.NUMERO_CONTA,  VND1001_ON.NUMERO_AGENCIA, ';
	$queryAssociado .= ' 	ESP0002.DESCRICAO_GRUPO_CONTRATO, VND1000_ON.CODIGO_GRUPO_CONTRATO ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN ESP0002 ON (VND1000_ON.CODIGO_GRUPO_CONTRATO = ESP0002.CODIGO_GRUPO_CONTRATO) ';
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssociado = jn_query($queryAssociado);
	if(!$rowAssociado = jn_fetch_object($resAssociado)){
		echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
		exit;
	}
	
	
	$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
	$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
	$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
	
	$resPerc = jn_query($queryPerc);
	while($rowPerc = jn_fetch_object($resPerc)){
		$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
	}
	
	$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	
	$valorTit = $rowValores->VALOR_PLANO;

	if($percentual > 0){
		$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
		$valorTit = ($rowValores->VALOR_PLANO + $calculo);
	}
	
	$valorTotal = ($valorTotal + $valorTit);

	$ContaDigito = list($conta, $digito) = explode('-',$rowAssociado->NUMERO_CONTA);

	//Tratativas para dependentes

	//Dependente 1
	$codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';

	$queryDep1  = ' SELECT ';
	$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep1 .= ' 	PESO, ALTURA ';
	$queryDep1 .= ' FROM VND1000_ON ';
	$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

	$resDep1 = jn_query($queryDep1);
	if($rowDep1 = jn_fetch_object($resDep1)){
		$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
		
		$diaNascDep1 = '';
		$mesNascDep1 = '';
		$anoNascDep1 = '';
		
		$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
		$diaNascDep1 = explode(' ', $diaNascDep1);
		$diaNascDep1 = $diaNascDep1[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep1 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep1);

		
		$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
		$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
		$numeroRGDep1 = $rowDep1->NUMERO_RG;
		$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$sexoDep1 = $rowDep1->SEXO;
		$nomeMaeDep1 = $rowDep1->NOME_MAE;
		$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
		$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
		$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
		$pesoDep1 = $rowDep1->PESO;
		$alturaDep1 = $rowDep1->ALTURA;
	}

	//Dependente 2
	$codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';

	$queryDep2  = ' SELECT ';
	$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep2 .= ' 	PESO, ALTURA ';
	$queryDep2 .= ' FROM VND1000_ON ';
	$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep2 = jn_query($queryDep2);
	if($rowDep2 = jn_fetch_object($resDep2)){
		
		$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
		
		$diaNascDep2 = '';
		$mesNascDep2 = '';
		$anoNascDep2 = '';
		
		$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
		$diaNascDep2 = explode(' ', $diaNascDep2);
		$diaNascDep2 = $diaNascDep2[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep2 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep2);
		
		$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
		$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
		$numeroRGDep2 = $rowDep2->NUMERO_RG;
		$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$sexoDep2 = $rowDep2->SEXO;
		$nomeMaeDep2 = $rowDep2->NOME_MAE;
		$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
		$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
		$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
		$pesoDep2 = $rowDep2->PESO;
		$alturaDep2= $rowDep2->ALTURA;
	}

	//Dependente 3
	$codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';

	$queryDep3  = ' SELECT ';
	$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep3 .= ' 	PESO, ALTURA ';
	$queryDep3 .= ' FROM VND1000_ON ';
	$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep3 = jn_query($queryDep3);
	if($rowDep3 = jn_fetch_object($resDep3)){
		
		$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
		
		$diaNascDep3 = '';
		$mesNascDep3 = '';
		$anoNascDep3 = '';
		
		$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
		$diaNascDep3 = explode(' ', $diaNascDep3);
		$diaNascDep3 = $diaNascDep3[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep3 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep3);
		
		$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
		$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
		$numeroRGDep3 = $rowDep3->NUMERO_RG;
		$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$sexoDep3 = $rowDep3->SEXO;
		$nomeMaeDep3 = $rowDep3->NOME_MAE;
		$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
		$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
		$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
		$pesoDep3 = $rowDep3->PESO;
		$alturaDep3= $rowDep3->ALTURA;
	}

	//Dependente 4
	$codigoDep4 = explode('.',$codAssociadoTmp);
	$codigoDep4 = $codigoDep4[0] . '.4';

	$queryDep4  = ' SELECT ';
	$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep4 .= ' 	PESO, ALTURA ';
	$queryDep4 .= ' FROM VND1000_ON ';
	$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
	$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep4 = jn_query($queryDep4);
	if($rowDep4 = jn_fetch_object($resDep4)){
		
		$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
		
		$diaNascDep4 = '';
		$mesNascDep4 = '';
		$anoNascDep4 = '';
		
		$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
		$diaNascDep4 = explode(' ', $diaNascDep4);
		$diaNascDep4 = $diaNascDep4[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep4 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep4);
		
		$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
		$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
		$numeroRGDep4 = $rowDep4->NUMERO_RG;
		$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$sexoDep4 = $rowDep4->SEXO;
		$nomeMaeDep4 = $rowDep4->NOME_MAE;
		$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
		$parentescoDep4 = $rowDep4->CODIGO_PARENTESCO;
		$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
		$pesoDep4 = $rowDep4->PESO;
		$alturaDep4= $rowDep4->ALTURA;
	}

	//Dependente 5
	$codigoDep5 = explode('.',$codAssociadoTmp);
	$codigoDep5 = $codigoDep5[0] . '.5';

	$queryDep5  = ' SELECT ';
	$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep5 .= ' 	PESO, ALTURA ';
	$queryDep5 .= ' FROM VND1000_ON ';
	$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
	$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep5 = jn_query($queryDep5);
	if($rowDep5 = jn_fetch_object($resDep5)){
		
		$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
		$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
		
		$diaNascDep5 = '';
		$mesNascDep5 = '';
		$anoNascDep5 = '';
		
		$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
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
		$pesoDep5 = $rowDep5->PESO;
		$alturaDep5= $rowDep5->ALTURA;
	}

	if($_GET['pagina'] == '1'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax1.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 660, 333, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 223, 1420, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 14, 0, 415, 1420, $cor,"../../Site/assets/img/arial.ttf",date('M'));
		imagettftext($imagem, 14, 0, 110, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}	
	
	if($_GET['pagina'] == '2'){
		$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
		$admissao = explode('/',$data);
		$diaAdmissao = $admissao[0];
		$mesAdmissao = $admissao[1];
		$anoAdmissao = $admissao[2];
		
		
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax2.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 850, 250, $cor,"../../Site/assets/img/arial.ttf",$diaAdmissao);
		imagettftext($imagem, 14, 0, 950, 250, $cor,"../../Site/assets/img/arial.ttf",$mesAdmissao);
		imagettftext($imagem, 14, 0, 1050, 250, $cor,"../../Site/assets/img/arial.ttf",$anoAdmissao);
		imagettftext($imagem, 14, 0, 120, 250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->DESCRICAO_GRUPO_CONTRATO));
		imagettftext($imagem, 14, 0, 120, 340, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 120, 385, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 14, 0, 120, 480, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 14, 0, 880, 385, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 14, 0, 250, 385, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
		imagettftext($imagem, 14, 0, 340, 480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
		imagettftext($imagem, 14, 0, 120, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 14, 0, 120, 625, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 14, 0, 120, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 14, 0, 120, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 14, 0, 120, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 14, 0, 420, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 14, 0, 420, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 14, 0, 820, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 14, 0, 820, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 14, 0, 1100, 580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 14, 0, 1100, 670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 14, 0, 120, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
		imagettftext($imagem, 14, 0, 120, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
		imagettftext($imagem, 14, 0, 420, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
		
		//Dependente 1
		imagettftext($imagem, 14, 0, 130, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
		imagettftext($imagem, 14, 0, 1100, 850, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
		imagettftext($imagem, 14, 0, 130, 900, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
		imagettftext($imagem, 14, 0, 360, 900, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
		imagettftext($imagem, 14, 0, 580, 900, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
		imagettftext($imagem, 14, 0, 850, 900, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
		imagettftext($imagem, 14, 0, 130, 950, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
		imagettftext($imagem, 14, 0, 130, 1000, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep1);
		imagettftext($imagem, 14, 0, 500, 1000, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
		
		//Dependente 2
		imagettftext($imagem, 14, 0, 130, 1040, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
		imagettftext($imagem, 14, 0, 1100, 1040, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
		imagettftext($imagem, 14, 0, 130, 1090, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
		imagettftext($imagem, 14, 0, 360, 1090, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
		imagettftext($imagem, 14, 0, 580, 1090, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
		imagettftext($imagem, 14, 0, 850, 1090, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
		imagettftext($imagem, 14, 0, 130, 1140, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
		imagettftext($imagem, 14, 0, 130, 1185, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep2);
		imagettftext($imagem, 14, 0, 500, 1185, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
		
		//Dependente 3
		imagettftext($imagem, 14, 0, 130, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
		imagettftext($imagem, 14, 0, 1100, 1230, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
		imagettftext($imagem, 14, 0, 130, 1280, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
		imagettftext($imagem, 14, 0, 360, 1280, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
		imagettftext($imagem, 14, 0, 580, 1280, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
		imagettftext($imagem, 14, 0, 850, 1280, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
		imagettftext($imagem, 14, 0, 130, 1330, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
		imagettftext($imagem, 14, 0, 130, 1375, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep3);
		imagettftext($imagem, 14, 0, 500, 1375, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
		
		//Dependente 4
		imagettftext($imagem, 14, 0, 130, 1420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
		imagettftext($imagem, 14, 0, 1100, 1420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
		imagettftext($imagem, 14, 0, 130, 1470, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);
		imagettftext($imagem, 14, 0, 360, 1470, $cor,"../../Site/assets/img/arial.ttf",$idadeDep4);
		imagettftext($imagem, 14, 0, 580, 1470, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep4);
		imagettftext($imagem, 14, 0, 850, 1470, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep4);
		imagettftext($imagem, 14, 0, 130, 1520, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep4);
		imagettftext($imagem, 14, 0, 130, 1565, $cor,"../../Site/assets/img/arial.ttf",$pisPasepDep4);
		imagettftext($imagem, 14, 0, 500, 1565, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep4);
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}


	if($_GET['pagina'] == '3'){
		$linha = 0;
		
		$queryPlano = 'SELECT CODIGO_CADASTRO_ANS FROM PS1030 WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$resPlano = jn_query($queryPlano);
		$rowPlano = jn_fetch_object($resPlano);
		$registroANS = $rowPlano->CODIGO_CADASTRO_ANS;
		
		if($registroANS == 408050995){
			$linha = 370;
		}elseif($registroANS == 481926188){
			$linha = 410;
		}elseif($registroANS == 481682180){
			$linha = 435;
		}elseif($registroANS == 477844178){
			$linha = 465;
		}elseif($registroANS == 474361150){
			$linha = 493;
		}elseif($registroANS == 477831176){
			$linha = 523;
		}elseif($registroANS == 474328158){
			$linha = 550;
		}elseif($registroANS == 480081188){
			$linha = 578;
		}elseif($registroANS == 474409158){
			$linha = 603;
		}elseif($registroANS == 477826170){
			$linha = 633;
		}elseif($registroANS == 477834171){
			$linha = 660;
		}elseif($registroANS == 474468153){
			$linha = 690;
		}elseif($registroANS == 474454153){
			$linha = 718;
		}elseif($registroANS == 474465159){
			$linha = 742;
		}elseif($registroANS == 474453155){
			$linha = 770;
		}elseif($registroANS == 474452157){
			$linha = 800;
		}elseif($registroANS == 474441151){
			$linha = 828;
		}elseif($registroANS == 474440153){
			$linha = 857;
		}elseif($registroANS == 474435157){
			$linha = 885;
		}elseif($registroANS == 474342153){
			$linha = 910;
		}elseif($registroANS == 474425150){
			$linha = 937;
		}elseif($registroANS == 481928184){
			$linha = 1105;
		}elseif($registroANS == 481681181){
			$linha = 1135;
		}elseif($registroANS == 477845176){
			$linha = 1163;
		}elseif($registroANS == 474408150){
			$linha = 1190;
		}elseif($registroANS == 477832174){
			$linha = 1220;
		}elseif($registroANS == 474403159){
			$linha = 1248;
		}elseif($registroANS == 480082186){
			$linha = 1275;
		}elseif($registroANS == 474329156){
			$linha = 1300;
		}elseif($registroANS == 477827178){
			$linha = 1328;
		}elseif($registroANS == 477833172){
			$linha = 1358;
		}elseif($registroANS == 474370159){
			$linha = 1386;
		}elseif($registroANS == 474464151){
			$linha = 1412;
		}elseif($registroANS == 474463152){
			$linha = 1438;
		}elseif($registroANS == 474451159){
			$linha = 1470;
		}elseif($registroANS == 474450151){
			$linha = 1498;
		}elseif($registroANS == 474337157){
			$linha = 1525;
		}elseif($registroANS == 474336159){
			$linha = 1553;
		}elseif($registroANS == 474343151){
			$linha = 1580;
		}elseif($registroANS == 474434159){
			$linha = 1608;
		}elseif($registroANS == 474353159){
			$linha = 1635;
		}
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax3.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 90, $linha, $cor,"../../Site/assets/img/arial.ttf",'X');
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '4'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax4.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 130, 610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' ,');
		imagettftext($imagem, 14, 0, 270, 610, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 14, 0, 580, 610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '5'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax5.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );

		if($rowAssociado->CODIGO_BANCO == '033' || $rowAssociado->CODIGO_BANCO == '33'){
			$nomeBanco = 'Santander';
		}elseif($rowAssociado->CODIGO_BANCO == '341'){
			$nomeBanco = 'Itaú';			
		}elseif($rowAssociado->CODIGO_BANCO == '1' || $rowAssociado->CODIGO_BANCO == '01' || $rowAssociado->CODIGO_BANCO == '001'){
			$nomeBanco = 'Banco do Brasil';			
		}
			
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 220, 730, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 14, 0, 140, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_BANCO));
		imagettftext($imagem, 14, 0, 280, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeBanco));
		imagettftext($imagem, 14, 0, 500, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_AGENCIA));
		imagettftext($imagem, 14, 0, 840, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($conta));
		imagettftext($imagem, 14, 0, 1100, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($digito));
		imagettftext($imagem, 14, 0, 340, 730, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
		imagettftext($imagem, 14, 0, 220, 775, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
		imagettftext($imagem, 14, 0, 340, 775, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep1));
		imagettftext($imagem, 14, 0, 220, 810, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
		imagettftext($imagem, 14, 0, 340, 810, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep2));
		imagettftext($imagem, 14, 0, 220, 850, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
		imagettftext($imagem, 14, 0, 340, 850, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep3));
		imagettftext($imagem, 14, 0, 220, 890, $cor,"../../Site/assets/img/arial.ttf",$idadeDep4);
		imagettftext($imagem, 14, 0, 340, 890, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorDep4));
		imagettftext($imagem, 14, 0, 740, 890, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '6'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax6.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 890, 1080, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->VALOR_TAXA_ADESAO);
		imagettftext($imagem, 14, 0, 410, 1310, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NOME_VENDEDOR);
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '7'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax7.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '8'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax8.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '9'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax9.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '10'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax10.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 180, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 180, 1310, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 14, 0, 180, 1550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
		imagettftext($imagem, 14, 0, 180, 1600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '11'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax11.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$pesoTit = $rowAssociado->PESO;
		$alturaTit = str_replace(',','.',$rowAssociado->ALTURA);
		
		if($rowAssociado->PESO){		           	
			$imcTitular = ($rowAssociado->PESO / ($alturaTit * $alturaTit));
			$imcTitular = ($imcTitular * 10000);
			$imcTitular = number_format($imcTitular,1);				
		}
		
		if($pesoDep1){			
			$imcDep1 = ($pesoDep1 / ($alturaDep1 * $alturaDep1));
			$imcDep1 = ($imcDep1 * 10000);
			$imcDep1 = number_format($imcDep1,1);								
		}
		
		if($pesoDep2){			
			$imcDep2 = ($pesoDep2 / ($alturaDep2 * $alturaDep2));
			$imcDep2 = ($imcDep2 * 10000);
			$imcDep2 = number_format($imcDep2,1);
		}
		
		if($pesoDep3){			
			$imcDep3 = ($pesoDep3 / ($alturaDep3 * $alturaDep3));
			$imcDep3 = ($imcDep3 * 10000);
			$imcDep3 = number_format($imcDep3,1);
		}
		
		if($pesoDep4){			
			$imcDep4 = ($pesoDep4 / ($alturaDep4 * $alturaDep4));
			$imcDep4 = ($imcDep4 * 10000);
			$imcDep4 = number_format($imcDep4,1);
		}

		$queryDecTit  = ' SELECT ';
		$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
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
				$coluna = 1005;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
				$coluna = 1035;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
				$coluna = 1065;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
				$coluna = 1095;
			}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
				$coluna = 1125;
			}
			
			if($rowDecTit->NUMERO_PERGUNTA == '1'){
				imagettftext($imagem, 14, 0, $coluna, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
				imagettftext($imagem, 14, 0, $coluna, 790, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
				imagettftext($imagem, 14, 0, $coluna, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
				imagettftext($imagem, 14, 0, $coluna, 845, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '5'){
				imagettftext($imagem, 14, 0, $coluna, 890, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
				imagettftext($imagem, 14, 0, $coluna, 918, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
				imagettftext($imagem, 14, 0, $coluna, 943, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
				imagettftext($imagem, 14, 0, $coluna, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
				imagettftext($imagem, 14, 0, $coluna, 992, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
				imagettftext($imagem, 14, 0, $coluna, 1025, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '11'){
				imagettftext($imagem, 14, 0, $coluna, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
				imagettftext($imagem, 14, 0, $coluna, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
				imagettftext($imagem, 14, 0, $coluna, 1115, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
				imagettftext($imagem, 14, 0, $coluna, 1140, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '15'){
				imagettftext($imagem, 14, 0, $coluna, 1170, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '16'){
				imagettftext($imagem, 14, 0, $coluna, 1205, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '17'){
				imagettftext($imagem, 14, 0, $coluna, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '18'){
				imagettftext($imagem, 14, 0, $coluna, 1265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '19'){
				imagettftext($imagem, 14, 0, $coluna, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '20'){
				imagettftext($imagem, 14, 0, $coluna, 1320, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '21'){
				imagettftext($imagem, 14, 0, $coluna, 1345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '22'){
				imagettftext($imagem, 14, 0, $coluna, 1380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '23'){
				imagettftext($imagem, 14, 0, $coluna, 1415, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '24'){
				imagettftext($imagem, 14, 0, $coluna, 1442, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}elseif($rowDecTit->NUMERO_PERGUNTA == '25'){
				imagettftext($imagem, 14, 0, $coluna, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
			}	
			
		}
		
		imagettftext($imagem, 14, 0, 255, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoTit));
		imagettftext($imagem, 14, 0, 250, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaTit));
		imagettftext($imagem, 14, 0, 255, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcTitular));
		imagettftext($imagem, 14, 0, 255, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
		
		imagettftext($imagem, 14, 0, 380, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep1));
		imagettftext($imagem, 14, 0, 380, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep1));
		imagettftext($imagem, 14, 0, 385, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep1));
		imagettftext($imagem, 14, 0, 380, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
		
		imagettftext($imagem, 14, 0, 510, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep2));
		imagettftext($imagem, 14, 0, 510, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep2));
		imagettftext($imagem, 14, 0, 515, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep2));
		imagettftext($imagem, 14, 0, 510, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
		
		imagettftext($imagem, 14, 0, 640, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep3));
		imagettftext($imagem, 14, 0, 640, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep3));
		imagettftext($imagem, 14, 0, 645, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep3));
		imagettftext($imagem, 14, 0, 640, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
		
		imagettftext($imagem, 14, 0, 770, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep4));
		imagettftext($imagem, 14, 0, 770, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep4));
		imagettftext($imagem, 14, 0, 775, 1620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep4));
		imagettftext($imagem, 14, 0, 770, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep4));
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '12'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax12.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$queryObservacoes  = ' 	SELECT  ';	
		$queryObservacoes .= ' 		NUMERO_PERGUNTA, CODIGO_ASSOCIADO, DESCRICAO_OBSERVACAO, ';
		$queryObservacoes .= ' 		CASE ';
		$queryObservacoes .= ' 			WHEN VND1005_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$queryObservacoes .= ' 				THEN "TIT."  ';
		$queryObservacoes .= ' 			ELSE "DEP."  ';
		$queryObservacoes .= ' 		END  AS TIPO_ASSOCIADO';
		$queryObservacoes .= ' 	FROM VND1005_ON  ';
		$queryObservacoes .= ' 	WHERE CODIGO_ASSOCIADO IN ( ';
		$queryObservacoes .= ' 		SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);	
		$queryObservacoes .= ' 	) ';	
		
		$resObservacoes = jn_query($queryObservacoes);
		
		$GridObservacoes = Array();
		$i = 0;
		
		while($rowObservacoes = jn_fetch_object($resObservacoes)){		
			$GridObservacoes[$i]['NUMERO_PERGUNTA'] = $rowObservacoes->NUMERO_PERGUNTA;
			$GridObservacoes[$i]['CODIGO_ASSOCIADO'] = $rowObservacoes->CODIGO_ASSOCIADO;
			$GridObservacoes[$i]['DESCRICAO_OBSERVACAO'] = jn_utf8_encode($rowObservacoes->DESCRICAO_OBSERVACAO);
			$GridObservacoes[$i]['TIPO_ASSOCIADO'] = jn_utf8_encode($rowObservacoes->TIPO_ASSOCIADO);
			$i++;
		}
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 110, 340, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 340, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 340, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 375,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 375, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 375, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 405, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 405, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 405, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 440,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 440, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 480,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 480, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 480, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['DESCRICAO_OBSERVACAO']);
		imagettftext($imagem, 14, 0, 110, 510,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['NUMERO_PERGUNTA']);
		imagettftext($imagem, 14, 0, 200, 510, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['TIPO_ASSOCIADO']);
		imagettftext($imagem, 14, 0, 300, 510, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['DESCRICAO_OBSERVACAO']);
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '13'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax13.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );	
	}
	
	if($_GET['pagina'] == '14'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax14.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '15'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax15.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '16'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax16.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '17'){
		$img = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax17.jpg");
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '18'){
		$imagem = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax18.jpg');	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '19'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax19.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '20'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax20.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );	
	}
	
	if($_GET['pagina'] == '21'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax21.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}

	if($_GET['pagina'] == '22'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax22.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '23'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax23.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '24'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta3_Vidamax24.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '25'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax25.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		imagettftext($imagem, 14, 0, 900, 120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		imagettftext($imagem, 14, 0, 220, 1576, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 14, 0, 400, 1576, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 14, 0, 600, 1576, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
		imagettftext($imagem, 14, 0, 180, 1660, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '26'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax26.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
		$admissao = explode('/',$data);
		$diaAdmissao = $admissao[0];
		$mesAdmissao = $admissao[1];
		$anoAdmissao = $admissao[2];

		$nascimento = explode('-',$rowAssociado->DATA_NASCIMENTO);
		$diaNascimento = explode(' ',$nascimento[2]);
		$diaNascimento = $diaNascimento[0];
		$mesNascimento = $nascimento[1];
		$anoNascimento = $nascimento[0];
		
		imagettftext($imagem, 14, 0, 100, 360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
		imagettftext($imagem, 14, 0, 120, 420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 280, 360, $cor,"../../Site/assets/img/arial.ttf",$diaAdmissao);
		imagettftext($imagem, 14, 0, 320, 360, $cor,"../../Site/assets/img/arial.ttf",$mesAdmissao);
		imagettftext($imagem, 14, 0, 380, 360, $cor,"../../Site/assets/img/arial.ttf",$anoAdmissao);
		imagettftext($imagem, 14, 0, 240, 460, $cor,"../../Site/assets/img/arial.ttf",$diaNascimento);
		imagettftext($imagem, 14, 0, 280, 460, $cor,"../../Site/assets/img/arial.ttf",$mesNascimento);
		imagettftext($imagem, 14, 0, 340, 460, $cor,"../../Site/assets/img/arial.ttf",$anoNascimento);
		imagettftext($imagem, 14, 0, 120, 500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 14, 0, 510, 500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
		imagettftext($imagem, 14, 0, 160, 610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 14, 0, 370, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 14, 0, 630, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 14, 0, 920, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 14, 0, 280, 680, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
		imagettftext($imagem, 14, 0, 710, 680, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
		imagettftext($imagem, 14, 0, 120, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
		
		if($rowAssociado->FLAG_DEBITO_AUTOMATICO == 'S'){
			imagettftext($imagem, 20, 0, 70, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}
		
		imagettftext($imagem, 16, 0, 260, 1285, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_AGENCIA));
		imagettftext($imagem, 16, 0, 860, 1285, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTA));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '27'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta3_Vidamax27.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		
		$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
		$admissao = explode('/',$data);
		$diaAdmissao = $admissao[0];
		$mesAdmissao = $admissao[1];
		$anoAdmissao = substr($admissao[2],-2);
		
		imagettftext($imagem, 14, 0, 120, 380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 14, 0, 100, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 14, 0, 700, 1046, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($diaAdmissao));
		imagettftext($imagem, 14, 0, 900, 1046, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($mesAdmissao));
		imagettftext($imagem, 14, 0, 1080, 1046, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($anoAdmissao));
		
		imagettftext($imagem, 14, 0, 80, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
		imagettftext($imagem, 14, 0, 930, 640, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
		
		imagettftext($imagem, 14, 0, 80, 675, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
		imagettftext($imagem, 14, 0, 930, 675, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
		
		imagettftext($imagem, 14, 0, 80, 705, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
		imagettftext($imagem, 14, 0, 930, 705, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
		
		imagettftext($imagem, 14, 0, 80, 735, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
		imagettftext($imagem, 14, 0, 930, 735, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep4);
		
		imagettftext($imagem, 14, 0, 80, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep5));
		imagettftext($imagem, 14, 0, 930, 765, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep5);
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
}

if($_GET['modelo'] == 7){
	if(($_GET['portabilidade'] == 'N') or ($_GET['portabilidade'] == '')){

		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}else{
			//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
		}
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
			$parentescoDep1 = $rowDep1->NOME_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
		$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->NOME_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
			$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->NOME_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
		}

		//Dependente 4
		$codigoDep4 = explode('.',$codAssociadoTmp);
		$codigoDep4 = $codigoDep4[0] . '.4';

		$queryDep4  = ' SELECT ';
		$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep4 .= ' 	PESO, ALTURA ';
		$queryDep4 .= ' FROM VND1000_ON ';
		$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
		$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep4 = jn_query($queryDep4);
		if($rowDep4 = jn_fetch_object($resDep4)){
			
			$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
			
			$diaNascDep4 = '';
			$mesNascDep4 = '';
			$anoNascDep4 = '';
			
			$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
			$diaNascDep4 = explode(' ', $diaNascDep4);
			$diaNascDep4 = $diaNascDep4[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep4 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep4);
			
			$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
			$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
			$numeroRGDep4 = $rowDep4->NUMERO_RG;
			$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$sexoDep4 = $rowDep4->SEXO;
			$nomeMaeDep4 = $rowDep4->NOME_MAE;
			$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
			$parentescoDep4 = $rowDep4->NOME_PARENTESCO;
			$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
			$pesoDep4 = $rowDep4->PESO;
			$alturaDep4= $rowDep4->ALTURA;
		}

		//Dependente 5
		$codigoDep5 = explode('.',$codAssociadoTmp);
		$codigoDep5 = $codigoDep5[0] . '.5';

		$queryDep5  = ' SELECT ';
		$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep5 .= ' 	PESO, ALTURA ';
		$queryDep5 .= ' FROM VND1000_ON ';
		$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
		$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep5 = jn_query($queryDep5);
		if($rowDep5 = jn_fetch_object($resDep5)){
			
			$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
			
			$diaNascDep5 = '';
			$mesNascDep5 = '';
			$anoNascDep5 = '';
			
			$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
			$diaNascDep5 = explode(' ', $diaNascDep5);
			$diaNascDep5 = $diaNascDep5[0];
			
			$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
			$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
			$numeroRGDep5 = $rowDep5->NUMERO_RG;
			$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$sexoDep5 = $rowDep5->SEXO;
			$nomeMaeDep5 = $rowDep5->NOME_MAE;
			$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
			$parentescoDep5 = $rowDep5->NOME_PARENTESCO;
			$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
			$pesoDep5 = $rowDep5->PESO;
			$alturaDep5= $rowDep5->ALTURA;
		}	
		
		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 14, 0, 618, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 14, 0, 212, 1419, $cor,"../../Site/assets/img/arial.ttf",date('d'));		
			imagettftext($imagem, 14, 0, 325, 1419, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));	
			imagettftext($imagem, 10, 0, 130, 1595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );		
		}		
			
		
		if($_GET['pagina'] == '2'){
			$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
			$admissao = explode('/',$data);
			$diaAdmissao = $admissao[0];
			$mesAdmissao = $admissao[1];
			$anoAdmissao = $admissao[2];

			
			$colunaAcomodacao == 0;
			if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 480;
			}else{
				$colunaAcomodacao = 285; 		
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 965, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
			imagettftext($imagem, 14, 0, 163, 290, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
			imagettftext($imagem, 10, 0, 610, 265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 820, 265, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 995, 355, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 10, 0, 180, 415, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 170, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 330, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 455, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 700, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 880, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 10, 0, 960, 455, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 1120, 455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
			imagettftext($imagem, 10, 0, 160, 510, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 160, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 10, 0, 160, 645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 160, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 680, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 870, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 1120, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 670, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			imagettftext($imagem, 10, 0, 160, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 390, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			
			
			//Dep1
			imagettftext($imagem, 10, 0, 180, 790, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 180, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 350, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
			imagettftext($imagem, 10, 0, 505, 830, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep1));
			imagettftext($imagem, 10, 0, 660, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
			imagettftext($imagem, 10, 0, 820, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
			imagettftext($imagem, 10, 0, 885, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 10, 0, 970, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 1120, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
			imagettftext($imagem, 10, 0, 170, 880, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
			imagettftext($imagem, 10, 0, 180, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
			imagettftext($imagem, 10, 0, 780, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));
			
			//Dep2
			imagettftext($imagem, 10, 0, 180, 1020, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 180, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
			imagettftext($imagem, 10, 0, 360, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
			imagettftext($imagem, 10, 0, 505, 1060, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep2));
			imagettftext($imagem, 10, 0, 660, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));
			imagettftext($imagem, 10, 0, 820, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));
			imagettftext($imagem, 10, 0, 885, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 10, 0, 970, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 1120, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
			imagettftext($imagem, 10, 0, 170, 1110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
			imagettftext($imagem, 10, 0, 180, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
			imagettftext($imagem, 10, 0, 780, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));		
			
			//Dep3
			imagettftext($imagem, 10, 0, 180, 1245, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 180, 1290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
			imagettftext($imagem, 10, 0, 360, 1290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
			imagettftext($imagem, 10, 0, 505, 1290, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep3));
			imagettftext($imagem, 10, 0, 660, 1290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
			imagettftext($imagem, 10, 0, 820, 1290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
			imagettftext($imagem, 10, 0, 885, 1290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 10, 0, 970, 1290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 1120, 1290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
			imagettftext($imagem, 10, 0, 170, 1335, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
			imagettftext($imagem, 10, 0, 180, 1420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
			imagettftext($imagem, 10, 0, 780, 1420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));		
			
			imagettftext($imagem, 10, 0, 405, 1532, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 950, 1532, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
			imagettftext($imagem, 10, 0, 180, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 122, 1655, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 10, 0, 320, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}


		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 935, 110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 10, 0, 320, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 122, 1655, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7_Vidamax4.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '5'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7_Vidamax5.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '6'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7_Vidamax6.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '7'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 935, 110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 240, 1435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' , ' . date('d/m/Y'));
			imagettftext($imagem, 10, 0, 420, 1490, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 110, 1544, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '8'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7_Vidamax8.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 935, 110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
			imagettftext($imagem, 14, 0, 985, 1120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->VALOR_TAXA_ADESAO));
			imagettftext($imagem, 12, 0, 136, 1162, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(valorPorExtenso(str_replace(',','.',$rowAssociado->VALOR_TAXA_ADESAO))));	
			imagettftext($imagem, 10, 0, 443, 1274, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 382, 1336, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '9'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7_Vidamax9.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '10'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7_Vidamax10.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '11'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7_Vidamax11.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '12'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7_Vidamax12.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		
	}elseif($_GET['portabilidade'] == 'S'){	
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}else{
			//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
		}
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
			$parentescoDep1 = $rowDep1->NOME_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
		$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->NOME_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
			$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->NOME_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
		}

		//Dependente 4
		$codigoDep4 = explode('.',$codAssociadoTmp);
		$codigoDep4 = $codigoDep4[0] . '.4';

		$queryDep4  = ' SELECT ';
		$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep4 .= ' 	PESO, ALTURA ';
		$queryDep4 .= ' FROM VND1000_ON ';
		$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
		$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep4 = jn_query($queryDep4);
		if($rowDep4 = jn_fetch_object($resDep4)){
			$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
			
			$diaNascDep4 = '';
			$mesNascDep4 = '';
			$anoNascDep4 = '';
			
			$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
			$diaNascDep4 = explode(' ', $diaNascDep4);
			$diaNascDep4 = $diaNascDep4[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep4 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep4);
			
			$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
			$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
			$numeroRGDep4 = $rowDep4->NUMERO_RG;
			$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$sexoDep4 = $rowDep4->SEXO;
			$nomeMaeDep4 = $rowDep4->NOME_MAE;
			$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
			$parentescoDep4 = $rowDep4->NOME_PARENTESCO;
			$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
			$pesoDep4 = $rowDep4->PESO;
			$alturaDep4= $rowDep4->ALTURA;
		}

		//Dependente 5
		$codigoDep5 = explode('.',$codAssociadoTmp);
		$codigoDep5 = $codigoDep5[0] . '.5';

		$queryDep5  = ' SELECT ';
		$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep5 .= ' 	PESO, ALTURA ';
		$queryDep5 .= ' FROM VND1000_ON ';
		$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
		$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep5 = jn_query($queryDep5);
		if($rowDep5 = jn_fetch_object($resDep5)){
			$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
			
			$diaNascDep5 = '';
			$mesNascDep5 = '';
			$anoNascDep5 = '';
			
			$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
			$diaNascDep5 = explode(' ', $diaNascDep5);
			$diaNascDep5 = $diaNascDep5[0];
			
			$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
			$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
			$numeroRGDep5 = $rowDep5->NUMERO_RG;
			$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$sexoDep5 = $rowDep5->SEXO;
			$nomeMaeDep5 = $rowDep5->NOME_MAE;
			$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
			$parentescoDep5 = $rowDep5->NOME_PARENTESCO;
			$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
			$pesoDep5 = $rowDep5->PESO;
			$alturaDep5= $rowDep5->ALTURA;
		}	
		
		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7port_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 14, 0, 618, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 14, 0, 212, 1419, $cor,"../../Site/assets/img/arial.ttf",date('d'));		
			imagettftext($imagem, 14, 0, 325, 1419, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));	
			imagettftext($imagem, 10, 0, 130, 1595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}		
			
		
		if($_GET['pagina'] == '2'){
			$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
			$admissao = explode('/',$data);
			$diaAdmissao = $admissao[0];
			$mesAdmissao = $admissao[1];
			$anoAdmissao = $admissao[2];
			
			$colunaAcomodacao == 0;
			if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 480;
			}else{
				$colunaAcomodacao = 285;		
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7port_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 935, 105, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
			imagettftext($imagem, 14, 0, 130, 305, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
			imagettftext($imagem, 10, 0, 620, 275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 820, 275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 980, 365, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 365, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 10, 0, 180, 420, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 170, 465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 330, 465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 465, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 700, 465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 880, 465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 10, 0, 960, 465, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 1120, 465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
			imagettftext($imagem, 10, 0, 160, 515, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 160, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 10, 0, 160, 650, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 160, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 680, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 870, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 1120, 690, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 670, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			imagettftext($imagem, 10, 0, 160, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 390, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			
			
			//Dep1
			imagettftext($imagem, 10, 0, 180, 795, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 180, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 350, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
			imagettftext($imagem, 10, 0, 505, 840, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep1));
			imagettftext($imagem, 10, 0, 660, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
			imagettftext($imagem, 10, 0, 820, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
			imagettftext($imagem, 10, 0, 885, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 10, 0, 970, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 1120, 840, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
			imagettftext($imagem, 10, 0, 170, 885, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
			imagettftext($imagem, 10, 0, 180, 975, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
			imagettftext($imagem, 10, 0, 780, 975, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));
			
			//Dep2
			imagettftext($imagem, 10, 0, 180, 1025, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 180, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
			imagettftext($imagem, 10, 0, 360, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
			imagettftext($imagem, 10, 0, 505, 1065, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep2));
			imagettftext($imagem, 10, 0, 660, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));
			imagettftext($imagem, 10, 0, 820, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));
			imagettftext($imagem, 10, 0, 885, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 10, 0, 970, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 1120, 1065, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
			imagettftext($imagem, 10, 0, 170, 1115, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
			imagettftext($imagem, 10, 0, 180, 1205, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
			imagettftext($imagem, 10, 0, 780, 1205, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));		
			
			//Dep3
			imagettftext($imagem, 10, 0, 180, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 180, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
			imagettftext($imagem, 10, 0, 360, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
			imagettftext($imagem, 10, 0, 505, 1295, $cor,"../../Site/assets/img/arial.ttf",SqlToData($dataEmissaoRGDep3));
			imagettftext($imagem, 10, 0, 660, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
			imagettftext($imagem, 10, 0, 820, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
			imagettftext($imagem, 10, 0, 885, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 10, 0, 970, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 1120, 1295, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
			imagettftext($imagem, 10, 0, 170, 1340, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
			imagettftext($imagem, 10, 0, 180, 1430, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
			imagettftext($imagem, 10, 0, 780, 1430, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));		
			
			imagettftext($imagem, 10, 0, 405, 1542, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 955, 1542, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
			imagettftext($imagem, 10, 0, 180, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 122, 1655, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 10, 0, 320, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}


		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta7port_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 935, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 245, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ' , ' . date('d/m/Y'));
			imagettftext($imagem, 10, 0, 430, 1460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 130, 1515, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7port_Vidamax4.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '5'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7port_Vidamax5.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '6'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta7port_Vidamax6.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);		
		}
	}	

}

if($_GET['modelo'] == 8){
	if(($_GET['portabilidade'] == 'N') or ($_GET['portabilidade'] == '')){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, VND1000_ON.NUMERO_PIS, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, PS1044.NOME_ESTADO_CIVIL, VND1000_ON.NATUREZA_RG, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (VND1000_ON.CODIGO_ESTADO_CIVIL = PS1044.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}else{
			//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
		}
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;
		if ($rowAssociado->CODIGO_PLANO == '401') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
		}
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);


		//Regra OdontoPrev 
		$queryEvento = 'SELECT sum(VALOR_FATOR) AS SOMA_VALOR_FATOR FROM VND1003_ON WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp) . ')';
		$resEvento = jn_query($queryEvento);
		$rowEvento = jn_fetch_object($resEvento);
			
		$valoresEventos = $rowEvento->SOMA_VALOR_FATOR;
		$valorTotal += $valoresEventos;
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, PS1045.NOME_PARENTESCO, PS1044.NOME_ESTADO_CIVIL ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (VND1000_ON.CODIGO_ESTADO_CIVIL = PS1044.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;
			if ($rowDep1->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$sexoDep1 = $rowDep1->SEXO;
			$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$nomeParentescoDep1 = $rowDep1->NOME_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
		$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, PS1045.NOME_PARENTESCO, PS1044.NOME_ESTADO_CIVIL ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (VND1000_ON.CODIGO_ESTADO_CIVIL = PS1044.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;
			if ($rowDep2->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$nomeParentescoDep2 = $rowDep2->NOME_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG, PS1045.NOME_PARENTESCO, PS1044.NOME_ESTADO_CIVIL ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (VND1000_ON.CODIGO_ESTADO_CIVIL = PS1044.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;
			if ($rowDep3->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
		
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
			$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$nomeParentescoDep3 = $rowDep3->NOME_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
		}

		//Dependente 4
		$codigoDep4 = explode('.',$codAssociadoTmp);
		$codigoDep4 = $codigoDep4[0] . '.4';

		$queryDep4  = ' SELECT ';
		$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep4 .= ' 	PESO, ALTURA ';
		$queryDep4 .= ' FROM VND1000_ON ';
		$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
		$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep4 = jn_query($queryDep4);
		if($rowDep4 = jn_fetch_object($resDep4)){
			$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
			
			$diaNascDep4 = '';
			$mesNascDep4 = '';
			$anoNascDep4 = '';
			
			$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
			$diaNascDep4 = explode(' ', $diaNascDep4);
			$diaNascDep4 = $diaNascDep4[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;
			if ($rowDep4->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep4 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep4);
			
			$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
			$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
			$numeroRGDep4 = $rowDep4->NUMERO_RG;
			$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$sexoDep4 = $rowDep4->SEXO;
			$nomeMaeDep4 = $rowDep4->NOME_MAE;
			$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
			$nomeParentescoDep4 = $rowDep4->NOME_PARENTESCO;
			$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
			$pesoDep4 = $rowDep4->PESO;
			$alturaDep4= $rowDep4->ALTURA;
		}

		//Dependente 5
		$codigoDep5 = explode('.',$codAssociadoTmp);
		$codigoDep5 = $codigoDep5[0] . '.5';

		$queryDep5  = ' SELECT ';
		$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep5 .= ' 	PESO, ALTURA ';
		$queryDep5 .= ' FROM VND1000_ON ';
		$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
		$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep5 = jn_query($queryDep5);
		if($rowDep5 = jn_fetch_object($resDep5)){
			$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
			
			$diaNascDep5 = '';
			$mesNascDep5 = '';
			$anoNascDep5 = '';
			
			$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
			$diaNascDep5 = explode(' ', $diaNascDep5);
			$diaNascDep5 = $diaNascDep5[0];
			
			$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
			$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
			$numeroRGDep5 = $rowDep5->NUMERO_RG;
			$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$sexoDep5 = $rowDep5->SEXO;
			$nomeMaeDep5 = $rowDep5->NOME_MAE;
			$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
			$parentescoDep5 = $rowDep5->NOME_PARENTESCO;
			$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
			$pesoDep5 = $rowDep5->PESO;
			$alturaDep5= $rowDep5->ALTURA;
		}	
		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
			imagettftext($imagem, 14, 0, 1000, 110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 15, 0, 625, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '2'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
			$admissao = explode('/',$data);
			$diaAdmissao = $admissao[0];
			$mesAdmissao = $admissao[1];
			$anoAdmissao = $admissao[2];

			$linhaPlano == 0;

			if($rowAssociado->CODIGO_PLANO == 401){
				$linhaPlano = 347; 
			}elseif($rowAssociado->CODIGO_PLANO == 400){
				$linhaPlano = 377; 
			}elseif($rowAssociado->CODIGO_PLANO == 408){
				$linhaPlano = 408; 
			}elseif($rowAssociado->CODIGO_PLANO == 409){
				$linhaPlano = 438; 
			}else{
				$linhaPlano = 0;
			}
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
			imagettftext($imagem, 14, 0, 160, 290, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));	
			imagettftext($imagem, 14, 0, 400, 482, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));	
			imagettftext($imagem, 14, 0, 315, $linhaPlano, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X')); 	
			imagettftext($imagem, 10, 0, 137, 537, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 177, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 310, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 470, 577, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 695, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 790, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 880, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 10, 0, 960, 577, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 1120, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
			imagettftext($imagem, 10, 0, 160, 627, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 160, 667, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PAI));
			imagettftext($imagem, 10, 0, 160, 717, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 10, 0, 160, 757, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 160, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 680, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 870, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 1110, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 670, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			imagettftext($imagem, 10, 0, 160, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 390, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			
			
			//Dep1
			imagettftext($imagem, 10, 0, 180, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 970, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep1));
			imagettftext($imagem, 10, 0, 180, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 350, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
			imagettftext($imagem, 10, 0, 505, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep1));
			imagettftext($imagem, 10, 0, 660, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
			imagettftext($imagem, 8, 0, 720, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep1));
			imagettftext($imagem, 10, 0, 800, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
			imagettftext($imagem, 10, 0, 885, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 10, 0, 970, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 1120, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
			imagettftext($imagem, 10, 0, 180, 1000, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));	
			imagettftext($imagem, 10, 0, 180, 1045, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep1));	
			imagettftext($imagem, 10, 0, 200, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));		
			imagettftext($imagem, 10, 0, 780, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));	
			
			
			
			//Dep2
			imagettftext($imagem, 10, 0, 180, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 970, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep2));		
			imagettftext($imagem, 10, 0, 180, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));		
			imagettftext($imagem, 10, 0, 330, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));		
			imagettftext($imagem, 10, 0, 490, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep2));			
			imagettftext($imagem, 10, 0, 650, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));
			imagettftext($imagem, 8, 0, 720, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep2));		
			imagettftext($imagem, 10, 0, 800, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));		
			imagettftext($imagem, 10, 0, 885, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 10, 0, 970, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 1120, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
			imagettftext($imagem, 10, 0, 180, 1227, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));		
			imagettftext($imagem, 10, 0, 180, 1317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));	
			imagettftext($imagem, 10, 0, 780, 1317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));			
			imagettftext($imagem, 10, 0, 180, 1272, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep2));		
			
			
			//Dep3
			imagettftext($imagem, 10, 0, 180, 1360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 970, 1360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep3));		
			imagettftext($imagem, 10, 0, 180, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));	
			imagettftext($imagem, 10, 0, 360, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
			imagettftext($imagem, 10, 0, 505, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep3));
			imagettftext($imagem, 10, 0, 660, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
			imagettftext($imagem, 8, 0, 720, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep3));
			imagettftext($imagem, 10, 0, 800, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
			imagettftext($imagem, 10, 0, 885, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 10, 0, 970, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 1120, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
			imagettftext($imagem, 10, 0, 180, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));		
			imagettftext($imagem, 10, 0, 180, 1545, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
			imagettftext($imagem, 10, 0, 780, 1545, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));	
			imagettftext($imagem, 10, 0, 180, 1500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep3));			
			
			imagettftext($imagem, 10, 0, 122, 1615, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 10, 0, 320, 1615, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}


		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 240, 1595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ',  ' . date('d/m/Y'));
			imagettftext($imagem, 14, 0, 480, 1650, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '7'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 130, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 14, 0, 983, 1334, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->VALOR_TAXA_ADESAO));
			imagettftext($imagem, 12, 0, 130, 1378, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(valorPorExtenso(str_replace(',','.',$rowAssociado->VALOR_TAXA_ADESAO))));	
			imagettftext($imagem, 10, 0, 380, 1490, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 416, 1555, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 10, 0, 370, 1610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '8'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8_Vidamax8.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '9'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8_Vidamax9.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '10'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8_Vidamax10.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '11'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8_Vidamax11.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}

		

		if($_GET['pagina'] == '12'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax12.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			//Titular 
			
			imagettftext($imagem, 12, 0, 820, 242, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valoresEventos));
			imagettftext($imagem, 10, 0, 230, 332, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 200, 359, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 440, 359, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 830, 359, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 1020, 359, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 290, 389, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 200, 415, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 170, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 465, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 980, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 1220, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 150, 500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_PIS));
			imagettftext($imagem, 10, 0, 475, 502, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 1020, 502, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 275, 558, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
			imagettftext($imagem, 10, 0, 465, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));


			//Dependente 1 
			imagettftext($imagem, 10, 0, 250, 633, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 250, 680, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
			imagettftext($imagem, 14, 0, 660, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 830, 633, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 830, 673, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
			

			if($parentescoDep1 == 1){
				imagettftext($imagem, 12, 0, 1012, 653, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep1 == 3){
				imagettftext($imagem, 12, 0, 1115, 650, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep1 == 9){
				imagettftext($imagem, 12, 0, 1012, 678, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 674, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep1));
			}

			if($sexoDep1 == 'M'){
				imagettftext($imagem, 12, 0, 1215, 652, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep1 == 'F'){
				imagettftext($imagem, 12, 0, 1215, 669, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}

			//Dependente 2 
			imagettftext($imagem, 10, 0, 250, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 250, 755, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
			imagettftext($imagem, 14, 0, 660, 743, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 830, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
			imagettftext($imagem, 10, 0, 830, 750, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));

			if($parentescoDep2 == 1){
				imagettftext($imagem, 12, 0, 1012, 728, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep2 == 3){
				imagettftext($imagem, 12, 0, 1115, 727, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep2 == 9){
				imagettftext($imagem, 12, 0, 1012, 752, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 747, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep2));
			}

			if($sexoDep2 == 'M'){
				imagettftext($imagem, 12, 0, 1214, 724, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep2 == 'F'){
				imagettftext($imagem, 12, 0, 1214, 744, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			
			//Dependente 3
			imagettftext($imagem, 10, 0, 250, 787, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 250, 829, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
			imagettftext($imagem, 14, 0, 660, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 830, 785, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
			imagettftext($imagem, 10, 0, 830, 824, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));

			if($parentescoDep3 == 1){
				imagettftext($imagem, 12, 0, 1012, 798, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep3 == 3){
				imagettftext($imagem, 12, 0, 1115, 798, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep3 == 9){
				imagettftext($imagem, 12, 0, 1012, 824, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 819, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep3));
			}

			if($sexoDep3 == 'M'){
				imagettftext($imagem, 12, 0, 1214, 800, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep3 == 'F'){
				imagettftext($imagem, 12, 0, 1214, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}	

			//Dependente 4
			imagettftext($imagem, 10, 0, 250, 858, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
			imagettftext($imagem, 10, 0, 250, 900, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
			imagettftext($imagem, 14, 0, 660, 889, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep4));
			imagettftext($imagem, 10, 0, 830, 859, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
			imagettftext($imagem, 10, 0, 830, 897, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));

			if($parentescoDep4 == 1){
				imagettftext($imagem, 12, 0, 1012, 872, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep4 == 3){
				imagettftext($imagem, 12, 0, 1115, 872, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep4 == 9){
				imagettftext($imagem, 12, 0, 1012, 898, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 896, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep4));
			}

			if($sexoDep4 == 'M'){
				imagettftext($imagem, 12, 0, 1214, 874, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep4 == 'F'){
				imagettftext($imagem, 12, 0, 1214, 892, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}	
			

			$image_p = imagecreatetruecolor(1379, 1013);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1379, 1013, 1379, 1013);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '13'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8_Vidamax13.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 14, 0, 235, 812, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 14, 0, 395, 812, $cor,"../../Site/assets/img/arial.ttf",date('d'));
			imagettftext($imagem, 14, 0, 715, 812, $cor,"../../Site/assets/img/arial.ttf",date('Y'));	
			imagettftext($imagem, 14, 0, 490, 812, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));	
			imagettftext($imagem, 14, 0, 180, 855, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
			$image_p = imagecreatetruecolor(1379, 1013);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1379, 1013, 1379, 1013);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}


	}elseif($_GET['portabilidade'] == 'S'){	
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}else{
			//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
		}
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;
		if ($rowAssociado->CODIGO_PLANO == '401') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
		}		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);

		//Regra OdontoPrev 
		$queryEvento = 'SELECT sum(VALOR_FATOR) AS SOMA_VALOR_FATOR FROM VND1003_ON WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp) . ')';
		$resEvento = jn_query($queryEvento);
		$rowEvento = jn_fetch_object($resEvento);
			
		$valoresEventos = $rowEvento->SOMA_VALOR_FATOR;
		$valorTotal += $valoresEventos;
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, PS1045.NOME_PARENTESCO ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;
			if ($rowDep1->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$nomeParentescoDep1 = $rowDep1->NOME_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
		$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, PS1045.NOME_PARENTESCO ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;
			if ($rowDep2->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$nomeParentescoDep2 = $rowDep2->NOME_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, PS1045.NOME_PARENTESCO ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;
			if ($rowDep3->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
			$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$nomeParentescoDep3 = $rowDep3->NOME_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
		}

		//Dependente 4
		$codigoDep4 = explode('.',$codAssociadoTmp);
		$codigoDep4 = $codigoDep4[0] . '.4';

		$queryDep4  = ' SELECT ';
		$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep4 .= ' 	PESO, ALTURA, PS1045.NOME_PARENTESCO ';
		$queryDep4 .= ' FROM VND1000_ON ';
		$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
		$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep4 = jn_query($queryDep4);
		if($rowDep4 = jn_fetch_object($resDep4)){
			$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
			
			$diaNascDep4 = '';
			$mesNascDep4 = '';
			$anoNascDep4 = '';
			
			$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
			$diaNascDep4 = explode(' ', $diaNascDep4);
			$diaNascDep4 = $diaNascDep4[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;
			if ($rowDep4->CODIGO_PLANO == '401') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('1');
			}		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep4 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep4);
			
			$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
			$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
			$numeroRGDep4 = $rowDep4->NUMERO_RG;
			$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
			$sexoDep4 = $rowDep4->SEXO;
			$nomeMaeDep4 = $rowDep4->NOME_MAE;
			$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
			$parentescoDep4 = $rowDep4->NOME_PARENTESCO;
			$nomeParentescoDep4 = $rowDep4->NOME_PARENTESCO;
			$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
			$pesoDep4 = $rowDep4->PESO;
			$alturaDep4= $rowDep4->ALTURA;
		}

		//Dependente 5
		$codigoDep5 = explode('.',$codAssociadoTmp);
		$codigoDep5 = $codigoDep5[0] . '.5';

		$queryDep5  = ' SELECT ';
		$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
		$queryDep5 .= ' 	PESO, ALTURA, PS1045.NOME_PARENTESCO ';
		$queryDep5 .= ' FROM VND1000_ON ';
		$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
		$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep5 = jn_query($queryDep5);
		if($rowDep5 = jn_fetch_object($resDep5)){
			$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
			
			$diaNascDep5 = '';
			$mesNascDep5 = '';
			$anoNascDep5 = '';
			
			$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
			$diaNascDep5 = explode(' ', $diaNascDep5);
			$diaNascDep5 = $diaNascDep5[0];
			
			$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
			$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
			$numeroRGDep5 = $rowDep5->NUMERO_RG;
			$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
			$sexoDep5 = $rowDep5->SEXO;
			$nomeMaeDep5 = $rowDep5->NOME_MAE;
			$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
			$parentescoDep5 = $rowDep5->NOME_PARENTESCO;
			$nomeParentescoDep5 = $rowDep5->NOME_PARENTESCO;
			$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
			$pesoDep5 = $rowDep5->PESO;
			$alturaDep5= $rowDep5->ALTURA;
		}	
		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
	
			imagettftext($imagem, 14, 0, 1000, 105, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 15, 0, 625, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );

		}
		
		if($_GET['pagina'] == '2'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
			$admissao = explode('/',$data);
			$diaAdmissao = $admissao[0];
			$mesAdmissao = $admissao[1];
			$anoAdmissao = $admissao[2];

			$linhaPlano == 0;

			if($rowAssociado->CODIGO_PLANO == 401){
				$linhaPlano = 347; 
			}elseif($rowAssociado->CODIGO_PLANO == 400){
				$linhaPlano = 377; 
			}elseif($rowAssociado->CODIGO_PLANO == 408){
				$linhaPlano = 408; 
			}elseif($rowAssociado->CODIGO_PLANO == 409){
				$linhaPlano = 438; 
			}else{
				$linhaPlano = 0;
			}
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
			imagettftext($imagem, 14, 0, 160, 290, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));	
			imagettftext($imagem, 14, 0, 400, 482, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));	
			imagettftext($imagem, 14, 0, 305, $linhaPlano, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X')); 	
			imagettftext($imagem, 10, 0, 137, 537, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 177, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 310, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 470, 577, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 695, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 810, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 880, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 10, 0, 960, 577, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 1120, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
			imagettftext($imagem, 10, 0, 160, 627, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 160, 667, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PAI));
			imagettftext($imagem, 10, 0, 160, 717, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 10, 0, 160, 757, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 160, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 680, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 870, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 1110, 807, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 670, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			imagettftext($imagem, 10, 0, 160, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 390, 852, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			
			
			//Dep1
			imagettftext($imagem, 10, 0, 180, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 970, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep1));
			imagettftext($imagem, 10, 0, 180, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 350, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
			imagettftext($imagem, 10, 0, 505, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep1));
			imagettftext($imagem, 10, 0, 660, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
			imagettftext($imagem, 8, 0, 720, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep1));
			imagettftext($imagem, 10, 0, 820, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
			imagettftext($imagem, 10, 0, 885, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 10, 0, 970, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 1120, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
			imagettftext($imagem, 10, 0, 180, 1000, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));	
			imagettftext($imagem, 10, 0, 180, 1045, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep1));	
			imagettftext($imagem, 10, 0, 200, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));		
			imagettftext($imagem, 10, 0, 780, 1090, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));	
			
			
			
			//Dep2
			imagettftext($imagem, 10, 0, 180, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 970, 1135, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep2));		
			imagettftext($imagem, 10, 0, 180, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));		
			imagettftext($imagem, 10, 0, 330, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));		
			imagettftext($imagem, 10, 0, 490, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep2));			
			imagettftext($imagem, 10, 0, 650, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));
			imagettftext($imagem, 8, 0, 720, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep2));		
			imagettftext($imagem, 10, 0, 820, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));		
			imagettftext($imagem, 10, 0, 885, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 10, 0, 970, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 1120, 1177, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
			imagettftext($imagem, 10, 0, 180, 1227, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));		
			imagettftext($imagem, 10, 0, 180, 1317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));	
			imagettftext($imagem, 10, 0, 780, 1317, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));			
			imagettftext($imagem, 10, 0, 180, 1272, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep2));		
			
			
			//Dep3
			imagettftext($imagem, 10, 0, 180, 1360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 970, 1360, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep3));		
			imagettftext($imagem, 10, 0, 180, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));	
			imagettftext($imagem, 10, 0, 360, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
			imagettftext($imagem, 10, 0, 505, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep3));
			imagettftext($imagem, 10, 0, 660, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
			imagettftext($imagem, 8, 0, 720, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep3));
			imagettftext($imagem, 10, 0, 820, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
			imagettftext($imagem, 10, 0, 885, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 10, 0, 970, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 1120, 1405, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
			imagettftext($imagem, 10, 0, 180, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));		
			imagettftext($imagem, 10, 0, 180, 1545, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
			imagettftext($imagem, 10, 0, 780, 1545, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));	
			imagettftext($imagem, 10, 0, 180, 1500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomePaiDep3));			
			
			imagettftext($imagem, 10, 0, 122, 1615, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 10, 0, 320, 1615, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}


		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 240, 1208, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ',  ' . date('d/m/Y'));
			imagettftext($imagem, 14, 0, 500, 1262, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '7'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8port_Vidamax7.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '8'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8port_Vidamax8.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '9'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8port_Vidamax9.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}
		
		if($_GET['pagina'] == '10'){
			$img = imagecreatefromjpeg('../../Site/assets/img/proposta8port_Vidamax10.jpg');
			header( "Content-type: image/jpeg" );
			return imagejpeg( $img, NULL);	
		}

		if($_GET['pagina'] == '11'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax11.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			//Titular 
			
			imagettftext($imagem, 12, 0, 820, 242, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valoresEventos));
			imagettftext($imagem, 10, 0, 230, 332, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 200, 359, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 10, 0, 440, 359, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 830, 359, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 1020, 359, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 10, 0, 290, 389, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 10, 0, 200, 415, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 10, 0, 170, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 10, 0, 465, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 10, 0, 980, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 10, 0, 1220, 445, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 10, 0, 150, 500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_PIS));
			imagettftext($imagem, 10, 0, 475, 502, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 10, 0, 1020, 502, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 10, 0, 275, 558, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
			imagettftext($imagem, 10, 0, 465, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));


			//Dependente 1 
			imagettftext($imagem, 10, 0, 250, 633, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 250, 680, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
			imagettftext($imagem, 14, 0, 660, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
			imagettftext($imagem, 10, 0, 830, 633, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
			imagettftext($imagem, 10, 0, 830, 673, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
			

			if($parentescoDep1 == 1){
				imagettftext($imagem, 12, 0, 1012, 653, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep1 == 3){
				imagettftext($imagem, 12, 0, 1115, 650, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep1 == 9){
				imagettftext($imagem, 12, 0, 1012, 678, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 674, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep1));
			}

			if($sexoDep1 == 'M'){
				imagettftext($imagem, 12, 0, 1215, 652, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep1 == 'F'){
				imagettftext($imagem, 12, 0, 1215, 669, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}

			//Dependente 2 
			imagettftext($imagem, 10, 0, 250, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 250, 755, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
			imagettftext($imagem, 14, 0, 660, 743, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
			imagettftext($imagem, 10, 0, 830, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
			imagettftext($imagem, 10, 0, 830, 750, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));

			if($parentescoDep2 == 1){
				imagettftext($imagem, 12, 0, 1012, 728, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep2 == 3){
				imagettftext($imagem, 12, 0, 1115, 727, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep2 == 9){
				imagettftext($imagem, 12, 0, 1012, 752, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 747, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep2));
			}

			if($sexoDep2 == 'M'){
				imagettftext($imagem, 12, 0, 1214, 724, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep2 == 'F'){
				imagettftext($imagem, 12, 0, 1214, 744, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			
			//Dependente 3
			imagettftext($imagem, 10, 0, 250, 787, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 250, 829, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
			imagettftext($imagem, 14, 0, 660, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
			imagettftext($imagem, 10, 0, 830, 785, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
			imagettftext($imagem, 10, 0, 830, 824, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));

			if($parentescoDep3 == 1){
				imagettftext($imagem, 12, 0, 1012, 798, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep3 == 3){
				imagettftext($imagem, 12, 0, 1115, 798, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep3 == 9){
				imagettftext($imagem, 12, 0, 1012, 824, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 819, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep3));
			}

			if($sexoDep3 == 'M'){
				imagettftext($imagem, 12, 0, 1214, 800, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep3 == 'F'){
				imagettftext($imagem, 12, 0, 1214, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}	

			//Dependente 4
			imagettftext($imagem, 10, 0, 250, 858, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
			imagettftext($imagem, 10, 0, 250, 900, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
			imagettftext($imagem, 14, 0, 660, 889, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep4));
			imagettftext($imagem, 10, 0, 830, 859, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
			imagettftext($imagem, 10, 0, 830, 897, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));

			if($parentescoDep4 == 1){
				imagettftext($imagem, 12, 0, 1012, 872, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep4 == 3){
				imagettftext($imagem, 12, 0, 1115, 872, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			elseif($parentescoDep4 == 9){
				imagettftext($imagem, 12, 0, 1012, 898, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));	
			}
			else{
				imagettftext($imagem, 10, 0, 1112, 896, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeParentescoDep4));
			}

			if($sexoDep4 == 'M'){
				imagettftext($imagem, 12, 0, 1214, 874, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}
			elseif($sexoDep4 == 'F'){
				imagettftext($imagem, 12, 0, 1214, 892, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			}	
			

			$image_p = imagecreatetruecolor(1379, 1013);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1379, 1013, 1379, 1013);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '12'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta8port_Vidamax12.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 14, 0, 235, 812, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 14, 0, 395, 812, $cor,"../../Site/assets/img/arial.ttf",date('d'));
			imagettftext($imagem, 14, 0, 715, 812, $cor,"../../Site/assets/img/arial.ttf",date('Y'));	
			imagettftext($imagem, 14, 0, 490, 812, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));	
			imagettftext($imagem, 14, 0, 180, 855, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
			$image_p = imagecreatetruecolor(1379, 1013);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1379, 1013, 1379, 1013);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
	}
}

if($_GET['modelo'] == 9){
	if(($_GET['portabilidade'] == 'N') or ($_GET['portabilidade'] == '')){
		
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, VND1000_ON.NUMERO_DECLARACAO_NASC_VIVO,  ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}
		
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;

		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);

		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
			
		}	

		
		if($_GET['pagina'] == '1'){
			
			$colunaAcomodacao  = 0;

			if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 480;
			}else{
				$colunaAcomodacao = 285;		
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 130, 285, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 12, 0, 980, 340, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 340, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 12, 0, 130, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 12, 0, 130, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 320, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 435, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 700, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 12, 0, 880, 435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 955, 435, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 1115, 435, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 130, 480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 12, 0, 130, 570, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 12, 0, 600, 570, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
			imagettftext($imagem, 12, 0, 130, 620, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 12, 0, 130, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 12, 0, 640, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 12, 0, 870, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 1100, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 12, 0, 130, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 12, 0, 410, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			imagettftext($imagem, 12, 0, 640, 710, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			
			//Dependente 1
			imagettftext($imagem, 12, 0, 170, 767, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 1124, 767, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
			imagettftext($imagem, 12, 0, 170, 810, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 12, 0, 330, 810, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 12, 0, 485, 810, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep1);
			imagettftext($imagem, 12, 0, 620, 810, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep1);
			imagettftext($imagem, 12, 0, 720, 810, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep1);
			imagettftext($imagem, 11, 0, 795, 810, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 12, 0, 890, 810, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 12, 0, 970, 810, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
			imagettftext($imagem, 12, 0, 1124, 810, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
			imagettftext($imagem, 12, 0, 170, 860, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 12, 0, 170, 950, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 12, 0, 780, 950, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
			
			
			//Dependente 2
			if(isset($nomeDep2)){
				imagettftext($imagem, 12, 0, 170, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
				imagettftext($imagem, 10, 0, 1124, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
				imagettftext($imagem, 12, 0, 170, 1040, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
				imagettftext($imagem, 12, 0, 330, 1040, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
				imagettftext($imagem, 12, 0, 485, 1040, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep2);
				imagettftext($imagem, 12, 0, 620, 1040, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep2);
				imagettftext($imagem, 12, 0, 720, 1040, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep2);
				imagettftext($imagem, 11, 0, 795, 1040, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep2);
				imagettftext($imagem, 12, 0, 890, 1040, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
				imagettftext($imagem, 12, 0, 970, 1040, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
				imagettftext($imagem, 12, 0, 1124, 1040, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
				imagettftext($imagem, 12, 0, 170, 1080, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
				imagettftext($imagem, 12, 0, 170, 1175, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
				imagettftext($imagem, 12, 0, 780, 1175, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			}
			
			//Dependente 3
			if(isset($nomeDep3)){
				imagettftext($imagem, 12, 0, 170, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
				imagettftext($imagem, 10, 0, 1124, 1220, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
				imagettftext($imagem, 12, 0, 170, 1270, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
				imagettftext($imagem, 12, 0, 330, 1270, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
				imagettftext($imagem, 12, 0, 485, 1270, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep3);
				imagettftext($imagem, 12, 0, 620, 1270, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep3);
				imagettftext($imagem, 12, 0, 720, 1270, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep3);
				imagettftext($imagem, 11, 0, 795, 1270, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep3);
				imagettftext($imagem, 12, 0, 890, 1270, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
				imagettftext($imagem, 12, 0, 970, 1270, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
				imagettftext($imagem, 12, 0, 1124, 1270, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
				imagettftext($imagem, 12, 0, 170, 1310, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
				imagettftext($imagem, 12, 0, 170, 1400, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
				imagettftext($imagem, 12, 0, 780, 1400, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			}

			imagettftext($imagem, 12, 0, 420, 1555, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 12, 0, 940, 1555, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->CODIGO_VENDEDOR);
			imagettftext($imagem, 12, 0, 120, 1685, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 330, 1685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}


		if($_GET['pagina'] == '2'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 12, 0, 110, 1525, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 400, 1525, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 680, 1525, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 990, 1525, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));	
			
			imagettftext($imagem, 12, 0, 150, 1615, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			imagettftext($imagem, 12, 0, 195, 1675, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			
			imagettftext($imagem, 12, 0, 740, 1615, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			imagettftext($imagem, 12, 0, 790, 1675, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);			
			header("Content-Type: image/jpeg");
			
			return imagejpeg( $image_p, NULL, 80 );

		}
		
		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 128, 815, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('x'));
			imagettftext($imagem, 12, 0, 200, 1480, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 700, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
			$pesoTit = $rowAssociado->PESO;
			$alturaTit = str_replace(',','.',$rowAssociado->ALTURA);
			$alturaDep1 = str_replace(',','.',$rowDep1->ALTURA);
			$alturaDep2 = str_replace(',','.',$rowDep2->ALTURA);
			$alturaDep3 = str_replace(',','.',$rowDep3->ALTURA);
			$alturaDep4 = str_replace(',','.',$rowDep4->ALTURA);
			
			
			if($rowAssociado->PESO){		           	
				$imcTitular = ($rowAssociado->PESO / ($alturaTit * $alturaTit));
				$imcTitular = ($imcTitular * 10000);
				$imcTitular = number_format($imcTitular,1);				
			}

			
			if($pesoDep1){			
				$imcDep1 = ($pesoDep1 / ($alturaDep1 * $alturaDep1));
				$imcDep1 = ($imcDep1 * 10000);
				$imcDep1 = number_format($imcDep1,1);								
			}
			
			if($pesoDep2){			
				$imcDep2 = ($pesoDep2 / ($alturaDep2 * $alturaDep2));
				$imcDep2 = ($imcDep2 * 10000);
				$imcDep2 = number_format($imcDep2,1);
			}
			
			if($pesoDep3){			
				$imcDep3 = ($pesoDep3 / ($alturaDep3 * $alturaDep3));
				$imcDep3 = ($imcDep3 * 10000);
				$imcDep3 = number_format($imcDep3,1);
			}
			
			if($pesoDep4){			
				$imcDep4 = ($pesoDep4 / ($alturaDep4 * $alturaDep4));
				$imcDep4 = ($imcDep4 * 10000);
				$imcDep4 = number_format($imcDep4,1);
			}

			$queryDecTit  = ' SELECT ';
			$queryDecTit .= '	VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
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
					$coluna = 885;
				}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep1){
					$coluna = 950;
				}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep2){
					$coluna = 1000;
				}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep3){
					$coluna = 1070;
				}elseif($rowDecTit->CODIGO_ASSOCIADO == $codigoDep4){
					$coluna = 1125;
				}
				
				if($rowDecTit->NUMERO_PERGUNTA == '1'){
					imagettftext($imagem, 14, 0, $coluna, 330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '2'){
					imagettftext($imagem, 14, 0, $coluna, 370, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '3'){
					imagettftext($imagem, 14, 0, $coluna, 425, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '4'){
					imagettftext($imagem, 14, 0, $coluna, 480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '5'){
					imagettftext($imagem, 14, 0, $coluna, 540, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '6'){
					imagettftext($imagem, 14, 0, $coluna, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '7'){
					imagettftext($imagem, 14, 0, $coluna, 665, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '8'){
					imagettftext($imagem, 14, 0, $coluna, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '9'){
					imagettftext($imagem, 14, 0, $coluna, 820, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '10'){
					imagettftext($imagem, 14, 0, $coluna, 880, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '11'){
					imagettftext($imagem, 14, 0, $coluna, 950, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '12'){
					imagettftext($imagem, 14, 0, $coluna, 1010, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '13'){
					imagettftext($imagem, 14, 0, $coluna, 1070, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}elseif($rowDecTit->NUMERO_PERGUNTA == '14'){
					imagettftext($imagem, 14, 0, $coluna, 1120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowDecTit->RESPOSTA_DIGITADA));	
				}			
			}
			
			imagettftext($imagem, 14, 0, 285, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 14, 0, 285, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoTit));
			imagettftext($imagem, 14, 0, 285, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaTit));
			imagettftext($imagem, 14, 0, 285, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcTitular));
			
			imagettftext($imagem, 14, 0, 390, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 14, 0, 390, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep1));
			imagettftext($imagem, 14, 0, 390, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep1));
			imagettftext($imagem, 14, 0, 390, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep1));
			
			imagettftext($imagem, 14, 0, 510, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 14, 0, 510, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep2));
			imagettftext($imagem, 14, 0, 510, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep2));
			imagettftext($imagem, 14, 0, 510, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep2));
			
			imagettftext($imagem, 14, 0, 640, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 14, 0, 640, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep3));
			imagettftext($imagem, 14, 0, 640, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep3));
			imagettftext($imagem, 14, 0, 640, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep3));		
			
			imagettftext($imagem, 14, 0, 770, 1210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
			imagettftext($imagem, 14, 0, 770, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($pesoDep4));
			imagettftext($imagem, 14, 0, 770, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($alturaDep4));
			imagettftext($imagem, 14, 0, 770, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($imcDep4));		
			
			imagettftext($imagem, 14, 0, 980, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			
			$queryObservacoes  = ' 	SELECT  ';	
			$queryObservacoes .= ' 		NUMERO_PERGUNTA, CODIGO_ASSOCIADO, DESCRICAO_OBSERVACAO, ';
			$queryObservacoes .= ' 		CASE ';
			$queryObservacoes .= ' 			WHEN VND1005_ON.CODIGO_ASSOCIADO = ' . aspas($_GET['codAssociado']);
			$queryObservacoes .= ' 				THEN "TIT."  ';
			$queryObservacoes .= ' 			ELSE "DEP."  ';
			$queryObservacoes .= ' 		END  AS TIPO_ASSOCIADO';
			$queryObservacoes .= ' 	FROM VND1005_ON  ';
			$queryObservacoes .= ' 	WHERE CODIGO_ASSOCIADO IN ( ';
			$queryObservacoes .= ' 		SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($_GET['codAssociado']);	
			$queryObservacoes .= ' 	) ';	
			
			$resObservacoes = jn_query($queryObservacoes);
			
			$GridObservacoes = Array();
			$i = 0;
			
			while($rowObservacoes = jn_fetch_object($resObservacoes)){		
				$GridObservacoes[$i]['NUMERO_PERGUNTA'] = $rowObservacoes->NUMERO_PERGUNTA;
				$GridObservacoes[$i]['CODIGO_ASSOCIADO'] = $rowObservacoes->CODIGO_ASSOCIADO;
				$GridObservacoes[$i]['DESCRICAO_OBSERVACAO'] = jn_utf8_encode($rowObservacoes->DESCRICAO_OBSERVACAO);
				$GridObservacoes[$i]['TIPO_ASSOCIADO'] = jn_utf8_encode($rowObservacoes->TIPO_ASSOCIADO);
				$i++;
			}
					
			imagettftext($imagem, 12, 0, 110, 1435, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['NUMERO_PERGUNTA']);
			imagettftext($imagem, 12, 0, 200, 1435, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['TIPO_ASSOCIADO']);
			imagettftext($imagem, 12, 0, 500, 1435, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[0]['DESCRICAO_OBSERVACAO']);
			imagettftext($imagem, 12, 0, 110, 1470,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['NUMERO_PERGUNTA']);
			imagettftext($imagem, 12, 0, 200, 1470, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['TIPO_ASSOCIADO']);
			imagettftext($imagem, 12, 0, 500, 1470, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[1]['DESCRICAO_OBSERVACAO']);
			imagettftext($imagem, 12, 0, 110, 1510, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['NUMERO_PERGUNTA']);
			imagettftext($imagem, 12, 0, 200, 1510, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['TIPO_ASSOCIADO']);
			imagettftext($imagem, 12, 0, 500, 1510, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[2]['DESCRICAO_OBSERVACAO']);
			imagettftext($imagem, 12, 0, 110, 1540,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['NUMERO_PERGUNTA']);
			imagettftext($imagem, 12, 0, 200, 1540, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['TIPO_ASSOCIADO']);
			imagettftext($imagem, 12, 0, 500, 1540, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[3]['DESCRICAO_OBSERVACAO']);
			imagettftext($imagem, 12, 0, 110, 1575,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['NUMERO_PERGUNTA']);
			imagettftext($imagem, 12, 0, 200, 1575, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['TIPO_ASSOCIADO']);
			imagettftext($imagem, 12, 0, 500, 1575, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[4]['DESCRICAO_OBSERVACAO']);
			imagettftext($imagem, 12, 0, 110, 1610,  $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['NUMERO_PERGUNTA']);
			imagettftext($imagem, 12, 0, 200, 1610, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['TIPO_ASSOCIADO']);
			imagettftext($imagem, 12, 0, 500, 1610, $cor,"../../Site/assets/img/arial.ttf",$GridObservacoes[5]['DESCRICAO_OBSERVACAO']);
			
			
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	
		
		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 950, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 315, 785, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			imagettftext($imagem, 12, 0, 765, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 950, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '7'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 950, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			imagettftext($imagem, 12, 0, 265, 1330, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 12, 0, 345, 1395, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 295, 1465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			imagettftext($imagem, 12, 0, 760, 1570, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '8'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax8.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 950, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
			imagettftext($imagem, 12, 0, 265, 1435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ',' . date('d/m/Y'));					
			imagettftext($imagem, 12, 0, 860, 1435, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 295, 1495, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			imagettftext($imagem, 12, 0, 760, 1580, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '9'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax9.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 950, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 150, 560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));					
			imagettftext($imagem, 12, 0, 990, 1370, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->VALOR_TAXA_ADESAO));					
			imagettftext($imagem, 12, 0, 430, 1530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));					
			imagettftext($imagem, 12, 0, 375, 1590, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));					
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '10'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax10.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 950, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 260, 1213, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 340, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 130, 1374, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '11'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax11.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');

			imagettftext($imagem, 14, 0, 950, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			
			imagettftext($imagem, 12, 0, 510, 325, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));	
			imagettftext($imagem, 12, 0, 120, 1380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 370, 1380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(date('d')));					
			imagettftext($imagem, 14, 0, 490, 1380, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));		
			imagettftext($imagem, 12, 0, 700, 1380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(date('Y')));					
			imagettftext($imagem, 12, 0, 120, 1560, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '12'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax12.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '13'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax13.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
					
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		if($_GET['pagina'] == '14'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax14.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}		
		
		if($_GET['pagina'] == '15'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax15.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	
		
		if($_GET['pagina'] == '16'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax16.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '17'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax17.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	
		
		if($_GET['pagina'] == '18'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta9_Vidamax18.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
	}elseif($_GET['portabilidade'] == 'S'){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}
		
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;

		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);

		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
			
		}	

		if($_GET['pagina'] == '1'){
			
			$colunaAcomodacao == 0;
			if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 480;
			}else{
				$colunaAcomodacao = 287;		
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 165, 310, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 12, 0, 980, 365, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 366, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 12, 0, 130, 425, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 12, 0, 130, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 320, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 468, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 725, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 12, 0, 895, 468, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 965, 468, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 1110, 468, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 130, 515, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 12, 0, 130, 605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 12, 0, 600, 600, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
			imagettftext($imagem, 12, 0, 130, 650, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 12, 0, 130, 695, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 12, 0, 640, 695, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 12, 0, 870, 695, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 1100, 695, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 12, 0, 130, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 12, 0, 410, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			imagettftext($imagem, 12, 0, 640, 740, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			
			//Dependente 1
			imagettftext($imagem, 12, 0, 170, 797, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 1124, 797, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
			imagettftext($imagem, 12, 0, 170, 842, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 12, 0, 330, 842, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 12, 0, 497, 842, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep1);
			imagettftext($imagem, 12, 0, 645, 842, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep1);
			imagettftext($imagem, 12, 0, 735, 842, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep1);
			imagettftext($imagem, 11, 0, 795, 842, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 12, 0, 910, 842, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 12, 0, 980, 842, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
			imagettftext($imagem, 12, 0, 1120, 842, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
			imagettftext($imagem, 12, 0, 170, 890, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 12, 0, 170, 980, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 12, 0, 780, 980, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
			
			
			//Dependente 2
			imagettftext($imagem, 12, 0, 170, 1025, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 1124, 1025, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
			imagettftext($imagem, 12, 0, 170, 1070, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 12, 0, 330, 1070, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
			imagettftext($imagem, 12, 0, 497, 1070, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep2);
			imagettftext($imagem, 12, 0, 645, 1070, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep2);
			imagettftext($imagem, 12, 0, 735, 1070, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep2);
			imagettftext($imagem, 11, 0, 795, 1070, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep2);
			imagettftext($imagem, 12, 0, 910, 1070, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 12, 0, 980, 1070, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
			imagettftext($imagem, 12, 0, 1120, 1070, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
			imagettftext($imagem, 12, 0, 170, 1117, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 12, 0, 170, 1205, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 12, 0, 780, 1205, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			
			
			//Dependente 3
			imagettftext($imagem, 12, 0, 170, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 1124, 1250, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
			imagettftext($imagem, 12, 0, 170, 1297, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 12, 0, 330, 1297, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
			imagettftext($imagem, 12, 0, 497, 1297, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep3);
			imagettftext($imagem, 12, 0, 645, 1297, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep3);
			imagettftext($imagem, 12, 0, 735, 1297, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep3);
			imagettftext($imagem, 11, 0, 795, 1297, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep3);
			imagettftext($imagem, 12, 0, 910, 1297, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 12, 0, 980, 1297, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
			imagettftext($imagem, 12, 0, 1120, 1297, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
			imagettftext($imagem, 12, 0, 170, 1345, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 12, 0, 170, 1430, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 12, 0, 780, 1430, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			
			imagettftext($imagem, 12, 0, 120, 1645, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 330, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '2'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 900, 1410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 265, 1410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 300, 1473, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 10, 0, 740, 1558, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));	
			imagettftext($imagem, 10, 0, 150, 1558, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '3'){
			$dataNasc = explode('/',SqlToData($rowAssociado->DATA_NASCIMENTO));
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 990, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 120, 277, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 11, 0, 220, 327, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 280, 327, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 350, 327, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 12, 0, 550, 327, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 100, 497, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 12, 0, 100, 537, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 12, 0, 100, 577, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 15, 0, 83, 872, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 15, 0, 83, 977, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 15, 0, 83, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));

			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
		
		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');

			imagettftext($imagem, 14, 0, 990, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 270, 1120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 14, 0, 572, 1120, $cor,"../../Site/assets/img/arial.ttf", jn_utf8_encode(date('d')));		
			imagettftext($imagem, 14, 0, 780, 1120, $cor,"../../Site/assets/img/arial.ttf", strftime('%B', strtotime('today')));	
			imagettftext($imagem, 14, 0, 980, 1120, $cor,"../../Site/assets/img/arial.ttf", jn_utf8_encode(date('Y')));				
			imagettftext($imagem, 14, 0, 100, 1260, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	
		
		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 14, 0, 990, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 510, 325, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 14, 0, 180, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 14, 0, 380, 1373, $cor,"../../Site/assets/img/arial.ttf", jn_utf8_encode(date('d')));		
			imagettftext($imagem, 14, 0, 550, 1373, $cor,"../../Site/assets/img/arial.ttf", strftime('%B', strtotime('today')));	
			imagettftext($imagem, 14, 0, 730, 1373, $cor,"../../Site/assets/img/arial.ttf", jn_utf8_encode(date('Y')));
			imagettftext($imagem, 14, 0, 100, 1550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));							
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );	
		}
		
		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '7'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '8'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta32_Vidamax8.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	
	}
}

if($_GET['modelo'] == 13){
	if(($_GET['portabilidade'] == 'N') or ($_GET['portabilidade'] == '')){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, VND1000_ON.CODIGO_TABELA_PRECO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, VND1000_ON.NUMERO_DECLARACAO_NASC_VIVO, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);

		
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}
		
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;	
		if ($rowAssociado->CODIGO_PLANO == '35') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
		} 
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;
		
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;	
			if ($rowDep1->CODIGO_PLANO == '35') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
			} 
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;	
			if ($rowDep2->CODIGO_PLANO == '35') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
			} 	
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;
			if ($rowDep3->CODIGO_PLANO == '35') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
			} 		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
			
		}	

		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 16, 0, 628, 340, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
				
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}		

		if($_GET['pagina'] == '2'){
			
			$colunaAcomodacao == 0;

			if($rowAssociado->CODIGO_PLANO == 34){
				$colunaAcomodacao = 654;
			}elseif($rowAssociado->CODIGO_PLANO == 35){
				$colunaAcomodacao = 746; 
			}elseif($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 465; 
			}else{
				$colunaAcomodacao = 370; 	
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 1000, 105, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 160, 295, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 12, 0, 980, 358, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 12, 0, 130, 410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 12, 0, 130, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 320, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 460, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 700, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 12, 0, 880, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 955, 460, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 1115, 460, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 130, 505, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 12, 0, 130, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 12, 0, 600, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
			imagettftext($imagem, 12, 0, 130, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 12, 0, 130, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 12, 0, 640, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 12, 0, 870, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 1100, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 12, 0, 130, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 12, 0, 410, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			imagettftext($imagem, 12, 0, 640, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			
			//Dependente 1
			imagettftext($imagem, 12, 0, 170, 787, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 1124, 787, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
			imagettftext($imagem, 12, 0, 170, 833, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 12, 0, 330, 833, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 12, 0, 485, 833, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep1);
			imagettftext($imagem, 12, 0, 620, 833, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep1);
			imagettftext($imagem, 12, 0, 720, 833, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep1);
			imagettftext($imagem, 11, 0, 795, 833, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 12, 0, 890, 833, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 12, 0, 970, 833, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
			imagettftext($imagem, 12, 0, 1124, 833, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
			imagettftext($imagem, 12, 0, 170, 878, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 12, 0, 170, 968, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 12, 0, 780, 968, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
			
			
			//Dependente 2
			imagettftext($imagem, 12, 0, 170, 1010, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 1124, 1010, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
			imagettftext($imagem, 12, 0, 170, 1057, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 12, 0, 330, 1057, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
			imagettftext($imagem, 12, 0, 485, 1057, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep2);
			imagettftext($imagem, 12, 0, 620, 1057, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep2);
			imagettftext($imagem, 12, 0, 720, 1057, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep2);
			imagettftext($imagem, 11, 0, 795, 1057, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep2);
			imagettftext($imagem, 12, 0, 890, 1057, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 12, 0, 970, 1057, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
			imagettftext($imagem, 12, 0, 1124, 1057, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
			imagettftext($imagem, 12, 0, 170, 1105, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 12, 0, 170, 1195, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 12, 0, 780, 1195, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			
			
			//Dependente 3
			imagettftext($imagem, 12, 0, 170, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 1124, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
			imagettftext($imagem, 12, 0, 170, 1287, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 12, 0, 330, 1287, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
			imagettftext($imagem, 12, 0, 485, 1287, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep3);
			imagettftext($imagem, 12, 0, 620, 1287, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep3);
			imagettftext($imagem, 12, 0, 720, 1287, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep3);
			imagettftext($imagem, 11, 0, 795, 1287, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep3);
			imagettftext($imagem, 12, 0, 890, 1287, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 12, 0, 970, 1287, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
			imagettftext($imagem, 12, 0, 1124, 1287, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
			imagettftext($imagem, 12, 0, 170, 1328, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 12, 0, 170, 1420, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 12, 0, 780, 1420, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			
			imagettftext($imagem, 12, 0, 120, 1625, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 330, 1625, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		
		if($_GET['pagina'] == '3'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 120, 1655, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 465, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		

		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '7'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 270, 1410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 465, 1470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '8'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax8.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));			
			imagettftext($imagem, 14, 0, 200, 393, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->VALOR_TAXA_ADESAO));
			imagettftext($imagem, 12, 0, 340, 393, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(valorPorExtenso(str_replace(',','.',$rowAssociado->VALOR_TAXA_ADESAO))));											
			imagettftext($imagem, 12, 0, 440, 495, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));					
			imagettftext($imagem, 12, 0, 400, 575 , $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));					
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '9'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax9.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '10'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax10.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '11'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax11.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		if($_GET['pagina'] == '12'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13_Vidamax12.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
	}elseif($_GET['portabilidade'] == 'S'){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, VND1000_ON.CODIGO_TABELA_PRECO, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}
		
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;
		if ($rowAssociado->CODIGO_PLANO == '35') {
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
		} 		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;

		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);

		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;	
			if ($rowDep1->CODIGO_PLANO == '35') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
			} 
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;
			if ($rowDep2->CODIGO_PLANO == '35') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
			} 		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;
			if ($rowDep3->CODIGO_PLANO == '35') {
				$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas('35');
			} 	
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
			
		}	

		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13port_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 16, 0, 620, 337, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
				
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}		

		if($_GET['pagina'] == '2'){
			
			$colunaAcomodacao == 0;			

			if($rowAssociado->CODIGO_PLANO == 34){
				$colunaAcomodacao = 500;
			}elseif($rowAssociado->CODIGO_PLANO == 35){
				$colunaAcomodacao = 590; 
			}elseif($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 350; 
			}else{
				$colunaAcomodacao = 255; 	
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13port_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 1000, 103, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 160, 310, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 12, 0, 980, 390, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 12, 0, 130, 448, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 12, 0, 130, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 320, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 494, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 700, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 12, 0, 880, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 955, 494, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 1115, 494, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 130, 540, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 12, 0, 130, 629, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 12, 0, 600, 629, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
			imagettftext($imagem, 12, 0, 130, 675, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 12, 0, 130, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 12, 0, 640, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 12, 0, 870, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 1100, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 12, 0, 130, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 12, 0, 410, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			imagettftext($imagem, 12, 0, 640, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			
			//Dependente 1
			imagettftext($imagem, 12, 0, 170, 822, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 1124, 822, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
			imagettftext($imagem, 12, 0, 170, 867, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 12, 0, 330, 867, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 12, 0, 485, 867, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep1);
			imagettftext($imagem, 12, 0, 620, 867, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep1);
			imagettftext($imagem, 12, 0, 720, 867, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep1);
			imagettftext($imagem, 11, 0, 795, 867, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 12, 0, 890, 867, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 12, 0, 970, 867, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
			imagettftext($imagem, 12, 0, 1124, 867, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
			imagettftext($imagem, 12, 0, 170, 915, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 12, 0, 170, 1005, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 12, 0, 780, 995, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
			
			
			//Dependente 2
			imagettftext($imagem, 12, 0, 170, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 1124, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
			imagettftext($imagem, 12, 0, 170, 1095, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 12, 0, 330, 1095, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
			imagettftext($imagem, 12, 0, 485, 1095, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep2);
			imagettftext($imagem, 12, 0, 620, 1095, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep2);
			imagettftext($imagem, 12, 0, 720, 1095, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep2);
			imagettftext($imagem, 11, 0, 795, 1095, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep2);
			imagettftext($imagem, 12, 0, 890, 1095, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 12, 0, 970, 1095, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
			imagettftext($imagem, 12, 0, 1124, 1095, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
			imagettftext($imagem, 12, 0, 170, 1140, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 12, 0, 170, 1230, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 12, 0, 780, 1230, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			
			
			//Dependente 3
			imagettftext($imagem, 12, 0, 170, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 1124, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
			imagettftext($imagem, 12, 0, 170, 1325, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 12, 0, 330, 1325, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
			imagettftext($imagem, 12, 0, 485, 1325, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep3);
			imagettftext($imagem, 12, 0, 620, 1325, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep3);
			imagettftext($imagem, 12, 0, 720, 1325, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep3);
			imagettftext($imagem, 11, 0, 795, 1325, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep3);
			imagettftext($imagem, 12, 0, 890, 1325, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 12, 0, 970, 1325, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
			imagettftext($imagem, 12, 0, 1124, 1325, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
			imagettftext($imagem, 12, 0, 170, 1370, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 12, 0, 170, 1459, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 12, 0, 780, 1459, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			
			imagettftext($imagem, 12, 0, 120, 1645, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 330, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13port_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13port_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

			if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13port_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '6'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta13port_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 270, 1350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 465, 1410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
	}
}

if($_GET['modelo'] == 15){
	if(($_GET['portabilidade'] == 'N') or ($_GET['portabilidade'] == '')){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, VND1000_ON.CODIGO_TABELA_PRECO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, VND1000_ON.NUMERO_DECLARACAO_NASC_VIVO, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);

		
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}
		
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;	
		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;
		
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);
		
		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NOME_PARENTESCO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;	
			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
			$parentescoDep1 = $rowDep1->NOME_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NOME_PARENTESCO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;	
			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->NOME_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NOME_PARENTESCO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_TABELA_PRECO, PESO, ALTURA ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;
				
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->NOME_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
			
		}	

		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 16, 0, 525, 328, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
				
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}		

		if($_GET['pagina'] == '2'){
			
			$linhaAcomodacao == 0;
			$linhaHelpMovel == 0;

			if($rowAssociado->CODIGO_PLANO == 113){        
				$linhaAcomodacao = 346;
			}elseif($rowAssociado->CODIGO_PLANO == 114){  
				$linhaAcomodacao = 378;  
			}else{
				$linhaAcomodacao = ''; 	
			}
			
			$queryEvento = 'SELECT CODIGO_EVENTO, sum(VALOR_FATOR) AS SOMA_VALOR_FATOR FROM VND1003_ON WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp) . ')';	
			$queryEvento .= ' GROUP BY CODIGO_EVENTO ';
			$resEvento = jn_query($queryEvento);
			$valorHelpMovel = 0;

			while($rowEvento = jn_fetch_object($resEvento)){

				if($rowEvento->CODIGO_EVENTO == 73){
					$valorHelpMovel = $rowEvento->SOMA_VALOR_FATOR;
				
					if($valorHelpMovel > 0){
						$linhaHelpMovel = 408;
					}else{
						$linhaHelpMovel = '';
					}
				}else{
					$valorTotal += $rowEvento->SOMA_VALOR_FATOR;	
				}
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 105, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 160, 295, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 12, 0, 360, 452, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 12, 0, 910, 452, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorHelpMovel));
			imagettftext($imagem, 14, 0, 315, $linhaAcomodacao, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 14, 0, 315, $linhaHelpMovel, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 12, 0, 130, 505, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 12, 0, 130, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 320, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 550, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 695, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 12, 0, 880, 550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 955, 550, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 1115, 550, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 130, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 12, 0, 130, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 12, 0, 130, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 12, 0, 130, 773, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 12, 0, 640, 773, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 12, 0, 870, 773, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 1100, 773, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 12, 0, 130, 818, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 12, 0, 410, 818, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			imagettftext($imagem, 12, 0, 640, 818, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			
			
			//Dependente 1
			imagettftext($imagem, 12, 0, 170, 878, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 1035, 878, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
			imagettftext($imagem, 12, 0, 170, 921, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 12, 0, 330, 921, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 12, 0, 485, 921, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep1);
			imagettftext($imagem, 12, 0, 620, 921, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep1);
			imagettftext($imagem, 12, 0, 720, 921, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep1);
			imagettftext($imagem, 11, 0, 795, 921, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 12, 0, 890, 921, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 12, 0, 970, 921, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
			imagettftext($imagem, 12, 0, 1124, 921, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
			imagettftext($imagem, 12, 0, 170, 966, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 12, 0, 170, 1056, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 12, 0, 780, 1056, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
			
			
			//Dependente 2
			imagettftext($imagem, 12, 0, 170, 1105, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 1035, 1105, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
			imagettftext($imagem, 12, 0, 170, 1150, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 12, 0, 330, 1150, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
			imagettftext($imagem, 12, 0, 485, 1150, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep2);
			imagettftext($imagem, 12, 0, 620, 1150, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep2);
			imagettftext($imagem, 12, 0, 720, 1150, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep2);
			imagettftext($imagem, 11, 0, 795, 1150, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep2);
			imagettftext($imagem, 12, 0, 890, 1150, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 12, 0, 970, 1150, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
			imagettftext($imagem, 12, 0, 1124, 1150, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
			imagettftext($imagem, 12, 0, 170, 1195, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 12, 0, 170, 1285, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 12, 0, 780, 1285, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			
			
			//Dependente 3
			imagettftext($imagem, 12, 0, 170, 1329, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 1035, 1329, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
			imagettftext($imagem, 12, 0, 170, 1375, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 12, 0, 330, 1375, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
			imagettftext($imagem, 12, 0, 485, 1375, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep3);
			imagettftext($imagem, 12, 0, 620, 1375, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep3);
			imagettftext($imagem, 12, 0, 720, 1375, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep3);
			imagettftext($imagem, 11, 0, 795, 1375, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep3);
			imagettftext($imagem, 12, 0, 890, 1375, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 12, 0, 970, 1375, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
			imagettftext($imagem, 12, 0, 1124, 1375, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
			imagettftext($imagem, 12, 0, 170, 1420, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 12, 0, 170, 1515, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 12, 0, 780, 1515, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			
			imagettftext($imagem, 12, 0, 120, 1635, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 330, 1635, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		
		if($_GET['pagina'] == '3'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
		
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		

		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));

			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));

			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 270, 1480, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 450, 1533, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '7'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 110, 530, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));			
			imagettftext($imagem, 14, 0, 970, 1335, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->VALOR_TAXA_ADESAO));
			imagettftext($imagem, 14, 0, 130, 1380, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(valorPorExtenso(str_replace(',','.',$rowAssociado->VALOR_TAXA_ADESAO))));											
			imagettftext($imagem, 12, 0, 370, 1490, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));	//CONSULTOR É O VENDEDOR?				
			imagettftext($imagem, 12, 0, 410, 1555 , $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR)); //CONSULTOR É O VENDEDOR?
			imagettftext($imagem, 12, 0, 355, 1610 , $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CPF_VENDEDOR)); //CONSULTOR É O VENDEDOR?
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '8'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax8.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '9'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax9.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '10'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax10.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '11'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta15_Vidamax11.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

	
	}
}

if($_GET['modelo'] == 14){
	if(($_GET['portabilidade'] == 'N') or ($_GET['portabilidade'] == '')){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);

		
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}
		
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;

		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);

		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
			
		}	

		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 16, 0, 628, 335, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 16, 0, 222, 1424, $cor,"../../Site/assets/img/arial.ttf",date('d'));		
			imagettftext($imagem, 16, 0, 335, 1424, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));	
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}		

		if($_GET['pagina'] == '2'){
			
			$colunaAcomodacao == 0;
			if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 465;
			}else{
				$colunaAcomodacao = 370;		
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 1000, 105, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 130, 295, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 12, 0, 980, 358, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 12, 0, 130, 410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 12, 0, 130, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 320, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 460, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 700, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 12, 0, 880, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 955, 460, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 1115, 460, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 130, 505, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 12, 0, 130, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 12, 0, 600, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
			imagettftext($imagem, 12, 0, 130, 640, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 12, 0, 130, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 12, 0, 640, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 12, 0, 870, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 1100, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 12, 0, 130, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 12, 0, 410, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			imagettftext($imagem, 12, 0, 640, 730, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			
			//Dependente 1
			imagettftext($imagem, 12, 0, 170, 787, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 1124, 787, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
			imagettftext($imagem, 12, 0, 170, 833, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 12, 0, 330, 833, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 12, 0, 485, 833, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep1);
			imagettftext($imagem, 12, 0, 620, 833, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep1);
			imagettftext($imagem, 12, 0, 720, 833, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep1);
			imagettftext($imagem, 11, 0, 795, 833, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 12, 0, 890, 833, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 12, 0, 970, 833, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
			imagettftext($imagem, 12, 0, 1124, 833, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
			imagettftext($imagem, 12, 0, 170, 878, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 12, 0, 170, 968, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 12, 0, 780, 968, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
			
			
			//Dependente 2
			imagettftext($imagem, 12, 0, 170, 1010, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 1124, 1010, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
			imagettftext($imagem, 12, 0, 170, 1057, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 12, 0, 330, 1057, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
			imagettftext($imagem, 12, 0, 485, 1057, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep2);
			imagettftext($imagem, 12, 0, 620, 1057, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep2);
			imagettftext($imagem, 12, 0, 720, 1057, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep2);
			imagettftext($imagem, 11, 0, 795, 1057, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep2);
			imagettftext($imagem, 12, 0, 890, 1057, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 12, 0, 970, 1057, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
			imagettftext($imagem, 12, 0, 1124, 1057, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
			imagettftext($imagem, 12, 0, 170, 1105, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 12, 0, 170, 1195, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 12, 0, 780, 1195, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			
			
			//Dependente 3
			imagettftext($imagem, 12, 0, 170, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 1124, 1240, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
			imagettftext($imagem, 12, 0, 170, 1287, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 12, 0, 330, 1287, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
			imagettftext($imagem, 12, 0, 485, 1287, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep3);
			imagettftext($imagem, 12, 0, 620, 1287, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep3);
			imagettftext($imagem, 12, 0, 720, 1287, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep3);
			imagettftext($imagem, 11, 0, 795, 1287, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep3);
			imagettftext($imagem, 12, 0, 890, 1287, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 12, 0, 970, 1287, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
			imagettftext($imagem, 12, 0, 1124, 1287, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
			imagettftext($imagem, 12, 0, 170, 1328, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 12, 0, 170, 1420, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 12, 0, 780, 1420, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			
			imagettftext($imagem, 12, 0, 120, 1610, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 330, 1610, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		
		if($_GET['pagina'] == '3'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 12, 0, 120, 1655, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 465, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		

		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '6'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '7'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax7.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 270, 1410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 465, 1470, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '8'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax8.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));			
			imagettftext($imagem, 12, 0, 200, 1030, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->VALOR_TAXA_ADESAO));					
			imagettftext($imagem, 12, 0, 440, 1175, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));					
			imagettftext($imagem, 12, 0, 400, 1255 , $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_VENDEDOR));					
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '9'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax9.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '10'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax10.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '11'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax11.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

		if($_GET['pagina'] == '12'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14_Vidamax12.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
	}elseif($_GET['portabilidade'] == 'S'){
		$codAssociadoTmp = $_GET['codAssociado'];
		$percentual = 0;
		$valorTotal = 0;
		
		$queryAssociado  = ' SELECT ';
		$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
		$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
		$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
		$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
		$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
		$queryAssociado .= ' FROM VND1000_ON ';
		$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
		$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
		
		$resAssociado = jn_query($queryAssociado);
		if(!$rowAssociado = jn_fetch_object($resAssociado)){
			echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
			exit;
		}
		
		
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
		$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
		
		$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorTit = $rowValores->VALOR_PLANO;

		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorTit = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorTit);

		//Tratativas para dependentes

		//Dependente 1
		$codigoDep1 = explode('.',$codAssociadoTmp);
		$codigoDep1 = $codigoDep1[0] . '.1';

		$queryDep1  = ' SELECT ';
		$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep1 .= ' FROM VND1000_ON ';
		$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
		$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

		$resDep1 = jn_query($queryDep1);
		if($rowDep1 = jn_fetch_object($resDep1)){
			$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
			$diaNascDep1 = '';
			$mesNascDep1 = '';
			$anoNascDep1 = '';
			
			$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
			$diaNascDep1 = explode(' ', $diaNascDep1);
			$diaNascDep1 = $diaNascDep1[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep1 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep1);

			
			$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
			$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
			$numeroRGDep1 = $rowDep1->NUMERO_RG;
			$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
			$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
			$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
			$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
			$sexoDep1 = $rowDep1->SEXO;
			$nomeMaeDep1 = $rowDep1->NOME_MAE;
			$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
			$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
			$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
			$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
			$pesoDep1 = $rowDep1->PESO;
			$alturaDep1 = $rowDep1->ALTURA;
		}

		//Dependente 2
		$codigoDep2 = explode('.',$codAssociadoTmp);
		$codigoDep2 = $codigoDep2[0] . '.2';

		$queryDep2  = ' SELECT ';
		$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep2 .= ' FROM VND1000_ON ';
		$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
		$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep2 = jn_query($queryDep2);
		if($rowDep2 = jn_fetch_object($resDep2)){
			
			$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
			
			$diaNascDep2 = '';
			$mesNascDep2 = '';
			$anoNascDep2 = '';
			
			$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
			$diaNascDep2 = explode(' ', $diaNascDep2);
			$diaNascDep2 = $diaNascDep2[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep2 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep2);
			
			$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
			$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
			$numeroRGDep2 = $rowDep2->NUMERO_RG;
			$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
			$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
			$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
			$sexoDep2 = $rowDep2->SEXO;
			$nomeMaeDep2 = $rowDep2->NOME_MAE;
			$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
			$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
			$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
			$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
			$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
			$pesoDep2 = $rowDep2->PESO;
			$alturaDep2= $rowDep2->ALTURA;
		}

		//Dependente 3
		$codigoDep3 = explode('.',$codAssociadoTmp);
		$codigoDep3 = $codigoDep3[0] . '.3';

		$queryDep3  = ' SELECT ';
		$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
		$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
		$queryDep3 .= ' FROM VND1000_ON ';
		$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
		$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
		$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
		$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
		$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
			
		$resDep3 = jn_query($queryDep3);
		if($rowDep3 = jn_fetch_object($resDep3)){
			$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
			
			$diaNascDep3 = '';
			$mesNascDep3 = '';
			$anoNascDep3 = '';
			
			$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
			$diaNascDep3 = explode(' ', $diaNascDep3);
			$diaNascDep3 = $diaNascDep3[0];
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valorDep3 = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
				$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
			}
			
			$valorTotal = ($valorTotal + $valorDep3);
			
			$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
			$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
			$numeroRGDep3 = $rowDep3->NUMERO_RG;
			$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
			$sexoDep3 = $rowDep3->SEXO;
			$nomeMaeDep3 = $rowDep3->NOME_MAE;
			$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
			$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
			$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
			$pesoDep3 = $rowDep3->PESO;
			$alturaDep3= $rowDep3->ALTURA;
			$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
			$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
			$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
			$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
			
		}	

		
		if($_GET['pagina'] == '1'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14port_Vidamax1.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');
			
			imagettftext($imagem, 16, 0, 620, 337, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 16, 0, 216, 1423, $cor,"../../Site/assets/img/arial.ttf",date('d'));		
			imagettftext($imagem, 16, 0, 330, 1423, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));	
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}		

		if($_GET['pagina'] == '2'){
			
			$colunaAcomodacao == 0;
			if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
				$colunaAcomodacao = 450;
			}else{
				$colunaAcomodacao = 255;		
			}
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14port_Vidamax2.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );
			imagettftext($imagem, 14, 0, 1000, 103, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 130, 310, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));		
			imagettftext($imagem, 12, 0, 980, 390, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
			imagettftext($imagem, 14, 0, $colunaAcomodacao, 390, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 12, 0, 130, 448, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			imagettftext($imagem, 12, 0, 130, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
			imagettftext($imagem, 12, 0, 320, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
			imagettftext($imagem, 10, 0, 480, 494, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
			imagettftext($imagem, 10, 0, 630, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
			imagettftext($imagem, 10, 0, 700, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
			imagettftext($imagem, 10, 0, 780, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
			imagettftext($imagem, 12, 0, 880, 494, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
			imagettftext($imagem, 12, 0, 955, 494, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 1115, 494, $cor,"../../Site/assets/img/arial.ttf",calcularIdade($rowAssociado->DATA_NASCIMENTO));
			imagettftext($imagem, 12, 0, 130, 540, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
			imagettftext($imagem, 12, 0, 130, 629, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
			imagettftext($imagem, 12, 0, 600, 629, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
			imagettftext($imagem, 12, 0, 130, 675, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
			imagettftext($imagem, 12, 0, 130, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
			imagettftext($imagem, 12, 0, 640, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
			imagettftext($imagem, 12, 0, 870, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
			imagettftext($imagem, 12, 0, 1100, 720, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
			imagettftext($imagem, 12, 0, 130, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
			imagettftext($imagem, 12, 0, 410, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
			imagettftext($imagem, 12, 0, 640, 765, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
			
			//Dependente 1
			imagettftext($imagem, 12, 0, 170, 822, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 10, 0, 1124, 822, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
			imagettftext($imagem, 12, 0, 170, 867, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 12, 0, 330, 867, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 12, 0, 485, 867, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep1);
			imagettftext($imagem, 12, 0, 620, 867, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep1);
			imagettftext($imagem, 12, 0, 720, 867, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep1);
			imagettftext($imagem, 11, 0, 795, 867, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 12, 0, 890, 867, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
			imagettftext($imagem, 12, 0, 970, 867, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep1);
			imagettftext($imagem, 12, 0, 1124, 867, $cor,"../../Site/assets/img/arial.ttf",$idadeDep1);
			imagettftext($imagem, 12, 0, 170, 915, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 12, 0, 170, 1005, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 12, 0, 780, 995, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
			
			
			//Dependente 2
			imagettftext($imagem, 12, 0, 170, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 10, 0, 1124, 1050, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
			imagettftext($imagem, 12, 0, 170, 1095, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 12, 0, 330, 1095, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
			imagettftext($imagem, 12, 0, 485, 1095, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep2);
			imagettftext($imagem, 12, 0, 620, 1095, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep2);
			imagettftext($imagem, 12, 0, 720, 1095, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep2);
			imagettftext($imagem, 11, 0, 795, 1095, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep2);
			imagettftext($imagem, 12, 0, 890, 1095, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
			imagettftext($imagem, 12, 0, 970, 1095, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep2);
			imagettftext($imagem, 12, 0, 1124, 1095, $cor,"../../Site/assets/img/arial.ttf",$idadeDep2);
			imagettftext($imagem, 12, 0, 170, 1140, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 12, 0, 170, 1230, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 12, 0, 780, 1230, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			
			
			//Dependente 3
			imagettftext($imagem, 12, 0, 170, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 10, 0, 1124, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
			imagettftext($imagem, 12, 0, 170, 1325, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 12, 0, 330, 1325, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
			imagettftext($imagem, 12, 0, 485, 1325, $cor,"../../Site/assets/img/arial.ttf",$dataEmissaoRGDep3);
			imagettftext($imagem, 12, 0, 620, 1325, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmissorRGDep3);
			imagettftext($imagem, 12, 0, 720, 1325, $cor,"../../Site/assets/img/arial.ttf",$naturezaRGDep3);
			imagettftext($imagem, 11, 0, 795, 1325, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep3);
			imagettftext($imagem, 12, 0, 890, 1325, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
			imagettftext($imagem, 12, 0, 970, 1325, $cor,"../../Site/assets/img/arial.ttf",$dataNascimentoDep3);
			imagettftext($imagem, 12, 0, 1124, 1325, $cor,"../../Site/assets/img/arial.ttf",$idadeDep3);
			imagettftext($imagem, 12, 0, 170, 1370, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 12, 0, 170, 1459, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 12, 0, 780, 1459, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			
			imagettftext($imagem, 12, 0, 120, 1645, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
			imagettftext($imagem, 12, 0, 330, 1645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '3'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14port_Vidamax3.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '4'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14port_Vidamax4.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}	

			if($_GET['pagina'] == '5'){
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14port_Vidamax5.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );		
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}

		if($_GET['pagina'] == '6'){
			
			$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta14port_Vidamax6.jpg");	
			$cor = imagecolorallocate($imagem, 0, 0, 0 );

			imagettftext($imagem, 14, 0, 1000, 100, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));
			imagettftext($imagem, 14, 0, 270, 1350, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE) . ', ' . date('d/m/Y'));					
			imagettftext($imagem, 14, 0, 465, 1410, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
			
			$image_p = imagecreatetruecolor(1240, 1754);
			imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
			header( "Content-type: image/jpeg" );
			return imagejpeg( $image_p, NULL, 80 );
		}
	}
}

if($_GET['modelo'] == 10){
	$codAssociadoTmp = $_GET['codAssociado'];
	$percentual = 0;
	$valorTotal = 0;
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
	$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
	$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
	$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	
	$resAssociado = jn_query($queryAssociado);
	if(!$rowAssociado = jn_fetch_object($resAssociado)){
		echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
		exit;
	}
	
	$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
	$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
	$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
	
	$resPerc = jn_query($queryPerc);
	while($rowPerc = jn_fetch_object($resPerc)){
		$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
	}
	
	$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	
	$valorTit = $rowValores->VALOR_PLANO;

	if($percentual > 0){
		$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
		$valorTit = ($rowValores->VALOR_PLANO + $calculo);
	}
	
	$valorTotal = ($valorTotal + $valorTit);

	//Tratativas para dependentes

	//Dependente 1
	$codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';

	$queryDep1  = ' SELECT ';
	$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep1 .= ' FROM VND1000_ON ';
	$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

	$resDep1 = jn_query($queryDep1);
	if($rowDep1 = jn_fetch_object($resDep1)){
		$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
			
		$diaNascDep1 = '';
		$mesNascDep1 = '';
		$anoNascDep1 = '';
			
		$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
		$diaNascDep1 = explode(' ', $diaNascDep1);
		$diaNascDep1 = $diaNascDep1[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep1 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep1);

		
		$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
		$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
		$numeroRGDep1 = $rowDep1->NUMERO_RG;
		$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
		$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
		$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
		$sexoDep1 = $rowDep1->SEXO;
		$nomeMaeDep1 = $rowDep1->NOME_MAE;
		$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
		$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
		$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
		$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
		$pesoDep1 = $rowDep1->PESO;
		$alturaDep1 = $rowDep1->ALTURA;
	}

	//Dependente 2
	$codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';

	$queryDep2  = ' SELECT ';
	$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep2 .= ' FROM VND1000_ON ';
	$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep2 = jn_query($queryDep2);
	if($rowDep2 = jn_fetch_object($resDep2)){
		
		$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
		
		$diaNascDep2 = '';
		$mesNascDep2 = '';
		$anoNascDep2 = '';
		
		$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
		$diaNascDep2 = explode(' ', $diaNascDep2);
		$diaNascDep2 = $diaNascDep2[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep2 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep2);
		
		$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
		$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
		$numeroRGDep2 = $rowDep2->NUMERO_RG;
		$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
		$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
		$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
		$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$sexoDep2 = $rowDep2->SEXO;
		$nomeMaeDep2 = $rowDep2->NOME_MAE;
		$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
		$parentescoDep2 = $rowDep2->NOME_PARENTESCO;
		$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
		$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
		$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
		$pesoDep2 = $rowDep2->PESO;
		$alturaDep2= $rowDep2->ALTURA;
	}

	//Dependente 3
	$codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';

	$queryDep3  = ' SELECT ';
	$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep3 .= ' FROM VND1000_ON ';
	$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep3 = jn_query($queryDep3);
	if($rowDep3 = jn_fetch_object($resDep3)){
		$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
		
		$diaNascDep3 = '';
		$mesNascDep3 = '';
		$anoNascDep3 = '';
		
		$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
		$diaNascDep3 = explode(' ', $diaNascDep3);
		$diaNascDep3 = $diaNascDep3[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep3 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep3);
		
		$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
		$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
		$numeroRGDep3 = $rowDep3->NUMERO_RG;
		$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$sexoDep3 = $rowDep3->SEXO;
		$nomeMaeDep3 = $rowDep3->NOME_MAE;
		$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
		$parentescoDep3 = $rowDep3->NOME_PARENTESCO;
		$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
		$pesoDep3 = $rowDep3->PESO;
		$alturaDep3= $rowDep3->ALTURA;
		$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
		$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
		$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
		$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
		
	}
	
	//Dependente 4
	$codigoDep4 = explode('.',$codAssociadoTmp);
	$codigoDep4 = $codigoDep4[0] . '.4';

	$queryDep4  = ' SELECT ';
	$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep4 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep4 .= ' FROM VND1000_ON ';
	$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep4 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
	$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep4 = jn_query($queryDep4);
	if($rowDep4 = jn_fetch_object($resDep4)){
		$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
		
		$diaNascDep4 = '';
		$mesNascDep4 = '';
		$anoNascDep4 = '';
		
		$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
		$diaNascDep4 = explode(' ', $diaNascDep4);
		$diaNascDep4 = $diaNascDep4[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep4 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep4);
		
		$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
		$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
		$numeroRGDep4 = $rowDep4->NUMERO_RG;
		$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$sexoDep4 = $rowDep4->SEXO;
		$nomeMaeDep4 = $rowDep4->NOME_MAE;
		$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
		$parentescoDep4 = $rowDep4->NOME_PARENTESCO;
		$estadoCivilDep4 = $rowDep4->NOME_ESTADO_CIVIL;
		$pesoDep4 = $rowDep4->PESO;
		$alturaDep4= $rowDep4->ALTURA;
		$naturezaRGDep4 = $rowDep4->NATUREZA_RG;
		$orgaoEmissorRGDep4 = $rowDep4->ORGAO_EMISSOR_RG;
		$dataEmissaoRGDep4 = SqlToData($rowDep4->DATA_EMISSAO_RG);
		$numeroDecNascVivoDep4 = $rowDep4->NUMERO_DECLARACAO_NASC_VIVO;
	}
	
	if($_GET['pagina'] == '1'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta10_Vidamax1.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		$colunaRecebeEmail = '';
		
		if($rowAssociado->ENDERECO_EMAIL){		
			$colunaRecebeEmail = 147;
		}else{
			$colunaRecebeEmail = 230;			
		}
		
		
		imagettftext($imagem, 12, 0, 720, 120, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_PLANO_FAMILIARES,0,17)));
		imagettftext($imagem, 12, 0, 740, 155, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));		
		imagettftext($imagem, 11, 0, 139, 225, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 12, 0, 120, 255, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 12, 0, 350, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
		imagettftext($imagem, 10, 0, 730, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
		imagettftext($imagem, 12, 0, 920, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 12, 0, 190, 290, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 12, 0, 920, 417, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
		imagettftext($imagem, 12, 0, 380, 417, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
		imagettftext($imagem, 12, 0, 65, 450, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
		imagettftext($imagem, 12, 0, 485, 450, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
		imagettftext($imagem, 12, 0, 860, 450, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
		imagettftext($imagem, 12, 0, 100, 320, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 12, 0, 90, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 12, 0, 380, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 12, 0, 860, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 12, 0, 1120, 355, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 12, 0, 380, 485, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
		imagettftext($imagem, 14, 0, $colunaRecebeEmail, 485, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		
		
		//Dependente 1
		if ($nomeDep1 != ''){
			$dataNasc = explode('/',$dataNascimentoDep1);		
			$linhaParentesco = '';
			
			if($parentescoDep1 == '1' || $parentescoDep1 == '2'){
				$linhaParentesco = 590;
			}elseif($parentescoDep1 == '21' || $parentescoDep1 == '13' || $parentescoDep1 == '10' || $parentescoDep1 == '3'){
				$linhaParentesco = 608;
			}else{
				$linhaParentesco = 623;
			}

			imagettftext($imagem, 11, 0, 120, 580	, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 11, 0, 750, 580, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 11, 0, 1005, 580, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 1050, 580, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1080, 580, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 170, 613, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 11, 0, 750, 613, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 11, 0, 970, 613, $cor,"../../Site/assets/img/arial.ttf",substr($numeroDecNascVivoDep1,0,19));
			imagettftext($imagem, 10, 0, 1128, $linhaParentesco, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}
		
		//Dependente 2
		if ($nomeDep2 != ''){
			$dataNasc = explode('/',$dataNascimentoDep2);		
			$linhaParentesco = '';
			
			if($parentescoDep2 == '1' || $parentescoDep2 == '2'){
				$linhaParentesco = 657;
			}elseif($parentescoDep2 == '21' || $parentescoDep2 == '13' || $parentescoDep2 == '10' || $parentescoDep2 == '3'){
				$linhaParentesco = 675;
			}elseif($parentescoDep2 != ''){
				$linhaParentesco = 690;
			}
			
			imagettftext($imagem, 11, 0, 120, 648, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 11, 0, 750, 648, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 11, 0, 1005, 648, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 1050, 648, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1080, 648, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 170, 681, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 11, 0, 750, 681, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 11, 0, 970, 681, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
			imagettftext($imagem, 10, 0, 1128, $linhaParentesco, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}
		
		//Dependente 3
		if ($nomeDep3 != ''){
			$dataNasc = explode('/',$dataNascimentoDep3);		
			$linhaParentesco = '';
			
			if($parentescoDep3 == '1' || $parentescoDep3 == '2'){
				$linhaParentesco = 725;
			}elseif($parentescoDep3 == '21' || $parentescoDep3 == '13' || $parentescoDep3 == '10' || $parentescoDep3 == '3'){
				$linhaParentesco = 740;
			}elseif($parentescoDep3 != ''){
				$linhaParentesco = 755;
			}
			
			imagettftext($imagem, 11, 0, 120, 715, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 11, 0, 750, 715, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 11, 0, 1005, 715, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 1050, 715, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1080, 715, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 170, 748, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 11, 0, 750, 748, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 11, 0, 970, 748, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
			imagettftext($imagem, 10, 0, 1128, $linhaParentesco, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}	
		
		//Dependente 4
		if ($nomeDep4 != ''){
			$dataNasc = explode('/',$dataNascimentoDep4);		
			$linhaParentesco = '';
			
			if($parentescoDep4 == '1' || $parentescoDep4 == '2'){
				$linhaParentesco = 790;
			}elseif($parentescoDep4 == '21' || $parentescoDep4 == '13' || $parentescoDep4 == '10' || $parentescoDep4 == '3'){
				$linhaParentesco = 810;
			}elseif($parentescoDep4 != ''){
				$linhaParentesco = 825;
			}
			
			imagettftext($imagem, 11, 0, 120, 780, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
			imagettftext($imagem, 11, 0, 750, 780, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep4);
			imagettftext($imagem, 11, 0, 1005, 780, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 1050, 780, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1080, 780, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 170, 815, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep4);
			imagettftext($imagem, 11, 0, 750, 815, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep4);
			imagettftext($imagem, 11, 0, 970, 815, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep4);
			imagettftext($imagem, 10, 0, 1128, $linhaParentesco, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
		}

		setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo');
		
		imagettftext($imagem, 12, 0, 90, 1618, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 12, 0, 315, 1618, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 12, 0, 385, 1618, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));
		imagettftext($imagem, 12, 0, 618, 1618, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
		imagettftext($imagem, 12, 0, 60, 1685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));		
		
		$image_p = imagecreatetruecolor(1200, 1720);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
		

	}
}

if($_GET['modelo'] == 11){
	$codAssociadoTmp = $_GET['codAssociado'];
	$percentual = 0;
	$valorTotal = 0;
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
	$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
	$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
	$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL, ';
	$queryAssociado .= ' 	ESP0002.DESCRICAO_GRUPO_CONTRATO, VND1001_ON.CODIGO_BANCO, VND1001_ON.NUMERO_AGENCIA, VND1001_ON.NUMERO_CONTA ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN ESP0002 ON (VND1000_ON.CODIGO_GRUPO_CONTRATO = ESP0002.CODIGO_GRUPO_CONTRATO) ';
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	

	$resAssociado = jn_query($queryAssociado);
	if(!$rowAssociado = jn_fetch_object($resAssociado)){
		echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
		exit;
	}
	
	
	$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
	$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
	$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
	
	$resPerc = jn_query($queryPerc);
	while($rowPerc = jn_fetch_object($resPerc)){
		$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
	}
	
	$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	
	$valorTit = $rowValores->VALOR_PLANO;

	if($percentual > 0){
		$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
		$valorTit = ($rowValores->VALOR_PLANO + $calculo);
	}
	
	$valorTotal = ($valorTotal + $valorTit);

	//Tratativas para dependentes

	//Dependente 1
	$codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';

	$queryDep1  = ' SELECT ';
	$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep1 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep1 .= ' FROM VND1000_ON ';
	$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep1 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

	$resDep1 = jn_query($queryDep1);
	if($rowDep1 = jn_fetch_object($resDep1)){
		$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
		
		$diaNascDep1 = '';
		$mesNascDep1 = '';
		$anoNascDep1 = '';
		
		$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
		$diaNascDep1 = explode(' ', $diaNascDep1);
		$diaNascDep1 = $diaNascDep1[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep1 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep1);

		
		$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
		$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
		$numeroRGDep1 = $rowDep1->NUMERO_RG;
		$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
		$orgaoEmissorRGDep1 = $rowDep1->ORGAO_EMISSOR_RG;
		$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
		$sexoDep1 = $rowDep1->SEXO;
		$nomeMaeDep1 = $rowDep1->NOME_MAE;
		$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
		$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;
		$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
		$codigoEstCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
		$estadoCivilDep1 = $rowDep1->NOME_ESTADO_CIVIL;
		$pesoDep1 = $rowDep1->PESO;
		$alturaDep1 = $rowDep1->ALTURA;
	}

	//Dependente 2
	$codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';

	$queryDep2  = ' SELECT ';
	$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep2 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep2 .= ' FROM VND1000_ON ';
	$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep2 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep2 = jn_query($queryDep2);
	if($rowDep2 = jn_fetch_object($resDep2)){
		
		$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
		
		$diaNascDep2 = '';
		$mesNascDep2 = '';
		$anoNascDep2 = '';
		
		$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
		$diaNascDep2 = explode(' ', $diaNascDep2);
		$diaNascDep2 = $diaNascDep2[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep2 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep2);
		
		$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
		$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
		$numeroRGDep2 = $rowDep2->NUMERO_RG;
		$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
		$orgaoEmissorRGDep2 = $rowDep2->ORGAO_EMISSOR_RG;
		$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
		$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$sexoDep2 = $rowDep2->SEXO;
		$nomeMaeDep2 = $rowDep2->NOME_MAE;
		$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
		$parentescoDep2 = $rowDep2->NOME_PARENTESCO;
		$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
		$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;
		$estadoCivilDep2 = $rowDep2->NOME_ESTADO_CIVIL;
		$pesoDep2 = $rowDep2->PESO;
		$alturaDep2= $rowDep2->ALTURA;
	}

	//Dependente 3
	$codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';

	$queryDep3  = ' SELECT ';
	$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep3 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep3 .= ' FROM VND1000_ON ';
	$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep3 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep3 = jn_query($queryDep3);
	if($rowDep3 = jn_fetch_object($resDep3)){
		$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
		
		$diaNascDep3 = '';
		$mesNascDep3 = '';
		$anoNascDep3 = '';
		
		$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
		$diaNascDep3 = explode(' ', $diaNascDep3);
		$diaNascDep3 = $diaNascDep3[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep3 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep3);
		
		$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
		$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
		$numeroRGDep3 = $rowDep3->NUMERO_RG;
		$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$sexoDep3 = $rowDep3->SEXO;
		$nomeMaeDep3 = $rowDep3->NOME_MAE;
		$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
		$parentescoDep3 = $rowDep3->NOME_PARENTESCO;
		$estadoCivilDep3 = $rowDep3->NOME_ESTADO_CIVIL;
		$pesoDep3 = $rowDep3->PESO;
		$alturaDep3= $rowDep3->ALTURA;
		$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
		$orgaoEmissorRGDep3 = $rowDep3->ORGAO_EMISSOR_RG;
		$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);		
		$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;
		
	}
	
	//Dependente 4
	$codigoDep4 = explode('.',$codAssociadoTmp);
	$codigoDep4 = $codigoDep4[0] . '.4';

	$queryDep4  = ' SELECT ';
	$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, NOME_PARENTESCO, VND1000_ON.CODIGO_ESTADO_CIVIL, ';
	$queryDep4 .= ' 	NATUREZA_RG, DATA_EMISSAO_RG, ORGAO_EMISSOR_RG, NOME_ESTADO_CIVIL, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep4 .= ' FROM VND1000_ON ';
	$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep4 .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
	$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep4 = jn_query($queryDep4);
	if($rowDep4 = jn_fetch_object($resDep4)){
		
		$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
		
		$diaNascDep4 = '';
		$mesNascDep4 = '';
		$anoNascDep4 = '';
		
		$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
		$diaNascDep4 = explode(' ', $diaNascDep4);
		$diaNascDep4 = $diaNascDep4[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep4 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep4);
		
		$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
		$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
		$numeroRGDep4 = $rowDep4->NUMERO_RG;
		$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$sexoDep4 = $rowDep4->SEXO;
		$nomeMaeDep4 = $rowDep4->NOME_MAE;
		$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
		$parentescoDep4 = $rowDep4->NOME_PARENTESCO;
		$estadoCivilDep4 = $rowDep4->NOME_ESTADO_CIVIL;
		$pesoDep4 = $rowDep4->PESO;
		$alturaDep4= $rowDep4->ALTURA;
		$naturezaRGDep4 = $rowDep4->NATUREZA_RG;
		$orgaoEmissorRGDep4 = $rowDep4->ORGAO_EMISSOR_RG;
		$dataEmissaoRGDep4 = SqlToData($rowDep4->DATA_EMISSAO_RG);
		$numeroDecNascVivoDep4 = $rowDep4->NUMERO_DECLARACAO_NASC_VIVO;
	}
	
	if($_GET['pagina'] == '1'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta11_Vidamax1.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );		
		$dataNasc = explode('/',SqlToData($rowAssociado->DATA_NASCIMENTO));	
		$dataVigencia = explode('/',SqlToData($rowAssociado->DATA_ADMISSAO));	
		
		imagettftext($imagem, 11, 0, 400, 170, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->DESCRICAO_GRUPO_CONTRATO));
		imagettftext($imagem, 12, 0, 250, 210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataVigencia[0]));
		imagettftext($imagem, 12, 0, 320, 210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataVigencia[1]));
		imagettftext($imagem, 12, 0, 390, 210, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataVigencia[2]));
		imagettftext($imagem, 11, 0, 139, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 11, 0, 839, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('BRASILEIRO'));
		imagettftext($imagem, 12, 0, 240, 725, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 12, 0, 945, 725, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
		imagettftext($imagem, 12, 0, 165, 795, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNasc[0]));
		imagettftext($imagem, 12, 0, 270, 795, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNasc[1]));
		imagettftext($imagem, 12, 0, 480, 795, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNasc[2]));
		imagettftext($imagem, 12, 0, 880, 795, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 12, 0, 520, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
		imagettftext($imagem, 12, 0, 1060, 830, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
		imagettftext($imagem, 12, 0, 320, 870, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
		imagettftext($imagem, 12, 0, 440, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
		imagettftext($imagem, 12, 0, 810, 910, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
		imagettftext($imagem, 12, 0, 300, 960, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 12, 0, 370, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 12, 0, 600, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 12, 0, 840, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 12, 0, 900, 995, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 12, 0, 780, 1025, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
		
		
		
		//Dependente 1
		if($nomedep1 !=''){
			$dataNasc = explode('/',$dataNascimentoDep1);		
			$colunaSexo = '';
			
			if($sexoDep1 == 'M'){
				$colunaSexo = 205;
			}else{
				$colunaSexo = 287;		
			}
		
			
			if($parentescoDep1 == '1' || $parentescoDep1 == '2'){
				$colunaParentesco = 282;
			}elseif($parentescoDep1 == '4'){
				$colunaParentesco = 582;
			}elseif($parentescoDep1 == '21' || $parentescoDep1 == '13' || $parentescoDep1 == '10' || $parentescoDep1 == '3'){
				$colunaParentesco = 493;
			}elseif($parentescoDep1 == '5' || $parentescoDep1 == '31'){
				$colunaParentesco = 692;
			}else{
				$colunaParentesco = 778;
			}
		
			
			imagettftext($imagem, 10, 0, $colunaParentesco, 1125, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('X'));
			imagettftext($imagem, 11, 0, 210, 1165	, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
			imagettftext($imagem, 13, 0, $colunaSexo, 1198, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 11, 0, 600, 1198, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep1);
			imagettftext($imagem, 11, 0, 280, 1235, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep1);
			imagettftext($imagem, 11, 0, 820, 1235, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmisorDep1);
			imagettftext($imagem, 11, 0, 280, 1275, $cor,"../../Site/assets/img/arial.ttf",$estadoCivilDep1);
			imagettftext($imagem, 11, 0, 890, 1275, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 970, 1275, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1030, 1275, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 790, 1350, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep1);
			imagettftext($imagem, 11, 0, 340, 1380, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep1);
			imagettftext($imagem, 11, 0, 460, 1420, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep1);
		}
				
		imagettftext($imagem, 12, 0, 120, 1605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}	
	
	if($_GET['pagina'] == '2'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta11_Vidamax2.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );				
		
		//Dependente 2
		if($nomedep2 !=''){
			$dataNasc = explode('/',$dataNascimentoDep2);		
			$colunaSexo = '';

			if($sexoDep2 == 'M'){
				$colunaSexo = 205;
			}elseif($sexoDep2 == 'F'){
				$colunaSexo = 290;		
			}

			imagettftext($imagem, 11, 0, 210, 200	, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
			imagettftext($imagem, 13, 0, $colunaSexo, 235, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 11, 0, 600, 235, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep2);
			imagettftext($imagem, 11, 0, 280, 270, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep2);
			imagettftext($imagem, 11, 0, 820, 270, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmisorDep2);
			imagettftext($imagem, 11, 0, 890, 315, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 970, 315, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1030, 315, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 790, 380, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep2);
			imagettftext($imagem, 11, 0, 340, 420, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep2);
			imagettftext($imagem, 11, 0, 460, 455, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep2);
		}

		//Dependente 3
		if($nomedep3 !=''){
			$dataNasc = explode('/',$dataNascimentoDep3);		
			$colunaSexo = '';

			if($sexoDep3 == 'M'){
				$colunaSexo = 205;
			}elseif($sexoDep3 == 'F'){
				$colunaSexo = 290;		
			}

			imagettftext($imagem, 11, 0, 210, 550	, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
			imagettftext($imagem, 13, 0, $colunaSexo, 585, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 11, 0, 600, 585, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep3);
			imagettftext($imagem, 11, 0, 280, 620, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep3);
			imagettftext($imagem, 11, 0, 820, 620, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmisorDep3);
			imagettftext($imagem, 11, 0, 890, 665, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 970, 665, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1030, 665, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 790, 730, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep3);
			imagettftext($imagem, 11, 0, 340, 770, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep3);
			imagettftext($imagem, 11, 0, 460, 805, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep3);
		}

		//Dependente 4
		if($nomedep4 !=''){
			$dataNasc = explode('/',$dataNascimentoDep4);		
			$colunaSexo = '';

			if($sexoDep4 == 'M'){
				$colunaSexo = 205;
			}elseif($sexoDep4 == 'F'){
				$colunaSexo = 290;		
			}

			imagettftext($imagem, 11, 0, 210, 900	, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
			imagettftext($imagem, 13, 0, $colunaSexo, 935, $cor,"../../Site/assets/img/arial.ttf",'X');
			imagettftext($imagem, 11, 0, 600, 935, $cor,"../../Site/assets/img/arial.ttf",$numeroCPFDep4);
			imagettftext($imagem, 11, 0, 280, 970, $cor,"../../Site/assets/img/arial.ttf",$numeroRGDep4);
			imagettftext($imagem, 11, 0, 820, 970, $cor,"../../Site/assets/img/arial.ttf",$orgaoEmisorDep4);
			imagettftext($imagem, 11, 0, 890, 1010, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[0]);
			imagettftext($imagem, 11, 0, 970, 1010, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[1]);
			imagettftext($imagem, 11, 0, 1030, 1010, $cor,"../../Site/assets/img/arial.ttf",$dataNasc[2]);
			imagettftext($imagem, 11, 0, 790, 1080, $cor,"../../Site/assets/img/arial.ttf",$nomeMaeDep4);
			imagettftext($imagem, 11, 0, 340, 1120, $cor,"../../Site/assets/img/arial.ttf",$codigoCNSDep4);
			imagettftext($imagem, 11, 0, 460, 1155, $cor,"../../Site/assets/img/arial.ttf",$numeroDecNascVivoDep4);
		}	
		
		imagettftext($imagem, 11, 0, 150, 1630, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '3'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta11_Vidamax3.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );		
		
		imagettftext($imagem, 11, 0, 490, 255, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 11, 0, 250, 500, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->DESCRICAO_GRUPO_CONTRATO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '4'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta11_Vidamax4.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '5'){
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta11_Vidamax5.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );		
		
		setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo');
		
	
		imagettftext($imagem, 15, 0, 805, 530, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTit));
		imagettftext($imagem, 15, 0, 750, 1560, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 15, 0, 825, 1560, $cor,"../../Site/assets/img/arial.ttf",strftime('%B', strtotime('today')));
		imagettftext($imagem, 15, 0, 1090, 1560, $cor,"../../Site/assets/img/arial.ttf",date('y'));
		
		imagettftext($imagem, 11, 0, 400, 1275, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_BANCO));
		imagettftext($imagem, 11, 0, 400, 1305, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_AGENCIA));
		imagettftext($imagem, 11, 0, 400, 1335, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTA));
		imagettftext($imagem, 11, 0, 150, 1630, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
}

if($_GET['modelo'] == 12){
	$codAssociadoTmp = $_GET['codAssociado'];
	$percentual = 0;
	$valorTotal = 0;
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
	$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS,NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
	$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
	$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02,VND1001_ON.CODIGO_BANCO, VND1001_ON.NOME_BANCO, VND1001_ON.NUMERO_AGENCIA, VND1001_ON.NUMERO_CONTA,   ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';	
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssociado = jn_query($queryAssociado);
	if(!$rowAssociado = jn_fetch_object($resAssociado)){
		echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
		exit;
	}else{
		//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
	}
	
	$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
	$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
	$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
	
	$resPerc = jn_query($queryPerc);
	while($rowPerc = jn_fetch_object($resPerc)){
		$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
	}
	
	$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	
	$valorTit = $rowValores->VALOR_PLANO;
	if($percentual > 0){
		$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
		$valorTit = ($rowValores->VALOR_PLANO + $calculo);
	}
	
	$valorTotal = ($valorTotal + $valorTit);
	
	//Tratativas para dependentes

	//Dependente 1
	$codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';

	$queryDep1  = ' SELECT ';
	$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep1 .= ' FROM VND1000_ON ';
	$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

	$resDep1 = jn_query($queryDep1);
	if($rowDep1 = jn_fetch_object($resDep1)){
		$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
		
		$diaNascDep1 = '';
		$mesNascDep1 = '';
		$anoNascDep1 = '';
		
		$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
		$diaNascDep1 = explode(' ', $diaNascDep1);
		$diaNascDep1 = $diaNascDep1[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep1 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep1);

		
		$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
		$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
		$numeroRGDep1 = $rowDep1->NUMERO_RG;
		$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
		$sexoDep1 = $rowDep1->SEXO;
		$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
		$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
		$nomeMaeDep1 = $rowDep1->NOME_MAE;
		$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
		$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
		$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
		$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
		$pesoDep1 = $rowDep1->PESO;
		$alturaDep1 = $rowDep1->ALTURA;
	}

	//Dependente 2
	$codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';

	$queryDep2  = ' SELECT ';
	$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
	$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep2 .= ' FROM VND1000_ON ';
	$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep2 = jn_query($queryDep2);
	if($rowDep2 = jn_fetch_object($resDep2)){
		
		$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
		
		$diaNascDep2 = '';
		$mesNascDep2 = '';
		$anoNascDep2 = '';
		
		$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
		$diaNascDep2 = explode(' ', $diaNascDep2);
		$diaNascDep2 = $diaNascDep2[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep2 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep2);
		
		$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
		$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
		$numeroRGDep2 = $rowDep2->NUMERO_RG;
		$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
		$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
		$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
		$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
		$sexoDep2 = $rowDep2->SEXO;
		$nomeMaeDep2 = $rowDep2->NOME_MAE;
		$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
		$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
		$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
		$pesoDep2 = $rowDep2->PESO;
		$alturaDep2= $rowDep2->ALTURA;
	}

	//Dependente 3
	$codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';

	$queryDep3  = ' SELECT ';
	$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep3 .= ' FROM VND1000_ON ';
	$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep3 = jn_query($queryDep3);
	if($rowDep3 = jn_fetch_object($resDep3)){
		
		$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
		
		$diaNascDep3 = '';
		$mesNascDep3 = '';
		$anoNascDep3 = '';
		
		$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
		$diaNascDep3 = explode(' ', $diaNascDep3);
		$diaNascDep3 = $diaNascDep3[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep3 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep3);
		
		$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
		$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
		$numeroRGDep3 = $rowDep3->NUMERO_RG;
		$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
		$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
		$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
		$sexoDep3 = $rowDep3->SEXO;
		$nomeMaeDep3 = $rowDep3->NOME_MAE;
		$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
		$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
		$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
		$pesoDep3 = $rowDep3->PESO;
		$alturaDep3= $rowDep3->ALTURA;
		$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
	}

	//Dependente 4
	$codigoDep4 = explode('.',$codAssociadoTmp);
	$codigoDep4 = $codigoDep4[0] . '.4';

	$queryDep4  = ' SELECT ';
	$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep4 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep4 .= ' FROM VND1000_ON ';
	$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
	$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep4 = jn_query($queryDep4);
	if($rowDep4 = jn_fetch_object($resDep4)){
		$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
		
		$diaNascDep4 = '';
		$mesNascDep4 = '';
		$anoNascDep4 = '';
		
		$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
		$diaNascDep4 = explode(' ', $diaNascDep4);
		$diaNascDep4 = $diaNascDep4[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep4 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep4);
		
		$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
		$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
		$numeroRGDep4 = $rowDep4->NUMERO_RG;
		$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$dataEmissaoRGDep4 = SqlToData($rowDep4->DATA_EMISSAO_RG);
		$orgaoEmisorDep4 = $rowDep4->ORGAO_EMISSOR_RG;
		$naturezaRGDep4 = $rowDep4->NATUREZA_RG;
		$sexoDep4 = $rowDep4->SEXO;
		$nomeMaeDep4 = $rowDep4->NOME_MAE;
		$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
		$parentescoDep4 = $rowDep4->CODIGO_PARENTESCO;
		$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
		$pesoDep4 = $rowDep4->PESO;
		$alturaDep4= $rowDep4->ALTURA;
		$numeroDecNascVivoDep4 = $rowDep4->NUMERO_DECLARACAO_NASC_VIVO;	
	}

	//Dependente 5
	$codigoDep5 = explode('.',$codAssociadoTmp);
	$codigoDep5 = $codigoDep5[0] . '.5';

	$queryDep5  = ' SELECT ';
	$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep5 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep5 .= ' FROM VND1000_ON ';
	$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
	$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep5 = jn_query($queryDep5);
	if($rowDep5 = jn_fetch_object($resDep5)){
		$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
		$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
		
		$diaNascDep5 = '';
		$mesNascDep5 = '';
		$anoNascDep5 = '';
		
		$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
		$diaNascDep5 = explode(' ', $diaNascDep5);
		$diaNascDep5 = $diaNascDep5[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep5->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep5;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep5;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep5 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep5 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep5);
		
		$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
		$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
		$numeroRGDep5 = $rowDep5->NUMERO_RG;
		$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
		$dataEmissaoRGDep5 = SqlToData($rowDep5->DATA_EMISSAO_RG);
		$orgaoEmisorDep5 = $rowDep5->ORGAO_EMISSOR_RG;
		$naturezaRGDep5 = $rowDep5->NATUREZA_RG;
		$sexoDep5 = $rowDep5->SEXO;
		$nomeMaeDep5 = $rowDep5->NOME_MAE;
		$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
		$parentescoDep5 = $rowDep5->CODIGO_PARENTESCO;
		$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
		$pesoDep5 = $rowDep5->PESO;
		$alturaDep5= $rowDep5->ALTURA;
		$numeroDecNascVivoDep5 = $rowDep5->NUMERO_DECLARACAO_NASC_VIVO;	
	}

	
	if($_GET['pagina'] == '1'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta12_Vidamax1.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 15, 0, 625, 329, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 15, 0, 225, 1424, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 15, 0, 430, 1424, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 15, 0, 95, 1604, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '2'){
		$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
		$admissao = explode('/',$data);
		$diaAdmissao = $admissao[0];
		$mesAdmissao = $admissao[1];
		$anoAdmissao = $admissao[2];
		
		$colunaAcomodacao == 0;
		if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
			$colunaAcomodacao = 480;
		}else{
			$colunaAcomodacao = 285;
		}
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta12_Vidamax2.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );

		$queryEvento = 'SELECT sum(VALOR_FATOR) AS SOMA_VALOR_FATOR FROM VND1003_ON WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp) . ')';
		$resEvento = jn_query($queryEvento);
		$rowEvento = jn_fetch_object($resEvento);
		
		imagettftext($imagem, 12, 0, 355, 380, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
		imagettftext($imagem, 12, 0, 800, 380, $cor,"../../Site/assets/img/arial.ttf",toMoeda($rowEvento->SOMA_VALOR_FATOR));
		imagettftext($imagem, 14, 0, 1005, 102, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
		imagettftext($imagem, 14, 0, 165, 290, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
		imagettftext($imagem, 14, 0, 254, 345, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
		imagettftext($imagem, 10, 0, 170, 430, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 10, 0, 170, 476, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 10, 0, 330, 476, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
		imagettftext($imagem, 10, 0, 480, 476, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
		imagettftext($imagem, 10, 0, 630, 476, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
		imagettftext($imagem, 10, 0, 718, 476, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
		
		imagettftext($imagem, 10, 0, 780, 476, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
		imagettftext($imagem, 10, 0, 880, 476, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
		imagettftext($imagem, 10, 0, 975, 476, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 10, 0, 1120, 476, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
		imagettftext($imagem, 10, 0, 170, 524, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 10, 0, 170, 615, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
		imagettftext($imagem, 12, 0, 810, 615, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
		imagettftext($imagem, 10, 0, 170, 660, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 10, 0, 170, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 10, 0, 680, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 10, 0, 870, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 10, 0, 1115, 703, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 10, 0, 670, 748, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
		imagettftext($imagem, 10, 0, 170, 748, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
		imagettftext($imagem, 10, 0, 390, 748, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
		
		
		//Dep1
		imagettftext($imagem, 10, 0, 180, 804, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
		imagettftext($imagem, 10, 0, 1120, 804, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
		imagettftext($imagem, 10, 0, 180, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
		imagettftext($imagem, 10, 0, 350, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
		imagettftext($imagem, 10, 0, 505, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep1));
		imagettftext($imagem, 10, 0, 650, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
		imagettftext($imagem, 10, 0, 737, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep1));
		imagettftext($imagem, 10, 0, 820, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
		imagettftext($imagem, 10, 0, 885, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
		imagettftext($imagem, 10, 0, 990, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
		imagettftext($imagem, 10, 0, 1120, 851, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
		imagettftext($imagem, 10, 0, 180, 898, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
		imagettftext($imagem, 10, 0, 180, 945, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
		imagettftext($imagem, 10, 0, 780, 945, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));
		
		//Dep2
		imagettftext($imagem, 10, 0, 180, 988, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
		imagettftext($imagem, 10, 0, 1120, 988, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
		imagettftext($imagem, 10, 0, 180, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
		imagettftext($imagem, 10, 0, 360, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
		imagettftext($imagem, 10, 0, 505, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep2));
		imagettftext($imagem, 10, 0, 650, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));
		imagettftext($imagem, 10, 0, 737, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep2));
		imagettftext($imagem, 10, 0, 820, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));
		imagettftext($imagem, 10, 0, 885, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
		imagettftext($imagem, 10, 0, 990, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
		imagettftext($imagem, 10, 0, 1120, 1032, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
		imagettftext($imagem, 10, 0, 180, 1077, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
		imagettftext($imagem, 10, 0, 180, 1125, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
		imagettftext($imagem, 10, 0, 780, 1125, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));		
		
		//Dep3
		imagettftext($imagem, 10, 0, 180, 1169, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
		imagettftext($imagem, 10, 0, 1120, 1169, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
		imagettftext($imagem, 10, 0, 180, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
		imagettftext($imagem, 10, 0, 360, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
		imagettftext($imagem, 10, 0, 505, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep3));
		imagettftext($imagem, 10, 0, 650, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
		imagettftext($imagem, 10, 0, 737, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep3));
		imagettftext($imagem, 10, 0, 820, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
		imagettftext($imagem, 10, 0, 885, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
		imagettftext($imagem, 10, 0, 990, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
		imagettftext($imagem, 10, 0, 1120, 1215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
		imagettftext($imagem, 10, 0, 180, 1263, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
		imagettftext($imagem, 10, 0, 180, 1308, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
		imagettftext($imagem, 10, 0, 780, 1308, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));		

		//Dep 4
		imagettftext($imagem, 10, 0, 180, 1352, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
		imagettftext($imagem, 10, 0, 1120, 1352, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep4));
		imagettftext($imagem, 10, 0, 180, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
		imagettftext($imagem, 10, 0, 360, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
		imagettftext($imagem, 10, 0, 505, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep4));
		imagettftext($imagem, 10, 0, 650, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep4));
		imagettftext($imagem, 10, 0, 737, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep4));
		imagettftext($imagem, 10, 0, 820, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep4));
		imagettftext($imagem, 10, 0, 885, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
		imagettftext($imagem, 10, 0, 990, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep4));
		imagettftext($imagem, 10, 0, 1120, 1398, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep4));
		imagettftext($imagem, 10, 0, 180, 1443, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
		imagettftext($imagem, 10, 0, 180, 1488, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
		imagettftext($imagem, 10, 0, 780, 1488, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep4));		

		//Dep 5
		imagettftext($imagem, 10, 0, 180, 1534, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep5));
		imagettftext($imagem, 10, 0, 1120, 1534, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep5));
		imagettftext($imagem, 10, 0, 180, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep5));
		imagettftext($imagem, 10, 0, 360, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep5));
		imagettftext($imagem, 10, 0, 505, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep5));
		imagettftext($imagem, 10, 0, 650, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep5));
		imagettftext($imagem, 10, 0, 737, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep5));
		imagettftext($imagem, 10, 0, 820, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep5));
		imagettftext($imagem, 10, 0, 885, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep5));
		imagettftext($imagem, 10, 0, 990, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep5));
		imagettftext($imagem, 10, 0, 1120, 1578, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep5));
		imagettftext($imagem, 10, 0, 180, 1625, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep5));
		imagettftext($imagem, 10, 0, 180, 1670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep5));
		imagettftext($imagem, 10, 0, 780, 1670, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep5));	
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}


	if($_GET['pagina'] == '3'){
		$img = imagecreatefromjpeg("../../Site/assets/img/proposta12_Vidamax3.jpg");
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);
	}
	
	if($_GET['pagina'] == '4'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta12_Vidamax4.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '5'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta12_Vidamax5.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		$nomeBanco = '';
		
		if($rowAssociado->CODIGO_BANCO == '033' || $rowAssociado->CODIGO_BANCO == '33'){
			$nomeBanco = 'Santander';
		}elseif($rowAssociado->CODIGO_BANCO == '341'){
			$nomeBanco = 'Itaú';			
		}elseif($rowAssociado->CODIGO_BANCO == '1' || $rowAssociado->CODIGO_BANCO == '01' || $rowAssociado->CODIGO_BANCO == '001'){
			$nomeBanco = 'Banco do Brasil';			
		}
		
				
		imagettftext($imagem, 12, 0, 300, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeBanco));
		imagettftext($imagem, 12, 0, 660, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_BANCO));
		imagettftext($imagem, 12, 0, 830, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_AGENCIA));
		imagettftext($imagem, 12, 0, 1020, 1455, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTA));
		imagettftext($imagem, 12, 0, 152, 1540, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 12, 0, 370, 1540, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	
	}
}

if($_GET['modelo'] == 36){
	$codAssociadoTmp = $_GET['codAssociado'];
	$percentual = 0;
	$valorTotal = 0;
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, VND1000_ON.SEXO, VND1000_ON.NOME_MAE, VND1000_ON.CODIGO_PARENTESCO, VND1001_ON.CODIGO_VENDEDOR, VND1000_ON.DATA_ADMISSAO, ';
	$queryAssociado .= ' 	VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, CODIGO_CNS,NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_GRUPO_CONTRATO, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.CODIGO_ESTADO_CIVIL, VND1000_ON.CODIGO_CNS, ';
	$queryAssociado .= ' 	VND1000_ON.DESC_CIRURGIA, VND1000_ON.TEMPO_CIRURGIA, VND1000_ON.PROCEDIMENTO_CIRURGICO, VND1000_ON.EXAMES_ULTIMOS_MESES, VND1000_ON.MOTIVO_INTERNACAO, ';
	$queryAssociado .= ' 	VND1000_ON.PERIODO_INICIAL, VND1000_ON.PERIODO_FINAL, VND1000_ON.OUTRAS_OBSERVACOES, VND1000_ON.NATUREZA_RG, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02,VND1001_ON.CODIGO_BANCO, VND1001_ON.NOME_BANCO, VND1001_ON.NUMERO_AGENCIA, VND1001_ON.NUMERO_CONTA,   ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1102.NUMERO_CPF AS CPF_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_ACOMODACAO, PS1044.NOME_ESTADO_CIVIL ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' LEFT JOIN PS1044 ON (PS1044.CODIGO_ESTADO_CIVIL = VND1000_ON.CODIGO_ESTADO_CIVIL) ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';	
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);	
	$resAssociado = jn_query($queryAssociado);
	if(!$rowAssociado = jn_fetch_object($resAssociado)){
		echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
		exit;
	}else{
		//jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
	}
	
	$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
	$queryPerc .= ' WHERE PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowAssociado->CODIGO_PLANO . '%');			
	$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);			
	$resPerc = jn_query($queryPerc);
	while($rowPerc = jn_fetch_object($resPerc)){
		$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
	}
	
	$idadeTit = calcularIdade($rowAssociado->DATA_NASCIMENTO);
	$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
	$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
	$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeTit;
	$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeTit;		
	$resValores = jn_query($queryValores);
	$rowValores = jn_fetch_object($resValores);
	
	$valorTit = $rowValores->VALOR_PLANO;
	if($percentual > 0){
		$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
		$valorTit = ($rowValores->VALOR_PLANO + $calculo);
	}
	
	$valorTotal = ($valorTotal + $valorTit);
	
	//Tratativas para dependentes

	//Dependente 1
	$codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';

	$queryDep1  = ' SELECT ';
	$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep1 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep1 .= ' FROM VND1000_ON ';
	$queryDep1 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

	$resDep1 = jn_query($queryDep1);
	if($rowDep1 = jn_fetch_object($resDep1)){
		$dtNascDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$idadeDep1 = calcularIdade($rowDep1->DATA_NASCIMENTO);
		
		$diaNascDep1 = '';
		$mesNascDep1 = '';
		$anoNascDep1 = '';
		
		$listNasc = list($diaNascDep1, $mesNascDep1, $anoNascDep1) = explode('/', $dtNascDep1);
		$diaNascDep1 = explode(' ', $diaNascDep1);
		$diaNascDep1 = $diaNascDep1[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep1 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep1 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep1);

		
		$nomeDep1 = $rowDep1->NOME_ASSOCIADO;
		$numeroCPFDep1 = $rowDep1->NUMERO_CPF;
		$numeroRGDep1 = $rowDep1->NUMERO_RG;
		$dataNascimentoDep1 = SqlToData($rowDep1->DATA_NASCIMENTO);
		$dataEmissaoRGDep1 = SqlToData($rowDep1->DATA_EMISSAO_RG);
		$sexoDep1 = $rowDep1->SEXO;
		$orgaoEmisorDep1 = $rowDep1->ORGAO_EMISSOR_RG;
		$naturezaRGDep1 = $rowDep1->NATUREZA_RG;
		$nomeMaeDep1 = $rowDep1->NOME_MAE;
		$codigoCNSDep1 = $rowDep1->CODIGO_CNS;
		$numeroDecNascVivoDep1 = $rowDep1->NUMERO_DECLARACAO_NASC_VIVO;		
		$parentescoDep1 = $rowDep1->CODIGO_PARENTESCO;
		$estadoCivilDep1 = $rowDep1->CODIGO_ESTADO_CIVIL;
		$pesoDep1 = $rowDep1->PESO;
		$alturaDep1 = $rowDep1->ALTURA;
	}

	//Dependente 2
	$codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';

	$queryDep2  = ' SELECT ';
	$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL,  CODIGO_GRUPO_CONTRATO, ';
	$queryDep2 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep2 .= ' FROM VND1000_ON ';
	$queryDep2 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$queryDep2 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep2 = jn_query($queryDep2);
	if($rowDep2 = jn_fetch_object($resDep2)){
		
		$dtNascDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$idadeDep2 = calcularIdade($rowDep2->DATA_NASCIMENTO);
		
		$diaNascDep2 = '';
		$mesNascDep2 = '';
		$anoNascDep2 = '';
		
		$listNasc = list($diaNascDep2, $mesNascDep2, $anoNascDep2) = explode('/', $dtNascDep2);
		$diaNascDep2 = explode(' ', $diaNascDep2);
		$diaNascDep2 = $diaNascDep2[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep2 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep2 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep2);
		
		$nomeDep2 = $rowDep2->NOME_ASSOCIADO;
		$numeroCPFDep2 = $rowDep2->NUMERO_CPF;
		$numeroRGDep2 = $rowDep2->NUMERO_RG;
		$dataNascimentoDep2 = SqlToData($rowDep2->DATA_NASCIMENTO);
		$dataEmissaoRGDep2 = SqlToData($rowDep2->DATA_EMISSAO_RG);
		$orgaoEmisorDep2 = $rowDep2->ORGAO_EMISSOR_RG;
		$naturezaRGDep2 = $rowDep2->NATUREZA_RG;
		$numeroDecNascVivoDep2 = $rowDep2->NUMERO_DECLARACAO_NASC_VIVO;		
		$sexoDep2 = $rowDep2->SEXO;
		$nomeMaeDep2 = $rowDep2->NOME_MAE;
		$codigoCNSDep2 = $rowDep2->CODIGO_CNS;
		$parentescoDep2 = $rowDep2->CODIGO_PARENTESCO;
		$estadoCivilDep2 = $rowDep2->CODIGO_ESTADO_CIVIL;
		$pesoDep2 = $rowDep2->PESO;
		$alturaDep2= $rowDep2->ALTURA;
	}

	//Dependente 3
	$codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';

	$queryDep3  = ' SELECT ';
	$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep3 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep3 .= ' FROM VND1000_ON ';
	$queryDep3 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	$queryDep3 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep3 = jn_query($queryDep3);
	if($rowDep3 = jn_fetch_object($resDep3)){
		
		$dtNascDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$idadeDep3 = calcularIdade($rowDep3->DATA_NASCIMENTO);
		
		$diaNascDep3 = '';
		$mesNascDep3 = '';
		$anoNascDep3 = '';
		
		$listNasc = list($diaNascDep3, $mesNascDep3, $anoNascDep3) = explode('/', $dtNascDep3);
		$diaNascDep3 = explode(' ', $diaNascDep3);
		$diaNascDep3 = $diaNascDep3[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep3 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep3 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep3);
		
		$nomeDep3 = $rowDep3->NOME_ASSOCIADO;
		$numeroCPFDep3 = $rowDep3->NUMERO_CPF;
		$numeroRGDep3 = $rowDep3->NUMERO_RG;
		$dataNascimentoDep3 = SqlToData($rowDep3->DATA_NASCIMENTO);
		$dataEmissaoRGDep3 = SqlToData($rowDep3->DATA_EMISSAO_RG);
		$orgaoEmisorDep3 = $rowDep3->ORGAO_EMISSOR_RG;
		$naturezaRGDep3 = $rowDep3->NATUREZA_RG;
		$sexoDep3 = $rowDep3->SEXO;
		$nomeMaeDep3 = $rowDep3->NOME_MAE;
		$codigoCNSDep3 = $rowDep3->CODIGO_CNS;
		$parentescoDep3 = $rowDep3->CODIGO_PARENTESCO;
		$estadoCivilDep3 = $rowDep3->CODIGO_ESTADO_CIVIL;
		$pesoDep3 = $rowDep3->PESO;
		$alturaDep3= $rowDep3->ALTURA;
		$numeroDecNascVivoDep3 = $rowDep3->NUMERO_DECLARACAO_NASC_VIVO;	
	}

	//Dependente 4
	$codigoDep4 = explode('.',$codAssociadoTmp);
	$codigoDep4 = $codigoDep4[0] . '.4';

	$queryDep4  = ' SELECT ';
	$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep4 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep4 .= ' FROM VND1000_ON ';
	$queryDep4 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep4 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
	$queryDep4 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep4 = jn_query($queryDep4);
	if($rowDep4 = jn_fetch_object($resDep4)){
		$dtNascDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$idadeDep4 = calcularIdade($rowDep4->DATA_NASCIMENTO);
		
		$diaNascDep4 = '';
		$mesNascDep4 = '';
		$anoNascDep4 = '';
		
		$listNasc = list($diaNascDep4, $mesNascDep4, $anoNascDep4) = explode('/', $dtNascDep4);
		$diaNascDep4 = explode(' ', $diaNascDep4);
		$diaNascDep4 = $diaNascDep4[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep4 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep4 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep4);
		
		$nomeDep4 = $rowDep4->NOME_ASSOCIADO;
		$numeroCPFDep4 = $rowDep4->NUMERO_CPF;
		$numeroRGDep4 = $rowDep4->NUMERO_RG;
		$dataNascimentoDep4 = SqlToData($rowDep4->DATA_NASCIMENTO);
		$dataEmissaoRGDep4 = SqlToData($rowDep4->DATA_EMISSAO_RG);
		$orgaoEmisorDep4 = $rowDep4->ORGAO_EMISSOR_RG;
		$naturezaRGDep4 = $rowDep4->NATUREZA_RG;
		$sexoDep4 = $rowDep4->SEXO;
		$nomeMaeDep4 = $rowDep4->NOME_MAE;
		$codigoCNSDep4 = $rowDep4->CODIGO_CNS;
		$parentescoDep4 = $rowDep4->CODIGO_PARENTESCO;
		$estadoCivilDep4 = $rowDep4->CODIGO_ESTADO_CIVIL;
		$pesoDep4 = $rowDep4->PESO;
		$alturaDep4= $rowDep4->ALTURA;
		$numeroDecNascVivoDep4 = $rowDep4->NUMERO_DECLARACAO_NASC_VIVO;	
	}

	//Dependente 5
	$codigoDep5 = explode('.',$codAssociadoTmp);
	$codigoDep5 = $codigoDep5[0] . '.5';

	$queryDep5  = ' SELECT ';
	$queryDep5 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, VND1000_ON.CODIGO_PARENTESCO,  VND1000_ON.CODIGO_ESTADO_CIVIL, CODIGO_GRUPO_CONTRATO, ';
	$queryDep5 .= ' 	PESO, ALTURA, VND1000_ON.DATA_EMISSAO_RG, VND1000_ON.ORGAO_EMISSOR_RG, VND1000_ON.NATUREZA_RG ';
	$queryDep5 .= ' FROM VND1000_ON ';
	$queryDep5 .= ' LEFT JOIN PS1045 ON (VND1000_ON.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ';
	$queryDep5 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep5 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep5 .= ' AND  VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep5);
	$queryDep5 .= ' ORDER BY CODIGO_ASSOCIADO ';
		
	$resDep5 = jn_query($queryDep5);
	if($rowDep5 = jn_fetch_object($resDep5)){
		$dtNascDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
		$idadeDep5 = calcularIdade($rowDep5->DATA_NASCIMENTO);
		
		$diaNascDep5 = '';
		$mesNascDep5 = '';
		$anoNascDep5 = '';
		
		$listNasc = list($diaNascDep5, $mesNascDep5, $anoNascDep5) = explode('/', $dtNascDep5);
		$diaNascDep5 = explode(' ', $diaNascDep5);
		$diaNascDep5 = $diaNascDep5[0];
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep5->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep5;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep5;		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		
		$valorDep5 = $rowValores->VALOR_PLANO;
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valorDep5 = ($rowValores->VALOR_PLANO + $calculo);
		}
		
		$valorTotal = ($valorTotal + $valorDep5);
		
		$nomeDep5 = $rowDep5->NOME_ASSOCIADO;
		$numeroCPFDep5 = $rowDep5->NUMERO_CPF;
		$numeroRGDep5 = $rowDep5->NUMERO_RG;
		$dataNascimentoDep5 = SqlToData($rowDep5->DATA_NASCIMENTO);
		$dataEmissaoRGDep5 = SqlToData($rowDep5->DATA_EMISSAO_RG);
		$orgaoEmisorDep5 = $rowDep5->ORGAO_EMISSOR_RG;
		$naturezaRGDep5 = $rowDep5->NATUREZA_RG;
		$sexoDep5 = $rowDep5->SEXO;
		$nomeMaeDep5 = $rowDep5->NOME_MAE;
		$codigoCNSDep5 = $rowDep5->CODIGO_CNS;
		$parentescoDep5 = $rowDep5->CODIGO_PARENTESCO;
		$estadoCivilDep5 = $rowDep5->CODIGO_ESTADO_CIVIL;
		$pesoDep5 = $rowDep5->PESO;
		$alturaDep5= $rowDep5->ALTURA;
		$numeroDecNascVivoDep5 = $rowDep5->NUMERO_DECLARACAO_NASC_VIVO;	
	}

	
	if($_GET['pagina'] == '1'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta36_Vidamax1.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		imagettftext($imagem, 15, 0, 625, 329, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 15, 0, 225, 1424, $cor,"../../Site/assets/img/arial.ttf",date('d'));
		imagettftext($imagem, 15, 0, 430, 1424, $cor,"../../Site/assets/img/arial.ttf",date('m'));
		imagettftext($imagem, 15, 0, 95, 1604, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}
	
	if($_GET['pagina'] == '2'){
		$data = SqlToData($rowAssociado->DATA_ADMISSAO); 
		$admissao = explode('/',$data);
		$diaAdmissao = $admissao[0];
		$mesAdmissao = $admissao[1];
		$anoAdmissao = $admissao[2];
		
		$colunaAcomodacao == 0;
		if($rowAssociado->CODIGO_TIPO_ACOMODACAO == 1){
			$colunaAcomodacao = 480;
		}else{
			$colunaAcomodacao = 285;
		}
		
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta36_Vidamax2.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );

		$queryEvento = 'SELECT sum(VALOR_FATOR) AS SOMA_VALOR_FATOR FROM VND1003_ON WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp) . ')';
		$resEvento = jn_query($queryEvento);
		$rowEvento = jn_fetch_object($resEvento);
		
		imagettftext($imagem, 12, 0, 355, 365, $cor,"../../Site/assets/img/arial.ttf",toMoeda($valorTotal));
		imagettftext($imagem, 12, 0, 800, 365, $cor,"../../Site/assets/img/arial.ttf",toMoeda($rowEvento->SOMA_VALOR_FATOR));
		imagettftext($imagem, 14, 0, 1005, 90, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTRATO));		
		imagettftext($imagem, 14, 0, 165, 275, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_ADMISSAO));
		imagettftext($imagem, 14, 0, 254, 325, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
		imagettftext($imagem, 10, 0, 170, 415, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));
		imagettftext($imagem, 10, 0, 170, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CPF));
		imagettftext($imagem, 10, 0, 330, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_RG));
		imagettftext($imagem, 10, 0, 480, 460, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_EMISSAO_RG));
		imagettftext($imagem, 10, 0, 630, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ORGAO_EMISSOR_RG));
		imagettftext($imagem, 10, 0, 718, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NATUREZA_RG));
		
		imagettftext($imagem, 10, 0, 780, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ESTADO_CIVIL));
		imagettftext($imagem, 10, 0, 880, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->SEXO));
		imagettftext($imagem, 10, 0, 975, 460, $cor,"../../Site/assets/img/arial.ttf",SqlToData($rowAssociado->DATA_NASCIMENTO));
		imagettftext($imagem, 10, 0, 1120, 460, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeTit));
		imagettftext($imagem, 10, 0, 170, 505, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_MAE));
		imagettftext($imagem, 10, 0, 170, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_CNS));
		imagettftext($imagem, 12, 0, 810, 595, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_DECLARACAO_NASC_VIVO));
		imagettftext($imagem, 10, 0, 170, 645, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO));
		imagettftext($imagem, 10, 0, 170, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->BAIRRO));
		imagettftext($imagem, 10, 0, 680, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
		imagettftext($imagem, 10, 0, 870, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CIDADE));
		imagettftext($imagem, 10, 0, 1115, 685, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
		imagettftext($imagem, 10, 0, 670, 733, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ENDERECO_EMAIL));
		imagettftext($imagem, 10, 0, 170, 733, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
		imagettftext($imagem, 10, 0, 390, 733, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_02));
		
		
		//Dep1
		imagettftext($imagem, 10, 0, 180, 785, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep1));
		imagettftext($imagem, 10, 0, 1120, 785, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep1));
		imagettftext($imagem, 10, 0, 180, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep1));
		imagettftext($imagem, 10, 0, 350, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep1));
		imagettftext($imagem, 10, 0, 505, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep1));
		imagettftext($imagem, 10, 0, 650, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep1));
		imagettftext($imagem, 10, 0, 737, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep1));
		imagettftext($imagem, 10, 0, 820, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep1));
		imagettftext($imagem, 10, 0, 885, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep1));
		imagettftext($imagem, 10, 0, 990, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep1));
		imagettftext($imagem, 10, 0, 1120, 835, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep1));
		imagettftext($imagem, 10, 0, 180, 875, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep1));
		imagettftext($imagem, 10, 0, 180, 930, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep1));
		imagettftext($imagem, 10, 0, 780, 930, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep1));
		
		//Dep2
		imagettftext($imagem, 10, 0, 180, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep2));
		imagettftext($imagem, 10, 0, 1120, 970, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep2));
		imagettftext($imagem, 10, 0, 180, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep2));
		imagettftext($imagem, 10, 0, 360, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep2));
		imagettftext($imagem, 10, 0, 505, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep2));
		imagettftext($imagem, 10, 0, 650, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep2));
		imagettftext($imagem, 10, 0, 737, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep2));
		imagettftext($imagem, 10, 0, 820, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep2));
		imagettftext($imagem, 10, 0, 885, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep2));
		imagettftext($imagem, 10, 0, 990, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep2));
		imagettftext($imagem, 10, 0, 1120, 1015, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep2));
		imagettftext($imagem, 10, 0, 180, 1060, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep2));
		imagettftext($imagem, 10, 0, 180, 1110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep2));
		imagettftext($imagem, 10, 0, 780, 1110, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep2));		
		
		//Dep3
		imagettftext($imagem, 10, 0, 180, 1154, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep3));
		imagettftext($imagem, 10, 0, 1120, 1154, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep3));
		imagettftext($imagem, 10, 0, 180, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep3));
		imagettftext($imagem, 10, 0, 360, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep3));
		imagettftext($imagem, 10, 0, 505, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep3));
		imagettftext($imagem, 10, 0, 650, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep3));
		imagettftext($imagem, 10, 0, 737, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep3));
		imagettftext($imagem, 10, 0, 820, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep3));
		imagettftext($imagem, 10, 0, 885, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep3));
		imagettftext($imagem, 10, 0, 990, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep3));
		imagettftext($imagem, 10, 0, 1120, 1200, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep3));
		imagettftext($imagem, 10, 0, 180, 1247, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep3));
		imagettftext($imagem, 10, 0, 180, 1288, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep3));
		imagettftext($imagem, 10, 0, 780, 1288, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep3));		

		//Dep 4
		imagettftext($imagem, 10, 0, 180, 1337, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep4));
		imagettftext($imagem, 10, 0, 1120, 1337, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep4));
		imagettftext($imagem, 10, 0, 180, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep4));
		imagettftext($imagem, 10, 0, 360, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep4));
		imagettftext($imagem, 10, 0, 505, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep4));
		imagettftext($imagem, 10, 0, 650, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep4));
		imagettftext($imagem, 10, 0, 737, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep4));
		imagettftext($imagem, 10, 0, 820, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep4));
		imagettftext($imagem, 10, 0, 885, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep4));
		imagettftext($imagem, 10, 0, 990, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep4));
		imagettftext($imagem, 10, 0, 1120, 1373, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep4));
		imagettftext($imagem, 10, 0, 180, 1428, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep4));
		imagettftext($imagem, 10, 0, 180, 1473, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep4));
		imagettftext($imagem, 10, 0, 780, 1473, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep4));		

		//Dep 5
		imagettftext($imagem, 10, 0, 180, 1519, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeDep5));
		imagettftext($imagem, 10, 0, 1120, 1519, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($parentescoDep5));
		imagettftext($imagem, 10, 0, 180, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroCPFDep5));
		imagettftext($imagem, 10, 0, 360, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroRGDep5));
		imagettftext($imagem, 10, 0, 505, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataEmissaoRGDep5));
		imagettftext($imagem, 10, 0, 650, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($orgaoEmisorDep5));
		imagettftext($imagem, 10, 0, 737, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($naturezaRGDep5));
		imagettftext($imagem, 10, 0, 820, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($estadoCivilDep5));
		imagettftext($imagem, 10, 0, 885, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($sexoDep5));
		imagettftext($imagem, 10, 0, 990, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($dataNascimentoDep5));
		imagettftext($imagem, 10, 0, 1120, 1563, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($idadeDep5));
		imagettftext($imagem, 10, 0, 180, 1605, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeMaeDep5));
		imagettftext($imagem, 10, 0, 180, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($codigoCNSDep5));
		imagettftext($imagem, 10, 0, 780, 1655, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numeroDecNascVivoDep5));	
		
		
		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	}


	if($_GET['pagina'] == '3'){
		$img = imagecreatefromjpeg("../../Site/assets/img/proposta36_Vidamax3.jpg");
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);
	}
	
	if($_GET['pagina'] == '4'){
		$img = imagecreatefromjpeg('../../Site/assets/img/proposta36_Vidamax4.jpg');
		header( "Content-type: image/jpeg" );
		return imagejpeg( $img, NULL);	
	}
	
	if($_GET['pagina'] == '5'){
		$imagem = imagecreatefromjpeg("../../Site/assets/img/proposta36_Vidamax5.jpg");	
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		$nomeBanco = '';
		
		if($rowAssociado->CODIGO_BANCO == '033' || $rowAssociado->CODIGO_BANCO == '33'){
			$nomeBanco = 'Santander';
		}elseif($rowAssociado->CODIGO_BANCO == '341'){
			$nomeBanco = 'Itaú';			
		}elseif($rowAssociado->CODIGO_BANCO == '1' || $rowAssociado->CODIGO_BANCO == '01' || $rowAssociado->CODIGO_BANCO == '001'){
			$nomeBanco = 'Banco do Brasil';			
		}
		
		
		imagettftext($imagem, 12, 0, 300, 1465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($nomeBanco));		
		imagettftext($imagem, 12, 0, 660, 1465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CODIGO_BANCO));
		imagettftext($imagem, 12, 0, 830, 1465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_AGENCIA));
		imagettftext($imagem, 12, 0, 1020, 1465, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_CONTA));
		imagettftext($imagem, 12, 0, 152, 1550, $cor,"../../Site/assets/img/arial.ttf",date('d/m/Y'));
		imagettftext($imagem, 12, 0, 370, 1550, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_ASSOCIADO));

		$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
	
	}
}

function calcularIdade($date){	
	if(!$date){
		return null;
	}
	
	$date = SqlToData($date);
	$date = dataToSql($date);
	$date = str_replace("'",'',$date);

    // separando yyyy, mm, ddd
    list($ano, $dia, $mes) = explode('-', $date);
	$dia = substr($dia, 0, 2);
	
    // data atual
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
	
    // cálculo
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
}

function valorPorExtenso($valor=0) {

	$singular = array('centavo', 'real', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão'); 
	$plural = array('centavos', 'reais', 'mil', 'milhões', 'bilhões', 'trilhões','quatrilhões'); 
	$c = array('', 'cem', 'duzentos', 'trezentos', 'quatrocentos','quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'); 
	$d = array('', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta','sessenta', 'setenta', 'oitenta', 'noventa'); 
	$d10 = array('dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze','dezesseis', 'dezesete', 'dezoito', 'dezenove'); 
	$u = array('', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis','sete', 'oito', 'nove'); 
	$z=0; 
	$valor = number_format($valor, 2, '.', '.'); 
	$inteiro = explode('.', $valor); 
	$count = count($inteiro); 
	for($i=0;$i<$count;$i++) 
	for($ii=strlen($inteiro[$i]);$ii<3;$ii++) $inteiro[$i] = "0".$inteiro[$i]; 

	// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ???? 
	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2); 
	for ($i=0;$i < count($inteiro);$i++) { 
		$valor = $inteiro[$i]; 
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]]; 
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]]; 
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : ''; 
		$r = $rc.(($rc && ($rd || $ru)) ? ' e ' : '').$rd.(($rd && $ru) ? ' e ' : '').$ru; 
		$t = count($inteiro)-1-$i; 
		$r .= $r ? ' '.($valor > 1 ? $plural[$t] : $singular[$t]) : ''; 
		if ($valor == '000')	
			$z++; 
		elseif ($z > 0) 
			$z–; 
		
		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? ' de ' : '').$plural[$t]; 
		$rt = (!isset($rt)) ? '' : $rt; 
		if ($r) 
			$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r; 
	} 
	
	return($rt ? $rt : "zero"); 
}

?>