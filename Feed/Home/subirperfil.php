<?php
include "../../modulo.php";

if(isset($_FILES['archivo']['name'])){
	$ext=".jpeg";
	$fileImagen="";
	$bytes = random_bytes(5);
	$codigorifa = bin2hex($bytes);
 	$archivo = $_FILES['archivo']['name'];
  if (isset($archivo) && $archivo != "") {
  	$tipo = $_FILES['archivo']['type'];
    $tamano = $_FILES['archivo']['size'];
    $temp = $_FILES['archivo']['tmp_name'];
    if (!((strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "webp") || strpos($tipo, "png")) && ($tamano < 500000))) {
    	die ("<br><br>Error. La extensión o el tamaño de los archivos no es correcta.<br>Actualice la Pagina y Intente de Nuevo");
    }
    else {
			if(strpos($tipo,"jpg")) $ext=".jpg";
			else if (strpos($tipo,"png")) $ext=".png";
			else if (strpos($tipo,"webp")) $ext=".webp";
			$imagen="perfiles/".readVendedor($_SESSION['user'])['PERFIL'];
			if($imagen != "perfiles/perfil.jpg") {
				if(file_exists ($imagen))unlink($imagen);
			}
      $fileImagen='perfiles/'.$codigorifa.$ext;
      if (move_uploaded_file($temp,$fileImagen)) {
            chmod($fileImagen, 0777);
						updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'PERFIL',$codigorifa.$ext);
      }
   	else {
          echo '<script>alert(\"Ocurrió algún error al subir la foto de Perfil. No pudo guardarse.\")</script>';
      }
     }
   }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>subir perfil</title>
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8">
<meta http-equiv="content-style-type" content="text/css">
<meta http-equiv="expires" content="0">
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
<link rel="stylesheet" href="../../appfortunar.css">
<style type="text/css">

</style>

</head>
<body>
<form id="target" action="subirperfil" method="POST" enctype="multipart/form-data" style='text-align: center;'>
<label for="archivo" >
	<img class='img-perfil' style="border: 2px double blue; padding:3px;"  width='150' height='150' src='perfiles/<?php echo readVendedor($_SESSION['user'])['PERFIL']?>' alt=''><br>
</label>
	<input style="display: none;" accept='image/*' type='file' id='archivo' name='archivo' accept=".jpg, .jpeg, .png, .webp" onchange="hola()">
	<label class="upload-btn" for="archivo" >Subir Foto</label>

</form>
<script>
 function hola(){
	 document.getElementById("target").submit();
 }
</script>
</body>
</html>
