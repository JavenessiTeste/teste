<?php
require('../lib/base.php');
require_once('fpdf/fdpf.php');
//require_once('fpdfi/src/fpdfi.php');

$pdf = new FPDI();

$pdf->AddPage(); 

$pdf->setSourceFile('proposta_padrao.pdf'); 
// import page 1 
$tplIdx = $this->pdf->importPage(1); 
//use the imported page and place it at point 0,0; calculate width and height
//automaticallay and ajust the page size to the size of the imported page 
$this->pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 

// now write some text above the imported page 
$this->pdf->SetFont('Arial', '', '13'); 
$this->pdf->SetTextColor(0,0,0);
//set position in pdf document
$this->pdf->SetXY(20, 20);
//first parameter defines the line height
$this->pdf->Write(0, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
//force the browser to download the output
$this->pdf->Output('gift_coupon_generated.pdf', 'D');


?>