<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var id_servicio = $('#id_servicio').val();

var id_producto = $('#id_producto').val();

var cantidad = $('#cantidad').val();

var id_almacen = $('#id_almacen').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/servicio_producto.php', {op: 'add',id:id,id_servicio:id_servicio,id_producto:id_producto,cantidad:cantidad,id_almacen:id_almacen,estado_fila:estado_fila}, function(data) {
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

var id_servicio = $('#id_servicio').val();

var id_producto = $('#id_producto').val();

var cantidad = $('#cantidad').val();

var id_almacen = $('#id_almacen').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/servicio_producto.php', {op: 'mod',id:id,id_servicio:id_servicio,id_producto:id_producto,cantidad:cantidad,id_almacen:id_almacen,estado_fila:estado_fila}, function(data) {
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
$.post('ws/servicio_producto.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_servicio(data.id_servicio.id);
sel_id_producto(data.id_producto.id);
$('#cantidad').val(data.cantidad);

sel_id_almacen(data.id_almacen.id);
$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id){
$.post('ws/servicio_producto.php', {op: 'del', id: id}, function(data) {
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

function sel_id_servicio(id_e){
$.post('ws/servicio.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_servicio').val(data.id);
$('#txt_id_servicio').html(data.<?php echo $gl_servicio_producto_id_servicio;?>);
$('#modal_id_servicio').modal('hide');
}
}, 'json');
}

function sel_id_producto(id_e){
$.post('ws/producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_producto').val(data.id);
$('#txt_id_producto').html(data.<?php echo $gl_servicio_producto_id_producto;?>);
$('#modal_id_producto').modal('hide');
}
}, 'json');
}

function sel_id_almacen(id_e){
$.post('ws/almacen.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_almacen').val(data.id);
$('#txt_id_almacen').html(data.<?php echo $gl_servicio_producto_id_almacen;?>);
$('#modal_id_almacen').modal('hide');
}
}, 'json');
}