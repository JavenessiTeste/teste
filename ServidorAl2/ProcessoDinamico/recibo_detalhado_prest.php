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
$queryProc .= ' 	CODIGO_PRESTADOR,
					NOME_PRESTADOR, 
					CODIGO_ASSOCIADO, 
					VALOR_GERADO, 
					VALOR_PROCEDIMENTO, 
					NOME_ASSOCIADO, 
					CODIGO_BANCO, 
					NUMERO_AGENCIA, 
					NUMERO_CRM, 
					NUMERO_CONTA_CORRENTE, 
					VALOR_BASE_INSS_PF, 
					VALOR_BASE_INSS_PJ, 
					VALOR_CAPITALIZACAO_COOPE, 
					NUMERO_INSC_MUNICIPAL, 
					NUMERO_CNPJ, 
					NUMERO_INSCRICAO_INSS, 
					VALOR_GERADO_TOTAL, 
					RAZAO_SOCIAL_NM_COMPLETO, 
					DATA_PROCEDIMENTO, 
					CODIGO_GLOSA, 
					VALOR_GLOSA, 
					VALOR_COPARTICIPACAO, 
					NUMERO_PROCESSAMENTO, 
					DATA_CONFIRMACAO, 
					TIPO_PRINCIPAL_ACESSORIO, 
					NUMERO_GUIA_OPERADORA, 
					NUMERO_GUIA_PRESTADOR, 
					REFERENCIA_TABELA_SERVICO, 
					ISS_HOSPITAL, 
					ISS_CONS_EXAME, 					
					VALOR_DEPENDENTE_IR, 
					VALOR_IR_ACUMULADO, 
					NUMERO_INSCRICAO_ISS, 
					VALOR_IRRF, 
					VALOR_OUTROS_DESCONTOS, 
					VALOR_TOTAL_GLOSADO, 
					VALOR_LIQUIDO_PAGAR, 
					VALOR_ISS_HOSPITAL, 
					VALOR_ISS_CONSEXAME, 
					DATA_PAGAMENTO, 
					MES_ANO_REFERENCIA, 
					VALOR_IR_ACUMULADO_2, 
					VALOR_INSS, 
					VALOR_DEDUCAO_BASE_INSS, 
					CODIGO_STATUS_PROTOC, 
					VALOR_GERADO_ODONTO, 
					VALOR_TOTAL_DESCONTO, 
					VALOR_GERADO_BENEF_PF, 
					VALOR_GERADO_BENEF_PJ, 
					VALOR_INSS_BENEF_PJ, 
					VALOR_INSS_BENEF_PF, 
					DESCRICAO_COMPLETA_GLOSA, 
					NOME_GLOSA ';
$queryProc .= ' FROM VW_RECIBO_DETALHADO_PREST_AL2 ';
$queryProc .= ' WHERE NUMERO_PROCESSAMENTO = ' . aspas($numeroProcessamento);
$queryProc .= ' AND CODIGO_PRESTADOR = ' . aspas($codigoPrest);

$resProc = jn_query($queryProc);

$ArrGuias = Array();
$i = 0;
while($rowProc = jn_fetch_object($resProc)){
	$ArrGuias[$i]['CODIGO_ASSOCIADO'] = $rowProc->CODIGO_ASSOCIADO;
	$ArrGuias[$i]['NOME_ASSOCIADO'] = $rowProc->NOME_ASSOCIADO;
	$ArrGuias[$i]['VALOR_GERADO'] = $rowProc->VALOR_GERADO;
	$ArrGuias[$i]['NOME_GLOSA'] = $rowProc->NOME_GLOSA;
	$ArrGuias[$i]['NOME_PRESTADOR'] = $rowProc->NOME_PRESTADOR;
	$ArrGuias[$i]['NUMERO_CRM'] = $rowProc->NUMERO_CRM;
	$ArrGuias[$i]['NUMERO_INSC_MUNICIPAL'] = $rowProc->NUMERO_INSC_MUNICIPAL;
	$ArrGuias[$i]['NUMERO_CNPJ'] = $rowProc->NUMERO_CNPJ;
	$ArrGuias[$i]['NUMERO_INSCRICAO_INSS'] = $rowProc->NUMERO_INSCRICAO_INSS;
	$ArrGuias[$i]['CODIGO_BANCO'] = $rowProc->CODIGO_BANCO;
	$ArrGuias[$i]['NUMERO_AGENCIA'] = $rowProc->NUMERO_AGENCIA;
	$ArrGuias[$i]['NUMERO_CONTA_CORRENTE'] = $rowProc->NUMERO_CONTA_CORRENTE;
	$ArrGuias[$i]['VALOR_BASE_INSS_PF'] = $rowProc->VALOR_BASE_INSS_PF;
	$ArrGuias[$i]['VALOR_BASE_INSS_PJ'] = $rowProc->VALOR_BASE_INSS_PJ;
	$ArrGuias[$i]['VALOR_CAPITALIZACAO_COOPE'] = $rowProc->VALOR_CAPITALIZACAO_COOPE;
	$ArrGuias[$i]['VALOR_GERADO_ODONTO'] = $rowProc->VALOR_GERADO_ODONTO;
	
	
	$i++;
}
//pr($ArrGuias,true);
?>


