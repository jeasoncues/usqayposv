<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var id_taxonomias = $('#id_taxonomias').val();

var valor = $('#valor').val();

var padre = $('#padre').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/taxonomias_valor.php', {op: 'add',id:id,id_taxonomias:id_taxonomias,valor:valor,padre:padre,estado_fila:estado_fila}, function(data) {
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

var id_taxonomias = $('#id_taxonomias').val();

var valor = $('#valor').val();

var padre = $('#padre').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/taxonomias_valor.php', {op: 'mod',id:id,id_taxonomias:id_taxonomias,valor:valor,padre:padre,estado_fila:estado_fila}, function(data) {
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
$.post('ws/taxonomias_valor.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

sel_id_taxonomias(data.id_taxonomias.id);

$('#valor').val(data.valor);

sel_padre(data.padre);

$('#estado_fila').val(data.estado_fila);
}
}, 'json');
}

function del(id){
$.post('ws/taxonomias_valor.php', {op: 'del', id: id}, function(data) {
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

var padre_tax = $("#padre_tax").val();

$.post('ws/taxonomias_valor.php', {op: 'listpadre',id_padre:padre_tax}, function(data) {
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

function sel_id_taxonomias(id_e){
$.post('ws/taxonomias.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_taxonomias').val(data.id);
$('#txt_id_taxonomias').html(data.<?php echo $gl_taxonomias_valor_id_taxonomias;?>);
$('#modal_id_taxonomias').modal('hide');
}
}, 'json');
}

function img(id){
    $("#muestra").attr("src","recursos/uploads/valores_atributos_servicios/"+id+".png");
    $("#idimg").val(id);
    $('#modal_imagen').modal('show');
}

function upload_image(){
    var id = $("#idimg").val();
    var archivos = document.getElementById("imge");

    var arc = 0;
    try {
        arc = archivos.files;
    }
    catch (err)
    {
    }

    var data = new FormData();

    for (i = 0; i <arc.length; i++) {
        data.append('img', arc[i]);
    }

    data.append('op','img');
    data.append('id',id);
    
    var request = $.ajax({
        url: 'ws/taxonomias_valor.php',
        type: 'POST',
        contentType: false,
        data: data,
        processData: false,
        cache: false
    });
    request.done(function() {
        $("#imge").val("");
        $('#modal_imagen').modal('hide');
    });
    request.fail(function() {
        $("#imge").val("");
        $('#modal_imagen').modal('hide');
    });
}

function sel_padre(id_e){
$.post('ws/taxonomias_valor.php', {op: 'get', id:id_e}, function(data) {
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
