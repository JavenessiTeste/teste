<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] =='dados'){
	
	permissaoPx('',6,true);
	
	
	jn_query('update cfgentidade_sis set descricao_entidade = (select descricao_tabela from cfgtabelas_sis where cfgtabelas_sis.nome_tabela = cfgentidade_sis.nome_entidade) '.
    ' where cfgentidade_sis.nome_entidade in (select cfgtabelas_sis.nome_tabela from cfgtabelas_sis where cfgtabelas_sis.nome_tabela = cfgentidade_sis.nome_entidade)');
	
	
	$retorno['DADOS'] = array();
	
	
	$dados = "select CODIGO_IDENTIFICACAO, COALESCE(NOME_COMPLETO,NOME_USUAL) NOME_COMPLETO, TIPO_CADASTRO from ps1100 where codigo_identificacao =" . aspas($dadosInput['codigo']);
	$resDados = jn_query($dados);
	$rowDados = jn_fetch_object($resDados);
	$retorno['NOME'] = $rowDados->CODIGO_IDENTIFICACAO.' - '.$rowDados->NOME_COMPLETO.' - '.$rowDados->TIPO_CADASTRO;
	
	$queryGrupos = 'SELECT * FROM CFGENTIDADE_SIS WHERE (nome_entidade_pai IS NULL)';
	$resGrupos = jn_query($queryGrupos);
	
	while($rowGrupos = jn_fetch_object($resGrupos))
	{

		$grupo['nomeEntidade'] 	= utf8_encode($rowGrupos->DESCRICAO_ENTIDADE);
		$grupo['expandido'] 	= true;
		$grupo['itens'] 		= array();

		$grupo['entidade']  = $rowItens->NOME_ENTIDADE;
		$grupo['leitura'] = '';
		$grupo['inclusao'] = '';
		$grupo['edicao'] = '';
		$grupo['exclusao'] = '';
		$grupo['impressao'] = '';
		$grupo['exportacao'] = '';
		
		$queryItens = 'SELECT CFGENTIDADE_SIS.*,CFGENTIDADE_SIS_PERMISS.ACESSOS_AUTORIZADOS,CFGENTIDADE_SIS_PERMISS.ACESSOS_NEGADOS FROM CFGENTIDADE_SIS
					   left join CFGENTIDADE_SIS_PERMISS on CFGENTIDADE_SIS_PERMISS.NOME_ENTIDADE = CFGENTIDADE_SIS.NOME_ENTIDADE and CFGENTIDADE_SIS_PERMISS.codigo_identificacao = '.aspas($dadosInput['codigo']). '
					   WHERE (nome_entidade_pai IS NOT NULL) and nome_entidade_pai ='.aspas($rowGrupos->NOME_ENTIDADE);
		
		$resItens = jn_query($queryItens);

		while($rowItens = jn_fetch_object($resItens))
		{
			
			$item = array();
			$item['entidade']  = $rowItens->NOME_ENTIDADE;
			$item['descricao'] = utf8_encode($rowItens->DESCRICAO_ENTIDADE);
			
			$item['leitura'] = '';
			$item['inclusao'] = '';
			$item['edicao'] = '';
			$item['exclusao'] = '';
			$item['impressao'] = '';
			$item['exportacao'] = '';
			
			//$queryPessoa = "select * from CFGENTIDADE_SIS_PERMISS  where   codigo_identificacao =" . aspas($dadosInput['codigo']).' and nome_entidade = '. aspas($rowItens->NOME_ENTIDADE);
			//$resPessoa = jn_query($queryPessoa);
			
			
			//if($rowPessoa = jn_fetch_object($resPessoa)){
				//pr($rowPessoa->ACESSOS_AUTORIZADOS);
				//exit;
				//$dadoSim = $rowPessoa->ACESSOS_AUTORIZADOS;
				$dadoSim = $rowItens->ACESSOS_AUTORIZADOS;
				$dadoSim = explode(',',$dadoSim);
				//LEI,INC,EDI,EXC,IMP,EXP

				foreach ($dadoSim as $val) 
				{
					
					if($val=='LEI')
						$item['leitura'] = 'S';
					if($val=='INC')
						$item['inclusao'] = 'S';
					if($val=='EDI')
						$item['edicao'] = 'S';
					if($val=='EXC')
						$item['exclusao'] = 'S';
					if($val=='IMP')
						$item['impressao'] = 'S';
					if($val=='EXP')
						$item['exportacao'] = 'S';
				}
				
				//$dadoNao = $rowPessoa->ACESSOS_NEGADOS;
				$dadoNao = $rowItens->ACESSOS_NEGADOS;
				$dadoNao = explode(',',$dadoNao);
				
				foreach ($dadoNao as $val) 
				{
					if($val=='LEI')
						$item['leitura'] = 'N';
					if($val=='INC')
						$item['inclusao'] = 'N';
					if($val=='EDI')
						$item['edicao'] = 'N';
					if($val=='EXC')
						$item['exclusao'] = 'N';
					if($val=='IMP')
						$item['impressao'] = 'N';
					if($val=='EXP')
						$item['exportacao'] = 'N';
				}
				
				
			//}	
			
			$grupo['itens'][] = $item;
		}

		$retorno['DADOS'][] = $grupo;
	}	

	echo json_encode($retorno);	
}

