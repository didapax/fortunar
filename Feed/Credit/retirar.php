<?php
  include "../../modulo.php";
  date_default_timezone_set('America/Caracas');

?>
<!DOCTYPE html>
<html lang="en" >
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

 </script>

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
  </style>
</head>

<body>
  <div id='preloader'>
  	<div class='charge_logo'>
      <div class='charge_word'>
        <div class='loader'></div>
      </div>
    </div>
  </div>
  <script>
    document.getElementById("preloader").style.display='block'
    window.onload= function() {
      /*$('#preloader').fadeOut('slow');*/
      window.location.href="depos.php";
    }
  </script>
</body>
</html>
