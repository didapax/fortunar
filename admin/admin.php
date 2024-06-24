<?php
	/*
	  admin para el panel ADMINISTRADOR
		revision 0.0.0.5.1
	  FortunaRoyal
	  (c) 2020 by Triangulo Rojo
	*/
	require "../init.php";

	session_start();

	if (isset($_GET['close'])){
			activarChat(readVendedor($_SESSION['user'])['CORREO'],'0');
			session_unset();
			session_destroy();
	}

/***************************************************************************************************************
 * Funciones del Bloqueo para cerrar la casa Juego
 * */

function limpiar($cadena) {
    $vowels = array("'", "\"");
    return str_replace($vowels, "", $cadena);
}

function sqlconector($consulta) {
	 $conexion = @mysqli_connect(servidor(),user(),password(),database());
	 if (!$conexion) {
		 echo "Refresh page, Failed to connect to Data: " . mysqli_connect_error();
		 exit();
	 }else{
		 $resultado = mysqli_query( $conexion, $consulta );
		 mysqli_close($conexion);
	 }
}

function row_sqlconector($consulta) {
 $row =0;
 $conexion = @mysqli_connect(servidor(),user(),password(),database());
 if (!$conexion) {
	 echo "Refresh page, Failed to connect to Data: " . mysqli_connect_error();
	 exit();
 }else{
	 if($resultado = mysqli_query( $conexion, $consulta )){
		 $row = mysqli_fetch_array($resultado);
	 }
	 mysqli_close($conexion);
 }
 return $row;
}

function insertReferido($referido,$referente,$monto){
	sqlconector("insert into REFERIDOS (REFERIDO,REFERENTE,RECOMPENSA) values ('".$referido."','".$referente."',".$monto.")");
}

function readReferido($referido){
	return row_sqlconector("select * from USUARIOS where CODIGOREFERIDO='".$referido."'");
}

function insertBloqueoJuego($juego,$desde,$hasta){
	sqlconector("insert into BLOQUEO (JUEGO,DESDE,HASTA) values ('".$juego."','".$desde."','".$hasta."')");
}
function editBloqueoJuego($juego,$desde,$hasta){
	sqlconector("UPDATE BLOQUEO SET DESDE='".$desde."', HASTA='".$hasta."' WHERE JUEGO='".$juego."'");
}
function deleteBloqueoJuego($juego){
	sqlconector("delete from BLOQUEO where JUEGO='".$juego."'");
}

/****************************************************************************************************************
 * Funciones para las promociones
 * */

function insertPromo($fecha,$mensaje,$bg,$fg,$border,$ubicacion){
	sqlconector("insert into PROMO (FECHA,MENSAJE,COLORBG,COLORFG,BORDER,UBICACION) values ('".$fecha."','".$mensaje."','".$bg."','".$fg."','".$border."','{$ubicacion}')");
}

function editPromo($id,$mensaje,$bg,$fg,$border){
	sqlconector("UPDATE PROMO SET MENSAJE='".$mensaje."',COLORBG='".$bg."',COLORFG='".$fg."',BORDER='".$border."' WHERE ID=".$_POST['id']);
}

function deletePromo($id){
	sqlconector("delete from PROMO  WHERE ID=".$id);
}

function readPromo(){
	$row = row_sqlconector("select * from PROMO where FECHA BETWEEN '".date("Y/m/d")." 00:00' AND '".date("Y/m/d")." 23:59'");
	return 	"<div class='relative'>
		<div style='background: ".$row['COLORBG'].";border-color:".$row['BORDER'].";' class='relative relative-absolute'>
		<a href='javascript:void(0);' class='icon-close' onclick=\"$('.relative').fadeOut()\">✖️</a><br>
		<div style=';padding:21px; color:".$row['COLORFG'].";width:100%;'>".$row['MENSAJE']."</div>
		</div>
		</div>
		";
}

function ifReadPromo(){
	if($row = row_sqlconector("select * from PROMO where FECHA BETWEEN '".date("Y/m/d")." 00:00' AND '".date("Y/m/d")." 23:59'")){
		if(strlen($row['MENSAJE'])>0) return TRUE;
		return FALSE;
	}
}

function readPromoId($id){
	return row_sqlconector("select * from PROMO where ID=".$id);
}
//**************************************************************************************************************
//funciones de la Pagina WEB

if(isset($_GET['estadistica'])) {
	$row = row_sqlconector("select SUM(SALDO) as CAPITAL FROM USUARIOS WHERE NIVEL=0");
	$row2= row_sqlconector("SELECT SUM(ACUMULADO) AS TOTAL FROM TORNEO WHERE LENGTH(ADMINISTRADOR)>0 AND ELIMINAR = 0");
	echo "Capital: ".strval($row['CAPITAL'] + $row2['TOTAL']). "FRD";

	$row = row_sqlconector("select SUM(MONTO) as MONTO, COUNT(ID) AS NUM FROM OPERACIONES  WHERE TIPO='RETIRO'");
	echo "<br>Promedio de Retiro: ". number_format(strval($row['MONTO']/$row['NUM']),2,",","."). "FRD";

	$row = row_sqlconector("select SUM(RECIBE) as MONTO, COUNT(ID) AS NUM FROM OPERACIONES  WHERE TIPO='FONDEO'");
	echo "<br>Promedio de Depositos: ". number_format(strval($row['MONTO']/$row['NUM']),2,",","."). "FRD";
}

function generaTicket(){
	$ticket=0;
	$row = row_sqlconector("SELECT TICKET from FORTUNA");
	$ticket=$row['TICKET']+1;
	sqlconector("UPDATE FORTUNA SET TICKET=".$ticket);
	return $ticket;
}

function generaRefer(){
	$ticket=0;
	$row = row_sqlconector("SELECT USUARIOS from FORTUNA");
	$ticket=$row['USUARIOS']+1;
	sqlconector("UPDATE FORTUNA SET USUARIOS=".$ticket);
	return $ticket;
}

  function readBloqueoJuego($juego){
		$row = row_sqlconector("select * from BLOQUEO where JUEGO='".$juego."'");
		if (!empty($row))
			return $row;
  }

  function latinFecha($fecha){
		$date=date_create($fecha);
		return date_format($date,"d/M/y h:ia");
  }

	function latinNumero($num,$decimal){
 	 return number_format($num,$decimal,",",".");
  }

  function cerrarCasa($juego){
	$desde="00";
	$hasta="00";
	if(!empty(readBloqueoJuego($juego)['DESDE'])) $desde=readBloqueoJuego($juego)['DESDE'];
	if(!empty(readBloqueoJuego($juego)['HASTA'])) $hasta=readBloqueoJuego($juego)['HASTA'];
	date_default_timezone_set("America/Caracas");
	$horaR=(int)date("H");
	if($horaR>=(int)$desde&&$horaR<=(int)$hasta){
		return true;
	}
	else{
		return false;
	}
  }

  function apuestasGanadoras(){
  	$conexion = mysqli_connect(servidor(),user(),password(),database());

		$consulta = "SELECT * from GANADORES GROUP BY JUEGO";
		$resultado = mysqli_query( $conexion, $consulta );
		$cadena="<table>";
		while($row = mysqli_fetch_array($resultado)){
		  $cadena= $cadena . "
			<tr>
			<td style='font-size:1.5rem; background: rgb(0 0 0 / 80%); padding: 3px; font-family: Oswald', cursive; '>".$row['JUEGO'].
			" <span style='font-size:13px;'>Entregados(".number_format(row_sqlconector("SELECT SUM(MONTO) AS TOTAL FROM GANADORES WHERE JUEGO='".$row['JUEGO']."'")['TOTAL'],0,",",".").")FRT</span> ".
			makeImgEstrellas($row['RATE'])."</td>
			</tr>
			";
		}
		$cadena = $cadena . "</table>";
  	mysqli_close($conexion);
  	echo $cadena;
  }

	function marquesina(){
		//&#9650;
		//&#9660;
  	echo "Token FRT ".readToken("FRT")['VALOR'].
		" &#9650;&#9660; BTC ".latinNumero(readMetodo2("BITCOIN")['COSTO'],2).
		" &#9650;&#9660; FRD-VES ".latinNumero(readMetodo2("BOLIVARES")['PRECIO1'],2).
		" &#9650;&#9660;".
		" <span style='color:#FCB2F2;'>CAMBIA TUS FRD POR TOKEN FRT Y GANA JUGANDO...</span>";
	}

  function makeImgEstrellas($rate){
	  $cadena="";
	  for ($i=1; $i<=$rate; $i++){
		  $cadena= $cadena. "<img style='width: 1.7rem; vertical-align:middle;border-radius:50%; background: none;' src='picture/estrella.png'>";
	  }
	  return $cadena;
  }

  function makeAnciEstrellas($rate){
	  $cadena="";
	  for ($i=1; $i<=$rate; $i++){
		  $cadena= $cadena. "<span style='color:#F9F11D;vertical-align:middle;font-size:18px;'>★</span>";
	  }
	  return $cadena;
  }

  function makeImgPerfil($image){
	  $cadena="";
	  $cadena= "<img style='vertical-align:middle;border-radius:50%;' src='upload/perfiles/".$image."' alt='' width='89px'>";
	  return $cadena;
  }

  function readFortuna(){
	 return row_sqlconector("select * from FORTUNA");
  }

  function readCasa(){
	 return row_sqlconector("select * from CASA");
  }

  function updateSaldoCasa($valor){
	sqlconector("UPDATE FORTUNA SET SALDO=".$valor);
  }

  function encrypt($string, $key) {
    $result = '';
    for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
    }
    return base64_encode($result);
  }

  function decrypt($string, $key) {
   $result = '';
   $string = base64_decode($string);
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)-ord($keychar));
      $result.=$char;
   }
   return $result;
  }

 function createTmp($tabla){
    $vowels = array("@", ".", "-", "_", "#", "$", "&", "*", "[", "}");
    $only = str_replace($vowels, "", $tabla);
	 sqlconector("CREATE TABLE IF NOT EXISTS ".$only." (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,CORREO VARCHAR(34),JUGADA VARCHAR(20),MONTO VARCHAR(20),GANA VARCHAR(20),MONEDA VARCHAR(20))");
  }

  function deleteTmp($tabla){
    $vowels = array("@", ".", "-", "_", "#", "$", "&", "*", "[", "}");
    $only = str_replace($vowels, "", $tabla);
    sqlconector("DROP TABLE if EXISTS $only");
  }

  function insertTmp($correo,$jugada,$monto,$gana,$moneda){
    $vowels = array("@", ".", "-", "_", "#", "$", "&", "*", "[", "}");
    $only = str_replace($vowels, "", $correo);
	 sqlconector("insert into ".$only." (CORREO,JUGADA,MONTO,GANA,MONEDA) values ('".$correo."','".$jugada."','".$monto."','".$gana."','".$moneda."')");
  }

  function sumJugada($email){
    $vowels = array("@", ".", "-", "_", "#", "$", "&", "*", "[", "}");
    $only = str_replace($vowels, "", $email);
 	 return row_sqlconector("select SUM(MONTO) as SUMA FROM ".$only)['SUMA'];
  }

  function deleteJugada($id,$email){
    $vowels = array("@", ".", "-", "_", "#", "$", "&", "*", "[", "}");
    $only = str_replace($vowels, "", $email);
    sqlconector("delete from ".$only." where ID=".$id);
  }

  function readJugadasTmp($email){
    $cadena="";
    $vowels = array("@", ".", "-", "_", "#", "$", "&", "*", "[", "}");
    $only = str_replace($vowels, "", $email);
    $conexion = mysqli_connect(servidor(),user(),password(),database());

 	 $consulta = "select * from ".$only;
	 $resultado = mysqli_query( $conexion, $consulta );
    while($row = mysqli_fetch_array($resultado)){
      	$cadena= $cadena . "<div style='font-size:1em;'><a style='text-decoration:none; color:#fff;' href='?siexiste=3&email=".$row['CORREO']."&b=".$row['MONEDA']."&borrar=".$row['ID']."'>Delete&nbsp;&nbsp;</a><span>".$row['JUGADA']."&nbsp;</span><span> x&nbsp;".$row['MONTO']."</span></div>";
	  }
    $cadena = $cadena . "";
    mysqli_close($conexion);
	return $cadena;
  }

	function readJugadasTmpAnimalitos($email){
	$cadena="";
	$vowels = array("@", ".", "-", "_", "#", "$", "&", "*", "[", "}");
	$only = str_replace($vowels, "", $email);
	$conexion = mysqli_connect(servidor(),user(),password(),database());

	$consulta = "select * from ".$only;
	$resultado = mysqli_query( $conexion, $consulta );
	while($row = mysqli_fetch_array($resultado)){
			$cadena= $cadena . "<div style='font-size:1em;'><a style='text-decoration:none; color:#fff;' href='?siexiste=3&email=".$row['CORREO']."&b=".$row['MONEDA']."&borrar=".$row['ID']."'>Delete&nbsp;&nbsp;</a><span>".lee_animalito($row['JUGADA'])."&nbsp;</span><span> x&nbsp;".$row['MONTO']."</span></div>";
	}
	$cadena = $cadena . "";
	mysqli_close($conexion);
	return $cadena;
  }

	//****************************************************************************************************************
	//SALA DE JUEGOS

	function ifSalaExist($sala) {
		if($row = row_sqlconector("select * from TORNEO where SALA='".$sala."'")){
			if(strlen($row['SALA'])>0) return TRUE;
			return FALSE;
		}
	}

	if(isset($_GET["revision"])) {
		if(ifSalaExist($_GET['sala'])) echo "true";
		else echo "false";
	}

  //*******************************************************************************************************************
  //Usuarios

