<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

echo 'testes2';
$serverName = "serverName";
$connectionInfo = array( "Database"=>"Alianca08_01_Prod", "UID"=>"sa", "PWD"=>"Senha");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
	

if( $conn ) {
	 echo "Connection established.<br />";
}else{
	 echo "Connection could not be established.<br />";
	 die( print_r( sqlsrv_errors(), true));
}

?>