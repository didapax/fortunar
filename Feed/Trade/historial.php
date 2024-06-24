<?php
  include "../../modulo.php";
  date_default_timezone_set('America/Caracas');
?>
<!DOCTYPE html>
<html lang="en" style="overflow-y:auto;" >
<head>
  <meta charset="UTF-8">
  <title>Fortuna Royal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
  <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet' href='https://netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.css'>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <script>
  function getOrdenes(tiempo){
    $.get("../../modulo?historialmov&email="+document.getElementById('correo').value+"&filtro="+tiempo,
      function(data){
        $("#div3").html(data);
      });
  }
  </script>

  <style>
    body{
      background: #263238;
    }

    table{
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

    table{
      font-size: 13px;
    }
    table tr td{
      padding: 5px;
    }
  </style>
</head>

<body onload="getOrdenes('day')">

  <?php
  if(!isset($_SESSION['user']) ){
  ?>

  <?php
  }
  else{
  ?>
    <input type="hidden" id="correo" value="<?php echo readVendedor($_SESSION['user'])['CORREO']?>">
      <div style="padding:21px; color:white;" id="historial">Historial<hr>
        <a onclick="getOrdenes('day')" style="font-size:13px; padding: 3px; cursor:pointer; color:white;font-weight:bold;">Dia</a>
        <a onclick="getOrdenes('month')" style="font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Mes</a>
        <a onclick="getOrdenes('all')" style="font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Todo</a>
      </div>
      <div style="padding:13px;" id="div3"></div>
  <?php
  }
  ?>
</body>
</html>
