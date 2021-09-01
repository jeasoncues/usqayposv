<?php require_once('../../globales_sistema.php'); ?>

jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });

let monto_actual = 0;
}

function valida_insert(){
    if($('#id_proveedor').val().length==0){
        $.notify("Ingrese un Proveedor")
        return false;
    }

    if($('#categoria option:selected').val().length==0){
        $.notify("Ingrese un Tipo de Documento")
        return false;
    }

    if($('#numero_documento').val().length==0){
        $.notify("Ingrese un Numero de Documento")
        return false;
    }

    if($('#monto_total').val().length==0){
        $.notify("Ingrese un Monto Total")
        return false;
    }

    if($('#fecha').val().length==0){
        $.notify("Ingrese una Fecha")
        return false;
    }

    return true;

}

function insert(){
var id = $('#id').val();

var id_usuario = $('#id_usuario').val();

var id_proveedor = $('#id_proveedor').val();

var categoria = $('#categoria').find('option:selected').val();

var numero_documento = $('#numero_documento').val();

var monto_total = $('#monto_total').val();

var fecha = $('#fecha').val();

var monto_pendiente = $('#monto_pendiente').val();

var id_caja = $('#id_caja').find('option:selected').val();

var proximo_pago = $('#proximo_pago').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/compra.php', {op: 'add',id:id,id_usuario:id_usuario,id_proveedor:id_proveedor,categoria:categoria,numero_documento:numero_documento,monto_total:monto_total,fecha:fecha,monto_pendiente:monto_pendiente,id_caja:id_caja,proximo_pago:proximo_pago,estado_fila:estado_fila}, function(data) {
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

var id_usuario = $('#id_usuario').val();

var id_proveedor = $('#id_proveedor').val();

var categoria = $('#categoria').find('option:selected').val();

var numero_documento = $('#numero_documento').val();

var monto_total = $('#monto_total').val();

var fecha = $('#fecha').val();

var monto_pendiente = $('#monto_pendiente').val();

var id_caja = $('#id_caja').find('option:selected').val();

var proximo_pago = $('#proximo_pago').val();

var estado_fila = $('#estado_fila').val();

console.log(monto_actual - monto_total);

var dif = monto_total - monto_pendiente;
console.log(dif);


$.post('ws/compra.php', {op: 'edit',id:id,id_usuario:id_usuario,id_proveedor:id_proveedor,categoria:categoria,numero_documento:numero_documento,monto_total:monto_total,fecha:fecha,monto_pendiente:monto_pendiente,id_caja:id_caja,proximo_pago:proximo_pago,estado_fila:estado_fila, dif:dif}, function(data) {
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

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")){
        $.post('ws/compra.php', {op: 'del', id: id}, function (data) {
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

function edit(id) {
    if (confirm("¿Desea Editar esta compra?")){
        $.post('ws/compra.php', {op: 'traer', id: id}, function (data) {
            console.log(data[0]);

            $('#id').val(data[0].id);
            $("#txt_id_proveedor").html(data[0].razon_social);
            $("#id_proveedor").val(data[0].id_proveedor);
            $("#categoria").val(data[0].categoria);
            $("#numero_documento").val(data[0].numero_documento);
            $("#monto_total").val(parseFloat(data[0].monto_total));
            monto_actual = data[0].monto_total;
            console.log(monto_actual);
            $("#fecha").val(data[0].fecha);
            $("#id_caja").val(data[0].id_caja);
            $("#monto_pendiente").val(parseFloat(data[0].monto_pendiente));
            $("#proximo_pago").val(data[0].proximo_pago);
            $('#estado_fila').val(data[0].estado_fila);
            // if (data === 0) {
            //     $('body,html').animate({
            //         scrollTop: 0
            //     }, 800);
            //     $('#merror').show('fast').delay(4000).hide('fast');
            // }
            // else {
            //     $('body,html').animate({
            //         scrollTop: 0
            //     }, 800);
            //     $('#msuccess').show('fast').delay(4000).hide('fast');
            //     location.reload();
            // }
        }, 'json');
    }
}

$(document).ready(function() {
    $(".select2").select2();
    var search = localStorage.getItem('search-compra') ? localStorage.getItem('search-compra') : '';
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
        localStorage.setItem("search-compra", tbl.search());
    });
   


$.post('ws/proveedor.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_proveedor').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_proveedor('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.razon_social+'</td>';
    ht += '<td>'+value.ruc+'</td>';
    ht += '<td>'+value.direccion+'</td>';
    ht += '<td>'+value.telefono+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_proveedor').html(ht);
$('#tbl_modal_id_proveedor').dataTable();
}
}, 'json');

$('#fecha').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});


$('#proximo_pago').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

$('#proximo_pago_d').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});
});

