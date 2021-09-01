<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var id_producto = $('#id_producto').val();

var id_almacen = $('#id_almacen').val();

var cantidad = $('#cantidad').val();

var costo = $('#costo').val();

var tipo_movimiento = $('#tipo_movimiento').val();

var id_usuario = $('#id_usuario').val();

var id_turno = $('#id_turno').val();

var fecha = $('#fecha').val();

var fecha_cierre = $('#fecha_cierre').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/movimiento_producto.php', {op: 'add',id:id,id_producto:id_producto,id_almacen:id_almacen,cantidad:cantidad,costo:costo,tipo_movimiento:tipo_movimiento,id_usuario:id_usuario,id_turno:id_turno,fecha:fecha,fecha_cierre:fecha_cierre,estado_fila:estado_fila}, function(data) {
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

var id_producto = $('#id_producto').val();

var id_almacen = $('#id_almacen').val();

var cantidad = $('#cantidad').val();

var costo = $('#costo').val();

var tipo_movimiento = $('#tipo_movimiento').val();

var id_usuario = $('#id_usuario').val();

var id_turno = $('#id_turno').val();

var fecha = $('#fecha').val();

var fecha_cierre = $('#fecha_cierre').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/movimiento_producto.php', {op: 'mod',id:id,id_producto:id_producto,id_almacen:id_almacen,cantidad:cantidad,costo:costo,tipo_movimiento:tipo_movimiento,id_usuario:id_usuario,id_turno:id_turno,fecha:fecha,fecha_cierre:fecha_cierre,estado_fila:estado_fila}, function(data) {
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
$.post('ws/movimiento_producto.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_producto(data.id_producto.id);
sel_id_almacen(data.id_almacen.id);
$('#cantidad').val(data.cantidad);

$('#costo').val(data.costo);

$('#tipo_movimiento').val(data.tipo_movimiento);

sel_id_usuario(data.id_usuario.id);
sel_id_turno(data.id_turno.id);
$('#fecha').val(data.fecha);
$('#fecha_cierre').val(data.fecha_cierre);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id){
$.post('ws/movimiento_producto.php', {op: 'del', id: id}, function(data) {
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

$.post('ws/producto.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_producto').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_producto('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.descripcion+'</td>';
    ht += '<td>'+value.precio_compra+'</td>';
    ht += '<td>'+value.precio_venta+'</td>';
    ht += '<td>'+value.incluye_impuesto+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_producto').html(ht);
$('#tbl_modal_id_producto').dataTable();
}
}, 'json');

$.post('ws/almacen.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_almacen').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_almacen('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.ubicacion+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_almacen').html(ht);
$('#tbl_modal_id_almacen').dataTable();
}
}, 'json');

$.post('ws/usuario.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_usuario').html('');
var ht = '';
$.each(data, function(key, value) {
ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_usuario('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.documento+'</td>';ht += '<td>'+value.nombres_y_apellidos+'</td>';ht += '<td>'+value.tipo_usuario+'</td>';ht += '<td>'+value.password+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_usuario').html(ht);
$('#tbl_modal_id_usuario').dataTable();
}
}, 'json');
$.post('ws/turno.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_turno').html('');
var ht = '';
$.each(data, function(key, value) {
ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_turno('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.nombre+'</td>';ht += '<td>'+value.inicio+'</td>';ht += '<td>'+value.fin+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_turno').html(ht);
$('#tbl_modal_id_turno').dataTable();
}
}, 'json');
$('#fecha').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
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

function sel_id_producto(id_e){
$.post('ws/producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_producto').val(data.id);
$('#txt_id_producto').html(data.<?php
echo $gl_movimiento_producto_id_producto;
?>);
$('#modal_id_producto').modal('hide');
}
}, 'json');
}
function sel_id_almacen(id_e){
$.post('ws/almacen.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_almacen').val(data.id);
$('#txt_id_almacen').html(data.<?php
echo $gl_movimiento_producto_id_almacen;
?>);
$('#modal_id_almacen').modal('hide');
}
}, 'json');
}
function sel_id_usuario(id_e){
$.post('ws/usuario.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_usuario').val(data.id);
$('#txt_id_usuario').html(data.<?php
echo $gl_movimiento_producto_id_usuario;
?>);
$('#modal_id_usuario').modal('hide');
}
}, 'json');
}
function sel_id_turno(id_e){
$.post('ws/turno.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_turno').val(data.id);
$('#txt_id_turno').html(data.<?php
echo $gl_movimiento_producto_id_turno;
?>);
$('#modal_id_turno').modal('hide');
}
}, 'json');
}