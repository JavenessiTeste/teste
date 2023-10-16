<?php
session_start();
if(!$_SESSION['codigoIdentificacao']){ 
 Header ('Location: frm_login.php');		
}


?>