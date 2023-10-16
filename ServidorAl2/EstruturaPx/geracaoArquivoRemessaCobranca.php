<?php

require_once('../lib/base.php');
//require('../private/autentica.php');
require_once('../EstruturaPrincipal/processoPx.php');
//require('../lib/sysutils01.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

set_time_limit(0);

//pr($_POST['ID_PROCESSO']);

global $identificacaoRegistro; 
$identificacaoRegistro = 1;

global $totalRegistrosAProcessar; 
$totalRegistrosAProcessar = 0;

global $rowPs7300; 
$resPs7300            = selecionaContaBancaria(' where numero_conta_corrente = ' . aspas($_POST['AUTOCOMP_NUMERO_CONTA_CORRENTE']));
$rowPs7300            = jn_fetch_object($resPs7300);

global $PercentualMulta;
$PercentualMulta      = retornaValorConfiguracao('PERCENTUAL_MULTA_PADRAO');

global $PercentualMoraDiaria;
$PercentualMoraDiaria = retornaValorConfiguracao('PERCENTUAL_MORA_DIARIA');

global $arquivoLogProcesso;

/* PENDENCIA, CRIAR ROTINA PARA GERAR O NOME DO ARQUIVO CORRETO */
$nomeArquivoProcesso = retornaValorConfiguracao('PD_DIR_ARQUIVOS_REMESSA') . 'arquivoRemessa.txt';

$nomeCaminhoArquivo  = retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . $nomeArquivoProcesso;

criaDiretorioSeNaoExistir(retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . retornaValorConfiguracao('PD_DIR_ARQUIVOS_REMESSA'));

$arquivoRemessa      = fopen($nomeCaminhoArquivo, 'w');

$nomearquivoLogProcesso = escreveArquivoLogProcesso('C',$_POST['ID_PROCESSO']);

$linhaArquivo = '';
$valorTotalTitulos = 0;
$quantidadeTitulos = 0;
$linhaLogProcesso  = '';



