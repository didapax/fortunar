/*
 * Modulo JavaScript Revision 0.0.0.0.8
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

function closeModal() {
document.getElementById("poster_cambiable").style.display = "none";
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
	$.get("../../modulo.php?asociado&email="+document.getElementById('asociado').value, function(data){
		if(data=="true"){
			document.getElementById('asociado').style.background="#CCE6CC";
			document.getElementById('in').style.display='inline-block';
			document.getElementById('semeolvido').style.display='inline-block';
			document.getElementById('rg').style.display='none';
			document.getElementById('status').value=0;
		}else{
			document.getElementById('asociado').style.background="#F0D6DB";
			document.getElementById('rg').style.display='inline-block';
			document.getElementById('in').style.display='none';
			document.getElementById('status').value=1;
		}
	});
}

function revision_pass(){
	if(document.getElementById('status').value=="1"){
		if (document.getElementById('psw').value.length<8) {
			document.getElementById('psw').value='';
			alert("La contraseña Requiere ser de 8 o mas caracteres..!");
			document.getElementById('psw').clear();
		}
	}
}

function comprobarSocio(){
	$.get("modulo?asociado&email="+document.getElementById('asociado').value, function(data){
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
		document.getElementById('montoSocio').value='0';
		document.getElementById('montoSocio').focus();
	}
}

function actSaldo(correo){
	$.get("modulo?saldo&email="+correo, function(data){
	    $('#textbox').html(data);
	});
}

function actSaldotoken(correo,token){
  document.getElementById("textbox").html("00");
	$.get("../../modulo?saldotoken&correo="+correo+"&token="+token, function(data){
	    $('#saltoken').html(data);
	    document.getElementById("saldotoken").value=data;
	});

	$.get("../../modulo?saldito&email="+correo, function(data){
	    $('#textbox').html(data);
	    document.getElementById("saldo").value=data;
	});
}

function actNotif(correo){
	$.get("modulo?notif&email="+correo, function(data){
	    $('#notif').html(data);
	});
}
