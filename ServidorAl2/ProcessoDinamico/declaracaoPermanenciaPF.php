<?php
require('../lib/base.php');		
header("Content-Type: text/html; charset=ISO-8859-1",true);

$ano   = isset($_POST['ANO']) ? $_POST['ANO'] : $_GET['ano'];
$codigoBeneficiario = isset($_GET['codAssociado']) ? $_GET['codAssociado'] : $_SESSION['codigoIdentificacao'];

$queryBeneficiarios = 'Select * from PS1000 where CODIGO_TITULAR = ' . aspas($codigoBeneficiario);
$resBeneficiarios=jn_query($queryBeneficiarios);

$i = 0;
$ArrAssoc = array();
$qtdeAssociados = 0;
while($rowBeneficiarios=jn_fetch_object($resBeneficiarios)){
	$ArrAssoc[$i]['CODIGO_ASSOCIADO'] 	= $rowBeneficiarios->CODIGO_ASSOCIADO;
	$ArrAssoc[$i]['TIPO_ASSOCIADO']		= $rowBeneficiarios->TIPO_ASSOCIADO;	
    $ArrAssoc[$i]['NOME_ASSOCIADO']		= $rowBeneficiarios->NOME_ASSOCIADO;    	
	$ArrAssoc[$i]['DATA_NASCIMENTO']	= $rowBeneficiarios->DATA_NASCIMENTO;
	$ArrAssoc[$i]['NUMERO_CPF']			= $rowBeneficiarios->NUMERO_CPF;
	$ArrAssoc[$i]['DATA_ADESAO']		= $rowBeneficiarios->DATA_ADMISSAO;


	$i++;
}

$qtdeAssociados = $i;

$queryDados        =  ' Select first 13 * from VW_DECLARACAO_PERM_PF where VW_DECLARACAO_PERM_PF.Codigo_Associado = ' . aspas($codigoBeneficiario);
$queryDados 		.=  ' and CAST(EXTRACT(YEAR FROM VW_DECLARACAO_PERM_PF.Data_Vencimento) AS INTEGER ) = ' . aspas($ano);
$queryDados 		.=  ' order by VW_DECLARACAO_PERM_PF.Data_Vencimento';
$resDados			 =	jn_query($queryDados);

$i = 0;
$ArrDados = array();

while($rowDados = jn_fetch_object($resDados)){
	//Planos
	$ArrDados[$i]['NOME_PLANO_FAMILIARES'] 		= $rowDados->NOME_PLANO_FAMILIARES;
	$ArrDados[$i]['NUMERO_REGISTRO_ANS'] 			= $rowDados->NUMERO_REGISTRO_ANS;
	$ArrDados[$i]['TIPO_CONTRATACAO_ANS'] 			= $rowDados->TIPO_CONTRATACAO_ANS;
	$ArrDados[$i]['ACOMODACAO'] 						= $rowDados->ACOMODACAO;
	$ArrDados[$i]['ABRANGENCIA_GEOGRAFICA'] 		= $rowDados->ABRANGENCIA_GEOGRAFICA;
	$ArrDados[$i]['SEGMENTACAO'] 						= $rowDados->SEGMENTACAO;
	$ArrDados[$i]['FLAG_APLICAR_CPT']				= $rowDados->FLAG_APLICAR_CPT;
	$ArrDados[$i]['SITUACAO_PLANO']				   = $rowDados->SITUACAO_PLANO;
	$ArrDados[$i]['NUMERO_CONTRATO']				   = $rowDados->NUMERO_CONTRATO;

	//Faturas
	$ArrDados[$i]['DATA_VENCIMENTO']					=	$rowDados->DATA_VENCIMENTO;
	$ArrDados[$i]['DATA_PAGAMENTO'] 				   =  $rowDados->DATA_PAGAMENTO;
	$ArrDados[$i]['VALOR_FATURA']						=	$rowDados->VALOR_FATURA;
	$ArrDados[$i]['VALOR_PAGO']						=	$rowDados->VALOR_PAGO;
	
	$i++;
}


//Imagem e posições//

