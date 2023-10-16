<?php
require_once('../lib/base.php');

global $clientIDVitHadoc;
global $clientSecretVitHadoc;
global $linkVitHadoc;
global $audienceVitHadoc;
global $bearerVitHadoc;

$clientIDVitHadoc     = retornaValorConfiguracao('CLIENT_ID_VITHADOC'); 
$clientSecretVitHadoc = retornaValorConfiguracao('CLIENTE_SECRET_VITHADOC');
$linkVitHadoc         = retornaValorConfiguracao('LINK_VITHADOC'); 
$audienceVitHadoc     = retornaValorConfiguracao('AUDIENCE_VITHADOC'); 


if($_GET['tit']!=''){
	$queryEmp = 'SELECT CODIGO_SMART FROM CFGEMPRESA ';
	$resEmp = jn_query($queryEmp);
	$rowEmp= jn_fetch_object($resEmp);
	
	if(strtoupper(md5($_GET['tit'].$rowEmp->CODIGO_SMART)) != strtoupper($_GET['vf'])){
		echo 'ERRO';
		exit;
	}
	
	$bearerVitHadoc = RequestTokenVitHadoc();
	
	$query = 'select  PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO,PS1000.NUMERO_CPF,PS1000.DATA_NASCIMENTO,PS1000.DATA_EXCLUSAO,PS1001.ENDERECO_EMAIL,PS1001.ENDERECO,PS1001.BAIRRO,PS1001.CIDADE,PS1001.ESTADO,PS1001.CEP,ERRO_VITHADOC,STATUS_VITHADOC,UUDI_VITHADOC from PS1000
			  left join PS1001 on PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_TITULAR
			  where PS1000.CODIGO_TITULAR = '.aspas($_GET['tit']);
	$res = jn_query($query);
	
	while($row = jn_fetch_object($res)){
		$queryTel = "select top 1 RTRIM(LTRIM(replace(replace(replace(coalesce(CAST(CODIGO_AREA as varchar(2)),'')+NUMERO_TELEFONE,'(',''),')','') ,'-',''))) TELEFONE from PS1006 where Ps1006.CODIGO_ASSOCIADO =".aspas($_GET['tit']);
		$resTel = jn_query($queryTel);
		$rowTel= jn_fetch_object($resTel);
		if(trim($row->UUDI_VITHADOC)==''){
			$retorno = CadastraClienteVitHadoc($row->NOME_ASSOCIADO,$row->NUMERO_CPF,sqlToData($row->DATA_NASCIMENTO),$row->ENDERECO_EMAIL,$row->ENDERECO,$row->BAIRRO,$row->CIDADE,$row->ESTADO,$row->CEP,$rowTel->TELEFONE);
			if($retorno['DADOS']['status']=='200'){
				$update = 'UPDATE PS1000 set ERRO_VITHADOC='.aspas($retorno['DADOS']['message']).',UUDI_VITHADOC = '.aspas($retorno['DADOS']['patientUid']).',STATUS_VITHADOC='.aspas('ATIVADO').' where CODIGO_ASSOCIADO='.aspas($row->CODIGO_ASSOCIADO);
				jn_query($update);
			}else{
				$update = 'UPDATE PS1000 set ERRO_VITHADOC='.aspas($retorno['DADOS']['message']).',STATUS_VITHADOC='.aspas('ERRO').' where CODIGO_ASSOCIADO='.aspas($row->CODIGO_ASSOCIADO);
				jn_query($update);
			}
		}else{
			if(($row->STATUS_VITHADOC=='ATIVADO')and (sqlToData(PS1000.DATA_EXCLUSAO)!='')){
				$retorno = DesativaClienteVitHadoc($row->UUDI_VITHADOC);
				if($retorno['DADOS']['status']=='200'){
					$update = 'UPDATE PS1000 set ERRO_VITHADOC='.aspas($retorno['DADOS']['message']).',STATUS_VITHADOC='.aspas('DESATIVADO').' where CODIGO_ASSOCIADO='.aspas($row->CODIGO_ASSOCIADO);
					jn_query($update);
				}else{
					$update = 'UPDATE PS1000 set ERRO_VITHADOC='.aspas($retorno['DADOS']['message']).' where CODIGO_ASSOCIADO='.aspas($row->CODIGO_ASSOCIADO);
					jn_query($update);
				}
			}
			if(($row->STATUS_VITHADOC=='DESATIVADO')and (sqlToData(PS1000.DATA_EXCLUSAO)=='')){
				$retorno = AtivaClienteVitHadoc($row->UUDI_VITHADOC);
				if($retorno['DADOS']['status']=='200'){
					$update = 'UPDATE PS1000 set ERRO_VITHADOC='.aspas($retorno['DADOS']['message']).',STATUS_VITHADOC='.aspas('DESATIVADO').' where CODIGO_ASSOCIADO='.aspas($row->CODIGO_ASSOCIADO);
					jn_query($update);
				}else{
					$update = 'UPDATE PS1000 set ERRO_VITHADOC='.aspas($retorno['DADOS']['message']).' where CODIGO_ASSOCIADO='.aspas($row->CODIGO_ASSOCIADO);
					jn_query($update);
				}
			}
		
		}
		
	
	}
	


	

}


