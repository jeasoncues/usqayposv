<?php require_once('../../globales_sistema.php');?>
$(document).ready(function () {
    $('#loading').hide();
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
        // dom: 'Bfrtip',
        dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [0, 'asc'],
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

    

    $("#frm-gen").keypress(function(e) {
        if (e.which == 13) {
            // alert();
            return false;
        }
    });

    $('#frm-gen').on('submit', function(e){

        e.preventDefault();
        $('#loading').show();
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
                            console.log(data);
                            if(data["errors"]){
                                alert(data["errors"]);
                                $('#myModalG').modal("hide");
                                return;
                            }else{
                                $('#loading').hide();
                                location.reload();
                            }
                        }, 'json');
            }else{
                alert("Para emitir la factura debe llenar los campos de Razon Social, Direccion, Correo Electronico (Opcional)")
                $('#loading').hide();
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
                                    $('#loading').hide();
                                    location.reload();
                                }
                            }, 'json');
                }else{
                    alert("La venta Excede los 700 Soles, debe colocar el DNI, Nombre, Direccion y Correo (Opcional)");
                    $('#loading').hide();

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
                                $('#loading').hide();
                                location.reload();
                            }
                        }, 'json');
                }else{
                    alert("Para Emitir la Boleta el DNI debe estar Vacio o Contener 8 caracteres");
                    $('#loading').hide();

                }
            }
        }else{
            alert("Para generar la factura el RUC debe contener 11 caracteres");
            $('#loading').hide();

        }
    })
    
    $('#doc').change(function() {
    // $('#doc').keypress(function(e){
    //     var code = (e.KeyCode ? e.KeyCode : e.which);
    //     if(code == 13){

            var doc = $("#doc").val();
            // var radioValue = $("input[name='optComp']:checked").val()
            // if( radioValue == 'BOL' && doc.length == 8){
                // alert(radioValue+" "+doc.length);
            // }
            // $.post('ws/cliente.php', {
            //     op: 'getdocumento',
            //     doc: doc
            // }, function(data) {
            //     console.log(data);
            //     if (data !== 0) {
            //         $("#nombre").val(data["nombre"]);
            //         $("#direccion").val(data["direccion"]);
            //         $("#correo").val(data["correo"]);
            //     }
            // }, 'json');

            $.ajaxblock();
           

        if(doc != ""){

            $.ajax({
                type: 'POST',
                url: 'ws/cliente.php',
                dataType: "json",
                data: { 
                    op: 'getdocumento',
                    doc: doc,
                },
                success:function(data) {

                    if (data !== 0) {
                        $("#nombre").val(data.nombre);
                        $("#direccion").val(data.direccion);
                        $.ajaxunblock();

                    } else {
                        if(navigator.onLine) {
                            if(doc.length == 11){
                                $.ajax({
                                    type: 'POST',
                                    url: 'ws/cliente.php',
                                    dataType: "json",
                                    data: { 
                                        op: 'getdocumentosunat',
                                        dni: doc,
                                    },
                                    beforeSend: function(){
                                    },  
                                    complete:function(data){
                                    },
                                    success: function(data){
                                        if(data != 0){
                                            console.log(data);
                                            $("#nombre").val(data[0].companyName);
                                            $("#direccion").val(data[0].address);
                                            $.ajaxunblock();

                                        }
                                    },
                                    error: function(err){
                                        console.log(err);
                                    }
                                });
                            }else if(doc.length == 8){
                                $.ajax({
                                    type: 'POST',
                                    url: 'ws/cliente.php',
                                    dataType: "json",
                                    data: { 
                                        op: 'getdocumentoreniec',
                                        dni: doc,
                                    },
                                    beforeSend: function(){
                                    },  
                                    complete:function(data){
                                    },
                                    success: function(data){
                                        console.log(data);
                                        if(data != 0){
                                            
                                            $("#nombre").val(data[0].nombres);
                                            $("#direccion").val(data[0].direccion);
                                        }else{

                                            alert("No se Ecnuentra el cliente");

                                        }

                                        $.ajaxunblock();

                                    }

                                });
                            }else{
                                alert("la cantidad de digitos no corresponde a un DNI o RUC");
                                $.ajaxunblock();  
                            }
                        }else{
                            alert("Se encuentra sin conexion a internet, no se puede realizar la busqueda del DNI o RUC");
                            $.ajaxunblock();
                        }
                    }
                }
            });
            
        }else{
            $.ajaxunblock();

        }
            // };
            // return false;
        
    });

    $('input[name=optComp]').change(function(){
        var radioValue = $("input[name='optComp']:checked").val();
        if(radioValue == 'FAC'){
            $("#labelDoc").html("RUC:");
            $("#labelNombre").html("Razon Social:");
        }else{
            $("#labelDoc").html("DNI:");
            $("#labelNombre").html("Nombre:");

        }
    });
});


