<?php
require('../lib/base.php');
require('../private/autentica.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

$queryDadosBenef  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, ENDERECO_EMAIL, PS1000.CODIGO_PARENTESCO, PS1000.TIPO_ASSOCIADO, ';
$queryDadosBenef .= ' PS1000.SEXO, PS1000.DATA_NASCIMENTO, PS1000.NUMERO_CPF, PS1000.DATA_ADMISSAO, ';
$queryDadosBenef .= ' T.CODIGO_ASSOCIADO CODIGO_TITULAR, T.NOME_ASSOCIADO NOME_TITULAR, T.SEXO SEXO_TITULAR, T.NOME_MAE, T.CODIGO_ESTADO_CIVIL, ';
$queryDadosBenef .= ' T.DATA_NASCIMENTO DATA_NASCIMENTO_TITULAR, T.NUMERO_CPF CPF_TITULAR, T.DATA_ADMISSAO DATA_ADMISSAO_TITULAR, ';
$queryDadosBenef .= ' F.CODIGO_AREA AREA_FIXO, F.NUMERO_TELEFONE NUMERO_FIXO, C.CODIGO_AREA AREA_CELULAR, C.NUMERO_TELEFONE NUMERO_CELULAR ';
$queryDadosBenef .= ' FROM PS1000';
$queryDadosBenef .= ' INNER JOIN PS1000 T ON (T.CODIGO_ASSOCIADO = PS1000.CODIGO_TITULAR)';
$queryDadosBenef .= ' LEFT OUTER JOIN PS1001 ON (PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO)';
$queryDadosBenef .= ' LEFT OUTER JOIN PS1006 F ON (F.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO AND F.INDICE_TELEFONE = 1)';
$queryDadosBenef .= ' LEFT OUTER JOIN PS1006 C ON (C.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO AND C.INDICE_TELEFONE = 2)';
$queryDadosBenef .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);

$resDadosBenef = jn_query($queryDadosBenef);
$rowDadosBenef = jn_fetch_object($resDadosBenef);

$tpAssociado = $rowDadosBenef->TIPO_ASSOCIADO;
$numeroSequencial = jn_gerasequencial('PS6110');
//$protocoloAtendimento = $numeroSequencial . '00' . date('Hi') . date('dm') . '20';

$queryEmp = 'SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA';
$resEmp = jn_query($queryEmp);
$rowEmp = jn_fetch_object($resEmp);

$protocoloAtendimento = $rowEmp->NUMERO_INSC_SUSEP . date('Y') . date('m') . date('d') . $numeroSequencial;

$queryDep  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO FROM PS1000 ';
$queryDep .= ' WHERE TIPO_ASSOCIADO ="D" AND PS1000.DATA_EXCLUSAO IS NULL ';
$queryDep .= ' 	AND PS1000.CODIGO_TITULAR = ' . aspas($_SESSION['codigoIdentificacao']);
	
$resDep = jn_query($queryDep);

