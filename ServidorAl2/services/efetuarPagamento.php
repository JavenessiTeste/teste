<?php

require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="compPag/jquery.min.js"></script>
<link href="compPag/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="compPag/index.css" rel="stylesheet" id="bootstrap-css">
<script src="compPag/bootstrap.min.js"></script>
<script src="compPag/util.js?<?php echo time(); ?>"></script>
<script src="compPag/jquery.inputmask.min.js"></script>

<!------ Include the above in your HEAD tag ---------->
<?php

$jurosCartao = false;

if($_GET['JC']=='OK'){
	$jurosCartao = true;
}

if($_GET['tipo']=='F'){	
	
	$queryUltimaParcela  = ' SELECT TOP 1 B.NUMERO_REGISTRO, B.MES_ANO_REFERENCIA AS ULTIMO_VENC_ABERTO, A.MES_ANO_REFERENCIA AS VENC_FAT_ESCOLHIDA  FROM PS1020 A ';
	$queryUltimaParcela .= ' INNER JOIN PS1020 B ON (A.CODIGO_ASSOCIADO = B.CODIGO_ASSOCIADO) ';
	$queryUltimaParcela .= ' WHERE ';
	$queryUltimaParcela .= ' A.DATA_PAGAMENTO IS NULL ';
	$queryUltimaParcela .= ' AND A.DATA_CANCELAMENTO IS NULL '; 
	$queryUltimaParcela .= ' AND B.DATA_PAGAMENTO IS NULL ';
	$queryUltimaParcela .= ' AND B.DATA_CANCELAMENTO IS NULL ';
	$queryUltimaParcela .= ' AND A.NUMERO_REGISTRO =' . aspas($_GET['reg']);
	$queryUltimaParcela .= ' ORDER BY B.DATA_VENCIMENTO ';
	$resUltimaParcela = jn_query($queryUltimaParcela);
    $rowUltimaParcela = jn_fetch_object($resUltimaParcela);

	if($rowUltimaParcela->NUMERO_REGISTRO != $_GET['reg']  and $rowUltimaParcela->NUMERO_REGISTRO>0){ 
		$msg  = ' Caro beneficiário foi selecionado o pagamento referente ao mês ' . $rowUltimaParcela->VENC_FAT_ESCOLHIDA;
		$msg .= ' deseja seguir com esse pagamento? Sua parcela de ' . $rowUltimaParcela->ULTIMO_VENC_ABERTO . ' consta em aberto!';
		echo "<script>alert('" . $msg . "');</script>";
	}

	
	$sqlFatura = "select * from ps1020 where data_pagamento is null and  numero_registro = ".aspas($_GET['reg']);
	$resFatura  = jn_query($sqlFatura);

    if($rowFatura = jn_fetch_object($resFatura)) {
		$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
		$dataVencimento = $rowFatura->DATA_VENCIMENTO;
		$valorFatura 	= $rowFatura->VALOR_FATURA;
		$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
		$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
		if($codigoEmpresa == 400){
			
			$sqlEndereco = "select ps1001.*,Ps1000.nome_associado NOME,Ps1000.numero_cpf DOCUMENTO from ps1001 
			inner join ps1000 on ps1000.codigo_associado = ps1001.codigo_associado
			where ps1000.codigo_associado = ".aspas($codigoAssociado);
			$retorno = 'https://app.plenasaude.com.br/AliancaNet/html/frm_emissao_boletos.php';
			
			if($_GET['info'] == 'REAT'){
				jn_query('UPDATE PS1000 SET FLAG_ASSOC_REATIVACAO =' . aspas('S') . ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado));
				$retorno = 'https://app.plenasaude.com.br/AliancaAppNet2/Site/site/faturasReativacao';
			}
			
		}else{
			$sqlEndereco = "select ps1001.*,ps1010.NOME_EMPRESA NOME,Ps1010.NUMERO_CNPJ DOCUMENTO from ps1001 
			inner join ps1010 on ps1010.codigo_empresa = ps1001.codigo_empresa
			where ps1010.codigo_empresa = ".aspas($codigoEmpresa);
			$retorno = 'https://app.plenasaude.com.br/AliancaNet/html/frm_faturas_geral.php';
		}
		$resEndereco  = jn_query($sqlEndereco);
		if($rowEndereco = jn_fetch_object($resEndereco)){
			$nome = $rowEndereco->NOME;
			$documento = $rowEndereco->DOCUMENTO;
		}
		
		$databd     = $dataVencimento->format('d/m/Y');
		$databd     = explode("/",$databd); 
		$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
		$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$dias       = ($data_atual-$dataBol)/86400;
		$diasAtrazo = ceil($dias);

		
		
	   //pr($diasAtrazo,true);
		$valor_boleto = '';
		if($diasAtrazo>0){
			//pr('teste',true);
			$valor_boleto    = str_replace(",", ".", $valorFatura);
			//$multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
			//$mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
			//$multa = 2;
			//$mora  = 0.033; 
			$multa = 0.03;
			$mora  = 0.0005555; 

			$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
			//$valor_boleto_multa    =  ($valor_boleto * $multa) + $valor_boleto; 
			//$valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
			$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
			$valorBoleto = explode('.',$valor_boleto);
			$val1 = $valorBoleto[0];
			$val2 = substr($valorBoleto[1],0,2);		
			$valorAtual = $val1 . '.' . $val2;		
			$valor_boleto          =  number_format($valorAtual, 2, ',', '');
			//pr($valor_boleto);
			//$valorFatura = $valor_boleto;
			$data_venc=date('d/m/Y');
			
		}		
		
	}else{
		exit;
	}

}else if($_GET['tipo']=='PF'){
	$_GET['P'] = '';
	
	$boleto = true;
	
	
	$sqlFatura ="Select TMP1020_NET.* from TMP1000_GETNET
			    inner join TMP1020_NET on TMP1000_GETNET.CODIGO_ASSOCIADO = TMP1020_NET.CODIGO_ASSOCIADO
				Where TMP1000_GETNET.UUID = ".aspas($_GET['UUID']);
	$resFatura  = jn_query($sqlFatura);

    if($rowFatura = jn_fetch_object($resFatura)) {
		$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
		$_GET['reg']    = $numeroRegistro; 
		$dataVencimento = $rowFatura->DATA_VENCIMENTO;
		$valorFatura 	= $rowFatura->VALOR_FATURA;
		$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
		$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
		$sqlEndereco    =  "Select TMP1001_NET.*,TMP1000_GETNET.nome_associado NOME,TMP1000_GETNET.numero_cpf DOCUMENTO from TMP1001_NET 
							inner join TMP1000_GETNET on TMP1000_GETNET.codigo_associado = TMP1001_NET.codigo_associado
							where TMP1000_GETNET.codigo_associado = ".aspas($codigoAssociado);
		
		$resEndereco  = jn_query($sqlEndereco);
		
		if($rowEndereco = jn_fetch_object($resEndereco)){
			$nome = $rowEndereco->NOME;
			$documento = $rowEndereco->DOCUMENTO;
		}
		
		$databd     = $dataVencimento->format('d/m/Y');
		$databd     = explode("/",$databd); 
		$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
		$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$dias       = ($data_atual-$dataBol)/86400;
		$diasAtrazo = ceil($dias);

		
		
	   //pr($diasAtrazo,true);
		$valor_boleto = '';
		if($diasAtrazo>0){
			//pr('teste',true);
			$valor_boleto    = str_replace(",", ".", $valorFatura);
			//$multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
			//$mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
			//$multa = 2;
			//$mora  = 0.033; 
			$multa = 0.03;
			$mora  = 0.0005555; 

			$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
			//$valor_boleto_multa    =  ($valor_boleto * $multa) + $valor_boleto; 
			//$valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
			$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
			$valorBoleto = explode('.',$valor_boleto);
			$val1 = $valorBoleto[0];
			$val2 = substr($valorBoleto[1],0,2);		
			$valorAtual = $val1 . '.' . $val2;		
			$valor_boleto          =  number_format($valorAtual, 2, ',', '');
			//pr($valor_boleto);
			//$valorFatura = $valor_boleto;
			$data_venc=date('d/m/Y');
			
		}
		
		if($diasAtrazo>1){
			echo "<script>alert('Esse link expirou.');</script>";
			exit;
		}		
		
		$retorno = '';		
	}else{
		exit;
	}

}else if($_GET['tipo']=='PJ'){
	$_GET['P'] = '';
	if($_GET['BVO']=='OK'){
		$boleto = true;
	}
	$sqlFatura =   "Select TMP1020_NET.* from TMP1021_NET
					inner join TMP1020_NET on TMP1020_NET.NUMERO_REGISTRO =  TMP1021_NET.NUMERO_REGISTRO_PS1020
					inner join TMP1000_GETNET on TMP1000_GETNET.CODIGO_ASSOCIADO = TMP1021_NET.CODIGO_ASSOCIADO
					where TMP1020_NET.CODIGO_ASSOCIADO is null and TMP1000_GETNET.UUID = ".aspas($_GET['UUID']);
	$resFatura  = jn_query($sqlFatura);
	//pr($sqlFatura);
    if($rowFatura = jn_fetch_object($resFatura)) {
		
		$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
		$_GET['reg']    = $numeroRegistro; 
		$dataVencimento = $rowFatura->DATA_VENCIMENTO;
		$valorFatura 	= $rowFatura->VALOR_FATURA;
		$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
		$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
		
		$sqlEndereco    =  "Select TMP1001_NET.*,TMP1010_NET.NOME_EMPRESA NOME,TMP1010_NET.NUMERO_CNPJ DOCUMENTO from TMP1001_NET 
							inner join TMP1010_NET on TMP1010_NET.codigo_empresa = TMP1001_NET.codigo_empresa
							where TMP1010_NET.codigo_empresa = ".aspas($codigoEmpresa);
		
		$resEndereco  = jn_query($sqlEndereco);
		//pr($sqlEndereco);
		if($rowEndereco = jn_fetch_object($resEndereco)){
			$nome = $rowEndereco->NOME;
			$documento = $rowEndereco->DOCUMENTO;
		}else{
			exit;
			$sqlEndereco    =  "Select PS1001.*,PS1010.NOME_EMPRESA NOME,PS1010.NUMERO_CNPJ DOCUMENTO from PS1001 
								inner join PS1010 on PS1010.codigo_empresa = PS1001.codigo_empresa
								where PS1010.codigo_empresa = ".aspas($codigoEmpresa);
			//pr($sqlEndereco,true);
			$resEndereco  = jn_query($sqlEndereco);
			
			if($rowEndereco = jn_fetch_object($resEndereco)){
				$nome = $rowEndereco->NOME;
				$documento = $rowEndereco->DOCUMENTO;
			}		
		}
		
		$databd     = $dataVencimento->format('d/m/Y');
		$databd     = explode("/",$databd); 
		$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
		$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$dias       = ($data_atual-$dataBol)/86400;
		$diasAtrazo = ceil($dias);

		
		
	   //pr($diasAtrazo,true);
		$valor_boleto = '';
		if($diasAtrazo>0){
			//pr('teste',true);
			$valor_boleto    = str_replace(",", ".", $valorFatura);
			//$multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
			//$mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
			//$multa = 2;
			//$mora  = 0.033; 
			$multa = 0.03;
			$mora  = 0.0005555; 

			$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
			//$valor_boleto_multa    =  ($valor_boleto * $multa) + $valor_boleto; 
			//$valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
			$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
			$valorBoleto = explode('.',$valor_boleto);
			$val1 = $valorBoleto[0];
			$val2 = substr($valorBoleto[1],0,2);		
			$valorAtual = $val1 . '.' . $val2;		
			$valor_boleto          =  number_format($valorAtual, 2, ',', '');
			//pr($valor_boleto);
			//$valorFatura = $valor_boleto;
			$data_venc=date('d/m/Y');
			
		}	
		
		if($diasAtrazo>1){
			echo "<script>alert('Esse link expirou.');</script>";
			exit;
		}
		
		
		$retorno = '';		
	}else{
		exit;
	}

}elseif($_GET['tipo']=='FA'){
	
	//$_GET['P'] = 'OK';
		
	$sqlFatura = "select PS1020.*,ESP_FATURAS_AGRUPADAS.NUMERO_REGISTRO REGISTRO_AGRUPADO from PS1020 
				  inner join ESP_FATURAS_AGRUPADAS on ESP_FATURAS_AGRUPADAS.NUMERO_REGISTRO_PS1020 = PS1020.NUMERO_REGISTRO
				  where   ESP_FATURAS_AGRUPADAS.NUMERO_AGRUPAMENTO = ".aspas($_GET['id'])."  order by ESP_FATURAS_AGRUPADAS.NUMERO_REGISTRO DESC ";//PS1020.DATA_PAGAMENTO is null and
	$resFatura  = jn_query($sqlFatura);

	$html = '';
	$valorTotal = 0;
    while($rowFatura = jn_fetch_object($resFatura)) {		
		$numeroRegistro = $rowFatura->REGISTRO_AGRUPADO;
		$dataVencimento = $rowFatura->DATA_VENCIMENTO;
		$valorFatura 	= $rowFatura->VALOR_FATURA;
		if($_GET['reg']==''){
			$_GET['reg']    = $numeroRegistro;
			$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
			$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
			
			if($codigoEmpresa == 400){
				$sqlEndereco = "select ps1001.*,Ps1000.nome_associado NOME,Ps1000.numero_cpf DOCUMENTO from ps1001 
				inner join ps1000 on ps1000.codigo_associado = ps1001.codigo_associado
				where ps1000.codigo_associado = ".aspas($codigoAssociado);
				$retorno = 'https://app.plenasaude.com.br/AliancaNet/html/frm_emissao_boletos.php';
			}else{
				$sqlEndereco = "select ps1001.*,ps1010.NOME_EMPRESA NOME,Ps1010.NUMERO_CNPJ DOCUMENTO from ps1001 
				inner join ps1010 on ps1010.codigo_empresa = ps1001.codigo_empresa
				where ps1010.codigo_empresa = ".aspas($codigoEmpresa);
				$retorno = 'https://app.plenasaude.com.br/AliancaNet/html/frm_faturas_geral.php';
			}
			$resEndereco  = jn_query($sqlEndereco);
			if($rowEndereco = jn_fetch_object($resEndereco)){
				$nome = $rowEndereco->NOME;
				$documento = $rowEndereco->DOCUMENTO;
				$html .=$nome.'<br>';
			}
		}

		if($_GET['info'] == 'REAT'){
			jn_query('UPDATE PS1000 SET FLAG_ASSOC_REATIVACAO =' . aspas('S') . ' WHERE CODIGO_ASSOCIADO = ' . aspas($codigoAssociado));
			$retorno = 'https://app.plenasaude.com.br/AliancaAppNet2/Site/site/faturasReativacao';
		}
		
			 
		
		
		$databd     = $dataVencimento->format('d/m/Y');
		$databd     = explode("/",$databd); 
		$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
		$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$dias       = ($data_atual-$dataBol)/86400;
		$diasAtrazo = ceil($dias);

		
		
	   //pr($diasAtrazo,true);
		$valor_boleto = '';
		if($diasAtrazo>0){
			//pr('teste',true);
			$valor_boleto    = str_replace(",", ".", $valorFatura);
			//$multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
			//$mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
			//$multa = 2;
			//$mora  = 0.033; 
			$multa = 0.03;
			$mora  = 0.0005555; 

			$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
			//$valor_boleto_multa    =  ($valor_boleto * $multa) + $valor_boleto; 
			//$valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
			$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
			$valorBoleto = explode('.',$valor_boleto);
			$val1 = $valorBoleto[0];
			$val2 = substr($valorBoleto[1],0,2);		
			$valorAtual = $val1 . '.' . $val2;		
			$valor_boleto          =  number_format($valorAtual, 2, '.', '');
			//pr($valor_boleto);
			//$valorFatura = $valor_boleto;
			$data_venc=date('d/m/Y');
			
		}		
		

		$dataFormatada = $dataVencimento->format('d/m/Y');
		$html .='<br>Valor: '.number_format($valorFatura, 2, ',', '').'<br>';
		$html .='Vencimento: '. $dataFormatada .'<br>';
		if($valor_boleto != ''){
			$html .='Valor Atualizado: '.number_format($valor_boleto, 2, ',', '').'<br>';
			$valorTotal = $valorTotal + $valor_boleto;	
		}else{
			$valorTotal = $valorTotal + $valorFatura;	
		}
		
	}
	
	$html .='<br><br>Valor Total: '.number_format($valorTotal, 2, ',', '').'<br>';
}

