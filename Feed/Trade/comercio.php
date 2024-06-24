<?php
include "block.php";
date_default_timezone_set('America/Caracas');

?>

<!DOCTYPE php>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="./style.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
  <style>

  #preloader {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: black;
    /* change if the mask should have another color then white */
    z-index: 99;
    display: none;
  /* makes sure it stays on top */
  }
  .charge_logo{
  display:flex;
  justify-content:center;
  align-items:center;
  }
  .charge_word{
  color:white;
  font-size:80px;
  margin:auto;
  top:50%;
  position:absolute;
  font-family: 'Bebas Neue', cursive;
  }
  .loader {
    border: 16px solid #D4D4D4;
    border-radius: 50%;
    border-top: 16px solid gray;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 2s linear infinite; /* Safari */
    animation: spin 2s linear infinite;
  }
  /* Safari */
  @-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* Safari */
  @-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  input[type=number]{
    font-size: 14px;
    padding: 8px;
    margin: 3px;
    font-weight: bold;
    border-radius: 3px;
  }

  .numeral{
    display:inline-block;
    border:1px solid black;
    border-radius:5px;
    background:#F0F1F2;
  }

  .numeral:hover{
    border:2px solid blue;
  }

  .numeral-caja{
    display:inline-block;
    width:41px;
    color:#9F97B9;
    font-weight:bold;
  }

  .numeral-number{
    background:#F0F1F2;
    text-align: right;
    width:75px;
    font-weight: bold;
    border:0;
    outline:0;
    border-radius: 3px;
    padding: 8px;
  }

  .numeral-button{
    font-weight: bold;
    text-decoration:none;
    font-size:12px;
    color: #9F97B9;
    cursor:pointer;
    background: transparent;
    border: solid 1px #F0F1F2;
    border-radius: 3px;
    padding: 3px;
    margin-right: 13px;
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
  	width:100px;
  	background: #0ECB81;
  	border: 0;
  	padding: 6px;
  	color:black;
  	cursor: pointer;
  	font-weight: bold;
    font-size: 14px;
  }

  .venta{
  	width:100px;
  	background: #F6465D;
  	border: 0;
  	padding: 6px;
  	color:black;
  	cursor: pointer;
  	font-weight: bold;
    font-size: 14px;
  }

  .buscar{
    width:100px;
  	background: #FDFDFD;
  	border: 2px solid #FDFDFD;
  	/*border-radius: 5px;*/
  	padding: 6px;
  	color:#EBD065;
  	cursor: pointer;
  	font-weight: bold;
    font-size: 14px;
  }

  .div2{
  	display: inline-block;
  	width: 100%;
  	height: 355px;
  	background: #110F25;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .div3{
  	display: inline-block;
  	width: 100%;
  	height: 250px;
  	background: #110F25;
    overflow-y: auto;
    overflow-x: hidden;
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

.boxer{
  display:none;
  position:absolute;
  margin:auto;
  z-index:1000;
  background:#D4DEE2;
  opacity: 0.8;
  border: 2px solid #C2CFD6;
  border-radius: 8px;
  width:344px;
  height:89px;
  top: 21px;
  right: 15px;
  float: right;
  color: black;
  font-weight: bold;
  font-size: 14px;
  text-align: center;
  padding: 5px;
}

  </style>
  <script>

  function buscar(){
    if(document.getElementById('fiat').value.length > 1){
      if(document.getElementById('comercio').value === "compra"){
        $.get("block?findComprar&monto="+document.getElementById('cantidad').value+"&fiat="+document.getElementById('fiat').value,
          function(data){
            $("#div3").html(data);
          });
      }
      else{
        $.get("block?findVender&monto="+document.getElementById('cantidad').value+"&fiat="+document.getElementById('fiat').value,
          function(data){
            $("#div3").html(data);
          });
      }
    }
    else {
      alert("Debe seleccionar una Moneda Fiat");
    }
  }


  	function inicio(){
  			myVar = setInterval(myTimer, 2000);
        /*getOrdenes('day');*/
        if(document.getElementById('comercio').value === "compra"){
          $("#vender").css("background","gray");
        }
        else{
          $("#comprar").css("background","gray");
        }
        document.getElementById("preloader").style.display='block'
        $('#preloader').fadeOut('slow');
    }


  	function myTimer() {
      $.get("block?findOrdenes&correo="+document.getElementById('correo').value,
        function(data){
          $("#div4").html(data);
        });
  	}

    function sivendo(id){
      $.get("block?getUserMetodo=&id="+id,
        function(data){
          var datos=data.split(',');
          document.getElementById('precioVenta').value = datos[5];
          document.getElementById('saldoFiat').value = datos[6];
          $("#ventaFiat").html(datos[2]);
        });
      var btvender = document.getElementById('venderButton');
      btvender.disabled = true;
      document.getElementById('idventa').value = id;
      document.getElementById('montoVenta').value="";
      document.getElementById('recibeVenta').value="";
      document.getElementById('dialogventa').show()
    }

    function sicompro(id){
      $.get("block?getUserMetodo=&id="+id,
        function(data){
          var datos=data.split(',');
          document.getElementById('precioCompra').value = datos[4];
          document.getElementById('saldoFRD').value = datos[7];
          $("#compraFiat").html(datos[2]);
        });
      var btcomprar = document.getElementById('comprarButton');
      btcomprar.disabled = true;
      document.getElementById('idcompra').value = id;
      document.getElementById('montoCompra').value="";
      document.getElementById('recibeCompra').value="";
      document.getElementById('dialogcompra').show()
    }

    function ver(id){
      window.location.href="conex.php?tk="+id;
    }

    function ejecutaVenta(){
      if((document.getElementById('montoVenta').value *1) >=1 ){
        $.post("block",{
          ejecutaVenta: "",
          anunciante: document.getElementById('idventa').value,
          correo: document.getElementById('correo').value,
          monto: document.getElementById('montoVenta').value,
          recibe: document.getElementById('recibeVenta').value

        },function(data){
          window.location.href="conex.php?tk="+data;
        });
      }
    }

    function ejecutaCompra(){
      if((document.getElementById('montoCompra').value *1) >=1 ){
        $.post("block",{
          ejecutaCompra: "",
          anunciante: document.getElementById('idcompra').value,
          correo: document.getElementById('correo').value,
          monto: document.getElementById('montoCompra').value,
          recibe: document.getElementById('recibeCompra').value

        },function(data){
          window.location.href="conex.php?tk="+data;
        });
      }
    }

    function revisaCompra(){
      var btcomprar = document.getElementById('comprarButton');
      if((document.getElementById('montoCompra').value *1) > 1 ){
        document.getElementById('recibeCompra').value = document.getElementById('montoCompra').value / document.getElementById('precioCompra').value;
        if( (document.getElementById('recibeCompra').value *1) <= (document.getElementById('saldoFRD').value *1) ){
          btcomprar.disabled = false;
        }
        else{
          btcomprar.disabled = true;
        }
      }
      else {
          //..
      }
    }

    function revisaVenta(){
      var btvender = document.getElementById('venderButton');
      if((document.getElementById('montoVenta').value *1) > (document.getElementById('saldo').value *1)){
        document.getElementById('montoVenta').title="Monto Errado Saldo no Disponible";
        document.getElementById('montoVenta').value = "";
        document.getElementById('recibeVenta').value = "";
        btvender.disabled = true;
      }
      else {
        document.getElementById('recibeVenta').value = document.getElementById('montoVenta').value * document.getElementById('precioVenta').value;

        if( (document.getElementById('recibeVenta').value *1) <= (document.getElementById('saldoFiat').value *1) ){
          btvender.disabled = false;
        }
        else{
          btvender.disabled = true;
        }
      }
    }

  </script>
</head>
<body onload="inicio()">
  <div id='preloader'>
    <div class='charge_logo'>
      <div class='charge_word'>
        <div class='loader'></div>
      </div>
    </div>
  </div>
<!-- partial:index.partial.html -->
<div class="boxer" id="boxe">
<div style="float: right; padding:5px; text-align:center;font-weight:bold;">x</div>
<div style="height:10px;width:100%;"></div>
<div id="boxe-msg"></div>
</div>
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title" style="font-weight:bold;">Fortuna Royal
    <span style="color: white;font-size:16px; font-weight:bold;"> Comercio P2P<hr>
      <div style="margin-left: 10px; display:inline; cursor:pointer;" onclick="window.location.href='trade.php?token=frt'"> Market</div>
    </span></h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">Fortuna Royal
<span style="color: white;font-size:16px;font-weight:bold;"> Comercio P2P<hr>
  <div style="margin-left: 10px;display:inline; cursor:pointer;" onclick="window.location.href='trade.php?token=frt'"> Market</div>
</span></h3>

  </div>
      <div style="padding:15px;">
    <?php
    if(!isset($_SESSION['user']) ){
    ?>
    <script type='text/javascript'>
      function redirect() {
            window.location.href = 'sesion.php?token=FRT';
        }
        window.onload = redirect();
    </script>
    <?php
    }
    else{
      readPromo('p2p');
      ?>

      <input type='hidden' id='comercio' value='<?php echo $_GET['comercio'] ?>' readonly>
      <input type="hidden" id="correo" value="<?php echo readVendedor($_SESSION['user'])['CORREO']?>">
      <input type="hidden" id="saldo" value="<?php echo readVendedor($_SESSION['user'])['SALDO']?>">

      <button type="button" onclick="window.location.href='comercio.php?comercio=compra'" class="compra" id="comprar">Comprar</button>
      <button type="button" onclick="window.location.href='comercio.php?comercio=venta'" class="venta" id="vender">Vender</button>
      <span style="margin-left:13px; text-decoration:underline; color:white; font-weight:bold; cursor:pointer;">FRD</span>
      <div style="background:#FDFDFD;">
        <input style="padding:8px;width:75px;display:inline;" type="text" id="fiat" placeholder="Fiat" list="listfiat">
        <datalist id="listfiat">
          <?php
      		   $conexion = mysqli_connect(servidor(),user(),password());
      		   $db = mysqli_select_db( $conexion, database());
      			 $consulta = "select * from METODOS WHERE BLOQUEADO=0 ORDER BY METODO";
      			 $resultado = mysqli_query( $conexion, $consulta );
      		   while($row = mysqli_fetch_array($resultado)){
      			  echo "<option label='".$row['METODO']."' value='".$row['METODO']."'>";
      		   }
      			mysqli_close($conexion);
      		?>
        </datalist>
        <input step="0.01" type="number" id="cantidad" placeholder="Cantidad" min="1" style="width:150px;" ><button type="button" class="buscar" onclick="buscar()">Buscar</button>
      </div>
      <dialog class="dialog_transf" id="dialogcompra" close>
        <input type="hidden" value="" id="saldoFRD">
        <input type="hidden" value="" id="precioCompra">
        <input type="hidden" value="" id="idcompra">
        <a style="font-weight: bold;float:right;cursor:pointer;" onclick="document.getElementById('dialogcompra').close()">X</a><br>
        <span>Quiero pagar</span> <div style="display:inline;" id="compraFiat"></div>
        <input type="number" value="" id="montoCompra" min="1" onkeyup="revisaCompra()" onchange="revisaCompra()">
        <br><span>Recibiré FRD</span>
        <input type="number" value="" id="recibeCompra" readonly><br>
        <input type="button"  id="comprarButton" onclick="ejecutaCompra()" value="Comprar">
      </dialog>

      <dialog class="dialog_transf" id="dialogventa" close>
        <input type="hidden" value="" id="saldoFiat">
        <input type="hidden" value="" id="precioVenta">
        <input type="hidden" value="" id="idventa">
        <a style="font-weight: bold;float:right;cursor:pointer;" onclick="document.getElementById('dialogventa').close()">X</a><br>
        <span>Quiero vender FRD</span>
        <input type="number" value="" id="montoVenta" min="1" onkeyup="revisaVenta()" onchange="revisaVenta()">
        <br><span>Recibiré <div style="display:inline;" id="ventaFiat"></div></span>
        <input type="number" value="" id="recibeVenta" readonly><br>
        <input type="button" onclick="ejecutaVenta()" value="Vender" id="venderButton">
      </dialog>

      <div class="div2" id="div3"></div>
      <div class="div3" style="background:#263238;" id="div4"></div>
  <?php

}
?>
</div>
</body>
</html>
