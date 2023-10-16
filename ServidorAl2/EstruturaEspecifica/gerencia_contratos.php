<?php

// LEO, POR FAVOR VALIDAR: SE O PERFIL FOR OPERADOR EU ESTOU SAINDO DA ROTINA, POIS É O CASO DO PREENCHIMENTO DA DECLARAÇAO DE SAUDE
// PELO ERP E NÃO PELO PORTAL.

require('../lib/base.php');
require('../private/autentica.php');

if ($_SESSION['perfilOperador'] == 'OPERADOR')
{
	$retorno['STATUS'] = 'OK';
	$retorno['MSG'] = 'Rotina de envio de e-mail não necessária, pois o perfil é operador';
	echo json_encode($retorno);
	return;
}


require_once('../lib/base.php');
require_once('../lib/mpdf60/mpdf.php');
header ('Content-type: text/html; charset=ISO-8859-1');

global $codAssociadoTmp, $caminhoArquivo;



$codAssociadoTmp = $_GET['codAssociado'] ? $_GET['codAssociado'] : $dadosInput['codAssociado'];
$codEmpresaTmp   = $_GET['codEmpresa'] ? $_GET['codEmpresa'] : $dadosInput['codEmpresa'];

if($codAssociadoTmp == '' && $_SESSION['perfilOperador'] == 'BENEFICIARIO_VO'){	
	$codAssociadoTmp = $_SESSION['codigoIdentificacao'];
}

$codAssociadoTmp = trim($codAssociadoTmp);
if($codAssociadoTmp == ''){
	echo 'Associado nao encontrado.';
	exit;
}

$numeroContrato = '';
$valorTotal = 0;
$statusRegistrado = false;
$caminhoArquivo = retornaValorConfiguracao('ESTRUTURA_CONTRATOS');
$utilizaPadraoHTML = retornaValorConfiguracao('UTILIZA_PADRAO_HTML');
$codigoPlano = '';
$percentual = 0;
$tipoCalculo = 0;
$numeroProtocolo = '';

if($_SESSION['codigoSmart'] == ''){
	$querySmart = 'SELECT CODIGO_SMART FROM CFGEMPRESA';
	$resSmart = jn_query($querySmart);
	$rowSmart = jn_fetch_object($resSmart);
	$_SESSION['codigoSmart'] = $rowSmart->CODIGO_SMART;
}


if (($codEmpresaTmp != '') and ($codEmpresaTmp != null) and ($codEmpresaTmp != '400'))
{
	registraStatusEmpresa($codEmpresaTmp);
}
else
{
	if($_SESSION['codigoSmart'] == '4022'){
		trataContratoOdontoMais();
		return false;
	}elseif($_SESSION['codigoSmart'] == '4246'){
		trataContratoMV2C();
		return false;
	}elseif($_SESSION['codigoSmart'] == '777777'){
		trataContratoStaCasaMaua();
		return false;
	}elseif($_SESSION['codigoSmart'] == '4010'){
		trataContratoClasse();
		return false;
	}elseif($_SESSION['codigoSmart'] == '3389'){		
		trataContratoVidamax();	
		return false;
	}elseif($_SESSION['codigoSmart'] == '4200'){
		trataContratoPropulsao();	
		return false;
	}elseif($_SESSION['codigoSmart'] == '4018'){
		trataContratoHebrom();	
		return false;
	}elseif($_SESSION['codigoSmart'] == '4206'){
		trataContratoVixMed();	
		return false;
	}elseif($_SESSION['codigoSmart'] == '4277'){
		trataContratoDemaisDescontos();	
		return false;
	}elseif($_SESSION['codigoSmart'] == '4306'){
		trataContratoVileve();	
		return false;
	}elseif($_SESSION['codigoSmart'] == '4318'){
		trataContratoSomarMaisSaude();	
		return false;
	}
	

	if($utilizaPadraoHTML == 'SIM'){
		geraContratoPadraoHTML();
	}
}





