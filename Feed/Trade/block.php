<?php
  date_default_timezone_set('America/Caracas');
include "../../modulo.php";

if(isset($_POST['ejecutaVenta'])){
  $tag = generaTicket();
  $monto = $_POST['monto'];
  $recibe = $_POST['recibe'];
  $cliente = $_POST['correo'];
  $cajero = readUserDataMetodoId($_POST['anunciante'])['CORREO'];
  $fiat = readUserDataMetodoId($_POST['anunciante'])['METODO'];
  sqlconector("UPDATE USUARIOS SET SALDO=".strval(readCliente($cliente)['SALDO'] - $monto)." WHERE CORREO='{$cliente}'" );
  sqlconector("INSERT INTO OPERACIONES(TICKET,SUJETO,CAJERO,CLIENTE,TIPO,WALLET,MONTO,RECIBE,ESTATUS,MONEDA)
        VALUES({$tag},'P2P','{$cajero}','{$cliente}','VENTA','BANCO',{$monto},{$recibe},'EN PROCESO','{$fiat}')");
  $saldo=readCliente($cliente)['SALDO'] - $monto;
  sqlconector("UPDATE USUARIOS SET SALDO={$saldo} WHERE CORREO='{$cliente}'");
  $saldoCajero = readUserDataMetodoId($_POST['anunciante'])['SALDOFIAT'] - $recibe;
  sqlconector("UPDATE USUARIOSMETODOS SET SALDOFIAT={$saldoCajero},ACTIVO=0  WHERE CORREO='{$cajero}' AND METODO='{$fiat}'");
  echo $tag;
}

if(isset($_POST['ejecutaCompra'])){
  $tag = generaTicket();
  $monto = $_POST['monto'];
  $recibe = $_POST['recibe'];
  $cliente = $_POST['correo'];
  $cajero = readUserDataMetodoId($_POST['anunciante'])['CORREO'];
  $fiat = readUserDataMetodoId($_POST['anunciante'])['METODO'];
  sqlconector("UPDATE USUARIOS SET SALDO=".strval(readCliente($cliente)['SALDO'] - $monto)." WHERE CORREO='{$cliente}'" );
  sqlconector("INSERT INTO OPERACIONES(TICKET,SUJETO,CAJERO,CLIENTE,TIPO,WALLET,MONTO,RECIBE,ESTATUS,MONEDA)
        VALUES({$tag},'P2P','{$cajero}','{$cliente}','COMPRA','BANCO',{$monto},{$recibe},'EN PROCESO','{$fiat}')");
  insertChat($cajero,$tag,$cajero,$cliente,readUserDataMetodoId($_POST['anunciante'])['WALLET']);
  $saldo=readCliente($cajero)['P2P'] - $recibe;
  sqlconector("UPDATE USUARIOS SET P2P={$saldo} WHERE CORREO='{$cajero}'");
  sqlconector("UPDATE USUARIOSMETODOS SET SALDOFRD={$saldo},ACTIVO=0 WHERE CORREO='{$cajero}' AND METODO='{$fiat}'");
  echo $tag;
}

if(isset($_POST['cancelarCompra'])){
    $ticked = readTicked($_POST['cancelarCompra']);
    $cajero = $ticked['CAJERO'];
    $metodo = $ticked['MONEDA'];
    $montoFrd = readUserDataMetodo($cajero,$metodo)['SALDOFRD'] + $ticked['RECIBE'];
    $montoP2p = readCliente($cajero)['P2P'] + $ticked['RECIBE'];
    sqlconector("UPDATE USUARIOSMETODOS SET ACTIVO=1, SALDOFRD={$montoFrd} WHERE CORREO = '{$cajero}' AND METODO='{$metodo}'");
    sqlconector("UPDATE USUARIOS SET P2P={$montoP2p} WHERE CORREO='{$cajero}'");
    sqlconector("UPDATE OPERACIONES SET PAGADO=1, ESTATUS='CANCELADO' WHERE TICKET=".$ticked['TICKET']);
    insertChat($ticked['CLIENTE'],$ticked['TICKET'],$ticked['CLIENTE'],$cajero,"El Cliente Ha Cancelado la Orden...!");
}

if(isset($_POST['recibirVenta'])){
    $ticked = readTicked($_POST['recibirVenta']);
    $cajero = $ticked['CAJERO'];
    $montoP2p = readCliente($cajero)['SALDO'] + $ticked['MONTO'];
    sqlconector("UPDATE USUARIOS SET P2P={$montoP2p} WHERE CORREO='{$cajero}'");
    sqlconector("UPDATE USUARIOSMETODOS SET ACTIVO=1 WHERE CORREO = '{$cajero}'");
    sqlconector("UPDATE OPERACIONES SET PAGADO=1, ESTATUS='COMPLETADO' WHERE TICKET=".$ticked['TICKET']);
}

if(isset($_POST['marcarEnvio'])){
	$ticked = readTicked($_POST['marcarEnvio']);
	sqlconector("UPDATE OPERACIONES SET ENVIADO=1, ESTATUS='CONFIRMACION' WHERE TICKET=".$ticked['TICKET']);
	insertChat($ticked['CLIENTE'],$ticked['TICKET'],$ticked['CLIENTE'],$ticked['CAJERO'],"El Cliente Confirma que envio el Pago...!");
}

if(isset($_GET['getUserMetodo'])){
  $row=readUserDataMetodoId($_GET['id']);
  echo $row['ID'].
   ",".$row['CORREO'].
   ",".$row['METODO'].
   ",".$row['WALLET'].
   ",".$row['PRECIOVENTA'].
   ",".$row['PRECIOCOMPRA'].
   ",".$row['SALDOFIAT'].
   ",".$row['SALDOFRD'].
   ",".$row['RATE'].
   ",".$row['ACTIVO'].
   ",".$row['BLOQUEADO'];
   /*
   0 ID
   1 CORREO
   2 METODO
   3 WALLET
   4 PRECIOVENTA
   5 PRECIOCOMPRA
   6 SALDOFIAT
   7 SALDOFRD
   8 RATE
   9 ACTIVO
   10 BLOQUEADO
   */
}


if(isset($_GET['findComprar'])){
  $monto = $_GET['monto'];
  $fiat = $_GET['fiat'];

  $conexion = mysqli_connect(servidor(),user(),password());
  $db = mysqli_select_db( $conexion, database());

  if($monto > 1){
    $consulta = "select * from USUARIOSMETODOS WHERE BLOQUEADO=0 AND ACTIVO=1 AND SALDOFRD=>{$monto} ORDER BY SALDOFRD";
  }
  else{
    $consulta = "select * from USUARIOSMETODOS WHERE BLOQUEADO=0 AND ACTIVO=1 ORDER BY SALDOFRD";
  }
  echo "
  <table style='width:100%;'>
    <th>Anunciante</th>
    <th>Precio</th>
    <th>Disponible</th>
    <th>Operacion</th>
  ";
  $resultado = mysqli_query( $conexion, $consulta );
  while($row = mysqli_fetch_array($resultado)){
   $nombre = readCliente($row['CORREO'])['NOMBRE'];
   $precio = miformat($row['PRECIOVENTA']);
   $saldo = miformat($row['SALDOFRD']);
   echo "<tr>
    <td><span>{$nombre}</span></td>
    <td><span>{$precio}{$fiat}</span></td>
    <td><span>{$saldo}FRD</span></td>
    <td><button type='button' onclick=\"sicompro({$row['ID']})\">Comprar FRD</button></td>
    </tr>";
  }

  echo "</table>";
  mysqli_close($conexion);
}

if(isset($_GET['findVender'])){
  $monto = $_GET['monto'];
  $fiat = $_GET['fiat'];

  $conexion = mysqli_connect(servidor(),user(),password());
  $db = mysqli_select_db( $conexion, database());

  if($monto > 1){
    $consulta = "select * from USUARIOSMETODOS WHERE BLOQUEADO=0 AND ACTIVO=1 AND SALDOFIAT=>{$monto} ORDER BY SALDOFIAT";
  }
  else{
    $consulta = "select * from USUARIOSMETODOS WHERE BLOQUEADO=0 AND ACTIVO=1 ORDER BY SALDOFIAT";
  }
  echo "
  <table style='width:100%;'>
    <th>Anunciante</th>
    <th>Precio</th>
    <th>Disponible</th>
    <th>Operacion</th>
  ";
  $resultado = mysqli_query( $conexion, $consulta );
  while($row = mysqli_fetch_array($resultado)){
   $nombre = readCliente($row['CORREO'])['NOMBRE'];
   $precio = miformat($row['PRECIOCOMPRA']);
   $saldo = miformat($row['SALDOFIAT']);
   echo "<tr>
    <td><span>{$nombre}</span></td>
    <td><span>{$precio}{$fiat}</span></td>
    <td><span>{$saldo}{$fiat}</span></td>
    <td><button type='button' onclick=\"sivendo(".$row['ID'].")\">Vender FRD</button></td>
    </tr>";
  }
  echo "</table>";
  mysqli_close($conexion);

}

if(isset($_GET['findOrdenes'])){
  $correo = $_GET['correo'];

  $conexion = mysqli_connect(servidor(),user(),password());
  $db = mysqli_select_db( $conexion, database());

  $consulta = "select * from OPERACIONES WHERE PAGADO=0 AND SUJETO='P2P' AND CLIENTE='{$correo}'  ORDER BY FECHA";

  echo "
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
    <td><button type='button' onclick=\"ver(".$row['TICKET'].")\">Ver</button></td>
    </tr>";
  }
  echo "</table>";
  mysqli_close($conexion);
}

if(isset($_POST['datatrader'])){
  recalcTokens();
  $token = $_POST['token'];
  $correo = $_POST['correo'];
  $saldoTope = redo(strval(readToken($token)['PATRIMONIO'] - deudaToken(readToken($token)['CORREO'],$token)));

  $arr = array('saldotoken' => loQueTengo($correo,$token),'saldofrd' => readCliente($correo)['SALDO'],
        'preciotoken' => readToken($token)['VALOR'],'saldotope' => $saldoTope,'volumen' => readToken($token)['VOLUMEN'],
        'patrimonio' => readToken($token)['PATRIMONIO'],'colorcap' => colorCap($token),
        'colorvol' => colorVol($token),'colorcreci' => colorCreci24($token),'creci' => redo(creci24($_GET['token'])),
        'token' => $token, 'cliente' => $correo,'colorprecio' => colorPrecio($token)
      );

  echo json_encode($arr);
}
?>