//echo $rowEndereco->ENDERECO;
$endereco = explode(',',$rowEndereco->ENDERECO);
if(trim($endereco[0]) == ''){
	echo "<script>alert('Erro beneficiário sem endereço.');</script>";
	exit;
}


function calculaValorFinal($valor,$parcela){
	
	//pr(number_format(($valor)*pow(1.026,$parcela)/$parcela,2));
	//return number_format(number_format(($valor)*pow(1.026,$parcela),2)/$parcela,2);
	return number_format((($valor)*pow(1.03,$parcela))/$parcela,2);
}
?>



<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
<script type="text/javascript" src="https://h.online-metrix.net/fp/tags.js?org_id=k8vif92e&session_id=<?php echo date("Ymd").$_GET['tipo'].'-'.$_GET['reg']; ?>"></script>
<!--<script type="text/javascript" src="https://h.online-metrix.net/fp/tags.js?org_id=1snn5n9w&session_id=<?php echo date("Ymd").$_GET['tipo'].'-'.$_GET['reg']; ?>"></script>-->
</head>


<body style="background-image: url('compPag/fundo.png');background-color: #cccccc;">

<div class="row">
<br>
<aside class="col-sm-1">
</aside>
<aside class="col-sm-5">
	<article>
	 <input type="hidden" id="retorno" name="retorno" value="<?php echo $retorno; ?>">
	 <input type="hidden" name="deviceFingerprintID" value="<?php echo date("Ymd").$_GET['tipo'].'-'.$_GET['reg']; ?>" />
	<?php if($_GET['tipo']!=='FA'){?>
	<p class="dadosPagadorp" style="color: white;">
		<?php echo $nome; ?> <br/>
		Valor: <?php echo $valorFatura; ?>  <br/>
		Vencimento:  <?php echo $dataVencimento->format('d/m/Y');?>
		<?php
		if($valor_boleto != ''){
			echo '<br/>Valor Atualizado: '.$valor_boleto;
		}else{
			$valor_boleto = $valorFatura;
		}
		
		if($valorTotal < 1){			
			$valorTotal = $valor_boleto;
		}
				
		?>
	</p>
	<?php }else{
		$_GET['reg'] = $_GET['id'];
	?>	
	<p class="dadosPagadorp" style="color: white;">
	<?php echo $html; ?>
	
	</p>
	<?php } 
	if($valor_boleto != ''){
			
	}else{
			$valor_boleto = $valorFatura;
	}
	?>
	</article>
	<div class="dadosPagadorp" id="Tipo">
		<p   style="color: white;bottom: 50px;">
			Será aceito as seguintes bandeiras na função crédito
			
		<p/>
		<img class="img-fluid" style="max-width: 70% !important;" src="compPag/Imgcredito.png" >
	</div>
	
