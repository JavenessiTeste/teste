<?php

/* 	Senhor te entregamos estes fontes, nossos projetos, nosso trabalho e toda a nossa vida!
	Conduza este projeto com tua sabedoria, pois nosso entendimento é limitado e pouco sabemos o que devemos fazer. Então, venha com Tua
	sabedoria Divina e nos oriente em como proceder.
	Te Amamos, pois Tu SEMPRE NOS CONDUZIU, seremos eternamente gratos e temos a alegria de saber que dependemos da Tua Graça, pois é ela quem nos conduz.
	Obrigado por tudo e continue sempre conosco, amém! */

//ini_set('display_errors', 1);
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
//header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
//header("Access-Control-Allow-Origin: http://localhost:4200");//deixar somente para rodar pelo localhosta, remover em produção
//header("Access-Control-Allow-Origin: http://192.168.100.10:8100/");//deixar somente para rodar pelo localhosta, remover em produção
//header("Access-Control-Allow-Origin: http://localhost:8100, http://localhost:4200");//deixar somente para rodar pelo localhosta, remover em produção
//header("Access-Control-Allow-Origin: http://localhost:8080");//deixar somente para rodar pelo localhosta, remover em produção
//header("Access-Control-Allow-Credentials: true");
//
header('Access-Control-Allow-Methods: *');
header('Content-type: application/json');

header("Access-Control-Allow-Headers:Accept, PKID, pkid,access-control-allow-origin");

