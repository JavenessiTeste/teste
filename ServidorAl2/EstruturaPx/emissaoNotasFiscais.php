<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../lib/sysutilsAlianca.php');
require('../EstruturaPrincipal/processoPx.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

set_time_limit(0);

//pr($_POST['ID_PROCESSO']);
//	$_GET['Teste'] = 'OK';

global $arquivoLogProcesso;


/* ------------------------------------------------------------------------------------------ */
/*	EXECUTA CADA ETAPA DO PROCESSO PADRÃO													  */
/* ------------------------------------------------------------------------------------------ */

    $rowProcesso = qryUmRegistro('SELECT IDENTIFICACAO_PROCESSO FROM CFGDISPAROPROCESSOSCABECALHO 
                                  WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));


	echo apresentaMensagemInicioProcesso();
	$nomearquivoLogProcesso = escreveArquivoLogProcesso('C',$_POST['ID_PROCESSO']);

    if ($rowProcesso->IDENTIFICACAO_PROCESSO=='7009') // Processo padrão de geração de notas fiscais e depois da geração do arquivo
    {
		$jsonNotasGeradas = geraNotasFiscais();
		$JsonValores      = json_decode($jsonNotasGeradas);

		geraArquivoNotaFiscal($JsonValores->PRIMEIRA_NF,$JsonValores->ULTIMA_NF,$JsonValores->CODIGO_PREFIXO);

		$queryRelatorioProcessamento = 'Select Coalesce(Ps1020.Codigo_Associado, Ps1010.Codigo_Empresa) CODIGO_PAGADOR, 
											   Coalesce(Ps1000.Nome_Associado,Ps1010.Nome_Empresa) Nome_Pagador, 
		                                       Ps1020.Valor_Fatura, Ps1020.Data_Vencimento Vencimento_Fatura, 
		                                       Ps1056.Codigo_Prefixo, Ps1056.Numero_Nota_Fiscal, 
		                                       Ps1056.Numero_Rps, Ps1056.Numero_Lote_Nfse, Ps1056.Status_Nota_Fiscal 
		                                       From Ps1020
		                                       Inner Join Ps1056 on (Ps1020.Numero_Registro = Ps1056.Numero_Registro_Ps1020)
								               Left Outer Join Ps1010 On (Coalesce(Ps1020.Codigo_Empresa, 400) = Ps1010.Codigo_Empresa) 
								               Left Outer join Ps1000 On (Ps1020.Codigo_Associado = Ps1000.Codigo_Associado)
		                                       Where Ps1056.ID_INSTANCIA_PROCESSO = ' . aspas($_POST['ID_PROCESSO']);

		$nomearquivoRelatorioProcesso = geraRelatorioAutomaticoProcessamento($_POST['ID_PROCESSO'],$queryRelatorioProcessamento);	     

		$detalheConclusao = '';
		$detalheConclusao = 'Foram gerados: ' . $registrosProcessados . ' de um total de: ' . $totalRegistrosAProcessar;

		registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',$detalheConclusao,$nomeArquivoProcesso, $nomearquivoLogProcesso, $nomearquivoRelatorioProcesso);


    }
    else if ($rowProcesso->IDENTIFICACAO_PROCESSO=='7010') // Processos auxiliares
    {
    	if ($_POST['COMBO_TIPO_PROCESSO']=='REPROCESSAMENTO') // Reprocessamento do arquivo de notas fiscais já geradas
    	{
			geraArquivoNotaFiscal($_POST['AUTOCOMP_PRIMEIRA_NF'], $_POST['AUTOCOMP_ULTIMA_NF'], $_POST['CODIGO_PREFIXO_NF']);
		}
    	else if (($_POST['COMBO_TIPO_PROCESSO']=='ESTORNO_MANTER') or // Estorno de lotes de notas fiscais mantendo a numeração
    	         ($_POST['COMBO_TIPO_PROCESSO']=='ESTORNO_VOLTAR'))   // Estorno de lotes de notas fiscais voltando a numeração
    	{
			estornarGeracaoNotaFiscal($_POST['AUTOCOMP_PRIMEIRA_NF'], $_POST['AUTOCOMP_ULTIMA_NF'], $_POST['CODIGO_PREFIXO_NF'],$_POST['COMBO_TIPO_PROCESSO']);
		}
    	else if ($_POST['COMBO_TIPO_PROCESSO']=='CONSULTA') // Consulta de lotes de notas fiscais
    	{
			geraArquivoNotaFiscal($_POST['AUTOCOMP_PRIMEIRA_NF'], $_POST['AUTOCOMP_ULTIMA_NF'], $_POST['CODIGO_PREFIXO_NF']);
			//consultaLoteNotasFiscais($_POST['AUTOCOMP_PRIMEIRA_NF'], $_POST['AUTOCOMP_ULTIMA_NF'], $_POST['CODIGO_PREFIXO_NF']);
		}
	}

	escreveArquivoLogProcesso('F','');


