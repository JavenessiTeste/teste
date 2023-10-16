<?php
require('../lib/base.php');
require('../lib/autenticaCelular.php');
require('../private/autentica.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$mesAno = $_GET['comp'];
//pr($mesAno);
//pr($_SESSION);
//pr("utilizacao".$_SESSION['codigoIdentificacao']. str_replace('/','',$mesAno).'.pdf');

$query = "SELECT TIPO_GUIA, NUMERO_GUIA, NOME_ASSOCIADO, DATA_PROCEDIMENTO, NOME_PRESTADOR, VALOR_COPARTICIPACAO, NOME_PROCEDIMENTO, QUANTIDADE, NOME_ESPECIALIDADE 
		  FROM VW_CONFERENCIA_UTILIZACAO WHERE MES_ANO_REFERENCIA = ".aspas($mesAno)." and CODIGO_ASSOCIADO = ". aspas($_SESSION['codigoIdentificacao']);

$res = jn_query($query);


$html = "<table align='center' style='font-size:10px;'>
			<tr>														
				<td>		
					<b>RELATORIO DE EXTRATO DE UTILIZA&Ccedil;&Atilde;O - ". $mesAno ."</b>									
				</td>						
			</tr>												
		</table>
		<br>
  <table  style='font-size:14px;padding-left: 10%;padding-right: 10%;'>
    <thead>
        <tr>
            <th align='left' style='white-space: nowrap;'>Tipo Guia</th>
            <th align='left' style='white-space: nowrap;'>Número Guia</th>
            <th align='left' style='white-space: nowrap;'>Nome Associado</th>
            <th align='center' style='white-space: nowrap;'>Data Procedimento</th>
            <th align='left' style='white-space: nowrap;'>Nome Prestador</th>
            <th align='left' style='white-space: nowrap;'>Valor Coparticipação</th>
            <th align='left' style='white-space: nowrap;'>Nome Procedimento</th>
            <th align='center' style='white-space: nowrap;'>Quantidade</th>
            <th align='left' style='white-space: nowrap;'>Nome Especialidade</th>
        </tr>
    </thead>
    <tbody>";

while($row = jn_fetch_object($res)){	
 $html .= "<tr width='100%'  >
                <td  align='center'>" . $row->TIPO_GUIA . "</td>
                <td align='left'>" . $row->NUMERO_GUIA . "</td>
                <td align='left' style='white-space: nowrap;'>" . substr($row->NOME_ASSOCIADO,0,35) . "</td>
                <td align='center'>" . SqlToData($row->DATA_PROCEDIMENTO) . "</td>
                <td  align='left' style='white-space: nowrap;'>" . substr($row->NOME_PRESTADOR,0,25) . "</td>
                <td  align='left'>" . toMoeda($row->VALOR_COPARTICIPACAO) . "</td>
                <td  align='left' style='white-space: nowrap;'>" . $row->NOME_PROCEDIMENTO . "</td>
                <td  align='center'>" . $row->QUANTIDADE . "</td>
                <td  align='left' style='white-space: nowrap;'>" .  substr($row->NOME_ESPECIALIDADE,0,25) . "</td>
              </tr>";
}

$html .= "</tbody></table>";

$header = "<img src='../../Site/assets/img/cabecalho.png' />";
$footer = "<img src='../../Site/assets/img/rodape.png' />";


include("../lib/mpdf60/mpdf.php");
$mpdf=new mPDF('utf-8', 'A4', '', '', 5, 5, 45, 35); 
$mpdf->SetHTMLHeader($header);
$mpdf->SetHTMLFooter($footer);
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output("utilizacao".$_SESSION['codigoIdentificacao']. str_replace('/','',$mesAno).'.pdf', 'I');
exit;