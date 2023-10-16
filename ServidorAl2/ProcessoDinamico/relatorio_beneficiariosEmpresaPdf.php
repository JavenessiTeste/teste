<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$queryBenefEmpresa  = " SELECT top 500 * FROM VW_BENEF_EMPR_NET ";	
$queryBenefEmpresa .= " WHERE CODIGO_EMPRESA = " . aspas($_SESSION['codigoIdentificacao']);
$resBenefEmpresa    = jn_query($queryBenefEmpresa); 
 
$i=0; 
while($rowBenef = jn_fetch_object($resBenefEmpresa)){	
	$benef[$i]['Codigo']					= $rowBenef->CODIGO_ASSOCIADO;
	$benef[$i]['Nome']						= $rowBenef->NOME_ASSOCIADO;
	$benef[$i]['Numero CPF']				= $rowBenef->NUMERO_CPF;
	$benef[$i]['Data Nascimento']			= SqlToData($rowBenef->DATA_NASCIMENTO);
	$benef[$i]['Plano'] 		        	= $rowBenef->NOME_PLANO_EMPRESAS;
	$benef[$i]['Data Admissao']				= SqlToData($rowBenef->DATA_ADMISSAO);
	$benef[$i]['Situacao'] 		= $rowBenef->SITUACAO_CADASTRO;	

	$i++;
} 

$queryEmpresa = 'SELECT * FROM CFGEMPRESA';
$resEmpresa = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

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
					<img src="../../Site/assets/img/logo_operadora.png" style="height:9%; width:25%;"/>
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
					<b>RELATORIO DE LISTAGEM BENEFICIARIOS</b>									
				</td>						
			</tr>												
		</table>
		<br>
		<table width="100%" border="0" style="font-size:11px;">
			<tr>
				<td width="10%" align="left" >
					<strong>C&oacute;digo</strong>
				</td>
				<td width="25%" align="left" >
					<strong>Nome</strong>
				</td>													
				<td width="10%" align="center" >
					<strong>N&uacute;mero CPF</strong>
				</td>
				<td width="10%" align="left" >
					<strong>Data Nascimento</strong>
				</td>
				<td width="20%" align="left" >
					<strong>Plano</strong>
				</td>
				<td width="10%" align="left" >
					<strong>Data Admiss&atilde;o</strong>
				</td>
				<td width="15%" align="left" >
					<strong>Situa&ccedil;&atilde;o Cadastro</strong>
				</td>				
			</tr>
		</table>
	';

	//corpo do relatório
	
	foreach ($benef as $item){
		$html .= '
			<table width="100%" border="0" style="font-size:11px;">
				<tr>
					<td width="10%" align="left">										
						' . $item['Codigo']  . '
					</td>
					<td width="25%" align="left">										
						' . substr($item['Nome'],0, 25) . '										
					</td>	
					<td width="10%" align="center">										
						' . $item['Numero CPF']  . '										
					</td>
					<td width="10%" align="left">										
						' . $item['Data Nascimento'] . '
					</td>
					<td width="20%" align="left">										
						' . $item['Plano'] . '
					</td>
					<td width="10%" align="left">										
						' . $item['Data Admissao'] . '										
					</td>
					<td width="15%" align="left">										
						' . substr($item['Situacao'],0, 25) . '										
					</td>
				</tr>
			</table>';			

		$i++;
	}

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