<?php

$name = 'retornoPagamentoCredito.log';
$text = json_encode($_GET)."\n";
$file = fopen($name, 'a');
fwrite($file, $text,strlen($text));
fclose($file);


?>
