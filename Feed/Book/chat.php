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
  function inicio(){
  	myVar = setInterval(myTimer, 2000);
  }

  function myTimer() {
  	try {
  			$.post("../../modulo.php",{
  				verChatApp: document.getElementById('ticked').value
  			},function(data) {
  				$("#chat").html(data);
  			});
  	}catch (err) {

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

  body{
    color: white;
  }
  </style>
</head>
<body style="padding:15px;" onload="inicio()">
  <?php
  echo "
  <input type='hidden' id='ticked' value='".$_GET['tk']."'>
  <input type='hidden' id='envia' value='".readVendedor($_SESSION['user'])['CORREO']."'>
  ";
   ?>
<!-- partial:index.partial.html -->
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title">SOPORTE FORTUNA ROYAL</h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">SOPORTE FORTUNA ROYAL</h3>
  </div>
    <p>
      Soporte por: <?php echo readTicked($_GET['tk'])['REFERENCIA']; ?>
      <br><span style="font-weight:bold; color:white;">Ticket <?php echo $_GET['tk']; ?></span>
    </p>
    <div style='color:black;border-top-left-radius: 5px; border-top-right-radius: 5px; background:#D4DEE2; width: 96%; height: 300px; overflow-x: hidden;overflow-y: scroll;' id='chat'></div>
    <div style='border-bottom-left-radius:5px; border-bottom-right-radius: 5px; background: transparent; width:96%; height: 70px;'>
      <input autocomplete='off' style='font-size:16px; border: 0; color: #333;display:inline-block; width: 60%;padding: 21px;' type='text' id='mensaje' onkeyup='myFunction(event)'>
      <button style='display:inline-block;cursor:pointer; font-weight: 600; border: 2px solid #333; border-radius: 5px; font-size: 16px;font-weight: bold; color:#fff;padding: .5rem 1rem;; background:#EA4040;' onclick='chat()'>Enviar</button>
    </div>
    <br><br>
</body>
</html>
