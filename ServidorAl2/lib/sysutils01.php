<?php
require_once('../lib/class.phpmailer.php');
require_once('../lib/class.smtp.php');
require_once('../lib/PHPMailerAutoload.php');



function completaString($string,$tamanho,$caracterCompletar = ' '){

    //return str_pad($string, $tamanho, $caracterCompletar, STR_PAD_RIGHT);  

    return $string . str_repeat($caracterCompletar, $tamanho - strlen($string));
}


function adicionaCampoLinhaLog($string,$tamanhoCampo){

    if ($tamanhoCampo>=10000)
        $tamanhoCampo = 10000;

    $string = substr($string,0,$tamanhoCampo);

    return $string . str_repeat(' ', $tamanhoCampo - strlen($string));

}



function escreveArquivoLogProcesso($tipo, $informacao){

    global $arquivoLogProcesso;

    if ($tipo=='C') // Criar um novo arquivo
    {
        $query = 'SELECT NOME_PROCESSO, DATA_DISPARO, HORA_DISPARO FROM CFGDISPAROPROCESSOSCABECALHO 
                  WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($informacao);

        $res   = jn_query($query);
        $row   = jn_fetch_object($res);

        $nomeArquivoGerado        = retornaValorConfiguracao('PD_DIR_LOG_PROCESSOS') . 
                                    'LogProcesso_ID_' . $informacao . '_PR_' . $row->NOME_PROCESSO . '.txt';

        $nomeCaminhoArquivo       = retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . 
                                    $nomeArquivoGerado;

        criaDiretorioSeNaoExistir(retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . retornaValorConfiguracao('PD_DIR_LOG_PROCESSOS'));


        //$nomeCaminhoArquivo = '../../ArquivosGerados/LogProcessos/testeLog.txt';


        $arquivoLogProcesso = fopen($nomeCaminhoArquivo, 'w');
        return $nomeArquivoGerado;
    }


    if ($tipo=='L') // Escrever uma linha
    {
        fwrite($arquivoLogProcesso, $informacao . PHP_EOL);
    }


    if ($tipo=='F') // Fechar o arquivo
    {
        fclose($arquivoLogProcesso);
    }


}




function geraRelatorioAutomaticoProcessamento($idProcesso,$queryRelatorioProcessamento, $forcarArquivoTexto = 'N')
{

    if ($forcarArquivoTexto=='N')
    {
        $rowDadosProcesso       = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($idProcesso));
        $numeroRegistroProcesso = $rowDadosProcesso->IDENTIFICACAO_PROCESSO;

        require_once('../ProcessoDinamico/geradorRelatorios.php');

        return executaRelatorio('HTML',$queryRelatorioProcessamento,$numeroRegistroProcesso, $_POST['ID_PROCESSO'],'S');
    }
    else // então é para gerar arquivo texto
    {

        $row = qryUmRegistro('SELECT NOME_PROCESSO, DATA_DISPARO, HORA_DISPARO FROM CFGDISPAROPROCESSOSCABECALHO 
                            WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($idProcesso));

        $nomeArquivoGerado  = retornaValorConfiguracao('PD_DIR_RELATORIO_PROCESSOS') . 
                            'RelatorioAutomatico_ID_' . $idProcesso . '_PR_' . $row->NOME_PROCESSO . '.txt';

        $nomeCaminhoArquivo = retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . 
                            $nomeArquivoGerado;

        criaDiretorioSeNaoExistir(retornaValorConfiguracao('PD_DIR_PADRAO_SALVAR_ARQ') . retornaValorConfiguracao('PD_DIR_RELATORIO_PROCESSOS'));

        $arquivoLogProcesso = fopen($nomeCaminhoArquivo, 'w');

        $res   = jn_query($queryRelatorioProcessamento);
        
        /* ---------------------------------------------------------------------------------------- */
        /* GERA CABECALHO DO ARQUIVO DO RELATORIO                                                   */
        /* ---------------------------------------------------------------------------------------- */

        $tamanhoCampo     = 40;
        $qtCamposConsulta = jn_num_fields($res);
        $informacaoCampos = array();

        for ($j = 0; $j < $qtCamposConsulta; $j++) 
        {

            $nomeCampo                              = strToUpper(jn_field_metadata($res,$j)['Name']);
            $informacaoCampos['NOME_CAMPO'][]       = $nomeCampo;
            $informacaoCampos['TIPO_CAMPO'][]       = retornaTipoCampoMetadataBanco($res,$j);

            $tamanhoCampo = retornaTamanhoCampoMetadataBanco($res,$j);

            if ($tamanhoCampo < strlen($nomeCampo))
            {
                $tamanhoCampo = strlen($nomeCampo) + 2;
            }

            $informacaoCampos['TAMANHO_CAMPO'][]    = $tamanhoCampo;

            $linhaRelatorio .= adicionaCampoLinhaLog($nomeCampo,$tamanhoCampo);
        }

        fwrite($arquivoLogProcesso, $linhaRelatorio . PHP_EOL);


        /* ---------------------------------------------------------------------------------------- */
        /* GERA DADOS DOS REGISTROS DO RELATORIO                                                    */
        /* ---------------------------------------------------------------------------------------- */

        while ($row = jn_fetch_object($res))
        {

            $linhaRelatorio  = '';

            for ($j = 0; $j < $qtCamposConsulta; $j++) 
            {
                $nomeCampo       = $informacaoCampos['NOME_CAMPO'][$j];

                if ($informacaoCampos['TIPO_CAMPO'][$j]=='DATE')
                    $linhaRelatorio .= adicionaCampoLinhaLog(sqlToData($row->$nomeCampo),$informacaoCampos['TAMANHO_CAMPO'][$j]);
                else
                    $linhaRelatorio .= adicionaCampoLinhaLog($row->$nomeCampo,$informacaoCampos['TAMANHO_CAMPO'][$j]);
            }

            fwrite($arquivoLogProcesso,$linhaRelatorio . PHP_EOL);

        }        

        fclose($arquivoLogProcesso);

        return $nomeArquivoGerado;
    }

}



