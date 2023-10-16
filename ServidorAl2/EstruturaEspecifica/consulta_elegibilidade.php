<?php
require('../lib/base.php');

if($dadosInput['tipo']== 'dados'){
	$codigoAssociado = $dadosInput['codAssociado'];
	
	$queryAssoc  = ' SELECT ';
	$queryAssoc .= '	PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.DATA_NASCIMENTO, PS1000.CODIGO_PLANO, PS1000.DATA_ADMISSAO, PS1000.DATA_EXCLUSAO, PS1000.DATA_VALIDA_CARENCIA,  ';
	$queryAssoc .= '	case when (PS1000.DATA_EXCLUSAO IS NULL or (PS1000.DATA_EXCLUSAO >= current_timestamp)) then "ATIVO" ELSE "EXCLUIDO" END STATUS, ';
	$queryAssoc .= '	PS1000.CODIGO_CNS, PS1000.CODIGO_SITUACAO_ATENDIMENTO, PS1000.CODIGO_TITULAR, PS1010.CODIGO_EMPRESA, PS1010.NOME_EMPRESA, PS1000.DATA_VALIDADE_CARTEIRINHA, ';
	$queryAssoc .= '	PS1030.NOME_PLANO_FAMILIARES, ';
	$queryAssoc .= '	CASE 
						   CODIGO_TIPO_ACOMODACAO WHEN "1" THEN "INDIVIDUAL" 
												  WHEN "2" THEN "COLETIVO" 
												  WHEN "3" THEN "SEM ACOMODAÇÃO"
												  WHEN "4" THEN "SEMI PRIVATIVO" 
						END AS TIPO_ACOMODACAO, NOME_MOTIVO_EXCLUSAO ';
	$queryAssoc .= ' FROM PS1000 ';
	$queryAssoc .= ' INNER JOIN PS1030  ON PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO ';	
	$queryAssoc .= ' INNER JOIN PS1010  ON PS1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA ';	
	$queryAssoc .= ' LEFT OUTER JOIN PS1047 ON (PS1000.CODIGO_MOTIVO_EXCLUSAO = PS1047.CODIGO_MOTIVO_EXCLUSAO) '; 		
	$queryAssoc .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado); 		
	$resAssoc = jn_query($queryAssoc); 
	$rowAssoc = jn_fetch_object($resAssoc);
	
	$status = $rowAssoc->STATUS;
	
	if(retornaValorConfiguracao('TRATAR_INADIMPLENTE_ELEG') == 'SIM'){				
		$queryInad = 'select cfg0001.quantidade_dias_inadimp from cfg0001';
		$resInad = jn_query($queryInad);
		$rowInad = jn_fetch_object($resInad);
		
		$query  = 'SELECT * From sp_param_resumo_beneficiario(' . aspas($codigoAssociado) . ') ';
		$res = jn_query($query);
		$row3 = jn_fetch_object($res);
		
		if($row3->QUANTIDADE_DIAS_EM_ABERTO > $rowInad->QUANTIDADE_DIAS_INADIMP){
			$status = 'Inadimplente';
		}
		
	}
	
	
	$alteraStatus = false; 
	$situacaoAtend = ''; 
	
	if($_SESSION['codigoSmart'] == '4055'){//Sintimmmeb
		$alteraStatus = true; 
		
		if($Data['beneficiario'][0]->CODIGO_SITUACAO_ATENDIMENTO == '3' || $Data['beneficiario'][0]->CODIGO_SITUACAO_ATENDIMENTO == '5'){
			$alteraStatus = false;
		}
		
		if($rowAssoc->DATA_EXCLUSAO){
			$status = 'EXCLUIDO';
		}elseif($rowAssoc->CODIGO_SITUACAO_ATENDIMENTO){
			$querySituacao = '	
						SELECT
							PS1041.DESCRICAO_SITUACAO
						FROM PS1041					
						WHERE (FLAG_ATENDIMENTO_SUSPENSO = "S" or (PS1041.CODIGO_SITUACAO_ATENDIMENTO = "5") or (PS1041.CODIGO_SITUACAO_ATENDIMENTO = "24") or (PS1041.CODIGO_SITUACAO_ATENDIMENTO = "25")) AND PS1041.CODIGO_SITUACAO_ATENDIMENTO = ' . aspas($rowAssoc->CODIGO_SITUACAO_ATENDIMENTO);
			
			$resSituacao  = jn_query($querySituacao);
			$rowSituacao = jn_fetch_object($resSituacao);			
			$situacaoAtend = $rowSituacao->DESCRICAO_SITUACAO;						
		}
		if($situacaoAtend && $alteraStatus){
			if($rowAssoc->CODIGO_SITUACAO_ATENDIMENTO == 5 || $rowAssoc->CODIGO_SITUACAO_ATENDIMENTO == 24 || $rowAssoc->CODIGO_SITUACAO_ATENDIMENTO == 25){
				$status = 'ATIVO.';				
			}else{				
				$status = 'PENDENCIA ADMINISTRATIVA';
			}
		}
	
	}elseif($_SESSION['codigoSmart'] == '4200'){//Propulsao

		$queryEmpresa = 'SELECT NUMERO_CNPJ FROM CFGEMPRESA';
		$resultadoEmpresa = qryUmRegistro($queryEmpresa);

		$cnpj = $resultadoEmpresa->NUMERO_CNPJ;

		if($cnpj == '29.846.400/0001-02'){
			if($rowAssoc->DATA_VALIDA_CARENCIA){
				$databd     = $rowAssoc->DATA_VALIDA_CARENCIA;
				$databd     = explode("-",$databd); 		
				$dataBol    = mktime(0,0,0,$databd[1],$databd[2],$databd[0]);
				$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
				$dias       = ($dataBol - $data_atual)/86400;
				$diasFaltantes = ceil($dias);
				
				if($diasFaltantes > 0){
					$status = 'AGUARDANDO INICIO DE VIGENCIA';
				}
			}else{
				$status = 'AGUARDANDO INICIO DE VIGENCIA';
			}		
		}		

	}else{
		if($rowAssoc->CODIGO_SITUACAO_ATENDIMENTO){
			$querySituacao = '	
						SELECT
							PS1041.DESCRICAO_SITUACAO
						FROM PS1041					
						WHERE PS1041.CODIGO_SITUACAO_ATENDIMENTO = ' . aspas($rowAssoc->CODIGO_SITUACAO_ATENDIMENTO);
			
			$resSituacao  = jn_query($querySituacao);
			$rowSituacao = jn_fetch_object($resSituacao);
			
			$situacaoAtend = $rowSituacao->DESCRICAO_SITUACAO;			
		}
	}
	
	$empresa = $rowAssoc->NOME_EMPRESA;
	
	if($_SESSION['codigoSmart'] == '4246' and $rowAssoc->CODIGO_EMPRESA = '400'){//MV2C
		$empresa = 'PLANO COLETIVO POR ADESÃO';
	}
	
	$query  = 'SELECT * From sp_param_resumo_beneficiario(' . aspas($codigoAssociado) . ') ';
	$res = jn_query($query);
	$row3 = jn_fetch_object($res);
	
	$queryTel = 'SELECT * FROM PS1006 WHERE NUMERO_TELEFONE <> "0" AND CODIGO_ASSOCIADO = ' . aspas($rowAssoc->CODIGO_ASSOCIADO);
	$resTel = jn_query($queryTel);
	$i = 0;
	$telefone = '';
	while($rowTel = jn_fetch_object($resTel)){		
		if($i > 0){
			$telefone .= ' - ';
		}
		
		$telefone .= '(' . $rowTel->CODIGO_AREA . ') ' . $rowTel->NUMERO_TELEFONE;
		
		$i++;
	}
	
	$dadosAssoc['SITUACAO'] 					= jn_utf8_encode($status);
	$dadosAssoc['CODIGO'] 						= jn_utf8_encode($rowAssoc->CODIGO_ASSOCIADO);
	$dadosAssoc['NOME_ASSOCIADO'] 				= jn_utf8_encode($rowAssoc->NOME_ASSOCIADO);
	$dadosAssoc['NOME_EMPRESA'] 				= jn_utf8_encode($empresa);
	$dadosAssoc['DATA_ADMISSAO'] 				= SqlToData($rowAssoc->DATA_ADMISSAO);
	$dadosAssoc['DATA_NASCIMENTO'] 				= SqlToData($rowAssoc->DATA_NASCIMENTO);
	$dadosAssoc['DATA_EXCLUSAO'] 				= SqlToData($rowAssoc->DATA_EXCLUSAO);
	$dadosAssoc['DATA_VALIDA_CARENCIA'] 		= SqlToData($rowAssoc->DATA_VALIDA_CARENCIA);	
	$dadosAssoc['MOTIVO_EXCLUSAO'] 				= jn_utf8_encode($rowAssoc->NOME_MOTIVO_EXCLUSAO);
	$dadosAssoc['NUMERO_CNS'] 					= jn_utf8_encode($rowAssoc->CODIGO_CNS);
	$dadosAssoc['DESCRICAO_SITUACAO'] 			= jn_utf8_encode($situacaoAtend);
	$dadosAssoc['CODIGO_PLANO'] 				= jn_utf8_encode($rowAssoc->CODIGO_PLANO);
	$dadosAssoc['NOME_PLANO'] 					= jn_utf8_encode($rowAssoc->NOME_PLANO_FAMILIARES);
	$dadosAssoc['TIPO_ACOMODACAO'] 				= jn_utf8_encode($rowAssoc->TIPO_ACOMODACAO);
	$dadosAssoc['NUMERO_TELEFONE'] 				= jn_utf8_encode($telefone);
	$dadosAssoc['QUANTIDADE_TOTAL_FATURAS'] 	= jn_utf8_encode($row3->QUANTIDADE_TOTAL_FATURAS);
	$dadosAssoc['QUANTIDADE_DIAS_EM_ABERTO'] 	= jn_utf8_encode($row3->QUANTIDADE_DIAS_EM_ABERTO);	
	$dadosAssoc['QUANTIDADE_FATURAS_EM_ABERTO'] = jn_utf8_encode($row3->QUANTIDADE_FATURAS_EM_ABERTO);
	$dadosAssoc['QUANTIDADE_FATURAS_PAGAS'] 	= jn_utf8_encode($row3->QUANTIDADE_FATURAS_PAGAS);
	$dadosAssoc['VALOR_TOTAL_EM_ABERTO'] 		= toMoeda($row3->VALOR_TOTAL_EM_ABERTO);
	$dadosAssoc['VALOR_TOTAL_PAGO'] 			= toMoeda($row3->VALOR_TOTAL_PAGO);
	$dadosAssoc['FOTO']  = '';
	
	if(retornaValorCFG0003('MOSTRA_IMAGEM_BENEFICIARIO_ELEGIBILIDADE')=='SIM'){
		$queryImg  = "select caminho_arquivo||caminho_arquivo_armazenado||nome_arquivo_armazenado IMAGEM FROM controle_arquivos
								INNER JOIN configuracoes_arq ON 1=1
								where NOME_TABELA = 'FOTO' and DATA_EXCLUSAO is null and CHAVE_REGISTRO =" . aspas($codigoAssociado);
		$resImg = jn_query($queryImg);
		if($rowImg = jn_fetch_object($resImg)){
			$dadosAssoc['FOTO']  = $rowImg->IMAGEM;
		}
		
	}
	
		
	echo json_encode($dadosAssoc);
}

