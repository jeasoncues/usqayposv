<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var tipo_taxonomia = $("#tipo_taxonomia").val();

var id = $('#id').val();

var id_producto = $('#id_producto').val();

var id_taxonomiap = $('#id_taxonomiap').val();

var valor = null;
if(parseInt(tipo_taxonomia) === 1){
    valor = $('#valor_a').val();
}else{
    valor = $('#valor_r').val();
}

var estado_fila = $('#estado_fila').val();

$.post('ws/producto_taxonomiap.php', {op: 'add',id:id,id_producto:id_producto,id_taxonomiap:id_taxonomiap,valor:valor,estado_fila:estado_fila}, function(data) {
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
var tipo_taxonomia = $("#tipo_taxonomia").val();

var id = $('#id').val();

var id_producto = $('#id_producto').val();

var id_taxonomiap = $('#id_taxonomiap').val();

var valor = null;
if(parseInt(tipo_taxonomia) === 1){
    valor = $('#valor_a').val();
}else{
    valor = $('#valor_r').val();
}

var estado_fila = $('#estado_fila').val();

$.post('ws/producto_taxonomiap.php', {op: 'mod',id:id,id_producto:id_producto,id_taxonomiap:id_taxonomiap,valor:valor,estado_fila:estado_fila}, function(data) {
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
$.post('ws/producto_taxonomiap.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_producto(data.id_producto.id);
sel_id_taxonomiap(data.id_taxonomiap.id,"'"+data.valor+"'");

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id){
$.post('ws/producto_taxonomiap.php', {op: 'del', id: id}, function(data) {
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

$.post('ws/taxonomiap.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_taxonomiap').html('');
var ht = '';
$.each(data, function(key, value) {
    if(parseInt(value.id)>1){
        ht += '<tr>';
        ht += '<td><a href="#" onclick="sel_id_taxonomiap('+value.id+')">SEL</a></td>';
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
$('#data_tbl_modal_id_taxonomiap').html(ht);
$('#tbl_modal_id_taxonomiap').dataTable();
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

function sel_id_producto(id_e){
    $.post('ws/producto.php', {op: 'get', id:id_e}, function(data) {
        if(data != 0){
            $('#id_producto').val(data.id);
            $('#txt_id_producto').html(data.nombre);
            $('#modal_id_producto').modal('hide');
        }
    }, 'json');
}

function sel_id_taxonomiap(id_e,nvalo = ''){
    $.post('ws/taxonomiap.php', {op: 'get', id:id_e}, function(data) {
        if(data != 0){
            $.post('ws/taxonomiap_valor.php', {op: 'listbytp', id:id_e}, function(data0) {
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
            $('#id_taxonomiap').val(data.id);
            $('#txt_id_taxonomiap').html(data.nombre);
            $('#modal_id_taxonomiap').modal('hide');
        }
    }, 'json');
}