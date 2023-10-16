<?php
require('../lib/base.php');
require('../private/autentica.php');

if($dadosInput['tipo'] =='dados')
{

	atualizaTabelaCfg_Assist_Config();

	$retorno['DADOS'] = array();
	
	if ($dadosInput['textoBusca']!='')
	{
		$criterioBusca = ' where (CFG_ASSIST_CONFIG.processo_rotina like ' . aspas($dadosInput['textoBusca']) . ') or 
								 (CFG_ASSIST_CONFIG.identificador_configuracao like ' . aspas('%' . $dadosInput['textoBusca'] . '%') . ') or 
		                         (CFG_ASSIST_CONFIG.descricao_configuracao like ' . aspas('%' . $dadosInput['textoBusca'] . '%') . ') ';
	}

	$dados = "Select  cfg_assist_config.modulo_aplicavel + '->' + cfg_assist_config.processo_rotina MODULO_ROTINA, 
				      cfg_assist_config.modulo_aplicavel, cfg_assist_config.processo_rotina
					  From CFG_ASSIST_CONFIG " . $criterioBusca . 
				    " Group by cfg_assist_config.modulo_aplicavel, cfg_assist_config.processo_rotina";

	$resDados = jn_query($dados);

	if ($dadosInput['textoBusca']!='')
	{
		$criterioBusca = ' and ((CFG_ASSIST_CONFIG.processo_rotina like ' . aspas('%' . $dadosInput['textoBusca'] . '%') . ') or 
							    (CFG_ASSIST_CONFIG.identificador_configuracao like ' . aspas('%' . $dadosInput['textoBusca'] . '%') . ') or 
		                        (CFG_ASSIST_CONFIG.descricao_configuracao like ' . aspas('%' . $dadosInput['textoBusca'] . '%') . ')) ';
	}

	while ($rowDados = jn_fetch_object($resDados))
	{

		$grupo['nomeEntidade'] 	= jn_utf8_encode($rowDados->MODULO_ROTINA);

		if ($dadosInput['textoBusca']!='')
		   $grupo['expandido'] 	= true;
		else
		   $grupo['expandido'] 	= false;

		$grupo['itens'] 		= array();
		$grupo['entidade']      = jn_utf8_encode($rowDados->MODULO_ROTINA);
		
		//pr($grupo);

		$queryItens = "Select  cfg_assist_config.numero_registro,
				   	      cfg_assist_config.descricao_configuracao DESCRICAO_CONFIGURACAO, IDENTIFICADOR_CONFIGURACAO, Tabela_Origem, 
				   	      cfg_assist_config.tipo_configuracao, cfg_assist_config.componente_formulario, cfg_assist_config.tipo_opcoes_resposta
						  From CFG_ASSIST_CONFIG
						  where (cfg_assist_config.modulo_aplicavel = " . aspas($rowDados->MODULO_APLICAVEL) . "
						  and cfg_assist_config.processo_rotina = " . aspas($rowDados->PROCESSO_ROTINA) . ") " . $criterioBusca . 
						" Order by cfg_assist_config.modulo_aplicavel, cfg_assist_config.processo_rotina, cfg_assist_config.descricao_configuracao";
		
		$resItens = jn_query($queryItens);

		while($rowItens = jn_fetch_object($resItens))
		{
			
			//pr('-----------------------');

			$item = array();
			$item['entidade']                  = jn_utf8_encode($rowDados->MODULO_ROTINA);
			$item['descricao']                 = jn_utf8_encode($rowItens->DESCRICAO_CONFIGURACAO);
			
			$item['valorOriginalConfiguracao'] = jn_utf8_encode(retornaValorConfiguracao($rowItens->IDENTIFICADOR_CONFIGURACAO));
			$item['novoValorConfiguracao']     = $item['valorOriginalConfiguracao'];

			if (($rowItens->COMPONENTE_FORMULARIO=='DBCHECKBOX') or ($rowItens->COMPONENTE_FORMULARIO=='DBCOMBOBOX'))
			   $item['componente']             = 'COMBOBOX';
			else
			   $item['componente']             = 'EDIT';

			$item['tipoConfiguracao']          = jn_utf8_encode($rowItens->TIPO_CONFIGURACAO);
			$item['tiposRespostas']            = jn_utf8_encode($rowItens->TIPO_OPCOES_RESPOSTA);
			$item['tabelaOrigem']              = jn_utf8_encode($rowItens->TABELA_ORIGEM);
			$item['identificadorConfiguracao'] = jn_utf8_encode($rowItens->IDENTIFICADOR_CONFIGURACAO);
   			
			$grupo['itens'][] = $item;

			//pr($item);
		}

		$retorno['DADOS'][] = $grupo;
	}	

	echo json_encode($retorno);	
}
else if($dadosInput['tipo'] =='salvar')
{
	
	$dados             = $dadosInput['dados']; 
	
	$retorno['STATUS'] = 'OK';
	$retorno['MSG']    = '';
	
	foreach ($dados as $itemDados)
	{
		$valorOriginalConfiguracao = $itemDados['valorOriginalConfiguracao'];
		$novoValorConfiguracao     = $itemDados['novoValorConfiguracao'];
		$tabelaOrigem              = $itemDados['tabelaOrigem']; 
		$identificadorConfiguracao = $itemDados['identificadorConfiguracao'];

		if ($valorOriginalConfiguracao != $novoValorConfiguracao)
		{
			if (($tabelaOrigem == 'CFG0001') or ($tabelaOrigem == 'CFG0002'))
			{
				if ($novoValorConfiguracao=='SIM')
					$novoValorConfiguracao='S';
				else if ($novoValorConfiguracao=='NAO')
					$novoValorConfiguracao='N';

				$query = 'Update ' . $tabelaOrigem . ' Set ' . $identificadorConfiguracao . ' = ' . aspas($novoValorConfiguracao);
			}
			else if ($tabelaOrigem == 'CFG0003')
				$query = 'Update Cfg0003 Set Valor_configuracao = ' . aspas($novoValorConfiguracao) . ' where Identificador_Configuracao = ' . aspas($identificadorConfiguracao);
			else if ($tabelaOrigem == 'CFGCONFIGURACOES_NET')
				$query = 'Update CFGCONFIGURACOES_NET Set Valor_configuracao = ' . aspas($novoValorConfiguracao) . ' where Identificacao_Validacao = ' . aspas($identificadorConfiguracao);

			jn_query($query);

			//pr($identificadorConfiguracao);
			//pr($novoValorConfiguracao);

	        $_SESSION['CFGCONFIGURACOES_NET'][$identificadorConfiguracao] = $novoValorConfiguracao;

	        $retorno['MSG'] .= jn_utf8_encode($identificadorConfiguracao) . ': Vl anterior: ' . $valorOriginalConfiguracao. ', Novo Valor: ' . jn_utf8_encode($novoValorConfiguracao) . '<br>';

			//pr($query);
			//pr('novo valor configuracao: ' . $identificadorConfiguracao . '->' . retornaValorConfiguracao($identificadorConfiguracao));

		}

	}

	if ($retorno['MSG'] == '')
		$retorno['MSG'] = 'Nenhuma configuracao foi modificada, portanto, nenhuma configuracao precisou ser salva';
	else
        $retorno['MSG'] = 'As seguintes configuracoes foram salvas:<br>' . $retorno['MSG'] . '<br>Recomendamos que voce saia e entre novamente no sistema';

	echo json_encode($retorno);	

}	




