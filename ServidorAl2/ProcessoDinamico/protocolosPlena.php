<?php
require('../lib/base.php');
require('../private/autentica.php');

$tipo = explode(";",$_GET['tipo']);

if ($tipo[0] == 'I'){
	$Titulo = "PROTOCOLO DE ADESÃO EFETIVADA";	
	$filtro = "CODIGO_ASSOCIADO = " . aspas(htmlentities($tipo[1]));
}else if($tipo[0] == 'E'){
	$Titulo = "Exclusão de Associado";
	$filtro = "CODIGO_ASSOCIADO IN (".str_replace("\'","'",htmlentities($tipo[1])).")";
}

$codigoValidar = $tipo[1];	
if($codigoValidar < 0){
	$tabela = "TMP1000_NET";		
}else{		
	$tabela = "PS1000";
}

$buf  = " Select " . $tabela . ".*, PS1030.CODIGO_CADASTRO_ANS, PS1030.NOME_PLANO_EMPRESAS, PS1010.NOME_EMPRESA from ". $tabela;
$buf .= " inner join ps1030 on (ps1030.codigo_plano = " . $tabela .".codigo_plano)" ;
$buf .= " inner join ps1010 on (ps1010.codigo_empresa = " . $tabela .".codigo_empresa)" ;
$buf .= " where  ". $filtro . " Order by Codigo_Associado " ;

$res=jn_query($buf);
$row=jn_fetch_assoc($res);

$numeroProtocolo = $row['PROTOCOLO_GERAL_PS6450'];
$codigoAssociado = $row['CODIGO_ASSOCIADO'];
$codigoTitular = $row['CODIGO_TITULAR'];
$nomeAssociado = $row['NOME_ASSOCIADO'];
$nomePlano = $row['NOME_PLANO_EMPRESAS'];
$numeroRegAns = $row['CODIGO_CADASTRO_ANS'];
$nomeEmpresa = $row['NOME_EMPRESA'];
$codigoEmpresa = $row['CODIGO_EMPRESA'];
$TipoAssociado = $row['TIPO_ASSOCIADO']=="T"? "TITULAR": "DEPENDENTE";
$dataNascimento = SqlToData($row['DATA_NASCIMENTO']);
$numeroCPF = $row['NUMERO_CPF'];
$dataCadastro = SqlToData($row['DATA_ADMISSAO']);	
$dataSolExclusaoExp = explode(' D',$row['INFORMACOES_LOG_E']);
$dataSolExclusao = explode(']',$dataSolExclusaoExp[1]);
$dataExclusao = $dataSolExclusao[0];

if(!$dataExclusao){
	$dataExclusao = date('d/m/Y');	
}

$dataFimUtiliz = SqlToData($row['DATA_EXCLUSAO']);

