<?php

require('../lib/base.php');
require('../lib/pagamentoGetNet.php');
require('../lib/registroBoletoSantander.php');

$dados = file_get_contents("php://input");
$dados = json_decode($dados, True);

$chave = Autentica();

//print_r($dados['name']);

$retorno = "";

global $jurosCartao;
$jurosCartao = false;

if($dados['JC']=='1'){
	$jurosCartao = true;
}

if($dados['FAT']=='F'){
	
	
	$sqlFatura = "select * from ps1020 where numero_registro = ".aspas($dados['REG']);
	$resFatura  = jn_query($sqlFatura);

    if($rowFatura = jn_fetch_object($resFatura)) {
		$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
		$dataVencimento = $rowFatura->DATA_VENCIMENTO;
		$valorFatura 	= $rowFatura->VALOR_FATURA;
		
		$databd     = $dataVencimento->format('d/m/Y');
		$databd     = explode("/",$databd); 
		$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
		$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$dias       = ($data_atual-$dataBol)/86400;
		$diasAtrazo = ceil($dias);

		
		
	   // pr($diasAtrazo,true);
		
		if($diasAtrazo>0){
			//pr('teste',true);
			$valor_boleto    = str_replace(",", ".", $valorFatura);
			//$multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
			//$mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
			//$multa = 2;
			//$mora  = 0.033; 
			$multa = 0.02;
			$mora  = 0.0003333; 

			$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
			//$valor_boleto_multa    =  ($valor_boleto * $multa) + $valor_boleto; 
			//$valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
			$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
			$valorBoleto = explode('.',$valor_boleto);
			$val1 = $valorBoleto[0];
			$val2 = substr($valorBoleto[1],0,2);		
			$valorAtual = $val1 . '.' . $val2;		
			$valor_boleto          =  number_format($valorAtual, 2, ',', '');
			$valorFatura = $valor_boleto;
			$data_venc=date('d/m/Y');
			
		}		
		
		
		$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
		$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
		if($codigoEmpresa == '400'){
			$sqlEndereco = "select ps1001.*,Ps1000.nome_associado NOME,Ps1000.numero_cpf DOCUMENTO from ps1001 
			inner join ps1000 on ps1000.codigo_associado = ps1001.codigo_associado
			where ps1000.codigo_associado = ".aspas($codigoAssociado);
		}else{
			$codigoAssociado = $codigoEmpresa;
			$sqlEndereco = "select ps1001.*,ps1010.NOME_EMPRESA NOME,Ps1010.NUMERO_CNPJ DOCUMENTO from ps1001 
			inner join ps1010 on ps1010.codigo_empresa = ps1001.codigo_empresa
			where ps1010.codigo_empresa = ".aspas($codigoEmpresa);
		}
		//pr($sqlEndereco);
		$resEndereco  = jn_query($sqlEndereco);
		if($rowEndereco = jn_fetch_object($resEndereco)){
			$nome = $rowEndereco->NOME;
			$documento = $rowEndereco->DOCUMENTO;
			$email = $rowEndereco->ENDERECO_EMAIL;
			$bairro = $rowEndereco->BAIRRO;
			$cidade = $rowEndereco->CIDADE;
			$estado = $rowEndereco->ESTADO;
			$cep = remove_caracteres($rowEndereco->CEP);
			$auxEndereco = $rowEndereco->ENDERECO;
			
			$auxEndereco = explode(',',$auxEndereco);
			
			$endereco = $auxEndereco[0]; 
			
			$numero = '';
			if(count($auxEndereco)>1){
				$auxEndereco = explode('-',$auxEndereco[1]);
				$numero = $auxEndereco[0]; 
				$complemento = "";
				if(count($auxEndereco)>1){
					$complemento = $auxEndereco[1];
				}
			}
		}
	}

}elseif($dados['FAT']=='FA'){

	$sqlFatura = "select PS1020.*,ESP_FATURAS_AGRUPADAS.NUMERO_REGISTRO REGISTRO_AGRUPADO  from PS1020 
				  inner join ESP_FATURAS_AGRUPADAS on ESP_FATURAS_AGRUPADAS.NUMERO_REGISTRO_PS1020 = PS1020.NUMERO_REGISTRO
				  where   ESP_FATURAS_AGRUPADAS.NUMERO_AGRUPAMENTO = ".aspas($dados['REG'])."  order by ESP_FATURAS_AGRUPADAS.NUMERO_REGISTRO DESC ";//PS1020.DATA_PAGAMENTO is null and
	$resFatura  = jn_query($sqlFatura);

	$numeroRegistro = '';
	$valorTotal = 0;
    while($rowFatura = jn_fetch_object($resFatura)) {
		
		//$numeroRegistro = $rowFatura->REGISTRO_AGRUPADO;
		$dataVencimento = $rowFatura->DATA_VENCIMENTO;
		$valorFatura 	= $rowFatura->VALOR_FATURA;
		
		$databd     = $dataVencimento->format('d/m/Y');
		$databd     = explode("/",$databd); 
		$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
		$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$dias       = ($data_atual-$dataBol)/86400;
		$diasAtrazo = ceil($dias);

		
	   // pr($diasAtrazo,true);
		
		if($diasAtrazo>0){
			
			//pr('teste',true);
			$valor_boleto    = str_replace(",", ".", $valorFatura);
			//$multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
			//$mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
			//$multa = 2;
			//$mora  = 0.033; 
			$multa = 0.02;
			$mora  = 0.0003333; 

			$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
			//$valor_boleto_multa    =  ($valor_boleto * $multa) + $valor_boleto; 
			//$valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
			$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
			$valorBoleto = explode('.',$valor_boleto);
			$val1 = $valorBoleto[0];
			$val2 = substr($valorBoleto[1],0,2);		
			$valorAtual = $val1 . '.' . $val2;		
			$valor_boleto          =  number_format($valorAtual, 2, '.', '');
			$valorFatura = $valor_boleto;
			$data_venc=date('d/m/Y');
						
		}	

		$valorTotal = $valorTotal + $valorFatura; 
		
		if($numeroRegistro == ''){
			$numeroRegistro = $rowFatura->REGISTRO_AGRUPADO;
			$dados['REG'] = $numeroRegistro; 
			$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
			$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
			if($codigoEmpresa == '400'){
				$sqlEndereco = "select ps1001.*,Ps1000.nome_associado NOME,Ps1000.numero_cpf DOCUMENTO from ps1001 
				inner join ps1000 on ps1000.codigo_associado = ps1001.codigo_associado
				where ps1000.codigo_associado = ".aspas($codigoAssociado);
			}else{
				$codigoAssociado = $codigoEmpresa;
				$sqlEndereco = "select ps1001.*,ps1010.NOME_EMPRESA NOME,Ps1010.NUMERO_CNPJ DOCUMENTO from ps1001 
				inner join ps1010 on ps1010.codigo_empresa = ps1001.codigo_empresa
				where ps1010.codigo_empresa = ".aspas($codigoEmpresa);
			}
			//pr($sqlEndereco);
			$resEndereco  = jn_query($sqlEndereco);
			if($rowEndereco = jn_fetch_object($resEndereco)){
				$nome = $rowEndereco->NOME;
				$documento = $rowEndereco->DOCUMENTO;
				$email = $rowEndereco->ENDERECO_EMAIL;
				$bairro = $rowEndereco->BAIRRO;
				$cidade = $rowEndereco->CIDADE;
				$estado = $rowEndereco->ESTADO;
				$cep = remove_caracteres($rowEndereco->CEP);
				$auxEndereco = $rowEndereco->ENDERECO;
				
				$auxEndereco = explode(',',$auxEndereco);
				
				$endereco = $auxEndereco[0]; 
				
				$numero = '';
				if(count($auxEndereco)>1){
					$auxEndereco = explode('-',$auxEndereco[1]);
					$numero = $auxEndereco[0]; 
					$complemento = "";
					if(count($auxEndereco)>1){
						$complemento = $auxEndereco[1];
					}
				}
			}
		}
	}
	
	
	$valorFatura =$valorTotal; 
	$valorFatura =number_format($valorFatura, 2, ',', '');
	
	
}else{
	
	$sqlFatura = "select * from TMP1020_NET where numero_registro = ".aspas($dados['REG']);
	$resFatura  = jn_query($sqlFatura);

    if($rowFatura = jn_fetch_object($resFatura)) {
		$numeroRegistro = $rowFatura->NUMERO_REGISTRO;
		$dataVencimento = $rowFatura->DATA_VENCIMENTO;
		$valorFatura 	= $rowFatura->VALOR_FATURA;
		
		$databd     = $dataVencimento->format('d/m/Y');
		$databd     = explode("/",$databd); 
		$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
		$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$dias       = ($data_atual-$dataBol)/86400;
		$diasAtrazo = ceil($dias);

		
		
	   // pr($diasAtrazo,true);
		
		if($diasAtrazo>0){
			//pr('teste',true);
			$valor_boleto    = str_replace(",", ".", $valorFatura);
			//$multa = $result[0]['PERCENTUAL_MULTA_PADRAO'];
			//$mora  = $result[0]['PERCENTUAL_MORA_DIARIA']; 
			//$multa = 2;
			//$mora  = 0.033; 
			$multa = 0.02;
			$mora  = 0.0003333; 

			$valor_boleto_multa    =  (round($valor_boleto * $multa,2)) + $valor_boleto; 
			//$valor_boleto_multa    =  ($valor_boleto * $multa) + $valor_boleto; 
			//$valor_boleto          =  $valor_boleto_multa + (round($valor_boleto * $mora,2) * $diasAtrazo);
			$valor_boleto          =  $valor_boleto_multa + (($valor_boleto * $mora) * $diasAtrazo);
			$valorBoleto = explode('.',$valor_boleto);
			$val1 = $valorBoleto[0];
			$val2 = substr($valorBoleto[1],0,2);		
			$valorAtual = $val1 . '.' . $val2;		
			$valor_boleto          =  number_format($valorAtual, 2, ',', '');
			$valorFatura = $valor_boleto;
			$data_venc=date('d/m/Y');
			
		}		
		
		
		$codigoAssociado= $rowFatura->CODIGO_ASSOCIADO;
		$codigoEmpresa  = $rowFatura->CODIGO_EMPRESA;
		if($codigoEmpresa == '400'){
			$sqlEndereco = "select TMP1001_NET.*,TMP1000_GETNET.nome_associado NOME,TMP1000_GETNET.numero_cpf DOCUMENTO from TMP1001_NET  
			inner join TMP1000_GETNET on TMP1000_GETNET.codigo_associado = TMP1001_NET.codigo_associado
			where TMP1000_GETNET.codigo_associado = ".aspas($codigoAssociado);
		}else{
			$codigoAssociado = $codigoEmpresa;
			$sqlEndereco = "select TMP1001_NET.*,TMP1010_NET.NOME_EMPRESA NOME,TMP1010_NET.NUMERO_CNPJ DOCUMENTO from TMP1001_NET 
			inner join TMP1010_NET on TMP1010_NET.codigo_empresa = TMP1001_NET.codigo_empresa
			where TMP1010_NET.codigo_empresa = ".aspas($codigoEmpresa);
		}
		//pr($sqlEndereco);
		$resEndereco  = jn_query($sqlEndereco);
		if($rowEndereco = jn_fetch_object($resEndereco)){
			$nome = $rowEndereco->NOME;
			$documento = $rowEndereco->DOCUMENTO;
			$email = $rowEndereco->ENDERECO_EMAIL;
			$bairro = $rowEndereco->BAIRRO;
			$cidade = $rowEndereco->CIDADE;
			$estado = $rowEndereco->ESTADO;
			$cep = remove_caracteres($rowEndereco->CEP);
			$auxEndereco = $rowEndereco->ENDERECO;
			
			$auxEndereco = explode(',',$auxEndereco);
			
			$endereco = $auxEndereco[0]; 
			
			$numero = '';
			if(count($auxEndereco)>1){
				$auxEndereco = explode('-',$auxEndereco[1]);
				$numero = $auxEndereco[0]; 
				$complemento = "";
				if(count($auxEndereco)>1){
					$complemento = $auxEndereco[1];
				}
			}
		}
	}


}


