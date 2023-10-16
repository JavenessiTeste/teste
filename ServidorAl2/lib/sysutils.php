<?php
require_once('../lib/class.phpmailer.php');
require_once('../lib/class.smtp.php');
require_once('../lib/PHPMailerAutoload.php');

//retorna a string entre aspas simples
 
function aspas($string){
    $string = trim(str_replace('\'','',$string));
    $string = trim(str_replace('"','',$string));
    $string = "'" . $string . "'";
    return $string;
}



function aspasNull($string){
	if($_SESSION['type_db'] == 'sqlsrv'){
		$string = trim(str_replace('"','__|A|__',$string));
	}
	
	$string = trim(str_replace('\'','',$string));
    $string = trim(str_replace('\\','',$string));

    if ($string == '')
        $string = 'null';
    else
        $string = "'" . trim($string) . "'";

    return $string;
}

function integerNull($var) {
    if (is_numeric($var)) {
        return $var;
    } else {
        return 'null';
    }
}

function numSql($var) {
    if (is_numeric($var)) {
        return $var;
    } else {
        return '-999';
    }
}


function testaInt($var) 
{
    if (is_numeric($var)) {
        return true;
    } else {
        return false;
    }
}


 
function valSql($string)
{

   $string = trim($string);
   $string = trim(str_replace(',','.',$string));

   // Por enquanto coloquei para retornar aspas, pq não estava funcionando validar como float, por isto comentei as linhas abaixo.
   // Mas pode ser que precisemos revisar isto.

   if ($string=='')
       $string = 0;

   return aspas($string);

   /*if (is_float($string))
   {
      $string = $string;
   }
   else
   {
      $string = 0;
   }
   
   return $string;*/
}


function retornaOpcoesCombo($opcoes,$tamanhoCampo,$valorInicial)
{

    $retorno = "";
	$temp    = "";
	
	for ($i = 0; $i < strlen($opcoes); $i++) 
	{
	    if ((substr($opcoes,$i,1) != ';') and (substr($opcoes,$i,1) != ','))
   	       $temp.= substr($opcoes,$i,1);

	    if ($i == 0)
           $retorno.= "<option value=''></option>";
		
	    if (((substr($opcoes,$i,1) == ';') or (substr($opcoes,$i,1) == ',')) and ($i != 0))
		{
		
		   $tagSelected	   = "";
		   $temp		   = trim($temp);
		   $valorParaBanco = $temp;
		   
		   if (strpos($valorParaBanco,"|"))
		   {
		       $valorParaBanco = substr($valorParaBanco,0,strpos($valorParaBanco,"|"));
			   $temp		   = substr($temp,strpos($temp,"|")+3,strlen($temp)-(strpos($temp,"|")+3));
		   } 
		   
		   if  ($valorInicial == substr($valorParaBanco,0,(int)$tamanhoCampo))
		   {
		        $tagSelected = "selected='selected'";
		   }		
		
           $retorno.= "<option value='" . $valorParaBanco . "' " . $tagSelected . ">" . $temp . "</option>";
		   $temp    = "";
		}
    }		   
	
    if (!empty($temp))
	{
	
	   $tagSelected	= "";
	   $temp		= trim($temp);
		   
	   if  ($valorInicial == substr($temp,0,(int)$tamanhoCampo))
	   {
	        $tagSelected = "selected='selected'";
	   }		

       $retorno.= "<option value='" . substr($temp,0,(int)$tamanhoCampo) . "' " . $tagSelected . ">" . $temp . "</option>";

	}
	
    return $retorno;
		
}



 // Recebe uma string contendo uma data no formato dd/mm/aaaa ou dd-mm-aaaa
 // Retorna ela no formto 'aaaa-mm-dd'
 // Ficando dessa forma em conformidade com SQL ANSI
 
 //firebird ano-mes-dia
 //sql server ano-dia-mes