if(!function_exists('getallheaders')){
    function getallheaders(){
       $headers = [];
       foreach ($_SERVER as $name => $value){
           if (substr($name, 0, 5) == 'HTTP_'){
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
}

$headers = Array();
$headers = getallheaders();

global $celular;

$celular = false;
$idCelular = '';
$host = '';

foreach ($headers as $header => $value) 
{
	//echo $header.'<br>';
	if(strtoupper($header) == 'PKID'){
		$idCelular  = $value;
		$celular	= true;
		//break;
	}
	if(strtoupper($header) == 'REFERER'){
		$host = $value;
		//$celular	= true;
		//break;
	}
	if(strtoupper($header) == 'ORIGIN'){
		$host = $value;
		//$celular	= true;
		//break;
	}
	if((strtoupper($header) == 'ACCESS-CONTROL-REQUEST-HEADERS')and strtoupper($value)=='PKID'){
		$celular	= true;
	}
}

//$celular	= true;
//echo 'OK';
		
if(!$celular){
	header("Access-Control-Allow-Credentials: true");
}

if((strtolower(trim(@$_SERVER['HTTP_ORIGIN']))=='https://localhost:4200')or(strtolower(trim(@$_SERVER['HTTP_ORIGIN']))=='https://localhost:4200/')or($celular)){
	if(strtolower(trim(@$_SERVER['HTTP_ORIGIN']))=='https://localhost:4200' or strtolower(trim(@$_SERVER['HTTP_ORIGIN']))=='https://localhost:4200/')
		header("Access-Control-Allow-Origin: https://localhost:4200");
    else
		header("Access-Control-Allow-Origin: ".$host);
	$cookie_timeout = 0;
	$cookie_domain = '';
	$session_secure = true;
	$cookie_httponly = true;

	if (PHP_VERSION_ID >= 70300) { 
		session_set_cookie_params([
			'lifetime' => $cookie_timeout,
			'path' => '/',
			'domain' => $cookie_domain,
			'secure' => $session_secure,
			'httponly' => $cookie_httponly,
			'samesite' => 'NONE'
		]);
	} else { 
		session_set_cookie_params(
			$cookie_timeout,
			'/; samesite=NONE',
			$cookie_domain,
			$session_secure,
			$cookie_httponly
		);
	}
}

if(@$_SERVER['REQUEST_METHOD']=='OPTIONS'){
	header("HTTP/1.1 200 OK");
	exit;
}

//if(isset($_GET['Teste']) == 'OK')
//   ini_set('display_errors', 1); // Ensure errors get to the user.
//else
//   ini_set('display_errors', 0); // Ensure errors get to the user.




error_reporting(E_ALL & ~E_NOTICE);
session_cache_expire(60); // sessao dura 60 minutos


@session_start();

$_SESSION['APP'] = false;


if($idCelular !=''){
	$_SESSION['idDevice']  = $idCelular;
	$_SESSION['APP']	   = $celular;
}
 


date_default_timezone_set('America/Sao_Paulo');
require('../lib/jnQuery.php');
require('../lib/jnErro.php');
require('../lib/jnGrid.php');
require('../lib/jnDataValidator.php');
require('../../ServidorCliente/config.php');
require('../private/conecta_db.php');
require('../lib/sysutils.php');
require('../lib/sysutils_db.php');
require('../EstruturaEspecifica/permissoes.php');
require('../lib/convertImagemBase64.php');
require('../lib/sysutils01.php');


global $ObjErro;
global $ObjDataVal;
global $codigoSmart;
global $SisConfig;
global $dadosInput;

$SisConfig = array();
$ObjErro = new jnErro();
$ObjErro->Clear();
$ObjDataVal = new jnDataValidator();




$dadosInput   = file_get_contents("php://input");

$dadosInput = json_encode($dadosInput);

$dadosInput = json_decode(file_get_contents('php://input'), TRUE); 

if(isset($_GET['Teste']) == 'OK'){
	pr($dadosInput);
}
if ((array)$dadosInput ) { 
	$dadosInput = addslashesArray($dadosInput);
}

function addslashesArray(&$dados){
	foreach ($dados as $key => $value){
			if(gettype($value)=='array'){
				$dados[$key] = addslashesArray($value);
			}else{
				$dados[$key] = addslashes($value);
			}
	}
	return $dados;
}

//$_GET['Teste'] = 'OK';

if($_SESSION['APP']){
	$qry = "SELECT APP_USUARIO_INTERNO.CODIGO_INTERNO, APP_USUARIO_INTERNO.CODIGO_USUARIO, APP_USUARIO_INTERNO.PERFIL_USUARIO, 
	               APP_VW_DADOS_LOGIN_V3.QUANTIDADE_CONTRATOS, CFGEMPRESA.CODIGO_SMART, APP_VW_DADOS_LOGIN_V3.CODIGO_TIPO_CARACTERISTICA, 
				   PS1030.*, PS1000.CODIGO_PLANO,PS1000.CODIGO_TITULAR,Ps1000.NOME_ASSOCIADO FROM APP_LOGIN_AUTOMATICO
			INNER JOIN  APP_USUARIO_INTERNO ON APP_USUARIO_INTERNO.CODIGO_INTERNO = APP_LOGIN_AUTOMATICO.CODIGO_INTERNO and APP_USUARIO_INTERNO.CODIGO_CONTRATANTE = '2' 
			INNER JOIN  APP_VW_DADOS_LOGIN_V3  ON APP_VW_DADOS_LOGIN_V3.CODIGO_USUARIO = APP_USUARIO_INTERNO.CODIGO_USUARIO
			LEFT OUTER JOIN PS1000 ON (APP_VW_DADOS_LOGIN_V3.CODIGO_USUARIO = PS1000.CODIGO_ASSOCIADO) 
			LEFT OUTER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) 
			INNER JOIN  CFGEMPRESA ON (1=1)
			WHERE APP_LOGIN_AUTOMATICO.FLAG_CONECTAR_AUTOMATICO = 'S' AND APP_LOGIN_AUTOMATICO.CODIGO_DEVICE = " . aspas($_SESSION['idDevice']);

	$resQuery = jn_query($qry);
					

					
	if ($objResult = jn_fetch_object($resQuery))
	{
			$queryEmp = 'SELECT NOME_EMPRESA, CODIGO_SMART FROM CFGEMPRESA ';
			$resEmp = jn_query($queryEmp);
			$rowEmp = jn_fetch_object($resEmp);
			
			$_SESSION['codigoIdentificacao']          = $objResult->CODIGO_USUARIO;
			$_SESSION['codigoIdentificacaoTitular']   = $objResult->CODIGO_TITULAR;
			$_SESSION['nomeUsuario']           = jn_utf8_encode($objResult->NOME_ASSOCIADO);
			$_SESSION['versaoAplicacao']       = '1.0.0';
			$_SESSION['ErrorList']             = array();
			$_SESSION['UrlAcesso']             = $_SERVER['HTTP_REFERER'];
			$_SESSION['HorarioAcesso']         = @mktime();
			$_SESSION['IpUsuario']             = $_SERVER['REMOTE_ADDR'];
			$_SESSION['nomeEmpresa']           = $rowEmp->NOME_EMPRESA;
			$_SESSION['codigoSmart']           = $rowEmp->CODIGO_SMART;
			//$_SESSION['SESSAO_ID'] 			   = jn_gerasequencial('CFGLOG_NET');
		
			$_SESSION['CODIGO_INTERNO']             =  $objResult->CODIGO_INTERNO;
			$_SESSION['CODIGO_USUARIO']             =  $objResult->CODIGO_USUARIO;
			$_SESSION['PERFIL_USUARIO']             =  $objResult->PERFIL_USUARIO;
			$_SESSION['perfilOperador']				=  $objResult->PERFIL_USUARIO;
			$_SESSION['CODIGO_CONTRATANTE']         =  $objResult->CODIGO_CONTRATANTE;	 
			$_SESSION['QUANTIDADE_CONTRATOS']       =  $objResult->QUANTIDADE_CONTRATOS;
			$_SESSION['CODIGO_TIPO_CARACTERISTICA'] =  $objResult->CODIGO_TIPO_CARACTERISTICA; 
			$_SESSION['CODIGO_SMART']               =  $objResult->CODIGO_SMART;
			$_SESSION['REDE_INDICADA']			    =  $objResult->REDE_INDICADA;
			$_SESSION['CODIGO_PLANO']               =  $objResult->CODIGO_PLANO;
				
		 
	}
	if($_GET['Teste']=='OK'){
		pr($_SESSION);
	}
}
//exit;
?>