if($dados['T']=='C'){
	$retorno = TokenCartao($dados['C'],$chave);
	echo json_encode($retorno);
}

if($dados['T']=='CD'){
	$dados['CN'] = remove_caracteres($dados['CN']);
	$dados['CT'] = remove_caracteres($dados['CT']);
	if($jurosCartao){
		$valorFatura = str_replace(",", ".", $valorFatura);
		$valorFatura = number_format(($valorFatura*(2/100))+$valorFatura,2);
	}
	$valor 		 = trim($valorFatura);
	$valor 		 = str_replace(".", "", $valor);
	$valor 		 = str_replace(",", "", $valor);
	$dados['CE'] = explode('/',$dados['CE']);
	
	$resultadoPagamentoDebito =  PagamentoDebito($valor,$dados['FAT'].'-'.$dados['REG'],$codigoAssociado,$nome,$email,$documento,$dados['CT'],$endereco,$numero,$complemento,$bairro,$cidade,$estado,$cep,$dados['CTC'],$dados['CB'],$dados['CN'],$dados['CE'][0],$dados['CE'][1],$dados['CV'],$dados['CT'],$chave);
	
	echo json_encode($resultadoPagamentoDebito);
}

if($dados['T']=='FCD'){
	
	$sql = "select * from ESP_GETNET where aux_busca = ".aspas($dados['ID']). " order by numero_registro desc ";
	$res  = jn_query($sql);
	if($row = jn_fetch_object($res)){
		$retorno = json_decode($row->RETORNO,true);
		//$retorno['AG']    = 'CANCEL';
		//$retorno['ID']    = $dados['ID'];
		//$retorno['PARES'] = $retorno['PaRes'];
		//$retorno['IDC']   = $chave;
		//print_r($retorno);
		$retorno = FinalizaPagamentoDebito($retorno['PaRes'],$dados['ID'],$chave);
		//print_r($retorno);
		$retorno = (array)$retorno;
		
		$retorno['debit'] = (array)$retorno['debit'];
		$retorno['AG'] = 'CANCEL';
		if(strtoupper($retorno['status'])=='APPROVED'){
			$insert  = "Insert into ESP_GETNET(TIPO_PAGAMENTO,CODIGO_CLIENTE,LOCAL_DADO,CODIGO_RETORNO,RETORNO,AUX_BUSCA,ID_PEDIDO,DATA_EXEC)
						values(".aspas('DEPOISFINALIZARDEBITO').",".aspas('').",".aspas('retornoPagamentoDebito.php').",".aspas('').",".aspas(json_encode($retorno)).",".aspas($retorno['payment_id']).",".aspas($retorno['order_id']).",GETDATE())";
			
			$res  = jn_query($insert,false,false);
			
			$dadoBoleto = explode('-',$retorno['order_id']);
			efetuaBaixa('CD',$dadoBoleto[0],$dadoBoleto[1],$retorno['amount'],$retorno['debit']['terminal_nsu']);
				
			
			//echo '{MSG:"OK}';
		}
	}else{
		$retorno['AG'] = 'OK';
	}
	echo json_encode($retorno);
}

