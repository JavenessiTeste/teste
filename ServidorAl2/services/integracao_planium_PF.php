<?php
require('../lib/base.php');

$jsonStr = file_get_contents("php://input");
$dados = json_decode($jsonStr);

$name = 'logPlanium.log';
$text = '\n'.date('d/m/Y').'-------------------INICIO-JSON-------------------\n'.$jsonStr."\n".date('d/m/Y').'-------------------FIM--------------------\n';	
$file = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);


//$chaveAPiPlanium = retornaValorConfiguracao('CHAVE_PLANIUM');
//$cnpjOperadora = retornaValorConfiguracao('CNPJ_OPERADORA_PLANIUM');

$faturaSalva = 'N';
$retorno = '';
$dataAssinatura 		= $dados->data_assinatura;
$numeroProposta 		= $dados->numero_proposta;
$dadosVendedor 			= $dados->corretagem;
$dadosContrato 			= $dados->contrato;
$dadosBeneficiarios		= $dados->beneficiarios;
$dadosEmpresa			= $dados->empresa;
$responsavelFinanceiro	= $dados->responsavel_financeiro;
$numRegTmp1000_net		= '';
$diaVencimento			= 0;
$dataPrimeitoFat		= '';

global $formatoData;
$formatoData = 'AAAA-DD-MM';

if(!$dados){	
	echo 'Nao foi encontrada nenhuma informacao, favor verificar se o arquivo JSON esta no padrao correto.';	
	return false;
}

$codigoEmpresa = '400';

foreach($dadosBeneficiarios as $contrato){	
	foreach($contrato as $benef){
		salvaAssociado($benef);
	}
}

