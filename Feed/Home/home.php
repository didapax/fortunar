<?php
  include "../../modulo.php";
  date_default_timezone_set('America/Caracas');
  recalcTokens();
?>
<!DOCTYPE html>
<html lang="es" style="overflow-y:auto;">
<head>
  <meta charset="UTF-8">
  <title>Fortuna Royal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel="stylesheet" href="./style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
  <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
  <script src="../../modulo.js"></script>
  <script>
  var context= new AudioContext();

  function jsNota(frecuencia){
          var o= context.createOscillator();
          g=context.createGain();
          o.connect(g);
          o.type="sawtooth";
          o.frequency.value=frecuencia;
          g.connect(context.destination);
          o.start(0);
          g.gain.exponentialRampToValueAtTime(0.00001,context.currentTime +1.5);
  }
    function marcar(correo){
    	  $.post("../../modulo.php",
    	  {
    	    marcarNotif: "Donald",
    	    correo: correo,
    	    email: correo
    	  },
    	  function(data){
    	  	window.location.href="home.php";
    	  });
    }

    function vender(token,precio,cantidad,total){
      var txt;
    	var r = confirm("Esta Seguro de Convertir "+token+" a FRD?");
    	if (r == true) {
        $.post("../../modulo",{
          vender: "vender",
          token: token,
          vendedor: document.getElementById('correo').value,
          precio_venta: precio,
          cantidad: cantidad,
          ttotal: total
        },function(data) {
          window.location.href="home.php";
        });
    	} else {
    	  /*txt = "You pressed Cancel!";*/
    	}
    }

    function perfil(){
      window.location.href="perfil.php";
    }

    function cerrar() {
	    $.post("perfil.php",
      {
 	      cerrado: ""
      },
      function(data){
 		    window.location.href="home.php";
      });
    }    
  </script>
  <style>
    /* width */
    ::-webkit-scrollbar {
      width: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      background: #1B183E;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #1B183E;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #1B183E;
    }
  </style>
</head>

<body>
<!-- partial:index.partial.html -->
<div class="wrapper">
 <div class="main-container">
  <div class="header">
   <div class="user-info">
    <?php
    $link = "#dashboard";
    if(!isset($_SESSION['user']) ){
    ?>
    <div class="user-name"><a href="sesion.php" style="color:white;font-weight:bold;text-decoration: none;">Log In/Sign Up</a></div>
    <?php
    }
    else{
      $link = "perfil.php";
      ?>
      <div class="user-name"><a href="perfil.php" style="color:white;font-weight:bold;text-decoration: none;">Perfil</a> 
      <a onclick="cerrar()" style="color:white;font-weight:bold;text-decoration: none;margin-left:5px; border:1px solid white; cursor:pointer;background:darkgray; padding-left:1px;padding-right:1px;">Cerrar Sesion</a>
    </div>
      <?php
    }
    ?>
    <button class="button" style="--bgColorItem: #ff8c00;" >
        <a  href="<?php echo $link ?>" accesskey="1">
          <div>
            <h1 style="color:powderblue;
            text-align: center"</h1>
          <span  class="icon icon-user"></span>
        </div>
        </a>
    </button>
  </div>

  <div class="user-name"></div>
  <?php
  if(!isset($_SESSION['user']) ){
  ?>
  <button class="button" style="--bgColorItem: #ff8c00;" >
      <a href="#dashboard" accesskey="1">
        <div>
          <h1 style="color:powderblue;
          text-align: center"</h1>
        <span  class="icon icon-bell"></span>
      </div>
      </a>
  </button>
  <?php
  }
  else{
    ?>
    <li class="button">
      <label for='toggle' id='campana' onclick='notifread()'>
        <span class="icon icon-bell" style="color:powderblue;text-align: center;font-size:18px;">&#9742;</span>
      </label>
      <input type='checkbox' id='toggle'/>
      <div class='notification' style='background:transparent; overflow-y:auto;'>
        <ul id='notif'>
          <?php
          echo notif(readVendedor($_SESSION['user'])['CORREO']);
          ?>
        </ul>
      </div>
    </li>
    <?php
  }
  ?>

