<?php
require_once('../lib/base.php');

require_once('../private/autentica.php');
require('../EstruturaEspecifica/valorPadraoProcesso.php');


if($dadosInput['tipo'] =='salvar')
{
	
//	$_GET['Teste'] = 'OK';

	$registro = jn_gerasequencial('CFGDISPAROPROCESSOSCABECALHO');
	
	jn_query('SET IDENTITY_INSERT CFGDISPAROPROCESSOSCABECALHO ON',false, true, true);

    $insert = "INSERT INTO CFGDISPAROPROCESSOSCABECALHO(
														NUMERO_REGISTRO_PROCESSO,
														NOME_PROCESSO, 
														IDENTIFICACAO_PROCESSO, 
														DATA_DISPARO, 
														HORA_DISPARO, 
														OPERADOR_DISPARO,FLAG_ENVIAR_MSG_CONCLUSAO) 
												VALUES (
														".aspas($registro).", 
														".aspas($dadosInput['reg']).", 
														".aspas($dadosInput['reg']).", 
														".dataToSql(date("d/m/Y")).", 
														".aspas(date('H:i')).", 
														".aspas($_SESSION['codigoIdentificacao']).",'S')";
	jn_query($insert);

	jn_query('SET IDENTITY_INSERT CFGDISPAROPROCESSOSCABECALHO OFF',false, true, true);

	$select       = "select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = " . aspas($registro);
	$resSelect    = jn_query($select);
	$rowSelect    = jn_fetch_object($resSelect);

	//pr('asdkfjasdklf asdklfj asdklfjaklsd');

	foreach ($dadosInput['dados'] as $chave => $valor) 
	{
		//$registroItem	 = jn_gerasequencial('CFGDISPAROPROCESSOSPARAMETROS');
		
		//print_r($valor);

		if(is_array($valor))
		{
			$valor = json_encode($valor);
		}
		
		//print_r($valor);
		//exit;

		$jsonValor = json_decode($valor);

		//print_r($jsonValor);

		if (($dadosInput['salvarParametrosComoDefault']=='S') and ($chave=='formulario'))
		{
			foreach ($jsonValor as $chaveJson => $valorJson) 
			{
				$nomeCampo               = $chaveJson;


				if ((strpos($nomeCampo,'DATA')!==false) or (strpos($nomeCampo,'DT')!==false) or 
 				    (strpos($nomeCampo,'CHK')!==false) or (strpos($nomeCampo,'CHEK')!==false) or 
				    (strpos($nomeCampo,'RADI')!==false) or (strpos($nomeCampo,'CHEK')!==false) or 
				    (strpos($nomeCampo,'_INICIAL')!==false) or (strpos($nomeCampo,'_FINAL')!==false))
				{
					$valorParametroInformado = $valorJson;
				}
				else
				{
					$valorParametroInformado = jn_utf8_decode($valorJson);
				}

				$insert = "UPDATE CFGCAMPOS_PD SET VALOR_PADRAO = " . aspas(copyDelphi($valorParametroInformado,1,200)) . " where NOME_CAMPO = " . aspas($nomeCampo) . 
				          " and NUMERO_REGISTRO_PROCESSO = " . aspas($rowSelect->IDENTIFICACAO_PROCESSO);

				jn_query($insert);
			}
		}

		$insert = "INSERT INTO CFGDISPAROPROCESSOSPARAMETROS(
															 NUMERO_REGISTRO_PROCESSO, 
															 NOME_PARAMETRO, 
															 TIPO_PARAMETRO, 
															 VALOR_PARAMETRO) 
													 VALUES (
															 ".aspas($registro).", 
															 ".aspas($chave).", 
															 ".aspas('F').", 
															 ".aspas($valor).")";
		jn_query($insert);
	}

	$retorno['STATUS'] = 'OK';
	$retorno['MSG']    = 'Processando '.date("d/m/Y H:i:s");
	$retorno['PROC']   = $registro;
	
	echo json_encode($retorno);
}

