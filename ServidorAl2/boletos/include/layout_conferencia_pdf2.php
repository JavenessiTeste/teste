<style type="text/css">
<!--	
	.cp {  
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000;
		font-weight:normal;
	};
	
	.ti {  
		font-family: Arial, Helvetica, sans-serif;
		font-size: 9px;
		color: #000;
		font-weight:normal;
	};
	
	.ld {  
		font-family: Arial, Helvetica, sans-serif;
		font-size: 15px;
		color: #000;
		font-weight:normal;
	};
	
	.ct {  
		font-family: Arial, Helvetica, sans-serif;
		font-size: 9px;
		color: #000033;
		font-weight:normal;
	};
	
	.cn {  
		font-family: Arial, Helvetica, sans-serif;
		font-size: 20px;
		color: #000000;
		font-weight:bold;
	};
	
	table, td
	{
		padding:0;
	}
	
-->
</style>
<page backtop="7mm" backbottom="7mm" backleft="11mm" backright="10mm" style="font-size: 10px; font-weight: normal; color:#000033; ">			
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=140 class=cp>
				<img src="<?php echo getLogoOperadora('../../Site/assets/img') ?>" alt="vidamax" width="150" height="47">
			</td>
		</tr>
		<tr><td colspan=5><img height=1 src=imagens/2.png width=666></td></tr>
	</table>
	<br>
	<table style="font-size: 12px; border-width: 1px; border-style: dashed;">			
		<tr>		
			<td>
				Prezado (a) Beneficiário (a),
			</td>
		</tr>			
		<tr>
			<td>
				<br>
				O boleto refere-se ao pagamento de Plano de Saúde Coletivo por Adesão.			
			</td>
		</tr>					
		<tr>
			<td>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				DETALHAMENTO DA MENSALIDADE POR BENEFICIÁRIO
			</td>
		</tr>							
		<tr><td>&nbsp;</td></tr>
		<?php
		
		$queryPs1021  = " SELECT PS1000.NOME_ASSOCIADO, PS1021.CODIGO_ASSOCIADO, PS1021.VALOR_CONVENIO AS VALOR, PS1000.TIPO_ASSOCIADO FROM PS1021 ";
		$queryPs1021 .= " INNER JOIN PS1000 ON (PS1021.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";		
		$queryPs1021 .= " WHERE NUMERO_REGISTRO_PS1020 = " . aspas($_GET['numeroRegistro']);
		$queryPs1021 .= " ORDER BY PS1021.NUMERO_REGISTRO DESC";
		$resPs1021 = jn_query($queryPs1021);
		
		$valorTotal = 0;
		while($rowPs1021 = jn_fetch_object($resPs1021)){
			$html = '';
		
			$queryEvento  = " Select A.CODIGO_EVENTO, A.TIPO_CALCULO, A.VALOR_FATOR, B.CODIGO_GRUPO_CONTRATO, COALESCE(C.FLAG_APRESENTAR_DESC_AL2, 'N') AS FLAG_APRESENTAR_DESC_AL2 from Ps1003 a ";
			$queryEvento .= " inner join PS1000 b on a.Codigo_Associado = b.Codigo_Associado ";
			$queryEvento .= " inner join PS1024 c on a.Codigo_Evento = c.Codigo_Evento ";
			$queryEvento .= " where a.Codigo_Associado = " . aspas($rowPs1021->CODIGO_ASSOCIADO);			
			$queryEvento .= " and a.Data_Inicio_Cobranca <= " . DataToSql($dadosboleto["data_vencimento"]);
			$queryEvento .= " and a.Data_Fim_Cobranca 	>= " . DataToSql($dadosboleto["data_vencimento"]);
			
			$resEvento = jn_query($queryEvento);
			
			$valorEvento = 0;
			
			while($rowEvento = jn_fetch_object($resEvento)){
				if ($rowEvento->FLAG_APRESENTAR_DESC_AL2 != 'S') {					
					if ($rowEvento->TIPO_CALCULO == 'V'){
						$valorEvento += $rowEvento->VALOR_FATOR;
					}else{
						$valorEvento += (($rowPs1021->VALOR * $rowEvento->VALOR_FATOR) / 100);
					}					
				}
				
			}
			
			$valor = ($rowPs1021->VALOR + $valorEvento);

			$valorTotal += $valor;
			
			echo '	<tr>
						<td>
							<b>' . $rowPs1021->NOME_ASSOCIADO . ' - ' . toMoeda($valor) .  '</b> 
						</td>
					</tr>';
		
			if($rowPs1021->TIPO_ASSOCIADO == 'T'){
				$query2  = " SELECT E.NOME_EVENTO, PS1029.VALOR_EVENTO AS VALOR_EVENTO, E.FLAG_APRESENTAR_DESC_AL2 FROM PS1029 ";
				$query2 .= " INNER JOIN PS1024 E ON (PS1029.CODIGO_EVENTO = E.CODIGO_EVENTO) ";
				$query2 .= " WHERE PS1029.NUMERO_REGISTRO_PS1020 = " . aspas($_GET['numeroRegistro']);			
				$query2 .= " AND E.FLAG_APRESENTAR_DESC_AL2 = 'S' ";
				$query2 .= " ORDER BY E.NOME_EVENTO ";
				
				$res2 = jn_query($query2);			
				$valorOutros = 0;
				while($row2 = jn_fetch_object($res2)){
						
					$html .= ' <tr>
									<td>
										' . ucwords(strtolower($row2->NOME_EVENTO)) . ' - ' . toMoeda($row2->VALOR_EVENTO) . '
									</td>
								</tr>';
					
				}
			}
	
			if($rowPs1021->TIPO_ASSOCIADO == 'T'){
				echo '
						<tr>							
							<td>								
								Mensalidade de Plano de Saúde: ' . toMoeda($valorTotal) . '
							</td>						
						</tr>
						' . $html;
			}
	
			echo '	<tr><td>&nbsp;</td></tr> ';
		}
		
		if($valorTotal == 0){
			echo '
						<tr>							
							<td>								
								Mensalidade de Plano de Saúde: R$ ' . $dadosboleto["valor_boleto"] . '
							</td>						
						</tr>
						';
		}

		?>
		
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
	<br>	
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=7 height=1><img src=imagens/1.png width=7 height=1></td>
			<td width=112><img src=imagens/1.png width=112 height=1></td>
			<td width=7><img src=imagens/1.png width=7 height=1></td>
			<td width=113><img src=imagens/1.png width=113 height=1></td>
			<td width=7><img src=imagens/1.png width=7 height=1></td>
			<td width=53><img src=imagens/1.png width=53 height=1></td>
			<td width=7><img src=imagens/1.png width=7 height=1></td>
			<td width=53><img src=imagens/1.png width=53 height=1></td>
			<td width=7><img src=imagens/1.png width=7 height=1></td>
			<td width=113><img src=imagens/1.png width=113 height=1></td>
			<td width=7><img src=imagens/1.png width=7 height=1></td>
			<td width=180><img src=imagens/1.png width=180 height=1></td>
		</tr>
		<tr>
			<!--
			<td width=5 rowspan=12 valign=top>
				<img src="<?php echo getLogoOperadora('../images') ?>" alt="vidamax" width="5" height="27">
			</td>
			-->
		</tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Data Processamento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct colspan=3>Agência/Código Cedente</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Espécie</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Controle</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Vencimento</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo date('d/m/Y'); ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp colspan=3>				
			</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["especie"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["data_vencimento"]?></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Valor documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(-) Desconto/Abatimento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(-) Outros</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(+) Multa</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(+) Outros acréscimos</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(=) Valor cobrado</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["valor_boleto"]?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right>&nbsp;</td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>Nosso Número</td>			
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=5 class=ct align=left>Número do Contrato</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Tipo Documento</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=3 class=cp></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp colspan=5 align=left></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=9 class=ct>Titular</td>			
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=9 class=cp><?php echo $dadosboleto["sacado"]?></td>			
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<br>	
	<table cellspacing=0 cellpadding=0 border=0>
		<tbody>
			<tr>
				<br>
				<td width=666 align="center"><b><u>BOLETO PARA SIMPLES CONFERÊNCIA</u></b></td>					
			</tr>
		</tbody>
	</table>
	<br>
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			<tr><td colspan=4 class=ct align=right></td></tr>
			<tr><td colspan=4><img src=imagens/6.png width=665 height=1></td></tr>
		</tbody>
	</table>
	<br>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
		<td width=140 class=cp></td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=65 class=bc valign=bottom align=center>
				<span align="center" style="font-size: 20px; font-weight: bold; font-family: Arial, Helvetica, sans-serif; color:#000;">
					<?php echo $dadosboleto["codigo_banco_com_dv"]; ?>
				</span>
			</td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=450 class=ld align=right valign=bottom>
				<span style="font-size: 15px; font-weight: normal; color:#000; font-weight: normal; font-family: Arial, Helvetica, sans-serif;">
					
				</span>
			</td>
		</tr>
		<tr><td colspan=5><img height=1 src=imagens/2.png width=666></td></tr>
	</table>
	<table cellspacing=0 cellpadding=0 border=0 height="2">
		<tr>
			<td width=7 height=1><img src=imagens/2.png width=7 height=1></td>
			<td width=100><img src=imagens/2.png width=100 height=1></td>
			<td width=7><img src=imagens/2.png width=7 height=1></td>
			<td width=74><img src=imagens/2.png width=74 height=1></td>
			<td width=7><img src=imagens/2.png width=7 height=1></td>
			<td width=73><img src=imagens/2.png width=73 height=1></td>
			<td width=7><img src=imagens/2.png width=7 height=1></td>
			<td width=55><img src=imagens/2.png width=55 height=1></td>
			<td width=7><img src=imagens/2.png width=7 height=1></td>
			<td width=35><img src=imagens/2.png width=35 height=1></td>
			<td width=7><img src=imagens/2.png width=7 height=1></td>
			<td width=100><img src=imagens/2.png width=100 height=1></td>
			<td width=7><img src=imagens/2.png width=7 height=1></td>
			<td width=180><img src=imagens/2.png width=180 height=1></td>
		</tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=11 class=ct>Local de pagamento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Vencimento</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=11 class=cp></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["data_vencimento"]?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=11 class=ct>Cedente</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Agência/Código Cedente</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=11  class=cp><?php echo $dadosboleto["cedente"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right>				
			</td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Data do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>Número do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Espécie doc</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Aceite</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Data processamento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Nosso número</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["data_documento"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td colspan=3 class=cp><?php echo $dadosboleto["numero_documento"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["data_processamento"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Carteira</td>
			<td><!--<img src=imagens/1.png width=1 height=13>--></td>
			<td class=ct></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Espécie</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>Quantidade</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Valor</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(=) Valor documento</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td class=campo height=12></td>
			<td><!--<img src=imagens/1.png width=1 height=12>--></td>
			<td class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["especie"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td colspan=3 class=cp><?php echo $dadosboleto["quantidade"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["valor_unitario"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["valor_boleto"]?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=7 height=26><img src=imagens/1.png width=1 height=26></td>
			<td width=472 rowspan=9 valign=top>
				<span class=ct>Instruções (Todas informações deste bloqueto são de exclusiva responsabilidade do beneficiário)</span><br>
				&nbsp;<br>&nbsp;<br>&nbsp;<br>
				&nbsp;<br>&nbsp;<br>&nbsp;<br>
				&nbsp;<br>&nbsp;<br>&nbsp;<br>
				&nbsp;<br>
				<?php echo $dadosboleto["instrucoes4"]?>
				<span class=cp></span>
			</td>
			<td width=7><img src=imagens/2.png width=1 height=26></td>
			<td width=180 class=ct>(-) Desconto / Abatimentos</td>
		</tr>
		<tr><td height=1><img src=imagens/2.png width=1 height=1></td><td><img src=imagens/2.png width=7 height=1></td><td><img src=imagens/2.png width=180 height=1></td></tr>
		<tr>
			<td height=26><img src=imagens/1.png width=1 height=26></td>
			<td><img src=imagens/2.png width=1 height=26></td>
			<td class=ct>(-) Outras deduções</td>
		</tr>
		<tr><td height=1><img src=imagens/1.png width=1 height=1></td><td><img src=imagens/2.png width=7 height=1></td><td><img src=imagens/2.png width=180 height=1></td></tr>
		<tr>
			<td height=26><img src=imagens/1.png width=1 height=26></td>
			<td><img src=imagens/2.png width=1 height=26></td>
			<td class=ct>(+) Mora / Multa</td>
		</tr>
		<tr><td height=1><img src=imagens/1.png width=1 height=1></td><td><img src=imagens/2.png width=7 height=1></td><td><img src=imagens/2.png width=180 height=1></td></tr>
		<tr>
			<td height=26><img src=imagens/1.png width=1 height=26></td>
			<td><img src=imagens/2.png width=1 height=26></td>
			<td class=ct>(+) Outros acréscimos</td>
		</tr>
		<tr><td height=1><img src=imagens/1.png width=1 height=1></td><td><img src=imagens/2.png width=7 height=1></td><td><img src=imagens/2.png width=180 height=1></td></tr>
		<tr>
			<td height=26><img src=imagens/1.png width=1 height=26></td>
			<td><img src=imagens/2.png width=1 height=26></td>
			<td class=ct>(=) Valor cobrado</td>
		</tr>
		<tr><td colspan=4 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Sacado</td>
			<td><img src=imagens/b.png width=1 height=1></td>
			<td><img src=imagens/b.png width=1 height=1></td>
		</tr>
		<tr>
			<td height=39><img src=imagens/1.png width=1 height=39></td>
			<td class=cp><?php echo $dadosboleto["sacado"] . '<br>' . $dadosboleto["endereco1"] . '<br>' . $dadosboleto["endereco2"]?></td>
			<td valign=bottom><img src=imagens/1.png width=1 height=13></td>
			<td valign=bottom><span class=ct></span></td>
		</tr>
		<tr><td colspan=4 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<br />
	<table style="border-width: 1px; border-style: dashed;">
		<tr>			
			<td width=90></td>
			<td width=155></td>
			<td width=155></td>
			<td width=155></td>
			<td width=90></td>
			
		</tr>
		<tr>
			<td>
				DATA
			</td>
			<td>
				DESCRIÇÃO
			</td>
			<td>
				NOME USUÁRIO
			</td>
			<td>
				COOPERADO
			</td>
			<td align=right>
				VALOR
			</td>
		</tr>
		<br>
		<br>
		<?php
		$query  = " SELECT PS1023.DATA_EVENTO, T.NOME_ASSOCIADO NOME_TITULAR, PS1000.NOME_ASSOCIADO, PS1023.DESCRICAO_HISTORICO, PS1023.VALOR_EVENTO FROM PS1023 ";
		$query .= " INNER JOIN PS1000 ON (PS1023.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
		$query .= " INNER JOIN PS1000 T ON (PS1000.CODIGO_TITULAR = T.CODIGO_ASSOCIADO) ";
		$query .= " WHERE NUMERO_REGISTRO_PS1020 = " . aspas($_GET['numeroRegistro']);
		$query .= " AND DETALHAMENTO_FATURA IS NOT NULL AND DETALHAMENTO_FATURA <> '' ";
		$query .= " ORDER BY PS1023.NUMERO_REGISTRO DESC";
		$res = jn_query($query);
		
		if(!$row = jn_fetch_object($res)){			
			$query  = " SELECT ";
			$query .= " 	T.NOME_ASSOCIADO NOME_TITULAR, PS1000.NOME_ASSOCIADO, ";
			$query .= " 	ESP0011.DATA_UTILIZACAO DATA_EVENTO, ESP0011.CODIGO_COOPERADO, ";
			$query .= " 	ESP0011.DESCRICAO_ATENDIMENTO, ESP0011.VALOR_GERADO AS VALOR_EVENTO";
			$query .= " FROM ESP0010 ";
			$query .= " INNER JOIN ESP0011 ON (ESP0010.NUMERO_REGISTRO = ESP0011.NUMERO_REGISTRO_ESP0010) ";			
			$query .= " INNER JOIN PS1000 ON (ESP0010.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ";
			$query .= " INNER JOIN PS1000 T ON (PS1000.CODIGO_TITULAR = T.CODIGO_ASSOCIADO) ";
			$query .= " WHERE T.CODIGO_ASSOCIADO = " . aspas($_SESSION['codigoIdentificacao']);
			$query .= " AND ESP0010.MES_ANO_REFERENCIA = " . aspas($dadosboleto["mes_ano_referencia"]);
			$query .= " ORDER BY ESP0011.NUMERO_REGISTRO ";
			//pr($query,true);
			$res = jn_query($query);
		}else{
			$res = jn_query($query);
		}
		
		while($row = jn_fetch_object($res)){
			$descProcedimento = $row->DESCRICAO_HISTORICO; 
			$descProcedimento = explode('/',$descProcedimento); 				
			$descProcedimento = substr($descProcedimento[2],0,25);
			
			if(!$descProcedimento){
				$descProcedimento = $row->DESCRICAO_ATENDIMENTO; 
			}
			
			echo '
					<tr>
						<td>
							' . SqlToData($row->DATA_EVENTO) . '
						</td>
						<td>								
							' . $descProcedimento . '
						</td>
						<td>
							' . $row->NOME_ASSOCIADO . '
						</td>
						<td>
							' . $row->CODIGO_COOPERADO . '
						</td>
						<td align="right">
							' . toMoeda($row->VALOR_EVENTO) . '
						</td>
					</tr>	
			';
		}
		?>			
	</table>		
</page> 