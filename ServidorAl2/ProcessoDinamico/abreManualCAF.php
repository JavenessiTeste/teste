<?php
require('../lib/base.php');
require('../private/autentica.php');

$queryPlano = 'SELECT CODIGO_PLANO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
$resPlano = jn_query($queryPlano);
$rowPlano = jn_fetch_object($resPlano);

$codigoPlano = $rowPlano->CODIGO_PLANO;

$query = 'Select ARQUIVO_PDF from ps1030 where codigo_plano = ' . aspas($codigoPlano);
$res = jn_query($query);

if( $row = jn_fetch_assoc($res)  ) {		
    header('Content-type: application/pdf; charset=utf-8');
    echo $row['ARQUIVO_PDF'];
}

?>