if($dadosInput['tipo'] =='timer')
{

	$select = 'Select first 100 DATA_DISPARO, HORA_DISPARO, EVOLUCAO_PROCESSO, RESULTADO_PROCESSO, DATA_CONCLUSAO, HORA_CONCLUSAO, 
	                            ARQUIVO_RESULTADO_PROCESSO, ARQUIVO_RELATORIO_PROCESSO, NUMERO_REGISTRO_PROCESSO, IDENTIFICACAO_PROCESSO 
	           from CFGDISPAROPROCESSOSCABECALHO 
	           where IDENTIFICACAO_PROCESSO='.aspas($dadosInput['reg']).
	     //      ' and NUMERO_REGISTRO_PROCESSO='.aspas($dadosInput['proc']).
	     //      ' and OPERADOR_DISPARO='.aspas($_SESSION['codigoIdentificacao']);
	           ' order by DATA_DISPARO desc, HORA_DISPARO desc ';
	$res = jn_query($select);
   
	$retorno['STATUS'] = 'ERRO';
	$retorno['MSG']    = '';
	$retorno['HTML']   = '';
	$retorno['END']		= true;
	  

	$retorno['DADOS'] = array();

	while ($row = jn_fetch_object($res)) 
	{
		$retorno['STATUS'] = 'OK';

		//pr($row);

		if($row->DATA_CONCLUSAO=='')
		{
			$dado['TITULO_RESULTADO'] 			= 'Processo iniciado em: ' . SqlToData($row->DATA_DISPARO) . ' as ' . $row->HORA_DISPARO; 
			$dado['MSG']    		  			= 'Processando:' . date("d/m/Y H:i:s");
			$dado['MSG']    		 			.= '<br>';
			$dado['MSG']    		 			.= jn_utf8_encode($row->EVOLUCAO_PROCESSO);
			$dado['END']		      			= false;
			$dado['NUMERO_REGISTRO_PROCESSO'] 	= $row->NUMERO_REGISTRO_PROCESSO;
			$dado['IDENTIFICACAO_PROCESSO']   	= $row->IDENTIFICACAO_PROCESSO;
		}
		else
		{
			$dado['MSG']    	  	  			= '';
			$dado['TITULO_RESULTADO'] 			= 'Processo iniciado em: ' . SqlToData($row->DATA_DISPARO) . ' as ' . $row->HORA_DISPARO . 
			                            		  ' e concluido em: ' . SqlToData($row->DATA_CONCLUSAO) . ' as ' . $row->HORA_CONCLUSAO;

			if ($row->ARQUIVO_RESULTADO_PROCESSO!='')
			   $dado['MSG']    		 			.= 'Como resultado, foi gerado o arquivo: ' . $row->ARQUIVO_RESULTADO_PROCESSO . '<br><br>';

			if ($row->ARQUIVO_RELATORIO_PROCESSO!='')
		    	$dado['MSG']    		 			.= 'Arquivo relatorio do processo: ' . $row->ARQUIVO_RELATORIO_PROCESSO . '<br><br>';
		    
			$dado['HTML']   		  			= $row->RESULTADO_PROCESSO ; 
			$dado['END']			  			= true;
			$dado['NUMERO_REGISTRO_PROCESSO'] 	= $row->NUMERO_REGISTRO_PROCESSO;
			$dado['IDENTIFICACAO_PROCESSO']   	= $row->IDENTIFICACAO_PROCESSO;
		    $dado['ARQ_RELATORIO']	  			= '';
		    $dado['ARQ_RESULTADO']	  			= '';

			//$localPersistencia = retornaValorConfiguracao('LINK_PERSISTENCIA');
			//$localPersistencia = stringReplace_Delphi($localPersistencia,'ServidorAl2/','ArquivosGerados',false);

			if ($row->ARQUIVO_RELATORIO_PROCESSO!='')
			   $dado['ARQ_RELATORIO']	  		= $row->ARQUIVO_RELATORIO_PROCESSO;

			if ($row->ARQUIVO_RESULTADO_PROCESSO!='')
			   $dado['ARQ_RESULTADO']	  		= $row->ARQUIVO_RESULTADO_PROCESSO;
		}

		$retorno['DADOS'][] = $dado;
	}

	echo json_encode($retorno);

}

if($dadosInput['tipo'] =='registraConclusaoManual')
{

	registraConclusaoProcesso($dadosInput['numeroRegistroProceso'], 'Processo concluído manualmente');

	$retorno['PROC'] = 'OK';
	echo json_encode($retorno);

}


