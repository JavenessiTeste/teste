<?php
require('../lib/base.php');

require('../private/autentica.php');


if($dadosInput['tipo'] =='parecer'){
	
	$query  = ' Select NUMERO_SOLICITACAO, CODIGO_OPERADOR_SOLICITACAO, CODIGO_OPERADOR_AUTORIZACAO, CODIGO_FORNECEDOR, DEPARTAMENTO, ';
	$query .= ' TIPO_AUDITORIA, VALOR_SOLICITADO, OPERADOR.NOME_USUAL AS NOME_OPERADOR, FORNECEDOR.NOME_USUAL AS NOME_FORNECEDOR, DATA_SOLICITACAO, DATA_AUTORIZACAO, STATUS_AUDITORIA, OBSERVACAO ';
	$query .= ' From Esp_Auditoria_Pagamentos_Net A ';
	$query .= ' Inner Join Ps1100 as Operador On (Operador.Codigo_Identificacao = A.Codigo_Operador_Solicitacao) ';	
	$query .= ' Left Outer Join Ps1100 as Fornecedor On (Fornecedor.Codigo_Identificacao = A.Codigo_Fornecedor) ';	
	$query .= ' Where Tipo_Auditoria = ' . aspas('P');
	$query .= ' And (A.Numero_Solicitacao = '. aspas($dadosInput['solicitacao'] ) . ') ';
	$res = jn_query($query);

	if($row = jn_fetch_object($res)) {
		$departamento = '';
		if($row->DEPARTAMENTO == 'I'){
			$departamento = 'Informática';
		}elseif($row->DEPARTAMENTO == 'A'){
			$departamento = 'Administração';
		}elseif($row->DEPARTAMENTO == 'P'){
			$departamento = 'Projetos';
		}elseif($row->DEPARTAMENTO == 'F'){
			$departamento = 'Financeiro';
		}elseif($row->DEPARTAMENTO == 'C'){
			$departamento = 'Contas Médicas';
		}
		
		$retorno['SOLICITACAO']  = '<ul>';
		$retorno['SOLICITACAO']  .='<li>
										<div class="contact-list"><strong>Número da Solicitação</strong></div>
										'.$row->NUMERO_SOLICITACAO.'&nbsp;
									</li>';
									
		$retorno['SOLICITACAO']  .='<li>
										<div class="contact-list"><strong>Fornecedor</strong></div>
										'.$row->NOME_FORNECEDOR.'&nbsp;
									</li>';
		$retorno['SOLICITACAO']  .='<li>
										<div class="contact-list"><strong>Operador Solicitante</strong></div>
										'.$row->CODIGO_OPERADOR_SOLICITACAO.'&nbsp;
									</li>';
		$retorno['SOLICITACAO']  .='<li>
										<div class="contact-list"><strong>Departamento</strong></div>
										'.$departamento.'&nbsp;
									</li>';
		$retorno['SOLICITACAO']  .='<li>
										<div class="contact-list"><strong>Observacao</strong></div>
										'.$row->OBSERVACAO.'&nbsp;
									</li>';
		$retorno['SOLICITACAO']  .='<li>
										<div class="contact-list"><strong>Valor Solicitado</strong></div>
										'.toMoeda($row->VALOR_SOLICITADO).'&nbsp;
									</li>';									
		$retorno['SOLICITACAO']  .='<li>
										<div class="contact-list"><strong>Data Autorização</strong></div>
										'.SqlToData($row->DATA_SOLICITACAO).'&nbsp;
									</li>';		
		
		$retorno['SOLICITACAO'] .= '</ul>';

	}
	
	echo json_encode($retorno);

}else if($_POST['tipo'] =='enviar'){	
	$solicitacao = $_POST['registro'];
	
	$query = "INSERT INTO ESP_PARECER_PAGAMENTOS_NET 
				(NUMERO_SOLICITACAO,
				 CODIGO_OPERADOR_PARECER,
				 DATA_PARECER,
				 HORA_PARECER,
				 STATUS_PARECER,
				 OBSERVACOES_PARECER) 
			 VALUES 
				(" . aspas($solicitacao) . ",
				 " . aspas($_SESSION['codigoIdentificacao']) . ", 				 
				 getdate(),
				 " . aspas(date("H:i")) . ",
				 " . aspas($_POST["status"]) . ",
				 " . aspas(jn_utf8_encode($_POST["texto"])) . ")";			
	if (jn_query($query)){
		$statusValidacao = $_POST["status"];
		if($statusValidacao == 'E'){
			$statusValidacao = 'P';
		}
		$queryUpdate  = ' UPDATE ESP_AUDITORIA_PAGAMENTOS_NET SET STATUS_AUDITORIA = ' . aspas($statusValidacao);
		$queryUpdate .= ' WHERE NUMERO_SOLICITACAO = ' . aspas($solicitacao);
		if (jn_query($queryUpdate)){
			$retorno['MSG'] = 'OK';
		}else{
			$retorno['MSG'] = 'ERRO AO ALTER STATUS PARECER';
		}
	}else{
		$retorno['MSG'] = 'ERRO AO GRAVAR PARECER';
	}	

	echo json_encode($retorno);


}







?>