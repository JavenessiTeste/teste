<?php
require('../lib/base.php');
require('../private/autentica.php');
require('../EstruturaPrincipal/disparoEmail.php');
require('complementoSql.php');

if($dadosInput['tipo'] =='dadosBasicosPessoa'){
		
	$queryDados = 'select VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.CODIGO_TITULAR, VND1000_ON.NOME_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.CODIGO_PLANO, VND1000_ON.CODIGO_TABELA_PRECO, 
	                      VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, 
						  VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_TELEFONE_01 
				   from VND1000_ON 
				   LEFT OUTER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) 
				   where (1 = 1) ';
				   
	if (($dadosInput['codigoEmpresa'] != '') and ($dadosInput['codigoEmpresa'] != '400'))
		$queryDados.= ' and VND1000_ON.codigo_empresa = ' . aspas($dadosInput['codigoEmpresa']);

	if ($dadosInput['codigoAssociado'] == 'TODOS_TITULARES')
		$queryDados.= ' and VND1000_ON.tipo_associado = ' . aspas('T');
	else
		$queryDados.= ' and VND1000_ON.codigo_associado = ' . aspas($dadosInput['codigoAssociado']);
	
	if ($dadosInput['cpfCnpjCombinacao'] != '')
		$queryDados.= ' and VND1000_ON.numero_cpf = ' . aspas($dadosInput['cpfCnpjCombinacao']);
	
	$queryDados.= ' order by VND1000_ON.CODIGO_TITULAR, VND1000_ON.CODIGO_ASSOCIADO ';
	
	$resDados   = jn_query($queryDados);
	
	$item = Array();
	$i    = 0;

	while($rowDados = jn_fetch_object($resDados))
	{
		$item[$i]['CODIGO_ASSOCIADO'] 	= $rowDados->CODIGO_ASSOCIADO;
		$item[$i]['NOME_ASSOCIADO'] 	= jn_utf8_encode($rowDados->NOME_ASSOCIADO);
		$item[$i]['TIPO_ASSOCIADO'] 	= $rowDados->TIPO_ASSOCIADO;
		$item[$i]['ENDERECO_EXIBICAO'] 	= jn_utf8_encode($rowDados->ENDERECO);
		$item[$i]['BAIRRO'] 			= jn_utf8_encode($rowDados->BAIRRO);
		$item[$i]['CIDADE'] 			= jn_utf8_encode($rowDados->CIDADE);
		$item[$i]['CEP'] 				= jn_utf8_encode($rowDados->CEP);
		$item[$i]['ESTADO'] 			= jn_utf8_encode($rowDados->ESTADO);
		$item[$i]['EMAIL'] 				= jn_utf8_encode($rowDados->ENDERECO_EMAIL);
		$item[$i]['TELEFONE'] 			= jn_utf8_encode($rowDados->NUMERO_TELEFONE_01);
		$item[$i]['VALOR_PREVISAO']     = retornaValorPrevisao($rowDados->CODIGO_ASSOCIADO);
		$item[$i]['EXIBE_VALOR_ADIC']   = retornaSeExibeValorAdicional($rowDados->CODIGO_PLANO, $rowDados->CODIGO_TABELA_PRECO, $rowDados->TIPO_ASSOCIADO );

		$i++;
	}	

	foreach ($item as $value)
	{
		$retorno[]=$value;
	} 
	
	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='dependentesCadastradosConcluidos'){
		
	$queryDados = 'select VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.CODIGO_TITULAR, VND1000_ON.NOME_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.CODIGO_PLANO, VND1000_ON.CODIGO_TABELA_PRECO 
				   from VND1000_ON 
		           where VND1000_ON.tipo_Associado = "D" and VND1000_ON.codigo_titular = ' . aspas($dadosInput['codigoTitular']) . 
				  'order by VND1000_ON.CODIGO_ASSOCIADO';
	
	$resDados   = jn_query($queryDados);
	
	$item = Array();
	$i    = 0;

	while($rowDados = jn_fetch_object($resDados))
	{
		$item[$i]['CODIGO_ASSOCIADO'] 	= $rowDados->CODIGO_ASSOCIADO;
		$item[$i]['NOME_ASSOCIADO'] 	= jn_utf8_encode($rowDados->NOME_ASSOCIADO);
		$item[$i]['TIPO_ASSOCIADO'] 	= $rowDados->TIPO_ASSOCIADO;
		$item[$i]['VALOR_PREVISAO']     = retornaValorPrevisao($rowDados->CODIGO_ASSOCIADO);
		$item[$i]['EXIBE_VALOR_ADIC']   = retornaSeExibeValorAdicional($rowDados->CODIGO_PLANO, $rowDados->CODIGO_TABELA_PRECO, $rowDados->TIPO_ASSOCIADO);

		$i++;
	}	

	foreach ($item as $value)
	{
		$retorno[]=$value;
	} 
	
	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='dadosEmpresa'){
		
	$queryDados = 'select VND1010_ON.CODIGO_EMPRESA, VND1010_ON.NOME_EMPRESA, 
	                      VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, 
						  VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_TELEFONE_01 
				   from VND1010_ON 
				   LEFT OUTER JOIN VND1001_ON ON (VND1010_ON.CODIGO_EMPRESA = VND1001_ON.CODIGO_EMPRESA) 
				   where VND1010_ON.codigo_EMPRESA = ' . aspas($dadosInput['cod']);
	
	if ($dadosInput['cpfCnpjCombinacao'] != '')
		$queryDados.= ' and VND1010_ON.numero_cnpj = ' . aspas(formatCnpjCpf($dadosInput['cpfCnpjCombinacao']));
	
	$resDados   = jn_query($queryDados);
	$rowDados   = jn_fetch_object($resDados);

	$item['CODIGO_EMPRESA'] 	= $rowDados->CODIGO_EMPRESA;
	$item['NOME_EMPRESA'] 	    = jn_utf8_encode($rowDados->NOME_EMPRESA);
	$item['ENDERECO_EXIBICAO'] 	= jn_utf8_encode($rowDados->ENDERECO);
	$item['BAIRRO'] 			= jn_utf8_encode($rowDados->BAIRRO);
	$item['CIDADE'] 			= jn_utf8_encode($rowDados->CIDADE);
	$item['CEP'] 				= jn_utf8_encode($rowDados->CEP);
	$item['ESTADO'] 			= jn_utf8_encode($rowDados->ESTADO);
	$item['EMAIL'] 				= jn_utf8_encode($rowDados->ENDERECO_EMAIL);
	$item['TELEFONE'] 			= jn_utf8_encode($rowDados->NUMERO_TELEFONE_01);

	$retorno['DADOS'][]         = $item;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='configuracoesPlanosVenda'){
		
	/*ps1030->codigo_tipo_cobertura : 01 - Ambulatorial (A),02 - Hospitalar (H),03 - Odontológico (O),04 - Obstetrícia (OB),05 - A + H,06 - A + H + O,07 - A + H + OB,08 - A + H + O + OB,09 - A + O,10 - H + O, 11 - H + OB,12 - H + OB + O,13 - Outras
	  ps1030->codigo_tipo_acomodacao: 1 - Individual,2 - Coletivo,3 - Sem Acomodacao
	  ps1030->tipo_contratacao_ans  : 1 - INDIVIDUALFAMILIAR, 3 - COLETIVOEMPRESARIAL, 4 - COLETIVOADESAO, 9 - OUTROS*/
		
	$criterio   = ' (1=1) ';
	
	if ($dadosInput['tpcontratacao'] != '')
	{
		if ($dadosInput['tpcontratacao'] == '2')
			$dadosInput['tpcontratacao'] = '3';
		else if ($dadosInput['tpcontratacao'] == '3')
			$dadosInput['tpcontratacao'] = '4';
		
		$criterio .= ' and PS1030.TIPO_CONTRATACAO_ANS = ' . aspas($dadosInput['tpcontratacao']) . ' ';
	}
	
	if ($dadosInput['tipoPlanoSaudeOdonto'] != '')
	{
		if ($dadosInput['tipoPlanoSaudeOdonto'] == '1')
		   $criterio .= ' and (PS1030.CODIGO_TIPO_COBERTURA in ("01","02","04","05","07","11","12","13") or ';
		else if ($dadosInput['tipoPlanoSaudeOdonto'] == '2')
		   $criterio .= ' and (PS1030.CODIGO_TIPO_COBERTURA in ("03") or ';
		else if ($dadosInput['tipoPlanoSaudeOdonto'] == '3')
		   $criterio .= ' and (PS1030.CODIGO_TIPO_COBERTURA in ("01","02","03","04","05","06","07","08","09","10","11","12","13") or ';
	    else 
		   $criterio .= ' and (';	
		   
		$criterio .= '  coalesce(PS1030.CODIGO_TIPO_COBERTURA,"") = "") ';   
	}	
	
	if ($dadosInput['idEntidade'] != '')
	{
		if(retornaValorConfiguracao('FORCA_USO_GRUPO_PESSOAS_AUTOC') == 'SIM')
			$criterio .= ' and VND1030CONFIG_ON.CODIGO_GRUPO_PESSOAS_AUTOC	= ' . aspas($dadosInput['idEntidade']) . ' ';
		else
			$criterio .= ' and VND1030CONFIG_ON.CODIGO_GRUPO_CONTRATO_AUTOC	= ' . aspas($dadosInput['idEntidade']) . ' ';
	}

	if (($dadosInput['tamanhoEmpresaMin'] != '') and ($dadosInput['tamanhoEmpresMax'] != ''))
	{
		$criterio .= ' and VND1030CONFIG_ON.TAMANHO_EMPRESA_MIN_AUTOC <= ' . aspas($dadosInput['tamanhoEmpresaMin']) . ' ';
		$criterio .= ' and VND1030CONFIG_ON.TAMANHO_EMPRESA_MAX_AUTOC >= ' . aspas($dadosInput['tamanhoEmpresMax']) . ' ';
	}
	
	$criterio .= ' and (VND1030CONFIG_ON.TITULO_PLANO_AUTOC Is not null or VND1030CONFIG_ON.DESCRICAO_PLANO_AUTOC_HTML is not null) ';
	
	$queryDados = 'select VND1030CONFIG_ON.*, PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_COBERTURA,  PS1030.CODIGO_TIPO_ACOMODACAO, PS1030.TIPO_CONTRATACAO_ANS  
				   from PS1030   
	               Inner Join VND1030CONFIG_ON On (Ps1030.Codigo_Plano = VND1030CONFIG_ON.Codigo_Plano) ' . 
				   'where ' . $criterio;
	
	$resDados   = jn_query($queryDados);
  
    $dadosInput['jsonIdadesQuantidades'] = str_replace('\\','',$dadosInput['jsonIdadesQuantidades']);
	
	//pr('$dadosInput[jsonIdadesQuantidades]->'.$dadosInput['jsonIdadesQuantidades']);
  
	//$dadosInput['jsonIdadesQuantidades'] = '{"idadesQuantidades":[{"idadeMinima":24,"idadeMaxima":28,"quantidadePessoas":3},{"idadeMinima":29,"idadeMaxima":33,"quantidadePessoas":3}]}';
	
	$jsonIdadesQuantidades  = json_decode($dadosInput['jsonIdadesQuantidades']);

	//pr($jsonIdadesQuantidades);

	$listaIdadesQuantidades = $jsonIdadesQuantidades->idadesQuantidades;

	//print_r($listaIdadesQuantidades);
	
	while ($rowDados   = jn_fetch_object($resDados))
	{
		
		//Lembrar de colocar o jn_utf8_encode sempre que houve uma string, por conta dos acentos. Se não dá erro no JSON 
		$item['CODIGO_PLANO'] 					= $rowDados->CODIGO_PLANO;
		$item['CODIGOS_MODELO_CONTRATO']		= $rowDados->CODIGOS_MODELO_CONTRATO;
		$item['TABELA_PRECO_AUTOC'] 			= jn_utf8_encode($rowDados->TABELA_PRECO_AUTOC);
		$item['TITULO_PLANO_AUTOC'] 			= jn_utf8_encode($rowDados->TITULO_PLANO_AUTOC);
		if ($item['TITULO_PLANO_AUTOC'] == '')
    		$item['TITULO_PLANO_AUTOC'] = jn_utf8_encode($rowDados->NOME_PLANO_FAMILIARES);

		$item['DESCRICAO_PLANO_AUTOC_HTML'] 	= jn_utf8_encode($rowDados->DESCRICAO_PLANO_AUTOC_HTML);
		if ($item['DESCRICAO_PLANO_AUTOC_HTML'] == '')
    		$item['DESCRICAO_PLANO_AUTOC_HTML'] = jn_utf8_encode($rowDados->NOME_PLANO_FAMILIARES);
			
		$item['TAMANHO_EMPRESA_MIN_AUTOC'] 		= $rowDados->TAMANHO_EMPRESA_MIN_AUTOC;
		$item['LINK_PAGINA_PRODUTO'] 			= jn_utf8_encode($rowDados->LINK_PAGINA_PRODUTO);
		$item['LINK_PORTIFOLIO_AUTOC'] 			= jn_utf8_encode($rowDados->LINK_PORTIFOLIO_AUTOC);
		$item['TAMANHO_EMPRESA_MAX_AUTOC'] 		= $rowDados->TAMANHO_EMPRESA_MAX_AUTOC;
		
		if(retornaValorConfiguracao('FORCA_USO_GRUPO_PESSOAS_AUTOC') == 'SIM')
			$item['CODIGO_GRUPO_CONTRATO_AUTOC'] 	= $rowDados->CODIGO_GRUPO_PESSOAS_AUTOC;
		else
			$item['CODIGO_GRUPO_CONTRATO_AUTOC'] 	= $rowDados->CODIGO_GRUPO_CONTRATO_AUTOC;
		
		$item['CODIGO_TIPO_COBERTURA']	   		= $rowDados->CODIGO_TIPO_COBERTURA;
		$item['CODIGO_TIPO_ACOMODACAO']			= $rowDados->CODIGO_TIPO_ACOMODACAO;
		$item['TIPO_CONTRATACAO_ANS']			= $rowDados->TIPO_CONTRATACAO_ANS;
		$item['QUANTIDADE_MAXIMA_DEPENDENTES']	= $rowDados->QUANTIDADE_MAXIMA_DEPENDENTES;
		
		//
		
		$criterioTabelas = '';
		
		if ($dadosInput['idEntidade'] != '')
		{
			if(retornaValorConfiguracao('FORCA_USO_GRUPO_PESSOAS_AUTOC') == 'SIM')
			    $criterioTabelas = ' and VND1030CONFIG_ON.CODIGO_GRUPO_PESSOAS_AUTOC = ' . aspas($dadosInput['idEntidade']);
			elseif(retornaValorConfiguracao('VALIDAR_GRUPO_CONTRATO_PS1032') == 'SIM')
			    $criterioTabelas = ' and VND1030CONFIG_ON.CODIGO_GRUPO_CONTRATO_AUTOC = ' . aspas($dadosInput['idEntidade']) . ' and (PS1032.CODIGO_GRUPO_CONTRATO IS NULL OR (PS1032.CODIGO_GRUPO_CONTRATO = ' . aspas($dadosInput['idEntidade']) . ')) ';
			else
				$criterioTabelas = ' and VND1030CONFIG_ON.CODIGO_GRUPO_CONTRATO_AUTOC = ' . aspas($dadosInput['idEntidade']) . ' and PS1032.CODIGO_GRUPO_CONTRATO = ' . aspas($dadosInput['idEntidade']) . ' ';
		}
		
		$queryTabelas = 'select VND1030CONFIG_ON.CODIGO_GRUPO_CONTRATO_AUTOC, VND1030CONFIG_ON.CODIGO_GRUPO_PESSOAS_AUTOC, PS1032.* 
					   from VND1030CONFIG_ON 
					   INNER JOIN PS1032 ON (VND1030CONFIG_ON.TABELA_PRECO_AUTOC = PS1032.CODIGO_TABELA_PRECO) AND 
											(VND1030CONFIG_ON.CODIGO_PLANO = PS1032.CODIGO_PLANO) ' .
					  'WHERE (VND1030CONFIG_ON.CODIGO_PLANO = ' . numSql($rowDados->CODIGO_PLANO) . ') ' . $criterioTabelas . 
					  ' ORDER BY IDADE_MINIMA ';	

		$resTabelas   		   = jn_query($queryTabelas);
		$valorTotal			   = 0;

		//Não use o echo, use o pr porque ele mostra o conteúdo do array e fica muito melhor para ver. Não concatene com uma string se não ele não mostrará o array
		//pr('listaIdadesQuantidades->' . $listaIdadesQuantidades);
		//pr('<br>');
		//echo 'ccc';

		while ($rowTabelas  = jn_fetch_object($resTabelas))
		{
		
			//pr('rowTabelas->' . $rowTabelas);
			
			foreach ( $listaIdadesQuantidades as $idadesQuantidades )
			{
			    //echo "nome: $e->nome - idade: $e->idade - sexo: $e->sexo<br>"; 
				
				//print_r('idadesQuantidades' . $idadesQuantidades->idadeMinima . ' aa ' . $idadesQuantidades->idadeMaxima);
				//print_r('rowTabelas' . $rowTabelas->IDADE_MINIMA . ' aa ' . $rowTabelas->IDADE_MAXIMA);
				
				if (($idadesQuantidades->idadeMinima >= $rowTabelas->IDADE_MINIMA) and 
					($idadesQuantidades->idadeMaxima <= $rowTabelas->IDADE_MAXIMA))
				{
					$valorPlano = $rowTabelas->VALOR_PLANO;

					if(retornaValorConfiguracao('SOMA_VL_ADICIONAL_NA_SIMULACAO') == 'SIM')
					{
						$queryPerc  = ' SELECT VALOR_SUGERIDO, TIPO_CALCULO FROM PS1024 ';
						$queryPerc .= ' WHERE 1 = 1';
			
						if(retornaValorConfiguracao('UTILIZA_PLANOS_PS1024') == 'SIM'){
							$queryPerc .= ' AND PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $rowTabelas->CODIGO_PLANO . '%');		
						}

						if(retornaValorConfiguracao('FORCA_USO_GRUPO_PESSOAS_AUTOC') == 'SIM')
							$queryPerc .= ' AND PS1024.CODIGO_GRUPO_PESSOAS = ' . aspas($rowTabelas->CODIGO_GRUPO_PESSOAS_AUTOC);		
						else
							$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowTabelas->CODIGO_GRUPO_CONTRATO_AUTOC);		
						
						$resPerc    = jn_query($queryPerc);
						$rowPerc    = jn_fetch_object($resPerc);
						$percentual = $rowPerc->VALOR_SUGERIDO;						
						
						if($percentual > 0)
						{
							if ($rowPerc->TIPO_CALCULO == 'V')
							{
								$valorPlano = ($rowTabelas->VALOR_PLANO + $rowPerc->VALOR_SUGERIDO);
							}
							else
							{
								$calculo = (ceil($rowTabelas->VALOR_PLANO * $percentual) / 100);				
								$valorPlano = ($rowTabelas->VALOR_PLANO + $calculo);
							}
						}
					}

					$valorTotal = $valorTotal + ($idadesQuantidades->quantidadePessoas * $valorPlano);
					
				}
			}
		}

		$item['VALOR_TOTAL']    = toMoeda($valorTotal);
		
		//Ao passar um valor da forma abaixo, o PHP faz o push do array na variável inserindo um elemento a mais.
		$retorno['DADOS'][]     = $item;

	}

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='linksArquivosExternosPreVenda'){
		
	$queryDados = 'select CODIGO_MODELO, TITULO_MODELO, HIPERLINK_MODELO, FLAG_EXIGIR_ACEITE, FLAG_EXIBIR_ANTES_CADASTRO ' . 
				   $dadosInput['CRITERIO_VND1030MODELOS_ON'];
	
	$resDados   = jn_query($queryDados);
	$rowDados   = jn_fetch_object($resDados);

	$item['CODIGO_MODELO'] 					= $rowDados->CODIGO_MODELO;
	$item['TITULO_MODELO'] 					= jn_utf8_encode($rowDados->TITULO_MODELO);
	$item['HIPERLINK_MODELO'] 				= jn_utf8_encode($rowDados->HIPERLINK_MODELO);
	$item['FLAG_EXIGIR_ACEITE'] 			= $rowDados->FLAG_EXIGIR_ACEITE;
	$item['FLAG_EXIBIR_ANTES_CADASTRO'] 	= $rowDados->FLAG_EXIBIR_ANTES_CADASTRO;

	$retorno['DADOS'][]         = $item;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='informacoesEntidades'){

	
	if(retornaValorConfiguracao('FORCA_USO_GRUPO_PESSOAS_AUTOC') == 'SIM')
	{
		$queryDados = 'select PS1014.CODIGO_GRUPO_PESSOAS CODIGO_GRUPO_CONTRATO, COALESCE(DESCRICAO_ENTIDADE_AUTOC,NOME_GRUPO_PESSOAS) DESCRICAO_GRUPO_CONTRATO, 
					   PS1014.DESCRICAO_PROFISSOES_AUTOC, NOME_GRUPO_PESSOAS NOME_OPERADORA, PS1014.FLAG_EXIBIR_VND_AUTO FROM PS1014 
					   WHERE PS1014.CODIGO_GRUPO_PESSOAS IN (SELECT CODIGO_GRUPO_PESSOAS FROM VND1030CONFIG_ON)
					   ORDER BY COALESCE(DESCRICAO_ENTIDADE_AUTOC,NOME_GRUPO_PESSOAS) '; 
	}
	else
	{
		$queryDados = 'select CODIGO_GRUPO_CONTRATO, COALESCE(DESCRICAO_ENTIDADE_AUTOC,DESCRICAO_GRUPO_CONTRATO) DESCRICAO_GRUPO_CONTRATO, 
					   DESCRICAO_PROFISSOES_AUTOC, NOME_OPERADORA, FLAG_EXIBIR_VND_AUTO FROM ESP0002 
					   WHERE CODIGO_GRUPO_CONTRATO IN (SELECT CODIGO_GRUPO_CONTRATO_AUTOC FROM VND1030CONFIG_ON)
					   ORDER BY COALESCE(DESCRICAO_ENTIDADE_AUTOC,DESCRICAO_GRUPO_CONTRATO) '; 
	}
	
	$resDados   = jn_query($queryDados);

	$item = Array();
	$i    = 0;

	while($rowDados = jn_fetch_object($resDados))
	{
		$item[$i]['CODIGO_GRUPO_CONTRATO'] 					= $rowDados->CODIGO_GRUPO_CONTRATO;
		$item[$i]['DESCRICAO_GRUPO_CONTRATO'] 				= jn_utf8_encode($rowDados->DESCRICAO_GRUPO_CONTRATO);
		$item[$i]['DESCRICAO_PROFISSOES_AUTOC'] 			= jn_utf8_encode($rowDados->DESCRICAO_PROFISSOES_AUTOC);
		$item[$i]['NOME_OPERADORA'] 			            = jn_utf8_encode($rowDados->NOME_OPERADORA);
		$item[$i]['FLAG_EXIBIR_VND_AUTO'] 					= $rowDados->FLAG_EXIBIR_VND_AUTO;
	
		$i++;
	}	

	foreach ($item as $value)
	{
		$retorno[]=$value;
	} 

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='retornaValorConfiguracao')
{
	if(retornaValorConfiguracao($dadosInput['idConfiguracao']))
		$retorno['DADOS'][] = jn_utf8_encode(retornaValorConfiguracao($dadosInput['idConfiguracao']));
    else
        $retorno['DADOS'][] = '';

	echo json_encode($retorno);
	
}
else if($dadosInput['tipo'] =='salvaLead'){
		
	$queryDados = 'select ENDERECO_EMAIL, NUMERO_TELEFONE FROM VND1000_ON_LEAD ' .
				  'WHERE (ENDERECO_EMAIL = ' . aspas($dadosInput['email']) . ' or ' .  
				  '       NUMERO_TELEFONE = ' . aspas($dadosInput['telefone']) . ') and ' .  
				  '       DATA_CADASTRAMENTO = ' . dataToSql( date("d/m/Y"));
	
	$resDados   = jn_query($queryDados);

	if ($rowDados=jn_fetch_object($resDados))
	{
	   	$item['retorno'] 					= 'JA_EXISTENTE';
	}
	else
	{
	      $queryDados = 'INSERT INTO VND1000_ON_LEAD(NOME_PESSOA, ENDERECO_EMAIL, NUMERO_TELEFONE, NUMERO_CEP, NUMERO_CPF, CODIGO_VENDEDOR, DATA_CADASTRAMENTO, HORA_CADASTRAMENTO, ENDERECO_IP) ' . 
		                                  'values(' . aspas(utf8_decode($dadosInput['nome'])) . ', ' . 
		                                              aspas($dadosInput['email']) . ', ' . 
		                                              aspas($dadosInput['telefone']) . ', ' . 
		                                              aspas($dadosInput['cep']) . ', ' . 
													  aspas($dadosInput['cpf']) . ', ' . 
		                                              aspas($dadosInput['codigoVendedor']) . ', ' . 
		                                              dataToSql( date("d/m/Y")) . ', ' . 
		                                              aspas('00:00') . ', ' . 
		                                              aspas('000.000.000.000') . ')'; 
													  
    	jn_query($queryDados);
		$item['retorno'] 					= 'OK';
													  
	}
	
	$retorno['DADOS'][]         = $item;

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='excluirLead'){
		
	$queryDados = 'DELETE FROM VND1000_ON_LEAD ' .
				  'WHERE (ENDERECO_EMAIL = ' . aspas($dadosInput['email']) . ' and ' .  
				  '       NUMERO_TELEFONE = ' . aspas($dadosInput['telefone']) . ') and ' .  
				  '       DATA_CADASTRAMENTO = ' . dataToSql( date("d/m/Y"));
	
	jn_query($queryDados);

    $retorno['DADOS'][] = 'OK';

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='excluirDependente')
{

    jn_query("DELETE FROM VND1000_ON WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['codigo']));

    $retorno['DADOS'][] = 'EXCLUIDO';

	echo json_encode($retorno);
	
}
else if($dadosInput['tipo'] =='forma_pagto_valor_total')
{

	if (($dadosInput['codigoEmpresa'] != '') && ($dadosInput['codigoEmpresa'] != '400'))
	{
		$criterio = ' where VND1000_ON.codigo_empresa = ' . numSql($dadosInput['codigoEmpresa']);
	}
	else
	{
		$criterio = ' where VND1000_ON.codigo_titular = ' . aspas($dadosInput['codigoTitular']);
	}
		
	$queryDados = 'select VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.CODIGO_TITULAR, VND1000_ON.NOME_ASSOCIADO, VND1000_ON.TIPO_ASSOCIADO, VND1000_ON.CODIGO_PLANO, VND1000_ON.CODIGO_TABELA_PRECO, CFGEMPRESA.CODIGO_SMART  
				   from VND1000_ON 
				   inner join cfgempresa on (1 = 1) ' . 
		           $criterio . 
				  'order by VND1000_ON.CODIGO_ASSOCIADO';
	
	$resDados   = jn_query($queryDados);
	$valorTotal = 0;
	$qtPessoas  = 0;
	
	while($rowDados = jn_fetch_object($resDados))
	{
		$valorBeneficiario = retornaValorPrevisao($rowDados->CODIGO_ASSOCIADO);
		$codigoSmart       = $rowDados->CODIGO_SMART;
		
		$queryVlAdicional = 'SELECT COALESCE(VALOR_FATOR,0) VALOR_FATOR, TIPO_CALCULO, COALESCE(QUANTIDADE_EVENTOS,1) QUANTIDADE_EVENTOS FROM VND1003_ON  
    					     where codigo_associado = ' . aspas($rowDados->CODIGO_ASSOCIADO);
		$resVlAdicional   = jn_query($queryVlAdicional);
		
		$valorAdicional   = 0;

		while($rowVlAdicional = jn_fetch_object($resVlAdicional))
		{
			if ($rowVlAdicional->TIPO_CALCULO == 'F') 
			   $valorAdicional   = $valorAdicional + (($valorBeneficiario * $rowVlAdicional->VALOR_FATOR) * $rowVlAdicional->QUANTIDADE_EVENTOS);
			else
			   $valorAdicional   = $valorAdicional + ($rowVlAdicional->VALOR_FATOR * $rowVlAdicional->QUANTIDADE_EVENTOS);
		}
		
		$valorTotal       = $valorTotal + ($valorBeneficiario + $valorAdicional);
		$qtPessoas        = $qtPessoas + 1;
		
	}	

	$item = Array();

	if (($codigoSmart == '3397111') or ($codigoSmart == '4200') or ($codigoSmart == '4285')) // PCM, banco de testes, Sempre Bem e Propulsão 4200
	{
		$valorTotalCarteirinhas = 0;
		$valorTotalCarteirinhas = ($qtPessoas * 10);
		
		$item[0]['ID_PAGAMENTO']                    = 'CART_CRED_MES';
		$item[0]['TITULO_FORMA_PAGAMENTO']  	    = 'Cartão de crédito';
		$item[0]['SUB_TITULO_FORMA_PAGAMENTO']      = 'Pagamento no cartão de crédito e sem carência';
		$item[0]['VALOR_EXIBICAO']			        = number_format($valorTotal, 2, ',', '.');
		$item[0]['TIPO_PAGAMENTO']			        = 'Pagar no cartão de crédito';
		$item[0]['LINK_MAIS_INFORMACOES']	        = 'http://www.ans.gov.br/images/stories/prestadores/contrato/nota_tecnica_45.pdf';
		$item[0]['TIPO_PERIODO']			        = 'Mês';
		$item[0]['PERGUNTA_CONFIRMACAO']            = '';

		if($dadosInput['codigoEmpresa'] == '400' && $codigoSmart != '4285'){
			$item[1]['ID_PAGAMENTO']                    = 'DEB_CONTA_MES';
			$item[1]['TITULO_FORMA_PAGAMENTO']  	    = 'Débito em conta';
			$item[1]['SUB_TITULO_FORMA_PAGAMENTO']      = 'Débito em conta Caixa ou Sicob, com carência';
			$item[1]['VALOR_EXIBICAO']			        = number_format($valorTotal + $valorTotalCarteirinhas, 2, ',', '.');
			$item[1]['TIPO_PAGAMENTO']			        = '';
			$item[1]['LINK_MAIS_INFORMACOES']	        = 'http://www.ans.gov.br/images/stories/prestadores/contrato/nota_tecnica_45.pdf';
			$item[1]['TIPO_PERIODO']			        = 'Mês';
			$item[1]['PERGUNTA_CONFIRMACAO']            = 'Nesta modalidade, há carências a cumprir. Clique em "SIM" para confirmar que você tem ciência desta informação';
		}
		
		if($dadosInput['codigoEmpresa'] != '400' || $codigoSmart == '4285'){
			$item[2]['ID_PAGAMENTO']                    = 'BOLETO_MES';
			$item[2]['TITULO_FORMA_PAGAMENTO']  	    = 'Pagamento no boleto mensal';
			$item[2]['SUB_TITULO_FORMA_PAGAMENTO']      = '';
			$item[2]['VALOR_EXIBICAO']			        = number_format($valorTotal, 2, ',', '.');
			$item[2]['TIPO_PAGAMENTO']			        = 'Pagar no boleto mensal';
			$item[2]['LINK_MAIS_INFORMACOES']	        = 'http://www.ans.gov.br/images/stories/prestadores/contrato/nota_tecnica_45.pdf';
			$item[2]['TIPO_PERIODO']			        = 'Mês';
			$item[2]['PERGUNTA_CONFIRMACAO']            = '';
		}
	}
	else if(retornaValorConfiguracao('METODO_PAGAMENTO_AUTOC') == 'ASSINATURA_CARTAO_CREDITO'){
		$item[0]['ID_PAGAMENTO']                    = 'CART_CRED_ASSINATURA';
		$item[0]['TITULO_FORMA_PAGAMENTO']  	    = 'Cartão de crédito';
		$item[0]['SUB_TITULO_FORMA_PAGAMENTO']      = 'Assinatura no cartão de crédito';
		$item[0]['VALOR_EXIBICAO']			        = number_format($valorTotal, 2, ',', '.');
		$item[0]['TIPO_PAGAMENTO']			        = 'Assinar no cartão de crédito';
		$item[0]['LINK_MAIS_INFORMACOES']	        = '';
		$item[0]['TIPO_PERIODO']			        = 'Mês';
		$item[0]['PERGUNTA_CONFIRMACAO']            = '';
	}
	else // Padrão -> Neste caso é o pagamento do boleto mensal, nem precisa apresentar para o cliente
	{
		$item[0]['ID_PAGAMENTO']                    = 'BOLETO_MES';
		$item[0]['TITULO_FORMA_PAGAMENTO']  	    = 'PADRAO';
		$item[0]['SUB_TITULO_FORMA_PAGAMENTO']      = 'PADRAO';
		$item[0]['VALOR_EXIBICAO']			        = number_format($valorTotal, 2, ',', '.');
		$item[0]['TIPO_PAGAMENTO']			        = 'PADRAO';
		$item[0]['LINK_MAIS_INFORMACOES']	        = '';
		$item[0]['TIPO_PERIODO']			        = 'Mês';
		$item[0]['PERGUNTA_CONFIRMACAO']            = '';
	}
	
	foreach ($item as $value)
	{
		$retorno[] = $value;
	} 

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='dispara_email_simulacao')
{

	$assunto    = 'Cotacao de plano de saude';
	
	$queryDados = 'select VND1030CONFIG_ON.* , PS1030.NOME_PLANO_FAMILIARES, PS1030.CODIGO_TIPO_COBERTURA,  PS1030.CODIGO_TIPO_ACOMODACAO, PS1030.TIPO_CONTRATACAO_ANS  
				   from PS1030   
	               Inner Join VND1030CONFIG_ON On (Ps1030.Codigo_Plano = VND1030CONFIG_ON.Codigo_Plano) ' . 
				   'where PS1030.CODIGO_PLANO = ' . $dadosInput['codigoPlano'];
	
	$resDados     = jn_query($queryDados);
	$corpoEmail   = '';

	if ($rowDados = jn_fetch_object($resDados))
	{
		$corpoEmail = $rowDados->TEXTO_EMAIL_AUTOC;
		
		$corpoEmail = str_replace('__**__NOME_ASSOCIADO__**__', $dadosInput['nomePessoa'], $corpoEmail);
		$corpoEmail = str_replace('__**__QUANTIDADE_PESSOAS__**__', $dadosInput['quantidadePessoas'] , $corpoEmail);
		$corpoEmail = str_replace('__**__VALOR_SIMULACAO__**__', $dadosInput['valorSimulacao'] , $corpoEmail);
	}

	//print_r('aaaaaaaaaaaa ' . $dadosInput['enderecoEmail']);
	
	disparaEmail($dadosInput['enderecoEmail'], $assunto, $corpoEmail);

}
else if($dadosInput['tipo'] =='validaEnderecoAssocEmp')
{
	
	$queryEmp = 'SELECT FLAG_CADASTRA_ENDER_FUNC FROM VND1010_ON WHERE CODIGO_EMPRESA = ' . aspas($dadosInput['codigoEmpresa']);
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);
	
    $retorno['FLAG_CADASTRA_ENDER_FUNC'] = $rowEmp->FLAG_CADASTRA_ENDER_FUNC;

	echo json_encode($retorno);
	
}


