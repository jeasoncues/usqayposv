<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var tipo_taxonomia = $("#tipo_taxonomia").val();

var id = $('#id').val();

var id_servicio = $('#id_servicio').val();

var id_taxonomias = $('#id_taxonomias').val();

var valor = null;
if(parseInt(tipo_taxonomia) === 1){
    valor = $('#valor_a').val();
}else{
    valor = $('#valor_r').val();
}

var estado_fila = $('#estado_fila').val();

$.post('ws/servicio_taxonimias.php', {op: 'add',id:id,id_servicio:id_servicio,id_taxonomias:id_taxonomias,valor:valor,estado_fila:estado_fila}, function(data) {
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

var id_taxonomias = $('#id_taxonomias').val();

var valor = null;
if(parseInt(tipo_taxonomia) === 1){
    valor = $('#valor_a').val();
}else{
    valor = $('#valor_r').val();
}

var estado_fila = $('#estado_fila').val();

$.post('ws/servicio_taxonimias.php', {op: 'mod',id:id,id_servicio:id_servicio,id_taxonomias:id_taxonomias,valor:valor,estado_fila:estado_fila}, function(data) {
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
$.post('ws/servicio_taxonimias.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_servicio(data.id_servicio.id);

sel_id_taxonomias(data.id_taxonomias.id,"'"+data.valor+"'");

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id){
$.post('ws/servicio_taxonimias.php', {op: 'del', id: id}, function(data) {
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
$('#data_tbl_modal_id_taxonomias').html('');
var ht = '';
$.each(data, function(key, value) {
    if(parseInt(value.id)>1){
        ht += '<tr>';
        ht += '<td><a href="#" onclick="sel_id_taxonomias('+value.id+')">SEL</a></td>';
        ht += '<td>'+value.id+'</td>';
        if(value.padre !== null){
            ht += '<td>'+value.padre.nombre+'</td>';
        }else{
            ht += '<td></td>';
        }
        ht += '<td>'+value.nombre+'</td>';
        ht += '<td>'+value.tipo_valor+'</td>';
        ht += '</tr>';
    }
});
$('#data_tbl_modal_id_taxonomias').html(ht);
$('#tbl_modal_id_taxonomias').dataTable();
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
$('#txt_id_servicio').html(data.nombre);
$('#modal_id_servicio').modal('hide');
}
}, 'json');
}

function sel_id_taxonomias(id_e,nvalo = ''){
    $.post('ws/taxonomias.php', {op: 'get', id:id_e}, function(data) {
        if(data != 0){
            $.post('ws/taxonomias_valor.php', {op: 'listbytp', id:id_e}, function(data0) {
                if(data0 != 0){
                    $('#tipo_taxonomia').val('2');
                    var ht = "";
                    $.each(data0, function(key, value) {
                        if(value.valor == nvalo){
                            ht += '<option value="'+value.valor+'" selected>'+value.valor+'</option>';
                        }else{
                            ht += '<option value="'+value.valor+'">'+value.valor+'</option>';
                        }
                    });
                    $("#valor_r").html(ht);
                    $("#valor_rango").show('fast');
                }else{
                    $("#valor_r").val(nvalo);
                    $('#tipo_taxonomia').val('1');
                    $("#valor_abierto").show('fast');
                }
            }, 'json');
            $('#id_taxonomias').val(data.id);
            $('#txt_id_taxonomias').html(data.nombre);
            $('#modal_id_taxonomias').modal('hide');
        }
    }, 'json');
}