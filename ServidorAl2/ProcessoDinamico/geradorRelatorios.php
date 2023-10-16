<?php


header("Content-Type: text/html; charset=UTF-8",true);

global $valorUltimoRegistroGrupo;
global $imprimiuAlgumDadoDepoisDoCabecalho;


/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			FUNCOES ESTRUTURAIS PARA QUALQUER TIPO DE RELATORIO
/*
/* ------------------------------------------------------------------------------------------------------- */



function retornaCamposRelatorioDinamico($resDados, $dadosInput)
{

	$j         = 0;
	$numCampos = jn_num_fields($resDados);
	$arrayRetorno = array();

	while ($j < $numCampos)
	{
			$nomeCampo   = strToUpper(jn_field_metadata($resDados,$j)['Name']);
			
			$registros['VALOR_SOMA_COLUNA_TOTAL_GERAL']         = 0; // SÓ Para poder armazenar a soma da coluna....
			$registros['VALOR_SOMA_COLUNA_TOTAL_PARCIAL']       = 0; // SÓ Para poder armazenar a soma da coluna....
			$registros['QUANTIDADE_TOTAL_COLUNA_TOTAL_GERAL']   = 0; // SÓ Para poder armazenar a soma da coluna....
			$registros['QUANTIDADE_TOTAL_COLUNA_TOTAL_PARCIAL'] = 0;
		   $registros['NOME_CAMPO']                            = strToUpper($nomeCampo);
         $registros['LABEL_CAMPO']                           = strToUpper($nomeCampo);
         $registros['TIPO_CAMPO']                            = retornaTipoCampoMetadataBanco($resDados,$j);
         $registros['TAMANHO_EXIBICAO']                      = retornaTamanhoCampoMetadataBanco($resDados,$j);
         $registros['ORDEM_CAMPO']                           = $J+100;
         $registros['FLAG_EXIBIR_CAMPO']                     = 'S';

         if (($dadosInput['primeiroAgrupamento'] == strToUpper($dadosInput['tabelaPrincipal']) . '.' . strToUpper($nomeCampo)) or 
             ($dadosInput['segundoAgrupamento']  == strToUpper($dadosInput['tabelaPrincipal']) . '.' . strToUpper($nomeCampo)) or 
             ($dadosInput['terceiroAgrupamento'] == strToUpper($dadosInput['tabelaPrincipal']) . '.' . strToUpper($nomeCampo))) 
         {
	         $registros['FLAG_AGRUPAR_CAMPO']                 = 'S';
         }
	      else
	      {
	         $registros['FLAG_AGRUPAR_CAMPO']                 = 'N';
	      }


         if ($dadosInput['primeiroAgrupamento'] == strToUpper($dadosInput['tabelaPrincipal']) . '.' . strToUpper($nomeCampo))
         {
         	$registros['ORDEM_AGRUPAMENTO_CAMPOS'] = '1';
         }
         else if ($dadosInput['segundoAgrupamento'] == strToUpper($dadosInput['tabelaPrincipal']) . '.' . strToUpper($nomeCampo))
         {
         	$registros['ORDEM_AGRUPAMENTO_CAMPOS'] = '2';
         }
         else if ($dadosInput['terceiroAgrupamento'] == strToUpper($dadosInput['tabelaPrincipal']) . '.' . strToUpper($nomeCampo)) 
         {
         	$registros['ORDEM_AGRUPAMENTO_CAMPOS'] = '3';
         }
         else
         {
         	$registros['ORDEM_AGRUPAMENTO_CAMPOS'] = '';
         }

         $registros['FLAG_TOTALIZAR_CAMPO']               = 'N';
         $arrayRetorno[] = $registros;
			$j++;
	}

	return $arrayRetorno;

}



function retornaConfiguracoesCampos($idProcesso)
{

	$arrayRetorno = array();
	$resRetorno   = jn_query('SELECT * FROM CfgRelatorios_Campos_PD WHERE NUMERO_REGISTRO_PROCESSO = ' . numSql($idProcesso) . ' order by ordem_campo');

	while($rowRetorno = jn_fetch_object($resRetorno))
	{
		$j = 0;

		while ($j < jn_num_fields($resRetorno))
		{

			if ($j==0)
			{
				$registros['VALOR_SOMA_COLUNA_TOTAL_GERAL']         = 0; // SÓ Para poder armazenar a soma da coluna....
				$registros['VALOR_SOMA_COLUNA_TOTAL_PARCIAL']       = 0; // SÓ Para poder armazenar a soma da coluna....
				$registros['QUANTIDADE_TOTAL_COLUNA_TOTAL_GERAL']   = 0; // SÓ Para poder armazenar a soma da coluna....
				$registros['QUANTIDADE_TOTAL_COLUNA_TOTAL_PARCIAL'] = 0; // SÓ Para poder armazenar a soma da coluna....
			}

			$nomeCampo                    = strToUpper(jn_field_metadata($resRetorno,$j)['Name']);
			$registros[$nomeCampo]        = jn_utf8_encode($rowRetorno->$nomeCampo);
			$j++;
		}

		$arrayRetorno[] = $registros;
	}

	return $arrayRetorno;

}