</aside>
<aside class="col-sm-5" style="padding-top: 50px;">

<article class="card">
<div class="card-body p-5">

<ul class="nav bg-light nav-pills rounded nav-fill mb-3" role="tablist">
	<li class="nav-item">
	
		<a class="nav-link active" data-toggle="pill" href="#nav-tab-card" onclick="tipoPag('CR')">
		<i class="far fa-credit-card"></i> Cartão de Crédito</a></li>
		
	<li class="nav-item">
		<a class="nav-link" data-toggle="pill" href="#nav-tab-bank" onclick="tipoPag('DE')">
		<i class="fas fa-money-check-alt"></i> Cartão de Débito</a>
	</li>
		<?php 
			if($boleto){ 
		?>
	<li class="nav-item">
		<a class="nav-link" data-toggle="pill" href="#nav-tab-paypal" onclick="tipoPag('BO')">
		<i class="fas fa-money-check"></i> Boleto Bancário</a>
	</li>
		<?php }
		?>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="nav-tab-card">
			
			<div class="row">
				<div class="col-md-12 mb-6">
					<label for="cc-parcelas">Parcelas</label>
					<select class="form-control" class="form-control" id="cc-parcelas" placeholder="" required="">
						<option value="1" selected> <?php	if($jurosCartao) 
																		echo '1x R$ '.number_format(($valorTotal*(3/100))+$valorTotal,2).' - com juros';
															else
																		echo 'À Vista R$ '.$valorTotal;
																		?></option>
						<option value="2">2x  R$ <?php echo calculaValorFinal($valorTotal,2); ?> - com juros</option>
						<option value="3">3x  R$ <?php echo calculaValorFinal($valorTotal,3); ?> - com juros</option>
						<option value="4">4x  R$ <?php echo calculaValorFinal($valorTotal,4); ?> - com juros</option>
						<option value="5">5x  R$ <?php echo calculaValorFinal($valorTotal,5); ?> - com juros</option>
						<option value="6">6x  R$ <?php echo calculaValorFinal($valorTotal,6); ?> - com juros</option>
					</select>
				</div>
			</div>

			<div class="row">
              <div class="col-md-12 mb-6">
                <label for="cc-nome">Nome no cartão</label>
                <input type="text" class="form-control" id="cc-nome" placeholder="" required="" pattern="[0-9a-zA-ZáàÀÁãâÃÂéèÈÉ???êóòÓÒõôÔÕúùuûÛUÚÙ ]*">
                <small class="text-muted">Nome completo, como mostrado no cart?o.</small>
                <div class="invalid-feedback">
                  O nome que está no cartão é obrigatório.
                </div>
              </div>
            </div>
			<div class="row">
              <div class="col-md-6 mb-3">
                <label for="cc-numero">Número do cartão de crédito</label>
                <input type="number" class="form-control" id="cc-numero" placeholder="" required="" maxlength="23" onkeyup="validacaoNumero('cc-numero','cc-bandeira')">
                <div class="invalid-feedback">
                  O número do cartão de crédito é obrigatório.
                </div>
              </div>
			  <div class="col-md-6 mb-3">
                <label for="cc-nome">Bandeira do cartão</label>
                <select class="form-control" class="form-control" id="cc-bandeira" placeholder="" required="">
					<option value="mastercard">Mastercard</option>
					<option value="visa">Visa</option>
					<option value="amex">Amex</option>
					<option value="elo">Elo</option>
					<option value="hipercard">Hipercard</option>
				 </select>
                <div class="invalid-feedback">
                  A bandeira é obrigatoria.
                </div>
              </div>
            </div>
			<div class="row">
              <div class="col-md-6 mb-3">
                <label for="cc-expiracao">Expiração do cartão</label>
                <input type="text" class="form-control" id="cc-expiracao" placeholder="MM/AA" required="" maxlength="5">
                <div class="invalid-feedback">
                  Expiração é obrigatória.
                </div>
              </div>
			  
              <div class="col-md-6 mb-3">
                <label for="cc-cvv">CVV</label><img  alt="calendario" src="compPag/cartao.png" style="height: 29px;">
                <input type="text" class="form-control" id="cc-cvv" placeholder="" required="" maxlength="4">
                <div class="invalid-feedback">
                  Código de segurança é obrigatório.
                </div>
              </div>
            </div>
			
			<div  class="form-group-btn">
				<button  id="gerar-credito" onClick="pagamentoCredito('<?php echo $_GET['tipo']; ?>','<?php echo $_GET['reg']; ?>');" class="botaoForm">Efetuar Pagamento</button>
			</div>