function dataToSql($string, $AAspa = true)
{	
	global $formatoData;
	if( $_SESSION['type_db'] == 'firebird' ){
		if (!ValidaData($string))
		{	
			return aspas('1899-01-01');
			exit;
		}	
			
		if (strlen($string) == 10){
			if($formatoData=='AAAA-MM-DD')
				$string = substr($string,6,4) . '-' . substr($string,3,2) . "-" . substr($string,0,2); // 
			else if($formatoData=='AAAA-DD-MM')
				$string = substr($string,6,4) . '-' .   substr($string,0,2) . '-' . substr($string,3,2) ; 
			else
				$string = substr($string,6,4) . '-' . substr($string,3,2) . "-" . substr($string,0,2); 
		}
		if ($AAspa) {
			return aspasNull($string);
		} else {
			return $string;
		}
	}else if( $_SESSION['type_db'] == 'mssqlserver'){
		if (strlen($string) == 10){
			if($formatoData=='AAAA-MM-DD')
				$string = substr($string,6,4) . '-' . substr($string,3,2) . "-" . substr($string,0,2); // 
			else if($formatoData=='AAAA-DD-MM')
				$string = substr($string,6,4) . '-' .   substr($string,0,2) . '-' . substr($string,3,2) ; 
			else
				$string = substr($string,6,4) . '-' . substr($string,3,2) . "-" . substr($string,0,2); 
		}
		if ($AAspa) {
			return aspasNull($string);
		} else {
			return $string;
		}	
	}else if( $_SESSION['type_db'] == 'sqlsrv'){
		if (strlen($string) == 10){
			if($formatoData=='AAAA-MM-DD')
				$string = substr($string,6,4) . '-' . substr($string,3,2) . "-" . substr($string,0,2); // 
			else if($formatoData=='AAAA-DD-MM')
				$string = substr($string,6,4) . '-' .   substr($string,0,2) . '-' . substr($string,3,2) ; 
			else
				$string = substr($string,6,4) . '-' . substr($string,3,2) . "-" . substr($string,0,2);  
		}
		if ($AAspa) {
			return aspasNull($string);
		} else {
			return $string;
		}	
	}else if($_SESSION['type_db'] == 'mysql'){		
		if (strlen($string) == 10)
			$string = substr($string,6,4) . '/' . substr($string,3,2) . '/' . substr($string,0,2); // aaaa-mm-dd
		if ($AAspa) {
			return aspasNull($string);
		} else {
			return $string;
		}	
	}else{				
		if (strlen($string) == 10)
			$string = substr($string,6,4) . '/' . substr($string,3,2) . '/' . substr($string,0,2); // aaaa-mm-dd
		if ($AAspa) {
			return aspasNull($string);
		} else {
			return $string;
		}	

	}
}

// Recebe uma string contendo a data no formato yyyy-mm-dd
// Retorna ela no formato brasileiro dd/mm/yyyy

function SqlToData($value){
    
	$string = "";
	
	if(is_object($value)){
		$string = $value->format('d/m/Y');
	}else if ((strlen($value) == 10) or (strlen($value) == 19)) 
	{
		$string = substr($value, 8, 2) . '/' . substr($value, 5, 2) . '/' . substr($value, 0, 4); // dd-mm-aaaa
	} 
	else if (strlen($value) == 26  || strlen($value) == 19 || strlen($value) == 18) 
	{
	
		$string=$value;
		
		switch(substr($value, 0, 3))
		{
		
			case "Jan":
			$mes_sqlserver = "01";
			break;

			case "Feb":
			$mes_sqlserver = "02";
			break;	
			
			case "Mar":
			$mes_sqlserver = "03";
			break;

			case "Apr":
			$mes_sqlserver = "04";
			break;

			case "May":
			$mes_sqlserver = "05";
			break;

			case "Jun":
			$mes_sqlserver = "06";
			break;

			case "Jul":
			$mes_sqlserver = "07";
			break;

			case "Aug":
			$mes_sqlserver = "08";
			break;

			case "Sep":
			$mes_sqlserver = "09";
			break;

			case "Oct":
			$mes_sqlserver = "10";
			break;

			case "Nov":
			$mes_sqlserver = "11";
			break;

			case "Dec":
			$mes_sqlserver = "12";
			break;
		
		}
		
		if (substr($value, 4, 2)<10)
			$dia_sqlserver='0'.substr($value, 5, 1);
		else
			$dia_sqlserver=substr($value, 4, 2);
		if (substr($value, 4, 2)<10)
			$ano_sqlserver=substr($value, 7, 4);
		else
			$ano_sqlserver=substr($value, 7, 4);		
		
		$string = $dia_sqlserver . '/' . $mes_sqlserver . '/' . $ano_sqlserver; // dd-mm-aaaa	
	}
	else 
	{
		if($value=='')
			$string ='';
		else
			$string = 'Data invalida.';
	}

	if (!ValidaData($string))
	    return SqlToData2($value);
	else
		return $string;
	
	
	
}