function retornaCriterioRelatorio($nomeCampo, $criterio = '', $valor = '', $funcaoMascara = '', $obrigatorio = '', $juncaoCriterios = ' and ')
{

	if ($nomeCampo=='INICIA_CRITERIOS')
	{
		return ' Where (1=1) ';
	}

	if (($valor=='')&&($obrigatorio=='N'))
	{
		return '';
	}


	if (strToUpper($criterio)=='LIKE')
		$valor .= '%';

	if ($funcaoMascara=='NUM')
		$valor = numSql($valor);
	else if ($funcaoMascara=='DATE')
		$valor = dataAngularToSql($valor);
	else 
		$valor = aspas($valor);

 	$criterio = $juncaoCriterios . ' (' . $nomeCampo . ' ' . $criterio . ' ' . $valor . ')' . PHP_EOL;

  	return $criterio;

}




function incluiNovosCamposConfiguracaoRelatorio($resDados,$identificacaoProcesso)
{

	$j         = 0;
	$numCampos = jn_num_fields($resDados);

	while ($j < $numCampos)
	{
			$nomeCampo   = strToUpper(jn_field_metadata($resDados,$j)['Name']);

			$rowTemp     = qryUmRegistro('Select numero_registro from CFGRELATORIOS_CAMPOS_PD where nome_campo = ' . aspas($nomeCampo) . 
				                          ' and numero_registro_processo = ' . aspas($identificacaoProcesso));

			if ($rowTemp->NUMERO_REGISTRO=='')
			{
				$sqlEdicao   = '';
				$sqlEdicao 	.= linhaJsonEdicao('numero_registro_processo', $identificacaoProcesso);
				$sqlEdicao 	.= linhaJsonEdicao('nome_campo', strToUpper($nomeCampo));
				$sqlEdicao 	.= linhaJsonEdicao('label_campo', strToUpper($nomeCampo));
				$sqlEdicao 	.= linhaJsonEdicao('tipo_campo', retornaTipoCampoMetadataBanco($resDados,$j));
				$sqlEdicao 	.= linhaJsonEdicao('tamanho_exibicao', retornaTamanhoCampoMetadataBanco($resDados,$j));
				$sqlEdicao 	.= linhaJsonEdicao('ordem_campo', $J+100,'N');
				$sqlEdicao 	.= linhaJsonEdicao('flag_exibir_campo', 'S');
				$sqlEdicao 	.= linhaJsonEdicao('flag_totalizar_campo', 'N');
				$sqlEdicao 	.= linhaJsonEdicao('flag_agrupar_campo', 'N');

				gravaEdicao('CFGRELATORIOS_CAMPOS_PD', $sqlEdicao, 'I', $criterioWhereGravacao);
			}

			$j++;
	}

}





/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			INICIA A EXECUÇÃO DO RELATÓRIO
/*
/* ------------------------------------------------------------------------------------------------------- */



