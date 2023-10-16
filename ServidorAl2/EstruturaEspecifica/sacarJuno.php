<?php
require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='dados')
{
	require('../services/juno.php');
	$retorno['MSG'] = '';
	
	$queryToken  = ' Select Nome_Prestador,TOKEN_JUNO from Ps5000 where  ID_JUNO = ' . aspas($dadosInput['idJuno']);	
	$resToken = jn_query($queryToken);
	$rowToken = jn_fetch_object($resToken);

	
	$retornoSaldo = SaldoConta($rowToken->TOKEN_JUNO);
	
	if($retornoSaldo['STATUS'] == 'OK'){
		$retorno['STATUS'] = 'OK';
		$retorno['HTML']   = '<h1>'.$rowToken->NOME_PRESTADOR.'</h1><br><br>';
		
		$retorno['HTML']   .= '<b>Saldo</b>: '.toMoeda($retornoSaldo['DADOS']['balance']).'<br>';
		$retorno['HTML']   .= '<b>Saldo Retido</b>: '.toMoeda($retornoSaldo['DADOS']['withheldBalance']).'<br>';
		$retorno['HTML']   .= '<b>Saldo transferível </b>: '.toMoeda($retornoSaldo['DADOS']['transferableBalance']).'<br>';
		
		if($retornoSaldo['DADOS']['transferableBalance']>0)
			$retorno['MOSTRASACAR'] = true;
		else		
			$retorno['MOSTRASACAR'] = true;
		
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'Erro ao obter os dados.<br><br>';
				for ($i = 0; $i < count($retornoSaldo['ERROS']['details']); $i++) {
					$retorno['MSG'] .=deParaCampoJuno($retornoSaldo['ERROS']['details'][$i]['field']).': '.$retornoSaldo['ERROS']['details'][$i]['message'].'<br>';
				}
		$retorno['MOSTRASACAR'] = false;
	}
	
	echo json_encode($retorno);		

}else if($dadosInput['tipo'] =='sacar')
{
	require('../services/juno.php');
	$retorno['MSG'] = '';
	
	$queryToken  = ' Select Nome_Prestador,TOKEN_JUNO from Ps5000 where  ID_JUNO = ' . aspas($dadosInput['idJuno']);	
	$resToken = jn_query($queryToken);
	$rowToken = jn_fetch_object($resToken);

	$retornoTranferencia = SolicitaTranferencia($rowToken->TOKEN_JUNO,$dadosInput['valor'] );
	if($retornoTranferencia['STATUS'] == 'OK'){
		$retorno['STATUS'] = 'OK';
		$retorno['MSG'] .= 'Saque efetuado.<br><br>';
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] .= 'Erro ao tentar o saque.<br><br>';
				for ($i = 0; $i < count($retornoTranferencia['ERROS']['details']); $i++) {
					$retorno['MSG'] .=deParaCampoJuno($retornoTranferencia['ERROS']['details'][$i]['field']).': '.$retornoTranferencia['ERROS']['details'][$i]['message'].'<br>';
				}
		
	}
	
	
	
	$retornoSaldo = SaldoConta($rowToken->TOKEN_JUNO);
	
	if($retornoSaldo['STATUS'] == 'OK'){
		$retorno['STATUS'] = 'OK';
		$retorno['HTML']   = '<h1>'.$rowToken->NOME_PRESTADOR.'</h1><br><br>';
		
		$retorno['HTML']   .= '<b>Saldo</b>: '.toMoeda($retornoSaldo['DADOS']['balance']).'<br>';
		$retorno['HTML']   .= '<b>Saldo Retido</b>: '.toMoeda($retornoSaldo['DADOS']['withheldBalance']).'<br>';
		$retorno['HTML']   .= '<b>Saldo transferível </b>: '.toMoeda($retornoSaldo['DADOS']['transferableBalance']).'<br>';
		
		if($retornoSaldo['DADOS']['transferableBalance']>0)
			$retorno['MOSTRASACAR'] = true;
		else		
			$retorno['MOSTRASACAR'] = true;
		
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] .= 'Erro ao obter os dados.<br><br>';
				for ($i = 0; $i < count($retornoSaldo['ERROS']['details']); $i++) {
					$retorno['MSG'] .=deParaCampoJuno($retornoSaldo['ERROS']['details'][$i]['field']).': '.$retornoSaldo['ERROS']['details'][$i]['message'].'<br>';
				}
		$retorno['MOSTRASACAR'] = false;
	}
	
	echo json_encode($retorno);		

}


?>