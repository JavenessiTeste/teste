<?php
require('../lib/base.php');

if ($dadosInput['codigoAssociadoPs1000']!='')
{
	$tabelaPs1000Vnd1000  = 'PS1000';
	$tabelaPs1005Vnd1005 = 'PS1005';
	$codigoIdBeneficiario = $dadosInput['codigoAssociadoPs1000'];
	$campoFiltragem        = 'CODIGO_ASSOCIADO';
}
else
{
	$codigoIdBeneficiario  = $_SESSION['codigoIdentificacao'];
	$tabelaPs1000Vnd1000   = 'VND1000_ON';		
	$tabelaPs1005Vnd1005   = 'VND1005_ON';		
	$campoFiltragem        = 'CODIGO_TITULAR';
}


if($dadosInput['tipo']== 'dados')
{

	$queryAssociados  = ' SELECT ';
	$queryAssociados .= '	' . $tabelaPs1000Vnd1000 . '.CODIGO_ASSOCIADO, ' . $tabelaPs1000Vnd1000 . '.NOME_ASSOCIADO, ' . $tabelaPs1000Vnd1000 . '.PESO, ' . $tabelaPs1000Vnd1000 . '.ALTURA ';
	$queryAssociados .= ' FROM ' . $tabelaPs1000Vnd1000 . ' ';
	$queryAssociados .= ' WHERE ' . $tabelaPs1000Vnd1000 . '.' . $campoFiltragem . ' = ' . aspas($codigoIdBeneficiario); 	
	$resAssociados = jn_query($queryAssociados); 
	$retornoJson    = '[';
	
	while($rowAssociados = jn_fetch_object($resAssociados))
	{
		if ($retornoJson != '[')
			$retornoJson .= ',';
			
		$queryPerguntas  = ' SELECT ';		
		$queryPerguntas .= '	PS1039.NUMERO_PERGUNTA, PS1039.DESCRICAO_PERGUNTA, ' . $tabelaPs1005Vnd1005 . '.RESPOSTA_DIGITADA, ';
		$queryPerguntas .= '	' . $tabelaPs1005Vnd1005 . '.DESCRICAO_OBSERVACAO ';
		$queryPerguntas .= ' FROM ' . $tabelaPs1000Vnd1000 . ' ';
		$queryPerguntas .= ' INNER JOIN PS1039 ON ' . $tabelaPs1000Vnd1000 . '.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
		$queryPerguntas .= ' LEFT  JOIN ' . $tabelaPs1005Vnd1005 . ' ON (' . $tabelaPs1005Vnd1005 . '.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA AND ' . $tabelaPs1005Vnd1005 . '.CODIGO_ASSOCIADO = ' . $tabelaPs1000Vnd1000 . '.CODIGO_ASSOCIADO) ';
		$queryPerguntas .= ' WHERE ' . $tabelaPs1000Vnd1000 . '.CODIGO_ASSOCIADO = ' . aspas($rowAssociados->CODIGO_ASSOCIADO);

		if (CamposExisteCfgTabelas_Sis('PS1039','DATA_INUTILIZACAO_REGISTRO'))
		{
			$queryPerguntas .= ' and PS1039.DATA_INUTILIZACAO_REGISTRO IS NULL ';		
		}

		$queryPerguntas .= ' ORDER BY PS1039.NUMERO_PERGUNTA '; 	
		$resPerguntas = jn_query($queryPerguntas); 
		
		$retornoJson .= '{"CODIGO_ASSOCIADO":"' . $rowAssociados->CODIGO_ASSOCIADO . '","NOME_ASSOCIADO":"' . jn_utf8_encode($rowAssociados->NOME_ASSOCIADO) . '","PESO":"' . jn_utf8_encode($rowAssociados->PESO) . '", "ALTURA":"' . jn_utf8_encode($rowAssociados->ALTURA) . '", "PERGUNTAS":[';
		
		$i = 0;

		while($rowPerguntas = jn_fetch_object($resPerguntas))
		{
			if($i > 0)
				$retornoJson .= ',';
			
			$retornoJson .= '{"NUMERO_PERGUNTA":"' . $rowPerguntas->NUMERO_PERGUNTA . '","DESCRICAO_PERGUNTA":"' . strtoupper(jn_utf8_encode($rowPerguntas->DESCRICAO_PERGUNTA)) . '","RESPOSTA_DIGITADA":"' . jn_utf8_encode($rowPerguntas->RESPOSTA_DIGITADA) . '","DESCRICAO_OBSERVACAO":"' . strtoupper(jn_utf8_encode($rowPerguntas->DESCRICAO_OBSERVACAO)) . '"} ';
			$i++;
		}
		
		$retornoJson .= ' 	] ';
		$retornoJson .= ' } ';
	}
	
	$retornoJson .=  ']';
	
	echo $retornoJson;
}

