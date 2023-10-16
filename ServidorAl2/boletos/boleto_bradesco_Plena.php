<?php
require('../lib/base.php');

$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

$Msg = (isset($_GET["MSG"])) ? $_GET["MSG"] : 'V';


if($Msg == 'V'){
	$queryUltimaParcela  = ' SELECT TOP 1 B.NUMERO_REGISTRO, B.MES_ANO_REFERENCIA AS ULTIMO_VENC_ABERTO, A.MES_ANO_REFERENCIA AS VENC_FAT_ESCOLHIDA  FROM PS1020 A ';
	$queryUltimaParcela .= ' INNER JOIN PS1020 B ON (A.CODIGO_ASSOCIADO = B.CODIGO_ASSOCIADO) ';
	$queryUltimaParcela .= ' WHERE ';
	$queryUltimaParcela .= ' A.DATA_PAGAMENTO IS NULL ';
	$queryUltimaParcela .= ' AND A.DATA_CANCELAMENTO IS NULL '; 
	$queryUltimaParcela .= ' AND B.DATA_PAGAMENTO IS NULL ';
	$queryUltimaParcela .= ' AND B.DATA_CANCELAMENTO IS NULL ';
	$queryUltimaParcela .= ' AND A.NUMERO_REGISTRO =' . aspas($numRegistro);
	$queryUltimaParcela .= ' ORDER BY B.DATA_VENCIMENTO ';
	$resUltimaParcela = jn_query($queryUltimaParcela);
	$rowUltimaParcela = jn_fetch_object($resUltimaParcela);

	if($rowUltimaParcela->NUMERO_REGISTRO != $numRegistro  and $rowUltimaParcela->NUMERO_REGISTRO>0){ 
		$URL_ATUAL= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	    header('Content-Type: text/html');
		$msg  = ' Caro beneficiário foi selecionado o pagamento referente ao mês ' . $rowUltimaParcela->VENC_FAT_ESCOLHIDA;
		$msg .= ' deseja seguir com esse pagamento? Sua parcela de ' . $rowUltimaParcela->ULTIMO_VENC_ABERTO . ' consta em aberto!';
		echo "<script>alert('" . $msg . "');window.location.replace('".$URL_ATUAL."&MSG=OK"."');</script>";
		exit;
	}
}


if($numRegistro){
	
	$query = "SELECT PS1020.CODIGO_EMPRESA, PS1020.CODIGO_ASSOCIADO, PS1020.DATA_VENCIMENTO, PS1020.DATA_EMISSAO, PS1020.VALOR_FATURA, ";
	$query.= "PS1020.IDENTIFICACAO_GERACAO, PS1020.CODIGO_IDENTIFICACAO_FAT, PS1020.CODIGO_BANCO, PS1020.NUMERO_CONTA_COBRANCA, PS1020.CODIGO_IDENTIFICACAO_FAT,  PS1020.NOSSO_NUMERO, PS1020.NUMERO_REGISTRO, PS1020.CODIGO_CARTEIRA, CFGEMPRESA.RAZAO_SOCIAL, ";
	$query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO, CFGEMPRESA.NUMERO_INSC_SUSEP  ";
	$query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;
}else{
	exit;
}

$resPs1020=jn_query($query);
$rowPs1020=jn_fetch_object($resPs1020);

if ($rowPs1020->CODIGO_ASSOCIADO=="") {
   $query = "SELECT PS1010.NOME_EMPRESA NOME , PS1010.NUMERO_CNPJ DOCUMENTO, ";
   $query.= "PS1001.ENDERECO, PS1001.CIDADE, PS1001.BAIRRO, PS1001.CEP, PS1001.ESTADO, ";
   $query.= "Ps1010.FLAG_PLANOFAMILIAR ";
   $query.= "From Ps1010 ";
   $query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
   $query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
} else {
   $query = "SELECT PS1000.NOME_ASSOCIADO NOME , PS1000.NUMERO_CPF DOCUMENTO, ";
   $query.= "PS1001.ENDERECO, PS1001.CIDADE, PS1001.BAIRRO, PS1001.CEP, PS1001.ESTADO, ";
   $query.= "PS1000.FLAG_PLANOFAMILIAR, PS1000.CODIGO_GRUPO_FATURAMENTO, PS1000.DATA_ADMISSAO, PS1030.CODIGO_CADASTRO_ANS, PS1030.NOME_PLANO_FAMILIARES ";
   $query.= "From Ps1000 ";
   $query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
   $query.= "Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ";
   $query.= "Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
}

$resPs1000=jn_query($query);

$rowPs1000=jn_fetch_object($resPs1000);

// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
//$dias_de_prazo_para_pagamento = 5;
//$taxa_boleto = 0; //2.95
//$data_venc = SqlToData($rowPs1020->DATA_VENCIMENTO);
//pr($data_venc,true);
//$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
//$valor_cobrado = str_replace(",", ".",$valor_cobrado);
//$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$query  = 'SELECT CAST(percentual_multa_padrao AS NUMERIC(15,5)) AS PERCENTUAL_MULTA_PADRAO, CAST(percentual_mora_diaria AS NUMERIC(15,5)) AS PERCENTUAL_MORA_DIARIA, ';
if ($rowPs1020->CODIGO_ASSOCIADO <> '') {    
    $query .= '    mensagem_fat_fam01 AS MSG01, mensagem_fat_fam02 AS MSG02, mensagem_fat_fam03 AS MSG03, mensagem_fat_fam04 AS MSG04 ';
} else {
    $query .= '    mensagem_fat_emp01 AS MSG01, mensagem_fat_emp02 AS MSG02, mensagem_fat_emp03 AS MSG03, mensagem_fat_emp04 AS MSG04 ';
}
$query .= 'FROM cfg0001 ';
$result = sqlExecute($query, true);
//pr($result, true);



    // DADOS DO BOLETO PARA O SEU CLIENTE
	
	$dias_de_prazo_para_pagamento = 5;
    $taxa_boleto   = 0; //2.95;
    $data_venc     = SqltoData($rowPs1020->DATA_VENCIMENTO);
	//$venc_2via	   = date("d/m/Y", strtotime($rowPs1020->DATA_VENCIMENTO));
    $valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
    $valor_cobrado = str_replace(",", ".", $valor_cobrado);
    $valor_boleto  = number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
    
    $databd     = $data_venc;
    $databd     = explode("/",$databd); 
    $dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
    $data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
    $dias       = ($data_atual-$dataBol)/86400;
    $diasAtrazo = ceil($dias);

	
	
   // pr($diasAtrazo,true);
	
    if($diasAtrazo>0){
		//pr('teste',true);
        $valor_boleto    = str_replace(",", ".", $valor_boleto);
        $multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
        $mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
        //$multa = 2;
        //$mora  = 0.033; 

        $valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
        $valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
        $valor_boleto          =  number_format($valor_boleto, 2, ',', '');
        
        $data_venc=date('d/m/Y');
        
    }


if($rowPs1020->IDENTIFICACAO_GERACAO == 'SEG-VIA-CART'){
	$dadosboleto["instrucoes4"] = "Ap&oacute;s vencimento cobrar multa de 2% ";
	$dadosboleto["instrucoes3"] = "Ap&oacute;s vencimento cobrar mora di&aacute;ria de 0,333% ";
}else{
	$dadosboleto["instrucoes3"] = "Ap&oacute;s vencimento cobrar mora di&aacute;ria de R$ " . toMoeda(($valor_boleto * $result[0]['PERCENTUAL_MORA_DIARIA']), false, false);
	$dadosboleto["instrucoes4"] = "Ap&oacute;s vencimento cobrar multa de R$ "       . toMoeda(($valor_boleto * $result[0]['PERCENTUAL_MULTA_PADRAO']), false, false);
}

$dadosboleto["instrucoes5"] = $result[0]['MSG01'];
$dadosboleto["instrucoes6"] = $result[0]['MSG02'];
$dadosboleto["instrucoes7"] = $result[0]['MSG03'];
$dadosboleto["instrucoes8"] = $result[0]['MSG04'];	





/*
 * Pego os dados da conta, com base nos parametros
 * parametros configurados
 */

$query = "SELECT CODIGO_BANCO, NUMERO_CONTA_CORRENTE, DIGITO_VERIFICADOR_CONTA, NUMERO_AGENCIA, DIGITO_VERIFICADOR_BANCO, CODIGO_CEDENTE, ";
$query.= "CODIGO_CONVENIO, NOME_CEDENTE, DIGITO_VERIFICADOR_AGENCIA  ";
$query.= "From Ps7300 ";
$query.= "Where  ";//FLAG_CONTA_WEB = 'S' AND
$query.= "  NUMERO_CONTA_CORRENTE = " . aspas($rowPs1020->NUMERO_CONTA_COBRANCA);

//pr($rowPs1020->CODIGO_IDENTIFICACAO_FAT,true);

if ($rowPs1020->NUMERO_CONTA_COBRANCA == '' or $rowPs1020->CODIGO_IDENTIFICACAO_FAT == '' or $rowPs1020->CODIGO_CARTEIRA == '')
	exit;


// if ($rowPs1000->FLAG_PLANOFAMILIAR == 'S') {
    // $lPreNumero = '4';
    // $armazena_grupo = $rowPs1000->CODIGO_GRUPO_FATURAMENTO;
        
    // $conta_grupo_faturamento = rvc('CONTA_GRUPO_FATURAMENTO');

    //conta padrao para pessoa fisica...
    // $_conta = rvc('BOLETO_WEB_CONTA_PF');
    // $arrGContas = explode(';', $conta_grupo_faturamento);
    
    // foreach((array) $arrGContas as $item) {
        // $arrDet = explode(':', trim($item));

        // if ($arrDet[0] == $armazena_grupo) {
            // $_conta =  $arrDet[1];
        // }
    // }
 // } else {
    // $lPreNumero = '9';    
    // $_conta = rvc('BOLETO_WEB_CONTA_PJ');
