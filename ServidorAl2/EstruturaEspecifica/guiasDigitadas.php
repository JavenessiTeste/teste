<?php
require('../lib/base.php');

require('../private/autentica.php');


if($dadosInput['tipo'] =='carregaDados'){

	$queryPrincipal  = "Select NUMERO_REGISTRO,COALESCE(NUMERO_GUIA_OPERADORA, NUMERO_REGISTRO) AS NUMERO_GUIA_OPERADORA, TIPO_GUIA, CODIGO_ASSOCIADO, NOME_PESSOA, DATA_CADASTRAMENTO, DATA_PROCEDIMENTO 
						from ps5750 
						where PS5750.NUMERO_GUIA_GERADA IS NULL and ps5750.IMPORTAR is null and ps5750.codigo_prestador = " . aspas($_SESSION['codigoIdentificacao']);								

	$resultQuery    = jn_query($queryPrincipal);		
	
	$dadosGrid = array();
	
	$item['NOME_CAMPO'] = "CHECK";
	$item['LABEL'] = 'Selecionar';
	$item['TIPO_CAMPO'] = "CHECK";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NUMERO_REGISTRO";
	$item['LABEL'] = 'NUMERO REGISTRO';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'N';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NUMERO_GUIA_OPERADORA";
	$item['LABEL'] = 'Numero Guia';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "TIPO_GUIA";
	$item['LABEL'] = 'Tipo Guia';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "CODIGO_ASSOCIADO";
	$item['LABEL'] = 'Código Associado';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "NOME_PESSOA";
	$item['LABEL'] = 'Nome Pessoa';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "DATA_CADASTRAMENTO";
	$item['LABEL'] = 'Data Cadastro';
	$item['TIPO_CAMPO'] = "DATE";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "Data Procedimento";
	$item['LABEL'] = 'Data Procedimento';
	$item['TIPO_CAMPO'] = "DATE";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "VALOR_COBRADO";
	$item['LABEL'] = 'Valor Cobrado';
	$item['TIPO_CAMPO'] = "";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "ALTERAR";
	$item['LABEL'] = 'Alterar';
	$item['TIPO_CAMPO'] = "BUTTON";
	$item['GRID'] = 'S';
	$infoGrid[] = $item;
	
	$item['NOME_CAMPO'] = "EXCLUIR";
	$item['LABEL'] = 'Excluir';
	$item['TIPO_CAMPO'] = "BUTTON";
	$item['GRID'] = 'S';	
	$infoGrid[] = $item;
	
	
	
	while ($rowPrincipal = jn_fetch_object($resultQuery)){
		$dadoLinha = array();
		$queryValor  = "Select Sum(coalesce(ps5760.valor_cobrado,0)) as VALOR_COBRADO from ps5760 where ps5760.numero_registro_ps5750 =". aspas($rowPrincipal->NUMERO_REGISTRO);
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
		$valor = $rowValor->VALOR_COBRADO;
		
		foreach ($rowPrincipal as $key => $value){
			$dadoLinha[$key] = utf8_decode($value);
		}
		$dadoLinha['VALOR_COBRADO'] = toMoeda($valor);
		$dadoLinha['ALTERAR'] = '';
		$dadoLinha['EXCLUIR'] = '';
		$dadoLinha['CHECK'] = 'N';
		$dadosGrid[] = $dadoLinha;
		$valorTotal = $valorTotal + $valor;
	}
	$retorno['DADOS_GRID']  = $dadosGrid;
	$retorno['VALOR_TOTAL'] = toMoeda($valorTotal);
	$retorno['INFO_GRID']  = $infoGrid;
	
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='enviar'){
	$guias ='';
	if(ValidaData('01/'.$dadosInput['competencia'])){
		foreach ($dadosInput['guias'] as $valor){
			if ($guias == '')
				$guias = $valor;
			else
				$guias = $guias.','.$valor;
		}
		$queryProtocolo = 'SELECT MAX(NUMERO_PROTOCOLO) as NUMERO_PROTOCOLO FROM PS5750';
		$resProtocolo = jn_query($queryProtocolo);
		$rowProtocolo = jn_fetch_object($resProtocolo);

		$somar = $rowProtocolo->NUMERO_PROTOCOLO + 1;
		$query = "UPDATE PS5750 SET DATA_ENVIO = current_timestamp, IMPORTAR='S', NUMERO_PROTOCOLO = " . aspas($somar) . ", MES_ANO = ". aspas($dadosInput['competencia']);
		$query .= " WHERE IMPORTAR is null and ps5750.codigo_prestador = " . $_SESSION['codigoIdentificacao'] . " AND Ps5750.Numero_Registro in ($guias)";
		//pr($query,true);
		jn_query($query);

		if (retornaValorConfiguracao('IMPORTAR_GUIAS_AUTOMATICO') == 'SIM'){
			$registroProcesso = jn_gerasequencial('CFGDISPAROPROCESSOSCABECALHO');
			$hora = date('H:i');

			$queryCabecalho = " INSERT INTO CFGDISPAROPROCESSOSCABECALHO (NUMERO_REGISTRO_PROCESSO, NOME_PROCESSO, IDENTIFICACAO_PROCESSO, DATA_DISPARO, HORA_DISPARO, OPERADOR_DISPARO, FLAG_ENVIAR_MSG_CONCLUSAO) ";
			$queryCabecalho .= " VALUES (" . aspas($registroProcesso) . ", '501', '501', current_timestamp, " . aspas($hora) . ", " . aspas($_SESSION['codigoIdentificacao']) . ", 'S' );";
			
			if(jn_query($queryCabecalho)){						

				jn_query('SET IDENTITY_INSERT CFGDISPAROPROCESSOSCABECALHO ON');

				$queryPrest = " INSERT INTO CFGDISPAROPROCESSOSPARAMETROS (NUMERO_REGISTRO_PROCESSO, NOME_PARAMETRO, TIPO_PARAMETRO, VALOR_PARAMETRO) VALUES ";
				$queryPrest .= " ("  . aspas($registroProcesso) . ", 'CODIGO_PRESTADOR', 'F', " . aspas($_SESSION['codigoIdentificacao']) . "); ";
				jn_query($queryPrest);

				jn_query('SET IDENTITY_INSERT CFGDISPAROPROCESSOSCABECALHO OFF');
				
				$queryMes = " INSERT INTO CFGDISPAROPROCESSOSPARAMETROS (NUMERO_REGISTRO_PROCESSO, NOME_PARAMETRO, TIPO_PARAMETRO, VALOR_PARAMETRO) VALUES ";
				$queryMes .= " (" . aspas($registroProcesso) . ", 'MES_ANO_COMPETENCIA', 'F', " . aspas($dadosInput['competencia']) . "); ";
				jn_query($queryMes);

				$queryProtocolo = " INSERT INTO CFGDISPAROPROCESSOSPARAMETROS (NUMERO_REGISTRO_PROCESSO, NOME_PARAMETRO, TIPO_PARAMETRO, VALOR_PARAMETRO) VALUES ";
				$queryProtocolo .= " (" . aspas($registroProcesso) . ", 'NUMERO_PROTOCOLO', 'F', " . aspas($somar) . "); ";
				jn_query($queryProtocolo);

				$queryProces = 'SELECT NUMERO_PROCESSAMENTO FROM CFG0001';
				$resProces= jn_query($queryProces);
				$rowProces = jn_fetch_object($resProces);

				$queryProcessamento = " INSERT INTO CFGDISPAROPROCESSOSPARAMETROS (NUMERO_REGISTRO_PROCESSO, NOME_PARAMETRO, TIPO_PARAMETRO, VALOR_PARAMETRO) VALUES ";
				$queryProcessamento .= " (" . aspas($registroProcesso) . ", 'NUMERO_PROCESSAMENTO', 'F', " . aspas($rowProces->NUMERO_PROCESSAMENTO) . "); ";
				jn_query($queryProcessamento);

			}
		}

		$retorno['STATUS'] = 'OK';
		$retorno['PROTOCOLO'] = $somar;
		
		$retorno['MSG'] = 'Protocolo Gerado: '.$somar;
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'Informe uma competência válida.';
	}
	
	echo json_encode($retorno);
}






?>