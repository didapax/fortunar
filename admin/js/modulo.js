/*
 * admin JavaScript Revision 0.0.0.0.7
 * funciones resumidas del sistema de apuestas
 * FortunaRoyal
 * (c) 2020 by Triangulo Rojo
 *
 * */

var formatter = new Intl.NumberFormat('ve-VE', {
  style: 'currency',
  currency: 'VES',
});

function chat(){
window.location.href="historial?chat&insert&tickedchat="+document.getElementById('ticked').value+"&email="+document.getElementById('envia').value+"&mensaje="+document.getElementById('mensaje').value;
}

function recargar() {
window.location.href="https://www.fortunaroyal.com/appfortunar";
}

function myFunction(event){
var x = event.key;
if (x == "Enter" || x == "Intro"){
chat();}
}

function cerrarchat(){
window.location.href="historial?chat&cerrar&tickedchat="+document.getElementById('ticked').value+"&email="+document.getElementById('envia').value+"&mensaje="+document.getElementById('mensaje').value;
}

function juego_maquinita(){
document.getElementById("open_triple").style.display='block';
document.getElementById("open_triple").innerHTML = "<div style='position:absolute; top:0; left:0; ' ><button class='close'onclick='close_juego_triple()'>X</button></div><iframe  src='maquinita?siexiste=3'></iframe>";
}

function juego_torneo(){
window.location.href="salatorneo";
}

function juego_token(){
window.location.href="earn";
}

function juego_rifa(){
document.getElementById("open_triple").style.display='block';
document.getElementById("open_triple").innerHTML = "<div style='position:absolute; top:0; left:0; ' ><button class='close'onclick='close_juego_triple()'>X</button></div><iframe  src='rifas?rifas&auto=off'></iframe>";
}

function juego_animalito(){
document.getElementById("open_triple").style.display='block';
document.getElementById("open_triple").innerHTML = "<div style='position:absolute; top:0; left:0; ' ><button class='close'onclick='close_juego_triple()'>X</button></div><iframe  src='animalitos?siexiste=3'></iframe>";
}

function juego_triple(){
document.getElementById("open_triple").style.display='block';
document.getElementById("open_triple").innerHTML = "<div style='position:absolute; top:0; left:0; ' ><button class='close' onclick='close_juego_triple()'>X</button></div><iframe  src='triple?siexiste=3&'></iframe>";
}

function juego_caballo(){
document.getElementById("open_triple").style.display='block';
document.getElementById("open_triple").innerHTML = "<div style='position:absolute; top:0; left:0; ' ><button class='close' onclick='close_juego_triple()'>X</button></div><iframe  src='caballos'></iframe>";
}

function close_juego_triple(){
document.getElementById("open_triple").style.display='none';
}

function closeModal() {
document.getElementById("poster_cambiable").style.display = "none";
}

function openAnimalitos() {
document.getElementById("Animalitos_Game").style.display = "block";
}

function openAnimalitos2() {
document.getElementById("pipe").style.display = "block";
}

function show_poster() {
document.getElementById("poster_cambiable").style.display = "flex";
}

function openAnimalitos() {
document.getElementById("Animalitos_Game").style.display = "block";
}

function closeModals() {
document.getElementById("pipe").style.display = "none";
}

function chequeameesta() {
var checkBox = document.getElementById("click");

if (checkBox.checked == true){
document.getElementsByClassName("marquee").style.display = "none";
}
}

function notifread(){
  document.getElementById('campana').style.animation='none 1s ease-in-out infinite alternate';
}

function funcion() {
var contador = 0;
for (var i=0;i < document.forms["despedido"].elements.length;i++) {
inpt = document.forms[0].elements[i];
if (inpt.checked) {
contador++;
}

}
if(contador > 3) {
alert('Has seleccionados demasiados');
contador = 0;
return false;
}
}

function home(){
window.location.href="index";
}

function calculoFondeo(pcompra){
	var total;
	var comision;
	var str= document.getElementById('calculoB').value;
	if (document.getElementById('moneda').value=="BOLIVARES") {
		total= str.replace(/,/gi,"")  / pcompra;
	}
	else if(document.getElementById('moneda').value=="TETHER"){
		total=str *1;
	}
	else {
		total=str.replace(/,/gi,"") * pcompra;
	}
	document.getElementById('recibes').value=(total*1).toFixed(2);
}

function calculoRetiro(saldo,pventa){
	var total;
	if(document.getElementById('montoF').value <= saldo){
		if (document.getElementById('moneda').value=="BOLIVARES") {
			total=(document.getElementById('montoF').value * 1) * pventa;
			document.getElementById('recibes').value=(total*1).toFixed(2);
		}else if(document.getElementById('moneda').value=="TETHER"){
			total=(document.getElementById('montoF').value * 1) - 3;
			document.getElementById('recibes').value=(total*1).toFixed(2);
		}
		else {
			total=(document.getElementById('montoF').value * 1) / pventa;
			document.getElementById('recibes').value=(total*1).toFixed(6);
		}
	}
	else{
		alert("Saldo Insuficiente");
		document.getElementById('montoF').value='';
		document.getElementById('recibes').value='';
		document.getElementById('montoF').focus();
	}
}

function revision_init(){
	$.get("admin?asociado&email="+document.getElementById('asociado').value, function(data){
		if(data=="true"){
			document.getElementById('asociado').style.background="#CCE6CC";
			document.getElementById('in').style.display='inline-block';
			document.getElementById('semeolvido').style.display='inline-block';
				document.getElementById('status').value=0;
		}else{
			document.getElementById('asociado').style.background="#F0D6DB";
			document.getElementById('status').value=0;
		}
	});
}

function revision_pass(){
	if(document.getElementById('status').value=="1"){
		if (document.getElementById('psw').value.length<8) {
			document.getElementById('psw').value='';
			//document.getElementById('psw').focus();
			alert("La contraseña Requiere ser de 8 o mas caracteres..!");
			document.getElementById('psw').clear();
		}
	}
}

function comprobarSocio(){
	$.get("admin?asociado&email="+document.getElementById('asociado').value, function(data){
		if(data=="true"){
				document.getElementById('asociado').style.background="#CCE6CC";
				document.getElementById('enviar_asociado').style.display='block';
		}else{
				document.getElementById('asociado').style.background="#F0D6DB";
		}
	});
}

function revisionSaldoSocio(saldo){
	if((document.getElementById('montoSocio').value * 1) <= saldo){
		document.getElementById('montoSocio').style.background="#CCE6CC";
	}
	else{
		alert("Saldo Insuficiente");
		document.getElementById('montoSocio').value='';
		document.getElementById('montoSocio').focus();
	}
	if((document.getElementById('montoSocio').value*1)<1){
		alert("Monto Minimo 1")
		document.getElementById('montoSocio').value="";
		document.getElementById('montoSocio').focus();
	};
}

function actSaldo(correo){
	$.get("admin?saldo&email="+correo, function(data){
	    $('#textbox').html(data);
	});
}

function actSaldotoken(correo,token){
	$.get("admin?saldotoken&correo="+correo+"&token="+token, function(data){
	    $('#saltoken').html(data);
	    document.getElementById("saldotoken").value=data;
	});

	$.get("admin?saldito&email="+correo, function(data){
	    $('#textbox').html(data);
	    document.getElementById("saldo").value=data;
	});
}

function actNotif(correo){
	$.get("admin?notif&email="+correo, function(data){
	    $('#notif').html(data);
	});
}

/*$(window).on('load', function() {
  $('#status').fadeOut();
  $('#preloader').delay(350).fadeOut('slow');
  $('body').delay(350).css({'overflow':'visible'});
})*/