function retornaValorPrevisao($codigoAssociado)
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


function formatCnpjCpf($value)
{
  $cnpj_cpf = preg_replace("/\D/", '', $value);
  
  if (strlen($cnpj_cpf) === 11) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
  } 
  
  return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
}



function retornaSeExibeValorAdicional($codigoPlano, $codigoTabelaPreco, $tipoAssociado){
	
	$queryDados = 'Select CODIGOS_EVENTO_OFERECER_TIT, CODIGOS_EVENTO_OFERECER_DEP  
				   FROM VND1030CONFIG_ON
				   WHERE VND1030CONFIG_ON.CODIGO_PLANO = ' . aspas($codigoPlano);

	if ($codigoTabelaPreco != '')
		$queryDados .= ' and TABELA_PRECO_AUTOC = ' . aspas($codigoTabelaPreco);  

	$resDados   = jn_query($queryDados);
	$rowDados   = jn_fetch_object($resDados);
	
	if (($tipoAssociado = 'T') and ($rowDados->CODIGOS_EVENTO_OFERECER_TIT != ''))
	    return 'S';
	else if (($tipoAssociado = 'D') and ($rowDados->CODIGOS_EVENTO_OFERECER_DEP != ''))
	    return 'S';
	else 
		return 'N';
	
}