function retornaTipoCampoMetadataBanco($res, $indice)
{
    if($_SESSION['type_db'] == 'firebird')
    {
        // Tem que implementar os tipos do Firebird
        $type = jn_field_metadata($res,$indice)['type']; // no Firebird é em minusculo
    }
    else
    {
        $type = jn_field_metadata($res,$indice)['Type'];

        if ($type=='12')
            return 'STRING';
        else if ($type=='3')
            return 'NUMERIC';
        else if ($type=='93')
            return 'DATE';
        else
            return 'STRING';
        
    }
}


function retornaTamanhoCampoMetadataBanco($res, $indice)
{
    if($_SESSION['type_db'] == 'firebird')
    {
        $tamanho = jn_field_metadata($res,$indice)['length']; // no Firebird é em minusculo
    }
    else
    {
        $tamanho = jn_field_metadata($res,$indice)['Size'];

        if (($tamanho=='') or ($tamanho==0))
        {
            if (retornaTipoCampoMetadataBanco($res, $indice)=='DATE')
                $tamanho = 10;
            else 
                $tamanho = 12;
        }

        $tamanho+=2;
    }

    return $tamanho;
}




function iif($tst,$cmp,$bad) 
{
    return (($tst == $cmp)?$cmp:$bad);
}



function substrDelphi($string,$inicial,$tamanho)
{
    return substr($string, $inicial-1, $tamanho);
}



function stringReplace_Delphi($string, $valorProcurar, $valorSubstituir, $transformarUpper = 'S')
{

    if ($transformarUpper=='S')
        return str_replace(strToUpper($valorProcurar), $valorSubstituir,$string);
    else
        return str_replace($valorProcurar, $valorSubstituir,$string);

}



function stringReplace_Delphi_All($string, $valorProcurar, $valorSubstituir)
{
    $valor = $string;
    $iteracoes=0;

    while (1 == 1)
    {
        $valor    = str_replace(strToUpper($valorProcurar), $valorSubstituir,$valor);
        $iteracoes++;

        if (($valor == $string) or
            ($iteracoes >= 50))
            break;
    }

    return $valor;

}



function strposDelphi($stringProcurar, $stringTotal)
{

    if (($stringProcurar=='') or ($stringTotal==''))
    {
        return -1;
    }    

    $strPos = strpos($stringTotal,$stringProcurar);

    if ($strPos === false)
        $strPos = -1;

    return $strPos;

}



function testaData($data)
{
    return $data;
}



function day($dataReferencia)
{
    $dataReferencia = SqlToData($dataReferencia);
    $dataReferencia = date_parse_from_format("d/m/Y", $dataReferencia); 
    $dia            = str_pad($dataReferencia['day'], 2, 0, STR_PAD_LEFT);

    return $dia;
}



function month($dataReferencia)
{
    $dataReferencia = SqlToData($dataReferencia);
    $dataReferencia = date_parse_from_format("d/m/Y", $dataReferencia); 
    $mes            = str_pad($dataReferencia['month'], 2, 0, STR_PAD_LEFT);

    return $mes;
}




function year($dataReferencia)
{

    $dataReferencia = SqlToData($dataReferencia);

    $dataReferencia = date_parse_from_format("d/m/Y", $dataReferencia); 

    $ano            = $dataReferencia['year'];

    return $ano;
}




function extraiMesAnoData($data)
{

    return month($data) . '/' . year($data);

}





