<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../EstruturaEspecifica/azureStorage.php');

if($_POST['tipo'] =='efetivarExclusao'){
	
	$codigoAssociado 	= $_POST['codigoAssociado'];
	$dataExclusao 		= $_POST['dataExclusao'];
	$horaExclusao 		= $_POST['horaExclusao'];
	$motivoExclusao 	= $_POST['motivoExclusao'];

	$arquivoExc = isset($_FILES["arquivoExc"]) ? $_FILES["arquivoExc"] : FALSE;
	$arquivoExc_name = $_FILES['arquivoExc']['name'];
	$arquivoExc_type = $_FILES['arquivoExc']['type'];
	$arquivoExc_size = $_FILES['arquivoExc']['size'];
	$arquivoExc_temp_name = $_FILES['arquivoExc']['tmp_name'];
	$nomefinalArq = strtolower( remove_caracteres( $arquivoExc['name'] ) );
	$nomefinalArq = str_replace(' ','_',$nomefinalArq);
	
	if (isset($arquivoExc))
	{		
				
		$numeroRegistro   = jn_gerasequencial('CFGARQUIVOS_BENEF_NET');			
		$nomefinalArq = 'Exc_' . $codigoAssociado . '_' . $numeroRegistro . '_' . $nomefinalArq;		   
		uploadFileBlogStorage('UploadArquivos/ExcAssociados',$nomefinalArq,fopen($arquivoExc_temp_name , "r"),mime_content_type($arquivoExc_temp_name));
						
		$caminhoArquivo = 'https://app.plenasaude.com.br/UploadArquivos/ExcAssociados/';			

		$query  = ' INSERT INTO CFGARQUIVOS_BENEF_NET (CODIGO_ASSOCIADO, CAMINHO_ARQUIVO, NOME_ARQUIVO) ';
		$query .= ' VALUES (' . aspas($codigoAssociado) . ','.  aspas($caminhoArquivo). ', ' . aspas($nomefinalArq) . ')';

		if(jn_query($query)){

			$queryAssoc = 'SELECT TIPO_ASSOCIADO, DATA_NASCIMENTO, CODIGO_PLANO, CODIGO_TABELA_PRECO, CODIGO_TITULAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
			$resAssoc = jn_query($queryAssoc);
			$rowAssoc = jn_fetch_object($resAssoc);
			$tipoAssoc = $rowAssoc->TIPO_ASSOCIADO;
			$idade = calcularIdade($rowAssoc->DATA_NASCIMENTO);
			$plano = $rowAssoc->CODIGO_PLANO;
			$tabelaPreco = $rowAssoc->CODIGO_TABELA_PRECO;
			$codigoTitular = $rowAssoc->CODIGO_TITULAR;
			$valorTotalMulta = 0;
			$multaGerada = false;

			$complementoExclusaoAssoc = '';

			if($_SESSION['codigoSmart'] == '3423'){//Plena

				$complementoExclusaoAssoc .= ' FLAG_ISENTO_PAGTO = ' . aspas('S') . ' , ';
			}

			if($motivoExclusao == '5' && $tipoAssoc == 'T'){
				$dataExclusao = date('d/m/Y');

				$queryExc  = ' UPDATE PS1000 SET  ';
				$queryExc .= ' 	DATA_EXCLUSAO = ' . dataToSql($dataExclusao) . ', ';
				$queryExc .= ' 	INFORMACOES_LOG_E = ' . aspas('[OP_NET D' . date('d/m/Y') . ' H' . date('H:i') . ']') . ' , ';
				$queryExc .= $complementoExclusaoAssoc;
				$queryExc .= ' 	CODIGO_MOTIVO_EXCLUSAO = ' . aspas('1') ;
				$queryExc .= ' 	WHERE TIPO_ASSOCIADO = "D" AND CODIGO_TITULAR = ' . aspas($codigoAssociado) ;
				jn_query($queryExc);
			}elseif($motivoExclusao == '5'){
				$dataExclusao = date('d/m/Y');
			}

			$queryExc  = ' UPDATE PS1000 SET  ';
			$queryExc .= ' 	DATA_EXCLUSAO = ' . dataToSql($dataExclusao) . ', ';
			$queryExc .= ' 	INFORMACOES_LOG_E = ' . aspas('[OP_NET D' . date('d/m/Y') . ' H' . date('H:i') . ']') . ' , ';
			$queryExc .= $complementoExclusaoAssoc;
			$queryExc .= ' 	CODIGO_MOTIVO_EXCLUSAO = ' . aspas($motivoExclusao) ;

			if($tipoAssoc == 'T' && $motivoExclusao != '5'){
				$queryExc .= ' 	WHERE CODIGO_TITULAR = ' . aspas($codigoAssociado) ;
			}else{
				$queryExc .= ' 	WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
			}

			if(jn_query($queryExc)){

				$numeroProtocoloGeral = GeraProtocoloGeralPs6450($codigoAssociado, '');			

				if($motivoExclusao == '1' && '1' != '1'){

					if($tipoAssoc == 'D'){
						geraMultaAssociados($codigoAssociado);
					}else{
						$queryAssocCont = 'SELECT CODIGO_ASSOCIADO FROM PS1000 WHERE (DATA_EXCLUSAO IS NULL OR DATA_EXCLUSAO = ' . dataToSql($dataExclusao) . ') AND CODIGO_TITULAR = ' . aspas($codigoAssociado);						
						$resAssocCont = jn_query($queryAssocCont);
						while($rowAssocCont = jn_fetch_object($resAssocCont)){
							geraMultaAssociados($rowAssocCont->CODIGO_ASSOCIADO);
						}
					}

					if($multaGerada){
						$retorno['STATUS'] = 'OK';
						$retorno['MSG'] = 'Associado Excluído, multa de ' . toMoeda($valorTotalMulta) . ' gerada para próxima fatura.';
					}else{
						$retorno['STATUS'] = 'OK';
						$retorno['MSG'] = 'Associado Excluído';
					}
				}else{
					$retorno['STATUS'] = 'OK';
					$retorno['MSG'] = 'Associado Excluído ' . ' - Protocolo: ' . $numeroProtocoloGeral;
				}
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG'] = 'Erro ao gravar Exclusão';
			}
			
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG'] = 'Erro ao gravar arquivo';
		}
		
	}


	
	echo json_encode($retorno);
	
}else if($dadosInput['tipo'] == 'dadosAssociado'){
	
	$query 	 = " SELECT PS1000.NOME_ASSOCIADO, PS1000.DATA_NASCIMENTO, T.NOME_ASSOCIADO NOME_TITULAR, PS1030.TIPO_PRE_POS_PAGTO FROM PS1000 ";
	$query 	.= " INNER JOIN PS1000 T ON (PS1000.CODIGO_TITULAR = T.CODIGO_ASSOCIADO) ";	
	$query 	.= " INNER JOIN PS1030 ON (PS1030.CODIGO_PLANO = PS1000.CODIGO_PLANO) ";	
	$query 	.= " WHERE PS1000.CODIGO_ASSOCIADO = " . aspas($dadosInput['associado']);	
	$res 	= jn_query($query);
	
	$retorno = array();
	while($row = jn_fetch_object($res)){

		$dataExclusao = date('d-m-Y');		
		if($row->TIPO_PRE_POS_PAGTO != 'POS'){			
			$dataExclusao = date('d/m/Y', strtotime("+30 days",strtotime($dataExclusao)));
		}else{
			$dataExclusao = date('d/m/Y');	
		}

		$retorno['NOME_ASSOCIADO'] = jn_utf8_encode($row->NOME_ASSOCIADO);		
		$retorno['NOME_TITULAR'] = jn_utf8_encode($row->NOME_TITULAR);	
		$retorno['DATA_NASCIMENTO'] = SqlToData($row->DATA_NASCIMENTO);	
		$retorno['DATA_EXCLUSAO'] = $dataExclusao;	
		$retorno['HORA_EXCLUSAO'] = date('H:i');
				
	}  
	echo json_encode($retorno);
}else if($dadosInput['tipo'] == 'motivosExc'){
	
	$queryMotivos 	 = " SELECT CODIGO_MOTIVO_EXCLUSAO, NOME_MOTIVO_EXCLUSAO FROM PS1047 ";	
	$queryMotivos 	.= " WHERE PS1047.CODIGO_MOTIVO_EXCLUSAO IN ('1','5','23') ";	
	$resMotivos 	= jn_query($queryMotivos);
	
	$retorno = array();
	$i = 0;	
	while($rowMotivos = jn_fetch_object($resMotivos)){
		$retorno[$i]['CODIGO_MOTIVO_EXCLUSAO'] 	= $rowMotivos->CODIGO_MOTIVO_EXCLUSAO;		
		$retorno[$i]['NOME_MOTIVO_EXCLUSAO'] 	= $rowMotivos->NOME_MOTIVO_EXCLUSAO;	
		$i++;			
	}  
	echo json_encode($retorno);
}else if($dadosInput['tipo'] == 'configuracoes'){		
	
	$retorno = array();	
	$retorno['VALOR_CONFIG'] = retornaValorConfiguracao($dadosInput['config']);		
	
	echo json_encode($retorno);
}


