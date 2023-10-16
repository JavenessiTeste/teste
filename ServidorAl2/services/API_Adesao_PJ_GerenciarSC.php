<?php
require('../lib/base.php');

$jsonStr = file_get_contents("php://input");
$dados = json_decode($jsonStr);

$faturaSalva = 'N';
$retorno = '';
$dadosContrato 			= $dados->contrato;
$dadosBeneficiarios		= $dados->beneficiarios;
$dadosEmpresa			= $dados->empresa;
$numRegTmp1000_net		= '';
$diaVencimento			= 0;
$numeroProposta 		= $dados->numero_proposta;
$regANS 				= '';

global $formatoData;
$formatoData = 'AAAA-DD-MM';


if(!$dados){	
	echo 'Nao foi encontrada nenhuma informacao, favor verificar se o arquivo JSON esta no padrao correto.';	
	return false;
}


if($dadosEmpresa->cnpj == ''){
	echo 'Erro: Nao foi encontrado o CNPJ da empresa.';	
	return false;
}else{
	salvaEmpresa();
}

foreach($dadosBeneficiarios as $contrato){	
	foreach($contrato as $benef){
		salvaAssociadoPJ($benef);
	}
}

function salvaEmpresa(){
	global $retorno;
	global $dadosEmpresa;
	global $codigoEmpresa;
	global $dadosBeneficiarios;
	global $regANS;
	

	foreach($dadosBeneficiarios as $contrato){
		foreach($contrato as $benef){			
			$regANS = $benef->produtos->saude->reg_ans;
			if(!$regANS){
				$regANS = $benef->produtos->odonto->reg_ans;
			}
			break;			
		}
	}
	
	$codigoEmpresa = jn_gerasequencial('TMP1010_NET');

	$nome = substr($dadosEmpresa->razaosocial,0,50);
	$nomeFantasia = substr($dadosEmpresa->nomefantasia,0,50);
	
	$insertEmpresa  = ' INSERT INTO TMP1010_NET ( ';
	$insertEmpresa .= ' 	CODIGO_EMPRESA, DATA_ADMISSAO, DATA_DIGITACAO,  ';
	$insertEmpresa .= ' 	NOME_EMPRESA, NOME_USUAL_FANTASIA, NUMERO_CNPJ ';
	$insertEmpresa .= ' ) VALUES ( ';
	$insertEmpresa .= aspas($codigoEmpresa) . ', getdate(), getdate(), ';
	$insertEmpresa .= aspas($nome) . ', ' . aspas($nomeFantasia) . ',' . aspas($dadosEmpresa->cnpj) . ' )';
		
	if (! jn_query($insertEmpresa)) {
		pr($insertEmpresa);
		$retorno .= 'Erro: Nao foi possivel cadastrar a empresa';
		return false; // saio retornando false
	}
		
	salvaContratoPJ();

}

function salvaContratoPJ(){
	global $retorno;
	global $diaVencimento;
	global $codigoEmpresa;	
	global $numeroProposta;
	global $dadosVendedor;
	global $dadosContrato;
	global $dadosEmpresa;
	
	if(!$diaVencimento){
		$diaVencimento = 10;
	}	
	
	$valorAdesao = $dadosContrato->total_valor;
	$dataVigencia = $dadosContrato->data_vigencia;
	
	$queryContrato  = 'INSERT INTO TMP1002_NET ( ';
	$queryContrato .= ' CODIGO_EMPRESA, NUMERO_CONTRATO, ';	
	$queryContrato .= ' NOME_CONTRATANTE, NUMERO_CPF_CONTRATANTE, ';	
	$queryContrato .= ' VALOR_ADESAO, DATA_PRIMEIRO_FATURAMENTO, DIA_VENCIMENTO ) ';	
	$queryContrato .= 'VALUES ( ';	
	$queryContrato .= aspas($codigoEmpresa) . ", ";	
	$queryContrato .= aspas($numeroProposta) . ", ";
	$queryContrato .= aspas(remove_caracteres($dadosEmpresa->responsavel->nome)) . ", ";
	$queryContrato .= aspas($dadosEmpresa->responsavel->cpf) . ", ";	
	$queryContrato .= aspas($valorAdesao) . ", ";
	$queryContrato .= aspas($dataVigencia) . ", ";
	$queryContrato .= aspas($diaVencimento);
	$queryContrato .= " ) ";
	
	$queryContrato  = 'INSERT INTO TMP1002_NET (CODIGO_EMPRESA, DIA_VENCIMENTO) ';
	$queryContrato .= 'VALUES ( ';	
	$queryContrato .= aspas($codigoEmpresa) . ", ";	
	$queryContrato .= aspas($diaVencimento);
	$queryContrato .= ' ) ';
	
	
	if (! jn_query($queryContrato)) {
		$retorno .= 'Erro: Nao foi possivel gravar o contrato';
		return false; // saio retornando false
	}
}

