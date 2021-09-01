<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Ventas Descartadas';
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

    // echo "SELECT * FROM venta where fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = -1 ORDER BY id DESC";

    $objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND subtotal is NULL AND estado_fila = 2 AND id_caja = '{$_COOKIE["id_caja"]}' ORDER BY id DESC");


    $conn->consulta_simple("SET sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';");

    $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila = 1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante = -1 AND id_caja = '{$_COOKIE["id_caja"]}'");
    $totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila = 1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante = -1 AND id_caja = '{$_COOKIE["id_caja"]}'");
    $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila = 1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante = -1 AND id_caja = '{$_COOKIE["id_caja"]}'");

    $clientesCredito = $conn->consulta_matriz("SELECT * FROM venta WHERE tipo_comprobante = -1 GROUP by id_cliente");
    // if (isset($_GET['opcion'])){
    //     $tipo = $_GET['opcion'];

    //     $objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = ".$tipo." ORDER BY id DESC");

    //     $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = ".$tipo);
    //     $totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = ".$tipo);
    //     $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = ".$tipo);
    // }else
    //   $tipo = '';




    //die();
    // $sucursal = UserLogin::get_pkSucursal();
    ?>
    
    <div class="container-fluid">
        <div class="panel">
            <div class="panel-body">
                <div class="row">

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
                <input type="date" id="txtfechafin" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>" />
                <input type=hidden id="opc" class='form-control' placeholder='AAAA-MM-DD' name='opc' value="-1" />
            </div>

            <div class='col-md-4' style="margin-top:27px;">
                <button type="button" onclick="buscar()" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-search"></span> Buscar</button>
            </div>
        </div>
                   
                    <!-- <div class="col-md-4">
                        <div class="panel bordeado">
                            <div class="panel-body text-center total">
                                <h3>S./ <?php echo number_format((floatval($totalvendido['total'])), 2); ?></h3>
                                <h4>SUB TOTAL: <b>S./ <?php echo number_format((floatval($subtotal['subtotal'])), 2); ?></b></h4>
                                <h4>TOTAL IMPUESTOS: <b>S./ <?php echo number_format((floatval($totalimpuestos['impuestos'])), 2); ?></b></h4>
                            </div>
                        </div>
                    </div> -->
                </div>

                <div class="contenedor-tabla col-md-12" style="margin-top: 30px">
                    <table id="tblKardex" title="Total de Ventas" class="display cell-border">
                        <thead>
                            <tr>
                                <th>
                                    <center>Venta</center>
                                </th>
                                <!-- <th><center>Cliente</center></th> -->
                                <th>
                                    <center>Usuario</center>
                                </th>
                                <th>
                                    <center>Turno</center>
                                </th>
                                <!-- <th>Dscto.</th> -->
                                <!-- <th><center>SubTotal</center></th> -->
                                <!-- <th><center>Total Impuestos</center></th> -->
                                <!-- <th><center>Total</center></th> -->
                                <!-- <th><center>Por Pagar</center></th> -->
                                <th>
                                    <center>Caja</center>
                                </th>
                                <!-- <th><center>Tipo de Comprobante</center></th> -->
                                <th>
                                    <center>Estado</center>
                                </th>
                                <th>
                                    <center>Fecha y hora de Pedido</center>
                                </th>
                                <th>
                                    <center>Fecha Cierre</center>
                                </th>

                                <!-- <th><center>Opciones</center></th> -->
                                <!-- <th><center>Retomar</center></th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_desc = 0;
                            if (is_array($objs)) {
                                foreach ($objs as $o) {
                                    $dscto = 0;
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $o['id']; ?></td>
                                        <!-- <td style="text-align: center;"><?php
                                                                                        $objcliente = new cliente();
                                                                                        $objcliente->setVar('id', $o['id_cliente']);
                                                                                        $objcliente->getDB();
                                                                                        echo $objcliente->getNombre();
                                                                                        ?>
                                        </td> -->
                                        <td style="text-align: center;"><?php
                                                                                $objusuario = new usuario();
                                                                                $objusuario->setVar('id', $o['id_usuario']);
                                                                                $objusuario->getDB();
                                                                                echo $objusuario->getNombresYApellidos();
                                                                                ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php
                                                    $objoturno = new turno();
                                                    $objoturno->setVar('id', $o['id_turno']);
                                                    $objoturno->getDB();
                                                    echo $objoturno->getNombre();
                                                    ?>
                                        </td>
                                        <!-- <td style="text-align: center;"><?php
                                                                                        $descto = $conn->consulta_arreglo(
                                                                                            "SELECT ROUND(SUM(monto),2) AS dscto
                                                            FROM venta_medio_pago
                                                            WHERE id_venta = {$o['id']} AND medio = 'DESCUENTO'
                                                            GROUP BY medio"
                                                                                        );

                                                                                        if (is_array($descto)) {
                                                                                            $dscto = $descto['dscto'];
                                                                                            $total_desc += $dscto;
                                                                                        }
                                                                                        echo $dscto;
                                                                                        ?></td> -->
                                        <!-- <td style="text-align: center;">
                                            <?php echo number_format(floatval($o['subtotal']), 2); ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo  number_format(floatval($o['total_impuestos']), 2); ?></td>
                                        <td style="text-align: center;"><?php echo  number_format(floatval($o['total'] - $dscto), 2); ?></td>

                                        <td style="text-align: center;">

                                            <?php
                                                    $total_pagado = 0;
                                                    $eft = $conn->consulta_arreglo(
                                                        "SELECT ROUND(SUM(monto),2) AS pagado FROM `venta_medio_pago` WHERE id_venta = {$o['id']} AND MEDIO <> 'CREDITO'"
                                                    );

                                                    if (is_array($eft)) {
                                                        $pagado = $eft['pagado'];
                                                        $total_pagado += $pagado;
                                                    }

                                                    echo  number_format(floatval($o['total'] - $total_pagado), 2);
                                                    ?>
                                        </td> -->
                                        <td style="text-align: center;"><?php
                                                                                $objocaja = new caja();
                                                                                $objocaja->setVar('id', $o['id_caja']);
                                                                                $objocaja->getDB();
                                                                                echo $objocaja->getNombre();
                                                                                ?>
                                        </td>
                                        <!--  <td style="text-align: center;">
                                        <?php
                                                echo "Credito";
                                                ?>
                                        </td> -->

                                        <td style="text-align: center;"><?php
                                                                                if ($o['estado_fila'] == 1) {
                                                                                    echo "<span class='label label-success'>Emitida</span>";
                                                                                } else {
                                                                                    echo "<span class='label label-danger'>Anulada</span>";
                                                                                }
                                                                                ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo ($o['fecha_hora']); ?></td>
                                        <td style="text-align: center;"><?php echo $o['fecha_cierre']; ?></td>


                                        <!-- <td><a class='$grilla' onclick='anularCredito(<?php echo $o['id'] ?>)'><span class='glyphicon glyphicon-remove-circle'></span></a></td>
                                            <td>
                                                <a href='pantalla_teclado.php?id=<?php echo $o["id"]; ?>'><i class="fa fa-reply-all" aria-hidden="true"></i> Retomar</a>
                                            </td> -->
                                        <!-- <?php
                                                        if ($o['estado_fila'] == 1) {
                                                            ?>
                                                <button type="button" class="btn-link text-red" onclick="anula_venta(<?php echo $o['id']; ?>)">Anular</button><br/>
                                            <?php
                                                    }

                                                    if ($o['tipo_comprobante'] != 0) {
                                                        ?>
                                                <button type="button" class="btn-link text-green" onclick="subir_sunat(<?php echo $o['id']; ?>)">Cargar Sunat</button><br/>
                                            <?php
                                                    }
                                                    ?>
                                            <a href='detalles_venta_totales.php?id=<?php echo $o['id']; ?>' class="btn-link text-orange">Medio de Pago</a><br/>
                                            <a href='detalle_venta_productos.php?id=<?php echo $o['id']; ?>' class="btn-link text-blue">Detalles</a><br/>
                                            <button type="button" class="btn-link text-green" onclick="reimprimir(<?php echo $o['id']; ?>,'<?php
                                                                                                                                                    if ($o['tipo_comprobante'] == 1) {
                                                                                                                                                        echo "BOL";
                                                                                                                                                    } elseif ($o['tipo_comprobante'] == 2) {
                                                                                                                                                        echo "FAC";
                                                                                                                                                    } elseif ($o['tipo_comprobante'] == 0) {
                                                                                                                                                        echo "NOT";
                                                                                                                                                    }
                                                                                                                                                    ?>',<?php echo $_COOKIE["id_caja"]; ?>)">Reimprimir</button>
                                        </td> -->
                                    </tr>

                            <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>

                            <!--  <tr>
                                <td colspan="4" style="text-align: center;"><b>TOTALES<b></td>

                                <?php if (is_array($objs)) : ?>
                                    <td style="text-align: center;"><b><?php echo number_format($total_desc, 2, '.', ' '); ?></b></td>
                                    <td style="text-align: center;"><b><?php echo number_format($subtotal['subtotal'], 2, '.', ' '); ?></b></td>
                                    <td style="text-align: center;"><b><?php echo number_format($totalimpuestos['impuestos'], 2, '.', ' '); ?></b></td>
                                    <td style="text-align: center;"><b><?php echo number_format($totalvendido['total'] - $total_desc, 2, '.', ' '); ?></b></td>
                                <?php endif ?>
                            </tr> -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


        <?php
        // $nombre_tabla = 'reporte_ventas_totales_old';
        $nombre_tabla = 'ventas_descartadas';
        require_once('recursos/componentes/footer.php');
        ?>