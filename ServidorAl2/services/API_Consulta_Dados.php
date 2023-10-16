<?php
require('../lib/base.php');
require("../lib/mpdf60/mpdf.php");

$tipo = $_GET['tipo'];

//criaLog($tipo,$_GET);


$headers = getallheaders();
$token = null;

foreach ($headers as $header => $value){
	if(strtoupper($header) == 'AUTHORIZATION'){
		$token = $value;
		break;
	}	
}
if(!$token){	
	responseAPI(1);
	return false;
}

if($token != retornaValorConfiguracao('TOKEN_API_CONSULTA_DADOS')){	
	responseAPI(2);
	return false;
}


$retorno = array();

if($tipo=='consultaContrato'){
	$retorno = consultaContrato($_GET['cpfCnpj'], $_GET['excluidos']);
}else if($tipo=='consultaBoleto'){
	$retorno = consultaBoleto($_GET['codigo']);
}else if($tipo=='pdfBoleto'){
	$retorno = pdfBoleto($_GET['codigo'],$_GET['registro']);
}else if($tipo=='consultaCancelamento'){
	$retorno = consultaCancelamento($_GET['codigo']);
}else if($tipo=='confirmaCancelamento'){
	$retorno = confirmaCancelamento($_GET['codigo']);
}else if($tipo=='carteirinha'){
	$retorno = carteirinha($_GET['codigo']);
}else if($tipo=='carencia'){
	$retorno = carencia($_GET['codigo']);
}else if($tipo=='ir'){
	$retorno = ir($_GET['codigo'] ,$_GET['ano']);
}else if($tipo=='ir'){
	$retorno = ir($_GET['codigo'] ,$_GET['ano']);
}else if($tipo=='cartaPermanencia'){
	$retorno = cartaPermanencia($_GET['codigo'] ,$_GET['ano']);
}else if($tipo=='reativacao'){
	$retorno = reativacao($_GET['codigo'] );
}else if($tipo=='consultaAlteraVencimento'){
	$retorno = consultaAlteraVencimentoApi($_GET['codigo'] );
}else if($tipo=='valorAlteraVencimento'){
	$retorno = valorAlteraVencimentoApi($_GET['codigo'] ,$_GET['dia'] );
}else if($tipo=='confirmaAlteraVencimento'){
	$retorno = confirmaAlteraVencimentoApi($_GET['codigo'] ,$_GET['dia'] );
}

echo json_encode($retorno);



