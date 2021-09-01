<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function update(){
var id = $('#id').val();

var fecha_cierre = $('#fecha_cierre').val();

var nombre_negocio = $('#nombre_negocio').val();

var ruc = $('#ruc').val();

var direccion = $('#direccion').val();

var tipo_negocio = $('#tipo_negocio').val();

var telefono = $('#telefono').val();

var pagina_web = $('#pagina_web').val();

var razon_social = $('#razon_social').val();

var moneda = $('#moneda').val();

var serie_boleta = $('#serie_boleta').val();

var serie_factura = $('#serie_factura').val();

var almacen_principal = $('#almacen_principal').val();

var correoEmisor = $('#correo_emisor').val();

var estado_fila = $('#estado_fila').val();

var ruta = $('#ruta').val();

var token = $('#token').val();

var url_os_ticket = $('#url_os_ticket').val();
var key_os_ticket = $('#key_os_ticket').val();
var ip_publica_cliente_os_ticket = $('#ip_publica_cliente_os_ticket').val();

var logo_ticket = $('#logo_ticket option:selected').val();
var logo_boleta = $('#logo_boleta option:selected').val();
var logo_factura = $('#logo_factura option:selected').val();

var id_detraccion = $('#id_detraccion').val();

$.post('ws/configuracion.php', {op: 'mod',id:id,fecha_cierre:fecha_cierre,nombre_negocio:nombre_negocio,ruc:ruc,direccion:direccion,tipo_negocio:tipo_negocio,telefono:telefono,pagina_web:pagina_web,razon_social:razon_social,moneda:moneda,serie_boleta:serie_boleta,serie_factura:serie_factura,almacen_principal:almacen_principal,estado_fila:estado_fila,correoEmisor:correoEmisor ,ruta:ruta, token:token, ip_publica_cliente_os_ticket:ip_publica_cliente_os_ticket, key_os_ticket:key_os_ticket, url_os_ticket:url_os_ticket,logo_ticket:logo_ticket,logo_boleta:logo_boleta,logo_factura:logo_factura,id_detraccion  }, function(data) {
if(data === 0){
$('body,html').animate({
scrollTop: 0
}, 800);
$('#merror').show('fast').delay(4000).hide('fast');
}
else{
$('#frmall').reset();
$('body,html').animate({
scrollTop: 0
}, 800);
$('#msuccess').show('fast').delay(3000).hide('fast');
//alert("Se actualizo correctamente");
location.reload();
}
//location.reload();
}, 'json');
}

function sel(id){
$.post('ws/configuracion.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

$('#fecha_cierre').val(data.fecha_cierre);

$('#nombre_negocio').val(data.nombre_negocio);

$('#ruc').val(data.ruc);

$('#direccion').val(data.direccion);

$('#tipo_negocio').val(data.tipo_negocio);

$('#telefono').val(data.telefono);

$('#pagina_web').val(data.pagina_web);

$('#razon_social').val(data.razon_social);

$('#moneda').val(data.moneda);

$('#serie_boleta').val(data.serie_boleta);

$('#serie_factura').val(data.serie_factura);

$('#estado_fila').val(data.estado_fila);

$('#almacen_principal').val(data.almacen_principal);

$('#correo_emisor').val(data.correoEmisor);

$('#ruta').val(data.ruta);

$('#token').val(data.token);

$('#url_os_ticket').val(data.url_os_ticket);
$('#key_os_ticket').val(data.key_os_ticket);
$('#ip_publica_cliente_os_ticket').val(data.ip_publica_cliente_os_ticket);

$('#logo_ticket').val(data.logo_ticket);
$('#logo_boleta').val(data.logo_boleta);
$('#logo_factura').val(data.logo_factura);

$('#id_detraccion').val(data.id_detraccion);

}
}, 'json');
}

$(document).ready(function() {
sel(1);
});

function save(){
update();
}

function upload_image(){
    console.log("//////");
var id = $("#idimg").val();
var archivos = document.getElementById("imge");

var arc = 0;
try {
arc = archivos.files;
console.log(arc.length);
}
catch (err)
{
    console.log(err);
}

var data = new FormData();
if(arc.length==0){
    alert("seleccionar una imagen");
}else{

for (i = 0; i <arc.length; i++) {
    data.append('img', arc[i]);
    }

    data.append('op','img');
  
    console.log(data);
    var request = $.ajax({
    url: 'ws/configuracion.php',
    type: 'POST',
    contentType: false,
    data: data,
    processData: false,
    cache: false
    });
    console.log(request);
    request.done(function() {
    $("#imge").val("");
    $('#modal_imagen').modal('hide');
    $('body,html').animate({
    scrollTop: 0
    }, 800);
    $('#msuccess').show('fast').delay(4000).hide('fast');
    //location.reload();
    });
    request.fail(function() {
    $("#imge").val("");
    $('#modal_imagen').modal('hide');
    });
    }

}


