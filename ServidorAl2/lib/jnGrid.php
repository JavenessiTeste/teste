<?php

class jnGridColuna {
    var $Nome;
    var $Largura;

    function __construct($ANome, $ALargura) {
        $this->Nome = $ANome;
        $this->Largura = $ALargura;
    }

    function Output() {
        return "\t\t\t" . '<th scope="col" width="'. $this->Largura . '">' . $this->Nome . '</th>';
    }
}

class jnGridColunaList {
    var $Items;

    function __construct() {
        $this->Items = array();
    }
    
    function Add($AItem) {
        $this->Items[] = $AItem;
        return $this->Count() - 1;
    }

    function Count(){
        return count($this->Items);
    }

    function Output() {
        $str  = "\t\t" . '<tr>' . "\n";
        foreach($this->Items as $item)
            $str .= $item->OutPut() . "\n";
        $str .= "\t\t" . '</tr>';

        return $str;
    }
}

class jnGridCell {
    var $Valor;
    var $Coluna;
    var $AttrClass;
	var $AttrColSpan;
    var $AttrTitle;
    var $AttrStyle;
    var $AttrId;
	var $OnClick = null;

    function __construct($AValor = '', $AColIndex = 0, $AAttrClassName = '') {
        $this->Valor = $AValor;
        $this->Coluna = $AColIndex;
        $this->AttrClass = $AAttrClassName;
		$this->AttrColSpan = '';
    }

    function Output() {
		$result  = "\t\t\t\t" . '<td ';
		$result .= 'class="' . $this->AttrClass . '" colspan="' . $this->AttrColSpan . '" title="' . $this->AttrTitle . '" ';
		$result .= 'style="' . $this->AttrStyle . '" id="' . $this->AttrId . '" ';
		
		// evento click
		$_OnClick = $this->_getOnClickFormatado();
		if ($_OnClick)
			$result .= 'onclick="' . $_OnClick . '" ';
			
		
		$result .= '>'. (empty($this->Valor) ? '&nbsp;' : $this->Valor) . '</td>';
        
		return $result;
    }

	/*
	 * Retorna o evento click formatado caso tenha sido informado.
	 * Caso não esteja configurado, retorna false;
	 */
	function _getOnClickFormatado() {
		$result = false;
		

		if (($this->OnClick <> null) && (is_array($this->OnClick))) {
			$result = '';
			$params = array(
				'sender'		=> 'this',
				'call'			=> 'null',
				'exit'			=> 'null',
				'extraParams'	=> 'null'
			);

			// verifico se foi solicitado um helper
			if (key_exists('helper', $this->OnClick)) {
				
				switch ($this->OnClick['helper']) {
					case 'text' :
						$params['call'] = 'jnGridOnEventHelperText';
					break;
					case 'numeric':
						$params['call'] = 'jnGridOnEventHelperNumeric';
					break;
					case 'date':
						$params['call'] = 'jnGridOnEventHelperDate';
					break;
				}

			} else {
				if (key_exists('call', $this->OnClick))
					$params['call'] = $this->OnClick['call'];
			}
			
			

			if (key_exists('exit', $this->OnClick))
				$params['exit'] = $this->OnClick['exit'];			

			// preparo os parametros extras no formato JSON
			if (key_exists('extraParams', $this->OnClick) && is_array($this->OnClick['extraParams'])) {
				
				$params['extraParams']	= '{';
				foreach ((array) $this->OnClick['extraParams'] as $key => $value) {
					$params['extraParams'] .= "'" . $key . "': '" . $value . "', ";
				}
				$params['extraParams'] .= '}';
				$params['extraParams'] = preg_replace("/\, \}$/", "}", $params['extraParams']);
			}
					
			$result .= 'jnGridOnCellClick(' . implode(', ', $params) . ');';			
		}

		return $result;
	}
}

class jnGridLinha {
    var $Items;
    var $AttrId;
    var $AttrClass;
	var $AttrStyle;

    function __construct() {
        $this->Items = array();
        $this->AttrId = '';
        $this->AttrClass = '';
    }
    
    function AddCell($AValor, $AColIndex, $AttrClassName) {
        $this->Items[] = new jnGridCell($AValor, $AColIndex, $this->AttrClass);
        return $this->Count() - 1;
    }

    function Count(){
        return count($this->Items);
    }

     function Output() {
        $str  = "\t\t\t" . '<tr id="' . $this->AttrId . '" class="' . $this->AttrClass . '" style="' . $this->AttrStyle . '">' . "\n";
        foreach($this->Items as $item)
            $str .= $item->OutPut() . "\n";
        $str .= "\t\t\t" . '</tr>';

        return $str;
    }
}

class jnGridLinhaList {
    var $Items = array();
    
    function __construct() {
        $this->Items = array();
    }

    function Add($AItem) {
        $this->Items[] = $AItem;
        return $this->Count() - 1;
    }

    function Count(){
        return count($this->Items);
    }

    function Output() {
        $str = '';
        
        foreach($this->Items as $item)
            $str .= $item->OutPut() . "\n";

        return $str;
    }
}

class jnGrid {
    var $AttrClassValues;
    var $AttrId;
    var $AttrClass;
    var $AttrCellSpacing;
    var $AttrWidth;
    var $EmptyText;

    var $Colunas;
    var $Linhas;

    function  __construct($AOptions = array()) {
    
        $this->AttrClassValues = array(0 => '', 1 => 'alt');
        $this->AttrId = 'myJnGrid';
        $this->AttrClass = 'jnGrid';
        $this->AttrCellSpacing = 0;
        $this->AttrWidth = 'auto';
        $this->EmptyText = 'Não há registros.';
    
        // verifico se o parametro passado é um array...
        $this->Colunas = new jnGridColunaList();
        $this->Linhas = new jnGridLinhaList();
    }

    function addColuna($ANome, $ALargura){
        $lColuna = new jnGridColuna($ANome, $ALargura);
        return $this->Colunas->Add($lColuna);
    }

    function addLinha($AValue) {
        // instancio a linha
        $lLinha = new jnGridLinha();
        $lLinha->AttrClass = $this->AttrClassValues[($this->Linhas->Count() % 2)];
        $lLinha->AttrId = 'jnGridRow' . ($this->Linhas->Count() + 1);

        $i = 0;
        foreach($AValue as $valor) {
            $lLinha->AddCell($valor, $i, null);
            $i++;
        }

        return $this->Linhas->Add($lLinha);
    }
    
    function Output() {
        $str  = '<table id="' . $this->AttrId . '" class="' . $this->AttrClass . '" cellspacing="' . $this->AttrCellSpacing . '" summary="" width="'. $this->AttrWidth . '">' . "\n";
        $str .= "\t" . '<tbody>' . "\n";

        $str .= $this->Colunas->Output() . "\n";
        
        // add o Emty text, mas ele soh estará visivel se nao tiver registros...
        $vazio = ($this->Linhas->Count() == 0);
        
        $estilo = $vazio ? '' : 'display: none';

        $str .= "\t\t\t" . '<tr id="jnGridRowEmpty" class="' . $this->AttrClassValues[0] . '" style="' . $estilo . '">' . "\n";
        $str .= "\t\t\t\t" . '<td class="' . $this->AttrClassValues[0] . '" colspan="' . $this->Colunas->Count() .'">' . $this->EmptyText . '</td>';
        $str .= "\t\t\t" . '</tr>';

        if(!$vazio) {
            $str .= $this->Linhas->Output() . "\n";
        }
        
        $str .= "\t" . '</tbody>' . "\n";
        $str .= '</table> ' . "\n";
        return $str;
    }


}

?>
