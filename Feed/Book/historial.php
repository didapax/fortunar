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
  <link rel="stylesheet" href="./style2.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <script>
    function crear(token){
      window.location.href="token.php?token="+token;
    }
    function trade(token){
      window.location.href="../Games/info.php?token="+token;
    }
  </script>
  <style>
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
<body style="padding:5px;">
<!-- partial:index.partial.html -->
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title">Comercios Creados</h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">Comercios Creados</h3>
  </div>
  <li style="cursor:pointer;" onclick="window.location.href='soporte#revision'">Que significa que tu comercio este en <u>Revisión</u>.?</li>
  <li>Puedes Crear un Token BEP20 de tu Comercio en La "blockchain" Binance Smart Chain (BSC) Haciendo Click en Crear.</li>
  <div class="grid-container app-grid">
    <h3 class="apps-title"></h3>
    <?php
		   $conexion = mysqli_connect(servidor(),user(),password());
		   $db = mysqli_select_db( $conexion, database() );
		   $consulta = "select * from TOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' ORDER BY BLOQUEADO";
		   $resultado = mysqli_query( $conexion, $consulta );
   		while($row = mysqli_fetch_array($resultado)){
      		echo "
          <div class='app'>
            <div class='app-image' onclick=\"trade('".$row['TOKEN']."')\">
              <img height='100px' src='comercios/".$row['IMAGEN']."' >
            </div>
            <div class='app-content'>
              <div class='app-title'>".$row['TOKEN']."</div>
              <div class='app-publisher'>".$row['NOMBRE']." <br>Patrimonio ".redo($row['PATRIMONIO'])."<span style='font-size:9px;'> FRD</span>".
              "<br>Total Part ".redo($row['VOLUMEN'])."
               <br>Mis Part ".redo(read_wallet_token(readVendedor($_SESSION['user'])['CORREO'],$row['TOKEN'])['CANTIDAD'])."</div><br>";
              if($row['BLOQUEADO']==0){
                echo "<div class='app-rating app-rating--".$row['RATE']."'></div>
                <div class='app-price' style=' font-weight:bold; color:".colorPrecio($row['TOKEN']).";'>".price($row['VALOR'])."<span style='font-size:9px;'>FRD</span> ";
                if(ifBEP20(readVendedor($_SESSION['user'])['CORREO'],$row['TOKEN'])==0){
                    echo "<button type='button' id='crear' onclick=\"crear('".$row['TOKEN']."')\" style='cursor:pointer; margin-left:5px; text-decoration:none; padding: 3px; border: solid 1px white; border-radius:3px;'>Crear</button>";
                }
                echo "</div>";
              }
              else {
                echo "<span title='Tu Comercio esta en Revision Atento al correo y otros medios de Verificacion.' style='font-weight:bold; color:yellow;'>Comercio en Revision...!</span>";
              }

          echo "</div></div>";
	  		}
	  		mysqli_close($conexion);
		?>
    <br><br>
</div>
</body>
</html>
