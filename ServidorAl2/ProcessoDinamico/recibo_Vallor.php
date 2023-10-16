<?php
require('../lib/base.php');
require('../private/autentica.php');

header("Content-Type: text/html; charset=utf-8",true);


$numeroRegistro = $_GET['numeroRegistro'];

		$sqlEmpresa = "SELECT 								
								NOME_EMPRESA,
								NUMERO_CNPJ,
								NUMERO_INSC_SUSEP,
								ENDERECO,
								ENDERECO_EMAIL,
								TELEFONE_01
							FROM CFGEMPRESA ";
		$res = jn_query($sqlEmpresa);	
		if(!($row = jn_fetch_assoc($res))){
			//echo $sqlGrupoContrato;
			exit();
		}

		
		$dadosEmpresa['NOME_OPERADORA']		   	= jn_utf8_encode($row['NOME_EMPRESA']);
		$dadosEmpresa['CNPJ_OPERADORA'] 		= $row['NUMERO_CNPJ'];
		$dadosEmpresa['NUMERO_ANS_OPERADORA'] 	= $row['NUMERO_INSC_SUSEP'];
		$dadosEmpresa['ENDERECO'] 				= $row['ENDERECO'];
		$dadosEmpresa['TELEFONE_01'] 			= $row['TELEFONE_01'];
		$dadosEmpresa['ENDERECO_EMAIL'] 		= $row['ENDERECO_EMAIL'];	

																	
		$queryTitular  = " SELECT PS1020.CODIGO_ASSOCIADO, PS1000.CODIGO_ANTIGO, PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO, PS1021.VALOR_FATURA, PS1030.NOME_PLANO_FAMILIARES, ";
		$queryTitular .= " 	PS1003.VALOR_FATOR, PS1024.NOME_EVENTO, PS1020.VALOR_PAGO, PS1020.NUMERO_REGISTRO, PS1020.DATA_VENCIMENTO ";
		$queryTitular .= " FROM PS1020 ";
		$queryTitular .= " INNER JOIN PS1021 ON PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020 ";
		$queryTitular .= " INNER JOIN PS1000 ON PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO ";
		$queryTitular .= " INNER JOIN PS1030 ON PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO ";
		$queryTitular .= " LEFT JOIN PS1003 ON PS1000.CODIGO_ASSOCIADO = PS1003.CODIGO_ASSOCIADO ";
		$queryTitular .= " LEFT JOIN PS1024 ON PS1024.CODIGO_EVENTO = PS1003.CODIGO_EVENTO ";
		$queryTitular .= " WHERE PS1020.DATA_PAGAMENTO IS NOT NULL AND TIPO_ASSOCIADO = 'T' AND PS1020.NUMERO_REGISTRO = '" . $numeroRegistro  . "'";
		
		$resTitular = jn_query($queryTitular);	
		$rowTitular = jn_fetch_object($resTitular);

		$nomeAssociado   = $rowTitular->NOME_ASSOCIADO;
		$codigoAssociado = $rowTitular->CODIGO_ASSOCIADO;
		$valorFatura 	  = $rowTitular->VALOR_FATURA;
		$nomePlano       = $rowTitular->NOME_PLANO_FAMILIARES;
		$nomeEvento      = $rowTitular->NOME_EVENTO;
		$valorEvento 	  = $rowTitular->VALOR_FATOR;
		$numeroRegistro  = $rowTitular->NUMERO_REGISTRO;
		$dataVencimento  = $rowTitular->DATA_VENCIMENTO;
		$valorPago      = $rowTitular->VALOR_PAGO;

			

			
		$sqlDadosFaturas = "SELECT PS1020.CODIGO_ASSOCIADO, PS1000.CODIGO_ANTIGO, PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO, PS1021.VALOR_FATURA, PS1030.NOME_PLANO_FAMILIARES, PS1020.VALOR_PAGO
								  FROM 
									PS1020
								  INNER JOIN PS1021 ON PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020
								  INNER JOIN PS1000 ON PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
								  INNER JOIN PS1030 ON PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO
								  WHERE PS1020.DATA_PAGAMENTO IS NOT NULL AND TIPO_ASSOCIADO = 'D' AND PS1020.NUMERO_REGISTRO = '" . $numeroRegistro  . "'";
					

	$res = jn_query($sqlDadosFaturas);

    $i = 0;
    $ArrDadosFatBen = Array();
   while( $row = jn_fetch_assoc($res)  ) {
	   
			$ArrDadosFatBen[$i]['CODIGO_ASSOCIADO'] 			= $row['CODIGO_ASSOCIADO'];
			$ArrDadosFatBen[$i]['CODIGO_ANTIGO'] 				= $row['CODIGO_ANTIGO'];
			$ArrDadosFatBen[$i]['NUMERO_CPF'] 					= $row['NUMERO_CPF'];
			$ArrDadosFatBen[$i]['NOME_ASSOCIADO'] 				= $row['NOME_ASSOCIADO'];
			$ArrDadosFatBen[$i]['DATA_VENCIMENTO'] 				= $row['DATA_VENCIMENTO'];
			$ArrDadosFatBen[$i]['VALOR_FATURA'] 				= $row['VALOR_FATURA'];
			$ArrDadosFatBen[$i]['VALOR_PAGO'] 					= $row['VALOR_PAGO'];
			$ArrDadosFatBen[$i]['NOME_PLANO_FAMILIARES'] 		= $row['NOME_PLANO_FAMILIARES'];

        $i++;
   }