function retornaProximoMesAno($mesAno)
{

    $data    = getMontaDataDMY('01/' . $mesAno);
    $data    = date("Y-m-d", strtotime("+1 month", $data));

    return extraiMesAnoData($data);

}


// Esta função serve para pegar a data de hoje para passar para uma consulta sql a partir do DataToSql, exemplo: DataToSql(dataHoje_Date())
function dataHoje_Date(){

    return date('d/m/Y');

}



function dataHoje(){

    return getObjetoDate();
    
}


function getObjetoDate(){

    return new DateTime(date('d-m-Y'));

}


// Tem que passar a data no formato YYYY/MM/DD
function getMontaData($data){

    return new DateTime($data);

}



function getMontaDataDMY($data)
{

    $data  = substr($data, 6, 4) . '/' . substr($data, 3, 2) . '/' . substr($data, 0, 2);

    return new DateTime($data);
}


function retornaMesAno($data){

    $mesAnoReferencia = month($data) . '/' . year($data);

    return $mesAnoReferencia;

}


function horaAtual(){

    return date('H:i');

}



function retornaDataHoraString()
{
    $string = date("Y-m-d H:i:s");
    $string = eliminaMascaras($string);

    return $string;
}


function dataAngularToSql($value){
    
    global $formatoData;

    if ($formatoData=='AAAA-MM-DD')
    {
        $string = "";
        
        if(is_object($value))
        {
            $string = $value->format('d/m/Y');
        }
        else if ((strlen($value) == 10) or (strlen($value) == 19)) 
        {
            $string = substr($value, 8, 2) . '/' . substr($value, 5, 2) . '/' . substr($value, 0, 4); // dd-mm-aaaa
        } 
        else 
        {
            if($value=='')
                $string ='';
            else
                $string = 'Data invalida.';
        }
    }

    if (!ValidaData($string))
    {
        $string = SqlToData2($value);
    }

    $string = DataToSql($string);

    return $string;
    
}




function prDebug($string)
{
    if ($_GET['Teste']=='OK')
    {
       pr($string);
    }
}




function linhaJsonEspecial($tipo, $campo, $valor, $mascara = 'A', $caracterFinal = ',')
{

    if ($tipo=='SEM_ACENTO')
        $valor = retiraAcentos($valor);

    return linhaJsonEdicao($campo, $valor, $mascara = 'A', $caracterFinal = ',');

}


function linhaJsonEdicao($campo, $valor, $mascara = 'A', $caracterFinal = ',')
{

    if (($mascara=='IGVAZIO') and ($valor=='')) // IGNORAR SE VAZIO
    {
       return '';
    }

    if ($mascara=='D')
        $valor = DataToSql(sqlToData($valor));
    else if ($mascara=='N')
    {
        if ($valor=='')
            $valor = 0;
        else
        {   
            $valor = round($valor,2);
            $valor = numSql($valor);
        }
    }
    else if ($mascara=='ANULL')
    {
        $valor = aspasNull($valor);
    }
    else 
        $valor = aspas($valor);

    $linhaMontada  = '{"CAMPO":"' . $campo . '","VALOR":"' . $valor . '"}' . $caracterFinal;

    return $linhaMontada;

}



