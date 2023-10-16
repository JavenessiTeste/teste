<?php


function depoisGravacao($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,&$campos,&$retorno){
//$tipo = INC ALT EXC 
//campos apenas para INC ALT
//$retorno['STATUS'] = 'OK';
//$retorno['STATUS'] = 'ERRO'; para processo
//$retorno['MSG']    = ''; mensagem que ira aparecer operador quando der erro.
//$retorno['MSG']    = jn_utf8_encode('usar esssa função quando tiver acentuação');

	//pr($chave);
	//pr($nomeChave);
	
	if(($retorno['STATUS'] == 'ERRO')and($tipo == 'EXC')and($tabela=='XXX')){
		$retorno['MSG']    = 'Deu erro';
	}


	if ($_SESSION['AliancaPx4Net']=='S') // Se for o ERP AliancaPx4Net
	{

		if ($tipo!='EXC') // não pode entrar se for exclusão, pq há tratamentos especificos na rotina de exclusão para tratar as msgs de retorno para o usuário.
		{		
		  	require_once('../EstruturaPx/depoisDaGravacao_ERPPx.php');
		  	$retorno = depoisGravacao_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,$campos,$retorno);
		}

	}
	else // então não é o ERP AliancaPX4Net, é o portal
	{

			// Se for alguma tabela que precisa registrar protocolo
			if (($tipo == 'INC') and 
				   (($tabela=='PS1063') or ($tabela=='PS1095') or ($tabela=='PS6550') or 	
					($tabela=='PS6400') or ($tabela=='PS6110') or ($tabela=='PS6120') or 	
					($tabela=='PS6360') or ($tabela=='PS6120') or ($tabela=='TMP1000_NET') or 
					($tabela=='ESP_REEMBOLSO') or ($tabela=='ESP_AGENDAMENTO_CIRURGICO'))
				)
			{
								
					for($i=0;$i<count($campos);$i++)
					{
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO')
						{
							$codigoAssociado = $campos[$i]['VALOR'];
						}
					}

					if ($_SESSION['NUMERO_PROTOCOLO_ATIVO']!='') 
						$numeroProtocoloGeral = $_SESSION['NUMERO_PROTOCOLO_ATIVO'];
					else 
					    $numeroProtocoloGeral = GeraProtocoloGeralPs6450($codigoAssociado, '');			
					
					$sql                  = 'update ' . $tabela . ' set PROTOCOLO_GERAL_PS6450 = ' . aspas($numeroProtocoloGeral) . ' where ' . $nomeChave . ' = ' . aspas($chave);
					jn_query($sql);
					
					if($tabela == 'PS1095' && $_SESSION['codigoSmart'] == '3389'){
						$retorno['MSG']        = ' <div align="justify"><font color="green">Sua solicitação foi enviada com sucesso</font> <br>';
						$retorno['MSG']       .= ' <font color="green">Número do Protocolo: ' . substr($numeroProtocoloGeral,0,6) . '.' . substr($numeroProtocoloGeral,6,8) . '.' . substr($numeroProtocoloGeral,14,6) . ' </font> <br>';
						$retorno['MSG']       .= ' <font color="red"><b> Atenção! </b></font> A presente solicitação não efetiva o cancelamento  <br>';
						$retorno['MSG']       .= ' automaticamente, portanto caso não receba a confirmação em até <br> ';
						$retorno['MSG']       .= ' 72h, entre em contato com a nossa central de atendimento no <br> ';
						$retorno['MSG']       .= ' telefone <font color="green"><b> (11) 3113-1717 </b></font> ou pelo e-mail <font color="green"><b> cancelamento@vidamax.com.br </b></font>. </div>';
					}else{				
						$retorno['MSG']       = 'Seu chamado gerou o protocolo número: ' . substr($numeroProtocoloGeral,0,6) . '.' . substr($numeroProtocoloGeral,6,8) . '.' . substr($numeroProtocoloGeral,14,6);
					}
					
			}			
			
			if ($tabelaOrigem=='VND1000_ON')
			{
				
					for($i=0;$i<count($campos);$i++)
					{
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO')
						{
							$codigoAssociado = $campos[$i]['VALOR'];
						}
					}
				
					$queryCfg  = ' SELECT VND1030CONFIG_ON.TABELA_PRECO_AUTOC, VND1000_ON.CODIGO_TABELA_PRECO FROM VND1000_ON ';
					$queryCfg .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
					$queryCfg .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);

					$resCfg = jn_query($queryCfg);

					if($rowCfg = jn_fetch_object($resCfg))
					{				
						if ($tipo == 'ALT' && $rowCfg->TABELA_PRECO_AUTOC != '' && $rowCfg->CODIGO_TABELA_PRECO == ''){
						    jn_query('UPDATE VND1000_ON SET CODIGO_TABELA_PRECO = ' . aspas($rowCfg->TABELA_PRECO_AUTOC) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado));
						}else if($rowCfg->TABELA_PRECO_AUTOC != ''){
						    jn_query('UPDATE VND1000_ON SET CODIGO_TABELA_PRECO = ' . aspas($rowCfg->TABELA_PRECO_AUTOC) . ' WHERE CODIGO_TABELA_PRECO is null and CODIGO_ASSOCIADO = ' . aspas($codigoAssociado));
						}
							
					}
			}
			
			if ($tabelaOrigem=='PS2510')
			{
				$query = 'SELECT FACES FROM PS2510 WHERE NUMERO_REGISTRO = ' . aspas($chave);
				$res = jn_query($query);
				$row = jn_fetch_object($res);
				
				if($row->FACES){
					
					jn_query('DELETE FROM PS2511 WHERE NUMERO_REGISTRO_PS2510 = ' . aspas($chave));	
					
					$facesMarcadas = substr($row->FACES, 1);
					$facesMarcadas = explode(';',$facesMarcadas);
					foreach($facesMarcadas as $face){
						$face = explode('-',$face);
						jn_query('INSERT INTO PS2511 (NUMERO_REGISTRO_PS2510, FACE_DENTE) VALUES (' . aspas($chave) . ',' . aspas($face[0]) .')');			
					}
				}
				
				if($tipo == 'INC'){
					if(retornaValorConfiguracao('INSERIR_PACOTE_2510') == 'SIM'){	
						$queryProcedimento = 'Select CODIGO_PROCEDIMENTO,FACES from ps2510 where numero_registro ='.aspas($chave);
						$resProcedimento = jn_query($queryProcedimento);

						if($rowProcedimento = jn_fetch_object($resProcedimento)){
							$queryPacote = "select * from ps2211 where CODIGO_PROCEDIMENTO_PACOTE =". aspas($rowProcedimento->CODIGO_PROCEDIMENTO);
							$resPacote = jn_query($queryPacote);

							while($rowPacote = jn_fetch_object($resPacote)){
								$sqlInsertPacote = 'Insert into Ps2510(NUMERO_PLANO_TRATAMENTO, NUMERO_DENTE_SEGMENTO, CODIGO_PROCEDIMENTO, CODIGO_PRESTADOR, DATA_PROCEDIMENTO, QUANTIDADE_PROCEDIMENTOS, NUMERO_ATENDIMENTO, VALOR_COPARTICIPACAO, SITUACAO, DATA_CANCELAMENTO, DATA_CONCLUSAO_PROCEDIMENTO, CODIGO_PROCEDIMENTO_PACOTE, VALOR_ESTIMATIVA_CUSTO, NUMERO_AUTORIZACAO, DATA_AUTORIZACAO, CODIGO_OPERADOR, CODIGO_PERITO, INFORMACOES_LOG_I, INFORMACOES_LOG_A, FACES)
														select    NUMERO_PLANO_TRATAMENTO, NUMERO_DENTE_SEGMENTO, '.aspas($rowPacote->CODIGO_PROCEDIMENTO_CONTIDO).', CODIGO_PRESTADOR, DATA_PROCEDIMENTO, QUANTIDADE_PROCEDIMENTOS, NUMERO_ATENDIMENTO, VALOR_COPARTICIPACAO, SITUACAO, DATA_CANCELAMENTO, DATA_CONCLUSAO_PROCEDIMENTO, CODIGO_PROCEDIMENTO_PACOTE, VALOR_ESTIMATIVA_CUSTO, NUMERO_AUTORIZACAO, DATA_AUTORIZACAO, CODIGO_OPERADOR, CODIGO_PERITO, INFORMACOES_LOG_I, INFORMACOES_LOG_A, FACES from ps2510 where numero_registro = '.aspas($chave).' ';
								$resInsertPacote = jn_query($sqlInsertPacote);
								$idGerado = jn_insert_id();
								if($row->FACES){
									$facesMarcadas = substr($rowProcedimento->FACES, 1);
									$facesMarcadas = explode(';',$facesMarcadas);
									foreach($facesMarcadas as $face){
										$face = explode('-',$face);
										jn_query('INSERT INTO PS2511 (NUMERO_REGISTRO_PS2510, FACE_DENTE) VALUES (' . aspas($idGerado) . ',' . aspas($face[0]) .')');			
									}
								}
							}
						}
					}
					
					
				}
			}
			
			if ($tabela=='PS1095' and $tipo == 'INC' and $_SESSION['codigoSmart'] == '3389'){//Vidamax			
				
				$codigoAssociado = '';
				$enderecoEmail = '';		
				$numeroRegistro = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$numeroRegistro = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='EMAIL'){
						$enderecoEmail = $campos[$i]['VALOR'];
					}
				}
				
				$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);
				
				$queryProtocolo = 'SELECT PROTOCOLO_GERAL_PS6450 FROM PS1095 WHERE NUMERO_REGISTRO = ' . aspas($numeroRegistro);
				$resProtocolo = jn_query($queryProtocolo);
				$rowProtocolo = jn_fetch_object($resProtocolo);
				
				$assunto = 'Solicitacao de Cancelamento';
				
				$corpoEmail  = '<html lang="pt-br"> ';
				$corpoEmail .= '	<br><br> ';
				$corpoEmail .= '	<body> ';
				$corpoEmail .= '		<img src="data:image/png;base64, ' . convertImagemBase64($numeroRegistro,'PS1095')  . '"  /> ';
				$corpoEmail .= '	</body> ';
				$corpoEmail .= '</html> ';
				
				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
				
				$enderecoEmail = 'cancelamento@vidamax.com.br';		
				$corpoEmail  = ' Numero Protocolo: ' . $rowProtocolo->PROTOCOLO_GERAL_PS6450 . ' <br>';
				$corpoEmail .= ' Data Solicitacao: '. date('d/m/Y') . ', as ' . date('H:i') . '<br>';		
				$corpoEmail .= ' Nome do Beneficiario ' . $rowAssoc->NOME_ASSOCIADO;		
				
				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
				
			}
			
			
			if ($tabela=='PS6110' and $tipo == 'INC' and $_SESSION['codigoSmart'] == '4055'){//Sintimmmeb			
				
				$codigoAssociado = '';
				$descRecSug = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='DESCRICAO_RECLAMACAO_SUGESTAO'){
						$descRecSug = $campos[$i]['VALOR'];
					}
				}
				
				$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);
						
				$assunto = 'Novo registro de Reclamacao/Sugestao';		
				$enderecoEmail = 'ouvidoria@sintimmmebsaude.com.br';		
				
				$corpoEmail  = ' Novo registro de Reclamacao/Sugestao <br> ';
				$corpoEmail .= " Associado: " . $codigoAssociado . ' - ' . $rowAssoc->NOME_ASSOCIADO . " <br> ";
				$corpoEmail .= " Assunto: " . $descRecSug . " <br> ";
				
				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
				
			}
			
			if ($tabela=='PS6120' and retornaValorConfiguracao('ENDERECO_EMAIL_OUVIDORIA') != ''){
				
				$codigoAssociado = '';
				$registro = '';		
				$emailAssociado = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$registro = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='EMAIL'){
						$emailAssociado = $campos[$i]['VALOR'];
					}
				}
				
				$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);
						
				$queryProtocolo = 'SELECT PROTOCOLO_GERAL_PS6450, DESCRICAO_OCORRENCIA, RETORNO_OPERADORA FROM PS6120 WHERE NUMERO_REGISTRO = ' . aspas($registro);
				$resProtocolo = jn_query($queryProtocolo);
				$rowProtocolo = jn_fetch_object($resProtocolo);
				
				$assunto = '';
				if($tipo == 'INC'){
					$assunto = 'Novo registro de Ocorrencia';
				}else{
					$assunto = 'Alteracao na Ocorrencia';
				}
				
				$enderecoEmail = retornaValorConfiguracao('ENDERECO_EMAIL_OUVIDORIA');
				
				$corpoEmail  = ' Novo registro de Ocorrencia <br> ';
				$corpoEmail .= " Associado: " . $codigoAssociado . ' - ' . $rowAssoc->NOME_ASSOCIADO . " <br> ";
				$corpoEmail .= " Assunto: " . $rowProtocolo->DESCRICAO_OCORRENCIA . " <br> ";
				$corpoEmail .= " Protocolo: " . $rowProtocolo->PROTOCOLO_GERAL_PS6450 . " <br> ";
				
				if($tipo == 'ALT')
					$corpoEmail .= " Retorno: " . $rowProtocolo->RETORNO_OPERADORA . " <br> ";

				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail, retornaValorConfiguracao('ENDERECO_EMAIL_OUVIDORIA'));
				disparaEmailFunc($emailAssociado, $assunto, $corpoEmail, retornaValorConfiguracao('ENDERECO_EMAIL_OUVIDORIA'));
				
			}
			

			if ($tabela=='PS1063' and $tipo == 'INC' and retornaValorConfiguracao('ENDERECO_EMAIL_PORTABILIDADE') != ''){
				
				$codigoAssociado = '';
				$registro = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$registro = $campos[$i]['VALOR'];
					}
				}
				
				$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);
				
				$queryProtocolo = 'SELECT PROTOCOLO_GERAL_PS6450 FROM PS1063 WHERE NUMERO_REGISTRO = ' . aspas($registro);
				$resProtocolo = jn_query($queryProtocolo);
				$rowProtocolo = jn_fetch_object($resProtocolo);
				
				$assunto = 'Novo registro de Portabilidade';
				$enderecoEmail = retornaValorConfiguracao('ENDERECO_EMAIL_PORTABILIDADE');
				
				$corpoEmail  = ' Novo registro de Portabilidade <br> ';
				$corpoEmail .= " Associado: " . $codigoAssociado . ' - ' . $rowAssoc->NOME_ASSOCIADO . " <br> ";
				$corpoEmail .= " Protocolo: " . $rowProtocolo->PROTOCOLO_GERAL_PS6450 . " <br> ";
				
				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
				
			}
			
			if ($tabela=='PS6110' and $tipo == 'INC' and retornaValorConfiguracao('ENDERECO_EMAIL_SUGESTOES_CONT') != ''){
				
				$codigoAssociado = '';
				$descRecSug = '';		
				$emailInformado = '';		
				$registro = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='DESCRICAO_RECLAMACAO_SUGESTAO'){
						$descRecSug = jn_utf8_encode($campos[$i]['VALOR']);
					}
					
					if($campos[$i]['CAMPO']=='EMAIL_CONTATO'){
						$emailInformado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$registro = $campos[$i]['VALOR'];
					}
					
				}
				
				$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);

				$queryProtocolo = 'SELECT PROTOCOLO_GERAL_PS6450 FROM PS6110 WHERE NUMERO_REGISTRO = ' . aspas($registro);
				$resProtocolo = jn_query($queryProtocolo);
				$rowProtocolo = jn_fetch_object($resProtocolo);
				
				$assunto = 'Novo registro de Reclamacao/Sugestao';		
				$enderecoEmail = retornaValorConfiguracao('ENDERECO_EMAIL_SUGESTOES_CONT');				
				
				$corpoEmail  = ' Novo registro de Reclamacao/Sugestao <br> ';
				$corpoEmail .= " Associado: " . $codigoAssociado . ' - ' . $rowAssoc->NOME_ASSOCIADO . " <br> ";
				$corpoEmail .= " Assunto: " . $descRecSug . " <br> ";
				$corpoEmail .= " Protocolo: " . $rowProtocolo->PROTOCOLO_GERAL_PS6450 . " <br> ";
				
				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
				disparaEmailFunc($emailInformado, $assunto, $corpoEmail);
			}
			
			
			if ($tabela=='TMP1000_NET' and $tipo == 'INC' and retornaValorConfiguracao('ENVIAR_EMAIL_EMPRESA') == 'SIM'){
				
				$codigoAssociado = '';
				$codigoEmpresa = '';
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='CODIGO_EMPRESA'){
						$codigoEmpresa = $campos[$i]['VALOR'];
					}
				}
				
				$queryAssoc = 'SELECT NOME_ASSOCIADO, PROTOCOLO_GERAL_PS6450, DATA_NASCIMENTO, NUMERO_CPF FROM TMP1000_NET WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);
				
				$queryEmpresa = 'SELECT ENDERECO_EMAIL FROM PS1001 WHERE CODIGO_EMPRESA = ' . aspas($codigoEmpresa);
				$resEmpresa = jn_query($queryEmpresa);
				$rowEmpresa = jn_fetch_object($resEmpresa);
				$enderecoEmail = $rowEmpresa->ENDERECO_EMAIL;
				
				$assunto = 'Novo associado cadastro';
				
				$corpoEmail  = ' Registro de novo associado cadastrado <br> ';
				$corpoEmail .= " Associado: " . $codigoAssociado . ' - ' . $rowAssoc->NOME_ASSOCIADO . " <br> ";
				$corpoEmail .= " Data Nascimento: " . SqlToData($rowAssoc->DATA_NASCIMENTO) . " <br> ";
				$corpoEmail .= " CPF: " . $rowAssoc->NUMERO_CPF . " <br> ";
				$corpoEmail .= " Protocolo: " . $rowAssoc->PROTOCOLO_GERAL_PS6450 . " <br> ";
				
				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
				
			}
			
			if ($tabela=='PS5750' and $tipo == 'INC'){
				
				$numeroRegistro = '';		
				$codigoAssociado = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$numeroRegistro = $campos[$i]['VALOR'];
					}				
				}
				
				$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);
				
				jn_query('UPDATE PS5750 SET NOME_PESSOA = ' . aspas($rowAssoc->NOME_ASSOCIADO) . ' WHERE NUMERO_REGISTRO = ' . aspas($numeroRegistro));
				
			}
			
			if ($tabela=='PS6550' and $tipo == 'INC'){
				
				$numeroSolicitacao = '';		
				$fonteDado = '';
				
				for($i=0;$i<count($campos);$i++)
				{		
					if($campos[$i]['CAMPO']=='NUMERO_SOLICITACAO'){
						$numeroSolicitacao = $campos[$i]['VALOR'];
					}				
				}
				
				if($_SESSION['codigoSmart'] == '3423'){
					if($_SESSION['AUDITOR'] == 'S'){
						$fonteDado = 'AUDITOR';		
					}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){
						$fonteDado = 'CREDENCIADO';		
					}else{
						$fonteDado = $_SESSION['perfilOperador'];		
					}
				}else{
					$fonteDado = 'WEB';		
				}
				
				jn_query('UPDATE PS6550 SET FONTE_DADO = ' . aspas($fonteDado) . ' WHERE NUMERO_SOLICITACAO = ' . aspas($numeroSolicitacao));
				
			}
			
			if ($tabela=='TMP1000_NET' and $tipo == 'INC' and retornaValorConfiguracao('PLANO_DEP_IGUAL_TIT') == 'SIM'){
				
				$codigoAssociado = '';		
				$tipoAssociado = '';
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='TIPO_ASSOCIADO'){
						$tipoAssociado = $campos[$i]['VALOR'];
					}
				}
				
				if($tipoAssociado == 'D'){				
					$queryPlanoTit  = ' SELECT COALESCE(TT.CODIGO_PLANO, TP.CODIGO_PLANO) AS CODIGO_PLANO_TIT FROM TMP1000_NET D ';
					$queryPlanoTit .= ' LEFT OUTER JOIN TMP1000_NET TT ON (D.CODIGO_TITULAR = TT.CODIGO_ASSOCIADO) ';
					$queryPlanoTit .= ' LEFT OUTER JOIN PS1000 TP ON (D.CODIGO_TITULAR = TP.CODIGO_ASSOCIADO) ';
					$queryPlanoTit .= ' WHERE D.TIPO_ASSOCIADO ="D" AND D.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
					$resPlanoTit  = jn_query($queryPlanoTit);
					if($rowPlanoTit = jn_fetch_object($resPlanoTit)){
						jn_query('UPDATE TMP1000_NET SET CODIGO_PLANO = ' . aspas($rowPlanoTit->CODIGO_PLANO_TIT) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado));
					}
					
					validaParametrosPS1059($codigoAssociado);
				}
			}
			

			if (($tabela=='TMP1000_NET') and $tipo == 'INC' and retornaValorConfiguracao('PERMITIR_VIGENCIA_IMEDIATA') == 'SIM')
			{

				$codigoAssociado      = '';		
				$codigoTitular        = '';		
				$flagVigenciaImediata = 'N';
				$tipoAssociado        = '';

				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='CODIGO_TITULAR'){
						$codigoTitular = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='FLAG_VIGENCIA_IMEDIATA'){
						$flagVigenciaImediata = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='TIPO_ASSOCIADO'){
						$tipoAssociado = $campos[$i]['VALOR'];
					}

				}

				if ($flagVigenciaImediata=='S')
				{
					$codAssocPS1000 = migraBeneficiarioTmp1000ToPs1000($codigoAssociado, $tipoAssociado,$codigoTitular);

					$numeroProtocoloGeral = GeraProtocoloGeralPs6450($codAssocPS1000, '');			
					
					$sql = 'update PS1000 set PROTOCOLO_GERAL_PS6450 = ' . aspas($numeroProtocoloGeral) . ' where CODIGO_ASSOCIADO = ' . aspas($codAssocPS1000);
					jn_query($sql);
					
					$retorno['MSG'] = 'Você marcou a opção de [Vigência imediata], neste caso o beneficiário já está com cadastro em vigência e a primeira mensalidade do beneficiário será dobrada. Protocolo: ' . $numeroProtocoloGeral;		
				}

			}



			if ($tabela=='PS6500' and $tipo == 'INC')
			{
				$numeroAutorizacao = '';
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='NUMERO_AUTORIZACAO')
					{
						$numeroAutorizacao = $campos[$i]['VALOR'];
					}		
				}
				
				if($numeroAutorizacao != ''){			
					jn_query('UPDATE PS6500 SET 
									NUMERO_SENHA_AUTORIZ = ' . aspas($numeroAutorizacao) . ', 
									DESCRICAO_OBSERVACAO = ' . aspas('AUTORIZACAO CADASTRADA PELO AL2') . ',
									HORARIO_AUTORIZACAO = ' . aspas(date('H:i')) . ' 
								WHERE NUMERO_AUTORIZACAO = ' . aspas($numeroAutorizacao));
				}

				if($_SESSION['codigoSmart'] == '3419'){
					$queryEsp = 'Select ESTADO_CONSELHO_CLASSE,NUMERO_CONSELHO_CLASSE,CODIGO_CONSELHO_PROFISS,NOME_PRESTADOR_SOLICITANTE,CODIGO_PROFISSIONAL_SOLIC from ps6500 where NUMERO_AUTORIZACAO='.aspas($numeroAutorizacao);
					$resEsp  = jn_query($queryEsp);
					if($rowEsp = jn_fetch_object($resEsp)){
						if($rowEsp->CODIGO_PROFISSIONAL_SOLIC =='' AND $rowEsp->NOME_PRESTADOR_SOLICITANTE != ''){
							$insert = 'insert into esp_profissional(NOME_PROFISSIONAL,NUMERO_CONSELHO_CLASSE,ESTADO_CONSELHO_CLASSE,CODIGO_CONSELHO_PROFISS)VALUES(
							'.aspas($rowEsp->NOME_PRESTADOR_SOLICITANTE).','.aspas($rowEsp->NUMERO_CONSELHO_CLASSE).','.aspas($rowEsp->ESTADO_CONSELHO_CLASSE).','.aspas($rowEsp->CODIGO_CONSELHO_PROFISS).'
							)'; 
							jn_query($insert);
						}
					}
				}
				
				if(retornaValorCFG0003('CALCULA_COPART_AUT_WEB')=='SIM'){
					$valor = 0;
					for($i=0;$i<count($campos);$i++){		
						if($campos[$i]['CAMPO']=='TIPO_GUIA'){
							$tipoGuia = $campos[$i]['VALOR'];
						}
						if($campos[$i]['CAMPO']=='NUMERO_AUTORIZACAO'){
							$numeroAutorizacao = $campos[$i]['VALOR'];
						}
						if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
							$codigoAssociado = $campos[$i]['VALOR'];
						}
						if($campos[$i]['CAMPO']=='CODIGO_PRESTADOR'){
							$codigoPrestador = $campos[$i]['VALOR'];
						}
						
					}	
					if($tipoGuia=='C'){
						require_once('../EstruturaEspecifica/coparticipacaoDana.php');
						$valor = geraCoparticitacaoDana($codigoAssociado,$tipoGuia,'10101012',$codigoPrestador,$numeroAutorizacao);
					}
					jn_query('UPDATE PS6500 SET 
									VALOR_TOTAL_COPARTICIPACAO = ' . aspas($valor) . ', 
									FLAG_COPART_AUT = ' . aspas('S') . '
								WHERE NUMERO_AUTORIZACAO = ' . aspas($numeroAutorizacao));
					if($valor>0){
						$retorno['MSG'] = 'Gerado uma coparticipação de '.$valor;
					}		
				}
				
			
			}
			
			if ($tabela=='PS6510' ){
				if(retornaValorCFG0003('CALCULA_COPART_AUT_WEB')=='SIM'){
					$valor = 0;
					for($i=0;$i<count($campos);$i++){		
						if($campos[$i]['CAMPO']=='NUMERO_AUTORIZACAO'){
							$numeroAutorizacao = $campos[$i]['VALOR'];
						}
						if($campos[$i]['CAMPO']=='CODIGO_PROCEDIMENTO'){
							$procedimento = $campos[$i]['VALOR'];
						}
						
					}

					$queryAut = 'Select CODIGO_PRESTADOR,CODIGO_ASSOCIADO,TIPO_GUIA from ps6500 where NUMERO_AUTORIZACAO='.aspas($numeroAutorizacao);
					$resAut  = jn_query($queryAut);
					$rowAut = jn_fetch_object($resAut);
								
					require_once('../EstruturaEspecifica/coparticipacaoDana.php');
					$valor = geraCoparticitacaoDana($rowAut->CODIGO_ASSOCIADO,$rowAut->TIPO_GUIA,$procedimento,$rowAut->CODIGO_PRESTADOR,$numeroAutorizacao);
					
					jn_query('UPDATE PS6510 SET 
									VALOR_COPARTICIPACAO = ' . aspas($valor) . ' 
								WHERE NUMERO_AUTORIZACAO = ' . aspas($numeroAutorizacao));
					if($valor>0){
						$retorno['MSG'] = 'Gerado uma coparticipação de '.$valor;
					}
					
				}		
			}

			if ($tabela=='ESP_JUNO_DOCUMENTOS' ){
				require('../services/juno.php');
				$codigoPrestador = '';
				$idDocumento    = '';
				
				for($i=0;$i<count($campos);$i++){
					if($campos[$i]['CAMPO']=='ID_DOCUMENTO'){
						$idDocumento = $campos[$i]['VALOR'];
					}
					if($campos[$i]['CAMPO']=='CODIGO_PRESTADOR'){
						$codigoPrestador = $campos[$i]['VALOR'];
					}					
				}
				$query = 'Select TOKEN_JUNO from ps5000 where CODIGO_PRESTADOR='.aspas($codigoPrestador);
				$res  = jn_query($query);
				if($row = jn_fetch_object($res)){
					$queryImg = "select    '../../UploadArquivos/server/files/'||(caminho_arquivo_armazenado||nome_arquivo_armazenado) IMAGEM from controle_arquivos where chave_registro = ".aspas($idDocumento);
					$resImg  = jn_query($queryImg);
					while($rowImg = jn_fetch_object($resImg)){
						if(file_exists($rowImg->IMAGEM)){
							$retornoEnvio = EnviarDocumentos($row->TOKEN_JUNO,$idDocumento,realpath($rowImg->IMAGEM));	
							if($retornoEnvio['STATUS'] == 'OK'){
								$queryUpdate = 'UPDATE ESP_JUNO_DOCUMENTOS set STATUS_DOCUMENTO='.aspas($retornoEnvio['DADOS']['approvalStatus']).' where ID_DOCUMENTO ='.aspas($idDocumento);
								$resUpdate = jn_query($queryUpdate);
							}
						}
					}
					
					$retornoDocumentos = ListaDocumentosEnvio($row->TOKEN_JUNO);

					if($retornoDocumentos['STATUS'] == 'OK'){
						for ($i = 0; $i < count($retornoDocumentos['DADOS']['_embedded']['documents']); $i++) {
							
						
							$query = 'Select * from ESP_JUNO_DOCUMENTOS where ID_DOCUMENTO = '. aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['id']);
							$res = jn_query($query);
							if($row = jn_fetch_object($res)){
								$query = 'update ESP_JUNO_DOCUMENTOS set STATUS_DOCUMENTO='.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['approvalStatus']).' where ID_DOCUMENTO ='.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['id']);
								$res = jn_query($query);
							}else{
								$query = 'INSERT into ESP_JUNO_DOCUMENTOS(ID_DOCUMENTO,DESCRICAO_DOCUMENTO,STATUS_DOCUMENTO,CODIGO_PRESTADOR) VALUES('.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['id']).','.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['description']).','.aspas($retornoDocumentos['DADOS']['_embedded']['documents'][$i]['approvalStatus']).','.aspas($dadosInput['dados']['prestador']).')';
								$res = jn_query($query);
							}
						}
					}		
					
				}
			}

			if ($tabela=='ESP_REEMBOLSO' && (($tipo == 'INC') || ($tipo == 'ALT'))){
				$numeroRegistro = '';
				$codigoAssociado = '';
				$tipoDespesa = '';
				$valorDespesa = 0;
				$dataUtilizacao = '';
				$statusSolicitacao = '';
				$valorCalculado = 0;
				$qtdeAtendimento = 0;

				for($i=0;$i<count($campos);$i++){
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$numeroRegistro = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='TIPO_DESPESA'){
						$tipoDespesa = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='VALOR_TOTAL_DESPESA'){
						$valorDespesa = $campos[$i]['VALOR'];
					}	
					
					if($campos[$i]['CAMPO']=='DATA_UTILIZACAO'){
						$dataUtilizacao = $campos[$i]['VALOR'];
					}	

					if($campos[$i]['CAMPO']=='STATUS_SOLICITACAO'){
						$statusSolicitacao = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='QUANTIDADE_ATENDIMENTOS'){
						$qtdeAtendimento = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='NOME_PRESTADOR'){
						$campos[$i]['VALOR'] = strtoupper($campos[$i]['VALOR']);
					}
				}

				$valorCalculado = 0;

				if ($tipoDespesa != '05'){

					$valorCalculado = (($valorDespesa * 75) / 100);
					
					if($valorCalculado > 300 && ($tipoDespesa == '01' || $tipoDespesa == '02' || $tipoDespesa == '03')){						
						$valorCalculado = 300;
					}

					$valorCalculado = ($valorCalculado * $qtdeAtendimento);

					$valorCalculado = str_replace(".",",",$valorCalculado);
				}
				
				$atualizaValor = jn_query('UPDATE ESP_REEMBOLSO SET VALOR_CALCULADO = ' . aspas($valorCalculado) . ' WHERE NUMERO_REGISTRO = ' . aspas($numeroRegistro));

				if ($statusSolicitacao == 'N'){

				    $retorno['MSG'] = 'Solicitação negada, não será reembolsado este valor';

				}else if ($tipoDespesa == '05'){

					$retorno['MSG'] = 'O reembolso desse tipo de despesa, será calculado e informado posteriormente.';

				}else{

					$retorno['MSG'] = 'Será reembolsado o valor de ' . toMoeda($valorCalculado) . ' para esse atendimento.';		
				}
			
			}

			
			if ($tabela=='ESP_AGENDAMENTO_CIRURGICO' and $tipo == 'INC'){
				
				$numeroRegistro = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$numeroRegistro = $campos[$i]['VALOR'];
					}			
				}
				
				$queryDados  = '	SELECT PS1000.CODIGO_ASSOCIADO, NOME_ASSOCIADO, EMAIL_ASSOCIADO, TELEFONE_ASSOCIADO, ';
				$queryDados .= '		DATA_CADASTRO, DATA_AGENDAMENTO, DATA_LIMITE, NOME_MEDICO, PROTOCOLO_GERAL_PS6450 ';
				$queryDados .= '	FROM ESP_AGENDAMENTO_CIRURGICO ';
				$queryDados .= '	INNER JOIN PS1000 ON (ESP_AGENDAMENTO_CIRURGICO.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ';
				$queryDados .= '	WHERE ESP_AGENDAMENTO_CIRURGICO.NUMERO_REGISTRO = ' . aspas($numeroRegistro);
				
				$resDados = jn_query($queryDados);
				$rowDados = jn_fetch_object($resDados);
					
				$assunto = 'Nova solicitacao de agendento Cirurgico ';
				
				$corpoEmail  = ' Registro de nova solicita&ccedil;&atilde;o de agendamento cir&uacute;rgico <br> ';
				$corpoEmail .= " Associado(a): " . $rowDados->CODIGO_ASSOCIADO . ' - ' . $rowDados->NOME_ASSOCIADO . " <br> ";
				$corpoEmail .= " Data Cadastro: " . SqlToData($rowDados->DATA_CADASTRO) . " <br> ";
				
				if($rowDados->DATA_AGENDAMENTO){
					$corpoEmail .= " Data Agendamento: " . SqlToData($rowDados->DATA_AGENDAMENTO) . " <br> ";
				}

				$corpoEmail .= " Data Limite: " . SqlToData($rowDados->DATA_LIMITE) . " <br> ";
				$corpoEmail .= " M&eacute;dico: " . $rowDados->NOME_MEDICO . " <br> ";
				$corpoEmail .= " Protocolo: " . $rowDados->PROTOCOLO_GERAL_PS6450 . " <br> ";
				
				disparaEmailFunc($rowDados->EMAIL_ASSOCIADO, $assunto, $corpoEmail);

				$telefoneAssociado = remove_caracteres($rowDados->TELEFONE_ASSOCIADO);
				$telefoneAssociado = str_replace('_','',$telefoneAssociado);

				if(strlen($telefoneAssociado) == '11'){
					require_once('../lib/smsPointer.php');			
					enviaSmsPointer($telefoneAssociado,'Nova solicitacao de agendento Cirurgico. Protocolo: ' . $rowDados->PROTOCOLO_GERAL_PS6450);			
				}
			}

			if ($tabela=='VND1000_ON' and $tabelaOrigem=='VW_VND1000_CAAPSML'){

				$codigoAssociado =  '';
				$codigoTitular =  '';
				$tipoAssociado =  '';

				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='CODIGO_TITULAR'){
						$codigoTitular = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='TIPO_ASSOCIADO'){
						$tipoAssociado = $campos[$i]['VALOR'];
					}
				}
			
				if($tipoAssociado == 'D'){
					$queryEventos  = ' INSERT INTO VND1003_ON (CODIGO_EVENTO, CODIGO_ASSOCIADO, QUANTIDADE_EVENTOS, TIPO_CALCULO, VALOR_FATOR, FLAG_COBRA_DEPENDENTE, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA) ';
					$queryEventos .= ' SELECT CODIGO_EVENTO, ' . aspas($codigoAssociado) . ', QUANTIDADE_EVENTOS, TIPO_CALCULO, VALOR_FATOR, FLAG_COBRA_DEPENDENTE, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA FROM VND1003_ON ';
					$queryEventos .= ' WHERE VND1003_ON.CODIGO_ASSOCIADO = ' . aspas($codigoTitular);
					$queryEventos .= ' AND VND1003_ON.CODIGO_EVENTO NOT IN (SELECT COALESCE(DEP.CODIGO_EVENTO,"") FROM VND1003_ON DEP WHERE DEP.CODIGO_ASSOCIADO =' . aspas($codigoAssociado) . ')';
					jn_query($queryEventos);
				}
			}

			if ($tabela=='ESP_TRANSFERENCIA_CAD'){


				$codigoAssociado =  '';
				$codigoTitular =  '';
				$tipoAssociado =  '';

				for($i=0;$i<count($campos);$i++)
				{	
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO_ANTIGO'){
						$codigoAssociadoAntigo = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='CODIGO_EMPRESA'){
						$codigoEmpresa = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='CODIGO_MOTIVO_EXCLUSAO'){
						$motivoExclusao = $campos[$i]['VALOR'];
					}

					if($campos[$i]['CAMPO']=='DATA_EXCLUSAO'){
						$dataExclusao = SqlToData($campos[$i]['VALOR']);
					}
				}
					
				$query = 'select CODIGO_EMPRESA, CODIGO_PLANO, CODIGO_TITULAR, TIPO_ASSOCIADO, FLAG_PLANOFAMILIAR, NUMERO_DEPENDENTE, CODIGO_ASSOCIADO, CODIGO_SEQUENCIAL
				                 from PS1000 Where CODIGO_TITULAR = ' . aspas($codigoAssociadoAntigo);

			


				$res   = jn_query($query);
				$codigoTitularPs1000  = '';
				$i = 0;
				while($row   = jn_fetch_object($res)){			

					$tipoAssociado      		= $row->TIPO_ASSOCIADO;
					$descricao          		= 'TRASNFERENCIA DE EMPRESAS ROTINA ALIANCANET2';
					$dataAdmissao      			=  $dataExclusao;
					$codigoAssociadoAnterior	=  $row->CODIGO_ASSOCIADO;

					if ($tipoAssociado == 'T'){

						$codigoSequencial      = jn_gerasequencial('PS1000');
						$numeroDependente      = '0';
				    	$codigoAssociadoPs1000 = geraCodigoAssociado('', $row->CODIGO_PLANO, $codigoEmpresa, $numeroDependente, $codigoSequencial); 
				    	$codigoTitularPs1000   = $codigoAssociadoPs1000;

				    }else{

				    	$queryDep  = ' select CODIGO_EMPRESA, CODIGO_PLANO, CODIGO_TITULAR, TIPO_ASSOCIADO, FLAG_PLANOFAMILIAR, NUMERO_DEPENDENTE, CODIGO_ASSOCIADO, CODIGO_SEQUENCIAL'; 
				    	$queryDep .= ' from PS1000 Where CODIGO_ASSOCIADO = ' . aspas($codigoTitularPs1000); 

				    	$resDep   = jn_query($queryDep);
						$rowDep   = jn_fetch_object($resDep);
						
						$codigoSequencial      = $rowDep->CODIGO_SEQUENCIAL;
				    	$codigoTitularPs1000   = $rowDep->CODIGO_TITULAR;
				    	$numeroDependente      = $i;
				    	$codigoAssociadoPs1000 = geraCodigoAssociado($rowDep->CODIGO_TITULAR, $rowDep->CODIGO_PLANO, $codigoEmpresa, $numeroDependente, $codigoSequencial);
			    	}

			
					$queryPs1000  = ' Insert Into PS1000(CODIGO_ASSOCIADO, CODIGO_TITULAR, CODIGO_SEQUENCIAL, 
			                             CODIGO_ANTIGO, CODIGO_EMPRESA, CODIGO_CARENCIA, 
										 NOME_ASSOCIADO, CODIGO_TABELA_PRECO, CODIGO_PLANO, 
										 DATA_NASCIMENTO, DATA_ADMISSAO, DATA_VALIDA_CARENCIA, DATA_DIGITACAO,
										 CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, TIPO_ASSOCIADO, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG,
										 SEXO, CODIGO_PAIS_EMISSOR, FLAG_PLANOFAMILIAR, CODIGO_AUXILIAR,	NOME_MAE, NOME_PAI, NUMERO_PIS,
										 FLAG_COPARTICIPACAO_PROC, CODIGO_CNS, NUMERO_DEPENDENTE, NATUREZA_RG, DATA_EMISSAO_RG,
										 CODIGO_MOTIVO_INCLUSAO,	NUMERO_DECLARACAO_NASC_VIVO, DESCRICAO_OBSERVACAO,	INFORMACOES_LOG_A)
										 select ' . 
										 aspas($codigoAssociadoPs1000) . ', ' . aspas($codigoTitularPs1000) . ' , ' . aspas($codigoSequencial) . ', 
										 CODIGO_ASSOCIADO, ' . aspas($codigoEmpresa) . ', CODIGO_CARENCIA, 
										 UPPER(NOME_ASSOCIADO), CODIGO_TABELA_PRECO, CODIGO_PLANO, 
										 DATA_NASCIMENTO, ' . dataToSql($dataAdmissao) . ' , DATA_VALIDA_CARENCIA, DATA_DIGITACAO,
										 CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, TIPO_ASSOCIADO, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG,
										 SEXO, CODIGO_PAIS_EMISSOR, FLAG_PLANOFAMILIAR, CODIGO_AUXILIAR,	UPPER(NOME_MAE), UPPER(NOME_PAI), NUMERO_PIS,
										 FLAG_COPARTICIPACAO_PROC, CODIGO_CNS, ' . aspas($numeroDependente) . ', NATUREZA_RG, DATA_EMISSAO_RG,
										 CODIGO_MOTIVO_INCLUSAO,	NUMERO_DECLARACAO_NASC_VIVO, '. aspas($descricao) .' ,INFORMACOES_LOG_A
										 from PS1000
										 where CODIGO_ASSOCIADO = ' . aspas($codigoAssociadoAnterior);

					jn_query($queryPs1000);


					if ($tipoAssociado == 'T'){			
						$query1 = 'Update ESP_TRANSFERENCIA_CAD set CODIGO_ASSOCIADO = ' . aspas($codigoAssociadoPs1000) . ', DATA_HORA = '. dataToSql(date("d/m/Y")) . '  where CODIGO_ASSOCIADO_ANTIGO = ' . aspas($codigoAssociadoAnterior);
						jn_query($query1);
					}
					

								
					$queryExclusao = 'Update Ps1000 set CODIGO_MOTIVO_EXCLUSAO = ' . aspas($motivoExclusao) . ', DATA_EXCLUSAO = '. dataToSql($dataExclusao) . '  where CODIGO_ASSOCIADO = ' . aspas($codigoAssociadoAnterior);
					jn_query($queryExclusao);
					
					$i++;
				}				
			}
			
			if(($tabela == 'PS6500')){
				if(retornaValorCFG0003('VALIDA_TOKEN_AUTORIZACAO')=='SIM'){
					for($i=0;$i<count($campos);$i++){
						if($campos[$i]['CAMPO']=='TOKEN'){
							$token = $campos[$i]['VALOR'];
						}
					}
					$update = 'UPDATE ESP_TOKEN SET REGISTRO='.aspas($chave).' WHERE TOKEN ='. aspas($token);
					jn_query($update);
				}
			}
			
			if(($tabela == 'PS6550')){
				if(retornaValorCFG0003('VALIDA_TOKEN_SOLICITACAO')=='SIM'){
					for($i=0;$i<count($campos);$i++){
						if($campos[$i]['CAMPO']=='TOKEN'){
							$token = $campos[$i]['VALOR'];
						}
					}
					$update = 'UPDATE ESP_TOKEN SET REGISTRO='.aspas($chave).' WHERE TOKEN ='. aspas($token);
					jn_query($update);
				}
			}
			
			if ($tabela=='PS1095' and $tipo == 'INC' and retornaValorConfiguracao('ENVIA_EMAIL_PS1095')=='SIM'){			
				
				$codigoAssociado = '';
				$enderecoEmail = '';		
				$numeroRegistro = '';		
				
				for($i=0;$i<count($campos);$i++)
				{
					
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO'){
						$codigoAssociado = $campos[$i]['VALOR'];
					}
					if($campos[$i]['CAMPO']=='NUMERO_REGISTRO'){
						$numeroRegistro = $campos[$i]['VALOR'];
					}
					
					if($campos[$i]['CAMPO']=='EMAIL'){
						$enderecoEmail = $campos[$i]['VALOR'];
					}
				}

				$enderecoEmail = retornaValorConfiguracao('ENDERECO_EMAIL_PS1095');
				if($enderecoEmail){
				
					$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
					$resAssoc = jn_query($queryAssoc);
					$rowAssoc = jn_fetch_object($resAssoc);
					
					$queryProtocolo = 'SELECT PROTOCOLO_GERAL_PS6450 FROM PS1095 WHERE NUMERO_REGISTRO = ' . aspas($numeroRegistro);
					$resProtocolo = jn_query($queryProtocolo);
					$rowProtocolo = jn_fetch_object($resProtocolo);
					
					$assunto = 'Solicitacao de Cancelamento';			
					$corpoEmail  = ' Numero Protocolo: ' . $rowProtocolo->PROTOCOLO_GERAL_PS6450 . ' <br>';
					$corpoEmail .= ' Data Solicitacao: '. date('d/m/Y') . ', as ' . date('H:i') . '<br>';		
					$corpoEmail .= ' Nome do Beneficiario ' . $rowAssoc->NOME_ASSOCIADO;		
					
					disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
				}
			}

			if (($tabela=='PS1001' || $tabela=='PS1006') and $tipo == 'ALT' and retornaValorConfiguracao('ENVIA_EMAIL_ALT_CADASTRAIS')=='SIM'){			
				
				$codigoAssociado = '';
				
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO')
						$codigoAssociado = $campos[$i]['VALOR'];			
				}

				$queryAssoc = 'SELECT NOME_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				$resAssoc = jn_query($queryAssoc);
				$rowAssoc = jn_fetch_object($resAssoc);

				$complemento = '';
				if($tabela == 'PS1001'){
					$complemento = ' - Endereco';
				}elseif($tabela == 'PS1006'){
					$complemento = ' - Telefone';
				}
				
				$assunto = 'Alteracao dados cadastrais ' . $complemento;					
				$corpoEmail .= ' Data Alteracao: '. date('d/m/Y') . ', as ' . date('H:i') . '<br>';		
				$corpoEmail .= ' Nome do Beneficiario: ' . $rowAssoc->NOME_ASSOCIADO;		
				
				$enderecoEmail = retornaValorConfiguracao('END_EMAIL_ALT_CADASTRAIS');
				disparaEmailFunc($enderecoEmail, $assunto, $corpoEmail);
			}

	}
	
	
}

