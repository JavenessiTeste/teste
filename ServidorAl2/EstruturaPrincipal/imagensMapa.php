<?php
require('../lib/base.php');
global $chaveGoogleMaps;

$aux = true;
$_SERVER['QUERY_STRING'] = tirarAcentos($_SERVER['QUERY_STRING']);
if(file_exists('../../mapas/'. md5($_SERVER['QUERY_STRING']). '.png')){
	$aux = false;
	
	$imagem = imagecreatefrompng('../../mapas/'. md5($_SERVER['QUERY_STRING']). '.png');
	$tamanho = filesize('../../mapas/'. md5($_SERVER['QUERY_STRING']). '.png');
	//print_r($problema);
	//exit;
	if(($tamanho==8184)or ($tamanho<5000)){
		$aux = true;
		unlink('../../mapas/'. md5($_SERVER['QUERY_STRING']). '.png');
	}else{
		header( 'Content-type: image/png' );
		imagepng($imagem,NULL,9);	
	}
	imagedestroy($imagem);
	imagedestroy($problema);
}
if($aux){

	
	
	$url = 'http://maps.google.com/maps/api/staticmap?'.$_SERVER['QUERY_STRING'].'&key='.$chaveGoogleMaps;
	
	$ch = CURL_INIT();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($ch);

	$mostraimagemErro = false;
	if($result === false)
	{
		$mostraimagemErro = true;
	}else if(strlen($result)<500){
		$mostraimagemErro = true;
	}
	

	
	$info = curl_getinfo($ch);

	//$start = $info['header_size'];
	//$body = substr($result, $start, strlen($result) - $start);

	curl_close($ch);
	//$output = $result;
	//$arr = json_decode($body, TRUE);
			
	if($result){
	   $fp = fopen('../../mapas/'. md5($_SERVER['QUERY_STRING']). '.png','w');	
	   fwrite($fp, $result);
	   fclose($fp);
	}
	if($mostraimagemErro){
		$imagem = imagecreatefrompng('../../mapas/erroMapa.png');
		
		
		header( 'Content-type: image/png' );
		imagepng($imagem,NULL,9);	
		imagedestroy($imagem);	
			
	}
	
	header( 'Content-type: image/png' );
	echo $result;
	print_r($body);
	
}
	

function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

?>