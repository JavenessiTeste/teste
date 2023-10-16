<?php
require('../lib/base.php');
require("vendor/autoload.php");
require 'MyLogPHP.php';

global $pagSeguro;

$log = new MyLogPHP('log.csv',';');
 
$log->info('Inicio Pagina.'); 
 


//Verifica se foi enviado um método post
if($_SERVER['REQUEST_METHOD'] == 'POST'){
 
	$log->info('Inicio Post.');
 
	foreach($_POST as $key => $value){
		$log->info($key,$value);		
	}
 
}else{
	$log->info('Inicio Get.');
 
	foreach($_GET as $key => $value){
		$log->info($key,$value);		
	}
	
}