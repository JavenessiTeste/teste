<?php

require('../lib/base.php');		
header("Content-Type: text/html; charset=ISO-8859-1",true);



    $numero_Autorizacao = isset($_GET['numero']) ? $_GET['numero'] : $_SESSION['numeroAutorizacao'];
	
	if($numero_Autorizacao==''){
		exit;
	}
	if(retornaValorCFG0003('CALCULA_COPART_AUT_WEB')=='SIM'){
		require_once('../EstruturaEspecifica/coparticipacaoDana.php');
		geraMensagemCoparticipacaoDana($numero_Autorizacao);
	}

    $buf = "select CfgEmpresa.Nome_Empresa, CfgEmpresa.Razao_Social, CfgEmpresa.Numero_Insc_Susep, "
            ."Ps6500.Tipo_Guia, Ps6500.Data_Autorizacao, Ps6500.Codigo_Associado, Ps6500.Codigo_Prestador, Ps6500.Codigo_Prestador_Executante, Ps6500.Codigo_Cid, "
            ."Ps6500.Numero_Autorizacao, Ps1000.Nome_Associado, Ps5000.Nome_Prestador, Ps5000.Codigo_CNES,Ps1000.Codigo_Cns, Ps1000.Data_Validade_Carteirinha, Ps5000.Tipo_Pessoa, "
            ."Ps6500.Tipo_Doenca, Ps6500.Tempo_Doenca, Ps6500.INDICADOR_ACIDENTE, Ps6500.Unidade_Tempo_Doenca, pS6500.Data_Validade, pS6500.PROCEDIMENTO_PRINCIPAL, "
            ."Ps1030.Nome_Plano_Familiares, Ps6500.Numero_Guia, Ps6500.Numero_guia_operadora, PS6500.TIPO_CONSULTA, ps6500.FLAG_ATENDIMENTO_RN, "
            ."Ps5002.CODIGO_CONSELHO_PROFISS, Ps5002.UF_CONSELHO_PROFISS, Ps5002.NUMERO_CRM, Ps5002.Numero_Cpf, Ps5002.Numero_Cnpj, "

            // DADOS DO PRESTADOR EXECUTANTE
            ."Ps5002_1.CODIGO_CONSELHO_PROFISS AS CODIGO_CONSELHO_PROFISS_1, "
            ."Ps5002_1.UF_CONSELHO_PROFISS AS UF_CONSELHO_PROFISS_1, "
            ."Ps5002_1.NUMERO_CRM AS NUMERO_CRM_1, "
            ."Ps5002_1.Numero_Cpf AS Numero_Cpf_1, "
            ."Ps5002_1.Numero_Cnpj AS Numero_Cnpj_1, "


            ."Ps5001.ENDERECO, Ps5001.CIDADE, Ps5001.estado, Ps5001.CEP, Ps5001.TELEFONE_01, "
            ."(SELECT FIRST 1 Ps5001_1.ENDERECO "
            ."FROM Ps5001 Ps5001_1 "
            ."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS ENDERECO, "
            ."(SELECT FIRST 1 Ps5001_1.CIDADE "
            ."FROM Ps5001 Ps5001_1 "
            ."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS CIDADE, "
            ."(SELECT FIRST 1 Ps5001_1.ESTADO "
            ."FROM Ps5001 Ps5001_1 "
            ."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS ESTADO, "
            ."(SELECT FIRST 1 Ps5001_1.CEP "
            ."FROM Ps5001 Ps5001_1 "
            ."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS CEP, "
            ."(SELECT FIRST 1 Ps5001_1.TELEFONE_01 "
            ."FROM Ps5001 Ps5001_1 "
            ."WHERE Ps5001_1.codigo_prestador = Ps6500.codigo_prestador AND Ps5001_1.flag_ha_atendimento = 'S') AS TELEFONE_01, "
            // Nome do prestador executante
            ."(SELECT Ps5000.Nome_Prestador "
            ."FROM Ps5000 "
            ."WHERE Ps5000.codigo_prestador = Ps6500.codigo_prestador_executante) AS Nome_Prestador_Executante, "
            
			."Ps5100.Codigo_Terminologia_Cbo, Ps5100.Nome_Especialidade 	"
			
            ."From Ps6500 "
            ."Inner Join Ps1000 On (Ps6500.codigo_Associado = Ps1000.Codigo_Associado) "
            ."Inner Join Ps5000 On (Ps6500.codigo_Prestador = Ps5000.Codigo_Prestador) "
            ."Left Outer Join Ps5002 On (Ps5000.Codigo_Prestador = Ps5002.Codigo_Prestador) "
            ."Left Outer Join Ps5002 Ps5002_1 On (Ps6500.Codigo_Prestador_Executante = Ps5002_1.Codigo_Prestador) "
            ."Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) "
			
			."Left Outer Join Ps5100 On (Ps6500.Codigo_Especialidade = Ps5100.Codigo_Especialidade) "
            
			."Inner Join CfgEmpresa On (Nome_Empresa is not null) "
            ."Left join ps5001 on (Ps5001.Codigo_Prestador = Ps6500.Codigo_Prestador and ps5001.flag_ha_atendimento = 'S' "
            ."						and ((Ps6500.Registro_Endereco_Prestador is null) or (Ps6500.Registro_Endereco_Prestador = Ps5001.numero_registro_endereco))) "            
            ." Where Ps6500.Numero_Autorizacao = ". aspas($numero_Autorizacao);
			
			if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
				$buf .= " And Ps6500.Codigo_Associado = " . aspas($_SESSION['codigoIdentificacao']);
			}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){
				$buf .= " And Ps6500.Codigo_Prestador = " . aspas($_SESSION['codigoIdentificacao']);
			}elseif(retornaValorConfiguracao('VALIDA_PARAMETRO_VW_GUIAS') == 'SIM'){
				$buf .= " And Ps6500.Codigo_Associado = " . aspas($_GET['cod']);
			}

    //pr($buf, true);
