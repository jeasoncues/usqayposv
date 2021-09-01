<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function aviso_edit_mea(){
    $.notify("Solo se puede editar desde Movimiento Almacenes")
}

function aviso_delete_mea(){
    $.notify("Solo se puede eliminar desde Movimiento Almacenes")
}

function insert(){
var id_usuario = $('#id_usuario').val();

var fecha_realizada = $('#fecha_realizada').val();

var tipo = $('#tipo').find('option:selected').val();

var numero_guia = $('#numero_guia').val();

var id_proveedor = $('#id_proveedor').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/guia_producto.php', {op: 'add',id_usuario:id_usuario,fecha_realizada:fecha_realizada,tipo:tipo,numero_guia:numero_guia,id_proveedor:id_proveedor,estado_fila:estado_fila}, function(data) {
    if(data === 0){
        $('body,html').animate({scrollTop: 0}, 800);
        $('#merror').show('fast').delay(4000).hide('fast');
    }
    else{
        $('body,html').animate({scrollTop: 0}, 800);
        $('#msuccess').show('fast').delay(4000).hide('fast');
        location.reload();
    }
}, 'json');
}

function update(){
var id = $('#id').val();

var id_usuario = $('#id_usuario').val();

var fecha_realizada = $('#fecha_realizada').val();

var tipo = $('#tipo').find('option:selected').val();

var numero_guia = $('#numero_guia').val();

var id_proveedor = $('#id_proveedor').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/guia_producto.php', {op: 'mod',id:id,id_usuario:id_usuario,fecha_realizada:fecha_realizada,tipo:tipo,numero_guia:numero_guia,id_proveedor:id_proveedor,estado_fila:estado_fila}, function(data) {
    if(data === 0){
        $('body,html').animate({scrollTop: 0}, 800);
        $('#merror').show('fast').delay(4000).hide('fast');
    }
    else{
        $('body,html').animate({scrollTop: 0}, 800);
        $('#msuccess').show('fast').delay(4000).hide('fast');
        location.reload();
    }
}, 'json');
}

function sel(id){
$.post('ws/guia_producto.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

$('#id_usuario').val(data.id_usuario.id);

$('#fecha_registro').val(data.fecha_registro);

$('#fecha_realizada').val(data.fecha_realizada);

$('#numero_guia').val(data.numero_guia);

// $('#tipo option[value="'+data.tipo+'"]').attr('selected', true);
$('#tipo').val(data.tipo);

$('#estado_fila').val(data.estado_fila);
if( data.id_proveedor!="" ){
    sel_id_proveedor(data.id_proveedor.id);
}else{
    sel_id_proveedor(0);
}


}
}, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/guia_producto.php', {op: 'del', id: id}, function (data) {
            if (data === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
    }
}

$(document).ready(function() {
var tbl = $('#tb').dataTable();
tbl.fnSort( [ [0,'desc'] ] );

$('#fecha_realizada').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

$.post('ws/proveedor.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_proveedor').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_proveedor('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.razon_social+'</td>';
    ht += '<td>'+value.ruc+'</td>';
    ht += '<td>'+value.direccion+'</td>';
    ht += '<td>'+value.telefono+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_proveedor').html(ht);
$('#tbl_modal_id_proveedor').dataTable();
}
}, 'json');

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

function sel_id_proveedor(id_e){
$.post('ws/proveedor.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
    $('#id_proveedor').val(data.id);
    $('#txt_id_proveedor').html(data.razon_social);
    $('#modal_id_proveedor').modal('hide');
}else{
    $('#id_proveedor').val("");
    $('#txt_id_proveedor').html("...");
    $('#modal_id_proveedor').modal('hide');
}
}, 'json');
}