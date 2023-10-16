<?php
require('../lib/base.php');
require('../private/autentica.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$dadosCopart = array();

if ($_SESSION["perfilOperador"] != 'EMPRESA'){
	echo '<script>alert("Seu perfil não esta habilitado para visualizar este relatório.");</script>';
}

$fatura = $_GET['numeroRegistro'];

$query  = " SELECT NUMERO_REGISTRO_PS1020, CODIGO_EMPRESA,	NOME_PLANO_FAMILIARES, CODIGO_ASSOCIADO, ";
$query .= " NOME_ASSOCIADO, CODIGO_TITULAR,	DATA_NASCIMENTO, NUMERO_CPF, NOME_PRESTADOR, NOME_ESPECIALIDADE, ";
$query .= " DATA_PROCEDIMENTO, VALOR_EVENTO ";
$query .= " FROM VW_RELATORIO_COPART_NET ";
$query .= " WHERE NUMERO_REGISTRO_PS1020 = " . aspas($fatura);    
$res = jn_query($query);

$quant = 0;
while($row = jn_fetch_object($res)){
	$dadosCopart["NUMERO_REGISTRO_PS1020"] 	= $row->NUMERO_REGISTRO_PS1020;
	$dadosCopart["CODIGO_EMPRESA"] 			= $row->CODIGO_EMPRESA;
	$dadosCopart["NOME_PLANO_FAMILIARES"] 	= $row->NOME_PLANO_FAMILIARES;
	$dadosCopart["CODIGO_ASSOCIADO"] 		= $row->CODIGO_ASSOCIADO;
	$dadosCopart["NOME_ASSOCIADO"] 			= $row->NOME_ASSOCIADO;
	$dadosCopart["CODIGO_TITULAR"] 			= $row->CODIGO_TITULAR;
	$dadosCopart["DATA_NASCIMENTO"] 		= $row->DATA_NASCIMENTO;
	$dadosCopart["NUMERO_CPF"] 				= $row->NUMERO_CPF;
	$dadosCopart["NOME_PRESTADOR"] 			= $row->NOME_PRESTADOR;
	$dadosCopart["NOME_ESPECIALIDADE"] 		= $row->NOME_ESPECIALIDADE;
	$dadosCopart["DATA_PROCEDIMENTO"] 		= $row->DATA_PROCEDIMENTO;
	$dadosCopart["VALOR_EVENTO"] 			= $row->VALOR_EVENTO;

	$quant++;	
}

$html = '
<html xmlns="http://www.w3.org/1999/xhtml">
	<body style="text-align:left; font-size: 11px; background-color:#ffffff !important; margin: 30px 30px 30px 30px!important;">
		<div style="font-size:12px; font-weight: bold;" align="center">
			RELATÓRIO DE COPARTICIPAÇÕES
		</div>
';
$i = 0;

while ($i < $quant){


	$html .= '				
	<table width="100%" border="0" style="font-size:11px;" align="center">
		<tr>
			<td width="400">
				<strong>Registro</strong>
				<br /><br />
				' . $dadosCopart["NUMERO_REGISTRO_PS1020"] . '
			</td>
			<td width="195">
				<strong>Empresa</strong>
				<br /><br />
				' .  $dadosCopart["CODIGO_EMPRESA"] . '
			</td>
			<td width="195">
				<strong>Plano</strong>
				<br /><br />
				' . $dadosCopart["NOME_PLANO_FAMILIARES"] . '
			</td>
			<td width="195">
				<strong>Código Associado</strong>
				<br /><br />
				' . $dadosCopart["CODIGO_ASSOCIADO"] . '
			</td>
			<td width="195">
				<strong>Nome Associado</strong>
				<br /><br />
				' . $dadosCopart["NOME_ASSOCIADO"] . '
			</td>
			<td width="195">
				<strong>Titular</strong>
				<br /><br />
				' . $dadosCopart["CODIGO_TITULAR"] . '
			</td>
			<td width="195">
				<strong>Data Nascimento</strong>
				<br /><br />
				' . SqlToData($dadosCopart["DATA_NASCIMENTO"]) . '
			</td>
			<td width="195">
				<strong>Número CPF</strong>
				<br /><br />
				' . $dadosCopart["NUMERO_CPF"] . '
			</td>
			<td width="195">
				<strong>Prestador</strong>
				<br /><br />
				' . $dadosCopart["NOME_PRESTADOR"] . '
			</td>
			<td width="195">
				<strong>Especialidade</strong>
				<br /><br />
				' . $dadosCopart["NOME_ESPECIALIDADE"] . '
			</td>
			<td width="195">
				<strong>Data Procedimento</strong>
				<br /><br />
				' . SqlToData($dadosCopart["DATA_PROCEDIMENTO"]) . '
			</td>
			<td width="195">
				<strong>Valor</strong>
				<br /><br />
				' . toMoeda($dadosCopart["VALOR_EVENTO"]) . '
			</td>
		</tr>
	</table>
	';

	$i++;
}
$html .= '
</HTML>';

$arquivo = 'Empresa_' . $_SESSION['codigoIdentificacao'] . '_Registro_' . $fatura . '.xls';
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
header ("Content-Description: PHP Generated Data" );

echo $html;
exit;