<?php
/*
 * PANEL ADMINISTRADOR FortunaRoyal
 * Version 1.6.1
 * (c)2020
 * ultima modificacion (06/05/2022)
 * */
include 'admin.php';

date_default_timezone_set('America/Caracas');

if(isset($_POST['deletecomercio'])){
	updateSaldo(readCliente(readTokenID($_POST['pipe'])['CORREO'])['SALDO']+readTokenID($_POST['pipe'])['PATRIMONIOINICIAL'],readTokenID($_POST['pipe'])['CORREO']);
	updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO']-readTokenID($_POST['pipe'])['PATRIMONIOINICIAL'],"fortuna@fortunaroyal.com");
	insertNotif(readTokenID($_POST['pipe'])['CORREO'],"Devolucion, Token ".readTokenID($_POST['pipe'])['TOKEN']." No Autorizado","");
	
	$conexion = mysqli_connect(servidor(),user(),password(),database());
	$consulta="delete from WALLETOKEN WHERE TOKEN='".readTokenID($_POST['pipe'])['TOKEN']."'";
	$resultado = mysqli_query( $conexion, $consulta );
	$imagen="../Feed/Book/comercios/".readTokenID($_POST['pipe'])['IMAGEN'];
	if(file_exists ($imagen))unlink($imagen);
	$consulta="delete from TOKEN WHERE ID=".$_POST['pipe'];
	$resultado = mysqli_query( $conexion, $consulta );
	mysqli_close($conexion);
}

if (isset($_POST['enviar_correo'])){
	ini_set( 'display_errors', 1 );
	error_reporting( E_ALL );
	$from = "soporte@fortunaroyal.com";
	$to = $_POST['t_correo'];
	$subject = $_POST['t_asunto'];;
	$message = $_POST['t_mensaje'];
	$headers = "From:" . $from;
	mail($to,$subject,$message, $headers);
}

if (isset($_POST['guardar_empresa'])){
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database() );

	$consulta="UPDATE FORTUNA SET ".
		  "WALLET='".$_POST['t_wallet'].
		"',CORREO='".$_POST['t_correo'].
		"',WEB='".$_POST['t_web'].
		"',DEPOSITOS='".$_POST['t_depositos'].
		"',RETIROS='".$_POST['t_retiros'].
		"',SOPORTE='".$_POST['t_soporte'].
		"',INTERESPRESTAMO=".$_POST['interes'].
		",COMISIONRETORNO=".$_POST['retorno'].
		",COMISIONEMISION=".$_POST['emision'].
		",PORCEN=".$_POST['porcen']."";
	$resultado = mysqli_query( $conexion, $consulta ) or die ( "ERROR: Datos de en table Fortuna." );
	mysqli_close($conexion);
}

if (isset($_POST['add_bloqueo'])){
	insertBloqueoJuego($_POST['t_vjuego'],$_POST['t_vdesde'],$_POST['t_vhasta']);
}

if (isset($_POST['add_pops'])){
	insertPromo($_POST['t_vfecha'],$_POST['t_vmensaje'],$_POST['t_vbg'],$_POST['t_vfg'],$_POST['t_vborder'],$_POST['t_vubicacion']);
}

if (isset($_POST['save_bloqueo'])){
	editBloqueoJuego($_POST['t_vjuego'],$_POST['t_vdesde'],$_POST['t_vhasta']);
}

if (isset($_POST['save_pops'])){
	editPromo($_POST['id'],$_POST['t_vmensaje'],$_POST['t_vbg'],$_POST['t_vfg'],$_POST['t_vborder']);
}

if (isset($_POST['save_token'])){
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database());
	$consulta= "update TOKEN SET ".
	"  TOKEN='".strtoupper($_POST['token']).
	"',NOMBRE='".$_POST['nombre'].
	"',CEO='".$_POST['ceo'].
	"',TELEFONO='".$_POST['telefono'].
	"',INSTAGRAM='".$_POST['instagram'].
	"',CORREO='".$_POST['correo'].
	"',DESCRIPCION='".$_POST['descripcion'].
	"',IMAGEN='".$_POST['imagen'].
	"',VALOR=".$_POST['valor'].
	",PATRIMONIO=".$_POST['patrimonio'].
	",MAXSUPPLY=".$_POST['maxsupply'].
	",VOLUMEN=".$_POST['volumen'].
	",RATE=".$_POST['rate'].
	",BLOQUEADO=".$_POST['bloqueado'].
	" WHERE ID = ".$_POST['t_id'];
	$resultado = mysqli_query( $conexion, $consulta ) or die ( "Error al Guardar token");
	mysqli_close($conexion);
	if($_POST['bloqueado']==0){
		sqlconector("UPDATE WALLETOKEN SET BLOQUEADO=0 WHERE CORREO='".$_POST['correo']."' AND TOKEN='".strtoupper($_POST['token'])."'");
		sqlconector("UPDATE WALLETOKEN SET BLOQUEADO=0 WHERE CORREO='fortuna@fortunaroyal.com' AND TOKEN='".strtoupper($_POST['token'])."'");
	}
	else {
		sqlconector("UPDATE WALLETOKEN SET BLOQUEADO=1 WHERE CORREO='".$_POST['correo']."' AND TOKEN='".strtoupper($_POST['token'])."'");
	}
}

if (isset($_POST['add_metodo'])){
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database());
	$consulta="insert into METODOS (
	METODO,
	COSTO,
	PRECIO1,
	PRECIO2,
	PORCEN1,
	PORCEN2,
	PERFIL,
	BLOQUEADO) values ('"
	.$_POST['t_metodo']."',"
	.$_POST['t_costo'].","
	.$_POST['t_pventa'].","
	.$_POST['t_pcompra'].","
	.$_POST['t_porcenVenta'].","
	.$_POST['t_porcenCompra'].",'"
	.$_POST['t_perfil']."',"
	.$_POST['t_bloqueado'].")";
	$resultado = mysqli_query( $conexion, $consulta ) or die ( "Error al insertar Metodo" );
	mysqli_close($conexion);
}

if (isset($_POST['save_metodo'])){
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database());
	$consulta= "update METODOS SET ".
	"  METODO='".$_POST['t_metodo'].
	"',COSTO=".$_POST['t_costo'].
	",PRECIO1=".$_POST['t_pventa'].
	",PRECIO2=".$_POST['t_pcompra'].
	",PORCEN1=".$_POST['t_porcenVenta'].
	",PORCEN2=".$_POST['t_porcenCompra'].
	",PERFIL='".$_POST['t_perfil'].
	"',BLOQUEADO=".$_POST['t_bloqueado'].
	" WHERE ID = ".$_POST['t_id'];
	$resultado = mysqli_query( $conexion, $consulta ) or die ( "Error al Guardar Metodo");
	mysqli_close($conexion);
}

