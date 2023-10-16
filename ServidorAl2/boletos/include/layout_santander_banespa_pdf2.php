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
<page backtop="7mm" backbottom="7mm" backleft="11mm" backright="10mm" style="font-size: 9px; font-weight: normal; color:#000033;">
	<table width="704" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td valign="top" class="cp">
				<div align="center">
					Instru&ccedil;&otilde;es de Impress&atilde;o
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top" class="cp">
				<div align="left" class="cabecalho">
					<ul>
						<li>
						<?php echo $dadosboleto["mensagemTopUm"]; ?>
						</li>
						<br>
						<li>
						<?php echo $dadosboleto["mensagemTopDois"]; ?>							
						</li>
						<br>
						<li>
						<?php echo $dadosboleto["mensagemTopTres"]; ?>													
						</li>
						<br>
						<li>Caso n&atilde;o apare&ccedil;a o c&oacute;digo de barras no final, clique em F5 para atualizar esta tela.
						</li>
						<br>
						<li>
						Caso tenha problemas ao imprimir, copie a sequencia num&eacute;rica abaixo e pague no caixa eletr&ocirc;nico ou no internet banking:<br><br>
						<span style="font-size: 13px; font-weight: normal; color:#000033; padding:10px;">
						<div>Linha Digit&aacute;vel: &nbsp;<?php echo $dadosboleto["linha_digitavel"]; ?></div>
						<div>Valor: &nbsp;&nbsp;R$ <?php echo $dadosboleto["valor_boleto2"]; ?></div>
						</span>
						</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
	<br />
	<br />
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=140 class=cp><img src="imagens/logosantander.jpg" alt="santender" width="150" height="27"></td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=65 valign=bottom align=left>
				<span align="center" style="font-size: 20px; font-weight: bold; font-family: Arial, Helvetica, sans-serif; color:#000;">
					<?php echo $dadosboleto["codigo_banco_com_dv"]; ?>
				</span>
			</td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=450 class=ld align=right valign=bottom>
				<span style="font-size: 15px; font-weight: normal; font-weight: normal; font-family: Helvetica, Arial, sans-serif; color:#000;">
					<?php echo $dadosboleto["linha_digitavel"]; ?>
				</span>
			</td>
		</tr>
		<tr><td colspan=5><img height=1 src=imagens/2.png width=666></td></tr>
	</table>
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
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=7 class=ct>Benefici&aacute;rio</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Ag&ecirc;ncia/C&oacute;digo do Cedente</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Vencimento</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=7 class=cp><?php echo $dadosboleto["cedente"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp>
				<?php
					$tmp2 = $dadosboleto["codigo_cliente"];
					$tmp2 = substr($tmp2,0,strlen($tmp2)-1).'-'.substr($tmp2,strlen($tmp2)-1,1);
				?>
				<?php
					echo $dadosboleto["ponto_venda"]." <img src='imagens/b.png' width=10 height=1> ".$tmp2;
				?>
			</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["data_vencimento2"]; ?></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>CPF/CNPJ</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>N&uacute;mero do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Esp&eacute;cie</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Quantidade</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Valor</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Valor documento</td>
		</tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["cpf_cnpj"]; ?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["numero_documento"]; ?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["especie"]; ?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["quantidade"]; ?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["valor_unitario"]; ?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp align=right><?php echo $dadosboleto["valor_boleto2"]; ?></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(-) Desconto / Abatimentos</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(-) Outras dedu&ccedil;&otilde;es</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>(+) Mora / Multa</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(+) Outros acr&eacute;scimos</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(=) Valor cobrado</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td colspan=3 class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right>&nbsp;</td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=9 class=ct>Pagador</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Nosso n&uacute;mero</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=9 class=cp><?php echo $dadosboleto["sacado"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["nosso_numero"]; ?></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=7 height=12 class=ct>&nbsp;</td>
			<td width=558 class=ct>Demonstrativo</td>
			<td width=7 class=ct>&nbsp;</td>
			<td width=94 class=ct>Autentica&ccedil;&atilde;o mec&acirc;nica</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class=cp><?php echo $dadosboleto["demonstrativo1"] . '<br>' . isset($dadosboleto["demonstrativo2"]) . '<br>' . $dadosboleto["demonstrativo3"]; ?><br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td colspan=4 class=ct align=right>Corte na linha pontilhada</td></tr>
		<tr><td colspan=4><img src=imagens/6.png width=665 height=1></td></tr>
	</table>
	&nbsp;<br>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=140 class=cp><img src="imagens/logosantander.jpg" alt="santender" width="150" height="27"></td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=65 class=bc valign=bottom align=center>
				<span align="center" style="font-size: 20px; font-weight: bold; font-family: Arial, Helvetica, sans-serif; color:#000;">
					<?php echo $dadosboleto["codigo_banco_com_dv"]; ?>
				</span>
			</td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=450 class=ld align=right valign=bottom>
				<span style="font-size: 15px; font-weight: normal; color:#000; font-weight: normal; font-family: Arial, Helvetica, sans-serif;">
					<?php echo $dadosboleto["linha_digitavel"]; ?>
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
			<td colspan=11 class=cp>Pag&aacute;vel em qualquer Banco at&eacute; o vencimento.</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["data_vencimento2"]; ?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=11 class=ct>Benefici&aacute;rio</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Ag&ecirc;ncia/C&oacute;digo Cedente</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=11  class=cp><?php echo $dadosboleto["cedente"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right>
				<?php
					$tmp2 = $dadosboleto["codigo_cliente"];
					$tmp2 = substr($tmp2,0,strlen($tmp2)-1).'-'.substr($tmp2,strlen($tmp2)-1,1);
				?>	
				<?php
					echo $dadosboleto["ponto_venda"]." <img src='imagens/b.png' width=10 height=1> ".$tmp2;
				?>
			</td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Data do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>N&uacute;mero do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Esp&eacute;cie doc.</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Aceite</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Data processamento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Nosso n&uacute;mero</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["data_documento"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td colspan=3 class=cp><?php echo $dadosboleto["numero_documento"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["especie_doc"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["aceite"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["data_processamento"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["nosso_numero"]; ?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Carteira</td>
			<td><!--<img src=imagens/1.png width=1 height=13>--></td>
			<td class=ct></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Esp&eacute;cie</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>Quantidade</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Valor</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(=) Valor documento</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td class=campo height=12><?php echo $dadosboleto["carteira_descricao"]; ?></td>
			<td><!--<img src=imagens/1.png width=1 height=12>--></td>
			<td class=cp>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["especie"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td colspan=3 class=cp><?php echo $dadosboleto["quantidade"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["valor_unitario"]; ?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["valor_boleto2"]; ?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=7 height=26><img src=imagens/1.png width=1 height=26></td>
			<td width=472 rowspan=9 valign=top>
				<span class=ct>Instru&ccedil;&otilde;es (Todas informa&ccedil;&otilde;es deste bloqueto s&atilde;o de exclusiva responsabilidade do benefici&aacute;rio)</span><br>
				&nbsp;<br>
				<span class=cp><?php echo $dadosboleto["instrucoes1"] . '<br>' . isset($dadosboleto["instrucoes2"]) . '<br>' . $dadosboleto["instrucoes3"] . '<br>' . $dadosboleto["instrucoes4"]; ?></span>
			</td>
			<td width=7><img src=imagens/2.png width=1 height=26></td>
			<td width=180 class=ct>(-) Desconto / Abatimentos</td>
		</tr>
		<tr><td height=1><img src=imagens/2.png width=1 height=1></td><td><img src=imagens/2.png width=7 height=1></td><td><img src=imagens/2.png width=180 height=1></td></tr>
		<tr>
			<td height=26><img src=imagens/1.png width=1 height=26></td>
			<td><img src=imagens/2.png width=1 height=26></td>
			<td class=ct>(-) Outras dedu&ccedil;&otilde;es</td>
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
			<td class=ct>(+) Outros acr&eacute;scimos</td>
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
			<td class=ct>Pagador</td>
			<td><img src=imagens/b.png width=1 height=1></td>
			<td class=ct>CNPJ/CPF</td>			
		</tr>
		<tr>
			<td height=39><img src=imagens/1.png width=1 height=39></td>
			<td class=cp><?php echo $dadosboleto["sacado"] . '<br>' . $dadosboleto["endereco1"] . '<br>' . $dadosboleto["endereco2"]; ?></td>
			<td></td>
			<td><span class=ct><?php echo $dadosboleto["cpf_cnpj"]; ?></span></td>
		</tr>
		<tr><td colspan=4 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<br />
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=333 class=ct>Pagador/Avalista</td>
			<td width=333 class=ct align=right>Autentica&ccedil;&atilde;o mec&acirc;nica - <span class=cp>Ficha de Compensa&ccedil;&atilde;o</span></td>
		</tr>
		<tr><td height=50 colspan=2><?php fbarcode($dadosboleto["codigo_barras"]); ?></td></tr>
		<tr><td colspan=2 class=ct align=right>Corte na linha pontilhada</td></tr>
		<tr><td colspan=2 height=1><img src=imagens/6.png width=665 height=1></td></tr>
	</table>	
</page> 