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

global $codigoPlano;
global $codigoTabela;
global $codigoCarencia;

$codigoPlano    = retornaValorConfiguracao('CODIGO_PLANO_ODONTO');//'71';//retornaValorConfiguracao('CODIGO_PLANO_ODONTO');
$codigoTabela   = retornaValorConfiguracao('CODIGO_TABELA_ODONTO');//'2';//retornaValorConfiguracao('CODIGO_TABELA_ODONTO');
$codigoCarencia = retornaValorConfiguracao('CODIGO_CARENCIA_ODONTO');//'1';//retornaValorConfiguracao('CODIGO_CARENCIA_ODONTO');

if($dadosInput['tipo'] =='consultaVendaPlano'){
	$retorno['TITULO'] = 'Venda Plano Odonto';
	$retorno['HTML']   = ''; 
	$retorno['CONFIRMAR'] = true;
	$retorno['DIAS'] = array();
	
	for ($i = 1; $i <= 30; $i++) {
		$retorno['DIAS'][] = $i;
	}
	
		
	if($_SESSION['perfilOperador']=='BENEFICIARIO'){
		
		$queryCpf = 'select PS1000.* from PS1000
					 inner join PS1000 B on PS1000.CODIGO_ASSOCIADO <> B.CODIGO_ASSOCIADO AND B.NUMERO_CPF = PS1000.NUMERO_CPF and B.DATA_EXCLUSAO IS NULL and B.CODIGO_PLANO = '.aspas($codigoPlano).'
					 where PS1000.codigo_associado = '.aspas($_SESSION['codigoIdentificacao']);
		$resCpf  = jn_query($queryCpf);
		if($rowCpf = jn_fetch_object($resCpf)){
			$retorno['HTML'] = 'Você Já Possui o Plano Odonto Cadastrado.'; 
			$retorno['CONFIRMAR'] = false;
		}else{
			$retorno['HTML'] = 'Não Foi possivel encontrar os dados.'; 
			$retorno['CONFIRMAR'] = false;
			$query = "select PS1000.CODIGO_ASSOCIADO, PS1000.CODIGO_TITULAR,PS1000.NOME_ASSOCIADO,VW_BENEF_IDADE_UNION_TEMP.IDADE,PS1032.VALOR_PLANO from ps1000
						inner join VW_BENEF_IDADE_UNION_TEMP ON VW_BENEF_IDADE_UNION_TEMP.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
						inner join PS1030 on PS1030.CODIGO_PLANO = ".aspas($codigoPlano)."
						inner join PS1032 on PS1032.CODIGO_PLANO = PS1030.CODIGO_PLANO
						where  PS1032.CODIGO_TABELA_PRECO = ".aspas($codigoTabela)." and VW_BENEF_IDADE_UNION_TEMP.IDADE between PS1032.IDADE_MINIMA AND PS1032.IDADE_MAXIMA and Ps1000.DATA_EXCLUSAO is null and PS1000.CODIGO_TITULAR = ".aspas($_SESSION['codigoIdentificacao'])."
						order by PS1000.TIPO_ASSOCIADO DESC, PS1000.NOME_ASSOCIADO ";
			$res  = jn_query($query);
			$primeiro = true;
			$total = 0;
			While($row = jn_fetch_object($res)){
				if($primeiro){
					$retorno['HTML'] = '<p>Para contratar o Plano Odonto, verifique os valores, preencha os campos abaixo e confirme a contratação.</p>'; 
					$retorno['HTML'].= '<table >';
					$retorno['CONFIRMAR'] = true;
				}
				$primeiro = false;
				$retorno['HTML'].='<tr><td>'.$row->NOME_ASSOCIADO.'</td><td>'.number_format($row->VALOR_PLANO, 2,'.','').'</td></tr>';
				$total += $row->VALOR_PLANO; 
			}
			if(!$primeiro){
				//$retorno['HTML'].='<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
				$retorno['HTML'].='<tr><td>Total</td><td>'.number_format($total, 2,'.','').'</td></tr>';
				$retorno['HTML'].= '</table >';
				$selectEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1000 WHERE PS1000.CODIGO_ASSOCIADO= '.aspas($_SESSION['codigoIdentificacaoTitular']);
				$resEmpresa  = jn_query($selectEmpresa);
				$dadosEndereco = array();
				$dadosEndereco['vencimento'] = 1;
				$dadosEndereco['cep'] = '';
				$dadosEndereco['endereco'] = '';
				$dadosEndereco['numero'] = '';
				$dadosEndereco['complemento'] = '';
				$dadosEndereco['bairro'] = '';
				$dadosEndereco['cidade'] = '';
				$dadosEndereco['estado'] = '';
				//$dadosEndereco['codigo']     = '';
				
				if($rowEmpresa = jn_fetch_object($resEmpresa)){	
					if($rowEmpresa->CODIGO_EMPRESA == '400' )
						$queryEnd = 'select * from ps1001 where Ps1001.codigo_associado = '.aspas($_SESSION['codigoIdentificacaoTitular']);
					else
						$queryEnd = 'select * from ps1015 where Ps1015.codigo_associado = '.aspas($_SESSION['codigoIdentificacaoTitular']);
					
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
						$dadosEndereco['cep'] = $rowEnd->CEP;
						$dadosEndereco['endereco'] = jn_utf8_encode($endereco);
						$dadosEndereco['numero'] = jn_utf8_encode($numero);
						$dadosEndereco['complemento'] = jn_utf8_encode($complemento);
						$dadosEndereco['bairro'] = jn_utf8_encode($rowEnd->BAIRRO);
						$dadosEndereco['cidade'] = jn_utf8_encode($rowEnd->CIDADE);
						$dadosEndereco['estado'] = jn_utf8_encode($rowEnd->ESTADO);
						
				}
				$retorno['DADOS'] = $dadosEndereco;
			}
		}
		

		
	}
	echo json_encode($retorno);

}