// Recebe um date
// Retorna ela no formato brasileiro com ano de 2 digitos: dd/mm/yy

function SqlToData2($value){
   if ((strlen($value) == 10) || (strlen($value) == 19)|| (strlen($value) == 24))
        $string = substr($value, 8, 2) . '/' . substr($value, 5, 2) . '/' . substr($value, 0, 4); // dd-mm-aaaa
    else
        $string = '';
    return $string;
}



// Recebe um numero ou string
// Retorna uma string no formato brasileiro de moeda R$ 100,00 ou 100,00
// dependendo do valor de $cifao
// $AShowPonto: Se desejar as unidades de milhar separadas por ponto
 
function toMoeda($value, $cifao = true, $AShowPonto = true){
    $value = str_replace('.', ',', sprintf("%01.2f", $value));

    if ($AShowPonto) {
        $parteInteira = substr($value, 0, -3);
        $parteDecimal = substr($value, -3);

        $temp = '';
        $j = 0;
        for ($i = strlen($parteInteira) - 1; $i >= 0; $i--) {
            if (($i >= 0) && ($j == 3)) {
                $temp = '.' . $temp;
                $j = 0;
            }

            $temp =  $parteInteira[$i] . $temp;
            $j++;
        }
        $value = $temp . $parteDecimal;
    }

    if ($cifao)
        return 'R$ ' . $value;

    return $value;
}


function getArquivos($nomedir){
// Note que !== n�o existia antes do PHP 4.0.0-RC2

    if ($handle = opendir($nomedir)) {        

        // Esta � a forma correta de varrer o diret�rio 
        $arquivos = array(); 
        while (false !== ($file = readdir($handle))) 
            if(!is_dir($file)) 
                $arquivos[] = $file;
        
        closedir($handle);
        return $arquivos;
    }
}

function pr($AArray, $AExit = false) {
    if ((is_array($AArray)) || (is_object($AArray))) {
        echo '<pre style="text-align: left;">';
        print_r($AArray);
        echo '</pre>';
    } else {
        echo '<pre style="text-align: left;">';
        echo "\n" . $AArray . "\n";
        echo '</pre>';
    }

    if ($AExit) {
        exit(0);
    }
}

function s($string, $start = null, $length = null) {
    return substr($string, $start, $length);
}

function retornaValorConfiguracao($identificacaoValidacao) 
{

    if (isset($_SESSION['CFGCONFIGURACOES_NET'][$identificacaoValidacao]))
    {
       return $_SESSION['CFGCONFIGURACOES_NET'][$identificacaoValidacao];
    }
    else
    {
       $res   = jn_query("SELECT VALOR_CONFIGURACAO, Coalesce(valor_complemento, '') as VALOR_COMPLEMENTO From CFGCONFIGURACOES_NET Where (Identificacao_Validacao = " . aspas($identificacaoValidacao) . ")");
        
       if ($row=jn_fetch_object($res))
       {
            $retorno = $row->VALOR_CONFIGURACAO . $row->VALOR_COMPLEMENTO;
       }
       else
       { 
            // Se não encontrou nas configurações específicas do portal, tenta ver se é alguma configuração das tabelas do 
            //AliançaPX (Cfg0001, Cfg0002, Cfg0003, CfgEmpresa)
            //Se não encontrar, vai retornar vazio.
            $retorno = retornaValorCFG0003($identificacaoValidacao);
       } 

       $_SESSION['CFGCONFIGURACOES_NET'][$identificacaoValidacao] = $retorno;   

       return $retorno;
    }
}