$res=jn_query($buf);

$row=jn_fetch_assoc($res);

	// Este campo pode ser o codigo do prestador na operadora, ou seu cnpj ou seu CPF
	if($row['NUMERO_CNPJ'] <> ''){
	   $codigoPrestador = $row['NUMERO_CNPJ'];
	}
	else {
	   $codigoPrestador = $row['NUMERO_CPF'];
	}

    $numeroGuia                     = $row['NUMERO_GUIA'];
    $numeroGuiaOperadora            = $row['NUMERO_GUIA_OPERADORA'];
	$tipoConsulta                   = $row['TIPO_CONSULTA'];
	$indicadorAcedidente            = $row['INDICADOR_ACIDENTE'];
	$AtendimentoRn            		= $row['FLAG_ATENDIMENTO_RN'];
	$registroAns                    = $row['NUMERO_INSC_SUSEP'];
    $dataEmissao                    = date("d/m/Y",strtotime($row['DATA_AUTORIZACAO']));
    $numeroCarteira                 = $row['CODIGO_ASSOCIADO'];
    $planoBeneficiario              = $row['NOME_PLANO_FAMILIARES'];
    $nomeBeneficiario               = $row['NOME_ASSOCIADO'];
    $codigoPrestadorExecutante      = $row['CODIGO_PRESTADOR_EXECUTANTE'];
    $nomePrestador                  = $row['NOME_PRESTADOR'];
    $nomePrestadorExecutante        = $row['NOME_PRESTADOR_EXECUTANTE'];
    $conselhoProfissional           = $row['CODIGO_CONSELHO_PROFISS'];
    $conselhoProfissionalExec       = $row['CODIGO_CONSELHO_PROFISS_1'];
    $numeroConselhoProfissional     =  $row['NUMERO_CRM'];
    $numeroConselhoProfissionalExec = $row['NUMERO_CRM_1'];
    $ufConselhoProfissional         = $row['UF_CONSELHO_PROFISS'];
    $ufConselhoProfissionalExec     = $row['UF_CONSELHO_PROFISS_1'];
    $codigoProcedimento             = "00010014";

    if(retornaValorConfiguracao('OCULTAR_VAL_CARTEIRINHA_GUIAS') == 'SIM'){
        $DataValidadeCarteirinha = '';
    }else{ 
        $DataValidadeCarteirinha      = SqlToData($row->DATA_VALIDADE_CARTEIRINHA);
    }
     
    $CodigoCid                      = $row['CODIGO_CID'];
    $CodigoCNES                     = $row['CODIGO_CNES'];
    $CodigoCNS                      = $row['CODIGO_CNS'];
	
	$CodigoCBO                      = $row['CODIGO_TERMINOLOGIA_CBO'];
	$NomeEspecialidade              = $row['NOME_ESPECIALIDADE'];
		
    $autorizacao                    = $row['NUMERO_AUTORIZACAO'];
    $Endereco                       = $row['ENDERECO'];
    $Cidade                         = $row['CIDADE'];
    $Estado                         = $row['ESTADO'];
    $Cep                            = $row['CEP'];
    $Telefone_01                    = $row['TELEFONE_01'];

    $TipoDoenca                     = $row['TIPO_DOENCA'];
    $TempoDoenca                    = $row['TEMPO_DOENCA'];
    $UnidadeTempoDoenca             = $row['UNIDADE_TEMPO_DOENCA'];
    $DataAutorizacao                = SqlToData($row['DATA_AUTORIZACAO']);
    $DataValidade                   = $row['DATA_VALIDADE'];
    $ProcedimentoPrincipal          = $row['PROCEDIMENTO_PRINCIPAL'];
    $codigoPrestEnd          		= $row['CODIGO_PRESTADOR'];

	$numeroGuia = validaGuiaExistente($numeroGuia,'C'); 
		