function validaParametrosPS1059($codigoAssociado){

	if($_SESSION['codigoSmart'] == '3423'){//Plena

		$queryAssoc = '	SELECT 
							CODIGO_EMPRESA, CODIGO_PLANO, DATA_ADMISSAO_EMPRESA, DATA_NASCIMENTO 
						FROM TMP1000_NET 
						WHERE CODIGO_ASSOCIADO =' . aspas($codigoAssociado);
		$resAssoc = jn_query($queryAssoc);
		$rowAssoc = jn_fetch_object($resAssoc);
		$dataAdmissaoEmpresa = $rowAssoc->DATA_ADMISSAO_EMPRESA;

		$queryDadosEmpresa	= ' SELECT TOP 1 * FROM PS1059';
		$queryDadosEmpresa .= ' WHERE CODIGO_EMPRESA = ' . aspas($rowAssoc->CODIGO_EMPRESA);
		$queryDadosEmpresa .= ' AND CODIGO_PLANO = ' . aspas($rowAssoc->CODIGO_PLANO);	
		$resDadosEmpresa = jn_query($queryDadosEmpresa);
		$rowDadosEmpresa = jn_fetch_object($resDadosEmpresa);	

		
		$quantidadeDias 	 = $rowDadosEmpresa->QUANTIDADE_DIAS_CARENCIA;
		$codigoComCarencia 	 = $rowDadosEmpresa->CODIGO_COM_CARENCIA;
		$codigoSemCarencia 	 = $rowDadosEmpresa->CODIGO_SEM_CARENCIA;
		$diaAdmissao 		 = $rowDadosEmpresa->DIA_ADMISSAO;		

		$dataAdmissaoEmpresa    = SqlToData($dataAdmissaoEmpresa);
		$dataAdmissaoEmpresa    = explode("/",$dataAdmissaoEmpresa); 
		$dataAdmissaoEmpresa	= mktime(0,0,0,$dataAdmissaoEmpresa[1],$dataAdmissaoEmpresa[0],$dataAdmissaoEmpresa[2]);						
		$dataAtual     = date('d/m/Y');
		$dataAtual     = explode("/",$dataAtual); 
		$dataAtual    = mktime(0,0,0,$dataAtual[1],$dataAtual[0],$dataAtual[2]);						
		$dias       = ($dataAtual-$dataAdmissaoEmpresa)/86400;
		$validaDias = ceil($dias);
					
		$codigoCarencia = '';
		if($validaDias >= $quantidadeDias){
			$codigoCarencia = $codigoComCarencia;
		}else{
			$codigoCarencia = $codigoSemCarencia;
		}
		
		if($diaAdmissao){
			$dataAdmissao = date('d/m/Y');
			$diaAdmissao = str_pad($diaAdmissao, 2, 0, STR_PAD_LEFT);
			
			if(date('m') == '12'){
				$mes = '01';
				$mes = str_pad($mes, 2, 0, STR_PAD_LEFT);
				$ano = (date('Y') + 1);
				$dataAdmissao = $ano . '-' . $mes . '-' . $diaAdmissao;					
			}else{
				$mes = (date('m') + 1);
				$mes = str_pad($mes, 2, 0, STR_PAD_LEFT);
				$ano = date('Y');					
				$dataAdmissao = $ano . '-' . $mes . '-' . $diaAdmissao;										
			}			
		}else{
			$dataAdmissao = date('Y-m-d');
		}
		
		jn_query('	UPDATE TMP1000_NET SET 
						CODIGO_TABELA_PRECO = ' . aspas($rowDadosEmpresa->CODIGO_TABELA_PRECO) . ',
						CODIGO_CARENCIA = ' . aspas($codigoCarencia) . ',
						DATA_ADMISSAO = ' . aspas($dataAdmissao) . '
						WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado)
				);		
	}
}




