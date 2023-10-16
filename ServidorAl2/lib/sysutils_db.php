<?php

function rvc($AValue, $AValueEsperado = null) {
    
    if (!is_array($_SESSION['CONFIG_GLOBAL'])) {
        $_SESSION['CONFIG_GLOBAL'] = array();
    }

    // verifico se a chave ainda não existe no array
    if (!array_key_exists(upper($AValue), $_SESSION['CONFIG_GLOBAL'])) {
        // add o valor da configuração ao cache para otimizar novas solicitações
        $_SESSION['CONFIG_GLOBAL'][upper($AValue)] = retornaValorConfiguracao($AValue);
    }

    // verifico se é para comparar o resultado com algum valor...
    if (!is_null($AValueEsperado)) {
        // retorno o resultado da comparação do valor..
        return ($_SESSION['CONFIG_GLOBAL'][upper($AValue)] == $AValueEsperado);
    }


    // retorno o valor da configuração
    return $_SESSION['CONFIG_GLOBAL'][upper($AValue)];
}

function getGenerator($AGeneratorName, $AValueInc = 1) {
    // consulto o generator
    $query  = 'SELECT gen_id(' . $AGeneratorName . ', ' . $AValueInc . ' ) ';
    $query .= 'FROM RDB$database ';

    if ($res = @jn_query($query)) {
        if ($row = @jn_fetch_row($res)) {
             return $row[0];
        }
    }
    return false;
}

function inserirTelefone($AData, $ATable = 'Ps1006') {	
    // salvo os dados do telefone
    if (!empty($AData['telefone']['numero_telefone'])) {
        $query  = 'INSERT INTO ' . $ATable . ' (CODIGO_EMPRESA, CODIGO_ASSOCIADO, INDICE_TELEFONE, CODIGO_AREA, ';
        $query .= 'NUMERO_TELEFONE) ';
        $query .= 'VALUES ( ';

        $query .= integerNull($AData['codigo_empresa']) . ", ";
        $query .= aspasNull($AData['codigo_associado']) . ", ";
		$query .= aspas('1') . ", ";
        $query .= integerNull($AData['telefone']['codigo_area']) . ", ";
        $query .= aspas($AData['telefone']['numero_telefone']);
        $query .= " )";		

        if (! jn_query($query)) {
            return false; // saio retornando false
        }
    }
	if (!empty($AData['telefone']['numero_celular'])) {
        $query  = 'INSERT INTO ' . $ATable . ' (CODIGO_EMPRESA, CODIGO_ASSOCIADO, INDICE_TELEFONE, CODIGO_AREA, ';
        $query .= 'NUMERO_TELEFONE) ';
        $query .= 'VALUES ( ';

        $query .= integerNull($AData['codigo_empresa']) . ", ";
        $query .= aspasNull($AData['codigo_associado']) . ", ";
        $query .= aspas('2') . ", ";
        $query .= integerNull($AData['telefone']['codigo_area']) . ", ";
        $query .= aspas($AData['telefone']['numero_celular']);
        $query .= " )";
				 
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
    } else {
        return true;
    }
		
    return false;
}

function inserirEndereco($AData, $ATable = 'Ps1001') {
    
	$AData['enderecamento']['cep'] = str_replace('-','',$AData['enderecamento']['cep']);
	
	// salvo os dados do endereço	
    if (!empty($AData['enderecamento']['endereco'])) {
        $query  = 'INSERT INTO ' . $ATable . ' (CODIGO_EMPRESA, CODIGO_ASSOCIADO, ENDERECO, BAIRRO, ';
        $query .= 'CIDADE, CEP, ESTADO, ENDERECO_EMAIL) ';
        $query .= 'VALUES ( ';

        $query .= integerNull($AData['codigo_empresa']) . ", ";
        $query .= aspasNull($AData['codigo_associado']) . ", ";
        $query .= aspas($AData['enderecamento']['endereco']) . ", ";
        $query .= aspas($AData['enderecamento']['bairro']) . ", ";
        $query .= aspas($AData['enderecamento']['cidade']) . ", ";
        $query .= aspas($AData['enderecamento']['cep']) . ", ";
        $query .= aspas($AData['enderecamento']['estado']). ",";
        $query .= aspasNull($AData['enderecamento']['endereco_email']);
        $query .= " )";
        
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
        return true;
    } else {
        return false;
    }
}

function inserirContrato($AData, $ATable = 'Ps1002') {
    // salvo os dados do endereço
   
    if (!empty($AData['contrato']['nome_contratante'])) {
        $query  = 'INSERT INTO ' . $ATable . ' (NOME_CONTRATANTE, NUMERO_CPF_CONTRATANTE )';
        //$query .= 'CIDADE, CEP, ESTADO, ENDERECO_EMAIL) ';
        $query .= 'VALUES ( ';

        $query .= aspas($AData['contrato']['nome_contratante']) . ", ";
      //  $query .= aspasNull($AData['codigo_associado']) . ", ";
       // $query .= aspas($AData['enderecamento']['endereco']) . ", ";
       // $query .= aspas($AData['enderecamento']['bairro']) . ", ";
       // $query .= aspas($AData['enderecamento']['cidade']) . ", ";
       // $query .= aspas($AData['enderecamento']['cep']) . ", ";
       // $query .= aspas($AData['enderecamento']['estado']). ",";
        $query .= aspas($AData['contrato']['cpf_contratante']);
        $query .= " )";
       // pr($query, true);
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
        return true;
    } else {
        return false;
    }
}

function atualizarTelefone($AData, $ATable = 'Ps1006') {
	
    $query  = 'UPDATE ' . $ATable . ' SET ';
    $query .= 'codigo_area = '          . integerNull($AData['telefone']['codigo_area']);
    $query .= ', numero_telefone = '    . aspas($AData['telefone']['numero_telefone']);    
    $query .= ' WHERE ';
    $query .= 'numero_registro = ' . $AData['telefone']['numero_registro'];
    //pr($query, true);
	
    if (! jn_query($query)) {
        return false; // saio retornando false
    }
    return true;
}

function atualizarEndereco($AData, $ATable = 'Ps1001') {
    $query  = 'UPDATE ' . $ATable . ' SET ';
    $query .= 'endereco = ' . aspas($AData['enderecamento']['endereco']);
    $query .= ', bairro = ' . aspas($AData['enderecamento']['bairro']);
    $query .= ', cidade = ' . aspas($AData['enderecamento']['cidade']);
    $query .= ', cep = '    . aspas($AData['enderecamento']['cep']);
    $query .= ', estado = ' . aspas($AData['enderecamento']['estado']);
    $query .= ', endereco_email = ' . aspas($AData['enderecamento']['endereco_email']);
    $query .= ' WHERE ';
    $query .= 'numero_registro = ' . $AData['enderecamento']['numero_registro'];
    //jnLog::log($query);
    //pr($query, true);
    if (! jn_query($query)) {
        return false; // saio retornando false
    }
    return true;
}

function inserirEnderecoPrestador($AData, $ATable = 'Ps5001') {
    // salvo os dados do endereço
    if (!empty($AData['enderecamento']['endereco'])) {
        $query  = 'INSERT INTO ' . $ATable . ' (CODIGO_PRESTADOR, ENDERECO, BAIRRO, ';
        $query .= 'CIDADE, CEP, ESTADO, TELEFONE_01, TELEFONE_02, ENDERECO_EMAIL) ';
        $query .= 'VALUES ( ';

        $query .= $AData['codigo_prestador'] . ', ';
        $query .= aspas($AData['enderecamento']['endereco']) . ', ';
        $query .= aspas($AData['enderecamento']['bairro']) . ', ';
        $query .= aspas($AData['enderecamento']['cidade']) . ', ';
        $query .= aspas($AData['enderecamento']['cep']) . ', ';
        $query .= aspas($AData['enderecamento']['estado']) . ', ';
        $query .= aspas($AData['enderecamento']['telefone_01']) . ', ';
        $query .= aspas($AData['enderecamento']['telefone_02']) . ', ';
        $query .= aspas($AData['enderecamento']['endereco_email']);
        $query .= ' )';
        //jnLog::log($query);
        if (! jn_query($query)) {
            return false; // saio retornando false
        }
        return true;
    } else {
        return false;
    }
    return false;
}

function atualizarEnderecoPrestador($AData, $ATable = 'Ps5001') {
    $query  = 'UPDATE ' . $ATable . ' SET ';
    //$query .= 'endereco = '         . aspas($AData['enderecamento']['endereco']);
    //$query .= ', bairro = '         . aspas($AData['enderecamento']['bairro']);
    //$query .= ', cidade = '         . aspas($AData['enderecamento']['cidade']);
    //$query .= ', cep = '            . aspas($AData['enderecamento']['cep']);
    //$query .= ', estado = '         . aspas($AData['enderecamento']['estado']);
    $query .= ' telefone_01 = '    . aspas($AData['enderecamento']['telefone_01']);
    $query .= ', telefone_02 = '    . aspas($AData['enderecamento']['telefone_02']);
    $query .= ', endereco_email = ' . aspas($AData['enderecamento']['endereco_email']);
    $query .= ' WHERE ';
    $query .= 'numero_registro_endereco = ' . $AData['enderecamento']['numero_registro_endereco'];
    //jnLog::log($query);
    // pr($query, true);
    if (! jn_query($query)) {
        return false; // saio retornando false
    }
    return true;
}

function getTiposPessoa() {
    return array(
        array(
            'Codigo'    => 'F',
            'Nome'      => 'PESSOA FÍSICA'
        ),
        array(
            'Codigo'    => 'J',
            'Nome'      => 'PESSOA JURÍDICA'
        )
    );
}

