<?php
include '../../modulo.php';
date_default_timezone_set('America/Caracas');

if (isset($_GET['borrar'])){ //borrar metodo
	sqlconector("delete from USUARIOSMETODOS WHERE ID=".$_GET['id']);
}

if(isset($_POST['cerrado'])) {
	session_unset();
	session_destroy();
	header("Location: home.php");
}

if(isset($_POST['guardarPerfil'])){
	sqlconector("INSERT INTO USUARIOSMETODOS(CORREO,METODO,WALLET) VALUES('".$_POST['correo']."','".$_POST['metodo']."','".$_POST['wallet']."')");
}

if(isset($_POST['verificar'])){
	sqlconector("CREATE TABLE IF NOT EXISTS ACTIVATE(CORREO VARCHAR(34), REFERENCIA VARCHAR(255) )");
	$bytes = random_bytes(8);
	$referencia = bin2hex($bytes);
	sqlconector("INSERT INTO ACTIVATE(CORREO,REFERENCIA) VALUES('{$_POST['correo']}','{$referencia}')");

	ini_set( 'display_errors', 1 );
	error_reporting( E_ALL );
	$from = "activar@fortunaroyal.com";
	$to = $_POST['correo'];
	$subject = "FortunaRoyal";
	$message = "No Conteste este Email solo Copie y Pegue el Link en su navegador para activar Su cuenta:
	http://fortunaroyal.com/activar?ref={$referencia}";
	$headers = "From:" . $from;
	mail($to,$subject,$message, $headers);
}

if(isset($_POST['guardarPerfil'])){
	updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'NOMBRE',$_POST['Nombre']);
	updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'TELEFONO',$_POST['Telefono']);
	if(strlen($_POST['password'])>0)updateUsuario(readVendedor($_SESSION['user'])['CORREO'],'PASSWORD',$_POST['password']);
}
?>

<html style="overflow-y:auto;">
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<link rel="stylesheet" href="../../appfortunar.css">
<div style="padding:13px;">
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
				window.location.href="home.php";
		  });
	}
}
function cerrar() {
	$.post("perfil.php",
  {
 	 cerrado: ""
  },
  function(data){
 		window.location.href="home.php";
  });
}
function borrar(id){
	var txt;
	var r = confirm("Esta Seguro de Eliminar la Wallet?");
	if (r == true) {
		$.get("perfil.php?borrar&id="+id,
			function(data){
				window.location.href="perfil.php";
			}
		);
	} else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function selectMetodo(){
	if (document.getElementById("metodos").value=="BOLIVARES") {
		document.getElementById('labelwallet').innerHTML="Datos Bancarios: (En una sola linea Separado por Espacios)";
	}
	else if (document.getElementById("metodos").value=="TETHER") {
		document.getElementById('labelwallet').innerHTML="Wallet TRC20 Tron (TRX):";
	}
	else if (document.getElementById("metodos").value=="PAYPAL") {
		document.getElementById('labelwallet').innerHTML="Correo de PayPal Asociado:";
	}
	else if (document.getElementById("metodos").value=="AIRTM") {
		document.getElementById('labelwallet').innerHTML="Correo de Airtm Asociado:";
	}
	else {
		document.getElementById('labelwallet').innerHTML="Direccion de la Wallet:";
	}
	document.getElementById('labelwallet').style.display='block';
	document.getElementById('wallet').style.display='block';
	document.getElementById('add').type='button';
	document.getElementById('cancel').type='button';
}
function agregar(){
		  $.post("perfil.php",
		  {
		    guardarPerfil: "",
		    metodo: document.getElementById('metodos').value,
		    correo: document.getElementById('correo').value,
		    wallet: document.getElementById('wallet').value
		  },
		  function(data){
				window.location.href="perfil.php";
		  });
}
function cancelar() {
	window.location.href="perfil.php";
}
</script>
<div style='text-align: center;width:100%;display:inline-block; margin: 2.5rem 0 0; '>
<span style='font-size:2em; color:black;'>Perfil</span><br>
</div><br>
<?php
if(isset($_SESSION['user'])){
	if(readVendedor($_SESSION['user'])['ACTIVO']=='0'){
		$bo="<input class='ver_input ver_input-none' type='button' onclick='verificar();' value='Verificar'>";
	}
	else $bo="<input class='ver_input' style='background:green' type='button'  value='Verificado'>";
	echo "
				<iframe style='border:none; width:100%; height: 250px;' src='subirperfil'></iframe>
				<br>
				<label style='margin:0 0;display:block;'>Correo:</label><input class='app_input' type='text' name='correo' id='correo' readonly value='".readVendedor($_SESSION['user'])['CORREO']."'>
				".$bo."<br>
				<br><label style='margin:0 0;display:block;'>Nombre:   </label><input class='app_input'  type='text' id='perfilNombre' value='".readVendedor($_SESSION['user'])['NOMBRE']."'>
				<br><label style='margin:0 0;display:block;'>Teléfono: </label><input class='app_input'  type='text' id='perfilTelefono' value='".readVendedor($_SESSION['user'])['TELEFONO']."'>
		<br><label style='margin:0 0;display:block;'>Contraseña: </label><input class='app_input' id='pass' readonly type='text' placeholder='Introduce una nueva Contraseña'>
		<input class='newpass-input' type='button' onclick='cambiar()' value='Cambiar Contraseña'>
		<br>
	<div class='button_container'>
	<br>
		<button class='appbtn' onclick='guardar();'>Guardar Datos</button> <br><br><br><br>
	</div>
				 ";
}
?>
</div>
</html>