//print_r(CadastraClienteVitHadoc('Teste teste','957.274.000-82','21/10/1984','teste@teste.com.br','','','','','',''));
/*
Array
(
    [STATUS] => OK
    [DADOS] => Array
        (
            [status] => 200
            [message] => Your request was successfully processed.
            [patientUid] => 5a79e5aa-07db-452c-b768-95dba95ae14c
            [registryId] => 1944
            [signature] => oUaH4uIFTKdh5No1yPLsJP1Efm/VAsYDHiPKDVYvdqsRW7HY9dCCkZeHwGb9IV/jdB4nji5UiQrd+K8mSkSJjKEFiYBtWIU1dbLfp5lrrcSGc/TEvq+2+pS6w7IR+artWCPGAxssqS38LthQZGw5SYVpuUPoahMFF4bPx5OvUId2wVoN+Su2el8eKnX4K3fXzgmqP2O7ceXJbZUuA/u4p14aq1qeUBZPT4iSgafG0Ls1Ul0c39Ngnb1tv9q6q7Qcwv0ksljxvlGwzuSslPlxZJ85LfN8TYw7675JceG79IqsmthPabZyQqR7PRduxE5tSO1fFwLkjhV6bwEZJJbh2dizI7yxLA/dvtRcnBPr04/2Nhn6v15z8P8LAYIP659gpv2mNQAvM4Y2HOXRpmGdTDuw8hb4ihSJJBty7UJ/XmFIwYkns2Rlir3m2UEqN520N4DnG67Hv2q6P4F/M9lg2FGchlZgOa9KjVSN1DcBV3ZtgmGWEhvDuadq2kw8lYvlJ02dD35W/1Mx5N8hKyulx8QZ+btXgPGHiINbZGxFmaGjd72r+Lz8ycGy5BodhpYLBQv6+Usgs8xyaM9s6kvcTz+k0OixivbMQ0xRpQOwObl0JvZieKp6N8GQGy89UQv3Ce2DmzT/xhIZdZYqedRs4zIEktJ/mCktt/4WvDgeO/g=
        )

)
Array
(
    [STATUS] => OK
    [DADOS] => Array
        (
            [status] => 706
            [message] => Validation Error: This CPF is already in our database.
            [errorCode] => ERROR_706_CPF_ALREADY_USED
            [signature] => 5e0yv/ufG5pk6hFXur0vP0HyfmzxSZfe7lysWc3cpaxbVh/AcJJFbKnSzUWYX1/Y9r5yJAODMKZ+EUjLOU98FhxFZA0OBv7S9e0CTId2YORyYYweVtWHeMZxwlKMPZynr3yrxBGg/4rK8wIrOablNZhwzeT59zcKOCbTAgOg1k8jpMC//teOl3IqcOcKxNTkXd9Edy4XKeOkl3xhrnMijQJYY5CIjgoXUOLCSL9za0H4oCVsENskEfEUzLDUnFhYrd5J/3zimE/UciR6YvY1iLsuA41Q90RUSm2RUWfzQWL33DBJyV4bNkotFGytTxM8cGXTK5UQhhsRI1jTQegBv5lhE+bzAv3sf8OBNsucoJrQZGxFGw66GYaR+5YUs+TjB/PVoJpw9usXHypIWNMvu4vcGObo8U0RT7gJ5bZmbB4iPUBzFHqotWbvHoESgNayssmgttyWvJOjf7I2YYbadAtjm1YiR2H9Fov21VL51+kuuGu/0J0kIhOVP/7ami6gFXlHBeutvGJYmGK0/7ouAJtraeLH2EEgbj/MTW+NEJz+vOBUyNZF6OmdfDNtWmX2q+BERlpkQHtM0vUdpZwl9e69/4mbHdPMuvv9jhs3tz5c48Fo/i0V+RDvM/uD25HWRINtownIcpzKkBw6g04OdDMholgSFtqYlpm79RBIP7o=
        )

)
*/
//print_r(DesativaClienteVitHadoc('5a79e5aa-07db-452c-b768-95dba95ae14c'));
/*
Array
(
    [STATUS] => OK
    [DADOS] => Array
        (
            [status] => 200
            [message] => The patient was deactivated successfully.
            [patientUid] => 5a79e5aa-07db-452c-b768-95dba95ae14c
            [signature] => 4ianZF/mHGl2r7H7oIr5HDeB483HdT2yVJ1BsJwGjo2Mttc0H+7qzpqTxfgRn0XqlsbrEiHd+rIhuDo7S8iV/L+RnmY0HlJAru01Xp9f/2+lfjyfoYdsMnwhSF9VfJjY1m0UqEqOjsQNMzxluAOjYIKBmmTzaY+NUrmzwzeRbOu1/fH098TZtJzQPADiD5VVAam667iMyEkEtxiwPUO1VBEhTbJsMfHlYxt5ThNWFz69CU/DaVisTHxoalieKDsrVi/JHQH5NStdHsDsH3xwdBJhQvjwLfcqcskCmdcz/fxEozrlZn2w6dgHkh+GiDtDVA3GsadgqUSteKqgsKfDQdStfIlom4gXer5wo8vw0/4eynpI5uSvuY4W4uCO8VG7mcrH/dK6qiLH9hXeNWjvAU5wYo0RVG6rbJP69z1IJlYmK/h7KDEHQmTgdR1of2EVrxL54PVwDOS6tbDTdrZwAc62p1cWQRFcUHOzfb1oYoyipnOlSV5nM3brHGoVVFhWH263KSpWDf6NszVCtvVHUZaQWgVTBr52Z8C7JDYPh72fIwmISta+G0tdlrSWpqdwJcocAVxQPSxXbjTcOXH1Ov5t9rIMUHg9pqU8kNSLPDc5C1P4dhE9YN7My7Ixpg5cQws6UHlYHKxI0vkJPF9UsUuNlFGAROVRODaeOFuKEIQ=
        )

)
*/