function migraBeneficiarioTmp1000ToPs1000($codigoAssociado, $tipoAssociado,$codigoTitular)
{

	if ($tipoAssociado == 'T')
	{
		$query = 'select CODIGO_EMPRESA, CODIGO_PLANO, CODIGO_TITULAR, TIPO_ASSOCIADO, FLAG_PLANOFAMILIAR, 0 NUMERO_DEPENDENTE, CODIGO_ASSOCIADO
		                 from TMP1000_NET Where CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);

		$res   = jn_query($query);
		$row   = jn_fetch_object($res);

		$codigoSequencial      = jn_gerasequencial('PS1000');
    	$codigoAssociadoPs1000 = geraCodigoAssociado('', $row->CODIGO_PLANO, $row->CODIGO_EMPRESA, $row->NUMERO_DEPENDENTE, $codigoSequencial); 
    	$codigoTitularPs1000   = $codigoAssociadoPs1000;
    	$codigoEmpresa         = $row->CODIGO_EMPRESA;
    	$numeroDependente      = '0';
    }
    else
    {

		$query = 'select  PS1000.CODIGO_EMPRESA, PS1000.CODIGO_PLANO, PS1000.CODIGO_TITULAR, PS1000.TIPO_ASSOCIADO, CODIGO_SEQUENCIAL, PS1000.FLAG_PLANOFAMILIAR, PS1000.CODIGO_ASSOCIADO,
		                 (select coalesce(max(PS1000DEP.NUMERO_DEPENDENTE)+1,1) FROM PS1000 PS1000DEP WHERE (PS1000DEP.CODIGO_TITULAR = PS1000.CODIGO_ASSOCIADO)) NUMERO_DEPENDENTE           
		                 from PS1000 
						 LEFT OUTER JOIN TMP1000_NET ON (PS1000.CODIGO_ANTIGO = TMP1000_NET.CODIGO_ASSOCIADO)
						 Where 
						 		((PS1000.TIPO_ASSOCIADO = "T") OR (TMP1000_NET.TIPO_ASSOCIADO = "T"))
							AND ((PS1000.CODIGO_TITULAR = ' . aspas($codigoTitular) . ') OR (TMP1000_NET.CODIGO_TITULAR = ' . aspas($codigoTitular) . ')) ';						 

		$res   = jn_query($query);
		$row   = jn_fetch_object($res);

		$codigoSequencial      = $row->CODIGO_SEQUENCIAL;
    	$codigoTitularPs1000   = $row->CODIGO_TITULAR;
    	$codigoEmpresa         = $row->CODIGO_EMPRESA;
    	$numeroDependente      = $row->NUMERO_DEPENDENTE;

    	$codigoAssociadoPs1000 = geraCodigoAssociado($row->CODIGO_TITULAR, $row->CODIGO_PLANO, $row->CODIGO_EMPRESA, $row->NUMERO_DEPENDENTE, $codigoSequencial); 
    }

	$query = 'Insert Into PS1000(CODIGO_ASSOCIADO, CODIGO_TITULAR, CODIGO_SEQUENCIAL, 
	                             CODIGO_ANTIGO, CODIGO_EMPRESA, CODIGO_CARENCIA, 
								 NOME_ASSOCIADO, CODIGO_TABELA_PRECO, CODIGO_PLANO, 
								 DATA_NASCIMENTO, DATA_ADMISSAO, DATA_VALIDA_CARENCIA, DATA_DIGITACAO,
								 CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, TIPO_ASSOCIADO, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG,
								 SEXO, CODIGO_PAIS_EMISSOR, FLAG_PLANOFAMILIAR, CODIGO_AUXILIAR,	NOME_MAE, NOME_PAI, NUMERO_PIS,
								 FLAG_COPARTICIPACAO_PROC, CODIGO_CNS, NUMERO_DEPENDENTE, NATUREZA_RG, DATA_EMISSAO_RG,
								 CODIGO_MOTIVO_INCLUSAO,	NUMERO_DECLARACAO_NASC_VIVO,	INFORMACOES_LOG_A)
								 select ' . 
								 aspas($codigoAssociadoPs1000) . ', ' . aspas($codigoTitularPs1000) . ' , ' . aspas($codigoSequencial) . ', 
								 CODIGO_ASSOCIADO, CODIGO_EMPRESA, CODIGO_CARENCIA, 
								 UPPER(NOME_ASSOCIADO), CODIGO_TABELA_PRECO, CODIGO_PLANO, 
								 DATA_NASCIMENTO, ' . dataToSql(date("d/m/Y")) . ' , DATA_VALIDA_CARENCIA, DATA_DIGITACAO,
								 CODIGO_PARENTESCO, CODIGO_ESTADO_CIVIL, TIPO_ASSOCIADO, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG,
								 SEXO, CODIGO_PAIS_EMISSOR, FLAG_PLANOFAMILIAR, CODIGO_AUXILIAR,	UPPER(NOME_MAE), UPPER(NOME_PAI), NUMERO_PIS,
								 FLAG_COPARTICIPACAO_PROC, CODIGO_CNS, ' . aspas($numeroDependente) . ', NATUREZA_RG, DATA_EMISSAO_RG,
								 CODIGO_MOTIVO_INCLUSAO,	NUMERO_DECLARACAO_NASC_VIVO,	INFORMACOES_LOG_A
								 from TMP1000_NET
								 where CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);

	jn_query($query);

	//

	$query1 = 'Update TMP1000_NET set FLAG_IMPORTADO = ' . aspas('S') . ' where CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
	jn_query($query1);

	//

	$codigoEvento = '44';

	$query2 	= 'Insert Into PS1003(CODIGO_EVENTO, CODIGO_EMPRESA, CODIGO_ASSOCIADO, QUANTIDADE_EVENTOS, VALOR_FATOR, 
	                                  TIPO_CALCULO, FLAG_COBRA_DEPENDENTE, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA) ' .
				  'Values(' . aspas($codigoEvento) . ', '.aspas($row->CODIGO_EMPRESA).', ' . aspas($codigoAssociadoPs1000) . ', 1, 100, "P", "N", ' . 
				              dataToSql(date("01/m/Y",strtotime("+1 month"))) . ', '. dataToSql(date("t/m/Y",strtotime("+1 month"))) . ')';
					
	jn_query($query2); 

	return $codigoAssociadoPs1000;

}



