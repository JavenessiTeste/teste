<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

/*$codigoEmpresa  = $_SESSION['codigoIdentificacao'];
$dataInicio     = $_POST['DATA_INICIO_COBRANCA'];
$dataFim        = $_POST['DATA_FIM_COBRANCA'];
*/

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
					AAAAAAAAAAAAAAAAAAAAAAAAAAAAA
				</td>											
				<td align="right">
					<b>bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb</b><br>										
					CNPJ: ccccccccccccccccccccccccccc
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
	';

//corpo do relatório

		
			$html .= '
				<table width="100%" border="0" style="font-size:11px;">
					<tr>
						<td width="12%" align="left">										
							$_POST[..
							......................................"COMBO_TIPO_ARQUIVO"]: ' . $_POST["COMBO_TIPO_ARQUIVO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CMB_NUMEROCONTACORRENTE"]: ' . $_POST["CMB_NUMEROCONTACORRENTE"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_TODAS"]: ' . $_POST["CHK_TODAS"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_FILTROSMANUAIS"]: ' . $_POST["OP_FILTROSMANUAIS"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["VENCIMENTO_INICIAL"]: ' . $_POST["VENCIMENTO_INICIAL"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["EMISSAO_INICIAL"]: ' . $_POST["EMISSAO_INICIAL"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST[""]: ' . $_POST[""] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["EMISSAO_FINAL"]: ' . $_POST["EMISSAO_FINAL"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["EDT_CODIGOGRUPOCONTRATO"]: ' . $_POST["EDT_CODIGOGRUPOCONTRATO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST[""]: ' . $_POST[""] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST[""]: ' . $_POST[""] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_FATURASBANCOSELECIONADO"]: ' . $_POST["CHK_FATURASBANCOSELECIONADO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["COMBOTIPOFATURA"]: ' . $_POST["COMBOTIPOFATURA"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CODIGO_BANCO"]: ' . $_POST["CODIGO_BANCO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CAMPO_VALOR_NUMERICO"]: ' . $_POST["CAMPO_VALOR_NUMERICO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_NAOFILTRARCORRECAO"]: ' . $_POST["OP_NAOFILTRARCORRECAO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["EDT_CODIGOGRUPOFATURAMENTO"]: ' . $_POST["EDT_CODIGOGRUPOFATURAMENTO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["DATA_BAIXA_FINAL"]: ' . $_POST["DATA_BAIXA_FINAL"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["DATA_PRORROGACAO_INICIAL"]: ' . $_POST["DATA_PRORROGACAO_INICIAL"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["DATA_PRORROGACAO_FINAL"]: ' . $_POST["DATA_PRORROGACAO_FINAL"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_VALIDAFLAG"]: ' . $_POST["OP_VALIDAFLAG"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_TEXTOGRANDE"]: ' . $_POST["CHK_TEXTOGRANDE"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_GERAR_DATA_EMISSAO_ARQUIVO"]: ' . $_POST["OP_GERAR_DATA_EMISSAO_ARQUIVO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_GERAR_DATA_EMISSAO_BOLETO"]: ' . $_POST["OP_GERAR_DATA_EMISSAO_BOLETO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHB_IMPEDIRINADIMPLENTE"]: ' . $_POST["CHB_IMPEDIRINADIMPLENTE"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CKBDESMARCARFATURA"]: ' . $_POST["CKBDESMARCARFATURA"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_SUBTRAIRVALORDESCARQREMESSA"]: ' . $_POST["OP_SUBTRAIRVALORDESCARQREMESSA"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_TITULOSDESCONTADOS"]: ' . $_POST["OP_TITULOSDESCONTADOS"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["TIPO_GERACAO_COBRANCA"]: ' . $_POST["TIPO_GERACAO_COBRANCA"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["TIPO_ARQUIVO_REMESSA"]: ' . $_POST["TIPO_ARQUIVO_REMESSA"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_TESTE"]: ' . $_POST["OP_TESTE"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_RELATORIO"]: ' . $_POST["CHK_RELATORIO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_DEBITOCONTA"]: ' . $_POST["CHK_DEBITOCONTA"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_AVALISTA"]: ' . $_POST["CHK_AVALISTA"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_IMPRIMENOSSONUMERO"]: ' . $_POST["CHK_IMPRIMENOSSONUMERO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["CHK_PROTESTO"]: ' . $_POST["CHK_PROTESTO"] . 
						'</td>	
					</tr>
					<tr>
						<td width="12%" align="left">										
							$_POST["OP_GERARINFORMEPGTOPORCPF"]: ' . $_POST["OP_GERARINFORMEPGTOPORCPF"] . 
						'</td>	
					</tr>


				</table>		
			';			
			
			$i++;




$html .= '					
		<div class="clareador">&nbsp;</div>	';

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