<?php

function saidaCampo($tipo,$tabela,$campo,$valor,$campos){
//$tipo = INC ALT
	$retorno=array();
	$retorno['MSG'] ='';
	$retorno['MSG_CONFIRMA'] ='';
	$retorno['CAMPOS'] =array();
	
/*	////////////////////////////
	//EXEMPLO DE COMO UTILIZAR//
	////////////////////////////
	
	if($tabela=='PS1000'){
		if($campo=='NUMERO_CPF'){
						
			$retorno['MSG'] ='Mensagem Retorno Dados';
			$retorno['MSG_CONFIRMA'] ='Mensagem Retorno Dados'.$campos['NUMERO_CPF'];
			
			$campo = array();
			$campo['NOME_CAMPO']= 'NUMERO_CPF';
			$campo['VALOR']= '11111111111';
			
			$retorno['CAMPOS'][] = $campo;
*/
	
	
	if($tabela=='PS6500'){
		
		if($_SESSION['codigoSmart'] == '3419' and $campo=='CODIGO_PROFISSIONAL_SOLIC'){
			if($valor['VALOR']>0){
				$querySaida = 'select
								PS5286.nome_profissional NOME_PRESTADOR,PS5286.CODIGO_CONSELHO_PROFISS,PS5286.estado_conselho_classe UF_CONSELHO_PROFISS,PS5286.numero_conselho_classe NUMERO_CRM
								from PS5286 Where CODIGO_PROFISSIONAL =  '. aspas($valor['VALOR']);
				
				$resSaida = jn_query($querySaida);
				if($rowSaida = jn_fetch_object($resSaida)){
					$campo = array();
			
					$campo['NOME_CAMPO']= 'NOME_PRESTADOR_SOLICITANTE';
					$campo['COMPORTAMENTO']= '2';
					$campo['VALOR']= $rowSaida->NOME_PRESTADOR;
					
					$retorno['CAMPOS'][] = $campo;
					
					$campo = array();
			
					$campo['NOME_CAMPO']= 'CODIGO_CONSELHO_PROFISS';
					$campo['COMPORTAMENTO']= '2';
					$campo['VALOR']= $rowSaida->CODIGO_CONSELHO_PROFISS;
					
					$retorno['CAMPOS'][] = $campo;
					
					$campo = array();
					
					$campo['NOME_CAMPO']= 'NUMERO_CONSELHO_CLASSE';
					$campo['COMPORTAMENTO']= '2';
					$campo['VALOR']= $rowSaida->NUMERO_CRM;
					
					$retorno['CAMPOS'][] = $campo;
					
					
					$campo = array();
			
					$campo['NOME_CAMPO']= 'ESTADO_CONSELHO_CLASSE';
					$campo['COMPORTAMENTO']= '2';
					$campo['VALOR']= $rowSaida->UF_CONSELHO_PROFISS;
					
					$retorno['CAMPOS'][] = $campo;
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NOME_PRESTADOR_SOLICITANTE', '1');
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CONSELHO_PROFISS', '1');
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_CONSELHO_CLASSE', '1');
					$retorno['CAMPOS'][] = alteraComportamentoCampo('ESTADO_CONSELHO_CLASSE', '1');
				}
			}else{

				$retorno['CAMPOS'][] = alteraComportamentoCampo('NOME_PRESTADOR_SOLICITANTE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CONSELHO_PROFISS', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_CONSELHO_CLASSE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('ESTADO_CONSELHO_CLASSE', '1');
			}
		}
		
		if($campo=='TIPO_GUIA'){
			
			if ($valor == 'C'){

				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PRESTADOR_EXECUTANTE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERN', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CONSULTA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_SAIDA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ACIDENTE_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORARIO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_TOTAL_COBRADO', '1');
	
				if(retornaValorConfiguracao('OCULTAR_CID_AUTORIZACAO') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');
				
				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '1');

				
				if(retornaValorConfiguracao('APRESENTA_CONSULTA_FIXO') == 'SIM'){
					$campo = array();
					$campo['NOME_CAMPO']= 'CODIGO_TIPO_ATENDIMENTO';
					$campo['COMPORTAMENTO']= '1';
					$campo['OBRIGATORIO'] = true;
					
					$linha[0]['VALOR']= 4;
					$linha[0]['LABEL']= 'CONSULTA';
					$linha[1]['VALOR']= 22;
					$linha[1]['LABEL']= 'TELESAUDE';						

					$campo['GRUPO_DADOS'] = $linha;
					$campo['TIPO'] =  'COMBOBOX';
					$retorno['CAMPOS'][] = $campo;

				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');						
				}	

				$campo = array();
				$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
				$campo['COMPORTAMENTO']= '1';					
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}
				$retorno['CAMPOS'][] = $campo;

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');

				$campo = array();
				$campo['NOME_CAMPO']	= 'PROCEDIMENTO_PRINCIPAL';
				$campo['VALOR'] 		= '10101012';		
				$campo['COMPORTAMENTO']	= '3';
				$retorno['CAMPOS'][] 	= $campo;

				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');

				if($_SESSION['codigoSmart'] == '4316'){ // Medhealth
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '1');						
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '3');						
				}
			}
			else if ($valor == 'S'){
					
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PRESTADOR_EXECUTANTE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERN', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ACIDENTE_TRABALHO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_REGISTRO_TRANSACAO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORARIO_PROCEDIMENTO', '1');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('PROCEDIMENTO_PRINCIPAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_TOTAL_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '3');
				
				
				if(retornaValorConfiguracao('OCULTAR_CID_AUTORIZACAO') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');

				if(retornaValorConfiguracao('OCULTAR_SOLIC_AUTORIZ') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '1');	

				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '1');	
					
				if(retornaValorConfiguracao('OCULTAR_CAMPO_TIPO_CONSULTA') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CONSULTA', '3');	

				if(retornaValorConfiguracao('OCULTAR_TIPO_ACOMODACAO') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '3');

				if(retornaValorConfiguracao('OCULTAR_CLINICA_CIRUR') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '3');

				$campo = array();
				$campo['NOME_CAMPO']= 'CODIGO_TIPO_SAIDA';
				$campo['COMPORTAMENTO']= '1';
				if(retornaValorConfiguracao('TIPO_SAIDA_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_SAIDA_FIXO');
					$campo['COMPORTAMENTO']	= '2';
					$campo['TIPO']	= 'TEXT';
				}
				$retorno['CAMPOS'][] = $campo;
	
				$campo = array();			
				$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
				$campo['COMPORTAMENTO']= '1';
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}					
				$retorno['CAMPOS'][] = $campo;

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');
				
				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');
			}
			else if ($valor == 'A'){
					
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PRESTADOR_EXECUTANTE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ACIDENTE_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORARIO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('PROCEDIMENTO_PRINCIPAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_TOTAL_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '3');


				if(retornaValorConfiguracao('OCULTAR_CID_AUTORIZACAO') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');

				if(retornaValorConfiguracao('APRESENTA_CODIGO_REGIME_INTERN') == 'SIM'){						
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERN', '1');
				}else{	
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERN', '3');
				}
				
				if(retornaValorConfiguracao('OCULTAR_SOLIC_AUTORIZ') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '1');
					
				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '1');

				if(retornaValorConfiguracao('APRESENTA_COD_TIPO_ACOM') == 'SIM'){						
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '1');
				}else{	
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				}

				if(retornaValorConfiguracao('OCULTAR_CAMPO_TIPO_CONSULTA') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CONSULTA', '3');

				$campo = array();
				$campo['NOME_CAMPO']= 'CODIGO_TIPO_SAIDA';
				$campo['COMPORTAMENTO']= '1';
				if(retornaValorConfiguracao('TIPO_SAIDA_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_SAIDA_FIXO');
					$campo['COMPORTAMENTO']	= '2';
					$campo['TIPO']	= 'TEXT';
				}
				$retorno['CAMPOS'][] = $campo;


				$campo = array();
				$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
				$campo['COMPORTAMENTO']= '1';					
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}
				$retorno['CAMPOS'][] = $campo;		

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');
			
				
				if(retornaValorConfiguracao('APRESENTA_QUANT_DIAS_INTERN') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIAS_INTERNACAO', '1');

				if(retornaValorConfiguracao('APRESENTA_QUANT_DIAS_PRORR') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIAS_PRORROGADO', '1');

				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');
			}
			else if ($valor == 'I'){

				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PRESTADOR_EXECUTANTE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERN', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_INTERNACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA_INTERNACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ACIDENTE_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORARIO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');

					
				if(retornaValorConfiguracao('OCULTAR_CID_AUTORIZACAO') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');

				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '1');

				$campo = array();
				$campo['NOME_CAMPO']= 'CODIGO_TIPO_SAIDA';
				$campo['COMPORTAMENTO']= '1';
				if(retornaValorConfiguracao('TIPO_SAIDA_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_SAIDA_FIXO');
					$campo['COMPORTAMENTO']	= '2';
					$campo['TIPO']	= 'TEXT';
				}
				$retorno['CAMPOS'][] = $campo;

				$campo = array();
				$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
				$campo['COMPORTAMENTO']= '1';
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}
				$retorno['CAMPOS'][] = $campo;

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');

				if(retornaValorConfiguracao('APRESENTA_QUANT_DIAS_INTERN') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIAS_INTERNACAO', '1');

				if(retornaValorConfiguracao('APRESENTA_QUANT_DIAS_PRORR') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIAS_PRORROGADO', '1');

				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');

				if($_SESSION['codigoSmart'] == '4316'){ // medhealth
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '3');						
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '1');						
				}

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');
				

				$retorno['CAMPOS'][] = alteraComportamentoCampo('PROCEDIMENTO_PRINCIPAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_TOTAL_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '3');
			}
		}
	}
	else if($tabela=='PS5750'){
		
		if($campo=='TIPO_GUIA'){
			
			if ($valor == 'C'){
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_SAIDA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICADOR_ACIDENTE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '3');


				if(retornaValorConfiguracao('INABILITA_CODIGO_ATENDIMENTO') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '3');
					
			
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo = array();
					$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
					$campo['COMPORTAMENTO']= '1';					
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');

				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');
				
				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');


				$campo = array();
				$campo['NOME_CAMPO']= 'PROCEDIMENTO_PRINCIPAL';
				$campo['COMPORTAMENTO']= '2';
				$campo['TIPO']= 'TEXT';
				$campo['VALOR']= '10101012';				
				$retorno['CAMPOS'][] = $campo;

			}
			else if ($valor == 'S'){

				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ACIDENTE_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('PROCEDIMENTO_PRINCIPAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_TOTAL_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '3');

				if(retornaValorConfiguracao('APRESENTA_CID_G_SADT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');

				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '1');	

				if(retornaValorConfiguracao('APRES_TP_CONSULTA_G_SADT') == 'SIM'){
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '1');
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '3');						
				}
					
				if(retornaValorConfiguracao('APRESENTA_TP_SAIDA_G_SADT') == 'SIM'){
					$campo = array();
			
					$campo['NOME_CAMPO']= 'CODIGO_TIPO_SAIDA';
					$campo['COMPORTAMENTO']= '1';

					if(retornaValorConfiguracao('TIPO_SAIDA_FIXO') > 0){
						$campo['VALOR'] = retornaValorConfiguracao('TIPO_SAIDA_FIXO');
						$campo['COMPORTAMENTO']	= '2';
						$campo['TIPO']	= 'TEXT';
					}
					
					$retorno['CAMPOS'][] = $campo;
				}

				if(retornaValorConfiguracao('APRES_DT_TRANSACAO_G_SADT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');

				if(retornaValorConfiguracao('APRESENTA_DT_SOLICITACAO_G_SADT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '1');

				if(retornaValorConfiguracao('APRES_MED_TRABALHO_G_SADT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');

				if(retornaValorConfiguracao('APRES_PRONTO_SOCORRO_G_SADT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '1');

				if(retornaValorConfiguracao('APRESENTA_IND_CLINICA_G_SADT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '1');


				$campo = array();		
				$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
				$campo['COMPORTAMENTO']= '1';				
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}
				$retorno['CAMPOS'][] = $campo;

				if(retornaValorConfiguracao('APRES_GUIA_ORIGINAL_G_SADT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');

				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');				
			}
			else if ($valor == 'A'){

				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ACIDENTE_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORARIO_PROCEDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICADOR_ACIDENTE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('PROCEDIMENTO_PRINCIPAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_TOTAL_COBRADO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '3');


				if(retornaValorConfiguracao('OCULTAR_TIPO_INTERNACAO') == 'SIM'){
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '3');
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '1');
				}

				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '1');							
	
				if(retornaValorConfiguracao('OCULTAR_TIPO_CONSULTA_AMB') != 'SIM'){
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '1');		
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '3');		
				}
					
				$campo = array();
		
				$campo['NOME_CAMPO']= 'CODIGO_TIPO_SAIDA';
				$campo['COMPORTAMENTO']= '1';

				if(retornaValorConfiguracao('TIPO_SAIDA_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_SAIDA_FIXO');
					$campo['COMPORTAMENTO']	= '2';
					$campo['TIPO']	= 'TEXT';
				}
					
				$retorno['CAMPOS'][] = $campo;

				if(retornaValorConfiguracao('INABILITA_DATA_SOLICITACAO') == 'SIM'){
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '3');						
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '1');
				}

				if(retornaValorConfiguracao('OCULTAR_DESCRICAO_OBSERVACAO') != 'SIM'){
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_OBSERVACAO', '1');						
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_OBSERVACAO', '3');						
				}

				if(retornaValorConfiguracao('OCULTAR_INDICACAO_CLINICA') != 'SIM'){
					$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '1');						
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '3');						
				}


				$campo = array();
				$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
				$campo['COMPORTAMENTO']= '1';
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}
				$retorno['CAMPOS'][] = $campo;

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');	

				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');	
				
				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');	

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');

			}
			else if ($valor == 'I'){

				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CID', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_ESPECIALIDADE', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_LOCAL_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MEDICAMENTO_MATERIAL', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_PROFISSIONAL_SOLIC', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERNACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SERVICO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA_INTERNACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_PROCEDIMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_REGISTRO_TRANSACAO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DECLARACAO_NASCIDO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_DIAGNOSTICO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_FINAL_FATURAMENTO', '1');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DESCRICAO_JUSTIFICATIVA', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ACIDENTE_TRABALHO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ALTO_CUSTO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_ATENDIMENTO_RN', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_MEDICINA_TRABALHO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PLANTAO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('FLAG_PRONTO_SOCORRO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_FINAL_FATURAMENTO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_INICIO_FATURAMENTO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORA_REGISTRO_TRANSACAO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICADOR_ACIDENTE', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_ORIGINAL', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('PROCEDIMENTO_PRINCIPAL', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FATOR', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_FILMES', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_PROCEDIMENTOS', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ACOMODACAO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_CLINICA_CIRURGICA', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATOR', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_FATURAMENTO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_VIA_ACESSO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_COBRADO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('VALOR_TOTAL_COBRADO', '1');				
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CARATER_SOLICITACAO', '1');

					
				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '1');	

				if(retornaValorConfiguracao('OCULTAR_TIPO_CONSULTA') != 'SIM'){						
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '1');
				}else{
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '3');
				}

				if(retornaValorConfiguracao('OCULTAR_DT_INIC_FAT') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_INICIO_FATURAMENTO', '1');

				if(retornaValorConfiguracao('OCULTAR_DT_INIC_FAT') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '1');

				if(retornaValorConfiguracao('OCULTAR_TIPO_SAIDA_FAT') != 'SIM'){
				
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_SAIDA', '3');					

					if(retornaValorConfiguracao('TIPO_SAIDA_FIXO') > 0){
						$campo = array();
						$campo['NOME_CAMPO']= 'CODIGO_TIPO_SAIDA';
						$campo['VALOR'] = retornaValorConfiguracao('TIPO_SAIDA_FIXO');
						$campo['COMPORTAMENTO']	= '2';
						$campo['TIPO']	= 'TEXT';

						$retorno['CAMPOS'][] = $campo;
					}
				}


				$campo = array();
				$campo['NOME_CAMPO']= 'INDICADOR_ACIDENTE';
				$campo['COMPORTAMENTO']= '1';				
				if(retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO') > 0){
					$campo['VALOR'] = retornaValorConfiguracao('TIPO_INDICADOR_ACIDENTE_FIXO');						
					$campo['TIPO']	= 'COMBOBOX';
				}
				$retorno['CAMPOS'][] = $campo;

				if(retornaValorConfiguracao('OCULTAR_INDICADOR_ACIDENTE') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICADOR_ACIDENTE', '3');

				if(retornaValorConfiguracao('OCULTAR_QTDE_DIARIAS_UTI') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');

				if(retornaValorConfiguracao('OCULTAR_TIPO_ACOMODACAO') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');

				if(retornaValorConfiguracao('OCULTAR_MOTIVO_ENCERRAMENTO') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');

				if(retornaValorConfiguracao('OCULTAR_REGIME_ATENDIMENTO') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_ATENDIMENTO', '3');
				
				if(retornaValorConfiguracao('OCULTAR_ELETIVA_URGENCIA') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_ELETIVA_URGENCIA', '3');

				if(retornaValorConfiguracao('OCULTAR_DIAG_OBITO') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '1');
					
				if(retornaValorConfiguracao('OCULTAR_HORARIO_PROC') != 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('HORARIO_PROCEDIMENTO', '1');

				if(retornaValorConfiguracao('APRESENTA_NUM_G_PREST_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '1');
				
				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');

				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');

			}else if ($valor == 'O'){

				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_AUTORIZACAO', '3');							
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_CBO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICADOR_ACIDENTE', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('PROCEDIMENTO_PRINCIPAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_CONSULTA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('INDICACAO_CLINICA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('HORARIO_PROCEDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ACOMODACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_MOTIVO_ENCERRAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_GUIA_PRESTADOR', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_INTERNACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_SAIDA', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('QUANTIDADE_DIARIAS_UTI', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_FATURAMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_SOLICITACAO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DIAGNOSTICO_OBITO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TIPO_ATENDIMENTO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NUMERO_CONSELHO', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('UF', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CONSELHO_PROFISSIONAL', '3');
				$retorno['CAMPOS'][] = alteraComportamentoCampo('NOME_SOLICITANTE', '3');		
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_INTERNACAO', '3');	
				$retorno['CAMPOS'][] = alteraComportamentoCampo('DATA_ALTA_INTERNACAO', '3');		
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_REGIME_ATENDIMENTO', '3');	
				
				
				if(retornaValorConfiguracao('APRESENTA_PREST_SOL_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_SOLICITANTE', '3');
				
				if(retornaValorConfiguracao('APRESENTA_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TEMPO_DOENCA', '1');
				
				if(retornaValorConfiguracao('APRES_UN_TEMPO_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('UNIDADE_TEMPO_DOENCA', '1');
												
				if(retornaValorConfiguracao('APRESENTA_TP_DOENCA_AUT') == 'SIM')
					$retorno['CAMPOS'][] = alteraComportamentoCampo('TIPO_DOENCA', '1');
			}

		}elseif($campo=='NUMERO_AUTORIZACAO'){
			$queryAutoriz  = 'SELECT * FROM PS6500 WHERE NUMERO_AUTORIZACAO = ' . aspas($valor);
			$queryAutoriz .= ' AND CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']);
			$resAutoriz = jn_query($queryAutoriz);
			$rowAutoriz = jn_fetch_object($resAutoriz);			
			if($rowAutoriz->CODIGO_ASSOCIADO){
				$campo = array();
				
				$campo['NOME_CAMPO']= 'CODIGO_ASSOCIADO';
				$campo['TIPO']= 'TEXT';
				$campo['VALOR']= $rowAutoriz->CODIGO_ASSOCIADO;
				
				$retorno['CAMPOS'][] = $campo;
				
				if(retornaValorConfiguracao('APRESENTA_NM_ASSOCIADO') == 'SIM'){
					$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($rowAutoriz->CODIGO_ASSOCIADO);
					$resAssoc = jn_query($queryAssoc);
					$rowAssoc = jn_fetch_object($resAssoc);
					
					$campo = array();
				
					$campo['NOME_CAMPO']= 'NOME_ASSOCIADO';
					$campo['TIPO']= 'TEXT';
					$campo['COMPORTAMENTO']= '2';
					$campo['VALOR']= $rowAssoc->NOME_ASSOCIADO;
					
					$retorno['CAMPOS'][] = $campo;
					
				}

				$campo = array();
				
				$campo['NOME_CAMPO']= 'DATA_AUTORIZACAO';
				$dataAut = '';
				if(is_object($rowAutoriz->DATA_AUTORIZACAO)){
					$dataAut = $rowAutoriz->DATA_AUTORIZACAO->format('Y-m-d H:i:s');
				}else{
					$dataAut = $rowAutoriz->DATA_AUTORIZACAO;
				}
				$campo['VALOR']= $dataAut;
				
				$retorno['CAMPOS'][] = $campo;	

				$campo = array();
				
				$campo['NOME_CAMPO']= 'DATA_PROCEDIMENTO';			
				$campo['VALOR']= $dataAut;
				
				$retorno['CAMPOS'][] = $campo;

				$campo = array();
				
				$campo['NOME_CAMPO']= 'CODIGO_CBO';
				$campo['TIPO']= 'TEXT';
				$campo['VALOR']= $rowAutoriz->CODIGO_ESPECIALIDADE;
				
				$retorno['CAMPOS'][] = $campo;
				
				if(retornaValorConfiguracao('APRESENTA_DESC_ESPEC') == 'SIM'){
					$queryEspec = 'SELECT NOME_ESPECIALIDADE FROM PS5100 WHERE CODIGO_ESPECIALIDADE = ' . aspas($rowAutoriz->CODIGO_ESPECIALIDADE);
					$resEspec = jn_query($queryEspec);
					$rowEspec = jn_fetch_object($resEspec);
					
					$campo = array();
				
					$campo['NOME_CAMPO']= 'DESCRICAO_ESPECIALIDADE';
					$campo['TIPO']= 'TEXT';
					$campo['COMPORTAMENTO']= '2';
					$campo['VALOR']= jn_utf8_encode($rowEspec->NOME_ESPECIALIDADE);
					
					$retorno['CAMPOS'][] = $campo;
					
				}								
			}
		}elseif($campo=='NUMERO_CONSELHO'){
			$queryProfis  = 'SELECT CODIGO_PROFISSIONAL, NOME_PROFISSIONAL, ESTADO_CONSELHO_CLASSE, CODIGO_CONSELHO_PROFISS FROM PS5286 WHERE NUMERO_CONSELHO_CLASSE = ' . aspas($valor);			
			$resProfis = jn_query($queryProfis);
			$rowProfis = jn_fetch_object($resProfis);			
			if($rowProfis->CODIGO_CONSELHO_PROFISS){
				if(retornaValorConfiguracao('APRESENTA_NM_SOLICITANTE') == 'SIM'){					
					$campo = array();
					
					$campo['NOME_CAMPO']= 'NOME_SOLICITANTE';
					$campo['TIPO']= 'TEXT';
					$campo['COMPORTAMENTO']= '2';
					$campo['VALOR']= $rowProfis->NOME_PROFISSIONAL;
					
					$retorno['CAMPOS'][] = $campo;
				}

				$campo = array();
				
				$campo['NOME_CAMPO']= 'CODIGO_SOLICITANTE';
				$campo['VALOR']= $rowProfis->CODIGO_PROFISSIONAL;
				$campo['TIPO']= 'TEXT';
				$retorno['CAMPOS'][] = $campo;	
				
				$campo = array();
				
				$campo['NOME_CAMPO']= 'UF';
				$campo['VALOR']= $rowProfis->ESTADO_CONSELHO_CLASSE;
				
				$retorno['CAMPOS'][] = $campo;	

				$campo = array();
				
				$campo['NOME_CAMPO']= 'CONSELHO_PROFISSIONAL';
				$campo['TIPO']= 'TEXT';
				$campo['VALOR']= $rowProfis->CODIGO_CONSELHO_PROFISS;
				
				$retorno['CAMPOS'][] = $campo;
			}
		}
	}else if($tabela=='VND1000_ON'){
		
		$token = retornaValorConfiguracao('TOKEN_SINTEGRA');
		if($token != ''){
			if(($campo=='NUMERO_CPF')or($campo=='DATA_NASCIMENTO')){
							
				if(($campos['DATA_NASCIMENTO']!='') and($campos['NUMERO_CPF']!='')and (trim($campos['NOME_ASSOCIADO'])=='')){
						
					$service_url = 'https://www.sintegraws.com.br/api/v1/execute-api.php';

					
					$cpf = '';
					$dataNascimento = '';
					
					$dataNascimento = substr($campos['DATA_NASCIMENTO'], 5,2).substr($campos['DATA_NASCIMENTO'], 8,2).substr($campos['DATA_NASCIMENTO'], 0,4);
					$cpf = str_replace('.','',$campos['NUMERO_CPF']);
					$cpf = str_replace('-','',$cpf);
					
					if((strlen($dataNascimento)==8)and(strlen($cpf)==11)){
					
					
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $service_url.'?token='.$token.'&cpf='.$cpf.'&data-nascimento='.$dataNascimento.'&plugin=CPF');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
						$head = curl_exec($ch);
						$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
																																 
						$errors = curl_error($ch);                                                                                                            
						$result = curl_exec($ch);
						$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
						$retornoJSON = json_decode($result, true);
						//{"code":"0","status":"OK","message":"Pesquisa realizada com sucesso.","cpf":"002.064.101-07","nome":"DIEGO RIBEIRO DA ROZA","data_nascimento":"21/10/1984","situacao_cadastral":"Regular","data_inscricao":"29/06/2001","genero":"M","digito_verificador":"00","comprovante":"1BF2.BA1A.53BB.02F1","version":"1"}
						curl_close($ch);
						
						
						if($retornoJSON['status'] == 'OK'){
							$campo = array();
							
							$campo['NOME_CAMPO']= 'NOME_ASSOCIADO';
							$campo['VALOR']= $retornoJSON['nome'];
							
							$retorno['CAMPOS'][] = $campo;
							
							$campo = array();
							
							$campo['NOME_CAMPO']= 'SEXO';
							$campo['VALOR']= $retornoJSON['genero'];
							
							$retorno['CAMPOS'][] = $campo;
						}else{
							$retorno['MSG'] = 'Não foi possivel efetuar a pesquisa do Cpf(1)';
						}
					}else{
						$retorno['MSG'] = 'Não foi possivel efetuar a pesquisa do Cpf(2)';
						
					}
								
				}
			}
		}		
	}else if($tabela=='PS2500'){		
		if($campo=='CODIGO_ASSOCIADO'){
			$queryInad = 'Select QUANTIDADE_FATURAS_EM_ABERTO from SP_PARAM_RESUMO_BENEFICIARIO (' . aspas($valor['VALOR']) . ')';
			$resInad = jn_query($queryInad);
			$rowInad = jn_fetch_object($resInad);
			
			if($rowInad->QUANTIDADE_FATURAS_EM_ABERTO > 0){
				$retorno['MSG'] = 'Associado com pendência administrativa. Favor entrar em contato com a operadora. <br> ';
			}	
			
			if($_SESSION['codigoSmart'] == '3555'){//Cooperativa Evidente
				$queryPacotes = '	SELECT FIRST 1 * FROM PS2500
									INNER JOIN PS2510 ON (PS2500.NUMERO_PLANO_TRATAMENTO = PS2510.NUMERO_PLANO_TRATAMENTO)
									WHERE 	(CODIGO_PROCEDIMENTO = "10001" or (CODIGO_PROCEDIMENTO = "10002"))
											AND PS2500.CODIGO_ASSOCIADO =' . aspas($valor['VALOR']) . '
									ORDER BY DATA_CADASTRAMENTO DESC ';
				$resPacotes = jn_query($queryPacotes);
				if($rowPacotes = jn_fetch_object($resPacotes)){
					$retorno['MSG'] .= '<br> O procedimento ' . $rowPacotes->CODIGO_PROCEDIMENTO . ' foi cadastrado no dia ' . SqlToData($rowPacotes->DATA_CADASTRAMENTO);
				}
			}
			
			$queryTel = 'SELECT NUMERO_TELEFONE FROM PS1006 WHERE INDICE_TELEFONE = 0 AND CODIGO_ASSOCIADO = ' . aspas($valor['VALOR']);
			$resTel = jn_query($queryTel);
			$rowTel = jn_fetch_object($resTel);
			
			$campo = array();			
			$campo['NOME_CAMPO']= 'CP__EX_TELEFONE';
			$campo['VALOR']= $rowTel->NUMERO_TELEFONE;
			$retorno['CAMPOS'][] = $campo;
		}
	}else if($tabela=='PS6120'){
		if($campo=='CODIGO_ASSOCIADO'){
			$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($valor['VALOR']);
			$resAssoc = jn_query($queryAssoc);
			$rowAssoc = jn_fetch_object($resAssoc);			
			
			$campo = array();			
			$campo['NOME_CAMPO']= 'NOME_PESSOA';
			$campo['VALOR']= $rowAssoc->NOME_ASSOCIADO;
			$retorno['CAMPOS'][] = $campo;
		}
	}else if($tabela=='TMP1001_NET' || $tabela=='VND1001_ON'){
		if($campo=='CEP'){
			$json_file = file_get_contents('http://viacep.com.br/ws/'. sanitizeString($valor) .'/json');   
			$json_str = json_decode($json_file, true);	
						
			$campo = array();			
			$campo['NOME_CAMPO']= 'ENDERECO';
			$campo['VALOR']= jn_utf8_encode(strToUpper(sanitizeString($json_str['logradouro'])));
			$retorno['CAMPOS'][] = $campo;
				
			
			$campo = array();			
			$campo['NOME_CAMPO']= 'BAIRRO';
			$campo['VALOR']= jn_utf8_encode(strToUpper(sanitizeString($json_str['bairro'])));
			$retorno['CAMPOS'][] = $campo;
			
			$campo = array();			
			$campo['NOME_CAMPO']= 'CIDADE';
			$campo['VALOR']= jn_utf8_encode(strToUpper(sanitizeString($json_str['localidade'])));
			$retorno['CAMPOS'][] = $campo;
			
			$campo = array();			
			$campo['NOME_CAMPO']= 'ESTADO';
			$campo['VALOR']= jn_utf8_encode(strToUpper(sanitizeString($json_str['uf'])));
			$retorno['CAMPOS'][] = $campo;
		}
	}else if($tabela=='ESP_REEMBOLSO'){		
		if($campo=='CNPJ_PRESTADOR'){
			$cpfCnpj = sanitizeString($valor);
			$queryPrest  = ' SELECT NOME_PROFISSIONAL FROM ESP_PROFISSIONAL_REEMBOLSO ';
			$queryPrest .= ' WHERE  ((ESP_PROFISSIONAL_REEMBOLSO.NUMERO_CNPJ = ' . aspas($cpfCnpj) . ') OR (ESP_PROFISSIONAL_REEMBOLSO.NUMERO_CPF = ' . aspas($cpfCnpj) . '))';
			$resPrest = jn_query($queryPrest);
			if ($rowPrest = jn_fetch_object($resPrest)){

				$campo = array();			
				$campo['NOME_CAMPO']= 'NOME_PRESTADOR';
				$campo['VALOR']= jn_utf8_encode(strToUpper($rowPrest->NOME_PROFISSIONAL));
				$retorno['CAMPOS'][] = $campo;	

			}	
						
		}

		if($campo=='CODIGO_ASSOCIADO'){
			$codigoAssociado = $valor['VALOR'];
		
			$queryDados  = ' SELECT FIRST 1 ';
			$queryDados .= '  COALESCE(ESP_REEMBOLSO.TELEFONE_ASSOCIADO, PS1006.NUMERO_TELEFONE) AS NUMERO_TELEFONE, ';
			$queryDados .= '  COALESCE(ESP_REEMBOLSO.EMAIL_ASSOCIADO, PS1001.ENDERECO_EMAIL) AS ENDERECO_EMAIL ';
			$queryDados .= '  FROM PS1000 ';
			$queryDados .= '  LEFT OUTER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
			$queryDados .= '  LEFT OUTER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO) ';
			$queryDados .= '  LEFT OUTER JOIN ESP_REEMBOLSO ON (ESP_REEMBOLSO.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ';
			$queryDados .= '  WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
			$resDados = jn_query($queryDados);

			if ($rowDados = jn_fetch_object($resDados)){
				$campo = array();			
				$campo['NOME_CAMPO']= 'TELEFONE_ASSOCIADO';
				$campo['VALOR']= jn_utf8_encode(strToUpper($rowDados->NUMERO_TELEFONE));
				$retorno['CAMPOS'][] = $campo;	

				$campo = array();			
				$campo['NOME_CAMPO']= 'EMAIL_ASSOCIADO';
				$campo['VALOR']= jn_utf8_encode(strToUpper($rowDados->ENDERECO_EMAIL));
				$retorno['CAMPOS'][] = $campo;	
			}
		}
	}else if($tabela=='ESP_AGENDAMENTO_CIRURGICO'){
		if($campo=='CODIGO_ASSOCIADO'){
			
			$queryAssoc  = ' SELECT NOME_ASSOCIADO, COALESCE(PS1001.ENDERECO_EMAIL, PS1015.ENDERECO_EMAIL) ENDERECO_EMAIL,  PS1006.CODIGO_AREA, PS1006.NUMERO_TELEFONE FROM PS1000 ';
			$queryAssoc .= ' LEFT OUTER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO AND ((INDICE_TELEFONE IS NULL) OR (INDICE_TELEFONE = "1"))) ';
			$queryAssoc .= ' LEFT OUTER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';			
			$queryAssoc .= ' LEFT OUTER JOIN PS1015 ON (PS1000.CODIGO_ASSOCIADO = PS1015.CODIGO_ASSOCIADO) ';
			$queryAssoc .= ' WHERE  PS1000.CODIGO_ASSOCIADO = ' . aspas($valor['VALOR']);
			$resAssoc = jn_query($queryAssoc);
			$rowAssoc = jn_fetch_object($resAssoc);
			
			$campo = array();			
			$campo['NOME_CAMPO']= 'TELEFONE_ASSOCIADO';
			$campo['VALOR']= jn_utf8_encode('('. $rowAssoc->CODIGO_AREA . ') ' . $rowAssoc->NUMERO_TELEFONE);
			$retorno['CAMPOS'][] = $campo;		
			
			$campo = array();			
			$campo['NOME_CAMPO']= 'EMAIL_ASSOCIADO';
			$campo['VALOR']= jn_utf8_encode(strToUpper($rowAssoc->ENDERECO_EMAIL));
			$retorno['CAMPOS'][] = $campo;


		}
	}elseif($tabela == 'TMP1000_NET'){
		if($_SESSION['codigoSmart'] == '3423'){//Plena
			
			if($campo == 'CODIGO_PARENTESCO' and ($valor['VALOR'] == 2 or $valor['VALOR'] == 8)){//Conjuge ou Companheiro	
				$campo = array();
				$campo['NOME_CAMPO']= 'DATA_REGISTRO_CASAMENTO';			
				$campo['COMPORTAMENTO']= '1';						
				$retorno['CAMPOS'][] = $campo;
			}else{
				$campo = array();
				$campo['NOME_CAMPO']= 'DATA_REGISTRO_CASAMENTO';			
				$campo['COMPORTAMENTO']= '3';						
				$retorno['CAMPOS'][] = $campo;

			}
		}
	}
	else if($tabela == 'PS1000')
	{
		if ($campo == 'TIPO_ASSOCIADO') 
		{
			if ($valor == 'D')
			{
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_EMPRESA', '3');
				//$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TITULAR', '3');
			}
			else
			{
				$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_EMPRESA', '1');
				//$retorno['CAMPOS'][] = alteraComportamentoCampo('CODIGO_TITULAR', '3');
			}
		}
	}


	return $retorno;
}

function alteraComportamentoCampo($nomeCampo, $comportamento){

	$campo = array();
	$campo['NOME_CAMPO']= $nomeCampo;
	$campo['COMPORTAMENTO']= $comportamento;		
	return $campo;	
}