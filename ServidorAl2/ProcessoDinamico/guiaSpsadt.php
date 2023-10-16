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
	
//	pr($numero_Autorizacao,true);
	
    $Query  = 'SELECT * ';
    $Query .= 'FROM vw_guias_sadt ';
    $Query .= 'WHERE numero_autorizacao =' . aspas($numero_Autorizacao);
    //pr($Query,true);
	$res = jn_query($Query);

    $guia = null;
    if ($row = jn_fetch_object($res)) {
        //echo "<pre>";
        //pr($row); exit();
        $guia = array(
            'NumeroAutorizacao'             => $row->NUMERO_AUTORIZACAO,
            'NomeEmpresa'                   => $row->NOME_EMPRESA,
            'RazaoSocial'                   => $row->RAZAO_SOCIAL,
            'RegistroANS'                   => substr($row->NUMERO_INSC_SUSEP, 0, -1) . '-' . substr($row->NUMERO_INSC_SUSEP, -1),
            'NumeroGuia'                    => $row->NUMERO_GUIA,
            'NumeroGuiaPrincipal'           => '',
            'TipoGuia'                      => $row->TIPO_GUIA,
            'DataAutorizacao'               => SqlToData2($row->DATA_AUTORIZACAO),
            'HorarioAutorizacao'            => $row->HORARIO_AUTORIZACAO,
            'DataValidade'                  => $row->DATA_VALIDADE_SENHA,
            'NumeroCateirinha'              => $row->CODIGO_ASSOCIADO,
            'ValidadeCarteirinha'           => SqlToData2($row->DATA_VALIDADE_CARTEIRINHA),
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
			'IndicacaoAcidente'	    		=> $row->INDICADOR_ACIDENTE

        );
    }
    else {
        echo 'falha no processamento.';
        exit();
    }





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




    $buf = "select Ps6510.Codigo_Procedimento, Ps5210.Nome_Procedimento, Ps6510.Quantidade_Procedimentos "
           ." From Ps6510 "
           ." Inner Join Ps5210 On (Ps6510.codigo_Procedimento = Ps5210.Codigo_Procedimento) "
           ." Where Ps6510.Situacao = 'A' AND Ps6510.Numero_Autorizacao = ". aspas($numero_Autorizacao);
	//pr($buf,true);
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

?>
    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Emiss&atilde;o da guia de SP/SADT</title>


