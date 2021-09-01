<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var nombre = $('#nombre').val();

var precio_venta = $('#precio_venta').val();

var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/servicio.php', {op: 'add',id:id,nombre:nombre,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila}, function(data){
    if(data === 0){
        $('body,html').animate({scrollTop: 0}, 800);
        $('#merror').show('fast').delay(4000).hide('fast');
    }
    else{
        $('body,html').animate({scrollTop: 0}, 800);
        $('#msuccess').show('fast').delay(4000).hide('fast');
        $("#frmpro").attr("src","servicio_producto.php?id="+data);
        $("#cifrmpro").show("fast");
        $("#frmatr").attr("src","servicio_taxonimias.php?id="+data);
        $("#cifrmatr").show("fast");
        $("#panel_save").hide("fast");    
        $("#panel_end").show("fast");
    }
}, 'json');
}

function update(){
var id = $('#id').val();

var nombre = $('#nombre').val();

var descripcion = $('#descripcion').val();

var precio_venta = $('#precio_venta').val();

var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/servicio.php', {op: 'mod',id:id,nombre:nombre,descripcion:descripcion,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila}, function(data){
    if(data === 0){
        $('body,html').animate({scrollTop: 0}, 800);
        $('#merror').show('fast').delay(4000).hide('fast');
    }
    else{
        $('body,html').animate({scrollTop: 0}, 800);
        $('#msuccess').show('fast').delay(4000).hide('fast');
        $("#frmpro").attr("src","servicio_producto.php?id="+id);
        $("#cifrmpro").show("fast");
        $("#frmatr").attr("src","servicio_taxonimias.php?id="+id);
        $("#cifrmatr").show("fast");
        $("#panel_save").hide("fast");    
        $("#panel_end").show("fast");
    }
}, 'json');
}

function sel(id){
$.post('ws/servicio.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

$('#nombre').val(data.nombre);

$('#descripcion').val(data.descripcion);

$('#precio_venta').val(data.precio_venta);

$('#incluye_impuesto option[value="'+data.incluye_impuesto+'"]').attr('selected', true);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id){
$.post('ws/servicio.php', {op: 'del', id: id}, function(data) {
if(data === 0){
$('body,html').animate({
scrollTop: 0
}, 800);
$('#merror').show('fast').delay(4000).hide('fast');
}
else{
$('body,html').animate({
scrollTop: 0
}, 800);
$('#msuccess').show('fast').delay(4000).hide('fast');
location.reload();
}
}, 'json');
}

$(document).ready(function() {
var tbl = $('#tb').dataTable();
tbl.fnSort( [ [0,'desc'] ] );

});

function save(){
var vid = $('#id').val();
if(vid === '0')
{
insert();
}
else
{
update();
}
}

function finalizar(){
    $('#frmall').reset();
    location.reload();
}

function img(id){
    $("#muestra").attr("src","recursos/uploads/servicios/"+id+".png");
    $("#idimg").val(id);
    $('#modal_imagen').modal('show');
}

function upload_image(){
    var id = $("#idimg").val();
    var archivos = document.getElementById("imge");

    var arc = 0;
    try {
        arc = archivos.files;
    }
    catch (err)
    {
    }

    var data = new FormData();

    for (i = 0; i <arc.length; i++) {
        data.append('img', arc[i]);
    }

    data.append('op','img');
    data.append('id',id);
    
    var request = $.ajax({
        url: 'ws/servicio.php',
        type: 'POST',
        contentType: false,
        data: data,
        processData: false,
        cache: false
    });
    request.done(function() {
        $("#imge").val("");
        $('#modal_imagen').modal('hide');
    });
    request.fail(function() {
        $("#imge").val("");
        $('#modal_imagen').modal('hide');
    });
}
