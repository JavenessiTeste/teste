<?php
require('../lib/base.php');
header ('Content-type: text/html; charset=ISO-8859-1');

$codAssociado = trim($dadosInput['codAssociado']);
$url = retornaValorConfiguracao('LINK_PASTA_CONTRATOS') . 'ServidorAl2/EstruturaPrincipal/disparoEmail.php?retornaMensagem=true&codigoModelo=1&vnd=true&codigoAssociado='.$codAssociado;

$ch = curl_init();	
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
$info = curl_getinfo($ch);
$start = $info['header_size'];
$body = substr($result, $start, strlen($result) - $start);
curl_close($ch);

?>