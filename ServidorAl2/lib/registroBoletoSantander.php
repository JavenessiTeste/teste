<?php

//setAmbinteProducao(false);

//print_r(RegistraBoleto('002','400','Diego Ribeiro da Roza','00206410107','RUaa SEte de Setembro,366','Centro','PONTA PORa','79904682','MS','10/09/2021',10.15,'CARTAODEMAIS.pem','12345678','0029525','PE4P','003',9));

//Array ( [STATUS] => OK [DADOS] => Array ( [codcede] => 000029525 [convenio] => Array ( [codBanco] => 0033 [codConv] => 000029525 ) [descricaoErro] => 00000 - Título registrado em cobrança [dtNsu] => 27092021 [estacao] => PE4P [nsu] => TST0000003 [pagador] => Array ( [bairro] => Centro [cep] => 79904682 [cidade] => PONTA PORa [ender] => RUaa SEte de Setembro,366 [nome] => Diego Ribeiro da Roza [numDoc] => 000000206410107 [tpDoc] => 01 [uf] => MS ) [situacao] => 00 [titulo] => Array ( [aceito] => N [cdBarra] => 03395875900000010159002952500000000040240101 [codPartilha1] => [codPartilha2] => [codPartilha3] => [codPartilha4] => [dtEmissao] => 27092021 [dtEntr] => 27092021 [dtLimiDesc] => [dtLimiDesc2] => 0001-01-01 [dtLimiDesc3] => 0001-01-01 [dtVencto] => 30092021 [especie] => 02 [linDig] => 03399002925250000000600402401012587590000001015 [mensagem] => Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento. [nomeAvalista] => [nossoNumero] => 0000000004024 [numDocAvalista] => 000000000000000 [pcIof] => 00000000 [pcJuro] => 00000 [pcMulta] => 00000 [qtDiasBaixa] => 01 [qtDiasMulta] => 00 [qtDiasProtesto] => 00 [qtdParciais] => 00 [seuNumero] => 900000000000003 [tipoPagto] => 0 [tipoValor] => 0 [tpDesc] => 0 [tpDocAvalista] => 00 [tpProtesto] => 0 [valorMaximo] => 00000000000000000 [valorMinimo] => 00000000000000000 [vlAbatimento] => 000000000000000 [vlDesc] => 000000000000000 [vlDesc2] => 000000000000000 [vlDesc3] => 000000000000000 [vlNominal] => 000000000001015 [vlPartilha1] => 000000000000000 [vlPartilha2] => 000000000000000 [vlPartilha3] => 000000000000000 [vlPartilha4] => 000000000000000 ) [tpAmbiente] => T ) )
//Array ( [STATUS] => ERRO [DADOS] => Array ( [codcede] => [convenio] => Array ( [codBanco] => 0033 [codConv] => 000029525 ) [descricaoErro] => 00100-DATA EMISSAO MAIOR QUE A DATA VENCIMENTO [dtNsu] => 27092021 [estacao] => PE4P [nsu] => TST0000003 [pagador] => Array ( [bairro] => Centro [cep] => 79904682 [cidade] => PONTA PORa [ender] => RUaa SEte de Setembro,366 [nome] => Diego Ribeiro da Roza [numDoc] => 000000206410107 [tpDoc] => 01 [uf] => MS ) [situacao] => 20 [titulo] => Array ( [aceito] => N [cdBarra] => [codPartilha1] => [codPartilha2] => [codPartilha3] => [codPartilha4] => [dtEmissao] => 27092021 [dtEntr] => [dtLimiDesc] => [dtLimiDesc2] => 0001-01-01 [dtLimiDesc3] => 0001-01-01 [dtVencto] => 10092021 [especie] => 02 [linDig] => [mensagem] => Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento. [nomeAvalista] => [nossoNumero] => 0000000000000 [numDocAvalista] => 000000000000000 [pcIof] => 00000000 [pcJuro] => 00000 [pcMulta] => 00000 [qtDiasBaixa] => 01 [qtDiasMulta] => 00 [qtDiasProtesto] => 00 [qtdParciais] => 00 [seuNumero] => 900000000000003 [tipoPagto] => 0 [tipoValor] => 0 [tpDesc] => 0 [tpDocAvalista] => 00 [tpProtesto] => 0 [valorMaximo] => 00000000000000000 [valorMinimo] => 00000000000000000 [vlAbatimento] => 000000000000000 [vlDesc] => 000000000000000 [vlDesc2] => 000000000000000 [vlDesc3] => 000000000000000 [vlNominal] => 000000000001015 [vlPartilha1] => 000000000000000 [vlPartilha2] => 000000000000000 [vlPartilha3] => 000000000000000 [vlPartilha4] => 000000000000000 ) [tpAmbiente] => T ) )

//$retorno['DADOS']['titulo'];
function setAmbinteProducao($valor){
	global $ambienteProducaoXmlSantander;
	
	if($valor)
		$ambienteProducaoXmlSantander = 'P';
	else
		$ambienteProducaoXmlSantander = 'T';
}