//print_r(AtivaClienteVitHadoc('5a79e5aa-07db-452c-b768-95dba95ae14c'));
/*
Array
(
    [STATUS] => OK
    [DADOS] => Array
        (
            [status] => 200
            [message] => The patient was reactivated successfully.
            [patientUid] => 5a79e5aa-07db-452c-b768-95dba95ae14c
            [signature] => J5kiN7sPfzgHg1WfEOy83qwlg8xCcijzEfAH6qYvbF9l22swZGYdvxGn5N/8L9SjdsK0RtzqxxLSuLNaRyDI9aWSg3cQb47WoPx19ugjTEoggtlo9jSJTDUv6OynoQjOxvrPhNaZBg1YfmztoisXT8N5dRyhhYHgJs0FrCUnSUDriaOZYGM1esFXbNJ06/7QyF0KdFpDOROD+U4SDZ39+3OgBEq2PxhigKI0TusFybF1RqvMuGiIVwoVL4rwwm6ibSsZK332yQPqyT4OFfufwjN0d41Z4xN9Qs42rHWl3bivPN5XyWOMZRTf3UdfwSC8KYNh5nQ/Jg8xytRbL0i4W5vpcgr0w2UeXHdt6IsuLtPY6SOGIpkY5Al9mmHQwREVvsmqJL+NPNUoPscum/rQ+MI4K8Gs3vw6oP3Bcddd8vWgJAz75NARtMtryiMLmkanMV77B+v5TQPuUNtfdHpg2/zIOGLU+ey6VgMMt9HKAk716ktulslD1wYg4gpg3PPS5a6E2dcbX9a+qVXsWHh4ZABc7FpPTo1Oi1i2t8J8XBj3PcVHSN55Ul8gutEdzMfMoSuRzNoIPWiTfRsFn4yzI+okhMO4kCidZmR6b90nnuB/mrOUOz+ip+27aFOyWrdDEQmFqb5MkV0iNATZTZwJdUExNnXpeoS6mXNgSd7NFfo=
        )

)
*/


