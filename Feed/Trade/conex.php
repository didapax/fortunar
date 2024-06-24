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
  <link rel="stylesheet" href="./style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <script>
  function inicio(){
  	myVar = setInterval(myTimer, 2000);
  }

  function myTimer() {
  	try {
  			$.post("../../modulo",{
  				verChatApp: document.getElementById('ticked').value
  			},function(data) {
  				$("#chat").html(data);
  			});
  	}catch (err) {

  	}
  }

  function myStopFunction() {
    try {
  		clearInterval(myVar);
    }
    catch (err) {

    }
  }

  function chat(){
  	  $.post("../../modulo",
  	  {
  	    insertchat: "Donald",
  	    tickedchat: document.getElementById('ticked').value,
  	    email: document.getElementById('envia').value,
  	    mensaje: document.getElementById('mensaje').value
  	  },
  	  function(data){
  		  document.getElementById("mensaje").value="";
  		  document.getElementById("mensaje").focus();
  	  });
  }

  function myFunction(event){
  	var x = event.key;
  	if (x == "Enter" || x == "Intro"){
  		chat();}
  }

  function cerrarchat(){
  	  $.post("../../modulo",
  	  {
  	    cerrarchat: "Donald",
  	    tickedchat: document.getElementById('ticked').value,
  	    email: document.getElementById('envia').value,
  	    mensaje: document.getElementById('mensaje').value
  	  },
  	  function(data){
  	  	alert("Chat Cerrado...");
  	  });
  }

  function cancelar(tk){
    var r = confirm("Estas Seguro deseas Cancelar la Orden...?");
  	if (r == true) {
      $.post("block",{
        cancelarCompra: tk
      },function(data){
        window.location.href="comercio.php?comercio=compra";
      });
  	}
  	else {
  	  /*txt = "You pressed Cancel!";*/
  	}
  }

  function recibido(tk){
    var r = confirm("Deseas Marcar como Pago Recibido la Orden..?");
  	if (r == true) {
      $.post("block",{
        recibirVenta: tk
      },function(data){
        window.location.href="comercio.php?comercio=compra";
      });
  	}
  	else {
  	  /*txt = "You pressed Cancel!";*/
  	}
  }

  function marcarEnvio(tk){
  	var r = confirm("Confirma que envio el Pago...?");
  	if (r == true) {
  		$.post("block",{
  			marcarEnvio: tk
  		},function(data){
  			window.location.href="comercio.php?comercio=compra";
  		});
  	}
  	else {
  	  /*txt = "You pressed Cancel!";*/
  	}
  }

  </script>

  <style>
body{
  color: white;
  background: #263238;
  padding: 13px;
}
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

<body onload="inicio()">

  <?php
  if(!isset($_SESSION['user']) ){
  ?>

  <?php
  }
  else{
    $sujeto="Envia";
    $moneda1="";
    $moneda2="";
    $boton="";
    $mensaje="";
    $ticked = readTicked($_GET['tk']);

    $tenvia1= number_format($ticked['MONTO'],2,",",".");

    $tenvia2= number_format($ticked['RECIBE'],2,",",".");

    if($ticked['TIPO']=="COMPRA"){
  		$moneda1 = $ticked['MONEDA'];
  		$moneda2= "FRD";
      $boton = "<button type='button' onclick=\"cancelar({$_GET['tk']})\">Cancelar</button>
                <button type='button' onclick=\"marcarEnvio({$_GET['tk']})\">Pago Enviado</button>
               ";
      $mensaje = "Transfiere el Monto al Comerciante.";
  	}
  	else{
  		$moneda1= "FRD";
  		$moneda2 = $ticked['MONEDA'];
      $boton = "<button type='button' onclick=\"recibido({$_GET['tk']})\">Pago Recibido</button>";
      $mensaje = "Envia tus datos bancarios en el chat al Anunciante.";
  	}

    echo "<br><span style='background:FFF;font-weight: 600;font-size:24px;'>".$ticked['TIPO']." ".$ticked['ESTATUS']."</span> ".$boton;
    echo "<br><b>Fecha: </b>".latinFecha($ticked['FECHA'])."";
    echo "<br>Monto: <b>".$tenvia1."</b>".$moneda1;
    echo "<br>Recibe: <b>".$tenvia2."</b>".$moneda2;
    echo "<br>ORDEN ".$ticked['TICKET']." ({$mensaje})";
    echo "<br>";
    $chateo = " <div style='color:black;font-size: 0.8em; display: block; overflow: hidden; width: 100%; height: auto; paddin:3px;'>
    <br>
    <label style='padding:3px; font-weight: 600;background:#FFEEEE;'>Chat: </label>
    <input type='hidden' id='ticked' value='".$_GET['tk']."'>
    <input type='hidden' id='envia' value='".readVendedor($_SESSION['user'])['CORREO']."'>
    </div>
      <div style='color:black;background:#D4DEE2; width: 96%; height: 250px; overflow-x: hidden;overflow-y: scroll;' id='chat'></div>
      <input autocomplete='off' style='font-size:16px;border:0; color: #333;display:inline-block; width: 60%;padding: 21px;' type='text' id='mensaje' onkeyup='myFunction(event)'>
      <button style='display:inline-block;cursor:pointer; font-weight: 600; border: 2px solid #333; border-radius: 5px; font-size: 16px;font-weight: bold; color:#fff;padding: .5rem 1rem;; background:#EA4040;' onclick='chat()'>Enviar</button>
    ";
    echo $chateo;
  }
  ?>
</body>
</html>
