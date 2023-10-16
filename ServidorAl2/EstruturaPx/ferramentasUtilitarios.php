<?php

	require('../lib/base.php');
	require('../EstruturaPrincipal/processoPx.php');
	require('../lib/sysutilsAlianca.php');
	require('../EstruturaPx/carregaProcessosNovos.php');

	header("Content-Type: text/html; charset=ISO-8859-1",true);

	//  $_GET['Teste'] = 'OK';
	//  prDebug($_POST['ID_PROCESSO']);

	set_time_limit(0);

	echo apresentaMensagemInicioProcesso();

	//pr($_POST['ID_PROCESSO']);

	//require("../services/executaprocessos.php?opForm=executarProcesso&idProcessoInterno=" . $_POST['ID_PROCESSO']);

	//return;
	/* ---------------------------------------------------------------------------------- */
	/* Aqui o sistema atualiza os novos processos ou relatórios nas tabelas de parâmetros */
	/* ---------------------------------------------------------------------------------- */

	if ($_POST['TIPO_UTILITARIO_EXECUTAR']=='ATU_PROCESSOS')
	{
		validaCarregamentoNovosProcessos();
		$resultadoProcesso['MSG_RESULTADO'] = 'Carregamento realizado, agora ajuste o campo "dados_link_pagina" da tabela "cfgmenu_dinamico_net_al2" !';
	}


	/* ---------------------------------------------------------------------------------- */
	/* Aqui o sistema verifica se precisa criar novos campos nas tabelas (UpdatePk)       */
	/* ---------------------------------------------------------------------------------- */

	if ($_POST['TIPO_UTILITARIO_EXECUTAR']=='ATU_UPDATEPK')
	{
		executaUpdatePk(); 
		$resultadoProcesso['MSG_RESULTADO'] = 'Ok, atualização de tabelas executadas, agora o mapeamento precisa ser feito.';
	}


	/* ----------------------------------------------------------------------------------------------------------------- */
	/* Aqui o sistema verifica se precisa mapear campos nas tabelas CFGTABELAS_sis, cfgcampos_sis, cfgcampos_cd, etc...  */
	/* ----------------------------------------------------------------------------------------------------------------- */

	if ($_POST['TIPO_UTILITARIO_EXECUTAR']=='ATU_TABELAS_SISTEMA')
	{
		$resultado                          = mapeiaTabelasSistema(); 
		$resultadoProcesso['MSG_RESULTADO'] = 'Ok, o mapeamento das tabelas do sistema foi atualizado com sucesso<br> 
										 	   Tabelas novas: ' . $resultado['TABELAS_NOVAS'] . '<br>
										 	   Campos novos: ' . $resultado['CAMPOS_NOVOS']  . '<br>
										 	   Campos excluidos: ' . $resultado['CAMPOS_EXCLUIDOS']  . '<br>
										 	   Campos Novos Net-CD: ' . $resultado['CAMPOS_NOVOS_CD'];

	}


	$resultado = registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',
		                                   $resultadoProcesso['MSG_RESULTADO'],$nomeArquivoProcesso, $nomearquivoLogProcesso);







function executaUpdatePk()
{

	// tem que ser implementado //

}




