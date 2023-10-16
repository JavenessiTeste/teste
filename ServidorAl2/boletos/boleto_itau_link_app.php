<?php
require('../lib/base.php');

////require('../private/autentica.php');

header('Content-type: text/html');

if($_GET['chave']){
	$chave = $_GET['chave'];
}else{
	$chave = 1;
}


if (strlen($_GET['CODIDENTIFICACAO']) >= 7)
    $query .= 'select numero_cpf DOCUMENTO from ps1000 where codigo_associado = ' . aspas($_GET['CODIDENTIFICACAO']);
else
    $query .= 'select NUMERO_CNPJ DOCUMENTO from ps1010 where codigo_empresa = ' . aspas($_GET['CODIDENTIFICACAO']);


$res = jn_query($query);
   
   
    
if ($row = jn_fetch_object($res)) {
	$documento =  $row->DOCUMENTO;
}else{
	echo 'Erro Documento';
	exit;
}


$DC = file_get_contents("http://vidamax.net.br/Dados/Index.asp?chave=".$chave."&codSacado=".$documento);
$DC =  strip_tags($DC);

?>
<html>
	<body>
		<form action="https://ww2.itau.com.br/2viabloq/pesquisa.asp" method="Post" name="form" id="form"  onsubmit="carregabrw();"> 
			<input type="hidden" name="DC" id="DC" value="<?php echo $DC;  ?>"> 
			<input type="hidden" name="msg" id="msg" value="S"> 
		</form>
	</body>
</html>
<script>
  document.addEventListener("DOMContentLoaded", function(event) {
    document.getElementById('form').submit();
  });
</script>