function salvaAssociadoPJ($dadosAssoc){
	global $codigoEmpresa;	
	global $retorno;
	global $regANS;	
	
	$dataNasc = SqlToData($dadosAssoc->data_nascimento);
	$dataNasc = str_replace("'",'',$dataNasc);	

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
	
	$queryParametros = 'SELECT * FROM ESP_TRATATIVAS_PROPOSTA WHERE CODIGO_CADASTRO_ANS = ' . aspas($regANS);
	$resParametros = jn_query($queryParametros);
	$rowParametros = jn_fetch_object($resParametros);

	
	$query  = ' INSERT INTO TMP1000_NET (CODIGO_PLANO, CODIGO_CARENCIA, CODIGO_TABELA_PRECO, CODIGO_MOTIVO_INCLUSAO, CODIGO_GRUPO_FATURAMENTO, CODIGO_GRUPO_PESSOAS, CODIGO_TIPO_CARACTERISTICA, ';
	$query .= ' NOME_ASSOCIADO, UUID, TITULAR_UUID, ';
	$query .= ' SEXO, DATA_NASCIMENTO, DATA_DIGITACAO, DATA_ADMISSAO, DATA_EMISSAO_RG, CODIGO_EMPRESA, FLAG_PLANOFAMILIAR, ';
	$query .= ' NOME_MAE, NUMERO_CPF, NUMERO_RG, ORGAO_EMISSOR_RG, NATUREZA_RG, CODIGO_ESTADO_CIVIL, CODIGO_PARENTESCO, CODIGO_ASSOCIADO, CODIGO_TITULAR, TIPO_ASSOCIADO, CODIGO_CNS, SITUACAO_CADASTRAL, NUMERO_DECLARACAO_NASC_VIVO ';
	$query .= ' )VALUES ( ';
	$query .= aspas($rowParametros->CODIGO_PLANO) . ", ";
	$query .= aspas($rowParametros->CODIGO_CARENCIA) . ", ";
	$query .= aspas($rowParametros->CODIGO_TABELA_PRECO) . ", ";
	$query .= aspas($rowParametros->CODIGO_MOTIVO_INCLUSAO) . ", ";
	$query .= aspas($rowParametros->CODIGO_GRUPO_FATURAMENTO) . ", ";
	$query .= aspas($rowParametros->CODIGO_GRUPO_PESSOAS) . ", ";
	$query .= aspas($rowParametros->CODIGO_TIPO_CARACTERISTICA) . ", ";
	$query .= aspas(remove_caracteres($dadosAssoc->nome)) . ", ";
	$query .= aspas($dadosAssoc->uuid) . ", ";
	$query .= aspas($idTit) . ", ";
	$query .= aspas($dadosAssoc->sexo) . ", ";
	$query .= dataToSql($dataNasc) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql(date('d/m/Y')) . ", ";
	$query .= dataToSql($dadosAssoc->data_emissao_rg) . ", ";
	$query .= aspas($codigoEmpresa) . ", ";	
	$query .= aspasNull($flagPlanoFamiliar) . ", ";	
	$query .= aspasNull($dadosAssoc->nome_mae) . ", ";	
	$query .= aspasNull($dadosAssoc->cpf) . ", ";
	$query .= aspasNull($dadosAssoc->rg) . ", ";
	$query .= aspasNull($dadosAssoc->orgao_emissor) . ", ";
	$query .= aspasNull($dadosAssoc->natureza_rg) . ", ";	
	$query .= aspasNull($dadosAssoc->estado_civil) . ", ";
	$query .= aspasNull($dadosAssoc->parentesco) . ", ";
	$query .= aspas($numRegTmp1000_net) . ", ";
	$query .= aspas($codTitular) . ", ";
	$query .= aspas($tpAssoc) . ", ";
	$query .= aspasNull($dadosAssoc->cns) . ",";
	$query .= aspas('Pendente Para Analise') . ",";
	$query .= aspasNull($_POST['numero_declaracao_nasc_vivo']);
	$query .= " )";
	
	if (!jn_query($query)) {
		$retorno .= 'Erro: Nao foi possivel gravar o associado!';
		return false; // saio retornando false
	}else{		
		salvaTelefonePJ($dadosAssoc, $numRegTmp1000_net);
	}
}

function salvaTelefonePJ($dadosAssoc, $associado){
	global $retorno;
	global $codigoEmpresa;	
	global $dadosEmpresa;
	
	$codAreaTel 	= substr($dadosEmpresa->tel_fixo,0,2);
	$numeroTel 		= substr($dadosEmpresa->tel_fixo,2,11);
	$codAreaCel 	= substr($dadosEmpresa->tel_celular,0,2);
	$numeroCel 		= substr($dadosEmpresa->tel_celular,2,11);	
	
	// salvo os dados do telefone
    if ($numeroTel != '') {
        $query  = 'INSERT INTO TMP1006_NET (CODIGO_EMPRESA, INDICE_TELEFONE, CODIGO_AREA, ';
        $query .= 'NUMERO_TELEFONE) ';
        $query .= 'VALUES ( ';

        $query .= integerNull($codigoEmpresa) . ", ";
		$query .= aspas('1') . ", ";
        $query .= aspasNull($codAreaTel) . ", ";
        $query .= aspas($numeroTel);
        $query .= " )";		
		
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
    }
	if ($numeroCel != '') {
        $query  = 'INSERT INTO TMP1006_NET (CODIGO_EMPRESA, INDICE_TELEFONE, CODIGO_AREA, ';
        $query .= 'NUMERO_TELEFONE) ';
        $query .= 'VALUES ( ';

        $query .= integerNull($codigoEmpresa) . ", ";        
        $query .= aspas('2') . ", ";
        $query .= integerNull($codAreaCel) . ", ";
        $query .= aspas($numeroCel);
        $query .= " )";
				 
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
    } 
	
	salvaEnderecoPJ($dadosAssoc,$associado);
}

