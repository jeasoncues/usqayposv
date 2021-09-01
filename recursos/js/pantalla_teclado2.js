function Pantalla() {

    Pantalla.PRODUCTO = "producto";
    Pantalla.SERVICIO = "servicio";

    this.idVenta = $("#id_venta").val();
    this.tiComp = $("#tipo_comprobante").val();
    this.vuelto = 0;
    this.montoTotal = 0;
    this.montoAPagar = 0;
    this.montoAPagarCheck = 0;
    this.subTotal = 0;
    this.totalImpuestos = 0;
    this.descuento = 0;
    this.pagadoTotal = 0;
    this.dataImpuestos = JSON.parse(decodeURIComponent($("#data_impuestos").val()));
    this.tipoCambio = parseFloat($("#tipo_cambio").val());
    this.tipoComprobante = 0;
    this.tipoComprobanteAux = 0;
    this.caja = $("#id_caja").val();
    this.usuario = $("#id_usuario").val();
    this.cliente = {
        id: 0,
        documento: null,
        nombre: null,
        direccion: null,
        fecha: null
    };
    this.descuentoCupones = 0;
    this.inc_incluye;
    let opc = {};
    let ArrOpc = [];
    let agrega = {};
    let agregaArr = [];
    let tipoNubeFact;
    let guia = 0;
    var total_pago = 0;
    var impuesto = 0;
    var inc = 0;

    var parent = this;

    this.getVenta = function() {
        if (parent.idVenta == 0) {
            return $.post('ws/venta.php', {
                op: 'gen',
                id_usuario: parent.usuario,
                id_caja: parent.caja
            }, function(data) {
                parent.idVenta = data;
                $('#id_venta').val(parent.idVenta);
                $('#txtVenta').html("");
                $('#txtVentaid').show();
                $('#txtVenta').html(parent.idVenta);
                history.pushState({}, null, "pantalla_teclado.php?id=" + data);
            });
        }
    };

    this.agregarItem = function(tipo) {
        $.when(parent.getVenta()).done(function() {
            parent.inc_incluye = $("#inc_impuesto").val();
            if (tipo === Pantalla.PRODUCTO) {
                var id_usuario = $("#id_usuario").val();
                var id_producto = $('#id_producto').val();
                var cantidad = $("#cantidad_producto").val();
                var precio = $("#precio_producto").html();
                var id_almacen = $("#almacen_venta").val();

                $.post('ws/producto_venta.php', {
                    op: 'addventa',
                    id_venta: parent.idVenta,
                    id_producto: id_producto,
                    precio: precio,
                    cantidad: cantidad,
                    id_usuario: id_usuario,
                    id_almacen: id_almacen
                }, function(data) {
                    if (data !== 0) {
                        parent.getDetalleVenta();
                        level1();
                        $("#busquedaproducto").show("fast");
                        $("#agregarproducto").hide("fast");
                        $("#cantidad_producto").val(1);
                    }
                }, 'json');
            } else if (tipo === Pantalla.SERVICIO) {
                var idServicio = $('#id_servicio').val();
                var precioServicio = $("#precio_servicio").html();
                var cantidadServicio = $("#cantidad_servicio").val();
                var usuario = $("#id_usuario").val();

                $.post('ws/servicio_venta.php', {
                    op: 'addventa',
                    id_venta: parent.idVenta,
                    id_servicio: idServicio,
                    precio: precioServicio,
                    cantidad: cantidadServicio,
                    id_usuario: usuario
                }, function(data) {
                    if (data !== 0) {
                        parent.getDetalleVenta();
                        level1_servicio();
                        $("#busquedaservicio").show("fast");
                        $("#agregarservicio").hide("fast");
                    }
                }, 'json');
            }
        });
    };

    this.eliminarItem = function(id, tipo) {
        if (tipo === Pantalla.PRODUCTO) {
            $.post('ws/producto_venta.php', {
                op: 'delventa',
                id: id
            }, function(data) {
                if (data !== 0) {
                    parent.getDetalleVenta();
                }
            }, 'json');
        } else if (tipo === Pantalla.SERVICIO) {
            var id_usuario = $("#id_usuario").val();
            $.post('ws/servicio_venta.php', {
                op: 'delventa',
                id: id,
                id_usuario: id_usuario
            }, function(data) {
                if (data !== 0) {
                    parent.getDetalleVenta();
                }
            }, 'json');
        }
    };

    this.cantidadItem = function(id, tipo, cantidad) {
        if (tipo == Pantalla.PRODUCTO) {
            $.post('ws/producto_venta.php', {
                op: 'addventa',
                id_actualizar: id,
                cantidad: cantidad
            }, function(data) {
                if (data !== 0) {
                    parent.getDetalleVenta();
                } else {
                    parent.eliminarItem(id, tipo);
                }
            }, 'json');
        } else if (tipo == Pantalla.SERVICIO) {
            $.post('ws/servicio_venta.php', {
                op: 'addventa',
                id_actualizar: id,
                cantidad: cantidad
            }, function(data) {
                if (data !== 0) {
                    parent.getDetalleVenta();
                } else {
                    parent.eliminarItem(id, tipo);
                }
            }, 'json');
        }
    };

    this.getDetalleVenta = function() {
        $("#tablaventa").html("");
        parent.montoTotal = 0;

        $.when(getProductos()).done(function() {
            $.when(getServicios()).done(function() {
                $("#total_venta").val(parent.montoTotal.toFixed(2));
                parent.calculaImpuestos();
                parent.cargarCupones();
                parent.cargarDescuentos();
            });
        });
    };

    function getServicios() {
        //Una vez finalizada la carga de productos, se procede a realizar la carga de servicios
        return $.post('ws/servicio_venta.php', {
            op: 'listbyventa',
            id: parent.idVenta
        }, function(data) {
            var row = "";
            if (data !== 0) {
                $.each(data, function(key, value) {
                    row +=
                        '<tr>' +
                        '<th><input type="checkbox" id="item" value="' + value.id + '"></th>' +
                        '<th>' + value.id_servicio.nombre + '</th>' +
                        '<td id="cantidadEditServicio" name="' + value.id + '">' + value.cantidad + '</td>' +
                        '<td id="precioEditServicio" name="' + value.id + '">S./ ' + value.precio + '</td>' +
                        '<td>S./ ' + value.total + '</td>' +
                        '<td hidden id="incluye" >' + value.id_servicio.incluye_impuesto + '</td>' +
                        '<td> <div class="btn-group">' +
                        '<button type="button" class="btn btn-success btnSumarServicio" id="' + value.id + '">' +
                        '<i class="fa fa-plus" aria-hidden="true"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-danger btnRestarServicio" id="' + value.id + '">' +
                        '<i class="fa fa-minus" aria-hidden="true"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-default text-red btnEliminarServicio" id="' + value.id + '">' +
                        '<i class="fa fa-close" aria-hidden="true"></i>' +
                        '</button></div>' +
                        '</td>' +
                        '</tr>';

                    parent.montoTotal += parseFloat(value.total);
                });
                $("#tablaventa").append(row);
            }
        }, 'json');
    }

    function getProductos() {
        return $.post('ws/producto_venta.php', {
            op: 'listbyventa',
            id: parent.idVenta
        }, function(data) {
            console.log(data);
            var row = "";
            if (data !== 0) {
                $.each(data, function(key, value) {
                    var ofertas = '';
                    if (value.ofertas) {
                        ofertas = '<ul>';
                        value.ofertas.forEach(function(o) {
                            ofertas += '<li>' + o + '</li>';
                        });
                        ofertas += '</ul>';
                    }

                    total = parseFloat(value.total);


                    row +=
                        '<tr>' +
                        '<th><input type="checkbox" id="item" value="' + value.id + '"></th>' +
                        '<th>' + value.id_producto.nombre + ofertas + '</th>' +
                        '<td id="cantidadEdit" name="' + value.id + '">' + value.cantidad + '</td>' +
                        '<td id="precioEdit" name="' + value.id + '">S./ ' + value.precio + '</td>' +
                        '<td>S./ ' + total.toFixed(2) + '</td>' +
                        '<td hidden id="incluye" >' + value.id_producto.incluye_impuesto + '</td>' +
                        '<td> <div class="btn-group">' +
                        '<button type="button" class="btn btn-success btnSumarProducto" id="' + value.id + '">' +
                        '<i class="fa fa-plus" aria-hidden="true"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-danger btnRestarProducto" id="' + value.id + '" data-producto="' + value.id_producto.id + '">' +
                        '<i class="fa fa-minus" aria-hidden="true"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-default text-red btnEliminarProducto" id="' + value.id + '">' +
                        '<i class="fa fa-close" aria-hidden="true"></i>' +
                        '</button></div>' +
                        '</td>' +
                        '</tr>';
                    parent.montoTotal += parseFloat(value.total);
                });
                $("#tablaventa").append(row);
            }
        }, 'json');
    }

    // this.calculaImpuestos = function () {
    //     var impuesto = 0;
    //     parent.totalImpuestos = 0;
    //     parent.dataImpuestos.forEach(function (value, index, ts) {
    //         if (parseInt(value.tipo) === 1) {
    //             //Cuando el impuesto es porcentual le sumamos 1
    //             impuesto = parent.montoTotal - (parent.montoTotal/(parseInt(1)+parseFloat(value.valor)));
    //         } else {
    //             impuesto = value.valor;
    //         }
    //         parent.totalImpuestos += parseFloat(impuesto).toFixed(2);
    //         $("#impuesto_" + value.nombre).val(impuesto.toFixed(2));
    //     });

    //     parent.subTotal = parseFloat(parent.montoTotal - parent.totalImpuestos).toFixed(2);

    //     $("#sub_total_venta").val(parent.subTotal);

    //     parent.calcularDescuento();
    // };

    this.calculaImpuestos = function() {
        var impuesto = 0;
        parent.totalImpuestos = 0;

        let contador = 0,
            exonerada = 0,
            gravada = 0,
            inafecta = 0;

        parent.dataImpuestos.forEach(function(value, index, ts) {
            // console.log(parseInt(value.tipo));
            // console.log(parseInt(parent.inc_incluye));
            $('#tablaventa #incluye').each(function() {

                // parent.inc_incluye = $(this).text();
                // calculaImpuestos();
                if (parseInt(value.tipo) == $(this).text()) {
                    impuesto = parent.montoTotal - (parent.montoTotal / 1.18);
                    // alert(impuesto)
                    // alert(parent.montoTotal)
                    parent.totalImpuestos = impuesto;
                    $("#impuesto_" + value.nombre).val(impuesto.toFixed(2));
                } else {
                    impuesto = value.valor;
                }

            });

        });

        parent.subTotal = parseFloat(parent.montoTotal - parent.totalImpuestos).toFixed(2);
        $("#sub_total_venta").val(parent.subTotal);

        parent.calcularDescuento();
    };

    this.calcularDescuento = function() {
        var desc = $("#descuento_venta").val();
        if (typeof desc === 'string' && desc.length === 0) {
            desc = 0;
            $("#descuento_venta").val(desc);
        }
        parent.descuento = desc;
        if (parent.descuento < 0) {
            parent.descuento = Math.abs(parent.descuento);
            $("#descuento_venta").val(parent.descuento);
        }
        parent.montoAPagar = (parent.montoTotal - parent.descuento).toFixed(2);
        $("#apagar_venta").val(parent.montoAPagar);
    };

    this.getCliente = function() {
        var doc = $("#documento_venta").val();
        $.post('ws/cliente.php', {
            op: 'getdocumento',
            doc: doc
        }, function(data) {
            console.log(data);
            if (data !== 0) {
                parent.cliente.nombre = data.nombre;
                parent.cliente.direccion = data.direccion;
                parent.cliente.fecha = data.fecha_nacimiento;
                parent.cliente.documento = doc;
                parent.cliente.correo = data.correo;

                parent.cliente.id = data.id;
            } else {
                //parent.cliente.id = 0;
                parent.cliente.nombre = $("#cliente_venta").val();
                parent.cliente.direccion = $("#direccion_venta").val();
                parent.cliente.fecha = $("#fecha_venta").val();;
                parent.cliente.documento = doc;
                parent.cliente.correo = $("#correo").val();
            }

            $("#cliente_venta").val(parent.cliente.nombre);
            $("#direccion_venta").val(parent.cliente.direccion);
            $("#fecha_venta").val(parent.cliente.fecha);
            $("#documento_venta").val(parent.cliente.documento);
            $("#correo").val(parent.cliente.correo);
        }, 'json');
    };

    this.preCuenta = function() {

        if (parent.idVenta != 0) {

            $.post('ws/venta.php', {
                op: 'imprimir',
                id: parent.idVenta,
                tipo: 'PRE',
                id_caja: 1
            }, function(data) {
                window.open('archivos_impresion/precuenta.php?id=' + parent.idVenta, '_blank');
            });

        }

    }

    this.notaVenta = function() {

        response = verificarproductos();
        if (response) {
            parent.cargarPagos();

            parent.tipoComprobante = 0;
            // if(($("#documento_venta").val() == "" && $("#cliente_venta").val() == "") || ($("#documento_venta").val() != "" && $("#cliente_venta").val() != ""))  {
            mostrarComprobante();

            // }else{
            //     alert("Ingrese DNI o RUC");
            // }
        }

    };

    this.boleta = function() {
        response = verificarproductos();
        if (response) {
            parent.cargarPagos();
            parent.tipoComprobante = 1;
            if (($("#documento_venta").val() == "" && $("#cliente_venta").val() == "") || ($("#documento_venta").val() != "" && $("#cliente_venta").val() != "")) {
                mostrarComprobante();
            } else {
                alert("Ingrese DNI o RUC");
            }
        }

    };

    this.factura = function() {
        response = verificarproductos();
        if (response) {
            parent.cargarPagos();
            parent.tipoComprobante = 2;
            if (($("#documento_venta").val() != "" && $("#cliente_venta").val() != "") && $("#direccion_venta").val() != "") {
                mostrarComprobante();
            } else {
                alert("Ingrese los Campos obligatorios");
            }
        }
    };

    this.credito = function() {
        response = verificarproductos();
        if (response) {
            parent.cargarPagos();
            parent.tipoComprobante = -1;
            if (($("#documento_venta").val() != "" && $("#cliente_venta").val() != "") && $("#direccion_venta").val() != "") {
                // if (confirm("¿Está seguro de Permitir el Credito?")){
                mostrarComprobante();
                // }
            } else {
                alert("Ingrese los Campos obligatorios");
            }
        }
    };

    function verificarproductos() {
        let contador = 0,
            exonerada = 0,
            gravada = 0,
            inafecta = 0,
            gratuita = 0;

        $('#tablaventa #incluye').each(function() {
            contador++;
            switch ($(this).text()) {
                case '0':
                    inafecta++;
                    break;
                case '1':
                    gravada++;
                    break;
                case '2':
                    exonerada++;
                    break;
                case '3':
                    gratuita++;
                    break;
            }
        });


        if (contador == gravada) {
            tipoNubeFact = 1;
            return true;
        } else if (contador == inafecta) {
            tipoNubeFact = 0;
            return true;
        } else if (contador == exonerada) {
            tipoNubeFact = 2;
            return true;
        } else if (contador == gratuita) {
            tipoNubeFact = 3;
            return true;
        } else {
            alert("No se puede Realizar la compra con productos de distintos impuestos");
            return false;
        }

        console.log(contador);
        console.log(exonerada);
        console.log(gravada);
        console.log(inafecta);
        console.log(gratuita);

    }

    function mostrarComprobante() {
        // parent.getCliente();

        if (parent.cliente.id) {
            if (existeUrl("recursos/uploads/clientes/" + parent.cliente.nombre.id + ".png")) {
                $("#imgcliente").attr("src", "recursos/img/logo-mini2.png");
            } else {
                $("#imgcliente").attr("src", "recursos/img/logo-mini2.png");
            }
        }

        // alert(parent.tipoComprobante);
        if (parent.tipoComprobante == -1) {

            $('#metodo').html('');
            var opc = '<option value="CREDITO">Credito</option>';
            $('#metodo').html(opc);
            $('#moneda').html('');
            var opc2 = '<option value="0"> NOTA DE CREDITO</option>' +
                '<option value="1"> BOLETA </option>' +
                '<option value="2"> FACTURA </option>';
            $('#moneda').html(opc2);

            $("#txtmontopago").prop("disabled", true);
            $("#btnAgregarPago").prop("disabled", true);
        }

        $("#nombre_pago").html(parent.cliente.nombre);
        $("#doc_pago").html(parent.cliente.documento);
        $("#dir_pago").html(parent.cliente.direccion);


        $("#modal_pago").modal("show");
    }

    this.agregarPago = function() {
        var monto = $("#txtmontopago").val();
        var moneda = $("#moneda").val();
        var medio = $("#metodo").val();
        var porpagar = $("#por_pagar_pago").html();
        var vuelto = parseFloat(monto) - parseFloat(porpagar);
        // if(medio == 'CREDITO'){
        //     parent.tipoComprobante = -1;
        // }

        // alert(medio);

        var txtmoneda = "SOLES";

        if (moneda == "USD") {
            txtmoneda = "DOLARES";
        }

        vale = parseFloat(porpagar - monto);
        if (medio == 'EFECTIVO') {

            if (parseFloat(monto) < parseFloat(porpagar)) {
                vuelto = 0.00;
            }

            var items = [];
            var i = 0,
                j = 0;
            $("#item:checked").each(function() {
                items[i] = $(this).val();
                i++;
            });

            j = $("#tablaventa tr").length;

            if (items.length == j || items.length == 0) {

                $.post('ws/venta_medio_pago.php', {
                    op: 'add',
                    id_venta: parent.idVenta,
                    medio: medio,
                    monto: monto,
                    vuelto: vuelto,
                    moneda: moneda
                }, function(data) {
                    if (data !== 0) {
                        parent.cargarPagos();
                        $("#txtmontopago").val("");
                        $("#txtmontopago").focus();
                    }
                }, 'json');
            } else {
                if ($("#txtmontopago").val() > 0) {
                    agrega = {
                        medio: medio,
                        monto: monto,
                        vuelto: vuelto,
                        moneda: moneda
                    }

                    agregaArr.push(agrega);
                    parent.cargarPagos();
                    $("#txtmontopago").val("");
                    $("#txtmontopago").focus();
                } else {
                    alert("Debe introducir un monto mayor a cero");
                }
            }
        } else {
            if (vale >= 0) {

                if (parseFloat(monto) < parseFloat(porpagar)) {
                    vuelto = 0.00;
                }

                var items = [];
                var i = 0,
                    j = 0;
                $("#item:checked").each(function() {
                    items[i] = $(this).val();
                    i++;
                });

                j = $("#tablaventa tr").length;

                if (items.length == j || items.length == 0) {

                    $.post('ws/venta_medio_pago.php', {
                        op: 'add',
                        id_venta: parent.idVenta,
                        medio: medio,
                        monto: monto,
                        vuelto: vuelto,
                        moneda: moneda
                    }, function(data) {
                        if (data !== 0) {
                            parent.cargarPagos();
                            $("#txtmontopago").val("");
                            $("#txtmontopago").focus();
                        }
                    }, 'json');
                } else {
                    if ($("#txtmontopago").val() > 0) {
                        agrega = {
                            medio: medio,
                            monto: monto,
                            vuelto: vuelto,
                            moneda: moneda
                        }

                        agregaArr.push(agrega);
                        parent.cargarPagos();
                        $("#txtmontopago").val("");
                        $("#txtmontopago").focus();
                    } else {
                        alert("Debe introducir un monto mayor a cero");
                    }
                }

            } else {
                alert("No puede pagar mas del monto con tarjeta");
            }
        }

    };

    this.cargarPagos = function() {
        parent.pagadoTotal = 0;
        parent.vuelto = 0;
        var porPagar = 0;
        var vueltoUSD = 0;

        var items = [];
        var i = 0,
            j = 0;
        $("#item:checked").each(function() {
            items[i] = $(this).val();
            i++;
        });

        j = $("#tablaventa tr").length;

        var pagado_total = 0;

        if (items.length == j || items.length == 0) {
            $.post('ws/venta_medio_pago.php', {
                op: 'listventa',
                id: parent.idVenta
            }, function(data) {
                if (data !== 0) {
                    var row = '';
                    $.each(data, function(key, value) {
                        var monedita = "";
                        var valorUSD = "";
                        if (value.moneda == "PEN") {
                            monedita = "SOLES";
                            parent.pagadoTotal += parseFloat(value.monto);
                        } else {
                            monedita = "DOLARES";
                            parent.pagadoTotal += parseFloat(value.monto) * parent.tipoCambio;
                            valorUSD = " (S./" + (value.monto * parent.tipoCambio).toFixed(2) + ")";
                        }

                        display = 'block';
                        pagado_total = parseFloat(parent.pagadoTotal).toFixed(2);
                        // alert(parent.tiComp);
                        if (parent.tiComp == -1) {
                            if (value.medio != 'CREDITO') {
                                display = 'none';
                            } else {
                                // parent.tipoComprobante = -1;
                                pagado_total -= parseFloat(value.monto).toFixed(2);

                            }
                        }

                        row +=
                            '<tr>' +
                            '<th>' + value.medio + '</th>' +
                            '<td>' + monedita + '</td>' +
                            '<td>' + value.monto + valorUSD + '</td>' +
                            // '<td>' + value.vuelto + '</td>' +
                            '<td>' +
                            '<button type="button" style="display:' + display + ';" class="btn-link text-red btnEliminaPago" id="' + value.id + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' +
                            '</tr>';
                    });

                    row += '<tr><td colspan="2" class="text-right"><strong>Total pagado: </strong></td><td>S./' + pagado_total + '</td></tr>';


                    if (parent.pagadoTotal > parent.montoAPagar) {
                        parent.vuelto = (parent.pagadoTotal - parent.montoAPagar).toFixed(2);
                        vueltoUSD = (parent.vuelto / parent.tipoCambio).toFixed(2);
                    } else {
                        porPagar = (parent.montoAPagar - pagado_total).toFixed(2);
                    }

                    console.log("pagadoTotal " + parent.pagadoTotal.toFixed(2));
                    console.log("montoAPagar " + parent.montoAPagar);
                    console.log("vuelto soles " + parent.vuelto);
                    console.log("vuelto dolares " + vueltoUSD);
                    console.log("por pagar " + porPagar);
                } else {
                    porPagar = (parent.montoAPagar - parent.pagadoTotal).toFixed(2);
                    $('#tablapago').html('');
                }

                $('#tablapago').html(row);
                $("#por_pagar_pago").html(porPagar);
                $("#vuelto_soles").text("S./" + parent.vuelto);
                $("#vuelto_usd").text("$" + vueltoUSD);
                $("#total_pago").html(parent.montoAPagar);
            }, 'json');
        } else {
            // $("#txtmontopago").prop( "disabled", true );
            // $("#btnAgregarPago").prop( "disabled", true );
            parent.totalImpuestos = 0;
            // for(k=0; k<items.length;k++){
            total_pago = 0;
            $.each(items, function(key, value) {
                $.post('ws/producto_venta.php', {
                    op: 'checkproducto',
                    item: value
                }, function(data) {

                    opc = {
                        'opc': data[0].opc,
                        'id': data[0].id
                    }

                    ArrOpc.push(opc);

                    total_pago += parseFloat(data[0].total);

                    inc++;
                    parent.montoAPagarCheck = total_pago;
                    parent.montoAPagar = total_pago;
                    parent.montoTotal = total_pago;

                    impuesto = parseFloat(total_pago - (total_pago / (1.18)));
                    parent.totalImpuestos = parseFloat(impuesto).toFixed(2);
                    parent.subTotal = parseFloat(total_pago - parent.totalImpuestos).toFixed(2);

                    // $("#total_pago").html(parent.montoAPagarCheck);
                    parent.pagadoTotal = 0;

                    var row = '';
                    $.each(agregaArr, function(key, value) {
                        // alert(key)
                        var monedita = "";
                        var valorUSD = "";
                        if (value.moneda == "PEN") {
                            monedita = "SOLES";
                            parent.pagadoTotal += parseFloat(value.monto).toFixed(2);
                        } else {
                            monedita = "DOLARES";
                            parent.pagadoTotal += parseFloat(value.monto) * parent.tipoCambio;
                            valorUSD = " (S./" + (value.monto * parent.tipoCambio).toFixed(2) + ")";
                        }
                        row +=
                            '<tr>' +
                            '<th>' + value.medio + '</th>' +
                            '<td>' + monedita + '</td>' +
                            '<td>' + value.monto + valorUSD + '</td>' +
                            // '<td>' + value.vuelto + '</td>' +
                            '<td>' +
                            '<button type="button" class="btn-link text-red btnEliminaPagoCheck" id="' + value.id + '">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                            '</button>' +
                            '</td>' +
                            '</tr>';
                    });

                    row += '<tr><td colspan="2" class="text-right"><strong>Total pagado: </strong></td><td>S./' + parent.pagadoTotal + '</td></tr>';


                    if (parent.pagadoTotal > parent.montoAPagarCheck) {
                        parent.vuelto = (parent.pagadoTotal - parent.montoAPagarCheck).toFixed(2);
                        vueltoUSD = (parent.vuelto / parent.tipoCambio).toFixed(2);
                    } else {
                        porPagar = (parent.montoAPagarCheck - parent.pagadoTotal).toFixed(2);
                    }



                    $('#tablapago').html(row);
                    $("#por_pagar_pago").html(porPagar);
                    $("#vuelto_soles").text("S./" + parent.vuelto);
                    $("#vuelto_usd").text("$" + vueltoUSD);
                    $("#total_pago").html(parent.montoAPagarCheck);

                }, 'json');
            });

            // mostrarComprobante();
            // }



        }
    };

    this.eliminarPago = function(id) {
        $.post('ws/venta_medio_pago.php', {
            op: 'del',
            id: id
        }, function(data) {
            if (data !== 0) {
                parent.cargarPagos();
            }
        }, 'json');
    };


    this.eliminarPagoCheck = function(id) {
        agregaArr.splice(id, 1);
        parent.cargarPagos();
    };


    this.pagar = function() {
        var ventaDoc = $("#documento_venta").val();
        var ventaCliente = $("#cliente_venta").val();
        var ventaDireccion = $("#direccion_venta").val();
        var ventaFecha = $("#fecha_venta").val();
        var correo = $("#correo").val();

        if( $('#guia').prop('checked') ) {
            guia = 1;
        }


        $('#tablapago > tr > th').each(function(key, row) {
            if ($(row).text() == 'CREDITO') {
                parent.tipoComprobanteAux = -1;
            }
        });

        if (parent.tipoComprobante == -1) {

            // alert($('#moneda').val());
            parent.tipoComprobante = $('#moneda').val();
            parent.tipoComprobanteAux = -1;
        }


        var items = [];
        var i = 0,
            j = 0;
        $("#item:checked").each(function() {
            items[i] = $(this).val();
            i++;
        });

        j = $("#tablaventa tr").length;

        var pagado_total = 0;

        // if (items.length == j || items.length == 0)
        //     alert(parent.montoAPagar);
        // else
        //     alert(parent.montoApagarCheck);

            // alert(parent.montoAPagar);


        if ((!(ventaDoc && ventaCliente && ventaDireccion) && parent.tipoComprobante === 2) || !(ventaDoc && ventaCliente && ventaDireccion) && parent.tipoComprobante === 1 && parent.montoAPagar > 700) {
            if (parent.tipoComprobante === 1)
                alert("Debes agregar los datos del cliente el monto excede los S/ 700");
            else
                alert("Debes agregar los datos del cliente");

            $("#modal_pago").modal('hide').on('hidden.bs.modal', function() {
                $("#documento_venta").focus();
            });

        } else {

            var items = [];
            var i = 0,
                j = 0;
            $("#item:checked").each(function() {
                items[i] = $(this).val();
                i++;
            });

            j = $("#tablaventa tr").length;

            if (items.length == j || items.length == 0) {

                if (Number(parent.pagadoTotal.toFixed(2)) > 0 && Number(parent.montoAPagar) > 0 && Number(parent.pagadoTotal.toFixed(2)) < Number(parent.montoAPagar)) {
                    alert("Debes cancelar totalmente la cuenta");
                } else {
                    $("#modal_pago").modal("hide");
                    $("#modal_envio_anim").modal("show");

                    // alert(parent.cliente.id);
                    if (parent.cliente.id == 0) {
                        if (ventaDoc === "") {
                            //parent.cliente.id = 1;
                            //Procesamos Pagos
                            if (parent.pagadoTotal == 0) {
                                $.post('ws/venta_medio_pago.php', {
                                    op: 'add',
                                    id_venta: parent.idVenta,
                                    medio: $("#metodo").val(),
                                    monto: parent.montoAPagar,
                                    vuelto: 0.00,
                                    moneda: 'PEN'
                                }, function(data0) {
                                    //Procesamos Descuentos
                                    if (parseFloat(parent.descuento) > 0) {
                                        $.post('ws/venta_medio_pago.php', {
                                            op: 'add',
                                            id_venta: parent.idVenta,
                                            medio: 'DESCUENTO',
                                            monto: parent.descuento,
                                            vuelto: 0.00,
                                            moneda: 'PEN'
                                        }, function(data1) {
                                            //Finalizamos Venta
                                            $.post('ws/venta.php', {
                                                op: 'end',
                                                id: parent.idVenta,
                                                subtotal: parent.subTotal,
                                                total_impuestos: parent.totalImpuestos,
                                                total: parent.montoTotal,
                                                tipo_comprobante: parent.tipoComprobante,
                                                id_cliente: parent.cliente.id,
                                                id_caja: parent.caja
                                            }, function(data2) {
                                                parent.sendOrders();
                                            }, 'json');
                                        }, 'json');
                                    } else {
                                        //Finalizamos Venta
                                        $.post('ws/venta.php', {
                                            op: 'end',
                                            id: parent.idVenta,
                                            subtotal: parent.subTotal,
                                            total_impuestos: parent.totalImpuestos,
                                            total: parent.montoTotal,
                                            tipo_comprobante: parent.tipoComprobante,
                                            id_cliente: parent.cliente.id,
                                            id_caja: parent.caja
                                        }, function(data2) {
                                            parent.sendOrders();
                                        }, 'json');
                                    }
                                }, 'json');
                            } else {
                                //Cuando ya se hacen calculos previos
                                //Procesamos Descuentos
                                if (parseFloat(parent.descuento) > 0) {
                                    $.post('ws/venta_medio_pago.php', {
                                        op: 'add',
                                        id_venta: parent.idVenta,
                                        medio: 'DESCUENTO',
                                        monto: parent.descuento,
                                        vuelto: 0.00,
                                        moneda: 'PEN'
                                    }, function(data1) {
                                        //Finalizamos Venta
                                        $.post('ws/venta.php', {
                                            op: 'end',
                                            id: parent.idVenta,
                                            subtotal: parent.subTotal,
                                            total_impuestos: parent.totalImpuestos,
                                            total: parent.montoTotal,
                                            tipo_comprobante: parent.tipoComprobante,
                                            id_cliente: parent.cliente.id,
                                            id_caja: parent.caja
                                        }, function(data2) {
                                            parent.sendOrders();
                                        }, 'json');
                                    }, 'json');
                                } else {
                                    //Finalizamos Venta
                                    $.post('ws/venta.php', {
                                        op: 'end',
                                        id: parent.idVenta,
                                        subtotal: parent.subTotal,
                                        total_impuestos: parent.totalImpuestos,
                                        total: parent.montoTotal,
                                        tipo_comprobante: parent.tipoComprobante,
                                        id_cliente: parent.cliente.id,
                                        id_caja: parent.caja
                                    }, function(data2) {
                                        parent.sendOrders();
                                    }, 'json');
                                }
                            }
                        } else {
                            $.post('ws/cliente.php', {
                                op: 'add',
                                nombre: ventaCliente,
                                documento: ventaDoc,
                                direccion: ventaDireccion,
                                tipo_cliente: parent.tipoComprobante,
                                fecha_nacimiento: ventaFecha,
                                correo: correo
                            }, function(data) {
                                if (data !== 0) {
                                    parent.cliente.id = data;
                                    //Procesamos Pagos
                                    if (parent.pagadoTotal == 0) {
                                        $.post('ws/venta_medio_pago.php', {
                                            op: 'add',
                                            id_venta: parent.idVenta,
                                            medio: $("#metodo").val(),
                                            monto: parent.montoAPagar,
                                            vuelto: 0.00,
                                            moneda: 'PEN'
                                        }, function(data0) {
                                            //Procesamos Descuentos
                                            if (parseFloat(parent.descuento) > 0) {
                                                $.post('ws/venta_medio_pago.php', {
                                                    op: 'add',
                                                    id_venta: parent.idVenta,
                                                    medio: 'DESCUENTO',
                                                    monto: parent.descuento,
                                                    vuelto: 0.00,
                                                    moneda: 'PEN'
                                                }, function(data1) {
                                                    //Finalizamos Venta
                                                    $.post('ws/venta.php', {
                                                        op: 'end',
                                                        id: parent.idVenta,
                                                        subtotal: parent.subTotal,
                                                        total_impuestos: parent.totalImpuestos,
                                                        total: parent.montoTotal,
                                                        tipo_comprobante: parent.tipoComprobante,
                                                        id_cliente: parent.cliente.id,
                                                        id_caja: parent.caja
                                                    }, function(data2) {
                                                        parent.sendOrders();
                                                    }, 'json');
                                                }, 'json');
                                            } else {
                                                //Finalizamos Venta
                                                $.post('ws/venta.php', {
                                                    op: 'end',
                                                    id: parent.idVenta,
                                                    subtotal: parent.subTotal,
                                                    total_impuestos: parent.totalImpuestos,
                                                    total: parent.montoTotal,
                                                    tipo_comprobante: parent.tipoComprobante,
                                                    id_cliente: parent.cliente.id,
                                                    id_caja: parent.caja
                                                }, function(data2) {
                                                    parent.sendOrders();
                                                }, 'json');
                                            }
                                        }, 'json');
                                    } else {
                                        //Cuando ya se hacen calculos previos
                                        //Procesamos Descuentos
                                        if (parseFloat(parent.descuento) > 0) {
                                            $.post('ws/venta_medio_pago.php', {
                                                op: 'add',
                                                id_venta: parent.idVenta,
                                                medio: 'DESCUENTO',
                                                monto: parent.descuento,
                                                vuelto: 0.00,
                                                moneda: 'PEN'
                                            }, function(data1) {
                                                //Finalizamos Venta
                                                $.post('ws/venta.php', {
                                                    op: 'end',
                                                    id: parent.idVenta,
                                                    subtotal: parent.subTotal,
                                                    total_impuestos: parent.totalImpuestos,
                                                    total: parent.montoTotal,
                                                    tipo_comprobante: parent.tipoComprobante,
                                                    id_cliente: parent.cliente.id,
                                                    id_caja: parent.caja
                                                }, function(data2) {
                                                    parent.sendOrders();
                                                }, 'json');
                                            }, 'json');
                                        } else {
                                            //Finalizamos Venta
                                            $.post('ws/venta.php', {
                                                op: 'end',
                                                id: parent.idVenta,
                                                subtotal: parent.subTotal,
                                                total_impuestos: parent.totalImpuestos,
                                                total: parent.montoTotal,
                                                tipo_comprobante: parent.tipoComprobante,
                                                id_cliente: parent.cliente.id,
                                                id_caja: parent.caja
                                            }, function(data2) {
                                                parent.sendOrders();
                                            }, 'json');
                                        }
                                    }
                                } else {
                                    $("#modal_envio_anim").modal("hide");
                                    alert("No se pudo registrar el cliente");
                                }
                            }, 'json');
                        }
                    } else {
                        // alert($("#correo").val());
                        $.post('ws/cliente.php', {
                            op: 'mod',
                            id: parent.cliente.id,
                            nombre: ventaCliente,
                            documento: ventaDoc,
                            direccion: ventaDireccion,
                            tipo_cliente: parent.tipoComprobante,
                            fecha_nacimiento: ventaFecha,
                            correo: correo
                        }, function(data) {
                            console.log(data);
                            //Procesamos Pagos
                            if (parseFloat(parent.descuento) > 0) {
                                $.post('ws/venta_medio_pago.php', {
                                    op: 'add',
                                    id_venta: parent.idVenta,
                                    medio: 'DESCUENTO',
                                    monto: parent.descuento,
                                    vuelto: 0.00,
                                    moneda: 'PEN'
                                }, function(data1) {
                                    //Finalizamos Venta
                                    $.post('ws/venta.php', {
                                        op: 'end',
                                        id: parent.idVenta,
                                        subtotal: parent.subTotal,
                                        total_impuestos: parent.totalImpuestos,
                                        total: parent.montoTotal,
                                        tipo_comprobante: parent.tipoComprobante,
                                        id_cliente: parent.cliente.id,
                                        id_caja: parent.caja
                                    }, function(data2) {
                                        parent.sendOrders();
                                    }, 'json');
                                }, 'json');
                            } else {
                                //Finalizamos Venta
                                console.log(parent.idVenta);
                                console.log(parent.subTotal);
                                console.log(parent.totalImpuestos);
                                console.log(parent.montoTotal);
                                console.log(parent.tipoComprobante);
                                console.log(parent.cliente.id);

                                if (parseInt(parent.pagadoTotal - parent.montoTotal) < 0) {
                                    $.post('ws/venta_medio_pago.php', {
                                        op: 'add',
                                        id_venta: parent.idVenta,
                                        medio: $("#metodo").val(),
                                        monto: parent.montoTotal,
                                        vuelto: 0.00,
                                        moneda: 'PEN'
                                    }, function(data1) {

                                    }, 'json');
                                }

                                $.post('ws/venta.php', {
                                    op: 'end',
                                    id: parent.idVenta,
                                    subtotal: parent.subTotal,
                                    total_impuestos: parent.totalImpuestos,
                                    total: parent.montoTotal,
                                    tipo_comprobante: parent.tipoComprobante,
                                    id_cliente: parent.cliente.id,
                                    id_caja: parent.caja
                                }, function(data2) {
                                    parent.sendOrders();
                                }, 'json');
                            }
                        }, 'json');
                    }
                }
            } else {

                $("#modal_pago").modal("hide");
                $("#modal_envio_anim").modal("show");
                $.post('ws/venta.php', {
                    op: 'gen',
                    id_usuario: parent.usuario,
                    id_caja: parent.caja
                }, function(data) {
                    parent.idVenta = data;
                    for (k = 0; k < items.length; k++) {
                        // alert( ArrOpc[k]["id"]);
                        $.post('ws/producto_venta.php', {
                            op: 'updatecheckproducto',
                            item: ArrOpc[k]["id"],
                            opc: ArrOpc[k]["opc"],
                            idVenta: parent.idVenta
                        }, function(data) {}, 'json');
                    }

                    for (let k = 0; k < agregaArr.length; k++) {
                        $.post('ws/venta_medio_pago.php', {
                            op: 'add',
                            id_venta: parent.idVenta,
                            medio: agregaArr[k]["medio"],
                            monto: agregaArr[k]["monto"],
                            vuelto: agregaArr[k]["vuelto"],
                            moneda: agregaArr[k]["moneda"]
                        }, function(data) {
                            if (data !== 0) {
                                parent.cargarPagos();
                                $("#txtmontopago").val("");
                                $("#txtmontopago").focus();
                            }
                        }, 'json');
                    }

                    if (parent.cliente.id == 0) {
                        if (agregaArr.length > 0) {
                            $.post('ws/cliente.php', {
                                op: 'add',
                                nombre: ventaCliente,
                                documento: ventaDoc,
                                direccion: ventaDireccion,
                                tipo_cliente: parent.tipoComprobante,
                                fecha_nacimiento: ventaFecha,
                                correo: correo
                            }, function(data) {
                                $.post('ws/venta.php', {
                                    op: 'end',
                                    id: parent.idVenta,
                                    subtotal: parent.subTotal,
                                    total_impuestos: parent.totalImpuestos,
                                    total: parent.montoTotal,
                                    tipo_comprobante: parent.tipoComprobante,
                                    id_cliente: 1,
                                    id_caja: parent.caja
                                }, function(data2) {
                                    parent.sendOrders();
                                }, 'json');
                            });
                        } else {
                            $.post('ws/cliente.php', {
                                op: 'add',
                                nombre: ventaCliente,
                                documento: ventaDoc,
                                direccion: ventaDireccion,
                                tipo_cliente: parent.tipoComprobante,
                                fecha_nacimiento: ventaFecha,
                                correo: correo
                            }, function(data) {
                                $.post('ws/venta_medio_pago.php', {
                                    op: 'add',
                                    id_venta: parent.idVenta,
                                    medio: $("#metodo").val(),
                                    monto: parent.montoAPagarCheck,
                                    vuelto: 0.00,
                                    moneda: 'PEN'
                                }, function(data0) {
                                    $.post('ws/venta.php', {
                                        op: 'end',
                                        id: parent.idVenta,
                                        subtotal: parent.subTotal,
                                        total_impuestos: parent.totalImpuestos,
                                        total: parent.montoTotal,
                                        tipo_comprobante: parent.tipoComprobante,
                                        id_cliente: 1,
                                        id_caja: parent.caja
                                    }, function(data2) {
                                        parent.sendOrders();
                                    }, 'json');
                                });
                            });
                        }


                    } else {
                        // alert(correo);
                        $.post('ws/cliente.php', {
                            op: 'mod',
                            id: parent.cliente.id,
                            nombre: ventaCliente,
                            documento: ventaDoc,
                            direccion: ventaDireccion,
                            tipo_cliente: parent.tipoComprobante,
                            fecha_nacimiento: ventaFecha,
                            correo: correo
                        }, function(data) {
                            console.log(data);

                            //Finalizamos Venta
                            console.log(parent.idVenta);
                            console.log(parent.subTotal);
                            console.log(parent.totalImpuestos);
                            console.log(parent.montoTotal);
                            console.log(parent.tipoComprobante);
                            console.log(parent.cliente.id);

                            alert(parseInt(parent.pagadoTotal - parent.montoTotal));
                            if (parseInt(parent.pagadoTotal - parent.montoTotal) < 0) {
                                $.post('ws/venta_medio_pago.php', {
                                    op: 'add',
                                    id_venta: parent.idVenta,
                                    medio: $("#metodo").val(),
                                    monto: parent.montoTotal,
                                    vuelto: 0.00,
                                    moneda: 'PEN'
                                }, function(data1) {

                                }, 'json');
                            }

                            $.post('ws/venta.php', {
                                op: 'end',
                                id: parent.idVenta,
                                subtotal: parent.subTotal,
                                total_impuestos: parent.totalImpuestos,
                                total: parent.montoTotal,
                                tipo_comprobante: parent.tipoComprobante,
                                id_cliente: parent.cliente.id,
                                id_caja: parent.caja
                            }, function(data2) {
                                alert();
                                parent.sendOrders();
                            }, 'json');

                        }, 'json');

                    }
                });

            }
        }
    };

    this.sendOrders = function() {
        var items = [];
        var i = 0,
            j = 0;
        $("#item:checked").each(function() {
            items[i] = $(this).val();
            i++;
        });

        j = $("#tablaventa tr").length;

        if (parseInt(parent.tipoComprobante) > 0) {
            let mData,
                header,
                details,
                mDetails,
                tipoAdq,
                numDocAdq,
                razSocAdq,
                dirAdq,
                correoAdq,
                numero,
                descuento = $("#descuento_venta").val(),
                oJson = [],
                subtotal, serie, tipoComp;

            // alert("AUX "+parent.tipoComprobanteAux);
            // alert("AUX "+parent.idVenta);
            // alert("AUX "+parent.tipoComprobante);
            $.post('ws/venta_medio_pago.php', {
                op: 'ordersHeader',
                id: parent.idVenta,
                comprobante: parent.tipoComprobante
            }, function(data) {

                mData = JSON.parse(data);
                console.log(mData);
                var d = new Date();
                let dd = d.getDay().toString();
                if (dd.length == 1)
                    dd = '0' + dd;
                let mm = d.getMonth().toString();
                if (mm.length == 1)
                    mm = '0' + mm;
                let aa = d.getFullYear().toString();
                let fecha_actual = dd + '/' + mm + '/' + aa;
                numero = mData.id;

                if (parent.tipoComprobante == 1) {
                    tipoComp = "2";
                    serie = mData.serie_boleta;
                    if ($("#documento_venta").val() == '') {
                        tipoAdq = '-';
                        numDocAdq = '-';
                        razSocAdq = '----';
                        dirAdq = '-';
                        correoAdq = '';

                    } else {
                        tipoAdq = '1';
                        numDocAdq = $("#documento_venta").val();
                        razSocAdq = $("#cliente_venta").val();
                        dirAdq = $("#direccion_venta").val();
                        correoAdq = $("#correo").val();
                    }
                } else {
                    tipoComp = "1";
                    serie = mData.serie_factura;
                    tipoAdq = '6';
                    numDocAdq = $("#documento_venta").val();
                    razSocAdq = $("#cliente_venta").val();
                    correoAdq = $("#correo").val();
                    dirAdq = $("#direccion_venta").val();
                }

                let total_gravada = 0,
                    total_inafecta = 0,
                    total_exonerada = 0,
                    total_gratuita = 0,
                    total_igv = 0,
                    total = 0,
                    tipo_de_igv = 0;

                // alert(tipoNubeFact);
                switch (tipoNubeFact) {
                    case 0:
                        total_gravada = "";
                        total_inafecta = parseFloat(mData.total).toFixed(2);
                        total_exonerada = "";
                        total_igv = "";
                        total_gratuita = "";
                        total = parseFloat(mData.total).toFixed(2);
                        tipo_de_igv = 9;
                        break;

                    case 1:
                        total_gravada = parseFloat(mData.subtotal).toFixed(2);
                        total_inafecta = "";
                        total_exonerada = "";
                        total_igv = parseFloat(mData.total_impuestos).toFixed(2);
                        total_gratuita = "";
                        total = parseFloat(mData.total).toFixed(2);
                        tipo_de_igv = 1;
                        break;

                    case 2:
                        total_gravada = "";
                        total_inafecta = "";
                        total_exonerada = parseFloat(mData.total).toFixed(2);
                        total_igv = "";
                        total_gratuita = "";
                        total = parseFloat(mData.total).toFixed(2);
                        tipo_de_igv = 8;
                        break;

                    case 3:
                        total_gravada = "";
                        total_inafecta = "";
                        total_exonerada = "";
                        total_igv = "";
                        total_gratuita = parseFloat(mData.total).toFixed(2);
                        total = parseFloat(mData.total).toFixed(2);
                        tipo_de_igv = 6;
                        break;

                }


                oJsonNubeFact = {
                    "operacion": "generar_comprobante",
                    "tipo_de_comprobante": tipoComp,
                    "serie": serie,
                    "numero": parseInt(numero),
                    "sunat_transaction": 1,
                    "cliente_tipo_de_documento": tipoAdq,
                    "cliente_numero_de_documento": numDocAdq,
                    "cliente_denominacion": razSocAdq,
                    "cliente_email": correoAdq,
                    "cliente_email_1": "",
                    "cliente_email_2": "",
                    "fecha_de_emision": fecha_actual,
                    "fecha_de_vencimiento": "",
                    "moneda": 1,
                    "tipo_de_cambio": "",
                    "porcentaje_de_igv": "18.00",
                    "descuento_global": descuento,
                    "total_descuento": descuento,
                    "total_anticipo": "",
                    "total_gravada": total_gravada,
                    "total_inafecta": total_inafecta,
                    "total_exonerada": total_exonerada,
                    "total_igv": total_igv,
                    "total_gratuita": total_gratuita,
                    "total_otros_cargos": "",
                    "total": total,
                    "percepcion_tipo": "",
                    "percepcion_base_imponible": "",
                    "total_percepcion": "",
                    "total_incluido_percepcion": "",
                    "detraccion": false,
                    "observaciones": "",
                    "documento_que_se_modifica_tipo": "",
                    "documento_que_se_modifica_serie": "",
                    "documento_que_se_modifica_numero": "",
                    "tipo_de_nota_de_credito": "",
                    "tipo_de_nota_de_debito": "",
                    "enviar_automaticamente_a_la_sunat": true,
                    "enviar_automaticamente_al_cliente": true,
                    "codigo_unico": "",
                    "condiciones_de_pago": "",
                    "medio_de_pago": "",
                    "placa_vehiculo": "",
                    "orden_compra_servicio": "",
                    "tabla_personalizada_codigo": "",
                    "formato_de_pdf": "TICKET",
                }

                console.log(oJsonNubeFact);

                let neto = 0,
                    total2 = 0,
                    igv = 0,
                    totalItems = 0,
                    unidadMedida;

                $.post('ws/venta_medio_pago.php', {
                    op: 'ordersDetailsUnion',
                    id: parent.idVenta
                }, function(data) {
                    mDetails = JSON.parse(data);
                    for (var i = 0; i < mDetails.length; i++) {


                        // alert(tipoNubeFact);
                        switch (tipoNubeFact) {
                            case 0:
                                neto = parseFloat(mDetails[i].precio);
                                subtotal = neto * mDetails[i].cantidad;
                                totalItems = (mDetails[i].precio * mDetails[i].cantidad);
                                igv = 0;
                                break;
                            case 1:
                                neto = (mDetails[i].precio / 1.18);
                                total2 = parseInt(mDetails[i].precio);
                                subtotal = neto * mDetails[i].cantidad;
                                totalItems = (mDetails[i].precio * mDetails[i].cantidad);
                                igv = ((mDetails[i].precio - neto) * mDetails[i].cantidad);
                                break;
                            case 2:
                                neto = parseFloat(mDetails[i].precio);
                                subtotal = neto * mDetails[i].cantidad;
                                totalItems = (mDetails[i].precio * mDetails[i].cantidad);
                                igv = 0;
                                break;
                            case 3:
                                neto = parseFloat(mDetails[i].precio);
                                subtotal = neto * mDetails[i].cantidad;
                                totalItems = (mDetails[i].precio * mDetails[i].cantidad);
                                igv = 0;
                                break;

                        }

                        if (mDetails[i].opc == 'p')
                            unidadMedida = 'NIU';
                        else
                            unidadMedida = 'ZZ';

                        oJsonItemsNIU = {
                            "unidad_de_medida": unidadMedida,
                            "codigo": mDetails[i].id,
                            "descripcion": mDetails[i].nombre,
                            "cantidad": mDetails[i].cantidad,
                            "valor_unitario": neto.toFixed(3),
                            "precio_unitario": parseFloat(mDetails[i].precio).toFixed(3),
                            "descuento": "",
                            "subtotal": subtotal.toFixed(3),
                            "tipo_de_igv": tipo_de_igv,
                            "igv": igv.toFixed(3),
                            "total": totalItems.toFixed(3),
                            "anticipo_regularizacion": false,
                            "anticipo_documento_serie": ""
                        }

                        oJson.push(oJsonItemsNIU);
                        neto = 0;
                        total2 = 0;
                    }

                    console.log(JSON.stringify(oJsonNubeFact));
                    console.log(JSON.stringify(oJson));

                    $.post('ws/venta_medio_pago.php', {
                        op: 'curl',
                        details: JSON.stringify(oJson),
                        header: JSON.stringify(oJsonNubeFact)
                    }, function(data) {
                        console.log("curl", data);
                        if(data != 'NE'){
                            let response = JSON.parse(data);
                            console.log(response)
                            if (response.errors == undefined) {

                                if (parent.tipoComprobante == 1) {
                                    tipoImprime = 'BOL';
                                } else {
                                    tipoImprime = 'FAC';
                                }

                                $.post('ws/venta_medio_pago.php', {
                                    op: 'endImprime',
                                    id: parent.idVenta,
                                    tipoImprime: tipoImprime,
                                    caja: parent.caja

                                }, function(data) {
                                    console.log('ENdImprime', data);

                                    if(guia == 1){
                                        $.post('ws/venta.php', {
                                        op: 'addGuia',
                                        id: parent.idVenta
                                        }, function(data) {
                                            $.post('ws/venta_medio_pago.php', {
                                                op: 'endImprime',
                                                id: parent.idVenta,
                                                tipoImprime: 'REM',
                                                caja: parent.caja
                                            }, function(data) {

                                            });
                                        });
                                    }

                                });
                                //Verificamos el tema de la descarga
                                $.post('ws/venta.php', { op: 'cambiadescarga' }, function(data) {
                                    if (data == 1) {
                                        // tienes que configurar el navegador para que permita desbloquear los pop ups y abrir la pestaña
                                        // if (parent.tipoComprobante == 1) {
                                        //     //window.open('http://35.204.182.136/usqayPOS/archivos_impresion/boleta.php?id='+parent.idVenta, '_blank');
                                        //     window.open('archivos_impresion/boleta.php?id=' + parent.idVenta, '_blank');
                                        // } else {
                                        //     //window.open('http://35.204.182.136/usqayPOS/archivos_impresion/factura.php?id='+parent.idVenta, '_blank');
                                        //     window.open('archivos_impresion/factura.php?id=' + parent.idVenta, '_blank');
                                        // }

                                    }
                                    if (parent.tipoComprobanteAux == -1) {
                                        $.post('ws/venta.php', {
                                            op: 'CambiaCredito',
                                            id: parent.idVenta
                                        }, function(data) {
                                            console.log("credito ", data);
                                        }, 'json');
                                    }
                                    if (items.length == j || items.length == 0) {
                                        location.href = "pantalla_teclado.php";
                                    } else {
                                        location.reload()
                                    }

                                }, 'json');
                            } else {
                                alert(response.errors);
                                // alert(parent.tipoComprobante);
                                // alert(parent.idVenta);
                                $.post('ws/venta.php', {
                                    op: 'foul',
                                    id: parent.idVenta,
                                    comprobante: parent.tipoComprobante
                                }, function(data) {
                                    if (data != 0) {
                                        console.log("Foul ", data);
                                        alert("La venta no pudo ser Finalizada y fue Anulada");
                                    }
                                    location.href = "pantalla_teclado.php";
                                }, 'json');


                            }
                        }else{
                            if (parent.tipoComprobante == 1) {
                                    tipoImprime = 'BOL';
                                } else {
                                    tipoImprime = 'FAC';
                                }

                                $.post('ws/venta_medio_pago.php', {
                                    op: 'endImprime',
                                    id: parent.idVenta,
                                    tipoImprime: tipoImprime,
                                    caja: parent.caja

                                }, function(data) {
                                    console.log('ENdImprime', data);

                                });
                                //Verificamos el tema de la descarga
                                $.post('ws/venta.php', { op: 'cambiadescarga' }, function(data) {
                                    // if (data == 1) {
                                    //     // tienes que configurar el navegador para que permita desbloquear los pop ups y abrir la pestaña
                                    //     if (parent.tipoComprobante == 1) {
                                    //         //window.open('http://35.204.182.136/usqayPOS/archivos_impresion/boleta.php?id='+parent.idVenta, '_blank');
                                    //         window.open('archivos_impresion/boleta.php?id=' + parent.idVenta, '_blank');
                                    //     } else {
                                    //         //window.open('http://35.204.182.136/usqayPOS/archivos_impresion/factura.php?id='+parent.idVenta, '_blank');
                                    //         window.open('archivos_impresion/factura.php?id=' + parent.idVenta, '_blank');
                                    //     }

                                    // }
                                    if (parent.tipoComprobanteAux == -1) {
                                        $.post('ws/venta.php', {
                                            op: 'CambiaCredito',
                                            id: parent.idVenta
                                        }, function(data) {
                                            console.log("credito ", data);
                                        }, 'json');
                                    }
                                    if (items.length == j || items.length == 0) {
                                        location.href = "pantalla_teclado.php";
                                    } else {
                                        location.reload()
                                    }

                                }, 'json');
                        }

                    });

                });
            });
        } else {

            if (parent.tipoComprobante == 0) {
                $.post('ws/venta_medio_pago.php', {
                    op: 'endImprime',
                    id: parent.idVenta,
                    tipoImprime: 'NOT',
                    caja: parent.caja

                }, function(data) {
                    console.log('ENdImprime', data);
                });
            }

            if (parent.tipoComprobanteAux == -1) {
                $.post('ws/venta.php', {
                    op: 'CambiaCredito',
                    id: parent.idVenta
                }, function(data) {
                    console.log("credito ", data);
                }, 'json');
            }

            if (items.length == j || items.length == 0) {
                location.href = "pantalla_teclado.php";
            } else {
                location.reload()
            }
        }

    }


    this.anularVenta = function() {
        // if (parent.idVenta != 0) {
        $.post('ws/venta.php', {
            op: 'anulaventa2',
            id: parent.idVenta
        }, function(data) {
            if (data == 1) {
                location.href = "pantalla_teclado.php";
            } else {
                alert("Ocurrió un error al anular la venta");
            }
        }, 'json');
        // }
    };

    this.buscaBarcode = function(codigo) {
        var operacion = $('input[name="tipo_op_codigo"]:checked').val();

        if (!codigo) {
            $('#contenedor_bloques_productos').html('');
            level1();
        } else {
            $.post('ws/taxonomiap.php', {
                op: 'searchByBarcode',
                valor: codigo
            }, function(data) {
                if (data !== 0) {

                    $('#contenedor_bloques_productos').html('');

                    var ht = '';
                    var csm = 0;
                    $.each(data, function(key, value) {
                        switch (csm) {
                            case 0:
                                if (offset_producto_bus === 0) {
                                    ht += '<button onclick="level1()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-level-up" aria-hidden="true"></i></button>';
                                    ht += '<button type="button" onclick="addprod1(' + value.id_producto.id + ',0,\'' + value + '\')" class="btn btn-default btn-lg btntax">' + value.id_producto.nombre + '<br/>S./' + value.id_producto.precio_venta + '</button>';
                                } else {
                                    ht += '<button onclick="prev_bus()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>';
                                }
                                break;

                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                                ht += '<button type="button" onclick="addprod1(' + value.id_producto.id + ',0,\'' + value + '\')" class="btn btn-default btn-lg btntax">' + value.id_producto.nombre + '<br/>S./' + value.id_producto.precio_venta + '</button>';
                                break;

                            case 7:
                                if (offset_producto_bus === 0) {
                                    offset_producto_bus = offset_producto_bus - 1;
                                } else {
                                    ht += '<button type="button" onclick="addprod1(' + value.id_producto.id + ',0,\'' + value + '\')" class="btn btn-default btn-lg btntax">' + value.id_producto.nombre + '<br/>S./' + value.id_producto.precio_venta + '</button>';
                                }
                                break;

                            case 8:
                                ht += '<button onclick="next_bus()" type="button" class="btn btn-default btn-lg btntax"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>';
                                break;
                        }
                        csm = csm + 1;

                        //Agregamos el ultimo resultado
                        $('#id_producto').val(value.id_producto.id);
                        $("#cantidad_producto").val(1);
                        $("#precio_producto").html(value.id_producto.precio_venta);
                    });

                    $('#contenedor_bloques_productos').html(ht);

                    if (operacion == 'true') {
                        if (csm == 1) {
                            $("#btnAgregaProducto").trigger("click");
                        }
                    } else {
                        var id_producto = $('#id_producto').val();
                        var id = $('.btnRestarProducto[data-producto=' + id_producto + ']').trigger("click");
                        $('input[name="tipo_op_codigo"][value=true]').trigger('click');
                    }

                    $("#txtCodProd").val("").focus();
                } else {
                    //busca cupón
                    parent.buscaCupon(codigo);
                }
            }, 'json');
        }
    };

    this.cargarCupones = function() {
        $.post('ws/ofertas.php', {
            op: 'getCupones',
            id_venta: parent.idVenta
        }, function(data) {
            $("#tabla_cupones").empty();
            var descuentos = 0;
            if (data != 0) {
                var row = '<tr><th colspan="5" class="text-center">CUPONES</th></tr>';
                row += '<tr><th colspan="2">CUPON</th><th colspan="2">DESCUENTO</th><th></th></tr>';
                data.forEach(function(value, number) {
                    row +=
                        '<tr>' +
                        '<td colspan="2">' + value.numero + '</td>' +
                        '<td colspan="2">' + value.descuento + '</td>' +
                        '<td><button class="btn-link text-red btnEliminaCupon" id="' + value.cupon + '"><i class="fa fa-trash-o"></i></button></td>' +
                        '</tr>';

                    descuentos = parseFloat(descuentos) + parseFloat(value.descuento);
                });

                $("#tabla_cupones").html(row)
            }

            parent.descuento = descuentos;
            $("#descuento_venta").val(parseFloat(parent.descuento).toFixed(2)).change();
        }, 'json');
    };

    this.buscaCupon = function(codigo) {
        console.log("buscando cupon");
        $.post('ws/ofertas.php', {
            op: 'searchCupon',
            cupon: codigo,
            id_venta: parent.idVenta
        }, function(data) {
            console.log(data);
            /*if (data!=0){
                switch (parseInt(data.tipo_oferta)) {
                    case 3: {
                        parent.descuento = parseFloat(parent.descuento) + parseFloat(parent.montoTotal * data.descuento / 100);
                        console.log(parent.descuento);
                        $("#descuento_venta").val(parseFloat(parent.descuento).toFixed(2)).change();
                        break;
                    }
                }
            }*/

            if (data != 0) {
                parent.cargarCupones();
            }

            $("#txtCodProd").val('').focus();

        }, 'json');
    };

    this.eliminaCupon = function(cupon) {
        $.post('ws/ofertas.php', {
            op: 'deleteCupon',
            cupon: cupon
        }, function(data) {
            if (data != 0) {
                parent.cargarCupones();
            }
            $("#txtCodProd").val('').focus();

        }, 'json');
    };

    this.cargarDescuentos = function() {
        $.post('ws/ofertas.php', {
            op: 'getDescuentosByVenta',
            id_venta: parent.idVenta
        }, function(data) {
            $("#list_ofertas").empty();
            var row = '';
            if (data != 0) {
                data.forEach(function(value, number) {
                    row += '<li>' + value + '</li>';
                });

                $("#list_ofertas").html(row);
            }
        }, 'json');
    }
}