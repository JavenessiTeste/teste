<?php
require_once('../lib/class.phpmailer.php');
require_once('../lib/class.smtp.php');
require_once('../lib/PHPMailerAutoload.php');



function leInformacaoCampoLayout($informacaoLinhaArquivo, $rowDadosLayout, $nomeCampo)
{

    //pr('InfLinha:' .$informacaoLinhaArquivo);
    //pr('InfCampo: ' . $rowDadosLayout->NOSSO_NUMERO_INICIAL);
    //pr('FuncaoCalFinal:' . retornaPosicaoFinalArquivoRetornoBancario($rowDadosLayout,'NOSSO_NUMERO'));

    if ($campoRetornar=='CAMPO_XXXXX') // Quando o campo for personalizado e não for direto do campo da tabela do banco
    {
        $retorno = copyDelphi($informacaoLinhaArquivo,$rowDadosLayout->NOSSO_NUMERO_INICIAL,retornaPosicaoFinalArquivoRetornoBancario($rowDadosLayout,'NOSSO_NUMERO'));
    }
    else
    {
        $campoPosicaoInicial = $nomeCampo . '_INICIAL';
        $retorno             = copyDelphi($informacaoLinhaArquivo,$rowDadosLayout->$campoPosicaoInicial,retornaPosicaoFinalArquivoRetornoBancario($rowDadosLayout,$nomeCampo));
    }

    return $retorno;

}




function retornaPosicaoFinalArquivoRetornoBancario($rowDadosLayout,$nomeCampo)
{

   $nomeCampoInicial = $nomeCampo . '_INICIAL';
   $nomeCampoFinal   = $nomeCampo . '_FINAL';

   //pr('$nomeCampoInicial' . $nomeCampoInicial);
   //pr('$nomeCampoFinal' . $nomeCampoFinal);

   $resultado = ($rowDadosLayout->$nomeCampoFinal - $rowDadosLayout->$nomeCampoInicial) + 1;
   //pr('conta:' . $resultado);
    
   return $resultado;

}




function retornaNumeroRegistroPs1020Baixa($seuNumero,$nossoNumero)
{

    $numeroRegistroRetornar = $seuNumero;

    return  $numeroRegistroRetornar;

}



function ajustaSituacaoAtendimentoPosBaixaFaturas($numeroRegistroPs1020)
{

    // Aqui vou colocar tratamentos específicos quando uma fatura for baixada, pois alguns clientes tem tratamento especial (por exemplo a Hebrom)

}



function podeModificarFaturamento($mesAnoReferencia,$tipo,$numeroRegistroPs1020='') // $tipo = 'CALCULO_FAT, BAIXA_PAGTO'
{

    $rowValidacao = qryUmRegistro('Select Mes_Ano_Referencia, Flag_Travar_Edicao, Flag_Travar_Baixa 
                                   From Ps1067 Where (Mes_Ano_Referencia = ' . aspas($mesAnoReferencia) . ') and ' . '(Flag_Travar_Edicao = ' . aspas('S') . ') ');

    $podeModificarFaturamento = true;

    if (($rowValidacao->FLAG_TRAVAR_EDICAO=='S') and ($tipo=='CALCULO_FAT'))
        $podeModificarFaturamento = false;

    if (($rowValidacao->FLAG_TRAVAR_BAIXA=='S') and ($tipo=='BAIXA_PAGTO'))
        $podeModificarFaturamento = false;

    return $podeModificarFaturamento;

}



?>