if (isset($_POST['save_cliente'])){
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database());
	$consulta= "update USUARIOS SET ".
	"  NOMBRE='".$_POST['t_vnombre'].
	"',NACIONALIDAD='".$_POST['t_vnacionalidad'].
	"',TELEFONO='".$_POST['t_vpago'].
	"',CORREO='".$_POST['t_vcorreo'].
	"',PASSWORD='".$_POST['t_vpassword'].
	"',WALLET='".$_POST['t_vwallet'].
	"',APIKEY='".$_POST['t_vapikey'].
	"',NIVEL=".$_POST['t_vsuper'].
	",RATE=".$_POST['t_vrate'].
	",ACTIVO=".$_POST['t_vactivo'].
	" WHERE ID = ".$_POST['t_id'];
	$resultado = mysqli_query( $conexion, $consulta ) or die ( "Error al Guardar Cliente");
	mysqli_close($conexion);
}

if(isset($_POST['retirar'])){
	sqlconector("INSERT INTO LIBROCONTABLE(OPERACION,TIPO,MONEDA,MONTO) VALUES('RETI','RETIRO','FRD',".$_POST['monto'].")");
	setPatrimonio();
}

if (isset($_GET['borrarbloqueo'])){
	deleteBloqueoJuego($_GET['juego']);
}

if (isset($_GET['borrarpops'])){
	deletePromo($_GET['id']);
}

if (isset($_GET['borrar'])){ //borrar Usuario
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database());

	$consulta="delete from USUARIOS WHERE ID=".$_GET['id'];
	$resultado = mysqli_query( $conexion, $consulta );
	mysqli_close($conexion);
}

if (isset($_GET['borrarmetodo'])){ //borrar Metodo de Pago
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database());
	$consulta="delete from METODOS WHERE ID=".$_GET['id'];
	$resultado = mysqli_query( $conexion, $consulta );
	mysqli_close($conexion);
}


?>

<html>
<head>
<style>
	.relative {
	top:0;
	position: absolute;
	width: 100%;
	height: 100%;
	background:rgba(0, 0, 0, 0.5);
	z-index:1000;
	}

	.relative-absolute {
	top: 50%;
	right: 5%;
	width: 344px;
	height: 233px;
	border: 3px solid #FF0000;
	border-radius:6px;
	background:#FF96A8;
	box-shadow: 0 2px 12px rgb(0, 0, 0);
	opacity:0.8;
	}
	input[type=text], input[type=password], input[type=number], input[type=email]{
	width: 55%;
	padding: 5px;
	display: block;
	box-sizing: border-box;
	border: none;
	margin: 5px;
	background: #EAEDED;
	color:black;
	}
	.btn{
	padding: 15px 60px;
	cursor: pointer;
	color:white;
	border-radius: 4px;
	font-size: 12px;
	}
	.btn-jugar{
	color: #fff ;
	background: #FF4F43;
	font-size: 1em;
	padding: 8px 60px;
	margin-right: 20px;
	border-radius:8px;
	}
	.btn_apostar{
	color:black ;
	background-color: #ea4439;
	box-shadow: 2px 2px 40px -6px #ea4439;
	font-size: 60px;
	font-weight: bolder;
	border: none;
	padding: 0px 60px;
	font-family: movie_poster;
	letter-spacing: 5px;
	width: 60%;
	height: 80px;
	}
	.btn_apostar-ticket{
	background: #e9bf3a;
	padding: 0px 40px;
	box-shadow: 2px 2px 40px -6px #e9bf3a;
	}
	.btn-jugar:hover{
	color:#fff;
	background-color: #E83C3C;
	}
	.btn-ayuda{
	color:#fff;
	background-color: #797D7F;
	font-weight: bolder;
	font-size: 1em;
	padding: 14px 40px;
	}
	.btn-ayuda:hover{
	color:#fff;
	background-color:  #626567;
	}
	.btn-verde{
	color:#ffe3b5;
	background-color:#71a364;
	font-size: 34px;
	font-weight: bolder;
	padding: 0px 60px;
	-webkit-box-shadow: 12px 12px 0px 11px #36794c;
	box-shadow: 8px 10px 0px 3px #36794c ;border-radius: 0;
	border: none;
	font-family: movie_poster;
	letter-spacing: 5px;
	}
	.btn-verde:hover{
	color:#ffebbb;
	-webkit-animation: glow 1s ease-in-out infinite alternate;
	-moz-animation: glow 1s ease-in-out infinite alternate;
	animation: glow 1s ease-in-out infinite alternate;
	}
	.btn-ticket {
	min-width: 25%;
	background-color:#71a364;
	font-size: 34px;
	font-weight: bolder;
	padding: 0px 30px;
	-webkit-box-shadow: 12px 12px 0px 11px #36794c;
	box-shadow: 8px 10px 0px 3px #36794c ;border-radius: 0;
	border: none;
	font-family: movie_poster;
	letter-spacing: 5px;
	color:#ffe3b5;
	margin:10px 10px;
	}
	.btn-ticket:hover{
	color:#ffebbb;
	-webkit-animation: glow 1s ease-in-out infinite alternate;
	-moz-animation: glow 1s ease-in-out infinite alternate;
	animation: glow 1s ease-in-out infinite alternate;
	}
	.btn-ticket a{
	text-decoration: none;
	color:#ffe3b5;
	}
	.btn-form{
	color:#fff;
	background-color: #E72828;
	font-size: 1em;
	font-weight: bolder;
	padding: 14px 50px;
	}
	.btn-form:hover{
	color:#fff;
	background-color:#F11D1D;
	}
	.btn-form2{
	color:#ffe3b5;
	background-color:#d14544;
	font-size: 5em;
	font-weight: bolder;
	padding: 14px 10px;
	-webkit-box-shadow: 12px 12px 0px 11px #8d2731;
	box-shadow: 8px 10px 0px 3px #8d2731 ;border-radius: 0;
	border: none;
	font-family: movie_poster;
	letter-spacing: 5px;
	margin:auto 20px;
	}
	.btn-form2:hover{
	color:#ffebbb;
	-webkit-animation: glow 1s ease-in-out infinite alternate;
	-moz-animation: glow 1s ease-in-out infinite alternate;
	animation: glow 1s ease-in-out infinite alternate;
	}
</style>

