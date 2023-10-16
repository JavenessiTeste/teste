<?PHP

require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);



function getFaces($AFaces) {
    $_faces = '';
    foreach ((array) $AFaces as $face) {
        $_faces .=  substr($face['face_dente'], 0, 1);
    }
    if (empty($_faces)) {
        return '&nbsp;&nbsp;';
    } 
    return $_faces;
}

  $numero_plano_tratamento = isset($_GET['numero']) ? $_GET['numero'] : $_SESSION['numero_plano_tratamento'];
  
  if($numero_plano_tratamento==''){
	exit;
  }

  $buf = "SELECT Numero_Insc_Susep,Numero_Cnpj FROM CFGEMPRESA";

  $res=jn_query($buf);
  if($row=jn_fetch_object($res)){
      $NumeroInscSusep = $row->NUMERO_INSC_SUSEP;
	  $NumeroCNPJ = $row->NUMERO_CNPJ;
  }


  $buf = "SELECT Ps2500.Numero_Plano_Tratamento, PS2500.CODIGO_ASSOCIADO, PS2500.CODIGO_PRESTADOR, PS2500.DATA_CADASTRAMENTO,
			Ps1000.CODIGO_EMPRESA, PS1000.NOME_ASSOCIADO,
            Ps1000.Codigo_Plano, Ps1000.Data_Validade_Carteirinha, Ps1000.Codigo_CNS, T_Ps1000.Codigo_Titular, ps2500.data_real_termino,
            T_Ps1000.Nome_Associado as nome_associado_titular, Ps1010.Nome_empresa, Ps1010.Flag_PlanoFamiliar, Ps1010.Flag_Cad_Telefones_Func,
            Ps1030.Nome_plano_familiares, Ps5000.Nome_Prestador, Ps5000.Codigo_CNES, Ps5002.Numero_Crm, Ps5002.UF_Conselho_Profiss, Ps5002.Numero_Cpf, Ps5002.Numero_Cnpj from Ps2500
            inner join Ps1000 on(Ps2500.Codigo_Associado = Ps1000.Codigo_Associado)
            inner join Ps1000 as T_Ps1000 on(Ps1000.Codigo_Titular = T_Ps1000.Codigo_Associado)
            inner join Ps1010 on(Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa)
            inner join Ps1030 on(Ps1000.Codigo_Plano = Ps1030.Codigo_Plano)
            inner join Ps5000 on(Ps2500.Codigo_Prestador = Ps5000.Codigo_Prestador)
            inner join Ps5002 on(Ps5002.Codigo_Prestador = Ps5000.Codigo_Prestador)
            WHERE Ps2500.numero_plano_tratamento = " . aspas($numero_plano_tratamento);


    //pr($buf, true);
    $res=jn_query($buf);

    $row=jn_fetch_object($res);

    $numeroPlanoTratamento        = $row->NUMERO_PLANO_TRATAMENTO;

    $codigoAssociado              = $row->CODIGO_ASSOCIADO;
    $nomeBeneficiario             = $row->NOME_ASSOCIADO;
    $numeroCarteira               = $row->CODIGO_ASSOCIADO;
    $dataValidadeCarteirinha      = SqlToData2($row->DATA_VALIDADE_CARTEIRINHA);
    $dataCadastramento      	  = $row->DATA_CADASTRAMENTO;
    $codigoCNS                    = $row->CODIGO_CNS;

    $planoBeneficiario            = $row->NOME_PLANO_FAMILIARES;

    $codigoEmpresa                = $row->CODIGO_EMPRESA;
    $nomeEmpresa                  = $row->NOME_EMPRESA;
    $FlagPlanoFamiliar            = $row->FLAG_PLANOFAMILIAR;
    $FlagCadTelefonesFunc         = $row->FLAG_CAD_TELEFONES_FUNC;

    $nomePrestador                = $row->NOME_PRESTADOR;
    $codigoCNES                   = $row->CODIGO_CNES;
    $numeroCrm                    = $row->NUMERO_CRM;
    $ufConselhoProfiss            = $row->UF_CONSELHO_PROFISS;
    $prestadorCPF                 = $row->NUMERO_CPF;
    $prestadorCNPJ                = $row->NUMERO_CNPJ;
	$dataTermino	              = $row->DATA_REAL_TERMINO;	
    $codigoTitular                = $row->CODIGO_TITULAR;
    $nomeBeneficiarioTitular      = $row->NOME_ASSOCIADO_TITULAR;                
    $dataEmissaoGuia              = getDataAtual();
    $numeroGuia                   = $row->NUMERO_GUIA;
	$numeroSenhaAutoriz			  = $numero_plano_tratamento;
	

    
    if($FlagPlanoFamiliar == 'S' && $FlagCadTelefonesFunc == 'S'){
        $buf = "select Ps1006.Codigo_Area, Ps1006.Numero_telefone from Ps1006 "
                ."Where Ps1006.Codigo_Associado = " .$codigoAssociado;
    } else if($FlagPlanoFamiliar == 'N' && $FlagCadTelefonesFunc == 'S') {
        $buf = "select Ps1006.Codigo_Area, Ps1006.Numero_telefone from Ps1006 "
                ."Where Ps1006.Codigo_Associado = " .$codigoAssociado;
    } else if($FlagPlanoFamiliar == 'N' && $FlagCadTelefonesFunc == 'N') {
        $buf = "select Ps1006.Codigo_Area, Ps1006.Numero_telefone from Ps1006 "
                ."Where Ps1006.Codigo_Empresa = " .$codigoEmpresa;
    }

    $res=jn_query($buf);
    $row=jn_fetch_object($res);

    $codigoArea         = $row->CODIGO_AREA;
    $numeroTelefone     = $row->NUMERO_TELEFONE;


    
    $ArrProcs = array();

    $buf = "Select Ps2510.Numero_Registro, coalesce(Ps2510.valor_estimativa_custo, Ps2210.valor_estimativa_custo) as valor_estimativa_custo, Ps2510.Codigo_Procedimento, Ps2510.Situacao, Ps2510.Numero_Dente_Segmento, Ps2510.Quantidade_Procedimentos, Ps2510.Data_conclusao_procedimento, Ps2210.Nome_Procedimento from ps2510
            inner join Ps2210 on(Ps2510.Codigo_Procedimento = Ps2210.Codigo_Procedimento)
            Where Ps2510.Data_Cancelamento IS NULL AND Ps2510.Numero_Plano_Tratamento = " . aspas($numeroPlanoTratamento);

    $res=jn_query($buf);
    
    $i = 0;
	$valorTotal = 0;
    while($row = jn_fetch_assoc($res)) {
        $query = 'SELECT count(CODIGO_PROCEDIMENTO_PACOTE) 
                  FROM Ps2211
                  WHERE CODIGO_PROCEDIMENTO_PACOTE = \''. $row['CODIGO_PROCEDIMENTO'] .'\'
                  GROUP BY CODIGO_PROCEDIMENTO_PACOTE';
        $sepacote = jn_query($query);
        $sepacoteval = jn_fetch_row($sepacote);
        if(!$sepacoteval[0]){
            $ArrProcs[$i] = array(
                'numero_registro'				=> $row['NUMERO_REGISTRO'],
                'codigo_procedimento'			=> $row['CODIGO_PROCEDIMENTO'],
                'valor_estimativa_custo'		=> toMoeda($row['VALOR_ESTIMATIVA_CUSTO']),
                'nome_procedimento'				=> substr($row['NOME_PROCEDIMENTO'], 0, 35),
                //'nome_procedimento'			=> $row['NOME_PROCEDIMENTO'],
                'numero_dente_segmento'			=> $row['NUMERO_DENTE_SEGMENTO'],
                'situacao'						=> $row['SITUACAO'],
                'quantidade_procedimentos'		=> number_format($row['QUANTIDADE_PROCEDIMENTOS']),
                'faces'							=> array(),
                'data_conclusao_procedimento'   => SqlToData2($row['DATA_CONCLUSAO_PROCEDIMENTO'])
            );
            
			$valorTotal = $valorTotal + $row['VALOR_ESTIMATIVA_CUSTO'];

            $query  = 'Select Ps2511.Face_Dente FROM Ps2511 ';
            $query .= 'Where Numero_Registro_Ps2510 = ' . $ArrProcs[$i]['numero_registro'];

            $resFaces = jn_query($query);

            while ($rowFace = jn_fetch_object($resFaces)) {
                $ArrProcs[$i]['faces'][] = array(
                    'face_dente' => $rowFace->FACE_DENTE
                );
            }

            $i++;
        }
    }

    foreach ($ArrProcs as $proc) {
        foreach ($proc['faces'] as $face) {
             $face = explode("-", $face['face_dente']);
        }
    }
	
	$dataValidadeSenha = date('d/m/Y', strtotime($dataCadastramento. ' + 90 days'));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Emiss&atilde;o da guia de SP/SADT</title>

