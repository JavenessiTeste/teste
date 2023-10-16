<?php
require('../lib/base.php');
require('../private/autentica.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

$ano   = isset($_POST['ANO']) ? $_POST['ANO'] : $_GET['ANO'];
$associado = $_SESSION['codigoIdentificacao'];

if (!empty($ano)) {

	$queryCpf = 'select numero_cpf, nome_associado from ps1000 where codigo_associado = ' . $associado;
	$resCpf = jn_query($queryCpf);	
	$rowCpf = jn_fetch_object($resCpf);	

	$numeroCpf 		= mask($rowCpf->NUMERO_CPF, '###.###.###-##');
	$nomeAssociado 	= $rowCpf->NOME_ASSOCIADO;


	$sqlGrupoContrato = "SELECT 
							ESP0002.CODIGO_GRUPO_CONTRATO,
							ESP0002.DESCRICAO_GRUPO_CONTRATO, 
							ESP0002.NOME_OPERADORA,
							ESP0002.CNPJ_OPERADORA,
							ESP0002.NUMERO_ANS_OPERADORA
						FROM ESP0002
							INNER JOIN PS1000 ON PS1000.CODIGO_GRUPO_CONTRATO = ESP0002.CODIGO_GRUPO_CONTRATO
						WHERE PS1000.CODIGO_ASSOCIADO = '" . $associado  . "'";

						
	$res = jn_query($sqlGrupoContrato);	
	if($row = jn_fetch_assoc($res)){
		$dadosEmpresa['CODIGO_GRUPO_CONTRATO']    	= $row['CODIGO_GRUPO_CONTRATO'];
		$dadosEmpresa['DESCRICAO_GRUPO_CONTRATO'] 	= $row['DESCRICAO_GRUPO_CONTRATO'];
		$dadosEmpresa['NOME_OPERADORA']				= $row['NOME_OPERADORA'];
		$dadosEmpresa['CNPJ_OPERADORA'] 			= $row['CNPJ_OPERADORA'];
		$dadosEmpresa['NUMERO_ANS_OPERADORA'] 		= $row['NUMERO_ANS_OPERADORA'];	
	}

	

	$sqlEmpresa = "SELECT 								
							NOME_EMPRESA,
							NUMERO_CNPJ,
							NUMERO_INSC_SUSEP
						FROM CFGEMPRESA ";
	$res = jn_query($sqlEmpresa);	
	if(!($row = jn_fetch_assoc($res))){
		//echo $sqlGrupoContrato;
		exit();
	}

	
	$dadosEmpresa['NOME_OPERADORA']		   	= jn_utf8_encode($row['NOME_EMPRESA']);
	$dadosEmpresa['CNPJ_OPERADORA'] 		= $row['NUMERO_CNPJ'];
	$dadosEmpresa['NUMERO_ANS_OPERADORA'] 	= '42346-7';	
	$dadosEmpresa['NOME_OPERADORA'] = 'BENET ADMINISTRADORA DE BENEF&Iacute;CIOS LTDA';		
	
	

	

	$queryTitular = 'select codigo_associado from ps1000 where (DATA_EXCLUSAO IS NULL or (EXTRACT(YEAR FROM DATA_EXCLUSAO) >= ' . (date('Y') - 1) . ')) and numero_cpf = "' . $numeroCpf . '"';
	$resTitular = jn_query($queryTitular);	
	$rowTitular = jn_fetch_object($resTitular);
	$codigoAssociado = $rowTitular->CODIGO_ASSOCIADO;

	$sqlDadosFaturas = "SELECT CODIGO_ASSOCIADO, CODIGO_ANTIGO, NUMERO_CPF, NOME_ASSOCIADO, SUM(VALOR_FATURA) AS TOTAL FROM (
								SELECT 
									PS1000.CODIGO_ASSOCIADO,
									PS1000.CODIGO_ANTIGO,
									PS1000.NUMERO_CPF, 
									PS1000.NOME_ASSOCIADO,									
									(PS1021.VALOR_FATURA + ((PS1021.VALOR_FATURA/PS1020.VALOR_FATURA) * (PS1020.VALOR_PAGO - PS1020.VALOR_FATURA))) as VALOR_FATURA
								FROM 
									PS1020
								INNER JOIN PS1021 ON PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020
								INNER JOIN PS1000 ON PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
								WHERE PS1020.CODIGO_ASSOCIADO = '" . $associado. "' AND Extract(Year From Ps1020.Data_Vencimento) =  '$ano' and ps1021.codigo_titular =  '" . $associado . "'
								
								UNION ALL

								SELECT
									PS1000.CODIGO_ASSOCIADO, PS1000.CODIGO_ANTIGO, PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO, PS1020.VALOR_FATURA
								FROM PS1020
								INNER JOIN PS1000 ON PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
								WHERE
									PS1020.CODIGO_ASSOCIADO = " . aspas($associado) . "
								AND Extract(Year From Ps1020.Data_Vencimento) = '$ano'
								AND PS1020.NUMERO_REGISTRO NOT IN (SELECT NUMERO_REGISTRO_PS1020 FROM PS1021 WHERE PS1021.CODIGO_ASSOCIADO = " . aspas($associado) . ")
							) AS FATURAS
							GROUP BY   FATURAS.CODIGO_ASSOCIADO, FATURAS.CODIGO_ANTIGO,FATURAS.NUMERO_CPF, FATURAS.NOME_ASSOCIADO";

	$res = jn_query($sqlDadosFaturas);

    $i = 0;
   while( $row = jn_fetch_assoc($res)  ) {
	 	   	  
		$ArrDadosFatBen[$i]['CODIGO_ASSOCIADO'] = $row['CODIGO_ASSOCIADO'];
		$ArrDadosFatBen[$i]['CODIGO_ANTIGO'] 	= $row['CODIGO_ANTIGO'];
		$ArrDadosFatBen[$i]['NUMERO_CPF'] 		= mask($row['NUMERO_CPF'], '###.###.###-##');
		$ArrDadosFatBen[$i]['NOME_ASSOCIADO'] 	= $row['NOME_ASSOCIADO'];
		$ArrDadosFatBen[$i]['TOTAL'] 			= $row['TOTAL'];
   
        $i++;
   }
	
} else {
    //mensagem de erro...
	echo '1';
	exit();
}

