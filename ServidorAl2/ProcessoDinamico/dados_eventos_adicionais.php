<?php
require('../lib/base.php');

require('../private/autentica.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);


$mesAno = $_POST['MESANO'];

$queryEventos  = ' SELECT DESCRICAO_GRUPO_CONTRATO, PS1023.CODIGO_ASSOCIADO, NOME_ASSOCIADO, MES_ANO_VENCIMENTO, VALOR_EVENTO, DESCRICAO_HISTORICO, DETALHAMENTO_FATURA ';
$queryEventos .= ' FROM PS1023 ';
$queryEventos .= ' INNER JOIN PS1000 ON (PS1023.CODIGO_ASSOCIADO = PS1000.CODIGO_ASSOCIADO) ';
$queryEventos .= ' INNER JOIN ESP0002 ON (ESP0002.CODIGO_GRUPO_CONTRATO = PS1000.CODIGO_GRUPO_CONTRATO) ';
$queryEventos .= ' WHERE (PS1023.CODIGO_ASSOCIADO = ' . aspas($_SESSION['codigoIdentificacao']);
$queryEventos .= ' OR PS1000.CODIGO_TITULAR = ' . aspas($_SESSION['codigoIdentificacao']) . ' ) ';
$queryEventos .= ' AND PS1023.MES_ANO_VENCIMENTO = ' . aspas($mesAno);
$resEventos = jn_query($queryEventos);

$i = 0;
$ArrEventos = array();
$totalEventosRecebidos = 0;
while($rowEventos = jn_fetch_object($resEventos)){
	$ArrEventos[$i]['descricaoGrupoContrato'] 	= $rowEventos->DESCRICAO_GRUPO_CONTRATO;
	$ArrEventos[$i]['codigoAssociado'] 			= $rowEventos->CODIGO_ASSOCIADO;
	$ArrEventos[$i]['nomeAssociado'] 			= $rowEventos->NOME_ASSOCIADO;
	$ArrEventos[$i]['mesAnoVencimento'] 		= $rowEventos->MES_ANO_VENCIMENTO;
	$ArrEventos[$i]['descricaoHistorico'] 		= $rowEventos->DESCRICAO_HISTORICO;
	$ArrEventos[$i]['detalhamentoFatura'] 		= $rowEventos->DETALHAMENTO_FATURA;
	$ArrEventos[$i]['valorEvento'] 				= toMoeda($rowEventos->VALOR_EVENTO);
	$totalEventosRecebidos = $totalEventosRecebidos + $rowEventos->VALOR_EVENTO;
	$i++;
}

$gridEventos = new jnGrid();
$gridEventos->addColuna('Grupo Contrato', '150');
$gridEventos->addColuna('Nome Associado', '210');
$gridEventos->addColuna('Valor Evento', '90');
$gridEventos->addColuna('Mês/Ano vencimento', '150');
$gridEventos->addColuna('Descrição Histórico', '');
$gridEventos->addColuna('Detalhamento Fatura', '');


foreach ($ArrEventos as $item) {
	
	$value = array(
		$item['descricaoGrupoContrato'],
		$item['nomeAssociado'],
		$item['valorEvento'],
		$item['mesAnoVencimento'],
		$item['descricaoHistorico'],
		$item['detalhamentoFatura']);	

	$gridEventos->addLinha($value);
	unset($value);
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Relatórios de eventos adicionais</title>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <link href="css/principal.css" media="all" rel="stylesheet" type="text/css" />
        <link href="css/dados_cadastrais.css" media="all" rel="stylesheet" type="text/css" />

        <style media="print">
            #BoxBotoes {
                display: none;
            }
        </style>
    </head>

    <body>
		<div style="position:absolute; background-color:#FFFFFF; width:100%; height:100%;">
			<div class="dadosCadastrais">
				<table>
					<tr class="comBG">
						<td>
							<h1><?php echo $OPERADORA['RAZAO_SOCIAL']; ?></h1>
							<h4>Relatório Evento adicionais pagos</h4>
						</td>
					</tr>        
					<tr>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $gridEventos->Output(); ?>
						</td>
					</tr>
					<tr height="30"></tr>
					<tr>
						<td class="" >
							<div style="position:relative; width:100%; text-align:left; font-weight:normal; font-size:12px;">
								<b><?php echo 'Total eventos recebidos: ' . toMoeda($totalEventosRecebidos); ?></b>
							</div>
						</td>
					</tr>
					<tr height="30"></tr>
					<tr>
						<td class="lbl" >
							<div style="position:relative; width:100%; text-align:right; font-weight:normal; font-size:10px;">
								<?php echo 'Data emissão: '.@getDataHoraAtual(); ?>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
    </body>
</html>