<?php
require('../lib/base.php');

$CabecalhoGlosa = array();
$GuiasGlosa = array();
$ProcedimentosGlosa = array();

if ($_SESSION["perfilOperador"] != 'PRESTADOR'){
	echo '<script>alert("Seu perfil não esta habilitado para visualizar este perfil.");</script>';
}

$query  = " SELECT A.CODIGO_PRESTADOR, A.NOME_PRESTADOR, A.MES_REFERENCIA, A.NF, A.VALOR_COBRADO, A.NUMERO_PROTOCOLO, B.NUMERO_PROCESSAMENTO, ";
$query .= " A.VALOR_GLOSADO, A.VALIR_LIQUIDO, A.DATA_VENCIMENTO ";
$query .= " FROM VW_GLOSA_EXCEL_AL2 AS A   ";
$query .= " INNER JOIN PS5800 B ON (A.NUMERO_PROTOCOLO = B.NUMERO_REGISTRO)   ";
$query .= " WHERE 1=1 ";    
$query .= " AND A.CODIGO_PRESTADOR = " . aspas($_SESSION["codigoIdentificacao"]);    

if($_GET["numeroCapa"] != ""){
	$query .= " AND A.NUMERO_PROTOCOLO = " . aspas($_GET["numeroCapa"]);    
}

if($_GET["numeroProcessamento"] != ""){
	$query .= " AND B.NUMERO_PROCESSAMENTO = " . aspas($_GET["numeroProcessamento"]);    
}

if($_GET["numeroNF"] != ""){
	$query .= " AND (A.NF = " . aspas($_GET["numeroNF"]) . " OR (A.NF = " . aspas("REC" . $_GET["numeroNF"]) . ") OR (A.NF = " . aspas("REC " . $_GET["numeroNF"]) . ")) ";    
}

$res = jn_query($query);

$i = 0;
while($row = jn_fetch_object($res)){
	$CabecalhoGlosa["CODIGO_PRESTADOR" . $i] 	= $row->CODIGO_PRESTADOR;
	$CabecalhoGlosa["NOME_PRESTADOR" . $i] 		= $row->NOME_PRESTADOR;
	$CabecalhoGlosa["MES_REFERENCIA" . $i] 		= $row->MES_REFERENCIA;
	$CabecalhoGlosa["NUMERO_NF" . $i] 			= $row->NF;
	$CabecalhoGlosa["VALOR_COBRADO" . $i] 		= $row->VALOR_COBRADO;
	$CabecalhoGlosa["VALOR_GLOSADO" . $i] 		= $row->VALOR_GLOSADO;
	$CabecalhoGlosa["VALOR_LIQUIDO" . $i] 		= $row->VALIR_LIQUIDO;
	$CabecalhoGlosa["DATA_VENCIMENTO" . $i] 	= $row->DATA_VENCIMENTO;
	$CabecalhoGlosa["PRAZO_RECURSO" . $i] 		= $row->PRAZO_RECURSO;	
	$CabecalhoGlosa["NUMERO_CAPA" . $i] 		= $row->NUMERO_PROTOCOLO;	
	$CabecalhoGlosa["NUMERO_PROCESSAMENTO" . $i]= $row->NUMERO_PROCESSAMENTO;	
	
	$numeroCabecalho = $i;
	$i++;
	$quantidadeCabecalhos = $i;
}

if(!$quantidadeCabecalhos){
	echo '<script>alert("Não foi encontrado nenhuma guia glosada com os filtros informados, favor verificar os dados digitados.");</script>';
	echo "<script>window.close();</script>";
}
?>

<?php
$html = '
<html xmlns="http://www.w3.org/1999/xhtml">
	<body style="text-align:left; font-size: 11px; background-color:#ffffff !important; margin: 30px 30px 30px 30px!important;">
		<div style="font-size:12px; font-weight: bold;" align="center">
			RELAT&Oacute;RIO DE GLOSAS
		</div>
