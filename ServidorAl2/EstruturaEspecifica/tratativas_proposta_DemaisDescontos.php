<?php
require('../lib/base.php');

$codAssociadoTmp = trim($_GET['codAssociado']);

$queryAssociado  = ' SELECT ';
$queryAssociado .= ' 	NOME_ASSOCIADO, CODIGO_TITULAR, NUMERO_CPF, NUMERO_RG, VND1000_ON.DATA_NASCIMENTO, DIA_VENCIMENTO, SEXO, NOME_MAE, CODIGO_PARENTESCO, CODIGO_VENDEDOR, DATA_ADMISSAO, VND1000_ON.PESO, VND1000_ON.ALTURA, CODIGO_CNS, VND1000_ON.CODIGO_TABELA_PRECO, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO, VND1001_ON.BAIRRO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1001_ON.NUMERO_TELEFONE_01, VND1001_ON.NUMERO_TELEFONE_02, ';
$queryAssociado .= ' 	VND1001_ON.ENDERECO_EMAIL, VND1001_ON.NUMERO_CONTRATO, PS1100.NOME_USUAL AS NOME_VENDEDOR, PS1030.CODIGO_PLANO, PS1030.NOME_PLANO_FAMILIARES, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NOME_CONTRATANTE, VND1000_ON.NOME_ASSOCIADO) AS NOME_CONTRATANTE, COALESCE(VND1001_ON.NUMERO_CPF_CONTRATANTE, VND1000_ON.NUMERO_CPF) AS CPF_CONTRATANTE, ';
$queryAssociado .= ' 	COALESCE(VND1001_ON.NUMERO_RG_CONTRATANTE, VND1000_ON.NUMERO_RG) AS RG_CONTRATANTE ';
$queryAssociado .= ' FROM VND1000_ON ';
$queryAssociado .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1030 ON (VND1000_ON.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
$queryAssociado .= ' LEFT OUTER JOIN PS1100 ON (VND1001_ON.CODIGO_VENDEDOR = PS1100.CODIGO_IDENTIFICACAO) ';
$queryAssociado .= ' WHERE TIPO_ASSOCIADO = "T" AND CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp);
$resAssociado = jn_query($queryAssociado);
if(!$rowAssociado = jn_fetch_object($resAssociado)){
	echo 'Titular n&atilde;o encontrado, favor verificar o c&oacute;digo enviado no par&acirc;metro.';
	exit;
}else{
	jn_query('DELETE FROM VND1002_ON WHERE CODIGO_ASSOCIADO = ' . aspas($codAssociadoTmp));
}

$listNascTit = list($endereco, $numerocomplemento) = explode(',',$rowAssociado->ENDERECO); 
$listNascTit = list($numero, $complemento) = explode('-', $numerocomplemento);
			  

$date = new DateTime($rowAssociado->DATA_NASCIMENTO);
$interval = $date->diff( new DateTime( date('Y-m-d') ) );
$idade = $interval->format('%Y');

$queryValores  = ' SELECT VALOR_PLANO FROM PS1032 ';
$queryValores .= ' WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
$queryValores .= ' AND IDADE_MINIMA <= ' . $idade;
$queryValores .= ' AND IDADE_MAXIMA >= ' . $idade;	
$queryValores .= ' AND CODIGO_TABELA_PRECO = ' . aspas($rowAssociado->CODIGO_TABELA_PRECO);
		
$resValores = jn_query($queryValores);
$rowValores = jn_fetch_object($resValores);
$valorTit = $rowValores->VALOR_PLANO;

$queryValorTotal =  ' SELECT SUM(VALOR_PLANO) AS SOMA_VALOR_PLANO FROM VND1000_ON';
$queryValorTotal .=  ' INNER JOIN PS1032 ON (VND1000_ON.CODIGO_PLANO = PS1032.CODIGO_PLANO AND VND1000_ON.CODIGO_TABELA_PRECO = PS1032.CODIGO_TABELA_PRECO)';
$queryValorTotal .= ' WHERE VND1000_ON.CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
$queryValorTotal .= ' AND VND1000_ON.CODIGO_TITULAR = ' .  aspas($rowAssociado->CODIGO_TITULAR);
$resValorTotal = jn_query($queryValorTotal);
$rowValorTotal = jn_fetch_object($resValorTotal);
$valorTitDep = $rowValorTotal->SOMA_VALOR_PLANO;

			
$queryModelo = 'SELECT * FROM VND1030CONFIG_ON WHERE CODIGO_PLANO = ' . aspas($rowAssociado->CODIGO_PLANO);
$resModelo = jn_query($queryModelo);
$rowModelo = jn_fetch_object($resModelo);
$codModelo = $rowModelo->CODIGOS_MODELO_CONTRATO;




if($_GET['pagina'] == '1' && $codModelo == 1){
	$imagem = imagecreatefromjpeg("../../Site/assets/img/Contrato_DemaisDescontos01.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 10, 0, 738, 152, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NOME_PLANO_FAMILIARES));
	imagettftext($imagem, 10, 0, 448, 193, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->NOME_ASSOCIADO, 0, 30)));
	imagettftext($imagem, 10, 0, 784, 193, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_RG);
	imagettftext($imagem, 10, 0, 902, 193, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_CPF);
	imagettftext($imagem, 9, 0, 167, 215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($endereco, 0, 27)));
	imagettftext($imagem, 25, 0, 2900, 485, $cor,"../../Site/assets/img/arial.ttf",$rowAssociado->NUMERO_TELEFONE_01);
	imagettftext($imagem, 9, 0, 396, 215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($numero));
	imagettftext($imagem, 10, 0, 480, 215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->BAIRRO, 0, 13)));
	imagettftext($imagem, 9, 0, 686, 215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode(substr($rowAssociado->CIDADE, 0, 8)));
	imagettftext($imagem, 10, 0, 782, 215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->ESTADO));
	imagettftext($imagem, 10, 0, 850, 215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->CEP));
	imagettftext($imagem, 10, 0, 1028, 215, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($rowAssociado->NUMERO_TELEFONE_01));
	
	imagettftext($imagem, 10, 0, 850, 1225, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode('18,90'));
	imagettftext($imagem, 10, 0, 900, 1225, $cor,"../../Site/assets/img/arial.ttf", '(' . extenso('18,90') . ')');
	imagettftext($imagem, 10, 0, 670, 1247, $cor,"../../Site/assets/img/arial.ttf",jn_utf8_encode($valorTitDep));
	imagettftext($imagem, 10, 0, 715, 1247, $cor,"../../Site/assets/img/arial.ttf",'(' . extenso($valorTitDep) . ')');
	imagettftext($imagem, 25, 0, 1140, 1950, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 25, 0, 1230, 1950, $cor,"../../Site/assets/img/arial.ttf",date('m'));
	imagettftext($imagem, 25, 0, 1330, 1950, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	
	
	$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
}