function getGrausParentesco($ACodParentesco, $AFields) {
    $tblParentesco = 'Ps1045';
    $AFieldsDefault = array(
        'codigo_parentesco',
        'nome_parentesco'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $tblParentesco);

    $query .= 'FROM ';
    $query .= $tblParentesco . ' ';
	
	if($_SESSION['codigoSmart'] == '3423'){
		$query .= 'WHERE ';
        $query .= ' codigo_parentesco in(1,2,3,7,30) ';
	}
    
	if(!is_null($ACodParentesco)){
        if($_SESSION['codigoSmart'] != '3423'){
			$query .= 'where ';
		}else{
			$query .= ' and ';			
		}
        $query .= 'codigo_parentesco = ' . $ACodParentesco;
    }
    return sqlExecute($query, true);
}

function getTiposContratoPrestadores() {
    return array(
        array(
            'Codigo'    => 1,
            'Nome'      => 'CREDENCIADO'
        ),/*
         * A Rita solicitou que fosse apenas Credenciado
        array(
            'Codigo'    => 2,
            'Nome'      => 'PRÓPRIO'
        ),
        array(
            'Codigo'    => 3,
            'Nome'      => 'AUTORIZADO'
        )*/
    );
}

function getTiposPrestadores() {
    return array(
        array(
            'Codigo'    => '01',
            'Nome'      => 'MÉDICO'
        ),
        array(
            'Codigo'    => '02',
            'Nome'      => 'CLÍNICA'
        ),
        array(
            'Codigo'    => '03',
            'Nome'      => 'LABORATÓRIO'
        ),
        array(
            'Codigo'    => '04',
            'Nome'      => 'HOSPITAL'
        ),
        array(
            'Codigo'    => '05',
            'Nome'      => 'OUTROS'
        )
    );
}

function inserirEspecPrest($AData, $ATable = 'Ps5003') {
    // salvo as especialidades do prestador
    if (!empty($AData['especialidades'])) {
        
        // insiro cada especialidade
        foreach($AData['especialidades'] as $espec) {
            $query  = 'INSERT INTO ' . $ATable . ' (CODIGO_PRESTADOR, CODIGO_ESPECIALIDADE, FLAG_DIVULGA_ESPECIALIDADE ';
            $query .= ') ';
            $query .= 'VALUES ( ';

            $query .= $AData['codigo_prestador'] . ", ";
            $query .= $espec . ", ";
            $query .= aspas('N'); // por padrao esta ficando não
            $query .= " )";

            //jnLog::log($query);
//pr('tAAAAA',true);
            if (! jn_query($query)) {
                return false; // saio retornando false
            }
        }
    } else {
        return true;
    }
    return false;
}

function getBenTitFromCodigo($ACodigoBenef, $AFieldsBenef = null, $ACalcIdade = false, $AUnionTemp = false, $AAssoc = false) {
    $tblBenef       = 'Ps1000';
    $tblBenefTemp   = 'TMP1000_NET';
    $tblIdade       = 'VW_BENEF_IDADE_UNION_TEMP';

    $AFieldsDefault = array(
        'codigo_associado',
        'nome_associado'
    );
    
    /**
     *  pego os campos que foram solicitados
     */
    $strFields      = getFieldsForSelectStatement($AFieldsBenef, $AFieldsDefault, $tblBenef);
    $strFieldsUnion = getFieldsForSelectStatement($AFieldsBenef, $AFieldsDefault, $tblBenefTemp);

    if ($ACalcIdade) {
        // pego o campo da view que calcula idade
        $strFields      .= ', ' . $tblIdade . '.idade ';
        $strFieldsUnion .= ', ' . $tblIdade . '.idade ';
    }
    
    $strTabelas         = $tblBenef . ' ';
    $strTabelasUnion    = $tblBenefTemp . ' ';

    $strJoin = '';
    $strJoinUnion = '';
    if ($ACalcIdade) {
        // add o join de idades
        $strJoin        .= 'INNER JOIN ' . $tblIdade . ' ON ' . $tblIdade . '.codigo_associado = ' . $tblBenef . '.codigo_associado ';
        $strJoinUnion   .= 'INNER JOIN ' . $tblIdade . ' ON ' . $tblIdade . '.codigo_associado = ' . $tblBenefTemp . '.codigo_associado ';
    }

    // Monto a clausula WHERE

    // Tabela Padrao...
    $strWhere  = $tblBenef . '.codigo_titular = ';
    $strWhere .= '( ';
    $strWhere .= '  SELECT ';
    $strWhere .= '      b.codigo_titular FROM ps1000 b ';
    $strWhere .= '  WHERE ';
    $strWhere .= '      b.codigo_associado = \'' . $ACodigoBenef . '\' ';
    $strWhere .= '    ) ';

    $strWhere .= 'OR ';

    $strWhere .= $tblBenef . '.codigo_titular = ';
    $strWhere .= '( ';
    $strWhere .= '  SELECT ';
    $strWhere .= '      bt.codigo_titular FROM TMP1000_NET bt ';
    $strWhere .= '  WHERE ';
    $strWhere .= '      bt.codigo_associado = \'' . $ACodigoBenef . '\' ';
    $strWhere .= '    ) ';

    if ($AUnionTemp) {
        // Tabela Temporaria...
        $strWhereUnion  = $tblBenefTemp . '.codigo_titular = ';
        $strWhereUnion .= '( ';
        $strWhereUnion .= '  SELECT ';
        $strWhereUnion .= '      b.codigo_titular FROM ps1000 b ';
        $strWhereUnion .= '  WHERE ';
        $strWhereUnion .= '      b.codigo_associado = \'' . $ACodigoBenef . '\' ';
        $strWhereUnion .= '    ) ';

        $strWhereUnion .= 'OR ';

        $strWhereUnion .= $tblBenefTemp . '.codigo_titular = ';
        $strWhereUnion .= '( ';
        $strWhereUnion .= '  SELECT ';
        $strWhereUnion .= '      bt.codigo_titular FROM TMP1000_NET bt ';
        $strWhereUnion .= '  WHERE ';
        $strWhereUnion .= '      bt.codigo_associado = \'' . $ACodigoBenef . '\' ';
        $strWhereUnion .= '    ) ';
    }

    /*
     * MONTO A INSTRUÇÃO SQL
     */
    $query  = 'SELECT ';
    $query .= $strFields;
    $query .= 'FROM ';
    $query .= $strTabelas;
    if ($ACalcIdade) {
        $query .= $strJoin;
    }
    $query .= 'WHERE ';
    $query .= $strWhere;

    if ($AUnionTemp) {
        // concateno a UNION

        $query .= 'UNION ';

        $query .= 'SELECT ';
        $query .= $strFieldsUnion;
        $query .= 'FROM ';
        $query .= $strTabelasUnion;
        if ($ACalcIdade) {
            $query .= $strJoinUnion;
        }
        $query .= 'WHERE ';
        $query .= $strWhereUnion;
    }

    /***************************************************************************
     * Executo a query
     **************************************************************************/
    // pr($query, true);
    return sqlExecute($query, $AAssoc);
}

function getDadosEmpresa($ACodEmpresa, $AFields, $AAssoc = false) {
    $tblEmpresa = 'Ps1010';
    $AFieldsDefault = array(
        'codigo_empresa',
        'nome_empresa'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $tblEmpresa);

    $query .= 'FROM ';
    $query .= $tblEmpresa . ' ';
    $query .= 'WHERE ';
    $query .= 'codigo_empresa = ' . $ACodEmpresa;

    return sqlExecute($query, $AAssoc);
}

function getDadosTitular($ACodBenef, $AFields, $AUnionTemp) {
    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, array('codigo_associado','nome_associado'), 'Ps1000');
    $query .= 'FROM Ps1000 ';
    $query .= 'WHERE ';
    $query .= 'Ps1000.Codigo_associado = ';
    $query .= '    ( ';
    $query .= '  SELECT ';
    $query .= '      b.codigo_titular FROM ps1000 b ';
    $query .= '  WHERE ';
    $query .= '      b.codigo_associado = \'' . $ACodBenef . '\' ';
    $query .= '    ) ';

    $query .= 'OR ';
    
    $query .= 'Ps1000.Codigo_associado = ';
    $query .= '    ( ';
    $query .= '  SELECT ';
    $query .= '      b.codigo_titular FROM TMP1000_NET b ';
    $query .= '  WHERE ';
    $query .= '      b.codigo_associado = \'' . $ACodBenef . '\' ';
    $query .= '    ) ';

    if ($AUnionTemp) {
        $query .= 'UNION ';

        $query .= 'SELECT ';
        $query .= getFieldsForSelectStatement($AFields, array('codigo_associado','nome_associado'), 'TMP1000_NET');
        $query .= 'FROM TMP1000_NET ';
        $query .= 'WHERE ';
        $query .= 'TMP1000_NET.Codigo_associado = ';
        $query .= '    ( ';
        $query .= '  SELECT ';
        $query .= '      b.codigo_titular FROM PS1000 b ';
        $query .= '  WHERE ';
        $query .= '      b.codigo_associado = \'' . $ACodBenef . '\' ';
        $query .= '    ) ';
        $query .= 'OR ';
        $query .= 'TMP1000_NET.Codigo_associado = ';
        $query .= '    ( ';
        $query .= '  SELECT ';
        $query .= '      b.codigo_titular FROM TMP1000_NET b ';
        $query .= '  WHERE ';
        $query .= '      b.codigo_associado = \'' . $ACodBenef . '\' ';
        $query .= '    ) ';
    }
    //pr($query, true);

    return sqlExecute($query);    
}

