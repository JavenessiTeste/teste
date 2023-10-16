<?php
require('../lib/base.php');		
header("Content-Type: text/html; charset=ISO-8859-1",true);

$codigoBeneficiario = isset($_GET['codAssociado']) ? $_GET['codAssociado'] : $_SESSION['codigoIdentificacao'];

$queryCodEmpresa = 'Select CODIGO_EMPRESA from ps1000 where codigo_associado = ' . $codigoBeneficiario;
$resCodEmpresa = jn_query($queryCodEmpresa);
$rowCodEmpresa = jn_fetch_object($resCodEmpresa);

$codigoEmpresa = $rowCodEmpresa->CODIGO_EMPRESA;


$queryEmpresa = 'Select * from PS1010 where codigo_empresa = ' . $codigoEmpresa;
$resEmpresa=jn_query($queryEmpresa);


$rowEmpresa=jn_fetch_object($resEmpresa);

$numeroCnpj  = $rowEmpresa->NUMERO_CNPJ;
$nomeEmpresa = $rowEmpresa->NOME_EMPRESA;


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

$queryDados          =  ' Select first 13 * from VW_DECLARACAO_PERM_PJ where VW_DECLARACAO_PERM_PJ.Codigo_Associado = ' . aspas($codigoBeneficiario);
$resDados			 =	jn_query($queryDados);

$i = 0;
$ArrDados = array();

while($rowDados = jn_fetch_object($resDados)){
	//Planos
	$ArrDados[$i]['NOME_PLANO_FAMILIARES'] 		    = $rowDados->NOME_PLANO_FAMILIARES;
	$ArrDados[$i]['NUMERO_REGISTRO_ANS'] 			= $rowDados->NUMERO_REGISTRO_ANS;
	$ArrDados[$i]['TIPO_CONTRATACAO_ANS'] 			= $rowDados->TIPO_CONTRATACAO_ANS;
	$ArrDados[$i]['ACOMODACAO'] 				    = $rowDados->ACOMODACAO;
	$ArrDados[$i]['ABRANGENCIA_GEOGRAFICA'] 		= $rowDados->ABRANGENCIA_GEOGRAFICA;
	$ArrDados[$i]['SEGMENTACAO'] 					= $rowDados->SEGMENTACAO;
	$ArrDados[$i]['FLAG_APLICAR_CPT']				= $rowDados->FLAG_APLICAR_CPT;
	$ArrDados[$i]['SITUACAO_PLANO']				    = $rowDados->SITUACAO_PLANO;
	$ArrDados[$i]['NUMERO_CONTRATO']				= $rowDados->NUMERO_CONTRATO;

	//Faturas
	$ArrDados[$i]['DATA_VENCIMENTO']				=	$rowDados->DATA_VENCIMENTO;
	$ArrDados[$i]['DATA_PAGAMENTO'] 				=  $rowDados->DATA_PAGAMENTO;
	$ArrDados[$i]['VALOR_FATURA']					=	$rowDados->VALOR_FATURA;
	$ArrDados[$i]['VALOR_PAGO']						=	$rowDados->VALOR_PAGO;
	
	$i++;
}


//Imagem e posições//