';
?>
				
		<?php
		$i = 0;
		
		while ($i < $quantidadeCabecalhos){

		
$html .= '				
			<table width="100%" border="0" style="font-size:11px;" align="center">
				<tr>
					<td width="400">
						<strong>C&oacute;digo - Nome Prestador</strong>
						<br /><br />
						' . $CabecalhoGlosa["CODIGO_PRESTADOR" . $i] . " - " . $CabecalhoGlosa["NOME_PRESTADOR" . $i] . '
					</td>
					<td width="195">
						<strong>N&uacute;mero Capa</strong>
						<br /><br />
						' .  $CabecalhoGlosa["NUMERO_CAPA" . $i] . '
					</td>
					<td width="195">
						<strong>Processamento</strong>
						<br /><br />
						' . $CabecalhoGlosa["NUMERO_PROCESSAMENTO" . $i] . '
					</td>
					<td width="195">
						<strong>Numero NF</strong>
						<br /><br />
						' . $CabecalhoGlosa["NUMERO_NF" . $i] . '
					</td>
					<td width="195">
						<strong>Valor Cobrado</strong>
						<br /><br />
						' . toMoeda($CabecalhoGlosa["VALOR_COBRADO" . $i]) . '
					</td>
					<td width="195">
						<strong>Valor Glosado</strong>
						<br /><br />
						' . toMoeda($CabecalhoGlosa["VALOR_GLOSADO" . $i]) . '
					</td>
					<td width="195">
						<strong>Valor Liquido</strong>
						<br /><br />
						' . toMoeda($CabecalhoGlosa["VALOR_LIQUIDO" . $i]) . '
					</td>
					<td width="195">
						<strong>Data Vencimento</strong>
						<br /><br />
						' . SqlToData($CabecalhoGlosa["DATA_VENCIMENTO" . $i]) . '
					</td>
					<td width="195">
						<strong>Prazo Recurso</strong>
						<br /><br />
						' . SqlToData($CabecalhoGlosa["PRAZO_RECURSO" . $i]) . '
					</td>
				</tr>
			</table>
			';
			
				$queryGuias  = " SELECT DISTINCT C.NUMERO_GUIA, C.CODIGO_ASSOCIADO, C.NOME_ASSOCIADO, C.NOME_PLANO, C.NUMERO_AUTORIZACAO, C.DATA_PROCEDIMENTO, C.TIPO_GUIA ";				
				$queryGuias .= " FROM VW_DEMONSTRATIVO_GLOSA C ";										
				$queryGuias .= " WHERE C.CODIGO_PRESTADOR = ". $CabecalhoGlosa["CODIGO_PRESTADOR" . $i];    
				$queryGuias .= " AND C.CAPA = ". $CabecalhoGlosa["NUMERO_CAPA" . $i];    
				$queryGuias .= " ORDER BY C.NOME_ASSOCIADO";				
				$resGuias = jn_query($queryGuias);
				
				$ig = 0;
				$valorTotalCobrado = 0;
				$valorTotalGerado = 0;
				$valorTotalGlosado = 0;
				while($rowGuias = jn_fetch_object($resGuias)) {
					$GuiasGlosa["TIPO_GUIA" . $ig] 			= $rowGuias->TIPO_GUIA;
					$GuiasGlosa["NUMERO_GUIA" . $ig] 		= $rowGuias->NUMERO_GUIA;
					$GuiasGlosa["CODIGO_ASSOCIADO" . $ig] 	= $rowGuias->CODIGO_ASSOCIADO;
					$GuiasGlosa["NOME_ASSOCIADO" . $ig] 	= $rowGuias->NOME_ASSOCIADO;
					$GuiasGlosa["NOME_PLANO" . $ig] 		= $rowGuias->NOME_PLANO;
					$GuiasGlosa["DATA_PROCEDIMENTO" . $ig] 	= $rowGuias->DATA_PROCEDIMENTO;				
					$GuiasGlosa["NUMERO_AUTORIZACAO" . $ig] = $rowGuias->NUMERO_AUTORIZACAO;				
					
$html .= '
					<table width="90%" border="0" style="font-size:11px;" align="center">						
						<br />						
						<tr>
							<td width="195">
								<strong>N&uacute;mero Guia</strong>
								<br />
								' . $GuiasGlosa["NUMERO_GUIA" . $ig] . '
							</td>
							<td width="400">
								<strong>C&oacute;digo - Nome Associado</strong>
								<br />
								' . $GuiasGlosa["CODIGO_ASSOCIADO" . $ig] . " - " . $GuiasGlosa["NOME_ASSOCIADO" . $ig] . '
							</td>
							<td width="195">
								<strong>Nome Plano</strong>
								<br />
								' . $GuiasGlosa["NOME_PLANO" . $ig] . '
							</td>							
							<td width="195">
								<strong>Data Procedimento</strong>
								<br />
								' . SqlToData($GuiasGlosa["DATA_PROCEDIMENTO" . $ig]) . '
							</td>
							<td width="195">
								<strong>N&uacute;mero Autoriza&ccedil;&atilde;o</strong>
								<br />
								' . $GuiasGlosa["NUMERO_AUTORIZACAO" . $ig] . '
							</td>								
						</tr>
					</table>';
								
			
				$queryProcedimentos  = " SELECT P.CODIGO_PROC_MATMED_SERV, P.DESCRICAO, P.CODIGO_GLOSA, P.NOME_GLOSA, ";				
				$queryProcedimentos .= " P.OBSERVACAO_SIMPLES, P.QTD, P.VALOR_COBRADO, P.VALOR_GLOSA, P.VALOR_GERADO ";												
				$queryProcedimentos .= " FROM VW_DEMONSTRATIVO_GLOSA P ";
				$queryProcedimentos .= " WHERE P.NUMERO_GUIA = ". aspas($GuiasGlosa["NUMERO_GUIA" . $ig]);
				$queryProcedimentos .= " AND P.TIPO_GUIA = ". aspas($GuiasGlosa["TIPO_GUIA" . $ig]);
				$queryProcedimentos .= " ORDER BY P.NOME_ASSOCIADO";				
				$resProcedimentos = jn_query($queryProcedimentos);
				
				$p = 0;
				$valorTotalCobrado = 0;
				$valorTotalGerado = 0;
				$valorTotalGlosado = 0;
				while($rowProcedimentos = jn_fetch_object($resProcedimentos)) {
					$ProcedimentosGlosa["CODIGO_PROC_MATMED_SERV" . $p] = $rowProcedimentos->CODIGO_PROC_MATMED_SERV;
					$ProcedimentosGlosa["DESCRICAO" . $p] 				= $rowProcedimentos->DESCRICAO;
					$ProcedimentosGlosa["CODIGO_GLOSA" . $p] 			= $rowProcedimentos->CODIGO_GLOSA;
					$ProcedimentosGlosa["NOME_GLOSA" . $p] 				= $rowProcedimentos->NOME_GLOSA;					
					$ProcedimentosGlosa["OBSERVACAO_SIMPLES" . $p] 		= $rowProcedimentos->OBSERVACAO_SIMPLES;					
					$ProcedimentosGlosa["QTD" . $p] 					= $rowProcedimentos->QTD;					
					$ProcedimentosGlosa["VALOR_COBRADO" . $p] 			= toMoeda($rowProcedimentos->VALOR_COBRADO);
					$ProcedimentosGlosa["VALOR_GLOSA" . $p] 			= toMoeda($rowProcedimentos->VALOR_GLOSA);
					$ProcedimentosGlosa["VALOR_GERADO" . $p] 			= toMoeda($rowProcedimentos->VALOR_GERADO);
					
					$valorTotalCobrado = $valorTotalCobrado + $rowProcedimentos->VALOR_COBRADO;
					$valorTotalGerado  = $valorTotalGerado + $rowProcedimentos->VALOR_GERADO;
					$valorTotalGlosado = $valorTotalGlosado + $rowProcedimentos->VALOR_GLOSA;
					
$html .= '
					<table width="85%" border="0" style="font-size:11px;" align="center">
						<tr>
							<td width="195">';								
								if($p == 0){
									$html .= '<br>';
									$html .= '<strong>C&oacute;digo Procedimento/Material - Descri&ccedil;&atilde;o</strong>';
								}
$html .= '								
								<br />
								<li>
									' . $ProcedimentosGlosa["CODIGO_PROC_MATMED_SERV" . $p] . " - " . $ProcedimentosGlosa["DESCRICAO" . $p] . '
								</li>
							</td>							
							<td width="140">';
								
									if($p == 0){										
										$html .= '<br>';
										$html .= '<strong>C&oacute;digo - Nome Glosa</strong>';
									}
$html .= '								
								<br />								
								' . $ProcedimentosGlosa["CODIGO_GLOSA" . $p] . " - " . $ProcedimentosGlosa["NOME_GLOSA" . $p] . '
							</td>
							<td width="100">';
								
									if($p == 0){										
										$html .= '<br>';
										$html .= '<strong>Observa&ccedil;&atilde;o Simples</strong>';
									}
								
$html .= '
								<br />
								' . $ProcedimentosGlosa["OBSERVACAO_SIMPLES" . $p] . '
							</td>	
							<td width="60">';
								
									if($p == 0){										
										$html .= '<br>';
										$html .= '<strong>Quantidade</strong>';
									}
$html .= '														
								<br />								
								' . $ProcedimentosGlosa["QTD" . $p] . '
							</td>	
							<td width="80">';
								if($p == 0){										
									$html .= '<br>';
									$html .= '<strong>Valor Cobrado</strong>';
								}
$html .= '
								<br />								
								' . $ProcedimentosGlosa["VALOR_COBRADO" . $p] . '
							</td>	
							<td width="80">';						
								if($p == 0){
									$html .= '<br>';
									$html .= '<strong>Valor Gerado</strong>';
								}
$html .= '								
								<br />								
								' . $ProcedimentosGlosa["VALOR_GERADO" . $p] . '
							</td>								
							<td width="80">';
								if($p == 0){										
									$html .= '<br>';
									$html .= '<strong>Valor Glosado</strong>';
								}
$html .= '
								<br />								
								' . $ProcedimentosGlosa["VALOR_GLOSA" . $p] . '
							</td>	
						</tr>
					</table>';						
								
					$p++;					
				}
$html .= '
					
						<br>
						<table width="85%" border="0" style="font-size:11px;" align="center">
						<tr>
							<td width="195">																											
							</td>							
							<td width="140">								
							</td>
							<td width="100">								
							</td>	
							<td width="60">
								"<b>Total: </b>"
							</td>	
							<td width="80">
								' . toMoeda($valorTotalCobrado) . '
							</td>	
							<td width="80">
								' . toMoeda($valorTotalGerado) . '
							</td>								
							<td width="80">
								' . toMoeda($valorTotalGlosado) . '
							</td>	
						</tr>
					</table>';									
		
					$ig++;					
				}		
			$i++;
		}
$html .= '
</HTML>';

$arquivo = 'Prestador_' . $_SESSION['codigoIdentificacao'] . '_Capa_' . $_GET["numeroCapa"] . '.xls';
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
header ("Content-Description: PHP Generated Data" );
// Envia o conteúdo do arquivo
echo $html;
exit;

?>