function salvaAssociado($dadosAssoc){	
	global $codigoEmpresa;	
	global $dadosContrato;	
	global $diaVencimento;	
	global $dataAssinatura;	
	global $dataPrimeitoFat;	
	global $codigoPlano;	
	global $retorno;
	
	
	$dataNasc = SqlToData($dadosAssoc->data_nascimento);
	$dataNasc = str_replace("'",'',$dataNasc);	
	
	$dataVigencia = SqlToData($dadosContrato->data_vigencia);
	$dataVigencia = str_replace("'",'',$dataVigencia);	

	$flagPlanoFamiliar = 'S';
	if($codigoEmpresa != '400'){
		$flagPlanoFamiliar = 'N';
	}
		
		
	$numRegTmp1000_net = jn_gerasequencial('TMP1000_NET');
	$numRegTmp1000_net *= -1;
	
	$idTit = '';
	$codTitular = '';
	$tpAssoc = '';
	if($dadosAssoc->titular_uuid){
		$idTit = $dadosAssoc->titular_uuid;
		$tpAssoc = 'D';
		
		$queryTit = 'SELECT CODIGO_ASSOCIADO FROM TMP1000_NET WHERE UUID =' . aspas($dadosAssoc->titular_uuid);
		$resTit = jn_query($queryTit);
		$rowTit = jn_fetch_object($resTit);
		$codTitular = $rowTit->CODIGO_ASSOCIADO;
	}else{
		$idTit = $dadosAssoc->uuid;
		$codTitular = $numRegTmp1000_net;
		$tpAssoc = 'T';
	}
	
	
	$regANS = $dadosAssoc->produtos->saude->reg_ans;
	if(!$regANS){
		$regANS = $dadosAssoc->produtos->odonto->reg_ans;
	}
	
	$queryParametros = 'SELECT * FROM ESP_TRATATIVAS_PROPOSTA WHERE CODIGO_CADASTRO_ANS = ' . aspas($regANS);
	$resParametros = jn_query($queryParametros);
	$rowParametros = jn_fetch_object($resParametros);
	
	$codigoPlano = $rowParametros->CODIGO_PLANO;
	
	$dataPrimeitoFat = '';

	if($dataAssinatura){
		$dataFat = explode('-',$dataAssinatura);
		$dataPrimeitoFat = dataToSql($dataFat[2] . '/' . $dataFat[1] . '/' . $dataFat[0]);
	}else{
		$dataPrimeitoFat = 'null';
	}

	$query  = ' INSERT INTO TMP1000_NET ( ';
	$query .= ' CODIGO_PLANO, CODIGO_CARENCIA, CODIGO_TABELA_PRECO, CODIGO_MOTIVO_INCLUSAO, CODIGO_GRUPO_FATURAMENTO, CODIGO_GRUPO_PESSOAS, CODIGO_TIPO_CARACTERISTICA, CODIGO_SITUACAO_ATENDIMENTO,  ';	
	$query .= ' NOME_ASSOCIADO, UUID, TITULAR_UUID, DATA_VALIDA_CARENCIA, DATA_PRIMEIRO_FATURAMENTO, ';
	$query .= ' SEXO, DATA_NASCIMENTO, DATA_DIGITACAO, DATA_ADMISSAO, DATA_EMISSAO_RG, CODIGO_EMPRESA, FLAG_PLANOFAMILIAR, ';
	$query .= ' NOME_MAE, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG, NATUREZA_RG, CODIGO_ESTADO_CIVIL, CODIGO_PARENTESCO, CODIGO_ASSOCIADO, CODIGO_TITULAR, TIPO_ASSOCIADO, NOME_SOCIAL, CODIGO_CNS, SITUACAO_CADASTRAL, NUMERO_DECLARACAO_NASC_VIVO ';
	$query .= ' )VALUES ( ';
	$query .= aspas($rowParametros->CODIGO_PLANO) . ", ";
	$query .= aspas($rowParametros->CODIGO_CARENCIA) . ", ";
	$query .= aspas($rowParametros->CODIGO_TABELA_PRECO) . ", ";
	$query .= aspasNull($rowParametros->CODIGO_MOTIVO_INCLUSAO) . ", ";
	$query .= aspasNull($rowParametros->CODIGO_GRUPO_FATURAMENTO) . ", ";
	$query .= aspasNull($rowParametros->CODIGO_GRUPO_PESSOAS) . ", ";
	$query .= aspasNull($rowParametros->CODIGO_TIPO_CARACTERISTICA) . ", ";
	$query .= aspasNull($rowParametros->CODIGO_SITUACAO_ATENDIMENTO) . ", ";
	$query .= aspas(remove_caracteres($dadosAssoc->nome)) . ", ";
	$query .= aspas($dadosAssoc->uuid) . ", ";
	$query .= aspas($idTit) . ", ";
	$query .= dataToSql($dataVigencia) . ", ";
	$query .= $dataPrimeitoFat . ", ";
	$query .= aspas($dadosAssoc->sexo) . ", ";
	$query .= dataToSql($dataNasc) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql($dadosAssoc->data_emissao_rg) . ", ";
	$query .= aspas('400') . ", ";	
	$query .= aspasNull($flagPlanoFamiliar) . ", ";	
	$query .= aspasNull($dadosAssoc->nome_mae) . ", ";	
	$query .= aspasNull($dadosAssoc->cpf) . ", ";
	$query .= aspasNull($dadosAssoc->rg_numero) . ", ";
	$query .= aspasNull($dadosAssoc->orgao_emissor) . ", ";
	$query .= aspasNull($dadosAssoc->natureza_rg) . ", ";	
	$query .= aspasNull($dadosAssoc->estado_civil) . ", ";
	$query .= aspasNull($dadosAssoc->parentesco) . ", ";
	$query .= aspas($numRegTmp1000_net) . ", ";
	$query .= aspas($codTitular) . ", ";
	$query .= aspas($tpAssoc) . ", ";
	$query .= aspasNull($dadosAssoc->nome_social) . ",";
	$query .= aspasNull($dadosAssoc->cns) . ",";
	$query .= aspas('Pendente Para Analise') . ",";
	$query .= aspasNull($_POST['numero_declaracao_nasc_vivo']);
	$query .= " )";
	
	if (!jn_query($query)) {
		$retorno .= 'Erro: Nao foi possivel gravar o associado!';
		return false; // saio retornando false
	}else{		
		salvaTelefone($dadosAssoc, $numRegTmp1000_net);
	}
}

