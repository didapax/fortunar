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

function recalcTokens() {
   $conexion = mysqli_connect(servidor(),user(),password(),database());
	if (!$conexion) {
	    echo "Refresh page, Failed to connect to Data...";
	    exit();
	}
	else{
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

function updateValorToken($token,$valor) {
	sqlconector("UPDATE TOKEN SET VALOR=".$valor. " WHERE TOKEN='".$token."'");
}

function readCredito($id){
	return row_sqlconector("SELECT * FROM PRESTAMOTOKEN WHERE ID=".$id);
}

function tasa($tasa) {
	return ($tasa / 100);
}

function updateSaldo($valor,$correo){
	sqlconector("UPDATE USUARIOS SET SALDO=".$valor." WHERE CORREO='".$correo."'");
}

function readVendedor($id){
	return row_sqlconector("select * from USUARIOS where ID=".$id);
}

function readCliente($email){
	return row_sqlconector("select * from USUARIOS where CORREO='".$email."'");
}

function readFortuna(){
	return row_sqlconector("select * from FORTUNA");
 }

function pagar($id){
	$ganancia = readCredito($id)['PORPAGAR'] - readCredito($id)['CAPITAL'];
	$comision = $ganancia * tasa(readFortuna()['COMISIONEMISION']);
	$cliente = readCredito($id)['CORREO'];
  
	updateSaldo(readCliente($cliente)['SALDO'] - readCredito($id)['PORPAGAR'],readCliente($cliente)['CORREO']);
	updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO'] + readCredito($id)['PORPAGAR'],"fortuna@fortunaroyal.com");
  
	if(readCliente($cliente)['CORREO']=="fortuna@fortunaroyal.com"){
	  updateSaldo(readCliente("fortuna@fortunaroyal.com")['SALDO'] + $ganancia,"fortuna@fortunaroyal.com");
	}
  
	sqlconector("UPDATE TOKEN SET PATRIMONIO=".strval(readToken(readCredito($id)['TOKEN'])['PATRIMONIO']+($ganancia - $comision))." WHERE TOKEN='".readCredito($id)['TOKEN']."'");
	sqlconector("INSERT INTO LIBROCONTABLE(TIPO,MONTO) VALUES('CREDITO',".$comision.")");
	sqlconector("UPDATE PRESTAMOTOKEN SET PAGADO=1 WHERE ID=".$id);	
}

$fechaActual= date_create(date('Y-m-d'));
$intereses=0;
$conexion = mysqli_connect(servidor(),user(),password(),database());
if (!$conexion) {
   	echo "Refresh page, Failed to connect to Data...";
   	exit();
}
else{
   	$consulta = "select * from PRESTAMOTOKEN WHERE PAGADO=0";
   	$resultado = mysqli_query( $conexion, $consulta );
   	while($row = mysqli_fetch_array($resultado)){
		$intereses=0;
   		if(date_format(date_create($row['FIN']),'Y-m-d')==date_format($fechaActual,'Y-m-d')){
			if($row['ACEPTADO']==1){
				if( readCliente($row['CORREO'])['SALDO'] >= $row['PORPAGAR'] ){
					if($row['AUTO']==1){
						pagar($row['ID']);
					}
				}
				else{
					$intereses=$row['PORPAGAR'] - $row['CAPITAL'];
					$consulta = "UPDATE PRESTAMOTOKEN SET PAGADO=2 WHERE ID=".$row['ID'];
					$setPrestamo = mysqli_query( $conexion, $consulta );
					$consulta = "UPDATE TOKEN SET PATRIMONIO=".strval(readToken($row['TOKEN'])['PATRIMONIO'] - $row['CAPITAL'])." WHERE TOKEN='".$row['TOKEN']."'";
					$setToken = mysqli_query( $conexion, $consulta );
				}
			}
			else{
				$consulta = "DELETE FROM PRESTAMOTOKEN WHERE ID=".$row['ID'];
      			$setPrestamo = mysqli_query( $conexion, $consulta );
			}
   		}
  	}
	mysqli_close($conexion);
}
recalcTokens();

?>