if($dadosInput['tipo']== 'dadosBenef')
{
	$queryBenef  = ' SELECT ';
	$queryBenef .= '	' . $tabelaPs1000Vnd1000 . '.NOME_ASSOCIADO, PS1030.NOME_PLANO_FAMILIARES, ' . $tabelaPs1000Vnd1000 . '.PESO, ' . $tabelaPs1000Vnd1000 . '.ALTURA ';
	$queryBenef .= ' FROM ' . $tabelaPs1000Vnd1000 . ' ';
	$queryBenef .= ' INNER JOIN PS1030  ON ' . $tabelaPs1000Vnd1000 . '.CODIGO_PLANO = PS1030.CODIGO_PLANO ';	
	$queryBenef .= ' WHERE ' . $tabelaPs1000Vnd1000 . '.CODIGO_ASSOCIADO = ' . aspas($codigoIdBeneficiario); 		
	$resBenef = jn_query($queryBenef); 
		
	$rowBenef = jn_fetch_object($resBenef);
	$perguntas['NOME_ASSOCIADO'] 	= jn_utf8_encode($rowBenef->NOME_ASSOCIADO);
	$perguntas['NOME_PLANO'] 		= jn_utf8_encode($rowBenef->NOME_PLANO_FAMILIARES);
	$perguntas['ALTURA'] 		= jn_utf8_encode($rowBenef->ALTURA);
	$perguntas['PESO'] 		= jn_utf8_encode($rowBenef->PESO);
		
	echo json_encode($perguntas);
}

