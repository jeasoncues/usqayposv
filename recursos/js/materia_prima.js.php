<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var id_almacen_origen = $('#id_almacen_origen').val();

var id_producto_origen = $('#id_producto_origen').val();

var id_producto_destino = $('#id_producto_destino').val();

var id_almacen_destino = $('#id_almacen_destino').val();

var cantidad = $('#cantidad').val();

var merma = $('#merma').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/materia_prima.php', {op: 'add',id:id,id_almacen_origen:id_almacen_origen,id_producto_origen:id_producto_origen,id_producto_destino:id_producto_destino,id_almacen_destino:id_almacen_destino,cantidad:cantidad,merma:merma,estado_fila:estado_fila}, function(data) {
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
$('#msuccess').show('fast').delay(4000).hide('fast');
location.reload();
}
}, 'json');
}

function update(){
var id = $('#id').val();

var id_almacen_origen = $('#id_almacen_origen').val();

var id_producto_origen = $('#id_producto_origen').val();

var id_producto_destino = $('#id_producto_destino').val();

var id_almacen_destino = $('#id_almacen_destino').val();

var cantidad = $('#cantidad').val();

var merma = $('#merma').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/materia_prima.php', {op: 'mod',id:id,id_almacen_origen:id_almacen_origen,id_producto_origen:id_producto_origen,id_producto_destino:id_producto_destino,id_almacen_destino:id_almacen_destino,cantidad:cantidad,merma:merma,estado_fila:estado_fila}, function(data) {
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
$('#msuccess').show('fast').delay(4000).hide('fast');
location.reload();
}
}, 'json');
}

function sel(id){
$.post('ws/materia_prima.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_almacen_origen(data.id_almacen_origen.id);
sel_id_producto_origen(data.id_producto_origen.id);
sel_id_producto_destino(data.id_producto_destino.id);
sel_id_almacen_destino(data.id_almacen_destino.id);
$('#cantidad').val(data.cantidad);

$('#merma').val(data.merma);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/materia_prima.php', {op: 'del', id: id}, function (data) {
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

$.post('ws/almacen.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_almacen_origen').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_almacen_origen('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.ubicacion+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_almacen_origen').html(ht);
$('#tbl_modal_id_almacen_origen').dataTable();
}
}, 'json');

$.post('ws/producto.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_producto_origen').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_producto_origen('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.precio_compra+'</td>';
    ht += '<td>'+value.precio_venta+'</td>';
    ht += '<td>'+value.incluye_impuesto+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_producto_origen').html(ht);
$('#tbl_modal_id_producto_origen').dataTable();
}
}, 'json');

$.post('ws/producto.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_producto_destino').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_producto_destino('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.precio_compra+'</td>';
    ht += '<td>'+value.precio_venta+'</td>';
    ht += '<td>'+value.incluye_impuesto+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_producto_destino').html(ht);
$('#tbl_modal_id_producto_destino').dataTable();
}
}, 'json');

$.post('ws/almacen.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_almacen_destino').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_almacen_destino('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.ubicacion+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_almacen_destino').html(ht);
$('#tbl_modal_id_almacen_destino').dataTable();
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

function sel_id_almacen_origen(id_e){
$.post('ws/almacen.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_almacen_origen').val(data.id);
$('#txt_id_almacen_origen').html(data.<?php echo $gl_materia_prima_id_almacen_origen;?>);
$('#modal_id_almacen_origen').modal('hide');
}
}, 'json');
}

function sel_id_producto_origen(id_e){
$.post('ws/producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_producto_origen').val(data.id);
$('#txt_id_producto_origen').html(data.<?php echo $gl_materia_prima_id_producto_origen;?>);
$('#modal_id_producto_origen').modal('hide');
}
}, 'json');
}

function sel_id_producto_destino(id_e){
$.post('ws/producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_producto_destino').val(data.id);
$('#txt_id_producto_destino').html(data.<?php echo $gl_materia_prima_id_producto_destino;?>);
$('#modal_id_producto_destino').modal('hide');
}
}, 'json');
}

function sel_id_almacen_destino(id_e){
$.post('ws/almacen.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_almacen_destino').val(data.id);
$('#txt_id_almacen_destino').html(data.<?php echo $gl_materia_prima_id_almacen_destino;?>);
$('#modal_id_almacen_destino').modal('hide');
}
}, 'json');
}