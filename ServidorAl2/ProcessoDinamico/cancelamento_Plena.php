<?php
require('../lib/base.php');
require('../private/autentica.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

$queryDadosBenef  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, ENDERECO_EMAIL, PS1000.CODIGO_PARENTESCO, ';
$queryDadosBenef .= ' PS1000.SEXO, PS1000.DATA_NASCIMENTO, PS1000.NUMERO_CPF, PS1000.DATA_ADMISSAO, ';
$queryDadosBenef .= ' T.CODIGO_ASSOCIADO CODIGO_TITULAR, T.NOME_ASSOCIADO NOME_TITULAR, T.SEXO SEXO_TITULAR, T.NOME_MAE, T.CODIGO_ESTADO_CIVIL, ';
$queryDadosBenef .= ' T.DATA_NASCIMENTO DATA_NASCIMENTO_TITULAR, T.NUMERO_CPF CPF_TITULAR, T.DATA_ADMISSAO DATA_ADMISSAO_TITULAR, ';
$queryDadosBenef .= ' F.CODIGO_AREA AREA_FIXO, F.NUMERO_TELEFONE NUMERO_FIXO, C.CODIGO_AREA AREA_CELULAR, C.NUMERO_TELEFONE NUMERO_CELULAR ';
$queryDadosBenef .= ' FROM PS1000';
$queryDadosBenef .= ' INNER JOIN PS1000 T ON (T.CODIGO_ASSOCIADO = PS1000.CODIGO_TITULAR)';
$queryDadosBenef .= ' INNER JOIN PS1001 ON (PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)';
$queryDadosBenef .= ' LEFT OUTER JOIN PS1006 F ON (F.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO AND F.INDICE_TELEFONE = 1)';
$queryDadosBenef .= ' LEFT OUTER JOIN PS1006 C ON (C.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO AND C.INDICE_TELEFONE = 2)';
$queryDadosBenef .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);

$resDadosBenef = jn_query($queryDadosBenef);
$rowDadosBenef = jn_fetch_object($resDadosBenef);

$numeroSequencial = jn_gerasequencial('PS6110');

$queryEmp = 'SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA';
$resEmp = jn_query($queryEmp);
$rowEmp = jn_fetch_object($resEmp);

$protocoloAtendimento = $rowEmp->NUMERO_INSC_SUSEP . date('Y') . date('m') . date('d') . $numeroSequencial;

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$tipoModelo = substr($_SERVER['HTTP_USER_AGENT'],0,100); 
$assinatura = "Assinado eletronicamente mediante login/senha por ".$rowDadosBenef->NOME_ASSOCIADO. ", "."em ".strftime('%A, %d de %B de %Y as %H:%M:%S', strtotime('now'))."\n"."atrav&eacute;s  do ".$tipoModelo."\n"."IP:".$_SERVER["REMOTE_ADDR"];

