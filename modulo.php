<?php
	/*
	  Modulo Revision 0.0.0.5.3
	  funciones resumidas del sistema de apuestas
	  FortunaRoyal
	  (c) 2020 by Triangulo Rojo
	*/
	require "init.php";
	session_start();
/***************************************************************************************************************
 * Funciones del Bloqueo para cerrar la casa Juego
 * */

function return_url(){
		return "http://fortunaroyal.com";
}

function limpiar($cadena) {
    $vowels = array("'", "\"");
    return str_replace($vowels, "", $cadena);
}

function sqlconector($consulta) {
	 $conexion = @mysqli_connect(servidor(),user(),password(),database());
	 if (!$conexion) {
		 echo "Refresh page, Failed to connect to Data...";
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
	 echo "Refresh page, Failed to connect to Data...";
	 exit();
 }else{
	 if($resultado = mysqli_query( $conexion, $consulta )){
		 $row = mysqli_fetch_array($resultado);
	 }
	 mysqli_close($conexion);
 }
 return $row;
}

function readPromo($ubicacion){
	if(ifReadPromo($ubicacion)){
		$row = row_sqlconector("select * from PROMO where UBICACION='{$ubicacion}' AND FECHA BETWEEN '".date("Y/m/d")." 00:00' AND '".date("Y/m/d")." 23:59'");
		echo 	"<div  style='margin-top:20px; z-index:2000;position: absolute; width:90%;' id='relative'>
			<div style='border-radius:5px; margin:3px; background: ".$row['COLORBG'].";border:1px solid ".$row['BORDER'].";' >
			<span style='color:".$row['COLORFG'].";'>Aviso!</span>
			<a href='javascript:void(0);'  style='float:right;background:white; color:black; text-decoration:none;padding:3px;' onclick=\"$('#relative').fadeOut()\">✖️</a><br>
			<div style='text-align:center; padding:13px; color:".$row['COLORFG'].";width:100%;'>".$row['MENSAJE']."</div>
			</div>
			</div>
			";
	}
}

