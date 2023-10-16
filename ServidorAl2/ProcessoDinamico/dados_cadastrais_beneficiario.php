<?php
require('../lib/base.php');

require('../private/autentica.php');

header("Content-Type: text/html; charset=ISO-8859-1",true);

//pr('1',false);

//pr($_SESSION['codigoIdentificacao'],true);

$Benef = array();
$numero = $_SESSION['codigoIdentificacao'];

if (!empty($numero)) {
    $Benef['beneficiario'] = getDadosBeneficiario($numero, array('CODIGO_ASSOCIADO', 'CODIGO_EMPRESA'), true);
} else {
    //mensagem de erro...
}


// dados da empresa
$Benef['empresa'] = getDadosEmpresa($Benef['beneficiario'][0]->CODIGO_EMPRESA,
    array(
        'CODIGO_EMPRESA',
        'NOME_EMPRESA'
    )
);
//pr($Benef, true);

// dados do titular
$Benef['titular'] = getDadosTitular($Benef['beneficiario'][0]->CODIGO_ASSOCIADO,
        array(
            'CODIGO_ASSOCIADO',
            'CODIGO_PLANO',
            'NUMERO_CPF',
            'NUMERO_RG',
            'FLAG_PLANOFAMILIAR',
            'CODIGO_EMPRESA',
            'NOME_MAE'
        ),
        true
    );

// todos os beneficiarios do contrato
$Benef['beneficiarios'] = getBenTitFromCodigo($Benef['beneficiario'][0]->CODIGO_ASSOCIADO,
        array(
            'CODIGO_ASSOCIADO',
            'CODIGO_AUXILIAR',
            'NOME_ASSOCIADO',
            'CODIGO_PLANO',
            'CODIGO_CARENCIA',
            'DATA_ADMISSAO',
            'DATA_NASCIMENTO',
            'SEXO',
            'TIPO_ASSOCIADO',
            'DATA_EXCLUSAO',
            'VALOR_NOMINAL'
        ),
        true,
        true
    );


// pego os dados do plano do titulasr
$Benef['plano'] = getDadosPlano($Benef['titular'][0]->CODIGO_PLANO,
        array(
            'CODIGO_PLANO',
            'NOME_PLANO_FAMILIARES',
        ),
        true,
        true
    );

// pego os dados do contrato
$Benef['contrato'] = getDadosContrato($Benef['titular'][0]->CODIGO_ASSOCIADO,
        $Benef['titular'][0]->CODIGO_EMPRESA,
        array(
            'NUMERO_CONTRATO',
            'DIA_VENCIMENTO',
            'FATOR_TAXA_CALCULO',
            'DATA_LIMITE_TAXA',
            'NOME_CONTRATANTE',
            'VALOR_TAXA_ADESAO',
            'VALOR_ADESAO'
        ),
        $Benef['titular'][0]->FLAG_PLANOFAMILIAR == 'S'
    );



// pego os dados do contrato
$Benef['endereco'] = getDadosEndereco($Benef['titular'][0]->CODIGO_ASSOCIADO, null, 'B');

// pego os dados do telefone (contato)
$Benef['telefone'] = getDadosTelefone($Benef['titular'][0]->CODIGO_ASSOCIADO, null, 'B');

// pego os dados das carencias
$Benef['carencias'] = getCarencias($Benef['beneficiario'][0]->CODIGO_ASSOCIADO, null, 'B');


/*
 * Alguns valores devem ser ocultados, mas isso é definido de operadora para
 * operadora. Portanto, pego alguns campos passiveis de ocultamento e coloco
 * numa lista, caso deseje exibi-los.
 */

$ValoresOcultos = explode(', ', rvc('CAMPOS_OCULTOS_DADOS_CAD_BEN'));

/*******************************************************************************
 * Monto a estrutura do grid
 ******************************************************************************/

$gridBenefs = new jnGrid();
$gridBenefs->addColuna('C&oacute;digo', '80');
$gridBenefs->addColuna('C&oacute;digo Auxiliar', '160');
$gridBenefs->addColuna('Nome do benefici&aacute;rio', '240');
$gridBenefs->addColuna('Plano', '');
$gridBenefs->addColuna('Tab. Car.', '');
$gridBenefs->addColuna('Admiss&atilde;o', '');
$gridBenefs->addColuna('Dt. Nasc', '');
$gridBenefs->addColuna('Idade', '');
$gridBenefs->addColuna('Sexo', '');
$gridBenefs->addColuna('Tipo', '');
$gridBenefs->addColuna('Dt. Exclus&atilde;o', '');
if (!in_array('ValorNominal', $ValoresOcultos)) { // campo personalizado
    $gridBenefs->addColuna('Vl.Nom.', '');
}

