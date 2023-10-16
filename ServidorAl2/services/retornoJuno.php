<?php
require('../lib/base.php');

$dados = file_get_contents("php://input");
$dados = json_decode($dados, True);

$name  = 'retornoPJuno.log';

$text  = 'Post  : '.json_encode($_POST)."\n";
$text .= 'Get   : '.json_encode($_GET)."\n";
$text .= 'Input : '.json_encode($dados)."\n";

$file  = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);

if($_GET['id']=='PROPRIO'){
	if($dados['eventType']=='PAYMENT_NOTIFICATION'){
		jn_query('UPDATE PS5720 set VALOR_PAGO_CLIENTE ='.aspas($dados['data'][0]['attributes']['amount']).',DATA_PAGAMENTO_CLIENTE= '.aspas(dataToSql(date('d/m/Y'))).',ID_PAGAMENTO_CLIENTE ='.aspas($dados['data'][0]['attributes']['entityId']).' where numero_identificacao_compra = '.aspas($_POST['chargeReference']));
	}
}else{
	if($dados['eventType']=='DIGITAL_ACCOUNT_STATUS_CHANGED'){	
		jn_query('UPDATE PS5000 SET STATUS_JUNO ='.aspas($dados['data'][0]['attributes']['status']).'  WHERE ID_JUNO = '  . aspas($dados['data'][0]['entityId']));
	}
	
}
	

?>