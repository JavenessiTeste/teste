<?php
require('../lib/base.php');

if($dadosInput['tipo'] =='val'){
	
		$codigoAssociado		= $_SESSION['codigoIdentificacao'];//$_GET[''];
		$codigoPrestador	 	= $dadosInput['PRES'];
		$codigoProcedimento  	= $dadosInput['PROC'];
		$tipoGuia			 	= $dadosInput['TIPO'];
		$qteProc				= $dadosInput['QTE'];
		$codigoEspecialidade	= $dadosInput['ESP'];	
	
		$query = "SELECT VALIDACAO FROM SP_VALIDAPREAUTORIZACAO(".
													aspas($codigoAssociado).",".
													aspas($codigoPrestador).",".
													aspas($codigoProcedimento).",".
													aspas($tipoGuia).",".
													aspas($qteProc).",".
													"NULL,".
													"NULL,".
													aspas($codigoEspecialidade).",".
													"NULL,".
													"NULL)";
		
		$res = jn_query($query);
		
		$retorno['VALIDACAO'] = '';
		
		if ($row = jn_fetch_object($res)) {
			$retorno['VALIDACAO'] = jn_utf8_encode($row->VALIDACAO); 
		}
		
		$queryTipoAut = "select TIPO_AUTORIZACAO from PS5210 where codigo_procedimento =".aspas($codigoProcedimento); 
		$resTipoAut = jn_query($queryTipoAut);
		if ($rowTipoAut = jn_fetch_object($resTipoAut)) {
			$lib = trim(substr(trim($rowTipoAut->TIPO_AUTORIZACAO),0,1));
			//pr($rowTipoAut->TIPO_AUTORIZACAO);
			//pr($rowTipoAut);
			//pr($lib);
			if(($lib=='') or ($lib=='L')){
				$retorno['TIPO_AUTO'] = 'OK';
			}else{
				$retorno['TIPO_AUTO'] = 'AUDITORIA';
			}
		}
		
		if($_GET['TIPO']=='I'){
			$retorno['TIPO_AUTO'] = 'AUDITORIA';
		}
		echo json_encode($retorno);	
}else if($dadosInput['tipo'] =='salvar'){
		$retorno["MSG"] = 'OK';
		$dados = array();  
		
		
		foreach ($dadosInput['PROC'] as $item) {
			$dados[$item['prest']][] = $item;
		}
		
		foreach ($dados as $chave => $valor){
			
			
			$NumAutorizacao = jn_gerasequencial('PS6500');
			
			$nomePessoa;
			$senha = rand(100000, 9999999);
			$queryPs6500= "
				Insert Into PS6500(
									NUMERO_AUTORIZACAO,
									TIPo_GUIA,
									CODIGO_ASSOCIADO,
									CODIGO_PRESTADOR,
									CODIGO_SOLICITANTE,
									DATA_DIGITACAO,
									NOME_PESSOA,
									HORARIO_AUTORIZACAO,
									PROCEDIMENTO_PRINCIPAL,
									CODIGO_ESPECIALIDADE,
									DATA_VALIDADE,
									DATA_AUTORIZACAO,
									DATA_PROCEDIMENTO,
									AUTORIZADO_POR,
									DESCRICAO_OBSERVACAO,
									CARATER_SOLICITACAO,
									NUMERO_SENHA_AUTORIZ,
									CODIGO_PRESTADOR_EXECUTANTE,
									FLAG_GUIA_DIGITAL,
									PROTOCOLO_GUIA_DIGITAL
								)Values(
								".aspas($NumAutorizacao ).",
								".aspas($dadosInput['TIPO']).",
								".aspas($_SESSION['codigoIdentificacao']).",
								".aspas($chave).",
								".aspas($chave).",
								GETDATE(),
								".aspas($nomePessoa).",
								CONVERT(VARCHAR(5), DATEADD(hour, 4, getdate()), 108),
								".aspas($valor[0]['proc']).",
								".aspas($dadosInput['ESP']).",
								DATEADD (day , 30 , GETDATE() )  ,
								GETDATE(),
								GETDATE(),
								".aspas('1').",
								".aspas('Nome Solicitante: '. $dadosInput['NOME'].' Crm Solicitante: '.$dadosInput['CRM']).",
								".aspas('E').",
								".aspas($senha).",
								".aspas($chave).",
								".aspas('S').",
								".aspas(date('dmY').$NumAutorizacao).")";
			$resPs6500 = jn_query($queryPs6500);	
		
			//echo ($queryPs6500);
			
			$auditoria = false;
			
			foreach ($valor as $item){
					$situacao = 'A';
					$queryTipoAut = "select TIPO_AUTORIZACAO from PS5210 where codigo_procedimento =".aspas($item['proc']); 
					$resTipoAut = jn_query($queryTipoAut);
					if ($rowTipoAut = jn_fetch_object($resTipoAut)) {
						$lib = trim(substr(trim($rowTipoAut->TIPO_AUTORIZACAO),0,1));
						
						if(($lib=='') or ($lib=='L')){
							$situacao = 'A';
						}else{
							$situacao = 'P';
						}
					}
					if($dadosInput['TIPO']=='I')
						$situacao = 'P';
					
					if($situacao == 'P'){
						$auditoria = true;
					}
				$queryPs6510 = "Insert into PS6510(
												NUMERO_AUTORIZACAO,
												CODIGO_PROCEDIMENTO,
												QUANTIDADE_PROCEDIMENTOS,SITUACAO
												)Values(
												".aspas($NumAutorizacao).",
												".aspas($item['proc']).",
												".aspas($item['qte']).",
												".aspas($situacao)."
												)";
								
				$resPs6510 = jn_query($queryPs6510);
				//echo $queryPs6510;

				
									
			}						
								
		
		}
		
		if($auditoria){
			
			$query = "select NOME_ASSOCIADO,ENDERECO_EMAIL from PS1000 
					inner join PS1001 on PS1001.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO
					where ps1000.codigo_associado =".aspas($_SESSION['codigoIdentificacao']);
				
			$res = jn_query($query);
		
			$valores = ''; 	
			$valorFinal;
			
			if($row = jn_fetch_object($res)) {
				if($row->ENDERECO_EMAIL!=''){
					$texto = 'Protocolo: '.date('dmY').$NumAutorizacao.'<br><br>
							  Sr(a). '.$row->NOME_ASSOCIADO.'<br><br>
							  Recebemos sua solicitação daremos retorno em até 05 dias úteis.<br><br>
							  Atenciosamente,
							  Central de Guias Plena Saúde';
					disparaEmail($row->ENDERECO_EMAIL,gerahtml($texto),'Autorização');
				}
			}
		}
		$retorno['MSG'] = 'Autorização Salva com Sucesso.';
		$retorno['DESTINO'] = 'site/gridDinamico';
		$retorno['tabela'] = 'PS6500';
		echo json_encode($retorno);	
}
	
?>