// add as linhas que representa cada registro de beneficiario
if (!empty($Benef['beneficiarios'])) {
    foreach ($Benef['beneficiarios'] as $item) {
        $value = array();
        $value[] = $item->CODIGO_ASSOCIADO;
        $value[] = $item->CODIGO_AUXILIAR;
        $value[] = $item->NOME_ASSOCIADO;
        $value[] = $item->CODIGO_PLANO;
        $value[] = $item->CODIGO_CARENCIA;
        $value[] = SqlToData($item->DATA_ADMISSAO);
        $value[] = SqlToData($item->DATA_NASCIMENTO);
        $value[] = '&nbsp;' .floor($item->IDADE);
        $value[] = '&nbsp;&nbsp;' . $item->SEXO;
        $value[] = '&nbsp;&nbsp;' . $item->TIPO_ASSOCIADO;
        $value[] = SqlToData($item->DATA_EXCLUSAO);
        if (!in_array('ValorNominal', $ValoresOcultos)) { // campo personalizado
            $value[] = toMoeda($item->VALOR_NOMINAL);
        };

        $gridBenefs->addLinha($value);
        unset($value);
    }
}

/*******************************************************************************
 * Monto a estrutura do grid das carencias
 ******************************************************************************/

$gridCarencias = new jnGrid();
$gridCarencias->addColuna('C&oacute;digo Grupo', '100');
$gridCarencias->addColuna('Nome do grupo', '400');
$gridCarencias->addColuna('Data Car&ecirc;ncia', '');

// add as linhas que representa cada registro de beneficiario
if (!empty($Benef['carencias'])) {
    foreach ($Benef['carencias'] as $item) {
        $value = array(
            $item->RESULTADO_NUMERO_GRUPO,
            $item->RESULTADO_DESCRICAO_GRUPO,
            SqlToData($item->RESULTADO_DATA_CARENCIA)
        );
        $gridCarencias->addLinha($value);
    }
}
	$imagem = '';
	if(retornaValorCFG0003('MOSTRA_IMAGEM_BENEFICIARIO_ELEGIBILIDADE')=='SIM'){
		$queryImg  = "select caminho_arquivo||caminho_arquivo_armazenado||nome_arquivo_armazenado IMAGEM FROM controle_arquivos
								INNER JOIN configuracoes_arq ON 1=1
								where NOME_TABELA = 'FOTO' and DATA_EXCLUSAO is null and CHAVE_REGISTRO =" . aspas($numero);
		$resImg = jn_query($queryImg);
		if($rowImg = jn_fetch_object($resImg)){
			$imagem  = $rowImg->IMAGEM;
		}
		
	}
	


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Dados Cadastrais - Benefici&aacute;rio</title>
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
        <!--
		<div id="BoxBotoes">
            <input type="button" value="Imprimir" class="" name="btnImprimirProtocolo" id="btnImprimirProtocolo" onclick="window.print()" />
            <input type="button" name="btnVoltar" class="" value="Voltar" id="btnVoltar" onClick="history.go(-1);" />
        </div>
		-->

