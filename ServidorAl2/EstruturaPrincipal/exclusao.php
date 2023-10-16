<?php


function excluirRegistro($tabela,$registro,$nomeChave, $reativar = 'N'){
	
	$retorno = array();
	
	$retorno['STATUS'] = 'OK';  //OK ERRO 
	$retorno['MSG']    = '';    //MENSAGEM ERRO


	/* --------------------------------------------------------------------------------------------------------------------- */

	if ($_SESSION['AliancaPx4Net']=='S') // Se for o ERP AliancaPx4Net
	{

		  require_once('../EstruturaPx/exclusao_ERPPx.php');
		  $retorno = excluirRegistro_ERPPx($tabela,$registro,$nomeChave, $reativar);

	}

	/* --------------------------------------------------------------------------------------------------------------------- */
	else // então se não for o ERP (for o portal)
	/* --------------------------------------------------------------------------------------------------------------------- */
	
	{

			if($tabela == 'PS6010')
			{
				$query ='select count(*) QTE from ps6010 WHERE  data_marcacao> '.dataToSql(date("d/m/Y")).' and numero_registro ='.aspas($registro);	
				$res = jn_query($query);
				$row = jn_fetch_object($res);
			
				if($row->QTE > 0){
					$query = 'UPDATE PS6010 SET codigo_associado =NULL , NOME_PESSOA =NULL, TIPO_SITUACAO ='.aspas('LIVRE'). '
							 WHERE numero_registro ='.aspas($registro);	
					$resDelete = jn_query($query);
					if($resDelete){
						$retorno['STATUS'] = 'OK';	
						$retorno['MSG'] = 'Ok, Horário desmarcado!'; 			
					}else{
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = erroSql(jn_GetErroSql());
					}
				}else{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = 'Você so pode desmarcar agenda com data superior a hoje';
				}
				
			}
			else if ($tabela == 'PS6500')
			{
						
				$queryDet ='delete from ps6510 where numero_autorizacao = '.aspas($registro);	
				$resDet = jn_query($queryDet);	
				
				if($resDet){
					$query ='delete from ps6500 where numero_autorizacao = '.aspas($registro);	
					$res = jn_query($query);	
					
					if($res){
						$retorno['STATUS'] = 'OK';	
						$retorno['MSG'] = 'Ok, registro excluído com sucesso!'; 			
					}else{				
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = erroSql(jn_GetErroSql());
					}
				
				}else{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = erroSql(jn_GetErroSql());
				}		


			}
			else if ($tabela == 'PS6110')
			{			
						
				$queryDet ='select count(*) REGISTRO from ps6130 where numero_registro_ps6110 = '.aspas($registro);	
				$resDet = jn_query($queryDet);			
				$rowDet = jn_fetch_object($resDet);	
						
				if($rowDet->REGISTRO==0){
					$query ='delete from ps6110 where numero_registro = '.aspas($registro);	
					$res = jn_query($query);
								
					if($res){
						$retorno['STATUS'] = 'OK';	
						$retorno['MSG'] = 'Ok, registro excluído com sucesso!'; 			
					}else{				
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = erroSql(jn_GetErroSql());
					}
				}
				else {
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = 'Essa solicitação não pode ser alterada, já existe interação.';			
				}
				
			}else if($tabela=='PS2510'){
				
					$queryConcluido = "select DATA_CONCLUSAO_PROCEDIMENTO from ps2510 where numero_registro =".aspas($registro);
					$resConcluido = jn_query($queryConcluido);
					$rowConcluido = jn_fetch_object($resConcluido);
						
					if ($rowConcluido->DATA_CONCLUSAO_PROCEDIMENTO != ''){  //diferente de vazio
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = 'Este procedimento já foi transformado em sinistro e não pode ser excluído';
					}else{
						$query ='delete from ps2511 where numero_registro_PS2510 = '.aspas($registro);	
						$res = jn_query($query);
						$query ='delete from ps2510 where numero_registro = '.aspas($registro);	
						$res = jn_query($query);
									
						if($res){
							$retorno['STATUS'] = 'OK';	
							$retorno['MSG'] = 'Ok, registro excluído com sucesso!'; 		
						}else{				
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    = erroSql(jn_GetErroSql());
						}
									
					}	
			}
			else if($tabela=='PS1010')
			{

				jn_query('Update Ps1000 Set Data_Exclusao = ' . dataToSql(date('d/m/Y')) . ' where data_exclusao is null and ' . $nomeChave . ' = ' . aspas($registro));
				jn_query('Update Ps1010 Set Data_Exclusao = ' . dataToSql(date('d/m/Y')) . ' where data_exclusao is null and ' . $nomeChave . ' = ' . aspas($registro));

				$retorno['STATUS'] = 'OK';	
				$retorno['MSG'] = 'Ok, empresa e beneficiários vinculados a mesma excluída com sucesso!'; 		

			}	
			else if($tabela=='PS1000')
			{
				$codigoMotivoExc = retornaValorConfiguracao('MOT_EXCLUSAO_PS1000');
				$codigoAssociado = $registro;
				$dataExclusao = '';	
				$complementoExclusaoAssoc = '';	

				if($_SESSION['type_db'] == 'sqlsrv'){
					$dataExclusao = ' DATA_EXCLUSAO = CONVERT(date, GETDATE()), ';			
				}else{
					$dataExclusao = ' DATA_EXCLUSAO = CURRENT_TIMESTAMP, ';
				}
				
				if($_SESSION['codigoSmart'] == '3423'){//Plena

					if ($_SESSION['perfilOperador'] == 'EMPRESA'){
						$queryQuantBenef  = ' SELECT COUNT(*) AS QUANTIDADE_BENEFICIARIOS, TIPO_ASSOCIADO ';
						$queryQuantBenef .= ' FROM PS1000 ';
						$queryQuantBenef .= ' WHERE DATA_EXCLUSAO IS NULL AND CODIGO_EMPRESA = ' . aspas($_SESSION['codigoIdentificacao']);
						$queryQuantBenef .= ' GROUP BY TIPO_ASSOCIADO ';
						$resQuantBenef = jn_query($queryQuantBenef);

						$quantTit = 0;
						$quantDep = 0;
						$quantTotal = 0;
						
						while($rowQuantBenef = jn_fetch_object($resQuantBenef)){
							if($rowQuantBenef->TIPO_ASSOCIADO == 'T'){
								$quantTit = $rowQuantBenef->QUANTIDADE_BENEFICIARIOS;
							}else{
								$quantDep = $rowQuantBenef->QUANTIDADE_BENEFICIARIOS;
							}

							$quantTotal += $rowQuantBenef->QUANTIDADE_BENEFICIARIOS;

						}
						
						if($quantTotal < 2){
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG'] = 'Nao e possivel fazer exclusao do beneficiario, por gentileza entre em contato com o suporte empresarial. Telefone: 3944-5400 Ramal: 5444/5434 ou no e-mail: cadastro.empresas@plenasaude.com.br.';					
						}
					}

					$dataExclusao = '';
					$queryAssoc = 'SELECT CODIGO_PLANO, TIPO_ASSOCIADO, CODIGO_EMPRESA, CODIGO_TITULAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
					$resAssoc = jn_query($queryAssoc);
					$rowAssoc = jn_fetch_object($resAssoc);
					
					$queryTpPgto = 'SELECT TIPO_PRE_POS_PAGTO FROM PS1030 WHERE CODIGO_PLANO = ' . aspas($rowAssoc->CODIGO_PLANO);
					$resTpPgto = jn_query($queryTpPgto);
					$rowTpPgto = jn_fetch_object($resTpPgto);
					
					if($rowTpPgto->TIPO_PRE_POS_PAGTO == 'POS'){
						$dataExclusao = ' DATA_EXCLUSAO = CONVERT(date, GETDATE()), ';				
					}else{
						$dataExclusao = ' DATA_EXCLUSAO = DATEADD(day, 30, CONVERT(date, GETDATE())), ';				
					}
					
					$queryQuant  = ' SELECT COUNT(*) AS QUANTIDADE_ASSOCIADOS FROM PS1000 ';
					$queryQuant .= ' WHERE  CODIGO_TITULAR = ' . aspas($rowAssoc->CODIGO_TITULAR);
					$queryQuant .= ' 	AND DATA_EXCLUSAO IS NULL ';
					$resQuant = jn_query($queryQuant);
					$rowQuant = jn_fetch_object($resQuant);
					$quantPessoas = $rowQuant->QUANTIDADE_ASSOCIADOS - 1;
					
					$queryTabPreco  = ' SELECT CODIGO_TABELA_PRECO FROM PS1037 ';
					$queryTabPreco .= ' WHERE  	CODIGO_EMPRESA = ' . aspas($rowAssoc->CODIGO_EMPRESA);
					$queryTabPreco .= ' 	AND CODIGO_PLANO = ' . aspas($rowAssoc->CODIGO_PLANO);
					$queryTabPreco .= ' 	AND QUANTIDADE_PESSOAS = ' . aspas($quantPessoas);
					$resTabPreco = jn_query($queryTabPreco);
					$rowTabPreco = jn_fetch_object($resTabPreco);
					$tabelaPreco = $rowTabPreco->CODIGO_TABELA_PRECO;			
								
					if($tabelaPreco){
						$queryTabela  = ' UPDATE PS1000 SET CODIGO_TABELA_PRECO = ' . aspas($tabelaPreco);				
						$queryTabela .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($rowAssoc->CODIGO_TITULAR);				
						jn_query($queryTabela);				
					}
					
					$complementoExclusaoAssoc .= ' FLAG_ISENTO_PAGTO = ' . aspas('S') . ' , ';
				}
				
				$queryExc  = ' UPDATE PS1000 SET ';		
				$queryExc .= $dataExclusao;	
				$queryExc .= ' CODIGO_MOTIVO_EXCLUSAO = ' . aspasNull($codigoMotivoExc) . ',';	
				$queryExc .= $complementoExclusaoAssoc;	
				$queryExc .= ' INFORMACOES_LOG_E = ' . aspas('[E' . $_SESSION['codigoIdentificacao'] . ' WEB D' . date('d/m/Y') . ']');	
				$queryExc .= ' WHERE ((CODIGO_ASSOCIADO = ' . aspas($codigoAssociado) . ') OR (CODIGO_TITULAR = ' . aspas($codigoAssociado) . ')) ';
 			    $queryExc .= ' AND CODIGO_EMPRESA = ' . aspas($_SESSION['codigoIdentificacao']);

				if(jn_query($queryExc)){

					if(rvc('ENVIAR_SMS_ZENVIA_EXCLUSAO')=='SIM' && $_SESSION['codigoSmart'] == '3423'){//Funcionalidade especial para Plena
						
						$queryFat  = ' SELECT CAST(SUM(PS1021.VALOR_FATURA) AS INT) AS VALOR_TOTAL_TIT FROM PS1021 ';
						$queryFat .= ' INNER JOIN PS1020 ON (PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020) ';
						$queryFat .= ' WHERE DATA_VENCIMENTO > DATEADD(day, -365, GETDATE()) ';
						$queryFat .= ' AND CODIGO_TITULAR = ' . aspas($codigoAssociado);
						$resFat = jn_query($queryFat);
						$rowFat = jn_fetch_object($resFat);
						$valorTotalFat = $rowFat->VALOR_TOTAL_TIT;

						$queryUtiliz  = ' SELECT CAST(SUM(VALOR_GERADO) AS INT) AS VALOR_TOTAL_GERADO FROM VW_UTILIZACAO_WEB ';
						$queryUtiliz .= ' WHERE DATA_PROCEDIMENTO > DATEADD(DAY, -365, GETDATE()) ';
						$queryUtiliz .= ' AND PS1021.CODIGO_TITULAR = ' . aspas($codigoAssociado);
						$resUtiliz = jn_query($queryUtiliz);
						$rowUtiliz = jn_fetch_object($resUtiliz);
						$valorTotalGerado = $rowUtiliz->VALOR_TOTAL_GERADO;

						$percentual = (($valorTotalGerado / $valorTotalFat) * 100);
						if($percentual < 70){
							//require('../EstruturaEspecifica/smsZenvia.php');
							require('../lib/smsPointer.php');
							
							$queryDadosAssoc  = ' SELECT PS1006.NUMERO_REGISTRO, CAST(CODIGO_AREA AS VARCHAR(2)) + CAST(NUMERO_TELEFONE AS VARCHAR(12)) AS TELEFONE, PS1000.NOME_ASSOCIADO, PS1000.DATA_EXCLUSAO ';
							$queryDadosAssoc .= ' INNER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO)  ';
							$queryDadosAssoc .= ' FROM PS1006 ';
							$queryDadosAssoc .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
							$resDadosAssoc = jn_query($queryDadosAssoc);
							$rowDadosAssoc = jn_fetch_object($resDadosAssoc);

							$nomeAssociado = $rowDadosAssoc->NOME_ASSOCIADO;
							$nomeEmpresa = $_SESSION['nomeUsuario'];
							$dataVigencia = SqlToData($rowDadosAssoc->DATA_EXCLUSAO);
							$msgSms  = ' Boa tarde seu ' . $nomeAssociado .', indentificamos que seu plano de saude estará vigente até ' . $dataVigencia . ' pela empresa ' . $nomeEmpresa . ' <br> ';
							$msgSms .= ' Gostaria de saber se o senhor tem interesse em continuar com o plano para o senhor e sua família pagando apenas “o valor atual que é cobrado da empresa pelos titulares e dependentes detalhados”. ';
							
							if(trim($rowDadosAssoc->TELEFONE)!=''){					
								$chave = $rowTel->NUMERO_REGISTRO;
								$rand = rand(1, 50);
								//enviaSmsZenvia(trim($rowTel->TELEFONE),$msgSms,'$chave'.$rand);
								$retornoSms = enviaSmsPointer(remove_caracteresT(trim($rowTel->TELEFONE)),$msgSms);
							}

							$push  = ' INSERT INTO APP_CONTROLE_PUSH (TITULO_MENSAGEM, DESCRICAO_MENSAGEM, CODIGO_ASSOCIADO, CODIGO_INTERNO, PRIORIDADE_PUSH) VALUES ( ';
							$push .= aspas('Oportunidade Plena') . ', ' . aspas($msgSms) . ', ' . aspas($codigoAssociado) . ', ';
							$push .= aspas($codigoAssociado) . ', 5) ';
							jn_query($push);
						}
					}

					$retorno['STATUS'] = 'OK';	
					$retorno['MSG'] = 'Exclusão realizada com sucesso.'; 			
				}else{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG'] = 'Não foi possivel realizar a exclusão deste associado.'; 			
				}
			}else if ($tabela == 'PS5750'){
						
				$queryGuia = "SELECT IMPORTAR, NUMERO_GUIA_GERADA FROM PS5750 WHERE NUMERO_REGISTRO =" . aspas($registro);
				$resGuia = jn_query($queryGuia);
				$rowGuia = jn_fetch_object($resGuia);
					
				if($rowGuia->NUMERO_GUIA_GERADA != ''){//Guia já importada
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = 'A guia não pode ser excluída, porque o registro já foi importado.';
				}elseif($rowGuia->IMPORTAR == 'S'){// Guia já marcada para importação
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = 'A guia não pode ser excluída, porque o registro já está marcado para importação.';
				}else{
					$queryDet ='DELETE FROM PS5760 WHERE NUMERO_REGISTRO_PS5750 = '.aspas($registro);	
					$resDet = jn_query($queryDet);	
					
					if($resDet){
						$query ='DELETE FROM PS5750 WHERE NUMERO_REGISTRO = '.aspas($registro);	
						$res = jn_query($query);	
						
						if($res){
							$retorno['STATUS'] = 'OK';	
							$retorno['MSG'] = 'Ok, registro excluído com sucesso!'; 			
						}else{				
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    = erroSql(jn_GetErroSql());
						}
					
					}else{
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = erroSql(jn_GetErroSql());
					}		
				}

			}else if ($tabela == 'PS1100'){

				$exclusaoPs1100 = 'update ps1100 set data_exclusao = current_timestamp where codigo_identificacao = ' . aspas($registro); 
				if (jn_query($exclusaoPs1100)){
					$retorno['STATUS'] = 'OK';	
					$retorno['MSG'] = 'Ok, registro excluído com sucesso!'; 			
				}else{				
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = erroSql(jn_GetErroSql());
				}
			}else if ($tabela == 'TMP1000_NET'){
				$erro = false;
				$sqlDelete_TMP1006_NET = ' delete from TMP1006_NET where CODIGO_ASSOCIADO = '.aspas($registro);
				if(jn_query($sqlDelete_TMP1006_NET)){			
					$sqlDelete_TMP1005_NET = ' delete from TMP1005_NET where CODIGO_ASSOCIADO = '.aspas($registro);
					if(jn_query($sqlDelete_TMP1005_NET)){			
						$sqlDelete_TMP1002_NET = ' delete from TMP1002_NET where CODIGO_ASSOCIADO = '.aspas($registro);
						if(jn_query($sqlDelete_TMP1002_NET)){					
							$sqlDelete_TMP1001_NET = ' delete from TMP1001_NET where CODIGO_ASSOCIADO = '.aspas($registro);
							if(jn_query($sqlDelete_TMP1001_NET)){				
								$sqlDelete_TMP1000_NET = ' delete from TMP1000_NET where CODIGO_ASSOCIADO = '.aspas($registro).' OR CODIGO_TITULAR = '.aspas($registro);
								if(!jn_query($sqlDelete_TMP1000_NET)){					
									$erro = true;
									}
							}else{
								$erro = true;
								}
						}else{
							$erro = true;
							}	
					}else{
						$erro = true;
						}	
				}else{
					$erro = true;
					}

				if(!$erro){
					$retorno['STATUS'] = 'OK';	
					$retorno['MSG'] = 'Ok, registro excluído com sucesso!';
				}else{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = erroSql(jn_GetErroSql());
				}

			}
			else
			{
				$sqlDelete = ' delete from '. strtolower($tabela).' where '.strtolower($nomeChave).' = '.aspas($registro);
				$resDelete = jn_query($sqlDelete);

				if($resDelete)
				{
					$retorno['STATUS'] = 'OK';	
					$retorno['MSG'] = 'Ok, registro excluído com sucesso!'; 			
				}
				else
				{
					//$retorno['STATUS'] = 'ERRO';
					//$retorno['MSG']    = erroSql(jn_GetErroSql());

					$sqlDelete = 'Update ' . strtolower($tabela).' set DATA_INUTILIZ_REGISTRO = ' . dataToSql(date('d/m/Y')) . ' where '.strtolower($nomeChave).' = '.aspas($registro);
					$resDelete = jn_query($sqlDelete);

					if($resDelete)
					{
						$retorno['STATUS'] = 'OK';	
						$retorno['MSG']    = 'O registro já está sendo utilizado em outras tabelas relacionadas.<br>
											Neste caso o registro não pode ser excluído, por isto o sistema inativou o registro!'; 			
					}
					else
					{
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = 'aaa' . erroSql(jn_GetErroSql());
					}

				}
			}

	} // Fim de validação se não for o AliancaPX4Net (ERP)
			
	return $retorno;
	
	
}

?>