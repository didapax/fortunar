<?php
  include "../../modulo.php";
  date_default_timezone_set('America/Caracas');

  if(isset($_POST['enviar'])){

  	$referencia = generaTicket();
  	$recibes = "0";
  	$moneda = $_POST['comercio'];

  	sqlconector("INSERT INTO OPERACIONES(SUJETO,PAGADO,TICKET,CAJERO,CLIENTE,TIPO,REFERENCIA,MONTO,RECIBE,ESTATUS,MONEDA)
  				VALUES('EMI',1,".$referencia.",'"
  				.readVendedor($_SESSION['user'])['CORREO']."','"
  				.$_POST['asociado']."','PAGO','E"
  				.$referencia."',"
  				.$_POST['monto'].","
  				.$_POST['monto'].",'COMPLETADO','"
  				.$moneda."')");

          if($moneda == "FRD"){
            updateSaldo(readCliente($_POST['correo'])['SALDO'] - $_POST['monto'],$_POST['correo']);
          	updateSaldo(readCliente($_POST['asociado'])['SALDO'] + $_POST['monto'],$_POST['asociado']);
          }
          else{
            if(!if_wallet_exist($_POST['asociado'],$_POST['comercio'])) {
              create_wallet($_POST['asociado'],$_POST['comercio']);
            }
            sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo($_POST['correo'],$_POST['comercio']) - $_POST['monto'])." WHERE TOKEN='".$_POST['comercio']."' AND CORREO='".$_POST['correo']."'");
            sqlconector("UPDATE WALLETOKEN SET CANTIDAD=".strval(loQueTengo($_POST['asociado'],$_POST['comercio']) + $_POST['monto'])." WHERE TOKEN='".$_POST['comercio']."' AND CORREO='".$_POST['asociado']."'");
          }


  	insertNotif($_POST['asociado'],readVendedor($_SESSION['user'])['NOMBRE']." Te ha Enviado: ".$_POST['monto']." {$moneda}","../Credit/historial?filtro=month");

  	ini_set( 'display_errors', 1 );
  	error_reporting( E_ALL );
  	$from = "soporte@fortunaroyal.com";
  	$to = $_POST['asociado'];
  	$subject = "Te han Enviado FRD";
  	$message = "Has Recibido del compañero ".$_POST['correo']. " la Cantidad de: ".$_POST['monto']." {$moneda}";
  	$headers = "From:" . $from;
  	mail($to,$subject,$message, $headers);
  }
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
  <script type="text/javascript" src="jquery.qrcode.min.js"></script>

  <script>

function verificar(){
	$.post("../Home/perfil",
	{
	verificar: "",
	correo: document.getElementById('correo').value
	},
	function(data){
	});
	alert("Revisa tu Correo y confirma el Link que se te envio, luego de verificar actualiza la pagina de perfil..!");
}

  function price(price){
    if((price * 1) >= 1) {
      return price.toFixed(2);
    }
    else{
      return price.toFixed(4);
    }
  }

  function comprobarSocio(){
    if(document.getElementById('asociado').value.length > 0 ){
      $.get("../../modulo?asociado&email="+document.getElementById('asociado').value, function(data){
    		if(data=="true"){
    				document.getElementById('asociado').style.background="#CCE6CC";
            document.getElementById('montoSocio').style.background="#fff";
    				/*document.getElementById('enviar_asociado').style.display='block';*/
    		}else{
    				document.getElementById('asociado').style.background="#F0D6DB";
            document.getElementById('asociado').value="";
            document.getElementById('asociado').focus();
            document.getElementById('montoSocio').style.background="#fff";
    		}
    	});
    }
  }


  function movCode() {
    /*window.location.href="trade?token="+document.getElementById('codigo').value;*/
    if(document.getElementById('coin').value.length > 0){
      if(document.getElementById('coin').value === "FRD"){
        $.get("../../modulo?saldito&email="+correo, function(data){
            var valor = (data * 1).toFixed(2);
      	    $('#salida').html("<span style='color:#9F97B9;'>Balance Fortuna Royal</span> "+valor+" FRD");
      	    document.getElementById("saldo").value = valor;
            document.getElementById("comercio").value = "FRD";
            document.getElementById('montoSocio').max= valor;
      	});
      }
      else{
        $.get("../../modulo?saldotoken&correo="+document.getElementById('correo').value+"&token="+document.getElementById('coin').value, function(data){
      	    $('#salida').html("<span style='color:#9F97B9;'>Balance "+document.getElementById('coin').value+"</span> "+price(data*1)+ " ");
      	    document.getElementById("saldotoken").value=price(data*1);
            document.getElementById("comercio").value = document.getElementById('coin').value;
            document.getElementById('montoSocio').max=price(data*1);
      	});
      }
    }
  }
