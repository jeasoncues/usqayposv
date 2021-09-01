<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
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
        
          $('td:eq(3)', nRow).html(aData[7]);
          $('td:eq(4)', nRow).html(aData[5]);

          $('td:eq(5)', nRow).html(aData[2]);
          $('td:eq(6)', nRow).html(aData[3]);
          $('td:eq(7)', nRow).html(aData[4]);
          
      },
      drawCallback: mostrarModal
  });

  }

  function mostrarModal(){
      $("#modal_id_producto").modal('show')
  }

  function cargarAlmacenesDisponible(id_almacen_origen){
    $('#id_almacen_d').html('')
        $.post('ws/almacen.php', {op: 'list_cotos', id:id_almacen_origen}, function(data) {            
            console.log(data)
            console.log( JSON.parse(data))            
            if(data != 0){
                let dataSeteada=JSON.parse(data)
                let html=''
                $.each(dataSeteada, function(key, value) {
                    console.log(key+": "+ (value['nombre']) )
                    let nombre=value['nombre']
                    let id=value['id']
                    html+= '<option  value=' + id + '>' + nombre + '</option>'
                    // $("#id_almacen_d").append("<option  value='" + value['id'] + "'>" + value['nombre'] + "</option>")
                })
                $('#id_almacen_d').html( html )
            }else{
                $.notify("SIN ALMACENES DE DESTINO DISPONIBLES")
            }

        })
}



function evaluarInsert(){
    try{

    var id_producto = $('#id_producto').val();

    var id_almacen_o = $('#id_almacen_o').val();

    var id_almacen_d = $('#id_almacen_d option:selected').val();

    var cantidad = $('#cantidad').val();

    var cantidad_maxima = $('#cantidad_maxima').val();
   
        if( id_producto!='' &&   id_almacen_o!='' &&  id_almacen_d!='' && cantidad!='' ){
            if( parseFloat(cantidad) <= 0 ){
                $.notify(" Ingrese una Cantidad Mayor a Cero ")
                return false
            }else{
                if( parseFloat($("#cantidad_maxima").val()) < parseFloat($("#cantidad").val())  ){
                    $.notify(" La cantidad Maxima a restar es : "+ cantidad_maxima)
                    return false
                }else{
                    return true
                }
            }
        }

        if( id_producto=='' ||   id_almacen_o=='' ||  id_almacen_d!='' || cantidad=='' ){
            if( id_producto=='' ){
                $.notify("Seleccione un Producto ")
                return false

            }

            if( id_almacen_o=='' ){
                $.notify("Seleccione un Producto ")
                return false                
            }

            if( id_almacen_d!=''){
                $.notify("Ingrese un Almacen de Destino ")
                return false
                
            }

            if( cantidad=='' ){
                $.notify("Ingrese una Cantidad ")
                return false                
            }
            
        }


    }catch(error){
        $.notify("Informacion incompleta al Guardar")
        return false
    }
    return true
}

function insert(){
var id = $('#id').val();

var id_producto = $('#id_producto').val();

var id_almacen_o = $('#id_almacen_o').val();

var id_almacen_d = $('#id_almacen_d').val();

var cantidad = $('#cantidad').val();

var costo = $('#costo').val();

var id_usuario = $('#id_usuario').val();

var id_movimiento_almacenes = $('#id_movimiento_almacenes').val();

var estado_fila = $('#estado_fila').val();

$.post('ws/movimiento_producto.php', {op: 'adddetmov',id:id,id_producto:id_producto,id_almacen_o:id_almacen_o,id_almacen_d:id_almacen_d,cantidad:cantidad,costo:costo,id_usuario:id_usuario,id_movimiento_almacenes:id_movimiento_almacenes,estado_fila:estado_fila}, function(data) {
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

function del(id,idr){
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/movimiento_producto.php', {op: 'deldetmov', id: id}, function (data) {
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



$.post('ws/almacen.php', {op: 'list'}, function(data) {
if(data != 0){
$('#data_tbl_modal_id_almacen_o').html('');
$('#data_tbl_modal_id_almacen_d').html('');
var hto = '';
var htd = '';
$.each(data, function(key, value) {
    hto += '<tr>';
    hto += '<td><a href="#" onclick="sel_id_almacen_o('+value.id+')">SEL</a></td>';
    hto += '<td>'+value.id+'</td>';
    hto += '<td>'+value.nombre+'</td>';
    hto += '<td>'+value.ubicacion+'</td>';
    hto += '</tr>';
    
    htd += '<tr>';
    htd += '<td><a href="#" onclick="sel_id_almacen_d('+value.id+')">SEL</a></td>';
    htd += '<td>'+value.id+'</td>';
    htd += '<td>'+value.nombre+'</td>';
    htd += '<td>'+value.ubicacion+'</td>';
    htd += '</tr>';
});
$('#data_tbl_modal_id_almacen_o').html(hto);
$('#data_tbl_modal_id_almacen_d').html(htd);
$('#tbl_modal_id_almacen_o').dataTable();
$('#tbl_modal_id_almacen_d').dataTable();
}
}, 'json');

});



function save(){
    if(evaluarInsert()){
        insert()
    }    
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
$("#id_almacen_o").val(id_almacen)
$('#cantidad').val(stock);
$('#modal_id_producto').modal('hide');
cargarAlmacenesDisponible(id_almacen)
}
}, 'json');
}



function sel_id_almacen_o(id_e){
$.post('ws/almacen.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
    $('#id_almacen_o').val(data.id);
    $('#txt_id_almacen_o').html(data.nombre);
    $('#modal_id_almacen_o').modal('hide');
}
}, 'json');
}

function sel_id_almacen_d(id_e){
$.post('ws/almacen.php', {op: 'get', id:id_e}, function(data) {
if(data != 0){
    $('#id_almacen_d').val(data.id);
    $('#txt_id_almacen_d').html(data.nombre);
    $('#modal_id_almacen_d').modal('hide');
}
}, 'json');
}
