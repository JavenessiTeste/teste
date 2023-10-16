<?php
header ('Content-type: text/html; charset=ISO-8859-1');
require('../lib/base.php');


$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

if($numRegistro){
	$query = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, Ps1020.numero_linha_digitavel, ";
	$query.= "Ps1020.Identificacao_Geracao, Ps1020.Codigo_Identificacao_Fat, Ps1020.Codigo_Banco, Ps1020.Numero_Conta_Cobranca, Ps1020.Codigo_Identificacao_Fat,  Ps1020.Nosso_Numero, Ps1020.Numero_Registro, Ps1020.Codigo_Carteira, CFGEMPRESA.RAZAO_SOCIAL, ";
	$query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
	$query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;
}else{
	exit;
}

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
   $query = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, ";
   $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";
   $query.= "Ps1000.Flag_PlanoFamiliar, Ps1000.Codigo_Grupo_faturamento ";
   $query.= "From Ps1000 ";
   $query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
   $query.= "Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
}

$resPs1000=jn_query($query);

$rowPs1000=jn_fetch_object($resPs1000);

// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
//$dias_de_prazo_para_pagamento = 5;
//$taxa_boleto = 0; //2.95
//$data_venc = SqlToData($rowPs1020->DATA_VENCIMENTO);
//pr($data_venc,true);
//$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
//$valor_cobrado = str_replace(",", ".",$valor_cobrado);
//$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$query  = 'SELECT CAST(percentual_multa_padrao AS NUMERIC(15,5)) AS percentual_multa_padrao, CAST(percentual_mora_diaria AS NUMERIC(15,5)) AS percentual_mora_diaria, ';
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
    
	/*
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
*/

if($rowPs1020->IDENTIFICACAO_GERACAO == 'SEG-VIA-CART'){
	$dadosboleto["instrucoes4"] = "Após vencimento cobrar multa de 2% ";
	$dadosboleto["instrucoes3"] = "Após vencimento cobrar mora diária de 0,333% ";
}else{
	$dadosboleto["instrucoes3"] = "Após vencimento cobrar mora diária de R$ " . toMoeda(($valor_boleto * $result[0]['PERCENTUAL_MORA_DIARIA']), false, false);
	$dadosboleto["instrucoes4"] = "Após vencimento cobrar multa de R$ "       . toMoeda(($valor_boleto * $result[0]['PERCENTUAL_MULTA_PADRAO']), false, false);
}

$dadosboleto["instrucoes5"] = $result[0]['MSG01'];
$dadosboleto["instrucoes6"] = $result[0]['MSG02'];
$dadosboleto["instrucoes7"] = $result[0]['MSG03'];
$dadosboleto["instrucoes8"] = $result[0]['MSG04'];	





/*
 * Pego os dados da conta, com base nos parametros
 * parametros configurados
 */

$query = "Select * ";
$query.= " ";
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
$rowPs7300=jn_fetch_object($resPs7300);

if(!function_exists('formata_numdoc'))
{
	function formata_numdoc($num,$tamanho)
	{
		while(strlen($num)<$tamanho)
		{
			$num="0".$num; 
		}
	return $num;
	}
}


$IdDoSeuSistemaAutoIncremento = $rowPs1020->NUMERO_REGISTRO; // Deve informar um numero sequencial a ser passada a função abaixo, Até 6 dígitos
$agencia = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$conta = $rowPs7300->NUMERO_CONTA_CORRENTE; // Num da conta, sem digito

$NossoNumero = formata_numdoc($IdDoSeuSistemaAutoIncremento,7);
$qtde_nosso_numero = strlen($NossoNumero);
$sequencia = formata_numdoc($agencia,4).formata_numdoc(str_replace("-","",$conta),10).formata_numdoc($NossoNumero,7);
$cont=0;
$calculoDv = '';
for($num=0;$num<=strlen($sequencia);$num++)
{
	$cont++;
	if($cont == 1)
	{
		// constante fixa Sicoob » 3197 
		$constante = 3;
	}
	if($cont == 2)
	{
		$constante = 1;
	}
	if($cont == 3)
	{
		$constante = 9;
	}
	if($cont == 4)
	{
		$constante = 7;
		$cont = 0;
	}
	$calculoDv = $calculoDv + (substr($sequencia,$num,1) * $constante);
}
$Resto = $calculoDv % 11;
$Dv = 11 - $Resto;
if ($Dv == 0) $Dv = 0;
if ($Dv == 1) $Dv = 0;
if ($Dv > 9) $Dv = 0;
$dadosboleto["nosso_numero"] = $NossoNumero;











$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;
$dadosboleto["numero_linha_digitavel"] = $rowPs1020->NUMERO_LINHA_DIGITAVEL;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
//$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ ".$taxa_boleto;
$dadosboleto["demonstrativo3"] = "";
//$dadosboleto["instrucoes1"] = "APÓS VENCIMENTO COBRAR MORA DIÁRIA DE ". toMoeda((0.00033 * $rowPs1020->VALOR_FATURA), true, false);
$dadosboleto["instrucoes1"] = "*** Valores expressos em R$ ***";
//$_percDesconto = getDescontoBoletoBenef($rowPs1020->CODIGO_ASSOCIADO);
//if ($_percDesconto) {
//    $dadosboleto["instrucoes2"] = "DESCONTO DE R$ : " . toMoeda(($rowPs1020->VALOR_FATURA * ($_percDesconto / 100)), false, false) . " ATÉ " . $data_venc . " - SR.CAIXA, FAVOR CONCEDER O DESCONTO ATÉ O VENC.";
//}



// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "1";
$dadosboleto["valor_unitario"] = $valor_boleto;
$dadosboleto["aceite"] = "Não";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //




$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$dadosboleto["agencia_dv"] = $rowPs7300->DIGITO_VERIFICADOR_AGENCIA; // Digito do Num da agencia
$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; 	// Digito do Num da conta

$dadosboleto["conta_cedente"] = $rowPs7300->NUMERO_CONTA_CORRENTE; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; // Digito da ContaCedente do Cliente
//$dadosboleto["carteira"] = "03";  // Código da Carteira: pode ser 06 ou 03

//if ($rowPs1020->CODIGO_EMPRESA == '400'){
//	$dadosboleto["carteira"] = "03";
//} else {
//	$dadosboleto["carteira"] = "09";
//};

$dadosboleto["carteira"] = $rowPs1020->CODIGO_CARTEIRA;


$dadosboleto["codigo_cliente"] = $rowPs7300->CODIGO_CEDENTE; // Código do Cliente (PSK) (Somente 7 digitos)
$dadosboleto["ponto_venda"] = $rowPs7300->NUMERO_AGENCIA; // Ponto de Venda = Agencia
$dadosboleto["carteira"] = $rowPs7300->CODIGO_CARTEIRA;  // Cobrança Simples - SEM Registro
$dadosboleto["carteira_descricao"] = "COBRANÇA SIMPLES";  // Descrição da Carteira



// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = $rowPs1020->RAZAO_SOCIAL;


// NÃO ALTERAR!
//header("Content-Type: text/html; charset=UTF-8", true);
//include("include/funcoes_itau.php"); 
//include("include/layout_itau.php");

ob_start();
//header("Content-Type: text/html; charset=UTF-8", true);
// NÃO ALTERAR!
include("include/funcoes_sicoob_Propulsao.php");
include("include/layout_sicoob.php");

$content = utf8_encode(ob_get_clean());

// convert


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
	$html2pdf->Output('boleto' . $numRegistro . '.pdf');
	
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
