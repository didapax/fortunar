<?php
include "../../modulo.php";

if(isset($_POST['in'])){
	if ($_POST['psw']==readCliente($_POST['asociado'])['PASSWORD']){
		if(readCliente($_POST['asociado'])['BLOQUEADO']=='0'){
				$ipreal = getRealIpAddr();
				sqlconector("UPDATE USUARIOS SET IP='{$ipreal}' WHERE CORREO='{$_POST['asociado']}'");
				$_SESSION['user'] =readCliente($_POST['asociado'])['ID'];
				$_SESSION['nivel'] =readCliente($_POST['asociado'])['NIVEL'];
				recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
				asignarCredito();
				header("Location: home.php");
		}
		else{
			echo "<script>alert('Usuario Bloqueado Contacte a Soporte para Desbloquear...');</script>";
		}
	}
	else{
		echo "<script>alert('Error: Password Incorrecto...');</script>";
	}
}

if(isset($_POST['rg'])){
	if(readCliente($_POST['asociado'])['CORREO'] != $_POST['asociado']) {
		$ipreal = getRealIpAddr();
		$codeRefer="F".rand(0,999)."N".rand(0,999)."R".generaRefer();
		$conexion = mysqli_connect(servidor(),user(),password());
		$db = mysqli_select_db( $conexion, database());
		$linkReferido = readFortuna()['WEB']."sesion?user=".$_POST['asociado']."&code=".$codeRefer;
		$cliente = array('nombre' => $_POST['asociado'],'password' => $_POST['psw'], 'linkreferido' => $linkReferido,'codigoreferido' => $codeRefer );
		$consulta="insert into USUARIOS (IP,NOMBRE,PASSWORD,CORREO,LINKREFERIDO,CODIGOREFERIDO,CLIENTE) values ('{$ipreal}','"
		.$_POST['asociado']."','"
		.$_POST['psw']."','"
		.$_POST['asociado']."','".$linkReferido."','".$codeRefer."','".json_encode($cliente)."')";

		if($resultado = mysqli_query( $conexion, $consulta )){
			$_SESSION['user'] =readCliente($_POST['asociado'])['ID'];
			$_SESSION['nivel'] =readCliente($_POST['asociado'])['NIVEL'];
			mysqli_close($conexion);
			if(isset($_POST['referente'])){
				insertReferido($codeRefer,$_POST['referente'],1);
			}
			header("Location: home.php");
		}else mysqli_close($conexion);
	}
	else {
		echo "<script>alert('El Correo ya esta Registrado...');</script>";
	}
}
?>

<html lang="es">
<head>

    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" href="../../stylesd.css">
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0" />
	<title>Sesion</title>
	<script src="../../modulo.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
    <style>
		body{
		background:#F8F9F9;
		}
		a{
		text-decoration: none;
		color: var(--gris_claro);
		}
		label{
		color:#4D4D4D;
		}
		a {
		text-decoration: none;
		-webkit-transition: all 1s;
		-moz-transition: all 1s;
		transition: all 1s;
		display:inline-block;
		}
   </style>
   <script type="text/javascript" >
		function recuperar(){
            if(document.getElementById('asociado').value.includes("@")){
                $.post("../../recuperar",
                {
                    recuperar: "",
                    correo: document.getElementById('asociado').value
                },
                function(data){
                    alert("Revisa tu Correo y Ejecuta el Link que se te envio, Cambia tu clave Nuevamente..!");
                });            
            }else alert("Coloca tu Correo de Usuario a Recuperar...");
	    }
   </script>
</head>
<main>
	<nav style="top:0" class="nav">
		<h2 class="logo" style="font-family: Bebas Neue, cursive;">FORTUNA ROYAL</h2>
	</nav>
	<div class="formulario_contenedor">
	<form id="form" action="sesion" method="POST" autocomplete="off">
		<input type="hidden" id="status" value="0">
		<input type="email" placeholder="Enter Email" name="asociado" id="asociado" required onfocusout="revision_init();">
		<br>
		<input type="password" placeholder="Enter Password" name="psw" id="psw" required onfocusout="revision_pass();">
		<?php
			if(isset($_GET['code'])){
				echo "<input type='hidden' name='referente' value='".$_GET['code']."'>";
			}
		?>
		<button type="submit" style="background:#BCE7BC;display:none;" name="in" id="in" class="btn btn-form">Ingresar</button>
		<button type="submit" name="rg" id="rg" style="display:none;" class="btn btn-form">Registrarse</button>
		<br>
	</form>
	<u><h5 style='text-align:center; color: black; cursor: pointer;' onclick="revision_init()">Regístrese o Iniciar Sesión</h5></u>
	<br>
	<a id="semeolvido" style="display:none; font-size: 13px; font-style: italic; color: gray; cursor: pointer;text-decoration: none; " onclick="recuperar()"> Se me Olvidó la Contraseña</a>
  </div>
  <br>
  <h5 style="text-align:center;">Copyright &copy; 2020 FortunaRoyal All Rights Reserved (by) Triángulo Rojo</h5>
</main>
</html>