if ($tipo[0] == 'I'){
	if($row['TIPO_ASSOCIADO'] == 'T'){		
		
		$atualizaAnexos  = ' UPDATE CFGARQUIVOS_BENEF_NET SET CODIGO_ASSOCIADO = ' . aspas($codigoValidar);
		$atualizaAnexos .= ' WHERE  CODIGO_ASSOCIADO IS NULL AND ';
		$atualizaAnexos .= ' CODIGO_ASSOCIADO_TMP IN (SELECT CODIGO_ANTIGO FROM PS1000 WHERE CODIGO_ASSOCIADO =' . aspas($codigoValidar) . ') ';
		jn_query($atualizaAnexos);

		$imagem = imagecreatefromjpeg( "../../Site/assets/img/protocoloAdesaoTitular.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		$cor2 = imagecolorallocate($imagem, 254, 254, 254 );

		imagettftext($imagem, 13, 0, 170, 210, $cor,"../../Site/assets/img/arial.ttf",$codigoEmpresa);
		imagettftext($imagem, 13, 0, 430, 210, $cor,"../../Site/assets/img/arial.ttf",$nomeEmpresa);
		imagettftext($imagem, 13, 0, 100, 250, $cor,"../../Site/assets/img/arial.ttf",$nomePlano);
		imagettftext($imagem, 13, 0, 750, 250, $cor,"../../Site/assets/img/arial.ttf",$numeroRegAns);
		imagettftext($imagem, 13, 0, 210, 295, $cor,"../../Site/assets/img/arial.ttf",$nomeAssociado);
		imagettftext($imagem, 13, 0, 810, 295, $cor,"../../Site/assets/img/arial.ttf",$TipoAssociado);
		imagettftext($imagem, 13, 0, 180, 340, $cor,"../../Site/assets/img/arial.ttf",$dataNascimento);
		imagettftext($imagem, 13, 0, 650, 340, $cor,"../../Site/assets/img/arial.ttf",$numeroCPF);
		imagettftext($imagem, 13, 0, 1080, 455, $cor,"../../Site/assets/img/arial.ttf",$dataExclusao);

	}elseif($row['TIPO_ASSOCIADO'] == 'D'){
		$imagem = imagecreatefromjpeg( "../../Site/assets/img/protocoloAdesaoDependente.jpg");
		$cor = imagecolorallocate($imagem, 0, 0, 0 );
		$cor2 = imagecolorallocate($imagem, 254, 254, 254 );

		imagettftext($imagem, 13, 0, 40, 160, $cor2,"../../Site/assets/img/arial.ttf",'Protocolo: ' . $numeroProtocolo);
		imagettftext($imagem, 13, 0, 170, 210, $cor,"../../Site/assets/img/arial.ttf",$codigoEmpresa);
		imagettftext($imagem, 13, 0, 430, 210, $cor,"../../Site/assets/img/arial.ttf",$nomeEmpresa);
		imagettftext($imagem, 13, 0, 100, 250, $cor,"../../Site/assets/img/arial.ttf",$nomePlano);
		imagettftext($imagem, 13, 0, 750, 250, $cor,"../../Site/assets/img/arial.ttf",$numeroRegAns);
		imagettftext($imagem, 13, 0, 210, 295, $cor,"../../Site/assets/img/arial.ttf",$nomeAssociado);
		imagettftext($imagem, 13, 0, 682, 295, $cor,"../../Site/assets/img/arial.ttf",$codigoAssociado);
		imagettftext($imagem, 13, 0, 180, 340, $cor,"../../Site/assets/img/arial.ttf",$dataNascimento);
		imagettftext($imagem, 13, 0, 400, 340, $cor,"../../Site/assets/img/arial.ttf",$numeroCPF);
		imagettftext($imagem, 13, 0, 810, 340, $cor,"../../Site/assets/img/arial.ttf",$TipoAssociado);
		imagettftext($imagem, 13, 0, 1080, 472, $cor,"../../Site/assets/img/arial.ttf",$dataCadastro);
	}
}elseif ($tipo[0] == 'E'){
	$imagem = imagecreatefromjpeg( "../../Site/assets/img/protocoloExclusao.jpg");
	$cor = imagecolorallocate($imagem, 0, 0, 0 );
	$cor2 = imagecolorallocate($imagem, 254, 254, 254 );

	imagettftext($imagem, 13, 0, 170, 210, $cor,"../../Site/assets/img/arial.ttf",$codigoEmpresa);
	imagettftext($imagem, 13, 0, 430, 210, $cor,"../../Site/assets/img/arial.ttf",$nomeEmpresa);
	imagettftext($imagem, 13, 0, 100, 250, $cor,"../../Site/assets/img/arial.ttf",$nomePlano);
	imagettftext($imagem, 13, 0, 750, 250, $cor,"../../Site/assets/img/arial.ttf",$numeroRegAns);
	imagettftext($imagem, 13, 0, 210, 295, $cor,"../../Site/assets/img/arial.ttf",$nomeAssociado);
	imagettftext($imagem, 13, 0, 682, 295, $cor,"../../Site/assets/img/arial.ttf",$codigoTitular);		
	imagettftext($imagem, 13, 0, 940, 340, $cor,"../../Site/assets/img/arial.ttf",$TipoAssociado);
	imagettftext($imagem, 13, 0, 210, 340, $cor,"../../Site/assets/img/arial.ttf",$dataExclusao);
	imagettftext($imagem, 13, 0, 610, 340, $cor,"../../Site/assets/img/arial.ttf",$dataFimUtiliz);
	imagettftext($imagem, 13, 0, 1000, 472, $cor,"../../Site/assets/img/arial.ttf",'Data Atual: ' . date('d/m/Y'));
}else{
	exit;
}

$image_p = imagecreatetruecolor(2050, 720);
imagecopyresampled($image_p, $imagem, 0, 0, 0, 0, 2790, 890, 1600, 600);
header( 'Content-type: image/jpeg' ); 
imagejpeg( $image_p, NULL, 80 );
?>