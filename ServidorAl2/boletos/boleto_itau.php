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

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Ita�: Glauber Portella                        |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc)	//
mb_internal_encoding("UTF-8"); 
mb_http_output( "iso-8859-1" );  
ob_start("mb_output_handler");   
header("Content-Type: text/html; charset=ISO-8859-1",true);
date_default_timezone_set('America/Sao_Paulo'); 

require('../lib/base.php');

//require('../private/autentica.php');

    $numRegistro = (isset($_POST["numeroRegistro"])) ? $_POST["numeroRegistro"] : $_GET["numeroRegistro"];

    $query  = "Select Ps1020.Codigo_Empresa, Ps1020.Codigo_Associado, Ps1020.Data_Vencimento, Ps1020.Data_Emissao, Ps1020.Valor_Fatura, ";
	$query .= " case EXTRACT( WEEKDAY FROM ps1020.data_vencimento) when 0 then dateadd(1 day to ps1020.data_vencimento)  when 6 then dateadd(2 day to ps1020.data_vencimento) else ps1020.data_vencimento end AS vencimento_2via, ";
    $query .= "Ps1020.Codigo_Identificacao_Fat,PS1020.Codigo_Carteira,Ps1020.Nosso_Numero, Ps1020.Numero_Registro,PS1020.Data_Pagamento, Ps1020.Numero_Conta_Cobranca ,CFGEMPRESA.RAZAO_SOCIAL, ";
    $query .= "CFGEMPRESA.NUMERO_CNPJ, CFGEMPRESA.ENDERECO, CFGEMPRESA.BAIRRO, CFGEMPRESA.CIDADE, CFGEMPRESA.ESTADO  ";
    $query .= "From Ps1020, CFGEMPRESA Where PS1020.Data_Pagamento is null and Numero_Registro = " . $numRegistro;

    $resPs1020 = jn_query($query);
    if ($rowPs1020 = jn_fetch_object($resPs1020)){
		
	}else{		
		header('Location: ../frm_emissao_boletos.php'); 
	}

    if ($rowPs1020->CODIGO_ASSOCIADO == "") {
        $query  = "Select Ps1010.Nome_Empresa Nome , Ps1010.Numero_Cnpj Documento, ";
        $query .= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
        $query .= "From Ps1010 ";
        $query .= "Inner Join Ps1001 On (Ps1010.Codigo_Empresa = Ps1001.Codigo_Empresa) ";
        $query .= "Where (Ps1010.Codigo_Empresa = " . $rowPs1020->CODIGO_EMPRESA . ")";
    } else {
        $query  = "Select Ps1000.Nome_Associado Nome , Ps1000.Numero_Cpf Documento, ps1000.codigo_grupo_contrato, ";
        $query .= "Ps1001.Endereco, Ps1001.Cidade, Ps1001.Bairro, Ps1001.Cep, Ps1001.Estado ";
        $query .= "From Ps1000 ";
        $query .= "Inner Join Ps1001 On (Ps1000.Codigo_Associado = Ps1001.Codigo_Associado) ";
        $query .= "Where (Ps1000.Codigo_Associado = " . aspas($rowPs1020->CODIGO_ASSOCIADO) . ")";
    }

    $resPs1000 = jn_query($query);

    $rowPs1000 = jn_fetch_object($resPs1000);
	
	
    if ($rowPs1020->CODIGO_ASSOCIADO == "") {	
      if ($rowPs1020->CODIGO_EMPRESA != $UsuarioLogado['CODIGO']){
		
		header('Location: ../frm_emissao_boletos.php');   
	  } 
	}	

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

    $dadosboleto["nosso_numero"] = (
        (is_null($rowPs1020->CODIGO_IDENTIFICACAO_FAT) or ($rowPs1020->CODIGO_CARTEIRA == '138')) ? $rowPs1020->NUMERO_REGISTRO : $rowPs1020->CODIGO_IDENTIFICACAO_FAT
    );                                                                        // Nosso numero - REGRA: M�ximo de 13 caracteres!
    
    $dadosboleto["numero_documento"]   = $rowPs1020->NUMERO_REGISTRO;         // Num do pedido ou do documento
    $dadosboleto["data_vencimento"]    = $data_venc;                          // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
    $dadosboleto["data_documento"]     = SqlToData($rowPs1020->DATA_EMISSAO); // Data de emiss�o do Boleto
    $dadosboleto["data_processamento"] = SqlToData($rowPs1020->DATA_EMISSAO); // Data de processamento do boleto (opcional)
    $dadosboleto["valor_boleto"]       = $valor_boleto;                       // Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula

    // DADOS DO SEU CLIENTE
    $dadosboleto["sacado"]    = $rowPs1000->NOME;
    $dadosboleto["endereco1"] = $rowPs1000->ENDERECO;
    $dadosboleto["endereco2"] = $rowPs1000->BAIRRO . " - " . $rowPs1000->CIDADE . " - " . $rowPs1000->ESTADO . " - CEP : " . $rowPs1000->CEP;

    // INFORMACOES PARA O CLIENTE
    $dadosboleto["demonstrativo1"] = "Segunda via do boleto de pagamento";

    if ($_SESSION['MULTIDATABASE'] == 5)
        $dadosboleto["demonstrativo2"] = "REFERE-SE AO PAGAMENTO DO PLANO ODONTOLOGICO";
    else
        $dadosboleto["demonstrativo2"] = "REFERE-SE AO PAGAMENTO DO PLANO DE SAUDE ";

