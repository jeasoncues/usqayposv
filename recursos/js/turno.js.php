<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert(){
var id = $('#id').val();

var nombre = $('#nombre').val();

var inicio = $('#inicio').val();

var fin = $('#fin').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/turno.php', {op: 'add',id:id,nombre:nombre,inicio:inicio,fin:fin,estado_fila:estado_fila}, function(data) {
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

var nombre = $('#nombre').val();

var inicio = $('#inicio').val();

var fin = $('#fin').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/turno.php', {op: 'mod',id:id,nombre:nombre,inicio:inicio,fin:fin,estado_fila:estado_fila}, function(data) {
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
$.post('ws/turno.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

$('#nombre').val(data.nombre);

$('#inicio').val(data.inicio);

$('#fin').val(data.fin);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}
function del(id) {
    if (confirm("¿Desea eliminar este elemento?")) {
        $.post('ws/turno.php', {op: 'del', id: id}, function (data) {
            if (data === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
    }
}

$(document).ready(function() {
var tbl = $('#tb').DataTable({
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

$('#datetimepicker3').datetimepicker({
format: 'HH:mm:ss'
})
$('#datetimepicker4').datetimepicker({
format: 'HH:mm:ss'
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
