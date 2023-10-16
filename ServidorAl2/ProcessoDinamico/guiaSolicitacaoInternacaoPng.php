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


$buf = "select CFGEMPRESA.NOME_EMPRESA, CFGEMPRESA.RAZAO_SOCIAL, CFGEMPRESA.NUMERO_INSC_SUSEP, "
		."PS6500.QUANTIDADE_DIAS_INTERNACAO, PS6500.TIPO_DOENCA, PS6500.UNIDADE_TEMPO_DOENCA, PS6500.TIPO_GUIA, PS6500.DATA_AUTORIZACAO, PS6500.CODIGO_ASSOCIADO, PS6500.CODIGO_PRESTADOR, PS6500.CODIGO_PRESTADOR_EXECUTANTE, PS6500.CODIGO_CID, PS6500.CARATER_SOLICITACAO, PS6500.TIPO_INTERNACAO,"
		."PS6500.NUMERO_AUTORIZACAO, PS6500.INDICADOR_ACIDENTE, PS1000.NOME_ASSOCIADO, PS5000.NOME_PRESTADOR, PS5000.CODIGO_CNES,PS1000.CODIGO_CNS, PS1000.DATA_VALIDADE_CARTEIRINHA, PS5000.TIPO_PESSOA, "
		."PS6500.TIPO_DOENCA, PS6500.TEMPO_DOENCA, PS6500.UNIDADE_TEMPO_DOENCA, PS6500.DATA_VALIDADE, "
		."PS6500.DATA_PROCEDIMENTO, PS6500.CODIGO_TIPO_ACOMODACAO, PS6500.DESCRICAO_OBSERVACAO,"
		."PS1030.NOME_PLANO_FAMILIARES, PS6500.CODIGO_REGIME_INTERN, PS6500.NUMERO_GUIA, PS6500.CODIGO_ESPECIALIDADE, "
		."PS5002.CODIGO_CONSELHO_PROFISS, PS6500.NUMERO_GUIA_OPERADORA, PS6500.FLAG_ATENDIMENTO_RN, PS6500.FLAG_PREVISAO_OPME, PS6500.FLAG_PREVISAO_QUIMIOTERAPIA, PS5002.UF_CONSELHO_PROFISS, PS5002.NUMERO_CRM, PS5002.NUMERO_CPF, PS5002.NUMERO_CNPJ, "

		// DADOS DO PRESTADOR EXECUTANTE
		."PS5002_1.CODIGO_CONSELHO_PROFISS AS CODIGO_CONSELHO_PROFISS_1, "
		."PS5002_1.UF_CONSELHO_PROFISS AS UF_CONSELHO_PROFISS_1, "
		."PS5002_1.NUMERO_CRM AS NUMERO_CRM_1, "
		."PS5002_1.NUMERO_CPF AS NUMERO_CPF_1, "
		."PS5002_1.NUMERO_CNPJ AS NUMERO_CNPJ_1, "


		."PS5001.ENDERECO, PS5001.CIDADE, PS5001.ESTADO, PS5001.CEP, PS5001.TELEFONE_01, "
		."(SELECT FIRST 1 PS5001_1.ENDERECO "
		."FROM PS5001 PS5001_1 "
		."WHERE PS5001_1.CODIGO_PRESTADOR = PS6500.CODIGO_PRESTADOR AND PS5001_1.FLAG_HA_ATENDIMENTO = 'S') AS ENDERECO, "
		."(SELECT FIRST 1 PS5001_1.CIDADE "
		."FROM PS5001 PS5001_1 "
		."WHERE PS5001_1.CODIGO_PRESTADOR = PS6500.CODIGO_PRESTADOR AND PS5001_1.FLAG_HA_ATENDIMENTO = 'S') AS CIDADE, "
		."(SELECT FIRST 1 PS5001_1.ESTADO "
		."FROM PS5001 PS5001_1 "
		."WHERE PS5001_1.CODIGO_PRESTADOR = PS6500.CODIGO_PRESTADOR AND PS5001_1.FLAG_HA_ATENDIMENTO = 'S') AS ESTADO, "
		."(SELECT FIRST 1 PS5001_1.CEP "
		."FROM PS5001 PS5001_1 "
		."WHERE PS5001_1.CODIGO_PRESTADOR = PS6500.CODIGO_PRESTADOR AND PS5001_1.FLAG_HA_ATENDIMENTO = 'S') AS CEP, "
		."(SELECT FIRST 1 PS5001_1.TELEFONE_01 "
		."FROM PS5001 PS5001_1 "
		."WHERE PS5001_1.CODIGO_PRESTADOR = PS6500.CODIGO_PRESTADOR AND PS5001_1.FLAG_HA_ATENDIMENTO = 'S') AS TELEFONE_01, "
		// NOME DO PRESTADOR EXECUTANTE
		."(SELECT PS5000.NOME_PRESTADOR "
		."FROM Ps5000 "
		."WHERE Ps5000.codigo_prestador = Ps6500.codigo_prestador_executante) AS Nome_Prestador_Executante, "
		."PS5286.Nome_Profissional AS Profissional_Solicitante "
		."From Ps6500 "
		."Inner Join Ps1000 On (Ps6500.codigo_Associado = Ps1000.Codigo_Associado) "
		."Inner Join Ps5000 On (Ps6500.codigo_Prestador = Ps5000.Codigo_Prestador) "
		."Left Outer Join Ps5002 On (Ps5000.Codigo_Prestador = Ps5002.Codigo_Prestador) "
		."Left Outer Join Ps5002 Ps5002_1 On (Ps6500.Codigo_Prestador_Executante = Ps5002_1.Codigo_Prestador) "
		."Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) "
		."Inner Join CfgEmpresa On (Nome_Empresa is not null) "
		."Left join ps5001 on(Ps5001.Codigo_Prestador = Ps6500.Codigo_Prestador and ps5001.flag_ha_atendimento = 'S') "
		."Left join PS5286 on(Ps6500.Codigo_Profissional_Solic = PS5286.Codigo_Profissional) "
		." Where Ps6500.Numero_Autorizacao = ". aspas($numero_Autorizacao);

		if($_SESSION['perfilOperador'] == 'BENEFICIARIO'){
			$buf .= " And Ps6500.Codigo_Associado = " . aspas($_SESSION['codigoIdentificacao']);
		}elseif($_SESSION['perfilOperador'] == 'PRESTADOR'){
			$buf .= " And Ps6500.Codigo_Prestador = " . aspas($_SESSION['codigoIdentificacao']);
		}elseif(retornaValorConfiguracao('VALIDA_PARAMETRO_VW_GUIAS') == 'SIM'){
			$buf .= " And Ps6500.Codigo_Associado = " . aspas($_GET['cod']);
		}