/* ------------------------------------------------------------------------------------------ */
/*	ABAIXO AS FUNÇÕES DE CRIAÇÃO DAS NOTAS E DO ARQUIVO DE NF								  */
/* ------------------------------------------------------------------------------------------ */


function geraNotasFiscais()
{

    $numeroRps           = '';
    $numeroNotaFiscal    = '';
    $qtNotasGeradas      = 0;
    $primeiraNotaGerada  = '';
    $ultimaNotaGerada    = '';
    $qtNotasLote         = 0;

	$linhaLogProcesso = '';
	$linhaLogProcesso .= adicionaCampoLinhaLog('Registro',14);
    $linhaLogProcesso .= adicionaCampoLinhaLog('Código',17);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Nome do pagador',40);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Emissão',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Vl fatura',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Número da NF',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Informações adicionais',50);

	escreveArquivoLogProcesso('L',$linhaLogProcesso);

    $rowTemp             = qryUmRegistro('Select JSON_AUXILIAR from CfgLayouts where CFGLAYOUTS.PADRAO_ARQUIVO LIKE ' . aspas(strZero($_POST['COMBO_TIPO_ARQUIVO'],3) . '%'));

    if ($rowTemp->JSON_AUXILIAR!='')
    {
	   $JsonValores      = json_decode($rowTemp->JSON_AUXILIAR);
       $codigoPrefixo	 = $JsonValores->CODIGO_PREFIXO; 
    }

	$resPs1020           = selecionaFaturasParaNotasFiscais('GERACAO_REGISTRO_NF');

	while ($rowPs1020 = jn_fetch_object($resPs1020))
	{

	    // Verifico se eh um pgto de franquia, pois se for, tenho que verificar se já nao foi emitida a nota para este RPS
		if ($rowPs1020->TIPO_BAIXA=='Q')
		{
			$rowTemp = qryUmRegistro('select NUMERO_NFSE from PS1056 where Numero_Nota_Fiscal = ' . aspas($rowPs1020->NUMERO_NOTA_FISCAL) .
			                         ' and Numero_Registro_Ps1020 = ' . aspas($rowPs1020->NUMERO_REGISTRO) .
			                         ' and Status_Nota_Fiscal = ' . aspas('NFSE-EMITIDA'));

			if ($rowTemp->NUMERO_NFSE!='')
				Continue;
		}

	    if (($qtNotasLote >= $_POST['QUANTIDADE_FATURAS_LOTE']) or 
	        ($qtNotasLote == 0))
	    {
	      	$numeroLote = jn_gerasequencial('PS1056_LOTE_NSE');
	      	$qtNotasLote++;
	    }

        $qtNotasGeradas++;

      	if ($rowPs1020->TIPO_BAIXA != 'Q')
      	{
           // Reservo um novo numero de RPS para o prefixo selecionado...
      		$numeroRps = reservaNumeroRps($codigoPrefixo);
      	}

      	$numeroNotaFiscal = $numeroRps;

      	if ($rowPs1020->NUMERO_NOTA_FISCAL != $numeroNotaFiscal)
	       jn_query('update PS1020 set Numero_Nota_Fiscal = ' . aspas($numeroNotaFiscal) . ' where Numero_Registro = ' . numSql($rowPs1020->NUMERO_REGISTRO));

	    if ($rowPs1020->DATA_CANCELAMENTO!='')
	       jn_query('update PS1020 set Numero_Nota_Cancelamento = ' . aspas($numeroNotaFiscal) . ' where Numero_Registro = ' . numSql($rowPs1020->NUMERO_REGISTRO));
	    else
	       jn_query('update PS1020 set Numero_Nota_Emissao = ' . aspas($numeroNotaFiscal) . ' where Numero_Registro = ' . numSql($rowPs1020->NUMERO_REGISTRO));


	    if ($primeiraNotaGerada=='')
	    	$primeiraNotaGerada = $numeroNotaFiscal; 

		$sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('Codigo_Prefixo',$codigoPrefixo);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_NF_Sem_Formatar',$numeroNotaFiscal);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Nota_Fiscal',$numeroNotaFiscal);
		$sqlEdicao 	.= linhaJsonEdicao('Data_Recebimento',dataHoje(),'D');
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Rps',$numeroRps);
		$sqlEdicao 	.= linhaJsonEdicao('Data_Emissao',dataHoje(),'D');
		$sqlEdicao 	.= linhaJsonEdicao('Status_Nota_Fiscal','NFSE-EMITIDA');
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Ps1020',$rowPs1020->NUMERO_REGISTRO);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Lote_Nfse',$numeroLote);

		$criterioWhere = ' Numero_Nota_Fiscal = ' . aspas($numeroNotaFiscal) . 
                         ' and Status_Nota_Fiscal = ' . aspas('NFSE-EMITIDA') . 
                         ' and Numero_Registro_Ps1020 = ' . aspas($rowPs1020->NUMERO_REGISTRO);

		gravaEdicao('PS1056', $sqlEdicao, 'V', $criterioWhere );

		// Na rotina padrão, ele gera um detalhamento em tabelas
		// vou tentar resolver estes dados com a leitura das tabelas ps1029, ps1021, etc...

		$linhaLogProcesso = '';
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->NUMERO_REGISTRO,14);
	    $linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->CODIGO_IDENTIFICACAO,17);
		$linhaLogProcesso .= adicionaCampoLinhaLog(copyDelphi($rowPs1020->RAZAO_SOCIAL_TOMADOR,1,37),40);
		$linhaLogProcesso .= adicionaCampoLinhaLog(sqlToData(dataHoje()),14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('------',14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->numeroNotaFiscal,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('NF-GERADA',50);

		registraLogEvolucaoProcesso($_POST['ID_PROCESSO'],$linhaLogProcesso);

		escreveArquivoLogProcesso('L',$linhaLogProcesso);

	}

    if ($ultimaNotaGerada=='')
	    $ultimaNotaGerada = $numeroNotaFiscal; 

	$retorno = '{"PRIMEIRA_NF":"' . $primeiraNotaGerada . '","ULTIMA_NF":"' . $ultimaNotaGerada . '","CODIGO_PREFIXO":"' . $codigoPrefixo . '"}';

	return $retorno;

}




