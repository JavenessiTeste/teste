<?php
require('../lib/base.php');		
header("Content-Type: text/html; charset=ISO-8859-1",true);

function getFaces($AFaces) {
    $_faces = '';
    foreach ((array) $AFaces as $face) {
        $_faces .=  substr($face['face_dente'], 0, 1);
    }
    if (empty($_faces)) {
        return '&nbsp;&nbsp;';
    } 
    return $_faces;
}

  $numero_plano_tratamento = isset($_GET['numero']) ? $_GET['numero'] : $_SESSION['numero_plano_tratamento'];
  
  if($numero_plano_tratamento==''){
	exit;
  }

  $buf = "SELECT Numero_Insc_Susep,Numero_Cnpj FROM CFGEMPRESA";

  $res=jn_query($buf);
  if($row=jn_fetch_object($res)){
      $NumeroInscSusep = $row->NUMERO_INSC_SUSEP;
	  $NumeroCNPJ = $row->NUMERO_CNPJ;
  }

  $buf = "SELECT Ps2500.Numero_Plano_Tratamento, PS2500.CODIGO_ASSOCIADO, PS2500.CODIGO_PRESTADOR, PS2500.DATA_CADASTRAMENTO,Ps2500.Tipo_Atendimento_Odonto,
			Ps1000.CODIGO_EMPRESA, PS1000.NOME_ASSOCIADO,
            Ps1000.Codigo_Plano, Ps1000.Data_Validade_Carteirinha, Ps1000.Codigo_CNS, T_Ps1000.Codigo_Titular, ps2500.data_real_termino,
            T_Ps1000.Nome_Associado as nome_associado_titular, Ps1010.Nome_empresa, Ps1010.Flag_PlanoFamiliar, Ps1010.Flag_Cad_Telefones_Func,
            Ps1030.Nome_plano_familiares, Ps5000.Nome_Prestador, Ps5000.Codigo_CNES, Ps5002.Numero_Crm, Ps5002.UF_Conselho_Profiss, Ps5002.Numero_Cpf, Ps5002.Numero_Cnpj from Ps2500
            inner join Ps1000 on(Ps2500.Codigo_Associado = Ps1000.Codigo_Associado)
            inner join Ps1000 as T_Ps1000 on(Ps1000.Codigo_Titular = T_Ps1000.Codigo_Associado)
            inner join Ps1010 on(Ps1000.Codigo_Empresa = Ps1010.Codigo_Empresa)
            inner join Ps1030 on(Ps1000.Codigo_Plano = Ps1030.Codigo_Plano)
            inner join Ps5000 on(Ps2500.Codigo_Prestador = Ps5000.Codigo_Prestador)
            left join Ps5002 on(Ps5002.Codigo_Prestador = Ps5000.Codigo_Prestador)
            WHERE Ps2500.numero_plano_tratamento = " . aspas($numero_plano_tratamento);


    //pr($buf, true);
    $res=jn_query($buf);

    $row=jn_fetch_object($res);

    $nomeMedico = '';

    if(retornaValorConfiguracao('APRESENTA_PROFI_EXEC') == 'SIM'){
      
        $queryMedico = " Select PS2500.Codigo_Profissional_Exec, Esp0002.Nome_Medico
                        FROM PS2500
                        LEFT OUTER JOIN ESP0002 ON (Ps2500.Codigo_Profissional_Exec = Esp0002.Codigo_Medico)
                        WHERE Ps2500.numero_plano_tratamento = " . aspas($numero_plano_tratamento);

        $resultadoMedico = qryUmRegistro($queryMedico);

        $nomeMedico = $resultadoMedico->NOME_MEDICO;
    }


    $numeroPlanoTratamento        = $row->NUMERO_PLANO_TRATAMENTO;

    $codigoAssociado              = $row->CODIGO_ASSOCIADO;
    $nomeBeneficiario             = $row->NOME_ASSOCIADO;
    $numeroCarteira               = $row->CODIGO_ASSOCIADO;
    $dataValidadeCarteirinha      = SqlToData2($row->DATA_VALIDADE_CARTEIRINHA);
    $dataCadastramento      	  = SqlToData2($row->DATA_CADASTRAMENTO);
    $codigoCNS                    = $row->CODIGO_CNS;

    $planoBeneficiario            = $row->NOME_PLANO_FAMILIARES;

    $codigoEmpresa                = $row->CODIGO_EMPRESA;
    $nomeEmpresa                  = $row->NOME_EMPRESA;
    $FlagPlanoFamiliar            = $row->FLAG_PLANOFAMILIAR;
    $FlagCadTelefonesFunc         = $row->FLAG_CAD_TELEFONES_FUNC;

    $nomePrestador                = $row->NOME_PRESTADOR;
    $codigoCNES                   = $row->CODIGO_CNES;
    $numeroCrm                    = $row->NUMERO_CRM;
    $ufConselhoProfiss            = $row->UF_CONSELHO_PROFISS;
    $prestadorCPF                 = $row->NUMERO_CPF;
    $prestadorCNPJ                = $row->NUMERO_CNPJ;
	$prestadorCPF				  = $row->NUMERO_CPF;
	$dataTermino	              = $row->DATA_REAL_TERMINO;	
    $codigoTitular                = $row->CODIGO_TITULAR;
    $nomeBeneficiarioTitular      = $row->NOME_ASSOCIADO_TITULAR;                
    $dataEmissaoGuia              = getDataAtual();
    $numeroGuia                   = $row->NUMERO_GUIA;
	$numeroSenhaAutoriz			  = $numero_plano_tratamento;
	$tipoAtendimento              = $row->TIPO_ATENDIMENTO_ODONTO;
    
    if($FlagPlanoFamiliar == 'S' && $FlagCadTelefonesFunc == 'S'){
        $buf = "select Ps1006.Codigo_Area, Ps1006.Numero_telefone from Ps1006 "
                ."Where Ps1006.Codigo_Associado = " . aspas($codigoAssociado);
    } else if($FlagPlanoFamiliar == 'N' && $FlagCadTelefonesFunc == 'S') {
        $buf = "select Ps1006.Codigo_Area, Ps1006.Numero_telefone from Ps1006 "
                ."Where Ps1006.Codigo_Associado = " . aspas($codigoAssociado);
    } else if($FlagPlanoFamiliar == 'N' && $FlagCadTelefonesFunc == 'N') {
        $buf = "select Ps1006.Codigo_Area, Ps1006.Numero_telefone from Ps1006 "
                ."Where Ps1006.Codigo_Empresa = " . aspas($codigoEmpresa);
    }

    $res=jn_query($buf);
    $row=jn_fetch_object($res);

    $codigoArea         = $row->CODIGO_AREA;
    $numeroTelefone     = $row->NUMERO_TELEFONE;

    
    $ArrProcs = array();

    $buf = "Select Ps2510.Numero_Registro, coalesce(Ps2510.valor_estimativa_custo, Ps2210.valor_estimativa_custo) as valor_estimativa_custo, Ps2510.Codigo_Procedimento, Ps2510.Situacao, Ps2510.Numero_Dente_Segmento, Ps2510.Quantidade_Procedimentos, Ps2510.Data_conclusao_procedimento, Ps2210.Nome_Procedimento, Ps2510.Data_Procedimento from ps2510
            inner join Ps2210 on(Ps2510.Codigo_Procedimento = Ps2210.Codigo_Procedimento)
            Where Ps2510.Data_Cancelamento IS NULL AND Ps2510.Numero_Plano_Tratamento = " . aspas($numeroPlanoTratamento);
