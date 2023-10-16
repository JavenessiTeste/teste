<?php

if(@$_GET['CELULAR']=='OK'){
	require('../lib/autenticaCelular.php');
	if(substr($_SESSION['PERFIL_USUARIO'],0,9)=='ASSOCIADO'){
		$_SESSION['perfilOperador'] = 'BENEFICIARIO';
	}	
	$celular= true;
	
}elseif($_GET['PX']=='OK'){
	$_SESSION['perfilOperador'] = 'BENEFICIARIO';
}else{
	$celular= false;
	require('../lib/base.php');
	require('../private/autentica.php');

}

require('../lib/registroBoletoSantander.php');

require('../services/multaCancelamento.php');


if($dadosInput['tipo'] =='consultaCancelamento'){
	$retorno['HTML'] = ''; 
	$retorno['STATUS'] = 'OK'; 
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		$query = "select TIPO_ASSOCIADO,CODIGO_EMPRESA,DATA_EXCLUSAO from PS1000 where PS1000.CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->TIPO_ASSOCIADO!='T'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Somente o Titular pode solicitar o cancelamento.'; 
			}
			if($row->CODIGO_EMPRESA!='400'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Apenas o perfil o Titular Pf e a Empresa podem solicitar o cancelamento.'; 
			}
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}
			$empresa = '400';
			$codigoAssociado = $_SESSION['codigoIdentificacao'] ;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Erro ao Obter os dados.'; 
		}
		
	}else if($_SESSION['perfilOperador']=='EMPRESA'){
		$empresa = $_SESSION['codigoIdentificacao'] ;
		$codigoAssociado = '';
		$query = "select DATA_EXCLUSAO from PS1010 where PS1010.CODIGO_EMPRESA = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}		
		}
	}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Perfil invalido.';	
	}
		
	if($retorno['STATUS'] == 'OK'){	
	
		$retornoCancela = CancelaContrato($empresa,$codigoAssociado ,false);
		if($retornoCancela['STATUS']=='ERRO'){
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = $retornoCancela['MSG'];		
		}else{
			$retorno['HTML']   .= '<div>';
			$retorno['HTML']   .= '<p>'.$retornoCancela['MSG'].'</p>';
			$retorno['HTML']   .= '<br><p>Caso deseje continuar com o cancelamento preencha os campos abaixo e clique no botão confirmar.</p></div>';			
		}
	}
	echo json_encode($retorno);

}
if($dadosInput['tipo'] =='ConfirmaCancelamento'){

	$retorno['HTML'] = ''; 
	$retorno['STATUS'] = 'OK'; 
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		$query = "select TIPO_ASSOCIADO,CODIGO_EMPRESA,DATA_EXCLUSAO from PS1000 where PS1000.CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->TIPO_ASSOCIADO!='T'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Somente o Titular pode solicitar o cancelamento.'; 
			}
			if($row->CODIGO_EMPRESA!='400'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Apenas o perfil o Titular Pf e a Empresa podem solicitar o cancelamento.'; 
			}
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}
			$empresa = '400';
			$codigoAssociado = $_SESSION['codigoIdentificacaoTitular'] ;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Erro ao Obter os dados.'; 
		}
		
	}else if($_SESSION['perfilOperador']=='EMPRESA'){
		$empresa = $_SESSION['codigoIdentificacao'] ;
		$codigoAssociado = '';
		$query = "select DATA_EXCLUSAO from PS1010 where PS1010.CODIGO_EMPRESA = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}		
		}
	}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Perfil invalido.';	
	}
		
	if($retorno['STATUS'] == 'OK'){	
	
		/*
		    [nomeCompleto] => ssdghas dashkg dashdg as
            [cpf] => 11111111111
            [telefone] => 11111111111
            [Parentesco] => 1
            [motivo] => 1
            [nomeMae] => 2121 sdfsdf
            [cpfTitular] => 1231456
            [nascimentoTitular] => 1984-10-21T04:00:00.000Z
		*/
		
		//pr($dadosInput);
		//exit;
		$query = "select * from PS1000 
				  left join ps1002 on ps1000.Codigo_Associado = Ps1002.Codigo_Associado
				  where PS1000.CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacaoTitular']);
		$res  = jn_query($query);
		$retorno['HTML'] = '';
		if($row = jn_fetch_object($res)){
			$validado = true;
			
			if(strtoupper(trim($row->NOME_MAE)) != strtoupper(trim($dadosInput['dado']['nomeMae']))){
				$validado = false;
				//$retorno['HTML'] .= 'NN'.$dadosInput['dado']['nomeMae'] ;
			}
			if(str_replace('.','',str_replace('-','',str_replace(' ','',$row->NUMERO_CPF))) != str_replace('.','',str_replace('-','',str_replace(' ','',$dadosInput['dado']['cpfTitular'])))){
				$validado = false;
				//$retorno['HTML'] .= 'CC'.$dadosInput['dado']['cpfTitular'];
			}
			if(SqlToData($row->DATA_NASCIMENTO) != SqlToData(substr($dadosInput['dado']['nascimentoTitular'],0,10))){
				$validado = false;
				
				//$retorno['HTML'] .= 'DD'.SqlToData($row->DATA_NASCIMENTO).' ';
			}
			
			
			if(!$validado){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Os dados não conferem(1).';	
			}
		
		}else{
			
			$validado = false;
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Os dados não conferem(2).';	
		}
		
		if(!$validado){
			echo json_encode($retorno);
			exit;
		}
		//exit;
		$dadosInput['dado']['NOME_ASSOCIADO'] = utf8_encode($row->NOME_ASSOCIADO);
		$dadosInput['dado']['CODIGO_ASSOCIADO'] = $row->CODIGO_ASSOCIADO;
		$dadosInput['dado']['NUMERO_CPF'] = $row->NUMERO_CPF;
		$dadosInput['dado']['DATA_NASCIMENTO'] = SqlToData($row->DATA_NASCIMENTO);
		$dadosInput['dado']['DATA_ADMISSAO'] = SqlToData($row->DATA_ADMISSAO);
		$dadosInput['dado']['NOME_CONTRATANTE'] = utf8_encode($row->NOME_CONTRATANTE);
		$dadosInput['dado']['NUMERO_CPF_CONTRATANTE'] = $row->NUMERO_CPF_CONTRATANTE;
		$dadosInput['dado']['PROTOCOLO'] = '';
		if($_GET['PX']=='OK'){
			$dadosInput['dado']['ATENDENTE'] = utf8_encode($_GET['opd']);
		}else{
			$dadosInput['dado']['ATENDENTE'] = 'WEB/APP';
		}
		$dadosInput['dado']['DATA'] = date('d/m/Y');
		$dadosInput['dado']['HORA'] = date('h:i');

		$dadosInput['dado']['nomeCompleto'] = utf8_encode($dadosInput['dado']['nomeCompleto']);
		
		//print_r($dadosInput['dado']);
		//print_r($dadosInput['dado']['nomeCompleto']);
		
		//print_r(json_decode($dadosInput['dado']));
		//print_r(json_encode($dadosInput['dado']));
		//print_r(protocoloCancelaContrato($codigoEmpresa,$_SESSION['codigoIdentificacaoTitular'],$dadosInput['dado']));
		//exit;
		$retornoCancela = CancelaContrato($empresa,$codigoAssociado ,true);
		
		if($retornoCancela['STATUS'] == 'OK'){
				
			if($retornoCancela['FATURA']>0){
				if($empresa == '400'){
					$conta = '13000523';
					$retornoFatura = RegistraFatura($retornoCancela['FATURA'],$codigoAssociado,$conta);
				}else{
					$conta = '13001400';
					$retornoFatura = RegistraFatura($retornoCancela['FATURA'],$empresa,$conta );
				}		
				
				//pr($retornoFatura);
				//exit;
					
				if($retornoFatura['STATUS']=='OK'){
					$sqlFatura = 'Update  ps1020 set 
									CODIGO_IDENTIFICACAO_FAT = '.aspas($retornoFatura['DADOS']['titulo']['nossoNumero']).',
									CODIGO_BANCO = '.aspas('033').',
									MES_ANO_REFERENCIA = '.aspas(date("m/Y")).',
									CODIGO_CARTEIRA = '.aspas('101').',
									CODIGO_ULTIMA_EMISSAO = '.aspas('XML').',
									NUMERO_CONTA_COBRANCA = '.aspas($conta).', 
									NUMERO_LINHA_DIGITAVEL= '.aspas($retornoFatura['DADOS']['titulo']['linDig']).'
								  where numero_registro='.aspas($retornoCancela['FATURA']);
					$resFatura  = jn_query($sqlFatura);
					if($empresa == '400'){
						$cancelaFaturas = 'UPDATE  ps1020 set DATA_CANCELAMENTO ='.dataToSql(date("d/m/Y")).' 
										  where CODIGO_ASSOCIADO ='.aspas($codigoAssociado).' and 
												DATA_PAGAMENTO IS NULL AND 
												DATA_CANCELAMENTO IS NULL and
												NUMERO_REGISTRO <> '.aspas($retornoCancela['FATURA']);
						$resCancelaFaturas  = jn_query($cancelaFaturas);
						
						$CancelaBeneficiarios= 'UPDATE PS1000 set DATA_EXCLUSAO = '.dataToSql(date("d/m/Y")).',CODIGO_MOTIVO_EXCLUSAO='.aspas('1').'  WHERE CODIGO_TITULAR = '.aspas($codigoAssociado) . ' and DATA_EXCLUSAO IS NULL'; 
						$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);
					}else{
						$cancelaFaturas = 'UPDATE  ps1020 set DATA_CANCELAMENTO = '.dataToSql(date("d/m/Y")).' 
										  where CODIGO_EMPRESA ='.aspas($empresa).' and 
												DATA_PAGAMENTO IS NULL AND 
												DATA_CANCELAMENTO IS NULL and
												NUMERO_REGISTRO <> '.aspas($retornoCancela['FATURA']);
						$resCancelaFaturas  = jn_query($cancelaFaturas);
						
						$CancelaBeneficiarios= 'UPDATE PS1000 set DATA_EXCLUSAO = '.dataToSql(date("d/m/Y")).',CODIGO_MOTIVO_EXCLUSAO='.aspas('1').'  WHERE CODIGO_EMPRESA = '.aspas($empresa) . ' and DATA_EXCLUSAO IS NULL'; 
						$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);
						
						$CancelaBeneficiarios= 'UPDATE PS1010 set DATA_EXCLUSAO = '.dataToSql(date("d/m/Y")).' WHERE CODIGO_EMPRESA = '.aspas($empresa) . ' and DATA_EXCLUSAO IS NULL'; 
						$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);					
					}
					$retorno['FATURA'] = $retornoFatura;
					
				}Else{
					$sqlFatura = 'delete from ps1020 where numero_registro='.aspas($retornoCancela['FATURA']);
					$resFatura  = jn_query($sqlFatura);
					$retornoCancela['STATUS'] = 'ERRO';
					$retornoCancela['MSG']   = 'Erro ao gerar o boleto.('.$retornoFatura['DADOS']['descricaoErro'].')';		
				}
			}else{
				if($empresa == '400'){
					$CancelaBeneficiarios= 'UPDATE PS1000 set DATA_EXCLUSAO = '.dataToSql(date("d/m/Y")).',CODIGO_MOTIVO_EXCLUSAO='.aspas('1').' WHERE CODIGO_TITULAR = '.aspas($codigoAssociado) . ' and DATA_EXCLUSAO IS NULL'; 
					$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);
					$cancelaFaturas = 'UPDATE  ps1020 set DATA_CANCELAMENTO ='.dataToSql(date("d/m/Y")).' 
										  where CODIGO_ASSOCIADO ='.aspas($codigoAssociado).' and 
												DATA_PAGAMENTO IS NULL AND 
												DATA_CANCELAMENTO IS NULL ';
					$resCancelaFaturas  = jn_query($cancelaFaturas);
				}else{
					$CancelaBeneficiarios= 'UPDATE PS1000 set DATA_EXCLUSAO = '.dataToSql(date("d/m/Y")).',CODIGO_MOTIVO_EXCLUSAO='.aspas('1').'  WHERE CODIGO_EMPRESA = '.aspas($empresa) . ' and DATA_EXCLUSAO IS NULL'; 
					$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);
					$cancelaFaturas = 'UPDATE  ps1020 set DATA_CANCELAMENTO ='.dataToSql(date("d/m/Y")).' 
										  where CODIGO_EMPRESA ='.aspas($empresa).' and 
												DATA_PAGAMENTO IS NULL AND 
												DATA_CANCELAMENTO IS NULL ';
					$resCancelaFaturas  = jn_query($cancelaFaturas);
						
					$CancelaBeneficiarios= 'UPDATE PS1010 set DATA_EXCLUSAO = '.dataToSql(date("d/m/Y")).' WHERE CODIGO_EMPRESA = '.aspas($empresa) . ' and DATA_EXCLUSAO IS NULL'; 
					$resCancelaBeneficiarios  = jn_query($CancelaBeneficiarios);	
				}
			}
		}

			if($retornoCancela['STATUS']=='ERRO'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = $retornoCancela['MSG'];		
			}else{
				$retorno['STATUS'] = 'OK';
				$retornoProtocolo = protocoloCancelaContrato($empresa,$_SESSION['codigoIdentificacaoTitular'],$dadosInput['dado'],$retornoCancela['FATURA']);
				$retorno['HTML'] = 'Cancelamento foi efetuada com sucesso.<br>Protocolo Gerado: '.$retornoProtocolo['PROTOCOLO'].'<br>';
				if($_GET['PX']=='OK'){
					$retorno['HTML'] .='<br><br> '.$retornoProtocolo['LINK'];
				}else{
					$retorno['HTML'] .='<br><br><img src="'.$retornoProtocolo['LINK'].'"/>';
				}
			
			}
		}
	echo json_encode($retorno);
	
}
if($dadosInput['tipo'] =='consultaAlteraVencimento'){

	$retorno['HTML'] = ''; 
	$retorno['STATUS'] = 'OK'; 
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		$query = "select TIPO_ASSOCIADO,CODIGO_EMPRESA,DATA_EXCLUSAO from PS1000 where PS1000.CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->TIPO_ASSOCIADO!='T'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Somente o tTitular pode alterar o vencimento.'; 
			}
			if($row->CODIGO_EMPRESA!='400'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Apenas o perfil o Titular Pf e a Empresa alterar o vencimento.'; 
			}
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}
			$empresa = '400';
			$codigoAssociado = $_SESSION['codigoIdentificacao'] ;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Erro ao Obter os dados.'; 
		}
		
	}else if($_SESSION['perfilOperador']=='EMPRESA'){
		$empresa = $_SESSION['codigoIdentificacao'] ;
		$codigoAssociado = '';
		$query = "select DATA_EXCLUSAO from PS1010 where PS1010.CODIGO_EMPRESA = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}		
		}
	}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Perfil invalido.';	
	}
		
	if($retorno['STATUS'] == 'OK'){	
	
		$retornoConsulta = ConsultaAlteraVencimento($empresa,$codigoAssociado);
		if($retornoConsulta['STATUS']=='ERRO'){
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = $retornoConsulta['MSG'];		
		}else{
			$retorno['HTML']   .= '<div>';
			$retorno['HTML']   .= '<p>'.$retornoConsulta['MSG'].'</p>';
			$retorno['HTML']   .= '<br>';
			$retorno['DIAS']    = $retornoConsulta['DIAS'];			
		}
	}
	echo json_encode($retorno);
	
	
}

