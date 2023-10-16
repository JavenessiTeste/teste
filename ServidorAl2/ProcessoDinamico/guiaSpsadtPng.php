<?php
require('../lib/base.php');		
header("Content-Type: text/html; charset=ISO-8859-1",true);

    $numero_Autorizacao = $_SESSION['numero_autorizacao_guias'];
	
	if ($numero_Autorizacao == ''){
		$numero_Autorizacao = $_GET['numero'];
	}
	
	if($numero_Autorizacao==''){
		exit;
	}
	if(retornaValorCFG0003('CALCULA_COPART_AUT_WEB')=='SIM'){
		require_once('../EstruturaEspecifica/coparticipacaoDana.php');
		geraMensagemCoparticipacaoDana($numero_Autorizacao);
	}
	
//	pr($numero_Autorizacao,true);
	
    $Query  = 'SELECT * ';
    $Query .= 'FROM vw_guias_sadt ';
    $Query .= 'WHERE numero_autorizacao =' . aspas($numero_Autorizacao);
   
   
   if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
		$Query .= " And CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']);
	}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){
		$Query .= " And CODIGO_PRESTADOR_CONTRATADO = " . aspas($_SESSION['codigoIdentificacao']);
	}elseif(retornaValorConfiguracao('VALIDA_PARAMETRO_VW_GUIAS') == 'SIM'){
		$Query .= " And CODIGO_ASSOCIADO = " . aspas($_GET['cod']);
	}
	
	//pr($Query,true);
	$res = jn_query($Query);

    $guia = null;
    if ($row = jn_fetch_object($res)) {
        
        $guia = array(
            'NumeroAutorizacao'             => $row->NUMERO_AUTORIZACAO,
            'NomeEmpresa'                   => $row->NOME_EMPRESA,
            'RazaoSocial'                   => $row->RAZAO_SOCIAL,
            'RegistroANS'                   => $row->NUMERO_INSC_SUSEP,
            'NumeroGuia'                    => $row->NUMERO_GUIA,
            'NumeroGuiaPrincipal'           => '',
            'TipoGuia'                      => $row->TIPO_GUIA,
            'DataAutorizacao'               => SqlToData($row->DATA_AUTORIZACAO),
            'HorarioAutorizacao'            => $row->HORARIO_AUTORIZACAO,
            'DataValidade'                  => SqlToData($row->DATA_VALIDADE_SENHA),
            'NumeroCateirinha'              => $row->CODIGO_ASSOCIADO,
            'ValidadeCarteirinha'           => SqlToData($row->DATA_VALIDADE_CARTEIRINHA),
            'TipoEletivaUrgencia'           => $row->TIPO_ELETIVA_URGENCIA,
            'NomeBeneficiario'              => $row->NOME_ASSOCIADO,
            'CodigoPrestadorContrat'        => $row->CODIGO_PRESTADOR_CONTRATADO,
            'CodigoPrestadorExec'           => $row->CODIGO_PRESTADOR_EXECUTANTE,
            'CodigoPrestadorSolic'          => $row->CODIGO_SOLICITANTE,
            'NumeroCNS'                     => $row->CODIGO_CNS,

            'DataProcedimento'              => $row->DATA_PROCEDIMENTO,
            'TipoDoenca'                    => $row->TIPO_DOENCA,
            'TempoDoenca'                   => $row->TEMPO_DOENCA,
            'UnidadeTempoDoenca'            => $row->UNIDADE_TEMPO_DOENCA,
            
            'CodigoCid'                     => $row->CODIGO_CID,
            'CodigoCNESSolic'               => $row->CODIGO_CNES_SOLIC,
            'CodigoCNESContrat'             => $row->CODIGO_CNES_CONTRAT,
            'CodigoCNESExec'                => $row->CODIGO_CNES_EXEC,

            'NomePlano'                     => $row->NOME_PLANO_FAMILIARES,

            'NomePrestadorExec'             => $row->NOME_PRESTADOR_EXEC,
            'PrestadorExecCPF'              => $row->NUMERO_CPF_EXEC,
            'PrestadorExecCNPJ'             => $row->NUMERO_CNPJ_EXEC,
            'PrestadorExecCodConselho'      => $row->CODIGO_CONSELHO_PROFISS_EXEC,
            'PrestadorExecUFConselho'       => $row->UF_CONSELHO_PROFISS_EXEC,
            'PrestadorExecNumConselho'      => $row->NUMERO_CRM_EXEC,
            'PrestadorExecTipoPessoa'       => $row->TIPO_PESSOA_EXEC,
            'PrestadorExecCBOS'             => $row->CODIGO_ESPECIALIDADE_TISS_EXEC,

            'NomePrestadorSolic'            => $row->NOME_PRESTADOR_SOLIC,
            'NomePrestadorSolicAux'         => $row->NOME_PRESTADOR_SOLICITANTE_AUX,
            'PrestadorSolicCPF'             => $row->NUMERO_CPF_SOLIC,
            'PrestadorSolicCNPJ'            => $row->NUMERO_CNPJ_SOLIC,
            'PrestadorSolicCodConselho'     => $row->CODIGO_CONSELHO_PROFISS_SOLIC,
            'PrestadorSolicUFConselho'      => $row->UF_CONSELHO_PROFISS_SOLIC,
            'PrestadorSolicNumConselho'     => $row->NUMERO_CRM_SOLIC,
            'PrestadorSolicTipoPessoa'      => $row->TIPO_PESSOA_SOLIC,
            'PrestadorSolicCBOS'            => $row->CODIGO_ESPECIALIDADE_TISS_SOLIC,

            'NomePrestadorContrat'          => $row->NOME_PRESTADOR_CONTRAT,
            'PrestadorContratCPF'           => $row->NUMERO_CPF_CONTRAT,
            'PrestadorContratCNPJ'          => $row->NUMERO_CNPJ_CONTRAT,
            'PrestadorContratCodConselho'   => $row->CODIGO_CONSELHO_PROFISS_CONTRAT,
            'PrestadorContratUFConselho'    => $row->UF_CONSELHO_PROFISS_CONTRAT,
            'PrestadorContratNumConselho'   => $row->NUMERO_CRM_CONTRAT,
            'PrestadorContratTipoPessoa'    => $row->TIPO_PESSOA_CONTRAT,

            'EnderecoContrat'               => $row->ENDERECO_CONTRAT,
            'CidadeContrat'                 => $row->CIDADE_CONTRAT,
            'EstadoContrat'                 => $row->ESTADO_CONTRAT,
            'TelefoneContrat'               => $row->TELEFONE_CONTRAT,
            'CepContrat'                    => $row->CEP_CONTRAT,
            'CodigoMunicipioIBGEContrat'    => $row->CODIGO_MUNICIPIO_IBGE_CONTRAT,
			'FlagAtendimentoRn'    			=> $row->FLAG_ATENDIMENTO_RN,
			'FlagPrevisaoOpme'    			=> $row->FLAG_PREVISAO_OPME,
			'FlagPrevisaoQuimioterapia'	    => $row->FLAG_PREVISAO_QUIMIOTERAPIA,
			'NumeroGuiaOperadora'	    	=> $row->NUMERO_GUIA_OPERADORA,
			'TipoSaida'	    				=> $row->CODIGO_TIPO_SAIDA,
			'TipoAtendimento'	    		=> $row->CODIGO_TIPO_ATENDIMENTO,
			'TipoConsulta'	    			=> $row->TIPO_CONSULTA,
			'IndicacaoAcidente'	    		=> $row->INDICADOR_ACIDENTE,
			'DescricaoObservacao'	    	=> $row->DESCRICAO_OBSERVACAO

        );
        
        if($_SESSION['codigoSmart'] == '4316'){
            $guia['DescricaoObservacao']        = 'Plano Tratamento:' . $row->CODIGO_PLANO_TRATAMENTO;
        }

		if($_SESSION['codigoSmart'] == '3419'){
			$guia['NomePrestadorSolic']         = $row->SOLIC_RAZAO_SOCIAL_NM_COMPLETO;
			$guia['NOME_PRESTADOR_SOLICITANTE'] = $row->NOME_PRESTADOR_SOLICITANTE;
			$guia['ESTADO_CONSELHO_CLASSE']     = $row->ESTADO_CONSELHO_CLASSE;
			$guia['NUMERO_CONSELHO_CLASSE']     = $row->NUMERO_CONSELHO_CLASSE;
			$guia['CODIGO_CONSELHO_PROFISS']    = $row->CODIGO_CONSELHO_PROFISS;
		}

    }
    else {
        echo 'falha no processamento.';
        exit();
    }


	// Este campo pode ser o codigo do prestador na operadora, ou seu cnpj ou seu CPF
	if($guia['PrestadorExecCNPJ'] <> ''){
	   $codPrestadorExec = $guia['PrestadorExecCNPJ'];
	}
	else {
	   $codPrestadorExec = $guia['PrestadorExecCPF'];
	}
	
	if($guia['PrestadorSolicCNPJ'] <> ''){
	   $codPrestadorSolic = $guia['PrestadorSolicCNPJ'];
	}
	else {
	   $codPrestadorSolic = $guia['PrestadorSolicCPF'];
	}
	
	if($guia['PrestadorContratCNPJ'] <> ''){
	   $codPrestadorContrat = $guia['PrestadorContratCNPJ'];
	}
	else {
	   $codPrestadorContrat = $guia['PrestadorContratCPF'];
	}
	
	//pr($guia['TipoAtendimento'],true);
	
    /*
     *  pega o codigo tiss da tabela de procedimentos que esta no contrato do
     * prestador contratado
     */

    $TabelaProc = ''; // por padrao vem vazio

    $query  = 'SELECT ';
    $query .= '   PS5211.codigo_na_tiss ';
    $query .= 'FROM ';
    $query .= '    ps5211 ';
    $query .= 'WHERE ';
    $query .= '    PS5211.referencia_tabela = ( ';
    $query .= '        select ';
    $query .= '            PS5002.referencia_tabela_exames ';
    $query .= '        FROM ';
    $query .= '            ps5002 ';
    $query .= '        WHERE ';
    $query .= '            PS5002.codigo_prestador = ' . $guia['CodigoPrestadorContrat'];
    $query .= '    ) ';

     if ($res = jn_query($query)) {
         if ($row = jn_fetch_row($res)) {
            $TabelaProc = $row[0];
         }
     }



    if($_GET['janelaAbrir'] != '' && $_SESSION['type_db'] != ''){           

        if($_SESSION['type_db'] == 'sqlsrv'){

            $inicio = ((($_GET['janelaAbrir'] - 1) * 5) + 1);
            $fim = $inicio + 4;

            $buf  = ' WITH retornaAutorizacoes AS  ( ';
            $buf .= '   SELECT  PS6510.CODIGO_PROCEDIMENTO, PS5210.NOME_PROCEDIMENTO, PS6510.QUANTIDADE_PROCEDIMENTOS,  ';
            $buf .= '    ROW_NUMBER() OVER (ORDER BY PS6510.CODIGO_PROCEDIMENTO) AS RowNumber ';
            $buf .= '    FROM PS6510 ';
            $buf .= '    Inner Join Ps5210 On (Ps6510.codigo_Procedimento = Ps5210.Codigo_Procedimento) ';
            $buf .= '    WHERE Ps6510.Situacao = "A" AND PS6510.NUMERO_AUTORIZACAO =' . aspas($numero_Autorizacao); 
            $buf .= ') ';
            $buf .= ' SELECT CODIGO_PROCEDIMENTO, NOME_PROCEDIMENTO, QUANTIDADE_PROCEDIMENTOS RowNumber ';
            $buf .= ' FROM retornaAutorizacoes ';
            $buf .= ' WHERE RowNumber BETWEEN ' . $inicio . ' AND ' . $fim;  

        }elseif($_SESSION['type_db'] == 'firebird'){
            $skip = (($_GET['janelaAbrir'] - 1) * 5);            

            $buf = "SELECT FIRST 5 SKIP " . $skip. " PS6510.CODIGO_PROCEDIMENTO, PS5210.NOME_PROCEDIMENTO, PS6510.QUANTIDADE_PROCEDIMENTOS "
            ." From Ps6510 "
            ." Inner Join Ps5210 On (Ps6510.codigo_Procedimento = Ps5210.Codigo_Procedimento) "
            ." Where Ps6510.Situacao = 'A' AND Ps6510.Numero_Autorizacao = ". aspas($numero_Autorizacao);
        }

    }else{
        $buf = "SELECT PS6510.CODIGO_PROCEDIMENTO, PS5210.NOME_PROCEDIMENTO, PS6510.QUANTIDADE_PROCEDIMENTOS "
        ." From Ps6510 "
        ." Inner Join Ps5210 On (Ps6510.codigo_Procedimento = Ps5210.Codigo_Procedimento) "
        ." Where Ps6510.Situacao = 'A' AND Ps6510.Numero_Autorizacao = ". aspas($numero_Autorizacao);
    }    
    	
    $res=jn_query($buf);

	for ($i=0; $i<=4; $i++)
	{
          $tabela[$i]                 = "";
          $codigoProcedimento[$i]     = "";
          $nomeProcedimento[$i]       = "";
          $quantidadeSolicitada[$i]   = "";
          $quantidadeAutorizada[$i]   = "";
	}

	$i= 0;

    while ($row=jn_fetch_object($res))
    {

          $tabela[$i]                 = $TabelaProc; // usam sempre a mesma tabela
          $codigoProcedimento[$i]     = $row->CODIGO_PROCEDIMENTO;
          $nomeProcedimento[$i]       = $row->NOME_PROCEDIMENTO;
          $quantidadeSolicitada[$i]   = $row->QUANTIDADE_PROCEDIMENTOS;
          $quantidadeAutorizada[$i]   = $row->QUANTIDADE_PROCEDIMENTOS;

		  $i++;
    }   


	