?> 

<HTML xmlns="http://www.w3.org/1999/xhtml">
    <head>
       
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
       <title>Recibo de pagamentos</title>
        
        <link href="css/principal.css" media="all" rel="stylesheet" type="text/css" />
    </head>
	

   <body style="text-align:center; font-size: 12px; width:780px;" leftmargin="10">
		<div align="center">
			<div style="width:90% !important; height:5% !important;">
				<img align="left" style="width:50%; float: left; padding-top: 40px; margin-right: 10% !important;" src="<?php echo file_exists('../../Site/assets/img/logoRecibo.png') ? '../../Site/assets/img/logoLogin.png' : '../../Site/assets/img/logoLogin.jpg';?>" border="0"/>
				<div style="width:80% !important"> </div>
			</div>	
	      

	      <div style="width:780px; text-align: right; margin-left:2px; ">		  

		      <div style="font-size:11px; font-weight: bold; text-align:left; margin-bottom: 1px; border-style: 2px, solid,blue;  ">
		         <br />
				 	<div style="font-size: 20px;">
						Recibo de pagamento					 
				 	</div>
					<br>
					<strong>
						<?php echo $dadosEmpresa['NOME_OPERADORA']; ?>
					</strong>
					<br />
					<strong>
						CNPJ: <?php  echo $dadosEmpresa['CNPJ_OPERADORA']; ?>
					</strong>	
					<br />
					<strong>
						Fone: <?php echo $dadosEmpresa['TELEFONE_01']; ?>
					</strong>			
					<br />
					<strong>
						<?php echo $dadosEmpresa['ENDERECO']; ?>
					</strong>			
					<br />
					<strong>
						<?php echo $dadosEmpresa['ENDERECO_EMAIL']; ?>
					</strong>					
		      </div>

	         <div style="width:780px; text-align: left; "> 
					 <br>
			  		 <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
	         </div>
	      </div>
	      <br>   	
		  	<div style="width:780px; text-align: left; ">
		  		<strong>
				C&oacute;digo da Boleta: <?php  echo $numeroRegistro ?> 
				<br>
				Data Vencimento <?php  echo SqlToData($dataVencimento);  ?> 
				<br>
				Benefici&aacute;rio Titular: <?php echo $_SESSION['nomeUsuario']; ?>
				</strong>
		  		<br />
		  		<br>

	      	<table width="780px" border="0" style="font-size:12px;">               
				
					<tr>
						<td width="780px" style="text-align:left;" colspan="2">
							<?php echo $codigoAssociado  . ' &nbsp;&nbsp; ' . $nomeAssociado; ?>							  
						</td>
					</tr>
					<tr>
						<td width="480px" style="text-align:left;">
							<span style="margin-left:150px;"> Mensalidade: <?php echo $nomePlano; ?> </span>
						</td>
						<td width="300px" style="text-align:right;">
							<?php echo toMoeda($valorFatura); ?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;">
							<span style="margin-left:150px;"> Aditivo: <?php echo $nomeEvento; ?>  </span>
						</td>
						<td style="text-align:right;" >
							<?php echo toMoeda($valorEvento); ?>
						</td>
					</tr>	
					<?php

					$valorTotal = $valorEvento + $valorFatura;

					?>
				 	<tr>
					 	<td style="text-align:right;" >
							<b>TOTAL:</b>
						</td>
						<td style="text-align:right; " >
							<?php echo toMoeda($valorTotal); ?>
						</td>					
					</tr>
				</table>

				<img src="images/ponto_preto.jpg" style="height:2px; background-color:#000000; width:780px; " />

		  
				<table width="780px" border="0" style="font-size:12px;">  	
					<?php
						$totalGeral = 0;
						if (count($ArrDadosFatBen)> 0){				
							foreach($ArrDadosFatBen as $item) {
													
			
									echo "<tr>";
										echo	"<td width='130px' style='text-align:right;'>
													". 	$item['NUMERO_CPF']     ."
												</td>";
										echo	"<td width='650px' style='text-align:left;  colspan='2''>
													". $item['NOME_ASSOCIADO'] ."
												</td>";
									echo "</tr>";	
									echo "<tr>";			
										echo	"<td width='480px' style='text-align:left;'  colspan='2'>
													<span style='margin-left:150px;'> Mensalidade ". $item['NOME_PLANO_FAMILIARES'] ." </span> :
												</td>";
										echo	"<td width='300px' style='text-align:right;'>
													". toMoeda($item['VALOR_FATURA'])          ."
												</td>";
									echo "</tr>";
							}
						}			
						?>
				
				 	<tr>						
						<td style='font-size: 12px; text-align:right;' colspan='3'>
							<?php echo "<b>TOTAL:</b> " .toMoeda($valorPago); ?>
						</td>
					</tr>
				</table>
				<?php
				echo "<td> 
							<strong> RECEBEMOS DO SEGURADO:   " . $nomeAssociado . " A QUANTIA DE  " . toMoeda($valorPago) . "<br> (" . apresentaValorPorExtenso($valorPago) . ") <strong>
					   </td>";
				?>
	      </div>
	      <br /><br />
	      <img src="images/ponto_preto.jpg" style="height:2px; background-color:#000000; width:780px; " />
      </div>
   </body>
</HTML>