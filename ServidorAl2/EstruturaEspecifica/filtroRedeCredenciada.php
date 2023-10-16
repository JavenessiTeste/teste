<?php

require('../lib/base.php');

require('../private/autentica.php');

$codigoSmart = $_SESSION['codigoSmart'];

if(!$codigoSmart){
	$queryEmp = 'SELECT CODIGO_SMART FROM CFGEMPRESA ';
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);	
	$codigoSmart = $rowEmp->CODIGO_SMART;
}

if($dadosInput['tipo'] =='dadosInicias'){
	
	
	$retorno = null;
	
	
	$retorno['PLANOS']['MOSTRAR']  = true;
	$retorno['PLANOS']['LABEL']    = 'Plano do Beneficiario';
	
	if(retornaValorConfiguracao('OCULTAR_PLANO_REDECREDENCIADA') == 'SIM'){
		$retorno['PLANOS']['MOSTRAR']  = false;		
	}
	
	$retorno['TIPOS']['MOSTRAR']   = true;
	$retorno['TIPOS']['LABEL']     = 'Tipo do Prestador';
	
	$retorno['BAIRROS']['MOSTRAR'] = true;
	$retorno['BAIRROS']['LABEL']   = 'Bairro do Prestador';
	
	$retorno['CIDADES']['MOSTRAR'] = true;
	$retorno['CIDADES']['LABEL']   = 'Cidade do Prestador';
	
	$retorno['ESTADOS']['MOSTRAR'] = true;
	$retorno['ESTADOS']['LABEL']   = 'Estado do Prestador';
	
	if(retornaValorConfiguracao('OCULTAR_ESTADO_REDECREDENCIADA') == 'SIM'){
		$retorno['ESTADO']['MOSTRAR'] = false;
	}
	
	$retorno['ESPECIALIDADES']['MOSTRAR'] = true;
	$retorno['ESPECIALIDADES']['LABEL'] = 'Especialidade do Prestador';

	$retorno['NOME']['MOSTRAR'] = true;
	$retorno['NOME']['LABEL'] = 'Nome do Prestador';

	$filtro = ' Where 1= 1 ';
	
	if($_SESSION['perfilOperador']==='BENEFICIARIO'){
		$query  = "SELECT coalesce(cast(CODIGO_REDE_INDICADA as varchar(10)), 'NULL') CODIGO_REDE_INDICADA  from PS1000
				   inner join PS1030 on PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO
				   WHERE (CODIGO_ASSOCIADO = ". aspas($_SESSION['codigoIdentificacao']).")";    
		
		$res = jn_query($query);

		if($row = jn_fetch_object($res)){
			$redeIndicada = $row->CODIGO_REDE_INDICADA;
		}
		
		if(retornaValorConfiguracao('OCULTAR_PLANO_REDECREDENCIADA') == 'SIM'){
			$retorno['PLANOS']['MOSTRAR']  = false;		
		}
		
	}
	
	if((trim($redeIndicada) !== '') and ($redeIndicada!=='NULL')){
		$filtro .= ' and CODIGO_REDE_INDICADA= '.aspas($redeIndicada);
	}

	
	if($retorno['PLANOS']['MOSTRAR'] === true){
				
		$queryPlano  = " SELECT COALESCE(CAST(CODIGO_REDE_INDICADA AS VARCHAR(10)), 'NULL') CODIGO_REDE_INDICADA, NOME_PLANO_FAMILIARES ";
		$queryPlano .= " FROM PS1030 ";
		$queryPlano .= " WHERE PS1030.DATA_INUTILIZ_REGISTRO IS NULL ";
		
		if($_SESSION['perfilOperador'] == 'EMPRESA' && retornaValorConfiguracao('VALIDAR_PLANOS_ESP_EMPRESA') == 'SIM'){
			$queryPlano .= " AND PS1030.CODIGO_PLANO IN (SELECT PS1059.CODIGO_PLANO FROM PS1059 WHERE CODIGO_EMPRESA = " . aspas($_SESSION['codigoIdentificacao']) . ") ";			
		}
		
		if($_SESSION['perfilOperador'] == 'BENEFICIARIO' && $codigoSmart == '3423'){//Plena
			$queryPlano .= " AND PS1030.CODIGO_PLANO IN (SELECT PS1000.CODIGO_PLANO FROM PS1000 WHERE CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']) . ") ";			
		}
		$queryPlano .= " ORDER BY NOME_PLANO_FAMILIARES";
		
		$resSubPlano = jn_query($queryPlano);
		
		$plano['VALOR'] = '';
		$plano['DESC'] = '';
		$retorno['PLANOS']['DADOS'][] = $plano;
		
		while($rowPlano = jn_fetch_object($resSubPlano)){
		
			$plano['VALOR'] = $rowPlano->CODIGO_REDE_INDICADA;
			$plano['DESC'] = jn_utf8_encode($rowPlano->NOME_PLANO_FAMILIARES);
			$retorno['PLANOS']['DADOS'][] = $plano;
		}
	}
	
	if($retorno['TIPOS']['MOSTRAR'] === true){
	
		$query  = "SELECT t.OPCOES_COMBO FROM cfgCampos_Sis t 
				   WHERE (Nome_Tabela = ". aspas('PS5000').") AND (Nome_Campo = ".aspas('TIPO_PRESTADOR').")";    
		
		$tipo['VALOR'] = '';
		$tipo['DESC'] = '';
		$retorno['TIPOS']['DADOS'][] = $tipo;		

		if(retornaValorConfiguracao('OPCAO_TODOS_REDE_CREDENCIADA') == 'SIM' || retornaValorConfiguracao('OP_TODOS_TP_PREST_REDE_CRED') == 'SIM'){
			$tipo['VALOR'] = '';
			$tipo['DESC'] = 'TODOS';
			$retorno['TIPOS']['DADOS'][] = $tipo;		
		}
		
		$res = jn_query($query);
		if ($row = jn_fetch_assoc($res)) {
			
			if($codigoSmart == '4055' || $codigoSmart == '3808'){
				$opcoes = ';99 - MEDICO / CLINICA;' . $row['OPCOES_COMBO'];
			}else{				
				$opcoes = $row['OPCOES_COMBO'];
			}
			
			
			
			if (strpos($opcoes, ',')) {
				$_cs = ',';
			} else {
				$_cs = ';';
			}

			$valores = explode($_cs, $opcoes);
			
			foreach ((array) $valores as $val ) {
				if (empty($val))
					continue;

				$temp = explode('-', trim($val));

				if(($codigoSmart == '4055' || $codigoSmart == '3808') && ($temp[0] == 01 || $temp[0] == 02))//RS Saude e Sintimmmeb usam outra opcao
					continue;				


				$tipo['VALOR'] = trim($temp[0]);

				if($codigoSmart == '3808' && trim($temp[0]) == 05){
					$tipo['DESC'] = jn_utf8_encode('OUTROS / ODONTOLOGIA ');
				}else{
					$tipo['DESC'] = jn_utf8_encode(trim($temp[1]));
				}
								
				$retorno['TIPOS']['DADOS'][] = $tipo;
				
			}

		}	
	
	}
	
	if($retorno['CIDADES']['MOSTRAR'] === true){
		$retorno['CIDADES']['DADOS']= getCidades($filtro);
		//jn_utf8_encode_array($retorno['CIDADES']['DADOS'])
	}
	
	if($retorno['BAIRROS']['MOSTRAR'] === true){
		if(count($retorno['CIDADES']['DADOS']) > 1){
			$retorno['BAIRROS']['DADOS'] = GetBairros($filtro);
			//jn_utf8_encode_array($retorno['BAIRROS']['DADOS']);
		}else{
			$retorno['BAIRROS']['MOSTRAR'] = false;
		}
	}
	
	if($retorno['ESTADOS']['MOSTRAR'] === true){
		$retorno['ESTADOS']['DADOS'] = GetEstados($filtro);
	}

	if($retorno['ESPECIALIDADES']['MOSTRAR'] === true){

		$retorno['ESPECIALIDADES']['DADOS'] = GetEspecialidades($filtro);
		//jn_utf8_encode_array($retorno['ESPECIALIDADES']['DADOS']);
		
	}
	
	if($retorno['PLANOS']['MOSTRAR'] === true){
		$retorno['PLANOS']['DADOS'] = GetPlanos($filtro);				
	}
	
	//pr($retorno);
	echo json_encode($retorno);
	
}else if($dadosInput['tipo'] =='atualizaDados'){
	$retorno = null;	
	
	$retorno['PLANOS']['MOSTRAR']  = true;
	$retorno['PLANOS']['LABEL']    = 'Plano do Beneficiario';
	
	$retorno['TIPOS']['MOSTRAR']   = true;
	$retorno['TIPOS']['LABEL']     = 'Tipo do Prestador';
	
	$retorno['BAIRROS']['MOSTRAR'] = true;
	$retorno['BAIRROS']['LABEL']   = 'Bairro do Prestador';
	
	$retorno['CIDADES']['MOSTRAR'] = true;
	$retorno['CIDADES']['LABEL']   = 'Cidade do Prestador';
	
	$retorno['ESTADOS']['MOSTRAR']  = true;
	$retorno['ESTADOS']['LABEL']    = 'Estado do Prestador';
	
	$retorno['ESPECIALIDADES']['MOSTRAR'] = true;
	$retorno['ESPECIALIDADES']['LABEL'] = 'Especialidade do Prestador';

	$retorno['NOME']['MOSTRAR'] = true;
	$retorno['NOME']['LABEL'] = 'Nome do Prestador';
	
	
	$filtro = ' Where 1= 1 ';
	
	if($_SESSION['perfilOperador']==='BENEFICIARIO'){
		$query  = "SELECT coalesce(cast(CODIGO_REDE_INDICADA as varchar(10)), 'NULL') CODIGO_REDE_INDICADA  from PS1000
				   inner join PS1030 on PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO
				   WHERE (CODIGO_ASSOCIADO = ". aspas($_SESSION['codigoIdentificacao']).")";    
		
		$res = jn_query($query);

		if($row = jn_fetch_object($res)){
			$redeIndicada = $row->CODIGO_REDE_INDICADA;
		}
		
	}
	
	if(($redeIndicada!=='') and($redeIndicada!=='NULL')and ($redeIndicada!==null)){
		$filtro .= ' and CODIGO_REDE_INDICADA= '.aspas($redeIndicada);
	}
	
	

	$campoAlterado = $dadosInput['campo'];
	$valorAlterado = $dadosInput['dados'][$campoAlterado];
	
	
	if(($dadosInput['dados']['PLANOS']!=='') and($dadosInput['dados']['PLANOS']!=='NULL')and ($dadosInput['dados']['PLANOS']!==null)){		
		$filtro .= ' and CODIGO_REDE_INDICADA= '.aspas($dadosInput['dados']['PLANOS']);		
	}

	if(($dadosInput['dados']['TIPOS']!=='') and($dadosInput['dados']['TIPOS']!=='NULL')and ($dadosInput['dados']['TIPOS']!==null)){ 
		if($dadosInput['dados']['TIPOS'] == '99' && ($codigoSmart == '4055' || $codigoSmart == '3808')){
			$filtro .= '  AND (TIPO_PRESTADOR = 01 ';
			$filtro .= '  OR TIPO_PRESTADOR = 02 ) ';
		}else{			
			$filtro .= ' and TIPO_PRESTADOR= '.aspas($dadosInput['dados']['TIPOS']);		
		}
	
	}

	if(($dadosInput['dados']['ESTADOS']!=='') and($dadosInput['dados']['ESTADOS']!=='NULL')and ($dadosInput['dados']['ESTADOS']!==null)){ 
		$filtro .= ' and ESTADO_PRESTADOR= '.aspas($dadosInput['dados']['ESTADOS']);				
	}

	if(($dadosInput['dados']['CIDADES']!=='') and($dadosInput['dados']['CIDADES']!=='NULL')and ($dadosInput['dados']['CIDADES']!==null)){ 
		$filtro .= ' and CIDADE_PRESTADOR= '.aspas($dadosInput['dados']['CIDADES']);		
	
	}

	if(($dadosInput['dados']['BAIRROS']!=='') and($dadosInput['dados']['BAIRROS']!=='NULL')and ($dadosInput['dados']['BAIRROS']!==null)){ 
		$filtro .= ' and BAIRRO_PRESTADOR= '.aspas($dadosInput['dados']['BAIRROS']);		
	
	}

	if(($dadosInput['dados']['ESPECIALIDADES']!=='') and($dadosInput['dados']['ESPECIALIDADES']!=='NULL')and ($dadosInput['dados']['ESPECIALIDADES']!==null)){ 
		$filtro .= ' and CODIGO_ESPECIALIDADE= '.aspas($dadosInput['dados']['ESPECIALIDADES']);		
	
	}

	if($retorno['PLANOS']['MOSTRAR'] === true){
		$retorno['PLANOS']['DADOS']= getPlanos($filtro);
	}
	
	if($retorno['ESTADOS']['MOSTRAR'] === true){
		$retorno['ESTADOS']['DADOS']= getEstados($filtro);
	}
	
	if($retorno['CIDADES']['MOSTRAR'] === true){
		$retorno['CIDADES']['DADOS']= getCidades($filtro);
	}
	
	if($retorno['BAIRROS']['MOSTRAR'] === true){
		if(count($retorno['CIDADES']['DADOS']) > 1){
			$retorno['BAIRROS']['DADOS'] = GetBairros($filtro);
		}else{
			$retorno['BAIRROS']['MOSTRAR'] = false;
		}
	}
	if($retorno['ESPECIALIDADES']['MOSTRAR'] === true){
		$retorno['ESPECIALIDADES']['DADOS'] = GetEspecialidades($filtro);
	}

		
	
	echo json_encode($retorno);
	
}else if($dadosInput['tipo'] =='buscaDadosPlanos'){
	
	$codPlano = $dadosInput['codigoPlano'];
	
	$query  = ' SELECT NOME_PLANO_FAMILIARES,CODIGO_TIPO_COBERTURA, PS1030.CODIGO_CADASTRO_ANS, ';
	$query .= ' 	CASE WHEN TIPO_CONTRATACAO_ANS = "1" THEN "INDIVIDUAL FAMILIAR" WHEN TIPO_CONTRATACAO_ANS = "3" THEN  "COLETIVO EMPRESARIAL" WHEN TIPO_CONTRATACAO_ANS = "4" THEN  "COLETIVO ADESAO" ELSE "OUTROS" END AS TIPO_CONTRATACAO_ANS, ';
	$query .= ' 	STATUS_COMERCIALIZACAO_PLANO FROM PS1030 ';

	if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
		$query .= ' INNER JOIN PS1000 ON (PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO) ';
		$query .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	}elseif(retornaValorConfiguracao('BUSCA_PLANOS_REDE') == 'SIM'){
		$query .= ' WHERE CODIGO_REDE_INDICADA = ' . aspas($codPlano);
	}else{
		$query .= ' WHERE CODIGO_PLANO = ' . aspas($codPlano);
	}

	$res = jn_query($query);
	$row = jn_fetch_object($res);  
	
	$ArrDadosPlano = Array();
	
	$ArrDadosPlano['nomePlano']					= jn_utf8_encode($row->NOME_PLANO_FAMILIARES);
	$ArrDadosPlano['codigoCadastroAns'] 		= jn_utf8_encode($row->CODIGO_CADASTRO_ANS);
	$ArrDadosPlano['tipoContratacao'] 			= jn_utf8_encode($row->TIPO_CONTRATACAO_ANS);
	$ArrDadosPlano['statusComercializacao'] 	= jn_utf8_encode($row->STATUS_COMERCIALIZACAO_PLANO);

	$retorno = $ArrDadosPlano;
	
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='buscaDescricao'){

	$descricaoCampo = '';

	if($dadosInput['campoDesc'] == 'especialidade'){
		$queryEsp = 'SELECT PS5100.NOME_ESPECIALIDADE FROM PS5100 WHERE CODIGO_ESPECIALIDADE = ' . aspas($dadosInput['valorCampo']);
		$resEsp = jn_query($queryEsp);
		$rowEsp = jn_fetch_object($resEsp);
		$descricaoCampo = jn_utf8_encode($rowEsp->NOME_ESPECIALIDADE);	
	}elseif($dadosInput['campoDesc'] == 'tipoPrest'){

		$query  = "SELECT t.OPCOES_COMBO FROM cfgCampos_Sis t 
				   WHERE (Nome_Tabela = ". aspas('PS5000').") AND (Nome_Campo = ".aspas('TIPO_PRESTADOR').")";    
		
		$tipo['VALOR'] = '';
		$tipo['DESC'] = '';
		$retorno['TIPOS']['DADOS'][] = $tipo;		
		
		$res = jn_query($query);
		if ($row = jn_fetch_assoc($res)) {
			
			if (strpos($row['OPCOES_COMBO'], ',')) {
				$_cs = ',';
			} else {
				$_cs = ';';
			}

			$valores = explode($_cs, $row['OPCOES_COMBO']);			
			foreach ((array) $valores as $val ) {
				$temp = explode('-', trim($val));
				if(trim($temp[0]) == $dadosInput['valorCampo']){
					if($codigoSmart == '3808' && $dadosInput['valorCampo'] == '05'){//RS Saude
						$descricaoCampo = jn_utf8_encode('OUTROS / DENTISTA');	
					}else{
						$descricaoCampo = jn_utf8_encode(trim($temp[1]));	
					}
					
				}elseif($codigoSmart == '3808' && $dadosInput['valorCampo'] == '99'){//RS Saude
					$descricaoCampo = jn_utf8_encode('MEDICO / CLINICA');
				}
			}
		}	
	}
	
	$retorno = Array();
	$retorno['descricao']	= $descricaoCampo;	
	
	echo json_encode($retorno);
	
}

