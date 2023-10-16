<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);


$numero_Autorizacao = isset($_GET['numero']) ? $_GET['numero'] : $_SESSION['numeroAutorizacao'];

if($numero_Autorizacao==''){
	exit;
}


$buf = "select CfgEmpresa.Nome_Empresa, CfgEmpresa.Razao_Social, CfgEmpresa.Numero_Insc_Susep, "
		."Ps6500.Quantidade_dias_Internacao, Ps6500.Tipo_Doenca, Ps6500.Unidade_Tempo_Doenca, Ps6500.Tipo_Guia, Ps6500.Data_Autorizacao, Ps6500.Codigo_Associado, Ps6500.Codigo_Prestador, Ps6500.Codigo_Prestador_Executante, Ps6500.Codigo_Cid, Ps6500.Carater_Solicitacao, Ps6500.Tipo_Internacao,"
		."Ps6500.Numero_Autorizacao, Ps6500.Indicador_Acidente, Ps1000.Nome_Associado, Ps5000.Nome_Prestador, Ps5000.Codigo_CNES,Ps1000.Codigo_Cns, Ps1000.Data_Validade_Carteirinha, Ps5000.Tipo_Pessoa, "
		."Ps6500.Tipo_Doenca, Ps6500.Tempo_Doenca, Ps6500.Unidade_Tempo_Doenca, pS6500.Data_Validade, "
		."Ps6500.DATA_PROCEDIMENTO, Ps6500.CODIGO_TIPO_ACOMODACAO, "
		."Ps1030.Nome_Plano_Familiares, ps6500.codigo_regime_intern, Ps6500.Numero_Guia, PS6500.CODIGO_ESPECIALIDADE, "
		."Ps5002.CODIGO_CONSELHO_PROFISS, Ps6500.Numero_guia_operadora, ps6500.flag_atendimento_rn, ps6500.flag_previsao_opme, ps6500.flag_previsao_quimioterapia, Ps5002.UF_CONSELHO_PROFISS, Ps5002.NUMERO_CRM, Ps5002.Numero_Cpf, Ps5002.Numero_Cnpj, "

		// DADOS DO PRESTADOR EXECUTANTE
		."Ps5002_1.CODIGO_CONSELHO_PROFISS AS CODIGO_CONSELHO_PROFISS_1, "
		."Ps5002_1.UF_CONSELHO_PROFISS AS UF_CONSELHO_PROFISS_1, "
		."Ps5002_1.NUMERO_CRM AS NUMERO_CRM_1, "
		."Ps5002_1.Numero_Cpf AS Numero_Cpf_1, "
		."Ps5002_1.Numero_Cnpj AS Numero_Cnpj_1, "


		."Ps5001.ENDERECO, Ps5001.CIDADE, Ps5001.estado, Ps5001.CEP, Ps5001.TELEFONE_01, "
		."(SELECT FIRST 1 Ps5001_1.ENDERECO "
		."FROM Ps5001 Ps5001_1 "
		."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS ENDERECO, "
		."(SELECT FIRST 1 Ps5001_1.CIDADE "
		."FROM Ps5001 Ps5001_1 "
		."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS CIDADE, "
		."(SELECT FIRST 1 Ps5001_1.ESTADO "
		."FROM Ps5001 Ps5001_1 "
		."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS ESTADO, "
		."(SELECT FIRST 1 Ps5001_1.CEP "
		."FROM Ps5001 Ps5001_1 "
		."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS CEP, "
		."(SELECT FIRST 1 Ps5001_1.TELEFONE_01 "
		."FROM Ps5001 Ps5001_1 "
		."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS TELEFONE_01, "
		// Nome do prestador executante
		."(SELECT Ps5000.Nome_Prestador "
		."FROM Ps5000 "
		."WHERE Ps5000.codigo_prestador = Ps6500.codigo_prestador_executante) AS Nome_Prestador_Executante "

		."From Ps6500 "
		."Inner Join Ps1000 On (Ps6500.codigo_Associado = Ps1000.Codigo_Associado) "
		."Inner Join Ps5000 On (Ps6500.codigo_Prestador = Ps5000.Codigo_Prestador) "
		."Left Outer Join Ps5002 On (Ps5000.Codigo_Prestador = Ps5002.Codigo_Prestador) "
		."Left Outer Join Ps5002 Ps5002_1 On (Ps6500.Codigo_Prestador_Executante = Ps5002_1.Codigo_Prestador) "
		."Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) "
		."Inner Join CfgEmpresa On (Nome_Empresa is not null) "
		."Left join ps5001 on(Ps5001.Codigo_Prestador = Ps6500.Codigo_Prestador and ps5001.flag_ha_atendimento = 'S') "
		." Where Ps6500.Numero_Autorizacao = ". aspas($numero_Autorizacao);