<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Recibo de Pagamento Detalhado</title>
		<link href="css/principal.css" media="all" rel="stylesheet" type="text/css" />
	</head>
	<body style="text-align:left; font-size: 12px;" leftmargin="10">
		<div align="center">
		  <br /><br />
		  
		  <div style="font-size:12px; font-weight: bold;">
			 Cooperativa Evidente Cooperativa de Trabalho Odontológica de Botucatu
			 <br>
			 Botucatu - SP - CNPJ: 02.947.180/0001-49
			 <br>
			 Recibo de Pagamento Detalhado
			 <br>
			 Central de Atendimento (14) 3882-4142
		  </div>
		  
		  <br /><br />		  
		  <div style="width:780px; text-align: left; ">
		  <b>Data: </b><?php echo date('d/m/Y') . ' ' . date('H:i:s')?>
		  
		  <br><br>
		  N° do Recibo:
		  
		  <br><br>
		  <?php echo $codigoPrest . ' - ' . $ArrGuias[0]['NOME_PRESTADOR']; ?>
		  </div>
		  
		  
		  <div style="width:780px; text-align: left; ">
			<br /><br />
			<table width="780" border="0" style="font-size:12px;">
				<?php
					foreach($ArrGuias as $item){
						echo '<tr>';
						echo '	<td width="150">';
						echo 		$item['CODIGO_ASSOCIADO'];
						echo '	</td>';
						echo '	<td width="530">';
						echo 		$item['NOME_ASSOCIADO'];
						echo '	</td>';
						echo '	<td width="100" align="right">';
						echo 		toMoeda($item['VALOR_GERADO']);
						echo '	</td>';
						echo '</tr>';
					}
				?>				
			</table>
			<br />
			<br /><br />
			<table width="780" border="0" style="font-size:12px;">				
				<tr>
				   <td width="680">
						<b>Valor Total Bruto</b>
				   </td>
				   <td width="100" align="right">
						<b>  <?php echo toMoeda($ArrGuias[0]['VALOR_GERADO_ODONTO']); ?> </b>
				   </td>
				</tr>
			</table>
			
			<br />
			<br /><br />
			
			
			
			
			<b><?php echo $ArrGuias[0]['NOME_PRESTADOR']; ?></b>
			
			<br />
			<br /><br />
			

			<br />
			<table width="780" border="0" style="font-size:12px;">				
				<tr>
				   <td width="300" align="left">
						CRO:
				   </td>
				   <td width="480" align="left">
						<?php echo $ArrGuias[0]['NUMERO_CRM']; ?>
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						Inscrição na Prefeitura:
				   </td>
				   <td width="480" align="left">
						<?php echo $ArrGuias[0]['NUMERO_INSC_MUNICIPAL']; ?>
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						&nbsp;&nbsp;&nbsp;&nbsp;CPF / CNPJ
				   </td>
				   <td width="480" align="left">
						<?php echo $ArrGuias[0]['NUMERO_CNPJ']; ?>
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						&nbsp;&nbsp;&nbsp;&nbsp;INSS:
				   </td>
				   <td width="480" align="left">
						<?php echo $ArrGuias[0]['NUMERO_INSCRICAO_INSS']; ?>
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						<b>Dados para Crédito bancário</b>
				   </td>
				   <td width="480" align="left">
						&nbsp;
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						&nbsp;&nbsp;&nbsp;&nbsp;Banco:
				   </td>
				   <td width="480" align="left">
						<?php echo $ArrGuias[0]['CODIGO_BANCO']; ?>
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						&nbsp;&nbsp;&nbsp;&nbsp;Agencia:
				   </td>
				   <td width="480" align="left">
						<?php echo $ArrGuias[0]['NUMERO_AGENCIA']; ?>
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						&nbsp;&nbsp;&nbsp;&nbsp;Conta:
				   </td>
				   <td width="480" align="left">
						<?php echo $ArrGuias[0]['NUMERO_CONTA_CORRENTE']; ?>
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						<b>Para seu controle de contribuição ao INSS:</b>
				   </td>
				   <td width="480" align="left">
						&nbsp;
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						Atendimentos relaizados a Pessoa Fisica:
				   </td>
				   <td width="480" align="left">
						<?php echo toMoeda($ArrGuias[0]['VALOR_BASE_INSS_PF']); ?> 
				   </td>
				</tr>
				<tr>
				   <td width="300" align="left">
						Atendimentos relaizados a Pessoa Juridica:
				   </td>
				   <td width="480" align="left">
						<?php echo toMoeda($ArrGuias[0]['VALOR_BASE_INSS_PJ']); ?>
				   </td>
				</tr>
				<tr></tr>
				<tr></tr>
				<tr></tr>
				<tr>
				   <td width="300" align="left">
						Capitação Financeira:
				   </td>
				   <td width="480" align="left">
						<?php echo toMoeda($ArrGuias[0]['VALOR_CAPITALIZACAO_COOPE']); ?> 
				   </td>
				</tr>
			</table>
			
			<br />
			<br /><br />	
			
		  </div>
		</div>
   </body>
</html>