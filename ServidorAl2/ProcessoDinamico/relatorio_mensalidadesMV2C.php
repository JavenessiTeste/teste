<?php
require('../lib/base.php');

$numeroRegistro = $_GET['numeroRegistro'];

$queryFat  = ' SELECT DATA_VENCIMENTO, MES_ANO_REFERENCIA, VALOR_FATURA, VALOR_ADICIONAL, VALOR_CONVENIO, ';

if($_SESSION['perfilOperador'] == 'EMPRESA'){
	$queryFat .= ' NOME_EMPRESA as NOME_ASSOCIADO, "" AS CODIGO_PLANO ';
}else{
	$queryFat .= ' NOME_ASSOCIADO, PS1000.CODIGO_PLANO ';
}

$queryFat .= ' FROM PS1020 ';

if($_SESSION['perfilOperador'] == 'EMPRESA'){
	$queryFat .= ' INNER JOIN PS1010 ON (PS1020.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA)';
}else{
	$queryFat .= ' INNER JOIN PS1000 ON (PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)';
}

$queryFat .= ' WHERE NUMERO_REGISTRO = ' . aspas($numeroRegistro);
$resFat = jn_query($queryFat);
$rowFat = jn_fetch_object($resFat);

$queryDetFat  = " SELECT PS1021.CODIGO_ASSOCIADO, NOME_ASSOCIADO, PS1045.NOME_PARENTESCO, PS1021.VALOR_CONVENIO, PS1021.VALOR_FATURA, PS1021.VALOR_ADICIONAL ";
$queryDetFat .= " FROM PS1021 ";
$queryDetFat .= " INNER JOIN PS1000 ON (PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
$queryDetFat .= " LEFT JOIN PS1045 ON (PS1000.CODIGO_PARENTESCO = PS1045.CODIGO_PARENTESCO) ";
$queryDetFat .= " WHERE PS1021.NUMERO_REGISTRO_PS1020 = " . aspas($numeroRegistro);
$queryDetFat .= " ORDER BY PS1021.CODIGO_ASSOCIADO";

$resDetFat = jn_query($queryDetFat);
$i=0;
while($rowDetFat = jn_fetch_object($resDetFat))
{
	$fatura[$i]['Codigo Associado']			= $rowDetFat->CODIGO_ASSOCIADO;
	$fatura[$i]['Nome Associado']			= $rowDetFat->NOME_ASSOCIADO;
	$fatura[$i]['Nome Parentesco'] 			= $rowDetFat->NOME_PARENTESCO;	
	$fatura[$i]['Valor Convenio'] 		   	= $rowDetFat->VALOR_CONVENIO;
	$fatura[$i]['Valor Adicional'] 		   	= $rowDetFat->VALOR_ADICIONAL;
	$fatura[$i]['Valor Fatura'] 		   	= $rowDetFat->VALOR_FATURA;	
	$i++;	
}

$queryEmpresa = 'SELECT * FROM CFGEMPRESA';
$resEmpresa = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

$valorTotal = 0;
$html = '
	<HTML xmlns="http://www.w3.org/1999/xhtml">
		<head>
		   <meta http-equiv="Content-Type" content="text/html; charset=iso8859-1" />               
		   <link href="css/principal.css" media="all" rel="stylesheet" type="text/css" />				
		   <link rel="stylesheet" href="css/font-awesome.css" />
		</head>				
	
		<table width="100%" style="font-size:13px;">
			<tr>
				<td align="left">						
					<img src="../../Site/assets/img/logo_operadora.png" style="height:20%; width:20%;"/>
				</td>											
				<td align="right">
					<b>' . $rowEmpresa->RAZAO_SOCIAL . '</b><br>
					' . $rowEmpresa->ENDERECO . '<br>
					' . $rowEmpresa->TELEFONE_01 . '<br>
					' . $rowEmpresa->CEP . ' – ' . $rowEmpresa->CIDADE . ' – ' . $rowEmpresa->ESTADO . ' <br>
					CNPJ: ' . $rowEmpresa->NUMERO_CNPJ . ' 
				</td>
			</tr>
		</table>
		<br>
		<table align="center" style="font-size:13px;">
			<tr>														
				<td>		
					<b>RELATÓRIO DETALHAMENTO DE FATURAS</b>																
				</td>						
			</tr>												
		</table>		
		<br>
		<table width="100%" border="0" style="font-size:9px;">
			<tr>
				<td width="40%" align="left" >
					<strong>Nome </strong>
				</td>													
				<td width="15%" align="right" >
					<strong>Mês Ano Competência </strong>
				</td>
				<td width="15%" align="right" >
					<strong>Vlr. Fatura </strong>
				</td>
				<td width="15%" align="right" >
					<strong>Vlr. Convênio </strong>
				</td>
				<td width="15%" align="right" >
					<strong>Vlr. Adicional </strong>
				</td>
			</tr>
			<tr>
				<td width="40%" align="left" >
					' . $rowFat->NOME_ASSOCIADO . '
				</td>													
				<td width="15%" align="right" >
					' . $rowFat->MES_ANO_REFERENCIA . '
				</td>
				<td width="15%" align="right" >
					' . toMoeda($rowFat->VALOR_FATURA) . '
				</td>
				<td width="15%" align="right" >
					' . toMoeda($rowFat->VALOR_CONVENIO) . '
				</td>
				<td width="15%" align="right" >
					' . toMoeda($rowFat->VALOR_ADICIONAL) . '
				</td>
			</tr>
		</table>
	';

