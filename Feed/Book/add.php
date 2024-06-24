<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');
if (isset($_POST['add_token'])){
	if(isset($_FILES['archivo']['name'])){
			$ext=".jpeg";
			$fileImagen="";		
			$codigorifa = strtoupper($_POST['token']);
			$archivo = $_FILES['archivo']['name'];
		if (isset($archivo) && $archivo != "") {
			$tipo = $_FILES['archivo']['type'];
			$tamano = $_FILES['archivo']['size'];
			$temp = $_FILES['archivo']['tmp_name'];
			if (!((strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "webp") || strpos($tipo, "png")) && ($tamano < 500000))) {
				die ("<br><br>Error. La extensión o el tamaño de los archivos no es correcta.");
			}
			else {
				if(strpos($tipo,"jpg")) $ext=".jpg";
				else if (strpos($tipo,"png")) $ext=".png";
				else if (strpos($tipo,"webp")) $ext=".webp";
				$fileImagen='comercios/'.$codigorifa.$ext;
				if (move_uploaded_file($temp,$fileImagen)) {
					chmod($fileImagen, 0777);
					$conexion = mysqli_connect(servidor(),user(),password(),database());
					$consulta="insert into TOKEN (
					TOKEN,
					NOMBRE,
					CORREO,
					CEO,
					TELEFONO,
					INSTAGRAM,
					DESCRIPCION,
					IMAGEN,
					VALOR,
					PATRIMONIOINICIAL,
					PATRIMONIO,
					MAXSUPPLY,
					RATE,
					VOLUMEN,
					BLOQUEADO) values ('"
					.strtoupper($_POST['token'])."','"
					.$_POST['nombre']."','"
					.readVendedor($_SESSION['user'])['CORREO']."','"
					.$_POST['ceo']."','"
					.$_POST['telefono']."','"
					.$_POST['instagram']."','"
					.$_POST['descripcion']."','"
					.$codigorifa.$ext."',"
					.$_POST['valor'].","
					.$_POST['patrimonio'].","
					.$_POST['patrimonio'].",0,1,"
					.$_POST['volumen'].",2)";
					if($resultado = mysqli_query( $conexion, $consulta )){
						$comision = $_POST['volumen'] * tasa(readFortuna()['COMISIONEMISION']);
						updateSaldo(readVendedor($_SESSION['user'])['SALDO']-$_POST['patrimonio'],readVendedor($_SESSION['user'])['CORREO']);
						if(readVendedor($_SESSION['user'])['CORREO']!="fortuna@fortunaroyal.com"){
							updateSaldo(readVendedor2("fortuna@fortunaroyal.com")['SALDO']+$_POST['patrimonio'],"fortuna@fortunaroyal.com");
						}
						if(!if_wallet_exist(readVendedor($_SESSION['user'])['CORREO'],strtoupper($_POST['token']) ) ) {
							create_wallet(readVendedor($_SESSION['user'])['CORREO'],strtoupper($_POST['token']));
						}
						if(!if_wallet_exist("fortuna@fortunaroyal.com",strtoupper($_POST['token']) ) ) {
							create_wallet("fortuna@fortunaroyal.com",strtoupper($_POST['token']));
						}
						update_saldo_token_positivo($_POST['volumen'] - $comision,readVendedor($_SESSION['user'])['CORREO'],strtoupper($_POST['token']) );
						update_saldo_token_positivo($comision,"fortuna@fortunaroyal.com",strtoupper($_POST['token']) );
						sqlconector("INSERT INTO LIBROCONTABLE(TIPO,MONEDA,MONTO) VALUES('COMI','".strtoupper($_POST['token'])."',".$comision.")");
						recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
						sqlconector("UPDATE WALLETOKEN SET BLOQUEADO=1 WHERE TOKEN='".strtoupper($_POST['token'])."'");
					}
				}
				else {
					echo '<script>alert(\"Ocurrió algún error al subir la foto de Perfil. No pudo guardarse.\")</script>';
				}
			}
		}
	}
	mysqli_close($conexion);
	header("location: historial.php");
}
?>

