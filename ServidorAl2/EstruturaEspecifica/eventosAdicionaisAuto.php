<?php
if(@$_GET['CELULAR']=='OK'){
	require('../lib/autenticaCelular.php');
	if(substr($_SESSION['PERFIL_USUARIO'],0,9)=='ASSOCIADO'){
		$_SESSION['perfilOperador'] = 'BENEFICIARIO';
	}	
	
}else{

	require('../lib/base.php');
	require('../private/autentica.php');

}

require('../lib/registroBoletoSantander.php');

$associado = $_SESSION[''];

if($dadosInput['tipo']== 'dados'){
	
	$queryPrincipal  = " select PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.CODIGO_PLANO, PS1000.TIPO_ASSOCIADO
						 from PS1000
						 where PS1000.CODIGO_TITULAR = " . aspas($_SESSION['codigoIdentificacao']);									

	$resultQuery    = jn_query($queryPrincipal);		
	$retornoJson    = '[';
	
	while ($rowPrincipal = jn_fetch_object($resultQuery)) 
	{
		if (($rowPrincipal->TIPO_ASSOCIADO == 'T'))
		{
			if ($retornoJson != '[')
				$retornoJson .= ',';
					
			$retornoJson .= '{"CODIGO_ASSOCIADO":"' . $rowPrincipal->CODIGO_ASSOCIADO . '","NOME_ASSOCIADO":"' . jn_utf8_encode($rowPrincipal->NOME_ASSOCIADO) . '", "EVENTOS":[';
				
			$queryAux  = " select * FROM PS1024 where (ltrim(rtrim(coalesce(codigos_planos,'')))='' or (','+REPLACE(coalesce(codigos_planos,''),' ','')+',' ) like '%,".$rowPrincipal->CODIGO_PLANO.",%') and FLAG_AUTO_CONTRATACAO = 'S' and SUBSTRING(Tipo_Insercao_Automatica,1,2) IN('01','05','07')";									
			$resultAux = jn_query($queryAux);		
			
			$i = 0;
			while ($rowAux = jn_fetch_object($resultAux)) 
			{
				if($i > 0)
					$retornoJson .= ',';
				

				$queryAux2     = " select * FROM Ps1003  where data_fim_cobranca > getdate() and CODIGO_ASSOCIADO = " . aspas($rowPrincipal->CODIGO_ASSOCIADO) . " and CODIGO_EVENTO = " . $rowAux->CODIGO_EVENTO;									
				$resultAux2    = jn_query($queryAux2);		
				$valorCheckBox = "N";
				
				if ($rowAux2 = jn_fetch_object($resultAux2)) 
					$valorCheckBox = "S";
				
				$retornoJson .= '{"CODIGO_EVENTO":"' . $rowAux->CODIGO_EVENTO . '","NOME_EVENTO":"' . strtoupper(jn_utf8_encode($rowAux->NOME_EVENTO)) . ' - ' . toMoeda($rowAux->VALOR_SUGERIDO) . '","VALOR_EVENTO":"' . $rowAux->VALOR_SUGERIDO . '","FLAG_MARCADO":"' . $valorCheckBox . '"} ';
				
				$i++;
			}
			
			$retornoJson .= ' 	] ';
			$retornoJson .= ' } ';

		}
		else if (($rowPrincipal->TIPO_ASSOCIADO == 'D'))
		{
		
			
			$retornoJson .= ', {"CODIGO_ASSOCIADO":"' . $rowPrincipal->CODIGO_ASSOCIADO . '","NOME_ASSOCIADO":"' . jn_utf8_encode($rowPrincipal->NOME_ASSOCIADO) . '", "EVENTOS":[';	
			
			$queryAux  = " select * FROM PS1024 where  (ltrim(rtrim(coalesce(codigos_planos,'')))='' or (','+REPLACE(coalesce(codigos_planos,''),' ','')+',' ) like '%,".$rowPrincipal->CODIGO_PLANO.",%') and  FLAG_AUTO_CONTRATACAO = 'S' and  SUBSTRING(Tipo_Insercao_Automatica,1,2) IN('02','06','07')";									
			$resultAux = jn_query($queryAux);		
			
			$i = 0;
			while ($rowAux = jn_fetch_object($resultAux)) 
			{
				if($i > 0)
					$retornoJson .= ',';
				
				$queryAux2     = " select * FROM PS1003  where data_fim_cobranca > getdate() and CODIGO_ASSOCIADO = " . aspas($rowPrincipal->CODIGO_ASSOCIADO) . " and CODIGO_EVENTO = " . $rowAux->CODIGO_EVENTO;									
				$resultAux2    = jn_query($queryAux2);		
				$valorCheckBox = "N";
				
				if ($rowAux2 = jn_fetch_object($resultAux2)) 
					$valorCheckBox = "S";
				
				$retornoJson .= '{"CODIGO_EVENTO":"' . $rowAux->CODIGO_EVENTO . '","NOME_EVENTO":"' . strtoupper(jn_utf8_encode($rowAux->NOME_EVENTO)) . ' - ' . toMoeda($rowAux->VALOR_SUGERIDO) . '","VALOR_EVENTO":"' . $rowAux->VALOR_SUGERIDO . '","FLAG_MARCADO":"' . $valorCheckBox . '"} ';
				$i++;
			}
			
			$retornoJson .= ' 	] ';
			$retornoJson .= ' } ';
		}
	}
	
	$retornoJson .=  ']';
	
	echo $retornoJson;
}

