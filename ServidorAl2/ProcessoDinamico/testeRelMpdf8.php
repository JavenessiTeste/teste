<?php

$htmlPadrao  = '<h1>Teste Impressão PDF - Leonardo</h1> <br>'; 
$htmlPadrao .= phpversion();

if(phpversion() > 7.3){
	require_once("../lib/mpdf60/mpdf.php");
	$mpdf=new mPDF('c'); 
}else{
	require_once __DIR__ . '../../lib/vendor/autoload.php';
	$mpdf = new \Mpdf\Mpdf();
}

$mpdf->WriteHTML($htmlPadrao);
$mpdf->Output('teste.pdf', 'I');