</div> <!-- tab-pane.// -->
<div class="tab-pane fade" id="nav-tab-paypal">
	<div  class="payment-form">
	  <h4 align="center">Leia as informações e gere o seu boleto</h4>

	  <div  class="boleto-steps">

		<div  class="boleto-item">
		  <p  class="boleto-text"><img  alt="impressora" src="compPag/impressora.svg"> Faça o pagamento do boleto em uma agência bancária ou pela internet.</p>
		</div>
		
		<div  class="boleto-item">
		  <p  class="boleto-text"><img  alt="calendario" src="compPag/calendario.svg">Fique atento à data de vencimento do boleto.</p>
		</div>
		<br>		
	  </div>
	  <br>	
	  <div  class="attention-message">
		<p><span >!</span><strong >Atenção:</strong> Caso você possua um programa anti pop-up, será necessário desativá-lo para conseguir imprimir ou salvar o boleto.</p>
	  </div>

	  <div  class="form-group-btn">
		<button  id="gerar-boleto" onClick="pagamentoBoletoSantander('<?php echo $_GET['tipo']; ?>','<?php echo $_GET['reg']; ?>');" class="botaoForm">Gerar boleto</button>
	  </div>
	</div>
	<div  class="payment-form">
		  <ul  class="boleto-informations">
			<li >Só emitimos boletos do Banco Santander. Confira os dados antes de efetuar o pagamento.</li>
			<li >O boleto não será enviado para o seu endereço físico.</li>
			<li >Não realize o pagamento por meio de transferência, depósito ou DOC para a conta indicada no boleto.</li>
		  </ul>
	</div>