if($dadosInput['tipo']== 'configuracoesApresentacao'){
	$retorno['APRESENTACAO_ELIBILIDADE'] = retornaValorConfiguracao('APRESENTACAO_ELIBILIDADE');
	$retorno['PLANO_DADOS_BENEF'] = retornaValorConfiguracao('APRESET_PLANO_DADOS_BENEF');
	$retorno['APRESENTAR_MENSAGEM_INAD'] = retornaValorConfiguracao('APRESENTAR_MENSAGEM_INAD');	
	$retorno['DESCRICAO_DATA_CARENCIA'] = jn_utf8_encode(retornaValorConfiguracao('DESCRICAO_DATA_CARENCIA'));
	$retorno['OCULTAR_SIT_ATEND_ELEGIB'] = retornaValorConfiguracao('OCULTAR_SIT_ATEND_ELEGIB');
	$retorno['OCULTAR_EXCLUSAO_ELEGIB'] = retornaValorConfiguracao('OCULTAR_EXCLUSAO_ELEGIB');
	date_default_timezone_set("America/Fortaleza");
	$retorno['HORA_ATUAL'] = date('H:i') . ' - ' . date('d/m/Y');

	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'token'){
	$retorno['STATUS'] = 'OK';
	$retorno['MSG']    = '';
	
	$queryPrincipal = "select * From ESP_TOKEN  WHERE DATA_UTILIZACAO is null and DATA_EXPIRACAO > GETDATE() and CODIGO_ASSOCIADO =" . aspas($dadosInput['codAssociado']).' and TOKEN = '.aspas($dadosInput['token']);
	$resultQuery    = jn_query($queryPrincipal);
	if($objResult      = jn_fetch_object($resultQuery)){
		$update  = ' UPDATE ESP_TOKEN SET DATA_UTILIZACAO = GETDATE(), ';

		if($_SESSION['perfilOperador'] == 'PRESTADOR')
			$update .= ' CODIGO_PRESTADOR= '.aspas($_SESSION['codigoIdentificacao']) . ', ';

		$update .= ' TABELA='.aspas('ELEGI').' WHERE NUMERO_REGISTRO ='. aspas($objResult->NUMERO_REGISTRO);
		jn_query($update);
	}else{

		validaTokenInvalido($dadosInput['codAssociado'], $dadosInput['token'], 'ELEGI');

		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Token Invalido!';	
	}
	
	
	echo json_encode($retorno);
}

