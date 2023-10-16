<?php
require('../lib/base.php');
//require('../lib/mpdf60/mpdf.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

    $numero_Autorizacao = isset($_GET['numero']) ? $_GET['numero'] : $_SESSION['numeroAutorizacao'];
	
	if($numero_Autorizacao==''){
		exit;
	}

    $buf = "select CfgEmpresa.Nome_Empresa, CfgEmpresa.Razao_Social, CfgEmpresa.Numero_Insc_Susep, "
            ."Ps6500.Tipo_Guia, Ps6500.Data_Autorizacao, Ps6500.Codigo_Associado, Ps6500.Codigo_Prestador, Ps6500.Codigo_Prestador_Executante, Ps6500.Codigo_Cid, "
            ."Ps6500.Numero_Autorizacao, Ps1000.Nome_Associado, Ps5000.Nome_Prestador, Ps5000.Codigo_CNES,Ps1000.Codigo_Cns, Ps1000.Data_Validade_Carteirinha, Ps5000.Tipo_Pessoa, "
            ."Ps6500.Tipo_Doenca, Ps6500.Tempo_Doenca, Ps6500.INDICADOR_ACIDENTE, Ps6500.Unidade_Tempo_Doenca, pS6500.Data_Validade, pS6500.PROCEDIMENTO_PRINCIPAL, "
            ."Ps1030.Nome_Plano_Familiares, Ps6500.Numero_Guia, Ps6500.Numero_guia_operadora, PS6500.TIPO_CONSULTA, ps6500.FLAG_ATENDIMENTO_RN, "
            ."Ps5002.CODIGO_CONSELHO_PROFISS, Ps5002.UF_CONSELHO_PROFISS, Ps5002.NUMERO_CRM, Ps5002.Numero_Cpf, Ps5002.Numero_Cnpj, "

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
            ."WHERE Ps5000.codigo_prestador = Ps6500.codigo_prestador_executante) AS Nome_Prestador_Executante, "
            
			."Ps5100.Codigo_Terminologia_Cbo, Ps5100.Nome_Especialidade 	"
			
            ."From Ps6500 "
            ."Inner Join Ps1000 On (Ps6500.codigo_Associado = Ps1000.Codigo_Associado) "
            ."Inner Join Ps5000 On (Ps6500.codigo_Prestador = Ps5000.Codigo_Prestador) "
            ."Left Outer Join Ps5002 On (Ps5000.Codigo_Prestador = Ps5002.Codigo_Prestador) "
            ."Left Outer Join Ps5002 Ps5002_1 On (Ps6500.Codigo_Prestador_Executante = Ps5002_1.Codigo_Prestador) "
            ."Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) "
			
			."Left Outer Join Ps5100 On (Ps6500.Codigo_Especialidade = Ps5100.Codigo_Especialidade) "
            
			."Inner Join CfgEmpresa On (Nome_Empresa is not null) "
            ."Left join ps5001 on (Ps5001.Codigo_Prestador = Ps6500.Codigo_Prestador and ps5001.flag_ha_atendimento = 'S' "
            ."						and ((Ps6500.Registro_Endereco_Prestador is null) or (Ps6500.Registro_Endereco_Prestador = Ps5001.numero_registro_endereco))) "            
            ." Where Ps6500.Numero_Autorizacao = ". aspas($numero_Autorizacao);


    //pr($buf, true);
    $res=jn_query($buf);

    $row=jn_fetch_assoc($res);

    $numeroGuia                     = $row['NUMERO_GUIA'];
    $numeroGuiaOperadora            = $row['NUMERO_GUIA_OPERADORA'];
	$tipoConsulta                   = $row['TIPO_CONSULTA'];
	$indicadorAcedidente            = $row['INDICADOR_ACIDENTE'];
	$AtendimentoRn            		= $row['FLAG_ATENDIMENTO_RN'];
	$registroAns                    = $row['NUMERO_INSC_SUSEP'];
    $dataEmissao                    = date("d/m/Y",strtotime($row['DATA_AUTORIZACAO']));
    $numeroCarteira                 = $row['CODIGO_ASSOCIADO'];
    $planoBeneficiario              = $row['NOME_PLANO_FAMILIARES'];
    $nomeBeneficiario               = $row['NOME_ASSOCIADO'];
    // Este campo pode ser o codigo do prestador na operadora, ou seu cnpj ou seu CPF
    $codigoPrestador                = $row['NUMERO_CNPJ'].$row['NUMERO_CPF'];
    $codigoPrestadorExecutante      = $row['CODIGO_PRESTADOR_EXECUTANTE'];
    $nomePrestador                  = $row['NOME_PRESTADOR'];
    $nomePrestadorExecutante        = $row['NOME_PRESTADOR_EXECUTANTE'];
    $conselhoProfissional           = $row['CODIGO_CONSELHO_PROFISS'];
    $conselhoProfissionalExec       = $row['CODIGO_CONSELHO_PROFISS_1'];
    $numeroConselhoProfissional     =  $row['NUMERO_CRM'];
    $numeroConselhoProfissionalExec = $row['NUMERO_CRM_1'];
    $ufConselhoProfissional         = $row['UF_CONSELHO_PROFISS'];
    $ufConselhoProfissionalExec     = $row['UF_CONSELHO_PROFISS_1'];
    $codigoProcedimento             = "00010014";
    $DataValidadeCarteirinha        = SqlToData2($row['DATA_VALIDADE_CARTEIRINHA']);
    $CodigoCid                      = $row['CODIGO_CID'];
    $CodigoCNES                     = $row['CODIGO_CNES'];
    $CodigoCNS                      = $row['CODIGO_CNS'];
	
	$CodigoCBO                      = $row['CODIGO_TERMINOLOGIA_CBO'];
	$NomeEspecialidade              = $row['NOME_ESPECIALIDADE'];
		
    $autorizacao                    = $row['NUMERO_AUTORIZACAO'];
    $Endereco                       = $row['ENDERECO'];
    $Cidade                         = $row['CIDADE'];
    $Estado                         = $row['ESTADO'];
    $Cep                            = $row['CEP'];
    $Telefone_01                    = $row['TELEFONE_01'];

    $TipoDoenca                     = $row['TIPO_DOENCA'];
    $TempoDoenca                    = $row['TEMPO_DOENCA'];
    $UnidadeTempoDoenca             = $row['UNIDADE_TEMPO_DOENCA'];
    $DataAutorizacao                = SqlToData($row['DATA_AUTORIZACAO']);
    $DataValidade                   = $row['DATA_VALIDADE'];
    $ProcedimentoPrincipal          = $row['PROCEDIMENTO_PRINCIPAL'];
    $codigoPrestEnd          		= $row['CODIGO_PRESTADOR'];

	$numeroGuia = validaGuiaExistente($numeroGuia,'C'); 
		
	if($tipoConsulta != ''){		
		if($tipoConsulta == '1'){
			$tipoConsulta = 'PRIMEIRA CONSULTA';
		}else if($tipoConsulta == '2'){
			$tipoConsulta = 'RETORNO';
		}else if($tipoConsulta == '3'){
			$tipoConsulta = 'PRE NATAL';
		}else if($tipoConsulta == '4'){
			$tipoConsulta = 'POR ENCAMINHAMENTO';
		}
	}
    /*
     * Verifico se a guia pode ser impressa novamente ou não
     */
    if ((rvc('BLOQ_IMP_ALT_AUTORIZ_VENCIDA') == 'SIM') && (compareData(SqlToData($DataValidade), null) < 0)) {
        echo "<script>alert('Esta autorização não pode ser mais impressa pois a data de validade já foi atingida.'); history.go(-1);</script>";
        exit();
    }