function decodeUrlAjax($AValue) {
    $output = iconv('UTF-8', 'ISO-8859-1', urldecode($AValue));
    return $output;
}

function upper($AValue) {
    return strtoupper($AValue);
}

function lower($AValue) {
    return strtolower($AValue);
}

function r($Search, $Replace = null, $Subject = null) {
    return str_replace($Search, $Replace, $Subject);
}

function quotedstr($string) {
    return "'" .  r("'", "\'", $string) . "'";
}

function getDataAtual() {
    return date('d/m/Y');
}

function getDataHoraAtual() {
    return date('d/m/Y H:i:s');
}

function getHoraAtual() {
    return date('H:i:s');
}

function addCampoDesabilitado($AIdCampo) {
    global $FORM_CAMPOS_DISABLED;
    if (is_array($AIdCampo)) {
        return count($FORM_CAMPOS_DISABLED = array_merge($FORM_CAMPOS_DISABLED, $AIdCampo));
    }
    return count($FORM_CAMPOS_DISABLED[] = $AIdCampo);
}

function isDisabled($ANomeCampo, $AArrCamposDisabled = null) {
    global $FORM_CAMPOS_DISABLED;
    if (is_array($AArrCamposDisabled)) {
        // retorno do parametro
        return in_array($ANomeCampo, $AArrCamposDisabled) ? 'true' : 'false';
    } else {
        // retorno do controle global de campos desabilidados
        return in_array($ANomeCampo, $FORM_CAMPOS_DISABLED) ? 'true' : 'false';
    }
}

function gerarNumeroRandomico($AMaxDigitos = 4) {
    $lMax = pow(10, $AMaxDigitos) - 1;
    $lMax = mt_rand(1, $lMax);
    
    return $lMax;
}

function decodeDataFromAjax(&$AData) {
    if (is_array($AData)) {
        $keys = array_keys($AData);
        foreach ($keys as $key) {
            decodeDataFromAjax($AData[$key]);
        }
    } else {
        $AData = decodeUrlAjax($AData);
    }

    return $AData;
}

function emptyToNbsp($AValue) {
    if ($AValue == '') {
        return '&nbsp;';
    }
    return $AValue;
}

function truncar($Texto, $Delitador, $App = '...') {
    $tamanho = strlen($Texto);
    if($tamanho > $Delitador)
        return $truncado = substr($Texto,0,$Delitador).$App;
    else
        return $Texto;
}

// Fun��o que compara duas datas
// As datas devem estar no formado: dd/mm/aaaa 
// Se algum dos parametros for passado como null,
// o sistema assume a data atual para ele
// Retorna:
//  -1 para A menor que B
//  0  para A igual a B
//  1 para A maior que B

function compareData($ADataA = null, $ADataB = null) {

    if (is_null($ADataA)) {
        $ADataA = date('d/m/Y');
    }
    $lDataA = @mktime(0, 0, 0,
        s($ADataA, 3, 2),
        s($ADataA, 0, 2),
        s($ADataA, 6, 4)
    );

    if (is_null($ADataB)) {
        $ADataB = date('d/m/Y');
    }
    $lDataB = mktime(0, 0, 0,
        s($ADataB, 3, 2),
        s($ADataB, 0, 2),
        s($ADataB, 6, 4)
    );

    if ($lDataA > $lDataB) {
        return 1;
    } else if ($lDataA == $lDataB) {
        return 0;
    } else if ($lDataA < $lDataB) {
        return -1;
    }
}

function getLogoOperadora($APathImages = './images') {

    // Busco pelo arquivo referente a logomarca da operadora

    $_ExtLogo = array('.png', '.PNG', '.jpg', '.JPG', '.jpeg', '.JPEG', '.bmp', '.BMP', '.gif', '.GIF');

    foreach ((array) $_ExtLogo as $ext) {
        if (file_exists($APathImages . '/logo_operadora' . $ext)) {
            return $APathImages . '/logo_operadora' . $ext;
        }
    }
    return '';
}

//Criado pelo Douglas Ticket 1659
function mimeRetornaTipo($mime){
   $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

            //Outros
            'Outros' => 'application/octet-stream'
        );
   foreach($mime_types as $key => $value){
      if($value == $mime)
         return $key;
   }

}