function geraContratoPadraoHTML(){
	global $codAssociadoTmp;
	global $codigoPlano;
	global $numeroContrato;
	global $valorAdesao;
	global $nomeVendedor;
	global $cpfVendedor;		
	global $numeroProtocolo;		
	global $percentual;
	global $tipoCalculo;
	
	$queryAssociado  = ' SELECT ';
	$queryAssociado .= ' 	VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, VND1000_ON.VALOR_TAXA_ADESAO, VND1000_ON.CODIGO_GRUPO_CONTRATO, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.PROTOCOLO_GERAL_PS6450, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
	$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, ';
	$queryAssociado .= ' 	ESP0002.DESCRICAO_GRUPO_CONTRATO, PS1102.NUMERO_CPF AS CPF_VENDEDOR ';
	$queryAssociado .= ' FROM VND1000_ON ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' INNER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
	$queryAssociado .= ' INNER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT  JOIN PS1102 ON (PS1100.CODIGO_IDENTIFICACAO = PS1102.CODIGO_IDENTIFICACAO) ';
	$queryAssociado .= ' LEFT OUTER JOIN ESP0002 ON (VND1000_ON.CODIGO_GRUPO_CONTRATO = ESP0002.CODIGO_GRUPO_CONTRATO) ';
	$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	
	$resAssociado = jn_query($queryAssociado);
	if(!$rowAssociado = jn_fetch_object($resAssociado)){
		//echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
		exit;
	}else{
		jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
	}

	$numeroContrato = $rowAssociado->NUMERO_CONTRATO;
	$codigoPlano = $rowAssociado->CODIGO_PLANO;
	$valorAdesao = $rowAssociado->VALOR_TAXA_ADESAO;
	$nomeVendedor = $rowAssociado->NOME_VENDEDOR;
	$cpfVendedor = $rowAssociado->CPF_VENDEDOR;
	$numeroProtocolo = $rowAssociado->PROTOCOLO_GERAL_PS6450;


	if(retornaValorConfiguracao('UTILIZA_PERCENTUAL') == 'SIM'){	
		$queryPerc  = ' SELECT VALOR_SUGERIDO, TIPO_CALCULO FROM PS1024 ';
		$queryPerc .= ' WHERE 1 = 1';
		
		if(retornaValorConfiguracao('UTILIZA_PLANOS_PS1024') == 'SIM'){
			$queryPerc .= ' AND PS1024.CODIGOS_PLANOS LIKE '. aspas('%' . $codigoPlano . '%');		
		}
		
		if(retornaValorConfiguracao('UTILIZA_CONTRATO_PS1024') == 'SIM'){
			$queryPerc .= ' AND PS1024.CODIGO_GRUPO_CONTRATO = ' . aspas($rowAssociado->CODIGO_GRUPO_CONTRATO);		
		}
		
		$resPerc = jn_query($queryPerc);
		if($rowPerc = jn_fetch_object($resPerc)){
			$percentual = $rowPerc->VALOR_SUGERIDO;	
			$tipoCalculo = $rowPerc->TIPO_CALCULO;	
		}
	}
	
	$queryPlano = 'SELECT CODIGOS_MODELO_CONTRATO FROM VND1030CONFIG_ON WHERE CODIGO_PLANO =' . aspas($rowAssociado->CODIGO_PLANO);
	$resPlano = jn_query($queryPlano);
	$rowPlano = jn_fetch_object($resPlano);

	$queryContratos = 'SELECT TEXTO_CONTRATO, CODIGO_MODELO, HIPERLINK_MODELO FROM VND1030MODELOS_ON WHERE CODIGO_MODELO IN (' . $rowPlano->CODIGOS_MODELO_CONTRATO . ')';
	$resContratos = jn_query($queryContratos);		
	if($rowContratos = jn_fetch_row($resContratos)) {
		if($rowContratos[0]){
			global $valorTotal;
			
			$date = new DateTime($rowAssociado->DATA_NASCIMENTO);
			$interval = $date->diff( new DateTime( date('Y-m-d') ) );
			$idade = $interval->format('%Y');
			
			if($rowAssociado->PESO != '' && $rowAssociado->ALTURA != ''){
				$imcTitular = ($rowAssociado->PESO / ($rowAssociado->ALTURA * $rowAssociado->ALTURA));
			}
			
			if($_SESSION['codigoSmart'] == '3389' || $_SESSION['codigoSmart'] == '3397'){						
				$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
				$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
				$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
				$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;			
				$resValores = jn_query($queryValores);
				$rowValores = jn_fetch_object($resValores);
				$valor = $rowValores->VALOR_PLANO;
				
				if($percentual > 0){
					if ($tipoCalculo == 'V'){
						$valor = ($rowValores->VALOR_PLANO + $percentual);
					}else{
						$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
						$valor = ($rowValores->VALOR_PLANO + $calculo);
					}
				}
							
				$valorTotal = ($valorTotal + $valor);
			}
						
			$textoContrato = $rowContratos[0];			
			$textoContrato = str_replace('__**__NOME_ASSOCIADO__**__', $rowAssociado->NOME_ASSOCIADO, $textoContrato);
			$textoContrato = str_replace('__**__DATA_NASCIMENTO__**__', SqlToData($rowAssociado->DATA_NASCIMENTO), $textoContrato);
			$textoContrato = str_replace('__**__DATA_ADMISSAO__**__', SqlToData($rowAssociado->DATA_ADMISSAO), $textoContrato);
			$textoContrato = str_replace('__**__NUMERO_RG__**__', $rowAssociado->NUMERO_RG, $textoContrato);
			$textoContrato = str_replace('__**__NUMERO_CPF__**__', $rowAssociado->NUMERO_CPF, $textoContrato);
			$textoContrato = str_replace('__**__EMAIL__**__', $rowAssociado->ENDERECO_EMAIL, $textoContrato);
			$textoContrato = str_replace('__**__ENDERECO__**__', $rowAssociado->ENDERECO, $textoContrato);
			$textoContrato = str_replace('__**__BAIRRO__**__', $rowAssociado->BAIRRO, $textoContrato);
			$textoContrato = str_replace('__**__CIDADE__**__', $rowAssociado->CIDADE, $textoContrato);
			$textoContrato = str_replace('__**__ESTADO__**__', $rowAssociado->ESTADO, $textoContrato);
			$textoContrato = str_replace('__**__CEP__**__', $rowAssociado->CEP, $textoContrato);
			$textoContrato = str_replace('__**__TELEFONE01__**__', $rowAssociado->NUMERO_TELEFONE_01, $textoContrato);
			$textoContrato = str_replace('__**__TELEFONE02__**__', $rowAssociado->NUMERO_TELEFONE_02, $textoContrato);
			$textoContrato = str_replace('__**__TELEFONE03__**__', $rowAssociado->TELEFONE03, $textoContrato);
			$textoContrato = str_replace('__**__NOME_PLANO__**__', $rowAssociado->NOME_PLANO_FAMILIARES, $textoContrato);
			$textoContrato = str_replace('__**__DIA_VENCIMENTO__**__', $rowAssociado->DIA_VENCIMENTO, $textoContrato);
			$textoContrato = str_replace('__**__CODIGO_VENDEDOR__**__', $rowAssociado->CODIGO_VENDEDOR, $textoContrato);
			$textoContrato = str_replace('__**__NOME_VENDEDOR__**__', $rowAssociado->NOME_VENDEDOR, $textoContrato);
			$textoContrato = str_replace('__**__CPF_VENDEDOR__**__', $rowAssociado->CPF_VENDEDOR, $textoContrato);
			$textoContrato = str_replace('__**__IDADE__**__', $idade, $textoContrato);
			$textoContrato = str_replace('__**__SEXO__**__', $rowAssociado->SEXO, $textoContrato);
			$textoContrato = str_replace('__**__DATA_EMISSAO_RG__**__', $rowAssociado->DATA_EMISSAO_RG, $textoContrato);
			$textoContrato = str_replace('__**__ORGAO_EMISSOR_RG__**__', $rowAssociado->ORGAO_EMISSOR_RG, $textoContrato);
			$textoContrato = str_replace('__**__UFRG__**__', $rowAssociado->UFRG, $textoContrato);
			$textoContrato = str_replace('__**__EC__**__', $rowAssociado->CODIGO_PARENTESCO, $textoContrato);
			$textoContrato = str_replace('__**__CODIGO_CNS__**__', $rowAssociado->CODIGO_CNS, $textoContrato);
			$textoContrato = str_replace('__**__PIS__**__', $rowAssociado->CODIGO_CNS, $textoContrato);
			$textoContrato = str_replace('__**__NUMERO_DECLARACAO_NASC_VIVO__**__', $rowAssociado->NUMERO_DECLARACAO_NASC_VIVO, $textoContrato);
			$textoContrato = str_replace('__**__NOME_MAE__**__', $rowAssociado->NOME_MAE, $textoContrato);
			$textoContrato = str_replace('__**__IDENTIFICACAO_UNIDADE__**__', $rowAssociado->DESCRICAO_GRUPO_CONTRATO, $textoContrato);
			$textoContrato = str_replace('__**__InicioVigencia__**__', '&nbsp;', $textoContrato);
			$textoContrato = str_replace('__**__MATRICULA_SINPRO__**__', '&nbsp;', $textoContrato);
			$textoContrato = str_replace('__**__DATA_ATUAL__**__', date('d/m/Y'), $textoContrato);
			$textoContrato = str_replace('__**__PESO_TITULAR__**__', $rowAssociado->PESO, $textoContrato);
			$textoContrato = str_replace('__**__ALTURA_TITULAR__**__', $rowAssociado->ALTURA, $textoContrato);
			$textoContrato = str_replace('__**__IMC_TITULAR__**__', round($imcTitular), $textoContrato);
			$textoContrato = str_replace('__**__VALOR_TITULAR__**__', toMoeda($valor), $textoContrato);			
			$textoContrato = declaracaoSaudeTitular($textoContrato);			
			$textoContrato = trataPrimeiroDependente($textoContrato);			
			$textoContrato = trataSegundoDependente($textoContrato);			
			$textoContrato = trataTerceiroDependente($textoContrato);			
			$textoContrato = trataQuartoDependente($textoContrato);					
			$textoContrato = trataPlanoMarcado($textoContrato);					
			$textoContrato = trataReciboVendedor($textoContrato);				
			$textoContrato = termosContratos($textoContrato);				
			$textoContrato = trataAngariacao($textoContrato);				
			$textoContrato = trataGridDeclaracao($textoContrato);
			
			$textoContrato = str_replace('__**__VALOR_TOTAL__**__', toMoeda($valorTotal), $textoContrato);
			
			geraPdf(jn_utf8_encode($textoContrato), $rowContratos[1]);
		}else{		
			salvaRegistroContrato($rowContratos[1], $rowContratos[2], 'FIXO');
		}	
	}
}