if (isset($_POST['validarCajero'])){

//SI ES UN FONDEO
	if(readTicked($_POST['tk'])['TIPO']=='DEPOSITO' && readTicked($_POST['tk'])['PAGADO']=='0'){

		sqlconector("UPDATE OPERACIONES SET MONTO=".$_POST['monto'].", PAGADO=1,ESTATUS='COMPLETADO' WHERE TICKET=".$_POST['tk']);

		updateSaldo(readCliente(readTicked($_POST['tk'])['CLIENTE'])['SALDO'] + readTicked($_POST['tk'])['MONTO'],readTicked($_POST['tk'])['CLIENTE']);

		insertNotif(readTicked($_POST['tk'])['CLIENTE'],"Su Fondeo Ticket: ".$_POST['tk']." se ha Aprobado","../Credit/historial?filtro=month");

		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "soporte@fortunaroyal.com";
		$to = readTicked($_POST['tk'])['CLIENTE'];
		$subject = "Fondeo Fortuna Royal";
		$message = "Su  nueva solicitud de Fondeo, ha sido Completado ".$_POST['email']. " TxId:".readTicked($_POST['tk'])['REFERENCIA']." Monto: ".readTicked($_POST['tk'])['MONTO'];
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
	}

//SI ES UN RETIRO
	if(readTicked($_POST['tk'])['TIPO']=='RETIRO' && readTicked($_POST['tk'])['PAGADO']=='0'){
		sqlconector("UPDATE OPERACIONES SET PAGADO=1,ESTATUS='COMPLETADO', REFERENCIA='".$_POST['refe']."' WHERE TICKET=".$_POST['tk']);

		updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO'] - readTicked($_POST['tk'])['MONTO'],"fortuna@fortunaroyal.com");

		insertNotif(readTicked($_POST['tk'])['CLIENTE'],"Su Retiro Ticket: ".$_POST['tk']." se ha Aprobado","../Credit/historial?filtro=month");

		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "soporte@fortunaroyal.com";
		$to = readTicked($_POST['tk'])['CLIENTE'];
		$subject = "Retiro Fortuna Royal";
		$message = "Su Retiro de Fondos, Ha sido Completado ".$_POST['email']. " TxId:".readTicked($_POST['tk'])['REFERENCIA']." Monto: ".readTicked($_POST['tk'])['MONTO'];
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
	}

	//Si es un Soporte
	if(readTicked($_POST['tk'])['TIPO']=='SOPORTE' && readTicked($_POST['tk'])['PAGADO']=='0'){
		sqlconector("UPDATE OPERACIONES SET PAGADO=1,ESTATUS='COMPLETADO', REFERENCIA='".$_POST['refe']."' WHERE TICKET=".$_POST['tk']);
	}
}
/********************************/

if (isset($_POST['enviado'])){
		sqlconector("UPDATE OPERACIONES SET ENVIADO=1,ESTATUS='ENVIADO', REFERENCIA='".$_POST['refe']."' WHERE TICKET=".$_POST['tk']);
		insertNotif(readTicked($_POST['tk'])['CLIENTE']," Ticket: ".$_POST['tk']." El Cliente ha Informado del envio.","");

		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "soporte@fortunaroyal.com";
		$to = readTicked($_POST['tk'])['CLIENTE'];
		$subject = " Retiro fortunaroyal";
		$message = "El Cajero: ".$_POST['email']. " ha informado que le envio la solicitud Referencia:".readTicked($_POST['tk'])['REFERENCIA']." Monto: ".readTicked($_POST['tk'])['MONTO']." Favor Revise si lo tiene en su wallet y marque la operacion como RECIBIDO, Gracias...!";
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
}

if (isset($_GET['revision'])){
	sqlconector("UPDATE OPERACIONES SET ESTATUS='EN REVISION' WHERE TICKET=".$_GET['tk']);
}

if (isset($_GET['cancelar'])){
	sqlconector("UPDATE OPERACIONES SET ESTATUS='CANCELADO' WHERE TICKET=".$_GET['tk']);
	if(readTicked($_GET['tk'])['TIPO']=="RETIRO"){
		updateSaldo(readCliente(readTicked($_GET['tk'])['CLIENTE'])['SALDO'] + readTicked($_GET['tk'])['MONTO'],readTicked($_GET['tk'])['CLIENTE']);
  }
	if(readTicked($_GET['tk'])['TIPO']=="DONACION"){
		sqlconector("DELETE FROM CARTONRIFA WHERE CODIGORIFA='".readTicked($_GET['tk'])['REFERENCIA']."' AND CLIENTE='".readTicked($_GET['tk'])['CLIENTE']."' AND PAGO=0 AND NUMERO=".readTicked($_GET['tk'])['SUJETO']);
	}
}

function readUserDataMetodo($correo,$metodo){
	return row_sqlconector("select * from USUARIOSMETODOS WHERE CORREO='".$correo."' AND METODO='".$metodo."'");
}

if(isset($_POST['editDataMetodo'])){
 $row=readUserDataMetodo($_POST['correo'],$_POST['editDataMetodo']);
 echo $row['PRECIOVENTA'].
	",".$row['PRECIOCOMPRA'].
	",".$row['SALDOFIAT'].
	",".$row['SALDOFRD'].
	",".$row['ACTIVO'].
	",".$row['ID'];
}

function liberarSaldo($operacion){
	if(readTicked($operacion)['TIPO']=='FONDEO'){
		updateSaldo(readCajero(readTicked($operacion)['CLIENTE'])['SALDO'] + readSaldoRetenido($operacion)['RETENIDO'],readTicked($operacion)['CLIENTE']);
	}
	else if(readTicked($operacion)['TIPO']=='RETIRO'){
		updateSaldo(readCajero(readTicked($operacion)['CAJERO'])['SALDO'] + readSaldoRetenido($operacion)['RETENIDO'],readTicked($operacion)['CAJERO']);
	}
	sqlconector("DELETE FROM SALDORETENIDO WHERE OPERACION='".$operacion."'");
}

/*
Trabajo P2P
*/

if(isset($_GET['verTrabajoPdosP'])) {
	$correo = readVendedor($_SESSION['user'])['CORREO'];

  $conexion = mysqli_connect(servidor(),user(),password(),database());

  $consulta = "select * from OPERACIONES WHERE PAGADO=0 AND SUJETO='P2P' AND CAJERO='{$correo}'  ORDER BY FECHA";

  echo "
	<h2>Trabajos P2P</h2>
  <table style='width:100%;'>
    <th></th>
		<th></th>
    <th></th>
    <th></th>
    <th></th>
  ";
  $resultado = mysqli_query( $conexion, $consulta );
  while($row = mysqli_fetch_array($resultado)){
   $nombre = readCliente($row['CAJERO'])['NOMBRE'];

   echo "<tr>
    <td><span>{$row['FECHA']}</span></td>
		<td><span>{$row['TIPO']}</span></td>
    <td><span>{$row['TICKET']} {$row['ESTATUS']}</span></td>
    <td><span>{$nombre}</span></td>
    <td><button type='button' onclick=\"verPdosP(".$row['TICKET'].")\">Ver</button></td>
    </tr>";
  }
  echo "</table>";
  mysqli_close($conexion);
}

if(isset($_GET['verChatPdosP'])) {
	$sujeto="Envia";
	$moneda1="";
	$moneda2="";
	$boton="";
	$proceso="";
	$ticked = readTicked($_GET['tk']);

	$tenvia1= number_format($ticked['MONTO'],2,",",".");

	$tenvia2= number_format($ticked['RECIBE'],2,",",".");

	if($ticked['TIPO']=="COMPRA"){
		$moneda1 = $ticked['MONEDA'];
		$moneda2= "FRD";
		$boton = "<button type='button' onclick=\"recibido({$_GET['tk']})\">Pago Recibido</button>";
		$proceso = "<br>Recibe: <b>{$tenvia1}</b>{$moneda1}<br>Monto: <b>{$tenvia2}</b>".$moneda2;
	}
	else{
		$moneda1= "FRD";
		$moneda2 = $ticked['MONEDA'];
		$boton = "<button type='button' onclick=\"cancelar({$_GET['tk']})\">Cancelar</button>
							<button type='button' onclick=\"marcarEnvio({$_GET['tk']})\">Pago Enviado</button>
						 ";

		$proceso = "<br>Monto: <b>{$tenvia1}</b>{$moneda1}<br>Transfiere: <b>{$tenvia2}</b>".$moneda2;
	}

	echo "<br><span style='background:FFF;font-weight: 600;font-size:24px;'>".$ticked['TIPO']." ".$ticked['ESTATUS']."</span>";
	echo "<br><b>Fecha: </b>".latinFecha($ticked['FECHA'])."";
	echo $proceso;
	echo "<br>ORDEN ".$ticked['TICKET']."";
	echo "<br>";
	echo $boton;
	$chateo = " <div style='font-size: 0.8em; display: block; overflow: hidden; width: 100%; height: auto; paddin:3px;'>
	<br>
	<label style='padding:3px; font-weight: 600;background:#FFEEEE;'>Chat: </label>
	<input type='hidden' id='ticked' value='".$_GET['tk']."'>
	<input type='hidden' id='envia' value='".readVendedor($_SESSION['user'])['CORREO']."'>
	</div>
		<div style='background:#D4DEE2; width: 96%; height: 250px; overflow-x: hidden;overflow-y: scroll;' id='chat'></div>
		<input autocomplete='off' style='font-size:16px;border:0; background:#EAECED; color: #333;display:inline-block; width: 60%;padding: 21px;' type='text' id='mensaje' onkeyup='myFunction(event)'>
		<button style='display:inline-block;cursor:pointer; font-weight: 600; border: 2px solid #333; border-radius: 5px; font-size: 16px;font-weight: bold; color:#fff;padding: .5rem 1rem;; background:#EA4040;' onclick='chat()'>Enviar</button>
	";
	echo $chateo;
}

if(isset($_POST['recibirCompra'])){
	$ticked = readTicked($_POST['recibirCompra']);
	$cliente = $ticked['CLIENTE'];
	$montoFrd = readCliente($cliente)['SALDO'] + $ticked['MONTO'];
	sqlconector("UPDATE USUARIOS SET SALDO={$montoFrd} WHERE CORREO='{$cliente}'");
	sqlconector("UPDATE OPERACIONES SET PAGADO=1, ESTATUS='COMPLETADO' WHERE TICKET=".$ticked['TICKET']);
	sqlconector("UPDATE USUARIOSMETODOS SET ACTIVO=1 WHERE CORREO = '{$ticked['CAJERO']}'");
}

if(isset($_POST['cancelarVenta'])){
	$ticked = readTicked($_POST['cancelarVenta']);
	$cliente = $ticked['CLIENTE'];
	$metodo = $ticked['MONEDA'];
	$montoFrd = readCliente($cliente)['SALDO'] + $ticked['MONTO'];
	$fiat = $ticked['MONEDA'];
	$saldoCajero = readUserDataMetodo( $ticked['CAJERO'],$fiat)['SALDOFIAT'] + $ticked['RECIBE'];
	sqlconector("UPDATE USUARIOSMETODOS SET SALDOFIAT={$saldoCajero},ACTIVO=1  WHERE CORREO='{$ticked['CAJERO']}' AND METODO='{$fiat}'");
	sqlconector("UPDATE USUARIOS SET SALDO={$montoFrd} WHERE CORREO='{$cliente}'");
	sqlconector("UPDATE OPERACIONES SET PAGADO=1, ESTATUS='CANCELADO' WHERE TICKET=".$ticked['TICKET']);
	insertChat($ticked['CAJERO'],$ticked['TICKET'],$ticked['CAJERO'],$cliente,"El Anunciante Ha Cancelado la Orden...!");
}

