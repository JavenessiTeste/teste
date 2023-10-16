<?php
require('base.php');
require 'MyLogPHP.php';

$chaveClickSign = retornaValorConfiguracao('CHAVE_CLICKSIGN');
$homolClickSign = retornaValorConfiguracao('HOMOLOGACAO_CLICKSIGN');
$dados = file_get_contents("php://input");
$dados = json_decode($dados, True);

$nomeEvento = $dados['event']['name'];
$codigoClickSign = $dados['document']['key'];

$codigoEmpresa = 400;
$codigoAssociado = '';

if($nomeEvento == 'sign'){
	$log = new MyLogPHP('logwebhookClicksign.csv',';');
	$log->info('Inicio LOG.'); 
	$log->info('nomeEvento',$nomeEvento);			
	$log->info('codigoClickSign',$codigoClickSign);	
	$log->info('Fim LOG.'); 
}

if($nomeEvento == 'sign'){
	$queryAssoc  = ' SELECT * FROM VND1000_ON ';
	$queryAssoc .= ' INNER JOIN VND1002_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1002_ON.CODIGO_ASSOCIADO)';
	$queryAssoc .= ' WHERE VND1002_ON.CHAVE_DOC_CLICKSIGN =' . aspas($codigoClickSign);
	$queryAssoc .= ' AND VND1000_ON.CODIGO_GRUPO_CONTRATO =' . aspas('28');
	$queryAssoc .= ' AND VND1000_ON.CODIGO_ASSOCIADO NOT IN (SELECT COALESCE(CODIGO_ANTIGO,"") FROM PS1000) ';
	$resAssoc = jn_query($queryAssoc);
	if($rowAssoc = jn_fetch_object($resAssoc)){
		$codigoAssociado = $rowAssoc->CODIGO_ASSOCIADO;		
		$codigoGrupoContrato = $rowAssoc->CODIGO_GRUPO_CONTRATO;	
		importaTitular($codigoAssociado, $codigoGrupoContrato);
	}
}



function importaTitular($codigoAssociado, $codigoGrupoContrato){
	global $codigoEmpresa;
	
	$codigoSequencial = jn_gerasequencial('PS1000');
	$codigoTitularGerado = '01'.substr($codigoEmpresa, 0, 3).str_pad(substr($codigoSequencial, 0, 7), 7, "0", STR_PAD_LEFT).'000';
	
	$campos = ' CODIGO_PLANO, NOME_ASSOCIADO, DATA_NASCIMENTO, DATA_ADMISSAO, DATA_DIGITACAO, SEXO, NOME_MAE, ';
	$campos .= ' TIPO_ASSOCIADO, CODIGO_PARENTESCO, NUMERO_CPF, NUMERO_RG, NATUREZA_RG, ORGAO_EMISSOR_RG, ';	
	$campos .= ' CODIGO_CNS, NUMERO_DECLARACAO_NASC_VIVO, DESCRICAO_OBSERVACAO, CODIGO_ESTADO_CIVIL  ';	
	
	$queryInsertPS1000 = "insert into Ps1000(CODIGO_EMPRESA,
											 CODIGO_ASSOCIADO,
											 CODIGO_TITULAR,
											 CODIGO_SEQUENCIAL,
											 CODIGO_ANTIGO,											 
											 CODIGO_GRUPO_CONTRATO,
											 FLAG_PLANOFAMILIAR,
											 CODIGO_TABELA_PRECO,
											 NUMERO_DEPENDENTE,
											 PROFISSAO, ".
											 $campos.")
						 select ".aspas($codigoEmpresa).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoSequencial).",".
								  aspas($codigoAssociado)."," .
								  aspas($codigoGrupoContrato).","
								  ." 'S', COALESCE(CODIGO_TABELA_PRECO,CODIGO_PLANO), 0, MATRICULA, ".								  
								  $campos." FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ".aspas($codigoAssociado);	

	
	if(!jn_query($queryInsertPS1000)){
		echo 'Erro: ';
		pr($queryInsertPS1000);
		exit;
	}
	
	importaEnderecoAssociado($codigoEmpresa,$codigoTitularGerado,$codigoAssociado);
	importaContratoAssociado($codigoTitularGerado,$codigoAssociado);
	importaTelefoneAssociado($codigoTitularGerado,$codigoAssociado);

	$queryDep =' SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE TIPO_ASSOCIADO='.aspas('D').' and CODIGO_TITULAR ='.aspas($codigoAssociado);
	$resDep  = jn_query($queryDep);
	
	$i=1;
	while($rowDep  = jn_fetch_object($resDep)){
		importaDependente($codigoEmpresa,$codigoTitularGerado,$rowDep->CODIGO_ASSOCIADO,$codigoSequencial,$i, $codigoGrupoContrato);
		$i++;
	}
	
	atualizaProposta($codigoAssociado);
	
	return $codigoTitularGerado; 	
	
}

