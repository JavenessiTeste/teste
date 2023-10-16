<?php
require('../lib/base.php');

$codAssociadoTmp = $dadosInput['codAssociado'];

if($dadosInput['tipo']== 'dados'){
	$queryAssociado  = ' SELECT ';	
	$queryAssociado .= ' 	A.CODIGO_ASSOCIADO, B.ENDERECO, A.CODIGO_PLANO, A.NOME_ASSOCIADO, B.NUMERO_CONTRATO, A.CODIGO_GRUPO_CONTRATO, A.ULTIMO_STATUS ';
	$queryAssociado .= ' FROM VND1000_ON A ';	
	$queryAssociado .= ' LEFT OUTER JOIN VND1001_ON B ON (A.CODIGO_ASSOCIADO = B.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' WHERE A.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssociado = jn_query($queryAssociado);

	$rowAssociado = jn_fetch_object($resAssociado);
	$dadosAssoc['ASSOCIADO'] = $rowAssociado->CODIGO_ASSOCIADO;
	$dadosAssoc['ENDERECO'] = jn_utf8_encode($rowAssociado->ENDERECO);
	$dadosAssoc['CODIGO_PLANO'] = $rowAssociado->CODIGO_PLANO;	
	$dadosAssoc['NOME_ASSOCIADO'] = jn_utf8_encode($rowAssociado->NOME_ASSOCIADO);
	$dadosAssoc['NUMERO_CONTRATO'] = $rowAssociado->NUMERO_CONTRATO;	
	$dadosAssoc['CODIGO_GRUPO_CONTRATO'] = $rowAssociado->CODIGO_GRUPO_CONTRATO;	
	$dadosAssoc['ULTIMO_STATUS'] = $rowAssociado->ULTIMO_STATUS;	
	
	foreach ($dadosAssoc as $value){
		$retorno[]=$value;
	} 
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'geraCodigoDependente'){
	
	$query = "SELECT count(*) QUANTIDADE from VND1000_ON where CODIGO_TITULAR = " . aspas($dadosInput['codTitular']);
	$res  = jn_query($query);
	
	if ($row = jn_fetch_object($res)) 
		$numeroDependente = $row->QUANTIDADE;
	
	$codDep = explode('.',$dadosInput['codTitular']);
	$codDep = $codDep[0] . '.' . $numeroDependente;
	
	$dadosAssoc['CODIGO_DEPENDENTE'] = $codDep;
}

if($dadosInput['tipo'] == 'pesquisaCep'){

	$retorno = Array();

	if($dadosInput['numeroCep']){
		
		$json_file = file_get_contents('http://viacep.com.br/ws/'. retiraCaractere($dadosInput['numeroCep']) .'/json');   
		$json_str = json_decode($json_file, true);	
		
		$dadosEndereco['CEP'] = $json_str['cep'];
		$dadosEndereco['ENDERECO'] = jn_utf8_encode(strToUpper(retiraCaractere($json_str['logradouro'])));
		$dadosEndereco['BAIRRO'] = jn_utf8_encode(strToUpper(retiraCaractere($json_str['bairro'])));
		$dadosEndereco['CIDADE'] = jn_utf8_encode(strToUpper(retiraCaractere($json_str['localidade'])));
		$dadosEndereco['UF'] = jn_utf8_encode(strToUpper($json_str['uf']));	

		foreach ($dadosEndereco as $value){
			$retorno[]=$value;
		} 
	}else{
		$retorno = false;
	}


	echo json_encode($retorno);
}


if($dadosInput['tipo'] == 'usaEstruturaGrupoContrato'){
	
	$temEstrutura = 'N';

	$apresentarCombo = retornaValorConfiguracao('COMBO_GRUPO_CONTRATO');
	
	if ($apresentarCombo == 'SIM')
	   $temEstrutura = 'S'; 
	
	$retornoValidado['TEMESTRUTURA'] = $temEstrutura;

	foreach ($retornoValidado as $value){
		$retorno[]=$value;
	} 

	echo json_encode($retorno);
}

if($dadosInput['tipo'] == 'tipoEntidade'){
	
	$tipoEntidade = 'CODIGO_GRUPO_CONTRATO';

	if(retornaValorConfiguracao('FORCA_USO_GRUPO_PESSOAS_AUTOC') == 'SIM'){		
		$tipoEntidade = 'CODIGO_GRUPO_PESSOAS';
	}	
	
	$retornoValidado['TIPO_ENTIDADE'] = $tipoEntidade;

	$retorno = $retornoValidado['TIPO_ENTIDADE'];
	

	echo json_encode($retorno);
}


if($dadosInput['tipo'] == 'comboEntidade'){
	$retorno = Array();
	$Grupos = Array();
	
	if(retornaValorConfiguracao('FORCA_USO_GRUPO_PESSOAS_AUTOC') == 'SIM'){
		$queryGrupos  = ' SELECT PS1014.CODIGO_GRUPO_PESSOAS CODIGO_GRUPO, NOME_GRUPO_PESSOAS DESC_GRUPO FROM PS1014 '; 
		$queryGrupos .= ' WHERE CODIGO_GRUPO_PESSOAS IN (SELECT CODIGO_GRUPO_PESSOAS FROM VND1030CONFIG_ON) '; 
	}else{		
		$codigosGrupoContrato = retornaValorConfiguracao('CODIGOS_GRUPO_CONTRATO');
		
		$queryGrupos = " SELECT CODIGO_GRUPO_CONTRATO CODIGO_GRUPO, DESCRICAO_GRUPO_CONTRATO DESC_GRUPO FROM ESP0002 ";
		
		if($codigosGrupoContrato){
			$queryGrupos .= " WHERE CODIGO_GRUPO_CONTRATO IN (". $codigosGrupoContrato . ")";		
		}
		

		$queryGrupos .= " ORDER BY DESC_GRUPO ";				
		
	}
	$resGrupos = jn_query($queryGrupos);
		
	while($rowGrupos = jn_fetch_object($resGrupos)){		
		$Grupos['VALOR'] 	= $rowGrupos->CODIGO_GRUPO;
		$Grupos['DESC'] 	= jn_utf8_encode($rowGrupos->DESC_GRUPO);
		$retorno[] = $Grupos;
	}
		
	echo json_encode($retorno);
	
}

function retiraCaractere($str) {
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);
	$str = preg_replace('/[,(),;:.|!"#$%&?~^><ªº-]/', '', $str);
    //$str = preg_replace('/[^a-z0-9]/i', '', $str);
    $str = preg_replace('/_+/', '', $str);
    
    return $str;
}

?>