</head>
<div>
<script>
function borrar_Token(tid){
	if (confirm("Alerta Cuidado..! Esta Seguro de Eliminar el Comercio?")) {
		$.post("panel",
		{
			deletecomercio: "",
			pipe: tid
		},
		function(data){
			alert("Comercio Borrado, se ha hecho la devolucion.");
			$('#frame').load("panel?start&token&c=2&lista");
		});		
	}else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function guardar_empresa(){
$.post("panel",
{
guardar_empresa: "",
t_wallet: document.getElementById('t_wallet').value,
t_correo: document.getElementById('t_correo').value,
t_depositos: document.getElementById('t_depositos').value,
t_retiros: document.getElementById('t_retiros').value,
t_soporte: document.getElementById('t_soporte').value,
t_web: document.getElementById('t_web').value,
interes: document.getElementById('interes').value,
retorno: document.getElementById('retorno').value,
emision: document.getElementById('emision').value,
porcen: document.getElementById('porcen').value
},
function(data){
	alert("Datos Guardados con Exito..!");
});
$('#frame').load("panel?start&perfil");
}

function save_token(){
	$.post("panel",
	{
	save_token: "",
	t_id: document.getElementById('t_id').value,
	ceo: document.getElementById('token_ceo').value,
	telefono: document.getElementById('token_telefono').value,
	instagram: document.getElementById('token_instagram').value,
	token: document.getElementById('token_token').value,
	nombre: document.getElementById('token_nombre').value,
	correo: document.getElementById('token_correo').value,
	descripcion: document.getElementById('token_descripcion').value,
	imagen: document.getElementById('token_imagen').value,
	valor: document.getElementById('token_valor').value,
	patrimonio: document.getElementById('token_patrimonio').value,
	maxsupply: document.getElementById('token_maxsupply').value,
	volumen: document.getElementById('token_volumen').value,
	rate: document.getElementById('token_rate').value,
	bloqueado: document.getElementById('token_bloqueado').value
	},
	function(data){});
	$('#frame').load("panel?start&token&c=2&lista");
}


function add_metodo(){
$.post("panel",
{
add_metodo: "",
t_metodo: document.getElementById('t_metodo').value,
t_costo: document.getElementById('t_costo').value,
t_porcenVenta: document.getElementById('t_porcenVenta').value,
t_pventa: document.getElementById('t_pventa').value,
t_porcenCompra: document.getElementById('t_porcenCompra').value,
t_pcompra: document.getElementById('t_pcompra').value,
t_perfil: document.getElementById('t_perfil').value,
t_bloqueado: document.getElementById('t_bloqueado').value
},
function(data){});
$('#frame').load("panel?start&metodo&c=2&lista");
}

function save_metodo(){
$.post("panel",
{
save_metodo: "",
t_id: document.getElementById('t_id').value,
t_metodo: document.getElementById('t_metodo').value,
t_costo: document.getElementById('t_costo').value,
t_porcenVenta: document.getElementById('t_porcenVenta').value,
t_pventa: document.getElementById('t_pventa').value,
t_porcenCompra: document.getElementById('t_porcenCompra').value,
t_pcompra: document.getElementById('t_pcompra').value,
t_perfil: document.getElementById('t_perfil').value,
t_bloqueado: document.getElementById('t_bloqueado').value
},
function(data){});
$('#frame').load("panel?start&metodo&c=2&lista");
}

function save_cliente(){
$.post("panel",
{
save_cliente: "",
t_id: document.getElementById('t_id').value,
t_vnombre: document.getElementById('t_vnombre').value,
t_vnacionalidad: document.getElementById('t_vnacionalidad').value,
t_vpago: document.getElementById('t_vpago').value,
t_vcorreo: document.getElementById('t_vcorreo').value,
t_vpassword: document.getElementById('t_vpassword').value,
t_vwallet: document.getElementById('t_vwallet').value,
t_vapikeyt: document.getElementById('t_vapikey').value,
t_vrate: document.getElementById('t_vrate').value,
t_vsuper: document.getElementById('t_vsuper').value,
t_vactivo: document.getElementById('t_vactivo').value,
t_vsaldo: document.getElementById('t_vsaldo').value,
t_vperfil: document.getElementById('t_vperfil').value
},
function(data){ alert("Datos Guardados..!"); });
$('#frame').load("panel?start&clientes&c=2&lista");
}

function enviar_correo(){
$.post("panel",
{
enviar_correo: "",
t_correo: document.getElementById('t_correo').value,
t_asunto: document.getElementById('t_asunto').value,
t_mensaje: document.getElementById('t_mensaje').value
},
function(data){});
$('#frame').load("panel?start&clientes&c=2&lista");
}

function add_bloqueo(){
$.post("panel",
{
add_bloqueo: "",
t_vjuego: document.getElementById('t_vjuego').value,
t_vdesde: document.getElementById('t_vdesde').value,
t_vhasta: document.getElementById('t_vhasta').value
},
function(data){});
$('#frame').load("panel?bloque");
}

function save_bloqueo(){
$.post("panel",
{
save_bloqueo: "",
t_vjuego: document.getElementById('t_vjuego').value,
t_vdesde: document.getElementById('t_vdesde').value,
t_vhasta: document.getElementById('t_vhasta').value
},
function(data){});
$('#frame').load("panel?bloque");
}

function add_pops(){
$.post("panel",
{
add_pops: "",
t_vfecha: document.getElementById('t_vfecha').value,
t_vbg: document.getElementById('t_vbg').value,
t_vfg: document.getElementById('t_vfg').value,
t_vborder: document.getElementById('t_vborder').value,
t_vmensaje: document.getElementById('t_vmensaje').value,
t_vubicacion: document.getElementById('t_vubicacion').value
},
function(data){

});
$('#frame').load("panel?start&pops1&c=0");
}

function save_pops(){
$.post("panel",
{
save_pops: "",
t_vfecha: document.getElementById('t_vfecha').value,
t_vbg: document.getElementById('t_vbg').value,
t_vfg: document.getElementById('t_vfg').value,
t_vborder: document.getElementById('t_vborder').value,
id: document.getElementById('promoid').value,
t_vmensaje: document.getElementById('t_vmensaje').value
},
function(data){

});
$('#frame').load("panel?start&pops1&c=0");
}

function calculo(){
	var total;
	total=document.getElementById('monto').value * 60;
	document.getElementById('pagado').value=total.toFixed(2);
}

function calcular(){
	var precioVenta=0;
	var precioCompra=0;
	var porcenVenta=0;
	var porcenCompra=0;
	porcenVenta=(document.getElementById('t_costo').value * document.getElementById('t_porcenVenta').value)/100;
	porcenCompra=(document.getElementById('t_costo').value * document.getElementById('t_porcenCompra').value)/100;
	precioVenta= (document.getElementById('t_costo').value*1)+porcenVenta;
	precioCompra= (document.getElementById('t_costo').value*1)-porcenCompra;
	document.getElementById('t_pventa').value=precioVenta;
	document.getElementById('t_pcompra').value=precioCompra;
}

function valorLiquido(){
	var total;
	total=document.getElementById('token_patrimonio').value / document.getElementById('token_volumen').value;
	document.getElementById('token_valor').value = total.toFixed(4);
}

function retirar(){
	let text;
  let amount = prompt("Please enter your amount:", "0");
  if (amount != null || amount != "") {
    $.post("panel.php",{
			retirar:"",
			monto:amount
		},function(data){
			alert("Retiro Completado...!");
		});
  }
}

</script>

  <script>
        $(document).ready(function () {
            $('#tablax').DataTable({
                language: {
                    processing: "Tratamiento en curso...",
                    search: "Buscar&nbsp;:",
                    lengthMenu: "Agrupar de _MENU_ items",
                    info: "Mostrando del item _START_ al _END_ de un total de _TOTAL_ items",
                    infoEmpty: "No existen datos.",
                    infoFiltered: "(filtrado de _MAX_ elementos en total)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron datos con tu busqueda",
                    emptyTable: "No hay datos disponibles en la tabla.",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Ultimo"
                    },
                    aria: {
                        sortAscending: ": active para ordenar la columna en orden ascendente",
                        sortDescending: ": active para ordenar la columna en orden descendente"
                    }
                },
                scrollY: 400,
                lengthMenu: [ [20, 25, -1], [20, 25, "All"] ],
            });
        });
    </script>

    <div style='overflow-x: hidden; overflow-y: hidden; width: 100%;'>