//Imagem e posições//
		
$imagem = imagecreatefrompng("img/sadt.png");
$logo   = imagecreatefrompng('../../Site/assets/img/logo_operadora.png');
list($width, $height) = getimagesize('../../Site/assets/img/logo_operadora.png');
$logoRedimensionada = imagecreatetruecolor(500, 200);
$color = imagecolorallocate($logoRedimensionada, 255, 255, 255);
imagefill($logoRedimensionada, 0, 0, $color);
imagecopyresampled($logoRedimensionada, $logo, 0, 0, 0, 0, 500, 200, $width, $height);	

	
$cor = imagecolorallocate($imagem, 0, 0, 0 );
imagecopymerge($imagem, $logoRedimensionada,34, 16, 0, 0, 500, 200, 100);
		
$normal  = "../../Site/assets/fonts/unispace rg.ttf";
		
		
		
$tamanhoLetra = 25;


//calculo posição
//100,100
// fica
//119,89
//+19,-11

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 51, 323, $cor,$normal,$guia['RegistroANS'],34.5);                       //1
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 456, 323, $cor,$normal,$guia['NumeroGuiaPrincipal'],34.5);              //3
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 60, 439, $cor,$normal, $guia['DataAutorizacao'],34.5,30);              //4
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 627, 432, $cor,$normal,$guia['NumeroAutorizacao'],34.5);                //5
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 1778, 433, $cor,$normal, $guia['DataValidade'],34.5,30);               //6
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2322, 433, $cor,$normal,$guia['NumeroGuia'],34.5);                      //7
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 58, 596, $cor,$normal,$guia['NumeroCateirinha'],29);                    //8