$.ajaxblock    = function(){
      $("body").prepend("<div id='ajax-overlay'><div id='ajax-overlay-body' class='center'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Loading...</span></div></div>");
      $("#ajax-overlay").css({
         position: 'absolute',
         color: '#FFFFFF',
         top: '0',
         left: '0',
         width: '100%',
         height: '100%',
         position: 'fixed',
         background: 'rgba(39, 38, 46, 0.67)',
         'text-align': 'center',
         'z-index': '9999'
      });
      $("#ajax-overlay-body").css({
         position: 'absolute',
         top: '40%',
         left: '50%',
         width: '120px',
         height: '48px',
         'margin-top': '-12px',
         'margin-left': '-60px',
         //background: 'rgba(39, 38, 46, 0.1)',
         '-webkit-border-radius':   '10px',
         '-moz-border-radius':      '10px',
         'border-radius':        '10px'
      });
      $("#ajax-overlay").fadeIn(50);
   };
   $.ajaxunblock  = function(){
      $("#ajax-overlay").fadeOut(100, function()
      {
         $("#ajax-overlay").remove();
      });
   };


function buscar() {
    if($("#opc").val() == ""){
        window.location.href = "reporte_ventas_totales.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val()+ "&caja=" +$("#caja").val()+"&usr=" +$("#usuario").val();
    }else if($("#opc").val() == "-1"){
        window.location.href = "ventas_descartadas.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val() + "&caja=" +$("#caja").val();
    }
    else{
        window.location.href = "reporte_ventas_totales.php?fecha_inicio=" + $('#txtfechaini').val() +
        "&fecha_fin=" + $('#txtfechafin').val() +"&opcion="+$("#opc").val() + "&caja=" +$("#caja").val()+"&usr=" +$("#usuario").val();
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
        console.log("notas", data);
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

                location.reload();
            });
        }else{
            alert(data.errors);
            // location.reload();
        }
    }, 'json');
}


var flag = 0;
function anula_venta(id) {
    if(flag == 0){

        if (confirm("¿Está seguro de anular esta venta?")){

            flag = 1;
            $.ajax({
                type: 'POST',
                url: 'ws/venta.php',
                dataType: "json",
                data: { op: 'anulaventa', id: id},
                success:function(data) {
                    if (data !== 0) {
                        location.reload();
                    } else {
                        alert("Ocurrió un error al anular la venta");
                    }
                    flag = 0;
                }
            });
            
            // $.post('ws/venta.php', {
            //     op: 'anulaventa',
            //     id: id
            // }, function (data) {
            //     console.log(data);
                
            // }, 'json');
        }
    }else{
        alert("La Anulacion esta en proceso, por favor Espere");
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
        $.ajax({
            type: 'POST',
            url: 'ws/venta.php',
            dataType: "json",
            data: { op: 'AnulaCredito', id: id},
            success:function(response) {
                // console.log("res", response);
                // alert();
                if (response !== 0) {
                    alert("Anulacion con éxito");
                    location.reload();
                } else {
                    alert("Ocurrió un error al anular la venta");
                }

                
            },
            error: function (err) {
                alert(JSON.stringify(err));
                // $("#btnAgregarPago").prop( "disabled", false );
                // $.ajaxunblock();

            }
        });
        // $.post('ws/venta.php', {
        //     op: 'AnulaCredito',
        //     id: id
        // }, function (data) {
        //     if (data !== 0) {
        //         alert("Anulacion con éxito");
                // location.reload();
        //     } else {
        //         alert("Ocurrió un error al anular la venta");
        //     }
        // }, 'json');
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