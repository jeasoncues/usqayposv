<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var id_movimiento_producto = $('#id_movimiento_producto').val();

var id_guia_producto = $('#id_guia_producto').val();

var numero_guia = $('#numero_guia').val();

var id_proveedor = $('#id_proveedor').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/guia_movimiento.php', {op: 'add',id:id,id_movimiento_producto:id_movimiento_producto,id_guia_producto:id_guia_producto,numero_guia:numero_guia,id_proveedor:id_proveedor,estado_fila:estado_fila}, function(data) {
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

var id_movimiento_producto = $('#id_movimiento_producto').val();

var id_guia_producto = $('#id_guia_producto').val();

var numero_guia = $('#numero_guia').val();

var id_proveedor = $('#id_proveedor').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/guia_movimiento.php', {op: 'mod',id:id,id_movimiento_producto:id_movimiento_producto,id_guia_producto:id_guia_producto,numero_guia:numero_guia,id_proveedor:id_proveedor,estado_fila:estado_fila}, function(data) {
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
$.post('ws/guia_movimiento.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_movimiento_producto(data.id_movimiento_producto.id);
sel_id_guia_producto(data.id_guia_producto.id);
$('#numero_guia').val(data.numero_guia);

sel_id_proveedor(data.id_proveedor.id);
$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}
function del(id){
$.post('ws/guia_movimiento.php', {op: 'del', id: id}, function(data) {
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

$.post('ws/movimiento_producto.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_movimiento_producto').html('');
var ht = '';
$.each(data, function(key, value) {
ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_movimiento_producto('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.id_producto.<?php
        echo $gl_movimiento_producto_id_producto;
        ?>+'</td>';ht += '<td>'+value.id_almacen.<?php
        echo $gl_movimiento_producto_id_almacen;
        ?>+'</td>';ht += '<td>'+value.cantidad+'</td>';ht += '<td>'+value.costo+'</td>';ht += '<td>'+value.tipo_movimiento+'</td>';ht += '<td>'+value.id_usuario.<?php
            echo $gl_movimiento_producto_id_usuario;
            ?>+'</td>';ht += '<td>'+value.id_turno.<?php
            echo $gl_movimiento_producto_id_turno;
            ?>+'</td>';ht += '<td>'+value.fecha+'</td>';ht += '<td>'+value.fecha_cierre+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_movimiento_producto').html(ht);
$('#tbl_modal_id_movimiento_producto').dataTable();
}
}, 'json');
$.post('ws/guia_producto.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_guia_producto').html('');
var ht = '';
$.each(data, function(key, value) {
ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_guia_producto('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.id_usuario.<?php
        echo $gl_guia_producto_id_usuario;
        ?>+'</td>';ht += '<td>'+value.fecha_registro+'</td>';ht += '<td>'+value.fecha_realizada+'</td>';ht += '<td>'+value.tipo+'</td>';ht += '<td>'+value.categoria+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_guia_producto').html(ht);
$('#tbl_modal_id_guia_producto').dataTable();
}
}, 'json');
$.post('ws/proveedor.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_proveedor').html('');
var ht = '';
$.each(data, function(key, value) {
ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_proveedor('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.razon_social+'</td>';ht += '<td>'+value.ruc+'</td>';ht += '<td>'+value.direccion+'</td>';ht += '<td>'+value.telefono+'</td>';
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

function sel_id_movimiento_producto(id_e){
$.post('ws/movimiento_producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_movimiento_producto').val(data.id);
$('#txt_id_movimiento_producto').html(data.<?php
echo $gl_guia_movimiento_id_movimiento_producto;
?>);
$('#modal_id_movimiento_producto').modal('hide');
}
}, 'json');
}
function sel_id_guia_producto(id_e){
$.post('ws/guia_producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_guia_producto').val(data.id);
$('#txt_id_guia_producto').html(data.<?php
echo $gl_guia_movimiento_id_guia_producto;
?>);
$('#modal_id_guia_producto').modal('hide');
}
}, 'json');
}
function sel_id_proveedor(id_e){
$.post('ws/proveedor.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_proveedor').val(data.id);
$('#txt_id_proveedor').html(data.<?php
echo $gl_guia_movimiento_id_proveedor;
?>);
$('#modal_id_proveedor').modal('hide');
}
}, 'json');
}