if($dados['T']=='CC'){
	$dados['CN'] = remove_caracteres($dados['CN']);
	$dados['CT'] = remove_caracteres($dados['CT']);
	
	if(($jurosCartao)and($dados['CP']==1)){
		$valorFatura 		 = str_replace(",", ".", $valorFatura);
		$valorFatura =number_format( ($valorFatura*(3/100))+$valorFatura,2);
		
	}

	$valor 		 = trim($valorFatura);
	$valor 		 = str_replace(".", "", $valor);
	$valor 		 = str_replace(",", "", $valor);
	$dados['CE'] = explode('/',$dados['CE']);
	
	$resultadoPagamentoCredito =  PagamentoCredito($valor,$dados['FAT'].'-'.$dados['REG'],$codigoAssociado,$nome,$email,$documento,$dados['CT'],$endereco,$numero,$complemento,$bairro,$cidade,$estado,$cep,$dados['CTC'],$dados['CB'],$dados['CN'],$dados['CE'][0],$dados['CE'][1],$dados['CV'],$chave,$dados['CP']);
								 //PagamentoCredito('100.00','B-123','1','João da Silva','email@email.com','00206410107','1111','Rua Abc','100','apt 401','Centro','Curitiba','PR','80060140',$chaveCartao,"mastercard","JOAO DA SILVA","10","20","123",$chave);
	
	if($resultadoPagamentoCredito['status_code']== 200){
		if($resultadoPagamentoCredito['status'] == 'APPROVED'){
			//efetuaBaixa($tipoPagamento,$tipoFatura,$registro,$valor)
			efetuaBaixa('CC',$dados['FAT'],$dados['REG'],$resultadoPagamentoCredito['amount'],$resultadoPagamentoCredito['credit']['terminal_nsu']);
		}
	}
	
	echo json_encode($resultadoPagamentoCredito);
}