/*
    $res=jn_query($buf);
    
    $i = 0;
	$valorTotal = 0;
    while($row = jn_fetch_assoc($res)) {
        $query = 'SELECT count(CODIGO_PROCEDIMENTO_PACOTE) 
                  FROM Ps2211
                  WHERE CODIGO_PROCEDIMENTO_PACOTE = \''. $row['CODIGO_PROCEDIMENTO'] .'\'
                  GROUP BY CODIGO_PROCEDIMENTO_PACOTE';
        $sepacote = jn_query($query);
        $sepacoteval = jn_fetch_row($sepacote);
        if(!$sepacoteval[0]){
            $ArrProcs[$i] = array(
                'numero_registro'				=> $row['NUMERO_REGISTRO'],
                'codigo_procedimento'			=> $row['CODIGO_PROCEDIMENTO'],
                'valor_estimativa_custo'		=> toMoeda($row['VALOR_ESTIMATIVA_CUSTO']),
                'nome_procedimento'				=> substr($row['NOME_PROCEDIMENTO'], 0, 35),
                //'nome_procedimento'			=> $row['NOME_PROCEDIMENTO'],
                'numero_dente_segmento'			=> $row['NUMERO_DENTE_SEGMENTO'],
                'situacao'						=> $row['SITUACAO'],
                'quantidade_procedimentos'		=> number_format($row['QUANTIDADE_PROCEDIMENTOS']),
                'faces'							=> array(),
                'data_conclusao_procedimento'   => SqlToData2($row['DATA_CONCLUSAO_PROCEDIMENTO'])
            );
            
			$valorTotal = $valorTotal + $row['VALOR_ESTIMATIVA_CUSTO'];

            $query  = 'Select Ps2511.Face_Dente FROM Ps2511 ';
            $query .= 'Where Numero_Registro_Ps2510 = ' . $ArrProcs[$i]['numero_registro'];

            $resFaces = jn_query($query);

            while ($rowFace = jn_fetch_object($resFaces)) {
                $ArrProcs[$i]['faces'][] = array(
                    'face_dente' => $rowFace->FACE_DENTE
                );
            }

            $i++;
        }
    }

    foreach ($ArrProcs as $proc) {
        foreach ($proc['faces'] as $face) {
             $face = explode("-", $face['face_dente']);
        }
    }
*/	

	$dataValidadeSenha = date('d/m/Y', strtotime($dataCadastramento. ' + 90 days'));

	// Este campo pode ser o codigo do prestador na operadora, ou seu cnpj ou seu CPF
	if($prestadorCNPJ <> ''){
	   $codPrestador = $prestadorCNPJ;
	}
	else {
	   $codPrestador = $prestadorCPF;
	}


