<?php
include "../../modulo.php";
date_default_timezone_set('America/Caracas');
?>

<!DOCTYPE html>
<html lang="en" style="overflow-y:auto;">
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="./style.css">
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
  <link href="c3.css" rel="stylesheet">
  <script src="c3.js"></script>
  <script src="https://d3js.org/d3.v5.min.js"></script>

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

  function getPrices(token){
    $.get("../../block?getPrices=",
    function(data){
      var datos= JSON.parse(data);
        document.getElementById(datos[token]['name']).innerHTML=datos[token]['symbol']+datos[token]['price'];
        $("#"+datos[token]['name']).css("color",datos[token]['colorprice']);

        document.getElementById("P_"+datos[token]['name']).innerHTML=datos[token]['patrimonio'];
        $("#P_"+datos[token]['name']).css("color",datos[token]['colorcap']);

        document.getElementById("V_"+datos[token]['name']).innerHTML=datos[token]['volumen'];
        $("#V_"+datos[token]['name']).css("color",datos[token]['colorvol']);
    });
  }

  function inicio(){
      grafico();
      myVar = setInterval(myTimer, 34000);
  }

  function myTimer(){
    grafico();
    getPrices(document.getElementById('token').value);
  }

  function insertNoticia(){
    $.post("../../block",{
    titulo: document.getElementById('titulo').value,
    insertnoticia: document.getElementById('noticia').value,
    correo:document.getElementById('correo').value,
    token: document.getElementById('token').value
    },function(data){
      alert("Noticia Insertada con exito...!, para verla Actualice la Pagina");
      document.getElementById('titulo').value='';
      document.getElementById('noticia').value='';
    });
  }
  </script>
  <style>
    body{
        color:white;
    }

    .div1{
      width: 350px;
      height: 200px;
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
    table{
      color: black;
    }

    .insertnoticia{
      padding:5px;
      font-size: 14px;
    }

    .insertnoticia input[type="text"]{
      margin: 3px;
      border-radius:5px;
      padding:5px;
    }

    .insertnoticia button{
      margin: 3px;
      border-radius:5px;
      padding:8px;
      background: blue;
    }

    .noticia{
      margin-bottom:5px;
    }

    .noticia-titulo{
      font-weight: bold;
      font-size: 18px;
    }
  </style>
</head>
<body style="padding:15px;" onload="inicio()">
  <input type="hidden" id="token" value="<?php echo $_GET['token']; ?>">
  <!-- partial:index.partial.html -->

  <div style="padding: 3px; width:95%;">
    <?php
      $token = readToken($_GET['token']);
      echo "
        <h3 class='apps-title'>
        <img style='vertical-align:middle' height='50px' src='../Book/comercios/".$token['IMAGEN'].
        "' >".$token['NOMBRE']." <span style='border-radius:5px;border: solid 2px white; padding:2px;'>".
        $token['TOKEN']."</span> ".makeImgEstrellas($token['RATE'])."</h3>
        <div style='font-weight:bold;'>
          <div>{$token['TOKEN']} Precio: <span id='{$token['TOKEN']}' style='font-size:18px; color:".colorPrecio($token['TOKEN']).";'>".getSymbol($token['TOKEN']).price($token['VALOR'])."</span><span style='font-size:13px;'></span></div>
          <div style='height:10px; width:100%;'></div>
          Patrimonio: <span id='P_{$token['TOKEN']}' style='color:".colorCap($token['TOKEN']).";'>".redo($token['PATRIMONIO'])."</span><span style='font-size:13px;'></span>
          <div style='height:10px; width:100%;'></div>
          Participaciones en Circulación: <span id='V_{$token['TOKEN']}' style='color:".colorVol($token['TOKEN']).";'>".redo($token['VOLUMEN'])."</span>{$token['TOKEN']}
          <div style='height:10px; width:100%;'></div>
        </div>
        <br>
        <div style='background:white;'>
          <div id='chart'></div>
        </div>
        <h3>Que es ".$token['NOMBRE']." (".$token['TOKEN'].")?</h3>
        <p>".$token['DESCRIPCION']."</p>
        <h3>Quienes son los Fundadores de ".$token['TOKEN']."?</h3>
        <p>".$token['CEO']." <br><br>
        <b><u>Red Social:</u></b> ".$token['INSTAGRAM']."
        </p>
      ";
      //Leemos Las Noticias.
      echo "
        <h2>Noticias</h2>
      ";
      $conexion = mysqli_connect(servidor(),user(),password(),database());
      if(!$conexion){
        echo "Refresh Page error to consulte in the Network...";
        exit();
      }
      else{
        $consulta = "select * from TOKENOTICIAS WHERE TOKEN='{$token['TOKEN']}' ORDER BY FECHA DESC";
        $resultado = mysqli_query( $conexion, $consulta );
        while($row = mysqli_fetch_array($resultado)){
           echo "
           <div class='noticia'>
            <div class='noticia-titulo'>{$row['TITULO']}</div>
            <div class='noticia-contenido'>{$row['NOTICIA']}</div>
           </div>
           ";
         }
         mysqli_close($conexion);
      }      
      if(isset($_SESSION['user']))
      if($token['CORREO']==readVendedor($_SESSION['user'])['CORREO']){
      ?>
      <hr>
        <div class="insertnoticia">
          <input type='hidden' id='correo' value='<?php echo $token['CORREO'] ?>'>
          <input type='hidden' id='token' value='<?php echo $token['TOKEN'] ?>'>
          <span>Titulo: </span><input type='text' id='titulo'><br>
          <span>Noticia: </span><br>
          <textarea rows="10" cols="40" id='noticia'></textarea>
          <br>
          <button type='button' onclick='insertNoticia()'>Insertar</button>
        </div>
      <?php
      }
		?>
    <br><br>
</div>
</body>
</html>
