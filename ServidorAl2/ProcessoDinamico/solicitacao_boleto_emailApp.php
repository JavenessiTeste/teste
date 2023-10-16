<?php
require('../lib/base.php');
require('../lib/autenticaCelular.php');
require('../private/autentica.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$solicitacaoRealizada = false;

$queryValid = 'SELECT FLAG_BOLETO_APENAS_EMAIL FROM PS1002 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);	
$resValid = jn_query($queryValid);
$rowValid = jn_fetch_object($resValid);

if($rowValid->FLAG_BOLETO_APENAS_EMAIL == 'S'){
	$solicitacaoRealizada = true;
}

$queryDadosBenef  = ' SELECT PS1001.ENDERECO_EMAIL, PS1006.CODIGO_AREA, PS1006.NUMERO_TELEFONE FROM PS1000';
$queryDadosBenef .= ' INNER JOIN PS1001 ON (PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)';
$queryDadosBenef .= ' LEFT OUTER JOIN PS1006 ON (PS1006.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO AND PS1006.INDICE_TELEFONE = 1)';	
$queryDadosBenef .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);	
$resDadosBenef = jn_query($queryDadosBenef);
$rowDadosBenef = jn_fetch_object($resDadosBenef);

$email = $rowDadosBenef->ENDERECO_EMAIL;
$codArea = $rowDadosBenef->CODIGO_AREA;
$telefone = $rowDadosBenef->NUMERO_TELEFONE;

?>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<link rel="stylesheet" href="../css/sweetalert2.css">

<html xmlns="http://www.w3.org/1999/xhtml">    
	<head>
		<meta name="viewport" content="initial-scale=1.0, width=device-width">
	</head>
   <body style="text-align:left; font-size: 12px;" leftmargin="10">
		<form class="form-horizontal" name="formDefault" method="post" id="formDefault" accept-charset="utf-8" enctype="multipart/form-data" target="_self">
			<div id="paginaSemAssinatura">
				<img id="imagem" width="100%" src="retornaImagem.php?tp=imagemBoletoPlena<?php echo '&email='.$email.'&codArea='.$codArea.'&telefone='.$telefone; ?>">				
			</div>
			<div id="paginaJaAssinado">
				<img id="imagem" width="100%" src="retornaImagem.php?tp=imagemBoletoPlena<?php echo '&email='.$email.'&codArea='.$codArea.'&assinado=JaAssinado&telefone='.$telefone; ?>">
			</div>
			<div id="conteudoImpressao"  style="display:none;">
				<img id="imagem" width="100%" src="retornaImagem.php?tp=imagemBoletoPlena<?php echo '&email='.$email.'&codArea='.$codArea.'&assinado=SIM&telefone='.$telefone; ?>">				
			</div>
			<br />
			<input class="ace" type="checkbox" id="contratoInc" onClick="atualizaComAssinatura();" ><span class="lbl" id="descAceite">&nbsp;&nbsp;Declaro a veracidade das informa&ccedil;&otilde;es </span>
			<br />
			<div class="formDefault_Rodape" id="painelBotoes">
				<input type='button' class="btn btn-success" onclick="salvaSolBoletoEmail(); " name="btnConcluir" id="btnConcluir"  value="Gravar" />				
			</div> 						
		</form>
   </body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js" integrity="sha384-THVO/sM0mFD9h7dfSndI6TS0PgAGavwKvB5hAxRRvc0o9cPLohB0wb/PTA7LdUHs" crossorigin="anonymous"></script>

<script>

verificaSolicitacaoRealizada();

var element = $("#conteudoImpressao"); // global variable
var getCanvas ; // global variable

function salvaSolBoletoEmail(){
	
	if (!document.getElementById('contratoInc').checked) {
		swal({
			type: 'error',
			title: 'Para continuar, favor declarar ci\u00eancia das informa\u00e7\u00f5es.'
		})
		return false;
	}	
	
	$.ajax({
			type: "GET",
			dataType: "html",
			url: "operacoesProcessos.php?op=solicitacaoBoletoEmail",
			data: 	"",
			complete: function(){
				
			},
			success: function(msg){		
				gerarPdf();			
				swal({
						type: 'success',
						title: msg,
						showLoaderOnConfirm: true,
						preConfirm: () => {
							location.reload();
						}
					}).then((value) => {
						$('#painelBotoes').css("display", "none");
						$('#contratoInc').css("display", "none");
						$('#descAceite').css("display", "none");
						funcaoVoltar();
					});
			}		
		});

    	
}

function gerarPdf(){	
	const img = document.getElementById('imagem');
	const width = img.clientWidth;
	const height = img.clientHeight;
	img.style.height = '1754px';
    img.style.width = '1240px';
	let options = {};
	  options = {
		useCORS: true,
		foreignObjectRendering: true,
		width: 1240,
		height: 1754
	  };  
		
	html2canvas(element, options).then(function(canvas) {
		var dataURL = canvas.toDataURL(); 
		img.style.height = height+'px';
		img.style.width = width+'px';
		var type = 'boletoEmail';		
		var xhr = new XMLHttpRequest();
        xhr.open( 'POST', 'upload_pdf_ftp_jquery.php', true ); //Post to php Script to save to server
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("dados="+dataURL+"&type="+type);		        
	});	
		
}

function funcaoVoltar(){
	setTimeout(window.history.back(), 250000);	
}

function verificaSolicitacaoRealizada(){
	$.ajax({
			type: "GET",
			dataType: "html",
			url: "operacoesProcessos.php?op=verificaSolicitacaoRealizada",				
			success: function(msg){		
				if(msg == 'S'){
					document.getElementById('conteudoImpressao').style.display = "none";
					document.getElementById('paginaSemAssinatura').style.display = "block";
		
					$('#painelBotoes').css("display", "none");
					$('#contratoInc').css("display", "none");
					$('#descAceite').css("display", "none");
					document.getElementById('conteudoImpressao').style.display = "none";
					document.getElementById('paginaSemAssinatura').style.display = "none";
					document.getElementById('paginaJaAssinado').style.display = "block";
					
					swal({
							type: 'error',
							title: 'Esta solicita\u00e7\u00e3o j\u00e1 foi realizada.',
							showLoaderOnConfirm: true						
						});
				}
			}		
		});
}

function atualizaComAssinatura(){
	if (!document.getElementById('contratoInc').checked) {
		document.getElementById('conteudoImpressao').style.display = "none";
		document.getElementById('paginaSemAssinatura').style.display = "block";
	}else{
		document.getElementById('conteudoImpressao').style.display = "block";
		document.getElementById('paginaSemAssinatura').style.display = "none";
	}
}
</script>