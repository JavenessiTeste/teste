<?php
require('../lib/base.php');

$associado = $_GET['codAssociado'];

$queryAssoc  = ' SELECT ';
$queryAssoc .= ' 	VND1000_ON.CODIGO_ASSOCIADO, VNDSTATUS_NIVEL1.NOME_STATUS AS NOME_STATUS1, VNDSTATUS_NIVEL2.NOME_STATUS AS NOME_STATUS2, ';
$queryAssoc .= ' VNDSTATUS_NIVEL3.NOME_STATUS AS NOME_STATUS3, VND1001_ON.NUMERO_CONTRATO  '; 
$queryAssoc .= ' FROM VND1000_ON '; 
$queryAssoc .= ' INNER JOIN VND1001_ON ON (VND1001_ON.CODIGO_ASSOCIADO = VND1000_ON.CODIGO_ASSOCIADO) ';
$queryAssoc .= ' INNER JOIN VNDSTATUS_NIVEL1 ON (VNDSTATUS_NIVEL1.NUMERO_REGISTRO = VND1000_ON.NUMERO_REGISTRO_NIVEL1) '; 
$queryAssoc .= ' LEFT OUTER JOIN VNDSTATUS_NIVEL2 ON (VNDSTATUS_NIVEL2.NUMERO_REGISTRO = VND1000_ON.NUMERO_REGISTRO_NIVEL2) '; 
$queryAssoc .= ' LEFT OUTER JOIN VNDSTATUS_NIVEL3 ON (VNDSTATUS_NIVEL3.NUMERO_REGISTRO = VND1000_ON.NUMERO_REGISTRO_NIVEL3) '; 
$queryAssoc .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($associado);
$resAssoc = jn_query($queryAssoc);

if(!$rowAssoc = jn_fetch_object($resAssoc)){	
	echo 'Não ocorreu alteração de Status';
	exit;
}

$data = Array();
$data['numeroContrato'] = $rowAssoc->NUMERO_CONTRATO;
$data['status'] = jn_utf8_encode($rowAssoc->NOME_STATUS1);
$data['fluxo'] = jn_utf8_encode($rowAssoc->NOME_STATUS2);
$data['motivo'] = jn_utf8_encode($rowAssoc->NOME_STATUS3);
			
$ch = curl_init();
$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyZWZyZXNoVG9rZW4iOiJleUpoYkdjaU9pSklVekkxTmlJc0luUjVjQ0k2SWtwWFZDSjkuZXlKMWMyVnlJam9pYW1GMlpTNTJhV3hsZG1WQVoyMWhhV3d1WTI5dElpd2ljR0Z6YzNkdmNtUWlPaUl6WW1GbU9ESmxaVGxsT1RKaE9UTTNPVEl3TkdGaE9HRmxaR0l3WmpGbU1qazJZelUzWXpFeFltTXhaalF5WXpJMVl6Qm1NekV4WkRSak1XSmxOVFUySWl3aWFXRjBJam94TmpnMU1ETTVPVFV4TENKbGVIQWlPakUyT0RVd05EY3hOVEY5LlFpWG9FREVyaFRqeUtTNGJmQW5UU1RyU19ZYUV0R18xdXBqdi1iRUpxaEkiLCJpYXQiOjE2ODUwMzk5NTEsImV4cCI6OC42NGUrMzZ9.htia0Fz-FTogJgMnzyNibg5lMo5SzsgbuFm48sZvw0I';
$url = retornaValorConfiguracao('URL_WEBHOOK_VILEVE');
$headers = array("Accept:application/json", "Authorization: Bearer " . $token);

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$result = curl_exec($ch);
$info = curl_getinfo($ch);	
$start = $info['header_size'];
$body = substr($result, $start, strlen($result) - $start);

curl_close($ch);
$body = json_decode($body);
?>