?>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<link rel="stylesheet" href="../css/sweetalert2.css">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Formulário de Cancelamento</title>
        <link href="css/formulariosPlena.css" media="all" rel="stylesheet" type="text/css" />
    </head>	
   <body style="text-align:left; font-size: 12px;" leftmargin="10">
		<form class="form-horizontal" name="formDefault" method="post" id="formDefault" enctype="multipart/form-data" target="_self">
			<input type="hidden" id="protocoloAtendimento" name="protocoloAtendimento" value="<?php echo $protocoloAtendimento; ?>"></input>						
			<input type="hidden" id="numeroSequencial" name="numeroSequencial" value="<?php echo $numeroSequencial; ?>"></input>						
			<input type="hidden" id="assinatura" name="assinatura" value="<?php echo $assinatura; ?>"></input>						
			<div style="overflow-x: auto;overflow-y: hidden;" id="conteudoImpressao">
				<table border="1" id="tabelaPdf">
					<tr>
					<td width="1060">
						<table width="100%" height="56" border="0" cellpadding="0" cellspacing="0">
							<tr align="center" valign="middle">
								<td width="50%" height="100" valign="middle" class="style13" style="text-align: left;"><img src="<?php echo file_exists('../../Site/assets/img/logo_operadora.png') ? '../../Site/assets/img/logo_operadora.png' : '../../Site/assets/img/logo_operadora.jpg';?>" width="100" height="70" style="border-radius: 8px !important; margin-left: 3px;" /></td>
								<td width="50%" align="right">
									Escrit&oacute;rio Corporativo
									<br>
									Av.Raimundo Pereira Magalh&atilde;es, 12.575 - S&atilde;o Paulo | SP | CEP 02989-95
								</td>									
							</tr>
						</table>
							
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="35%" height="100" valign="middle" style="text-align: center" >
									<div class="label1"><b>FORMUL&Aacute;RIO PARA CANCELAMENTO DO CONTRATO</b></div>
								</td>
							</tr>
						</table>
													
						<div class="label1"><b>1 - Dados do titular</b></div>
						<table width="100%" border="1" cellpadding="0" cellspacing="0">				
							<tr>					
								<td width="20%">						
									<div class="label1">C&oacute;digo Benefici&aacute;rio</div>
									<input type="text" name="codigo_associado" id="codigo_associado" size="20" value="<?php echo $rowDadosBenef->CODIGO_TITULAR; ?>"></input>
								</td>
								<td width="80%">
									<div class="label1">Nome Completo</div>
									  <input type="text" name="nome_associado" id="nome_associado" size="100" value="<?php echo $rowDadosBenef->NOME_TITULAR; ?>"></input>
								</td>
							</tr>				
						</table>			
						
						<table width="100%" border="1" cellpadding="0" cellspacing="0">	
							<tr>					
								<td width="5%">						
									<div class="label1">Sexo</div>
									<input type="text" name="sexo" id="sexo" size="5" value="<?php echo $rowDadosBenef->SEXO_TITULAR; ?>" ></input>
								</td>
								<td width="5%">
									1 - Masculino <br> 
									2 - Feminino	
								</td>
								<td width="10%">
									<div class="label1">Data Nascimento</div>
									  <input type="text" name="data_nascimento" id="data_nascimento" size="10" value="<?php echo SqlToData($rowDadosBenef->DATA_NASCIMENTO_TITULAR) ?>"  ></input>
								</td>		
								<td width="5%">
									<div class="label1">Estado Civil</div>
									  <input type="text" name="estado_civil" id="estado_civil" size="5" value="<?php echo $rowDadosBenef->CODIGO_ESTADO_CIVIL; ?>"  ></input>
								</td>	
								<td width="10%">
									1 - Solteiro &nbsp; 3 - Vi&uacute;vo<br> 
									2 - Casado &nbsp; 4 - Outros							
								</td>	
								<td width="15%">
									<div class="label1">CPF</div>
									  <input type="text" name="numero_cpf" id="numero_cpf" size="15" value="<?php echo $rowDadosBenef->CPF_TITULAR; ?>"  ></input>
								</td>	
								<td width="10%">
									<div class="label1">Data Admissao</div>
									  <input type="text" name="data_admissao" id="data_admissao" size="15" value="<?php echo SqlToData($rowDadosBenef->DATA_ADMISSAO_TITULAR); ?>"  ></input>
								</td>							
							</tr>	
						</table>
						
						<table width="100%" border="1" cellpadding="0" cellspacing="0">	
							<tr>
								<td width="65%">
									<div class="label1">Nome da M&atilde;e</div>
									  <input type="text" name="nome_mae" id="nome_mae" size="80" value="<?php echo $rowDadosBenef->NOME_MAE; ?>"  ></input>
								</td>	
								<td width="35%">
									<div class="label1">CPF da M&atilde;e</div>
									  <input type="text" name="cpf_mae" id="cpf_mae" size="25" value="<?php echo $rowDadosBenef->CPF_MAE; ?>"  ></input>
								</td>	
							</tr>
						</table>
						<br>
						
						<div class="label1"><b>2 - Dados do Requerente Pelo Cancelamento do Contrato</b></div>
						<table width="100%" border="1" cellpadding="0" cellspacing="0">				
							<tr>					
								<td width="60%">						
									<div class="label1">Nome Completo</div>
									<input type="text" name="nome_completo" id="nome_completo" size="60" value="" ></input>
								</td>
								<td width="10%">
									<div class="label1">Parentesco</div>
									  <input type="text" name="parentesco" id="parentesco" size="10" value=""  ></input>
								</td>
								<td width="30%">
									1 - C&ocirc;njuge &nbsp; 3 - M&atilde;e &nbsp; 5 - Sogro &nbsp; 7 - Tutelado<br> 
									2 - Filho &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 4 - Pai &nbsp;&nbsp;&nbsp; 6 - Sogra &nbsp;&nbsp; 8 - Outros <br>							
								</td>						
							</tr>				
						</table>	
						
						<table width="100%" border="1" cellpadding="0" cellspacing="0">	
							<tr>
								<td width="15%">
									<div class="label1">CPF</div>
									  <input type="text" name="cpf_requerente" id="cpf_requerente" size="15" value=""  ></input>
								</td>	
								<td width="15%">
									<div class="label1">Telefone(com DDD)</div>
									  <input type="text" name="telefone_requerente" id="telefone_requerente" size="15" value=""  ></input>
								</td>					
								<td width="15%">
									<div class="label1">Telefone Celular(com DDD)</div>
									  <input type="text" name="celular_requerente" id="celular_requerente" size="15" value=""  ></input>
								</td>	
								<td width="19%">
									<div class="label1">E-mail</div>
									  <input type="text" name="email_requerente" id="email_requerente" size="40" value=""  />
								</td>					
							</tr>
						</table>	
						<br>
						
						<div class="label1"><b>3 - Motivo do Cancelamento do Contrato</b></div>
						<table width="100%" border="1" cellpadding="0" cellspacing="0">				
							<tr>					
								<td width="10%">						
									<div class="label1">Motivo</div>
									<input type="text" name="motivo_cancelamento" id="motivo_cancelamento" size="1"></input>
								</td>					
								<td width="30%">
									1 - Insatisfa&ccedil;&atilde;o &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  4 - Falecimento <br> 
									2 - Mudan&ccedil;a de Estado &nbsp;&nbsp;&nbsp; 5 - Motivo Financeiro <br>	
									3 - Mudan&ccedil;a de plano  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 6 - Outros;				
								</td>						
							</tr>				
						</table>				
						
						<table width="100%" height="56" border="0" cellpadding="0" cellspacing="0" style="background-color:yellow;">
							<tr align="center" valign="middle">
								
								<td width="100%" p align="justify">
									<b>
									* A solicita&ccedil;&atilde;o de cancelamento do contrato n&atilde;o admite desist&ecirc;ncia a partir da ci&ecirc;ncia da operadora; 
									<br>
									* O pedido de cancelamento tem <b><font color="red">EFEITO IMEDITATO</font></b>, a partir da ciencia da operadora. Ap&oacute;s a data e hor&aacute;rio da solicita&ccedil;&atilde;o de cancelamento do contrato, os benefici&aacute;rios
									n&atilde;o estar&atilde;o mais cobertos pelo plano de sa&uacute;de e/ou odontol&oacute;gico. Qualquer utiliza&ccedil;&atilde;o do plano(ex.exame, cirurgia, etc), mesmo que j&aacute; autorizada pela operadora, n&atilde;o 
									ser&aacute; realizado ap&oacute;s a solicita&ccedil;&atilde;o de cancelamento do contrato.						
									<br>
									* O encerramento do contrato prev&ecirc;, cumprimento de novas car&ecirc;ncias, perda do direito &agrave; portabilidade de car&ecirc;ncias e preenchimento de nova declara&ccedil;&atilde;o de sa&uacute;de.
									</b> 
								</td>									
							</tr>
						</table>			
						<br>
						
						<hr/>
						<div class="label1"><b>4 - Reten&ccedil;&atilde;o da Carterinha</b></div>
					
						<table width="100%" height="50" border="1" cellpadding="0" cellspacing="0">
							<tr>
								<td width="20%">	
									<input id="flagCarteirinha" name="flagCarteirinha" type="radio" checked/>Sim	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;													
									<input id="flagCarteirinha" name="flagCarteirinha" type="radio"/>N&atilde;o	
								</td>	
								<td width="50%">
									Caso negativo, qual o motivo?
									<input type="text" name="motivo_carteirinha" id="motivo_carteirinha" size="45"></input>
								</td>									
							 </tr>
						</table>
						<br>
						
						<div class="label1"><b>5 - Comprovante de Atendimento</b></div>
						
						<table width="100%" height="50" border="1" cellpadding="0" cellspacing="0">
							<tr>
								<td width="25%">					
									Protocolo de Atendimento
									<br><?php echo $protocoloAtendimento; ?>
								</td>
								<td width="25%" >
									Atendente
									<br>ONLINE&nbsp;&nbsp;						
								</td>	
								<td width="25%" >
									Data
									<br><?php echo date('d/m/Y'); ?>
								</td>	
								<td width="25%" >
									Hora
									<br><?php echo date('H:i'); ?>
								</td>
							 </tr>
						</table>	

						<table width="100%" border="1" cellpadding="0" cellspacing="0">	
							<tr>					
								<td width="20%">						
									<div class="label1">Titular e dependentes com boletos em aberto</div>						
									<tr>
										<td width="20%">	
											<input id="boletoAberto" name="boletoAberto" type="radio"/>Sim	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;													
											<input id="boletoAberto" name="boletoAberto" type="radio"/>N&atilde;o	
										</td>
										<td width="50%" height="50">					
											Caso positivo, quantos e quais os meses?&nbsp;	
											<input type="text" name="meses_boletos" id="meses_boletos" size="45"></input>
										</td>
									 </tr>
								</td>
							</tr>
						</table>
									
						<table width="100%" height="56" border="0" cellpadding="0" cellspacing="0">
							<tr align="center" valign="middle">
								
								<td width="100%" p align="justify">
									* As mensalidades vencidas ou eventuais multas contratuais, s&atilde;o de responsabilidade do benefici&aacute;rio;
									<br>
									* Eventuais utiliza&ccedil;&otilde;es ap&oacute;s a solicita&ccedil;&atilde;o de cancelamento &eacute; de responsabilidade do benefici&aacute;rio;
									<br>
									* D&eacute;bitos pendentes com a operadora ser&atilde;o cobrados ap&oacute;s a entrega do comprovante de efetivo cancelamento do contrato, de modo a n&atilde;o retardar o cancelamento.						
								</td>									
							</tr>
						</table>		
						<br>
						<br>
						
						<hr/>
						<div class="label1"><b>Data da solicita&ccedil;&atilde;o do cancelamento</b></div>				
						<?php echo date('d/m/Y'); ?>
						<br>							
						<hr/>
						<div class="label1"><b>Dados Assinatura </b></div>				
						<?php echo $assinatura; ?>
						<br>	
						<hr/>
						<input class="ace" type="checkbox" id="contrato"><span class="lbl">&nbsp;&nbsp;Declaro a veracidade das informa&ccedil;&otilde;es prestadas neste formul&aacute;rio e autorizo o processamento do cancelamento do contrato na operadora Plena Sa&uacute;de Ltda.</span>
							
					</td>
				  </tr>
				</table>
			</div>						
		
			<br />
			<br />
			<div class="formDefault_Conteudo_linha">
				<div class="formDefault_Conteudo_linha_Label">
					<label for="arquivosAnexar" class="labelDefault_Req">CNS (Cart&atilde;o Nacional do SUS); </label>
				</div>
				<input name="arquivoCNS" type="file" id="arquivoCNS" size="35">
				<div class="clareador">&nbsp;</div>
			</div>
			<div class="formDefault_Conteudo_linha">
				<div class="formDefault_Conteudo_linha_Label">
					<label for="arquivosAnexar" class="labelDefault_Req">Certid&atilde;o de Nascimento</label>
				</div>
				<input name="arquivoCertidao" type="file" id="arquivoCertidao" size="35">
				<div class="clareador">&nbsp;</div>
			</div>
			<div class="formDefault_Conteudo_linha">
				<div class="formDefault_Conteudo_linha_Label">
					<label for="arquivosAnexar" class="labelDefault_Req">CPF</label>
				</div>
				<input name="arquivoCPF" type="file" id="arquivoCPF" size="35">
				<div class="clareador">&nbsp;</div>
			</div>
			<div class="formDefault_Conteudo_linha">
				<div class="formDefault_Conteudo_linha_Label">
					<label for="arquivosAnexar" class="labelDefault_Req">RG ou CNH</label>
				</div>							
				<input name="arquivoRG" type="file" id="arquivoRG" size="35">
				<div class="clareador">&nbsp;</div>
			</div>
			<br />
			
			<div class="formDefault_Rodape" id="painelBotoes">				
				<input type='button' name='btnImprimir' id='btnImprimir' class="btn btn-info" value='Imprimir' onclick="printDiv('conteudoImpressao')"/>
				<input type='button' class="btn btn-success" onclick="salvaCancelamento(); " name="btnConcluir" id="btnConcluir"  value="Gravar" />
				<?php
				
					// echo '<input class="btn btn-success" onclick="gerarPdf(); " name="buttonGeraPdf" id="buttonGeraPdf"  value="Gera PDF" />';								
				
				?>							
			</div>    
		</form>
   </body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js" integrity="sha384-THVO/sM0mFD9h7dfSndI6TS0PgAGavwKvB5hAxRRvc0o9cPLohB0wb/PTA7LdUHs" crossorigin="anonymous"></script>