if ($qtdeAssociados == 1){

	$imagem = imagecreatefromjpeg("img/DeclPerman_01_PF.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 190;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato

	//Posições Beneficiários
	$posicaoXCodAssoc  = 210; 
	$posicaoYCodAssoc    = 1154; 

	$posicaoXTipo      = 820; 
	$posicaoYTipo      = 1154;
		
	$posicaoXNome      = 963; 
	$posicaoYNome      = 1154; 
		
	$posicaoXData      = 1894; 
	$posicaoYData      = 1154;
		
	$posicaoXCpf    	 = 2200; 
	$posicaoYCpf       = 1154;
		
	$posicaoXAdesao    = 2555; 
	$posicaoYAdesao    = 1154; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 1420; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 1420;
		
	$posicaoXContratacao       = 1125; 
	$posicaoYContratacao       = 1420; 
		
	$posicaoXAcomodacao        = 1595; 
	$posicaoYAcomodacao        = 1420;
		
	$posicaoXAbrangencia   	   = 1950; 
	$posicaoYAbrangencia       = 1420;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 1420; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim    = 1395; //FlagCPTSim
	$posicaoYCptSim    = 1900;

	$posicaoXCptNao    = 1540; //FlagCPTNao
	$posicaoYCptNao    = 1900;

	$posicaoXDtExcl    = 300;
	$posicaoYDtExcl    = 2165;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase = "Declaramos para os devidos fins que o plano de saúde encontra-se " . $ArrDados[0]['SITUACAO_PLANO'] . ", abaixo últimos pagamentos:";



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,102);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];
		
		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl-30, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl+30, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano	

	//Faturas
	$tamanhoLetra = 21;
		
	$posicaoXDtVenc  		= 310; //Data Vencimento
	$posicaoYDtVenc 		= 2340; 
		
	$posicaoXDtPag 		= 705; //Data Pagamento
	$posicaoYDtPag 		= 2340;
		
	$posicaoXVlrFatura   = 1200; //Valor Fatura
	$posicaoYVlrFatura   = 2340; 
		
	$posicaoXVlrPag   	= 1700; //Valor Pago
	$posicaoYVlrPag   	= 2340;

	$posicaoXDiaAtraso   = 2200; //Dia Atraso
	$posicaoYDiaAtraso   = 2340;

	$posicaoXTotalAtraso = 2265; //Total Atraso
	$posicaoYTotalAtraso = 3227;

	$posicaoXUltimaFat   = 1705; //Ultima Fatura
	$posicaoYUltimaFat   = 3347;

	$posicaoXDataAtual   = 500; //Data Atual
	$posicaoYDataAtual   = 3808;



	foreach ($ArrDados as $item) {
	   
	   $databd         = SqlToData($item['DATA_VENCIMENTO']);;
	   $databd    		 = explode("/",$databd); 
	   $dataBol    	 = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);

	   $dataPag        = SqlToData($item['DATA_PAGAMENTO']);
	   if(!$dataPag)
	   	$dataPag = mktime(0,0,0,date("m"),date("d"),date("Y"));

	   $dataPag        = explode("/",$dataPag); 
	   $dataPagBol     = mktime(0,0,0,$dataPag[1],$dataPag[0],$dataPag[2]);
	  	$diasAtraso 	 = '' . 0;

	  	if($dataPagBol > $dataBol){
			$dias       	 = ($dataPagBol-$dataBol)/86400;   
	   	$diasAtraso 	 = '' . ceil($dias);
	  	}
	   
	   $diasAtraso = str_pad($diasAtraso, 2, 0, STR_PAD_LEFT); 

	   $diasTotalAtraso += $diasAtraso;


		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtVenc, $posicaoYDtVenc, $cor,$normal, SqlToData($item['DATA_VENCIMENTO'],0,35),5); //Data Vencimento	  					
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtPag, $posicaoYDtPag, $cor,$normal, SqlToData($item['DATA_PAGAMENTO'],0,35),5);	// Data Pagamento	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrFatura, $posicaoYVlrFatura, $cor,$normal, toMoeda($item['VALOR_FATURA']),5);	  	// Valor fatura	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrPag, $posicaoYVlrPag, $cor,$normal, toMoeda($item['VALOR_PAGO']),5); 	// Valor Pago

		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDiaAtraso, $posicaoYDiaAtraso, $cor,$normal, $diasAtraso,5); 	// Dias Atraso
			
		$posicaoYDtVenc   	= $posicaoYDtVenc + 65;	
		$posicaoYDtPag 		= $posicaoYDtPag + 65;
		$posicaoYVlrFatura   = $posicaoYVlrFatura + 65;
		$posicaoYVlrPag   	= $posicaoYVlrPag + 65;
		$posicaoYDiaAtraso  	= $posicaoYDiaAtraso + 65;
	}

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXTotalAtraso, $posicaoYTotalAtraso, $cor,$normal, '' . $diasTotalAtraso,5); //Total Atraso

	$i  = $i - 1;
	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXUltimaFat, $posicaoYUltimaFat, $cor,$normal, toMoeda($ArrDados[$i]['VALOR_FATURA']),5);


   setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataAtual = strftime('%d de %B de %Y', strtotime('today'));

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXDataAtual, $posicaoYDataAtual, $cor,$normal, $dataAtual,5);

	ob_start();
	imagejpeg ( $imagem); 
	imagedestroy( $imagem ); 
	$dadoImagem = ob_get_clean();
}

