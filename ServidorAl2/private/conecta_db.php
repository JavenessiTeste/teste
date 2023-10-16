<?php

	/*
	* Este é o arquivo responsavel pela conexao com o banco de dados do Firebird;
	* Ele deve ser incluido apenas 1 vez (require_once) onde ele tentará instanciar
	* a variavel global $conexao com o identificador da conexao.
	*
	* Caso não seja possível, será informado o erro.
	*/
 
	//Variavel dominio de banco de dados
	$DBDom['firebird'] = 0;
	$DBDom['mssqlserver'] = 1;
	$DBDom['mysql'] = 2;
	$DBDom['sqlsrv'] = 3;	
	
	$MultiBancoTest = !empty($_SESSION['MULTIDATABASE']) ? $_SESSION['MULTIDATABASE'] : '';
	$type_db = !empty($SisConfig['MULTIDATABASE'][$MultiBancoTest]['TYPE']) ? $SisConfig['MULTIDATABASE'][$MultiBancoTest]['TYPE'] : $SisConfig['DATABASE']['TYPE'];


	//$SisConfig['MULTIDATABASE'][$MultiBancoTest]['TYPE'];

	$dadosCon = array( 
					$SisConfig['DATABASE']['SERVER'],
					$SisConfig['DATABASE']['USERNAME'],
					$SisConfig['DATABASE']['PASSWORD'],
					$SisConfig['DATABASE']['SOURCE'],
					$SisConfig['DATABASE']['TYPE'],
					$SisConfig['DATABASE']['COLLATION'],	  
	);
		
	$jnCon = new jnConnect();
	$Con = $jnCon->conectarBancoDeDados($dadosCon[0],$dadosCon[1],$dadosCon[2],$dadosCon[3],$type_db,$dadosCon[5]);

	
	$_SESSION['error_db']  = $jnCon->error;
	$_SESSION['type_db']  = $type_db;
	$_SESSION['version_add_db']  = $DBDom[$type_db];
	$_SESSION['SOURCE'] = $dadosCon[3];

	//$_SESSION['tipo_operadora']  = $dadosCon[6] ;

	jn_format_time();
	
?>