function consultaAlteraVencimentoApi($codigo){
	
	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	geraSessionCodigo($codigo);

	$dadosInput['tipo'] ='consultaAlteraVencimento';
	chdir('../EstruturaEspecifica/');
	ob_start();
	include("multaprorata.php");
	$retornoAux = array();
	$retornoAux = ob_get_contents();
	ob_end_clean();
	if (substr($retornoAux, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $retornoAux = substr($retornoAux, 3);
	}
	$retornoAux = json_decode($retornoAux);

	$retornoAux->HTML  = preg_replace('/<[^>]+>/', ' ', $retornoAux->HTML );
	$retornoAux->HTML = strip_tags($retornoAux->HTML);

	$retorno = array();
	$retorno['STATUS'] = $retornoAux->STATUS;
	$retorno['MSG'] = trim($retornoAux->HTML);

	if($retorno['STATUS'] =='OK'){
		$retorno['MSG'] = ' É possível fazer a alteração do vencimento.';
	}

	return $retorno;

}

function valorAlteraVencimentoApi($codigo, $dia){
	
	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	geraSessionCodigo($codigo);

	$dadosInput['tipo'] ='valorAlteraVencimento';
	$dadosInput['vencimento'] = $dia;
	chdir('../EstruturaEspecifica/');
	ob_start();
	include("multaprorata.php");
	$retornoAux = array();
	$retornoAux = ob_get_contents();
	ob_end_clean();
	if (substr($retornoAux, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $retornoAux = substr($retornoAux, 3);
	}
	$retornoAux = json_decode($retornoAux);
  
	$retornoAux->HTML  = preg_replace('/<[^>]+>/', ' ', $retornoAux->HTML );
	$retornoAux->HTML = strip_tags($retornoAux->HTML);

	$retorno = array();
	$retorno['STATUS'] = $retornoAux->STATUS;
	$retorno['MSG'] = trim($retornoAux->HTML);

	return $retorno;

}

function confirmaAlteraVencimentoApi($codigo, $dia){
	
	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	geraSessionCodigo($codigo);

	$dadosInput['tipo'] ='confirmaAlteraVencimento';
	$dadosInput['vencimento'] = $dia;
	chdir('../EstruturaEspecifica/');
	ob_start();
	include("multaprorata.php");
	$retornoAux = array();
	$retornoAux = ob_get_contents();
	ob_end_clean();
	if (substr($retornoAux, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $retornoAux = substr($retornoAux, 3);
	}
	$retornoAux = json_decode($retornoAux);
  
	$retornoAux->HTML  = preg_replace('/<[^>]+>/', ' ', $retornoAux->HTML );
	$retornoAux->HTML = strip_tags($retornoAux->HTML);

	$retorno = array();
	$retorno['STATUS'] = $retornoAux->STATUS;
	$retorno['MSG'] = trim($retornoAux->HTML);

	return $retorno;

}

function reativacao($codigo){

	$retornoFuncao = array();
	$queryPrincipal = "SELECT CODIGO_ASSOCIADO FROM PS1000 WHERE CODIGO_ASSOCIADO = ".aspas($codigo).' and DATA_EXCLUSAO is not null ' ;
	$resPrincipal = jn_query($queryPrincipal);
			
	$retornoFuncao['STATUS'] = 'ERRO';
	$retornoFuncao['MSG'] = 'Não é possível fazer a reativação desse contrato';

	if($rowPrincipal = jn_fetch_object($resPrincipal)){
		if (retornaValorConfiguracao('PROCESSO_REATIVACAO') == 'SIM' ){

			$queryReativ  = 'SELECT * FROM SP_REATIVACAO_PF(' . aspas($codigo)  . ')';
			
			$resReativ = jn_query($queryReativ);
			if($rowReativ = jn_fetch_object($resReativ)){
				if($rowReativ->PERMITE_REATIVACAO == 'S'){					
					$query  = ' SELECT NUMERO_REGISTRO,CODIGO_ASSOCIADO, CODIGO_EMPRESA, (CAST(REPLICATE("0", 2 - LEN(DAY(DATA_VENCIMENTO))) + RTrim(DAY(DATA_VENCIMENTO)) AS CHAR(2)) + "/" + CAST(REPLICATE("0", 2 - LEN(MONTH(DATA_VENCIMENTO))) + RTrim(MONTH(DATA_VENCIMENTO)) AS CHAR(2)) + "/" + CAST(YEAR(DATA_VENCIMENTO) AS VARCHAR(4))) AS DATA_VENCIMENTO, VALOR_FATURA, ';
					$query .= ' MES_REFERENCIA FROM VW_REATIVACAO_AL2 ';
					$query .= ' WHERE CODIGO_ASSOCIADO = ' .aspas($codigo);	
					$query .= ' ORDER BY NUMERO_REGISTRO DESC';
					$res = jn_query($query);
					
					$retornoFuncao['STATUS'] = 'OK';
					$retornoFuncao['MSG'] = ('Efetuo o pagamento através do link para reativar seu contrato.');	
					$identificacao = md5($codigo.time());
					$retornoFuncao['LINK_PAGAMENTO'] = 'https://app.plenasaude.com.br/AliancaNet/services/efetuarPagamento.php?tipo=FA&info=REAT&id='.$identificacao;	

					while ($row = jn_fetch_object($res)){	
						$item = array();
						
						$item['REGISTRO'] = $row->NUMERO_REGISTRO;
						$item['VENCIMENTO'] = ('Data Vencimento :'.$row->DATA_VENCIMENTO);
						$item['VALOR'] = ($row->VALOR_FATURA);
			
						$retornoFuncao['FATURAS'][] =$item;

						$chave = jn_gerasequencial('ESP_FATURAS_AGRUPADAS');
						$insert  = " INSERT INTO ESP_FATURAS_AGRUPADAS(NUMERO_REGISTRO,";
					
						$insert .= "CODIGO_ASSOCIADO,";	
					
						$insert .= " NUMERO_AGRUPAMENTO,NUMERO_REGISTRO_PS1020)
								 			VALUES(".aspas($chave).",".
										  aspas($codigo).",".
										  aspas($identificacao).",".
										  aspas($row->NUMERO_REGISTRO).")";
						jn_query($insert);
					
					}
					
				}
			}
		}
	}
	return $retornoFuncao;
}



function cartaPermanencia($codigo,$ano){

	$retornoFuncao = array();
	$queryPrincipal = "SELECT LINK FROM APP_VW_CONSULTA_PER_V3 WHERE CODIGO_ASSOCIADO = ".aspas($codigo) . " and ((ANO=".aspas($ano).") or (ANO is null))";
	$resPrincipal = jn_query($queryPrincipal);
			
	if($rowPrincipal = jn_fetch_object($resPrincipal)){
		chdir('../ProcessoDinamico/');
		$senhaDocumento = retornaSenhaCodigo($codigo);
		$carta = geraPdfSenhaBase64UrlHtml($senhaDocumento, $rowPrincipal->LINK);
		$retornoFuncao['CARTA'] = $carta ;
	}
	return $retornoFuncao;
}


function ir($codigo,$ano){

	$retornoFuncao = array();
	$queryPrincipal = "SELECT LINK FROM APP_VW_CONSULTA_IR_V3 WHERE CODIGO_ASSOCIADO = ".aspas($codigo) . " and ANO=".aspas($ano);
	$resPrincipal = jn_query($queryPrincipal);
			
	if($rowPrincipal = jn_fetch_object($resPrincipal)){
		chdir('../ProcessoDinamico/');
		$senhaDocumento = retornaSenhaCodigo($codigo);
		$ir = geraPdfSenhaBase64UrlHtml($senhaDocumento, $rowPrincipal->LINK);
		$retornoFuncao['IR'] = $ir ;
	}
	return $retornoFuncao;
}


function carencia($codigo){
	geraSessionCodigo($codigo);

	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	if($empresa){
		$retornoFuncao = array();
	}else{

		$retornoFuncao = array();

		$carencias = getCarencias($codigo);
	
		if($carencias != ''){
			foreach((array) $carencias as $carencia) {
				$linha = Array();
				$tipoImagem = (compareData(SqlToData($carencia->RESULTADO_DATA_CARENCIA)) >= 0) ? 1 : 0;
				
				$linha['CODIGO'] 		= $carencia->RESULTADO_NUMERO_GRUPO;
				$linha['DESC'] 		= jn_utf8_encode($carencia->RESULTADO_DESCRICAO_GRUPO);
				$linha['DATA'] 			    = SqlToData($carencia->RESULTADO_DATA_CARENCIA);
				$linha['SITUACAO'] 			= ($tipoImagem == 0) ? 'LIBERADO' : 'EM CARÊNCIA';
				
				$retornoFuncao[] = $linha;
			}
		}

	}

	return $retornoFuncao;
}

function carteirinha($codigo){
	
	geraSessionCodigo($codigo);

	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	if($empresa){
		$retornoFuncao = array();
	}else{

		$retornoFuncao = array();

		ob_start();
		$dadosInput['cod']  = $codigo;
	  $dadosInput['tipo']  = 'verso';
		include("../EstruturaEspecifica/carteirinha.php");
		$retornoAux = array();
		$retornoAux = ob_get_contents();
		ob_end_clean();
		if (substr($retornoAux, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
			$retornoAux = substr($retornoAux, 3);
		}
		$retornoAux = json_decode($retornoAux);

		$retornoFuncao['VERSO'] = $retornoAux->IMG;

		ob_start();
		$dadosInput['cod']  = $codigo;
	  $dadosInput['tipo']  = 'frente';
		include("../EstruturaEspecifica/carteirinha.php");
		$retornoAux = array();
		$retornoAux = ob_get_contents();
		ob_end_clean();
		if (substr($retornoAux, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
			$retornoAux = substr($retornoAux, 3);
		}
		$retornoAux = json_decode($retornoAux);

		$retornoFuncao['FRENTE'] = $retornoAux->IMG;

	}

	return $retornoFuncao;

}


function consultaCancelamento($codigo){
	
	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	geraSessionCodigo($codigo);

	$dadosInput['tipo'] ='consultaCancelamento';

	ob_start();
	include("../EstruturaEspecifica/multaprorata.php");
	$retornoAux = array();
	$retornoAux = ob_get_contents();
	ob_end_clean();
	if (substr($retornoAux, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $retornoAux = substr($retornoAux, 3);
	}
	$retornoAux = json_decode($retornoAux);

	$retornoAux->HTML  = preg_replace('/<[^>]+>/', ' ', $retornoAux->HTML );
	$retornoAux->HTML = strip_tags($retornoAux->HTML);
	$retornoAux->HTML = str_replace('Caso deseje continuar com o cancelamento preencha os campos abaixo e clique no botão confirmar.','',$retornoAux->HTML);

	$retornoFuncao = array();
	$retornoFuncao['STATUS'] = $retornoAux->STATUS;
	$retornoFuncao['MSG'] = trim($retornoAux->HTML);

	return $retornoFuncao;

}

function confirmaCancelamento($codigo){
	
	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	geraSessionCodigo($codigo);

	$dadosInput['tipo'] ='ConfirmaCancelamento';
	$dadosInput['dado'] =$_GET;

	ob_start();
	include("../EstruturaEspecifica/multaprorata.php");
	$retornoAux = array();
	$retornoAux = ob_get_contents();
	ob_end_clean();
	if (substr($retornoAux, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $retornoAux = substr($retornoAux, 3);
	}
	$retornoAux = json_decode($retornoAux);

	$retornoAux->HTML  = preg_replace('/<[^>]+>/', ' ', $retornoAux->HTML );
	$retornoAux->HTML = strip_tags($retornoAux->HTML);
	$retornoAux->HTML = str_replace('Caso deseje continuar com o cancelamento preencha os campos abaixo e clique no botão confirmar.','',$retornoAux->HTML);

	$retorno = array();
	$retorno['STATUS'] = $retornoAux->STATUS;
	$retorno['MSG'] = trim($retornoAux->HTML);

	return $retorno;

}



function consultaBoleto($codigo){
	$retorno = array();

	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;
		$filtro = "";
		if($empresa){
			$queryPrincipal = "select  VW_SEGUNDA_VIA_AL2.NUMERO_REGISTRO REGISTRO, VW_SEGUNDA_VIA_AL2.VALOR_FATURA VALOR, VW_SEGUNDA_VIA_AL2.DATA_VENCIMENTO from VW_SEGUNDA_VIA_AL2 
																		inner join ps1010 on VW_SEGUNDA_VIA_AL2.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA
																		where VW_SEGUNDA_VIA_AL2.CODIGO_EMPRESA =" .  aspas($codigo) .  " AND VW_SEGUNDA_VIA_AL2.CODIGO_ASSOCIADO IS NULL ";
		}else{
			$queryPrincipal = "select  VW_SEGUNDA_VIA_AL2.NUMERO_REGISTRO REGISTRO, VW_SEGUNDA_VIA_AL2.VALOR_FATURA VALOR, VW_SEGUNDA_VIA_AL2.DATA_VENCIMENTO from VW_SEGUNDA_VIA_AL2 
																		inner join ps1000 on VW_SEGUNDA_VIA_AL2.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
																		where VW_SEGUNDA_VIA_AL2.CODIGO_ASSOCIADO =". aspas($codigo). " ";
		}

		$queryPrincipal .= " and VISUALIZA_BOLETO <> '' and STATUS = 'Pendente de Pagamento' ";

		$resPrincipal = jn_query($queryPrincipal);
			
	while($rowPrincipal = jn_fetch_object($resPrincipal)){
		$item = array();
		$item['REGISTRO'] = $rowPrincipal->REGISTRO;
		$item['VALOR_FATURA']= $rowPrincipal->VALOR;
		$item['DATA_VENCIMENTO']= SqlToData($rowPrincipal->DATA_VENCIMENTO);
		
		$retorno[] = $item;
	}

	return $retorno;

}

function pdfBoleto($codigo,$registro){
	$retorno = array();

	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

		if($empresa){
			$queryPrincipal = "select  VISUALIZA_BOLETO from VW_SEGUNDA_VIA_AL2 
						where VW_SEGUNDA_VIA_AL2.CODIGO_EMPRESA =" .  aspas($codigo) .  " AND VW_SEGUNDA_VIA_AL2.CODIGO_ASSOCIADO IS NULL  and  VW_SEGUNDA_VIA_AL2.NUMERO_REGISTRO =". aspas($registro). " ";
		}else{
			$queryPrincipal = "select  VISUALIZA_BOLETO from VW_SEGUNDA_VIA_AL2 
						where VW_SEGUNDA_VIA_AL2.CODIGO_ASSOCIADO =" .  aspas($codigo) .  " and  VW_SEGUNDA_VIA_AL2.NUMERO_REGISTRO =". aspas($registro). " ";
		}

	
	
		$resPrincipal = jn_query($queryPrincipal);
			
	if($rowPrincipal = jn_fetch_object($resPrincipal)){
		
		$senha = retornaSenhaCodigo($codigo);
		$retorno['BOLETO'] = insereSenhaPdfUrlToBase64($senha , $rowPrincipal->VISUALIZA_BOLETO);
	}

	return $retorno;

}




function consultaContrato($cpfCnpj, $excluidos){
	
	$retorno = array();
	
	$filtro = " 1 <> 1 ";
	$pf = false;
	
	$cpfCnpj = remove_caracteres($cpfCnpj);
	
	if(strlen($cpfCnpj)==11){
		
		$filtro = " REPLACE(REPLACE(REPLACE(PS1000.NUMERO_CPF,'.',''),'.',''),'-','') = ". aspas($cpfCnpj);
		$pf = true;
		if($excluidos)
			$filtro .= " and Ps1000.data_exclusao is not null ";
		else
			$filtro .= " and Ps1000.data_exclusao is null ";
		
	}elseif(strlen($cpfCnpj)==14){
		
		$filtro = " REPLACE(REPLACE(REPLACE(REPLACE(PS1010.NUMERO_CNPJ,'.',''),'.',''),'-',''),'/','')  = ". aspas($cpfCnpj);
		$pf = false;
		
		if($excluidos)
			$filtro .= " and Ps1010.data_exclusao is not null ";
		else
			$filtro .= " and Ps1010.data_exclusao is null ";
		
	}

		
	if($pf){
		$queryPrincipal  =" select PS1000.CODIGO_ASSOCIADO CODIGO,PS1000.CODIGO_TIPO_CARACTERISTICA, ps1030.NOME_PLANO_FAMILIARES ,DATA_ADMISSAO,CONVERT(VARCHAR(10),PS1000.DATA_EXCLUSAO,103)DATA_EXCLUSAO, PS1000.NOME_ASSOCIADO NOME, PS1000.CODIGO_EMPRESA  from PS1000 
							inner join ps1030 on ps1030.codigo_plano = ps1000.codigo_plano
							where ps1000.TIPO_ASSOCIADO = 'T' and ".$filtro;
	}else{
		$queryPrincipal  =" select PS1010.CODIGO_EMPRESA CODIGO,PS1010.CODIGO_TIPO_CARACTERISTICA, '' NOME_PLANO_FAMILIARES ,DATA_ADMISSAO,CONVERT(VARCHAR(10),PS1010.DATA_EXCLUSAO,103)DATA_EXCLUSAO, Ps1010.NOME_EMPRESA NOME, PS1010.CODIGO_EMPRESA   from PS1010 
							where 1=1  and ".$filtro;	
	}
			
	
							
	$resPrincipal = jn_query($queryPrincipal);
			
	while($rowPrincipal = jn_fetch_object($resPrincipal)){
		$item = array();
		
		$item['CODIGO'] = $rowPrincipal->CODIGO;
		$item['NOME'] = $rowPrincipal->NOME;
		
		$item['NOME_PLANO'] = $rowPrincipal->NOME_PLANO_FAMILIARES;
		
		if($rowPrincipal->CODIGO_TIPO_CARACTERISTICA==10)
			$item['TIPO_PLANO'] = 'ODONTOLÓGICO ';
		else
			$item['TIPO_PLANO'] = 'SAÚDE';
		
		
		if($rowPrincipal->CODIGO_EMPRESA==400)
			$item['TIPO_PESSOA'] = 'PF ';
		else
			$item['TIPO_PESSOA'] = 'PJ';
		
		
		$item['DATA_EXCLUSAO'] = $rowPrincipal->DATA_EXCLUSAO;
		
		$retorno[] = $item;
	
	}
	
	return $retorno;

}


function criaLog($tipo,$dados){

$name  = 'logAPI_Consulta_Dados_'.$tipo.'.log';
$text .= '\n'.date('d/m/Y');	
$text .= '-------------------INICIO-DADOS-------------------\n';	
$text .= json_encode($dados);	
$text .= "\n".date('d/m/Y');	
$text .= '-------------------FIM-DADOS--------------------\n';

$file = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);


}

function responseAPI($codErro, $mensagem = ''){

	$mensagemApresentar = '';
	$retornoResponse 	= Array();	
	$retornoResponse['Sucesso'] = false;

	if($codErro == 0){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = $mensagem;
	}elseif($codErro == 1 || $codErro == 2){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = 'Acesso Negado';	
	}elseif($codErro == 200){
		$retornoResponse['ObjErros']['codErro'] = $codErro;
		$retornoResponse['ObjErros']['mensagem'] = 'Processo realizado com sucesso';	
	}


	if($codErro != 200){
		header('HTTP/1.0 401 Unauthorized');
		$retornoResponse['Sucesso'] = false;
	}else{
		$retornoResponse['Sucesso'] = true;
	}

	echo json_encode($retornoResponse);
	exit;
}


function insereSenhaPdfUrlToBase64($senhaDocumento, $urlArquivo){
	// Baixa o arquivo PDF da internet
	$pdfContent = downloadFile($urlArquivo);
	if (!$pdfContent) {
			die('Erro ao baixar o arquivo.');
	}

	// Salve o conteúdo em um arquivo temporário
	$tmpFile = tempnam(sys_get_temp_dir(), 'pdf');
	file_put_contents($tmpFile, $pdfContent);
	
	$mpdf=new mPDF('c'); 
	$mpdf->SetImportUse();
	$mpdf->SetProtection(array(), $senhaDocumento, $senhaDocumento);	
	$pagecount = $mpdf->SetSourceFile($tmpFile);	
	$tplId = $mpdf->ImportPage($pagecount);
	$mpdf->UseTemplate($tplId);
	//$mpdf->Output($tmpFile);	

	$protectedPdfContent = $mpdf->Output('', 'S'); 
	$protectedPdfContent = base64_encode($protectedPdfContent ); 
	
	/* teste decode base64
	$tmpFileTeste = tempnam(sys_get_temp_dir(), 'pdf');
	$pdfContent = base64_decode($protectedPdfContent);
	if (file_put_contents($tmpFileTeste, $pdfContent)) {
    echo "Arquivo salvo com sucesso em " . $pathToFile;
} else {
    echo "Erro ao salvar o arquivo.";
}
*/

	// Limpa o arquivo temporário
	unlink($tmpFile);

	return $protectedPdfContent;
}

function geraPdfSenhaBase64UrlHtml($senhaDocumento, $urlArquivo){
	$htmlContent = downloadFile($urlArquivo);
	if (!$htmlContent) {
			die('Erro ao baixar o arquivo.');
	}
	$htmlContent = str_replace('<img src="images/ponto_preto.jpg"','<hr',$htmlContent);
	$htmlContent = utf8_encode($htmlContent);
		
	$mpdf=new mPDF('c'); 
	$mpdf->WriteHTML($htmlContent);
	$mpdf->SetProtection(array(), $senhaDocumento, $senhaDocumento);	
	$protectedPdfContent = $mpdf->Output('', 'S'); 
	$protectedPdfContent = base64_encode($protectedPdfContent ); 
	
	

	return $protectedPdfContent;
}

function downloadFile($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$data = curl_exec($ch);
	if ($data === false) {
    die('cURL error: ' . curl_error($ch));
}
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpCode !== 200) {
			return false;
	}
	
	return $data;
}

function retornaSenhaCodigo($codigo){
	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

		if($empresa){
			$queryPrincipal = "Select NUMERO_CNPJ SENHA from PS1010 WHERE CODIGO_EMPRESA=".aspas($codigo);
		}else{
			$queryPrincipal = "Select NUMERO_CPF  SENHA from PS1000 WHERE CODIGO_ASSOCIADO=".aspas($codigo);
		}

		$senha = '';
		
		$resPrincipal = jn_query($queryPrincipal);
		
		
		if($rowPrincipal = jn_fetch_object($resPrincipal)){
			//$senha = substr(remove_caracteres($rowPrincipal->SENHA), -5);
			$senha = substr(remove_caracteres($rowPrincipal->SENHA),0, 5);
		}

		return $senha;
}

function geraSessionCodigo($codigo){

	$empresa = true;

	if(strlen($codigo)>8)
		$empresa = false;

	if($empresa){
		$qry = "SELECT  PS1010.CODIGO_EMPRESA CODIGO_USUARIO, CFGEMPRESA.CODIGO_SMART, Ps1010.NOME_EMPRESA FROM PS1010
		INNER JOIN  CFGEMPRESA ON (1=1)
		WHERE PS1000.CODIGO_ASSOCIADO = " . aspas($codigo);

	 $resQuery = jn_query($qry);
				

				
	if ($objResult = jn_fetch_object($resQuery)){

		$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
		$resEmp = jn_query($queryEmp);
		$rowEmp = jn_fetch_object($resEmp);
		
		$_SESSION['codigoIdentificacao']          = $objResult->CODIGO_USUARIO;
		$_SESSION['nomeUsuario']           = jn_utf8_encode($objResult->NOME_EMPRESA);
		$_SESSION['perfilOperador']        = 'EMPRESA';
		$_SESSION['versaoAplicacao']       = '1.0.0';
		$_SESSION['ErrorList']             = array();
		$_SESSION['UrlAcesso']             = $_SERVER['HTTP_REFERER'];
		$_SESSION['HorarioAcesso']         = @mktime();
		$_SESSION['IpUsuario']             = $_SERVER['REMOTE_ADDR'];
		$_SESSION['nomeEmpresa']           = $rowEmp->NOME_EMPRESA;
		$_SESSION['codigoSmart']           = $rowEmp->CODIGO_SMART;
		//$_SESSION['SESSAO_ID'] 			   = jn_gerasequencial('CFGLOG_NET');

		$_SESSION['CODIGO_USUARIO']             =  $objResult->CODIGO_USUARIO;
		$_SESSION['PERFIL_USUARIO']             =  'EMPRESA';
		$_SESSION['QUANTIDADE_CONTRATOS']       =  '1';
		$_SESSION['CODIGO_TIPO_CARACTERISTICA'] =  '1'; 
		$_SESSION['CODIGO_SMART']               =  $objResult->CODIGO_SMART;

		$_GET['PX'] = 'OK';
	}
	}else{

		$qry = "SELECT  PS1000.CODIGO_ASSOCIADO CODIGO_USUARIO, CFGEMPRESA.CODIGO_SMART, PS1030.REDE_INDICADA, PS1000.CODIGO_PLANO,PS1000.CODIGO_TITULAR,Ps1000.NOME_ASSOCIADO FROM PS1000
			
			LEFT OUTER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) 
			INNER JOIN  CFGEMPRESA ON (1=1)
			WHERE PS1000.CODIGO_ASSOCIADO = " . aspas($codigo);

		$resQuery = jn_query($qry);
					

					
		if ($objResult = jn_fetch_object($resQuery)){

			$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
			$resEmp = jn_query($queryEmp);
			$rowEmp = jn_fetch_object($resEmp);
			
			$_SESSION['codigoIdentificacao']          = $objResult->CODIGO_USUARIO;
			$_SESSION['codigoIdentificacaoTitular']   = $objResult->CODIGO_TITULAR;
			$_SESSION['nomeUsuario']           = jn_utf8_encode($objResult->NOME_ASSOCIADO);
			$_SESSION['perfilOperador']        = 'BENEFICIARIO';
			$_SESSION['versaoAplicacao']       = '1.0.0';
			$_SESSION['ErrorList']             = array();
			$_SESSION['UrlAcesso']             = $_SERVER['HTTP_REFERER'];
			$_SESSION['HorarioAcesso']         = @mktime();
			$_SESSION['IpUsuario']             = $_SERVER['REMOTE_ADDR'];
			$_SESSION['nomeEmpresa']           = $rowEmp->NOME_EMPRESA;
			$_SESSION['codigoSmart']           = $rowEmp->CODIGO_SMART;
			//$_SESSION['SESSAO_ID'] 			   = jn_gerasequencial('CFGLOG_NET');

			$_SESSION['CODIGO_USUARIO']             =  $objResult->CODIGO_USUARIO;
			$_SESSION['PERFIL_USUARIO']             =  'BENEFICIARIO';
			$_SESSION['CODIGO_CONTRATANTE']         =  $objResult->CODIGO_CONTRATANTE;	 
			$_SESSION['QUANTIDADE_CONTRATOS']       =  '1';
			$_SESSION['CODIGO_TIPO_CARACTERISTICA'] =  '1'; 
			$_SESSION['CODIGO_SMART']               =  $objResult->CODIGO_SMART;
			$_SESSION['REDE_INDICADA']			    =  $objResult->REDE_INDICADA;
			$_SESSION['CODIGO_PLANO']               =  $objResult->CODIGO_PLANO; 
			$_GET['PX'] = 'OK';
		}
}
}

?>