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

    $('#fecha_ingreso').datepicker({dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });

    $('#fecha_cese').datepicker({dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });

    $('#divFlujo').hide();

    $('#regimen_pensionario').change(function(){
        let valor = $('#regimen_pensionario').val();

        if(valor == 21 || valor == 22 || valor == 23 || valor == 24){
            $('#divFlujo').show();
            $('#tipo_flujo').show();
        }
        else{
            $('#tipo_flujo option[value="0"]').attr('selected', true);
            $('#divFlujo').hide();

        }

    })

});

function save(){
    var vid = $('#id').val();
    if(vid === '0')
        insert();
    else
        update();
}

function insert(){
    var id = $('#id').val();
    var nombre = $('#nombre').val();
    var documento = $('#documento').val();
    var tipo_documento = $('#tipo_documento').val();
    var sueldo = $('#sueldo').val();
    var situacion = $('#situacion').val();
    var ocupacion = $('#ocupacion').val();
    var contrato = $('#contrato').val();
    var condicion = $('#condicion').val();
    var fecha_ingreso = $('#fecha_ingreso').val();
    var fecha_cese = $('#fecha_cese').val();
    var quinta_categoria = 1;
    var asignacion_familiar = $('#asignacion_familiar').val();
    var regimen_pensionario = $('#regimen_pensionario').val();
    var cuspp = $('#cuspp').val();
    var estado_fila = $('#estado_fila').val();
    var tipo_flujo = $('#tipo_flujo').val();

    //  if(fecha_cese < fecha_ingreso){
    //     alert("La Fecha de Cese no puede ser Antes que la fecha Actual");
    //     return false;
    // }

    if(regimen_pensionario == 21 || regimen_pensionario == 22 || regimen_pensionario == 23 || regimen_pensionario == 24){
        if(tipo_flujo == 0){
            alert("Para el Regimen Pensionario debe seleccionar un tipo de Flujo");
            return false;
        }
    }


    $.post('ws/cliente.php', {
        op: 'addTrabajador',
        id:id,
        nombre:nombre,
        documento:documento,
        tipo_documento:tipo_documento,
        sueldo:sueldo,
        condicion:condicion,
        situacion:situacion,
        fecha_ingreso:fecha_ingreso,
        fecha_cese:fecha_cese,
        quinta_categoria:quinta_categoria,
        asignacion_familiar:asignacion_familiar,
        regimen_pensionario:regimen_pensionario,
        cuspp:cuspp,
        estado_fila:estado_fila,
        ocupacion:ocupacion,
        contrato:contrato,
        tipo_flujo:tipo_flujo
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
    var id = $('#id').val();
    var nombre = $('#nombre').val();
    var documento = $('#documento').val();
    var tipo_documento = $('#tipo_documento').val();
    var sueldo = $('#sueldo').val();
    var situacion = $('#situacion').val();
    var ocupacion = $('#ocupacion').val();
    var contrato = $('#contrato').val();
    var condicion = $('#condicion').val();
    var fecha_ingreso = $('#fecha_ingreso').val();
    var fecha_cese = $('#fecha_cese').val();
    var quinta_categoria = 1;
    var asignacion_familiar = $('#asignacion_familiar').val();
    var regimen_pensionario = $('#regimen_pensionario').val();
    var cuspp = $('#cuspp').val();
    var estado_fila = $('#estado_fila').val();
    var tipo_flujo = $('#tipo_flujo').val();


    if(situacion == 13){
        if(fecha_cese == ""){
            alert("Debe colocar una Fecha Cese");
            return false;
        }else if(fecha_cese < fecha_ingreso){
            alert("La Fecha de Cese no puede ser Antes que la fecha Actual");
            return false;
        }
    }else{
        if(fecha_cese != ""){
            if(fecha_cese < fecha_ingreso){
                alert("La Fecha de Cese no puede ser Antes que la fecha Actual");
                return false;
            }
        }
    }
    


    $.post('ws/cliente.php', {
        op: 'upTrabajador',
        id:id,
        nombre:nombre,
        documento:documento,
        tipo_documento:tipo_documento,
        sueldo:sueldo,
        condicion:condicion,
        situacion:situacion,
        fecha_ingreso:fecha_ingreso,
        fecha_cese:fecha_cese,
        quinta_categoria:quinta_categoria,
        asignacion_familiar:asignacion_familiar,
        regimen_pensionario:regimen_pensionario,
        cuspp:cuspp,
        estado_fila:estado_fila,
        ocupacion:ocupacion,
        contrato:contrato,
        tipo_flujo:tipo_flujo
    }, function(data) {
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
        op: 'gettrabajador', 
        id: id
    }, function(data) {
        console.log(data);
        if(data !== 0){
            $('#id').val(data.id);
            $('#nombre').val(data.nombres_y_apellidos);
            $('#documento').val(data.documento);
            $('#tipo_documento option[value="'+data.tipo_documento+'"]').attr('selected', true);
            $('#sueldo').val(data.sueldo_basico);
            $('#situacion option[value="'+data.situacion+'"]').attr('selected', true);
            $('#condicion option[value="'+data.condicion+'"]').attr('selected', true);
            $('#fecha_ingreso').val(data.fecha_de_ingreso);
            if(data.fecha_cese == '0000-00-00')
                $('#fecha_cese').val("");
            else
                $('#fecha_cese').val(data.fecha_cese);
            // $('#quinta_categoria option[value="'+data.quinta_categoria+'"]').attr('selected', true);
            $('#asignacion_familiar option[value="'+data.asignacion_familiar+'"]').attr('selected', true);
            $('#regimen_pensionario option[value="'+data.regimen_pensionario+'"]').attr('selected', true);
            $('#cuspp').val(data.cuspp);
            $('#contrato option[value="'+data.contrato+'"]').attr('selected', true);
            $('#condicion').val(data.condicion);
            if(data.regimen_pensionario == 21 || data.regimen_pensionario == 22 || data.regimen_pensionario == 23 || data.regimen_pensionario == 24){
                $('#divFlujo').show();
                $('#tipo_flujo').show();
                $('#tipo_flujo option[value="'+data.tipo_flujo+'"]').attr('selected', true);
            }else{
                $('#divFlujo').hide();
                $('#tipo_flujo').hide();
                $('#tipo_flujo option[value="0"]').attr('selected', true);
            }

        }
    }, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar este Trabajador?")) {
        $.post('ws/cliente.php', {
            op: 'delTrabajador', 
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