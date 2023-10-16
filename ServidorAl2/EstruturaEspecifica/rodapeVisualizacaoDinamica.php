<?php

function rodapeVisualizacaoDinamica($tabela){
	
	$retorno['HTML']   = '';
	$retorno['STYLE_HTML']  = '';
	$retorno['CLASS_HTML']  = '';
	$retorno['TITULO'] = '';
	$retorno['STYLE_TITULO']  = '';
	$retorno['CLASS_TITULO']  = 'text-center';
	
	if($tabela =='VW_REDECREDENCIADA_AL2'){
		$retorno['HTML']= "<table  align='center'><tbody><tr><td style='padding:5px'><img class='img' style='height: 16px !important; width: auto !important;' alt='Padrão internacional de qualidade' title='Padrão internacional de qualidade' src='assets/img/Acba.jpg'></td><td>Padrão internacional de qualidade</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Padrão nacional de qualidade' title='Padrão nacional de qualidade' '='' src='assets/img/Adicq.jpg'></td><td>Padrão nacional de qualidade</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Padrão internacional de qualidade' title='Padrão internacional de qualidade' src='assets/img/Aiqg.jpg'></td><td>Padrão internacional de qualidade</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Padrão nacional de qualidade' title='Padrão nacional de qualidade' src='assets/img/Aona.jpg'></td><td>Padrão nacional de qualidade</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Padrão nacional de qualidade' title='Padrão nacional de qualidade' src='assets/img/Apalc.jpg'></td><td>Padrão nacional de qualidade</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Título de Especialista' title='Título de Especialista' src='assets/img/E.jpg'></td><td>Título de Especialista</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Comunicação de eventos adversos' title='Comunicação de eventos adversos' src='assets/img/N.jpg'></td><td>Comunicação de eventos adversos</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Profissional com especialização' title='Profissional com especialização' src='assets/img/P.jpg'></td><td>Profissional com especialização</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Qualidade monitorada' title='Qualidade monitorada' src='assets/img/Q.jpg'></td><td>Qualidade monitorada</td></tr><tr><td style='padding:5px'><img style='height: 16px !important; width: auto !important;' alt='Profissional com residência' title='Profissional com residência' src='assets/img/R.jpg'></td><td>Profissional com residência</td></tr></tbody></table>";
		$retorno['TITULO'] = 'Legenda';
	}
	
	return $retorno;

}

?>