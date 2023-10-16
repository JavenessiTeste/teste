<?php

/*

require('../lib/base.php');
require('../lib/registroBoletoSantander.php');


$empresa = '400';
$codigoAssociado = '014000306649000';

$retorno = CancelaContrato($empresa,$codigoAssociado ,true);

print_R($retorno);

if($retorno['FATURA']!=''){

	if($empresa == '400'){
		$conta = '13000523';
		$retornoFatura = RegistraFatura($retorno['FATURA'],$codigoAssociado,$conta);
	}else{
		$conta = '13001400';
		$retornoFatura = RegistraFatura($retorno['FATURA'],$empresa,$conta );
	}

	print_R($retornoFatura);
	
	if($retornoFatura['STATUS']=='OK'){
		$sqlFatura = 'Update  ps1020 set 
						CODIGO_IDENTIFICACAO_FAT = '.aspas($retornoFatura['DADOS']['titulo']['nossoNumero']).'
						CODIGO_BANCO = '.aspas('341').'
						NUMERO_CONTA_COBRANCA = '.aspas($conta).' 
						NUMERO_LINHA_DIGITAVEL= '.aspas($retornoFatura['DADOS']['titulo']['linDig']).'
					  where numero_registro='.aspas($retorno['FATURA']);
		$resFatura  = jn_query($sqlFatura);
		if($empresa == '400'){
			$cancelaFaturas = 'UPDATE  ps1020 set DATA_CANCELAMENTO = GETDATE() 
							  where CODIGO_ASSOCIADO ='.aspas($codigoAssociado).' and 
									DATA_PAGAMENTO IS NULL AND 
									DATA_CANCELAMENTO IS NULL and
									NUMERO_REGISTRO <> '.aspas($retorno['FATURA']);
			$resCancelaFaturas  = jn_query($cancelaFaturas);
			
			$CancelaBeneficiarios= 'UPDATE PS1000 set DATA_EXCLUSAO = GETDATE() WHERE CODIGO_TITULAR = '.aspas($codigoAssociado) . ' and DATA_EXCLUSAO IS NULL'; 
			$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);
		}else{
			$cancelaFaturas = 'UPDATE  ps1020 set DATA_CANCELAMENTO = GETDATE() 
							  where CODIGO_EMPRESA ='.aspas($empresa).' and 
									DATA_PAGAMENTO IS NULL AND 
									DATA_CANCELAMENTO IS NULL and
									NUMERO_REGISTRO <> '.aspas($retorno['FATURA']);
			$resCancelaFaturas  = jn_query($cancelaFaturas);
			
			$CancelaBeneficiarios= 'UPDATE PS1000 set DATA_EXCLUSAO = GETDATE() WHERE CODIGO_EMPRESA = '.aspas($empresa) . ' and DATA_EXCLUSAO IS NULL'; 
			$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);
			
			$CancelaBeneficiarios= 'UPDATE PS1010 set DATA_EXCLUSAO = GETDATE() WHERE CODIGO_EMPRESA = '.aspas($empresa) . ' and DATA_EXCLUSAO IS NULL'; 
			$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);					
		}
		
	}Else{
		$sqlFatura = 'delete from ps1020 where numero_registro='.aspas($retorno['FATURA']);
		$resFatura  = jn_query($sqlFatura);
	}
}
*/
function RegistraFatura($registro,$codigo,$banco){

	$sqlFatura = "select * from PS1020 where data_pagamento is null and  numero_registro = ".aspas($registro);
		
	$resFatura  = jn_query($sqlFatura);
	if($rowFatura = jn_fetch_object($resFatura)) {
		$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
		$dataVencimento = sqlToData($rowFatura->DATA_VENCIMENTO);//->format('dmY');
		//$valorFatura 	= str_replace('.','',str_replace(',','',number_format($rowFatura->VALOR_FATURA,2)));
		//$valorFatura    = str_pad($valorFatura, 15, '0', STR_PAD_LEFT );
		$valorFatura =$rowFatura->VALOR_FATURA;
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
			$nome = $rowEndereco->NOME;
			$documento = $rowEndereco->DOCUMENTO;
			$endereco = substr($rowEndereco->ENDERECO, 0, 40);
			$bairro = $rowEndereco->BAIRRO;
			$cidade = substr($rowEndereco->CIDADE, 0, 20);
			$cep = $rowEndereco->CEP;
			$estado = $rowEndereco->ESTADO;
		}
		$resBanco  = jn_query($sqlbanco);
		if($rowBanco = jn_fetch_object($resBanco)){
			//print_r($rowBanco);
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
		}else{
			//print_r('Sem dados');
		}
		
		$retorno = RegistraBoleto($codigoAssociado,$codigoEmpresa,$nome,$documento,$endereco,$bairro,$cidade,$cep,$estado,$dataVencimento,$valorFatura,$caminhoCertificado,$senhaCertificado,$convenio,$codigoEstacao,$seuNumero,'');

		return ($retorno);
		
		//Array ( [STATUS] => OK [DADOS] => Array ( [codcede] => 000029525 [convenio] => Array ( [codBanco] => 0033 [codConv] => 000029525 ) [descricaoErro] => 00000 - Título registrado em cobrança [dtNsu] => 27092021 [estacao] => PE4P [nsu] => TST0000003 [pagador] => Array ( [bairro] => Centro [cep] => 79904682 [cidade] => PONTA PORa [ender] => RUaa SEte de Setembro,366 [nome] => Diego Ribeiro da Roza [numDoc] => 000000206410107 [tpDoc] => 01 [uf] => MS ) [situacao] => 00 [titulo] => Array ( [aceito] => N [cdBarra] => 03395875900000010159002952500000000040240101 [codPartilha1] => [codPartilha2] => [codPartilha3] => [codPartilha4] => [dtEmissao] => 27092021 [dtEntr] => 27092021 [dtLimiDesc] => [dtLimiDesc2] => 0001-01-01 [dtLimiDesc3] => 0001-01-01 [dtVencto] => 30092021 [especie] => 02 [linDig] => 03399002925250000000600402401012587590000001015 [mensagem] => Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento. [nomeAvalista] => [nossoNumero] => 0000000004024 [numDocAvalista] => 000000000000000 [pcIof] => 00000000 [pcJuro] => 00000 [pcMulta] => 00000 [qtDiasBaixa] => 01 [qtDiasMulta] => 00 [qtDiasProtesto] => 00 [qtdParciais] => 00 [seuNumero] => 900000000000003 [tipoPagto] => 0 [tipoValor] => 0 [tpDesc] => 0 [tpDocAvalista] => 00 [tpProtesto] => 0 [valorMaximo] => 00000000000000000 [valorMinimo] => 00000000000000000 [vlAbatimento] => 000000000000000 [vlDesc] => 000000000000000 [vlDesc2] => 000000000000000 [vlDesc3] => 000000000000000 [vlNominal] => 000000000001015 [vlPartilha1] => 000000000000000 [vlPartilha2] => 000000000000000 [vlPartilha3] => 000000000000000 [vlPartilha4] => 000000000000000 ) [tpAmbiente] => T ) )
		//Array ( [STATUS] => ERRO [DADOS] => Array ( [codcede] => [convenio] => Array ( [codBanco] => 0033 [codConv] => 000029525 ) [descricaoErro] => 00100-DATA EMISSAO MAIOR QUE A DATA VENCIMENTO [dtNsu] => 27092021 [estacao] => PE4P [nsu] => TST0000003 [pagador] => Array ( [bairro] => Centro [cep] => 79904682 [cidade] => PONTA PORa [ender] => RUaa SEte de Setembro,366 [nome] => Diego Ribeiro da Roza [numDoc] => 000000206410107 [tpDoc] => 01 [uf] => MS ) [situacao] => 20 [titulo] => Array ( [aceito] => N [cdBarra] => [codPartilha1] => [codPartilha2] => [codPartilha3] => [codPartilha4] => [dtEmissao] => 27092021 [dtEntr] => [dtLimiDesc] => [dtLimiDesc2] => 0001-01-01 [dtLimiDesc3] => 0001-01-01 [dtVencto] => 10092021 [especie] => 02 [linDig] => [mensagem] => Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento. [nomeAvalista] => [nossoNumero] => 0000000000000 [numDocAvalista] => 000000000000000 [pcIof] => 00000000 [pcJuro] => 00000 [pcMulta] => 00000 [qtDiasBaixa] => 01 [qtDiasMulta] => 00 [qtDiasProtesto] => 00 [qtdParciais] => 00 [seuNumero] => 900000000000003 [tipoPagto] => 0 [tipoValor] => 0 [tpDesc] => 0 [tpDocAvalista] => 00 [tpProtesto] => 0 [valorMaximo] => 00000000000000000 [valorMinimo] => 00000000000000000 [vlAbatimento] => 000000000000000 [vlDesc] => 000000000000000 [vlDesc2] => 000000000000000 [vlDesc3] => 000000000000000 [vlNominal] => 000000000001015 [vlPartilha1] => 000000000000000 [vlPartilha2] => 000000000000000 [vlPartilha3] => 000000000000000 [vlPartilha4] => 000000000000000 ) [tpAmbiente] => T ) )

	}
}