if(retornaValorConfiguracao('OCULTAR_VAL_CARTEIRINHA_GUIAS') != 'SIM' || retornaValorConfiguracao('OCULTAR_VAL_CARTEIRINHA_GUIAS') == ''){
    dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 1088, 600, $cor,$normal, $guia['ValidadeCarteirinha'],34.5,30);    //9
}

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1619, 597, $cor,$normal,$guia['NomeBeneficiario'],5);                   //10
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3143, 599, $cor,$normal,$guia['NumeroCNS'],29);                         //11 
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 4036, 602, $cor,$normal,$guia['FlagAtendimentoRn'],29);                 //12 
if($_SESSION['codigoSmart'] != '3419'){
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 67, 764, $cor,$normal,$codPrestadorContrat,35);                     //13
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 869, 760, $cor,$normal,$guia['NomePrestadorContrat'],5);            //14
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 39, 892, $cor,$normal,$guia['NomePrestadorSolic'],5);               //15
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1322, 892, $cor,$normal,$guia['PrestadorSolicCodConselho'],30);         //16
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1527, 892, $cor,$normal,$guia['PrestadorSolicNumConselho'],34);         //17
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2388, 892, $cor,$normal,$guia['PrestadorSolicUFConselho'],30);          //18
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2543, 892, $cor,$normal,$guia['PrestadorSolicCBOS'],30);                //19
}
if($_SESSION['codigoSmart'] == '3419'){
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 67, 764, $cor,$normal,$codPrestadorSolic,35);                       //13
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 869, 760, $cor,$normal,$guia['NomePrestadorSolic'],5);              //14
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 39, 892, $cor,$normal,$guia['NOME_PRESTADOR_SOLICITANTE'],5);       //15
	$queryCodCon = 'select PS5261.CODIGO_TERMO_CONS_PROFISS from PS5261 where PS5261.codigo_conselho_profiss ='.aspas($guia['CODIGO_CONSELHO_PROFISS']);
	if ($resCodCon = jn_query($queryCodCon)) {
         if ($rowCodCon = jn_fetch_object($resCodCon)) {
			if($rowCodCon->CODIGO_TERMO_CONS_PROFISS!=''){
				$guia['CODIGO_CONSELHO_PROFISS'] = $rowCodCon->CODIGO_TERMO_CONS_PROFISS;
			}
		 }
	}
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1322, 892, $cor,$normal,$guia['CODIGO_CONSELHO_PROFISS'],30);       //16
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1527, 892, $cor,$normal,$guia['NUMERO_CONSELHO_CLASSE'],34);        //17
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2388, 892, $cor,$normal,$guia['ESTADO_CONSELHO_CLASSE'],30);        //18
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2543, 892, $cor,$normal,$guia['PrestadorSolicCBOS'],30);            //19
}


