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
  <link rel="stylesheet" href="./style.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
  <style>

  input[type=number]{
    font-size: 9px;
    font-weight: bold;
  }

  .table1 {
     /*width: 100%;*/
     border: 0px;
     /*text-align: left;*/
     border-collapse: collapse;
     /*margin: 0 0 1em 0;
     caption-side: top;*/
  }

  .table1 td {
     border: 0px;
  	 background: #2A2525;
  }

  .table1 th {
     border: 0px;
  	 background: #2A2525;
  	 text-align: center;
  }

  .table2 {
     width: 100%;
     border: 1px solid #999;
     text-align: center;
     border-collapse: collapse;
     margin: 0 0 1em 0;
     caption-side: top;
  }
  .table2 caption, td, th {
     padding: 0.3em;
  }
  .table2 th, td {
     border-bottom: 1px solid #999;
     width: 25%;
     background: transparent;
     font-size: 11px;
  }
  .table2 caption {
     font-weight: bold;
     font-style: italic;
  }

  .compra{
  	width:96%;
  	background: #0ECB81;
  	border: 2px solid #0ECB81;
  	border-radius: 5px;
  	padding: 13px;
  	color:black;
  	cursor: pointer;
  	font-weight: bold;
    font-size: 18px;
  }

  .venta{
  	width:96%;
  	background: #F6465D;
  	border: 2px solid #F6465D;
  	border-radius: 5px;
  	padding: 13px;
  	color:black;
  	cursor: pointer;
  	font-weight: bold;
    font-size: 18px;
  }

  .btncompra{
  	width:48%;
  	background: #0ECB81;
  	border: 2px solid #0ECB81;
  	border-radius: 3px;
  	padding: 8px;
  	color:white;
  	cursor: pointer;
  	font-weight: 600;
  }

  .btnventa{
  	width:48%;
  	margin-left: 5px;
  	background: lightgray;
  	border: 2px solid #F6465D;
  	border-radius: 3px;
  	padding: 8px;
  	color:white;
  	cursor: pointer;
  	font-weight: 600;
  }

  .divPrecio{
  	display: block;
  	width: 100%;
  	height: 50px;
  	background: #263238;
  	margin-bottom: 2px;
  }

  .div1{
  	display: inline-block;
  	width: 70%;
  	height: 500px;
  	background: #263238;
  	overflow-y: scroll;
  	overflow-x: hidden;
  }

  .div2{
  	display: inline-block;
  	width: 29%;
  	height: 500px;
  	background:#263238;
  }

  .div2dentro1{
  	display: inline-block;
  	width: 48%;
  	height: 250px;
  	background: #263238;
  	overflow-x: hidden;
  	overflow-y: scroll;
    font-size: 11px;
  }

  .div2dentro2{
  	display: block;
  	width: 100%;
  	height: 200px;
  	background:#263238;
  	overflow-x: hidden;
  	overflow-y: scroll;
  }

  .div3{
  	display: inline-block;
  	width: 100%;
  	height: 250px;
  	background: #263238;
  	overflow: scroll;
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
  <script>

  function actSaldotoken(correo,token){

  }

  function carga_precio(precio){
  	document.getElementById('Dprecio').value=precio;
  	document.getElementById('Dtotal').value=0;
  	document.getElementById('cantidad').value=0;
    document.getElementById('Dprecio_venta').value=precio;
  	document.getElementById('Dtotal_venta').value=0;
  	document.getElementById('cantidad_venta').value=0;
  }

  function calc_precio(){
   	document.getElementById('Dtotal').value=0;
  	document.getElementById('cantidad').value=0;
  }

  function calc_token(){
   	document.getElementById('Dtotal').value = (document.getElementById('Dprecio').value * document.getElementById('cantidad').value).toFixed(2);
  }

  function calc_cash(){
  		document.getElementById('cantidad').value=document.getElementById('Dtotal').value / document.getElementById('Dprecio').value;
  }

  function calc_precio_venta(){
    document.getElementById('Dtotal_venta').value=0;
    document.getElementById('cantidad_venta').value=0;
  }

  function calc_token_venta(){
    document.getElementById('Dtotal_venta').value = (document.getElementById('Dprecio_venta').value * document.getElementById('cantidad_venta').value).toFixed(2);
  }

  function calc_cash_venta(){
      document.getElementById('cantidad_venta').value=document.getElementById('Dtotal_venta').value / document.getElementById('Dprecio_venta').value;
  }


  	function inicio(){
  			myVar = setInterval(myTimer, 2000);
  			/*$("#div1").load(document.getElementById('token').value.toLowerCase()+".html");*/
  	}

  	function myTimer() {

  		$.get("../../modulo?valortoken="+document.getElementById('token').value,
  			function(data){
  				var valor_token=0;
  				if (data<1) {
  					valor_token = data;
  				}else {
  					valor_token= data.toFixed(2)
  				}
  				document.getElementById('valor1').value=valor_token;
  				$("#valor2").html(valor_token);
  				/*$("#valor3").html(valor_token);
  				$("#valor4").html(valor_token);*/
  			});

  		actSaldotoken(document.getElementById('correo').value,document.getElementById('token').value);
  	}

    function movCode() {
      window.location.href="sesion?token="+document.getElementById('codigo').value;
  	}

    function iniciar_sesion() {
      window.location.href="../Home/sesion.php";
  	}

  </script>
</head>
<body onload="inicio()">
<!-- partial:index.partial.html -->
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title">Fortuna Royal <sub style="font-size:13px;"> Market</sub></h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">Fortuna Royal <sub style="font-size:13px;"> Market</sub></h3>
  </div>
    <h3 class="apps-title"></h3>
      <div style="padding:15px;">
    <?php
    if(!isset($_SESSION['user']) ){
    ?>
    <div>
    <div class="divPrecio">
      <img style="background:#263238; vertical-align: middle;" width="34" height="34" src="../Book/comercios/<?php echo readToken($_GET['token'])['IMAGEN']; ?>" >
      <strong style="background:#263238;color: white; font-size: 18px; font-weight: bold;"> <?php echo readToken($_GET['token'])['TOKEN'] ?> / FRD</strong>
      <div style="background:#263238;margin-left: 21px; display:inline-block; color: <?php echo colorPrecio($_GET['token'])?>; font-size:18px " id="valor2"><?php echo readToken($_GET['token'])['VALOR'] ?></div>
      <input placeholder="Buscar" type="search" id="codigo" name="codigo" list="code" onchange="movCode()" style="border:0; border-radius:3px;  width:150px;">
      <datalist id="code" >
      <?php
        $conexion = mysqli_connect(servidor(),user(),password(),database());
        $consulta = "select * from TOKEN WHERE BLOQUEADO=0 ORDER BY TOKEN";
        $resultado = mysqli_query( $conexion, $consulta );
         while($row = mysqli_fetch_array($resultado)){
          echo "<option label='".$row['TOKEN']."' value='".$row['TOKEN']."'>";
         }
        mysqli_close($conexion);
      ?>
      </datalist>
    </div>
    <div class="div2dentro1"><!--COMPRAS-->
      <br>
      <br>
      <div style="width:100%; background:#263238; color: white; font-size: 13px; display: inline-block;" id="textbox">Disp</div>
      <div style="height:5px;"></div>
      <div style="display:inline-block;border:1px solid black; border-radius:5px;background:#F0F1F2;">
      <div style="display:inline-block; width:41px; color:#9F97B9;font-weight:bold;">Precio</div>
      <input onkeyup="calc_precio()" onchange="calc_precio()"name="Dprecio" id="Dprecio" style="background:#F0F1F2; text-align: right; width:60px; font-weight: bold;border:0; border-radius: 3px; padding: 8px; " type="number" step="0.0001" readonly value="<?php echo readToken($_GET['token'])['VALOR']?>" min="0.0001">
      <div style="display:inline-block; width:41px; color:#9F97B9;background:#F0F1F2;font-weight:bold;">FRD</div>
      </div><div style="height:5px;"></div>

      <div style="display:inline-block;border:1px solid black; border-radius:5px;background:#F0F1F2;">
      <div style="display:inline-block; width:41px; color:#9F97B9;font-weight:bold;">Cantidad</div>
      <input onkeyup="calc_token()" onchange="calc_token()" name="cantidad" id="cantidad" style="background:#F0F1F2;text-align: right;width:60px; font-weight: bold;border:0; border-radius: 3px; padding: 8px;" type="number" step="1" value="0" min="1">
      <div style="display:inline-block; width:41px; color:#9F97B9;background:#F0F1F2;font-weight:bold;"><?php echo readToken($_GET['token'])['TOKEN'] ?></div>
      </div><div style="height:5px;"></div>

      <div style="display:inline-block;border:1px solid black; border-radius:5px; background:#F0F1F2;">
      <div style="display:inline-block; width:41px; color:#9F97B9; font-weight:bold;">Total</div>
      <input onkeyup="calc_cash()" onchange="calc_cash()" name="Dtotal" id="Dtotal" style="background:#F0F1F2;text-align: right;width:60px; font-weight: bold;border:0; border-radius: 3px; padding: 8px; " type="number" value="0">
      <div style="display:inline-block; width:41px; color:#9F97B9;background:#F0F1F2;font-weight:bold;">FRD</div>
      </div>

      <br><br>
      <button type="button" id="compra" class="compra" onclick="iniciar_sesion();">Iniciar Sesion</button>
    </div>

    <div class="div2dentro1"><!--VENTAS-->
      <br>
      <br>

      <div id="saltoken" style="width:100%; color: white;font-size: 13px; background: #263238; ">Disp</div>
      <div style="height:5px;"></div>
      <div style="display:inline-block;border:1px solid black; border-radius:5px;background:#F0F1F2;">
      <div style="display:inline-block; width:41px; color:#9F97B9;font-weight:bold;">Precio</div>
      <input onkeyup="calc_precio_venta()" onchange="calc_precio_venta()" name="Dprecio_venta" id="Dprecio_venta" style="background:#F0F1F2; text-align: right; width:60px; font-weight: bold;border:0; border-radius: 3px; padding: 8px; " type="number" step="0.0001" readonly value="<?php echo readToken($_GET['token'])['VALOR']?>" min="0.0001">
      <div style="display:inline-block; width:41px; color:#9F97B9;background:#F0F1F2;font-weight:bold;">FRD</div>
      </div><div style="height:5px;"></div>

      <div style="display:inline-block;border:1px solid black; border-radius:5px;background:#F0F1F2;">
      <div style="display:inline-block; width:41px; color:#9F97B9;font-weight:bold;">Cantidad</div>
      <input onkeyup="calc_token_venta()" onchange="calc_token_venta()" name="cantidad_venta" id="cantidad_venta" style="background:#F0F1F2;text-align: right;width:60px; font-weight: bold;border:0; border-radius: 3px; padding: 8px;" type="number" step="1" value="0" min="1">
      <div style="display:inline-block; width:41px; color:#9F97B9;background:#F0F1F2;font-weight:bold;"><?php echo readToken($_GET['token'])['TOKEN'] ?></div>
      </div><div style="height:5px;"></div>

      <div style="display:inline-block;border:1px solid black; border-radius:5px; background:#F0F1F2;">
      <div style="display:inline-block; width:41px; color:#9F97B9;font-weight:bold;">Total</div>
      <input onkeyup="calc_cash_venta()" onchange="calc_cash_venta()" name="Dtotal_venta" id="Dtotal_venta" style="background:#F0F1F2;text-align: right;width:60px; font-weight: bold;border:0; border-radius: 3px; padding: 8px; " type="number" value="0">
      <div style="display:inline-block; width:41px; color:#9F97B9;background:#F0F1F2;font-weight:bold;">FRD</div>
      </div>
      <br><br>
      <button type="button" id="venta" class="venta" onclick="iniciar_sesion();">Iniciar Sesion</button>
    </div>

  <div class="div3" id="div3">

  </div>

    <?php
    }
    else{
      ?>

      <?php
    }
    ?>
    <br><br>
</div>
</body>
</html>
