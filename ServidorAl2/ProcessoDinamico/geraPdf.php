<?php
require('../lib/base.php');
require('../lib/mpdf60/mpdf.php');
require_once('../lib/html2pdf/html2pdf.class.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);


pr($_SERVER);
$pagina = $_SERVER['SCRIPT_URI'];
pr($pagina);
$pagina = str_replace('geraPdf.php',$_GET['pagina'],$pagina);
pr($pagina);
$pagina = $pagina.'?'.$_SERVER['QUERY_STRING'];
pr($pagina);

$mpdf=new mPDF();
$mpdf->debug = true;
$html = file_get_contents($pagina);
//echo $html;
//exit;
$mpdf->WriteHTML($html);
$mpdf->Output();
exit;

$content = jn_utf8_encode($html);

// convert


require_once('../lib/html2pdf/html2pdf.class.php');
try
{
	//$html2pdf = new HTML2PDF('P','A4','fr', array(0, 0, 0, 0));
	
	$html2pdf = new HTML2PDF('P','A4','pt', true, 'UTF-8');
	
	/* Abre a tela de impressão */
	//$html2pdf->pdf->IncludeJS("print(true);");
	
	$html2pdf->pdf->SetDisplayMode('real');
	
	/* Parametro vuehtml = true desabilita o pdf para desenvolvimento do layout */
	
	
	//$content = nl2br(str_replace("&", "&amp;", htmlentities($content)));
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));//
	
	/* Abrir no navegador */
	$html2pdf->Output('boleto.pdf');
	
	/* Salva o PDF no servidor para enviar por email */
	//$html2pdf->Output('caminho/boleto.pdf', 'F');
	
	/* Força o download no browser */
	//$html2pdf->Output('boleto.pdf', 'D');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}

?>