if($dadosInput['tipo'] =='dado')
{

		$queryTabela = "Select COALESCE(MENSAGEM_ALERTA,'')MENSAGEM_ALERTA,COALESCE(MENSAGEM_CONFIRMA,'')MENSAGEM_CONFIRMA,cfgprocessos_pd.* from  cfgprocessos_pd where numero_registro =".aspas($dadosInput['reg']);
		
		$resTabela = jn_query($queryTabela);
		
		if($rowTabela = jn_fetch_object($resTabela)){
			$retorno['NOME_PROCESSO']   = jn_utf8_encode($rowTabela->NOME_PROCESSO);
			$retorno['DESCRICAO_AJUDA'] = jn_utf8_encode($rowTabela->DESCRICAO_AJUDA);
			$retorno['MENSAGEM_ALERTA'] = jn_utf8_encode($rowTabela->MENSAGEM_ALERTA);
			$retorno['MENSAGEM_CONFIRMA'] = jn_utf8_encode($rowTabela->MENSAGEM_CONFIRMA);
			$retorno['TIPO_PROCESSO'] = jn_utf8_encode($rowTabela->TIPO_PROCESSO);
			$retorno['DESTINO_PROCESSO'] = jn_utf8_encode($rowTabela->DESTINO_PROCESSO);
			$retorno['BOTAO_IMPRIMIR'] = jn_utf8_encode($rowTabela->BOTAO_IMPRIMIR);
			$retorno['AUX_PADRAO'] = jn_utf8_encode($rowTabela->AUX_PADRAO);
			$retorno['TEMPO_VERIFICACAO'] = jn_utf8_encode($rowTabela->TEMPO_VERIFICACAO);
		}
		
		if(($rowTabela->PERMISSAO_TABELA!='')and($rowTabela->PERMISSAO_NIVEL!='')){
			permissaoPx($rowTabela->PERMISSAO_TABELA,$rowTabela->PERMISSAO_NIVEL,true);
		}
		
		echo json_encode($retorno);
}

if($dadosInput['tipo'] =='aberto')
{
		$queryTabela = "Select NUMERO_REGISTRO_PROCESSO from  CFGDISPAROPROCESSOSCABECALHO where DATA_CONCLUSAO IS NULL " . 
		               " and OPERADOR_DISPARO = ".aspas($_SESSION['codigoIdentificacao'])." and IDENTIFICACAO_PROCESSO =".aspas($dadosInput['reg']);
		
		$resTabela = jn_query($queryTabela);
		
		$retorno['PROC'] = '';
		
		if($rowTabela = jn_fetch_object($resTabela)){
			$retorno['PROC']   = jn_utf8_encode($rowTabela->NUMERO_REGISTRO_PROCESSO);
			
		}
		
		echo json_encode($retorno);
}






if($dadosInput['tipo'] =='download'){

	$file = 'https://aliancanet2/ArquivosGerados/Remessa/arquivoRemessa.txt';
	$file = '/ArquivosGerados/Remessa/arquivoRemessa.txt';
	//$file = '//Temp//arquivoRemessa.txt';

	pr('download' . $file);

	if (file_exists($file)) 
	{

		pr('entrou');


	    // Define os cabeçalhos
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.basename($file).'"');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));

	    // Lê e envia o arquivo para o usuário
	    readfile($file);
	    exit;
	}

}













function registraLogEvolucaoProcesso($numeroRegistroProcesso, $informacoesRegistrar)
{
	$insert = "insert into CfgDisparoProcessosLog(Numero_Registro_Processo, Informacoes_Log) " . 
			   "values(" . aspas($numeroRegistroProcesso) . ', ' . aspas(copyDelphi($informacoesRegistrar,1,247)) . ")";

	jn_query($insert);
}



function atualizaStatusProcesso($numeroRegistroProcesso, $evolucaoProcesso, $logDetalhadoProcesso = '')
{

	$evolucaoProcesso = jn_utf8_decode($evolucaoProcesso);

	$update = "update CFGDISPAROPROCESSOSCABECALHO Set EVOLUCAO_PROCESSO = " . aspas(copyDelphi($evolucaoProcesso,1,40));

	if ($logDetalhadoProcesso!='')
	{
		$logDetalhadoProcesso = jn_utf8_decode($logDetalhadoProcesso);
		$update .= ", LOG_PROCESSO = " . aspas($logDetalhadoProcesso);
	}

	$update .= " where NUMERO_REGISTRO_PROCESSO = " . aspas($numeroRegistroProcesso);

	jn_query($update);

}


