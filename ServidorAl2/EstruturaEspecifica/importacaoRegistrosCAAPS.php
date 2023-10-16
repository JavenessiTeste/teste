<?php
require_once('../lib/base.php');
$_SESSION['codigoSmart'] = 3389; //Foi passado o smart fixo, porque será usado no gerencia contrato.

$queryAssocImport = '	SELECT FIRST 500 MATRICULA FROM TMP_BENEF_CAAPSML
						WHERE 
							CODIGO_PARENTESCO = 0 
						AND NUMERO_CPF IS NULL
						AND MATRICULA NOT IN (SELECT COALESCE(MATRICULA,"") AS MATRICULA FROM VND1000_ON)
						';

$res = jn_query($queryAssocImport);
while($row = jn_fetch_object($res)){	
	$url = retornaValorConfiguracao('LINK_PASTA_CONTRATOS') . 'ServidorAl2/EstruturaEspecifica/requestLoginCAAPSML.php?atualizaPag=N&paramBenef='.$row->MATRICULA;

	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);

	echo 'Associado Importado = ' . $row->MATRICULA . ' ---- ';
}

?>
