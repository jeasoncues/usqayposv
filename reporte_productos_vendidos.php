<?php 
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte Ventas de Productos / Productos Vendidos';
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

    <?php

    $fechaInicio = date('Y-m-01');
    $fechaFin = date('Y-m-d');
    // $fechaCierre=$obj->fechaCierre();
    if (isset($_GET['fecha_inicio'])) {
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }

    if (isset($_GET['fecha_fin']))
        $fechaFin = $_GET['fecha_fin'];

    $stockAnterior = 0;
    $stockIngreso = 0;
    $stockSalida = 0;

    $objs = $conn->consulta_matriz(
        "SELECT pv.id_producto AS id_producto, SUM(pv.cantidad) AS cantidad, SUM(round(pv.total,2)) AS totalventa, v.fecha_cierre
                    FROM producto_venta pv
                    INNER JOIN venta v ON v.id = pv.id_venta
                    WHERE v.estado_fila = 1 AND v.fecha_cierre BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
                    GROUP BY pv.id_producto
                    ORDER BY totalventa DESC"
    );
    $totalvendido = $conn->consulta_arreglo("SELECT sum(pv.total)as total FROM venta v inner join producto_venta pv on (v.id=pv.id_venta) where v.estado_fila=1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59'");

    ?>
    </form>
    <style>
        .content-wrapper {
            background-color: #FFF !important;
        }
    </style>
    <div class="row container">

        <p>
            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-filter"></i> Filtros
            </button>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="panel panel-body col-12">
                <form method="POST" action="ws/venta.php" id="form-ventas" class="row">
                    <div class="form-group col-6 col-sm-6 col-md-4 col-lg-4 col-xs-3">
                        <label for="">Fecha Inicio</label>
                        <input type="date" id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>" />
                    </div>
                    <div class="form-group col-6 col-sm-6 col-md-4 col-lg-4 col-xs-3">
                        <label>Fecha Fin</label>
                        <input type=date id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>" />
                        <input type="hidden" name="op" value="reporte">
                    </div>
                    <div class="form-group col-6 col-sm-6 col-md-4 col-lg-4 col-xs-3" style="margin-top:27px;">
                        <button type="submit" class="btn btn-primary "><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row col-md-12">
        <div class="col-md-6">
            <canvas id="myChart2" width="400" height="220"></canvas>    
        </div>
        <div class="col-md-6">
            <canvas id="myChart" width="400" height="220"></canvas>
        </div>
    </div>

    <div class="row" style="margin-top: 40px;">
        <!-- <div class="col-md-3">
            <div class="panel">
                <div class="panel-body">
                    <div class="page-header text-center">
                        <h3>Filtros por fechas</h3>
                    </div>
                    <form  method="POST" action="ws/venta.php" id="form-ventas">
                        <div class="form-group">
                            <label for="">Fecha Inicio</label>
                            <input type="date" id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                        </div>
                        <div class="form-group">
                            <label>Fecha Fin</label>
                            <input  type=date id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                            <input type="hidden" name="op" value="reporte">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                </div>
                <div class="col-md-6">
                    <div id="chartContainer2" style="height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div> -->
        <div class="col-md-6" style="display: none;">
            <div id="chartContainer" style="height: 300px; width: 100%;"></div>
        </div>
        <div class="col-md-6" style="display: none;">
            <div id="chartContainer2" style="height: 300px; width: 100%;"></div>
        </div>
    </div>    
    <div class="row container" style="margin-top: 40px;">
        <div class="col-md-12" style="margin-top: 25px;">
            
                
                    <table id="tbl-productos" class="table table-bordered responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Producto</th>
                                <th>Cantidad de Productos vendidos</th>
                                <th>Precio de Compra</th>
                                <th>Precio de Venta Unitario</th>
                                <th>Utilidad por Producto</th>
                                <th>Total en Venta</th>
                                <th>Utilidad Total en venta del Producto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="7">No se seleccionó una fecha</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center" colspan="7">Utilidad Total</th>
                                <th class="text-center">S/ 0.00</th>
                            </tr>
                             <tr>
                                <th class="text-center" colspan="7">Descuentos</th>
                                <th class="text-center">S/ 0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                
            
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
    $nombre_tabla = 'reporte_productos_vendidos';
    require_once('recursos/componentes/footer.php');
    ?>