</div> <!-- end div header -->

   <div class="account-wrapper" style="--delay: .2s">

     <?php
     if(!isset($_SESSION['user']) ){
     ?>
     <div class="account-profile" style="width:800px; margin:0 auto; bottom:-200px; position: fixed; left: 50%; top: 55%; transform: translate(-50%, -50%);">
       <img src="fortuna_logo1.png" alt="" style="width:120px; height:120px; ">
       <div class="blob-wrap">
         <div class="blob"></div>
         <div class="blob"></div>
         <div class="blob"></div>
       </div>
      <div class="account-name" style="color:white;font-size:34px; font-weight:bold;">Fortuna Royal</div>
      <div class="account-title"></div>
     </div>
     <div class='account card' style='background: transparent; overflow-y: auto;width:80%; height: 45%; margin:0 auto; bottom:-100px; position: fixed; left: 50%; top: 65%; transform: translate(-50%, -50%);'>
      <h2 style='color:white;'>Compra y vende Participaciones de Comercios en minutos</h2>
      <span style='color:white;'> Una alternativa de inversión diversificada para emprendedores, Únete al mayor exchange de participaciones del mundo, conoce y sigue de cerca cada Proyecto de negocio.</span>
      <div style="height:20px;width:100%;"></div>
      <a style="width:200px; padding:10px; color:black; text-align:center; border:1px solid #FCD535; border-radius: 3px; background: #FCD535; font-weight:bold; cursor:pointer;" onclick="window.location.href='../Home/sesion.php'">Comenzar Ahora</a>
      <div style="height:20px;width:100%;"> </div>
      <h3>Compra y Vende fácilmente</h3>
      <p>
        <img style="margin:10px;" align="right" src="FRT_logo.png" width="50" height="50">
        Disfruta del exchange de participaciones en los comercios afiliados. Puedes operar con cientos de Comercios e interfaces muy facil de usar.
        invierten en numerosos instrumentos, lo que reduce el riesgo, los comercios estan constituidos por el aporte de diversas personas,
        diariamente se tienen un precio o valor liquidativo, obtenido por la división entre el patrimonio valorado y el número de participaciones en circulación.
      </p>
     </div>
     <?php
     }
     else{
       readPromo('inicio');
       if(countNotif(readVendedor($_SESSION['user'])['CORREO'])>0){echo "<script>jsNota(233.082);</script>";}
       ?>
       <input type="hidden" id="correo" value="<?php echo readVendedor($_SESSION['user'])['CORREO']; ?>">
       <div onclick="perfil()" class="account-profile" style="width:800px; margin:0 auto; bottom:-200px; position: fixed; left: 50%; top: 55%; transform: translate(-50%, -50%);">
         <img src="perfiles/<?php echo readVendedor($_SESSION['user'])['PERFIL']?>" alt="" style="width:120px; height:120px; ">
         <div class="blob-wrap">
           <div class="blob"></div>
           <div class="blob"></div>
           <div class="blob"></div>
         </div>
        <div class="account-name"><?php echo readVendedor($_SESSION['user'])['NOMBRE']?></div>

       </div>

       <div class='account card' style='background: transparent; overflow-y: auto;width:80%; height: 60%; margin:0 auto; bottom:-100px; position: fixed; left: 50%; top: 68%; transform: translate(-50%, -50%);'>
       <?php
       echo "<div onclick='' style='background: #15193C;  border:solid 1px;border-radius:5px; border-color:white; padding:5px;cursor:default;'>
         <div style=' text-transform:capitalize; display: flex; align-items: center;justify-content: justify;'>
         <img src='fdollar_balance.png' alt='' style='width:80px;'> Fortuna Royal Dolar
         </div>
         <div style='margin-top:13px;'>Balance</div>
         <div style='color:white;' >".miformat(readVendedor($_SESSION['user'])['SALDO'])."FRD</div>
         <div class='account-iban' style='padding:3px;color:#0ECB81;'>≈".miformat(readVendedor($_SESSION['user'])['SALDO'])."USDT</div>
       </div>";
       recalc_mis_wallet(readVendedor($_SESSION['user'])['CORREO']);
       $conexion = mysqli_connect(servidor(),user(),password());
 		   $db = mysqli_select_db( $conexion, database() );
 		   $consulta = "select * from WALLETOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."' AND BLOQUEADO=0 AND CANTIDAD>0";
 		   $resultado = mysqli_query( $conexion, $consulta );
    		while($row = mysqli_fetch_array($resultado)){
       		echo "
          <div onclick='' style='background: #15193c; margin-top:8px; border:solid 1px;border-radius:5px; border-color:white; padding:5px;cursor:default;'>
            <div style='text-transform:capitalize;display: flex; align-items: center;justify-content: justify;'>
              <img src='../Book/comercios/".readToken($row['TOKEN'])['IMAGEN']."' alt='' style='width:80px;'>
              ".readToken($row['TOKEN'])['NOMBRE']."<br>Price ".price($row['VALOR']).getSymbol($row['TOKEN'])."
            </div>
            <div style='margin-top:13px;'>Balance
            <label style='font-size:18px; margin-left:5px;' title='Convertir Saldo a FRD' onclick=\"vender('{$row['TOKEN']}',{$row['VALOR']},{$row['CANTIDAD']},{$row['TOTAL']})\">⇆</label></div>
            <div  style='color:white;'>".miformat($row['CANTIDAD'])."<a style='cursor:pointer; text-decoration:underline;' onclick=\"window.location.href='../Games/info.php?token=".$row['TOKEN']."'\">{$row['TOKEN']}</a></div>
            <div class='account-iban' style='padding:3px;color:".colorPrecio($row['TOKEN']).";'>≈ ".miformat($row['TOTAL'])."FRD</div>
          </div>
           ";
 	  		} 
 	  		mysqli_close($conexion);
        echo "<br>
             <span style=\"color:white;font-weight:bold; font-size:10px; \">Balance Estimado</span>
             <span style=\"color:white;font-weight:bold; font-size:12px; \">".balanceEstimado()."FRD ≈ ".balanceEstimado()."USDT</span>
             <br>
             <br>";
     }
     ?>
    </div>
   </div>
  </div>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script><script  src="./script.js"></script>
</body>
</html>