<script>

	
var element = $("#conteudoImpressao"); // global variable
var getCanvas ; // global variable

function salvaCancelamento(){
	
	enderecoEmail 			= $('#email_requerente').val();	
	cpf_requerente 			= $('#cpf_requerente').val();	
	telefone_requerente 	= $('#telefone_requerente').val();	
	celular_requerente 		= $('#celular_requerente').val();	
	numeroTel 				= $('#celular_requerente').val();
	arquivo 				= $('#arquivoCNS').val();
	arquivo1 				= $('#arquivoCertidao').val();
	arquivo2 				= $('#arquivoCPF').val();
	arquivo3 				= $('#arquivoRG').val();
	protocoloAtendimento 	= $('#protocoloAtendimento').val();	
	numeroRegistro 			= $('#numeroSequencial').val();		
	assinatura 				= $('#assinatura').val();		
	
	var formData = new FormData(formDefault);

    $.ajax({
        url: "upload_arquivos_frm_Plena.php",
        type: "POST",
        data: formData,
        success: function (data) {
            //alert(data)
        },
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {  // Custom XMLHttpRequest
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
                myXhr.upload.addEventListener('progress', function () {
                    /* faz alguma coisa durante o progresso do upload */
                }, false);
            }
        return myXhr;
        }
    });
	
	if (!document.getElementById('contrato').checked) {
		swal({
			type: 'error',
			title: 'Para continuar, favor declarar ci\u00eancia das informa\u00e7\u00f5es.'
		})
		return false;
	}
	
	if(enderecoEmail == '' || enderecoEmail == 'undefined'){
		swal({
			type: 'error',
			title: 'Favor inserir um e-mail v\u00e1lido.'
		})
		return false;
	}
	
	if	(	
			cpf_requerente == '' 		|| cpf_requerente == 'undefined' 		|| 	
			telefone_requerente == '' 	|| telefone_requerente == 'undefined' 	|| 	
			celular_requerente == '' 	|| celular_requerente == 'undefined' 
		){
		swal({
			type: 'error',
			title: 'Favor inserir todas as informa\u00e7\u00f5es do requerente. '
		})
		return false;
	}
	
	if(arquivo == '' || arquivo == 'undefined' || arquivo1 == '' || arquivo1 == 'undefined' || arquivo2 == '' || arquivo2 == 'undefined' || arquivo3 == '' || arquivo3 == 'undefined'){
		swal({
			type: 'error',
			title: 'Favor inserir todos os anexos.'
		})
		return false;
	}	
	
	$.ajax({
			url: "operacoesProcessos.php?op=cancelamentoPlenaPF",
			type: "GET",			
			data: 	"enderecoEmail="      	+ enderecoEmail 		+ '&' +
					"protocoloAtendimento=" + protocoloAtendimento 		+ '&' +
					"numeroRegistro=" 		+ numeroRegistro 		+ '&' +
					"assinatura=" 			+ assinatura 		+ '&' +
					"numeroTel="      		+ numeroTel,
			complete: function(){				
			},
			success: function(msg){
				gerarPdf();
				swal({
						type: 'success',
						title: 'Solicitacao de cancelamento encaminhada para operadora. Protocolo: ' + msg,
						showLoaderOnConfirm: true,
						preConfirm: () => {
							
						}
					}).then((value) => {					  
						funcaoVoltar();
					});
				return true;
			},
			beforeSend: function(){				
			}
		});
}


function printDiv(divID) {
        var divElements = document.getElementById(divID).innerHTML;
        var oldPage = document.body.innerHTML;
        document.body.innerHTML = 
          "<html><head><title></title></head><body style='margin:20px;'>" + 
          divElements + "</body>";

        window.print();

        document.body.innerHTML = oldPage;
}

function gerarPdf(){
	let options = {};
	  options = {
		useCORS: true,
		foreignObjectRendering: true,
		width: 1300,
		height: 3100
	  };  
		
	html2canvas(element, options).then(function(canvas) {
		var dataURL = canvas.toDataURL(); 
		var type = 'cancelamento';
		//console.log(dataURL);
		var xhr = new XMLHttpRequest();
        xhr.open( 'POST', 'upload_pdf_ftp_jquery.php', true ); //Post to php Script to save to server
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("dados="+dataURL+"&type="+type);		        
	});	
		
}

function funcaoVoltar(){
	setTimeout(window.history.back(), 250000);	
}
</script>