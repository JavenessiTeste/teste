<?php
 
function antesGravacao_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,&$campos,&$retorno){
//$tipo = INC ALT EXC 
//campos apenas para INC ALT
//$retorno['STATUS'] = 'OK';
//$retorno['STATUS'] = 'ERRO'; para processo
//$retorno['MSG']    .= ''; mensagem que ira aparecer operador quando der erro.
//$retorno['MSG']    .= jn_utf8_encode('usar esssa função quando tiver acentuação');
//$campos[$i]['CAMPO']
//$campos[$i]['VALOR']
$retorno['STATUS'] = 'OK'; 
	/*
	if(($tipo == 'EXC')and($tabela=='XXX')){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    .= 'Não pode excluir';
	}


	if($tabela=="XXX"){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    .= 'TESTE ERRO';
	}
	
	for($i=0;$i<count($campos);$i++){
		if($campos[$i]['CAMPO']=='CODIGO_OPPROV'){
			$campos[$i]['VALOR'] = 1;
		}
	}
	*/
	
	/* AQUI CARREGO UM ARRAY PARA FACILITAR VALIDAÇÃO DOS CAMPOS */

	//pr($chave);
	//pr($nomeChave);
	//pr($campos);
	//pr($retorno);
	//pr('---------------------------------');

	$camposValores = array();

	if ($tipo != 'EXC')
	{
			for($i=0;$i<count($campos);$i++)
			{
				$nomeCampo = $campos[$i]['CAMPO'];
				$camposValores[$nomeCampo] = $campos[$i]['VALOR'];
			}
	}	



	if ($tipo != 'EXC')
	{
		
			if ($tabela=='PS1000')
			{

		        if (($tipo == 'INC') and (retornaValorConfiguracao('FLAG_VALIDAR_EMPR_PLANOS_EXC') == 'SIM'))
		        {
		        	if ($camposValores['CODIGO_GRUPO_PESSOAS']!='')
		        	{
		        		$rowTmp = qryUmRegistro('Select Coalesce(Ps1010.Data_Exclusao, Ps1030.Data_Inutiliz_Registro, Ps1014.Data_Exclusao) Data_Exclusao 
		        			                     From Cfg0001, Ps1010, Ps1014, Ps1030
												 where (ps1010.codigo_empresa = ' . aspas($camposValores['CODIGO_EMPRESA']) . ') and
												       (ps1014.codigo_grupo_pessoas = ' . aspas($camposValores['CODIGO_GRUPO_PESSOAS']) . ') and
												       (ps1030.codigo_plano = ' . aspas($camposValores['CODIGO_PLANO']) . ')');

		        		if ($rowTmp->DATA_EXCLUSAO!='')
		        		{
							$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('Há vínculos entre este beneficiário, sua empresa, seu grupo de pessoas ou ainda seu plano 
																		que estão excluídos.' 
												. '<br>Por isto, o beneficiário não pode ser cadastrado neste grupo de pessoas.');
		        		}
		        	}	
		        }

		        if (retornaValorConfiguracao('FLAG_VAL_PLANO_EMPRESA') == 'S') 
		        {
		        	$rowTmp = qryUmRegistro('Select CODIGO_EMPRESA From Ps1059 Where (codigo_Empresa = ' . aspas($camposValores['CODIGO_EMPRESA']) . ') And ' .
		                                                               '(Codigo_Plano = ' . aspas($camposValores['CODIGO_PLANO']) . ') ');

		       		if ($rowTmp->CODIGO_EMPRESA=='')
		       		{
						$retorno['STATUS'] = 'VALIDACAO';
						$retorno['MSG']    .= formataMsgErroAviso('Conforme parâmetros estabelecidos na empresa: ' . $camposValores['CODIGO_EMPRESA'] . 
											  ' o plano: ' . $camposValores['CODIGO_PLANO'] . 
									          ' não faz parte dos planos aceitos para esta empresa!');
		       		}
		        }

		        if ($camposValores['DATA_NASCIMENTO'] > $camposValores['DATA_ADMISSAO'])
		        {
					$retorno['STATUS'] = 'VALIDACAO';
					$retorno['MSG']    .= formataMsgErroAviso('A data de nascimento do beneficiário não pode ser maior que a data de admissão do mesmo.');
		        }

		        $rowTmp = qryUmRegistro('Select Flag_Omite_Processos_Ans, coalesce(quant_maxima_dependentes,0) quant_maxima_dependentes 
		        	                       From Ps1030 Where (Codigo_Plano = ' . aspas($camposValores['CODIGO_PLANO']) . ')');


				if ($rowTmp->QUANT_MAXIMA_DEPENDENTES!='0')
				{
	           		$rowQtDep = qryUmRegistro('Select Count(*) Quantidade From Ps1000 Where (Data_Exclusao is null) and
	           								  (Tipo_Associado = ' . aspas('D') . ') and 
	           								  (Codigo_Titular = ' . aspas($camposValores['CODIGO_TITULAR']) . ')');

		           if ($rowQtDep->QUANTIDADE >= $rowTmp->QUANT_MAXIMA_DEPENDENTES)
		           {
						$retorno['STATUS'] = 'VALIDACAO';
						$retorno['MSG']    .= formataMsgErroAviso('Neste plano, o máximo de dependentes parametrizado e de: ' . $rowTmp->QUANT_MAXIMA_DEPENDENTES . 
																	' e este contrato, já possui: ' . $rowQtDep->QUANTIDADE . ' dependentes.');
		           }
				}


				if ($rowTmp->FLAG_OMITE_PROCESSOS_ANS!='S')
					$beneficiarioExpAns = 'S';

				if ($beneficiarioExpAns=='S')
				{
			        if (($tipo == 'INC') and 
		                ($camposValores['CODIGO_MOTIVO_INCLUSAO'] != '15') And
		                ($camposValores['CODIGO_MOTIVO_INCLUSAO'] != '16') And
		                ($camposValores['CODIGO_MOTIVO_INCLUSAO'] != '17') And
		                ($camposValores['CODIGO_MOTIVO_INCLUSAO'] != '31') And
		                ($camposValores['CODIGO_MOTIVO_INCLUSAO'] != '41')) 
		            {
			             	$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= ('Para inclusões apenas são aceitos os códigos de motivo de inclusão : 15, 16, 17, 31 e 41. ');
		            }
		            else if (($tipo == 'ALT') And
		                      ($camposValores['CODIGO_MOTIVO_ALTERACAO'] != '6')  And
		                      ($camposValores['CODIGO_MOTIVO_ALTERACAO'] != '11') And
		                      ($camposValores['CODIGO_MOTIVO_ALTERACAO'] != '12') And
		                      ($camposValores['CODIGO_MOTIVO_ALTERACAO'] != '43') And
		                      ($camposValores['CODIGO_MOTIVO_ALTERACAO'] != '52') And
		                      ($camposValores['CODIGO_MOTIVO_ALTERACAO'] != '51'))
		            {
			             	$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('Para alterações apenas são aceitos os códigos de motivo de alteração : 6, 11, 12, 43, 51 e 52. ');
	  				}	

				    if (($camposValores['NUMERO_DECLARACAO_NASC_VIVO'] == '') And
			            ($camposValores['DATA_NASCIMENTO'] >= getMontaDataDMY('01/01/2010')))
			        {
				           	$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('As novas regras da ANS em relação ao SIB, obrigam que beneficiários com data de nascimento igual ou 
																				  superior a 01/01/2010, 
				                                 tenham o número da declaração de nascido vivo cadastrado. ');
				    }

					if ($camposValores['CODIGO_CNS'] == '') 
				    {
				         	$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('As novas regras da ANS em relação ao SIB, obrigam que todos os beneficiários possuam o campo 
								                      "codigo do cartão nacional de saúde (Código CNS)" preenchido. ');
					}

					if (($camposValores['NUMERO_CPF'] == '') And 
					   	($beneficiarioExpAns=='S') and 
					   	(($camposValores['TIPO_ASSOCIADO'] == 'T') Or (calculaIdade(dataHoje(),$camposValores['DATA_NASCIMENTO']) >= 18))) 
					{
						$retorno['STATUS'] = 'VALIDACAO';
						$retorno['MSG']    .= formataMsgErroAviso('O Número do cpf não foi informado e segundo normas da ANS e Receita Federal o CPF é obrigatório para
								                      titulares ou beneficiários com mais de 18 anos. ');
					}

				}


		        if (($tipo == 'INC') and ($camposValores['NUMERO_CPF'] != ''))
		        {
		            $rowTmp = qryUmRegistro('Select Codigo_Associado, Nome_Associado, Numero_Cpf From Ps1000 Where 
		            	                     ((Nome_Associado = ' . aspas($camposValores['NOME_ASSOCIADO']) . ') Or 
		                                      (Numero_Cpf = ' . aspas($camposValores['NUMERO_CPF']) . ')) and 
		                                      (Data_Exclusao Is Null) ');

		            if ($rowTmp->CODIGO_ASSOCIADO)
		            {
						$retorno['STATUS'] = 'VALIDACAO';
						$retorno['MSG']    .= formataMsgErroAviso('Já existe um beneficiário cadastrado com os seguintes parâmetros : <br>
								Nome : ' . $rowTmp->NOME_ASSOCIADO . '<br>
								Código : ' . $rowTmp->CODIGO_ASSOCIADO . '<br>
								Cpf : ' . $rowTmp->NUMERO_CPF . '<br>
								Analise se não haverá duplicação cadastral. ');
		            }
		        }


		        if ($camposValores['CODIGO_TABELA_PRECO'] != '')
		        {
			        	$qryTmp = qryUmRegistro('Select flag_valor_particular, FLAG_PLANOFAMILIAR From Ps1010 Where Codigo_Empresa = ' . aspas($camposValores['CODIGO_EMPRESA']));

			        	if ($qryTmp->FLAG_VALOR_PARTICULAR=='S')		
			        	{
			        		$qryTmp = qryUmRegistro('Select Numero_Registro From Ps1011 Where (Codigo_Empresa = ' . aspas($camposValores['CODIGO_EMPRESA']) . ') 
			        							      and (Codigo_Plano = ' . aspas($camposValores['CODIGO_PLANO']) . ')
			        			                      and (Codigo_Tabela_Preco = ' . aspas($camposValores['CODIGO_TABELA_PRECO']) . ')');
			        	}
			        	else
			        	{
			        		$qryTmp = qryUmRegistro('Select Numero_Registro From Ps1032 Where (Codigo_Plano = ' . aspas($camposValores['CODIGO_PLANO']) . ') 
			        			                     and (Codigo_Tabela_Preco = ' . aspas($camposValores['CODIGO_TABELA_PRECO']) . ')');
			        	}

			        	if ($qryTmp->NUMERO_REGISTRO=='')
			        	{
							$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('A tabela de preços informada não foi localizada!');
			        	}	
		        }


		        /* Preciso percorrer duas vezes, uma para preencher o código do associado, depois para completar os demais campos */

		        if ($tipo == 'INC')
		        {

			        	if (($camposValores['TIPO_ASSOCIADO']=='T') and ($camposValores['CODIGO_TITULAR']!=''))
			        	{
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    = formataMsgErroAviso('Não é possível vincular um TITULAR a outro TITULAR. <BR>
																		Então se este for realmente um TITULAR apague o valor do campo "CODIGO TITULAR"<br>
																		Se for um DEPENDENTE, corrija o valor do campo "TIPO BENEFICIÁRIO"');
						}					                                   
			        	else if (($camposValores['TIPO_ASSOCIADO']=='D') and ($camposValores['CODIGO_TITULAR']==''))
			        	{
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG']    = formataMsgErroAviso('Para cadastrar um dependente é obrigatório preenche o campo "CODIGO TITULAR" para 
																		vincular este DEPENDENTE ao seu respectivo TITULAR');
						}					                                   

						for ($i=0; $i<count($campos); $i++)
						{
							if (($campos[$i]['CAMPO']=='CODIGO_SEQUENCIAL') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='T'))
							{
								$camposValores['CODIGO_SEQUENCIAL'] = jn_gerasequencial('PS1000');
								$campos[$i]['VALOR']                = $camposValores['CODIGO_SEQUENCIAL'];

								$codigoAssociado = geraCodigoAssociado($camposValores['CODIGO_TITULAR'], $camposValores['CODIGO_PLANO'], 
									                                   $camposValores['CODIGO_EMPRESA'], $camposValores['NUMERO_DEPENDENTE'], 
									                                   $camposValores['CODIGO_SEQUENCIAL']);
							}
							else if (($campos[$i]['CAMPO']=='CODIGO_SEQUENCIAL') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='D'))
							{
								$qryQtdDep     = qryUmRegistro('Select MAX(NUMERO_DEPENDENTE)+1 NUMERO_DEPENDENTE 
										                            From Ps1000 
										                            Where (Codigo_TITULAR = ' . aspas($camposValores['CODIGO_TITULAR']) . ') ');

								$qryTmpTitular = qryUmRegistro('Select CODIGO_TITULAR, CODIGO_SEQUENCIAL, CODIGO_PLANO, CODIGO_EMPRESA
										                            From Ps1000 
										                            Where (Codigo_TITULAR = ' . aspas($camposValores['CODIGO_TITULAR']) . ') AND 
										                                  (TIPO_ASSOCIADO = ' . aspas('T') . ')');
								
								$codigoAssociado = geraCodigoAssociado($qryTmpTitular->CODIGO_TITULAR,$qryTmpTitular->CODIGO_PLANO, 
									                                     $qryTmpTitular->CODIGO_EMPRESA, $qryQtdDep->NUMERO_DEPENDENTE, 
									                                     $qryTmpTitular->CODIGO_SEQUENCIAL);
							}

						}

				/* --------------------------------------------------------------- */ 

						for ($i=0; $i<count($campos); $i++)
						{

								/* ---- TITULAR ------------------------------------- */

								if (($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='T'))
								{
									$campos[$i]['VALOR']   = $codigoAssociado;
								}
								else if (($campos[$i]['CAMPO']=='CODIGO_TITULAR') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='T'))
								{
									$campos[$i]['VALOR']   = $codigoAssociado;
								}
								else if (($campos[$i]['CAMPO']=='NUMERO_DEPENDENTE') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='T'))
								{
									$campos[$i]['VALOR']   = '0';
								}


								/* ---- DEPENDENTE ------------------------------------- */

								if (($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='D'))
								{
									$campos[$i]['VALOR']   = $codigoAssociado;
								}
								else if (($campos[$i]['CAMPO']=='CODIGO_EMPRESA') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='D'))
								{
									$campos[$i]['VALOR']   = $qryTmpTitular->CODIGO_EMPRESA;
								}
								else if (($campos[$i]['CAMPO']=='NUMERO_DEPENDENTE') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='D'))
								{
									$campos[$i]['VALOR']   = $qryQtdDep->NUMERO_DEPENDENTE;
								}
								else if (($campos[$i]['CAMPO']=='CODIGO_SEQUENCIAL') and ($campos[$i]['VALOR']=='') and ($camposValores['TIPO_ASSOCIADO']=='D'))
								{
									$campos[$i]['VALOR']   = $qryTmpTitular->CODIGO_SEQUENCIAL;
								}


								/* ---- OUTROS CAMPOS ----------------------------------- */

								else if ($campos[$i]['CAMPO']=='FLAG_PLANOFAMILIAR')
								{
									$qryTmp = qryUmRegistro('Select FLAG_PLANOFAMILIAR From Ps1010 Where (Codigo_Empresa = ' . aspas($camposValores['CODIGO_EMPRESA']) . ')');
									$campos[$i]['VALOR'] = $qryTmp->FLAG_PLANOFAMILIAR;
								}
						}

				}

			} /* Fim Ps1000 */



		    if ($tabela == 'PS1006') 
		    {
					if ($camposValores['CODIGO_ASSOCIADO']!= '')
						$criterio = ' Codigo_Associado = ' . aspas($camposValores['CODIGO_ASSOCIADO']);
					else
						$criterio = ' Codigo_Empresa = ' . aspas($camposValores['CODIGO_EMPRESA']);

		        	$qryTmp = qryUmRegistro('Select Count(*) Quantidade From Ps1006 Where ' . $criterio);

					for($i=0;$i<count($campos);$i++)
					{			
						if (($campos[$i]['CAMPO']=='INDICE_TELEFONE') and ($campos[$i]['VALOR']==''))
						{
							$campos[$i]['VALOR'] = $qryTmp->QUANTIDADE+1;
						}
					}
		    }


			if ($tabela=='PS1015')
			{			
					for($i=0;$i<count($campos);$i++)
					{			
							if (($campos[$i]['CAMPO']=='CODIGO_EMPRESA') and ($campos[$i]['VALOR']==''))
							{
								$rowTmp = qryUmRegistro('Select Codigo_Empresa From Ps1000 Where Codigo_Associado = ' . aspas($camposValores['ODIGO_ASSOCIADO']));
								$campos[$i]['VALOR'] = $rowTmp->CODIGO_EMPRESA;	
							}
					}
			}


			if (($tabela=='PS6110') or ($tabela=='PS1095'))
			{			
					for($i=0;$i<count($campos);$i++)
					{			
						if (($campos[$i]['CAMPO']=='CODIGO_OPERADOR') and ($campos[$i]['VALOR']==''))
						{
							$campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];	
						}
					}
			}


			if ($tabela=='PS6400')
			{			
					for($i=0;$i<count($campos);$i++)
					{			
						if (($campos[$i]['CAMPO']=='PROTOCOLO_GERAL_PS6450') and ($campos[$i]['VALOR']==''))
							  $campos[$i]['VALOR'] = 'XXXXX'; // *Provisório Gerar número do protocolo geral e colocar aqui. 	
						else if (($campos[$i]['CAMPO']=='CODIGO_ID_OPERADOR') and ($campos[$i]['VALOR']==''))
							  $campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];	
					}
			}


			if ($tabela=='PS6500')
			{			

					$qryTmp = qryUmRegistro('Select nome_associado from ps1000 where (codigo_associado = ' . aspas($camposValores['CODIGO_ASSOCIADO']) . ')');

					for($i=0;$i<count($campos);$i++)
					{			
							if (($campos[$i]['CAMPO']=='AUTORIZADO_POR') and ($campos[$i]['VALOR']==''))
								  $campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];	
							else if (($campos[$i]['CAMPO']=='CODIGO_ASSOCIADO') and ($campos[$i]['VALOR']==''))
								  $campos[$i]['VALOR'] = copyDelphi($qryTmp->NOME_ASSOCIADO,1,30);	
					}

					if ((($camposValores['TIPO_GUIA']=='S') or ($camposValores['TIPO_GUIA']=='A') or ($camposValores['TIPO_GUIA']=='I')) and 
						  ($camposValores['CODIGO_SOLICITANTE']==''))
					{
							$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('Para este tipo de autorização é necessário informado o código do solicitante.');
					}


					if ($camposValores['PROCEDIMENTO_PRINCIPAL']=='')
					{
							$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('Informe o procedimento principal, caso não haja, informe o primeiro procedimento.');
					}
			}


		    if ($tabela == 'PS6010')
		    {
					for($i=0;$i<count($campos);$i++)
					{			
							if (($campos[$i]['CAMPO']=='CODIGO_OPERADOR') and ($campos[$i]['VALOR']==''))
								  $campos[$i]['VALOR'] = $_SESSION['codigoIdentificacao'];	
							else if (($campos[$i]['CAMPO']=='DATA_DIGITACAO') and ($campos[$i]['VALOR']==''))
								  $campos[$i]['VALOR'] = DataToSql(date('d/m/Y'));
							else if (($campos[$i]['CAMPO']=='TIPO_SITUACAO') and ($campos[$i]['VALOR']==''))
								  $campos[$i]['VALOR'] = 'LIVRE';
					}

					if ($camposValores['NUMERO_TELEFONE']=='')
					{
							$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('É necessário confirmar o número do telefone.');
					}


			        if (retornaValorConfiguracao('FLAG_AVISA_AGENDAMENTO_FUTURO') == 'SIM')
			        {

			            $resTmp = jn_query('Select Ps6010.Numero_Registro, Ps6010.Data_Marcacao, Ps6010.Hora_Marcacao, Ps5000.Nome_Prestador From Ps6010 
			                                Inner Join Ps5000 On (Ps6010.Codigo_Prestador = Ps5000.Codigo_Prestador) 
			                                Where (Ps6010.Data_Marcacao >= ' . DataSql(dataToSql(date('d/m/Y'))) . ') And 
			                                      (Ps6010.Codigo_Associado = ' . aspas($camposValores['CODIGO_ASSOCIADO']) . ') And 
			                                      ((Ps6010.Codigo_Prestador = ' . aspas($camposValores['CODIGO_PRESTADOR']) . ') Or 
			                                       (Ps6010.Codigo_Especialidade = ' . aspas($camposValores['CODIGO_ESPECIALIDADE']) . ')) 
			                                Order By Ps6010.Data_Marcacao, Ps6010.Hora_Marcacao');

			            $msgLimite = '';

						while ($rowTmp = jn_fetch_object($resTmp))
						{
			                 $msgLimite .= 'Data : ' . sqltoData($rowTmp->DATA_MARCACAO) . ' hora : ' . 
			                               $rowTmp->HORA_MARCACAO . ' Prestador : ' . $rowTmp->NOME_PRESTADOR;
		            	}

		            	if ($msgLimite!='')
		            	{
							$retorno['STATUS'] = 'VALIDACAO';
							$retorno['MSG']    .= formataMsgErroAviso('Existem agendamentos futuros para este beneficiário no mesmo prestador. 
		                                                           Abaixo segue a lista dos agendamentos : <br>' . $msgLimite);
						}
					}			


					//*** PENDENCIA, COLOCAR A CHAMADA PARA VALIDAR DADOSAUTORIZACAOAGENDA *** PROVISÓRIO
					//*** NESTA FUNCAO, TAMBÉM VALIDAR PS1041, SITUACOES DE ATENDIMENTO
			}


			// Validações de preenchimento de campos diversos


			if (($camposValores['NUMERO_CPF']!='') and (!validaCPF($camposValores['NUMERO_CPF'])))
			{
				$retorno['STATUS'] = 'VALIDACAO';
				$retorno['MSG']    .= formataMsgErroAviso('Número CPF inválido.');
			}
			else if (($camposValores['NUMERO_CNPJ']!='') and (!validaCNPJ($camposValores['NUMERO_CNPJ'])))
			{
				$retorno['STATUS'] = 'VALIDACAO';
				$retorno['MSG']    .= formataMsgErroAviso('Número CNPJ inválido.');
			}
			
	}// fecha o if ($tipo != 'EXC'))



	return $retorno;


}


?>


