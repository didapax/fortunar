<?php
	include "modulo.php";

	if(row_sqlconector("SELECT * FROM ACTIVATE WHERE REFERENCIA='{$_GET['ref']}'")['REFERENCIA'] == $_GET['ref']){
		$correo = row_sqlconector("SELECT * FROM ACTIVATE WHERE REFERENCIA='{$_GET['ref']}'")['CORREO'];
		sqlconector("UPDATE USUARIOS SET ACTIVO=1 WHERE CORREO ='{$correo}'");
		sqlconector("DELETE FROM ACTIVATE WHERE CORREO='{$correo}'");
	}
	header("Location:index");
?>
