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
    $.notify("Solo se puede eliminar desde Movimiento Almacenes")
}

$(document).ready(function() {
    $("#fecha_vencimiento").datepicker({dateFormat: 'yy-mm-dd',
          changeMonth: true,
          changeYear: true
    });
   

    $('#boton-tabla').on('click', function(){
			const id_almacen = $('#id_almacen').val();
			const inicio = $('#txtfechaini').val();
			if ($.fn.dataTable.isDataTable('#tbl-k'))

			{
				table = $('#tbl-k').DataTable();
				table.destroy();
			}
			$("#tbl-k > tbody").html("");
			$('#table').bootstrapTable('showLoading');  
			$.post('ws/movimiento_producto.php',{ op: 'kardex', id_almacen: id_almacen, inicio: inicio }, function(response){
				
				for(i=0; i< response.data.length; i++){

					var t1 = Number(response.data[i]['cantidad3']);
					response.data[i]['cantidad3'] = t1.toFixed(2);
					
				}
				$('#table').bootstrapTable('hideColumn','id');
				$('#table').bootstrapTable('hideLoading');
				$('#table').bootstrapTable('load', response.data);
				console.log(response.data)

			},'json')
		})


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
    if(evalua_save()){
        insert();
    }
    
}

function evalua_save(){
    let cantidad_maxima = $("#cantidad_maxima").val()
    let cantidad_ingresada = $("#cantidad").val()
    
    if( parseFloat(cantidad_ingresada) > parseFloat(cantidad_maxima) ){
        $.notify("La cantidad maxima a ingrsar es de :"+cantidad_maxima )
        return false
    }
    return true
}

function sel_id_producto_s(id_e){
    console.log(id_e)
$.post('ws/producto.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
    $('#id_producto').val(data.id);
    $('#txt_id_producto').html(data.nombre);
    $("#costo").val(data.precio_compra);
    $('#modal_id_producto').modal('hide');
}
}, 'json');
}

function sel_id_producto_c(dataIn){
    
    let stock=dataIn['stock']
    let id_almacen=dataIn['id_almacen']
    let nombre_almacen=dataIn['almacen'].replace(/_/g, " ")
    $("#mensaje_cantidad_maxima").html("")
    $.post('ws/producto.php', {op: 'get', id:dataIn['id_producto']}, function(data) {
if(data != 0){
    $('#id_producto').val(data.id);    
    $('#txt_id_producto').html(data.nombre +" ("+nombre_almacen+")");
    $("#costo").val(data.precio_compra);
    $("#mensaje_cantidad_maxima").html("Cantidad Maxima a retirar : "+stock)
    $("#cantidad_maxima").val(stock)
    $("#id_almacen").val(id_almacen)
    $('#cantidad').val(stock)
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

function mostrarTable(){
   var table_mostrar_producto= $('#tbl_modal_id_producto').DataTable({
       destroy:true,
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
            d.op = "server_side_cotos"
          }
      },
      createdRow: function( row, data, dataIndex ) {
        $('td', row).eq(0).attr('colspan',1);
      },
      fnRowCallback: function( nRow, aData, iDisplayIndex ) {
          // console.log(aData)                 
          let data={
              id_producto: aData[0],
              id_almacen: aData[6],
              stock: aData[5],
              almacen: aData[7].replace(/ /g, "_")
          }
          $('td:eq(0)', nRow).html( "<a href='#' onclick=sel_id_producto_c("+ JSON.stringify(data) +")>SEL</a>");
          
          $('td:eq(1)', nRow).html(aData[0]);
          $('td:eq(2)', nRow).html(aData[1]);

          // $('td:eq(3)', nRow).html(aData[6]);
          $('td:eq(3)', nRow).html(aData[7]);
          $('td:eq(4)', nRow).html(aData[5]);

          $('td:eq(5)', nRow).html(aData[2]);
          $('td:eq(6)', nRow).html(aData[3]);
          // $('td:eq(8)', nRow).html(aData[4]);
          // $('td:eq(6)', nRow).html(aData[6]);
          // $('td:eq(7)', nRow).html(aData[7]);
      },
      drawCallback: mostrarModal
  });

  }

  function mostrarModal(){
      $("#modal_id_producto").modal('show')
  }