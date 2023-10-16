<?php


function antesMostrarCampo_ERPPx($tipo,$tabela,$tabelaOrigem,$chave,$nomeChave,&$campo, $campos = null, $parametroPrompt = '', $rowDadoSub = null)
{
	global $dadosInput;
	$tabela = strtoupper($tabela);
	
	// Vou tratar no formulário, se o retorno for "IGNORAR_CAMPO", forço subir o loop do formulário sem adicionar o campo. 
	//Isto para que quando for apenas um campo comportamento 3 na pasta, a pasta nem seja criada.
	$retorno = 'PADRAO';


	if($tipo=='INC')
	{
			if (($tabela=='PS1001') and ($parametroPrompt!='') and ($campo['VALOR']=='') and 
				(($campo['NOME_CAMPO']=='ENDERECO') or ($campo['NOME_CAMPO']=='BAIRRO') or ($campo['NOME_CAMPO']=='CIDADE') or 
				($campo['NOME_CAMPO']=='CEP') or ($campo['NOME_CAMPO']=='ESTADO')))
			{

				$qryTmp = qryUmRegistro('Select * from ps1040 where cep = ' . aspas($parametroPrompt));

				if($campo['NOME_CAMPO']=='ENDERECO')
					$campo['VALOR'] = $qryTmp->LOGRADOURO;
				else if($campo['NOME_CAMPO']=='CIDADE')
						$campo['VALOR'] = $qryTmp->CIDADE;
				else if($campo['NOME_CAMPO']=='BAIRRO')
						$campo['VALOR'] = $qryTmp->BAIRRO;
				else if($campo['NOME_CAMPO']=='CEP')
						$campo['VALOR'] = $qryTmp->CEP;
				else if($campo['NOME_CAMPO']=='ESTADO')
						$campo['VALOR'] = $qryTmp->ESTADO;
			}



			/*if ($tabela=='PS3100')
			{
				pr($campo);
				pr($rowDadoSub);
				pr('aaaaaaaaa');
				pr($campos);
				pr($chave);
				pr($nomeChave);
				pr($tabelaOrigem);
			}*/


			if (($tabela=='PS3100') and ($campo['VALOR']=='') and 
				(($campo['NOME_CAMPO']=='NUMERO_CONTRATO') or ($campo['NOME_CAMPO']=='NOME_CONTRATANTE')))
			{

				if (strlen($rowDadoSub->CAMPO01) >= 7)
				{
					$qryTmp = qryUmRegistro('Select NUMERO_CONTRATO, NOME_ASSOCIADO, DATA_ADMISSAO, DIA_VENCIMENTO from ps1000 
												INNER JOIN PS1002 ON (PS1000.CODIGO_ASSOCIADO = PS1002.CODIGO_ASSOCIADO)
												where PS1000.CODIGO_ASSOCIADO = ' . aspas($rowDadoSub->CAMPO01));
				}
				else
				{
					$qryTmp = qryUmRegistro('Select NUMERO_CONTRATO, NOME_EMPRESA NOME_ASSOCIADO, DATA_ADMISSAO, DIA_VENCIMENTO from ps1010 
												INNER JOIN PS1002 ON (PS1010.CODIGO_EMPRESA = PS1002.CODIGO_EMPRESA)
												where PS1010.CODIGO_EMPRESA = ' . aspas($rowDadoSub->CAMPO01));
				}

				if ($qryTmp->NUMERO_CONTRATO!='')
				{
					if($campo['NOME_CAMPO']=='NUMERO_CONTRATO')
						$campo['VALOR'] = $qryTmp->NUMERO_CONTRATO;
					else if($campo['NOME_CAMPO']=='NOME_CONTRATANTE')
						$campo['VALOR'] = $qryTmp->NOME_ASSOCIADO;
					else if($campo['NOME_CAMPO']=='DATA_CONTRATO')
						$campo['VALOR'] = $qryTmp->DATA_ADMISSAO;
					else if($campo['NOME_CAMPO']=='DIA_VENCIMENTO')
						$campo['VALOR'] = $qryTmp->DIA_VENCIMENTO;
				}
			}


			if ($tabela=='PS1002')  
			{
				if (($campo['NOME_CAMPO']=='NUMERO_CONTRATO') and
					($campo['VALOR']==''))
				{
					if (retornaValorConfiguracao('Flag_Gera_Num_Contr_Autom') == 'S')
					{
						$campo['VALOR'] = jn_gerasequencial('PS1002');		
					}
				}
			}


			if (($campo['NOME_CAMPO']=='NUMERO_PROCESSAMENTO') and ($campo['VALOR']==''))
				$campo['VALOR'] = retornaValorConfiguracao('NUMERO_PROCESSAMENTO_ATIVO');


			if (($campo['NOME_CAMPO']=='MES_ANO_REFERENCIA') and ($campo['VALOR']==''))
				$campo['VALOR'] = retornaMesAno(dataHoje());

	}


	return 	$retorno;

}