//ob_start();
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
                  <div class="valor1">&nbsp;<?php echo $autorizacao ?></div>
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
            <td width="34%">
                <div class="label1">6 - Atendimento a RN (Sim ou Não)</div>
                <div class="valor2">&nbsp;<span class="valor2"><?php echo $AtendimentoRn ?></span></div>
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
                <div class="valor1">&nbsp;<?php echo $codigoPrestador ?></div>
            </td>
            <td width="55%">
                <div class="label1">10 - Nome do Contratado</div>
                <div class="valor1">&nbsp;<?php echo $nomePrestador ?></div>
            </td>
            <td width="15%" class="label1">
                <div class="label1">11 - C&oacute;digo CNES</div>
                <div class="valor1">&nbsp;<?php echo isset($CodigoCNES) ? $CodigoCNES : ''; ?> </div>
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
                <div class="valor1">&nbsp;<?php echo empty($codigoPrestadorExecutante) ? $conselhoProfissional : $conselhoProfissionalExec; ?></div>
            </td>
            <td width="22%">
                <div class="label1">14 - N&uacute;mero do Conselho</div>
                <div class="valor1">&nbsp;<?php echo empty($codigoPrestadorExecutante) ? $numeroConselhoProfissional : $numeroConselhoProfissionalExec ?></div>
            </td>
            <td width="7%">
                <div class="label1">15 - UF</div>
                <div class="valor1">&nbsp;<?php echo empty($codigoPrestadorExecutante) ? $ufConselhoProfissional : $ufConselhoProfissionalExec ?></div>
            </td>
            <td width="15%" class="label1">
                <div class="label1">16 - C&oacute;digo CBO S</div>
                <div class="valor1">&nbsp;<?php echo isset($CodigoCBO) ? $CodigoCBO : ''; ?> </div>
            </td>
        </tr>
    </table>