if($dadosInput['tipo'] =='confirmaAlteraVencimento'){
	
	$retorno['HTML'] = ''; 
	$retorno['STATUS'] = 'OK'; 
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		$query = "select TIPO_ASSOCIADO,CODIGO_EMPRESA,DATA_EXCLUSAO from PS1000 where PS1000.CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->TIPO_ASSOCIADO!='T'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Somente o Titular pode alterar o vencimento.'; 
			}
			if($row->CODIGO_EMPRESA!='400'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Apenas o perfil o Titular Pf e a Empresa alterar o vencimento.'; 
			}
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}
			$empresa = '400';
			$codigoAssociado = $_SESSION['codigoIdentificacao'] ;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Erro ao Obter os dados.'; 
		}
		
	}else if($_SESSION['perfilOperador']=='EMPRESA'){
		$empresa = $_SESSION['codigoIdentificacao'] ;
		$codigoAssociado = '';
		$query = "select DATA_EXCLUSAO from PS1010 where PS1010.CODIGO_EMPRESA = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}		
		}
	}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Perfil invalido.';	
	}
		
	if($retorno['STATUS'] == 'OK'){	
		
		$retornoConsulta = ConfirmaAlteraVencimento($empresa,$codigoAssociado,$dadosInput['vencimento']);
		if($retornoConsulta['STATUS']=='ERRO'){
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = $retornoConsulta['MSG'];		
		}else{
			$retorno['HTML']   = $retornoConsulta['MSG'];
			$erroRegistro = false;
			for ($i = 0; $i < count($retornoConsulta['NOVAS_FATURAS']); $i++) {
					if($empresa == '400'){
						$conta = '13000523';
						$retornoFatura = RegistraFatura($retornoConsulta['NOVAS_FATURAS'][$i],$codigoAssociado,$conta);
					}else{
						$conta = '13001400';
						$retornoFatura = RegistraFatura($retornoConsulta['NOVAS_FATURAS'][$i],$empresa,$conta );
					}
					if($retornoFatura['STATUS']=='OK'){
						$sqlFatura = 'Update  ps1020 set 
									CODIGO_IDENTIFICACAO_FAT = '.aspas($retornoFatura['DADOS']['titulo']['nossoNumero']).',
									CODIGO_BANCO = '.aspas('033').',
									CODIGO_CARTEIRA = '.aspas('101').',
									CODIGO_ULTIMA_EMISSAO = '.aspas('XML').',
									NUMERO_CONTA_COBRANCA = '.aspas($conta).', 
									NUMERO_LINHA_DIGITAVEL= '.aspas($retornoFatura['DADOS']['titulo']['linDig']).'
								  where numero_registro='.aspas($retornoConsulta['NOVAS_FATURAS'][$i]);
						$resFatura  = jn_query($sqlFatura);
					}else{
						$erroRegistro = true;
						break;
					}
					//print_r($retornoFatura);	
			
			}
			if($erroRegistro){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Não foi possivel concluir a solicitação, entre em contato.';	
				if($retornoFatura['STATUS']=='ERRO'){
					$retorno['HTML']   .='<br>'.$retornoFatura['DADOS']['descricaoErro'];
				}
			
				for ($i = 0; $i < count($retornoConsulta['NOVAS_FATURAS']); $i++) {
					$delete = 'delete from ps1020 where numero_registro ='.aspas($retornoConsulta['NOVAS_FATURAS'][$i]);
					jn_query($delete);
				}
			}else{
				for ($i = 0; $i < count($retornoConsulta['VELHAS_FATURAS']); $i++) {
					$cancelaFatura = 'UPDATE  ps1020 set DATA_CANCELAMENTO = GETDATE() 
									  where NUMERO_REGISTRO = '.aspas($retornoConsulta['VELHAS_FATURAS'][$i]);
					$resCancelaFatura  = jn_query($cancelaFatura);
				}
				if($empresa == '400'){
						$selectDia = 'select DIA_VENCIMENTO from ps1002 Where codigo_associado ='. aspas($codigoAssociado);
				}else{
						$selectDia = 'select DIA_VENCIMENTO from ps1002 Where codigo_Empresa ='. aspas($empresa);
				}
				$resDia  = jn_query($selectDia);
				$rowDia = jn_fetch_object($resDia);
				if($empresa == '400'){
						$update = 'update ps1002 set dia_vencimento = '.aspas($dadosInput['vencimento']).'Where codigo_associado ='. aspas($codigoAssociado);
				}else{
						$update = 'update ps1002 set dia_vencimento = '.aspas($dadosInput['vencimento']).'Where codigo_Empresa ='. aspas($empresa);
				}
				jn_query($update);
				
				$retorno = protocoloAlteraVencimento($empresa,$codigoAssociado,$rowDia->DIA_VENCIMENTO,$dadosInput['vencimento']);
				$retorno['HTML'] = 'A Alteração do dia vencimento foi efetuada com sucesso.<br>Protocolo Gerado: '.$retorno['PROTOCOLO'].'<br>';
				if(!$celular)
					$retorno['HTML'] .= ' <div>Clique <a href="site/gridDinamico?tabela=VW_SEGUNDA_VIA_AL2&at=1646077267137"> aqui </a> para visualizar suas faturas.</div>'.'<br>';
				$retorno['HTML'] .='<br><br><img src="'.$retorno['LINK'].'"/>';
				
			}
		}
	}
	echo json_encode($retorno);
	
}
if($dadosInput['tipo'] =='valorAlteraVencimento'){
	$retorno['HTML'] = ''; 
	$retorno['STATUS'] = 'OK'; 
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		$query = "select TIPO_ASSOCIADO,CODIGO_EMPRESA,DATA_EXCLUSAO from PS1000 where PS1000.CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->TIPO_ASSOCIADO!='T'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Somente o Titular pode alterar o vencimento.'; 
			}
			if($row->CODIGO_EMPRESA!='400'){
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Apenas o perfil o Titular Pf e a Empresa alterar o vencimento.'; 
			}
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}
			$empresa = '400';
			$codigoAssociado = $_SESSION['codigoIdentificacao'] ;	
		}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Erro ao Obter os dados.'; 
		}
		
	}else if($_SESSION['perfilOperador']=='EMPRESA'){
		$empresa = $_SESSION['codigoIdentificacao'] ;
		$codigoAssociado = '';
		$query = "select DATA_EXCLUSAO from PS1010 where PS1010.CODIGO_EMPRESA = ".aspas($_SESSION['codigoIdentificacao']);
		$res  = jn_query($query);
		if($row = jn_fetch_object($res)){
			if($row->DATA_EXCLUSAO==''){
			
			}else{
				$retorno['STATUS'] = 'ERRO';
				$retorno['HTML']   = 'Usuario não ativo.'; 
			}		
		}
	}else{
			$retorno['STATUS'] = 'ERRO';
			$retorno['HTML']   = 'Perfil invalido.';	
	}
		
	if($retorno['STATUS'] == 'OK'){	
	
		$retornoConsulta = ValorAlteraVencimento($empresa,$codigoAssociado,$dadosInput['vencimento']);
		$retorno['HTML'] = $retornoConsulta['MSG'];
	}
	
	echo json_encode($retorno);
}