if($dadosInput['tipo']== 'dadosCabecalho')
{

	if (CamposExisteCfgTabelas_Sis('ESP0002','CODIGO_GRUPO_CONTRATO'))
	{
		$queryDadosCabecalho  = ' SELECT ';
		$queryDadosCabecalho .= '	PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, TIPO_CONTRATACAO_ANS, CODIGO_TIPO_COBERTURA, PS1030.CODIGO_CADASTRO_ANS, ' . $tabelaPs1000Vnd1000 . '.CODIGO_GRUPO_CONTRATO, ';
		$queryDadosCabecalho .= '	ESP0002.NOME_OPERADORA, ESP0002.NUMERO_ANS_OPERADORA, ';
		$queryDadosCabecalho .= '	(SELECT COUNT(*) FROM ' . $tabelaPs1000Vnd1000 . ' VND_COUNT WHERE ' . $campoFiltragem . ' = ' . aspas($codigoIdBeneficiario) . ') AS QUANT_BENEF';
		$queryDadosCabecalho .= ' FROM ' . $tabelaPs1000Vnd1000 . ' ';
		$queryDadosCabecalho .= ' INNER JOIN PS1030 ON ' . $tabelaPs1000Vnd1000 . '.CODIGO_PLANO = PS1030.CODIGO_PLANO ';	
		$queryDadosCabecalho .= ' LEFT JOIN ESP0002 ON ESP0002.CODIGO_GRUPO_CONTRATO = PS1030.CODIGO_GRUPO_CONTRATO ';	
		$queryDadosCabecalho .= ' WHERE ' . $tabelaPs1000Vnd1000 . '.CODIGO_ASSOCIADO = ' . aspas($codigoIdBeneficiario); 		
		$resDadosCabecalho = jn_query($queryDadosCabecalho); 
	}
	else
	{
		$queryDadosCabecalho  = ' SELECT ';
		$queryDadosCabecalho .= '	PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, TIPO_CONTRATACAO_ANS, CODIGO_TIPO_COBERTURA, PS1030.CODIGO_CADASTRO_ANS, ';
		$queryDadosCabecalho .= '	CFGEMPRESA.NOME_EMPRESA, CFGEMPRESA.NUMERO_INSC_SUSEP, ';
		$queryDadosCabecalho .= '	(SELECT COUNT(*) FROM ' . $tabelaPs1000Vnd1000 . ' VND_COUNT WHERE ' . $campoFiltragem . ' = ' . aspas($codigoIdBeneficiario) . ') AS QUANT_BENEF';
		$queryDadosCabecalho .= ' FROM ' . $tabelaPs1000Vnd1000 . ' ';
		$queryDadosCabecalho .= ' INNER JOIN PS1030 ON ' . $tabelaPs1000Vnd1000 . '.CODIGO_PLANO = PS1030.CODIGO_PLANO ';	
		$queryDadosCabecalho .= ' LEFT JOIN CFGEMPRESA ON (CFGEMPRESA.NOME_EMPRESA IS NOT NULL) ';	
		$queryDadosCabecalho .= ' WHERE ' . $tabelaPs1000Vnd1000 . '.CODIGO_ASSOCIADO = ' . aspas($codigoIdBeneficiario); 		
		$resDadosCabecalho = jn_query($queryDadosCabecalho); 
	}
		
	$rowDadosCabecalho = jn_fetch_object($resDadosCabecalho);
	$dadosCabecalho['NOME_PLANO'] 			= jn_utf8_encode($rowDadosCabecalho->NOME_PLANO_FAMILIARES);
	$dadosCabecalho['CODIGO_CADASTRO_ANS'] 	= jn_utf8_encode($rowDadosCabecalho->CODIGO_CADASTRO_ANS);
	$dadosCabecalho['TIPO_CONTRATACAO_ANS'] = '';
	$dadosCabecalho['TIPO_COBERTURA'] 		= '';
	$dadosCabecalho['NOME_OPERADORA'] 		= jn_utf8_encode($rowDadosCabecalho->NOME_OPERADORA);
	$dadosCabecalho['NUMERO_ANS_OPERADORA'] = jn_utf8_encode($rowDadosCabecalho->NUMERO_ANS_OPERADORA);
	$dadosCabecalho['QUANT_BENEF'] 			= jn_utf8_encode($rowDadosCabecalho->QUANT_BENEF);
	$dadosCabecalho['TABELA_BENEF']			= $tabelaPs1000Vnd1000;
		
	
	$queryComboContrat = 'SELECT OPCOES_COMBO FROM CFGCAMPOS_SIS WHERE NOME_TABELA = "PS1030" AND NOME_CAMPO = "TIPO_CONTRATACAO_ANS"';
	$resComboContrat = jn_query($queryComboContrat);
	$rowComboContrat = jn_fetch_object($resComboContrat);
	$tipoContratacao = $rowComboContrat->OPCOES_COMBO;
	$tipoContratacao = str_replace(',',';',$tipoContratacao);	
	$tipoContratacao = explode(';',$tipoContratacao);
	
	foreach($tipoContratacao as $value){
		$dado = explode('-',$value);		
		if(trim($dado[0]) == $rowDadosCabecalho->TIPO_CONTRATACAO_ANS){			
			$dadosCabecalho['TIPO_CONTRATACAO_ANS'] = $dado[1];
		}		
	}
	
	$queryComboCobert = 'SELECT OPCOES_COMBO FROM CFGCAMPOS_SIS WHERE NOME_TABELA = "PS1030" AND NOME_CAMPO = "CODIGO_TIPO_COBERTURA"';
	$resComboCobert = jn_query($queryComboCobert);
	$rowComboCobert = jn_fetch_object($resComboCobert);
	$tipoContratacao = $rowComboCobert->OPCOES_COMBO;
	$tipoContratacao = str_replace(',',';',$tipoContratacao);	
	$tipoContratacao = explode(';',$tipoContratacao);
	
	foreach($tipoContratacao as $value){
		$dado = explode('-',$value);		
		if(trim($dado[0]) == $rowDadosCabecalho->CODIGO_TIPO_COBERTURA){			
			$dadosCabecalho['TIPO_COBERTURA'] = $dado[1];
		}		
	}
	
	$percentual = 0;
	if(retornaValorConfiguracao('UTILIZA_PERCENTUAL') == 'SIM'){	
		$queryPerc  = ' SELECT VALOR_SUGERIDO FROM PS1024 ';
		$queryPerc .= ' WHERE 1 = 1';
		
		if(retornaValorConfiguracao('UTILIZA_PLANOS_PS1024') == 'SIM'){
			$queryPerc .= ' AND PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowDadosCabecalho->CODIGO_PLANO . '%');		
		}
		
		if(retornaValorConfiguracao('UTILIZA_CONTRATO_PS1024') == 'SIM'){
			$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowDadosCabecalho->CODIGO_GRUPO_CONTRATO);		
		}
		
		$resPerc = jn_query($queryPerc);
		while($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $percentual + $rowPerc->VALOR_SUGERIDO;	
		}
	
		
	}
	$queryAssoc = 'SELECT CODIGO_ASSOCIADO, DATA_NASCIMENTO, CODIGO_PLANO, CODIGO_TABELA_PRECO FROM ' . $tabelaPs1000Vnd1000 . ' WHERE ' . $campoFiltragem . ' = ' . aspas($codigoIdBeneficiario);
	$resAssoc = jn_query($queryAssoc);
	
	$valorTotal = 0;	
	while($rowAssoc = jn_fetch_object($resAssoc)){	
		$dataNascimento = $rowAssoc->DATA_NASCIMENTO;
		if(!is_object($dataNascimento))
			$dataNascimento = new DateTime($rowAssoc->DATA_NASCIMENTO);	
		
		$idade = calcularIdade($dataNascimento);
		
		$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
		$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssoc->CODIGO_PLANO);
		$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
		$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;	
		if($rowAssoc->CODIGO_TABELA_PRECO){
			$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowAssoc->CODIGO_TABELA_PRECO);			
		}
		
		
		$resValores = jn_query($queryValores);
		$rowValores = jn_fetch_object($resValores);
		$valor = $rowValores->VALOR_PLANO;
		
		if($percentual > 0){
			$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
			$valor = ($rowValores->VALOR_PLANO + $calculo);
		}
					
		$valorTotal = ($valorTotal + $valor);
	}

	$dadosCabecalho['VALOR']	= toMoeda($valorTotal);
	
	echo json_encode($dadosCabecalho);
}