function save(){
var vid = $('#id').val();
if(vid === '0')
{
    if(valida_insert()){
        insert();
    }
}
else
{
update();
}
}

function sel_id_usuario(id_e){
$.post('ws/usuario.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_usuario').val(data.id);
$('#txt_id_usuario').html(data.nombres_y_apellidos);
$('#modal_id_usuario').modal('hide');
}
}, 'json');
}

function sel_id_proveedor(id_e){
$.post('ws/proveedor.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
$('#id_proveedor').val(data.id);
$('#txt_id_proveedor').html(data.razon_social);
$('#modal_id_proveedor').modal('hide');
}
}, 'json');
}

var monto_max = 0;

function pagar(id_e){
$.post('ws/compra.php', {op: 'get', id:id_e}, function(data) {
    if(data != 0){
        monto_max = parseFloat(data.monto_pendiente);
        $("#id_pago").val(data.id);
        $("#monto_pago").val(data.monto_pendiente);
        $("#modal_pago").modal("show");
    }
}, 'json');
}

function finaliza_pago() {
    var caja = $("#caja_pago").val();
    var monto = $("#monto_pago").val();
    var proximo = $("#proximo_pago_p").val();
    var id = $("#id_pago").val();
    var id_usuario = $('#id_usuario').val();

    if (monto !== "" && !isNaN(monto) && verfecha()) {
        $.post('ws/compra.php', {
            op: 'pay',
            id: id,
            monto: monto,
            proximo: proximo,
            id_usuario: id_usuario,
            id_caja: caja
        }, function (data) {
            if (data != 0) {
                $("#modal_pago").modal("hide");
                $('#frmall').reset();
                $('body,html').animate({scrollTop: 0}, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
    } else {
        alert("Porfavor, completa los campos correctamente");
    }
}

function verifica_monto(){
    var monto = parseFloat($("#monto_pago").val());
    if(monto > monto_max || monto <= 0){
        alert("Ingresa un monto válido!");
        $("#monto_pago").val(monto_max);
    }
    
    if(monto < monto_max){
        $("#prx").show(1);
    }else{
        $("#prx").hide(1);
    }
}

function verfecha(){
    var monto = parseFloat($("#monto_pago").val());
    var prox = $("#proximo_pago_p").val();
    if(monto < monto_max){
        if(prox !== ""){
            return true;
        }else{
            return false;
        }
    }else{
        return true;
    }
}

function addguia(id_e){
    if(confirm("AVISO: Al asociar la compra a una guia, no podras eliminarla. ¿Deseas Continuar?")){
        $("#id_cmp").val(id_e);
        $("#error_guia").hide(0);
        $("#modal_guia").modal("show");
    }
}

function finaliza_guia(){
    var id_compra = $("#id_cmp").val();
    var numero_guia = $("#numero_guia").val();
    if(numero_guia !== ""){
        $.post('ws/compra_guia.php', {op: 'assocCotos',id_compra:id_compra,numero_guia:numero_guia}, function(data) {
            if(data != 0){
                $("#error_guia").hide(0);
                $("#modal_guia").modal("hide");
                $('body,html').animate({scrollTop: 0}, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }else{
                $("#error_guia").show("fast");
            }
        }, 'json');
    }else{
        alert("Porfavor, ingresa un numero de guía");
    }
}