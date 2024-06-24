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
  </script>

  <style>
body{
  background: #1B183E;
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
    $ticked = readTicked($_GET['tk']);

    $tenvia1= number_format($ticked['MONTO'],2,",",".");

    $tenvia2= number_format($ticked['RECIBE'],2,",",".");


    echo "<br><span style='background:FFF;font-weight: 600;font-size:24px;'>".$ticked['TIPO']." ".$ticked['ESTATUS']."</span>";
    echo "<br><b>Fecha: </b>".latinFecha($ticked['FECHA'])."";
    echo "<br>Monto: <b>".$tenvia1."</b> ".$moneda1;
    echo "<br>Recibe: <b>".$tenvia2."</b> <span style='color:#008000;'>".$moneda2."</span>";
    echo "<br>Referencia: ".$ticked['REFERENCIA']."";
    echo "<br>";
    $chateo = " <div style='font-size: 0.8em; display: block; overflow: hidden; width: 100%; height: auto; paddin:3px;'>
    <br>
    <label style='padding:3px; font-weight: 600;background:#FFEEEE;'>Chat: </label>
    <input type='hidden' id='ticked' value='".$_GET['tk']."'>
    <input type='hidden' id='envia' value='".readVendedor($_SESSION['user'])['CORREO']."'>
    </div>
      <div style='background:#D4DEE2; width: 96%; height: 250px; overflow-x: hidden;overflow-y: scroll;' id='chat'></div>
      <input autocomplete='off' style='font-size:16px;border:0; color: #333;display:inline-block; width: 60%;padding: 21px;' type='text' id='mensaje' onkeyup='myFunction(event)'>
      <button style='display:inline-block;cursor:pointer; font-weight: 600; border: 2px solid #333; border-radius: 5px; font-size: 16px;font-weight: bold; color:#fff;padding: .5rem 1rem;; background:#EA4040;' onclick='chat()'>Enviar</button>
    ";
    if(strlen($ticked['REFERENCIA'])==0 && $ticked['TIPO']=="RETIRO"){
    echo $chateo;
    }
    if(strlen($ticked['REFERENCIA'])>0 && $ticked['TIPO']=="DEPOSITO"){
    echo $chateo;
    }
  }
  ?>
</body>
</html>
