<?php
require('../lib/base.php');

require('../private/autentica.php');

require('../EstruturaEspecifica/complementoSql.php');
require('../EstruturaEspecifica/ordemGrid.php');
require('../EstruturaEspecifica/rodapeVisualizacaoDinamica.php');


// Aqui eu limpo o protolo, pq se o cara navegou é porque ele mudou de usuário.
$_SESSION['NUMERO_PROTOCOLO_ATIVO'] = '';


if($dadosInput['tipo'] =='dados'){

	//$dadosInput['numpag'] = '10';//itens por paginal
	
	$queryTabela = "Select distinct NOME_CAMPO,TIPO_CAMPO,TAMANHO_CARD,STYLE_CAMPO,LABEL_CAMPO,ALTURA_IMG_CORTE,ORDEM_CAMPO from  CFGCAMPOS_SIS_VZ where NOME_TABELA =".aspas($dadosInput['tab'])." order by ORDEM_CAMPO";
		
	$resTabela = jn_query($queryTabela);
	
	$campos = '';
	$campos2 = '';
	
	$arrayCampos = array();
	
	while($rowTabela = jn_fetch_object($resTabela)){
		$campoVz = null;
		//pr($rowTabela);
		
		
		if(trim($rowTabela->NOME_CAMPO)!==''){
			if($campos !== ''){
				$campos .=',';
			}else{
				$campos .= '  ';
				$dadosInput['ordem'] = $rowTabela->NOME_CAMPO;
			}
			$campos .= $rowTabela->NOME_CAMPO;
		}
		
		$campo['CAMPO'] = $rowTabela->NOME_CAMPO;
		$campo['TIPO'] = $rowTabela->TIPO_CAMPO;
		$campo['TAMANHO_CARD'] = $rowTabela->TAMANHO_CARD;
		$campo['STYLE'] = json_decode($rowTabela->STYLE_CAMPO);
		if($campo['STYLE']==''){
			$campo['STYLE']== json_decode('{}');	
		}
		$campo['VALOR'] = '';
		$campo['LABEL'] = ($rowTabela->LABEL_CAMPO);
		
		if($rowTabela->ALTURA_IMG_CORTE!==''){
			$campo['STYLE_IMG']['height']   = $rowTabela->ALTURA_IMG_CORTE;// '{"height": "'.$rowTabela->ALTURA_IMG_CORTE.'","overflow": "hidden"}';
			$campo['STYLE_IMG']['overflow'] = "hidden";
			$campo['ABERTO'] = false;
		}else{
			$campo['STYLE_IMG'] = '';
		}
		
				
		
		$arrayCampos[] = $campo;
	}
	$dadosInput['ordem'] =  ordemGrid($dadosInput['tab']);
	
	if($dadosInput['tab'] == 'VW_REDECREDENCIADA_AL2' and (($_SESSION['type_db'] =='mssqlserver') or ($_SESSION['type_db'] =='sqlsrv'))){		
		$campos2 = 'distinct '.$campos;
	}
	
	if($dadosInput['tab'] != 'VW_COMUNICACAO_NET_AL2'){		
		$campos = 'distinct '.$campos;
	}
	
	$filtros = ' WHERE 1 = 1 ';
	
	$filtros .= CompSql($dadosInput['tab'],'VZ_DM');
	
	if($dadosInput['tab'] == 'VW_REDECREDENCIADA_AL2'){		
		$dadosInput['filtros'] =  json_decode(str_replace("\\","",$dadosInput['filtros']));
	}else{		
		$dadosInput['filtros'] =  json_decode($dadosInput['filtros']);
	}
	
	if(count($dadosInput['filtros'])>0){		
		for($i=0;$i<count($dadosInput['filtros']);$i++){
			$dadosInput['filtros'][$i] = (array)$dadosInput['filtros'][$i];
			if($dadosInput['filtros'][$i]['TIPO'] == "L")
				$filtros .=' and '. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ' Like '. aspas($dadosInput['filtros'][$i]['VALOR'].'%').' ';	
			else if($dadosInput['filtros'][$i]['TIPO'] == "D")
				$filtros .=' and '. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ' = ' . dataToSql($dadosInput['filtros'][$i]['VALOR']).' ';	
			
			else
				$filtros .=' and '. strtolower($dadosInput['filtros'][$i]['CAMPO']) . ' = ' . aspas($dadosInput['filtros'][$i]['VALOR']).' ';		
		}
	}
	
	$codigoSmart = $_SESSION['codigoSmart'];

	if(!$codigoSmart){
		$queryEmp = 'SELECT CODIGO_SMART FROM CFGEMPRESA ';
		$resEmp = jn_query($queryEmp);
		$rowEmp = jn_fetch_object($resEmp);	
		$codigoSmart = $rowEmp->CODIGO_SMART;
	}

	if($codigoSmart == '4055' || $codigoSmart == '3808'){
		$filtros = str_replace("tipo_prestador = '99'"," (tipo_prestador = '01' or tipo_prestador = '02') ",$filtros);
	}

	if($dadosInput['tab'] == 'VW_REDECREDENCIADA_AL2' && $_SESSION['perfilOperador']=='BENEFICIARIO' && retornaValorConfiguracao('VALIDA_ASSOC_REDE_INDICADA') == 'SIM') {
		$filtros .= ' and VW_REDECREDENCIADA_AL2.CODIGO_PRESTADOR IN (
							SELECT 
								PS5013.CODIGO_PRESTADOR
							FROM PS5013								
							WHERE PS5013.CODIGO_REDE_INDICADA IN (
								SELECT 
									PS1030.CODIGO_REDE_INDICADA 
								FROM PS1000
								INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO)
								WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']) . '
							)
						) ';
		
	}

	$queryColunas = 'select '.$campos.' from ' . strtolower($dadosInput['tab']).$filtros;
	
	if($dadosInput['ordem']!=''){
		$queryColunas= $queryColunas . ' order by '. $dadosInput['ordem'].' ';
	}	
	
	

	if($_SESSION['type_db'] =='mysql'){
		
		if($dadosInput['numpag']>0){
			$limite= ' limit '.(($dadosInput['pag']-1)*$dadosInput['numpag']).','.$dadosInput['numpag'];
		}
		$queryColunas = $queryColunas.$limite;
		
	}else if (($_SESSION['type_db'] =='mssqlserver') or ($_SESSION['type_db'] =='sqlsrv')){
			if($campos2 == ''){
				$campos2 = $campos;
			}else{	
				
				$ArrCampos2 = explode(',', $campos2);
				$ArrCampos2 = array_unique($ArrCampos2);

				$campos2 = '';
				foreach($ArrCampos2 as $item){

					if($campos2 != '')
						$campos2 .= ', ';

					$campos2 .= $item;					
				}
			}

			$ArrCampos = explode(',', $campos);
			$ArrCampos = array_unique($ArrCampos);
			$campos = '';
			foreach($ArrCampos as $item){
				if($campos != '')
					$campos .= ', ';

				$campos .= $item;
			}

			$queryColunas =	"		WITH paginacao AS
										(
											SELECT ".$campos.",
											indice = ROW_NUMBER() OVER (ORDER BY ". $dadosInput['ordem'] .") 
											from (SELECT  ".$campos2." FROM ".$dadosInput['tab']." " . $filtros  .") T
										)
										 SELECT  ".$campos2."
										 FROM paginacao
										 WHERE indice BETWEEN ".(($dadosInput['pag']-1)*$dadosInput['numpag'])." AND ".((($dadosInput['pag']-1)*$dadosInput['numpag'])+$dadosInput['numpag']) ."
										 ORDER BY ".$dadosInput['ordem'] ;
			
	}else if($_SESSION['type_db'] =='firebird'){
		
		$queryColunas = 'select first '.$dadosInput['numpag'].' skip '.(($dadosInput['pag']-1)*$dadosInput['numpag']).' '.$campos.' from ' . strtolower($dadosInput['tab']).$filtros;
	
		if($dadosInput['ordem']!=''){
			$queryColunas= $queryColunas . ' order by '. $dadosInput['ordem'].' ';
		}		

	}
	
	if(($dadosInput['pag'] == 1) and ($dadosInput['numpag']>0)){
		if($_SESSION['type_db'] =='firebird')
			$queryCount = 'select count('.str_replace(",", "||",$campos).') REGISTROS from ' . strtolower($dadosInput['tab']).$filtros;
		else if($_SESSION['type_db'] =='sqlsrv')
			$queryCount = 'select count(*) REGISTROS from ' . $dadosInput['tab'].$filtros;
		else
			$queryCount = 'select count(*) REGISTROS from ' . strtolower($dadosInput['tab']).$filtros;
		$resCount = jn_query($queryCount);
		if($rowCount = jn_fetch_object($resCount)){
			$retorno['PAGINAS'] =  ceil($rowCount->REGISTROS);
		}
	}else if($dadosInput['pag'] == 1){
		$retorno['PAGINAS'] = 1;
	}
	
	if($retorno['PAGINAS'] == 0){
		$retorno['PAGINAS'] = 1;
	}
	
	$resColunas = jn_query($queryColunas);
	
	$i=0;
	while($rowColunas = jn_fetch_object($resColunas)){
		$linha = null;
		$dadoLinha =(array) $rowColunas;
		$colunas = null;
		
		$apresentaPendencia = validaPendencia($dadosInput['tab']);
		if($apresentaPendencia == true && $i == 0){
			$ArrPendencia[0]['CAMPO'] = 'NUMERO_REGISTRO';
			$ArrPendencia[0]['TIPO'] = 'H3';
			$ArrPendencia[0]['TAMANHO_CARD'] = 45;
			$ArrPendencia[0]['VALOR'] = '';
			
			$ArrPendencia[1]['CAMPO'] = 'TITULO_MENSAGEM';
			$ArrPendencia[1]['TIPO'] = 'H3';
			$ArrPendencia[1]['TAMANHO_CARD'] = 45;
			$ArrPendencia[1]['VALOR'] = 'Pendência Cadastrada';					
			
			$ArrPendencia[2]['CAMPO'] = 'DESCRICAO_MENSAGEM';
			$ArrPendencia[2]['TIPO'] = 'HTML';
			$ArrPendencia[2]['TAMANHO_CARD'] = 45;
			$ArrPendencia[2]['VALOR'] = '<h4>Seu faturamento possui pendências. Acesse o menu "Pendências Prestador > Pendências Cadastradas" para visualizar. </h4>';
			
			$i++;
			$retorno['LINHAS'][]['COLUNAS'] = $ArrPendencia;	
		}
		
		foreach ($arrayCampos as $item){
			$coluna = $item;
			
			if($item['CAMPO'] == 'DATA_MENSAGEM'){
				$coluna['VALOR']  = sqlToData($dadoLinha[$coluna['CAMPO']]);
			}else{
				$coluna['VALOR']  = jn_utf8_encode($dadoLinha[$coluna['CAMPO']]);
			}
			
			if($coluna['TIPO']=='MOSTRAR'){
				$coluna['VALOR_ORIGINAL'] = ($coluna['VALOR']);
			}
		
			$retorno['TAMANHO_CARD'] = $coluna['TAMANHO_CARD'];
			$colunas[]= $coluna;
			
		}	
		
		
		if($retorno['TAMANHO_CARD'] == ''){
			$retorno['TAMANHO_CARD'] = '50';
		}

		$retorno['LINHAS'][]['COLUNAS'] = $colunas;
	}	

	echo json_encode($retorno);
}else if($dadosInput['tipo'] =='rodape'){
	
	$retorno = rodapeVisualizacaoDinamica($dadosInput['tab']);
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] =='mostrar'){
	
	$retorno = '';
	
	$queryTabela = "Select * from  CFGCAMPOS_SIS_VZ where NOME_TABELA =".aspas($dadosInput['tab'])." and NOME_CAMPO=".aspas($dadosInput['campo']['CAMPO']);
	
	if($dadosInput['campo']['LABEL'] =='Corpo Clinico'){
		$queryTabela .= ' AND TABELA_MOSTRAR = "VW_CORPO_CLINICO_AL2" ';
	}elseif($dadosInput['campo']['LABEL'] =='Planos'){
		$queryTabela .= ' AND CAMPO_MOSTRAR = "NOME_PLANO" ';
	}



	$resTabela = jn_query($queryTabela);	
	
	if($rowTabela = jn_fetch_object($resTabela)){
		$filtros  = ' WHERE 1 = 1 ';
		$filtros .= CompSql($dadosInput['tab'],'VZ_MO');
		$filtros .= str_replace("|VALOR|", aspas($dadosInput['campo']['VALOR']), $rowTabela->FILTRO_MOSTRAR);
		
		$campoQuery = $rowTabela->CAMPO_MOSTRAR;
		
		if($rowTabela->CAMPO_MOSTRAR == 'NOME_ESPECIALIDADE' and $rowTabela->TABELA_MOSTRAR == 'VW_REDECREDENCIADA_AL2'){
			$campoQuery = 'DISTINCT NOME_ESPECIALIDADE AS NOME_ESPECIALIDADE ';
		}elseif($rowTabela->CAMPO_MOSTRAR == 'NOME_PLANO' and $rowTabela->TABELA_MOSTRAR == 'VW_REDECREDENCIADA_AL2'){
			$campoQuery = 'DISTINCT NOME_PLANO AS NOME_PLANO ';
		}

		$selectMostrar = "Select ".$campoQuery." from ".$rowTabela->TABELA_MOSTRAR. " ".$filtros;
		
		$resMostrar = jn_query($selectMostrar);
		
		while($rowMostrar = jn_fetch_object($resMostrar)){
			$dadoLinha =(array) $rowMostrar;
			$retorno .= jn_utf8_encode($dadoLinha[$rowTabela->CAMPO_MOSTRAR])."<br>";							
		}
	}
	
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] =='retornaConfiguracaoLGPD'){
	
	if(retornaValorConfiguracao('CAMINHO_AVISO_LGPD')){
		$retorno['CAMINHO_AVISO_LGPD'] = retornaValorConfiguracao('CAMINHO_AVISO_LGPD');

		$queryLGPD  = ' SELECT DADOS_LGPD FROM PS1000 ';
		$queryLGPD .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$resLGPD = jn_query($queryLGPD);
		$rowLGPD = jn_fetch_object($resLGPD);
		
		if($rowLGPD->DADOS_LGPD){
			$retorno = '';
		}
	}
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] =='gravaCampoLGPD'){
	
	$queryLGPD  = ' UPDATE PS1000 SET DADOS_LGPD = ' . aspas('[IP - ' . $_SERVER["REMOTE_ADDR"] . ' - DT e HR - ' . date('d/m/Y') . ' : ' . date('H:i') . ']');
	$queryLGPD .= ' WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
	jn_query($queryLGPD);
	
	if($_SESSION['codigoSmart'] == '4012'){//RBS
	
		$publicKey = retornaValorConfiguracao('PUBLIC_KEY_LGPD');
		$consentHash = retornaValorConfiguracao('CONSENT_HASH_LGPD');
		
		/*
		--Apagar após OK da operadora
		$headers = array("publicauthorization: " . $publicKey);
		
		$url = 'https://dpo.privacytools.com.br/public_api/consent/multiple/' . $_SESSION['codigoIdentificacao'];
		
		$data = '{
					"encryptedTemplate": ' . $consentHash . ',
					"value": true					
				}';
		
		$ch = CURL_INIT();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);	
		$start = $info['header_size'];
		$body = substr($result, $start, strlen($result) - $start);
		curl_close($ch);
		*/
		
		$queryAssoc  = ' SELECT TOP 1 NUMERO_CPF, ENDERECO_EMAIL FROM PS1000 ';
		$queryAssoc .= ' INNER JOIN PS1015 ON (PS1000.CODIGO_ASSOCIADO = PS1015.CODIGO_ASSOCIADO) ';
		$queryAssoc .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$resAssoc = jn_query($queryAssoc);
		$rowAssoc = jn_fetch_object($resAssoc);
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://dpo.privacytools.com.br/external_api/consent/create-multiple-request',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>'{
		"consents": [
		{
		  "encryptedTemplate": "' . $consentHash . '"
		  "value": false
		}
		],
		"document": "' . $rowAssoc->NUMERO_CPF . '",
		"email": "' . $rowAssoc->ENDERECO_EMAIL . '",
		"identifyUser": "' . $_SESSION['codigoIdentificacao'] . '"
		}',
		CURLOPT_HTTPHEADER => array(
		'Authorization: Basic ' . $publicKey,
		'Content-Type: application/json'
		),
		));

		$response = curl_exec($curl);
		
		curl_close($curl);
	}
			
	echo json_encode($retorno);
}
else if($dadosInput['tipo'] =='retornarTabelasAuxiliares')
{

	$tabelas = aspas('NENHUMA'); // Só para náo dar erro

	if (($dadosInput['tipoTabelas'] =='CADASTRO_FATURAMENTO') or ($dadosInput['tipoTabelas'] =='TODAS'))		
		$tabelas .= aspas('PS1014') . ',' . aspas('PS1051') . ',' . aspas('PS1310') . ',' . aspas('PS1311') . ',' . aspas('PS1024') . ',' . 
		           aspas('PS1077') . ',' . aspas('PS1041') . ',' . aspas('PS1046') . ',' . aspas('PS1016') . ',' . aspas('PS1047') . ',' . 
		           aspas('AAA') . ',' . aspas('PS1097') . ',' . aspas('PS1040') . ',' . aspas('PS1044') . ',' . aspas('PS1045') . ',' . 
		           aspas('PS1027') . ',' . aspas('PS1064') . ',' . 
		           aspas('PS1067') . ',' . aspas('PS1098') . ',' . aspas('PS1058') . ',' . aspas('PS1064') . ',' . 
		           aspas('PS1065');

	if ($dadosInput['tipoTabelas'] =='CONFIGURACOES')		
	{
		$tabelas .= ',' . aspas('CFGTABELAS_SIS') . ',' . aspas('CFGCAMPOS_SIS_CD') . ',' . aspas('CFGSEQUENCIAS');
		$tabelas .= ',' . aspas('CFGCADPESSOAS') . ',' . aspas('CFGCAMPOS_PD') . ',' . aspas('CFGDISPAROPROCESSOSCABECALHO');
		$tabelas .= ',' . aspas('CFGDISPAROPROCESSOSPARAMETROS') . ',' . aspas('CFGEMPRESA') . ',' . aspas('CFGLAYOUTS');
		$tabelas .= ',' . aspas('CFGLAYOUTS_CAMPOS') . ',' . aspas('CFGLAYOUTS_MENSAGENS') . ',' . aspas('CFGPROCESSOS_PD');
		$tabelas .= ',' . aspas('CFGRELATORIOS_CAMPOS_PD') . ',' . aspas('CFGTABELAS_SUBPROCESSOS_CD') . ',' . aspas('CONFIGURACOES_ARQ');
	}

	if (($dadosInput['tipoTabelas'] =='CONTAS_MEDICAS') or ($dadosInput['tipoTabelas'] =='TODAS'))		
	{
		$tabelas .= ',' . aspas('PS5120') . ',' . aspas('PS1071') . ',' . aspas('PS1072');
		$tabelas .= ',' . aspas('PS1073') . ',' . aspas('PS1078') . ',' . aspas('PS5015');
		$tabelas .= ',' . aspas('PS5100') . ',' . aspas('PS5130') . ',' . aspas('PS5209');
		$tabelas .= ',' . aspas('PS5211') . ',' . aspas('PS5212') . ',' . aspas('PS5218');
		$tabelas .= ',' . aspas('PS5219') . ',' . aspas('PS5223') . ',' . aspas('PS5224');
		$tabelas .= ',' . aspas('PS5225') . ',' . aspas('PS5226') . ',' . aspas('PS5227');
		$tabelas .= ',' . aspas('PS5228') . ',' . aspas('PS5260') . ',' . aspas('PS5261');
		$tabelas .= ',' . aspas('PS5262') . ',' . aspas('PS5263') . ',' . aspas('PS5266');
		$tabelas .= ',' . aspas('PS5267') . ',' . aspas('PS5268') . ',' . aspas('PS5269');
		$tabelas .= ',' . aspas('PS5270') . ',' . aspas('PS5273') . ',' . aspas('PS5279');
		$tabelas .= ',' . aspas('PS5280') . ',' . aspas('PS5281') . ',' . aspas('PS5282');
		$tabelas .= ',' . aspas('PS5283') . ',' . aspas('PS5284') . ',' . aspas('PS5285');
		$tabelas .= ',' . aspas('PS5286') . ',' . aspas('PS5289') . ',' . aspas('PS5290');
		$tabelas .= ',' . aspas('PS5801') . ',' . aspas('PS5802') . ',' . aspas('PS5803');
		$tabelas .= ',' . aspas('PS5808') . ',' . aspas('PS5812') . ',' . aspas('PS5282');
	}

	if (($dadosInput['tipoTabelas'] =='FINANCEIRO') or ($dadosInput['tipoTabelas'] =='TODAS'))		
	{
		$tabelas .= ',' . aspas('PS7000') . ',' . aspas('PS7010') . ',' . aspas('PS7020');
		$tabelas .= ',' . aspas('PS7030') . ',' . aspas('PS7300') . ',' . aspas('PS7301');
		$tabelas .= ',' . aspas('PS7302') . ',' . aspas('PS7401') . ',' . aspas('PS7402');
	}

	if (($dadosInput['tipoTabelas'] =='COMERCIAL') or ($dadosInput['tipoTabelas'] =='TODAS'))		
	{
		$tabelas .= ',' . aspas('PS3007') . ',' . aspas('PS3020') . ',' . aspas('PS3040');
		$tabelas .= ',' . aspas('PS3050') . ',' . aspas('PS3300') . ',' . aspas('PS3400');
		$tabelas .= ',' . aspas('PS3500') . ',' . aspas('PS3300') . ',' . aspas('PS3400');
	}

	if (($dadosInput['tipoTabelas'] =='ATENDIMENTO') or ($dadosInput['tipoTabelas'] =='TODAS'))		
	{
		$tabelas .= ',' . aspas('PS6100') . ',' . aspas('aaaa') . ',' . aspas('PS6350');
		$tabelas .= ',' . aspas('PS6360') . ',' . aspas('PS6410') . ',' . aspas('AAAAAA');
	}

	if (($dadosInput['tipoTabelas'] =='ODONTO') or ($dadosInput['tipoTabelas'] =='TODAS'))		
	{
		$tabelas .= ',' . aspas('PS5120') . ',' . aspas('PS2000') . ',' . aspas('PS2005');
		$tabelas .= ',' . aspas('PS2040') . ',' . aspas('PS2041') . ',' . aspas('PS2050');
		$tabelas .= ',' . aspas('PS2215') . ',' . aspas('PS2216') . ',' . aspas('PS2217');
		$tabelas .= ',' . aspas('PS2218') . ',' . aspas('PS5211') . ',' . aspas('PS2217');
	}

	$query = 'Select Nome_Tabela, Descricao_Tabela From CfgTabelas_sis where nome_tabela in (' . $tabelas . ') order By Nome_Tabela';
	$res   = jn_query($query);
	$linha = Array();

	$corLinha = 'corImpar';

	while ($row = jn_fetch_object($res))
	{

	    $linha['VALOR']       		= jn_utf8_encode_AscII($row->NOME_TABELA);
	    $linha['DESC']        		= jn_utf8_encode_AscII($row->NOME_TABELA) . ' - '  . jn_utf8_encode_AscII($row->DESCRICAO_TABELA);
	    $linha['CLASS']		  		= $corLinha;
	    $linha['TIPO_COMPONENTE']	= 'BUTTON';

  		if ($corLinha=='corPar')
            $corLinha = 'corImpar';
        else
            $corLinha = 'corPar';	    	

		$retorno['TABELAS'][] = $linha;	    
	}

	echo json_encode($retorno);

}
else if($dadosInput['tipo'] =='retornarPermissoesMenu')
{

	$resPermissoes = jn_query('Select menu.numero_registro, menu.numero_registro_pai, 
								menu.label_menu, 
								menuPai.label_menu label_menu_Pai, 
								menuAvo.label_menu label_menu_avo,
								menu.numero_registro numero_registro_cfgPermissoes, 
								CFGPERMISSOES_MENU_NET.FLAG_POSSUI_ACESSO FLAG_POSSUI_ACESSO
								From CFGMENU_DINAMICO_NET_AL2 menu
								left outer join CFGMENU_DINAMICO_NET_AL2 menuPai on (menu.numero_registro_pai = menuPai.numero_registro)
								left outer join CFGMENU_DINAMICO_NET_AL2 menuAvo on (menuPai.numero_registro_pai = menuAvo.numero_registro)
								left outer join CFGPERMISSOES_MENU_NET on (menu.numero_registro = CFGPERMISSOES_MENU_NET.numero_registro_menu) 
							                                          and (CFGPERMISSOES_MENU_NET.Codigo_Identificacao = ' . aspas($dadosInput['chaveBusca']) . ')
									  where menu.flag_habilitado = ' . aspas('S') . ' and 
									        menu.perfis_visivel like ' . aspas('%OPERADOR%') . ' 
									        order by menu.numero_registro');

	$linha    = Array();
	$corLinha = 'corImpar';

	while ($row = jn_fetch_object($resPermissoes))
	{

	    $linha['CHAVE']       		= jn_utf8_encode_AscII($row->NUMERO_REGISTRO);
	    $linha['VALOR']       		= jn_utf8_encode_AscII($row->FLAG_POSSUI_ACESSO);
	    $linha['DESC']        		= retiraAcentos(jn_utf8_encode($row->LABEL_MENU_AVO) . 
	    											       '->' . retiraAcentos(jn_utf8_encode($row->LABEL_MENU_PAI)) . 
	    	                                               '->' . retiraAcentos(jn_utf8_encode($row->LABEL_MENU)));
	    $linha['CLASS']		  		= $corLinha;
	    $linha['VALOR_01']     		= $dadosInput['chaveBusca'];
	    $linha['TIPO_COMPONENTE']	= 'CHECKBOX';

  		if ($corLinha=='corPar')
            $corLinha = 'corImpar';
        else
            $corLinha = 'corPar';	    	

		$retorno['TABELAS'][] = $linha;	    
	}

	echo json_encode($retorno);

}



function validaPendencia($tabela){
	
	if($tabela == 'VW_COMUNICACAO_NET_AL2' && $_SESSION['perfilOperador'] == 'PRESTADOR' && $_SESSION['codigoSmart'] == '3423'){//Plena		
		$queryPendencia  = ' SELECT TOP 1 NUMERO_CAPA FROM ESP_PENDENCIAS_PRESTADOR_NET ';	
		$queryPendencia .= 'WHERE Data_Visualizacao IS NULL AND CODIGO_PRESTADOR = ' . aspas($_SESSION['codigoIdentificacao']);
		$resPendencia = jn_query($queryPendencia);
		if($rowPendencia = jn_fetch_object($resPendencia)){			
			return true;	
		}else{			
			return false;
		}
	}else{		
		return false;
	}
}

?>