function gravaEdicao($tabela, $valores, $tipoGravacao, $criterioWhere = '',$nomeChaveRetornarInsert=false)
{

    /* $tabela          = nome da tabela a ser edita, exemplo: PS1000
       $valores         = Json de campos e valores pré-montado pela função linhaJsonEdicao
       $tipoGravacao    = A->Alteração direta (update) 
                          I->Inclusão direta (Insert)
                          V->Valida a existência do registro, se houver vai para Update, se não houver vai para Inclusão.
                          NA->Não alterar, ou seja, se já existir não faz nada
       $criterioWhere   = Critério where utilizado para localizar ou atualizar o registro.
    */

    if ($criterioWhere!='')
        $criterioWhere = ' Where ' . $criterioWhere;

    if (($tipoGravacao=='V') Or // Então é para primeiro validar a existência, se existir faz um update. Se não existir vai para insert.
        ($tipoGravacao=='NA'))  // Então é para primeiro validar a existência, se existir não altera e sai
    {
        $rowTmp = qryUmRegistro('Select first 1 ' . aspas('SIM') . ' EXISTE_REGISTRO from ' . $tabela . $criterioWhere);

        if ($rowTmp->EXISTE_REGISTRO=='SIM')
        {
            if ($tipoGravacao=='NA') // Não alterar, então ele simplesmente vai voltar...
            {
                return;
            }
            else
            {
                $tipoGravacao ='A';
            }
        }
        else
        {
            $tipoGravacao ='I';
        }
    }

    // Removo a ultima virgula caso exista
    if (copyDelphi($valores,strlen($valores),1)==',')
    {   
        $valores = copyDelphi($valores,1,strlen($valores)-1);
    }

    if ($_SESSION['NUMERO_PROTOCOLO_ATIVO']!='')
    {
        $tabela         = strtoupper($tabela);
        $campoProtocolo = registrarProtocoloPs6450($tabela);

        if ($campoProtocolo!='')
        {
           $valores = $valores . ',{"CAMPO":"' . $campoProtocolo . '","VALOR":"' . $_SESSION['NUMERO_PROTOCOLO_ATIVO'] . '"}';
        }
    }

    if ($_POST['ID_PROCESSO']!='')
    {
        $tabela = strtoupper($tabela);

        if ((strpos($tabela,'PS')!==false) or (strpos($tabela,'TEMP')!==false) or
            (strpos($tabela,'DW')!==false) or (strpos($tabela,'VND')!==false))
        {
           $valores = $valores . ',{"CAMPO":"ID_INSTANCIA_PROCESSO","VALOR":"' . $_POST['ID_PROCESSO'] . '"}';
        }
    }

    $valores     = '[' . $valores . ']';   

    // Aqui eu tiro os enters da string para não dar erro. 
    // Mas isto é provisório, precisamos achar uma solução efetiva

    $valores     = str_replace(PHP_EOL, ' ', $valores);;
    $valores     = str_replace(array("\n","\r"), ' ', $valores);

    $JsonValores = json_decode($valores);

    $sqlParte1   = '';
    $sqlParte2   = '';


    foreach ($JsonValores as $j)
    {
        if ($sqlParte1!='')
        {
            $sqlParte1 .= ',';
            $sqlParte2 .= ',';
        }

        if ($tipoGravacao=='I')
        {
            $sqlParte1 .= $j->CAMPO;
            $sqlParte2 .= $j->VALOR;

        }
        else if ($tipoGravacao=='A')
        {
            $sqlParte1 .= $j->CAMPO . '=' . $j->VALOR;
        }
    }

    //

    if ($tipoGravacao=='I')
    {
        $sqlFinal = 'INSERT INTO ' . $tabela . '(' . $sqlParte1 . ') values(' . $sqlParte2 . ')' ;
    }
    else if ($tipoGravacao=='A')
    {
        $sqlFinal = 'UPDATE ' . $tabela . ' SET ' . $sqlParte1 . $criterioWhere;
    }

    //

    if (($tipoGravacao=='I')and ($nomeChaveRetornarInsert!=false)){

        if( $_SESSION['type_db'] == 'firebird' ){
            $sqlFinal = $sqlFinal . ' returning '.$nomeChaveRetornarInsert;
        }

        $res = jn_query($sqlFinal);
        if($res){ 
            $retorno = array();
            $retorno['RES'] = true;
            if( $_SESSION['type_db'] == 'firebird' ){
                $dadosRetornoInsert = jn_fetch_object($res);
                $dadosRetornoInsert=(array) $dadosRetornoInsert;
                $retorno['ID']  = $dadosRetornoInsert[$nomeChaveRetornarInsert];
            }else{
                $retorno['ID']  = jn_insert_id();
            }
        }else{
           $retorno['RES'] = $res;
        }
        return $retorno;
    }else{
        return jn_query($sqlFinal);
    }

    

}



function trataQuerySqlServer($textoQuery)
{
 
   if ($_SESSION['type_db'] == 'firebird')
   {
      return $textoQuery;
   }

   $textoTratadoSql = strtoUpper($textoQuery);

   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'Extract(day from ','day(');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'Extract(month from ','month(');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'Extract(year from ','year(');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,' As Date) ',' As DateTime)');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,' from 1 ',', 1 ');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,' from 5 ',', 5 ');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,' from 7 ',', 7 ');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,' from 4 ',', 4 ');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,' for ',' , ');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'"',Chr(39));
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'[DUPLA]',Chr(34));
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'||','+');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'ps5800a','ps5800');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'ps5800b','ps5800');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'Case EXTRACT(WEEKDAY FROM Ps6010.Data_Marcacao) ',' Case DATEPART(dw,Ps6010.Data_Marcacao)-1 ');

   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'Select First','Select Top ');
   $textoTratadoSql = stringReplace_Delphi($textoTratadoSql,'Select  First','Select Top ');

   return $textoTratadoSql;

}



function calculaDiferencaDatas($data1, $data2)
{

    //$dateInterval = $data_inicio->diff($data_fim);
    //echo $dateInterval->days;

    $dateInterval = $data1->diff($data2);
    //echo $dateInterval->days;

    return $dateInterval->days;

}