function geraPdf($contrato, $modelo){	
	global $caminhoArquivo;
	global $codAssociadoTmp;	
	global $numeroContrato;	
	
	$nomeArquivo = $codAssociadoTmp . '_' . $modelo . '_' . date('Ymd') . date('His') . '.pdf';
	$nomeFinal = $caminhoArquivo . $nomeArquivo;	

	if($_SESSION['codigoSmart'] == '4022'){
		$mpdf=new mPDF('c', 'A4-L'); 
	}else{		
		$mpdf=new mPDF();
	}
	
	$mpdf->SetDisplayMode('fullpage');	
	
	if($_SESSION['codigoSmart'] == '3389' && $modelo == 4){
		$mpdf->SetHTMLHeader('	<div id="divimagem">
									<img Width="1000" Height="400" src="cabecalho_proposta.php?numeroContrato=' . $numeroContrato . '" />
								</div>
							');
		
		$footer = "<table width=\"1000\">
		<tr>
		<td style='font-size: 18px; padding-bottom: 20px;' align=\"right\">{PAGENO}</td>
		</tr>
		</table>";
		$mpdf->SetHTMLFooter($footer);
	}
	
	$mpdf->WriteHTML($contrato);
	$mpdf->Output('../../'.$nomeFinal);
	
	salvaRegistroContrato($modelo, retornaValorConfiguracao('LINK_PASTA_CONTRATOS') . $nomeFinal, 'DINAMICO');
}


function salvaRegistroContrato($modelo, $nomeFinal, $tpContrato){	
	global $codAssociadoTmp;
	global $statusRegistrado;
	
		
	$queryValida = 'SELECT NOME_ARQUIVO FROM VND1002_ON WHERE CODIGO_MODELO = ' . aspas($modelo) . ' AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resValida = jn_query($queryValida);
	$rowValida = jn_fetch_object($resValida);
	
	if($rowValida->NOME_ARQUIVO){
		jn_query('DELETE FROM VND1002_ON WHERE CODIGO_MODELO = ' . aspas($modelo) . ' AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp),false,true,true);
	}
	
	$insertContrato  = 'INSERT INTO VND1002_ON (CODIGO_ASSOCIADO, CODIGO_MODELO, NOME_ARQUIVO) VALUES (';
	$insertContrato .= aspas($codAssociadoTmp) . ',' . aspas($modelo) . ',' . aspas($nomeFinal) . ')';
		
	if(!jn_query($insertContrato)){
		echo 'Erro ao registrar contrato ' . $modelo;
	}else{
		if($statusRegistrado == false && $tpContrato != 'FIXO'){
			registraStatus();
		}
	}
	
}

function registraStatus(){
	global $dadosInput;
	global $codAssociadoTmp;
	global $statusRegistrado;
	
	$queryConfig  = ' SELECT VND1030CONFIG_ON.FLAG_EXIGIR_DECL_SAUDE FROM VND1000_ON ';
	$queryConfig .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryConfig .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resConfig = jn_query($queryConfig);
	$rowConfig = jn_fetch_object($resConfig);
	
	$enviarEmailContrato = 'SIM';
	if(retornaValorConfiguracao('ENVIAR_EMAIL_CONTRATO') == 'NAO'){
		$enviarEmailContrato = 'NAO';
	}
	
	$status = '';
	if($enviarEmailContrato == 'NAO'){
		$status = 'AGUARDANDO_AVALIACAO';
	}elseif($rowConfig->FLAG_EXIGIR_DECL_SAUDE == 'S'){
		$status = 'AGUARDANDO_DECL_SAUDE';
	}else{
		$status = 'AGUARDANDO_ACEITE_CONTRATO';		
	}
	
	$updateStatus  = ' UPDATE VND1000_ON SET ULTIMO_STATUS = ' . aspas($status);
	$updateStatus .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	jn_query($updateStatus);
	
	$queryValidStatus = 'SELECT * FROM VND1000STATUS_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resValidStatus = jn_query($queryValidStatus);
	$rowValidStatus = jn_fetch_object($resValidStatus);
	
	$statusExiste = true;

	if($rowValidStatus->TIPO_STATUS == ''){
		
		$queryAssocCont = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
		$resAssocCont = jn_query($queryAssocCont);
		while($rowAssocCont = jn_fetch_object($resAssocCont)){
			$insertStatus  = ' INSERT INTO VND1000STATUS_ON (CODIGO_ASSOCIADO, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS, ';
			$insertStatus .= ' REMETENTE_STATUS, DESTINATARIO_STATUS) VALUES (';
			$insertStatus .= aspas($rowAssocCont->CODIGO_ASSOCIADO) . ',' . aspas($status) . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas(date('H:i')) . ', ';
			$insertStatus .= aspas('VENDEDOR') . ',' . aspas('BENEFICIARIO') . ')';
			if(!jn_query($insertStatus)){
				$statusExiste = false;
			}
		}
	}
		
	if($statusExiste){		
		$statusRegistrado = true;	
					
		if((($dadosInput['envioEmail'] == 1 ) || ($dadosInput['retornaMensagem'] == 1)) && $enviarEmailContrato == 'SIM'){
			$retornaMensagem = '';
			if($dadosInput['retornaMensagem']){
				$retornaMensagem = '&retornaMensagem=true';
			}
			
			$url = retornaValorConfiguracao('LINK_PERSISTENCIA') . 'EstruturaPrincipal/disparoEmail.php?codigoModelo=1&vnd=true&codigoAssociado='.$codAssociadoTmp.$retornaMensagem;
			
			$ch = curl_init();	
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			$info = curl_getinfo($ch);
			$start = $info['header_size'];
			$body = substr($result, $start, strlen($result) - $start);
			curl_close($ch);
			
		}
	}	
}






function registraStatusEmpresa($codigoEmpresa)
{
	
	$status 	    = 'AGUARDANDO_ACEITE_CONTRATO';		
	$modelo		    = '0';
	$nomeFinal	    = 'SEM ARQUIVO IMPLEMENTADO';

	$insertContrato  = 'INSERT INTO VND1002_ON (CODIGO_EMPRESA, CODIGO_MODELO, NOME_ARQUIVO) VALUES (';
	$insertContrato .= aspas($codigoEmpresa) . ',' . aspas($modelo) . ',' . aspas($nomeFinal) . ')';
	jn_query($insertContrato);
	
	$queryAssocCont = 'SELECT CODIGO_ASSOCIADO FROM VND1000_ON WHERE CODIGO_EMPRESA = ' . aspas($codigoEmpresa);
	$resAssocCont   = jn_query($queryAssocCont);
		
	while($rowAssocCont = jn_fetch_object($resAssocCont))
	{
		$updateStatus  = ' UPDATE VND1000_ON SET ULTIMO_STATUS = ' . aspas($status);
		$updateStatus .= ' WHERE CODIGO_TITULAR = ' . aspas($rowAssocCont->CODIGO_ASSOCIADO);
		jn_query($updateStatus);
			
		$insertStatus  = ' INSERT INTO VND1000STATUS_ON (CODIGO_ASSOCIADO, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS, ';
		$insertStatus .= ' REMETENTE_STATUS, DESTINATARIO_STATUS) VALUES (';
		$insertStatus .= aspas($rowAssocCont->CODIGO_ASSOCIADO) . ',' . aspas($status) . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas(date('H:i')) . ', ';
		$insertStatus .= aspas('VENDEDOR') . ',' . aspas('BENEFICIARIO') . ')';
		jn_query($insertStatus);
	}
	
	$insertStatus  = ' INSERT INTO VND1000STATUS_ON (CODIGO_EMPRESA, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS, ';
	$insertStatus .= ' REMETENTE_STATUS, DESTINATARIO_STATUS) VALUES (';
	$insertStatus .= aspas($codigoEmpresa) . ',' . aspas($status) . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas(date('H:i')) . ', ';
	$insertStatus .= aspas('VENDEDOR') . ',' . aspas('EMPRESA') . ')';
	
	jn_query($insertStatus);
	
	$insertStatus  = ' INSERT INTO VND1010STATUS_ON (CODIGO_EMPRESA, TIPO_STATUS, DATA_CRIACAO_STATUS, HORA_CRIACAO_STATUS, ';
	$insertStatus .= ' REMETENTE_STATUS, DESTINATARIO_STATUS) VALUES (';
	$insertStatus .= aspas($codigoEmpresa) . ',' . aspas('AGUARDANDO_AVALIACAO') . ', ' . dataToSql(date('d/m/Y')) . ', ' . aspas(date('H:i')) . ', ';
	$insertStatus .= aspas('VENDEDOR') . ',' . aspas('EMPRESA') . ')';
	
	jn_query($insertStatus);
	
	$updateStatus  = ' UPDATE VND1010_ON SET ULTIMO_STATUS = ' . aspas('AGUARDANDO_AVALIACAO');
	$updateStatus .= ' WHERE CODIGO_EMPRESA = ' . aspas($codigoEmpresa);
	jn_query($updateStatus);
	
}






function declaracaoSaudeTitular($textoContrato){
	global $codAssociadoTmp;	
	
	$queryDecTit  = ' SELECT ';
	$queryDecTit .= '	PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
	$queryDecTit .= ' FROM VND1000_ON ';
	$queryDecTit .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
	$queryDecTit .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
	$queryDecTit .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp); 	
	$queryDecTit .= ' ORDER BY PS1039.NUMERO_PERGUNTA ';
	$resDecTit = jn_query($queryDecTit); 
	
	$i = 1;
	while($rowDecTit = jn_fetch_object($resDecTit)){
		$textoContrato = str_replace('__**__TITULAR_PERGUNTA'.$i.'__**__', $rowDecTit->RESPOSTA_DIGITADA, $textoContrato);
		$i++;
	}
	
	$quantPer = $i-1;
	
	for ($i = $quantPer; $i <= 25; $i++) {
		$textoContrato = str_replace('__**__TITULAR_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
	}
	
	return $textoContrato;
}

function trataPrimeiroDependente($textoContrato){
	global $codAssociadoTmp;
	global $valorTotal;	
	global $percentual;
	global $tipoCalculo;
	
	$codigoDep1 = explode('.',$codAssociadoTmp);
	$codigoDep1 = $codigoDep1[0] . '.1';
	
	$queryDep1  = ' SELECT ';
	$queryDep1 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA ';
	$queryDep1 .= ' FROM VND1000_ON ';
	$queryDep1 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep1 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep1 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep1);
	$queryDep1 .= ' ORDER BY CODIGO_ASSOCIADO ';

	$resDep1 = jn_query($queryDep1);
	if($rowDep1 = jn_fetch_object($resDep1)){
		$date = new DateTime($rowDep1->DATA_NASCIMENTO);
		$interval = $date->diff( new DateTime( date('Y-m-d') ) );
		$idadeDep1 = $interval->format('%Y');
		
		if($rowDep1->PESO != '' && $rowDep1->ALTURA != ''){
			$imcDep1 = ($rowDep1->PESO / ($rowDep1->ALTURA * $rowDep1->ALTURA));
		}
		
		
		if($_SESSION['codigoSmart'] == '3389' || $_SESSION['codigoSmart'] == '3397'){		
			
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep1->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep1;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep1;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			
			$valor = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				if ($tipoCalculo == 'V'){
					$valor = ($rowValores->VALOR_PLANO + $percentual);
				}else{
					$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
					$valor = ($rowValores->VALOR_PLANO + $calculo);
				}
			}
			
			$valorTotal = ($valorTotal + $valor);
		}
	}
			
	$textoContrato = str_replace('__**__NOME_DEPENDENTE1__**__', $rowDep1->NOME_ASSOCIADO, $textoContrato);
	$textoContrato = str_replace('__**__SEXO_DEPENDENTE1__**__', $rowDep1->SEXO, $textoContrato);
	$textoContrato = str_replace('__**__Data_Nascimento_Dependente1__**__', SqlToData($rowDep1->DATA_NASCIMENTO), $textoContrato);
	$textoContrato = str_replace('__**__IDADE_DEPENDENTE1__**__', $idadeDep1, $textoContrato);
	$textoContrato = str_replace('__**__GP_DEPENDENTE1__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__PIS_DEPENDENTE1__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__RG_DEPENDENTE1__**__', $rowDep1->NUMERO_RG, $textoContrato);
	$textoContrato = str_replace('__**__CPF_DEPENDENTE1__**__', $rowDep1->NUMERO_CPF, $textoContrato);
	$textoContrato = str_replace('__**__MAE_DEPENDENTE1__**__', $rowDep1->NOME_MAE, $textoContrato);
	$textoContrato = str_replace('__**__CNS_DEPENDENTE1__**__', $rowDep1->CODIGO_CNS, $textoContrato);
	$textoContrato = str_replace('__**__NASCIDO_VIVO_DEPENDENTE1__**__', $rowDep1->NUMERO_DECLARACAO_NASC_VIVO, $textoContrato);
	$textoContrato = str_replace('__**__PESO_DEP1__**__', $rowDep1->PESO, $textoContrato);
	$textoContrato = str_replace('__**__ALTURA_DEP1__**__', $rowDep1->ALTURA, $textoContrato);
	$textoContrato = str_replace('__**__IMC_DEP1__**__', round($imcDep1), $textoContrato);
	$textoContrato = str_replace('__**__VALOR_DEPENDENTE1__**__', toMoeda($valor), $textoContrato);
	
	if($rowDep1){
		$queryDecDep1  = ' SELECT ';
		$queryDecDep1 .= '	PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
		$queryDecDep1 .= ' FROM VND1000_ON ';
		$queryDecDep1 .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
		$queryDecDep1 .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
		$queryDecDep1 .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep1); 	
		$queryDecDep1 .= ' ORDER BY PS1039.NUMERO_PERGUNTA '; 	
		$resDecDep1 = jn_query($queryDecDep1); 
		
		$i = 1;
		while($rowDecDep1 = jn_fetch_object($resDecDep1)){
			$textoContrato = str_replace('__**__DEPENDENTE1_PERGUNTA'.$i.'__**__', $rowDecDep1->RESPOSTA_DIGITADA, $textoContrato);
			$i++;
		}
		
		$quantPer = $i-1;
	
		for ($i = $quantPer; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE1_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}else{
		for ($i = 1; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE1_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}
	
	
	return $textoContrato;
}

function trataSegundoDependente($textoContrato){
	global $codAssociadoTmp;
	global $codigoPlano;
	global $valorTotal;
	global $percentual;
	global $tipoCalculo;
	
	$codigoDep2 = explode('.',$codAssociadoTmp);
	$codigoDep2 = $codigoDep2[0] . '.2';
	
	$queryDep2  = ' SELECT ';
	$queryDep2 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA  ';
	$queryDep2 .= ' FROM VND1000_ON ';
	$queryDep2 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep2 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep2 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep2);
	$resDep2 = jn_query($queryDep2);
	
	if($rowDep2 = jn_fetch_object($resDep2)){
		$date = new DateTime($rowDep2->DATA_NASCIMENTO);
		$interval = $date->diff( new DateTime( date('Y-m-d') ) );
		$idadeDep2 = $interval->format('%Y');
		
		if($rowDep2->PESO != '' && $rowDep2->ALTURA != ''){
			$imcDep2 = ($rowDep2->PESO / ($rowDep2->ALTURA * $rowDep2->ALTURA));
		}	
		
		if($_SESSION['codigoSmart'] == '3389' || $_SESSION['codigoSmart'] == '3397'){		
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep2->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep2;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep2;			
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			$valor = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				if ($tipoCalculo == 'V'){
					$valor = ($rowValores->VALOR_PLANO + $percentual);
				}else{
					$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
					$valor = ($rowValores->VALOR_PLANO + $calculo);
				}
			}
			
			$valorTotal = ($valorTotal + $valor);		
		}
	}
		
	$textoContrato = str_replace('__**__NOME_DEPENDENTE2__**__', $rowDep2->NOME_ASSOCIADO, $textoContrato);
	$textoContrato = str_replace('__**__SEXO_DEPENDENTE2__**__', $rowDep2->SEXO, $textoContrato);
	$textoContrato = str_replace('__**__Data_Nascimento_DEPENDENTE2__**__', SqlToData($rowDep2->DATA_NASCIMENTO), $textoContrato);
	$textoContrato = str_replace('__**__IDADE_DEPENDENTE2__**__', $idadeDep2, $textoContrato);
	$textoContrato = str_replace('__**__GP_DEPENDENTE2__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__PIS_DEPENDENTE2__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__RG_DEPENDENTE2__**__', $rowDep2->NUMERO_RG, $textoContrato);
	$textoContrato = str_replace('__**__CPF_DEPENDENTE2__**__', $rowDep2->NUMERO_CPF, $textoContrato);
	$textoContrato = str_replace('__**__MAE_DEPENDENTE2__**__', $rowDep2->NOME_MAE, $textoContrato);
	$textoContrato = str_replace('__**__CNS_DEPENDENTE2__**__', $rowDep2->CODIGO_CNS, $textoContrato);
	$textoContrato = str_replace('__**__NASCIDO_VIVO_DEPENDENTE2__**__', $rowDep2->NUMERO_DECLARACAO_NASC_VIVO, $textoContrato);
	$textoContrato = str_replace('__**__PESO_DEP2__**__', $rowDep2->PESO, $textoContrato);
	$textoContrato = str_replace('__**__ALTURA_DEP2__**__', $rowDep2->ALTURA, $textoContrato);
	$textoContrato = str_replace('__**__IMC_DEP2__**__', round($imcDep2), $textoContrato);
	$textoContrato = str_replace('__**__VALOR_DEPENDENTE2__**__', toMoeda($valor), $textoContrato);
	
	if($rowDep2){
		$queryDecDep2  = ' SELECT ';
		$queryDecDep2 .= '	PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
		$queryDecDep2 .= ' FROM VND1000_ON ';
		$queryDecDep2 .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
		$queryDecDep2 .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
		$queryDecDep2 .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep2); 	
		$queryDecDep2 .= ' ORDER BY PS1039.NUMERO_PERGUNTA '; 	
		$resDecDep2 = jn_query($queryDecDep2); 
		
		$i = 1;
		while($rowDecDep2 = jn_fetch_object($resDecDep2)){
			$textoContrato = str_replace('__**__DEPENDENTE2_PERGUNTA'.$i.'__**__', $rowDecDep2->RESPOSTA_DIGITADA, $textoContrato);
			$i++;
		}
		
		$quantPer = $i-1;
	
		for ($i = $quantPer; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE2_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}else{
		for ($i = 1; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE2_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}
	
	return $textoContrato;
}

