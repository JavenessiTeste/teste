<?php
header ('Content-type: text/html; charset=ISO-8859-1');
require('../lib/base.php');
$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

if($numRegistro){
	$query = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, ";
	$query.= "Ps1020.Codigo_Identificacao_Fat,  ps1020.NUMERO_CONTA_COBRANCA,  Ps1020.Nosso_Numero, Ps1020.Numero_Registro, CFGEMPRESA.RAZAO_SOCIAL, ";
	$query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
	$query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;
}else{
	exit;
}

$resPs1020=jn_query($query);

$rowPs1020=jn_fetch_object($resPs1020);

if($rowPs1020->DATA_PAGAMENTO){
	echo 'O boleto com vencimento em ' . SqlToData($rowPs1020->DATA_VENCIMENTO) . ', foi pago no dia ' . SqlToData($rowPs1020->DATA_PAGAMENTO) . '!';
	exit;
}
	
	
if ($rowPs1020->CODIGO_ASSOCIADO=="") {	
   $query = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
   $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";
   $query.= "Ps1010.Flag_PlanoFamiliar ";
   $query.= "From Ps1010 ";
   $query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
   $query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
} else {	
   $query = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, ";
   $query.= " Coalesce(Ps1001.Endereco, Ps1015.Endereco) Endereco, Coalesce(Ps1001.Cidade, Ps1015.Cidade) Cidade, ";
   $query.= " Coalesce(Ps1001.Bairro, Ps1015.Bairro) Bairro, Coalesce(Ps1001.Cep, Ps1015.Cep) Cep, Coalesce(Ps1001.Estado, Ps1015.Estado) Estado, ";
   $query.= "Ps1000.Flag_PlanoFamiliar, Ps1000.Codigo_Grupo_faturamento ";
   $query.= "From Ps1000 ";
   $query.= "Left Outer Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
   $query.= "Left Outer Join Ps1015 On (Ps1000.Codigo_Associado = Ps1015.Codigo_Associado) ";
   $query.= "Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";   
}

$resPs1000=jn_query($query);

$rowPs1000=jn_fetch_object($resPs1000);

$query =  'SELECT NOME_PESSOA ';
$query .= 'FROM ps2500 ';
$query .= 'WHERE nosso_numero_boleto = \'' . $numRegistro . '\'';

$resPs2500=jn_query($query);
$rowPs2500=jn_fetch_object($resPs2500);

// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 0; //2.95
$data_venc = SqlToData($rowPs1020->DATA_VENCIMENTO);
$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

/*
 * Pego os dados da conta, com base nos parametros
 * parametros configurados
 */

$query = "Select codigo_banco, numero_conta_corrente, digito_verificador_conta, numero_agencia, digito_verificador_banco, codigo_cedente, ";
$query.= "codigo_convenio, Nome_Cedente, Digito_verificador_agencia  ";
$query.= "From Ps7300 ";
$query.= "Where FLAG_CONTA_WEB = 'S' ";
//$query.= " AND NUMERO_CONTA_CORRENTE = " . aspas($rowPs1020->NUMERO_CONTA_COBRANCA);

if ($rowPs1000->FLAG_PLANOFAMILIAR == 'S') {
    $lPreNumero = '4';
    $armazena_grupo = $rowPs1000->CODIGO_GRUPO_FATURAMENTO;
        
    $conta_grupo_faturamento = rvc('CONTA_GRUPO_FATURAMENTO');

    // conta padrao para pessoa fisica...
    $_conta = rvc('BOLETO_WEB_CONTA_PF');
    $arrGContas = explode(';', $conta_grupo_faturamento);
    
    foreach((array) $arrGContas as $item) {
        $arrDet = explode(':', trim($item));

        if ($arrDet[0] == $armazena_grupo) {
            $_conta =  $arrDet[1];
        }
    }
 } else {
    $lPreNumero = '9';    
    $_conta = rvc('BOLETO_WEB_CONTA_PJ');
}

if ($_conta <> '') {
    $query.= "AND Numero_Conta_Corrente = '$_conta'";
}

//pr($query, true);
$resPs7300=jn_query($query);

//$dadosboleto["nosso_numero"] = $lPreNumero . Str_pad($rowPs1020->NUMERO_REGISTRO,10,'0',STR_PAD_LEFT);  // Nosso numero - REGRA: Máximo de 13 caracteres!
$dadosboleto["nosso_numero"] = Str_pad($rowPs1020->NUMERO_REGISTRO,10,'0',STR_PAD_LEFT);  // Nosso numero - REGRA: Máximo de 13 caracteres!


$dadosboleto["numero_documento"] = $rowPs1020->CODIGO_ASSOCIADO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME . ' - ' . $rowPs1000->DOCUMENTO;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ ".$taxa_boleto;
$dadosboleto["demonstrativo3"] = "";

//$dadosboleto["instrucoes1"] = "APÓS VENCIMENTO COBRAR MORA DIÁRIA DE ". toMoeda((0.00033 * $rowPs1020->VALOR_FATURA), true, false);
/*
$dadosboleto["instrucoes1"] = "*** Valores expressos em R$ ***";
$_percDesconto = getDescontoBoletoBenef($rowPs1020->CODIGO_ASSOCIADO);
if ($_percDesconto) {
    $dadosboleto["instrucoes2"] = "DESCONTO DE R$ : " . toMoeda(($rowPs1020->VALOR_FATURA * ($_percDesconto / 100)), false, false) . " ATÉ " . $data_venc . " - SR.CAIXA, FAVOR CONCEDER O DESCONTO ATÉ O VENC.";
}
*/

