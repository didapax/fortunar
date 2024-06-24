<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');
recalcTokens();
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel="stylesheet" href="./style.css">
<link rel="stylesheet" href="./style2.css">
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
</head>
<body>
<!-- partial:index.partial.html -->

 <div class="main-container">
  <div class="header">

    <div>
<img src="dark_bar.png" style="width: fixed; position: fixed; left: 0%; top: 0%; width: 200%; height: 10%;">
  </div>

    <div style="margin:20px;">
      <?php
      if(!isset($_SESSION['user'])) {
      ?>
      <img src="fortuna_logo1.png" style="width: fixed; position: fixed; left: 47.8%; top: 1.3%; width: 60px; height: 60px;border-radius: 50%;">
      <?php
      }
      else{
        if(readVendedor($_SESSION['user'])['ACTIVO']=='1'){
        ?>
        <h3 class="apps-title" >
          <?php
          if(readVendedor($_SESSION['user'])['SALDO'] >= 40){
            echo "<a href='add.php'>Crear Comercio</a>";
          }
          else{
            echo "
              <a  onclick=\"document.getElementById('d').show();\" style='cursor:pointer;color:#BABDB6;'>Crear Comercio</a>
              <dialog onclick=\"document.getElementById('d').close()\" id='d' close>
                <span style='font-size:18px;'>Recargue un saldo minimo de 40 F$..!</span>
              </dialog>
            ";
          }
          ?>
          <a href="historial.php">Cartera</a>
          <a href="creditos.php">Créditos</a></h3>
        <?php
        }
        else echo "<img src=\"fortuna_logo1.png\" style=\"width: fixed; position: fixed; left: 47.8%; top: 1.3%; width: 60px; height: 60px;border-radius: 50%;\">";
      }
      ?>
  </div>

   </div>
  </div>
  <div class="user-box first-box" onclick="document.getElementById('d').close()">
   <div class="activity card" style="--delay: .2s">
    <div class="title" style="font-size:21px;color:white;">Acerca de Fortuna Royal </div>
    <div class="subtitle" style="color:white;">Nuestra visión es dar Oportunidad a los emprendedores y la libertad de Invertir en Proyectos Comerciales.
      Creemos que al difundir esta libertad, podemos contribuir al crecimiento económico personal
      dando la oportunidad de crear comercios a través de fondos con participaciones colectivas,
      gestionados profesionalmente.</div>
      <div style="height:15px;"></div>
    <div class="activity-links" style="color:#FFE797;">
     <div class="activity-link notify" style="font-size:18px;" onclick="window.location.href='comunidad.php';">Comunidad</div>
     <div class="activity-link notify" style="font-size:18px;" onclick="window.location.href='soporte.php';">Soporte</div>
     <div class="activity-link notify" style="font-size:18px;" onclick="window.location.href='condiciones.php';">Términos</div>
    </div>
   </div>

   <div class="card transection" style="--delay: .4s;">
     <p style="color:white;">
     <img src="redes.png" align="right" width="80"> Crear tus Grupos Sociales y comparte tu idea de Negocio, demuestra a tus inversionistas el progreso y participen juntos
   </p>
   </div>
   <div class="card transection" style="--delay: .4s;">
     <p style="color:white;">
     <img src="creditos.png" align="right" width="100"> Accede a Creditos para tu negocio dependiendo de tu crecimiento en el volumen de participaciones, son creditos definidos y con un plazo de 1 mes con bajos intereses
   </p>
   </div>
  </div>
  <br><br><br>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script><script  src="./script.js"></script>

</body>
</html>
