<?php require_once('../../globales_sistema.php');?>

$(document).ready(function () {
   
   
    
    $('#tbl-cobros').DataTable({
        responsive: true,
       dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
         "bInfo": false,
        "bSort": false,
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
    $('#tblCompras').DataTable({
        responsive: true,
        dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
    
    var tbl = $("#tblKardex").DataTable({
        responsive: true,
        "dom": 'T<"clear">lfrtip',
        "bInfo": false,
        "bSort": false,
        "oTableTools": {
            "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls"
                },
                {
                    "sExtends": "pdf"
                }
            ]
        }
    });
    
   $('#form-pagos').on('click', function(){
        
        const data = {
            op: 'nuevo_pago',
            id: $('#id_venta_medio_pago').val(),
            monto: $('#monto_pago').val(),
            medio: $('#medio').val()
        };

        console.log("data.monto",data.monto)
        console.log("monto_actual",$("#monto_actual").val())
        console.log(data.monto > $("#monto_actual").val())
        console.log( parseFloat(data.monto) > parseFloat($("#monto_actual").val()))

        if(data.monto == ""){
            data.monto = $("#monto_actual").val();
        }else if( parseFloat(data.monto) > parseFloat($("#monto_actual").val())){
            alert("El monto ingresado es mayor al actual, se cobrara su totalidad y cerrara su venta");
            $('#monto_pago').val($("#monto_actual").val());
            data.monto = $("#monto_actual").val();
        }

        /*else if(data.monto > $("#monto_actual").val()){
            alert("El monto ingresado es mayor al actual, se cobrara su totalidad y cerrara su venta");
            $('#monto_pago').val($("#monto_actual").val());
            data.monto = $("#monto_actual").val();
        }*/

        if(data.medio == ""){
            alert("Debe Seleccionar un Metodo de Pago");
            return false;
        }
        
        // data.monto= parseFloat(data.monto)
        console.log("accept",data);
        $.post('ws/venta_medio_pago.php', data, function(response){
            console.log(response);

            if(response["respuesta"]){
                location.reload();
            }else{
                alert("No se pudo registrar el pago");
                //swal("El sistema informa", "No se pudo registrar el pago", "danger");
            }
        },'json')

   });

    //tbl.fnSort( [ [0,'desc'] ] );
});
function buscar() {

    if($("#opc").val() == ""){
        window.location.href = "reporte_creditos_compras.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val();
    }else if($("#opc").val() == "-1"){
        window.location.href = "reporte_creditos_compras.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val();
    }
    else{
        window.location.href = "reporte_creditos_compras.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val() +"&opcion="+$("#opc").val();        
    }
}
function detalles_credito(){
    var result_style = document.getElementById('detalles_credito').style;
    result_style.display ="";
    var result_style = document.getElementById('detalles_creditos').style;
    result_style.display ="";
}
function subir_sunat(id){
    $.post('ws/venta.php', {
        op: 'cargar_sunat',
        id: id
    }, function (data) {
        console.log("Response ",data);
    }, 'json');
}

function subir_sunat2(id, idCaja){
    const swalWithBootstrapButtons = swal.mixin({
  confirmButtonClass: 'btn btn-success',
  cancelButtonClass: 'btn btn-danger',
  buttonsStyling: false,
})

swalWithBootstrapButtons({
  title: '¿Qué tipo de comprobante desea generar?',
  text: "Deberás escoger entre Boleta o Factura",
  type: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Boleta',
  cancelButtonText: 'Factura',
  reverseButtons: true
}).then((result) => {
  if (result.value) {
    $.post('ws/venta.php', {
        op: 'convertir_comprobante_subir',
        id: id,
        tipo_comprobante: 1
    }, function (data) {
        console.log("Response ",data);
        
        if(data.errors != undefined){
            alert(data.errors);
        }else{
            let tipoImprime = 'BOL';

            $.post('ws/venta_medio_pago.php', {
                op: 'endImprime',
                id: id, 
                tipoImprime: tipoImprime,
                caja: idCaja

            }, function (data) {
                console.log('ENdImprime',data);
            });
            
            alert("comprobante cargado correctamente");
            location.reload();
        }
    }, 'json');
    
  } else if (
    // Read more about handling dismissals
    result.dismiss === swal.DismissReason.cancel
  ) {
    $.post('ws/venta.php', {
        op: 'convertir_comprobante_subir',
        id: id,
        tipo_comprobante: 2
    }, function (data) {
        console.log("Response ",data);
        if(data.errors != undefined){
            alert(data.errors);
        }else{

            let tipoImprime = 'FAC';

            $.post('ws/venta_medio_pago.php', {
                op: 'endImprime',
                id: id, 
                tipoImprime: tipoImprime,
                caja: idCaja

            }, function (data) {
                console.log('ENdImprime',data);
            });
            
            alert("comprobante cargado correctamente");
            location.reload();
        }

    }, 'json');
  }
})
    
}

function anula_venta(id) {
    if (confirm("¿Está seguro de anular esta venta?")){
        $.post('ws/venta.php', {
            op: 'anulaventa',
            id: id
        }, function (data) {
            if (data !== 0) {
                location.reload();
            } else {
                alert("Ocurrió un error al anular la venta");
            }
        }, 'json');
    }
}
function reimprimir(id,tipo,caja) {
    if (confirm("¿Está seguro de re-imprimir esta venta?")){
        $.post('ws/venta.php', {
            op: 'imprimir',
            id: id,
            tipo: tipo,
            id_caja: caja
        }, function (data) {
            if (data !== 0) {
                alert("Reimpreso con éxito");
            } else {
                alert("Ocurrió un error al anular la venta");
            }
        }, 'json');
    }
}

function anularCredito(id){
    if (confirm("¿Está seguro de Anular el credito?")){
        $.post('ws/venta.php', {
            op: 'AnulaCredito',
            id: id
        }, function (data) {
            if (data !== 0) {
                alert("Anulacion con éxito");
                location.reload();
            } else {
                alert("Ocurrió un error al anular la venta");
            }
        }, 'json');
    }
}

function tipoDocumento(tipo)
{
    const tipos = [null, "BOLETA", "FACTURA", "NOTA DE VENTA"];
    return tipos[tipo];
}

function detalles(id)
{
    
    if ($.fn.dataTable.isDataTable('#tbl-detalles'))
	{
		table = $('#tbl-detalles').DataTable();
		table.destroy();
	}
    
	$("#tbl-detalles > tbody").html("");

    
    $('#myModal2').modal();
    $.post('ws/proveedor.php', { op:'compras', id } ,function(response){
        console.log(response);
        let monto_total_final = 0;
        let monto_total_pendiente = 0;
        let monto_pagado = 0;

        //let tbody = ``;
        let tfoot = ``;
        $("#tbl-detalles > tbody").html("");
        $.each(response, (i, val) => {
            monto_total_final += Number(val['monto_total']);
            monto_total_pendiente += Number(val['monto_pendiente']);
            monto_pagado = monto_total_final - monto_total_pendiente;
            var id = val['id'];
            console.log(id);
            let tbody = `<tr>`;
            tbody += `<td>${val['fecha']}</td>`;
            tbody += `<td>${val['monto_total']}</td>`;
            tbody += `<td>${val['monto_pendiente']}</td>`;
            tbody += `<td>${tipoDocumento(val['categoria'])}</td>`;
            tbody += `<td>${val['numero_documento']}</td>`;
            tbody += `<td>${val['proximo_pago']}</td>`;
            if(val['monto_pendiente'] > 0){
                // tbody += '<td><button title="Pagar" type="button" onclick="pagar('+id+')" class="btn btn-success btn-sm detallar"><i class="fa fa-money"></i></button><button title="Ver Pagos" type="button" onclick="verPagosDeCompra('+id+')" class="btn btn-primary btn-sm detallar"><i class="fa fa-eye"></i></button></td>';
                tbody += '<td><button title="Pagar" type="button" onclick="pagar('+id+')" class="btn btn-success btn-sm detallar"><i class="fa fa-money"></i></button></td>';
            }else{
                tbody += '<td></td>';
            }
            tbody += `</tr>`;
            $("#tbl-detalles > tbody:last").append(tbody);
        });

        tfoot += `<tr>`;
        tfoot += `<th>Total</th>`;
        tfoot += `<th>${monto_total_final.toFixed(2)}</th>`;
        tfoot += `</tr>`;

        //$('#tbl-detalles > tbody').html(tbody);
        $('#tbl-detalles > tfoot').html(tfoot);
        $('#tbl-detalles').DataTable();
        const dp = [
            { y: monto_pagado, label: "Monto Pagado" },
            { y: monto_total_pendiente, label: "Monto Pendiente" }
        ];

        console.log(monto_total_final, monto_total_pendiente, monto_pagado);
        
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title: {
                text: "Reporte Deuda / Haber"
            },
            data: [{
                type: "pie",
                startAngle: 240,
                yValueFormatString: "##0.00\"\"",
                indexLabel: "{label} {y}",
                dataPoints: dp
            }]
        });
        chart.render();

    }, 'json' ).done( function(){
        $('#myModal2').modal('hide');
    } )
}