?> 

<HTML xmlns="http://www.w3.org/1999/xhtml">
    <head>
       
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
       <title>Demonstrativo de pagamentos</title>
        
        <link href="css/principal.css" media="all" rel="stylesheet" type="text/css" />
    </head>
	
	<body style="text-align:center; font-size: 12px; width:780px; margin-left:3px;" leftmargin="10">	
		<div style="width:100% !important; height:185px !important;">
			<img align="left" style="width:25% !important;" src="../../Site/assets/img/logo_demonstrativo_ir.jpg" border="0" />
			<div style="width:75% !important"> </div>
		</div>
		
		<div style="width:780px; text-align: right; margin-left:3px;">		 

			<div style="font-size:11px; font-weight: bold; text-align:left">
				<br />
				DEMONSTRATIVO DE PAGAMENTOS DAS MENSALIDADES DO PLANO DE SA&Uacute;DE CORRESPONDENTES AO ANO DE <?php echo $ano; ?>
			</div>		

			<div style="width:760px; text-align: justify; margin-left:10px;">
				<br /><br />
				Prezado (a) Associado (a) <br /><br />

				Informamos a seguir os valores arrecadados de seu plano de sa&uacute;de contratado atrav&eacute;s do SINDICATO DOS <br>
				ADMINISTRADORES DO RIO GRANDE DO SUL - SINDAERGS, correspondentes ao Contrato Coletivo por Ades&atilde;o que <br>
				a entidade mant&eacute;m junto &agrave; UNIMED PORTO ALEGRE.
				<br /><br />
			</div>
			<img src="images/ponto_preto.jpg" style="height:2px; background-color:#000000; width:780px; " />
			<div style="width:750px; text-align: center; margin-left:6px;">
				</br><strong>DADOS DA ADMINISTRADORA</strong><br /><br />
				
				<table width="750px" border="0" style="font-size:12px; margin-left: 10px;">
					<tr>
					   <td width="95" align="center">
						  <strong>ANS n&deg;</strong>
						  <br /><br />
						  <?php
						  echo $dadosEmpresa['NUMERO_ANS_OPERADORA']; 
						  ?>
					   </td>
					   <td width="350" align="center">
						  <strong>Administradora</strong>
						  <br /><br />
						  <?php 
						  echo $dadosEmpresa['NOME_OPERADORA'];
						  ?>
					   </td>
					   <td width="160" align="left" style="margin-left:10px;">
						  <strong>CNPJ</strong>
						  <br /><br />
						  <?php 
						  echo $dadosEmpresa['CNPJ_OPERADORA'];
						  ?>
					   </td>
					   <td width="135" align="center" style="margin-left:2px;">
						  <strong>DATA DE EMISS&Atilde;O</strong>
						  <br /><br />
						  <?php 
						  echo @date('d/m/Y'); 
						  ?>
					   </td>
					</tr>
				</table>
			</div>
			<br /><br />
			<div style="width:780px; text-align: center; ">
	  
				<img src="images/ponto_preto.jpg" style="height:2px; background-color:#000000; width:780px; " />
				<br>
				<strong></br>DADOS DO BENEFICI&Aacute;RIO </strong>
				<br>
					
				<table width="780px" border="0" style="font-size:12px; margin-left: 40px;">
					<tr>
						<td width="350px">
							<strong></br>N&uacute;mero do C.P.F.: </strong> <?php echo $numeroCpf; ?>
						</td>
						<td width="430px">
							<strong></br>Nome do benefici&aacute;rio: </strong> <?php echo $nomeAssociado; ?>
					 	</td>
					</tr>
				</table>				
				<br>
				<img src="images/ponto_preto.jpg" style="height:2px; background-color:#000000; width:780px; " />
	  
				</br>
				<strong></br>PER&Iacute;ODO DE PAGAMENTO <br> (MAIO A DEZEMBRO / 2022) </br></strong><br /><br />

				<table width="650" border="0" style="font-size:12px; margin-left: 80px;">               
					<tr>
						<td width="200px"><b>CPF</b></td>
						<td width="300px"><b>NOME</b></td>
						<td width="150px" style='text-align:right; font-size: 12px;'><b>VALOR</b></td>
					</tr>
					<?php
					$totalGeral = 0;
					if (count($ArrDadosFatBen)> 0){				
						foreach($ArrDadosFatBen as $item) {
							echo "<tr>";
							echo	"<td width='200px'>". $item['NUMERO_CPF']     ."</td>";
							echo	"<td width='300px'  >". $item['NOME_ASSOCIADO'] ."</td>";
							echo	"<td width='150px' style='text-align:right; font-size: 12px;' >". toMoeda($item['TOTAL'])          ."</td>";
							echo "</tr>";
							$totalGeral = $totalGeral + $item['TOTAL'];
						}
					}			
					?>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					 <tr>
						<td class="lblToValue">&nbsp;</td>
						<td class="lblToValue"></td>
						<td class="lblToValue" style='text-align:right; font-size: 12px;'><?php echo "<b>TOTAL:</b> " .toMoeda($totalGeral); ?></td>
					</tr>
				</table>
			</div>
			<br /><br />
		</div>
	</body>
</html>

<?php

function mask($val, $mask) {
	$maskared = '';
	$k = 0;
	for($i = 0; $i<=strlen($mask)-1; $i++) {
		if($mask[$i] == '#') {
			if(isset($val[$k])) $maskared .= $val[$k++];
		} else {
			if(isset($mask[$i])) $maskared .= $mask[$i];
		}
	}
	return $maskared;
}
?>