<!DOCTYPE html>
<html lang="es" style="overflow-y: auto;">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8">
  <meta http-equiv="content-style-type" content="text/css">
  <meta http-equiv="expires" content="0">  
  <title></title>
  <link rel="stylesheet" href="./style2.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <script>

  function valorLiquido(){
  	var total;
  	total=document.getElementById('token_patrimonio').value / document.getElementById('token_volumen').value;
  	document.getElementById('token_valor').value = total.toFixed(4);
  }

  function revision(){
    $.get("../../modulo.php?token="+document.getElementById('token').value, function(data){
  		if(data=="true"){
        document.getElementById('token').style.background="#F0D6DB";
  			document.getElementById('token').value="";
        document.getElementById('token').focus();
  		}else{
        document.getElementById('token').style.background="#CCE6CC";
  		}
  	});
  }

  </script>
	<style>
	input[type=text]{
		padding: 5px;
		border-radius: 5px;
	}

	input[type=number]{
		padding: 5px;
		border-radius: 5px;
	}
	tr td{
		padding: 3px;
	}

	.add-button {
	  margin-top:20px;
	  outline: none;
	  border: solid 1px white;
	  color: white;
	  height: 55px;
	  width: 60%;
	  border-radius: 5px;
	  background: #4486F8;
	  font-weight: bold;
	  font-size: 18px;
	  cursor: pointer;
	  text-transform:uppercase;
	}
	</style>
</head>
<body>
<!-- partial:index.partial.html -->
<?php readPromo('add'); ?>
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
		<h1 class="apps-title" style="font-weight:bold;">Crear Comercio</h1>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h1 class="apps-title" style="font-weight:bold;">Crear Comercio</h1>
  </div>

  <div >
    <form id="target" method="post" action="add.php" style="padding:21px;" enctype="multipart/form-data">
			<span style="display:block;color:white;font-weight:bold;">Biografia del Creador</span>
				<textarea rows="10" cols="40" required id="ceo" name="ceo" minlength="10"></textarea>
			<span style="margin-top: 10px;display:block;color:white;font-weight:bold;">Telefono</span>
				<input required style='display:inline-block;' type='text' name='telefono'>
			<span style="margin-top: 10px;display:block;color:white;font-weight:bold;">Red Social</span>
				<input required style='display:inline-block;width:80%;' minlength="8" type='text' name='instagram'>
			<span style="margin-top: 10px;display:block;color:white;font-weight:bold;">Nombre Corto del Comercio</span>
					<input required style='display:inline-block;' maxlength="34" type='text' name='nombre'>
	    	<span style="margin-top: 10px;display:block;color:white;font-weight:bold;">Siglas del Comercio ("Tres Letras")</span>
				<input title="Tres Letras que Representan al Proyecto" onfocusout="revision()" minlength="3" required maxlength='3' style='text-transform: uppercase;width:20%; display:inline-block;' type='text' name='token' id="token" >
	    	<span style="margin-top: 10px;display:block;color:white;font-weight:bold;">Descripcion</span>
				<textarea rows="10" cols="40" id="descripcion" name="descripcion" minlength="10" ></textarea>
	    	<span style="display:block;color:white;font-weight:bold;padding:5px;">Subir Imagen del Comercio</span>
				<input style="color:white;" title="El logo del token" accept='image/*' type='file' id='archivo' name='archivo' accept=".jpg, .jpeg, .png, .webp" >
			<span style="margin-top: 10px;display:block;color:white;font-weight:bold;">Patrimonio</span>
				<input min="40" max="<?php echo readVendedor($_SESSION['user'])['SALDO'] ?>" required onkeyup='valorLiquido()' onchange="valorLiquido()" style='width:30%;
				display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number' id="token_patrimonio"  name='patrimonio'  step='1' title='Capital Inicial debe ser Minimo 40FRD'  value='1'>
			<span style="display:block;color:white;font-weight:bold;">Volumen</span>
				<input min="1" required onkeyup='valorLiquido()' onchange="valorLiquido()" style='width:30%; display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number' id="token_volumen" name='volumen' step='1' title='Currency' value=1>
			<span style="display:block;color:white;font-weight:bold;">Valor FRD</span>
				<input readonly required style='width:30%; display:inline-block;color:black;font-weight: bolder;background:#65DDB7;' type='number' id="token_valor" name='valor' step='1' title='Currency'  value=0>
			<br>
			<button class="add-button" value="add_token" name="add_token" type="submit">Crear</button>
			<br>
  </form>
  <script>
	function hola(){
		document.getElementById("target").submit();
 	}
  </script>  
<br><br><br>
</div>
</body>
</html>
