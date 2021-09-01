<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function evalua_insert(){    
    if($('#fecha_realizada').val().length==0 || $('#numero_guia_salida').val().length==0 || $('#numero_guia_entrada').val().length==0 ){
        if($('#fecha_realizada').val().length==0){
            $.notify("Ingrese una Fecha")
            return false
        }

        if($('#numero_guia_salida').val().length==0){
            $.notify("Ingrese Un Numero de Guia de Salida")
            return false
        }

        if($('#numero_guia_entrada').val().length==0){
            $.notify("Ingrese Un Numero de Guia de Entrada")
            return false
        }
    }

    if($('#fecha_realizada').val().length>0 && $('#numero_guia_salida').val().length>0 && $('#numero_guia_entrada').val().length>0 ){
        return true
    }

    $.notify("Ingrese Datos")
    return false


}
function insert(){
var id_usuario = $('#id_usuario').val();

var fecha_realizada = $('#fecha_realizada').val();

var numero_guia_salida = $('#numero_guia_salida').val();

var numero_guia_entrada = $('#numero_guia_entrada').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/guia_producto.php', {op: 'addmov',id_usuario:id_usuario,fecha_realizada:fecha_realizada,numero_guia_entrada:numero_guia_entrada,numero_guia_salida:numero_guia_salida,estado_fila:estado_fila}, function(data) {
    if(data === 0){
        $('body,html').animate({scrollTop: 0}, 800);
        $('#merror').show('fast').delay(4000).hide('fast');
    }
    else{
        $('body,html').animate({scrollTop: 0}, 800);
        $('#msuccess').show('fast').delay(4000).hide('fast');
        location.reload();
    }
}, 'json');
}

function update(){
var id = $('#id').val();

var id_usuario = $('#id_usuario').val();

var fecha_realizada = $('#fecha_realizada').val();

var numero_guia_salida = $('#numero_guia_salida').val();

var numero_guia_entrada = $('#numero_guia_entrada').val();

$.post('ws/guia_producto.php', {op: 'modmov',id:id,id_usuario:id_usuario,fecha_realizada:fecha_realizada,numero_guia_salida:numero_guia_salida,numero_guia_entrada:numero_guia_entrada}, function(data) {
    if(data === 0){
        $('body,html').animate({scrollTop: 0}, 800);
        $('#merror').show('fast').delay(4000).hide('fast');
    }
    else{
        $('body,html').animate({scrollTop: 0}, 800);
        $('#msuccess').show('fast').delay(4000).hide('fast');
        location.reload();
    }
}, 'json');
}

function sel(id) {
    $.post('ws/guia_producto.php', {op: 'getmov', id: id}, function (data) {
        if (data !== 0) {

            $('#id').val(data.id);

            $('#id_usuario').val(data.salida.id_usuario.id);

            $('#fecha_realizada').val(data.salida.fecha_realizada);

            $('#numero_guia_salida').val(data.salida.numero_guia);

            $('#numero_guia_entrada').val(data.entrada.numero_guia);

            $('#estado_fila').val(data.estado_fila);

        }
    }, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/guia_producto.php', {op: 'delmov', id: id}, function (data) {
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
var tbl = $('#tb').dataTable();
tbl.fnSort( [ [0,'desc'] ] );

$('#fecha_realizada').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

});

function save(){
var vid = $('#id').val();
if(vid === '0')
{
    if(evalua_insert()){
        insert()
    }
}
else
{
update();
}
}