function getDadosBeneficiario($ACodBenef, $AFields, $AUnionTemp, $AAssoc = false) {
    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, array('codigo_associado','nome_associado'), 'Ps1000');
    $query .= 'FROM Ps1000 ';
    $query .= 'WHERE ';
    $query .= 'Ps1000.codigo_associado = \'' . $ACodBenef . '\' ';

    if ($AUnionTemp) {
        $query .= 'UNION ';

        $query .= 'SELECT ';
        $query .= getFieldsForSelectStatement($AFields, array('codigo_associado','nome_associado'), 'TMP1000_NET');
        $query .= 'FROM TMP1000_NET ';
        $query .= 'WHERE ';
        $query .= 'TMP1000_NET.codigo_associado = \'' . $ACodBenef . '\' ';
    }
    // echo $query;
    return sqlExecute($query, $AAssoc);
}

function getDadosPlano($ACodPlano, $AFields) {
    $table = 'Ps1030';
    $AFieldsDefault = array(
        'codigo_plano',
        'nome_plano_familiares'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);

    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';
    $query .= 'codigo_plano = ' . $ACodPlano;

    return sqlExecute($query);
}

function getDadosPlanoCompleto($ACodPlano, $AFields) {
    $table = 'Ps1030';
    $AFieldsDefault = array(
        'codigo_plano',
        'nome_plano_familiares',
		'codigo_cadastro_ans'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);

    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';
    $query .= 'codigo_plano = ' . $ACodPlano;

    return sqlExecute($query);
}


function getDadosContrato($ACodBenef, $ACodEmpresa, $AFields, $AIsFamiliar) {
    $table = 'Ps1002';
    $AFieldsDefault = array(
        'codigo_contrato',
        'dia_vencimento',
        'nome_contratante'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);

    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';
    if ($AIsFamiliar) {
        $query .= 'codigo_associado = \'' . $ACodBenef . '\'';
    } else {
        $query .= 'codigo_empresa = ' . $ACodEmpresa;
    }
    
    return sqlExecute($query);
}

function getDadosEndereco($ACodigo, $AFields, $AWho) {
    $table = 'Ps1001';
    $AFieldsDefault = array(
        'numero_registro',
        'endereco',
        'bairro',
        'cidade',
        'cep',
        'estado',
        'endereco_email'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);

    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';

    switch ($AWho){
        case 'B':
            $query .= 'codigo_associado = \'' . $ACodigo . '\'';
            break;
        case 'E':
            $query .= 'codigo_empresa = ' . $ACodigo;
            break;        
    }
    //echo $query;
    return sqlExecute($query);
}

function getDadosTelefone($ACodigo, $AFields, $AWho) {
    $table = 'Ps1006';
    $AFieldsDefault = array(
        'numero_registro',
        'codigo_area',
        'numero_telefone'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);

    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';

    switch ($AWho){
        case 'B':
            $query .= 'codigo_associado = \'' . $ACodigo . '\'';
            break;
        case 'E':
            $query .= 'codigo_empresa = ' . $ACodigo;
            break;
    }
    return sqlExecute($query);
}

function getFieldsForSelectStatement($AFields, $ADefault, $AAlias) {
    if(is_null($AFields) || empty($AFields)) {
        $AFields = $ADefault;
    }

    foreach($AFields as $field) {
        $result .= $AAlias . '.' . $field . ', ';
    }
    $result = substr($result, 0, -2) . ' ';

    return $result;
}

function sqlExecute($ASql, $AAssoc = false) {
     //pr($ASql);
    
    $res = jn_query($ASql);

    $result = array();

    if ($AAssoc) {
        while ($row = jn_fetch_assoc($res)) {
            $result[] = $row;
        }
    } else {
        while ($row = jn_fetch_object($res)) {
            $result[] = $row;
        }
    }

    return $result;
}

function sqlInsertExecute($ATableName, $AFieldsValues) {

    if (!function_exists('_extractFieldsName')) {
        function _extractFieldsName($ArrFields) {
            $result = array();
            foreach ((array) $ArrFields as $Field) {
                // extraio o nome do campo
                $result[] = $Field[0];
            }

            if (empty($result)) {
                return false;
            }

            return $result;
        }
    }

    if (!function_exists('_extractFieldsValue')) {
        function _extractFieldsValue($ArrFields) {
            $result = array();
            foreach ((array) $ArrFields as $Field) {
                // extraio o valor do campo
                $result[] = $Field[1];
            }

            if (empty($result)) {
                return false;
            }

            return $result;
        }
    }

    // pego os nomes dos campos
    $_campos = _extractFieldsName($AFieldsValues);
    // pego os valores dos campos
    $_valores = _extractFieldsValue($AFieldsValues);
    
    // verifico se são validos
    if ($_campos && $_valores) {

        $query  = "INSERT INTO ";
        $query .= "    $ATableName ";
        $query .= "    ( ";
        $query .= implode(', ', $_campos);
        $query .= "    ) ";
        $query .= "VALUES ";
        $query .= "    ( ";
        $query .= implode(', ', $_valores);
        $query .= "    ) ";

        //pr($query, true);
        return jn_query($query);
    } else {
        trigger_error('Instrução inválida!');
    }

    return false;
}

function getDadosPrestador($ACodPrest, $AFields, $AUnionTemp) {
    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, array('codigo_prestador','nome_prestador'), 'Ps5000');
    $query .= 'FROM Ps5000 ';
    $query .= 'WHERE ';
    $query .= 'Ps5000.codigo_prestador = ' . integerNull($ACodPrest) . ' ';

    if ($AUnionTemp) {
        $query .= 'UNION ';

        $query .= 'SELECT ';
        $query .= getFieldsForSelectStatement($AFields, array('codigo_prestador','nome_prestador'), 'Tmp5000');
        $query .= 'FROM Tmp5000 ';
        $query .= 'WHERE ';
        $query .= 'Tmp5000.codigo_prestador = ' . integerNull($ACodPrest) . ' ';
    }
    //pr($query, true);
    return sqlExecute($query);
}

function getDadosEnderecoPrestador($ACodigo, $AFields) {
    $table = 'Ps5001';
    $AFieldsDefault = array(
        'endereco',
        'bairro',
        'cidade',
        'cep',
        'estado',
        'telefone_01',
        'telefone_02',
        'endereco_eletronico',
        'endereco_email'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);
    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';
    $query .= 'codigo_prestador = ' . $ACodigo;

    //echo $query;
    return sqlExecute($query);
}

function getDadosContratoPrestador($ACod, $AFields) {
    $table = 'Ps5002';
    $AFieldsDefault = array(
        'codigo_contrato',
        'dia_vencimento'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);
    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';
    $query .= 'codigo_prestador = ' . $ACod;

    return sqlExecute($query);
}

function getEspecsPrestador($ACod) {
    $table1 = 'Ps5003';
    $table2 = 'Ps5100';

    $AFieldsTable1 = array(
        'Numero_Registro',
        'Codigo_Prestador',
        'Codigo_Especialidade'
    );

    $AFieldsTable2 = array(
        'Nome_Especialidade',
        'Tipo_Especialid_Titulo'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFieldsTable1, null, $table1);
    $query .= ',  ';
    $query .= getFieldsForSelectStatement($AFieldsTable2, null, $table2);
    $query .= 'FROM ';
    $query .= $table1 . ' ';
    $query .= 'INNER JOIN ' . $table2 . ' ON ' . $table2 . '.codigo_especialidade = ' . $table1 . '.Codigo_Especialidade ';
    $query .= 'WHERE ';
    $query .= 'codigo_prestador = ' . $ACod;

    return sqlExecute($query);
}

function getPrestadoresVinculados($ACodPrest, $AFields, $AUnionTemp) {
    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, array('codigo_prestador','nome_prestador', 'codigo_prestador_principal', 'codigo_prestador_hospital'), 'Ps5000');
    $query .= 'FROM Ps5000 ';
    $query .= 'WHERE ';
    $query .= '( ';
    $query .= 'Ps5000.codigo_prestador_principal = ' . aspasNull($ACodPrest) . ' ';
    $query .= 'OR ';
    $query .= 'Ps5000.codigo_prestador_hospital = ' . aspasNull($ACodPrest) . ' ';
    $query .= ') ';
    $query .= 'AND ';
    $query .= 'Ps5000.codigo_prestador <> ' . integerNull($ACodPrest) . ' ';

    if ($AUnionTemp) {
        $query .= 'UNION ';

        $query .= 'SELECT ';
        $query .= getFieldsForSelectStatement($AFields, array('codigo_prestador','nome_prestador', 'codigo_prestador_principal', 'codigo_prestador_hospital'), 'Tmp5000');
        $query .= 'FROM Tmp5000 ';
        $query .= 'WHERE ';
        $query .= '( ';
        $query .= 'Tmp5000.codigo_prestador_principal = ' . aspasNull($ACodPrest) . ' ';
        $query .= 'OR ';
        $query .= 'Tmp5000.codigo_prestador_hospital = ' . aspasNull($ACodPrest) . ' ';
        $query .= ') ';
        $query .= 'AND ';
        $query .= 'Tmp5000.codigo_prestador <> ' . integerNull($ACodPrest) . ' ';
    }
    
    return sqlExecute($query);
}

/**
 *
 * Retorna os dados de uma fatura
 * @param <int> $ACodigo = código da fatura
 * @param <array> $AFields = campos que deseja trazer da tabela de faturas
 * @param <boolean> $AShowDet = true: caso deseja trazer o detalhamento da fatura
 * @return <array>
 */
