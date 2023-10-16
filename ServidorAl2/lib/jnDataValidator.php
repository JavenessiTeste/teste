<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of jnDataValidation
 *
 * @author Erich Nascimento
 */

class jnDataValidator {
    var $rules = null;

    function jnDataValidation() {
        $this->rules = array();
    }

    function AddField($AFieldValue, $ARule) {
        $this->rules[] = array (
            'value' => $AFieldValue,
            'rule'  => $ARule
        );
    }

    function Validate() {
        $errors = array();

        foreach ((array) $this->rules as $item) {
            if ( !$this->ValidateRule($item['value'], $item['rule']) ) {
                // echo $item['rule']['alert'];
                $errors[] = $item['rule']['alert'];
            }
        }
        if (count($errors > 0)) {
            return $errors;
        } else {
            return true;
        }
    }

    function ValidateAutoError(&$AObjErro) {
        $errors = $this->Validate();
        foreach ((array) $errors as $item) {
            $AObjErro->addErro($item);
        }
        return count($errors);
    }

    function ValidateRule($AValue, $ARule) {
         //pr($ARule, true);


        switch ($ARule['rule']) {

            case 'required' :
                if ($AValue == '') {
                    return false;
                }
            break;            

            case 'minlen' :
                if (strlen($AValue) < $ARule['value']) {
                     return false;
                }
            break;

            case 'maxlen' :
                if (strlen($AValue) > $ARule['value']) {
                     return false;
                }
            break;

            case 'length':

                $range = explode(',', $ARule['value']);

                if ((strlen($AValue) < $range[0]) || (strlen($AValue) > $range[1])) {
                     return false;
                }
            break;

            case 'alfa' :
                $regexp="/^[a-aA-Z]+$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'numeric' :
                $regexp="/^[0-9]+$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'alfa-numeric' :
                $regexp="/^[0-9a-zA-Z]+$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'email' :
                $regexp="/^[a-z0-9]+([_+\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i"; 

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'cep' :
                $regexp="/^[0-9]{5}-[0-9]{3}$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'ddd-space-fone' : // '(41) 322-6990'
                $regexp="/^\([0-9]{2}\) [0-9]{4}-[0-9]{4}$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'simple-fone' :
                $regexp="/^[0-9]{4}-[0-9]{4}$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'date' : // 01/01/2001
                $regexp="/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;

            case 'simple-date' :
                $regexp="/^[0-9]{2}/[0-9]{2}/[0-9]{2}$/";

                if (! preg_match($regexp, $AValue)) {
                    return false;
                }
            break;
            


        }
        

        return true;
    }


}
?>