echo apresentaMensagemInicioProcesso();


	/* ------------------------------------------------------------------------ */
	/* 				GERA HEADER/CABEÇALHO DO ARQUIVO 							*/
	/* ------------------------------------------------------------------------ */


	$resInformacoesLayout = selecionaConfiguracoesLayout(' Where (TIPO_ESTRUTURA = ' . aspas('H') . ')');
	$tipoRegistro         = -1;

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

		$campoRetornado = retornaCampoTratadoLayout($rowInformacaoLayout,'',0,0,$rowPs7300);
		$html          .= $campoRetornado;
		$linhaArquivo  .= $campoRetornado;
	}

	fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);
	


	/* ------------------------------------------------------------------------ */
	/* 				GERA DETALHE, REGISTROS DO ARQUIVO 							*/
	/* ------------------------------------------------------------------------ */



	$resPs1020            		   = selecionaFaturasConformeParametros();

	$resInformacoesLayoutPadrao    = selecionaConfiguracoesLayout(' Where (TIPO_ESTRUTURA = ' . aspas('D') . ')');
	$linhaArquivo 				   = '';

	$arrayInformacaoLayout 		   = array();

	while ($rowInformacaoLayout = jn_fetch_object($resInformacoesLayoutPadrao))
	{
		$arrayInformacaoLayout[] = $rowInformacaoLayout;		
	}


	/* Para percorrer é simples, basta percorrer o array. Segue exemplo comentado abaixo.
	foreach($arrayInformacaoLayout as $itemArrayInformacao)
	{
		pr($itemArrayInformacao->TIPO_REGISTRO);
	}
	*/

	//

	$linhaLogProcesso .= adicionaCampoLinhaLog('Registro',14);
    $linhaLogProcesso .= adicionaCampoLinhaLog('Codigo',17);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Nome do pagador',40);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Vencimento',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Vl fatura',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Convenio',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Correcao',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Adicional',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Vl Outros',14);
	$linhaLogProcesso .= adicionaCampoLinhaLog('Emissao',14);

	escreveArquivoLogProcesso('L',$linhaLogProcesso);
	$linhaLogProcesso = '';
	//

	$registrosProcessados = 0;

	while ($rowPs1020 = jn_fetch_object($resPs1020))
	{

	   if ($_POST['FLAG_IMPEDIR_INADIMPLENTE']!='S') 
	   {
 			/* *** COLOCAR AQUI VALIDAÇÃO DE INADIMPLENTE ***/
 			/* PENDENCIA */
   	   }

	   if (($_POST['FLAG_EXCLUIDOS']!='S') && ($rowPs1020->DATA_EXCLUSAO!=''))
	   {
   			continue;
	   }

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

			$campoRetornado = retornaCampoTratadoLayout($itemArrayInformacao, $rowPs1020, 0,0, $rowPs7300);
			$html          .= $campoRetornado;
			$linhaArquivo  .= $campoRetornado;
		}

		$valorTotalTitulos+= $rowPs1020->VALOR_FATURA;
		$quantidadeTitulos++;

		if ($linhaArquivo!='')
		{
			fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);
			$linhaArquivo = '';
		}

		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->NUMERO_REGISTRO,14);

		if ($rowPs1020->FLAG_PLANOFAMILIAR=='S')
		   $linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->CODIGO_ASSOCIADO,17);
		else
		   $linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->CODIGO_EMPRESA,17);

		$linhaLogProcesso .= adicionaCampoLinhaLog(copyDelphi($rowPs1020->NOME_PAGADOR,1,37),40);
		$linhaLogProcesso .= adicionaCampoLinhaLog(sqlToData($rowPs1020->DATA_VENCIMENTO),14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->VALOR_FATURA,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->VALOR_CONVENIO,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->VALOR_CORRECAO,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->VALOR_ADICIONAL,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog($rowPs1020->VALOR_OUTROS,14);
		$linhaLogProcesso .= adicionaCampoLinhaLog(sqlToData($rowPs1020->DATA_EMISSAO),14);

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

		$campoRetornado = retornaCampoTratadoLayout($rowInformacaoLayout,'',$valorTotalTitulos, $quantidadeTitulos, $rowPs7300);
		$html          .= $campoRetornado;
		$linhaArquivo  .= $campoRetornado;
	}

	fwrite($arquivoRemessa, $linhaArquivo . PHP_EOL);

	fclose($arquivoRemessa);

	escreveArquivoLogProcesso('F','');

	//

	/*$queryRelatorioProcessamento = 'Select Coalesce(Ps1020.Codigo_Associado, Ps1010.Codigo_Empresa) CODIGO_PAGADOR, 
										   Coalesce(Ps1000.Nome_Associado,Ps1010.Nome_Empresa) Nome_Pagador,  
									       Ps1020.Valor_Convenio, Ps1020.Valor_Correcao,
	                                       Ps1020.Valor_Adicional, Ps1020.Valor_Outros, Ps1020.Valor_Fatura, Ps1020.Data_Emissao, Ps1020.Data_Vencimento, 
	                                       Ps1020.Descricao_Observacao   
	                                       From Ps1020
							               Left Outer Join Ps1010 On (Coalesce(Ps1020.Codigo_Empresa, 400) = Ps1010.Codigo_Empresa) 
							               Left Outer join Ps1000 On (Ps1020.Codigo_Associado = Ps1000.Codigo_Associado)
	                                       Where Ps1020.ID_INSTANCIA_PROCESSO = ' . aspas($_POST['ID_PROCESSO']);*/

	$nomearquivoRelatorioProcesso = $nomearquivoLogProcesso;	                                       

	$detalheConclusao = '';
	$detalheConclusao = 'Foram gerados: ' . $registrosProcessados . ' de um total de: ' . $totalRegistrosAProcessar;

	registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',$detalheConclusao,$nomeArquivoProcesso, $nomearquivoLogProcesso,$nomearquivoRelatorioProcesso);

exit;



/* ----------------------------------------------------------------------------------------------------------------------------------------- */

/* FUNCÕES PARA TRATAMENTO DAS INFORMACOES */

/* ----------------------------------------------------------------------------------------------------------------------------------------- */


