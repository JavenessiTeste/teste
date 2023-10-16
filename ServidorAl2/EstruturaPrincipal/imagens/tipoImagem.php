<html>
<?php
/* This will give an error. Note the output
 * above, which is before the header() call */
 if(strtoupper($_GET['tipo'])=='PDF'){
	header('Location: pdf.png');
 }elseif(strtoupper($_GET['tipo'])=='JPG'){
	header('Location: jpg.png');
 }else{
	header('Location: outros.png');
 }

exit;
?>