<?php

require('../lib/base.php');


$token =  '';

$dadosAuth = Array();
$dadosAuth['clientId'] = retornaValorConfiguracao('CLIENT_ID_EPHARMA');
$dadosAuth['clientSecret'] = retornaValorConfiguracao('CLIENT_SECRET_EPHARMA');
$dadosAuth['username'] = retornaValorConfiguracao('USERNAME_EPHARMA');
$dadosAuth['password'] = retornaValorConfiguracao('PASSWORD_EPHARMA');
$homolEpharma = retornaValorConfiguracao('HOMOLOGACAO_EPHARMA');

$query  = " SELECT  PS1000.CODIGO_ASSOCIADO,PS1000.NOME_ASSOCIADO,PS1000.FLAG_API_VARIAVEL, PS1000.NUMERO_CPF, PS1000.TIPO_ASSOCIADO, PS1000.DATA_NASCIMENTO, PS1000.SEXO FROM PS1000 ";        
$query .= " WHERE PS1000.FLAG_API_VARIAVEL = 'S' ";
$resultado = jn_query($query);

while($row = jn_fetch_object($resultado)){

	if($token == ''){               
		$retornoAutentica = autentica_epharma($dadosAuth, $homolEpharma);
		$token = $retornoAutentica['token'];
        if(!$token){
            exit;
        }
	}

    $dadosCliente = Array();
    $dadosCliente['numeroCpf'] = sanitizeString($row->NUMERO_CPF);
    $dadosCliente['dtInicioVigencia'] = date('d/m/Y');
    $dadosCliente['tipoAssociado'] = $row->TIPO_ASSOCIADO;
    $dadosCliente['nome'] = $row->NOME_ASSOCIADO;
    $dadosCliente['dtNascimento'] = sqlToData($row->DATA_NASCIMENTO);
    $dadosCliente['sexo'] = $row->SEXO;
    $codigoEpharma = $row->CODIGO_ASSOCIADO;

    $retornoCartaoCliente = movimentacaoCartaoCliente($token, $homolEpharma, $codigoEpharma, $dadosCliente);

    if($retornoCartaoCliente['STATUS'] == 'OK' ){
        $query  = " UPDATE PS1000 SET FLAG_API_VARIAVEL = 'N' ";        
        $query .= " WHERE PS1000.CODIGO_ASSOCIADO = ".aspas($row->CODIGO_ASSOCIADO);
        $resultado = jn_query($query);
    }
    
}