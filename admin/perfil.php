<?php
/*
 *
 * PERFIL
 */
include 'admin.php';
date_default_timezone_set('America/Caracas');

?>
<html>
<div>
<script>
function cambiar(){
	document.getElementById('pass').style.background="#fff";
	document.getElementById('pass').value="";
	document.getElementById('pass').readOnly=false;
	document.getElementById('pass').focus();
}

function revision_p() {

}

function verificar(){
	$.post("perfil",
	{
	verificar: "",
	correo: document.getElementById('correo').value
	},
	function(data){
	});
	alert("Revisa tu Correo y confirma el Link que se te envio, luego de verificar actualiza la pagina de perfil..!");
	$('#frame').load("perfil");
}

function guardar(){
	if (document.getElementById('pass').value.length<8 && document.getElementById('pass').value >0 ) {
		alert("La contraseña Requiere ser de 8 o mas caracteres..!");
		document.getElementById('pass').focus();
	}
	else if (document.getElementById('perfilNombre').value.length<4) {
		alert("Debe Colocar un Nombre Real..!");
		document.getElementById('perfilNombre').focus();
	}
	else {
		  $.post("perfil",
		  {
		    guardarPerfil: "",
		    Nombre: document.getElementById('perfilNombre').value,
		    Telefono: document.getElementById('perfilTelefono').value,
		    password: document.getElementById('pass').value
		  },
		  function(data){
			  alert("Datos Guardados con Exito");
		  });
		  $('#frame').load("perfil");
	}
}
function cancelar() {
	$('#frame').load("perfil");
}
</script>
	<link rel="stylesheet" href="css/appfortunar.css">
	<div style='text-align: center;width:100%;display:inline-block; margin: 2.5rem 0 0; '>
		<span style='font-size:2em; color:black;'>Perfil</span><br>
	</div><br>
<?php

if(isset($_POST['verificar'])){
	ini_set( 'display_errors', 1 );
	error_reporting( E_ALL );
	$from = "activar@fortunaroyal.com";
	$to = $_POST['correo'];
	$subject = "FortunaRoyal";
	$message = "No Conteste este Email solo Copie y Pegue el Link en su navegador para activar Su cuenta: "."http://fortunaroyal.com/activar?email=".
	encrypt($_POST['correo'],'fortunaroyal1234567890_.');
	$headers = "From:" . $from;
	mail($to,$subject,$message, $headers);
}

if(isset($_POST['guardarPerfil'])){
	updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'NOMBRE',$_POST['Nombre']);
	updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'TELEFONO',$_POST['Telefono']);
	updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'APELLIDO',$_POST['apellido']);
	updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'NACIONALIDAD',$_POST['nacionalidad']);
	if(strlen($_POST['password'])>0)updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'PASSWORD',$_POST['password']);
}

if(readVendedor($_SESSION['user'])['ACTIVO']=='0'){
	$bo="<input class='ver_input ver_input-none' type='button' onclick='verificar();' value='Verificar'>";
}
else $bo="<input class='ver_input' type='button'  value='Verificado'>";
		echo "				
				<label style='margin:0 0;display:block;'>Correo:</label><input class='app_input' type='text' name='correo' id='correo' readonly value='".readVendedor($_SESSION['user'])['CORREO']."'>
				".$bo."<br>
				<br><label style='margin:0 0;display:block;'>Nombre:   </label><input class='app_input'  type='text' id='perfilNombre' value='".readVendedor($_SESSION['user'])['NOMBRE']."'>
				<br><label style='margin:0 0;display:block;'>Teléfono: </label><input class='app_input'  type='text' id='perfilTelefono' value='".readVendedor($_SESSION['user'])['TELEFONO']."'>
				<br>
				<br><label style='margin:0 0;display:block;'>Contraseña: </label><input class='app_input' id='pass' readonly type='text' placeholder='Introduce una nueva Contraseña'>
				<input class='newpass-input' type='button' onclick='cambiar()' value='Cambiar Contraseña'>
				";
				echo	"<br><br>
				<div class='button_container'>
				<button class='appbtn' onclick='guardar();'>Guardar Datos</button>
				<button class='appbtn appbtn-cancel' onclick='cancelar();'>Cancelar</button><br><br><br><br>
				</div><br><br>
				 ";
?>

</div>
</html>
