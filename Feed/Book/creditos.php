<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');
recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
asignarCredito();

if(isset($_POST['aceptar'])){
  sqlconector("UPDATE PRESTAMOTOKEN SET ACEPTADO=1 WHERE ID=".$_POST['id']);
  //sqlconector("UPDATE TOKEN SET PATRIMONIO=".strval(readToken(readCredito($_POST['id'])['TOKEN'])['PATRIMONIO']-readCredito($_POST['id'])['CAPITAL'])." WHERE TOKEN='".readCredito($_POST['id'])['TOKEN']."'");
  updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO']-readCredito($_POST['id'])['CAPITAL'],"fortuna@fortunaroyal.com");
  updateSaldo(readVendedor($_SESSION['user'])['SALDO']+readCredito($_POST['id'])['CAPITAL'],readVendedor($_SESSION['user'])['CORREO']);
  recalcTokens();
}

if(isset($_POST['pagar'])){
  $ganancia = readCredito($_POST['id'])['PORPAGAR'] - readCredito($_POST['id'])['CAPITAL'];
  $comision = $ganancia * tasa(readFortuna()['COMISIONEMISION']);

  updateSaldo(readVendedor($_SESSION['user'])['SALDO'] - readCredito($_POST['id'])['PORPAGAR'],readVendedor($_SESSION['user'])['CORREO']);
  updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO'] + readCredito($_POST['id'])['PORPAGAR'],"fortuna@fortunaroyal.com");

  if(readVendedor($_SESSION['user'])['CORREO']=="fortuna@fortunaroyal.com"){
    updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO'] + $ganancia,"fortuna@fortunaroyal.com");
  }

  sqlconector("UPDATE TOKEN SET PATRIMONIO=".strval(readToken(readCredito($_POST['id'])['TOKEN'])['PATRIMONIO']+($ganancia - $comision))." WHERE TOKEN='".readCredito($_POST['id'])['TOKEN']."'");
  sqlconector("INSERT INTO LIBROCONTABLE(TIPO,MONTO) VALUES('CREDITO',".$comision.")");
  sqlconector("UPDATE PRESTAMOTOKEN SET PAGADO=1 WHERE ID=".$_POST['id']);
  recalcTokens();
}
?>

<!DOCTYPE html>
<html lang="en" style="overflow-y:auto;" >
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="./style2.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <style>
  /* width */
  ::-webkit-scrollbar {
    width: 5px;
  }

  /* Track */
  ::-webkit-scrollbar-track {
    background: #263238;
  }

  /* Handle */
  ::-webkit-scrollbar-thumb {
    background: #263238;
  }

  /* Handle on hover */
  ::-webkit-scrollbar-thumb:hover {
    background: #263238;
  }
  </style>

  <script>
  function getOrdenes(tiempo){
    $.get("../../modulo?historialcredi&email="+document.getElementById('correo').value+"&filtro="+tiempo,
      function(data){
        $("#div3").html(data);
      });
  }

  function aceptar(id){
    $.post("creditos.php",
    {
    aceptar: "",
    id: id
    },
    function(data){
      window.location.href="book.php";
    });
  }

  function pagar(id,porpagar){
    if(document.getElementById("saldo").value >= porpagar){
      $.post("creditos.php",
      {
      pagar: "",
      id: id
      },
      function(data){
        window.location.href="book.php";
      });
    }else {
      alert("No tiene saldo suficiente recargue...!");
    }
  }

  function pagoAuto(tid){
  var valor= 0;
  if(document.getElementById('auto').checked === true){
    valor = 1;
  }
  $.post("../../block",{
    pagoauto: valor,
    id: tid
  },function(data){
    
  });
}  
  </script>
