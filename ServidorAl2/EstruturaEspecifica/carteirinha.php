<?php

require_once('../lib/base.php');

require_once('../private/autentica.php');


if($_SESSION['codigoSmart'] =='3423'){
	if($dadosInput['tipo'] =='frente'){
		$queryPrincipal =	"Select CODIGO_ASSOCIADO, NOME_ASSOCIADO, CODIGO_EMPRESA, NOME_EMPRESA, CODIGO_PLANO, DATA_NASCIMENTO, 	ARQUIVO_BACK_APP_FRENTE, ARQUIVO_BACK_APP_VERSO,
 							 DATA_ADMISSAO, NOME_PLANO, CADASTRO_ANS, CODIGO_CARENCIA, TIPO_ACOMODACAO, NOME_TITULAR from APP_VW_CABECALHO_CARTEIRINHA_V3
							 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
		
		$resultQuery = jn_query($queryPrincipal); 
		
		if($rowPrincipal    = jn_fetch_object($resultQuery)){
			$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
			
			ob_start(); 
			imagejpeg( $imagem, NULL, 100 ); 
			imagedestroy( $imagem ); 
			$i = ob_get_clean();

			
			$retorno['IMG'] = base64_encode($i);
		
		}	
		echo json_encode($retorno);
	}else if($dadosInput['tipo'] =='verso'){
		$queryPrincipal =	"Select APP_VW_CABECALHO_CARTEIRINHA_V3.CODIGO_ASSOCIADO, APP_VW_CABECALHO_CARTEIRINHA_V3.NOME_ASSOCIADO, APP_VW_CABECALHO_CARTEIRINHA_V3.CODIGO_EMPRESA, 
							 APP_VW_CABECALHO_CARTEIRINHA_V3.NOME_EMPRESA, APP_VW_CABECALHO_CARTEIRINHA_V3.CODIGO_PLANO, APP_VW_CABECALHO_CARTEIRINHA_V3.DATA_NASCIMENTO, 	APP_VW_CABECALHO_CARTEIRINHA_V3.ARQUIVO_BACK_APP_FRENTE, 
							 APP_VW_CABECALHO_CARTEIRINHA_V3.ARQUIVO_BACK_APP_VERSO,APP_VW_CABECALHO_CARTEIRINHA_V3.DATA_ADMISSAO, APP_VW_CABECALHO_CARTEIRINHA_V3.NOME_PLANO, APP_VW_CABECALHO_CARTEIRINHA_V3.CADASTRO_ANS, APP_VW_CABECALHO_CARTEIRINHA_V3.CODIGO_CARENCIA, 
							 APP_VW_CABECALHO_CARTEIRINHA_V3.TIPO_ACOMODACAO, APP_VW_CABECALHO_CARTEIRINHA_V3.NOME_TITULAR,PS1000.CODIGO_CNS,ps1030.NOME_PLANO_EMPRESAS,NOME_USUAL_FANTASIA,
							 APP_VW_CABECALHO_CARTEIRINHA_V3.NOME_SOCIAL
							 from APP_VW_CABECALHO_CARTEIRINHA_V3
							 inner join ps1000 on ps1000.codigo_associado = APP_VW_CABECALHO_CARTEIRINHA_V3.codigo_associado 
							 inner join ps1030 on ps1000.CODIGO_PLANO = ps1030.CODIGO_PLANO 
							 inner join ps1010 on ps1000.codigo_empresa =  ps1010.codigo_empresa 
							 WHERE APP_VW_CABECALHO_CARTEIRINHA_V3.CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
		
		$resultQuery = jn_query($queryPrincipal); 
		
		if($rowPrincipal    = jn_fetch_object($resultQuery)){
			
			$segmentacao = 'Ambulatorial + Hospitalar + Obstetricia';
			$cpt= 'Isento';
			$naoOdonto = true;
			if(trim($rowPrincipal->ARQUIVO_BACK_APP_VERSO) === 'CARTEIRINHAVERSO_MEDICINAPJ.PNG'){
				$segmentacao = 'Ambulatorial + Hospitalar + Obstetricia';
			}else if(trim($rowPrincipal->ARQUIVO_BACK_APP_VERSO) === 'CARTEIRINHAVERSO_MEDICINA.PNG'){
				$segmentacao = 'Ambulatorial + Hospitalar + Obstetricia';
			}else if(trim($rowPrincipal->ARQUIVO_BACK_APP_VERSO) === 'CARTEIRINHAVERSO_ODONTO.PNG'){
				$segmentacao = 'Odontológico';
				$naoOdonto = false;
			}else if(trim($rowPrincipal->ARQUIVO_BACK_APP_VERSO) === 'CARTEIRINHAVERSO_ONIX.PNG'){
				$segmentacao = 'Ambulatorial+Hospitalar+Obstetricia+Odont.';
			}else if(trim($rowPrincipal->ARQUIVO_BACK_APP_VERSO) === 'CARTEIRINHAVERSO_DIAMANTE.PNG'){
				$segmentacao = 'Ambulatorial+Hospitalar+Obstetricia+Odont.';
			}else if(trim($rowPrincipal->ARQUIVO_BACK_APP_VERSO) === 'CARTEIRINHAVERSO_MEDICINA.PNG'){
				$segmentacao = 'Ambulatorial + Hospitalar + Obstetricia';
			}
			if($rowPrincipal->CODIGO_PLANO=='51'){
				$naoOdonto = true;
				$segmentacao = 'Ambulatorial+Hospitalar+Obstetricia.';
			}
			if($rowPrincipal->CODIGO_PLANO=='163'){
				$naoOdonto = true;
				$segmentacao = 'Ambulatorial+Hospitalar+Obstetricia+Odont.';
			}
			
			$CodigoAssociado2 = Mask("##.###.#######.##-#",$dadosInput['cod']);
			
			$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
			
			$cor = imagecolorallocate($imagem, 39, 64, 139 );
			
			$normal  = "../../Site/assets/img/arial.ttf";
			$negrito = "../../Site/assets/img/arialbd.ttf";
		
			
			$linha  = 40;
			$coluna = 30;
			$coluna2 = 650;
			
			$pulaLinha = 23;
			$pulaLinha2 = 23;
			
			$tamanhoCaracter = 7;
			$tamanhoCaracter2 = 14;
			
			$tamanhoLetra = 9;
			$tamanhoLetra2 = 19;
			

			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Beneficiário');
			$linha  += $pulaLinha+5;
			imagettftext($imagem, $tamanhoLetra2+5, 0, $coluna, $linha, $cor,$negrito,substr($rowPrincipal->NOME_ASSOCIADO,0,32));
			$linha  += $pulaLinha2;
			
			if ($rowPrincipal->NOME_SOCIAL != ''){
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nome Social');
				$linha  += $pulaLinha+5;
				imagettftext($imagem, $tamanhoLetra2+5, 0, $coluna, $linha, $cor,$negrito,substr($rowPrincipal->NOME_SOCIAL,0,32));				
				$linha  += $pulaLinha2;						
			}else{
				$linha  += $pulaLinha+5;				
				$linha  += $pulaLinha2;		
			}
			
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Data Nascimento');
			imagettftext($imagem, $tamanhoLetra, 0, $coluna2-(strlen('Admissão')*$tamanhoCaracter), $linha, $cor,$negrito,'Admissão');
			
			$linha  += $pulaLinha;
			
			imagettftext($imagem, $tamanhoLetra2, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->DATA_NASCIMENTO->format('d/m/Y'));
			imagettftext($imagem, $tamanhoLetra2, 0, $coluna2+$tamanhoCaracter2-(strlen($rowPrincipal->DATA_ADMISSAO->format('d/m/Y'))*$tamanhoCaracter2), $linha, $cor,$normal,$rowPrincipal->DATA_ADMISSAO->format('d/m/Y'));
			$linha  += $pulaLinha2;
			
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Plano Regulamentado');
			imagettftext($imagem, $tamanhoLetra, 0, $coluna2-(strlen('CNS n°')*$tamanhoCaracter), $linha, $cor,$negrito,'  CNS n°');
			
			$linha  += $pulaLinha;
			
			imagettftext($imagem, $tamanhoLetra2, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_PLANO_EMPRESAS);
			imagettftext($imagem, $tamanhoLetra2, 0, $coluna2-(strlen($rowPrincipal->CODIGO_CNS)*$tamanhoCaracter2), $linha, $cor,$normal,$rowPrincipal->CODIGO_CNS);
			$linha  += $pulaLinha2;

			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Segmentação Assistencial');
			if($naoOdonto)
				imagettftext($imagem, $tamanhoLetra, 0, $coluna2+2-(strlen('Acomodação')*$tamanhoCaracter), $linha, $cor,$negrito,'Acomodação');
			
			$linha  += $pulaLinha;
			
			imagettftext($imagem, $tamanhoLetra2, 0, $coluna, $linha, $cor,$normal,substr($segmentacao,0,39));
			if($naoOdonto)
				imagettftext($imagem, $tamanhoLetra2, 0, $coluna2-10-(strlen(substr($rowPrincipal->TIPO_ACOMODACAO,0,38))*$tamanhoCaracter2), $linha, $cor,$normal,substr($rowPrincipal->TIPO_ACOMODACAO,0,38));
			$linha  += $pulaLinha2;
			
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Registro ANS');
			if($naoOdonto)
				imagettftext($imagem, $tamanhoLetra, 0, $coluna2+7-(strlen('Abrangência')*$tamanhoCaracter), $linha, $cor,$negrito,'Abrangência');
			
			$linha  += $pulaLinha;
			
			imagettftext($imagem, $tamanhoLetra2, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CADASTRO_ANS);
			if($naoOdonto)
				imagettftext($imagem, $tamanhoLetra2, 0, $coluna2+36-(strlen('Grupo de Municípios')*$tamanhoCaracter2), $linha, $cor,$normal,'Grupo de Municípios');
			$linha  += $pulaLinha2;			

			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Empresa Contratante');
			imagettftext($imagem, $tamanhoLetra, 0, $coluna2-(strlen('CPT até')*$tamanhoCaracter), $linha, $cor,$negrito,'CPT até');
			
			$linha  += $pulaLinha;
			
			if (($_SESSION['PERFIL_USUARIO'] == 'ASSOCIADO_PJ') or ($_SESSION['PERFIL_USUARIO'] == 'ASSOCIADO_PJ_T') or ($_SESSION['PERFIL_USUARIO'] == 'ASSOCIADO_PJ_D')) {
				
				$queryAuxiliar = "Select Count(*) as TOTAL from APP_VW_CARENCIAS_CART_PJ_V3
								  WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);
				
				$resultAux = jn_query($queryAuxiliar);
				
				if ($objAux = jn_fetch_object($resultAux))
				{
					if ($objAux->TOTAL > 0) 
					{
						$queryPrincipal = "Select CODIGO_ASSOCIADO,
												  CODIGO_CARENCIA,
												  DESCRICAO_CARENCIA,
												  DATA_CARENCIA from APP_VW_CARENCIAS_CART_PJ_V3
												  WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);
					} 
					else 
					{
						$queryPrincipal = "Select CODIGO_ASSOCIADO,
												  CODIGO_CARENCIA,
												  DESCRICAO_CARENCIA,
												  DATA_CARENCIA from APP_VW_CARENCIAS_CART_PF_V3
												  WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);
					}
				}
				
			} 
			else 
			{
				$queryPrincipal =	"Select CODIGO_ASSOCIADO,
											CODIGO_CARENCIA,
											DESCRICAO_CARENCIA,
											DATA_CARENCIA from APP_VW_CARENCIAS_CART_PF_V3
											WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);
			}	

			$resultQueryGrupo = jn_query($queryPrincipal); 
			
			$i=0;
			while($rowPrincipalGrupo    = jn_fetch_object($resultQueryGrupo)){
				
				$pos = strpos(strtoupper($rowPrincipalGrupo->DESCRICAO_CARENCIA), 'CTP'); 
				if ($pos !== false) {
					$cpt = $rowPrincipalGrupo->DATA_CARENCIA->format('d/m/Y');
				}
				$pos = strpos(strtoupper($rowPrincipalGrupo->DESCRICAO_CARENCIA), 'CPT'); 
				if ($pos !== false) {
					$cpt = $rowPrincipalGrupo->DATA_CARENCIA->format('d/m/Y');
				}
				
			}			
			

			imagettftext($imagem, $tamanhoLetra2, 0, $coluna, $linha, $cor,$normal,substr($rowPrincipal->NOME_USUAL_FANTASIA,0,32));
			imagettftext($imagem, $tamanhoLetra2, 0, $coluna2+$tamanhoCaracter2-(strlen($cpt)*$tamanhoCaracter2), $linha, $cor,$normal,$cpt);
			$linha  += $pulaLinha2;	
			
			imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,'Matrícula Beneficiário');
			$linha  += $pulaLinha;	
			imagettftext($imagem, $tamanhoLetra2+5, 0, 200, $linha, $cor,$negrito,$CodigoAssociado2);
			$linha  += $pulaLinha2;	
			
			ob_start(); 
			imagejpeg( $imagem, NULL, 100 ); 
			imagedestroy( $imagem ); 
			$i = ob_get_clean();

			$retorno['IMG'] = base64_encode($i);
		}	
		echo json_encode($retorno);
	}

}else if($_SESSION['codigoSmart'] =='3808'){// Dana, GKN ou Associacao
	$queryInscSusep = "Select numero_insc_susep from CFGEMPRESA";

	$resultInscSusep = jn_query($queryInscSusep);
	$rowInscSusep = jn_fetch_object($resultInscSusep);
	
	if($rowInscSusep->NUMERO_INSC_SUSEP == '418218'){ // Associacao - Foi necessario criar um IF auxiliar, porque a RS Saude tem o mesmo codigo smart
	
		if($dadosInput['tipo'] =='frente'){
			$retorno['IMG'] = '';
				
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				
				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();
	
				
				$retorno['IMG'] = base64_encode($i);
			
			}
			
	
			echo json_encode($retorno);
		
		}else if($dadosInput['tipo'] =='verso'){
			
			$retorno['IMG'] = '';
				
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 

			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
				}



				if (($rowPrincipal->CODIGO_PLANO=='55') or ($rowPrincipal->CODIGO_PLANO=='75') or ($rowPrincipal->CODIGO_PLANO=='45') or ($rowPrincipal->CODIGO_PLANO=='15')){


					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);


					$normal  = "../../Site/assets/fonts/arial.ttf";
					$negrito = "../../Site/assets/fonts/arialbd.ttf";
				
					$linha  = 90;
					$coluna = 30;
			
					$pulaLinha = 27;
			

					$linha  += $pulaLinha;
					$tamanhoLetra = 20;
					imagettftext($imagem, $tamanhoLetra+20, 0, $coluna+20, $linha+50, $cor,$negrito,$rowPrincipal->CODIGO_ASSOCIADO);
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+20, $linha+175, $cor,$negrito,$rowPrincipal->NOME_ASSOCIADO);
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+1085, $linha+175, $cor, $normal,SqlToData($rowPrincipal->DATA_VALIDADE));

					$tamanhoLetra = 20;
					$linha  += $pulaLinha+230;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+20, $linha, $cor,$negrito,$rowPrincipal->NOME_TITULAR);
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+655, $linha, $cor,$normal,$rowPrincipal->NOME_EMPRESA);		
							

					$linha  += $pulaLinha+20;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+655, $linha+44, $cor,$negrito,$rowPrincipal->CODIGO_INTERCAMBIO);	
				
					$linha  += $pulaLinha;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+485, $linha, $cor,$negrito,$rowPrincipal->ABRANGENCIA);


					if ($rowPrincipal->CODIGO_CARENCIA != '') {
					

	                    $carencias = getCarencias($rowPrincipal->CODIGO_ASSOCIADO);	                    
	                    $linha += 90;
	                    foreach((array) $carencias as $carencia) {
							$tipoImagem = (compareData(SqlToData($carencia->RESULTADO_DATA_CARENCIA)) >= 0) ? 1 : 0;

							$databd     = $carencia->RESULTADO_DATA_CARENCIA;
					    	$databd     = explode("/",$databd); 
					    	$dataBol    = mktime(0,0,0,$databd[1],$databd[0],$databd[2]);
					    	$data_atual = mktime(0,0,0,date("m"),date("d"),date("Y"));
					    	$dias       = ($data_atual - $dataBol)/86400;
					    	$diasCarencia = ceil($dias);

		                    if ($diasCarencia > 0){

			                    if ($carencia->RESULTADO_NUMERO_GRUPO == '1') {

			                    	$descCarencia = 'URG/EMERG';

			                    }else if ($carencia->RESULTADO_NUMERO_GRUPO == '2'){

			                    	$descCarencia = 'CONS';

			                    }else if ($carencia->RESULTADO_NUMERO_GRUPO == '3'){

			                    	$descCarencia = 'EX SIMPLE';
			                    	
			                    }else if ($carencia->RESULTADO_NUMERO_GRUPO == '4'){

			                    	$descCarencia = 'EX ESPEC';	
			                    	
			                    }else if ($carencia->RESULTADO_NUMERO_GRUPO == '5'){

			                    	$descCarencia = 'QUIMIO/RADO';
			                    	
			                    }else if ($carencia->RESULTADO_NUMERO_GRUPO == '6'){

			                    	$descCarencia = 'PARTO/CESAR';
			                    	
			                    }else if ($carencia->RESULTADO_NUMERO_GRUPO == '7'){

			                    	$descCarencia = 'DOENÇAS PRÉ EXIS';	
			                    }

			                }else{

			                	$descCarencia = 'Já Cumpridas';
			                }

			                $linha += 32;
			                imagettftext($imagem, $tamanhoLetra, 0, $coluna+20, $linha, $cor,$normal,$descCarencia);
			                imagettftext($imagem, $tamanhoLetra, 0, $coluna+315, $linha, $cor,$normal,SqlToData($carencia->RESULTADO_DATA_CARENCIA));
						}
		            }
                  
					ob_start(); 
					imagejpeg( $imagem, NULL, 100 ); 
					imagedestroy( $imagem ); 
					$i = ob_get_clean();

			
					$retorno['IMG'] = base64_encode($i); 	             


				}else{

					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
					
					$cor = imagecolorallocate($imagem, 39, 64, 139 );				
					
					$normal  = "../../Site/assets/fonts/arial.ttf";
					$negrito = "../../Site/assets/fonts/arialbd.ttf";
					
					$linha  = 80;
					$coluna = 140;
					
					$pulaLinha = 27;
					$tamanhoCaracter = 20;
								

					$tamanhoLetra = 14;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,$rowPrincipal->NOME_EMPRESA);

					$linha  += $pulaLinha;				
					imagettftext($imagem, $tamanhoLetra, 0, $coluna-5, $linha, $cor,$negrito,$rowPrincipal->NOME_ASSOCIADO);

					$linha  += $pulaLinha+3;				
					imagettftext($imagem, $tamanhoLetra, 0, $coluna-15, $linha, $cor,$negrito,$rowPrincipal->NOME_ASSOCIADO);

								
					$linha  += $pulaLinha+4;				
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+9, $linha, $cor,$negrito,$rowPrincipal->CODIGO_ASSOCIADO);
					
					$linha  += $pulaLinha;				
					imagettftext($imagem, $tamanhoLetra, 0, $coluna-30, $linha, $cor,$negrito,$rowPrincipal->NOME_PLANO);
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+310, $linha, $cor,$negrito,$rowPrincipal->TIPO_ACOMODACAO);
					
					$linha  += $pulaLinha;	
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+140, $linha, $cor,$negrito,$rowPrincipal->CODIGO_INTERCAMBIO);
					
					$linha  += $pulaLinha+31;	
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+10, $linha, $cor,$negrito,'JÁ CUMPRIDAS');
					
					$linha  += $pulaLinha+114;				
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+220, $linha, $cor,$negrito,SqlToData($rowPrincipal->DATA_VALIDADE));


					ob_start(); 
					imagejpeg( $imagem, NULL, 80 ); 
					imagedestroy( $imagem ); 
					$i = ob_get_clean();

					
					$retorno['IMG'] = base64_encode($i);

				}
				
			}

			echo json_encode($retorno);
		}
				
	}elseif($dadosInput['tipo'] =='frente'){

		

		$retorno['IMG'] = '';
			
		$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
							 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
		
		$resultQuery = jn_query($queryPrincipal); 


		if($rowInscSusep->NUMERO_INSC_SUSEP == '416819'){ // GKN - Foi necessario criar um IF auxiliar, porque Dana e GKN tem o mesmo codigo smart

			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				
				$cor = imagecolorallocate($imagem, 39, 64, 139 );				
				
				$normal  = "../../Site/assets/fonts/arial.ttf";
				$negrito = "../../Site/assets/fonts/arialbd.ttf";
				
				$linha  = 180;
				$coluna = 30;
				
				$pulaLinha = 27;
				$tamanhoCaracter = 20;
							

				$linha  += $pulaLinha;
				$tamanhoLetra = 17;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,$rowPrincipal->NOME_ASSOCIADO);

				$tamanhoLetra = 15;
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+470, $linha, $cor,$negrito,sqlTodata($rowPrincipal->DATA_NASCIMENTO));

				$linha  += $pulaLinha+60;
				imagettftext($imagem, $tamanhoLetra+4, 0, $coluna, $linha, $cor,$negrito,$rowPrincipal->CODIGO_ASSOCIADO);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+455, $linha, $cor,$negrito,$rowPrincipal->CODIGO_CNS);			
							

				$linha  += $pulaLinha+18;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+500, $linha, $cor,$negrito,'Não há');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+105, $linha, $cor,$negrito,'Já cumpridas');

				$linha  += $pulaLinha+20;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+150, $linha, $cor,$negrito,substr($rowPrincipal->NOME_PLANO,0,25));

				$linha  += $pulaLinha+2;			
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+170, $linha, $cor,$negrito,($rowPrincipal->CADASTRO_ANS));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+470, $linha, $cor,$negrito,$rowPrincipal->TIPO_ACOMODACAO);
				
				$linha  += $pulaLinha;
				
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+485, $linha, $cor,$negrito,$rowPrincipal->ABRANGENCIA);

							

				ob_start(); 
				imagejpeg( $imagem, NULL, 80 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
			
			}
			
		} else { //Dana
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}


				if ($rowPrincipal->CODIGO_PLANO=='17'){
					
					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				
					$cor = imagecolorallocate($imagem, 39, 64, 139 );				
					
					$normal  = "../../Site/assets/fonts/arial.ttf";
					$negrito = "../../Site/assets/fonts/arialbd.ttf";
					
					$linha  = 280;
					$coluna = 60;
					
					$pulaLinha = 27;
					$tamanhoCaracter = 18;
								

					$linha  += $pulaLinha+37;
					$tamanhoLetra = 28;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,$rowPrincipal->NOME_ASSOCIADO);

					$linha  += $pulaLinha+60;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+210, $linha+15, $cor,$negrito,$rowPrincipal->CODIGO_ASSOCIADO);

					$linha  += $pulaLinha+34;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+430, $linha, $cor,$negrito,sqlTodata($rowPrincipal->DATA_NASCIMENTO));


					$linha  += $pulaLinha+72;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+165, $linha, $cor,$negrito,($rowPrincipal->CODIGO_CNS));	


					ob_start(); 
					imagejpeg( $imagem, NULL, 80 ); 
					imagedestroy( $imagem ); 
					$i = ob_get_clean();

					
					$retorno['IMG'] = base64_encode($i);	

				}else {

					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
					

					$cor = imagecolorallocate($imagem, 0, 0, 0 );
					
					$normal  = "../../Site/assets/fonts/arial.ttf";
					$negrito = "../../Site/assets/fonts/arialbd.ttf";
					
					$linha  = 250;
					$coluna = 20;
					
					$pulaLinha = 40;
					$tamanhoCaracter = 20;
								

					$linha  += $pulaLinha;
					$tamanhoLetra = 28;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,$rowPrincipal->NOME_ASSOCIADO);

					$tamanhoLetra = 19;

					$linha  += $pulaLinha+40;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Matrícula: '. $rowPrincipal->CODIGO_ASSOCIADO);
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+675, $linha, $cor,$negrito,'Tipo de acomodação: ');

					$linha  += $pulaLinha;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nascimento: '. sqlTodata($rowPrincipal->DATA_NASCIMENTO));			
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+675, $linha, $cor,$negrito,$rowPrincipal->TIPO_ACOMODACAO);

					$linha  += $pulaLinha;
					$linha  += $pulaLinha;
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nº CNS: '. ($rowPrincipal->CODIGO_CNS));			
					

					$linha  += $pulaLinha;
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nº Produto ANS: '. ($rowPrincipal->CADASTRO_ANS));			
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+675, $linha, $cor,$negrito,'Carências: Já cumpridas');
					
					
					$linha  += $pulaLinha;
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nome Produto: '.substr($rowPrincipal->NOME_PLANO,0,25));
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+675, $linha, $cor,$negrito,'CPT: Não há');
					$linha  += $pulaLinha;
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Coletivo empresarial');
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+675, $linha, $cor,$negrito,$rowPrincipal->ABRANGENCIA);

								

					ob_start(); 
					imagejpeg( $imagem, NULL, 100 ); 
					imagedestroy( $imagem ); 
					$i = ob_get_clean();

					
					$retorno['IMG'] = base64_encode($i);

				}
			}

		}

		echo json_encode($retorno);
	
	}else if($dadosInput['tipo'] =='verso'){
		
		$retorno['IMG'] = '';
			
		$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
							 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
		
		$resultQuery = jn_query($queryPrincipal); 
		
		if($rowPrincipal    = jn_fetch_object($resultQuery)){
			if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
				$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
			}
			
			$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
			
			ob_start(); 
			imagejpeg( $imagem, NULL, 100 ); 
			imagedestroy( $imagem ); 
			$i = ob_get_clean();

			
			$retorno['IMG'] = base64_encode($i);
			
		}
		
		echo json_encode($retorno);
				
	}

	
}else if($_SESSION['codigoSmart'] =='exe'){
	
	
}

