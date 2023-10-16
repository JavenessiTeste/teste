<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$codigoAssociado  = $_GET['codigoAssociado'];
$mesAno           = $_GET['mesAno'];

$query_proc  = " SELECT NUMERO_GUIA, NOME_ASSOCIADO, DATA_PROCEDIMENTO, NOME_PROCEDIMENTO, VALOR_GERADO, VALOR_COPARTICIPACAO, NOME_PRESTADOR, QUANTIDADE_PROCEDIMENTOS FROM VW_UTILIZACAO_WEB ";	
$query_proc .= " WHERE 1 = 1 ";


if($codigoAssociado != ''){		
	$query_proc .= " AND CODIGO_ASSOCIADO = " . aspas($codigoAssociado);							
}else{
	$query_proc .= " AND CODIGO_TITULAR = " . aspas($_SESSION['codigoIdentificacao']);									
}

if($mesAno != ''){
	$query_proc .= " AND MES_ANO = " . aspas($mesAno);							
} 

$query_proc 	.= " ORDER BY DATA_PROCEDIMENTO DESC";							
$resultQuery    = jn_query($query_proc);

$i=0;

$valorTotalGerado = 0;
$valorTotalCopart = 0;
$quantidadeTotal  = 0;

while($rowProc = jn_fetch_object($resultQuery))
{
	$copart[$i]['Numero Guia']					= $rowProc->NUMERO_GUIA;
	$copart[$i]['Nome Beneficiario']			= $rowProc->NOME_ASSOCIADO;
	$copart[$i]['Data Procedimento']			= $rowProc->DATA_PROCEDIMENTO;
	$copart[$i]['Nome Procedimento']			= $rowProc->NOME_PROCEDIMENTO;
	$copart[$i]['Valor Gerado'] 		        = $rowProc->VALOR_GERADO;
	$copart[$i]['Valor Coparticipação'] 		= $rowProc->VALOR_COPARTICIPACAO;	
	$copart[$i]['Nome Prestador'] 		   		= $rowProc->NOME_PRESTADOR;
	$copart[$i]['Quantidade Procedimentos'] 	= $rowProc->QUANTIDADE_PROCEDIMENTOS;
	
	$valorTotalGerado += $rowProc->VALOR_GERADO;
	$valorTotalCopart += $rowProc->VALOR_COPARTICIPACAO;
	$quantidadeTotal += $rowProc->QUANTIDADE_PROCEDIMENTOS;

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
					<b>RELATORIO DE EXTRATO DE UTILIZA&Ccedil;&Atilde;O - '. $mesAno .'</b>									
				</td>						
			</tr>												
		</table>
		<br>
		<table width="100%" border="0" style="font-size:11px;">
			<tr>
				<td width="7%" align="left" >
					<strong>Numero Guia </strong>
				</td>
				<td width="15%" align="left" >
					<strong>Nome Benef. </strong>
				</td>													
				<td width="10%" align="center" >
					<strong>Data </strong>
				</td>
				<td width="33%" align="left" >
					<strong>Nome Procedimento </strong>
				</td>
				<td width="8%" align="left" >
					<strong>Vlr. Gerado </strong>
				</td>
				<td width="8%" align="left" >
					<strong>Vlr. Copart </strong>
				</td>
				<td width="16%" align="left" >
					<strong>Nome Prestador </strong>
				</td>
				<td width="3%" align="center" >
					<strong>Qtde</strong>
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
						<td width="7%" align="left">										
							' . $copart[$i]['Numero Guia']  . '
						</td>
						<td width="17%" align="left">										
							' . substr($copart[$i]['Nome Beneficiario'],0, 35) . '										
						</td>	
						<td width="8%" align="center">										
							' . SqlToData($copart[$i]['Data Procedimento'])  . '										
						</td>
						<td width="33%" align="left">										
							' . substr($copart[$i]['Nome Procedimento'],0, 42) . '										
						</td>
						<td width="8%" align="left">										
							' . toMoeda($copart[$i]['Valor Gerado']) . '										
						</td>
						<td width="8%" align="left">										
							' . toMoeda($copart[$i]['Valor Coparticipação']) . '										
						</td>
							<td width="16%" align="left">										
							' . substr($copart[$i]['Nome Prestador'],0, 25) . '										
						</td>
						<td width="3%" align="center">										
							' . $copart[$i]['Quantidade Procedimentos'] . '										
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
					Total Valor Gerado: ' . toMoeda($valorTotalGerado) . ' <br>
					Total Valor Coparticipa&ccedil;&atilde;o: ' . toMoeda($valorTotalCopart) . ' <br>
					Quantidade: ' . $quantidadeTotal . ' <br>
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