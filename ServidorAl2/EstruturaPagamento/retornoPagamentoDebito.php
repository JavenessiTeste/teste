<?php

require('../lib/base.php');

require('pagamentoGetNet.php');

$dados = file_get_contents("php://input");
$dados = json_decode($dados, True);


$name  = 'retornoPagamentoDebito.log';

$text  = 'Post  : '.json_encode($_POST)."\n";
$text .= 'Get   : '.json_encode($_GET)."\n";
$text .= 'Input : '.json_encode($dados)."\n";

$file  = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);

if($_POST['MD']!=''){
	
	$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC)
				values(".aspas('RETORNOPAGAMENTODEBITO').",".aspas('').",".aspas('retornoPagamentoDebito.php').",".aspas('').",".aspas(json_encode($_POST)).",".aspas($_GET['ID']).",".aspas('').",GETDATE())";
	
	$res  = jn_query($insert,false,false);
}else if($dados['status']=='APPROVED'){
	$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC)
				values(".aspas('DEPOISFINALIZARDEBITO').",".aspas('').",".aspas('retornoPagamentoDebito.php').",".aspas('').",".aspas(json_encode($dados)).",".aspas($dados['payment_id']).",".aspas($dados['order_id']).",GETDATE())";
	
	$res  = jn_query($insert,false,false);
	
	$dadoBoleto = explode('-',$dados['order_id']);
	efetuaBaixa('CD',$dadoBoleto[0],$dadoBoleto[1],$dados['amount']);
		
	
	echo '{MSG:"OK}';
	

}else if($_POST['status']=='APPROVED'){
	$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC)
				values(".aspas('DEPOISFINALIZARDEBITO').",".aspas('').",".aspas('retornoPagamentoDebito.php').",".aspas('').",".aspas(json_encode($_POST)).",".aspas($_POST['payment_id']).",".aspas($_POST['order_id']).",GETDATE())";
	
	$res  = jn_query($insert,false,false);
	
	$dadoBoleto = explode('-',$_POST['order_id']);
	efetuaBaixa('CD',$dadoBoleto[0],$dadoBoleto[1],$_POST['amount']);
		
	
	echo '{MSG:"OK}';
	

}



?>