function calcularIdade($date){	
	if(!$date){
		return null;
	}
	
	$date = SqlToData($date);
	$date = dataToSql($date);
	$date = str_replace("'",'',$date);	

    // separando yyyy, mm, ddd
    list($ano, $mes, $dia) = explode('-', $date);
	$dia = substr($dia, 0, 2);
	
    // data atual
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
	
    // cálculo
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
}

function geraMultaAssociados($codigoAssoc){
	global $valorTotalMulta;
	global $multaGerada;

	$queryMesesMulta  = ' SELECT (12 - DATEDIFF(MONTH, DATA_ADMISSAO, GETDATE())) AS MESES FROM PS1000 ';					
	$queryMesesMulta .= ' WHERE CODIGO_ASSOCIADO =' . aspas($codigoAssoc);
	$resMesesMulta 	= jn_query($queryMesesMulta);
	$rowMesesMulta = jn_fetch_object($resMesesMulta);

	$valorConvenio = 0;
	$queryValorMulta  = ' SELECT COALESCE(PRIORIDADE0_PS1021, COALESCE(PRIORIDADE1_VLNOMINAL, COALESCE(PRIORIDADE2_PS1011, PRIORIDADE3_PS1032))) AS VALOR_CONVENIO FROM VW_PS1000_ESTIMAT_VLCONVENIO ';
	$queryValorMulta .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssoc);					
	$resValorMulta 	= jn_query($queryValorMulta);
	$rowValorMulta = jn_fetch_object($resValorMulta);
	$valorConvenio = $rowValorMulta->VALOR_CONVENIO;			

	if($rowMesesMulta->MESES > 0 && $valorConvenio > 0){
		$porcent = 50 / 100;						
		$valorMulta = ($rowMesesMulta->MESES * $rowValorMulta->VALOR_CONVENIO);
		$valorMulta = $porcent * $valorMulta;
		
		$valorTotalMulta += $valorMulta;
		
		$insereMulta  = 'INSERT INTO PS1003 (CODIGO_EVENTO, CODIGO_EMPRESA, CODIGO_ASSOCIADO, QUANTIDADE_EVENTOS, TIPO_CALCULO, VALOR_FATOR, FLAG_COBRA_DEPENDENTE, ';
		$insereMulta .= ' DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA, DESCRICAO_OBSERVACAO) VALUES ( ';
		$insereMulta .= ' "87", ' . aspas($_SESSION['codigoIdentificacao']) . ', ' . aspas($codigoAssoc) . ', 1, "V", ' . aspas($valorMulta) . ', "N", ' . dataToSql(date("01/m/Y",strtotime("+1 month"))) . ', '. dataToSql(date("t/m/Y",strtotime("+1 month"))) . ', "MULTA_ROMPIMENTO_AL2") ';
		if(jn_query($insereMulta)){
			$multaGerada = true;			
		}
	}

	return $valorTotalMulta;
}
?>