function protocoloAlteraVencimento($codigoEmpresa,$codigoAssociado,$diaAntigo,$diaNovo){
	
	if($codigoEmpresa==400){
		$tipo= 'F';
		$descricao = 'Troca Dia Vencimento: Solicitada via portal ' . "\n" . 'Codigo do associado:' . $codigoAssociado . "\n";		
		$selectDados = 'Select NOME_ASSOCIADO NOME, NUMERO_CPF DOCUMENTO, ENDERECO,BAIRRO, CIDADE,CEP,ENDERECO_EMAIL,DATA_ADMISSAO
						from PS1000
						left JOIN PS1001 on PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO WHERE ps1000.CODIGO_ASSOCIADO='.aspas($codigoAssociado);
	}else{
		$tipo= 'J';
		$descricao = 'Troca Dia Vencimento: Solicitada via portal ' . "\n" . 'Codigo do empresa:' . $codigoEmpresa . "\n";	
		$selectDados = 'Select NOME_EMPRESA NOME, NUMERO_CNPJ DOCUMENTO, ENDERECO,BAIRRO, CIDADE,CEP,ENDERECO_EMAIL,DATA_ADMISSAO
						from PS1010
						left JOIN PS1001 on PS1010.CODIGO_EMPRESA = PS1001.CODIGO_EMPRESA WHERE PS1010.CODIGO_EMPRESA='.aspas($codigoEmpresa);
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

	$query =" INSERT INTO ESP_PROTOCOLO_VENCIMENTO
					   (NOME
					   ,DOCUMENTO
					   ,ENDERECO
					   ,BAIRRO
					   ,CIDADE
					   ,CEP
					   ,PROTOCOLO
					   ,HASH
					   ,TIPO
					   ,DIA_VENCIMENTO_ANTIGO
					   ,DIA_VENCIMENTO_NOVO
					   ,DATA_ALTERACAO
					   ,HORA_ALTERACAO
					   ,DATA_CONTRATO
					   ,ASSINATURA)
				 VALUES
					   (".aspas($rowDados->NOME)."
					   ,".aspas($rowDados->DOCUMENTO)."
					   ,".aspas($rowDados->ENDERECO)."
					   ,".aspas($rowDados->BAIRRO)."
					   ,".aspas($rowDados->CIDADE)."
					   ,".aspas($rowDados->CEP)."
					   ,".aspas($protocoloAtendimento)."
					   ,".aspas($hash)."
					   ,".aspas($tipo)."
					   ,".aspas($diaAntigo)."
					   ,".aspas($diaNovo)."
					   ,".dataToSql($data)."
					   ,".aspas($hora)."
					   ,".dataToSql(($rowDados->DATA_ADMISSAO->format('d/m/Y')))."
					   ,".aspas($assinatura).")";

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
		
		$link = 'https://app.plenasaude.com.br/AliancaAppNet2/ServidorAl2/ProcessoDinamico/relatorioVencimentoPlena.php?cod='.$hash;
							
		
		jn_query($query);
		
		$corpoMSG  = '	<!doctype html>
					<html>
						<head>
							<meta charset="utf-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">							
						</head>
						<body >
							Prezado, ' . $rowEmp->NOME . '<br>
							
							Sua alteração de vencimento foi efetuada com sucesso.
							
							<br>' . $link . '<br>
							
							Protocolo Atendimento: ' . $protocoloAtendimento . ' <br>
							Permanecemos a sua disposicao, <br>
							Plena Saude

						</body>
					</html>';

		//$emailAssociado = 'diego2607@gmail.com';
		
		if(trim($emailAssociado!=''))
			disparaEmailMulta($emailAssociado, $protocoloAtendimento, 'ALTERAÇÃO DE VENCIMENTO', $corpoMSG);
		
		$ps1007 ="\nDia : " . date('d/m/Y')."
		Dia Vencimento Antigo : ".$diaAntigo . "
		Dia Vencimento Novo : ".$diaNovo . "
		Protocolo : ".$protocoloAtendimento . "
		\n".$link."
		\n".$assinatura."
		\n\n
		****   
		\n\n
		";
		if($codigoEmpresa==400){
			$resDados  = jn_query("Select * from PS1007 where CODIGO_ASSOCIADO = ".aspas($codigoAssociado));
		}else{
			$resDados  = jn_query("Select * from PS1007 where CODIGO_EMPRESA = ".aspas($codigoAssociado));
		}
		if($rowDados = jn_fetch_object($resDados)){
				if($codigoEmpresa==400){
					$query = "UPDATE Ps1007 set Observacao_Cobranca = concat(".aspas($ps1007).",Observacao_Cobranca) WHERE CODIGO_ASSOCIADO=".aspas($codigoAssociado);
				}else{
					$query = "UPDATE Ps1007 set Observacao_Cobranca = concat(".aspas($ps1007).",Observacao_Cobranca) WHERE CODIGO_EMPRESA = ".aspas($codigoEmpresa);
				}
		}else{
				if($codigoEmpresa==400){
					$query = "INSERT INTO PS1007(CODIGO_ASSOCIADO, OBSERVACAO_COBRANCA) VALUES(".aspas($codigoAssociado).",".aspas($ps1007).")";
				}else{
					$query = "INSERT INTO PS1007(CODIGO_EMPRESA, OBSERVACAO_COBRANCA) VALUES(".aspas($codigoEmpresa).",".aspas($ps1007).")";
				}
		}
		jn_query($query);
		
		$retorno['PROTOCOLO'] = $protocoloAtendimento;
		$retorno['LINK'] = $link;
		return $retorno;
		
}
function disparaEmailMulta($emailAssociado, $protocolo = '', $assunto = '', $corpoMSG = ''){
	
	if($assunto == '')
		$assunto = 'Assunto Teste';	
	
	if($corpoMSG == '')
		$corpoMSG = 'Mensagem Teste';	
	
	$corpoMSG = utf8_decode($corpoMSG);
	$assunto = utf8_decode($assunto);

	require_once('../lib/class.phpmailer.php');
	require_once("../lib/PHPMailerAutoload.php");

	$mail   = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');	
	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));	
	$mail->AddAddress($emailAssociado, $emailAssociado);
	

	
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoMSG);
	
	if(!$mail->Send()) {		
		echo "Erro: " . $mail->ErrorInfo;
	}
}