</script>

  <style>
  :root{
  --rojo: #FF4F43;
  --rojo_hover:#E83C3C  ;
  --rojo_fuerte:#E72828;
  --transperente:rgba(0, 0, 0, 0.5);
  --gris: #797D7F;
  --gris_hover:#626567;
  --verde:#5c8c62;
  --verde_hover:#91BD92;
  --pastel:#cccabb;
  --select-color: #fff;
  --select-background: #c0392b;
  --select-width: 220px;
  --select-height: 40px;
  --background: #2C2C2C;
  }
  * {

  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
  }

  body {
  background:#263238;
  color: black;
  padding: 21px;
  }

  input[type=number]{
    font-size: 14px;
  }

  input[type=email]{
    font-size: 13px;
  }

  input[type=search]{
    font-size: 13px;
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
    width:115px;
    color:#9F97B9;
    font-weight:bold;
    text-align: left;
  }

  .numeral-number{
    background:#F0F1F2;
    text-align: right;
    width:225px;
    font-weight: bold;
    border:0;
    outline:0;
    border-radius: 3px;
    padding: 8px;
  }

  .numeral-text{
    background:#F0F1F2;
    text-align: right;
    width:225px;
    font-weight: bold;
    border:0;
    outline:0;
    border-radius: 3px;
    padding: 8px;
  }

  .btn{
  cursor: pointer;
  border: 1px solid black;
  border-radius: 5px;
  }
  .btn-jugar{
  color: #fff ;
  background:blue;
  font-size: 1.5em;
  padding:21px;
  width: 99%;
  font-weight: bolder;
  text-transform: uppercase;
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
  </style>
</head>

<body>

  <?php
  if(!isset($_SESSION['user']) ){
  ?>
  <script type='text/javascript'>
    function redirect() {
          window.location.href = 'sesion.php';
      }
      window.onload = redirect();
  </script>
  <?php
  }
  else{
    ?>
     <div class="user-box first-box">
      <div class="activity card" style="--delay: .2s;">
       <div style="font-size:18px;color:white;">Envia Dinero o Participaciones sin Comisiones</div>
       <div class="subtitle" style="color:white;">
         <form  method="post" action="credit.php" >
             <input type="hidden" id="comercio" name="comercio" value="FRD">
             <input type="hidden" id="correo" name="correo" value="<?php echo readVendedor($_SESSION['user'])['CORREO']?>">
             <input type="hidden" id="saldo" name="saldo" value="<?php echo readVendedor($_SESSION['user'])['SALDO']?>">
             <input type="hidden" id="saldotoken" name="saldotoken" value="0">
             
             <div class="account-name" style="font-weight:bold; font-size:18px; color:coral;" id="salida">
                   
             <span style='color:#9F97B9;'>Balance Fortuna Royal </span><?php echo miformat(readVendedor($_SESSION['user'])['SALDO'])?> FRD</div>
            
            <div>
             <div class="numeral">
             <div class="numeral-caja">Comercio </div>
             <input onclick="this.value=''" class="numeral-text" type="search" name="coin" id="coin" list="code" onchange="movCode()"  value='FRD'>
             <datalist id="code">
               <option label='FRD' value='FRD'>
         		<?php
         		   $conexion = mysqli_connect(servidor(),user(),password());
         		   $db = mysqli_select_db( $conexion, database());
         			 $consulta = "select * from WALLETOKEN WHERE CORREO='".readVendedor($_SESSION['user'])['CORREO']."'";
         			 $resultado = mysqli_query( $conexion, $consulta );
         		   while($row = mysqli_fetch_array($resultado)){
         			  echo "<option label='".$row['TOKEN']."' value='".$row['TOKEN']."'>";
         		   }
         			mysqli_close($conexion);
         		?>
         	  </datalist>
             </div>
             <div style="height:15px;"></div>
             <div class="numeral">
             <div class="numeral-caja">Correo a Pagar</div>
             <input class="numeral-number" type="email" required name="asociado" id="asociado" onfocusout="comprobarSocio()">             
             </div>
             
             <div style="height:5px;"></div>

             <div class="numeral">
             <div class="numeral-caja">Monto</div>
             <input onclick="this.value=''" class="numeral-number" required type="number" name="monto" id="montoSocio" value='0.10' min='0.01' max="<?php echo readVendedor($_SESSION['user'])['SALDO'] ?>" step="0.01" title='Monto FRD'>
             </div>


              </div><!--Fin bloque-->

             <div style="height:20px;width:100%;"></div>

             <button id="enviar_asociado" type="submit" name="enviar" value="enviar" class="btn btn-jugar" >Pagar</button>

         </form>
       </div>
         <div style="height:25px;"></div>
      </div>

      <div class="card transection" style="--delay: .4s;">
        <p style="color:white;">
        <img src="envio.png" align="right" width="150"> Deposita y recarga tu cuenta Fortuna royal con USDT y obten tus FRD y comienza a Operar, se parte de los Comercios afiliados, tenemos soporte chat en linea para tus exigencias.
        <br>
      </p>
      <?php
        if(readVendedor($_SESSION['user'])['ACTIVO']==0){
          echo "<a style=\"text-align:center; padding:10px; border:1px solid #FCD535; border-radius: 3px; background: #FCD535; font-weight:bold; cursor:pointer;\" onclick=\"verificar()\">Verificar Correo</a>";
        }else{
          echo "<a style=\"text-align:center; padding:10px; border:1px solid #FCD535; border-radius: 3px; background: #FCD535; font-weight:bold; cursor:pointer;\" onclick=\"window.location.href='retirar.php'\">Depositar</a>";
        }
      ?>      
      </div>
      <div class="card transection" style="--delay: .4s;">
        <p style="color:white;">
          <img src="fdollar_balance.png" align="right" width="100"> Recibe Participaciones y Pagos en Fortuna Royal Dolar (FRD )
          Correo: <?php echo readVendedor($_SESSION['user'])['CORREO'] ?>
        </p>
        <div id="demo" style="height:260px;width:260px;">
            <script>
              jQuery("#demo").qrcode(
                  {
                    //render:"table"
                    width: 244,
                    height: 244,
                    text: "<?php echo readVendedor($_SESSION['user'])['CORREO']?>"
                  });              
            </script>
          </div>        
      </div>      
      <div class="card transection" style="--delay: .4s;">
        <p style="color:white;">
          <img src="retiro.png" align="right" width="100"> Retira tu dinero y tus ganacias cuando quieras a tu wallet USDT usando de la red Tron  (TRC20)
        </p>
        <?php
          if(readVendedor($_SESSION['user'])['ACTIVO']==0){
            echo "<a style=\"text-align:center; padding:10px; border:1px solid #FCD535; border-radius: 3px; background: #FCD535; font-weight:bold; cursor:pointer;\" onclick=\"verificar()\">Verificar Correo</a>";
          }else{
            echo "<a style=\"text-align:center; padding:10px; border:1px solid #FCD535; border-radius: 3px; background: #FCD535; font-weight:bold; cursor:pointer;\" onclick=\"window.location.href='retirar.php'\">Retirar</a>";
          }
        ?>        
      </div>
     </div>
     <br><br><br>
    <?php
  }
  ?>
</body>
</html>