function CancelaContrato($codigoEmpresa,$codigoAssociado,$efetivar=false){
	
	$retorno = array();
	$retorno['STATUS'] = '';
	$retorno['MSG']    = '';
	$retorno['FATURA'] = '';
	
	if($codigoEmpresa=='400'){
		$pf = true;
	}else{
		$pf = false;
	}
	if($pf){
		//$sqlFatura = "select coalesce(sum(VALOR_FATURA+ROUND(VALOR_FATURA*0.02,2)+ROUND(VALOR_FATURA*0.00033*DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()),2)),0) VALOR_FATURAS_EM_ABERTO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento <=".dataToSql(date("d/m/Y"));
		//$sqlFaturaNaoVencida = "select coalesce(sum(case when (30 -DATEDIFF(DAY,GETDATE(),DATA_VENCIMENTO))>0 then (VALOR_FATURA/30)*(30 -DATEDIFF(DAY,GETDATE(),DATA_VENCIMENTO)) else 0 end),0) VALOR_FATURAS_EM_ABERTO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento >".dataToSql(date("d/m/Y"));
		//$sqlSemFatura = "select top 1 (VALOR_FATURA/30)* DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()) VALOR_SEM_FATURA from PS1020 where Data_cancelamento is null and CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento <=".dataToSql(date("d/m/Y")).' ORDER BY data_vencimento desc';
		$sqlFatura = "select coalesce(SUM(Case when DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()) >=30 then (VALOR_FATURA+ROUND(VALOR_FATURA*0.02,2)+ROUND(VALOR_FATURA*0.00033*DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()),2)) else (((VALOR_FATURA/30)*(DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE())))+ROUND(((VALOR_FATURA/30)*(DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE())))*0.02,2)+ROUND(((VALOR_FATURA/30)*(DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE())))*0.00033*DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()),2)) end),0) VALOR_FATURAS_EM_ABERTO from PS1020  where Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento <=".dataToSql(date("d/m/Y"));
		
	}else{
		//$sqlFatura = "select coalesce(sum(VALOR_FATURA+ROUND(VALOR_FATURA*0.02,2)+ROUND(VALOR_FATURA*0.00033*DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()),2)),0) VALOR_FATURAS_EM_ABERTO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento <=".dataToSql(date("d/m/Y"));
		//$sqlFaturaNaoVencida = "select coalesce(sum(case when (30 -DATEDIFF(DAY,GETDATE(),DATA_VENCIMENTO))>0 then (VALOR_FATURA/30)*(30 -DATEDIFF(DAY,GETDATE(),DATA_VENCIMENTO)) else 0 end),0) VALOR_FATURAS_EM_ABERTO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento >".dataToSql(date("d/m/Y"));
		//$sqlSemFatura = "select top 1 (VALOR_FATURA/30)* DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()) VALOR_SEM_FATURA from PS1020 where Data_cancelamento is null and CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento <=".dataToSql(date("d/m/Y")).' ORDER BY data_vencimento desc';
		$sqlFatura = "select coalesce(SUM(Case when DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()) >=30 then (VALOR_FATURA+ROUND(VALOR_FATURA*0.02,2)+ROUND(VALOR_FATURA*0.00033*DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()),2)) else (((VALOR_FATURA/30)*(DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE())))+ROUND(((VALOR_FATURA/30)*(DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE())))*0.02,2)+ROUND(((VALOR_FATURA/30)*(DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE())))*0.00033*DATEDIFF(DAY,DATA_VENCIMENTO,GETDATE()),2)) end),0) VALOR_FATURAS_EM_ABERTO from PS1020  where Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento <=".dataToSql(date("d/m/Y"));
		
	}
	
	//$resFaturaNaoVencida  = jn_query($sqlFaturaNaoVencida);
	//$rowFaturaNaoVencida = jn_fetch_object($resFaturaNaoVencida);
		
	//$resSemFatura  = jn_query($sqlSemFatura);
	//$rowSemFatura = jn_fetch_object($resSemFatura);	
		
	$resFatura  = jn_query($sqlFatura);
	if($rowFatura = jn_fetch_object($resFatura)) {
		
			if($pf){
				$sqlParcela = "select max(NUMERO_PARCELA) NUMERO_PARCELA from PS1020 where Data_cancelamento is null  and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento <".dataToSql(date("d/m/Y"));
			}else{
				$sqlParcela = "select max(NUMERO_PARCELA) NUMERO_PARCELA from PS1020 where  Data_cancelamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento <".dataToSql(date("d/m/Y"));;
			}
			$resParcela  = jn_query($sqlParcela);
			if($rowParcela = jn_fetch_object($resParcela)) {
						$retorno['VALOR'] = 0;
						$retorno['STATUS'] = 'OK';
						if($pf){
							$sqlValor = "select first 1 VALOR_FATURA from PS1020 where data_pagamento is not null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado).' ORDER by DATA_VENCIMENTO DESC';
						}else{
							$sqlValor = "select first 1 VALOR_FATURA from PS1020 where data_pagamento is not null and  CODIGO_EMPRESA   = ".aspas($codigoEmpresa).' ORDER by DATA_VENCIMENTO DESC';
						}
						//if($rowFaturaNaoVencida->VALOR_FATURAS_EM_ABERTO == 0)
						//		$rowFaturaNaoVencida->VALOR_FATURAS_EM_ABERTO = $rowSemFatura->VALOR_SEM_FATURA;
							
						$resValor  = jn_query($sqlValor);
						$rowValor = jn_fetch_object($resValor);
						$parcelasPagas = $rowParcela->NUMERO_PARCELA;
						$retorno['STATUS'] = 'OK';
						$retorno['MSG']    = 'Valor em Aberto ' .number_format($rowFatura->VALOR_FATURAS_EM_ABERTO,2,",",".").'<br>';
						if($rowParcela->NUMERO_PARCELA < 12){
							$retorno['VALOR']  = (($rowValor->VALOR_FATURA*(12 -$parcelasPagas))*0.5);
							$retorno['MSG']    .= 'Valor Multa ' .number_format((($rowValor->VALOR_FATURA*(12 -$parcelasPagas))*0.5),2,",",".").'<br>';
						}
						$retorno['VALOR']  = $retorno['VALOR'] + $rowFatura->VALOR_FATURAS_EM_ABERTO;
						//$retorno['VALOR']  = $retorno['VALOR'] + $rowFatura->VALOR_FATURAS_EM_ABERTO+$rowFaturaNaoVencida->VALOR_FATURAS_EM_ABERTO;
						//print_r($rowFatura->VALOR_FATURAS_EM_ABERTO);
						//exit;
						$retorno['MSG']    .= 'Para efetuar o cancelamento você terá que acertar o valor de ' .number_format($retorno['VALOR'],2,",",".");
						
						if($efetivar and  $retorno['VALOR']>0){
							
							$data_vencimento_boleto = date( 'd/m/Y', mktime( 0, 0, 0, date( 'm' ), date( 'd' )+5, date( 'Y' ) ) );
							$data_gerar_boleto = date('d/m/Y');
							$numeroRegistro = jn_gerasequencial('PS1020');
							$obs = 'Multa Cancelamento - WEB/APP';
							$query  = 'INSERT INTO PS1020 (NUMERO_REGISTRO,CODIGO_EMPRESA, CODIGO_ASSOCIADO, DATA_VENCIMENTO, VALOR_FATURA, DATA_EMISSAO, TIPO_REGISTRO, INFORMACOES_GERACAO) ';
							$query .= " VALUES (".aspas($numeroRegistro).", ". aspas($codigoEmpresa) . "," . aspasNull($codigoAssociado) . ", " . dataToSql($data_vencimento_boleto) . ", " . ($retorno['VALOR']) . ", " . dataToSql($data_gerar_boleto) . ", 'Q', " . aspas($obs) . " );";
							if(jn_query($query)){
								$retorno['FATURA'] = $numeroRegistro;
							}
						}else if($efetivar){
							$retorno['FATURA'] = 0;
						}
					
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				
			}
		
	}
	
	return $retorno;
	
}

