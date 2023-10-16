<?php 
require('../lib/base.php');

 $dados = file_get_contents("php://input");
 $dados = json_decode($dados, True);

 print_r($dados);
 exit;

if(isset($_GET['Teste']) and isset($_GET['API'])){

    if($_GET['API'] == 'ZsPay'){

        require('../lib/zsPay.php');
        global $tokenZsPay;

        $tokenZsPay = retornaValorConfiguracao('TOKEN_ZSPAY');
                
        if($_GET['Teste'] == 'Cria_Cliente'){
            
            $dadosEndTeste = Array();
            $dadosEndTeste['endereco'] = 'RUA PEDRO TESTE';
            $dadosEndTeste['numero'] = '12';
            $dadosEndTeste['complemento'] = 'APTO 1000';
            $dadosEndTeste['cep'] = '81050222';
            $dadosEndTeste['cidade'] = 'CURITIBA';
            $dadosEndTeste['estado'] = 'PR';
            
            $retornoCliente = cria_cliente_zspay('LEONARDO WITZEL', '70104372036', '1993-06-23', 'leonardo@javenessi.com.br', '41996000443', 'M', $dadosEndTeste);	
            print_r($retornoCliente);
            //Array ( [CODE] => 200 [STATUS] => OK [ID] => 26592465 ) 
            //Array ( [CODE] => 202 [STATUS] => ERRO [ERROS] => Já existe um cliente cadastrado com esse documento. [ID] => 26585342 )
        }

        if($_GET['Teste'] == 'Vincula_Cartao'){		
            $retornoCartao = vincula_cartao_zspay('27311673', '5476311117050224', 'LEONARDO G WITZEL', '10/2024', '923');		
            print_r($retornoCartao);
            //Array ( [STATUS] => OK [ID] => 26361670 [DIG_FINAIS] => 0224 ) 
        }

        if($_GET['Teste'] == 'Cria_Plano'){		
            $retornoPlano = cria_plano_zspay('160', 'leonardo@javenessi.com.br');
            print_r($retornoPlano);
            //Array ( [STATUS] => OK [planoId] => 4605 ) 
        }
        
        if($_GET['Teste'] == 'Cria_Assinatura'){
            
            $dadosEndTeste = Array();
            $dadosEndTeste['endereco'] = 'RUA PEDRO TESTE';
            $dadosEndTeste['numero'] = '12';
            $dadosEndTeste['complemento'] = 'APTO 1000';
            $dadosEndTeste['cep'] = '81050222';
            $dadosEndTeste['cidade'] = 'CURITIBA';
            $dadosEndTeste['estado'] = 'PR';	

            $dadosCartaoTest = Array();
            $dadosCartaoTest['nomeCartao'] = 'LEONARDO G WITZEL';
            $dadosCartaoTest['validadeCartao'] = '10/2024';
            $dadosCartaoTest['numeroCartao'] = '5476311117050224';
            $dadosCartaoTest['cvvCartao'] = '923';		
            
            //certo
            $retornoAssinatura = cria_assinatura_zspay('4602', 'LEONARDO GONCALVES WITZEL', 'leonardo@javenessi.com.br', '1993-06-23', '31165908000', '41996000443', $dadosEndTeste, $dadosCartaoTest);	
            //Array ( [STATUS] => OK [assinaturaId] => 5084 ) 
            
            //error
            //$retornoAssinatura = cria_assinatura_zspay('4603', 'LEONARDO WITZEL', 'leonardo@javenessi.com.br', '1993-06-23', '62026412038', '41996000443', $dadosEndTeste, $dadosCartaoTest);	

            print_r($retornoAssinatura);
        }

        if($_GET['Teste'] == 'Cria_Venda_CC'){
            
            $retornoVendaCC = cria_venda_cartao_credito('26585342', '26355693 ', '200', 1);
            print_r($retornoVendaCC);
            //Array ( [STATUS] => ERRO [ERROS] => Transação não autorizada. Para mais informações, entre em contato com seu banco. ) 
        }

        if($_GET['Teste'] == 'Cria_Venda_Boleto'){
            
            //certo
            $retornoVendaBB = cria_venda_boleto('26585342', '2023-12-12 ', '900', 'Boleto Teste');
            //Array ( [STATUS] => OK [idVenda] => 67600527 [urlBoleto] => https://api-boleto-production.s3.amazonaws.com/28f64c9e721c4eb89feff1791380a547/8149c24f0a144fdd8376a47a11e3d3f8/64b975a9db966c0c1e9a1fc4.html ) 


            //errado
            //$retornoVendaBB = cria_venda_boleto('26585349', '2023-12-12 ', '900', 'Boleto Teste');
            print_r($retornoVendaBB);
        }

        if($_GET['Teste'] == 'Cria_Webhook'){
                        
            $retornoWebhook = cria_webhook_zspay('http://pg.somarmaissaude.com/SomarMaisSaude/ServidorAl2/services/retornoZsPay.php');
            print_r($retornoWebhook);
            //Array ( [CODE] => 200 [STATUS] => OK ) 
        }

        if($_GET['Teste'] == 'Listar_Webhook'){

            $retornoListWebhook = listar_webhook_zspay();
            print_r($retornoListWebhook);
        }

        if($_GET['Teste'] == 'Remover_Webhook'){

            $retornoListWebhook = remover_webhook_zspay($_GET['idWebhook']);
            print_r($retornoListWebhook);
        }

        
        if($_GET['Teste'] == 'excluir_cliente'){
            $retornoExcluirCliente = excluir_cliente_zspay($_GET['idCliente']);
            print_r($retornoExcluirCliente);
        }
        

    }

    if($_GET['API'] == 'Doc24'){

        require('../lib/doc24.php');

        if($_GET['Teste'] == 'CRIA_TOKEN'){                        
            global $tokenDoc24;

            $homolDoc24 = retornaValorConfiguracao('HOMOLOGACAO_DOC24');    
            $clientId = retornaValorConfiguracao('CLIENT_ID_DOC24');
            $clientSecret = retornaValorConfiguracao('CLIENT_SEC_DOC24');

            $retornaSessaoDoc24 = gera_token_doc24($clientId, $clientSecret);
            $tokenDoc24 = $retornaSessaoDoc24['TOKEN'];

            print_r($retornaSessaoDoc24);
            //Array ( [CODE] => 200 [TOKEN] => ---------- ) 
        }

        if($_GET['Teste'] == 'SESSAO'){
            global $tokenDoc24, $homolDoc24;
            
            $homolDoc24 = retornaValorConfiguracao('HOMOLOGACAO_DOC24');           
            $clientId = retornaValorConfiguracao('CLIENT_ID_DOC24');
            $clientSecret = retornaValorConfiguracao('CLIENT_SEC_DOC24');

            $retornaSessaoDoc24 = cria_sessao_usuario_doc24('LEONARDO', '06435031967', 'leonardo@javenessi.com.br', $clientId, $clientSecret);
            print_r($retornaSessaoDoc24);
            //Array ( [CODE] => 200 [STATUS] => OK [ID_SESSION] => 29235 [LINK] => https://aaaaaaaaa [HORARIO] => 2023-07-24 13:30:26 ) 
        }
    }

    if($_GET['API'] == 'Epharma'){
        require('../lib/epharma.php');
        
        global $token;
        $dadosAuth = Array();
        $dadosAuth['clientId'] = retornaValorConfiguracao('CLIENT_ID_EPHARMA');
        $dadosAuth['clientSecret'] = retornaValorConfiguracao('CLIENT_SECRET_EPHARMA');
        $dadosAuth['username'] = retornaValorConfiguracao('USERNAME_EPHARMA');
        $dadosAuth['password'] = retornaValorConfiguracao('PASSWORD_EPHARMA');
        $homolEpharma = retornaValorConfiguracao('HOMOLOGACAO_EPHARMA');
        
        if($_GET['Teste'] == 'Autentica'){
            
            $retornoAutentica = autentica_epharma($dadosAuth, $homolEpharma);
            $token = $retornoAutentica['token'];
            print_r($token);

        }elseif($_GET['Teste'] == 'CartaoCliente' or $_GET['Teste'] == 'CartaoEpharma' ){

            if($token == ''){               
                $retornoAutentica = autentica_epharma($dadosAuth, $homolEpharma);
                $token = $retornoAutentica['token'];
            }            

            $dadosCliente = Array();
            $dadosCliente['numeroCpf'] = '85208164019';
            $dadosCliente['dtInicioVigencia'] = date('d/m/Y');
            $dadosCliente['tipoAssociado'] = 'T';
            $dadosCliente['nome'] = 'TESTE JN';
            $dadosCliente['dtNascimento'] = '23/06/1993';
            $dadosCliente['sexo'] = 'M';
            $codigoEpharma = '137526';

            if($_GET['Teste'] = 'CartaoCliente'){
                $retornoCartaoCliente = movimentacaoCartaoCliente($token, $homolEpharma, $codigoEpharma, $dadosCliente);
                print_r($retornoCartaoCliente);
            }elseif($_GET['Teste'] = 'CartaoEpharma'){
                $retornoCartaoEpharma = movimentacaoCartaoEpharma($token, $homolEpharma, $codigoEpharma, $dadosCliente);
                print_r($retornoCartaoEpharma);
            }
            

        }  
    }

    if($_GET['API'] == 'RMS'){                                
        require('../lib/redeMais.php');
           
        global $tokenRedeMais, $homolRedeMais, $idCliente, $idClienteRedeMais, $tipoPlanoRMS;      

        if(retornaValorConfiguracao('HOMOLOGACAO_RMS') == 'SIM')
            $homolRedeMais = true;

        $tokenRedeMais = retornaValorConfiguracao('TOKEN_RMS');
        $idCliente = retornaValorConfiguracao('ID_CLIENTE_RMS');
        $idClienteRedeMais = retornaValorConfiguracao('ID_CLIENTE_CONTATO_RMS');
        $tipoPlanoRMS = $_GET['tpPlano'];

        $dadosCliente = Array();
        $dadosCliente['codigoAssoc'] = '06090537101';
        $dadosCliente['numeroCpf'] = '06090537101';
        $dadosCliente['dtInicioVigencia'] = date('d/m/Y');
        $dadosCliente['tipoAssociado'] = 'T';
        $dadosCliente['nome'] = 'TESTE JN';
        $dadosCliente['dtNascimento'] = '23/06/1993';
        $dadosCliente['sexo'] = 'M';
        $dadosCliente['email'] = 'leonardo@javenessi.com.br';    
        
        $dadosEndereco = Array();
        $dadosEndereco['endereco'] = 'RUA PEDRO TESTE';
        $dadosEndereco['numero'] = '12';
        $dadosEndereco['complemento'] = 'APTO 1000';
        $dadosEndereco['cep'] = '81050222';
        $dadosEndereco['cidade'] = 'CURITIBA';
        $dadosEndereco['estado'] = 'PR';

        if($_GET['Teste'] == 'ADESAO'){            
            $retornoAdesao = adesao_rede_mais($dadosCliente['codigoAssoc'], $dadosCliente['nome'], $dadosCliente['numeroCpf'], $dadosCliente['numeroCpf'], $dadosCliente['dtNascimento'], $dadosCliente['email'], '41996120443', $dadosCliente['sexo'], 'T', $dadosEndereco);                    
            print_r($retornoAdesao);
        }

        if($_GET['Teste'] == 'CANCELAMENTO'){
            $retornoCancelamento = cancelamento_rede_mais($dadosCliente['numeroCpf']);
            print_r($retornoCancelamento);
        }

        if($_GET['Teste'] == 'REDE_CREDENCIADA'){
            $retornoRedeCredenciada = rede_credenciada_rede_mais($dadosCliente['numeroCpf'], 1, 'https://portal.somarmaissaude.com/SomarMaisSaude/ServidorAl2/services/requisicoes_payload.php?API=RMS&tp=callback');
            print_r($retornoRedeCredenciada);
        }

        if($_GET['Teste'] == 'INATIVACAO'){
            $retornoCancelamento = inativacao_rede_mais($dadosCliente['numeroCpf'], $dadosCliente['codigoAssoc']);
            print_r($retornoCancelamento);
        }

        if($_GET['Teste'] == 'ATIVACAO'){
            $retornoCancelamento = ativacao_rede_mais($dadosCliente['numeroCpf'], $dadosCliente['codigoAssoc']);
            print_r($retornoCancelamento);
        }

    }

    if($_GET['API'] == 'IGS'){
        
        require('../lib/igs.php');
           
        global $tokenIGS, $homolIGS;
        
        if(retornaValorConfiguracao('HOMOLOGACAO_IGS') == 'SIM'){
            $homolIGS = true;
        }else{
            $homolIGS = false;
        }

        if($_GET['Teste'] == 'AUTENTICA'){                        

            $dadosAuth = Array();
            $dadosAuth['service']   = retornaValorConfiguracao('AUTH_SERVICE_IGS');
            $dadosAuth['auth_key']  = retornaValorConfiguracao('AUTH_NAME_IGS');
            $dadosAuth['username']  = retornaValorConfiguracao('AUTH_USERNAME_IGS');
            $dadosAuth['password']  = retornaValorConfiguracao('AUTH_PASS_IGS');                        
            
            $retornoAutentica = autentica_igs($dadosAuth);            
        }        

        if($_GET['Teste'] == 'adesao_customer'){

            
            if($tokenIGS == ''){               
                $dadosAuth = Array();
                $dadosAuth['service']   = retornaValorConfiguracao('AUTH_SERVICE_IGS');
                $dadosAuth['auth_key']  = retornaValorConfiguracao('AUTH_NAME_IGS');
                $dadosAuth['username']  = retornaValorConfiguracao('AUTH_USERNAME_IGS');
                $dadosAuth['password']  = retornaValorConfiguracao('AUTH_PASS_IGS');                        
                
                $retornoAutentica = autentica_igs($dadosAuth);                
                $dadosAuthToken['user_id'] = $retornoAutentica['user_id'];
                $dadosAuthToken['token'] = $retornoAutentica['token'];                  

            }            

            $dadosCliente = Array();
            $dadosCliente['numeroCpf'] = '85208164019';
            $dadosCliente['dtInicioVigencia'] = date('Y-m-d');            
            $dadosCliente['dtIFimVigencia'] = '2099-12-30';
            $dadosCliente['tipoAssociado'] = 'T';
            $dadosCliente['nome'] = 'TESTE JAVE NESSI';
            $dadosCliente['apelido'] = 'JAVE';
            $dadosCliente['dtNascimento'] = '23/06/1993';
            $dadosCliente['sexo'] = 'M';      
            $dadosCliente['produto'] =  retornaValorConfiguracao('PRODUTO_IGS');                  

            $telefone = '41996120443';

            $dadosEndereco = Array();
            $dadosEndereco['email'] = 'leonardo@javenessi.com.br';
            $dadosEndereco['endereco'] = 'RUA PEDRO TESTE';
            $dadosEndereco['numero'] = '12';
            $dadosEndereco['complemento'] = 'APTO 1000';
            $dadosEndereco['bairro'] = 'NOVO MUNDO';
            $dadosEndereco['cep'] = '81050222';
            $dadosEndereco['cidade'] = 'CURITIBA';
            $dadosEndereco['estado'] = 'PR';

            
            $retornoAdesaoCustumer = adesao_customer_igs($dadosAuth, $dadosAuthToken, $dadosCliente, $telefone, $dadosEndereco);
            print_r($retornoAdesaoCustumer);
        }
    }

    if($_GET['API'] == 'Pagbank'){

        require('../lib/pagBank.php');
        global $tokenPagBank;

        if($_GET['Teste'] == 'cria_aplicacao'){
        global $tokenPagBank;

        $dadosCartao = Array();
        $dadosCartao['numeroCartao'] 	  = '5555666677778884';
        $dadosCartao['nomeCartao'] 	  = 'matheus g v santos';
        $dadosCartao['VencimentoCartao'] = '122026';
        $dadosCartao['mesExp'] 		  = substr($dadosCartao['VencimentoCartao'],0,2); 
        $dadosCartao['anoExp'] 		  = substr($dadosCartao['VencimentoCartao'],2,4);
        $dadosCartao['codSegCartao'] 	  = '123';
        
        $teste = envia_dados_criptografia($dadosCartao);

       
        print_r($teste);
        exit;
        $dadosPagbank = cria_aplicacao('Matheus','','','http://localhost:8000/');
       
            $dadosEndTeste = Array();
            $dadosEndTeste['endereco'] = 'RUA FERNANDO DE NORONHA';
            $dadosEndTeste['numero'] = '1461';
            $dadosEndTeste['complemento'] = 'SOBRADO';
            $dadosEndTeste['cep'] = '82640350';
            $dadosEndTeste['cidade'] = 'CURITIBA';
            $dadosEndTeste['estado'] = 'PR';
            $dadosEndTeste['bairro'] = 'Boa vista';
            $dadosEndTeste['email'] = 'vaicomer55@gmail.com';
            $dadosEndTeste['dtNascimento'] = '1996-05-05';
            $dadosEndTeste['cpf'] = '09460447910';
            $dadosEndTeste['nomeMae'] = 'Fausei Mohamad';
            $dadosEndTeste['area'] = '41';
            $dadosEndTeste['nome'] = 'Matheus Ghotme';
            $dadosEndTeste['telefone'] = '996357037';
        
        $criaConta = cria_conta_pagbank($dadosEndTeste,'',$dadosPagbank['client_id'], $dadosPagbank['client_secret']);
        
            $email = $criaConta['email'];
            $nome = $criaConta['person']['name'];
            $cpf = $criaConta['person']['tax_id'];
            $rua = $criaConta['person']['address']['street'];
            $numeroRua = $criaConta['person']['address']['number'];
            $cidade = $criaConta['person']['address']['city'];
            $bairro = $criaConta['person']['address']['locality'];
            $estado = $criaConta['person']['address']['region_code'];
            $cep = $criaConta['person']['address']['postal_code'];
            $codArea = $criaConta['person']['phones'][0]['area'];
            $numero = $criaConta['person']['phones'][0]['number'];
       
        $criaPedido = cria_pedido($criaConta['id'], $nome, $email, $cpf, '100', $codArea, $numero, 'Plano A');
       
            $referenciaId = $criaPedido['reference_id'];
            $idPedido = $criaPedido['id'];
            $valorPedido = $criaPedido['items'][0]['unit_amount'];
            $qrCodeArray = array();
            $qrCodeArray = $criaPedido['qr_codes']; //dados do qrcode do pedido, verificar onde iremos usar
        
        $criaBoleto = cria_cobranca_boleto($criaConta['id'],'pagamento referente ao plano de beneficiarios',$valorPedido,'2024-12-31',$nome, $cpf, $email, $rua, $numeroRua, $cidade,$bairro, $estado, $cep);
        
        $teste = token_cartao();

        $publicKey = $teste['public_key'];
        $nomeCartao = 'matheus g v santos';
        $numeroCartao = '4539620659922097';
        $mesExp = '12';
        $anoExp = '2026';
        $cvv = '123';

        $retornaCartao = retorna_token_cartao($publicKey,$numeroCartao,$mesExp,$anoExp,$cvv,$nomeCartao);
        
       
        $codBarras = $criaBoleto['barcode'];
        $codBarrasFormatado = $criaBoleto['formatted_barcode'];
        $vencimento = $criaBoleto['due_date'];
        $linkBoleto = $criaBoleto['links'][0]['href'];

        $cartaoCredito = cria_venda_cartao_credito($referenciaId,'',$valorPedido,'1','TesteLojas','4539620659922097','12','2026','123','matheus g v santos');

        $idCartaoCredito = $cartaoCredito['id'];
        $status = $cartaoCredito['status'];
        $respostaPagamento = array();
        $respostaPagamento = $cartaoCredito['payment_response'];


        $cancelarTransacao = cancela_transacao($idReferencia, $valor);

        $statusCancelamento = $cancelarTransacao['status'];
        $idCancelamento = $cancelarTransacao['id'];
        $dataCancelamento = $cancelarTransacao['created_at'];


        $plano = criar_plano($valor = 100,$intervalo = 'MONTH',$trial = 'false',$pagamento = 'CREDIT_CARD',$nome = 'matheus g v santos',$descricao = 'foda');

        $idPlano = $plano['id'];
        
        $inativarPlano = inativa_plano($idPlano,$situacao = 'INACTIVE',$nome,$intevalo = 'DAY');
        }

        $criaCliente = cria_cliente_pagbank($nome,$email,$cpf,$nascimento,$area,$numero);

        $idCliente = $criaCliente['id'];

        $alteraCliente = altera_cliente_pagbank($idCliente);
        
        $criaAssinatura = cria_assinatura($idCliente,$idPlano,$valor,$pagamento);

        $idAssinatura = $criaAssinatura['id'];

        $cancelaAssinatura = cancelar_assinatura($idAssinatura);

        $suspenderAssinatura = suspender_assinatura($idAssinatura);

        $ativarAssinatura = ativar_assinatura($idAssinatura);

       
    }

}
?>