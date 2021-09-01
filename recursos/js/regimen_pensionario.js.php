<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
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

});

function save(){
    var vid = $('#id').val();
    if(vid === '0')
        insert();
    else
        update();
}

function insert(){
    var codigo = $('#codigo').val();
    var descripcion = $('#descripcion').val();
    var comisionporcentual = $('#comisionporcentual').val();
    var primaseguro = $('#primaseguro').val();
    var aportacionobl = $('#aportacionobl').val();
    var comi_sf = $('#comisionporcentual_sf').val();

    $.post('ws/cliente.php', {
        op: 'addRegimenPensionario',
        codigo:codigo,
        descripcion:descripcion,
        comisionporcentual:comisionporcentual,
        primaseguro:primaseguro,
        aportacionobl:aportacionobl,
        comi_sf:comi_sf
    }, function(data) {
        console.log(data);

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
    var codigo = $('#codigo').val();
    var descripcion = $('#descripcion').val();
    var comisionporcentual = $('#comisionporcentual').val();
    var primaseguro = $('#primaseguro').val();
    var aportacionobl = $('#aportacionobl').val();
    var estado_fila = $('#estado_fila').val();
    var comi_sf = $('#comisionporcentual_sf').val();
    
    $.post('ws/cliente.php', {
        op: 'upRegimenPensionario',
        codigo:codigo,
        descripcion:descripcion,
        comisionporcentual:comisionporcentual,
        primaseguro:primaseguro,
        aportacionobl:aportacionobl,
        estado_fila:estado_fila,
        comi_sf:comi_sf
    }, function(data) {
        console.log(data);

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
    $.post('ws/cliente.php', {
        op: 'getRegimenPensionario', 
        codigo: id
    }, function(data) {
        console.log(data);
        if(data !== 0){
            $('#id').val(data.id);
            $('#codigo').val(data.id);
            $('#descripcion').val(data.nombre);
            $('#comisionporcentual').val(data.comision_porcentual);
            $('#primaseguro').val(data.prima_seguro);
            $('#aportacionobl').val(data.aportacion_obligatoria);
            $('#comisionporcentual_sf').val(data.comision_porcentual_sf);
        }
    }, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/cliente.php', {
            op: 'delRegimenPensionario', 
            id: id
        }, function (data) {
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