<?php

if (isset($_SESSION['admin'])){

	if (isset($_GET['bloqueos'])){
		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
			<span style='font-size:2em; color:black;'>Bloqueos</span><br>
			<a onclick=\"$('#frame').load('panel?bloqueos&c=0');\" >Editar</a>
			<div style='display:inline-block;padding-left:55px;'></div>
			<a onclick=\"$('#frame').load('panel?bloque');\">Lista</a></div>";
	    if($_GET['c']=="0"){
	      echo "
	      <div>
	      <label>Juego: </label><input style='display:inline-block; width:34%;' type='text' id='t_vjuego'>
	      <br><label>Desde: </label><input style='display:inline-block; width:21%;' type='text' id='t_vdesde' > <label>Horas en Formato 24H</label>
	      <br><label>Hasta: </label><input style='display:inline-block; width:21%;' type='text' id='t_vhasta'>
	      <br><button style='background:#4D4D4D;margin-top:13px;' onclick='add_bloqueo();' class='btn-jugar'>Agregar</button>
	      </div>";
	    }
	    if($_GET['c']=="1"){
	      echo "
	      <div>
	      <label>Juego: </label><input readonly style='display:inline-block; width:34%;' type='text' name='t_vjuego' value='".$_GET['vjuego']."'>
	      <br><label>Desde: </label><input style='display:inline-block; width:21%;' type='text' name='t_vdesde'  value='".$_GET['vdesde']."'>
	      <br><label>Hasta: </label><input style='display:inline-block; width:21%;' type='text' name='t_vhasta' value='".$_GET['vhasta']."'>
	      <br><button style='margin-top:13px;' onclick='save_bloqueo();' class='btn-jugar'>Guardar</button>
	      <a style='margin-top:13px;text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"$('#frame').load('panel?start&bloque&c=0');\">Cancelar</a>
	      </div>";
	    }
	}

	if (isset($_GET['bloque'])){
		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
			<span style='font-size:2em; color:black;'>Bloqueos</span><br>
			<a onclick=\"$('#frame').load('panel?bloqueos&c=0');\" >Editar</a>
			<div style='display:inline-block;padding-left:55px;'></div>
			<a onclick=\"$('#frame').load('panel?bloque');\" >Lista</a></div>";
			$conexion = mysqli_connect(servidor(),user(),password()) or die ("Error de Conexion: ". mysqli_connect_error());
			$db = mysqli_select_db( $conexion, database() ) or die ( "Upps! no se ha podido conectar a la base de datos" );

		    $consulta = "select * from BLOQUEO ORDER BY JUEGO ASC";
		    $resultado = mysqli_query( $conexion, $consulta );
		    $cadena="<table class='table table-striped table-bordered' style='width: 100%; text-align:center; font-size:1em;' >".
		    "<thead>
				<th>ACCIONES</th>
				<th>JUEGO</th>
				<th>DESDE</th>".
				"<th>HASTA</th>
			</thead><tbody>";
		    while($row = mysqli_fetch_array($resultado)){
		         $cadena= $cadena . "<tr><td><a style='text-decoration:none; color:black;' onclick=\"$('#frame').load('panel?start&bloqueos=&editar=&c=1"
		         ."&vjuego=".$row['JUEGO']
		         ."&vdesde=".$row['DESDE']
		         ."&vhasta=".$row['HASTA']
		         ."');\">Editar</a><label> - </label><button onclick=\"borrar_bloqueo('".$row['JUEGO']."')\" value='".$row['JUEGO']."'>Borrar</button></td><td>".$row['JUEGO']."</td><td>".$row['DESDE']."</td><td>".$row['HASTA']."</td></tr>";
		    }
		    $cadena = $cadena . "</tbody></table>";
		    echo $cadena;
		    mysqli_close($conexion);
	}

	if (isset($_GET['pops'])){
		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
			<span style='font-size:2em; color:black;'>Mensajes Pops</span><br>
			<a onclick=\"$('#frame').load('panel?pops&c=0');\">Editar</a>
			<div style='display:inline-block;padding-left:55px;'></div>
			<a onclick=\"$('#frame').load('panel?pops1');\">Lista</a></div>";
	    if($_GET['c']=="0"){
	      echo "
	      <div>
	      <br><label>Fecha: </label><input style='display:inline-block; width:21%;' type='date' id='t_vfecha' >
	      <br><label>Color de Fondo: </label><input style='display:inline-block;' type='color' id='t_vbg' value='#F1B1B1' >
	      <label>Color de Texto: </label><input style='display:inline-block;' type='color' id='t_vfg' value='#000000' >
	      <label>Color de Borde: </label><input style='display:inline-block;' type='color' id='t_vborder' value='#FF5151' >
          <div title='Mensaje'><textarea style='display:inline-block;' cols='55' rows='13'  id='t_vmensaje' class='tmen'></textarea></div>
				<br><label>Ubicacion: </label><input style='display:inline-block;' type='text' id='t_vubicacion' value='inicio' >
	      <br><button style='margin-top:13px;background:#4D4D4D;' onclick='add_pops();' class='btn-jugar'>Agregar</button>
	      </div>";
	    }
	    if($_GET['c']=="1"){
	      echo "
	      <div>
				<input type='hidden' id='promoid' value='".$_GET['vid']."'
	      <br><label>Fecha: </label><input readonly style='display:inline-block; width:21%;' type='text' id='t_vfecha'  value='".$_GET['vfecha']."'>
	      <br><label>Color de Fondo: </label><input style='display:inline-block;' type='color' id='t_vbg' value='".readPromoId($_GET['vid'])['COLORBG']."' >
	      <label>Color de Texto: </label><input style='display:inline-block;' type='color' id='t_vfg' value='".readPromoId($_GET['vid'])['COLORFG']."' >
	      <label>Color de Borde: </label><input style='display:inline-block;' type='color' id='t_vborder' value='".readPromoId($_GET['vid'])['BORDER']."' >
	      <div title='Mensaje'><textarea style='display:inline-block;' cols='55' rows='19'  id='t_vmensaje' class='tmen' >".readPromoId($_GET['vid'])['MENSAJE']."</textarea></div>
	      <br><button style='margin-top:13px;' onclick='save_pops();' class='btn-jugar'>Guardar</button>
	      <a style='margin-top:13px;text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"$('#frame').load('panel?start&pops1');\">Cancelar</a>
	      </div>";
	    }
	}

	if (isset($_GET['pops1'])){
		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
		<span style='font-size:2em; color:black;'>Mensajes Pops</span><br>
			<a onclick=\"$('#frame').load('panel?pops&c=0');\">Insertar</a>
			<div style='display:inline-block;padding-left:55px;'></div>
			<a onclick=\"$('#frame').load('panel?pops1');\" >Lista</a></div>";
			$conexion = mysqli_connect(servidor(),user(),password()) or die ("Error de Conexion: ". mysqli_connect_error());
			$db = mysqli_select_db( $conexion, database() ) or die ( "Upps! no se ha podido conectar a la base de datos" );

		    $consulta = "select * from PROMO ORDER BY FECHA ASC";
		    $resultado = mysqli_query( $conexion, $consulta );
		    echo "
				<table class='table table-striped table-bordered' style='width: 100%; text-align:center; font-size:0.8em;'>
		    <thead>
					<th>ACCIONES</th>
					<th>FECHA</th>
					<th>UBICACION</th>
					<th>MENSAJE</th>
				</thead>
				<tbody>";
		    while($row = mysqli_fetch_array($resultado)){
				$len=-1;if(strlen($row['MENSAJE'])>50)$len=-34;
				$miFecha=date_create($row['FECHA']);
		        echo "
		        <tr>
						 	<td>
								<a style='text-decoration:none; color:black;background:#E2F7E2; padding:2px;border:1px solid; border-radius:4px;'
								onclick=\"$('#frame').load('panel?pops&c=1&vfecha=".date_format($miFecha,'d/m/Y')."&vid=".$row['ID']."');\">Edit &#9998;</a>
								<button style='border-radius:4px; border: 1px solid;' onclick=\"borrar_pops('".$row['ID']."')\" value='".$row['ID']."'>Borrar</button>
							</td>
							<td>".latinFecha($row['FECHA'])."</td>
							<td>{$row['UBICACION']}</td>
							<td style='background: #BFBFBF; text-align:justify;'>".substr($row['MENSAJE'], 0, $len)."</td>
						</tr>";
		    }
		    echo "</tbody></table>";
		    mysqli_close($conexion);
	}

/*
 * METODOS DE PAGO
 * */
	if (isset($_GET['metodo'])){
	echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
			<span style='font-size:2em; color:black;'>Metodos de Pago</span><br>
			<a onclick=\"$('#frame').load('panel?start&metodo&c=0&edicion');\">Edicion</a>
			<div style='display:inline-block;padding-left:55px;'></div>
			<a onclick=\"$('#frame').load('panel?start&metodo&c=2&lista');\" >Lista</a></div>";
    if($_GET['c']=="0" && isset($_GET['edicion'])){
      echo "
      <div>
      <br><label>Metodo: </label><input style='display:inline-block; ' type='text' id='t_metodo' >
      <br><label>%Venta: </label><input style='display:inline-block; width:8%;'  type='number' id='t_porcenVenta' min=1 max=100 value=1>
      <br><label>%Compra: </label><input style='display:inline-block; width:8%;'  type='number' id='t_porcenCompra' min=1 max=100 value=1>
      <br><label>Costo: </label><input onkeyup=\"calcular();\" style='display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='t_costo' min='0' step='0.000001' title='Currency' pattern='^\d+(?:\.\d{1,6})?$' onblur='this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,6})?$/.test(this.value)?'inherit':'red' value=0>
	  <br><label>Precio Venta: </label><input style='display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='t_pventa' min='0' step='0.000001' title='Currency' pattern='^\d+(?:\.\d{1,6})?$' onblur='this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,6})?$/.test(this.value)?'inherit':'red' value=0>
      <br><label>Precio Compra: </label><input style='display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='t_pcompra' min='0' step='0.000001' title='Currency' pattern='^\d+(?:\.\d{1,6})?$' onblur='this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,6})?$/.test(this.value)?'inherit':'red' value=0>
      <br><label>Perfil: </label><input style='display:inline-block; width:34%;' type='text' id='t_perfil'>
      <label>Bloqueado: </label><input style='display:inline-block; width:8%;'  type='number' id='t_bloqueado' min=0 max=1 value=0>
      <br><button style='background:#4D4D4D;' onclick='add_metodo();' class='btn-jugar'>Agregar</button>
      </div>";
    }
    if($_GET['c']=="1"&& isset($_GET['edicion'])){
      echo "
      <div>
      <input type='hidden' id='t_id' value='".$_GET['id']."'>
      <br><label>Metodo: </label><input style='display:inline-block; width:21%;' type='text' id='t_metodo'  value='".readMetodo($_GET['id'])['METODO']."'>
      <br><label>%Venta: </label><input style='display:inline-block; width:8%;'  type='number' id='t_porcenVenta' min=1 max=100 value='".readMetodo($_GET['id'])['PORCEN1']."'>
      <br><label>%Compra: </label><input style='display:inline-block; width:8%;'  type='number' id='t_porcenCompra' min=1 max=100 value='".readMetodo($_GET['id'])['PORCEN2']."'>
      <br><label>Costo: </label><input onkeyup=\"calcular();\" style='display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='t_costo' min='0' step='0.000001' title='Currency' pattern='^\d+(?:\.\d{1,6})?$' onblur='this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,6})?$/.test(this.value)?'inherit':'red' value='".readMetodo($_GET['id'])['COSTO']."'>
      <br><label>Precio Venta: </label><input style='display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='t_pventa' min='0' step='0.000001' title='Currency' pattern='^\d+(?:\.\d{1,6})?$' onblur='this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,6})?$/.test(this.value)?'inherit':'red' value='".readMetodo($_GET['id'])['PRECIO1']."'>
      <br><label>Precio Compra: </label><input style='display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='t_pcompra' min='0' step='0.000001' title='Currency' pattern='^\d+(?:\.\d{1,6})?$' onblur='this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,6})?$/.test(this.value)?'inherit':'red' value='".readMetodo($_GET['id'])['PRECIO2']."'>
      <br><label>Perfil: </label><input style='display:inline-block; width:34%;' type='text' id='t_perfil' value='".readMetodo($_GET['id'])['PERFIL']."'>
      <label>Bloqueado: </label><input style='display:inline-block; width:8%;'  type='number' id='t_bloqueado' min=0 max=1 value='".readMetodo($_GET['id'])['BLOQUEADO']."'>
      <br><button onclick='save_metodo();' class='btn-jugar'>Guardar</button>
      <a style='text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"$('#frame').load('panel?start&metodo&c=2&lista');\" >Cancelar</a>
      </div>";
    }
	if($_GET['c']=="2" && isset($_GET['lista'])){
			$conexion = mysqli_connect(servidor(),user(),password());
			$db = mysqli_select_db( $conexion, database());

		    $consulta = "select * from METODOS ORDER BY METODO ASC";
		    $resultado = mysqli_query( $conexion, $consulta );
		    $cargo="";
		    $cadena="
		    <div class='container' style='margin:0;width:auto;'>
		    <table id='tablax' class='table table-striped table-bordered' style='width: 100%; text-align:center; font-size:0.7em;'>".
		    "<thead>
				<th>Opciones</th>
				<th>Metodo</th>
				<th>Costo</th>".
				"<th>Venta</th>
				<th>Compra</th>
				<th>Perfil</th>
				<th>Bloqueo</th>
			</thead><tbody>";
		    while($row = mysqli_fetch_array($resultado)){
		         $cadena= $cadena . "<tr>
		         <td>
					<a style='text-decoration:none; color:black;background:#E2F7E2; padding:2px;border:1px solid; border-radius:4px;' onclick=\"$('#frame').load('panel?start&metodo=&edicion=&c=1"
					."&id=".$row['ID']
					."');\">&#9998;</a>
					<button style='border-radius:4px; border: 1px solid;' onclick=\"borrarMetodo('".$row['ID']."')\" value='".$row['ID']."'>Borrar</button>
		         </td>
		         <td>".$row['METODO']."</td>
		         <td>".$row['COSTO']."</td>
		         <td>".$row['PRECIO1']."</td>
		         <td>".$row['PRECIO2']."</td>
		         <td>".$row['PERFIL']."</td>
		         <td>".$row['BLOQUEADO']."</td>
		         </tr>";
		    }
		    $cadena = $cadena . "</tbody></table></div>";
		    echo $cadena;
		    mysqli_close($conexion);
	}
	}

	/*
	 * TOKEN
	 * */
		if (isset($_GET['token'])){

		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
				<span style='font-size:2em; color:black;'>Comercios</span><br>
				<a onclick=\"$('#frame').load('panel?start&token&c=2&lista');\" >Lista</a></div>";

	    if($_GET['c']=="1"&& isset($_GET['edicion'])){
	      echo "
	      <table style='width:100%;'>
	      <input type='hidden' id='t_id' value='".$_GET['id']."'>
				<tr><td>Creador: </td><td><textarea cols='55' rows='13 style='display:inline-block;'  id='token_ceo'>".readTokenID($_GET['id'])['CEO']."</textarea></td></tr>
				<tr><td>Telefono: </td><td><input value='".readTokenID($_GET['id'])['TELEFONO']."' style='display:inline-block;'  type='text' id='token_telefono'></td></tr>
				<tr><td>Instagram: </td><td><input value='".readTokenID($_GET['id'])['INSTAGRAM']."' style='display:inline-block;'  type='text' id='token_instagram'></td></tr>
				<tr><td>Token: </td><td><input maxlength='3' value='".readTokenID($_GET['id'])['TOKEN']."' style='text-transform: uppercase;width:20%;display:inline-block;' type='text' id='token_token' ></td></tr>
	      <tr><td>Nombre: </td><td><input value='".readTokenID($_GET['id'])['NOMBRE']."' style='display:inline-block;'  type='text' id='token_nombre'></td></tr>
				<tr><td>Correo: </td><td><input maxlength='34' value='".readTokenID($_GET['id'])['CORREO']."' style='display:inline-block;'  type='text' id='token_correo'></td></tr>
				<tr><td>Descripcion: </td><td><textarea cols='55' rows='13 style='display:inline-block;'  id='token_descripcion'>".readTokenID($_GET['id'])['DESCRIPCION']."</textarea></td></tr>
	      <tr><td>Imagen: </td><td><input style='width:30%; display:inline-block;' value='".readTokenID($_GET['id'])['IMAGEN']."' type='text'  id='token_imagen'></td></tr>
		    <tr><td>Valor: </td><td><input value='".readTokenID($_GET['id'])['VALOR']."' style='width:20%; display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='token_valor' min='0' step='0.000001' title='Currency'  value=0></td></tr>
				<tr><td>Patrimonio: </td><td><input onkeyup='valorLiquido()' value='".readTokenID($_GET['id'])['PATRIMONIO']."' style='width:20%; display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='token_patrimonio' min='0' step='0.000001' title='Currency' value=0></td></tr>
				<tr><td>Maxsupply: </td><td><input value='".readTokenID($_GET['id'])['MAXSUPPLY']."' style='width:20%; display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='token_maxsupply' min='0' step='0.000001' title='Currency'  value=0></td></tr>
	      <tr><td>Volumen: </td><td><input onkeyup='valorLiquido()' value='".readTokenID($_GET['id'])['VOLUMEN']."' style='width:20%; display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='token_volumen' min='0' step='0.000001' title='Currency'  value=0></td></tr>
				<tr><td>Rate: </td><td><input style='width:30%; display:inline-block;color:black;font-weight: bolder;background:#E3C886;' type='number'  id='token_rate' min='1' max='5' title='estrellas' value='".readTokenID($_GET['id'])['RATE']."'></td><tr>
				<tr><td>Bloqueado:</td><td><input onchange=\"nivelito()\" style='display:inline-block; width:8%;'  type='number' id='token_bloqueado' min=0 max=2 value='".readTokenID($_GET['id'])['BLOQUEADO']."'><div style='display:inline-block;' id='descrip'></div></td></tr>
	      </table>
				<br><button onclick='save_token();' class='btn-jugar'>Guardar</button>
				<a style='text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"$('#frame').load('panel?start&token&c=2&lista');\" >Cancelar</a>
				<div style='height:15px;'></div>
				<br>
				<br>
				<br>
				";
	    }
		if($_GET['c']=="2" && isset($_GET['lista'])){
				$conexion = mysqli_connect(servidor(),user(),password(),database());
				recalcTokens();
			    $consulta = "select * from TOKEN ORDER BY VOLUMEN DESC";
					if(isset($_GET['filtro'])){
						$consulta = "select * from TOKEN ORDER BY ".strtoupper($_GET['filtro'])." ASC";
					}
					if(isset($_GET['filtro2'])){
						$consulta = "select * from TOKEN ORDER BY ".strtoupper($_GET['filtro2'])." DESC";
					}
			    $resultado = mysqli_query( $conexion, $consulta );
			    $cargo="";
			    echo "
			    <div class='container' style='margin:0;width:auto;'>
			    <table id='tablax' class='table table-striped table-bordered' style='width: 100%; text-align:center; font-size:0.7em;'>
			    <thead>
					<th></th>
					<th></th>
					<th>Nombre<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro=nombre');\">▲</button>
						<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro2=nombre');\">▼</button>
					</th>
					<th>Precio<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro=valor');\">▲</button>
						<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro2=valor');\">▼</button>
					</th>.
					<th>Volumen<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro=volumen');\">▲</button>
						<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro2=volumen');\">▼</button>
					</th>
					<th>Bloqueado<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro=bloqueado');\">▲</button>
						<button type='button' onclick=\"$('#frame').load('panel?start&token&c=2&lista&filtro2=bloqueado');\">▼</button>
					</th>
					</thead><tbody>";
			    while($row = mysqli_fetch_array($resultado)){
			      echo "<tr><td>
						<a style='text-decoration:none; color:black;background:#E2F7E2; padding:2px;border:1px solid; border-radius:4px;'
						onclick=\"$('#frame').load('panel?start&token=&edicion=&c=1&id=".$row['ID']."');\">&#9998;</a>
						";
						if(readVendedor($_SESSION['user'])['NIVEL']=='2'){
						echo "<button style='border-radius:4px; border: 1px solid;' onclick=\"borrar_Token('{$row['ID']}')\" value='".$row['ID']."'>Borrar</button>";
						}
			       echo "</td>
			         <td><img style='vertical-align: middle;background:none; border-radius:50%;' width=\"32\" height=\"32\" src=\"../Feed/Book/comercios/".$row['IMAGEN']."\" >".$row['TOKEN']."</td>
							 <td>".$row['NOMBRE']."</td>
					     <td>".$row['VALOR']."</td>
					     <td>".$row['VOLUMEN']."</td>
							 <td>".$row['BLOQUEADO']."</td>
			         </tr>";
			    }
			    echo "</tbody></table></div>";
			    mysqli_close($conexion);
		}
		}

