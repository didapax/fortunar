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
  <link rel='stylesheet' href='https://netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.css'>
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>

  <script>

  </script>
<style>
a{
  color: white;
  text-decoration: none;
}
body{
  color: white;
}
ul li{
  padding: 5px;
  text-transform: capitalize;
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
<body style="padding:15px;">
<!-- partial:index.partial.html -->
<div class="grid-container grid-container--outer">
  <div class="flex-container play-header">
    <h3 class="apps-title">Centro de ayuda</h3>
  </div>
</div>
  <div class="sticky flex-container apps-header apps-header--small">
<h3 class="apps-title">Centro de Ayuda</h3>
  </div>
  <div style=" width: 90%; text-align: justify;">
    <h3 class="apps-title">Preguntas frecuentes</h3>
        <ul>
          <li><a href="#quesonlostoken">Que son los Comercios?</a></li>
          <li><a href="#comocrearuntoken">Como crear un Comercio?</a></li>
          <li><a href="#verificar">Como verifico mi correo?</a></li>
          <li><a href="#puedo">Puedo ceder y traspasar un Comercio?</a></li>
          <li><a href="#comisiones">Que comisión se cobra?</a></li>
          <li><a href="#accederacreditos">Como acceder a los Créditos?</a></li>
          <li><a href="#puedoabonar">Puedo abonar a un crédito?</a></li>
          <li><a href="#depositos">Como Depositar?</a></li>
          <li><a href="#montoexchange">Montos para operar en el exchange</a></li>
          <li><a href="#revision">Que significa que tu comercio este en Revisión?</a></li>
        </ul>

        <p id="quesonlostoken">
          <h2>Que son los Comercios?</h2>
          Un Comercio es una unidad de valor que una organización o persona crea para gobernar su
          modelo de negocio y dar más poder a sus usuarios y así interactuar con su proyecto y productos,
          al tiempo que facilita la distribución y reparto de beneficios entre todos sus accionistas (inversionistas).
        </p>

        <p id="comocrearuntoken">
          <h2>Como crear un Comercio?</h2>
          Para crear un Comercio, primero debes tener tu correo <a href="#verificar">verificado</a> y poseer en tu cuenta un saldo mayor a 40FRD
           en segundo lugar solo debes presionar en el menú book <span  class="icon icon-book"></span> de Fortuna Royal
           en la parte superior aparecerá un menú indicando el acceso para <u>Crear Comercio</u>
          Debes tener a mano los datos requeridos, como la biografía del creador, los datos de la persona que lo esta creando,
          es importante suministrar un teléfono para la verificación, la red social del comercio es importante como canales de telegram,
          twitter, esa información es importante para tus futuros inversionistas, las siglas del comercio tienen que ser 3 (tres) letras
          combinadas que servirán para identificar de forma mas corta tu proyecto, el nombre del Comercio debe ser corto.<br>La descripcion, es muy
          importante es donde describes lo que harás con tu proyecto a que te dedicaras con tus proyecciones a futuro en que vas a invertir tus prestamos,
          el link de la imagen es un link que contiene la imagen identificativa del logo del comercio debes subirla por ejemplo a <b>imgfz</b> y copiar el link que te suministra
          guarda ese link para el futuro es muy importante tener a mano tu link del logo del comercio creado, el patrimonio es el monto de dinero
          que aportaras inicialmente para crear tu Negocio y debes tenerlo disponible en saldo FRD, el monto minimo de apertura es de 40FRD,  el volumen es el numero inicial de acciones en las
          que dividirás tu patrimonio inicial y así darle un valor a las participaciones para que tus inversionistas puedan acceder a el, el volumen aumenta
          cada vez que se compra una participacion en tu comercio y es muy importante ya que a partir de este crecimiento recibirás los créditos.
        </p>

        <p id="verificar">
          <h2>Como verifico mi correo?</h2>
          Para verificar tu correo debes acceder a la pagina principal HOME <span  class="icon icon-home"></span> en la parte superior tendrás la opción
          de <u>Config</u> o configuraciones donde podrás cambiar tus datos y foto de perfil y verificar tu correo solo darle al botón de verificar e
          inmediatamente se te enviara al correo un link de verificación (recuerda buscar en los no deseados sino aparece en la bandeja de entrada)
          copia el link y pegalo en tu navegador rápidamente quedaras verificado, si presentas fallas envia un correo explicativo a <b>soporte@fortunaroyal.com</b>
        </p>

        <p id="puedo">
          <h2>Puedo ceder y traspasar un Comercio?</h2>
            Si puedes es como vender o traspasar tu negocio a otro socio mutuo acuerdo, debes envía un correo explicando tu caso a <b>soporte@fortunaroyal.com</b> indicando también
            el correo del socio a traspasar que debe estar verificado en Fortuna Royal, debes saber que esta accion quedara marcada en la descripción del Comercio.
        </p>

        <p id="comisiones">
          <h2>Que comisión se cobra?</h2>
            Las Comisiones son necesarias para el mantenimiento de los gestores, se cobra un 5% para el retorno de la inversión en la venta de participaciones y un 0.25% para emitir las
            participaciones o la compra de las participaciones en un comercio, para retirar solo se paga 1 USDT la comisión por la Red TRON, para los depósitos 0% de comisión.
            <ul>
              <li>Retorno de Inversión (Venta) ..........5%</li>
              <li>Emisión de Participaciones (Compra) ...0.25%</li>
              <li>Deposito (recarga) ....................0%</li>
              <li>Creación del Comercio (Emisión) ...........0.25%</li>
              <li>Retiro a wallet TRON USDT..............1 USDT</li>
            </ul>
        </p>

        <p id="accederacreditos">
          <h2>Como acceder a los Créditos?</h2>
          Para acceder a los créditos en el menú book <span  class="icon icon-book"></span> de Fortuna Royal
          en la parte superior aparecerá un menu indicando el acceso <u>Créditos</u> el sistema asigna los créditos
          automáticamente, cuando aumentas tu patrimonio y volumen con la compra de las participaciones los créditos van en crecimiento a
          medida que vas avanzando, al aceptar un crédito estas comprometido con tus inversionistas para devolver lo mas pronto posible
          el retorno y las ganancias, inmediatamente después de aceptado aparecerá el botón de pagar, una vez pago aparecerá otro crédito disponible
          para que lo utilices en la inversión de tus productos, servicio, o actividad que realice tu proyecto comercial.
          <br> Sino pagas el crédito en el tiempo establecido que es un Mes el sistema automáticamente lo reconoce como una perdida y lo descontara del patrimonio
          haciendo bajar el precio de la participacion.
          <br> El sistema podría bloquearte la asignación de créditos con la falta de atención en los pagos incumplidos en este caso para desbloquearte deberás escribir
          a <b>soporte@fortunaroyal.com</b> explicando el caso y para pagar los créditos incumplidos.
          <br>Mientas tengas deudas activas no podrás vender tus participaciones.
        </p>

        <p id="puedoabonar">
          <h2>Puedo abonar a un crédito?</h2>
          No puedes abonar los créditos están diseñados para ser cancelados en su totalidad al termino de un mes.
        </p>

        <p id="depositos">
          <h2>Como Depositar?</h2>
          Para Depositar o Recargar tu saldo FRD  ve al menú <span  class="icon icon-credit-card"></span>
          presiona el boton de Depositar y debes transferir desde tu wallet USDT Tron, a la wallet del sistema que se suministra e indicar
          la <b>ID de Trasaccion</b> para alertar al sistema y relacionarlo con tu cuenta, la velocidad de la transacción dependerá del congestionamiento
          de la red.
          <u>Posibles Problemas:</u> favor comunicarse con <b>soporte@fortunaroyal.com</b> indicando Correo asociado, el monto y el Id de transaccion a la brevedad posible sera atendido.
          <b>Notas</b> otra forma de conseguir los FRD es a través del comercio P2P (pago por intercambio personal), puedes pagar y recibir F4 sin comisiones en el mismo menu
          <span  class="icon icon-credit-card"></span>
        </p>

        <p id="montoexchange">
          <h2>Montos para Operar en el Exchange</h2>
          Para utilizar el exchange y tradear las participaciones se necesita un monto minimo por operacion de 10 FRD.
        </p>

        <p id="revision">
          <h2>Que significa que tu comercio este en Revisión?</h2>
          Cuando creas un comercio este pasa a revisión y puede tardar hasta 48 horas dependiendo de la facilidad de comunicación que Fortuna Royal tenga
          para su verificación, los asesores de Fortuna Royal pueden contactarte por correo, Redes sociales, teléfono y otros medios
          pidiendo información. De ser necesario puedes abrir un ticket de soporte con tu caso <a style="text-decoration:underline; font-weight:bold; color: yellow;" href="comunidad#crearticket">aquí</a>.<br>
          De no ser aprobado tu comercio todos los fondos serán devueltos y podrás intentarlo de nuevo.
        </p>
    <br><br><br>
</div>
</body>
</html>
