<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');
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
}
  </style>
</head>
<body style="padding:15px;">
<!-- partial:index.partial.html -->
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title">Crear un Token BEP20 en La "blockchain" Binance Smart Chain (BSC)</h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">Crear un Token BEP20 en La "blockchain" Binance Smart Chain (BSC)</h3>
  </div>
  <div style="  width: 90%; text-align: justify; color:white;">
    <p>
      Comercio <?php echo $_GET['token']; ?>
      <h3>Estamos Trabajando para poner en funcionamiento lo mas pronto posible este segmento, gracias por tu paciencia.</h3>
    </p>
    <br><br>
</div>
</body>
</html>