function selecionaFaturasParaNotasFiscais($tipoSelecao, $primeiraNota = '', $ultimaNota = '', $codigoPrefixo = '')
{

	if ($tipoSelecao=='GERACAO_REGISTRO_NF')
	{

	    $qryPs1020 =   'Select top 1000 Ps1020.*, Ps1010.Flag_Nota_Fiscal, PS1010.NOME_EMPRESA RAZAO_SOCIAL_TOMADOR, PS1010.CODIGO_EMPRESA CODIGO_IDENTIFICACAO 
	                    From PS1020 
	                    Inner Join Ps1010 On (Ps1020.Codigo_Empresa = Ps1010.Codigo_Empresa) 
	                    where 
	                    (Ps1020.Data_Vencimento between ' . dataAngularToSql($_POST['DATA_VENCIMENTO_INICIAL']) . ' and ' . dataAngularToSql($_POST['DATA_VENCIMENTO_FINAL']) . ') AND 
	                    (Ps1020.Data_Emissao between ' . dataAngularToSql($_POST['DATA_EMISSAO_INICIAL']) . ' and ' . dataAngularToSql($_POST['DATA_EMISSAO_FINAL']) . ') and  
	                    (Coalesce(Ps1020.IDENTIFICACAO_GERACAO,"") <> ' . aspas('SEG-VIA-CART') . ')'; // Segundo a Plena faturas de segunda via de carteirinhas não devem aparecer no relatório de inadimplência

	   if ($_POST['MES_ANO_REFERENCIA']!='')
	   		$qryPs1020 .= ' AND (Ps1020.Mes_Ano_Referencia = ' . aspas($_POST['MES_ANO_REFERENCIA']) . ') ';

	   if (($_POST['CODIGO_EMPRESA_INICIAL']!='') and ($_POST['CODIGO_EMPRESA_FINAL']!=''))
	   		$qryPs1020 .= ' AND (PS1020.CODIGO_EMPRESA BETWEEN ' . numSql($_POST['CODIGO_EMPRESA_INICIAL']) . ' and ' . numSql($_POST['CODIGO_EMPRESA_FINAL']) . ') ';

	   if (($_POST['GRUPO_FATURAMENTO_INICIAL']!='') and ($_POST['GRUPO_FATURAMENTO_FINAL']!=''))
	   		$qryPs1020 .= ' AND (PS1010.CODIGO_GRUPO_FATURAMENTO BETWEEN ' . numSql($_POST['GRUPO_FATURAMENTO_INICIAL']) . ' and ' . numSql($_POST['GRUPO_FATURAMENTO_FINAL']) . ') ';

	   if ($_POST['NUMERO_REGISTRO_PS1020']!='')
	   		$qryPs1020 .= ' AND (PS1020.NUMERO_REGISTRO = ' . numSql($_POST['NUMERO_REGISTRO_PS1020']) . ') ';

	   if ($_POST['CHECK_FATURAS_NEGOCIACAO']=='N')
	   		$qryPs1020 .= ' AND (COALESCE(PS1020.TIPO_REGISTRO,"F") <> ' . aspas('N') . ') ';

	   	if (($_POST['DATA_PAGAMENTO_INICIAL']!='') and ($_POST['DATA_PAGAMENTO_FINAL']!=''))
	   		$qryPs1020 .= ' AND (Ps1020.Data_Vencimento between ' . dataAngularToSql($_POST['DATA_PAGAMENTO_INICIAL']) . ' and ' . dataAngularToSql($_POST['DATA_PAGAMENTO_FINAL']) . ') ';

	   	if (($_POST['DATA_CANCELAMENTO_INICIAL']!='') and ($_POST['DATA_CANCELAMENTO_FINAL']!=''))
	   		$qryPs1020 .= ' AND (Ps1020.Data_Cancelamento between ' . dataAngularToSql($_POST['DATA_CANCELAMENTO_INICIAL']) . ' and ' . dataAngularToSql($_POST['DATA_CANCELAMENTO_FINAL']) . ') ';

	   	if ($_POST['TIPO_NOTAS_FISCAIS']=='PF')
	   		$qryPs1020 .= ' AND (Ps1010.Flag_PlanoFamiliar = ' . aspas('S') . ')';
	   	else if ($_POST['TIPO_NOTAS_FISCAIS']=='PJ')
	   		$qryPs1020 .= ' AND (Ps1010.Flag_PlanoFamiliar = ' . aspas('N') . ')';

	   if ($_POST['CHECK_REEMITIR']=='N')
	   		$qryPs1020 .= ' and ((Ps1020.Numero_Nota_Fiscal is null AND (Ps1020.Tipo_Baixa IS NULL OR Ps1020.Tipo_Baixa <> ' . aspas('Q') . ') OR 
	   		                     (Ps1020.Tipo_baixa = ' . aspas('Q') . '))';  // nfse de franquias já possuem um numero de RPS atribuido e não pode ser reemitida, pois o cliente já esta com o recibo que possui o numero do RPS

	   	$qryPs1020 .= ' Order By Ps1020.Numero_Registro';

	}
	else if ($tipoSelecao=='GERACAO_ARQUIVO_NF')
	{

	    $qryPs1020 =   'Select * from vw_cabecalho_nfse 
                        Where (vw_cabecalho_nfse.numero_Rps between ' . numSql($primeiraNota)  . ' And ' . numSql($ultimaNota) . ') And 
                              (vw_cabecalho_nfse.codigo_prefixo = ' . aspas($codigoPrefixo) . ') And 
                              (vw_cabecalho_nfse.Numero_RPS is not null) And (vw_cabecalho_nfse.Numero_Lote_Nfse is not null) And ';

 	    if (($_POST['CODIGO_EMPRESA_INICIAL']!='') and ($_POST['CODIGO_EMPRESA_FINAL']!=''))
	   		$qryPs1020 .= ' (vw_cabecalho_nfse.codigo_identificacao BETWEEN ' . numSql($_POST['CODIGO_EMPRESA_INICIAL']) . 
	   	                    ' and ' . numSql($_POST['CODIGO_EMPRESA_FINAL']) . ') And ';

	   	$qryPs1020 .= ' (vw_cabecalho_nfse.status_nota_fiscal <> ' . aspas('CANCELADA') . ') ';

        $qryPs1020 .= ' Order By cast(vw_cabecalho_nfse.numero_nota_fiscal_sistema as Integer)';

	}

    return  jn_query($qryPs1020);

}




