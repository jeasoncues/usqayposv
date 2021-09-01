<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte de Productos / Utilidades';
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
$ranking_utilidad = new Producto();

require_once('recursos/componentes/header.php');
?>
<style>
    .bordeado {
        border: 1px solid #000;
    }
</style>

<body>
    <style>
        .content-wrapper {
            background-color: #FFF !important;
        }
    </style>
    <?php



    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d');
  


if (isset($_GET['fecha_inicio'])) {
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }

    if (isset($_GET['fecha_fin'])) {
        $fechaFin = $_GET['fecha_fin'];
    }


 $ranking_utilidad = $conn->consulta_matriz(
                       "SELECT p.id as id,p.nombre as nombre , p.precio_compra as precio_compra,p.precio_venta as precio_venta,(p.precio_venta - p.precio_compra)as utilidad ,SUM(cantidad) as cantidad,(SUM(cantidad)*(p.precio_venta - p.precio_compra) ) as utilidad_total
                        FROM producto_venta pv 
                        inner join producto p on pv.id_producto = p.id 
                        inner join venta ven on pv.id_venta= ven.id
                        WHERE pv.estado_fila=1 AND ven.fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59'  AND ven.total IS NOT NULL 
                        GROUP BY pv.id_producto 
                        ORDER BY utilidad_total
                        DESC");

   


    ?>
    </form>
    <div class="row container">
        <div class="col-md-12">
            <p>
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-filter"></i> Filtros
                </button>
            </p>
            <div class="collapse col-md-12" id="collapseExample">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Fecha Inicio</label>
                            <input type="date" id="txtfechaini" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>" />
                        </div>
                        <div class="form-group">
                            <label>Fecha Fin</label>
                            <input type=date id="txtfechafin" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>" />
                        </div>
                        
                        <div class="form-group"> 
                            <button type="button" onclick="buscar()" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
       
        
        <div class="row container" style="margin-top:30px">
            <div class="">
                <div class="panel">
                    <div class="panel-body">
                    
                        <div class='contenedor-tabla'>
                            <table id="tblKardex" title="Total de Ventas" class="display dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>
                                            <center>Id Producto</center>
                                        </th>
                                        <th>
                                            <center>Nombre Producto</center>
                                        </th>
                                        <th>
                                            <center>Precio Compra</center>
                                        </th>
                                        <th>
                                            <center>Precio Venta</center>
                                        </th>
                                        <th>
                                            <center>Utilidad</center>
                                        </th>
                                        <th>
                                            <center>Cantidad</center>
                                        </th>
                                        <th>
                                            <center>Utilidad Total</center>
                                        </th>
                                     
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php
                                    $total_desc = 0;
                                    $config =  $conn->consulta_arreglo("SELECT * FROM configuracion");
                                    if (is_array($ranking_utilidad)) :
                                        foreach ($ranking_utilidad as $o) :
                                            $dscto = 0;
                                    ?>
                                            <tr>
                                                <td style="text-align: center;"><?php echo $o['id']; ?></td>
                                                <td style="text-align: center;"><?php echo $o['nombre'];  ?></td>
                                                <td style="text-align: center;"><?php echo $o['precio_compra']; ?></td>
                                                <td style="text-align: center;"><?php echo $o['precio_venta'];   ?></td>
                                                <td style="text-align: center;"><?php  echo $o['utilidad']; ?> </td>
                                                <td style="text-align: center;"> <?php echo $o['cantidad'];?>  </td>
                                                <td style="text-align: center;"> <?php echo $o['utilidad_total']; ?> </td>
                                              
                                            </tr>
                                        <?php
                                        endforeach; ?>

                                    <?php endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
  
    <?php
    $nombre_tabla = 'reporte_ventas_utilidades';
    require_once('recursos/componentes/footer.php');
    ?>
    <script src="recursos/js/notify.js"></script>