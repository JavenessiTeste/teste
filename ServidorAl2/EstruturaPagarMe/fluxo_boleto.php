<?php
require('../lib/base.php');
require("vendor/autoload.php");

$empresa = $dadosInput['codigoEmpresa'];
$associado = $dadosInput['codigoTitular'];
$associado = '00085225.0';

$numeroCartao = $dadosInput['dadosCartao']['Numero'];
$nomeCartao = $dadosInput['dadosCartao']['Nome'];
$validadeCartao = $dadosInput['dadosCartao']['Vencimento'];
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

$pagarme = new PagarMe\Client($token);

$transaction = $pagarme->transactions()->create([
  'amount' => 100,
  'payment_method' => 'boleto',
  'postback_url' => $linkRetorno,
  'customer' => [
    'external_id' => $rowAssoc->CODIGO_ASSOCIADO,
    'name' => $rowAssoc->NOME_ASSOCIADO, 
    'email' => $rowAssoc->ENDERECO_EMAIL,
    'type' => 'individual',
      'country' => 'br',
      'documents' => [
        [
          'type' => 'cpf',
          'number' => $rowAssoc->NUMERO_CPF
        ]
      ],
      'phone_numbers' => [ '+551199999999' ]
  ]
]);
pr($transaction,true);


echo json_encode($retorno);
?>