//pr($buf,true);
$res=jn_query($buf);

$row=jn_fetch_object($res);

$numeroGuia                   = $row->NUMERO_GUIA;
$registroAns                  = $row->NUMERO_INSC_SUSEP;
$dataEmissao                  = SqlToData($row->DATA_AUTORIZACAO);
$numeroCarteira               = $row->CODIGO_ASSOCIADO;
$planoBeneficiario            = $row->NOME_PLANO_FAMILIARES;
$nomeBeneficiario             = $row->NOME_ASSOCIADO;
// Este campo pode ser o codigo do prestador na operadora, ou seu cnpj ou seu CPF
$codigoPrestador              = $row->NUMERO_CNPJ.$row->NUMERO_CPF;
$codigoPrestadorExecutante    = $row->NUMERO_CNPJ_1.$row->NUMERO_CPF_1;
$prestadorAlianca             = $row->CODIGO_PRESTADOR;
$CodigoPrestadorExecutanteAlianca = $row->CODIGO_PRESTADOR_EXECUTANTE;//codigo do prestador executante no sistema

$nomePrestador                = $row->NOME_PRESTADOR;
$nomePrestadorExecutante      = $row->NOME_PRESTADOR_EXECUTANTE;
$conselhoProfissional         = $row->CODIGO_CONSELHO_PROFISS;
$conselhoProfissionalExec     = $row->CODIGO_CONSELHO_PROFISS_1;
$atendimentoRn				  = $row->FLAG_ATENDIMENTO_RN;
$previsaoOpme     			  = $row->FLAG_PREVISAO_OPME;
$previsaoQuimioterapia	      = $row->FLAG_PREVISAO_QUIMIOTERAPIA;
$guiaOperadora			      = $row->NUMERO_GUIA_OPERADORA;
$numeroConselhoProfissional   = $row->NUMERO_CRM;
$numeroConselhoProfissionalExec   = $row->NUMERO_CRM_1;
$ufConselhoProfissional       = $row->UF_CONSELHO_PROFISS;
$ufConselhoProfissionalExec       = $row->UF_CONSELHO_PROFISS_1;

