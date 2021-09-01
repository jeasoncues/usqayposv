<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var subtotal = $('#subtotal').val();

var total_impuestos = $('#total_impuestos').val();

var total = $('#total').val();

var tipo_comprobante = $('#tipo_comprobante').find('option:selected').val();

var fecha_hora = $('#fecha_hora').val();

var fecha_cierre = $('#fecha_cierre').val();

var id_turno = $('#id_turno').val();

var id_usuario = $('#id_usuario').val();

var id_caja = $('#id_caja').val();

var id_cliente = $('#id_cliente').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/venta.php', {op: 'add',id:id,subtotal:subtotal,total_impuestos:total_impuestos,total:total,tipo_comprobante:tipo_comprobante,fecha_hora:fecha_hora,fecha_cierre:fecha_cierre,id_turno:id_turno,id_usuario:id_usuario,id_caja:id_caja,id_cliente:id_cliente,estado_fila:estado_fila}, function(data) {
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

var subtotal = $('#subtotal').val();

var total_impuestos = $('#total_impuestos').val();

var total = $('#total').val();

var tipo_comprobante = $('#tipo_comprobante').find('option:selected').val();

var fecha_hora = $('#fecha_hora').val();

var fecha_cierre = $('#fecha_cierre').val();

var id_turno = $('#id_turno').val();

var id_usuario = $('#id_usuario').val();

var id_caja = $('#id_caja').val();

var id_cliente = $('#id_cliente').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/venta.php', {op: 'mod',id:id,subtotal:subtotal,total_impuestos:total_impuestos,total:total,tipo_comprobante:tipo_comprobante,fecha_hora:fecha_hora,fecha_cierre:fecha_cierre,id_turno:id_turno,id_usuario:id_usuario,id_caja:id_caja,id_cliente:id_cliente,estado_fila:estado_fila}, function(data) {
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
$.post('ws/venta.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

$('#subtotal').val(data.subtotal);

$('#total_impuestos').val(data.total_impuestos);

$('#total').val(data.total);

$('#tipo_comprobante option[value="'+data.tipo_comprobante+'"]').attr('selected', true);

$('#fecha_hora').val(data.fecha_hora);

$('#fecha_cierre').val(data.fecha_cierre);

sel_id_turno(data.id_turno.id);
sel_id_usuario(data.id_usuario.id);
sel_id_caja(data.id_caja.id);
sel_id_cliente(data.id_cliente.id);
$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}
function del(id){
$.post('ws/venta.php', {op: 'del', id: id}, function(data) {
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
var search = localStorage.getItem('search-venta') ? localStorage.getItem('search-venta') : '';
var tbl = $('#tb').DataTable({
    "search": {
        "search": search
    },
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

tbl.on( 'search.dt', function () { 
    localStorage.setItem("search-venta", tbl.search());
});


$('#fecha_hora').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});
$('#fecha_cierre').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});
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
$.post('ws/caja.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_caja').html('');
var ht = '';
$.each(data, function(key, value) {
ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_caja('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.nombre+'</td>';ht += '<td>'+value.ubicacion+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_caja').html(ht);
$('#tbl_modal_id_caja').dataTable();
}
}, 'json');
$.post('ws/cliente.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_cliente').html('');
var ht = '';
$.each(data, function(key, value) {
ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_cliente('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.nombre+'</td>';ht += '<td>'+value.documento+'</td>';ht += '<td>'+value.direccion+'</td>';ht += '<td>'+value.tipo_cliente+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_cliente').html(ht);
$('#tbl_modal_id_cliente').dataTable();
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

function sel_id_turno(id_e){
$.post('ws/turno.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_turno').val(data.id);
$('#txt_id_turno').html(data.<?php
echo $gl_venta_id_turno;
?>);
$('#modal_id_turno').modal('hide');
}
}, 'json');
}
function sel_id_usuario(id_e){
$.post('ws/usuario.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_usuario').val(data.id);
$('#txt_id_usuario').html(data.<?php
echo $gl_venta_id_usuario;
?>);
$('#modal_id_usuario').modal('hide');
}
}, 'json');
}
function sel_id_caja(id_e){
$.post('ws/caja.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_caja').val(data.id);
$('#txt_id_caja').html(data.<?php
echo $gl_venta_id_caja;
?>);
$('#modal_id_caja').modal('hide');
}
}, 'json');
}
function sel_id_cliente(id_e){
$.post('ws/cliente.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_cliente').val(data.id);
$('#txt_id_cliente').html(data.<?php
echo $gl_venta_id_cliente;
?>);
$('#modal_id_cliente').modal('hide');
}
}, 'json');
}