if($_GET['pagina'] == '2' && $codModelo == 1){
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');

	$imagem = imagecreatefromjpeg("../../Site/assets/img/Contrato_DemaisDescontos02.jpg");	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	imagettftext($imagem, 9, 0, 228, 1130, $cor,"../../Site/assets/img/arial.ttf",date('d'));
	imagettftext($imagem, 9, 0, 247, 1130, $cor,"../../Site/assets/img/arial.ttf",date('m'));	
	imagettftext($imagem, 9, 0, 267, 1130, $cor,"../../Site/assets/img/arial.ttf",date('Y'));
	$image_p = imagecreatetruecolor(1240, 1754);
		imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 1240, 1754, 1240, 1754);
		header( "Content-type: image/jpeg" );
		return imagejpeg( $image_p, NULL, 80 );
}

function extenso($valor=0, $maiusculas=false,$moeda=false,$np=false)
//$maiusculas true para definir o primeiro caracter
//$moeda true para definir se escreve Reais / Centavos para usar com numerais simples ou monetarios
{
// verifica se tem virgula decimal
if (strpos($valor,",") > 0)
{
    // retira o ponto de milhar, se tiver
    $valor = str_replace(".","",$valor);

    // troca a virgula decimal por ponto decimal
    $valor = str_replace(",",".",$valor);
}

if(!$moeda)
{
$singular  = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
$plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
}
else
{
$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
}

$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
"sessenta", "setenta", "oitenta", "noventa");
$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
"dezesseis", "dezesete", "dezoito", "dezenove");

if(!$moeda) // se for usado apenas para numerais
{
    if($np)
        $u = array("", "uma", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
    else
        $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
}
else
{
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
}
$z=0;

$valor = number_format($valor, 2, ".", ".");
$inteiro = explode(".", $valor);
for($i=0;$i<count($inteiro);$i++)
for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
$inteiro[$i] = "0".$inteiro[$i];

$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
for ($i=0;$i<count($inteiro);$i++) {
$valor = $inteiro[$i];
$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
$ru) ? " e " : "").$ru;
$t = count($inteiro)-1-$i;
$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
if ($valor == "000")$z++; elseif ($z > 0) $z--;
if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
}

if(!$maiusculas){
return($rt ? $rt : "zero");
} else {
return (ucwords($rt) ? ucwords($rt) : "Zero");
}

}


?>