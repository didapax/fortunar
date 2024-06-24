<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');
recalcTokens();
if(isset($_SESSION['user']) && strlen($_GET['token'])>0){
  if(!if_wallet_exist(readVendedor($_SESSION['user'])['CORREO'],$_GET['token'])) {
    create_wallet(readVendedor($_SESSION['user'])['CORREO'],$_GET['token']);
  }
}
?> 

<!DOCTYPE php>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="./style.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
  <link href="c3.css" rel="stylesheet">
  <script src="c3.js"></script>
  <script src="https://d3js.org/d3.v5.min.js"></script>

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
      font-size: 11px;
    }

    .numeral{
      display:inline-block;
      border:1px solid black;
      border-radius:5px;
      background:#F0F1F2;
      width: 95%;
    }

    .numeral:hover{
      border:2px solid blue;
    }

    .numeral-caja{
      display:inline-block;
      width:auto;
      color:#9F97B9;
      font-weight:bold;
      text-align: left;
    }

    .numeral-number{
      background:#F0F1F2;
      text-align: right;
      width:50%;
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
      padding: 3px;
      margin-right: 8px;
      width:21%;
      display: inline-block;
    }

    .table1 {
      border: 0px;
      border-collapse: collapse;
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
      height: 77px;
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

    .divcontainer{
      display: inline-block;
      width: 48%;
      height: 270px;
      background:  #263238;
      overflow-x: hidden;
      overflow-y: scroll;
      font-size: 11px;
      text-align: center;
    }

    .div2dentro1{
      display: inline-block;
      width: 48%;
      height: 270px;
      background:  #263238;
      overflow-x: hidden;
      overflow-y: scroll;
      font-size: 11px;
      text-align: center;
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

    @media (max-width: 600px) {
      .divcontainer{
      display: inline-block;
      width: 99%;
      height: 270px;
      background:  #263238;
      overflow-x: hidden;
      overflow-y: scroll;
      font-size: 11px;
      text-align: center;
    }
    }    
  </style>
  <script>
  function grafico(){
      $.get("../../modulo?librordenes&token="+document.getElementById('token').value,
        function(data){
          var datos= JSON.parse(data)
          var chart = c3.generate({
              data: {
                  columns: [
                      datos[0],
                      datos[1]
                  ],
                  colors: {
                    Compras: '#EF2929',
                    Ventas: '#73D216'
                  },
                  types: {
                      Compras: 'area-spline',
                      Ventas: 'area-spline'
                      // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
                  },
                  /*groups: [['Compras', 'Ventas']]*/
              }
          });
        });
    }

  function price(price){
    if((price * 1) >= 1) {
      return price.toFixed(2);
    }
    else{
      return price.toFixed(4);
    }
  }

  function actSaldotoken(Correo,Token){ 
    $.post("block",{
      datatrader: "",
      correo: Correo,
      token: Token
    },function(data){
      var datos= JSON.parse(data);      
      $('#saltoken').html("<span style='color:#9F97B9;'>Disp</span> "+ price(datos.saldotoken *1) + " "+datos.token);
  	  document.getElementById("saldotoken").value = price(datos.saldotoken *1);

  	  $('#textbox').html("<span style='color:#9F97B9;'>Disp</span> "+ price(datos.saldofrd *1) +" FRD");
  	  document.getElementById("saldo").value = price(datos.saldofrd *1); 

  	  $('#valor2').html(price(datos.preciotoken *1));
      $('#newPatrimonio').html((datos.patrimonio *1).toFixed(0) );
      $('#newVolumen').html((datos.volumen *1).toFixed(0));
      $('#newCreci').html((datos.creci *1).toFixed(0));
      document.getElementById('valor1').value = price(datos.preciotoken *1);
  	  document.getElementById("Dprecio").value = price(datos.preciotoken *1);
      document.getElementById("Dprecio_venta").value = price(datos.preciotoken *1);

      document.getElementById("tope").value=datos.saldotope;
      
      $("#valor2").css("color",datos.colorprecio);
      $("#newPatrimonio").css("color",datos.colorcap);
      $("#newVolumen").css("color",datos.colorvolumen);
      $("#newCreci").css("color",datos.colorcreci);
    });
  }

  function carga_precio(precio){
  	document.getElementById('Dprecio').value=precio;
  	document.getElementById('Dtotal').value=0;
  	document.getElementById('cantidad').value=0;
    document.getElementById('Dprecio_venta').value=precio;
  	document.getElementById('Dtotal_venta').value=0;
  	document.getElementById('cantidad_venta').value=0;
  }

  function carga(){
  	document.getElementById('Dtotal').value=0;
  	document.getElementById('cantidad').value=0;
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
  		document.getElementById('cantidad').value = (document.getElementById('Dtotal').value / document.getElementById('Dprecio').value).toFixed(4);
  }

  function calc_precio_venta(){
    document.getElementById('Dtotal_venta').value=0;
    document.getElementById('cantidad_venta').value=0;
  }

  function calc_token_venta(){
    document.getElementById('Dtotal_venta').value = (document.getElementById('Dprecio_venta').value * document.getElementById('cantidad_venta').value).toFixed(2);
  }

  function calc_cash_venta(){
      document.getElementById('cantidad_venta').value = (document.getElementById('Dtotal_venta').value / document.getElementById('Dprecio_venta').value).toFixed(4);
  }

  function max_frt(){
    var total = document.getElementById('saldotoken').value;
  	document.getElementById('cantidad_venta').value = total;
  	calc_token_venta();
  }

  function max_frt_50(){
    var total = document.getElementById('saldotoken').value;
    document.getElementById('cantidad_venta').value = (total*0.5).toFixed(4);
    calc_token_venta();
  }

  function max_frt_25(){
    var total = document.getElementById('saldotoken').value;
    document.getElementById('cantidad_venta').value = (total*0.25).toFixed(4);
    calc_token_venta();
  }

  function max_f(){
  	document.getElementById('Dtotal').value=document.getElementById('saldo').value;
  	calc_cash();
  }

  function max_f_50(){
    document.getElementById('Dtotal').value = document.getElementById('saldo').value*0.5;
    calc_cash();
  }

  function max_f_25(){
  	document.getElementById('Dtotal').value = document.getElementById('saldo').value*0.25;
  	calc_cash();
  }

  	function comprar() {
  		var total_compra = document.getElementById('Dtotal').value;

  		if (total_compra > (document.getElementById('saldo').value *1) ) {
  			alert("Saldo FRD Insuficiente Recargue.!");
  		} else {
        if(total_compra < 10){
          alert("La Compra debe ser mayor a 10 FRD")
        }
        else{
          $.post("../../modulo",{
    				comprar: "comprar",
    				token: document.getElementById('token').value,
    				comprador: document.getElementById('correo').value,
    				precio_compra: document.getElementById('Dprecio').value,
    				cantidad: document.getElementById('cantidad').value,
    				ttotal: total_compra
    			},function(data) {
            actSaldotoken(document.getElementById('correo').value,document.getElementById('token').value);
            carga();
            getOrdenes('day');
            $("#boxe-msg").html("Compra Realizada con Exito...!");
            $("#boxe").fadeIn(1000);
    			});
        }
  		}
  	}

  	function vender() {
      var total_venta = document.getElementById('Dtotal_venta').value;

      if((document.getElementById('deudor').value *1) == 0){
        if( (document.getElementById('Dtotal_venta').value *1) < (document.getElementById('tope').value *1) ){
          if ((document.getElementById('cantidad_venta').value *1) > (document.getElementById('saldotoken').value *1) ) {
      			alert("Su Saldo en " + document.getElementById('token').value + " es Insuficiente, Recargue..!");
      		} else {
            if((document.getElementById('Dtotal_venta').value *1) < 10){
              alert("La Venta Debe Ser Mayor a 10 FRD")
            }
            else{
              $.post("../../modulo",{
        				vender: "vender",
        				token: document.getElementById('token').value,
        				vendedor: document.getElementById('correo').value,
        				precio_venta: document.getElementById('Dprecio_venta').value,
        				cantidad: document.getElementById('cantidad_venta').value,
        				ttotal: total_venta
        			},function(data) {
                actSaldotoken(document.getElementById('correo').value,document.getElementById('token').value);
                carga();
                getOrdenes('day');
                $("#boxe-msg").html("Venta Realizada con Exito...!");
                $("#boxe").fadeIn(1000);
        			});
            }
      		}
        }
        else {
          alert("Por los Momentos No hay Liquidez en el Fondo, intente mas tarde..!");
        }
      }
      else{
        alert("Tiene Creditos Pendiente por Pagar.");
      }
  	}

  	function inicio(){
  			myVar = setInterval(myTimer, 1000);
        myVarGrafica = setInterval(myTimerGrafica, 34000);
        grafico();
        getOrdenes('day');
        document.getElementById("preloader").style.display='block';
        $('#preloader').fadeOut('slow');
    }

    function getOrdenes(tiempo){
      $.get("../../modulo?ordenes&email="+document.getElementById('correo').value+"&filtro="+tiempo+"&token="+document.getElementById('token').value,
  			function(data){
  				$("#div3").html(data);
  			});
    }

  	function myTimer() {
      $("#boxe").fadeOut(3000);
  		actSaldotoken(document.getElementById('correo').value,document.getElementById('token').value);      
  	}

  	function myTimerGrafica() {
      grafico();
  	}    

    function movCode() {
      window.location.href="trade?token="+document.getElementById('codigo').value;
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
      readPromo('trader');
      ?>
<!-- partial:index.partial.html -->
<div class="boxer" id="boxe">
<div style="float: right; padding:5px; text-align:center;font-weight:bold;">x</div>
<div style="height:10px;width:100%;"></div>
<div id="boxe-msg"></div>
</div>
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title" style="font-weight:bold;">Fortuna Royal
    <span style="color: white;font-size:16px; font-weight:bold;"> Market<hr>
      <div style="display:inline; cursor:pointer;" onclick="window.location.href='comercio.php?comercio=compra'"> Comercio P2P</div>
      <div style="margin-left: 10px; display:inline; cursor:pointer;" onclick="window.location.href='historial'"> Historial</div>
    </span></h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">Fortuna Royal
<span style="color: white;font-size:16px;font-weight:bold;"> Market<hr>
  <div style="display:inline; cursor:pointer;" onclick="window.location.href='comercio.php?comercio=compra'"> Comercio P2P</div>
  <div style="margin-left: 10px; display:inline; cursor:pointer;" onclick="window.location.href='historial'"> Historial</div>
</span></h3>
  </div>
  <div style="width:100%;height:21px;"></div>
      <div style="padding:15px;">      
      <div>
      <input type='hidden' name='tope' id='tope' value='<?php echo redo( strval( readToken( $_GET['token'] )['PATRIMONIO'] - deudaToken( readToken( $_GET['token'] )['CORREO'],$_GET['token'] ) ) ) ?>' readonly>
      <input type='hidden' name='deudor' id='deudor' value='<?php echo deudor(readVendedor($_SESSION['user'])['CORREO'],$_GET['token'])?>' readonly>
      <input type='hidden' name='t_correo' id='user' value="<?php echo readVendedor($_SESSION['user'])['CORREO']?>">
      <input type="hidden" id="token" value="<?php if(isset($_GET['token'])) echo $_GET['token']; ?>">
      <input type="hidden" id="correo" value="<?php echo readVendedor($_SESSION['user'])['CORREO']?>">
      <input type="hidden" id="saldo" value="<?php echo readVendedor($_SESSION['user'])['SALDO']?>">
      <input type="hidden" id="valor1" value="<?php echo readToken($_GET['token'])['VALOR']; ?>">
      <input type="hidden" id="saldotoken" value="<?php echo loQueTengo(readVendedor($_SESSION['user'])['CORREO'],$_GET['token']); ?>">
      <div class="divPrecio">
      	<img style="background:#263238; vertical-align: middle;" width="34" height="34" src="../Book/comercios/<?php echo readToken($_GET['token'])['IMAGEN']; ?>" >
      	<strong style="background:#263238;color: white; font-size: 18px; font-weight: bold;"> <?php echo readToken($_GET['token'])['TOKEN'] ?>/FRD</strong>
      	<div style="background:#263238;margin-left: 8px; display:inline-block; color: <?php echo colorPrecio($_GET['token'])?>; font-size:18px; font-weight: bold;" id="valor2"><?php echo price(readToken($_GET['token'])['VALOR']) ?></div>
        <input placeholder="Buscar" type="search" id="codigo" name="codigo" list="code" onchange="movCode()" style="margin-left: 8px; border:0; border-radius:3px;  width:150px; padding:5px;">
     		<datalist id="code" >
    		<?php
    		   $conexion = mysqli_connect(servidor(),user(),password());
    		   $db = mysqli_select_db( $conexion, database());
    			 $consulta = "select * from TOKEN WHERE BLOQUEADO=0 ORDER BY TOKEN";
    			 $resultado = mysqli_query( $conexion, $consulta );
    		   while($row = mysqli_fetch_array($resultado)){
    			  echo "<option label='".$row['TOKEN']."' value='".$row['TOKEN']."'>";
    		   }
    			mysqli_close($conexion);
    		?>
    	  </datalist>
        <br>

        <div style="color:white;display:inline-block;"><?php echo substr(readToken($_GET['token'])['NOMBRE'],0,20); ?></div>
        <div style="color:white;display:inline-block;margin-left:10px; color:white; font-size:13px; font-weight:bold;">Patrimonio <span id="newPatrimonio" style="color:<?php echo colorCap($_GET['token']) ?>;"><?php echo redo(readToken($_GET['token'])['PATRIMONIO']); ?></span></div>
        <div style="color:white;display:inline-block;margin-left:10px; color:white; font-size:13px; font-weight:bold;">Vol <span id="newVolumen" style="color:<?php echo colorVol($_GET['token']) ?>;"><?php echo redo(readToken($_GET['token'])['VOLUMEN']); ?></span></div>
        <div style="color:white;display:inline-block;margin-left:10px; color:white; font-size:13px; font-weight:bold;">24h % <span id="newCreci" style="color:<?php echo colorCreci24($_GET['token']) ?>;"><?php echo redo(creci24($_GET['token'])); ?></span></div>

      </div>
      <div class="divcontainer" id='chart'><!--GRAFICO--></div>
      <div class="divcontainer"><!--ACCIONES-->
        <div class="div2dentro1"><!--COMPRAS-->
          <br>
          <div style="width:90%;height:21px; background:transparent;display: inline-block;">
          <div style="margin-right:5px; float: right; font-weight:bold; width:auto; background:transparent; color: white; font-size: 13px; display: inline-block;" id="textbox">Disp 0.00 ---</div>
          </div>
          <div style="height:5px;"></div>
          <div class="numeral">
          <div class="numeral-caja">Precio<span style="color:white;">**</span></div>
          <input class="numeral-number" onkeyup="calc_precio()" onchange="calc_precio()" name="Dprecio" id="Dprecio"  type="number" step="0.0001" readonly value="<?php echo str_replace(",",".",strval(price(readToken($_GET['token'])['VALOR'])))?>" min="0.0001">
          <div class="numeral-caja">FRD</div>
          </div>
          <div style="height:5px;"></div>

          <div class="numeral">
          <div class="numeral-caja">Cantidad</div>
          <input onkeyup="calc_token()" onchange="calc_token()" onclick="this.value='';" name="cantidad" id="cantidad" class="numeral-number" type="number" value="0" min="1" step="0.01">
          <div class="numeral-caja"><?php echo readToken($_GET['token'])['TOKEN'] ?></div>
          </div>
          <div style="height:5px;"></div>

          <div class="numeral">
          <div class="numeral-caja">Total<span style="color:white;">***</span></div>
          <input onkeyup="calc_cash()" onchange="calc_cash()" onclick="this.value='';" name="Dtotal" id="Dtotal" class="numeral-number" type="number" value="0" min="10" step="0.01">
          <div class="numeral-caja">FRD</div>
          </div>
          <div style="height:10px;"></div>
          <div class="numeral-button" id='maxf' onclick='max_f_25()'>25%</div>
          <div class="numeral-button" id='maxf' onclick='max_f_50()'>50%</div>
          <div class="numeral-button" id='maxf' onclick='max_f()'>100%</div>

          <br><br>
          <button type="button" id="compra" class="compra" onclick="comprar();">Comprar</button>

        </div>

        <div class="div2dentro1"><!--VENTAS-->
          <br>
          <div style="width:90%;height:21px; background:transparent;display: inline-block;">
          <div id="saltoken" style="margin-right:5px; float: right;font-weight:bold; width:auto; color: white;font-size: 13px; background: transparent; ">Disp 0.00 ---</div>
          </div>
          <div style="height:5px;"></div>
          <div class="numeral">
          <div class="numeral-caja">Precio<span style="color:white;">**</span></div>
          <input onkeyup="calc_precio_venta()" onchange="calc_precio_venta()" name="Dprecio_venta" id="Dprecio_venta" class="numeral-number" type="number" step="0.0001" readonly value="<?php echo str_replace(",",".",strval(price(readToken($_GET['token'])['VALOR'])))?>" min="0.0001">
          <div class="numeral-caja">FRD</div>
          </div>
          <div style="height:5px;"></div>

          <div class="numeral">
          <div class="numeral-caja">Cantidad</div>
          <input onkeyup="calc_token_venta()" onchange="calc_token_venta()" onclick="this.value='';" name="cantidad_venta" id="cantidad_venta" class="numeral-number" type="number" step="0.01" value="0" min="1">
          <div class="numeral-caja"><?php echo readToken($_GET['token'])['TOKEN'] ?></div>
          </div>
          <div style="height:5px;"></div>

          <div class="numeral">
          <div class="numeral-caja">Total<span style="color:white;">***</span></div>
          <input onkeyup="calc_cash_venta()" onchange="calc_cash_venta()" onclick="this.value='';" name="Dtotal_venta" id="Dtotal_venta" class="numeral-number" type="number" value="0" min="10" step="0.01">
          <div class="numeral-caja">FRD</div>
          </div>
          <div style="height:10px;"></div>

          <div class="numeral-button" id='maxf_venta' onclick='max_frt_25()'>25%</div>
          <div class="numeral-button" id='maxf_venta' onclick='max_frt_50()'>50%</div>
          <div class="numeral-button" id='maxf_venta' onclick='max_frt()'>100%</div>
          <br><br>

          <button type="button" id="venta" class="venta" onclick="vender();">Vender</button>
        </div>
      </div>
  	</div>
    <span style="font-size:13px; color:lightgray;font-weight:bold;">Historial</span>
    <a onclick="getOrdenes('day')" style="font-size:13px; padding: 3px; cursor:pointer; color:white;font-weight:bold;">Dia</a>
    <a onclick="getOrdenes('month')" style="font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Mes</a>
    <a onclick="getOrdenes('all')" style="font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Todo</a>
    <div class="div3" id="div3">

    </div>
      <?php
    }
    ?>
    <div style="height:21px;"></div>
</div>
</body>
</html>