/*
Obtener ventas por credito del cliente
*/
function verVenta(id_cliente){

    if ($.fn.dataTable.isDataTable('#tbl-ventas-creditos'))

	{
		table = $('#tbl-ventas-creditos').DataTable();
		table.destroy();
	}
	$("#tbl-ventas-creditos > tbody").html("");

    if ($.fn.dataTable.isDataTable('#tbl-pagos-montos'))
    {
        table2 = $('#tbl-pagos-montos').DataTable();
        table2.destroy();
    }
        
        $("#tbl-pagos-montos > tbody").html("");
        $("#tbl-pagos-montos > tfoot").html("");
   
    
    $.post('ws/venta.php',{op:'ventas_cliente_credito', id_cliente: id_cliente}, function(response){

        console.log(response);
        let monto_total_final = 0;
        let monto_total_pagado = 0;
        let deuda_total = 0;
        let tfoot = ``;

       
        $("#tbl-ventas-creditos > tbody").html("");
        
        

        $.each(response, (i, val) => {
            let serie = "";

             monto_total_final += Number(val['total']);
                    const monto_pagado = val['Pagado'] ? Number(val['Pagado']) : 0;
                    monto_total_pagado += monto_pagado;
                    const deuda = Number(val['total']) - monto_pagado;
                    deuda_total += deuda;
                    let estado;
                    let id = val['id']

            $.ajax({
                type: 'POST',
                url: 'ws/venta_medio_pago.php',
                dataType: "json",
                data: { 
                    op: 'consultarfacturacion', 
                    id: val['id']
                },
                success:function(response) {
                    serie = response;

                   
                    
                    let tbody = `<tr>`;
                    tbody += `<td>${val['id']}</td>`;
                    tbody += `<td>${serie}</td>`;
                    tbody += `<td>${Number(val['total']).toFixed(2)}</td>`;
                    tbody += `<td>${monto_pagado.toFixed(2)}</td>`;
                    tbody += `<td>${deuda.toFixed(2)}</td>`;
                    tbody += `<td>${deuda == 0 ? '<span class="label label-success">Cancelado</span>' : '<span class="label label-danger">Deuda</span>'}</td>`;
                    tbody += `<td>${val['fecha_hora']}</td>`;
                    tbody += '<td><button type="button" onclick="ventas_pago('+val['id']+', '+deuda+')" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>';
                    tbody += '<button type="button" onclick="detalles2('+val['id']+')" class="btn btn-warning btn-sm"><i class="fa fa-tasks"></i></button>';
                    tbody += '<button type="button" onclick="anularCredito('+val['id']+')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                    if( val['boleta'] == 0 && val['factura'] == 0 ){
                        tbody += '<button type="button" onclick="subir_sunat2('+val['id']+', <?php echo $_COOKIE['id_caja']?>)" class="btn btn-primary btn-sm"><i class="fa fa-cloud-upload"></i></button></td>';
                    }else{
                        tbody += '</td>';
                    }
                    
                    tbody += `</tr>`;

                    $("#tbl-ventas-creditos > tbody:last").append(tbody);
                },
                error: function (err) {
                    // alert(JSON.stringify(err));

                }
            });
            

        });
        
        tfoot += `<tr>`;
        tfoot += `<th></th>`;
        tfoot += `<th>Total</th>`;
        tfoot += `<th>${monto_total_final.toFixed(2)}</th>`;
        tfoot += `<th>${monto_total_pagado.toFixed(2)}</th>`;
        tfoot += `<th>${deuda_total.toFixed(2)}</th>`;
        tfoot += `</tr>`;

        $('#tbl-ventas-creditos > tfoot').html(tfoot);

        $('#tbl-ventas-creditos').DataTable();

    }, 'json' );

}

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

