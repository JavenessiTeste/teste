<?php

function efetivaBeneficiario($fatura){
	ob_start();
	
	$sqlFatura ="select * FROM TMP1020_NET where numero_registro =".aspas($fatura);
	$resFatura  = jn_query($sqlFatura);
	$rowFatura = jn_fetch_object($resFatura);
	
	$codigoAssociado = $rowFatura->CODIGO_ASSOCIADO;
	$codigoEmpresa   = $rowFatura->CODIGO_EMPRESA;
	
	if($codigoEmpresa==400){
		$codigoTitularGerado = importaTitular($codigoEmpresa,$codigoAssociado);
		$codigoEmpresaGerada = '400';
	}else{
		$codigoEmpresaGerada = importaEmpresa($codigoEmpresa);
	}
	importaFatura($fatura,$codigoEmpresaGerada,$codigoTitularGerado);
	
	if($codigoEmpresaGerada == '400')
		$sqlContrato ="select NUMERO_CONTRATO FROM PS1002 where CODIGO_ASSOCIADO =".aspas($codigoTitularGerado);
	else
		$sqlContrato ="select NUMERO_CONTRATO FROM PS1002 where CODIGO_EMPRESA =".aspas($codigoEmpresaGerada);
	
	$resContrato  = jn_query($sqlContrato);
	$rowContrato = jn_fetch_object($resContrato);
	
	if($rowContrato->NUMERO_CONTRATO!='')
		atualizaProposta($rowContrato->NUMERO_CONTRATO);
	
	if($codigoEmpresaGerada == 400){
		$sqlDigitacao = "UPDATE PS1000 SET DATA_DIGITACAO = getdate() WHERE CODIGO_TITULAR = " . aspas($codigoTitularGerado);
	}else{
		$sqlDigitacao = "UPDATE PS1000 SET DATA_DIGITACAO = getdate() WHERE CODIGO_EMPRESA = " . aspas($codigoEmpresaGerada);
	}
	jn_query($sqlDigitacao);
	
	$dados = ob_get_contents();
	ob_end_clean();
	//echo $dados;
	if(trim($dados) != ''){
		$name = 'efetivaAssociado.log';
		$text = '\n'.date('d/m/Y').'-------------------INICIO--------------------\n'.$dados."\n".date('d/m/Y').'-------------------FIM--------------------\n';
		$file = fopen($name, 'a');
		fwrite($file, $text,strlen($text));
		fclose($file);
	}
}
function importaFatura($fatura,$codigoEmpresa,$codigoAssociado){
	if($codigoEmpresa!='400')
			$codigoAssociado = '';
		
	$numeroRegistro = jn_gerasequencial('PS1020');
	
	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
					A.NOME_TABELA = 'PS1020' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
					select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
					B.NOME_TABELA = 'TMP1020_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' 
					and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO' and B.NOME_CAMPO <>'DESCRICAO_OBSERVACAO' 
					)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1020 = "insert into Ps1020(NUMERO_REGISTRO,CODIGO_EMPRESA,CODIGO_ASSOCIADO,DESCRICAO_OBSERVACAO,".$campos.")
						 select ".aspas($numeroRegistro).", ".aspasNull($codigoEmpresa).", ".aspasNull($codigoAssociado).",".aspasNull('FATURA EFETIVADA: '.$fatura).",".$campos." FROM TMP1020_NET where numero_registro = ".aspas($fatura);

	if(!jn_query($queryInsertPS1020)){
		pr($queryInsertPS1020);
		//exit;
	}
	importaDetFatura($numeroRegistro,$fatura);
}
function importaDetFatura($faturagerada,$fatura){
	if($empresa!='400')
			$codigoAssociado = '';
		
	
	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
					A.NOME_TABELA = 'PS1021' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
					select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
					B.NOME_TABELA = 'TMP1021_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' 
					and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO' 
					and  B.NOME_CAMPO <> 'NUMERO_REGISTRO_PS1020'
					)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos =='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryItens ='select * from TMP1021_NET where NUMERO_REGISTRO_PS1020 ='.aspas($fatura);
	$resItens = jn_query($queryItens);
	
	
	while($rowItens  = jn_fetch_object($resItens)){
		$codigoEmpresa = $rowItens->CODIGO_EMPRESA;
		if(($rowItens->CODIGO_EMPRESA) !='' and($rowItens->CODIGO_EMPRESA!='400')){
			$queryBenef ='select * from PS1010 where CODIGO_ANTIGO ='.aspas($rowItens->CODIGO_EMPRESA);
			$resBenef = jn_query($queryBenef);
			$rowBenef  = jn_fetch_object($resBenef);
			$codigoEmpresa = $rowBenef->CODIGO_EMPRESA;
		}
		$queryBenef ='select * from PS1000 where CODIGO_ANTIGO ='.aspas($rowItens->CODIGO_ASSOCIADO);
		$resBenef = jn_query($queryBenef);
		$rowBenef  = jn_fetch_object($resBenef);
		$codigoAssociado = $rowBenef->CODIGO_ASSOCIADO;
		
		$queryInsertPS1021 = "insert into Ps1021(NUMERO_REGISTRO_PS1020,CODIGO_EMPRESA,CODIGO_ASSOCIADO,".$campos.")
							select ".aspasNull($faturagerada).", ".aspasNull($codigoEmpresa).", ".aspasNull($codigoAssociado).",".$campos." FROM TMP1021_NET where NUMERO_REGISTRO_PS1020 = ".aspas($fatura);

			
		
		if(!jn_query($queryInsertPS1021)){
		pr($queryInsertPS1021);
		//exit;
	}
	}	

}
function importaTitular($codigoEmpresaGerado,$codigoAssociado){
	$codigoSequencial = jn_gerasequencial('PS1000');
	$codigoTitularGerado = '01'.substr($codigoEmpresaGerado, 0, 3).str_pad(substr($codigoSequencial, 0, 7), 7, "0", STR_PAD_LEFT).'000';
	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
					A.NOME_TABELA = 'PS1000' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
					select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
					B.NOME_TABELA = 'TMP1000_GETNET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' 
					and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO' and  B.NOME_CAMPO <>'CODIGO_ANTIGO' and  B.NOME_CAMPO <>'NUMERO_DEPENDENTE'
					and B.NOME_CAMPO <>'CODIGO_TITULAR'
					)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1000 = "insert into Ps1000(CODIGO_EMPRESA,
											 CODIGO_ASSOCIADO,
											 CODIGO_TITULAR,
											 CODIGO_SEQUENCIAL,
											 CODIGO_ANTIGO,
											 NUMERO_DEPENDENTE,".
											 $campos.")
						 select ".aspas($codigoEmpresaGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoSequencial).",".
								  aspas($codigoAssociado).","
								  ."0,".
								  $campos." FROM TMP1000_GETNET where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);
	
	if(!jn_query($queryInsertPS1000)){
		pr($queryInsertPS1000);
		//exit;
	}
	if($codigoEmpresaGerado==400){
		importaEnderecoAssociado($codigoEmpresaGerado,$codigoTitularGerado,$codigoAssociado);
		importaContratoAssociado($codigoTitularGerado,$codigoAssociado);
		importaTelefoneAssociado($codigoTitularGerado,$codigoAssociado);
	}
	
	importaQuestionarioAssociado($codigoTitularGerado,$codigoAssociado);

	$queryDep ='select CODIGO_ASSOCIADO from TMP1000_GETNET where TIPO_ASSOCIADO='.aspas('D').' and CODIGO_TITULAR ='.aspas($codigoAssociado);
	$resDep  = jn_query($queryDep);
	
	$i=1;
	while($rowDep  = jn_fetch_object($resDep)){
		importaDependente($codigoEmpresaGerado,$codigoTitularGerado,$rowDep->CODIGO_ASSOCIADO,$codigoSequencial,$i);
		$i++;
	}
	
	return $codigoTitularGerado; 
	
}
function importaDependente($codigoEmpresaGerado,$codigoTitularGerado,$codigoAssociado,$codigoSequencial,$numeroDependente){
	
	$codigoAssociadoGerado = '01'.substr($codigoEmpresaGerado, 0, 3).str_pad(substr($codigoSequencial, 0, 7), 7, "0", STR_PAD_LEFT).str_pad(substr($numeroDependente, 0, 2), 2, "0", STR_PAD_LEFT).'0';
	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
					A.NOME_TABELA = 'PS1000' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
					select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
					B.NOME_TABELA = 'TMP1000_GETNET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' 
					and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO' and  B.NOME_CAMPO <>'CODIGO_ANTIGO' and  B.NOME_CAMPO <>'NUMERO_DEPENDENTE'
					and B.NOME_CAMPO <>'CODIGO_TITULAR'
					)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1000 = "insert into Ps1000(CODIGO_EMPRESA,
											 CODIGO_ASSOCIADO,
											 CODIGO_TITULAR,
											 CODIGO_SEQUENCIAL,
											 CODIGO_ANTIGO,
											 NUMERO_DEPENDENTE,".
											 $campos.")
						 select ".aspas($codigoEmpresaGerado).",".
								  aspas($codigoAssociadoGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoSequencial).",".
								  aspas($codigoAssociado).",".
								  aspas($numeroDependente).",".
								  $campos." FROM TMP1000_GETNET where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);
	
	if(!jn_query($queryInsertPS1000)){
		pr($queryInsertPS1000);
		//exit;
	}
	
	importaQuestionarioAssociado($codigoAssociadoGerado,$codigoAssociado);
	
}
function importaEmpresa($codigoEmpresa){

	$codigoEmpresaGerada = jn_gerasequencial('PS1010');
	
	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
					A.NOME_TABELA = 'PS1010' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
					select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
					B.NOME_TABELA = 'TMP1010_NET' and B.NOME_CAMPO <>'CODIGO_ANTIGO'
					)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1010 = "insert into Ps1010(CODIGO_EMPRESA,CODIGO_ANTIGO,".$campos.")
						 select ".aspas($codigoEmpresaGerada).",".aspas($codigoEmpresa).",".$campos." FROM TMP1010_NET where CODIGO_EMPRESA = ".aspas($codigoEmpresa);

	
	if(!jn_query($queryInsertPS1010)){
		pr($queryInsertPS1010);
		//exit;
	}
	
	importaEnderecoEmpresa($codigoEmpresaGerada,$codigoEmpresa);
	importaContratoEmpresa($codigoEmpresaGerada,$codigoEmpresa);
	importaTelefoneEmpresa($codigoEmpresaGerada,$codigoEmpresa);
	importaPs1059($codigoEmpresaGerada,$codigoEmpresa);
	
	$queryTit = 'select CODIGO_ASSOCIADO from TMP1000_GETNET where TIPO_ASSOCIADO='.aspas('T').' and CODIGO_EMPRESA ='.aspas($codigoEmpresa);
	$resTit  = jn_query($queryTit);
	while($rowTit  = jn_fetch_object($resTit)){
		importaTitular($codigoEmpresaGerada,$rowTit->CODIGO_ASSOCIADO);
	}
	
	return $codigoEmpresaGerada; 
	
}
function importaEnderecoEmpresa($codigoEmpresaGerada,$codigoEmpresa){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1001' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1001_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1001 = "insert into Ps1001(CODIGO_EMPRESA,CODIGO_ASSOCIADO,".$campos.")
						 select ".aspas($codigoEmpresaGerada).",NULL,".$campos." FROM TMP1001_NET where CODIGO_EMPRESA = ".aspas($codigoEmpresa);

	
	if(!jn_query($queryInsertPS1001)){
		pr($queryInsertPS1001);
		//exit;
	}

}