if($dadosInput['tipo'] =='salvar'){
	
	$dados = $dadosInput['dados']; 
	
	$retorno['STATUS'] = 'OK';
	$retorno['MSG'] = 'Ok, permissões do operador salvas com sucesso!';
	
	foreach ($dados as $itemDados){
		//pr($itemDados['entidade']);
		$queryPessoa = "select * from CFGENTIDADE_SIS_PERMISS  where   codigo_identificacao =" . aspas($dadosInput['codigo']).' and nome_entidade = '. aspas($itemDados['entidade']);
		$resPessoa = jn_query($queryPessoa);
		//pr($queryPessoa);	
		$achou = false;	
		if($rowPessoa = jn_fetch_object($resPessoa)){
			$achou = true;	
		}
		if($achou and $itemDados['permitido']=='' and $itemDados['naoPermitido']==''){
			//delete
			//pr('delete');
			//pr($itemDados);
			jn_query("delete from CFGENTIDADE_SIS_PERMISS where codigo_identificacao =" . aspas($dadosInput['codigo'])." and nome_entidade =".aspas($itemDados['entidade']));
		}else if($achou and ($itemDados['permitido']==$rowPessoa->ACESSOS_AUTORIZADOS and $itemDados['naoPermitido']==$rowPessoa->ACESSOS_NEGADOS)){
			//pr('nada');
			//pr($itemDados);
		}else if($achou and ($itemDados['permitido']!='' or $itemDados['naoPermitido']!='')){
			//update
			//pr('update');
			//pr($itemDados);
			jn_query("UPDATE CFGENTIDADE_SIS_PERMISS set ACESSOS_AUTORIZADOS = ".aspas($itemDados['permitido']).",ACESSOS_NEGADOS = ".aspas($itemDados['naoPermitido'])."  where codigo_identificacao =" . aspas($dadosInput['codigo'])." and nome_entidade =".aspas($itemDados['entidade']));
		}else if(!$achou and ($itemDados['permitido']!='' or $itemDados['naoPermitido']!='')){
			//insert
			//pr('insert');
			//pr($itemDados);
			jn_query("INSERT INTO CFGENTIDADE_SIS_PERMISS(ACESSOS_AUTORIZADOS,ACESSOS_NEGADOS,CODIGO_IDENTIFICACAO,NOME_ENTIDADE)VALUES(".aspas($itemDados['permitido']).",".aspas($itemDados['naoPermitido']).",".aspas($dadosInput['codigo']).",".aspas($itemDados['entidade']).")");
		}
			
		
	}
	echo json_encode($retorno);	
	

}	
if($dadosInput['tipo'] =='dadosGrupos'){
	
	permissaoPx('',6,true);
	
	jn_query('update cfgentidade_sis set descricao_entidade = (select descricao_tabela from cfgtabelas_sis where cfgtabelas_sis.nome_tabela = cfgentidade_sis.nome_entidade) '.
    ' where cfgentidade_sis.nome_entidade in (select cfgtabelas_sis.nome_tabela from cfgtabelas_sis where cfgtabelas_sis.nome_tabela = cfgentidade_sis.nome_entidade)');
	
	
	$retorno['DADOS'] = array();
	
	
	$dados = "select CODIGO_IDENTIFICACAO, COALESCE(NOME_COMPLETO,NOME_USUAL) NOME_COMPLETO, TIPO_CADASTRO from ps1100 where codigo_identificacao =" . aspas($dadosInput['codigo']);
	$resDados = jn_query($dados);
	$rowDados = jn_fetch_object($resDados);
	$retorno['NOME'] = $rowDados->CODIGO_IDENTIFICACAO.' - '.$rowDados->NOME_COMPLETO.' - '.$rowDados->TIPO_CADASTRO;
	
	$queryGrupos = 'SELECT * FROM CFGENTIDADE_SIS WHERE (nome_entidade_pai IS NULL)';
	$resGrupos = jn_query($queryGrupos);
	
	while($rowGrupos = jn_fetch_object($resGrupos)){
		$grupo['nomeEntidade'] 	= utf8_encode($rowGrupos->DESCRICAO_ENTIDADE);
		$grupo['entidade']       = $rowGrupos->NOME_ENTIDADE;
		
		$retorno['DADOS'][] = $grupo;
	}
	echo json_encode($retorno);	

}	