function geraArquivoNotaFiscal($primeiraNf, $ultimaNf, $codigoPrefixo)
{

	/* PENDENCIA, CRIAR ROTINA PARA GERAR O NOME DO ARQUIVO CORRETO */
	$nomeArquivoProcesso = retornaValorConfiguracao('PD_DIR_ARQUIVOS_NF') . 'NF_AJUSTAR_NOME.txt';

	$nomeCaminhoArquivo  = retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . $nomeArquivoProcesso;

	criaDiretorioSeNaoExistir(retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . retornaValorConfiguracao('PD_DIR_ARQUIVOS_NF'));

	$arquivoRemessa      = fopen($nomeCaminhoArquivo, 'w');

	$linhaArquivo      = '';
	$valorTotalTitulos = 0;
	$quantidadeTitulos = 0;
    $linhaLogProcesso  = '';

	/* ------------------------------------------------------------------------ */
	/* 				GERA HEADER/CABEÇALHO DO ARQUIVO 							*/
	/* ------------------------------------------------------------------------ */

	$resInformacoesLayout = selecionaConfiguracoesLayout(' Where (TIPO_ESTRUTURA = ' . aspas('H') . ')');
	$tipoRegistro         = -1;

	// Aqui faço a query inicial pois precisarei utilizar alguns campos da empresa que vem na view.
	// Mais abaixo vou fazer a query novamente.

    $resPs1020            = selecionaFaturasParaNotasFiscais('GERACAO_ARQUIVO_NF', $primeiraNf, $ultimaNf, $codigoPrefixo);
    $rowPs1020            = jn_fetch_object($resPs1020);

	while ($rowInformacaoLayout = jn_fetch_object($resInformacoesLayout))
	{
		if ($tipoRegistro==-1)
		{
			$tipoRegistro = $rowInformacaoLayout->TIPO_REGISTRO;				
		}
		else if ($tipoRegistro!=$rowInformacaoLayout->TIPO_REGISTRO)
		{
			if ($linhaArquivo!='')
			{
				fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);
				$linhaArquivo = '';
			}

			$tipoRegistro = $rowInformacaoLayout->TIPO_REGISTRO;				
		}

		$campoRetornado = retornaCampoTratadoLayout($rowInformacaoLayout,$rowPs1020,0,0,'');
		$html          .= $campoRetornado;
		$linhaArquivo  .= $campoRetornado;
	}

	if ($linhaArquivo!='')
		fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);
	

	/* ------------------------------------------------------------------------ */
	/* 				GERA DETALHE, REGISTROS DO ARQUIVO 							*/
	/* ------------------------------------------------------------------------ */

    $resPs1020 					   = selecionaFaturasParaNotasFiscais('GERACAO_ARQUIVO_NF', $primeiraNf, $ultimaNf,$codigoPrefixo);

	$resInformacoesLayoutPadrao    = selecionaConfiguracoesLayout(' Where (TIPO_ESTRUTURA = ' . aspas('D') . ')');
	$linhaArquivo 				   = '';

	$arrayInformacaoLayout 		   = array();

	while ($rowInformacaoLayout = jn_fetch_object($resInformacoesLayoutPadrao))
	{
		$arrayInformacaoLayout[] = $rowInformacaoLayout;		
	}

	//

	$linhaLogProcesso = '';
	//

	$registrosProcessados = 0;

	while ($rowPs1020 = jn_fetch_object($resPs1020))
	{

		$tipoRegistro         = -1;

		foreach($arrayInformacaoLayout as $itemArrayInformacao)
		{
			if ($tipoRegistro==-1)
			{
				$tipoRegistro = $itemArrayInformacao->TIPO_REGISTRO;				
			}
			else if ($tipoRegistro!=$itemArrayInformacao->TIPO_REGISTRO)
			{
				if ($linhaArquivo!='')
				{
					fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);
					$linhaArquivo = '';
				}

				$tipoRegistro = $itemArrayInformacao->TIPO_REGISTRO;				
			}

			$campoRetornado = retornaCampoTratadoLayout($itemArrayInformacao, $rowPs1020, 0,0, '');
			$html          .= $campoRetornado;
			$linhaArquivo  .= $campoRetornado;
		}

		$valorTotalTitulos+= $rowPs1020->VALOR_TOTAL_NF;
		$quantidadeTitulos++;

		if ($linhaArquivo!='')
		{
			fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);
			$linhaArquivo = '';
		}

		$linhaLogProcesso = '';
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->NUMERO_REGISTRO,14);
	    $linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->CODIGO_IDENTIFICACAO,17);
		$linhaLogProcesso .= adicionaCampoLinhaLog(copyDelphi($rowPs1020->RAZAO_SOCIAL_TOMADOR,1,37),40);
		$linhaLogProcesso .= adicionaCampoLinhaLog(sqlToData($rowPs1020->DATA_VENCIMENTO),14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->VALOR_FATURA,14,'N');
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->NUMERO_NOTA_FISCAL,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog('NF EMITIDA NO ARQUIVO',50);

		registraLogEvolucaoProcesso($_POST['ID_PROCESSO'],$linhaLogProcesso);

		escreveArquivoLogProcesso('L',$linhaLogProcesso);

		$registrosProcessados++;

		if ($_POST['FLAG_ATUALIZAR_EVOLUCAO_PROCESSO']=='S')
		{
			atualizaStatusProcesso($_POST['ID_PROCESSO'],'Gerados: ' . $registrosProcessados . ' de: ' . $totalRegistrosAProcessar);
		}	

		$linhaLogProcesso = '';

	}


	/* ------------------------------------------------------------------------ */
	/* 				GERA TRAYLLER, RODAPE DO ARQUIVO 							*/
	/* ------------------------------------------------------------------------ */

	$resInformacoesLayout = selecionaConfiguracoesLayout(' Where (TIPO_ESTRUTURA = ' . aspas('T') . ')');
	$tipoRegistro         = -1;
	$linhaArquivo         = '';

	while ($rowInformacaoLayout = jn_fetch_object($resInformacoesLayout))
	{
		if ($tipoRegistro==-1)
		{
			$tipoRegistro = $rowInformacaoLayout->TIPO_REGISTRO;				
		}
		else if ($tipoRegistro!=$rowInformacaoLayout->TIPO_REGISTRO)
		{
			if ($linhaArquivo!='')
			{
				fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);
				$linhaArquivo = '';
			}
				$tipoRegistro = $rowInformacaoLayout->TIPO_REGISTRO;				
		}

		$campoRetornado = retornaCampoTratadoLayout($rowInformacaoLayout,'',$valorTotalTitulos, $quantidadeTitulos, '');
		$html          .= $campoRetornado;
		$linhaArquivo  .= $campoRetornado;
	}

	if ($linhaArquivo!='')
	   fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);

	fclose($arquivoRemessa);

	//

}