if ($qtdeAssociados == 2){

	$imagem = imagecreatefromjpeg("img/DeclPerman_02_PF.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 190;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato

	//Posições Beneficiários
	$posicaoXCodAssoc  = 210; 
	$posicaoYCodAssoc  = 1154; 

	$posicaoXTipo      = 820; 
	$posicaoYTipo      = 1154;
		
	$posicaoXNome      = 963; 
	$posicaoYNome      = 1154; 
		
	$posicaoXData      = 1894; 
	$posicaoYData      = 1154;
		
	$posicaoXCpf    	 = 2200; 
	$posicaoYCpf       = 1154;
		
	$posicaoXAdesao    = 2555; 
	$posicaoYAdesao    = 1154; 
		

	//Titular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 1485; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 1485;
		
	$posicaoXContratacao       = 1125; 
	$posicaoYContratacao       = 1485; 
		
	$posicaoXAcomodacao        = 1595; 
	$posicaoYAcomodacao        = 1485;
		
	$posicaoXAbrangencia   	   = 1950; 
	$posicaoYAbrangencia       = 1485;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 1485; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim    = 1395; //FlagCPTSim
	$posicaoYCptSim    = 1965;

	$posicaoXCptNao    = 1540; //FlagCPTSim
	$posicaoYCptNao    = 1965;

	$posicaoXDtExcl    = 300;
	$posicaoYDtExcl    = 2235;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Flag_aplicar_CPT
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Flag_aplicar_CPT
	}


	$frase = "Declaramos para os devidos fins que o plano de saúde encontra-se " . $ArrDados[0]['SITUACAO_PLANO'] . ", abaixo últimos pagamentos:";



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,102);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];
		
		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}


	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl-30, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl+30, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano


	//Faturas
	$tamanhoLetra = 21;
		
	$posicaoXDtVenc  		= 310; //Data Vencimento
	$posicaoYDtVenc 		= 2405; 
		
	$posicaoXDtPag 		= 705; //Data Pagamento
	$posicaoYDtPag 		= 2405;
		
	$posicaoXVlrFatura   = 1200; //Valor Fatura
	$posicaoYVlrFatura   = 2405; 
		
	$posicaoXVlrPag   	= 1700; //Valor Pago
	$posicaoYVlrPag   	= 2405;

	$posicaoXDiaAtraso   = 2200; //Dia Atraso
	$posicaoYDiaAtraso   = 2405;

	$posicaoXTotalAtraso = 2265; //Total Atraso
	$posicaoYTotalAtraso = 3338;

	$posicaoXUltimaFat   = 1705; //Ultima Fatura
	$posicaoYUltimaFat   = 3458;

	$posicaoXDataAtual   = 500; //Data Atual
	$posicaoYDataAtual   = 3825;



	foreach ($ArrDados as $item) {
	   
	   $databd         = SqlToData($item['DATA_VENCIMENTO']);;
	   $databd    		 = explode("/",$databd); 
	   $dataBol    	 = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);

	   $dataPag        = SqlToData($item['DATA_PAGAMENTO']);
	   if(!$dataPag)
	   	$dataPag = mktime(0,0,0,date("m"),date("d"),date("Y"));

	   $dataPag        = explode("/",$dataPag); 
	   $dataPagBol     = mktime(0,0,0,$dataPag[1],$dataPag[0],$dataPag[2]);
	  	$diasAtraso 	 = '' . 0;

	  	if($dataPagBol > $dataBol){
			$dias       	 = ($dataPagBol-$dataBol)/86400;   
	   	$diasAtraso 	 = '' . ceil($dias);
	  	}
	   
	   $diasAtraso = str_pad($diasAtraso, 2, 0, STR_PAD_LEFT); 

	   $diasTotalAtraso += $diasAtraso;


		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtVenc, $posicaoYDtVenc, $cor,$normal, SqlToData($item['DATA_VENCIMENTO'],0,35),5); //Data Vencimento	  					
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtPag, $posicaoYDtPag, $cor,$normal, SqlToData($item['DATA_PAGAMENTO'],0,35),5);	// Data Pagamento	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrFatura, $posicaoYVlrFatura, $cor,$normal, toMoeda($item['VALOR_FATURA']),5);	  	// Valor fatura	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrPag, $posicaoYVlrPag, $cor,$normal, toMoeda($item['VALOR_PAGO']),5); 	// Valor Pago

		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDiaAtraso, $posicaoYDiaAtraso, $cor,$normal, $diasAtraso,5); 	// Dias Atraso
			
		$posicaoYDtVenc   	= $posicaoYDtVenc + 65;	
		$posicaoYDtPag 		= $posicaoYDtPag + 65;
		$posicaoYVlrFatura   = $posicaoYVlrFatura + 65;
		$posicaoYVlrPag   	= $posicaoYVlrPag + 65;
		$posicaoYDiaAtraso  	= $posicaoYDiaAtraso + 65;
	}

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXTotalAtraso, $posicaoYTotalAtraso, $cor,$normal, '' . $diasTotalAtraso,5); //Total Atraso

	$i  = $i - 1;
	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXUltimaFat, $posicaoYUltimaFat, $cor,$normal, toMoeda($ArrDados[$i]['VALOR_FATURA']),5);


   setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataAtual = strftime('%d de %B de %Y', strtotime('today'));

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXDataAtual, $posicaoYDataAtual, $cor,$normal, $dataAtual,5);

	ob_start();
	imagejpeg ( $imagem); 
	imagedestroy( $imagem ); 
	$dadoImagem = ob_get_clean();
}