<link type="text/css" href="js/jquery/ui/css/themes/base/ui.all.css" rel="stylesheet" />
<link type="text/css" href="js/jquery/tooltip/jquery.tooltip.css" rel="stylesheet" />


<script src="js/AC_RunActiveContent.js" type="text/javascript"></script>
<script src="js/jquery/jquery.js" type="text/javascript"></script>

<script type="text/javascript" src="js/jquery/jquery.js"></script>
<script type="text/javascript" src="js/jquery/bgiframe/jquery.bgiframe.js"></script>
<script type="text/javascript" src="js/jquery/dimensions/jquery.dimensions.js"></script>
<script type="text/javascript" src="js/jquery/tooltip/jquery.tooltip.js"></script>
<script type="text/javascript" src="js/jquery/ui/ui.core.js"></script>
<script type="text/javascript" src="js/jquery/ui/ui.draggable.js"></script>
<script type="text/javascript" src="js/jquery/ui/ui.resizable.js"></script>
<script type="text/javascript" src="js/jquery/ui/ui.dialog.js"></script>
<script src="js/utils.js" type="text/javascript"></script>

<script type="text/javascript">
    function imprimir(){
        alert("Para imprimir a guia configure a página para o formato paisagem \n\nConfigure o cabeçalho e o rodapé para não serem impressos\n\nConfigure as margens da página: esquerda(5 milímetros), direita(5 milímetros), superior(5 milímetros) e inferior(5 milímetros)");
        window.print(); return false;

    }