function trataTerceiroDependente($textoContrato){
	global $codAssociadoTmp;
	global $valorTotal;
	global $percentual;
	global $tipoCalculo;
	
	$codigoDep3 = explode('.',$codAssociadoTmp);
	$codigoDep3 = $codigoDep3[0] . '.3';
	
	$queryDep3  = ' SELECT ';
	$queryDep3 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA  ';
	$queryDep3 .= ' FROM VND1000_ON ';
	$queryDep3 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep3 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep3 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep3);
	
	$resDep3 = jn_query($queryDep3);
	if($rowDep3 = jn_fetch_object($resDep3)){		
		$date = new DateTime($rowDep3->DATA_NASCIMENTO);
		$interval = $date->diff( new DateTime( date('Y-m-d') ) );
		$idadeDep3 = $interval->format('%Y');
		
		if($rowDep3->PESO != '' && $rowDep3->ALTURA != ''){
			$imcDep3 = ($rowDep3->PESO / ($rowDep3->ALTURA * $rowDep3->ALTURA));
		}	
		
		if($_SESSION['codigoSmart'] == '3389' || $_SESSION['codigoSmart'] == '3397'){
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep3->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep3;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep3;		
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			$valor = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				if ($tipoCalculo == 'V'){
					$valor = ($rowValores->VALOR_PLANO + $percentual);
				}else{
					$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
					$valor = ($rowValores->VALOR_PLANO + $calculo);
				}
			}
			
			$valorTotal = ($valorTotal + $valor);		
		}
	}
		
	$textoContrato = str_replace('__**__NOME_DEPENDENTE3__**__', $rowDep3->NOME_ASSOCIADO, $textoContrato);
	$textoContrato = str_replace('__**__SEXO_DEPENDENTE3__**__', $rowDep3->SEXO, $textoContrato);
	$textoContrato = str_replace('__**__Data_Nascimento_DEPENDENTE3__**__', SqlToData($rowDep3->DATA_NASCIMENTO), $textoContrato);
	$textoContrato = str_replace('__**__IDADE_DEPENDENTE3__**__', $idadeDep3, $textoContrato);
	$textoContrato = str_replace('__**__GP_DEPENDENTE3__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__PIS_DEPENDENTE3__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__RG_DEPENDENTE3__**__', $rowDep3->NUMERO_RG, $textoContrato);
	$textoContrato = str_replace('__**__CPF_DEPENDENTE3__**__', $rowDep3->NUMERO_CPF, $textoContrato);
	$textoContrato = str_replace('__**__MAE_DEPENDENTE3__**__', $rowDep3->NOME_MAE, $textoContrato);
	$textoContrato = str_replace('__**__CNS_DEPENDENTE3__**__', $rowDep3->CODIGO_CNS, $textoContrato);
	$textoContrato = str_replace('__**__NASCIDO_VIVO_DEPENDENTE3__**__', $rowDep3->NUMERO_DECLARACAO_NASC_VIVO, $textoContrato);
	$textoContrato = str_replace('__**__PESO_DEP3__**__', $rowDep3->PESO, $textoContrato);
	$textoContrato = str_replace('__**__ALTURA_DEP3__**__', $rowDep3->ALTURA, $textoContrato);
	$textoContrato = str_replace('__**__IMC_DEP3__**__', round($imcDep3), $textoContrato);
	$textoContrato = str_replace('__**__VALOR_DEPENDENTE3__**__', toMoeda($valor), $textoContrato);
	
	if($rowDep3){
		$queryDecDep3  = ' SELECT ';
		$queryDecDep3 .= '	PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
		$queryDecDep3 .= ' FROM VND1000_ON ';
		$queryDecDep3 .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
		$queryDecDep3 .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
		$queryDecDep3 .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep3); 	
		$queryDecDep3 .= ' ORDER BY PS1039.NUMERO_PERGUNTA '; 	
		$resDecDep3 = jn_query($queryDecDep3); 
		
		$i = 1;
		while($rowDecDep3 = jn_fetch_object($resDecDep3)){
			$textoContrato = str_replace('__**__DEPENDENTE3_PERGUNTA'.$i.'__**__', $rowDecDep3->RESPOSTA_DIGITADA, $textoContrato);
			$i++;
		}
		$quantPer = $i-1;
	
		for ($i = $quantPer; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE3_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}else{
		for ($i = 1; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE3_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}
	
	return $textoContrato;
}