// }

// if ($_conta <> '') {
    // $query.= "AND Numero_Conta_Corrente = '$_conta'";
// }

//pr($query, true);
$resPs7300=jn_query($query);


$dadosboleto["nosso_numero"] = substr($rowPs1020->CODIGO_IDENTIFICACAO_FAT, 0, 11); 


$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emiss�o do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
//$dadosboleto["demonstrativo2"] = "Taxa banc�ria - R$ ".$taxa_boleto;
$dadosboleto["demonstrativo3"] = "";
//$dadosboleto["instrucoes1"] = "AP�S VENCIMENTO COBRAR MORA DI�RIA DE ". toMoeda((0.00033 * $rowPs1020->VALOR_FATURA), true, false);
$dadosboleto["instrucoes1"] = "*** Valores expressos em R$ ***";
//$_percDesconto = getDescontoBoletoBenef($rowPs1020->CODIGO_ASSOCIADO);
//if ($_percDesconto) {
//    $dadosboleto["instrucoes2"] = "DESCONTO DE R$ : " . toMoeda(($rowPs1020->VALOR_FATURA * ($_percDesconto / 100)), false, false) . " AT� " . $data_venc . " - SR.CAIXA, FAVOR CONCEDER O DESCONTO AT� O VENC.";
//}



// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "N�o";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";


// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //

$rowPs7300=jn_fetch_object($resPs7300);

// DADOS DA SUA CONTA - Bradesco
$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$dadosboleto["agencia_dv"] = $rowPs7300->DIGITO_VERIFICADOR_AGENCIA; // Digito do Num da agencia
$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - Bradesco
$dadosboleto["conta_cedente"] = $rowPs7300->NUMERO_CONTA_CORRENTE; // ContaCedente do Cliente, sem digito (Somente N�meros)
$dadosboleto["conta_cedente_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; // Digito da ContaCedente do Cliente
//$dadosboleto["carteira"] = "03";  // C�digo da Carteira: pode ser 06 ou 03

//if ($rowPs1020->CODIGO_EMPRESA == '400'){
//	$dadosboleto["carteira"] = "03";
//} else {
//	$dadosboleto["carteira"] = "09";
//};

$dadosboleto["carteira"] = $rowPs1020->CODIGO_CARTEIRA;

// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = $rowPs1020->RAZAO_SOCIAL;


$dataAdmissao = SqlToData($rowPs1000->DATA_ADMISSAO);
$d = date_parse_from_format("d/m/Y", $dataAdmissao);	
$mesAniversario = str_pad($d['month'], 2, 0, STR_PAD_LEFT);

$dataVencimento = SqlToData($rowPs1020->DATA_VENCIMENTO);
$d2 = date_parse_from_format("d/m/Y", $dataVencimento);	
$mesVencimento = str_pad($d2['month'], 2, 0, STR_PAD_LEFT);
$anoVencimento = $d2['year'];

if($mesVencimento > $mesAniversario){
	$anoVencimento++;
}

$dadosboleto["mensagemTopUm"] = 'REG DA OPERADORA NA ANS: ' . $rowPs1020->NUMERO_INSC_SUSEP;
$dadosboleto["mensagemTopDois"] = $rowPs1000->CODIGO_CADASTRO_ANS . ' - ' . $rowPs1000->NOME_PLANO_FAMILIARES;
$dadosboleto["mensagemTopTres"] = 'PR&Oacute;XIMO REAJUSTE: ' . $mesAniversario . '/' . $anoVencimento;



// N�O ALTERAR!
//header("Content-Type: text/html; charset=UTF-8", true);
//include("include/funcoes_itau.php"); 
//include("include/layout_itau.php");

ob_start();
//header("Content-Type: text/html; charset=UTF-8", true);
// N�O ALTERAR!
include("include/funcoes_bradesco_Plena.php");
include("include/layout_bradesco_Plena.php");

$content = utf8_encode(ob_get_clean());

// convert


require_once('../lib/html2pdf/html2pdf.class.php');
try
{
	//$html2pdf = new HTML2PDF('P','A4','fr', array(0, 0, 0, 0));
	
	$html2pdf = new HTML2PDF('P','A4','pt', true, 'UTF-8');
	
	/* Abre a tela de impress�o */
	//$html2pdf->pdf->IncludeJS("print(true);");
	
	$html2pdf->pdf->SetDisplayMode('real');
	
	/* Parametro vuehtml = true desabilita o pdf para desenvolvimento do layout */
	
	
	//$content = nl2br(str_replace("&", "&amp;", htmlentities($content)));
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));//
	
	/* Abrir no navegador */
	$html2pdf->Output('boleto.pdf');
	
	/* Salva o PDF no servidor para enviar por email */
	//$html2pdf->Output('caminho/boleto.pdf', 'F');
	
	/* For�a o download no browser */
	//$html2pdf->Output('boleto.pdf', 'D');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}



?>