function executaRelatorio($formatoSaidaRelatorio,$queryPrincipal,$identificacaoProcesso,
	                       $numeroRegistroInstanciaProcesso, $exibirRelatorioAppAngular='N', 
	                       $dadosInput = null, $tituloRelatorio = '', $nomeSalvarRelatorio = '', $criterioWhere = '')
{


	/* Aqui eu trato o formato original solicitado pelo usuário e o formato que será base para a criação 										*/
	/* por exemplo para criar o PDF e o Excel, primeiro criarei o HTML e depois o converterei para o formato desejado pelo usuario   */

	$converterAposTerminar    = '';
	$formatoOriginalSaida     = $formatoSaidaRelatorio;

   if ($formatoSaidaRelatorio=='PDF')
	{
		$converterAposTerminar = 'PDF';
		$formatoSaidaRelatorio  = 'HTML';
	}	
   else if ($formatoSaidaRelatorio=='XLS')
	{
		$formatoSaidaRelatorio = 'HTML';
		$converterAposTerminar = 'XLS';
	}	


	/* Abre a query principal utilizada passada como parametro pelo relatório */

   $resDados               = jn_query($queryPrincipal);
   $quantidadeLinhasQuebra = 70;


   if ($nomeSalvarRelatorio!= '') // Caso seja um relatório dinamico e o usuário deseje salvar o relatório
   {
   	salvarRelatorioDinamico($resDados,$nomeSalvarRelatorio, $tituloRelatorio, $criterioWhere);
   }

	$arrayCampos 		      = array();


	/* Se for relatório dinamico, não vou usar campos salvos na tabela */
	if ($identificacaoProcesso == 'RELATORIO_DINAMICO')
	{
		$arrayCampos = retornaCamposRelatorioDinamico($resDados,$dadosInput);
	}
	else
	{
	  	 /* Aqui verifica se precisa criar as colunas nas tabelas de configuração do relatório */
	   incluiNovosCamposConfiguracaoRelatorio($resDados,$identificacaoProcesso);
		$arrayCampos = retornaConfiguracoesCampos($identificacaoProcesso);
	}


   /* Dá início a geração do arquivo conforme o formato escolhido pelo usuário */

   if ($formatoSaidaRelatorio=='HTML')
   	$extensaoArquivo = 'HTML';
   else if ($formatoSaidaRelatorio=='TXT')
   	$extensaoArquivo = 'TXT';
   else if ($formatoSaidaRelatorio=='JSON')
   	$extensaoArquivo = 'JSON';
   else if ($formatoSaidaRelatorio=='CSV')
   	$extensaoArquivo = 'CSV';

   if ($identificacaoProcesso == 'RELATORIO_DINAMICO')
   {
	   $nomeArquivoGerado     = retornaValorConfiguracao('PD_DIR_RELATORIO_OPERACIONAIS') . 
	                            'Relatorio_ID_' . $numeroRegistroInstanciaProcesso . retornaDataHoraString() . '.' . $extensaoArquivo;
	}
	else
	{
	   $nomeArquivoGerado     = retornaValorConfiguracao('PD_DIR_RELATORIO_OPERACIONAIS') . 
	                            'Relatorio_ID_' . $informacao . '_PR_' . $numeroRegistroInstanciaProcesso . '.' . $extensaoArquivo;
	}

   $nomeCaminhoArquivo       = retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . 
                               $nomeArquivoGerado;


   /* Como eu gero o PDF e o XLS a partir do HTML, eu preciso salvar também o nome do arquivo final, */
   /* Pois será gerado um HTML e o arquivo com a extensão correta. */

   if ($formatoOriginalSaida=='PDF')
	{
		$nomeArquivoReal = str_replace('HTML','PDF',$nomeCaminhoArquivo);
	}	
   else if ($formatoOriginalSaida=='XLS')
	{
		$nomeArquivoReal = str_replace('HTML','XLS',$nomeCaminhoArquivo);
	}	
	else
	{	
		$nomeArquivoReal = $nomeCaminhoArquivo;
	}


	/* Caso não exista o relatório onde o relatório será salvo  */
	/* Cria o diretório 														*/

   criaDiretorioSeNaoExistir(retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . retornaValorConfiguracao('PD_DIR_RELATORIO_OPERACIONAIS'));

   $arquivoRelatorio   = fopen($nomeCaminhoArquivo, 'w');
   $linha 			     = '';


   /* Aqui soma as colunas para poder calcular a proporção das colunas do relatório (no caso do HTML) */ 

	for($i = 0;$i < count($arrayCampos) ; $i++)
	{
			if ($arrayCampos[$i]['FLAG_EXIBIR_CAMPO']!='N')
			{
				$totalColunas += $arrayCampos[$i]['TAMANHO_EXIBICAO'];
			}
	}


	/* Dá início a emissão do relatório, se for HTML tem a primeira parte onde monta o CSS */

	if ($formatoSaidaRelatorio!='JSON')
	{
  		$linha       .= iniciaRelatorioGerador($formatoSaidaRelatorio,$tituloRelatorio, $formatoOriginalSaida);
	   fwrite($arquivoRelatorio, $linha);
	}


	if ($formatoSaidaRelatorio=='JSON')
	{
   	$linha = '[' . PHP_EOL;
	   fwrite($arquivoRelatorio, $linha);
	}


   /* Agora prepara o início do loop da exibição dos dados do relatório */ 

	global $valorUltimoRegistroGrupo;

   $valorUltimoRegistroGrupo[1] = '';
   $valorUltimoRegistroGrupo[2] = '';
   $valorUltimoRegistroGrupo[3] = '';
   $cssLinha                    = 'linhaImpar';
   $caracterSeparacaoJson       = '';
   $ImprimiuPrimeiraQuebra      = 'N';


   /* Loop da query da tabela */ 

   
   while($rowDados = jn_fetch_object($resDados))
   {

   	/* Aqui verifica se precisa fazer a quebra (por conta da primeira linha ou por conta do estouro de linhas da página) */ 

		if ($formatoSaidaRelatorio!='JSON')
		{
			if ($ImprimiuPrimeiraQuebra=='N')
			{
				$linha = imprimeQuebraETotalizacoesGrupo($formatoSaidaRelatorio,$arrayCampos, $rowDados, $totalColunas,'INICIAL',$resDados);

				if ($linha=='')
		   		$linha  .= montaLinhaCabecalhoColunas($formatoSaidaRelatorio,$arrayCampos,$totalColunas, 'INICIAL',$resDados);

				$ImprimiuPrimeiraQuebra = 'S';
			}
			else
			{
	   		$linha = imprimeQuebraETotalizacoesGrupo($formatoSaidaRelatorio,$arrayCampos, $rowDados, $totalColunas,'',$resDados);
	   	}

	   	if ($linha!='')
	   		fwrite($arquivoRelatorio, $linha . PHP_EOL);
	   }


	   /* Agora ele vai começar a criar as linhas de cada registro */

	   if ($formatoSaidaRelatorio=='HTML')
	   {
	   	$linha = edenta(24) . '<tr class="alturaLinha ' . $cssLinha . '">' . PHP_EOL;
	   }
	   else if ($formatoSaidaRelatorio=='JSON')
	   	$linha = $caracterSeparacaoJson . '{';
	  	else
	   	$linha = quebraLinha($formatoSaidaRelatorio);


	   /* Agora cria um loop nas colunas para exibir os dados de todas as colunas do relatório */ 

		for($i = 0;$i < count($arrayCampos) ; $i++)
		{
			if ($arrayCampos[$i]['FLAG_EXIBIR_CAMPO']!='N')
			{
				$linha .= adicionaCampoLinhaRelatorioGerador($formatoSaidaRelatorio,$rowDados,$arrayCampos[$i],$totalColunas);
			}

			if ($arrayCampos[$i]['FLAG_TOTALIZAR_CAMPO']=='S')
			{
				$nomeCampo    								                 = $arrayCampos[$i]['NOME_CAMPO'];
				$arrayCampos[$i]['VALOR_SOMA_COLUNA_TOTAL_GERAL']   += $rowDados->$nomeCampo;
				$arrayCampos[$i]['VALOR_SOMA_COLUNA_TOTAL_PARCIAL'] += $rowDados->$nomeCampo;
			}

			$arrayCampos[$i]['QUANTIDADE_TOTAL_COLUNA_TOTAL_GERAL']   ++;
			$arrayCampos[$i]['QUANTIDADE_TOTAL_COLUNA_TOTAL_PARCIAL'] ++;

		}


		/* Faz os fechamentos das tags abertas */

	   if ($formatoSaidaRelatorio=='HTML')
	   	$linha .= edenta(24) . '</tr>' . PHP_EOL;
	   else if ($formatoSaidaRelatorio=='JSON')
	   {
	  		if (copyDelphi($linha,strlen($linha),1)==',')
    		{   
       		 $linha = copyDelphi($linha,1,strlen($linha)-1);
    		}
	   	$linha .= '}' . PHP_EOL;
	   	$caracterSeparacaoJson = ',';
	   }


	   /* Muda a cor do CSS */

	   if ($cssLinha=='linhaImpar')
	   	$cssLinha='linhaPar';
	  	else
	  		$cssLinha='linhaImpar';

		fwrite($arquivoRelatorio, $linha);

	}


	/* Verifica se há sub-totais a imprimir */ 

	if ($formatoSaidaRelatorio!='JSON')
	{
		  	$linha = imprimeQuebraETotalizacoesGrupo($formatoSaidaRelatorio,$arrayCampos, $rowDados, $totalColunas,'FINAL',$resDados);

		  	if ($linha!='')
		  		fwrite($arquivoRelatorio, $linha . PHP_EOL);

		  	/* Agora exibe os totais armazenados */ 

			$exibirTotais = '';

			for($i = 0;$i < count($arrayCampos) ; $i++)
			{
					if ($i1==0)
					{
						$quantidadeRegistrosTotal			  = $arrayCampos[$i]['QUANTIDADE_TOTAL_COLUNA_TOTAL_GERAL'];
					}

					if ($arrayCampos[$i]['FLAG_TOTALIZAR_CAMPO']=='S') //|| ($arrayCampos[$i]['FLAG_AGRUPAR_CAMPO']=='S'))
					{
						$nomeCampo    		  = $arrayCampos[$i]['NOME_CAMPO'];
						$exibirTotais		 .= quebraLinha($formatoSaidaRelatorio) . imprimeInformacaoManualGerador($formatoSaidaRelatorio,'Total geral do campo: ' . $nomeCampo . ': ' . $arrayCampos[$i]['VALOR_SOMA_COLUNA_TOTAL_GERAL']);
					}
			}

			$exibirTotais .= quebraLinha($formatoSaidaRelatorio) . imprimeInformacaoManualGerador($formatoSaidaRelatorio,'Quantidade total de registros: ' . $quantidadeRegistrosTotal);

		   if ($formatoSaidaRelatorio=='HTML')
		   {
		   	$linha = edenta(22) . '</tbody>' . PHP_EOL . edenta(18) . '</table>' . PHP_EOL . edenta(18) . $exibirTotais . PHP_EOL . edenta(16) . '</body>' . PHP_EOL . edenta(14) . '</html>';
		   }
		   else
		   {
		   	$linha = quebraLinha($formatoSaidaRelatorio) . $exibirTotais;
		   }
			
		   if ($_POST['FLAG_EXIBIR_FILTROS_UTILIZADOS']=='S')
		   {
		   	$linha .= geraFiltrosNoRelatorio($formatoSaidaRelatorio);
		   }

		   fwrite($arquivoRelatorio, $linha);
	}
	else if ($formatoSaidaRelatorio=='JSON')
	{
   	$linha = ']' . PHP_EOL;
	   fwrite($arquivoRelatorio, $linha);
	}


   /* Fecha o arquivo gerado */

	fclose($arquivoRelatorio);


	/* Retorna o nome do arquivo gerado */ 

	if ($formatoSaidaRelatorio!='JSON')
	{
		if ($converterAposTerminar == 'PDF')
			geraPdfGerador($nomeCaminhoArquivo);

		if ($converterAposTerminar == 'XLS')
		{

		   if ($identificacaoProcesso == 'RELATORIO_DINAMICO')
   		{
	   		$nomeArquivoConvertido     = retornaValorConfiguracao('PD_DIR_RELATORIO_OPERACIONAIS') . 
	                                      'Relatorio_ID_' . $numeroRegistroInstanciaProcesso . retornaDataHoraString() . '.xls';
			}
			else
			{
				$nomeArquivoConvertido = 'Relatorio_ID_' . $informacao . '_PR_' . $numeroRegistroInstanciaProcesso . '.xls';
			}

			converteArquivoParaExcel($nomeCaminhoArquivo, $nomeArquivoConvertido);
			$nomeCaminhoArquivo    = $nomeArquivoConvertido;
		}

	}


	/* Termina tudo e retorna o nome do arquivo gerado */ 

	if ($identificacaoProcesso != 'RELATORIO_DINAMICO')
	{
		$resultado = registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',
			                                    'Arquivo do relatorio gerado no seguinte caminho: <br><br>' . $nomeArquivoReal,$nomeArquivoReal, 
			                                    $nomeArquivoReal,$nomeArquivoReal);
	}


   return $nomeCaminhoArquivo;

}