function protocoloCancelaContrato($codigoEmpresa,$codigoAssociado,$dados,$fatura){
	
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

	$dados['TIPO']       = utf8_encode($tipo);
	$dados['ASSINATURA'] = utf8_encode($assinatura);
	$dados['PROTOCOLO']  = $protocoloAtendimento;

	

	$query =" INSERT INTO ESP_ASSINATURA_DOCUMENTO
					   (CAMPOS
					   ,HASH)
				 VALUES
					   ('".(json_encode(($dados)))."'
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
		
		$link = 'https://app.plenasaude.com.br/AliancaAppNet2/ServidorAl2/ProcessoDinamico/relatorioCancelamentoMultaPlena.php?cod='.$hash;
		$linkPdf = 'https://app.plenasaude.com.br/AliancaAppNet2/ServidorAl2/ProcessoDinamico/relatorioCancelamentoMultaPlenaPdf.php?cod='.$hash;					
		
		jn_query($query);
		
		$corpoMSG  = '	<!doctype html>
					<html>
						<head>
							<meta charset="utf-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">							
						</head>
						<body >
							Prezado, ' . $rowEmp->NOME . '<br>
							
							Cancelamento foi efetuado com sucesso.
							
							<br>
							
							Protocolo Atendimento: ' . $protocoloAtendimento . ' <br>
							Permanecemos a sua disposicao, <br>
							Plena Saude

						</body>
					</html>';

		//$emailAssociado = 'diego2607@gmail.com';
		
		if(trim($emailAssociado!=''))
			disparaEmailCancelamento($emailAssociado, $protocoloAtendimento, 'Cancelamento', $corpoMSG,$fatura,$codigoAssociado,$linkPdf);
		
		$ps1007 ="\nDia : " . date('d/m/Y')."
		Cancelamento Contrato
		Protocolo : ".$protocoloAtendimento . "
		\n".$link."
		\n".$assinatura."
		\n\n
		****   
		\n\n
		";
		if($codigoEmpresa==400){
			$resDados  = jn_query("Select * from PS1007 where CODIGO_ASSOCIADO = ".aspas($codigoAssociado));
		}else{
			$resDados  = jn_query("Select * from PS1007 where CODIGO_EMPRESA = ".aspas($codigoAssociado));
		}
		if($rowDados = jn_fetch_object($resDados)){
				if($codigoEmpresa==400){
					$query = "UPDATE Ps1007 set Observacao_Cobranca = concat(".aspas($ps1007).",Observacao_Cobranca) WHERE CODIGO_ASSOCIADO=".aspas($codigoAssociado);
				}else{
					$query = "UPDATE Ps1007 set Observacao_Cobranca = concat(".aspas($ps1007).",Observacao_Cobranca) WHERE CODIGO_EMPRESA = ".aspas($codigoEmpresa);
				}
		}else{
				if($codigoEmpresa==400){
					$query = "INSERT INTO PS1007(CODIGO_ASSOCIADO, OBSERVACAO_COBRANCA) VALUES(".aspas($codigoAssociado).",".aspas($ps1007).")";
				}else{
					$query = "INSERT INTO PS1007(CODIGO_EMPRESA, OBSERVACAO_COBRANCA) VALUES(".aspas($codigoEmpresa).",".aspas($ps1007).")";
				}
		}
		jn_query($query);
		
		$retorno['PROTOCOLO'] = $protocoloAtendimento;
		$retorno['LINK'] = $link;
		return $retorno;
		
}

function disparaEmailCancelamento($emailAssociado, $protocolo = '', $assunto = '', $corpoMSG = '',$fatura,$codigoAssociado,$linkPdf=null){
	
	if($assunto == '')
		$assunto = 'Assunto Teste';	
	
	if($corpoMSG == '')
		$corpoMSG = 'Mensagem Teste';	
	
	$corpoMSG = utf8_decode($corpoMSG);
	$assunto = utf8_decode($assunto);

	require_once('../lib/class.phpmailer.php');
	require_once("../lib/PHPMailerAutoload.php");

	$mail   = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');	
	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));	
	$mail->AddAddress($emailAssociado, $emailAssociado);
	
	//if($_GET['PX']=='OK'){
		$mail->AddBCC("centraldeatendimento@plenasaude.com.br", "centraldeatendimento");
		$mail->AddBCC("negociacao@plenasaude.com.br", "negociacao");
	//}	
	if($linkPdf!=null){
		$html = file_get_contents($linkPdf);
		$mail->addStringAttachment($html,'Cancelamento.pdf');
	}
	
	
	if($fatura>0){
		$attachmentUrl = 'https://app.plenasaude.com.br/AliancaAppNet2/ServidorAl2/boletos/boleto_santander_PlenaPDF.php?numeroRegistro='.$fatura.'&cod='.$codigoAssociado;
		$html = file_get_contents($attachmentUrl);
		$mail->addStringAttachment($html,'Boleto.pdf');
	}
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoMSG);
	
	if(!$mail->Send()) {		
		return  "Erro: " . $mail->ErrorInfo;
	}else{
		return "OK";
	}
}

?>