function salvaTelefone($dadosAssoc, $associado){
	global $retorno;
	
	$codAreaTel 	= substr($dadosAssoc->tel_fixo,0,2);
	$numeroTel 		= substr($dadosAssoc->tel_fixo,2,11);
	$codAreaCel 	= substr($dadosAssoc->tel_celular,0,2);
	$numeroCel 		= substr($dadosAssoc->tel_celular,2,11);	
	
	// salvo os dados do telefone
    if ($numeroTel != '') {
        $query  = 'INSERT INTO TMP1006_NET (CODIGO_EMPRESA, CODIGO_ASSOCIADO, INDICE_TELEFONE, CODIGO_AREA, ';
        $query .= 'NUMERO_TELEFONE) ';
        $query .= 'VALUES ( ';

        $query .= integerNull($codigoEmpresa) . ", ";
        $query .= aspasNull($associado) . ", ";
		$query .= aspas('1') . ", ";
        $query .= aspasNull($codAreaTel) . ", ";
        $query .= aspas($numeroTel);
        $query .= " )";		
		
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
    }
	if ($numeroCel != '') {
        $query  = 'INSERT INTO TMP1006_NET (CODIGO_EMPRESA, CODIGO_ASSOCIADO, INDICE_TELEFONE, CODIGO_AREA, ';
        $query .= 'NUMERO_TELEFONE) ';
        $query .= 'VALUES ( ';

        $query .= integerNull($codigoEmpresa) . ", ";
        $query .= aspasNull($associado) . ", ";
        $query .= aspas('2') . ", ";
        $query .= integerNull($codAreaCel) . ", ";
        $query .= aspas($numeroCel);
        $query .= " )";
				 
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
    } 
	
	salvaEndereco($dadosAssoc,$associado);
}

function salvaEndereco($dadosAssoc, $associado){	
	global $codigoEmpresa;
	global $dadosEmpresa;
	global $retorno;
	$dadosEndereco = '';
	$codigoAssociado = '';
	$enderecoEmail = '';	
	
	$queryEnd  = ' SELECT NUMERO_REGISTRO FROM TMP1001_NET ';
	$queryEnd .= ' WHERE 1 = 1 ';	
	
	if($codigoEmpresa == '400'){
		$dadosEndereco = $dadosAssoc->endereco1;		
		$codigoAssociado = $associado;
		$enderecoEmail = $dadosAssoc->email;
		
		$queryEnd .= ' AND CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
		
		
	}else{
		$dadosEndereco = $dadosEmpresa->endereco1;
		$enderecoEmail = $dadosEmpresa->responsavel->email;
		$queryEnd .= ' AND CODIGO_EMPRESA = ' . aspas($codigoEmpresa);
	}
			
	$resEnd = jn_query($queryEnd);
	$rowEnd = jn_fetch_object($resEnd);
	
	if(!$rowEnd->NUMERO_REGISTRO){	
		$numeroCep = $dadosEndereco->cep;
		$logradouroEndereco = $dadosEndereco->logradouro;
		
		if($dadosEndereco->numero){
			$logradouroEndereco .= ', ' . $dadosEndereco->numero;
		}
		
		if($dadosEndereco->complemento){
			$logradouroEndereco .= ' - ' . $dadosEndereco->complemento;
		}

		$bairro = $dadosEndereco->bairro;
		$cidade = $dadosEndereco->cidade;
		$estado = $dadosEndereco->uf;

		$query  = 'INSERT INTO TMP1001_NET (CODIGO_EMPRESA, CODIGO_ASSOCIADO, ENDERECO, BAIRRO, ';
		$query .= 'CIDADE, CEP, ESTADO, ENDERECO_EMAIL) ';
		$query .= 'VALUES ( ';

		$query .= integerNull($codigoEmpresa) . ", ";
		$query .= aspasNull($codigoAssociado) . ", ";
		$query .= aspas(substr($logradouroEndereco,0,45)) . ", ";
		$query .= aspas(substr($bairro,0,24)) . ", ";
		$query .= aspas($cidade) . ", ";
		$query .= aspas($numeroCep) . ", ";
		$query .= aspas($estado). ",";
		$query .= aspasNull($enderecoEmail);
		$query .= " )";

		if (! jn_query($query)) {
			$retorno .= 'Erro: Nao foi possivel gravar o endereco!';
			return false; // saio retornando false
		}
	}
	
	salvaDeclaracaoSaude($dadosAssoc, $associado);		
}