$res=jn_query($buf);

$row=jn_fetch_object($res);

$numeroGuia                   = $row->NUMERO_GUIA;
$registroAns                  = $row->NUMERO_INSC_SUSEP;
$dataEmissao                  = date("d/m/Y",strtotime($row->DATA_AUTORIZACAO));
$numeroCarteira               = $row->CODIGO_ASSOCIADO;
$planoBeneficiario            = $row->NOME_PLANO_FAMILIARES;
$nomeBeneficiario             = $row->NOME_ASSOCIADO;
// Este campo pode ser o codigo do prestador na operadora, ou seu cnpj ou seu CPF
$codigoPrestador              = $row->NUMERO_CNPJ.$row->NUMERO_CPF;
$codigoPrestadorExecutante    = $row->NUMERO_CNPJ_1.$row->NUMERO_CPF_1;

$CodigoPrestadorExecutanteAlianca = $row->CODIGO_PRESTADOR_EXECUTANTE;//codigo do prestador executante no sistema

$nomePrestador                = $row->NOME_PRESTADOR;
$nomePrestadorExecutante      = $row->NOME_PRESTADOR_EXECUTANTE;
$conselhoProfissional         = $row->CODIGO_CONSELHO_PROFISS;
$conselhoProfissionalExec     = $row->CODIGO_CONSELHO_PROFISS_1;
$atendimentoRn				  = $row->FLAG_ATENDIMENTO_RN;
$previsaoOpme     			  = $row->FLAG_PREVISAO_OPME;
$previsaoQuimioterapia	      = $row->FLAG_PREVISAO_QUIMIOTERAPIA;
$guiaOperadora			      = $row->NUMERO_GUIA_OPERADORA;
$numeroConselhoProfissional   = $row->NUMERO_CRM;
$numeroConselhoProfissionalExec   = $row->NUMERO_CRM_1;
$ufConselhoProfissional       = $row->UF_CONSELHO_PROFISS;
$ufConselhoProfissionalExec       = $row->UF_CONSELHO_PROFISS_1;

$DataValidadeCarteirinha      = date("d/m/Y",strtotime($row->DATA_VALIDADE_CARTEIRINHA));;
$CodigoCid                    = $row->CODIGO_CID;
$CodigoCNES                   = $row->CODIGO_CNES;
$CodigoCNS                   = $row->CODIGO_CNS;
$autorizacao                  = $row->NUMERO_AUTORIZACAO;
$Endereco                     = $row->ENDERECO;	
$codigoEspecialidade          = $row->CODIGO_ESPECIALIDADE;
$Cidade                       = $row->CIDADE;
$Estado                       = $row->ESTADO;
$Cep                          = $row->CEP;
$Telefone_01                  = $row->TELEFONE_01;
$codigoRegimeIntern 		  = $row->CODIGO_REGIME_INTERN;
$TipoDoenca                   = $row->TIPO_DOENCA;
$TempoDoenca                  = $row->TEMPO_DOENCA;
$UnidadeTempoDoenca           = $row->UNIDADE_TEMPO_DOENCA;