function importaContratoEmpresa($codigoEmpresaGerada,$codigoEmpresa){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1002' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1002_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1002 = "insert into Ps1002(CODIGO_EMPRESA,CODIGO_ASSOCIADO,".$campos.")
						 select ".aspas($codigoEmpresaGerada).",NULL,".$campos." FROM TMP1002_NET where CODIGO_EMPRESA = ".aspas($codigoEmpresa);

	
	if(!jn_query($queryInsertPS1002)){
		pr($queryInsertPS1002);
		//exit;
	}

}

function importaTelefoneEmpresa($codigoEmpresaGerada,$codigoEmpresa){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1006' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1006_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1006 = "insert into Ps1006(CODIGO_EMPRESA,CODIGO_ASSOCIADO,".$campos.")
						 select ".aspas($codigoEmpresaGerada).",NULL,".$campos." FROM TMP1006_NET where CODIGO_EMPRESA = ".aspas($codigoEmpresa);

	if(!jn_query($queryInsertPS1006)){
		pr($queryInsertPS1006);
		//exit;
	}
	
	

}

function importaPs1059($codigoEmpresaGerada,$codigoEmpresa){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1059' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1059_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1059 = "insert into PS1059(CODIGO_EMPRESA,".$campos.")
						 select ".aspas($codigoEmpresaGerada).",".$campos." FROM TMP1059_NET where CODIGO_EMPRESA = ".aspas($codigoEmpresa);

	if(!jn_query($queryInsertPS1059)){
		pr($queryInsertPS1059);
		//exit;
	}
	
	

}