textoImagemEspacamento($imagem, $tamanhoLetra, 0, 129, 1055, $cor,$normal,$guia['TipoEletivaUrgencia'],30);               //21
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 300, 1043, $cor,$normal, $guia['DataAutorizacao'],34.5,30);            //22

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 91, 1170, $cor,$normal,trim($tabela[0]),30);                            //24 item 1
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 230, 1170, $cor,$normal,trim($codigoProcedimento[0]),35);               //25 item 1
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 788, 1170, $cor,$normal,trim($nomeProcedimento[0]),5);                  //26 item 1
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3629, 1170, $cor,$normal,trim($quantidadeSolicitada[0]),30);            //27 item 1
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3867, 1170, $cor,$normal,trim($quantidadeAutorizada[0]),30);            //28 item 1

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 91, 1225, $cor,$normal,trim($tabela[1]),30);                            //24 item 2
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 230, 1225, $cor,$normal,trim($codigoProcedimento[1]),35);               //25 item 2
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 788, 1225, $cor,$normal,trim($nomeProcedimento[1]),5);                  //26 item 2
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3629, 1225, $cor,$normal,trim($quantidadeSolicitada[1]),30);            //27 item 2
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3867, 1225, $cor,$normal,trim($quantidadeAutorizada[1]),30);            //28 item 2
if($_SESSION['codigoSmart'] == '3419'){
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 50, 1576, $cor,$normal,$codPrestadorContrat,35);                       //29
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 923, 1576, $cor,$normal,$guia['NomePrestadorContrat'],5);              //30
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3795, 1570, $cor,$normal,$guia['CodigoCNESContrat'],35);    
	
}

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 91, 1280, $cor,$normal,trim($tabela[2]),30);                            //24 item 3
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 230, 1280, $cor,$normal,trim($codigoProcedimento[2]),35);               //25 item 3
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 788, 1280, $cor,$normal,trim($nomeProcedimento[2]),5);                  //26 item 3
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3629, 1280, $cor,$normal,trim($quantidadeSolicitada[2]),30);            //27 item 3
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3867, 1280, $cor,$normal,trim($quantidadeAutorizada[2]),30);            //28 item 3

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 91, 1335, $cor,$normal,trim($tabela[3]),30);                            //24 item 4
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 230, 1335, $cor,$normal,trim($codigoProcedimento[3]),35);               //25 item 4
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 788, 1335, $cor,$normal,trim($nomeProcedimento[3]),5);                  //26 item 4
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3629, 1335, $cor,$normal,trim($quantidadeSolicitada[3]),30);            //27 item 4
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3867, 1335, $cor,$normal,trim($quantidadeAutorizada[3]),30);            //28 item 4

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 91, 1390, $cor,$normal,trim($tabela[4]),30);                            //24 item 5
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 230, 1390, $cor,$normal,trim($codigoProcedimento[4]),35);               //25 item 5
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 788, 1390, $cor,$normal,trim($nomeProcedimento[4]),5);                  //26 item 5
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3629, 1390, $cor,$normal,trim($quantidadeSolicitada[4]),30);            //27 item 5
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3867, 1390, $cor,$normal,trim($quantidadeAutorizada[4]),30);            //28 item 5

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 51, 1575, $cor,$normal,$codPrestadorExec,35);                           //29
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 935, 1575, $cor,$normal,$guia['NomePrestadorExec'],5);                  //30
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3792, 1565, $cor,$normal,$guia['CodigoCNESExec'],35);                   //31			
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 161, 1740, $cor,$normal,$guia['TipoAtendimento'],35);                   //32
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 803, 1740, $cor,$normal,$guia['IndicacaoAcidente'],35);                 //33
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1410, 1740, $cor,$normal,$guia['TipoConsulta'],35);                     //34
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1860, 1740, $cor,$normal,$guia['TipoSaida'],35);                        //35
textoImagemEspacamento($imagem, $tamanhoLetra-4, 0, 100, 2790, $cor,$normal,$guia['DescricaoObservacao'],35);             //58

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 64, 2775, $cor,$normal, '',5);			  //58 linha 01
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 64, 2810, $cor,$normal, '',5);			  //58 linha 02	
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 64, 2845, $cor,$normal, '',5);			  //58 linha 03


			
ob_start();
imagejpeg ( $imagem); 
imagedestroy( $imagem ); 
$dadoImagem = ob_get_clean();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body width="1329px" height="717px"  >

<?php
echo '<img width="1319px" height="707px"  src="data:image/jpeg;base64,' . base64_encode( $dadoImagem ) . '" />';
?>
</body>
</html>