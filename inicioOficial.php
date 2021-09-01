<?php
session_start();
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

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
require_once 'nucleo/include/MasterConexion.php';
$objcon = new MasterConexion();

$cierre = $objcon->consulta_arreglo("SELECT fecha_cierre FROM configuracion LIMIT 1")['fecha_cierre'];



$total_venta = $objcon->consulta_arreglo("SELECT ROUND(SUM(total),2) AS total
                FROM venta
                WHERE estado_fila = 1 AND id_caja = '{$_COOKIE["id_caja"]}' AND
                fecha_cierre = '{$cierre}'");

$tipo_cambio = $objcon->consulta_arreglo("SELECT * FROM tipo_cambio");
$hora_punta = $objcon->consulta_arreglo(
    "select count(*) as cantidad, HOUR(fecha_hora) as punta
            from venta
            group by punta
            order by cantidad desc
            limit 1");
?>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="index.php" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img src='recursos/img/logo-mini2.png' width="80%"></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img src='recursos/img/logo-mini1.png' height="45px"></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
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
    <div class="content-wrapper">
        <section class="content">
            <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?=$_SESSION['mensaje']['tipo']?>">
                <?= htmlspecialchars_decode($_SESSION['mensaje']['texto']) ?>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <a href="reporte_ventas_totales.php" class="small-box bg-blue">
                        <div class="inner">
                            <h3>
                                S./<?php echo empty($total_venta["total"]) ? '0.0' : $total_venta["total"]; ?><br>
                                $<?php echo round($total_venta["total"] / $tipo_cambio['venta'], 2); ?>
                            </h3>
                        </div>
                        <div class="icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h2>
                                <?php
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

                            <p>HORA PUNTA</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
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
                                        WHERE ven.fecha_cierre > CONCAT(YEAR(CURDATE()),'-', MONTH('{$cierre}'),'-01') AND total IS NOT NULL
                                        GROUP BY dia
                                        ORDER BY dia ASC");
                            ?>
                            <input type="hidden" id="ventas_mes_data"
                                   value="<?php echo urlencode(json_encode($ranking_ventas_mes)); ?>">

                            <div class="chart" id="ventas-chart" style="height: 300px;"></div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                    <div class="box box-primary">
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

                            $ventas_semana = $objcon->consulta_matriz(
                                "SELECT DATE_FORMAT(ven.fecha_hora,'%Y-%m-%d') AS dia, ROUND(SUM(ven.total), 2) AS total
                                        FROM venta ven
                                        WHERE ven.fecha_cierre > STR_TO_DATE(concat(year(curdate()), week('{$cierre}'), ' Monday'), '%X%V %W') AND total IS NOT NULL
                                        GROUP BY dia
                                        ORDER BY dia ASC");

                            $compras_semana = $objcon->consulta_matriz(
                                "SELECT DATE_FORMAT(com.fecha,'%Y-%m-%d') AS dia, ROUND(SUM(com.monto_total),2) AS total
                                        FROM compra com
                                        WHERE com.fecha > STR_TO_DATE(concat(year(curdate()), week('{$cierre}'), ' Monday'), '%X%V %W') AND monto_total IS NOT NULL
                                        GROUP BY dia
                                        ORDER BY dia ASC");
                            ?>
                            <input type="hidden" id="ventas_semana"
                                   value="<?php echo urlencode(json_encode($ventas_semana)); ?>">

                            <input type="hidden" id="compras_semana"
                                   value="<?php echo urlencode(json_encode($compras_semana)); ?>">

                            <div class="chart" id="ventas-vs-compras-chart" style="height: 300px;"></div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="">Rankings de Hoy
                        <small>(<?php echo date('d-m-Y'); ?>)</small>
                    </h3>
                    <?php

                    $ranking_vendedor = $objcon->consulta_matriz(
                        "SELECT usr.nombres_y_apellidos as nombre,count(ven.id) AS cantidad, ROUND(SUM(ven.total), 2) as monto
            FROM venta ven
            INNER JOIN usuario usr ON ven.id_usuario = usr.id
            WHERE ven.fecha_cierre > '{$cierre}' AND ven.total IS NOT NULL
            GROUP BY usr.id
            ORDER BY SUM(ven.total) DESC");

                    $ranking_cliente = $objcon->consulta_matriz(
                        "SELECT cli.nombre as nombre,count(ven.id) as cantidad, ROUND(SUM(ven.total), 2) as monto
            FROM venta ven
            INNER JOIN cliente cli ON ven.id_cliente = cli.id
            WHERE ven.fecha_cierre > '{$cierre}' AND ven.total IS NOT NULL
            GROUP BY cli.id
            ORDER BY SUM(ven.total) DESC");

                    $ranking_productos = $objcon->consulta_matriz(
                        "SELECT p.nombre as nombre, ROUND(SUM(pv.total), 2) as cantidad
            FROM venta ven
            inner join producto_venta pv on pv.id_venta = ven.id
            inner join producto p on pv.id_producto = p.id
            WHERE ven.fecha_cierre > '{$cierre}' AND ven.total IS NOT NULL
            GROUP BY p.id
            ORDER BY cantidad DESC");

                    $ranking_servicios = $objcon->consulta_matriz(
                        "SELECT p.nombre as nombre, ROUND(SUM(pv.total), 2) as cantidad
            FROM venta ven
            inner join servicio_venta pv on pv.id_venta = ven.id
            inner join servicio p on pv.id_servicio = p.id
            WHERE ven.fecha_cierre > '{$cierre}' AND ven.total IS NOT NULL
            GROUP BY p.id
            ORDER BY cantidad DESC");

                    ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Vendedores</a>
                            </li>
                            <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Clientes</a></li>
                            <li><a href="#tab_3" data-toggle="tab">Productos</a></li>
                            <li><a href="#tab_4" data-toggle="tab">Servicios</a></li>
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
                        </div>
                    </div>

                    <h3 class="">Rankings de la semana
                        <small>(a partir de <?php echo date('d-m-Y', strtotime('Last Monday', time())); ?>)</small>
                    </h3>
                    <?php

                    $ranking_vendedor = $objcon->consulta_matriz(
                        "SELECT usr.nombres_y_apellidos as nombre,count(ven.id) AS cantidad, ROUND(SUM(ven.total), 2) as monto
                        FROM venta ven
                        INNER JOIN usuario usr ON ven.id_usuario = usr.id
                        WHERE WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL
                        GROUP BY usr.id
                        ORDER BY monto DESC");

                    $ranking_cliente = $objcon->consulta_matriz(
                        "SELECT cli.nombre as nombre,count(ven.id) as cantidad,  ROUND(SUM(ven.total), 2) as monto
                        FROM venta ven
                        INNER JOIN cliente cli ON ven.id_cliente = cli.id
                        WHERE WEEK(ven.fecha_cierre) = WEEK('{$cierre}') AND ven.total IS NOT NULL
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

                    ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_12" data-toggle="tab" aria-expanded="true">Vendedores</a>
                            </li>
                            <li class=""><a href="#tab_22" data-toggle="tab" aria-expanded="false">Clientes</a></li>
                            <li><a href="#tab_32" data-toggle="tab">Productos</a></li>
                            <li><a href="#tab_42" data-toggle="tab">Servicios</a></li>
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
                        </div>
                        <!-- /.tab-content -->
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

<script>
    $(document).ready(function () {
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
        var ventas_semana = JSON.parse(decodeURIComponent($("#ventas_semana").val()));
        var compras_semana = JSON.parse(decodeURIComponent($("#compras_semana").val()));

        var data = [];
        var data_v_sem = [];

        if(Array.isArray(ventas_data)){
            ventas_data.forEach(function (t) {
                data.push({dia: t.dia, monto: parseFloat(t.total)});
            });
        }

        if(Array.isArray(ventas_semana)){
            ventas_semana.forEach(function (t) {
                data_v_sem.push({dia: t.dia, monto: parseFloat(t.total)});
            });
        }

        if (Array.isArray(compras_semana)) {
            data_v_sem.forEach(function (t, index1) {
                compras_semana.forEach(function (t2) {
                    if (t.dia == t2.dia) {
                        data_v_sem[index1].monto2 = parseFloat(t2.total);
                    } else {
                        data_v_sem[index1].monto2 = 0;
                    }
                });

            });
        }

        new Morris.Line({
            element: 'ventas-chart',
            data: data,
            xkey: 'dia',
            ykeys: ['monto'],
            labels: ['Monto Vendido S./'],
            hideHover : 'auto'
        });

        new Morris.Area({
            element: 'ventas-vs-compras-chart',
            data: data_v_sem,
            xkey: 'dia',
            ykeys: ['monto', 'monto2'],
            labels: ['Monto Vendido S./', 'Monto Comprado S./'],
            hideHover: 'auto',
            lineColors: ['#148122', '#f00']
        });
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
