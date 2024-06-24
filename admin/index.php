<?php
include 'admin.php';

if(isset($_POST['in'])){
	if ($_POST['psw']==readCliente($_POST['asociado'])['PASSWORD']){
		if(readCliente($_POST['asociado'])['BLOQUEADO']=='0'){
				if(readCliente($_POST['asociado'])['NIVEL'] >= '1'){
					$_SESSION['user'] =readCliente($_POST['asociado'])['ID'];
					$_SESSION['admin'] =readCliente($_POST['asociado'])['NIVEL'];
					//air_drop_frt($_POST['asociado']);
					header("Location: appfortunar");
				}
				else{
					header("Location: ../index");
				}
				//se agregan los AirDrop
			}
		else{
			echo "<script>alert('Usuario Bloqueado Contacte al Proveedor para Desbloquear...');</script>";
		}
	}
	else{
		echo "<script>alert('Error: Password Incorrecto...');</script>";
	}
}
?>

<html lang="es">
<head>
		<link rel="shortcut icon" href="../Feed/Home/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" href="css/stylesd.css">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <meta charset="utf-8">
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device_width, initial-scale=1.0">
	<title>Sesion</title>
	<script src="js/modulo.js"></script>
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

   </script>
</head>
<main>
	<nav style="top:0" class="nav">
		<h2 class="logo" style="font-family: Bebas Neue, cursive;"><a href="index">FORTUNA ROYAL</a></h2>
	</nav>
	<div class="formulario_contenedor">
	<form id="form" action="index" method="POST" autocomplete="off">
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
		<br>
	</form>
	<u><h5 style='text-align:center; color: black; cursor: pointer;' onclick="revision_init()">Iniciar Sesión</h5></u>
	<br>
	</div>
  <br>
  <h5 style="text-align:center;">Copyright &copy; 2022 FortunaRoyal Admin</h5>
	<h5  style="text-align:center;"> All Rights Reserved (by) Triángulo Rojo</h5>
</main>
</html>
