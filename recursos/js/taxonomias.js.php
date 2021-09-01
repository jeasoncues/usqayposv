<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var padre = $('#padre').val();

var nombre = $('#nombre').val();

var tipo_valor = $('#tipo_valor').find('option:selected').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/taxonomias.php', {op: 'add',id:id,padre:padre,nombre:nombre,tipo_valor:tipo_valor,estado_fila:estado_fila}, function(data) {
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

var padre = $('#padre').val();

var nombre = $('#nombre').val();

var tipo_valor = $('#tipo_valor').find('option:selected').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/taxonomias.php', {op: 'mod',id:id,padre:padre,nombre:nombre,tipo_valor:tipo_valor,estado_fila:estado_fila}, function(data) {
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
$.post('ws/taxonomias.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_padre(data.padre);

$('#nombre').val(data.nombre);

$('#tipo_valor option[value="'+data.tipo_valor+'"]').attr('selected', true);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id){
$.post('ws/taxonomias.php', {op: 'del', id: id}, function(data) {
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

$.post('ws/taxonomias.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_padre').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_padre('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    if(value.padre !== null){
        ht += '<td>'+value.padre.nombre+'</td>';
    }else{
        ht += '<td></td>';
    }
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.tipo_valor+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_padre').html(ht);
$('#tbl_modal_padre').dataTable();
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

function sel_padre(id_e){
$.post('ws/taxonomias.php', {op: 'get', id:id_e}, function(data) {
    if(data != 0){
    $('#padre').val(data.id);
    $('#txt_padre').html(data.nombre);
    $('#modal_padre').modal('hide');
    }else{
    $('#padre').val('');
    $('#txt_padre').html('...');
    $('#modal_padre').modal('hide');
    }
}, 'json');
}