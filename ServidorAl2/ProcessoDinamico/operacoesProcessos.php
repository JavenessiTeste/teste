<?php
require('../lib/base.php');
require('../private/autentica.php');
$op = isset($_GET['op']) ? $_GET['op'] : $_POST['op'];


switch ($op) {
	
	case 'cancelamentoPlenaPF' :		
		$descricao = 'Tipo de Exclusao: Solicitada via portal ' . "\n" . 'Codigo do associado:' . $_SESSION['codigoIdentificacao'] . "\n";		
		$numeroTel = $_GET['numeroTel'];
		$emailAssociado = $_GET['enderecoEmail'];
		
		if($numeroTel == '' | $numeroTel == 'undefined'){
			$numeroTel = 'NAO INFORMADO';
		}
		
        $query  = "INSERT INTO PS6110(CODIGO_ASSOCIADO, NOME_PESSOA, PROTOCOLO_ATENDIMENTO, ";
		$query .= "CODIGO_RECLAMACAO_SUGESTAO, DATA_RECLAMACAO_SUGESTAO, DATA_EVENTO, DESCRICAO_RECLAMACAO_SUGESTAO, ";
		$query .= " FONE_CONTATO, FONE_CONTATO_02, DEPARTAMENTO_RESPONSAVEL, ASS_SOL_CANCELAMENTO, EMAIL_CONTATO) "; 
		$query .= "Values ( ";		
		$query .= aspas($_SESSION['codigoIdentificacao']) . ", ";
		$query .= aspas($_SESSION['nomeUsuario']) . ", ";
		$query .= aspas($_GET['protocoloAtendimento']) . ", ";
		$query .= aspas('45') . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= aspas($descricao) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas('CAD') . ", ";
		$query .= aspas($_GET['assinatura']) . ", ";
		$query .= aspas($_GET['enderecoEmail']);
		$query .= ')'; 
		
		jn_query($query);
		
		$corpoMSG  = '	<!doctype html>
					<html>
						<head>
							<meta charset="utf-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">							
						</head>
						<body >
							Prezado, ' . $_SESSION['nomeUsuario'] . ' - ' . $_SESSION['codigoIdentificacao'] . '<br>
							Obrigado pelo seu contato, sua mensagem foi recebida e sera respondida no prazo de ate 10 dias uteis. <br>

							Para maiores informacoes por gentileza entrar em contato com a central de atendimento (011) 2450-0070 <br>
							 
							Protocolo Atendimento: ' . $_GET['protocoloAtendimento'] . ' <br>
							Permanecemos a sua disposicao, <br>
							Plena Saude

						</body>
					</html>';

		disparaEmail($emailAssociado, $_GET['protocoloAtendimento'], 'FORMULARIO PARA CANCELAMENTO DO CONTRATO', $corpoMSG);
		
		//return 'Solicitacao de cancelamento encaminhada para operadora. Protocolo: ' . $_GET['protocoloAtendimento'];
		echo $_GET['protocoloAtendimento'];
		
	break;

	case 'inclusaoDep' :
		
		$descricao = 'Tipo de Inclusao Dep: Solicitada via portal ' . "\n" . 'Codigo do associado:' . $_SESSION['codigoIdentificacao'] . "\n";		
		$numeroTel = $_GET['numeroTel'];
		$emailAssociado = $_GET['enderecoEmail'];
		
		if($numeroTel == '' | $numeroTel == 'undefined'){
			$numeroTel = 'NAO INFORMADO';
		}
		
		$query  = "INSERT INTO PS6110(CODIGO_ASSOCIADO, NOME_PESSOA, PROTOCOLO_ATENDIMENTO, ";
		$query .= "CODIGO_RECLAMACAO_SUGESTAO, DATA_RECLAMACAO_SUGESTAO, DATA_EVENTO, DESCRICAO_RECLAMACAO_SUGESTAO, ";
		$query .= " FONE_CONTATO, FONE_CONTATO_02, DEPARTAMENTO_RESPONSAVEL, EMAIL_CONTATO) "; 
		$query .= "Values ( ";		
		$query .= aspas($_SESSION['codigoIdentificacao']) . ", ";
		$query .= aspas($_SESSION['nomeUsuario']) . ", ";
		$query .= aspas($_GET['protocoloAtendimento']) . ", ";
		$query .= aspas('48') . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= aspas($descricao) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas('CAD') . ", ";
		$query .= aspas($_GET['enderecoEmail']);
		$query .= ' ) '; 		
		jn_query($query);	
		
		$corpoMSG  = '	<!doctype html>
						<html>
							<head>
								<meta charset="utf-8">
								<meta name="viewport" content="width=device-width, initial-scale=1">							
							</head>
							<body >
								Prezado, ' . $_SESSION['nomeUsuario'] . ' - ' . $_SESSION['codigoIdentificacao'] . '<br>
								Obrigado pelo seu contato, sua mensagem foi recebida e sera respondida no prazo de ate 10 dias uteis. <br>

								Para maiores informacoes por gentileza entrar em contato com a central de atendimento (011) 2450-0070 <br>
								 
								Protocolo Atendimento: ' . $_GET['protocoloAtendimento'] . ' <br>
								Permanecemos a sua disposicao, <br>
								Plena Saude

							</body>
						</html>';

		disparaEmail($emailAssociado, $_GET['protocoloAtendimento'], 'FORMULARIO PARA INCLUSAO', $corpoMSG);
		
		echo 'Solicitacao de inclusao do dependente encaminhada para operadora. Protocolo: ' . $_GET['protocoloAtendimento'];

	break;
	
	case 'exclusaoDep' :		
		$descricao  = ' Tipo de Exclusao Dep: Solicitada via portal ' . "\n" . 'Codigo do associado:' . $_SESSION['codigoIdentificacao'] . "\n";		
		
		if($_GET['cod_benef_1']){
			$descricao .= ' Dependente1 : ' . $_GET['cod_benef_1'] . "\n";		
		}
		
		if($_GET['cod_benef_2']){
			$descricao .= ' Dependente2 : ' . $_GET['cod_benef_2'] . "\n";		
		}
		
		if($_GET['cod_benef_3']){
			$descricao .= ' Dependente3 : ' . $_GET['cod_benef_3'] . "\n";		
		}

		$descricao .= ' Valor a ser reduzido : ' . $_GET['valor_reduzir'] . "\n";		
		
		$numeroTel = $_GET['numeroTel'];
		$emailAssociado = $_GET['enderecoEmail'];
		
		if($numeroTel == '' | $numeroTel == 'undefined'){
			$numeroTel = 'NAO INFORMADO';
		}
		
		$query  = "INSERT INTO PS6110(CODIGO_ASSOCIADO, NOME_PESSOA, PROTOCOLO_ATENDIMENTO, ";
		$query .= "CODIGO_RECLAMACAO_SUGESTAO, DATA_RECLAMACAO_SUGESTAO, DATA_EVENTO, DESCRICAO_RECLAMACAO_SUGESTAO, ";
		$query .= " FONE_CONTATO, FONE_CONTATO_02, DEPARTAMENTO_RESPONSAVEL, EMAIL_CONTATO) "; 
		$query .= "Values ( ";		
		$query .= aspas($_SESSION['codigoIdentificacao']) . ", ";
		$query .= aspas($_SESSION['nomeUsuario']) . ", ";
		$query .= aspas($_GET['protocoloAtendimento']) . ", ";
		$query .= aspas('49') . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= dataToSql(date('d/m/Y')) . ", ";
		$query .= aspas($descricao) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas($numeroTel) . ", ";
		$query .= aspas('CAD') . ", ";
		$query .= aspas($_GET['enderecoEmail']);
		$query .= ')'; 
		
		jn_query($query);	
		
		$corpoMSG  = '	<!doctype html>
						<html>
							<head>
								<meta charset="utf-8">
								<meta name="viewport" content="width=device-width, initial-scale=1">							
							</head>
							<body >
								Prezado, ' . $_SESSION['nomeUsuario'] . ' - ' . $_SESSION['codigoIdentificacao'] . '<br>
								Obrigado pelo seu contato, sua mensagem foi recebida e sera respondida no prazo de ate 10 dias uteis. <br>

								Para maiores informacoes por gentileza entrar em contato com a central de atendimento (011) 2450-0070 <br>
								 
								Protocolo Atendimento: ' . $_GET['protocoloAtendimento'] . ' <br>
								Permanecemos a sua disposicao, <br>
								Plena Saude

							</body>
						</html>';

		disparaEmail($emailAssociado, $_GET['protocoloAtendimento'], 'FORMULARIO PARA EXCLUSAO DE BENEFICIARIO PF', $corpoMSG);
		
		echo 'Solicitacao de exclusao do dependente encaminhada para operadora. Protocolo: ' . $_GET['protocoloAtendimento'];

	break;
	
	case 'buscaValor' :		
		$valorPlanoDep = 0;
		$dtNasc = $_GET['dtNascDep'];
		
		$queryDadosTit  = ' SELECT CODIGO_PLANO, CODIGO_TABELA_PRECO FROM PS1000 ';
		$queryDadosTit .= ' WHERE CODIGO_ASSOCIADO =' . aspas($_SESSION['codigoIdentificacao']);
		$resDadosTit = jn_query($queryDadosTit);
		$rowDadosTit = jn_fetch_object($resDadosTit);
		if(
			$rowDadosTit->CODIGO_PLANO != '36' && 
			$rowDadosTit->CODIGO_PLANO != '473' && 
			$rowDadosTit->CODIGO_PLANO != '122'  && 
			$rowDadosTit->CODIGO_PLANO != '32'  && 
			$rowDadosTit->CODIGO_PLANO != '251'  && 
			$rowDadosTit->CODIGO_PLANO != '252'  && 
			$rowDadosTit->CODIGO_PLANO != '253'  && 
			$rowDadosTit->CODIGO_PLANO != '250'  && 			
			$rowDadosTit->CODIGO_PLANO != '42'){
			$valor = '90.00';
		}else{
			$idade = calcula_idadeDep($dtNasc);
			$tabelaPreco = $rowDadosTit->CODIGO_TABELA_PRECO;
			
			if($_SESSION['codigoSmart'] == '3423' && $rowDadosTit->CODIGO_PLANO == '122'){
				$tabelaPreco = 13;
			}
			
			$queryValor  = ' SELECT VALOR_PLANO FROM PS1032 ';
			$queryValor .= ' WHERE CODIGO_PLANO = ' . $rowDadosTit->CODIGO_PLANO;
			$queryValor .= ' AND CODIGO_TABELA_PRECO = ' . $tabelaPreco;
			$queryValor .= ' AND IDADE_MINIMA <= ' . $idade;
			$queryValor .= ' AND IDADE_MAXIMA >= ' . $idade;
			
			$resValor = jn_query($queryValor);
			$rowValor = jn_fetch_object($resValor);
			
			$valor = $rowValor->VALOR_PLANO;
		}
		
		echo $valor;

	break;
	
	case 'ultimaFatura' :				
		$codAssociado = $_GET['codAssociado'];
				
		$queryValor  = ' SELECT TOP 1 PS1021.VALOR_FATURA FROM PS1020 ';
		$queryValor .= ' INNER JOIN PS1021 ON (PS1020.NUMERO_REGISTRO = PS1021.NUMERO_REGISTRO_PS1020) ';
		$queryValor .= ' WHERE PS1020.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		$queryValor .= ' AND PS1021.CODIGO_ASSOCIADO = ' . aspas($codAssociado);
		$queryValor .= ' AND PS1020.DATA_PAGAMENTO IS NOT NULL';
		$queryValor .= ' ORDER BY PS1020.NUMERO_REGISTRO DESC ';
		//pr($queryValor,true);
		$resValor = jn_query($queryValor);
		$rowValor = jn_fetch_object($resValor);
		
		echo $rowValor->VALOR_FATURA;

	break;
	
	case 'tipoAssoc' :				
		$codAssociado = $_GET['codAssociado'];
				
		$queryTp  = ' SELECT TIPO_ASSOCIADO FROM PS1000 ';		
		$queryTp .= ' WHERE PS1000.CODIGO_ASSOCIADO = ' . aspas($codAssociado);
		$resTp = jn_query($queryTp);
		$rowTp = jn_fetch_object($resTp);
		
		echo $rowTp->TIPO_ASSOCIADO;

	break;

	case 'solicitacaoBoletoEmail' :	

		$tipoModelo = substr($_SERVER['HTTP_USER_AGENT'],0,100); 
		$assinatura = "Assinado eletronicamente mediante login/senha por ".$_SESSION['nomeUsuario']. ", "."em ".strftime('%A, %d de %B de %Y as %H:%M:%S', strtotime('now'))."\n"."através  do ".$tipoModelo." - IP:".$_SERVER["REMOTE_ADDR"];				
				
		$query  = ' UPDATE PS1002 SET 	FLAG_BOLETO_APENAS_EMAIL = "S", ';	
		$query .= '						ASS_BOLETO_EMAIL = ' . aspas($assinatura) . ', ';	
		$query .= ' 					LOG_BOLETO_EMAIL = ' . aspas(date('d/m/Y H:i') . 'IP: ' . $_SERVER['REMOTE_ADDR']);		
		$query .= ' WHERE PS1002.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
		
		if(jn_query($query)){
			echo 'Processo alterado. Os novos boletos serão enviados via e-mail.';
		}else{
			echo 'Não foi possível alterar a regra, favor entrar em contato com a operadora.';
		}		

	break;
    	
	case 'verificaSolicitacaoRealizada' :						
				
		$queryValid = 'SELECT FLAG_BOLETO_APENAS_EMAIL FROM PS1002 WHERE CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);	
		$resValid = jn_query($queryValid);
		$rowValid = jn_fetch_object($resValid);
		
		echo $rowValid->FLAG_BOLETO_APENAS_EMAIL;

	break;

	default:

}

function disparaEmail($emailAssociado, $protocolo = '', $assunto = '', $corpoMSG = ''){
	
	if($assunto == '')
		$assunto = 'Assunto Teste';	
	
	if($corpoMSG == '')
		$corpoMSG = 'Mensagem Teste';	
	

	require_once('../lib/class.phpmailer.php');
	require_once("../lib/PHPMailerAutoload.php");

	$mail   = new PHPMailer();
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
	
	
	if($_SESSION['codigoSmart'] == '3423'){
		$mail->AddCC('movimentacaopf@plenasaude.com.br', 'Plena Saude');
	}
	
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoMSG);
	
	if(!$mail->Send()) {		
		echo "Erro: " . $mail->ErrorInfo;
	}
}

function calcula_idadeDep($data_nasc) {

	$data_nasc=explode('/',$data_nasc);

	$data=date('d/m/Y');

	$data=explode('/',$data);

	$anos=$data[2]-$data_nasc[2];

	if($data_nasc[1] > $data[1])

	return $anos-1;

	if($data_nasc[1] == $data[1])
	if($data_nasc[0] <= $data[0]) {
	return $anos;
	break;
	}
	else{
	return $anos-1;
	break;
	}

	if ($data_nasc[1] < $data[1])
	return $anos;
}