$DataValidade                 = date("d/m/Y",strtotime($row->DATA_VALIDADE));
$DataProcedimento             = date("d/m/Y",strtotime($row->DATA_PROCEDIMENTO));
$CaraterSolicitacao           = $row->CARATER_SOLICITACAO;
$TipoInternacao               = $row->TIPO_INTERNACAO;
$TipoInternacao 			  = explode('-', $TipoInternacao);
$QuantidadeDiasInternacao     = $row-> QUANTIDADE_DIAS_INTERNACAO;
$TipoDoenca                   = $row-> TIPO_DOENCA;
$UnidadeTempoDoenca           = $row-> UNIDADE_TEMPO_DOENCA;
$IndicadorAcidente            = $row-> INDICADOR_ACIDENTE;
$codigoTipoAcomodacao         = $row->CODIGO_TIPO_ACOMODACAO;

//$CodigoProcedimento           = $row-> CODIGO_PROCEDIMENTO;
//$NomeProcedimento             = $row-> NOME_PROCEDIMENTO;

$bufProcedimentos = "Select Ps6510.Numero_Autorizacao, Ps6510.Codigo_Procedimento, Ps5210.Nome_Procedimento from PS6510 "
					."Inner Join Ps5210 on(Ps6510.Codigo_Procedimento = Ps5210.Codigo_Procedimento) "
					."Where Ps6510.Numero_Autorizacao = ". aspas($numero_Autorizacao);

$res=jn_query($bufProcedimentos);

   /*
 *  pega o codigo tiss da tabela de procedimentos que esta no contrato do
 * prestador contratado
 */

$TabelaProc = ''; // por padrao vem vazio

if ($CodigoPrestadorExecutanteAlianca == ''){
	$prestadorSql = $codigoPrestador;
}else{
	$prestadorSql = $CodigoPrestadorExecutanteAlianca;
}


$query  = 'SELECT ';
$query .= '   PS5211.referencia_tabela ';
$query .= 'FROM ';
$query .= '    ps5211 ';
$query .= 'WHERE ';
$query .= '    PS5211.referencia_tabela = ( ';
$query .= '        select ';
$query .= '            PS5002.referencia_tabela_internacoes ';
$query .= '        FROM ';
$query .= '            ps5002 ';
$query .= '        WHERE ';
$query .= '            PS5002.codigo_prestador = ' . aspas($prestadorSql);
$query .= '    ) ';

 if ($res = jn_query($query)) {
	 if ($row = jn_fetch_row($res)) {
		$TabelaProc = $row[0];
	 }
 }

$buf = "select Ps6510.Codigo_Procedimento, Ps5210.Nome_Procedimento, Ps6510.Quantidade_Procedimentos "
   ." From Ps6510 "
   ." Inner Join Ps5210 On (Ps6510.codigo_Procedimento = Ps5210.Codigo_Procedimento) "
   ." Where Ps6510.Numero_Autorizacao = ". aspas($numero_Autorizacao);
//echo $buf;exit();
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

<title>Emiss&atilde;o da guia de Solicitaç&atilde;o de Internaç&atilde;o</title>

<link type="text/css" href="js/jquery/ui/css/themes/base/ui.all.css" rel="stylesheet" />
<link type="text/css" href="js/jquery/tooltip/jquery.tooltip.css" rel="stylesheet" />


<script type="text/javascript">
    function imprimir(){
        alert("Configure o cabeçalho e o rodapé para não serem impressos\n\nConfigure as margens da página: esquerda(5 milímetros), direita(5 milímetros), superior(5 milímetros) e inferior(5 milímetros)");
        window.print(); return false;

    }
