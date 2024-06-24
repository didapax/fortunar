<?php
/*METODOS*/

include 'admin.php';
date_default_timezone_set('America/Caracas');

if (isset($_GET['borrar'])){ //borrar metodo
	sqlconector("delete from USUARIOSMETODOS WHERE ID=".$_GET['id']);
}

if(isset($_POST['guardarPerfil'])){
	sqlconector("INSERT INTO USUARIOSMETODOS(CORREO,METODO,WALLET) VALUES('".$_POST['correo']."','".$_POST['metodo']."','".$_POST['wallet']."')");
}

if(isset($_POST['save'])){
	sqlconector("UPDATE USUARIOSMETODOS SET PRECIOVENTA={$_POST['precioVenta']},
		PRECIOCOMPRA={$_POST['precioCompra']},SALDOFIAT={$_POST['saldoFiat']},
		ACTIVO={$_POST['activo']} WHERE ID={$_POST['id']}");
}

if(isset($_POST['pasar'])){
	$spot = readVendedor($_SESSION['user'])['SALDO'];
	$p2p = readVendedor($_SESSION['user'])['P2P'];
	$monto = $_POST['monto'];

	if($_POST['pasar'] == "SPOT"){
		$valor = $p2p + $monto;
		$resta = $spot - $monto;
		sqlconector("UPDATE USUARIOS SET P2P={$valor}, SALDO={$resta} WHERE CORREO = '{$_POST['correo']}'");
		sqlconector("UPDATE USUARIOSMETODOS SET SALDOFRD={$valor} WHERE CORREO = '{$_POST['correo']}'");
	}
	if ($_POST['pasar'] == "P2P"){
		$valor = $p2p - $monto;
		$resta = $spot + $monto;
		sqlconector("UPDATE USUARIOS SET P2P={$valor}, SALDO={$resta} WHERE CORREO = '{$_POST['correo']}'");
		sqlconector("UPDATE USUARIOSMETODOS SET SALDOFRD={$valor} WHERE CORREO = '{$_POST['correo']}'");
	}
}
?>
<html>
<div style="overflow-x: hidden;overflow-y: auto;">
<script>

function isselect(){
	if(document.getElementById('select').value === "P2P"){
			$("#cambio").html("SPOT");
			document.getElementById('value1').max = document.getElementById('saldo_P2P').value;
	}
	else if (document.getElementById('select').value === "SPOT") {
		$("#cambio").html("P2P");
		document.getElementById('value1').max = document.getElementById('saldo_SPOT').value;
	}
}

function revisa(){
	if((document.getElementById('ventaFrd').value *1) > (document.getElementById('saldo').value *1)){
		document.getElementById('ventaFrd').title="Monto errado";
		document.getElementById('ventaFrd').value = "";
	}
	else {

	}
}

function revisaTransf(){
	if((document.getElementById('value1').value *1) > (document.getElementById('value1').max *1)){
		document.getElementById('value1').title="Monto errado";
		document.getElementById('value1').value = "";
	}
	else {
		document.getElementById('value2').value = document.getElementById('value1').value;
	}
}

function borrar(id){
	var txt;
	var r = confirm("Esta Seguro de Eliminar el Método de Pago?");
	if (r == true) {
		$.get("metodos?borrar&id="+id,
			function(data){
				$('#frame').load("metodos");
			}
		);
	} else {
	  /*txt = "You pressed Cancel!";*/
	}
}

function selectMetodo(){
	if (document.getElementById("metodos").value=="VES") {
		document.getElementById('labelwallet').innerHTML="Datos Bancarios";
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
		  $.post("metodos",
		  {
		    guardarPerfil: "",
		    metodo: document.getElementById('metodos').value,
		    correo: document.getElementById('correo').value,
		    wallet: document.getElementById('wallet').value
		  },
		  function(data){
		  });
		  $('#frame').load("metodos");
}

function cancelar() {
	$('#frame').load("metodos");
}

function save(){
			$.post("metodos",{
				save:"",
				id: document.getElementById('id').value,
				precioVenta: document.getElementById('precioVenta').value,
				precioCompra: document.getElementById('precioCompra').value,
				saldoFiat: document.getElementById('saldoFiat').value,
				activo: document.getElementById('activo').value
			},function(data){
				alert("Datos Guardados con exito..!");
				$('#frame').load("metodos");
			});
}

function ajustes(metodo){
	$.post("admin.php",{
			correo: document.getElementById('correo').value,
			editDataMetodo: metodo
		},function(data){
			var datos=data.split(',');
			document.getElementById('precioVenta').value = (datos[0] *1).toFixed(2);
			document.getElementById('precioCompra').value = (datos[1] *1).toFixed(2);
			document.getElementById('saldoFiat').value = (datos[2] *1).toFixed(2);
			document.getElementById('activo').value = datos[4];
			document.getElementById('id').value = datos[5];
		});
 	document.getElementById('ajustes').show();
}

function pasar(){
	if(document.getElementById('value1').value >=1 ){
		if(document.getElementById('select').value === "SPOT"){
			document.getElementById('transferir').close();
			$.post("metodos",{
				pasar:"SPOT",
				wallet: "SPOT",
				correo: document.getElementById('correo').value,
				monto: document.getElementById('value1').value
			},function(data){
				alert("transferencia realizada con exito..!");
				$('#frame').load("metodos");
			});
		}
		else if (document.getElementById('select').value === "P2P") {
			document.getElementById('transferir').close();
			$.post("metodos",{
				pasar:"P2P",
				wallet: "P2P",
				correo: document.getElementById('correo').value,
				monto: document.getElementById('value1').value
			},function(data){
				alert("transferencia realizada con exito..!");
				$('#frame').load("metodos");
			});
		}
	}
	else{
		alert("debe ser minimo 1")
	}
}

</script>
<style>

input[type=number]{
		width: 150px;
		margin: 3px;
}

.dialog_transf{
	margin-left: 21px;
	border 1px solid black;
	border-radius: 5px;
	background: #151937;
	width: 355px;
	height: 250px;
	padding: 3px;
	left: 5px;
	color: white;
}
</style>
	<link rel="stylesheet" href="appfortunar.css">
	<div style='text-align: center;width:100%;display:inline-block;'>
		<span style='font-size:2em; color:black;'>Comercio P2P</span><br>
	</div><br>
<?php

	echo "
	<input type='hidden' id='saldo_SPOT' value='".readVendedor($_SESSION['user'])['SALDO']."'>
	<input type='hidden' id='saldo_P2P' value='".readVendedor($_SESSION['user'])['P2P']."'>
	<input type='hidden' name='correo' id='correo' value='".readVendedor($_SESSION['user'])['CORREO']."'>
	<input type='hidden' id='saldo' value='".readVendedor($_SESSION['user'])['SALDO']."'>
	";
   $cadena="";
   $conexion = mysqli_connect(servidor(),user(),password());
   $db = mysqli_select_db( $conexion, database() );
   $consulta = "select * from METODOS WHERE BLOQUEADO=0";
   $resultado = mysqli_query( $conexion, $consulta );
   while($row = mysqli_fetch_array($resultado)){
		 if(!isset(readUserDataMetodo(readVendedor($_SESSION['user'])['CORREO'],$row['METODO'])['METODO']))
      	$cadena= $cadena . "<option value='".$row['METODO']."'>".$row['METODO']."</option>";
   }
   mysqli_close($conexion);
?>

<?php
	echo "<span>Balance P2P </span> <button class='appbtn' style='background:blue;' type='button' onclick=\"document.getElementById('transferir').show()\">Transferir</button><br> ".readVendedor($_SESSION['user'])['P2P']."FRD
	<br><label style='margin:0 0;display:block;font-weight:bold;'>Agregar Método de Compra y Venta: </label>
	<select id='metodos' class='form' onchange='selectMetodo()' style='font-size:1.1rem;width:300px;padding:5px;'>
	<option value''>Selecciona</option>".$cadena."</select>
	<br><br><label style='margin:0 0;display:none;' id='labelwallet'>Pon Aquí tus Datos Bancarios: </label>
	<textarea cols='30' rows='8' id='wallet' style='display:none; padding:5px;'></textarea> <br>
	<div class='button_container'>
	<input class='appbtn' id='add' type='hidden' onclick='agregar();' value='Agregar'>
	<input class='appbtn appbtn-cancel' id='cancel' type='hidden' onclick='cancelar();' value='Cancelar'>
	</div>
	";

echo " <br><label style='font-weight:bold; margin:0 0;display:block;margin: 1rem 0 0 0;'>Tus Métodos de Pago Agregados:</label>";
?>
<dialog class="dialog_transf" id="transferir" close>
	<a style="font-weight: bold;float:right;cursor:pointer;" onclick="document.getElementById('transferir').close()">X</a><br>
	<select style="paddin:8px; width:55px;" id="select" onchange="isselect()">
		<option value='null'>select</option>
		<option value='SPOT'>Spot</option>
		<option value='P2P'>P2P</option>
	</select>
	<input type="number" value="0" id="value1" min="1" onkeyup="revisaTransf()" onchange="revisaTransf()">
	<div style="width:100%;text-align:center;">
	<span style="font-size:30px;">⇆</span><br>
	</div>
	<div style="display:inline;" id="cambio"></div><input type="number" value="0" id="value2" readonly><br>
	<button class='appbtn' style="float:right;background:blue;paddin:3px;" type="button" onclick="pasar()">Transferir</button>
</dialog>

<dialog class="dialog_transf" id="ajustes" close>
	<input type="hidden" id="id" value="">
	<a style="font-weight: bold;float:right;cursor:pointer;" onclick="document.getElementById('ajustes').close()">X</a><br>
	<span>Precio de Venta</span>
	<input type="number" value="0" id="precioVenta" min="1">
	<br><span>Precio de Compra</span>
	<input type="number" value="0" id="precioCompra" min="1">
	<br><span>Tu Saldo en Fiat</span>
	<input type="number" value="0" id="saldoFiat" min="1">
	<br><span>Activo</span>
	<input type="number" id="activo" list="activar" min="0" max="1">
	<datalist id="activar">
		<option value="0" label="No">
		<option value="1" label="Si">
	</datalist>
	<br>
	<button class='appbtn' style="float:right;background:blue;" type="button" onclick="save()">Guardar</button>
</dialog>

<div style="width: 100%; height: 300px; overflow: auto; background: #fff; border-color:black; ">
<?php
   $cadena="";
   $conexion = mysqli_connect(servidor(),user(),password());
   $db = mysqli_select_db( $conexion, database() );
   $consulta = "select * from USUARIOSMETODOS WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."'";
   if($resultado = mysqli_query( $conexion, $consulta )){
	   while($row = mysqli_fetch_array($resultado)){
	      	$cadena= $cadena . "<br> <div style='display:block;'>
					<button class='appbtn appbtn-cancel' onclick=\"borrar('".$row['ID']."')\" value='".$row['ID']."'>Borrar</button>
					<button class='appbtn appbtn-cancel' onclick=\"ajustes('".$row['METODO']."')\" value='".$row['ID']."'>Ajustes</button>
					<label class='pay-label'>".$row['METODO']."</label><span class='pay-method'>".$row['WALLET']."</span> </div>";
	   }
   }
   mysqli_close($conexion);
if(strlen($cadena)<1) echo "Vacio...";
else echo $cadena;
echo "</div><br><br>";
?>

</div>

</html>
