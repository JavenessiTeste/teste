<?php
require('base.php');
require_once('class.phpmailer.php');
require_once('class.smtp.php');
require_once('PHPMailerAutoload.php');
require_once("mpdf60/mpdf.php");

global $nomeArquivo;
global $caminhoArquivoLocal;
global $caminhoArquivoExterno;
global $caminhoArquivo;

if($_GET['nomeArquivo']){
	global $nomeArquivo;
	global $caminhoArquivoLocal;
	global $caminhoArquivoExterno;

	$nomeArquivo = $_GET['nomeArquivo'];	
	$caminhoArquivoLocal = '../../ServidorCliente/Boletos/';
	$caminhoArquivoExterno = '/www/Aliancaappnet2/Boletos/';
	
	require('copiaArquivoFTPExterno.php');
	exit;
}



if(!$_GET['numeroRegistro']){
	echo 'Registro nao informado';
	exit;
}

$query  = ' 	SELECT ';
$query .= ' 		COALESCE(PS1000.NOME_ASSOCIADO,PS1010.NOME_EMPRESA) AS NOME_CLIENTE, NUMERO_REGISTRO_PS1020, ENDERECO_EMAIL, CAMINHO_ARQUIVO, EMAIL_ENVIADO, PS1020.VALOR_FATURA, PS1020.NUMERO_LINHA_DIGITAVEL, ';
$query .= " 		replace(ASSUNTO_EMAIL, '-','.') as ASSUNTO_EMAIL, COALESCE(PS1000.NUMERO_CPF, PS1010.NUMERO_CNPJ) AS NUMERO_CPFCNPJ, DATA_VENCIMENTO, PS1020.MES_ANO_REFERENCIA ";
$query .= '	FROM ESP_EMAIL_PORTAL ';
$query .= '	INNER JOIN PS1020 ON (PS1020.NUMERO_REGISTRO = ESP_EMAIL_PORTAL.NUMERO_REGISTRO_PS1020) ';
$query .= '	LEFT JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = ESP_EMAIL_PORTAL.CODIGO_ASSOCIADO) ';
$query .= '	LEFT JOIN PS1010 ON (PS1010.CODIGO_EMPRESA = ESP_EMAIL_PORTAL.CODIGO_EMPRESA) ';
$query .= '	WHERE ESP_EMAIL_PORTAL.NUMERO_REGISTRO_PS1020 = ' . aspas($_GET['numeroRegistro']);
$query .= '	ORDER BY ESP_EMAIL_PORTAL.NUMERO_REGISTRO DESC ';
$res = jn_query($query);
$row = jn_fetch_object($res);

if(!$row->NOME_CLIENTE){
	echo 'Cliente nao encontrado';
	exit;
}

$nomeCliente = $row->NOME_CLIENTE;
$dataVencimento = SqlToData($row->DATA_VENCIMENTO);
$assunto = sanitizeString($row->ASSUNTO_EMAIL);
$corpoEmail = 'Mensagem de Email padrao';
$emailAssociado = $row->ENDERECO_EMAIL;
$caminhoArquivo = $row->CAMINHO_ARQUIVO;
$mesAnoReferencia = $row->MES_ANO_REFERENCIA;
$valorFatura = $row-> VALOR_FATURA;
$linhaDigitavel = $row-> NUMERO_LINHA_DIGITAVEL;


if(retornaValorConfiguracao('VALIDA_FTP_EXTERNO') == 'SIM'){
	validarFTPExterno($row);
}

if(retornaValorConfiguracao('INSERE_SENHA_PDF_EMAIL') == 'SIM'){
	insereSenhaArquivo(substr($row->NUMERO_CPFCNPJ, -5));
}

if(montaCorpoEmail()){
	echo disparaEmailSSL($emailAssociado, $assunto, $corpoEmail);
	exit;
}else{
	echo 'Email nao configurado';
	exit;
}

function disparaEmailSSL($emailAssociado, $assunto, $corpoEmail)
{
	global $caminhoArquivo, $nomeCliente;

	$corpoEmail = str_replace(chr(92),"",$corpoEmail);
	
	$mail = new PHPMailer();
	$mail->isSMTP();
	
	$mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
	
	$mail->Host = retornaValorConfiguracao('HOST_EMAIL');
	$mail->SMTPAuth = retornaValorConfiguracao('SMTP_EMAIL');	
	$mail->Username = retornaValorConfiguracao('USERNAME_EMAIL');	
	$mail->Password = retornaValorConfiguracao('PASSWORD_EMAIL');	
	$mail->Port = retornaValorConfiguracao('PORT_EMAIL');	
	$mail->SetFrom(retornaValorConfiguracao('EMAIL_PADRAO'), retornaValorConfiguracao('NOME_EMPRESA_EMAIL'));	
	$mail->AddAddress($emailAssociado, $emailAssociado);
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoEmail);
	$mail->AddAttachment($caminhoArquivo, 'Boleto_'.$nomeCliente);
	
	if(!$mail->Send()) {
		return json_encode("STATUS:ERRO");		
	}else{
		jn_query('UPDATE ESP_EMAIL_PORTAL SET EMAIL_ENVIADO = "S" WHERE NUMERO_REGISTRO_PS1020 = ' . aspas($_GET['numeroRegistro']));
		return json_encode("STATUS:OK");		
	}
	
	return true;
}