function arrayUtf8(&$dados){
	foreach ($dados as $key => $value){
			if(gettype($value)=='array'){
				$dados[$key] = arrayUtf8($value);
			}else{
				$dados[$key] = ($value);
			}
	}
	return $dados;
}
	

  function createEntry($key, $value)
  {
      $toReturn = array('key'   => $key,
                        'value' => $value);
      return $toReturn;
  }

  function getTicketXml($codigoAssociado,$codigoEmpresa,$nome,$documento,$endereco,$bairro,$cidade,$cep,$estado,$dataVencimento,$valorFatura,$convenio,$estacao,$seuNumero,$prefixoSeuNumero='0')
  {
		$dataVencimento = str_replace('/','',$dataVencimento);
		
		$valorFatura 	= str_replace('.','',str_replace(',','',number_format($valorFatura,2)));
		$valorFatura    = str_pad($valorFatura, 15, '0', STR_PAD_LEFT );
		
		//$nossoNumero = str_pad('0', 13, '0', STR_PAD_LEFT );
		$nossoNumero = $prefixoSeuNumero.str_pad(intval($seuNumero), 11, '0', STR_PAD_LEFT );
		$nossoNumero = $nossoNumero.modulo_11_D($nossoNumero);
		$seuNumero   = $prefixoSeuNumero.str_pad(intval($seuNumero), 14, '0', STR_PAD_LEFT );
		
		if($codigoAssociado != ''){
			$tipoDoc = '01';
		}else{
			$tipoDoc = '02';
		}
		
		
      $dados = array(createEntry('CONVENIO.COD-BANCO', '0033'), 
                      createEntry('CONVENIO.COD-CONVENIO', $convenio), 
                      
                      createEntry('PAGADOR.TP-DOC', $tipoDoc),
                      createEntry('PAGADOR.NUM-DOC', $documento),
                      createEntry('PAGADOR.NOME', $nome),
                      createEntry('PAGADOR.ENDER', $endereco),
                      createEntry('PAGADOR.BAIRRO', $bairro),
                      createEntry('PAGADOR.CIDADE', $cidade),
                      createEntry('PAGADOR.UF', $estado),
                      createEntry('PAGADOR.CEP', $cep),
                      
                      createEntry('TITULO.NOSSO-NUMERO', $nossoNumero),
                      createEntry('TITULO.SEU-NUMERO',   $seuNumero),
                      createEntry('TITULO.DT-VENCTO', $dataVencimento),
                      createEntry('TITULO.DT-EMISSAO', date("dmY")), 
                      createEntry('TITULO.ESPECIE', '02'),
                      createEntry('TITULO.VL-NOMINAL', $valorFatura),
                      createEntry('TITULO.PC-MULTA', '000'),
                      createEntry('TITULO.QT-DIAS-MULTA', '00'),
                      createEntry('TITULO.PC-JURO', '000'),
                      createEntry('TITULO.TP-DESC', '0'),
                      createEntry('TITULO.VL-DESC', '000'),
                      createEntry('TITULO.DT-LIMI-DESC', '00000000'),
                      createEntry('TITULO.VL-DESC2', '000'),
                      createEntry('TITULO.DT-LIMI-DESC2', '00000000'),
                      createEntry('TITULO.VL-DESC3', '000'),
                      createEntry('TITULO.DT-LIMI-DESC3', '00000000'),
                      createEntry('TITULO.VL-ABATIMENTO', '000'),
                      createEntry('TITULO.TP-PROTESTO', '0'),
                      createEntry('TITULO.QT-DIAS-PROTESTO', '00'),
                      createEntry('TITULO.QT-DIAS-BAIXA', '1'),
                      createEntry('TITULO.TP-PAGAMENTO', ''),
                      createEntry('TITULO.QT-PARCIAIS', ''),
                      createEntry('TITULO.TP-VALOR', ''),
                      createEntry('TITULO.VL-PERC-MINIMO', ''),
                      createEntry('TITULO.QT-PERC-MAXIMO', ''),
                      createEntry('TITULO.TP-DOC-AVALISTA', ''),
                      createEntry('TITULO.NUM-DOC-AVALISTA', ''),
                      createEntry('TITULO.NOME-AVALISTA', ''),
                      createEntry('TITULO.COD-PARTILHA1', ''),
                      createEntry('TITULO.VL-PARTILHA1', ''),
                      createEntry('TITULO.COD-PARTILHA2', ''),
                      createEntry('TITULO.VL-PARTILHA2', ''),
                      createEntry('TITULO.COD-PARTILHA3', ''),
                      createEntry('TITULO.VL-PARTILHA3', ''),
                      createEntry('TITULO.COD-PARTILHA4', ''),
                      createEntry('TITULO.VL-PARTILHA4', ''),
                      
                      
                      createEntry('MENSAGEM', 'Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento.')
                     );
					 
	  	  
      
      $ticketRequest = array('dados'     => $dados,
                             'expiracao' => 100,
                             'sistema'   => 'YMB');

      $toReturn = array('TicketRequest' => $ticketRequest);
      
      return $toReturn;
	
  }

  function getRegistroXml($ticket,$registro,$estacao)
  {
	  global $ambienteProducaoXmlSantander;
	  
	  
	  
	  $nsu = str_pad(substr($registro,-10), 10, '0', STR_PAD_LEFT );
	  
	  if($ambienteProducaoXmlSantander == 'T') 
		$nsu = 'TST'.substr($nsu,-7);
  
      $inclusaoTitulo = array('dtNsu'      => date("dmY"),//'12112018', //INSERIR DATA DO DIA DO REGISTRO (EX: 12/07/2018 -> 12072018)
                              'estacao'    => $estacao, //INSERIR CÓDIGO DA ESTAÇÃO
                              'nsu'        => $nsu, 
                              'ticket'     => $ticket,
                              'tpAmbiente' => $ambienteProducaoXmlSantander
                             );
      
      $toReturn = array('dto' => $inclusaoTitulo);
      
      return $toReturn;
  }

  function RegistraBoleto($codigoAssociado,$codigoEmpresa,$nome,$documento,$endereco,$bairro,$cidade,$cep,$estado,$dataVencimento,$valorFatura,$caminhoCertificado,$senhaCertificado,$convenio,$estacao,$seuNumero,$prefixoSeuNumero='0')
  {
		 $retorno = array();
		  try
		  {
			  
			$options = array('keep_alive' => false,
							'trace'      => true,
							'local_cert' => ($caminhoCertificado), // substituir pelo caminho do certificado
							'passphrase' => $senhaCertificado,   // substituir pela senha do certificado
							'cache_ws'   => WSDL_CACHE_NONE,
							);          
			  //pr($options);
			  $cliTicket = new SoapClient("https://ymbdlb.santander.com.br/dl-ticket-services/TicketEndpointService/TicketEndpointService.wsdl", $options);
			  //echo ("CHAMANDO O DLB TICKET!!");                             
			  // ticket
			  //PRINT_R($cliTicket);
			  
			  $xmlCreate = getTicketXml($codigoAssociado,$codigoEmpresa,$nome,$documento,$endereco,$bairro,$cidade,$cep,$estado,$dataVencimento,$valorFatura,$convenio,$estacao,$seuNumero,$prefixoSeuNumero);
			  $cResponse = $cliTicket->create($xmlCreate); 

			  // cobrança
			  $cliCobranca = new SoapClient("https://ymbcash.santander.com.br/ymbsrv/CobrancaV3EndpointService/CobrancaV3EndpointService.wsdl", $options);
			  $xmlRegistro = getRegistroXml($cResponse->TicketResponse->ticket,$seuNumero,$estacao);
			  $rResponse   = $cliCobranca->registraTitulo($xmlRegistro);

			  // Imprime no browser:
			  //print_r($xmlCreate);
			  //echo '<br><br>';
			  //print_r($xmlRegistro);
			  //echo '<br><br>';
			  //print_r($cResponse);
			  //echo '<br><br>';
			  
			  //print_r(json_encode($rResponse));
			  $rResponse= (array)$rResponse;
			  $rResponse['return'] = (array)$rResponse['return'];
			  $rResponse['return']['convenio'] = (array)$rResponse['return']['convenio'];
			  $rResponse['return']['pagador'] = (array)$rResponse['return']['pagador'];
			  $rResponse['return']['titulo'] = (array)$rResponse['return']['titulo'];
			  //echo '<br><br>';
			  //print_r($rResponse);
			  $retorno['STATUS'] = 'ERRO';
			  if($rResponse['return']['descricaoErro']=='00000 - Título registrado em cobrança'){
				//echo 'Boleto Registrado';
				 $retorno['STATUS'] = 'OK';
				
			  }
			  $retorno['DADOS'] =$rResponse['return'];
			  //$encodedArray = array_map(utf8_encode, $rResponse);
			  //echo '<br><br>';
			  $rResponse = arrayUtf8($rResponse);
			  //print_r($rResponse);
			  $retornoBanco = (json_encode($rResponse));
			  //echo '<br><br>';
			  $retornoBanco = (str_replace('\u','|__|',$retornoBanco));

			 
		  }
		  catch(SoapFault $e)
		  {
			  $retorno['STATUS']  = 'ERRO';
			  var_dump($e);
			  
		  }
		  
		  return $retorno;
	 
  }

  function modulo_11_D($num, $base=9, $r=0)  {

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);
        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;
        // Soma dos digitos
        $soma += $parcial[$i];
        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }
        $fator++;
    }

    /* Calculo do modulo 11 */
    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1){
        $resto = $soma % 11;
        return $resto;
    }
}

?>
