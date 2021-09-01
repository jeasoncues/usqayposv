<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var id_taxonomiav = $('#id_taxonomiav').val();

var valor = $('#valor').val();

var padre = $('#padre').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/taxonomiav_valor.php', {op: 'add',id:id,id_taxonomiav:id_taxonomiav,valor:valor,padre:padre,estado_fila:estado_fila}, function(data) {
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

var id_taxonomiav = $('#id_taxonomiav').val();

var valor = $('#valor').val();

var padre = $('#padre').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/taxonomiav_valor.php', {op: 'mod',id:id,id_taxonomiav:id_taxonomiav,valor:valor,padre:padre,estado_fila:estado_fila}, function(data) {
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
$.post('ws/taxonomiav_valor.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_taxonomiav(data.id_taxonomiav.id);

$('#valor').val(data.valor);

sel_padre(data.padre);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id){
$.post('ws/taxonomiav_valor.php', {op: 'del', id: id}, function(data) {
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

$.post('ws/taxonomiav_valor.php', {op: 'listpadre',id_padre:padre_tax}, function(data) {
if(data != 0){
$('#data_tbl_modal_padre').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_padre('+value.id+')">SEL</a></td>'; 
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.id_taxonomias.nombre+'</td>';
    ht += '<td>'+value.valor+'</td>';
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

function sel_id_taxonomiav(id_e){
$.post('ws/taxonomiav.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_taxonomiav').val(data.id);
$('#txt_id_taxonomiav').html(data.<?php echo $gl_taxonomiav_valor_id_taxonomiav;?>);
$('#modal_id_taxonomiav').modal('hide');
}
}, 'json');
}

function sel_padre(id_e){
$.post('ws/taxonomiav_valor.php', {op: 'get', id:id_e}, function(data) {
    if(data != 0){
    $('#padre').val(data.id);
    $('#txt_padre').html(data.valor);
    $('#modal_padre').modal('hide');
    }else{
    $('#padre').val('');
    $('#txt_padre').html('...');
    $('#modal_padre').modal('hide');
    }
}, 'json');
}