if(isset($_POST['marcarEnvio'])){
	$ticked = readTicked($_POST['marcarEnvio']);
	sqlconector("UPDATE OPERACIONES SET ENVIADO=1, ESTATUS='CONFIRMACION' WHERE TICKET=".$ticked['TICKET']);
	insertChat($ticked['CAJERO'],$ticked['TICKET'],$ticked['CAJERO'],$ticked['CLIENTE'],"El Anunciante Confirma que envio el Pago...!");
}

/*
Trabajo de los cajeros
*/
	if(isset($_GET['vertrabajocajero'])) {
		if(isset($_GET['ticked'])){
		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
			<span style='font-weight: bold; font-size: large; '><a onclick=\"$('#frame').load('admin?vertrabajocajero')\">Trabajos</a></span>";
			if(isset($_GET['tipo'])) {
				echo "<span style='padding-left:89px; font-weight: bold; font-size: large; border-left: solid 1px; border-left-color: gray;'>Ticket ".$_GET['ticked']."</span>";
			}
		echo "</div>";

		if($_GET['tipo']=='SOPORTE'){
			$cliente="";
			$correo="";
			$color_ref="#000000";
			$ver_ref="";
			$cliente=readCliente(readTicked($_GET['ticked'])['CLIENTE'])['NOMBRE']. " ".makeAnciEstrellas(readCliente(readTicked($_GET['ticked'])['CLIENTE'])['RATE']);
			$correo=readTicked($_GET['ticked'])['CLIENTE'];

			if(if_exist_ref(readTicked($_GET['ticked'])['REFERENCIA'])) {
				$color_ref="#BC0000";
				$ver_ref = ver_ref(readTicked($_GET['ticked'])['REFERENCIA']);
			}
			else {
				$color_ref="#000000";
				$ver_ref="";
			}

			echo "<div style='margin-top: 8px;'><span style='color:black; font-size:21px;'>".$_GET['tipo']. " </span></div>
			<div>
					".readTicked($_GET['ticked'])['ESTATUS']."<br>
					Cliente: ".$cliente. "<br>
					Correo: ".$correo."<br>
					Motivo: <input readonly style='font-size:16px; border:0;outline:0;font-weight:bold;' id='refe' value='".readTicked($_GET['ticked'])['REFERENCIA']."'><br>
					<input type='hidden' id='tipo' value='".$_GET['tipo']."'>
					<input type='hidden' id='tk' value='".$_GET['ticked']."'>
					<input type='hidden' style='font-size:16px;color:black;' value='0' id='vaca' name='vaca'>
					<input type='hidden' id='email' value='".readVendedor($_SESSION['user'])['CORREO']."'>
					<br>
					<a style='text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"borrar('".$_GET['ticked']."')\">Cancelar</a>
					<a style='text-decoration:none; color:black; border:solid 1px; background:#F4F4AA;' class='btn btn-jugar' onclick='revision(".$_GET['ticked'].");'>Poner en Revision</a>
					<button class='btn btn-jugar' onclick='validarCajero();'>Cerrar Caso</button>
			</div>
				";
		}

			if($_GET['tipo']=='DEPOSITO'){
				$cliente="";
				$correo="";
				$color_ref="#000000";
				$ver_ref="";
				$cliente=readCliente(readTicked($_GET['ticked'])['CLIENTE'])['NOMBRE']. " ".makeAnciEstrellas(readCliente(readTicked($_GET['ticked'])['CLIENTE'])['RATE']);
				$correo=readTicked($_GET['ticked'])['CLIENTE'];

				if(if_exist_ref(readTicked($_GET['ticked'])['REFERENCIA'])) {
					$color_ref="#BC0000";
					$ver_ref = ver_ref(readTicked($_GET['ticked'])['REFERENCIA']);
				}
				else {
					$color_ref="#000000";
					$ver_ref="";
				}

				echo "<div style='margin-top: 8px;'><span style='color:black; font-size:21px;'>".$_GET['tipo']. " EN: ".readTicked($_GET['ticked'])['MONEDA']."</span></div>
				<div>
						Cliente: ".$cliente. "<br>
						Correo: ".$correo."<br>
						Por: FRD <strong style='font-size:16px;color:black;'>".readTicked($_GET['ticked'])['RECIBE']."</strong><br>
						Revisa si El Cliente Envio: <input type='text' style='font-size:16px;color:black;' value='".number_format(readTicked($_GET['ticked'])['MONTO'],2,",",".")."' id='vaca' name='vaca'> ".readTicked($_GET['ticked'])['MONEDA']."<br>
						Id de Transaccion: <strong style='font-size:13px;color:".$color_ref.";' id='refe'>".readTicked($_GET['ticked'])['REFERENCIA']."</strong>".$ver_ref."<br>
						WALLET (DESTINO) : <strong style='font-size:16px;color:black;'>".readTicked($_GET['ticked'])['WALLET']."</strong><br>
						<input type='hidden' id='tipo' value='".$_GET['tipo']."'>
						<input type='hidden' id='tk' value='".$_GET['ticked']."'>
						<input type='hidden' id='email' value='".readVendedor($_SESSION['user'])['CORREO']."'>
						<br>
						<a style='text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"borrar('".$_GET['ticked']."')\">Cancelar</a>
						<a style='text-decoration:none; color:black; border:solid 1px; background:#F4F4AA;' class='btn btn-jugar' onclick='revision(".$_GET['ticked'].");'>Poner en Revision</a>
						<button class='btn btn-jugar' onclick='validarCajero();'>Validar</button>
				</div>
					";
			}

			if($_GET['tipo']=='RETIRO'){
				$cliente="";
				$correo="";
				$telefono="";
				if (readTicked($_GET['ticked'])['SUJETO']=='CLI'){
					$cliente=readCliente(readTicked($_GET['ticked'])['CLIENTE'])['NOMBRE']. " ".makeAnciEstrellas(readCliente(readTicked($_GET['ticked'])['CLIENTE'])['RATE']);
					$correo=readTicked($_GET['ticked'])['CLIENTE'];
					$telefono=readCliente(readTicked($_GET['ticked'])['CLIENTE'])['TELEFONO'];
				}

				echo "<div style='margin-top: 8px;'>
						<span style=' color:black; font-size:21px;'> ".$_GET['tipo']." A: ".readTicked($_GET['ticked'])['MONEDA']."</span>
						</div>
						<div>
							Cliente: ".$cliente. "<br>
							Correo: ".$correo."<br>
							Monto de la Operacion: ".number_format(readTicked($_GET['ticked'])['MONTO'],2,",",".")." FRD<br>
							Envia Al Cliente la Cantidad (USDT): <input type='text' readonly style='font-weight:bold; border:0; font-size:16px;color:black;' value='". number_format(readTicked($_GET['ticked'])['RECIBE'],2,",",".")."' id='vaca' name='vaca'><br>
							WALLET (DESTINO): <strong style='font-size:16px;color:black;'>".readTicked($_GET['ticked'])['WALLET']."</strong><br>
							Telefono:<strong style='font-size:16px;color:black;'> ".$telefono."</strong><br>
							<label>Id de Transaccion: </label><input  type='text' id='refe'>
							<div style='margin: 8px;'>
							<input type='hidden' id='tipo' value='".$_GET['tipo']."'>
							<input type='hidden' id='tk' value='".$_GET['ticked']."'>
							<input type='hidden' id='email' value='".readVendedor($_SESSION['user'])['CORREO']."'>
							<br>
							<a style='text-decoration:none; color:black; border:solid 1px; background:#7F7F7F;' class='btn btn-jugar' onclick=\"borrar('".$_GET['ticked']."')\">Cancelar</a>
							<a style='text-decoration:none; color:black; border:solid 1px; background:#F4F4AA;' class='btn btn-jugar' onclick='revision(".$_GET['ticked'].");'>Poner en Revision</a>

					";
					if(readVendedor($_SESSION['user'])['NIVEL']==2){
						echo "<button class='btn btn-jugar' onclick='validarCajero()'>Validar</button></div>";
					}
					echo "</div>";
			}
		}
		else{
		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
			<span style='font-weight: bold; font-size: large; '><a onclick=\"$('#frame').load('admin?vertrabajocajero')\">Trabajos</a></span>";
			if(isset($_GET['tipo'])) {
				echo "<span style='padding-left:89px; background-color: silver;font-weight: bold; font-size: large; border-left: solid 1px; border-left-color: gray;'>Ver</span>";
			}
		echo "</div>";
				  $color="";
				  $mon="FRD";
				  $bg="";
				  $conexion = mysqli_connect(servidor(),user(),password(),database());

				  $consulta = "select * from OPERACIONES where CAJERO='".readVendedor($_SESSION['user'])['CORREO']."'AND PAGADO=0 AND ENVIADO=0 AND NOT ESTATUS='CANCELADO' order by fecha desc";
				  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar el Historico del Vendedor");
				  $cadena="
				  <div class='container' style='margin-top: 25px;padding: 10px'>
				  <table style='width: 100%; text-align:center; fone-size:0.7em; padding: 13px 0;'>
				  <thead>
				  <th>Msn</th>
				  <th>Ticket</th>
				  <th>Monto</th>
				 </thead><tbody>";
				  while($row = mysqli_fetch_array($resultado)){
					    if($row['ESTATUS']=='CANCELADO'){$bg="#F2D1C8";}
						if($row['ESTATUS']=='EN PROCESO'){$bg="#C8F0F2";}
						if($row['ESTATUS']=='COMPLETADO'){$bg="#B5F1A4";}
						if($row['ESTATUS']=='EN REVISION'){$bg="#F1EF63";}

						$color=chatColor($row['TICKET'],readVendedor($_SESSION['user'])['CORREO']);

						$cadena=$cadena. "<tr>
						<td><span style='font-size:21px; color:".$color.";'>&#9993;</span></td>
						<td style='color:black;background-color: ".$bg.";'><span><a style='text-decoration:none; color:black;' onclick='irTicket(\"".$row['SUJETO']."\",\"".$row['TIPO']."\",\"".$row['TICKET']."\",\"".$row['CAJERO']."\");'>
						<b>".$row['TICKET']."	</b> - ".$row['TIPO']."<span style='font-size:11px;'> (".substr($row['REFERENCIA'],0,15).")</span></span></a></td>
						<td>".$row['RECIBE']."<span style='font-weight: bold; color:#6FB06F;'>".$row['MONEDA']."</span></td>
						</tr>";
				  }
				  $cadena = $cadena."</tbody></table><br><br>";
				  mysqli_close($conexion);
				  echo $cadena;

		}

		if (isset($_GET['tipo'])){
			echo"
				<div style='font-size: 0.8em; display: block; overflow: hidden; width: 100%; height: auto; paddin:3px;'>
				<br>
				<label style='padding:3px; font-weight: 600;background:#FFEEEE;'>Chat: </label>";
			echo "
				<input type='hidden' id='ticked' value='".$_GET['tickedchat']."'>
				<input type='hidden' id='envia' value='".readVendedor($_SESSION['user'])['CORREO']."'>
				</div>
				";
		echo "<div style='background:#FFEEEE;width: 100%; height: 250px; overflow-x: hidden;overflow-y: scroll;' id='chat'></div>";
 			chatLeido($_GET['tickedchat']);
			if(!ifChatCerrado($_GET['tickedchat'])){
				echo "<div style='background-color: #FFF; width:100%; height: 90px; padding:20px;'>
					<input autocomplete='off' style='border: 2px solid #333; border-radius: 5px;color: #333;margin: 0 0 5px;display:inline-block; width: 80%;padding: .5rem 1rem;' type='text' id='mensaje' onkeyup='myFunction(event)'>
					<button style='display:inline-block;cursor:pointer; font-weight: 600; border: 2px solid #333; border-radius: 5px; font-size: 16px;font-weight: bold; color:#fff;padding: .5rem 1rem;; background:#EA4040;' onclick='chat()'>Enviar</button>
				";
				if(readVendedor($_SESSION['user'])['NIVEL']=='2'){ echo "<br><br><button style='width: 94%;border: 2px solid #333;color:#fff;border-radius:5px;padding:.5rem;margin-left:5px; background:#776A6A; ' onclick='cerrarchat()'>Bloquear</button>";}
				echo "</div>";
			}
			else{
				echo "<br><label>Chat Cerrado...</label>";
			}
		}
	}

