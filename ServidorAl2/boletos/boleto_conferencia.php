<?php

header("Content-Type: text/html; charset=ISO-8859-1",true);
require('../lib/base.php');

$NossoNumero_Registrado = rvc('UTILIZAR_NOSSO_NUMERO_BANCO','SIM');
$Atualizado             = rvc('GERAR_BOLETO_ATUALIZADO', 'SIM');

$query                  = "SELECT PERCENTUAL_MULTA_PADRAO FROM CFG0001";
$resMulta               = jn_query($query);
$rowMulta               = jn_fetch_assoc($resMulta);
$multa                  = $rowMulta['PERCENTUAL_MULTA_PADRAO'];

$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

$query  = "Select Ps1020.Codigo_Empresa, Ps1020.CODIGO_ASSOCIADO, PS1020.DATA_VENCIMENTO, PS1020.DATA_EMISSAO, PS1020.VALOR_FATURA, PS1020.MES_ANO_REFERENCIA, ";
$query .= " CASE DATEPART(WEEKDAY, PS1020.DATA_VENCIMENTO) WHEN 0 THEN DATEADD(DAY, 1, PS1020.DATA_VENCIMENTO)  WHEN 6 THEN DATEADD(DAY, 2, PS1020.DATA_VENCIMENTO) ELSE PS1020.DATA_VENCIMENTO END AS VENCIMENTO_2VIA, ";
$query .= "PS1020.CODIGO_IDENTIFICACAO_FAT,PS1020.CODIGO_CARTEIRA,PS1020.NOSSO_NUMERO, PS1020.NUMERO_REGISTRO, PS1020.NUMERO_CONTA_COBRANCA ,CFGEMPRESA.RAZAO_SOCIAL, ";
$query .= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
$query .= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . aspas($numRegistro);

$resPs1020 = jn_query($query);
$rowPs1020 = jn_fetch_object($resPs1020);

if ($rowPs1020->CODIGO_ASSOCIADO == "") {
	$query  = "Select PS1010.NOME_EMPRESA NOME , PS1010.NUMERO_CNPJ DOCUMENTO, ";
	$query .= "PS1001.ENDERECO, PS1001.CIDADE, PS1001.BAIRRO, PS1001.CEP, PS1001.ESTADO ";
	$query .= "From Ps1010 ";
	$query .= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
	$query .= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
} else {
	$query  = "SELECT PS1000.NOME_ASSOCIADO NOME , PS1000.NUMERO_CPF DOCUMENTO, PS1000.CODIGO_GRUPO_CONTRATO, ";
	$query .= "PS1001.ENDERECO, PS1001.CIDADE, PS1001.BAIRRO, PS1001.CEP, PS1001.ESTADO ";
	$query .= "From Ps1000 ";
	$query .= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
	$query .= "Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
}

$resPs1000 = jn_query($query);

$rowPs1000 = jn_fetch_object($resPs1000);

// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto   = 0; //2.95;
$data_venc     = SqlToData($rowPs1020->DATA_VENCIMENTO);
$venc_2via	   = date("d/m/Y", strtotime($rowPs1020->VENCIMENTO_2VIA));
$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".", $valor_cobrado);
$valor_boleto  = number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$databd=$venc_2via;
$databd= explode("/",$databd); 
$dataBol = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
$dias = ($data_atual-$dataBol )/86400;
$diasAtrazo  = ceil($dias);

$rowPs1020->CODIGO_IDENTIFICACAO_FAT = (int) $rowPs1020->CODIGO_IDENTIFICACAO_FAT; //Converter o n�mero para inteiro.
$dadosboleto["nosso_numero"] = ($NossoNumero_Registrado) ? $rowPs1020->CODIGO_IDENTIFICACAO_FAT : $rowPs1020->NUMERO_REGISTRO;  // Nosso numero - Modifica��o feita para utilizar o nosso n�mero enviado pelo banco!
$dadosboleto["numero_documento"]   = $rowPs1020->NUMERO_REGISTRO;         // Num do pedido ou do documento
$dadosboleto["data_vencimento"]    = $data_venc;                          // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"]     = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"]       = $valor_boleto;                       // Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula
$dadosboleto["mes_ano_referencia"] = $rowPs1020->MES_ANO_REFERENCIA;                       // Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"]    = $rowPs1000->NOME;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
    $dadosboleto["demonstrativo2"] = "REFERE-SE AO PAGAMENTO DO PLANO DE SAÚDE";

if ($_SESSION['MULTIDATABASE'] == 1) {
	if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 1) 
		$dadosboleto["demonstrativo3"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO COOPERSINPRO";
	if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 2)
		$dadosboleto["demonstrativo3"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO SINPRAFARMA ";
	if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 3)
		$dadosboleto["demonstrativo3"] = "DIX COLETIVO POR ADESÃO DO SEIBCSSP";
} 

if ($_SESSION['MULTIDATABASE'] == 2)
	$dadosboleto["demonstrativo2"] = "DIX COLETIVO POR ADESAO - SEIBCSSP";
elseif ($_SESSION['MULTIDATABASE'] == 3)
	$dadosboleto["idemonstrativo2"] = "UNIMED CAMPINAS COLETIVO POR ADESÃO – SINPRO CAMPINAS";      
elseif ($_SESSION['MULTIDATABASE'] == 4)
	$dadosboleto["demonstrativo2"] = "UNIMED CAMPINAS COLETIVO POR ADESAO USPESP";