else if($_SESSION['codigoSmart'] =='4018')//Hebrom
{
	if($dadosInput['tipo'] =='frente'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				
				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
				
			}
			
			echo json_encode($retorno);
		
	}else if($dadosInput['tipo'] =='verso'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select APP_VW_CABECALHO_CARTEIRINHA_V3.*, ps1000.CODIGO_CNS, ps1030.CODIGO_CADASTRO_ANS, 
			                     case 
								    when ps1030.codigo_tipo_acomodacao = '1' then '1 - Individual'
									when ps1030.codigo_tipo_acomodacao = '2' then '2 - Coletivo' 
									when ps1030.codigo_tipo_acomodacao = '3' then '3 - Sem Acomodacao' 
									else '---'
								 end TIPO_ACOMODACAO,
			                     case 
								    when ps1030.CODIGO_TIPO_ABRANGENCIA = '1' then '1 - Nacional'
									when ps1030.CODIGO_TIPO_ABRANGENCIA = '2' then '2 - Grupo de Estados'
									when ps1030.CODIGO_TIPO_ABRANGENCIA = '3' then '3 - Estadual'
									when ps1030.CODIGO_TIPO_ABRANGENCIA = '4' then '4 - Grupo de Municípios'
									when ps1030.CODIGO_TIPO_ABRANGENCIA = '5' then '5 - Municipal'
									when ps1030.CODIGO_TIPO_ABRANGENCIA = '6' then '6 - Outros'
									else '---'
								 end TIPO_ABRANGENCIA
								 
								 from APP_VW_CABECALHO_CARTEIRINHA_V3
			                     inner join ps1000 on (APP_VW_CABECALHO_CARTEIRINHA_V3.codigo_associado = ps1000.codigo_associado)
								 inner join ps1030 on (ps1000.codigo_plano = ps1030.codigo_plano)
								 WHERE APP_VW_CABECALHO_CARTEIRINHA_V3.CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
				}
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
				

				$cor = imagecolorallocate($imagem, 0, 0, 0 );
				
				$normal  = "../../Site/assets/fonts/arial.ttf";
				$negrito = "../../Site/assets/fonts/arialbd.ttf";
				
				$linha  = 520;
				$coluna = 300;
				
				$pulaLinha = 450;
				$tamanhoCaracter = 100;
				

				$tamanhoLetra = 70;
				
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,substr($rowPrincipal->NOME_ASSOCIADO,0,20));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha+100, $cor,$normal,substr($rowPrincipal->NOME_ASSOCIADO,20,40));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+1250, $linha, $cor,$normal,$rowPrincipal->CODIGO_AUXILIAR);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+2500, $linha, $cor,$normal,$rowPrincipal->CODIGO_PLANO.'  '.$rowPrincipal->NOME_PLANO);

				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,SqlToData($rowPrincipal->CODIGO_CNS));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+1250, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_NASCIMENTO));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+2500, $linha, $cor,$normal,$rowPrincipal->CODIGO_CADASTRO_ANS);
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_ADMISSAO));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+1250, $linha, $cor,$normal,$rowPrincipal->TIPO_ABRANGENCIA);
				
				$linha  += $pulaLinha;
				//imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_EMPRESA);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,'HEBROM BENEFICIOS');  // foi pedido para deixar como padrão pelo Artur em 23/10/2020
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+1250, $linha, $cor,$normal,$rowPrincipal->TIPO_ACOMODACAO);
				$linha  += $pulaLinha;		

						

				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
			
			}
				
			echo json_encode($retorno);
	}
	
}
else if($_SESSION['codigoSmart'] =='4246')//MV2C
{
	if($dadosInput['tipo'] =='frente'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				
				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
				
			}
			
			echo json_encode($retorno);
		
	}else if($dadosInput['tipo'] =='verso'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
				}
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
				

				$cor = imagecolorallocate($imagem, 0, 0, 0 );
				
				$normal  = "../../Site/assets/fonts/arial.ttf";
				$negrito = "../../Site/assets/fonts/arialbd.ttf";
				
				$linha  = 220;
				$coluna = 82;
				
				$pulaLinha = 53;
				$tamanhoCaracter = 20;
				

				$tamanhoLetra = 27;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Código');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+400, $linha, $cor,$negrito,'Adesão');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+700, $linha, $cor,$negrito,'Dt. Nasc.');
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_AUXILIAR);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+400, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_ADMISSAO));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+700, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_NASCIMENTO));
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nome');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Empresa');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->DESCRICAO_GRUPO_PESSOAS);
				$linha  += $pulaLinha;		

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Plano');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_PLANO.'  '.$rowPrincipal->NOME_PLANO);
				$linha  += $pulaLinha;	

							

				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
			
			}
				
			echo json_encode($retorno);
	}
}
else if($_SESSION['codigoSmart'] =='4200')//Propulsão
{
	if($dadosInput['tipo'] =='frente'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				
				if($rowPrincipal->ARQUIVO_BACK_APP_FRENTE == 'IMAGEMFRENTEKIDS.png'){
					$cor = imagecolorallocate($imagem, 0, 0, 0 );
					
					$normal  = "../../Site/assets/fonts/arial.ttf";
					$negrito = "../../Site/assets/fonts/arialbd.ttf";
					
					$linha  = 580;
					$coluna = 115;
					
					$pulaLinha = 20;
					$tamanhoCaracter = 20;
					
					$tamanhoLetra = 20;
					
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);

					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;		
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_ASSOCIADO);
					
					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;							
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,'Data de Nascimento: ' . SqlToData($rowPrincipal->DATA_NASCIMENTO));
					
					$linha  += $pulaLinha;							
					$linha  += $pulaLinha-5;							
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,'Data de Admissão: ' . SqlToData($rowPrincipal->DATA_ADMISSAO));

				}
				
				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
				
			}
			
			echo json_encode($retorno);
		
	}else if($dadosInput['tipo'] =='verso'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
				}
				
				if($rowPrincipal->ARQUIVO_BACK_APP_VERSO == 'IMAGEMVERSO.png'){
					
					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
					

					$cor = imagecolorallocate($imagem, 0, 0, 0 );
					
					$normal  = "../../Site/assets/fonts/arial.ttf";
					$negrito = "../../Site/assets/fonts/arialbd.ttf";
					
					$linha  = 190;
					$coluna = 115;
					
					$pulaLinha = 30;
					$tamanhoCaracter = 20;
					
					$tamanhoLetra = 18;
					
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_ASSOCIADO);
					$linha  += $pulaLinha;
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);

					$linha  += $pulaLinha;		
					$linha  += $pulaLinha+5;		
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_NASCIMENTO));
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+370, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_ADMISSAO));
					imagettftext($imagem, $tamanhoLetra-3, 0, $coluna+360, $linha+23, $cor,$normal,'Data de vigência');
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+220, $linha, $cor,$normal,$rowPrincipal->CODIGO_CNS);

					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;						
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_EMPRESA.'  '.$rowPrincipal->NOME_EMPRESA);


				}else{
					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
				}

				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
			
			}
				
			echo json_encode($retorno);
	}
}
else if($_SESSION['codigoSmart'] =='4012')//CAF
{
	if($dadosInput['tipo'] =='frente'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				$cor = imagecolorallocate($imagem, 255, 255, 255);
				
				$normal  = "../../Site/assets/fonts/arial.ttf";
				$negrito = "../../Site/assets/fonts/arialbd.ttf";
				
				$linha  = 455;
				$coluna = 145;
				
				$pulaLinha = 20;
				$tamanhoCaracter = 20;
				
				$tamanhoLetra = 20;
				
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);				
				imagettftext($imagem, $tamanhoLetra, 0, 185, 518, $cor,$normal,$rowPrincipal->CODIGO_ASSOCIADO);
				imagettftext($imagem, $tamanhoLetra, 0, 755, 518, $cor,$normal,SqlToData($rowPrincipal->DATA_ADMISSAO));
				imagettftext($imagem, $tamanhoLetra, 0, 205, 558, $cor,$normal,$rowPrincipal->TIPO_ACOMODACAO);
				imagettftext($imagem, $tamanhoLetra, 0, 755, 558, $cor,$normal,SqlToData($rowPrincipal->DATA_VALIDADE_CARTEIRINHA));
				
				$linha  += $pulaLinha;		
				$linha  += $pulaLinha;							
				

				
				
				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
				
			}
			
			echo json_encode($retorno);
		
	}else if($dadosInput['tipo'] =='verso'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
				}
				
				if($rowPrincipal->ARQUIVO_BACK_APP_VERSO == 'IMAGEMVERSO.png'){
					
					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
					

					$cor = imagecolorallocate($imagem, 0, 0, 0 );
					
					$normal  = "../../Site/assets/fonts/arial.ttf";
					$negrito = "../../Site/assets/fonts/arialbd.ttf";
					
					$linha  = 220;
					$coluna = 115;
					
					$pulaLinha = 30;
					$tamanhoCaracter = 20;
					
					$tamanhoLetra = 18;
					
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);

					$linha  += $pulaLinha;		
					$linha  += $pulaLinha+5;		
					
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_NASCIMENTO));
					imagettftext($imagem, $tamanhoLetra, 0, $coluna+220, $linha, $cor,$normal,$rowPrincipal->CODIGO_CNS);

					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;		
					$linha  += $pulaLinha;						
					imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_EMPRESA.'  '.$rowPrincipal->NOME_EMPRESA);


				}else{
					$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
				}

				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
			
			}
				
			echo json_encode($retorno);
	}
}
else if($_SESSION['codigoSmart'] =='3419'){
	if($dadosInput['tipo'] =='frente'){
		$queryPrincipal =	"Select CODIGO_ASSOCIADO, NOME_ASSOCIADO, CODIGO_EMPRESA, NOME_EMPRESA, CODIGO_PLANO, DATA_NASCIMENTO, 	ARQUIVO_BACK_APP_FRENTE, ARQUIVO_BACK_APP_VERSO,
		DATA_ADMISSAO, NOME_PLANO, CADASTRO_ANS, CODIGO_CARENCIA, TIPO_ACOMODACAO, NOME_TITULAR,NUMERO_CPF,DATA_VALIDADE,NOME_CORRETORA from APP_VW_CABECALHO_CARTEIRINHA_V3
		WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	

		$resultQuery = jn_query($queryPrincipal); 


		if($rowPrincipal    = jn_fetch_object($resultQuery))
		{
		if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
		$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.PNG';
		}

		$imagem = imagecreatefrompng("../../../AliancaApp/servidorPersistencia_V3/auxCarteirinhas/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);

		ob_start(); 
		imagejpeg( $imagem, NULL, 100 ); 
		imagedestroy( $imagem ); 
		$i = ob_get_clean();

		$retorno['IMG'] = base64_encode($i);

		}
		echo json_encode($retorno);
	}
	if($dadosInput['tipo'] =='verso'){
				$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
						WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	

				$resultQuery = jn_query($queryPrincipal); 

				if($rowPrincipal    = jn_fetch_object($resultQuery)){
						if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
								$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.PNG';
				}
				$imagem = imagecreatefrompng("../../../AliancaApp/servidorPersistencia_V3/auxCarteirinhas/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);

				$cor = imagecolorallocate($imagem, 0, 0, 0 );

				$normal  = "../../../AliancaApp/servidorPersistencia_V3/auxCarteirinhas/arial.ttf";
				$negrito = "../../../AliancaApp/servidorPersistencia_V3/auxCarteirinhas/arialbd.ttf";

				$linha            = 40;
				$coluna           = 30;
				$pulaLinha        = 50;
				$tamanhoCaracter  = 20;
				//$linha  += $pulaLinha;
				$tamanhoLetra     = 27;


				$queryAuxiliar =	"Select ps1030.codigo_plano, ps1000.codigo_cns, ps1030.nome_plano_familiares, ps1030.nome_plano_empresas, ps1030.nome_plano_abreviado, ps1030.flag_plano_executivo, 
											ps1030.flag_tipo_plano, ps1030.flag_plano_regulamentado, ps1000.data_admissao, 
										CASE
											WHEN ps1030.codigo_tipo_abrangencia = '1' Then 'Nacional'
											When ps1030.codigo_tipo_abrangencia = '2' Then 'Grupo de Estados'
											When ps1030.codigo_tipo_abrangencia = '3' Then 'Estadual'
											When ps1030.codigo_tipo_abrangencia = '4' Then 'Grupo de Municípios'
											When ps1030.codigo_tipo_abrangencia = '5' Then 'Municipal'
											When ps1030.codigo_tipo_abrangencia = '6' Then 'Outros'
										End abrangencia, 
										ps1030.codigo_cadastro_ans, 
										case 
											when ps1030.codigo_tipo_acomodacao = '1' Then 'Individual' 
											when ps1030.codigo_tipo_acomodacao = '2' Then 'Coletivo' 
											when ps1030.codigo_tipo_acomodacao = '3' Then 'Sem Acomodacao'
										end codigo_tipo_acomodacao, 												 
										ps1030.tipo_pre_pos_pagto, ps1030.codigo_rede_indicada, 
										CASE
											When ps1030.tipo_contratacao_ans = '1' Then 'INDIVIDUALFAMILIAR' 
											When ps1030.tipo_contratacao_ans = '3' Then 'COLETIVOEMPRESARIAL' 
											When ps1030.tipo_contratacao_ans = '4' Then 'COLETIVOADESAO' 
											else 'OUTROS'
										end tipo_contratacao_ans, 
										Case 
											When ps1030.codigo_tipo_cobertura = '01' then 'Ambulatorial (A)'
											When ps1030.codigo_tipo_cobertura = '02' then 'Hospitalar (H)'
											When ps1030.codigo_tipo_cobertura = '03' then 'Odontológico (O)' 
											When ps1030.codigo_tipo_cobertura = '04' then 'Obstetrícia (OB)' 
											When ps1030.codigo_tipo_cobertura = '05' then 'A + H' 
											When ps1030.codigo_tipo_cobertura = '06' then 'A + H + O'
											When ps1030.codigo_tipo_cobertura = '07' then 'A + H + OB'
											When ps1030.codigo_tipo_cobertura = '08' then 'A + H + O + OB'
											When ps1030.codigo_tipo_cobertura = '09' then 'A + O' 
											When ps1030.codigo_tipo_cobertura = '10' then 'H + O' 
											When ps1030.codigo_tipo_cobertura = '11' then 'H + OB' 
											When ps1030.codigo_tipo_cobertura = '12' then 'H + OB + O' 
											When ps1030.codigo_tipo_cobertura = '13' then 'Outras'
											else ps1030.codigo_tipo_cobertura
										end codigo_tipo_cobertura,
										ps1030.codigo_plano_ans_spca, PS5012.NOME_REDE_INDICADA, COALESCE(PS1010.NOME_USUAL_FANTASIA, PS1010.NOME_EMPRESA) NOME_USUAL_FANTASIA
											From PS1000
										inner join ps1030 on (ps1000.codigo_plano = ps1030.codigo_plano)
										inner join ps1010 on (ps1000.codigo_empresa = ps1010.codigo_empresa) 
										LEFT OUTER JOIN PS5012 ON (PS1030.CODIGO_REDE_INDICADA = PS5012.CODIGO_REDE_INDICADA) 
										WHERE ps1000.CODIGO_ASSOCIADO = " . aspas($dados['CODIGO_USUARIO']);	
				
				$resultQueryAux = jn_query($queryAuxiliar); 
				$rowAux    = jn_fetch_object($resultQueryAux);

				$linha  			= 40;
				$coluna 			= 14;
				$pulaLinha 			= 23;
				$tamanhoCaracter 	= 20;
				$tamanhoLetra 		= 12;

				imagettftext($imagem, $tamanhoLetra+4, 0, $coluna, $linha, $cor,$negrito,'Matrícula');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+450, $linha, $cor,$negrito,'CAC - 2898-7000');
				$linha  += $pulaLinha;

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_ASSOCIADO);
				$linha  += $pulaLinha;

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nome do beneficiário');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);
				$linha  += $pulaLinha;

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Contratante');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$negrito,'Abrangência');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowAux->NOME_USUAL_FANTASIA);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$normal,$rowAux->ABRANGENCIA);
				$linha  += $pulaLinha;		

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Data de nascimento');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$negrito,'Data de admissao');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+440, $linha, $cor,$negrito,'Rede de atendimento');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_NASCIMENTO));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$normal,SqlToData($rowAux->DATA_ADMISSAO));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+450, $linha, $cor,$normal,$rowAux->NOME_REDE_INDICADA);
				$linha  += $pulaLinha;		

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Plano');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$negrito,'Cód.produto');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+440, $linha, $cor,$negrito,'Segmentacao');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_PLANO);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$normal,$rowAux->CODIGO_CADASTRO_ANS);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+440, $linha, $cor,$normal,$rowAux->CODIGO_TIPO_COBERTURA);
				$linha  += $pulaLinha;	

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Acomodação');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$negrito,'CNS');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+440, $linha, $cor,$negrito,'Tipo de contratação');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->TIPO_ACOMODACAO);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+240, $linha, $cor,$normal,$rowAux->CODIGO_CNS);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+440, $linha, $cor,$normal,$rowAux->TIPO_CONTRATACAO_ANS);
				$linha  += $pulaLinha;	

				$pulaLinha 			= 15;

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Carências');
				$linha  += $pulaLinha;

				for ($i = 0; $i<= 4;$i++)
				{

					$queryCarencia =	"select FIRST 1 skip " . $i . " substring(lower(RESULTADO_DESCRICAO_GRUPO) from 1 for 20) DESC, RESULTADO_DATA_CARENCIA from sp_retornacarencias(".aspas($dados['CODIGO_USUARIO']).")";	
					$resultCarencia = jn_query($queryCarencia); 

					$skyp = $i + 5;
					
					$queryCarenciaAux =	"select FIRST 1 skip " . $skyp . " substring(lower(RESULTADO_DESCRICAO_GRUPO) from 1 for 20) DESC, RESULTADO_DATA_CARENCIA from sp_retornacarencias(".aspas($dados['CODIGO_USUARIO']).")";	
					$resultCarenciaAux = jn_query($queryCarenciaAux); 

					if($rowCarencia    = jn_fetch_object($resultCarencia))
					{
						imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowCarencia->DESC);
						imagettftext($imagem, $tamanhoLetra, 0, $coluna+200, $linha, $cor,$normal,SqlToData($rowCarencia->RESULTADO_DATA_CARENCIA));
						
						$rowCarenciaAux    = jn_fetch_object($resultCarenciaAux);
						
						imagettftext($imagem, $tamanhoLetra, 0, $coluna+330, $linha, $cor,$normal,$rowCarenciaAux->DESC);
						imagettftext($imagem, $tamanhoLetra, 0, $coluna+480, $linha, $cor,$normal,SqlToData($rowCarenciaAux->RESULTADO_DATA_CARENCIA));
						$linha  += $pulaLinha;			
					}

				}

				
		ob_start(); 
		imagejpeg( $imagem, NULL, 100 ); 
		imagedestroy( $imagem ); 
		$i = ob_get_clean();

		$retorno['IMG'] = base64_encode($i);

		}
		echo json_encode($retorno);
	}
	


}
else if($_SESSION['codigoSmart'] =='4318')//SomarMaisSaude
{
	if($dadosInput['tipo'] =='frente'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);				
				
				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
				
			}
			
			echo json_encode($retorno);
		
	}else if($dadosInput['tipo'] =='verso'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"SELECT NOME_PLANO, NOME_ASSOCIADO, DATA_ADMISSAO, NUMERO_CPF, ARQUIVO_BACK_APP_VERSO FROM APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);			
				$cor = mt_rand(50,230);;
				
				$normal  = "../../Site/assets/fonts/arial.ttf";
				$negrito = "../../Site/assets/fonts/arialbd.ttf";
				
				$linha  = 120;
				$coluna = 195;
				$pulaLinha = 40;					
				$tamanhoLetra = 25;
				
				$numeroCpf = str_replace(".","", $rowPrincipal->NUMERO_CPF);
				$numeroCpf = str_replace("-","", $numeroCpf);
				$numeroCPF = Mask("###.###.###-##",$numeroCpf);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);												
				
				$linha  += $pulaLinha+50;	
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$numeroCPF);

				$linha  += $pulaLinha+75;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_PLANO);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+420, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_ADMISSAO));									

				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
			
			}
				
			echo json_encode($retorno);
	}
}
else
{
	if($dadosInput['tipo'] =='frente'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_FRENTE == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_FRENTE = 'IMAGEMFRENTE.png';
				}
				
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_FRENTE);
				
				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
				
			}
			
			echo json_encode($retorno);
		
	}else if($dadosInput['tipo'] =='verso'){
		
			$retorno['IMG'] = '';
			
			$queryPrincipal =	"Select * from APP_VW_CABECALHO_CARTEIRINHA_V3
								 WHERE CODIGO_ASSOCIADO = " . aspas($dadosInput['cod']);	
			
			$resultQuery = jn_query($queryPrincipal); 
			
			if($rowPrincipal    = jn_fetch_object($resultQuery)){
				if($rowPrincipal-> ARQUIVO_BACK_APP_VERSO == ''){
					$rowPrincipal->ARQUIVO_BACK_APP_VERSO = 'IMAGEMVERSO.png';
				}
				$imagem = imagecreatefrompng("../../Site/assets/img/".$rowPrincipal->ARQUIVO_BACK_APP_VERSO);
				

				$cor = imagecolorallocate($imagem, 0, 0, 0 );
				
				$normal  = "../../Site/assets/fonts/arial.ttf";
				$negrito = "../../Site/assets/fonts/arialbd.ttf";
				
				$linha  = 170;
				$coluna = 30;
				
				$pulaLinha = 50;
				$tamanhoCaracter = 20;
				

				$tamanhoLetra = 27;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Código');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+500, $linha, $cor,$negrito,'Adesão');
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+750, $linha, $cor,$negrito,'Dt. Nasc.');
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_ASSOCIADO);
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+500, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_ADMISSAO));
				imagettftext($imagem, $tamanhoLetra, 0, $coluna+750, $linha, $cor,$normal,SqlToData($rowPrincipal->DATA_NASCIMENTO));
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Nome');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->NOME_ASSOCIADO);
				$linha  += $pulaLinha;
				
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Empresa');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_EMPRESA.'  '.$rowPrincipal->NOME_EMPRESA);
				$linha  += $pulaLinha;		

				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$negrito,'Plano');
				$linha  += $pulaLinha;
				imagettftext($imagem, $tamanhoLetra, 0, $coluna, $linha, $cor,$normal,$rowPrincipal->CODIGO_PLANO.'  '.$rowPrincipal->NOME_PLANO);
				$linha  += $pulaLinha;	

							

				ob_start(); 
				imagejpeg( $imagem, NULL, 100 ); 
				imagedestroy( $imagem ); 
				$i = ob_get_clean();

				
				$retorno['IMG'] = base64_encode($i);
			
			}
				
			echo json_encode($retorno);
	}
	
}

if($dadosInput['tipo'] =='validaSolicCarteirinha'){
	$retorno['OCULTAR_SOLIC_CARTEIRINHA'] = retornaValorConfiguracao('OCULTAR_SOLIC_CARTEIRINHA');
	
	echo json_encode($retorno);
}

?>