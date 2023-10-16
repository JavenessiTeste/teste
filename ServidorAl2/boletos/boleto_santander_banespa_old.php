<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote se não, escreva para:                                    |
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
// +----------------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>         |
// | Desenvolvimento Boleto Santander-Banespa : Fabio R. Lenharo                |
// +----------------------------------------------------------------------------+
// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

	require('../base.php');
	require('../private/config.php');
	require('../private/autentica.php');
	require('../private/conecta_db.php');
	include('../lib/sysutils.php');
	include('../lib/sysutils_db.php');
   $NossoNumero_Registrado = rvc('UTILIZAR_NOSSO_NUMERO_BANCO','SIM');
   $Atualizado = rvc('GERAR_BOLETO_ATUALIZADO', 'SIM');


   $query = "SELECT PERCENTUAL_MULTA_PADRAO FROM CFG0001";
   $resMulta=jn_query($query);
   $rowMulta=jn_fetch_assoc($resMulta);

   $multa = $rowMulta['PERCENTUAL_MULTA_PADRAO'];

   $numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];
   $query = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, ";
   $query.= "Ps1020.Codigo_Identificacao_Fat,        Ps1020.Nosso_Numero, Ps1020.Numero_Registro, CFGEMPRESA.RAZAO_SOCIAL, ";
   $query.= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
   $query.= "From Ps1020, CFGEMPRESA Where Numero_Registro = " . $numRegistro;

   $resPs1020=jn_query($query);
   $rowPs1020=jn_fetch_object($resPs1020);

   if ($rowPs1020->CODIGO_ASSOCIADO=="")   {       $query = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
       $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
       $query.= "From Ps1010 ";
       $query.= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
       $query.= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
   }   else   {       $query = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, ";
       $query.= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
       $query.= "From Ps1000 ";
       $query.= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
       $query.= "Where (Ps1000.Codigo_Associado = " . aspa($rowPs1020->CODIGO_ASSOCIADO) . ")";
   }
   $resPs1000=jn_query($query);

   $rowPs1000=jn_fetch_object($resPs1000);
// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//
// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 0;
 //2.95
 $data_venc =  SqlToData($rowPs1020->DATA_VENCIMENTO);


$data_ex = explode('/',$data_venc);
$dia_v = $data_ex[0];
$mes_v = $data_ex[1];
$ano_v = $data_ex[2];
$dif_dias_limite = time() - mktime(0,0,0,$mes_v,$dia_v,$ano_v );
$valida_nova_data = ( $Atualizado && ( time() > mktime(0,0,0,$mes_v,$dia_v,$ano_v ) ) );
//&& ( $dif_dias > 2592000 ) );
$data_venc = $valida_nova_data ? date('d/m/Y') : $data_venc;
$valor_cobrado = ( $Atualizado && ( time() > mktime(0,0,0,$mes_v,$dia_v,$ano_v ) ) ) ? ( ($rowPs1020->VALOR_FATURA * $multa ) + $rowPs1020->VALOR_FATURA ) : $rowPs1020->VALOR_FATURA;
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

