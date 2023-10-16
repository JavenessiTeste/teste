<?php
require('base.php');

$chaveClickSign = retornaValorConfiguracao('CHAVE_CLICKSIGN');
$homolClickSign = retornaValorConfiguracao('HOMOLOGACAO_CLICKSIGN');

$headers = array("Content-Type: application/json","Accept:application/json");

$codAssociado = $_GET['codAssociado'];

$queryDoc = 'SELECT CHAVE_DOC_CLICKSIGN FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociado);
$resDoc = jn_query($queryDoc);
$rowDoc = jn_fetch_object($resDoc);

$chaveDoc = $rowDoc->CHAVE_DOC_CLICKSIGN;

if($homolClickSign == 'SIM'){
	$url = 'https://sandbox.clicksign.com/api/v1/documents/' . $chaveDoc . '?access_token=' . $chaveClickSign;
}else{
	$url = 'https://app.clicksign.com/api/v1/documents/' . $chaveDoc . '?access_token=' . $chaveClickSign;
}

$ch = CURL_INIT();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

$result = curl_exec($ch);
$info = curl_getinfo($ch);	
$start = $info['header_size'];
$body = substr($result, $start, strlen($result) - $start);

$body = json_decode($body);	
header("Location:" . $body->document->downloads->signed_file_url);
?>
