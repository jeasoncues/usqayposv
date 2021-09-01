<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte de Factura';
$titulo_sistema = 'Katsu';
include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/venta.php');
include_once('nucleo/producto_venta.php');
include_once('nucleo/servicio_venta.php');
include_once('nucleo/producto.php');
include_once('nucleo/usuario.php');
include_once('nucleo/servicio.php');
include_once('nucleo/caja.php');
include_once('nucleo/cliente.php');
include_once('nucleo/turno.php');

$conn = new MasterConexion();
$obj = new venta();


require_once('recursos/componentes/header.php');
?>

<body>
<style>
        .content-wrapper {
            background-color: #FFF !important;
        }
    </style>
    <?php

    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d');
    // $fechaCierre=$obj->fechaCierre();
    if (isset($_GET['fecha_inicio'])) {
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }

    if (isset($_GET['fecha_fin']))
        $fechaFin = $_GET['fecha_fin'];

    $stockAnterior = 0.00;
    $stockIngreso = 0.00;
    $stockSalida = 0;
    $totalvendido =  number_format(0.000, 3);
    $totalimpuestos =  number_format(0.000, 3);

    $config = $conn->consulta_arreglo("Select * from configuracion");


    $objs = $conn->consulta_matriz("SELECT v.id, b.id_venta, v.subtotal, v.total_impuestos, v.total,v.tipo_comprobante, v.fecha_hora,v.fecha_cierre,v.id_turno,v.id_usuario, v.id_caja, v.id_cliente,v.estado_fila,b.id as id_factura FROM venta v inner join factura b on (v.id=b.id_venta) where v.fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' ");

    //    $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila = 1  and tipo_comprobante=1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
    //    $totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila = 1 and tipo_comprobante=1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
    //    $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila = 1 and tipo_comprobante=1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");




    //die();
    // $sucursal = UserLogin::get_pkSucursal();
    ?>

    <!--    <div class="container">-->

    <!--        <br /><br /><br />-->
    <!--        <h3>Kardex Resumen</h3>-->
    </form>
    <div class="row container">
        <p>
            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-filter"></i> Filtros
            </button>
        </p>
        <div class="collapse col-md-12" id="collapseExample">
            <div class='col-md-4'>
                <label>Fecha Inicio</label>
                <input type="date" id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>" />
            </div>

            <div class='col-md-4'>
                <label>Fecha Fin</label>
                <input type=date id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>" />
            </div>

            <div class='col-md-4' style="margin-top:27px;">
                <button type="button" onclick="buscar()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>
            </div>
        </div>
    </div>    

    <div class='contenedor-tabla' style="margin-top: 30px">
        <table id="tblKardex" title="Total de Ventas" class="display dataTable no-footer">
            <thead>
                <tr>
                    <th>
                        <center>Nro de Factura</center>
                    </th>
                    <th>
                        <center># Venta</center>
                    </th>
                    <th>
                        <center>Dni/RUC</center>
                    </th>
                    <th>
                        <center>Nombre/Razon</center>
                    </th>
                    <th>
                        <center>Direccion</center>
                    </th>
                    <th>
                        <center>Usuario</center>
                    </th>
                    <th>
                        <center>Turno</center>
                    </th>
                    <th>
                        <center>Caja</center>
                    </th>
                    <!-- <th><center>Tipo de Comprobante</center></th> -->
                    <th>
                        <center>Estado de Comprobante</center>
                    </th>
                    <th>
                        <center>Fecha y hora de Pedido</center>
                    </th>
                    <th>
                        <center>Fecha Cierre</center>
                    </th>
                    <th>
                        <center>SubTotal</center>
                    </th>
                    <th>
                        <center>Total Impuestos</center>
                    </th>
                    <th>
                        <center>Total</center>
                    </th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php
                if (is_array($objs)) :
                    $subtotal = 0;
                    $totalimpuestos = 0;
                    $totalvendido = 0;
                    foreach ($objs as $o) :
                        ?>
                        <tr>
                            <td style="text-align: center;"><b><?php echo $config['serie_factura'] . '-' . str_pad($o['id_factura'], 8, "0", STR_PAD_LEFT); ?></b></td>
                            <td style="text-align: center;"><?php echo ($o['id']); ?></td>

                            <td style="text-align: center;"><?php
                                                                    $objcliente = new cliente();
                                                                    $objcliente->setVar('id', $o['id_cliente']);
                                                                    $objcliente->getDB();
                                                                    echo $objcliente->getDocumento();

                                                                    ?></td>


                            <td style="text-align: center;"><?php
                                                                    $objcliente = new cliente();
                                                                    $objcliente->setVar('id', $o['id_cliente']);
                                                                    $objcliente->getDB();
                                                                    echo $objcliente->getNombre();
                                                                    ?></td>
                            <td style="text-align: center;"><?php
                                                                    $objcliente = new cliente();
                                                                    $objcliente->setVar('id', $o['id_cliente']);
                                                                    $objcliente->getDB();
                                                                    echo $objcliente->getDireccion();
                                                                    ?></td>
                            <td style="text-align: center;"><?php
                                                                    $objusuario = new usuario();
                                                                    $objusuario->setVar('id', $o['id_usuario']);
                                                                    $objusuario->getDB();
                                                                    echo $objusuario->getNombresYApellidos();
                                                                    ?></td>
                            <td style="text-align: center;"><?php
                                                                    $objoturno = new turno();
                                                                    $objoturno->setVar('id', $o['id_turno']);
                                                                    $objoturno->getDB();
                                                                    echo $objoturno->getNombre();
                                                                    ?></td>

                            <td style="text-align: center;"><?php
                                                                    $objocaja = new caja();
                                                                    $objocaja->setVar('id', $o['id_caja']);
                                                                    $objocaja->getDB();
                                                                    echo $objocaja->getNombre();
                                                                    ?>
                            </td>
                            <!-- <td style="text-align: center;"><?php
                                                                            if ($o['tipo_comprobante'] == 1) {
                                                                                echo "Boleta";
                                                                            } elseif ($o['tipo_comprobante'] == 2) {
                                                                                echo "Factura";
                                                                            } elseif ($o['tipo_comprobante'] == 0) {
                                                                                echo "Sin Comprobante";
                                                                            }
                                                                            ?></td> -->
                            <td style="text-align: center;"><?php
                                                                    if ($o['estado_fila'] == 1 || $o['estado_fila'] == 10) {
                                                                        echo "<span class='label label-success'>Emitida(".$o['estado_fila'].")</span>";
                                                                    } else if ($o['estado_fila'] == 2) {
                                                                        echo "<span class='label label-danger'>Anulada(".$o['estado_fila'].")</span>";
                                                                    } else if ($o['estado_fila'] == 3) {
                                                                        echo "<span class='label label-warning'>Nota de Credito(".$o['estado_fila'].")</span>";
                                                                    } else {
                                                                        echo "<span class='label label-info'>Nota de Debito(".$o['estado_fila'].")</span>";
                                                                    }
                                                                    ?></td>

                            <td style="text-align: center;"><?php echo ($o['fecha_hora']); ?></td>
                            <td style="text-align: center;"><?php echo $o['fecha_cierre']; ?></td>

                            <td style="text-align: center;"><?php echo number_format(floatval($o['subtotal']), 2); ?></td>
                            <td style="text-align: center;"><?php echo  number_format(floatval($o['total_impuestos']), 2); ?></td>
                            <td style="text-align: center;"><?php echo  number_format(floatval($o['total']), 2); ?></td>
                            <?php
                                    if ($o['estado_fila'] == 1 || $o['estado_fila'] == 10) {
                                        ?>

                                <td>
                                    <div class="btn-group" role="group">
                                        <a title="Reimprimir" class="btn btn-sm btn-default" href='#' onclick="reimprimir(<?php echo $o['id']; ?>,'FAC',<?php echo $_COOKIE["id_caja"]; ?>)"><i class="fa fa-print" aria-hidden="true"></i></a>
                                       
                                        <a title="Descargar Factura" class="btn btn-sm btn-info" onclick='DescargarBoleta(<?php echo $o['id_factura'] ?>)'><i class="fa fa-file-pdf-o" aria-hidden="true"></i></span></a>
                                       
                                        <a title="Anular Factura" class="btn btn-sm btn-danger" onclick='anularBoleta(<?php echo $o['id_factura'] ?>)'><span class='glyphicon glyphicon-remove-circle'></span></a>
                                    </div>
                                </td>
                            <?php
                                    } else
                                        echo "<td></td>";
                                    ?>
                            <?php
                                    if ($o['estado_fila'] == 1 || $o['estado_fila'] == 10) {
                                        if(!empty($o['subtotal'])){
                                            $subtotal = $subtotal + floatval(number_format($o['subtotal'], 2));
                                        }

                                        if(!empty($o['total'])){
                                            $totalvendido = $totalvendido + floatval(number_format($o['total'], 2));
                                        }

                                        if(!empty($o['total_impuestos'])){
                                            $totalimpuestos = $totalimpuestos + floatval(number_format($o['total_impuestos'], 2));
                                        }
                                        //$subtotal = $subtotal + floatval(number_format($o['subtotal'], 2));
                                        //$totalvendido = $totalvendido + floatval(number_format($o['total'], 2));
                                        //$totalimpuestos = $totalimpuestos + floatval(number_format($o['total_impuestos'], 2));
                                    };



                                    ?>

                        </tr>

                    <?php
                        endforeach; ?>
                    <tr>
                        <td style="text-align: center;"><b>TOTALES<b></td>
                        <td style="text-align: center;"><b></b></td>
                        <td style="text-align: center;"><b></b></td>

                        <td style="text-align: center;"><b></b></td>
                        <td style="text-align: center;"><b><b></b></td>
                        <td style="text-align: center;"><b><b></b></td>
                        <td style="text-align: center;"><b><b></b></td>
                        <td style="text-align: center;"><b><b></b></td>
                        <td style="text-align: center;"><b><b></b></td>
                        <td style="text-align: center;"><b><b></b></td>
                        <td style="text-align: center;"><b><b></b></td>
                        <td style="text-align: center;"><b><?php echo number_format(floatval($subtotal), 2, '.', ' '); ?></b></td>
                        <td style="text-align: center;"><b><?php echo number_format(floatval($totalimpuestos), 2, '.', ' '); ?></b></td>
                        <td style="text-align: center;"><b><?php echo number_format(floatval($totalvendido), 2, '.', ' '); ?></b></td>
                        <td></td>
                    </tr>

                <?php endif;
                ?>

                <?php
                $nombre_tabla = 'reporte_factura';
                require_once('recursos/componentes/footer.php');
                ?>
                <script src="recursos/js/notify.js"></script>
                <script type="text/javascript">
                    function anularBoleta(codComprobante) {
                        console.log("codComprobante", codComprobante);
                        let oJsonDescargaFact;

                        var serie = '<?php echo $config["serie_factura"]; ?>';

                        console.log(serie);

                        oJsonDescargaFact = {
                            "operacion": "generar_anulacion",
                            "tipo_de_comprobante": 1,
                            "serie": serie,
                            "numero": codComprobante
                        }

                        console.log("oJsonDescargaFact", oJsonDescargaFact);

                        $.post('ws/venta_medio_pago.php', {
                            op: 'AnulaFactCotos',
                            header: JSON.stringify(oJsonDescargaFact),
                        }, function(data) {
                            console.log(data);
                            if(data['success']==true){
                                $.notify(data['message']);
                                location.reload();
                            }else{
                                alert(data['message']);
                                $.notify(data['message']);
                            }
                            /*$.post('ws/venta_medio_pago.php', {
                                op: 'CambiaStatus',
                                id_boleta: codComprobante,
                            }, function(data) {
                                console.log(data);
                                location.reload();

                            }, 'json');*/
                        }, 'json');
                    }


                    function DescargarBoleta(codComprobante) {
                        console.log("codComprobante", codComprobante);
                        let oJsonDescargaFact;

                        var serie = '<?php echo $config["serie_factura"]; ?>';

                        console.log(serie);

                        oJsonDescargaFact = {
                            "operacion": "consultar_comprobante",
                            "tipo_de_comprobante": 1,
                            "serie": serie,
                            "numero": codComprobante
                        }

                        console.log("oJsonDescargaFact", oJsonDescargaFact);

                        $.post('ws/venta_medio_pago.php', {
                            op: 'descargaFact',
                            header: JSON.stringify(oJsonDescargaFact),
                        }, function(data) {
                            console.log(data["enlace_del_pdf"]);
                            window.open(data["enlace_del_pdf"], "_blank");
                        }, 'json');
                    }

                    function reimprimir(id, tipo, caja) {
                        if (confirm("¿Está seguro de re-imprimir esta venta?")) {
                            $.post('ws/venta.php', {
                                op: 'imprimir',
                                id: id,
                                tipo: tipo,
                                id_caja: caja
                            }, function(data) {
                                if (data !== 0) {
                                    alert("Reimpreso con éxito");
                                } else {
                                    alert("Ocurrió un error al anular la venta");
                                }
                            }, 'json');
                        }
                    }
                </script>