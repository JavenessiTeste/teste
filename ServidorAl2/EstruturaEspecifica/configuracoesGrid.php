<?php

function configuracoesGrid($tabela){
	
	$retorno['PAGINAS'] = 10; //10 25 50 100 1000
	$retorno['ABRE_FILTRO'] = false;
	$retorno['LABEL_TITULO_SEM_REGISTROS'] = 'Nenhum registro encontrado.';//APP
	$retorno['TEXTO_GRID'] = '';//'<FONT FACE=Times COLOR="#0000AA">Fonte Times azul</FONT><br><FONT FACE=Arial COLOR="#00AA00">Fonte Arial verde</FONT><br><FONT FACE=Courier COLOR="#AA0000">Fonte Courier vermelha</FONT>';

	if(retornaValorConfiguracao('FILTRO_ABERTO_PADRAO') == 'SIM'){		
		$retorno['ABRE_FILTRO'] = true;
	}
	
	if(retornaValorConfiguracao('QUANT_ITENS_GRID') != ''){			
		$retorno['PAGINAS'] = retornaValorConfiguracao('QUANT_ITENS_GRID');
	}	
	
	if($tabela == 'VW_SEGUNDA_VIA_AL2' && $_SESSION['codigoSmart'] == '4055'){
		$retorno['TEXTO_GRID']  = ' <b><u>IMPORTANTE</u></b>: SE VOCÊ OPTAR EM PAGAR POR <b>BOLETO BANCÁRIO</b> VIA <b>PAGSEGURO</b>, <font color="red">NÃO PAGUE O BOLETO APÓS A DATA DO VENCIMENTO E NÃO TENTE ATUALIZÁ-LO NO SITE DO BANCO</font>. EFETUE O PAGAMENTO SOMENTE ATÉ O VENCIMENTO APRESENTADO NO BOLETO. ';
		$retorno['TEXTO_GRID'] .= ' <br> SE POR ALGUM MOTIVO VOCÊ NÃO CONSEGUIR PAGAR ATÉ O VENCIMENTO, <font color="red">DESCARTE ESTE BOLETO</font>, ENTRE NO NOSSO SITE <a target="_blank" href="http://www.sintimmmeb.com.br/"> WWW.SINTIMMMEBSAUDE.COM.BR </a>, FAÇA O LOGIN E <font color="red">GERE UM NOVO BOLETO</font>. '; 
	}
	
	if($tabela == 'VW_BENEF_EMPR_NET' && $_SESSION['codigoSmart'] == '3423'){//Plena
		$retorno['PAGINAS'] ='100';
	}
	
	if($tabela == 'VW_VND1000_CAAPSML'){
		$retorno['ABRE_FILTRO'] = false;
		$retorno['TEXTO_GRID'] .= ' <font color="#087539;" style="font-size:21px;">Para inclusão de dependentes, acesse o botão acima "Adicionar novo registro"</font>. '; 
	}

	if($tabela == 'VW_PAINEL_AGENDAMENTO_CIR_AL2'){		
		$retorno['TEXTO_GRID'] .= ' <table width="45%" style="font-size:9px; line-height: 0.2;" border="1px"> '; 
		$retorno['TEXTO_GRID'] .= ' <tr><td>STATUS</td><td>QUANT ITENS</td></tr> '; 

		$queryQuant  = '	SELECT COUNT(*) QUANTIDADE, ';
		$queryQuant .= '		CASE 	WHEN STATUS_AGENDAMENTO = "AU" THEN "AUTORIZADO" ';
		$queryQuant .= '				WHEN STATUS_AGENDAMENTO = "EA" THEN "EM ATENDIMENTO" ';
		$queryQuant .= '				WHEN STATUS_AGENDAMENTO = "PE" THEN "PENDENTE" ';
		$queryQuant .= '				WHEN STATUS_AGENDAMENTO = "CA" THEN "CANCELADO" ';
		$queryQuant .= '				WHEN STATUS_AGENDAMENTO = "FA" THEN "FALTOU" ';
		$queryQuant .= '				WHEN STATUS_AGENDAMENTO = "RE" THEN "REALIZADO" ';
		$queryQuant .= ' 		END STATUS_AGENDAMENTO ';
		$queryQuant .= ' 	FROM ESP_AGENDAMENTO_CIRURGICO ';
		$queryQuant .= '	GROUP BY STATUS_AGENDAMENTO ';
		$resQuant = jn_query($queryQuant);
		
		while($rowQuant = jn_fetch_object($resQuant)){
			$inicioFont = '';
			$fimFont = '</font>';

			if($rowQuant->STATUS_AGENDAMENTO == 'AUTORIZADO'){
				$inicioFont = '<font color="green">';
			}elseif($rowQuant->STATUS_AGENDAMENTO == 'EM ATENDIMENTO'){
				$inicioFont = '<font color="Gold">';
			}elseif($rowQuant->STATUS_AGENDAMENTO == 'PENDENTE'){
				$inicioFont = '<font color="red">';
			}elseif($rowQuant->STATUS_AGENDAMENTO == 'CANCELADO' || $rowQuant->STATUS_AGENDAMENTO == 'PENDENTE'){
				$inicioFont = '<font color="grey">';
			}elseif($rowQuant->STATUS_AGENDAMENTO == 'REALIZADO'){
				$inicioFont = '<font color="black">';
			}

			$retorno['TEXTO_GRID'] .= ' <tr><td>' . $inicioFont . $rowQuant->STATUS_AGENDAMENTO . $fimFont . '</td><td>' . $inicioFont . $rowQuant->QUANTIDADE . $fimFont . '</td></tr> '; 
		}		

		$retorno['TEXTO_GRID'] .= ' </table> '; 
	}


	if($tabela == 'VW_PS1095_CD_AL2' && $_SESSION['codigoSmart'] == '4308'){//BKR Saúde
	
		$retorno['TEXTO_GRID'] = '<p> Para solicitar o cancelamento de seu plano ou do seu dependente, selecione a opção Adicionar Novo Registro e preencha todos os campos solicitados. O comprovante do efetivo desligamento do beneficiário, efetuado pela Operadora, será enviado via e-mail, no prazo de 10(dez) dias úteis, após registro de cancelamento. </p>';
		$retorno['TEXTO_GRID'].= '<p> Prezado Beneficiário, antes de solicitar o cancelamento de seu plano, TENHA CIÊNCIA das informações abaixo, previstas no art. 15 da RN 412 da ANS, que dispõe sobre a solicitação de exclusão de beneficiário de contrato coletivo empresarial ou por adesão:  </p>';
		$retorno['TEXTO_GRID'].= '<p> I - Eventual ingresso em novo plano de saúde poderá importar:   </p>';
		$retorno['TEXTO_GRID'].= '<p> a)	no cumprimento de novos períodos de carência, observado o disposto no inciso V do artigo 12, da Lei nº 9.656, de 3 de junho de 1998; </p>';
		$retorno['TEXTO_GRID'].= '<p> b)	na perda do direito à portabilidade de carências, caso não tenha sido este o motivo do pedido, nos termos previstos na RN nº186, de 14 de janeiro de 2009, que dispõe, em especial, sobre a regulamentação da portabilidade das carências previstas no inciso V do art. 12 da Lei nº 9.656, de 3 de junho de 1998;  </p>';
		$retorno['TEXTO_GRID'].= '<p> c)	no preenchimento de nova declaração de saúde, e, caso haja doença ou lesão preexistente - DLP, no cumprimento de Cobertura Parcial Temporária - CPT, que determina, por um período ininterrupto de até 24 meses, a partir da data da contratação ou adesão ao novo plano, a suspensão da cobertura de Procedimentos de Alta Complexidade (PAC), leitos de alta tecnologia e procedimentos cirúrgicos; </p>';
		$retorno['TEXTO_GRID'].= '<p> d)	na perda imediata do direito de remissão, quando houver, devendo o beneficiário arcar com o pagamento de um novo contrato de plano de saúde que venha a contratar; </p>';
		$retorno['TEXTO_GRID'].= '<p> II	- efeito imediato e caráter irrevogável da solicitação de cancelamento do contrato ou exclusão de beneficiário, a partir da ciência da operadora ou administradora de benefícios; </p>';
		$retorno['TEXTO_GRID'].= '<p> III	- as contraprestações pecuniárias vencidas e/ou eventuais coparticipações devidas, nos planos em pré-pagamento ou em pós pagamento, pela utilização de serviços realizados antes da solicitação de cancelamento ou exclusão do plano de saúde são de responsabilidade do beneficiário; </p>';
		$retorno['TEXTO_GRID'].= '<p> IV	- as despesas decorrentes de eventuais utilizações dos serviços pelos beneficiários após a data de solicitação de cancelamento ou exclusão do plano de saúde, inclusive nos casos de urgência ou emergência, correrão por sua conta; </p>';
		$retorno['TEXTO_GRID'].= '<p> V	- a exclusão do beneficiário titular do contrato individual ou familiar não extingue o contrato, sendo assegurado aos dependentes já inscritos o direito à manutenção das mesmas condições contratuais, com a assunção das obrigações decorrentes; e </p>';
		$retorno['TEXTO_GRID'].= '<p> VI	- a exclusão do beneficiário titular do contrato coletivo empresarial ou por adesão, observará as disposições contratuais quanto à exclusão ou não dos dependentes, conforme o disposto no inciso II do parágrafo único do artigo 18, da RN nº 195, de 14 de julho de 2009, que dispõe sobre a classificação e características dos planos privados de assistência à saúde, regulamenta a sua contratação, institui a orientação para contratação de planos privados de assistência à saúde e dá outras providências. </p>';	

	}

	if($tabela == 'VW_REATIVACAO_AL2'){
		$retorno['TEXTO_GRID'] = ' <b><u>IMPORTANTE</u></b>: <font color="red">TODOS OS BOLETOS EM ABERTO DEVEM SER PAGOS, CASO NÃO, A REATIVAÇÃO NÃO SERÁ PROCESSADA POR COMPLETO. </font> ';		
	}

	if($tabela == 'VW_COBERTURA_PLANO_AL2'){
		$retorno['PAGINAS'] ='50';
	}
	
	return $retorno;
}


?>