if($dadosInput['tipo'] =='salvaDados'){
	
	//print_r($dadosInput);
	$retorno = array();
	$retorno['STATUS'] = 'OK';
	$retorno['MSG']    = 'Contratação Efetuado com Sucesso.';
	$dado = '';
	$dado = importaTitular('400',$_SESSION['codigoIdentificacaoTitular'],$dadosInput['dados']);

	if($dado!=''){
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG']    = 'Erro ao tentar fazer a contratação.<br><br>'.$dado;
	}
	echo json_encode($retorno);
}	
if($dadosInput['tipo']== 'pesquisaCep'){
	
	
	$json_file = file_get_contents('http://viacep.com.br/ws/'. retiraCaractere($dadosInput['cep']) .'/json');   
	$json_str = json_decode($json_file, true);	
	
	$dadosEndereco['cep'] = $json_str['cep'];
	$dadosEndereco['endereco'] 	= jn_utf8_encode(strToUpper(retiraCaractere($json_str['logradouro'])));
	$dadosEndereco['bairro'] 	= jn_utf8_encode(strToUpper(retiraCaractere($json_str['bairro'])));
	$dadosEndereco['cidade'] 	= jn_utf8_encode(strToUpper(retiraCaractere($json_str['localidade'])));
	$dadosEndereco['estado'] 	= jn_utf8_encode(strToUpper($json_str['uf']));	


	echo json_encode($dadosEndereco);

}
function importaTitular($codigoEmpresaGerado,$codigoAssociado,$dados){
	
	global $codigoPlano;
	global $codigoTabela;
	global $codigoCarencia;
	
	$retorno = ''; 
	
	$codigoSequencial = jn_gerasequencial('PS1000');
	$codigoTitularGerado = '01'.substr($codigoEmpresaGerado, 0, 3).str_pad(substr($codigoSequencial, 0, 7), 7, "0", STR_PAD_LEFT).'000';
	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
					A.NOME_TABELA = 'PS1000' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
					select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
					B.NOME_TABELA = 'PS1000' and B.NOME_CAMPO <>'CODIGO_EMPRESA' 
					and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO' and  B.NOME_CAMPO <>'CODIGO_ANTIGO' and  B.NOME_CAMPO <>'NUMERO_DEPENDENTE'
					and B.NOME_CAMPO <>'CODIGO_SEQUENCIAL' and B.NOME_CAMPO <>'CODIGO_TITULAR' and  B.NOME_CAMPO <>'SEPARADOR_01'and  B.NOME_CAMPO <>'SEPARADOR_02'and  B.NOME_CAMPO <>'SEPARADOR_03'and  B.NOME_CAMPO <>'SEPARADOR_04' AND  B.NOME_CAMPO <>'CODIGO_PLANO' and  B.NOME_CAMPO <>'CODIGO_TABELA_PRECO' and  B.NOME_CAMPO <>'CODIGO_CARENCIA'
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
											 NUMERO_DEPENDENTE,
											 CODIGO_PLANO,
											 CODIGO_TABELA_PRECO,
											 CODIGO_CARENCIA,".
											 $campos.")
						 select ".aspas($codigoEmpresaGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoSequencial).",".
								  aspas($codigoAssociado).","
								  ."0,".
								  aspas($codigoPlano).",".
								  aspas($codigoTabela).",".
								  aspas($codigoCarencia).",".
								  $campos." FROM PS1000 where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);
	
	if(!jn_query($queryInsertPS1000)){
		$retorno .=' E 1000 T ';
	}
	if($codigoEmpresaGerado==400){
		$retorno .=importaEnderecoAssociado($codigoTitularGerado,$dados);
		$retorno .=importaContratoAssociado($codigoTitularGerado,$dados['vencimento']);
		$retorno .=importaTelefoneAssociado($codigoTitularGerado,$codigoAssociado);
	}
	

	$queryDep ='select CODIGO_ASSOCIADO from PS1000 where TIPO_ASSOCIADO='.aspas('D').' and CODIGO_TITULAR ='.aspas($codigoAssociado);
	$resDep  = jn_query($queryDep);
	
	$i=1;
	while($rowDep  = jn_fetch_object($resDep)){
		$retorno .=importaDependente($codigoEmpresaGerado,$codigoTitularGerado,$rowDep->CODIGO_ASSOCIADO,$codigoSequencial,$i);
		$i++;
	}
	
	return $retorno; 
	
}
function importaDependente($codigoEmpresaGerado,$codigoTitularGerado,$codigoAssociado,$codigoSequencial,$numeroDependente){
	
	global $codigoPlano;
	global $codigoTabela;
	global $codigoCarencia;
	
	
	$codigoAssociadoGerado = '01'.substr($codigoEmpresaGerado, 0, 3).str_pad(substr($codigoSequencial, 0, 7), 7, "0", STR_PAD_LEFT).str_pad(substr($numeroDependente, 0, 2), 2, "0", STR_PAD_LEFT).'0';
	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
					A.NOME_TABELA = 'PS1000' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
					select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
					B.NOME_TABELA = 'PS1000' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and B.NOME_CAMPO <>'SEPARADOR_01'and  B.NOME_CAMPO <>'SEPARADOR_02'and  B.NOME_CAMPO <>'SEPARADOR_03'and  B.NOME_CAMPO <>'SEPARADOR_04' 
					and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO' and  B.NOME_CAMPO <>'CODIGO_ANTIGO' and  B.NOME_CAMPO <>'NUMERO_DEPENDENTE'
					and B.NOME_CAMPO <>'CODIGO_TITULAR' and B.NOME_CAMPO <>'CODIGO_SEQUENCIAL' and  B.NOME_CAMPO <>'CODIGO_PLANO' and  B.NOME_CAMPO <>'CODIGO_TABELA_PRECO' and  B.NOME_CAMPO <>'CODIGO_CARENCIA'
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
											 NUMERO_DEPENDENTE,
											 CODIGO_PLANO,
											 CODIGO_TABELA_PRECO,
											 CODIGO_CARENCIA,".
											 $campos.")
						 select ".aspas($codigoEmpresaGerado).",".
								  aspas($codigoAssociadoGerado).",".
								  aspas($codigoTitularGerado).",".
								  aspas($codigoSequencial).",".
								  aspas($codigoAssociado).",".
								  aspas($numeroDependente).",".
								  aspas($codigoPlano).",".
								  aspas($codigoTabela).",".
								  aspas($codigoCarencia).",".
								  $campos." FROM TMP1000_GETNET where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);
	
	if(!jn_query($queryInsertPS1000)){
		$retorno .=' E 1000 D ';
	}
	
}
function importaEnderecoAssociado($codigoAssociadoGerado,$dados){


	$queryInsertPS1001 = 'INSERT INTO PS1001(CODIGO_ASSOCIADO,ENDERECO,BAIRRO,CIDADE,ESTADO,CEP,DATA_ALTERACAO_CADASTRAL)
							   VALUES('.aspas($codigoAssociadoGerado).','.
										aspas($endereco).','.
										aspas($dados['bairro']).','.
										aspas($dados['cidade']).','.
										aspas($dados['estado']).','.
										aspas($dados['cep']).','.
										dataToSql(date("d/m/Y")).')';
	
	if(!jn_query($queryInsertPS1001)){
		$retorno .=' E 1001 ';
	}

}
function importaContratoAssociado($codigoAssociadoGerado,$diaVencimento){


	$queryInsertPS1002 = "insert into Ps1002(CODIGO_EMPRESA,CODIGO_ASSOCIADO,DIA_VENCIMENTO)VALUES(NULL,".aspas($codigoAssociadoGerado).",".aspas($diaVencimento).")";

	
	if(!jn_query($queryInsertPS1002)){
		$retorno .=' E 1002 ';
	}
	

}
function importaTelefoneAssociado($codigoAssociadoGerado,$codigoAssociado){

	$querycampos ="select A.NOME_CAMPO from CFGCAMPOS_SIS A where
				A.NOME_TABELA = 'PS1006' and A.FLAG_CHAVEPRIMARIA <> 'S' and A.NOME_CAMPO in(
				select B.NOME_CAMPO from CFGCAMPOS_SIS B where 
				B.NOME_TABELA = 'PS1006' and B.NOME_CAMPO <>'CODIGO_EMPRESA' and B.NOME_CAMPO <>'SEPARADOR_01'and  B.NOME_CAMPO <>'SEPARADOR_02'and  B.NOME_CAMPO <>'SEPARADOR_03'and  B.NOME_CAMPO <>'SEPARADOR_04'  and  B.NOME_CAMPO <>'CODIGO_ASSOCIADO'
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
						 select NULL,".aspas($codigoAssociadoGerado).",".$campos." FROM PS1006 where CODIGO_ASSOCIADO = ".aspas($codigoAssociado);

	if(!jn_query($queryInsertPS1006)){
		$retorno .=' E 1006 ';
	}
	

}
?>