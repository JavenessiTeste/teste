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
				<img src="<?php echo getLogoOperadora('../images') ?>" alt="vidamax" width="150" height="47">
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
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
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
		$query .= " WHERE NUMERO_REGISTRO_PS1020 = " . aspa($_GET['numeroRegistro']);
		$query .= " ORDER BY PS1023.NUMERO_REGISTRO DESC";
		$res = jn_query($query);
		
		while($row = jn_fetch_object($res)){
			$descProcedimento = $row->DESCRICAO_HISTORICO; 
			$descProcedimento = explode('/',$descProcedimento); 				
			$descProcedimento = substr($descProcedimento[2],0,25);
			
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
							' . $row->NOME_TITULAR . '
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