<?php

/*
 * App Admin FortunaRoyal
 * Version 1.6.0
 * (c)2020
 *
 * */

	include 'admin.php';
	date_default_timezone_set('America/Caracas');
	setPatrimonio();

	if(isset($_SESSION['user'])){
		createAmo(readVendedor($_SESSION['user'])['CORREO']);
		activarChat(readVendedor($_SESSION['user'])['CORREO'],'1');
	}
	else{
		activarChat(readVendedor($_SESSION['user'])['CORREO'],'0');
		header("Location: index");
	}

	if(ifReadPromo()&&!isset($_COOKIE['promo'])){
		setcookie("promo","1",time()+3600);
	}

?>

<html lang="es">
<head>
	<link rel="shortcut icon" href="../Feed/Home/favicon.png">
  <title>FortunaRoyal Admin</title>
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="theme-color" content="#ffffff">
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
   <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
   <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
   <script src="js/modulo.js"></script>
   <link rel="stylesheet" href="css/appfortunar.css">

<style>
#preloader {
position: absolute;
top: 0;
left: 0;
right: 0;
bottom: 0;
z-index: 99;
display: none;

}
.charge_logo{
display:flex;
justify-content:center;
align-items:center;
}

.charge_word{
color:white;
font-size:80px;
margin:auto;
top:50%;
position:absolute;
font-family: 'Bebas Neue', cursive;
}

.loader {
border: 16px solid #D4D4D4;
border-radius: 50%;
border-top: 16px solid gray;
width: 120px;
height: 120px;
-webkit-animation: spin 2s linear infinite; /* Safari */
animation: spin 2s linear infinite;
}

</style>

<script type="text/javascript" >

function nivelito(){
	if(document.getElementById("token_bloqueado").value == 0) $("#descrip").html("Desbloqueado");
	if(document.getElementById("token_bloqueado").value == 1) $("#descrip").html("Mantenimiento");
	if(document.getElementById("token_bloqueado").value == 2) $("#descrip").html("Bloqueado");
}

var nmenu=0;
function inicio(){
	myVar = setInterval(myTimer, 3000);
}

function myTimer() {
	try {
		actNotif("<?php echo readVendedor($_SESSION['user'])['CORREO'] ?>");
		actSaldo("<?php echo readVendedor($_SESSION['user'])['CORREO'] ?>");

		if (nmenu===1) {
			$.post("admin",{
				verChatApp: document.getElementById('ticked').value
			},function(data) {
				$("#chat").html(data);
			});
		}

		if (nmenu===2) {
			$.post("admin",{
				verChatApp: document.getElementById('ticked').value
			},function(data) {
				$("#chat").html(data);
			});
		}
	}catch (err) {

	}
}

function myStopFunction() {
  try {
		clearInterval(myVar);
  }
  catch (err) {

  }
}

function adminTrabajo(){
	$.get("admin?vertrabajocajero&lista",
	function(data) {
	$('#frame').html(data);
	});
	nmenu=1;
	myStopFunction();
	inicio();
}

function pdosp(){
	$.get("admin?verTrabajoPdosP&lista",
	function(data) {
	$('#frame').html(data);
	});
	nmenu=1;
	myStopFunction();
	inicio();
}

function verPdosP(ticket){
	$.get("admin?verChatPdosP&tk="+ticket,
	function(data) {
	$('#frame').html(data);
	});
	nmenu=1;
	myStopFunction();
	inicio();
}

function historia(){
	$.get("admin?verhistorial&historial",
	function(data) {
	$('#frame').html(data);
	});
	nmenu=2;
	myStopFunction();
	inicio();
}

function fondeo(){
$('#frame').load("fondeo?metodo&tipo=fondeo&inicio");
nmenu=3;
myStopFunction();
}
function retiro(){
$('#frame').load("fondeo?metodo&tipo=retiro&inicio");
nmenu=4;
myStopFunction();
}
function metodosUser(){
$('#frame').load("metodos");
nmenu=5;
myStopFunction();
}
function referidos(){
$('#frame').load("referidos");
nmenu=6;
myStopFunction();
}
function perfil(){
$('#frame').load("perfil");
nmenu=7;
myStopFunction();
}
function datos(){
$('#frame').load("panel?start&perfil");
nmenu=8;
myStopFunction();
}
function token(){
$('#frame').load("panel?token&start&c=2&lista");
nmenu=14;
myStopFunction();
}
function metodos(){
$('#frame').load("panel?metodo&start&c=2&lista");
nmenu=9;
myStopFunction();
}
function usuarios(){
$('#frame').load("panel?start&clientes&c=2&lista");
nmenu=10;
myStopFunction();
}
function bloqueos(){
$('#frame').load("panel?start&bloque&c=0");
nmenu=11;
myStopFunction();
}
function mensajes(){
$('#frame').load("panel?start&pops1&c=0");
nmenu=12;
myStopFunction();
}
function wallet(){
	$.get("admin?miswallet",
	function(data) {
	$('#frame').html(data);
	});
	nmenu=13;
	myStopFunction();
}
function marcar(correo){
	  var correo=<?php echo "\"".readVendedor($_SESSION['user'])['CORREO']."\""; ?>;
	  $.post("admin",
	  {
	    marcarNotif: "Donald",
	    correo: correo,
	    email: correo
	  },
	  function(data){
	  	actNotif(correo);
	  });
}

