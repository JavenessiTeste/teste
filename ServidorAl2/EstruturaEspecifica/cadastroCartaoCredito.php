<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] =='cadastrar'){
	$gatewayCartao = retornaValorConfiguracao('GATEWAY_CARTAO_CREDITO');

	$codigoAssociado = '';
	$associadoVnd = $dadosInput['codigo'];			
	$valorAssinatura = retornaValorPrevisaoAssinatura($associadoVnd);

	if($_SESSION['perfilOperador'] == 'BENEFICIARIO')
		$codigoAssociado = $_SESSION['codigoIdentificacao'];
	else	
		$codigoAssociado = $dadosInput['codigo'];	


	if($gatewayCartao == 'ZSPAY'){
		require('../lib/zsPay.php');		

		global $tokenZsPay;

		$tokenZsPay = retornaValorConfiguracao('TOKEN_ZSPAY');
		$retornoId = retornaIdAssociadoZsPay($codigoAssociado, $associadoVnd);		
		$criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
		$retorno = Array();

		if(isset($retornoId['STATUS']) == 'OK'){

			$idClienteZsPay = $retornoId['ID'];
			$numeroCartao = $dadosInput['dadosCartao']['card_number'];
			$nomeCartao = $dadosInput['dadosCartao']['user_card_name'];
			$VencimentoCartao = $dadosInput['dadosCartao']['expiry_date'];
			$VencimentoCartao = substr($VencimentoCartao,0,2) . '/' . substr($VencimentoCartao,2,4);
			$codSegCartao = $dadosInput['dadosCartao']['cvv'];

			$retornoCartao = vincula_cartao_zspay($idClienteZsPay, $numeroCartao, $nomeCartao, $VencimentoCartao, $codSegCartao);
			
			if($retornoCartao['STATUS'] == 'OK'){

				$sqlInativaCartao   = linhaJsonEdicao('DATA_INUTILIZACAO', dataHoje(), 'D'); 
				gravaEdicao('ESP_HIST_CARTOES_ASSOCIADOS', $sqlInativaCartao, 'A', $criterioWhereGravacao . ' AND DATA_INUTILIZACAO IS NULL'); 

				$sqlInclusaoCartao   = linhaJsonEdicao('CODIGO_ASSOCIADO', $codigoAssociado);     
				$sqlInclusaoCartao  .= linhaJsonEdicao('ID_CARTAO_GATEWAY', $retornoCartao['ID']);     
				$sqlInclusaoCartao  .= linhaJsonEdicao('GATEWAY_PAGAMENTO', 'ZSPAY');     
				$sqlInclusaoCartao  .= linhaJsonEdicao('ULTIMOS_DIGITOS', $retornoCartao['DIG_FINAIS']);   
				$sqlInclusaoCartao  .= linhaJsonEdicao('DATA_CADASTRO', dataHoje(), 'D');   
				gravaEdicao('ESP_HIST_CARTOES_ASSOCIADOS', $sqlInclusaoCartao, 'I', ''); 

				$tabela = 'PS1000';
				if($associadoVnd == "1" or $associadoVnd == 1 or $associadoVnd == true)
					$tabela = 'VND1000_ON';

				$sqlEdicaoPs1000   = linhaJsonEdicao('ID_CARTAO_GATEWAY', $retornoCartao['ID']);     
				gravaEdicao($tabela, $sqlEdicaoPs1000, 'A', $criterioWhereGravacao);            	

				if($associadoVnd == "1" or $associadoVnd == 1 or $associadoVnd == true){
					
					$queryPlano  = ' SELECT CODIGO_PLANO_GATEWAY FROM ESP_PLANOS_GATEWAY ';
					$queryPlano .= ' WHERE VALOR_PLANO =' . aspas($valorAssinatura);
					$resultadoPlano = qryUmRegistro($queryPlano);

					if(!isset($resultadoPlano->CODIGO_PLANO_GATEWAY)){
						$retornoPlano = cria_plano_zspay($valorAssinatura, retornaValorConfiguracao('EMAIL_PADRAO'), 'monthly');

						if(isset($retornoPlano['planoId'])){

							$sqlInclusaoPlano  .= linhaJsonEdicao('CODIGO_PLANO_GATEWAY', $retornoPlano['planoId']);     
							$sqlInclusaoPlano  .= linhaJsonEdicao('VALOR_PLANO', $valorAssinatura);     				
							$sqlInclusaoPlano  .= linhaJsonEdicao('DATA_CRIACAO_PLANO', dataHoje(), 'D');   
							gravaEdicao('ESP_PLANOS_GATEWAY', $sqlInclusaoPlano, 'I', ''); 

							$codigoPlanoZs = $retornoPlano['planoId'];
						}
					}else{
						$codigoPlanoZs = $resultadoPlano->CODIGO_PLANO_GATEWAY;
					}

					if(!isset($codigoPlanoZs)){
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG'] = 'Erro ao encontrar Plano';
					}else{
						
						$query  = '	SELECT ID_CLIENTE_ZSPAY, ID_CARTAO_GATEWAY FROM VND1000_ON ';			
						$query .= '	WHERE 	VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);			
						$resultado = qryUmRegistro($query);
						
						$retornoAssinatura = cria_assinatura_zspay($codigoPlanoZs, $resultado->ID_CLIENTE_ZSPAY, $resultado->ID_CARTAO_GATEWAY);
						
						if($retornoAssinatura['STATUS'] == 'OK'){

							$sqlInclusaoAssinatura  .= linhaJsonEdicao('CODIGO_PLANO_GATEWAY', $codigoPlanoZs);
							$sqlInclusaoAssinatura  .= linhaJsonEdicao('CODIGO_ASSOCIADO', $codigoAssociado);
							$sqlInclusaoAssinatura  .= linhaJsonEdicao('ID_ASSINATURA', $retornoAssinatura['assinaturaId']);     				
							$sqlInclusaoAssinatura  .= linhaJsonEdicao('DATA_ASSINATURA', dataHoje(), 'D');				
							
							gravaEdicao('ESP_CONTROLE_ASSINATURAS', $sqlInclusaoAssinatura, 'I', ''); 

							$criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
				
							$sqlEdicaoPs1020   = linhaJsonEdicao('ID_ASSINATURA_ZSPAY', $retornoAssinatura['assinaturaId']);
							gravaEdicao('VND1000_ON', $sqlEdicaoPs1020, 'A', $criterioWhereGravacao);
				
							geraContrato($codigoAssociado);

							$retorno['STATUS'] = 'OK';
							$retorno['MSG'] = 'Assinatura realizada com sucesso!';
						
						}else{
							$retorno['STATUS'] = 'ERRO';
							$retorno['MSG'] = 'Erro ao Realizar Assinatura. -- ' . $retornoAssinatura['ERROS'];
						}
					}
				}else{
					$retorno['STATUS'] = 'OK';
					$retorno['MSG'] = 'Cartão vinculado com sucesso!';								
				}
				
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['MSG'] = 'Erro ao cadastrar cartão. Ref[1.2]';
			}
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['MSG'] = 'Erro ao cadastrar cartão. Ref[1.1]';
		}
	}elseif($gatewayCartao == 'PAGBANK'){

		require('../lib/pagBank.php');

		global $tokenPagBank;
		
		if($valorAssinatura < 1)
			$valorAssinatura = '1';
		
		$retornoId = retornaIdAssociadoPagbank($codigoAssociado);
		
		$criterioWhereGravacao  = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
		$retorno 				= Array();
		
		$dadosEndereco = Array();
		$dadosEndereco = $retornoId['dados'];
		
		if(isset($retornoId['STATUS']) == 'OK'){

			$idClientePagBank = $retornoId['ID'];	
			$codSegCartao = $dadosInput['cvv'];
			
			$dadosAssinante   = criar_assinante($dadosEndereco,$idClientePagBank, $dadosInput['dadosCartao'],$codSegCartao,$dadosInput['nomeCartao']);
			pr($dadosAssinante);
			if($dadosAssinante['STATUS'] == 'OK'){

				$idAssinante   = $dadosAssinante['ID'];
				$idCartao 	   = $dadosAssinante['ID_TOKEN'];
				$nomeAssinante = $dadosAssinante['NAME'];

				$queryCartao = "SELECT ID_CARTAO_GATEWAY FROM PS1000 WHERE CODIGO_ASSOCIADO = ". aspas($codigoAssociado);
				$result = qryUmRegistro($queryCartao);

				if($result->ID_CARTAO_GATEWAY){

					$atualizaCartao = editar_dados_pagamento($dadosInput['dadosCartao'],$codSegCartao,$dadosInput['nomeCartao'],$idClientePagBank);

					if($atualizaCartao['STATUS'] == 'OK'){

						$idAssinante   = $atualizaCartao['ID'];
						$idCartao 	   = $atualizaCartao['ID_TOKEN'];
						$nomeAssinante = $atualizaCartao['NAME'];

					}else{
						$retorno['STATUS'] = 'ERRO';
						$retorno['MSG'] = 'Erro ao editar dados do cartão';	
					}

				}
				
				$sqlInativaCartao   = linhaJsonEdicao('DATA_INUTILIZACAO', dataHoje(), 'D'); 
				gravaEdicao('ESP_HIST_CARTOES_ASSOCIADOS',$sqlInativaCartao, 'A', $criterioWhereGravacao . ' AND DATA_INUTILIZACAO IS NULL'); 
				
				$sqlInclusaoCartao   = linhaJsonEdicao('CODIGO_ASSOCIADO', $codigoAssociado);     
				$sqlInclusaoCartao  .= linhaJsonEdicao('ID_CARTAO_GATEWAY', $idCartao);     
				$sqlInclusaoCartao  .= linhaJsonEdicao('GATEWAY_PAGAMENTO', 'PAGBANK');     
				$sqlInclusaoCartao  .= linhaJsonEdicao('ULTIMOS_DIGITOS', $dadosAssinante['DIG_FINAIS']);   
				$sqlInclusaoCartao  .= linhaJsonEdicao('DATA_CADASTRO', dataHoje(), 'D');   
				gravaEdicao('ESP_HIST_CARTOES_ASSOCIADOS', $sqlInclusaoCartao, 'I', ''); 

				$sqlEdicaoPs1000   = linhaJsonEdicao('ID_CARTAO_GATEWAY', $idCartao);     
				gravaEdicao('PS1000', $sqlEdicaoPs1000, 'A', $criterioWhereGravacao);     
				
				$queryPlano  = ' SELECT VALOR_PLANO,CODIGO_PLANO_GATEWAY FROM ESP_PLANOS_GATEWAY ';
				$queryPlano .= ' WHERE VALOR_PLANO =' . aspas($valorAssinatura);				
				
				$resultadoPlano = qryUmRegistro($queryPlano);
				$valorPlano = str_replace('.', '', $resultadoPlano->VALOR_PLANO);
				$divisao = 100;

				$valorPlanoBanco = $valorPlano / $divisao; 
			
				if(!isset($resultadoPlano->VALOR_PLANO)){
					
					$cadastraPlano = criar_plano($idClientePagBank,$valorPlano,'MONTH',$metodoPagamento = 'CREDIT_CARD',$nomeDoPlano = 'Plano Mensal' );
					
					if(isset($cadastraPlano['id'])){

						$sqlInclusaoPlano  .= linhaJsonEdicao('CODIGO_PLANO_GATEWAY', aspas($cadastraPlano['id']));     
						$sqlInclusaoPlano  .= linhaJsonEdicao('VALOR_PLANO', $valorPlanoBanco);     				
						$sqlInclusaoPlano  .= linhaJsonEdicao('DATA_CRIACAO_PLANO', dataHoje(), 'D');
						gravaEdicao('ESP_PLANOS_GATEWAY', $sqlInclusaoPlano, 'I', ''); 

						$codigoPlanoPagbank = $cadastraPlano['id'];
					}
				}else{
					$codigoPlanoPagbank = $resultadoPlano->CODIGO_PLANO_GATEWAY;
				}
			
				if(!isset($codigoPlanoPagbank)){
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG'] = 'Erro ao encontrar Plano';	
					
				}else{
					$query  	 = '	SELECT ID_CLIENTE_PAGBANK FROM PS1000 ';			
					$query 		.= '	WHERE 	PS1000.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);			
					$resultado 	 = qryUmRegistro($query);
	
					$retornoAssinatura = cria_assinatura($idAssinante,$codigoPlanoPagbank,$valorPlano,$resultado->ID_CLIENTE_PAGBANK,$codSegCartao);
					pr($retornoAssinatura);
					if($retornoAssinatura['STATUS'] == 'OK'){

						$sqlInclusaoAssinatura  .= linhaJsonEdicao('CODIGO_PLANO_GATEWAY', $codigoPlanoPagbank);
						$sqlInclusaoAssinatura  .= linhaJsonEdicao('CODIGO_ASSOCIADO', $codigoAssociado);
						$sqlInclusaoAssinatura  .= linhaJsonEdicao('ID_ASSINATURA', $retornoAssinatura['id']);     				
						$sqlInclusaoAssinatura  .= linhaJsonEdicao('DATA_ASSINATURA', dataHoje(), 'D');				
						
						gravaEdicao('ESP_CONTROLE_ASSINATURAS', $sqlInclusaoAssinatura, 'I', ''); 

						$criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
			
						$sqlEdicaoPs1020   = linhaJsonEdicao('ID_ASSINATURA_PAGBANK', $retornoAssinatura['id']);
						gravaEdicao('PS1000', $sqlEdicaoPs1020, 'A', $criterioWhereGravacao);

						$retorno['STATUS']  = 'OK';
						$retorno['MSG'] 	= 'Assinatura realizada com sucesso!';
					}else{
						$retorno['STATUS']  = 'ERRO';
						$retorno['MSG'] 	= 'Erro ao criar assinatura';
					}
				}
			}else{
				$retorno['STATUS'] == 'ERRO';
				$retorno['MSG'] == 'Erro ao criar assinante';
			}
		}else{
			$retorno['STATUS']  = 'ERRO';
			$retorno['MSG'] 	= 'Erro ao cadastrar cartão. Ref[1.1]';
		}
		
	}
	
	echo json_encode($retorno);	

}else if($dadosInput['tipo'] =='retornaValorConfiguracao'){

	if(retornaValorConfiguracao($dadosInput['idConfiguracao']))
		$retorno['DADOS'][] = jn_utf8_encode(retornaValorConfiguracao($dadosInput['idConfiguracao']));
    else
        $retorno['DADOS'][] = '';

	echo json_encode($retorno);
	
}