function salvaDeclaracaoSaude($dadosAssoc, $associado){			
	global $codigoPlano;
	
	if($dadosAssoc->altura){		
		
		$queryAltura  = ' SELECT TOP 1 NUMERO_PERGUNTA FROM PS1039 ';
		$queryAltura .= ' WHERE DESCRICAO_PERGUNTA LIKE "%ALTURA%" ';
		$queryAltura .= ' AND CODIGO_PLANO = ' . aspas($codigoPlano);
		$resAltura = jn_query($queryAltura);
		$rowAltura = jn_fetch_object($resAltura);
		
		$query  = 'INSERT INTO TMP1005_NET (CODIGO_ASSOCIADO, NUMERO_PERGUNTA, RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO) ';
		$query .= 'VALUES ( ';		
		$query .= aspas($associado) . ", ";
		$query .= aspas($rowAltura->NUMERO_PERGUNTA) . ", ";
		$query .= aspas('S') . ", ";
		$query .= aspas($dadosAssoc->altura);
		$query .= " )";

		if (! jn_query($query)) {
			$retorno .= 'Erro: Nao foi possivel gravar a declaracao de saude - ALTURA!';
			return false; // saio retornando false
		}
	}
		
	if($dadosAssoc->peso){
		
		$queryPeso  = ' SELECT TOP 1 NUMERO_PERGUNTA FROM PS1039 ';
		$queryPeso .= ' WHERE DESCRICAO_PERGUNTA LIKE "%PESO%" ';
		$queryPeso .= ' AND CODIGO_PLANO = ' . aspas($codigoPlano);
		$resPeso = jn_query($queryPeso);
		$rowPeso = jn_fetch_object($resPeso);

		$query  = 'INSERT INTO TMP1005_NET (CODIGO_ASSOCIADO, NUMERO_PERGUNTA, RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO) ';
		$query .= 'VALUES ( ';		
		$query .= aspas($associado) . ", ";
		$query .= aspas($rowPeso->NUMERO_PERGUNTA) . ", ";
		$query .= aspas('S') . ", ";
		$query .= aspas($dadosAssoc->peso);
		$query .= " )";

		if (! jn_query($query)) {
			$retorno .= 'Erro: Nao foi possivel gravar a declaracao de saude - PESO!';
			return false; // saio retornando false
		}
	}
	
	$i = 0;
	while($i < 23){
		$dadosAssoc->decsau->perguntas[$i];
		
		if($dadosAssoc->decsau->perguntas[$i]->resposta){
			
			$desc  = ' Ano - ' . $dadosAssoc->decsau->perguntas[$i]->ano;
			$desc .= ' Desc - ' . utf8_encode($dadosAssoc->decsau->perguntas[$i]->descricao);
			$query  = 'INSERT INTO TMP1005_NET (CODIGO_ASSOCIADO, NUMERO_PERGUNTA, RESPOSTA_DIGITADA, DESCRICAO_OBSERVACAO) ';
			$query .= 'VALUES ( ';		
			$query .= aspas($associado) . ", ";
			$query .= aspas($dadosAssoc->decsau->perguntas[$i]->id) . ", ";
			$query .= aspas('S') . ", ";
			$query .= aspas(substr($desc,0,99));
			$query .= " )";

			if (! jn_query($query)) {				
				$retorno .= 'Erro: Nao foi possivel gravar a declaracao de saude - ' . $i . '!';
				return false; // saio retornando false
			}			
		}
		
		$i++;
	}	
	
	salvaContrato($dadosAssoc, $associado);		
}