function selecionaFaturasConformeParametros()
{

	global $totalRegistrosAProcessar;

	$padraoTipoArquivo = $_POST['COMBO_TIPO_ARQUIVO'];

	$queryPs1020 = 'Select

					top 50000

					PS1020.VALOR_DESCONTO     , PS1020.DATA_EMISSAO,      PS1020.DATA_VENCIMENTO    ,  ' . 
           	        iif($_POST['FLAG_SUBTRAIR_VALOR_DESC_ARQ_REMESSA']=='S',' (PS1020.VALOR_FATURA - COALESCE(PS1020.VALOR_DESC_ARQ_REMESSA,0)) VALOR_FATURA, ','PS1020.VALOR_FATURA, ') . 
       			   'PS1020.VALOR_ADICIONAL    , PS1020.VALOR_CORRECAO,      PS1020.CODIGO_ASSOCIADO   , PS1020.CODIGO_EMPRESA, PS1020.NUMERO_NOTA_FISCAL, 
           			PS1020.NUMERO_REGISTRO    , PS1020.VALOR_CONVENIO, PS1020.VALOR_OUTROS, PS1020.CODIGO_IDENTIFICACAO_FAT,
           			PS1020.PERCENTUAL_CORRECAO, PS1010.FLAG_PLANOFAMILIAR, PS1002.CODIGO_BANCO, PS1002.NUMERO_CONTA, PS1002.NUMERO_AGENCIA, 
           			PS1020.DATA_EMISSAO_BOLETO, PS1020.DATA_EMISSAO_ARQUIVO, PS1020.NUMERO_PARCELA,
           			PS1002.MENSAGEM_ESPECIFICA_BOLETO, PS1020.MES_ANO_REFERENCIA, PS1020.FLAG_MARCADO, 
           			COALESCE(PS1000.DATA_EXCLUSAO, PS1010.DATA_EXCLUSAO) DATA_EXCLUSAO, 
                  	PS1020.VALOR_CSLL, PS1020.VALOR_PIS, PS1020.VALOR_COFINS, 
           			PS1020.NUMERO_NOTA_FISCAL, 
                    COALESCE(PS1001.ENDERECO_COBRANCA,PS1001.ENDERECO) ENDERECO, COALESCE(PS1001.BAIRRO_COBRANCA, PS1001.BAIRRO) BAIRRO, COALESCE(PS1001.CIDADE_COBRANCA,PS1001.CIDADE) CIDADE, 
                    COALESCE(PS1001.ESTADO_COBRANCA,PS1001.ESTADO) ESTADO, COALESCE(PS1001.CEP_COBRANCA,PS1001.CEP) CEP, PS1001.OBSERVACAO_ENDERECO, PS1001.ENDERECO_EMAIL, 
                    PS1010.NUMERO_CNPJ, CAST(0 AS INTEGER) CODIGO_SEQUENCIAL, 
                    PS1002.MENSAGEM_ESPECIFICA_BOLETO, PS1002.NOME_CONTRATANTE, COALESCE(PS1002.NUMERO_CPF_CONTRATANTE,PS1000.NUMERO_CPF) NUMERO_CPF, PS1010.CODIGO_GRUPO_PESSOAS, PS1010.FLAG_PLANOFAMILIAR, 
                    PS1002.FLAG_DEBITO_AUTOMATICO, PS1002.BANDEIRA_CARTAO_CREDITO, PS1002.NUMERO_CARTAO_CREDITO, 
                    case 
                        When Ps1010.FLAG_PLANOFAMILIAR = ' . aspas('S') . ' then COALESCE(PS1002.NOME_CONTRATANTE,PS1000.NOME_ASSOCIADO) 
                        else PS1010.NOME_EMPRESA 
                    end NOME_PAGADOR, 
                    PS1020.DATA_VENCIMENTO + 1 DATA_BASE_JUROS, 
                    "CNPJ" TIPO_DOCUMENTO, PS1002.NUMERO_CONTRATO, PS1002.NUMERO_AGENCIA, PS1002.NUMERO_CONTA, PS1002.CODIGO_BANCO, PS1002.DIA_VENCIMENTO ' . 
                    
                    iif($_SESSION['codigoSmart'] = '3423',', PS1002.FLAG_RECEBE_BOLETO_EMAIL', '') .   //Plena
                    iif($_SESSION['codigoSmart'] = '4200',', PS1002.FLAG_DESCONTO_PONTUALIDADE', '') .   //Propulsao
                    ', PS1002.FLAG_OMITIR_REMESSA ' .

		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'L4'),', PS1002.FLAG_CADASTRO_OPTANTE ', '') .   //Plena
		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'M2'),', PS1002.FLAG_CADASTRO_OPTANTE ', '') .   //Plena
		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'M5'),', PS1002.FLAG_CADASTRO_OPTANTE, PS1002.CONVENIO, PS1002.NUMERO_CONTA ', '') .   //Vileve
		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'M6'),', Ps1002.Flag_Cadastro_Optante, Ps1002.Convenio, Ps1002.numero_conta ', '') .   //Vileve
		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'M7'),', PS1002.CONVENIO, PS1002.NUMERO_CONTA ', '') .   //Vileve
		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'M8'),', PS1002.CONVENIO, PS1002.NUMERO_CONTA ', '') .   //Vileve
		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'M4'),', PS1002.CONVENIO, PS1002.NUMERO_CONTA ', '') .   //Vileve
		            iif((substrDelphi($padraoTipoArquivo,1,2) == 'M9'),', PS1002.FLAG_CADASTRO_OPTANTE, PS1002.CONVENIO, PS1002.NUMERO_CONTA ', '') .   //Vileve

		           '  From Ps1020
		              Left Outer Join Ps1010 On (Coalesce(Ps1020.Codigo_Empresa, 400) = Ps1010.Codigo_Empresa) 
		              Left Outer join Ps1000 On (Ps1020.Codigo_Associado = Ps1000.Codigo_Associado)
					  LEFT OUTER JOIN PS1001 ON (COALESCE(PS1020.CODIGO_ASSOCIADO,CAST(PS1010.CODIGO_EMPRESA AS VARCHAR(15))) = COALESCE(PS1001.CODIGO_ASSOCIADO, CAST(PS1001.CODIGO_EMPRESA AS VARCHAR(15))))
					  LEFT OUTER JOIN PS1002 ON (COALESCE(PS1020.CODIGO_ASSOCIADO,CAST(PS1010.CODIGO_EMPRESA AS VARCHAR(15))) = COALESCE(PS1002.CODIGO_ASSOCIADO, CAST(PS1002.CODIGO_EMPRESA AS VARCHAR(15))))

		           Where 
	           
		              (Ps1020.Data_Vencimento Between ' . DataToSql(sqlToData($_POST['DATA_VENCIMENTO_INICIAL'])) . ' And ' . DataToSql(sqlToData($_POST['DATA_VENCIMENTO_FINAL'])) . ') And 
		              (Ps1020.Data_Emissao Between '  . DataToSql(sqlToData($_POST['DATA_EMISSAO_INICIAL'])) . ' And ' . DataToSql(sqlToData($_POST['DATA_EMISSAO_FINAL'])) . ') ';

				   if ($_POST['COMBO_TIPO_FATURA']!='T') 
      				 	$queryPs1020.= ' and (Ps1020.Tipo_Registro = ' . aspas($_POST['COMBO_TIPO_FATURA']) . ') ';

				   if ($_POST['FLAG_DEBITO_CONTA']=='S') 
      				 	$queryPs1020.= ' and (Ps1002.Flag_Debito_Automatico = ' . aspas('S') . ')';
				   else 
      				 	$queryPs1020.= ' and ((Ps1002.Flag_Debito_Automatico <> ' . aspas('S') . ') Or (Ps1002.Flag_Debito_Automatico Is Null)) ';

				   if ($_POST['RADIO_TIPOS_PESSOAS']=='F') 
      				 	$queryPs1020.= ' and (Ps1010.Flag_PlanoFamiliar = ' . aspas('S') . ')';
				   else if ($_POST['RADIO_TIPOS_PESSOAS']=='J')  
      				 	$queryPs1020.= ' and (Ps1010.Flag_PlanoFamiliar = ' . aspas('N') . ') ';

				   if ($_POST['COMBO_TIPO_ARQUIVO_REMESSA']=='0') 
            			$queryPs1020.= ' and (Ps1020.Data_Cancelamento is null) ';

				   if ($_POST['AUTOCOMP_CODIGO_GRUPO_CONTRATO']!='') 
            			$queryPs1020.= ' and (Ps1000.Codigo_Grupo_Contrato = ' . aspas($_POST['AUTOCOMP_CODIGO_GRUPO_CONTRATO']) . ') ';

				   if ($_POST['AUTOCOMP_CODIGO_GRUPO_FATURAMENTO']!='') 
            			$queryPs1020.= ' and (Ps1000.Codigo_Grupo_Faturamento = ' . aspas($_POST['AUTOCOMP_CODIGO_GRUPO_FATURAMENTO']) . ') ';

				   if ($_POST['FLAG_FATURAS_BANCO_SELECIONADO']=='S') 
              			$queryPs1020.= ' and (Ps1020.Codigo_Banco = ' . numSql($_POST['AUTOCOMP_CODIGO_BANCO']) . ') ';

				   if ($_POST['FLAG_GERAR_DATA_EMISSAO_ARQUIVO']!='S') 
              			$queryPs1020.= ' and (Ps1020.Data_Emissao_Arquivo is null) ';

				   if ($_POST['FLAG_GERAR_DATA_EMISSAO_BOLETO']!='S') 
              			$queryPs1020.= ' and (Ps1020.Data_Emissao_Boleto is null) ';

				   if ($_POST['FLAG_GERAR_NEGOCIACAO']=='S') 
              			$queryPs1020.= ' and (Ps1020.Tipo_Registro = ' . aspas('N') . ') ';

				   if ($_POST['FLAG_VALIDA_BOLETO_EMPRESA']=='S') 
              			$queryPs1020.= ' and (Ps1010.Flag_Emite_Boleto = ' . aspas('S') . ') ';

				   if (($_POST['PARCELA_INICIAL'] <> 1) && ($_POST['PARCELA_FINAL'] <> 99999))
						$queryPs1020.= ' and (Ps1020.Numero_Parcela Between ' . numSql($_POST['PARCELA_INICIAL']) . ' And ' . numSql($_POST['PARCELA_FINAL']) . ') ';

				   if ($_POST['COMBO_TIPO_ARQUIVO_REMESSA'] >= 1) 
				   { 
				         if ((testaData($_POST['DATA_BAIXA_INICIAL'])) and (testaData($_POST['DATA_BAIXA_FINAL'])))
							$queryPs1020.= ' and (Ps1020.Data_Baixa_Remessa between ' .  DataToSql(sqlToData($_POST['DATA_BAIXA_INICIAL'])) . ' and ' . 
						                    DataToSql(sqlToData($_POST['DATA_BAIXA_FINAL'])) . ') ';

				         if ((testaData($_POST['DATA_PRORROGACAO_INICIAL'])) and (testaData($_POST['DATA_PRORROGACAO_FINAL'])))
							$queryPs1020.= ' and (Ps1020.Data_Baixa_Remessa between ' .  DataToSql(sqlToData($_POST['DATA_PRORROGACAO_INICIAL'])) . ' and ' . 
						                    DataToSql(sqlToData($_POST['DATA_PRORROGACAO_FINAL'])) . ') ';
				   }

				   if ($_POST['CODIGO_CONVENIO'] >= 1) 
				   {
						$queryPs1020.= ' and (Ps1002.convenio = ' . aspas($_POST['CODIGO_CONVENIO']) . ')';
				   }

				   $queryPs1020.= ' Order By Codigo_Associado, Codigo_Empresa, Data_Vencimento';

				   //	


	$totalRegistrosAProcessar = totalRegistrosResult($queryPs1020);

    return jn_query($queryPs1020);
	
}