function getDadosFatura($ACodigo, $AFields = false, $AShowDet = false, $AFieldsDet = null) {
    $tblFat = 'Ps1020';
    $tblBen = 'Ps1000';
    $tblEmp = 'Ps1010';
    $tblDet = 'Ps1021';
    $AFieldsDefault = array(
        'numero_registro',
        'codigo_empresa',
        'codigo_associado',
        'data_vencimento',
        'data_emissao',
        'valor_outros',
        'valor_correcao',
        'valor_convenio',
        'valor_adicional',
        'valor_fatura_bruto'
    );

    // campos q eu quero trazer da tbl de beneficiarios
    $AFieldsBen = array(
        'nome_associado'
    );

    // campos q eu quero trazer da tbl de empresas
    $AFieldsEmp = array(
        'nome_empresa'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $tblFat);
    // pego os dados da empresa
    $query .= ', ';
    $query .= getFieldsForSelectStatement($AFieldsEmp, null, $tblEmp);
    // pego os dados do beneficiario
    $query .= ', ';
    $query .= getFieldsForSelectStatement($AFieldsBen, null, $tblBen);

    $query .= 'FROM ';
    $query .= $tblFat . ' ';
    $query .= 'LEFT JOIN ' . $tblEmp . ' ON ' . $tblEmp . '.codigo_empresa = ' . $tblFat . '.codigo_empresa ';
    $query .= 'LEFT JOIN ' . $tblBen . ' ON ' . $tblBen . '.codigo_associado = ' . $tblFat . '.codigo_associado ';
    $query .= 'WHERE ';
    $query .= $tblFat . '.numero_registro = ' . $ACodigo;

    $result = sqlExecute($query, true);

    if ($AShowDet && !empty($result)) {
        // busco o detalhamento da fatura

        // campos q eu quero trazer da tbl de detalhamento da fatura
        $AFieldsDetDefault = array(
            'codigo_empresa',
            'codigo_associado',
            'valor_outros',
            'valor_correcao',
            'valor_convenio',
            'valor_adicional',
            'valor_fatura'
        );

        $query  = 'SELECT ';
        $query .= getFieldsForSelectStatement($AFieldsDet, $AFieldsDetDefault, $tblDet);
        // pego os dados da empresa
        $query .= ', ';
        $query .= getFieldsForSelectStatement($AFieldsEmp, null, $tblEmp);
        // pego os dados do beneficiario
        $query .= ', ';
        $query .= getFieldsForSelectStatement($AFieldsBen, null, $tblBen);

        $query .= 'FROM ';
        $query .= $tblDet . ' ';
        $query .= 'LEFT JOIN ' . $tblEmp . ' ON ' . $tblEmp . '.codigo_empresa = ' . $tblDet . '.codigo_empresa ';
        $query .= 'LEFT JOIN ' . $tblBen . ' ON ' . $tblBen . '.codigo_associado = ' . $tblDet . '.codigo_associado ';
        
        $query .= 'WHERE ';
        $query .= $tblDet . '.numero_registro_ps1020 = ' . $ACodigo;

        $result[0]['detalhamento'] = sqlExecute($query, true);
    }

    return $result;
}

function getDetalhesFatura($ACodFat, $AFields) {
    $tbl = 'Ps1021';
    $AFieldsDefault = array(
        'numero_registro',
        'codigo_empresa',
        'codigo_associado',
        'valor_adicional',
        'valor_correcao',
        'valor_convenio',
        'valor_outros',
        'valor_fatura'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);
    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';
    $query .= 'codigo_prestador = ' . $ACod;

    return sqlExecute($query);
}

/**
 * Retorna todas as solicitadoes de um beneficiario.
 * Solicitações: CARTEIRINHA, BOLETO, LIVRETO ...
 * @param <type> $ACodBenef
 * @param <type> $ATipoSol = 'CARTERINHAS'
 * @return <array> Array Associativo com as colunas
 */

function getSolicServicosBenef($ACodBenef, $ATipoSol = null) {
    $tblSolicServ = 'Ps6360';
    $AFieldsDefault = array(
        'numero_registro',
        'codigo_associado',
        'data_solicitacao',
        'data_emissao',
        'tipo_documento'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement(null, $AFieldsDefault, $tblSolicServ);

    $query .= 'FROM ';
    $query .= $tblSolicServ . ' ';
    $query .= 'WHERE ';
    $query .= 'codigo_associado = \'' . $ACodBenef . '\' ';

    return sqlExecute($query, true);
}

function inserirSolicServicosBenef($AData) {
    $query  = 'INSERT INTO ';
    $query .= 'Ps6360 ';
    $query .= '(';
    $query .= 'codigo_associado, ';
    $query .= 'data_solicitacao, ';
    $query .= 'tipo_documento ';
    $query .= ') ';
    $query .= 'values ';
    $query .= '( ';
    $query .= aspas($AData['CODIGO_ASSOCIADO']) . ', ';
    $query .= 'CURRENT_TIMESTAMP, ';
    $query .= aspas($AData['TIPO_DOCUMENTO']);
    $query .= ') ';

    return jn_query($query);
}

function getPlanosComercializados($AFields = null, $AComStatus = false, $AShowOmite = false) {
    $tblPlanos = 'VW_PLANOS_NET';
    $AFieldsDefault = array(
        'codigo_plano',
        'nome_plano_familiares',
        'codigo_tipo_abrangencia',
        'nome_tipo_abrangencia',
        'codigo_cadastro_ans',
        'flag_plano_regulamentado',
        'codigo_tipo_cobertura',
        'nome_tipo_cobertura',
        'flag_omite_processos_ans',
        'status_comercializacao_plano'
    );
    $_ValorStatusCancelado = 'CANCELADO';

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $tblPlanos);

    $query .= 'FROM ';
    $query .= $tblPlanos . ' ';

    $query .= 'WHERE 1 = 1 ';

    if (!$AShowOmite) {
        $query .= 'AND ';
        $query .= '    (FLAG_OMITE_PROCESSOS_ANS <> \'S\' ';
        $query .= '    OR ';
        $query .= '    FLAG_OMITE_PROCESSOS_ANS IS NULL) ';
        $query .= 'AND ';
        $query .= '    (STATUS_COMERCIALIZACAO_PLANO <> \'' . $_ValorStatusCancelado . '\' ';
		$query .= '    OR ';
		$query .= '    STATUS_COMERCIALIZACAO_PLANO IS NULL) ';
    }

    if ($AComStatus) {
        $query .= 'AND ';
        $query .= 'STATUS_COMERCIALIZACAO_PLANO = \'' . $AComStatus . '\' ';
    }
    
    return sqlExecute($query, true);
}

function getDadosCep($ACodCep, $AFields) {
    $table = 'Ps1040';
    $AFieldsDefault = array(
        'numero_registro',
        'cep',
        'logradouro',
        'cidade',
        'estado',
        'bairro',
        'codigo_praca',
        'codigo_municipio_ibge'
    );

    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);
    $query .= 'FROM ';
    $query .= $table . ' ';
    $query .= 'WHERE ';
    $query .= 'cep = \''. $ACodCep . '\'' ;
    $query .= 'OR ';
    $query .= 'cep = \''. str_replace('-', '', $ACodCep) . '\'' ;
   // pr($query, true);
    return sqlExecute($query);
}
		
		
function getCarencias($ACodBen, $AFields = null) {
    $table = 'Sp_retornaCarencias';
    $AFieldsDefault = array(
        'RESULTADO_DATA_ADMISSAO',
        'RESULTADO_NUMERO_GRUPO',
        'RESULTADO_DESCRICAO_GRUPO',
        'RESULTADO_DATA_CARENCIA'
    );

	$queryEmpresa = '	
					SELECT
                        CFGEMPRESA.NUMERO_INSC_SUSEP
                    FROM CFGEMPRESA';
		
	$resEmpresa  = jn_query($queryEmpresa);
	$rowEmpresa = jn_fetch_object($resEmpresa);
		
	$EmpresaCfg = $rowEmpresa->NUMERO_INSC_SUSEP;
	
    $query  = 'SELECT ';
    $query .= getFieldsForSelectStatement($AFields, $AFieldsDefault, $table);
    $query .= 'FROM ';
    $query .= $table . '(\'';
    $query .= $ACodBen;
    
	if($EmpresaCfg == '400190'){		
		$query .= '\') where sp_retornacarencias.resultado_numero_grupo between "1" and "8" ';
    }else{		
		$query .= '\') ';
    }	
    
    $query .= ' order by Sp_retornaCarencias.resultado_data_admissao ';
	return sqlExecute($query);
}

function getTipoOperadora() {
    $query  = 'SELECT ';
    $query .= '   count(*) as ct ';
    $query .= 'FROM ';
    $query .= '   rbtable ';
    $query .= 'WHERE ';
    $query .= '   TABLENAME = \'PS2000\'';

    $res = jn_query($query);
    if ($row = jn_fetch_row($res)) {        
        return $row[0];
    }

    return -1;
}

function getDescontoBoletoBenef($ACodAssociado) {
    if (!empty($ACodAssociado)) {
        if (procedureExiste('SP_RETORNA_DESCONTO_BOLETO')) {
            $query  = 'SELECT ';
            $query .= '    * ';
            $query .= 'FROM ';
            $query .= '    SP_RETORNA_DESCONTO_BOLETO(';
            $query .= aspasNull($ACodAssociado);
            $query .= '    )';            

            $result = sqlExecute($query, true);

            return $result[0]['PERCENTUAL_DESCONTO'];
        } else {
            return false;
        }
    } else {
        return false;
    }

    return false;
}

function procedureExiste($AProcedureName) {
    $query  = 'SELECT 1 as EXISTE FROM rdb$procedures ';
    $query .= 'WHERE rdb$procedure_name = ' . aspas(upper($AProcedureName));

    $result = sqlExecute($query, true);

    return ($result[0]['EXISTE'] == 1);
}
/**
 *
 * @param <String> $ATableName
 * @return <String> Contento o valor do sequencial da tabela solicitada
 */

function geraSequencial($ATableName) {
    $query  = 'SELECT ';
    $query .= '   * ';
    $query .= 'FROM ';
    $query .= '   GeraSequencial(\'' . strtoupper($ATableName) . '\') ';

    $res = jn_query($query);
    if ($row = jn_fetch_row($res)) {
        return $row[0];
    } else {
        trigger_error('Falha ao buscar valor sequencial.');
        return false;
    }
}

