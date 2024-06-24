<?php
	include "modulo.php";

	sqlconector("CREATE TABLE IF NOT EXISTS LINKS (
					ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					FECHA TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					LINK VARCHAR(255),
					CORREO VARCHAR(255),
					BLOQUEADO INT NOT NULL DEFAULT 0)");

	if(isset($_POST['guardar'])){
		sqlconector("update USUARIOS SET PASSWORD='".$_POST['psw']."' WHERE CORREO='{$_POST['asociado']}'");
		sqlconector("delete from LINKS where LINK='".$_POST['code']."'");
		header("Location:index");
	}

	if(isset($_POST['recuperar'])){
		$bytes = random_bytes(8);
		$codigo = bin2hex($bytes);
		sqlconector("insert into LINKS (LINK,CORREO) values ('".$codigo."','".$_POST['correo']."')");

		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "activar@fortunaroyal.com";
		$to = $_POST['correo'];
		$subject = "Cambio de Contraseña Fortuna Royal";
		$message = "No Conteste este Email solo Copie y Pegue el Link en su navegador para Cambiar su Contraseña: ".
			"http://fortunaroyal.com/recuperar?code={$codigo}&email={$_POST['correo']}";
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
	}

	function ifCodeExist($link) {
		if(row_sqlconector("select COUNT(*) AS TOTAL from LINKS where LINK='".$link."'")['TOTAL']==1) return TRUE;
		return FALSE;
	}
?>

<html>
	<head>
		<link rel="stylesheet" href="css/stylesd.css">
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
	</head>
<body>
	<nav style="top:0" class="nav">
		<h3 style="font-family: Bebas Neue, cursive;">FORTUNA ROYAL</h3>
	</nav>
	<div class="formulario_contenedor">
	<?php
	if(isset($_GET['code'])) {
		if (ifCodeExist($_GET['code'])) {
	?>
	<form id="form" action="recuperar" method="POST" autocomplete="off">
		<input type="hidden" name="asociado" id="asociado" value="<?php echo $_GET['email']; ?>">
		<input type="hidden" name="code" id="code" value="<?php echo $_GET['code']; ?>">
		<span>Recuperar Contraseña</span>
		<input type="password" placeholder="Enter New Password" name="psw" id="psw" required >
		<button type="submit" style="background:#BCE7BC;" name="guardar" class="btn btn-form">Cambiar Contraseña</button>
		<br>
	</form>

  <?php }
  	else {
  		echo "<span style='color:black;'>El Link de Recuperacion ha Expirado....!</span>";
  	}
  }
  ?>
    </div>
  <br>
  <h5 style="text-align:center;">Copyright &copy; 2020 FortunaRoyal All Rights Reserved (by) Triángulo Rojo</h5>
</body>
</html>