/*
function selecionaConfiguracoesLayout($criterio){

	$queryLayout = 'select CFGLAYOUTS_CAMPOS.NUMERO_REGISTRO, CFGLAYOUTS_CAMPOS.TIPO_REGISTRO, CFGLAYOUTS_CAMPOS.CODIGO_LAYOUT, 
						   CFGLAYOUTS_CAMPOS.NOME_CAMPO, CFGLAYOUTS_CAMPOS.POSICAO_INICIAL, CFGLAYOUTS_CAMPOS.TAMANHO_CAMPO, 
						   CFGLAYOUTS_CAMPOS.INFORMACAO_CAMPO, CFGLAYOUTS_CAMPOS.TIPO_CAMPO, CFGLAYOUTS_CAMPOS.FUNCAO_GERACAO, 
						   CFGLAYOUTS_CAMPOS.PARAMETROS_ADICIONAIS, CFGLAYOUTS_CAMPOS.MASCARA_CAMPO, CFGLAYOUTS_CAMPOS.TIPO_ESTRUTURA 
						   from CfgLayouts
				    inner join CfgLayouts_Campos on (CfgLayouts.codigo_Layout = CfgLayouts_Campos.Codigo_Layout) ' .
				    $criterio . 
				    ' AND CFGLAYOUTS.PADRAO_ARQUIVO LIKE ' . aspas($_POST['COMBO_TIPO_ARQUIVO'] . '%') .
				    ' order by TIPO_REGISTRO, POSICAO_INICIAL ';

    return jn_query($queryLayout);

}
*/


