<?php 
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Kardex de Productos';
$titulo_sistema = 'Katsu';
include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/venta.php');
include_once('nucleo/producto_venta.php');
include_once('nucleo/servicio_venta.php');
include_once('nucleo/producto.php');
include_once('nucleo/usuario.php');
include_once('nucleo/servicio.php');
include_once('nucleo/almacen.php');
include_once('nucleo/movimiento_producto.php');
include_once('nucleo/turno.php');

$conn = new MasterConexion();
$obj= new venta();
           
require_once('recursos/componentes/header.php');

$almacenes = $conn->consulta_matriz("Select * from almacen where estado_fila = 1");
?>
<style>
    .tabla{
        margin-top: 20px;
    }
    .tabla2{
        margin-top: 20px;
        padding: 20px;
    }
    .tr1{
        background: #295a9c;
        color: #fff;
        font-weight: bold;
    }
    .tr2{
        background: #75933d;
    }
    
</style>
<body>
    <?php
  
    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d');
    $almacen = 0;
    
    if (isset($_GET['fecha_inicio'])){
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }
    
    if (isset($_GET['fecha_fin'])){
        $fechaFin = $_GET['fecha_fin'];
    }
    if (isset($_GET['id_almacen'])) {
        $almacen = $_GET["id_almacen"];
    }

    $count = $conn->consulta_arreglo("SELECT count(*) as cantidad FROM producto");
    // echo $count["cantidad"];
    $objs = $conn->consulta_matriz(
            "SELECT p.id AS id, p.nombre AS nombre, SUM(mp.cantidad) AS cantidad
            FROM producto p
            INNER JOIN movimiento_producto mp ON (mp.id_producto=p.id)
            INNER JOIN almacen a ON (mp.id_almacen=a.id)
            WHERE mp.id_almacen = '{$almacen}' AND 
            mp.estado_fila = 1 AND 
            mp.fecha_cierre <= '{$fechaInicio}'
            GROUP BY mp.id_producto
            ORDER BY nombre ASC");
    ?>   
    <div class="container-fluid">
        <div class="panel">
            <div class="panel-body">
                <div class="page-header">
                    <h3>Reporte de Productos en Almacen</h3>
                </div>
                <form class="form-horizontal">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="txtfechaini" class="col-sm-4 control-label">Fecha Cierre:</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                                <input aria-describedby="basic-addon1" type="date" id="txtfechaini" class="form-control" name='fecha_inicio' value="<?php echo $fechaInicio ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="caja" class="col-sm-2 control-label">Caja:</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon2"><i class="fa fa-archive"></i></span>
                                
                                <select aria-describedby="basic-addon2" class='form-control' id='id_almacen' name='id_almacen' >
                                    <?php 
                                    if(is_array($almacenes)){
                                        foreach($almacenes as $alv){
                                            $selected = $almacen==$alv['id']?"selected":"";
                                            echo "<option value='{$alv["id"]}' {$selected}>{$alv["nombre"]}</option>";
                                        }
                                    }
                                    ?>   
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <button type="button" id="boton-tabla" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
                
                </form>
                <div class="tabla">
                
                <table id="table"
                data-show-export="true"
                data-export-types="['excel']"
                data-toggle="table"
                data-sort-name="id"
                data-click-to-select="true"
                data-toolbar="#toolbar"
                data-sort-order="desc"
                data-search="true"
                data-pagination="true"               
                data-page-size="20"
                data-page-list="[20,50,100,<?php echo $count["cantidad"]; ?>]"
                data-pagination-first-text="Primero"
                data-pagination-pre-text="Anterior"
                data-pagination-next-text="Siguiente">
                    <thead>
                        <tr>
                            <th class="text-center tr1" colspan="11">Kardex</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="text-center" data-field="id">ID</th>
                            <th rowspan="2" class="text-center" data-sortable="true" data-field="nombre">Detalle</th>
                           <!--  <th colspan="1" class="text-center">Entradas</th>
                            <th colspan="1" class="text-center">Salidas</th> -->
                            <th colspan="1" class="text-center">Existencias</th>
                            <th rowspan="2"
                                data-field="operate"
                                data-align="center"
                                data-formatter="operateFormatter"
                                data-events="operateEvents">Movimientos</th>
                        </tr>
                        <tr>
                            <!-- <th class="text-center" data-field="cantidad1">Cant.</th> -->
                            <!--<th class="text-center" data-field="valor1">V/ Unit.</th>-->
                            <!--<th class="text-center" data-field="total1">V/ Tot.</th>-->

                            <!-- <th class="text-center" data-field="cantidad2">Cant.</th> -->
                            <!--<th class="text-center" data-field="valor2">V/ Unit.</th>-->
                            <!--<th class="text-center" data-field="total2">V/ Tot.</th>-->

                            <th class="text-center" data-field="cantidad3" data-sortable="true" data-cell-style="cellStyle">Cant.</th>
                            
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
            
                <div class="tabla2">
                    <table id="tbl-k" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Almacen</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Costo</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Tipo Movimiento</th>
                                <th class="text-center">Usuario</th>
                                <th class="text-center">Turno</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Fecha Cierre</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="10">No ha seleccionado un producto</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center" colspan="2">Stock Actual</th>
                                <th class="text-center">0</th>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="4">Total Vendido</th>
                                <th class="text-center">0</th>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="4">Total Gastado</th>
                                <th class="text-center">0</th>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="4">Total Ganancia</th>
                                <th class="text-center">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            
        </div>
    </div>
    
    <?php
            $nombre_tabla = 'reporte_kardex_productos';
            require_once('recursos/componentes/footer.php');
            ?>