<div class="dadosCadastrais">

    <img style="max-width:20% !important" src="<?php echo file_exists('../../Site/assets/img/logo_operadora.png') ? '../../Site/assets/img/logo_operadora.png' : '../../Site/assets/img/logo_operadora.jpg';?>" border="0"/>
	
    <table>
        <tr>
            <td class="dataEmissao">Data emiss&atilde;o: <?php echo @getDataHoraAtual(); ?></td>
        </tr>
        <tr class="comBG">
            <td>
                <h1><?php echo $OPERADORA['RAZAO_SOCIAL']; ?></h1>
                <h4>Consulta de dados cadastrais de benefici&aacute;rio</h4>
            </td>
        </tr>        
        <tr>
            <td>
                &nbsp;
            </td>
        </tr>
		<?php if($imagem != ''){?>
		<tr>
            <td>
                <img src="<?php echo $imagem; ?>">
            </td>
        </tr>
		<tr>
            <td>
                &nbsp;
            </td>
        </tr>
		<?php }?>
        <tr>
            <td>
                <?php echo $gridBenefs->Output(); ?>
            </td>
        </tr>

        <tr>
            <td class="lbl">Dados do plano</td>
        </tr>
        <tr>
            <td>
                <table>                    
                    <tr>
                        <td class="lblToValue">Plano:</td>
                        <td class="lblValue"><?php echo $Benef['plano'][0]->CODIGO_PLANO; ?> - <?php echo $Benef['plano'][0]->NOME_PLANO_FAMILIARES; ?></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="lbl">Dados do contrato</td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="lblToValue">N&uacute;mero do contrato:</td>
                        <td class="lblValue"><?php echo $Benef['contrato'][0]->NUMERO_CONTRATO; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Dia de vencimento:</td>
                        <td class="lblValue"><?php echo $Benef['contrato'][0]->DIA_VENCIMENTO; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Taxa de c&aacute;lculo:</td>
                        <td class="lblValue"><?php echo $Benef['contrato'][0]->TAXA_CALCULO; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Dt. Limite uso taxa:</td>
                        <td class="lblValue"><?php echo $Benef['contrato'][0]->DATA_LIMITE_TAXA; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Nome do contratante:</td>
                        <td class="lblValue"><?php echo $Benef['contrato'][0]->NOME_CONTRATANTE; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Nome do vendedor:</td>
                        <td class="lblValue"></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Data &uacute;ltima suspens&atilde;o:</td>
                        <td class="lblValue">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Data &uacute;ltima reativa&ccedil;&atilde;o:</td>
                        <td class="lblValue">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Motivo de exclus&atilde;o:</td>
                        <td class="lblValue">&nbsp;</td>
                    </tr>
                    <?php if (!in_array('ValorAdesao', $ValoresOcultos)) : // campo personalizado ?>
                    <tr>
                        <td class="lblToValue">Valor de ades&atilde;o:</td>
                        <td class="lblValue"><?php echo toMoeda($Benef['contrato'][0]->VALOR_ADESAO); ?></td>
                    </tr>
                    <?php endif; ?>

                    <?php if (!in_array('TaxaAdesao', $ValoresOcultos)) : // campo personalizado ?>
                    <tr>
                        <td class="lblToValue">Taxa de ades&atilde;o:</td>
                        <td class="lblValue"><?php echo toMoeda($Benef['contrato'][0]->VALOR_TAXA_ADESAO); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>

        <tr>
            <td class="lbl">Dados do endere&ccedil;o:</td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="lblToValue">Endere&ccedil;o:</td>
                        <td class="lblValue"><?php echo $Benef['endereco'][0]->ENDERECO; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Bairro:</td>
                        <td class="lblValue"><?php echo $Benef['endereco'][0]->BAIRRO; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Cidade:</td>
                        <td class="lblValue"><?php echo $Benef['endereco'][0]->CIDADE; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">CEP:</td>
                        <td class="lblValue"><?php echo $Benef['endereco'][0]->CEP; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Estado:</td>
                        <td class="lblValue"><?php echo $Benef['endereco'][0]->ESTADO; ?></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="lbl">Dados do contato:</td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="lblToValue">Telefone 01:</td>
                        <td class="lblValue"><?php echo $Benef['telefone'][0]->CODIGO_AREA; ?> - <?php echo $Benef['telefone'][0]->NUMERO_TELEFONE; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Telefone 02:</td>
                        <td class="lblValue"><?php echo $Benef['telefone'][1]->CODIGO_AREA; ?> - <?php echo $Benef['telefone'][1]->NUMERO_TELEFONE; ?></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="lbl">Dados do titular:</td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="lblToValue">CPF:</td>
                        <td class="lblValue"><?php echo $Benef['titular'][0]->NUMERO_CPF; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">RG:</td>
                        <td class="lblValue"><?php echo $Benef['titular'][0]->NUMERO_RG; ?></td>
                    </tr>
                    <tr>
                        <td class="lblToValue">Nome da m&atilde;e:</td>
                        <td class="lblValue"><?php echo $Benef['titular'][0]->NOME_MAE; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
		
		<?php
		if(rvc('OCULTAR_CARENCIA_DADOS_BENEF') == '' || rvc('OCULTAR_CARENCIA_DADOS_BENEF') != 'SIM'){
		?>
			<tr>
				<td class="lbl">Car&ecirc;ncias para grupos de atendimentos:</td>
			</tr>

			<tr>
				<td>
					<?php echo $gridCarencias->Output(); ?>
				</td>
			</tr>
		<?php
		}
		?>
        <tr height="30"></tr>
        <tr>
            <td class="lbl" >
            	<div style="position:relative; width:100%; text-align:right; font-weight:normal; font-size:10px;">
                	<?php echo 'Data emiss&atilde;o: '.@getDataHoraAtual(); ?>
				</div>
            </td>
	</tr>
        <tr>
        </tr>
    </table>

</div>
</div>
    </body>
</html>