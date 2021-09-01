<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function evaluar_insert(){    
    
    if($('#nombre').val().length==0 ){
        $.notify("Ingrese un Nombre");
        return false;
    }
    if($('#documento').val().length==0 ){
        $.notify("Ingrese un Numeero de Documento DNI / RUC");
        return false;
    }
   /* if($('#direccion').val().length==0 ){
        $.notify("Ingrese una Direccion");
        return false;
    }*/
    if($('#documento').val().length==8 || $('#documento').val().length==11 ){
        return true;
    }else{
        $.notify("Ingrese un Numeero de Documento DNI / RUC valido");
        return false;
    }
    return true;
}

function insert(){
var id = $('#id').val();

var nombre = $('#nombre').val();

var documento = $('#documento').val();

var direccion = $('#direccion').val();

var correo = $('#correo').val();

var tipo_cliente = $('#tipo_cliente').find('option:selected').val();

var fecha_nacimiento = $('#fecha_nacimiento').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/cliente.php', {op: 'add',id:id,nombre:nombre,documento:documento,direccion:direccion,correo:correo,tipo_cliente:tipo_cliente,fecha_nacimiento:fecha_nacimiento,estado_fila:estado_fila}, function(data) {
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

var direccion = $('#direccion').val();

var correo = $('#correo').val();

var tipo_cliente = $('#tipo_cliente').find('option:selected').val();

var fecha_nacimiento = $('#fecha_nacimiento').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/cliente.php', {op: 'mod',id:id,nombre:nombre,documento:documento,direccion:direccion,correo:correo,tipo_cliente:tipo_cliente,fecha_nacimiento:fecha_nacimiento,estado_fila:estado_fila}, function(data) {
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
$.post('ws/cliente.php', {op: 'get', id: id}, function(data) {
if(data !== 0){

$('#id').val(data.id);

$('#nombre').val(data.nombre);

$('#documento').val(data.documento);

$('#direccion').val(data.direccion);

$('#correo').val(data.correo);

$('#tipo_cliente option[value="'+data.tipo_cliente+'"]').attr('selected', true);

$('#fecha_nacimiento').val(data.fecha_nacimiento);

$('#estado_fila').val(data.estado_fila);

}
}, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/cliente.php', {op: 'del', id: id}, function (data) {
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
var search = localStorage.getItem('search-cliente') ? localStorage.getItem('search-cliente') : '';
var tbl = $('#tb').DataTable({
    "search": {
        "search": search 
    },
    responsive: true,
        "order": [[ 0, "desc" ]],
        dom: 'lBfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        pageLength: 10,
      searching: true,
      bLengthChange: false,
      order: [0, ['DESC']],
      "processing": true,
      "serverSide": true,
      "ajax":{
          url: "ws/cliente.php",
          type: "post",
          data: {
            op : "server_side"
          }
      },
      createdRow: function( row, data, dataIndex ) {
      },
      fnRowCallback: function( nRow, aData, iDisplayIndex ) {
          $('td:eq(7)', nRow).html( "<div class='btn-group' role='group'>"+
              "<a title='Editar' class='btn btn-sm btn-default' href='#' onclick='sel("+aData[0]+")'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>"+
              "<a title='Asignar Credito' class='btn btn-sm btn-default' href='asignarCredito.php?id="+aData[0]+"'><i class='fa fa-money' aria-hidden='true'></i></a>"+
              "<a title='Eliminar' class='btn btn-sm btn-default' href='#' onclick='del("+aData[0]+")'><i class='fa fa-trash' aria-hidden='true'></i></a>"+
              "<!--<a title='Archivar' class='btn btn-sm btn-default' href='cliente_archivos.php?id="+aData[0]+"' ><i class='fa fa-file-archive-o' aria-hidden='true'></i></a></div>-->"
          );
      }
});

tbl.on( 'search.dt', function () { 
    localStorage.setItem("search-cliente", tbl.search());
});


$('#fecha_nacimiento').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});
});

function save(){
var vid = $('#id').val();
if(vid === '0')
{
    if(evaluar_insert()){
        insert();
    }
}
else
{
    if(evaluar_insert()){
        update();
    }
}
}

function img(id){
$("#muestra").attr("src","recursos/uploads/clientes/"+id+".png");
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
    url: 'ws/cliente.php',
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

