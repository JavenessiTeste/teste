<?php
header ('Content-type: text/html; charset=ISO-8859-1');
require('../lib/base.php');


$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

if($numRegistro){
	$query = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, ";
	$query.= "Ps1020.Identificacao_Geracao, Ps1020.Codigo_Identificacao_Fat, Ps1020.Codigo_Banco, Ps1020.Numero_Conta_Cobranca, Ps1020.Codigo_Identificacao_Fat,  Ps1020.Nosso_Numero, Ps1020.Numero_Registro, Ps1020.Codigo_Carteira, CFGEMPRESA.RAZAO_SOCIAL, ";
	$query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO, CFGEMPRESA.CODIGO_SMART, CFGEMPRESA.NUMERO_INSC_SUSEP  ";
	$query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;
}else{
	exit;
}

$resPs1020=jn_query($query);

$rowPs1020=jn_fetch_object($resPs1020);

if ($rowPs1020->CODIGO_ASSOCIADO == "") {
   $query = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
   $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";
   $query.= "Ps1010.Flag_PlanoFamiliar ";
   $query.= "From Ps1010 ";
   $query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
   $query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
} else {
   $query = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, Ps1000.Data_Admissao, ";
   $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";
   $query.= "Ps1000.Flag_PlanoFamiliar, Ps1000.Codigo_Grupo_faturamento, Ps1030.Codigo_Cadastro_Ans, Ps1030.Nome_Plano_Familiares, PS1000.CODIGO_GRUPO_CONTRATO, PS1000.CODIGO_GRUPO_PESSOAS, PS1014.NOME_GRUPO_PESSOAS, PS1014.NUMERO_CNPJ ";
   $query.= "From Ps1000 ";
   $query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
   $query.= "Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ";
   $query.= "INNER JOIN PS1014 ON PS1000.CODIGO_GRUPO_PESSOAS = PS1014.CODIGO_GRUPO_PESSOAS ";
   $query.= "Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
}

$resPs1000=jn_query($query);

$rowPs1000=jn_fetch_object($resPs1000);

$grupoPessoa = $rowPs1000->CODIGO_GRUPO_CONTRATO;	

$grupoANAF = false;
if($rowPs1000->CODIGO_GRUPO_CONTRATO == '9' and $rowPs1000->CODIGO_GRUPO_PESSOAS == '8'){
	$grupoANAF = true;
}

// ------------------------- DADOS DINAMICOS DO SEU CLIENTE PARA A GERACAO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulario c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE

$query  = 'SELECT CAST(percentual_multa_padrao AS NUMERIC(15,5)) AS percentual_multa_padrao, CAST(percentual_mora_diaria AS NUMERIC(15,5)) AS percentual_mora_diaria, ';
if ($rowPs1020->CODIGO_ASSOCIADO <> '') {    
    $query .= '    mensagem_fat_fam01 AS MSG01, mensagem_fat_fam02 AS MSG02, mensagem_fat_fam03 AS MSG03, mensagem_fat_fam04 AS MSG04 ';
} else {
    $query .= '    mensagem_fat_emp01 AS MSG01, mensagem_fat_emp02 AS MSG02, mensagem_fat_emp03 AS MSG03, mensagem_fat_emp04 AS MSG04 ';
}
$query .= 'FROM cfg0001 ';
$result = sqlExecute($query, true);

// DADOS DO BOLETO PARA O SEU CLIENTE

$dias_de_prazo_para_pagamento = 5;
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
    

if($diasAtrazo > 0){
	$dadosboleto["instrucoes3"] = " ESTE BOLETO DESTINA-SE A PAGAMENTO EXCLUSIVO <br>
									ATE A DATA EXPRESSA NO CAMPO VENCIMENTO";
	$dadosboleto["instrucoes4"] = " VALOR COBRAO ATUALIZADO COM JUROS, MULTA E <br>
									DEDUCOES DEVIDOS.";
}else{	
	if($rowPs1020->IDENTIFICACAO_GERACAO == 'SEG-VIA-CART'){
		$dadosboleto["instrucoes4"] = "Após vencimento cobrar multa de 2% ";
		$dadosboleto["instrucoes3"] = "Após vencimento cobrar mora diária de 0,333% ";
	}else{
		$dadosboleto["instrucoes3"] = "Após vencimento cobrar mora diária de R$ " . toMoeda(($valor_boleto * $result[0]['PERCENTUAL_MORA_DIARIA']), false, false);
		$dadosboleto["instrucoes4"] = "Após vencimento cobrar multa de R$ "       . toMoeda(($valor_boleto * $result[0]['PERCENTUAL_MULTA_PADRAO']), false, false);
	}
}

$dadosboleto["instrucoes5"] = $result[0]['MSG01'];
$dadosboleto["instrucoes6"] = $result[0]['MSG02'];
$dadosboleto["instrucoes7"] = $result[0]['MSG03'];
$dadosboleto["instrucoes8"] = $result[0]['MSG04'];	


if ($rowPs1020->NUMERO_CONTA_COBRANCA == '' or $rowPs1020->CODIGO_IDENTIFICACAO_FAT == '' or $rowPs1020->CODIGO_CARTEIRA == '')
	exit;