function atualizaTabelaCfg_Assist_Config()
{


	// Primeiro apago do assistente configurações que existem na tabela, mas não existem na prática na cfg0001, cfg0002 ou cfg0003

    jn_query('DELETE FROM Cfg_Assist_Config 
              WHERE IDENTIFICADOR_CONFIGURACAO NOT IN (SELECT NOME_CAMPO FROM CFGCAMPOS_SIS WHERE CFGCAMPOS_SIS.NOME_CAMPO = IDENTIFICADOR_CONFIGURACAO) 
              AND TABELA_ORIGEM IN (' . aspas('CFG0001') . ', ' . aspas('CFG0002') . ')');

    jn_query('DELETE FROM Cfg_Assist_Config
              WHERE IDENTIFICADOR_CONFIGURACAO NOT IN (SELECT IDENTIFICADOR_CONFIGURACAO FROM CFG0003 WHERE CFG0003.IDENTIFICADOR_CONFIGURACAO = IDENTIFICADOR_CONFIGURACAO) 
              AND TABELA_ORIGEM IN (' . aspas('CFG0003') . ')');

    // Agora insiro configurações que existem nas tabelas cfg0001, cfg0002 ou cfg0003 e não existem no assistente

    jn_query('Insert Into Cfg_Assist_Config(Tabela_Origem, Modulo_Aplicavel, Processo_Rotina, Tipo_Configuracao, Identificador_Configuracao, Descricao_Configuracao) 
              SELECT "CFG0003", "GERAL NOVO", "OUTROS E ATUALIZAÇÕES", "DIVERSOS OU NOVOS", IDENTIFICADOR_CONFIGURACAO, DESCRICAO_CONFIGURACAO FROM CFG0003 
              WHERE CFG0003.IDENTIFICADOR_CONFIGURACAO NOT IN (SELECT Identificador_Configuracao FROM Cfg_Assist_Config WHERE (CFG0003.IDENTIFICADOR_CONFIGURACAO = Cfg_Assist_Config.Identificador_Configuracao))');

    jn_query('Insert Into Cfg_Assist_Config(Tabela_Origem, Modulo_Aplicavel, Processo_Rotina, Tipo_Configuracao, Identificador_Configuracao, Descricao_Configuracao) 
              SELECT NOME_TABELA, "GERAL NOVO", "OUTROS E ATUALIZAÇÕES", "DIVERSOS OU NOVOS", NOME_CAMPO, LABEL_CAMPO FROM CFGCAMPOS_SIS WHERE
              CFGCAMPOS_SIS.NOME_CAMPO NOT IN (SELECT Identificador_Configuracao FROM Cfg_Assist_Config WHERE (CFGCAMPOS_SIS.NOME_CAMPO = Cfg_Assist_Config.Identificador_Configuracao)) 
              AND CFGCAMPOS_SIS.NOME_TABELA IN ("CFG0001","CFG0002") ');

    jn_query('Insert Into Cfg_Assist_Config(Tabela_Origem, Modulo_Aplicavel, Processo_Rotina, Tipo_Configuracao, Identificador_Configuracao, Descricao_Configuracao) 
              SELECT "CFGCONFIGURACOES_NET", "ALIANCA_NET", "ALIANCA_NET E PORTAL", "ALIANCA_NET E PORTAL", IDENTIFICACAO_VALIDACAO, DESCRICAO_VALIDACAO FROM CFGCONFIGURACOES_NET 
              WHERE CFGCONFIGURACOES_NET.IDENTIFICACAO_VALIDACAO NOT IN (SELECT Identificador_Configuracao FROM Cfg_Assist_Config WHERE (CFGCONFIGURACOES_NET.IDENTIFICACAO_VALIDACAO = Cfg_Assist_Config.Identificador_Configuracao))');

}


?>