$i = 0;
$ArrDep = Array();
while($rowDep=jn_fetch_object($resDep)) {
   $ArrDep[$i]['codigo'] = $rowDep->CODIGO_ASSOCIADO;
   $ArrDep[$i]['nome'] = $rowDep->CODIGO_ASSOCIADO . ' - ' . $rowDep->NOME_ASSOCIADO;
   $i++;
}		

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
		<form class="form-horizontal" name="formDefault" method="post" id="formDefault" enctype="multipart/form-data" target="_self">						
			<input type="hidden" id="protocoloAtendimento" name="protocoloAtendimento" value="<?php echo $protocoloAtendimento; ?>"></input>
			<input type="hidden" id="numeroSequencial" name="numeroSequencial" value="<?php echo $numeroSequencial; ?>"></input>
			<input type="hidden" id="tpAssociado" name="tpAssociado" value="<?php echo $tpAssociado; ?>"></input>
			<div style="overflow-x: auto;overflow-y: hidden;" id="conteudoImpressao">
				<table border="1">
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
									<div class="label1"><b>FORMUL&Aacute;RIO PARA EXCLUS&Atilde;O DE BENEFICI&Aacute;RIO PF</b></div>
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
						
						<div class="label1"><b>2 - Dados do Requerente Pela Exclus&atilde;o do Benefici&aacute;rio</b> **(Caso seja o titular necess&aacute;rio preenchimento apenas dos telefones, e-mail e parentesco)</div>
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
									  <input type="text" name="email_requerente" id="email_requerente" size="40" value=""  ></input>
								</td>					
							</tr>
						</table>	
						<br>
						<div class="label1"><b>3 - Benefici&aacute;rio a ser excluso</b></div>
						<table width="100%" border="1" cellpadding="0" cellspacing="0">				
							<tr>					
								<td width="10%">												
									01 - Dependente<br>
									02 - Dependente
								</td>											
								<td width="10%">												
									Tipo Benefici&aacute;rio <br>
									<input type="text" name="tipo_benef_1" id="tipo_benef_1" size="10"></input>
									<hr style="height:1px; border:none; color:#000; background-color:#000; margin-top: 0px; margin-bottom: 0px;"/>
									Tipo Benefici&aacute;rio <br>
									<input type="text" name="tipo_benef_2" id="tipo_benef_2" size="10"></input>
									<hr style="height:1px; border:none; color:#000; background-color:#000; margin-top: 0px; margin-bottom: 0px;"/>
									Tipo Benefici&aacute;rio <br>
									<input type="text" name="tipo_benef_3" id="tipo_benef_3" size="10"></input>
								</td>
								<td width="80%">
									Benefici&aacute;rio <br>
									<select name="cod_benef_1" id="cod_benef_1" onchange="valorPlanoDep('cod_benef_1', '1');">
										<option value="">&nbsp;</option>

										<?php
										// add os itens do combo
										foreach($ArrDep as $item) {
											printf('<option value="%s" onchange="valorPlanoDep(\'cod1\', this.value);" %s>%s</option>',
												$item['codigo'],
												'',
												$item['nome']
											);
										}
										?>
									</select>
									<!--<input type="text" name="cod_benef_1" id="cod_benef_1" onblur="valorPlanoDep('cod1', this.value);" size="20"></input>-->
									<hr style="height:1px; border:none; color:#000; background-color:#000; margin-top: 0px; margin-bottom: 0px;"/>
									Benefici&aacute;rio <br>
									<select name="cod_benef_2" id="cod_benef_2" onchange="valorPlanoDep('cod_benef_2', '2');">
										<option value="">&nbsp;</option>

										<?php
										// add os itens do combo
										foreach($ArrDep as $item) {
											printf('<option value="%s" onchange="valorPlanoDep(\'cod2\', this.value);" %s>%s</option>',
												$item['codigo'],
												'',
												$item['nome']
											);
										}
										?>
									</select>
									<!--<input type="text" name="cod_benef_2" id="cod_benef_2" onblur="valorPlanoDep('cod2', this.value);" size="20"></input>-->
									<hr style="height:1px; border:none; color:#000; background-color:#000; margin-top: 0px; margin-bottom: 0px;"/>
									Benefici&aacute;rio <br>
									<select name="cod_benef_3" id="cod_benef_3" onchange="valorPlanoDep('cod_benef_3', '3');">
										<option value="">&nbsp;</option>

										<?php
										// add os itens do combo
										foreach($ArrDep as $item) {
											printf('<option value="%s" onchange="valorPlanoDep(\'cod3\', this.value);" %s>%s</option>',
												$item['codigo'],
												'',
												$item['nome']
											);
										}
										?>
									</select>									
								</td>
							</tr>				
						</table>
						<div class="label1"><b>4 - Motivo da exclus&atilde;o do benefici&aacute;rio</b></div>
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

						<table width="100%" height="56" border="0" cellpadding="0" cellspacing="0">
							<tr align="center" valign="middle">
								
								<td width="100%" p align="justify">
									* A solicita&ccedil;&atilde;o de exclus&atilde;o do benefici&aacute;rio n&atilde;o admite desist&ecirc;ncia a partir da ci&ecirc;ncia da operadora;<br>
									* O pedido de exclus&atilde;o tem <b>EFEITO IMEDIATO</b>, a partir da ci&ecirc;ncia da operadora. Ap&oacute;s a data e hor&aacute;rio da solicita&ccedil;&atilde;o de exclus&atilde;o os benefici&aacute;rios n&atilde;o estar&atilde;o mais cobertos pelo plano de sa&uacute;de e/ou odontol&oacute;gico. Qualquer utiliza&ccedil;&atilde;o do plano (ex. exame, cirurgia, etc), mesmo que j&aacute; autorizada pela operadora, n&atilde;o ser&aacute; realizado ap&oacute;s a solicita&ccedil;&atilde;o de exclus&atilde;o;<br>
									* A exclus&atilde;o do benefici&aacute;rio prev&ecirc;, cumprimento de novas car&ecirc;ncias, perda do direito à portabilidade de car&ecirc;ncias e preenchimento de nova declara&ccedil;&atilde;o de sa&uacute;de.
								</td>									
							</tr>
						</table>												
						<br>
						<div class="label1"><b>5 - Reten&ccedil;&atilde;o da Carterinha</b></div>
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
						
						<div class="label1"><b>6 - Comprovante de Atendimento</b></div>
						
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
									Possui boletos em aberto?<br>
									<input id="boletoAberto" name="boletoAberto" type="radio" />Sim	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;													
									<input id="boletoAberto" name="boletoAberto" type="radio"/>N&atilde;o	
								</td>
								<td width="30%">					
									Caso positivo, quantos e quais os meses?<br>
									<input type="text" name="dias_boleto_aberto" id="dias_boleto_aberto" size="30"></input>
								</td>
								<td width="50%">					
									Valor a ser reduzido na mensalidade?<br>
									R$&nbsp;&nbsp;&nbsp;(<input type="text" name="valor_reduzir" id="valor_reduzir" size="40"></input>)
								</td>
							 </tr>										
						</table>
									
						<table width="100%" height="56" border="0" cellpadding="0" cellspacing="0">
							<tr align="center" valign="middle">
								<td width="100%" p align="justify">
									* As mensalidades vencidas ou eventuais multas contratuais, s&atilde;o de responsabilidade do titular benefici&aacute;rio ou respons&aacute;vel pela contrata&ccedil;&atilde;o do plano;<br>
									* Eventuais utiliza&ccedil;&otilde;es ap&oacute;s a solicita&ccedil;&atilde;o de exclus&atilde;o &eacute; de responsabilidade do benefici&aacute;rio ou respons&aacute;vel pela contrata&ccedil;&atilde;o do plano;<br>
									* D&eacute;bitos pendentes com a operadora ser&atilde;o cobrados ap&oacute;s a entrega do comprovante efetivo de exclus&atilde;o do benefici&aacute;rio, de modo a n&atilde;o retardar a exclus&atilde;o;
								</td>									
							</tr>
						</table>											
						<br>
						<hr/>
						<div class="label1"><b>Data da solicita&ccedil;&atilde;o da exclus&atilde;o do benefici&aacute;rio</b></div>				
						<?php echo date('d/m/Y'); ?>
						<br>	
						<hr/>
						<input class="ace" type="checkbox" id="contrato"><span class="lbl">&nbsp;&nbsp;Declaro a veracidade das informa&ccedil;&otilde;es prestadas neste formul&aacute;rio e autorizo o processamento de exclus&atilde;o do benefici&aacute;rio na operadora Plena Sa&uacute;de Ltda.</span>
							
					</td>
				  </tr>
				</table>
			</div>	

			<br />
			<div class="formDefault_Conteudo_linha">
				<div class="formDefault_Conteudo_linha_Label">
					<label for="arquivosAnexar" class="labelDefault_Req">CNS (Cart&atilde;o Nacional do SUS) </label>
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
				<input type='button' class="btn btn-success" onclick="salvaExclusaoDep(); " name="btnConcluir" id="btnConcluir"  value="Gravar" />
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