function reservaNumeroRps($codigoPrefixo)
{

	jn_query('UPDATE PS1077 SET Numero_Rps_Atual = (Numero_Rps_Atual + 1) WHERE Codigo_Prefixo = ' . aspas($codigoPrefixo));

	$rowTemp = qryUmRegistro('SELECT Numero_Rps_Atual FROM PS1077 WHERE Codigo_Prefixo = ' . aspas($codigoPrefixo));

	return $rowTemp->NUMERO_RPS_ATUAL;

}  




function estornarGeracaoNotaFiscal($primeiraNf, $ultimaNf, $codigoPrefixo, $comboTipoProcesso)
{


	$numeroLote = $_POST['NUMERO_LOTE_NFSE'];

	if ($comboTipoProcesso=='ESTORNO_VOLTAR')
	{
		$rowPs1056 = qryUmRegistro('Select NUMERO_REGISTRO From Ps1056 Where (Numero_Lote_Nfse > ' . numSql($numeroLote) . ') And 
                                                               (Codigo_Prefixo = ' . aspas($codigoPrefixo) . ')');
		if ($rowPs1056->NUMERO_REGISTRO!='')
		{
			$detalheConclusao = 'Não é possível utilizar a opção de "voltar" a numeração visto que o lote informado não é o ultimo lote gerado. 
	                             Esta opção é apenas possível quando o estorno for do ultimo lote gerado.';
			registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',$detalheConclusao,'', '');
	        return;
	    }
	}

	$resPs1056 = jn_query('Select * From Ps1056 Where (Numero_Lote_Nfse = ' . numSql($numeroLote) . ') Order By Numero_Nf_Sem_Formatar');

    $numeroPrimeiraNf    = '';
    $codigoPrefixo       = '';
    $qtNotasEstornadas   = 0;

    while ($rowPs1056 = jn_fetch_object($resPs1056))
    {

    	if ($numeroPrimeiraNf=='')
    	{
	    	$numeroPrimeiraNf    = $rowPs1056->NUMERO_NF_SEM_FORMATAR;
	    	$codigoPrefixo       = $rowPs1056->CODIGO_PREFIXO;
	    }

        jn_query('Update Ps1020 Set Numero_Nota_Fiscal = Null Where (Numero_Nota_Fiscal = ' . aspas($rowPs1056->NUMERO_RPS) . ')');

        jn_query('Update Ps1020 Set Numero_Nota_Emissao = Null Where (Numero_Nota_Emissao = ' . aspas($rowPs1056->NUMERO_RPS) . ')');

        jn_query('Update Ps1020 Set Numero_Nota_Cancelamento = Null Where (Numero_Nota_Cancelamento = ' . aspas($rowPs1056->NUMERO_RPS) . ')');

        jn_query('Update PS1056 Set DESCRICAO_OBSERVACAO = ' . aspas('ESTORNO DE LOTE FEITO PELO OPERADOR') . ', 
                  Status_Nota_Fiscal = ' . aspas('CANCELADA') . ' 
                  where (Numero_Nota_Fiscal = ' . aspas($rowPs1056->NUMERO_RPS) . ') And (Numero_Rps = ' . aspas($rowPs1056->NUMERO_RPS) . ') And 
                        (Status_Nota_Fiscal = ' . aspas('NFSE-EMITIDA') . ')');

	    $qtNotasEstornadas++;

    }


	if ($comboTipoProcesso=='ESTORNO_VOLTAR')
	{
       if ((testaInt($numeroPrimeiraNf)) And (testaInt($codigoPrefixo)))
       {
        	jn_query('Update Ps1077 Set Numero_Rps_Atual = (' . numSql($numeroPrimeiraNf) . '-1) 
                      Where (Codigo_Prefixo = ' . aspas($codigoPrefixo) . ')');
       }
    }   

	$detalheConclusao = 'Ok, processo de estorno realizado com sucesso. Foram estornadas: ' . $qtNotasEstornadas;
	registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',$detalheConclusao,'', '');

}



?>