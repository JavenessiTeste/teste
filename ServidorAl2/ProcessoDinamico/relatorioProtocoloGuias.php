<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$codigoPrestador  = isset($_GET['codigoPrestador']) ? $_GET['codigoPrestador'] : $_SESSION['codigoIdentificacao'];
$numeroProtocolo  = $_GET['numeroProtocolo'];



	$queryTpPrest = 'SELECT REFERENCIA_TABELA_ODONTO FROM PS5002 WHERE CODIGO_PRESTADOR = ' . aspas($codigoPrestador);
	$resTpPrest = jn_query($queryTpPrest);
	$rowTpPrest = jn_fetch_object($resTpPrest);	
	
	if($rowTpPrest->REFERENCIA_TABELA_ODONTO != ''){
		$tipoPrestador = 'ODONTO';
		
		$queryProtocolo  = ' SELECT  ';
		$queryProtocolo .= '  PS5750.CODIGO_PRESTADOR, ';
		$queryProtocolo .= '  PS5000.NOME_PRESTADOR, ';
		$queryProtocolo .= '  PS5750.DATA_ENVIO, ';
		$queryProtocolo .= '  PS5750.MES_ANO, ';
		$queryProtocolo .= '  PS5750.NUMERO_PROTOCOLO, ';
		$queryProtocolo .= '  COUNT(PS5750.NUMERO_REGISTRO) AS QUANT_ODONTO, ';
		$queryProtocolo .= '  SUM(VALOR_COBRADO_TOTAL) AS VALOR_ODONTO ';
		$queryProtocolo .= ' FROM PS5750';
		$queryProtocolo .= ' INNER JOIN PS5000 ON (PS5750.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR) ';
		$queryProtocolo .= ' INNER JOIN VW_SOMA_PS5760_AL2 ON (VW_SOMA_PS5760_AL2.NUMERO_REGISTRO_PS5750 = PS5750.NUMERO_REGISTRO) ';
		$queryProtocolo .= ' WHERE PS5750.CODIGO_PRESTADOR = ' . aspas($codigoPrestador);
		$queryProtocolo .= ' AND PS5750.NUMERO_PROTOCOLO = ' . aspas($numeroProtocolo);
		$queryProtocolo .= ' GROUP BY PS5750.CODIGO_PRESTADOR, PS5000.NOME_PRESTADOR, PS5750.DATA_ENVIO, PS5750.MES_ANO, PS5750.NUMERO_PROTOCOLO';	
		


	}else{
		$tipoPrestador = 'MEDICINA';	
		
		$queryProtocolo  = ' SELECT * FROM VW_PROTOCOLOS_GUIAS_AL2 A ';
		$queryProtocolo .= ' INNER JOIN PS5000 ON (A.CODIGO_PRESTADOR = PS5000.CODIGO_PRESTADOR)';
		$queryProtocolo .= ' WHERE A.CODIGO_PRESTADOR = ' . aspas($codigoPrestador);
		$queryProtocolo .= ' AND A.NUMERO_PROTOCOLO = ' . aspas($numeroProtocolo);
	}
	
	
	$resProtocolo = jn_query($queryProtocolo); 
	
	if($rowProtocolo    = jn_fetch_object($resProtocolo)){
		$item['CODIGO_PRESTADOR']  	= $rowProtocolo->CODIGO_PRESTADOR;
		$item['NOME_PRESTADOR']  	= $rowProtocolo->NOME_PRESTADOR;
		$item['NUMERO_PROTOCOLO']  	= $rowProtocolo->NUMERO_PROTOCOLO;
		$item['DATA_ENVIO']  		= SqlToData($rowProtocolo->DATA_ENVIO);
		$item['MES_ANO']  			= $rowProtocolo->MES_ANO;
		$item['QUANT_CONSULTAS'] 	= $rowProtocolo->QUANT_CONSULTAS;		
		$item['VALOR_CONSULTAS'] 	= toMoeda($rowProtocolo->VALOR_CONSULTAS);
		$item['QUANT_SADT'] 		= $rowProtocolo->QUANT_SADT;		
		$item['VALOR_SADT'] 		= toMoeda($rowProtocolo->VALOR_SADT);
		$item['QUANT_INTERNACOES'] 	= $rowProtocolo->QUANT_INTERNACOES;		
		$item['VALOR_INTERNACOES'] 	= toMoeda($rowProtocolo->VALOR_INTERNACOES);
		$item['QUANT_ODONTO'] 		= $rowProtocolo->QUANT_ODONTO;		
		$item['VALOR_ODONTO'] 		= toMoeda($rowProtocolo->VALOR_ODONTO);
		$item['QUANT_TOTAL'] 		= $rowProtocolo->QUANT_TOTAL;		
		$item['VALOR_TOTAL'] 		= toMoeda($rowProtocolo->VALOR_TOTAL);
	}
	
	$retorno['DADOS_PROTOCOLO'] = $item;	
	
	$queryGuias  = ' SELECT NUMERO_REGISTRO,NUMERO_GUIA_OPERADORA, TIPO_GUIA, CODIGO_ASSOCIADO, NOME_PESSOA, DATA_CADASTRAMENTO  ';						
	$queryGuias .= ' FROM PS5750 WHERE PS5750.NUMERO_PROTOCOLO = '. aspas($numeroProtocolo) . '  AND PS5750.CODIGO_PRESTADOR = ' . aspas($codigoPrestador);		
	$resGuias = jn_query($queryGuias);	
	$i = 0;
	while($rowGuias = jn_fetch_object($resGuias)) {
		$queryValor  = "SELECT SUM(COALESCE(PS5760.VALOR_COBRADO,0)) AS VALOR_COBRADO FROM PS5760 WHERE PS5760.NUMERO_REGISTRO_PS5750 =". aspas($rowGuias->NUMERO_REGISTRO);
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
		
		$valor = $rowValor->VALOR_COBRADO;

		if (trim($valor) == '')
			$valor = 0;
			
		$Guias[$i]['NUMERO_REGISTRO']		= $rowGuias->NUMERO_REGISTRO;
		$Guias[$i]['TIPO_GUIA']				= $rowGuias->TIPO_GUIA;
		$Guias[$i]['CODIGO_ASSOCIADO']		= $rowGuias->CODIGO_ASSOCIADO;
		$Guias[$i]['NOME_PESSOA']			= $rowGuias->NOME_PESSOA;
		$Guias[$i]['DATA_CADASTRAMENTO']	= SqlToData($rowGuias->DATA_CADASTRAMENTO);	
		$Guias[$i]['VALOR_COBRADO']			= toMoeda($valor,false);	
		$Guias[$i]['NUMERO_GUIA']			= $rowGuias->NUMERO_GUIA_OPERADORA;
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
					<b>RELAT&Oacute;RIO DE PROTOCOLOS DE GUIAS - '	. $item['MES_ANO']  . '</b>									
				</td>						
			</tr>
		</table>
		<br>
		<br>
		<hr>
		<table align="left" style="font-size:13px;">
			<div class="clareador">&nbsp;</div>
			<tr>		
				<td width="50%" align="left">
					<b>C&oacute;digo Prestador:</b>										
					' . $item['CODIGO_PRESTADOR']  . '										
				</td>
				<td width="50%" align="left">
					<b>Nome Prestador:</b>										
					' . $item['NOME_PRESTADOR']  . '										
				</td>
			<tr>	
			<tr>		
				<td width="50%" align="left">
					<b>N&uacute;mero Protocolo:</b>										
					' . $item['NUMERO_PROTOCOLO']  . '										
				</td>
				<td width="50%" align="left">
					<b>Data Envio:</b>										
					' . $item['DATA_ENVIO']  . '										
				</td>
			<tr>			
			<tr>
			<td width="50%" align="left">
					<b>Quantidade Consulta:</b>										
					' . $item['QUANT_CONSULTAS'] . '										
				</td>
				<td width="50%" align="left">
					<b>Valor Consulta:</b>										
					' . $item['VALOR_CONSULTAS']  . '
				</td>	
			<tr>
			<tr>
			<td width="50%" align="left">
					<b>Quantidade Sadt:</b>										
					' . $item['QUANT_SADT'] . '										
				</td>
				<td width="50%" align="left">
					<b>Valor Sadt:</b>										
					' . $item['VALOR_SADT']  . '
				</td>	
			<tr>
			<tr>
			<td width="50%" align="left">
					<b>Quantidade Internacao:</b>										
					' . $item['QUANT_INTERNACOES'] . '										
				</td>
				<td width="50%" align="left">
					<b>Valor Internacao:</b>										
					' . $item['VALOR_INTERNACOES']  . '
				</td>	
			<tr>
			<tr>
			<td width="50%" align="left">
					<b>Quantidade Odonto:</b>										
					' . $item['QUANT_ODONTO'] . '										
				</td>
				<td width="50%" align="left">
					<b>Valor Odonto:</b>										
					' . $item['VALOR_ODONTO']  . '
				</td>	
			<tr>
			<tr>
			<td width="50%" align="left">
					<b>Quantidade Total:</b>										
					' . $item['QUANT_TOTAL'] . '										
				</td>
				<td width="50%" align="left">
					<b>Valor Total:</b>										
					' . $item['VALOR_TOTAL']  . '
				</td>	
			<tr>
		</table>
		<hr> 
		<br>
		<br>

		
		<table width="100%" border="0" style="font-size:11px;">
			<tr>
				<td width="7%" align="left" >
					<strong>N&uacute;mero Registro </strong>
				</td>
				<td width="5%" align="center" >
					<strong>Tipo Guia </strong>
				</td>													
				<td width="15%" align="center" >
					<strong>C&oacute;digo Associado </strong>
				</td>
				<td width="30%" align="left" >
					<strong>Nome Pessoa </strong>
				</td>
				<td width="14%" align="left" >
					<strong>Data Cadastramento </strong>
				</td>
				<td width="12%" align="right" >
					<strong>Valor Cobrado </strong>
				</td>
				<td width="16%" align="center" >
					<strong>N&uacute;mero Guia </strong>
				</td>
			</tr>
		</table>
	';

//corpo do relatório

		
		$i = 0;		

		foreach ($Guias as $protocolo){
			
			$html .= '
				<table width="100%" border="0" style="font-size:11px;">
					<tr>
						<td width="7%" align="left">										
							' . $Guias[$i]['NUMERO_REGISTRO']  . '
						</td>
						<td width="5%" align="center">										
							' . $Guias[$i]['TIPO_GUIA'] . '										
						</td>	
						<td width="15%" align="center">										
							' . $Guias[$i]['CODIGO_ASSOCIADO']  . '										
						</td>
						<td width="30%" align="left">										
							' . $Guias[$i]['NOME_PESSOA'] . '										
						</td>
						<td width="14%" align="left">										
							' . $Guias[$i]['DATA_CADASTRAMENTO'] . '										
						</td>
						<td width="12%" align="right">										
							' . $Guias[$i]['VALOR_COBRADO'] . '										
						</td>
						<td width="16%" align="center">										
							' . $Guias[$i]['NUMERO_GUIA'] . '										
						</td>	
				</table>		
			
			';			
			
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