/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Inicia o relatório e coloca a primeira table que identificará a primeira página
/*
/* ------------------------------------------------------------------------------------------------------- */


function iniciaRelatorioGerador($formatoSaidaRelatorio, $tituloRelatorio, $formatoOriginalSaida)
{

	$tituloRelatorio = primeiraMaiuscula(retiraAcentos($tituloRelatorio));

	if ($formatoOriginalSaida!='PDF')
	{
	    $linkImg = '<img src="' . retornaValorConfiguracao('CAMINHO_LOGOTIPO_RELATORIO') . '" alt="*******" width="150">';
	}	

	if ($formatoSaidaRelatorio=='HTML')
	{

		$strRetorno = '<!DOCTYPE html>
			<html>
				<head>
					<meta http-equiv="Content-Language" content="pt-br">
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
					<title>' . $tituloRelatorio . '</title>
					<meta name="Microsoft Border" content="none, default"> 
					<link rel="stylesheet" href="../../ServidorCliente/styleRelatorios.css">
					<link rel="stylesheet" href="https://javenessi.com.br/produtos/cssAlianca4Net/styleRelatorios.css">
				</head>
				<body>
					<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; line-height: 100%; bordercolor=#111111; background-color: #D5EFFF; width:100%; "> 
					  <tr>
					    <td width="70%">
							<p class="fonteTitulo alinhaEsquerda" style="margin-top: 17px !important; margin-bottom: 17px !important; margin-left: 17px !important">
								<b><span style="font-size: 17px !important">Nome da empresa</span></b>
								<br>
								<span style="line-height: 35px !important">' . $tituloRelatorio . '</span>
							</p>
						</td>
					    <td width="15%">
							<p class="fonteTitulo alinhaCentro">
								<b>Pagina 01</b>
								<br>
								<b>' . sqlToData(dataHoje()) . '</b>
								<br>
								<b>&nbsp;</b>
							</p>
						</td>
					   <td width="15%">' . $linkImg . '</td>
					 </tr>
				  </table>' . PHP_EOL;

		}
		else if ($formatoSaidaRelatorio=='TXT')
		{
				$strRetorno .= '* ' . str_repeat('-', 140) . ' * ' . quebraLinha($formatoSaidaRelatorio) ;
				$strRetorno .= '* ' . $tituloRelatorio . quebraLinha($formatoSaidaRelatorio);
				$strRetorno .= '* ' . str_repeat('-', 140) . ' * ' . quebraLinha($formatoSaidaRelatorio);
		}

		return $strRetorno;

}




