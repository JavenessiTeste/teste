<?php
//require('base.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$dados = array(
    "host" => "ftp.vidamax.net.br",
    "usuario" => "vidamax",
    "senha" => "VxADB32djfbcLmU"
);

$fconn = ftp_connect($dados["host"]);

#Utilizamos a função ftp_login() para realizar o login no servidor, que recebe como parâmetro a conexão, usuário e senha.
ftp_login($fconn, $dados["usuario"], $dados["senha"]);
//ftp_put($fconn, "/public_html/texto.txt", "/texto.txt", FTP_BINARY);
$retorno = ftp_get($fconn, "../../ServidorCliente/Boletos/Boleto_ITAMAR_G_G_JUNIOR_10032019.pdf", "/www/Aliancaappnet2/Boletos/Boleto_ITAMAR_G_G_JUNIOR_10032019.pdf", FTP_BINARY);


?>