function insereSenhaArquivo($senhaDocumento){	
	global $caminhoArquivo;	
	
	$mpdf=new mPDF('c'); 
	$mpdf->SetImportUse();
	$mpdf->SetProtection(array(), $senhaDocumento, $senhaDocumento);	
	$pagecount = $mpdf->SetSourceFile($caminhoArquivo);	
	$tplId = $mpdf->ImportPage($pagecount);
	$mpdf->UseTemplate($tplId);
	$mpdf->Output($caminhoArquivo);	
}

function montaCorpoEmail(){
	global $nomeCliente,  $dataVencimento, $corpoEmail, $assunto, $mesAnoReferencia, $valorFatura, $linhaDigitavel, $caminhoArquivo;

	$queryEmpresa = 'SELECT CODIGO_SMART FROM CFGEMPRESA';
	$resEmpresa = jn_query($queryEmpresa);
	$rowEmpresa = jn_fetch_object($resEmpresa);

	if($rowEmpresa->CODIGO_SMART == '4298'){//Beneficio Sul
		$corpoEmail  = 'Ol&aacute; ' . $nomeCliente . ',' . '<br><br>';
		
		//----------------------INICIO DO COMENTADO TEMPORARIAMENTE POR SOLICITAÇÃO DO CLIENTE, CHAMADO 3501---------------------
		/*$corpoEmail .= 'Acabou de chegar o boleto do seu Plano de Sa&uacute;de Unimed, com vencimento para dia ' . $dataVencimento . '. <br><br>';
		$corpoEmail .= 'Aten&ccedil;&atilde;o, para abrir a sua fatura &eacute; necess&aacute;rio digitar os ultimos 5 n&uacute;meros do CPF. <br><br>';
		$corpoEmail .= 'Os benefici&aacute;rios dever&atilde;o pagar, em seu banco de prefer&ecirc;ncia, os boletos que ser&atilde;o enviados mensalmente por e-mail. Caso n&atilde;o tenha os receba at&eacute; o dia 10 de cada m&ecirc;s,  solicite a segunda via pelo telefone (WhatsApp) (51) 99243.7681. <br><br>';		
		$corpoEmail .= 'Estamos investindo em um novo portal e em um app pr&oacute;prio para facilitar e melhorar a comunica&ccedil;&atilde;o com voc&ecirc; e a sua experi&ecirc;ncia com seu plano de sa&uacute;de. <br><br>';
		$corpoEmail .= 'Pedimos que atente-se para manter seus pagamentos em dia, evitando suspens&atilde;o ou preju&iacute;zos, al&eacute;m de encargos e multas previstas contratualmente.  <br><br>';	
		$corpoEmail .= 'Muitas melhorias est&atilde;o a caminho. Muito obrigada pela confian&ccedil;a!  <br><br>';*/
		//----------------------FIM DO COMENTÁRIO TEMPORÁRIO---------------------------------------------------------------------
		
		//----------------------INICIO NO TEXTO TEMPORÁRIO PARA COMUNICAR O REAJUSTE DO PLANO------------------------------------
		$corpoEmail .= 'Como ocorre a cada anivers&aacute;rio de contrato, os reajustes dos planos UNIMED, junto ao SINDAERGS, ter&atilde;o 10,80% de atualiza&ccedil;&atilde;o no m&ecirc;s de outubro de 2023. <br><br>';
		$corpoEmail .= 'Nos &uacute;ltimos dois anos estamos abaixo da soma dos percentuais aplicados para os planos individuais/familiares, que &eacute;	considerado o menor &iacute;ndice do mercado. <br><br>';
		$corpoEmail .= 'Salientamos que o cuidado preventivo com a sa&uacute;de &eacute; a ferramenta mais forte contra os altos reajustes dos planos.  Cuide-se! <br><br>';
		$corpoEmail .= 'Informamos que seu boleto foi emitido e encontra&#45;se anexo. <br><br>'; 
		$corpoEmail .= 'Destacamos que o vencimento est&aacute; programado para dia ' . $dataVencimento . '. <br><br>'; 
		$corpoEmail .= 'Qualquer d&uacute;vida ou dificuldade favor nos encaminhar e&#45;mail para sindaergs@benetsaude.com.br ou pelo whatsapp (51) 99243.7681. <br><br>'; 
		$corpoEmail .= 'Observa&ccedil;&atilde;o importante: <br>'; 
		$corpoEmail .= 'Senha para abrir o documento: <br>'; 
		$corpoEmail .= 'Utilizar os 5 &uacute;ltimos n&uacute;meros do seu CPF. <br><br>';		
		//----------------------FIM NOVO TEXTO TEMPORÁRIO-------------------------------------------------------------------------
		
		$corpoEmail .= 'Atenciosamente, <br><br>';
		$corpoEmail .= 'BENET Sa&uacute;de <br>';
		$corpoEmail .= '<a href="www.benetsaude.com.br">www.benetsaude.com.br</a> <br>';
		$corpoEmail .= 'respons&aacute;vel pelo plano de sa&uacute;de do SINDAERGS. <br>';
		return true;
	}elseif($rowEmpresa->CODIGO_SMART == '3389'){//Vidamax	
		
		$caminhoArquivoLocal = '../../ServidorCliente/Boletos/';
		$caminhoArquivoExterno = '/www/Aliancaappnet2/Boletos/';
		
		$corpoEmail  = 'Prezado (a) Senhor (a) ' . $nomeCliente . ', <br><br>';

		$corpoEmail  .= 'IMPORTANTE: Algumas empresas tem sido alvo de fraudes, que consistem na adultera&ccedil;&atilde;o do c&oacute;digo de barras dos t&iacute;tulos banc&aacute;rios (boletos) emitidos por elas. Ao pagar esse boleto, os valores s&atilde;o creditados na conta corrente desses fraudadores ao inv&eacute;s da institui&ccedil;&atilde;o emissora. Por este motivo, a VIDAMAX vem alertar que ao efetuar o pagamento do seu Plano de Sa&uacute;de, confira o dom&iacute;nio do e-mail enviado que &eacute; sempre @vidamax.com.br, as informa&ccedil;&otilde;es impressas no documento emitido, bem como os dados do destinat&aacute;rio descritos pelo banco no comprovante ao digitar o c&oacute;digo de barras, antes de confirmar a transa&ccedil;&atilde;o: Nome do Benefici&aacute;rio: VIDAMAX Administradora de Benef&iacute;cios - CNP 09.164.784/0003-68, Seguran&ccedil;a &eacute; assunto s&eacute;rio e depende de todos n&oacute;s! Com cuidado aten&ccedil;&atilde;o, podemos evitar esses transtornos. <br><br>';

		$corpoEmail  .= 'Conforme solicita&ccedil;&atilde;o, enviamos em anexo o boleto banc&aacute;rio referente ao seu Plano de Sa&uacute;de, compet&ecirc;ncia ' .$mesAnoReferencia . '. <br><br>';
		$corpoEmail  .= 'Para visualizar seu boleto por quest&atilde;o de seguran&ccedil;a, &eacute; necess&aacute;rio digitar os 06(seis) primeiros n	&uacute;meros do CPF do titular do plano. <br><br>'; 
		$corpoEmail  .= 'Acesse o nosso site ou aplicativo da VIDAMAX, e confira o acesso &agrave; segunda via do seu boleto, detalhamento de Faturas, Informe de pagamentos etc. <br><br>';
		$corpoEmail  .= 'No primeiro acesso, clique em Recuperar Dados de Login os quais ser&atilde;o enviados para seu e-mail cadastrado em sistema. <br><br>';
		$corpoEmail  .= 'Para sua maior comodidade, opte pelo D&eacute;bito Autom&eacute;tico em conta corrente, dispon&iacute;veis nos bancos: Ita&uacute;, Santander e Banco do Brasil. <br><br>';
		$corpoEmail  .= 'Lembre-se! Na d&uacutevida, entre sempre em contato com nossos canais de atendimento. <br><br>';
		$corpoEmail  .= 'Telefone: (11) 3113-1717 <br>';
		$corpoEmail  .= 'Whatsapp: (11) 3113-1717 <br>';
		$corpoEmail  .= 'E-mail: contato@vidamax.com.br <br><br>';
		$corpoEmail  .= 'Atenciosamente, <br><br>';
		$corpoEmail  .= 'Vidamax <br>';

		return true;

	}elseif($rowEmpresa->CODIGO_SMART == '4018'){ //Hebrom		
	
		$nomeArquivoEx = end(explode('/', $caminhoArquivo));				
		$caminhoArquivo = '../../ServidorCliente/uploadsBoleto/' . $nomeArquivoEx;
		
		$assunto = 'Boleto de pagamento do plano de saude';
		$corpoEmail  =  '<b><font color="green"> Ol&aacute; ' . $nomeCliente . ', </font></b><br><br>';
		$corpoEmail  .= 'Conforme solicitado, segue em anexo a segunda via de seu boleto com vencimento  <font color="green"><b>' .$dataVencimento . '</b></font> no valor de <font color="green"><b>'. $valorFatura .'. </b></font><br><br>';

		$corpoEmail  .= '<b><font color="green">Linha Digit&aacute;vel:</font></b> <br> ' . $linhaDigitavel .'<br><br>';


		$corpoEmail  .= 'Lembramos tamb&eacute;m que &eacute; poss&iacute;vel a emiss&atilde;o do boleto atrav&eacute;s do portal do cliente e no aplicativo. Siga o passo a passo:<br>';
		$corpoEmail  .= '1. Basta acessar o Portal do Cliente, <a href="https://portal.hebrombeneficios.com.br/AliancaNet2/Site/site"> clique aqui</a>.<br>';
		$corpoEmail  .= '2. Em perfil, selecione o Perfil Benefici&aacute;rio.<br>';
		$corpoEmail  .= '3. No campo Cpf Usu&aacute;rio digite N&uacute;mero do CPF do titular do plano(apenas os n&uacute;meros sem pontos e tra&ccedil;os ou espa&ccedil;os).<br>';
		$corpoEmail  .= '4. No campo Senha, digite tamb&eacute;m o N&uacute;mero do CPF do titular do plano(apenas os n&uacute;meros sem pontos e tra&ccedil;os ou espa&ccedil;os).<br>';
		$corpoEmail  .= '5. Selecione a op&ccedil;a&otilde;o Segunda via de boleto<br>';
		$corpoEmail  .= '6. <b><font color="green">Pronto!</font></b> O boleto ser&aacute; exibido : )<br><br>';

		$corpoEmail  .= '<b><font color="green"><a href="https://portal.hebrombeneficios.com.br/AliancaNet2/Site/site"> Baixe nosso Aplicativo</a></font></b><br>';
		$corpoEmail  .= 'Google Play Store, <b><font color="green"><a href="https://play.google.com/store/apps/details?id=com.javenessi.HebromApp&pli=1"> clique aqui</a></font></b><br>';
		$corpoEmail  .= 'Apple App Store, <b><font color="green"><a href="https://apps.apple.com/br/app/hebromapp/id1547996349"> clique aqui</a></font></b><br>';

		$corpoEmail  .= 'Atenciosamente Hebrom Benef&iacute;cios <br><br>';
		$corpoEmail  .= '<b><font color="green">Central de Atendimento</font></b><br>';
		$corpoEmail  .= 'Central (11) 2284-3540 <br>';
		$corpoEmail  .= 'WhatsApp (11) 4040-3500 <br><br><br>';
		$corpoEmail  .= '<b><font color="green">Hor&aacute;rio de funcionamento</font></b> <br>';
		$corpoEmail  .= 'Segunda a sexta-feira, das 08:00 &agrave;s 11:45 e das 13:00 &agrave;s 18:00 <br><br>';
		$corpoEmail  .= '<b><font color="green">Endere&ccedil;o </font></b><br>';
		$corpoEmail  .= 'Av. Hil&aacute;rio Pereira de Souza, 406 - Torre II, 24&deg; andar - conjunto 2405 - Centro, Osasco - SP, 06010-170 <br><br>';
		$corpoEmail  .= 'Por favor, pedimos que voc&ecirc; n&atilde;o responda esse e-mail, pois se trata de uma mensagem autom&aacute;tica e n&atilde;o &eacute; poss&iacute;vel dar continuidade com seu atendimento por aqui. <br><br><br>';

		$corpoEmail  .= 'Atenciosamente';


		return true;
	}elseif($rowEmpresa->CODIGO_SMART == '4308'){//BKR		
		
		$caminhoArquivo = '../../../..' .$caminhoArquivo;
		
		$corpoEmail = '<img height="90%" width="100%" src="https://www.bkrsaude.com.br/AliancaAppNet2/ServidorAl2/ProcessoDinamico/retornaImagem.php?tp=imagemBoletoBKR&cliente='.$nomeCliente .'&vencimento='.$dataVencimento .'&valor='.$valorFatura .'&linhaDigitavel='.$linhaDigitavel. '"> ';				
		
		return true;
	}else{
		return false;
	}
}

function validarFTPExterno($dados){
	global $nomeArquivo, $caminhoArquivoLocal, $caminhoArquivoExterno, $caminhoArquivo;
	
	$caminhoArquivoLocal = '../../ServidorCliente/Boletos/';
	$caminhoArquivoExterno = '/www/Aliancaappnet2/Boletos/';
	$nomeArquivo = $dados->CAMINHO_ARQUIVO;
	$nomeArquivo = explode($caminhoArquivoExterno, $nomeArquivo);
	$nomeArquivo = $nomeArquivo[1];	
	$caminhoArquivo = $caminhoArquivoLocal . '/' . $nomeArquivo;
	
	require('copiaArquivoFTPExterno.php');			
}
?>