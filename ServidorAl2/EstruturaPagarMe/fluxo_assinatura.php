<?php
require('../lib/base.php');
require("vendor/autoload.php");

$empresa = $dadosInput['codigoEmpresa'];
$associado = $dadosInput['codigoTitular'];

$numeroCartao = $dadosInput['dadosCartao']['Numero'];
$nomeCartao = $dadosInput['dadosCartao']['Nome'];
$validadeCartao = $dadosInput['dadosCartao']['Vencimento'];
$validadeCartao = str_replace("/","",$validadeCartao);
$codigoSeguranca = $dadosInput['dadosCartao']['CodigoSeg'];
$retorno = Array();

$token = retornaValorConfiguracao('CHAVE_PAGARME');

$queryAssoc  = ' SELECT * FROM VND1000_ON ';
$queryAssoc .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
$queryAssoc .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($associado);
$resAssoc = jn_query($queryAssoc);
$rowAssoc = jn_fetch_object($resAssoc);

$endereco = explode(',',$rowAssoc->ENDERECO);
$rua = $endereco[0];

$comp = explode('-',$endereco[1]);
$numero = $comp[0];
$complemento = $comp[1];	

$linkRetorno = retornaValorConfiguracao('LINK_RETORNO_PAGARME');

$valorFat1020 = 0;
$valorFatura = 0;

$queryCodigos = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($associado);
$resCodigos = jn_query($queryCodigos);
while($rowCodigos = jn_fetch_object($resCodigos)){
	$valorAssoc = retornaValorPrevisao($rowCodigos->CODIGO_ASSOCIADO);
	$valorFatura = $valorFatura + $valorAssoc;
}

$valorFat1020 = $valorFatura;

$valorFatura = str_replace(',','',$valorFatura);
$valorFatura = str_replace('.','',$valorFatura);
$valorFatura = ($valorFatura * 10);

$queryPlano = 'SELECT CODIGO_PLANO_PAGARME FROM ESP_PLANOS_PAGARME WHERE VALOR_PLANO = ' . aspas($valorFatura);
$resPlano = jn_query($queryPlano);
$rowPlano = jn_fetch_object($resPlano);
$idPlano = $rowPlano->CODIGO_PLANO_PAGARME;
if($idPlano == ''){
	$idPlano = criaPlano($valorFatura);
}

$pagarme = new PagarMe\Client($token);

$subscription = $pagarme->subscriptions()->create([
  'plan_id' => $idPlano,
  'payment_method' => 'credit_card',
  'card_number' => $numeroCartao,
  'card_holder_name' => $nomeCartao,
  'card_expiration_date' => $validadeCartao,
  'card_cvv' => $codigoSeguranca,
  'postback_url' => $linkRetorno,
  'customer' => [
	'email' => $rowAssoc->ENDERECO_EMAIL,
	'name' => $rowAssoc->NOME_ASSOCIADO,
	'document_number' => $rowAssoc->NUMERO_CPF,
	'address' => [
	  'street' => $rua,
	  'street_number' => $numero,
	  'complementary' => $complemento,
	  'neighborhood' => $rowAssoc->BAIRRO,
	  'zipcode' => $rowAssoc->CEP
	],
	'phone' => [
	  'ddd' => '01',
	  'number' => '923456780'
	],
	'sex' => 'other',
	'born_at' => '1970-01-01',
  ],
  'metadata' => [
	'foo' => 'bar'
  ]
]);