function importaDependente($codigoEmpresa,$codigoTitularGerado,$codigoAssociado,$codigoSequencial,$numeroDependente, $codigoGrupoContrato){
	
	$codigoAssociadoGerado = '01'.substr($codigoEmpresa, 0, 3).str_pad(substr($codigoSequencial, 0, 7), 7, "0", STR_PAD_LEFT).str_pad(substr($numeroDependente, 0, 2), 2, "0", STR_PAD_LEFT).'0';
	
	$campos  = ' CODIGO_PLANO, NOME_ASSOCIADO, DATA_NASCIMENTO, DATA_ADMISSAO, DATA_DIGITACAO, SEXO, NOME_MAE, ';
	$campos .= ' TIPO_ASSOCIADO, CODIGO_PARENTESCO, NUMERO_CPF, NUMERO_RG, NATUREZA_RG, ORGAO_EMISSOR_RG, ';	
	$campos .= ' CODIGO_CNS, NUMERO_DECLARACAO_NASC_VIVO, DESCRICAO_OBSERVACAO, CODIGO_ESTADO_CIVIL ';	
	
	$queryInsertPS1000 = "INSERT INTO PS1000(CODIGO_EMPRESA,
											 CODIGO_ASSOCIADO,
											 CODIGO_TITULAR,
											 CODIGO_SEQUENCIAL,
											 CODIGO_ANTIGO,		
											 CODIGO_GRUPO_CONTRATO,										 
											 FLAG_PLANOFAMILIAR, 
											 CODIGO_TABELA_PRECO, 
											 NUMERO_DEPENDENTE,".
											 $campos.")
						 select ".aspas($codigoEmpresa).",".
								  aspas($codigoAssociadoGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoSequencial).",".
								  aspas($codigoAssociado).",".
								  aspas($codigoGrupoContrato).",".								  								  
								  aspas('S').",".
								  " COALESCE(CODIGO_TABELA_PRECO,CODIGO_PLANO), ".
								  aspas($numeroDependente).",".
								  $campos." FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ".aspas($codigoAssociado);
	
	if(!jn_query($queryInsertPS1000)){
		echo 'Erro: ';
		pr($queryInsertPS1000);
		exit;
	}
}

function importaEnderecoAssociado($codigoEmpresa,$codigoAssociadoGerado,$codigoAssociado){

	$campos = 'ENDERECO, BAIRRO, CIDADE, CEP, ESTADO, ENDERECO_EMAIL ';
	
	$queryInsertPS1001 = " 	INSERT INTO PS1001(
								CODIGO_EMPRESA,
								CODIGO_ASSOCIADO,
								".$campos.")
							SELECT ".
								aspas($codigoEmpresa).",".
								aspas($codigoAssociadoGerado).",".
								$campos.
							" FROM VND1001_ON WHERE CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	
	if(!jn_query($queryInsertPS1001)){
		echo 'Erro: ';
		pr($queryInsertPS1001);
		exit;
	}

}

function importaContratoAssociado($codigoAssociadoGerado,$codigoAssociado){

	$campos = 'NUMERO_CONTRATO, FLAG_DEBITO_AUTOMATICO, CODIGO_BANCO, NUMERO_AGENCIA, NUMERO_CONTA';
		
	$queryInsertPS1002 = "	INSERT INTO PS1002(
								CODIGO_ASSOCIADO,
								DIA_VENCIMENTO, ".
								$campos.")
							SELECT ".
								aspas($codigoAssociadoGerado). "," .
								aspas('01'). "," .
								$campos." 
							FROM VND1001_ON WHERE CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	
	if(!jn_query($queryInsertPS1002)){
		echo 'Erro: ';
		pr($queryInsertPS1002);
		exit;
	}
	

}

function importaTelefoneAssociado($codigoAssociadoGerado,$codigoAssociado){
	
	$queryInsertPS1006 = "	INSERT INTO PS1006 (								
								CODIGO_ASSOCIADO,
								INDICE_TELEFONE,
								CODIGO_AREA,
								NUMERO_TELEFONE)
							SELECT ".
								aspas($codigoAssociadoGerado).",".
								aspas('1').",".
								" substring(NUMERO_TELEFONE_01 from 2 for 2)" ."," .
								" substring(NUMERO_TELEFONE_01 from 6 for 14)" .
								" FROM VND1001_ON WHERE CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	jn_query($queryInsertPS1006);
	
}

function atualizaProposta($codigoTitular){
	jn_query('UPDATE VND1000_ON SET ULTIMO_STATUS = ' . aspas('CONCLUIDO') . ' WHERE CODIGO_TITULAR = ' . aspas($codigoTitular));
}
?>