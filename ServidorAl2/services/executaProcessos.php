<?php 
require('../lib/base.php');
@header('content-type: text/html');

global $codigoIdProcesso;

if(isset($_GET['opForm'])){

    $codigoIdProcesso = $_GET['idProcessoInterno'];

    if($_GET['opForm'] == 'executarProcesso'){

        atualizaStatusProcesso($codigoIdProcesso, 'AGUARDANDO');
        
        $retornoProcesso = validaProcessoIniciado();
        if($retornoProcesso['ID_PROCESSO'] == '' or $retornoProcesso['ID_PROCESSO'] == null)
            executaShellPHP('executaProcessos.php');
    }
}else{

    $retornoGet = getopt('j:');
    $codigoIdProcesso = $retornoGet['j'];
    
    $retornoProc = executaProcessosComplexos($codigoIdProcesso);
    return $retornoProc; 
}

function executaProcessosComplexos($codigoIdProcesso){

    $script = buscaUrlProcesso($codigoIdProcesso);    
    $php_exe = 'php';
    $php_ini = php_ini_loaded_file();

    if (stripos(PHP_OS, 'WIN') !== false) {
        $exec = 'start /B cmd /S /C ' . escapeshellarg($php_exe . ' -c ' . $php_ini . ' ' . $script) . ' -j ' . $codigoIdProcesso . ' > NUL'; //Windows
    } else {
        $exec = 'php -c ' . $php_ini . ' ' . $script . ' -j ' . $codigoIdProcesso . ' >/dev/null 2>&1'; //Unix
    }

    $descriptorspec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];

    $proc = proc_open($exec, $descriptorspec, $pipes);    
    $proc_details = Array();
    $proc_details = proc_get_status($proc);

    if(isset($codigoIdProcesso))
        atualizaStatusProcesso($codigoIdProcesso, 'INICIADO_CLI', $proc_details['pid']);
    
    $i = 0;
    while($i < 1){           
        $aguardarProcesso = stream_get_contents($pipes[2]);        
        $i++;       
    }
    
    $resultadoProcesso = proc_close($proc);

    if(isset($codigoIdProcesso))
        atualizaStatusProcesso($codigoIdProcesso, 'FINALIZADO_CLI');
    
    $idProximoProcesso = validaProcessoFila();
    
    if($idProximoProcesso)
        $retornoProc = executaProcessosComplexos($idProximoProcesso);

    return true;
}

function executaShellPHP($script, $php_exe = 'php', $php_ini = null) {
    
    global $codigoIdProcesso;

    $script = realpath($script);
    $php_exe = 'php';
    $php_ini = php_ini_loaded_file();

    if (stripos(PHP_OS, 'WIN') !== false) {        
        $exec = 'start /B cmd /S /C ' . escapeshellarg($php_exe . ' -c ' . $php_ini . ' ' . $script) .  ' -j ' . $codigoIdProcesso . ' > NUL';//Windows
    } else {                
        $exec = 'php -c ' . $php_ini . ' ' . $script . ' -j ' . $codigoIdProcesso . ' >/dev/null 2>&1'; //Unix
    }
    
    $descriptorspec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];

    $proc = proc_open($exec, $descriptorspec, $pipes);
    $proc_details = Array();
    $proc_details = proc_get_status($proc);
}

function validaProcessoIniciado(){

    $queryProcAtivo = 'SELECT COALESCE(NUMERO_REGISTRO_PROCESSO, "") AS PROCESSO_ATIVO FROM CFGDISPAROPROCESSOSCABECALHO WHERE STATUS_FILA = ' . aspas('INICIADO_CLI');
    $resultadoProcAtivo = qryUmRegistro($queryProcAtivo);
    
    $retorno = Array();    
    $retorno['ID_PROCESSO'] = $resultadoProcAtivo->PROCESSO_ATIVO;
    
    return $retorno;
}

function atualizaStatusProcesso($codigoIdProcesso, $status, $idCli = ''){    
    
    $criterioWhereGravacao = ' NUMERO_REGISTRO_PROCESSO = ' . aspas($codigoIdProcesso);

    $sqlEdicao  = linhaJsonEdicao('STATUS_FILA', $status);

    if($idCli != '')                 
        $sqlEdicao .= linhaJsonEdicao('CODIGO_ID_CLI', $idCli);  

    gravaEdicao('CFGDISPAROPROCESSOSCABECALHO', $sqlEdicao, 'A', $criterioWhereGravacao);  
            
}

function validaProcessoFila(){
    $queryAguardando  = ' SELECT NUMERO_REGISTRO_PROCESSO FROM CFGPROCESSOS_PD ';
    $queryAguardando .= ' INNER JOIN CFGDISPAROPROCESSOSCABECALHO ON (CFGPROCESSOS_PD.NUMERO_REGISTRO = CFGDISPAROPROCESSOSCABECALHO.IDENTIFICACAO_PROCESSO) ';
    $queryAguardando .= ' WHERE CFGDISPAROPROCESSOSCABECALHO.STATUS_FILA = "AGUARDANDO" AND COALESCE(CFGPROCESSOS_PD.NUMERO_PRIORIDADE, 1) > 0 ';
    $queryAguardando .= ' ORDER BY COALESCE(CFGPROCESSOS_PD.NUMERO_PRIORIDADE, 999), CFGDISPAROPROCESSOSCABECALHO.NUMERO_REGISTRO_PROCESSO ';
    
    $resultadoAguardando = qryUmRegistro($queryAguardando);
    
    return $resultadoAguardando->NUMERO_REGISTRO_PROCESSO;
}


function buscaUrlProcesso($codigoIdProcesso){

    $queryUrl  = ' SELECT DESTINO_PROCESSO FROM CFGPROCESSOS_PD '; 
    $queryUrl .= ' INNER JOIN CFGDISPAROPROCESSOSCABECALHO ON (CFGPROCESSOS_PD.NUMERO_REGISTRO = CFGDISPAROPROCESSOSCABECALHO.IDENTIFICACAO_PROCESSO) ';   
    $queryUrl .= ' WHERE CFGDISPAROPROCESSOSCABECALHO.NUMERO_REGISTRO_PROCESSO = ' . aspas($codigoIdProcesso);        

    $resultadoUrl = qryUmRegistro($queryUrl);

    $retornaUrl = explode('\services',getcwd());
    $urlProcesso = $retornaUrl[0] . '/' . $resultadoUrl->DESTINO_PROCESSO;

    return $urlProcesso;
}