<?php
session_start();
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
if ($_COOKIE['tipo_usuario']==3) {
    header('Location: entrada_salida.php');
}



require_once 'nucleo/include/MasterConexion.php';
require_once 'nucleo/cliente.php';
// require_once 'ws/Facturacion.php';


$objcon = new MasterConexion();
$objcliente = new cliente();
/* $facturacion = new Facturacion(0,11);

var_dump($facturacion->procesarItem()); */
use PHPMailer\PHPMailer\PHPMailer;


function mapped_implode($glue, $array, $symbol = '=') {
    return implode($glue, array_map(
            function($k, $v) use($symbol) {
                /* if(!empty($v)){
                    return $k . $symbol . 'sin valor';
                }else{
                    return $k . $symbol . $v;
                }                 */
                return $k . $symbol . $v;
            },
            array_keys($array),
            array_values($array)
            )
        );
}
function execInBackground($cmd) {
    
    error_reporting(E_ALL);    
    if (substr(php_uname(), 0, 7) == "Windows"){  
        echo("entro windowsssssssssssssssssssssssss/");        
        //pclose(popen("start /B ". $cmd, "r"));
        $gestor = popen("start /B ". $cmd, "r");
        echo "'$gestor'; " . gettype($gestor) . "\n";
        $leer = fread($gestor, 2096);
        echo $leer; 
    }
    else {        
        exec($cmd . " > /dev/null &");  
    }
}














?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>UsqayPOS</title>
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="recursos/adminLTE/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="recursos/js/plugins/datatables/jquery-datatables.css" rel="stylesheet">
    <link href="recursos/css/bootstrap-overrides.css" rel="stylesheet">
    <link href="recursos/css/jquery-ui.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="usqay-icon.svg">

    <!-- Morris para graficos -->
    <link rel="stylesheet" href="recursos/js/plugins/datatables/morris.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="recursos/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="recursos/fa/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="recursos/adminLTE/dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. We have chosen the skin-green for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="recursos/adminLTE/dist/css/skins/skin-blue.min.css">

</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-blue                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<?php
/* require_once 'nucleo/include/MasterConexion.php';
$objcon = new MasterConexion(); */

$cierre = $objcon->consulta_arreglo("SELECT fecha_cierre FROM configuracion LIMIT 1")['fecha_cierre'];


