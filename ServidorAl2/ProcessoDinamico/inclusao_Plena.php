<?php
require('../lib/base.php');
require('../private/autentica.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);
$queryDadosBenef  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, ENDERECO_EMAIL, PS1000.CODIGO_PARENTESCO, ';
$queryDadosBenef .= ' PS1000.SEXO, PS1000.CODIGO_PLANO, PS1000.CODIGO_TABELA_PRECO, PS1000.DATA_NASCIMENTO, PS1000.NUMERO_CPF, PS1000.DATA_ADMISSAO, PS1000.FLAG_PLANOFAMILIAR, ';
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
//$protocoloAtendimento = $numeroSequencial . '00' . date('Hi') . date('dm') . '20';

$queryEmp = 'SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA';
$resEmp = jn_query($queryEmp);
$rowEmp = jn_fetch_object($resEmp);

$protocoloAtendimento = $rowEmp->NUMERO_INSC_SUSEP . date('Y') . date('m') . date('d') . $numeroSequencial;

$planoFamiliar = $rowDadosBenef->FLAG_PLANOFAMILIAR;

$data = SqlToData($rowDadosBenef->DATA_NASCIMENTO); 
/*
$data = DateTime::createFromFormat('d/m/Y', $data);
$data->add(new DateInterval('P30D'));
$dataNas30 = $data->format('d/m/Y');
*/

$diaVencimento = '';
$valorFatura = '';

$queryPs1020  = ' SELECT TOP 1 DAY(DATA_VENCIMENTO) DIA_VENCIMENTO, VALOR_FATURA, DATA_VENCIMENTO FROM PS1020 ';
$queryPs1020 .= ' WHERE (1 = 1) ';
if($planoFamiliar == 'S'){
	$queryPs1020 .= ' AND CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
}else{
	$queryPs1020 .= ' AND CODIGO_EMPRESA = ' . aspas($rowDadosBenef->CODIGO_EMPRESA);
}
$queryPs1020 .= ' ORDER BY DATA_VENCIMENTO DESC ';

$resPs1020 = jn_query($queryPs1020);
$rowPs1020 = jn_fetch_object($resPs1020);

$diaVencimento 	= $rowPs1020->DIA_VENCIMENTO;
$dataVencimento = $rowPs1020->DATA_VENCIMENTO;
$valorFatura 	= $rowPs1020->VALOR_FATURA;

?>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<link rel="stylesheet" href="../css/sweetalert2.css">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Demonstrativo de pagamentos</title>
        <link href="css/formulariosPlena.css" media="all" rel="stylesheet" type="text/css" />
    </head>	
   <body style="text-align:left; font-size: 12px;" leftmargin="10">
		<form class="form-horizontal" name="formDefault" method="post" id="formDefault" accept-charset="utf-8" enctype="multipart/form-data" target="_self">
			<input type="hidden" id="valorFatTit" name="valorFatTit" value="<?php echo $valorFatura; ?>" ></input>
			<input type="hidden" id="valor_plano_dep2" name="valor_plano_dep2"></input>
			<input type="hidden" id="protocoloAtendimento" name="protocoloAtendimento" value="<?php echo $protocoloAtendimento; ?>"></input>
			<input type="hidden" id="numeroSequencial" name="numeroSequencial" value="<?php echo $numeroSequencial; ?>"></input>
			<div style="overflow-x: auto;overflow-y: hidden;" id="conteudoImpressao">
				<table border="1">
				  <tr>
					<td width="1060">
						<table width="100%" height="56" border="0" cellpadding="0" cellspacing="0">
							<tr align="center" valign="middle">
								<td width="50%" height="100" valign="middle" class="style13" style="text-align: left;"><img src="<?php echo file_exists('../../Site/assets/img/logo_operadora.png') ? '../../Site/assets/img/logo_operadora.png' : '../../Site/assets/img/logo_operadora.jpg';?>" width="100" height="70" style="border-radius: 8px !important; margin-left: 3px;" /></td>
								<td width="50%" align="right">
									<b>Escrit&oacute;rio Corporativo</b>
									<br>
									Av.Raimundo Pereira Magalh&atilde;es, 12.575 - S&atilde;o Paulo | SP | CEP 02989-95
								</td>									
							</tr>
						</table>
							
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="35%" height="100" valign="middle" style="text-align: center" >
									<div class="label1"><b>FORMUL&Aacute;RIO PARA INCLUS&Atilde;O</b></div>
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
						
						<div class="label1"><b>2 - Dados do requerente pela inclus&atilde;o</b> (Quando os dados forem os mesmos acima se faz necess&aacute;rio apenas o preenchimento do parentesco, telefone e e-mail) </div>
						<table width="100%" border="1" cellpadding="0" cellspacing="0">				
							<tr>					
								<td width="60%">						
									<div class="label1">Nome Completo</div>
									<input type="text" name="nome_completo" id="nome_completo" size="60" value="<?php echo $rowDadosBenef->NOME_TITULAR; ?>" ></input>
								</td>
								<td width="10%">
									<div class="label1">Parentesco</div>
									  <input type="text" name="parentesco" id="parentesco" size="10" value=""  ></input>
								</td>
								<td width="30%">
									1 - C&ocirc;njuge &nbsp; 3 - M&atilde;e &nbsp; 5 - Sogro &nbsp; 7 - Outros<br> 
									2 - Filho &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 4 - Pai &nbsp;&nbsp;&nbsp; 6 - Sogra &nbsp;&nbsp; <br>							
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
									  <input type="text" name="email_requerente" id="email_requerente" size="40" value=""  ></input>
								</td>
							</tr>
						</table>
						<br>
						
						<div class="label1"><b>3 - Dados do benefici&aacute;rio a ser inscrito</b></div>
						<table width="100%" border="1" cellpadding="0" cellspacing="0">
							<tr>
								<td width="65%">
									<div class="label1">Nome Completo</div>
									<input type="text" name="nome_dependente" id="nome_dependente" size="80" value=""  ></input>
								</td>
								<td width="35%">
									<div class="label1">Data de Nascimento</div>
									<input type="text" name="data_nasc_dep" id="data_nasc_dep" size="25" onblur="valorPlanoDep();" onkeypress="mascaraData(this)" ></input>
								</td>
							</tr>									
							<tr>
								<td width="98%" colspan=2>
									<div class="label1">Nome da M&atilde;e</div>
									<input type="text" name="nome_mae_dep" id="nome_mae_dep" size="100" value=""  ></input>
								</td>											
							</tr>
						</table>
						<table width="100%" border="1" cellpadding="0" cellspacing="0">
							<tr>
								<td width="10%">
									<div class="label1">Motivo</div>
									<input type="text" name="motivo_inc_dep" id="motivo_inc_dep" size="3" ></input>
								</td>											
								<td width="90%">
									1 - RN 
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									3 - Filho menor de 12 anos cuja a paternidade tenha sido reconhecida. &nbsp;&nbsp;&nbsp;<br> 
									2 - Ado&ccedil;&atilde;o (Guarda ou Tutela) &nbsp;&nbsp;&nbsp;&nbsp; 4 - Outros(<input type="text" name="motivo_benef_dep" id="motivo_benef_dep" size="10"></input>) &nbsp;&nbsp;<br>							
								</td>		
							</tr>
						</table>
						
						<br>
						
						<div class="label1"><b>4 - Forma&ccedil;&atilde;o do Valor</b></div>								
						<table width="100%" height="50" border="1" cellpadding="0" cellspacing="0">
							<tr>
								<td width="40%">	
									A inclus&atilde;o est&aacute; dentro do prazo de 30 dias (Corridos)? <br>
									<input class="ace" type="radio" id="diasCorridos" name="diasCorridos"><span class="lbl">&nbsp;&nbsp;Sim</span>
									<input class="ace" type="radio" id="diasCorridos" name="diasCorridos"><span class="lbl">&nbsp;&nbsp;N&atilde;o</span>
									<br>
									1 - Assumir&aacute; a redu&ccedil;&atilde;o de car&ecirc;ncia contratual do plano;<br>
									2 - Assumir&aacute; a car&ecirc;ncia contratual;												
								</td>	
								<td width="60%">
									Data de nascimento + 30 dias (Corridos):
									<label name="inputDtNascimento" id="inputDtNascimento"></label><br>
									* A data fim ser&aacute; o per&iacute;odo que o benefici&aacute;rio estar&aacute; coberto pelo plano do titular.
								</td>									
							 </tr>
						</table>
						<table width="100%" height="50" border="1" cellpadding="0" cellspacing="0">
							<tr>
								<td width="60%">	
									Vencimento da fatura: <?php echo $diaVencimento; ?><br>
									Valor do plano atual: <?php echo toMoeda($valorFatura); ?><br>
									Valor do plano referente a nova inclus&atilde;o: <label type="text" id="valor_plano_dep"></label><br>												
									Taxa ADM (Carteirinha): R$ 0,00<br>
									Dias fora da Cobertura de: <br>
									<input type="text" name="dias_fora_cobert_inic" id="dias_fora_cobert_inic" size="10"></input>
									a
									<input type="text" name="dias_fora_cobert_final" id="dias_fora_cobert_final" size="10" onBlur="calculaIntervaloData();"></input>
									= <input type="text" name="quant_dias_cobert" id="quant_dias_cobert" size="6" onBlur="calculaValor();"></input> dias = R$ <input type="text" name="valor_fora_cobert" id="valor_fora_cobert" size="10" onblur="atualizaTotal();"></input>
									<br>
								</td>	
								<td width="40%" align="center">
									<b>Valor total:</b>
									<br>&nbsp;
									<br><label id="valorTotal"/>
									<br>&nbsp;
									<br>&nbsp;
									<br>&nbsp;
								</td>									
							 </tr>
							 <tr>
								<td colspan="2">
									* Dias fora da cobertura, &eacute; o per&iacute;odo ap&oacute;s os 30 dias de nascimento, ado&ccedil;&atilde;o ou reconhecimento da paternidade, at&eacute; o pr&oacute;ximo vencimento da fatura, per&iacute;odo em que o benefici&aacute;rio n&atilde;o est&aacute; coberto pelo plano;<br>
									* Este valor ser&aacute; cobrado na pr&oacute;xima mensalidade, as demais mensalidades n&atilde;o ser&atilde;o cobradas a taxa ADM e os dias fora da cobertura.
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
											<input id="boletoAberto" name="boletoAberto" type="radio" />N&atilde;o	
										</td>
										<td width="50%" height="50">					
											Caso positivo, quantos e quais os meses?	<br>
											<input type="text" name="dias_boleto_aberto" id="dias_boleto_aberto" size="60"></input>
										</td>
									 </tr>
								</td>
							</tr>
						</table>
						<br>									
						<div class="label1"><b>Data da solicita&ccedil;&atilde;o da inclus&atilde;o</b></div>				
						<?php echo date('d/m/Y'); ?>
						<br>	
						<hr/>
						<input class="ace" type="checkbox" id="contratoInc"><span class="lbl">&nbsp;&nbsp;Declaro a veracidade das informa&ccedil;&otilde;es prestadas neste formul&aacute;rio e autorizo o processamento de inclus&atilde;o.</span>
							
					</td>
				  </tr>
				</table>
			</div>
			
			<br />
			<div class="formDefault_Conteudo_linha">
				<div class="formDefault_Conteudo_linha_Label">
					<label for="arquivosAnexar" class="labelDefault_Req">Selecione os arquivos a serem enviados:</label>
				</div>
			</div>
			<br />
			<div class="formDefault_Conteudo_linha">
				<div class="formDefault_Conteudo_linha_Label">
					<label for="arquivosAnexar" class="labelDefault_Req">CNS </label>
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
					<label for="arquivosAnexar" class="labelDefault_Req">RG ou CNH do solicitante pela inclus&atilde;o</label>
				</div>							
				<input name="arquivoRG" type="file" id="arquivoRG" size="35">
				<div class="clareador">&nbsp;</div>
			</div>
			<br />
			<div class="formDefault_Rodape" id="painelBotoes">				
				<input type='button' name='btnImprimir' id='btnImprimir' class="btn btn-info" value='Imprimir' onclick="printDiv('conteudoImpressao')"/>
				<input type='button' class="btn btn-success" onclick="salvaInclusaoDep(); " name="btnConcluir" id="btnConcluir"  value="Gravar" />
				<?php
					
					//echo '<input class="btn btn-success" onclick="gerarPdf(); " name="buttonGeraPdf" id="buttonGeraPdf"  value="Gera PDF" />';								
				
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

var valorTotal = 0;
var valorTaxa  = 0;
var valorCobertura = 0;

function salvaInclusaoDep(){
	
	enderecoEmail 			= $('#email_requerente').val();	
	cpf_requerente 			= $('#cpf_requerente').val();	
	telefone_requerente 	= $('#telefone_requerente').val();	
	celular_requerente 		= $('#celular_requerente').val();	
	numeroTel 				= $('#numero_telefone').val();	
	dtNascDep 				= $('#data_nasc_dep').val();
	arquivo 				= $('#arquivoCNS').val();
	protocoloAtendimento 	= $('#protocoloAtendimento').val();	
	numeroRegistro 			= $('#numeroSequencial').val();		
	
	
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
	
	
	if (!document.getElementById('contratoInc').checked) {
		swal({
			type: 'error',
			title: 'Para continuar, favor declarar ci\u00eancia das informa\u00e7\u00f5es.'
		})
		return false;
	}
	
	if(maior_de_idade(dtNascDep)){		
		swal({
			type: 'error',
			title: 'Esta tela deve ser utilizada apenas para dependentes menores de idade. Favor entrar em contato com a operadora! '
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
	
	if(arquivo == '' || arquivo == 'undefined'){
		swal({
			type: 'error',
			title: 'Favor inserir o anexo.'
		})
		return false;
	}
	
	$.ajax({
			type: "GET",
			dataType: "html",
			url: "operacoesProcessos.php?op=inclusaoDep",
			data: 	"enderecoEmail="      	+ enderecoEmail 		+ '&' +
					"protocoloAtendimento=" + protocoloAtendimento 		+ '&' +
					"numeroRegistro=" 		+ numeroRegistro 		+ '&' +
					"numeroTel="      		+ numeroTel,
			complete: function(){
				//Carregando(null, 'hide');
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
						funcaoVoltar();
					});
			},
			beforeSend: function(){
				//Carregando(null, 'show');
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

function valorPlanoDep(){	
	dtNascDep = $('#data_nasc_dep').val();
	valorFatTit = $('#valorFatTit').val();		

	if(maior_de_idade(dtNascDep)){
		swal({
			type: 'error',
			title: 'Esta tela deve ser utilizada apenas para dependentes menores de idade. Favor entrar em contato com a operadora! '
		})
		return false;
	}
	
	var dtNascTrinta = adicionarDiasData(dtNascDep, 29);
	$("#inputDtNascimento").html(dtNascTrinta);
	
	$.ajax({
		type: "GET",
		dataType: "html",
		url: "operacoesProcessos.php?op=buscaValor",
		data: 	"dtNascDep=" + dtNascDep,
		complete: function(){
			//Carregando(null, 'hide');
		},
		success: function(msg){
			//Carregando(null, 'hide');
			$("#valor_plano_dep").html('R$ ' + msg);
			$("#valor_plano_dep2").val(msg);
			
			valorTotal = (valorTaxa + parseFloat(msg) + parseFloat(valorFatTit));
			valorTotal = formataDinheiro(valorTotal);
			
			$("#valorTotal").html(valorTotal);			
			return true;
		},
		beforeSend: function(){
		}
	});
		
}

function calculaIntervaloData(){
	data1 = $("#dias_fora_cobert_inic").val();
	data2 = $("#dias_fora_cobert_final").val();
	
	var dataInicial = new Date();
    var dadosDataI = data1.split('/');

    dataInicial.setHours(0, 0, 0, 0);
    dataInicial.setFullYear(dadosDataI[2],(dadosDataI[1]- 1),dadosDataI[0]);
	
	var dataFinal = new Date();
    var dadosDataF = data2.split('/');
	
	dataFinal.setHours(0, 0, 0, 0);
    dataFinal.setFullYear(dadosDataF[2],(dadosDataF[1]- 1),dadosDataF[0]);

	dias = days_between(dataFinal, dataInicial);
	$("#quant_dias_cobert").val(dias+1);	
}

function atualizaTotal(){		
	valorForaCobert = valorCobertura;
		
	valorTotalAtu = valorTotal.replace(',', '.');
	valorTotalAtu = valorTotalAtu.replace('R$', '');
	valorTotalAtu = valorTotalAtu.replace(' ', '');	
	
	newValorTotal = (parseFloat(valorTotalAtu) + parseFloat(valorForaCobert));	
	$("#valorTotal").html(formataDinheiro(newValorTotal));
	
}

function calculaValor(){	

	dataNasc = $("#data_nasc_dep").val();
	if(!dataNasc){
		alert('Para o valor ser calculado, preencha a data de nascimento');
		return false;
	}
	
	valor = $("#valor_plano_dep2").val();	
	valor = (parseFloat(valor) / 30);	
	dias = $("#quant_dias_cobert").val();
	valorForaCobert = (valor * dias);	

	$("#valor_fora_cobert").val(formataDinheiro(valorForaCobert));
	valorCobertura = valorForaCobert;	
	atualizaTotal();
}

function formataDinheiro(n) {
	return "R$ " + n.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
}

function days_between(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime()
    var date2_ms = date2.getTime()

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1_ms - date2_ms)

    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY)

}

function years_between(ADataA, ADataB){
    var days = days_between(ADataA, ADataB);
    return parseInt(days/365);
}

function maior_de_idade(AdataA){
    if(AdataA == ''){
      return true;
    }
    var today = new Date();
    today.setHours(0, 0, 0, 0);

    var dataNacimento = new Date();
    var dadosDataN = AdataA.split('/');

    dataNacimento.setHours(0, 0, 0, 0);
    dataNacimento.setFullYear(dadosDataN[2],(dadosDataN[1]- 1),dadosDataN[0]);
    var idade = years_between(today, dataNacimento);
    return idade >= 18 ? true : false;
}

function adicionarDiasData(data, dias){
		
	var dtSplit = data.split("/");
	var diaNascimento = dtSplit[0];
	var mesNascimento = (dtSplit[1] - 1);
	var anoNascimento = dtSplit[2];
	
	var dataNascimento = new Date(anoNascimento, mesNascimento, diaNascimento);	
	var dataVenc    = new Date(dataNascimento.getTime() + (dias * 24 * 60 * 60 * 1000));
	
	var diaFinal = ("0" + dataVenc.getDate()).slice(-2);
	var mesFinal = ("0" + (dataVenc.getMonth() + 1)).slice(-2);
	var anoFinal = dataVenc.getFullYear();
	
	//return dataVenc.getDate() + "/" + (dataVenc.getMonth() + 1) + "/" + dataVenc.getFullYear();
	return diaFinal + "/" + mesFinal + "/" + anoFinal;
}

function funcaoVoltar(){
	setTimeout(window.history.back(), 250000);	
}

function gerarPdf(){
	let options = {};
	  options = {
		useCORS: true,
		foreignObjectRendering: true
	  };  
		
	html2canvas(element, options).then(function(canvas) {
		var dataURL = canvas.toDataURL(); 
		var type = 'inclusao';
		//console.log(dataURL);
		var xhr = new XMLHttpRequest();
        xhr.open( 'POST', 'upload_pdf_ftp_jquery.php', true ); //Post to php Script to save to server
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("dados="+dataURL+"&type="+type);
	});	
		
}

	
function mascaraData(val) {
  var pass = val.value;
  var expr = /[0123456789]/;

  for (i = 0; i < pass.length; i++) {
    // charAt -> retorna o caractere posicionado no índice especificado
    var lchar = val.value.charAt(i);
    var nchar = val.value.charAt(i + 1);

    if (i == 0) {
      // search -> retorna um valor inteiro, indicando a posição do inicio da primeira
      // ocorrência de expReg dentro de instStr. Se nenhuma ocorrencia for encontrada o método retornara -1
      // instStr.search(expReg);
      if ((lchar.search(expr) != 0) || (lchar > 3)) {
        val.value = "";
      }

    } else if (i == 1) {

      if (lchar.search(expr) != 0) {
        // substring(indice1,indice2)
        // indice1, indice2 -> será usado para delimitar a string
        var tst1 = val.value.substring(0, (i));
        val.value = tst1;
        continue;
      }

      if ((nchar != '/') && (nchar != '')) {
        var tst1 = val.value.substring(0, (i) + 1);

        if (nchar.search(expr) != 0)
          var tst2 = val.value.substring(i + 2, pass.length);
        else
          var tst2 = val.value.substring(i + 1, pass.length);

        val.value = tst1 + '/' + tst2;
      }

    } else if (i == 4) {

      if (lchar.search(expr) != 0) {
        var tst1 = val.value.substring(0, (i));
        val.value = tst1;
        continue;
      }

      if ((nchar != '/') && (nchar != '')) {
        var tst1 = val.value.substring(0, (i) + 1);

        if (nchar.search(expr) != 0)
          var tst2 = val.value.substring(i + 2, pass.length);
        else
          var tst2 = val.value.substring(i + 1, pass.length);

        val.value = tst1 + '/' + tst2;
      }
    }

    if (i >= 6) {
      if (lchar.search(expr) != 0) {
        var tst1 = val.value.substring(0, (i));
        val.value = tst1;
      }
    }
  }

  if (pass.length > 10)
    val.value = val.value.substring(0, 10);
  return true;
}
</script>