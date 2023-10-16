<?php
require('../lib/base.php');

require('../private/autentica.php');


header("Content-Type: text/html; charset=ISO-8859-1",true);


$arrAtend = array();
$ano   = isset($_POST['ANO']) ? $_POST['ANO'] : '';
$valorTotalAno = 0;

if (!empty($ano)) {
    
	if($_SESSION['codigoSmart'] == '3389'){

		$sqlGrupoContrato = "SELECT 
								ESP0002.CODIGO_GRUPO_CONTRATO,
								ESP0002.DESCRICAO_GRUPO_CONTRATO, 
								ESP0002.NOME_OPERADORA,
								ESP0002.CNPJ_OPERADORA,
								ESP0002.NUMERO_ANS_OPERADORA
							FROM ESP0002
								INNER JOIN PS1000 ON PS1000.CODIGO_GRUPO_CONTRATO = ESP0002.CODIGO_GRUPO_CONTRATO
							WHERE PS1000.CODIGO_ASSOCIADO = '" . $_SESSION['codigoIdentificacao']  . "'";

							
		$res = jn_query($sqlGrupoContrato);	
		if(!($row = jn_fetch_assoc($res))){
			//echo $sqlGrupoContrato;
			exit();
		}
		
		$dadosEmpresa['CODIGO_GRUPO_CONTRATO']    	= $row['CODIGO_GRUPO_CONTRATO'];
		$dadosEmpresa['DESCRICAO_GRUPO_CONTRATO'] 	= $row['DESCRICAO_GRUPO_CONTRATO'];
		$dadosEmpresa['NOME_OPERADORA']				= $row['NOME_OPERADORA'];
		$dadosEmpresa['CNPJ_OPERADORA'] 			= $row['CNPJ_OPERADORA'];
		$dadosEmpresa['NUMERO_ANS_OPERADORA'] 		= $row['NUMERO_ANS_OPERADORA'];	

		$sqlGrupoContrato = "SELECT 
								ESP0002.CODIGO_GRUPO_CONTRATO,
								ESP0002.DESCRICAO_GRUPO_CONTRATO, 
								ESP0002.NOME_OPERADORA,
								ESP0002.CNPJ_OPERADORA,
								ESP0002.NUMERO_ANS_OPERADORA
							FROM ESP0002
							WHERE ESP0002.CODIGO_GRUPO_CONTRATO = '400'";
							
		$res = jn_query($sqlGrupoContrato);	
		if(!($row = jn_fetch_assoc($res))){
			//echo $sqlGrupoContrato;
			exit();
		}
		$dadosEmpresa['CODIGO_GRUPO_CONTRATO_VM']    	= $row['CODIGO_GRUPO_CONTRATO'];
		$dadosEmpresa['DESCRICAO_GRUPO_CONTRATO_VM'] 	= $row['DESCRICAO_GRUPO_CONTRATO'];
		$dadosEmpresa['NOME_OPERADORA_VM']		   		= $row['NOME_OPERADORA'];
		$dadosEmpresa['CNPJ_OPERADORA_VM'] 		   		= $row['CNPJ_OPERADORA'];
		$dadosEmpresa['NUMERO_ANS_OPERADORA_VM'] 	   	= $row['NUMERO_ANS_OPERADORA'];	

		
		$queryCpf = 'select numero_cpf, codigo_grupo_contrato, nome_associado from ps1000 where codigo_associado = ' . $_SESSION['codigoIdentificacao'];
		$resCpf = jn_query($queryCpf);	
		$rowCpf = jn_fetch_object($resCpf);

		
		$numeroCpf = $rowCpf->NUMERO_CPF;
		$grupoContrato = $rowCpf->CODIGO_GRUPO_CONTRATO;
		$nomeTitular = $rowCpf->NOME_ASSOCIADO;

	
	}else{
		if($_SESSION['perfilOperador'] == 'BENEFICIARIO' || $_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF') {

			$queryCpf = 'select numero_cpf, flag_planofamiliar from ps1000 where codigo_associado = ' . $_SESSION['codigoIdentificacao'];
			$resCpf = jn_query($queryCpf);	
			$rowCpf = jn_fetch_object($resCpf);

			$numeroCpf = $rowCpf->NUMERO_CPF;
			$flagPlanoFam = $rowCpf->FLAG_PLANOFAMILIAR;
		}


		if($_SESSION['codigoSmart'] == '3423'){
		   $queryContratante  = 'select COALESCE(ps1002.nome_contratante, ps1000.nome_associado) as nome_contratante,'; 
		   $queryContratante .= 'COALESCE(ps1002.numero_cpf_contratante, ps1000.numero_cpf) as numero_cpf_contratante, '; 
		   $queryContratante .= 'ps1000.nome_associado '; 
		   $queryContratante .= 'from ps1000 ';
		   $queryContratante .= 'inner join PS1002 on (ps1000.codigo_associado = ps1002.codigo_associado) ';
		   $queryContratante .= 'where ps1000.codigo_associado = ' . $_SESSION['codigoIdentificacao'];
		   $resContratante    = jn_query($queryContratante);
		   $rowContratante    = jn_fetch_object($resContratante);


		   $nomeContratante = $rowContratante->NOME_CONTRATANTE;
		   $cpfContratante  = $rowContratante->NUMERO_CPF_CONTRATANTE;
		   $nomeAssociado   = $rowContratante->NOME_ASSOCIADO;
		  


		}

		if($_SESSION['codigoSmart'] == '3423'){
		   $queryDependentes  = 'select sum(ps1021.valor_fatura) VALOR_FATURA, ps1000.codigo_associado, ps1000.nome_associado, ps1000.data_nascimento, ps1000.numero_cpf, ps1045.nome_parentesco from ps1000 ';
		   $queryDependentes .= 'left join ps1021 on (ps1000.codigo_associado = ps1021.codigo_associado) ';
		   $queryDependentes .= 'left join ps1045 on (ps1000.codigo_parentesco = ps1045.codigo_parentesco) ';
		   $queryDependentes .= 'where ps1000.codigo_titular = ' . $_SESSION['codigoIdentificacao'] ;
		   $queryDependentes .= ' group by ps1000.codigo_associado, ps1000.nome_associado, ps1000.data_nascimento, ps1000.numero_cpf, ps1045.nome_parentesco';
		   $queryDependentes .= ' order by ps1000.codigo_associado';
		   $resDependentes    = jn_query($queryDependentes);

		   $i = 0;
		   $ArrDep = array();

		    while($rowDependentes = jn_fetch_object($resDependentes)){		    	
				$ArrDep[$i]['Data_Nascimento']				= $rowDependentes->DATA_NASCIMENTO;
				$ArrDep[$i]['Nome_Associado']			    = $rowDependentes->NOME_ASSOCIADO;
				$ArrDep[$i]['Numero_Cpf']			        = $rowDependentes->NUMERO_CPF;
				$ArrDep[$i]['Valor_Fatura']					= $rowDependentes->VALOR_FATURA;
				$ArrDep[$i]['Nome_Parentesco']			    = $rowDependentes->NOME_PARENTESCO;


				$valorTotalAno += $rowDependentes->VALOR_FATURA;

			$i++;	
				
			}

		}	


		if(retornaValorConfiguracao('BANCO_ADMINISTRADORA') == 'SIM'){

			$sqlGrupoContrato = "SELECT 
									ESP0002.CODIGO_GRUPO_CONTRATO,
									ESP0002.DESCRICAO_GRUPO_CONTRATO, 
									ESP0002.NOME_OPERADORA,
									ESP0002.CNPJ_OPERADORA,
									ESP0002.NUMERO_ANS_OPERADORA
								FROM ESP0002
									INNER JOIN PS1000 ON PS1000.CODIGO_GRUPO_CONTRATO = ESP0002.CODIGO_GRUPO_CONTRATO
								WHERE PS1000.CODIGO_ASSOCIADO = '" . $_SESSION['codigoIdentificacao']  . "'";

								
			$res = jn_query($sqlGrupoContrato);	
			if($row = jn_fetch_assoc($res)){
				$dadosEmpresa['CODIGO_GRUPO_CONTRATO']    	= $row['CODIGO_GRUPO_CONTRATO'];
				$dadosEmpresa['DESCRICAO_GRUPO_CONTRATO'] 	= $row['DESCRICAO_GRUPO_CONTRATO'];
				$dadosEmpresa['NOME_OPERADORA']				= $row['NOME_OPERADORA'];
				$dadosEmpresa['CNPJ_OPERADORA'] 			= $row['CNPJ_OPERADORA'];
				$dadosEmpresa['NUMERO_ANS_OPERADORA'] 		= $row['NUMERO_ANS_OPERADORA'];	
			}

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
		$dadosEmpresa['NUMERO_ANS_OPERADORA'] 	= $row['NUMERO_INSC_SUSEP'];	

		if($_SESSION['codigoSmart'] == '4298'){//Benet Saude
			$dadosEmpresa['NOME_OPERADORA'] = 'BENET ADMINISTRADORA DE BENEF&Iacute;CIOS LTDA';		
		}
	}

	
	
	if($grupoContrato == '50' && $_SESSION['codigoSmart'] == '3389'){//Vidamax grupo 50
	
		$queryTitular = 'select CODIGO_TITULAR from ps1000 where (DATA_EXCLUSAO IS NULL or (EXTRACT(YEAR FROM DATA_EXCLUSAO) >= ' . (date('Y') - 1) . ')) and numero_cpf = "' . $numeroCpf . '"';
		
		$resTitular = jn_query($queryTitular);	
		$rowTitular = jn_fetch_object($resTitular);

		$codigoAssociado = $rowTitular->CODIGO_TITULAR;		
		
		$sqlDadosFaturas = "SELECT 
								PS1000.CODIGO_ANTIGO,
								PS1000.NUMERO_CPF, 
								PS1000.NOME_ASSOCIADO,
								SUM(CASE 
									WHEN ps1000.TIPO_ASSOCIADO = 'T' THEN PS1021.VALOR_FATURA - (coalesce(VW_INFORME_PAGAMENTO_SUBCON.valor_evento,0)) 
									ELSE PS1021.VALOR_FATURA
								END) AS TOTAL  
							FROM 
								PS1020
							INNER JOIN PS1021 ON PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020
							INNER JOIN PS1000 ON PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
							LEFT OUTER JOIN VW_INFORME_PAGAMENTO_SUBCON ON (PS1021.NUMERO_REGISTRO_PS1020 = VW_INFORME_PAGAMENTO_SUBCON.NUMERO_REGISTRO_PS1020)
							WHERE 
									PS1000.CODIGO_TITULAR =  '" . $codigoAssociado . "' 
								AND Extract(Year From Ps1020.Data_Pagamento) =  '$ano'
								AND (
										(PS1021.VALOR_ADICIONAL <> PS1021.VALOR_FATURA) or
										(PS1020.NUMERO_REGISTRO IN (
											SELECT
												PS1029.NUMERO_REGISTRO_PS1020
											FROM PS1029
											WHERE   PS1029.NUMERO_REGISTRO_PS1020 = PS1020.NUMERO_REGISTRO
												AND (PS1029.CODIGO_EVENTO = '1016' OR PS1029.CODIGO_EVENTO = '7')
											)
										)
									)
								GROUP BY   PS1000.CODIGO_ANTIGO,PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO";
									
	}elseif($_SESSION['codigoSmart'] == '3555'){//Evidente
		$queryTitular = '	select 
								codigo_associado 
							from ps1000 
							where 
									(DATA_EXCLUSAO IS NULL or (EXTRACT(YEAR FROM DATA_EXCLUSAO) >= ' . (date('Y') - 1) . ')) 
								and numero_cpf = ' . aspas($numeroCpf) . ' 
							order by DATA_EXCLUSAO ';
		
		$resTitular = jn_query($queryTitular);	
		$rowTitular = jn_fetch_object($resTitular);
		$codigoAssociado = $rowTitular->CODIGO_ASSOCIADO;
			
		
		$sqlDadosFaturas = "SELECT 
							CODIGO_ANTIGO,
							NUMERO_CPF, 
							NOME_ASSOCIADO,
							SUM(VALOR_FATURA) AS TOTAL  
						FROM VW_DEMONSTR_IR_DMED						
						WHERE CODIGO_TITULAR =  " . aspas($codigoAssociado) . " AND ANO_PAGAMENTO =  '$ano'
						GROUP BY CODIGO_ANTIGO, NUMERO_CPF, NOME_ASSOCIADO";							
				
	}elseif($_SESSION['codigoSmart'] == '3423'){//Plena
	
		if ($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
			$sqlDadosFaturas = "	SELECT F.DATA_VENCIMENTO, F.DATA_PAGAMENTO, F.VALOR_PAGO AS VALOR_PAGO fROM ps1020 F
						WHERE 	F.data_cancelamento is null and F.codigo_associado = " . aspas($_SESSION['codigoIdentificacao']) . "
							AND CAST(EXTRACT(YEAR FROM F.data_pagamento) AS INTEGER ) = '$ano'
							AND F.DATA_PAGAMENTO IS NOT NULL	
						ORDER BY F.DATA_VENCIMENTO ASC ";
		}else if ($_SESSION['perfilOperador'] == 'EMPRESA'){
			$sqlDadosFaturas = "	SELECT F.DATA_VENCIMENTO, F.DATA_PAGAMENTO, F.VALOR_PAGO AS VALOR_PAGO fROM ps1020 F
						WHERE  F.data_cancelamento is null and F.codigo_empresa = " . aspas($_SESSION['codigoIdentificacao']) . "
							AND CAST(EXTRACT(YEAR FROM F.data_pagamento) AS INTEGER ) = '$ano'
							AND F.DATA_PAGAMENTO IS NOT NULL
						ORDER BY F.DATA_VENCIMENTO ASC ";

		}
												
				
	}elseif(($grupoContrato != '14' && $_SESSION['codigoSmart'] == '3389') || $_SESSION['codigoSmart'] != '3389'){		
		$queryTitular = 'select codigo_associado from ps1000 where (DATA_EXCLUSAO IS NULL or (EXTRACT(YEAR FROM DATA_EXCLUSAO) >= ' . (date('Y') - 1) . ')) and numero_cpf = "' . $numeroCpf . '"';
		$resTitular = jn_query($queryTitular);	
		$rowTitular = jn_fetch_object($resTitular);
		$codigoAssociado = $rowTitular->CODIGO_ASSOCIADO;
			
		if($flagPlanoFam == 'N'){
			$sqlDadosFaturas = "SELECT 
								PS1000.CODIGO_ASSOCIADO,
								PS1000.CODIGO_ANTIGO,
								PS1000.NUMERO_CPF, 
								PS1000.NOME_ASSOCIADO,
								SUM(PS1021.VALOR_FATURA) AS TOTAL  
							FROM 
								PS1021
							INNER JOIN PS1020 ON PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020
							INNER JOIN PS1000 ON PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
							WHERE PS1000.CODIGO_TITULAR =  " . aspas($codigoAssociado) . " AND Extract(Year From Ps1020.Data_Pagamento) =  '$ano'
								GROUP BY   PS1000.CODIGO_ANTIGO,PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO";
		}else{
			
			if(retornaValorConfiguracao('UTILIZA_VALOR_PAGO_IR') == 'NAO' || retornaValorConfiguracao('UTILIZA_VALOR_PAGO_IR') == ''){
				$sqlDadosFaturas = "SELECT CODIGO_ASSOCIADO, CODIGO_ANTIGO, NUMERO_CPF, NOME_ASSOCIADO, SUM(VALOR_FATURA) AS TOTAL FROM (
										SELECT 
											PS1000.CODIGO_ASSOCIADO,
											PS1000.CODIGO_ANTIGO,
											PS1000.NUMERO_CPF, 
											PS1000.NOME_ASSOCIADO,
											PS1021.VALOR_FATURA 
										FROM 
											PS1020
										INNER JOIN PS1021 ON PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020
										INNER JOIN PS1000 ON PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
										WHERE PS1020.CODIGO_ASSOCIADO = '" . $_SESSION['codigoIdentificacao']. "' AND Extract(Year From Ps1020.Data_Pagamento) =  '$ano' and ps1021.codigo_titular =  '" . $_SESSION['codigoIdentificacao'] . "'
										
										UNION ALL
    
										SELECT
											PS1000.CODIGO_ASSOCIADO, PS1000.CODIGO_ANTIGO, PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO, PS1020.VALOR_FATURA
										FROM PS1020
										INNER JOIN PS1000 ON PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
										WHERE
											PS1020.CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']) . "
										AND Extract(Year From Ps1020.Data_Pagamento) = '$ano'
										AND PS1020.NUMERO_REGISTRO NOT IN (SELECT NUMERO_REGISTRO_PS1020 FROM PS1021 WHERE PS1021.CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']) . ")
									) AS FATURAS
									GROUP BY   FATURAS.CODIGO_ASSOCIADO, FATURAS.CODIGO_ANTIGO,FATURAS.NUMERO_CPF, FATURAS.NOME_ASSOCIADO";
			}else{
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
										WHERE PS1020.CODIGO_ASSOCIADO = '" . $_SESSION['codigoIdentificacao']. "' AND Extract(Year From Ps1020.Data_Pagamento) =  '$ano' and ps1021.codigo_titular =  '" . $_SESSION['codigoIdentificacao'] . "'
										
										UNION ALL
    
										SELECT
											PS1000.CODIGO_ASSOCIADO, PS1000.CODIGO_ANTIGO, PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO, PS1020.VALOR_FATURA
										FROM PS1020
										INNER JOIN PS1000 ON PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
										WHERE
											PS1020.CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']) . "
										AND Extract(Year From Ps1020.Data_Pagamento) = '$ano'
										AND PS1020.NUMERO_REGISTRO NOT IN (SELECT NUMERO_REGISTRO_PS1020 FROM PS1021 WHERE PS1021.CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']) . ")
									) AS FATURAS
									GROUP BY   FATURAS.CODIGO_ASSOCIADO, FATURAS.CODIGO_ANTIGO,FATURAS.NUMERO_CPF, FATURAS.NOME_ASSOCIADO";
			}
		
					
		}		
		
		
	}else{
		
		$queryTitular = 'select codigo_associado from ps1000 where (DATA_EXCLUSAO IS NULL or (EXTRACT(YEAR FROM DATA_EXCLUSAO) >= ' . (date('Y') - 1) . ')) and numero_cpf = "' . $numeroCpf . '"';
		
		$resTitular = jn_query($queryTitular);	
		$rowTitular = jn_fetch_object($resTitular);

		$codigoAssociado = $rowTitular->CODIGO_ASSOCIADO;		
		
		$sqlDadosFaturas = "SELECT 							
								PS1000.NUMERO_CPF, 
								PS1000.CODIGO_ASSOCIADO,
								PS1000.NOME_ASSOCIADO,
								SUM(PS1021.VALOR_FATURA) AS TOTAL  
							FROM 
								PS1020
							INNER JOIN PS1021 ON PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020
							INNER JOIN PS1000 ON PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
							WHERE PS1000.CODIGO_TITULAR IN (select codigo_associado from ps1000 where (DATA_EXCLUSAO IS NULL or (EXTRACT(YEAR FROM DATA_EXCLUSAO) >= " . (date('Y') - 1) . ")) and numero_cpf = " . aspas($numeroCpf) . ") AND Extract(Year From Ps1020.Data_Pagamento) =  '$ano'
								GROUP BY   PS1000.NUMERO_CPF, PS1000.NOME_ASSOCIADO";

	}

	$res = jn_query($sqlDadosFaturas);

    $i = 0;
    $ArrDadosFatBen = Array();
   while( $row = jn_fetch_assoc($res)  ) {
	   if($_SESSION['codigoSmart'] == '3423'){//Plena
			$ArrDadosFatBen[$i]['DATA_VENCIMENTO'] 	= $row['DATA_VENCIMENTO'];
			$ArrDadosFatBen[$i]['DATA_PAGAMENTO'] 	= $row['DATA_PAGAMENTO'];
			$ArrDadosFatBen[$i]['VALOR_PAGO'] 		= $row['VALOR_PAGO'];
			$ArrDadosFatBen[$i]['NOME_ASSOCIADO'] 	= $row['NOME_ASSOCIADO'];
	   }else{		   	  
			$ArrDadosFatBen[$i]['CODIGO_ASSOCIADO'] = $row['CODIGO_ASSOCIADO'];
			$ArrDadosFatBen[$i]['CODIGO_ANTIGO'] 	= $row['CODIGO_ANTIGO'];
			$ArrDadosFatBen[$i]['NUMERO_CPF'] 		= $row['NUMERO_CPF'];
			$ArrDadosFatBen[$i]['NOME_ASSOCIADO'] 	= $row['NOME_ASSOCIADO'];
			$ArrDadosFatBen[$i]['TOTAL'] 			= $row['TOTAL'];
	   }
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
	

   <body style="text-align:center; font-size: 12px; width:780px;" leftmargin="10">
	<div align="center">
		<div style="width:100% !important; height:5% !important;">
			<img align="left" style="width:20% !important" src="<?php echo file_exists('../../Site/assets/img/logo_operadora.png') ? '../../Site/assets/img/logo_operadora.png' : '../../Site/assets/img/logo_operadora.jpg';?>" border="0"/>
			<div style="width:80% !important"> </div>
		</div>	
      

      <div style="width:780px; text-align: right; ">
		   <?php if($_SESSION['codigoSmart'] == '3423'){ ?> 
			<table width="780" border="0" style="font-size:12px;">
				<tr>
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
				</tr>
			 </table>
		</div>
      <?php } ?>

      <div style="font-size:11px; font-weight: bold; text-align:center">
         <br />
		 <?php 
			if($_SESSION['codigoSmart'] == '4055'){
				
				echo 'Declaração Para Imposto de Renda ' .  ($ano + 1) . ' - Ano Calendário ' . $ano;
			}else{
				echo 'DEMONSTRATIVO DE PAGAMENTOS DAS MENSALIDADES DO PLANO DE SA&Uacute;DE  <br> CORRESPONDENTES AO ANO DE ' .  $ano;
			}
		 
		 ?>
      </div>

         <div style="width:780px; text-align: left; "> 
		   <?php if($_SESSION['codigoSmart'] == '3423'){ ?> 
		   	<br>
		   	<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
		   <br /><br>
			<table width="780" border="0" style="font-size:12px;">

				<tr>
					  <strong>Nome Contratante ou responsável:</strong>
						  <?php 
						  echo $nomeContratante; 
						  ?>
					  <br /><br />
					  <strong>CPF Contratante ou responsável:</strong>
						  <?php 
						  echo $cpfContratante; 
						  ?>
				</tr>
			 </table>

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
							  <strong>Relação<br> depedência</strong>
							 
					    </td>
					    <td width="166">
							  <strong>Valor Pago<br> no ano</strong>
							 
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
			 <?php } ?>
				 <br>
				 <?php
				 if($valorTotalAno > 0){
				 ?>
					<table width="720" border="0" style="font-size:12px;"> 
						<tr>
							<td class="lblToValue" style='text-align:right; font-size: 12px;'><?php echo "<b>TOTAL:</b> " .toMoeda($valorTotalAno); ?></td>
						</tr>
					</table>	
				 <?php				 				 
				 }
				 
				 ?>
	         	 
				 <br>
		  		 <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
         </div>

         	
	  <div style="width:780px; text-align: left; ">
      <br />
		<?php 
				
		if($_SESSION['codigoSmart'] == '4055'){
			echo 'Em conformidade com o disposto na Lei nº 9.250, de 26 de dezembro de 1.995, arts. 8º, inciso II, alínea “a”,
					e $2º, incisos de I a IV, e 35; e Instrução Normativa RFB nº 1.500, de 29 de outubro de 2014, art 100, $ 1º)
					declaramos que recebemos do Sr (a) ' . $_SESSION['nomeUsuario'] . ', inscrito no CPF sob o nº '. $numeroCpf . ' os valores
					listados abaixo, referentes ao pagamento de co-participações do plano de saúde, CAIXA DE ASSISTÊNCIA A SAÚDE DOS TRABALHADORES DAS INDUSTRIAS METALÚRGICAS, MECÂNICA E DE
					MATERIAL ELÉTRICO DE BRUSQUE - SINTIMMMEB SAÚDE,  CNPJ: 21.205.801/0001-63, localizado na Rua João Bauer, 75, CEP 88350-101,  na cidade de Brusque, SC.';			
		}elseif($grupoContrato != '14') { ?>
		  <br /><br />
		  
		  Prezado (a) Senhor (a) <?php echo $_SESSION['nomeUsuario']; ?>
		  <br /><br />
		  <?php if($ano >= 2013 && $_SESSION['codigoSmart'] == '3389'){ ?>
		  <div align="justify">
		  Pelas atribui&ccedil;&otilde;es concedidas nas Resolu&ccedil;&otilde;es Normativas 515 e 557, na condi&ccedil;&atilde;o de <b>"COBRAN&Ccedil;A POR DELEGA&Ccedil;&Atilde;O"</b>, informamos a seguir os valores arrecadados pela <b><?php echo $dadosEmpresa['NOME_OPERADORA_VM']; ?></b> do seu <?php  echo ($dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '6' || $dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '7')? "plano odontológico":"plano de sa&uacute;de";?>  coletivo por ades&atilde;o da <b><?php echo $dadosEmpresa['NOME_OPERADORA']; ?></b><?php  echo ($dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '6' || $dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '7')? ".":", contratado atrav&eacute;s da Entidade de Classe <b>" . $dadosEmpresa['DESCRICAO_GRUPO_CONTRATO'] . '</b>.'; ?>	  
		  </div>
		  <?php }elseif($_SESSION['codigoSmart'] == '3555' && $flagPlanoFam == 'S'){//Evidente  ?>
			Informamos a seguir os valores arrecadados de seu <?php  echo ($dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '6' || $dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '7')? "plano odontológico":"plano de sa&uacute;de";?> contratado atrav&eacute;s do <?php  echo $dadosEmpresa['DESCRICAO_GRUPO_CONTRATO'];  ?> no ano de <?php  echo $ano;  ?>, correspondentes ao plano Evidente Individual contratado na <?php echo $dadosEmpresa['NOME_OPERADORA']; ?>.
		  <?php }elseif($_SESSION['codigoSmart'] == '3423' && $flagPlanoFam == 'S'){//Plena ?>
			Informamos a seguir os valores arrecadados de seu <?php  echo ($dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '6' || $dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '7')? "plano odontológico":"plano de sa&uacute;de";?> contratado atrav&eacute;s do <?php  echo $dadosEmpresa['DESCRICAO_GRUPO_CONTRATO'];  ?> no ano de <?php  echo $ano;  ?>.
		  <?php }else{ ?>
			Informamos a seguir os valores arrecadados de seu <?php  echo ($dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '6' || $dadosEmpresa['CODIGO_GRUPO_CONTRATO']== '7')? "plano odontológico":"plano de sa&uacute;de";?> contratado atrav&eacute;s do <?php  echo $dadosEmpresa['DESCRICAO_GRUPO_CONTRATO'];  ?> no ano de <?php  echo $ano;  ?>, correspondentes ao Contrato Coletivo por Ades&atilde;o que a entidade mant&eacute;m junto &agrave; <?php echo $dadosEmpresa['NOME_OPERADORA']; ?>
		  <?php } ?>
		<?php } ?>
	  <br />
      <br /><br />
	  </div>
      <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
      <div style="width:780px; text-align: center; ">
		   <?php if($ano >= 2013 && $_SESSION['codigoSmart'] == '3389'){ ?> 
		<strong></br>DADOS DA ADMINISTRADORA</strong>
         <br />
		  <br />
		   <br />

			<table width="780" border="0" style="font-size:12px;">
				<tr>
				   <td width="105">
					  <strong>ANS n&deg;</strong>
					  <br /><br />
					  <?php
					  echo $dadosEmpresa['NUMERO_ANS_OPERADORA_VM']; 
					  ?>
				   </td>
				   <td width="355">
					  <strong>RAZ&Atilde;O SOCIAL</strong>
					  <br /><br />
					  <?php 
					  echo $dadosEmpresa['NOME_OPERADORA_VM'];
					  ?>
				   </td>
				   <td width="195">
					  <strong>CNPJ</strong>
					  <br /><br />
					  <?php 
					  echo $dadosEmpresa['CNPJ_OPERADORA_VM'];
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
			 <?php }elseif($_SESSION['codigoSmart'] != '4055') {

				if(retornaValorConfiguracao('BANCO_ADMINISTRADORA') == 'SIM'){
					echo '<strong></br>DADOS DA ADMINISTRADORA DE BENEF&Iacute;CIOS</strong>';
				}else{
					echo '<strong></br>DADOS DA OPERADORA</strong>';
				}
			
			?>
			
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
			 <?php } ?>
      </div>
      <br /><br />
      <div style="width:780px; text-align: center; ">
	  <?php
	  if($_SESSION['codigoSmart'] != '4055') {
	  ?>
      <img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />
	  
	  <?php

			if($_SESSION['codigoSmart'] == '4298'){//Benet Saude
				
				echo '<br>';
				echo '<strong></br>DADOS DO BENEFICI&Aacute;RIO </strong>';
				echo '<br>';
				echo '<table width="780" border="0" style="font-size:12px;">' ;
				echo '	<tr> ';
				echo '   	<td width="300">';
				echo '			<strong></br>N&uacute;mero do C.P.F.: </strong> ' . $numeroCpf;
				echo '	 	</td>';
				echo '   	<td width="480">';
				echo '			<strong></br>Nome do benefici&aacute;rio: </strong> ' . $_SESSION['nomeUsuario'];
				echo '	 	</td>';
				echo '	</tr>';
				echo '</table>';				
				echo '<br>';
				echo '<img src="images/ponto_preto.jpg" style="height:1px; background-color:#000000; width:780px; " />';
			}
	  ?>
	  
	  <?php
	
	  	if($_SESSION['codigoSmart'] == '4298' && $ano == '2022'){//Benet Saude
			echo '<strong></br>PER&Iacute;ODO DE PAGAMENTO (MAIO A DEZEMBRO / 2022) </strong>';
		}else{
			echo '<strong></br>VALORES ARRECADADOS</strong>';
		}
			
		?>         

         <br /><br />
	  <?php
	  }
	  
	  $descLabel1 = 'CPF';
	  $descLabel2 = 'NOME';
	  $descLabel3 = 'VALOR';
	  if($_SESSION['codigoSmart'] == '3423'){//Plena
		  $descLabel1 = 'DATA VENCIMENTO';
		  $descLabel2 = 'DATA PAGAMENTO';
		  $descLabel3 = 'VALOR PAGO';
	  }
	 
	  ?>
		 <table width="780" border="0" style="font-size:12px;">               
			<tr>
				<td class="lblToValue"><b><?php echo $descLabel1; ?></b></td>
				<td class="lblToValue"><b><?php echo $descLabel2; ?></b></td>
				<td class="lblToValue" style='text-align:right; font-size: 12px;'><b><?php echo $descLabel3; ?></b></td>
			</tr>
			<?php
			$totalGeral = 0;
			if (count($ArrDadosFatBen)> 0){				
				foreach($ArrDadosFatBen as $item) {
					if($_SESSION['codigoSmart'] == '3423'){//Plena
						echo "<tr>";
						echo	"<td class='lblToValue'>". SqlToData($item['DATA_VENCIMENTO'])     ."</td>";
						echo	"<td class='lblValue'  >". SqlToData($item['DATA_PAGAMENTO']) ."</td>";
						echo	"<td class='lblValue' style='text-align:right; font-size: 12px;' >". toMoeda($item['VALOR_PAGO'])          ."</td>";
						echo "</tr>";					
						$totalGeral = $totalGeral + $item['VALOR_PAGO'];
					}else{						
						echo "<tr>";
						echo	"<td class='lblToValue'>". $item['NUMERO_CPF']     ."</td>";
						echo	"<td class='lblValue'  >". $item['NOME_ASSOCIADO'] ."</td>";
						echo	"<td class='lblValue' style='text-align:right; font-size: 12px;' >". toMoeda($item['TOTAL'])          ."</td>";
						echo "</tr>";
						$totalGeral = $totalGeral + $item['TOTAL'];
					}
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