/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Monta o cabecalho das colunas do relatório
/*
/* ------------------------------------------------------------------------------------------------------- */


function montaLinhaCabecalhoColunas($formatoSaidaRelatorio,&$arrayCampos, $totalColunas,$tipoCabecalho='NORMAL',$resDados=null)
{

		global $imprimiuAlgumDadoDepoisDoCabecalho;

		if ($imprimiuAlgumDadoDepoisDoCabecalho=='N')
		{
			return '';
		}

		if ($formatoSaidaRelatorio=='HTML')
		{
			$strRetorno = edenta(18) . '<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; line-height: 100%; bordercolor=#111111; width:100%">
					     <thead>
				           <tr>' . PHP_EOL;
		}
		else
		{
			$strRetorno = quebraLinha($formatoSaidaRelatorio,2);
		}

		for($i = 0;$i < count($arrayCampos) ; $i++)
		{


			/* Aqui eu verifico se o campo que veio da tabela CFGRELATORIOS_CAMPOS_PD está no resultSet da query */ 

			$CampoEstaNaConsulta = 'N';

			for($j = 0;$j < jn_num_fields($resDados) ; $j++)
			{
					if ($arrayCampos[$i]['NOME_CAMPO'] == strToUpper(jn_field_metadata($resDados,$j)['Name']))
						$CampoEstaNaConsulta = 'S';
			}						

			if ($CampoEstaNaConsulta=='N')
				 $arrayCampos[$i]['FLAG_EXIBIR_CAMPO'] = 'N';

			/* Se o campo estiver estiver query ou se for para exibir, aí eu exibo o cabecalho dele. */

			if (($arrayCampos[$i]['FLAG_EXIBIR_CAMPO']!='N') and ($CampoEstaNaConsulta == 'S'))
			{

				$labelCampo = $arrayCampos[$i]['LABEL_CAMPO'];
				$labelCampo = primeiraMaiuscula(retiraAcentos($labelCampo));

				if ($formatoSaidaRelatorio=='HTML')
				{
				   $percentual = round(($arrayCampos[$i]['TAMANHO_EXIBICAO'] / ($totalColunas)) * 100,2)-3;

				   if ($arrayCampos[$i]['TIPO_CAMPO']=='DATE')
				   	$alinhamento = 'alinhaCentro';
				   else if ($arrayCampos[$i]['TIPO_CAMPO']=='NUMERIC')
				   	$alinhamento = 'alinhaDireita';
				   else
				   	$alinhamento = 'alinhaEsquerda';

					$strRetorno .= edenta(30) . '<th scope="col" class="fontePadrao corFundoTitulo ' . $alinhamento . '" style="width=' . $percentual . '% !important">' . $labelCampo . '</th>' . PHP_EOL;
				}
				else if ($formatoSaidaRelatorio=='CSV')
				{
					$string       = primeiraMaiuscula(retiraAcentos($arrayCampos[$i]['LABEL_CAMPO']));
					$tamanhoCampo = strlen($labelCampo);
				   $string       = substr($string,0,$tamanhoCampo);

				   $strRetorno  .= $string . ';';
				}
				else 
				{
					$string       = primeiraMaiuscula(retiraAcentos($arrayCampos[$i]['LABEL_CAMPO']));
					$tamanhoCampo = strlen($string);
				   $string       = substr($string,0,$tamanhoCampo);
				   $qtRepeticoes = $arrayCampos[$i]['TAMANHO_EXIBICAO'] - $tamanhoCampo;

				   if ($qtRepeticoes >= 1)
				      $strRetorno  .= $string . str_repeat(' ', $qtRepeticoes);
				  	else
					   $strRetorno  .= $string; 		
				}
			}
		}


		if ($formatoSaidaRelatorio=='HTML')
		{
			$strRetorno .= edenta(25) . '</tr>
			            </thead>' . PHP_EOL;

			//if ($tipoCabecalho=='NORMAL')
			//	$strRetorno .= edenta(24) . '<p style="page-break-after: always">&nbsp</p>';
		}
		else
		{
			$strRetorno . quebraLinha($formatoSaidaRelatorio);			
		}

		$strRetorno . quebraLinha($formatoSaidaRelatorio);

		$imprimiuAlgumDadoDepoisDoCabecalho = 'N';

		return $strRetorno;

}





