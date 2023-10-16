<?php
require('../lib/base.php');

$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];


$query = "SELECT PS1020.CODIGO_EMPRESA, PS1020.CODIGO_ASSOCIADO, PS1020.DATA_VENCIMENTO, PS1020.DATA_EMISSAO, PS1020.VALOR_FATURA, ";
$query.= "PS1020.CODIGO_IDENTIFICACAO_FAT, PS1020.CODIGO_BANCO, PS1020.NUMERO_CONTA_COBRANCA, PS1020.CODIGO_IDENTIFICACAO_FAT,  PS1020.NOSSO_NUMERO, PS1020.NUMERO_REGISTRO, PS1020.CODIGO_CARTEIRA, CFGEMPRESA.RAZAO_SOCIAL, ";
$query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
$query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;

//pr($query,false);

$resPs1020=jn_query($query);

$rowPs1020=jn_fetch_object($resPs1020);

if ($rowPs1020->CODIGO_ASSOCIADO=="") {
   $query = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
   $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";
   $query.= "Ps1010.Flag_PlanoFamiliar ";
   $query.= "From Ps1010 ";
   $query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
   $query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
} else {
   $query = "SELECT PS1000.NOME_ASSOCIADO NOME , PS1000.NUMERO_CPF DOCUMENTO, ";
   $query.= "PS1001.ENDERECO, PS1001.CIDADE, PS1001.BAIRRO, PS1001.CEP, PS1001.ESTADO, ";
   $query.= "PS1000.FLAG_PLANOFAMILIAR, PS1000.CODIGO_GRUPO_FATURAMENTO ";
   $query.= "FROM PS1000 ";
   $query.= "INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ";
   $query.= "LEFT OUTER JOIN PS1002 ON (PS1000.CODIGO_ASSOCIADO = PS1002.CODIGO_ASSOCIADO) ";
   $query.= "WHERE (PS1000.CODIGO_ASSOCIADO = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
}



$resPs1000=jn_query($query);

$rowPs1000=jn_fetch_object($resPs1000);

//pr($query,true);


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 0; //2.95
$data_venc = SqlToData($rowPs1020->DATA_VENCIMENTO);
//pr($data_venc,true);
$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

/*
 * Pego os dados da conta, com base nos parametros
 * parametros configurados
 */

$query = " SELECT CODIGO_BANCO, NUMERO_CONTA_CORRENTE, DIGITO_VERIFICADOR_CONTA, NUMERO_AGENCIA, DIGITO_VERIFICADOR_BANCO, CODIGO_CEDENTE, ";
$query.= " CODIGO_CONVENIO, NOME_CEDENTE, DIGITO_VERIFICADOR_AGENCIA, CNPJ_SACADOR  ";
$query.= " From Ps7300 ";
$query.= " Where  ";
$query.= "  NUMERO_CONTA_CORRENTE = " . aspas($rowPs1020->NUMERO_CONTA_COBRANCA);


//pr($rowPs1020->CODIGO_IDENTIFICACAO_FAT,true);

if ($rowPs1020->NOSSO_NUMERO == '')
{	
	PR('Este boleto não pode ser gerado pelo AliançaNet, para o registro: ' .  $numRegistro );

   if ($rowPs1020->NOSSO_NUMERO == '')
  	   PR('O campo "NOSSO NÚMERO" não está preenchido');
   
    if ($rowPs1020->NUMERO_CONTA_COBRANCA == '')
  	   PR('O campo "NUMERO CONTA COBRANÇA" não está preenchido');

	if ($rowPs1020->CODIGO_IDENTIFICACAO_FAT == '') 
  	   PR('O campo "CODIGO IDENTIFICACAO FATURA" não está preenchido');

	if ($rowPs1020->CODIGO_CARTEIRA == '')
  	   PR('O campo "CODIGO DA CARTEIRA" não está preenchido');
   

	exit;
}

$resPs7300=jn_query($query);

// DADOS DO BOLETO PARA O SEU CLIENTE
$taxa_boleto   = 0; //2.95;	
$data_venc     = SqltoData($rowPs1020->DATA_VENCIMENTO);	
$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".", $valor_cobrado);
$valor_boleto  = number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
$valor_boleto_original  = number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$databd     = $data_venc;
$databd     = explode("/",$databd); 
$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
$dias       = ($data_atual-$dataBol)/86400;
$diasAtrazo = ceil($dias);

$query              = "SELECT PERCENTUAL_MULTA_PADRAO, PERCENTUAL_MORA_DIARIA FROM CFG0001";
$resMulta           = jn_query($query);
$rowMulta           = jn_fetch_assoc($resMulta);
$multa              = $rowMulta['PERCENTUAL_MULTA_PADRAO'];
$mora               = $rowMulta['PERCENTUAL_MORA_DIARIA'];