function close_session(){
	$.get("admin?close",
		function(data){
			window.location.href="index";
		}
	);
}

$(document).ready(main);

var contador = 1;

function main () {
$('.bt-menu').click(function(){
if (contador == 1) {
$('nav').animate({
left: '0'
});
contador = 0;
} else {
contador = 1;
$('nav').animate({
left: '-100%'
});
}
});

$('.submenu').click(function(){
$(this).children('.children').slideToggle();
});

$('nav ul li').click(function(){
	contador = 1;
	$('nav').animate({
	left: '-100%'
	});
});
}

function validarCajero(){
  var correo=<?php echo "\"".readVendedor($_SESSION['user'])['CORREO']."\""; ?>;
  $.post("admin",
  {
    validarCajero: "Donald",
    tipo: document.getElementById('tipo').value,
    refe: document.getElementById('refe').value,
    tk: document.getElementById('tk').value,
		monto: document.getElementById('vaca').value,
    email: document.getElementById('email').value
  },
  function(data){
	  actSaldo(correo);
  });
  $('#frame').load("admin?vertrabajocajero&lista");
}

function enviado(){
  var correo=<?php echo "\"".readVendedor($_SESSION['user'])['CORREO']."\""; ?>;
  $.post("admin",
  {
    enviado: "Biden",
    tipo: document.getElementById('tipo').value,
    refe: document.getElementById('refe').value,
    tk: document.getElementById('tk').value,
    email: document.getElementById('email').value
  },
  function(data){
	  actSaldo(correo);
  });
  $('#frame').load("admin?vertrabajocajero&lista");
}

function revision(id){
$.get("admin?vertrabajocajero&rep&tk="+ id +"&revision",
function(data){
	$('#frame').load("admin?vertrabajocajero&lista");
});
}

function irChat(id){
	$.get("admin?vertrabajocajero&open&chat&tickedchat="+id,
	function(data){
		$('#frame').load("admin?vertrabajocajero&open&chat&tickedchat="+id);
	});
}