/*************************/
/*
 * PERFIL
 * */

  if (isset($_GET['perfil'])){
   echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
		<span style='font-size:2em; color:black;'>Datos de Pagina</span><br></div>";
	$conexion = mysqli_connect(servidor(),user(),password());
	$db = mysqli_select_db( $conexion, database());

	$consulta = "select * from FORTUNA";
	$resultado = mysqli_query( $conexion, $consulta );
	$row = mysqli_fetch_array($resultado);
    echo "
    <div>
    <label>WALLET: </label><input style='display:inline-block; width:55%;' type='text' id='t_wallet' VALUE='".$row['WALLET']."'>
    <br><label>EMAIL: </label><input style='display:inline-block; width:34%;' type='email' id='t_correo' VALUE='".$row['CORREO']."'>
    <br><label>Pagina Web:</label><input style='display:inline-block; width:34%;' type='text' id='t_web' VALUE='".$row['WEB']."'>
		<br><label>Depositos:</label><input style='display:inline-block; width:34%;' type='text' id='t_depositos' VALUE='".$row['DEPOSITOS']."'>
		<br><label>Retiros:</label><input style='display:inline-block; width:34%;' type='text' id='t_retiros' VALUE='".$row['RETIROS']."'>
		<br><label>Soporte:</label><input style='display:inline-block; width:34%;' type='text' id='t_soporte' VALUE='".$row['SOPORTE']."'>
    <br><label>Disponible FRD: </label><input
			style='display:inline-block; width:21%;color:black;font-weight: bolder;outline:0; border:none; background:transparent;'
			readonly type='number'  value='".$row['SALDO']."'> <button type='button' onclick='retirar()'>Retirar</button>
    <br><label>Interes Prestamo: </label><input style='display:inline-block; width:8%;' type='number' min=0  id='interes' step='0.01' value='".$row['INTERESPRESTAMO']."'>%
		<br><label>Comision Retorno: </label><input style='display:inline-block; width:8%;' type='number' min=0  id='retorno' step='0.01' value='".$row['COMISIONRETORNO']."'>%
		<br><label>Comision Emision: </label><input style='display:inline-block; width:8%;' type='number' min=0  id='emision' step='0.01' value='".$row['COMISIONEMISION']."'>%
		<br><label>Premios: </label><input style='display:inline-block; width:8%;' type='number' min=0 max=100 id='porcen' value='".$row['PORCEN']."'>%
    <br><label>TICKET: </label><input
			style='display:inline-block; width:34%;color:black;font-weight: bolder;outline:0; border:none; background:transparent;'
			readonly type='number'  value='".$row['TICKET']."' >
    <br><label>REGISTRADOS: </label><input
			style='display:inline-block; width:34%;color:black;font-weight: bolder;outline:0; border:none; background:transparent;'
			readonly type='number'  value='".$row['USUARIOS']."' >
    <br><button onclick='guardar_empresa();' class='btn-jugar'>Guardar</button>
    </div>
		<br><br><br>
    ";
    mysqli_close($conexion);
  }
