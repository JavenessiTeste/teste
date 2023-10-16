<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa				        |
// | 														                                   			  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +--------------------------------------------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>              		             				|
// | Desenvolvimento Boleto Banco do Brasil: Daniel William Schultz / Leandro Maniezo / Rog�rio Dias Pereira|
// +--------------------------------------------------------------------------------------------------------+


// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc)	//

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

   $rowPs1020=jn_fetch_assoc($resPs1020);

   if ($rowPs1020['CODIGO_ASSOCIADO']=="")
   {
       $query = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
       $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
       $query.= "From Ps1010 ";
       $query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
       $query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020['CODIGO_EMPRESA'] . ")";
   }
   else
   {
       $query = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, ";
       $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
       $query.= "From Ps1000 ";
       $query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
       $query.= "Where (Ps1000.Codigo_Associado = " . aspa($rowPs1020['CODIGO_ASSOCIADO']) . ")";
   }

   $resPs1000=jn_query($query);

   $rowPs1000=jn_fetch_assoc($resPs1000);



// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 0;
$data_venc = SqlToData($rowPs1020['DATA_VENCIMENTO']);
$valor_cobrado =  $rowPs1020['VALOR_FATURA'];
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto= number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
$dadosboleto["nosso_numero"] = substr($rowPs1020['NUMERO_REGISTRO'],1);
$dadosboleto["numero_documento"] = $rowPs1020['NUMERO_REGISTRO'];
$dadosboleto["data_vencimento"] = $data_venc;
$dadosboleto["data_documento"] = SqlToData($rowPs1020['DATA_EMISSAO']);
$dadosboleto["data_processamento"] = SqlToData($rowPs1020['DATA_EMISSAO']);
$dadosboleto["valor_boleto"] = $valor_boleto;

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $rowPs1000['NOME'];
$dadosboleto["endereco1"] = $rowPs1000['ENDERECO'];
$dadosboleto["endereco2"] = $rowPs1000['BAIRRO'] . " - " . $rowPs1000['CIDADE'] . " - " . $rowPs1000['ESTADO'] . " - CEP : " . $rowPs1000['CEP'];

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";
$dadosboleto["demonstrativo2"] = "Taxa banc�ria - R$ ".$taxa_boleto;
$dadosboleto["demonstrativo3"] = "";

// INSTRU��ES PARA O CAIXA
$dadosboleto["instrucoes1"] = "N�O RECEBER AP�S 60 DIAS ";
$dadosboleto["instrucoes2"] = "ESTA DATA N�O EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
$dadosboleto["instrucoes3"] = "ATRASOS SUP A 60DIAS CONSECUTIVOS OU N�O NOS ULTIM. 12 MESES";
$dadosboleto["instrucoes4"] = "AP�S O VENCIMENTO COBRAR MULTA DE 2%";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "N";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "RC";


// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //

$query = "Select codigo_banco, codigo_carteira, numero_conta_corrente, digito_verificador_conta, numero_agencia, digito_verificador_banco, codigo_cedente, ";
$query.= "codigo_convenio, Nome_Cedente  ";
$query.= "From Ps7300 ";
$query.= "Where CONTA_WEB = 'S'";
$resPs7300=jn_query($query);
$rowPs7300=jn_fetch_assoc($resPs7300);

// DADOS DA SUA CONTA - BANCO DO BRASIL
$dadosboleto["agencia"] = $rowPs7300['NUMERO_AGENCIA'];
$dadosboleto["conta"] = str_pad($rowPs7300['NUMERO_CONTA_CORRENTE'], 8, "0", STR_PAD_LEFT);

// DADOS PERSONALIZADOS - BANCO DO BRASIL
$dadosboleto["convenio"] = $rowPs7300['CODIGO_CONVENIO'];  // Num do conv�nio - REGRA: 6 ou 7 ou 8 d�gitos
$dadosboleto["contrato"] = $rowPs7300['CODIGO_CEDENTE']; // Num do seu contrato

$dadosboleto["carteira"] = $rowPs7300['CODIGO_CARTEIRA'];
$dadosboleto["variacao_carteira"] = "";  // Varia��o da Carteira, com tra�o (opcional)

// TIPO DO BOLETO
$dadosboleto["formatacao_convenio"] = strlen($rowPs7300['CODIGO_CONVENIO']); // REGRA: 8 p/ Conv�nio c/ 8 d�gitos, 7 p/ Conv�nio c/ 7 d�gitos, ou 6 se Conv�nio c/ 6 d�gitos
$dadosboleto["formatacao_nosso_numero"] = "1"; // REGRA: Usado apenas p/ Conv�nio c/ 6 d�gitos: informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos

/*
#################################################
DESENVOLVIDO PARA CARTEIRA 18

- Carteira 18 com Convenio de 8 digitos
  Nosso n�mero: pode ser at� 9 d�gitos

- Carteira 18 com Convenio de 7 digitos
  Nosso n�mero: pode ser at� 10 d�gitos

- Carteira 18 com Convenio de 6 digitos
  Nosso n�mero:
  de 1 a 99999 para op��o de at� 5 d�gitos
  de 1 a 99999999999999999 para op��o de at� 17 d�gitos

#################################################
*/


// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300['NOME_CEDENTE'];
$dadosboleto["cpf_cnpj"] = $rowPs1020['NUMERO_CNPJ'];
$dadosboleto["endereco"] = $rowPs1020['ENDERECO'];
$dadosboleto["cidade_uf"] = $rowPs1020['CIDADE'] . " - " . $rowPs1020['ESTADO'];
$dadosboleto["cedente"] = $rowPs1020['RAZAO_SOCIAL'];

// N�O ALTERAR!
include("include/funcoes_bb.php"); 
include("include/layout_bb.php");
?>
