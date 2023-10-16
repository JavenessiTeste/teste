<?php 

require('../lib/base.php');
require('../private/autentica.php');

	if($_POST['QTE_GUIAS']>50){
		$retorno['MSG']  = 'Insira uma quantidade menor que 50.';
		$retorno['VOLTAR_FILTROS']  = true;
	}else{

		$i = 0;
		while($i < $_POST['QTE_GUIAS']){		
			if(($_SESSION['type_db'] == 'mssqlserver') or ($_SESSION['type_db'] == 'sqlsrv')){
				$queryInsert  = " exec SP_GERA_NUMERO_GUIA_TISS_NET ";
				$queryInsert .= "  @ATipoGuia = ". aspas($_POST['TIPO_GUIA']);
				$queryInsert .= ", @AAno = " . substr($_POST['ANO_GUIA'], -2);
				$queryInsert .= ", @ANumeroAutorizacao = " . aspas('') . "";
				$queryInsert .= ", @ACodigoPrestador = " . aspas($_SESSION['codigoIdentificacao']);
				
				
			}else if($_SESSION['type_db'] =='firebird'){
				
			$queryInsert  = " select * from SP_GERA_NUMERO_GUIA_TISS_NET(";
				$queryInsert .= "  ". aspas($_POST['TIPO_GUIA']);
				$queryInsert .= ", " . substr($_POST['ANO_GUIA'], -2);
				$queryInsert .= ", " . aspas('') . "";
				$queryInsert .= ", " . aspas($_SESSION['codigoIdentificacao']).')';
			}
			//pr($queryInsert,false);
			$res = jn_query($queryInsert);
			$row = jn_fetch_object($res);
			//pr($row);
			$i++;
		}

		$retorno['MSG']  = 'Guias Criadas com Sucesso';
		$retorno['DESTINO']  = 'site/gridDinamico';
		
		$tipoGuiaFiltro = '';
		if($_POST['TIPO_GUIA']=='C'){
			$tipoGuiaFiltro = 'CONSULTA';
		}else if($_POST['TIPO_GUIA']=='S'){
			$tipoGuiaFiltro = 'SP/SADT';		
		}		
		
		$retorno['DADOS_DESTINO']['tabela'] ='VW_PS5804_AL2';
		$retorno['DADOS_DESTINO']['filtros'] = '[{"CAMPO":"ANO_GUIA","VALOR":'.substr($_POST['ANO_GUIA'], -2).'},{"CAMPO":"TIPO_GUIA","VALOR":"'.$tipoGuiaFiltro.'"}]';
	}

echo json_encode($retorno);

?>