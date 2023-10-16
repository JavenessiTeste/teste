<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$numero_guia = $_GET['numeroGuia'];

$query = " select CfgEmpresa.Nome_Empresa, CfgEmpresa.Razao_Social, CfgEmpresa.Numero_Insc_Susep, "            
		." Ps5000.Nome_Prestador, Ps5000.Codigo_CNES, Ps5000.Tipo_Pessoa, PS5002.Numero_Cpf, PS5002.Numero_CNPJ "            
		."From PS5000 "            
		."Inner Join CfgEmpresa On (Nome_Empresa is not null) "
		."Inner Join Ps5002 On (PS5000.codigo_Prestador = Ps5002.Codigo_Prestador) "
		." Where PS5000.Codigo_Prestador = ". aspas($_SESSION['codigoIdentificacao']);
$res=jn_query($query);
$row=jn_fetch_assoc($res);

$registroAns = $row['NUMERO_INSC_SUSEP'];    
$codigoPrestador                = $row['NUMERO_CPF'] . $row['NUMERO_CNPJ'] ;
$CodigoCNES                     = $row['CODIGO_CNES'];
$nomePrestador                  = $row['NOME_PRESTADOR'];

?>
    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Emiss&atilde;o da guia de SP/SADT</title>



<style type="text/css">
body {
	margin:0;
	margin-top: 0mm;
	margin-left: 0mm;
	margin-right: 0mm;
	margin-bottom: 0mm;
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
@page 
    {
        size: auto;   /* auto is the initial value */
        margin: 0mm;  /* this affects the margin in the printer settings */
    }
</style>
<link href="css/guiaSpsadt.css" rel="stylesheet" type="text/css" />

<style media="print">
    form {
        display: none;
		margin:0;
    }
</style>

</head>
<body  height="100%"> 
 
  
  <table width="1069" height="707" border="1" align="center">
  <tr>
    <td width="1040" height="655" align="left" valign="top">
		<div align="center">
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
                    <div class="valor1"><?php echo $registroAns; ?></div>
                </td>
                <td width="29%">
                    <div class="label1">3 - Numero da Guia Principal</div>
                    <div class="valor1">&nbsp;</div>
                </td>
			</tr>
			<tr align="left" valign="top">
                <td width="14%">
                    <div class="label1">4 - Data da Autoriza&ccedil;&atilde;o</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="13%">
                    <div class="label1">5 - Senha</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="11%">
                    <div class="label1">6 - Data validade Senha</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="20%">
                    <div class="label1">7 - Numero Guia Atribuido pela Operadora</div>
                    <div class="valor1"><?php echo $numero_guia; ?></div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Benefici&aacute;rio</div>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="13%">
                    <div class="label1">8 - N&uacute;mero da Carteira</div>
                    <div class="valor1">&nbsp;</div>
                </td>                
                <td width="12%" class="label1">
                    <div class="label1">9 - Validade da Carteira</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td>
                    <div class="label1">10 - Nome</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="24%" class="label1">
                    <div class="label1">11 - Numero do Cartao Nacional de Sa&uacute;de</div>
                    <div class="valor1">&nbsp;</div>
                </td>
				<td>
                    <div class="label1">12 - Atendimento a RN</div>
                    <div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Solicitante</div>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="25%">
                    <div class="label1">13 - C&oacute;digo na Operadora</div>
                    <div class="valor1">&nbsp;<?php echo !empty($codigoPrestador) ? $codigoPrestador : $codigoPrestador ?></div>
                </td>
                <td>
                    <div class="label1">14 - Nome do Contratado</div>
                    <div class="valor1"><?php echo !empty($nomePrestador) ? $nomePrestador : $nomePrestador ;?></div>
                </td>
            </tr>
        </table>
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td  width="18%">
                    <div class="label1">15 - Nome do Profissional solicitante</div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="7%">
                    <div class="label1">16 - Conselho Profissional</div>
                    <div class="valor1"><?php echo !empty($conselhoProfissional) ? $conselhoProfissional : '&nbsp;' ?></div>
                </td>
                <td width="7%">
                    <div class="label1">17 - N&uacute;mero de conselho</div>
                    <div class="valor1"><?php echo !empty($numeroConselhoProfissional) ? $numeroConselhoProfissional : '&nbsp;' ?></div>
                </td>
                <td width="3%">
                    <div class="label1">18 - UF</div>
                    <div class="valor1"><?php echo !empty($ufConselhoProfissional) ? $ufConselhoProfissional : '&nbsp;' ?></div>
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
                <td width="20%">
                    <div class="label1">21 - Car&aacute;ter	do Atendimento</div>
                    <div class="valor1">&nbsp;</div>
                </td>
				<td width="20%">
                    <div class="label1">22 - Data da Solicita&ccedil;&atilde;o</div>
                    <div class="valor1">&nbsp;<?php echo($dataEmissao) ?></div>
                </td>
                <td width="60%">
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
                </td>
                <td width="15%" class="bgEscuro">
                    <div class="label1">25 - C&oacute;digo do Procedimento</div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($codigoProcedimento[2]) ?></div>
                </td>
                <td>
                    <div class="label1">26 - Descri&ccedil;&atilde;o</div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($nomeProcedimento[2]) ?></div>
                </td>
                <td width="7%">
                    <div class="label1">27 - Qtde. Solic.</div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeSolicitada[2]) ?></div>
                </td>
                <td width="9%">
                    <div class="label1">28 - Qtde. Aut.</div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[0]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[1]) ?></div>
                    <div class="valor1"><?php echo emptyToNbsp($quantidadeAutorizada[2]) ?></div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Contratado Executante</div>
            
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="17%">
                    <div class="label1">29 - C&oacute;digo na Operadora</div>                    
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="70%">
                    <div class="label1">30 - Nome do Contratado</div>                    
                    <div class="valor1">&nbsp;</div>
                </td >
                <td width="10%">
                    <div class="label1">31 - C&oacute;digo CNES</div>                    
                    <div class="valor1">&nbsp;</div>
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
                    <div class="valor1"><?php echo '';?></div>
                    <div class="valor1">&nbsp;</div>
                    </div>
                </td>
				<td width="10%">
                    <div class="label1">34 - Tipo de Consulta</div>
                    <div class="valor1"><?php echo '';?></div>
                    <div class="valor1">&nbsp;</div>
                </td>
                <td width="20%">
                    <div class="label1">35 - Motivo de Encerramento do Atendimento</div>
                    <div class="valor1"><?php echo '';?></div>
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
       
        <table id="GridProcsRel" width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="7%">
                    <div class="label1">48 - Seq. Ref</div>
                    <div class="valor2"><span class="grade">  |  |  |</span></div>                    
					<div class="valor2"><span class="grade">  |  |  |</span></div>                    
					<div class="valor2"><span class="grade">  |  |  |</span></div>                    
					<div class="valor2"><span class="grade">  |  |  |</span></div>                    
                </td>
                <td width="7%">
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
                <td width="20%">
                    <div class="label1">50 - Código na Operadora/CPF</div>
                    <div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
					<div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
                </td>
                <td width="15">
                    <div class="label1">51 - Nome do Profisional</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                    <div class="grade2">&nbsp;</div>
                </td>
                <td width="12%">
                    <div class="label1">&nbsp;&nbsp;52- Conselho   Profissional</div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                    <div class="valor2">&nbsp;&nbsp;<span class="grade">|  |  |</span></div>
                </td>
                <td width="20%">
                    <div class="label1">53 - Número no Conselho</div>
                    <div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
                    <div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
                    <div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
                    <div class="valor2"></span><span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span></div>
                </td>
                <td width="5%">
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
				<td width="10%">
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
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro">
                    <div class="label1">58 - Observa&ccedil;&atilde;o</div>                    
                    <div class="valor1">&nbsp;</div>
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
