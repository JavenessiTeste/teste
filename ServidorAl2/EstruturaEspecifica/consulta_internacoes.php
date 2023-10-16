<?php
require('../lib/base.php');

if($dadosInput['tipo']== 'dados'){	
	$codAssociado = $dadosInput['codAssociado'];
	$codigoPrest = $dadosInput['codPrestador'];	
	$dtinicio = '';
	$dtfim = '';
	$dtAlta = '';
	
	$queryPrest = 'Select Codigo_Prestador, Nome_Prestador From Ps5000 Where Codigo_Prestador = ' . aspas($codigoPrest);
	$resPrest = jn_query($queryPrest);
	$rowPrest = jn_fetch_object($resPrest);
	$codigoPrestador = $rowPrest->CODIGO_PRESTADOR;
	$nomePrestador	 = $rowPrest->NOME_PRESTADOR;

	$query  =	'Select ' .             	   		 
				'PS6500.Codigo_Associado, Ps1000.Nome_Associado, Ps1000.Data_Nascimento, Ps1000.Sexo, PS1010.Nome_Empresa,  ' .
				'Ps1000.Codigo_Tipo_Caracteristica, Ps1046.Nome_Tipo_Caracteristica, ' .
				'PS6500.Descricao_Diagnostico, Ps5100.Nome_Especialidade, PS6500.Quantidade_Dias_Enfermagem, ' .
				'PS6500.Descricao_Observacao, PS6500.Observacao_Auditoria, PS6500.Observacao_Solicitacao, ' .
				'PS6500.Quantidade_Dias_Uti, PS6500.Quantidade_Dias_Internacao, Ps6500.Data_Alta, Ps6500.Numero_Autorizacao, ' .
				'COALESCE(DATA_INTERNACAO,DATA_PREVISAO_INTERNACAO) as DATA_INTERNACAO, ' .
				'Case '.
					'When Tipo_Acomodacao = "01" Then "APARTAMENTO INDIVIDUAL" '.
					'When Tipo_Acomodacao = "02" Then "APARTAMENTO COLETIVO" '.
					'When Tipo_Acomodacao = "03" Then "ENFERMARIA" ' .
				'End as Tipo_Acomodacao, '.
				'PS6500.Codigo_Associado, Ps1000.Nome_Associado, Ps1000.Data_Nascimento, PS1010.Nome_Empresa,  ' .
				'PS6500.Data_Entrada_Enfermagem, PS6500.Data_Saida_Enfermagem, DATEDIFF(day, DATA_ENTRADA_ENFERMAGEM, GETDATE()) AS QUANTIDADE_PROVISORIA_ENF,  ' .
				'PS6500.Data_Entrada_Uti, PS6500.Data_Saida_Uti, DATEDIFF(day, DATA_ENTRADA_UTI, GETDATE()) AS QUANTIDADE_PROVISORIA_UTI ' .			
				'From PS6500 ' .
				'Inner Join Ps5000 On (Ps5000.Codigo_Prestador = PS6500.Codigo_Prestador) ' .			  
				'Inner Join Ps1000 On (Ps1000.Codigo_Associado = PS6500.Codigo_Associado) ' .			  
				'Inner Join Ps1010 On (Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa) ' .			  
				'Inner Join Ps1046 On (Ps1000.Codigo_Tipo_Caracteristica = Ps1046.Codigo_Tipo_Caracteristica) ' .			  
				'Left Outer Join Ps5100 On (PS6500.Codigo_Especialidade = PS5100.Codigo_Especialidade) ' .			  
				'Where 1 = 1 ' .
				"And Ps6500.FLAG_ALIANCA_NET = 'S' " .
				"And TIPO_GUIA = 'I' " .
				"And TIPO_PRESTADOR = '04' " .
				'And (DATEDIFF(month,GETDATE(),COALESCE(DATA_INTERNACAO,DATA_PREVISAO_INTERNACAO)) <= 12) ' .
				'And PS6500.Codigo_Prestador = ' . aspas($codigoPrest) . ' ';

	if ($codAssociado > 0)
		$query  .= ' And (PS6500.Codigo_Associado = '  . aspas($codAssociado) . ') ';	

	if ($dtinicio != "" and $dtinicio != " "){
			$query  .= ' And (PS6500.DATA_INTERNACAO >= ' . DataToSql($dtinicio) . ') ';
	}
		
	if ($dtfim != "" and $dtfim != " ")
		$query  .= ' And (PS6500.DATA_INTERNACAO <= ' . DataToSql($dtfim) . ') ';		

	if ($dtAlta == 'S'){
		$query  .= ' And (PS6500.DATA_ALTA IS NOT NULL) ';		
	}else{
		$query  .= ' And (PS6500.DATA_ALTA IS NULL) ';			
	}

	$query  .= " Order By PS6500.DATA_INTERNACAO ";		
	$resInter = jn_query($query);
	$rowInter = jn_fetch_object($resInter);
	
	$idade = calcularIdade($rowInter->DATA_NASCIMENTO);
	$classificacao = '';
	if($idade <= 12){
		$classificacao = 'INFANTIL';
	}else{
		$classificacao = 'ADULTO';										
	}	
	
	$dadosInter['CODIGO_PRESTADOR'] 			= jn_utf8_encode($codigoPrestador);
	$dadosInter['NOME_PRESTADOR'] 				= jn_utf8_encode($nomePrestador);
	
	$dadosInter['CODIGO_ASSOCIADO'] 			= jn_utf8_encode($rowInter->CODIGO_ASSOCIADO);
	$dadosInter['NOME_ASSOCIADO'] 				= jn_utf8_encode($rowInter->NOME_ASSOCIADO);
	$dadosInter['NOME_EMPRESA'] 				= jn_utf8_encode($rowInter->NOME_EMPRESA);
	$dadosInter['DATA_NASCIMENTO'] 				= SqlToData($rowInter->DATA_NASCIMENTO);
	$dadosInter['IDADE'] 						= jn_utf8_encode(calcularIdade($rowInter->DATA_NASCIMENTO));
	$dadosInter['CLASSIFICACAO'] 				= jn_utf8_encode($classificacao);
	$dadosInter['TIPO_CARACTERISTICA'] 			= jn_utf8_encode($rowInter->CODIGO_TIPO_CARACTERISTICA);
	$dadosInter['NOME_TIPO_CARACTERISTICA'] 	= jn_utf8_encode($rowInter->NOME_TIPO_CARACTERISTICA);
	$dadosInter['DIAGNOSTICO'] 					= jn_utf8_encode($rowInter->DESCRICAO_DIAGNOSTICO);
	$dadosInter['ESPECIALIDADE'] 				= jn_utf8_encode($rowInter->NOME_ESPECIALIDADE);
	$dadosInter['TIPO_ACOMODACAO'] 				= jn_utf8_encode($rowInter->TIPO_ACOMODACAO);
	$dadosInter['DATA_INTERNACAO'] 				= SqlToData($rowInter->DATA_INTERNACAO);
	$dadosInter['DATA_ALTA'] 					= SqlToData($rowInter->DATA_ALTA);
	$dadosInter['DATA_ENTRADA_ENF'] 			= SqlToData($rowInter->DATA_ENTRADA_ENFERMAGEM);
	$dadosInter['DATA_SAIDA_ENF'] 				= SqlToData($rowInter->DATA_SAIDA_ENFERMAGEM);
	$dadosInter['QNT_DIARIAS_PROV'] 			= jn_utf8_encode($rowInter->QUANTIDADE_PROVISORIA_ENF);
	$dadosInter['QTE_DIARIAS_ENF'] 				= jn_utf8_encode($rowInter->QUANTIDADE_DIAS_ENFERMAGEM);
	$dadosInter['DATA_ENTRADA_UTI'] 			= SqlToData($rowInter->DATA_ENTRADA_UTI);
	$dadosInter['DATA_SAIDA_UTI'] 				= SqlToData($rowInter->DATA_SAIDA_UTI);
	$dadosInter['QTE_DIARIAS_UTI'] 				= jn_utf8_encode($rowInter->QUANTIDADE_DIAS_UTI);
	$dadosInter['QTE_GERAL_DIARIAS'] 			= jn_utf8_encode($rowInter->QUANTIDADE_DIARIAS);
	$dadosInter['QTE_DIAS_INTERNACAO'] 			= jn_utf8_encode($rowInter->QUANTIDADE_DIAS_INTERNACAO);
	$dadosInter['DESCRICAO_OBSERVACAO'] 		= jn_utf8_encode($rowInter->DESCRICAO_OBSERVACAO);
	$dadosInter['OBSERVACAO_SOLICITACAO'] 		= jn_utf8_encode($rowInter->OBSERVACAO_SOLICITACAO);
	$dadosInter['OBSERVACAO_AUDITORIA'] 		= jn_utf8_encode($rowInter->OBSERVACAO_AUDITORIA);
	
		
	echo json_encode($dadosInter);
}

function calcularIdade($date){
	if(!$date){
		return null;
	}
	
    // separando yyyy, mm, ddd
    list($ano, $mes, $dia) = explode('-', $date);

    // data atual
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

    // cálculo
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
}
?>