function selecionaConfiguracoesLayout($criterio){

    $queryLayout = 'select CFGLAYOUTS_CAMPOS.NUMERO_REGISTRO, CFGLAYOUTS_CAMPOS.TIPO_REGISTRO, CFGLAYOUTS_CAMPOS.CODIGO_LAYOUT, 
                           CFGLAYOUTS_CAMPOS.NOME_CAMPO, CFGLAYOUTS_CAMPOS.POSICAO_INICIAL, CFGLAYOUTS_CAMPOS.TAMANHO_CAMPO, 
                           CFGLAYOUTS_CAMPOS.INFORMACAO_CAMPO, CFGLAYOUTS_CAMPOS.TIPO_CAMPO, CFGLAYOUTS_CAMPOS.FUNCAO_GERACAO, 
                           CFGLAYOUTS_CAMPOS.PARAMETROS_ADICIONAIS, CFGLAYOUTS_CAMPOS.MASCARA_CAMPO, CFGLAYOUTS_CAMPOS.TIPO_ESTRUTURA 
                           from CfgLayouts
                    inner join CfgLayouts_Campos on (CfgLayouts.codigo_Layout = CfgLayouts_Campos.Codigo_Layout) ' .
                    $criterio . 
                    ' AND CFGLAYOUTS.PADRAO_ARQUIVO LIKE ' . aspas(strZero($_POST['COMBO_TIPO_ARQUIVO'],3) . '%') .
                    ' order by TIPO_REGISTRO, POSICAO_INICIAL ';

    return jn_query($queryLayout);

}