function GetCidades($filtro){
		$queryCidade = 'select distinct CIDADE_PRESTADOR from VW_REDECREDENCIADA_AL2 '.$filtro.'  order by CIDADE_PRESTADOR';
		
		$resCidade = jn_query($queryCidade);
		$cidades = null;
		
		$cidade['VALOR'] = '';
		$cidade['DESC'] = '';
		$cidades[] = $cidade;

		if(retornaValorConfiguracao('OPCAO_TODOS_REDE_CREDENCIADA') == 'SIM'){
			$cidade['VALOR'] = '';
			$cidade['DESC'] = 'TODOS';
			$cidades[] = $cidade;
		}
		
		while($rowCidade = jn_fetch_object($resCidade)){
		
			$cidade['VALOR'] = jn_utf8_encode($rowCidade->CIDADE_PRESTADOR);
			$cidade['DESC'] = jn_utf8_encode($rowCidade->CIDADE_PRESTADOR);
			$cidades[] = $cidade;
		}	
			
		return $cidades;
}

function GetBairros($filtro){

	
		$queryBairro = 'select distinct BAIRRO_PRESTADOR from VW_REDECREDENCIADA_AL2 '.$filtro.' order by BAIRRO_PRESTADOR';
		
		$resBairro = jn_query($queryBairro);
		$bairros = null;
		
		$bairro['VALOR'] = '';
		$bairro['DESC'] = '';
		$bairros[] = $bairro;

		if(retornaValorConfiguracao('OPCAO_TODOS_REDE_CREDENCIADA') == 'SIM' || retornaValorConfiguracao('OP_TODOS_BAIRRO_REDE_CRED') == 'SIM'){
			$bairro['VALOR'] = 'TODOS';
			$bairro['DESC'] = 'TODOS';
			$bairros[] = $bairro;
		}
		
		while($rowBairro = jn_fetch_object($resBairro)){
		
			$bairro['VALOR'] = jn_utf8_encode($rowBairro->BAIRRO_PRESTADOR);
			$bairro['DESC'] = jn_utf8_encode($rowBairro->BAIRRO_PRESTADOR);
			$bairros[] = $bairro;
		}	
		
		return $bairros;
}

