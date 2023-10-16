<?php
require('../lib/base.php');
require('../lib/MyLogPHP.php');

$codigoAssociadoVnd = $_POST['codigoAssociadoVnd'] ? $_POST['codigoAssociadoVnd'] : $_GET['codigoAssociadoVnd'];
$codigoEmpresa = 400;

$log = new MyLogPHP('../../ServidorCliente/RetornoImportacaoRegistros/registrosImportados.csv',';');
$log->info('Inicio LOG.'); 	
$log->info('codigoAssociadoVnd',$codigoAssociadoVnd);	
$log->info('Fim LOG.');


if($codigoAssociadoVnd)
	importaTitular($codigoAssociadoVnd);

function importaTitular($codigoTitular){
	global $codigoEmpresa;
	
	$codigoSequencial = jn_gerasequencial('PS1000');
	$codigoTitularGerado = '01'.substr($codigoEmpresa, 0, 3).str_pad(substr($codigoSequencial, 0, 7), 7, "0", STR_PAD_LEFT).'000';
	
	$campos  = ' CODIGO_PLANO, NOME_ASSOCIADO, DATA_NASCIMENTO, DATA_ADMISSAO, DATA_DIGITACAO, SEXO, NOME_MAE, ';
	$campos .= ' TIPO_ASSOCIADO, CODIGO_PARENTESCO, NUMERO_CPF, NUMERO_RG, NATUREZA_RG, ORGAO_EMISSOR_RG, ';	
	$campos .= ' CODIGO_CNS, NUMERO_DECLARACAO_NASC_VIVO, DESCRICAO_OBSERVACAO, CODIGO_ESTADO_CIVIL  ';	
	
	$queryInsertPS1000 = "insert into Ps1000(CODIGO_EMPRESA,
											 CODIGO_ASSOCIADO,
											 CODIGO_TITULAR,
											 CODIGO_SEQUENCIAL,
											 CODIGO_ANTIGO,
											 FLAG_PLANOFAMILIAR,
											 CODIGO_TABELA_PRECO,
											 NUMERO_DEPENDENTE, ".
											 $campos.")
						 select ".aspas($codigoEmpresa).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoSequencial).",".
								  aspas($codigoTitular)."," .
								  " 'S', COALESCE(CODIGO_TABELA_PRECO,CODIGO_PLANO), 0, ".								  
								  $campos." FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ".aspas($codigoTitular);
	
	if(!jn_query($queryInsertPS1000)){
		echo 'Erro: ';
		pr($queryInsertPS1000);
		exit;
	}
	
	importaEnderecoAssociado($codigoEmpresa,$codigoTitularGerado,$codigoTitular);
	importaContratoAssociado($codigoTitularGerado,$codigoTitular);
	importaTelefoneAssociado($codigoTitularGerado,$codigoTitular);
	geraLoginPortal($codigoTitularGerado);

	if(retornaValorConfiguracao('BOAS_VINDAS_APOS_EFETIVACAO') == 'SIM')
		enviaEmailBoasVindas($codigoTitularGerado);

	$queryDep =' SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE TIPO_ASSOCIADO='.aspas('D').' and CODIGO_TITULAR ='.aspas($codigoTitular);
	$resDep  = jn_query($queryDep);
	
	$i=1;
	while($rowDep  = jn_fetch_object($resDep)){
		importaDependente($codigoEmpresa,$codigoTitularGerado,$rowDep->CODIGO_ASSOCIADO,$codigoSequencial,$i, $codigoGrupoContrato);
		$i++;
	}
	
	atualizaProposta($codigoTitular);
	
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

function geraLoginPortal($codigoAssociadoGerado){
	
	$queryAssoc = 'SELECT NUMERO_CPF FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociadoGerado);
	$resultado = qryUmRegistro($queryAssoc);

	$insertLogin  = ' INSERT INTO CFGLOGIN_DINAMICO_NET (USUARIO, CODIGO_IDENTIFICACAO, PERFIL_OPERADOR, SENHA_ACESSO) VALUES ';
	$insertLogin .= ' ( ' . aspas($resultado->NUMERO_CPF) . ', ' . aspas($codigoAssociadoGerado) . ' , ' . aspas('BENEFICIARIO') . ', ' . aspas($resultado->NUMERO_CPF) . ')';
	jn_query($insertLogin);
	
}

function enviaEmailBoasVindas($codigoAssociadoGerado){
	require_once('../EstruturaPrincipal/disparoEmail.php');

	$querySmart = 'SELECT CODIGO_SMART FROM CFGEMPRESA';
	$resultadoSmart = qryUmRegistro($querySmart);

	$queryDadosAssoc  = ' SELECT PS1000.NOME_ASSOCIADO, PS1001.ENDERECO_EMAIL, CFGLOGIN_DINAMICO_NET.USUARIO, CFGLOGIN_DINAMICO_NET.SENHA_ACESSO FROM PS1000 ';
	$queryDadosAssoc .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
	$queryDadosAssoc .= ' INNER JOIN CFGLOGIN_DINAMICO_NET ON (PS1000.CODIGO_ASSOCIADO = CFGLOGIN_DINAMICO_NET.CODIGO_IDENTIFICACAO) ';
	$queryDadosAssoc .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociadoGerado);
	$resultado = qryUmRegistro($queryDadosAssoc);

	$corpoEmail = 'Mensagem não parametrizada para a empresa. ';

	if($resultadoSmart->CODIGO_SMART == '4318'){//Somar
		$corpoEmail  = ' Ol&aacute;! <br> ';
		$corpoEmail .= ' Ficamos muito felizes em receber a sua assinatura, &eacute; um prazer ter voc&ecirc; como nosso cliente! <br> ';
		$corpoEmail .= ' Obrigado por escolher a Somar+Sa&uacute;de. <br> ';
		$corpoEmail .= ' Agora, a melhor parte: voc&ecirc; j&aacute; pode mergulhar no nosso Portal de Usu&aacute;rio e explorar todos os benef&iacute;cios da sua assinatura. <br> ';
		$corpoEmail .= ' Abaixo est&atilde;o as suas informa&ccedil;&otilde;es de acesso: <br> ';
		$corpoEmail .= ' URL do Portal: <a href="https://portal.somarmaissaude.com/SomarMaisSaude/Site/autenticacao/login?p=BENEFICIARIO"> Clique Aqui </a> <br>';
		$corpoEmail .= ' Nome de usu&aacute;rio: ' . $resultado->USUARIO . ' <br> ';
		$corpoEmail .= ' Senha:' . $resultado->SENHA_ACESSO . ' <br> ';
		$corpoEmail .= ' Quando entrar pela primeira vez, voc&ecirc; dever&aacute; redefinir sua senha para uma que seja totalmente de sua escolha. <br> ' ;
		$corpoEmail .= ' Isso &eacute; necess&aacute;rio para manter tudo seguro e protegido. <br> ' ;
		$corpoEmail .= ' Se voc&ecirc; tiver qualquer d&uacute;vida, a nossa equipe de suporte estar&aacute; pronta para te ajudar. <br> ';
		$corpoEmail .= ' Basta entrar em contato conosco atrav&eacute;s do e-mail atendimento@somarmaissaude.com . <br><br> ';
		$corpoEmail .= ' Mais uma vez, agradecemos a confian&ccedil;a. <br> ';
		$corpoEmail .= ' Estamos comprometidos em oferecer o melhor servi&ccedil;o poss&iacute;vel e &agrave; disposi&ccedil;&atilde;o para ajud&aacute;-lo em todas as suas necessidades. <br>';
		$corpoEmail .= ' Atenciosamente, <br> ';
		$corpoEmail .= ' Equipe Somar+Sa&uacute;de ';
	}

	$retorno = disparaEmail($resultado->ENDERECO_EMAIL, 'Boas Vindas', $corpoEmail);

	if($retorno['STATUS'] == 'OK'){
		return true;
	}else{
		echo $retorno['MSG'];
		return false;
	}
}

function atualizaProposta($codigoTitular){
	jn_query('UPDATE VND1000_ON SET ULTIMO_STATUS = ' . aspas('CONCLUIDO') . ' WHERE CODIGO_TITULAR = ' . aspas($codigoTitular));
}
?>