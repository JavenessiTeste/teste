<?php

require_once('../lib/base.php');
require_once('../EstruturaPrincipal/processoPx.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

set_time_limit(0);

//pr($_POST['ID_PROCESSO']);
//	$_GET['Teste'] = 'OK';


$rowDadosProcesso = qryUmRegistro('Select IDENTIFICACAO_PROCESSO from CFGDISPAROPROCESSOSCABECALHO WHERE NUMERO_REGISTRO_PROCESSO = ' . aspas($_POST['ID_PROCESSO']));

if ($rowDadosProcesso->IDENTIFICACAO_PROCESSO=='7011') 
{
	efetuaProgramacaoCoparticipacoesGuiasContasMedicas();
}






function efetuaProgramacaoCoparticipacoesGuiasContasMedicas()
{

    $rowTemp = qryUmRegistro('Select Mes_Ano_Referencia From Ps1067 Where (Mes_Ano_Referencia = ' . aspas($_POST['MES_ANO_VENCIMENTO']) . ') and 
                             (Flag_Travar_Edicao = ' . aspas('S') . ') ');

    if ($rowTemp->MES_ANO_REFERENCIA!='')
    {
    	$detalheConclusao = 'A competência : ' . $rowTemp->MES_ANO_REFERENCIA . ' está concluida/fechada. Portanto, não pode ter suas faturas calculadas/re-calculadas.';
    }

    if ($_POST['CHECK_APAGAR_PROCESSAMENTOS_ANTERIORES']=='S') 
    {
	    $qryExclusao = 'Delete from ps1023 where ps1023.numero_registro in (select a.numero_registro from ps1023 a 
	    		        inner join ps1000 on (a.codigo_associado = ps1000.codigo_associado) 
	                    Where ';

		if (($_POST['CODIGO_EMPRESA_INICIAL']!='') and ($_POST['CODIGO_EMPRESA_FINAL']!=''))
		   $qryExclusao .= ' (Ps1000.Codigo_Empresa between ' . numSql($_POST['CODIGO_EMPRESA_INICIAL']) . ' and ' . numSql($_POST['CODIGO_EMPRESA_FINAL']) . ') And ';

		if (($_POST['CODIGO_PLANO_INICIAL']!='') and ($_POST['CODIGO_PLANO_FINAL']!=''))
		   $qryExclusao .= ' (Ps1000.Codigo_Plano between ' . numSql($_POST['CODIGO_PLANO_INICIAL']) . ' and ' . numSql($_POST['CODIGO_PLANO_FINAL']) . ') And ';

		$qryExclusao .= ' (a.Mes_Ano_Vencimento = ' . aspas($_POST['MES_ANO_VENCIMENTO']) . ') And 
	    		          (a.codigo_evento = ' . numSql($_POST['CODIGO_EVENTO_GERADO']) . '))';

	    jn_query($qryExclusao);
	}           


    $qryGuias = 'Select PS5500.NUMERO_GUIA, PS5500.CODIGO_ASSOCIADO, PS5500.DATA_PROCEDIMENTO, PS5500.DATA_DIGITACAO, PS5500.VALOR_TOTAL_GERADO, 
                        PS5500.TIPO_GUIA,  
                        PS5510.VALOR_GERADO, PS5510.NUMERO_REGISTRO, COALESCE(PS5510.QUANTIDADE_PROCEDIMENTOS,1) QUANTIDADE_PROCEDIMENTOS, 
                        Ps1000Pes.Codigo_Empresa, Ps1000Pes.Codigo_Plano, Ps1000Pes.Nome_Associado, Ps1000Pes.Codigo_Titular, 
                        Ps5000.Tipo_Contrato, Ps1000Tit.NOME_ASSOCIADO as NOME_TITULAR, Ps1000Pes.Tipo_Associado, 
     				    PS5000.Nome_Prestador, 
       					COALESCE(PS1000Pes.CODIGO_GRUPO_COPARTICIPACAO, PS1010.CODIGO_GRUPO_COPARTICIPACAO, 
       					   		PS1030.CODIGO_GRUPO_COPARTICIPACAO) CODIGO_GRUPO_COPARTICIPACAO
      					From PS5500
						Inner Join Ps1000 Ps1000Pes On (PS5500.Codigo_Associado = Ps1000Pes.Codigo_Associado) 
						Inner Join Ps1000 Ps1000Tit On (PS1000Pes.Codigo_Titular = Ps1000Tit.Codigo_Associado) 
						Inner Join Ps1010 On (Ps1000Pes.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA) 
						Inner Join Ps1030 ON (Ps1000Pes.CODIGO_PLANO = PS1030.CODIGO_PLANO)
						Left Outer Join Ps5000 On (PS5500.Codigo_Prestador = Ps5000.Codigo_Prestador) 
						Left Outer Join PS5510 On (PS5500.Numero_Guia = PS5510.numero_guia) 
						Where ';

	if (($_POST['CODIGO_EMPRESA_INICIAL']!='') and ($_POST['CODIGO_EMPRESA_FINAL']!=''))
       $qryGuias .= ' (Ps1000Pes.Codigo_Empresa between ' . numSql($_POST['CODIGO_EMPRESA_INICIAL']) . ' and ' . numSql($_POST['CODIGO_EMPRESA_FINAL']) . ') And ';

	if (($_POST['CODIGO_PLANO_INICIAL']!='') and ($_POST['CODIGO_PLANO_FINAL']!=''))
       $qryGuias .= ' (Ps1000Pes.Codigo_Plano between ' . numSql($_POST['CODIGO_PLANO_INICIAL']) . ' and ' . numSql($_POST['CODIGO_PLANO_FINAL']) . ') And ';

    if ($_POST['CHECK_GERAR_GLOSADAS']!='S') 
       $qryGuias .= ' (PS5500.Codigo_Glosa Is Null) And ';

    if ((testaData($_POST['DATA_PROCEDIMENTO_INICIAL'])) and (testaData($_POST['DATA_PROCEDIMENTO_FINAL'])))
	   $qryGuias .= ' (PS5500.Data_Procedimento between ' .  DataToSql(sqlToData($_POST['DATA_PROCEDIMENTO_INICIAL'])) . ' and ' . DataToSql(sqlToData($_POST['DATA_PROCEDIMENTO_FINAL'])) . ') ';

    if ((testaData($_POST['DATA_DIGITACAO_INICIAL'])) and (testaData($_POST['DATA_DIGITACAO_FINAL'])))
	   $qryGuias .= ' (PS5500.Data_Digitacao between ' .  DataToSql(sqlToData($_POST['DATA_DIGITACAO_INICIAL'])) . ' and ' . DataToSql(sqlToData($_POST['DATA_DIGITACAO_FINAL'])) . ') ';

    if ($_POST['LISTA_NUMEROS_PROCESSAMENTO']!='') 
       $qryGuias .= ' (PS5500.Numero_Processamento In (' . $_POST['LISTA_NUMEROS_PROCESSAMENTO'] . ')) ';

	$tiposGuias = ' and PS5500.Tipo_Guia in (' . aspas('X');

	if ($_POST['CHECK_GERAR_CONSULTAS']=='S')
		$tiposGuias .= ',' . aspas('C');

	if ($_POST['CHECK_GERAR_EXAMES']=='S')
		$tiposGuias .= ',' . aspas('S');

	if ($_POST['CHECK_GERAR_AMBULATORIOS']=='S')
		$tiposGuias .= ',' . aspas('A');

	if ($_POST['CHECK_GERAR_INTERNACOES']=='S')
		$tiposGuias .= ',' . aspas('I');

	if ($_POST['CHECK_GERAR_ODONTOLOGIA']=='S')
		$tiposGuias .= ',' . aspas('O');

	$tiposGuias .= ')';

    $qryGuias    .= $tiposGuias;

    $qryGuias    .= 'Order by Ps1000Tit.NOME_ASSOCIADO, Ps1000Pes.Tipo_Associado desc, PS5500.NOME_PESSOA, PS5500.numero_guia, PS5510.numero_registro ';

	$resGuias    = jn_query($qryGuias);

    while ($rowGuias = jn_fetch_object($resGuias))
    {

        $rowTemp = qryUmRegistro('Select A.Flag_Nao_Gerar_Coparticip CopartItem , B.Flag_Nao_Gerar_Coparticip CopartCab , Coalesce(A.Codigo_Procedimento, ' . aspas($rowGuias->CODIGO_PROCEDIMENTO) . ') Codigo_Procedimento 
                                  From Ps5510 A 
                                  Inner Join Ps5500 B on (B.Numero_Guia = A.Numero_Guia) 
                                  Where (A.Numero_Registro = ' . numSql($rowGuias->NUMERO_REGISTRO) . ')');

        if (($rowTemp->COPARTITEM=='S') Or
            ($rowTemp->COPARTCAB=='S'))
        {
           Continue;
        }

        $valorCalculado = calculaValorCoparticipacaoFranquia($rowTemp->CODIGO_PROCEDIMENTO, $rowGuias->CODIGO_ASSOCIADO,$rowGuias->CODIGO_GRUPO_COPARTICIPACAO );

        if ($valorCalculado==0)
        {
           Continue;	
        }

        $historico = 'GUIA:' . $rowGuias->NUMERO_GUIA . '-TIPO:' . $rowGuias->TIPO_GUIA;

	    $sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('Codigo_Associado',$rowGuias->CODIGO_ASSOCIADO);
		$sqlEdicao 	.= linhaJsonEdicao('Nome_Pessoa',$rowGuias->NOME_ASSOCIADO);
		$sqlEdicao 	.= linhaJsonEdicao('Descricao_Observacao',$historico);
		$sqlEdicao 	.= linhaJsonEdicao('Data_Evento',$rowGuias->DATA_PROCEDIMENTO,'D');
		$sqlEdicao 	.= linhaJsonEdicao('Codigo_Evento',$_POST['CODIGO_EVENTO_GERADO']);
		$sqlEdicao 	.= linhaJsonEdicao('Mes_Ano_Vencimento',$_POST['MES_ANO_VENCIMENTO']);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Guia',$rowGuias->NUMERO_GUIA);
		$sqlEdicao 	.= linhaJsonEdicao('Tipo_Guia',$rowGuias->TIPO_GUIA);
		$sqlEdicao 	.= linhaJsonEdicao('Numero_Registro_Item_Guia',$rowGuias->NUMERO_REGISTRO);

        if ($rowGuias->QUANTIDADE_PROCEDIMENTOS=='')
            $qtdeProcedimento     = 1;
        else
            $qtdeProcedimento     = $rowGuias->QUANTIDADE_PROCEDIMENTOS;

        if ($_POST['CHECK_QTDE_PROCEDIMENTO']!='S')
        {
            $qtdeProcedimento     = 1;
        }

		$sqlEdicao 	.= linhaJsonEdicao('Quantidade_Eventos',$qtdeProcedimento);
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Evento',$valorCalculado);

		$criterioWhere = ' (Codigo_Associado = ' . aspas($rowGuias->CODIGO_ASSOCIADO) . ') And 
		                   (Tipo_Guia = ' . aspas($rowGuias->TIPO_GUIA) . ') And 
		                   (Descricao_Observacao = ' . Aspas($historico) . ')';

		if ($rowGuias->NUMERO_REGISTRO!='')
           $criterioWhere .= ' And (Numero_Registro_Item_Guia = ' . numSql($rowGuias->NUMERO_REGISTRO) . ') ';

        if ($_POST['CHECK_SOBREPOR_PROGRAMACAO']=='S')
        	$tipoGravacao = 'V';
        else
        	$tipoGravacao = 'NA';

		gravaEdicao('PS1023', $sqlEdicao, $tipoGravacao, $criterioWhere );

	    $sqlEdicao   = '';
		$sqlEdicao 	.= linhaJsonEdicao('Valor_Coparticipacao',$valorCalculado * $qtdeProcedimento,'N');
		gravaEdicao('PS5510', $sqlEdicao, 'A',' Numero_Registro = ' . aspas($rowGuias->NUMERO_REGISTRO));

	}

	//

	jn_query('Update Ps5500 set valor_coparticipacao = (Select sum(PS5510.valor_coparticipacao) 
                                                        from Ps5510 where (Ps5510.numero_guia = Ps5500.numero_guia) and 
                                                                          (Ps5510.ID_INSTANCIA_PROCESSO = ' . aspas($_POST['ID_PROCESSO']) . ')) 
              where  Ps5500.ID_INSTANCIA_PROCESSO = ' . aspas($_POST['ID_PROCESSO'])); 

	//

	$queryRelatorioProcessamento  = 'Select Ps1023.Codigo_Associado, Ps1023.Nome_Pessoa, Ps1023.Numero_Guia, Ps1023.Tipo_Guia, 
	                                       Ps1023.Mes_Ano_Vencimento, Ps1023.Quantidade_Eventos, Ps1023.Valor_Evento 
	                                       From Ps1023 
	                                       Where ID_INSTANCIA_PROCESSO = ' . aspas($_POST['ID_PROCESSO']);

	$nomearquivoRelatorioProcesso = geraRelatorioAutomaticoProcessamento($_POST['ID_PROCESSO'],$queryRelatorioProcessamento);	                                       

	registraConclusaoProcesso($_POST['ID_PROCESSO'],'Processo concluído!',$detalheConclusao,'', $nomearquivoLogProcesso, $nomearquivoRelatorioProcesso);

}



function calculaValorCoparticipacaoFranquia($codigoProcedimento, $codigoAssociado, $codigoGrupoCoparticipacao = '', $codigoPrestador = '')
{

	if ($codigoGrupoCoparticipacao=='') // Caso a rotina não tenha a informação para passar, aí eu busco. Caso contrário uso o que foi passado
	{
	    $rowTemp = qryUmRegistro('Select COALESCE(PS1000.CODIGO_GRUPO_COPARTICIPACAO, PS1010.CODIGO_GRUPO_COPARTICIPACAO, 
	       					  	 		  PS1030.CODIGO_GRUPO_COPARTICIPACAO) CODIGO_GRUPO_COPARTICIPACAO
			      					From PS1000
									Inner Join Ps1010 On (Ps1000.CODIGO_EMPRESA = PS1010.CODIGO_EMPRESA) 
									Inner Join Ps1030 On (Ps1000.CODIGO_PLANO = PS1010.CODIGO_EMPRESA) 
									Where Ps1000.Codigo_Associado = ' . aspas($codigoAssociado));

	    $codigoGrupoCoparticipacao = $rowTemp->CODIGO_GRUPO_COPARTICIPACAO;
	}

    $rowCoparticipacoes = qryUmRegistro('Select COALESCE(PS1310.VALOR_LIMITE_COPARTICIPACAO,0) PS1310_LIMITE, PS1311.TIPO_CALCULO_COPARTICIPACAO, PS1311.VALOR_COPARTICIPACAO, 
                                                COALESCE(PS1311.VALOR_LIMITE_COPARTICIPACAO,0) PS1311_LIMITE, PS1311.QUANTIDADE_UTILIZ_INICIAL, PS1311.QUANTIDADE_UTILIZ_FINAL, 
                                                COALESCE(PS1312.PERCENTUAL_DESCONTO,0) PS1312_PERCENTUAL_DESCONTO
                                         From Ps1310
                                         Inner Join PS1311 on (PS1310.CODIGO_GRUPO_COPARTICIPACAO = PS1311.CODIGO_GRUPO_COPARTICIPACAO) and 
                                                              (PS1311.CODIGO_GRUPO_COPARTICIPACAO = ' . aspas($codigoGrupoCoparticipacao) . ') and 
                                                              (PS1311.CODIGO_PROCEDIMENTO_INICIAL <= ' . aspas($codigoProcedimento) . ') and 
                                                              (PS1311.CODIGO_PROCEDIMENTO_FINAL >= ' . aspas($codigoProcedimento) . ') 
                                         Left Outer Join PS1312 on (PS1312.CODIGO_ASSOCIADO = ' . aspas($codigoAssociado) . ') and 
                                                              	   (PS1312.CODIGO_PROCEDIMENTO_INICIAL <= ' . aspas($codigoProcedimento) . ') and 
                                                               	   (PS1312.CODIGO_PROCEDIMENTO_FINAL >= ' . aspas($codigoProcedimento) . ') ');


    if ($rowCoparticipacoes->TIPO_CALCULO_COPARTICIPACAO=='V')
        $valorCalculado = $rowCoparticipacoes->VALOR_COPARTICIPACAO;
    else 
    {
        if ($rowGuias->TIPO_GUIA=='C')
            $valorCalculado = ($rowCoparticipacoes->VALOR_COPARTICIPACAO / 100) * $rowCoparticipacoes->VALOR_TOTAL_GERADO;
        else
            $valorCalculado = ($rowCoparticipacoes->VALOR_COPARTICIPACAO / 100) * $rowCoparticipacoes->VALOR_GERADO;
    }

    if ($rowCoparticipacoes->PS1312_PERCENTUAL_DESCONTO!=0)
    {
    	$valorCalculado = ($valorCalculado - ($valorCalculado * ($rowCoparticipacoes->PS1312_PERCENTUAL_DESCONTO / 100)));    
    }

    if (($rowCoparticipacoes->PS1311_LIMITE!=0) and 
        ($valorCalculado > $rowCoparticipacoes->PS1311_LIMITE))
        $valorCalculado = $rowCoparticipacoes->PS1311_LIMITE;
    else if (($rowCoparticipacoes->PS1310_LIMITE!=0) and 
             ($valorCalculado > $rowCoparticipacoes->PS1310_LIMITE))
       	$valorCalculado = $rowCoparticipacoes->PS1310_LIMITE;

    pr($valorCalculado);

    return $valorCalculado;

}





?>