if ($qtdeAssociados == 3){

	
	$imagem = imagecreatefromjpeg("img/DeclPerman_03_PF.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 190;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato

	//Posições Beneficiários
	$posicaoXCodAssoc  = 210; 
	$posicaoYCodAssoc  = 1154; 

	$posicaoXTipo      = 820; 
	$posicaoYTipo      = 1154;
		
	$posicaoXNome      = 963; 
	$posicaoYNome      = 1154; 
		
	$posicaoXData      = 1894; 
	$posicaoYData      = 1154;
		
	$posicaoXCpf    	 = 2200; 
	$posicaoYCpf       = 1154;
		
	$posicaoXAdesao    = 2555; 
	$posicaoYAdesao    = 1154; 
		

	//Titular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 1

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao


	//Posição Planos
	$posicaoXNomePlano  			= 210; 
	$posicaoYNomePlano   		= 1550; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			   = 1550;
		
	$posicaoXContratacao       = 1125; 
	$posicaoYContratacao       = 1550; 
		
	$posicaoXAcomodacao        = 1595; 
	$posicaoYAcomodacao        = 1550;
		
	$posicaoXAbrangencia   	   = 1950; 
	$posicaoYAbrangencia       = 1550;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 1550; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim    = 1395; //FlagCPTSim
	$posicaoYCptSim    = 2030;

	$posicaoXCptNao    = 1540; //FlagCPTSim
	$posicaoYCptNao    = 2030;

	$posicaoXDtExcl    = 300;
	$posicaoYDtExcl    = 2295;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Flag Aplicar CPT
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Flag Aplicar CPT
	}

	$frase = "Declaramos para os devidos fins que o plano de saúde encontra-se " . $ArrDados[0]['SITUACAO_PLANO'] . ", abaixo últimos pagamentos:";



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,102);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];
		
		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl-30, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl+30, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano


	//Faturas
	$tamanhoLetra = 21;
		
	$posicaoXDtVenc  		= 310; //Data Vencimento
	$posicaoYDtVenc 		= 2470; 
		
	$posicaoXDtPag 		= 705; //Data Pagamento
	$posicaoYDtPag 		= 2470;
		
	$posicaoXVlrFatura   = 1200; //Valor Fatura
	$posicaoYVlrFatura   = 2470; 
		
	$posicaoXVlrPag   	= 1700; //Valor Pago
	$posicaoYVlrPag   	= 2470;

	$posicaoXDiaAtraso   = 2200; //Dia Atraso
	$posicaoYDiaAtraso   = 2470;

	$posicaoXTotalAtraso = 2265; //Total Atraso
	$posicaoYTotalAtraso = 3452;

	$posicaoXUltimaFat   = 1705; //Ultima Fatura
	$posicaoYUltimaFat   = 3567;

	$posicaoXDataAtual   = 500; //Data Atual
	$posicaoYDataAtual   = 3825;



	foreach ($ArrDados as $item) {
	   
	   $databd         = SqlToData($item['DATA_VENCIMENTO']);;
	   $databd    		 = explode("/",$databd); 
	   $dataBol    	 = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);

	   $dataPag        = SqlToData($item['DATA_PAGAMENTO']);
	   if(!$dataPag)
	   	$dataPag = mktime(0,0,0,date("m"),date("d"),date("Y"));

	   $dataPag        = explode("/",$dataPag); 
	   $dataPagBol     = mktime(0,0,0,$dataPag[1],$dataPag[0],$dataPag[2]);
	  	$diasAtraso 	 = '' . 0;

	  	if($dataPagBol > $dataBol){
			$dias       	 = ($dataPagBol-$dataBol)/86400;   
	   	$diasAtraso 	 = '' . ceil($dias);
	  	}
	   
	   $diasAtraso = str_pad($diasAtraso, 2, 0, STR_PAD_LEFT); 

	   $diasTotalAtraso += $diasAtraso;


		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtVenc, $posicaoYDtVenc, $cor,$normal, SqlToData($item['DATA_VENCIMENTO'],0,35),5); //Data Vencimento	  					
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtPag, $posicaoYDtPag, $cor,$normal, SqlToData($item['DATA_PAGAMENTO'],0,35),5);	// Data Pagamento	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrFatura, $posicaoYVlrFatura, $cor,$normal, toMoeda($item['VALOR_FATURA']),5);	  	// Valor fatura	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrPag, $posicaoYVlrPag, $cor,$normal, toMoeda($item['VALOR_PAGO']),5); 	// Valor Pago

		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDiaAtraso, $posicaoYDiaAtraso, $cor,$normal, $diasAtraso,5); 	// Dias Atraso
			
		$posicaoYDtVenc   	= $posicaoYDtVenc + 65;	
		$posicaoYDtPag 		= $posicaoYDtPag + 65;
		$posicaoYVlrFatura   = $posicaoYVlrFatura + 65;
		$posicaoYVlrPag   	= $posicaoYVlrPag + 65;
		$posicaoYDiaAtraso  	= $posicaoYDiaAtraso + 65;
	}

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXTotalAtraso, $posicaoYTotalAtraso, $cor,$normal, '' . $diasTotalAtraso,5); //Total Atraso

	$i  = $i - 1;
	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXUltimaFat, $posicaoYUltimaFat, $cor,$normal, toMoeda($ArrDados[$i]['VALOR_FATURA']),5);


   setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataAtual = strftime('%d de %B de %Y', strtotime('today'));

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXDataAtual, $posicaoYDataAtual, $cor,$normal, $dataAtual,5);

	ob_start();
	imagejpeg ( $imagem); 
	imagedestroy( $imagem ); 
	$dadoImagem = ob_get_clean();
}