$query  = 'SELECT CAST(percentual_multa_padrao AS NUMERIC(15,3)) AS percentual_multa_padrao, CAST(percentual_mora_diaria AS NUMERIC(15,3)) AS percentual_mora_diaria, ';
if ($rowPs1020->CODIGO_ASSOCIADO <> '') {    
    $query .= '    mensagem_fat_fam01 AS MSG01, mensagem_fat_fam02 AS MSG02, mensagem_fat_fam03 AS MSG03, mensagem_fat_fam04 AS MSG04 ';
} else {
    $query .= '    mensagem_fat_emp01 AS MSG01, mensagem_fat_emp02 AS MSG02, mensagem_fat_emp03 AS MSG03, mensagem_fat_emp04 AS MSG04 ';
}
$query .= 'FROM cfg0001 ';
$result = jn_execute($query, true);
//pr($result, true);

/*
$dadosboleto["instrucoes3"] = "Após vencimento cobrar mora diária de R$ " . toMoeda(($rowPs1020->VALOR_FATURA * $result[0]['PERCENTUAL_MORA_DIARIA']), false, false);
$dadosboleto["instrucoes4"] = "Após vencimento cobrar multa de R$ " . toMoeda(($rowPs1020->VALOR_FATURA * $result[0]['PERCENTUAL_MULTA_PADRAO']), false, false);

$dadosboleto["instrucoes5"] = $result[0]['MSG01']; */

if ($rowPs2500->NOME_PESSOA){
    $dadosboleto["instrucoes1"] = "FRANQUIA REFERENTE AO TRATAMENTO DE " . $rowPs2500->NOME_PESSOA;
    $dadosboleto["instrucoes2"] = "NÃO RECEBER APÓS 60 DIAS ";
    $dadosboleto["instrucoes3"] = "ESTA DATA NÃO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
    $dadosboleto["instrucoes4"] = "ATRASOS SUP A 60DIAS CONSECUTIVOS OU NÃO NOS ULTIM. 12 MESES";
    $dadosboleto["instrucoes5"] = "APÓS O VENCIMENTO COBRAR MULTA DE 2%";
} else{
    $dadosboleto["instrucoes1"] = "TEXTO DE RESPONSABILIDADE DO CEDENTE";
    $dadosboleto["instrucoes2"] = "NÃO RECEBER APÓS 60 DIAS ";
    $dadosboleto["instrucoes3"] = "ESTA DATA NÃO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
    $dadosboleto["instrucoes4"] = "ATRASOS SUP A 60DIAS CONSECUTIVOS OU NÃO NOS ULTIM. 12 MESES";
    $dadosboleto["instrucoes5"] = "APÓS O VENCIMENTO COBRAR MULTA DE " . toMoeda(($rowPs1020->VALOR_FATURA * $result[0]['PERCENTUAL_MULTA_PADRAO']), false, false);
    $dadosboleto["instrucoes6"] = "APÓS O VENCIMENTO COBRAR MORA DIÁRIA " . toMoeda(($rowPs1020->VALOR_FATURA * $result[0]['PERCENTUAL_MORA_DIARIA']), false, false);
    
}

/*
$dadosboleto["instrucoes6"] = $result[0]['MSG02'];
$dadosboleto["instrucoes7"] = $result[0]['MSG03'];
$dadosboleto["instrucoes8"] = $result[0]['MSG04'];
*/
// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "Não";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

$rowPs7300=jn_fetch_object($resPs7300);

// DADOS DA SUA CONTA - Bradesco
$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$dadosboleto["agencia_dv"] = ''; //$rowPs7300->DIGITO_VERIFICADOR_AGENCIA; // Digito do Num da agencia
$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = ''; //$rowPs7300->DIGITO_VERIFICADOR_CONTA; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - Bradesco
$dadosboleto["conta_cedente"] = $rowPs7300->NUMERO_CONTA_CORRENTE; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = '3'; //$rowPs7300->DIGITO_VERIFICADOR_CONTA; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = "09";  // Código da Carteira: pode ser 06 ou 03

// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = 'Cooperativa Evidente – 02.947.180/0001-49 - Rua Dr Cardoso de almeida, 579'; //$rowPs1020->RAZAO_SOCIAL;


ob_start();
// NÃO ALTERAR!
include("include/funcoes_bradesco.php");
include("include/layout_bradesco_Evidente.php");

$content = utf8_encode(ob_get_clean());


require_once('../lib/html2pdf/html2pdf.class.php');
try
{
	//$html2pdf = new HTML2PDF('P','A4','fr', array(0, 0, 0, 0));
	
	$html2pdf = new HTML2PDF('P','A4','pt', true, 'UTF-8');
	
	$html2pdf->pdf->SetDisplayMode('real');
	
	/* Parametro vuehtml = true desabilita o pdf para desenvolvimento do layout */
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));//
	
	/* Abrir no navegador */
	$html2pdf->Output('boleto' . $numRegistro . '.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}

?>