if($subscription->card->id){
	$queryCard = 'UPDATE VND1000_ON SET CARTAO_PAGARME = ' . aspas($subscription->card->id) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($associado);
	
	if(jn_query($queryCard)){
		
		if($subscription->current_transaction->status == 'paid'){
			$queryVnd1020  = ' INSERT INTO VND1020_ON (CODIGO_ASSOCIADO, CODIGO_EMPRESA, DATA_VENCIMENTO, DATA_PAGAMENTO, VALOR_PAGO, VALOR_FATURA, DATA_EMISSAO, MES_ANO_REFERENCIA, IDENTIFICACAO_GERACAO, NUMERO_PARCELA) ';
			$queryVnd1020 .= ' VALUES ';
			$queryVnd1020 .= " (" . aspas($associado) .  ", " . aspas($empresa) . ", current_timestamp, current_timestamp, " . aspas($valorFat1020) . "," . aspas($valorFat1020) . ", current_timestamp, "; 
			$queryVnd1020 .= " EXTRACT(MONTH FROM current_timestamp) || '/' || EXTRACT(YEAR FROM current_timestamp), 'FAT_VND', " . aspas('1') . ")";		
		}else{			
			$queryVnd1020  = ' INSERT INTO VND1020_ON (CODIGO_ASSOCIADO, CODIGO_EMPRESA, DATA_VENCIMENTO, VALOR_FATURA, DATA_EMISSAO, MES_ANO_REFERENCIA, IDENTIFICACAO_GERACAO, NUMERO_PARCELA) ';
			$queryVnd1020 .= ' VALUES ';
			$queryVnd1020 .= " (" . aspas($associado) .  ", " . aspas($empresa) . ", current_timestamp, " . aspas($valorFat1020) . ", current_timestamp, "; 
			$queryVnd1020 .= " EXTRACT(MONTH FROM current_timestamp) || '/' || EXTRACT(YEAR FROM current_timestamp), 'FAT_VND', " . aspas('1') . ")";		
		}
		
		
		jn_query($queryVnd1020);
		
		$filtro = '';	
		if($empresa == '400'){
			$filtro = ' AND CODIGO_ASSOCIADO = ' . aspas($associado);
		}else{
			$filtro = ' AND CODIGO_EMPRESA = ' . aspas($empresa);
		}
		
		$query1020  = ' SELECT FIRST 1 NUMERO_REGISTRO FROM VND1020_ON ';	
		$query1020 .= ' WHERE 1=1 ';
		$query1020 .= $filtro;
		$query1020 .= ' ORDER BY NUMERO_REGISTRO DESC ';
		$res1020 = jn_query($query1020);
		$row1020 = jn_fetch_object($res1020);
		$numRegFat = $row1020->NUMERO_REGISTRO;	
		
		$queryCodigos = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($associado);
		$resCodigos = jn_query($queryCodigos);
		while($rowCodigos = jn_fetch_object($resCodigos)){
			$valorAssoc = retornaValorPrevisao($rowCodigos->CODIGO_ASSOCIADO);
			
			$queryVnd1021  = ' INSERT INTO VND1021_ON (CODIGO_ASSOCIADO, CODIGO_EMPRESA, NUMERO_REGISTRO_PS1020, DATA_EMISSAO, MES_ANO_VENCIMENTO, VALOR_FATURA) ';
			$queryVnd1021 .= ' VALUES ';
			$queryVnd1021 .= " (" . aspasNull($associado) .  ", " . aspas($empresa) . ", " . aspas($numRegFat) . ", current_timestamp, EXTRACT(MONTH FROM current_timestamp) || '/' || EXTRACT(YEAR FROM current_timestamp), " . aspas($valorAssoc) . ")";			
			jn_query($queryVnd1021);
		}
		
		$retorno['STATUS'] = 'OK';
		$retorno['MSG']    = 'A assinatura foi realizada com sucesso!!!';
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'A assinatura foi realizada, mas o código nao foi cadastrado no Aliança.';
	}
}else{
	$retorno['STATUS'] = 'ERRO';
	$retorno['MSG']    = 'Não foi possível realizar a assinatura.';
}

echo json_encode($retorno);


function criaPlano($valor){
	global $token;
	
	$pagarme = new PagarMe\Client($token);

	$codPlano = $pagarme->plans()->create([
	  'amount' => $valor,
	  'days' => '30',
	  'name' => 'Plano ' . $valor
	]);
	
	$insertPlano  = 'INSERT INTO ESP_PLANOS_PAGARME (CODIGO_PLANO_PAGARME, DATA_CRIACAO, VALOR_PLANO) VALUES ';
	$insertPlano .= '( ' . aspas($codPlano->id) . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas($valor) . ' )';
	jn_query($insertPlano);
	
	return $codPlano->id;
}

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