//Imagem e posições//		
$imagem = imagecreatefrompng("img/gto.png");
$logo   = imagecreatefrompng('../../Site/assets/img/logo_operadora.png');
list($width, $height) = getimagesize('../../Site/assets/img/logo_operadora.png');
$logoRedimensionada = imagecreatetruecolor(500, 170);
$color = imagecolorallocate($logoRedimensionada, 255, 255, 255);
imagefill($logoRedimensionada, 0, 0, $color);
imagecopyresampled($logoRedimensionada, $logo, 0, 0, 0, 0, 500, 170, $width, $height);	
	
$cor = imagecolorallocate($imagem, 0, 0, 0 );
imagecopymerge($imagem, $logoRedimensionada,34, 16, 0, 0, 500, 170, 100);
		
$normal  = "../../Site/assets/fonts/unispace rg.ttf";			
		
$tamanhoLetra = 25;

//calculo posição
//100,100
// fica
//116,89
//+16,-11

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 45, 267, $cor,$normal, $NumeroInscSusep,31.5);                                  // 1
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 1362, 273, $cor,$normal, $dataCadastramento,28,20);			      // 4
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1815, 273, $cor,$normal, $numeroSenhaAutoriz,31.5);                             // 5
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2796, 273, $cor,$normal, $dataValidadeSenha,22,30);                             // 6
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 52, 434, $cor,$normal, $numeroCarteira,31.5);									  // 8
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1112, 434, $cor,$normal, $planoBeneficiario,5);   							  // 9
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2033, 434, $cor,$normal, $nomeEmpresa,5);   	    						      // 10
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 2981, 434, $cor,$normal, $dataValidadeCarteirinha,28,40);			          // 11
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3475, 434, $cor,$normal, $codigoCNS,26);   	    				  		  	  // 12
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 39, 545, $cor,$normal,$nomeBeneficiario,5);									  // 13
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1808, 545, $cor,$normal,trim($codigoArea),31.5);								  // 14
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1928, 545, $cor,$normal,$numeroTelefone,31.5);								  // 15
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2441, 545, $cor,$normal,$nomeBeneficiarioTitular,5);							  // 16
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 41, 727, $cor,$normal,$nomePrestador,5);						       	      	  // 17
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2835, 710, $cor,$normal,$numeroCrm,27);						       	      	  // 18
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3606, 710, $cor,$normal,$ufConselhoProfiss,31.5);						       	  // 19
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 61, 836, $cor,$normal,$codPrestador,31.5);			    			       	  // 21
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 818, 836, $cor,$normal,$nomePrestador,5);						       	      	  // 22
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2835, 833, $cor,$normal,$numeroCrm,27);						       	      	  // 23
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3621, 833, $cor,$normal,$ufConselhoProfiss,31.5);						       	  // 24
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3808, 832, $cor,$normal,$codigoCNES,31.5);                                      // 25

if($nomeMedico != ''){
    textoImagemEspacamento($imagem, $tamanhoLetra, 0, 38, 971, $cor,$normal,$nomeMedico,5);                                       // 26
}else{  
    textoImagemEspacamento($imagem, $tamanhoLetra, 0, 38, 971, $cor,$normal,$nomePrestador,5);                                    // 26                                                                                            // 26
}						       	      	  
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2835, 952, $cor,$normal,$numeroCrm,27);						       	      	  // 27
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 3621, 952, $cor,$normal,$ufConselhoProfiss,31.5);						       	  // 28
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 59, 2324, $cor,$normal, SqlToData($dataTermino),28,37);			      		  // 43
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 743, 2324, $cor,$normal,$tipoAtendimento,31.5);						       	  // 44
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 54, 2524, $cor,$normal, '',5);										          // 49 - justificativa
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 54, 2584, $cor,$normal, '',5);			          							  // 49	- justificativa