function IBBlobToStr($AFieldBlobValue) {
    // trato o blob
    $_blob_info = jn_blob_info($AFieldBlobValue);
    $_blob_hnd = jn_blob_open($AFieldBlobValue);

    $_blob_data = jn_blob_get($_blob_hnd, $_blob_info[0]);

    return $_blob_data;
}

function getMensagemWeb($AMensagemID) {
    $query  = 'SELECT ';
    $query .= '    texto_mensagem ';
    $query .= 'FROM ';
    $query .= '    CFGMENSAGENS_NET ';
    $query .= 'WHERE ';
    $query .= '    UPPER(identificacao_mensagem) = ' . aspas($AMensagemID);

    $result = sqlExecute($query, true);
    if (!empty ($result)) {
        return IBBlobToStr($result[0]['TEXTO_MENSAGEM']);
    } else {
        return '';
    }
}

function getTiposPrestadoresFromDB() {
    
    $result = array();

    $query  = 'SELECT ';
    $query .=   't.OPCOES_COMBO ';
    $query .= 'FROM ';
    
    
    //$_tipoOperadora = getTipoOperadora();    
    
    //if ($_tipoOperadora == TO_MEDICINA) { // Se for medicina, pego da tabela cfgCampos_Sis..
        $query .= 'cfgCampos_Sis t ';
    //} 
    //else if ($_tipoOperadora == TO_ODONTOLOGIA) { // Se for odonto, pego da tabela cfgCampos_Sis_Esp..
    //    $query .= 'cfgCampos_Sis_Esp t ';
    //}

    $query .= 'WHERE ';
    $query .=   '(Nome_Tabela = "PS5000") ';
    $query .=   'AND ';
    $query .=   '(Nome_Campo = "TIPO_PRESTADOR") ';    
    
    $res = jn_query($query);
    if ($row = jn_fetch_assoc($res)) {
        $opcoes = $row['OPCOES_COMBO'];
    
        // Pego o caractere separador..pode ser a virgula ou ponto e virgula
        if (strpos($opcoes, ',')) {
            $_cs = ',';
        } else {
            $_cs = ';';
        }

        // os dados vao vir da seguinte forma por exemplo: '01 - MEDICO, 02 - CLINICA, 03 - LABORATORIO, 04 - HOSPITAL, 05 - OUTROS'
        $valores = explode($_cs, $opcoes);
		
		$queryEmpresa = 'Select codigo_smart from cfgempresa';
		$resEmpresa = jn_query($queryEmpresa);
		$rowEmpresa = jn_fetch_object($resEmpresa);	
		
		

        foreach ((array) $valores as $val ) {
            // avanço caso seja vazio..
            if (empty($val))
                continue;

            // O conteudo de cada valor é algo como: '01 - Medico'
            // Sendo assim, tenho q separar o codigo do valor
            $temp = explode('-', trim($val));
			
			//Se for Medical Health, pega apenas as combos com valor menor que 5
			if($rowEmpresa->CODIGO_SMART == '3419'){
				if (($temp[0] < 5)){
					$result[] = array(
						'codigo'    => trim($temp[0]),
						'descricao' => trim($temp[1])
					);
				}				
			}else{
				$result[] = array(
					'codigo'    => trim($temp[0]),
					'descricao' => trim($temp[1])
				);				
			}
        }

    }

    return $result;
}

function getTiposAtendimentoOdonto() {
   $tipos_execao = array();
   $query  = 'SELECT CFGCAMPOS_SIS.OPCOES_COMBO FROM CFGCAMPOS_SIS  ';
   $query .= 'Where CFGCAMPOS_SIS.NOME_TABELA = \'PS2500\' ';
   $query .= 'AND   CFGCAMPOS_SIS.NOME_CAMPO = \'TIPO_ATENDIMENTO\' ';
   $iquery = jn_query($query);
   $result = jn_fetch_assoc( $iquery );
   if( $result['OPCOES_COMBO'] != '' ){
      $separacao = preg_split( '/[;,]/', $result['OPCOES_COMBO'], -1, PREG_SPLIT_NO_EMPTY );
      foreach( $separacao as $item ){
         $item_separacao = preg_split( '/[\-]/', $item, -1, PREG_SPLIT_NO_EMPTY );
         if( trim( $item_separacao[1] ) != '' ){
            $tipos_execao[] = array( 'codigo' => trim( $item_separacao[0] ), 'descricao' => trim( $item_separacao[1] ) );
         }
      }
   }else{
      $tipos_execao = array(
           array(
               'codigo'    => '4',
               'descricao' => 'Urgência / Emergência'
           ),
           array(
               'codigo'    => '3',
               'descricao' => 'Ortodontia'
           ),
           array(
               'codigo'    => '1',
               'descricao' => 'Tratamento odontológico'
           ),
           array(
               'codigo'    => '2',
               'descricao' => 'Exame radiológico'
           ),
           array(
               'codigo'    => '5',
               'descricao' => 'Auditoria'
           )
       );
   }
   return $tipos_execao;
}

function getRedeIndicadaBenef($ACodBen) {
	$query  = 'SELECT Ps1030.Codigo_Rede_Indicada FROM Ps1000 INNER JOIN Ps1030 ON ';
	$query .= '(Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ';
	$query .= 'Where (Ps1000.Codigo_Associado = ' . aspasNull($ACodBen) . ')';

    $res = sqlExecute($query, true);

	if (!empty($res)) {
		return $res[0]['CODIGO_REDE_INDICADA'];
	} else {
		return null;
	}
}


function CamposExisteCfgTabelas_Sis($nomeTabela, $nomeCampo)
{

    $row = qryUmRegistro('Select NUMERO_REGISTRO From CfgCampos_sis Where Nome_Tabela = ' . aspas($nomeTabela) . ' and nome_Campo = ' . aspas($nomeCampo));

    if ($row->NUMERO_REGISTRO=='')    
        return false;
    else
        return true;
}


function CampoExiste($ATabela, $ACampo) 
{

    if ($_SESSION['type_db'] == 'sqlsrv')  
    {
        $sql  =	'SELECT C.COLUMN_NAME nome_campo
                FROM INFORMATION_SCHEMA.Tables T JOIN INFORMATION_SCHEMA.Columns C 
                ON T.TABLE_NAME = C.TABLE_NAME 
                WHERE T.TABLE_NAME NOT LIKE ' . aspas('sys%') . '
                AND T.TABLE_NAME <> ' . aspas('dtproperties') . '
                AND T.TABLE_SCHEMA <> ' . aspas('INFORMATION_SCHEMA') . ' 
                And upper(T.TABLE_NAME) = ' . aspas(strToUpper($ATabela)) . '
                And upper(C.COLUMN_NAME) = ' . aspas(strToUpper($ACampo)) . '
                ORDER BY T.TABLE_NAME, C.ORDINAL_POSITION';

    }
    else
    {
        $sql  = 'SELECT RDB$RELATION_FIELDS.RDB$FIELD_NAME nome_campo
                 FROM RDB$RELATION_FIELDS 
                 WHERE RDB$RELATION_FIELDS.RDB$RELATION_NAME = UPPER(' . aspas($ATabela) . ') 
                 AND RDB$RELATION_FIELDS.RDB$FIELD_NAME = UPPER(' . aspas($ACampo) . ')';
    }
	
	$rowTmp = jn_query($sql);

    if ($rowTmp->NOME_CAMPO=='')
        return false;
    else
        return true; 

}