var monto_max = 0;
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

function anularCredito(id){
    if (confirm("¿Está seguro de Anular el credito?")){
        $.post('ws/venta.php', {
            op: 'AnulaCredito',
            id: id
        }, function (data) {
            if(data.errors != undefined){
                alert("Error al anular el comprobante: "+data.errors);
            }else{
                alert("Anulacion con éxito");
            }
            location.reload();
        }, 'json');
    }
}

function pagar(id)
{
    console.log("pagar compra csdm")
    $.post('ws/compra.php', {op: 'get', id:id}, function(data) {
        if(data != 0){
            monto_max = parseFloat(data.monto_pendiente);            
            $("#monto_pendiente_compra").val(monto_max);
            console.log("monto_max",monto_max)
            console.log("monto_pendiente_compra",$("#monto_pendiente_compra").val())

            $("#id_pago").val(data.id);
            $("#monto_pago2").val(data.monto_pendiente);            
            $("#modal_pago").modal("show");
        }
    }, 'json');
}

function valida_pagar_compra(){
    if( parseFloat( $("#monto_pago2").val() ) > parseFloat( $("#monto_pendiente_compra").val() ) ){
        $.notify("El Monto de Pago no debe ser mayor a S/."+$("#monto_pendiente_compra").val())
        return false;
    }
    return true;
}