/*************************/
/*
 * USUARIOS DEL SISTEMA
 * */
  if(isset($_GET['clientes'])){
	echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
			<span style='font-size:2em; color:black;'>Usuarios</span><br>
				<a onclick=\"$('#frame').load('panel?start&clientes&c=2&lista');\" >Lista</a></div>";

    if($_GET['c']=="1"&& isset($_GET['edicion'])){
		$cargo="";
		if($_GET['vsuper']=="0")$cargo="USUARIO";
		else if($_GET['vsuper']=="1")$cargo="VENDEDOR";
		else if($_GET['vsuper']=="2")$cargo="CAJERO";
		else if($_GET['vsuper']=="3")$cargo="ADMINISTRADOR";

      echo "
      <div>
      <input type='hidden' id='t_id' value='".$_GET['id']."'>
      <label>Nombre: </label><input style='display:inline-block; width:34%;' type='text' id='t_vnombre' value='".readCliente($_GET['vcorreo'])['NOMBRE']."'><label style='text-size:1em; color:black margin-left:10px;' >".$cargo.makeAnciEstrellas(readCliente($_GET['vcorreo'])['RATE'])."</label>
      <br><label>Telefono: </label><input style='display:inline-block; width:21%;' type='text' id='t_vpago' value='".readCliente($_GET['vcorreo'])['TELEFONO']."'>
      <br><label>Correo: </label><input style='display:inline-block; width:34%;' type='email' id='t_vcorreo' value='".$_GET['vcorreo']."'>
      <br><label>Password: </label><input style='display:inline-block; width:34%;' type='text' id='t_vpassword' value='".readCliente($_GET['vcorreo'])['PASSWORD']."'>
			<br><label>Wallet: </label><input style='display:inline-block; width:40%;' type='text' id='t_vwallet' value='".readCliente($_GET['vcorreo'])['WALLET']."'>
			<br><label>ApiKey: </label><input style='display:inline-block; width:40%;' type='text' id='t_vapikey' value='".readCliente($_GET['vcorreo'])['APIKEY']."'>
      <br><label>Rate: </label><input style='display:inline-block; width:8%;' type='number'  id='t_vrate' value='".readCliente($_GET['vcorreo'])['RATE']."' min=0 max=5>
      <label>Nivel: </label><input style='display:inline-block; width:8%;'  type='number' id='t_vsuper' value='".readCliente($_GET['vcorreo'])['NIVEL']."' min=0 max=3>
      <label>Activo: </label><input style='display:inline-block; width:8%;' type='number' id='t_vactivo' value='".readCliente($_GET['vcorreo'])['ACTIVO']."' min=0 max=1>
      <br><label>Saldo: </label><input style='display:inline-block; width:21%; background:#AECFAE;' type='text' id='t_vsaldo' value='".number_format(readCliente($_GET['vcorreo'])['SALDO'],4,",",".")."'>
      <label>Foto Perfil: </label><input style='display:inline-block; width:21%;' type='text' id='t_vperfil' value='".readCliente($_GET['vcorreo'])['PERFIL']."'>
      <br><label>Nacionalidad: </label><input style='display:inline-block; width:34%;' type='text' id='t_vnacionalidad'  value='".readCliente($_GET['vcorreo'])['NACIONALIDAD']."'>
      <br><button onclick='save_cliente()' class='btn-jugar'>Guardar</button>
      <a style='text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"$('#frame').load('panel?start&clientes&c=2&lista');\" >Cancelar</a>
      </div>";
    }
	if($_GET['c']=="2" && isset($_GET['lista'])){
			$conexion = mysqli_connect(servidor(),user(),password());
			$db = mysqli_select_db( $conexion, database());

		    $consulta = "select * from USUARIOS ORDER BY NOMBRE ASC";
		    $resultado = mysqli_query( $conexion, $consulta );
		    $cargo="";
		    echo "
		    <div class='container' style='margin:0;width:auto;'>
		    <table id='tablax' class='table table-striped table-bordered' style='width: 100%; text-align:center; font-size:0.7em;'>
				<thead>
					<th>Opciones</th>
					<th>Nombre</th>
					<th>Saldo</th>
					<th>Telefono</th>
					<th>Correo</th>
					<th>Cargo</th>
					<th>Activo</th>
			  </thead><tbody>";

		    while($row = mysqli_fetch_array($resultado)){
					if($row['NIVEL']=="0")$cargo="USUARIO";
					if($row['NIVEL']=="1")$cargo="CAJERO";
					if($row['NIVEL']=="2")$cargo="ADMIN";
		      echo "<tr><td>
					<a style='text-decoration:none; color:black;background:#FAFA81; padding:2px;border:1px solid; border-radius:4px;'
					onclick=\"$('#frame').load('panel?start&clientes&c=3&contacto&correo=".$row['CORREO']."');\">&#9993;</a>
					<a style='text-decoration:none; color:black;background:#E2F7E2; padding:2px;border:1px solid; border-radius:4px;'
					onclick=\"$('#frame').load('panel?start&clientes=&edicion=&c=1&vcorreo=".$row['CORREO']."&id=".$row['ID']."&vsuper=".$row['NIVEL']."');\">&#9998;</a>
					";
					if(readVendedor($_SESSION['user'])['NIVEL']=='2'){
							echo "<button style='border-radius:4px; border: 1px solid;' onclick=\"borrar('".$row['ID']."')\" value='".$row['ID']."'>Borrar</button>";
					}
					echo "
		         </td>
		         <td>".$row['NOMBRE']."</td>
		         <td>".$row['SALDO']."</td>
		         <td>".$row['TELEFONO']."</td>
		         <td>".$row['CORREO']."</td>
		         <td>".$cargo."</td>
		         <td>".$row['ACTIVO']."</td>
		         </tr>";
		    }
		    echo "</tbody></table></div>";
		    mysqli_close($conexion);
	}
	if($_GET['c']=="3"&& isset($_GET['contacto'])){
		echo 	"<div>
				<label>Correo a: </label> <input type='text' readonly id='t_correo' value='".$_GET['correo'] ."'>
				<label>Asunto:</label><input type='text' id='t_asunto'>
				<div title='Mensaje'><textarea style='padding:13px;display:inline-block;' cols='55' rows='13'  id='t_mensaje' ></textarea></div>
				<button onclick='enviar_correo();' class='btn-jugar'>Enviar</button>
				<br><br><br>
				</div>";
	}
  }

}
?>
</div>
<script>
function borrar(id){
	var txt;
	var r = confirm("Cuidado..! Esta Seguro de Eliminar el Usuario ["+id+"]....?");
	if (r == true) {
		$.get("panel?clientes&borrar&edicion&c=0&id="+id,
			function(data){
				$('#frame').load("panel?start&clientes&c=2&lista");
			}
		);
	} else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function borrarMetodo(id){
	var txt;
	var r = confirm("Cuidado..! Esta Seguro de Eliminar el Metodo de Pago?");
	if (r == true) {
		$.get("panel?metodo&borrarmetodo&edicion&c=0&id="+id,
			function(data){
				$('#frame').load("panel?start&metodo&c=2&lista");
			}
		);
	} else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function borrar_bloqueo(id){
	var txt;
	var r = confirm("Cuidado..! Esta Seguro de Eliminar el Bloqueo al Juego ["+id+"]....?");
	if (r == true) {
		$.get("panel?bloqueos&c=0&start&borrarbloqueo&juego="+id,
			function(data){
				$('#frame').load("panel?bloque");
			}
		);
	} else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function borrar_pops(id){
	var txt;
	var r = confirm("Cuidado..! Esta Seguro de Eliminar el Mensaje ["+id+"]....?");
	if (r == true) {
		$.get("panel?pops&c=0&start&borrarpops&id="+id,
			function(data){
				$('#frame').load("panel?pops1");
			}
		);
	} else {
	  /*txt = "You pressed Cancel!";*/
	}
}
</script>
</html>
