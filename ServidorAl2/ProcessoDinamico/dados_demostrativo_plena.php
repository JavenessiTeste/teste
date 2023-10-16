<?php
require('../lib/base.php');


header("Content-Type: text/html; charset=ISO-8859-1",true);

$arrAtend = array();
$ano   = isset($_POST['ANO']) ? $_POST['ANO'] : $_GET['ano'];

$codigoIdentificacao = isset($_GET['codAssociado']) ? $_GET['codAssociado'] : $_SESSION['codigoIdentificacao'];

if ($_SESSION['nomeUsuario'] != '') {
	 $nomeUsuario = $_SESSION['nomeUsuario'];
}else {
	$queryIdentificacao = 'select nome_associado from ps1000 where codigo_associado = ' . aspas($codigoIdentificacao);
	$resIdentificacao = jn_query($queryIdentificacao);
	$rowIdentificacao = jn_fetch_object($resIdentificacao);

	$nomeUsuario = $rowIdentificacao->NOME_ASSOCIADO;
}

$perfilOperador = isset($_GET['perfilOperador']) ? $_GET['perfilOperador'] : $_SESSION['perfilOperador'];



if (!empty($ano)) {
    
	
		if($perfilOperador == 'BENEFICIARIO' || $perfilOperador == 'BENEFICIARIO_CPF') {

			$queryCpf = 'SELECT NUMERO_CPF, FLAG_PLANOFAMILIAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoIdentificacao);
			$resCpf = jn_query($queryCpf);	
			$rowCpf = jn_fetch_object($resCpf);

			$numeroCpf = $rowCpf->NUMERO_CPF;
			$flagPlanoFam = $rowCpf->FLAG_PLANOFAMILIAR;
		

			$queryContratante  = 'select COALESCE(ps1002.nome_contratante, ps1000.nome_associado) as NOME_CONTRATANTE,'; 
			$queryContratante .= 'COALESCE(ps1002.numero_cpf_contratante, ps1000.numero_cpf) as NUMERO_CPF_CONTRATANTE, '; 
			$queryContratante .= 'ps1000.NOME_ASSOCIADO, ps1000.CODIGO_TIPO_CARACTERISTICA '; 
			$queryContratante .= 'from ps1000 ';
			$queryContratante .= 'inner join PS1002 on (ps1000.codigo_associado = ps1002.codigo_associado) ';
			$queryContratante .= 'where ps1000.codigo_associado = ' . aspas($codigoIdentificacao);
			$resContratante    = jn_query($queryContratante);
			$rowContratante    = jn_fetch_object($resContratante);


			$nomeContratante 		= $rowContratante->NOME_CONTRATANTE;
			$cpfContratante  		= $rowContratante->NUMERO_CPF_CONTRATANTE;
			$nomeAssociado   		= $rowContratante->NOME_ASSOCIADO;
			$tipoCaracteristica		= $rowContratante->CODIGO_TIPO_CARACTERISTICA;
			

			$queryDependentes  = 'SELECT SUM(PS1021.VALOR_FATURA) VALOR_FATURA, PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.DATA_NASCIMENTO, PS1000.NUMERO_CPF, PS1045.NOME_PARENTESCO FROM PS1000 ';
			$queryDependentes .= 'left join ps1021 on (ps1000.codigo_associado = ps1021.codigo_associado) ';
			$queryDependentes .= 'left join ps1045 on (ps1000.codigo_parentesco = ps1045.codigo_parentesco) ';
			$queryDependentes .= 'left join ps1020 on (ps1021.numero_registro_ps1020 = ps1020.numero_registro) ';
			$queryDependentes .= ' where ps1000.codigo_titular = ' . aspas($codigoIdentificacao) ;
			$queryDependentes .= ' and CAST(EXTRACT(YEAR FROM ps1020.data_pagamento) AS INTEGER ) = ' . $ano .' and ps1020.data_pagamento is not null and ps1020.data_cancelamento is null ';
			$queryDependentes .= ' group by ps1000.codigo_associado, ps1000.nome_associado, ps1000.data_nascimento, ps1000.numero_cpf, ps1045.nome_parentesco';
			$queryDependentes .= ' order by ps1000.codigo_associado';
			$resDependentes    = jn_query($queryDependentes);

			$i = 0;
			$ArrDep = array();

			while($rowDependentes = jn_fetch_object($resDependentes)){		    	
				$ArrDep[$i]['Data_Nascimento']			= $rowDependentes->DATA_NASCIMENTO;
				$ArrDep[$i]['Nome_Associado']			= $rowDependentes->NOME_ASSOCIADO;
				$ArrDep[$i]['Numero_Cpf']			    = $rowDependentes->NUMERO_CPF;
				$ArrDep[$i]['Valor_Fatura']				= $rowDependentes->VALOR_FATURA;
				$ArrDep[$i]['Nome_Parentesco']			= $rowDependentes->NOME_PARENTESCO;

				$valorTotalAno += $rowDependentes->VALOR_FATURA;

				$i++;			
			}

		}elseif($perfilOperador == 'EMPRESA'){

			$queryContratante  = 'select COALESCE(ps1002.nome_contratante, ps1010.nome_empresa) as NOME_CONTRATANTE,'; 
			$queryContratante .= 'COALESCE(ps1002.numero_cpf_contratante, ps1010.numero_cnpj) as NUMERO_CPF_CONTRATANTE, '; 
			$queryContratante .= 'ps1010.NOME_EMPRESA '; 
			$queryContratante .= 'from ps1010 ';
			$queryContratante .= 'inner join PS1002 on (ps1010.codigo_empresa = ps1002.codigo_empresa) ';
			$queryContratante .= 'where ps1010.codigo_empresa = ' . aspas($codigoIdentificacao);
			$resContratante    = jn_query($queryContratante);
			$rowContratante    = jn_fetch_object($resContratante);


			$nomeContratante 		= $rowContratante->NOME_CONTRATANTE;
			$cpfContratante  		= $rowContratante->NUMERO_CPF_CONTRATANTE;
			$nomeAssociado   		= $rowContratante->NOME_EMPRESA;			
			
			
			$queryEmpresa  = ' SELECT SUM(PS1020.VALOR_PAGO) VALOR_FATURA, PS1010.CODIGO_EMPRESA, PS1010.NOME_EMPRESA, PS1010.NUMERO_CNPJ FROM PS1010 ';			
			$queryEmpresa .= ' left join ps1020 on (ps1020.codigo_empresa = ps1010.codigo_empresa) ';
			$queryEmpresa .= ' where ps1010.codigo_empresa = ' . aspas($codigoIdentificacao) ;
			$queryEmpresa .= ' and CAST(EXTRACT(YEAR FROM ps1020.data_pagamento) AS INTEGER ) = ' . $ano .' and ps1020.data_pagamento is not null and ps1020.data_cancelamento is null ';
			$queryEmpresa .= ' group by ps1010.codigo_empresa, ps1010.nome_empresa, ps1010.numero_cnpj';
			$queryEmpresa .= ' order by ps1010.codigo_empresa';
			$resEmpresa   = jn_query($queryEmpresa);						
			$rowEmpresa = jn_fetch_object($resEmpresa);
			$valorTotalAno = $rowEmpresa->VALOR_FATURA;
		}

		$sqlEmpresa = "SELECT 								
								NOME_EMPRESA,
								NUMERO_CNPJ,
								NUMERO_INSC_SUSEP,
								ENDERECO,
								CIDADE
							FROM CFGEMPRESA ";
		$res = jn_query($sqlEmpresa);	
		if(!($row = jn_fetch_assoc($res))){
			//echo $sqlGrupoContrato;
			exit();
		}

		
		$dadosEmpresa['NOME_OPERADORA']		   	= jn_utf8_encode($row['NOME_EMPRESA']);
		$dadosEmpresa['CNPJ_OPERADORA'] 		= $row['NUMERO_CNPJ'];
		$dadosEmpresa['NUMERO_ANS_OPERADORA']   = $row['NUMERO_INSC_SUSEP'];
		$dadosEmpresa['ENDERECO']		   	    = jn_utf8_encode($row['ENDERECO']);
		$dadosEmpresa['CIDADE']		   			= jn_utf8_encode($row['CIDADE']);	
	
	
	
		if ($perfilOperador == 'BENEFICIARIO'){
			$sqlDadosFaturas = "	SELECT F.DATA_VENCIMENTO, F.DATA_PAGAMENTO, F.VALOR_PAGO AS VALOR_PAGO fROM ps1020 F
						WHERE 	F.data_cancelamento is null and F.codigo_associado = " . aspas($codigoIdentificacao) . "
							AND CAST(EXTRACT(YEAR FROM F.data_pagamento) AS INTEGER ) = '$ano'
							AND F.DATA_PAGAMENTO IS NOT NULL	
						ORDER BY F.DATA_VENCIMENTO ASC ";
		}else if ($perfilOperador == 'EMPRESA'){
			$sqlDadosFaturas = "	SELECT F.DATA_VENCIMENTO, F.DATA_PAGAMENTO, F.VALOR_PAGO AS VALOR_PAGO fROM ps1020 F
						WHERE  F.data_cancelamento is null and F.codigo_empresa = " . aspas($codigoIdentificacao) . "
							AND CAST(EXTRACT(YEAR FROM F.data_pagamento) AS INTEGER ) = '$ano'
							AND F.DATA_PAGAMENTO IS NOT NULL
						ORDER BY F.DATA_VENCIMENTO ASC ";

		}
												

	$res = jn_query($sqlDadosFaturas);

    $i = 0;
   while( $row = jn_fetch_assoc($res)  ) {
			$ArrDadosFatBen[$i]['DATA_VENCIMENTO'] 	= $row['DATA_VENCIMENTO'];
			$ArrDadosFatBen[$i]['DATA_PAGAMENTO'] 	= $row['DATA_PAGAMENTO'];
			$ArrDadosFatBen[$i]['VALOR_PAGO'] 		= $row['VALOR_PAGO'];
			$ArrDadosFatBen[$i]['NOME_ASSOCIADO'] 	= $row['NOME_ASSOCIADO'];
	   
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
    </head>
	

   <body style="text-align:left; font-size: 12px;" leftmargin="10">
	<div align="center">		
	       <div style="width:780px; text-align: right;">
				 
				<table width="780" border="0" style="font-size:12px;">
					<tr >
						<td  style="width:50% !important; ">
						<div>
				      <img style="width:40% !important; float: left; margin-top: 15px;"  src="<?php echo file_exists('../../Site/assets/img/logo_operadora.png') ? '../../Site/assets/img/logo_operadora.png' : '../../Site/assets/img/logo_operadora.jpg';?>" border="0"/>
		     	</div>
						</td>
						<td style="text-align: end;">
						  <br /><br />
						  <strong>
						  <?php
						   echo $dadosEmpresa['NOME_OPERADORA']; 
						  ?>
						  </strong>
						  <br />
						  <strong>
						  <?php 
						  echo $dadosEmpresa['CNPJ_OPERADORA'];
						  ?>
						  </strong>	
						  <br /> <br>
						  <?php 
						  echo 'AV RAIMUNDO PEREIRA DE MAGALHAES,12575';
						  ?>
						  <br/>
						  <?php 
						  echo $dadosEmpresa['CIDADE'];
						  ?>		
						</td>  					  			   
					</tr>
				 </table>
			</div>
	      


      <div style="font-size:12px; font-weight: bold; width: 780px;">
         <br />

		 		DEMONSTRATIVO DE PAGAMENTOS DAS MENSALIDADES DO <?php  echo ($tipoCaracteristica == '10') ? "PLANO ODONTOLÓGICO":"PLANO SA&Uacute;DE";?> CORRESPONDENTES AO ANO DE <?php  echo $ano;  ?>.		 
      </div>

         <div style="width:780px; text-align: left; "> 
		   	<br>
		   	<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
		   	<br /><br>
			<table width="780" border="0" style="font-size:12px;">

				<tr>
					<td>
					  <strong>Nome Contratante ou responsável:</strong>
						  <?php 
						  echo $nomeContratante; 
						  ?>
					  <br /><br />
					  <strong>CPF Contratante ou responsável:</strong>
						  <?php 
						  echo $cpfContratante; 
						  ?>
					<td>
				</tr>
			 </table>

			 <?php
			 	if($perfilOperador == 'BENEFICIARIO' || $perfilOperador == 'BENEFICIARIO_CPF') {
			 ?>
			 
			 <br /><br />
			 <table width="780"  style="font-size:12px; background: #dfdfdf;">	
			 	<tr>
						<td width="116" height="40px">
							  <strong>CPF</strong> 							   
							
					   </td>
						<td width="216">
							  <strong>Nome</strong>
							 		 
					    </td>
					    <td width="116">
							  <strong>Data<br>Nascimento</strong>
							 
					    </td>
					    <td width="166">
							  <strong>Relação de<br> dependência</strong>
							 
					    </td>
					    <td width="166">
							  <strong>Valor Faturamento<br> Anual</strong>
							 
					    </td>
					</tr>	
			 </table>
			<table width="780" border="0" style="font-size:12px;">
				<?php
				
				foreach ($ArrDep as $item) {
					
						echo '
						<tr>
							<td width="116">
								  <br />
								  ' .  $item['Numero_Cpf'] . '
						    </td>
							<td width="216">
								  <br />
								  ' .  substr($item['Nome_Associado'],0,35) . '
						    </td>
						    <td width="116">
								   <br />
								  ' .  SqlToData($item['Data_Nascimento'],0,35) . '
						    </td>
						    <td width="166">
								  <br /> 
								  ' .  $item['Nome_Parentesco'] . '
						    </td>
						    <td width="166">
								  <br />
								 ' . toMoeda($item['Valor_Fatura']) . '
						    </td>
						</tr> ';
				}
				
				?>
			 </table>	

			 <?php
				}elseif($perfilOperador == 'EMPRESA'){			 
			 ?>
				 <br /><br />
			 <table width="780"  style="font-size:12px; background: #dfdfdf;">	
			 	<tr>
						<td width="200" height="40px">
							  <strong>CNPJ</strong> 							   
							
					   </td>
						<td width="400">
							  <strong>Nome</strong>
							 		 
					    </td>
					    <td width="180">
							  <strong>Valor Faturamento<br> Anual</strong>
							 
					    </td>
					</tr>	
			 </table>
			<table width="780" border="0" style="font-size:12px;">
				<?php							
					
						echo '
						<tr>
							<td width="200">
								  <br />
								  ' .  $rowEmpresa->NUMERO_CNPJ . '
						    </td>
							<td width="400">
								  <br />
								  ' .  $rowEmpresa->NOME_EMPRESA . '
						    </td>						    
						    <td width="180">
								  <br />
								 ' . toMoeda($rowEmpresa->VALOR_FATURA) . '
						    </td>
						</tr> ';
				
				?>
			 </table>	

			 <?php
				}
			 ?>
			 
			 <?php  ?>
				 <br>
	         	 <table width="720" border="0" style="font-size:12px;"> 
		     		<tr>
						<td class="lblToValue" style='text-align:right; font-size: 12px;'><?php echo "<b>TOTAL:</b> " .toMoeda($valorTotalAno); ?></td>
					</tr>
				 </table>	
				 <br>
		  		 <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
         </div>

         	
	  <div style="width:780px; text-align: left; ">
      <br />
      	<?php if($flagPlanoFam == 'S'){ ?>
			 Prezado (a) Senhor (a) <?php echo $nomeUsuario; ?>
			<br/><br/>
			 Informamos a seguir os valores arrecadados de seu plano no ano de  <?php  echo $ano;  ?>.
			 <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
		<?php } ?>
	  <br />
      <br /><br />
	  </div>      
      <div style="width:780px; text-align: center; ">
			<strong></br>DADOS DA OPERADORA</strong>
         <br />
		  <br />
		   <br />
			<table width="780" border="0" style="font-size:12px;">
				<tr>
				   <td width="195">
					  <strong>ANS n&deg;</strong>
					  <br /><br />
					  <?php
					  echo $dadosEmpresa['NUMERO_ANS_OPERADORA']; 
					  ?>
				   </td>
				   <td width="195">
					  <strong>Operadora</strong>
					  <br /><br />
					  <?php 
					  echo $dadosEmpresa['NOME_OPERADORA'];
					  ?>
				   </td>
				   <td width="195">
					  <strong>CNPJ</strong>
					  <br /><br />
					  <?php 
					  echo $dadosEmpresa['CNPJ_OPERADORA'];
					  ?>
				   </td>
				   <td width="195">
					  <strong>DATA</strong>
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
	  
      <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
         <strong></br><br />VALORES ARRECADADOS</strong>
         <br /><br />

	  <?php
	  
		  $descLabel1 = 'DATA VENCIMENTO';
		  $descLabel2 = 'DATA PAGAMENTO';
		  $descLabel3 = 'VALOR PAGO';
	  
	  ?>
		 <table width="780" border="0" style="font-size:12px; text-align: center;">               
			<tr>
				<td class="lblToValue"><b><?php echo $descLabel1; ?></b></td>
				<td class="lblToValue"><b><?php echo $descLabel2; ?></b></td>
				<td class="lblToValue" style='text-align:right; font-size: 12px;'><b><?php echo $descLabel3; ?></b></td>
			</tr>
			<?php
			$totalGeral = 0;
			if (count($ArrDadosFatBen)> 0){				
				foreach($ArrDadosFatBen as $item) {
						echo "<tr>";
						echo	"<td class='lblToValue'>". SqlToData($item['DATA_VENCIMENTO'])     ."</td>";
						echo	"<td class='lblValue'  >". SqlToData($item['DATA_PAGAMENTO']) ."</td>";
						echo	"<td class='lblValue' style='text-align:right; font-size: 12px;' >". toMoeda($item['VALOR_PAGO'])          ."</td>";
						echo "</tr>";					
						$totalGeral = $totalGeral + $item['VALOR_PAGO'];
		
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
      <img src="images/ponto_preto.jpg" style="height:2px; background-color:#000000; width:780px; " />
      <br />
      <br />
      <br />
      <br />
      <br />
	 </div>
   </body>
</HTML>