function registraConclusaoProcesso($numeroRegistroProcesso, $evolucaoProcesso, $logDetalhadoProcesso = '', 
								   $arquivoGeradoResultadoProcesso = '', $nomearquivoLogProcesso = '', $nomearquivoRelatorioProcesso = '')
{

	$evolucaoProcesso     = jn_utf8_decode($evolucaoProcesso);
	$logDetalhadoProcesso = jn_utf8_decode($logDetalhadoProcesso);
	$msgResultado         = '';

	$update = "update CFGDISPAROPROCESSOSCABECALHO Set EVOLUCAO_PROCESSO = " . aspas(copyDelphi($evolucaoProcesso,1,40)) . 
	          ", DATA_CONCLUSAO = " . dataToSql(date("d/m/Y")) .  
	          ", RESULTADO_PROCESSO = " . aspas(copyDelphi($logDetalhadoProcesso,1,200)) . 
	          ", HORA_CONCLUSAO = " . aspas(date('H:i')); 

	$localPersistencia = retornaValorConfiguracao('LINK_PERSISTENCIA');
	$localPersistencia = stringReplace_Delphi($localPersistencia,'ServidorAl2/','ArquivosGerados',false);

	if ($arquivoResultadoProcesso!='')
		$arquivoResultadoProcesso = $localPersistencia . $arquivoResultadoProcesso;

	if ($nomearquivoRelatorioProcesso!='')
		$nomearquivoRelatorioProcesso = $localPersistencia . $nomearquivoRelatorioProcesso;

	$nomearquivoRelatorioProcesso = stringReplace_Delphi($nomearquivoRelatorioProcesso,'ArquivosGerados../../ArquivosGerados','ArquivosGerados','N');

	if ($nomearquivoLogProcesso!='')
		$nomearquivoLogProcesso = $localPersistencia . $nomearquivoLogProcesso;

	if (!file_exists($nomearquivoRelatorioProcesso))
	{
		$nomearquivoRelatorioProcesso = stringReplace_Delphi($nomearquivoRelatorioProcesso,$localPersistencia,'../../ArquivosGerados','N');
	}

	if (!file_exists($arquivoResultadoProcesso))
	{
		$arquivoResultadoProcesso = stringReplace_Delphi($arquivoResultadoProcesso,$localPersistencia,'../../ArquivosGerados','N');
	}

	if (!file_exists($nomearquivoLogProcesso))
	{
		$nomearquivoLogProcesso = stringReplace_Delphi($nomearquivoLogProcesso,$localPersistencia,'../../ArquivosGerados','N');
	}

	if ($logDetalhadoProcesso!='')
	{
		$logDetalhadoProcesso 	= jn_utf8_decode($logDetalhadoProcesso);
		$update 			   .= ", LOG_PROCESSO = " . aspas($logDetalhadoProcesso);
		$msgResultado 	        = $logDetalhadoProcesso;
	}

	if ($arquivoResultadoProcesso!='')
	{
		$update               .= ", ARQUIVO_RESULTADO_PROCESSO = " . aspas($arquivoResultadoProcesso);
		$msgResultado     	  .= '<br>Foi gerado o arquivo: ' . $arquivoResultadoProcesso;
	}

	if ($nomearquivoRelatorioProcesso!='')
	{
		$update               .= ", ARQUIVO_RELATORIO_PROCESSO = " . aspas($nomearquivoRelatorioProcesso);
		$msgResultado    	  .= '<br><a href="' . $nomearquivoRelatorioProcesso . '" target="_blank">Clique aqui para abrir o relatorio</a>';
	}

	if (($nomearquivoLogProcesso!='') and ($nomearquivoRelatorioProcesso==''))
	{
		$update               .= ", ARQUIVO_LOG_PROCESSO = " . aspas($nomearquivoLogProcesso);
		$msgResultado 	 	  .= '<br>Foi gerado o Log de processamento: ' . $nomearquivoLogProcesso;
	}

	$update .= " where NUMERO_REGISTRO_PROCESSO = " . aspas($numeroRegistroProcesso);

	$resultadoProcesso                  = array();
	$resultadoProcesso['ID_RESULTADO'] = 'OK';
	$resultadoProcesso['MSG_RESULTADO'] = $msgResultado;

	echo apresentaMensagemConclusaoProcesso($resultadoProcesso);

	return jn_query($update);

}



