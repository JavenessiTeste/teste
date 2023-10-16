<?php
require('../lib/base.php');

if($dadosInput['tipo']== 'dados'){
	$codigoPrest = '';
	$Autorizacao = '';
	
	$numeroProtocolo = $dadosInput['numeroProtocolo'];
	
	if($_SESSION['perfilOperador'] == 'PRESTADOR'){
		$codigoPrest = $_SESSION['codigoIdentificacao'];
	}else{
		$codigoPrest = $dadosInput['codPrestador'];
	}
	
	$queryPrest = 'SELECT CODIGO_PRESTADOR, NOME_PRESTADOR FROM PS5000 WHERE CODIGO_PRESTADOR = ' . aspas($codigoPrest);
	$resPrest = jn_query($queryPrest);
	$rowPrest = jn_fetch_object($resPrest);
	
	$nomePrestador = $rowPrest->NOME_PRESTADOR;
	
	$query  = "Select Distinct NUMERO_PROTOCOLO,DATA_ENVIO,MES_ANO from ps5750 where ps5750.IMPORTAR is not null  and ps5750.codigo_prestador = " . $codigoPrest . " and ps5750.NUMERO_PROTOCOLO = ". aspas($numeroProtocolo);

	$j = 0;
	$res = jn_query($query);
	while($row = jn_fetch_object($res)) {

		$Autorizacao[$j]['NUMERO_PROTOCOLO']           	= $row->NUMERO_PROTOCOLO;
		$Autorizacao[$j]['DATA_ENVIO']                 	= SqlToData($row->DATA_ENVIO);
		$Autorizacao[$j]['COMPETENCIA']                 = $row->MES_ANO;
		
		
		if ($_SESSION['tipo_operadora']  == 'saude'){		
		
		$queryValor  = "select nome_empresa,
							(Select count(*) AS QTE from ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.NUMERO_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . " AND PS5750.TIPO_GUIA = 'C') QTE,
							(Select coalesce(Sum(coalesce(PS5760.valor_cobrado,0)),0) AS VALOR from ps5750
								   left join ps5760 on Ps5750.numero_registro = ps5760.numero_registro_ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.numero_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . "  AND PS5750.TIPO_GUIA = 'C') VALOR
							from cfgempresa";
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);

		
		
		$Autorizacao[$j]['QTE_CONSULTAS']          	= $rowValor->QTE;
		$Autorizacao[$j]['VALOR_CONSULTAS']  		= toMoeda($rowValor->VALOR,false);

		$queryValor  = "select nome_empresa,
							(Select count(*) AS QTE from ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.NUMERO_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . " AND PS5750.TIPO_GUIA = 'S') QTE,
							(Select coalesce(Sum(coalesce(PS5760.valor_cobrado,0)),0) AS VALOR from ps5750
								   left join ps5760 on Ps5750.numero_registro = ps5760.numero_registro_ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.numero_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . "  AND PS5750.TIPO_GUIA = 'S') VALOR
							from cfgempresa";
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
			
		
		$Autorizacao[$j]['QTE_SADTS']          	    = $rowValor->QTE;
		$Autorizacao[$j]['VALOR_SADTS']  			    = toMoeda($rowValor->VALOR,false);
		
		$queryValor  = "select nome_empresa,
							(Select count(*) AS QTE from ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.NUMERO_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . " AND PS5750.TIPO_GUIA = 'I') QTE,
							(Select coalesce(Sum(coalesce(PS5760.valor_cobrado,0)),0) AS VALOR from ps5750
								   left join ps5760 on Ps5750.numero_registro = ps5760.numero_registro_ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.numero_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . "  AND PS5750.TIPO_GUIA = 'I') VALOR
							from cfgempresa";
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
		
		
		$Autorizacao[$j]['QTE_INTERNACOES']          	= $rowValor->QTE;
		$Autorizacao[$j]['VALOR_INTERNACOES']  			= toMoeda($rowValor->VALOR,false);
		
		}else{
		
		$queryValor  = "select nome_empresa,
							(Select count(*) AS QTE from ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.NUMERO_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . " AND PS5750.TIPO_GUIA = 'O') QTE,
							(Select coalesce(Sum(coalesce(PS5760.valor_cobrado,0)),0) AS VALOR from ps5750
								   left join ps5760 on Ps5750.numero_registro = ps5760.numero_registro_ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.numero_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . "  AND PS5750.TIPO_GUIA = 'O') VALOR
							from cfgempresa";
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);

		
		
		
		$Autorizacao[$j]['QTE_ODONTO']           	    = $rowValor->QTE;
		$Autorizacao[$j]['VALOR_ODONTO']  			    = toMoeda($rowValor->VALOR,false);
		
		}
		
		$queryValor  = "select nome_empresa,
							(Select count(*) AS QTE from ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.NUMERO_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . " ) QTE,
							(Select coalesce(Sum(coalesce(PS5760.valor_cobrado,0)),0) AS VALOR from ps5750
								   left join ps5760 on Ps5750.numero_registro = ps5760.numero_registro_ps5750
													where ps5750.IMPORTAR = 'S'  and ps5750.codigo_prestador = " . $codigoPrest . "
													and ps5750.numero_PROTOCOLO = " . aspas($row->NUMERO_PROTOCOLO) . " ) VALOR
							from cfgempresa";
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
				
		
		$Autorizacao[$j]['QTE_TOTAL']          	    = $rowValor->QTE;
		$Autorizacao[$j]['VALOR_TOTAL']  			    = toMoeda($rowValor->VALOR,false);		
		$j++;
	}
	
	$dadosProtocolo['NUMERO_PROTOCOLO'] 	= $Autorizacao[0]['NUMERO_PROTOCOLO'];
	$dadosProtocolo['DATA_ENVIO'] 			= $Autorizacao[0]['DATA_ENVIO'];
	$dadosProtocolo['COMPETENCIA'] 			= $Autorizacao[0]['COMPETENCIA'];
	$dadosProtocolo['CODIGO_PRESTADOR'] 	= $codigoPrest;
	$dadosProtocolo['NOME_PRESTADOR'] 		= jn_utf8_encode($nomePrestador);
	$dadosProtocolo['QTE_CONSULTA'] 		= $Autorizacao[0]['QTE_TOTAL'];
	$dadosProtocolo['VALOR_CONSULTAS'] 		= $Autorizacao[0]['VALOR_CONSULTAS'];
	$dadosProtocolo['QTE_SADTS'] 			= $Autorizacao[0]['QTE_SADTS'];
	$dadosProtocolo['VALOR_SADTS'] 			= $Autorizacao[0]['VALOR_SADTS'];
	$dadosProtocolo['QTE_INTERNACOES'] 		= $Autorizacao[0]['QTE_INTERNACOES'];
	$dadosProtocolo['VALOR_INTERNACOES'] 	= $Autorizacao[0]['VALOR_INTERNACOES'];
	$dadosProtocolo['QTE_ODONTO'] 			= $Autorizacao[0]['QTE_ODONTO'];
	$dadosProtocolo['VALOR_ODONTO'] 		= $Autorizacao[0]['VALOR_ODONTO'];
	$dadosProtocolo['QTE_TOTAL'] 			= $Autorizacao[0]['QTE_TOTAL'];
	$dadosProtocolo['VALOR_TOTAL'] 			= $Autorizacao[0]['VALOR_TOTAL'];
	
	echo json_encode($dadosProtocolo);
}

if($dadosInput['tipo'] == 'guiasProtocolo'){
	$codigoPrestador = $dadosInput['codPrestador'];
	$numeroProtocolo = $dadosInput['numeroProtocolo'];
	
	$queryGuias  = ' SELECT NUMERO_REGISTRO,NUMERO_GUIA_OPERADORA, TIPO_GUIA, CODIGO_ASSOCIADO, NOME_PESSOA, DATA_CADASTRAMENTO  ';						
	$queryGuias .= ' FROM PS5750 WHERE PS5750.NUMERO_PROTOCOLO = '. aspas($numeroProtocolo) . '  AND PS5750.CODIGO_PRESTADOR = ' . aspas($codigoPrestador);		
	$resGuias = jn_query($queryGuias);	
	$i = 0;
	while($rowGuias = jn_fetch_object($resGuias)) {
		$queryValor  = "SELECT SUM(COALESCE(PS5760.VALOR_COBRADO,0)) AS VALOR_COBRADO FROM PS5760 WHERE PS5760.NUMERO_REGISTRO_PS5750 =". aspas($rowGuias->NUMERO_REGISTRO);
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
		
		$valor = $rowValor->VALOR_COBRADO;
		
		if (trim($valor) == '')
			$valor = 0;
			
		$Guias[$i]['NUMERO_REGISTRO']		= $rowGuias->NUMERO_REGISTRO;
		$Guias[$i]['TIPO_GUIA']				= $rowGuias->TIPO_GUIA;
		$Guias[$i]['CODIGO_ASSOCIADO']		= $rowGuias->CODIGO_ASSOCIADO;
		$Guias[$i]['NOME_PESSOA']			= $rowGuias->NOME_PESSOA;
		$Guias[$i]['DATA_CADASTRAMENTO']	= SqlToData($rowGuias->DATA_CADASTRAMENTO);	
		$Guias[$i]['VALOR_COBRADO']			= toMoeda($valor,false);	
		$Guias[$i]['NUMERO_GUIA']			= $rowGuias->NUMERO_GUIA_OPERADORA;
		$i++;
	}

	$retorno['DADOS_GUIAS'] = $Guias;

	echo json_encode($retorno);
	
}
?>