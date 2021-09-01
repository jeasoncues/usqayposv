<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var nombre = $('#nombre').val();

var precio_compra = $('#precio_compra').val();

var precio_venta = $('#precio_venta').val();

var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/producto.php', {op: 'add',id:id,nombre:nombre,precio_compra:precio_compra,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila}, function(data) {
if(data === 0){
    $('body,html').animate({scrollTop: 0}, 800);
    $('#merror').show('fast').delay(4000).hide('fast');
}
else{
    $('body,html').animate({scrollTop: 0}, 800);
    $('#msuccess').show('fast').delay(4000).hide('fast');
    $("#frmtaxos").attr("src","producto_taxonomiap.php?id="+data);
    $("#panel_save").hide("fast");
    $("#cifrmtaxos").show("fast");
    $("#panel_end").show("fast");
}
}, 'json');
}

function update(){
var id = $('#id').val();

var nombre = $('#nombre').val();

var precio_compra = $('#precio_compra').val();

var precio_venta = $('#precio_venta').val();

var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/producto.php', {op: 'mod',id:id,nombre:nombre,precio_compra:precio_compra,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila}, function(data) {
if(data === 0){
    $('body,html').animate({scrollTop: 0}, 800);
    $('#merror').show('fast').delay(4000).hide('fast');
}
else{
    $('body,html').animate({scrollTop: 0}, 800);
    $('#msuccess').show('fast').delay(4000).hide('fast');
    $("#frmtaxos").attr("src","producto_taxonomiap.php?id="+id);
    $("#panel_save").hide("fast");
    $("#cifrmtaxos").show("fast");
    $("#panel_end").show("fast");
}
}, 'json');
}

function sel(id){
$.post('ws/producto.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

$('#nombre').val(data.nombre);

$('#precio_compra').val(data.precio_compra);

$('#precio_venta').val(data.precio_venta);

$('#incluye_impuesto option[value="'+data.incluye_impuesto+'"]').attr('selected', true);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/producto.php', {op: 'del', id: id}, function (data) {
            if (data === 0) {
                $('body,html').animate({scrollTop: 0}, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
                $('body,html').animate({scrollTop: 0}, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
    }
}

$(document).ready(function() {
var tbl = $('#tb').DataTable({
    responsive: true,
        "order": [[ 0, "desc" ]],
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
});
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
    $("#muestra").attr("src","recursos/uploads/productos/"+id+".png");
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
        url: 'ws/producto.php',
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



