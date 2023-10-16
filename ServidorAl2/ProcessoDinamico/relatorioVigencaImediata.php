<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$codigoEmpresa  = $_SESSION['codigoIdentificacao'];
$dataInicio     = $_POST['DATA_INICIO_COBRANCA'];
$dataFim        = $_POST['DATA_FIM_COBRANCA'];

$query_proc  = ' SELECT VW_PS1000_ESTIMAT_VLCONVENIO.CODIGO_ASSOCIADO, NOME_ASSOCIADO, VW_PS1000_ESTIMAT_VLCONVENIO.CODIGO_PLANO, VW_PS1000_ESTIMAT_VLCONVENIO.CODIGO_EMPRESA, CODIGO_TABELA_PRECO,';
$query_proc .= ' COALESCE(PRIORIDADE0_PS1021, COALESCE(PRIORIDADE1_VLNOMINAL, COALESCE(PRIORIDADE2_PS1011, PRIORIDADE3_PS1032))) AS VALOR_CONVENIO, '; 
$query_proc .= ' NOME_EMPRESA, NOME_PLANO_FAMILIARES, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA ';
$query_proc .= ' FROM VW_PS1000_ESTIMAT_VLCONVENIO';
$query_proc .= ' INNER JOIN PS1010 ON (PS1010.CODIGO_EMPRESA = VW_PS1000_ESTIMAT_VLCONVENIO.CODIGO_EMPRESA)';
$query_proc .= ' INNER JOIN PS1030 ON (PS1030.CODIGO_PLANO = VW_PS1000_ESTIMAT_VLCONVENIO.CODIGO_PLANO)';
$query_proc .= ' INNER JOIN PS1003 ON (PS1003.CODIGO_ASSOCIADO = VW_PS1000_ESTIMAT_VLCONVENIO.CODIGO_ASSOCIADO)';
$query_proc .= ' WHERE VW_PS1000_ESTIMAT_VLCONVENIO.CODIGO_EMPRESA = ' . aspas($codigoEmpresa) . ' and PS1003.CODIGO_EVENTO = "44" and VW_PS1000_ESTIMAT_VLCONVENIO.DATA_EXCLUSAO IS NULL  ';				

if (!empty($dataInicio)) {
	$query_proc .= ' AND DATA_INICIO_COBRANCA >= '. aspas($dataInicio);
} 

if (!empty($dataFim)) {
	$query_proc .= ' AND DATA_FIM_COBRANCA <= '. aspas($dataFim);
}



$query_proc .= ' ORDER BY VW_PS1000_ESTIMAT_VLCONVENIO.DATA_ADMISSAO';							
$resultQuery = jn_query($query_proc);


$i=0;

$totalPs1032 	= 0;

while($rowProc = jn_fetch_object($resultQuery))
{
	$copart[$i]['Codigo Associado']			= $rowProc->CODIGO_ASSOCIADO;
	$copart[$i]['Nome Associado']	    	= $rowProc->NOME_ASSOCIADO;
	$copart[$i]['Codigo Empresa'] 			= $rowProc->CODIGO_EMPRESA;
	$copart[$i]['Nome Empresa']	    	    = $rowProc->NOME_EMPRESA;
	$copart[$i]['Data Inicial'] 			= $rowProc->DATA_INICIO_COBRANCA;
	$copart[$i]['Data Fim']	    	        = $rowProc->DATA_FIM_COBRANCA;
	$copart[$i]['Nome Plano']	    	    = $rowProc->NOME_PLANO_FAMILIARES;
	$copart[$i]['Codigo Plano'] 		    = $rowProc->CODIGO_PLANO;	
	$copart[$i]['Codigo Tabela Preco'] 		= $rowProc->CODIGO_TABELA_PRECO;
	$copart[$i]['Valor'] 		            = $rowProc->VALOR_CONVENIO * 2;
	
	$total 	+= $rowProc->VALOR_CONVENIO;
	$i++;	
	
}


$queryEmpresa = 'SELECT * FROM CFGEMPRESA';
$resEmpresa = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

$caminhoImg = '<img src="../../Site/assets/img/logo_operadora.png" style="height:5%; width:17%;"/>';