/*
Fin Trabajo de Cajeros
*/

	if(isset($_GET['verhistorial'])) {
		echo "<div style='border-bottom: solid 1px;text-align: center;width:100%;display:inline-block;'>
				Historial
				<span style='font-weight: bold; font-size: large; '>
				<a style='text-decoration:underline;padding:5px;' onclick=\"$('#frame').load('admin?verhistorial&historial=dia')\">Dia</a>
				<a style='text-decoration:underline;padding:5px;' onclick=\"$('#frame').load('admin?verhistorial&historial=mes')\">Mes</a>
				<a style='text-decoration:underline;padding:5px;' onclick=\"$('#frame').load('admin?verhistorial&historial=all')\">Todo</a>
				</span>";
		if(isset($_GET['ver'])) {
				echo "<span style='color:gray; font-size:21px;'> | </span><span style='padding-left:89px; background-color: #FFF;font-weight: bold; font-size: large; border-radius:3px; color:black;'>Ticket ".$_GET['tk']."</span>";
		}
		echo "</div>";
		if(isset($_GET['historial'])) {
			  $color="";
			  $mon="FRD";
			  $bg="";
			  $color_tipo="";
			  $format_monto="";
			  $if_retiro="";
			  $verT="";
			  $conexion = mysqli_connect(servidor(),user(),password(),database());

				 if($_GET['historial']=="mes"){
					 $consulta = "select * from OPERACIONES where (CLIENTE='".readVendedor($_SESSION['user'])['CORREO']."' OR CAJERO='".readVendedor($_SESSION['user'])['CORREO']."') AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by fecha desc";
				 }
				 else if($_GET['historial']=="all"){
					 $consulta = "select * from OPERACIONES where (CLIENTE='".readVendedor($_SESSION['user'])['CORREO']."' OR CAJERO='".readVendedor($_SESSION['user'])['CORREO']."') order by fecha desc";
				 }
				 else {
					 $consulta = "select * from OPERACIONES where (CLIENTE='".readVendedor($_SESSION['user'])['CORREO']."' OR CAJERO='".readVendedor($_SESSION['user'])['CORREO']."') AND DAY(FECHA)= DAY(CURRENT_TIMESTAMP()) AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by fecha desc";
				 }
			  $resultado = mysqli_query( $conexion, $consulta );
			  echo "
			  <div class='container' style='margin-top: 25px;padding: 5px'>
			  <table style='width: 100%; text-align:center; font-size:0.8em;'>
			  <thead>
			  <th></th>
			  <th></th>
			  <th></th>
			  </thead><tbody>";
			  while($row = mysqli_fetch_array($resultado)){
				   if($row['ESTATUS']=='CANCELADO'){$bg="#F2D1C8";}
					if($row['ESTATUS']=='EN PROCESO'){$bg="#C8F0F2";}
					if($row['ESTATUS']=='COMPLETADO'){$bg="#B5F1A4";}
					if($row['ESTATUS']=='EN REVISION'){$bg="#F1EF63";}
					if($row['TIPO']=='DEPOSITO'){$mon="FRD"; $color_tipo="#2CA201";$if_retiro="";}
					if($row['TIPO']=='RETIRO'){$color_tipo="#FF0000";$if_retiro="FRD";}
					if($row['MONEDA']=='BOLIVARES' || $row['MONEDA']=='AIRTM' || $row['MONEDA']=='PAYPAL' || $row['MONEDA']=='TETHER') {
						$format_monto=number_format($row['MONTO'],2,",",".");
					}
					else {
						$format_monto=$row['MONTO'];
					}

					$color=chatColor($row['TICKET'],readVendedor($_SESSION['user'])['CORREO']);

					if($row['PAGADO']==0 && $row['ESTATUS']!='CANCELADO' && readVendedor($_SESSION['user'])['NIVEL'] > 0 && $row['CLIENTE']!=readVendedor($_SESSION['user'])['CORREO']){
						$verT="$('#frame').load('admin?vertrabajocajero&open&sujeto=".$row['SUJETO']."&tipo=".$row['TIPO']."&ticked=".$row['TICKET']."&email=".$row['CLIENTE']."&tickedchat=".$row['TICKET']."');";
					}
					else{
						$verT="$('#frame').load('admin?verhistorial&open&tipo&ver&tk=".$row['TICKET']."&tickedchat=".$row['TICKET']."');";
					}
				  echo  "<tr>
				  <td><span style='font-size:21px; vertical-align:middle; color: ".$color.";'>&#9993;</span> </td>
				  <td><div style='border-radius: 5px;background:".$bg.";'><span style='font-size:8px;color:black;font-weight: 600;'>".$row['ESTATUS']."</span></div></td>
				  <td>
					  <a style='font-size:13px; text-decoration:none; color:black;' onclick=\"".$verT."\"><div  class='teta'>
					  Ticket: ".$row['TICKET']." <span style='color:".$color_tipo."; font-weight: 600;'>".$row['TIPO']."</span>
					  en ".$row['MONEDA']." Monto: ".$format_monto.$if_retiro." ".latinFecha($row['FECHA'])."</div></a>
				  </td>
				  </tr>";
			  }
			  echo "</tbody></table></div><br><br>";
			  mysqli_close($conexion);
			}
			if (isset($_GET['ver'])){
				$sujeto="Envia";
				$moneda1="";
				$moneda2="";
				$ticked = readTicked($_GET['tk']);

				if($ticked['ESTATUS']=='CANCELADO'){$bg="#F2D1C8";}
				if($ticked['ESTATUS']=='EN PROCESO'){$bg="#C8F0F2";}
				if($ticked['ESTATUS']=='COMPLETADO'){$bg="#B5F1A4";}
				if($ticked['ESTATUS']=='EN REVISION'){$bg="#F1EF63";}
				if($ticked['ESTATUS']=='ENVIADO'){$bg="#A9D0F5";}

				if($ticked['TIPO']=='DEPOSITO'){$moneda1=$ticked['MONEDA'];}else{$moneda1="FRD";}

				if($ticked['TIPO']=='RETIRO'){$moneda2=$ticked['MONEDA'];}else{$moneda2="FRD";}

				switch($ticked['TIPO']){
					case 'RIFA':
						$sujeto="Vendedor: ".readCliente($ticked['CAJERO'])['NOMBRE'].makeAnciEstrellas(readCliente($ticked['CAJERO'])['RATE']).
							"<br>Email: ".readCliente($ticked['CAJERO'])['CORREO']."";
					break;
					case 'DEPOSITO':{
						if(readVendedor($_SESSION['user'])['NIVEL']=='0'){
							$sujeto="Cajero: ".readCliente($ticked['CAJERO'])['NOMBRE'].makeAnciEstrellas(readCliente($ticked['CAJERO'])['RATE']).
								"<br>Email: ".readCliente($ticked['CAJERO'])['CORREO']."";
						}else{
							$sujeto="Cliente: ".readCliente($ticked['CLIENTE'])['NOMBRE'].makeAnciEstrellas(readCliente($ticked['CLIENTE'])['RATE']).
								"<br>Email: ".readCliente($ticked['CLIENTE'])['CORREO']."";
						}
					}
					break;
					case 'RETIRO':{
						if(readVendedor($_SESSION['user'])['NIVEL']=='0'){
							$sujeto="Cajero: ".readCliente($ticked['CAJERO'])['NOMBRE'].makeAnciEstrellas(readCliente($ticked['CAJERO'])['RATE']).
								"<br>Email: ".readCliente($ticked['CAJERO'])['CORREO']."";
						}else{
							$sujeto="Cliente: ".readCliente($ticked['CLIENTE'])['NOMBRE'].makeAnciEstrellas(readCliente($ticked['CLIENTE'])['RATE']).
								"<br>Email: ".readCliente($ticked['CLIENTE'])['CORREO']."";
						}
					}
					break;
				}

				$tenvia1;
				if($moneda1=="BOLIVARES" || $moneda1=="PAYPAL" || $moneda1=="AIRTM" || $moneda1=="FRD") {
					$tenvia1= number_format($ticked['MONTO'],2,",",".");
				}else {
					$tenvia1= number_format($ticked['MONTO'],6,",",".");
				}

				$tenvia2;
				if($moneda2=="BOLIVARES" || $moneda2=="PAYPAL" || $moneda2=="AIRTM" || $moneda2=="FRD") {
					$tenvia2= number_format($ticked['RECIBE'],2,",",".");
				}else {
					$tenvia2= number_format($ticked['RECIBE'],6,",",".");
				}

				echo "<div style='background:#fff;padding:13px;'><input type='hidden' id='ticket' value='".$ticked['TICKET']."'";
				echo "<br><span style='background:FFF;font-weight: 600;font-size:24px;color:".$bg.";'>".$ticked['TIPO']." ".$ticked['ESTATUS']."</span>";
				echo "<br><b>Fecha: </b>".latinFecha($ticked['FECHA'])."";
				echo "<br>Monto: <b>".$tenvia1."</b> ".$moneda1;
				echo "<br>Recibe: <b>".$tenvia2."</b> <span style='color:#008000;'>".$moneda2."</span>";
				echo "<br>Referencia: ".$ticked['REFERENCIA']."";
				echo "<br>".$sujeto;
				echo"
					<div style='font-size: 0.8em; display: block; overflow: hidden; width: 100%; height: auto; paddin:3px;'>
					<br>
					<label style='padding:3px; font-weight: 600;background:#FFEEEE;'>Chat: </label>";
				echo "
					<input type='hidden' id='ticked' value='".$_GET['tickedchat']."'>
					<input type='hidden' id='envia' value='".readVendedor($_SESSION['user'])['CORREO']."'>
					</div>
					";
			echo "<div style='background:#FFEEEE; width: 100%; height: 250px; overflow-x: hidden;overflow-y: scroll;' id='chat'></div>";
	 			chatLeido($_GET['tickedchat']);
				if(!ifChatCerrado($_GET['tickedchat'])){
					echo "<div style='background-color: #FFF; width:100%; height: 90px; padding:20px;'>
						<input autocomplete='off' style='border: 2px solid #333; border-radius: 5px;color: #333;margin: 0 0 5px;display:inline-block; width: 80%;padding: .5rem 1rem;' type='text' id='mensaje' onkeyup='myFunction(event)'>
						<button style='display:inline-block;cursor:pointer; font-weight: 600; border: 2px solid #333; border-radius: 5px; font-size: 16px;font-weight: bold; color:#fff;padding: .5rem 1rem;; background:#EA4040;' onclick='chat()'>Enviar</button>
					";
					if(readVendedor($_SESSION['user'])['NIVEL']=='2'){ echo "<br><br><button style='width: 94%;border: 2px solid #333;color:#fff;border-radius:5px;padding:.5rem;margin-left:5px; background:#776A6A; ' onclick='cerrarchat()'>Bloquear</button>";}
					echo "</div>";
				}
				else{
					echo "<br><label>Chat Cerrado...</label>";
				}
			}
	}

  if (isset($_GET['asociado']) && isset($_GET['email'])){
	if (!empty(readVendedor2($_GET['email'])['CORREO'])) echo "true";
	else echo "false";
  }

  if (isset($_GET['delete']) && isset($_GET['email'])){
	deleteTmp($_GET['email']);
  }

  if (isset($_GET['saldo']) && isset($_GET['email'])){
	  $sal=0;
	  if (!empty(readVendedor2($_GET['email'])['SALDO']))$sal=readVendedor2($_GET['email'])['SALDO'];
	  echo number_format($sal,2,",",".")." <span style='color:white; font-size:13px; '>FRD";
  }

  if (isset($_GET['saldito'])){
	  $sal=0;
	  $sal=readVendedor($_SESSION['user'])['SALDO'];
	  echo $sal;
  }

	if (isset($_POST['restarfrt'])){
		sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo(readVendedor($_SESSION['user'])['CORREO'],"FRT") - $_POST['monto'])." WHERE TOKEN='FRT' AND CORREO='".readVendedor($_SESSION['user'])['CORREO']."'");
		sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo("fortuna@fortunaroyal.com","FRT") + $_POST['monto'])." WHERE TOKEN='FRT' AND CORREO='fortuna@fortunaroyal.com'");
  }

  if (isset($_POST['sumarfrt'])){
		 sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo(readVendedor($_SESSION['user'])['CORREO'],"FRT") + $_POST['monto'])." WHERE TOKEN='FRT' AND CORREO='".readVendedor($_SESSION['user'])['CORREO']."'");

		 if(loQueTengo("fortuna@fortunaroyal.com","FRT")>=$_POST['monto']) {
 			sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo("fortuna@fortunaroyal.com","FRT") - $_POST['monto'])." WHERE TOKEN='FRT' AND CORREO='fortuna@fortunaroyal.com'");
 	 	 }
 		 else {
 			sqlconector("UPDATE WALLETOKEN SET CANTIDAD=0 WHERE TOKEN='FRT' AND CORREO='fortuna@fortunaroyal.com'");
 		}
	   $rate=1;
	   $conexion = mysqli_connect(servidor(),user(),password(),database());

	   if($_POST['monto']>10 && $_POST['monto']<50 ){$rate=2;}
	   else if($_POST['monto']>50 && $_POST['monto']<100){$rate=3;}
	   else if($_POST['monto']>100 && $_POST['monto']<1000){$rate=4;}

		$consulta = "INSERT INTO GANADORES(CORREO, NOMBRE,JUEGO,RATE,MONTO) VALUES('".readVendedor($_SESSION['user'])['CORREO']."','".readVendedor($_SESSION['user'])['NOMBRE']."','".$_POST['juego']."',".$rate.",".$_POST['monto'].")";

		$resultado = mysqli_query( $conexion, $consulta );

		mysqli_close($conexion);
  }

  if (isset($_POST['restar'])){
		updateSaldo(readVendedor($_SESSION['user'])['SALDO']-$_POST['monto'],readVendedor($_SESSION['user'])['CORREO']);
		updateSaldo(readVendedor("fortuna@fortunaroyal.com")['SALDO'] + $_POST['monto'], "fortuna@fortunaroyal.com");
  }

  if (isset($_POST['sumar'])){
		updateSaldo(readVendedor($_SESSION['user'])['SALDO']+$_POST['monto'],readVendedor($_SESSION['user'])['CORREO']);

		if(readVendedor("fortuna@fortunaroyal.com")['SALDO']>=$_POST['monto']) {
			updateSaldo(readVendedor("fortuna@fortunaroyal.com")['SALDO'] - $_POST['monto'], "fortuna@fortunaroyal.com");
		}
		else {
			updateSaldo("0","fortuna@fortunaroyal.com");
		}
	   $rate=1;
	   $conexion = mysqli_connect(servidor(),user(),password(),database());

	   if($_POST['monto']>10 && $_POST['monto']<50 ){$rate=2;}
	   else if($_POST['monto']>50 && $_POST['monto']<100){$rate=3;}
	   else if($_POST['monto']>100 && $_POST['monto']<1000){$rate=4;}

		$consulta = "INSERT INTO GANADORES(CORREO, NOMBRE,JUEGO,RATE,MONTO) VALUES('".readVendedor($_SESSION['user'])['CORREO']."','".readVendedor($_SESSION['user'])['NOMBRE']."','".$_POST['juego']."',".$rate.",".$_POST['monto'].")";

		$resultado = mysqli_query( $conexion, $consulta );

		mysqli_close($conexion);
  }

	/*****************************************************************************************************************
	NOTIFICACIONES*/

  function countNotif($email){
	 return row_sqlconector("select COUNT(*) AS TOTAL from NOTIFICACIONES WHERE CORREO='".$email."' AND VISTO=0")['TOTAL'];
  }

  function notif($email){
	$cadena="";
    $conexion = mysqli_connect(servidor(),user(),password(),database());

    $consulta = "DELETE FROM NOTIFICACIONES WHERE CORREO='".$email."' AND VISTO=1";
	$resultado = mysqli_query( $conexion, $consulta );

    $consulta = "SELECT * FROM NOTIFICACIONES WHERE CORREO='".$email."' AND VISTO=0";
	if($resultado = mysqli_query( $conexion, $consulta )){
	   if(countNotif($email)>0){
			$cadena= $cadena ."<li style='text-align: center;'><a onclick=\"marcar('".$email."');\" style='font-weight: bolder;font-size:11px; color: #f12020; background:black;'>Marcar Como Leidas</a></li> <script>document.getElementById('campana').style.animation='glow 1s ease-in-out infinite alternate'; </script>";
	   }
	   else{
			$cadena = $cadena . "<li style='text-align:center;'>No Hay Nada</li> <script> document.getElementById('campana').style.animation='none 1s ease-in-out infinite alternate'; </script>";
	   }
	   while($row = mysqli_fetch_array($resultado)){
			$cadena= $cadena. "<li>".$row['NOTICIA']."</li>";
	   }
	}
    mysqli_close($conexion);
    return $cadena;
  }

  function insertNotif($correo,$noticia,$ubicacion){
	sqlconector("INSERT INTO NOTIFICACIONES(CORREO,NOTICIA,UBICACION) VALUES('".$correo."','".$noticia."','{$ubicacion}')");
  }

  if(isset($_POST['marcarNotif'])){
	sqlconector("UPDATE NOTIFICACIONES SET VISTO=1 WHERE CORREO='".$_POST['correo']."'");
  }

  if(isset($_GET['notif'])){
	echo notif($_GET['email']);
  }

	/**********************************************************************************************************************
	*/

  function updateUsuario($email,$campo,$valor){
	sqlconector("UPDATE USUARIOS SET ".$campo."='".$valor."' WHERE CORREO='".$email."'");
  }

  function updateSaldo($valor,$correo){
	sqlconector("UPDATE USUARIOS SET SALDO=".$valor." WHERE CORREO='".$correo."'");
  }

	function siClienteExiste($email){
		if(strlen(row_sqlconector("select * from USUARIOS where CORREO='".$email."'")['CORREO'])>0) return TRUE;
		return FALSE;
	}

	function siClienteActivo($email){
		if(row_sqlconector("select * from USUARIOS where CORREO='".$email."'")['ACTIVO']==1) return TRUE;
		return FALSE;
	}

	function readCliente($email){
		return row_sqlconector("select * from USUARIOS where CORREO='".$email."'");
	}

	function alertaRifa($correo){
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

		$consulta = "select * from RIFA WHERE BLOQUEADO=0 AND USUARIO='".$correo."'";
		if($resultado = mysqli_query( $conexion, $consulta )){
			while($row = mysqli_fetch_array($resultado)){
				$date1=date_create($row['FECHASORTEO']);
				if(date_format($date1,"Y/m/d")==date("Y/m/d")){
					//insertNotif($correo,"Hoy Recuerde Sorteo Rifa ".$row['TEMA']);
				}
			}
			mysqli_close($conexion);
		}
	}

	function readRifa($codigorifa){
		return row_sqlconector("select * from RIFA where CODIGORIFA='".$codigorifa."'");
	}

	if(isset($_POST['ganador'])){
		$row = row_sqlconector("select * from CARTONRIFA where NUMERO=".$_POST['numero']." AND CODIGORIFA='".$_POST['rifa']."'");
		if(!empty($row['CODIGORIFA'])){
			sqlconector("UPDATE CARTONRIFA SET GANADOR=1 WHERE CODIGORIFA='".$_POST['rifa']."' AND NUMERO=".$_POST['numero']."");
			sqlconector("UPDATE RIFA SET GANADOR=1,BLOQUEADO=1 WHERE CODIGORIFA='".$_POST['rifa']."'");
			$ticket=generaTicket();
			sqlconector("INSERT INTO OPERACIONES(PAGADO,TICKET,CAJERO,CLIENTE,TIPO,REFERENCIA,MONTO,RECIBE,ESTATUS,MONEDA)
					VALUES(1,".$ticket.",'"
					.readRifa($_POST['rifa'])['USUARIO']."','"
					.readRifa($_POST['rifa'])['CLIENTE']."','RIFA','"
					.$_POST['rifa']."',"
					.readRifa($_POST['rifa'])['MONTONUMERO'].","
					.readRifa($_POST['rifa'])['MONTONUMERO'].",'EN PROCESO','FRD')");

			//insertNotif(readRifa($_POST['rifa'])['CLIENTE'],"Te Has Ganado el Premio: ".readRifa($_POST['rifa'])['TEMA']." con el Numero".$_POST['numero']);
			if(!if_wallet_exist(readRifa($_POST['rifa'])['CLIENTE'],"FRT")) {
				create_wallet(readRifa($_POST['rifa'])['CLIENTE'],"FRT");
			}
			update_saldo_token_positivo(readRifa($_POST['rifa'])['MONTONUMERO'],readRifa($_POST['rifa'])['CLIENTE'],"FRT");

			ini_set( 'display_errors', 1 );
			error_reporting( E_ALL );
			$from = "soporte@fortunaroyal.com";
			$to = readRifa($_POST['rifa'])['CLIENTE'];
			$subject = "Numero Ganador en Donativos ".readRifa($_GET['rifa'])['TEMA'];
			$message = "Has Ganado con el N. ".$_POST['numero']." del Financiamiento ".readRifa($_POST['rifa'])['TEMA']." en el que participaste,".
			" Los token ganados se veran reflejados en tu wallet, Sino estas registrado en nuestra plataforma te invitamos a hacerlo para recibir ".
			"tus token y canjearlos o simplemente jugar para multiplicarlos desde la comodidad de tu casa. <a href='https://www.fortunaroyal.com/'>Fortuna Royal</a> ";
			$headers = "From:" . $from;
			mail($to,$subject,$message, $headers);

			sqlconector("INSERT INTO GANADORES(CORREO, NOMBRE,JUEGO,RATE,MONTO) VALUES('".readRifa($_POST['rifa'])['CLIENTE']."','".readVendedor(readRifa($_POST['rifa'])['CLIENTE'])['NOMBRE']."','FundMe',1,0)");
			echo "Hubo un Ganador en tu Financiamiento con el N.".$_POST['numero'];
		}
		else{
			sqlconector("UPDATE RIFA SET GANADOR=0,BLOQUEADO=1 WHERE CODIGORIFA='".$_POST['rifa']."'");
			update_saldo_token_positivo(readRifa($_POST['rifa'])['MONTONUMERO'],readRifa($_POST['rifa'])['USUARIO'],"FRT");
			echo "Salio el N. ".$_POST['numero']." No Hubo Ganador en el Financiamiento..!";
		}
	}

	function readSaldoRetenido($operacion){
		return row_sqlconector("select * from SALDORETENIDO where OPERACION='".$operacion."'");
	}

	function readMetodo($id){
		return row_sqlconector("select * from METODOS WHERE ID=".$id);
	}

	function readMetodo2($metodo){
		return row_sqlconector("select * from METODOS WHERE METODO='".$metodo."'");
	}

	function recalcMetodo($metodo,$newPrecio){
	    $porcenVenta=($newPrecio * readMetodo2($metodo)['PORCEN1']) / 100;
	    $porcenCompra=($newPrecio * readMetodo2($metodo)['PORCEN2']) / 100;

	    $precioVenta=$newPrecio + $porcenVenta;
	    $precioCompra=$newPrecio - $porcenCompra;

		 sqlconector("UPDATE METODOS SET COSTO=".$newPrecio
		.", PRECIO1=".$precioVenta
		.", PRECIO2=".$precioCompra
		." WHERE METODO='".$metodo."'");
	}

	function updateCostoMetodo($metodo,$newPrecio){
		sqlconector("UPDATE METODOS SET COSTO=".$newPrecio." WHERE METODO='".$metodo."'");
	}

  //******************************************************************************************************************
  //Area del Vendedor

  function readVendedor($id){
		return row_sqlconector("select * from USUARIOS where ID=".$id);
  }

  function readVendedor2($correo){
	return row_sqlconector("select * from USUARIOS where CORREO='".$correo."'");
  }

  //********************************************************************************************************************
  //Area del Cajero

  function readCajero($correo){
	return row_sqlconector("select * from USUARIOS where CORREO='".$correo."'");
  }

	function siMetodoExiste($correo,$metodo){
		if(strlen(row_sqlconector("SELECT CORREO,METODO FROM USUARIOSMETODOS WHERE CORREO='".$correo."' AND METODO='".$metodo."'")['METODO'])>0) return TRUE;
		return FALSE;
	}

   function listCajero($metodo,$correo,$monto,$nivel){
   $cadena="";
   $conexion = mysqli_connect(servidor(),user(),password(),database());

   $consulta = "select * from USUARIOS WHERE NIVEL=".$nivel." AND NOT CORREO='".$correo."' AND SALDO>=".$monto;
   $resultado = mysqli_query( $conexion, $consulta );
   $cadena="";
   while($row = mysqli_fetch_array($resultado)){
	   if(siMetodoExiste($row['CORREO'],$metodo)){
      	$cadena= $cadena. "<div style='background:#E1EBF6;display:inline-block; text-align: center; border-style: solid; border-color:#1E90FF;  border-radius: 8px; box-shadow: 0 2px 12px rgb(0, 0, 0); overflow: hidden; width: 220px; height: auto; margin:3px;padding:10px;'>
							".makeImgPerfil($row['PERFIL']).
							"<br>Cajero<br><label style='font-size:1em;font-weight: bolder; '>".$row['NOMBRE']." ".$row['APELLIDO']."</label><br>".makeImgEstrellas($row['RATE']).
							"<br>Email<br><span style='font-size:13px;'>
							".$row['CORREO']."</span><br>
							<button style='margin-top:3px;cursor:pointer; padding:10px;background:#1E90FF; border-radius:5px; border:solid 1px' id='selcajero' onclick='selcajero();'  value='".$row['CORREO']."'>Seleccionar</button>
							</div>";
	   }
   }
   mysqli_close($conexion);
   $cadena=$cadena."";
   return $cadena;
  }

 function listCajeroRetiro($metodo,$correo,$monto,$nivel){
   $cadena="";
   $conexion = mysqli_connect(servidor(),user(),password(),database());

   $consulta = "select * from USUARIOS WHERE NIVEL=".$nivel." AND NOT CORREO='".$correo."' AND SALDO>=".$monto;
   $resultado = mysqli_query( $conexion, $consulta );
   $cadena="";
   while($row = mysqli_fetch_array($resultado)){
	   if(siMetodoExiste($row['CORREO'],$metodo)){
      	$cadena= $cadena. "<div style='background:#E1EBF6;display:inline-block; text-align: center; border-style: solid; border-color:#1E90FF;  border-radius: 8px; box-shadow: 0 2px 12px rgb(0, 0, 0); overflow: hidden; width: 220px; height: auto; margin:3px;padding:10px;'>
							".makeImgPerfil($row['PERFIL']).
							"<br>Cajero<br><label style='font-size:1em;font-weight: bolder; '>".$row['NOMBRE']." ".$row['APELLIDO']."</label><br>".makeImgEstrellas($row['RATE']).
							"<br>Email<br><span style='font-size:13px;'>
							".$row['CORREO']."</span><br>
							<button style='margin-top:3px;cursor:pointer; padding:10px;background:#1E90FF; border-radius:5px; border:solid 1px' id='selcajeroRetiro' onclick='selcajeroRetiro();'  value='".$row['CORREO']."'>Seleccionar</button>
							</div>";
		}
   }
   mysqli_close($conexion);
   $cadena=$cadena."";
   return $cadena;
  }

  function readTicked($id){
	return row_sqlconector("select * from OPERACIONES where TICKET=".$id);
  }

  //*******************************************************************************************************************
  //CHAT

	if (isset($_POST['cerrarchat'])){
		if (strlen($_POST['tickedchat'])>0){
			cerrarChat($_POST['tickedchat']);
		}
	}

	if (isset($_POST['insertchat'])){
		if(readVendedor($_SESSION['user'])['CORREO']==readTicked($_POST['tickedchat'])['CAJERO']){
			$recibe=readTicked($_POST['tickedchat'])['CLIENTE'];
		}
		else{
			$recibe=readTicked($_POST['tickedchat'])['CAJERO'];
		}

		if (Strlen($_POST['mensaje'])>0){
			insertChat(readVendedor($_SESSION['user'])['CORREO'],$_POST['tickedchat'],readVendedor($_SESSION['user'])['CORREO'],$recibe,$_POST['mensaje']);
			if(readTicked($_POST['tickedchat'])['TIPO']=="SOPORTE"){
				insertNotif($recibe,"Ticket ".$_POST['tickedchat']." :un Nuevo Mensaje de: ".readVendedor($_SESSION['user'])['NOMBRE'],"../Book/chat?tk=".$_POST['tickedchat']);
			}
			else if(readTicked($_POST['tickedchat'])['TIPO']=="DEPOSITO"){
				insertNotif($recibe,"Ticket ".$_POST['tickedchat']." :un Nuevo Mensaje de: ".readVendedor($_SESSION['user'])['NOMBRE'],"../Credit/info?tk=".$_POST['tickedchat']);
			}
			else if(readTicked($_POST['tickedchat'])['TIPO']=="RETIRO"){
				insertNotif($recibe,"Ticket ".$_POST['tickedchat']." :un Nuevo Mensaje de: ".readVendedor($_SESSION['user'])['NOMBRE'],"../Credit/info?tk=".$_POST['tickedchat']);
			}
			else{
				insertNotif($recibe,"Ticket ".$_POST['tickedchat']." :un Nuevo Mensaje de: ".readVendedor($_SESSION['user'])['NOMBRE'],"");
			}
		}

		if (readVendedor($_SESSION['user'])['NIVEL']=='2') updateColor($_POST['tickedchat'],readVendedor($_SESSION['user'])['CORREO'],"#BABAEE","#000000");

		dibujaChatApp($_POST['tickedchat']);
	}

  function makeChat($ticked){
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select * from CHAT where TICKED='".$ticked."' order by fecha asc";
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar el Chat");
	  $cadena="<table style='width: 100%; padding: 13px 0;'>";
	  while($row = mysqli_fetch_array($resultado)){
			$cadena=$cadena . "<tr><td style='font-size:11px;'>".$row['FECHA']." - ".$row['ENVIA']."</td></tr>
				<tr><td style='background-color:".$row['BG']."; color=".$row['FG'].";'>".$row['MENSAJE']."</td></tr>";
	  }
	  $cadena = $cadena."</table>";
	  mysqli_close($conexion);
	  return $cadena;
  }

  function ifChatActivo($correo){
	$p=0;
	$row = row_sqlconector("select ACTIVO from CHAT where AMO='".$correo."'");
	if(!empty($row['ACTIVO']))$p=$row['ACTIVO'];
	if($p==0) return FALSE;
	else return TRUE;
  }

  function ifAmoExist($correo){
		try {
			$p="NULL";
		 $row = row_sqlconector("select AMO from CHAT where AMO='".$correo."'");
		 if(!empty($row['AMO']))$p=$row['AMO'];
		 if(strlen($p)>0) return TRUE;
		} catch (Exception $e) {
		   return FALSE;
		}
  }

  function ifChatCerrado($ticked){
		if(isset(row_sqlconector("select CERRADO from CHAT where TICKED='".$ticked."'")['CERRADO'])){
			if(row_sqlconector("select CERRADO from CHAT where TICKED='".$ticked."'")['CERRADO']==1)
				return TRUE;
		}
		return FALSE;
  }

  function chatSinLeer($ticked,$recibe){
	if(row_sqlconector("select LEIDO, COUNT(*) AS TOTAL from CHAT where TICKED='".$ticked."' AND LEIDO=0 AND RECIBE='".$recibe."'")['TOTAL']>0) return $row['TOTAL'];
	else return 0;
  }

  function chatColor($ticked,$recibe){
	 if(row_sqlconector("select COUNT(*) AS TOTAL from CHAT where TICKED='".$ticked."' AND LEIDO=0 AND RECIBE='".$recibe."'")['TOTAL']>0) return "#FF0000";
	 else return "#4D4D4D";
  }

  function cerrarChat($ticked){
	sqlconector("UPDATE CHAT SET CERRADO=1 WHERE TICKED='".$ticked."'");
  }

  function activarChat($correo,$estatus){
	sqlconector("UPDATE CHAT SET ACTIVO=".$estatus." WHERE AMO='".$correo."'");
  }


  function chatLeido($ticked){
	sqlconector("UPDATE CHAT SET LEIDO=1 WHERE TICKED='".$ticked."'");
  }

  function insertChat($amo,$ticked,$envia,$recibe,$mensaje){
	sqlconector("INSERT INTO CHAT(AMO,TICKED,ENVIA,RECIBE,MENSAJE) VALUES('".$amo."','".$ticked."','".$envia."','".$recibe."','".$mensaje."')");
  }

  function createAmo($correo){
	  if(!ifAmoExist($correo)){
	  		sqlconector("INSERT INTO CHAT(AMO) VALUES('".$correo."')");
			return 1;
	  }
	  else{
		  return 0;
	  }
  }

 function updateColor($ticked,$email,$bg,$fg){
	sqlconector("UPDATE CHAT SET BG='".$bg."',FG='".$fg."' WHERE TICKED='".$ticked."' AND ENVIA='".$email."'");
  }

  function dibujaChatApp($ticket) {
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select * from CHAT where TICKED='".$ticket."' order by FECHA asc";
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar el Chat");
	  $activo="";
	  $recibe="";
	  $amo="";
	  while($row = mysqli_fetch_array($resultado)){
	  		$icono="";
			$recibe=$row['RECIBE'];
			$amo=$row['AMO'];
			if($row['ENVIA']==$row['AMO']){
				$activo=$row['ENVIA'];
			}
			else $activo=$row['RECIBE'];

			if(ifChatActivo($activo)){
				$icono= "<span style='font-weight: lighter; font-size:11px;' title='Conectado'>&#9742;</span>";
			}
			else{
				$icono= "<span style='font-weight: lighter; font-size:11px;' title='Desconectado'>&#9743;</span>";
			}

			echo "
			<div style='width: fit-content; padding:10px; border-radius:8px;margin:13px; background-color:".$row['BG']."; color=".$row['FG'].";'>
			<span style='margin-top:5px;font-size:12px;'><b>".latinFecha($row['FECHA'])."</b>  ".readCliente($row['ENVIA'])['NOMBRE']." ".$icono."</span>
			<br><span style='font-weight: bolder;font-size:1em;'>".$row['MENSAJE']."</div>
			";
	  }

		if($activo==$amo){$activo=$recibe;}

      mysqli_close($conexion);
  }

  if(isset($_POST['verChatApp'])) {
  	dibujaChatApp($_POST['verChatApp']);
  }

  /**************************************************************************************************************
	Funciones de la sala pool para juegos online
  */

	function readTorneo($code) {
		return row_sqlconector("select * from TORNEO where CODIGOTORNEO='".$code."'");
	}

	function readJugador($code,$cliente) {
		return row_sqlconector("select * from TORNEO where CODIGOTORNEO='".$code."' AND CLIENTE='".$cliente."'");
	}

  if(isset($_POST["chatTorneo"])) {
	 sqlconector("INSERT INTO CHATORNEO(CODIGOTORNEO,ENVIA,MENSAJE) VALUES('".$_POST['code']."','".$_POST['cliente']."','".$_POST['mensaje']."')");
    dibujaChatTorneo($_POST['code']);
  }

    if(isset($_POST["jugarTorneo"])) {
	    $conexion = mysqli_connect(servidor(),user(),password(),database());

		  $numJugador=0;
	    $consulta = "SELECT COUNT(CLIENTE) AS TOTAL FROM TORNEO WHERE CODIGOTORNEO='".$_POST['code']."'";
		  $resultado = mysqli_query( $conexion, $consulta );
		 if($row = mysqli_fetch_array($resultado)){
			 if(readTorneo($_POST['code'])['MAXSALA'] >= $row["TOTAL"]) {
				$numJugador= $row["TOTAL"]+1;
			   $consulta = "INSERT INTO TORNEO(
			    	CODIGOTORNEO,
			    	CLIENTE,
			    	INICIO,
			    	NCLIENTE)
			    	VALUES('"
			    	.$_POST['code']."','"
			    	.$_POST['cliente']."',"
			    	."1,"
			    	.$numJugador.")";

				$resultado = mysqli_query( $conexion, $consulta );

				$consulta = "UPDATE TORNEO SET ACUMULADO=".(readTorneo($_POST['code'])['ACUMULADO']+$_POST['monto'])." WHERE CODIGOTORNEO='".$_POST['code']."'";
				$resultado = mysqli_query( $conexion, $consulta );

				/*updateSaldo(readVendedor($_SESSION['user'])['SALDO']-$_POST['monto'],readVendedor($_SESSION['user'])['CORREO']);
				updateSaldoCasa(readFortuna()['SALDO'] + $_POST['monto']);*/
				sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo(readVendedor($_SESSION['user'])['CORREO'],"FRT") - $_POST['monto'])." WHERE TOKEN='FRT' AND CORREO='".readVendedor($_SESSION['user'])['CORREO']."'");
				sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo("fortuna@fortunaroyal.com","FRT") + $_POST['monto'])." WHERE TOKEN='FRT' AND CORREO='fortuna@fortunaroyal.com'");

				echo "true";
			 }
			 else {
				echo "false";
			 }
		 }
	    mysqli_close($conexion);
  	}

	if(isset($_POST["sumarTorneo"])){
		sqlconector("UPDATE TORNEO SET CERRADO=1, PUNTAJE=".$_POST['puntaje']." WHERE CODIGOTORNEO='".$_POST['code']."' AND CLIENTE='".$_POST['cliente']."'");
	}

	function dibujaChatTorneo($codigotorneo) {
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select * from CHATORNEO where CODIGOTORNEO='".$codigotorneo."' order by FECHA asc";
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar el Chat");
	  while($row = mysqli_fetch_array($resultado)){
	  		echo "
			<div class='caja-mensaje'>
			<span class='fecha-chat'>".latinFecha($row['FECHA'])."  ".readCliente($row['ENVIA'])['NOMBRE']."</span>
			<br><span class='mensaje'>".$row['MENSAJE']."</div>
			";
	  }
	  echo "<span id='final'></span>";
	  mysqli_close($conexion);
	}

  	if(isset($_POST['verChatTorneo'])) {
  		dibujaChatTorneo($_POST['verChatTorneo']);
  	}

	function lugaresGanadores($code) {
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select NCLIENTE,PUNTAJE from TORNEO where CODIGOTORNEO='".$code."' order by PUNTAJE DESC";
	  $resultado = mysqli_query( $conexion, $consulta );
	  $ganadores= array("0","0","0",);
	  $i=0;
	  while($row = mysqli_fetch_array($resultado)){
			$ganadores[$i]=$row['NCLIENTE'];
			$i++;
	  }
	  mysqli_close($conexion);
	  return $ganadores;
	}

	function readGanador($code,$ncliente) {
		return row_sqlconector("select * from TORNEO where CODIGOTORNEO='".$code."' AND NCLIENTE='".$ncliente."'");
	}

	function cerrarSala($code) {
		sqlconector("UPDATE TORNEO SET ELIMINAR=1 WHERE CODIGOTORNEO='".$code."'");
	}

	function revisaGanador($ncliente,$code) {
		$jugadores=readTorneo($code)['MAXSALA']-countJugadores($code);
		$puntajeGanador=readGanador($code,$ncliente)['PUNTAJE'];
		if($jugadores == 0 && $puntajeGanador>0 && readTorneo($code)['ELIMINAR']==0) {
			$comision = readTorneo($code)['ACUMULADO'] * 3 / 100;
			$totalPagar =readTorneo($code)['ACUMULADO'] - $comision;
			echo "Ganador el Jugador N.<b>".$ncliente. "</b> Ha Ganado un Acumulado de:<b> ".readTorneo($code)['ACUMULADO']."FRT</b>" ;
			//insertNotif(readGanador($code,$ncliente)['CLIENTE'],"Felicidades eres el Ganador del Torneo por: ".$totalPagar."FRT menos Comisiones");

			sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo(readGanador($code,$ncliente)['CLIENTE'],"FRT")  + $totalPagar)." WHERE TOKEN='FRT' AND CORREO='".readGanador($code,$ncliente)['CLIENTE']."'");

 		  if(loQueTengo("fortuna@fortunaroyal.com","FRT")>=$totalPagar) {
  			sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo("fortuna@fortunaroyal.com","FRT") - $totalPagar)." WHERE TOKEN='FRT' AND CORREO='fortuna@fortunaroyal.com'");
  	 	 }
  		 else {
  			sqlconector("UPDATE WALLETOKEN SET CANTIDAD=0 WHERE TOKEN='FRT' AND CORREO='fortuna@fortunaroyal.com'");
  		}

			//updateSaldo(readVendedor2(readGanador($code,$ncliente)['CLIENTE'])['SALDO'] + $totalPagar,readGanador($code,$ncliente)['CLIENTE']);

			cerrarSala($code);

			$conexion = mysqli_connect(servidor(),user(),password(),database());

			$referencia=generaTicket();
			$consulta = "INSERT INTO OPERACIONES(PAGADO,TICKET,CAJERO,CLIENTE,TIPO,REFERENCIA,MONTO,RECIBE,ESTATUS,MONEDA)
						VALUES(1,".$referencia.",'Fortuna Royal','"
						.readGanador($code,$ncliente)['CLIENTE']."','TORNEO','T"
						.$referencia."',"
						.readTorneo($code)['ACUMULADO'].","
						.readTorneo($code)['ACUMULADO'].",'COMPLETADO','FRT')";

			$resultado = mysqli_query( $conexion, $consulta );

			$rate=1;
	   	if($totalPagar>10 && $totalPagar<50 ){$rate=2;}
	   	else if($totalPagar>50 && $totalPagar<100){$rate=3;}
	   	else if($totalPagar>100 && $totalPagar<1000){$rate=4;}
			$consulta = "INSERT INTO GANADORES(CORREO, NOMBRE,JUEGO,RATE,MONTO) VALUES('".readGanador($code,$ncliente)['CLIENTE']."','".readVendedor(readGanador($code,$ncliente)['CLIENTE'])['NOMBRE']."','Torneos',".$rate.",".$totalPagar.")";
			$resultado = mysqli_query( $conexion, $consulta );

			mysqli_close($conexion);
		}
		else {
			echo "";
		}
	}

	function dibujaInfoTorneo($codigotorneo) {
		echo "<br><label>Total de Jugadores:</label> ". readTorneo($codigotorneo)['MAXSALA']."";
		echo "<label>Jugadores Activos:</label> ". countJugadores($codigotorneo)."";
		echo "<br><label>Faltan por Jugar:</label> ".strval(readTorneo($codigotorneo)['MAXSALA']-countJugadores($codigotorneo))."";
		echo "<label>Acumulado:</label> ".readTorneo($codigotorneo)['ACUMULADO']."FRD";
		echo "<br><label>Eres el Jugador: </label> ".readJugador($codigotorneo,readVendedor($_SESSION['user'])['CORREO'])['NCLIENTE']. "";
		echo "<label>Puntuación: </label> ".readJugador($codigotorneo,readVendedor($_SESSION['user'])['CORREO'])['PUNTAJE']."";
		echo "<div style=\"margin-top:8px; width: 90%; text-align: center; padding: 3px;\">";
		echo "<div class='winnerbox winnerbox-1'><b>1er Lugar</b> Jugador N.<span style='font-size:21px;color:blue;'> ".lugaresGanadores($codigotorneo)[0]. "</span> <b>(". readGanador($codigotorneo,lugaresGanadores($codigotorneo)[0])['PUNTAJE']." Pts)</b></div>";
		echo "<div class='winnerbox winnerbox-2'><b>2do Lugar</b> Jugador N.<span style='font-size:21px;color:red;'> ".lugaresGanadores($codigotorneo)[1]. "</span> <b>(". readGanador($codigotorneo,lugaresGanadores($codigotorneo)[1])['PUNTAJE']." Pts)</b></div>";
		echo "<div class='winnerbox winnerbox-3'><b>3er Lugar</b> Jugador N.<span style='font-size:21px;color:red;'> ".lugaresGanadores($codigotorneo)[2]. "</span> <b>(". readGanador($codigotorneo,lugaresGanadores($codigotorneo)[2])['PUNTAJE']." Pts)</b></div>";
		echo "</div>";
		revisaGanador(lugaresGanadores($codigotorneo)[0],$codigotorneo);
	}

  	if(isset($_POST['verInfoTorneo'])) {
  		dibujaInfoTorneo($_POST['verInfoTorneo']);
  	}

	function ifJuegoInicia($code,$cliente) {
		$status=FALSE;
		if(row_sqlconector("select * from TORNEO where CODIGOTORNEO='".$code."' AND CLIENTE='".$cliente."'")['INICIO']==1) $status= TRUE;
		else $status= FALSE;
		return $status;
	}

	function miNumero($code,$cliente) {
		$miNumero=0;
		$miNumero= row_sqlconector("select * from TORNEO where CODIGOTORNEO='".$code."' AND CLIENTE='".$cliente."'")['NCLIENTE'];
		return $miNumero;
	}

	function countJugadores($codigoTorneo) {
		$numero=0;
		$numero= row_sqlconector("SELECT COUNT(CLIENTE) AS TOTAL FROM TORNEO WHERE CODIGOTORNEO='".$codigoTorneo."'")["TOTAL"];
   	return $numero;
	}

	function dibujaPan1($codigotorneo){
	  $classTipo="";
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select * from TORNEO where INICIO=1 AND CODIGOTORNEO='".$codigotorneo."' order by NCLIENTE asc";
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar el Chat");
	  echo "<h1>Jugadores</h1><br>";
	  while($row = mysqli_fetch_array($resultado)){
	  		if($row['CERRADO']==1) {
				 $classTipo="mute is-muted";
	  		}
	  		else {
	  			 $classTipo="";
	  		}
	  		echo "
				<div style='display:inline-block;' class='".$classTipo."'><img style='width: 48px;height: 48px; margin:8px;' src='picture/torneos/champion.png'/>
				<span style='text-align:center; font-size:10px;'></br>Jugador: ".$row['NCLIENTE']."</br>Puntaje: ".$row['PUNTAJE']."</span>
				</div>
			";
	  }
	  mysqli_close($conexion);
	}

  	if(isset($_POST['verpan1'])) {
  		dibujapan1($_POST['verpan1']);
  	}

  	function dibujaSala() {
		$conexion = mysqli_connect(servidor(),user(),password(),database());
		$consulta = "select * from TORNEO where ELIMINAR=0 AND LENGTH(ADMINISTRADOR) > 0";
	   $resultado = mysqli_query( $conexion, $consulta );
	   $color="#E1EBF6";
	   $privado="Privado";

		echo "<div class='form-sala-new'>
				<span style='text-transform: uppercase;background:none;font-size:18px; font-weight: bolder; color:#fff;'>Nueva Sala</span><br>
				<img style=\"width: 144px;height: 144px;\" src=\"picture/torneo_logo.png\" >
				<br><span style='line-height : 18px;background:none;font-size:18px; font-weight: bolder; color:#fff;'>Agregar Nueva Sala
				</br>De Juego Es Gratis</span></br></span><br>
				<button class='btn-sala-salir1' type=\"button\" id=\"salir\" onclick=\"window.location.href='index'\">Salir</button>
				<br>
				<button class='btn-sala-new' type=\"button\" id=\"addserver\" onclick=\"$('#newServer').fadeIn();\">Agregar Torneo</button>
		</div>";

	   while($row = mysqli_fetch_array($resultado)){
	   	if($row["PRIVADO"]==0){
	   		$color="#E1EBF6";
	   		$privado="Free";
	   	}
	   	else{
	   		$color="#FFC0CB";
	   		$privado="Privado";
	   	}
	   	echo "<form method='POST' action='salatorneo' class='form-sala' style='color:#fff;'>
							<br><span style='text-transform: uppercase;background:none;font-size:21px; font-weight: bolder; color:#fff;'>".$row['SALA']."</span><br>
							<img style=\"width: 144px;height: 144px;\" src=\"picture/torneos/".$row['JUEGO'].".png\" >
							<br><span style='line-height : 18px;background:none;font-size:17px; color:#fff;'>".$row["JUEGO"]."(".$privado.")</br>Monto: ".number_format($row['MONTOTORNEO'],0,",",".")."FRT</br>Total Acumulado: ".number_format($row['ACUMULADO'],0,",",".")."FRT</br>
							Maximo Jugadores: ".$row['MAXSALA']."<br>Jugando: ".countJugadores($row['CODIGOTORNEO'])."<br></span>
							<button type='submit' class='btn-sala' value='".$row['CODIGOTORNEO']."' name='inicio'>Jugar</button>
							</form>";
	   }
		mysqli_close($conexion);
  	}

  	if(isset($_POST['verSala'])) {
  		dibujaSala();
  	}

	//******************************************************************************************************************
	//token
	function barra_compra($num){
		if($num==NULL)$num=0;
		return "<span style='color:white; background:green;font-weight:bolder;'>".number_format($num,2,",",".")."</span>";
	}

	function barra_venta($num){
		if($num==NULL)$num=0;
		return "<span style='color:white; background:red;font-weight:bolder;'>".number_format($num,2,",",".")."</span>";
	}

	function libro_ordenes($token){
		$tmp_precio_venta=0;
		$conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select * from ORDENTOKEN where TOKEN='".$token."' AND EJECUTADO=0 ORDER BY PRECIO DESC";
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar el Libro de Ordenes");
	  echo "<table class=\"table1\"><tr>
	  <th><span style='color:white;background:none;'>Vol. Compra</span></th>
	  <th><span style='color:white;background:none;'>Vol. Venta</span></th>
		<th><span style='color:white;background:none;'>Precio</span></th>
	  </tr>";
	  while($row = mysqli_fetch_array($resultado)){
				if($tmp_precio_venta!=$row['PRECIO']){
					echo "<tr>
					<td style='text-align:right;'>".barra_compra(row_sqlconector("SELECT SUM(CANTIDAD) AS TOTAL FROM ORDENTOKEN WHERE PRECIO_COMPRA=".$row['PRECIO']." AND EJECUTADO=0 " )['TOTAL']).
					"</td><td style='text-align:left;'>".barra_venta(row_sqlconector("SELECT SUM(CANTIDAD) AS TOTAL FROM ORDENTOKEN WHERE PRECIO_VENTA=".$row['PRECIO']." AND EJECUTADO=0 ")['TOTAL'])."</td>
					<td style='text-align:center; color:white;'><span style='cursor:pointer;background:none;' onclick='carga_precio(".$row['PRECIO'].")'>".$row['PRECIO']."</span></td>
					</tr>";
				}else continue;
				$tmp_precio_venta=$row['PRECIO'];
		}
		echo "</table>";
	}

	function air_drop_frt($correo){
		if (!if_wallet_exist($correo,"FRT")){
			create_wallet($correo,"FRT");
			update_saldo_token_positivo("10",$correo,"FRT");
			update_saldo_token_negativo("10","fortuna@fortunaroyal.com","FRT");
		}
	}

	function if_wallet_exist($correo,$token) {
		$status=FALSE;
		if(strlen(row_sqlconector("select TOKEN from WALLETOKEN where CORREO='".$correo."' AND TOKEN='".$token."'")['TOKEN'])>0) $status= TRUE;
		else $status= FALSE;
		return $status;
	}

	function create_wallet($correo,$token) {
		sqlconector("INSERT INTO WALLETOKEN(CORREO,TOKEN) VALUES('".$correo."','".$token."')");
	}

	function recalcTokens() {
		   $conexion = mysqli_connect(servidor(),user(),password(),database());

		   $consulta = "select * from TOKEN ";
		   $valor_token=0;
		   $resultado = mysqli_query( $conexion, $consulta );
   		while($row = mysqli_fetch_array($resultado)){
				$valor_token=$row['PATRIMONIO'] / $row['VOLUMEN'];
				updateValorToken($row['TOKEN'],$valor_token);
	  		}
	  		mysqli_close($conexion);
	}

	function updateValorToken($token,$valor) {
		sqlconector("UPDATE TOKEN SET VALOR=".$valor. " WHERE TOKEN='".$token."'");
	}

	function updateTotalWalleToken($correo,$token,$valor) {
		sqlconector("UPDATE WALLETOKEN SET TOTAL=".$valor. " WHERE CORREO='".$correo."' AND TOKEN='".$token."'");
	}

	function readToken($token) {
		return row_sqlconector("select * from TOKEN where TOKEN='".$token."'");
	}

	function readTokenID($id) {
		return row_sqlconector("select * from TOKEN where ID=".$id);
	}

	function loQueTengo($correo,$token) {
		return row_sqlconector("select * from WALLETOKEN where TOKEN='".$token."' AND CORREO='".$correo."'")['CANTIDAD'];
	}

	function porcen($elcien, $x) {
			return number_format(($x * 100 / $elcien),0,",",".")."%";
	}

	if(isset($_POST['comprar'])) {
		 $orden=generaTicket();
		 sqlconector("INSERT INTO ORDENTOKEN(ORDEN,TOKEN,OPERACION,USUARIO,COMPRADOR,PRECIO,PRECIO_COMPRA,CANTIDAD,RESTANTE,TOTAL)
	     VALUES('".$orden."','".
				 $_POST['token']."','COMPRA','".
				 $_POST['comprador']."','".
				 $_POST['comprador']."',".
				 $_POST['precio_compra'].",".
				 $_POST['precio_compra'].",".
				 $_POST['cantidad'].",".
				 $_POST['cantidad'].",".
				 $_POST['ttotal'].")");
	}

	if(isset($_POST['vender'])) {
		 $orden=generaTicket();
		 sqlconector("INSERT INTO ORDENTOKEN(ORDEN,TOKEN,OPERACION,USUARIO,VENDEDOR,PRECIO,PRECIO_VENTA,CANTIDAD,RESTANTE,TOTAL)
	     VALUES('".$orden."','".
				 $_POST['token']."','VENTA','".
				 $_POST['vendedor']."','".
				 $_POST['vendedor']."',".
				 $_POST['precio_venta'].",".
				 $_POST['precio_venta'].",".
				 $_POST['cantidad'].",".
				 $_POST['cantidad'].",".
				 $_POST['ttotal'].")");
	}

	if(isset($_GET['librordenes'])) {
		libro_ordenes($_GET['token']);
	}

	if(isset($_GET['ordenes'])) {
	  $precio=0;
		$color="black";
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select * from ORDENTOKEN where USUARIO='".$_GET['email']."' AND TOKEN='".$_GET['token']."' AND EJECUTADO=0 order by FECHA asc";
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar las Ordenes");
	  echo "<table class='table2' ><thead>
	  <th>Orden</th>
	  <th>Fecha</th>
	  <th>FRD|USDT</th>
	  <th>Token</th>
	  <th>Precio</th>
	  <th>Completado</th>
	  <th></th>
	  </thead><tbody>";
	  while($row = mysqli_fetch_array($resultado)){
	  		if($row['OPERACION']=="COMPRA") {
				$precio=$row['PRECIO_COMPRA'];
				$color="green";
	  		}
	  		else {
				$precio=$row['PRECIO_VENTA'];
				$color="red";
	  		}
	  		echo "
	  		<tr>
	  		<td><span style='font-weight:bold;background:none; color:".$color.";'>".$row['ORDEN']."</span></td>
	  		<td>".latinFecha($row['FECHA'])."</td>
	  		<td>".number_format($row['TOTAL'],2,",",".")."FRD</td>
	  		<td>".number_format($row['CANTIDAD'],2,",",".").$row['TOKEN']."</td>
	  		<td>".$precio."</td>
	  		<td>".porcen($row['CANTIDAD'],$row['PROCESADO'])."</td>
	  		<td><button type='button' style='background:".$color.";color:white;font-weight:bold; border-radius: 3px; border:none; padding:5px;' onclick='cancelar(".$row['ORDEN'].");'>Cancelar</button></td>
			</tr>
			";
	  }
	  echo "</tbody></table>";
	  mysqli_close($conexion);
	}

	function read_orden_token($orden) {
		return row_sqlconector("select * from ORDENTOKEN where ORDEN='".$orden."'");
  }

	function read_wallet_token($correo,$token) {
		return row_sqlconector("select * from WALLETOKEN where CORREO='".$correo."' AND TOKEN='".$token."'");
	}

   function update_saldo_f_positivo($valor,$correo){
   	$saldo_anterior=readCliente($correo)['SALDO'];
   	sqlconector("UPDATE USUARIOS SET SALDO=".strval($saldo_anterior + $valor)." WHERE CORREO='".$correo."'");
   }

   function update_saldo_token_positivo($valor,$correo,$token){
		  $saldo_anterior=read_wallet_token($correo,$token)['CANTIDAD'];
    	$saldo_anterior_saldo=read_wallet_token($correo,$token)['SALDO'];
     	sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval($saldo_anterior + $valor).", SALDO=".strval($saldo_anterior_saldo + $valor)." WHERE CORREO='".$correo."' AND TOKEN='".$token."'");
   }

	 function update_saldo_token_negativo($valor,$correo,$token){
		  $saldo_anterior=read_wallet_token($correo,$token)['CANTIDAD'];
    	$saldo_anterior_saldo=read_wallet_token($correo,$token)['SALDO'];
     	sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval($saldo_anterior - $valor).", SALDO=".strval($saldo_anterior_saldo - $valor)." WHERE CORREO='".$correo."' AND TOKEN='".$token."'");
   }

  if (isset($_POST['restartoken'])){
		sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo($_POST['vendedor'],$_POST['token']) - $_POST['monto'])." WHERE TOKEN='".$_POST['token']."' AND CORREO='".$_POST['vendedor']."'");
  }

  if(isset($_GET['saldotoken'])) {
  		echo loQueTengo($_GET['correo'],$_GET['token']);
  }

  if(isset($_GET['valortoken'])) {
  		echo readToken($_GET['valortoken'])['VALOR'];
  }

  if(isset($_POST['cancelarorden'])) {
  		if(read_orden_token($_POST['numero'])['OPERACION']=="COMPRA") {
  			//espero me devuelvan mis FRD
  			$loquedevuelvo=read_orden_token($_POST['numero'])['RESTANTE'] * read_orden_token($_POST['numero'])['PRECIO_COMPRA'];
  			update_saldo_f_positivo($loquedevuelvo,read_orden_token($_POST['numero'])['USUARIO']);
	  	}
  		else {
  			//espero me devuelvan mis Token
  			$loquedevuelvo=read_orden_token($_POST['numero'])['RESTANTE'];
  			update_saldo_token_positivo($loquedevuelvo,read_orden_token($_POST['numero'])['USUARIO'],read_orden_token($_POST['numero'])['TOKEN']);
  		}
  		sqlconector("UPDATE ORDENTOKEN SET CANCELADO=1, EJECUTADO=1 WHERE ORDEN='".$_POST['numero']."'");
  		//recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
  	}

	  function recalc_mis_wallet($correo){
		$conexion = mysqli_connect(servidor(),user(),password(),database());

		$consulta = "select * from WALLETOKEN WHERE CORREO='".$correo."'";
		$resultado = mysqli_query( $conexion, $consulta );
		$valor_F=0;
		$multiplo=1;
		while($row = mysqli_fetch_array($resultado)){
			if(isset(readToken($row['TOKEN'])['VALOR'])) $multiplo = readToken($row['TOKEN'])['VALOR'];
			else $multiplo=1;
			 $valor_F=$row['CANTIDAD'] * $multiplo;
			 updateTotalWalleToken($correo,$row['TOKEN'],$valor_F);
		   }
		   mysqli_close($conexion);
	  }

	  function miformat($number){
		  return number_format($number,2,",",".");
	  }

	  if(isset($_GET['miswallet'])){
	  	  recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
		  $total=0;
		echo "<table style=''>
			<thead style='background:lightgray'>
			<th>Wallet</th>
			<th>Cantidad</th>
			<th>Balance en FRD</th>
			<th></th>
			</thead>
			<tbody>
			<tr><td></td><td></td><td></td><td></td></tr>
			<tr>
			<td>Fortuna Royal </td><td>".readVendedor($_SESSION['user'])['SALDO']."<b> FRD</b></td><td style='text-align: right;'>".miformat(readVendedor($_SESSION['user'])['SALDO'])."</td></tr>";
		$conexion = mysqli_connect(servidor(),user(),password(),database());

		$consulta = "select * from WALLETOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."'";
		$resultado = mysqli_query( $conexion, $consulta );
		$total=readVendedor($_SESSION['user'])['SALDO'];
		$nombre="";
		while($row = mysqli_fetch_array($resultado)){
			if(isset(readToken($row['TOKEN'])['NOMBRE'])) $nombre=readToken($row['TOKEN'])['NOMBRE'];
			else $nombre="";
			$total=$total + $row['TOTAL'];
			echo "<tr><td>".$nombre."</td><td>".$row['CANTIDAD']."<b> ".$row['TOKEN']."</b></td><td style='text-align: right;'>".miformat($row['TOTAL'])."</td></tr>";
		   }
		   mysqli_close($conexion);

		echo "<tr><td></td><td></td><td></td><td></td></tr>
		<tr>
			<td></td><td>Total <span style='color:gray; font-size:13px; '>FRD</span><span style='color:black; font-size:13px; '>|</span><span style='color:darkgreen; font-size:13px;'>USDT</span></td><td style='color:green;background:lightgreen;text-align: right;'><b>".miformat($total)."</b></td><td></td>
			</tr>
			</tbody></table>";
	  }

		function setPatrimonio(){
			$ingresos = row_sqlconector("SELECT SUM(MONTO) AS INGRESO FROM LIBROCONTABLE WHERE OPERACION='COMI' AND MONEDA='FRD'")['INGRESO'];
			$egresos =  row_sqlconector("SELECT SUM(MONTO) AS EGRESO FROM LIBROCONTABLE WHERE OPERACION='RETI' AND MONEDA='FRD'")['EGRESO'];
			sqlconector("UPDATE FORTUNA SET SALDO=".strval($ingresos - $egresos));
		}

	  function if_exist_ref($referencia) {
		$conexion = mysqli_connect(servidor(),user(),password(),database());

		$consulta = "SELECT REFERENCIA FROM OPERACIONES WHERE REFERENCIA='".$referencia."'";
		$resultado = mysqli_query( $conexion, $consulta );
		$num=0;
		while($row = mysqli_fetch_array($resultado)){
			$num++;
		}
	  	if($num > 1) return true;
			return false;
	  }

	  function ver_ref($referencia) {
	  		$row = row_sqlconector("SELECT TICKET,REFERENCIA FROM OPERACIONES WHERE REFERENCIA='".$referencia."'");
			return " <a style='text-decoration:underline;' onclick=\"$('#frame').load('admin?verhistorial&open&tipo&ver&tk=".$row['TICKET']."&tickedchat=".$row['TICKET']."');\">(".$row['TICKET'].")</a>";
	  }

?>