//$dadosboleto["demonstrativo2"] = $taxa_boleto <= 0 ? '' :"Taxa bancária - R$ ".$taxa_boleto;
//$dadosboleto["demonstrativo3"] = "";

$dadosboleto["instrucoes1"] = $valida_nova_data ? '' : "REFERE-SE AO PAGAMENTO DO PLANO DE SAÚDE ";

//    if ($_SESSION['MULTIDATABASE'] == 1) {
//        if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 1) 
//            $dadosboleto["instrucoes2"] = "UNIMED PAULISTANA COLETIVO POR ADESAO SINPRO SP/COOPERSINPRO";
//        else 
//            $dadosboleto["instrucoes2"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO – SINPRAFARMA";
//    } 

if ($_SESSION['MULTIDATABASE'] == 1) {
	if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 1) 
		$dadosboleto["instrucoes2"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO COOPERSINPRO";
	if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 2)
		$dadosboleto["instrucoes2"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO SINPRAFARMA ";
	if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 3)
		$dadosboleto["instrucoes2"] = "DIX COLETIVO POR ADESÃO DO SEIBCSSP";
} 


if ($_SESSION['MULTIDATABASE'] == 2)
	$dadosboleto["instrucoes2"] = "DIX COLETIVO POR ADESAO - SEIBCSSP";
elseif ($_SESSION['MULTIDATABASE'] == 3)
	$dadosboleto["instrucoes2"] = "UNIMED CAMPINAS COLETIVO POR ADESÃO – SINPRO CAMPINAS";      
elseif ($_SESSION['MULTIDATABASE'] == 4)
	$dadosboleto["instrucoes2"] = "UNIMED CAMPINAS COLETIVO POR ADESAO USPESP";

$dadosboleto["instrucoes3"] = (
	$valida_nova_data ? "ESTE BOLETO DESTINA-SE A PAGAMENTO EXCLUSIVO " . date('d/m/Y') : "NÃO RECEBER APÓS 30 DIAS "
);
$dadosboleto["instrucoes4"] = (
	//$valida_nova_data ? '' : "APÓS O VENCIMENTO COBRAR MULTA DE 1%"
	$valida_nova_data ? '' : "APOS O VENCIMENTO COBRAR MULTA DE 2% SOBRE O VALOR DA MENSALIDADE E MORA DE 0,033% POR DIA DE ATRASO"
);

//$dadosboleto["instrucoes2"] = $valida_nova_data ? '' : "ESTA DATA NÃO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
//$dadosboleto["instrucoes3"] = $valida_nova_data ? '' : "ATRASOS SUP A 60DIAS CONSECUTIVOS OU NÃO NOS ULTIM. 12 MESES";

$queryContrato = 'SELECT CODIGO_BANCO, NUMERO_AGENCIA, NUMERO_CONTA FROM PS1002 WHERE CODIGO_ASSOCIADO = ' . aspas($rowPs1020->CODIGO_ASSOCIADO);
$resContrato = jn_query($queryContrato);
$rowContrato = jn_fetch_assoc($resContrato);

$dadosboleto["instrucoes4"] = 'O d&eacute;bito ocorrer&aacute; no banco: ' . $rowContrato['CODIGO_BANCO'] . ', Ag&ecirc;ncia: ' . $rowContrato['NUMERO_AGENCIA'] . ' e Conta: ' . $rowContrato['NUMERO_CONTA'];

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"]     = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"]         = "A";
$dadosboleto["especie"]        = "R$";
$dadosboleto["especie_doc"]    = "RC";

// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

$query = "Select codigo_banco, numero_conta_corrente,codigo_convenio, codigo_cedente, digito_verificador_conta, numero_agencia, codigo_carteira, digito_verificador_banco, codigo_cedente,numero_arquivo_debito, ";
$query.= "codigo_convenio, Nome_Cedente  ";
$query.= "From Ps7300 ";
$query.= "Where codigo_banco = '033'";
$query.= " AND PS7300.DATA_INUTILIZ_REGISTRO IS NULL ";
$query.= " AND FLAG_CONTA_WEB = 'S'";

$resPs7300 = jn_query($query);
$rowPs7300 = jn_fetch_object($resPs7300);

// DADOS PERSONALIZADOS - SANTANDER BANESPA
$dadosboleto["codigo_cliente"]     = substr($rowPs7300->CODIGO_CEDENTE,8 ,8);                           // Código do Cliente (PSK) (Somente 7 digitos)
$dadosboleto["ponto_venda"]        = $rowPs7300->NUMERO_AGENCIA;                                        // Ponto de Venda = Agencia
$dadosboleto["carteira"]           = (
	$rowPs7300->CODIGO_CARTEIRA ? $rowPs7300->CODIGO_CARTEIRA : '102'                                   // Cobrança Simples - SEM Registro
);                                                                                                      
$dadosboleto["carteira_descricao"] = "COBRANÇA SIMPLES";                                                // Descrição da Carteira

// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"]      = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"]      = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"]     = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"]       = $rowPs1020->RAZAO_SOCIAL;

// NÃO ALTERAR!

ob_start();
include("include/funcoes_conferencia.php"); 
include("include/layout_conferencia_pdf2.php");


$content = utf8_encode(ob_get_clean());

// convert


require_once('../lib/html2pdf/html2pdf.class.php');
try
{
	$html2pdf = new HTML2PDF('P','A4','pt', true, 'UTF-8');
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));//
	$html2pdf->Output('BoletoConf_' . $numRegistro . '.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}

?>
