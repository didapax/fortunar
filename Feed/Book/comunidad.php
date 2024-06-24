<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');

if(isset($_POST['ticket'])){
  if(strlen($_POST['motivo'])>3){
    sqlconector("INSERT INTO OPERACIONES(TICKET,CAJERO,CLIENTE,TIPO,REFERENCIA,ESTATUS)
          VALUES(".generaTicket().",'".readFortuna()['SOPORTE']."','".readVendedor($_SESSION['user'])['CORREO']."','SOPORTE','".$_POST['motivo']."','EN PROCESO')");
  }
}
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="./style2.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <script>
    function trade(token){
      window.location.href="../Games/info.php?token="+token;
    }
  </script>
  <style>
a{
  margin: 10px;
  font-size: 34px;
  color: white;
}
body{
  color: white;
}

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
</head>
<body style="padding:15px;">
<!-- partial:index.partial.html -->
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title">Únete a la comunidad de Fortuna Royal</h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">Únete a la comunidad de Fortuna Royal</h3>
  </div>
  <div style="  width: 90%; text-align: justify;">
    <p>
    <h2>Únete a la conversación</h2>
    Descubre los grupos oficiales de Telegram que versan sobre una gran variedad de temas, como el trading, los Comercios con sus participaciones mas relevantes, los anuncios de la plataforma y mucho más.
    Encuentra tu comunidad.
    Por supuesto, Fortuna Royal no solo está presente en Telegram. También puedes seguirnos en Twitter, Facebook, Instagram y otras redes para recibir las últimas noticias,
    actualizaciones y sugerencias de negocios.
    </p>
    <div style"padding:15px;">
      <a target="_blank" href="https://www.facebook.com/fortunaroyal.official/"><span class="fab fa-facebook"></span></a>
      <a target="_blank" href="https://www.instagram.com/fortunaroyal_official/"><span class="fab fa-instagram"></span></a>
      <a target="_blank" href="https://t.me/+g5MF5glVI1gwMmNh"><span class="fab fa-telegram"></span></a>
      <a target="_blank" href="https://twitter.com/FortunaRoyal"><span class="fab fa-twitter"></span></a>
    </div>
    <br>
    <p>
      <h2>Canales oficiales de Fortuna Royal en Telegram</h2>
      ¡Únete a la conversación y mantente al día con los anuncios!
    </p>
    <div style"padding:15px;">
      <a target="_blank" href="https://t.me/fortunaroyal"><span class="fas fa-chart-line"></span></a>
    </div>

    <?php
    if(isset($_SESSION['user'])) {
    ?>
  <p>
    <h2 id="crearticket">Crear un Ticket de Soporte con Fortuna Royal</h2>
    ¡Aqui podras exponer mejoras, sugerencias, consultas!
  </p>
  <form style"padding:15px;" action="comunidad.php" method="post">
    <span>Motivo</span><br>
    <textarea name="motivo"></textarea>
    <br>
    <button type="submit" name="ticket">Crear Ticket</button>
  </form>
<p>
  <h2>Mis Ticket</h2>
  ¡Has Click en uno para ir al Chat!
</p>
<div style"padding:15px;">
  <table style='width: 100%; text-align:center; font-size:0.8em;'>
  <thead>
  <th>Historial</th>
  <th></th>
  </thead><tbody>
  <?php
  $color="";
  $bg="";
  $conexion = mysqli_connect(servidor(),user(),password());
  $db = mysqli_select_db( $conexion, database());
  $consulta = "select * from OPERACIONES where CLIENTE='".readVendedor($_SESSION['user'])['CORREO']."' AND TIPO='SOPORTE' order by fecha desc";
  $resultado = mysqli_query( $conexion, $consulta );
  while($row = mysqli_fetch_array($resultado)){
    if($row['ESTATUS']=='CANCELADO'){$bg="#F2D1C8";}
    if($row['ESTATUS']=='EN PROCESO'){$bg="#C8F0F2";}
    if($row['ESTATUS']=='COMPLETADO'){$bg="#B5F1A4";}
    if($row['ESTATUS']=='EN REVISION'){$bg="#F1EF63";}

    echo  "<tr>
    <td><div style='vertical-align:middle;padding:5px;background:".$bg.";'><span style='font-size:9px;color:black;font-weight: 600;'>".$row['ESTATUS']."</span></div></td>
    <td style='text-align:left;'>
      Orden: ".$row['TICKET']." <span style='font-weight: 600;'>".$row['TIPO']."</span>
       <a title='Click para ir al Chat directo.' style='font-size:14px;' href='chat.php?tk=".$row['TICKET']."'>".substr($row['REFERENCIA'], 0, 25)."</a> ";
      echo latinFecha($row['FECHA'])."";
      echo "</td></tr>";
  }
  mysqli_close($conexion);
}
?>
</tbody></table></div>
</div>
    <br><br>
</div>
</body>
</html>
