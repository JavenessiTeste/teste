<?php
require_once('../lib/base.php');


//pr(geraCoparticitacaoDana('060083100870030','C','10101012','1768',123));

function geraCoparticitacaoDana($codigoAssociado,$tipoGuia,$codigoProcedimento,$codigoPrestador,$numeroRegistro){

	$valorRetorno = 0;
	
	$queryPrestador = 'select FLAG_NAO_GERA_COPARTICIPACAO from ps5000 
										where codigo_prestador ='.aspas($codigoPrestador);
										
	$resPrestador = jn_query($queryPrestador);
	$rowPrestador = jn_fetch_object($resPrestador);
					
		
	if($rowPrestador->FLAG_NAO_GERA_COPARTICIPACAO == 'S'){
		$valorRetorno = 0;
	}else{
		
		

		$sqlUsuario = 'select a.CODIGO_ASSOCIADO, Coalesce(b.VALOR_SALARIO,1) VALOR_SALARIO, ' .
					  ' a.CODIGO_TITULAR, a.NOME_ASSOCIADO, a.CODIGO_EMPRESA, ' .
					  ' a.NUMERO_CPF, a.CODIGO_ANTIGO, a.CODIGO_AUXILIAR, a.CODIGO_PESSOA_FISICA, a.CODIGO_FILIAL ' .
					  ' from Ps1000 a ' .
					  ' left outer join ps1000 b on (a.codigo_titular = b.codigo_associado) and (b.tipo_associado = "T") '.
					  ' where a.codigo_associado = '.aspas($codigoAssociado);
		$resUsuario  = jn_query($sqlUsuario);
		$rowUsuario = jn_fetch_object($resUsuario);                             


		$sqlVigencia = ' Select first 1 * from Esp0009 ' .
					   ' where Esp0009.Codigo_Associado = ' . aspas($rowUsuario->CODIGO_ASSOCIADO) .
					   ' and ' . dataToSql(date("d/m/Y")) .
					   ' between Esp0009.Data_Vigencia_Inicial and Esp0009.Data_Vigencia_Final' .
					   ' Order by Esp0009.Data_Vigencia_Final desc';

		$resVigencia = jn_query($sqlVigencia);
		
		if($rowVigencia = jn_fetch_object($resVigencia)){ 
		
			$valorRetorno = 0;

		}else{
			$queryValor = 	' select * from Esp0018' .
							' where Tipo_Guia = ' . aspas($tipoGuia) .
							' and Salario_Inicial = ' . aspas($rowUsuario->VALOR_SALARIO) .
							' and Codigo_Procedimento = ' . aspas($codigoProcedimento) .
							' and Data_Inutiliz_Registro is Null ' .
							' and ' . dataToSql(date("d/m/Y")) . ' between Data_Vigencia_Inicial and Data_Vigencia_Final' .
							" and Coalesce(Codigo_Filial, '') = ".aspas($rowUsuario->CODIGO_FILIAL) .
							' and Codigo_Empresa = ' . aspas($rowUsuario->CODIGO_EMPRESA);
							
			$resValor = jn_query($queryValor);
			
			if($rowValor = jn_fetch_object($resValor)){
			
				if($rowValor->FLAG_PERCENTUAL == 'N'){
					$valorRetorno = $rowValor->VALOR_DESCONTO;
				}else{
					$queryValorPrestador = 'select * from ps5005 
											where '.aspas($codigoProcedimento).' between ps5005.codigo_procedimento_inicial and 
											ps5005.codigo_procedimento_final  and ps5005.data_inutiliz_registro is null and
											codigo_prestador ='.aspas($codigoPrestador).' order by ps5005.valor_procedimento desc';
											
					$resValorPrestador = jn_query($queryValorPrestador);
					if($rowValorPrestador = jn_fetch_object($resValorPrestador)){
						$valorRetorno = $rowValorPrestador->VALOR_PROCEDIMENTO * ($rowValor->VALOR_DESCONTO / 100);
					}else{
						$valorRetorno = 0;
					}
			
					

				}
				if (($valorRetorno > $rowValor->VALOR_MAXIMO) and ($rowValor->VALOR_MAXIMO > 0))
				   $valorRetorno = $rowValor->VALOR_MAXIMO;
			
			}else{
				$valorRetorno = 0;
			}
			
			
		
		}
	}
	
	return $valorRetorno;
}