if($rowEmpresa->NUMERO_INSC_SUSEP == '416819') { 						
	$caminhoImg = '<img src="../../Site/assets/img/logo_operadora.png" style="height:0%; width:20%;"/>';
}

$html = '
	<HTML xmlns="http://www.w3.org/1999/xhtml">
		<head>
		   <meta http-equiv="Content-Type" content="text/html; charset=iso8859-1" />               
		   <link href="css/principal.css" media="all" rel="stylesheet" type="text/css" />				
		   <link rel="stylesheet" href="../assets/css/font-awesome.css" />
		</head>		
		<table width="100%" style="font-size:13px;">
			<tr>
				<td align="left">
					' . $caminhoImg . '
				</td>											
				<td align="right">
					<b>' . $rowEmpresa->RAZAO_SOCIAL .'</b><br>										
					CNPJ: ' . $rowEmpresa->NUMERO_CNPJ . ' 
				</td>
			</tr>
		</table>	
				
		<table align="center" style="font-size:16px;">
			<tr>														
				<td>		
					<b>RELAT&Oacute;RIO DE VIG&Ecirc;NCIA IMEDIATA</b>									
				</td>						
			</tr>												
		</table>
		<br>
		<table width="100%" border="0" style="font-size:11px;">
			<tr>
				<td width="12%" align="left" >
					<strong>C&oacute;digo Associado </strong>
				</td>
				<td width="25%" align="left" >
					<strong>Nome Associado </strong>
				</td>
				<td width="12%" align="center" >
					<strong>C&oacute;digo Empresa </strong>
				</td>
				<td width="12%" align="center" >
					<strong>Nome Empresa </strong>
				</td>
				<td width="12%" align="center" >
					<strong>Valor Estimado </strong>
				</td>
				<td width="12%" align="center" >
					<strong>Data In&iacute;cio Cobran&ccedil;a </strong>
				</td>
				<td width="12%" align="center" >
					<strong>Data Fim Cobran&ccedil;a </strong>
				</td>
				<td width="12%" align="center" >
					<strong>C&oacute;d.Plano </strong>
				</td>
				<td width="12%" align="center" >
					<strong>Nome Plano</strong>
				</td>
			</tr>
		</table>
	';

//corpo do relatório

		
		$i = 0;		

		foreach ($copart as $item){
			
			$html .= '
				<table width="100%" border="0" style="font-size:11px;">
					<tr>
						<td width="12%" align="left">										
							' . substr($copart[$i]['Codigo Associado'],0, 35) . '										
						</td>	
						<td width="25%" align="left">										
							' . substr($copart[$i]['Nome Associado'],0, 40) . '										
						</td>
						<td width="12%" align="center">										
							' . substr($copart[$i]['Codigo Empresa'],0, 25) . '										
						</td>
						<td width="12%" align="center">										
							' . substr($copart[$i]['Nome Empresa'],0, 25) . '										
						</td>
						<td width="12%" align="center">										
							' . toMoeda($copart[$i]['Valor']) . '										
						</td>
						<td width="12%" align="center">										
							' . SqlToData($copart[$i]['Data Inicial']) . '										
						</td>		
						<td width="12%" align="center">										
							' . SqlToData($copart[$i]['Data Fim']) . '										
						</td>
			            <td width="12%" align="center">										
							' . substr($copart[$i]['Codigo Plano'],0, 15) . '										
						</td>		
						<td width="12%" align="center">										
							' . substr($copart[$i]['Nome Plano'],0, 30) . '										
						</td>	
				</table>		
			
			';			
			
			$i++;
		}

$html .= '					
		<div class="clareador">&nbsp;</div>			
		<table width="100%" border="0" style="font-size:13px;" align="right" >
			<tr>
				<td>
					Valor Total: ' . toMoeda($total) . ' <br>
				</td>
			</tr>
		</table>';

//rodapé
$html .= '					
		<div class="clareador">&nbsp;</div>			
		<table align="right" style="font-size:10px;">
			<tr>
				<td>
					' . date('d/m/Y') . '
				</td>
			</tr>
		</table>				
	</html>
';

include("../lib/mpdf60/mpdf.php");
$mpdf=new mPDF('c', 'A4-L'); 
$mpdf->WriteHTML($html);
$mpdf->Output();
exit;