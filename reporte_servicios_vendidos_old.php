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
$obj= new venta();

                
require_once('recursos/componentes/header.php');
?>
<style>
    .mt-25{
        margin-top: 25px;
    }

    .tarjeta{
        border: 1px solid #000;
    }

    .tarjeta h3{
        font-size: 4rem;
    }
</style>
<body>

    <?php
   
    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d');
   // $fechaCierre=$obj->fechaCierre();
    if (isset($_GET['fecha_inicio'])){
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
                    ORDER BY totalventa DESC");
  $totalvendido = $conn->consulta_arreglo("SELECT sum(sv.total)as total FROM venta v inner join servicio_venta sv on (v.id=sv.id_venta) where v.estado_fila=1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
    
   
   
//die();
// $sucursal = UserLogin::get_pkSucursal();
    ?>   
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="page-header">
                                    Filtrar por Fechas
                            </div>
                            <div class="col-md-9" id="dninicio">
                                
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Inicio</label>
                                        <input type="date" id="txtfechaini" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha Fin</label>
                                        <input  type=date id="txtfechafin" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" onclick="buscar()" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                                </div>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <div class="panel tarjeta">
                                    <div class="panel-body text-center">
                                        <h3>S./ <?php echo number_format((floatval($totalvendido['total'])),3);?></h3>
                                        <p>TOTAL DE VENTA EN SERVICIO </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class='contenedor-tabla'>
                                    <table id="tblVentasServicio" title="Pedidos Entregados" class="display dataTable no-footer" >
                                        <thead>
                                            <tr>
                                                <th><center>Nombre Servicio</center></th>
                                                <th><center>Cantidad de Servicios vendidos</center></th>
                                                <th><center>Precio de Venta Unitario</center></th>
                                                <th><center>Total en Venta</center></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                if (is_array($objs)):
                                                    $totalventas=0;
                                                    foreach ($objs as $o):
                                                        ?>
                                                        <tr>
                                                            <td style="text-align: center;"><?php
                                                            $objservicio = new servicio();
                                                            $objservicio->setVar('id', $o['id_servicio']);
                                                            $objservicio->getDB();
                                                            echo $objservicio->getNombre();
                                                            ?></td>
                                                            <td style="text-align: center;"><?php echo $o['cantidad']; ?></td>
                                                        
                                                            <td style="text-align: center;"><?php
                                                            $objservicio = new servicio();
                                                            $objservicio->setVar('id', $o['id_servicio']);
                                                            $objservicio->getDB();
                                                            echo number_format($objservicio->getPrecioVenta(),3);
                                                            ?></td>
                                                        
                                                            <td style="text-align: center;"><?php echo number_format($o['totalventa'],3);
                                                            $totalventas=$totalventas+$o['totalventa'];
                                            ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                            endforeach;
                                                        ?>
                                                        <tr>
                                                            <td style="text-align: center;"><b>TOTALES<b></td>
                                                            <td style="text-align: center;"><b><b></b></td>
                                                            <td style="text-align: center;"><b><b></b></td>
                                                            <td style="text-align: center;"><b><?php echo number_format($totalventas, 3, '.', ' '); ?></b></td>

                                                        </tr>                  
                                            <?php  
                                                endif;
                                            ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
    <?php
            $nombre_tabla = 'reporte_servicios_vendidos_old';
            require_once('recursos/componentes/footer.php');
            ?>
