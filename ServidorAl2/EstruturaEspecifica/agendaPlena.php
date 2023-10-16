<?php
require_once('../lib/base.php');
//require_once('../private/autentica.php');

global $API_NETPACS,$LINK_NETPACS;

$API_NETPACS = retornaValorConfiguracao('CHAVE_NETPACS');



$LINK_NETPACS = retornaValorConfiguracao('LINK_NETPACS'); 



//var_dump(visualizaHorarios('24/03/2021','207128','11'));

//      'ID_HORARIO' => int 3602766
//      'NOME_MEDICO' => string 'FERNANDO FUNARI VIVOLO' (length=22)
//      'HORA' => string '09:45' (length=5)
//      'DATA' => string '15/03/2021' (length=10)
//      'NOME_SALA' => string 'SÃO PAULO- BLOCO C 1ºANDAR CONSULTORIO 02' (length=43)

//var_dump(agendarHorario(250451 ,11,207128));

//var_dump(consultaPaciente('TESTE','12345678909'));

//207128

//var_dump(cadastraPaciente(1));
//211568



function visualizaHorarios($data,$idpaciente,$idProcedimento){//OK
	global $API_NETPACS,$LINK_NETPACS;
	
	$headers = array("accept: application/json","Authorization: ".$API_NETPACS);
	$url = $LINK_NETPACS .'horarios?';
	$url  .= 'buscaInteligente=false&';
	$url  .= 'dataBusca='.$data.'&';
	$url  .= 'idConvenio=3&';
	$url  .= 'idFilial=1&';
	$url  .= 'idPaciente='.$idpaciente.'&';
	$url  .= 'idPlanoConvenio=43&';
	$url  .= 'listIdProcedimento='.$idProcedimento.'&';
	$url  .= 'pesoPaciente=0';
	
	//pr($url);
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	$body = json_decode($result);
	
	$resultados = array();
    $i=0;
	foreach ($body[0] as $key => $value){
		//pr($value);
		//exit;
		$item= array();
		$i++;
		$item['ID_HORARIO']  = $value->idHorario;
		$item['NOME_MEDICO'] = $value->nomeMedico;
		$item['HORA']        = $value->horaInicialString;
		$item['DATA']        = $value->dataString;
		$item['NOME_SALA']   = $value->nomeSala;
		
		$resultados[]        = $item;
		//if($i==500)
		//	break;
	}
	
	return $resultados;
	
	

}

function agendarHorario($idHorario,$idProcedimento,$idpaciente){
	global $API_NETPACS,$LINK_NETPACS;
	
	$headers = array("Content-Type: application/json","Authorization: ".$API_NETPACS);
	$url = $LINK_NETPACS .'horarios';
	
	$data = '[
			  {
				"atendimentoRn": false,
				"dataAutorizacaoString": "'.date ('Y-m-d').'",
				"encaixe": false,
				"envioMensagemOrientacao": false,
				"guia": "",
				"guiaOperadora": "",
				"idConvenio": "3",
				"idPaciente": "'.$idpaciente.'",
				"idPlanoConvenio": "43",
				"indicacaoClinica": "",
				"listHorarioDTO": [
				  {
					"idHorario": "'.$idHorario.'",
					"idProcedimento": "'.$idProcedimento.'",
					"utilizaAnestesia": false,
					"utilizaContraste": false
				  }
				],
				"matricula": "",
				"pesoPaciente": "",
				"prontoAtendimento": false,
				"senhaAutorizacao": "",
				"validadeAutorizacaoString": "'.date ('Y-m-d').'",
				"validadeMatriculaString": "'.date ('Y-m-d').'"
			  }
			]';
	
	
	//pr($url);
	//pr($data);
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	$body = json_decode($result);
	
	$resultados = array();
	//pr($result); 
	if(@$body->status=='OK'){
		$mensagemRetorno = $body->message; 
		
		return substr($mensagemRetorno,strpos($mensagemRetorno,'ID: ')+3);
	}else{
		return 0;
	}
    
	//{"status":"OK","message":"Agendamento realizado com sucesso. ID: 178799"}


}