function ifReadPromo($ubicacion){
	if($row = row_sqlconector("select * from PROMO where UBICACION='{$ubicacion}' AND FECHA BETWEEN '".date("Y/m/d")." 00:00' AND '".date("Y/m/d")." 23:59'")){
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

  function makeImgEstrellas($rate){
	  $cadena="";
	  for ($i=1; $i<=$rate; $i++){
		  $cadena= $cadena. "<img style='width: 1.7rem; vertical-align:middle;border-radius:50%; background: none;' src='../Games/star_active.png'>";
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

  //*******************************************************************************************************************

function readUserDataMetodo($correo,$metodo){
	return row_sqlconector("select * from USUARIOSMETODOS WHERE CORREO='".$correo."' AND METODO='".$metodo."'");
}

function readUserDataMetodoId($id){
	return row_sqlconector("select * from USUARIOSMETODOS WHERE ID=".$id);
}

if(isset($_POST['editDataMetodo'])){
 $row=readUserDataMetodo($_POST['correo'],$_POST['editDataMetodo']);
 echo $row['PRECIOVENTA'].
	",".$row['PRECIOCOMPRA'].
	",".$row['SALDOFIAT'].
	",".$row['SALDOFRD'].
	",".$row['ACTIVO'].
	",".$row['METODO'];
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
	  echo number_format($sal,2,",",".")." <span style='color:white; font-size:13px; '>FRD</span><span style='color:white; font-size:13px; '>|</span><span style='color:#09ff3e; font-size:13px;'>USDT</span>";
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
		updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO'] + $_POST['monto'], "fortuna@fortunaroyal.com");
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
			$cadena= $cadena ."
			<li>
				<a onclick=\"marcar('".$email."');\" style='margin:0;font-weight: bolder;font-size:14px; color: black; background:white; }'>□ Marcar Como Leidas</a>
			</li>
			<script>document.getElementById('campana').style.animation='glow 1s ease-in-out infinite alternate'; </script>";
	   }
	   else{
			//$cadena = $cadena . "<li style='text-align:center;'></li> <script> document.getElementById('campana').style.animation='none 1s ease-in-out infinite alternate'; </script>";
	   }
	   while($row = mysqli_fetch_array($resultado)){
			$cadena= $cadena. "<li style='cursor:default;border:none;font-size:13px;' onclick=\"window.location.href='{$row['UBICACION']}'\">□ ".$row['NOTICIA']."</li>";
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
			insertNotif($recibe,"Ticket ".$_POST['tickedchat']." :un Nuevo Mensaje de: ".readVendedor($_SESSION['user'])['NOMBRE'],"");
		}

		if (readVendedor($_SESSION['user'])['NIVEL'] > 0) updateColor($_POST['tickedchat'],readVendedor($_SESSION['user'])['CORREO'],"#BABAEE","#000000");

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
	if(row_sqlconector("select * from CHAT where AMO='".$correo."'")['AMO']=="$correo") return TRUE;
	return FALSE;
  }

  function ifChatCerrado($ticked){
	if(row_sqlconector("select * from CHAT where TICKED='".$ticked."'")['CERRADO']==1) return TRUE;
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

  function createAmo($correo,$tk){
	  if(ifAmoExist($correo)==FALSE){
	  		sqlconector("INSERT INTO CHAT(AMO,TICKED) VALUES('".$correo."','".$tk."')");
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

	//******************************************************************************************************************
	//token
	function barra_compra($num){
		if($num==NULL)$num=0;
		return "<span style='color:white; background:#0ECB81;font-weight:bolder;'>".redo($num)."</span>";
	}

	function barra_venta($num){
		if($num==NULL)$num=0;
		return "<span style='color:white; background:#F6465D;font-weight:bolder;'>".redo($num)."</span>";
	}

	function libro_ordenes($token){
		$tmp_precio_venta=0;
		$conexion = mysqli_connect(servidor(),user(),password(),database());

	  $consulta = "select * from ORDENTOKEN where TOKEN='".$token."' AND EJECUTADO=1 ORDER BY PRECIO DESC";
	  $resultado = mysqli_query( $conexion, $consulta );
		$grafica= array(array());
		$grafica[0][0]="Compras";
	  $grafica[1][0]="Ventas";
	  $i=1;
	  while($row = mysqli_fetch_array($resultado)){
				if($tmp_precio_venta!=$row['PRECIO']){
					$grafica[0][$i] = row_sqlconector("SELECT SUM(CANTIDAD) AS TOTAL FROM ORDENTOKEN WHERE PRECIO_COMPRA=".$row['PRECIO']." AND EJECUTADO=1 " )['TOTAL'];
			    $grafica[1][$i] = row_sqlconector("SELECT SUM(CANTIDAD) AS TOTAL FROM ORDENTOKEN WHERE PRECIO_VENTA=".$row['PRECIO']." AND EJECUTADO=1 ")['TOTAL'];
			    $i++;
				}else continue;
				$tmp_precio_venta=$row['PRECIO'];
		}
		mysqli_close($conexion);
		echo json_encode($grafica);
	}

	function air_drop_frt($correo){
		if (!if_wallet_exist($correo,"FRT")){
			create_wallet($correo,"FRT");
			update_saldo_token_positivo("10",$correo,"FRT");
			update_saldo_token_negativo("10","fortuna@fortunaroyal.com","FRT");
		}
	}

	function if_wallet_exist($correo,$token) {
			if(isset(row_sqlconector("select TOKEN from WALLETOKEN where CORREO='".$correo."' AND TOKEN='".$token."'")['TOKEN'])){
				if(strlen(row_sqlconector("select TOKEN from WALLETOKEN where CORREO='".$correo."' AND TOKEN='".$token."'")['TOKEN'])>0)
					return TRUE;
			}
		return FALSE;
	}

	function create_wallet($correo,$token) {
		sqlconector("INSERT INTO WALLETOKEN(CORREO,TOKEN) VALUES('".$correo."','".$token."')");
	}

	function recalcTokens() {
		   $conexion = mysqli_connect(servidor(),user(),password(),database());
			 if (!$conexion) {
		     echo "Refresh page, Failed to connect to Data...";
		     exit();
		   }else{
				 $consulta = "select * from TOKEN ";
			   $valor_token=0;
			   $resultado = mysqli_query( $conexion, $consulta );
	   		while($row = mysqli_fetch_array($resultado)){
					$volumen = $row['VOLUMEN'];
					if($volumen==0)$volumen=1;
					$valor_token = $row['PATRIMONIO'] / $volumen;
					if($valor_token == 0)$valor_token = 0.0001;
					updateValorToken($row['TOKEN'],$valor_token);
		  		}
		  		mysqli_close($conexion);
			 }
	}

	function updateValorToken($token,$valor) {
		sqlconector("UPDATE TOKEN SET VALOR=".$valor. " WHERE TOKEN='".$token."'");
		sqlconector("UPDATE WALLETOKEN SET VALOR=".$valor. " WHERE TOKEN='".$token."'");
	}

	function updateTotalWalleToken($correo,$token,$valor) {
		sqlconector("UPDATE WALLETOKEN SET TOTAL=".$valor. " WHERE CORREO='".$correo."' AND TOKEN='".$token."'");
	}

	function readToken($token) {
		return row_sqlconector("select * from TOKEN where TOKEN='".$token."'");
	}

	function loQueTengo($correo,$token) {
		return row_sqlconector("select * from WALLETOKEN where TOKEN='".$token."' AND CORREO='".$correo."'")['CANTIDAD'];
	}

	function porcen($elcien, $x) {
			return number_format(($x * 100 / $elcien),0,",",".")."%";
	}

	function tasa($tasa) {
			return ($tasa / 100);
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

				 updateSaldo(readCliente($_POST['comprador'])['SALDO'] - $_POST['ttotal'],$_POST['comprador']);
		 		 updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO'] + $_POST['monto'], "fortuna@fortunaroyal.com");

				 $compra = read_orden_token($orden);
				 $estoy_comprando = $compra['RESTANTE'];
				 $compra_total=	$estoy_comprando;
				 $comision = $estoy_comprando * tasa(readFortuna()['COMISIONEMISION']);
				 update_saldo_token_positivo($compra_total - $comision,$compra['COMPRADOR'],$compra['TOKEN']);
				 update_saldo_token_positivo($comision,"fortuna@fortunaroyal.com",$compra['TOKEN']);
				 sqlconector("INSERT INTO LIBROCONTABLE(TIPO,MONEDA,MONTO) VALUES('COMPRA','".$compra['TOKEN']."',".$comision.")");
				 marcar_orden_ejecutada($orden);
				 sqlconector("UPDATE TOKEN SET PATRIMONIO=".strval(readToken($_POST['token'])['PATRIMONIO']+$_POST['ttotal'])." WHERE TOKEN='".$_POST['token']."'");
				 sqlconector("UPDATE TOKEN SET VOLUMEN=".strval(readToken($_POST['token'])['VOLUMEN']+$_POST['cantidad'])." WHERE TOKEN='".$_POST['token']."'");
	}

	function marcar_orden_ejecutada($orden) {
		   $conexion = mysqli_connect(servidor(),user(),password(),database());

		   $consulta = "UPDATE ORDENTOKEN SET EJECUTADO=1 WHERE ORDEN='".$orden."'";
		   $resultado = mysqli_query( $conexion, $consulta );
	  		mysqli_close($conexion);
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

				 sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo($_POST['vendedor'],$_POST['token']) - $_POST['cantidad'])." WHERE TOKEN='".$_POST['token']."' AND CORREO='".$_POST['vendedor']."'");

				 $venta = read_orden_token($orden);
				 $estoy_vendiendo = $venta['RESTANTE'];
				 $total_vendido= $estoy_vendiendo;
				 $venta_total = $venta['TOTAL']; //$total_vendido * $venta['PRECIO_VENTA'];
				 $comision= $venta_total * tasa(readFortuna()['COMISIONRETORNO']);

				 sqlconector("UPDATE TOKEN SET PATRIMONIO=".strval(readToken($venta['TOKEN'])['PATRIMONIO'] - $venta_total)." WHERE TOKEN='".$venta['TOKEN']."'");
				 sqlconector("UPDATE TOKEN SET VOLUMEN=".strval(readToken($venta['TOKEN'])['VOLUMEN'] - $estoy_vendiendo)." WHERE TOKEN='".$venta['TOKEN']."'");
				 update_saldo_f_positivo(($venta_total - $comision),$venta['VENDEDOR'],$total_vendido,$venta['TOKEN']);
				 updateSaldo(readVendedor2("fortuna@fortunaroyal.com")['SALDO'] + $comision, "fortuna@fortunaroyal.com");
				 sqlconector("INSERT INTO LIBROCONTABLE(TIPO,MONTO) VALUES('VENTA',".$comision.")");
				 marcar_orden_ejecutada($orden);

	}

	if(isset($_GET['librordenes'])) {
		libro_ordenes($_GET['token']);
	}

	if(isset($_GET['historialcredi'])){
		echo "
		<table style=\"width:100%;font-size:13px;\">
	    <thead>
	      <th></th>
	      <th></th>
	      <th></th>
	      <th></th>
	      <th></th>
	      <th></th>
	    </thead>
	    <tbody>
			";
	  $color="";
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

		if($_GET['filtro']=="month"){
			$consulta = "select * from PRESTAMOTOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND PAGADO>=1 AND MONTH(INICIO)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(INICIO)= YEAR(CURRENT_TIMESTAMP()) order by INICIO DESC";
		}
		else if($_GET['filtro']=="day"){
			$consulta = "select * from PRESTAMOTOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND PAGADO>=1 AND DAY(INICIO)= DAY(CURRENT_TIMESTAMP()) AND MONTH(INICIO)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(INICIO)= YEAR(CURRENT_TIMESTAMP()) order by INICIO DESC";
		}
		else {
			$consulta = "select * from PRESTAMOTOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND PAGADO>=1 ORDER BY INICIO DESC";
		}

	  $resultado = mysqli_query( $conexion, $consulta );
	  while($row = mysqli_fetch_array($resultado)){
	    if($row['PAGADO']==1){
	      $color="#0ECB81";
	    }
	    else if ($row['PAGADO']==2){
	      $color="#F6465D";
	    }
	    echo "
	    <tr style='color:".$color.";'>
	      <td>".fechaCredito($row['INICIO'])."</td>
	      <td>".$row['TOKEN']."</td>
	      <td style='text-align:right;'>".miformat($row['CAPITAL'])." FRD</td>
	      <td>".miformat($row['TASA'])."</td>
	      <td  style='text-align:right;'>".miformat($row['PORPAGAR'])." FRD</td>
	      ";
	    echo "</tr>";
	  }
	  mysqli_close($conexion);
		echo "
	</tbody>
	</table>
	";
	}

	if(isset($_GET['historialmov'])){
		$color="";
    $mon="";
    $color_tipo="";
    $format_monto="";

    $conexion = mysqli_connect(servidor(),user(),password(),database());

		echo "
			<table style='width: 100%; text-align:center; font-size:0.8em;'>
    	<thead>
			<th></th>
    	<th></th>
    	<th></th>
    	</thead><tbody>";

		if($_GET['filtro']=="month"){
			$consulta = "select * from OPERACIONES where (CLIENTE='".readVendedor($_SESSION['user'])['CORREO']."' OR CAJERO='".readVendedor($_SESSION['user'])['CORREO']."') AND TIPO <> 'SOPORTE' AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by FECHA desc";
		}
		else if($_GET['filtro']=="day"){
			$consulta = "select * from OPERACIONES where (CLIENTE='".readVendedor($_SESSION['user'])['CORREO']."' OR CAJERO='".readVendedor($_SESSION['user'])['CORREO']."') AND TIPO <> 'SOPORTE' AND DAY(FECHA)= DAY(CURRENT_TIMESTAMP()) AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by FECHA desc";
		}
		else {
			$consulta = "select * from OPERACIONES where (CLIENTE='".readVendedor($_SESSION['user'])['CORREO']."' OR CAJERO='".readVendedor($_SESSION['user'])['CORREO']."') AND TIPO <> 'SOPORTE' order by FECHA desc";
		}

    $resultado = mysqli_query( $conexion, $consulta );
    while($row = mysqli_fetch_array($resultado)){

			$mon= $row['MONEDA'];

      if($row['TIPO']=='DEPOSITO' || $row['TIPO']=='COMPRA'){
				$color_tipo="#2CA201";
			}
      if($row['TIPO']=='RETIRO' || $row['TIPO']=='VENTA'){
				$color_tipo="#FF0000";
			}
      if($row['TIPO']=='PAGO'){
        if($row['SUJETO']=='EMI' && $row['CAJERO']==readVendedor($_SESSION['user'])['CORREO']){
          $color_tipo="#FF0000";
        }
        else {
          $color_tipo="#2CA201";
        }
      }
			if($row['TIPO']=='VENTA'){
					$mon="FRD";
			}

      $format_monto=number_format($row['MONTO'],2,",",".");

      echo  "
				<tr>
					<td>".latinFecha($row['FECHA'])."</td>
      		<td>{$row['ESTATUS']}</td>
      		<td style='text-align:left;'>
        		Orden: {$row['TICKET']} <span style='color:{$color_tipo}; font-weight: 600;'>{$row['TIPO']}</span>
        		por {$format_monto} {$mon}";
      if($row['TIPO']=='PAGO' && $color_tipo=='#FF0000'){
          echo " a <span style='font-weight:bold;'> {$row['CLIENTE']}</span> ";
      }
      if($row['TIPO']=='PAGO' && $color_tipo=='#2CA201'){
          echo " de <span style='font-weight:bold;'> {$row['CAJERO']}</span> ";
      }
      echo "</td></tr>";
    }
    mysqli_close($conexion);
    echo "</tbody></table></div><br><br>";
	}

	if(isset($_GET['txid'])) {
	  $precio=0;
		$color="black";
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

		if($_GET['filtro']=="month"){
			$consulta = "select * from OPERACIONES where CLIENTE='".$_GET['email']."' AND TIPO='RETIRO' OR TIPO='TRANSF' AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by FECHA DESC";
		}
		else if($_GET['filtro']=="day"){
			$consulta = "select * from OPERACIONES where CLIENTE='".$_GET['email']."' AND TIPO='RETIRO' OR TIPO='TRANSF'  AND DAY(FECHA)= DAY(CURRENT_TIMESTAMP()) AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by FECHA DESC";
		}
		else {
			$consulta = "select * from OPERACIONES where CLIENTE='".$_GET['email']."' AND TIPO='RETIRO' OR TIPO='TRANSF' order by FECHA DESC";
		}
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar las Ordenes");

	  echo "<table><thead>
	  <th>Fecha</th>
	  <th>Id</th>
	  <th>Monto</th>
	  <th>Operacion</th>
	  <th>Estatus</th>
	  <th>Txid</th>
	  </thead><tbody>";
	  while($row = mysqli_fetch_array($resultado)){
			$tag = $row['TICKET'];
			$url = 'https://tronapi.net/api/.status';
			$parameters = [
			  'key' => '7ebe0e6515-c16a37d405-ca658d38d1-1e591cc646',
			  'id' => $tag			  
			];
			$qs = http_build_query($parameters); // query string encode the parameters
			$request = "{$url}?{$qs}"; // create the request URL
			$curl = curl_init();
		  
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $request,            // set the request URL
			  CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
			));
		  
			$response = curl_exec($curl);
		  
			$pepe = json_decode($response);
			if(!empty($pepe) ){ 
			  if(isset($pepe->result)){
				if($pepe->error != "send_not_found"){
				  echo "
				<tr>
				<td>".latinFecha($row['FECHA'])."</td>
				<td>".$tag."</td>
				<td>".number_format($row['RECIBE'],2,",",".")."USDT</td>
				<td>".$row['TIPO']."</td>
				<td>".$pepe->result->state."</td>
				<td>".$pepe->result->txid."</td>
			  </tr>
			  ";
			  }
				curl_close($curl);
			  }
			}
	  }
	  echo "</tbody></table>";
	  mysqli_close($conexion);
	}

	if(isset($_GET['ordenes'])) {
	  $precio=0;
		$color="black";
	  $conexion = mysqli_connect(servidor(),user(),password(),database());

		if($_GET['filtro']=="month"){
			$consulta = "select * from ORDENTOKEN where USUARIO='".$_GET['email']."' AND TOKEN='".$_GET['token']."' AND EJECUTADO=1 AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by FECHA DESC";
		}
		else if($_GET['filtro']=="day"){
			$consulta = "select * from ORDENTOKEN where USUARIO='".$_GET['email']."' AND TOKEN='".$_GET['token']."' AND EJECUTADO=1 AND DAY(FECHA)= DAY(CURRENT_TIMESTAMP()) AND MONTH(FECHA)= MONTH(CURRENT_TIMESTAMP()) AND YEAR(FECHA)= YEAR(CURRENT_TIMESTAMP()) order by FECHA DESC";
		}
		else {
			$consulta = "select * from ORDENTOKEN where USUARIO='".$_GET['email']."' AND TOKEN='".$_GET['token']."' AND EJECUTADO=1 order by FECHA DESC";
		}
	  $resultado = mysqli_query( $conexion, $consulta ) or die("No se pudo Consultar las Ordenes");
	  echo "<table class='table2' ><thead>
	  <th>Orden</th>
	  <th>Fecha</th>
	  <th>FRD</th>
	  <th>Comercio</th>
	  <th>Precio</th>
	  </thead><tbody>";
	  while($row = mysqli_fetch_array($resultado)){
	  		if($row['OPERACION']=="COMPRA") {
				$precio=$row['PRECIO_COMPRA'];
				$color="#0ECB81";
	  		}
	  		else {
				$precio=$row['PRECIO_VENTA'];
				$color="red";
	  		}
	  		echo "
	  		<tr>
	  		<td><span style='font-weight:bold;background:none; color:".$color.";'>".$row['OPERACION']."</span></td>
	  		<td>".latinFecha($row['FECHA'])."</td>
	  		<td>".number_format($row['TOTAL'],4,",",".")."FRD</td>
	  		<td>".number_format($row['CANTIDAD'],4,",",".").$row['TOKEN']."</td>
	  		<td>".$precio."</td>
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

   function update_saldo_f_positivo($valor,$correo,$cantidad,$token){
   	$saldo_anterior=readCliente($correo)['SALDO'];
   	sqlconector("UPDATE USUARIOS SET SALDO=".strval($saldo_anterior + $valor)." WHERE CORREO='".$correo."'");
		$saldo_anterior_saldo=read_wallet_token($correo,$token)['SALDO'];
		sqlconector("UPDATE WALLETOKEN SET SALDO=".strval($saldo_anterior_saldo - $cantidad)." WHERE CORREO='".$correo."' AND TOKEN='".$token."'");
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
		//sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo("fortuna@fortunaroyal.com",$_POST['token']) + $_POST['monto'])." WHERE TOKEN='".$_POST['token']."' AND CORREO='fortuna@fortunaroyal.com'");
  }

  if(isset($_GET['saldotoken'])) {
  		echo loQueTengo($_GET['correo'],$_GET['token']);
  }

	if(isset($_GET['saldotope'])) {
  		echo redo(strval(readToken($_GET['token'])['PATRIMONIO'] - deudaToken(readToken($_GET['token'])['CORREO'],$_GET['token'])));
  }

  if(isset($_GET['valortoken'])) {
			recalcTokens();
  		echo readToken($_GET['valortoken'])['VALOR'];
  }

  if(isset($_POST['cancelarorden'])) {
  		if(read_orden_token($_POST['numero'])['OPERACION']=="COMPRA") {
  			//espero me devuelvan mis FRD
  			$loquedevuelvo=read_orden_token($_POST['numero'])['RESTANTE'] * read_orden_token($_POST['numero'])['PRECIO_COMPRA'];
  			update_saldo_f_positivo($loquedevuelvo,read_orden_token($_POST['numero'])['USUARIO'],0,read_orden_token($_POST['numero'])['TOKEN']);
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
			if (!$conexion) {
				echo "Refresh page, Failed to connect to Data...";
				exit();
			}else{
				$consulta = "select * from WALLETOKEN WHERE CORREO='".$correo."'";
				$resultado = mysqli_query( $conexion, $consulta );
				$valor_F=0;
				while($row = mysqli_fetch_array($resultado)){
					 $valor_F=$row['CANTIDAD'] * readToken($row['TOKEN'])['VALOR'];
					 updateTotalWalleToken($correo,$row['TOKEN'],$valor_F);
				   }
				   mysqli_close($conexion);
			}
	  }

	  function miformat($number){
		  return number_format($number,2,",",".");
	  }

		function redo($number){
		  return number_format($number,0,",",".");
	  }

		function price($price){
			if($price >= 1)
				return number_format($price,2,",",".");
			return number_format($price,4,",",".");
		}

	  if(isset($_GET['miswallet'])){
	  	recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
		  $total=0;
		echo "<table style=''>
			<thead style='background:lightgray'>
			<th>Wallet</th>
			<th>Cantidad</th>
			<th>Saldo En FRD</th>
			<th></th>
			</thead>
			<tbody>
			<tr><td></td><td></td><td></td><td></td></tr>
			<tr>
			<td>Fortuna Royal|USDT </td><td>".readVendedor($_SESSION['user'])['SALDO']."<b>FRD</b></td><td style='text-align: right;'>".miformat(readVendedor($_SESSION['user'])['SALDO'])."</td><td>
			<a style='text-decoration: underline;' onclick='fondeo()'>Comprar</a> <a style='text-decoration: underline;margin-left:5px;' onclick='retiro()'>Vender</a></td>
			</tr>";
		$conexion = mysqli_connect(servidor(),user(),password(),database());
		if (!$conexion) {
			echo "Refresh page, Failed to connect to Data...";
			exit();
		}else{
			$consulta = "select * from WALLETOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."'";
			$resultado = mysqli_query( $conexion, $consulta );
			$total=readVendedor($_SESSION['user'])['SALDO'];
			while($row = mysqli_fetch_array($resultado)){
				$total=$total + $row['TOTAL'];
				echo "<tr><td>".readToken($row['TOKEN'])['NOMBRE']."</td><td>".$row['CANTIDAD']."<b>".$row['TOKEN']."</b></td><td style='text-align: right;'>".miformat($row['TOTAL'])."</td>
				<td><a style='text-decoration: underline;' href='operar?token=".$row['TOKEN']."'>Comprar</a> <a style='text-decoration: underline;margin-left:5px;' href='operar?token=".$row['TOKEN']."'>Vender</a></td></tr>";
			   }
			   mysqli_close($conexion);
		}
		echo "<tr><td></td><td></td><td></td><td></td></tr>
		<tr>
			<td></td><td></td><td>Total <span style='color:gray; font-size:13px; '>FRD</span><span style='color:black; font-size:13px; '>|</span><span style='color:dark#0ECB81; font-size:13px;'>USDT</span></td><td style='color:#0ECB81;background:light#0ECB81;text-align: right;'><b>".miformat($total)."</b></td>
			</tr>
			</tbody></table>";
	  }

	  function if_exist_ref($referencia) {
			$conexion = mysqli_connect(servidor(),user(),password(),database());
			if (!$conexion) {
				echo "Refresh page, Failed to connect to Data...";
				exit();
			}else{
				$consulta = "SELECT REFERENCIA FROM OPERACIONES WHERE REFERENCIA='".$referencia."'";
				$resultado = mysqli_query( $conexion, $consulta );
				$num=0;
				while($row = mysqli_fetch_array($resultado)){
					$num++;
				}
				mysqli_close($conexion);
			  	if($num > 1) return true;
					return false;
			}
	  }

	  function ver_ref($referencia) {
	  		$row = row_sqlconector("SELECT TICKET,REFERENCIA FROM OPERACIONES WHERE REFERENCIA='".$referencia."'");
			return " <a style='text-decoration:underline;' onclick=\"$('#frame').load('modulo?verhistorial&open&tipo&ver&tk=".$row['TICKET']."&tickedchat=".$row['TICKET']."');\">(".$row['TICKET'].")</a>";
	  }

		function balanceEstimado(){
			recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
		  $total=0;
			$conexion = mysqli_connect(servidor(),user(),password(),database());
			if (!$conexion) {
				echo "Refresh page, Failed to connect to Data...";
				exit();
			}else{
				$consulta = "select * from WALLETOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND BLOQUEADO=0 AND CANTIDAD>0";
				$resultado = mysqli_query( $conexion, $consulta );
				$total=readVendedor($_SESSION['user'])['SALDO'];
				while($row = mysqli_fetch_array($resultado)){
					$total=$total + $row['TOTAL'];
			  }
				mysqli_close($conexion);
				return miformat($total);
			}
		}

		function deudaToken($correo,$token){
			$row = row_sqlconector("SELECT * FROM PRESTAMOTOKEN WHERE CORREO='".$correo."' AND TOKEN='".$token."' AND ACEPTADO=1 AND PAGADO=0");
			if(!empty($row['TOKEN'])){
				return $row['CAPITAL'];
			}else{
					return 0;
			}
		}

		function crecimiento($patrimonio, $patrimonioinicial) {
				return (($patrimonio - $patrimonioinicial) * 100) / $patrimonioinicial;
		}

		function ifPrestamoExist($correo,$token){
			//select month(dateField), year(dateField)
			if(isset(row_sqlconector("SELECT * FROM PRESTAMOTOKEN WHERE CORREO='".$correo."' AND TOKEN='".$token."' AND ACEPTADO<=1 AND PAGADO=0")['TOKEN']))
				 return TRUE;
		 	return FALSE;
		}

		function ifDeudaExist($correo,$token){
			if(isset(row_sqlconector("SELECT * FROM PRESTAMOTOKEN WHERE CORREO='".$correo."' AND TOKEN='".$token."' AND ACEPTADO=1 AND PAGADO=0")['TOKEN']))
				 return TRUE;
		 	return FALSE;
		}

		function ifClienteIncumple($correo,$token){
			if(isset(row_sqlconector("SELECT * FROM PRESTAMOTOKEN WHERE CORREO='".$correo."' AND TOKEN='".$token."' AND ACEPTADO=1 AND PAGADO=2")['TOKEN']))
				 return TRUE;
		 	return FALSE;
		}

		function ifBEP20($correo,$token){
			if(isset(row_sqlconector("SELECT BEP20 FROM TOKEN WHERE CORREO='".$correo."' AND TOKEN='".$token."'")['BEP20']))
	  		return row_sqlconector("SELECT BEP20 FROM TOKEN WHERE CORREO='".$correo."' AND TOKEN='".$token."'")['BEP20'];
			return -1;
		}

		function deudor($correo,$token){
			if(ifClienteIncumple($correo,$token) || ifDeudaExist($correo,$token)){
				return 1;
			}else{
				return 0;
			}
		}

		function ifApto($correo,$token){
			$apto=1;
			if(countIncumplido($correo,$token)==0){
				$patrimonioinicial = readToken($token)['PATRIMONIOINICIAL'];
				$valor = readToken($token)['VALOR'];
				$wallet = read_wallet_token($correo,$token)['CANTIDAD'];
				$neto = $wallet * $valor;
				if($neto > $patrimonioinicial){
					$apto = 0;
				}else{
					$apto = 1;
				}
			}else{
				$apto=1;
			}
			return $apto;
		}

		function countIncumplido($correo,$token){
			return row_sqlconector("SELECT Count(*) as SUMA FROM PRESTAMOTOKEN WHERE CORREO='".$correo."' AND TOKEN='".$token."' AND PAGADO=2")['SUMA'];
		}

		function asignar($correo,$token,$porcen){
			/*
			I = C x t x T, donde I es el interés que vas a pagar
			C es el capital o monto recibido en préstamo.
			t es la tasa de interés
			T es el tiempo de duración del préstamo.
			*/
			$C = 0;
			$t=(readFortuna()['INTERESPRESTAMO'] + readFortuna()['COMISIONEMISION']) / 100;
			$T=1; //tiempo en mes.
			$I=0;
			list($day,$month,$year,$hour,$min,$sec) = explode("/",date('d/m/Y/h/i/s'));
			if($month == 12){
				$month==1;
				$year++;
			}
			else {
				$month++;
			}
			$fechafinal = date_create($day."-".$month."-".$year." $hour:$min:$sec");
			if(ifApto($correo,$token)==0){
				if(!ifPrestamoExist($correo,$token)){
					$C = readToken($token)['PATRIMONIO'] * ($porcen / 100);
					$I = $C * $t * $T;
					sqlconector("INSERT INTO PRESTAMOTOKEN(FIN,ORDEN,CORREO,TOKEN,CAPITAL,TASA,PORPAGAR) VALUES('".date_format($fechafinal,"Y-m-d h:i:s")."','".generaTicket()."','".$correo."','".$token."',".$C.",".$t.",".strval($C+$I).")");
					insertNotif($correo,"Disponible Un Nuevo Credito.","");
				}
			}
		}

		function fechaCredito($fecha){
			$date=date_create($fecha);
			return date_format($date,"d/M/y");
	  }

		function asignarCredito(){			
			$conexion = mysqli_connect(servidor(),user(),password(),database());
			if (!$conexion) {
				echo "Refresh page, Failed to connect to Data...";
				exit();
			}else{
				$consulta = "select * from TOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND BLOQUEADO=0";
				$resultado = mysqli_query( $conexion, $consulta );
				$result=0;
				while($row = mysqli_fetch_array($resultado)){
					$result=crecimiento($row['PATRIMONIO'], $row['PATRIMONIOINICIAL']);
					 	if($result > 21 && $result < 34){
							asignar($row['CORREO'],$row['TOKEN'],21);
						}
						if($result > 55 && $result < 89){
							asignar($row['CORREO'],$row['TOKEN'],21);
						}
						if($result > 89 && $result < 144){
							asignar($row['CORREO'],$row['TOKEN'],34);
						}
						if($result > 144){
							asignar($row['CORREO'],$row['TOKEN'],34);
						}
				}
				mysqli_close($conexion);
			}
		}

		function readCredito($id){
			return row_sqlconector("SELECT * FROM PRESTAMOTOKEN WHERE ID=".$id);
		}

		function recordCount($table){
			return row_sqlconector("SELECT Count(*) as SUMA FROM ".$table)['SUMA'];
		}

		function countPreci($token){
	    $recordcount = row_sqlconector("SELECT Count(*) as SUMA FROM PRECIOTOKEN WHERE TOKEN='".$token."'")['SUMA'];
	    $x=1;
	    $precioactual = 0;
	    $precioanterior = 0;
			$volactual = 0;
			$volanterior = 0;
			$patrimonioactual = 0;
			$patrimonioanterior = 0;
			$crecimiento = 0;
	    $conexion = mysqli_connect(servidor(),user(),password(),database());

	    $consulta = "select * from PRECIOTOKEN WHERE TOKEN='".$token."'";
	    $resultado = mysqli_query( $conexion, $consulta );
	    while($row = mysqli_fetch_array($resultado)){
	      if($x == ($recordcount - 1 )){
	        $precioanterior = $row['PRECIO'];
					$volanterior = $row['VOLUMEN'];
					$patrimonioanterior = $row['PATRIMONIO'];
	      }
	      if($x == $recordcount){
	        $precioactual = $row['PRECIO'];
					$volactual = $row['VOLUMEN'];
					$patrimonioactual = $row['PATRIMONIO'];
					$crecimiento = $row['CRECIMIENTO'];
	      }
	      $x++;
	    }
	    return array($precioactual,$precioanterior,$volactual,$volanterior,$patrimonioactual,$patrimonioanterior,$crecimiento);
	  }

		function is_negative_number($number=0){
			if( is_numeric($number) AND ($number<0) ){
				return true;
			}else{
				return false;
			}
		}

		function creci24($token){
			$creci24 = countPreci($token);
			return $creci24[6];
		}

		function colorCreci24($token){
			$color="#0ECB81";
			if(is_negative_number(creci24($token))){
				$color= "#F6465D";
			}
			else{
				$color="#0ECB81";
			}
			return $color;
		}

		function getSymbol($token){
		  $symbol="▲";
		  $precio = countPreci($token);
		  if($precio[0] < $precio[1]){
		    $symbol= "▼";
		  }
		  else{
		    $symbol="▲";
		  }
		  return $symbol;
		}

		function colorPrecio($token){
			$color="#0ECB81";
			$precio = countPreci($token);
			if($precio[0] < $precio[1]){
				$color= "#F6465D";
			}
			else{
			 	$color="#0ECB81";
			}
			return $color;
		}

		function colorCap($token){
			$color="#0ECB81";
			$cap = countPreci($token);
			if($cap[4] < $cap[5]){
				$color= "#F6465D";
			}
			else{
			 	$color="#0ECB81";
			}
			return $color;
		}

		function colorVol($token){
			$color="#0ECB81";
			$vol = countPreci($token);
			if($vol[2] < $vol[3]){
				$color= "#F6465D";
			}
			else{
			 	$color="#0ECB81";
			}
			return $color;
		}

		function getRealIpAddr(){
		  $ipaddress = '';
		  if (getenv('HTTP_CLIENT_IP'))
		    $ipaddress = getenv('HTTP_CLIENT_IP');
		  else if(getenv('HTTP_X_FORWARDED_FOR'))
		    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		  else if(getenv('HTTP_X_FORWARDED'))
		    $ipaddress = getenv('HTTP_X_FORWARDED');
		  else if(getenv('HTTP_FORWARDED_FOR'))
		    $ipaddress = getenv('HTTP_FORWARDED_FOR');
		  else if(getenv('HTTP_FORWARDED'))
		    $ipaddress = getenv('HTTP_FORWARDED');
		  else if(getenv('REMOTE_ADDR'))
		    $ipaddress = getenv('REMOTE_ADDR');
		  else
		    $ipaddress = 'UNKNOWN';
		  return $ipaddress;
		}
?>
