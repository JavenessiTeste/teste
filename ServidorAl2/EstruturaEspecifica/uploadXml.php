<?php

require('../lib/base.php');
require('../private/autentica.php');

global $numeroRegistroPS5297;
if(retornaValorConfiguracao('ROTINA_VALIDACAO_XML') == 'XML_INC3')
{
   require('../lib/xml_inc3.php');
}
else
{
   require('../lib/xml_inc2.php');
}


if($_POST['tipo'] =='enviar'){
	$numeroProtocolo = 0;
		
	$mesAnoCompetencia = explode("/",$_POST['mesAnoReferencia']);
	$mesAnoCompetencia = $mesAnoCompetencia[0] . '.' . $mesAnoCompetencia[1];	
	
	$i = 0;
	$erro = array();
	$protocolo = array();
	$erroInicial = false;
	$motivoErroInicial = '';
	
	while($i < 6){
		$i = $i+1;
		$arquivo = isset($_FILES["file" . $i]) ? $_FILES["file" . $i] : FALSE;
		$arquivo_name = $_FILES['file'  . $i]['name'];
		$arquivo_type = $_FILES['file' . $i]['type'];
		
		$ext = explode('.',$arquivo_name);
		$nomeA = $ext[(count($ext)-2)];
		$ext = $ext[(count($ext)-1)];
		$tiposLiberados = explode(';','xml;Xml;XML' );
		$controle_tipos = false;
		
		if(($arquivo) and( count($tiposLiberados) > 0) ){
			foreach($tiposLiberados as $item){
				if(strtolower($item) == strtolower($ext) )
					$controle_tipos = true;
			}
			if(!$controle_tipos)
				$erro['file'.$i] = jn_utf8_encode('Nao sao permitidos arquivos com a extensao "' . strtoupper($ext) . '"');
		}else{
			$controle_tipos = true;
		}
		$arquivo_size = $_FILES["file" . $i]['size'];
		$arquivo_temp_name = $_FILES["file" . $i]['tmp_name'];
		$up = $arquivo['name'];
		
		
		if ((($arquivo))&& ($controle_tipos)){ // Verificamos se a variavel "arquivo" existe (06/01/2011) verifica de o tipo do arquivo é liberado
			if(retornaValorConfiguracao('SEPARAR_ARQ_XML_PREST') == 'SIM'){
				@mkdir("../../xml/EnviadosPelosPrestadores/". $_SESSION['codigoIdentificacao'] . "/", 0777, true);//Cria a pasta do prestador, caso não exista.
				$dir="../../xml/EnviadosPelosPrestadores/". $_SESSION['codigoIdentificacao'] . "/"; //Quando tiver essa configuração, os arquivos ficarão salvos em pastas separadas por prestador.
			}else{
				$dir="../../xml/EnviadosPelosPrestadores/"; //Esse é o diretório onde ficara os arquivos enviados, lembre-se de cria-lo. Este script nao cria diretórios
			}
			
			
			$erro['file'.$i] = false;
			$dados = getDadosXml($arquivo_temp_name);
			
			if (!$dados){
				$erro['file'.$i] = jn_utf8_encode('Este arquivo Xml nao esta valido. Valide seu XML de acordo com os "schemas" XML/TISS preconizados pela ANS') ;
				$erroInicial = true;
				$motivoErroInicial = 'Schema';
			}
			
			if ($dados['msg'] <> '')
				$erro['file'.$i] = jn_utf8_encode($dados['msg']);
			
			if (!$erro['file'.$i]){
				if($dados['versao'] != ''){
					$dadosValidacao = validaXmlTiss($arquivo_temp_name, $dados['versao']);
					if(!$dadosValidacao['validado']){
						$erro['file'.$i] = jn_utf8_encode('Schema invalido:<br\>  '.$dadosValidacao['msg']);
						$erroInicial = true;
						$motivoErroInicial = 'Schema';
					}
						
				}
			}

			if(retornaValorConfiguracao('VERSAO_XML_ACEITA') != '' and retornaValorConfiguracao('VERSAO_XML_ACEITA') > $dados['versao']){
				$erro['file'.$i] = "A operadora nao aceita arquivo XML na versao " . $dados['versao'];				
				$erro['file'.$i] = jn_utf8_encode($erro['file'.$i]);
				$erroInicial = true;
			}
			
			
			if($_SESSION['codigoSmart'] != '3397'){
				if (!$erro['file'.$i]){						
					if (strtoupper($dados['hashArquivo']) != strtoupper($dados['hashCalculado'])){
						$erro['file'.$i] = "Hash do arquivo diferente do hash calculado" . $dados['hashCalculado'];
						$erro['file'.$i] .= " <br> Por favor, verifique se nao contem nenhum caractere especial no arquivo.";					
						$erro['file'.$i] = jn_utf8_encode($erro['file'.$i]);
						$erroInicial = true;
						$motivoErroInicial = 'Hash';
					}elseif($dados['cnpj'] == '' && $dados['codigoPrestadorNaOperadora'] == '' && $dados['cpf'] == ''){						
						$erro['file'.$i] = jn_utf8_encode("Identificacao do prestador nao informada");
						$erroInicial = true;
						$motivoErroInicial = 'Prestador';
					}
				}
			}			
			
			
			$query = "SELECT REPLACE(REPLACE(REPLACE(PS5002.NUMERO_CPF , '.', ''), '/', ''), '-', '' ) AS NUMERO_CPF, REPLACE(REPLACE(REPLACE(PS5002.NUMERO_CNPJ , '.', ''), '/', ''), '-', '' ) AS NUMERO_CNPJ FROM PS5002 WHERE PS5002.CODIGO_PRESTADOR = " . $_SESSION['codigoIdentificacao'] ;
			
			$res = jn_query($query);
			$row = jn_fetch_object($res);
			  
			
			if (!$erro['file'.$i] && $dados['codigoPrestadorNaOperadora'] <> '' ){	
				if ($dados['codigoPrestadorNaOperadora'] <> $_SESSION['codigoIdentificacao']){
					$erroInicial = true;
					$motivoErroInicial = 'Prestador';
					$erro['file'.$i] = jn_utf8_encode("Codigo do prestador nao coresponde ao prestador logado");
				}
			}
			
			if (!$erro['file'.$i] && $dados['cpf'] <> '' ){		
				if ($row->NUMERO_CPF <>  $dados['cpf']){
					$erroInicial = true;
					$motivoErroInicial = 'Prestador';
					$erro['file'.$i] = jn_utf8_encode("CPF nao corresponde ao do prestador logado");
				}
			}
			
			if (!$erro['file'.$i] && $dados['cnpj'] <> '' ){		
				if ($row->NUMERO_CNPJ <>  $dados['cnpj']){
					$erroInicial = true;
					$motivoErroInicial = 'Prestador';
					$erro['file'.$i] = jn_utf8_encode("CNPJ nao corresponde ao do prestador logado. ");
				}
			}
			
		
			$query =  "SELECT count(*) as Arquivo FROM PS5272 WHERE PS5272.NOME_ARQUIVO_UPLOAD = "  . aspas($arquivo_name); 
			$query .= " AND PS5272.CODIGO_PRESTADOR = " . $_SESSION['codigoIdentificacao'] ;
			
			$res = jn_query($query);
			$row = jn_fetch_object($res);
						
			if (!$erro['file'.$i] && ($row->ARQUIVO <> 0) ){
				$erro['file'.$i] = jn_utf8_encode("Arquivo ja enviado a operadora");
			}
			
			$queryEmpresa = 'Select codigo_smart from cfgempresa';
			$resEmpresa = jn_query($queryEmpresa);
			$rowEmpresa = jn_fetch_object($resEmpresa);	
			
			
			if(retornaValorConfiguracao('VALIDAR_NOME_ARQ_XML') == '' || retornaValorConfiguracao('VALIDAR_NOME_ARQ_XML') != 'NAO'){			
				if (!$erro['file'.$i]){	
				
					$nomeA = explode('_',$nomeA);
				
					if(count($nomeA) <> 2 ){
						$erro['file'.$i] = jn_utf8_encode("Nome do arquivo fora do padrao<br> Padrao: [sequencial da transacao, com 20 caracteres]_[código hash com 32 caracteres].xml");	
					}else{
						if (strcasecmp($nomeA[0], str_pad($dados['sequencialTransacao'], 20, "0", STR_PAD_LEFT)) <> 0 or  strcasecmp($nomeA[1], $dados['hashCalculado']) <> 0)
							$erro['file'.$i] = jn_utf8_encode("Nome do arquivo fora do padrao<br> Padrao:  [sequencial da transacao, com 20 caracteres]_[código hash com 32 caracteres].xml");	
					}
				}
			}
			
			if($dados['erroEncontrado'] != '' && $erro['file'.$i] == '' && retornaValorConfiguracao('DASHBOARD_VALIDADOR_XML') != 'SIM'){
				$erro['file'.$i] = jn_utf8_encode("O arquivo contem erros. Por favor, verifique a validacao no menu 'Arquivos Rejeitados' ");
			}elseif(retornaValorConfiguracao('DASHBOARD_VALIDADOR_XML') == 'SIM' and $erroInicial == true){								
				if($motivoErroInicial == 'Hash'){
					$erro['file'.$i] = "Hash do arquivo diferente do hash calculado" . $dados['hashCalculado'];
				}elseif($motivoErroInicial == 'Schema'){
					jn_utf8_encode('Este arquivo Xml nao esta valido. Valide seu XML de acordo com os "schemas" XML/TISS preconizados pela ANS');
				}elseif($motivoErroInicial == 'Prestador'){
					jn_utf8_encode('As informacoes do prestador no Arquivo XML nao estao corretas ou nao sao do prestador logado. ');
				}

				jn_query('DELETE FROM PS5298 WHERE NUMERO_REGISTRO_PS5297 IN (SELECT PS5297.NUMERO_REGISTRO FROM PS5297 WHERE PS5297.NUMERO_REGISTRO =' . $numeroRegistroPS5297 . ')');
				jn_query('DELETE FROM PS5297 WHERE PS5297.NUMERO_REGISTRO =' . $numeroRegistroPS5297);
			}
			
			if ((!$erro['file'.$i]) and (!$dados['erroEncontrado'])){
				if (move_uploaded_file($arquivo_temp_name, $dir.$up)) {

					chmod($dir.$up, 0777);

					$DataAtual = time();
					$mesAnoCompetencia = $_POST['mesAnoReferencia'];
					
					$Upload = array(
						'CodigoPrestador'   => $UsuarioLogado['CODIGO'],
						'NomeArquivo'       => $arquivo_name,
						'DataUpload'        => date('Y-m-d', $DataAtual),
						'HoraUpload'        => date('H:i', $DataAtual),
						'MesAnoCompetencia' => $mesAnoCompetencia,
						'NumeroProtocolo'   => date('Ymd', $DataAtual),

					);				
					
					$varNumeroProtocolo = '';
					$numeroRand1 = '';					
					$numeroRand2 = '';					
					$numeroRegistro = '';
					$protocoloTemp = '';
					
					if(($_SESSION['type_db'] != 'mssqlserver') and ($_SESSION['type_db'] != 'sqlsrv') AND (retornaValorConfiguracao('NUMERO_REGISTRO_AUT_XML') != 'SIM')){
						$numeroRegistro = jn_gerasequencial('PS5272');
					}else{
						$numeroRand1 = rand(0,100);
						$numeroRand2 = rand(101,200);
					}
					
					if(($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv') or (retornaValorConfiguracao('NUMERO_REGISTRO_AUT_XML') != 'SIM')){
						$protocoloTemp = $_SESSION['codigoIdentificacao'] . $numeroRand1 . $numeroRand2;
					}
					
					$query  = 'INSERT INTO ';
					$query .= 'Ps5272 ';
					if(($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv') or (retornaValorConfiguracao('NUMERO_REGISTRO_AUT_XML') == 'SIM')){
						$query .= '(CODIGO_PRESTADOR, NOME_ARQUIVO_UPLOAD, NUMERO_PROTOCOLO, ';					
					}else{						
						$query .= '(NUMERO_REGISTRO, CODIGO_PRESTADOR, NOME_ARQUIVO_UPLOAD, ';
					}
					$query .= 'MES_ANO_COMPETENCIA, DATA_UPLOAD, HORA_UPLOAD)';
					$query .= 'VALUES (';
					
					if(($_SESSION['type_db'] != 'mssqlserver') and ($_SESSION['type_db'] != 'sqlsrv') AND (retornaValorConfiguracao('NUMERO_REGISTRO_AUT_XML') != 'SIM')){
						$query .= $numeroRegistro . ', ';
					}
					
					$query .= $Upload['CodigoPrestador'] . ', ';

					$query .= aspas($Upload['NomeArquivo']) . ', ';
					
					if(($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv') or (retornaValorConfiguracao('NUMERO_REGISTRO_AUT_XML') == 'SIM')){
						$query .= aspas($protocoloTemp) . ', ';
					}
					
					$query .= aspas($Upload['MesAnoCompetencia']) . ', ';
					$query .= aspas($Upload['DataUpload']) . ', ';

					$query .= aspas($Upload['HoraUpload']);
					$query .= ')';

					if ( $res = jn_query($query) ) {
								
						if(($_SESSION['type_db'] != 'mssqlserver') and ($_SESSION['type_db'] != 'sqlsrv')){							
							if(retornaValorConfiguracao('NUMERO_REGISTRO_AUT_XML') == 'SIM'){								
								$queryProt = 'SELECT NUMERO_REGISTRO FROM PS5272 WHERE NUMERO_PROTOCOLO = ' . aspas($protocoloTemp);
								$resProt = jn_query($queryProt);
								$rowProt = jn_fetch_object($resProt);
								$numeroRegistro = $rowProt->NUMERO_REGISTRO;							
							}
							
							$Upload['NumeroProtocolo'] .=  $numeroRegistro;
							$varNumeroProtocolo = $Upload['NumeroProtocolo'];
							
							$query  = 'UPDATE Ps5272 SET Numero_Protocolo = ' . aspas($varNumeroProtocolo);

							if(retornaValorConfiguracao('DASHBOARD_VALIDADOR_XML') == 'SIM')
								$query .= ' , NUMERO_REGISTRO_PS5297 = ' . aspas($numeroRegistroPS5297);

							$query .= ' WHERE (1 = 1) ';
							$query .= 'AND Codigo_Prestador = ' . $Upload['CodigoPrestador'] . ' ';
							$query .= 'AND Data_Upload = ' . aspas($Upload['DataUpload']) . ' ';
							$query .= 'AND Hora_Upload = ' . aspas($Upload['HoraUpload']) . ' ';
							$query .= 'AND Numero_Registro = ' . aspas($numeroRegistro) . ' ';							
						}else{
							$queryProt = 'SELECT NUMERO_REGISTRO FROM PS5272 WHERE NUMERO_PROTOCOLO = ' . aspas($protocoloTemp);
							$resProt = jn_query($queryProt);
							$rowProt = jn_fetch_object($resProt);
							$registro = $rowProt->NUMERO_REGISTRO;
							
							$varNumeroProtocolo = $Upload['NumeroProtocolo'] . $registro;
							
							$query  = 'UPDATE Ps5272 SET Numero_Protocolo = ' . aspas($varNumeroProtocolo);

							if(retornaValorConfiguracao('DASHBOARD_VALIDADOR_XML') == 'SIM')
								$query .= ' , NUMERO_REGISTRO_PS5297 = ' . aspas($numeroRegistroPS5297);
								
							$query .= ' WHERE (1 = 1) ';
							$query .= 'AND Codigo_Prestador = ' . $Upload['CodigoPrestador'] . ' ';
							$query .= 'AND Data_Upload = ' . aspas($Upload['DataUpload']) . ' ';
							$query .= 'AND Hora_Upload = ' . aspas($Upload['HoraUpload']) . ' ';
							$query .= 'AND NUMERO_PROTOCOLO = ' . aspas($protocoloTemp) . ' ';							
						}
						
						if ( $res = jn_query($query) ) {
							$protocolo['file'.$i] =  $varNumeroProtocolo;
						}
					}
				}
				else {
					$erro['file'.$i]= "erro no envio"; // Caso ocorra algum erro, imprimi na tela "erro"
				}
			}else{
				@unlink($arquivo_temp_name);
			}
			
		}		
		
		
	}
	$retorno['ERRO'] = $erro;
	$retorno['PROTOCOLO'] = $protocolo;	
	echo json_encode($retorno);

}

if($dadosInput['tipo'] =='configuracoes'){
	$retorno['QUANTIDADE_ANEXOS'] = retornaValorConfiguracao('QUANTIDADE_ANEXOS');
	$retorno['TIPO_ALERTA_PROTOCOLO'] = retornaValorConfiguracao('TIPO_ALERTA_PROTOCOLO');
	$retorno['LINK_PROTOCOLO_XML'] = retornaValorConfiguracao('LINK_PROTOCOLO_XML');
	$retorno['DASHBOARD_VALIDADOR_XML'] = retornaValorConfiguracao('DASHBOARD_VALIDADOR_XML');
	
	echo json_encode($retorno);
}

?>