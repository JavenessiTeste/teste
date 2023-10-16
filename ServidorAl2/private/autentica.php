<?php
/*
 * Este arquivo tem por finalidade forçar a verificação de autenticidade do
 * usuário.
 * Se o usuário estiver autenticado, ele não faz nada.
 *
 * Se o usuario não estiver autenticado, ele redireciona para a
 * tela de autenticação.
 *
 * Este arquivo deverá ser o primeiro a ser incluído no sistema para fins
 * de validação, ou seja, todas as páginas que necessitarem de autenticação,
 * devem incluir em sua primeira linha de codigo php este arquivo.
 */

session_start();

if(@$_SESSION['codigoIdentificacao'] == '') {
	$_SESSION = null;
    header("HTTP/1.0 403 Forbidden");
	//
	exit;
} else {
    // pego os valores da sessao...
    $UsuarioLogado['AUTENTICADO']   = true;
    $UsuarioLogado['CODIGO']        = $_SESSION['codigoIdentificacao'];
    $UsuarioLogado['NOME']          = $_SESSION['nomeUsuario'];
    $UsuarioLogado['PERFIL']        = $_SESSION['perfilOperador'];
    
    if(isset($_SESSION['username']))
        $UsuarioLogado['USERNAME']      = $_SESSION['username'];

    $OPERADORA['RAZAO_SOCIAL']      = $_SESSION['razaoSocialOperadora'];
    
    // TODO: Jogar esta linha no SysConfig...
    // $_SESSION['versaoAplicacao']       = '3.0.001';
}



?>