if ($qtdeAssociados == 4){

	$imagem = imagecreatefromjpeg("img/DeclPerman_04_PF.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 190;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato

	//Posições Beneficiários
	$posicaoXCodAssoc  = 210; 
	$posicaoYCodAssoc  = 1154; 

	$posicaoXTipo      = 820; 
	$posicaoYTipo      = 1154;
		
	$posicaoXNome      = 963; 
	$posicaoYNome      = 1154; 
		
	$posicaoXData      = 1894; 
	$posicaoYData      = 1154;
		
	$posicaoXCpf    	 = 2200; 
	$posicaoYCpf       = 1154;
		
	$posicaoXAdesao    = 2555; 
	$posicaoYAdesao    = 1154; 
		


	//Titular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 1

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao


	//Posição Planos
	$posicaoXNomePlano  			= 210; 
	$posicaoYNomePlano   		= 1615; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			   = 1615;
		
	$posicaoXContratacao       = 1125; 
	$posicaoYContratacao       = 1615; 
		
	$posicaoXAcomodacao        = 1595; 
	$posicaoYAcomodacao        = 1615;
		
	$posicaoXAbrangencia   	   = 1950; 
	$posicaoYAbrangencia       = 1615;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 1615; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim    = 1395; //FlagCPTSim
	$posicaoYCptSim    = 2095;

	$posicaoXCptNao    = 1540; //FlagCPTSim
	$posicaoYCptNao    = 2095;

	$posicaoXDtExcl    = 300;
	$posicaoYDtExcl    = 2360;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase = "Declaramos para os devidos fins que o plano de saúde encontra-se " . $ArrDados[0]['SITUACAO_PLANO'] . ", abaixo últimos pagamentos:";



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,102);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];
		
		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl-30, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl+30, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano


	//Faturas
	$tamanhoLetra = 21;
		
	$posicaoXDtVenc  		= 310; //Data Vencimento
	$posicaoYDtVenc 		= 2535; 
		
	$posicaoXDtPag 		= 705; //Data Pagamento
	$posicaoYDtPag 		= 2535;
		
	$posicaoXVlrFatura   = 1200; //Valor Fatura
	$posicaoYVlrFatura   = 2535; 
		
	$posicaoXVlrPag   	= 1700; //Valor Pago
	$posicaoYVlrPag   	= 2535;

	$posicaoXDiaAtraso   = 2200; //Dia Atraso
	$posicaoYDiaAtraso   = 2535;

	$posicaoXTotalAtraso = 2265; //Total Atraso
	$posicaoYTotalAtraso = 3425;

	$posicaoXUltimaFat   = 1705; //Ultima Fatura
	$posicaoYUltimaFat   = 3537;

	$posicaoXDataAtual   = 500; //Data Atual
	$posicaoYDataAtual   = 3798;


	foreach ($ArrDados as $item) {
	   
	   $databd         = SqlToData($item['DATA_VENCIMENTO']);;
	   $databd    		 = explode("/",$databd); 
	   $dataBol    	 = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);

	   $dataPag        = SqlToData($item['DATA_PAGAMENTO']);
	   if(!$dataPag)
	   	$dataPag = mktime(0,0,0,date("m"),date("d"),date("Y"));

	   $dataPag        = explode("/",$dataPag); 
	   $dataPagBol     = mktime(0,0,0,$dataPag[1],$dataPag[0],$dataPag[2]);
	  	$diasAtraso 	 = '' . 0;

	  	if($dataPagBol > $dataBol){
			$dias       	 = ($dataPagBol-$dataBol)/86400;   
	   	$diasAtraso 	 = '' . ceil($dias);
	  	}
	   
	   $diasAtraso = str_pad($diasAtraso, 2, 0, STR_PAD_LEFT); 

	   $diasTotalAtraso += $diasAtraso;


		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtVenc, $posicaoYDtVenc, $cor,$normal, SqlToData($item['DATA_VENCIMENTO'],0,35),5); //Data Vencimento	  					
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtPag, $posicaoYDtPag, $cor,$normal, SqlToData($item['DATA_PAGAMENTO'],0,35),5);	// Data Pagamento	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrFatura, $posicaoYVlrFatura, $cor,$normal, toMoeda($item['VALOR_FATURA']),5);	  	// Valor fatura	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrPag, $posicaoYVlrPag, $cor,$normal, toMoeda($item['VALOR_PAGO']),5); 	// Valor Pago

		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDiaAtraso, $posicaoYDiaAtraso, $cor,$normal, $diasAtraso,5); 	// Dias Atraso
			
		$posicaoYDtVenc   	= $posicaoYDtVenc + 65;	
		$posicaoYDtPag 		= $posicaoYDtPag + 65;
		$posicaoYVlrFatura   = $posicaoYVlrFatura + 65;
		$posicaoYVlrPag   	= $posicaoYVlrPag + 65;
		$posicaoYDiaAtraso  	= $posicaoYDiaAtraso + 65;
	}

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXTotalAtraso, $posicaoYTotalAtraso, $cor,$normal, '' . $diasTotalAtraso,5); //Total Atraso

	$i  = $i - 1;
	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXUltimaFat, $posicaoYUltimaFat, $cor,$normal, toMoeda($ArrDados[$i]['VALOR_FATURA']),5);


   setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataAtual = strftime('%d de %B de %Y', strtotime('today'));

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXDataAtual, $posicaoYDataAtual, $cor,$normal, $dataAtual,5);

	ob_start();
	imagejpeg ( $imagem); 
	imagedestroy( $imagem ); 
	$dadoImagem = ob_get_clean();
}