//Criado pelo Douglas Ticket 1659 
function remove_caracteres($string){
   $patterns = array();
   $patterns[0] = '/[ïiíìî]/';
   $patterns[1] = '/[ãaáàâä]/';
   $patterns[2] = '/[eéèêë]/';
   $patterns[3] = '/[õoóòôö]/';
   $patterns[4] = '/[uúùûü]/';
   $patterns[5] = '/[Çç]/';
   $patterns[6] = '/[Ññ]/';
   $patterns[7] = '/[@#$%_\-\/;:°ºª{¹²³£¢¬\*()+§,¨´`^~\]\[}><]/';   
   //$patterns[8] = '/ /';
   $patterns[9] = '/[IÍÌÏÎ]/';
   $patterns[10] = '/[ÁAÀÄÂÃ]/';
   $patterns[11] = '/[ÉEÈËÊ]/';
   $patterns[12] = '/[ÓOÒÖÔÕ]/';
   $patterns[13] = '/[ÚUÙÜÛ]/';
   $replacements = array();
   $replacements[0] = 'i';
   $replacements[1] = 'a';
   $replacements[2] = 'e';
   $replacements[3] = 'o';
   $replacements[4] = 'u';
   $replacements[5] = 'c';
   $replacements[6] = 'n';
   $replacements[7] = '_';
   //$replacements[8] = '_';
   $replacements[9] = 'I';
   $replacements[10] = 'A';
   $replacements[11] = 'E';
   $replacements[12] = 'O';
   $replacements[13] = 'U';
   return preg_replace($patterns, $replacements, $string);
}
function troca_caracteres($string,$valorTroca){
   $patterns = array();
   $patterns[0] = '/[ïiíìî]/';
   $patterns[1] = '/[ãaáàâä]/';
   $patterns[2] = '/[eéèêë]/';
   $patterns[3] = '/[õoóòôö]/';
   $patterns[4] = '/[uúùûü]/';
   $patterns[5] = '/[Çç]/';
   $patterns[6] = '/[Ññ]/';
   $patterns[7] = '/[@#$%_\-\/;:°ºª{¹²³£¢¬\*()+§,¨´`^~\]\[}><]/';   
   //$patterns[8] = '/ /';
   $patterns[9] = '/[IÍÌÏÎ]/';
   $patterns[10] = '/[ÁAÀÄÂÃ]/';
   $patterns[11] = '/[ÉEÈËÊ]/';
   $patterns[12] = '/[ÓOÒÖÔÕ]/';
   $patterns[13] = '/[ÚUÙÜÛ]/';
   $replacements = array();
   $replacements[0] = $valorTroca;
   $replacements[1] = $valorTroca;
   $replacements[2] = $valorTroca;
   $replacements[3] = $valorTroca;
   $replacements[4] = $valorTroca;
   $replacements[5] = $valorTroca;
   $replacements[6] = $valorTroca;
   $replacements[7] = $valorTroca;
   //$replacements[8] = '_';
   $replacements[9] = $valorTroca;
   $replacements[10] = $valorTroca;
   $replacements[11] = $valorTroca;
   $replacements[12] = $valorTroca;
   $replacements[13] = $valorTroca;
   return preg_replace($patterns, $replacements, $string);
}

function ValidaData($dat){

	if (strlen($dat) != 10)
	{
		return false;
		exit;
	}	

	$data = explode("/",$dat); // fatia a string $dat em pedados, usando / como refer�ncia
	$d = $data[0];
	$m = $data[1];
	$y = $data[2];

	if ($dat == '')
	{
	    //pr('data inv�lida',false);	
        return false;
	}
	
   // verifica se a data � v�lida!
	// 1 = true (v�lida)
	// 0 = false (inv�lida)
	$res = checkdate($m,$d,$y);
	
	//pr($res,false);
	
	if ($res == 1){
	   return true;
	} else {
	   return false;
	}
}

function testarValoresInSelect($parametro)
{
	$resValores = explode ( ',' , $parametro);
	$resultado  = $parametro;

	foreach($resValores as $item)
	{
		if ($item> 0)
		{
			$item=0;
		}
		else
		{
		   $resultado = '0';
		}
	}

	return $resultado;
}

