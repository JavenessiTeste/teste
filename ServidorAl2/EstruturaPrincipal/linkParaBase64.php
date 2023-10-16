<?php
require('../lib/base.php');

//require('../private/autentica.php');

$retorno['BASE'] = getSslPage($dadosInput['link']);

echo json_encode($retorno);

function getSslPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $result = curl_exec($ch);
	//echo 'Curl error: ' . curl_error($ch).$result;
    curl_close($ch);
    return base64_encode($result);
}

?>