$descuento_diario = $objcon->consulta_arreglo("SELECT ROUND(SUM(monto),2) as descuento
FROM venta v
INNER JOIN venta_medio_pago vm ON vm.id_venta = v.id
WHERE v.fecha_cierre = '{$cierre}' AND medio = 'DESCUENTO' AND v.estado_fila = 1 AND id_caja = '{$_COOKIE["id_caja"]}'");

$total_v = $objcon->consulta_matriz("SELECT * FROM venta where fecha_cierre = '{$cierre}' AND tipo_comprobante <> -1 AND estado_fila IN (1,3) AND id_caja = '{$_COOKIE["id_caja"]}'");
$sumTotal =0;
$restaTotal =0;
$total_venta  =0;
if(is_array($total_v)){
    foreach ($total_v as $v) {
        $sumTotal += $v["total"];
        $flag = $objcon->consulta_arreglo("SELECT * FROM ventas_notas WHERE id_venta_nota = ".$v['id']);
        if(!empty($flag)){
            $restaTotal += $flag['total'];   
        }        
    }

    $total_venta = $sumTotal - $restaTotal;
}


// $total_venta = $objcon->consulta_arreglo("SELECT ROUND(SUM(total),2) AS total
//                 FROM venta
//                 WHERE estado_fila = 1 AND id_caja = '{$_COOKIE["id_caja"]}' AND
//                 fecha_cierre = '{$cierre}'");

$tipo_cambio = $objcon->consulta_arreglo("SELECT * FROM tipo_cambio");
$hora_punta = $objcon->consulta_arreglo(
    "select count(*) as cantidad, HOUR(fecha_hora) as punta
            from venta
            group by punta
            order by cantidad desc
            limit 1");

$count_retomar = $objcon->consulta_arreglo("SELECT COUNT(*) as cantidad FROM venta where subtotal is NULL AND estado_fila = 1 AND id_caja = '{$_COOKIE["id_caja"]}'");
$count_comprobantes = $objcon->consulta_arreglo("SELECT COUNT(*) as cantidad FROM venta WHERE tipo_comprobante IN(1,2) AND fecha_cierre = '{$cierre}' AND estado_fila = 1");
$dataPoints = array();
$dataPoints_utilidad = array();
$mas_vendidos = $objcon->consulta_matriz("SELECT p.nombre as label ,SUM(cantidad) as y FROM producto_venta pv inner join producto p on pv.id_producto = p.id WHERE pv.estado_fila=1 AND pv.id_venta IN(SELECT id from venta where estado_fila <> '2') GROUP BY pv.id_producto ORDER BY y DESC limit 5");
$mas_utilidad = $objcon->consulta_matriz("SELECT p.nombre as label , (SUM(cantidad)*(p.precio_venta - p.precio_compra) ) as y
FROM producto_venta pv inner join producto p on pv.id_producto = p.id 
WHERE pv.estado_fila=1 AND pv.id_venta IN(
			SELECT id 
			from venta 
			where estado_fila <> '2') 
GROUP BY pv.id_producto 
ORDER BY y
DESC limit 5");
$sql = "SELECT COUNT(p.nombre) as cant
FROM movimiento_producto mp
INNER JOIN producto p ON mp.id_producto = p.id
INNER JOIN almacen a ON mp.id_almacen = a.id
INNER JOIN guia_movimiento gm ON mp.id = gm.id_movimiento_producto
WHERE mp.cantidad<> 0 AND tipo_movimiento = 'ALMACEN' AND fecha_vencimiento <= ADDDATE(now(), interval 30  DAY) AND fecha_vencimiento >= (now())";
$vencidos = $objcon->consulta_arreglo($sql);
$creditos= $objcon->consulta_arreglo("SELECT count(id_cliente) as cant FROM venta WHERE tipo_comprobante = -1 AND estado_fila = 1  GROUP by id_cliente");

if($mas_vendidos != 0){
    foreach ($mas_vendidos as $key => $value) {
        $dataPoints[] = $value;
    }
}
if($mas_utilidad != 0){
    foreach ($mas_utilidad as $key => $value) {
        $dataPoints_utilidad[] = $value;
    }
}


$clientesCredito = $objcon->consulta_matriz("SELECT id_cliente, SUM(total) as total FROM venta WHERE tipo_comprobante = -1 AND estado_fila = 1  GROUP by id_cliente");

?>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
    <style>
        .cotoscard{
            
            /* box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;*/
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 6px -1px, rgba(0, 0, 0, 0.06) 0px 2px 4px -1px;
        }
    </style>
<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
                <a href="index.php" class="logo"  style="background: #00395e !important;">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span  class="logo-mini"><img src='recursos/img/usqay-circle-icon.svg' width="80%"></span>
                    <!-- logo for regular state and mobile devices -->
                    <span  class="logo-lg"><img src='recursos/img/usqay_logo.png'  height="60px"></span>
                </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation" style="background: #00395e !important;">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">Hola, <?php echo $_COOKIE["nombre_usuario"]; ?> <span
                                        class="caret"></span></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <p>
                                    <?php echo $_COOKIE["nombre_usuario"]; ?>
                                    <small>Usuario del Sistema</small>
                                </p>
                            </li>

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="manual/index.html" target="_blank" class="btn btn-warning btn-flat">Manual</a>
                                </div>
                                <div class="pull-right">
                                    <a href="logout_sistema.php" class="btn btn-default btn-flat">Salir del Sistema</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <?php include_once('navbar_sistema.php'); ?>
            </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style=" background-color: #FFF !important;">
        <section class="content">
            <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?=$_SESSION['mensaje']['tipo']?>">
                <?= htmlspecialchars_decode($_SESSION['mensaje']['texto']) ?>
            </div>
            <?php endif; ?>
            <div class="row">
                <!-- <div><?php var_dump($_COOKIE); ?></div> -->
                <div class="col-xs-6 col-md-3 " >
                    <!--<a href="reporte_ventas_totales.php">-->
                        <div class="panel cotoscard">
                            <div class="panel-body">
                                <div class="row titulos">
                                    <div class="col-md-6">
                                        <h2>
                                            <?php 
                                            if(!is_numeric($descuento_diario['descuento']))
                                            $descuento_diario['descuento'] = 0; ?>
                                            S./<?php echo empty($total_venta) ? '0.0' : round($total_venta - $descuento_diario["descuento"],2); ?>
                                        </h2>
                                        <h3>$
                                            <?php 
                                                if(empty($total_venta))
                                                    $ventas = 0;
                                                else
                                                    $ventas = $total_venta;

                                                echo round($ventas / $tipo_cambio['venta'], 2); 
                                            ?>
                                        </h3>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <img src="recursos/img/sales.png" class="img-responsive" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--</a>-->
                </div>
                <div class="col-xs-6 col-md-3 " >
                    <div class="panel cotoscard">
                        <div class="panel-body">
                            <div class="row titulos">
                                <div class="col-md-6">
                                    <h2>
                                        <?php
                                        if (empty($hora_punta['punta'])) {
                                           $hp = 0;
                                        }else
                                            $hp = $hora_punta['punta'];

                                        if ($hp > 12) {
                                            $hp -= 12;
                                            $hp .= ' P.M';
                                        } else {
                                            $hp .= ' A.M';
                                        }

                                        echo $hp;

                                        ?>
                                    </h2>
                                    <h4>Hora Punta</h4>
                                </div>
                                <div class="col-md-6">
                                    <img src="recursos/img/time-is-money.png" class="img-responsive" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 " >
                    <div class="panel cotoscard">
                        <div class="panel-body">
                            <div class="row titulos">
                                <div class="col-md-6">
                                    <h2>
                                        <?php echo $count_retomar['cantidad'] ?>
                                    </h2>
                                    <h4>Ventas por Retomar</h4>
                                </div>
                                <div class="col-md-6">
                                    <img src="recursos/img/product.png" class="img-responsive" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 " >
                    <div class="panel cotoscard">
                        <div class="panel-body">
                            <div class="row titulos">
                                <div class="col-md-6">
                                    <h2>
                                        <?php echo $count_comprobantes['cantidad'] ?>
                                    </h2>
                                    <h4>Comprobantes Emitidos</h4>
                                </div>
                                <div class="col-md-6">
                                    <img src="recursos/img/target.png" class="img-responsive" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="box cotoscard" >
                        <div class="box-header with-border">
                            <h3 class="box-title">Ventas del mes</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                            class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body chart-responsive">
                            <?php
                            $ranking_ventas_mes = $objcon->consulta_matriz(
                                "SELECT DATE_FORMAT(ven.fecha_cierre,'%Y-%m-%d') AS dia, ROUND(SUM(ven.total), 2) AS total
                                        FROM venta ven
                                        WHERE ven.fecha_cierre > CONCAT(YEAR(CURDATE()),'-', MONTH('{$cierre}'),'-01') AND total IS NOT NULL AND estado_fila = 1
                                        GROUP BY dia
                                        ORDER BY dia ASC");


                            ?>
                            <input type="hidden" id="ventas_mes_data"
                                   value="<?php echo urlencode(json_encode($ranking_ventas_mes)); ?>">

                            <div class="chart" id="ventas-chart" style="height: 300px;"></div>
                        </div>

                        <div style="display: none;">
                            <?php
                            $ranking_descuentos_mes = $objcon->consulta_matriz(
                                "SELECT DATE_FORMAT(ven.fecha_cierre,'%Y-%m-%d') AS dia, ROUND(SUM(vm.monto), 2) AS descuento
                                        FROM venta ven
                                        INNER JOIN venta_medio_pago vm ON vm.id_venta = ven.id
                                        WHERE ven.fecha_cierre > CONCAT(YEAR(CURDATE()),'-', MONTH('{$cierre}'),'-01') AND medio = 'DESCUENTO' AND ven.estado_fila = 1
                                        GROUP BY dia
                                        ORDER BY dia ASC");


                            ?>
                            <input type="hidden" id="descuentos_mes_data"
                                   value="<?php echo urlencode(json_encode($ranking_descuentos_mes)); ?>">

                            <div class="chart" id="ventas-chart" style="height: 300px;"></div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                    <div class="box cotoscard">
                        <div class="box-header with-border">
                            <h3 class="box-title"><span class="text-green">Ventas</span>/<span class="text-red">Compras</span> de la semana</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                            class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body chart-responsive">
                            <?php

                            $descuento_semana = $objcon->consulta_matriz("
                                SELECT DATE_FORMAT(ven.fecha_hora,'%Y-%m-%d') AS dia, ROUND(SUM(vm.monto), 2) AS descuento
                                    FROM venta ven
                                    INNER JOIN venta_medio_pago vm ON vm.id_venta = ven.id
                                    WHERE ven.fecha_cierre > STR_TO_DATE(concat(year(curdate()), week('{$cierre}'), ' Monday'), '%X%V %W') AND medio = 'DESCUENTO' AND ven.estado_fila = 1 
                                    GROUP BY dia ORDER BY dia ASC");
                           
                            $ventas_semana = $objcon->consulta_matriz(
                                "SELECT DATE_FORMAT(ven.fecha_hora,'%Y-%m-%d') AS dia, ROUND(SUM(ven.total), 2) AS total
                                        FROM venta ven
                                        WHERE ven.fecha_cierre > STR_TO_DATE(concat(year(curdate()), week('{$cierre}'), ' Monday'), '%X%V %W') AND total IS NOT NULL AND estado_fila = 1
                                        GROUP BY dia
                                        ORDER BY dia ASC");
                            
                            $compras_semana = $objcon->consulta_matriz(
                                "SELECT DATE_FORMAT(com.fecha,'%Y-%m-%d') AS dia, ROUND(SUM(com.monto_total), 2) AS total 
                                    FROM compra com 
                                    WHERE WEEK('{$cierre}') = WEEK(NOW()) AND year('{$cierre}') = year(NOW())
                                    AND monto_total IS NOT NULL AND estado_fila = 1 
                                    GROUP BY dia
                                    ORDER BY dia ASC");
                            ?>
                            <input type="hidden" id="ventas_semana"
                                   value="<?php echo urlencode(json_encode($ventas_semana)); ?>">

                            <input type="hidden" id="descuento_semana"
                                   value="<?php echo urlencode(json_encode($descuento_semana)); ?>">

                            <input type="hidden" id="compras_semana"
                                   value="<?php echo urlencode(json_encode($compras_semana)); ?>">

                            <div class="chart" id="ventas-vs-compras-chart" style="height: 300px;"></div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <h3 class="">Rankings de la semana
                        <small>(a partir de <?php echo date('d-m-Y', strtotime('Last Monday', time())); ?>)</small>
                    </h3>
                    <?php
                    // echo 
                    //     "SELECT usr.nombres_y_apellidos as nombre,count(ven.id) AS cantidad, ROUND(SUM(ven.total), 2) as monto
                    //         FROM venta ven
                    //         INNER JOIN usuario usr ON ven.id_usuario = usr.id
                    //         WHERE WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL AND ven.estado_fila = 1
                    //         GROUP BY usr.id
                    //         ORDER BY monto DESC";
                    $ranking_vendedor = $objcon->consulta_matriz(
                        "SELECT usr.nombres_y_apellidos as nombre,count(ven.id) AS cantidad, ROUND(SUM(ven.total), 2) as monto
                            FROM venta ven
                            INNER JOIN usuario usr ON ven.id_usuario = usr.id
                            WHERE WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL AND ven.estado_fila = 1
                            GROUP BY usr.id
                            ORDER BY monto DESC");

                    $ranking_cliente = $objcon->consulta_matriz(
                        "SELECT cli.nombre as nombre,count(ven.id) as cantidad,  ROUND(SUM(ven.total), 2) as monto
                        FROM venta ven
                        INNER JOIN cliente cli ON ven.id_cliente = cli.id
                        WHERE WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL AND ven.estado_fila = 1
                        GROUP BY cli.id
                        ORDER BY SUM(ven.total) DESC");

                    $ranking_productos = $objcon->consulta_matriz(
                        "SELECT p.nombre as nombre,  ROUND(SUM(pv.total), 2) as cantidad
                        FROM venta ven
                        inner join producto_venta pv on pv.id_venta = ven.id
                        inner join producto p on pv.id_producto = p.id
                        WHERE WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL
                        GROUP BY p.id
                        ORDER BY cantidad DESC");

                    $ranking_servicios = $objcon->consulta_matriz(
                        "SELECT p.nombre as nombre,  ROUND(SUM(pv.total), 2) as cantidad
                        FROM venta ven
                        INNER JOIN servicio_venta pv ON pv.id_venta = ven.id
                        INNER JOIN servicio p ON pv.id_servicio = p.id
                        WHERE WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL
                        GROUP BY p.id
                        ORDER BY cantidad DESC");

                    $ranking_utilidad = $objcon->consulta_matriz(
                       "SELECT p.nombre as nombre , SUM(cantidad) as cantidad, (SUM(cantidad)*(p.precio_venta - p.precio_compra) ) as utilidad
                        FROM producto_venta pv 
                        inner join producto p on pv.id_producto = p.id 
                        inner join venta ven on pv.id_venta= ven.id
                        WHERE pv.estado_fila=1 AND WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL 
                        GROUP BY pv.id_producto 
                        ORDER BY utilidad
                        DESC limit 10");

                    ?>
                    <div class="nav-tabs-custom cotoscard">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_12" data-toggle="tab" aria-expanded="true">Vendedores</a>
                            </li>
                            <li class=""><a href="#tab_22" data-toggle="tab" aria-expanded="false">Clientes</a></li>
                            <li><a href="#tab_32" data-toggle="tab">Productos</a></li>
                            <li><a href="#tab_42" data-toggle="tab">Servicios</a></li>
                             <li><a href="#tab_52" data-toggle="tab">Utilidad</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th># ventas</th>
                                        <th>Monto vendido</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_vendedor)):
                                        foreach ($ranking_vendedor as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td><?php echo $rank['cantidad']; ?></td>
                                                <td>S./<?php echo $rank['monto']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_22">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th># compras</th>
                                        <th>Monto vendido</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_cliente)):
                                        foreach ($ranking_cliente as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td><?php echo $rank['cantidad']; ?></td>
                                                <td>S./<?php echo $rank['monto']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_32">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Total ventas</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_productos)):
                                        foreach ($ranking_productos as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td>S./<?php echo $rank['cantidad']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="tab_42">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Total ventas</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_servicios)):
                                        foreach ($ranking_servicios as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td>S./<?php echo $rank['cantidad']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->
                            <!--AXALPUSA-->
                            <div class="tab-pane" id="tab_52">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Cantidad</th>
                                        <th>Utilidad Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_utilidad)):
                                        foreach ($ranking_utilidad as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td><?php echo $rank['cantidad']; ?></td>
                                                <td>S./<?php echo $rank['utilidad']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                </div>
                <div class="col-md-6">
                <!-- creditos  147-->
                  <!--   Si hay creditos mostrar cuadro caso contrario ocultar -->
                        <!--<h3 style="display:none" class="">Creditos a Cobrar-->
                
               
                <?php 
                 if(isset($creditos)){
                   
                        if ($creditos["cant"]>0) :?>
                <div class="panel" >

                <h3 class="">
                    Creditos a Cobrar
                </h3>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Deuda</th>
                                    <th>Fecha Limite</th>
                                </tr>
                                </thead>
                                <tbody>

                            <?php if (is_array($clientesCredito)) : ?>
                                <?php foreach ($clientesCredito as $key => $cliente) : ?>
                                    
                                        <?php
                                        $datos_cliente=$objcliente->consulta_arreglo("SELECT * FROM cliente  WHERE  id ={$cliente['id_cliente']}");

                                        $datos_cliente_credito=$objcliente->consulta_arreglo("SELECT * FROM cliente_credito  WHERE  IdCliente ={$cliente['id_cliente']}");
                                       
                                        $por_pagar = 0;
                                        $total_pagado = 0;
                                        $total = number_format($cliente['total'], 2, '.', '');

                                        $misVentasXcredito = $objcon->consulta_matriz(
                                            "SELECT id FROM venta WHERE tipo_comprobante = -1 AND  id_cliente = {$cliente['id_cliente']} AND estado_fila = 1"
                                        );
                                        foreach ($misVentasXcredito as $key => $value) {
                                            $t = $objcon->consulta_arreglo("SELECT SUM(monto) as total FROM `venta_medio_pago` WHERE id_venta = {$value['id']} AND medio <> 'CREDITO'");
                                            $total_pagado += floatval(($t[0]));
                                        }
                                        $total_pagado = number_format($total_pagado, 2, '.', '');
                                        $por_pagar = number_format($total - $total_pagado, 2, '.', '');
                                        ?>
                                    <tr>
                                        <td> <?php echo $datos_cliente['nombre']; ?> </td> 
                                        <td> <?php echo $por_pagar ?> </td>
                                      <!--  <td> <?php echo $datos_cliente['documento'];?> </td>-->
                                        <?php 
                                        if($datos_cliente_credito==0){
                                            echo '<td style="color:purple;"><b> --- </b></td>';
                                        }else{
                                            $hoy = new DateTime(date("Y-m-d"));
                                            $fecha_pago = new DateTime($datos_cliente_credito["FechaLimite"]);
                                            $diff = date_diff($hoy, $fecha_pago);
                                            $d = $diff->format('%R%a');
                                            
                                            //echo $d;
                                            if ($d==0) {
                                                echo '<td style="color: blue;"><b>Hoy</b></td>';
                                            }elseif($d <3){
                                                echo '<td style="color: red;"><b>'. $diff->format('%R%a dias') .'</b></td>';
                                            }elseif($d <7 & $d >=3 ){
                                                echo '<td style="color: orange;"><b>'. $diff->format('%R%a dias') .'</b></td>';
                                            }elseif($d >=7 ){
                                                echo '<td style="color: green;"><b>'. $diff->format('%R%a dias') .'</b></td>';
                                            }
                                            


                                        }
                                       ?> </td>
                                          
                                                
                                    </tr>
                                    
                                   
                                 <?php endforeach; ?>
                            <?php endif; ?>
                                </tbody>
                            </table>                            
                        </div>
                    </div>
                 <?php endif; }?>
                    
                    <?php 
                    $hoy2 = date("Y-m-d");
                    if ($cierre!= $hoy2) :?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> ADVERTENCIA!</h4>
                            La fecha actual es diferente a la fecha de cierre, se recomienda aperturar la caja. <a href="montoinicial.php"> Click para aperturar</a>
                        </div>
                    <?php endif; ?>


                    <?php if ($vencidos["cant"]>0) :?>
                       <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-ban"></i> ADVERTENCIA!</h4>
                            Hay Productos que estan su fecha de vencimiento se acerca <a href="productos_vencidos.php">Ver Lista de Productos Vencidos</a>
                        </div>
                    <?php endif; ?>



                 
                        
                   

                    <!--   Si hay creditos mostrar cuadro caso contrario ocultar -->
                        <!--<h3 style="display:none" class="">Creditos a Cobrar-->
                 <!--   <h3 class="">Creditos a Cobrar
                        <small>(<?php echo "Rango de 5 dias";?>)</small>
                    </h3>


                    <!--<div class="panel" style="display:none">-->
                    <!-- <div class="panel" >
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Total a Cobrar</th>
                                    <th>Fecha Limite</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (is_array($cobros)):
                                    foreach ($cobros as $co): 

                                        $fecha_pago = new DateTime($co["FechaLimite"]);
                                        $diff = date_diff($hoy, $fecha_pago);
                                        $d = $diff->format('%R%a');
                                        // echo $d;
                                        if($d<=5):?>

                                        <tr>
                                            <td><?php echo $co['nombre']; ?></td>
                                            <td><?php echo $co['total']; ?></td>
                                            <td><?php echo $d; ?></td>
                                            <!--<?php if($d == 0): ?>
                                                <td style="color: green;"><b>Hoy</b></td>
                                            <?php elseif($d == 4 || $d == 5): ?>
                                                <td style="color: blue;"><b><?php echo $diff->format('%R%a dias') ?></b></td>
                                            <?php else: ?>
                                                <td style="color: red;"><b><?php echo $diff->format('%R%a dias') ?></b></td>
                                            <?php endif; ?>-->
                                                
                                       <!-- </tr>
                                    <?php endif; endforeach; else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No hay datos registrados</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>                            
                        </div>
                    </div>-->

                    <div class="panel cotoscard" >
                        <div class="panel-body titulos">
                            
                            <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                        </div>
                         <div class="panel-body titulos">
                            
                            <div id="chartContainer2" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                                    
                    <h3 class="">Rankings de Hoy
                        <small>(<?php echo date('d-m-Y'); ?>)</small>
                    </h3>
                    <?php
                    // echo "SELECT usr.id, usr.nombres_y_apellidos as nombre, count(ven.id) AS cantidad, ROUND(SUM(ven.total), 2) as monto
                    //         FROM venta ven
                    //         INNER JOIN usuario usr ON ven.id_usuario = usr.id
                    //         WHERE ven.fecha_cierre = '{$cierre}' AND ven.estado_fila = 1 AND ven.total IS NOT NULL
                    //         GROUP BY usr.id
                    //         ORDER BY SUM(ven.total) DESC";
                    $ranking_vendedor = $objcon->consulta_matriz(
                        "SELECT usr.id, usr.nombres_y_apellidos as nombre, count(ven.id) AS cantidad, ROUND(SUM(ven.total), 2) as monto
                            FROM venta ven
                            INNER JOIN usuario usr ON ven.id_usuario = usr.id
                            WHERE ven.fecha_cierre = '{$cierre}' AND ven.estado_fila = 1 AND ven.total IS NOT NULL
                            GROUP BY usr.id
                            ORDER BY SUM(ven.total) DESC");

                    $ranking_vendedor_desc = $objcon->consulta_matriz(
                        "SELECT usr.id, usr.nombres_y_apellidos as nombre, count(ven.id) AS cantidad, ROUND(SUM(vmp.monto), 2) as monto 
                            FROM venta ven 
                            INNER JOIN venta_medio_pago vmp ON ven.id = vmp.id_venta
                            INNER JOIN usuario usr ON ven.id_usuario = usr.id 
                            WHERE ven.fecha_cierre = '{$cierre}' AND vmp.medio = 'DESCUENTO'
                            AND ven.estado_fila = 1 AND ven.total IS NOT NULL GROUP BY usr.id ORDER BY SUM(vmp.monto) DESC");

                    $ranking_cliente = $objcon->consulta_matriz(
                        "SELECT cli.nombre as nombre,count(ven.id) as cantidad, ROUND(SUM(ven.total), 2) as monto
                            FROM venta ven
                            INNER JOIN cliente cli ON ven.id_cliente = cli.id
                            WHERE ven.fecha_cierre = '{$cierre}' AND ven.total IS NOT NULL AND ven.estado_fila = 1
                            GROUP BY cli.id
                            ORDER BY SUM(ven.total) DESC");

                    $ranking_productos = $objcon->consulta_matriz(
                        "SELECT p.nombre as nombre, ROUND(SUM(pv.total), 2) as cantidad
                            FROM venta ven
                            inner join producto_venta pv on pv.id_venta = ven.id
                            inner join producto p on pv.id_producto = p.id
                            WHERE ven.fecha_cierre = '{$cierre}' AND ven.total IS NOT NULL 
                            GROUP BY p.id
                            ORDER BY cantidad DESC");

                    $ranking_utilidad = $objcon->consulta_matriz(
                       "SELECT p.nombre as nombre , SUM(cantidad) as cantidad, (SUM(cantidad)*(p.precio_venta - p.precio_compra) ) as utilidad
                        FROM producto_venta pv 
                        inner join producto p on pv.id_producto = p.id 
                        inner join venta ven on pv.id_venta= ven.id
                        WHERE pv.estado_fila=1 AND ven.fecha_cierre = '{$cierre}' AND ven.total IS NOT NULL 
                        GROUP BY pv.id_producto 
                        ORDER BY utilidad
                        DESC limit 10");
                       
                       
                       
                       
                  /*   pusa  "SELECT p.nombre as nombre, ROUND(SUM(pv.total), 2) as cantidad
                            FROM venta ven
                            inner join producto_venta pv on pv.id_venta = ven.id
                            inner join producto p on pv.id_producto = p.id
                            WHERE ven.fecha_cierre = '{$cierre}' AND ven.total IS NOT NULL 
                            GROUP BY p.id
                            ORDER BY cantidad DESC");*/

                    $ranking_servicios = $objcon->consulta_matriz(
                        "SELECT p.nombre as nombre, ROUND(SUM(pv.total), 2) as cantidad
                            FROM venta ven
                            inner join servicio_venta pv on pv.id_venta = ven.id
                            inner join servicio p on pv.id_servicio = p.id
                            WHERE ven.fecha_cierre = '{$cierre}' AND ven.total IS NOT NULL
                            GROUP BY p.id
                            ORDER BY cantidad DESC");

                    ?>
                    <div class="nav-tabs-custom cotoscard">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Vendedores</a>
                            </li>
                            <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Clientes</a></li>
                            <li><a href="#tab_3" data-toggle="tab">Productos</a></li>
                            <li><a href="#tab_4" data-toggle="tab">Servicios</a></li>
                            <li><a href="#tab_5" data-toggle="tab">Utilidad</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th># ventas</th>
                                        <th>Monto vendido</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_vendedor)):
                                        foreach ($ranking_vendedor as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td><?php echo $rank['cantidad']; ?></td>
                                                <?php 
                                                    $total = $rank['monto'];
                                                    if(is_array($ranking_vendedor_desc)):
                                                        foreach($ranking_vendedor_desc as $a):
                                                            if($rank["id"] == $a["id"]):
                                                                $total = $total - $a["monto"];
                                                            endif;
                                                        endforeach;
                                                    endif; 
                                                ?>
                                                <td>S./<?php echo $total ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_2">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th># compras</th>
                                        <th>Monto vendido</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_cliente)):
                                        foreach ($ranking_cliente as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td><?php echo $rank['cantidad']; ?></td>
                                                <td>S./<?php echo $rank['monto']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_3">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Total ventas</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_productos)):
                                        foreach ($ranking_productos as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td>S./<?php echo $rank['cantidad']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="tab_4">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Total ventas</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_servicios)):
                                        foreach ($ranking_servicios as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td>S./<?php echo $rank['cantidad']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!--AXALPUSA-->
                            <div class="tab-pane" id="tab_5">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Cantidad</th>
                                        <th>Utilidad Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (is_array($ranking_utilidad)):
                                        foreach ($ranking_utilidad as $rank): ?>
                                            <tr>
                                                <td><?php echo $rank['nombre']; ?></td>
                                                <td><?php echo $rank['cantidad']; ?></td>
                                                <td>S./<?php echo $rank['utilidad']; ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay datos registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    
                </div>
            </div>
        </section>
    </div><!-- /.content-wrapper -->


</div><!-- ./wrapper -->


<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.4 -->
<script src="recursos/adminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script src="recursos/js/plugins/datatables/jquery-datatables.js"></script>
<script src="recursos/js/plugins/datatables/dataTables.tableTools.js"></script>


<!-- Bootstrap 3.3.5 -->
<script src="recursos/adminLTE/bootstrap/js/bootstrap.min.js"></script>
<script src="recursos/adminLTE/plugins/jQueryUI/jquery-ui.js"></script>
<script src="recursos/adminLTE/plugins/raphael.min.js"></script>
<script src="recursos/adminLTE/plugins/morris.min.js"></script>
<script src="recursos/adminLTE/dist/js/app.js"></script>

<script src="recursos/js/moments.js"></script>
<script src="recursos/js/bootstrap-datetimepicker.min.js"></script>
<script src="recursos/js/plugins/canvasjs/canvasjs.min.js"></script>
<script>
    $(document).ready(function () {

        $.ajax({
            type: 'POST',
            url: 'ws/cliente.php',
            dataType: "json",
            data: { op: 'EnvioCorreo' },
            success:function(response) {
                //console.log(response.length);

                var rsp = response;

                if(response.length > 0){

                    // $.ajax({
                    //     type: 'POST',
                    //     url: 'http://usqay-cloud.com/app/data/enviopramart.php',
                    //     data: { data: response},
                    //     success:function(response) {
                    //         // console.log("res", rsp);
                    //         $.post('ws/cliente.php', {
                    //             op: "changeStatusSend",
                    //             data: rsp,
                    //         }, function (data) {
                    //             console.log(data);
                    //         }, 'json');
                    //     },
                    //     error: function (err) {
                    //         alert(err);
                    //     }
                    // });
                }else{
                    console.log("No hay Data para enviar");
                }

                

            }
        });

        /* $.ajax({
            type: 'POST',
            url: 'ws/correo.php',
            dataType: "json",
            data: { op: 'EnvioCorreo' },
            success:function(response) {
                console.log(response)
                console.log(response.length);

                var rsp = response;

                if(response.length > 0){
                }else{
                    console.log("No hay Data para enviar");
                }            
            },
            error:function(error) {
                console.log(error)
            }
        }); */

        $("#mnu-configuracion").click(function () {
            $("#myModalLabel").html("Configuración");
            $("#menu-modal").html($("#cnt-configuracion").html());
            $("#menu-modal").css("height", "200px");
            $("#modal_contenido").modal("show");
        });

        $("#mnu-almacen").click(function () {
            $("#myModalLabel").html("Almacen");
            $("#menu-modal").css("height", "120px");
            $("#menu-modal").html($("#cnt-almacen").html());
            $("#modal_contenido").modal("show");
        });

        $("#mnu-caja").click(function () {
            $("#myModalLabel").html("Caja");
            $("#menu-modal").css("height", "120px");
            $("#menu-modal").html($("#cnt-caja").html());
            $("#modal_contenido").modal("show");
        });

        $("#mnu-reportes").click(function () {
            $("#myModalLabel").html("Reportes");
            $("#menu-modal").css("height", "200px");
            $("#menu-modal").html($("#cnt-reportes").html());
            $("#modal_contenido").modal("show");
        });

        var ventas_data = JSON.parse(decodeURIComponent($("#ventas_mes_data").val()));
        var descuentos_data = JSON.parse(decodeURIComponent($("#descuentos_mes_data").val()));
        var ventas_semana = JSON.parse(decodeURIComponent($("#ventas_semana").val()));
        var descuento_semana = JSON.parse(decodeURIComponent($("#descuento_semana").val()));
        var compras_semana = JSON.parse(decodeURIComponent($("#compras_semana").val()));

        var data = [];
        var data2 = [];
        var data3 = [];
        let flag = false;
        var data_v_sem = [];
        var data_d_sem = [];
        var data_c_sem = [];

        if(Array.isArray(ventas_data)){
            ventas_data.forEach(function (t) {
                data.push({dia: t.dia, monto: parseFloat(t.total )});
            });

            // console.log(data);
        }

        if(Array.isArray(descuentos_data)){
            descuentos_data.forEach(function (t) {
                data2.push({dia: t.dia, monto: parseFloat(t.descuento)});
            });
            // console.log(data2);
        }
        var encuentra = false;
        for(let i=0; i<data.length; i++){
            encuentra = false;
            for(let j=0; j<data2.length; j++){
                if(data[i].dia == data2[j].dia){
                    data3.push({dia: data[i].dia, monto: parseFloat(data[i].monto - data2[j].monto)})
                    encuentra = true;
                    break;
                }
            }
            if(!encuentra){
                data3.push({dia: data[i].dia, monto: parseFloat(data[i].monto)});
                // break;
            }
        }

        // if(encuentra){
        //     alert("si son iguales");
        // }

        // console.log(data3);

        if(Array.isArray(ventas_semana)){
            ventas_semana.forEach(function (t) {
                data_v_sem.push({dia: t.dia, monto: parseFloat(t.total)});
            });
            console.log(data_v_sem)
        }

        if(Array.isArray(descuento_semana)){
            descuento_semana.forEach(function (t) {
                data_d_sem.push({dia: t.dia, monto: parseFloat(t.descuento)});
            });

            console.log(data_d_sem)

        }

        var encuentra = false;
        for(let i=0; i<data_v_sem.length; i++){
            encuentra = false;
            for(let j=0; j<data_d_sem.length; j++){
                if(data_v_sem[i].dia == data_d_sem[j].dia){
                    data_c_sem.push({dia: data_v_sem[i].dia, monto: parseFloat(data_v_sem[i].monto - data_d_sem[j].monto)})
                    encuentra = true;
                    break;
                }
            }
            if(!encuentra){
                data_c_sem.push({dia: data_v_sem[i].dia, monto: parseFloat(data_v_sem[i].monto)});
                // break;
            }
        }

        if (Array.isArray(compras_semana)) {
            data_c_sem.forEach(function (t, index1) {
                compras_semana.forEach(function (t2) {
                    if (t.dia == t2.dia) {
                        data_c_sem[index1].monto2 = parseFloat(t2.total);
                    } else {
                        data_c_sem[index1].monto2 = parseFloat(t2.total);
                    }
                });

            });
        }

        new Morris.Line({
            element: 'ventas-chart',
            data: data3,
            xkey: 'dia',
            ykeys: ['monto'],
            labels: ['Monto Vendido S./'],
            hideHover : 'auto'
        });

        new Morris.Area({
            element: 'ventas-vs-compras-chart',
            data: data_c_sem,
            xkey: 'dia',
            ykeys: ['monto', 'monto2'],
            labels: ['Monto Vendido S./', 'Monto Comprado S./'],
            hideHover: 'auto',
            lineColors: ['#148122', '#f00']
        });

    var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	title:{
		text: "Top 5 productos más vendidos"
	},
	subtitles: [{
		text: ""
	}],
	data: [{
		type: "pie",
		showInLegend: "true",
		legendText: "{label}",
		indexLabelFontSize: 16,
		indexLabel: "{label}",
		yValueFormatString: "#,##0",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
//axalpusa
chart.render();
    var chart = new CanvasJS.Chart("chartContainer2", {
	animationEnabled: true,
	exportEnabled: true,
	title:{
		text: "Top 5 productos mayor utilidad"
	},
	subtitles: [{
		text: ""
	}],
	data: [{
		type: "pie",
		showInLegend: "true",
		legendText: "{label}",
		indexLabelFontSize: 16,
		indexLabel: "{label}",
		yValueFormatString: "#,##0",
		dataPoints: <?php echo json_encode($dataPoints_utilidad, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
//axalpusa
        
    });
</script>
<!--Inicio Modal-->
<div class='modal fade' id='modal_contenido' tabindex='-1' role='dialog'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>...</h4>
            </div>
            <div class='modal-body' style="width:100%;height:100%;">
                <ul id="menu-modal" class="menu-inicio-m" style="width:100%;">

                </ul>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal-->
</body>
</html>