function selecionaContaBancaria($criterio){

	$banco = 'select * from ps7300 ' . $criterio;

    return jn_query($banco);

}



/*
function retornaCampoTratadoLayout($rowInformacaoLayout, $tabelaDadoPs1020, $valorTotalRegistros = 0, $quantidadeTitulos = 0){

	global $identificacaoRegistro; 
	global $rowPs7300; 

	$stringRetornar = '';

	// -------------------------------------------------------------------------------
	// PRIMEIRO GERA O DADO, SEJA LITERAL, DO BANCO DE DADOS OU POR MEIO DE UM CÁLCULO 
	// ------------------------------------------------------------------------------- 


	if ((strpos($rowInformacaoLayout->TIPO_CAMPO,'JSON_LITERAL')!==false)||(strpos($rowInformacaoLayout->TIPO_CAMPO,'JSON_CAMPO_DB')!==false))
	{
		$json 		= json_decode($rowInformacaoLayout->INFORMACAO_CAMPO);

		foreach ($json as $key => $value)
		{
			$campoValidar = $value->NOME_CAMPO_VALIDAR;

			if ($tabelaDadoPs1020->$campoValidar==$value->VALOR_VALIDAR)
			{
				$stringRetornar = $value->VALOR_RETORNAR;
			}
		}

		if (strpos($rowInformacaoLayout->TIPO_CAMPO,'JSON_CAMPO_DB')!==false)
		{
			$campo = explode('.',$stringRetornar);

			if (strtoupper($campo[0])=='PS1020')
			{
	   		   $nomeCampo      = $campo[1];
	   		   $stringRetornar = $tabelaDadoPs1020->$nomeCampo;
			}
			else if (strtoupper($campo[0])=='PS7300')
			{
	   		   $nomeCampo      = $campo[1];
	   		   $stringRetornar = $rowPs7300->$nomeCampo;
			}
		}
	}
	else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'LITERAL')!==false)
	{
		$stringRetornar = $rowInformacaoLayout->INFORMACAO_CAMPO;
	}
	else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'CAMPO_DB')!==false)
	{

		$campo = explode('.',$rowInformacaoLayout->INFORMACAO_CAMPO);

		if (strtoupper($campo[0])=='PS1020')
		{
   		   $nomeCampo      = $campo[1];
   		   $stringRetornar = $tabelaDadoPs1020->$nomeCampo;
		}
		else if (strtoupper($campo[0])=='PS7300')
		{
   		   $nomeCampo      = $campo[1];
   		   $stringRetornar = $rowPs7300->$nomeCampo;
		}


		// Cálculo de modulos, neste caso passar o valor do Tipo_campo como "CAMPO_DB" E "..., MODULO_10, MODULO_11, MODULO_11_INVERTIDO..."
	 	if (strpos($rowInformacaoLayout->TIPO_CAMPO,'MODULO_10')!==false)
	 	{
   		   $stringRetornar = modulo_10($stringRetornar);
	 	}
	 	else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'MODULO_11')!==false)
	 	{
   		   $stringRetornar = modulo_11($stringRetornar);
	 	}
	 	else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'MODULO_11_INVERTIDO')!==false)
	 	{
   		   $stringRetornar = modulo_11_invertido($stringRetornar);
	 	}
		
	}


	if (strpos($rowInformacaoLayout->INFORMACAO_CAMPO,'[DATE]')!==false)
	{
		$stringRetornar = date('d/m/Y');

   		if (strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAAAA')!==false)
   		{
			$stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 6, 4); 
		}
		else
		{
			$stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 8, 2); 
		}
		return $stringRetornar;
	}


	// -------------------------------------------------------------------- //


	if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'VALOR_TOTAL_TITULOS')!==false)
	{
   		$stringRetornar = $valorTotalRegistros;
	}
	else if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'QUANTIDADE_TOTAL_TITULOS')!==false)
	{
   		$stringRetornar = $quantidadeTitulos;
	}


	if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'JUROS_MORA_DIARIO')!==false)
	{
   		$stringRetornar = retornaValorJurosMoraDiaria($tabelaDadoPs1020, 'MORA');
	}


	if ((strpos($rowInformacaoLayout->FUNCAO_GERACAO,'MASCARADATA')!==false)||
		(strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAA')!==false)||
	    (strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAAAA')!==false))
	{
   		$stringRetornar = sqlToData($stringRetornar);

   		if (strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAAAA')!==false)
   		{
			$stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 6, 4); 
		}
		else
		{
			$stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 8, 2); 
		}
	}

	if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'IDENTIFICACAO_REGISTRO_STRZERO')!==false)
	{
		$stringRetornar = strZero($identificacaoRegistro,$rowInformacaoLayout->TAMANHO_CAMPO);
		$identificacaoRegistro++;
	}


	if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'MASCARAVALOR')!==false)
	{
		$stringRetornar = str_replace('.', '', $stringRetornar);
		$stringRetornar = str_replace(',', '', $stringRetornar);
		$stringRetornar = strZero($stringRetornar,$rowInformacaoLayout->TAMANHO_CAMPO);
	}
	else if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'STRZERO')!==false) // Aqui é um Else if, pq nao posso chamar novamente o strzero se for mascara valor, pois a mascaravalor ja poe o strzero
	{
		$stringRetornar = strZero($stringRetornar,$rowInformacaoLayout->TAMANHO_CAMPO);
	}

	if (strlen($stringRetornar) < $rowInformacaoLayout->TAMANHO_CAMPO)
	{
		$stringRetornar = completaString($stringRetornar,$rowInformacaoLayout->TAMANHO_CAMPO);
	}
	else if (strlen($stringRetornar) > $rowInformacaoLayout->TAMANHO_CAMPO)
	{
		$stringRetornar = copyDelphi($stringRetornar,1,$rowInformacaoLayout->TAMANHO_CAMPO);
	}

	return $stringRetornar;

}

*/