valorCod1 = 0;
valorCod2 = 0;
valorCod3 = 0;

function salvaExclusaoDep(){	
	
	enderecoEmail 			= $('#email_requerente').val();	
	cpf_requerente 			= $('#cpf_requerente').val();	
	telefone_requerente 	= $('#telefone_requerente').val();	
	celular_requerente 		= $('#celular_requerente').val();	
	arquivo 				= $('#arquivoCNS').val();
	arquivo1 				= $('#arquivoCertidao').val();
	arquivo2 				= $('#arquivoCPF').val();
	arquivo3 				= $('#arquivoRG').val();
	cod_benef_1 			= $('#cod_benef_1').val();
	cod_benef_2 			= $('#cod_benef_2').val();
	cod_benef_3 			= $('#cod_benef_3').val();
	valor_reduzir 			= $('#valor_reduzir').val();
	protocoloAtendimento 	= $('#protocoloAtendimento').val();
	numeroRegistro 			= $('#numeroSequencial').val();
	tpAssoc 				= $('#tpAssociado').val();
	
	var formData = new FormData(formDefault);

    $.ajax({
        url: "upload_arquivos_frm_Plena.php",
        type: "POST",
        data: formData,
        success: function (data) {
            
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

	if (!cod_benef_1) {		
		swal({
			type: 'error',
			title: 'Para continuar, favor informar um dependente.'
		})
		return false;
	}
	
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
	
	if((arquivo == '' || arquivo == 'undefined') && (arquivo1 == '' || arquivo1 == 'undefined') && (arquivo2 == '' || arquivo2 == 'undefined') && (arquivo3 == '' || arquivo3 == 'undefined')){
		swal({
			type: 'error',
			title: 'Favor inserir o anexo.'
		})
		return false;
	}
	
	
		
	$.ajax({
			type: "GET",
			dataType: "html",
			url: "operacoesProcessos.php?op=exclusaoDep",
			data: 	"enderecoEmail="      	+ enderecoEmail 		+ '&' +
					"protocoloAtendimento=" + protocoloAtendimento 		+ '&' +
					"cod_benef_1=" 			+ cod_benef_1 		+ '&' +
					"cod_benef_2=" 			+ cod_benef_2 		+ '&' +
					"cod_benef_3=" 			+ cod_benef_3 		+ '&' +
					"valor_reduzir=" 		+ valor_reduzir 		+ '&' +
					"numeroRegistro=" 		+ numeroRegistro 		+ '&' +
					"numeroTel="      		+ telefone_requerente,
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

function valorPlanoDep(idCod, codigo){	
	if(idCod == 'cod_benef_1'){
		codigo = document.getElementById("cod_benef_1").value;
	}
	
	if(idCod == 'cod_benef_2'){
		codigo = document.getElementById("cod_benef_2").value;
	}
	
	if(idCod == 'cod_benef_3'){
		codigo = document.getElementById("cod_benef_3").value;
	}
	
	
	if(!codigo){
		return false;
	}
	$.ajax({
		type: "GET",
		dataType: "html",
		url: "operacoesProcessos.php?op=tipoAssoc",
		data: 	"codAssociado=" + codigo,
		complete: function(){
			//Carregando(null, 'hide');
		},
		success: function(msg){
			if(msg == 'T'){
				alert('Canal exclusivo para dependentes');
				if(idCod == 'cod_benef_1'){
					$('#cod_benef_1').val('');
				}else if(idCod == 'cod_benef_2'){
					$('#cod_benef_2').val('');
				}else if(idCod == 'cod_benef_3'){
					$('#cod_benef_3').val('');
				}
				return false;
			}else{
				$.ajax({
					type: "GET",
					dataType: "html",
					url: "operacoesProcessos.php?op=ultimaFatura",
					data: 	"codAssociado=" + codigo,
					complete: function(){
						//Carregando(null, 'hide');
					},
					success: function(msg){
						
						if(idCod == 'cod_benef_1'){
							valorCod1 = msg;
						}else if(idCod == 'cod_benef_2'){
							valorCod2 = msg;				
						}else if(idCod == 'cod_benef_3'){
							valorCod3 = msg;				
						}
						
						valor = parseFloat(valorCod1) + parseFloat(valorCod2) + parseFloat(valorCod3);	
						//valor = Math.round(valor);
						valor = valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

						$('#valor_reduzir').val(valor);
						return true;
					},
					beforeSend: function(){
						//Carregando(null, 'show');
					}
				});
			}
			
		},
		beforeSend: function(){
			//Carregando(null, 'show');
		}
	});	
		
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
		var type = 'exc_dep';
		//console.log(dataURL);
		var xhr = new XMLHttpRequest();
        xhr.open( 'POST', 'upload_pdf_ftp_jquery.php', true ); //Post to php Script to save to server
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("dados="+dataURL+"&type="+type);	
	});	
		
}
</script>