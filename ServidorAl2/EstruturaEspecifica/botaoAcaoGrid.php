<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['dados']['persistencia'] =='gerarFranquia'){
		$retorno['MSG']    = 'Franquia Gerada';

        $numero_registro = getGenerator('i_c_PS1020');
        $benef = $dadosInput['dados']['benef'];
        $Nplano = $dadosInput['dados']['num'];
        $valor = 0;
        $query = 'SELECT VALOR_CONFIGURACAO FROM CFG0003 WHERE IDENTIFICADOR_CONFIGURACAO = \'VALOR_GERADO_PLANO_TRATAMENTO_WEB\'';
        $result = jn_query($query);
        $valor = jn_fetch_assoc($result);
        if( $valor['VALOR_CONFIGURACAO'] <= 0 ){
           $retorno['MSG'] = 'Erro VALOR_GERADO_PLANO_TRATAMENTO_WEB não está parametrizado';
        }else{
			$query = 'SELECT CODIGO_TITULAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . $benef . '';
			$result = jn_query($query);
			$titular = jn_fetch_assoc($result);

			$data_gerar_boleto = date( 'd.m.Y H:i' );
			$data_vencimento_boleto = date( 'd.m.Y H:i', mktime( 0, 0, 0, date( 'm' ), date( 'd' )+10, date( 'Y' ) ) );
			$obs = 'FRANQUIA DO PLANO DE TRATAMENTO ' . $Nplano;
			$query  = 'INSERT INTO PS1020 (NUMERO_REGISTRO, CODIGO_ASSOCIADO, DATA_VENCIMENTO, VALOR_FATURA, DATA_EMISSAO, TIPO_REGISTRO, DESCRICAO_OBSERVACAO) ';
			$query .= " VALUES (" . $numero_registro . ", '" . $titular['CODIGO_TITULAR'] . "', '" . $data_vencimento_boleto . "', " . $valor['VALOR_CONFIGURACAO'] . ", '" . $data_gerar_boleto . "', 'Q', '" . $obs . "' );";
			@jn_query($query);

			$query  = 'UPDATE PS2500 SET NOSSO_NUMERO_BOLETO = \'' . $numero_registro . '\' WHERE NUMERO_PLANO_TRATAMENTO = \'' . $Nplano . '\'';
			$sucesso = @jn_query($query);
			if( !$sucesso ){
				$query  = 'DELETE FROM PS1020 WHERE NUMERO_REGISTRO = ' . $numero_registro; 
				@jn_query($query);
				$retorno['MSG'] = 'Erro ao atualizar plano de tratamento.';
			}
			$retorno['DESTINO'] = 'site/cadastroDinamico'; 
			$retorno['tabela']  = 'VW_PS2500_CD_AL2'; 
		 }
         
		 echo json_encode($retorno);


}else if($dadosInput['dados']['persistencia'] =='agendaPlena'){
	
	require('agendaPlena.php');
	
	$query = 'Select * from ESP_AGENDA where numero_registro = '.aspas($dadosInput['dados']['codigo']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);
	
	$retornoCancelamento =  cancelaAgenda($row->ID_AGENDA_HOSPITAL);
	if($retornoCancelamento == 'OK'){
		$retorno['MSG'] = 'Cancelamento efetuado.';
		$query = "Update ESP_AGENDA set SITUACAO_AGENDA = 'CANCELADO' where numero_registro = ".aspas($dadosInput['dados']['codigo']);
		$res = jn_query($query);
	}else{
		$retorno['MSG'] = 'Não foi possivel cancelar o agendamento.';
	}
	$retorno['DESTINO'] = 'site/gridDinamico'; 
	$retorno['tabela']  = 'VW_AGENDA_PLENA_AL2'; 
	$retorno['rand']  = rand(); 
	echo json_encode($retorno);
	
}else if($dadosInput['dados']['persistencia'] =='ImportarConta'){
		
		$registroPs7201 = $dadosInput['dados']['codigo'];
		$queryPs7201  = ' SELECT PS7200.CODIGO_IDENTIFICACAO, PS7200.CODIGO_PRESTADOR, PS7201.VALOR_CONTA, PS7200.CODIGO_CENTRO_CUSTO FROM PS7201 ';
		$queryPs7201 .= ' INNER JOIN PS7200 ON (PS7201.CODIGO_CONTA = PS7200.CODIGO_CONTA)';
		$queryPs7201 .= ' WHERE PS7201.NUMERO_REGISTRO = ' . aspas($registroPs7201);
		$resPs7201 = jn_query($queryPs7201);
		$rowPs7201 = jn_fetch_object($resPs7201);
		
		$NumSolicitacao =  jn_gerasequencial('ESP_AUDITORIA_PAGAMENTOS_NET');	
		$date = date('d/m/Y');
		$query  = " INSERT INTO ESP_AUDITORIA_PAGAMENTOS_NET(NUMERO_SOLICITACAO, CODIGO_OPERADOR_SOLICITACAO, CODIGO_FORNECEDOR, CODIGO_PRESTADOR, ";
		$query .= " VALOR_SOLICITADO, TIPO_AUDITORIA, DATA_SOLICITACAO, STATUS_AUDITORIA, CODIGO_CENTRO_CUSTO, DEPARTAMENTO, OBSERVACAO, NUMERO_REGISTRO_PS7201) ";
		$query .= " Values( ";
		$query .= aspas($NumSolicitacao) . ", ";
		$query .= aspas($_SESSION['codigoIdentificacao']) . ", ";
		$query .= aspas($rowPs7201->CODIGO_IDENTIFICACAO) . ", ";
		$query .= aspas($rowPs7201->CODIGO_PRESTADOR) . ", ";
		$query .= aspas($rowPs7201->VALOR_CONTA) . ", ";
		$query .= aspas('P') . ", ";
		$query .= dataToSql($date) . ", ";
		$query .= aspas('P') . ", ";
		$query .= aspas($rowPs7201->CODIGO_CENTRO_CUSTO) . ", ";
		$query .= aspas('F') . ", ";
		$query .= aspas('REGISTRO IMPORTADO') . ", ";
		$query .= aspas($registroPs7201) .") ";	
	
		if(jn_query($query)){
			$retorno['MSG'] = 'REGISTRO IMPORTADO';
		}else{
			$retorno['MSG'] = 'Erro ao importar';
		}		

		$retorno['DESTINO'] = 'site/gridDinamico'; 
		$retorno['tabela']  = 'VW_CONTAS_IMPORTAR_AL2'; 
		$retorno['rand']  = rand(); // valor randomico pra forçar atualizar a pagina
		
		echo json_encode($retorno);
}else if($dadosInput['dados']['persistencia'] =='downloadNFPrestPlena'){
	$retorno['LINK'] = $dadosInput['dados']['link']; 
	
	jn_query('UPDATE CFGARQUIVOS_PROCESSOS_NET SET DATA_DOWNLOAD = CURRENT_TIMESTAMP WHERE DATA_DOWNLOAD IS NULL AND NUMERO_REGISTRO = ' . aspas($dadosInput['dados']['codigo']));
	
	$queryPrest = 'SELECT CODIGO_PRESTADOR FROM CFGARQUIVOS_PROCESSOS_NET WHERE NUMERO_REGISTRO = ' . aspas($dadosInput['dados']['codigo']);
	$resPrest = jn_query($queryPrest);
	$rowPrest = jn_fetch_object($resPrest);
	
	$retorno['DESTINO'] = 'site/gridDinamico'; 
	$retorno['tabela']  = 'VW_ARQ_NF_PREST_AL2'; 
	$retorno['filtros']  = '[{"CAMPO":"CODIGO_PRESTADOR","VALOR":"' . $rowPrest->CODIGO_PRESTADOR . '"}]'; 
	$retorno['rand']  = rand(); // valor randomico pra forçar atualizar a pagina
	
	echo json_encode($retorno);
}else if($dadosInput['dados']['persistencia'] =='dadosContatoAssociado'){	
	
	$codigoAssociado = $dadosInput['dados']['codigo'];
	
	$queryAssoc  = ' SELECT PS1000.NOME_ASSOCIADO, COALESCE(PS1001.ENDERECO_EMAIL, "Não informado") AS ENDERECO_EMAIL, ';
	$queryAssoc .= ' PS1006.CODIGO_AREA, PS1006.NUMERO_TELEFONE ';
	$queryAssoc .= ' FROM PS1000';
	$queryAssoc .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO)';
	$queryAssoc .= ' INNER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO)';
	$queryAssoc .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	$resAssoc = jn_query($queryAssoc);
	$rowAssoc = jn_fetch_object($resAssoc);
	
	$mensagem  = 'Nome Associado: ' . $rowAssoc->NOME_ASSOCIADO . ' <br> ';
	$mensagem .= 'Tefone: (' . $rowAssoc->CODIGO_AREA . ')' . ' ' . $rowAssoc->NUMERO_TELEFONE . ' <br> ';
	$mensagem .= 'E-mail: ' . $rowAssoc->ENDERECO_EMAIL . ' <br> ';
	
	$retorno['MSG'] = $mensagem;
	
	echo json_encode($retorno);
	
}else if($dadosInput['dados']['persistencia'] =='renovarAssinaturaVindi'){
	require('../lib/vindi.php');
	
	$codigoAssociado = $dadosInput['dados']['codigo'];
	$retorno = Array();

	$queryIdVinid  = ' SELECT PS1000.CODIGO_ASSOCIADO, ESP_CONTROLE_ASSINATURA.ID_VINDI, ESP_CONTROLE_ASSINATURA.ID_CARTAO_CREDITO FROM PS1000';	
	$queryIdVinid .= ' INNER JOIN ESP_CONTROLE_ASSINATURA ON (PS1000.CODIGO_ASSOCIADO = ESP_CONTROLE_ASSINATURA.CODIGO_ASSOCIADO) ';
	$queryIdVinid .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	$resIdVinid = jn_query($queryIdVinid);
	$rowIdVinid = jn_fetch_object($resIdVinid);
	
	$retornoRenovacao = CriaNovaAssinatura($rowIdVinid->CODIGO_ASSOCIADO,$rowIdVinid->ID_VINDI,$rowIdVinid->ID_CARTAO_CREDITO,$idPlano,$idProdutoMensalidade,$valor,date('d/m/Y'),12);
	
	if($retornoRenovacao['STATUS'] == 'OK'){

		$queryInsert  = ' INSERT INTO ESP_CONTROLE_ASSINATURA ';
		$queryInsert .= ' (CODIGO_ASSOCIADO, DATA_ASSINATURA, TIPO_PAGAMENTO, ID_VINDI, ID_CARTAO_CREDITO) VALUES ';
		$queryInsert .= ' (' . aspas($rowIdVinid->CODIGO_ASSOCIADO) . ', CURRENT_TIMESTAMP, "CC", ' . aspas($rowIdVinid->ID_VINDI) . ', ' . aspas($rowIdVinid->ID_CARTAO_CREDITO) . ') ';

		jn_query($queryInsert);
		$retorno['MSG'] = 'Assinatura Renovada';
	}else{
		$retorno['MSG'] = 'Erro ao efetuar a renovação. Favor contatar o administrador. ';
	}

	echo json_encode($retorno);

}
else if($dadosInput['dados']['persistencia'] =='cancelarAssinaturaVindi')
{
	require('../lib/vindi.php');
	
	$codigoAssociado = $dadosInput['dados']['codigo'];
	$retorno = Array();
	
	$queryIdVinid  = ' SELECT ESP_CONTROLE_ASSINATURA.ID_VINDI FROM PS1000';	
	$queryIdVinid .= ' INNER JOIN ESP_CONTROLE_ASSINATURA ON (PS1000.CODIGO_ASSOCIADO = ESP_CONTROLE_ASSINATURA.CODIGO_ASSOCIADO) ';
	$queryIdVinid .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	$resIdVinid = jn_query($queryIdVinid);
	$rowIdVinid = jn_fetch_object($resIdVinid);
	
	$retornoCancelamento = CancelaAssinatura($rowIdVinid->ID_VINDI ,true,'CancelamentoPortal');
	
	if($retornoCancelamento['STATUS'] == 'OK'){
		jn_query('UPDATE ESP_CONTROLE_ASSINATURA SET DATA_CANCELAMENTO = CURRENT_TIMESTAMP WHERE CODIGO_ASSOCIADO = '  . aspas($codigoAssociado));
		$retorno['MSG'] = 'Assinatura Cancelada';
	}else{		
		$retorno['MSG'] = 'Erro ao efetuar o cancelamento. Favor contatar o administrador. ';
	}
	
	echo json_encode($retorno);
}