function verPagosDeCompra(id)
{
    console.log("pagar VER PAGO  csdm")

    /*$.post('ws/compra.php', {op: 'get', id:id}, function(data) {
        if(data != 0){
            monto_max = parseFloat(data.monto_pendiente);
            $("#id_pago").val(data.id);
            $("#monto_pago2").val(data.monto_pendiente);
            $("#modal_pago").modal("show");
        }
    }, 'json');*/
}


function finaliza_pago() {

    if(valida_pagar_compra()){
        var caja = $("#caja_pago").val();
        // var monto = $("#monto_pago2").val();
        var monto = parseFloat($("#monto_pago2").val());
        var proximo = $("#proximo_pago_p").val();
        var id = $("#id_pago").val();
        var id_usuario = $('#id_usuario').val();
        
        if (monto !== "" && !isNaN(monto) && verfecha()) {
            console.log("caja",caja)
            console.log("monto",monto)
            console.log("proximo",proximo)
            console.log("id",id)
            $.post('ws/compra.php', {
                op: 'pay',
                id: id,
                monto: monto,
                proximo: proximo,
                id_usuario: id_usuario,
                id_caja: caja
            }, function (data) {
                console.log(data);
                if (data != 0) {
                    $("#modal_pago").modal("hide");
                    //$('#frmall').reset();
                    $('body,html').animate({scrollTop: 0}, 800);
                    $('#msuccess').show('fast').delay(4000).hide('fast');
                    location.reload();
                }
            }, 'json');
        } else {
            alert("Porfavor, completa los campos correctamente");
        }
    }
}

