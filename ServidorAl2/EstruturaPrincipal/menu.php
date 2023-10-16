<?php

require('../lib/base.php');
require('../private/autentica.php');

// Aqui eu limpo o protolo, pq se o cara navegou é porque ele mudou de usuário.
$_SESSION['NUMERO_PROTOCOLO_ATIVO'] = '';

if($dadosInput['tipo']=='dados')
{


	if ($_SESSION['AliancaPx4Net'] == 'S')
	{

			$query  = ' SELECT  CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO, CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO_PAI, CFGMENU_DINAMICO_NET_AL2.LABEL_MENU,
			                    CFGMENU_DINAMICO_NET_AL2.PERFIS_VISIVEL, CFGMENU_DINAMICO_NET_AL2.LINK_PAGINA, CFGPERMISSOES_MENU_NET.FLAG_POSSUI_ACESSO, 
			                    COALESCE(CFGMENU_DINAMICO_NET_AL2.ICONE,' . aspas('format_list_bulleted') . ') ICONE, CFGMENU_DINAMICO_NET_AL2.LINK_EXTERNO,
			                    CFGMENU_DINAMICO_NET_AL2.DADOS_LINK_PAGINA, CFGMENU_DINAMICO_NET_AL2.DESCRICAO_MENU, ';
			$query .= ' (SELECT COUNT(NUMERO_REGISTRO_PAI) FROM CFGMENU_DINAMICO_NET_AL2 MENU_PAI WHERE ((MENU_PAI.PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(MENU_PAI.PERFIS_VISIVEL Like "%TODOS%")) and MENU_PAI.NUMERO_REGISTRO_PAI = CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO and MENU_PAI.NUMERO_REGISTRO <> CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO ) as REGISTRO_PAI ';
			$query .= ' FROM CFGMENU_DINAMICO_NET_AL2 ';
			$query .= ' INNER JOIN CFGEMPRESA ON (1=1) ';
			$query .= ' LEFT OUTER JOIN CFGPERMISSOES_MENU_NET ON (CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO = CFGPERMISSOES_MENU_NET.NUMERO_REGISTRO_MENU) ';
			$query .= ' WHERE CFGMENU_DINAMICO_NET_AL2.FLAG_HABILITADO = "S" AND ((CFGMENU_DINAMICO_NET_AL2.PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(CFGMENU_DINAMICO_NET_AL2.PERFIS_VISIVEL Like "%TODOS%")) ';
			$query .= '	AND  ((CFGMENU_DINAMICO_NET_AL2.TIPO_FILTRO_CLIENTE LIKE "%"||CFGEMPRESA.TIPO_CLIENTE_ALIANCA||"%") OR (CFGMENU_DINAMICO_NET_AL2.TIPO_FILTRO_CLIENTE LIKE "%TODOS%")) ' . $complementoBeneficiario;
			$query .= '	ORDER BY NUMERO_REGISTRO_PAI, NUMERO_REGISTRO ';	

	}
	else
	{

			$complementoBeneficiario = '';
			
			if ($_SESSION['perfilOperador']=='BENEFICIARIO')
			{
		    	$query  = ' SELECT  FLAG_PLANOFAMILIAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
				$res    = jn_query($query);
			    $row    = jn_fetch_object($res);
				
				if ($row->FLAG_PLANOFAMILIAR == 'S')
					$complementoBeneficiario = ' and (PERFIS_VISIVEL NOT LIKE "%BENEFICIARIO_PJ%") ';
				else
					$complementoBeneficiario = ' and (PERFIS_VISIVEL NOT LIKE "%BENEFICIARIO_PF%") ';
			}
			
			$query  = ' SELECT  NUMERO_REGISTRO, NUMERO_REGISTRO_PAI, LABEL_MENU, PERFIS_VISIVEL, LINK_PAGINA, 
			                    COALESCE(ICONE,' . aspas('format_list_bulleted') . ') ICONE,LINK_EXTERNO,DADOS_LINK_PAGINA, 
			                    CFGMENU_DINAMICO_NET_AL2.DESCRICAO_MENU, ';
			$query .= ' (SELECT COUNT(NUMERO_REGISTRO_PAI) FROM CFGMENU_DINAMICO_NET_AL2 MENU_PAI WHERE ((MENU_PAI.PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(MENU_PAI.PERFIS_VISIVEL Like "%TODOS%")) and MENU_PAI.NUMERO_REGISTRO_PAI = CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO and MENU_PAI.NUMERO_REGISTRO <> CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO ) as REGISTRO_PAI ';
			$query .= ' FROM CFGMENU_DINAMICO_NET_AL2 ';
			$query .= ' INNER JOIN CFGEMPRESA ON (1=1) ';
			$query .= ' WHERE FLAG_HABILITADO = "S" AND ((PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(PERFIS_VISIVEL Like "%TODOS%")) ';
			$query .= '	AND  ((CFGMENU_DINAMICO_NET_AL2.TIPO_FILTRO_CLIENTE LIKE "%"||CFGEMPRESA.TIPO_CLIENTE_ALIANCA||"%") OR (CFGMENU_DINAMICO_NET_AL2.TIPO_FILTRO_CLIENTE LIKE "%TODOS%")) ' . $complementoBeneficiario;
			
			if ($dadosInput['filtro'] == 'atalhos')
				$query .= '	and LINK_PAGINA IS NOT NULL and numero_registro >= 1 ';
			
			if ($_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF')
				$query .= ' and PERFIS_VISIVEL LIKE "%BENEFICIARIO_CPF%" ';


			if($_SESSION['codigoSmart'] == '3808'){//RS Saude

				$queryInscSusep = "SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA";
				$resultInscSusep = jn_query($queryInscSusep);
				$rowInscSusep = jn_fetch_object($resultInscSusep);
				
				if($rowInscSusep->NUMERO_INSC_SUSEP == '418218' && $_SESSION['perfilOperador'] == 'OPERADOR'){ //Quando for Saude Rural
					if ($_SESSION['codigoIdentificacao'] == '70' || $_SESSION['codigoIdentificacao'] == '17')
						$query .= ' and NUMERO_REGISTRO = "161" ';
				}elseif($rowInscSusep->NUMERO_INSC_SUSEP == '416819' && $_SESSION['perfilOperador'] == 'BENEFICIARIO'){ //Quando for GKN - BENEF
					if ($_SESSION['codigoIdentificacao'] == '201050531701000')
						$query .= ' OR NUMERO_REGISTRO = "178" ';
				}elseif($rowInscSusep->NUMERO_INSC_SUSEP == '416819' && $_SESSION['perfilOperador'] == 'OPERADOR'){ //Quando for GKN - OPERADOR
					if ($_SESSION['codigoIdentificacao'] == '52')
						$query .= ' AND NUMERO_REGISTRO IN("6", "7", "178" ) ';
				}elseif($rowInscSusep->NUMERO_INSC_SUSEP == '416118' && $_SESSION['perfilOperador'] == 'OPERADOR'){ //Quando for Dana - OPERADOR		
					$opEnf = array("13", "14", "17", "18","42","45","46"); 
					
					if (in_array($_SESSION['codigoIdentificacao'], $opEnf)) {
						$query .= ' AND NUMERO_REGISTRO IN("15", "180", "181") ';	
					}							
						
				}elseif($rowInscSusep->NUMERO_INSC_SUSEP == '416118' && $_SESSION['perfilOperador'] == 'PRESTADOR'){ //Quando for Dana - PRESTADOR					
					if ($_SESSION['codigoIdentificacao'] == '1824') {
						$query .= ' AND NUMERO_REGISTRO = "15" OR (NUMERO_REGISTRO IN ("6","16")) ';	
					}							
						
				}
			}


			
			$query .= '	ORDER BY NUMERO_REGISTRO_PAI, NUMERO_REGISTRO ';	

			if($_SESSION['AUDITOR'] == 'S' && $_SESSION['codigoSmart'] == '3423'){
				$query  = ' SELECT  NUMERO_REGISTRO, NUMERO_REGISTRO_PAI, LABEL_MENU, PERFIS_VISIVEL, LINK_PAGINA, ICONE,LINK_EXTERNO,DADOS_LINK_PAGINA, CFGMENU_DINAMICO_NET_AL2.DESCRICAO_MENU, ';
				$query .= ' (SELECT COUNT(NUMERO_REGISTRO_PAI) FROM CFGMENU_DINAMICO_NET_AL2 MENU_PAI WHERE ((MENU_PAI.PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(MENU_PAI.PERFIS_VISIVEL Like "%TODOS%")) and MENU_PAI.NUMERO_REGISTRO_PAI = CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO and MENU_PAI.NUMERO_REGISTRO <> CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO ) as REGISTRO_PAI ';
				$query .= ' FROM CFGMENU_DINAMICO_NET_AL2 ';
				$query .= ' WHERE NUMERO_REGISTRO IN (90,144, 30, 31, 32)';
				
			}
			
			if($_SESSION['AUDITOR_AGENDAMENTO'] == 'S' && $_SESSION['codigoSmart'] == '3423'){
				$query  = ' SELECT  NUMERO_REGISTRO, NUMERO_REGISTRO_PAI, LABEL_MENU, PERFIS_VISIVEL, LINK_PAGINA, ICONE,LINK_EXTERNO,DADOS_LINK_PAGINA, CFGMENU_DINAMICO_NET_AL2.DESCRICAO_MENU, ';
				$query .= ' (SELECT COUNT(NUMERO_REGISTRO_PAI) FROM CFGMENU_DINAMICO_NET_AL2 MENU_PAI WHERE ((MENU_PAI.PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(MENU_PAI.PERFIS_VISIVEL Like "%TODOS%")) and MENU_PAI.NUMERO_REGISTRO_PAI = CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO and MENU_PAI.NUMERO_REGISTRO <> CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO ) as REGISTRO_PAI ';
				$query .= ' FROM CFGMENU_DINAMICO_NET_AL2 ';
				$query .= ' WHERE NUMERO_REGISTRO IN (0, 30, 31, 32)';
				
			}
			
			if($_SESSION['perfilOperador'] == 'BENEFICIARIO' && $_SESSION['codigoSmart'] == '3423'){//Plena
				$queryBenef = 'SELECT * FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . $_SESSION['codigoIdentificacao'];
				$resBenef = jn_query($queryBenef);
				$rowBenef = jn_fetch_object($resBenef);	
				
				$query  = ' SELECT  NUMERO_REGISTRO, NUMERO_REGISTRO_PAI, LABEL_MENU, PERFIS_VISIVEL, LINK_PAGINA, ICONE,LINK_EXTERNO,DADOS_LINK_PAGINA, CFGMENU_DINAMICO_NET_AL2.DESCRICAO_MENU, ';
				$query .= ' (SELECT COUNT(NUMERO_REGISTRO_PAI) FROM CFGMENU_DINAMICO_NET_AL2 MENU_PAI WHERE ((MENU_PAI.PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(MENU_PAI.PERFIS_VISIVEL Like "%TODOS%")) and MENU_PAI.NUMERO_REGISTRO_PAI = CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO and MENU_PAI.NUMERO_REGISTRO <> CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO ) as REGISTRO_PAI ';
				$query .= ' FROM CFGMENU_DINAMICO_NET_AL2 ';
				$query .= ' INNER JOIN CFGEMPRESA ON (1=1) ';
				$query .= ' WHERE FLAG_HABILITADO = "S" AND ((PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(PERFIS_VISIVEL Like "%TODOS%")) ';
				$query .= '	AND  ((CFGMENU_DINAMICO_NET_AL2.TIPO_FILTRO_CLIENTE LIKE "%"||CFGEMPRESA.TIPO_CLIENTE_ALIANCA||"%") OR (CFGMENU_DINAMICO_NET_AL2.TIPO_FILTRO_CLIENTE LIKE "%TODOS%")) ' . $complementoBeneficiario;
				
				if ($dadosInput['filtro'] == 'atalhos')
					$query .= '	and LINK_PAGINA IS NOT NULL and numero_registro >= 1 ';

				if ($dadosInput['filtro'] == 'atalhos' && $_SESSION['tipoMenu'] == 'OCULTO')
					$query .= ' and (1 = 2) ';
			
				if($rowBenef->CODIGO_TIPO_CARACTERISTICA == '10'){//Odonto
					$query .= '	AND (CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO NOT IN ("14","50","60","137","180","162","300","15", "327","328","329","144","135"))';				
					
					if($rowBenef->FLAG_PLANOFAMILIAR == 'N'){
						$query .= '	AND (CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO NOT IN ("40"))';				
					}
				}else{//Medicina			
					$query .= '	AND (CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO NOT IN ("6"))';				
					
					if($rowBenef->FLAG_PLANOFAMILIAR == 'N'){
						$query .= '	AND (CFGMENU_DINAMICO_NET_AL2.NUMERO_REGISTRO NOT IN ("40"))';				
					}
				}
				
				$query .= '	ORDER BY 	CASE 
											WHEN NUMERO_REGISTRO_PAI IS NOT NULL 
												THEN NUMERO_REGISTRO_PAI 
											ELSE NUMERO_REGISTRO 
										END, NUMERO_REGISTRO ';	
			}

	}
	
	//pr($_SESSION,true);
	//pr($query);
	$res = jn_query($query);

	// Array que armazena os dados vindo da tabela de logins da web
	$quantidadeUsuarios = 0;

	$aux3niveis = array();
	$cor = '#E8FBFF';
	
	while($row = jn_fetch_object($res))
	{
	
		if ($row->FLAG_POSSUI_ACESSO=='N')
		{
			continue;
		}

		if ($cor == '#E8FBFF')
			$cor = '#E6FFEE';
		else if ($cor == '#E6FFEE')
			$cor = '#FFF0F0';
		else if ($cor == '#FFF0F0')
			$cor = '#FFFBEC';
		else if ($cor == '#FFFBEC')
			$cor = '#F5F5F5';
		else
			$cor = '#E8FBFF';

		
		if ($dadosInput['filtro'] == 'atalhos')
		{
			$menu[$row->NUMERO_REGISTRO]['state'] = $row->LINK_PAGINA;
			$menu[$row->NUMERO_REGISTRO]['state_dados'] = json_decode(jn_utf8_encode($row->DADOS_LINK_PAGINA));
			$menu[$row->NUMERO_REGISTRO]['name'] = jn_utf8_encode($row->LABEL_MENU);
			$menu[$row->NUMERO_REGISTRO]['label'] = '';

			if ($row->DESCRICAO_MENU!='')
			   $menu[$row->NUMERO_REGISTRO]['descr'] = jn_utf8_encode($row->DESCRICAO_MENU);
			else
			   $menu[$row->NUMERO_REGISTRO]['descr'] = jn_utf8_encode($row->LABEL_MENU);

			$menu[$row->NUMERO_REGISTRO]['icon'] = $row->ICONE;
			$menu[$row->NUMERO_REGISTRO]['type'] = 'button';
			$menu[$row->NUMERO_REGISTRO]['link'] = $row->LINK_EXTERNO;

			$menu[$row->NUMERO_REGISTRO]['cor'] = $cor;
					
		}
		else
		{
			
			if(($row->NUMERO_REGISTRO_PAI=='')and($row->LINK_EXTERNO=='')and($row->LINK_PAGINA==''))
			{
				$menu[$row->NUMERO_REGISTRO]['state'] = '';
				$menu[$row->NUMERO_REGISTRO]['name'] = jn_utf8_encode($row->LABEL_MENU);
				$menu[$row->NUMERO_REGISTRO]['label'] = '';
				$menu[$row->NUMERO_REGISTRO]['icon'] = $row->ICONE;
				$menu[$row->NUMERO_REGISTRO]['type'] = 'sub';
				$menu[$row->NUMERO_REGISTRO]['link'] = $row->LINK_EXTERNO;
			}
			elseif($row->NUMERO_REGISTRO_PAI!='')
			{
				$filho['state'] = $row->LINK_PAGINA;
				$filho['registro'] = $row->NUMERO_REGISTRO;
				$filho['state_dados'] = json_decode(jn_utf8_encode($row->DADOS_LINK_PAGINA));
				$filho['icon'] = $row->ICONE;
				$filho['name'] = jn_utf8_encode($row->LABEL_MENU) ;
				$filho['link'] = $row->LINK_EXTERNO;
				$filho['descr'] = jn_utf8_encode($row->DESCRICAO_MENU);

				$filho['type'] = 'button';
				if($row->REGISTRO_PAI>0){
					$filho['type'] = 'sub';
				}	
				//pr($aux3niveis[$row->NUMERO_REGISTRO_PAI]); 
				//pr($row->NUMERO_REGISTRO);
				//pr($aux3niveis);

				if($aux3niveis[$row->NUMERO_REGISTRO_PAI]>0)
				{
					$menu[(int) $aux3niveis[$row->NUMERO_REGISTRO_PAI]]['children'][$row->NUMERO_REGISTRO_PAI]['children'][$row->NUMERO_REGISTRO] = $filho;
				}
				else
				{
					$menu[$row->NUMERO_REGISTRO_PAI]['children'][$row->NUMERO_REGISTRO] = $filho;
					$menu[$row->NUMERO_REGISTRO_PAI]['children'][$row->NUMERO_REGISTRO]['cor'] = $cor;
				}

				$aux3niveis[$row->NUMERO_REGISTRO] = $row->NUMERO_REGISTRO_PAI;
				
			}elseif($row->LINK_EXTERNO != ''){
				$menu[$row->NUMERO_REGISTRO]['state'] = $row->LINK_PAGINA;
				$menu[$row->NUMERO_REGISTRO]['state_dados'] = '';
				$menu[$row->NUMERO_REGISTRO]['name'] = jn_utf8_encode($row->LABEL_MENU);
				$menu[$row->NUMERO_REGISTRO]['label'] = '';
				$menu[$row->NUMERO_REGISTRO]['icon'] = $row->ICONE;
				$menu[$row->NUMERO_REGISTRO]['type'] = 'link';
				$menu[$row->NUMERO_REGISTRO]['link'] = $row->LINK_EXTERNO;
			}else{
				$menu[$row->NUMERO_REGISTRO]['state'] = $row->LINK_PAGINA;
				$menu[$row->NUMERO_REGISTRO]['state_dados'] = json_decode(jn_utf8_encode($row->DADOS_LINK_PAGINA));
				$menu[$row->NUMERO_REGISTRO]['name'] = jn_utf8_encode($row->LABEL_MENU);
				$menu[$row->NUMERO_REGISTRO]['label'] = '';
				$menu[$row->NUMERO_REGISTRO]['icon'] = $row->ICONE;
				$menu[$row->NUMERO_REGISTRO]['type'] = 'button';
				$menu[$row->NUMERO_REGISTRO]['link'] = $row->LINK_EXTERNO;
			}
		}
	}
	
	if (($_SESSION['codigoSmart'] == '4018') and ($_SESSION['perfilOperador'] == 'BENEFICIARIO'))
	{
		$query = 'SELECT CODIGO_GRUPO_CONTRATO FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$res   = jn_query($query);
		$row   = jn_fetch_object($res);
		
		$codigoGrupoContrato       = $row->CODIGO_GRUPO_CONTRATO;
		
		$menu[1223]['state']       = 'site/paginaExterna';
		$menu[1223]['state_dados'] = '';
		$menu[1223]['name']        = 'Rede Credenciada';
		$menu[1223]['label']       = '';
		$menu[1223]['icon']        = 'domain';
		$menu[1223]['type']        = 'link';
		$menu[1223]['link']        = '';  
		$menu[1223]['icon']        = 'domain';
		$menu[1223]['descr']       = 'Consulte aqui nossa rede credenciada';
		
		if (($codigoGrupoContrato==10)||($codigoGrupoContrato==11)||($codigoGrupoContrato==13)||($codigoGrupoContrato==14)||($codigoGrupoContrato==15)||
		    ($codigoGrupoContrato==16)||($codigoGrupoContrato==17)){
			$menu[1223]['link'] = 'https://www.ameplansaude.com.br/ameplanservices/redes';  		
		}else if ($codigoGrupoContrato==18){
			$menu[1223]['link'] = 'http://totalmedcare.hasp.org.br:55222/Busca/NaoCliente.aspx';
		}else if ($codigoGrupoContrato==12){
			$menu[1223]['link'] = 'https://app.plenasaude.com.br/AliancaNet/html/frm_localiza_rede.php';
		}else if ($codigoGrupoContrato==9){
			$menu[1223]['link'] = 'https://dentalpar.s4e.com.br/SYS/Rede_Atendimento/Rede_Atendimento.aspx?modal=1';
		}else if (($codigoGrupoContrato==5)||($codigoGrupoContrato==6)){
			$menu[1223]['link'] = 'https://portal.bdconnect.com.br/portal/?op=5&r=09#!/rede';  		
		}else if ($codigoGrupoContrato==7){
			$menu[1223]['link'] = 'https://www.odontoprevonline.com.br/encontre-um-dentista';  		
		}else if ($codigoGrupoContrato==8){
			$menu[1223]['link'] = 'https://medical.profex.com.br/portal-jave/AliancaNet/html/frm_localiza_rede.php';  		
		}
		
	}
	
	foreach ($menu as $value){
		$retorno[]=$value;
	} 
	echo json_encode($retorno);
}
else if($dadosInput['tipo']=='menusuperior')
{
	
	$query  = ' SELECT  NUMERO_REGISTRO, NUMERO_REGISTRO_PAI, LABEL_MENU, PERFIS_VISIVEL, LINK_PAGINA, ICONE,LINK_EXTERNO,DADOS_LINK_PAGINA ';
	$query .= ' FROM CFGMENU_DINAMICO_NET_AL2 ';
	$query .= ' WHERE FLAG_MENU_SUPERIOR = "S" AND ((PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(PERFIS_VISIVEL Like "%TODOS%")) and LINK_PAGINA IS NOT NULL ';
	
	// Se for auto-contratação não quero que exiba nenhum menu superior
	if ($_SESSION['perfilOperador'] == 'AUTOCONTRATACAO' || $_SESSION['tipoMenu'] == 'OCULTO')
		$query .= ' and (1 = 2) ';
	
	if ($_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF')
		$query .= ' and PERFIS_VISIVEL LIKE "%BENEFICIARIO_CPF%" ';

	if($_SESSION['codigoSmart'] == '3808'){//RS Saude

		$queryInscSusep = "SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA";
		$resultInscSusep = jn_query($queryInscSusep);
		$rowInscSusep = jn_fetch_object($resultInscSusep);
		
		if($rowInscSusep->NUMERO_INSC_SUSEP == '418218' && $_SESSION['perfilOperador'] == 'OPERADOR'){ //Quando for Saude Rural
			if ($_SESSION['codigoIdentificacao'] == '70' || $_SESSION['codigoIdentificacao'] == '17')
				$query .= ' and NUMERO_REGISTRO = "161" ';
		}elseif($rowInscSusep->NUMERO_INSC_SUSEP == '416819' && $_SESSION['perfilOperador'] == 'OPERADOR'){ //Quando for GKN - OPERADOR
			if ($_SESSION['codigoIdentificacao'] == '52')
				$query .= ' AND NUMERO_REGISTRO IN("6", "7", "178" ) ';
		}elseif($rowInscSusep->NUMERO_INSC_SUSEP == '416118' && $_SESSION['perfilOperador'] == 'OPERADOR'){ //Quando for Dana - OPERADOR		
			$opEnf = array("13", "14", "17", "18","42","45","46"); 
			
			if (in_array($_SESSION['codigoIdentificacao'], $opEnf)) {
				$query .= ' AND NUMERO_REGISTRO IN("15", "180", "181") ';	
			}							
				
		}elseif($rowInscSusep->NUMERO_INSC_SUSEP == '416118' && $_SESSION['perfilOperador'] == 'PRESTADOR'){ //Quando for Dana - PRESTADOR					
			if ($_SESSION['codigoIdentificacao'] == '1824') {//Prestador Prestus
				$query .= ' AND NUMERO_REGISTRO = "15" ';	
			}
		}
	}
	
	$query .= '	ORDER BY NUMERO_REGISTRO_PAI, NUMERO_REGISTRO ';	
	$res = jn_query($query);

	// Array que armazena os dados vindo da tabela de logins da web
	$quantidadeUsuarios = 0;
	$itemMenu = array();
	$retorno  = array();

	$cor      = '#67BC69';

	while($row = jn_fetch_object($res))
	{
		$itemMenu['LINKPAGINA']      = $row->LINK_PAGINA;
		$itemMenu['DADOSLINKPAGINA'] = json_decode(jn_utf8_encode($row->DADOS_LINK_PAGINA));
		$itemMenu['LABELMENU']       = jn_utf8_encode($row->LABEL_MENU);
		$itemMenu['ICONE']           = $row->ICONE;
		$itemMenu['CORBACKGROUND']   = $cor;
			
		if ($cor == '#67BC69')
			$cor = '#EC5355';
		else if ($cor == '#EC5355')
			$cor = '#40ABFB';
		else if ($cor == '#40ABFB')
			$cor = '#F39F18';
		else if ($cor == '#F39F18')
			$cor = '#CCCCCC';
		else
			$cor = '#67BC69';


		$retorno[] = $itemMenu;
	}
	
	echo json_encode($retorno);
	
}
else if($dadosInput['tipo']=='qtdMensagens')
{

	$query = 'SELECT coalesce(count(*),0) QUANTIDADE from PS6451 WHERE  DATA_VISUALIZACAO IS NULL AND CODIGO_DESTINATARIO = '. aspas($_SESSION['codigoIdentificacao']);
	$res   = jn_query($query);
	
	//pr($query);
	
	$resultado['QUANT_PS6451'] = '0';
	
	if($row = jn_fetch_object($res))
	{	
    	$resultado['QUANT_PS6451'] = $row->QUANTIDADE;
	}

    //
	
	$query = 'SELECT coalesce(count(*),0) QUANTIDADE from CFGCOMUNICACAO_NET WHERE 
	          ((PERFIS_VISIVEL Like "%' . $_SESSION['perfilOperador'] . '%")or(PERFIS_VISIVEL Like "%TODOS%")) and 
	          (FILTRO_CODIGOS LIKE ' . aspas('%' . $_SESSION['codigoIdentificacao'] . '%') . ' OR FILTRO_CODIGOS LIKE "%TODOS%")';
	$res   = jn_query($query);
	
	$resultado['QUANT_CFGCOMUNICACAO_NET'] = '0';
	
	if($row = jn_fetch_object($res))
	{	
    	$resultado['QUANT_CFGCOMUNICACAO_NET'] = $row->QUANTIDADE;
	}
	
	echo json_encode($resultado);

}	
else if($dadosInput['tipo']=='registraVisualizacao')
{

	$query = 'UPDATE PS6451 SET DATA_VISUALIZACAO = current_timestamp WHERE CODIGO_DESTINATARIO = '. aspas($_SESSION['codigoIdentificacao']);
	$res   = jn_query($query);
	
	$resultado['QUANT_PS6451'] = 'OK';

	echo json_encode($resultado);

}	
else if($dadosInput['tipo']=='menuSuperior')
{
	
	$ocultarMenu = retornaValorConfiguracao('OCULTAR_MENU_SUPERIOR');
	

	if($_SESSION['tipoMenu'] == 'OCULTO'){
		$ocultarMenu = 'SIM';
	}

	if($ocultarMenu == ''){
		$ocultarMenu = 'NAO';
	}
	
	$resultado['OCULTAR_MENU'] = $ocultarMenu;
	
	echo json_encode($resultado);

}
else if($dadosInput['tipo']=='descricaoBotao')
{
	
	$descBotao = retornaValorConfiguracao('DESCRICAO_BOTAO_CABECALHO');
	
	if($descBotao == ''){
		$descBotao = 'NAO';
	}
	
	$resultado['DESCRICAO_BOTAO_CABECALHO'] = $descBotao;
	
	echo json_encode($resultado);

}elseif($dadosInput['tipo']== 'configuracoes'){
	$retorno = Array();

	$configuracaoMenu = retornaValorConfiguracao('APRESENTAR_LOGO_MENU');
	if($configuracaoMenu){
		$retorno['APRESENTAR_LOGO_MENU'] = $configuracaoMenu;
	}else{
		$retorno['APRESENTAR_LOGO_MENU'] = 'SIM';
	}	

	echo json_encode($retorno);
}elseif($dadosInput['tipo']== 'inep'){
	$retorno = Array();
	
		$queryPrincipal = "select COUNT(*) as REGISTROS from ESP_PUSH_INADIM where codigo_associado = ".aspas($_SESSION['codigoIdentificacao'])." and DATA_LEITURA is null";
		$resultQuery = jn_query($queryPrincipal);
		if ($objResult = jn_fetch_object($resultQuery)){
			$retorno['REGISTROS'] = $objResult->REGISTROS;
			$retorno['MENSAGEM']  = "Você deve ler as notificações antes de continuar.";
		}	

	echo json_encode($retorno);
}elseif($dadosInput['tipo']== 'menuApp'){
	global $celular;
	$retorno = Array();
	$retorno['RATIO_W'] = '3';
	$retorno['RATIO_H'] = '4';
	$retorno['SIZE'] = '4';
	
	$complementoBeneficiario = '';
	
	if ($_SESSION['perfilOperador']=='BENEFICIARIO')
	{
    	$query  = ' SELECT  FLAG_PLANOFAMILIAR FROM PS1000 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$res    = jn_query($query);
	    $row    = jn_fetch_object($res);
		
		if ($row->FLAG_PLANOFAMILIAR == 'S')
			$complementoBeneficiario = ' and (PERFIS_VISIVEL NOT LIKE "%BENEFICIARIO_PJ%") ';
		else
			$complementoBeneficiario = ' and (PERFIS_VISIVEL NOT LIKE "%BENEFICIARIO_PF%") ';
	}
	
	$queryPrincipal = "SELECT * from CFGMENU_DINAMICO_NET_AL2 WHERE FLAG_APP_HABILITADO = 'S' and COALESCE(APP_FTP_CAMINHO_IMAGEM,'')<>'' and ((PERFIS_VISIVEL Like '%" . $_SESSION['perfilOperador'] . "%')or(PERFIS_VISIVEL Like '%TODOS%')) ".$complementoBeneficiario ." ORDER BY APP_ORDEM";
	$resultQuery = jn_query($queryPrincipal);
	while($objResult = jn_fetch_object($resultQuery)){
		$item = Array();
		$path = $objResult->APP_FTP_CAMINHO_IMAGEM;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		$item['imagem'] = $base64;
		$item['state'] = $objResult->LINK_PAGINA;
		$item['state_dados'] = json_decode(jn_utf8_encode($objResult->DADOS_LINK_PAGINA));
		$item['link'] = $objResult->LINK_EXTERNO;
		$retorno['MENU'][] = $item;
		
	}	

	echo json_encode($retorno);
}



?>