//    if ($_SESSION['MULTIDATABASE'] == 1) {
//        if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 1)
//            $dadosboleto["demonstrativo3"] = "UNIMED PAULISTANA COLETIVO POR ADESAO SINPRO SP/COOPERSINPRO";
//        else
//            $dadosboleto["demonstrativo3"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO – SINPRAFARMA";
//    }

    if ($_SESSION['MULTIDATABASE'] == 1) {
        if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 1) 
            $dadosboleto["demonstrativo3"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO COOPERSINPRO";
        if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 2)
            $dadosboleto["demonstrativo3"] = "UNIMED PAULISTANA COLETIVO POR ADESÃO SINPRAFARMA ";
        if ($rowPs1000->CODIGO_GRUPO_CONTRATO == 3)
            $dadosboleto["demonstrativo3"] = "DIX COLETIVO POR ADESÃO DO SEIBCSSP";
    } 
    
    if ($_SESSION['MULTIDATABASE'] == 2)
        $dadosboleto["demonstrativo3"] = "DIX COLETIVO POR ADESAO - SEIBCSSP";
    elseif ($_SESSION['MULTIDATABASE'] == 3)
        $dadosboleto["demonstrativo3"] = "UNIMED CAMPINAS COLETIVO POR ADESÃO – SINPRO CAMPINAS";   
    elseif ($_SESSION['MULTIDATABASE'] == 4)
        $dadosboleto["demonstrativo3"] = "UNIMED CAMPINAS COLETIVO POR ADESÃO – USPESP";
    elseif ($_SESSION['MULTIDATABASE'] == 5)
        $dadosboleto["demonstrativo3"] = "UNIODONTO SÃO JOSÉ DOS CAMPOS ";
       
    if ($_SESSION['MULTIDATABASE'] == 5)  
        $dadosboleto["instrucoes1"] = "REFERE-SE AO PAGAMENTO DO PLANO ODONTOLOGICO";
    else
        $dadosboleto["instrucoes1"] = "REFERE-SE AO PAGAMENTO DO PLANO DE SAUDE";
    
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
    elseif ($_SESSION['MULTIDATABASE'] == 5)
        $dadosboleto["instrucoes2"] = "UNIODONTO SÃO JOSÉ DOS CAMPOS ";
             
    $dadosboleto["instrucoes3"] = "APOS O VENCIMENTO, PAGAVEL SOMENTE NO ITAU.";
    $dadosboleto["instrucoes4"] = "APOS O VENCIMENTO COBRAR MULTA DE 2% SOBRE O VALOR DA MENSALIDADE E MORA DE 0,033% POR DIA DE ATRASO";
    //$dadosboleto["instrucoes4"] = "APOS O VENCIMENTO, COBRAR MULTA DE 1%.NAO RECEBER APOS 30 DIAS ";
    //12/05/2011 - PAULO - ALTERAÇÃO PARA VIDAMAX

    // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
    $dadosboleto["quantidade"]     = "";
    $dadosboleto["valor_unitario"] = "";
    $dadosboleto["aceite"]         = "A";
    $dadosboleto["especie"]        = "R$";
    $dadosboleto["especie_doc"]    = "DM";

    // ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //
    $query  = "Select codigo_banco, numero_conta_corrente, digito_verificador_conta, numero_agencia, digito_verificador_banco, codigo_cedente, ";
    $query .= "codigo_convenio, Nome_Cedente, Codigo_Carteira, CODIGO_EMPRESA_BANCO  ";
    $query .= "From Ps7300 ";
    $query .= "Where codigo_banco = '341'";
    $query .= "AND PS7300.DATA_INUTILIZ_REGISTRO IS NULL ";
    $query .= "AND PS7300.NUMERO_CONTA_CORRENTE = ". $rowPs1020->NUMERO_CONTA_COBRANCA . " ";
    //$query.= "Where FLAG_CONTA_WEB = 'S'";
    //pr($query,true);
    $resPs7300 = jn_query($query);
    $rowPs7300 = jn_fetch_object($resPs7300);

    $dadosboleto["agencia"]  = $rowPs7300->NUMERO_AGENCIA; // Num da agencia, sem digito
    $dadosboleto["conta"]    = preg_replace('/[\-]{1}[0-9]{1}/','',$rowPs7300->NUMERO_CONTA_CORRENTE); 	// Num da conta, sem digito
    $size_conta              = count($dadosboleto["conta"])-1;
    $dadosboleto["conta_dv"] = $rowPs7300->DIGITO_VERIFICADOR_CONTA ? $rowPs7300->DIGITO_VERIFICADOR_CONTA : $dadosboleto["conta"][$size_conta];  // C�digo da Carteira
    $dadosboleto["conta"] 	 = $rowPs7300->DIGITO_VERIFICADOR_CONTA ? $dadosboleto["conta"] : substr($dadosboleto["conta"],-7,6 );

    // DADOS PERSONALIZADOS - ITAÚ
    if  (!is_null($rowPs1020->CODIGO_CARTEIRA))
        $dadosboleto["carteira"] = $rowPs1020->CODIGO_CARTEIRA;
    else
        $dadosboleto["carteira"] = $rowPs7300->CODIGO_CARTEIRA;

    // SEUS DADOS
    $dadosboleto["identificacao"] = $rowPs7300->NOME_CEDENTE;
    $dadosboleto["cpf_cnpj"]      = $rowPs1020->NUMERO_CNPJ;
    $dadosboleto["endereco"]      = $rowPs1020->ENDERECO;
    $dadosboleto["cidade_uf"]     = $rowPs1020->CIDADE . " - " . $rowPs1020->ESTADO;
    $dadosboleto["cedente"]       = $rowPs1020->RAZAO_SOCIAL;

    // NÃO ALTERAR!
    include("include/funcoes_itau.php"); 
    include("include/layout_itau.php");
?>
