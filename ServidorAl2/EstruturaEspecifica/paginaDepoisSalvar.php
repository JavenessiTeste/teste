<?php

function paginaDepoisSalvar($chave,$nomeChave, $tabela, $tabelaOriginal,$tipo){
	//$tipo = INC ALT
	
	$retorno['DESTINO'] = '';
	$retorno['MENSAGEM_CONFIRMACAO'] = '';
	$retorno['NOME'] = '';
	

	if ($_SESSION['AliancaPx4Net']=='S') // Se for o ERP AliancaPx4Net
	{

		  require_once('../EstruturaPx/paginaDepoisSalvar_ERPPx.php');
		  $retorno = paginaDepoisSalvar_ERPPx($chave,$nomeChave, $tabela, $tabelaOriginal,$tipo);

	}
	else // então não é o ERP AliancaPX4Net, é o portal
	{
			
			if($tabela=='PS6500'){
				$tipoGuia = '';
				
				$queryTipoAut = "select TIPO_GUIA from PS6500 where numero_autorizacao =".aspas($chave); 
				$resTipoAut = jn_query($queryTipoAut);
				if ($rowTipoAut = jn_fetch_object($resTipoAut)) {
					$tipoGuia = $rowTipoAut->TIPO_GUIA;

					if($rowTipoAut->TIPO_GUIA=='C'){
						$retorno['NOME'] = 'Guia Consulta';
						$retorno['DESTINO'] = rvc('LINK_PERSISTENCIA').'ProcessoDinamico/guiaConsultaPng.php?numero='.$chave;
					}else if($rowTipoAut->TIPO_GUIA=='S'){
						$retorno['NOME'] = 'Guia SP/SADT';
						$retorno['DESTINO'] = rvc('LINK_PERSISTENCIA').'ProcessoDinamico/guiaSpsadtPng.php?numero='.$chave;
					}else if($rowTipoAut->TIPO_GUIA=='A'){
						$retorno['NOME'] = 'Guia Ambulatorioal';
						$retorno['DESTINO'] = rvc('LINK_PERSISTENCIA').'ProcessoDinamico/guiaSolicitacaoInternacaoPng.php?numero='.$chave;
					}else if($rowTipoAut->TIPO_GUIA=='I'){
						$retorno['NOME'] = 'Guia Internação';
						$retorno['DESTINO'] = rvc('LINK_PERSISTENCIA').'ProcessoDinamico/guiaSolicitacaoInternacaoPng.php?numero='.$chave;
					}
				}

				$retorno['MENSAGEM_CONFIRMACAO'] = 'Deseja imprimir a guia?';
				
				if ($tipoGuia != 'C'){			
					$queryDetPs6510 = "select NUMERO_AUTORIZACAO from Ps6510 where numero_autorizacao =".aspas($chave);
					$resDetPs6510 = jn_query($queryDetPs6510);
					$rowDetPs6510 = jn_fetch_object($resDetPs6510);
					if(!$rowDetPs6510->NUMERO_AUTORIZACAO){
						$retorno['DESTINO'] = '';
						$retorno['MENSAGEM_CONFIRMACAO'] = '';
						$retorno['NOME'] = '';				
					}
				}
				if($_SESSION['codigoSmart'] == '3419'){
					$queryDetPs6510 = "Select  coalesce(PS1015.endereco_email,PS1001.endereco_email)ENDERECO_EMAIL,PS1000.NOME_ASSOCIADO,PS5000.NOME_PRESTADOR,coalesce(ps6510.codigo_procedimento,Ps6500.PROCEDIMENTO_PRINCIPAL)CODIGO_PROCEDIMENTO,ps5210.NOME_PROCEDIMENTO from ps6500
									   inner join PS1000 on Ps1000.codigo_associado  = PS6500.codigo_associado
									   inner join ps5000 on PS5000.codigo_prestador  = Ps6500.codigo_prestador
									   left  join ps1001 on Ps1000.codigo_titular  = ps1001.codigo_associado
									   left  join ps1015 on Ps1000.codigo_titular  = ps1015.codigo_associado
									   left join ps6510 on ps6510.numero_autorizacao = Ps6500.numero_autorizacao
									   inner join ps5210 on ps5210.codigo_procedimento = coalesce(ps6510.codigo_procedimento,Ps6500.procedimento_principal) 
									   where ps6500.numero_autorizacao=".aspas($chave);
									   
					$resDetPs6510 = jn_query($queryDetPs6510);
					$rowDetPs6510 = jn_fetch_object($resDetPs6510);
					require('../EstruturaPrincipal/disparoEmail.php');
					$assunto = 'Geração de Autorização';
					$corpoEmail  = 'Olá ' . $rowDetPs6510->NOME_ASSOCIADO . ', <br>';
					$corpoEmail .= 'A Autorização Número '.$chave.' para o prestador '.$rowDetPs6510->NOME_PRESTADOR.' foi aprovada no dia '.date('d/m/Y').' as '. date('h:i').' com os procedimento(s):   <br> ';
					$corpoEmail .= '<br>Código Procedimento - Nome Procedimento<br><br>';
					$corpoEmail .= $rowDetPs6510->CODIGO_PROCEDIMENTO.' - '. $rowDetPs6510->NOME_PROCEDIMENTO.' <br>';
					$email = $rowDetPs6510->ENDERECO_EMAIL;
					while($rowDetPs6510 = jn_fetch_object($resDetPs6510)){
						$corpoEmail .= $rowDetPs6510->CODIGO_PROCEDIMENTO.' - '. $rowDetPs6510->NOME_PROCEDIMENTO.' <br>';
					}
					$corpoEmail .= '<br>Maiores  informações pelos canais de atendimento oficiais da Medical Health <br>';
					$corpoEmail .= 'Atenciosamente, <br>';
					$corpoEmail .= 'Medical Health<br>';
					if(trim($email)!='')
						disparaEmailRetorno($email, $assunto, $corpoEmail,'diego2607@gmail.com');
				}
			}elseif($tabela=='PS2500'){
				$retorno['NOME'] = 'GTO';
				$retorno['DESTINO'] = rvc('LINK_PERSISTENCIA').'ProcessoDinamico/guiaTratamentoOdontoApresentacaoPng.php?numero='.$chave;
				$retorno['MENSAGEM_CONFIRMACAO'] = 'Deseja imprimir a guia?';
			}
			else if (($tabela=='PS1095') and ($_SESSION['codigoSmart'] == '4018')) // Solicitação de Cancelamento Hebrom
			{
				$retorno['NOME'] = 'INFORMACOES';
				$retorno['DESTINO'] = 'https://portal.hebrombeneficios.com.br/AliancaNet2/ServidorCliente/MateriaisComercial/avisocancelamentohebrom.pdf';
				$retorno['NOME_BOTAO'] = 'Concordar e Imprimir';
			}
			else if ($tabela=='TMP1000_NET')
			{
				if(retornaValorConfiguracao('ABRIR_PROTOCOLO_TMP1000') == 'SIM'){	
					$retorno['NOME'] = 'PROTOCOLO';			
					$retorno['DESTINO'] = rvc('LINK_PERSISTENCIA').'ProcessoDinamico/protocolosPlena.php?tipo=I;'.$chave;
					$retorno['MENSAGEM_CONFIRMACAO'] = 'Deseja imprimir o protocolo?';	
				}elseif(retornaValorConfiguracao('VW_TMP1000_APOS_SALVAR') == 'NAO' or retornaValorConfiguracao('VW_TMP1000_APOS_SALVAR') == ''){			
					$retorno['NOME'] = 'NAVIGATE';
					$retorno['DESTINO_NAVIGATE'] = 'site/declaracaoSaudeTmp';	
					$retorno['CHAVE'] = $chave; 		
				}		
			}

			if($tabela=='PS1000')
			{
				$retorno['MENSAGEM'] = ' Finalizado o cadastro.<br>Código do titular gerado: ' . $chave;
			}

	}
	
	return $retorno;
	
	
}	



?>