function geraContrato($codigoAssociado){
	error_reporting(E_ALL ^ E_DEPRECATED);
	$_GET['codAssociado'] = $codigoAssociado;
	require('gerencia_contratos.php');	

	dispararEmailAssinatura($codigoAssociado);
}


function dispararEmailAssinatura($codigoAssociado){	
	$_GET['codigoModelo'] = 1;
	$_GET['vnd'] = true;
	$_GET['codigoAssociado'] = $codigoAssociado;
	$_GET['retornaMensagem'] = false;

	require('../EstruturaPrincipal/disparoEmail.php');	
}

function retornaValorPrevisaoAssinatura($codigoAssociado)
{

	$queryDados = 'Select  VND1000_ON.DATA_NASCIMENTO, VND1000_ON.CODIGO_TABELA_PRECO, VND1000_ON.CODIGO_PLANO
				   FROM VND1000_ON
				   WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);

	$resDados   = jn_query($queryDados);
	$rowDados   = jn_fetch_object($resDados);	
	
	$dataAtual = new DateTime(date('d-m-Y'));
	$dataNascimento = $rowDados->DATA_NASCIMENTO;
	if(!is_object($dataNascimento))
		$dataNascimento = new DateTime($rowDados->DATA_NASCIMENTO);	
	$retornoDifDatas = ($dataAtual->diff($dataNascimento));	
	$idade = $retornoDifDatas->format('%Y%');
	
	$queryTabelas  = ' Select coalesce(VALOR_PLANO,0) VALOR_PLANO From Ps1032 ';
	$queryTabelas .= ' WHERE CODIGO_PLANO = ' . numSql($rowDados->CODIGO_PLANO);
	$queryTabelas .= ' 	AND IDADE_MINIMA <= ' . numSql($idade);
	$queryTabelas .= ' 	AND IDADE_MAXIMA >= ' . numSql($idade);
	
	if($rowDados->CODIGO_TABELA_PRECO != 0)
		$queryTabelas .= ' 	AND CODIGO_TABELA_PRECO = ' . numSql($rowDados->CODIGO_TABELA_PRECO);
	
	$resTabelas   = jn_query($queryTabelas);
	$rowTabelas   = jn_fetch_object($resTabelas);
	
	return trim($rowTabelas->VALOR_PLANO);
	
}

?>