function importaEnderecoAssociado($codigoEmpresa,$codigoAssociadoGerado,$codigoAssociado){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1001' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1001_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1001 = "insert into Ps1001(CODIGO_EMPRESA,CODIGO_ASSOCIADO,".$campos.")
						 select ".aspas($codigoEmpresa).",".aspas($codigoAssociadoGerado).",".$campos." FROM TMP1001_NET where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	
	if(!jn_query($queryInsertPS1001)){
		pr($queryInsertPS1001);
		//exit;
	}

}
function importaContratoAssociado($codigoAssociadoGerado,$codigoAssociado){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1002' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1002_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1002 = "insert into Ps1002(CODIGO_EMPRESA,CODIGO_ASSOCIADO,".$campos.")
						 select NULL,".aspas($codigoAssociadoGerado).",".$campos." FROM TMP1002_NET where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	
	if(!jn_query($queryInsertPS1002)){
		pr($queryInsertPS1002);
		//exit;
	}
	

}
function importaQuestionarioAssociado($codigoAssociadoGerado,$codigoAssociado){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1005' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1005_NET' and   B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1005 = "insert into Ps1005(CODIGO_ASSOCIADO,".$campos.")
						 select ".aspas($codigoAssociadoGerado).",".$campos." FROM TMP1005_NET where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	if(!jn_query($queryInsertPS1005)){
		pr($queryInsertPS1005);
		//exit;
	}

}
function importaTelefoneAssociado($codigoAssociadoGerado,$codigoAssociado){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1006' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'TMP1006_NET' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
				)";
	$resCampos  = jn_query($querycampos);
	
	$campos = '';
	while($rowCampos  = jn_fetch_object($resCampos)){
		if($campos=='')
			$campos = $rowCampos->NOME_CAMPO;
		else
			$campos .= ','.$rowCampos->NOME_CAMPO;
	}
	
	$queryInsertPS1006 = "insert into Ps1006(CODIGO_EMPRESA,CODIGO_ASSOCIADO,".$campos.")
						 select NULL,".aspas($codigoAssociadoGerado).",".$campos." FROM TMP1006_NET where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	if(!jn_query($queryInsertPS1006)){
		pr($queryInsertPS1006);
		//exit;
	}
	

}
function atualizaProposta($numeroProposta){

	$filedata = array();
	$filedata['cnpj_operadora'] = '00338763000147';
	$proposta['numero_proposta'] = $numeroProposta;
	$proposta['status'] = 1;
	$filedata['propostas'][] = $proposta;

	$url       = 'https://dnv-api.planium.io/prod/proposta/status/v1';
	$cabecalho = array('Content-Type: application/json', 'Accept: application/json','Planium-Apikey: 0b13255d8e9d42c6c726f795645047fadfa63e802e33439286f56b64014dbe4b');
	
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
	} else {
	  $dados =  $resposta;
	}
	
	$name = 'atualizaProposta.log';
	$text = date('d/m/Y').' '.$numeroProposta.' '.$dados."\n";
	$file = fopen($name, 'a');
	fwrite($file, $text,strlen($text));
	fclose($file);

}

?>