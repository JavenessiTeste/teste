<?php
function retornaPermissoesUsuarioTabela($usuario,$perfil,$tabela,$idRegistro = null,$parTabelaOriginal=null){
	
	/* 
	   Estava com permissão otimista, ou seja, tudo era permitido e apenas o que não fosse seria tratado.
	   Mudamos o critério e tudo é negado, quando for o caso o item será habilitado especificamente.
	*/
	
	$retorno['INC'] = false;
	$retorno['ALT'] = false;
	$retorno['VIS'] = false;
	$retorno['EXC'] = false;
	$retorno['CSV'] = false;
	
		
	/* Coloquei este grupo inicialmente porque quando desenvolvemos haviamos dado permissão por padrão para tudo, e apenas tirávamos a permissão quando configurávamos uma tabela/view
	   Mas descobrimos que isto traria um enorme problema de segurança, porque se o operador digitásse uma URL que não estava nos if´s abaixo o sistema criaria um cadastro dinamico completamente
	   aberto e permissivo para a respectiva tabela. 
	   Com esta alteração todas as permissões serão negadas, depois caso seja uma das tabelas abaixo revertermos as permissões porque nos ifs especificos de cada tabela/view trataremos a respectiva tabela */
	   
	if ((testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS5750')) or 
	    (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5750_CD_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6010')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_AGENDA_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6130')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6130_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_RELATORIO_GLOSA_DET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6500')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6500_CD_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6510')) or 

		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TABELA_AVO')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TABELA_PAI')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TABELA_FILHA')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TABELA_NETA')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TABELA_PAI_MAE')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TABELA_TIA')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TABELA_SOGRA')) or 

		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS2500')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS2500_CD_AL2')) or 

		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6360_ALIANCANET2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6360')) or 
		((testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6451') and ($perfil == 'OPERADOR')))  or 
		((testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_COMUNICACAO_NET_AL2') and ($perfil == 'OPERADOR')))  or 

		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1063')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1063_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6110')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6110_ALIANCANET2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1095')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1095_CD_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1000_ON')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1010_ON')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PAINEL_VENDAS')) or	
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1001_ON')) or	
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1002_ON')) or	
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1003_ON')) or	
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1005_ON')) or	
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TMP1000_CD_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1095')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TMP1000NET_EMPRESA_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6550_CD_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'CFGCOMUNICACAO_NET')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CFGCOMUNICACAO_NET_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6550')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6550_CD_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TMP1000_NET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TMP1000_CD_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS2510')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TMP1000_CD_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1000')) or 		
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TMP1000_NET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1001')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1006')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1101')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS5760')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6110')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TMP1000_NET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TMP1001_NET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'TMP1006_NET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_DADOS_ENDERECO_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_DADOS_TELEFONE_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CFGCOMUNICACAO_NET_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1069_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1069')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TMP1000NET_DEP_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_VND_CORRETOR_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'CONTROLE_ARQUIVOS')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_BENEF_EMPR_NET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TOTALIZADOR_ASSOCIADOS_NET')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_DW_REGISTROS_RELATORIOS')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CONSULTA_INT_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_INTERNACOES_PREST_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_OUVIDORIA_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_UPLOAD_ARQUIVOS_XML_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5294_AL2')) or 		
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6120')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'CFGARQUIVOS_PROCESSOS_NET')) or 		
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ASSOC_EXC_EMP_AL2')) or 				
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PROTOCOLOS_PS5750_AL2')) or 				
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_NOVAS_PENDENCIAS_PREST_AL2')) or 				
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_VND1000_OPERADORES')) or 				
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'ESP_REEMBOLSO')) or					
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_AGENDAMENTO_CIRURGICO_AL2')) or					
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'ESP_AGENDAMENTO_CIRURGICO')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PAINEL_AGENDAMENTO_CIR_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_COPARTICIPACAO_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6130')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6130_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TOKENS_VALIDOS_AL2')) or	
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TOKENS_INVALIDOS_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6511')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ESP_PLANO_TRATAMENTO')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'ESP_PLANO_TRATAMENTO')) or	
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'ESP_PRORROGACAO_INTERNACAO')) or		
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6500_PRORROGACAO_CD_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_SOLICITACAO_PRORROGACAO')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_GUIAS_ENVIADAS_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_DOCUMENTOS_REAJUSTE_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ARQUIVOS_XML_DASH_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TOTALIZAR_ARQ_XML_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ARQUIVOS_RECEBIDOS_XML_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ARQUIVOS_REJEITADOS_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6512')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_COBERTURA_PLANO_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_COPART_ADIANTADA_PJ_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_LINKS_VENDAS_AL2')) or
	

		/*INICIO VIEWS AJUSTADAS MIGRAÇÃO PX-AL2*/
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1024_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1024')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1027_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1027')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1042_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1042')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1040_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1040')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1058_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1058')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1065_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1065')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1044_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1044')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1088_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1088')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1054_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1054')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1051_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1051')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1014_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1014')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1048_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1048')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1057_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1057')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1077_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1077')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1041_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1041')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1064_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1064')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1046_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1046')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5012_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS5012')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5013_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS5013')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1097_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1097')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1016_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1016')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6410_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6410')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1045_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1045')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1047_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1047')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ESP0002_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'ESP0002')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1100_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1100')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1101_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1101')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1102_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1102')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1103_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1103')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1106_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1106')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3010_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3010')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3020_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3020')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3030_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3030')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3031_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3031')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3150_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3150')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3100_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3100')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3200_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3200')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3040_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3040')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3050_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3050')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3500_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3500')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3600_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3600')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3300_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3300')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3400_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3400')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS3410_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS3410')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7000_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7000')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7010_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7010')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7030_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7030')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7300_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7300')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7301_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7301')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7302_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7302')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7401_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7401')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7402_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7402')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7200_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7200')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7201_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7201')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7204_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7204')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7203_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7203')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7400_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7400')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7310_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7310')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7410_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7410')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7210_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7210')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS7510_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS7510')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5802_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS5802')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1022_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1022')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1105_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1105')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1030_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_REEMBOLSO_ALIANCANET2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_NF_SOLICITADAS_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CARTEIRINHA_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ANALISE_TMP1000_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'ESP_TRANSFERENCIA_CAD')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1030')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1030_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6100_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6100')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1043_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1043')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1000_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1000')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1032_GRID_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1032')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1033_GRID_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1033')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1038_GRID_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1038')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5210_GRID_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS5210')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1010_GRID_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1010')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5000_GRID_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS5000')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1002_GRID_AL2')) or 
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1002')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1001')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1001_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1003')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1003_GRID_AL2')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS1006')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'ESP_HIST_CARTOES_ASSOCIADOS')) or
		(testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1006_GRID_AL2'))
		



		/*FIM VIEWS AJUSTADAS MIGRAÇÃO PX-AL2*/

		)
	{
		$retorno['INC'] = true;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;
		$retorno['EXC'] = true;			
	}	
	else if	((testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6451') and ($perfil == 'BENEFICIARIO'))) 
	{
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}	
	else if ((testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_LOTE_CAB')) 			      or   			 
 			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PAGAMENTOS_EFETUADOS'))     or 			 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6451_AL2'))               or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_LINKS_UTEIS_NET'))          or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PROCEDIMENTOS_LIBERADOS'))  or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PROCED_LIBERADOS_ODONTO_AL2'))  or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PROTOCOLO_GERAL_SIS'))      or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_GLOSA_EXCEL_AL2'))          or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PROTOCOLOS_GUIAS_AL2'))     or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5804_AL2'))               or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_SEGUNDA_VIA_AL2'))          or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5760_CD_AL2'))            or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CONSULTA_MENSALIDADES_AL2'))or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_COMUNICACAO_NET_AL2'))      or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_COPARTICIPACAO'))           or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_LOTE_GUIAS')) 	          or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CONFERENCIA_UTILIZACAO'))   or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_UTILIZACAO_AL2'))   or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_UTILIZACAO_DESPESA_NET'))   or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PRESTADOR_SUBSTITUTO'))     or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_REAJUSTE_EMPRESAS'))        or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_COMISSOES'))                or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_MAT_PUBLICIDADE_AL2'))      or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1021_CD_AL2')) 	          or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_DETALHE_FATURAMENTO_NET')) 	          or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PROTOCOLO_GERAL_SIS'))      or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CONSULTA_MENSALIDADES_AL2')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_RELATORIO_GLOSA_CAB'))      or 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1000_EMPRESA_AL2'))       or 
 			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1030CONFIG_ON')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1030MODELOS_ON')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VND1000STATUS_ON')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_VND1000_INCONSISTENCIAS')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_GTO_NEGADA')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PENDENCIAS_PRESTADOR_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ANEXOS_PLANO_TRAT_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ANEXOS_ARQ_GUIAS_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_NOTA_PRESTADOR_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CONTRATOS_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CABECALHO_COPART_NET')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_RELATORIO_COPART_NET')) or				
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5295_AL2')) or				
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'PS6530')) or		
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_INADIMP_AL2')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TMP1000_NET_AL2')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_CONTROLE_ASSINATURA_AL2')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_OUVIDORIA_RESP_AL2')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_MANUAIS_BENEF')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5297_AL2')) or			 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS5298_AL2')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_MATMED_LIBERADOS')) or			 
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ESPECIALIDADES_PREST_SUBST')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_REATIVACAO_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_ANALISADOS_TMP1000_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TRANSFERENCIA_CADASTRO')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS1000_DEP_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TOKENS_ASSOC_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_TOKENS_VALIDOS_ASSOC_AL2')) or	
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_STATUS_AUD_PAGAMENTO_AL2')) or
			 (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_VND1000_ON')))			 
	{
		$retorno['VIS'] = true;
	}	

		
	/*
		Agora tratamos cada tabela especificamente
	*/
	
	if (($tabela=='VW_LOTE_CAB') or	    
	    ($tabela=='VW_PS5750_CD_AL2') or
	    ($tabela=='VW_PAGAMENTOS_EFETUADOS') or
	    ($tabela=='VW_COMISSOES') or
	    ($tabela=='VW_UTILIZACAO_AL2') or
	    ($tabela=='VW_PS1021_CD_AL2') or
	    ($tabela=='VW_CABECALHO_COPART_NET') or
		($tabela=='VW_LOTE_GUIAS') or
	    ($tabela=='VW_RELATORIO_COPART_NET') or
		($tabela=='VW_OUVIDORIA_AL2') or
		($tabela=='VW_OUVIDORIA_RESP_AL2') or
		($tabela=='VW_REEMBOLSO_ALIANCANET2') or
		($tabela=='VW_COPARTICIPACAO_AL2') or
		($tabela=='VW_TOKENS_VALIDOS_AL2') or
		($tabela=='VW_TOKENS_INVALIDOS_AL2') or		
		($tabela=='VW_ARQUIVOS_REJEITADOS_AL2') or
		($tabela=='VW_ARQUIVOS_RECEBIDOS_XML_AL2') or
		($tabela=='VW_PS5297_AL2') or
		($tabela=='VW_PS5298_AL2') or
	    ($tabela=='VW_RELATORIO_GLOSA_CAB')) 
	{
		$retorno['CSV'] = true;
	}
	
	if($tabela=='PS1020'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}
	
	if($tabela=='VW_PS1000_EMPRESA_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}	
	
	if($tabela=='VW_STATUS_AUD_PAGAMENTO_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}	
	
	if($tabela=='VW_AGENDA_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = true;			
	}	
	
	if (($tabela=='PS6130') || ($tabela=='VW_PS6130_AL2') || (testarTratamentoEspecial($tabela,$parTabelaOriginal,'VW_PS6360_ALIANCANET2'))) 
	{
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}

	if($tabela=='VW_PS6500_CD_AL2' && (retornaValorConfiguracao('PERMITE_EXCLUIR_AUTORIZACAO') == 'NAO' or retornaValorConfiguracao('PERMITE_EXCLUIR_AUTORIZACAO') == '')
	){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}

	if($tabela=='VW_PS6500_CD_AL2' && retornaValorConfiguracao('PERMITE_EDICAO_GUIAS_PORTAL') == 'NAO'
	){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = true;			
		$retorno['CSV'] = true;
	}

	if($tabela=='PS6510'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}

	if($tabela=='VW_PS2500_CD_AL2'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}

	if($tabela=='VW_PS1063_AL2'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	if($tabela=='VW_PS1095_CD_AL2'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	if($tabela=='VW_PS6550_CD_AL2'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	if($tabela=='VW_COBERTURA_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	if($tabela=='VW_UTILIZACAO_NET'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}	
	if($tabela=='VW_CONSULTA_DECLARACAO_NET'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	if($tabela=='ESP_AUDITORIA_PAGAMENTOS_NET'){
		$retorno['INC'] = TRUE;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	
	if(($tabela=='VW_PS1100_CD_AL2' || $tabela=='PS1100') and $perfil == 'CORRETOR'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	
	if($tabela=='VW_DADOS_TELEFONE_AL2' || $tabela=='VW_DADOS_ENDERECO_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	
	if($tabela=='VW_VND1000_EXC_ON'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = false;
		$retorno['EXC'] = true;			
	}
	
	if($tabela=='VW_AGENDA_PLENA_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}
	if($tabela=='VW_CONTAS_IMPORTAR_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;		
	}
	
	if($tabela=='VW_GUIAS_DIGITADAS'){
		$retorno['INC'] = true;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}
	
	if($tabela=='VW_SOLICIT_AUTORIZADAS_AL2'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}
	
	if($tabela=='VW_PS1000_EXC_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;
		$retorno['EXC'] = true;			
		$retorno['CSV'] = false;
	}
	
	if($tabela=='VW_PS1000_EXC_AL2' && retornaValorConfiguracao('FORM_EXCLUSAO_EXCLUSIVO') == 'SIM'){
		$retorno['EXC'] = false;		
	}
	
	if($tabela=='VW_ARQ_NF_PREST_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = false;
		$retorno['EXC'] = true;			
		$retorno['CSV'] = false;
	}

	if($tabela=='VW_BENEF_EMPR_NET' && retornaValorConfiguracao('HABILITA_CSV_BENEF_EMPR') == 'SIM'){
		$retorno['CSV'] = true;
	}

	if($tabela=='VW_GUIAS_DIGITADAS' && retornaValorConfiguracao('HABILITA_EXC_GUIAS_DIGITADAS') == 'SIM'){
		$retorno['EXC'] = true;
	}

	if($tabela=='VW_PS1000_OPERADOR_AL2'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = true;
	}

	if($tabela=='VW_VND1000_CAAPSML'){
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = false;
		$retorno['EXC'] = false;	
	}	

	if($tabela=='VW_PS1069_AL2' and $perfil != 'OPERADOR'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = false;	
	}

	if($tabela=='VW_REEMBOLSO_ALIANCANET2' and $perfil != 'OPERADOR'){		
		$retorno['ALT'] = false;
	}
	
	if($tabela=='ESP_VIDEO_CONFERENCIA_CHAT' && ($_SESSION['perfilOperador'] == 'PRESTADOR' or $_SESSION['perfilOperador'] == 'BENEFICIARIO')){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = false;
	}
	
	if ((RetornaValorConfiguracao('UTILIZA_PERMI_PXAL2')=='SIM') || ($_SESSION['codigoSmart']=='77777'))
	{
		//pr('entrou aqui');
		if(($_SESSION['perfilOperador'] =='OPERADOR')or
		   ($_SESSION['perfilOperador'] =='VENDEDOR')or
		   ($_SESSION['perfilOperador'] =='CORRETOR')or
		   ($_SESSION['perfilOperador'] =='FORNECEDOR'))
		{

			$permissoesPx = permissaoPx($tabela,4,false,true);

			/*$retorno['VIS'] =  permissaoPx($tabela,1);
			$retorno['INC'] =  permissaoPx($tabela,2);
			$retorno['ALT'] =  permissaoPx($tabela,3);
			$retorno['EXC'] =  permissaoPx($tabela,4);
			$retorno['CSV'] =  permissaoPx($tabela,6);*/

			$retorno['VIS'] = false;
			$retorno['INC'] = false;
			$retorno['ALT'] = false;
			$retorno['EXC'] = false;			
			$retorno['CSV'] = false;

			if (strposDelphi('LEI',$permissoesPx) >= 0)
				$retorno['VIS'] = true;

			if (strposDelphi('INC',$permissoesPx) >= 0)
				$retorno['INC'] = true;

			if (strposDelphi('EDI',$permissoesPx) >= 0)
				$retorno['ALT'] = true;

			if (strposDelphi('EXC',$permissoesPx) >= 0)
				$retorno['EXC'] = true;

			if (strposDelphi('EXP',$permissoesPx) >= 0)
				$retorno['CSV'] = true;

		}
	}

	if($tabela=='VW_SOLIC_EXC_AL2' && $_SESSION['perfilOperador'] == 'EMPRESA'){
		$retorno['INC'] = false;
		$retorno['ALT'] = true;
		$retorno['VIS'] = false;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = false;
	}

	if((($tabela=='VW_RECURSO_CARENCIA_AL2') or ($tabela=='VW_RECURSO_GLOSA_AL2')) && $_SESSION['perfilOperador'] == 'PRESTADOR'){
		$retorno['INC'] = false;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = false;
	}elseif((($tabela=='VW_RECURSO_CARENCIA_AL2') or ($tabela=='VW_RECURSO_GLOSA_AL2'))  && $_SESSION['perfilOperador'] == 'OPERADOR'){
		$retorno['INC'] = true;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;
		$retorno['EXC'] = true;			
		$retorno['CSV'] = true;
	}

	if($tabela=='VW_PS6110_ALIANCANET2' && retornaValorConfiguracao('APENAS_VISUALIZA_PS6110') == 'SIM')
	{
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = false;
	}
	
	if((($tabela=='VW_PRORROGACOES_NEGADAS') or ($tabela=='VW_PRORROGACOES_APROVADAS')) && (($_SESSION['perfilOperador'] == 'OPERADOR') or ($_SESSION['perfilOperador'] == 'PRESTADOR'))){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
		$retorno['CSV'] = false;
	}

	if($tabela=='ESP_CAD_EMPRESAS_TMP')
	{
		$retorno['INC'] = true;
		$retorno['ALT'] = true;
		$retorno['VIS'] = true;

		if($_SESSION['perfilOperador'] =='OPERADOR'){
			$retorno['EXC'] = true;			
			$retorno['CSV'] = true;
		}else{
			$retorno['EXC'] = false;			
			$retorno['CSV'] = false;
		}
	}

	if($tabela=='VW_PS6110_ALIANCANET2' && retornaValorConfiguracao('DESABILITA_ALT_PS6110') == 'SIM')
	{
		$retorno['INC'] = true;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = true;			
		$retorno['CSV'] = true;
	}
	
	if($tabela=='ESP_SOLIC_SINISTRALIDADE'  and $perfil == 'EMPRESA'){
		$retorno['INC'] = false;
		$retorno['ALT'] = false;
		$retorno['VIS'] = true;
		$retorno['EXC'] = false;			
	}	
	
	return $retorno;
}



//INC ALT VIS EXC
function verificarPermissaoMinima($tabela,$tipoPermissao,$gerarInterupcao=true){
	$permissao = retornaPermissoesUsuarioTabela($_SESSION['codigoIdentificacao'],$_SESSION['perfilOperador'],$tabela);
	
	$permissaoPesquisa = $permissao[$tipoPermissao];
	
	if($gerarInterupcao and (!$permissaoPesquisa)){
		header("HTTP/1.0 401 Forbidden");
		echo '{"MSG":" Voce nao tem permissao para acessar esta pagina. [' . $tipoPermissao . ']"}';
		exit;
	}else{
		return $permissaoPesquisa;
	}
}


function testarTratamentoEspecial($tab, $tabOrig, $validacao) {
	
	if (($tab==$validacao) or 
	    ($tabOrig==$validacao))
	{
		return true;
	}
	else
	{
		return false;
	}

}  

function permissaoPx($_NomeTabela,$_NivelNecessario,$_EmitirMsg=false, $retornarStringPermissoes=false)
{
	//pr($_SESSION);
	//global $Con,$dadosCon,$jnCon;
	//$Con = $jnCon->conectarBancoDeDados($dadosCon[0],$dadosCon[1],$dadosCon[2],$dadosCon[3],$type_db,$dadosCon[5]);

	$query = 'SELECT COALESCE(TABELA_ORIGINAL,NOME_TABELA) NOME_TABELA FROM CFGTABELAS_SIS WHERE NOME_TABELA ='.aspas($_NomeTabela);
	$resultTabela = jn_query($query);
	
	if ($objTabela = jn_fetch_object($resultTabela))
	{
		$_NomeTabela = $objTabela->NOME_TABELA;
	}
	else
	{
		$_NomeTabela = $_NomeTabela;
	}
	
	$_NomeTabela = strtoupper($_NomeTabela);
	
	$Sql_Permissao = 'Select CFGENTIDADE_SIS_PERMISS.*, Ps1105.*, Ps1100.Codigo_Identificacao From Ps1100 ' .
                     'Left Outer Join Ps1105 On (Ps1100.Codigo_Identificacao = Ps1105.Codigo_Identificacao) ' .
                     'Left Outer Join CFGENTIDADE_SIS_PERMISS on (Ps1100.Codigo_Identificacao = CFGENTIDADE_SIS_PERMISS.codigo_identificacao) and ' .
                     ' (CFGENTIDADE_SIS_PERMISS.nome_entidade = ' . aspas($_NomeTabela) . ') ' .
                     'Where (Ps1100.Codigo_Identificacao = ' . aspas($_SESSION['codigoIdentificacao']) . ')';

	$result_Permissao = jn_query($Sql_Permissao);
	$obj_Permissao    = jn_fetch_object($result_Permissao);


   if ($_NivelNecessario == 1) 
      $_ValidacaoNivel = 'LEI';
   else if ($_NivelNecessario == 2) 
      $_ValidacaoNivel = 'INC';
   else if ($_NivelNecessario == 3) 
      $_ValidacaoNivel = 'EDI';
   else if ($_NivelNecessario == 4) 
      $_ValidacaoNivel = 'EXC';
   else if ($_NivelNecessario == 5) 
      $_ValidacaoNivel = 'IMP';
   else if ($_NivelNecessario == 6) 
      $_ValidacaoNivel = 'EXP';

   // Se tiver nivel de administrador, tem direito a tudo
   if ($obj_Permissao ->FLAG_NIVEL_ADMINISTRADOR == 'S')
   {
		 if ($retornarStringPermissoes)
		 {
		 	return 'LEI,INC,EDI,EXC,IMP,EXP';
		 }
		 else
		 {
           	return True;
         }
   }
   
   // Então é porque está chamando apenas para configurar algo
   if (($_NomeTabela == '') and ($_NivelNecessario >= 6)) 
   {
      if ($obj_Permissao->FLAG_PERMITE_CONFIG_SIS != 'S')
      {
      
         if ($_EmitirMsg) 
         {
			header("HTTP/1.0 401 Forbidden");
			echo jn_utf8_encode('{"MSG":"A opção solicitada necessita que o operador tenha permissão para alterar as configurações do sistema ou que o operador seja equivalente ao administrador.<br>Por favor, solicite ao administrador que esta permissão lhe seja concedida antes de executar esta rotina.  [Ref-03]"}');
			exit;
            
         }
         else
         {
			return false;
		 }
      }
      else
      {
         return True;
	  }	
   }
   
   
   if($_ValidacaoNivel=='LEI')
   {
	//$_ValidacaoNivel = 'AAAA';
	//pr(strpos($obj_Permissao->ACESSOS_AUTORIZADOS,$_ValidacaoNivel));
	//exit;
   }

   if (strpos($obj_Permissao->ACESSOS_AUTORIZADOS,$_ValidacaoNivel) === false )
   {
      // Verifica se a pessoa tem direito pelo perfil do usuário
      if ($obj_Permissao->CODIGO_IDENTIFICACAO_PERFIL != '')
      {
      
	         $Sql_Permissao =  'Select CFGENTIDADE_SIS_PERMISS.*, Ps1105.*, Ps1100.Codigo_Identificacao From Ps1100 ' .
	                           'Left Outer Join Ps1105 On (Ps1100.Codigo_Identificacao = Ps1105.Codigo_Identificacao) ' .
	                           'Left Outer Join CFGENTIDADE_SIS_PERMISS on (Ps1100.Codigo_Identificacao = CFGENTIDADE_SIS_PERMISS.codigo_identificacao) and ' .
	                           ' (CFGENTIDADE_SIS_PERMISS.nome_entidade = ' . Aspas($_NomeTabela) .') ' .
	                           'Where (Ps1100.Codigo_Identificacao = ' . aspas($obj_Permissao->CODIGO_IDENTIFICACAO_PERFIL) .') ';

			$result_Permissao = jn_query($Sql_Permissao);
			$obj_Permissao    = jn_fetch_object($result_Permissao);

	         if ((retornaValorCFG0003('SISTEMA_PERMISSAO_PERMISSIVO') == 'SIM') and
	            ($obj_Permissao->NOME_ENTIDADE == '') and
	            ($_NomeTabela <> '') and
	            ($_NivelNecessario <= 4))
	         {
				 if ($retornarStringPermissoes)
				 {
				 	return 'LEI,INC,EDI,EXC,IMP,EXP';
				 }
				 else
				 {
	             	return True;
	             }
			 }

 	         if (strpos($obj_Permissao->ACESSOS_AUTORIZADOS,$_ValidacaoNivel) === false ) 
	         {
	             return false;
	         } 
	         else 
	         {
				 if ($retornarStringPermissoes)
				 {
				 	return $obj_Permissao->ACESSOS_AUTORIZADOS;
				 }
				 else
				 {
	             	return True;
	             }
			 }
      }

      if ($_EmitirMsg) 
      {
			header("HTTP/1.0 401 Forbidden");
			echo jn_utf8_encode('{"MSG":"A opção solicitada necessita que o operador tenha o nível de pemissão para a tabela : ' . $_NomeTabela . ' configurada com no mínimo o nível : ' . $_NivelNecessario .'"}');
		    exit;
	  }
   }
   else
   {
		 if ($retornarStringPermissoes)
		 {
		 	return $obj_Permissao->ACESSOS_AUTORIZADOS;
		 }
		 else
		 {
	       	return True;
	     }

   }
	
}


?>