function consultaPaciente($cpf){//OK
	global $API_NETPACS,$LINK_NETPACS;
	
	$cpf = str_replace(".", "", $cpf);
	$headers = array("accept: application/json","Authorization: ".$API_NETPACS);
	$url = $LINK_NETPACS .'pacientes?';
	//$url  .= 'nome=like:'.urlencode('TESTE').'&';
	//$url  .= 'nome=eq:'.urlencode($nome).'&';
	$url  .= 'cpf=eq:'.$cpf;
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	$body = json_decode($result);
	
	$resultados = array();
    
	//print($result);

	if(count($body)==0)
		return 0;
    else
		return $body[0]->id_paciente;	
	

}

function cancelaAgenda($numeroAgenda){
	
	global $API_NETPACS,$LINK_NETPACS;
	
	$headers = array("accept: */*","Authorization: ".$API_NETPACS,"Content-Type: application/json");
	$url = $LINK_NETPACS .'atendimentos/'.$numeroAgenda.'/cancelar';
	//https://ris.plenasaude.com.br/api-gateway/netris/api/atendimentos/442856/cancelar
	//https://ris.plenasaude.com.br/api-gateway/netris/api/atendimentos/6837470/cancelar
	$data = '{"idMotivoSituacao": "2","observacao": "Usuario cancelou"}';
			
	//pr($headers);
	//pr($url);	
	//pr($data);
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	//pr($result);
	$body = json_decode($result);
	
	$resultados = array();
    

	return 'OK';	
	

}

