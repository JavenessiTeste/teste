<?php 
require('../private/autentica.php');

$quantAbas = $_GET['qtAbas'] ? $_GET['qtAbas'] : 0;
$numAut = $_GET['numeroAutorizacao'];

if($quantAbas == 0){
    require('../lib/base.php');

    $queryQuantGuias  = ' SELECT COUNT(*) AS QUANTIDADE_PROCEDIMENTOS FROM PS6510 ';
    $queryQuantGuias .= ' WHERE PS6510.NUMERO_AUTORIZACAO = ' . aspas($numAut);
    $resQuantGuias = jn_query($queryQuantGuias);
    $rowQuantGuias = jn_fetch_object($resQuantGuias);
    $quantidadeProc = $rowQuantGuias->QUANTIDADE_PROCEDIMENTOS;

    if($quantidadeProc <= 5){
        header('Location: ../ProcessoDinamico/guiaSpsadtPng.php?numero=' . $numAut);
    }else{

        $quantAbas = ceil($quantidadeProc / 5);
        header('Location: fluxoSpSadtDiversosProcedimentos.php?numeroAutorizacao=' . $numAut . '&qtAbas=' . $quantAbas);    
    }

}else{
    echo ' <input id="numeroAutorizacao" nome="numeroAutorizacao" type="hidden" value="' . $numAut .'">';
    echo ' <input id="qtdAbas" nome="qtdAbas" type="hidden" value="' . $quantAbas . '">';
}

?>

<script language="javascript" type="text/javascript">
    var qtdAbas = document.getElementById('qtdAbas').value;
    var numeroAutorizacao = document.getElementById('numeroAutorizacao').value;
    var janelaAtual = 0;
    
    i = 1;
    while(i <= qtdAbas){
        chamarFormGuias(i);
        i++;
    }            

    function chamarFormGuias(janelaAbrir) {
        if(qtdAbas != janelaAbrir){
            window.open('../ProcessoDinamico/guiaSpsadtPng.php?numero=' + numeroAutorizacao + '&janelaAbrir='+janelaAbrir, '_blank');
        }else{
            window.location.href = '../ProcessoDinamico/guiaSpsadtPng.php?numero=' + numeroAutorizacao + '&janelaAbrir='+janelaAbrir;
        }
        
    }

</script>