function retornaCampoTratadoLayout($rowInformacaoLayout, $tabelaDadoPs1020, $valorTotalRegistros = 0, $quantidadeTitulos = 0, $rowAuxiliar){

    global $identificacaoRegistro; 
    global $rowPs7300; 

    $stringRetornar = '';

    /* ------------------------------------------------------------------------------- */
    /* PRIMEIRO GERA O DADO, SEJA LITERAL, DO BANCO DE DADOS OU POR MEIO DE UM CÁLCULO */
    /* ------------------------------------------------------------------------------- */


    if ((strpos($rowInformacaoLayout->TIPO_CAMPO,'JSON_LITERAL')!==false)||(strpos($rowInformacaoLayout->TIPO_CAMPO,'JSON_CAMPO_DB')!==false))
    {
       /* echo 'json' . $json;
        $json       = stringReplace_Delphi_All($json,'\'\'','"');
        echo 'jsonalterado' . $json;*/

        
        $jsonTemp = $rowInformacaoLayout->INFORMACAO_CAMPO;

        //echo 'json' . $jsonTemp;
        $jsonTemp   = stringReplace_Delphi_All($jsonTemp,'\'\'','"');
        //echo 'jsonalterado' . $jsonTemp;

        $json       = json_decode($jsonTemp);
        

        foreach ($json as $key => $value)
        {
            $campoValidar = $value->NOME_CAMPO_VALIDAR;

            if ($tabelaDadoPs1020->$campoValidar==$value->VALOR_VALIDAR)
            {
                $stringRetornar = $value->VALOR_RETORNAR;
            }
        }

        if (strpos($rowInformacaoLayout->TIPO_CAMPO,'JSON_CAMPO_DB')!==false)
        {
            $campo = explode('.',$stringRetornar);

            if ((strtoupper($campo[0])=='PS1020') or 
                (strtoupper($campo[0])=='VW_CABECALHO_NFSE'))
            {
               $nomeCampo      = $campo[1];
               $stringRetornar = $tabelaDadoPs1020->$nomeCampo;
            }
            else if (strtoupper($campo[0])=='PS7300')
            {
               $nomeCampo      = $campo[1];
               $stringRetornar = $rowAuxiliar->$nomeCampo;
            }
        }
    }
    else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'LITERAL')!==false)
    {
        $stringRetornar = $rowInformacaoLayout->INFORMACAO_CAMPO;
    }
    else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'CAMPO_DB')!==false)
    {

        $campo = explode('.',$rowInformacaoLayout->INFORMACAO_CAMPO);

        if ((strtoupper($campo[0])=='PS1020') or 
            (strtoupper($campo[0])=='VW_CABECALHO_NFSE'))        
        {
           $nomeCampo      = $campo[1];
           $stringRetornar = $tabelaDadoPs1020->$nomeCampo;
        }
        else if (strtoupper($campo[0])=='PS7300')
        {
           $nomeCampo      = $campo[1];
           $stringRetornar = $rowAuxiliar->$nomeCampo;
        }


        // Cálculo de modulos, neste caso passar o valor do Tipo_campo como "CAMPO_DB" E "..., MODULO_10, MODULO_11, MODULO_11_INVERTIDO..."
        if (strpos($rowInformacaoLayout->TIPO_CAMPO,'MODULO_10')!==false)
        {
           $stringRetornar = modulo_10($stringRetornar);
        }
        else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'MODULO_11')!==false)
        {
           $stringRetornar = modulo_11($stringRetornar);
        }
        else if (strpos($rowInformacaoLayout->TIPO_CAMPO,'MODULO_11_INVERTIDO')!==false)
        {
           $stringRetornar = modulo_11_invertido($stringRetornar);
        }
        
    }


    if (strpos($rowInformacaoLayout->INFORMACAO_CAMPO,'[DATE]')!==false)
    {
        $stringRetornar = date('d/m/Y');

        if (strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAAAA')!==false)
        {
            $stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 6, 4); 
        }
        else if ((strpos($rowInformacaoLayout->MASCARA_CAMPO,'AAAAMMDD')!==false) or 
                 (strpos($rowInformacaoLayout->MASCARA_CAMPO,'YYYYMMDD')!==false))
        {
            $stringRetornar = substr($stringRetornar, 6, 4) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 0, 2); 
        }
        else
        {
            $stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 8, 2); 
        }
        return $stringRetornar;
    }


    // -------------------------------------------------------------------- //


    if ((strpos($rowInformacaoLayout->FUNCAO_GERACAO,'VALOR_TOTAL_TITULOS')!==false) or 
       (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'VALOR_TOTAL_NOTAS_FISCAIS')!==false))
    {
        $stringRetornar = $valorTotalRegistros;
    }
    else if ((strpos($rowInformacaoLayout->FUNCAO_GERACAO,'QUANTIDADE_TOTAL_TITULOS')!==false) or 
             (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'QUANTIDADE_NOTAS_FISCAIS')!==false))
    {
        $stringRetornar = $quantidadeTitulos;
    }


    if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'JUROS_MORA_DIARIO')!==false)
    {
        $stringRetornar = retornaValorJurosMoraDiaria($tabelaDadoPs1020, 'MORA');
    }


    if ((strpos($rowInformacaoLayout->FUNCAO_GERACAO,'MASCARADATA')!==false)||
        (strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAA')!==false)||
        (strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAAAA')!==false))
    {
        $stringRetornar = sqlToData($stringRetornar);

        if (strpos($rowInformacaoLayout->MASCARA_CAMPO,'DDMMAAAA')!==false)
        {
            $stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 6, 4); 
        }
        else
        {
            $stringRetornar = substr($stringRetornar, 0, 2) . substr($stringRetornar, 3, 2) . substr($stringRetornar, 8, 2); 
        }
    }

    if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'IDENTIFICACAO_REGISTRO_STRZERO')!==false)
    {
        $stringRetornar = strZero($identificacaoRegistro,$rowInformacaoLayout->TAMANHO_CAMPO);
        $identificacaoRegistro++;
    }

    if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'ELIMINAMASCARAS')!==false)
    {
        $stringRetornar = remove_caracteres($stringRetornar);
    }


    if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'MASCARAVALOR')!==false)
    {
        $stringRetornar = str_replace('.', '', $stringRetornar);
        $stringRetornar = str_replace(',', '', $stringRetornar);
        $stringRetornar = strZero($stringRetornar,$rowInformacaoLayout->TAMANHO_CAMPO);
    }
    else if (strpos($rowInformacaoLayout->FUNCAO_GERACAO,'STRZERO')!==false) // Aqui é um Else if, pq nao posso chamar novamente o strzero se for mascara valor, pois a mascaravalor ja poe o strzero
    {
        $stringRetornar = strZero($stringRetornar,$rowInformacaoLayout->TAMANHO_CAMPO);
    }

    if (strlen($stringRetornar) < $rowInformacaoLayout->TAMANHO_CAMPO)
    {
        $stringRetornar = completaString($stringRetornar,$rowInformacaoLayout->TAMANHO_CAMPO);
    }
    else if (strlen($stringRetornar) > $rowInformacaoLayout->TAMANHO_CAMPO)
    {
        $stringRetornar = copyDelphi($stringRetornar,1,$rowInformacaoLayout->TAMANHO_CAMPO);
    }

    return $stringRetornar;

}



function criaDiretorioSeNaoExistir($caminhoDiretorio)
{

    if (!file_exists($caminhoDiretorio))
    {
        mkdir($caminhoDiretorio,0777, true);
    }

}


function mascaraNomeCampo($nomeCampo){

    $string = str_replace('-', ' ',$nomeCampo);
    $string = str_replace('_', ' ',$nomeCampo);
    $string = strToUpper(copyDelphi($string,1,1)) . strtolower(copyDelphi($string,2,40));

    return $string;

}