</head>
<body onload="getOrdenes('day')">
<!-- partial:index.partial.html -->
<input type="hidden" id="correo" value="<?php echo readVendedor($_SESSION['user'])['CORREO']?>">
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
		<h1 class="apps-title">Creditos</h1>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h1 class="apps-title">Creditos</h1>
  </div>

		<h1 class="apps-title"></h1>
    <!-- aqui va el contenido -->
    <input type="hidden" id="saldo" value="<?php echo readVendedor($_SESSION['user'])['SALDO']?>">
    <!--****************************************** Asignacion de Creditos *************************-->
    <span style="color:white;font-size:13px;">ASIGNADO</span>
    <table style="width:100%;font-size:13px;">
      <thead>
        <th>Finaliza</th>
        <th>Comercio</th>
        <th>Prestamo x</th>
        <th>%I Mens</th>
        <th>Total a Pagar</th>
        <th></th>
      </thead>
      <tbody>
    <?php
    $conexion = mysqli_connect(servidor(),user(),password());
    $db = mysqli_select_db( $conexion, database() );
    $consulta = "select * from PRESTAMOTOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND PAGADO=0";
    $resultado = mysqli_query( $conexion, $consulta );
    while($row = mysqli_fetch_array($resultado)){
      if(ifApto(readVendedor($_SESSION['user'])['CORREO'],$row['TOKEN'])==0){
        echo "
        <tr>
          <td>".fechaCredito($row['FIN'])."</td>
          <td>".$row['TOKEN']."</td>
          <td>".miformat($row['CAPITAL'])." FRD</td>
          <td>".miformat($row['TASA'])."</td>
          <td>".miformat($row['PORPAGAR'])." FRD</td>
          ";
          if($row['ACEPTADO']==0){
              echo "<td><a style='cursor:pointer; border:1px solid white; border-radius:3px;padding:2px;' onclick='aceptar(".$row['ID'].")'>Aceptar</a></td>";
          }
          else{
            $checked = "";
            if($row['AUTO']==1) $checked = "checked";
            echo "<td><a style='cursor:pointer; border:1px solid white; border-radius:3px;padding:2px;' onclick='pagar(".$row['ID'].",".$row['PORPAGAR'].")'>Pagar</a>
                      <input type='checkbox' {$checked} id='auto' onclick='pagoAuto({$row['ID']})'><label for='auto' title='Pago Automatico'>Auto</label></td>";
          }
        echo "</tr>";
      }
    }
    mysqli_close($conexion);
    ?>
  </tbody>
</table>
<br><br>
<!--******************************************Creditos Incumplidos *************************-->
<span style="color:white;font-size:13px;">INCUMPLIDOS</span>
<table style="width:100%;font-size:13px;">
  <thead>
    <th>Finalizo</th>
    <th>Comercio</th>
    <th>Prestamo x</th>
    <th>%I Mens</th>
    <th>Total a Pagar</th>
    <th></th>
  </thead>
  <tbody>
<?php
$conexion = mysqli_connect(servidor(),user(),password());
$db = mysqli_select_db( $conexion, database() );
$consulta = "select * from PRESTAMOTOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND PAGADO=2";
$resultado = mysqli_query( $conexion, $consulta );
while($row = mysqli_fetch_array($resultado)){
    echo "
    <tr style='color:#F6465D;'>
      <td>".fechaCredito($row['FIN'])."</td>
      <td>".$row['TOKEN']."</td>
      <td>".miformat($row['CAPITAL'])." FRD</td>
      <td>".miformat($row['TASA'])."</td>
      <td>".miformat($row['PORPAGAR'])." FRD</td>
      ";
      if($row['ACEPTADO']==1){
          echo "<td><a style='color:white;cursor:pointer; border:1px solid white; border-radius:3px;padding:2px;' onclick='pagar(".$row['ID'].",".$row['PORPAGAR'].")'>Pagar</a></td>";
      }
    echo "</tr>";
}
mysqli_close($conexion);
?>
</tbody>
</table>
<br><br>

<!--****************************************** Historial *************************-->
<div style=" color:white;">HISTORIAL
  <a onclick="getOrdenes('day')" style="text-decoration: underline; font-size:13px; padding: 3px; cursor:pointer; color:white;font-weight:bold;">Dia</a>
  <a onclick="getOrdenes('month')" style="text-decoration: underline; font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Mes</a>
  <a onclick="getOrdenes('all')" style="text-decoration: underline; font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Todo</a>
</div>
<hr style="color:white;">
<div style="padding:13px; background: #151332;" id="div3"></div>
<div style="height:55px;"></div>
<br>
<br>
<br>
</body>
</html>