if($dados['T']=='BO'){


	$date = new DateTime('+2 day');
	//echo $date->format('d/m/Y');
	//exit;
	
	$valor 		 = trim($valorFatura);
	$valor 		 = str_replace(".", "", $valor);
	$valor 		 = str_replace(",", "", $valor);
	
	$resultadoPagamentoBoleto =  PagamentoBoleto($valor,$dados['FAT'].'-'.$dados['REG'],$codigoAssociado,$nome,$email,$documento,'',$endereco,$numero,$complemento,$bairro,$cidade,$estado,$cep,'','','','','','',$date->format('d/m/Y'),$chave);
								 //PagamentoCredito('100.00','B-123','1','João da Silva','email@email.com','00206410107','1111','Rua Abc','100','apt 401','Centro','Curitiba','PR','80060140',$chaveCartao,"mastercard","JOAO DA SILVA","10","20","123",$chave);
	

	echo json_encode($resultadoPagamentoBoleto);


}
if($dados['T']=='BOVOS'){
	$retorno['boleto'] = CriaBoleto($dados['REG']);
	echo json_encode($retorno);
}



//

//$chaveCartao = TokenCartao('5155901222280001',$chave);

//echo '{"chave":"'.$chaveCartao.'"}';


//$verificacaoCartao = VerificaCartao($chaveCartao,"mastercard","JOAO DA SILVA","10","18","123",$chave);

