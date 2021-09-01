<?php require_once('../../globales_sistema.php');?>
$(document).ready(function () {
    // var tbl = $("#tblKardex").dataTable({
    //     "responsive": true,
    //     "dom": 'T<"clear">lfrtip',
    //     "bInfo": false,
    //     "bSort": false,
    //     "oTableTools": {
    //         "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
    //         "aButtons": [
    //             {
    //                 "sExtends": "xls"
    //             },
    //             {
    //                 "sExtends": "pdf"
    //             }
    //         ]
    //     }
    // });
    //tbl.fnSort( [ [0,'desc'] ] );

    $('#tblKardex').DataTable({
        responsive: true,
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

    var chart = new CanvasJS.Chart("GraphBuy", {
        animationEnabled: true,
        data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "S/ ##0.00\"\"",
            indexLabel: "{label} {y}",
            dataPoints: [
                {y: $("#NOT").val(), label: "Nota de Venta"},
                {y: $("#BOL").val(), label: "Boleta"},
                {y: $("#FAC").val(), label: "Factura"},
                {y: $("#NOTD").val(), label: "Nota de Debito"},
                {y: $("#NOTC").val(), label: "Nota de Credito"},
            ]
        }]
    });
    chart.render();

    $('#frm-gen').on('submit', function(e){

        e.preventDefault();

        let doc = $("#doc").val();
		let nombre = $("#nombre").val();
    	let direccion = $("#direccion").val();
    	let correo = $("#correo").val();
		let radioValue = $("input[name='optComp']:checked").val();
		let idUsuario = <?php echo $_COOKIE["id_usuario"];?>;
		let idCaja = <?php echo $_COOKIE["id_caja"];?>;
        let id_venta = $('#id_v').val();
        let op = 'gen_comp_null';
        let total = $('#total_v').val();
        let subtotal = $('#subtotal_v').val();
        let igv = $('#igv_v').val();

        if( radioValue == 'FAC' && doc.length == 11){
            if(nombre != "" && direccion != "" ){
                $.post('ws/venta.php', {
			                op: 'gen_comp_null',
				            doc: doc,
				            nombre: nombre,
				            direccion: direccion,
				            correo: correo,
				            tipo: 2,
                            id_venta: id_venta,
				            subtotal: subtotal,
				            igv: igv,
				            total: total,
				            idUsuario: idUsuario,
				            idCaja: idCaja
				        }, function(data) {
				            if(data["errors"]){
				            	alert(data["errors"]);
				            	$('#myModalG').modal("hide");
				            	return;
				            }else{
				            	location.reload();
				            }
				        }, 'json');
            }else{
				alert("Para emitir la factura debe llenar los campos de Razon Social, Direccion, Correo Electronico (Opcional)")
			}
        }else if(radioValue == 'BOL'){
            if(doc.length == ""){
                if(Number(total) < 700 ){
                    $.post('ws/venta.php', {
				                op: 'gen_comp_null',
					            doc: "",
					            nombre: "",
					            direccion: "",
					            correo: "",
					            tipo: 1,
                                id_venta: id_venta,
                                subtotal: subtotal,
                                igv: igv,
                                total: total,
                                idUsuario: idUsuario,
                                idCaja: idCaja
					        }, function(data) {
					            if(data["errors"]){
					            	alert(data["errors"]);
				            		$('#myModalG').modal("hide");
					            	return;
					            }else{
					            	location.reload();
					            }
					        }, 'json');
                }else{
					alert("La venta Excede los 700 Soles, debe colocar el DNI, Nombre, Direccion y Correo (Opcional)");
				}
            }else{
                if(doc.length == 8){
                    $.post('ws/venta.php', {
			                op: 'gen_comp_null',
				            doc: doc,
				            nombre: nombre,
				            direccion: direccion,
				            correo: correo,
				            tipo: 1,
                            id_venta: id_venta,
				            subtotal: subtotal,
				            igv: igv,
				            total: total,
				            idUsuario: idUsuario,
				            idCaja: idCaja
				        }, function(data) {
				            console.log(data["errors"]);
				            if(data["errors"]){
				            	alert(data["errors"]);
				            	$('#myModalG').modal("hide");
				            	return false;
				            }else{
				            	location.reload();
				            }
				        }, 'json');
                }else{
					alert("Para Emitir la Boleta el DNI debe estar Vacio o Contener 8 caracteres");
				}
            }
        }else{
			alert("Para generar la factura el RUC debe contener 11 caracteres");
		}
    })
});
function buscar() {

    if($("#opc").val() == ""){
        window.location.href = "reporte_comprobantes_personalizados.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val() + "&caja=" +$("#caja").val();
    }else if($("#opc").val() == "-1"){
        window.location.href = "ventas_descartadas.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val() + "&caja=" +$("#caja").val();
    }
    else{
        window.location.href = "reporte_comprobantes_personalizados.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val() +"&opcion="+$("#opc").val() + "&caja=" +$("#caja").val();
    }
}

function subir_sunat(id){
    $.post('ws/venta.php', {
        op: 'cargar_sunat',
        id: id
        
    }, function (data) {
        if(data.errors != undefined){
            alert(data.errors);
        }else{
            alert("comprobante cargado correctamente");
        }

    }, 'json');
}

function notas(id, tipo, nota, caja){
    $.post('ws/venta.php', {
        op: 'notas',
        id: id,
        tipo: tipo,
        nota: nota
    }, function (data) {
        if(data.errors == undefined){
            if(data.tipo_de_comprobante == 4){
                alert("Venta Cambiada a nota de Debito");
            }else{
                alert("Venta Cambiada a nota de Credito");
            }

            if(tipo == 1 ){
                tipoImprime = 'BOL';
            }else{
                tipoImprime = 'FAC';
            }



            $.post('ws/venta_medio_pago.php', {
                op: 'endImprime',
                id: id,
                tipoImprime: tipoImprime,
                caja : '<?php echo $_COOKIE["id_caja"];?>'

            }, function (data) {
                console.log('ENdImprime',data);
            });
            // location.reload();
        }else{
            alert(data.errors);
            // location.reload();
        }
    }, 'json');
}



function anula_venta(id) {
    if (confirm("¿Está seguro de anular esta venta?")){
        $.post('ws/venta.php', {
            op: 'anulaventa',
            id: id
        }, function (data) {
            console.log(data);
            if (data !== 0) {
                <!-- location.reload(); -->
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

function generarComprobaten(id)
{
    $('#id_v').val(id);

    $.post('ws/venta.php', {op: 'cliente_gen', id: id}, function(response){
        console.log(response);
        $('#total_v').val(response['total']);
        $('#subtotal_v').val(response['subtotal']);
        let igv = Number(response['total']) - (Number(response['total']) / 1.18);
        $('#igv_v').val(igv);

        if(response['cliente']['id'] != 0){
            $('#doc').val(response['cliente']['documento']);
            $('#nombre').val(response['cliente']['nombre']);
            $('#direccion').val(response['cliente']['direccion']);
            $('#correo').val(response['cliente']['correo']);
        }else{
            $('#doc').val('');
            $('#nombre').val('');
            $('#direccion').val('');
            $('#correo').val('');
        }
        $('#myModalG').modal();
    },'json')
    
}