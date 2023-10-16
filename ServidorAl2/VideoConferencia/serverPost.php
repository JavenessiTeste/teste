<?php
require('../lib/base.php');

header("Access-Control-Allow-Origin: *");
// A unique identifier (not necessary when working with websockets)
if (!isset($_GET['unique'])) {
    die('no identifier');
}
$unique=$_GET['unique'];
//if (strlen($unique)==0 || ctype_digit($unique)===false) {
//   die('not a correct identifier');
//}

if(!file_exists('controle_'.$unique)){
	$file = fopen('controle_'.$unique,'ab');
	fclose($file);
}	

    
// A main lock to ensure save safe writing/reading
$mainlock = fopen('controle_'.$unique,'r');
if ($mainlock===false) {
    die('could not create main lock');
}
flock($mainlock, LOCK_EX);
   
// Add the new message to file
$filename = '_file_'. $unique;
$file = fopen($filename,'ab');
if (filesize($filename)!=0) {
    fwrite($file,'_MULTIPLEVENTS_');
}
$posted = file_get_contents('php://input');
fwrite($file,$posted);
fclose($file);

// Unlock main lock
flock($mainlock,LOCK_UN);
fclose($mainlock);

?>