if(retornaValorConfiguracao('OCULTAR_VAL_CARTEIRINHA_GUIAS') == 'SIM'){
	$DataValidadeCarteirinha = '';
}else{ 
	$DataValidadeCarteirinha      = SqlToData($row->DATA_VALIDADE_CARTEIRINHA);
}
 
$CodigoCid                    = $row->CODIGO_CID;
$CodigoCNES                   = $row->CODIGO_CNES;
$CodigoCNS                   = $row->CODIGO_CNS;	
$autorizacao                  = $row->NUMERO_AUTORIZACAO;
$Endereco                     = $row->ENDERECO;	
$codigoEspecialidade          = $row->CODIGO_ESPECIALIDADE;
$Cidade                       = $row->CIDADE;
$Estado                       = $row->ESTADO;
$Cep                          = $row->CEP;
$Telefone_01                  = $row->TELEFONE_01;
$codigoRegimeIntern 		  = $row->CODIGO_REGIME_INTERN;
$TipoDoenca                   = $row->TIPO_DOENCA;
$TempoDoenca                  = $row->TEMPO_DOENCA;
$UnidadeTempoDoenca           = $row->UNIDADE_TEMPO_DOENCA;
$DataValidade                 = SqlToData($row->DATA_VALIDADE);
$DataProcedimento             = SqlToData($row->DATA_PROCEDIMENTO);
$CaraterSolicitacao           = $row->CARATER_SOLICITACAO;
$TipoInternacao               = $row->TIPO_INTERNACAO;
$TipoInternacao 			  = substr($TipoInternacao,0,1);
$QuantidadeDiasInternacao     = $row-> QUANTIDADE_DIAS_INTERNACAO;
$TipoDoenca                   = $row-> TIPO_DOENCA;
$UnidadeTempoDoenca           = $row-> UNIDADE_TEMPO_DOENCA;
$IndicadorAcidente            = $row-> INDICADOR_ACIDENTE;
$codigoTipoAcomodacao         = $row->CODIGO_TIPO_ACOMODACAO;
$DescricaoObservacao          = $row->DESCRICAO_OBSERVACAO;
$ProfissionalSolicitante      = $row->PROFISSIONAL_SOLICITANTE;

$bufProcedimentos = "SELECT PS6510.NUMERO_AUTORIZACAO, PS6510.CODIGO_PROCEDIMENTO, PS5210.NOME_PROCEDIMENTO FROM PS6510 "
					."Inner Join Ps5210 on(Ps6510.Codigo_Procedimento = Ps5210.Codigo_Procedimento) "
					."Where Ps6510.Numero_Autorizacao = ". aspas($numero_Autorizacao);

$res=jn_query($bufProcedimentos);

   /*
 *  pega o codigo tiss da tabela de procedimentos que esta no contrato do
 * prestador contratado
 */

$TabelaProc = ''; // por padrao vem vazio

if ($CodigoPrestadorExecutanteAlianca == ''){
	$prestadorSql = $prestadorAlianca;
}else{
	$prestadorSql = $CodigoPrestadorExecutanteAlianca;
}


