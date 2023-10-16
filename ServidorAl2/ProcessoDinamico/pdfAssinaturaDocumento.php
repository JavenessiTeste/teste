<?php
if(@$_GET['CELULAR']=='OK'){
	require('../lib/autenticaCelular.php');
	if(substr($_SESSION['PERFIL_USUARIO'],0,9)=='ASSOCIADO'){
		$_SESSION['perfilOperador'] = 'BENEFICIARIO';
	}	
	$celular= true;
	
}else{
	$celular= false;
	require('../lib/base.php');

}
header("Content-Type: text/html; charset=UTF8",true);

//$_SESSION['codigoIdentificacao'] = '014009308038001';

$hash    = $_GET['hash']? $_GET['hash'] : false;
$assinar = $_GET['ass'] ? $_GET['ass']  : false;
$tipo    = $_GET['tipo']? $_GET['tipo'] : false;
$json    = $_GET['json']? $_GET['json'] : false;

//$assinar = true;


if($hash){
	$queryPrincipal =	"select * from ESP_ASSINATURA_DOCUMENTO
					 where ESP_ASSINATURA_DOCUMENTO.HASH=  " . aspas($hash);	
				
	$resultQuery = jn_query($queryPrincipal); 
			

	if($rowPrincipal   = jn_fetch_object($resultQuery)){
		$dados = $rowPrincipal->CAMPOS;
		$dados = str_replace('_|_','"',$dados);
		$dados = json_decode($dados,true);
		$tipo = $rowPrincipal->TIPO_DOCUMENTO;
	}else{
		exit;
	}

}else{
	
	
	if($tipo=='BOLETAGEM'){
		
		
		$queryEnd = 'SELECT COALESCE(EMAIL_CONFIRMADO,PS1001.ENDERECO_EMAIL) EMAIL_CONFIRMADO,NOME_ASSOCIADO,NUMERO_CPF,NUMERO_RG,PS1001.* FROM PS1000
					 LEFT JOIN PS1001 ON PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
					 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($_SESSION['codigoIdentificacao']);
	
		
	
		$resEnd  = jn_query($queryEnd);
		if($rowEnd = jn_fetch_object($resEnd)){	
			$endereco = $rowEnd->ENDERECO.' ';
			$numero = '';
			$complemento = '';
			$auxEndereco = explode(',',$endereco);
			$endereco = $auxEndereco[0];
			if(count($auxEndereco)>1){
				$auxEndereco = explode('-',$auxEndereco[1]); 
				$numero = $auxEndereco[0];
				if(count($auxEndereco)>1){
					$complemento = $auxEndereco[1];
				}
			}
		}
	
		
		$dados = array();
		
		$pagina = array();
		$pagina['imagem'] = '..\..\Site\assets\img\boletagem01.png';

		$inicialY = 730;
		$proximaLinha = 33;
		$coluna = 150;
		$tamamnhoLetra = 19;
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-bold.ttf';
		$texto['cor'] = [0,0,0];
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = ($rowEnd->NOME_ASSOCIADO);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		
		
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'Inscrito no CPF/MF: '.$rowEnd->NUMERO_CPF;
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'RG n. '.$rowEnd->NUMERO_RG;
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'Endereço residencial localizado na '. ($endereco);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'n° '.($numero);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'Bairro '. ($rowEnd->BAIRRO);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'Cidade '. ($rowEnd->CIDADE);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'Estado '. ($rowEnd->ESTADO);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'CEP '. ($rowEnd->CEP);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;

		$texto = array();
		$fonte = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'e-mail '.($rowEnd->EMAIL_CONFIRMADO);
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;
		
		$dados[] = $pagina;
		
		$pagina = array();

		$pagina['imagem'] = '..\..\Site\assets\img\boletagem02.png';
		
		$dados[] = $pagina;
		
		
		setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo');
		$tipoModelo = substr($_SERVER['HTTP_USER_AGENT'],0,100); 
		
		$assinatura1 = '';
		$assinatura2 = '';
		$assinatura3 = '';
		
		if($assinar){
			$assinatura1  = ("Assinado eletronicamente mediante login/senha por ".$rowEnd->NOME_ASSOCIADO. ", "."em ".strftime('%A, %d de %B de %Y as %H:%M:%S', strtotime('now')));
			$assinatura2  = ("através  do ".$tipoModelo);
			$assinatura3  = ("IP:".$_SERVER["REMOTE_ADDR"]);
		}
		
		$pagina = array();
		$pagina['imagem'] = '..\..\Site\assets\img\boletagem03.png';

		
		$inicialY = 1000;
		$proximaLinha = 30;
		$coluna = 150;
		$texto = array();
		$texto['fonte'] = 'css\calibri-bold.ttf';
		$texto['cor'] = [0,0,0];
		$texto['tamanho'] = $tamamnhoLetra;
		$texto['texto'] = 'São Paulo,'.strftime('%d de %B de %Y', strtotime('now'));
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		$inicialY = $inicialY + $proximaLinha;
		
		$pagina['textos'][] = $texto;		
		
		
		$inicialY =1530;
		$proximaLinha = 15;
		$texto = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		$texto['estilo'] ='B';
		$texto['tamanho'] = '12';
		$texto['texto'] = $assinatura1;
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		$pagina['textos'][] = $texto;
		$inicialY = $inicialY + $proximaLinha;
		
		$texto = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		$texto['estilo'] ='B';
		$texto['tamanho'] = '12';
		$texto['texto'] = $assinatura2;
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		$pagina['textos'][] = $texto;
		$inicialY = $inicialY + $proximaLinha;
		
		$texto = array();
		$texto['fonte'] = 'css\calibri-regular.ttf';
		$texto['cor'] = [0,0,0];
		$texto['estilo'] ='B';
		$texto['tamanho'] = '12';
		$texto['texto'] = $assinatura3;
		$texto['x'] = $coluna;
		$texto['texto'] = utf8_encode($texto['texto']);
		$texto['y'] = $inicialY;
		$pagina['textos'][] = $texto;
		
		$dados[] = $pagina;

	
	}
	

}