if ($qtdeAssociados == 1){
	$imagem = imagecreatefromjpeg("img/DeclPerman_01_PJ.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 270;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato


	//Posição Empresa

	$posicaoXCnpj    		 = 520; 
	$posicaoYCnpj    		 = 1290; 

	$posicaoXNomeEmpre       = 1200; 
	$posicaoYNomeEmpre       = 1290;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCnpj, $posicaoYCnpj, $cor,$normal, $numeroCnpj, 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeEmpre, $posicaoYNomeEmpre, $cor,$normal, $nomeEmpresa, 13); //Tipo Beneficiario


	//Posições Beneficiários
	$posicaoXCodAssociado    = 210; 
	$posicaoYCodAssociado    = 1685; 

	$posicaoXTipo      		 = 816; 
	$posicaoYTipo      		 = 1685;
		
	$posicaoXNome      		 = 963; 
	$posicaoYNome      		 = 1685; 
		
	$posicaoXData      		 = 1894; 
	$posicaoYData      		 = 1685;
		
	$posicaoXCpf       		 = 2200; 
	$posicaoYCpf       		 = 1685;
		
	$posicaoXAdesao    		 = 2555; 
	$posicaoYAdesao    		 = 1685; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 2040; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 2040;
		
	$posicaoXContratacao        = 1125; 
	$posicaoYContratacao        = 2040; 
		
	$posicaoXAcomodacao         = 1595; 
	$posicaoYAcomodacao         = 2040;
		
	$posicaoXAbrangencia   	    = 1950; 
	$posicaoYAbrangencia        = 2040;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 2040; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim       = 1415; //FlagCPTSim
	$posicaoYCptSim       = 2550;

	$posicaoXCptNao       = 1559; //FlagCPTSim
	$posicaoYCptNao       = 2550;

	$posicaoXDtExcl    	  = 1550; //ATIVO ou INATIVO
	$posicaoYDtExcl       = 2855;

	$posicaoXDataAtual    = 490; //Data Atual
	$posicaoYDataAtual    = 3830;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase =  $ArrDados[0]['SITUACAO_PLANO'];



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,54);
		//pr($substringFrase1,true);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];

		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl-1370, $posicaoYDtExcl+50, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano


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
	$imagem = imagecreatefromjpeg("img/DeclPerman_02_PJ.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 270;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato


	//Posição Empresa

	$posicaoXCnpj    		 = 520; 
	$posicaoYCnpj    		 = 1290; 

	$posicaoXNomeEmpre       = 1200; 
	$posicaoYNomeEmpre       = 1290;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCnpj, $posicaoYCnpj, $cor,$normal, $numeroCnpj, 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeEmpre, $posicaoYNomeEmpre, $cor,$normal, $nomeEmpresa, 13); //Tipo Beneficiario


	//Posições Beneficiários
	$posicaoXCodAssociado    = 210; 
	$posicaoYCodAssociado    = 1685; 

	$posicaoXTipo      		 = 816; 
	$posicaoYTipo      		 = 1685;
		
	$posicaoXNome      		 = 963; 
	$posicaoYNome      		 = 1685; 
		
	$posicaoXData      		 = 1894; 
	$posicaoYData      		 = 1685;
		
	$posicaoXCpf       		 = 2200; 
	$posicaoYCpf       		 = 1685;
		
	$posicaoXAdesao    		 = 2555; 
	$posicaoYAdesao    		 = 1685; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 2110; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 2110;
		
	$posicaoXContratacao        = 1125; 
	$posicaoYContratacao        = 2110; 
		
	$posicaoXAcomodacao         = 1595; 
	$posicaoYAcomodacao         = 2110;
		
	$posicaoXAbrangencia   	    = 1950; 
	$posicaoYAbrangencia        = 2110;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 2110; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim       = 1415; //FlagCPTSim
	$posicaoYCptSim       = 2625;

	$posicaoXCptNao       = 1559; //FlagCPTSim
	$posicaoYCptNao       = 2625;

	$posicaoXDtExcl    	  = 1550; //ATIVO ou INATIVO
	$posicaoYDtExcl       = 2930;

	$posicaoXDataAtual    = 490; //Data Atual
	$posicaoYDataAtual    = 3810;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase =  $ArrDados[0]['SITUACAO_PLANO'];



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,54);
		//pr($substringFrase1,true);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];

		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl-1370, $posicaoYDtExcl+50, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano



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
	$imagem = imagecreatefromjpeg("img/DeclPerman_03_PJ.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 270;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato


	//Posição Empresa

	$posicaoXCnpj    		 = 520; 
	$posicaoYCnpj    		 = 1290; 

	$posicaoXNomeEmpre       = 1200; 
	$posicaoYNomeEmpre       = 1290;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCnpj, $posicaoYCnpj, $cor,$normal, $numeroCnpj, 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeEmpre, $posicaoYNomeEmpre, $cor,$normal, $nomeEmpresa, 13); //Tipo Beneficiario


	//Posições Beneficiários
	$posicaoXCodAssociado    = 210; 
	$posicaoYCodAssociado    = 1685; 

	$posicaoXTipo      		 = 816; 
	$posicaoYTipo      		 = 1685;
		
	$posicaoXNome      		 = 963; 
	$posicaoYNome      		 = 1685; 
		
	$posicaoXData      		 = 1894; 
	$posicaoYData      		 = 1685;
		
	$posicaoXCpf       		 = 2200; 
	$posicaoYCpf       		 = 1685;
		
	$posicaoXAdesao    		 = 2555; 
	$posicaoYAdesao    		 = 1685; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 2185; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 2185;
		
	$posicaoXContratacao        = 1125; 
	$posicaoYContratacao        = 2185; 
		
	$posicaoXAcomodacao         = 1595; 
	$posicaoYAcomodacao         = 2185;
		
	$posicaoXAbrangencia   	    = 1950; 
	$posicaoYAbrangencia        = 2185;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 2185; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim       = 1415; //FlagCPTSim
	$posicaoYCptSim       = 2700;

	$posicaoXCptNao       = 1559; //FlagCPTSim
	$posicaoYCptNao       = 2700;

	$posicaoXDtExcl    	  = 1550; //ATIVO ou INATIVO
	$posicaoYDtExcl       = 3005;

	$posicaoXDataAtual    = 490; //Data Atual
	$posicaoYDataAtual    = 3795;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase =  $ArrDados[0]['SITUACAO_PLANO'];



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,54);
		//pr($substringFrase1,true);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];

		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl-1370, $posicaoYDtExcl+50, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano



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
	$imagem = imagecreatefromjpeg("img/DeclPerman_04_PJ.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 270;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato


	//Posição Empresa

	$posicaoXCnpj    		 = 520; 
	$posicaoYCnpj    		 = 1290; 

	$posicaoXNomeEmpre       = 1200; 
	$posicaoYNomeEmpre       = 1290;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCnpj, $posicaoYCnpj, $cor,$normal, $numeroCnpj, 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeEmpre, $posicaoYNomeEmpre, $cor,$normal, $nomeEmpresa, 13); //Tipo Beneficiario


	//Posições Beneficiários
	$posicaoXCodAssociado    = 210; 
	$posicaoYCodAssociado    = 1685; 

	$posicaoXTipo      		 = 816; 
	$posicaoYTipo      		 = 1685;
		
	$posicaoXNome      		 = 963; 
	$posicaoYNome      		 = 1685; 
		
	$posicaoXData      		 = 1894; 
	$posicaoYData      		 = 1685;
		
	$posicaoXCpf       		 = 2200; 
	$posicaoYCpf       		 = 1685;
		
	$posicaoXAdesao    		 = 2555; 
	$posicaoYAdesao    		 = 1685; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 2265; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 2265;
		
	$posicaoXContratacao        = 1125; 
	$posicaoYContratacao        = 2265; 
		
	$posicaoXAcomodacao         = 1595; 
	$posicaoYAcomodacao         = 2265;
		
	$posicaoXAbrangencia   	    = 1950; 
	$posicaoYAbrangencia        = 2265;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 2265; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim       = 1415; //FlagCPTSim
	$posicaoYCptSim       = 2775;

	$posicaoXCptNao       = 1559; //FlagCPTSim
	$posicaoYCptNao       = 2775;

	$posicaoXDtExcl    	  = 1550; //ATIVO ou INATIVO
	$posicaoYDtExcl       = 3080;

	$posicaoXDataAtual    = 490; //Data Atual
	$posicaoYDataAtual    = 3825;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase =  $ArrDados[0]['SITUACAO_PLANO'];



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,54);
		//pr($substringFrase1,true);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];

		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl-1370, $posicaoYDtExcl+50, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano



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
	$imagem = imagecreatefromjpeg("img/DeclPerman_05_PJ.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 270;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato


	//Posição Empresa

	$posicaoXCnpj    		 = 520; 
	$posicaoYCnpj    		 = 1290; 

	$posicaoXNomeEmpre       = 1200; 
	$posicaoYNomeEmpre       = 1290;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCnpj, $posicaoYCnpj, $cor,$normal, $numeroCnpj, 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeEmpre, $posicaoYNomeEmpre, $cor,$normal, $nomeEmpresa, 13); //Tipo Beneficiario


	//Posições Beneficiários
	$posicaoXCodAssociado    = 210; 
	$posicaoYCodAssociado    = 1685; 

	$posicaoXTipo      		 = 816; 
	$posicaoYTipo      		 = 1685;
		
	$posicaoXNome      		 = 963; 
	$posicaoYNome      		 = 1685; 
		
	$posicaoXData      		 = 1894; 
	$posicaoYData      		 = 1685;
		
	$posicaoXCpf       		 = 2200; 
	$posicaoYCpf       		 = 1685;
		
	$posicaoXAdesao    		 = 2555; 
	$posicaoYAdesao    		 = 1685; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 4
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[4]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[4]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[4]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[4]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 2335; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 2335;
		
	$posicaoXContratacao        = 1125; 
	$posicaoYContratacao        = 2335; 
		
	$posicaoXAcomodacao         = 1595; 
	$posicaoYAcomodacao         = 2335;
		
	$posicaoXAbrangencia   	    = 1950; 
	$posicaoYAbrangencia        = 2335;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 2335; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim       = 1415; //FlagCPTSim
	$posicaoYCptSim       = 2852;

	$posicaoXCptNao       = 1559; //FlagCPTSim
	$posicaoYCptNao       = 2852;

	$posicaoXDtExcl    	  = 1550; //ATIVO ou INATIVO
	$posicaoYDtExcl       = 3155;

	$posicaoXDataAtual    = 490; //Data Atual
	$posicaoYDataAtual    = 3812;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase =  $ArrDados[0]['SITUACAO_PLANO'];



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,54);
		//pr($substringFrase1,true);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];

		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl-1370, $posicaoYDtExcl+50, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano


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
	$imagem = imagecreatefromjpeg("img/DeclPerman_06_PJ.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 270;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato


	//Posição Empresa

	$posicaoXCnpj    		 = 520; 
	$posicaoYCnpj    		 = 1290; 

	$posicaoXNomeEmpre       = 1200; 
	$posicaoYNomeEmpre       = 1290;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCnpj, $posicaoYCnpj, $cor,$normal, $numeroCnpj, 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeEmpre, $posicaoYNomeEmpre, $cor,$normal, $nomeEmpresa, 13); //Tipo Beneficiario


	//Posições Beneficiários
	$posicaoXCodAssociado    = 210; 
	$posicaoYCodAssociado    = 1685; 

	$posicaoXTipo      		 = 816; 
	$posicaoYTipo      		 = 1685;
		
	$posicaoXNome      		 = 963; 
	$posicaoYNome      		 = 1685; 
		
	$posicaoXData      		 = 1894; 
	$posicaoYData      		 = 1685;
		
	$posicaoXCpf       		 = 2200; 
	$posicaoYCpf       		 = 1685;
		
	$posicaoXAdesao    		 = 2555; 
	$posicaoYAdesao    		 = 1685; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 4
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[4]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[4]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[4]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[4]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_ADESAO']),5); //Data Adesao


		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 5
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[5]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[5]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[5]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[5]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_ADESAO']),5); //Data Adesao

	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 2415; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 2415;
		
	$posicaoXContratacao        = 1125; 
	$posicaoYContratacao        = 2415; 
		
	$posicaoXAcomodacao         = 1595; 
	$posicaoYAcomodacao         = 2415;
		
	$posicaoXAbrangencia   	    = 1950; 
	$posicaoYAbrangencia        = 2415;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 2415; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim       = 1415; //FlagCPTSim
	$posicaoYCptSim       = 2927;

	$posicaoXCptNao       = 1559; //FlagCPTSim
	$posicaoYCptNao       = 2927;

	$posicaoXDtExcl    	  = 1550; //ATIVO ou INATIVO
	$posicaoYDtExcl       = 3230;

	$posicaoXDataAtual    = 490; //Data Atual
	$posicaoYDataAtual    = 3795;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase =  $ArrDados[0]['SITUACAO_PLANO'];



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,54);
		//pr($substringFrase1,true);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];

		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}



	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl-1370, $posicaoYDtExcl+50, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano



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
	$imagem = imagecreatefromjpeg("img/DeclPerman_07_PJ.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$normal  = "../../Site/assets/fonts/unispace rg.ttf";
			
	$tamanhoLetra = 23;

	//Número Contrato

	$posicaoXContrato   = 270;
	$posicaoYContrato   = 860;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContrato, $posicaoYContrato, $cor,$normal, $ArrDados[0]['NUMERO_CONTRATO'] . '.', 13); //Número Contrato


	//Posição Empresa

	$posicaoXCnpj    		 = 520; 
	$posicaoYCnpj    		 = 1290; 

	$posicaoXNomeEmpre       = 1200; 
	$posicaoYNomeEmpre       = 1290;

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCnpj, $posicaoYCnpj, $cor,$normal, $numeroCnpj, 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeEmpre, $posicaoYNomeEmpre, $cor,$normal, $nomeEmpresa, 13); //Tipo Beneficiario


	//Posições Beneficiários
	$posicaoXCodAssociado    = 210; 
	$posicaoYCodAssociado    = 1685; 

	$posicaoXTipo      		 = 816; 
	$posicaoYTipo      		 = 1685;
		
	$posicaoXNome      		 = 963; 
	$posicaoYNome      		 = 1685; 
		
	$posicaoXData      		 = 1894; 
	$posicaoYData      		 = 1685;
		
	$posicaoXCpf       		 = 2200; 
	$posicaoYCpf       		 = 1685;
		
	$posicaoXAdesao    		 = 2555; 
	$posicaoYAdesao    		 = 1685; 
		

	//CódigoTitular
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[0]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[0]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[0]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[0]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[0]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 1
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[1]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[1]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[1]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[1]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[1]['DATA_ADESAO']),5); //Data Adesao



		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 2
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[2]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[2]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[2]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[2]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[2]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 3
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[3]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[3]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[3]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[3]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[3]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 4
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[4]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[4]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[4]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[4]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[4]['DATA_ADESAO']),5); //Data Adesao


		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 5
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[5]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[5]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[5]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[5]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[5]['DATA_ADESAO']),5); //Data Adesao

		$posicaoYCodAssociado   	 = $posicaoYCodAssociado + 75;	
		$posicaoYTipo 				 = $posicaoYTipo + 75;
		$posicaoYNome        		 = $posicaoYNome + 75;
		$posicaoYData        		 = $posicaoYData + 75;
		$posicaoYCpf         		 = $posicaoYCpf + 75;
		$posicaoYAdesao      		 = $posicaoYAdesao + 75;


	//Dependente 6
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodAssociado, $posicaoYCodAssociado, $cor,$normal, $ArrAssoc[6]['CODIGO_ASSOCIADO'], 13); //Codigo Carteirinha
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTipo, $posicaoYTipo, $cor,$normal, $ArrAssoc[6]['TIPO_ASSOCIADO'], 13); //Tipo Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNome, $posicaoYNome, $cor,$normal, $ArrAssoc[6]['NOME_ASSOCIADO'], 6); //Nome Beneficiario
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($ArrAssoc[6]['DATA_NASCIMENTO']), 5); //Data Nascimento
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCpf, $posicaoYCpf, $cor,$normal, $ArrAssoc[6]['NUMERO_CPF'], 13); //Numero Cpf
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAdesao, $posicaoYAdesao, $cor,$normal, SqlToData($ArrAssoc[6]['DATA_ADESAO']),5); //Data Adesao



	//Posição Planos
	$posicaoXNomePlano  		= 210; 
	$posicaoYNomePlano   		= 2485; 

	$posicaoXAns      			= 773; 
	$posicaoYAns     			= 2485;
		
	$posicaoXContratacao        = 1125; 
	$posicaoYContratacao        = 2485; 
		
	$posicaoXAcomodacao         = 1595; 
	$posicaoYAcomodacao         = 2485;
		
	$posicaoXAbrangencia   	    = 1950; 
	$posicaoYAbrangencia        = 2485;
		
	$posicaoXSegmentacao   		= 2455; 
	$posicaoYSegmentacao    	= 2485; 

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomePlano, $posicaoYNomePlano , $cor,$normal, $ArrDados[0]['NOME_PLANO_FAMILIARES'], 2); //Nome Plano
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAns, $posicaoYAns, $cor,$normal, $ArrDados[0]['NUMERO_REGISTRO_ANS'], 10); //Numero Registro Ans
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXContratacao, $posicaoYContratacao, $cor,$normal, $ArrDados[0]['TIPO_CONTRATACAO_ANS'], 2); //Contratacao
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAcomodacao, $posicaoYAcomodacao, $cor,$normal, $ArrDados[0]['ACOMODACAO'], 2); //Acomodação
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXAbrangencia, $posicaoYAbrangencia, $cor,$normal, $ArrDados[0]['ABRANGENCIA_GEOGRAFICA'], 2); //Abrangência
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXSegmentacao, $posicaoYSegmentacao, $cor,$normal, $ArrDados[0]['SEGMENTACAO'],2); //Segmentação


	//Informações Complementares

	$posicaoXCptSim       = 1415; //FlagCPTSim
	$posicaoYCptSim       = 3000;

	$posicaoXCptNao       = 1559; //FlagCPTSim
	$posicaoYCptNao       = 3000;

	$posicaoXDtExcl    	  = 1550; //ATIVO ou INATIVO
	$posicaoYDtExcl       = 3305;

	$posicaoXDataAtual    = 490; //Data Atual
	$posicaoYDataAtual    = 3795;

	if ($ArrDados[0]['FLAG_APLICAR_CPT'] == 'S') {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptSim, $posicaoYCptSim, $cor,$normal, 'X' ,2); //Segmentação
	} else {
		textoImagemEspacamento($imagem, $tamanhoLetra + 4, 0, $posicaoXCptNao, $posicaoYCptNao, $cor,$normal, 'X' ,2); //Segmentação
	}

	$frase =  $ArrDados[0]['SITUACAO_PLANO'];



	if ($ArrDados[0]['SITUACAO_PLANO'] == 'ATIVO') {
		$frase1 = $frase;
	} else{

		$substringFrase1 = substr($frase,0,54);
		//pr($substringFrase1,true);	
		$substringFrase1 .= '---;---***;';	
		$explodeFrase1 = explode(' ', $substringFrase1);

		$ultimaPalavraFrase1 = $explodeFrase1[count($explodeFrase1) - 1];

		$frase1 = explode($ultimaPalavraFrase1, $substringFrase1);	
		$frase1 = $frase1[0];

		$frase2 = explode($frase1, $frase);
		$frase2 = $frase2[1];
	}


	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl, $posicaoYDtExcl, $cor,$normal, utf8_decode($frase1), 4); //Situação Plano
	textoImagemEspacamento($imagem, $tamanhoLetra +1, 0, $posicaoXDtExcl-1370, $posicaoYDtExcl+50, $cor,$normal, utf8_decode($frase2), 4); //Situação Plano



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