if($dadosInput['tipo'] =='dadosGrupoEndidades'){
	
	$retorno['DADOS'] = array();
	
		$queryItens = 'SELECT CFGENTIDADE_SIS.*,CFGENTIDADE_SIS_PERMISS.ACESSOS_AUTORIZADOS,CFGENTIDADE_SIS_PERMISS.ACESSOS_NEGADOS FROM CFGENTIDADE_SIS
					   left join CFGENTIDADE_SIS_PERMISS on CFGENTIDADE_SIS_PERMISS.NOME_ENTIDADE = CFGENTIDADE_SIS.NOME_ENTIDADE and CFGENTIDADE_SIS_PERMISS.codigo_identificacao = '.aspas($dadosInput['codigo']). '
					   WHERE (nome_entidade_pai IS NOT NULL) and nome_entidade_pai ='.aspas($dadosInput['nomeGrupo']);
		
		$resItens = jn_query($queryItens);
		while($rowItens = jn_fetch_object($resItens)){
			
			$item = array();
			$item['entidade']  = $rowItens->NOME_ENTIDADE;
			$item['descricao'] = utf8_encode($rowItens->DESCRICAO_ENTIDADE);
			
			$item['leitura'] = '';
			$item['inclusao'] = '';
			$item['edicao'] = '';
			$item['exclusao'] = '';
			$item['impressao'] = '';
			$item['exportacao'] = '';
			
			//$queryPessoa = "select * from CFGENTIDADE_SIS_PERMISS  where   codigo_identificacao =" . aspas($dadosInput['codigo']).' and nome_entidade = '. aspas($rowItens->NOME_ENTIDADE);
			//$resPessoa = jn_query($queryPessoa);
			
			
			//if($rowPessoa = jn_fetch_object($resPessoa)){
				//pr($rowPessoa->ACESSOS_AUTORIZADOS);
				//exit;
				//$dadoSim = $rowPessoa->ACESSOS_AUTORIZADOS;
				$dadoSim = $rowItens->ACESSOS_AUTORIZADOS;
				$dadoSim = explode(',',$dadoSim);
				//LEI,INC,EDI,EXC,IMP,EXP
				foreach ($dadoSim as $val) {
					
					if($val=='LEI')
						$item['leitura'] = 'S';
					if($val=='INC')
						$item['inclusao'] = 'S';
					if($val=='EDI')
						$item['edicao'] = 'S';
					if($val=='EXC')
						$item['exclusao'] = 'S';
					if($val=='IMP')
						$item['impressao'] = 'S';
					if($val=='EXP')
						$item['exportacao'] = 'S';
				}
				
				//$dadoNao = $rowPessoa->ACESSOS_NEGADOS;
				$dadoNao = $rowItens->ACESSOS_NEGADOS;
				$dadoNao = explode(',',$dadoNao);
				
				foreach ($dadoNao as $val) {
					if($val=='LEI')
						$item['leitura'] = 'N';
					if($val=='INC')
						$item['inclusao'] = 'N';
					if($val=='EDI')
						$item['edicao'] = 'N';
					if($val=='EXC')
						$item['exclusao'] = 'N';
					if($val=='IMP')
						$item['impressao'] = 'N';
					if($val=='EXP')
						$item['exportacao'] = 'N';
				}
				
			$retorno['DADOS'][]  = $item;
		}

	echo json_encode($retorno);	

}	

if($dadosInput['tipo'] =='salvarPermissoesMenu')
{
	
	$dados              = $dadosInput['dados']; 
	$retorno['STATUS']  = 'OK';
	$retorno['MSG']     = 'Ok, permissões de menu do operador salvas com sucesso!';

	jn_query('Delete From CFGPERMISSOES_MENU_NET Where Codigo_identificacao = ' . aspas($dados[0]['VALOR_01']));
	
	foreach	($dados as $key => $value)
	{

		$sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Menu', $value['CHAVE']);
		$sqlEdicao 	.= linhaJsonEdicao('Flag_Possui_Acesso', $value['VALOR']);
		$sqlEdicao 	.= linhaJsonEdicao('Codigo_identificacao', $value['VALOR_01']);

		gravaEdicao('CFGPERMISSOES_MENU_NET', $sqlEdicao, 'I', '');

	}

	echo json_encode($retorno);	
	
}	





?>