//$dadosboleto["nosso_numero"] = Str_pad($rowPs1020->NUMERO_REGISTRO,13,'0',STR_PAD_LEFT);  // Nosso numero - REGRA: Máximo de 13 caracteres!
//$dadosboleto["nosso_numero"] = $rowPs1020->NUMERO_REGISTRO;  // Nosso numero - Modificação feita para se adequar a validação do banco!
$rowPs1020->CODIGO_IDENTIFICACAO_FAT = (int) $rowPs1020->CODIGO_IDENTIFICACAO_FAT;//Converter o número para inteiro.
$dadosboleto["nosso_numero"] = ($NossoNumero_Registrado) ? $rowPs1020->CODIGO_IDENTIFICACAO_FAT : $rowPs1020->NUMERO_REGISTRO;  // Nosso numero - Modificação feita para utilizar o nosso número enviado pelo banco!
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
$dadosboleto["demonstrativo2"] = $taxa_boleto <= 0 ? '' :"Taxa bancária - R$ ".$taxa_boleto;
$dadosboleto["demonstrativo3"] = "";
/*$dadosboleto["instrucoes1"] = $valida_nova_data ? "ESTE BOLETO DESTINA-SE A PAGAMENTO EXCLUSIVO ".date('d/m/Y') : "NÃO RECEBER APÓS 30 DIAS ";
$dadosboleto["instrucoes2"] = $valida_nova_data ? '' : "ESTA DATA NÃO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE";
$dadosboleto["instrucoes3"] = $valida_nova_data ? '' : "ATRASOS SUP A 60DIAS CONSECUTIVOS OU NÃO NOS ULTIM. 12 MESES";
$dadosboleto["instrucoes4"] = $valida_nova_data ? '' : "APÓS O VENCIMENTO COBRAR MULTA DE 2%";
*/

$dadosboleto["instrucoes1"] = $valida_nova_data ? 'ESTE BOLETO DESTINA-SE A PAGAMENTO EXCLUSIVO '.date('d/m/Y') : 'APÓS O VENC., PAGÁVEL SOMENTE NO BANCO (SANTANDER, ITAÚ)';
$dadosboleto["instrucoes2"] = $valida_nova_data ? '' : 'COM MULTA DE 2% SOBRE O VALOR DA MENSALIDADE E MORA DE 0,033% POR DIA DE ATRASO.';
$dadosboleto["instrucoes3"] = $valida_nova_data ? '' : 'ESTA DATA NÃO EXIME O CANCELAMENTO DO CONTRATO EM CASO DE';
$dadosboleto["instrucoes4"] = $valida_nova_data ? '' : 'ATRASOS SUP A 60DIAS CONSECUTIVOS OU NÃO NOS ULTIM. 12 MESES';

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "A";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "RC";

// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //
$query = "Select codigo_banco, numero_conta_corrente,codigo_convenio, codigo_cedente, digito_verificador_conta, numero_agencia, codigo_carteira, digito_verificador_banco, codigo_cedente,numero_arquivo_debito, ";
$query.= "codigo_convenio, Nome_Cedente  ";
$query.= "From Ps7300 ";
$query.= "Where CONTA_WEB = 'S'";

$resPs7300=jn_query($query);
$rowPs7300=jn_fetch_object($resPs7300);

/*// DADOS DA SUA CONTA - REAL
$dadosboleto["agencia"] = $rowPs7300->NUMERO_AGENCIA;
 // Num da agencia, sem digito$dadosboleto["conta"] = $rowPs7300->NUMERO_CONTA_CORRENTE;
 	// Num da conta, sem digito$dadosboleto["carteira"] = "57";
  // Código da Carteira
*/
// DADOS PERSONALIZADOS - SANTANDER BANESPA
$dadosboleto["codigo_cliente"] = substr($rowPs7300->CODIGO_CEDENTE,8 ,8);
 // Código do Cliente (PSK) (Somente 7 digitos)$dadosboleto["ponto_venda"] = $rowPs7300->NUMERO_AGENCIA;
 // Ponto de Venda = Agencia$dadosboleto["carteira"] = $rowPs7300->CODIGO_CARTEIRA ? $rowPs7300->CODIGO_CARTEIRA : '102';
  // Cobrança Simples - SEM Registro$dadosboleto["carteira_descricao"] = "COBRANÇA SIMPLES";
  // Descrição da Carteira
// SEUS DADOS
$dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
$dadosboleto["cpf_cnpj"] = $rowPs1020->NUMERO_CNPJ;
$dadosboleto["endereco"] = $rowPs1020->ENDERECO;
$dadosboleto["cidade_uf"] = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
$dadosboleto["cedente"] = $rowPs1020->RAZAO_SOCIAL;

// NÃO ALTERAR!
include("include/funcoes_santander_banespa.php");
include("include/layout_santander_banespa.php");
?>
