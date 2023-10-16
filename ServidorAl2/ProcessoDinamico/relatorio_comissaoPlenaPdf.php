<?php
require('../lib/base.php');
header("Content-Type: text/plain");

$numeroRegistro = $_GET['numeroRegistro'];

$queryEmp = 'SELECT * FROM CFGEMPRESA';
$resEmp = jn_query($queryEmp);
$rowEmp = jn_fetch_object($resEmp);

$queryRel  = " SELECT IDENTIFICACAO_RELATORIO, REFERENCIA_RELATORIO_OPERADOR, CORPO_RELATORIO ";
$queryRel .= " FROM DW_REGISTROS_RELATORIOS ";
$queryRel .= " WHERE TABELA_CHAVE_PRIMARIA = 'PS1100' AND CHAVE_PRIMARIA_FILTRO = " . aspas($_SESSION['codigoIdentificacao']);
$queryRel .= ' AND NUMERO_REGISTRO =  ' . aspas($numeroRegistro);

$resRel = jn_query($queryRel);
$rowRel = jn_fetch_object($resRel);

$corpoRelatorio = jn_utf8_encode($rowRel->CORPO_RELATORIO); 

echo $corpoRelatorio;
exit;
?>