function salvarImagem($tabela,$chave,$arquivo){
		//print_r($arquivo);
		$retorno = array();
		$retorno['nome'] = '';
		$retorno['id']   = '';
		
		$tabela = strtoupper($tabela);
		
		
		$nomeArquivo =  $arquivo['name'];
		
		$nomeArquivo = pathinfo($nomeArquivo);
		
		$extensao = $nomeArquivo['extension'];
		
		$valorChavePrimaria = jn_gerasequencial('CONTROLE_ARQUIVOS');
		
		if(!file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela))){
			mkdir('../../UploadArquivos/server/files/'. strtoupper($tabela), 0777, true);
		}
		
		if(!file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave)){
			mkdir('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave, 0777, true);
		}
		
		$i = 1;
		$rand = rand();

		while(file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao)){
			$i++;
		}
			
		move_uploaded_file($arquivo['tmp_name'],'../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao);
		
		//file_put_contents('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao, base64_decode($valor));
		
		if(file_exists('../../UploadArquivos/server/files/'. strtoupper($tabela) . "/C".$chave.'/'.'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao)){
			$dataUpload = dataToSql(date('d/m/Y'));
			if($_SESSION['type_db'] == 'sqlsrv'){
				$dataUpload = 'getDate()';
			}
			$query 	="INSERT INTO controle_arquivos(
						caminho_arquivo_armazenado,
						nome_arquivo_armazenado,
						data_upload,
						hora_upload,
						codigo_identificacao_arquivo,
						nome_arquivo_original,
						endereco_origem_in,
						historico_arquivo,
						nome_tabela,
						chave_registro
					)VALUES(".
						aspas(strtoupper($tabela) . "/C".$chave.'/').",".
						aspas('REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao).",".
						$dataUpload.",".
						aspas(date('H:i:s')).",".
						aspas($tabela.'_'.$chave).",".
						aspas($arquivo['name']).",".
						aspas($_SERVER["REMOTE_ADDR"]).",".
						aspas('IN:'.$_SESSION['codigoIdentificacao']).",".
						aspas(strtoupper($tabela)).",".
						aspas($chave)."
					);";
			$res = jn_query($query);
			
			$retorno['nome'] = 'REG_'.$chave.'_SEQ_'.$i.'_DATA_'.date('Ymd').'_'.md5($rand).'.'.$extensao;
			$retorno['id']   = $chave;

		}
	
	return $retorno;
}

function disparaEmailFunc($emailAssociado, $assunto, $corpoEmail, $emailRemetente = ''){
	$resEmpresa = jn_query($queryEmpresa = 'SELECT NOME_EMPRESA FROM CFGEMPRESA');
	$rowEmpresa = jn_fetch_object($resEmpresa);
	
	
	
	if($emailRemetente == ''){
		$emailRemetente = retornaValorConfiguracao('EMAIL_PADRAO');
	}
	
	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');
	$mail->SetFrom($emailRemetente, $rowEmpresa->NOME_EMPRESA);
	$mail->AddAddress($emailAssociado, $emailAssociado);
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoEmail);
		
	if(!$mail->Send()) {
		return false;		
	}else{
		return true;
	}
}


function jn_utf8_encode_array(&$input) {
    if (is_string($input)) {
        $input = jn_utf8_encode($input);
    } else if (is_array($input)) {
        foreach ($input as &$value) {
            jn_utf8_encode_array($value);
        }

        unset($value);
    } else if (is_object($input)) {
        $vars = array_keys(get_object_vars($input));

        foreach ($vars as $var) {
            jn_utf8_encode_array($input->$var);
        }
    }
}