/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Adiciona linhas do relatório
/*
/* ------------------------------------------------------------------------------------------------------- */


function adicionaCampoLinhaRelatorioGerador($formatoSaidaRelatorio, $row,$parametrosCampo, $totalColunas)
{

	global $imprimiuAlgumDadoDepoisDoCabecalho;
	$imprimiuAlgumDadoDepoisDoCabecalho = 'S';

	$nomeCampo    = $parametrosCampo['NOME_CAMPO'];
	$tamanhoCampo = $parametrosCampo['TAMANHO_EXIBICAO'];

	if ($parametrosCampo['TIPO_CAMPO']=='DATE')
	{
	   $string       = sqlToData($row->$nomeCampo);
   	$alinhamento  = 'alinhaCentro';
	}
	else if ($parametrosCampo['TIPO_CAMPO']=='NUMERIC')
	{
	   $string       = $row->$nomeCampo;
   	$alinhamento  = 'alinhaDireita';
   }
	else
	{
	   $string       = strToUpper(retiraAcentos($row->$nomeCampo));
   	$alinhamento = 'alinhaEsquerda';
	}

   $string       = substr($string,0,$tamanhoCampo);

	if ($formatoSaidaRelatorio=='HTML')
	{
		$percentual = $parametrosCampo['TAMANHO_EXIBICAO'] / ($totalColunas);
	   $string     = edenta(27) . '<td class="fontePadrao ' . $alinhamento . '">' . $string . '</td>' . PHP_EOL;
	}
	else if ($formatoSaidaRelatorio=='CSV')
	{
		$string = $string . ';';
	}
	else if ($formatoSaidaRelatorio=='JSON')
	{
		$string = $string = '"' . $nomeCampo . '":"' . $string . '",';
	}
	else
	{
		$string = $string . str_repeat(' ', $tamanhoCampo - strlen($string));
	}

   return $string;

}




/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Imprime as quebras conforme os parametros e também as totalizações dos grupos
/*
/* ------------------------------------------------------------------------------------------------------- */


