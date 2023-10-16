<?php
require('../lib/base.php');
require('../services/iugu.php');


$empresa = $dadosInput['codigoEmpresa'];
$associado = $dadosInput['codigoTitular'];


$retorno = Array();

$utilizaIUGU = retornaValorConfiguracao('UTILIZA_IUGU');


if($utilizaIUGU == 'SIM'){
	$retorno['RESPOSTA'] = 'SIM';
}else{
	$retorno['RESPOSTA'] = 'NAO';
}

if($retorno['RESPOSTA']=='SIM'){

	$retorno['STATUS'] = 'OK';
	$queryAssoc  = ' SELECT * FROM VND1000_ON ';
	$queryAssoc .= ' LEFT JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssoc .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($associado);
	$resAssoc = jn_query($queryAssoc);
	$rowAssoc = jn_fetch_object($resAssoc);
	
	//if(trim($rowAssoc->ID_IUGU) != ''){
	//	$retorno['LINK'] = $rowAssoc->LINK_IUGU;
	//	echo json_encode($retorno);
	//	exit;
	//}
	
	//CARTAO BOLETO VAZIO= os dois;
	$tipo = '';
	
	if( $dadosInput['tipoPagamento']=='CART_CRED_MES')
		$tipo = 'CARTAO';
	if( $dadosInput['tipoPagamento']=='BOLETO_MES')
		$tipo = 'BOLETO';
	//CARTAO BOLETO VAZIO= os dois;

	$endereco = explode(',',$rowAssoc->ENDERECO);
	$rua = $endereco[0];

	$comp = explode('-',$endereco[1]);
	$numero = $comp[0];
	$complemento = $comp[1];	


	$valorFat1020 = 0;
	$valorFatura = 0;

	$queryCodigos = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($associado);
	$resCodigos = jn_query($queryCodigos);
	while($rowCodigos = jn_fetch_object($resCodigos)){
		$valorAssoc = retornaValorPrevisao($rowCodigos->CODIGO_ASSOCIADO);
		$valorFatura = $valorFatura + $valorAssoc;
	}

	$valorFat1020 = $valorFatura;

	$valorFatura = str_replace(',','.',$valorFatura);
	$valorFatura = str_replace('.','.',$valorFatura);

	//$rowAssoc->ENDERECO_EMAIL = 'diego2607@gmail.com';
	
	if(trim($rowAssoc->ID_IUGU) != ''){
		$cadastroCliente = AlterarCliente($rowAssoc->ID_IUGU,$rowAssoc->NOME_ASSOCIADO,$rowAssoc->NUMERO_CPF,$rowAssoc->ENDERECO_EMAIL,$rua,$numero,$rowAssoc->BAIRRO,$complemento,$rowAssoc->CIDADE,$rowAssoc->ESTADO,$rowAssoc->CEP,'','');
	}else{
		$cadastroCliente = CadastraCliente($rowAssoc->NOME_ASSOCIADO,$rowAssoc->NUMERO_CPF,$rowAssoc->ENDERECO_EMAIL,$rua,$numero,$rowAssoc->BAIRRO,$complemento,$rowAssoc->CIDADE,$rowAssoc->ESTADO,$rowAssoc->CEP,'','');
	}
	//print_r($cadastroCliente['DADOS']);
	if($cadastroCliente['STATUS']=='OK'){
		if(trim($rowAssoc->ID_IUGU_ASSINATURA) != ''){
			DeletaAssinatura($rowAssoc->ID_IUGU_ASSINATURA);
		}	
			//$Assinatura = AlteraAssinatura(trim($rowAssoc->ID_IUGU_ASSINATURA),date("d/m/Y", strtotime('+ 5 days')),$valorFatura,$tipo);
		
		$Assinatura = CriaAssinatura($cadastroCliente['DADOS']['id'],date("d/m/Y", strtotime('+ 5 days')),$valorFatura,$tipo);
		
		
		if($Assinatura['STATUS']=='OK'){
			$retorno['LINK'] = $Assinatura['DADOS']['recent_invoices'][0]['secure_url'];
			$query = 'UPDATE VND1000_ON set ID_IUGU_ASSINATURA='.aspas($Assinatura['DADOS']['id']).', ID_IUGU ='.aspas($cadastroCliente['DADOS']['id']).', LINK_IUGU='.aspas($Assinatura['DADOS']['recent_invoices'][0]['secure_url']).' WHERE CODIGO_ASSOCIADO = ' . aspas($associado); 
			jn_query($query);
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'Erro ao criar assinatura na IUGU (1)';
			$retorno['ERROS']  = $Assinatura['ERROS'];
		}
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Erro ao cadastrar usuario na IUGU (2)';
		$retorno['ERROS']  =  $cadastroCliente['ERROS'];
	}
	

}
echo json_encode($retorno);



function retornaValorPrevisao($codigoAssociado)
{

	$queryDados = 'Select  VND1000_ON.DATA_NASCIMENTO, VND1000_ON.CODIGO_TABELA_PRECO, VND1000_ON.CODIGO_PLANO
				   FROM VND1000_ON
				   WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);

	$resDados   = jn_query($queryDados);
	$rowDados   = jn_fetch_object($resDados);

	$date       = new DateTime($rowDados->DATA_NASCIMENTO);
	$interval   = $date->diff( new DateTime( date('Y-m-d') ) );
	$idade      = $interval->format('%Y');

	$queryTabelas = 'Select coalesce(VALOR_PLANO,0) VALOR_PLANO From Ps1032 
					 WHERE CODIGO_PLANO = ' . numSql($rowDados->CODIGO_PLANO) . ' AND CODIGO_TABELA_PRECO = ' . numSql($rowDados->CODIGO_TABELA_PRECO) . 
					' AND IDADE_MINIMA <= ' . numSql($idade) . ' and IDADE_MAXIMA >= ' . numSql($idade);

	$resTabelas   = jn_query($queryTabelas);
	$rowTabelas   = jn_fetch_object($resTabelas);
	
	return trim($rowTabelas->VALOR_PLANO);
	
}
?>