<?php
require_once('../lib/base.php');
require_once('../lib/registroBoletoSantander.php');

$registro  = $_GET['registro'];
$codigo    = $_GET['codigo'];
$banco     = $_GET['numeroConta'];

$sqlFatura = "select * from PS1020 where data_pagamento is null and  numero_registro = ".aspas($registro);
		
	$resFatura  = jn_query($sqlFatura);
	if($rowFatura = jn_fetch_object($resFatura)) {
		$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
		$dataVencimento = sqlToData($rowFatura->DATA_VENCIMENTO);//->format('dmY');
		$valorFatura 	= str_replace('.','',str_replace(',','',number_format($rowFatura->VALOR_FATURA,2)));
		$valorFatura    = str_pad($valorFatura, 15, '0', STR_PAD_LEFT );
		$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
		$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
		$nossoNumero = str_pad('0', 13, '0', STR_PAD_LEFT );
		$seuNumero   = str_pad($numeroRegistro, 14, '0', STR_PAD_LEFT );
		
		$sqlbanco = "select * from ps7300 where numero_conta_corrente =".aspas($banco);
		
		if($codigoAssociado != ''){
			$codigoEmpresa = '';
			if($codigoAssociado!=$codigo){
				echo 'ERRO';
				exit;
			}
			$sqlEndereco = "select PS1001.*,PS1000.NOME_ASSOCIADO NOME,PS1000.NUMERO_CPF DOCUMENTO from PS1001 
			inner join PS1000 on PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO
			where PS1000.CODIGO_ASSOCIADO = ".aspas($codigoAssociado);
			
			$tipoDoc = '01';
		}else{
			$codigoAssociado = '';
			if($codigoEmpresa!=$codigo){
				echo 'ERRO';
				exit;
			}
			$sqlEndereco = "select PS1001.*,PS1010.NOME_EMPRESA NOME,PS1010.NUMERO_CNPJ DOCUMENTO from PS1001 
			inner join PS1010 on PS1010.CODIGO_EMPRESA = PS1001.CODIGO_EMPRESA
			where PS1010.CODIGO_EMPRESA = ".aspas($codigoEmpresa);
			$tipoDoc = '02';
		}
		
		$resEndereco  = jn_query($sqlEndereco);
		if($rowEndereco = jn_fetch_object($resEndereco)){
			$nome = jn_utf8_encode($rowEndereco->NOME);
			$documento = $rowEndereco->DOCUMENTO;
			$endereco = jn_utf8_encode(substr($rowEndereco->ENDERECO, 0, 40));
			$bairro = jn_utf8_encode($rowEndereco->BAIRRO);
			$cidade = jn_utf8_encode(substr($rowEndereco->CIDADE, 0, 20));
			$cep = $rowEndereco->CEP;
			$cep = str_replace('.', '', $cep);
			$cep = str_replace('-', '', $cep);
			$estado = jn_utf8_encode($rowEndereco->ESTADO);
		}
		$resBanco  = jn_query($sqlbanco);
		if($rowBanco = jn_fetch_object($resBanco)){
			$convenio = $rowBanco->CODIGO_CEDENTE;
			$codigoEstacao = $rowBanco->CODIGO_ESTACAO;
			$caminhoCertificado = $rowBanco->CAMINHO_CERTIFICADO;
			$senhaCertificado = $rowBanco->SENHA_CERTIFICADO;
			$producao = $rowBanco->FLAG_PRODUCAO_XML;
			if($producao=='S'){
				$producao = true;
			}else{
				$producao = false;
			}
			setAmbinteProducao($producao);
		}
		
		$retorno = RegistraBoleto($codigoAssociado,$codigoEmpresa,$nome,$documento,$endereco,$bairro,$cidade,$cep,$estado,$dataVencimento,$valorFatura,$caminhoCertificado,$senhaCertificado,$convenio,$codigoEstacao,$seuNumero,'');

		if($retorno['STATUS']=='OK'){
				$sqlFatura = 'Update  ps1020 set 
								CODIGO_IDENTIFICACAO_FAT = '.aspas($retorno['DADOS']['titulo']['nossoNumero']).',
								CODIGO_BANCO = '.aspas('033').',
								CODIGO_CARTEIRA = '.aspas('101').',
								CODIGO_ULTIMA_EMISSAO = '.aspas('XML').',
								NUMERO_CONTA_COBRANCA = '.aspas($banco).', 
								NUMERO_LINHA_DIGITAVEL= '.aspas($retorno['DADOS']['titulo']['linDig']).'
							  where numero_registro='.aspas($registro);
				$resFatura  = jn_query($sqlFatura);
		}

		echo json_encode($retorno);
		
		//Array ( [STATUS] => OK [DADOS] => Array ( [codcede] => 000029525 [convenio] => Array ( [codBanco] => 0033 [codConv] => 000029525 ) [descricaoErro] => 00000 - Título registrado em cobrança [dtNsu] => 27092021 [estacao] => PE4P [nsu] => TST0000003 [pagador] => Array ( [bairro] => Centro [cep] => 79904682 [cidade] => PONTA PORa [ender] => RUaa SEte de Setembro,366 [nome] => Diego Ribeiro da Roza [numDoc] => 000000206410107 [tpDoc] => 01 [uf] => MS ) [situacao] => 00 [titulo] => Array ( [aceito] => N [cdBarra] => 03395875900000010159002952500000000040240101 [codPartilha1] => [codPartilha2] => [codPartilha3] => [codPartilha4] => [dtEmissao] => 27092021 [dtEntr] => 27092021 [dtLimiDesc] => [dtLimiDesc2] => 0001-01-01 [dtLimiDesc3] => 0001-01-01 [dtVencto] => 30092021 [especie] => 02 [linDig] => 03399002925250000000600402401012587590000001015 [mensagem] => Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento. [nomeAvalista] => [nossoNumero] => 0000000004024 [numDocAvalista] => 000000000000000 [pcIof] => 00000000 [pcJuro] => 00000 [pcMulta] => 00000 [qtDiasBaixa] => 01 [qtDiasMulta] => 00 [qtDiasProtesto] => 00 [qtdParciais] => 00 [seuNumero] => 900000000000003 [tipoPagto] => 0 [tipoValor] => 0 [tpDesc] => 0 [tpDocAvalista] => 00 [tpProtesto] => 0 [valorMaximo] => 00000000000000000 [valorMinimo] => 00000000000000000 [vlAbatimento] => 000000000000000 [vlDesc] => 000000000000000 [vlDesc2] => 000000000000000 [vlDesc3] => 000000000000000 [vlNominal] => 000000000001015 [vlPartilha1] => 000000000000000 [vlPartilha2] => 000000000000000 [vlPartilha3] => 000000000000000 [vlPartilha4] => 000000000000000 ) [tpAmbiente] => T ) )
		//Array ( [STATUS] => ERRO [DADOS] => Array ( [codcede] => [convenio] => Array ( [codBanco] => 0033 [codConv] => 000029525 ) [descricaoErro] => 00100-DATA EMISSAO MAIOR QUE A DATA VENCIMENTO [dtNsu] => 27092021 [estacao] => PE4P [nsu] => TST0000003 [pagador] => Array ( [bairro] => Centro [cep] => 79904682 [cidade] => PONTA PORa [ender] => RUaa SEte de Setembro,366 [nome] => Diego Ribeiro da Roza [numDoc] => 000000206410107 [tpDoc] => 01 [uf] => MS ) [situacao] => 20 [titulo] => Array ( [aceito] => N [cdBarra] => [codPartilha1] => [codPartilha2] => [codPartilha3] => [codPartilha4] => [dtEmissao] => 27092021 [dtEntr] => [dtLimiDesc] => [dtLimiDesc2] => 0001-01-01 [dtLimiDesc3] => 0001-01-01 [dtVencto] => 10092021 [especie] => 02 [linDig] => [mensagem] => Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento. [nomeAvalista] => [nossoNumero] => 0000000000000 [numDocAvalista] => 000000000000000 [pcIof] => 00000000 [pcJuro] => 00000 [pcMulta] => 00000 [qtDiasBaixa] => 01 [qtDiasMulta] => 00 [qtDiasProtesto] => 00 [qtdParciais] => 00 [seuNumero] => 900000000000003 [tipoPagto] => 0 [tipoValor] => 0 [tpDesc] => 0 [tpDocAvalista] => 00 [tpProtesto] => 0 [valorMaximo] => 00000000000000000 [valorMinimo] => 00000000000000000 [vlAbatimento] => 000000000000000 [vlDesc] => 000000000000000 [vlDesc2] => 000000000000000 [vlDesc3] => 000000000000000 [vlNominal] => 000000000001015 [vlPartilha1] => 000000000000000 [vlPartilha2] => 000000000000000 [vlPartilha3] => 000000000000000 [vlPartilha4] => 000000000000000 ) [tpAmbiente] => T ) )

		
	}

?>