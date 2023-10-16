<?php
	global $conexao;
	global $conexaoSequencial;
	global $transacao;
	global $erroSql;
	
	class jnConnect {
	
		// Definição das variáveis
		var $resource = 0;
		var $error    = '';
		
		function conectarBancoDeDados($server, $username, $password, $source, $type = 'firebird', $colation = ''){
			global $conexao;
			global $conexaoSequencial;
			global $testarCon;
			
			if($type == 'firebird'){
				
				if($this->resource = ibase_connect($server . ':' . $source, $username, $password, $colation, 0, 1))
					$this->error = '';
				else
					$this->error = 'Não foi possível se conectar ao banco de dados, por favor contate o suporte tecnico' . ($_GET['debug'] == true ? $server . ':' . $source : '');
			
			}
			elseif($type == 'mysql'){
				
				$aux = true;
				
				if($testarCon){

					$aux = @mysql_connect($server, $username, $password,$source);

					if($aux){
						mysql_close($aux);
						$aux = true;
						$this->error = "";
					}else{
						$this->error = "Falha ao conecar ao MySQL.";
						$aux = false;
					}
				}
				if ($aux){
					$this->resource = @mysqli_connect($server, $username, $password,$source);
					$conexao = $this->resource;
					
					if (mysqli_connect_errno())
					{
					  $this->error = "Falha ao conecar ao MySQL: " . mysqli_connect_error();
					}else{
						$conexaoSequencial = mysqli_connect($server, $username, $password,$source);
					}
				}
			}
			elseif($type == 'mssqlserver'){
			
				if($this->resource = mssql_connect($server, $username, $password)){
					mssql_select_db($source, $this->resource);
					$this->error = '';
				}
				else
					$this->error = 'Não foi possível se conectar ao banco de dados, por favor contate o suporte tecnico' . ($_GET['debug'] == true ? $server : '');
			
			}elseif( $type == 'sqlsrv' ){	
				global $bd;
				global $naoUtilizarUtf8Encode;
				$connectionInfo = array();		
				if($naoUtilizarUtf8Encode == true)
						$connectionInfo = array("Database"=> $source, "UID"=>$username, "PWD"=>$password, "CharacterSet" => "UTF-8");
			  else
					$connectionInfo = array("Database"=> $source, "UID"=>$username, "PWD"=>$password);
				if ($this->resource = sqlsrv_connect($server, $connectionInfo)){
					$this->error = 'Conetou';
					global $bd;
					$bd = $this->resource;
				}else{
					$this->error = 'Erro ao conectar banco de dados';
					echo '1:' . $this->error . '<br>';
					echo '2:' . print_r( sqlsrv_errors(), true);
					exit;
				}					
				$bd = $this->resource;
		    }
			return empty($this->error) ? $this->resource : false;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //




function msSqlParse($non_parsed,$debug =false){
	$parse  = '';
	$parse  = ($parse == '') ? $non_parsed : $parse;
	
	//Substituir as DATE por as DATETIME
	$for_parse 	= array(
					'/[A|a][s|S][\s]{1,}(DATE)/',
				  	'/[f|F][i|I][r|R][s|S][t|T][\s]{1,}[0-9]{0,}[\s]{1,}[S|s][K|k][I|i][P|p][\s]{1,}[0-9]{0,}/',
				  	'/\b[f|F][i|I][r|R][s|S][t|T]\b/',
					'/[e|E][x|X][t|T][r|R][a|A][c|C][t|T][\s]{0,}[(][\s]{0,}[d|D][a|D][y|Y][\s]{0,}[f|F][r|R][o|O][m|M]/',
					'/[e|E][x|X][t|T][r|R][a|A][c|C][t|T][\s]{0,}[(][\s]{0,}[m|m][o|O][n|N][t|T][h|H][\s]{0,}[f|F][r|R][o|O][m|M]/',
					'/[e|E][x|X][t|T][r|R][a|A][c|C][t|T][\s]{0,}[(][\s]{0,}[y|Y][e|E][a|A][r|R][\s]{0,}[f|F][r|R][o|O][m|M]/',
					'/\|\|/',
					//'/\+\+/',
					'["]',
					'/[_][_][|][A][|][_][_]/',
					'/(\b[f|F][r|R][o|O][m|M]\b) *(\d*) *(\b[f|F][o|O][r|R]\b)/'
				  );
	
	$to_parse	= array(
					'as DATETIME',
					"TOP pag_Aretornar * FROM ( SELECT ROW_NUMBER() OVER (ORDERBYPARSE) as ROWNUM, ",
					"TOP",
					'DAY(',
					'MONTH(',
					'YEAR(',
					'+',
					//'+',	
					'\'',
					'"',
					', $2 ,'
				  );

	$especial	= array(
					false,
					'pagination',
					false,
					false,
					false,
					false,
					false,
					false,
					false,
					false
				  );	  
	foreach($for_parse as $key => $item){			  
		preg_match($for_parse[$key],$parse, $tested);
		if(!empty($tested[0])){
			if(!$especial[$key])
				$parse = preg_replace($for_parse[$key], $to_parse[$key],$parse);
			elseif( $especial[$key] == 'pagination' ){
				preg_match_all('/[0-9]{1,}/',$tested[0], $values_pagination);
				$pag_Aretornar = $values_pagination[0][0];
				$pag_inicio = $values_pagination[0][1];
				
				preg_match('/[orderORDER]{5}[\s]{0,}[BYby]{2}.*/',$parse, $value_order);				
				$order_by = $value_order[0];
				$parse = preg_replace('/[orderORDER]{5}[\s]{0,}[BYby]{2}.*/', '',$parse);
				
				$parse = preg_replace($for_parse[$key], $to_parse[$key],$parse);
				//$parse = preg_replace('/(UNION|union|Union)[\s]{0,}(SELECT|select|Select)/', 'UNION SELECT 1, ',$parse);
                                $parse = $parse.' ) as PAG where ROWNUM > ' . $pag_inicio . ' ORDER BY ROWNUM ASC';
				$parse = str_replace('pag_Aretornar', $pag_Aretornar,$parse);
				//Pr($pag_inicio, false);
				//$parse = str_replace('pag_inicio', $pag_inicio,$parse);
				
				//$parse = str_replace('WHERE ', $order_by,$parse);
				//$parse = str_replace('ORDERBYPARSE', $order_by,$parse);
				
				$parse = str_replace('ORDERBYPARSE', $order_by,$parse);
			}
		}
	}
	
		
	$parsed = $parse;
	if($debug){
		print_r($parsed);
		echo '<br /><br />';
	}
	
	return $parsed;
}

// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_query($query, $debug = false, $parse = true, $ignorarErros = false){
		global $conexao;
		global $erroSql;
		global $dados;
		global $idMysql;
		global $msgErroSql;
		global $bd;
		$idMysql = '';
		$msgErroSql = '';
		
		if(isset($_GET['debugar']) == 'on'){
			$debug = true;
		}
		
		if($_SESSION['type_db'] == 'firebird'){
			if($debug) 
				echo ' | ' . $query . ' | ';
			
			$retorno = @ibase_query($query);
			
			if ($ignorarErros)
			{
				return $retorno;
			}

			if((!$retorno)){
				$erro = ibase_errmsg();
				if(isset($_GET['Teste']) == 'OK'){
						echo "\n\r -ERRO-> \n\r \t". jn_utf8_encode($query)." \n\r <-ERRO-  \n\r";
						echo "\n\r -DESC ERRO-> \n\r \t". jn_utf8_encode($erro)." \n\r <-DESC ERRO-  \n\r";
						
				}	
				
				$msgErroSql = str_replace("'", '"', $erro);

				$qryTratamento = $query;
				$qryTratamento = str_replace("'", '"', $qryTratamento);
				$dadosAuxiliares = "\n\rErro: ".$erro."\n\r" ;
				$dadosAuxiliares = str_replace("'", '"', $dadosAuxiliares);

				
				$qryLogQuery = "INSERT INTO log_operacoes_erro (DATA_OPERACAO, HORA_OPERACAO,ERRO, INSTRUCAO_SQL,CODIGO_USUARIO,PERFIL_USUARIO) VALUES ( " . 
							aspas(date('Y-m-d'))        				. ", " .
							aspas(date('H:i'))          				. ", " .
							aspas($dadosAuxiliares )      				. ", " .
							aspas($qryTratamento )      				. ", " .
							aspas($_SESSION['codigoIdentificacao'] )    . ", " .
							aspas($_SESSION['perfilOperador'])       	. " ) returning NUMERO_REGISTRO ";

				$result = @ibase_query($qryLogQuery);	
				//echo $qryLogQuery;
				$dadosResult = jn_fetch_object($result);
				geraErro409Header($dadosResult->NUMERO_REGISTRO);
				
			}else{
			
				if(isset($_GET['Teste']) == 'OK')
						echo "<br><br>\n\r -OK-> \n\r \t". jn_utf8_encode($query)." \n\r <-OK-  \n\r <br><br>";
			}
			return $retorno;
			
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			if($debug)
				echo ' | ' . $query . ' | ';
			mysqli_query($conexao,'SET CHARACTER SET ISO88591');

			$query = utf8_decode($query);
			$retorno = mysqli_query($conexao,$query);
			
			if(substr(strtoupper(trim($query)), 0, 6) == "INSERT"){
				$idMysql = mysqli_insert_id($conexao);
			}
			
			if((!$retorno)){
								

					$erro = mysqli_error($conexao);
					
					$msgErroSql = str_replace("'", '"', $erro);

					$qryTratamento = $query;
					$qryTratamento = str_replace("'", '"', $qryTratamento);
					$dadosAuxiliares = "\n\rErro: ".$erro."\n\r" ;
					$dadosAuxiliares = str_replace("'", '"', $dadosAuxiliares);

					
					$qryLogQuery = "INSERT INTO log_operacoes_erro (data_operacao, hora_operacao,erro, instrucao_sql,CODIGO_USUARIO,PERFIL_USUARIO) VALUES ( " . 
							aspas(date('Y-m-d'))        				. ", " .
							aspas(date('H:i'))          				. ", " .
							aspas($dadosAuxiliares )      				. ", " .
							aspas($qryTratamento )      				. ", " .
							aspas($_SESSION['codigoIdentificacao'] )    . ", " .
							aspas($_SESSION['perfilOperador'])       	. " ) ";

					mysqli_query($conexao, $qryLogQuery);	
					
					
					if(isset($_GET['Teste']) == 'OK'){
						echo "\n\r -ERRO-> \n\r \t". jn_utf8_encode($query)." \n\r <-ERRO-  \n\r";
						echo "\n\r -DESC ERRO-> \n\r \t".$erro." \n\r <-DESC ERRO-  \n\r";					
					}	
					geraErro409Header(jn_insert_id());
					
			}else{
				if(isset($_GET['Teste']) == 'OK')
					echo "\n\r -OK-> \n\r \t". jn_utf8_encode($query)." \n\r <-OK-  \n\r";
			}
					
			
			if(!$retorno)
				$erroSql++;
			return $retorno;
		}
		elseif( ($_SESSION['type_db'] == 'mssqlserver') && ($parse) ){
			$res = @mssql_query(msSqlParse($query, $debug));
			
			if ($ignorarErros){
				return $res;
			}
			
			if((!$res)){
				$erro = mssql_get_last_message();
				if(isset($_GET['Teste']) == 'OK'){
						echo "\n\r -ERRO-> \n\r \t". jn_utf8_encode(msSqlParse($query, $debug))." \n\r <-ERRO-  \n\r";
						echo "\n\r -DESC ERRO-> \n\r \t". jn_utf8_encode($erro)." \n\r <-DESC ERRO-  \n\r";
						
				}	
				
				$msgErroSql = str_replace("'", '"', $erro);

				$qryTratamento = msSqlParse($query, $debug);
				$qryTratamento = str_replace("'", '"', $qryTratamento);
				$dadosAuxiliares = "\n\rErro: ".$erro."\n\r" ;
				$dadosAuxiliares = str_replace("'", '"', $dadosAuxiliares);

				
				$qryLogQuery = "INSERT INTO log_operacoes_erro (data_operacao, hora_operacao,erro, instrucao_sql,CODIGO_USUARIO,PERFIL_USUARIO) VALUES ( " . 
							aspas(date('Y-m-d'))        				. ", " .
							aspas(date('H:i'))          				. ", " .
							aspas($dadosAuxiliares )      				. ", " .
							aspas($qryTratamento )      				. ", " .
							aspas($_SESSION['codigoIdentificacao'] )    . ", " .
							aspas($_SESSION['perfilOperador'])       	. " ) ";

				@mssql_query($qryLogQuery);	
				geraErro409Header(jn_insert_id());
			
			}else{
			
				if(isset($_GET['Teste']) == 'OK')
						echo "\n\r -OK-> \n\r \t". jn_utf8_encode($query)." \n\r <-OK-  \n\r";
			}			
			
			
			
			if($debug){
				echo msSqlParse($query, $debug) . " -Debug- ";
			}
			
			if(!$res){
				echo msSqlParse($query, $debug) . " -Error- ";
			}
			
			return $res;
		}
		elseif( ($_SESSION['type_db'] == 'sqlsrv') && ($parse) ){
			$res = @sqlsrv_query($bd, msSqlParse( $query, $debug ) );
			
			if ($ignorarErros){
				return $res;
			}
			
			if((!$res)){
				$erro =  sqlsrv_errors();
				//pr($erro[0]['message'],true);
				if(isset($_GET['Teste']) == 'OK'){
						pr($query);
						pr(msSqlParse($query, $debug));
						echo "\n\r -ERRO-> \n\r \t". jn_utf8_encode(msSqlParse($query, $debug))." \n\r <-ERRO-  \n\r";
						echo "\n\r -DESC ERRO-> \n\r \t". jn_utf8_encode($erro[0]['message'])." \n\r <-DESC ERRO-  \n\r";
						
				}	
				
				$msgErroSql = str_replace("'", '"', $erro[ 'message']);

				$qryTratamento = msSqlParse($query, $debug);
				$qryTratamento = str_replace("'", '"', $qryTratamento);
				$dadosAuxiliares = "\n\rErro: ".$erro[0]['message']."\n\r" ;
				$dadosAuxiliares = str_replace("'", '"', $dadosAuxiliares);

				
				$qryLogQuery = "INSERT INTO log_operacoes_erro (data_operacao, hora_operacao,erro, instrucao_sql,CODIGO_USUARIO,PERFIL_USUARIO) VALUES ( " . 
							aspas(date('Y-m-d'))        				. ", " .
							aspas(date('H:i'))          				. ", " .
							aspas($dadosAuxiliares )      				. ", " .
							aspas($qryTratamento )      				. ", " .
							aspas($_SESSION['codigoIdentificacao'] )    . ", " .
							aspas($_SESSION['perfilOperador'])       	. " ) ";

				@sqlsrv_query($bd,$qryLogQuery);	
				geraErro409Header(jn_insert_id());
			
			}else{
			
				if(isset($_GET['Teste']) == 'OK')
						echo "<br><br>\n\r -OK-> \n\r \t". jn_utf8_encode($query)." \n\r <-OK-  \n\r<br><br>";
			}			
			
			
			
			if($debug){
				echo msSqlParse($query, $debug) . " -Debug- ";
			}
			
			if(!$res){
				echo msSqlParse($query, $debug) . " -Error- ";
			}
			
			return $res;
		}
		
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_num_fields($sql, $debug = false){
		if($_SESSION['type_db'] == 'firebird'){
			return ibase_num_fields($sql);
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			return mysqli_num_fields($sql);
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			return mssql_num_fields($sql);
		}elseif($_SESSION['type_db'] == 'sqlsrv'){
			return sqlsrv_num_fields($sql);
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_field_info($sql, $indice){
		global $bd;
		if($_SESSION['type_db'] == 'firebird'){
			return ibase_field_info($sql, $indice);
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			$dado = mysqli_fetch_field($sql);
			return $dado->name;
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			return mssql_field_name($sql, $indice);
		}
		elseif($_SESSION['type_db'] == 'sqlsrv'){			
			$retorno = sqlsrv_field_metadata($sql);
			
			return $retorno[$indice]['Name'];
			
		}
	}




function jn_field_metadata($sql, $indice){
		global $bd;
		if($_SESSION['type_db'] == 'firebird'){
			return ibase_field_info($sql, $indice);
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			$dado = mysqli_fetch_field($sql);
			return $dado->name;
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			return mssql_field_info($sql, $indice);
		}
		elseif($_SESSION['type_db'] == 'sqlsrv'){			
			$retorno = sqlsrv_field_metadata($sql);
			return $retorno[$indice];
		}
	}





	
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_mssql_assoc_upper($resource, $debug = false){
		$assoc = mssql_fetch_assoc($resource);
		
		if($debug){
			pr($assoc);	
		}
		
		if(empty($assoc)){
			return false;
		}
		else{
			$arrayRetorno = array();
			foreach( $assoc as $assocKey => $assocItem ){
				$new_assoc_key = strtoupper($assocKey);
				$arrayRetorno[$new_assoc_key] = $assocItem;
			}
			return $arrayRetorno;
		}
	}

	function jn_sqlsrv_assoc_upper($resource, $debug = false){
		$assoc = sqlsrv_fetch_array($resource,SQLSRV_FETCH_ASSOC);
		
		if($debug){
			pr($assoc);	
		}
		
		if(empty($assoc)){
			return false;
		}
		else{
			$arrayRetorno = array();
			foreach( $assoc as $assocKey => $assocItem ){
				$new_assoc_key = strtoupper($assocKey);
				$arrayRetorno[$new_assoc_key] = $assocItem;
			}
			return $arrayRetorno;
		}
	}	


// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_fetch_assoc($resource){
		if($_SESSION['type_db'] == 'firebird'){
			return ibase_fetch_assoc($resource);
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			return mysqli_fetch_assoc($resource);
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			return jn_mssql_assoc_upper($resource);
		}elseif($_SESSION['type_db'] == 'sqlsrv'){
			return jn_sqlsrv_assoc_upper($resource);
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_fetch_object($resource){
	global $conexao;
		if($_SESSION['type_db'] == 'firebird'){
			return ibase_fetch_object($resource,IBASE_TEXT);
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			return mysqli_fetch_object($resource);
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			
			$dadosSelect = jn_arrayToObject(mssql_fetch_assoc($resource));
			$dadosRetorno = array();
			
			if((count($dadosSelect)>0)and(gettype($dadosSelect)=='object')){
				foreach ($dadosSelect as $key => $value) {
					if ($value==' ')
						$value= '';
					$dadosRetorno[$key] = $value;
				}
			}
			
			if((count($dadosSelect)>0) and(gettype($dadosSelect)=='object'))
				return  (object)$dadosRetorno;
			else
				return $dadosSelect;
			
			//return jn_arrayToObject(mssql_fetch_assoc($resource));
		}elseif($_SESSION['type_db'] == 'sqlsrv'){
			$dadosSelect = sqlsrv_fetch_object($resource);
			//pr($dadosSelect);
			if($dadosSelect){
				$dadosRetorno = array();

				//if(count($dadosSelect)>0){ 
				//$pkCount = (is_array($dadosSelect) ? count($dadosSelect) : 0);
				//if($pkCount > 0){//Alteração para funcionar no PHP 7.2
				//	
				//	foreach ($dadosSelect as $key => $value) {
				//		$dadosRetorno[strtoupper($key)] = $value;
				//	}
				//}
				foreach ($dadosSelect as $key => $value) {
					$dadosRetorno[strtoupper($key)] = $value;
				}
				
				//if(count($dadosSelect)>0)
				//if($pkCount > 0)//Alteração para funcionar no PHP 7.2
				//	return  (object)$dadosRetorno;
				//else
				//	return $dadosSelect;
				return (object)$dadosRetorno;
			}else{
				return $dadosSelect;
			}
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_fetch_row($resource){
		global $conexao;
		if($_SESSION['type_db'] == 'firebird'){
			return ibase_fetch_row($resource,IBASE_TEXT);
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			return mysqli_fetch_row($resource);
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			return mssql_fetch_row($resource);
		}elseif($_SESSION['type_db'] == 'sqlsrv'){			
			return sqlsrv_fetch_array($resource, SQLSRV_FETCH_NUMERIC);
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_free_result($resource){
		if($_SESSION['type_db'] == 'firebird'){
			ibase_free_result($resource);
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			mysqli_free_result($resource);
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			mssql_free_result($resource);
		}elseif($_SESSION['type_db'] == 'sqlsrv'){
			sqlsrv_free_stmt($resource);
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_blobtostr($AFieldBlobValue){
		if($_SESSION['type_db'] == 'firebird'){
			$_blob_info = ibase_blob_info($AFieldBlobValue);
			$_blob_hnd = ibase_blob_open($AFieldBlobValue);
			
			$_blob_data = ibase_blob_get($_blob_hnd, $_blob_info[0]);
			
			return $_blob_data;
		}
		else{
			return $AFieldBlobValue;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_arrayToObject($array = array()) {
		if (!empty($array)) {
			$data = false;
			foreach ($array as $akey => $aval) {
				$data -> {strtoupper($akey)} = $aval;
			}
			return $data;
		}
		return false;
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



function jn_paginacao($query, $registro, $pagina){
	if( $_SESSION['type_db'] == 'firebird' )
		return  'SELECT FIRST ' . $registro . ' SKIP ' . $pagina*$registro . ' ' .  substr($query, 6);		
	elseif( $_SESSION['type_db'] == 'mysql' )
		return  $query . ' LIMIT ' . $registro*$pagina . ' , ' . $registro;
	elseif(($_SESSION['type_db'] == 'mssqlserver')or( $_SESSION['type_db'] == 'sqlsrv' ) ){		
		$query    = strtoupper($query);
		$queryAux = explode(' FROM ',$query);
		$parteSelect = substr($queryAux[0], 6);
		$where = '';
		
		if ((strpos($queryAux[1], ' WHERE ') === false) and (strpos($queryAux[1], ' ORDER BY ')=== false)){
			$tabela = $queryAux[1];
		}else if (strpos($queryAux[1], ' WHERE ') === true){
			$queryAux2 = explode(' WHERE ',$queryAux[1]);
			$tabela = $queryAux2[0];
				if (strpos($queryAux2[1], ' ORDER BY ') === true){
					$queryAux2 = explode(' ORDER BY ',$queryAux[1]);
					$where     = ' WHERE '. $queryAux2[0];
				}else{
					$where     = ' WHERE '. $queryAux[1];
				}
				
		}else{
			$queryAux2 = explode(' ORDER BY ',$queryAux[1]);
			$tabela = $queryAux2[0]; 		
		}
		
		
		if (strpos($queryAux[1], ' ORDER BY ') === true){
			$queryAux2 = explode(' ORDER BY ',$queryAux[1]);
			$orderBy = $queryAux2[1]; 
		}else{
			$orderBy = $parteSelect; 		
		}
		
		$query = "WITH resultado AS
					(
						SELECT " . $parteSelect . ", ROW_NUMBER() OVER (ORDER BY ".$orderBy.") AS linha FROM ".$tabela." ". $where ."
					)
					SELECT * FROM resultado WHERE linha BETWEEN ".($pagina * $registro)." AND ".(($pagina * $registro)+ $registro )." ";		
		
		
		
		return $query;
	}	
} 		



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_gerasequencial($table, $contratante=NULL){
		global $conexaoSequencial;
		global $bd;
		if (($contratante == '') or ($contratante === '') or
		    ($contratante == "") or ($contratante === "") or
		    ($contratante == ' ') or ($contratante === ' '))
			$contratante = 'null';
	
		if($_SESSION['type_db'] == 'firebird'){
			$query = "SELECT * FROM GERASEQUENCIAL ('" . strtoupper($table) . "') ;";
		}
		elseif($_SESSION['type_db'] == 'mysql'){
			$query = "SELECT     GERASEQUENCIAL ('" . strtoupper($table) . "') ;";
		}
		elseif($_SESSION['type_db'] == 'mssqlserver'){
			$query = "EXEC GERASEQUENCIAL '" . strtoupper($table) . "' ;";
		}elseif($_SESSION['type_db'] == 'sqlsrv'){			
			/*
			$id_language = '';  
			$term = '';  
			$native_term = '';  
			
			$params = array( 
			array($id_language,SQLSRV_PARAM_IN), 
			array($term, SQLSRV_PARAM_OUT),  
			array($native_term, SQLSRV_PARAM_OUT)               
			);   
			$query = "EXEC GERASEQUENCIAL '" . strtoupper($table) . "' ";
			
			$stmt3 = sqlsrv_query($bd, $query, $params); 

			if( $stmt3 === false ){       
				echo "Error in executing statement 3.\n";       
				die( print_r( sqlsrv_errors(), true)); 
			}
			
			sqlsrv_next_result($stmt3); 
				
				while ($obj=sqlsrv_fetch_object($stmt3)) {       
			
					$retorno = $obj->SEQUENCIA_ATUAL;
				} 
			return $retorno;	
			exit;
			*/
			
			$query  = "exec GERASEQUENCIAL ".strtoupper($table).";";
			$res = sqlsrv_query($bd,$query);
			while(sqlsrv_next_result($res)){ 
				$row = jn_fetch_row($res);
			}
			return  $row[0];
			exit;
		}
		
		if ($_SESSION['type_db'] == 'mysql')
			$res = mysqli_query($conexaoSequencial,$query);
		else
			$res = jn_query($query);
			
			
		$row = jn_fetch_row($res);
		
		if (($row[0] == '') or ($row[0] == null))
		{
			jn_query('insert into cfgsequencias(nome_tabela, sequencia_atual) values(' . aspas($table) . ',100);');
			$row[0] = 100;
		}

		return $row[0];
	}






// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_insert_id( $generator = false ){
		global $conexao;
		global $idMysql;
		global $bd;
		if( $_SESSION['type_db'] == 'mssqlserver' ){
			$res = @mssql_query( 'SELECT @@identity' );;
			$data = mssql_fetch_row( $res );
			return $data[0];
		}elseif( $_SESSION['type_db'] == 'sqlsrv' ){
			$res = sqlsrv_query( $bd,'SELECT @@identity' );;
			$data = jn_fetch_row( $res );
			return $data[0];
		}
		elseif( $_SESSION['type_db'] == 'firebird' ){
			if(!$generator){
				return -1;
			}
			return getGenerator( $generator, 0 );
		}elseif($_SESSION['type_db'] == 'mysql'){
			return $idMysql;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
	function jn_affected_rows($resource){
		global $conexao;
		global $idMysql;
		global $bd;
		if( $_SESSION['type_db'] == 'mssqlserver' ){
			return mssql_rows_affected($resource);
		}elseif( $_SESSION['type_db'] == 'sqlsrv' ){
			return sqlsrv_rows_affected($resource);
		}elseif( $_SESSION['type_db'] == 'firebird' ){
			return ibase_affected_rows();
		}elseif($_SESSION['type_db'] == 'mysql'){
			return mysqli_affected_rows($conexao);
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	// Função que monta a query adequadamente a ser executada
	function montaQueryAPP($queryPrincipal, $nomeTabela) {
		if (getTabelaContemCodigoContrato($nomeTabela))
		{
			$queryAux = explode(' FROM ', $queryPrincipal);
			
			if ((strpos($queryAux[1], ' WHERE ') === false) and (strpos($queryAux[1], ' ORDER BY ')=== false)) // Não tem Clausulas "WHERE", "ORDER BY" e "GROUP BY"
			{
				$queryRetorno = $queryAux[0] . " FROM " . $queryAux[1];
				return $queryRetorno;
			}
			elseif (strpos($queryAux[1], ' WHERE ') == true) // Possui Clausula "WHERE"
			{
				$queryAux2 = explode(' WHERE ', $queryAux[1]);
				$tabela = $queryAux2[0]; // Pode ser o nome da Tabela ou Nome da tabela mais os JOIN's
			}
			else 
			{

			}
			
		}
		else
		{
			echo "não achou nada";
			return $queryPrincipal;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	// Função para retornar complemento do SELECT para validar Codigo Contrato = {parametros = Nome da Tabela a ser consultada e Código de identificação do usuário}
	function getComplSQLCodigoContrato($nomeTabela, $codigoIdentificacao) {
		$nomeTabela = strtoupper($nomeTabela);
		$codigoContratante = getObtemCodigoContratante($codigoIdentificacao);
		$nomeCampoContratante = getObtemNomeCampoContratante($nomeTabela);
		
		try 
		{
			if (getTabelaContemCodigoContrato($nomeTabela)) 
			{
				$complString = " (" . $nomeTabela . "." . $nomeCampoContratante . " = " . $codigoContratante . ") ";
				return $complString;
			}
			else 
			{
				return "";
			}
		}
		catch(Exception $e) 
		{
			header('Content-type: application/json');
			header("Access-Control-Allow-Origin: *");
			$e = '{"Excecao":"ERRO"}';
			echo $e;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	// Função para verificar se existe o campo de contrato na tabela
	function getTabelaContemCodigoContrato($nomeTabela) {		
		$resultQuery = jn_query("SELECT * FROM CFGCAMPOS_SIS_APP WHERE ((NOME_CAMPO = 'CODIGO_CONTRATANTE_NULL') OR (NOME_CAMPO = 'CODIGO_CONTRATANTE')) AND (NOME_TABELA = " . Aspas($nomeTabela) . ")");
		
		if ($objetoResult = jn_fetch_object($resultQuery)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	// Função para obter o código contratante do usuário
	function getObtemCodigoContratante($codigoIdentificacao) {
		$resultQuery = jn_query("SELECT CODIGO_CONTRATANTE_NULL FROM CFGUSUARIOS_APP WHERE CODIGO_USUARIO = " . $codigoIdentificacao);
		
		if ($objetoResult = jn_fetch_object($resultQuery))
		{
			$codigoContratante = $objetoResult->CODIGO_CONTRATANTE_NULL;
			return $codigoContratante;
		}
		else
		{
			return null;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	// Função para retornar nome do campo do contratante = {CODIGO_CONTRATANTE_NULL OR CODIGO_CONTRATANTE}
	function getObtemNomeCampoContratante($nomeTabela) {
		$resultQuery = jn_query("SELECT NOME_CAMPO FROM CFGCAMPOS_SIS_APP WHERE ((NOME_CAMPO = CODIGO_CONTRATANTE_NULL) OR (NOME_CAMPO = CODIGO_CONTRATANTE)) AND (NOME_TABELA = " . $nomeTabela . ")");
		
		if ($objetoResult = jn_fetch_object($resultQuery)) 
		{
			$nomeCampoContratante = $objetoResult->NOME_CAMPO;
			return $nomeCampoContratante;
		}
		else 
		{
			return null;
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function chamarDepoisCall(){
		global $conexao;
		if ($_SESSION['type_db'] == 'mysql')
			mysqli_next_result($conexao);
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_format_time(){
		if( $_SESSION['type_db'] == 'firebird' ){
			/*
			* Para futuras compatibilidades com outros bancos,
			* será efetuada a formatação da data para o padrão SQL ANSI que é: aaaa-mm-dd hh:mm:ss
			*/
			if (version_compare(phpversion(), '5.0.0') === 1) { // se for maior que php 5
				define('IBASE_DATE', 'ibase.dateformat');
				define('IBASE_TIME', 'ibase.timeformat');
				define('IBASE_TIMESTAMP', 'ibase.timestampformat');
		
				/*
				*  no php 5 a funcao ibase_timefmt não existe. A Configuração deve ser feita direto no ini_set.
				* Entao, criamos a simulação para ela...
				*/    
				function ibase_timefmt($format, $where = IBASE_TIMESTAMP){
					ini_set($where, $format);
				}
			}
		
			/**
			* Sets the format of timestamp, date or time type
			* columns returned from queries
			*/
			ibase_timefmt("%Y-%m-%d %H:%M:%S");
		}
		elseif( $_SESSION['type_db'] == 'mssqlserver' ){
			/*Pega qual a linguagem do banco de dados*/
			//$query = 'SET DATEFORMAT ymd;';
			$query = 'SET DATEFORMAT dmy;';
			$res = jn_query($query);
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_MsgErro($link){
		global $conexao;
		
		if( $_SESSION['type_db'] == 'firebird' ){
			return ibase_errmsg();
		}elseif($_SESSION['type_db'] == 'mysql'){
			return mysqli_error($conexao);
		}elseif( $_SESSION['type_db'] == 'mssqlserver' ){
			return msql_error($link);
		}		
	
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_AutoComit($autoComit){
		global $transacao;
		global $conexao;
		global $erroSql;
		
		if($_SESSION['type_db'] == 'mysql'){
			$erroSql = 0;
			$transacao = !$autoComit;
			mysqli_autocommit($conexao, $autoComit);
		}
	}



// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------ //



	function jn_Comit(){
		global $transacao;
		global $conexao;
		global $erroSql;

		if($transacao){
			if ($erroSql == 0){ 
				mysqli_commit($conexao); 
			}else{ 	
				mysqli_rollback($conexao); 
			}
		}
	}


	function jn_GetErroSql(){
		global $msgErroSql;
		return $msgErroSql;
	}
	
	function geraErro409Header($registro){
		header("HTTP/1.0 409 Forbidden");
		echo '{"erro":"'. $registro .'"}';
		exit;
	}
	
	function jn_utf8_encode($string){
		global $naoUtilizarUtf8Encode;
		
		if($naoUtilizarUtf8Encode == true and $string <> ''){		
			return $string;
		}else{
			return utf8_encode($string);
		}
	}
	


	function jn_utf8_encode_AscII($string)
	{
		global $naoUtilizarUtf8Encode;
	
		// Dica do site: http://leandrolisura.com.br/remover-todos-os-caracteres-nao-imprimiveis-em-uma-string/

		$string = filter_var($string, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW|FILTER_FLAG_STRIP_HIGH);
		//pr($string);
	
		if($naoUtilizarUtf8Encode == true and $string <> ''){		
			return $string;
		}else{
			return utf8_encode($string);
		}
	}
	



	function jn_utf8_decode($string){
		global $naoUtilizarUtf8Encode;
		
		if($naoUtilizarUtf8Encode){
			return $string;
		}else{
			return utf8_decode($string);
		}
	}





	function totalRegistrosResult($sqlOriginal){

		$sqlTratado = 'Select Count(*) As QUANTIDADE_REGISTROS ' . copyDelphi($sqlOriginal,strpos(strtoupper($sqlOriginal),strtoupper('from ')),10000);

	    if (strpos(strtoupper($sqlTratado),strtoupper('order by')) !==false)
	        $sqlTratado = copyDelphi($sqlTratado,1,strpos(strtoupper($sqlTratado),strtoupper('order by')) -1);

	    //pr($sqlTratado);

		$resQuantidade = jn_query($sqlTratado,false, true, true);

		if(!$resQuantidade) // Aqui testa se a consulta deu certo, se deu errado eu nào posso tentar ler o dado.
		{
			return -1;
		}
		else
		{
			$rowQuantidade = jn_fetch_object($resQuantidade);
			return $rowQuantidade->QUANTIDADE_REGISTROS;
		}

	}




	function qryUmRegistro($qry){

		$res = jn_query($qry);
		$row = jn_fetch_object($res);

		return $row;

	}

	
?>