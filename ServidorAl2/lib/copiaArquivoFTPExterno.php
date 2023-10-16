<?php

global $nomeArquivo;
global $caminhoArquivoLocal;
global $caminhoArquivoExterno;

$dados = array(
    "host" => retornaValorConfiguracao('HOST_FTP_EXTERNO'),
    "usuario" => retornaValorConfiguracao('USUARIO_FTP_EXTERNO'),
    "senha" => retornaValorConfiguracao('SENHA_FTP_EXTERNO'),
);

$fconn = ftp_connect($dados["host"]);
ftp_login($fconn, $dados["usuario"], $dados["senha"]);
$retorno = ftp_get($fconn, $caminhoArquivoLocal . $nomeArquivo, $caminhoArquivoExterno . $nomeArquivo, FTP_BINARY);


?>