$query  = 'SELECT ';
$query .= '   PS5211.CODIGO_NA_TISS ';
$query .= 'FROM ';
$query .= '    ps5211 ';
$query .= 'WHERE ';
$query .= '    PS5211.referencia_tabela = ( ';
$query .= '        select ';
$query .= '            PS5002.referencia_tabela_internacoes ';
$query .= '        FROM ';
$query .= '            ps5002 ';
$query .= '        WHERE ';
$query .= '            PS5002.codigo_prestador = ' . aspas($prestadorSql);
$query .= '    ) ';

 if ($res = jn_query($query)) {
	 if ($row = jn_fetch_row($res)) {
		$TabelaProc = $row[0];
	 }
 }


$buf = "SELECT PS6510.CODIGO_PROCEDIMENTO, PS5210.NOME_PROCEDIMENTO, PS6510.QUANTIDADE_PROCEDIMENTOS "
   ." FROM PS6510 "
   ." Inner Join Ps5210 On (Ps6510.codigo_Procedimento = Ps5210.Codigo_Procedimento) "
   ." Where Ps6510.Situacao = 'A' and Ps6510.Numero_Autorizacao = ". aspas($numero_Autorizacao);
//echo $buf;exit();

for ($i=0; $i<=4; $i++)
{
  $tabela[$i]                 = "";
  $codigoProcedimento[$i]     = "";
  $nomeProcedimento[$i]       = "";
  $quantidadeSolicitada[$i]   = "";
  $quantidadeAutorizada[$i]   = "";
}

$i= 0;

//Imagem e posições//
		
$imagem = imagecreatefrompng("img/solicitacaoInternacao.png");
$logo   = imagecreatefrompng('../../Site/assets/img/logo_operadora.png');
list($width, $height) = getimagesize('../../Site/assets/img/logo_operadora.png');
$logoRedimensionada = imagecreatetruecolor(500, 190);
$color = imagecolorallocate($logoRedimensionada, 255, 255, 255);
imagefill($logoRedimensionada, 0, 0, $color);
imagecopyresampled($logoRedimensionada, $logo, 0, 0, 0, 0, 500, 190, $width, $height);	

	
$cor = imagecolorallocate($imagem, 0, 0, 0 );
imagecopymerge($imagem, $logoRedimensionada,34, 16, 0, 0, 500, 190, 100);
		
$normal  = "../../Site/assets/fonts/unispace rg.ttf";
		
		
		
$tamanhoLetra = 25;

//calculo posição
//100,100
// fica
//119,89
//+19,-11