function ventas_pago(idventa, deuda)
{


    $("#boxDetalles").css("display","none");
    $("#boxPagos").css("display", "block");
    console.log(deuda);
    if ($.fn.dataTable.isDataTable('#tbl-pagos-montos'))

	{
		table = $('#tbl-pagos-montos').DataTable();
		table.destroy();
	}
	$("#tbl-pagos-montos > tbody").html("");
    
    $.post('ws/venta_medio_pago.php', {op:'listventa', id:idventa}, function(response){

        console.log(response);
        let monto_total_final = 0;
        
        let tfoot = ``;

        $("#tbl-pagos-montos > tbody").html("");
        
        const pagado = deuda == 0 ? true : false;
        $.each(response, (i, val) => {
            const monto = Number(val['monto']);

            if(val['medio'] == 'CREDITO') monto_total_final += monto;
                
            let tbody = `<tr>`;
            
            tbody += `<td>${ val['medio'] }</td>`;
            tbody += `<td>${ val['monto'] }</td>`;
            tbody += `<td>${ val['moneda'] }</td>`;
            if(!pagado){
                tbody += `<td>${ val['medio'] == 'CREDITO' ? '<button type="button" onclick="pagar2('+val['id']+','+val['monto']+')" class="btn btn-success btn-sm"><i class="fa fa-money"></i></button>' : '' }</td>`;
            }else{
                tbody += `<td></td>`;
            }
            
            tbody += `</tr>`;

            $("#tbl-pagos-montos > tbody:last").append(tbody);

        });
        
        
        

        tfoot += `<tr>`;
        tfoot += `<th>Total a Pagar</th>`;
        tfoot += `<th>${monto_total_final}</th>`;
        tfoot += `</tr>`;

        $('#tbl-pagos-montos > tfoot').html(tfoot);

        $('#tbl-pagos-montos').DataTable();

    }, 'json' );
    
}

function detalles2(id){
   $("#boxPagos").css("display","none");
   $("#boxDetalles").css("display", "block");

    if ($.fn.dataTable.isDataTable('#tbl-detalles2'))

    {
        table = $('#tbl-detalles2').DataTable();
        table.destroy();
    }
   $("#tbl-detalles2 > tbody").html("");
    
    $.post('ws/venta.php', {op:'DetallesUnion', id:id}, function(response){

        console.log(response);
        let monto_total_final = 0;
        
        let tfoot = ``;

        $("#tbl-detalles2 > tbody").html("");
        
        $.each(response, (i, val) => {
                
            let tbody = `<tr>`;
            
            tbody += `<td>${ val['id'] }</td>`;
            tbody += `<td>${ val['nombre'] }</td>`;
            tbody += `<td>${ val['cantidad'] }</td>`;
            tbody += `<td>${ Number(val['precio']).toFixed(2) }</td>`;            
            tbody += `</tr>`;

            $("#tbl-detalles2 > tbody:last").append(tbody);

        });
        
        
        $('#tbl-detalles2').DataTable({
            responsive: true
        });

        // tfoot += `<tr>`;
        // tfoot += `<th>Total a Pagar</th>`;
        // tfoot += `<th>${monto_total_final}</th>`;
        // tfoot += `</tr>`;

        // $('#tbl-pagos-montos > tfoot').html(tfoot);

    }, 'json' );
}


function pagar2(id,montoActual){
    $('#id_venta_medio_pago').val(id);
    $('#monto_actual').val(montoActual);
    $('#myModalVentas').modal();
}