</div>
<div class="tab-pane fade" id="nav-tab-bank">
			  <h4 align="center">Leia as informações e realize o débito on-line</h4>

			  <div  class="boleto-steps">

				<div  class="boleto-item">
				  <p  class="boleto-text"><img  alt="impressora" src="compPag/informardados.png"> Infome os dados do cartão de débito</p>
				</div>
				
				<div  class="boleto-item">
				  <p  class="boleto-text"><img  alt="calendario" src="compPag/banco.png">Você será direcionado para a página do banco</p>
				</div>
				
				<div  class="boleto-item">
				  <p  class="boleto-text"><img  alt="calendario" src="compPag/finalizarpagamento.png">Finalize o pagamento</p>
				</div>
				<br>
			  </div>
			  <br>
			  <div  class="attention-message">
				<p><span >!</span><strong >Atenção:</strong> Caso você possua um programa anti pop-up, será necessário desativá-lo para conseguir prosseguir com o pagamento.</p>
			  </div>
			 <?php if($jurosCartao){ ?>
			<div class="row">
				<div class="col-md-12 mb-6">
					<label for="cc-parcelas">Parcela</label>
					<select class="form-control" class="form-control" id="cc-parcelas" placeholder="" required="">
						<option value="1" selected> <?php	echo '1x R$ '.number_format(($valorTotal*(2/100))+$valorTotal,2).' - com juros';
															
																		?></option>
					</select>
				</div>
			</div>
			
			 <?php } ?>	
			<div class="row">
              <div class="col-md-12 mb-6">
                <label for="cd-nome">Nome no cartão</label>
                <input type="text" class="form-control" id="cd-nome" placeholder="" required="" pattern="[0-9a-zA-ZáàÀÁãâÃÂéèÈÉ???êóòÓÒõôÔÕúùuûÛUÚÙ ]*">
                <small class="text-muted">Nome completo, como mostrado no cartão.</small>
                <div class="invalid-feedback">
                  O nome que está no cartão é obrigatório.
                </div>
              </div>
            </div>
			<div class="row">
              <div class="col-md-6 mb-3">
                <label for="cd-numero">Número do cartão </label>
                <input type="number" class="form-control" id="cd-numero" placeholder="" required="" maxlength="23" onkeyup="validacaoNumero('cd-numero','cd-bandeira')">
                <div class="invalid-feedback">
                  O número do cartão é obrigatório.
                </div>
              </div>
			  <div class="col-md-6 mb-3">
                <label for="cd-nome">Bandeira do cartão</label>
                
				<select class="form-control" class="form-control" id="cd-bandeira" placeholder="" required="">
					<option value="mastercard">Mastercard</option>
					<option value="visa">Visa</option>
					<option value="elo">Elo</option>
				 </select>
                <div class="invalid-feedback">
                  A bandeira é obrigatoria.
                </div>
              </div>
            </div>
			<div class="row">
              <div class="col-md-6 mb-3">
                <label for="cd-expiracao">Expiração do cartão</label>
                <input type="text" class="form-control" id="cd-expiracao" placeholder="MM/AA" required="" maxlength="5">
                <div class="invalid-feedback">
                  Expiração é obrigatória.
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="cd-cvv">CVV</label><img  alt="calendario" src="compPag/cartao.png" style="height: 29px;">
                <input type="text" class="form-control" id="cd-cvv" placeholder="" required="" maxlength="4">
                <div class="invalid-feedback">
                  Código de segurança é obrigatório.
                </div>
              </div>
            </div>
			<div class="row">
              <div class="col-md-6 mb-3">
				<label for="cc-cvv">Telefone</label>
                <input type="tel" class="form-control" id="cd-telefone" placeholder="" required="" maxlength="15">			  
			  </div>
			</div>
			<div  class="form-group-btn">
				<button  id="gerar-debito" onClick="pagamentoDebito('<?php echo $_GET['tipo']; ?>','<?php echo $_GET['reg']; ?>');" class="botaoForm">Continuar Pagamento</button>
			</div>
			<div  class="payment-form">
				<ul  class="boleto-informations">
					<li >Disponível para clientes com cartões de débito emitidos pelo Itaú, Bradesco, Banco do Brasil e Santander.</li>
					<li >Consulte seu extrato bancário para se certificar de que o pagamento foi realmente realizado.</li>
				</ul>
			</div>
</div> <!-- tab-pane.// -->
<input id="JC" name="JC" type="hidden" value="<?php echo $jurosCartao; ?>">
</div> <!-- tab-content .// -->

</div> <!-- card-body.// -->
</article> <!-- card.// -->


	</aside> <!-- col.// -->
</div> <!-- row.// -->

</div> 
<div class="modal fade" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="loader"></div>
        <div clas="loader-txt">
          <p>Aguarde Processando Pagamento</p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="alert" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">	

      <div class="modal-body text-center">
	 
		<div clas="loader-txt">
          <p id="textoAlert">Aguarde Processando Pagamento</p>
        </div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="finalizacao" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">	

      <div class="modal-body text-center">
	 
		<div clas="loader-txt">
          <p id="textoFinalizacao">Aguarde Processando Pagamento</p>
        </div>
      </div>
    </div>
  </div>
</div>
<!--container end.//-->
</body>
</html>
