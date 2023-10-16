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
<img src="../../Site/assets/img/topoBoleto.png" width="666">
	<br>
	<br />
	
	<?php	
		$queryAtraso  = ' SELECT PS1020.CODIGO_ASSOCIADO, SUM(COALESCE(PS1020.DATA_PAGAMENTO,CURRENT_TIMESTAMP) - PS1020.DATA_VENCIMENTO) QUANTIDADE ';
        $queryAtraso .= ' FROM PS1020 ';
        $queryAtraso .= ' INNER JOIN PS1000 ON (PS1000.CODIGO_ASSOCIADO = PS1020.CODIGO_ASSOCIADO) ';
        $queryAtraso .= ' WHERE ';
        $queryAtraso .= ' 	PS1000.FLAG_PLANOFAMILIAR = "S" ';
        $queryAtraso .= ' 	AND PS1020.CODIGO_ASSOCIADO = ' . aspas($dadosboleto["codigo_associado"]);
        $queryAtraso .= '   AND PS1020.DATA_VENCIMENTO >= "01.01.2020" ';
        $queryAtraso .= '   AND ((PS1020.DATA_PAGAMENTO > PS1020.DATA_VENCIMENTO) OR (PS1020.DATA_PAGAMENTO IS NULL)) ';
        $queryAtraso .= '   AND PS1020.DATA_VENCIMENTO < CURRENT_TIMESTAMP ';
        $queryAtraso .= '   AND (PS1020.DATA_PAGAMENTO IS NULL) ';
        $queryAtraso .= '   AND (PS1020.DATA_CANCELAMENTO IS NULL) ';
        $queryAtraso .= ' GROUP BY PS1020.CODIGO_ASSOCIADO ';
        $queryAtraso .= ' ORDER BY PS1020.CODIGO_ASSOCIADO ';
		$resAtraso = jn_query($queryAtraso);
		$rowAtraso = jn_fetch_object($resAtraso);
		
		if($rowAtraso->QUANTIDADE > 5){
			$dadosboleto["quantidade"] = $rowAtraso->QUANTIDADE;					
		}else{
			$dadosboleto["quantidade"] = 000;								
		}
		
		$queryAtraso  = ' SELECT FIRST 1 DATA_VENCIMENTO ';
        $queryAtraso .= ' FROM PS1020 ';
        $queryAtraso .= ' WHERE ';
        $queryAtraso .= ' 	PS1020.CODIGO_ASSOCIADO = ' . aspas($dadosboleto["codigo_associado"]);
        $queryAtraso .= '   AND PS1020.DATA_VENCIMENTO >= "01.01.2020" ';
        $queryAtraso .= '   AND PS1020.DATA_VENCIMENTO < CURRENT_TIMESTAMP ';
        $queryAtraso .= '   AND PS1020.DATA_PAGAMENTO IS NULL ';
        $queryAtraso .= '   AND (PS1020.DATA_CANCELAMENTO IS NULL) ';
        $queryAtraso .= ' ORDER BY PS1020.DATA_VENCIMENTO ';
		$resAtraso = jn_query($queryAtraso);
		$rowAtraso = jn_fetch_object($resAtraso);
		if($rowAtraso->DATA_VENCIMENTO != ''){			
			$dadosboleto["data_venc_atraso"] = $rowAtraso->DATA_VENCIMENTO;	
		}else{
			$dadosboleto["data_venc_atraso"] = 000;
		}
		
		$queryAssocFat  = ' Select Ps1021.*, Ps1020.Data_Vencimento, Ps1000.Nome_Associado, Ps1000.Data_Nascimento, Ps1030.Nome_Plano_Abreviado from ps1020 ';
		$queryAssocFat .= ' inner join Ps1021 on (Ps1021.Numero_Registro_Ps1020 = Ps1020.Numero_Registro)';
		$queryAssocFat .= ' inner join Ps1000 on (Ps1000.Codigo_Associado = Ps1021.Codigo_Associado) ';
		$queryAssocFat .= ' inner join Ps1030 on (Ps1030.Codigo_Plano = Ps1000.Codigo_Plano) ';
		$queryAssocFat .= ' where Ps1020.Numero_Registro = ' . aspas($dadosboleto["numero_documento"]);
		$queryAssocFat .= '  and Data_Vencimento = ' . dataToSql($dadosboleto["data_vencimento"]);
		$queryAssocFat .= '  and Ps1021.Codigo_Plano is Not Null ';
		$resAssocFat = jn_query($queryAssocFat);
		
		$ArrAssoc = Array();
		$i = 0;
		while($rowAssocFat = jn_fetch_object($resAssocFat)){
			$ArrAssoc[$i]['NOME_ASSOCIADO'] = $rowAssocFat->NOME_ASSOCIADO . '&nbsp;&nbsp;&nbsp;';
			$ArrAssoc[$i]['NOME_PLANO'] = $rowAssocFat->NOME_PLANO_ABREVIADO . '&nbsp;&nbsp;&nbsp;';
			$ArrAssoc[$i]['QUANTIDADE'] = 1 . '&nbsp;&nbsp;&nbsp;';
			$ArrAssoc[$i]['IDADE'] = calcularIdade($rowAssocFat->DATA_NASCIMENTO) . '&nbsp;&nbsp;&nbsp;';
			$ArrAssoc[$i]['VALOR'] = ($rowAssocFat->VALOR_CONVENIO + $rowAssocFat->VALOR_CORRECAO);
			$i++;
		}
		
		
		
		$queryEventos  = ' Select Ps1024.*, Ps1029.* from Ps1029 ';
		$queryEventos .= ' inner join Ps1024 on(Ps1024.Codigo_Evento = Ps1029.Codigo_Evento) ';
		$queryEventos .= ' where Ps1029.Numero_Registro_Ps1020 = ' . aspas($dadosboleto["numero_documento"]);
		$queryEventos .= ' and Ps1029.Codigo_Plano is Null';
		$queryEventos .= ' order by Ps1029.Codigo_Evento ';
		
		$resEventos = jn_query($queryEventos);
		
		$ArrEventos = Array();
		$i = 0;
		while($rowEventos = jn_fetch_object($resEventos)){
			$ArrEventos[$i]['DESCRICAO_EVENTO'] = 'EVENTO ADICIONAL COD ' . $rowEventos->CODIGO_EVENTO . '&nbsp;&nbsp;&nbsp;';
			$ArrEventos[$i]['NOME_EVENTO'] = $rowEventos->NOME_EVENTO . '&nbsp;&nbsp;&nbsp;';
			$ArrEventos[$i]['QUANTIDADE'] = $rowEventos->QUANTIDADE . '&nbsp;&nbsp;&nbsp;';
			$ArrEventos[$i]['IDADE'] = '' . '&nbsp;&nbsp;&nbsp;';
			$ArrEventos[$i]['VALOR'] = ($rowEventos->QUANTIDADE * $rowEventos->VALOR_EVENTO);
			$i++;
		}
				
		
		echo '	Operadora: ' . $dadosboleto["nome_operadora"];
		echo '	<br />';
		echo '	Atrasos acumulados nos últimos 12 (doze) meses: ' . $dadosboleto["quantidade"];
		echo '	<br />';
		echo '	Existem parcelas em aberto referente ao(s) vencimento(s) abaixo : ' . $dadosboleto["data_venc_atraso"];
		echo '	<br />';
		echo '	<br />';
		echo '	<table >
					<tr>
						<td width="30%">
							Beneficiário
						</td>
						<td width="30%">
							Plano/Event
						</td>
						<td width="15%">
							Qtde &nbsp;&nbsp;&nbsp;
						</td>
						<td width="10%">
							Idade &nbsp;&nbsp;&nbsp;
						</td>
						<td width="15%">
							Valor
						</td>
					</tr>';
				
		foreach($ArrAssoc as $item){
			echo '	
					<tr>
						<td width="30%">
							' . $item['NOME_ASSOCIADO'] . '
						</td>
						<td width="30%">
							' . $item['NOME_PLANO'] . '
						</td>
						<td width="15%">
							' . $item['QUANTIDADE'] . '
						</td>
						<td width="10%">
							' . $item['IDADE'] . '
						</td>
						<td width="15%">
							' . toMoeda($item['VALOR']) . '
						</td>
					</tr>';
		}
		
		foreach($ArrEventos as $item){
			echo '	
					<tr>
						<td width="30%">
							' . $item['DESCRICAO_EVENTO'] . '
						</td>
						<td width="30%">
							' . $item['NOME_EVENTO'] . '
						</td>
						<td width="15%">
							' . $item['QUANTIDADE'] . '
						</td>
						<td width="10%">
							' . $item['IDADE'] . '
						</td>
						<td width="15%">
							' . toMoeda($item['VALOR']) . '
						</td>
					</tr>';
		}
		echo ' </table>';
		echo '	<br />';
		
		echo '	VALOR TOTAL DA MENSALIDADE : ' . 'R$ ' . $dadosboleto["valor_boleto"];		
	?>

	<br />
	<br />
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=140 class=cp><img src="imagens/logoitau.jpg" alt="itau" width="150" height="27"></td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=65 valign=bottom align=left>
				<span align="center" style="font-size: 20px; font-weight: bold; font-family: Arial, Helvetica, sans-serif; color:#000;">
					<?php echo $dadosboleto["codigo_banco_com_dv"]; ?>
				</span>
			</td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=450 class=ld align=right valign=bottom>
				<span style="font-size: 15px; font-weight: normal; font-weight: normal; font-family: Helvetica, Arial, sans-serif; color:#000;">
					<?php echo $dadosboleto["linha_digitavel"]?>
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
			<td colspan=7 class=ct>Cedente</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Agência/Código do Cedente</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Vencimento</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=7 class=cp><?php echo $dadosboleto["cedente"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["agencia_codigo"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["data_vencimento"]?></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>CPF/CNPJ</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Número do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Espécie</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Quantidade</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Valor</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Valor documento</td>
		</tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["cpf_cnpj"]?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["numero_documento"]?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["especie"]?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["quantidade"]?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp><?php echo $dadosboleto["valor_unitario"]?></td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=cp align=right><?php echo $dadosboleto["valor_boleto"]?></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(-) Desconto / Abatimentos</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(-) Outras deduções</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>(+) Mora / Multa</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>(+) Outros acréscimos</td>
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
			<td colspan=9 class=ct>Sacado</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Nosso número</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=9 class=cp><?php echo $dadosboleto["sacado"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["nosso_numero"]?></td>
		</tr>
		<tr><td colspan=12 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=7 height=12 class=ct>&nbsp;</td>
			<td width=558 class=ct>Demonstrativo</td>
			<td width=7 class=ct>&nbsp;</td>
			<td width=94 class=ct>Autenticação mecânica</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class=cp><?php echo $dadosboleto["demonstrativo1"] . '<br>' . $dadosboleto["demonstrativo2"] . '<br>' . $dadosboleto["demonstrativo3"]?><br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td colspan=4 class=ct align=right>Corte na linha pontilhada</td></tr>
		<tr><td colspan=4><img src=imagens/6.png width=665 height=1></td></tr>
	</table>
	&nbsp;<br>
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=140 class=cp><img src="imagens/logoitau.jpg" alt="itau" width="150" height="27"></td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=65 class=bc valign=bottom align=center>
				<span align="center" style="font-size: 20px; font-weight: bold; font-family: Arial, Helvetica, sans-serif; color:#000;">
					<?php echo $dadosboleto["codigo_banco_com_dv"]; ?>
				</span>
			</td>
			<td width=3 valign=bottom><img height=22 src=imagens/3.png width=2></td>
			<td width=450 class=ld align=right valign=bottom>
				<span style="font-size: 15px; font-weight: normal; color:#000; font-weight: normal; font-family: Arial, Helvetica, sans-serif;">
					<?php echo $dadosboleto["linha_digitavel"]?>
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
			<td colspan=11 class=cp>Pagável em qualquer Banco até o vencimento.</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["data_vencimento"]?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td colspan=11 class=ct>Cedente</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Agência/Código cedente</td>
		</tr>
		<tr>
			<td height=12><img src=imagens/1.png width=1 height=12></td>
			<td colspan=11  class=cp><?php echo $dadosboleto["cedente"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["agencia_codigo"]?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Data do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td colspan=3 class=ct>Número do documento</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Espécie doc.</td>
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
			<td class=cp><?php echo $dadosboleto["especie_doc"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["aceite"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["data_processamento"]?></td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp align=right><?php echo $dadosboleto["nosso_numero"]?></td>
		</tr>
		<tr><td colspan=14 height=1><img src=imagens/2.png width=666 height=1></td></tr>
		<tr>
			<td height=13><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Uso do banco</td>
			<td><img src=imagens/1.png width=1 height=13></td>
			<td class=ct>Carteira</td>
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
			<td class=cp height=12>&nbsp;</td>
			<td><img src=imagens/1.png width=1 height=12></td>
			<td class=cp><?php echo $dadosboleto["carteira"]?></td>
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
				<span class=ct>Instruções (Texto de responsabilidade do cedente)</span><br>
				&nbsp;<br>
				<span class=cp><?php echo $dadosboleto["instrucoes1"] . '<br>' . $dadosboleto["instrucoes2"] . '<br>' . $dadosboleto["instrucoes3"] . '<br>' . $dadosboleto["instrucoes4"]?></span>
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
			<td valign=bottom><span class=ct>Cód. baixa</span></td>
		</tr>
		<tr><td colspan=4 height=1><img src=imagens/2.png width=666 height=1></td></tr>
	</table>
	<br />
	<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td width=333 class=ct>Sacador/Avalista</td>
			<td width=333 class=ct align=right>Autenticação mecânica - <span class=cp>Ficha de Compensação</span></td>
		</tr>
		<tr><td height=50 colspan=2><?php fbarcode($dadosboleto["codigo_barras"]); ?></td></tr>
		<tr><td colspan=2 class=ct align=right>Corte na linha pontilhada</td></tr>
		<tr><td colspan=2 height=1><img src=imagens/6.png width=665 height=1></td></tr>
	</table>	
</page> 