if ($qtdeAssociados == 5){

	$imagem = imagecreatefromjpeg("img/DeclPerman_05_PF.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 190;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato

	//Posições Beneficiários
	$posicaoXCodAssoc  = 210; 
	$posicaoYCodAssoc  = 1154; 

	$posicaoXTipo      = 820; 
	$posicaoYTipo      = 1154;
		
	$posicaoXNome      = 963; 
	$posicaoYNome      = 1154; 
		
	$posicaoXData      = 1894; 
	$posicaoYData      = 1154;
		
	$posicaoXCpf    	 = 2200; 
	$posicaoYCpf       = 1154;
		
	$posicaoXAdesao    = 2555; 
	$posicaoYAdesao    = 1154; 
		


	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao


	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssoc    = $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		 = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssoc    = $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		 = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssoc    = $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		 = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 4
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[4]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[4]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[4]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[4]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_ADESAO']),5); //Data Adesao


	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 1680; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 1680;
		
	$posicaoXContratacao       = 1125; 
	$posicaoYContratacao       = 1680; 
		
	$posicaoXAcomodacao        = 1595; 
	$posicaoYAcomodacao        = 1680;
		
	$posicaoXAbrangencia   	   = 1950; 
	$posicaoYAbrangencia       = 1680;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 1680; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação

	  

	//Informações Complementares

	$posicaoXCptSim    = 1395; //FlagCPTSim
	$posicaoYCptSim    = 2160;

	$posicaoXCptNao    = 1540; //FlagCPTSim
	$posicaoYCptNao    = 2160;

	$posicaoXDtExcl    = 300;
	$posicaoYDtExcl    = 2425;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase = "Declaramos para os devidos fins que o plano de saúde encontra-se " . $ArrDados[0]['SITUACAO_PLANO'] . ", abaixo últimos pagamentos:";



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,102);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];
		
		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl-30, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl+30, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano

	//Faturas
	$tamanhoLetra = 21;
		
	$posicaoXDtVenc  	= 310; //Data Vencimento
	$posicaoYDtVenc 	= 2600; 
		
	$posicaoXDtPag 		= 705; //Data Pagamento
	$posicaoYDtPag 		= 2600;
		
	$posicaoXVlrFatura   = 1200; //Valor Fatura
	$posicaoYVlrFatura   = 2600; 
		
	$posicaoXVlrPag   	= 1700; //Valor Pago
	$posicaoYVlrPag   	= 2600;

	$posicaoXDiaAtraso   = 2200; //Dia Atraso
	$posicaoYDiaAtraso   = 2600;

	$posicaoXTotalAtraso = 2265; //Total Atraso
	$posicaoYTotalAtraso = 3490;

	$posicaoXUltimaFat   = 1705; //Ultima Fatura
	$posicaoYUltimaFat   = 3603;

	$posicaoXDataAtual   = 500; //Data Atual
	$posicaoYDataAtual   = 3820;


	foreach ($ArrDados as $item) {
	   
	   $databd         = SqlToData($item['DATA_VENCIMENTO']);;
	   $databd    		 = explode("/",$databd); 
	   $dataBol    	 = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);

	   $dataPag        = SqlToData($item['DATA_PAGAMENTO']);
	   if(!$dataPag)
	   	$dataPag = mktime(0,0,0,date("m"),date("d"),date("Y"));

	   $dataPag        = explode("/",$dataPag); 
	   $dataPagBol     = mktime(0,0,0,$dataPag[1],$dataPag[0],$dataPag[2]);
	  	$diasAtraso 	 = '' . 0;

	  	if($dataPagBol > $dataBol){
			$dias       	 = ($dataPagBol-$dataBol)/86400;   
	   	$diasAtraso 	 = '' . ceil($dias);
	  	}
	   
	   $diasAtraso = str_pad($diasAtraso, 2, 0, STR_PAD_LEFT); 

	   $diasTotalAtraso += $diasAtraso;


		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtVenc, $posicaoYDtVenc, $cor,$normal, SqlToData($item['DATA_VENCIMENTO'],0,35),5); //Data Vencimento	  					
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtPag, $posicaoYDtPag, $cor,$normal, SqlToData($item['DATA_PAGAMENTO'],0,35),5);	// Data Pagamento	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrFatura, $posicaoYVlrFatura, $cor,$normal, toMoeda($item['VALOR_FATURA']),5);	  	// Valor fatura	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrPag, $posicaoYVlrPag, $cor,$normal, toMoeda($item['VALOR_PAGO']),5); 	// Valor Pago

		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDiaAtraso, $posicaoYDiaAtraso, $cor,$normal, $diasAtraso,5); 	// Dias Atraso
			
		$posicaoYDtVenc   	= $posicaoYDtVenc + 65;	
		$posicaoYDtPag 		= $posicaoYDtPag + 65;
		$posicaoYVlrFatura   = $posicaoYVlrFatura + 65;
		$posicaoYVlrPag   	= $posicaoYVlrPag + 65;
		$posicaoYDiaAtraso  	= $posicaoYDiaAtraso + 65;
	}

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXTotalAtraso, $posicaoYTotalAtraso, $cor,$normal, '' . $diasTotalAtraso,5); //Total Atraso

	$i  = $i - 1;
	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXUltimaFat, $posicaoYUltimaFat, $cor,$normal, toMoeda($ArrDados[$i]['VALOR_FATURA']),5);


   setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataAtual = strftime('%d de %B de %Y', strtotime('today'));

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXDataAtual, $posicaoYDataAtual, $cor,$normal, $dataAtual,5);

	ob_start();
	imagejpeg ( $imagem); 
	imagedestroy( $imagem ); 
	$dadoImagem = ob_get_clean();
}