//procedimentos

$posicaoXCodProc   = 180; //nao incrementa
$posicaoYCodProc   = 1125; 
	
$posicaoXNomeProc  = 652; //nao incrementa
$prosicaoYNomeProc = 1125; 
	
$PosicaoXDente     = 1351; //nao incrementa
$PosicaoYDente     = 1125;
	
$PosicaoXFace     = 1540; //nao incrementa
$PosicaoYFace     = 1125;
	
$PosicaoXQuant     = 1793; //nao incrementa
$PosicaoYQuant     = 1125; 
	
$posicaoXValor     = 2236; //nao incrementa
$PosicaoYValor     = 1125;
	
$posicaoXData      = 3294; //nao incrementa
$posicaoYData      = 1125;


$res=jn_query($buf);

while($row=jn_fetch_object($res)) {
	$query = 'SELECT count(CODIGO_PROCEDIMENTO_PACOTE) 
                  FROM Ps2211
                  WHERE CODIGO_PROCEDIMENTO_PACOTE = \''. $row->CODIGO_PROCEDIMENTO .'\'
                  GROUP BY CODIGO_PROCEDIMENTO_PACOTE';
    $sepacote = jn_query($query);
    $sepacoteval = jn_fetch_row($sepacote);
	

	$query  = 'Select Ps2511.Face_Dente, Numero_Registro_Ps2510 FROM Ps2511 ';
	$query .= 'Where Numero_Registro_Ps2510 = ' . $row->NUMERO_REGISTRO;

	$resFaces = jn_query($query);

	$ArrProcs= Array();
	
	while ($rowFace = jn_fetch_object($resFaces)) {
		$ArrProcs[$i]['faces'][] = array(
			'Numero_Registro' => $rowFace->NUMERO_REGISTRO_PS2510,
			'face_dente' => $rowFace->FACE_DENTE
		);
	}            

	
	$faces = Array();
	
	foreach ($ArrProcs as $proc) {				
		foreach ($proc['faces'] as $face) {
			 $faces[] = explode("-", $face['face_dente']);
		}
	}
	
    if(!$sepacoteval[0]){
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodProc, $posicaoYCodProc, $cor,$normal, $row->CODIGO_PROCEDIMENTO,26.5);			      // 31		
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXNomeProc, $prosicaoYNomeProc, $cor,$normal, substr($row->NOME_PROCEDIMENTO,0,28),2);	  // 32		
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $PosicaoXDente, $PosicaoYDente, $cor,$normal, trim($row->NUMERO_DENTE_SEGMENTO),25);			  // 33	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $PosicaoXFace, $PosicaoYFace, $cor,$normal, $faces[0][0],30);			  // 34	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1590, $PosicaoYFace, $cor,$normal, $faces[1][0],30);			  // 34	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1633, $PosicaoYFace, $cor,$normal, $faces[2][0],30);			  // 34	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1680, $PosicaoYFace, $cor,$normal, $faces[3][0],30);			  // 34	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1720, $PosicaoYFace, $cor,$normal, $faces[4][0],30);			  // 34	
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $PosicaoXQuant, $PosicaoYQuant, $cor,$normal, $row->QUANTIDADE_PROCEDIMENTOS,31.5);			  // 35		
		textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXValor, $PosicaoYValor, $cor,$normal, $row->VALOR_ESTIMATIVA_CUSTO,31.5);			      // 37
		dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXData, $posicaoYData, $cor,$normal, SqlToData($row->DATA_PROCEDIMENTO),28,5);		  // 41
			
		$posicaoYCodProc   = $posicaoYCodProc + 55;	
		$prosicaoYNomeProc = $prosicaoYNomeProc + 55;
		$PosicaoYDente     = $PosicaoYDente + 55;
		$PosicaoYFace      = $PosicaoYFace + 55;
		$PosicaoYQuant     = $PosicaoYQuant + 55;
		$PosicaoYValor     = $PosicaoYValor + 55;
		$posicaoYData      = $posicaoYData + 55;
	}
}

ob_start();
imagejpeg ( $imagem); 
imagedestroy( $imagem ); 
$dadoImagem = ob_get_clean();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body width="1600px" height="917px">

<?php
echo '<img width="1590px" height="907px" src="data:image/jpeg;base64,' . base64_encode( $dadoImagem ) . '" />';
?>
</body>
</html>

