<?php
require('../lib/base.php');

require('../private/autentica.php');


if($dadosInput['tipo'] =='filtrar'){
	if($_SESSION['type_db']=='firebird'){
		$query  ='Select PS6510.NUMERO_REGISTRO,PS6510.CODIGO_PROCEDIMENTO, SUBSTRING(PS5210.NOME_PROCEDIMENTO FROM 1 FOR 30) NOME_PROCEDIMENTO, PS6500.CODIGO_CID, ' .
				  'Substring(Ps5201.Nome_Patologia from  1 for 30) DESCRICAO_CID, PS6500.CODIGO_ASSOCIADO, PS6500.NOME_PESSOA, PS6500.DATA_AUTORIZACAO, ' .
				  'PS6500.AUTORIZADO_POR OPERADOR, PS1100.NOME_USUAL, PS6500.CODIGO_PRESTADOR, A.NOME_PRESTADOR NOME_PRESTADOR, PS6500.CODIGO_SOLICITANTE, B.NOME_PRESTADOR NOME_SOLICITANTE, ' .
				  'PS6500.CODIGO_PRESTADOR_EXECUTANTE, PS6500.NOME_PRESTADOR_EXECUTANTE, PS1030.NOME_PLANO_EMPRESAS, PS1010.NOME_EMPRESA, ' .
				  'PS6500.NUMERO_AUTORIZACAO, PS6510.NUMERO_REGISTRO, PS6510.SITUACAO, PS1000.SEXO, PS1000.DATA_NASCIMENTO, PS6500.TIPO_GUIA,DATEDIFF(DAY,PS6500.DATA_AUTORIZACAO,CURRENT_TIMESTAMP) AS DIF, DATEADD(DAY , 5 , PS6500.DATA_AUTORIZACAO ) AS DATA_LIMITE ' .
				'From Ps6510 ' .
				  'Inner Join Ps6500 On (Ps6510.Numero_Autorizacao = Ps6500.Numero_Autorizacao) ' .
				  'Inner Join Ps1000 On (Ps6500.Codigo_Associado = Ps1000.Codigo_Associado) ' .
				  'Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ' .
				  'Inner Join Ps1010 On (Ps1000.codigo_Empresa = Ps1010.Codigo_Empresa) ' .
				  'Left Outer Join Ps5000 a On (Ps6500.Codigo_Prestador = a.Codigo_Prestador) ' .
				  'Left Outer Join Ps5000 b On (Ps6500.Codigo_Solicitante = b.Codigo_Prestador) ' .
				  'Left Outer Join Ps5201 On (Ps6500.Codigo_Cid = Ps5201.Codigo_Cid) ' .
				  'Left Outer Join Ps5210 On (Ps6510.Codigo_Procedimento = Ps5210.Codigo_Procedimento) ' .
				  'Left Outer Join Ps1100 On (Ps6500.Autorizado_Por = Ps1100.Codigo_Identificacao) ' .
				'Where (Ps6510.Numero_Registro > 0) ';
		$query  .= ' And DATEADD(MONTH, -12, current_timestamp) <= Ps6500.Data_Autorizacao ';
	}else{
		$query  ='SELECT PS6510.NUMERO_REGISTRO,PS6510.CODIGO_PROCEDIMENTO, SUBSTRING(PS5210.NOME_PROCEDIMENTO , 1 , 30) NOME_PROCEDIMENTO, PS6500.CODIGO_CID, ' .
				  'SUBSTRING(PS5201.NOME_PATOLOGIA , 1 , 30) DESCRICAO_CID, PS6500.CODIGO_ASSOCIADO, PS6500.NOME_PESSOA, PS6500.DATA_AUTORIZACAO, ' .
				  'PS6500.AUTORIZADO_POR OPERADOR, PS1100.NOME_USUAL, PS6500.CODIGO_PRESTADOR, A.NOME_PRESTADOR NOME_PRESTADOR, PS6500.CODIGO_SOLICITANTE, B.NOME_PRESTADOR NOME_SOLICITANTE, ' .
				  'PS6500.CODIGO_PRESTADOR_EXECUTANTE, PS6500.NOME_PRESTADOR_EXECUTANTE, PS1030.NOME_PLANO_EMPRESAS, PS1010.NOME_EMPRESA, ' .
				  'PS6500.NUMERO_AUTORIZACAO, PS6510.NUMERO_REGISTRO, PS6510.SITUACAO, PS1000.SEXO, PS1000.DATA_NASCIMENTO, PS6500.TIPO_GUIA,DATEDIFF(DAY,PS6500.DATA_AUTORIZACAO,GETDATE()) AS DIF, DATEADD(DAY , 3 , PS6500.DATA_AUTORIZACAO ) AS DATA_LIMITE ' .
				'From Ps6510 ' .
				  'Inner Join Ps6500 On (Ps6510.Numero_Autorizacao = Ps6500.Numero_Autorizacao) ' .
				  'Inner Join Ps1000 On (Ps6500.Codigo_Associado = Ps1000.Codigo_Associado) ' .
				  'Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ' .
				  'Inner Join Ps1010 On (Ps1000.codigo_Empresa = Ps1010.Codigo_Empresa) ' .
				  'Left Outer Join Ps5000 a On (Ps6500.Codigo_Prestador = a.Codigo_Prestador) ' .
				  'Left Outer Join Ps5000 b On (Ps6500.Codigo_Solicitante = b.Codigo_Prestador) ' .
				  'Left Outer Join Ps5201 On (Ps6500.Codigo_Cid = Ps5201.Codigo_Cid) ' .
				  'Left Outer Join Ps5210 On (Ps6510.Codigo_Procedimento = Ps5210.Codigo_Procedimento) ' .
				  'Left Outer Join Ps1100 On (Ps6500.Autorizado_Por = Ps1100.Codigo_Identificacao) ' .
				'Where (Ps6510.Numero_Registro > 0) ';
		$query  .= ' And DATEADD(M, -12, GETDATE()) <= Ps6500.Data_Autorizacao ';
	}

	if ($dadosInput["prestador"] !== '')
		$query  .= ' And (Ps6500.Codigo_Prestador = '  . aspas($dadosInput["prestador"]) . ') ';

	if ($dadosInput["beneficiario"]!== '')
		$query  .= ' And (Ps6500.Codigo_Associado = '  . aspas($dadosInput["beneficiario"]) . ') ';
	
	if ($dadosInput["operador"]!== '')
		$query  .= ' And (Ps6500.autorizado_por = '  . aspas($dadosInput["operador"]) . ') ';

	if ($dadosInput["numeroAutorizacao"] > 0)
		$query  .= ' And (Ps6500.Numero_Autorizacao = '. aspas($dadosInput["numeroAutorizacao"] ) . ') ';

	if ($dadosInput["dataInicial"] != "" and $dadosInput["dataInicial"] != " "){
		
		$dadosInput["dataInicial"] = explode('T',$dadosInput["dataInicial"]);
		$dadosInput["dataInicial"] = $dadosInput["dataInicial"][0] . 'T00:00:00.000Z';
		
		$query  .= ' And (Ps6500.Data_Autorizacao >= ' . DataToSql($dadosInput["dataInicial"]) . ') ';
	}
		
	if ($dadosInput["dataFinal"] != "" and $dadosInput["dataFinal"] != " "){		
		$query  .= ' And (Ps6500.Data_Autorizacao <= ' . DataToSql($dadosInput["dataFinal"]) . ') ';
	}
	
	
	$tipoSituacao = "";
	
	if($dadosInput["listarPendentes"]=="todos"){
		$tipoSituacao .= "'F','P','D'";
		$query  .= " And (Ps6510.Situacao in (". $tipoSituacao .")) ";
		
	}if($dadosInput["negados"]=="negados"){
		$tipoSituacao .= "'N'";
		$query  .= " And (Ps6510.Situacao in (". $tipoSituacao .")) ";
	}
		

	if ($dadosInput['tipoAuditoria'] != ''){
		$query  .= " And (Ps6510.Situacao =". aspas($dadosInput['tipoAuditoria']) . ") ";
	}
	
	$query  .= " ORDER BY PS6500.DATA_AUTORIZACAO ";
				
	$res = jn_query($query);
	
	$i = 0;
	while($row = jn_fetch_object($res)) {	
		$linha = null;
		if (($row->DIF > 5) and ($row->SITUACAO!="N") and ($row->SITUACAO!="A")){
	   	   $cor  = '<font color="#ff0000">';
		   $corf = '</font>'; 
		}else{
			$cor  = '';
			$corf = ''; 	   
		}
	
       
		$situacao = "";
	   
		if($row->SITUACAO=="P"){
			$situacao = "P - Pendente de auditoria medica";
		}elseif($row->SITUACAO=="F"){
			$situacao = "F - Pendente de auditoria de enfermagem";
		}elseif($row->SITUACAO=="N"){
			$situacao = "N - Negada";
		}elseif($row->SITUACAO=="D"){
			$situacao = "D - Pendente de auditoria Administrativa";
		}elseif($row->SITUACAO=="A"){
			$situacao = "A - Autorizada";
		}elseif($row->SITUACAO=="L"){
			$situacao = "L - Relatórios";
		}elseif($row->SITUACAO=="R"){
			$situacao = "R - Redirecionamento";
		}elseif($row->SITUACAO=="E"){
			$situacao = "E - Encerrado";
		}elseif($row->SITUACAO=="C"){
			$situacao = "C - Credenciamento";
		}
		
		$linha['SITUACAO'] 			 = $cor.jn_utf8_encode($situacao).$corf;
		$linha['NUMERO_AUTORIZACAO'] = $row->NUMERO_AUTORIZACAO;
		$linha['PROCEDIMENTO'] = $row->CODIGO_PROCEDIMENTO;
		$linha['NOME_PROCEDIMENTO'] = jn_utf8_encode($row->NOME_PROCEDIMENTO);
		$linha['ASSOCIADO'] = $row->CODIGO_ASSOCIADO;
		$linha['NOME_ASSOCIADO'] = jn_utf8_encode($row->NOME_PESSOA);
		$linha['DATA_AUTORIZACAO'] = sqlToData($row->DATA_AUTORIZACAO);
		$linha['DATA_LIMITE'] = sqlToData($row->DATA_LIMITE);
		$linha['OPERADOR'] = jn_utf8_encode($row->AUTORIZADO_POR);
		$linha['NOME_OPERADOR'] = jn_utf8_encode($row->NOME_USUAL);
		$linha['AUDITAR'] = '';
		$linha['REGISTRO'] = $row->NUMERO_REGISTRO;
		$retorno['GRID'][] = $linha;
		
		$i++;
	}
	
	if($i == 0){		
		$retorno['GRID'][] = '';
	}
	
	echo json_encode($retorno);

}else if($dadosInput['tipo'] =='resumo'){
	
    $query  = 'SELECT * From sp_param_resumo_beneficiario(' . aspas($dadosInput['beneficiario']) . ') ';
        
	$res = jn_query($query);
 
	if ($resumo = jn_fetch_object($res));
	
	$plano = getDadosPlano($resumo->CODIGO_PLANO, null);
	$retorno['CODIGO_PLANO'] = $resumo->CODIGO_PLANO;
	$query  ='SELECT PS6510.CODIGO_PROCEDIMENTO,PS5210.NOME_PROCEDIMENTO, PS6500.CODIGO_CID, ' .
              'PS5201.NOME_PATOLOGIA DESCRICAO_CID, PS6500.CODIGO_ASSOCIADO, PS6500.NOME_PESSOA, PS6500.DATA_AUTORIZACAO, ' .
	   		  'PS6500.AUTORIZADO_POR OPERADOR, PS6500.CODIGO_PRESTADOR, A.NOME_PRESTADOR NOME_PRESTADOR, PS6500.CODIGO_SOLICITANTE, B.NOME_PRESTADOR NOME_SOLICITANTE, ' .
			  'PS6500.CODIGO_PRESTADOR_EXECUTANTE, PS6500.NOME_PRESTADOR_EXECUTANTE, PS1030.NOME_PLANO_EMPRESAS, PS1010.NOME_EMPRESA, ' .
			  'PS6500.NUMERO_AUTORIZACAO, PS6510.NUMERO_REGISTRO, PS6510.SITUACAO, PS1000.SEXO, PS1000.DATA_NASCIMENTO, PS6500.TIPO_GUIA ' .
			'From Ps6510 ' .
			  'Inner Join Ps6500 On (Ps6510.Numero_Autorizacao = Ps6500.Numero_Autorizacao) ' .
			  'Inner Join Ps1000 On (Ps6500.Codigo_Associado = Ps1000.Codigo_Associado) ' .
			  'Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ' .
			  'Inner Join Ps1010 On (Ps1000.codigo_Empresa = Ps1010.Codigo_Empresa) ' .
			  'Left Outer Join Ps5000 a On (Ps6500.Codigo_Prestador = a.Codigo_Prestador) ' .
			  'Left Outer Join Ps5000 b On (Ps6500.Codigo_Solicitante = b.Codigo_Prestador) ' .
			  'Left Outer Join Ps5201 On (Ps6500.Codigo_Cid = Ps5201.Codigo_Cid) ' .
			  'Left Outer Join Ps5210 On (Ps6510.Codigo_Procedimento = Ps5210.Codigo_Procedimento) ' .
			'Where (Ps6510.Numero_Registro > 0) ';

	$query  .= ' And (Ps6510.Numero_Registro = '. aspas($dadosInput['registro']) . ') ';
	$query  .= ' And (Ps6500.Codigo_Associado = '. aspas($dadosInput['beneficiario']) . ') ';
		
	$res = jn_query($query);


	$autorizacao = jn_fetch_object($res);	
	$retorno['AUTORIZACAO'] = '';
	if($autorizacao){
		
		$retorno['AUTORIZACAO']  = '<ul>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Número da autorização</strong></div>
										'.$autorizacao->NUMERO_AUTORIZACAO.'&nbsp;
									</li>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Código procedimento</strong></div>
										'.$autorizacao->CODIGO_PROCEDIMENTO.'&nbsp;
									</li>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Nome procedimento</strong></div>
										'.utf8_encode($autorizacao->NOME_PROCEDIMENTO).'&nbsp;
									</li>';									
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Empresa</strong></div>
										'.utf8_encode($autorizacao->NOME_EMPRESA).'&nbsp;
									</li>';		
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Plano</strong></div>
										'.$autorizacao->NOME_PLANO_EMPRESAS.'&nbsp;
									</li>';					
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Prestador</strong></div>
										'.$autorizacao->CODIGO_PRESTADOR ." ". utf8_encode($autorizacao->NOME_PRESTADOR).'&nbsp;
									</li>';					
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Solicitante</strong></div>
										'.$autorizacao->CODIGO_SOLICITANTE ." ". utf8_encode($autorizacao->NOME_SOLICITANTE).'&nbsp;
									</li>';					
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Data Autorização</strong></div>
										'.SqlToData($autorizacao->DATA_AUTORIZACAO).'&nbsp;
									</li>';					
		
		$retorno['AUTORIZACAO'] .= '</ul>';
	}
	
	$retorno['RESUMO'] = '';
	
	if($resumo){
		
		
		
		$retorno['RESUMO']  = '<ul>';
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Código</strong></div>
										'.$resumo->CODIGO_ASSOCIADO.'&nbsp;
									</li>';
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Nome</strong></div>
										'.utf8_encode($resumo->NOME_ASSOCIADO).'&nbsp;
									</li>';									
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Nome Empresa</strong></div>
										'.$resumo->NOME_EMPRESA.'&nbsp;
									</li>';	
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Data Nascimento</strong></div>
										'.SqlToData($resumo->DATA_NASCIMENTO).'&nbsp;
									</li>';										
		
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Data Admissão</strong></div>
										'.SqlToData($resumo->DATA_ADMISSAO).'&nbsp;
									</li>';										
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Data Exclusão</strong></div>
										'.SqlToData($resumo->DATA_EXCLUSAO).'&nbsp;
									</li>';										
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Nome motivo exclusao</strong></div>
										'.utf8_encode($resumo->NOME_MOTIVO_EXCLUSAO).'&nbsp;
									</li>';	
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Admissao empresa</strong></div>
										'.SqlToData($resumo->ADMISSAO_EMPRESA).'&nbsp;
									</li>';	
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Nome grupo pessoas</strong></div>
										'.utf8_encode($resumo->NOME_GRUPO_PESSOAS).'&nbsp;
									</li>';
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Descrição situação</strong></div>
										'.utf8_encode($resumo->DESCRICAO_SITUACAO).'&nbsp;
									</li>';
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Codigo tipo caracteristica</strong></div>
										'.($resumo->CODIGO_TIPO_CARACTERISTICA).'&nbsp;
									</li>';		
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Nome Tipo caracteristica</strong></div>
										'.utf8_encode($resumo->NOME_TIPO_CARACTERISTICA).'&nbsp;
									</li>';	
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Quantidade total faturas</strong></div>
										'.($resumo->QUANTIDADE_TOTAL_FATURAS).'&nbsp;
									</li>';			
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Quantidade faturas em aberto</strong></div>
										'.($resumo->QUANTIDADE_FATURAS_EM_ABERTO).'&nbsp;
									</li>';			
		
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Quantidade faturas pagas</strong></div>
										'.($resumo->QUANTIDADE_FATURAS_PAGAS).'&nbsp;
									</li>';			
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Valor total em aberto</strong></div>
										'.($resumo->VALOR_TOTAL_EM_ABERTO).'&nbsp;
									</li>';			
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Valor total pago</strong></div>
										'.($resumo->VALOR_TOTAL_PAGO).'&nbsp;
									</li>';			
		$retorno['RESUMO']  .='<li>
										<div class="contact-list"><strong>Quantidade dias em aberto</strong></div>
										'.($resumo->QUANTIDADE_DIAS_EM_ABERTO).'&nbsp;
									</li>';			
		
		
		$retorno['RESUMO'] .= '</ul>';
		
			
	}
	
	$retorno['PLANO'] = '';
	
	if($plano){
		//pr($plano);
		$retorno['PLANO']  = '<ul>';
		$retorno['PLANO']  .='<li>
										<div class="contact-list"><strong>Codigo plano</strong></div>
										'.($plano[0]->CODIGO_PLANO).'&nbsp;
									</li>';			
		$retorno['PLANO']  .='<li>
										<div class="contact-list"><strong>Nome do plano</strong></div>
										'.utf8_encode($plano[0]->NOME_PLANO_FAMILIARES).'&nbsp;
									</li>';		
		$retorno['PLANO'] .= '</ul>';
	}
	
	echo json_encode($retorno);
	
}else if($dadosInput['tipo'] =='parecer'){

   $query  ='SELECT PS6510.CODIGO_PROCEDIMENTO, PS5210.NOME_PROCEDIMENTO , PS6500.CODIGO_CID, ' .
              ' PS5201.NOME_PATOLOGIA DESCRICAO_CID, PS6500.CODIGO_ASSOCIADO, PS6500.NOME_PESSOA, PS6500.DATA_AUTORIZACAO, ' .
	   		  'PS6500.AUTORIZADO_POR OPERADOR, PS6500.CODIGO_PRESTADOR, A.NOME_PRESTADOR NOME_PRESTADOR, PS6500.CODIGO_SOLICITANTE, B.NOME_PRESTADOR NOME_SOLICITANTE, ' .
			  'PS6500.CODIGO_PRESTADOR_EXECUTANTE, PS6500.NOME_PRESTADOR_EXECUTANTE, PS1030.NOME_PLANO_EMPRESAS, PS1010.NOME_EMPRESA, ' .
			  'PS6500.NUMERO_AUTORIZACAO, PS6510.NUMERO_REGISTRO, PS6510.SITUACAO, PS1000.SEXO, PS1000.DATA_NASCIMENTO, PS6500.TIPO_GUIA ' .
			'From Ps6510 ' .
			  'Inner Join Ps6500 On (Ps6510.Numero_Autorizacao = Ps6500.Numero_Autorizacao) ' .
			  'Inner Join Ps1000 On (Ps6500.Codigo_Associado = Ps1000.Codigo_Associado) ' .
			  'Inner Join Ps1030 On (Ps1000.Codigo_Plano = Ps1030.Codigo_Plano) ' .
			  'Inner Join Ps1010 On (Ps1000.codigo_Empresa = Ps1010.Codigo_Empresa) ' .
			  'Left Outer Join Ps5000 a On (Ps6500.Codigo_Prestador = a.Codigo_Prestador) ' .
			  'Left Outer Join Ps5000 b On (Ps6500.Codigo_Solicitante = b.Codigo_Prestador) ' .
			  'Left Outer Join Ps5201 On (Ps6500.Codigo_Cid = Ps5201.Codigo_Cid) ' .
			  'Left Outer Join Ps5210 On (Ps6510.Codigo_Procedimento = Ps5210.Codigo_Procedimento) ' .
			'Where (Ps6510.Numero_Registro > 0) ';

	$query  .= ' And (Ps6510.Numero_Registro = '. aspas($dadosInput['registro']) . ') ';
	

	$res = jn_query($query);



	if($row = jn_fetch_object($res)) {

		$retorno['AUTORIZACAO']  = '<ul>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Número da autorização</strong></div>
										'.$row->NUMERO_AUTORIZACAO.'&nbsp;
									</li>';
									
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Código Beneficiário</strong></div>
										'.$row->CODIGO_ASSOCIADO.'&nbsp;
									</li>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Nome Beneficiário</strong></div>
										'.$row->NOME_PESSOA.'&nbsp;
									</li>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Data Nascimento</strong></div>
										'.SqlToData($row->DATA_NASCIMENTO).'&nbsp;
									</li>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Nome Empresa</strong></div>
										'.$row->NOME_EMPRESA.'&nbsp;
									</li>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Plano</strong></div>
										'.$row->NOME_PLANO_EMPRESAS.'&nbsp;
									</li>';									
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Código procedimento</strong></div>
										'.$row->CODIGO_PROCEDIMENTO.'&nbsp;
									</li>';
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Nome procedimento</strong></div>
										'.$row->NOME_PROCEDIMENTO.'&nbsp;
									</li>';									
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Empresa</strong></div>
										'.$row->NOME_EMPRESA.'&nbsp;
									</li>';		
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Plano</strong></div>
										'.$row->NOME_PLANO_EMPRESAS.'&nbsp;
									</li>';					
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Prestador</strong></div>
										'.$row->CODIGO_PRESTADOR ." ". $row->NOME_PRESTADOR.'&nbsp;
									</li>';					
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Solicitante</strong></div>
										'.$row->CODIGO_SOLICITANTE ." ". $row->NOME_SOLICITANTE.'&nbsp;
									</li>';					
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Data Autorização</strong></div>
										'.SqlToData($row->DATA_AUTORIZACAO).'&nbsp;
									</li>';		
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>CID</strong></div>
										'.$row->CODIGO_CID.'&nbsp;
									</li>';	
		$retorno['AUTORIZACAO']  .='<li>
										<div class="contact-list"><strong>Tipo Autorização</strong></div>
										'.$row->TIPO_GUIA.'&nbsp;
									</li>';	
		
		$retorno['AUTORIZACAO'] .= '</ul>';
		
		$queryImagem = "SELECT CAMINHO_ARQUIVO_ARMAZENADO||NOME_ARQUIVO_ARMAZENADO IMAGEM, NUMERO_REGISTRO,NOME_ARQUIVO_ORIGINAL FROM CONTROLE_ARQUIVOS
								WHERE nome_tabela = ".aspas('PS6500')." AND chave_registro = ".aspas($dadosInput['registro'])." AND data_exclusao IS NULL";
					$resultImagem = jn_query($queryImagem); 
					
		while($rowImagem  = jn_fetch_object($resultImagem)){
		
			$retorno['IMGAUTORIZACAO'][]= '../ServidorAl2/EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowImagem->NUMERO_REGISTRO;
		
		}
		
		$query2 = "SELECT NUMERO_REGISTRO,STATUS_PARECER,DATA_PARECER,HORA_PARECER, OBSERVACOES_PARECER,PS1100.NOME_COMPLETO,PS1100.CODIGO_IDENTIFICACAO FROM PS6530
				   left join ps1100 on ps1100.codigo_identificacao = ps6530.codigo_identificacao	
				   Where Numero_Registro_Ps6510 = " . aspas($row->NUMERO_REGISTRO);

		$res2 = jn_query($query2);
		
		
		while($row2 = jn_fetch_object($res2)) {
			$linha = array();
			
			$linha['STATUS_PARECER']= $row2->STATUS_PARECER;
			$linha['DATA_PARECER']= SqlToData($row2->DATA_PARECER);
			$linha['HORA_PARECER']= $row2->HORA_PARECER;
			$linha['OBSERVACOES_PARECER']= jn_utf8_encode($row2->OBSERVACOES_PARECER);
			$linha['OPERADOR']= $row2->CODIGO_IDENTIFICACAO . ' - ' . jn_utf8_encode($row2->NOME_COMPLETO);
			
			$queryImagem = "SELECT CAMINHO_ARQUIVO_ARMAZENADO||NOME_ARQUIVO_ARMAZENADO IMAGEM, NUMERO_REGISTRO,NOME_ARQUIVO_ORIGINAL FROM CONTROLE_ARQUIVOS
								WHERE nome_tabela = ".aspas('PS6530')." AND chave_registro = ".aspas($row2->NUMERO_REGISTRO)." AND data_exclusao IS NULL";
					$resultImagem = jn_query($queryImagem); 
			
			$i = 0;
			while($rowImagem  = jn_fetch_object($resultImagem)){
				
				$ext = explode('.',$rowImagem->NOME_ARQUIVO_ORIGINAL);
				$ext = strtoupper($ext[(count($ext)-1)]);
				if($ext == 'PDF'){
					$linha['IMG'][$i]['ICO'] = '../Site/assets/img/imagPDF.png';
				}else{
					$linha['IMG'][$i]['ICO'] = '../ServidorAl2/EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowImagem->NUMERO_REGISTRO;
				}
			
				$linha['IMG'][$i]['LINK'] = '../ServidorAl2/EstruturaPrincipal/arquivos.php?tipo=V&reg='.$rowImagem->NUMERO_REGISTRO;
				
				$i++;
			}
			
			$queryImagemAl1  = " SELECT  NOME_ARQUIVO FROM ESP0030 WHERE NUMERO_PARECER = " . aspas($row2->NUMERO_REGISTRO);
			$queryImagemAl1 .= " UNION ALL ";
			$queryImagemAl1 .= " SELECT  NOME_ARQUIVO FROM CFGARQUIVOS_PROCESSOS_NET WHERE NUMERO_PARECER = " . aspas($row2->NUMERO_REGISTRO);
			
			$resImagemAl1 = jn_query($queryImagemAl1);			
		
			$i = 0;
			while($rowAl1 = jn_fetch_object($resImagemAl1)) 
			{				
				$ext = explode('.',$rowAl1->NOME_ARQUIVO);
				$ext = strtoupper($ext[(count($ext)-1)]);
				if($ext == 'PDF'){
					$linha['IMG'][$i]['ICO']= '../Site/assets/img/imagPDF.png';
				}else{
					$linha['IMG'][$i]['ICO']= 'http://' . $rowAl1->NOME_ARQUIVO;
				}

				$linha['IMG'][$i]['LINK']= 'http://' . $rowAl1->NOME_ARQUIVO;

				$i++;
			}
			
			$retorno['PARECERES'][] = $linha;
		}

	}
	
	echo json_encode($retorno);

}else if($_POST['tipo'] =='enviar'){
	$registro = jn_gerasequencial('PS6530');

    $res = jn_query("SELECT PS6500.CODIGO_ASSOCIADO, PS6500.NUMERO_AUTORIZACAO, PS5100.NOME_ESPECIALIDADE FROM PS6500 
					 INNER JOIN PS6510 ON PS6510.NUMERO_AUTORIZACAO = PS6500.NUMERO_AUTORIZACAO 
					 LEFT JOIN PS5100 ON PS6500.CODIGO_ESPECIALIDADE = PS5100.CODIGO_ESPECIALIDADE 
					 WHERE PS6510.NUMERO_REGISTRO = " . aspas($_POST["registro"])); 		
    if($row = jn_fetch_object($res)){
		$NumAutorizacao = $row->NUMERO_AUTORIZACAO;
		$Associado      = $row->CODIGO_ASSOCIADO;
		$especialidade  = $row->NOME_ESPECIALIDADE;
	}	
	
	$query = "INSERT INTO PS6530 
				(NUMERO_REGISTRO, NUMERO_REGISTRO_PS6510,
				 CODIGO_IDENTIFICACAO,
				 DATA_PARECER,
				 HORA_PARECER,
				 STATUS_PARECER,
				 OBSERVACOES_PARECER) 
			 VALUES 
				(" . aspas($registro) . ",
				 " . aspas($_POST["registro"]) . ", 
				 " . aspas($_SESSION['codigoIdentificacao']) . ",
				 " . dataToSql( date("d/m/Y")) . ",
				 " . aspas(date("H:i")) . ",
				 " . aspas($_POST["status"]) . ",
				 " . aspas($_POST["texto"]) . ")";
				//pr($query,true);
	$retorno['MSG'] = 'ERRO';
	if (jn_query($query)){

	$query = 	"UPDATE PS6510
					SET SITUACAO = " . aspas($_POST["status"]) . "
				WHERE (NUMERO_REGISTRO = " . aspas($_POST["registro"]) . ")";
				
		
		if (jn_query($query)){
			$retorno['MSG'] = 'OK';
		}
	}	
	$i = 0;
	while($i < 6){
		$i = $i+1;
		$arquivo = isset($_FILES["file" . $i]) ? $_FILES["file" . $i] : FALSE;
		if($arquivo){
			$retornoImg = salvarImagem('PS6530',$registro,$arquivo);
		}
	}

	if ($_POST["status"] == 'A' and $Associado == '014000218758000'){
		
		$query  	 = ' SELECT CODIGO_INTERNO, NOME_ASSOCIADO FROM APP_USUARIO_INTERNO';
		$query 		.= ' INNER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = APP_USUARIO_INTERNO.CODIGO_USUARIO) ';
		$query 		.= ' WHERE PS1000.CODIGO_ASSOCIADO = '. aspas($Associado);		
		$res = jn_query($query);
		$row = jn_fetch_object($res);		
		
		$interno   = $row->CODIGO_INTERNO;
		$nomeAssoc = $row->NOME_ASSOCIADO;  
		
		$descricaoPush = ' Solicita&ccedil;&atilde;o Autoriza&ccedil;&atilde;o <br> '. $especialidade .' <br><br> Sr(a) ' . $nomeAssoc . ' <br> Solicita&ccedil;&atilde;o de n&uacute;mero '. $NumAutorizacao .' liberada e dispon&iacute;vel para consulta, clique AQUI e acesse sua GUIA. <br><br> Atenciosamente, <br> Central de Guias Plena Sa&uacute;de ';

		$queryAutorizado  = linhaJsonEdicao('NUMERO_REGISTRO_ORIGEM', aspas($NumAutorizacao));
		$queryAutorizado .= linhaJsonEdicao('TITULO_MENSAGEM', 'Solicitacao Autorizada');
		$queryAutorizado .= linhaJsonEdicao('DESCRICAO_MENSAGEM', aspas($descricaoPush));
		$queryAutorizado .= linhaJsonEdicao('CODIGO_ASSOCIADO', aspas($Associado));
		$queryAutorizado .= linhaJsonEdicao('CODIGO_INTERNO', aspas($interno));
		$queryAutorizado .= linhaJsonEdicao('PRIORIDADE_PUSH', '5');

		$retornoGravacao = gravaEdicao('APP_CONTROLE_PUSH', $queryAutorizado, 'I');	
	}
	if ((($_POST["status"] == 'A') or ($_POST["status"] == 'N') )){
		require_once('../services/enviaMsgAutorizacaoTallos.php');
		$retornoTalos = enviaMsgAutorizacaoTallos($Associado,$NumAutorizacao,$_POST["status"] );
	}	

	
	echo json_encode($retorno);


}







?>