function imprimeQuebraETotalizacoesGrupo($formatoSaidaRelatorio,&$arrayCampos, $rowDados, $totalColunas,$local='',$resDados=null)
{

	global $valorUltimoRegistroGrupo;
	global $imprimiuAlgumDadoDepoisDoCabecalho;

	$linha 		  = '';
	$exibirTotais = '';

	for($i = 0;$i < count($arrayCampos) ; $i++)
	{
			if (($arrayCampos[$i]['ORDEM_AGRUPAMENTO_CAMPOS']=='1') or 
			    ($arrayCampos[$i]['ORDEM_AGRUPAMENTO_CAMPOS']=='2') or 
			    ($arrayCampos[$i]['ORDEM_AGRUPAMENTO_CAMPOS']=='3'))
			{

				$nomeCampo    				 = $arrayCampos[$i]['NOME_CAMPO'];
				$numeroOrdemAgrupamento  = $arrayCampos[$i]['ORDEM_AGRUPAMENTO_CAMPOS'];

				if ((($valorUltimoRegistroGrupo[$numeroOrdemAgrupamento]!='') and 
					 ($valorUltimoRegistroGrupo[$numeroOrdemAgrupamento]!=$rowDados->$nomeCampo)) or
					 ($local=='FINAL') or ($local=='INICIAL'))
				{

					if ($local!='INICIAL')
					{
						for($i1 = 0;$i1 < count($arrayCampos) ; $i1++)
						{
							if ($i1==0)
							{
								$quantidadeRegistrosParcial  										  = $arrayCampos[$i1]['QUANTIDADE_TOTAL_COLUNA_TOTAL_PARCIAL'];
							}

							if ($arrayCampos[$i1]['FLAG_TOTALIZAR_CAMPO']=='S') //|| ($arrayCampos[$i1]['FLAG_AGRUPAR_CAMPO']=='S'))
							{
								$valorSomaParcial            										  = $arrayCampos[$i1]['VALOR_SOMA_COLUNA_TOTAL_PARCIAL'];
								//$quantidadeRegistrosParcial  										  = $arrayCampos[$i1]['QUANTIDADE_TOTAL_COLUNA_TOTAL_PARCIAL'];

								$exibirTotais .= quebraLinha($formatoSaidaRelatorio) . imprimeInformacaoManualGerador($formatoSaidaRelatorio,'Total do campo: ' . $arrayCampos[$i1]['NOME_CAMPO'] . ': ' . $valorSomaParcial);

								$arrayCampos[$i1]['VALOR_SOMA_COLUNA_TOTAL_PARCIAL'] 	     = 0;
								$arrayCampos[$i1]['QUANTIDADE_TOTAL_COLUNA_TOTAL_PARCIAL'] = 0;
							}

							$arrayCampos[$i1]['QUANTIDADE_TOTAL_COLUNA_TOTAL_PARCIAL'] = 0;

						}
	
						$exibirTotais .= quebraLinha($formatoSaidaRelatorio) . imprimeInformacaoManualGerador($formatoSaidaRelatorio,'Quantidade registros: ' . $quantidadeRegistrosParcial);

					}


					if (($local!='sssssFINAL')) //if (($local!='FINAL') or ($local=='INICIAL'))
					{

							if (copyDelphi($nomeCampo,1,5)=='DATA_')
							{
							   $valorCampo  = sqlToData($rowDados->$nomeCampo);
							}
							else
							{
							   $valorCampo  = $rowDados->$nomeCampo;
							}

		   	    		$campoDescricao = '';

		   	    		if ($arrayCampos[$i]['CAMPO_DESCRICAO_TOTALIZACAO'] != '')
		   	    		{
		   	    			 $campoDescricao = $arrayCampos[$i]['CAMPO_DESCRICAO_TOTALIZACAO'];
		   	    			 $campoDescricao = $rowDados->$campoDescricao;
		   	    		}

							if ($formatoSaidaRelatorio=='HTML')
							{
								if (($local!='INICIAL') and ($quantidadeRegistrosParcial != 0))
			   	    			$linha   .= edenta(22) . '</tbody>' . PHP_EOL . edenta(18) . '</table><br>' . PHP_EOL . edenta(18) . $exibirTotais . '<br><br>' . PHP_EOL;

								if ($local!='FINAL')
								{
	 								 $linha 	.= '<h5>Registros ' . mascaraNomeCampo($nomeCampo) . ': ' . $valorCampo . ' - ' . $campoDescricao . '</h5>';
								    $linha 	.= montaLinhaCabecalhoColunas($formatoSaidaRelatorio,$arrayCampos, $totalColunas,'',$resDados) . '<tbody>';
								}
							}
							else
							{
			   	    		if ($exibirTotais!='')
				   	    		$linha   .= quebraLinha($formatoSaidaRelatorio) . $exibirTotais . quebraLinha($formatoSaidaRelatorio,2);

								$linha 	.= quebraLinha($formatoSaidaRelatorio) .  'Registros ' . mascaraNomeCampo($nomeCampo) . ': ' . $valorCampo . ' - ' . $campoDescricao . montaLinhaCabecalhoColunas($formatoSaidaRelatorio,$arrayCampos, $totalColunas,'',$resDados);
							}
					}

					$exibirTotais = '';
				}				

			   $valorUltimoRegistroGrupo[$numeroOrdemAgrupamento] = $rowDados->$nomeCampo;
			}
	}

	return $linha;

}


/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Função de quebra de linha conforme o formato
/*
/* ------------------------------------------------------------------------------------------------------- */


function quebraLinha($formatoSaidaRelatorio, $quantidadeLinhas = 1)
{

	if ($formatoSaidaRelatorio=='HTML')
		$linhas = str_repeat(' <br> ', $quantidadeLinhas);
	else 
		$linhas = str_repeat(PHP_EOL, $quantidadeLinhas);

	return $linhas;

}


