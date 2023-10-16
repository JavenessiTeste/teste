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
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa                |
// |                                                                      |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//


echo 'oi'; exit;
require('../base.php');
require('../private/config.php');
require('../private/autentica.php');
require('../private/conecta_db.php');
include('../lib/sysutils.php');
include('../lib/sysutils_db.php');



$numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

$query = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, ";
$query.= "Ps1020.Codigo_Identificacao_Fat,        Ps1020.Nosso_Numero, Ps1020.Numero_Registro, CFGEMPRESA.RAZAO_SOCIAL, ";
$query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
$query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;

$resPs1020=ibase_query($query);

$rowPs1020=ibase_fetch_object($resPs1020);

if ($rowPs1020->CODIGO_ASSOCIADO=="")
{
	$query = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
	$query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";

	$query.= "NULL as Nome_Contratante, NULL as FLAG_NOME_RESPONSAVEL ";
	$query.= "From Ps1010 ";
	$query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
	$query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
}
else
{
	$query = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, ";
	$query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado, ";

	$query.= "Coalesce(Ps1002.Nome_contratante, Ps1000.Nome_Associado) AS Nome_Contratante, ";
	
	// flag que armazena se a cobrança sai em nome do responsavel ou do titular
	$query.= "(SELECT FIRST 1 CFG0001.FLAG_NOME_RESPONSAVEL FROM CFG0001) AS FLAG_NOME_RESPONSAVEL ";

	$query.= "From Ps1000 ";

	// Junção para trazer os dados do contratante...
	$query.= "Inner Join Ps1002 On (Ps1002.Codigo_Associado = Ps1000.Codigo_Titular) ";
	$query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
	$query.= "Where (Ps1000.Codigo_Associado = " . aspa($rowPs1020->CODIGO_ASSOCIADO) . ")";
}

$resPs1000=ibase_query($query);
$rowPs1000=ibase_fetch_object($resPs1000);

    // DADOS DO BOLETO PARA O SEU CLIENTE
    $dias_de_prazo_para_pagamento = 5;
    $taxa_boleto   = 0; //2.95;
    $data_venc     = date("d/m/Y", strtotime($rowPs1020->DATA_VENCIMENTO));
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
    
    
    
    if($diasAtrazo>0){
  
        $valor_boleto    = str_replace(",", ".", $valor_boleto);
        $multa = 2;
        $mora  = 0.033; 

        $valor_boleto_multa    =  (($valor_boleto * $multa) / 100) + $valor_boleto; 
        $valor_boleto          =  $valor_boleto_multa + (($valor_boleto * ($mora * $diasAtrazo))/ 100);
        $valor_boleto          =  number_format($valor_boleto, 2, ',', '');
        
        $data_venc=date('d/m/Y');
        
    }


// Busco os dados da conta...
$query = "Select codigo_banco, numero_conta_corrente, digito_verificador_conta, numero_agencia, digito_verificador_banco, codigo_cedente, ";
$query.= "codigo_convenio, Nome_Cedente, Codigo_Carteira ";
$query.= "From Ps7300 ";
$query.= "Where FLAG_CONTA_WEB = 'S' AND codigo_banco = '104'";

$resPs7300=ibase_query($query);
$rowPs7300=ibase_fetch_object($resPs7300);


// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 0; //2.95;
//$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias  OU  informe data: "13/04/2006"  OU  informe "" se Contra Apresentacao;
$data_venc = SqlToData($rowPs1020->DATA_VENCIMENTO);
$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["inicio_nosso_numero"] = $rowPs7300->CODIGO_CARTEIRA;  // Carteira SR: 80, 81 ou 82  -  Carteira CR: 90 (Confirmar com gerente qual usar)
$dadosboleto["nosso_numero"] = $rowPs1020->NUMERO_REGISTRO;  // Nosso numero sem o DV - REGRA: Máximo de 8 caracteres!
$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = ($rowPs1000->FLAG_NOME_RESPONSAVEL == 'S') ? $rowPs1000->NOME_CONTRATANTE : $rowPs1000->NOME;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
//$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
//$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ ".number_format($taxa_boleto, 2, ',', '');
//$dadosboleto["demonstrativo3"] = "";

$query  = 'SELECT CAST(percentual_multa_padrao AS NUMERIC(15,3)) AS percentual_multa_padrao, CAST(percentual_mora_diaria AS NUMERIC(15,3)) AS percentual_mora_diaria, ';
if ($rowPs1020->CODIGO_ASSOCIADO <> '') {    
    $query .= '    mensagem_fat_fam01 AS MSG01, mensagem_fat_fam02 AS MSG02, mensagem_fat_fam03 AS MSG03, mensagem_fat_fam04 AS MSG04 ';
} else {
    $query .= '    mensagem_fat_emp01 AS MSG01, mensagem_fat_emp02 AS MSG02, mensagem_fat_emp03 AS MSG03, mensagem_fat_emp04 AS MSG04 ';
}
$query .= 'FROM cfg0001 ';
$res = ibase_query($query);
//pr($result, true);



$result = ibase_fetch_object($res);


$dadosboleto["instrucoes5"] = "Após vencimento cobrar mora diária de R$ " . toMoeda(($rowPs1020->VALOR_FATURA *  $result->PERCENTUAL_MORA_DIARIA), false, false);
$dadosboleto["instrucoes6"] = "Após vencimento cobrar multa de R$ " . toMoeda(($rowPs1020->VALOR_FATURA *  $result->PERCENTUAL_MULTA_PADRAO), false, false);

$dadosboleto["instrucoes1"] = $result->MSG01;
$dadosboleto["instrucoes2"] = $result->MSG02;
$dadosboleto["instrucoes3"] = $result->MSG03;
$dadosboleto["instrucoes4"] = $result->MSG04;	



// INSTRUÇÕES PARA O CAIXA
$dadosboleto["instrucoes1"] = "NÃO RECEBER APÓS 60 DIAS ";
$dadosboleto["instrucoes2"] = "ESTA DATA NÃO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
$dadosboleto["instrucoes3"] = "ATRASOS SUP A 60DIAS CONSECUTIVOS OU NÃO NOS ULTIM. 12 MESES";
$dadosboleto["instrucoes4"] = "APÓS O VENCIMENTO COBRAR MULTA DE 2%";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "";		
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - CEF
$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - CEF
$dadosboleto["conta_cedente"] = ""; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = ""; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = "SR";  // Código da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)

// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = "UNIDENTAL";

// NÃO ALTERAR!
//include("include/funcoes_cef.php"); 
//include("include/layout_cef.php");include("include/funcoes_cef_sinco.php"); include("include/layout_cef_sinco.php");
?>