function mapeiaTabelasSistema($tabelaEspecifica = '')
{

	//$tabelaEspecifica    = 'PS1000';

	$resultadoMapeamento = array();

	$resultadoMapeamento['TABELAS_NOVAS']    = 0;
	$resultadoMapeamento['CAMPOS_NOVOS']     = 0;
	$resultadoMapeamento['CAMPOS_EXCLUIDOS'] = 0;
	$resultadoMapeamento['CAMPOS_NOVOS_CD']  = 0;

	/* ---------------------------------------------------------------------- */
	/* Verifica se precisar criar tabelas CFGTABELAS_SIS                      */
	/* ---------------------------------------------------------------------- */


	if ($_SESSION['type_db'] == 'sqlsrv')	
	{

		if ($tabelaEspecifica != '')
           $sqlTabelaEspecifica = ' AND T.TABLE_NAME = ' . aspas($tabelaEspecifica);

        $resTabelas = jn_query(' SELECT CAST(T.TABLE_NAME AS VARCHAR(50)) Rdb_Relation_Name 
                                 FROM INFORMATION_SCHEMA.Tables T JOIN INFORMATION_SCHEMA.Columns C 
                                 ON T.TABLE_NAME = C.TABLE_NAME 
                                 WHERE T.TABLE_NAME NOT LIKE ' . aspas('sys%') . '
                                 AND T.TABLE_NAME <> ' . aspas('dtproperties') . ' 
                                 AND T.TABLE_SCHEMA <> ' . aspas('INFORMATION_SCHEMA') . $sqlTabelaEspecifica . '
                                 GROUP BY T.TABLE_NAME ORDER BY T.TABLE_NAME');
     }
     else
     {

		if ($tabelaEspecifica != '')
           $sqlTabelaEspecifica = ' AND Rdb$Relation_Name = ' . aspas($tabelaEspecifica);

        $resTabelas = jn_query('Select Rdb$Relation_Name Rdb_Relation_Name From Rdb$Relations Where (Rdb$Relation_name not like ' . aspas('%$%') . ') ' . $sqlTabelaEspecifica);

     }

	 while ($rowTabelas = jn_fetch_object($resTabelas))
	 {

         $rowTmp = qryUmRegistro('Select nome_tabela From CfgTabelas_Sis Where Nome_Tabela = ' . aspas(strtoupper(trim(CopyDelphi($rowTabelas->RDB_RELATION_NAME,1,30)))));

         if ($rowTmp->NOME_TABELA!='')
            Continue; 

         $sqlEdicao      = '';	
 		 $sqlEdicao 	.= linhaJsonEdicao('Nome_Tabela', copyDelphi(strToUpper(trim($rowTabelas->RDB_RELATION_NAME)),1,30));
 		 $sqlEdicao 	.= linhaJsonEdicao('Descricao_Tabela', strToUpper(trim($rowTabelas->RDB_RELATION_NAME)));

	  	 gravaEdicao('CfgTabelas_Sis', $sqlEdicao, 'I');

   	 	 $resultadoMapeamento['TABELAS_NOVAS']++;

	}


	/* ---------------------------------------------------------------------- */
	/* Verifica se precisar criar campos CFGCAMPOS_SIS                        */
	/* ---------------------------------------------------------------------- */


	if ($_SESSION['type_db'] == 'sqlsrv')	
	{
		if ($tabelaEspecifica != '')
           $sqlTabelaEspecifica = ' AND T.TABLE_NAME = ' . aspas($tabelaEspecifica);

        $resTabelas = jn_query('SELECT Upper(CAST(T.TABLE_NAME AS VARCHAR(50))) Rdb_Relation_Name
                  				FROM INFORMATION_SCHEMA.Tables T JOIN INFORMATION_SCHEMA.Columns C 
                  				ON T.TABLE_NAME = C.TABLE_NAME
                  				WHERE T.TABLE_NAME NOT LIKE ' . aspas('sys%') . '
                  				AND T.TABLE_NAME <> ' . aspas('dtproperties') . '
				  				AND T.TABLE_SCHEMA <> ' . aspas('INFORMATION_SCHEMA') . $sqlTabelaEspecifica . '  
                  				GROUP BY T.TABLE_NAME ORDER BY T.TABLE_NAME');
     } 
     else
     {

		if ($tabelaEspecifica != '')
           $sqlTabelaEspecifica = ' AND Rdb$Relation_Name = ' . aspas($tabelaEspecifica);

        $resTabelas = jn_query('Select Rdb$Relation_Name Rdb_Relation_Name From Rdb$Relations Where (Rdb$Relation_name not like ' . aspas('%$%') . ') ' . $sqlTabelaEspecifica);
     }
                                 

	 while ($rowTabelas = jn_fetch_object($resTabelas))
	 {

		 if ($_SESSION['type_db'] == 'sqlsrv')	
            $resCampos = jn_query('SELECT Upper(CAST(T.TABLE_NAME AS VARCHAR(50))) Nome_Tabela, Upper(CAST(C.COLUMN_NAME AS VARCHAR(40))) rdb_field_name, 
                                  CAST(C.IS_NULLABLE AS VARCHAR(20)) rdb_null_flag, CAST(C.DATA_TYPE AS VARCHAR(40)) rdb_type_name, 
                                  CHARACTER_MAXIMUM_LENGTH rdb_field_length 
                                  FROM INFORMATION_SCHEMA.Tables T JOIN INFORMATION_SCHEMA.Columns C 
                                  ON T.TABLE_NAME = C.TABLE_NAME 
                                  WHERE T.TABLE_NAME NOT LIKE ' . aspas('sys%') . '
                                  AND T.TABLE_NAME <> ' . aspas('dtproperties') . '
                                  AND T.TABLE_SCHEMA <> ' . aspas('INFORMATION_SCHEMA') . ' 
                                  And upper(T.TABLE_NAME) = ' . aspas(strToUpper($rowTabelas->RDB_RELATION_NAME)) . '
                                  ORDER BY T.TABLE_NAME, C.ORDINAL_POSITION');
         else
            $resCampos = jn_query('Select Trim(r.rdb$field_name) rdb_field_name, Trim(t.rdb$type_name) rdb_type_name, Trim(f.rdb$field_length) rdb_field_length, 
            	                         Trim(r.rdb$null_flag) rdb_null_flag, 
                                         Trim(f.rdb$validation_source) rdb_validation_source 
                                         from rdb$relation_fields r  
                                         join rdb$fields f on (f.rdb$field_name = r.rdb$field_source) 
                                         join rdb$types t on (f.rdb$field_type = t.rdb$type) 
                                         where (r.rdb$relation_name = ' . aspas(strToUpper($rowTabelas->RDB_RELATION_NAME)) . ') and 
                                               (t.rdb$field_name = ' . aspas('RDB$FIELD_TYPE') . ') 
                                         Order By R.rdb$field_position ');

         $ordemCriacao = 1;

		 while ($rowCampos = jn_fetch_object($resCampos))
		 {
		 		$ordemCriacao++;

              	$rowTmp = qryUmRegistro('Select NUMERO_REGISTRO From CfgCampos_Sis Where (Nome_Tabela = ' . aspas(trim(strToUpper($rowTabelas->RDB_RELATION_NAME))) . ') 
                	                     And (Nome_Campo = ' . aspas(trim(strToUpper($rowCampos->RDB_FIELD_NAME))) . ')');

              	if ($rowTmp->NUMERO_REGISTRO!='')
              	{
              		continue;
              	}

				$sqlEdicao   = '';

			    if ($_SESSION['type_db'] != 'sqlsrv')	
					$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro',jn_gerasequencial('CfgCampos_Sis'));

				$sqlEdicao 	.= linhaJsonEdicao('Nome_Tabela', copyDelphi(strToUpper(trim($rowTabelas->RDB_RELATION_NAME)),1,30));
				$sqlEdicao 	.= linhaJsonEdicao('Nome_Campo', copyDelphi(strToUpper(trim(mascaraNomeCampo($rowCampos->RDB_FIELD_NAME))),1,30));
				$sqlEdicao 	.= linhaJsonEdicao('Label_Campo',trim(mascaraNomeCampo($rowCampos->RDB_FIELD_NAME)));
				$sqlEdicao 	.= linhaJsonEdicao('Numero_Ordem_Criacao', $ordemCriacao);
				$sqlEdicao 	.= linhaJsonEdicao('Descricao_Campo',trim(mascaraNomeCampo($rowCampos->RDB_FIELD_NAME)));
				$sqlEdicao 	.= linhaJsonEdicao('Numero_Pasta', '1');
				$sqlEdicao 	.= linhaJsonEdicao('Flag_Permite_Minusculo','N');
				$sqlEdicao 	.= linhaJsonEdicao('FLAG_APRESENTA_DBGRID','N');

                if ((strtoUpper($rowCampos->RDB_TYPE_NAME) == 'LONG') Or
                    (strtoUpper($rowCampos->RDB_TYPE_NAME) == 'SHORT'))
				   $sqlEdicao 	.= linhaJsonEdicao('Tipo_Campo','INTEGER');
                else if (strtoUpper($rowCampos->RDB_TYPE_NAME) == 'VARYING') 
				   $sqlEdicao 	.= linhaJsonEdicao('Tipo_Campo','VARCHAR');
                Else if (strtoUpper($rowCampos->RDB_TYPE_NAME) == 'TEXT') 
				   $sqlEdicao 	.= linhaJsonEdicao('Tipo_Campo','CHAR');
                Else if (strtoUpper($rowCampos->RDB_TYPE_NAME) == 'TIMESTAMP') 
				   $sqlEdicao 	.= linhaJsonEdicao('Tipo_Campo','DATE');
                Else if (strtoUpper($rowCampos->RDB_TYPE_NAME) == 'DOUBLE') 
				   $sqlEdicao 	.= linhaJsonEdicao('Tipo_Campo','NUMERIC');
                Else
				   $sqlEdicao 	.= linhaJsonEdicao('Tipo_Campo',strtoUpper($rowCampos->RDB_TYPE_NAME));

                If (strToUpper($rowCampos->TIPO_CAMPO)=='INTEGER')
			   		$sqlEdicao 	.= linhaJsonEdicao('Tamanho_Campo','4');
                else
			   		$sqlEdicao 	.= linhaJsonEdicao('Tamanho_Campo',$rowCampos->RDB_FIELD_LENGTH);

				$tabelaRelacionada    = '';
		   		$flagChavePrimaria    = 'N';
		   		$flagChaveEstrangeira = 'N';

			 	if ($_SESSION['type_db'] == 'sqlsrv')	
                {

                    $qryTmp = qryUmRegistro('SELECT Cast(OBJECT_NAME(f.parent_object_id) As Varchar(40)) AS TableName, 
                                                Cast(COL_NAME(fc.parent_object_id, fc.parent_column_id) As Varchar(40)) AS ColumnName, 
                                                Cast(OBJECT_NAME (f.referenced_object_id) As Varchar(40)) AS ReferenceTableName, 
                                                Cast(COL_NAME(fc.referenced_object_id,fc.referenced_column_id) As Varchar(40)) AS ReferenceColumnName 
                                                FROM sys.foreign_keys AS f 
                                                INNER JOIN sys.foreign_key_columns AS fc ON f.OBJECT_ID = fc.constraint_object_id
                                                Where upper(OBJECT_NAME(f.parent_object_id)) = ' . aspas(strToUpper($rowTabelas->RDB_RELATION_NAME)) . ' AND
                                                upper(COL_NAME(fc.parent_object_id, fc.parent_column_id)) = ' . aspas(strtoupper($rowCampos->RDB_FIELD_NAME)));

                    if ($qryTmp->REFERENCETABLENAME != '')
                    {
				   		$tabelaRelacionada    = strtoupper($qryTmp->REFERENCETABLENAME);
				   		$flagChaveEstrangeira = 'S';
				   		$sqlEdicao 	         .= linhaJsonEdicao('Tabela_Relacionada',$tabelaRelacionada);
				   	}

                    $qryTmp = qryUmRegistro('SELECT Cast(COL_NAME(ic.OBJECT_ID,ic.column_id) As Varchar(40)) AS ColumnName 
                                             FROM sys.indexes AS i 
                                             INNER JOIN sys.index_columns AS ic 
                                             ON i.OBJECT_ID = ic.OBJECT_ID 
                                             AND i.index_id = ic.index_id 
                                             WHERE i.is_primary_key = 1 And 
                                             upper(OBJECT_NAME(ic.OBJECT_ID)) = ' . aspas(strtoupper($rowTabelas->RDB_RELATION_NAME)) . ' AND 
                                             upper(COL_NAME(ic.OBJECT_ID,ic.column_id)) = ' . aspas(strtoupper($rowCampos->RDB_FIELD_NAME)));

                    if ($qryTmp->COLUMNNAME!='')
				   		$flagChavePrimaria = 'S';

                }
                else
                {

                    $qryTmp = qryUmRegistro('select Trim(r.rdb$constraint_type) rdb_constraint_type, Trim(i.rdb$field_name) rdb_field_name 
                                            from rdb$relation_constraints r 
                                            join rdb$index_segments i on (r.rdb$index_name = i.rdb$index_name) 
                                            where (r.rdb$relation_name = ' . aspas($rowTabelas->RDB_RELATION_NAME) . ') 
                                             and  (i.rdb$Field_name = ' . aspas($rowCampos->RDB_FIELD_NAME) . ') 
                                             and ((r.rdb$constraint_type = ' . aspas('PRIMARY KEY') . ') or 
                                                  (r.rdb$constraint_type = ' . aspas('FOREIGN KEY') . '))');

                    if ($qryTmp->RDB_CONSTRAINT_TYPE == 'PRIMARY KEY')
				   		$flagChavePrimaria = 'S';
                    if ($qryTmp->RDB_CONSTRAINT_TYPE == 'FOREIGN KEY')
                    {

				   		$tabelaRelacionada     = strtoupper($qryTmp->RDB_RELATION_NAME);
				   		$flagChaveEstrangeira  = 'S';

                        $qryTmp1 = qryUmRegistro('select Trim(r.rdb$constraint_type) rdb_constraint_type, Trim(i.rdb$field_name) rdb_field_name, 
                          	                        Trim(r.rdb$relation_name) rdb_relation_name 
                                                    from rdb$relation_constraints r 
                                                    join rdb$index_segments i on (r.rdb$index_name = i.rdb$index_name) 
                                                    where (I.rdb$field_name = ' . aspas($rowCampos->RDB_FIELD_NAME) . ') 
                                                    and (r.rdb$constraint_type = ' . aspas('PRIMARY KEY') . ') Order By r.rdb$relation_name');

                        if ($qryTmp1->RDB_RELATION_NAME!='')
				   		    $sqlEdicao 	.= linhaJsonEdicao('Tabela_Relacionada',$qryTmp1->RDB_RELATION_NAME);
                         
                	}
                }

		   		$sqlEdicao 	.= linhaJsonEdicao('Flag_ChavePrimaria',$flagChavePrimaria);
		   		$sqlEdicao  .= linhaJsonEdicao('Flag_ChaveEstrangeira',$flagChaveEstrangeira);

                // Informação sobre o valor default do campo na inclusão.

                if (copyDelphi(strtoupper($rowCampos->RDB_FIELD_NAME),1,5) == 'FLAG_')
				    $sqlEdicao 	.= linhaJsonEdicao('Informacao_Default','N');
                else if ((copyDelphi(strtoupper($rowCampos->RDB_FIELD_NAME),1,6) == 'VALOR_') Or
                         (copyDelphi(strtoupper($rowCampos->RDB_FIELD_NAME),1,11) == 'PERCENTUAL_'))
				    $sqlEdicao 	.= linhaJsonEdicao('Informacao_Default','0');

                // Informação se é para criar o campo no form de edição ou não.

                if ($rowCampos->RDB_FIELD_NAME == 'NUMERO_REGISTRO') 
				    $sqlEdicao 	.= linhaJsonEdicao('Comportamento_Frm_Edicao','3');
                else
				    $sqlEdicao 	.= linhaJsonEdicao('Comportamento_Frm_Edicao','1');

                // Informação sobre o tipo de campo a criar no form edição.

                if (copyDelphi(strtoupper($rowCampos->RDB_FIELD_NAME),1,5) == 'FLAG_')
				    $sqlEdicao 	.= linhaJsonEdicao('Tipo_Componente','DBCHECKBOX');
                else if ((copyDelphi(strtoupper($rowCampos->RDB_FIELD_NAME),1,5) == 'DATA_') Or
                         (copyDelphi(strtoupper($rowCampos->RDB_TYPE_NAME),1,4)) == 'DATE')
				    $sqlEdicao 	.= linhaJsonEdicao('Tipo_Componente','DBDATEEDIT');
                else if ((copyDelphi(strtoupper($rowCampos->RDB_FIELD_NAME),1,6) == 'VALOR_') Or
                         (copyDelphi(strtoupper($rowCampos->RDB_FIELD_NAME),1,11) == 'QUANTIDADE_') Or
                         (copyDelphi(strtoupper($rowCampos->RDB_TYPE_NAME),1,7) == 'NUMERIC'))
				    $sqlEdicao 	.= linhaJsonEdicao('Tipo_Componente','RXDBCALCEDIT');
                else if ($tabelaRelacionada!='') 
				    $sqlEdicao 	.= linhaJsonEdicao('Tipo_Componente','RXLOOKUPEDIT');
                else if (copyDelphi(strtoupper($rowCampos->RDB_TYPE_NAME),1,4) == 'BLOB')
				    $sqlEdicao 	.= linhaJsonEdicao('Tipo_Componente','DBRICHEDIT');
                else
				    $sqlEdicao 	.= linhaJsonEdicao('Tipo_Componente','DBEDIT');

                // Informação se a chave primária da tabela é criar por generator, procedure, ou não é criada.
                $tipoChaveAutomatica = '1';

			 	if ($_SESSION['type_db'] == 'sqlsrv')	
                {
                   if ($flagChavePrimaria == 'S')
				        $tipoChaveAutomatica = '2';
				}
                else
                {
	                if ($flagChavePrimaria == 'S')
                    {
                        $qryTmp2 = qryUmRegistro('Select RDB$GENERATOR_NAME GENERATOR_NAME From Rdb$generators Where (Rdb$Generator_name = ' . aspas('I_C_' . $rowTabelas->RDB_RELATION_NAME) . ')');

                        if ($qryTmp2->GENERATOR_NAME != '')
				    	    $tipoChaveAutomatica = '3';
				    	else
				    	{	
                        	$qryTmp2 = qryUmRegistro('Select nome_tabela From CfgSequencias Where (Nome_Tabela = ' . aspas($rowTabelas->RDB_RELATION_NAME) . ')');

                          	if ($qryTmp2->NOME_TABELA!='')
				    		   $tipoChaveAutomatica = '2';
				    	}
                    }
                }

				$sqlEdicao 	.= linhaJsonEdicao('Tipo_Chave_Automatica',$tipoChaveAutomatica);
				$sqlEdicao 	.= linhaJsonEdicao('Sequencia_Combo','777');

              	// Informação se o campo aceita ou não registros nulos.

              	if (($rowCampos->RDB_NULL_FLAG == '1') Or
                	($rowCampos->RDB_NULL_FLAG == 'NAO') Or
                    ($rowCampos->RDB_NULL_FLAG == 'NO'))
				  	$sqlEdicao 	.= linhaJsonEdicao('Flag_NotNull','S');
              	else
					$sqlEdicao 	.= linhaJsonEdicao('Flag_NotNull','N');

			  	gravaEdicao('CfgCampos_Sis', $sqlEdicao, 'I');

				$resultadoMapeamento['CAMPOS_NOVOS']++;
                   
		}	  

	}




	/* ---------------------------------------------------------------------- */
	/* Verifica se precisar excluir campos                        			  */
	/* ---------------------------------------------------------------------- */


     $resCampos = jn_query('Select * From CfgCampos_Sis order by nome_tabela');

	 $loops = 0;    

	 while ($rowCampos = jn_fetch_object($resCampos))
	 {

	 	 $loops++;

	 	 if ($loops >= 10)
	 	 	Continue;

         if ($rowCampos->TIPO_CAMPO == 'SEPARADOR')
            Continue;

         if (campoExiste($rowCampos->NOME_TABELA, $rowCampos->NOME_CAMPO)===false)
         {
             //jn_query('Delete From CfgCampos_Sis Where (Numero_Registro = ' . numSql($rowCampos->NUMERO_REGISTRO) . ')');

		 	 $resultadoMapeamento['CAMPOS_EXCLUIDOS']++;
         }

     }




	/* ---------------------------------------------------------------------- */
	/* Verifica se precisa inserir nas entidades
	/* ---------------------------------------------------------------------- */

     jn_query('Insert Into CfgEntidade_Sis(Nome_Entidade, Descricao_Entidade, Objeto_Relacionado, Nome_entidade_Pai) 
               Values("CATEG_OUTRAS","OUTRAS TABELAS OU TABELAS NOVAS",Null,Null)',false, true, true);

     jn_query('Insert Into CfgEntidade_Sis(Nome_Entidade, Descricao_Entidade, Objeto_Relacionado, Nome_entidade_Pai) 
               select CfgTabelas_Sis.Nome_Tabela, CfgTabelas_Sis.Descricao_Tabela, CfgTabelas_Sis.Nome_Tabela,  "CATEG_OUTRAS" from cfgtabelas_sis 
               Where 
               (CfgTabelas_Sis.Nome_Tabela Not In (Select a.Nome_Entidade From CfgEntidade_Sis a Where (a.Nome_Entidade = CfgTabelas_Sis.Nome_Tabela))) And 
               ((CfgTabelas_Sis.Nome_Tabela Like "PS%") Or (CfgTabelas_Sis.Nome_Tabela Like "CFG%")) And 
               (CfgTabelas_Sis.Nome_Tabela Not Like "%BK%")',false, true, true);



	/* ---------------------------------------------------------------------- */
	/* Insere os campos na tabela cfgcampos_cd
	/* ---------------------------------------------------------------------- */


        $res = jn_query('INSERT INTO CFGCAMPOS_SIS_CD(NOME_TABELA ,NOME_CAMPO,LABEL_CAMPO, NUMERO_ORDEM_CRIACAO,        
						    TIPO_CAMPO, TAMANHO_CAMPO, FLAG_CHAVEPRIMARIA, TIPO_MASCARA, FLAG_CHAVEESTRANGEIRA, 
						    FLAG_NOTNULL,COMPORTAMENTO_FRM_EDICAO, TIPO_CHAVE_AUTOMATICA, VALOR_PADRAO, OPCOES_COMBO,
						    COMPONENTE_FORMULARIO, PASTA_APRESENTACAO, HINT_EXPLICACAO, NOME_TABELA_RELACIONADA, CAMPO_ID_TABELA_RELAC,
						    CAMPO_PESQUISA_TABELA_RELAC, FLAG_EXIBIR_GRID, CLASSE_CAMPO)
				SELECT NOME_TABELA      ,NOME_CAMPO      ,LABEL_CAMPO                ,NUMERO_ORDEM_CRIACAO
				      ,TIPO_CAMPO      ,TAMANHO_CAMPO    ,FLAG_CHAVEPRIMARIA         ,TIPO_MASCARA
				      ,FLAG_CHAVEESTRANGEIRA             ,coalesce(FLAG_NOTNULL,"N") ,COMPORTAMENTO_FRM_EDICAO
				      ,TIPO_CHAVE_AUTOMATICA             ,INFORMACAO_DEFAULT         ,replace(opcoes_combo,",",";")
					  ,case 
					     When TIPO_COMPONENTE = "DBRICHEDIT" then "TEXTAREA"
					     When TIPO_COMPONENTE = "SEPARADOR" then "DIVISORIA"
					     When TIPO_COMPONENTE = "BUTTON" then "BUTTON"
					     When TIPO_COMPONENTE = "DBCOMBOBOX" then "COMBOBOX"
					     When TIPO_COMPONENTE = "TCOMBOBOX" then "COMBOBOX"
					     When TIPO_COMPONENTE = "DBDATEEDIT" then "DATE"
					     When TIPO_COMPONENTE = "RXLOOKUPEDIT" then "AUTOCOMPLETE"
					     When TIPO_COMPONENTE = "RXDBCALCEDIT" then "NUMBER"
					     When TIPO_COMPONENTE = "DBCHECKBOX" then "CHECKBOX"
					     When TIPO_COMPONENTE = "DBEDIT" then "TEXT"
						 else "TEXT" 
					   end ,
					   Case 
					      When NUMERO_PASTA = 0 THEN "1-Dados principais"
					      When NUMERO_PASTA = 1 THEN "2-Informacoes auxiliares"
					      When NUMERO_PASTA = 2 THEN "3-Outras informacoes(1)"
					      When NUMERO_PASTA = 3 THEN "4-Outras informacoes(2)"
					      When NUMERO_PASTA = 4 THEN "5-Outras informacoes(3)"
					  end 	  
					  NUMERO_PASTA, COALESCE(DESCRICAO_CAMPO, LABEL_CAMPO), TABELA_RELACIONADA, 
					  case 
					     when TABELA_RELACIONADA is not null then (select TOP 1 a.NOME_CAMPO from cfgcampos_sis A WHERE (A.NOME_TABELA = tabFonte.TABELA_RELACIONADA) AND (A.FLAG_CHAVEPRIMARIA = "S"))
					  else null
					  end, 
					  case 
					     when TABELA_RELACIONADA is not null then (select TOP 1 a.NOME_CAMPO from cfgcampos_sis A WHERE (A.NOME_TABELA = tabFonte.TABELA_RELACIONADA) AND ((A.NOME_CAMPO LIKE "NOME%") OR (A.NOME_CAMPO LIKE "DESCRI%")) ORDER BY A.NUMERO_REGISTRO)
					  else null
					  end, 
					  FLAG_APRESENTA_DBGRID, "400px"
				FROM CFGCAMPOS_SIS tabFonte
				where (TabFonte.NOME_TABELA + tabFonte.NOME_CAMPO not in (Select cfgCampos_sis_cd.NOME_TABELA + cfgcampos_sis_cd.nome_campo from CFGCAMPOS_SIS_cd
				       where cfgCampos_sis_cd.NOME_TABELA + cfgcampos_sis_cd.nome_campo = TabFonte.NOME_TABELA + tabFonte.NOME_CAMPO ))
				order by nome_tabela, numero_registro');

	$resultadoMapeamento['CAMPOS_NOVOS_CD']  = jn_affected_rows($res); 

	return $resultadoMapeamento;

}


?>