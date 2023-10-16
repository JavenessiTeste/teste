<?php
require('../lib/base.php');
require 'PagSeguroLibrary/PagSeguroLibrary.php';
require 'DadosPagSeguro.php';
require 'MyLogPHP.php';

global $pagSeguro;

$log = new MyLogPHP('log.csv',';');
 
$log->info('Inicio Pagina.'); 
 
$queryEmpresa  = ' SELECT ';
$queryEmpresa .= ' 	NUMERO_INSC_SUSEP, ';
$queryEmpresa .= ' 	CODIGO_SMART ';
$queryEmpresa .= ' FROM CFGEMPRESA ';

$resEmpresa  = jn_query($queryEmpresa);
$rowEmpresa = jn_fetch_object($resEmpresa);

$CodigoSmart = $rowEmpresa->CODIGO_SMART;

//Verifica se foi enviado um método post
if($_SERVER['REQUEST_METHOD'] == 'POST'){
 
	$log->info('Inicio Post.');
 
    //Recebe o post como o Tipo de Notificação
    $tipoNotificacao   = $_POST['notificationType'];
 
	$log->info('tipoNotificacao',$tipoNotificacao);
 
    //Recebe o código da Notificação
    $codigoNotificacao = $_POST['notificationCode'];
 
	$log->info('codigoNotificacao',$codigoNotificacao);
 
    //Verificamos se tipo da notificação é transaction
    if($tipoNotificacao == 'transaction'){

    	//Informa as credenciais : Email, e TOKEN
        $credencial = new PagSeguroAccountCredentials($pagSeguro['email'], $pagSeguro['token']);
 
 
        //Verifica as informações da transação, e retorna 
        //o objeto Transaction com todas as informações
        $transacao = PagSeguroNotificationService::checkTransaction($credencial, $codigoNotificacao);
 		

        //Retorna o objeto TransactionStatus, que vamos resgatar o valor do status
        $codigoStatus    = $transacao->getStatus()->getValue();//CodigoStatus
        $nomeStatus		 = $transacao->getStatus()->getTypeFromValue();
        $tipoPagamento 	 = $transacao->getPaymentMethod()->getType()->getTypeFromValue(); 
        $valorBruto 	 = $transacao->getGrossAmount();
        $valorLiquido 	 = $transacao->getNetAmount();   
        
		$log->info('codigoStatus',$codigoStatus);
		$log->info('nomeStatus',$nomeStatus);
		$log->info('tipoPagamento',$tipoPagamento);
		$log->info('valorBruto',$valorBruto);
		$log->info('valorLiquido',$valorLiquido);
		
        /**
        * Pegamos o código que passamos por referência para o pagseguro
        * Que no nosso exemplo é id da tabela pedido
        */
        
        $idPedido = $transacao->getReference();
        
        $temp = explode("-", $idPedido);

		$log->info('idPedido',$idPedido);
        
        $banco          = $temp[0];
        $numeroRegistro = $temp[1];
        
		$SisConfig['DATABASE']['SOURCE']   = $temp[0];				
        	
		$sql = "INSERT INTO FATURA_RETORNO_NET(data_retorno, status_retorno,forma_pagamento,numero_registro_ps1020,id_pedido) values (current_timestamp,".aspas($nomeStatus).", substring(".aspas($tipoPagamento)." from 1 for 40),".aspas($idPedido).",".aspas($idPedido).")";			
		jn_query($sql);		

		if($nomeStatus == 'PAID'){
			$updateJv0020 = "UPDATE  PS1020 SET valor_pago = ". aspas($valorBruto).", data_pagamento = current_timestamp, TIPO_BAIXA = ".aspas('W')." WHERE numero_registro = " . aspas($idPedido);				
			jn_query($updateJv0020);			
			
			if($CodigoSmart == '4055'){
				$queryFat = "SELECT CODIGO_ASSOCIADO FROM PS1020 WHERE numero_registro = " . aspas($idPedido);
				$resFat = jn_query($queryFat);
				$rowFat = jn_fetch_object($resFat);
				$assoc = $rowFat->CODIGO_ASSOCIADO;
				
				if($assoc){
					$queryAssoc = ' SELECT CODIGO_SITUACAO_ATENDIMENTO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($assoc);
					$resAssoc = jn_query($queryAssoc);
					$rowAssoc = jn_fetch_object($resAssoc);
					
					$ArraySituacoes = array('2','4','6','7','8','20','21','22','23','25');
					if (in_array($rowAssoc->CODIGO_SITUACAO_ATENDIMENTO, $ArraySituacoes)){					
						
						$insertPS1096 = "INSERT INTO PS1096 (CODIGO_ASSOCIADO, CODIGO_SITUACAO_ATENDIMENTO, DATA_CADASTRO_SOLICITACAO, CODIGO_OPERADOR) VALUES (" . aspas($assoc) . ", -999, current_timestamp, 8)";
						jn_query($insertPS1096);
												
						$updateAssoc = 'UPDATE PS1000 SET CODIGO_SITUACAO_ATENDIMENTO = NULL WHERE CODIGO_TITULAR = ' . aspas($assoc);
						jn_query($updateAssoc);
					}
				}
			}
		}        
    }
 
}