function montaTabela($query,$nomeChave,$tabelaDesc, $tabela, $alterar,$excluir,$consultar,$imagem, &$campos, $htmlPaginacao, $idGrid = 'Grid',$controle = '')
{
	
	$query_campos = 'SELECT NOME_CAMPO, ESCONDER_RESPONSIVO, TAMANHO_RESPONSIVO, TAMANHO_CAMPO, TIPO_CAMPO FROM CFGCAMPOS_SIS_NET WHERE NOME_TABELA = ' . aspas($tabela) . ' AND FLAG_APRESENTA_DBGRID = \'S\' ORDER BY NUMERO_ORDEM_CRIACAO';
	$res_campos = jn_query($query_campos);
	
	$quantidadeCampos = 0;
	
	 while($row_campos =jn_fetch_object($res_campos)){
		$nomeCampo[]    		= trim($row_campos->NOME_CAMPO);
        $tamanhoCampo[] 		= trim($row_campos->TAMANHO_CAMPO);
        $tipoCampo[]    		= trim($row_campos->TIPO_CAMPO);
        $escondeResponsivo[]    = trim($row_campos->ESCONDER_RESPONSIVO);
        $tamanhoResponsivo[]    = trim($row_campos->TAMANHO_RESPONSIVO);
		$quantidadeCampos		= $quantidadeCampos+1;
	 }
	
	$campos['Nome'] = $nomeCampo;
	$campos['Tipo'] = $tipoCampo;
	
	
	$first      = true;
	$retorno	= '<div class="table-header"> ' . $tabelaDesc . ' </div>';
	$retorno	.= '<div>';
	$retorno	.= '<div id="dynamic-table_wrapper" class="dataTables_wrapper form-inline no-footer">';
	$retorno	.= '<div style="overflow:auto;">';	
	$retorno	.= '<table id="'.$idGrid.'" class="table table-striped table-bordered table-hover dataTable no-footer DTTT_selectable" role="grid" aria-describedby="dynamic-table_info">';
    
	$cor		= true;
	global $Con;
	
	$consultar = verificaTipo($tabela,'VISUALISAR');
	$alterar   = verificaTipo($tabela,'ALTERAR');
	$excluir   = verificaTipo($tabela,'EXCLUIR');
	
	$query = jn_query($query);
	$vazio = true;
	$cab = true;
	$idl = 1;
 	while((($row=jn_fetch_object($query, IBASE_TEXT)) or $cab ))
    {
	
	//pr($query,true);
	
	//$query2 = jn_query("SELECT MENSAGEM_CAD_BENEFICIARIOS FROM PS1010 WHERE CODIGO_EMPRESA = 400");
	//pr($Con,false);
	//pr($query2,false);
	//$temp = $query2;
 	//($row2=ibase_fetch_assoc($query2, IBASE_TEXT));
	//$_blob_info = ibase_blob_info($Con,$row2[0]);
	//$_blob_hnd  = ibase_blob_open($row2[0]);
	//$_blob_data = ibase_blob_get($_blob_hnd, $_blob_info[0]);
	
	
		 if ($first)
		 {
			 $retorno.= "<thead>";	
   		     $retorno.= "<tr> ";
  		 
			if ($consultar){
			
				$retorno.= "<th class='td-actions'>";
				$retorno.= "</th>";
			}		 
			 
			if ($alterar){
			
				$retorno.= "<th class='td-actions'>";
				$retorno.= "</th>";
			}

			if ($excluir){		
			
				$retorno.= "<th class='td-actions'>";
				$retorno.= "</th>";
			
			} 
			if ($imagem){		
			
				$retorno.= "<th class='td-actions'>";
				$retorno.= "</th>";
			
			} 			
   	         for ($i = 0; $i < $quantidadeCampos; $i++) 
	         {
			if ($escondeResponsivo[$i] <> 'S'){
				if($tamanhoResponsivo[$i]<> ""){
						$classe = 'hidden-' . $tamanhoResponsivo[$i];
					}else{				
						$classe = 'hidden-480';			
					}
				}else{
					$classe = '';
				}			 
			 
                 $retorno.= '<th class="'. $classe .'" tabindex="0" aria-controls="dynamic-table" rowspan="1" colspan="1" aria-label="teste">' . str_replace('_',' ',$nomeCampo[$i]) . '</th>';
	         }
	     		 
   		     $retorno .="</tr>";
			 $retorno.= "</thead>";
			 $retorno.= "<tbody>";
			 $first   = false;
	     }
		if ($cor){
			$retorno.= '<tr id="'.$idGrid.'_Linha_'.$idl.'" class="odd" role="row"> ';
		}else {
			$retorno.= '<tr id="'.$idGrid.'_Linha_'.$idl.'"  class="even" role="row"> ';
		}
		$idl++;
		$cor 	= !$cor;
		$cab = false;
		if (!$row){
			continue;
		}
		
		$vazio = false;

		if ($consultar){
		
			$retorno.= "<td class='td-actions'>";
			$retorno.='<a href="#" class="btn btn-info btn-mini" onclick="visualizaRegistro(' . aspas($nomeChave) . ',' . aspas($row->$nomeChave) . ')"></i> Visualizar</a>';
			//$retorno.= '<input type="image" src="imagens/bt_alteraregistro.jpg" onclick="alteraRegistro(' . aspas($nomeCampo[0]) . ',' . //<i class="icon-white icon-pencil">
			//            $row->$nomeCampo[0] . ')">';
			$retorno.= "</td>";
		}		 
		 
		if ($alterar){
		
			$retorno.= "<td class='td-actions'>";
			$retorno.='<a href="#" class="btn btn-success btn-mini" onclick="alteraRegistro(' . aspas($nomeChave) . ',' .  aspas($row->$nomeChave) . ')"></i> Alterar</a>';
			//$retorno.= '<input type="image" src="imagens/bt_alteraregistro.jpg" onclick="alteraRegistro(' . aspas($nomeCampo[0]) . ',' . //<i class="icon-white icon-pencil">
			//            $row->$nomeCampo[0] . ')">';
			$retorno.= "</td>";
		}

		if ($excluir){		
		
		$retorno.= "<td class='td-actions'>";
			$retorno.= '<a href="#" class="btn btn-danger btn-mini" onclick="excluiRegistro(' . aspas($nomeChave) . ',' .  aspas($row->$nomeChave) . ')" > Excluir</a>';
			//$retorno.= '<input type="image" src="imagens/bt_excluiregistro.jpg" onclick="excluiRegistro(' . aspas($nomeCampo[0]) . ',' . //<i class="icon-white icon-trash"></i>
			//            $row->$nomeCampo[0] . ')">';
			$retorno.= "</td>";
		
		} 
		if ($imagem){		
		
		$retorno.= "<td class='td-actions'>";
			$retorno.= '<a href="#" class="btn btn-info btn-mini" onclick="imagemRegistro(' . aspas($nomeChave) . ',' .  aspas($row->$nomeChave) . ')" > Imagens</a>';
			//$retorno.= '<input type="image" src="imagens/bt_excluiregistro.jpg" onclick="excluiRegistro(' . aspas($nomeCampo[0]) . ',' . //<i class="icon-white icon-trash"></i>
			//            $row->$nomeCampo[0] . ')">';
			$retorno.= "</td>";
		
		} 
		
		$classe = "";
   	     for ($i = 0; $i < $quantidadeCampos; $i++) 
	     {
			if ($escondeResponsivo[$i] <> 'S'){
				if($tamanhoResponsivo[$i]<> ""){
					$classe = 'hidden-' . $tamanhoResponsivo[$i];
				}else{				
					$classe = 'hidden-480';			
				}
			}else{
				$classe = '';
			}
		      if ((empty($row->$nomeCampo[$i])) or ($row->$nomeCampo[$i] == null) or ($row->$nomeCampo[$i] == ""))
			  {
                 $retorno.= "<td class = '" . $classe . "'>&nbsp</td>";
			  }
			  else
			  {				  
				 if(preg_match('/^LINK/', $nomeCampo[$i]))	
				 {   
					 $retorno.= "<td class = '" . $classe . "'>" . "<a  ".tipoLink($tabela,$nomeCampo[$i], $row->$nomeCampo[$i],$controle)."  >" .  nomeLink($tabela,$nomeCampo[$i]). "</a></td>";
					 //pr($row->$nomeCampo[$i]);
				 }
				 else if(preg_match('/^VALOR_/', $nomeCampo[$i]))	
				 {   
					$retorno.= "<td class = '" . $classe . "'>" . toMoeda($row->$nomeCampo[$i]). "&nbsp;</td>";
				 }else if ($tipoCampo[$i] === "DATE"){
                    $retorno.= "<td class = '" . $classe . "'>" . SqlToData($row->$nomeCampo[$i]). "&nbsp;</td>";					 
				 } 
				 
				 else if($tipoCampo[$i] == "BLOB"){
				    $retorno.= "<td class = '" . $classe . "'>" . ($row->$nomeCampo[$i]) . "&nbsp;</td>";
					//$retorno.= "<td class = '" . $classe . "'>" . jn_blobtostr($row->$nomeCampo[$i]) . "&nbsp;</td>";
													
							//$_blob_info = ibase_blob_info($Con,$row->$nomeCampo[$i]);
							//$_blob_hnd  = ibase_blob_open($Con,$row->$nomeCampo[$i]);
							//$_blob_data = ibase_blob_get($_blob_hnd, $_blob_info[0]);
							//pr($Con,true);
				 }else{					 
                    $retorno.= "<td class = '" . $classe . "'>" . str_replace(" "," ",$row->$nomeCampo[$i]) . "&nbsp;</td>";
				 }
			  }
	     }

	   
	     $retorno .= "</tr>";
		 
    }
	if ($vazio){
	
	  $retorno .= '<table class="table table-striped table-bordered table-hover dataTable no-footer DTTT_selectable"><tr><td>Nenhum registro para visualização.</td></tr></table>';
	}	
    $retorno.= "</tbody>";
    $retorno.= "</table>";
	$retorno.= "</div>";	
	$retorno.= $htmlPaginacao;
	$retorno.= "</div>";
    $retorno.= "</div>";	
	
	return $retorno;

}


function jn_execute($ASql, $AAssoc = false) {
    //pr($ASql);
    
    $res = jn_query($ASql);

    $result = array();

    if ($AAssoc) {
        while ($row = jn_fetch_assoc($res)) {
            $result[] = $row;
        }
    } else {
		while ($row = jn_fetch_object($res)) {
            $result[] = $row;
        }
    }
    return $result;
}


