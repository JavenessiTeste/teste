<?php
require('../lib/base.php');

$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

$query = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, ";
$query.= "Ps1020.Codigo_Identificacao_Fat, Ps1020.Codigo_Banco, Ps1020.Numero_Conta_Cobranca, Ps1020.Codigo_Identificacao_Fat,  Ps1020.Nosso_Numero, Ps1020.Numero_Registro, Ps1020.Codigo_Carteira, CFGEMPRESA.RAZAO_SOCIAL, ";
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
   //$query = "Select coalesce(Ps1002.Nome_Contratante, Ps1000.Nome_Associado) Nome , Ps1000.Numero_Cpf Documento, ";
   $query = "	Select 
					case
                        when Ps1002.Nome_Contratante is not null and Ps1002.Nome_Contratante != '' then
                            Ps1002.Nome_Contratante
                        else
                            Ps1000.Nome_Associado
                    end Nome, Ps1000.Numero_Cpf Documento, ";
   $query.= "		Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";
   $query.= "		Ps1000.Flag_PlanoFamiliar, Ps1000.Codigo_Grupo_faturamento, ps1002.codigo_convenio ";
   $query.= "	From Ps1000 ";
   $query.= "	Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
   $query.= "	Left Outer Join Ps1002 On (Ps1000.Codigo_Associado = Ps1002.Codigo_Associado) ";
   $query.= "	Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
}

//pr($query,false);

$resPs1000=jn_query($query);

$rowPs1000=jn_fetch_object($resPs1000);

// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 0; //2.95
$data_venc = SqlToData($rowPs1020->DATA_VENCIMENTO);
$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["nosso_numero"] = $rowPs1020->CODIGO_IDENTIFICACAO_FAT;
$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;
$dadosboleto["data_vencimento"] = $data_venc;
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO);
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO);
$dadosboleto["valor_boleto"] = $valor_boleto;

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ ".$taxa_boleto;
$dadosboleto["demonstrativo3"] = "";

// INSTRUÇÕES PARA O CAIXA
$dadosboleto["instrucoes1"] = "NÃO RECEBER APÓS 60 DIAS ";
$dadosboleto["instrucoes2"] = "ESTA DATA NÃO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
$dadosboleto["instrucoes3"] = "ATRASOS SUP A 60DIAS CONSECUTIVOS OU NÃO NOS ULTIM. 12 MESES";
$dadosboleto["instrucoes4"] = "APÓS O VENCIMENTO COBRAR MULTA DE 2% <BR> APÓS O VENCIMENTO COBRAR MORA DIÁRIA DE 0,033% ";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "A";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "RC";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

$query = " Select codigo_banco, codigo_carteira, numero_conta_corrente, digito_verificador_conta, numero_agencia, digito_verificador_banco, codigo_cedente, ";
$query.= " codigo_convenio, Nome_Cedente  ";
$query.= " From Ps7300 ";
$query.= " Where FLAG_CONTA_WEB = 'S' ";
$query.= " AND CODIGO_BANCO = '001' ";

$resPs7300=ibase_query($query);
$rowPs7300=ibase_fetch_assoc($resPs7300);
//pr($rowPs7300,true);
// DADOS DA SUA CONTA - BANCO DO BRASIL
$dadosboleto["agencia"] = $rowPs7300['NUMERO_CONTA_CORRENTE'];
//$dadosboleto["agencia"] = $rowPs7300['NUMERO_AGENCIA'];
$dadosboleto["conta"] = $rowPs7300['NUMERO_AGENCIA'];
//$dadosboleto["conta"] = $rowPs7300['NUMERO_CONTA_CORRENTE'];

// DADOS PERSONALIZADOS - BANCO DO BRASIL
if($rowPs1000->CODIGO_CONVENIO){
	$dadosboleto["convenio"] = $rowPs1000->CODIGO_CONVENIO;  // Num do convênio - REGRA: 6 ou 7 ou 8 dígitos
}else{
	$dadosboleto["convenio"] = "3120889";  // Num do convênio - REGRA: 6 ou 7 ou 8 dígitos
}
//$dadosboleto["convenio"] = "3120889";  // Num do convênio - REGRA: 6 ou 7 ou 8 dígitos
$dadosboleto["contrato"] = "999999"; // Num do seu contrato

if($rowPs7300['CODIGO_CARTEIRA']){
	$dadosboleto["carteira"] = $rowPs7300['CODIGO_CARTEIRA'];
}else{
	$dadosboleto["carteira"] = '17';	
}

$dadosboleto["variacao_carteira"] = "-019";  // Variação da Carteira, com traço (opcional)

// TIPO DO BOLETO
$dadosboleto["formatacao_convenio"] = "7"; // REGRA: 8 p/ Convênio c/ 8 dígitos, 7 p/ Convênio c/ 7 dígitos, ou 6 se Convênio c/ 6 dígitos
$dadosboleto["formatacao_nosso_numero"] = "2"; // REGRA: Usado apenas p/ Convênio c/ 6 dígitos: informe 1 se for NossoNúmero de até 5 dígitos ou 2 para opção de até 17 dígitos

/*
#################################################
DESENVOLVIDO PARA CARTEIRA 18

- Carteira 18 com Convenio de 8 digitos
  Nosso número: pode ser até 9 dígitos

- Carteira 18 com Convenio de 7 digitos
  Nosso número: pode ser até 10 dígitos

- Carteira 18 com Convenio de 6 digitos
  Nosso número:
  de 1 a 99999 para opção de até 5 dígitos
  de 1 a 99999999999999999 para opção de até 17 dígitos

#################################################
*/


// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300['NOME_CEDENTE'];
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = $rowPs1020->RAZAO_SOCIAL;

// NÃO ALTERAR!
ob_start();
include("include/funcoes_bb_MV2C.php"); 
include("include/layout_bb_pdf.php");
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