<style type="text/css">
body {
	margin-top: 1mm;
	margin-left: 1mm;
	margin-right: 1mm;
	margin-bottom: 1mm;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 6px;
}
.style19 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8px;
}
.style20 {
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8px;
}
.style22 {
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8px;
}
.style23 {
	color: #000000;
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8px;
}
.style25 {font-family: Arial, Helvetica, sans-serif; font-weight: bold;}
.style29 {
	font-size: 8px
}
.style33 {font-size: 12px}
.style34 {color: #FFFFFF}
.style36 {font-size: 12px; font-weight: bold; }
.style38 {font-size: 9}
.style40 {
	font-size: 10px;
	color: #FF0000;
	font-weight: bold;
}
.style43 {font-size: 10px; font-family: Arial, Helvetica, sans-serif;}

</style>
<link href="css/guiaSpsadt.css" rel="stylesheet" type="text/css" />

<style media="print">
    form {
        display: none;
    }
</style>

</head>
<body> 
<form id="form1" name="form1" action="">
<table width="1319" height="707" border="1" align="left">
  <tr>
    <td width="1309" height="703" align="left" valign="top"><div align="center">
      <table width="100%" border="0">
          <tr>
            <td width="21%" height="40" style="text-align: left"><img src='../../Site/assets/img/logo_operadora.png' width="90" height="50" /></td>
            <td width="61%"><div align="center" class="style36">GUIA DE SERVI&Ccedil;O PROFISSIONAL / SERVI&Ccedil;O AUXILIAR DE DIAGN&Oacute;STICO E TERAPIA - SP/SADT</div></td>
            <td width="18%" class="style33">Nº Guia no Prestador : <?php echo $guia['NumeroGuia'] ?></td>
          </tr>
        </table>
        </div>

        <table width="100%" height="27" border="1" cellpadding="0" cellspacing="0">
            <tr align="left" valign="top">
                <td width="13%">
                    <div class="label1">1 - Registro ANS</div>
                    <div class="valor1"><?php echo $guia['RegistroANS'] ?></div>
                </td>
                <td width="29%">
                    <div class="label1">3 - Numero da Guia Principal</div>
                    <div class="valor1"><?php echo $guia['NumeroGuiaPrincipal'] ?></div>
                </td>
			</tr>
			<tr align="left" valign="top">
                <td width="14%">
                    <div class="label1">4 - Data da Autoriza&ccedil;&atilde;o</div>
                    <div class="valor1"><?php echo $guia['DataAutorizacao'] ?></div>
                </td>
                <td width="13%">
                    <div class="label1">5 - Senha</div>
                    <div class="valor1"><?php echo $guia['NumeroAutorizacao'] ?></div>
                </td>
                <td width="11%">
                    <div class="label1">6 - Data validade Senha</div>
                    <div class="valor2"><?php echo (($guia['DataValidade']<> '') ? '<span="valor2">' . SqlToData2($guia['DataValidade']) : '<span class="grade">|  |  |/|  |  |/|  |  |'); ?></span></div>
                </td>
                <td width="20%">
                    <div class="label1">7 - Numero Guia Atribuido pela Operadora</div>
                    <div class="valor1"><?php echo $guia['NumeroGuiaOperadora'] ?></div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Benefici&aacute;rio</div>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="13%">
                    <div class="label1">8 - N&uacute;mero da Carteira</div>
                    <div class="valor1"><?php echo $guia['NumeroCateirinha'] ?></div>
                </td>                
                <td width="12%" class="label1">
                    <div class="label1">9 - Validade da Carteira</div>
                    <div class="valor1"><?php echo (($guia['ValidadeCarteirinha']) <> '') ? '<span class="valor1">' .  $guia['ValidadeCarteirinha'] : '<span class="grade">|  |  |/|  |  |/|  |  |'; ?></span></div>
                </td>
                <td>
                    <div class="label1">10 - Nome</div>
                    <div class="valor1"><?php echo $guia['NomeBeneficiario'] ?></div>
                </td>
                <td width="24%" class="label1">
                    <div class="label1">11 - Numero do Cartao Nacional de Sa&uacute;de</div>
                    <div class="valor1"><?php echo !empty($guia['NumeroCNS']) ? $guia['NumeroCNS'] : '&nbsp;'  ?></div>
                </td>
				<td>
                    <div class="label1">12 - Atendimento a RN</div>
                    <div class="valor1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $guia['FlagAtendimentoRn'] ?> </div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Solicitante</div>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="25%">
                    <div class="label1">13 - C&oacute;digo na Operadora</div>
                    <div class="valor1">&nbsp;<?php echo !empty($guia['PrestadorSolicCPF']) ? $guia['PrestadorSolicCPF'] : $guia['PrestadorSolicCNPJ'] ?></div>
                </td>
                <td>
                    <div class="label1">14 - Nome do Contratado</div>
                    <div class="valor1"><?php echo !empty($guia['NomePrestadorSolic']) ? $guia['NomePrestadorSolic'] : $guia['NomePrestadorSolicAux'] ;?></div>
                </td>
            </tr>
        </table>
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td  width="18%">
                    <div class="label1">15 - Nome do Profissional solicitante</div>
                    <div class="valor1"><?php echo !empty($guia['NomePrestadorSolic']) ? $guia['NomePrestadorSolic'] : $guia['NomePrestadorSolicAux'] ?></div>
                </td>
                <td width="7%">
                    <div class="label1">16 - Conselho Profissional</div>
                    <div class="valor1"><?php echo !empty($guia['PrestadorSolicCodConselho']) ? $guia['PrestadorSolicCodConselho'] : '&nbsp;' ?></div>
                </td>
                <td width="7%">
                    <div class="label1">17 - N&uacute;mero de conselho</div>
                    <div class="valor1"><?php echo !empty($guia['PrestadorSolicNumConselho']) ? $guia['PrestadorSolicNumConselho'] : '&nbsp;' ?></div>
                </td>
                <td width="3%">
                    <div class="label1">18 - UF</div>
                    <div class="valor1"><?php echo !empty($guia['PrestadorSolicUFConselho']) ? $guia['PrestadorSolicUFConselho'] : '&nbsp;' ?></div>
                </td>
                <td width="6%" class="label1">
                    <div class="label1">19 - C&oacute;digo CBO'S</div>
                    <div class="valor1"><?php echo !empty($guia['PrestadorSolicCBOS']) ? $guia['PrestadorSolicCBOS'] : '&nbsp;' ?></div>
                </td>
				<td width="10%" class="label1">
                    <div class="label1">20 - Assinatura do Profissional Solicitante</div>
                    <div class="valor1">&nbsp;&nbsp;&nbsp;&nbsp;</div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados da Solicita&ccedil;&atilde;o / Procedimento ou Itens Assistenciais Solicitados</div>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="8%">
                    <div class="label1">21 - Car&aacute;ter	do Atendimento</div>
                    <div class="valor2"><span class="valor1">&nbsp;<?PHP echo $guia['TipoEletivaUrgencia'];?></span><span class="subLabel">&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                </td>
				<td width="15%">
                    <div class="label1">22 - Data da Solicita&ccedil;&atilde;o</div>
                    <div class="valor1">&nbsp;<?php echo($guia['DataAutorizacao']) ?></div>
                </td>
                <td width="75%">
                    <div class="label1">23 - Indica&ccedil;&atilde;o cl&iacute;nica</div>
                    <div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr id="GridProcedimentos">
                <td width="7%" class="bgEscuro">
                    <div class="label1">24 - Tabela</div>
                    <div class="valor1"><?php echo emptyToNbsp($tabela[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($tabela[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($tabela[2]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($tabela[3]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($tabela[4]) ?></div>
                </td>
                <td width="15%" class="bgEscuro">
                    <div class="label1">25 - C&oacute;digo do Procedimento</div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[2]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[3]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[4]) ?></div>
                </td>
                <td>
                    <div class="label1">26 - Descri&ccedil;&atilde;o</div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[2]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[3]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[4]) ?></div>
                </td>
                <td width="7%">
                    <div class="label1">27 - Qtde. Solic.</div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[2]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[3]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[4]) ?></div>
                </td>
                <td width="9%">
                    <div class="label1">28 - Qtde. Aut.</div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[2]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[3]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[4]) ?></div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Contratado Executante</div>
            
        <table width="100%" border="1" cellpadding="0" cellspacing="0" height="5">
            <tr>
                <td width="17%">
                    <div class="label1">29 - C&oacute;digo na Operadora</div>
                    <div class="valor1"><?php echo !empty($guia['PrestadorContratCPF']) ? $guia['PrestadorContratCPF'] : $guia['PrestadorContratCNPJ'] ?></div>                   
                </td>
                <td width="70%">
                    <div class="label1">30 - Nome do Contratado</div>
                    <div class="valor1"><?php echo $guia['NomePrestadorContrat'] ?></div>                    
                </td >
                <td width="10%">
                    <div class="label1">31 - C&oacute;digo CNES</div>
                    <div class="valor1"><?php echo $guia['CodigoCNESContrat'];?></div>
                </td>
            </tr>
        </table>        
        <div width="100%" class="labelGrupo">Dados do Atendimento</div>

        <table width="55%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="10%">
                    <div class="label1">32 - Tipo Atendimento</div>
					<div class="valor1"><?php echo $guia['TipoAtendimento'];?></div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="20%">
                    <div class="label1">33 - Indica&ccedil;&atilde;o de Acidente</div>
                    <div class="valor1"><?php echo $guia['IndicacaoAcidente'];?></div>
                    <div class="valor1">&nbsp;</div>
                    </div>
                </td>
				<td width="10%">
                    <div class="label1">34 - Tipo de Consulta</div>
                    <div class="valor1"><?php echo $guia['TipoConsulta'];?></div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="20%">
                    <div class="label1">35 - Motivo de Encerramento do Atendimento</div>
                    <div class="valor1"><?php echo $guia['TipoSaida'];?></div>
                    <div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>        
		
        <div class="labelGrupo">Dados da Execução / Procedimentos e Exames Realizados</div>
       
        <table id="GridProcsRel" width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="10%">
                    <div class="label1">36 - Data</div>
                    <div class="valor2"><span class="subLabel">1-</span><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">2-</span><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">3-</span><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">4-</span><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">5-</span><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                </td>
                <td width="12%" class="bgEscuro">
                    <div class="label1">37 - Hora Inicial &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 38 - Hora Final</div>
                    <div class="valor2">
                        <span class="grade">|  |  |:|  |  |</span>
                        <span class="subLabel">a</span>
                        <span class="grade">|  |  |:|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |:|  |  |</span>
                        <span class="subLabel">a</span>
                        <span class="grade">|  |  |:|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |:|  |  |</span>
                        <span class="subLabel">a</span>
                        <span class="grade">|  |  |:|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |:|  |  |</span>
                        <span class="subLabel">a</span>
                        <span class="grade">|  |  |:|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |:|  |  |</span>
                        <span class="subLabel">a</span>
                        <span class="grade">|  |  |:|  |  |</span>
                    </div>
                </td>
                <td width="14%">
                    <div class="label1">39-Tabela&nbsp;&nbsp;40 - C&oacute;digo de Procedimento</div>
                    <div class="valor2"><span class="grade">|  |  |</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span></div>
                </td>
                <td width="8%" class="bgEscuro">
                    <div class="label1">41 - Descri&ccedil;&atilde;o</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                </td>
                <td width="4%" class="bgEscuro">
                    <div class="label1">&nbsp;&nbsp;42- Qtde</div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                </td>
                <td width="3%" class="bgEscuro    ">
                    <div class="label1">43- Via</div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                </td>
                <td width="3%" class="bgEscuro    ">
                    <div class="label1">44-Tec</div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                    <div class="valor2"><span class="grade">|  |</span></div>
                </td>
                <td width="7%" class="bgEscuro">
                    <div class="label1">45-Fator Red /Acresc</div>
                    <div class="valor2"><span class="grade">|  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |,|  |  |</span></div>
                </td>
                <td width="8%" class="bgEscuro">
                    <div class="label1">46 - Valor Unit&aacute;rio R$</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td width="9%" class="bgEscuro">
                    <div class="label1">47 - Valor Total R$</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                </td>
            </tr>
        </table>
		<div class="labelGrupo">Identificação do(s) Profissional(is) Executante(s)</div>
       
        <table id="GridProcsRel" width="100%" border="1" cellpadding="0" cellspacing="0" height="8">
            <tr>
                <td width="4%">
                    <div class="label1">48 - Seq. Ref</div>
                    <div class="valor2"><span class="grade">  |  |  |</span></div>                    
					<div class="valor2"><span class="grade">  |  |  |</span></div>                    
					<div class="valor2"><span class="grade">  |  |  |</span></div>                    
					<div class="valor2"><span class="grade">  |  |  |</span></div>                    
                </td>
                <td width="4%">
                    <div class="label1">49 - Grau Part.</div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>                    
                </td>
                <td width="8%">
                    <div class="label1">50 - Código na Operadora/CPF</div>
                    <div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
                </td>
                <td width="20%">
                    <div class="label1">51 - Nome do Profisional</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                </td>
                <td width="3%">
                    <div class="label1">&nbsp;&nbsp;52- Conselho   Profissional</div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                </td>
                <td width="10%">
                    <div class="label1">53 - Número no Conselho</div>
                    <div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
                </td>
                <td width="3%">
                    <div class="label1">54 - UF</div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |</span>
                    </div>                    
                </td>
				<td width="4%">
                    <div class="label1">55 - Código CBO</div>
                    <div class="valor2">
                        <span class="grade">|  |  |  |  |  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |  |  |  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |  |  |  |  |</span>
                    </div>
                    <div class="valor2">
                        <span class="grade">|  |  |  |  |  |  |</span>
                    </div>                    
                </td>
            </tr>
        </table>
        <table id="GridDataAssinatura" width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="label1">56 - Data de Realização de Procedimentos em Série		57 - Assinatura do Beneficiário ou Responsável</div>
                    <div class="valor2">
                        <span class="subLabel">1-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">3-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">5-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">7-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">9-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="valor2">
                        <span class="subLabel">2-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">4-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">6-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">8-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="subLabel">10-</span>
                        <span class="grade">|  |  |/|  |  |/|  |  | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </div>
                </td>
            </tr>
        </table>
		<?php 
			$queryRegEnd = 'SELECT registro_endereco_prestador FROM PS6500 
						 WHERE 							
							AND NUMERO_AUTORIZACAO = ' . aspas($guia['NumeroAutorizacao']);
			$resRegEnd = jn_query($queryRegEnd);
			$rowRegEnd = jn_fetch_object($resRegEnd);
			
			
			$queryEnd = 'SELECT FIRST 1 * FROM PS5001 
						 WHERE 
							PS5001.data_inutiliz_registro IS NULL 
							AND CODIGO_PRESTADOR = ' . $guia['CodigoPrestadorContrat'];
			if($rowRegEnd->REGISTRO_ENDERECO_PRESTADOR){
				$queryEnd .= ' AND PS5001.NUMERO_REGISTRO_ENDERECO = ' . aspas($rowRegEnd->REGISTRO_ENDERECO_PRESTADOR);
			}
			//pr($queryEnd);
			$resEnd = jn_query($queryEnd);
			$rowEnd = jn_fetch_object($resEnd);
								
			$dadosEnderecoPrestador = 'End. Prestador:  CEP - ' . $rowEnd->CEP . ' Logradouro: ' . $rowEnd->ENDERECO . '  -  ' . $rowEnd->BAIRRO;
			$dadosEnderecoPrestador .= ' ,' . $rowEnd->CIDADE . ' - ' . $rowEnd->ESTADO;
			$dadosEnderecoPrestador .= '&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';
			$dadosEnderecoPrestador .= '&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';
			$dadosEnderecoPrestador .= '&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';
			$dadosEnderecoPrestador .= '&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';
			$dadosEnderecoPrestador .= '&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';			
			$dadosEnderecoPrestador .= 'Telefone:  ' . $rowEnd->TELEFONE_01;
			$dadosEnderecoPrestador .= '&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;';
			$dadosEnderecoPrestador .= 'Numero Autorização: ' . $guia['NumeroAutorizacao'];
		?>
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro">
                    <div class="label1">58 - Observa&ccedil;&atilde;o &nbsp; &nbsp; <?php echo $dadosEnderecoPrestador;?></div>
                    <div class="grade2">&nbsp;</div>
                    
                </td>
            </tr>
        </table>
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro">
                    <div class="label1">59 - Total de Procedimentos(R$)</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">60 - Total Taxas e Alugueis(R$)</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">61 - Total de Materiais(R$)</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">62 - Total de OPME(R$)</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">63 - Total de Medicamentos(R$)</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">64 - Total de Gases Medicinais(R$)</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">65 - Total Geral(R$)</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |  |  |,|  |  |</span></div>
                </td>
            </tr>
        </table>
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="35%">
                    <div class="label1">66 - Assinatura do Respons&aacute;vel pela Autoriza&ccedil;&atilde;o</div>
                    <div class="valor2"><span class="label1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                </td>
                <td width="35%">
                    <div class="label1">67 - Assinatura do Benefici&aacute;rio ou Respons&aacute;vel</div>
                    <div class="valor2"><span class="label1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                </td>
                <td width="35%">
                    <div class="label1">68 - Assinatura do Contratado</div>
                    <div class="valor2"><span class="label1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                </td>
            </tr>
        </table>
    </td>
  </tr>
</table>
</body>
</html>
