<?php
require('../lib/base.php');
require 'PagSeguroLibrary/PagSeguroLibrary.php';
require 'DadosPagSeguro.php';

$fatura = $_GET['registro'];

$query 	 =	" SELECT PS1020.MES_ANO_REFERENCIA, VALOR_FATURA, PS1000.NOME_ASSOCIADO, PS1015.ENDERECO_EMAIL ";
$query 	.=	" FROM PS1020 ";
$query 	.=	" INNER JOIN PS1000 ON (PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
$query 	.=	" INNER JOIN PS1015 ON (PS1015.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
$query 	.=	" WHERE PS1020.NUMERO_REGISTRO = " . aspas($fatura);

$res    = jn_query($query);

if($row = jn_fetch_object($res))
{
	$descricao = 'Boleto Coparticipação';
	$link = geraPagamento($row->MES_ANO_REFERENCIA,$descricao,1,$row->VALOR_FATURA,$fatura,$row->NOME_ASSOCIADO,$row->ENDERECO_EMAIL,'frm_principal.php');
}else{
	$link = '../html/frm_principal.php';
}	

echo '<script language= "JavaScript">location.href="'.$link.'"</script>';



function geraPagamento($refItem,$descItem,$quant,$valor,$refPagamento,$nomeCliente,$emailCliente,$linkRetorno){ 				
		
		global $pagSeguro;
		
		$paymentRequest = new PagSeguroPaymentRequest();

		$paymentRequest->setCurrency("BRL");

		// Referencia dos itens
		$paymentRequest->addItem($refItem,$descItem, $quant,$valor);

		// Referencia da fatura
		$paymentRequest->setReference($refPagamento);

		// Dados Comprador. NOME E-MAIL DDD TELEFONE.
		$paymentRequest->setSender($nomeCliente, $emailCliente);

		// Local Retorno Cliente
		$paymentRequest->setRedirectUrl($linkRetorno);		
		
	
		try{
			/*
			* #### Crendencials ##### 
			*/

			$credentials = new PagSeguroAccountCredentials($pagSeguro['email'], $pagSeguro['token']);

			// Registra o link do pagseguro
			return $paymentRequest->register($credentials);

		} catch (PagSeguroServiceException $e) 
		{									
			return "Erro: ".$e->getMessage();

		}

}

?>