function ConsultaAlteraVencimento($codigoEmpresa,$codigoAssociado){
	
	$retorno = array();
	$retorno['STATUS'] = '';
	$retorno['MSG']    = '';
	$retorno['FATURA'] = '';
	
	if($codigoEmpresa=='400'){
		$pf = true;
	}else{
		$pf = false;
	}

	if($pf){
		$sqlFatura = "select Count(*) ALTEROUVENCIMENTO from PS1020 where  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and DATA_VENCIMENTO >  DATEADD(month, -6, GETDATE()) and Informacoes_Geracao =".aspas('ALTERACAOVENCIMENTO');
	}else{
		$sqlFatura = "select Count(*) ALTEROUVENCIMENTO from PS1020 where  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and DATA_VENCIMENTO >  DATEADD(month, -6, GETDATE()) and Informacoes_Geracao =".aspas('ALTERACAOVENCIMENTO');
	}
	$resFatura  = jn_query($sqlFatura);
	if($rowFatura = jn_fetch_object($resFatura)) {
		if($rowFatura->ALTEROUVENCIMENTO > 0){
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'Já foi feito uma alteração no dia vencimento nos ultimos 6 meses.';
			return $retorno;
			exit;
		}
	}
	
	if($pf){
		$sqlFatura = "select Count(*) FATURAS_EM_ABERTO from PS1020 where  Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento <".dataToSql(date("d/m/Y"));
	}else{
		$sqlFatura = "select Count(*) FATURAS_EM_ABERTO from PS1020 where  Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento <".dataToSql(date("d/m/Y"));
	}
		
		
	$resFatura  = jn_query($sqlFatura);
	if($rowFatura = jn_fetch_object($resFatura)) {
		if($rowFatura->FATURAS_EM_ABERTO > 0){
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'A alteração do vencimento não pode ser feito com faturas em Aberto.';
		}else{
			if($pf){
				$sqlParcela = "select DATA_VENCIMENTO ,DATEPART(DAY,DATA_VENCIMENTO) DIA from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado).'';
			}else{
				$sqlParcela = "select DATA_VENCIMENTO,DATEPART(DAY,DATA_VENCIMENTO) DIA from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa);
			}
			$resParcela  = jn_query($sqlParcela);
			if($rowParcela = jn_fetch_object($resParcela)) {
					if($rowParcela->DIA != ''){
						$retorno['DIA_VENCIMENTO'] = $rowParcela->DIA;
						$retorno['STATUS'] = 'OK';
						$retorno['DIAS']  = array();
						for ($i = 1; $i <= 31; $i++) {
							if($rowParcela->DIA != $i)
								$retorno['DIAS'][] = $i;
						}
						$retorno['MSG']    = 'Selecione um dia para o novo vencimento no combo abaixo:';
					}else{
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = 'Não existe fatura em aberto.';
					}
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG']    = 'Não existe fatura em aberto.';
			}
		}
	}
	
	return $retorno;
	
}