/*
$resultadoPagamentoCredito =  PagamentoCredito('100.00','B-123','1','João da Silva','email@email.com','00206410107','1111','Rua Abc','100','apt 401','Centro','Curitiba','PR','80060140',$chaveCartao,"mastercard","JOAO DA SILVA","10","20","123",$chave);

if($resultadoPagamentoCredito['status']=='APPROVED'){
	//pagamentoEfetuado
}
*/

//$resultadoPagamentoBoleto =  PagamentoBoleto('100,00','B-123','1','João da Silva','email@email.com','00206410107','1111','Rua Abc','100','apt 401','Centro','Curitiba','PR','80060140',$chaveCartao,"mastercard","JOAO DA SILVA","10","20","123",'16/06/2019',$chave);

//if($resultadoPagamentoBoleto['status']=='PENDING'){
//	echo "<a href='".$link.'/'.$resultadoPagamentoBoleto['boleto']['_links'][0]['href']."'>Baixar</a>";
//}

//print_r($resultadoPagamentoBoleto);
//echo "<br>";

/*

$resultadoPagamentoDebito =  PagamentoDebito('550.00','B-'.rand(),'1','João da Silva','email@email.com','00206410107','1111','Rua Abc','100','apt 401','Centro','Curitiba','PR','80060140',$chaveCartao,"visa","JOAO DA SILVA","10","20","123",'5551999887766',$chave);
if($resultadoPagamentoDebito['redirect_url']!= ''){
	echo '<form enctype="application/x-www-form-urlencoded" method="POST" action="'.$resultadoPagamentoDebito['redirect_url'].'" name="FormBD" id="FormBD" target="_blank">
				<input type="hidden" name="MD"      id="MD" value="'.$resultadoPagamentoDebito['post_data']['issuer_payment_id'].'" />
				<input type="hidden" name="PaReq"   id="" value="'.$resultadoPagamentoDebito['post_data']['payer_authentication_request'].'" />
				<input type="hidden" name="TermUrl" id="" value="https://aliancaweb.azurewebsites.net/AliancaNet/services/retornoPagamentoDebito.php?ID='.$resultadoPagamentoDebito['payment_id'].'" />
		   </form>';
	echo "<script>document.FormBD.submit();</script>";
}

/*


/*
echo "<br>";
var_dump($chave);
echo "<br>";
var_dump($chaveCartao);
echo "<br>";
var_dump($verificacaoCartao);
echo "<br>";
var_dump($resultadoPagamentoCredito);
echo "<br>";
*/



?>