function geraMensagemCoparticipacaoDana($numeroAutorizacao){
	$queryAut = 'Select CODIGO_PRESTADOR,Ps1000.CODIGO_ASSOCIADO,TIPO_GUIA,FLAG_ENVIOU_MSG,FLAG_COPART_AUT,VALOR_TOTAL_COPARTICIPACAO,NOME_PRESTADOR,NOME_ASSOCIADO,CODIGO_TITULAR from ps6500 
				inner join ps5000 on ps5000.CODIGO_PRESTADOR = PS6500.CODIGO_PRESTADOR
				inner join PS1000 on Ps1000.CODIGO_ASSOCIADO = ps6500.CODIGO_ASSOCIADO
				
				where NUMERO_AUTORIZACAO='.aspas($numeroAutorizacao);
	$resAut   = jn_query($queryAut);
	$rowAut   = jn_fetch_object($resAut);
	if((($rowAut->FLAG_ENVIOU_MSG=='') or ($rowAut->FLAG_ENVIOU_MSG=='N'))and $rowAut->FLAG_COPART_AUT=='S'){
		$msg = '';
		$valor = 0;
		if($rowAut->TIPO_GUIA=='C'){
			if($rowAut->VALOR_TOTAL_COPARTICIPACAO>0){
			  $msg = $rowAut->NOME_ASSOCIADO . ' foi gerado uma coparticipação de '.$rowAut->VALOR_TOTAL_COPARTICIPACAO . ' com o prestador '.$rowAut->NOME_PRESTADOR;		
			  $valor = $rowAut->VALOR_TOTAL_COPARTICIPACAO;
			}
		}else{
			$queryValor = 'select coalesce(sum(valor_coparticipacao),0) VALOR from ps6510 where numero_autorizacao = '.aspas($numeroAutorizacao);
			$resValor   = jn_query($queryValor);
			$rowValor   = jn_fetch_object($resValor);
			$valor = $rowValor->VALOR;
			$msg = $rowAut->NOME_ASSOCIADO . ' foi gerado uma coparticipação de '.$valor . ' com o prestador '.$rowAut->NOME_PRESTADOR;		
			  
		}	
		jn_query('UPDATE PS6500 SET 
						 VALOR_TOTAL_COPARTICIPACAO = ' . aspas($valor) . ',
						 FLAG_ENVIOU_MSG = ' . aspas('S') . '
				  WHERE NUMERO_AUTORIZACAO = ' . aspas($numeroAutorizacao));
		$msg = utf8_decode($msg);
		$titulo = utf8_decode('Nova Coparticipação');
		if($msg!=''){
			$registro = jn_gerasequencial('APP_NOTIFICACOES');
			$insertMsg ="INSERT INTO APP_NOTIFICACOES (NUMERO_REGISTRO,TITULO_NOTIFICACAO, DESCRICAO_NOTIFICACAO, CODIGO_ASSOCIADO, DATA_NOTIFICACAO, NOME_TABELA, NOME_CHAVE_TABELA, VALOR_CHAVE_TABELA, PAGINA_ABERTURA, PARAMETROS_ABERTURA, DATA_CIENTE) VALUES 
                                                      (".aspas($registro).",".aspas($titulo).", ".aspas($msg).", ".aspas($rowAut->CODIGO_ASSOCIADO).", ".dataToSql(date("d/m/Y")).", 'PS6500', 'NUMERO_AUTORIZACAO', ".aspas($numeroAutorizacao).", NULL, NULL, NULL)";
			jn_query($insertMsg);
			$sqlCelular = 'select  cast(PS1006.CODIGO_AREA as varchar(2))||Ps1006.NUMERO_TELEFONE NUMERO_CELULAR from PS1000
						 left join PS1006 on PS1006.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
						 WHERE TIPO_TELEFONE = '.aspas('C').' AND PS1000.CODIGO_ASSOCIADO =  '.aspas($rowAut->CODIGO_TITULAR);
		
			$resCelular  = jn_query($sqlCelular);
			$rowCelular->NUMERO_CELULAR = '41998061407';
			
			if($rowCelular = jn_fetch_object($resCelular)){	
				if($rowCelular->NUMERO_CELULAR!=''){
					require('../EstruturaEspecifica/smsZenvia.php');
					$retornoSms = enviaSmsZenvia('55'.trim($rowCelular->NUMERO_CELULAR),$msg,$registro);
				}		
			}
		}
				  
	
	}
}


?>