</script>
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
.style33 {font-size: 1.5em; font-weight: bold; }
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
<link href="css/guia_tratamento_odonto.css" rel="stylesheet" type="text/css" />

<style media="print">
    form {
        display: none;
    }
</style>

</head>
<body>
<form id="form1" name="form1" action="">

  <table width="1590" height="907" border="1" align="left">
  <tr>

    <td width="1309" height="903" align="left" valign="top"><div align="center">
      <table width="100%" border="0">
          <tr>
            <td width="21%" height="40" style="text-align: left"><img src="../../Site/assets/img/logo_operadora.png" width="80" height="44" /></td>
            <td width="61%"><div align="center" class="style36">GUIA TRATAMENTO ODONTOL&Oacute;LOGICO</div></td>
            <td width="18%" class="style33">2 - Nº Guia no Prestador</td>
          </tr>
        </table>
        </div>

        <table width="100%" height="27" border="1" cellpadding="0" cellspacing="0">
            <tr align="left" valign="top">
                <td width="6%">
                    <div class="label1">1 - Registro ANS</div>
                    <div class="valor1">&nbsp;<?PHP echo $NumeroInscSusep; ?></div>
                </td>
                <td width="20%" class="label1">
                    <div class="label1">3 - N&ordm; Guia Principal</div>
                    <div class="valor1">&nbsp;<?PHP echo $numeroGuiaPrincipal; ?></div>
                </td>
                <td width="10%" class="label1">
                    <div class="label1">4 - Data da Autoriza&ccedil;&atilde;o</div>
                    <div class="valor2">&nbsp;<?PHP echo !empty($dataCadastramento) ? SqlToData($dataCadastramento) :  '<span class="grade">|  |  |/|  |  |/|  |  |</span>';  ?></div>
                </td>
                <td width="18%" class="label1">
                    <div class="label1">5 - Senha</div>
                    <div class="valor1">&nbsp;<?PHP echo $numeroSenhaAutoriz; ?></div>
                </td>
                <td width="10%" class="label1">
                    <div class="label1">6 - Data Validade da Senha</div>
                    <div class="valor2">&nbsp;<?PHP echo $dataValidadeSenha; ?></div>
                </td>
                <td width="20%" class="label1">
                    <div class="label1">7 - N&ordm; da Guia Atribuido pela Operadora</div>
                    <div class="valor1">&nbsp;<?PHP echo $numeroSenhaAutoriz; ?></div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Benefici&aacute;rio</div>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="16%">
                    <div class="label1">8 - N&uacute;mero da Carteira</div>
                    <div class="valor2"><?PHP echo !empty($numeroCarteira) ? $numeroCarteira : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                </td>
                <td>
                    <div class="label1">9 - Plano</div>
                    <div class="valor1">&nbsp;<?PHP echo $planoBeneficiario?></div>

                </td>
                <td width="25%" class="bgEscuro">
                    <div class="label1">10 - Empresa</div>
                    <div class="valor1">&nbsp;<?PHP echo $nomeEmpresa; ?></div>
                </td>
                <td width="16%" class="bgEscuro">
                    <div class="label1">11 - Data Validade da Carteira</div>
                    <div class="valor2"><?PHP echo !empty($dataValidadeCarteirinha) ? $dataValidadeCarteirinha : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                </td>
                <td width="16%" class="bgEscuro">
                    <div class="label1">12 - N&uacute;mero do Cartao Nacional de Sa&uacute;de</div>
                    <div class="valor1">&nbsp;<?PHP echo $codigoCNS; ?></div>
                </td>
            </tr>

        </table>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="45%">
                    <div class="label1">13 - Nome</div>
                    <div class="valor1">&nbsp;<?php echo $nomeBeneficiario; ?></div>
                </td>
                <td width="10%" class="bgEscuro">
                    <div class="label1">14 - Telefone</div>
                    <div class="valor1"><?php echo !empty($codigoArea) ? '(' .$codigoArea.') ' : '<span class="grade">(&nbsp;&nbsp;)</span>'; echo !empty($numeroTelefone) ? $numeroTelefone : '<span class="grade">|  |  |  |  |-|  |  |  |  |</span>' ?></div>
                </td>
                <td width="15%" class="bgEscuro">
                    <div class="label1">15 - Nome do T&iacute;tular do Plano</div>
                    <div class="valor1">&nbsp;<?php echo $nomeBeneficiarioTitular; ?></div>
                </td>
				<td width="10%" class="bgEscuro">
                    <div class="label1">16 - Atendimento a RN</div>
                    <div class="valor1">&nbsp;<?PHP echo $atendimentoRN; ?></div>
                </td>
            </tr>
        </table>

        <div width="100%" class="labelGrupo">Dados do Contratado Respons&aacute;vel pelo Plano</div>
        
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro">
                    <div class="label1">17 - Nome do Profissional Solicitante</div>
                    <div class="valor1">&nbsp;<?PHP echo $nomePrestador; ?></div>
                </td>
                <td width="15%" class="bgEscuro">
                    <div class="label1">18 - N&uacute;mero no CRO</div>
                    <div class="valor1">&nbsp;<?PHP echo $numeroCrm; ?></div>
                </td>
                <td width="7%" class="bgEscuro">
                    <div class="label1">19 - UF</div>
                    <div class="valor1">&nbsp;<?PHP echo $ufConselhoProfiss; ?></div>
                </td>
                <td width="10%" class="bgEscuro">
                    <div class="label1">20 - C&oacute;digo CBO'S</div>
                    <div class="valor1">&nbsp;</div>
                </td>
           </tr>
        </table>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="19%">
                    <div class="label1">21 - C&oacute;digo da Operadora</div>
                    <div class="valor1"><?php echo $NumeroCNPJ;//!empty($prestadorCNPJ) ? $prestadorCNPJ : $prestadorCPF ?></div>
                </td>
                <td width="55%">
                    <div class="label1">22 - Nome do Contratado Executante</div>
                    <div class="valor1">&nbsp;<?PHP echo $nomePrestador; ?></div>
                </td>
                <td>
                    <div class="label1">23 - N&uacute;mero do CRO</div>
                    <div class="valor2">&nbsp;<?PHP echo $numeroCrm; ?></div>

                </td>
                <td>
                    <div class="label1">24 - UF</div>
                    <div class="valor1">&nbsp;<?PHP echo $ufConselhoProfiss; ?></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">25 - C&oacute;digo CNES</div>
                    <div class="valor1">&nbsp;<?PHP echo $codigoCNES?></div>
                </td>
            </tr>
        </table>
            
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="65%" class="bgEscuro">
                    <div class="label1">26 - Nome do Profissional Executante</div>
                    <div class="valor1">&nbsp;<?PHP echo $nomePrestador?></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">27 - N&uacute;mero no CRO</div>
                    <div class="valor1">&nbsp;<?PHP echo $numeroCrm; ?></div>
                </td>
                <td width="7%" class="bgEscuro">
                    <div class="label1">28 - UF</div>
                    <div class="valor1">&nbsp;<?PHP echo $ufConselhoProfiss;?></div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">29 - C&oacute;digo CBO'S</div>
                    <div class="valor1">&nbsp;</div>
                </td>
            </tr>
        </table>

        <div width="95%" class="labelGrupo">Plano de tratamento / Procedimentos Solicitados / Procedimentos Executados</div>
        
        <table id="GridProcsRel" width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td width="7%">
                    <div class="label1">30 - Tabela</div>
                    <div class="valor2"><span class="subLabel">1 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">2 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">3 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">4 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">5 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">6 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">7 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">8 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">9 - &nbsp;</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">10-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">11-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">12-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">13-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">14-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">15-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">16-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">17-</span><span class="grade">|  |  |</span></div>
					<div class="valor2"><span class="subLabel">18-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">19-</span><span class="grade">|  |  |</span></div>
                    <div class="valor2"><span class="subLabel">20-</span><span class="grade">|  |  |</span></div>
                </td>

                <td width="14%">
                    <div class="label1">31 - C&oacute;digo do Procedimento</div>
                    <div class="valor2"><?php echo !empty($ArrProcs[0]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[0]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[1]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[1]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[2]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[2]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[3]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[3]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[4]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[4]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[5]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[5]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[6]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[6]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[7]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[7]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[8]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[8]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[9]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[9]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[10]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[10]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[11]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[11]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[12]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[12]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[13]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[13]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[14]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[15]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[16]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[14]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[15]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['codigo_procedimento']) ? '<span class="valor3">'.$ArrProcs[16]['codigo_procedimento'].'</span>' : '<span class="grade">|  |  |  |  |  |  |  |  |  |  |</span>'; ?></div>
                    
                   
                </td>
                <td width="10%">
                    <div class="label1">32 - Descri&ccedil;&atilde;o</div>
                    <div class="valor2"><?php echo !empty($ArrProcs[0]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[0]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[1]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[1]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[2]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[2]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[3]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[3]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[4]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[4]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[5]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[5]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[6]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[6]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[7]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[7]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[8]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[8]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[9]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[9]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[10]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[10]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[11]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[11]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[12]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[12]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[13]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[13]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[14]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[15]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[16]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[14]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[15]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['nome_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[16]['nome_procedimento'].'</span>' : '<span class="grade3">&nbsp;&nbsp;</span>'; ?></div>
                             
                </td>
                <td width="8%" class="bgEscuro">
                    <div class="label1">33-Dente/Regi&atilde;o 34-Face</div>
                    <div class="valor2"><?php echo !empty($ArrProcs[0]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[0]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|<span class="subLabel2">' . getFaces($ArrProcs[0]['faces']) . '</span>|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[1]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[1]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[1]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[2]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[2]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[2]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[3]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[3]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[3]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[4]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[4]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[4]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[5]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[5]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[5]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[6]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[6]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[6]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[7]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[7]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[7]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[8]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[8]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[8]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[9]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[9]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[9]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[10]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[10]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[10]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[11]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[11]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[11]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[12]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[12]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[12]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[13]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[13]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[13]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[14]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[14]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[15]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[15]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[16]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[16]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[14]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[14]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[15]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[15]['faces']) . '|</span>';?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['numero_dente_segmento']) ? '<span class="subLabel2">'.$ArrProcs[16]['numero_dente_segmento'].'</span>' : '<span class="grade">|&nbsp;&nbsp;|</span>'; echo '<span class="grade">|' . getFaces($ArrProcs[16]['faces']) . '|</span>';?></div>
                    
                                     
                </td>
                <td width="5%" class="bgEscuro">
                    <div class="label1">&nbsp;35 - Qtd</div>
                    <div class="valor2"><?php echo !empty($ArrProcs[0]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[0]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[1]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[1]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[2]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[2]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[3]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[3]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[4]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[4]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[5]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[5]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[6]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[6]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[7]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[7]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[8]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[8]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[9]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[9]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[10]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[10]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[11]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[11]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[12]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[12]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[13]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[13]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[14]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[15]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[16]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
					<div class="valor2"><?php echo !empty($ArrProcs[14]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[14]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[15]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['quantidade_procedimentos']) ? '<span class="subLabel2">'.$ArrProcs[16]['quantidade_procedimentos'].'</span>' : '<span class="grade">|  |  |</span>'; ?></div>
	
                </td>
                <td width="12%" class="bgEscuro">
                    <div class="label1">&nbsp;36 - Qtde US</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
					<div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td width="12%" class="bgEscuro">
                    <div class="label1">37 - Valor R$</div>
                    <div class="valor2"><?php echo !empty($ArrProcs[0]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[0]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[1]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[1]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[2]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[2]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[3]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[3]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[4]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[4]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[5]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[5]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[6]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[6]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[7]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[7]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[8]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[8]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[9]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[9]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[10]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[10]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[11]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[11]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[12]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[12]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[13]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[13]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[14]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[15]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[16]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[17]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[17]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[18]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[18]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					
                    <div class="valor2"><?php echo !empty($ArrProcs[19]['valor_estimativa_custo']) ? '<span class="subLabel2">'.$ArrProcs[19]['valor_estimativa_custo'].'</span>' : '<span class="grade">|  |  |  |  |  |,|  |  |</span>'; ?></div>					                    
                </td>
                <td width="12%" class="bgEscuro">
                    <div class="label1">38 - Franquia R$</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>                    
					<div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
					<div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |  |,|  |  |</span></div>
                </td>
                <td width="4%" class="bgEscuro">
                    <div class="label1">39 - Aut</div>
					 <div class="valor2"><?php echo !empty($ArrProcs[0]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[0]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>
					 <div class="valor2"><?php echo !empty($ArrProcs[1]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[1]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[2]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[2]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[3]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[3]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[4]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[4]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[5]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[5]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[6]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[6]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[7]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[7]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[8]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[8]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[9]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[9]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[10]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[10]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[11]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[11]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[12]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[12]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[13]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[13]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[14]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[14]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[15]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[15]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[16]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[16]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[17]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[17]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[18]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[18]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
					 <div class="valor2"><?php echo !empty($ArrProcs[19]['situacao']) ? '<span class="subLabel2">'.$ArrProcs[19]['situacao'].'</span>' : '<span class="grade">|  |</span>'; ?></div>                    
                    
                </td>
				<td width="8%" class="bgEscuro">
                    <div class="label1">40 - Cod. Negativa</div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
					<div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                    <div class="valor2"><span class="grade">|  |  |  |  |</span></div>
                </td>
                <td width="12%" class="bgEscuro">
                    <div class="label1">&nbsp;41 - Data da Realiza&ccedil;&atilde;o</div>
					<div class="valor2"><?php echo !empty($ArrProcs[0]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[0]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[1]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[1]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[2]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[2]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[3]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[3]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[4]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[4]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[5]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[5]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[6]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[6]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[7]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[7]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[8]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[8]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[9]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[9]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[10]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[10]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[11]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[11]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[12]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[12]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[13]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[13]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[14]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[14]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[15]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[16]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
					<div class="valor2"><?php echo !empty($ArrProcs[14]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[14]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[15]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[15]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    <div class="valor2"><?php echo !empty($ArrProcs[16]['data_conclusao_procedimento']) ? '<span class="subLabel2">'.$ArrProcs[16]['data_conclusao_procedimento'].'</span>' : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>

                </td>
                <td width="17%" class="bgEscuro" align="left">
                    <div class="label1" align="left">42 - Assinatura</div>
                    <div class="valor2"><span class="grade3"> &nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3"> &nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
					<div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                    <div class="valor2"><span class="grade3">&nbsp;&nbsp;</span></div>
                   	
                </td>
            </tr>
        </table>
        <table id="GridDataAssinatura" width="80%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro">
                    <div class="label1">43 - Data Termino do Tratamento</div>
                    <div class="valor2">
                        <div class="valor2"><?PHP echo !empty($dataTermino) ? $dataTermino : '<span class="grade">|  |  |/|  |  |/|  |  |</span>'; ?></div>
                    </div>
                </td>
                <td class="bgEscuro" width="28%">
                    <div class="label1">44 - Tipo de Atendimento</div>
                    <div class=valor2"">
                        <div> <span class="grade">| <?PHP echo $tipoAtendimento; ?> | </span></div>
                    </div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">45 - Tipo de Faturamento</div>
                    <div class="valor2">
                        <span class="grade">|  |</span>
                    </div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">46 - Total Quantidade US</div>
                    <div class="valor2">
                        <span class="grade">|  |  |  |  |  |  |,|  |  |</span>
                    </div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">47 - Valor Total R$</div>
                    <div class="valor2">
                        <span class="grade"><?php echo toMoeda($valorTotal); ?></span>
                    </div>
                </td>
                <td class="bgEscuro">
                    <div class="label1">48 - Total Franquia R$</div>
                    <div class="valor2">
                        <span class="grade">|  |  |  |  |  |  |,|  |  |</span>
                    </div>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <div class="label1">
                        Declaro, que ap&oacute;s ter sido devidamente esclarecido sobre os prop&oacute;sitos, riscos, custos e alternativas de tratamento, conforme acima apresentados,
                        aceito e autorizo a execu&ccedil;&atilde;o do tratamento, comprometendo-me a cumprir as orienta&ccedil;&atilde;es do profissional assistente e arcar com os custos previstos
                        em contrato. Declaro, ainda, que o(s) procedimento(s) descrito(s) acima, e por mim assinado(s), foi/foram realizado(s) com o meu consentimento
                        e de forma satisfat&oacute;ria. Autorizo a operadora a pagar em meu nome e por minha conta, ao profissional contratado que assina esse documento, os
                        valores referentes ao tratamento realizado, compremetendo-me a arcar com os custos conforme previsto em contrato.
                    </div>
                </td>
            </tr>
        </table>
        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro">

                    <div class="label1">49 - Observa&ccedil;&atilde;o / Justificativa</div>
                    <div class="grade2">&nbsp;</div>                   
                </td>
            </tr>
        </table>

        <table width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bgEscuro" width="20%">
                    <div class="label1">50 - Data da Assinatura do Cirurgi&atilde;o-Dentista Solicitante</div>
                    <div class="valor2"><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                </td>
				<td class="bgEscuro" width="30%">
                    <div class="label1">51 - Assinatura do Cirurgi&atilde;o-Dentista Solicitante</div>
                    <div class="valor2" class="grade">&nbsp;&nbsp;</div>
                </td>
                <td>
                    <div class="label1" width="20%">52 - Data da Assinatura do Cirurgi&atilde;o-Dentista</div>
                    <div class="valor2"><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                </td>
				<td class="bgEscuro" width="30%">
                    <div class="label1">53 - Assinatura do Cirurgi&atilde;o-Dentista</div>
                    <div class="valor2" class="grade">&nbsp;&nbsp;</div>
                </td>
			</tr>
			</table>
			<table width="100%" border="1" cellpadding="0" cellspacing="0">
			<tr>
                <td>
                    <div class="label1" width="20%">54 - Data da Assinatura do Benefici&aacute;rio / Respons&aacute;vel</div>
                    <div class="valor2"><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                </td>
				<td class="bgEscuro" width="30%">
                    <div class="label1">55 - Assinatura do Benefici&aacute;rio / Respons&aacute;vel</div>
                    <div class="valor2" class="grade">&nbsp;&nbsp;</div>
                </td>
                <td class="bgEscuro" width="50%">
                    <div class="label1">56 - Data do Carimbo da Empresa</div>
                    <div class="valor2"><span class="grade">|  |  |/|  |  |/|  |  |</span></div>
                </td>
            </tr>
        </table>
    </td>
  </tr>
</table>
</body>
</html>