if ($qtdeAssociados == 6){

	$imagem = imagecreatefromjpeg("img/DeclPerman_06_PF.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 190;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato

	//Posições Beneficiários
	$posicaoXCodAssoc    = 210; 
	$posicaoYCodAssoc    = 1154; 

	$posicaoXTipo      = 820; 
	$posicaoYTipo      = 1154;
		
	$posicaoXNome      = 963; 
	$posicaoYNome      = 1154; 
		
	$posicaoXData      = 1894; 
	$posicaoYData      = 1154;
		
	$posicaoXCpf    	 = 2200; 
	$posicaoYCpf       = 1154;
		
	$posicaoXAdesao    = 2555; 
	$posicaoYAdesao    = 1154; 
		


	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao


	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 4
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[4]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[4]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[4]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[4]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 5
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[5]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[5]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[5]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[5]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_ADESAO']),5); //Data Adesao


	//Posição Planos
	$posicaoXNomePlano  			= 210; 
	$posicaoYNomePlano   		= 1745; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			   = 1745;
		
	$posicaoXContratacao       = 1125; 
	$posicaoYContratacao       = 1745; 
		
	$posicaoXAcomodacao        = 1595; 
	$posicaoYAcomodacao        = 1745;
		
	$posicaoXAbrangencia   	   = 1950; 
	$posicaoYAbrangencia       = 1745;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 1745; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim    = 1395; //FlagCPTSim
	$posicaoYCptSim    = 2215;

	$posicaoXCptNao    = 1540; //FlagCPTSim
	$posicaoYCptNao    = 2215;

	$posicaoXDtExcl    = 300;
	$posicaoYDtExcl    = 2500;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase = "Declaramos para os devidos fins que o plano de saúde encontra-se " . $ArrDados[0]['SITUACAO_PLANO'] . ", abaixo últimos pagamentos:";



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,102);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];
		
		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl-30, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl+30, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano


	//Faturas
	$tamanhoLetra = 21;
		
	$posicaoXDtVenc  		= 310; //Data Vencimento
	$posicaoYDtVenc 		= 2665; 
		
	$posicaoXDtPag 		= 705; //Data Pagamento
	$posicaoYDtPag 		= 2665;
		
	$posicaoXVlrFatura   = 1200; //Valor Fatura
	$posicaoYVlrFatura   = 2665; 
		
	$posicaoXVlrPag   	= 1700; //Valor Pago
	$posicaoYVlrPag   	= 2665;

	$posicaoXDiaAtraso   = 2200; //Dia Atraso
	$posicaoYDiaAtraso   = 2665;

	$posicaoXTotalAtraso = 2265; //Total Atraso
	$posicaoYTotalAtraso = 3460;

	$posicaoXUltimaFat   = 1705; //Ultima Fatura
	$posicaoYUltimaFat   = 3574;

	$posicaoXDataAtual   = 500; //Data Atual
	$posicaoYDataAtual   = 3790;



	foreach ($ArrDados as $item) {
	   
	   $databd         = SqlToData($item['DATA_VENCIMENTO']);;
	   $databd    		 = explode("/",$databd); 
	   $dataBol    	 = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);

	   $dataPag        = SqlToData($item['DATA_PAGAMENTO']);
	   if(!$dataPag)
	   	$dataPag = mktime(0,0,0,date("m"),date("d"),date("Y"));

	   $dataPag        = explode("/",$dataPag); 
	   $dataPagBol     = mktime(0,0,0,$dataPag[1],$dataPag[0],$dataPag[2]);
	  	$diasAtraso 	 = '' . 0;

	  	if($dataPagBol > $dataBol){
			$dias       	 = ($dataPagBol-$dataBol)/86400;   
	   	$diasAtraso 	 = '' . ceil($dias);
	  	}
	   
	   $diasAtraso = str_pad($diasAtraso, 2, 0, STR_PAD_LEFT); 

	   $diasTotalAtraso += $diasAtraso;


		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtVenc, $posicaoYDtVenc, $cor,$normal, SqlToData($item['DATA_VENCIMENTO'],0,35),5); //Data Vencimento	  					
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtPag, $posicaoYDtPag, $cor,$normal, SqlToData($item['DATA_PAGAMENTO'],0,35),5);	// Data Pagamento	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrFatura, $posicaoYVlrFatura, $cor,$normal, toMoeda($item['VALOR_FATURA']),5);	  	// Valor fatura	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrPag, $posicaoYVlrPag, $cor,$normal, toMoeda($item['VALOR_PAGO']),5); 	// Valor Pago

		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDiaAtraso, $posicaoYDiaAtraso, $cor,$normal, $diasAtraso,5); 	// Dias Atraso
			
		$posicaoYDtVenc   	= $posicaoYDtVenc + 65;	
		$posicaoYDtPag 		= $posicaoYDtPag + 65;
		$posicaoYVlrFatura   = $posicaoYVlrFatura + 65;
		$posicaoYVlrPag   	= $posicaoYVlrPag + 65;
		$posicaoYDiaAtraso  	= $posicaoYDiaAtraso + 65;
	}

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXTotalAtraso, $posicaoYTotalAtraso, $cor,$normal, '' . $diasTotalAtraso,5); //Total Atraso

	$i  = $i - 1;
	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXUltimaFat, $posicaoYUltimaFat, $cor,$normal, toMoeda($ArrDados[$i]['VALOR_FATURA']),5);


   setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataAtual = strftime('%d de %B de %Y', strtotime('today'));

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXDataAtual, $posicaoYDataAtual, $cor,$normal, $dataAtual,5);

	ob_start();
	imagejpeg ( $imagem); 
	imagedestroy( $imagem ); 
	$dadoImagem = ob_get_clean();
}