function geraCodigoAssociado($codigoTitular, $codigoPlano, $codigoEmpresa, $numeroDependente, $codigoSequencial)
{

	$query = 'SELECT CAMPOS_CODIGO_ASSOCIADO, QUANT_DIGITOS_CODIGO_ASSOCIADO from cfg0001';
	$res   = jn_query($query);
	$row   = jn_fetch_object($res);

	$camposCodigoAssociado            = $row->CAMPOS_CODIGO_ASSOCIADO;
	$quantidadeDigitosCodigoAssociado = $row->QUANT_DIGITOS_CODIGO_ASSOCIADO;
	$i 								  = 0;
	$codigoGerado                     = '';

    while ($i <= 20)
    {

    	//pr('retornaCampo($i,$camposCodigoAssociado)  ---->' . retornaCampo($i,$camposCodigoAssociado));
    	//pr('testaInt(retornaCampo($i,$camposCodigoAssociado)) ---->' . testaInt(retornaCampo($i,$camposCodigoAssociado,',')));

       if ((retornaCampo($i,$camposCodigoAssociado,',') == '') Or 
       	   (testaInt(retornaCampo($i,$camposCodigoAssociado,',')))) 
       {
           $i = $i + 1;
           continue;
       }

       if (retornaCampo($i,$camposCodigoAssociado,',') == 'LITERAL')
       {
          $codigoGerado  = $codigoGerado . retornaCampo($i+1,$camposCodigoAssociado,',');
       }
       else if (retornaCampo($i,$camposCodigoAssociado,',') == 'DIGITO')
	   {
	      $codigoGerado  = $codigoGerado . '0';
	   }
       else
       {
          if ((retornaCampo($i,$camposCodigoAssociado,',') == 'CODIGO_PLANO') And ($codigoTitular != '') And
              ($camposCodigoAssociado == 'CODIGO_EMPRESA,4,CODIGO_PLANO,2,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2,DIGITO,1'))
          {
             $codigoGerado  = copyDelphi($codigoTitular,1,6);
          }
          else if ((retornaCampo($i,$camposCodigoAssociado,',') == 'CODIGO_PLANO') And ($codigoTitular != '') AND 
                   ($camposCodigoAssociado == 'CODIGO_EMPRESA,3,CODIGO_PLANO,3,CODIGO_SEQUENCIAL,6,NUMERO_DEPENDENTE,2'))
          {
             $codigoGerado  = $codigoGerado . strZero(copyDelphi($codigoTitular,4,3),2);
          }
          else if ((retornaCampo($i,$camposCodigoAssociado,',') == 'CODIGO_PLANO') And ($codigoTitular != ''))
          {
             $codigoGerado  = $codigoGerado . strZero(copyDelphi($codigoTitular,6,2),2);
          }
          else if ($codigoTitular == '')
          {
          	 if (retornaCampo($i,$camposCodigoAssociado,',') == 'CODIGO_EMPRESA')
          	 {
          	 	$codigoGerado  = $codigoGerado . copyDelphi(strZero($codigoEmpresa,retornaCampo($i+1,$camposCodigoAssociado,',')),1,retornaCampo($i+1,$camposCodigoAssociado,','));
          	 	//pr(retornaCampo($i,$camposCodigoAssociado,',') . ' : ' . $codigoGerado);
          	 } 	
          	 else if (retornaCampo($i,$camposCodigoAssociado,',') == 'CODIGO_PLANO')
          	 {
          	 	$codigoGerado  = $codigoGerado . copyDelphi(strZero($codigoPlano,retornaCampo($i+1,$camposCodigoAssociado,',')),1,retornaCampo($i+1,$camposCodigoAssociado,','));
          	 	//pr(retornaCampo($i,$camposCodigoAssociado,',') . ' : ' . $codigoGerado);
          	 } 	
          	 else if (retornaCampo($i,$camposCodigoAssociado,',') == 'CODIGO_SEQUENCIAL')
          	 {
          	 	$codigoGerado  = $codigoGerado . copyDelphi(strZero($codigoSequencial,retornaCampo($i+1,$camposCodigoAssociado,',')),1,retornaCampo($i+1,$camposCodigoAssociado,','));
          	 	//pr(retornaCampo($i,$camposCodigoAssociado,',') . ' : ' . $codigoGerado);
          	 } 	
          	 else if (retornaCampo($i,$camposCodigoAssociado,',') == 'NUMERO_DEPENDENTE')
          	 {
          	 	$codigoGerado  = $codigoGerado . copyDelphi(strZero($numeroDependente,retornaCampo($i+1,$camposCodigoAssociado,',')),1,retornaCampo($i+1,$camposCodigoAssociado,','));
          	 	//pr(retornaCampo($i,$camposCodigoAssociado,',') . ' : ' . $codigoGerado);
          	 } 	
          	 else if (retornaCampo($i,$camposCodigoAssociado,',') == 'DIGITO')
          	 {
          	 	$codigoGerado  = $codigoGerado . '0';
          	 	//pr(retornaCampo($i,$camposCodigoAssociado,',') . ' : ' . $codigoGerado);
          	 } 	
          }
       }

       $i = $i + 2;

    }

    //

    if (($quantidadeDigitosCodigoAssociado == 15) && ($numeroDependente >= 1)) 
    {
        if (copyDelphi($codigoGerado,1,12) <> (copyDelphi($codigoTitular,1,12))) 
           $codigoGerado = copyDelphi($codigoTitular,1,12) . strZero($numeroDependente,2) . '0';
    }
    else if (($quantidadeDigitosCodigoAssociado == '14') && ($numeroDependente >= 1) && 
             ($camposCodigoAssociado == 'CODIGO_EMPRESA,4,CODIGO_PLANO,2,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2,DIGITO,1') &&
             (length($codigoTitular) == 15)) 
    {
        if (copyDelphi($codigoGerado,1,12) <> (copyDelphi($codigoTitular,1,12))) 
           $codigoGerado = copyDelphi($codigoTitular,1,12) . copyDelphi($codigoGerado,12,3);
    }
    else if (($quantidadeDigitosCodigoAssociado == '14') && ($numeroDependente >= 1) && 
             ($camposCodigoAssociado = 'CODIGO_EMPRESA,4,CODIGO_PLANO,2,CODIGO_SEQUENCIAL,5,NUMERO_DEPENDENTE,2,DIGITO,1'))
    {
        if (copyDelphi($codigoGerado,1,11) <> (copyDelphi($codigoTitular,1,11))) 
           $codigoGerado = copyDelphi($codigoTitular,1,11) . copyDelphi($codigoGerado,12,3);
    }
    else if (($quantidadeDigitosCodigoAssociado == '14') && ($numeroDependente >= 1) && 
             ($camposCodigoAssociado == 'CODIGO_EMPRESA,3,CODIGO_PLANO,3,CODIGO_SEQUENCIAL,6,NUMERO_DEPENDENTE,2')) 
    {
        if (copyDelphi($codigoGerado,1,12) <> (copyDelphi($codigoTitular,1,12))) 
           $codigoGerado = copyDelphi($codigoTitular,1,12) . copyDelphi($codigoGerado,13,2);
    }

 	//pr('CODIGO GERADO' . ' : ' . $codigoGerado);

    return $codigoGerado;



}



?>