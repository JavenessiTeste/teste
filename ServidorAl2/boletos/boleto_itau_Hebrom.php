<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				        |
// | 														                                   			  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Itaú: Glauber Portella                        |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//
require('../lib/base.php');

$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

$query = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, ";
$query.= "Ps1020.Codigo_Identificacao_Fat, Ps1020.Codigo_Banco, Ps1020.Numero_Conta_Cobranca, Ps1020.Codigo_Identificacao_Fat,  Ps1020.Nosso_Numero, Ps1020.Numero_Registro, Ps1020.Codigo_Carteira, CFGEMPRESA.RAZAO_SOCIAL, ";
$query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
$query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;

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
   $query = "Select coalesce(Ps1002.Nome_Contratante, Ps1000.Nome_Associado) Nome , Ps1000.Numero_Cpf Documento, ";
   $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";
   $query.= "Ps1000.Flag_PlanoFamiliar, Ps1000.Codigo_Grupo_faturamento ";
   $query.= "From Ps1000 ";
   $query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
   $query.= "Left Outer Join Ps1002 On (Ps1000.Codigo_Associado = Ps1002.Codigo_Associado) ";
   $query.= "Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
}

//pr($query,false);

$resPs1000=jn_query($query);

$rowPs1000=jn_fetch_object($resPs1000);

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

$query = " Select codigo_banco, numero_conta_corrente, digito_verificador_conta, numero_agencia, digito_verificador_banco, codigo_cedente, ";
$query.= " codigo_convenio, Nome_Cedente, Digito_verificador_agencia  ";
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

$dadosboleto["codigo_associado"] = $rowPs1020->CODIGO_ASSOCIADO;
$dadosboleto["nosso_numero"] = substr($rowPs1020->NOSSO_NUMERO, 0, 8); 
$dadosboleto["nosso_numero"] = substr($rowPs1020->NOSSO_NUMERO, 3);; 
$dadosboleto["nosso_numeroCompleto"] = $rowPs1020->NOSSO_NUMERO; 


$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME;
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

$dadosboleto["instrucoes3"] = "Ap&oacute;s vencimento cobrar mora di&aacute;ria de R$ " . toMoeda(($rowPs1020->VALOR_FATURA * $result[0]['PERCENTUAL_MORA_DIARIA']), false, false);
$dadosboleto["instrucoes4"] = "Ap&oacute;s vencimento cobrar multa de R$ " . toMoeda(($rowPs1020->VALOR_FATURA * $result[0]['PERCENTUAL_MULTA_PADRAO']), false, false);

$dadosboleto["instrucoes5"] = $result[0]['MSG01'];
$dadosboleto["instrucoes6"] = $result[0]['MSG02'];
$dadosboleto["instrucoes7"] = $result[0]['MSG03'];
$dadosboleto["instrucoes8"] = $result[0]['MSG04'];

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "Não";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

$rowPs7300=jn_fetch_object($resPs7300);

// DADOS DA SUA CONTA - Itau
$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$dadosboleto["agencia_dv"] = $rowPs7300->DIGITO_VERIFICADOR_AGENCIA; // Digito do Num da agencia
$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - Itau
$dadosboleto["conta_cedente"] = $rowPs7300->NUMERO_CONTA_CORRENTE; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; // Digito da ContaCedente do Cliente
//$dadosboleto["carteira"] = "03";  // Código da Carteira: pode ser 06 ou 03
$dadosboleto["carteira"] = $rowPs1020->CODIGO_CARTEIRA;

// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = $rowPs1020->RAZAO_SOCIAL;

// NÃO ALTERAR!
ob_start();
include("include/funcoes_itau_Hebrom.php"); 
include("include/layout_itau_Hebrom.php");
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