function trataQuartoDependente($textoContrato){
	global $codAssociadoTmp;
	global $valorTotal;
	global $percentual;
	global $tipoCalculo;
	
	$codigoDep4 = explode('.',$codAssociadoTmp);
	$codigoDep4 = $codigoDep4[0] . '.4';
	
	$queryDep4  = ' SELECT ';
	$queryDep4 .= ' 	NOME_ASSOCIADO, DATA_NASCIMENTO, NUMERO_CPF, NOME_MAE, NUMERO_RG, SEXO, CODIGO_CNS, CODIGO_PLANO, NUMERO_DECLARACAO_NASC_VIVO, PESO, ALTURA  ';
	$queryDep4 .= ' FROM VND1000_ON ';
	$queryDep4 .= ' WHERE CODIGO_TITULAR = ' . aspas($codAssociadoTmp);
	$queryDep4 .= ' AND  TIPO_ASSOCIADO = ' . aspas('D');
	$queryDep4 .= ' AND  CODIGO_ASSOCIADO = ' . aspas($codigoDep4);
	
	$resDep4 = jn_query($queryDep4);
	if($rowDep4 = jn_fetch_object($resDep4)){
		$date = new DateTime($rowDep4->DATA_NASCIMENTO);
		$interval = $date->diff( new DateTime( date('Y-m-d') ) );
		$idadeDep4 = $interval->format('%Y');
		
		if($rowDep4->PESO != '' && $rowDep4->ALTURA != ''){
			$imcDep4 = ($rowDep4->PESO / ($rowDep4->ALTURA * $rowDep4->ALTURA));
		}	
		
		if($_SESSION['codigoSmart'] == '3389' || $_SESSION['codigoSmart'] == '3397'){
			$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowDep4->CODIGO_PLANO);
			$queryValores .= ' AND IDADE_MINIMA <= ' . $idadeDep4;
			$queryValores .= ' AND IDADE_MAXIMA >= ' . $idadeDep4;
			$resValores = jn_query($queryValores);
			$rowValores = jn_fetch_object($resValores);
			$valor = $rowValores->VALOR_PLANO;
			if($percentual > 0){
				if ($tipoCalculo == 'V'){
					$valor = ($rowValores->VALOR_PLANO + $percentual);
				}else{
					$calculo = (ceil($rowValores->VALOR_PLANO * $percentual) / 100);				
					$valor = ($rowValores->VALOR_PLANO + $calculo);
				}
			}
			
			$valorTotal = ($valorTotal + $valor);
		}
	}
		
	$textoContrato = str_replace('__**__NOME_DEPENDENTE4__**__', $rowDep4->NOME_ASSOCIADO, $textoContrato);
	$textoContrato = str_replace('__**__SEXO_DEPENDENTE4__**__', $rowDep4->SEXO, $textoContrato);
	$textoContrato = str_replace('__**__Data_Nascimento_DEPENDENTE4__**__', SqlToData($rowDep4->DATA_NASCIMENTO), $textoContrato);
	$textoContrato = str_replace('__**__IDADE_DEPENDENTE4__**__', $idadeDep4, $textoContrato);
	$textoContrato = str_replace('__**__GP_DEPENDENTE4__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__PIS_DEPENDENTE4__**__', '&nbsp;', $textoContrato);
	$textoContrato = str_replace('__**__RG_DEPENDENTE4__**__', $rowDep4->NUMERO_RG, $textoContrato);
	$textoContrato = str_replace('__**__CPF_DEPENDENTE4__**__', $rowDep4->NUMERO_CPF, $textoContrato);
	$textoContrato = str_replace('__**__MAE_DEPENDENTE4__**__', $rowDep4->NOME_MAE, $textoContrato);
	$textoContrato = str_replace('__**__CNS_DEPENDENTE4__**__', $rowDep4->CODIGO_CNS, $textoContrato);
	$textoContrato = str_replace('__**__NASCIDO_VIVO_DEPENDENTE4__**__', $rowDep4->NUMERO_DECLARACAO_NASC_VIVO, $textoContrato);
	$textoContrato = str_replace('__**__PESO_DEP4__**__', $rowDep4->PESO, $textoContrato);
	$textoContrato = str_replace('__**__ALTURA_DEP4__**__', $rowDep4->ALTURA, $textoContrato);
	$textoContrato = str_replace('__**__IMC_DEP4__**__', round($imcDep4), $textoContrato);
	$textoContrato = str_replace('__**__VALOR_DEPENDENTE4__**__', toMoeda($valor), $textoContrato);
	
	if($rowDep4){
		$queryDecDep4  = ' SELECT ';
		$queryDecDep4 .= '	PS1039.NUMERO_PERGUNTA, COALESCE(VND1005_ON.RESPOSTA_DIGITADA,"N") AS RESPOSTA_DIGITADA ';
		$queryDecDep4 .= ' FROM VND1000_ON ';
		$queryDecDep4 .= ' INNER JOIN PS1039  ON VND1000_ON.CODIGO_PLANO = PS1039.CODIGO_PLANO ';
		$queryDecDep4 .= ' LEFT  JOIN VND1005_ON ON ((VND1005_ON.NUMERO_PERGUNTA = PS1039.NUMERO_PERGUNTA) and (VND1000_ON.CODIGO_ASSOCIADO = VND1005_ON.CODIGO_ASSOCIADO)) ';
		$queryDecDep4 .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codigoDep4); 	
		$queryDecDep4 .= ' ORDER BY PS1039.NUMERO_PERGUNTA '; 	
		$resDecDep4 = jn_query($queryDecDep4); 
		
		$i = 1;
		while($rowDecDep4 = jn_fetch_object($resDecDep4)){
			$textoContrato = str_replace('__**__DEPENDENTE4_PERGUNTA'.$i.'__**__', $rowDecDep4->RESPOSTA_DIGITADA, $textoContrato);
			$i++;
		}
		$quantPer = $i-1;
	
		for ($i = $quantPer; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE4_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}else{
		for ($i = 1; $i <= 25; $i++) {
			$textoContrato = str_replace('__**__DEPENDENTE4_PERGUNTA'.$i.'__**__', ' ', $textoContrato);
		}
	}
	
	return $textoContrato;
}