function montaGrid($nomeGrid,$colunas,$valores, $idGrid = 'Grid')
{	
	if (count($colunas) > 0) {
		 foreach($colunas as $itemColuna ){
			$nomeCampo[]    		= $itemColuna['nomecampo'];
			$tamanhoCampo[] 		= $itemColuna['tamanhocampo'];
			$escondeResponsivo[]    = $itemColuna['esconderesponsivo'];
			$tamanhoResponsivo[]    = $itemColuna['tamanhoresponsivo'];
			$quantidadeCampos		= $quantidadeCampos+1;
		 }
	}
	
	
	
	$first      = true;
	$retorno	= '<div class="table-header"> ' . $nomeGrid . ' </div>';
	$retorno	.= '<div>';
	$retorno	.= '<div id="dynamic-table_wrapper" class="dataTables_wrapper form-inline no-footer">';
	$retorno	.= '<div style="overflow:auto;">';	
	$retorno	.= '<table id="'.$idGrid.'" class="table table-striped table-bordered table-hover dataTable no-footer DTTT_selectable" role="grid" aria-describedby="dynamic-table_info">';
    
	$cor		= true;
	$vazio = true;
	//$cab = true;
	$idl = 1;
	//$vazio = false;
		
			 if ($first)
			 {
				 $retorno.= "<thead>";	
				 $retorno.= "<tr> ";
				 
				 for ($i = 0; $i < $quantidadeCampos; $i++) 
				 {
					if ($escondeResponsivo[$i] <> 'S'){
						if($tamanhoResponsivo[$i]<> ""){
								$classe = 'hidden-' . $tamanhoResponsivo[$i];
							}else{				
								$classe = 'hidden-480';			
							}
					}else{
							$classe = '';
					}			 
				 
					  $retorno.= '<th class="'. $classe .'" tabindex="0" aria-controls="dynamic-table" rowspan="1" colspan="1" aria-label="teste">' . str_replace('_',' ',$nomeCampo[$i]) . '</th>';
				 }
					 
				 $retorno .="</tr>";
				 $retorno.= "</thead>";
				 $retorno.= "<tbody>";
				 $first   = false;
			 }
			//$cab = false;
			//if (!$row){
			//	continue;
			//}
			
			//$cor 	= !$cor;
			
	if ((count($valores) > 0) ) {
		foreach($valores as $itemValores )
		{
			$vazio = false;
			if ($cor){
				$retorno.= '<tr id="'.$idGrid.'_Linha_'.$idl.'" class="odd" role="row"> ';
			}else {
				$retorno.= '<tr id="'.$idGrid.'_Linha_'.$idl.'"  class="even" role="row"> ';
			}
			$idl++;
			$cor 	= !$cor;
			
			$classe = "";
			 for ($i = 0; $i < $quantidadeCampos; $i++) 
			 {
				$nomeCampo[$i] = strtolower($nomeCampo[$i]);
				
				if ($escondeResponsivo[$i] <> 'S'){
					if($tamanhoResponsivo[$i]<> ""){
						$classe = 'hidden-' . $tamanhoResponsivo[$i];
					}else{				
						$classe = 'hidden-480';			
					}
				}else{
					$classe = '';
				}
				//if($nomeCampo[$i] == 'Excluir')	
				  //pr(str_replace(" "," ",$itemValores[$nomeCampo[$i]]),true);
						$retorno.= "<td class = '" . $classe . "'>" . $itemValores[$nomeCampo[$i]] . "&nbsp;</td>";
				  
			 }

		   
			 $retorno .= "</tr>";
			 
		}
	}
	if ($vazio){
	
	  $retorno .= '<table class="table table-striped table-bordered table-hover dataTable no-footer DTTT_selectable" id="'.$idGrid.'_Vazio"><tr><td>Nenhum registro para visualização.</td></tr></table>';
	}
    $retorno.= "</tbody>";
    $retorno.= "</table>";
	$retorno.= "</div>";	
	//$retorno.= $htmlPaginacao;
	$retorno.= "</div>";
    $retorno.= "</div>";	
	
	return $retorno;

}

function validaGuiaExistente($numeroGuia, $tipoGuia){ 
	if($tipoGuia == 'C'){
		$queryConsulta = 'SELECT NUMERO_GUIA FROM PS5300 WHERE NUMERO_GUIA = '. aspas($numeroGuia);	
		$resConsulta = jn_query($queryConsulta);
		if($rowConsulta = jn_fetch_object($resConsulta)){
			return $numeroGuia;
		}else{
			$numeroGuia = '';
			return $numeroGuia;
			
		}		
	}else if($tipoGuia == 'S'){
		$queryConsulta = 'SELECT NUMERO_GUIA FROM PS5400 WHERE NUMERO_GUIA = '. aspas($numeroGuia);	
		$resConsulta = jn_query($queryConsulta);
		if($rowConsulta = jn_fetch_object($resConsulta)){
			return $numeroGuia;
		}else{
			$numeroGuia = '';
			return $numeroGuia;
			
		}		
	}else if($tipoGuia == 'I'){
		$queryConsulta = 'SELECT NUMERO_GUIA FROM PS5500 WHERE NUMERO_GUIA = '. aspas($numeroGuia);	
		$resConsulta = jn_query($queryConsulta);
		if($rowConsulta = jn_fetch_object($resConsulta)){
			return $numeroGuia;
		}else{
			$numeroGuia = '';
			return $numeroGuia;
			
		}		
	}
}
function tabelaGrid($tabela){
	$queryCount = "SELECT COUNT(*) REGISTROS FROM cfgtabelas_sis WHERE  nome_tabela = ". aspas(strtoupper('VW_'.$tabela.'_GRID_AL2'));
	$resCount = jn_query($queryCount);
	if($rowCount = jn_fetch_object($resCount)){
		if($rowCount->REGISTROS>0)
			$tabela = strtoupper('VW_'.$tabela.'_GRID_AL2');
	}else{
		$queryCount = "SELECT COUNT(*) REGISTROS FROM cfgtabelas_sis WHERE  nome_tabela = ". aspas(strtoupper('VW_'.$tabela.'_GRID'));
		$resCount = jn_query($queryCount);
		if($rowCount = jn_fetch_object($resCount)){
			if($rowCount->REGISTROS>0)
				$tabela = strtoupper('VW_'.$tabela.'_GRID');
		}
	}
	
	return $tabela;
}


function validaCPF($cpf) {
	
	if (preg_match('/[a-zA-Z]/',    $cpf)) {		
		return false;
	}

    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;

}




function validaCNPJ($cnpj = null) 
{

     if(empty($cnpj))
        return false;

      // Remover caracteres especias
      $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

      // Verifica se o numero de digitos informados
      if (strlen($cnpj) != 14)
        return false;
      
      // Verifica se todos os digitos são iguais
      if (preg_match('/(\d)\1{13}/', $cnpj))
        return false;

      $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

      for ($i = 0, $n = 0; $i < 12; $n += $cnpj[$i] * $b[++$i]);

      if ($cnpj[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
      }
      
      for ($i = 0, $n = 0; $i <= 12; $n += $cnpj[$i] * $b[$i++]);
      
      if ($cnpj[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
      }
      
      return true;
  
}




function GeraProtocoloGeralPs6450($codigoAssociado, $codigoOperador, $status = '', $dataAbertura = '', $horaAbertura = ''){	
	
	$queryEmpresa = 'SELECT NUMERO_INSC_SUSEP FROM CFGEMPRESA';
	$resEmpresa = jn_query($queryEmpresa);
	$rowEmpresa = jn_fetch_object($resEmpresa);		
	
	$insSusep = $rowEmpresa->NUMERO_INSC_SUSEP;
	$insSusep = str_replace('-','',$insSusep);
	$insSusep = str_replace('.','',$insSusep);	
	$insSusep = str_pad($insSusep,6,0,STR_PAD_LEFT);
	
	$protocolo = jn_gerasequencial('PS6450');
	$protocolo = str_pad($protocolo,6,0,STR_PAD_LEFT);;
	
	$ano = date('Y');
	$mes = date('m');
	$dia = date('d');
	
	$numeroProtocoloGeral = $insSusep . $ano . $mes . $dia . $protocolo;
	
	if(!$status)
		$status = 'EM ABERTO';	
	
	if(!$dataAbertura)
		$dataAbertura = date('d/m/Y');	
	
	if(!$horaAbertura)
		$horaAbertura = date('H:i');
	
	if ($codigoOperador != '')
	    $codigoOperador = aspas($codigoOperador);
	else
		$codigoOperador	= ' null ';	
	
	$insertProtocolo  = ' INSERT INTO PS6450  ';
	$insertProtocolo .= ' (NUMERO_PROTOCOLO_GERAL, CODIGO_CADASTRO_CONTATO, CODIGO_OPERADOR, STATUS_PROTOCOLO, DATA_ABERTURA_PROTOCOLO,  ';
	$insertProtocolo .= ' HORA_ABERTURA_PROTOCOLO) VALUES (';
	$insertProtocolo .= aspas($numeroProtocoloGeral) . ', ' . aspas($codigoAssociado) . ', ' . $codigoOperador . ', ' . aspas($status) . ', ';
	$insertProtocolo .= DataToSql($dataAbertura) . ', ' . aspas($horaAbertura) . ' )';
	
	if(!jn_query($insertProtocolo)){
		echo 'ERRO AO INSERIR PROTOCOLO';		
	}else{
		return $numeroProtocoloGeral;		
	}
}

function SP_GERA_NUMERO_GUIA_TISS($TipoGuiaTiss, $ano, $NumAutorizacao){
	if( $_SESSION['type_db'] == 'firebird' )
		return	"SELECT * FROM SP_GERA_NUMERO_GUIA_TISS( '" . $TipoGuiaTiss . "', '" . date('y') . "' , '" . $NumAutorizacao . "')";
	elseif( $_SESSION['type_db'] == 'mssqlserver' )
		return	"EXEC SP_GERA_NUMERO_GUIA_TISS '" . $TipoGuiaTiss . "', '" . date('y') . "' , '" . $NumAutorizacao . "'";
}

function validaCNS($cns) {
	
	if (preg_match('/[a-zA-Z]/',    $cns)) {		
		return false;
	}

    // Extrai somente os n�meros
    $cns = preg_replace( '/[^0-9]/is', '', $cns );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cns) != 15) {
        return false;
    }

    // Verifica se foi informada uma sequ�ncia de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cns)) {
        return false;
    }
   
    return true;

}

function validaNome($nome) {	
    
	if (!preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ'\s]+$/",  $nome)) {		        
		return false;
	}
   
    return true;

}

function validaRG($numeroRG) {
	
	if (preg_match('/[a-zA-Z]/',    $numeroRG)) {		
		return false;
	}

    // Extrai somente os n�meros
    $numeroRG = preg_replace( '/[^0-9]/is', '', $numeroRG );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($numeroRG) != 8) {
        return false;
    }

    // Verifica se foi informada uma sequ�ncia de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $numeroRG)) {
        return false;
    }
   
    return true;

}