function irTicket(sujeto,tipo,ticked,email){
	$.get("admin?vertrabajocajero&open&sujeto="+sujeto+"&tipo="+tipo+"&ticked="+ticked+"&email="+email+"&tickedchat="+ticked,
	function(data){
		$('#frame').load("admin?vertrabajocajero&open&sujeto="+sujeto+"&tipo="+tipo+"&ticked="+ticked+"&email="+email+"&tickedchat="+ticked);
	});
}
function borrar(id){
	var txt;
	var r = confirm("Alerta..! Esta Seguro de Cancelar la Operacion Ticket "+id+"....?");
	if (r == true) {
		$.get("admin?vertrabajocajero&rep&cancelar&tk="+id,
		function(data){
			$('#frame').load("admin?vertrabajocajero&lista");
		});
	} else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function chat(){
	  $.post("admin",
	  {
	    insertchat: "Donald",
	    tickedchat: document.getElementById('ticked').value,
	    email: document.getElementById('envia').value,
	    mensaje: document.getElementById('mensaje').value
	  },
	  function(data){
		  document.getElementById("mensaje").value="";
		  document.getElementById("mensaje").focus();
	  });
}

function myFunction(event){
	var x = event.key;
	if (x == "Enter" || x == "Intro"){
		chat();}
}

function cerrarchat(){
	  $.post("admin",
	  {
	    cerrarchat: "Donald",
	    tickedchat: document.getElementById('ticked').value,
	    email: document.getElementById('envia').value,
	    mensaje: document.getElementById('mensaje').value
	  },
	  function(data){
	  	alert("Chat Cerrado...");
	  });
}

function uncheck() {
    document.getElementById("toggle").checked = false;
}

function marcarEnvio(tk){
	var r = confirm("Confirma que envio el Pago...?");
	if (r == true) {
		$.post("admin",{
			marcarEnvio: tk
		},function(data){
			pdosp();
		});
	}
	else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function cancelar(tk){
	var r = confirm("Esta Seguro que desea Cancelar la Orden...?");
	if (r == true) {
		$.post("admin",{
			cancelarVenta: tk
		},function(data){
			pdosp();
		});
	}
	else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function recibido(tk){
	var r = confirm("Deseas Marcar como Pago Recibido la Orden..?");
	if (r == true) {
		$.post("admin",{
			recibirCompra: tk
		},function(data){
			pdosp();
		});
	}
	else {
	  /*txt = "You pressed Cancel!";*/
	}
}
</script>
</head>
<body scroll="no">
  <?php
  if(ifReadPromo()&&!isset($_COOKIE['promo'])){echo readPromo(); }
  if(countNotif(readVendedor($_SESSION['user'])['CORREO'])>0){/*sonido si hay notificaciones*/}
    ?>
    <header>
		<div class="nav2" style="height: 79px;">
		<div class="logo">
		</div>

			<ul>
				<li id='dissapear'>
					<label>
						<?php echo readVendedor($_SESSION['user'])['NOMBRE'] . makeAnciEstrellas(readVendedor($_SESSION['user'])['RATE']);?>
					</label>
				</li>
				<li id='dissapear'><label>Balance </label></li>
				<li id='dissapear'>
					<label>
						<div type='text' id='textbox' class="saldo"><?php echo number_format(readVendedor($_SESSION['user'])['SALDO'],2,",",".")." <span style='color:white; font-size:13px; '>FRD</span>";?></div>
					</label>
				</li>
				<label for='toggle' id='campana' onclick='notifread()'>
					<i class='fas fa-concierge-bell'></i>
				</label>
				<input type='checkbox' id='toggle'/>
				<div class='notification'>
					<ul id='notif'>
					<?php
						echo notif(readVendedor($_SESSION['user'])['CORREO']);
					?>
					</ul>
				</div>
				<div class="bt-menu"><i class='fas fa-bars'></i></div>
				<li class="n" onclick="close_session();" style='cursor:pointer;' id="dissapear">
					Cerrar Sesión
				</li>
			</ul>
		</div>
    <nav>
		<ul>
			<li id='appear' class='user_li'>
				<a>
					<label> 	<?php echo readVendedor($_SESSION['user'])['NOMBRE']?>	</label>
					<br>
					<label>
						<div type='text' readonly id='textbox'  style='display: inline-block; margin-left:85px; position:relative;'  class="saldo">
							<?php echo number_format(readVendedor($_SESSION['user'])['SALDO'],2,",",".")." <span style='color:white; font-size:13px; '>FRD</span>";?>
						</div>
					</label>
				</a>
			</li>
			<?php
			if (readVendedor($_SESSION['user'])['NIVEL']=='2'){
				echo "
				<li style=\"cursor: pointer;\" class='n'onclick=\"adminTrabajo();\" >Trabajos</li>
				<li style=\"cursor: pointer;\" class='n' onclick=\"datos();\" >Datos</li>
				<li style=\"cursor: pointer;\" class='n' onclick=\"token();\" >Comercios</li>
				<li style=\"cursor: pointer;\" class='n' onclick=\"metodos();\" >Método de Pago</li>
				<li style=\"cursor: pointer;\" class='n' onclick=\"usuarios();\" >Usuarios</li>
				<li style=\"cursor: pointer;\" class='n' onclick=\"bloqueos();\" >Bloqueos</li>
				<li style=\"cursor: pointer;\" class='n' onclick=\"mensajes();\" >Mensajes POPS</li>
				<li style=\"cursor: pointer;\" class='n' onclick=\"wallet()\" >Mis Wallet</li>
				";
			}
			?>
			<?php
				if(readVendedor($_SESSION['user'])['NIVEL']=='1'){
						echo "
						<li style=\"cursor: pointer;\" class='n'onclick=\"pdosp();\" >Trabajos</li>
						<li style=\"cursor: pointer;\" onclick=\"metodosUser()\" >Métodos Compra/Venta</li>
						";
				}
			?>

			<li style="cursor: pointer;" onclick="historia();">Historial</li>
			<li style="cursor: pointer;" class="n" onclick="perfil();">Perfil</li>
			<li style="cursor: pointer;" class="n" onclick="close_session();" id='appear'>
				Cerrar Sesion
			</li>
		</ul>
	</nav>
  </header>
  <div class="pantalla" id='frame' onclick="uncheck();">
		<div id='preloader'>
			<div class='charge_logo'>
				<div class='charge_word'>
					<div class='loader'></div>
				</div>
			</div>
		</div>
	</div>
	<?php
		if (readVendedor($_SESSION['user'])['NIVEL'] == 1) echo "<script>pdosp();</script>";
		if (readVendedor($_SESSION['user'])['NIVEL'] == 2) echo "<script>adminTrabajo();</script>";
	?>
<script>
document.getElementById("preloader").style.display='block';
window.onload= function(){ $('#preloader').fadeOut('slow');}
</script>
</body>
</html>
