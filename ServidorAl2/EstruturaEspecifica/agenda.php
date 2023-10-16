<?php
require('../lib/base.php');

require('../private/autentica.php');

if($dadosInput['tipo'] =='dados'){
	if($dadosInput['data']==''){
		$dadosInput['data'] = date("d/m/Y");
	}else{
		$dadosInput['data'] = (sqlToData($dadosInput['data']));
	}
	
	if ($dadosInput['data'] == date("d/m/Y")){
		$filtro = " AND Ps6010.hora_marcacao >".aspas(date('H:m'));
	}
	if($dadosInput['prestador']!=''){
		$filtro = $filtro. " AND Ps6010.COdigo_PRestador =". aspas($dadosInput['prestador']);
	}
	if($dadosInput['especialidade']!=''){
		$filtro = $filtro. " AND ps5003.COdigo_Especialidade =". aspas($dadosInput['especialidade']);
	}
	
	$query  = " select distinct Ps6010.numero_registro,Ps6010.data_marcacao, Ps6010.hora_marcacao, Ps5000.nome_prestador,Ps1000.codigo_associado,Ps1000.Nome_Associado, ";
	
	if($_SESSION['type_db'] == 'sqlsrv'){
		$query .= "	CASE DATEPART(WEEKDAY , Ps6010.data_marcacao) ";		
	}else{		
		$query .= "	CASE EXTRACT(WEEKDAY FROM Ps6010.data_marcacao) ";
	}	
	
	$query .= "			 WHEN 0 THEN 'DOMINGO' 
						 WHEN 1 THEN 'SEGUNDA-FEIRA'
						 WHEN 2 THEN 'TERÇA-FEIRA'
						 WHEN 3 THEN 'QUARTA-FEIRA'
						 WHEN 4 THEN 'QUINTA-FEIRA'
						 WHEN 5 THEN 'SEXTA-FEIRA'
						 WHEN 6 THEN 'SÁBADO'
					END DIASEMANA from ps6010
				inner join ps5000 on ps5000.codigo_prestador = ps6010.codigo_prestador
				left join ps5003 on ps5003.codigo_prestador = ps6010.codigo_prestador
				left join ps1000 on ps1000.codigo_associado = Ps6010.codigo_associado
				where tipo_situacao = " . aspas('LIVRE') . " and Ps6010.Codigo_Associado is null  and ps6010.data_marcacao = " . dataToSql($dadosInput['data']) . " " . $filtro;


	$query .= 'ORDER BY Ps6010.data_marcacao,PS6010.Hora_Marcacao';

	$res = jn_query($query);
	$retorno['GRID'] = array();
	
	while($row = jn_fetch_object($res)){
		$linha['NUMERO_REGISTRO'] = $row->NUMERO_REGISTRO;
		$linha['DATA_MARCACAO'] = sqlToData($row->DATA_MARCACAO);
		$linha['DIA_SEMANA'] = ($row->DIASEMANA);
		$linha['HORA_MARCACAO'] = $row->HORA_MARCACAO;
		$linha['NOME_PRESTADOR'] = jn_utf8_encode($row->NOME_PRESTADOR);
		$linha['NOME_ASSOCIADO'] = jn_utf8_encode($row->NOME_ASSOCIADO);
		$linha['ACAO'] = '';
		$retorno['GRID'][] = $linha; 
		
	}
	
	$linha = null;
	
	$queryPrest = "select distinct Ps5000.codigo_prestador, Ps5000.nome_prestador from ps6010
				inner join ps5000 on ps5000.codigo_prestador = ps6010.codigo_prestador
				where tipo_situacao = " . aspas('LIVRE') . " and  Ps6010.Codigo_Associado is null and ps6010.data_marcacao >= current_timestamp ";

	$resPrest = jn_query($queryPrest);
	$retorno['PRESTADORES'] = array();
	$linha['VALOR'] = '';
	$linha['LABEL'] = 'TODOS';
	$retorno['PRESTADORES'][] = $linha;
	while($rowPrest = jn_fetch_object($resPrest)) {
		$linha['VALOR'] = $rowPrest->CODIGO_PRESTADOR;
		$linha['LABEL'] = jn_utf8_encode($rowPrest->NOME_PRESTADOR);
		$retorno['PRESTADORES'][] = $linha;
	}
	
	$linha = null;
	
	$queryEsp =   "select distinct Ps5100.codigo_especialidade,Ps5100.nome_especialidade from ps5003
												   inner join ps5100 on ps5100.codigo_especialidade = ps5003.codigo_especialidade
												   inner join ps6010 on ps6010.codigo_prestador = ps5003.codigo_prestador
												   where tipo_situacao = " . aspas('LIVRE') . " and Ps6010.Codigo_Associado is null and ps6010.data_marcacao >= current_timestamp ";

	$resEsp = jn_query($queryEsp);
	$retorno['ESPECIALIDADES'] = array();
	$linha['VALOR'] = '';
	$linha['LABEL'] = 'TODAS';	
	$retorno['ESPECIALIDADES'][] = $linha;
	while($rowEsp = jn_fetch_object($resEsp)) {
		$linha['VALOR'] = $rowEsp->CODIGO_ESPECIALIDADE;
		$linha['LABEL'] = jn_utf8_encode($rowEsp->NOME_ESPECIALIDADE);
		$retorno['ESPECIALIDADES'][] = $linha;
	}
	
	$retorno['DATA'] = ($dadosInput['data']);
	
	echo json_encode($retorno);

}else if($dadosInput['tipo']=='agendar'){
	$query = 'SELECT COUNT(*) QTE FROM PS6010 WHERE tipo_situacao = ' . aspas('LIVRE') . ' and numero_registro ='. aspas($dadosInput['reg']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);
	
	if($row->QTE > 0){
	
		$query = 'SELECT * FROM PS1000 WHERE CODIGO_ASSOCIADO ='. aspas($dadosInput['ben']);
		$res = jn_query($query);

		$row = jn_fetch_object($res);
		$query = 'UPDATE PS6010 SET codigo_associado ='.aspas($dadosInput['ben']).' , NOME_PESSOA ='.aspas($row->NOME_ASSOCIADO). ', TIPO_MARCACAO ='. aspas('O'). ', TIPO_SITUACAO ='.aspas('AGENDADO'). '
				 WHERE numero_registro ='.aspas($dadosInput['reg']);	
		$res = jn_query($query);	
		
		$retorno['MSG']= ('Horário agendado.');


	}else{
		$retorno['MSG']= ('Não foi possível agendar esse horário.');
	}
	
	echo json_encode($retorno);
}







?>