if($dadosInput['tipo']== 'respostaFoto'){
	$retorno['STATUS'] = 'OK';
	$retorno['MSG']    = '';
	
	$insert = 'INSERT INTO ESP_VALIDACAO_FOTO
				   (CODIGO_ASSOCIADO
				   ,PERFIL_USUARIO
				   ,CODIGO_USUARIO
				   ,DATA_VALIDACAO_FOTO
				   ,RESPOSTA_VALIDACAO_FOTO)
			   VALUES
				   ('.aspas($dadosInput['codAssociado']).'
				   ,'.aspas($_SESSION['perfilOperador']).'
				   ,'.aspas($_SESSION['codigoIdentificacao']).'
				   ,getdate()
				   ,'.aspas($dadosInput['respostaFoto']).')';

	$resultQuery    = jn_query($insert);
	
	echo json_encode($retorno);
}

function validaStatus($dataExclusao){
	
	$databd = SqlToData($dataExclusao);
	$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
	
	if($databd)
	{
		$databd = explode("/",$databd); 
		$dataBol = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);	
	}
	else
	{
		$data_atual = mktime(0,0,0,date("m"),(date("d") + 3),date("Y"));
	}
	
	if(($dataExclusao != '') && ($dataBol <= $data_atual)){
		return 'ASSOCIADO EXCLUÍDO';
	}else{
		return 'ATIVO';
	}
}
?>