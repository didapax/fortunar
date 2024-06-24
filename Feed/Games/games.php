<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="./style.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <script>
    function trade(token){
      window.location.href="info.php?token="+token;
    }

    function myFunction(event){
      var x = event.key;
      if (x == "Enter" || x == "Intro"){
        window.location.href="games.php?filtro="+document.getElementById("filtro").value;}
    }

    function getPrices(){
      $.get("../../block?getPrices=",
      function(data){
        var datos= JSON.parse(data);
        for( x in datos){
          document.getElementById(datos[x]['name']).innerHTML=datos[x]['symbol']+datos[x]['price'] + "<span style='font-size:9px;color:#D7E2CD;'> FRD</span>";
          $("#"+datos[x]['name']).css("color",datos[x]['colorprice']);

          document.getElementById("P_"+datos[x]['name']).innerHTML=datos[x]['patrimonio'];
          $("#P_"+datos[x]['name']).css("color",datos[x]['colorcap']);

          document.getElementById("V_"+datos[x]['name']).innerHTML=datos[x]['volumen'];
          $("#V_"+datos[x]['name']).css("color",datos[x]['colorvol']);
        }
      });
    }

    function inicio(){
        myVar = setInterval(getPrices, 34000);
    }


  </script>
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
<body onload="inicio()">
<!-- partial:index.partial.html -->
<div style="padding: 3px; width:95%;">
<h3 class="apps-title" > Comercios  <input placeholder="Buscar" style="width:175px; margin-left:10px; padding:5px; border-radius:5px;" type="search" id="filtro" onkeyup='myFunction(event)'> <span style="font-size:18px;" class="icon icon-search"></span></h3>
  <div class="grid-container app-grid">
    <?php
		   $conexion = mysqli_connect(servidor(),user(),password(),database());
       if(!$conexion){
         echo "Refresh Page error to consulte in the Network...";
         exit();
       }
       else{
         $consulta = "select * from TOKEN WHERE BLOQUEADO <= 1 ORDER BY VOLUMEN DESC";
         if(isset($_GET['filtro'])){
           $consulta = "select * from TOKEN WHERE BLOQUEADO <= 1 AND TOKEN LIKE '%".$_GET['filtro']."%' ORDER BY VOLUMEN DESC";
         }
  		   $resultado = mysqli_query( $conexion, $consulta );
     		while($row = mysqli_fetch_array($resultado)){
        		echo "
            <div class='app' onclick=\"trade('".$row['TOKEN']."')\">
              <div class='app-image'>
                <img height='100px' src='../Book/comercios/".$row['IMAGEN']."' >
              </div>
              <div class='app-content'>
                <div class='app-title'><span style='font-weight:bold;color:white;'>".$row['TOKEN']."</span></div>
                <div  style='font-size:14px; text-transform:capitalize;'>
                  <span style='font-weight:bold; color:white;'>".$row['NOMBRE']."</span><br>
                  <span style='font-size:12px;'>Patrimonio </span>
                  <span id='P_{$row['TOKEN']}' style='font-size:12px;font-weight:bold;color:".colorCap($row['TOKEN']).";'>".redo($row['PATRIMONIO'])."</span><br>
                  <span style='font-size:12px;'>Participaciones </span>
                  <span id='V_{$row['TOKEN']}' style='font-size:12px;font-weight:bold;color:".colorVol($row['TOKEN']).";'>".redo($row['VOLUMEN'])."</span>
                </div>
                <div class='app-rating app-rating--".$row['RATE']."'></div>
                <div id='{$row['TOKEN']}' class='app-price' style='color:".colorPrecio($row['TOKEN']).";'>".getSymbol($row['TOKEN']).price($row['VALOR'])."
                  <span style='font-size:9px;color:#D7E2CD;'> FRD</span>
                </div>
              </div>
            </div>
            ";
  	  		}
  	  		mysqli_close($conexion);
       }
		?>
</div>
</body>
</html>