function trataReciboVendedor($textoContrato){
	global $valorAdesao;
	global $nomeVendedor;
	
	$recibo = '	<div id="divimagem">
					<img Width="1000" Height="400" src="tratativas_proposta_Vidamax.php?tp=Recibo&nomeVendedor=' . $nomeVendedor . '&valorAdesao=' . $valorAdesao . '" />
				</div>';				
	$textoContrato = str_replace('__**__RECIBO_VENDEDOR__**__', $recibo, $textoContrato);
	
	return $textoContrato;
}

function termosContratos($textoContrato){	
	global $numeroProtocolo;	

	$recibo = '	<div id="divimagem">
					<img Width="1000" Height="400" src="tratativas_proposta_Vidamax.php?tp=TermoContrato&protocolo=' . $numeroProtocolo . '" />					
				</div>';				
	$textoContrato = str_replace('__**__TERMOS_CONTRATOS__**__', $recibo, $textoContrato);
	
	return $textoContrato;
}

function trataAngariacao($textoContrato){
	global $valorAdesao;
	global $nomeVendedor;
	
	$recibo = '	<div id="divimagem">
					<img Width="1000" Height="400" src="tratativas_proposta_Vidamax.php?tp=TaxaAngariacao&imagem=1&nomeVendedor=' . $nomeVendedor . '&valorAdesao=' . $valorAdesao . '" />
					<img Width="1000" Height="400" src="tratativas_proposta_Vidamax.php?tp=TaxaAngariacao&imagem=2&nomeVendedor=' . $nomeVendedor . '&valorAdesao=' . $valorAdesao . '" />
				</div>';				
	$textoContrato = str_replace('__**__PAGINA_ANGARIACAO__**__', $recibo, $textoContrato);
	
	return $textoContrato;
}