function registrarProtocoloPs6450($tabela)
{

   if (($tabela == 'PS1000') or 
       ($tabela == 'PS1063') or 
       ($tabela == 'PS1095') or 
       ($tabela == 'PS6010') or 
       ($tabela == 'PS6110') or 
       ($tabela == 'PS6120') or 
       ($tabela == 'PS6360') or 
       ($tabela == 'PS6451') or 
       ($tabela == 'PS6550') or 
       ($tabela == 'PS6500') or 
       ($tabela == 'TMP1000_NET') or 
       ($tabela == 'PS6400'))
        return 'PROTOCOLO_GERAL_PS6450';
   else if (($tabela == 'PS6450') or 
            ($tabela == 'PS6452')
           ($tabela == 'TMP1000_NET'))
        return 'NUMERO_PROTOCOLO_GERAL';
   else
        return '';

}






function iniciaTabelaVertical($colunas,$titulos)
{

    $string = '<table class="tabela">
                    <tr>';

    for ($j = 0; $j < $colunas; $j++) 
    {
        $string .= '<th class="tituloColuna">' . $titulos[$j] . '</th>';
    }

    $string .= '   </tr>';

    return $string;
                    
}



function retornaValoresEmTabelaVertical($qtCamposConsulta, $res, $row)
{

    $informacaoCampos = array();
    $corLinha = 'corImpar';

    for ($j = 0; $j < $qtCamposConsulta; $j++) 
    {

        $nomeCampo                              = strToUpper(jn_field_metadata($res,$j)['Name']);
        $informacaoCampos['NOME_CAMPO'][]       = $nomeCampo;
        $informacaoCampos['TIPO_CAMPO'][]       = retornaTipoCampoMetadataBanco($res,$j);

        $tamanhoCampo = retornaTamanhoCampoMetadataBanco($res,$j);

        if ($tamanhoCampo < strlen($nomeCampo))
        {
            $tamanhoCampo = strlen($nomeCampo) + 2;
        }

        $informacaoCampos['TAMANHO_CAMPO'][]    = $tamanhoCampo;
        $tamanhoRetornar                        = $informacaoCampos['TAMANHO_CAMPO'][$j];

        if (($tamanhoRetornar=='') or ($tamanhoRetornar==0))
        {
            $tamanhoRetornar = 25;
        }

        if ($informacaoCampos['TIPO_CAMPO'][$j]=='DATE')
            $valorCampo = adicionaCampoLinhaLog(sqlToData($row->$nomeCampo),$tamanhoRetornar);
        else
            $valorCampo = adicionaCampoLinhaLog(jn_utf8_encode_AscII($row->$nomeCampo),$tamanhoRetornar);

        if ($corLinha=='corPar')
            $corLinha = 'corImpar';
        else
            $corLinha = 'corPar';

        $campos .= '<tr class="alturaLinha ' . $corLinha . '"><td class="alturaLinha">' . mascaraNomeCampo($nomeCampo) . '</td><td class="alturaLinha">' . $valorCampo . '</td></tr>';
    }

    return $campos;

}




function finalizaTabelaVertical()
{

    return '</table>';

}




function montaTabelaHorizontalBaseadoNaQuery($query, $campoQuebra = '', &$totalizar = Array())
{

    $corLinha              = 'corImpar';
    $res                   = jn_query($query);
    $qtCamposConsulta      = jn_num_fields($res);
    $linha                 = Array();
    $retorno               = iniciaTabelaHorizontal($res, $qtCamposConsulta);
    $valorValidacao        = '';

    while ($row = jn_fetch_object($res))
    {

        if ($campoQuebra!='')
        {
            if (($valorValidacao!='') and ($valorValidacao!=$row->$campoQuebra))
            {
                $retorno .= '<tr class="linhaSeparadora"><td>&nbsp;</td></tr>';
            }
            $valorValidacao=$row->$campoQuebra;
        }

        $retorno .= retornaValoresEmTabelaHorizontal($qtCamposConsulta, $res, $row, $corLinha, $campoQuebra, $totalizar);

        if ($corLinha=='corPar')
            $corLinha = 'corImpar';
        else
            $corLinha = 'corPar';

    }

    $retorno .= finalizaTabelaHorizontal();

    $totais   = '';

    foreach ($totalizar as $key => $value)
    {
        if ($totais=='')
            $totais = '<br><b>Valores Totais:';

        $totais .= '<br>&nbsp;&nbsp;' . mascaraNomeCampo($key) . ' = ' . $value;
    }

    if ($totais!='')
        $totais .= '</b>';


    return $retorno . $totais;

}





function iniciaTabelaHorizontal($res, $qtCamposConsulta)
{

    $string = '<table class="tabela">
                    <tr>';

    for ($j = 0; $j < $qtCamposConsulta; $j++) 
    {
        $nomeCampo  = strToUpper(jn_field_metadata($res,$j)['Name']);
        $string     .= '<th class="tituloColuna">' . mascaraNomeCampo($nomeCampo) . '</th>';
    }

    $string .= '   </tr>';

    return $string;

}