if ($qtdeAssociados == 7){

	$imagem = imagecreatefromjpeg("img/DeclPerman_07_PF.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 190;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato

	//Posições Beneficiários
	$posicaoXCodAssoc    = 210; 
	$posicaoYCodAssoc    = 1154; 

	$posicaoXTipo      = 820; 
	$posicaoYTipo      = 1154;
		
	$posicaoXNome      = 963; 
	$posicaoYNome      = 1154; 
		
	$posicaoXData      = 1894; 
	$posicaoYData      = 1154;
		
	$posicaoXCpf    	 = 2200; 
	$posicaoYCpf       = 1154;
		
	$posicaoXAdesao    = 2555; 
	$posicaoYAdesao    = 1154; 
		


	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao


	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 4
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[4]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[4]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[4]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[4]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 5
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[5]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[5]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[5]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[5]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_ADESAO']),5); //Data Adesao

	   $posicaoYCodAssoc   	= $posicaoYCodAssoc + 65;	
		$posicaoYTipo 		   = $posicaoYTipo + 65;
		$posicaoYNome        = $posicaoYNome + 65;
		$posicaoYData        = $posicaoYData + 65;
		$posicaoYCpf         = $posicaoYCpf + 65;
		$posicaoYAdesao      = $posicaoYAdesao + 65;

	//Dependente 6
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssoc, $posicaoYCodAssoc, $cor,$normal, $ArrAssoc[6]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[6]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[6]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[6]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[6]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[6]['DATA_ADESAO']),5); //Data Adesao


	//Posição Planos
	$posicaoXNomePlano  			= 210; 
	$posicaoYNomePlano   		= 1810; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			   = 1810;
		
	$posicaoXContratacao       = 1125; 
	$posicaoYContratacao       = 1810; 
		
	$posicaoXAcomodacao        = 1595; 
	$posicaoYAcomodacao        = 1810;
		
	$posicaoXAbrangencia   	   = 1950; 
	$posicaoYAbrangencia       = 1810;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 1810; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim    = 1395; //FlagCPTSim
	$posicaoYCptSim    = 2275;

	$posicaoXCptNao    = 1540; //FlagCPTSim
	$posicaoYCptNao    = 2275;

	$posicaoXDtExcl    = 300;
	$posicaoYDtExcl    = 2565;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase = "Declaramos para os devidos fins que o plano de saúde encontra-se " . $ArrDados[0]['SITUACAO_PLANO'] . ", abaixo últimos pagamentos:";



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,102);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];
		
		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl-30, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl+30, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano


	//Faturas
	$tamanhoLetra = 21;
		
	$posicaoXDtVenc  		= 310; //Data Vencimento
	$posicaoYDtVenc 		= 2720; 
		
	$posicaoXDtPag 		= 705; //Data Pagamento
	$posicaoYDtPag 		= 2720;
		
	$posicaoXVlrFatura   = 1200; //Valor Fatura
	$posicaoYVlrFatura   = 2720; 
		
	$posicaoXVlrPag   	= 1700; //Valor Pago
	$posicaoYVlrPag   	= 2720;

	$posicaoXDiaAtraso   = 2200; //Dia Atraso
	$posicaoYDiaAtraso   = 2720;

	$posicaoXTotalAtraso = 2285; //Total Atraso
	$posicaoYTotalAtraso = 3480;

	$posicaoXUltimaFat   = 1705; //Ultima Fatura
	$posicaoYUltimaFat   = 3594;

	$posicaoXDataAtual   = 500; //Data Atual
	$posicaoYDataAtual   = 3810;



	foreach ($ArrDados as $item) {
	   
	   $databd         = SqlToData($item['DATA_VENCIMENTO']);;
	   $databd    		 = explode("/",$databd); 
	   $dataBol    	 = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);

	   $dataPag        = SqlToData($item['DATA_PAGAMENTO']);
	   if(!$dataPag)
	   	$dataPag = mktime(0,0,0,date("m"),date("d"),date("Y"));

	   $dataPag        = explode("/",$dataPag); 
	   $dataPagBol     = mktime(0,0,0,$dataPag[1],$dataPag[0],$dataPag[2]);
	  	$diasAtraso 	 = '' . 0;

	  	if($dataPagBol > $dataBol){
			$dias       	 = ($dataPagBol-$dataBol)/86400;   
	   	$diasAtraso 	 = '' . ceil($dias);
	  	}
	   
	   $diasAtraso = str_pad($diasAtraso, 2, 0, STR_PAD_LEFT); 

	   $diasTotalAtraso += $diasAtraso;


		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtVenc, $posicaoYDtVenc, $cor,$normal, SqlToData($item['DATA_VENCIMENTO'],0,35),5); //Data Vencimento	  					
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDtPag, $posicaoYDtPag, $cor,$normal, SqlToData($item['DATA_PAGAMENTO'],0,35),5);	// Data Pagamento	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrFatura, $posicaoYVlrFatura, $cor,$normal, toMoeda($item['VALOR_FATURA']),5);	  	// Valor fatura	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXVlrPag, $posicaoYVlrPag, $cor,$normal, toMoeda($item['VALOR_PAGO']),5); 	// Valor Pago

		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXDiaAtraso, $posicaoYDiaAtraso, $cor,$normal, $diasAtraso,5); 	// Dias Atraso
			
		$posicaoYDtVenc   	= $posicaoYDtVenc + 65;	
		$posicaoYDtPag 		= $posicaoYDtPag + 65;
		$posicaoYVlrFatura   = $posicaoYVlrFatura + 65;
		$posicaoYVlrPag   	= $posicaoYVlrPag + 65;
		$posicaoYDiaAtraso  	= $posicaoYDiaAtraso + 65;
	}

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXTotalAtraso, $posicaoYTotalAtraso, $cor,$normal, '' . $diasTotalAtraso,5); //Total Atraso

	$i  = $i - 1;
	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXUltimaFat, $posicaoYUltimaFat, $cor,$normal, toMoeda($ArrDados[$i]['VALOR_FATURA']),5);


   setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$dataAtual = strftime('%d de %B de %Y', strtotime('today'));

	textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXDataAtual, $posicaoYDataAtual, $cor,$normal, $dataAtual,5);

	ob_start();
	imagejpeg ( $imagem); 
	imagedestroy( $imagem ); 
	$dadoImagem = ob_get_clean();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body width="100%" height="1010" style="margin-left: -40px;">

<?php
echo '<img width="100%" height="1000px"  src="data:image/jpeg;base64,' . base64_encode( $dadoImagem ) . '" />';
?>
</body>
</html>