function trataGridDeclaracao($textoContrato){
	global $valorAdesao;
	global $nomeVendedor;
	global $codAssociadoTmp;	
	
	$recibo = '	<div id="divimagem">
					<img Width="1000" Height="400" src="tratativas_proposta_Vidamax.php?tp=declaracaoSaude&codAssociado=' . $codAssociadoTmp . '" />
				</div>';				
	$textoContrato = str_replace('__**__GRID_DECLARACAO_SAUDE__**__', $recibo, $textoContrato);
	
	return $textoContrato;
}


function trataContratoOdontoMais(){
	global $codAssociadoTmp;	
	
	$queryAssoc = 'SELECT CODIGO_TABELA_PRECO FROM VND1000_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssoc = jn_query($queryAssoc);
	$rowAssoc = jn_fetch_object($resAssoc);
	
	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	if($rowAssoc->CODIGO_TABELA_PRECO){		
		$queryModelo .= ' AND ((VND1030CONFIG_ON.TABELA_PRECO_AUTOC IS NULL) or (VND1030CONFIG_ON.TABELA_PRECO_AUTOC = ' . aspas($rowAssoc->CODIGO_TABELA_PRECO) . ')) ';
	}
	
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_object($resModelo);
	
	$url1 = 'https://www.operadoraodontomais.com.br/AliancaAppNet2/ServidorAl2/EstruturaEspecifica/tratativas_proposta_OdontoMais.php?pagina=1&codAssociado=' . $codAssociadoTmp;	
	$url2 = 'https://www.operadoraodontomais.com.br/AliancaAppNet2/ServidorAl2/EstruturaEspecifica/tratativas_proposta_OdontoMais.php?pagina=2&codAssociado=' . $codAssociadoTmp;	
	
	$ch = curl_init($url1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$responseLink1 = json_decode(curl_exec($ch));
	
	$ch = curl_init($url2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$responseLink2 = json_decode(curl_exec($ch));
	
	sleep(10);
	
	$contrato  = '	<div id="divimagem1"> ';
	$contrato .= '		<img width="1000px" height="1000px" src="../../ServidorCliente/EstruturaContratos/ImagensCriadas/' . $codAssociadoTmp . '_1.jpg" /> ';
	$contrato .= '	</div>';		
	$contrato .= '	<div> ';
	$contrato .= '		<img width="1000px" height="1000px" src="../../ServidorCliente/EstruturaContratos/ImagensCriadas/' . $codAssociadoTmp . '_2.jpg" /> ';
	$contrato .= '	</div>';	
	
	geraPdf($contrato, $rowModelo->CODIGOS_MODELO_CONTRATO);
}

function trataContratoMV2C(){
	global $codAssociadoTmp;	
	$contrato  = '';
	$quantPaginas = 12;
	$i = 1;
	
	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_object($resModelo);
	
	if($rowModelo->CODIGOS_MODELO_CONTRATO == '4'){
		$quantPaginas = 5;
	}
	
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_MV2C.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp . '" /> ';
		$contrato .= '	</div>';	
		$i++;
	}
	
	geraPdf($contrato, '1');
}


function trataPlanoMarcado($textoContrato){
	global $codigoPlano;
	
	$i = 1;	
	while($i <= 42){
		if($codigoPlano == '65' && $i == 1){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '81' && $i == 11){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', 'X', $textoContrato);	
		}elseif($codigoPlano == '67' && $i == 3){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '69' && $i == 4){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '71' && $i == 5){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '73' && $i == 6){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '75' && $i == 7){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '76' && $i == 8){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '83' && $i == 9){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '84' && $i == 10){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '88' && $i == 11){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '89' && $i == 12){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '92' && $i == 13){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '93' && $i == 14){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '94' && $i == 19){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '95' && $i == 15){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '98' && $i == 20){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '101' && $i == 21){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '103' && $i == 22){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '64' && $i == 23){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);
		}elseif($codigoPlano == '80' && $i == 24){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '102' && $i == 41){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '99' && $i == 40){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '96' && $i == 38){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}elseif($codigoPlano == '97' && $i == 39){
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', ' X ', $textoContrato);	
		}else{
			$textoContrato = str_replace('__**__PLANO_X_' . $i . '__**__', '&nbsp;', $textoContrato);	
		}
		
		$i++;
	}
	
	return $textoContrato;
}

function trataContratoStaCasaMaua(){
	global $codAssociadoTmp;	
	$contrato  = '';
	$quantPaginas = 2;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_StaCasaMaua.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp . '" /> ';
		$contrato .= '	</div>';	
		$i++;
	}
	
	geraPdf($contrato, '1');
}

function trataContratoClasse(){
	global $codAssociadoTmp;	
	$contrato  = '';
	$quantPaginas = 9;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_Classe.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp . '" /> ';
		$contrato .= '	</div>';	
		$i++;
	}
	
	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_object($resModelo);
	
	geraPdf($contrato, $rowModelo->CODIGOS_MODELO_CONTRATO);
}

function trataContratoVidamax(){
	global $codAssociadoTmp;
	
	
	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO, FLAG_PORTABILIDADE FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resModelo = jn_query($queryModelo);
	if($rowModelo = jn_fetch_object($resModelo)){	
		
		if($rowModelo->CODIGOS_MODELO_CONTRATO == '4'){
			geraContratoPadraoHTML();
			return false;
		}
		if($rowModelo->FLAG_PORTABILIDADE != 'S'){
			$portabilidade = 'N';
		}else{
			$portabilidade = 'S';
		}
		
		$contrato  = '';
		$quantPaginas = '';
		
		
		if((($rowModelo->CODIGOS_MODELO_CONTRATO == 7) || ($rowModelo->CODIGOS_MODELO_CONTRATO == 13) || ($rowModelo->CODIGOS_MODELO_CONTRATO == 14)) && $portabilidade == 'S'){
			$quantPaginas = 6;
		}elseif($rowModelo->CODIGOS_MODELO_CONTRATO == 9 && $portabilidade == 'S'){
			$quantPaginas = 8;
		}elseif((($rowModelo->CODIGOS_MODELO_CONTRATO == 2) || ($rowModelo->CODIGOS_MODELO_CONTRATO == 8)) && $portabilidade == 'S'){  
			$quantPaginas = 10;
		}elseif($rowModelo->CODIGOS_MODELO_CONTRATO == 2 || $rowModelo->CODIGOS_MODELO_CONTRATO == 15 || $rowModelo->CODIGOS_MODELO_CONTRATO == 8){  
			$quantPaginas = 11;
		}elseif($rowModelo->CODIGOS_MODELO_CONTRATO == 10){
			$quantPaginas = 1;
		}elseif($rowModelo->CODIGOS_MODELO_CONTRATO == 11 || $rowModelo->CODIGOS_MODELO_CONTRATO == 36 || $rowModelo->CODIGOS_MODELO_CONTRATO == 12){
			$quantPaginas = 5;	
		}elseif($rowModelo->CODIGOS_MODELO_CONTRATO == 7 || $rowModelo->CODIGOS_MODELO_CONTRATO == 13){  
			$quantPaginas = 12;
		}elseif($rowModelo->CODIGOS_MODELO_CONTRATO == 9){
			$quantPaginas = 18;
		}elseif($rowModelo->CODIGOS_MODELO_CONTRATO == 6){
			$quantPaginas = 27;	
		}else{
			$quantPaginas = 25;
		}
		

		if($rowModelo->CODIGOS_MODELO_CONTRATO == 8){

			$queryEvento = 'SELECT sum(VND1003_ON.VALOR_FATOR) AS SOMA_VALOR_FATOR FROM VND1003_ON WHERE VND1003_ON.CODIGO_ASSOCIADO IN (SELECT VND1000_ON.CODIGO_ASSOCIADO FROM VND1000_ON WHERE VND1000_ON.CODIGO_TITULAR = ' . aspas($codAssociadoTmp) . ')';
			$resEvento = jn_query($queryEvento);
			$rowEvento = jn_fetch_object($resEvento);

			$valoresEventos = $rowEvento->SOMA_VALOR_FATOR;

			//Subir uma nova atualização para a utilização 
			
			//$resultado = qryUmRegistro($queryEvento);
			//$valoresEventos = $resultado->SOMA_VALOR_FATOR;

			if($valoresEventos > 0){
				$quantPaginas +=  2;
			}
		}

		
		$i = 1;
		while($i <= $quantPaginas){				
			$contrato .= '	<div id="divimagem'.$i.'"> ';
			$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_Vidamax.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp . '&modelo=' . $rowModelo->CODIGOS_MODELO_CONTRATO . '&portabilidade=' . $portabilidade . '" /> ';				
			$contrato .= '	</div>';	
			$i++;
		}
		
	}else{		
		echo 'Modelo de contrato não encontrado.';
		return false;
	}
	
	geraPdf($contrato, $rowModelo->CODIGOS_MODELO_CONTRATO);
}