/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Função para emitir uma informação manual no relatório
/*
/* ------------------------------------------------------------------------------------------------------- */


function imprimeInformacaoManualGerador($formatoSaidaRelatorio,$string)
{

	if ($formatoSaidaRelatorio=='HTML')
	{
		return '<span class="fontePadrao alturaLinha" style="padding: 5px !important; line-height: 20px !important;">' . $string . '</span>';
	}
	else if ($formatoSaidaRelatorio=='CSV')
	{
		return $string . ';';
	}
	else
	{
		return $string;
	}

}




/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Função para conversão do HTML em PDF
/*
/* ------------------------------------------------------------------------------------------------------- */


function geraPdfGerador($nomeArquivoFonte)
{	

	require_once('../lib/mpdf60/mpdf.php');

	global $caminhoArquivo;
	global $codAssociadoTmp;	
	global $numeroContrato;	
	
	$nomeArquivoFinal = str_replace(strToUpper('HTML'), 'PDF',$nomeArquivoFonte);

	$arquivo        = fopen($nomeArquivoFonte, "r");

	while (($linha = fgets($arquivo)) !== false) 
	{
	 	 $htmlCorpo .= $linha;
	}

	fclose($arquivo);

	$cabecalho         = '<br><p>--------------------------------------------------</p><br><br>';
	$onde 			    = 'c';
	$orientacao		    = 'A4-P';
	$tamanhoFonte	    = 12;
	$tipoFonte	       = 'Verdana';
	$margemEsq		    = 5;
	$margemDir			 = 5;
	$margemTopoCab		 = 25;
	$margemRodapeCab   = 15; 
	$margemTopoPag	    = 5;
	$margemRodapePag	 = 4;


	if($_SESSION['codigoSmart'] == '4022'){
		$mpdf=new mPDF('c', 'A4-L'); 
	}else{		
		$mpdf=new mPDF();
	}

	$mpdf->SetDisplayMode('fullpage');	
	
	$arquivo        = fopen($nomeArquivoFonte, "r");

	while (($linha = fgets($arquivo)) !== false) 
	{
	 	 $arquivoHtml .= $linha;
	}

	$mpdf->WriteHTML($arquivoHtml);
	$mpdf->Output($nomeArquivoFinal);


}




/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Função para conversão do HTML em XLS (Excel)
/*
/* ------------------------------------------------------------------------------------------------------- */


function converteArquivoParaExcel($nomeArquivoFonte,$nomeArquivoExcel)
{

   	header("Content-type: application/vnd.ms-excel");   

   	// Força o download do arquivo
   	header("Content-type: application/force-download");  

   	// Seta o nome do arquivo
   	header("Content-Disposition: attachment; filename=" . $nomeArquivoExcel . '"');

   	header("Pragma: no-cache");

		header("MIME-Version: 1.0 ");

		$arquivo        = fopen($nomeArquivoFonte, "r");

		while (($linha = fgets($arquivo)) !== false) 
		{
		 	 $arquivoHtml .= $linha;
		}

		echo $arquivoHtml;

}




/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Função para apresentar os filtros no corpo do relatório
/*
/* ------------------------------------------------------------------------------------------------------- */



function geraFiltrosNoRelatorio($formatoSaidaRelatorio){

	$array = $_POST;

	$linhaRetorno = quebraLinha($formatoSaidaRelatorio,2) . 'Parâmetros e filtros:' . quebraLinha($formatoSaidaRelatorio);

   foreach ($array as $key => $value)
   {
   	$linhaRetorno .= $key . '->' . $value . quebraLinha($formatoSaidaRelatorio); 
   }

   return $linhaRetorno;


}



/* ------------------------------------------------------------------------------------------------------- */
/*
/* 			Função simples só para edentar o HTML para não ficar zoneado
/*
/* ------------------------------------------------------------------------------------------------------- */


function edenta($quantidadeEspacos){

	return completaString(' ',$quantidadeEspacos);

}




function salvarRelatorioDinamico($resDados,$nomeSalvarRelatorio, $tituloRelatorio, $criterioWhere)
{

	$j = 0;

	while ($j < jn_num_fields($resDados))
	{
		$sqlEdicao   = '';
		$sqlEdicao  .= linhaJsonEdicao('nome_relatorio',$nomeSalvarRelatorio);
		$sqlEdicao  .= linhaJsonEdicao('nome_tabela_coluna', jn_field_metadata($resDados,$j)['Name']);
		$sqlEdicao  .= linhaJsonEdicao('ordem_coluna', $j);
		$sqlEdicao  .= linhaJsonEdicao('flag_coluna_ordenada','N');
		$sqlEdicao  .= linhaJsonEdicao('TIPO_RETRATO_PAISAGEM','R');
		$sqlEdicao  .= linhaJsonEdicao('primeiro_titulo_relatorio', $tituloRelatorio);
		$sqlEdicao  .= linhaJsonEdicao('filtros_especiais',$criterioWhere);
		gravaEdicao('CFGRELATORIOSSALVOS', $sqlEdicao, 'I');

		$j++;
	}

}



?>