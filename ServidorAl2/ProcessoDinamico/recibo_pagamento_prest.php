<?php
require('../lib/base.php');
require('../private/autentica.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);


$codigoPrest = $_SESSION['codigoIdentificacao'];
$numeroProcessamento =  $_POST['NUMERO_PROCESSAMENTO'];

if($codigoPrest == '' || $numeroProcessamento == ''){
	echo 'Processamento não preenchido';
	exit;
}

$queryProc  = ' SELECT ';
$queryProc .= ' 	CODIGO_PRESTADOR, ';
$queryProc .= ' 	NOME_PRESTADOR, ';
$queryProc .= ' 	RAZAO_SOCIAL_NM_COMPLETO, ';
$queryProc .= ' 	TIPO_PRESTADOR, ';
$queryProc .= ' 	ISS_AUTONOMO, ';
$queryProc .= ' 	NUMERO_CPF, ';
$queryProc .= ' 	MES_ANO_REFERENCIA, ';
$queryProc .= ' 	VALOR_CAPITALIZACAO_COOPE, ';
$queryProc .= ' 	VALOR_IRRF, ';
$queryProc .= ' 	VALOR_LIQUIDO_PAGAR, ';
$queryProc .= ' 	PERCENTUAL_CAPITALIZ_COOP, ';
$queryProc .= ' 	NUMERO_CNPJ, ';
$queryProc .= ' 	NUMERO_INSC_ESTADUAL, ';
$queryProc .= ' 	VALOR_GERADO_TOTAL, ';
$queryProc .= ' 	VALOR_INSS, ';
$queryProc .= ' 	VALOR_TOTAL_DESCONTO, ';
$queryProc .= ' 	VALOR_GERADO_ODONTO, ';
$queryProc .= ' 	VALOR_ACRESCIMO, ';
$queryProc .= ' 	PS5800_VALOR_IRRF_PS58, ';
$queryProc .= ' 	PS5800_VALOR_GERADO_OD, ';
$queryProc .= ' 	PS5800_VALOR_GERADO_O_2, ';
$queryProc .= ' 				NUMERO_PROCESSAMENTO ';
$queryProc .= ' FROM VW_RECIBO_PAGAMENTO_NET ';
$queryProc .= ' WHERE NUMERO_PROCESSAMENTO = ' . aspas($numeroProcessamento);
$queryProc .= ' AND CODIGO_PRESTADOR = ' . aspas($codigoPrest);

$resProc = jn_query($queryProc);
$rowProc = jn_fetch_object($resProc);

?>


<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Recibo de Pagamento</title>
		<link href="css/principal.css" media="all" rel="stylesheet" type="text/css" />
	</head>
	<body style="text-align:left; font-size: 12px;" leftmargin="10">
		<div align="center">
		  <br /><br />
		  
		  <div style="font-size:12px; font-weight: bold;">
			 Cooperativa Evidente Cooperativa de Trabalho Odontológica de Botucatu
		  </div>
		  
		  <div style="width:780px; text-align: left; ">
			<br /><br />
			<table width="780" border="0" style="font-size:12px;">
				<tr>
				   <td width="600">
						<b>RECIBO DE PAGAMENTO DETALHADO</b>
				   </td>
				   <td width="180">
						<b> Mês: </b> <?php echo $rowProc->MES_ANO_REFERENCIA; ?>
				   </td>
				</tr>
				<tr></tr>
				<tr></tr>
				<tr></tr>
				<tr>
				   <td width="600">
						<b>Nome ou razão social da empresa</b>
				   </td>
				   <td width="180">
						<b> Matricula (CNPJ/CEI)</b>
				   </td>
				</tr>
				<tr>
				   <td width="600">
						COOPERATIVA EVIDENTE COOPERATIVA DE TRAB ODONT DE
				   </td>
				   <td width="180">
						02.947.180/0001-49
				   </td>
				</tr>
			</table>
			<br />
			<br /><br />
		  </div>
		  
		  <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
		  
		  <div style="width:780px; text-align: left; ">
			<br /><br />
			<table width="780" border="0" style="font-size:12px;">
				<tr>
				   <td width="550">
						<b>Nome completo</b>
				   </td>
				   <td width="230">
						<b> Departamento/Seção/Setor</b>
				   </td>
				</tr>
				<tr>
				</tr>
				<tr>
				   <td width="600">
						<?php echo $rowProc->NOME_PRESTADOR; ?>
				   </td>
				   <td width="180">
						<?php echo $rowProc->TIPO_PRESTADOR; ?>
				   </td>
				</tr>
			</table>
			<br />
			<br /><br />
			<table width="780" border="0" style="font-size:12px;">				
				<tr>
				   <td width="550">
						<b>N. INSS </b>
				   </td>
				   <td width="230">
						<b> CPF:</b> <?php echo $rowProc->NUMERO_CPF; ?>
				   </td>
				</tr>
			</table>
			
			<br />
			<br /><br />
			<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
			
			<div style="width:780px; text-align: left; ">
			<br /><br />
			<table width="780" border="0" style="font-size:12px;">
				<tr>
				   <td width="100">
						<b>CÓDIGO</b>
				   </td>
				   <td width="350">
						<b> DESCRIÇÃO</b>
				   </td>
				   <td width="150" align="right">
						<b>VALOR VENCIMENTO</b>
				   </td>
				   <td width="180" align="right">
						<b> VALOR DESCONTO</b>
				   </td>
				</tr>
				<tr>
				</tr>
				<tr>
				   <td width="100">
						&nbsp;
				   </td>
				   <td width="350">
						AUTONOMO CONTR. IN 87
				   </td>
				    <td width="150" align="right">
						<?php echo toMoeda($rowProc->VALOR_GERADO_ODONTO); ?>
				   </td>
				   <td width="180" align="right">						
						&nbsp;
				   </td>
				</tr>
				<tr>
				   <td width="100">
						&nbsp;
				   </td>
				   <td width="350">
						I.R.R.F
				   </td>
				    <td width="150" align="right">
						&nbsp;
				   </td>
				   <td width="180" align="right">						
						<?php echo toMoeda($rowProc->VALOR_IRRF); ?>
				   </td>
				</tr>
				<tr>
				   <td width="100">
						&nbsp;
				   </td>
				   <td width="350">
						I.N.S.S
				   </td>
				    <td width="150" align="right">
						&nbsp;
				   </td>
				   <td width="180" align="right">						
						<?php echo toMoeda($rowProc->VALOR_INSS); ?>
				   </td>
				</tr>
				<tr>
				   <td width="100">
						&nbsp;
				   </td>
				   <td width="350">
						CAPTAÇÃO FINANCEIRA EVIDENTE
				   </td>
				    <td width="150" align="right">
						&nbsp;
				   </td>
				   <td width="180" align="right">						
						<?php echo toMoeda($rowProc->VALOR_CAPITALIZACAO_COOPE); ?>
				   </td>
				</tr>
				<tr>
				   <td width="100">
						&nbsp;
				   </td>
				   <td width="350">
						OUTROS DESCONTOS
				   </td>
				    <td width="150" align="right">
						&nbsp;
				   </td>
				   <td width="180" align="right">						
						<?php echo toMoeda($rowProc->VALOR_TOTAL_DESCONTO); ?>
				   </td>
				</tr>
				<tr>
				   <td width="100">
						&nbsp;
				   </td>
				   <td width="350">
						VALOR ACRESCIMOS (SOBRAS)
				   </td>
				    <td width="150" align="right">
						<?php echo toMoeda($rowProc->VALOR_ACRESCIMO); ?>
				   </td>
				   <td width="180" align="right">						
						&nbsp;
				   </td>
				</tr>
			</table>
			
			<br />
			<br /><br />
			<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
			
			<table width="780" border="0" style="font-size:12px;">				
				<tr>
				   <td width="600" align="right">
						<b> <?php echo toMoeda((($rowProc->VALOR_GERADO_ODONTO + $rowProc->VALOR_ACRESCIMO))); ?> </b>
				   </td>
				   <td width="180" align="right">
						<b> <?php echo toMoeda((($rowProc->VALOR_IRRF + $rowProc->VALOR_INSS + $rowProc->VALOR_CAPITALIZACAO_COOPE + $rowProc->VALOR_TOTAL_DESCONTO))); ?> </b>
				   </td>
				</tr>
			</table>
			
			<br />
			<br /><br />
			<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:400px; " align="right" />
			
			<table width="780" border="0" style="font-size:12px;">				
				<tr>
				   <td width="600" align="right">
						<b> Total a Receber:  </b>
				   </td>
				   <td width="180" align="right">
						<b> <?php echo toMoeda((($rowProc->VALOR_GERADO_ODONTO + $rowProc->VALOR_ACRESCIMO) - ($rowProc->VALOR_IRRF + $rowProc->VALOR_INSS + $rowProc->VALOR_CAPITALIZACAO_COOPE +  $rowProc->VALOR_TOTAL_DESCONTO))); ?> </b>
				   </td>
				</tr>
			</table>
			
			<br />
			<br /><br />
			<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
			
			Recebemos da empresa acima identificada pela prestação de serviços a importancia de: <?php echo toMoeda((($rowProc->VALOR_GERADO_ODONTO + $rowProc->VALOR_ACRESCIMO) - ($rowProc->VALOR_IRRF + $rowProc->VALOR_INSS + $rowProc->VALOR_CAPITALIZACAO_COOPER +  $rowProc->VALOR_TOTAL_DESCONTO))); ?>
			Conforme Discriminativo Acima.
			
			<br />
			<br /><br />
			
			<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
			<br />
			<br /><br />
			<br />
			<br /><br />	
			<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:400px; " align="right" />
			<table width="780" border="0" style="font-size:12px;">				
				<tr>
				   <td width="530" align="right">
						&nbsp;
				   </td>
				   <td width="250" align="right">
						<b> <?php echo $rowProc->NOME_PRESTADOR; ?> </b>
				   </td>
				</tr>
			</table>
			
			<br />
			<br /><br />	
			
		  </div>
		</div>
   </body>
</html>