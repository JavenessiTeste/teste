<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../lib/vindi.php');
require('../lib/registroBoletoSantander.php');

global $ambienteProducao;
$ambienteProducao = false;


$queryPlanoAssoc  = ' SELECT ID_PLATAFORMA_VENDA FROM PS1000 ';
$queryPlanoAssoc .= ' INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
$queryPlanoAssoc .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
$resPlanoAssoc = jn_query($queryPlanoAssoc);
$rowPlanoAssoc = jn_fetch_object($resPlanoAssoc);

$idProdutoFaturaAvulsa = $rowPlanoAssoc->ID_PLATAFORMA_VENDA;
$idProdutoMensalidade  = $rowPlanoAssoc->ID_PLATAFORMA_VENDA;
$idPlano = $rowPlanoAssoc->ID_PLATAFORMA_VENDA;
$valorFatura = '';

if($dadosInput['tipo'] =='enviaDadosCartaoVendas'){
	global $idVindi;
	
	if($dadosInput['tipoPagamento'] == 'vendas'){
		$queryAssociado  = ' SELECT * FROM VND1000_ON ';
		$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';		
		$queryAssociado .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	}else{
		$queryAssociado  = ' SELECT * FROM PS1000 ';
		$queryAssociado .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' INNER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO) ';
		$queryAssociado .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	}
	
	$resAssociado = jn_query($queryAssociado);
	$rowAssociado = jn_fetch_object($resAssociado);
	
	$nome = $rowAssociado->NOME_ASSOCIADO;
	$email = $rowAssociado->ENDERECO_EMAIL;
	$codigoCliente = $rowAssociado->CODIGO_SEQUENCIAL;
	$documento = $rowAssociado->NUMERO_CPF;
	$observacoes = '';
	$rua = '';
	$numero = '';
	$complemento = '';
	$cep = $rowAssociado->CEP;
	$bairro = $rowAssociado->BAIRRO;
	$cidade = $rowAssociado->CIDADE;
	$estado = $rowAssociado->ESTADO;	
	$telefone = '0' . $rowAssociado->CODIGO_AREA . $rowAssociado->NUMERO_TELEFONE;
	$celular = '0' . $rowAssociado->CODIGO_AREA . $rowAssociado->NUMERO_TELEFONE;
	$idVindi = $rowAssociado->ID_VINDI;
	
	if($email == '' && retornaValorConfiguracao('EMAIL_PADRAO_VINDI') != ''){
		$email = retornaValorConfiguracao('EMAIL_PADRAO_VINDI');
	}
	
	if(!$idVindi){
		
		$cadUsuario = CadastraUsuarioVindi($nome,$email,$codigoCliente,$documento,$observacoes,$rua,$numero,$complemento,$cep,$bairro,$cidade,$estado,$telefone,$celular='');
		
		if($cadUsuario['STATUS'] == 'OK'){
			$idVindi = $cadUsuario['ID'];
			jn_query('UPDATE PS1000 SET ID_VINDI = ' . aspas($idVindi) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']));
		}else{						
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG'] = 'ERRO AO CADASTRAR USUARIO';
			echo json_encode($retorno);
			return false;
		}
	}
	
	$numeroCartao = $dadosInput['dadosCartao']['card_number'];
	$mesAnoVenc = $dadosInput['dadosCartao']['expiry_date'];
	$codigoSeg = $dadosInput['dadosCartao']['cvv'];	
		
	$salvaCartao = SalvaCartaoPerfil($idVindi,$nome,$documento,$mesAnoVenc,$numeroCartao,$codigoSeg,$bandeira);			
		
	if($salvaCartao['STATUS'] == 'OK'){
		$idCartao = $salvaCartao['ID'];
		jn_query('UPDATE PS1000 SET ID_CARTAO_VINDI = ' . aspas($idCartao) . ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']));
	}else{					
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'ERRO AO CADASTRAR CARTAO';
		echo json_encode($retorno);
		return false;
	}
	
	if($dadosInput['tipoPagamento'] == 'vendas'){
		$queryVnd1020  = ' SELECT NUMERO_REGISTRO, VALOR_FATURA ';	
		$queryVnd1020 .= ' FROM VND1020_ON ';	
		$queryVnd1020 .= ' WHERE NUMERO_PARCELA="1" ';	
		$queryVnd1020 .= ' AND CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);		
		$resVnd1020 = jn_query($queryVnd1020);
		$row1020 = jn_fetch_object($resVnd1020);
	}else{
		$queryPS1020  = ' SELECT NUMERO_REGISTRO, VALOR_FATURA ';	
		$queryPS1020 .= ' FROM PS1020 ';	
		$queryPS1020 .= ' WHERE NUMERO_PARCELA="1" ';	
		$queryPS1020 .= ' AND CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);		
		$resPS1020 = jn_query($queryPS1020);
		$row1020 = jn_fetch_object($resPS1020);
	}
	$numeroRegistro = $row1020->NUMERO_REGISTRO;
	$valorFatura = $row1020->VALOR_FATURA;
	
	$quantParcelas = str_replace('x','',$dadosInput['dadosCartao']['quantidadeParcelas']);
	
	$geraFaturaAvulsa = GeraFaturaAvulsa($idVindi,$idCartao,$idProdutoFaturaAvulsa,$valorFatura,$numeroRegistro . '_TEMP',$quantParcelas);
	
	$retorno['MSG'] = 'FATURA GERADA';
	echo json_encode($retorno);

}elseif($dadosInput['tipo'] =='registraBoleto'){

	$queryAssociado  = ' SELECT * FROM PS1000 ';
	$queryAssociado .= ' INNER JOIN PS1020 ON (PS1000.CODIGO_ASSOCIADO = PS1020.CODIGO_ASSOCIADO) ';		
	$queryAssociado .= ' WHERE PS1020.NUMERO_PARCELA ="1" AND CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	
	$resAssociado = jn_query($queryAssociado);
	$rowAssociado = jn_fetch_object($resAssociado);

	$codigoAssociado = $rowAssociado->CODIGO_ASSOCIADO;
	$numeroRegistro = $rowAssociado->NUMERO_REGISTRO;
		
	$queryBanco  = ' SELECT NUMERO_CONTA_CORRENTE FROM PS7300 ';
	$queryBanco .= ' WHERE CODIGO_BANCO = "033" AND CAMINHO_CERTIFICADO IS NOT NULL ';
	$resBanco = jn_query($queryBanco);
	$rowBanco = jn_fetch_object($resBanco);
	
	$numeroConta = $rowBanco->NUMERO_CONTA_CORRENTE;	
	
	$caminhoPersistencia = retornaValorConfiguracao('LINK_PERSISTENCIA');
	$retorno['URL_BOLETO'] = $caminhoPersistencia . '/geraBoletoSantander.php?imprimir=SIM&registro='.$numeroRegistro.'&codigo='.$codigoAssociado.'&numeroConta='.$numeroConta;
	
	echo json_encode($retorno);	

}
elseif($dadosInput['tipo'] =='pesquisarDebitos')
{
	
	if($dadosInput['tipoCliente'] == 'vendas')
	{
		
		$queryFat = 'SELECT NUMERO_REGISTRO FROM PS1020 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$resFat = jn_query($queryFat);
		if(!$rowFat = jn_fetch_object($resFat)){
			//$numeroRegistro = jn_gerasequencial('PS1020');
	
			$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
							A.NOME_TABELA = 'PS1020' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
							select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
							B.NOME_TABELA = 'VND1020_ON' and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO' and B.NOME_CAMPO <>'DESCRICAO_OBSERVACAO' 
							)";
			$resCampos  = jn_query($querycampos);
			
			$campos = '';
			while($rowCampos  = jn_fetch_object($resCampos)){
				if($campos=='')
					$campos = $rowCampos->NOME_CAMPO;
				else
					$campos .= ','.$rowCampos->NOME_CAMPO;
			}
			
			$queryInsertPS1020 = "INSERT INTO PS1020(CODIGO_ASSOCIADO,DESCRICAO_OBSERVACAO,".$campos.")
								 SELECT 	".aspasNull($_SESSION['codigoIdentificacao']).",".aspasNull('FATURA VENDAS: '.$fatura).",".$campos." 
								 FROM VND1020_ON 
								 WHERE CODIGO_ASSOCIADO IN (SELECT VND1000_ON.CODIGO_ASSOCIADO FROM VND1000_ON WHERE VND1000_ON.CODIGO_ASSOCIADO_PS1000 = '" . $_SESSION['codigoIdentificacao'] . "') ";

			if(!jn_query($queryInsertPS1020)){
				pr($queryInsertPS1020);
			}
		}
		
		$queryDebitos  = ' SELECT DATA_VENCIMENTO, VALOR_FATURA ';	
		$queryDebitos .= ' FROM PS1020 ';	
		$queryDebitos .= ' WHERE NUMERO_PARCELA="1" ';	
		$queryDebitos .= ' AND CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);	
		$resDebitos = jn_query($queryDebitos);
		$rowDebitos = jn_fetch_object($resDebitos);
		
		$retorno['DATA_VENCIMENTO'] = SqlToData($rowDebitos->DATA_VENCIMENTO);
		$retorno['TOTAL_DEBITOS'] = toMoeda($rowDebitos->VALOR_FATURA);
		
		echo json_encode($retorno);		
	}

}
else if($dadosInput['tipo'] =='fechamentoCompraServicos')
{

	$dataVencimento = date('d/m/Y', strtotime('+5 days'));

	$retorno['DATA_VENCIMENTO']        = $dataVencimento;
	$retorno['TOTAL_DEBITOS']          = toMoeda($dadosInput['valorCobrancaGerar']);
	$retorno['NUMERO_REGISTRO_PS1020'] = $numeroRegistro;
			
	echo json_encode($retorno);		

}
else if($dadosInput['tipo'] =='pagamentoCompraServicos')
{

	$numeroRegistro = jn_gerasequencial('PS1020');
		
	$queryInsertPS1020 = "INSERT INTO PS1020(NUMERO_REGISTRO, CODIGO_ASSOCIADO, DESCRICAO_OBSERVACAO, DATA_VENCIMENTO, 
		                                     DATA_EMISSAO, VALOR_FATURA, CODIGO_SEQUENCIAL)
						  VALUES (" . aspasNull($numeroRegistro) . ", " . 
						  	          aspasNull($dadosInput['codigoAssociadoGerar']) . "," . 
						  	          aspasNull('FATURA CONTRATACAO SERVIÇOS') . "," . 
						  	          dataToSql($dataVencimento) . ", " .
						  	          dataToSql(date('d/m/Y', strtotime('+0 days'))) . ", " .
						  	          aspas($dadosInput['valorCobrancaGerar']) . "," . 
						  	          aspas("0") . ")";

	jn_query($queryInsertPS1020);

	//// COLOCAR AQUI OS DADOS RECEBIDOS DO PAGAMENTO DO CARTÃO
	//// PARA PODER CHAMAR O PROCESSO DE PAGAMENTOS E DEPOIS
	//// DAR BAIXA NA PS1020

}
else if($dadosInput['tipo'] =='regrasTiposPagamentos')
{

	$query = 'SELECT CODIGO_PLANO, CODIGO_TABELA_PRECO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	$res = jn_query($query);	
	$row = jn_fetch_object($res);

	$recorrencia = true;
	$cartaoCredito = true;
	$boleto = true;

	if($row->CODIGO_TABELA_PRECO == '1' && $_SESSION['codigoSmart'] == '4277'){
		$cartaoCredito = false;
	}elseif($row->CODIGO_TABELA_PRECO == '2' && $_SESSION['codigoSmart'] == '4277'){
		$boleto = false;
	}

	$retorno['RECORRENCIA']        	= $recorrencia;
	$retorno['CARTAO_CREDITO']     	= $cartaoCredito;
	$retorno['BOLETO'] 				= $boleto;
			
	echo json_encode($retorno);		

}

?>