if($dadosInput['tipo']== 'salvar'){	
	$respostaPositiva = false;
	jn_query('DELETE FROM ' . $tabelaPs1005Vnd1005 . ' WHERE CODIGO_ASSOCIADO IN (SELECT CODIGO_ASSOCIADO FROM ' . $tabelaPs1000Vnd1000 . ' WHERE ' . $campoFiltragem . ' = ' . aspas($codigoIdBeneficiario) . ')');
	
	$retorno = Array();
	foreach($dadosInput['dadosSalvar'] as $dadosPergunta){
		$respostaPositiva = true;

		$updatePesoAlt  = ' UPDATE ' . $tabelaPs1000Vnd1000 . ' SET ';
		$updatePesoAlt .= ' PESO = ' . aspas($dadosPergunta['PESO']) . ', ' ;
		$updatePesoAlt .= ' ALTURA = ' . aspas($dadosPergunta['ALTURA']);
		
		if($_SESSION['codigoSmart'] =='3389'){//Vidamax
			$updatePesoAlt .= ', ';
			$updatePesoAlt .= ' DESC_CIRURGIA = ' . aspas(utf8_decode($dadosPergunta['DESC_CIRURGIA'])) . ', ';
			$updatePesoAlt .= ' TEMPO_CIRURGIA = ' . aspas(utf8_decode($dadosPergunta['TEMPO_CIRURGIA'])) . ', ';
			$updatePesoAlt .= ' PROCEDIMENTO_CIRURGICO = ' . aspas(utf8_decode($dadosPergunta['PROCEDIMENTO_CIRURGICO'])) . ', ';
			$updatePesoAlt .= ' EXAMES_ULTIMOS_MESES = ' . aspas(utf8_decode($dadosPergunta['EXAMES_ULTIMOS_MESES'])) . ', ';
			$updatePesoAlt .= ' MOTIVO_INTERNACAO = ' . aspas(utf8_decode($dadosPergunta['MOTIVO_INTERNACAO'])) . ', ';
			$updatePesoAlt .= ' PERIODO_INICIAL = ' . aspas(utf8_decode($dadosPergunta['PERIODO_INICIAL'])) . ', ';
			$updatePesoAlt .= ' PERIODO_FINAL = ' . aspas(utf8_decode($dadosPergunta['PERIODO_FINAL'])) . ', ';
			$updatePesoAlt .= ' PESO_NASCIMENTO = ' . aspas(utf8_decode($dadosPergunta['PESO_NASCIMENTO'])) . ', ';
			$updatePesoAlt .= ' IDADE_GESTACIONAL = ' . aspas(utf8_decode($dadosPergunta['IDADE_GESTACIONAL'])) . ', ';
			$updatePesoAlt .= ' INTERCORRENCIAS_NEONATAIS = ' . aspas(utf8_decode($dadosPergunta['INTERCORRENCIAS_NEONATAIS'])) . ', ';
			$updatePesoAlt .= ' EVOLUCAO_PESO_CRES = ' . aspas(utf8_decode($dadosPergunta['EVOLUCAO_PESO_CRES'])) . ', ';
			$updatePesoAlt .= ' OUTRAS_OBSERVACOES = ' . aspas(utf8_decode($dadosPergunta['OUTRAS_OBSERVACOES']));
		}
		
		$updatePesoAlt .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($dadosPergunta['CODIGO_ASSOCIADO']);		
		jn_query($updatePesoAlt);
		
		if($dadosPergunta['NUMERO_PERGUNTA']){
			$query  = 'INSERT INTO ' . $tabelaPs1005Vnd1005 . ' (CODIGO_ASSOCIADO, NUMERO_PERGUNTA, ';
			$query .= 'RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO) ';
			$query .= 'VALUES ( ';
			$query .= aspas($dadosPergunta['CODIGO_ASSOCIADO']) . ", ";
			$query .= aspas($dadosPergunta['NUMERO_PERGUNTA']) . ', ';
			$query .= aspas('S') . ', ';
			$query .= aspas(utf8_decode($dadosPergunta['DESCRICAO_OBSERVACAO']));
			$query .= ' ) ';
			
			if (!jn_query($query)) {
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = 'Nao foi possivel gravar a pergunta -' . $dadosPergunta['NUMERO_PERGUNTA'];				
				return false; // saio retornando false
			}
		}
	}

	if(!$respostaPositiva){		
		$updatePesoAlt  = ' UPDATE ' . $tabelaPs1000Vnd1000 . ' SET ';
		$updatePesoAlt .= ' PESO = ' . aspas($dadosPergunta['PESO']) . ', ' ;
		$updatePesoAlt .= ' ALTURA = ' . aspas($dadosPergunta['ALTURA']);				
		$updatePesoAlt .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($dadosPergunta['CODIGO_ASSOCIADO']);		
		jn_query($updatePesoAlt);
	}

	if ($dadosInput['codigoAssociadoPs1000']=='')  // Então é VND1000
	{
				
			$queryAssocCont = 'SELECT CODIGO_ASSOCIADO FROM ' . $tabelaPs1000Vnd1000 . ' WHERE ' . $campoFiltragem . ' = ' . aspas($codigoIdBeneficiario);
			$resAssocCont = jn_query($queryAssocCont);

			while($rowAssocCont = jn_fetch_object($resAssocCont))
			{		
				$updateStatus  = ' UPDATE VND1000STATUS_ON SET TIPO_STATUS = ' . aspas('DECL_SAUDE_OK');
				$updateStatus .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssocCont->CODIGO_ASSOCIADO);
				$updateStatus .= ' AND TIPO_STATUS = ' . aspas('AGUARDANDO_DECL_SAUDE');
				jn_query($updateStatus);

				$updateStatus  = ' UPDATE ' . $tabelaPs1000Vnd1000 . ' SET ULTIMO_STATUS = ' . aspas('AGUARDANDO_ACEITE_CONTRATO');
				$updateStatus .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssocCont->CODIGO_ASSOCIADO);
				jn_query($updateStatus);

				$insertStatus  = ' INSERT INTO VND1000STATUS_ON (CODIGO_ASSOCIADO, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS, ';
				$insertStatus .= ' REMETENTE_STATUS, DESTINATARIO_STATUS) VALUES (';
				$insertStatus .= aspas($rowAssocCont->CODIGO_ASSOCIADO) . ',' . aspas('AGUARDANDO_ACEITE_CONTRATO') . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas(date('H:i')) . ', ';
				$insertStatus .= aspas('BENEFICIARIO') . ',' . aspas('BENEFICIARIO') . ')';

				if(jn_query($insertStatus)){
					$retorno['STATUS'] = 'OK';
				}else{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = 'Nao foi possivel gravar o status - DECLARACAO';
				}
			}
	}

	$retorno['STATUS'] = 'OK';
	echo json_encode($retorno);	
}

