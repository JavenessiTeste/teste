<?php


function excluirRegistro_ERPPx($tabela,$registro,$nomeChave, $reativar = 'N'){
	
	$retorno = array();
	
	$retorno['STATUS'] = 'OK';  //OK ERRO 
	$retorno['MSG']    = '';    //MENSAGEM ERRO


	/* --------------------------------------------------------------------------------------------------------------------- */

	if($tabela=='PS1010')
	{
		if ($reativar == 'S')
		{

		}		
		else
		{
			jn_query('Update Ps1000 Set Data_Exclusao = ' . dataToSql(date('d/m/Y')) . ' where data_exclusao is null and ' . 
						$nomeChave . ' = ' . aspas($registro));

			jn_query('Update Ps1010 Set Data_Exclusao = ' . dataToSql(date('d/m/Y')) . ' where data_exclusao is null and ' . 
						$nomeChave . ' = ' . aspas($registro));

			$retorno['STATUS'] = 'OK';	
			$retorno['MSG'] = 'Ok, empresa e beneficiários vinculados a mesma excluída com sucesso!'; 		
		}

	}	
	else if($tabela=='PS1000')
	{
		if ($reativar == 'S')
		{

		}		
		else
		{
			$codigoMotivoExc = retornaValorConfiguracao('MOT_EXCLUSAO_PS1000');
			$codigoAssociado = $registro;
			$dataExclusao = '';	
			$complementoExclusaoAssoc = '';	

			if ($_SESSION['type_db'] == 'sqlsrv')
				$dataExclusao = ' DATA_EXCLUSAO = CONVERT(date, GETDATE()), ';			
			else
				$dataExclusao = ' DATA_EXCLUSAO = CURRENT_TIMESTAMP, ';
			
			$queryExc  = ' UPDATE PS1000 SET ';		
			$queryExc .= $dataExclusao;	
			$queryExc .= ' CODIGO_MOTIVO_EXCLUSAO = ' . aspasNull($codigoMotivoExc) . ',';	
			$queryExc .= $complementoExclusaoAssoc;	
			$queryExc .= ' INFORMACOES_LOG_E = ' . aspas('[E' . $_SESSION['codigoIdentificacao'] . ' WEB D' . date('d/m/Y') . ']');	
			$queryExc .= ' WHERE ((CODIGO_ASSOCIADO = ' . aspas($codigoAssociado) . ') OR (CODIGO_TITULAR = ' . aspas($codigoAssociado) . ')) ';

			jn_query($queryExc);

			$retorno['STATUS'] = 'OK';	
			$retorno['MSG'] = 'Exclusão realizada com sucesso.'; 			
		}
	}
	else
	{
		if ($reativar == 'S')
		{

				$sqlDelete = 'Update  '. strtolower($tabela).' set DATA_INUTILIZ_REGISTRO = null where '.strtolower($nomeChave).' = '.aspas($registro);
				$resDelete = jn_query($sqlDelete, false, true, true);

				if(!$resDelete)
				{
					$sqlDelete = 'Update ' . strtolower($tabela).' set DATA_EXCLUSAO = null where '.strtolower($nomeChave).' = '.aspas($registro);
					$resDelete = jn_query($sqlDelete);
				}

				if($resDelete)
				{
					$retorno['STATUS'] = 'OK';	
					$retorno['MSG']    = 'O registro foi recuperado com sucesso!'; 			
				}
				else
				{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = 'Não foi possivel recuperar o registro.';
				}

		}		
		else
		{
			$sqlDelete = ' delete from '. strtolower($tabela).' where '.strtolower($nomeChave).' = '.aspas($registro);
			$resDelete = jn_query($sqlDelete, false, true, true);

			if($resDelete)
			{
				$retorno['STATUS'] = 'OK';	
				$retorno['MSG'] = 'Ok, registro excluído com sucesso!'; 			
			}
			else
			{

				$msgOriginalErro = erroSql(jn_GetErroSql());

				$sqlDelete = 'Update ' . strtolower($tabela).' set DATA_INUTILIZ_REGISTRO = ' . dataToSql(date('d/m/Y')) . ' where '.strtolower($nomeChave).' = '.aspas($registro);
				$resDelete = jn_query($sqlDelete, false, true, true);

				if(!$resDelete)
				{
					$sqlDelete = 'Update ' . strtolower($tabela).' set DATA_EXCLUSAO = ' . dataToSql(date('d/m/Y')) . ' where '.strtolower($nomeChave).' = '.aspas($registro);
					$resDelete = jn_query($sqlDelete);
				}


				if($resDelete)
				{
					$retorno['STATUS'] = 'OK';	
					$retorno['MSG']    = 'O registro já está sendo utilizado em outras tabelas relacionadas.<br>
                                          Neste caso o registro não pode ser excluído, por isto o sistema inativou o registro!'; 			
				}
				else
				{
					$retorno['STATUS'] = 'ERRO';
					$retorno['MSG']    = $msgOriginalErro;
				}
			}
		}
	}			


	return $retorno;

}

?>