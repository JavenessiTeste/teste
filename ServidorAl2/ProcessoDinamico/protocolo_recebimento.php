<?php
require('../lib/base.php');
header("Content-Type: text/html; charset=ISO-8859-1",true);

$Prot = array();
$Prot['NumeroProtocolo'] = isset($_GET['numero']) ? $_GET['numero'] : '';

$query  = 'SELECT Ps5272.*, Ps5000.Nome_Prestador ';
$query .= 'FROM Ps5272 INNER JOIN Ps5000 ON Ps5272.Codigo_Prestador = Ps5000.Codigo_Prestador ';
$query .= 'WHERE Numero_Protocolo = ' . aspas($Prot['NumeroProtocolo']);

$res = jn_query($query);
$i = 0;
$htmlArquivos = '';

while($row = jn_fetch_object($res)){
	
	$Prot['CodigoPrestador' . $i]    = $row->CODIGO_PRESTADOR;
	$Prot['NomePrestador' . $i]      = $row->NOME_PRESTADOR;
	$Prot['NomeArquivo' . $i]        = $row->NOME_ARQUIVO_UPLOAD;
	$Prot['DataUpload' . $i]         = $row->DATA_UPLOAD;
	$Prot['HoraUpload' . $i]         = $row->HORA_UPLOAD;
	$Prot['Competencia' . $i]         = $row->MES_ANO_COMPETENCIA;
		
		$htmlArquivos .= '	<tr>
								<td>
								</td>
								<td>' . 
									'Nome Arquivo: '. $Prot['NomeArquivo' . $i] . ' <br>' . 																
									'<hr>' .
							'	</td>
							</tr>';
	
	$i++;
	
	
	$valorGuias = number_format($Prot['ValorTotal' . $i], 2, ',', '.');

	
}

?>
<html>
    <head>
        <title>Protocolo de Transmissão</title>
        <link href="css/principal.css" rel="stylesheet" type="text/css" />

        <style media="all">
            body { text-align: left; font-family: "Courier New",Arial }
            table {
                border: #666 solid 1px;
                margin: 30px;
            }
            table td {padding: 4px;}
            h3 {margin: 35px;}
            h6 {margin: 12px; text-align: right;}
            hr {border: none; border-top: #666 solid 2px; padding: none;}
        </style>

    </head>

    <body>
        <h3><?php echo $_SESSION['razaoSocialOperadora']; ?></h3>
        <h6>Emissão: <?php echo date('d/m/Y H:i:s'); ?></h6>
        <hr>
        <table>
            <tr>
                <td>No. do Protocolo de Transmissão:</td>
                <td><b><?php echo $Prot['NumeroProtocolo'] ; ?></b></td>
            </tr>
            <tr>
                <td>Código do Prestador na Operadora:</td>
                <td><?php echo $Prot['CodigoPrestador0'] ; ?></td>
            </tr>
            <tr>
                <td>Nome do Prestador:</td>
                <td><?php echo $Prot['NomePrestador0'] ; ?></td>
            </tr>
			<tr>
                <td>Mês Ano Competencia:</td>
                <td><?php echo $Prot['Competencia0'] ; ?></td>
            </tr>
            <tr>
                <td>Arquivo enviado:</td>
                <?php
				echo $htmlArquivos;
				?>
            </tr>
            <tr>
                <td>Data Recebimento:</td>
                <td><?php echo SqlToData($Prot['DataUpload0']) . ' ' . $Prot['HoraUpload0'] ; ?></td>
            </tr>
        </table>

        <hr>

    </body>
</html>