textoImagemEspacamento($imagem, $tamanhoLetra, 0, 74, 317, $cor,$normal,$registroAns,32); 						// 1
textoImagemEspacamento($imagem, 35, 0, 2310, 85, $cor,$normal,$numeroGuia,5);									// 2
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 454, 315, $cor,$normal,$guiaOperadora,32);					// 3
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 75, 452, $cor,$normal,$dataEmissao,32,30);					// 4
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 590, 452, $cor,$normal,$autorizacao,32);						// 5
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 1665, 452, $cor,$normal,$DataValidade,32,30);				// 6
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 77, 657, $cor,$normal,$numeroCarteira,32);					// 7
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 1140, 657, $cor,$normal,$DataValidadeCarteirinha,32,20);		// 8
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1761, 657, $cor,$normal,$atendimentoRn,32);					// 9
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 80, 785, $cor,$normal,$nomeBeneficiario,5);					// 10
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2298, 785, $cor,$normal,$CodigoCNS,32);						// 11							 					  		
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 76, 983, $cor,$normal,$codigoPrestador,32);					// 12														
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 847, 983, $cor,$normal,$nomePrestador,5);						// 13														
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 60, 1128, $cor,$normal,$ProfissionalSolicitante,5);			// 14																												
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1580, 1117, $cor,$normal,$conselhoProfissional,32);			// 15														
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1780, 1117, $cor,$normal,$numeroConselhoProfissional,32);		// 16														
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2604, 1117, $cor,$normal,$ufConselhoProfissional,32);			// 17
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2754, 1117, $cor,$normal,trim($codigoEspecialidade),32);		// 18
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 78, 1315, $cor,$normal,trim($codigoPrestadorExecutante),32);	// 19
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 830, 1315, $cor,$normal,trim($nomePrestadorExecutante),32);	// 20
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 283, 1451, $cor,$normal,trim($CaraterSolicitacao),32);		// 22
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 746, 1451, $cor,$normal,$TipoInternacao,32);					// 23
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1186, 1451, $cor,$normal,trim($codigoRegimeIntern),32);		// 24
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1629, 1451, $cor,$normal,trim($QuantidadeDiasInternacao),32);	// 25
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2193, 1451, $cor,$normal,$previsaoOpme,32);					// 26
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2754, 1451, $cor,$normal,$previsaoQuimioterapia,32);			// 27
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 206, 2228, $cor,$normal,$CodigoCid,32);						// 29
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2573, 2228, $cor,$normal,trim($IndicadorAcidente),32);		// 33
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 78, 3517, $cor,$normal,$DataProcedimento,32,30);				// 39
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 840, 3517, $cor,$normal,trim($QuantidadeDiasInternacao),32);	// 25
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 1380, 3517, $cor,$normal,trim($codigoTipoAcomodacao),32);	// 25
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2573, 2228, $cor,$normal,trim($codigoTipoAcomodacao),32);		// 41
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 77, 3651, $cor,$normal,trim($codigoPrestadorExecutante),32);	// 42
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 853, 3651, $cor,$normal,trim($nomePrestadorExecutante),32);	// 43
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 2709, 3651, $cor,$normal,$CodigoCNES,32);						// 44
textoImagemEspacamento($imagem, $tamanhoLetra, 0, 77, 3833, $cor,$normal,$DescricaoObservacao,12);			// 45
dataBrImagemEspacamento($imagem, $tamanhoLetra, 0, 78, 4165, $cor,$normal,$dataEmissao,32,30);					// 46




//procedimentos

$posicaoXTabela   = 138; //nao incrementa
$posicaoYTabela   = 2430; 
	
$posicaoXCodProc  = 285; //nao incrementa
$prosicaoYCodProc = 2430; 
	
$PosicaoXNomeProc = 820; //nao incrementa
$PosicaoYNomeProc = 2430;
	
$PosicaoXQuant    = 2655; //nao incrementa
$PosicaoYQuant    = 2430; 
	
$posicaoXQuant2   = 2894; //nao incrementa
$PosicaoYQuant2   = 2430;

$res=jn_query($buf);
while ($row=jn_fetch_object($res))
{

	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXTabela, $posicaoYTabela, $cor,$normal, trim($TabelaProc),32);			      	  	// 34		
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXCodProc, $prosicaoYCodProc, $cor,$normal, $row->CODIGO_PROCEDIMENTO,32);	  			// 35		
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $PosicaoXNomeProc, $PosicaoYNomeProc, $cor,$normal, substr($row->NOME_PROCEDIMENTO,0,75),5);	// 36	
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $PosicaoXQuant, $PosicaoYQuant, $cor,$normal, trim($row->QUANTIDADE_PROCEDIMENTOS),32);	  	// 37		
	textoImagemEspacamento($imagem, $tamanhoLetra, 0, $posicaoXQuant2, $PosicaoYQuant2, $cor,$normal, trim($row->QUANTIDADE_PROCEDIMENTOS),32); 	// 38
			
	$posicaoYTabela   = $posicaoYTabela + 80;	
	$prosicaoYCodProc = $prosicaoYCodProc + 80;
	$PosicaoYNomeProc = $PosicaoYNomeProc + 80;
	$PosicaoYQuant    = $PosicaoYQuant + 80;
	$PosicaoYQuant2   = $PosicaoYQuant2 + 80;
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
<body width="1080" height="1010" >

<?php
echo '<img width="1070px" height="1000px"  src="data:image/jpeg;base64,' . base64_encode( $dadoImagem ) . '" />';
?>
</body>
</html>