<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<HTML>
	<HEAD>
		<TITLE><?php echo $dadosboleto["identificacao"]; ?></TITLE>
		<META http-equiv=Content-Type content="text/html charset=iso-8859-1">
		<meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licença GPL" />
		<style type="text/css">
		<!--.cp {  font: bold 10px Arial; color: black}
		<!--.ti {  font: 9px Arial, Helvetica, sans-serif}
		<!--.ld { font: bold 15px Arial; color: #000000}
		<!--.ct { FONT: 9px "Arial Narrow"; COLOR: #000033}
		<!--.cn { FONT: 9px Arial; COLOR: black }
		<!--.bc { font: bold 20px Arial; color: #000000 }
		<!--.ld2 { font: bold 12px Arial; color: #000000 }
		-->
		</style> 
	</head>

	<BODY text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0>

		<table width=666 cellspacing=5 cellpadding=0 border=0>
			<tr><td width=41></TD></tr>
		</table>
		<table width=666 cellspacing=5 cellpadding=0 border=0 align=Default>
		  <tr>
			<td width=41><IMG SRC=<?php echo getLogoOperadora('../images') ?>></td>
			<td align=RIGHT width=150 class=ti>&nbsp;</td>
		  </tr>
		</table>
		<table width="666" style="font-size: 12px; border-width: 1px; border-style: dashed;">
			<tr>
				<td>
					Prezado (a) Beneficiário (a),
				</td>
			</tr>
			<!--
			<tr>
				<td>
					O pagamento até a data de vencimento é fundamental para a manutenção do seu convenio.
				</td>
			</tr>
			<tr>
				<td>
					Apos o vencimento pagável somente nas agencias do Banco Santander com:<br>
					Multa de 2% sobre o valor da mensalidade e, Mora de 0,033% por dia de atraso
				</td>
			</tr>
			<tr>
				<td>
					Informações:<br>
					Pagamentos, exclusões e alterações cadastrais (11) 3113-1717 - VIDAMAX.
				</td>
			</tr>
			-->
			<tr>
				<td>
					<br>
					O boleto refere-se ao pagamento de Plano de Saúde Coletivo por Adesão.			
				</td>
			</tr>
		</table>
		<br>
		<table cellspacing=0 cellpadding=0 width=666 border=0>
			<tr>
				<td class=ct width=666></td>
			</tr>
			<tbody>
				<tr>
					<td class=ct width=666><img height=1 src=imagens/6.png width=665 border=0></td>
				</tr>
			</tbody>
		</table>
		<BR>
		<table cellspacing=0 cellpadding=0 width=666 border=0>
			<tr>
				<td class=cp width=150> 
					<span class="campo">
						<IMG src="../images/logo_operadora.jpg" width="140" height="37" border=0>
					</span>
				</td>
				<td width=3 valign=bottom>
					<img height=22 src=imagens/3.png width=2 border=0>
				</td>
				<td class=cpt width=58 valign=bottom>
					<div align=center><font class=bc><?php echo $dadosboleto["codigo_banco_com_dv"]?></font></div>
				</td>
				<td width=3 valign=bottom>
					<img height=22 src=imagens/3.png width=2 border=0>
				</td>
				<td class=ld align=right width=453 valign=bottom>
					<span class=ld> 
						<span class="campotitulo">
						</span>
					</span>
				</td>
			</tr>
			<tbody>
				<tr>
					<td colspan=5><img height=2 src=imagens/2.png width=666 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=298 height=13>Cedente</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=126 height=13>Agência/Código do Cedente</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=34 height=13>Espécie</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=53 height=13>Quantidade</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=120 height=13>Nosso número</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=298 height=12> 
						<span class="campo"><?php echo $dadosboleto["identificacao"]; ?></span>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=126 height=12> 
						<span class="campo">
							
						</span>	
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=34 height=12>
						<span class="campo">
							R$
						</span> 
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=53 height=12>
						<span class="campo">							
						</span> 
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=120 height=12> 
						<span class="campo">						
						</span>
					</td>
				</tr>
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=298 height=1><img height=1 src=imagens/2.png width=298 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=126 height=1><img height=1 src=imagens/2.png width=126 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=34 height=1><img height=1 src=imagens/2.png width=34 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=53 height=1><img height=1 src=imagens/2.png width=53 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=120 height=1><img height=1 src=imagens/2.png width=120 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top colspan=3 height=13>Número do documento</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=132 height=13>CPF/CNPJ</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=134 height=13>Vencimento</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=180 height=13>Valor documento</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top colspan=3 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["numero_documento"]?>
						</span>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=132 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["cpf_cnpj"]?>
						</span>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=134 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["data_vencimento"]?>
						</span>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=180 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["valor_boleto"]?>
						</span>
					</td>
				</tr>	
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=72 height=1><img height=1 src=imagens/2.png width=72 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=132 height=1><img height=1 src=imagens/2.png width=132 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=134 height=1><img height=1 src=imagens/2.png width=134 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=113 height=13>(-) Desconto / Abatimentos</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=112 height=13>(-) Outras deduções</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=113 height=13>(+) Mora / Multa</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=113 height=13>(+) Outros acréscimos</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=180 height=13>(=) Valor cobrado</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=113 height=12></td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=112 height=12></td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=113 height=12></td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=113 height=12></td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=180 height=12></td>
				</tr>
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=112 height=1><img height=1 src=imagens/2.png width=112 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=659 height=13>Sacado</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=659 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["sacado"]?>
						</span>
					</td>
				</tr>
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=659 height=1><img height=1 src=imagens/2.png width=659 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<br>
					<td width=666 align="center"><b><u>BOLETO PARA SIMPLES CONFERÊNCIA</u></b></td>					
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 width=666 border=0>
			<tbody>
				<tr>
					<td width=7></td>
					<td  width=500 class=cp> 
						<br> 
					</td>
					<td width=159></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 width=666 border=0>
			<tr>
				<td class=ct width=666></td>
			</tr>
			<tbody>
				<tr>
					<td class=ct width=666><img height=1 src=imagens/6.png width=665 border=0></td>
				</tr>
			</tbody>
		</table>
		<br>
		<table cellspacing=0 cellpadding=0 width=666 border=0>
			<tr>
				<td class=cp width=200> 
					<!--
					<span class="campo"><IMG src="../images/logo_operadora.jpg" width="140" height="37" border=0>
					</span>
					-->
				</td>
				<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
				<td class=ld align=right width=453 valign=bottom>
					<span class=ld> 
						<span class="campotitulo">
						
						</span>
					</span>
				</td>
				<td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $dadosboleto["codigo_banco_com_dv"]?></font></div></td>
				<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
			</tr>
			<tbody>
				<tr>
					<td colspan=5><img height=2 src=imagens/2.png width=666 border=0></td>
				</tr>
			</tbody>
		</table>		
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=472 height=13>Local de pagamento</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=180 height=13>Vencimento</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=472 height=12> 
						<span class="campo">
						
						</span>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=180 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["data_vencimento"]?>
						</span>
					</td>
				</tr>
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=472 height=1><img height=1 src=imagens/2.png width=472 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=472 height=13>Cedente</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=180 height=13>Agência/Código cedente</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=472 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["identificacao"]; ?>
						</span>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=180 height=12> 
						<span class="campo">
						
						</span>
					</td>
				</tr>
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=472 height=1><img height=1 src=imagens/2.png width=472 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=113 height=13>Data do documento</td>
					<td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=153 height=13>N<u>o</u> documento</td>
					<td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=62 height=13>Espécie doc.</td>
					<td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=34 height=13>Aceite</td>
					<td class=ct valign=top width=7 height=13> 
						<img height=13 src=imagens/1.png width=1 border=0>
					</td>
					<td class=ct valign=top width=82 height=13>Data processamento</td>
					<td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=180 height=13>Nosso número</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=113 height=12>
						<div align=left> 
							<span class="campo">
							<?php echo $dadosboleto["data_documento"]?>
							</span>
						</div>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=153 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["numero_documento"]?>
						</span>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=62 height=12>
						<div align=left>
							<span class="campo">
							R$
							</span> 
						</div>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=34 height=12>
						<div align=left>
							<span class="campo">
							
							</span> 
						</div>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=82 height=12>
						<div align=left> 
						   <span class="campo">
						   <?php echo $dadosboleto["data_processamento"]?>
						   </span>
						</div>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=180 height=12> 
						<span class="campo">
						<?php echo $tmp; ?>
						</span>
					</td>
				</tr>
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=153 height=1><img height=1 src=imagens/2.png width=153 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=62 height=1><img height=1 src=imagens/2.png width=62 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=34 height=1><img height=1 src=imagens/2.png width=34 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=82 height=1><img height=1 src=imagens/2.png width=82 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=180 height=1> <img height=1 src=imagens/2.png width=180 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr> 
					<td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top COLSPAN="5" height=13> Carteira</td>
					<td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=53 height=13>Espécie</td>
					<td class=ct valign=top height=13 width=7> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=123 height=13>Quantidade</td>
					<td class=ct valign=top height=13 width=7> <img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=72 height=13> Valor Documento</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=180 height=13>(=) Valor documento</td>
				</tr>
				<tr> 
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td valign=top class=cp height=12 COLSPAN="5">
						<div align=left></div>    
						<div align=left> 
							<span class="campo">
							
							</span>
						</div>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=53>
						<div align=left>
							<span class="campo">
							R$
							</span> 
						</div>
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=123>
						<span class="campo">
						
						</span> 
					</td>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top  width=72> 
						<span class="campo">
						<?php echo $dadosboleto["valor_unitario"]?>
						</span>
					</td>
					<td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top align=right width=180 height=12> 
						<span class="campo">
						<?php echo $dadosboleto["valor_boleto"]?>
						</span>
					</td>
				</tr>
				<tr>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=75 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=31 height=1><img height=1 src=imagens/2.png width=31 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=83 height=1><img height=1 src=imagens/2.png width=83 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=53 height=1><img height=1 src=imagens/2.png width=53 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=123 height=1><img height=1 src=imagens/2.png width=123 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=72 height=1><img height=1 src=imagens/2.png width=72 border=0></td>
					<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
				</tr>
			</tbody> 
		</table>
		<table cellspacing=0 cellpadding=0 width=666 border=0>
			<tbody>
				<tr>
					<td align=right width=10>
						<table cellspacing=0 cellpadding=0 border=0 align=left>
							<tbody> 
								<tr> 
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr> 
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr> 
									<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td valign=top width=468 rowspan=5>
						<font class=ct>Instruções (Todas informações deste bloqueto são de exclusiva responsabilidade do cedente)</font>
						<br><br>						
					</td>
					<td align=right width=188>
						<table cellspacing=0 cellpadding=0 border=0>
							<tbody>
								<tr>
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
									<td class=ct valign=top width=180 height=13>(-) Desconto / Abatimentos</td>
								</tr>
								<tr> 
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
									<td class=cp valign=top align=right width=180 height=12></td>
								</tr>
								<tr> 
									<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
									<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td align=right width=10> 
						<table cellspacing=0 cellpadding=0 border=0 align=left>
							<tbody>
								<tr>
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr>
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr>
									<td valign=top width=7 height=1> 
										<img height=1 src=imagens/2.png width=1 border=0>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td align=right width=188>
						<table cellspacing=0 cellpadding=0 border=0>
							<tbody>
								<tr>
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
									<td class=ct valign=top width=180 height=13>(-) Outras deduções</td>
								</tr>
								<tr>
									<td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
									<td class=cp valign=top align=right width=180 height=12></td>
								</tr>
								<tr>
									<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
									<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td align=right width=10> 
						<table cellspacing=0 cellpadding=0 border=0 align=left>
							<tbody>
								<tr>
									<td class=ct valign=top width=7 height=13> 
										<img height=13 src=imagens/1.png width=1 border=0>
									</td>
								</tr>
								<tr>
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr>
									<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td align=right width=188> 
						<table cellspacing=0 cellpadding=0 border=0>
							<tbody>
								<tr>
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
									<td class=ct valign=top width=180 height=13>(+) Mora / Multa</td>
								</tr>
								<tr>
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
									<td class=cp valign=top align=right width=180 height=12></td>
								</tr>
								<tr> 
									<td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
									<td valign=top width=180 height=1> 
										<img height=1 src=imagens/2.png width=180 border=0>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td align=right width=10>
						<table cellspacing=0 cellpadding=0 border=0 align=left>
							<tbody>
								<tr> 
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr>
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr>
									<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td align=right width=188> 
						<table cellspacing=0 cellpadding=0 border=0>
							<tbody>
								<tr> 
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
									<td class=ct valign=top width=180 height=13>(+) Outros acréscimos</td>
								</tr>
								<tr> 
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
									<td class=cp valign=top align=right width=180 height=12></td>
								</tr>
								<tr>
									<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
									<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td align=right width=10>
						<table cellspacing=0 cellpadding=0 border=0 align=left>
							<tbody>
								<tr>
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
								</tr>
								<tr>
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td align=right width=188>
						<table cellspacing=0 cellpadding=0 border=0>
							<tbody>
								<tr>
									<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
									<td class=ct valign=top width=180 height=13>(=) Valor cobrado</td>
								</tr>
								<tr>
									<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
									<td class=cp valign=top align=right width=180 height=12></td>
								</tr>
							</tbody> 
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 width=666 border=0>
			<tbody>
				<tr>
					<td valign=top width=666 height=1><img height=1 src=imagens/2.png width=666 border=0></td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=659 height=13>Sacado</td>
				</tr>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=659 height=12>
						<span class="campo">
						<?php echo $dadosboleto["sacado"]?>
						</span> 
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=659 height=12>
						<span class="campo">
						<?php echo $dadosboleto["endereco1"]?>
						</span> 
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 border=0>
			<tbody>
				<tr>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=cp valign=top width=472 height=13> 
						<span class="campo">
						<?php echo $dadosboleto["endereco2"]?>
						</span>
					</td>
					<td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
					<td class=ct valign=top width=180 height=13>Cód. baixa</td>
				</tr>
				<tr>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=472 height=1><img height=1 src=imagens/2.png width=472 border=0></td>
					<td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
					<td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
				</tr>
			</tbody>
		</table>
		<table width="666" class=cp style="border-width: 1px; border-style: dashed;">
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
				<td>
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
	</BODY>
</HTML>