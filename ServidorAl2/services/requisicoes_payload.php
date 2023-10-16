<?php 
if(isset($_GET['API'])){    
    if($_GET['API'] == 'Doc24'){  
              
        require('../lib/base.php');                        
        require('../lib/doc24.php');        
        global $tokenDoc24, $homolDoc24, $linkCriado;
        
        $homolDoc24 = retornaValorConfiguracao('HOMOLOGACAO_DOC24');           
        $clientId = retornaValorConfiguracao('CLIENT_ID_DOC24');
        $clientSecret = retornaValorConfiguracao('CLIENT_SEC_DOC24');

        $codigoAssociado = $_SESSION['codigoIdentificacao'];
        
        if(!$codigoAssociado){
            echo 'Associado nao encontrado';
            exit;
        }

        $query  = ' SELECT PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.NUMERO_CPF, PS1001.ENDERECO_EMAIL FROM PS1000 ';
        $query .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
        $query .= ' WHERE PS1000.CODIGO_ASSOCIADO = '. aspas($codigoAssociado);
        $resultado = qryUmRegistro($query);
        
        $linkCriado = cria_sessao_usuario_doc24($resultado->NOME_ASSOCIADO, $resultado->NUMERO_CPF, $resultado->ENDERECO_EMAIL, $clientId, $clientSecret);
        
        if($linkCriado['LINK'])
            chamaLinkExterno($linkCriado['LINK']);
        else
            echo $linkCriado['ERROS'];
        
    }elseif($_GET['API'] == 'ZsPay'){ 

        require('../lib/base.php');                        
        require('../lib/zsPay.php');   
        global $tokenZsPay;

        $tokenZsPay = retornaValorConfiguracao('TOKEN_ZSPAY');
        $codigoAssociado = '';

        if($_SESSION['perfilOperador'] == 'BENEFICIARIO' or $_SESSION['perfilOperador'] == 'BENEFICIARIO_CPF')
            $codigoAssociado    = $_SESSION['codigoIdentificacao'];

        if($_GET['codigoAssociado'] != '' and $codigoAssociado == '')
            $codigoAssociado = $_GET['codigoAssociado'];        

        if(!$codigoAssociado){
            echo 'Associado nao encontrado';
            exit;
        }        

        $idClienteZsPay     = '';
        $retornoId = retornaIdAssociadoZsPay($codigoAssociado);
        
		if($retornoId['STATUS'] == 'OK'){
			$idClienteZsPay = $retornoId['ID'];
		}

        if($_GET['payload'] == 'BOLETO' and (isset($idClienteZsPay)) and (isset($_GET['registro']))){
            
            $queryFat  = ' SELECT DATA_VENCIMENTO, VALOR_FATURA, MES_ANO_REFERENCIA FROM PS1020 ';
            $queryFat .= ' WHERE NUMERO_REGISTRO = ' . aspas($_GET['registro']) . ' AND CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
            $resultadoFat = qryUmRegistro($queryFat);            

            $dataVencimento = DataToSql(SqlToData($resultadoFat->DATA_VENCIMENTO), false);
            $valorBoleto = $resultadoFat->VALOR_FATURA;
            $mesAno = $resultadoFat->MES_ANO_REFERENCIA;

            $retornoVendaBB = cria_venda_boleto($idClienteZsPay, $dataVencimento, $valorBoleto, 'Boleto Ref: ' . $mesAno);
            if(isset($retornoVendaBB['urlBoleto'])){
                chamaLinkExterno($retornoVendaBB['urlBoleto']);
            }
        }


    }elseif($_GET['API'] == 'IGS'){
        require('../lib/base.php');                        
        require('../lib/igs.php');
           
        global $tokenIGS, $homolIGS;

        $codigoAssociado = $_SESSION['codigoIdentificacao'];

        if(!$codigoAssociado){
            echo 'Associado nao encontrado';
            exit;
        }
        
        if(retornaValorConfiguracao('HOMOLOGACAO_IGS') == 'SIM'){
            $homolIGS = true;
        }else{
            $homolIGS = false;
        }

        if($_GET['payload'] == 'AUTENTICA'){                        

            $dadosAuth = Array();
            $dadosAuth['service']   = retornaValorConfiguracao('AUTH_SERVICE_IGS');
            $dadosAuth['auth_key']  = retornaValorConfiguracao('AUTH_NAME_IGS');
            $dadosAuth['username']  = retornaValorConfiguracao('AUTH_USERNAME_IGS');
            $dadosAuth['password']  = retornaValorConfiguracao('AUTH_PASS_IGS');                        
            
            $retornoAutentica = autentica_igs($dadosAuth);            
        }        

        if($_GET['payload'] == 'ADESAO'){

            
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

            $query  = ' SELECT ';
            $query .= '     PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.NUMERO_CPF, PS1000.FLAG_ASSOCIADO_CAD_RMS, PS1000.TIPO_ASSOCIADO, PS1000.SEXO, ';
            $query .= '     PS1000.DATA_NASCIMENTO, PS1030.CODIGO_RMS, PS1006.NUMERO_TELEFONE, PS1001.ENDERECO_EMAIL, PS1001.ENDERECO, PS1001.CIDADE, PS1001.ESTADO, PS1001.CEP ';
            $query .= ' FROM PS1000 ';            
            $query .= ' INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
            $query .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
            $query .= ' INNER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO) ';
            $query .= ' WHERE PS1000.CODIGO_ASSOCIADO = '. aspas($codigoAssociado);
            $resultado = qryUmRegistro($query);

            $dataNascimento = SqlToData($resultado->DATA_NASCIMENTO);
            $auxEndereco = $resultado->ENDERECO;
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

            $dadosEndereco = Array();
            $dadosEndereco['endereco'] = $endereco;
            $dadosEndereco['numero'] = $numero;
            $dadosEndereco['complemento'] = $complemento;
            $dadosEndereco['cep'] = $resultado->CEP;
            $dadosEndereco['cidade'] = sanitizeString($resultado->CIDADE);
            $dadosEndereco['bairro'] = sanitizeString($resultado->BAIRRO);
            $dadosEndereco['estado'] = $resultado->ESTADO;
            $dadosEndereco['email'] = $resultado->ENDERECO_EMAIL;

            $dadosCliente = Array();
            $dadosCliente['numeroCpf'] = sanitizeString($resultado->NUMERO_CPF);
            $dadosCliente['dtInicioVigencia'] = date('Y-m-d');
            $dadosCliente['dtIFimVigencia'] = '2099-12-30';
            $dadosCliente['tipoAssociado'] = $resultado->TIPO_ASSOCIADO;
            $dadosCliente['nome'] = sanitizeString($resultado->NOME_ASSOCIADO);
            $dadosCliente['apelido'] = sanitizeString($resultado->NOME_ASSOCIADO);
            $dadosCliente['dtNascimento'] = $dataNascimento;
            $dadosCliente['sexo'] = $resultado->SEXO;
            $dadosCliente['produto'] =  retornaValorConfiguracao('PRODUTO_IGS');                  

            $telefone = $resultado->NUMERO_TELEFONE;
            
            $retornoAdesaoCustumer = adesao_customer_igs($dadosAuth, $dadosAuthToken, $dadosCliente, $telefone, $dadosEndereco);
            print_r($retornoAdesaoCustumer);
        }
    }elseif($_GET['API'] == 'RMS'){ 
        require('../lib/base.php');       
        require('../lib/redeMais.php');           

        $codigoAssociado = $_SESSION['codigoIdentificacao'];

        if(!$codigoAssociado){
            echo 'Associado nao encontrado';
            exit;
        }

        global $tokenRedeMais, $homolRedeMais, $idCliente, $idClienteRedeMais, $tipoPlanoRMS;   

        if(retornaValorConfiguracao('HOMOLOGACAO_RMS') == 'SIM')
            $homolRedeMais = true;

        $tokenRedeMais = retornaValorConfiguracao('TOKEN_RMS');
        $idCliente = retornaValorConfiguracao('ID_CLIENTE_RMS');
        $idClienteRedeMais = retornaValorConfiguracao('ID_CLIENTE_CONTATO_RMS');
          

        $query  = ' SELECT ';
        $query .= '     PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.NUMERO_CPF, T.NUMERO_CPF AS CPF_TITULAR, PS1000.FLAG_ASSOCIADO_CAD_RMS, PS1000.TIPO_ASSOCIADO, PS1000.SEXO, ';
        $query .= '     PS1000.DATA_NASCIMENTO, PS1030.CODIGO_RMS, PS1006.NUMERO_TELEFONE, PS1001.ENDERECO_EMAIL, PS1001.ENDERECO, PS1001.CIDADE, PS1001.ESTADO, PS1001.CEP ';
        $query .= ' FROM PS1000 ';
        $query .= ' INNER JOIN PS1000 T ON (T.CODIGO_ASSOCIADO = PS1000.CODIGO_TITULAR) ';
        $query .= ' INNER JOIN PS1030 ON (PS1000.CODIGO_PLANO = PS1030.CODIGO_PLANO) ';
        $query .= ' LEFT OUTER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
        $query .= ' LEFT OUTER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO) ';
        $query .= ' WHERE PS1000.CODIGO_ASSOCIADO = '. aspas($codigoAssociado);
        $resultado = qryUmRegistro($query);
        
        $tipoPlanoRMS = $resultado->CODIGO_RMS;

        if(!$resultado->NUMERO_TELEFONE){
            echo 'Associado não tem telefone cadastrado';
            exit;
        }elseif(!$resultado->ENDERECO){
            echo 'Associado não tem endereço cadastrado';
            exit;
        }

        if(!$resultado->FLAG_ASSOCIADO_CAD_RMS){            
            $dataNascimento = SqlToData($resultado->DATA_NASCIMENTO);
            $auxEndereco = $resultado->ENDERECO;
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

            $dadosEndereco = Array();
            $dadosEndereco['endereco'] = $endereco;
            $dadosEndereco['numero'] = $numero;
            $dadosEndereco['complemento'] = $complemento;
            $dadosEndereco['cep'] = $resultado->CEP;
            $dadosEndereco['cidade'] = $resultado->CIDADE;
            $dadosEndereco['estado'] = $resultado->ESTADO;

            $retornoAdesao = adesao_rede_mais(
                                                $codigoAssociado, 
                                                $resultado->NOME_ASSOCIADO, 
                                                sanitizeString($resultado->NUMERO_CPF), 
                                                sanitizeString($resultado->CPF_TITULAR), 
                                                $dataNascimento, 
                                                $resultado->ENDERECO_EMAIL, 
                                                $resultado->NUMERO_TELEFONE, 
                                                $resultado->SEXO, 
                                                $resultado->TIPO_ASSOCIADO, 
                                                $dadosEndereco);            
            if($retornoAdesao['CODE'] == '200' or $retornoAdesao['CODE'] == '1016'){
                $criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
                $sqlEdicaoPs1000   = linhaJsonEdicao('FLAG_ASSOCIADO_CAD_RMS', 'S');     
			    gravaEdicao('PS1000', $sqlEdicaoPs1000, 'A', $criterioWhereGravacao);    
            }
        }

        $retornoRedeCredenciada = rede_credenciada_rede_mais(sanitizeString($resultado->NUMERO_CPF), 1, '');        
        chamaLinkExterno($retornoRedeCredenciada['URL']);
    }elseif($_GET['API'] == 'Epharma'){  
              
        require('../lib/base.php');                        
        require('../lib/epharma.php');
        
        global $token;
        $dadosAuth = Array();
        $dadosAuth['clientId'] = retornaValorConfiguracao('CLIENT_ID_EPHARMA');
        $dadosAuth['clientSecret'] = retornaValorConfiguracao('CLIENT_SECRET_EPHARMA');
        $dadosAuth['username'] = retornaValorConfiguracao('USERNAME_EPHARMA');
        $dadosAuth['password'] = retornaValorConfiguracao('PASSWORD_EPHARMA');
        $homolEpharma = retornaValorConfiguracao('HOMOLOGACAO_EPHARMA');

        $codigoAssociado = $_SESSION['codigoIdentificacao'];
        
        if(!$codigoAssociado){
            echo 'Associado nao encontrado';
            exit;
        }

        if($token == ''){               
            $retornoAutentica = autentica_epharma($dadosAuth, $homolEpharma);
            $token = $retornoAutentica['token'];
        }

        $query  = ' SELECT PS1000.NOME_ASSOCIADO, PS1000.NUMERO_CPF, PS1000.TIPO_ASSOCIADO, PS1000.DATA_NASCIMENTO, PS1000.SEXO FROM PS1000 ';        
        $query .= ' WHERE PS1000.CODIGO_ASSOCIADO = '. aspas($codigoAssociado);
        $resultado = qryUmRegistro($query);

        $dadosCliente = Array();
        $dadosCliente['numeroCpf'] = sanitizeString($resultado->NUMERO_CPF);
        $dadosCliente['dtInicioVigencia'] = date('d/m/Y');
        $dadosCliente['tipoAssociado'] = $resultado->TIPO_ASSOCIADO;
        $dadosCliente['nome'] = $resultado->NOME_ASSOCIADO;
        $dadosCliente['dtNascimento'] = SqlToData($resultado->DATA_NASCIMENTO);
        $dadosCliente['sexo'] = $resultado->SEXO;
        $codigoEpharma = '137526';

        $retornoCartaoCliente = movimentacaoCartaoCliente($token, $homolEpharma, $codigoEpharma, $dadosCliente);
        if($retornoCartaoCliente['STATUS'] == 'OK'){
            chamaLinkExterno('https://pesquisamedicamento.epharma.com.br/?benefitId=' . $codigoEpharma); 
        }
    }
}

function chamaLinkExterno($link){
    header('Content-Type: text/html; charset=utf-8');    
    header('Location: ' . $link);
}

?>