if($dadosInput['tipo']== 'disparoEmail'){
	$codAssociado = $codigoIdBeneficiario;
	$url = retornaValorConfiguracao('LINK_PASTA_CONTRATOS') . 'ServidorAl2/EstruturaPrincipal/disparoEmail.php?codigoModelo=8&vnd=true&codigoAssociado='.$codAssociado;
			
	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
	$start = $info['header_size'];
	$body = substr($result, $start, strlen($result) - $start);
	curl_close($ch);
}

if($dadosInput['tipo']== 'configuracoes'){
	$retorno = Array();
	
	$queryConfSite  = ' SELECT CFGCONFIGURACOES_NET.VALOR_CONFIGURACAO FROM CFGCONFIGURACOES_NET ';
	$queryConfSite .= ' WHERE CFGCONFIGURACOES_NET.IDENTIFICACAO_VALIDACAO = "LINK_SITE_INSTITUCIONAL" ';
	$resConfSite = jn_query($queryConfSite);
	$rowConfSite = jn_fetch_object($resConfSite);
	$retorno['LINK_SITE'] = $rowConfSite->VALOR_CONFIGURACAO;
	
	$queryConfSite  = ' SELECT CFGCONFIGURACOES_NET.VALOR_CONFIGURACAO FROM CFGCONFIGURACOES_NET ';
	$queryConfSite .= ' WHERE CFGCONFIGURACOES_NET.IDENTIFICACAO_VALIDACAO = "POSSUI_ASSINATURA_CONTRATO" ';
	$resConfSite = jn_query($queryConfSite);
	$rowConfSite = jn_fetch_object($resConfSite);
	
	$retorno['CAMINHO_AVISO_CONTRATO'] = retornaValorConfiguracao('CAMINHO_AVISO_CONTRATO');
	$retorno['MOSTRAR_OPCOES_RESP_DEC_SAUDE'] = retornaValorConfiguracao('MOSTRAR_OPCOES_RESP_DEC_SAUDE');
	$retorno['MOSTRAR_PERGUNTAS_ADICIONAIS'] = 'NAO';
	
	if($_SESSION['codigoSmart'] == '3389'){
		$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1030CONFIG_ON ';
		$queryModelo .= ' WHERE CODIGO_PLANO = (SELECT CODIGO_PLANO FROM ' . $tabelaPs1000Vnd1000 . ' WHERE CODIGO_ASSOCIADO =' . aspas($codigoIdBeneficiario) . ')';
		$resModelo = jn_query($queryModelo);
		$rowModelo = jn_fetch_object($resModelo);
		
		if($rowModelo->CODIGOS_MODELO_CONTRATO == '2' || $rowModelo->CODIGOS_MODELO_CONTRATO == '7' || $rowModelo->CODIGOS_MODELO_CONTRATO == '8'){
			$retorno['MOSTRAR_PERGUNTAS_ADICIONAIS'] = 'SIM';
		}
	}

	$retorno['PESO_ALT_OBRIGATORIO_DEC_SAUD'] = retornaValorConfiguracao('PESO_ALT_OBRIGATORIO_DEC_SAUD');
	$retorno['MENS_CONFIRM_DEC_SAUDE'] = jn_utf8_encode(retornaValorConfiguracao('MENS_CONFIRM_DEC_SAUDE'));
	
	echo json_encode($retorno);
}

function calcularIdade($dataNascimento){		

	$dataAtual = new DateTime(date('d-m-Y'));
	$retornoDifDatas = ($dataAtual->diff($dataNascimento));
	$idade = $retornoDifDatas->format('%Y%');

    return $idade;
}
?>