function ConfirmaAlteraVencimento($codigoEmpresa,$codigoAssociado,$novoDiaVencimento){

	$retorno = array();
	$retorno['STATUS'] = '';
	$retorno['MSG']    = '';
	$retorno['FATURA'] = '';
	
	if($codigoEmpresa=='400'){
		$pf = true;
	}else{
		$pf = false;
	}
	if($pf){
		$sqlFatura = "select Count(*) FATURAS_EM_ABERTO from PS1020 where  Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento <".dataToSql(date("d/m/Y"));
	}else{
		$sqlFatura = "select Count(*) FATURAS_EM_ABERTO from PS1020 where  Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento <".dataToSql(date("d/m/Y"));
	}
	
		
	$resFatura  = jn_query($sqlFatura);
	if($rowFatura = jn_fetch_object($resFatura)) {
		if($rowFatura->FATURAS_EM_ABERTO > 0){
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'A alteração do vencimento não pode ser feito com faturas vencidas.';
		}else{
			if($pf){
				$sqlParcela = "select PS1020.*,DATEPART(DAY,DATA_VENCIMENTO) DIA,DATEPART(month,DATA_VENCIMENTO) MES,DATEPART(year,DATA_VENCIMENTO) ANO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado).' order by DATA_VENCIMENTO Asc ';
			}else{
				$sqlParcela = "select PS1020.*,DATEPART(DAY,DATA_VENCIMENTO) DIA,DATEPART(month,DATA_VENCIMENTO) MES,DATEPART(year,DATA_VENCIMENTO) ANO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa).' order by DATA_VENCIMENTO Asc ';
			}
			$resParcela  = jn_query($sqlParcela);
			$primeira = true;
			$retorno['NOVAS_FATURAS'] = array();
			$retorno['VELHAS_FATURAS'] = array();
			while($rowParcela = jn_fetch_object($resParcela)) {
					if($primeira){
						$diaAlterado = $novoDiaVencimento;
						
						$mesVencimento = $rowParcela->MES;
						$AnoVencimento = $rowParcela->ANO;
						
						while(!checkdate($mesVencimento, $diaAlterado, $AnoVencimento)){
							$diaAlterado = $diaAlterado -1;
						}
						
						$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
						
						$novoVencimento     =  mktime(0, 0, 0,$mesVencimento, $diaAlterado,$AnoVencimento);
						$vencimentoOriginal =  mktime(0, 0, 0,$rowParcela->MES, $rowParcela->DIA,$rowParcela->ANO);
						
						if($novoVencimento<=$hoje){
							$diaAlterado = $novoDiaVencimento;
							$mesVencimento = $mesVencimento +1;
							if($mesVencimento>12){
								$mesVencimento = 1;
								$AnoVencimento = $AnoVencimento +1;
							}
							while(!checkdate($mesVencimento, $diaAlterado, $AnoVencimento)){
								$diaAlterado = $diaAlterado -1;
							}
							
							$novoVencimento =  mktime(0, 0, 0,$mesVencimento, $diaAlterado,$AnoVencimento);
						}
						
						$diferencaDias  = (($novoVencimento-$vencimentoOriginal)/60/60/24);
						
						$quantidadeDias = 30; //date('d', mktime(0, 0, 0, $rowParcela->MES, 0, $rowParcela->ANO ));
						
						$valorDia =  $rowParcela->VALOR_FATURA/$quantidadeDias;
						
						$valorFatura = round($rowParcela->VALOR_FATURA + ($valorDia*$diferencaDias),2); 
						//$retorno['MSG']    .= 'Dias ='.$diferencaDias.' | Quantidade Dias mes'.$quantidadeDias.' | Valor Dia: '.$valorDia.' | Novo Valor :'.$valorFatura.' | VALOR Antigo: '.$rowParcela->VALOR_FATURA;
						
						$numeroRegistro = jn_gerasequencial('PS1020');
						$obs = 'ALTERACAOVENCIMENTO';
						InsereFatura($rowParcela->NUMERO_REGISTRO,$numeroRegistro,$valorFatura,date("d/m/Y", $novoVencimento),$obs);
						
					}else{
							$diaAlterado = $novoDiaVencimento;
							$mesVencimento = $mesVencimento +1;
							if($mesVencimento>12){
								$mesVencimento = 1;
								$AnoVencimento = $AnoVencimento +1;
							}
							while(!checkdate($mesVencimento, $diaAlterado, $AnoVencimento)){
								$diaAlterado = $diaAlterado -1;
							}
							
							$novoVencimento =  mktime(0, 0, 0,$mesVencimento, $diaAlterado,$AnoVencimento);
							$numeroRegistro = jn_gerasequencial('PS1020');
							$obs = 'ALTERACAOVENCIMENTO';
							InsereFatura($rowParcela->NUMERO_REGISTRO,$numeroRegistro,$rowParcela->VALOR_FATURA,date("d/m/Y", $novoVencimento),$obs);
					}
					$retorno['NOVAS_FATURAS'][] = $numeroRegistro;
					$retorno['VELHAS_FATURAS'][] = $rowParcela->NUMERO_REGISTRO;
					//$retorno['MSG']    .=' ' . date("d/m/Y", $novoVencimento);
					$primeira = false;
			}
		}
	}
	
	return $retorno;


}
function ValorAlteraVencimento($codigoEmpresa,$codigoAssociado,$novoDiaVencimento){

	$retorno = array();
	$retorno['STATUS'] = '';
	$retorno['MSG']    = '';
	$retorno['FATURA'] = '';
	
	if($codigoEmpresa=='400'){
		$pf = true;
	}else{
		$pf = false;
	}
	if($pf){
		$sqlFatura = "select Count(*) FATURAS_EM_ABERTO from PS1020 where  Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado)." and data_vencimento <".dataToSql(date("d/m/Y"));
	}else{
		$sqlFatura = "select Count(*) FATURAS_EM_ABERTO from PS1020 where  Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa)." and data_vencimento <".dataToSql(date("d/m/Y"));
	}
	
		
	$resFatura  = jn_query($sqlFatura);
	if($rowFatura = jn_fetch_object($resFatura)) {
		if($rowFatura->FATURAS_EM_ABERTO > 0){
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'A alteração do vencimento não pode ser feito com faturas vencidas.';
		}else{
			if($pf){
				$sqlParcela = "select PS1020.*,DATEPART(DAY,DATA_VENCIMENTO) DIA,DATEPART(month,DATA_VENCIMENTO) MES,DATEPART(year,DATA_VENCIMENTO) ANO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_ASSOCIADO = ".aspas($codigoAssociado).' order by DATA_VENCIMENTO Asc ';
			}else{
				$sqlParcela = "select PS1020.*,DATEPART(DAY,DATA_VENCIMENTO) DIA,DATEPART(month,DATA_VENCIMENTO) MES,DATEPART(year,DATA_VENCIMENTO) ANO from PS1020 where Data_cancelamento is null and data_pagamento is null and  CODIGO_EMPRESA = ".aspas($codigoEmpresa).' order by DATA_VENCIMENTO Asc ';
			}
			$resParcela  = jn_query($sqlParcela);
			$primeira = true;
			
			if($rowParcela = jn_fetch_object($resParcela)) {
					
						$diaAlterado = $novoDiaVencimento;
						
						$mesVencimento = $rowParcela->MES;
						$AnoVencimento = $rowParcela->ANO;
						
						while(!checkdate($mesVencimento, $diaAlterado, $AnoVencimento)){
							$diaAlterado = $diaAlterado -1;
						}
						
						$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
						
						$novoVencimento     =  mktime(0, 0, 0,$mesVencimento, $diaAlterado,$AnoVencimento);
						$vencimentoOriginal =  mktime(0, 0, 0,$rowParcela->MES, $rowParcela->DIA,$rowParcela->ANO);
						
						if($novoVencimento<=$hoje){
							$diaAlterado = $novoDiaVencimento;
							$mesVencimento = $mesVencimento +1;
							if($mesVencimento>12){
								$mesVencimento = 1;
								$AnoVencimento = $AnoVencimento +1;
							}
							while(!checkdate($mesVencimento, $diaAlterado, $AnoVencimento)){
								$diaAlterado = $diaAlterado -1;
							}
							
							$novoVencimento =  mktime(0, 0, 0,$mesVencimento, $diaAlterado,$AnoVencimento);
						}
						
						$diferencaDias  = (($novoVencimento-$vencimentoOriginal)/60/60/24);
						
						$quantidadeDias = 30;//date('d', mktime(0, 0, 0, $rowParcela->MES, 0, $rowParcela->ANO ));
						
						
						$valorDia =  $rowParcela->VALOR_FATURA/$quantidadeDias;
						
						$valorFatura = round($rowParcela->VALOR_FATURA + ($valorDia*$diferencaDias),2); 
						//$retorno['MSG']    .= 'Dias ='.$diferencaDias.' | Quantidade Dias mes'.$quantidadeDias.' | Valor Dia: '.$valorDia.' | Novo Valor :'.$valorFatura.' | VALOR Antigo: '.$rowParcela->VALOR_FATURA;
						
						$retorno['MSG'] = 	'Próximo Vencimento :'. date("d/m/Y", $novoVencimento).'<br>Valor :'.$valorFatura;
						//$retorno['MSG'].= '<br>Dias ='.$diferencaDias.' | Quantidade Dias mes'.$quantidadeDias.' | Valor Dia: '.$valorDia.' | Novo Valor :'.$valorFatura.' | VALOR Antigo: '.$rowParcela->VALOR_FATURA;
						//$retorno['MSG'].= '<br>'.$rowParcela->MES;
						

			}
		}
	}
	
	return $retorno;


}