function salvaContrato($dadosAssoc, $associado){
	global $diaVencimento;
	global $numeroProposta;
	global $dadosVendedor;
	global $dadosContrato;
	global $responsavelFinanceiro;
	global $retorno;
	
	if($dadosAssoc->titular_uuid == ''){

		$queryVenc = ' SELECT DATEPART(DAY, DATA_PRIMEIRO_FATURAMENTO) DIA_FATURAMENTO FROM TMP1000_NET WHERE CODIGO_ASSOCIADO = ' . aspas($associado);
		$resVenc = jn_query($queryVenc);
		$rowVenc = jn_fetch_object($resVenc);
	
		$diaVencimento = $rowVenc->DIA_FATURAMENTO;
		
		$valorAdesao = $dadosContrato->total_valor;
		$dataVigencia = $dadosContrato->data_vigencia;
		$formaPagamento = $dadosContrato->forma_pagamento;
		
		$flagDeb ='N';
		if($formaPagamento->tipo == 'debito'){
			$flagDeb = 'S';
		}
		
		$dataVencimento = somar_dias_uteis(date('d/m/Y'),2);
		
		$queryContrato  = 'INSERT INTO TMP1002_NET ( ';
		$queryContrato .= ' CODIGO_ASSOCIADO, NUMERO_CONTRATO, ';	
		$queryContrato .= ' NOME_CONTRATANTE, NUMERO_CPF_CONTRATANTE, NUMERO_RG_CONTRATANTE, ';	
		$queryContrato .= ' DATA_DEBITO_APP, FLAG_DEBITO_AUTOMATICO, CODIGO_BANCO, NUMERO_AGENCIA, NUMERO_CONTA, ';	
		$queryContrato .= ' VALOR_ADESAO, DATA_PRIMEIRO_FATURAMENTO, DIA_VENCIMENTO ) ';	
		$queryContrato .= 'VALUES ( ';	
		$queryContrato .= aspas($associado) . ", ";	
		$queryContrato .= aspas($numeroProposta) . ", ";
		$queryContrato .= aspas(remove_caracteres($responsavelFinanceiro->nome)) . ", ";
		$queryContrato .= aspas($responsavelFinanceiro->cpf) . ", ";
		$queryContrato .= aspas($responsavelFinanceiro->rg_numero) . ", ";
		$queryContrato .= dataToSql($dataVencimento) . ", ";
		$queryContrato .= aspas($flagDeb) . ", ";
		$queryContrato .= aspas($formaPagamento->num_banco) . ", ";
		$queryContrato .= aspas($formaPagamento->num_agencia) . ", ";
		$queryContrato .= aspas($formaPagamento->num_conta . '-' . $formaPagamento->num_conta_dv) . ", ";
		$queryContrato .= aspas($valorAdesao) . ", ";
		$queryContrato .= aspas($dataVigencia) . ", ";
		$queryContrato .= aspas($diaVencimento);
		$queryContrato .= " ) ";
		
		
		if (! jn_query($queryContrato)) {
			//pr($queryContrato);
			$retorno .= 'Erro: Nao foi possivel gravar o contrato';
			return false; // saio retornando false
		}else{		
			salvaFatura($dadosAssoc, $associado);
		}
	}else{
			salvaFatura($dadosAssoc, $associado);
	}
}

