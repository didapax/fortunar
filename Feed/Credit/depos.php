<?php
  include "../../modulo.php";
  date_default_timezone_set('America/Caracas');

  if(isset($_POST['crear'])){
    $tag = "oper".generaTicket();
    $url = 'https://tronapi.net/api/.give';
    $parameters = [
      'key' => '7ebe0e6515-c16a37d405-ca658d38d1-1e591cc646',
      'token' => 'USDT',
      'tag' => $tag
    ];
    $qs = http_build_query($parameters); // query string encode the parameters
    $request = "{$url}?{$qs}"; // create the request URL
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $request,            // set the request URL
      CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
    ));

    $response = curl_exec($curl);

    $datos = json_decode($response);
    if(isset($datos->result->address)){
        sqlconector("UPDATE USUARIOS SET WALLET='{$datos->result->address}' WHERE CORREO='{$_POST['correo']}'");
        curl_close($curl);
    }
  }

  if(isset($_POST['pasar'])){
    if($_POST['pasar'] == "USDT"){
       $tag = "TRANS ".generaTicket();
       $from = $_POST['wallet'];
       $to = "TNiquopoC4YfsS7DSLRxDtvgs55f9X7EWh"; //wallet de fortuna@fortunaroyal.com
       $monto = $_POST['monto'];

      $url = 'https://tronapi.net/api/.send';
      $parameters = [
        'key' => '7ebe0e6515-c16a37d405-ca658d38d1-1e591cc646',
        'token' => 'USDT',        
        'from' => $from,
        'address' => $to,
        'amount' => $monto,
        'tag' => $tag,
        'statusURL=' => ''
      ];
      $qs = http_build_query($parameters); // query string encode the parameters
      $request = "{$url}?{$qs}"; // create the request URL
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $request,            // set the request URL
        CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
      ));

      $response = curl_exec($curl);

      $datos = json_decode($response);

      if(!empty($datos) ){ 
        if(isset($datos->result)){
          sqlconector("UPDATE USUARIOS SET SALDO=".
          strval(row_sqlconector("SELECT SALDO FROM USUARIOS WHERE CORREO='".$_POST['correo']."'")['SALDO'] + $monto)." WHERE CORREO='".$_POST['correo']."'" );
          sqlconector("INSERT INTO OPERACIONES(TICKET,CAJERO,CLIENTE,TIPO,WALLET,MONTO,RECIBE,ESTATUS,MONEDA)
                VALUES(".$datos->result.",'".readFortuna()['RETIROS']."','"
                .readVendedor($_SESSION['user'])['CORREO']."','TRANSF','".$_POST['wallet']."',"
                .$_POST['monto'].","
                .$_POST['monto'].",'COMPLETADO','USDT')");
          curl_close($curl);
        }
      }
    }
    if ($_POST['pasar'] == "FRD"){
      $tag = "TRANS ".generaTicket();
      $to = $_POST['wallet']; //wallet del cliente
      $from = "TNiquopoC4YfsS7DSLRxDtvgs55f9X7EWh";
      $monto = $_POST['monto'];

      $url = 'https://tronapi.net/api/.send';
      $parameters = [
        'key' => '7ebe0e6515-c16a37d405-ca658d38d1-1e591cc646',
        'token' => 'USDT',
        'from' => $from,
        'address' => $to,
        'amount' => $monto,       
        'tag' => $tag,
        'statusURL=' => ''

      ];
      $qs = http_build_query($parameters); // query string encode the parameters
      $request = "{$url}?{$qs}"; // create the request URL
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $request,            // set the request URL
        CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
      ));

      $response = curl_exec($curl);

      $datos = json_decode($response);

      if(!empty($datos) ){ 
        if(isset($datos->result)){
          sqlconector("UPDATE USUARIOS SET SALDO=".
          strval(row_sqlconector("SELECT SALDO FROM USUARIOS WHERE CORREO='".$_POST['correo']."'")['SALDO'] - $monto)." WHERE CORREO='".$_POST['correo']."'" );
          sqlconector("INSERT INTO OPERACIONES(TICKET,CAJERO,CLIENTE,TIPO,WALLET,MONTO,RECIBE,ESTATUS,MONEDA)
                VALUES(".$datos->result.",'".readFortuna()['RETIROS']."','"
                .readVendedor($_SESSION['user'])['CORREO']."','TRANSF','".$_POST['wallet']."',"
                .$_POST['monto'].","
                .$_POST['monto'].",'COMPLETADO','FRD')");
          curl_close($curl);
        }
      }
    }
  }

  if(isset($_POST['retiro'])){
    $tag = generaTicket();
    $to = $_POST['to'];
    $from = $_POST['wallet'];
    $monto = $_POST['monto'] -1;

    $url = 'https://tronapi.net/api/.send';
    $parameters = [
      'key' => '7ebe0e6515-c16a37d405-ca658d38d1-1e591cc646',
      'token' => 'USDT',
      'from' => $from,
      'address' => $to,
      'amount' => $monto,
      'tag' => $tag,
      'statusURL=' => ''
    ];
    $qs = http_build_query($parameters); // query string encode the parameters
    $request = "{$url}?{$qs}"; // create the request URL
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $request,            // set the request URL
      CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
    ));

    $response = curl_exec($curl);

    $datos = json_decode($response);

    if(!empty($datos) ){ 
      if(isset($datos->result)){
        sqlconector("INSERT INTO OPERACIONES(TICKET,CAJERO,CLIENTE,TIPO,WALLET,MONTO,RECIBE,ESTATUS,MONEDA)
        VALUES(".$datos->result.",'".readFortuna()['RETIROS']."','"
        .readVendedor($_SESSION['user'])['CORREO']."','RETIRO','".$_POST['wallet']."',"
        .$_POST['monto'].","
        .$monto.",'COMPLETADO','USDT')");
        sqlconector("INSERT INTO LIBROCONTABLE(TIPO,MONEDA,MONTO) VALUES('RETIRO','FRD',1)");
        curl_close($curl);
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en" style="overflow-y: auto;">
<head>
  <meta charset="UTF-8">
  <title>Fortuna Royal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
  <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet' href='https://netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.css'>
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script> 
  <script type="text/javascript" src="jquery.qrcode.min.js"></script>

  <script>

  function myFunction() {
    /* Get the text field */
    var copyText = document.getElementById("myInput");

    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */

    /* Copy the text inside the text field */
    navigator.clipboard.writeText(copyText.value);
    /* Alert the copied text
    alert("Copied the text: " + copyText.value);*/
  }

  function crear(){
    document.getElementById('crearWallet').disabled = true;
    $.post("depos",{
      crear:"",
      correo: document.getElementById('correo').value

    },function(data){
      alert("Wallet creada con exito, recuerde solo Depositar a la red de tron TRC20");
      window.location.href="depos.php";
    });
  }


  function pasar(){
    if(document.getElementById('value1').value >=1 ){
      if(document.getElementById('select').value === "USDT"){
        document.getElementById('transferir').close();
        $.post("depos",{
          pasar:"USDT",
          wallet: document.getElementById('wallet').value,
          correo: document.getElementById('correo').value,
          monto: (document.getElementById('value1').value *1).toFixed(2)

        },function(data){
          alert("transferencia realizada con exito..!");
          window.location.href="credit.php";
        });
      }
      else if (document.getElementById('select').value === "FRD") {
        document.getElementById('transferir').close();
        $.post("depos",{
          pasar:"FRD",
          wallet: document.getElementById('wallet').value,
          correo: document.getElementById('correo').value,
          monto: (document.getElementById('value1').value *1).toFixed(2)

        },function(data){
          alert("transferencia realizada con exito..!");
          window.location.href="credit.php";
        });
      }
    }
    else{
      alert("debe ser minimo 1")
    }
  }

  function isselect(){
    if(document.getElementById('select').value === "USDT"){
        $("#cambio").html("FRD");
        document.getElementById('value1').max = document.getElementById('saldo_USDT').value;
    }
    else if (document.getElementById('select').value === "FRD") {
      $("#cambio").html("USDT");
      document.getElementById('value1').max = document.getElementById('saldo_FRD').value;
    }
  }

  function revisa(){
    if((document.getElementById('value1').value *1) > (document.getElementById('value1').max *1)){
      document.getElementById('value1').title="Monto errado";
      document.getElementById('value1').value = "";
    }
    else {
      document.getElementById('value2').value = document.getElementById('value1').value;
    }
  }

  function revisa_retiro(){
    if((document.getElementById('monto').value *1) > (document.getElementById('saldo_USDT').max *1)){
      document.getElementById('monto').title="Monto errado";
      document.getElementById('monto').value = "0";
    }
    else {
      document.getElementById("recibes").innerHTML=document.getElementById("monto").value -1;
    }
  }

  function retiro(){
    if(document.getElementById('monto').value >=1 ){
      document.getElementById('retiro').close();
      $.post("depos",{
        retiro:"",
        wallet: document.getElementById('to').value,
        correo: document.getElementById('correo').value,
        monto: (document.getElementById('monto').value *1).toFixed(2)

      },function(data){
        alert("Retiro realizado con exito..!");
        window.location.href="credit.php";
      });
    }
  }

  function getOrdenes(tiempo){
    document.getElementById("tiempo").value = tiempo;
    $.get("../../modulo?txid&email="+document.getElementById('correo').value+"&filtro="+tiempo,
      function(data){
        $("#div3").html(data);
      });
  }

  function inicio(){
      myVar = setInterval(myTimer, 3000);
      document.getElementById("preloader").style.display='block'
      $('#preloader').fadeOut('slow');
  }

  function myTimer() {
    getOrdenes(document.getElementById("tiempo").value);
    $.get("../../block?getbalance&wallet="+document.getElementById('wallet').value,
      function(data){
        $("#result").html(data);
        document.getElementById('saldo_USDT').value = data;
      });    
  }

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


  body {
  background:#263238;
  color: white;
  }
  /* Input Types*/
  input[type=text], input[type=password], input[type=number], input[type=email]{
  width: 80%;
  padding: 8px;
  display: flex;
  box-sizing: border-box;
  border: none;
  margin: 10px 10%;
  background: #EAEDED;
  text-align:center;
  }
  .appbtn{
    cursor: pointer;
    background: #EA4040;
    color: #fff;
    border: 8px;
    border-radius: 6px;
    padding: 8px;
    margin-right: 10px;
    font-size: 1.1rem;
  }

  .appbtn:hover{
    background: #ea2828;
    transform: scale(1.050);
  }
  .form{
    padding: 15px;
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

  .dialog_transf{
    position: absolute;
    top: 55px;
    border 1px solid black;
    border-radius: 5px;
    background: #151937;
    width: 355px;
    height: 290px;
    padding: 3px;
    left: 5px;
    color: white;
    box-shadow: 4px 3px 8px 1px #969696;    
  }

  .dialog_retiro{
    position: absolute;
    top: 55px;    
    border 1px solid black;
    border-radius: 5px;
    background: #151937;
    width: 355px;
    height: 300px;
    padding: 3px;
    left: 5px;
    color: white;
    box-shadow: 4px 3px 8px 1px #969696;
  }

  .div3{
  	display: inline-block;
  	width: 100%;
  	height: 250px;
  	background: #110F25;
  	overflow: scroll;
  }
  </style>
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

  <?php
  }
  else{
    $row = readVendedor($_SESSION['user']);
    ?>
      <form class="form">
        <input type="hidden" id="correo" value="<?php echo $row['CORREO'] ?>">
      <p style="paddin:13px;">
        <!--<div style="width:100%;text-align: center;">
          <img src="tether.png" style="width:140px; height:140px;"></div>-->
          <?php
          if(strlen($row['WALLET']) > 0){
          ?>
          <div id="demo" style="height:260px;width:260px;">
            <script>
              jQuery("#demo").qrcode(
                  {
                    //render:"table"
                    width: 244,
                    height: 244,
                    text: "<?php echo $row['WALLET']?>"
                  });              
            </script>
          </div>
        Wallet Tron (TRC20):<br>
        <div style="background:white;width:360px;">
          <input style="display: inline;font-size: 14px;border:0; outline: 0;font-weight:bold;width:320px;" value="<?php echo $row['WALLET'] ?>" readonly id="myInput">
          <button title="Has Click para copiar" type="button" style="border:0;cursor:pointer;" onclick="myFunction()"><i class="far fa-copy"></i></button> <br>
        </div>
        <?php

        $url = 'https://tronapi.net/api/.balance';
        $parameters = [
          'key' => '7ebe0e6515-c16a37d405-ca658d38d1-1e591cc646',
          'token' => 'USDT',
          'from' => $row['WALLET']
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $request,            // set the request URL
          CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl);

        $pepe = json_decode($response);

        if(!empty($pepe) ){ 
          if(isset($pepe->result)){
            echo "
              <input type='hidden' id='saldo_USDT' value='".$pepe->result."'>
              <input type='hidden' id='saldo_FRD' value='".$row['SALDO']."'>
              <input type='hidden' id='wallet' value='".$row['WALLET']."'>
              <input type='hidden' id='correo' value='".$row['CORREO']."'>
              <input type='hidden' id='tiempo' value='day'>
            ";
            print "<br><b>Balance:</b> <div id='result' style='display:inline-block;'>{$pepe->result}</div>USDT";
            print "<br><b>Balance FRD:</b> {$row["SALDO"]}FRD";
            print "<br><br><button class='appbtn' style='background:blue;' type='button' onclick=\"document.getElementById('transferir').show()\">Convertir</button>
            <button type='button' class='appbtn' onclick=\"document.getElementById('retiro').show()\">Retirar</button>";
            curl_close($curl);
          }
        }
        if(!isset($pepe->result)){
          echo "<script type='text/javascript'>
                function redirect() {
                      window.location.href = 'retirar.php';
                  }
                  window.onload = redirect();
              </script>";
        }
        ?>
        <div style="width:100%;height:10px;"></div>
        <ul style="font-size:10px;">
          <li>Envía solo USDT a esta dirección de depósito.</li>
          <li>Asegúrate de que la red es Tron (TRC20).</li>
        </ul>
      </p><br>
      <dialog class="dialog_transf" id="transferir" close>
        <a style="font-weight: bold;float:right;cursor:pointer;" onclick="document.getElementById('transferir').close()">X</a><br>
        <select style="paddin:8px; width:50%;" id="select" onchange="isselect()">
          <option value='null'>select</option>
          <option value='USDT'>USDT</option>
          <option value='FRD'>FRD</option>
        </select><br>
        <span>Monto</span>
        <input type="number" value="0" id="value1" min="1" onkeyup="revisa()" onchange="revisa()">
        <div style="width:100%;text-align:center;">
        <span style="font-size:30px;">⇆</span><br>
        </div>
        <div id="cambio"></div><input type="number" value="0" id="value2" readonly><br>
        <button class='appbtn' style="float:right;background:blue;" type="button" onclick="pasar()">Convertir</button>
      </dialog>

      <dialog class="dialog_retiro" id="retiro" close>
        <a style="font-weight: bold;float:right;cursor:pointer;" onclick="document.getElementById('retiro').close()">X</a><br>
        <span>To Wallet Tron TRC20:</span>
        <input type="text" value="" id="to" style="width:300px;"><br>
        <span>Monto USDT: </span><span style="font-size:21px;">↗</span>
        <input type="number" placeholder="10 como Minimo" required name="monto" id="monto" value='' min='10' onkeyup="revisa_retiro()" onchange="revisa_retiro()" step='0.1' title='USDT'>
        <ul style="font-size:10px;">
          <li>Retiro mínimo 10</li>
          <li>Comisión de retiro 1 USDT</li>
          <li style="font-size:14px;">Importe que se recibirá: <label style="font-weight:bold;font-size:16px;" id="recibes"></label> <span style="font-weight:bold;font-size:16px;">USDT</span></li>
        </ul>
        <button class='appbtn' style="float:right;" type="button" onclick="retiro()">Retirar</button>
      </dialog>

    </form>
    <span style="font-size:13px; color:lightgray;font-weight:bold;">Historial</span>
    <a onclick="getOrdenes('day')" style="font-size:13px; padding: 3px; cursor:pointer; color:white;font-weight:bold;">Dia</a>
    <a onclick="getOrdenes('month')" style="font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Mes</a>
    <a onclick="getOrdenes('all')" style="font-size:13px;padding: 3px; cursor:pointer;color:white;font-weight:bold;">Todo</a>
    <div class="div3" id="div3">

    </div>
      <?php
      }
      else{
        print "<button id='crearWallet' class='btn-depos' type='button' onclick=\"crear()\">Crear Waller TRC20</button>";
      }
      ?>
    <?php
  }
  ?>
</body>
</html>