function textoImagemEspacamento($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0){        
    if ($spacing == 0)
    {
        $bbox = imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
		$temp_x += $spacing + ($bbox[2] - $bbox[0]);
    }
    else
    {
		//Configuração Espaçamento imagem Guias
		global $configuracaoEspacamentoGuias;
		if($configuracaoEspacamentoGuias==null)
			$configuracaoEspacamentoGuias = 1;
		
		$spacing = $spacing * $configuracaoEspacamentoGuias;
        $temp_x = $x;
        for ($i = 0; $i < strlen($text); $i++)
        {
			if($text[$i]!=''){
				$bbox = imagettftext($image, $size, $angle, $temp_x, $y, $color, $font, $text[$i]);
				$temp_x += $spacing + ($bbox[2] - $bbox[0]);
			}
        }
		
    }
	
	return $temp_x;
}

function dataBrImagemEspacamento($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0,$spacingDate = 0){
	$dia = substr($text, 0, 2);
	$mes = substr($text, 3, 2);
	$ano = substr($text, 6, 4);
	
	$temp_x = textoImagemEspacamento($image, $size, $angle, $x, $y, $color, $font, $dia, $spacing);
	
	$temp_x = textoImagemEspacamento($image, $size, $angle, $temp_x+$spacingDate, $y, $color, $font, $mes, $spacing);
	
	$temp_x = textoImagemEspacamento($image, $size, $angle, $temp_x+$spacingDate, $y, $color, $font, $ano, $spacing);

}



function primeiraMaiuscula($string){
	return ucfirst(strtolower($string));	
}

function sanitizeString($str) {
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);    
    $str = preg_replace('/_+/', '_', $str);
    $str = preg_replace('/[,-.*@!#$%&]/ui', '', $str);
    
    return $str;
}


function eliminaMascaras($str) {

    /*$str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);    
    $str = preg_replace('/_+/', '_', $str);
    $str = preg_replace('/[,-.*@!#$%&]/ui', '', $str);*/

    $str = remove_caracteresT($str);
    $str = str_replace(" ","",$str);

    return $str;
}


function retiraAcentos($str)  // removeAcentos
{
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);    
    
    return $str;
}

function formatarTelefone($telefone) {
    // Remove todos os caracteres que não são dígitos
    $telefone = preg_replace('/\D/', '', $telefone);
    
    // Verifica se o número de telefone possui 9 dígitos
    if (strlen($telefone) === 9) {
        return $telefone; // Já possui 9 dígitos, retorna sem modificação
    } elseif (strlen($telefone) === 8) {
        // Adiciona um "9" na frente do número e formata com hífen
        $telefone = '9' . $telefone;
        return $telefone;
    } else {
        // Não é possível formatar o número
        return false;
    }
}

function retornaValorCfgPx($identificacaoValidacao)
{

    return retornaValorCFG0003($identificacaoValidacao);

}



