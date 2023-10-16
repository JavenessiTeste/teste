<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$codigoEmpresa = isset($_GET['codigoEmpresa']) ? $_GET['codigoEmpresa'] : $_SESSION['codigoIdentificacao'];

$queryAssocEmpresa  = " SELECT * FROM VW_BENEF_EMPR_NET ";
$queryAssocEmpresa .= " WHERE DATA_EXCLUSAO IS NULL AND CODIGO_EMPRESA = " . aspas($codigoEmpresa);
$queryAssocEmpresa .= " ORDER BY NOME_ASSOCIADO ";
$res = jn_query($queryAssocEmpresa);

$i=0;
$benef = Array();
while($row = jn_fetch_object($res)){
	$benef[$i]['CodAssociado']				= $row->CODIGO_ASSOCIADO;
	$benef[$i]['NomeAssociado']				= $row->NOME_ASSOCIADO;
	$benef[$i]['TipoAssociado']				= $row->TIPO_ASSOCIADO;
	$benef[$i]['DataAdmissao']					= $row->DATA_ADMISSAO;
	$benef[$i]['DataDigitacao']				= $row->DATA_DIGITACAO;
	$benef[$i]['DataNascimento']				= $row->DATA_NASCIMENTO;
	$benef[$i]['NomePlano']					= $row->NOME_PLANO;
	$benef[$i]['SituacaoCadastral'] 		    = $row->SITUACAO_CADASTRO;
	$benef[$i]['NúmeroCPF'] 					= $row->NUMERO_CPF;	
	
	$i++;	
}

$queryEmpresa = 'SELECT * FROM CFGEMPRESA';
$resEmpresa = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

$caminhoImg = '<img src="../../Site/assets/img/logo_operadora.png" style="height:5%; width:17%;"/>';

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
					<b>RELAT&Oacute;RIO DE BENEFICI&Aacute;RIOS DA EMPRESA</b>									
				</td>						
			</tr>												
		</table>
		<br>
		<table width="100%" border="0" style="font-size:11px;">
			<tr>
				<td width="10%" align="left" >
					<strong>C&oacute;digo Associado </strong>
				</td>
				<td width="7%" align="left" >
					<strong>Tipo Associado </strong>
				</td>													
				<td width="10%" align="center" >
					<strong>Nome Associado </strong>
				</td>
				<td width="33%" align="left" >
					<strong>Data Admissão </strong>
				</td>
				<td width="8%" align="left" >
					<strong>Data Digitação </strong>
				</td>
				<td width="8%" align="left" >
					<strong>Data Nascimento </strong>
				</td>
				<td width="16%" align="left" >
					<strong>Nome Plano </strong>
				</td>
				<td width="3%" align="center" >
					<strong>Situação Cadastral</strong>
				</td>
				<td width="3%" align="center" >
					<strong>Número CPF</strong>
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
						' . $item['CodAssociado']  . '
					</td>
					<td width="5%" align="left">										
						' . $item['TipoAssociado'] . '										
					</td>	
					<td width="25%" align="center">																
						' . substr($item['NomeAssociado'],0, 40) . '									
					</td>
					<td width="10%" align="left">																
						' . SqlToData($item['DataAdmissao'])  . '												
					</td>
					<td width="10%" align="left">										
						' . SqlToData($item['DataDigitacao'])  . '												
					</td>
					<td width="10%" align="left">										
						' . SqlToData($item['DataNascimento'])  . '												
					</td>
						<td width="20%" align="left">										
						' . substr($item['NomePlano'],0, 25) . '										
					</td>
					<td width="5%" align="center">										
						' . $item['SituacaoCadastral'] . '										
					</td>	
					<td width="5%" align="center">										
						' . $item['NumeroCPf'] . '										
					</td>	
			</table>		
		
		';			
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