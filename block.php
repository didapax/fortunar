<?php
include "modulo.php";

 if(isset($_GET['getPrices'])){
  recalcTokens();
  $conexion = mysqli_connect(servidor(),user(),password(),database());
  if(!$conexion){
    echo "Refresh Page error to consulte in the Network...";
    exit();
  }
  else{
    $consulta = "select * from TOKEN WHERE BLOQUEADO <= 1 ORDER BY VOLUMEN DESC";
    $resultado = mysqli_query( $conexion, $consulta );
    $arr = array();
    $i=0;
   while($row = mysqli_fetch_array($resultado)){
     $arr[$row['TOKEN']]['name']= $row['TOKEN'];
     $arr[$row['TOKEN']]['price']= price($row['VALOR']);
     $arr[$row['TOKEN']]['volumen']= redo($row['VOLUMEN']);
     $arr[$row['TOKEN']]['patrimonio']= redo($row['PATRIMONIO']);
     $arr[$row['TOKEN']]['symbol']= getSymbol($row['TOKEN']);
     $arr[$row['TOKEN']]['colorprice']= colorPrecio($row['TOKEN']);
     $arr[$row['TOKEN']]['colorvol']= colorVol($row['TOKEN']);
     $arr[$row['TOKEN']]['colorcap']= colorCap($row['TOKEN']);
     $i++;
   }
   mysqli_close($conexion);
   echo json_encode($arr);
  }
}

if(isset($_POST['pagoauto'])){
  sqlconector("UPDATE PRESTAMOTOKEN SET AUTO={$_POST['pagoauto']} WHERE ID={$_POST['id']}");
}

if(isset($_POST['insertnoticia'])){
  sqlconector("CREATE TABLE IF NOT EXISTS TOKENOTICIAS (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    FECHA TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    TITULO VARCHAR(255),
    NOTICIA TEXT,
    TOKEN VARCHAR(4),
    CORREO VARCHAR(34),
    BLOQUEADO INT DEFAULT 0)");

  sqlconector("INSERT INTO TOKENOTICIAS(TITULO,NOTICIA,TOKEN,CORREO) VALUES('{$_POST['titulo']}','{$_POST['insertnoticia']}','{$_POST['token']}','{$_POST['correo']}')");
} 

if(isset($_GET['getbalance'])){
  $url = 'https://tronapi.net/api/.balance';
  $parameters = [
    'key' => '7ebe0e6515-c16a37d405-ca658d38d1-1e591cc646',
    'token' => 'USDT',
    'from' => $_GET['wallet']
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
      echo $pepe->result;
      curl_close($curl);
    }
  }
  if(!isset($pepe->result)){
    echo "0.00";
  }  
}

?>