function GetEstados($filtro){
		$queryEstado = 'select distinct ESTADO_PRESTADOR from VW_REDECREDENCIADA_AL2 '.$filtro.'  order by ESTADO_PRESTADOR';
		
		$resEstado = jn_query($queryEstado);
		$estados = null;
		
		$estado['VALOR'] = '';
		$estado['DESC'] = '';
		$estados[] = $estado;

		if(retornaValorConfiguracao('OPCAO_TODOS_REDE_CREDENCIADA') == 'SIM'){
			$estado['VALOR'] = '';
			$estado['DESC'] = 'TODOS';
			$estados[] = $estado;
		}
		
		while($rowEstado = jn_fetch_object($resEstado)){
		
			$estado['VALOR'] = jn_utf8_encode($rowEstado->ESTADO_PRESTADOR);
			$estado['DESC'] = jn_utf8_encode($rowEstado->ESTADO_PRESTADOR);
			$estados[] = $estado;
		}	
			
		return $estados;
}

function GetEspecialidades($filtro){
		$queryEspecialidade = 'select distinct CODIGO_ESPECIALIDADE,NOME_ESPECIALIDADE from VW_REDECREDENCIADA_AL2 '.$filtro.' order by NOME_ESPECIALIDADE';
		
		$resEspecialidade = jn_query($queryEspecialidade);
		
		$especialidades = null;
		
		$especialidade['VALOR'] = '';
		$especialidade['DESC'] = '';
		$especialidades[] = $especialidade;

		if(retornaValorConfiguracao('OPCAO_TODOS_REDE_CREDENCIADA') == 'SIM'){
			$especialidade['VALOR'] = '';
			$especialidade['DESC'] = 'TODOS';
			$especialidades[] = $especialidade;
		}
		
		while($rowEspecialidade = jn_fetch_object($resEspecialidade)){
		
			$especialidade['VALOR'] = $rowEspecialidade->CODIGO_ESPECIALIDADE;
			$especialidade['DESC'] = jn_utf8_encode($rowEspecialidade->NOME_ESPECIALIDADE);
			$especialidades[] = $especialidade;
		}	
		return $especialidades;
}

function GetPlanos($filtro){
	$queryPlanos = 'select distinct CODIGO_REDE_INDICADA,NOME_PLANO from VW_REDECREDENCIADA_AL2 '.$filtro.' order by NOME_PLANO';
	$resPlanos = jn_query($queryPlanos);
	
	$plano = null;
	
	$plano['VALOR'] = '';
	$plano['DESC'] = '';
	$planos[] = $plano;

	if(retornaValorConfiguracao('OPCAO_TODOS_REDE_CREDENCIADA') == 'SIM'){
		$plano['VALOR'] = '';
		$plano['DESC'] = 'TODOS';
		$planos[] = $plano;
	}
	
	while($rowPlanos = jn_fetch_object($resPlanos)){
	
		$plano['VALOR'] = $rowPlanos->CODIGO_REDE_INDICADA;
		$plano['DESC'] = jn_utf8_encode($rowPlanos->NOME_PLANO);
		$planos[] = $plano;
	}	
	return $planos;
}
?>