<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var id_compra = $('#id_compra').val();

var id_guia_producto = $('#id_guia_producto').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/compra_guia.php', {op: 'add',id:id,id_compra:id_compra,id_guia_producto:id_guia_producto,estado_fila:estado_fila}, function(data) {
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

var id_compra = $('#id_compra').val();

var id_guia_producto = $('#id_guia_producto').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/compra_guia.php', {op: 'mod',id:id,id_compra:id_compra,id_guia_producto:id_guia_producto,estado_fila:estado_fila}, function(data) {
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
$.post('ws/compra_guia.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_compra(data.id_compra.id);
sel_id_guia_producto(data.id_guia_producto.id);
$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}
function del(id){
$.post('ws/compra_guia.php', {op: 'del', id: id}, function(data) {
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

$.post('ws/compra.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_compra').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_compra('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.id_usuario.nombres_y_apellidos+'</td>';
    ht += '<td>'+value.id_proveedor.razon_social+'</td>';
    ht += '<td>'+value.categoria+'</td>';
    ht += '<td>'+value.monto_total+'</td>';
    ht += '<td>'+value.fecha+'</td>';
    ht += '<td>'+value.monto_pendiente+'</td>';
    ht += '<td>'+value.id_caja.nombre+'</td>';
    ht += '<td>'+value.proximo_pago+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_compra').html(ht);
$('#tbl_modal_id_compra').dataTable();
}
}, 'json');

$.post('ws/guia_producto.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_guia_producto').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_guia_producto('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.id_usuario.nombres_y_apellidos+'</td>';
    ht += '<td>'+value.fecha_registro+'</td>';
    ht += '<td>'+value.fecha_realizada+'</td>';
    ht += '<td>'+value.tipo+'</td>';
    ht += '<td>'+value.id_proveedor.razon_social+'</td>';
    ht += '<td>'+value.numero_guia+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_guia_producto').html(ht);
$('#tbl_modal_id_guia_producto').dataTable();
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

function sel_id_compra(id_e){
$.post('ws/compra.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_compra').val(data.id);
$('#txt_id_compra').html(data.numero_documento);
$('#modal_id_compra').modal('hide');
}
}, 'json');
}
function sel_id_guia_producto(id_e){
$.post('ws/guia_producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_guia_producto').val(data.id);
$('#txt_id_guia_producto').html(data.id);
$('#modal_id_guia_producto').modal('hide');
}
}, 'json');
}