else if($dadosInput['dados']['persistencia'] =='confirmaAtendimentoACS')
{
	
	$numeroRegistro = $dadosInput['dados']['numeroregistrops5721'];
	
	$queryIdVinid   = ' UPDATE PS5721 SET DATA_CONFIRMACAO_ATENDIMENTO = ' . dataToSql(date('d/m/Y')) . ' , 
						     HORA_CONFIRMACAO_ATENDIMENTO = ' . aspas(date('H:m')) . 
						   ' WHERE NUMERO_REGISTRO = ' . aspas($numeroRegistro);  

	$resIdVinid     = jn_query($queryIdVinid);

	$queryIdVinid   = ' UPDATE PS6010 SET FLAG_PROCEDIMENTO_REALIZADO = ' . aspas('S') . ', TIPO_SITUACAO = ' . aspas('REALIZADO') .  
						   ' WHERE PS6010.NUMERO_REGISTRO IN ( SELECT PS5721.NUMERO_REGISTRO_PS6010 FROM PS5721 
						                                       WHERE PS5721.NUMERO_REGISTRO_PS6010 = PS6010.NUMERO_REGISTRO  
						                                       AND PS5721.NUMERO_REGISTRO = ' . aspas($numeroRegistro) . ')';  

	$resIdVinid     = jn_query($queryIdVinid);


   $retorno['MSG'] = 'Ok, registro de atendimento confirmado com sucesso! ';
	$retorno['DESTINO'] = 'site/gridDinamico'; 
	$retorno['tabela']  = 'VW_PS5721_CONFIRMACAO'; 

	echo json_encode($retorno);

}else if($dadosInput['dados']['persistencia'] =='junoCadastro'){
	require('../services/juno.php');
	
	$query = 'select
				Ps5000.CODIGO_PRESTADOR,Ps5000.NOME_PRESTADOR,PS5000.TIPO_PESSOA,PS5000.DATA_NASCIMENTO_FUNDACAO,PS5000.RAZAO_SOCIAL_NM_COMPLETO,
				PS5001.CIDADE,PS5001.TELEFONE_01,PS5001.ENDERECO_EMAIL,PS5001.ENDERECO,PS5001.ESTADO,PS5001.CEP,PS5001.BAIRRO,
				PS5002.TIPO_EMPRESA,PS5002.FLAG_POLITICAMENTE_EXPOSTO,PS5002.FLAG_POUPANCA,PS5002.NOME_REPRESENTANTE_LEGAL,
				PS5002.NOME_MAE_REPRESENTANTE_LEGAL,PS5002.CPF_REPRESENTANTE_LEGAL,PS5002.RENDA_MENSAL,PS5002.NUMERO_CPF,
				PS5002.NUMERO_CNPJ,PS5002.NOME_TITULAR_CONTA,PS5002.DOCUMENTO_TITULAR_CONTA,PS5002.COMPLEMENTO_NUMERO_CAIXA,
				PS5002.DATA_NASCI_REPRESENTANTE_LEGAL,PS5002.NUMERO_CNAE,PS5002.TIPO_REPRESENTANTE_LEGAL,PS5002.CODIGO_BANCO,
				PS5002.NUMERO_AGENCIA,PS5002.NUMERO_CONTA_CORRENTE
			from PS5000
			left join Ps5001 on Ps5001.CODIGO_PRESTADOR = Ps5000.CODIGO_PRESTADOR
			left join Ps5002 on Ps5002.CODIGO_PRESTADOR = Ps5000.CODIGO_PRESTADOR
			where Ps5000.CODIGO_PRESTADOR = '. aspas($dadosInput['dados']['prestador']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);

	$endereco = $row->ENDERECO.' ';
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
	
	if($row->TIPO_PESSOA == 'F'){
		$retornoConta = CriaContaDigitalPF(
										$row->NOME_PRESTADOR,$row->NOME_MAE_REPRESENTANTE_LEGAL,$row->NUMERO_CPF,$row->ENDERECO_EMAIL,sqlToData($row->DATA_NASCI_REPRESENTANTE_LEGAL),
										$row->TELEFONE_01,$endereco,$numero,$row->BAIRRO,$complemento,$row->CIDADE,$row->ESTADO,$row->CEP,
										$row->RENDA_MENSAL,$row->FLAG_POLITICAMENTE_EXPOSTO=='S',$row->CODIGO_BANCO,$row->NUMERO_AGENCIA,$row->NUMERO_CONTA_CORRENTE,
										$row->NOME_TITULAR_CONTA,$row->DOCUMENTO_TITULAR_CONTA,$row->FLAG_POUPANCA=='S',$row->COMPLEMENTO_NUMERO_CAIXA
									 );
	}else{
		$mebrosEmpresa = array();
		
		$retornoConta = CriaContaDigitalPJ(
										$row->NOME_PRESTADOR,$row->NUMERO_CNPJ,$row->NUMERO_CNAE,sqlToData($row->DATA_NASCIMENTO_FUNDACAO),$row->NOME_REPRESENTANTE_LEGAL,$row->CPF_REPRESENTANTE_LEGAL,
										sqlToData($row->DATA_NASCI_REPRESENTANTE_LEGAL),$row->NOME_MAE_REPRESENTANTE_LEGAL,$row->TIPO_REPRESENTANTE_LEGAL,$mebrosEmpresa,
										$row->ENDERECO_EMAIL,$row->TELEFONE_01,$endereco,$numero,$row->BAIRRO,$complemento,$row->CIDADE,$row->ESTADO,$row->CEP,
										$row->RENDA_MENSAL,$row->FLAG_POLITICAMENTE_EXPOSTO=='S',$row->TIPO_EMPRESA,$row->CODIGO_BANCO,$row->NUMERO_AGENCIA,$row->NUMERO_CONTA_CORRENTE,
										$row->NOME_TITULAR_CONTA,$row->DOCUMENTO_TITULAR_CONTA,$row->FLAG_POUPANCA=='S',$row->COMPLEMENTO_NUMERO_CAIXA);
	}
	
	if($retornoConta['STATUS'] == 'OK'){
		$retorno['MSG'] = 'Cadastro Efetuado, envie os anexos.';
		CriarWebhook($retornoConta['DADOS']['resourceToken'],$retornoConta['DADOS']['id'],retornaValorCFG0003('JUNO_LINK_RETORNO'));
		jn_query('UPDATE PS5000 SET ID_JUNO ='.aspas($retornoConta['DADOS']['id']).' ,TOKEN_JUNO ='.aspas($retornoConta['DADOS']['resourceToken']).' ,STATUS_JUNO ='.aspas($retornoConta['DADOS']['status']).'  WHERE CODIGO_PRESTADOR = '  . aspas($dadosInput['dados']['prestador']));
		$retorno['DESTINO'] = 'site/gridDinamico'; 
		$retorno['tabela']  = 'VW_PS5000_CD_AL2'; 
		$retorno['rand']  = rand(); // valor randomico pra forçar atualizar a pagina
		
	}else{		
		$retorno['MSG'] = 'Erro ao efetuar o cadastro.<br><br>';
		for ($i = 0; $i < count($retornoConta['ERROS']['details']); $i++) {
			$retorno['MSG'] .=deParaCampoJuno($retornoConta['ERROS']['details'][$i]['field']).': '.$retornoConta['ERROS']['details'][$i]['message'].'<br>';
		}

	}
	echo json_encode($retorno);
}else if($dadosInput['dados']['persistencia'] =='JunoAnexo'){
	require('../services/juno.php');
	
	$retornoDocumentos = ListaDocumentosEnvio($dadosInput['dados']['TOKEN_JUNO']);

	if($retornoDocumentos['STATUS'] == 'OK'){
		for ($i = 0; $i < count($retornoDocumentos['DADOS']['_embedded']['documents']); $i++) {
			
		
			$query = 'Select * from ESP_JUNO_DOCUMENTOS where ID_DOCUMENTO = '. aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['id']);
			$res = jn_query($query);
			if($row = jn_fetch_object($res)){
				$query = 'update ESP_JUNO_DOCUMENTOS set STATUS_DOCUMENTO='.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['approvalStatus']).' where ID_DOCUMENTO ='.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['id']);
				$res = jn_query($query);
			}else{
				$query = 'INSERT into ESP_JUNO_DOCUMENTOS(ID_DOCUMENTO,DESCRICAO_DOCUMENTO,STATUS_DOCUMENTO,CODIGO_PRESTADOR) VALUES('.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['id']).','.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['description']).','.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['approvalStatus']).','.aspas($dadosInput['dados']['prestador']).')';
				$res = jn_query($query);
			}
		}
		
		$retorno['DESTINO'] = 'site/cadastroDinamico'; 
		$retorno['tabela']  = 'ESP_JUNO_DOCUMENTOS'; 
		$retorno['filtros']  = '[{"CAMPO":"CODIGO_PRESTADOR","VALOR":"' . $dadosInput['dados']['prestador'] . '"}]'; 
		$retorno['rand']  = rand(); // valor randomico pra forçar atualizar a pagina
	}
	
	echo json_encode($retorno);
}else if($dadosInput['dados']['persistencia'] =='junoExtorno'){
	require('../services/juno.php');
	
	$selectRegistro = 'select JSON_PAGAMENTO_CARTAO from ESP_JUNO_FATURAS_CARTAO where NUMERO_IDENTIFICACAO_COMPRA ='.aspas($dadosInput['dados']['compra']).' and numero_registro='.aspas($dadosInput['dados']['id']);
	$resRegistro  = jn_query($selectRegistro);
	if($rowRegistro = jn_fetch_object($resRegistro)){
		$retornoPagamento = json_decode($rowRegistro->JSON_PAGAMENTO_CARTAO);
		$retornoPagamento = (array) $retornoPagamento;
		$retornoPagamento['DADOS'] = (array) $retornoPagamento['DADOS'];
		$retornoPagamento['DADOS']['payments'] = (array) $retornoPagamento['DADOS']['payments'];
		$retornoPagamento['DADOS']['payments'][0] = (array) $retornoPagamento['DADOS']['payments'][0];


		$retornoExtorno =  EstornaPagamentoCartao($retornoPagamento['DADOS']['payments'][0]['id']);
		
		if($retornoExtorno['STATUS'] == 'OK'){
			$queryUpdate = 'UPDATE PS5720 set VALOR_PAGO_CLIENTE =NULL,DATA_PAGAMENTO_CLIENTE= NULL,ID_PAGAMENTO_CLIENTE =NULL where numero_identificacao_compra = '.aspas($dadosInput['dados']['compra']);
			jn_query($queryUpdate);
			$update = 'UPDATE ESP_JUNO_FATURAS_CARTAO set JSON_EXTORNO_CARTAO='.aspas(json_encode($retornoExtorno)).' where NUMERO_IDENTIFICACAO_COMPRA='.aspas($dadosInput['dados']['compra']);
			jn_query($update);	
			
			$retorno['MSG'] = 'Extorno Efetuado.';
			
			$retorno['DESTINO'] = 'site/gridDinamico'; 
			$retorno['tabela']  = 'VW_JUNO_EXTORNO'; 
			$retorno['rand']  = rand(); // valor randomico pra forçar atualizar a pagina
			
		}else{		
			$retorno['MSG'] = 'Erro ao efetuar o cadastro.<br><br>';
			for ($i = 0; $i < count($retornoExtorno['ERROS']['details']); $i++) {
				$retorno['MSG'] .=deParaCampoJuno($retornoExtorno['ERROS']['details'][$i]['field']).': '.$retornoExtorno['ERROS']['details'][$i]['message'].'<br>';
			}

		}
	}

}
else if($dadosInput['dados']['persistencia'] =='JunoSaque')
{		
	$retorno['DESTINO'] = 'site/sacarJuno';		
	$retorno['idJuno']  = $dadosInput['dados']['ID_JUNO'];
	
	echo json_encode($retorno);

}
else if($dadosInput['dados']['persistencia'] =='AlterarCartao')
{		
	$retorno['DESTINO'] 			= 'site/pagamento';		
	$retorno['alteracao']  			= 'SIM';
	$retorno['associadoCartao']  	= $dadosInput['dados']['associadoCartao'];

	echo json_encode($retorno);

}else if($dadosInput['dados']['persistencia'] =='atualizarAgendamentoCirurgicoPlena'){
	$retorno['LINK'] = $dadosInput['dados']['link']; 
	
	jn_query('UPDATE ESP_AGENDAMENTO_CIRURGICO SET CODIGO_AUDITOR = ' . aspas($_SESSION['codigoIdentificacao']) . ', STATUS_AGENDAMENTO = ' . aspas($dadosInput['dados']['status']) . ' WHERE NUMERO_REGISTRO = ' . aspas($dadosInput['dados']['numeroRegistro']));
	
	$queryDados  = '	SELECT NOME_ASSOCIADO, TELEFONE_ASSOCIADO, PROTOCOLO_GERAL_PS6450 FROM ESP_AGENDAMENTO_CIRURGICO';		
	$queryDados .= '	INNER JOIN PS1000 ON (ESP_AGENDAMENTO_CIRURGICO.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ';
	$queryDados .= '	WHERE ESP_AGENDAMENTO_CIRURGICO.NUMERO_REGISTRO = ' . aspas($dadosInput['dados']['numeroRegistro']);

	$resDados = jn_query($queryDados);
	$rowDados = jn_fetch_object($resDados);

	$telefoneAssociado = remove_caracteres($rowDados->TELEFONE_ASSOCIADO);
	$telefoneAssociado = str_replace('_','',$telefoneAssociado);

	if(strlen($telefoneAssociado) == '11'){
		require_once('../lib/smsPointer.php');			
		enviaSmsPointer($telefoneAssociado,'A solicitacao de agendmento da Plena, com protocolo ' . $rowDados->PROTOCOLO_GERAL_PS6450 . ', teve o status alterado para ' . $dadosInput['dados']['status']);
	}

	
	$retorno['DESTINO'] = 'site/cadastroDinamico';
	$retorno['tabela']  = 'VW_AGENDAMENTO_CIRURGICO_AL2'; 	
	$retorno['rand']  = rand(); // valor randomico pra forçar atualizar a pagina
	
	echo json_encode($retorno);

}else if($dadosInput['dados']['persistencia'] =='RecursoGlosa'){
	$tipoGuia = $dadosInput['dados']['TipoGuia']; 
	$numeroGuia = $dadosInput['dados']['Guia']; 
	$codigoPrestador = $_SESSION['codigoIdentificacao'];
	$erro = false;

	$queryPS5700 = 'SELECT * FROM PS5700 WHERE GUIA_RECURSO_GLOSA_OPER = ' . aspas($numeroGuia);
	$resPS5700 = jn_query($queryPS5700);
	if(!$rowPS5700 = jn_fetch_object($resPS5700)){

		$registroPS5700 = jn_gerasequencial('PS5700');
		$numeroProtocolo = GeraProtocoloGeralPs6450($codigoPrestador, '');		

		$insertRecurso  = ' INSERT INTO PS5700 (NUMERO_REGISTRO, GUIA_RECURSO_GLOSA_OPER, CODIGO_PRESTADOR, NUMERO_PROTOCOLO, DATA_RECURSO, INFORMACOES_LOG_I) VALUES  ';
		$insertRecurso .= ' ( ' . $registroPS5700 . ', ' . aspas($numeroGuia) . ', ' . $codigoPrestador . ', ' . aspas($numeroProtocolo) . ', GETDATE(), ' . aspas('CAD_PORTAL:'. date('d/m/Y')) .  ')';
		if(jn_query($insertRecurso)){	
			$registroPS5710 = jn_gerasequencial('PS5710');
			
			$insertItemRec  = ' INSERT INTO PS5710 (NUMERO_REGISTRO, NUMERO_GUIA_OPERADORA, CODIGO_GLOSA_ITEM, DATA_INICIO, CODIGO_PROCEDIMENTO, DESCRICAO_PROCEDIMENTO,  ';
			$insertItemRec .= ' 	NOME_ASSOCIADO, NUMERO_REGISTRO_PS5700) VALUES ' ;
			$insertItemRec .= '(' . aspas($registroPS5710) . ', ' . aspas($dadosInput['dados']['Guia']) . ', ' . aspas($dadosInput['dados']['CodGlosa']) . ', GETDATE(),  ' . aspas($dadosInput['dados']['CodProcedimento']) . ', ';
			$insertItemRec .= 		aspas($dadosInput['dados']['DescProcedimento']) . ', ' . aspas($dadosInput['dados']['NomeAssociado']) . ', ' . aspas($registroPS5700) . ' )';
			if(!jn_query($insertItemRec))
				$erro = true;			
		}else
			$erro = true;
					
	}else{
		$registroPS5700 = $rowPS5700->NUMERO_REGISTRO;
		$registroPS5710 = jn_gerasequencial('PS5710');

		$insertItemRec  = ' INSERT INTO PS5710 (NUMERO_REGISTRO, NUMERO_GUIA_OPERADORA, CODIGO_GLOSA_ITEM, DATA_INICIO, CODIGO_PROCEDIMENTO, DESCRICAO_PROCEDIMENTO,  ';
		$insertItemRec .= ' 	NOME_ASSOCIADO, NUMERO_REGISTRO_PS5700) VALUES ' ;
		$insertItemRec .= '(' . aspas($registroPS5710) . ', ' . aspas($dadosInput['dados']['Guia']) . ', ' . aspas($dadosInput['dados']['CodGlosa']) . ', GETDATE(),  ' . aspas($dadosInput['dados']['CodProcedimento']) . ', ';
		$insertItemRec .= 		aspas($dadosInput['dados']['DescProcedimento']) . ', ' . aspas($dadosInput['dados']['NomeAssociado']) . ', ' . aspas($registroPS5700) . ' )';
		if(!jn_query($insertItemRec))
			$erro = true;	
	}
		
	if($erro){		
		$retorno['MSG'] = 'Erro ao cadastrar recurso, favor entrar em contato com a operadora.<br><br>';
	}else{		
		$retorno['DESTINO'] = 'site/cadastroDinamico';
		$retorno['tabela']  = 'VW_RECURSO_CARENCIA_AL2'; 	
		$retorno['rand']  = rand();		
		$retorno['MSG'] = 'Recurso cadastrado com sucesso.';
	}	
	
	echo json_encode($retorno);
}else if($dadosInput['dados']['persistencia'] =='DispararEmailErroCadastral'){

	$queryDadosEmp  = ' SELECT ENDERECO_EMAIL, NOME_EMPRESA FROM PS1010 ';
	$queryDadosEmp .= ' INNER JOIN PS1001 ON (PS1010.CODIGO_EMPRESA = PS1001.CODIGO_EMPRESA) ';
	$queryDadosEmp .= ' WHERE PS1010.CODIGO_EMPRESA = ' . aspas($dadosInput['dados']['codigoEmpresa']);
	$resDadosEmp = jn_query($queryDadosEmp);
	$rowDadosEmp = jn_fetch_object($resDadosEmp);
	$enderecoEmail = $rowDadosEmp->ENDERECO_EMAIL;	


	$assunto = 'Solicitacao de Ajuste Cadastral';
	$corpoEmail  = ' Prezado(a), <br>';
	$corpoEmail .= ' Precisamos que entrem em contato com a nossa operadora, para ajustarmos o cadastro abaixo: <br>';
	$corpoEmail .= ' Codigo Associado : ' . $dadosInput['dados']['codigoAssociado'] . ' <br>';
	$corpoEmail .= ' Nome Associado: ' . $dadosInput['dados']['nomeAssociado'] . ' <br>';	
	
	disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);		

}else if($dadosInput['dados']['persistencia'] =='GerarToken'){
	$retorno = Array();
	$codigoAssociado = $dadosInput['dados']['codigo'];
	
	$queryToken  = " SELECT TOKEN FROM ESP_TOKEN ";
	$queryToken .= " WHERE DATA_UTILIZACAO IS NULL AND DATA_EXPIRACAO > GETDATE() AND CODIGO_ASSOCIADO =" . aspas($codigoAssociado);
	$resToken = jn_query($queryToken);
	if($rowToken = jn_fetch_object($resToken)){
		$retorno['MSG'] = 'Você ja possui um token<br>Token : <b> ' . $rowToken->TOKEN . '</b>';
	}else{

		$token  = rand(1, 999999);
		$token  =  str_pad($token, 6, 0, STR_PAD_LEFT);
		$minutos = retornaValorCFG0003('MINUTOS_EXPIRACAO_TOKEN');
		$queryInsert = 'INSERT INTO ESP_TOKEN(CODIGO_ASSOCIADO,TOKEN,DATA_EXPIRACAO)values('.aspas($codigoAssociado).','.aspas($token).',DATEADD(minute, '.$minutos.', GETDATE() ))';
		
		if(jn_query($queryInsert)){
			$retorno['MSG'] = 'Token Gerado<br>Token : <b> ' . $token . '</b>';			
		}else{
			$retorno['MSG'] = 'Erro ao gerar o Token ' . $queryInsert;			
		}
		
	}
	
	echo json_encode($retorno);	

}else if($dadosInput['dados']['persistencia'] =='cartaoCredito'){	

	$gatewayCartao = retornaValorConfiguracao('GATEWAY_CARTAO_CREDITO');
	$codigoAssociado = $dadosInput['dados']['codigo'];

	if($gatewayCartao == 'ZSPAY'){
		require('../lib/zsPay.php');   
        global $tokenZsPay;

		$tokenZsPay = retornaValorConfiguracao('TOKEN_ZSPAY');
		$retornoId = retornaIdAssociadoZsPay($codigoAssociado);
		$criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
		$retorno = Array();
		
		if(isset($retornoId['STATUS']) == 'OK'){

			$idClienteZsPay = $retornoId['ID'];
			$numeroCartao = $dadosInput['dados']['dadosCartao']['Numero'];
			$nomeCartao = $dadosInput['dados']['dadosCartao']['Nome'];
			$VencimentoCartao = $dadosInput['dados']['dadosCartao']['Vencimento'];
			$VencimentoCartao = substr($VencimentoCartao,0,2) . '/' . substr($VencimentoCartao,2,4);
			$codSegCartao = $dadosInput['dados']['dadosCartao']['CodigoSeg'];

			$retornoCartao = vincula_cartao_zspay($idClienteZsPay, $numeroCartao, $nomeCartao, $VencimentoCartao, $codSegCartao);			

			if($retornoCartao['STATUS'] == 'OK'){

				$sqlInclusaoCartao   = linhaJsonEdicao('CODIGO_ASSOCIADO', $codigoAssociado);     
				$sqlInclusaoCartao  .= linhaJsonEdicao('ID_CARTAO_GATEWAY', $retornoCartao['ID']);     
				$sqlInclusaoCartao  .= linhaJsonEdicao('GATEWAY_PAGAMENTO', 'ZSPAY');     
				$sqlInclusaoCartao  .= linhaJsonEdicao('ULTIMOS_DIGITOS', $retornoCartao['DIG_FINAIS']);   
				$sqlInclusaoCartao  .= linhaJsonEdicao('DATA_CADASTRO', dataHoje(), 'D');   
				gravaEdicao('ESP_HIST_CARTOES_ASSOCIADOS', $sqlInclusaoCartao, 'I', ''); 

				$sqlEdicaoPs1000   = linhaJsonEdicao('ID_CARTAO_GATEWAY', $retornoCartao['ID']);     
				gravaEdicao('PS1000', $sqlEdicaoPs1000, 'A', $criterioWhereGravacao);            	

				$retorno['MSG'] = 'Cartão vinculado com sucesso!';
			}else{
				$retorno['MSG'] = 'Erro ao cadastrar cartão. Ref[1.2]';
			}
		}else{
			$retorno['MSG'] = 'Erro ao cadastrar cartão. Ref[1.1]';
		}
	}
	
	echo json_encode($retorno);	

}else if($dadosInput['dados']['persistencia'] =='efetuarPagamentoCartao'){	
	
	$gatewayCartao = retornaValorConfiguracao('GATEWAY_CARTAO_CREDITO');
	$codigoAssociado = $dadosInput['dados']['codigo'];
	$registro = $dadosInput['dados']['registro'];
	$retorno = Array();

	if($gatewayCartao == 'ZSPAY'){
		require('../lib/zsPay.php');   
        global $tokenZsPay;

		$tokenZsPay = retornaValorConfiguracao('TOKEN_ZSPAY');
		
		$query  = '	SELECT ID_CLIENTE_ZSPAY, ID_CARTAO_GATEWAY, VALOR_FATURA FROM PS1020 ';
		$query .= '	INNER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1020.CODIGO_ASSOCIADO) ';
		$query .= '	WHERE 	PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
		$query .= '		AND PS1020.NUMERO_REGISTRO = ' . aspas($registro);
		$resultado = qryUmRegistro($query);

		$retornoPagamentoCC = cria_venda_cartao_credito($resultado->ID_CLIENTE_ZSPAY, $resultado->ID_CARTAO_GATEWAY, $resultado->VALOR_FATURA, 1);
       	if($retornoPagamentoCC['STATUS'] == 'OK'){
			$criterioWhereGravacao = ' NUMERO_REGISTRO = ' . aspas($registro);

			$sqlEdicaoPs1020   = linhaJsonEdicao('VALOR_PAGO', $resultado->VALOR_FATURA);
			gravaEdicao('PS1020', $sqlEdicaoPs1020, 'A', $criterioWhereGravacao);

			$retorno['MSG'] = 'Pagamento realizado com sucesso!';
	   	}else{
			$retorno['MSG'] = 'Erro ao Realizar Pagamento. -- ' . $retorno['ERROS'];
		}
	}

	echo json_encode($retorno);

}else if($dadosInput['dados']['persistencia'] =='efetuarAssinaturaCartao'){	
	
	$gatewayCartao = retornaValorConfiguracao('GATEWAY_CARTAO_CREDITO');
	$codigoAssociado = $dadosInput['dados']['codigo'];
	$registro = $dadosInput['dados']['registro'];
	$retorno = Array();

	if($gatewayCartao == 'ZSPAY'){
		require('../lib/zsPay.php');   
        global $tokenZsPay;

		$tokenZsPay = retornaValorConfiguracao('TOKEN_ZSPAY');
		$codigoPlanoZs = '';

		$queryPlano  = ' SELECT CODIGO_PLANO_GATEWAY FROM ESP_PLANOS_GATEWAY ';
		$queryPlano .= ' WHERE VALOR_PLANO =' . aspas($dadosInput['dados']['valorFatura']);
		$resultadoPlano = qryUmRegistro($queryPlano);

		if(!isset($resultadoPlano->CODIGO_PLANO_GATEWAY)){
			$retornoPlano = cria_plano_zspay($dadosInput['dados']['valorFatura'], retornaValorConfiguracao('EMAIL_PADRAO'), 'daily');
            if(isset($retornoPlano['planoId'])){

				$sqlInclusaoPlano  .= linhaJsonEdicao('CODIGO_PLANO_GATEWAY', $retornoPlano['planoId']);     
				$sqlInclusaoPlano  .= linhaJsonEdicao('VALOR_PLANO', $dadosInput['dados']['valorFatura']);     				
				$sqlInclusaoPlano  .= linhaJsonEdicao('DATA_CRIACAO_PLANO', dataHoje(), 'D');   
				gravaEdicao('ESP_PLANOS_GATEWAY', $sqlInclusaoPlano, 'I', ''); 

				$codigoPlanoZs = $retornoPlano['planoId'];
			}
		}else{
			$codigoPlanoZs = $resultadoPlano->CODIGO_PLANO_GATEWAY;
		}

		if(!isset($codigoPlanoZs)){
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG'] = 'Erro ao encontrar Plano';
		}else{
			
			$query  = '	SELECT ID_CLIENTE_ZSPAY, ID_CARTAO_GATEWAY FROM PS1000 ';			
			$query .= '	WHERE 	PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);			
			$resultado = qryUmRegistro($query);
			
			$retornoAssinatura = cria_assinatura_zspay($codigoPlanoZs, $resultado->ID_CLIENTE_ZSPAY, $resultado->ID_CARTAO_GATEWAY);
			
			if($retornoAssinatura['STATUS'] == 'OK'){

				$sqlInclusaoAssinatura  .= linhaJsonEdicao('CODIGO_PLANO_GATEWAY', $codigoPlanoZs);
				$sqlInclusaoAssinatura  .= linhaJsonEdicao('CODIGO_ASSOCIADO', $codigoAssociado);
				$sqlInclusaoAssinatura  .= linhaJsonEdicao('ID_ASSINATURA', $retornoAssinatura['assinaturaId']);     				
				$sqlInclusaoAssinatura  .= linhaJsonEdicao('DATA_ASSINATURA', dataHoje(), 'D');				
				   
				gravaEdicao('ESP_CONTROLE_ASSINATURAS', $sqlInclusaoAssinatura, 'I', ''); 

				$criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	
				$sqlEdicaoPs1020   = linhaJsonEdicao('ID_ASSINATURA_ZSPAY', $retornoAssinatura['assinaturaId']);
				gravaEdicao('PS1000', $sqlEdicaoPs1020, 'A', $criterioWhereGravacao);
	
				$retorno['STATUS'] = 'OK';
				$retorno['MSG'] = 'Assinatura realizada com sucesso!';
			   
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG'] = 'Erro ao Realizar Assinatura. -- ' . $retornoAssinatura['ERROS'];
			}
		}
	}

	echo json_encode($retorno);

}else if($dadosInput['dados']['persistencia'] =='apresentaPopUp'){
	
	$retornoPopUp = '<img height="50px" src="' . $dadosInput['dados']['url'] . '">';
	
	$retorno['MSG'] = $retornoPopUp;
	
	echo json_encode($retorno);

}else if($dadosInput['dados']['persistencia'] =='dadosLinkVendas'){	
	
	$codigoPlano = $dadosInput['dados']['plano'];
	$qtdMaxDep = $dadosInput['dados']['qtdMaxDep'];
	$codigoVendedor = $dadosInput['dados']['codigoVendedor'];
	$nomeVendedor = $dadosInput['dados']['nomeVendedor'];

	$url = retornaValorConfiguracao('LINK_PORTAL_AL2') . '?t=A&d=site/autoContratacao&plano=' . $codigoPlano .  '&qtmaxdep=' . $qtdMaxDep . '&codVendedor='.$codigoVendedor;

	$mensagem  = ' O link de vendas foi criado para o vendedor ' . $nomeVendedor . ' <br> ';
	$mensagem .= ' Por favor, copie a URL abaixo: <br> ';
	$mensagem .= $url;
	
	$retorno['MSG'] = $mensagem;
	
	echo json_encode($retorno);
	
}else if($dadosInput['dados']['persistencia'] == 'gerarBoletoPagBank'){
	
	require_once('../lib/pagBank.php');
	$retorno = Array();

	$query  = ' SELECT 	PS1020.NUMERO_REGISTRO, VALOR_FATURA, DATA_VENCIMENTO, NOME_ASSOCIADO, NUMERO_CPF, ENDERECO_EMAIL, ';
	$query .= ' 		ENDERECO, CIDADE, BAIRRO, ESTADO, CEP ';
	$query .= ' FROM PS1020 ';
	$query .= ' INNER JOIN PS1000 ON (PS1020.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ';
	$query .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
	$query .= ' WHERE PS1020.NUMERO_REGISTRO = ' .aspas($dadosInput['dados']['registro']);
	$resultado = qryUmRegistro($query);	

	$auxEndereco = $resultado->ENDERECO;
    $auxEndereco = explode(',',$auxEndereco);
    $endereco    = $auxEndereco[0];
    
    $numeroEndereco = '';
    if(count($auxEndereco)>1){
        $auxEndereco 		 = explode('-',$auxEndereco[1]);
        $numeroEndereco      = $auxEndereco[0];         
    }

	$numeroRegistro = $resultado->NUMERO_REGISTRO;
	$nomeAssociado 	= $resultado->NOME_ASSOCIADO;
	$cpf 			= $resultado->NUMERO_CPF;
	$email 			= $resultado->ENDERECO_EMAIL;
	$valorFatura 	= $resultado->VALOR_FATURA;
	$cidade 		= $resultado->CIDADE;
	$bairro 		= $resultado->BAIRRO;
	$estado 		= $resultado->ESTADO;
	$cep 			= $resultado->CEP;
	
	
	$dataVencimento = dataToSql(SqlToData($resultado->DATA_VENCIMENTO), false);
	
	$retornoBoleto = cria_cobranca_boleto($numeroRegistro, 'BOLETO: ' . $numeroRegistro,sanitizeString($valorFatura),$dataVencimento,$nomeAssociado,$cpf,$email,$endereco,$numeroEndereco,$cidade,$bairro,$estado,sanitizeString($cep));	

	if($retornoBoleto['STATUS'] == 'OK'){
		$retorno['STATUS']  	=	'OK';
		$retorno['LINK']  		= 	$retornoBoleto['HREF'];
		$retorno['MSG']  		= 	'Boleto emitido com sucesso!';
	}else{
		$retorno['STATUS']  	=	'ERRO';
		$retorno['MSG']  		= 	'Erro ao emitir boleto.';
	}
	
	echo json_encode($retorno);
}

?>
