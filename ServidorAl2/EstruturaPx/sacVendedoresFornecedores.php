<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../lib/sysutilsAlianca.php');


if($dadosInput['tipo'] =='dadosGrid')
{
	
	if ($dadosInput['pesquisarApenasValoresExatos'] == 'SIM')
	{
		$tipoPesquisa = ' = ';
		$valorPesquisar = aspas($dadosInput['valorPesquisar']);
	}
	else
	{
		$tipoPesquisa   = ' Like ';
		$valorPesquisar = aspas('%' . $dadosInput['valorPesquisar'] . '%');
	}


    if ($dadosInput['campoPesquisar']=='INFORMACOES_ADICIONAIS')
		$dadosInput['campoPesquisar'] = 'PS1100.NOME_USUAL'; // SÓ PARA NÃO DAR ERRO
	else if ($dadosInput['campoPesquisar']=='')
		$dadosInput['campoPesquisar'] = 'PS1100.NOME_USUAL'; // SÓ PARA NÃO DAR ERRO
	else
		$dadosInput['campoPesquisar'] = 'PS1100.' . $dadosInput['campoPesquisar'];

	$query    = 'Select top ' . $dadosInput['quantidadeResultados'] . ' PS1100.CODIGO_IDENTIFICACAO , 
	                          PS1100.NOME_USUAL, PS1100.DATA_CADASTRAMENTO, PS1100.DATA_EXCLUSAO, PS1100.TIPO_CADASTRO, 
	                          PS1100.TIPO_PESSOA, null INFORMACOES_ADICIONAIS ';

	$query  .= 'From PS1100 ';

	if ($dadosInput['campoPesquisar']!='')
	    $query    .= ' Where ' . $dadosInput['campoPesquisar'] . $tipoPesquisa . $valorPesquisar;

	if ($dadosInput['apresentarRegistrosAtivos']=='NAO')
		$query    .= ' and PS1100.DATA_EXCLUSAO is not null';

	if ($dadosInput['apresentarRegistrosExcluidos']=='NAO')
		$query    .= ' and PS1100.DATA_EXCLUSAO is null';

	if ($dadosInput['tipoConsulta']=='VENDEDORES_CORRETORAS')
		$query    .= ' and PS1100.Tipo_Cadastro in (' . aspas('Cadastro_Vendedores') . ',' . aspas('Cadastro_Supervisores') . ',' . 
	                                                    aspas('Cadastro_Corretoras') . ')';
	if ($dadosInput['tipoConsulta']=='FORNECEDORES')
		$query    .= ' and PS1100.Tipo_Cadastro in (' . aspas('Cadastro_Fornecedores') . ',' . aspas('----') . ')';


	if ($dadosInput['OrderBy']=='')
		$query    .= ' Order By PS1100.CODIGO_IDENTIFICACAO';
	else
		$query    .= ' Order By ' . $dadosInput['OrderBy'];

	$res = jn_query($query);

	while ($rowPrinc = jn_fetch_object($res))
	{

		$linha = Array();

		$linha['MARCADO'] 	 	          = 'S';
		$linha['CODIGO_IDENTIFICACAO']    = jn_utf8_encode_AscII($rowPrinc->CODIGO_IDENTIFICACAO);
		$linha['NOME_USUAL']              = jn_utf8_encode_AscII($rowPrinc->NOME_USUAL);
		$linha['DATA_CADASTRAMENTO'] 	  = SqlToData($rowPrinc->DATA_CADASTRAMENTO);
		$linha['DATA_EXCLUSAO']           = SqlToData($rowPrinc->DATA_EXCLUSAO);
		$linha['TIPO_CADASTRO']           = jn_utf8_encode_AscII($rowPrinc->TIPO_CADASTRO);
		$linha['INFORMACOES_ADICIONAIS']  = jn_utf8_encode_AscII($rowPrinc->INFORMACOES_ADICIONAIS);
		$linha['TIPO_PESSOA']             = jn_utf8_encode_AscII($rowPrinc->TIPO_PESSOA);

		$retorno[] = $linha;

	}

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='dadosRegistroSelecionado')
{
	
	$titulos    = array();
	$titulos[0] = 'Campo de informacao';
	$titulos[1] = 'Valor';

	/* Dados principais do prestador */

	$query    = 'Select PS1100.CODIGO_IDENTIFICACAO , 
	                          PS1100.NOME_USUAL, PS1100.DATA_CADASTRAMENTO, PS1100.DATA_EXCLUSAO, PS1100.TIPO_CADASTRO, 
	                          PS1100.TIPO_PESSOA, 
						      null INFORMACOES_ADICIONAIS, PS1101.ENDERECO, PS1101.BAIRRO, PS1101.CIDADE, PS1101.CEP, PS1101.ESTADO, 
						      PS1101.TELEFONE_PRINCIPAL, PS1101.TELEFONE_SECUNDARIO, PS1101.NUMERO_CELULAR, PS1101.ENDERECO_EMAIL, 
						      PS1102.NUMERO_CNPJ, PS1102.NUMERO_CPF, PS1102.NUMERO_INSC_ESTADUAL, PS1102.NUMERO_INSC_MUNICIPAL, 
						      PS1102.NUMERO_RG, PS1102.NUMERO_CONTA_CORRENTE, PS1102.CODIGO_BANCO, PS1102.NUMERO_AGENCIA 
						 From PS1100 
						 LEFT OUTER JOIN PS1101 ON (PS1100.CODIGO_IDENTIFICACAO = PS1101.CODIGO_IDENTIFICACAO)
						 LEFT OUTER JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO)
					      where (PS1100.CODIGO_IDENTIFICACAO = ' . aspas($dadosInput['codigo']) . ')  ';

	$res   = jn_query($query);
	$linha = Array();

	$linha['INFORMACOES_CADASTRAIS']   = iniciaTabelaVertical(2,$titulos);

	$qtCamposConsulta = jn_num_fields($res);

	while ($row = jn_fetch_object($res))
	{
	    $linha['INFORMACOES_CADASTRAIS'] .= retornaValoresEmTabelaVertical($qtCamposConsulta,$res, $row);
	}

	$linha['INFORMACOES_CADASTRAIS'] .= finalizaTabelaVertical();

	$retorno[] = $linha;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='HTML_FINANCEIRO')
{

	$query  = 'Select TOP 100 COALESCE(PS7200.CODIGO_IDENTIFICACAO, PS7200.CODIGO_IDENTIFICACAO) CODIGO_IDENTIFICACAO, ps7200.DESCRICAO_CONTA, 
	                  PS7201.VALOR_CONTA, PS7201.DATA_VENCIMENTO, 
	                  PS7201.DATA_PREVISAO, PS7201.DATA_PAGAMENTO, PS7201.VALOR_PAGO, PS7201.OBSERVACAO_SIMPLES, 
	                  PS7201.NUMERO_NOTA_FISCAL, PS7201.MES_ANO_REFERENCIA, PS7201.DATA_EMISSAO 
				   from PS7200
				   INNER JOIN PS7201 ON (PS7200.CODIGO_CONTA = PS7201.CODIGO_CONTA)
				   where (PS7200.CODIGO_IDENTIFICACAO = ' . aspas($dadosInput['codigo']) . ') Order By PS7201.Data_Vencimento Desc';

	$totalizar                        = Array();
	$totalizar['VALOR_CONTA']  = 0;
	$totalizar['VALOR_PAGO'] = 0;

	$linha                   = Array();
	$linha['HTML_RETORNO']   = montaTabelaHorizontalBaseadoNaQuery($query,'',$totalizar);
	$linha['TITULO_RETORNO'] = 'Consulta dos últimos 100 Pagamentos e financeiro';
	$retorno[] 				 = $linha;

	echo json_encode($retorno);

}





?>