function apresentaMensagemInicioProcesso($tipoProcesso = 'PROC_NORMAL')
// $tipoProcesso = 'PROC_RAPIDO'
// $tipoProcesso = 'PROC_NORMAL'
// $tipoProcesso = 'PROC_LENTO'
{

	$queryProcesso = 'select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO where NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']);
	$resProcesso   = jn_query($queryProcesso);
	$rowProcesso   = jn_fetch_object($resProcesso);

	$html = '<div style="text-align:center; font-family: ' . aspas('Nunito Sans') . ', sans-serif;">';

	if ($tipoProcesso=='PROC_RAPIDO')
	{
		$html .= jn_utf8_decode('<h3>Processo disparado</h3>
									<p>Veja abaixo o resultado do processo</p>
									<p>Seu processo foi iniciado em: ' . date("d/m/Y") . ' as ' . date('H:i') . '</p>
									<p>Id do processo: ' . $_POST['ID_PROCESSO']  . '</p><br><br>
									</div>');
	}
	else if ($tipoProcesso=='PROC_LENTO')
	{
		$html .= jn_utf8_decode('<h3>Processo iniciado</h3>
									<p>Voce pode retornar e deixar o processo sendo executado em segundo plano, ou acompanhar o andamento do processo aqui</p><br>
									<p>Seu processo foi iniciado em: ' . date("d/m/Y") . ' as ' . date('H:i') . '</p>
									<p>Id do processo: ' . $_POST['ID_PROCESSO']  . '</p><br><br>
									<p>Para visualizar o andamento do processo ou obter seu resultado, clique no botao acima "Andamento do processo"</p>
									</div>');
	}
	else 
	{
		$html .= jn_utf8_decode('<h3>Processo iniciado</h3>
									<p>Voce pode retornar e deixar o processo sendo executado em segundo plano, ou acompanhar o andamento do processo aqui</p><br>
									<p>Seu processo foi iniciado em: ' . date("d/m/Y") . ' as ' . date('H:i') . '</p>
									<p>Id do processo: ' . $_POST['ID_PROCESSO']  . '</p><br><br>
									<p>Para visualizar o andamento do processo ou obter seu resultado, clique no botao acima "Andamento do processo"</p>
									</div>');
	}

	return $html;

}



function apresentaMensagemConclusaoProcesso($resultadoProcesso)
{

    if ($resultadoProcesso['ID_RESULTADO'] == 'ERRO')
    	$msgTratada = '<span style="color:red;font-weight: bold">' . $resultadoProcesso['MSG_RESULTADO'] . '</span>';
    else if ($resultadoProcesso['ID_RESULTADO'] == 'ERRO_VALIDACAO')
    	$msgTratada = '<span style="color:red;font-weight: bold">' . $resultadoProcesso['MSG_RESULTADO'] . '</span>';
    else if ($resultadoProcesso['ID_RESULTADO'] == 'AVISO_VALIDACAO')
    	$msgTratada = '<span style="color:DarkOrange;font-weight: bold">' . $resultadoProcesso['MSG_RESULTADO'] . '</span>';
    else 
    	$msgTratada = '<span style="font-weight: normal">' . $resultadoProcesso['MSG_RESULTADO'] . '</span>';

	$queryProcesso = 'select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO where NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']);
	$resProcesso   = jn_query($queryProcesso);
	$rowProcesso   = jn_fetch_object($resProcesso);

	$html 		   = jn_utf8_decode('<div style="text-align:center; max-with:700px; font-family: ' . aspas('Nunito Sans') . ', sans-serif;">
		 							 <h3>Processo concluido</h3>
									 <p>Resultado do processo: <br><br><br>' . $msgTratada . '</p><br>
									 <p>Seu processo foi concluido em: ' . date("d/m/Y") . ' as ' . date('H:i') . '</p>
									 <p>Id do processo: ' . $_POST['ID_PROCESSO']  . '</p>
									 </div>');

	return $html;

}



?>