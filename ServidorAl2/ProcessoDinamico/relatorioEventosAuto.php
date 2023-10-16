<?php
require_once('../lib/base.php');


$queryPrincipal =	"select * from ESP_ASSINATURA_DOCUMENTO
					 where ESP_ASSINATURA_DOCUMENTO.HASH=  " . aspas($_GET['cod']);	
				
$resultQuery = jn_query($queryPrincipal); 
		

if($rowPrincipal   = jn_fetch_object($resultQuery)){
	
	$dados = $rowPrincipal->CAMPOS;
	$dados = str_replace('_|_','"',$dados);
	$dados = json_decode($dados);

	if($_GET['pdf']=='OK'){
		require_once('../lib/base.php');

		include("../lib/mpdf60/mpdf.php");
		$mpdf=new mPDF(); 
		//$mpdf->format=[190, 236];
		$mpdf->charset_in='windows-1252';
		$mpdf->curlAllowUnsafeSslRequests = true;
		$mpdf->SetDisplayMode('fullpage');	
			
		$mpdf->WriteHTML('<style>@page {margin: 0px;}</style><img Width="1239" Height="1754" src="relatorioEventosAuto.php?cod=' . $_GET['cod'] . '&p=1" />');
		$mpdf->AddPage();
		$mpdf->WriteHTML('<img Width="1239" Height="1754" src="relatorioEventosAuto.php?cod=' . $_GET['cod'] . '&p=2" />');
		$mpdf->AddPage();
		$mpdf->WriteHTML('<img Width="1239" Height="1754" src="relatorioEventosAuto.php?cod=' . $_GET['cod'] . '&p=3" />');
		$mpdf->AddPage();
		$mpdf->WriteHTML('<img Width="1239" Height="1754" src="relatorioEventosAuto.php?cod=' . $_GET['cod'] . '&p=4" />');
		$mpdf->AddPage();
		$mpdf->WriteHTML('<img Width="1239" Height="1754" src="relatorioEventosAuto.php?cod=' . $_GET['cod'] . '&p=5" />');
		$mpdf->AddPage();
		$mpdf->WriteHTML('<img Width="1239" Height="1754" src="relatorioEventosAuto.php?cod=' . $_GET['cod'] . '&p=6" />');
		$mpdf->AddPage();
		$mpdf->WriteHTML('<img Width="1239" Height="1754" src="relatorioEventosAuto.php?cod=' . $_GET['cod'] . '&p=7" />');
		
		
		//$mpdf->debug = true;
		$mpdf->WriteHTML($html);
		$mpdf->Output();

		exit;
	}



	if($_GET['p'] == '1'){
		$imagem = imagecreatefrompng("../../Site/assets/img/Contrato1.png");	
	}
	if($_GET['p'] == '2'){
		$imagem = imagecreatefrompng("../../Site/assets/img/Contrato2.png");	
	}
	if($_GET['p'] == '3'){
		$imagem = imagecreatefrompng("../../Site/assets/img/Contrato3.png");	
	}
	if($_GET['p'] == '4'){
		$imagem = imagecreatefrompng("../../Site/assets/img/Contrato4.png");	
	}
	if($_GET['p'] == '5'){
		$imagem = imagecreatefrompng("../../Site/assets/img/Contrato5.png");	
	}
	if($_GET['p'] == '6'){
		$imagem = imagecreatefrompng("../../Site/assets/img/Contrato6.png");	
	}
	if($_GET['p'] == '7'){
		$imagem = imagecreatefrompng("../../Site/assets/img/Contrato7.png");	
	}

	
	$cor = imagecolorallocate($imagem, 0, 0, 0 );

	$arialbd = "../../Site/assets/img/arialbd.ttf";
	$arial = "../../Site/assets/img/arial.ttf";
	$tamanhoLetra = 16; 

	$pessoas = count($dados->pessoas);
	if($_GET['p'] == '1'){
			$linha = 490;
			$coluna = 178;
			$pulaLinha = 28;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$dados->nome);	
			$linha += $pulaLinha;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Inscrito no CPF/MF: ".$dados->cpf);	
			$linha += $pulaLinha;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"RG n. ".$dados->rg);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Endereço residencial localizado na ".$dados->endereco);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"n° ".$dados->numero);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Bairro ".$dados->bairro);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Cidade ".$dados->cidade);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Estado ".$dados->estado);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"CEP ".$dados->cep);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Email ".$dados->email);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Doravante designada  ");
			imagettftext($imagem, $tamanhoLetra, 0, 400, $linha, $cor,$arialbd,"CONTRATANTE".$pessoas);		

			if($pessoas>0){
				$pessoa = $dados->pessoas[0];
				$linha = 1100;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$pessoa->nome);	
				$linha += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Inscrito no CPF/MF: ".$pessoa->cpf);	
				$linha += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"RG n. ".$pessoa->rg);
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Endereço residencial localizado na ".$dados->endereco);
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"n° ".$dados->numero);
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Bairro ".$dados->bairro);
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Cidade ".$dados->cidade);	
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Estado ".$dados->estado);	
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"CEP ".$dados->cep);	
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Email ".$pessoa->email);	
				$linha += $pulaLinha;	
			}
			if($pessoas>1){
				$pessoa = $dados->pessoas[1];
				$linha = 1460;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$pessoa->nome);	
				$linha += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Inscrito no CPF/MF: ".$pessoa->cpf);	
				$linha += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"RG n. ".$pessoa->rg);
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Endereço residencial localizado na ".$dados->endereco);
				$linha += $pulaLinha;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"n° ".$dados->numero);
			}
	}
	if($_GET['p'] == '2'){
		$coluna = 178;
		$pulaLinha = 28;
		if($pessoas>1){
			$pessoa = $dados->pessoas[1];
			$linha = 194;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Bairro ".$dados->bairro);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Cidade ".$dados->cidade);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Estado ".$dados->estado);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"CEP ".$dados->cep);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Email ".$pessoa->email);	
			$linha += $pulaLinha;	
		}
		if($pessoas>2){
			$pessoa = $dados->pessoas[2];
			$linha += $pulaLinha;	
			$linha += $pulaLinha;	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$pessoa->nome);	
			$linha += $pulaLinha;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Inscrito no CPF/MF: ".$pessoa->cpf);	
			$linha += $pulaLinha;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"RG n. ".$pessoa->rg);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Endereço residencial localizado na ".$dados->endereco);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"n° ".$dados->numero);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Bairro ".$dados->bairro);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Cidade ".$dados->cidade);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Estado ".$dados->estado);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"CEP ".$dados->cep);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Email ".$pessoa->email);	
		}
		if($pessoas>3){
			$pessoa = $dados->pessoas[3];
			$linha += $pulaLinha;	
			$linha += $pulaLinha;	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$pessoa->nome);	
			$linha += $pulaLinha;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Inscrito no CPF/MF: ".$pessoa->cpf);	
			$linha += $pulaLinha;
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"RG n. ".$pessoa->rg);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Endereço residencial localizado na ".$dados->endereco);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"n° ".$dados->numero);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Bairro ".$dados->bairro);
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Cidade ".$dados->cidade);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Estado ".$dados->estado);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"CEP ".$dados->cep);	
			$linha += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,"Email ".$pessoa->email);	
		}

	}
	if($_GET['p'] == '5'){
		$coluna = 182;
		$linha  = 547;
		$pulaLinha = 28;
		imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$dados->diaVencimento);	
		imagettftext($imagem, $tamanhoLetra+3, 0, $coluna+200, $linha, $cor,$arial,$dados->diaVencimentoExtenso);	
	}

	if($_GET['p'] == '7'){
		$data = explode('/',$dados->data);
		//$dados->mes;
		$coluna = 295;
		$linha  = 1215;
		$pulaLinha = 28;
		$tamanhoLetra = 18; 
		imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$data[0]);
		$coluna = 401;
		imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$dados->mes);
		$coluna = 857;
		imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,substr($data[2],2,2));	
		$coluna = 196;
		$linha  = 1460;	
		imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$dados->nome);	
		$coluna = 181;
		$linha  = 1570;
		$tamanhoLetra = 12; 	

		imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$arial,$dados->assinatura);		
	}


	


	header( 'Content-type: image/jpeg' );
	imagejpeg( $imagem, NULL, 100 );

	
}

?>