<div class="labelGrupo">Dados do Atendimento / Procedimentos Realizado</div>

	<table width="38%" border="1">
        <tr>
            <td width="44%" class="label1">
                <div class="label1">17 - Indica&ccedil;&atilde;o de Acidente (acidente ou doenças relacionadas)</div>
                <div class="valor2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<span class="grade">| <?php echo $indicadorAcedidente; ?> |</span></div>
            </td>
        </tr>
    </table>
    <table width="90%" border="1" cellpadding="0" cellspacing="0">
        <tr valign="top">
            <td width="22%" height="37">
                <div class="label1">18 - Data do Atendimento</div>
				
                <div class="valor2"><?php echo ( $DataAutorizacao != null ) ? $DataAutorizacao :'<span class="grade">|  |  |/|  |  |/|  |  |</span>';?></div>
            </td>
			<td width="15%" valign="top">
                <div class="label1">19 - Tipo de Consulta</div>
                <div class="valor2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<span class="grade">| <?php $tipoConsulta ?>  | </span></div>
            </td>
            <td width="20%">
                <div class="label1">20 - C&oacute;digo Tabela</div>
                <div class="valor2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<span class="grade">| <?php  ?>  |  |</span></div>
            </td>
            <td width="25%">
                <div class="label1">21 - C&oacute;digo Procedimento</div>
                <div class="valor2"><?php echo ( $ProcedimentoPrincipal != null ) ? $ProcedimentoPrincipal :'<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
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
                <?php 
					$queryEnd = 'SELECT FIRST 1 * FROM PS5001 
								 WHERE PS5001.data_inutiliz_registro IS NULL 
									AND  CODIGO_PRESTADOR = ' . $codigoPrestEnd;
					//pr($queryEnd);
					$resEnd = jn_query($queryEnd);
					$rowEnd = jn_fetch_object($resEnd);
										
					$dadosEnderecoPrestador = 'End. Prestador:  CEP - ' . $rowEnd->CEP . ' Logradouro: ' . $rowEnd->ENDERECO . '  -  ' . $rowEnd->BAIRRO;
					$dadosEnderecoPrestador .= ' ,' . $rowEnd->CIDADE . ' - ' . $rowEnd->ESTADO;
					$dadosEnderecoPrestador .= '&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';
					$dadosEnderecoPrestador .= '&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';
				
					
					$dadosEnderecoPrestador .= ' Telefone:  ' . $rowEnd->TELEFONE_01;					
				?>
				<div class="label1">23 - Observa&ccedil;&atilde;o / Justificativa
				<br>
										 &nbsp;&nbsp;&nbsp;<?php echo $dadosEnderecoPrestador;?>
										 <br>
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 Senha : <?php echo $autorizacao ; ?></div>
                <div class="label1">     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 Especialidade : <?php echo $NomeEspecialidade ; ?></div>
				____________________________________________________________________________________________________________________________________________						 
				<br />
                <div>
				____________________________________________________________________________________________________________________________________________
                </div>
				<div>
                ____________________________________________________________________________________________________________________________________________
                </div>
                <div class="label1">     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										 Validade : <?php echo $DataValidade ; ?></div>
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
<?php

//$html = ob_get_contents();
//ob_end_clean();
//echo  $html;
//$mpdf=new mPDF();
//$html = file_get_contents($pagina);
//echo $html;
//exit;
//$mpdf->WriteHTML('');
//$mpdf->Output();

?>
