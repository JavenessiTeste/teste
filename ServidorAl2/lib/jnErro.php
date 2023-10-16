<?php

class jnErro {
    var $Items;
    var $DestinoSaida;
    var $DestinoRedirecionamento;
	var $MensagemSucesso = 'Operação efetuada com sucesso.';
    
    function __construct() {
        if (!is_array($_SESSION['ErrorList'])) {
            $_SESSION['ErrorList'] = array();
		}
        //$this->Clear();
    }
    
    function addErro($AValue) {
        $_SESSION['ErrorList'][] = $AValue;
        return (count($_SESSION['ErrorList']) - 1);
    }
    
    function Output() {
        $str = '';
        if (!empty($_SESSION['ErrorList'])){
            $str  = '<div id="messageBox" title="Informação">' . "\n";
            $str .= "\t" . '<div class="messageBoxContent">' . "\n";
            $str .= "\t\t" . '<ul>' . "\n";
            
            foreach ($_SESSION['ErrorList'] as $item) 
                $str .= "\t\t\t" . '<li>' . $item . '</li>' . "\n";
                
            $str .= "\t\t" . '</ul>' . "\n";
            $str .= "\t" . '</div>' . "\n";
            $str .= '</div>' . "\n";
            
            $this->Clear(); 
        }
        
        return $str;
    }
    
    function Clear() {
        $_SESSION['ErrorList'] = array();
        return true;
    }
    
    function ProcessaErro($AForceSaida = false) {
        if ((!empty($_SESSION['ErrorList'])) || ($AForceSaida)) {
            // envio um header, saindo imediatamente da pagina
            header("location: " . ($this->DestinoSaida)); 
            exit();
        }
    }

    function ProcessaErroAjax($AForceSaida = false) {

        $xml  = "";
        $xml .= "<resposta>\n";
        $xml .= "\t<sucesso>" . (empty($_SESSION['ErrorList']) ? $this->MensagemSucesso : false) . "</sucesso>\n";
        $xml .= "\t<mensagens>\n";

        if (!empty($_SESSION['ErrorList'])) {

            foreach($_SESSION['ErrorList'] as $erro) {
                $xml .= "\t\t<mensagem>$erro</mensagem>\n";
            }

            $_SESSION['ErrorList'] = array(); // limpo o array();

            $AForceSaida = true; // se houve erro, ele obrigatoriamente sai...
        }

        $xml .= "\t</mensagens>\n";
        $xml .= "\t<redirecionar>" . urlencode($this->DestinoRedirecionamento) . "</redirecionar>\n";
        $xml .= "</resposta>";        
	
		
        if ($AForceSaida) {
            header('Content-Type: application/xml; charset=ISO-8859-1');
            echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>\n";
            echo $xml;
            exit();
        }
    }

    function countErro() {
        return (count($_SESSION['ErrorList']));
    }
    
}






?>