function retornaValorCFG0003($identificacaoValidacao) {


    if (isset($_SESSION['CFG0003'][$identificacaoValidacao]))
    {
       return $_SESSION['CFG0003'][$identificacaoValidacao];
    }
    else
    {

       $retorno = '';

       $res   = jn_query("SELECT VALOR_CONFIGURACAO, VALOR_COMPLEM_CONFIGURACAO From CFG0003 
                          Where (IDENTIFICADOR_CONFIGURACAO = " . aspas($identificacaoValidacao) . ")");       

       if ($row=jn_fetch_object($res))
       { 
            $retorno = $row->VALOR_CONFIGURACAO . $row->VALOR_COMPLEM_CONFIGURACAO;
       }
       else 
       {
            $identificacaoValidacao = strtoupper($identificacaoValidacao);

            $query = 'select NUMERO_REGISTRO, NOME_TABELA from CFGCAMPOS_SIS WHERE ' . 
                      ' NOME_TABELA in (' . aspas('CFG0001') . ',' . aspas('CFG0002') . ',' . aspas('CFGEMPRESA') . ') ' . 
                      ' and NOME_CAMPO = ' . aspas($identificacaoValidacao);

            $resRetorno = jn_query($query);

            if ($rowRetorno = jn_fetch_object($resRetorno))
            {
                $query      = 'select ' . $identificacaoValidacao . ' from ' . $rowRetorno->NOME_TABELA;
                $resRetorno = jn_query($query);
                $rowRetorno = jn_fetch_object($resRetorno);
                $retorno    = $rowRetorno->$identificacaoValidacao;
            }
       } 

       $_SESSION['CFG0003'][$identificacaoValidacao] = $retorno;   

       return $retorno;
    }

}


function remove_caracteresT($string){
   $patterns = array();
   $patterns[0] = '/[ïiíìî]/';
   $patterns[1] = '/[ãaáàâä]/';
   $patterns[2] = '/[eéèêë]/';
   $patterns[3] = '/[õoóòôö]/';
   $patterns[4] = '/[uúùûü]/';
   $patterns[5] = '/[Çç]/';
   $patterns[6] = '/[Ññ]/';
   $patterns[7] = '/[@#$%_\-\/;:°ºª{¹²³£¢¬\*()+§,¨´`^~\]\[}><]/';   
   //$patterns[8] = '/ /';
   $patterns[9] = '/[IÍÌÏÎ]/';
   $patterns[10] = '/[ÁAÀÄÂÃ]/';
   $patterns[11] = '/[ÉEÈËÊ]/';
   $patterns[12] = '/[ÓOÒÖÔÕ]/';
   $patterns[13] = '/[ÚUÙÜÛ]/';
   $replacements = array();
   $replacements[0] = 'i';
   $replacements[1] = 'a';
   $replacements[2] = 'e';
   $replacements[3] = 'o';
   $replacements[4] = 'u';
   $replacements[5] = 'c';
   $replacements[6] = 'n';
   $replacements[7] = '';
   //$replacements[8] = '_';
   $replacements[9] = 'I';
   $replacements[10] = 'A';
   $replacements[11] = 'E';
   $replacements[12] = 'O';
   $replacements[13] = 'U';
   return preg_replace($patterns, $replacements, $string);
}


function retornaCampo($i,$string,$caracter = ',')
{

    $resValores = explode ($caracter , $string);
    $resultado  = '';
    $contador   = 0;

    foreach($resValores as $item)
    {
        if ($i == $contador)
        {
            $resultado = $item;
        }
        $contador++;
    }

    return $resultado;
}



function copyDelphi($string,$de,$ate)
{
    return substr($string,$de-1,$ate);
}



function strZero($string, $quantidade)
{
    return str_pad($string, $quantidade, 0, STR_PAD_LEFT);
}



function todos_caracteres_iguais($str) {
    $first = $str[0];
    for ($i = 1; $i < strlen($str); $i++) {
        $char = $str[$i];
        if ($char != $first) {
            return false;
        }
    }
    return true;
}


function apresentaValorPorExtenso( $v ){
    $sin = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plu = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

    $z = 0;

    $v = number_format( $v, 2, ".", "." );
    $int = explode( ".", $v );
    for ( $i = 0; $i < count( $int ); $i++ ) 
    {
      for ( $ii = mb_strlen( $int[$i] ); $ii < 3; $ii++ ) 
      {
          $int[$i] = "0" . $int[$i];
      }
    }

    $rt = null;
    $fim = count( $int ) - ($int[count( $int ) - 1] > 0 ? 1 : 2);
    for ( $i = 0; $i < count( $int ); $i++ )
    {
      $v = $int[$i];
      $rc = (($v > 100) && ($v < 200)) ? "cento" : $c[$v[0]];
      $rd = ($v[1] < 2) ? "" : $d[$v[1]];
      $ru = ($v > 0) ? (($v[1] == 1) ? $d10[$v[2]] : $u[$v[2]]) : "";

      $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
      $t = count( $int ) - 1 - $i;
      $r .= $r ? " " . ($v > 1 ? $plu[$t] : $sin[$t]) : "";
      if ( $v == "000")
          $z++;
      elseif ( $z > 0 )
          $z--;
          
      if ( ($t == 1) && ($z > 0) && ($int[0] > 0) )
          $r .= ( ($z > 1) ? " de " : "") . $plu[$t];
          
      if ( $r )
          $rt = $rt . ((($i > 0) && ($i <= $fim) && ($int[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    $rt = mb_substr( $rt, 1 );

    return($rt ? trim( $rt ) : "zero");
 
}

function Mask($mask,$str){

    $str = str_replace(" ","",$str);

    for($i=0;$i<strlen($str);$i++){
        $mask[strpos($mask,"#")] = $str[$i];
    }

    return $mask;

}

?>