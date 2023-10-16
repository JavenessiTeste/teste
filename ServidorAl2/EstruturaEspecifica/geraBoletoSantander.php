<?php
require('../lib/base.php');

$query1020 = 'SELECT CODIGO_IDENTIFICACAO_FAT FROM PS1020 WHERE NUMERO_REGISTRO = ' . aspas($_GET['registro']);
$res1020 = jn_query($query1020);
$row1020 = jn_fetch_object($res1020);

if(!$row1020->CODIGO_IDENTIFICACAO_FAT){
	require('../services/registraBoletoSantanter.php');
}

if($retorno['STATUS'] == 'OK' || $row1020->CODIGO_IDENTIFICACAO_FAT != ''){
	if($_GET['imprimir'] == 'SIM'){
		header('Location: \AliancaAppNet2\ServidorAl2\boletos\boleto_santander_DemaisDescontosPDF.php?especifico=SIM&numeroRegistro=' . $_GET['registro']);
	}else{
		return true;
	}
}else{
	echo json_encode($retorno);
	exit;
}

?>