if($dadosInput['tipo']== 'salvar'){
	
	$retorno = array();
	$retorno['STATUS'] = 'OK';
	$retorno['MSG']    = 'Cobertura Adicionais Salvas.';
	$retorno['DESTINO']= 'site/paginaInicial';
	$retorno['BOLETO'] = '';
	
	$resTit = jn_query('SELECT CODIGO_TITULAR,CODIGO_EMPRESA ,CODIGO_PLANO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($dadosInput['dadosSalvar'][0]['CODIGO_ASSOCIADO']));
	$rowTit = jn_fetch_object($resTit);
	
	$i = 0;
	$dados['pessoas'] = array();
	
	$valorTotal = 0;
	$valorTotalEvento = array();
	$qteTotalEvento = array();
	foreach($dadosInput['dadosSalvar'] as $dadosEvento){
		
		if($dadosEvento['FLAG_MARCADO'] == 'S'){
			$select = 'Select * from PS1024 where CODIGO_EVENTO= '.aspas($dadosEvento['CODIGO_EVENTO']);
			$resSelec = jn_query($select);
			if($rowSelec = jn_fetch_object($resSelec)){
				$valorTotalEvento[$dadosEvento['CODIGO_EVENTO']] = $valorTotalEvento[$dadosEvento['CODIGO_EVENTO']]?$valorTotalEvento[$dadosEvento['CODIGO_EVENTO']]:0;
				$qteTotalEvento[$dadosEvento['CODIGO_EVENTO']] = $qteTotalEvento[$dadosEvento['CODIGO_EVENTO']]?$qteTotalEvento[$dadosEvento['CODIGO_EVENTO']]:0;
				$valorTotal = $valorTotal + $rowSelec->VALOR_SUGERIDO;
				$valorTotalEvento[$dadosEvento['CODIGO_EVENTO']] = $valorTotalEvento[$dadosEvento['CODIGO_EVENTO']] + $rowSelec->VALOR_SUGERIDO;
				$qteTotalEvento[$dadosEvento['CODIGO_EVENTO']] = $qteTotalEvento[$dadosEvento['CODIGO_EVENTO']] + 1;
			}
		}
	}

	if($valorTotal>0){

		if($rowTit->CODIGO_EMPRESA == '400'){
			$selectDia = 'select DIA_VENCIMENTO from ps1002 Where codigo_associado ='. aspas($rowTit->CODIGO_TITULAR);
		}else{
			$selectDia = 'select DIA_VENCIMENTO from ps1002 Where codigo_Empresa ='. aspas($rowTit->CODIGO_EMPRESA);
		}
		$resDia  = jn_query($selectDia);
		$rowDia = jn_fetch_object($resDia);

		$diaVencimento = (int)$rowDia->DIA_VENCIMENTO;
		if($diaVencimento > date( 'd' )){

			$data_vencimento_boleto = date( 'd/m/Y', mktime( 0, 0, 0, date( 'm' ),$diaVencimento, date( 'Y' ) ) );
			$mesAno = date( 'm/Y', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) );
		}else{
			$data_vencimento_boleto = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), $diaVencimento, date( 'Y' ) ) );
			$mesAno = date('m/Y',  strtotime($data_vencimento_boleto . '+1 months'));
			$data_vencimento_boleto = date('d/m/Y', strtotime($data_vencimento_boleto . '+1 months'));
			
		}
		
		$data_gerar_boleto = date('d/m/Y');


		$numeroRegistro = jn_gerasequencial('PS1020');
		$obs = 'EVENTO AUTO';
		$query  = 'INSERT INTO PS1020 (NUMERO_REGISTRO,CODIGO_EMPRESA, CODIGO_ASSOCIADO, DATA_VENCIMENTO, VALOR_FATURA,VALOR_ADICIONAL, DATA_EMISSAO, TIPO_REGISTRO, INFORMACOES_GERACAO,MES_ANO_REFERENCIA) ';
		$query .= " VALUES (".aspas($numeroRegistro).", ". aspas($rowTit->CODIGO_EMPRESA) . "," . aspasNull($rowTit->CODIGO_TITULAR) . ", " . dataToSql($data_vencimento_boleto) . ", " . ($valorTotal) . ", ".aspas($valorTotal)."," . dataToSql($data_gerar_boleto) . ", 'Q', " . aspas($obs) . ",".aspas($mesAno )." );";
		if(jn_query($query)){
			 $retorno['FATURA'] = $numeroRegistro;
			 $observaoes= 'GERAÇÃO VIA WEB';
			 foreach ($valorTotalEvento as $key => $value) {
					$query = 'INSERT INTO PS1029(NUMERO_REGISTRO_PS1020,CODIGO_EVENTO,QUANTIDADE,VALOR_TOTAL,OBSERVACOES_ADICIONAIS,VALOR_EVENTO)
																							VALUES('.aspas($numeroRegistro).','.aspas($key).','.aspas($qteTotalEvento[$key]).',0,'.aspas($observaoes).','.aspas($value).')';
					jn_query($query);
				}
				foreach($dadosInput['dadosSalvar'] as $dadosEvento){
					if($dadosEvento['FLAG_MARCADO'] == 'S'){
						$select = 'Select * from PS1024 where CODIGO_EVENTO= '.aspas($dadosEvento['CODIGO_EVENTO']);
						$resSelec = jn_query($select);
						$rowSelec = jn_fetch_object($resSelec);
						$query = 'INSERT INTO PS1021(CODIGO_EMPRESA,CODIGO_ASSOCIADO,MES_ANO_VENCIMENTO,VALOR_CONVENIO,NUMERO_REGISTRO_PS1020,VALOR_ADICIONAL,CODIGO_PLANO, VALOR_PRORRATA, VALOR_CORRECAO,VALOR_FATURA,DATA_EMISSAO,CODIGO_TITULAR)
																							VALUES('.aspas($rowTit->CODIGO_EMPRESA).','.aspas($dadosEvento['CODIGO_ASSOCIADO']).','.aspas($mesAno).',0,'.aspas($numeroRegistro).','.aspas($rowSelec->VALOR_SUGERIDO).','.aspas($rowTit->CODIGO_PLANO).',0,0,'.aspas($rowSelec->VALOR_SUGERIDO).',GETDATE(),'.aspas($rowTit->CODIGO_TITULAR).')';
						jn_query($query);
					}
				}

				$conta = '13000523';
				$retornoFatura = RegistraFatura($numeroRegistro,$rowTit->CODIGO_TITULAR,$conta);
				if($retornoFatura['STATUS']=='OK'){
					$sqlFatura = 'Update  ps1020 set 
									CODIGO_IDENTIFICACAO_FAT = '.aspas($retornoFatura['DADOS']['titulo']['nossoNumero']).',
									CODIGO_BANCO = '.aspas('033').',
									CODIGO_CARTEIRA = '.aspas('101').',
									CODIGO_ULTIMA_EMISSAO = '.aspas('XML').',
									NUMERO_CONTA_COBRANCA = '.aspas($conta).', 
									NUMERO_LINHA_DIGITAVEL= '.aspas($retornoFatura['DADOS']['titulo']['linDig']).'
								  where numero_registro='.aspas($numeroRegistro);
					$resFatura  = jn_query($sqlFatura);
					$retorno['BOLETO'] = 'https://app.plenasaude.com.br/AliancaAppNet2Homol/ServidorAl2/boletos/boleto_santander_PlenaPDF.php?numeroRegistro='.$numeroRegistro.'&cod='.$rowTit->CODIGO_TITULAR;
				}else{
						$cancelaFatura = 'UPDATE  ps1020 set DATA_CANCELAMENTO ='.dataToSql(date("d/m/Y")).' 
										  where NUMERO_REGISTRO = '.aspas($numeroRegistro);
						$resCancelaFatura  = jn_query($cancelaFatura);
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG']    = 'Erro ao registrar o boleto('.$retornoFatura['DADOS']['descricaoErro'].')';		
				}

		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG']    = 'Erro ao gerar o boleto';		
		}


	}
	
	if($retorno['STATUS'] =='ERRO'){
		//pr($retornoFatura);
		echo json_encode($retorno);		
		exit;
	}
	
	foreach($dadosInput['dadosSalvar'] as $dadosEvento){
		
		if($dadosEvento['FLAG_MARCADO'] == 'S'){
			$i= $i + 1;

			$sqlEmail = 'SELECT EMAIL_CONFIRMADO,NOME_ASSOCIADO,NUMERO_CPF,NUMERO_RG FROM PS1000
								 	 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($dadosEvento['CODIGO_ASSOCIADO']);

			$resEmail  = jn_query($sqlEmail);

			$rowEmail = jn_fetch_object($resEmail);

			$pessoa['codigo'] = $dadosEvento['CODIGO_ASSOCIADO'];
			$pessoa['email'] = jn_utf8_encode($rowEmail->EMAIL_CONFIRMADO);
			$pessoa['cpf'] = $rowEmail->NUMERO_CPF;
			$pessoa['rg'] = $rowEmail->NUMERO_RG;
			$pessoa['nome'] = jn_utf8_encode($rowEmail->NOME_ASSOCIADO); 

			$dados['pessoas'][] = $pessoa;

			$select = 'Select * from PS1003 where codigo_associado ='.aspas($dadosEvento['CODIGO_ASSOCIADO']).' and CODIGO_EVENTO= '.aspas($dadosEvento['CODIGO_EVENTO']).' and data_fim_cobranca > getdate()';
			$resSelec = jn_query($select);
			if($rowSelec = jn_fetch_object($resSelec)){
			}else{
				$query 	= 	'Insert Into PS1003(CODIGO_EVENTO, CODIGO_EMPRESA, CODIGO_ASSOCIADO, QUANTIDADE_EVENTOS, TIPO_CALCULO, VALOR_FATOR, FLAG_COBRA_DEPENDENTE, DATA_INICIO_COBRANCA, DATA_FIM_COBRANCA) ' .
							'Select ' . $dadosEvento['CODIGO_EVENTO'] . ', '.aspas($rowTit->CODIGO_EMPRESA).', "' . $dadosEvento['CODIGO_ASSOCIADO'] . '", 1, coalesce(TIPO_CALCULO,"V"), VALOR_SUGERIDO, "N", ' .  dataToSql(date("d/m/Y")) . ', '.dataToSql("31/12/2099").' FROM PS1024 WHERE CODIGO_EVENTO = ' . $dadosEvento['CODIGO_EVENTO'];
									
				if (!jn_query($query)) {
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = 'Erro ao salvar o evento';				
				}
			}
		}else{
			$query 	= 'UPDATE PS1003 set DATA_FIM_COBRANCA = getdate() where CODIGO_ASSOCIADO = '.aspas($dadosEvento['CODIGO_ASSOCIADO']).' and CODIGO_EVENTO = '.aspas($dadosEvento['CODIGO_EVENTO']). ' and data_fim_cobranca > getdate() ';
			jn_query($query);	
		}
	}

	if(($i>0)and $retorno['STATUS']=='OK'){
		
		if($rowTit->CODIGO_EMPRESA == '400' )
			$queryEnd = 'select * from ps1001 where Ps1001.codigo_associado = '.aspas($rowTit->CODIGO_TITULAR);
		else
			$queryEnd = 'select * from ps1015 where Ps1015.codigo_associado = '.aspas($rowTit->CODIGO_TITULAR);
		
		$resEnd  = jn_query($queryEnd);
		if($rowEnd = jn_fetch_object($resEnd)){	
			$endereco = $rowEnd->ENDERECO.' ';
			$numero = '';
			$complemento = '';
			$auxEndereco = explode(',',$endereco);
			$endereco = $auxEndereco[0];
			if(count($auxEndereco)>1){
				$auxEndereco = explode('-',$auxEndereco[1]); 
				$numero = $auxEndereco[0];
				if(count($auxEndereco)>1){
					$complemento = $auxEndereco[1];
				}
			}
		}
		$dados['cep'] = $rowEnd->CEP;
		$dados['endereco'] = jn_utf8_encode($endereco);
		$dados['numero'] = jn_utf8_encode($numero);
		$dados['complemento'] = jn_utf8_encode($complemento);
		$dados['bairro'] = jn_utf8_encode($rowEnd->BAIRRO);
		$dados['cidade'] = jn_utf8_encode($rowEnd->CIDADE);
		$dados['estado'] = jn_utf8_encode($rowEnd->ESTADO);		
		
		if($rowTit->CODIGO_EMPRESA == '400' ){
			$tabela = 'PS1001';
		}else{
			$tabela = 'PS1015';
		}
		
		$sqlEmail = 'SELECT COALESCE(EMAIL_CONFIRMADO,'.$tabela.'.ENDERECO_EMAIL) EMAIL_CONFIRMADO,NOME_ASSOCIADO,NUMERO_CPF,NUMERO_RG FROM PS1000
					 LEFT JOIN '.$tabela.' ON '.$tabela.'.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
					 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($rowTit->CODIGO_TITULAR);
	
		$resEmail  = jn_query($sqlEmail);
		
		$rowEmail = jn_fetch_object($resEmail);
		
		$dados['email'] = jn_utf8_encode($rowEmail->EMAIL_CONFIRMADO);
		$dados['cpf'] = $rowEmail->NUMERO_CPF;			
		$dados['rg'] = $rowEmail->NUMERO_RG;
		$dados['nome'] = jn_utf8_encode($rowEmail->NOME_ASSOCIADO);	
		$dados['codigo'] = $rowTit->CODIGO_TITULAR;				
		
		setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo');
		$tipoModelo = substr($_SERVER['HTTP_USER_AGENT'],0,100); 
		
		$dados['assinatura'] = jn_utf8_encode("Assinado eletronicamente mediante login/senha por ".$rowDados->NOME. ", "."em ".strftime('%A, %d de %B de %Y as %H:%M:%S', strtotime('now'))."\n"."através  do ".$tipoModelo."\n"."IP:".$_SERVER["REMOTE_ADDR"]);
	
		if($rowTit->CODIGO_EMPRESA == '400'){
			$selectDia = 'select DIA_VENCIMENTO from ps1002 Where codigo_associado ='. aspas($rowTit->CODIGO_TITULAR);
		}else{
			$selectDia = 'select DIA_VENCIMENTO from ps1002 Where codigo_Empresa ='. aspas($rowTit->CODIGO_EMPRESA);
		}
		$resDia  = jn_query($selectDia);
		$rowDia = jn_fetch_object($resDia);

		$dados['diaVencimento'] = $rowDia->DIA_VENCIMENTO;
		$dados['data'] = date('d/m/Y');
		$dados['hora'] = date('H:i');
		$dados['mes'] = strftime('%B', strtotime('now'));
		$dados['diaVencimentoExtenso'] = valor_por_extenso($dados['diaVencimento']);

		$numeroSequencial = jn_gerasequencial('PS6110');

		$queryEmp = 'SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA';
		$resEmp = jn_query($queryEmp);
		$rowEmp = jn_fetch_object($resEmp);

		$data = date('d/m/Y');
		$hora = date('H:i');
		$hash = md5($data.$hora.$protocoloAtendimento);

		$protocoloAtendimento = $rowEmp->NUMERO_INSC_SUSEP . date('Y') . date('m') . date('d') . $numeroSequencial;
		$descricao = 'Contratação evento: ' . "\n" . 'Codigo do associado:' . $rowTit->CODIGO_TITULAR . "\n";	
		$query =" INSERT INTO ESP_ASSINATURA_DOCUMENTO
									(CAMPOS
									,HASH)
							VALUES
									('".(json_encode($dados))."'
									,".aspas($hash).")";

							//	print($query);
		$query = str_replace('"','_|_',$query);
		jn_query($query);
		$numeroTel = '';

		$query  = "INSERT INTO PS6110(NOME_PESSOA, PROTOCOLO_ATENDIMENTO, ";
		$query .= "CODIGO_RECLAMACAO_SUGESTAO, DATA_RECLAMACAO_SUGESTAO, DATA_EVENTO, DESCRICAO_RECLAMACAO_SUGESTAO, ";
		$query .= " FONE_CONTATO, FONE_CONTATO_02, DEPARTAMENTO_RESPONSAVEL, EMAIL_CONTATO) "; 
		$query .= "Values ( ";		
		$query .= aspas($rowEmail->NOME_ASSOCIADO) . ", ";
		$query .= aspas($protocoloAtendimento) . ", ";
		$query .= aspas('45') . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= aspas($descricao) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas('CAD') . ", ";
		$query .= aspas($dados['email']);
		$query .= ')'; 
		
		$link = 'https://app.plenasaude.com.br/AliancaAppNet2Homol/ServidorAl2/ProcessoDinamico/relatorioEventosAuto.php?cod='.$hash.'&pdf=OK';
			
		$retorno['pdf'] = $link;

		$codigoAssociado = $rowTit->CODIGO_TITULAR;
		
		$ps1007 ="\nDia : " . date('d/m/Y')."
		Contratacao Evento Auto
		Protocolo : ".$protocoloAtendimento . "
		\n".$link."
		\n".$dados['assinatura'] ."
		\n\n
		****   
		\n\n
		";

		$resDados  = jn_query("Select * from PS1007 where CODIGO_ASSOCIADO = ".aspas($codigoAssociado));
		
		if($rowDados = jn_fetch_object($resDados)){
				$query = "UPDATE Ps1007 set Observacao_Cobranca = concat(".aspas($ps1007).",Observacao_Cobranca) WHERE CODIGO_ASSOCIADO=".aspas($codigoAssociado);
				
		}else{
				$query = "INSERT INTO PS1007(CODIGO_ASSOCIADO, OBSERVACAO_COBRANCA) VALUES(".aspas($codigoAssociado).",".aspas($ps1007).")";
				
		}

		jn_query($query);

		
		
	}

	
	echo json_encode($retorno);
}