function validaTokenInvalido($codigoAssociado, $token, $tabela = ''){
    $motivoNegativa = 'TOKEN INEXISTENTE';

    $queryPrincipal = "select * From ESP_TOKEN  WHERE CODIGO_ASSOCIADO =" . aspas($codigoAssociado).' and TOKEN = '.aspas($token);
    $resultQuery    = jn_query($queryPrincipal);
    if($objResult   = jn_fetch_object($resultQuery)){

        if($objResult->DATA_UTILIZACAO){
            $motivoNegativa = 'TOKEN UTILIZADO ANTES';
        }else{
            $motivoNegativa = 'TOKEN EXPIRADO';
        }
    }

    date_default_timezone_set('America/Sao_Paulo');		
    $hora = date('H:i', time());

    $insertToken  = ' INSERT INTO ESP_TOKEN_INVALIDOS ';
    $insertToken .= ' (PERFIL_OPERADOR, CODIGO_IDENTIFICACAO, DATA_NEGATIVA, HORA_NEGATIVA, COD_ASSOC_PREENCHIDO, TOKEN_PREENCHIDO, MOTIVO_NEGATIVA, TABELA) VALUES ';
    $insertToken .= ' ( ' . aspas($_SESSION['perfilOperador']) . ',' . aspas($_SESSION['codigoIdentificacao']) . ', GETDATE() , ' . aspas($hora) . ', ';
    $insertToken .=  aspas($codigoAssociado) . ', ' . aspas($token) . ', ' . aspas(jn_utf8_encode($motivoNegativa)) . ',' . aspas($tabela) . ' )';
    jn_query($insertToken);
}

function retornaIdAssociadoZsPay($codigoAssociado, $associadoVnd = false){
    
    $retorno = Array();        
    
    if($associadoVnd == "false" or $associadoVnd == 0 or $associadoVnd == false){
        $query  = ' SELECT  ';
        $query .= '     PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.NUMERO_CPF, PS1000.DATA_NASCIMENTO, PS1001.ENDERECO_EMAIL, PS1000.SEXO, PS1006.NUMERO_TELEFONE, ';
        $query .= '     PS1001.ENDERECO, PS1001.CIDADE, PS1001.ESTADO, PS1001.CEP, PS1000.ID_CLIENTE_ZSPAY ';
        $query .= ' FROM PS1000 ';
        $query .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
        $query .= ' INNER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO) ';
        $query .= ' WHERE PS1000.CODIGO_ASSOCIADO = '. aspas($codigoAssociado);
    }else{
        $query  = ' SELECT  ';
        $query .= '     VND1000_ON.CODIGO_ASSOCIADO, VND1000_ON.NOME_ASSOCIADO, VND1000_ON.NUMERO_CPF, VND1000_ON.DATA_NASCIMENTO, VND1001_ON.ENDERECO_EMAIL, VND1000_ON.SEXO, VND1001_ON.NUMERO_TELEFONE_01 AS NUMERO_TELEFONE, ';
        $query .= '     VND1001_ON.ENDERECO, VND1001_ON.CIDADE, VND1001_ON.ESTADO, VND1001_ON.CEP, VND1000_ON.ID_CLIENTE_ZSPAY ';
        $query .= ' FROM VND1000_ON ';
        $query .= ' INNER JOIN VND1001_ON ON (VND1000_ON.CODIGO_ASSOCIADO = VND1001_ON.CODIGO_ASSOCIADO) ';        
        $query .= ' WHERE VND1000_ON.CODIGO_ASSOCIADO = '. aspas($codigoAssociado);
    }
    $resultado = qryUmRegistro($query);
    
    $nomeAssociado = $resultado->NOME_ASSOCIADO;
    $numeroCPF = sanitizeString($resultado->NUMERO_CPF);
    $dataNascimento = DataToSql(SqlToData($resultado->DATA_NASCIMENTO), false);
    $enderecoEmail = $resultado->ENDERECO_EMAIL;
    $telefone = $resultado->NUMERO_TELEFONE;
    $sexo = $resultado->SEXO;
    $idClienteZsPay = $resultado->ID_CLIENTE_ZSPAY;

    $auxEndereco = sanitizeString($resultado->ENDERECO);
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
    $dadosEndereco['estado'] = $resultado->ESTADO;

    $criterioWhereGravacao = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);

    if(!isset($idClienteZsPay)){
        $retornoCliente = cria_cliente_zspay($nomeAssociado, $numeroCPF, $dataNascimento, $enderecoEmail, $telefone, $sexo, $dadosEndereco);        

        if($retornoCliente['ID']){
            $tabela = 'PS1000';
            if($associadoVnd == "1" or $associadoVnd == 1 or $associadoVnd == true)
                $tabela = 'VND1000_ON';

            $sqlEdicao   = linhaJsonEdicao('ID_CLIENTE_ZSPAY', $retornoCliente['ID']);        
            gravaEdicao($tabela, $sqlEdicao, 'A', $criterioWhereGravacao);
            $idClienteZsPay = $retornoCliente['ID'];            
        }
    }

    if(isset($idClienteZsPay)){
        $retorno['STATUS'] = 'OK';
        $retorno['ID'] = $idClienteZsPay;
    }else{
        $retorno['STATUS'] = 'ERRO';
        $retorno['MSG'] = 'Erro ao realizar integração com a plataforma de pagamento';            
    }

    return $retorno;
}

function retornaIdAssociadoPagbank($codigoAssociado){
    
    $retorno = Array();

    $query  = ' SELECT  ';
    $query .= '     PS1000.CODIGO_ASSOCIADO, PS1000.NOME_ASSOCIADO, PS1000.NUMERO_CPF, PS1000.DATA_NASCIMENTO, PS1001.ENDERECO_EMAIL, PS1006.NUMERO_TELEFONE, ';
    $query .= '     PS1001.ENDERECO, PS1001.CIDADE, PS1001.ESTADO,PS1001.BAIRRO, PS1001.CEP,PS1006.CODIGO_AREA,PS1000.NOME_MAE, PS1000.ID_CLIENTE_PAGBANK ';
    $query .= ' FROM PS1000 ';
    $query .= ' INNER JOIN PS1001 ON (PS1000.CODIGO_ASSOCIADO = PS1001.CODIGO_ASSOCIADO) ';
    $query .= ' INNER JOIN PS1006 ON (PS1000.CODIGO_ASSOCIADO = PS1006.CODIGO_ASSOCIADO) ';
    $query .= ' WHERE PS1000.CODIGO_ASSOCIADO = '. aspas($codigoAssociado);
    $resultado = qryUmRegistro($query);
   
    $nomeAssociado      = $resultado->NOME_ASSOCIADO;
    $numeroCPF          = sanitizeString($resultado->NUMERO_CPF);
    $dataNascimento     = DataToSql(SqlToData($resultado->DATA_NASCIMENTO), false);
    $enderecoEmail      = $resultado->ENDERECO_EMAIL;
    $telefone           = formatarTelefone($resultado->NUMERO_TELEFONE);
    $idClientePagBank   = $resultado->ID_CLIENTE_PAGBANK;
    $nomeMae            = $resultado->NOME_MAE;
    $codArea            = $resultado->CODIGO_AREA;
    $bairro             = $resultado->BAIRRO;
    $cep                = sanitizeString($resultado->CEP);

    $auxEndereco =$resultado->ENDERECO;
    $auxEndereco = explode(',',$auxEndereco);
    $endereco    = $auxEndereco[0];
    
    $numero = '';
    if(count($auxEndereco)>1){
        $auxEndereco = explode('-',$auxEndereco[1]);
        $numero      = $auxEndereco[0]; 
        $complemento = "nao tem";

        if(count($auxEndereco)>1){
            $complemento = $auxEndereco[1];
        }
    }

    $dadosEndereco = Array();
    $dadosEndereco['endereco']     = $endereco;
    $dadosEndereco['numero']       = $numero;
    $dadosEndereco['complemento']  = $complemento;
    $dadosEndereco['cep']          = $cep;
    $dadosEndereco['cidade']       = $resultado->CIDADE;
    $dadosEndereco['estado']       = $resultado->ESTADO;
    $dadosEndereco['telefone']     = $telefone;
    $dadosEndereco['cpf']          = $numeroCPF;
    $dadosEndereco['email']        = $enderecoEmail;
    $dadosEndereco['dtNascimento'] = $dataNascimento;
    $dadosEndereco['area']         = $codArea;
    $dadosEndereco['nomeMae']      = $nomeMae;
    $dadosEndereco['bairro']       = $bairro;
    $dadosEndereco['nome']         = $nomeAssociado;
    
    $criterioWhereGravacao         = ' CODIGO_ASSOCIADO = ' . aspas($codigoAssociado);
    
    if(!isset($idClientePagBank)){

        $idApp          = cria_aplicacao_pagbank($nomeAssociado);
       
        $idAplicacao    = $idApp['account_id'];
        $clientId       = $idApp['client_id'];
        $clientSecret   = $idApp['client_secret'];
      
        $idConta        = cria_conta_pagbank($dadosEndereco,$categoria = 'HEALTH_AND_BEAUTY_SERVICE', $clientId, $clientSecret);
        $idConta        = $idConta['ID'];

        if(!isset($idConta)){

            $sqlEdicao        = linhaJsonEdicao('ID_CLIENTE_PAGBANK', $idConta);
            gravaEdicao('PS1000', $sqlEdicao, 'A', $criterioWhereGravacao);
            $idClientePagBank = $idConta;

        }
    }

    if(isset($idClientePagBank)){

        $retorno['STATUS'] = 'OK';
        $retorno['ID']     = $idClientePagBank;
        $retorno['dados'] = $dadosEndereco;

    }else{

        $retorno['STATUS']  = 'ERRO';
        $retorno['MSG']     = 'Erro ao realizar integração com a plataforma de pagamento'; 

    }

    return $retorno;
}
?>