//	if($tipoConsulta != ''){		
//		if($tipoConsulta == '1'){
//			$tipoConsulta = 'PRIMEIRA CONSULTA';
//		}else if($tipoConsulta == '2'){
//			$tipoConsulta = 'RETORNO';
//		}else if($tipoConsulta == '3'){
//			$tipoConsulta = 'PRE NATAL';
//		}else if($tipoConsulta == '4'){
//			$tipoConsulta = 'POR ENCAMINHAMENTO';
//		}
//	}




//Imagem e posições//
		
$imagem = imagecreatefrompng("img/consulta.png");
$logo   = imagecreatefrompng('../../Site/assets/img/logo_operadora.png');
list($width, $height) = getimagesize('../../Site/assets/img/logo_operadora.png');
$logoRedimensionada = imagecreatetruecolor(500, 250);
$color = imagecolorallocate($logoRedimensionada, 255, 255, 255);
imagefill($logoRedimensionada, 0, 0, $color);
imagecopyresampled($logoRedimensionada, $logo, 0, 0, 0, 0, 500, 250, $width, $height);	

	
$cor = imagecolorallocate($imagem, 0, 0, 0 );
imagecopymerge($imagem, $logoRedimensionada,34, 16, 0, 0, 500, 250, 100);
		
$normal  = "../../Site/assets/fonts/unispace rg.ttf";
		
		
//calculo posição
//100,100
// fica
//119,89		
		
$tamanhoLetra = 25;


textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1864, 131, $cor,$normal,$numeroGuiaPrestador,5);		
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 100, 400, $cor,$normal,$registroAns,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 587, 400, $cor,$normal,$autorizacao,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 97, 603, $cor,$normal, $numeroCarteira,43);
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 1488, 599, $cor,$normal, $DataValidadeCarteirinha,43,35);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2376, 606, $cor,$normal, $AtendimentoRn,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 77, 749, $cor,$normal,$nomeBeneficiario,5);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1968, 755, $cor,$normal, $CodigoCNS,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 81, 950, $cor,$normal, $codigoPrestador,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1015, 937, $cor,$normal, $nomePrestador,5);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2463, 949, $cor,$normal, $CodigoCNES,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 65, 1097, $cor,$normal, $nomePrestadorExecutante,5);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1294, 1119, $cor,$normal, $conselhoProfissionalExec,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1509, 1108, $cor,$normal, $numeroConselhoProfissionalExec,37);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2419, 1093, $cor,$normal, $ufConselhoProfissionalExec,40);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2610, 1101, $cor,$normal, $CodigoCBO,30);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 458, 1319, $cor,$normal, $indicadorAcedidente,30);
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 84, 1495, $cor,$normal, $DataAutorizacao,43,35);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 84, 1950, $cor,$normal, 'Senha: '. $autorizacao,43,35);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 859, 1500, $cor,$normal, $tipoConsulta,30);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1636, 1493, $cor,$normal, $ProcedimentoPrincipal,43);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 64, 1696, $cor,$normal, '',5);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 64, 1770, $cor,$normal, '',5);
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 64, 1844, $cor,$normal, '',5);


ob_start();
imagejpeg ( $imagem); 
imagedestroy( $imagem ); 
$dadoImagem = ob_get_clean();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body width="1070px" >

<?php
echo '<img width="1060px"  src="data:image/jpeg;base64,' . base64_encode( $dadoImagem ) . '" />';
?>
</body>
</html>