function trataContratoPropulsao(){
	global $codAssociadoTmp;	
	
	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_object($resModelo);
	
	$contrato  = '	<div id="divimagem1"> ';
	$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_Propulsao.php?pagina=1&codAssociado=' . $codAssociadoTmp . '" /> ';
	$contrato .= '	</div>';
	
	geraPdf($contrato, 1);
}

function trataContratoHebrom(){
	global $codAssociadoTmp;	
	
	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_object($resModelo);
	
	$contrato  = '';
	$quantPaginas = 15;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_Hebrom.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp .'" /> ';
		$contrato .= '	</div>';	
		$i++;
	}	
	
	geraPdf($contrato, $rowModelo->CODIGOS_MODELO_CONTRATO);
}

function trataContratoVixMed(){
	global $codAssociadoTmp;	
	
	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_VND1030CONFIG = VND1030CONFIG_ON.NUMERO_REGISTRO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_object($resModelo);	
	
	$contrato  = '';
	$quantPaginas = 12;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_VixMed.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp . '&modelo=' . (int)$rowModelo->CODIGOS_MODELO_CONTRATO . '" /> ';
		$contrato .= '	</div>';	
		$i++;
	}	
	
	geraPdf($contrato, 1);
}

function trataContratoDemaisDescontos(){
	global $codAssociadoTmp;	
	
	$queryAssociado  = ' SELECT * FROM VND1000_ON ';
	$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
	$queryAssociado .= ' INNER JOIN ps1032 ON (VND1000_ON.CODIGO_PLANO = PS1032.CODIGO_PLANO AND COALESCE(VND1000_ON.CODIGO_TABELA_PRECO,1) = PS1032.CODIGO_TABELA_PRECO) ';
	$queryAssociado .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resAssociado = jn_query($queryAssociado);
	$rowAssociado = jn_fetch_object($resAssociado);	
	
	$valorFatAdesao = $rowAssociado->VALOR_PLANO;	
	$dataVencimento = date('d-m-Y'); 
	$dataVencimento = date('d/m/Y', strtotime("+5 days",strtotime($dataVencimento)));	
	
	

	if($valorFatAdesao < 0 || $valorFatAdesao == ''){		
		$retorno['STATUS'] = 'ERRO';
		$retorno['MSG'] = 'Não foi encontrado o valor do associado. Por favor, confirme as informações do plano e tabela de preços.';
		echo json_encode($retorno);
		return false; 
	}
	jn_query('DELETE FROM VND1020_ON WHERE NUMERO_PARCELA = "1" AND CODIGO_ASSOCIADO =' . aspas($codAssociadoTmp));
	
	$queryVnd1020  = ' INSERT INTO VND1020_ON (CODIGO_ASSOCIADO, CODIGO_EMPRESA, DATA_VENCIMENTO, VALOR_FATURA, DATA_EMISSAO, MES_ANO_REFERENCIA, IDENTIFICACAO_GERACAO, NUMERO_PARCELA) ';
	$queryVnd1020 .= ' VALUES ';
	$queryVnd1020 .= " (" . aspas($codAssociadoTmp) .  ", " . aspas('400') . ", " . dataToSql($dataVencimento) . ", " . aspas($valorFatAdesao) . ", current_timestamp, "; 
	$queryVnd1020 .= " EXTRACT(MONTH FROM current_timestamp) || '/' || EXTRACT(YEAR FROM current_timestamp), 'FAT_VND', " . aspas('1') . ")";		
	
	if(jn_query($queryVnd1020)){
		/*
		require('../EstruturaPrincipal/disparoEmail.php');
		$assunto = 'Pagamento adesao D+Descontos';
		$corpoEmail  = 'Ol&aacute; ' . $rowAssociado->NOME_ASSOCIADO . ', <br>';
		$corpoEmail .= 'Seja bem vindo ao Cart&atilde;o D+ Descontos! <br> ';
		$corpoEmail .= 'Para dar continuidade na contrata&ccedil;&atilde;o, por favor, <a href="cartaodemaisdescontos.com.br/AliancaAppNet2/Site/autenticacao/login?t=cpfPagamento">clique aqui </a> <br>';
		$corpoEmail .= 'Muito obrigado!  <br>';
		$corpoEmail .= 'Att. <br>';
		$corpoEmail .= 'Equipe Cart&atilde;o D+ Descontos <br>';
		header ('Content-type: text/html; charset=ISO-8859-1');
		disparaEmail($rowAssociado->ENDERECO_EMAIL, $assunto, $corpoEmail, retornaValorConfiguracao('EMAIL_OCULTO_VENDAS'));		
		*/
	}

	$queryModelo  = ' SELECT CODIGOS_MODELO_CONTRATO FROM VND1000_ON ';
	$queryModelo .= ' INNER JOIN VND1030CONFIG_ON ON (VND1000_ON.CODIGO_PLANO = VND1030CONFIG_ON.CODIGO_PLANO) ';
	$queryModelo .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
	$resModelo = jn_query($queryModelo);
	$rowModelo = jn_fetch_object($resModelo);	
	
	$contrato  = '';
	$quantPaginas = 2;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_DemaisDescontos.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp . '&modelo=' . (int)$rowModelo->CODIGOS_MODELO_CONTRATO . '" /> ';
		$contrato .= '	</div>';	
		$i++;
	}	
	
	geraPdf($contrato, 1);
}

function trataContratoVileve(){
	global $codAssociadoTmp;	
	$contrato  = '';
	$quantPaginas = 40;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="tratativas_proposta_Vileve.php?pagina='.$i.'&codAssociado=' . $codAssociadoTmp . '&modelo=1" /> ';
		$contrato .= '	</div>';	
		$i++;
	}
	
	geraPdf($contrato, '1');
}

function trataContratoSomarMaisSaude(){	
	global $codAssociadoTmp;	
	$contrato  = '';

	$quantPaginas = 12;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="../../ServidorCliente/Arquivos/Contratos/Manual_Usuario_Vendas_Online_page-000' . $i . '.jpg" /> ';	
		$contrato .= '	</div>';
		$i++;
	}

	$quantPaginas = 6;
	
	$i = 1;
	while($i <= $quantPaginas){
		$contrato .= '	<div id="divimagem'.$i.'"> ';
		$contrato .= '		<img Width="1000px" Height="1000px" src="../../ServidorCliente/Arquivos/Contratos/Politicas_Privacidade_Vendas_Online_page-000' . $i . '.jpg" /> ';	
		$contrato .= '	</div>';
		$i++;
	}
	
	geraPdf($contrato, '1');
}

?>