function retornaValoresEmTabelaHorizontal($qtCamposConsulta, $res, $row, $corLinha, $campoQuebra = '', &$totalizar = Array())
{

    $informacaoCampos = array();

    if ($campoQuebra!='')
        $corLinha = '';

    $retorno = '<tr class="alturaLinha ' . $corLinha . '">';

    for ($j = 0; $j < $qtCamposConsulta; $j++) 
    {

        $nomeCampo                              = strToUpper(jn_field_metadata($res,$j)['Name']);
        $informacaoCampos['NOME_CAMPO'][]       = $nomeCampo;
        $informacaoCampos['TIPO_CAMPO'][]       = retornaTipoCampoMetadataBanco($res,$j);

        $tamanhoCampo = retornaTamanhoCampoMetadataBanco($res,$j);

        if ($tamanhoCampo < strlen($nomeCampo))
        {
            $tamanhoCampo = strlen($nomeCampo) + 2;
        }

        $informacaoCampos['TAMANHO_CAMPO'][]    = $tamanhoCampo;

        if ($informacaoCampos['TIPO_CAMPO'][$j]=='DATE')
            $valorCampo = adicionaCampoLinhaLog(sqlToData($row->$nomeCampo),$informacaoCampos['TAMANHO_CAMPO'][$j]);
        else
            $valorCampo = adicionaCampoLinhaLog(jn_utf8_encode_AscII($row->$nomeCampo),$informacaoCampos['TAMANHO_CAMPO'][$j]);

        $retorno .= '<td class="alturaLinha">' . $valorCampo . '</td>';

        //if ($totalizar!==null) 
        //{
            //if (array_key_exists($nomeCampo,$totalizar))
            if (isset($totalizar[$nomeCampo]))
            {
                $totalizar[$nomeCampo] += $row->$nomeCampo; 
            }
        //}

    }

    $retorno .= '</tr>';

    return $retorno;

}




function finalizaTabelaHorizontal()
{

    return '</table>';

}




function abreProtocoloAtendimentoPs6450($codigoCadastroContato)
{

    $numeroProtocoloGerado = strZero(sanitizeString(RetornaValorConfiguracao('Numero_Insc_Susep')),6) . 
                             strZero(year(dataHoje()),4) . 
                             strZero(month(dataHoje()),2) . 
                             strZero(day(dataHoje()),2) . 
                             strZero(jn_gerasequencial('PS6450'),6);

    $sqlEdicao   = '';
    $sqlEdicao  .= linhaJsonEdicao('Numero_Protocolo_Geral',$numeroProtocoloGerado);
    $sqlEdicao  .= linhaJsonEdicao('Status_Protocolo', 'EM ABERTO');
    $sqlEdicao  .= linhaJsonEdicao('Codigo_Cadastro_Contato', $codigoCadastroContato);
    $sqlEdicao  .= linhaJsonEdicao('Data_Abertura_Protocolo',dataHoje(),'D');
    $sqlEdicao  .= linhaJsonEdicao('Hora_Abertura_Protocolo', horaAtual());
    $sqlEdicao  .= linhaJsonEdicao('Codigo_Operador',$_SESSION['CODIGO_IDENTIFICACAO']);

    gravaEdicao('Ps6450', $sqlEdicao, 'I');

    $_SESSION['NUMERO_PROTOCOLO_ATIVO'] = $numeroProtocoloGerado;

    return $numeroProtocoloGerado;

}   




function concluiProtocoloAtendimentoPs6450($numeroProtocolo)
{

    $numeroProtocolo = sanitizeString($numeroProtocolo);

    $sqlEdicao   = '';
    $sqlEdicao  .= linhaJsonEdicao('Data_Fechamento_Protocolo',dataHoje(),'D');
    $sqlEdicao  .= linhaJsonEdicao('Hora_Fechamento_Protocolo', horaAtual());

    gravaEdicao('Ps6450', $sqlEdicao, 'A', 'NUMERO_PROTOCOLO_GERAL = ' . aspas($numeroProtocolo));

    $_SESSION['NUMERO_PROTOCOLO_ATIVO'] = '';

    return 'Ok protocolo número: ' . $numeroProtocolo . ' concluído com sucesso!';

}



function calculaIdade($dataValidacao,$dataNascimento)
{

    $idadeValidacao    = year($dataValidacao) - year($dataNascimento);

    if (month($dataValidacao) < month($dataNascimento))
       $idadeValidacao = $idadeValidacao - 1;

}



function formataMsgErroAviso($msg){

    return '<p>-' . $msg . '</p>';
}




?>