function salvaFatura($dadosAssoc, $associado){
	global $codigoEmpresa;
	global $dadosContrato;
	global $faturaSalva;
	global $diaVencimento;
	global $dataPrimeitoFat;
	global $retorno;
	
	$valorFatura = $dadosContrato->total_valor;
	$valorFaturaBenef = $dadosAssoc->produtos->saude->valor;
	if(!$valorFaturaBenef){
		$valorFaturaBenef = $dadosAssoc->produtos->odonto->valor;
	}
	$diasAdd = '1';	
	
	$dataVencimento = somar_dias_uteis(date('d/m/Y'),2);
	
	if($codigoEmpresa == '400'){
		if(!$dadosAssoc->titular_uuid){
			$queryTmp1020  = ' INSERT TMP1020_NET (CODIGO_ASSOCIADO, CODIGO_EMPRESA, DATA_VENCIMENTO, VALOR_FATURA, DATA_EMISSAO, MES_ANO_REFERENCIA, IDENTIFICACAO_GERACAO, NUMERO_PARCELA) ';
			$queryTmp1020 .= ' VALUES ';
			$queryTmp1020 .= " (" . aspas($associado) .  ", " . aspas($codigoEmpresa) . ", " . dataToSql($dataVencimento) . ", " . aspas($valorFatura) . ", dateadd(day," . $diasAdd . ",getdate()),"; 
			//$queryTmp1020 .= " (" . aspas($associado) .  ", " . aspas($codigoEmpresa) . ", dateadd(month,1," . aspas(date('m') . '.' . $diaVencimento . '.' . date('Y')) . "), " . aspas($valorFatura) . ", dateadd(day," . $diasAdd . ",getdate()),"; 
			$queryTmp1020 .= " RIGHT('0' + cast(DATEPART(month , dateadd(day," . $diasAdd . ",getdate())) as varchar(2)),2) + '/' + cast(DATEPART(year , dateadd(day," . $diasAdd . ",getdate())) as varchar(4)), 'FAT_GETNET', " . aspas('1') . ")";		

			if (! jn_query($queryTmp1020)) {
				$retorno .= 'Erro: Nao foi possivel gravar a fatura (TMP1020_NET)';
				return false; // saio retornando false
			}
		}
	}
	
	$filtro = '';	
	if($codigoEmpresa == '400'){
		if(!$dadosAssoc->titular_uuid){
			$filtro = ' AND CODIGO_ASSOCIADO = ' . aspas($associado);			
		}else{
			$queryDep  = ' SELECT FIRST 1 CODIGO_ASSOCIADO FROM TMP1000_NET ';
			$queryDep .= ' WHERE UUID = ' . aspas($dadosAssoc->titular_uuid);
			$queryDep .= ' ORDER BY CODIGO_ASSOCIADO DESC';
			$resDep = jn_query($queryDep);
			$rowDep = jn_fetch_object($resDep);
			$filtro = ' AND CODIGO_ASSOCIADO = ' . aspas($rowDep->CODIGO_ASSOCIADO);
		}
	}
	
	$query1020  = ' SELECT FIRST 1 NUMERO_REGISTRO FROM TMP1020_NET ';	
	$query1020 .= ' WHERE 1=1 ';
	$query1020 .= $filtro;
	$query1020 .= ' ORDER BY NUMERO_REGISTRO DESC ';
	$res1020 = jn_query($query1020);
	$row1020 = jn_fetch_object($res1020);
	$numRegFat = $row1020->NUMERO_REGISTRO;	
	
	$queryTmp1021  = ' INSERT TMP1021_NET (CODIGO_ASSOCIADO, CODIGO_EMPRESA, NUMERO_REGISTRO_PS1020, DATA_EMISSAO, MES_ANO_VENCIMENTO, VALOR_FATURA) ';
	$queryTmp1021 .= ' VALUES ';
	$queryTmp1021 .= " (" . aspasNull($associado) .  ", " . aspas($codigoEmpresa) . ", " . aspas($numRegFat) . ", dateadd(day," . $diasAdd . ",getdate()), RIGHT('0' + cast(DATEPART(month , dateadd(day," . $diasAdd . ",getdate())) as varchar(2)),2) + '/' + cast(DATEPART(year , dateadd(day," . $diasAdd . ",getdate())) as varchar(4)), " . aspasNull($valorFaturaBenef) . ")";			
	
	if (! jn_query($queryTmp1021)) {
		$retorno .= 'Erro: Nao foi possivel gravar a fatura do associado ' . $associado . '! <br> ';
		return false; // saio retornando false
	}else{
		//atualizaProposta();
		$retorno = 'OK: Registro gravado com sucesso!';
	}
	
}


function atualizaProposta(){
	global $numeroProposta, $chaveAPiPlanium, $cnpjOperadora;
	
	$filedata = array();
	$filedata['cnpj_operadora'] = $cnpjOperadora;
	$proposta['numero_proposta'] = $numeroProposta;
	$proposta['status'] = 4;
	$filedata['propostas'][] = $proposta;

	$url       = 'https://dnv-api.planium.io/prod/proposta/status/v1';
	$cabecalho = array('Content-Type: application/json', 'Accept: application/json','Planium-Apikey: ' . $chaveAPiPlanium);
	
	$campos    = json_encode($filedata);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,            $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER,     $cabecalho);
	curl_setopt($ch, CURLOPT_POSTFIELDS,     $campos);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST,           true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'PUT');

	$resposta = curl_exec($ch);
	$err = curl_error($ch);

	curl_close($ch);

	if ($err) {
	  $dados = "cURL Error #:" . $err;
	  $retorno = $dados;
	} else {
		$retorno = $resposta;	  
	}
	
}


function somar_dias_uteis($str_data,$int_qtd_dias_somar) {
	$str_data = substr($str_data,0,10);

	if ( preg_match('@/@',$str_data) == 1 ) {
	$str_data = implode('-', array_reverse(explode('/',$str_data)));
	}

	$array_data = explode('-', $str_data);
	$count_days = 0;
	$int_qtd_dias_uteis = 0;

	while ( $int_qtd_dias_uteis < $int_qtd_dias_somar ) {
	$count_days++;
	if ( ( $dias_da_semana = gmdate('w', strtotime('+'.$count_days.' day', mktime(0, 0, 0, $array_data[1], $array_data[2], $array_data[0]))) ) != '0' && $dias_da_semana != '6' ) {
	$int_qtd_dias_uteis++;
	}
	}

	return gmdate('d/m/Y',strtotime('+'.$count_days.' day',strtotime($str_data)));
}

echo $retorno;
?>