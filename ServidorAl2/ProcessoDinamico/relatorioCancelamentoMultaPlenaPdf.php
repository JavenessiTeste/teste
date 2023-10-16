<?php
require_once('../lib/base.php');

include("../lib/mpdf60/mpdf.php");
$mpdf=new mPDF(); 
$mpdf->charset_in='windows-1252';
$mpdf->curlAllowUnsafeSslRequests = true;
$mpdf->SetDisplayMode('fullpage');	
	
$mpdf->SetHTMLHeader('<div id="divimagem"><img Width="1389" Height="1965" src="relatorioCancelamentoMultaPlena.php?cod=' . $_GET['cod'] . '" /></div>');

$mpdf->WriteHTML($contrato);

//$mpdf->debug = true;
$mpdf->WriteHTML($html);
$mpdf->Output();

?>