function valor_por_extenso( $v ){
		
	$v = filter_var($v, FILTER_SANITIZE_NUMBER_INT);
   
        //$sin = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        //$plu = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");

        $sin = array("", "", "", "", "", "", "");
        $plu = array("", "", "", "", "", "","");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

        $z = 0;
 
        $v = number_format( $v, 2, ".", "." );
        $int = explode( ".", $v );
 
        for ( $i = 0; $i < count( $int ); $i++ ) 
        {
            for ( $ii = mb_strlen( $int[$i] ); $ii < 3; $ii++ ) 
            {
                $int[$i] = "0" . $int[$i];
            }
        }

        $rt = null;
        $fim = count( $int ) - ($int[count( $int ) - 1] > 0 ? 1 : 2);
        for ( $i = 0; $i < count( $int ); $i++ )
        {
            $v = $int[$i];
            $rc = (($v > 100) && ($v < 200)) ? "cento" : $c[$v[0]];
            $rd = ($v[1] < 2) ? "" : $d[$v[1]];
            $ru = ($v > 0) ? (($v[1] == 1) ? $d10[$v[2]] : $u[$v[2]]) : "";
 
            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count( $int ) - 1 - $i;
            $r .= $r ? " " . ($v > 1 ? $plu[$t] : $sin[$t]) : "";
            if ( $v == "000")
                $z++;
            elseif ( $z > 0 )
                $z--;
                
            if ( ($t == 1) && ($z > 0) && ($int[0] > 0) )
                $r .= ( ($z > 1) ? " de " : "") . $plu[$t];
                
            if ( $r )
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($int[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }
 
        $rt = mb_substr( $rt, 1 );
 
        return($rt ? trim( $rt ) : "zero");
 
}

/*
	if($codigoEmpresa==400){
		$tipo= 'F';
		$descricao = 'Cancela Contrato: Solicitada via portal ' . "\n" . 'Codigo do associado:' . $codigoAssociado . "\n";		
		$selectDados = 'Select NOME_ASSOCIADO NOME, NUMERO_CPF DOCUMENTO, ENDERECO,BAIRRO, CIDADE,CEP,ENDERECO_EMAIL,DATA_ADMISSAO
						from PS1000
						LEFT JOIN PS1001 on PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO WHERE ps1000.CODIGO_ASSOCIADO='.aspas($codigoAssociado);
	}else{
		$tipo= 'J';
		$descricao = 'Cancela Contrato: Solicitada via portal' . "\n" . 'Codigo do empresa:' . $codigoEmpresa . "\n";	
		$selectDados = 'Select NOME_EMPRESA NOME, NUMERO_CNPJ DOCUMENTO, ENDERECO,BAIRRO, CIDADE,CEP,ENDERECO_EMAIL,DATA_ADMISSAO
						from PS1010
						LEFT JOIN PS1001 on PS1010.CODIGO_EMPRESA = PS1001.CODIGO_EMPRESA WHERE PS1010.CODIGO_EMPRESA='.aspas($codigoEmpresa);
	}
	$resDados  = jn_query($selectDados);
	$rowDados = jn_fetch_object($resDados);
	
	$numeroSequencial = jn_gerasequencial('PS6110');

	$queryEmp = 'SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA';
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);

	$protocoloAtendimento = $rowEmp->NUMERO_INSC_SUSEP . date('Y') . date('m') . date('d') . $numeroSequencial;

	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$tipoModelo = substr($_SERVER['HTTP_USER_AGENT'],0,100); 
	
	$assinatura = "Assinado eletronicamente mediante login/senha por ".$rowDados->NOME. ", "."em ".strftime('%A, %d de %B de %Y as %H:%M:%S', strtotime('now'))."\n"."através  do ".$tipoModelo."\n"."IP:".$_SERVER["REMOTE_ADDR"];

	$data = date('d/m/Y');
	$hora = date('H:i');
	$hash = md5($data.$hora.$protocoloAtendimento);

	$dados['TIPO']       = $tipo;
	$dados['ASSINATURA'] = utf8_encode($assinatura);
	$dados['PROTOCOLO']  = $protocoloAtendimento;

	$query =" INSERT INTO ESP_ASSINATURA_DOCUMENTO
					   (CAMPOS
					   ,HASH)
				 VALUES
					   ('".(json_encode($dados))."'
					   ,".aspas($hash).")";

		//	print($query);
		$query = str_replace('"','_|_',$query);
		jn_query($query);

	
	
		$emailAssociado = $rowDados->ENDERECO_EMAIL;
		
		
		$numeroTel = 'NAO INFORMADO';

		
        $query  = "INSERT INTO PS6110(NOME_PESSOA, PROTOCOLO_ATENDIMENTO, ";
		$query .= "CODIGO_RECLAMACAO_SUGESTAO, DATA_RECLAMACAO_SUGESTAO, DATA_EVENTO, DESCRICAO_RECLAMACAO_SUGESTAO, ";
		$query .= " FONE_CONTATO, FONE_CONTATO_02, DEPARTAMENTO_RESPONSAVEL, EMAIL_CONTATO) "; 
		$query .= "Values ( ";		
		$query .= aspas($rowDados->NOME) . ", ";
		$query .= aspas($protocoloAtendimento) . ", ";
		$query .= aspas('45') . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= aspas($descricao) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas('CAD') . ", ";
		$query .= aspas($_GET['enderecoEmail']);
		$query .= ')'; 
		
		$link = 'https://app.plenasaude.com.br/AliancaAppNet2Homol/ServidorAl2/ProcessoDinamico/relatorioCancelamentoMultaPlena.php?cod='.$hash;
				
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
			$sqlEndereco = "select coalesce(PS1001.ENDERECO,PS1015.ENDERECO)  ENDERECO,
																						coalesce(PS1001.BAIRRO,PS1015.BAIRRO)  BAIRRO,
																						coalesce(PS1001.CIDADE,PS1015.CIDADE)  CIDADE,
																						coalesce(PS1001.CEP,PS1015.CEP)  CEP,
																						coalesce(PS1001.ESTADO,PS1015.ESTADO)  ESTADO,
																						PS1000.NOME_ASSOCIADO NOME,PS1000.NUMERO_CPF DOCUMENTO from PS1000 
			left join PS1001		 on PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO
			left join PS1015 on PS1000.CODIGO_ASSOCIADO = PS1015.CODIGO_ASSOCIADO
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


?>