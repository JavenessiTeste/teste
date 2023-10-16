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
$codigoPrestador                = $row['NUMERO_CPF'] . ' - ' . $row['NUMERO_CNPJ'] ;
$CodigoCNES                     = $row['CODIGO_CNES'];
$nomePrestador                  = $row['NOME_PRESTADOR'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Impress&atilde;o da guia de consulta</title>



<style type="text/css">
<!--
.style8 {
	font-size: 8px;
	font-family: Arial, Helvetica, sans-serif;
}
.style9 {font-size: 12px}
.style10 {
	font-size: 18px;
	font-weight: bold;
}
.style13 {font-family: Arial, Helvetica, sans-serif}
.style14 {font-size: 8px}
.style15 {font-size: 12px; font-weight: bold; }
.style17 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
}
.style18 {font-size: 9px}
body {
	margin-left: 0.1cm;
	margin-right: 0.1cm;
}
body p{padding: 0;margin: 0}
.style19 {font-family: Arial, Helvetica, sans-serif; font-size: 10px; }
-->
</style>

<style type="text/css">
<!--
.style20 {color: #FF0000}
-->

</style>
<link href="css/guia_consulta.css" rel="stylesheet" type="text/css" />

<style media="print">
    form {
        display: none;
    }
</style>

</head>

<body>
  
<table width="1070" border="1">
  <tr>
    <td width="1060">
        <table width="94%" height="56" border="0" cellpadding="0" cellspacing="0">
      <tr align="center" valign="middle">
        <td width="19%" height="56" valign="middle" class="style13" style="text-align: left"><img src='../../Site/assets/img/logo_operadora.png' width="120" height="84" /></td>
        <td width="52%" valign="middle" class="style13"><span class="style10"> GUIA DE CONSULTA</span></td>
        <td width="15%" valign="middle" class="style19">2 - N&ordm Guia no Prestador;</td>
        <td width="19%" valign="middle" class="style17"><?php echo $numeroGuia; ?></td>
      </tr>
    </table>
    <table width="48%" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td width="35%">
                <div class="label1">1 - Registro ANS</div>
                <div class="valor1">&nbsp;<?php echo $registroAns ?></div>
            </td>
            <!--<td width="55%">
                <div class="label1">3 - Data da Emiss&atilde;o da Guia</div>
                <div class="valor1"><?php echo $dataEmissao ?></div>
            </td>-->
			<td width="0%">
                <div class="label1">3 - N&uacute;mero da Guia Atribuido pela Operadora</div>
                  <div class="valor1">&nbsp;<?php echo $numero_guia ?></div>
            </td>
         </tr>
    </table>
    
    <div class="labelGrupo">Dados do benefici&aacute;rio</div>
    
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td width="33%">
                <div class="label1">4 - N&uacute;mero da Carteira</div>
                  <div class="valor1">&nbsp;<?php echo $numeroCarteira ?></div>
            </td>
            <td width="34%">
                <div class="label1">5 - Validade da Carteira</div>
                <div class="valor2">&nbsp;<?php echo !empty($DataValidadeCarteirinha) ? '<span class="valor2">'.$DataValidadeCarteirinha : '<span class="grade">|  |  |  |  |  |  |';?></span></div>
            </td>
            <td width="34%"">
                <div class="label1">6 - Atendimento a RN (Sim ou Não)</div>
                <div class="valor2">&nbsp;<?php echo $AtendimentoRn ?></span></div>
            </td>
          </tr>
    </table>
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td width="68%">
                <div class="label1">7 - Nome</div>
                <div class="valor1">&nbsp;<?php echo $nomeBeneficiario ?></div>
            </td>
            <td width="32%" class="label1">
               <div class="label1">8 - N&uacute;mero do cart&atilde;o Nacional de Sa&uacute;de</div>
               <div class="valor2">&nbsp;<?php echo !empty($CodigoCNS) ? '<span class="valor2">'.$CodigoCNS :  '<span class="grade">|  |  |  |  |  |  |  |' ;?></span></div>
            </td>
        </tr>
    </table>
  
    <div class="labelGrupo">Dados do Contratato</div>

    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td width="30%" height="40">
                <div class="label1">9 - C&oacute;digo na Operadora</div>
                <div class="valor1">&nbsp;<?php echo $_SESSION['codigoIdentificacao']; ?></div>
            </td>
            <td width="55%">
                <div class="label1">10 - Nome do Contratado</div>
                <div class="valor1">&nbsp;<?php echo $nomePrestador ?></div>
            </td>
            <td width="15%" class="label1">
                <div class="label1">11 - C&oacute;digo CNES</div>
                <div class="valor1">&nbsp;<?php echo $CodigoCNES; ?> </div>
            </td>
        </tr>
    </table>    
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td width="40%" class="label1">
                <div class="label1">12 - Nome do Profissional Executante</div>
                <div class="valor1">&nbsp;<?php echo $nomePrestadorExecutante; ?></div>
            </td>
            <td width="16%">
                <div class="label1">13 - Conselho Profissional</div>
                <div class="valor1">&nbsp;<?php echo $conselhoProfissionalExec; ?></div>
            </td>
            <td width="22%">
                <div class="label1">14 - N&uacute;mero do Conselho</div>
                <div class="valor1">&nbsp;<?php echo $numeroConselhoProfissionalExec; ?></div>
            </td>
            <td width="7%">
                <div class="label1">15 - UF</div>
                <div class="valor1">&nbsp;<?php echo $ufConselhoProfissionalExec; ?></div>
            </td>
            <td width="15%" class="label1">
                <div class="label1">16 - C&oacute;digo CBO S</div>
                <div class="valor1">&nbsp;<?php echo $CodigoCBO; ?></div>
            </td>
        </tr>
    </table>

<div class="labelGrupo">Dados do Atendimento / Procedimentos Realizado</div>

	<table width="38%" border="1">
        <tr>
            <td width="44%" class="label1">
                <div class="label1">17 - Indica&ccedil;&atilde;o de Acidente (acidente ou doenças relacionadas)</div>
                <div class="valor2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<span class="grade">| <?php echo $indicadorAcedidente; ?> |</span></div>
            </td>
        </tr>
    </table>
    <table width="90%" border="1" cellpadding="0" cellspacing="0">
        <tr valign="top">
            <td width="22%" height="37">
                <div class="label1">18 - Data do Atendimento</div>
                <div class="valor2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;<?php echo $DataProcedimento; ?> </div>
            </td>
			<td width="15%" valign="top">
                <div class="label1">19 - Tipo de Consulta</div>
                <div class="valor2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<span class="grade">| <?php echo $tipoConsulta; ?>  | </span></div>
            </td>
            <td width="20%">
                <div class="label1">20 - C&oacute;digo Tabela</div>
                <div class="valor2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php echo $codigoTabela; ?></div>
            </td>
            <td width="25%">
                <div class="label1">21 - C&oacute;digo Procedimento</div>
                <div class="valor2"><?php echo $codigoProcedimento; ?></div>
            </td>
			<td width="30%">
                <div class="label1">22 - Valor Procedimento</div>
                <div class="valor2"><?php  ?></div>
            </td>
        </tr>
    </table>
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr valign="top">
            <td height="74" valign="top" class="bgEscuro">
                <div class="label1">23 - Observa&ccedil;&atilde;o / Justificativa</div>
                <br />
                <div>
                ____________________________________________________________________________________________________________________________________________
                </div>
				<div>
                ____________________________________________________________________________________________________________________________________________
                
                </div>
                <div>
                ____________________________________________________________________________________________________________________________________________
                
                </div>
				<div>
                ____________________________________________________________________________________________________________________________________________
                
                </div>
				<div>
                ____________________________________________________________________________________________________________________________________________
                
                </div>
            </td>
        </tr>
    </table>
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr valign="top">
            <td width="36%" valign="top" class="bgEscuro">
                <div class="label1">24 - Assinatura do Profissional Executante</div>
                <div class="valor2"><span class="label1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp </span></div>
            </td>
            <td width="64%" valign="top" class="bgEscuro">
                <div class="label1">25 - Assinatura do Beneficiário ou Responsável</div>
                <div class="valor2"><span class="label1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp </span></div>
            </td>
        </tr>
    </table>
    </td>
  </tr>
</table>
</body>
</html>