$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;	// Num do pedido ou do documento
$dadosboleto["codigo_associado"] = $rowPs1020->CODIGO_ASSOCIADO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = SqltoData($rowPs1020->DATA_VENCIMENTO); // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_vencimento2"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto_original; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula
$dadosboleto["valor_boleto2"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula
$dadosboleto["nosso_numero"] = substr($rowPs1020->NUMERO_REGISTRO, 0, 11); 

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
$dadosboleto["demonstrativo3"] = "";


$dadosboleto["instrucoes1"] = $result[0]['MSG01'];
$dadosboleto["instrucoes2"] = $result[0]['MSG02'];
$dadosboleto["instrucoes3"] = '';
$dadosboleto["instrucoes4"] = 'APÓS VENCIMENTO COBRAR MULTA DE 2% + MORA DE 0,033% AO DIA';


// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "Não";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


if ($grupoANAF == true){
	$query = "Select descricao_carteira,codigo_banco, numero_conta_corrente,codigo_convenio, codigo_cedente, digito_verificador_conta, numero_agencia, codigo_carteira, digito_verificador_banco, codigo_cedente,numero_arquivo_debito, ";
	$query.= "codigo_convenio, Nome_Cedente  ";
	$query.= "From Ps7300 ";
	if($rowPs1020->NUMERO_CONTA_COBRANCA){
		$query.= "Where numero_conta_corrente = " . aspas($rowPs1020->NUMERO_CONTA_COBRANCA);
	}else{
		$query.= "Where numero_conta_corrente = '13003774'";
	}
}else{
	$query = "Select descricao_carteira,codigo_banco, numero_conta_corrente,codigo_convenio, codigo_cedente, digito_verificador_conta, numero_agencia, codigo_carteira, digito_verificador_banco, codigo_cedente,numero_arquivo_debito, ";
	$query.= "codigo_convenio, Nome_Cedente  ";
	$query.= "From Ps7300 ";
	if($rowPs1020->NUMERO_CONTA_COBRANCA){
		$query.= "Where numero_conta_corrente = " . aspas($rowPs1020->NUMERO_CONTA_COBRANCA);
	}else{
		$query.= "Where numero_conta_corrente = '13003786'";
	}	
}

$resPs7300=jn_query($query);
$rowPs7300=jn_fetch_object($resPs7300);

$dadosboleto["codigo_cliente"] = $rowPs7300->CODIGO_CEDENTE; // Código do Cliente (PSK) (Somente 7 digitos)
$dadosboleto["ponto_venda"] = $rowPs7300->NUMERO_AGENCIA; // Ponto de Venda = Agencia
$dadosboleto["carteira"] = $rowPs7300->CODIGO_CARTEIRA;  // Cobrança Simples - SEM Registro
$dadosboleto["carteira_descricao"] = $rowPs7300->DESCRICAO_CARTEIRA;  // Descrição da Carteira



// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;

if ($grupoANAF == true) {
	$dadosboleto["cpf_cnpj"] = $rowPs1000->NUMERO_CNPJ;
	$dadosboleto["endereco"] = $rowPs1000->ENDERECO;
	$dadosboleto["cidade_uf"] = $rowPs1000->CIDADE . " - " . $rowPs1020->ESTADO;
	$dadosboleto["cedente"] = 'ASSOC NAC FUNCIONARIOS E PROF  08.698.605/0001-00';
}else {
	$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
	$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
	$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
	$dadosboleto["cedente"] = $rowPs1020->RAZAO_SOCIAL;
}

$queryContratante = 'SELECT NOME_CONTRATANTE FROM PS1002 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
$resContratante = jn_query($queryContratante);
$rowContratante = jn_fetch_object($resContratante);

if($rowContratante->NOME_CONTRATANTE != ''){
	$dadosboleto["sacado"] = $rowContratante->NOME_CONTRATANTE;	
}

$dadosboleto["mensagemTopUm"] = 'Imprima em impressora jato de tinta (ink jet) ou laser em qualidade normal ou alta (Não use modo econômico).';
$dadosboleto["mensagemTopDois"] = 'Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens mínimas à esquerda e à direita do formulário.';
$dadosboleto["mensagemTopTres"] = 'Corte na linha indicada. Não rasure, risque, fure ou dobre a região onde se encontra o código de barras.';


$queryOperadora  = ' SELECT NOME_OPERADORA FROM PS1000 ';
$queryOperadora .= ' LEFT OUTER JOIN ESP0002 ON(ESP0002.CODIGO_GRUPO_CONTRATO = PS1000.CODIGO_GRUPO_CONTRATO)';
$queryOperadora .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($rowPs1020->CODIGO_ASSOCIADO);
$resOperadora = jn_query($queryOperadora);
$rowOperadora = jn_fetch_object($resOperadora);
$dadosboleto["nome_operadora"] = $rowOperadora->NOME_OPERADORA;

// NÃO ALTERAR!

ob_start();
include("include/funcoes_santander_banespa.php");
include("include/layout_santander_banespa_Hebrom.php");

$content = utf8_encode(ob_get_clean());

require_once('../lib/html2pdf/html2pdf.class.php');
try
{
	$html2pdf = new HTML2PDF('P','A4','pt', true, 'UTF-8');
	
	/* Abre a tela de impressão */
	$html2pdf->pdf->SetDisplayMode('real');
	
	/* Parametro vuehtml = true desabilita o pdf para desenvolvimento do layout */
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));//
	
	/* Abrir no navegador */
	$html2pdf->Output('boleto' . $numRegistro . '.pdf');
	
	/* Força o download no browser */
	//$html2pdf->Output('boleto.pdf', 'D');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}

function calcularIdade($date){	
	if(!$date){
		return null;
	}
	
    // separando yyyy, mm, ddd
    list($ano, $mes, $dia) = explode('-', $date);

    // data atual
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

    // cálculo
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
}
?>