function retornaValorJurosMoraDiaria($Ps1020, $tipo){

	global $PercentualMulta;
	global $PercentualMoraDiaria;

	$valorCalculado = 0;

	if ($tipo=='MORA')
	    $valorCalculado = $Ps1020->VALOR_FATURA * $PercentualMoraDiaria;
	else if ($tipo=='MULTA')
	    $valorCalculado = $Ps1020->VALOR_FATURA * ($PercentualMulta / 100);

	$valorCalculado = round($valorCalculado,2);

	return $valorCalculado;

}



function modulo_10($num) 
{ 
		$numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
            $temp = $numeros[$i] * $fator; 
            $temp0=0;
            foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
		
        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }
		
        return $digito;
		
}



function modulo_11($num, $base=9, $r=0)  
{
    /**
     *   Autor:
     *           Pablo Costa <pablo@users.sourceforge.net>
     *
     *   Função:
     *    Calculo do Modulo 11 para geracao do digito verificador 
     *    de boletos bancarios conforme documentos obtidos 
     *    da Febraban - www.febraban.org.br 
     *
     *   Entrada:
     *     $num: string numérica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Saída:
     *     Retorna o Digito verificador.
     *
     *   Observações:
     *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
     */                                        

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);
        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;
        // Soma dos digitos
        $soma += $parcial[$i];
        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }
        $fator++;
    }

    /* Calculo do modulo 11 */
    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1){
        $resto = $soma % 11;
        return $resto;
    }
}



function modulo_11_invertido($num)  // Calculo de Modulo 11 "Invertido" (com pesos de 9 a 2  e não de 2 a 9)
{ 
   $ftini = 2;
   $fator = $ftfim = 9;
   $soma = 0;
	
   for ($i = strlen($num); $i > 0; $i--) 
   {
      $soma += substr($num,$i-1,1) * $fator;
	  if(--$fator < $ftini) 
	     $fator = $ftfim;
    }
	
    $digito = $soma % 11;
	
	if($digito > 9) 
	   $digito = 0;
	
	return $digito;
}






?>