function salvaEnderecoPJ($dadosAssoc, $associado){	
	global $codigoEmpresa;
	global $dadosEmpresa;
	global $retorno;	
	
	$dadosEndereco = $dadosEmpresa->endereco1;
	$enderecoEmail = $dadosEmpresa->responsavel->email;

	$queryEnd  = ' SELECT NUMERO_REGISTRO FROM TMP1001_NET ';
	$queryEnd .= ' WHERE CODIGO_EMPRESA = ' . aspas($codigoEmpresa);

			
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

		$query  = 'INSERT INTO TMP1001_NET (CODIGO_EMPRESA, ENDERECO, BAIRRO, ';
		$query .= 'CIDADE, CEP, ESTADO, ENDERECO_EMAIL) ';
		$query .= 'VALUES ( ';

		$query .= integerNull($codigoEmpresa) . ", ";		
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
	
	salvaFaturaPJ($dadosAssoc, $associado);		
}

function salvaFaturaPJ($dadosAssoc, $associado){
	global $codigoEmpresa;
	global $dadosContrato;
	global $faturaSalva;
	global $retorno;
	global $diaVencimento;
	
	$valorFatura = $dadosContrato->total_valor;
	$valorFaturaBenef = $dadosAssoc->produtos->saude->valor;
	if(!$valorFaturaBenef){
		$valorFaturaBenef = $dadosAssoc->produtos->odonto->valor;
	}
	$diasAdd = '1';	
	
	if($faturaSalva == 'N'){
		$queryTmp1020  = ' INSERT TMP1020_NET (CODIGO_EMPRESA, DATA_VENCIMENTO, VALOR_FATURA, DATA_EMISSAO, MES_ANO_REFERENCIA, IDENTIFICACAO_GERACAO, NUMERO_PARCELA) ';
		$queryTmp1020 .= ' VALUES ';
		$queryTmp1020 .= " (" . aspas($codigoEmpresa) . ", dateadd(month,1," . aspas(date('m') . '.' . $diaVencimento . '.' . date('Y')) . "), " . aspas($valorFatura) . ", dateadd(day," . $diasAdd . ",getdate()),"; 
		$queryTmp1020 .= " cast(DATEPART(month , dateadd(day," . $diasAdd . ",getdate())) as varchar(2)) + '/' + cast(DATEPART(year , dateadd(day," . $diasAdd . ",getdate())) as varchar(4)), 'FAT_GETNET', " . aspas('1') . ")";			
		
		if (! jn_query($queryTmp1020)) {
			$retorno .= 'Erro: Nao foi possivel gravar a fatura (TMP1020_NET)';
			return false; // saio retornando false
		}else{
			$faturaSalva = 'S';				
		}
	}
	

	$query1020  = ' SELECT FIRST 1 NUMERO_REGISTRO FROM TMP1020_NET ';	
	$query1020 .= ' WHERE CODIGO_EMPRESA = ' . aspas($codigoEmpresa);
	$query1020 .= ' ORDER BY NUMERO_REGISTRO DESC ';
	$res1020 = jn_query($query1020);
	$row1020 = jn_fetch_object($res1020);
	$numRegFat = $row1020->NUMERO_REGISTRO;	
	
	$queryTmp1021  = ' INSERT TMP1021_NET (CODIGO_ASSOCIADO, CODIGO_EMPRESA, NUMERO_REGISTRO_PS1020, DATA_EMISSAO, MES_ANO_VENCIMENTO, VALOR_FATURA) ';
	$queryTmp1021 .= ' VALUES ';
	$queryTmp1021 .= " (" . aspasNull($associado) .  ", " . aspas($codigoEmpresa) . ", " . aspas($numRegFat) . ", dateadd(day," . $diasAdd . ",getdate()), cast(DATEPART(month , dateadd(day," . $diasAdd . ",getdate())) as varchar(2)) + '/' + cast(DATEPART(year , dateadd(day," . $diasAdd . ",getdate())) as varchar(4)), " . aspasNull($valorFaturaBenef) . ")";			

	if (! jn_query($queryTmp1021)) {
		$retorno .= 'Erro: Nao foi possivel gravar a fatura do associado ' . $associado . '! <br> ';
		return false; // saio retornando false
	}else{
		$retorno = 'OK: Registro gravado com sucesso!';
	}
}

echo $retorno;
?>