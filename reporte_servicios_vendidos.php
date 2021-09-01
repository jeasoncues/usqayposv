<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte Ventas de Servicios';
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
<style>
    .mt-25 {
        margin-top: 25px;
    }

    .tarjeta {
        border: 1px solid #000;
    }

    .tarjeta h3 {
        font-size: 4rem;
    }
</style>

<body>
    <style>
        .content-wrapper {
            background-color: #FFF !important;
        }
    </style>

    <?php

    $fechaInicio = date('Y-m-01');
    $fechaFin = date('Y-m-d');
    // $fechaCierre=$obj->fechaCierre();
    /* if (isset($_GET['fecha_inicio'])) {
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }

    if (isset($_GET['fecha_fin']))
        $fechaFin = $_GET['fecha_fin'];

    $objs = $conn->consulta_matriz(
        "SELECT pv.id_servicio AS id_servicio, pv.cantidad AS cantidad, pv.total AS totalventa, v.fecha_cierre
                    FROM servicio_venta pv
                    INNER JOIN venta v ON (v.id=pv.id_venta)
                    WHERE v.estado_fila = 1 AND v.fecha_cierre BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
                    ORDER BY totalventa DESC"
    );
    $totalvendido = $conn->consulta_arreglo("SELECT sum(sv.total)as total FROM venta v inner join servicio_venta sv on (v.id=sv.id_venta) where v.estado_fila=1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59'"); */



    //die();
    // $sucursal = UserLogin::get_pkSucursal();
    ?>

    <div class="row container">
        <div class="col-md-12">
            <p>
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-filter"></i> Filtros
                </button> 
            </p>
            <div class="collapse col-md-12" id="collapseExample">
                <form id="form-servicios">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Fecha Inicio</label>
                            <input type="date" id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>" />
                        </div>
                        <div class="col-md-4">
                            <label>Fecha Fin</label>
                            <input type=date id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>" />
                            <input type="hidden" name="op" value="reporte">
                        </div>
                        <div class="col-md-4" style="margin-top:27px;">
                            <button type="click" id="btnreporteservicios" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row col-md-12">
        <div class="col-md-6 col-sm-6">
            <canvas id="myChart2" width="400" height="220"></canvas>
        </div>
        <div class="col-md-6 col-sm-6">
            <canvas id="myChart" width="400" height="220"></canvas>
        </div>
    </div>

    <div class="row container">
        <div class="col-md-12" style="margin-top: 25px;">


            <table id="tbl-productos" class="table table-bordered responsive">
                <thead>
                    <tr>
                        <th>
                            <center>Nombre Servicio</center>
                        </th>
                        <th>
                            <center>Cantidad de Servicios vendidos</center>
                        </th>
                        <th>
                            <center>Precio de Venta Unitario</center>
                        </th>
                        <th>
                            <center>Total en Venta</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="4">No se seleccionó una fecha</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center" colspan="3">Totales</th>
                        <th class="text-center">S/ 0.00</th>
                    </tr>
                </tfoot>
            </table>


        </div>

    </div>

    <div class="col-md-3" style="display: none;">
        <div class="panel tarjeta">
            <div class="panel-body text-center">
                <h3>S./ <?php echo number_format((floatval($totalvendido['total'])), 3); ?></h3>
                <p>TOTAL DE VENTA EN SERVICIO </p>
            </div>
        </div>
    </div>

    <script>
        var lenguaje = {
            "sProcessing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ filas por pagina",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando del _START_ al _END_ de _TOTAL_ ",
            "sInfoEmpty": "Mostrando del 0 al 0 de 0 registros",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sSearchPlaceholder": "Dato a buscar",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        };
    </script>

    <?php
    $nombre_tabla = 'reporte_servicios_vendidos';
    require_once('recursos/componentes/footer.php');
    ?>