//corpo do relatório
$html .= '							
		<br /><br />
		<table width="100%" border="0" style="font-size:12px;" >					
			<tr>							
				<td width="15%">
					<strong>Matricula</strong>
				</td>
				<td width="40%" align="left" >
					<strong>Nome Associado</strong>
				</td>
				<td width="15%" align="right">
					<strong>Vlr. Mensalidade</strong>
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

		foreach($fatura as $item){												
			
			$html .= '
				<table width="100%" border="0" style="font-size:10px;">
					<tr>
						<td width="15%" align="left">										
							' . $item['Codigo Associado']  . '
						</td>	
						<td width="40%" align="left">										
							' . $item['Nome Associado']  . '							
						</td>							
						<td width="15%" align="right">										
							' . toMoeda($item['Valor Convenio']) . '										
						</td>
						<td width="15%" align="right">										
							' . toMoeda($item['Valor Adicional']) . '										
						</td>
						<td width="15%" align="right">										
							' . toMoeda($item['Valor Fatura']) . '										
						</td>							
					</tr>
				</table>		
			
			';
			
			if($item['Valor Adicional'] != 0){
				
				$queryCopart  = " SELECT VW_DETALHAMENTO_COPART_NET.NOME_PRESTADOR, VW_DETALHAMENTO_COPART_NET.CODIGO_PROCEDIMENTO, VW_DETALHAMENTO_COPART_NET.NOME_PROCEDIMENTO, ";
				$queryCopart .= " VW_DETALHAMENTO_COPART_NET.QUANTIDADE_PROCEDIMENTOS, VW_DETALHAMENTO_COPART_NET.DATA, VW_DETALHAMENTO_COPART_NET.VALOR_EVENTO_PS1023 as VALOR ";
				$queryCopart .= " FROM VW_DETALHAMENTO_COPART_NET ";
				$queryCopart .= " INNER JOIN PS1000 ON (VW_DETALHAMENTO_COPART_NET.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
				$queryCopart .= " WHERE 1 = 1";
				$queryCopart .= " AND VW_DETALHAMENTO_COPART_NET.VALOR_EVENTO_PS1023 <> '0' ";
				$queryCopart .= " AND NUMERO_REGISTRO_PS1020 = " . aspas($numeroRegistro);
				$queryCopart .= " AND CODIGO_ASSOCIADO = " . aspas($item['Codigo Associado']);

				$resCopart = jn_query($queryCopart);
				$valorTotal = 0;
				while($rowCopart = jn_fetch_object($resCopart))
				{
					$copart[$iC]['Nome Prestador']				= $rowCopart->NOME_PRESTADOR;	
					$copart[$iC]['Código Procedimento']			= $rowCopart->CODIGO_PROCEDIMENTO;
					$copart[$iC]['Descrição Procedimento'] 		= $rowCopart->NOME_PROCEDIMENTO;
					$copart[$iC]['Data Evento']         		= $rowCopart->DATA;
					$copart[$iC]['Valor Evento'] 		   		= $rowCopart->VALOR;
					$copart[$iC]['Quantidade Procedimentos'] 	= $rowCopart->QUANTIDADE_PROCEDIMENTOS;	
					
					$valorTotal = $valorTotal + $rowCopart->VALOR;

					$iC++;	
				}
				
				//corpo do relatório
				$html .= '															
						<table width="95%" border="0" style="font-size:12px;" align="right" >
							<tr>																
								<td width="25%">
									<strong>Prestador </strong>
								</td>
								<td width="15%">
									<strong>Código </strong>
								</td>
								<td width="30%">
									<strong>Descrição </strong>
								</td>																	
								<td width="10%">
									<strong>Quantidade</strong>
								</td>							
								<td width="10%" >
									<strong>Data </strong>
								</td>							
								<td width="10%" align="right">
									<strong>Valor </strong>
								</td>					
							</tr>					
						</table>
						';
						
						foreach($copart as $coparItem){
							
							$html .= '
								<table width="95%" border="0" style="font-size:12px;" align="right" >
									<tr>											
										<td width="25%" align="left">										
											' . substr($coparItem['Nome Prestador'],0,25)  . '										
										</td>
										<td width="15%" align="left">										
											' . trim($coparItem['Código Procedimento'],' ')  . '										
										</td>							
										<td width="30%" align="left">										
											' . substr($coparItem['Descrição Procedimento'],0,25) . '										
										</td>
										<td width="10%" align="center">										
											' . trim($coparItem['Quantidade Procedimentos'],' ') . '
										</td>
										<td width="10%" align="left">										
											' . SqlToData($coparItem['Data Evento'],' ') . '										
										</td>																		
										<td width="10%" align="right">										
											' . toMoeda($coparItem['Valor Evento']) . '										
										</td>									
									</tr>
								</table>		
							
							';												

							$iC++;
						}
			}
			
			$i++;
		}		

//Totalizador
$html .= '					
		<div class="clareador">&nbsp;</div>			
		<table width="100%" border="0" style="font-size:13px;" align="right" >
			<tr>
				<td>
					Total Mensalidade: ' . toMoeda($rowFat->VALOR_CONVENIO) . ' <br>
					Total Adicionais: ' . toMoeda($rowFat->VALOR_ADICIONAL) . ' <br>
					Total Geral: ' . toMoeda($rowFat->VALOR_FATURA) . '
				</td>
			</tr>
		</table>';
		
//rodapé
$html .= '							
		<div class="clareador">&nbsp;</div>			
		<table align="right">
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