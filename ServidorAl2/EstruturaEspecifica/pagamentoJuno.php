<?php
require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='fechamentoCompraServicos')
{
	require('../services/juno.php');
	
	$selectRegistro = 'select DADOS_RETORNO from ESP_JUNO_FATURAS_CARTAO where NUMERO_IDENTIFICACAO_COMPRA ='.aspas($dadosInput['compraGerada']);
	$resRegistro  = jn_query($selectRegistro);
	if($rowRegistro = jn_fetch_object($resRegistro)){
	
		$retorno = $rowRegistro->DADOS_RETORNO;
		echo $retorno;	
		exit;
	}
	$retorno['MSG']  = '';
	$query = 'select ps5720.numero_identificacao_compra,Ps5720.codigo_associado,Ps1000.NOME_ASSOCIADO,Ps5720.data_compra, sum(ps5721.valor_cobranca_cliente) VALOR_COMPRA from ps5720
			  inner join ps5721 on Ps5720.numero_identificacao_compra = ps5721.numero_identificacao_compra
			  inner join PS1000 on Ps5720.codigo_associado = PS1000.codigo_associado
			  where ps5720.DATA_PAGAMENTO_CLIENTE is null and ps5720.numero_identificacao_compra = '.aspas($dadosInput['compraGerada']).'
			  group by ps5720.numero_identificacao_compra,Ps5720.codigo_associado,Ps1000.NOME_ASSOCIADO,Ps5720.data_compra';
	
	$res  = jn_query($query);
	if($row = jn_fetch_object($res)){
	
		$dataVencimento = date('d/m/Y', strtotime('+5 days'));
		
		$retorno['STATUS'] = 'OK';
		$retorno['TITULO']				   = '<h1>'.$row->NOME_ASSOCIADO.' - '.$row->CODIGO_ASSOCIADO.'</h1>Id compra: '.$dadosInput['compraGerada'];
		$retorno['DATA_VENCIMENTO']        = $dataVencimento;
		$retorno['TOTAL_DEBITOS']          = toMoeda($row->VALOR_COMPRA);
		$retorno['PRODUCAO']			   = retornaValorCFG0003('JUNO_PRODUCAO') == 'SIM';
		$retorno['CHAVE_PUBLICA']          = retornaValorCFG0003('JUNO_TOKEN_PUBLICO');
			
		
		$querySplit = 'select Ps5000.codigo_prestador, PS5000.token_juno,sum(ps5722.valor_prestador) VALOR from ps5722
						inner join ps5000 on Ps5000.codigo_prestador = ps5722.codigo_prestador
						where Ps5722.numero_identificacao_compra = '.aspas($dadosInput['compraGerada']).'
						group by Ps5000.codigo_prestador, PS5000.token_juno';	

		$resSplit  = jn_query($querySplit);
		$splitFatura = array();
		$ValorPrestadores = 0; 
		while($rowSplit = jn_fetch_object($resSplit)){
		
			$itemFatura['TOKEN'] = $rowSplit->TOKEN_JUNO;
			$itemFatura['VALOR'] = $rowSplit->VALOR;
			$splitFatura[] = $itemFatura;
			$ValorPrestadores = $ValorPrestadores + $rowSplit->VALOR;

		}

		$resto = $row->VALOR_COMPRA - $ValorPrestadores;
		if($resto>0){
			$itemFatura['TOKEN'] = retornaValorCFG0003('JUNO_TOKEN_PRIVADO');
			$itemFatura['VALOR'] = $resto;
			$splitFatura[]       = $itemFatura;
		}
		$queryEnd = 'select PS1000.codigo_associado,Ps1000.nome_associado,Ps1000.DATA_NASCIMENTO,Ps1000.numero_cpf,ps1001.endereco_email,Ps1001.endereco,Ps1001.bairro,Ps1001.cidade,Ps1001.cep,Ps1001.ESTADO,(select first 1 ps1006.codigo_area||ps1006.numero_telefone from ps1006 where Ps1006.codigo_associado = Ps1000.codigo_associado) TELEFONE
					from ps1000
					left join ps1001 on Ps1000.codigo_associado = Ps1001.codigo_associado
					where Ps1000.codigo_associado = '.aspas($row->CODIGO_ASSOCIADO);
					
		$resEnd  = jn_query($queryEnd);
		if($rowEnd = jn_fetch_object($resEnd)){	
			$endereco = $rowEnd->ENDERECO.' ';
			$numero = '';
			$complemento = '';
			$auxEndereco = explode(',',$endereco);
			$endereco = $auxEndereco[0];
			if(count($auxEndereco)>1){
				$auxEndereco = explode('-',$endereco); 
				$numero = $auxEndereco[0];
				if(count($auxEndereco)>1){
					$complemento = $auxEndereco[1];
				}
			}
			$retornoCobranca = CadastraCobranca($rowEnd->NOME_ASSOCIADO,$rowEnd->NUMERO_CPF,$rowEnd->ENDERECO_EMAIL,$endereco,$numero,$rowEnd->BAIRRO,$complemento,$rowEnd->CIDADE,$rowEnd->ESTADO,$rowEnd->CEP,$rowEnd->TELEFONE,sqlToData($rowEnd->DATA_NASCIMENTO),$dadosInput['compraGerada'],'Pagamento referente a compra '.$dadosInput['compraGerada'],$row->VALOR_COMPRA,$dataVencimento,$splitFatura,0,0,0,false);
			if($retornoCobranca['STATUS'] == 'OK'){
				$retorno['ID_COBRANCA'] = $retornoCobranca['DADOS']['_embedded']['charges'][0]['id'];
				$retorno['IMAGE_PIX']   = $retornoCobranca['DADOS']['_embedded']['charges'][0]['pix']['imageInBase64'];
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG'] = 'Erro ao efetuar o cadastro.<br><br>';
				for ($i = 0; $i < count($retornoCobranca['ERROS']['details']); $i++) {
					$retorno['MSG'] .=deParaCampoJuno($retornoCobranca['ERROS']['details'][$i]['field']).': '.$retornoCobranca['ERROS']['details'][$i]['message'].'<br>';
				}
			}
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'Erro ao obter dados do beneficiario';		
		}
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Não Foi Possivel Gerar a fatura';
	}
	
	if($retorno['STATUS'] == 'OK'){
		$insert = 'insert into  ESP_JUNO_FATURAS_CARTAO(NUMERO_IDENTIFICACAO_COMPRA,DADOS_RETORNO,JSON_COBRANCA)VALUES('.aspas($dadosInput['compraGerada']).','.aspas(json_encode($retorno)).','.aspas(json_encode($retornoCobranca)).')';
		jn_query($insert);		
	}
	echo json_encode($retorno);		

}
else if($dadosInput['tipo'] =='efetuaPagamento')
{
	require('../services/juno.php');
	
	$retorno['STATUS'] = 'ERRO';
	$retorno['MSG']    = 'Não foi possivel efetuar o pagamento';		

	$query = 'select ps5720.numero_identificacao_compra,Ps5720.codigo_associado,Ps1000.NOME_ASSOCIADO,Ps5720.data_compra, sum(ps5721.valor_cobranca_cliente) VALOR_COMPRA from ps5720
			  inner join ps5721 on Ps5720.numero_identificacao_compra = ps5721.numero_identificacao_compra
			  inner join PS1000 on Ps5720.codigo_associado = PS1000.codigo_associado
			  where ps5720.numero_identificacao_compra = '.aspas($dadosInput['compraGerada']).'
			  group by ps5720.numero_identificacao_compra,Ps5720.codigo_associado,Ps1000.NOME_ASSOCIADO,Ps5720.data_compra';
	
	$res  = jn_query($query);
	if($row = jn_fetch_object($res)){	
		$queryEnd = 'select PS1000.codigo_associado,Ps1000.nome_associado,Ps1000.DATA_NASCIMENTO,Ps1000.numero_cpf,ps1001.endereco_email,Ps1001.endereco,Ps1001.bairro,Ps1001.cidade,Ps1001.cep,Ps1001.ESTADO,(select first 1 ps1006.codigo_area||ps1006.numero_telefone from ps1006 where Ps1006.codigo_associado = Ps1000.codigo_associado) TELEFONE
					from ps1000
					left join ps1001 on Ps1000.codigo_associado = Ps1001.codigo_associado
					where Ps1000.codigo_associado = '.aspas($row->CODIGO_ASSOCIADO);
					
		$resEnd  = jn_query($queryEnd);
		if($rowEnd = jn_fetch_object($resEnd)){	
			$endereco = $rowEnd->ENDERECO.' ';
			$numero = '';
			$complemento = '';
			$auxEndereco = explode(',',$endereco);
			$endereco = $auxEndereco[0];
			if(count($auxEndereco)>1){
				$auxEndereco = explode('-',$endereco); 
				$numero = $auxEndereco[0];
				if(count($auxEndereco)>1){
					$complemento = $auxEndereco[1];
				}
			}	
			
			
			$retornoPagamento = EfetuaPagamentoCartao($dadosInput['idPagamento'],$dadosInput['hash'],$rowEnd->ENDERECO_EMAIL,$endereco,$numero,$rowEnd->BAIRRO,$complemento,$rowEnd->CIDADE,$rowEnd->ESTADO,$rowEnd->CEP);
			
			if($retornoPagamento['STATUS'] == 'OK'){
				$retorno['ID_PAGAMENTO'] = $retornoPagamento['DADOS']['transactionId'];
				$retorno['RETORNO']      = $retornoPagamento;
				$retorno['STATUS'] = 'OK';
				$retorno['MSG']    = 'Pagamento Efetuado com sucesso.';
				$queryUpdate = 'UPDATE PS5720 set VALOR_PAGO_CLIENTE ='.aspas($retornoPagamento['DADOS']['payments'][0]['amount']).',DATA_PAGAMENTO_CLIENTE= '.aspas(dataToSql(date('d/m/Y'))).',ID_PAGAMENTO_CLIENTE ='.aspas($retorno['ID_PAGAMENTO']).' where numero_identificacao_compra = '.aspas($dadosInput['compraGerada']);
				jn_query($queryUpdate);
				$update = 'UPDATE ESP_JUNO_FATURAS_CARTAO set JSON_PAGAMENTO_CARTAO='.aspas(json_encode($retornoPagamento)).' where NUMERO_IDENTIFICACAO_COMPRA='.aspas($dadosInput['compraGerada']);
				jn_query($update);	
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG'] = 'Erro ao efetuar o pagamento.<br><br>';
				for ($i = 0; $i < count($retornoPagamento['ERROS']['details']); $i++) {
					$retorno['MSG'] .=deParaCampoJuno($retornoPagamento['ERROS']['details'][$i]['field']).': '.$retornoPagamento['ERROS']['details'][$i]['message'].'<br>';
				}
			}		
		}
	}
	
	echo json_encode($retorno);	
}


?>