</script>
<style type="text/css">
body {
	margin-top: 1mm;
	margin-left: 1mm;
	margin-right: 1mm;
	margin-bottom: 1mm;
	height: 100%;
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
<link href="css/guiaSolitacaoInternacao.css" rel="stylesheet" type="text/css" />

<style media="print">
    form {
        display: none;
		size: landscape;
		height: 100%;
    }
</style>

</head>
<body height="100%"> 

  
  <table width="1070" height="1000" border="1" align="center">
  <tr>

    <td width="1309" height="30%" align="left" valign="top">
		<div class="valor1">&nbsp;</div>    
		<div align="center">
			<table width="100%" border="0">
			  <tr>
				<td width="21%" height="20%" style="text-align: left"><img src='../../Site/assets/img/logo_operadora.png' width="100" height="84" /></td>
				<td width="61%" height="20%"><div align="center" class="style36">GUIA DE SOLICITA&Ccedil;&Atilde;O <br />DE INTERNA&Ccedil;&Atilde;O</div></td>
				<td width="18%" class="style33" height="20%">2-Nº Guia no Prestador <?php echo $numeroGuia ?></td>

			  </tr>
			</table>
        </div>
		<!--<div class="valor1">&nbsp;</div>  -->
        <table width="90%" height="8%" border="1" cellpadding="0" cellspacing="0">
            <tr align="left" valign="top">
                <td width="13%">
                    <div class="label1">1 - Registro ANS</div>
                    <div class="valor1">&nbsp;<?php echo $registroAns ?></div>
                </td>
				<td width="11%">
                    <div class="label1">3 - Número Guia Atribuido pela Operadora</div>
                    <div class="valor1"><?php echo $guiaOperadora ?></div>
                </td>
			</tr>
			<tr align="left" valign="top">
                <td width="15%" class="label1">
                    <div class="label1">4 - Data da Autoriza&ccedil;&atilde;o</div>
                    <div class="valor1"><?php echo (($dataEmissao <> '') ? '<span="valor2">' . $dataEmissao : '<span class="grade">|  |  |/|  |  |/|  |  |');?></span></div>
                </td>
                <td width="14%" class="label1">
                    <div class="label1">5 - Senha</div>
                    <div class="valor1">&nbsp;<?php echo $autorizacao ;?></div>
                </td>
                <td width="13%" class="label1">
                    <div class="label1">6 - Data Validade da Senha</div>
                    <div class="valor1"><?php echo (($DataValidade <> '') ? $DataValidade : '|  |  |/|  |  |/|  |  |') ;?></span></div>
                </td>

            </tr>
        </table>
		<div class="valor1">&nbsp;</div>    
        <div width="100%" height="2%" class="labelGrupo">Dados do Benefici&aacute;rio</div>

        <table width="60%" border="1" height="5%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="label1">7 - N&uacute;mero da Carteira</div>
                    <div class="valor1"> <?php echo (($numeroCarteira <> '') ? '<span="valor2">' . $numeroCarteira : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |');?> </span></div>
					<div class="valor1">&nbsp;</div>
                </td>                
                <td width="20%" class="label1">
                    <div class="label1">8 - Validade da Carteira</div>
                    <div class="valor1"><?php echo (($DataValidadeCarteirinha <> '') ? '<span="valor2">' . $DataValidadeCarteirinha : '<span class="grade">|  |  |/|  |  |/|  |  |');?></span></div>
					<div class="valor1">&nbsp;</div>
                </td>
				<td width="25%">
                    <div class="label1">9 - Atendimento a RN</div>
                    <div class="valor1">&nbsp;<?php echo $atendimentoRn ;?></div>
					<div class="valor1">&nbsp;</div>
                </td>
            </tr>

        </table>
        <table width="100%" height="5%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="label1">10 - Nome</div>
                    <div class="valor1">&nbsp;<?php echo $nomeBeneficiario ;?></div>
					<div class="valor1">&nbsp;</div>    	
                </td>
                <td width="30%" class="label1">
                    <div class="label1">11 - Numero do Cartao Nacional de Sa&uacute;de</div>
                    <div class="valor1">&nbsp;<?php echo $CodigoCNS ;?></div>
					<div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>

        <div width="100%" height="2%" class="labelGrupo">Dados do Contratado Solicitante</div>

        <table width="100%" height="5%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="25%">
                    <div class="label1">12 - C&oacute;digo na Operadora</div>
                    <div class="valor1">&nbsp;<?php echo $codigoPrestador ;?></div>
					<div class="valor1">&nbsp;</div>
                </td>
                <td width="75%">
                    <div class="label1">13 - Nome do Contratado</div>
                    <div class="valor1">&nbsp;<?php echo $nomePrestador;?></div>
					<div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>
        <table width="100%" height="5%" border="1" cellpadding="0" cellspacing="0">
            <tr>

                <td class="label1">
                    <div class="label1">14 - Nome do Profissional solicitante</div>
                    <div class="valor1">&nbsp;<?php echo $nomePrestador;?></div>
					<div class="valor1">&nbsp;</div>
                </td>
                <td width="15%">
                    <div class="label1">15 - Conselho Profissional</div>
                    <div class="valor1">&nbsp;<?php echo $conselhoProfissional ?></div>
					<div class="valor1">&nbsp;</div>

                </td>
                <td width="15%">
                    <div class="label1">16 - N&uacute;mero de conselho</div>
                    <div class="valor1">&nbsp;<?php echo $numeroConselhoProfissional ?></div>
					<div class="valor1">&nbsp;</div>
                </td>
                <td width="10%">
                    <div class="label1">17 - UF</div>

                    <div class="valor1">&nbsp;<?php echo $ufConselhoProfissional ?></div>
					<div class="valor1">&nbsp;</div>
                </td>
                <td width="10%" class="label1">
                    <div class="label1">18 - C&oacute;digo CBO'S</div>
                    <div class="valor1">&nbsp;<?php echo $codigoEspecialidade ?></div>
					<div class="valor1">&nbsp;</div>
                </td>
            </tr>

        </table>

        <div width="100%" height="2%" class="labelGrupo">Dados do Hospital/ Local Solicitado/ Dados da Interna&ccedil;&atilde;o</div>

        <table width="100%" height="5%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label1" width="25%">
                    <div class="label1">19 - C&oacute;digo na Operadora/CNPJ</div>
                    <div class="valor2"><?php echo (($codigoPrestadorExecutante <> '') ? '<span="valor2">' . $codigoPrestadorExecutante : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |');?></span></div>
					<div class="valor1">&nbsp;</div>
                </td>
                <td class="label1" width="55%">
                    <div class="label1">20 - Nome do Hospital / Local Solicitado</div>
                    <div class="valor1">&nbsp;<?php echo $nomePrestadorExecutante ;?></div>
					<div class="valor1">&nbsp;</div>
                </td>
				<td class="label1" width="20%">
                    <div class="label1">21 - Data sugerida para internação</div>
                    <div class="valor1">&nbsp;<?php ?></div>
					<div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>

        <table width="100%" height="6%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="13%">
                    <div class="label1">22 - Car&aacute;ter na Interna&ccedil;&atilde;o</div>
                    <div class="valor2">
                        <?php echo (($CaraterSolicitacao <> '') ? '<span="valor2">' . $CaraterSolicitacao : '<span class="grade">|  |');?></span>
						<div class="valor1">&nbsp;</div>
                    </div>
                </td>
                <td width="12%">
                    <div class="label1">23 - Tipo de Interna&ccedil;&atilde;o</div>
                    <div class="valor2">
                        <?php echo (($TipoInternacao[0] <> '') ? '<span="valor2">' . $TipoInternacao[0] : '<span class="grade">|  |');?></span>
						<div class="valor1">&nbsp;</div>
                    </div>
                </td>
                <td width="15%">
                    <div class="label1">24 - Regime de Interna&ccedil;&atilde;o</div>
					<div class="valor2">&nbsp; <?php $codigoRegimeIntern ?></div>
					<div class="valor1">&nbsp;</div>
                </td>
                <td width="15%">
                    <div class="label1">25 - Qtde. Diarias Solicitadas</div>
                    <div class="valor2">
                        <?php echo (($QuantidadeDiasInternacao <> '') ? '<span="valor2">' . $QuantidadeDiasInternacao : '<span class="grade">|  |  |');?></span>
						<div class="valor1">&nbsp;</div>
                    </div>
                </td>
				<td width="15%">
                    <div class="label1">26 - Previsão de uso OPME</div>
					<div class="valor2">
						&nbsp;<?php echo $previsaoOpme;?></span>
						<div class="valor1">&nbsp;</div>
					</div>
                </td>
                <td width="15%">
                    <div class="label1">27 - Previsão de uso quimioterápico</div>
                    <div class="valor2">
                        &nbsp;<?php echo $previsaoQuimioterapia;?></span>
						<div class="valor1">&nbsp;</div>
                    </div>
                </td>
            </tr>
        </table>

        <table width="100%" height="8%" border="1" cellpadding="0" cellspacing="0">
            <tr id="GridProcedimentos">
                <td width="7%">
                    <div class="label1">28 - Indica&ccedil;&atilde;o Cl&iacute;nica</div>

                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>                                       
                    <div class="valor1">&nbsp;</div>                                       
                </td>
            </tr>
        </table>
            
        <table width="100%" height="5%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="15%" class="bgEscuro">
                    <div class="label1">29 - Cid 10 Principal</div>
                    <div class="valor1">&nbsp;<?php echo $CodigoCid ;?></div>
                </td>
                <td width="15%" class="bgEscuro">
                    <div class="label1">30 - Cid 10 (2)</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="15%" class="bgEscuro">
                    <div class="label1">31 - Cid 10 (3)</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="15%" class="bgEscuro">
                    <div class="label1">32 - Cid 10 (4)</div>
                    <div class="valor1">&nbsp;</div>
                </td>
				<td width="20%" class="label1">
                    <div class="label1">33 - Indica&ccedil;&atilde;o de Acidente</div>
                    <div class="valor2">
                        <?php echo (($IndicadorAcidente <> '') ? '<span="valor2">' . $IndicadorAcidente : '<span class="grade">|  |');?></span>
                    </div>
					<div class="valor1">&nbsp;</div>
					<div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>

        <div width="100%" height="5%" class="labelGrupo">Procedimentos ou Itens Assistenciais Solicitados</div>
        <table width="100%" height="15%" border="1" cellpadding="0" cellspacing="0">
            <tr>
				<td width="10%" class="bgEscuro">
					<div class="valor1">&nbsp;</div>
					<div class="valor1">&nbsp;</div>
                    <div class="label1">34 - tabela</div>                
                    <div class="valor1"><span class="subLabel">1- <?php echo emptyToNbsp($tabela[0]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">2- <?php echo emptyToNbsp($tabela[1]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">3- <?php echo emptyToNbsp($tabela[2]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">4- <?php echo emptyToNbsp($tabela[3]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">5- <?php echo emptyToNbsp($tabela[4]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">6- <?php echo emptyToNbsp($tabela[5]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">7- <?php echo emptyToNbsp($tabela[6]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">8- <?php echo emptyToNbsp($tabela[7]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">9- <?php echo emptyToNbsp($tabela[8]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">10- <?php echo emptyToNbsp($tabela[9]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">11- <?php echo emptyToNbsp($tabela[10]) ;?></span></div>
                    <div class="valor1"><span class="subLabel">12- <?php echo emptyToNbsp($tabela[11]) ;?></span></div>                    
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="25%" class="bgEscuro">
					<div class="valor1">&nbsp;</div>
					<div class="valor1">&nbsp;</div>
                    <div class="label1">35 - C&oacute;digo do Procedimento ou Item Assistencial</div>

                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[0]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[1]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[2]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[3]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[4]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[5]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[6]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[7]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[8]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[9]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[10]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[11]) ;?></div>
                </td>
                <td>
					<div class="valor1">&nbsp;</div>
					<div class="valor1">&nbsp;</div>
                    <div class="label1">36 - Descri&ccedil;&atilde;o</div>

                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[0]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[1]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[2]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[3]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[4]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[5]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[6]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[7]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[8]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[9]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[10]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[11]) ;?></div>
                </td>
                <td width="10%">
					<div class="valor1">&nbsp;</div>
					<div class="valor1">&nbsp;</div>
                    <div class="label1">37 - Qdte. Solict</div>

                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[0]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[1]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[2]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[3]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[4]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[5]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[6]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[7]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[8]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[9]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[10]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[11]) ;?></div>
                </td>
                <td width="10%">
					<div class="valor1">&nbsp;</div>
					<div class="valor1">&nbsp;</div>
                    <div class="label1">38 - Qdte. Aut</div>

                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[0]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[1]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[2]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[3]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[4]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[5]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[6]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[7]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[8]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[9]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[10]) ;?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[11]) ;?></div>
                </td>
            </tr>			
        </table>
        
       <div width="100%" height="1%" class="labelGrupo">Dados da Autoriza&ccedil;&atilde;o</div>

        <table width="70%" height="5%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="label1">39 - Data Prov&aacute;vel da Admiss&atilde;o Hospitalar</div>
                    <div class="valor1"><?php echo (($DataProcedimento <> '') ? $DataProcedimento : '|  |  |/|  |  |/|  |  |') ;?></span></div>
					<!--div class="valor2"><span class="grade">|  |  |/|  |  |/|  |  |</span></div>-->
                </td>
                <td>
                    <div class="label1">40 - Qtde. Di&aacute;rias Autorizadas</div>

                    <div class="valor2"><span class="grade">|  |  |</span></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">41 - Tipo Acomoda&ccedil;&atilde;o Autorizada</div>
					<div class="valor2">
                        &nbsp;<?php echo $codigoTipoAcomodacao;?></span>
                    </div>
                </td>
            </tr>
        </table>
        <table width="100%" height="5%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="25%">
                    <div class="label1">42 - C&oacute;digo na Operadora/ CNPJ</div>
                    <div class="valor2"><?php echo (($codigoPrestadorExecutante <> '') ? '<span="valor2">' . $codigoPrestadorExecutante : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |');?></span></div>
                </td>
                <td>
                    <div class="label1">43 - Nome do Hospital / Local Autorizado</div>
                    <div class="valor1">&nbsp;<?php echo $nomePrestadorExecutante ;?></div>
                </td>
                <td class="bgEscuro" width="15%">
                    <div class="label1">44 - C&oacute;digo CNES</div>
                    <div class="valor1">&nbsp;<?php echo $CodigoCNES ;?></div>
                </td>
            </tr>

        </table>

        <table width="100%" height="12%" border="1" cellpadding="0" cellspacing="0">
            <tr id="GridProcedimentos">
                <td width="7%" class="bgEscuro">
                    <div class="label1">45 - Observa&ccedil;&atilde;o / Justificativa</div>

                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>

       <table width="100%" height="8%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro">
                    <div class="label1">46 - Data da Solicitação</div>
					<div class="valor1"><?php echo (($dataEmissao <> '') ? $dataEmissao : '|  |  |/|  |  |/|  |  |') ;?></span></div>					
					<div class="valor1">&nbsp;</div>
					<div class="valor1">&nbsp;</div>
                </td>
				<td class="bgEscuro">
                    <div class="label1">47 - Assinatura do Profissional Solicitante</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">48 - Assinatura do Benefici&aacute;rio ou Respons&aacute;vel</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">49 - Assinatura do Respons&aacute;vel pela Autoriza&ccedil;&atilde;o</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                    <div class="valor1">&nbsp;</div>
                </td>
            </tr>

        </table>
    </td>
  </tr>
</table>
</body>
</html>
