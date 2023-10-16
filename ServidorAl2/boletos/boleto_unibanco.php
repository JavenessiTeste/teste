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
// | Desenvolvimento Boleto Unibanco: Elizeu Alcantara                    |
// +----------------------------------------------------------------------+


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
   
   $resPs1020=jn_query($query);
   
   $rowPs1020=jn_fetch_object($resPs1020);

   if ($rowPs1020->CODIGO_ASSOCIADO=="")
   {
       $query = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
       $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
       $query.= "From Ps1010 ";
       $query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
       $query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
   }
   else
   {
       $query = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, ";
       $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
       $query.= "From Ps1000 ";
       $query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
       $query.= "Where (Ps1000.Codigo_Associado = " . aspa($rowPs1020->CODIGO_ASSOCIADO) . ")";
   }

   $resPs1000=jn_query($query);

   $rowPs1000=jn_fetch_object($resPs1000);
// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 0; //2.95;
$data_venc = date("d/m/Y", strtotime($rowPs1020->DATA_VENCIMENTO)); 
$valor_cobrado = $rowPs1020->VALOR_FATURA; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["nosso_numero"] = Str_pad($rowPs1020->NUMERO_REGISTRO,13,'0',STR_PAD_LEFT);  // Nosso numero - REGRA: Mï¿½ximo de 13 caracteres!
$dadosboleto["numero_documento"] = $rowPs1020->NUMERO_REGISTRO;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emissï¿½o do Boleto
$dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vï¿½rgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000->NOME;
$dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
$dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
$dadosboleto["demonstrativo2"] = "Taxa bancaria - R$ ".$taxa_boleto;
$dadosboleto["demonstrativo3"] = "";
$dadosboleto["instrucoes1"] = "NAO RECEBER APOS 60 DIAS ";
$dadosboleto["instrucoes2"] = "ESTA DATA NAO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
$dadosboleto["instrucoes3"] = "ATRASOS SUP A 60DIAS CONSECUTIVOS OU NAO NOS ULTIM. 12 MESES";
$dadosboleto["instrucoes4"] = "";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "A";		
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

$query = "Select codigo_banco, numero_conta_corrente, digito_verificador_conta, numero_agencia, digito_verificador_banco, codigo_cedente, ";
$query.= "codigo_convenio, Nome_Cedente, Codigo_Carteira, CODIGO_EMPRESA_BANCO  ";
$query.= "From Ps7300 ";
$query.= "Where FLAG_CONTA_WEB = 'S'";

$resPs7300=jn_query($query);
$rowPs7300=jn_fetch_object($resPs7300);

// DADOS DA SUA CONTA - UNIBANCO
$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE; 	// Num da conta, sem digito
//$dadosboleto["conta_dv"] = "57";  // Código da Carteira
$size_conta = count($dadosboleto["conta"])-1;
$dadosboleto["conta_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA ? $rowPs7300->DIGITO_VERIFICADOR_CONTA : $dadosboleto["conta"][$size_conta];  // Código da Carteira
$dadosboleto["conta"] 	 = $rowPs7300->DIGITO_VERIFICADOR_CONTA ? $dadosboleto["conta"] : substr($dadosboleto["conta"],-7,6 );

// DADOS PERSONALIZADOS - UNIBANCO
$dadosboleto["codigo_cliente"] = $rowPs7300->CODIGO_EMPRESA_BANCO; //CODIGO_CEDENTE;
$dadosboleto["carteira"] = $rowPs7300->CODIGO_CARTEIRA;


// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;

$dadosboleto["cedente"] = $rowPs1020->RAZAO_SOCIAL;



// NÃO ALTERAR!
include("include/funcoes_unibanco.php"); 
include("include/layout_unibanco.php");

?>
