<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}



function insert(){
var id = $('#id').val();

var id_producto = $('#id_producto').val();

var id_almacen = $('#id_almacen').val();

var cantidad = $('#cantidad').val();

var costo = $('#costo').val();

var id_usuario = $('#id_usuario').val();

var id_guia_producto = $('#id_guia_producto').val();
var fecha_vencimiento = $('#fecha_vencimiento').val();
var lote = $('#lote').val();
var estado_fila = $('#estado_fila').val();

$.post('ws/movimiento_producto.php', {op: 'addguia',id:id,id_producto:id_producto,id_almacen:id_almacen,cantidad:cantidad,costo:costo,id_usuario:id_usuario,id_guia_producto:id_guia_producto,estado_fila:estado_fila, fecha_vencimiento:fecha_vencimiento, lote:lote}, function(data) {
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

function del(id){
$.post('ws/movimiento_producto.php', {op: 'del', id: id}, function(data) {
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
    $("#fecha_vencimiento").datepicker({dateFormat: 'yy-mm-dd',
          changeMonth: true,
          changeYear: true
    });
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
        },
        pageLength: 10,
        searching: true,
        bLengthChange: false,
        order: [0, ['DESC']],
        "processing": true,
        "serverSide": true,
        "ajax":{
            url: "ws/movimiento_producto.php",
            type: "post",
            data: {
              op : "server_side",
              id: $('#id_guia_producto').val()
            }
        },
        createdRow: function( row, data, dataIndex ) {
        },
        fnRowCallback: function( nRow, aData, iDisplayIndex ) {
            $('td:eq(9)', nRow).html( "<div class='btn-group' role='group'>"+
                "<a href='#' class='btn btn-sm btn-default' onclick='del("+aData[0]+")'><i class='fa fa-trash' aria-hidden='true'></i></a></div>"
            );
        }
  });

$('#tbl_modal_id_producto').DataTable({
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
      },
      pageLength: 10,
      searching: true,
      bLengthChange: false,
      order: [0, ['DESC']],
      "processing": true,
      "serverSide": true,
      "ajax":{
          url: "ws/producto.php",
          type: "post",
          data: (d)=>{
            d.op = "server_side"
          }
      },
      createdRow: function( row, data, dataIndex ) {
        $('td', row).eq(0).attr('colspan',1);
      },
      fnRowCallback: function( nRow, aData, iDisplayIndex ) {
          $('td:eq(0)', nRow).html( '<a href="#" onclick="sel_id_producto('+aData[0]+')">SEL</a>');
          if(aData[5] == "SI"){
            $('td:eq(5)', nRow).html(
                '<span class="label label-success">'+aData[4]+'</span>'
            );
          }else{
              $('td:eq(5)', nRow).html(
                  '<span class="label label-warning">'+aData[4]+'</span>'
              );
          }
          $('td:eq(1)', nRow).html(aData[0]);
          $('td:eq(2)', nRow).html(aData[1]);
          $('td:eq(3)', nRow).html(aData[2]);
          $('td:eq(4)', nRow).html(aData[3]);
          // $('td:eq(6)', nRow).html(aData[6]);
          // $('td:eq(7)', nRow).html(aData[7]);
      }
  });

$.post('ws/almacen.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_almacen').html('');
var ht = '';
$.each(data, function(key, value) {
    ht += '<tr>';
    ht += '<td><a href="#" onclick="sel_id_almacen('+value.id+')">SEL</a></td>';
    ht += '<td>'+value.id+'</td>';
    ht += '<td>'+value.nombre+'</td>';
    ht += '<td>'+value.ubicacion+'</td>';
    ht += '</tr>';
});
$('#data_tbl_modal_id_almacen').html(ht);
$('#tbl_modal_id_almacen').dataTable();
}
}, 'json');

});

function save(){
    insert();
}

function sel_id_producto(id_e){
$.post('ws/producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
    $('#id_producto').val(data.id);
    $('#txt_id_producto').html(data.nombre);
    $("#costo").val(data.precio_compra);
    $('#modal_id_producto').modal('hide');
}
}, 'json');
}

function sel_id_almacen(id_e){
$.post('ws/almacen.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
    $('#id_almacen').val(data.id);
    $('#txt_id_almacen').html(data.nombre);
    $('#modal_id_almacen').modal('hide');
}
}, 'json');
}

function desvincular(id, guia){

    $.post('ws/almacen.php', {
        op: 'desvincular', 
        id: id,
        guia: guia
    }, function(data) {
        if(data == 0){
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            $('#merror').show('fast').delay(4000).hide('fast');
        }else{
            //$('#frmall').reset();
            // $('body,html').animate({
            //     scrollTop: 0
            // }, 800);
            $('#msuccess').show('fast').delay(4000).hide('fast');
            //
            location.reload();
            javascript:history.back(1);
        }
        //location.ref = "http://localhost/katsu/compra.php";
    }, 'json');
}

