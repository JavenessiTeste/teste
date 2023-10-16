<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$numeroRegistro = $_GET['numeroRegistro'];

$queryFat  = ' SELECT MES_ANO_REFERENCIA, NOME_EMPRESA, VALOR_FATURA, VALOR_ADICIONAL, VALOR_CONVENIO FROM PS1020 ';
$queryFat .= ' INNER JOIN PS1010 ON (PS1020.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA) ';
$queryFat .= ' WHERE NUMERO_REGISTRO = ' . aspas($numeroRegistro);
$resFat = jn_query($queryFat);
$rowFat = jn_fetch_object($resFat);
//pr($rowFat,true);

$queryDetFat  = " SELECT PS1021.CODIGO_ASSOCIADO, PS1000.CODIGO_TITULAR, PS1000.NOME_ASSOCIADO, PS1045.NOME_PARENTESCO, PS1021.VALOR_CONVENIO, PS1021.VALOR_FATURA, PS1021.VALOR_ADICIONAL ";
$queryDetFat .= " FROM PS1021 ";
$queryDetFat .= " INNER JOIN PS1000 ON (PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
$queryDetFat .= " INNER JOIN PS1045 ON (PS1000.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ";
$queryDetFat .= " INNER JOIN PS1000 T ON (PS1000.CODIGO_TITULAR = T.CODIGO_ASSOCIADO) ";
$queryDetFat .= " WHERE PS1021.NUMERO_REGISTRO_PS1020 = " . aspas($numeroRegistro);
$queryDetFat .= " ORDER BY T.NOME_ASSOCIADO, PS1000.CODIGO_TITULAR, ps1000.TIPO_ASSOCIADO DESC, PS1000.NOME_ASSOCIADO";

$resDetFat = jn_query($queryDetFat);
$i=0;
$quantTit = 0;
$quantDep = 0;
while($rowDetFat = jn_fetch_object($resDetFat))
{
	$fatura[$i]['Codigo Associado']			= $rowDetFat->CODIGO_ASSOCIADO;
	$fatura[$i]['Codigo Titular']			= $rowDetFat->CODIGO_TITULAR;
	$fatura[$i]['Nome Associado']			= $rowDetFat->NOME_ASSOCIADO;
	$fatura[$i]['Nome Parentesco'] 			= $rowDetFat->NOME_PARENTESCO;	
	$fatura[$i]['Valor Convenio'] 		   	= $rowDetFat->VALOR_CONVENIO;
	$fatura[$i]['Valor Adicional'] 		   	= $rowDetFat->VALOR_ADICIONAL;
	$fatura[$i]['Valor Fatura'] 		   	= $rowDetFat->VALOR_FATURA;	
	$i++;	
	
	if($rowDetFat->NOME_PARENTESCO == 'TITULAR'){
		$quantTit++;
	}else{
		$quantDep++;		
	}
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
					<b>RELATÓRIO DETALHAMENTO DE FATURAS</b>																
				</td>						
			</tr>												
		</table>
		<br>
		<table width="100%" border="0" style="font-size:11px;">
			<tr>
				<td width="23%" align="left" >
					<strong>Nome: </strong>' . $rowFat->NOME_EMPRESA . '
				</td>													
				<td width="17%" align="left" >
					<strong>Competência: </strong>' . $rowFat->MES_ANO_REFERENCIA . '
				</td>
				<td width="20%" align="left" >
					<strong>Vlr. Fat.: </strong>' . toMoeda($rowFat->VALOR_FATURA) . '
				</td>
				<td width="20%" align="left" >
					<strong>Vlr. Conv.: </strong>' . toMoeda($rowFat->VALOR_CONVENIO) . '
				</td>
				<td width="20%" align="left" >
					<strong>Vlr. Adic.: </strong>' . toMoeda($rowFat->VALOR_ADICIONAL) . '
				</td>
			</tr>
		</table>
	';

//corpo do relatório
$html .= '							
		<br />
		<table width="100%" border="0" style="font-size:11px;" >					
			<tr>							
				<td width="50%" align="left" >
					<strong>Nome Associado</strong>
				</td>
				<td width="20%" align="center">
					<strong>Parentesco</strong>
				</td>				
				<td width="15%" align="right">
					<strong>Vlr. Adicional</strong>
				</td>			
				<td width="15%" align="right">
					<strong>Vlr. Fatura</strong>
				</td>													
			</tr>					
		</table>
		
	';

		$i = 0;
		$codAssoc = '';
		$totalFaturaFam = 0;
		
		while($i < $quantidadeAssoc){
			if($codAssoc != $fatura[$i]['Codigo Titular']){
				$codAssoc = $fatura[$i]['Codigo Titular'];
				
				if($i > 0){
					$html .= '	<table width="100%" border="0" style="font-size:10px;">
									<tr>
										<td width="50%" align="left">										
											&nbsp;
										</td>	
										<td width="20%" align="center">										
											&nbsp;
										</td>
										<td width="15%" align="right">										
											&nbsp;
										</td>
										<td width="15%" align="right">	
											<hr/>										
											' . toMoeda($totalFaturaFam) . '										
										</td>							
									</tr>
								</table>	';
					
					$totalFaturaFam = 0;
					
					$html .= '<br>';
				}
				
				$html .= '<br>';
			}
			
			$totalAdicionalFam = ($totalAdicionalFam + $fatura[$i]['Valor Adicional']);
			$totalFaturaFam = ($totalFaturaFam + $fatura[$i]['Valor Fatura']);
			
			$html .= '
				<table width="100%" border="0" style="font-size:10px;">
					<tr>
						<td width="50%" align="left">										
							' . $fatura[$i]['Nome Associado']  . '
						</td>	
						<td width="20%" align="center">										
							' . $fatura[$i]['Nome Parentesco']  . '										
						</td>
						<td width="15%" align="right">										
							' . toMoeda($fatura[$i]['Valor Adicional']) . '										
						</td>
						<td width="15%" align="right">										
							' . toMoeda($fatura[$i]['Valor Fatura']) . '										
						</td>							
					</tr>
				</table>		
			
			';			
			
			$i++;
		}
		
		$html .= '	<table width="100%" border="0" style="font-size:10px;">
						<tr>
							<td width="50%" align="left">										
								&nbsp;
							</td>	
							<td width="20%" align="center">										
								&nbsp;
							</td>
							<td width="15%" align="right">										
								&nbsp;
							</td>
							<td width="15%" align="right">	
								<hr/>										
								' . toMoeda($totalFaturaFam) . '										
							</td>							
						</tr>
					</table>	';

$html .= '					
		<div class="clareador">&nbsp;</div>			
		<table width="100%" border="0" style="font-size:10px;">
			<tr>
				<td>
					<b>QUANT. TITULARES: ' . $quantTit . '</b>
				</td>
			</tr>
			<tr>
				<td>
					<b>QUANT. DEPENDENTES: ' . $quantDep. '</b>
				</td>
			</tr>
			<tr>
				<td>
					<b>QUANT. TOTAL: ' . $quantidadeAssoc. '</b>
				</td>
			</tr>
		</table>				
';

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
$mpdf=new mPDF('c'); 
$mpdf->WriteHTML($html);
$mpdf->Output();
exit;