if($assinar){
	$resultado['STATUS'] = 'OK';
	
	$queryEmp = 'SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA';
	$resEmp = jn_query($queryEmp);
	$rowEmp = jn_fetch_object($resEmp);

	$data = date('d/m/Y');
	$hora = date('H:i');
	$hash = md5($data.$hora.$_SESSION['codigoIdentificacao'].$tipo);
	
	$query =" INSERT INTO ESP_ASSINATURA_DOCUMENTO
				   (CAMPOS
				   ,HASH,
				   TIPO_DOCUMENTO,
				   FILTRO_DOCUMENTO)
			 VALUES
				   ('".(json_encode(($dados)))."'
				   ,".aspas($hash).",".aspas($tipo).",".aspas($_SESSION['codigoIdentificacao']).")";

	$query = str_replace('"','_|_',$query);
	
	jn_query($query);

	if($tipo=='BOLETAGEM'){
		$link = retornaValorConfiguracao('LINK_PERSISTENCIA').'ProcessoDinamico/pdfAssinaturaDocumento.php?hash='.$hash;
		$ps1007 ="\nDia : " . date('d/m/Y')."
		".$tipo." 
		\n".$link."
		\n".$assinatura1 ."
		\n".$assinatura2 ."
		\n".$assinatura3 ."
		\n\n
		****   
		\n\n
		";

		$resDados  = jn_query("Select * from PS1007 where CODIGO_ASSOCIADO = ".aspas($_SESSION['codigoIdentificacao']));
		
		if($rowDados = jn_fetch_object($resDados)){
				$query = "UPDATE Ps1007 set Observacao_Cobranca = concat(".aspas($ps1007).",Observacao_Cobranca) WHERE CODIGO_ASSOCIADO=".aspas($_SESSION['codigoIdentificacao']);
				
		}else{
				$query = "INSERT INTO PS1007(CODIGO_ASSOCIADO, OBSERVACAO_COBRANCA) VALUES(".aspas($_SESSION['codigoIdentificacao']).",".aspas($ps1007).")";
		}

		jn_query($query);
	}
	
	echo json_encode($resultado); 
}else{

	foreach ($dados as $pagina) {
		$imagem = imagecreatefrompng($pagina['imagem']);	
		 
		if(count($pagina['textos'])>0){
			foreach ($pagina['textos'] as $texto_data) {
				$cor = imagecolorallocate($imagem, $texto_data['cor'][0],$texto_data['cor'][1],$texto_data['cor'][2]);
				imagettftext($imagem, $texto_data['tamanho'], 0, $texto_data['x'],  $texto_data['y'], $cor,$texto_data['fonte'],utf8_decode($texto_data['texto']));
			}
		}
		ob_start();

		imagejpeg($imagem, null, 100);
		$imagem_base64 = base64_encode(ob_get_clean());
		$resultado['IMG'][] = 'data:image/jpeg;base64,'.$imagem_base64;
		imagedestroy($imagem);
	}
	
	if($json){
		echo json_encode($resultado); 
	}else{
		foreach ($resultado['IMG'] as $imagem){
			echo '<img style="border: 1px solid black;" src="' . $imagem . '" >';
			echo '<br><br>';
		}
	}
}