function RequestTokenVitHadoc(){
	global $clientIDVitHadoc;	
	global $clientSecretVitHadoc;
	global $linkVitHadoc;
	global $audienceVitHadoc;

	$data_string = '{
					   "grant_type":"client_credentials",
					   "client_id":"'.$clientIDVitHadoc.'",
					   "client_secret":"'.$clientSecretVitHadoc.'",
					   "audience":"'.$audienceVitHadoc.'"
					}';


	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkVitHadoc .'/authorization/issueToken' );    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $resultado['access_token'];	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}

function CadastraClienteVitHadoc($nome,$cpf,$nascimento,$email,$endereco,$bairro,$cidade,$estado,$cep,$telefone){
	
	global $linkVitHadoc;
	global $audienceVitHadoc;
	global $bearerVitHadoc;
	
	$data_string = '{
					   "name":"'.$nome.'",
					   "cpf":"'.$cpf.'",
					   "birthday":"'.$nascimento.'",
					   "phone":"'.$telefone.'",
					   "email":"'.$email.'",
					   "zipCode":"'.$cep.'",
					   "address":"'.$endereco.' - '.$bairro.'",
					   "city":"'.$cidade.'",
					   "state":"'.$estado.'"
	                  }';


	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkVitHadoc .'/patients/signup');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer '.$bearerVitHadoc                                                           
	));             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}

function DesativaClienteVitHadoc($id){
	
	global $linkVitHadoc;
	global $audienceVitHadoc;
	global $bearerVitHadoc;
	
	$data_string = '{
					   "patientUid":"'.$id.'"
					}';


	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkVitHadoc .'/patients/deactivate');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer '.$bearerVitHadoc                                                           
	));             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}
function AtivaClienteVitHadoc($id){
	
	global $linkVitHadoc;
	global $audienceVitHadoc;
	global $bearerVitHadoc;
	
	$data_string = '{
					   "patientUid":"'.$id.'"
					}';


	$data_string = utf8_encode($data_string);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $linkVitHadoc .'/patients/reactivate');    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POST, true);                                                                   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json' ,
		'Accept: application/json' ,
		'Authorization: Bearer '.$bearerVitHadoc                                                           
	));             

	$errors = curl_error($ch);                                                                                                            
	$result = curl_exec($ch);
	


	$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);  
	
	$resultado = json_decode($result, true);
	//print_r($data_string);
	if($returnCode==200){
		$retorno['STATUS'] = 'OK';
		$retorno['DADOS']  = $resultado;
		return $retorno;	
	}else{
		$retorno['STATUS'] = 'ERRO';
		$retorno['ERROS'] = $resultado;
		return $retorno;
	}	
}


?>