$valorMulta = round($rowPs1020->VALOR_FATURA * $multa,2);
$valorMora = round($rowPs1020->VALOR_FATURA * $mora,2);
$valorMora = $valorMora * $diasAtrazo;
$valorMultaMora = $valorMora + $valorMulta;


//$multa = 0.03;
//$mora  = 0.00055; 

if($diasAtrazo>0){	
	$valor_boleto    = str_replace(",", ".", $valor_boleto);	

	$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 	
	$boletoMora 		   =  round(($valor_boleto * $mora), 2);
	$valor_boleto          =  $valor_boleto_multa + ($boletoMora * $diasAtrazo);
	$valorBoleto = explode('.',$valor_boleto);
	$val1 = $valorBoleto[0];
	$val2 = substr($valorBoleto[1],0,2);		
	$valorAtual = $val1 . '.' . $val2;		
	$valor_boleto          =  number_format($valorAtual, 2, ',', '');
	
	$data_venc=date('d/m/Y');
	
}

$dadosboleto["nosso_numero"] = substr($rowPs1020->NOSSO_NUMERO, 3, 8); 
$dadosboleto["nosso_numeroCompleto"] = $rowPs1020->NOSSO_NUMERO; 

$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME . ' &nbsp;&nbsp; - &nbsp;&nbsp; CPF/CNPJ: ' . $rowPs1000->DOCUMENTO;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
$dadosboleto["demonstrativo3"] = "";
$dadosboleto["instrucoes1"] = "*** Valores expressos em R$ ***";

$query  = 'SELECT CAST(percentual_multa_padrao AS NUMERIC(15,3)) AS percentual_multa_padrao, CAST(percentual_mora_diaria AS NUMERIC(15,3)) AS percentual_mora_diaria, ';
if ($rowPs1020->CODIGO_ASSOCIADO <> '') {    
    $query .= '    mensagem_fat_fam01 AS MSG01, mensagem_fat_fam02 AS MSG02, mensagem_fat_fam03 AS MSG03, mensagem_fat_fam04 AS MSG04 ';
} else {
    $query .= '    mensagem_fat_emp01 AS MSG01, mensagem_fat_emp02 AS MSG02, mensagem_fat_emp03 AS MSG03, mensagem_fat_emp04 AS MSG04 ';
}
$query .= 'FROM cfg0001 ';
$result = sqlExecute($query, true);

$dadosboleto["instrucoes3"] = "Ap&oacute;s vencimento cobrar mora di&aacute;ria de R$ " . toMoeda(($valor_boleto * $mora), false, false);
$dadosboleto["instrucoes4"] = "Ap&oacute;s vencimento cobrar multa de R$ " . toMoeda(($rowPs1020->VALOR_FATURA * $multa), false, false);

$dadosboleto["instrucoes5"] = $result[0]['MSG01'];
$dadosboleto["instrucoes6"] = $result[0]['MSG02'];
$dadosboleto["instrucoes7"] = $result[0]['MSG03'];
$dadosboleto["instrucoes8"] = $result[0]['MSG04'];

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "N&atilde;o";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

$rowPs7300=jn_fetch_object($resPs7300);

// DADOS DA SUA CONTA - Bradesco
$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$dadosboleto["agencia_dv"] = $rowPs7300->DIGITO_VERIFICADOR_AGENCIA; // Digito do Num da agencia
$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - Bradesco
$dadosboleto["conta_cedente"] = $rowPs7300->NUMERO_CONTA_CORRENTE; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = $rowPs1020->CODIGO_CARTEIRA;

// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs7300->CNPJ_SACADOR;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = $rowPs7300->NOME_CEDENTE;

// NÃO ALTERAR!
ob_start();

include("include/funcoes_itau_Vallor.php"); 
include("include/layout_itau_Vallor.php");
$content = utf8_encode(ob_get_clean());


require_once('../lib/html2pdf/html2pdf.class.php');
try
{
	//$html2pdf = new HTML2PDF('P','A4','fr', array(0, 0, 0, 0));
	
	$html2pdf = new HTML2PDF('P','A4','pt', true, 'UTF-8');
	
	/* Abre a tela de impressão */
	//$html2pdf->pdf->IncludeJS("print(true);");
	
	$html2pdf->pdf->SetDisplayMode('real');
	
	/* Parametro vuehtml = true desabilita o pdf para desenvolvimento do layout */
	
	
	//$content = nl2br(str_replace("&", "&amp;", htmlentities($content)));
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));//
	
	/* Abrir no navegador */
	$html2pdf->Output('boleto.pdf');
	
	/* Salva o PDF no servidor para enviar por email */
	//$html2pdf->Output('caminho/boleto.pdf', 'F');
	
	/* Força o download no browser */
	//$html2pdf->Output('boleto.pdf', 'D');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}
?>