function cadastraPaciente($rowAssociado){
	global $API_NETPACS,$LINK_NETPACS;
	
	$headers = array("Content-Type: application/json","Authorization: ".$API_NETPACS);
	$url = $LINK_NETPACS .'pacientes';
	
	$data = '{
			  "alturaPaciente": "",
			  "bairro": "",
			  "cartaoSus": "",
			  "cep": "",
			  "codigoHospitalar": "",
			  "complemento": "",
			  "cpf": "'.maskCpf($rowAssociado->NUMERO_CPF).'",
			  "dataNascimentoPaciente": "'.$rowAssociado->DATA_NASCIMENTO->format('d/m/Y').'",
			  "dataUltimaMenstruacaoPaciente": "",
			  "documentoEstrangeiro": "",
			  "email": "",
			  "endereco": "",
			  "idCepCidade": "",
			  "idCepEstado": "",
			  "idEstadoCivil": "",
			  "idProfissao": "",
			  "nacionalidade": "BRASIL",
			  "nomeMae": "",
			  "nomePaciente": "'.$rowAssociado->NOME_ASSOCIADO.'",
			  "nomeResponsavel": "",
			  "nomeSocial": "",
			  "numero": "",
			  "observacao": "",
			  "password": "",
			  "pesoPaciente": "",
			  "remedio": "",
			  "rg": "",
			  "sexoPaciente": "'.$rowAssociado->SEXO.'",
			  "telefoneCelular": "",
			  "telefonePaciente": "0",
			  "telefoneTrabalho": ""
			}';
	
	
		
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	$body = json_decode($result);
	
	$resultados = array();
    
	//print_r($body);

	if($body->message=='Gravado com sucesso')
		return $body->idPaciente;
    else
		return 0;	
	


}
function maskCpf($val){
	$mask = '###.###.###-##';
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
        if ($mask[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
    }

    return $maskared;
}



if($dadosInput['tipo'] =='dados'){
	$query = "select * from ps1000 where codigo_associado = ".aspas($_SESSION['codigoIdentificacao']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);
	$idPaciente = consultaPaciente($row->NUMERO_CPF);
	if($idPaciente==0){
		$idPaciente = cadastraPaciente($row);
	}
	
	if($idPaciente==0){
		$retorno['MSG']= 'Erro ao localizar o paciente.';	
	}else{
		$queryProc = "Select * from ESP_PROCEDIMENTOS_AGENDA where numero_registro=".aspas($dadosInput['registro']);
		$resProc = jn_query($queryProc);
		$rowProc = jn_fetch_object($resProc);
		$retorno['DADOS'] = visualizaHorarios(sqlToData($dadosInput['data']),$idPaciente,$rowProc->CODIGO_PROCEDIMENTO_HOSPITAL);	
	}
	
	echo json_encode($retorno);
	
	
}else if($dadosInput['tipo']=='agendar'){
	
	$query = "select * from ps1000 where codigo_associado = ".aspas($_SESSION['codigoIdentificacao']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);
	$idPaciente = consultaPaciente($row->NUMERO_CPF);
	if($idPaciente==0){
		$idPaciente = cadastraPaciente($row);
	}
	
	if($idPaciente==0){
		$retorno['MSG']= 'Erro ao localizar o paciente.';	
	}else{
		$queryProc = "Select * from ESP_PROCEDIMENTOS_AGENDA where numero_registro=".aspas($dadosInput['registro']);
		$resProc = jn_query($queryProc);
		$rowProc = jn_fetch_object($resProc);
		$retornoAgenda = agendarHorario($dadosInput['registroAgenda'],$rowProc->CODIGO_PROCEDIMENTO_HOSPITAL,$idPaciente);	
		if($retornoAgenda>0){
			$sequencial = jn_gerasequencial('ESP_AGENDA');
			$sql = "insert into ESP_AGENDA(
									NUMERO_REGISTRO,
									CODIGO_ASSOCIADO,
									DATA_AGENDA,
									HORA_AGENDA,
									SITUACAO_AGENDA,
									ID_AGENDA_HOSPITAL,
									NOME_MEDICO,
									NOME_SALA
									)
					VALUES(".
							aspas($sequencial).",".
							aspas($row->CODIGO_ASSOCIADO).",".
							DataToSql($dadosInput['data']).",".
							aspas($dadosInput['hora']).",".
							aspas('Agendado').",".
							aspas($retornoAgenda).",".
							aspas($dadosInput['medico']).",".
							aspas($dadosInput['sala']).")";
			jn_query($sql);	 
			$retorno['MSG']= 'Agendamento efetuado.';
			$retorno['STATUS'] = 'OK';
			

 			
		}else{
			$retorno['MSG']= 'Não foi possivel efetuar o agendamento.';
		}
	}	
	
	
	echo json_encode($retorno);
}else if($dadosInput['tipo'] =='dadosAgrupado'){
	$query = "select * from ps1000 where codigo_associado = ".aspas($_SESSION['codigoIdentificacao']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);
	$idPaciente = consultaPaciente($row->NUMERO_CPF);
	if($idPaciente==0){
		$idPaciente = cadastraPaciente($row);
	}
	
	if($idPaciente==0){
		$retorno['MSG']= 'Erro ao localizar o paciente.';	
	}else{
		$queryProc = "Select * from ESP_PROCEDIMENTOS_AGENDA where numero_registro=".aspas($dadosInput['registro']);
		$resProc = jn_query($queryProc);
		$rowProc = jn_fetch_object($resProc);
		$retorno['DADOS'] = visualizaHorariosAgrupado(date('d/m/Y', strtotime(' + 1 days')),$idPaciente,$rowProc->CODIGO_PROCEDIMENTO_HOSPITAL);	
		$retorno['NOME_PROCEDIMENTO'] = utf8_encode(getNomeProcedimento($rowProc->CODIGO_PROCEDIMENTO_HOSPITAL));
		$retorno['NOME_PACIENTE'] = utf8_encode($_SESSION['nomeUsuario']);
		//$retorno['DADOS'] = visualizaHorariosAgrupadoUnidade(date('d/m/Y', strtotime(' + 1 days')),$idPaciente,$rowProc->CODIGO_PROCEDIMENTO_HOSPITAL,1);	
	}
	
	echo json_encode($retorno);
	
	
}else if($dadosInput['tipo'] =='dadosAgrupadoUnidade'){
	$query = "select * from ps1000 where codigo_associado = ".aspas($_SESSION['codigoIdentificacao']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);
	$idPaciente = consultaPaciente($row->NUMERO_CPF);
	if($idPaciente==0){
		$idPaciente = cadastraPaciente($row);
	}
	
	if($idPaciente==0){
		$retorno['MSG']= 'Erro ao localizar o paciente.';	
	}else{
		$queryProc = "Select * from ESP_PROCEDIMENTOS_AGENDA where numero_registro=".aspas($dadosInput['registro']);
		$resProc = jn_query($queryProc);
		$rowProc = jn_fetch_object($resProc);
		//$retorno['DADOS'] = visualizaHorariosAgrupado(date('d/m/Y', strtotime(' + 1 days')),$idPaciente,$rowProc->CODIGO_PROCEDIMENTO_HOSPITAL);	
		$retorno['DADOS'] = visualizaHorariosAgrupadoUnidade($dadosInput['data'],$idPaciente,$rowProc->CODIGO_PROCEDIMENTO_HOSPITAL,$dadosInput['unidade']);	
	}
	
	echo json_encode($retorno);
	
	
}else if($dadosInput['tipo']=='agendarAgrupado'){
	
	$query = "select * from ps1000 where codigo_associado = ".aspas($_SESSION['codigoIdentificacao']);
	$res = jn_query($query);
	$row = jn_fetch_object($res);
	$idPaciente = consultaPaciente($row->NUMERO_CPF);
	if($idPaciente==0){
		$idPaciente = cadastraPaciente($row);
	}
	

	
	if($idPaciente==0){
		$retorno['MSG']= 'Erro ao localizar o paciente.';	
	}else{
		$queryProc = "Select * from ESP_PROCEDIMENTOS_AGENDA where numero_registro=".aspas($dadosInput['registro']);
		$resProc = jn_query($queryProc);
		$rowProc = jn_fetch_object($resProc);
		
		//pr($dadosInput);
		global $unidades;
		getUnidade($dadosInput['idUnidade']);
		$unidade = $unidades[$dadosInput['idUnidade']];
		//pr($unidade->nome);
		//pr();
		//pr($dadosInput['nomeMedico']);
		$sala= getNomeProcedimento($rowProc->CODIGO_PROCEDIMENTO_HOSPITAL)." agendado para o dia ".$dadosInput['data']." às ".$dadosInput['hora']." na unidade ".$unidade->nome." no endereço: ".$unidade->endereco . ' - ' . $unidade->bairro . ' - '. $unidade->cidade. ' - '. $unidade->cep." com o profissional ".$dadosInput['nomeMedico']."";
		$sala = utf8_decode($sala);
		//pr($sala);
		//$sala = getNomeProcedimento($rowProc->CODIGO_PROCEDIMENTO_HOSPITAL)." agendado na unidade ".$unidade->nome." no endereço: ".$unidade->endereco . ' - ' . $unidade->bairro . ' - '. $unidade->cidade. ' - '. $unidade->cep;
		//pr($sala);
		//exit;
		$retornoAgenda = agendarHorario($dadosInput['registroAgenda'],$rowProc->CODIGO_PROCEDIMENTO_HOSPITAL,$idPaciente);	
		if($retornoAgenda>0){
			$sequencial = jn_gerasequencial('ESP_AGENDA');
			$sql = "insert into ESP_AGENDA(
									NUMERO_REGISTRO,
									CODIGO_ASSOCIADO,
									DATA_AGENDA,
									HORA_AGENDA,
									SITUACAO_AGENDA,
									ID_AGENDA_HOSPITAL,
									NOME_MEDICO,
									ID_PROCEDIMENTO,
									ID_MEDICO,
									ID_UNIDADE,
									ID_HORARIO,
									NOME_SALA
									)
					VALUES(".
							aspas($sequencial).",".
							aspas($row->CODIGO_ASSOCIADO).",".
							DataToSql($dadosInput['data']).",".
							aspas($dadosInput['hora']).",".
							aspas('Agendado').",".
							aspas($retornoAgenda).",".
							aspas($dadosInput['nomeMedico']).",".
							aspas($rowProc->CODIGO_PROCEDIMENTO_HOSPITAL).",".
							aspas($dadosInput['idMedico']).",".
							aspas($dadosInput['idUnidade']).",".
							aspas($dadosInput['registroAgenda']).",".
							aspas($sala).")";
			jn_query($sql);	 
			$retorno['MSG']= 'Agendamento efetuado.';
			$retorno['STATUS'] = 'OK';
			
			$selectEmpresa = 'SELECT CODIGO_EMPRESA FROM PS1000 WHERE PS1000.CODIGO_ASSOCIADO= '.aspas($_SESSION['codigoIdentificacao']);
			$resEmpresa  = jn_query($selectEmpresa);
			if($rowEmpresa = jn_fetch_object($resEmpresa)){	
				if($rowEmpresa->CODIGO_EMPRESA == '400' ){
					$tabela = 'PS1001';
				}else{
					$tabela = 'PS1015';
				}
				
				$sqlEmail = 'SELECT COALESCE(EMAIL_CONFIRMADO,'.$tabela.'.ENDERECO_EMAIL) EMAIL_CONFIRMADO, DATA_CONFIRMACAO_EMAIL FROM PS1000
							 LEFT JOIN '.$tabela.' ON '.$tabela.'.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
							 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($_SESSION['codigoIdentificacao']);
			
				$resEmail  = jn_query($sqlEmail);
				
				if($rowEmail = jn_fetch_object($resEmail)){	
					$emailAssociado = $rowEmail->EMAIL_CONFIRMADO;			
				}
			}
			$salaEmail= getNomeProcedimento($rowProc->CODIGO_PROCEDIMENTO_HOSPITAL)." agendado para o dia ".$dadosInput['data']." às ".$dadosInput['hora']."<br>Unidade: ".$unidade->nome."<br>Endereço: ".$unidade->endereco . ' - ' . $unidade->bairro . ' - '. $unidade->cidade. ' - '. $unidade->cep." <br>Profissional: ".$dadosInput['nomeMedico']."";
		
			$corpoMSG  = '	<!doctype html>
					<html>
						<head>
							<meta charset="utf-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">							
						</head>
						<body >
							Olá '.$_SESSION['nomeUsuario'].'<br><br>
							'.$salaEmail.'<br>
							Permanecemos a sua disposicao, <br>
							Plena Saude

						</body>
					</html>';
			
			//$emailAssociado = 'diego2607@gmail.com';
			
			if(trim($emailAssociado!=''))
				disparaEmailAgenda($emailAssociado, '', 'Agendamento', $corpoMSG);	

			$sqlEmail = 'select  coalesce(CELULAR_CONFIRMADO,cast(PS1006.CODIGO_AREA as varchar(2))+Ps1006.NUMERO_TELEFONE) CELULAR_CONFIRMADO, DATA_CONFIRMACAO_CELULAR from PS1000
						 left join PS1006 on PS1006.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
						 WHERE PS1000.CODIGO_ASSOCIADO =  '.aspas($_SESSION['codigoIdentificacao']);
		
			$resEmail  = jn_query($sqlEmail);
			
			if($rowEmail = jn_fetch_object($resEmail)){	
				$celular = $rowEmail->CELULAR_CONFIRMADO;		
			}
			if($celular!=''){
				//require('../EstruturaEspecifica/smsZenvia.php');
				require('../lib/smsPointer.php');
				$salaSms =  utf8_decode('Agendado '.getNomeProcedimento($rowProc->CODIGO_PROCEDIMENTO_HOSPITAL)." ".$dadosInput['data']." as ".$dadosInput['hora']." Local: ".$unidade->nome." ");
				
				//$salaSms=  utf8_decode('Agendado '.getNomeProcedimento($rowProc->CODIGO_PROCEDIMENTO_HOSPITAL)." ".$dadosInput['data']." às ".$dadosInput['hora']."\nLocal: ".$unidade->nome."\n ".$unidade->endereco . ' - ' . $unidade->bairro . ' - '. $unidade->cidade. ' - '. $unidade->cep." \nProfissional: ".$dadosInput['nomeMedico']."");
				$retornoSms = enviaSmsPointer(trim($celular),$salaSms);
				
				$salaSms =  utf8_decode($unidade->endereco . ' - ' . $unidade->bairro . ' - '. $unidade->cidade. ' - '. $unidade->cep." Profissional: ".$dadosInput['nomeMedico']."");
				$retornoSms = enviaSmsPointer(trim($celular),$salaSms);
				//$retornoSms = enviaSmsZenvia('55'.trim($celular),$salaSms,$dadosInput['registroAgenda'].rand(0,100));
				
			}	
 			
		}else{
			$retorno['MSG']= 'Não foi possivel efetuar o agendamento.';
		}
	}	
	
	
	echo json_encode($retorno);
}
function visualizaHorariosAgrupado($data,$idpaciente,$idProcedimento){//OK
	global $API_NETPACS,$LINK_NETPACS;
	
	$headers = array("accept: application/json","Authorization: ".$API_NETPACS);
	$url = $LINK_NETPACS .'horarios-agrupados?';
	$url  .= 'buscaInteligente=false&';
	$url  .= 'dataBusca='.$data.'&';
	$url  .= 'idConvenio=3&';
	$url  .= 'idFilial=1&';
	$url  .= 'idPaciente='.$idpaciente.'&';
	$url  .= 'idPlanoConvenio=43&';
	$url  .= 'listIdProcedimento='.$idProcedimento.'&';
	$url  .= 'pesoPaciente=0';
	
	
	//pr($API_NETPACS);
	
	//pr($url);
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	$body = json_decode($result);
	//print_r( $body);
	//exit;
	$resultados = array();
    $i=0;
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
	
	global $unidades;
	
	foreach ($body as $key => $value){
		//pr($value);
		$item= array();
		$item['DATA']  = $value->data; 
		$date = explode('/',$item['DATA']);
		$timestamp = mktime(0, 0, 0,$date[1]+0, $date[0]+0, $date[2]+0);
		$item['LABEL'] = utf8_encode(ucfirst(strftime('%A, %d de %B de %Y', $timestamp))); 
		$item['DADOS'] = array();
		

		
		foreach ($value->unidades as $keyUnidade => $valueUnidade){
			$itemUnidades = array();
			getUnidade($valueUnidade->idUnidade);
			$unidade = $unidades[$valueUnidade->idUnidade]; 
			$itemUnidades['ID_UNIDADE'] = $unidade->id_unidade;
			
			$itemUnidades['TITULO'] = utf8_encode($unidade->nome);
			
			$itemUnidades['LINHA1'] = utf8_encode($unidade->endereco . ' - ' . $unidade->bairro . ' - '. $unidade->cidade. ' - '. $unidade->cep);
			
			//$itemUnidades['LINHA2']	= $unidade->nome;
			
			$itemUnidades['IMAGEM'] = $unidade->logo;

			$horariosLivres = 0;
			$horarioInicial = '';
			$horarioFinal   = '';		
			
			foreach ($valueUnidade->medicos as $keyMedicos => $valueMedicos){
				//pr($valueUnidade->medicos);
				foreach ($valueMedicos->horarios as $keyHorarios => $valueHorarios){
					//pr($valueHorarios->horaInicial);
					$horario = explode(':',$valueHorarios->horaInicial);
					if($horarioInicial==''){
						$horarioInicial = $horario[0];
						$horarioFinal   = $horario[0];
					}
					if($horarioInicial>$horario[0])
						$horarioInicial = $horario[0];
					if($horarioFinal<$horario[0])
						$horarioFinal = $horario[0];
					
					$horariosLivres = $horariosLivres+1;
						
				}
			}
			
			$itemUnidades['LINHA2']	= utf8_encode($horariosLivres. ' vagas, entre '.$horarioInicial.'h e '.$horarioFinal.'h');
			
			$item['DADOS'][] = $itemUnidades; 
		}	
		
		$i++;
		//pr($item);
		
		$resultados[] = $item;

	}
	
	return $resultados;
	
	

}

function visualizaHorariosAgrupadoUnidade($data,$idpaciente,$idProcedimento,$idUnidade){//OK
	global $API_NETPACS,$LINK_NETPACS;
	
	$headers = array("accept: application/json","Authorization: ".$API_NETPACS);
	$url = $LINK_NETPACS .'horarios-agrupados?';
	$url  .= 'buscaInteligente=false&';
	$url  .= 'dataBusca='.$data.'&';
	$url  .= 'idConvenio=3&';
	$url  .= 'idFilial=1&';
	$url  .= 'idPaciente='.$idpaciente.'&';
	$url  .= 'idPlanoConvenio=43&';
	$url  .= 'listIdProcedimento='.$idProcedimento.'&';
	$url  .= 'idUnidade='.$idUnidade.'&';
	$url  .= 'pesoPaciente=0';
	
	
	//pr($API_NETPACS);
	
	//pr($url);
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	$body = json_decode($result);
	//print_r( $body);
	//exit;
	$resultados = array();
    $i=0;
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
	
	global $unidades;
	
	foreach ($body as $key => $value){
		//pr($value);
		$item= array();
		$item['DATA']  = $value->data; 
		$date = explode('/',$item['DATA']);
		$timestamp = mktime(0, 0, 0,$date[1]+0, $date[0]+0, $date[2]+0);
		$item['LABEL'] = utf8_encode(ucfirst(strftime('%A, %d de %B de %Y', $timestamp))); 
		$item['DADOS_MEDICOS'] = array();
		
		//$horarioInicial = '';
		//$horarioFinal   = '';
		//$horariosLivres  = 0;
		foreach ($value->unidades as $keyUnidade => $valueUnidade){
			//$itemUnidades = array();
			//getUnidade($valueUnidade->idUnidade);
			//$unidade = $unidades[$valueUnidade->idUnidade]; 
			//$itemUnidades['ID_UNIDADE'] = $unidade->id_unidade;
			
			//$itemUnidades['TITULO'] = $unidade->nome;
			
			//$itemUnidades['LINHA1'] = $unidade->endereco . ' - ' . $unidade->bairro . ' - '. $unidade->cidade;
			
			//$itemUnidades['LINHA2']	= $unidade->nome;
			
			//$itemUnidades['IMAGEM'] = $unidade->logo;
			
			foreach ($valueUnidade->medicos as $keyMedicos => $valueMedicos){
				//pr($valueMedicos->nomeMedico);
				$itemMedicos = array();
				$itemMedicos['LABEL']    = utf8_encode($valueMedicos->nomeMedico);
				$itemMedicos['ID_MEDICO']    = $valueMedicos->idMedico;
				$itemMedicos['HORARIOS'] = array();
				
				foreach ($valueMedicos->horarios as $keyHorarios => $valueHorarios){
					//pr($valueHorarios->horaInicial);
					$itemHorarios = array();
					$itemHorarios['LABEL'] = $valueHorarios->horaInicial;
					$itemHorarios['ID_HORARIO']    = $valueHorarios->idHorario;
					
					$itemMedicos['HORARIOS'][] = $itemHorarios;	
				}
				$item['DADOS_MEDICOS'][] =$itemMedicos;
			}
 
		}	

		//pr($item);
		$resultados[] = $item;
		//exit;
		/*
		$item= array();
		$i++;
		$item['ID_HORARIO']  = $value->idHorario;
		$item['NOME_MEDICO'] = $value->nomeMedico;
		$item['HORA']        = $value->horaInicialString;
		$item['DATA']        = $value->dataString;
		$item['NOME_SALA']   = $value->nomeSala;
		
		$resultados[]        = $item;
		//if($i==500)
		//	break;
		*/
	}
	
	return $resultados;
	
	

}

function getUnidade($unidade){
	global $unidades;

	global $API_NETPACS,$LINK_NETPACS;
	
	if(!isset($unidades[$unidade])){
		$headers = array("accept: application/json","Authorization: ".$API_NETPACS);
		$url = $LINK_NETPACS .'unidades/'.$unidade;
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		if($result === false)
		{
			echo "Erro : " . curl_error($ch);
			exit;
		}

		curl_close($ch);

		$body = json_decode($result);
		//print_r($body);
		$unidades[$unidade] = $body;
	}

}

function getNomeProcedimento($procedimento){
	
	global $API_NETPACS,$LINK_NETPACS;
	
	
	$headers = array("accept: application/json","Authorization: ".$API_NETPACS);
	$url = $LINK_NETPACS .'procedimentos/'.$procedimento;
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);
	if($result === false)
	{
		echo "Erro : " . curl_error($ch);
		exit;
	}

	curl_close($ch);

	$body = json_decode($result);
	//print_r($body);
	return $body->nome;


}
function disparaEmailAgenda($emailAssociado, $protocolo = '', $assunto = '', $corpoMSG = ''){
	
	if($assunto == '')
		$assunto = 'Assunto Teste';	
	
	if($corpoMSG == '')
		$corpoMSG = 'Mensagem Teste';	
	
	$corpoMSG = utf8_decode($corpoMSG);
	$assunto = utf8_decode($assunto);

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
	

	
	$mail->Subject = $assunto;
	$mail->MsgHTML($corpoMSG);
	
	if(!$mail->Send()) {		
		echo "Erro: " . $mail->ErrorInfo;
	}
}







?>