function InsereFatura($numeroRegistroAntigo,$numeroRegistroNovo,$Valor,$DataVencimento,$obs){

		$insertFatura = 'INSERT INTO PS1020(
							   NUMERO_REGISTRO
							  ,CODIGO_EMPRESA
							  ,CODIGO_ASSOCIADO
							  ,DATA_VENCIMENTO
							  ,VALOR_CONVENIO
							  ,VALOR_ADICIONAL
							  ,VALOR_PRORRATA
							  ,VALOR_OUTROS
							  ,VALOR_CORRECAO
							  ,VALOR_FATURA
							  ,DATA_EMISSAO
							  ,VALOR_PAGO
							  ,VALOR_MULTA
							  ,VALOR_DESCONTO
							  ,DATA_PAGAMENTO
							  ,NUMERO_NOTA_FISCAL
							  ,CODIGO_IDENTIFICACAO_FAT
							  ,NUMERO_PARCELA
							  ,TIPO_BAIXA
							  ,CODIGO_BANCO
							  ,DESCRICAO_OBSERVACAO
							  ,FLAG_MARCADO
							  ,CODIGO_SEQUENCIAL
							  ,DATA_CANCELAMENTO
							  ,DATA_RESTITUICAO
							  ,CODIGO_OPERADOR_BAIXA
							  ,NUMERO_RECIBO
							  ,DATA_VALIDACAO
							  ,PERCENTUAL_CORRECAO
							  ,DATA_EMISSAO_BOLETO
							  ,DATA_EMISSAO_ARQUIVO
							  ,DATA_BAIXA
							  ,NOSSO_NUMERO
							  ,TIPO_REGISTRO
							  ,DATA_NEGOCIACAO
							  ,MES_ANO_REFERENCIA
							  ,VALOR_FATURA_BRUTO
							  ,VALOR_DESC_IR
							  ,VALOR_CSLL_PIS_COFINS
							  ,VALOR_DESC_ISS
							  ,VALOR_CSLL
							  ,VALOR_PIS
							  ,VALOR_COFINS
							  ,CODIGO_ULTIMA_EMISSAO
							  ,NUMERO_CARTA_INADIMPLENCIA
							  ,TIPO_SITUACAO_FATURA
							  ,NUMERO_NOTA_EMISSAO
							  ,NUMERO_NOTA_CANCELAMENTO
							  ,IDENTIFICACAO_GERACAO
							  ,CODIGO_BANCO_BAIXA
							  ,FLAG_REAJUSTE_APLICADO
							  ,OBSERVACAO_MANUTENCAO
							  ,NUMERO_CONTA_BAIXA
							  ,Numero_Conta_Cobranca
							  ,Numero_Registro_Ps7400
							  ,Valor_Desc_Arq_Remessa
							  ,Descricao_Desc_Arq_Remessa
							  ,Padrao_Arquivo_Retorno
							  ,Informacoes_Geracao
							  ,OBSERVACOES_COBRANCA
							  ,Codigo_Carteira
							  ,CODIGO_COBRANCA_ATRASO
							  ,CODIGO_COBRANCA_NEGOCIADA
							  ,VALOR_FATURA_AGRUPADA_ORIG
							  ,DATA_PRORROGACAO
							  ,INFORMACOES_LOG_I
							  ,INFORMACOES_LOG_A
							  ,NUMERO_LINHA_DIGITAVEL
							  ,numero_pedido
							  ,LINK_NFSE
							  ,DATA_BAIXA_REMESSA
							  ,DATA_PRORROGACAO_REMESSA
							  ,TIPO_PAGAMENTO_TEF
							  ,NUMERO_AUTORIZACAO_TEF
							  ,CODIGO_RETORNO_REMESSA
							  ,Valor_Mora_Remessa
							  ,Valor_Multa_Remessa
							  ,Flag_Boleto_Enviado
							  ,TIPO_DEFERIMENTO_NET)
						SELECT '.aspas($numeroRegistroNovo).'
							  ,CODIGO_EMPRESA
							  ,CODIGO_ASSOCIADO
							  ,'.dataToSql($DataVencimento).'
							  ,VALOR_CONVENIO
							  ,VALOR_ADICIONAL
							  ,VALOR_PRORRATA
							  ,VALOR_OUTROS
							  ,VALOR_CORRECAO
							  ,'.$Valor.'
							  ,GETDATE()
							  ,VALOR_PAGO
							  ,VALOR_MULTA
							  ,VALOR_DESCONTO
							  ,DATA_PAGAMENTO
							  ,NUMERO_NOTA_FISCAL
							  ,CODIGO_IDENTIFICACAO_FAT
							  ,NUMERO_PARCELA
							  ,TIPO_BAIXA
							  ,CODIGO_BANCO
							  ,DESCRICAO_OBSERVACAO
							  ,FLAG_MARCADO
							  ,CODIGO_SEQUENCIAL
							  ,DATA_CANCELAMENTO
							  ,DATA_RESTITUICAO
							  ,CODIGO_OPERADOR_BAIXA
							  ,NUMERO_RECIBO
							  ,DATA_VALIDACAO
							  ,PERCENTUAL_CORRECAO
							  ,DATA_EMISSAO_BOLETO
							  ,DATA_EMISSAO_ARQUIVO
							  ,DATA_BAIXA
							  ,NOSSO_NUMERO
							  ,TIPO_REGISTRO
							  ,DATA_NEGOCIACAO
							  ,MES_ANO_REFERENCIA
							  ,VALOR_FATURA_BRUTO
							  ,VALOR_DESC_IR
							  ,VALOR_CSLL_PIS_COFINS
							  ,VALOR_DESC_ISS
							  ,VALOR_CSLL
							  ,VALOR_PIS
							  ,VALOR_COFINS
							  ,CODIGO_ULTIMA_EMISSAO
							  ,NUMERO_CARTA_INADIMPLENCIA
							  ,TIPO_SITUACAO_FATURA
							  ,NUMERO_NOTA_EMISSAO
							  ,NUMERO_NOTA_CANCELAMENTO
							  ,IDENTIFICACAO_GERACAO
							  ,CODIGO_BANCO_BAIXA
							  ,FLAG_REAJUSTE_APLICADO
							  ,OBSERVACAO_MANUTENCAO
							  ,NUMERO_CONTA_BAIXA
							  ,Numero_Conta_Cobranca
							  ,Numero_Registro_Ps7400
							  ,Valor_Desc_Arq_Remessa
							  ,Descricao_Desc_Arq_Remessa
							  ,Padrao_Arquivo_Retorno
							  ,'.aspas($obs).'
							  ,OBSERVACOES_COBRANCA
							  ,Codigo_Carteira
							  ,CODIGO_COBRANCA_ATRASO
							  ,CODIGO_COBRANCA_NEGOCIADA
							  ,VALOR_FATURA_AGRUPADA_ORIG
							  ,DATA_PRORROGACAO
							  ,INFORMACOES_LOG_I
							  ,INFORMACOES_LOG_A
							  ,NUMERO_LINHA_DIGITAVEL
							  ,numero_pedido
							  ,LINK_NFSE
							  ,DATA_BAIXA_REMESSA
							  ,DATA_PRORROGACAO_REMESSA
							  ,TIPO_PAGAMENTO_TEF
							  ,NUMERO_AUTORIZACAO_TEF
							  ,CODIGO_RETORNO_REMESSA
							  ,Valor_Mora_Remessa
							  ,Valor_Multa_Remessa
							  ,Flag_Boleto_Enviado
							  ,TIPO_DEFERIMENTO_NET
						  FROM PS1020
						  Where NUMERO_REGISTRO ='. aspas($numeroRegistroAntigo);

	jn_query($insertFatura);

}

?>