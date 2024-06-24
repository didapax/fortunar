<?php
  require "init.php";

  function sqlconector($consulta) {
     $conexion = @mysqli_connect(servidor(),user(),password(),database());
     if (!$conexion) {
       echo "Refresh page, Failed to connect to Data: " . mysqli_connect_error();
       exit();
     }else{
       $resultado = mysqli_query( $conexion, $consulta );
       mysqli_close($conexion);
     }
  }

  function row_sqlconector($consulta) {
   $row =0;
   $conexion = @mysqli_connect(servidor(),user(),password(),database());
   if (!$conexion) {
     echo "Refresh page, Failed to connect to Data: " . mysqli_connect_error();
     exit();
   }else{
     if($resultado = mysqli_query( $conexion, $consulta )){
       $row = mysqli_fetch_array($resultado);
     }
     mysqli_close($conexion);
   }
   return $row;
  }

  function readToken($token) {
    return row_sqlconector("select * from TOKEN where TOKEN='".$token."'");
  }

  function grabarPrecio($token,$patrimonio,$volumen,$precio,$crecimiento){
    sqlconector("INSERT INTO PRECIOTOKEN(TOKEN,PATRIMONIO,VOLUMEN,PRECIO,CRECIMIENTO) VALUES('".$token."',".$patrimonio.",".$volumen.",".$precio.",".$crecimiento.")");
  }

  function updateValorToken($token,$valor) {
 		sqlconector("UPDATE TOKEN SET VALOR=".$valor. " WHERE TOKEN='".$token."'");
 	}

  function recalcTokens() {
      $conexion = mysqli_connect(servidor(),user(),password(),database());
      if (!$conexion) {
        echo "Refresh page, Failed to connect to Data...";
        exit();
      }else{
        $consulta = "select * from TOKEN ";
        $valor_token=0;
        $resultado = mysqli_query( $conexion, $consulta );
         while($row = mysqli_fetch_array($resultado)){
         $volumen = $row['VOLUMEN'];
         if($volumen==0)$volumen=1;
         $valor_token=$row['PATRIMONIO'] / $volumen;
         updateValorToken($row['TOKEN'],$valor_token);
         }
         mysqli_close($conexion);
      }
 }

 function crecimiento($precioactual, $precioanterior) {
   if($precioactual == 0) $precioactual = 1;
   if($precioanterior == 0) $precioanterior =1;
   return (($precioactual - $precioanterior) * 100) / $precioanterior;
 }

 function recordCount(){
   return row_sqlconector("SELECT Count(*) as SUMA FROM PRECIOTOKEN")['SUMA'];
 }

 function countPreci($token){
   $recordcount = row_sqlconector("SELECT Count(*) as SUMA FROM PRECIOTOKEN WHERE TOKEN='".$token."'")['SUMA'];
   $x=1;
   $precioactual = 0;
   $precioanterior = 0;
   $volactual = 0;
   $volanterior = 0;
   $patrimonioactual = 0;
   $patrimonioanterior = 0;
   $crecimiento = 0;
   $conexion = mysqli_connect(servidor(),user(),password(),database());
   if (!$conexion) {
     echo "Refresh page, Failed to connect to Data...";
     exit();
   }else{
     $consulta = "select * from PRECIOTOKEN WHERE TOKEN='".$token."'";
     $resultado = mysqli_query( $conexion, $consulta );
     while($row = mysqli_fetch_array($resultado)){
       if($x == ($recordcount - 1 )){
         $precioanterior = $row['PRECIO'];
         $volanterior = $row['VOLUMEN'];
         $patrimonioanterior = $row['PATRIMONIO'];
       }
       if($x == $recordcount){
         $precioactual = $row['PRECIO'];
         $volactual = $row['VOLUMEN'];
         $patrimonioactual = $row['PATRIMONIO'];
         $crecimiento = $row['CRECIMIENTO'];
       }
       $x++;
     }
     mysqli_close($conexion);
     return array($precioactual,$precioanterior,$volactual,$volanterior,$patrimonioactual,$patrimonioanterior,$crecimiento);
   }
 }

 $creci=0;
 $conexion = mysqli_connect(servidor(),user(),password(),database());
 if (!$conexion) {
   echo "Refresh page, Failed to connect to Data...";
   exit();
 }else{
   $consulta = "select * from TOKEN WHERE BLOQUEADO=0";
   $resultado = mysqli_query( $conexion, $consulta );
   recalcTokens();
   while($row = mysqli_fetch_array($resultado)){
       if(recordCount() > 1){
         $precio = countPreci($row['TOKEN']);
         $creci = crecimiento($precio[0],$precio[1]);
         if($creci == 0){
           $creci = $precio[6];
         }
       }
       grabarPrecio($row['TOKEN'],readToken($row['TOKEN'])['PATRIMONIO'],readToken($row['TOKEN'])['VOLUMEN'],readToken($row['TOKEN'])['VALOR'],$creci);
   }
   mysqli_close($conexion);
 }
?>
