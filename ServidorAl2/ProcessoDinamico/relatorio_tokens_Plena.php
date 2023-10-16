<?php
require('../lib/base.php');

$dataInicial = $_POST['DATA_INICIAL'];
$dataFinal = $_POST['DATA_FINAL'];
$status = ($_POST['STATUS'] ? $_POST['STATUS'] : $_GET['STATUS']);
$codigoPrestador = $_POST['CODIGO_PRESTADOR'];

$queryTokens = '';
$descricaoRel = '';

if($status == 'A'){

	$queryTokens .= " SELECT CODIGO_ASSOCIADO, TOKEN, DATA_EXPIRACAO, DATA_UTILIZACAO, TABELA, REGISTRO, CODIGO_PRESTADOR FROM VW_TOKENS_VALIDOS_AL2 ";
	$queryTokens .= " WHERE DATA_UTILIZACAO BETWEEN " . aspas($dataInicial) . ' AND ' . aspas($dataFinal);
	
	if($codigoPrestador)
		$queryTokens .= " AND CODIGO_PRESTADOR " . aspas($codigoPrestador);

	$queryTokens .= " ORDER BY DATA_UTILIZACAO ";

	$descricaoRel = ' APROVADOS ';

}elseif($status == 'N'){

	$queryTokens .= " SELECT PERFIL_OPERADOR, CODIGO_IDENTIFICACAO AS CODIGO_PRESTADOR, DATA_NEGATIVA, HORA_NEGATIVA, MOTIVO_NEGATIVA, COD_ASSOC_PREENCHIDO as CODIGO_ASSOCIADO, TOKEN_PREENCHIDO AS TOKEN, TABELA FROM VW_TOKENS_INVALIDOS_AL2 ";
	$queryTokens .= " WHERE DATA_NEGATIVA BETWEEN " . aspas($dataInicial) . ' AND ' . aspas($dataFinal);
	
	if($codigoPrestador)
		$queryTokens .= " PERFIL_OPERADOR = 'PRESTADOR' AND CODIGO_IDENTIFICACAO  " . aspas($codigoPrestador);

	$queryTokens .= " ORDER BY DATA_NEGATIVA ";

	$descricaoRel = ' NEGADOS ';
}

$resTokens = jn_query($queryTokens);
$i = 0;
while($rowTokens = jn_fetch_object($resTokens))
{	
	$tokens[$i]['codigoAssociado']		= $rowTokens->CODIGO_ASSOCIADO;
	$tokens[$i]['numeroToken']			= $rowTokens->TOKEN;
	$tokens[$i]['dataUtilizacao']		= $rowTokens->DATA_UTILIZACAO;
	$tokens[$i]['codigoPrestador'] 		= $rowTokens->CODIGO_PRESTADOR;	
	$tokens[$i]['dataNegativa'] 		= $rowTokens->DATA_NEGATIVA;	
	$tokens[$i]['horaNegativa'] 		= $rowTokens->HORA_NEGATIVA;	
	$tokens[$i]['motivoNegativa'] 		= $rowTokens->MOTIVO_NEGATIVA;		

	$i++;
}

$queryEmpresa = 'SELECT * FROM CFGEMPRESA';
$resEmpresa = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

$quantidadeAssoc = $i;

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
					<img src="../../Site/assets/img/logo_operadora.png" style="height:18%; width:35%;"/>
				</td>											
				<td align="right">
					<b>' . $rowEmpresa->RAZAO_SOCIAL . '</b><br>										
					CNPJ: ' . $rowEmpresa->NUMERO_CNPJ . ' 
				</td>
			</tr>
		</table>	
				
		<table align="center" style="font-size:16px;">
			<tr>														
				<td>		
					<b>RELAT&Oacute;RIO TOKENS ' . $descricaoRel . '</b>																
				</td>						
			</tr>												
		</table>
		<br> ';

if($status == 'A'){	

	$html .= '		
			<br />
			<table width="100%" border="0" style="font-size:11px;" >					
				<tr>							
					<td width="25%" align="left" >
						<strong>Data Utiliza&ccedil;&atilde;o</strong>
					</td>
					<td width="25%" align="center">
						<strong>N&uacute;mero Token</strong>
					</td>				
					<td width="25%" align="right">
						<strong>C&oacute;digo Associado</strong>
					</td>			
					<td width="25%" align="right">
						<strong>C&oacute;digo Prestador</strong>
					</td>													
				</tr>					
			</table>
			
	';
	
	foreach($tokens as $item){
		$html .= '
					<table width="100%" border="0" style="font-size:10px;">
						<tr>
							<td width="25%" align="left">										
								' . SqlToData($item['dataUtilizacao']) . '
							</td>	
							<td width="25%" align="center">										
								' . $item['numeroToken']  . '									
							</td>
							<td width="25%" align="right">										
								' . $item['codigoAssociado']  . '									
							</td>
							<td width="25%" align="right">										
								' . $item['codigoPrestador']  . '											
							</td>							
						</tr>
					</table>		
				
				';			
	}
}elseif($status == 'N'){
		
	$html .= '		
			<br />
			<table width="100%" border="0" style="font-size:11px;" >					
				<tr>							
					<td width="25%" align="left" >
						<strong>Data Negativa</strong>
					</td>
					<td width="25%" align="center">
						<strong>N&uacute;mero Token</strong>
					</td>				
					<td width="25%" align="right">
						<strong>C&oacute;digo Associado</strong>
					</td>			
					<td width="25%" align="right">
						<strong>C&oacute;digo Prestador</strong>
					</td>													
				</tr>					
			</table>
			
	';

	foreach($tokens as $item){
		$html .= '
					<table width="100%" border="0" style="font-size:10px;">
						<tr>
							<td width="25%" align="left">										
								' . SqlToData($item['dataNegativa']) . '
							</td>	
							<td width="25%" align="center">										
								' . $item['numeroToken']  . '									
							</td>
							<td width="25%" align="right">										
								' . $item['codigoAssociado']  . '									
							</td>
							<td width="25%" align="right">										
								' . $item['codigoPrestador']  . '											
							</td>							
						</tr>
					</table>		
				
				';			
	}	

}

//rodape
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
$mpdf=new mPDF('c'); 
$mpdf->WriteHTML($html);
$mpdf->Output();
exit;