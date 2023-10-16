<?php
require('../lib/base.php');

require('../private/autentica.php');
require('../EstruturaEspecifica/paginaDepoisSalvar.php');
require('../EstruturaEspecifica/ignoraSubProcesso.php');
require('../EstruturaEspecifica/antesMostrarCampos.php');



if($dadosInput['tipo'] =='dadosSubProcesso'){
		$querySubProcesso = 'SELECT 
							NOME_TABELA_PRINCIPAL,CAMPO_LIGACAO_PRINCIPAL_01,CAMPO_LIGACAO_PRINCIPAL_01,
							NOME_TABELA_FILHA,CAMPO_LIGACAO_FILHA_01,CAMPO_LIGACAO_FILHA_02,
							TIPO_RELACAO,FLAG_ABRE_SEQUENCIA_INCLUS
						FROM cfgtabelas_subprocessos_cd where numero_registro ='.aspas($dadosInput['dados']['registro']);
		
		$resSubProcesso = jn_query($querySubProcesso);
		
		if($rowSubProcesso = jn_fetch_object($resSubProcesso)){
			$retorno['RELACAO']   = $rowSubProcesso->TIPO_RELACAO;
			if(ignoraSubProcesso($dadosInput,$rowSubProcesso->NOME_TABELA_FILHA)){
				$retorno['SEQUENCIA'] = 'N';				
			}else{
				$retorno['SEQUENCIA'] = $rowSubProcesso->FLAG_ABRE_SEQUENCIA_INCLUS;
			}
			
			$auxInner = "";
			
			if($rowSubProcesso->CAMPO_LIGACAO_FILHA_01 == 'CODIGO_LIGACAO_FORMULARIO')
			{
				$auxInner  =  "cast(tabPrincipal.".strtolower($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01)." as varchar(15))=tabFilha.".strtolower($rowSubProcesso->CAMPO_LIGACAO_FILHA_01);
			}
			else if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01 != ''){
				$auxInner  =  "tabPrincipal.".strtolower($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01)."=tabFilha.".strtolower($rowSubProcesso->CAMPO_LIGACAO_FILHA_01);
			}

			if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02 != ''){
				$auxInner .=  ' and tabPrincipal.'.strtolower($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02)."=tabFilha.".strtolower($rowSubProcesso->CAMPO_LIGACAO_FILHA_02);
			}
			
			$queryQteRegistro = "Select count(*) REGISTROS from ".strtolower($rowSubProcesso->NOME_TABELA_PRINCIPAL)." tabPrincipal
								 inner join ".strtolower($rowSubProcesso->NOME_TABELA_FILHA)." tabFilha 
								 on $auxInner where tabPrincipal.".strtolower($dadosInput['dados']['nomeChave'])." = ".aspas($dadosInput['dados']['chave']);
								 //strtolower($rowSubProcesso->NOME_TABELA_PRINCIPAL) .".".strtolower($dadosInput['dados']['nomeChave'])." = ".aspas($dadosInput['dados']['chave']);
			
			$resQteRegistro = jn_query($queryQteRegistro);
			
			if($rowQteRegistro = jn_fetch_object($resQteRegistro))
			{
				$retorno['REGISTROS'] = $rowQteRegistro->REGISTROS;
			}
			
			$sqlNomeChave = "SELECT NOME_CAMPO FROM  cfgcampos_sis_cd WHERE nome_tabela = " . aspas($rowSubProcesso->NOME_TABELA_FILHA) . " AND flag_chaveprimaria = 'S'";
			$resNomeChave = jn_query($sqlNomeChave);
			$rowNomeChave = jn_fetch_object($resNomeChave);
			
			$retorno['NOME_CHAVE'] = $rowNomeChave->NOME_CAMPO; 
			
			if(($rowSubProcesso->TIPO_RELACAO == '1')and ($retorno['REGISTROS'] > 0))
			{
				$sqlChave = "Select first 1 tabFilha.".$rowNomeChave->NOME_CAMPO." CHAVE from ".strtolower($rowSubProcesso->NOME_TABELA_PRINCIPAL)." tabPrincipal
							 inner join ".strtolower($rowSubProcesso->NOME_TABELA_FILHA)." tabFilha on $auxInner
							 where tabPrincipal.".strtolower($dadosInput['dados']['nomeChave'])." = ".aspas($dadosInput['dados']['chave'])." ";
				$resChave = jn_query($sqlChave);
				$rowChave = jn_fetch_object($resChave);	
				$retorno['CHAVE'] = $rowChave->CHAVE; 				
			}
			
		}

						   	
}else if($dadosInput['tipo'] =='dadosSubProcessosTabela'){
	 $retorno['DADOS'] = array();
	  $opcoes = array();
		$retornoOpcoes = botoesOpcoesAdicionaisEspecificas($dadosInput['dados']['origem'], $dadosInput['dados']['origemOriginal'], $dadosInput['dados']['chave'], $dadosInput['dados']['nomeChave']);
		$opcoes= $retornoOpcoes['OPCOESADICIONAIS'];
		
		if(count($opcoes)>0){
			foreach ($opcoes as $opcao) {
				if($opcao['ABRIR_SEQ_CADASTRO']=='S'){
					$opcao['TIPO'] = 'OPC';
					$retorno['DADOS'][] = $opcao;			
				}
			}
		}
	
		$querySubProcesso ="SELECT 
                            coalesce(cfgtabelas_sis.TABELA_ORIGINAL,cfgtabelas_subprocessos_cd.NOME_TABELA_FILHA) NOME_TABELA_FILHA_C,cfgtabelas_subprocessos_cd.*
                        FROM cfgtabelas_subprocessos_cd 
                        left join cfgtabelas_sis on cfgtabelas_sis.NOME_TABELA = cfgtabelas_subprocessos_cd.NOME_TABELA_FILHA 
                        where comportamento_frm_edicao <> 3  and flag_abre_sequencia_inclus='S' and nome_tabela_principal =".aspas($dadosInput['dados']['origem'])." order by ordem_atalho desc";
		
		$resSubProcesso = jn_query($querySubProcesso);
		
		
		
		$queryInformacoes = 'Select * from '.$dadosInput['dados']['origem'].' where '. $dadosInput['dados']['nomeChave'].' = '. aspas($dadosInput['dados']['chave']);
		$resInformacoes = jn_query($queryInformacoes);
		$rowInformacoes = jn_fetch_object($resInformacoes);
		if($rowInformacoes)
			$rowInformacoes = (array)$rowInformacoes;
				
		while($rowSubProcesso = jn_fetch_object($resSubProcesso)){			
						
			if(ignoraSubProcesso($dadosInput,$rowSubProcesso->NOME_TABELA_FILHA_C)){
				continue;
			}
			
			$sqlNomeChave = "SELECT NOME_CAMPO FROM  cfgcampos_sis_cd WHERE nome_tabela = " . aspas($rowSubProcesso->NOME_TABELA_FILHA_C) . " AND flag_chaveprimaria = 'S'";
			$resNomeChave = jn_query($sqlNomeChave);
			$rowNomeChave = jn_fetch_object($resNomeChave);
			
			if($rowSubProcesso->CAMPO_CONDICAO_ABERTURA01!='')
			{
				if (strposDelphi('IN(',$rowSubProcesso->VALOR_CONDICAO_ABERTURA01)!=-1)
				{
					if (strposDelphi(strtoupper($rowInformacoes[$rowSubProcesso->CAMPO_CONDICAO_ABERTURA01]),strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA01))==-1)
					{
						continue;
					}
				}
				else if(strtoupper($rowInformacoes[($rowSubProcesso->CAMPO_CONDICAO_ABERTURA01)]) != strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA01))
				{
					continue;
				}
			}
			
			if($rowSubProcesso->CAMPO_CONDICAO_ABERTURA02!='')
			{
				if (strposDelphi('IN(',$rowSubProcesso->VALOR_CONDICAO_ABERTURA02)!=-1)
				{
					if (strposDelphi(strtoupper($rowInformacoes[$rowSubProcesso->CAMPO_CONDICAO_ABERTURA02]),strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA02))==-1)
					{
						continue;
					}
				}
				else if(strtoupper($rowInformacoes[($rowSubProcesso->CAMPO_CONDICAO_ABERTURA02)]) != strtoupper($rowSubProcesso->VALOR_CONDICAO_ABERTURA02))
				{
					continue;
				}
			}			
			$linha['TIPO'] = 'SUB';
			$linha['NOMECHAVESUB'] = $rowNomeChave->NOME_CAMPO; 
			$linha['RELACAO']   = $rowSubProcesso->TIPO_RELACAO;
			$linha['TABELASUB']  = $rowSubProcesso->NOME_TABELA_FILHA_C;
			$linha['LABEL']  = jn_utf8_encode($rowSubProcesso->LABEL_ATALHO);
			$linha['CHAVE']  = $dadosInput['dados']['chave'];
			$linha['NOMECHAVE']  = $dadosInput['dados']['nomeChave'];
			$linha['REGISTRO']  = $rowSubProcesso->NUMERO_REGISTRO;
			$linha['TIPO_ADICIONAR']  = $rowSubProcesso->TIPO_ADICIONAR;
			if($linha['TIPO_ADICIONAR']==''){
				$linha['TIPO_ADICIONAR'] = 'N';
			}		
			$linha['FILTROS'] = array();
			if($linha['RELACAO']=='N'){
				if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01 != ''){
					$item['CAMPO'] = $rowSubProcesso->CAMPO_LIGACAO_FILHA_01;
					$item['VALOR'] = $rowInformacoes[$rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_01];
					$linha['FILTROS'][] = $item;
				}
				if($rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02 != ''){
					$item['CAMPO'] = $rowSubProcesso->CAMPO_LIGACAO_FILHA_02;
					$item['VALOR'] = $rowInformacoes[$rowSubProcesso->CAMPO_LIGACAO_PRINCIPAL_02];
					$linha['FILTROS'][] = $item;
				}
			}
						
			$retorno['DADOS'][] = $linha;			
		}
			
}else if($dadosInput['tipo'] =='dadosUmRegistro'){
	$queryTabela = 'Select * from  cfgtabelas_sis where nome_tabela ='.aspas($dadosInput['tab']);
		
	$resTabela = jn_query($queryTabela);
	
	if($rowTabela = jn_fetch_object($resTabela)){
		if($rowTabela->TABELA_ORIGINAL == '')
			$retorno['TABELA_ORIGINAL'] = $rowTabela->NOME_TABELA;
		else
			$retorno['TABELA_ORIGINAL'] = $rowTabela->TABELA_ORIGINAL;
		
		$retorno['TABELA']          = $dadosInput['tab'];
		
		$filtros = ' WHERE 1 = 1 ';
		if(count($dadosInput['filtros'])>0){
			for($i=0;$i<count($dadosInput['filtros']);$i++){
				if($dadosInput['filtros'][$i]['TIPO'] == "L")
					$filtros .=' and '. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ' Like '.aspas($dadosInput['filtros'][$i]['VALOR'].'%').' ';	
				else
					$filtros .=' and '. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ' = '.aspas($dadosInput['filtros'][$i]['VALOR']).' ';		
			}
		}
		
		$queryColunas = "select * from cfgcampos_sis_cd where flag_chaveprimaria = 'S' and nome_tabela = ".aspas($retorno['TABELA_ORIGINAL']);
			
		$resColunas = jn_query($queryColunas);
			
		if($rowColunas = jn_fetch_object($resColunas)){
			$retorno['NOME_CHAVE'] = strtoupper($rowColunas->NOME_CAMPO);
		}
		
		$retorno['TIPO_FORMULARIO'] = 'INC';
		$retorno['CHAVE'] = '';
		
		$sqlRegistro = "select ".strtoupper($retorno['NOME_CHAVE'])." as CHAVE from ".strtolower($retorno['TABELA_ORIGINAL']).$filtros;
		
		$resRegistro = jn_query($sqlRegistro);
			
		if($rowRegistro = jn_fetch_object($resRegistro)){
			$retorno['CHAVE'] = strtoupper($rowRegistro->CHAVE);
			$retorno['TIPO_FORMULARIO'] = 'ALT';
		}
	}
}else if($dadosInput['tipo'] =='dadosAcaoDepoisSalvar'){
	if(@$dadosInput['dados']['chave']){
		$retorno = paginaDepoisSalvar($dadosInput['dados']['chave'],$dadosInput['dados']['nomeChave'], $dadosInput['dados']['origem'], $dadosInput['dados']['origemOriginal'],$dadosInput['dados']['tipoFormulario']);//chave nomeChave, origem origemOriginal tipoFormulario
	}
}

echo json_encode($retorno);


