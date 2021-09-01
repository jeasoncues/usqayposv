<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte de Cuentas por Cobrar / Cuentas por Pagar';
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
include_once('nucleo/compra.php');
include_once('nucleo/usuario.php');
include_once('nucleo/proveedor.php');

$conn = new MasterConexion();
$obj = new venta();
//$objCompra = new compra();

require_once('recursos/componentes/header.php');
?>
<style>
    .tabla {
        margin-top: 20px;
    }

    .table-bordered>thead>tr>th,
    .table-bordered>thead>tr>td,
    .table-bordered>tbody>tr>th,
    .table-bordered>tbody>tr>td,
    .table-bordered>tfoot>tr>th,
    .table-bordered>tfoot>tr>td {
        text-align: center !important;
    }

    .top-20 {
        margin-top: 20px;
    }

    .top-25 {
        margin-top: 25px;
    }

    .bordeado {
        border: 1px solid #000;
    }

    .fechas {
        padding: 10px;
    }

    .total h3 {
        font-size: 5rem;
    }
</style>

<body>

    <?php

    /**
     * Compras
     */

    $objsCompras = $conn->consulta_matriz(
        "SELECT p.id,p.razon_social,p.ruc,
        (SELECT SUM(monto_pendiente) FROM compra WHERE id_proveedor = p.id) AS 'Deuda' 
        FROM proveedor p"
    );

    // -----------------------------------------

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
    $totalvendido =  number_format(0.000, 3, '.', '');
    $totalimpuestos =  number_format(0.000, 3, '.', '');

    // echo "SELECT * FROM venta where fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = -1 ORDER BY id DESC";

    $objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante = -1 AND estado_fila = 1 ORDER BY id DESC");


    $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila = 1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante = -1");
    $totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila = 1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante = -1");
    $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila = 1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante = -1");

    $clientesCredito = $conn->consulta_matriz("SELECT id_cliente, SUM(total) as total FROM venta WHERE tipo_comprobante = -1 AND estado_fila = 1  GROUP by id_cliente");

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

    <!--    <div class="container">-->

    <!--        <br /><br /><br />-->
    <!--        <h3>Kardex Resumen</h3>-->
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                    <h3 class="page-header">Cuentas por Cobrar</h3>
                    <table id="tbl-cobros" class="display cell-border">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>CLIENTE</th>
                                <th>DOCUMENTO</th>
                                <th>TOTAL</th>
                                <th>TOTAL PAGADO</th>
                                <th>POR PAGAR</th>
                                <th>ESTADO</th>
                                <th>OPCIONES</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (is_array($clientesCredito)) : ?>
                                <?php foreach ($clientesCredito as $key => $cliente) : ?>
                                    <tr>
                                        <?php

                                        $objcliente = new cliente();
                                        $objcliente->setVar('id', $cliente['id_cliente']);
                                        $objcliente->getDB();
                                        $por_pagar = 0;
                                        $total_pagado = 0;
                                        $name = $objcliente->getNombre();
                                        $total = number_format($cliente['total'], 2, '.', '');

                                        $misVentasXcredito = $conn->consulta_matriz(
                                            "SELECT id FROM venta WHERE tipo_comprobante = -1 AND  id_cliente = {$cliente['id_cliente']} AND estado_fila = 1"
                                        );

                                        foreach ($misVentasXcredito as $key => $value) {
                                            $t = $conn->consulta_arreglo("SELECT SUM(monto) as total FROM `venta_medio_pago` WHERE id_venta = {$value['id']} AND medio <> 'CREDITO'");

                                            $total_pagado += floatval(($t[0]));
                                        }
                                        $total_pagado = number_format($total_pagado, 2, '.', '');

                                        $por_pagar = number_format($total - $total_pagado, 2, '.', '');

                                        ?>
                                        <td><?php echo $objcliente->getId() ?></td>
                                        <td><?php echo $objcliente->getNombre() ?></td>
                                        <td><?php echo $objcliente->getDocumento() ?></td>
                                        <td><?php echo $total ?></td>

                                        <td><?php echo $total_pagado ?></td>
                                        <td><?php echo $por_pagar ?></td>
                                        <td>
                                            <?php if ($por_pagar == 0) : ?>
                                                <span class="label label-success">Sin Deuda</span>
                                            <?php else : ?>
                                                <span class="label label-danger">Deuda</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" onclick="verVenta(<?php echo $cliente['id_cliente'] ?>)" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="col-md-7">
                        <div class="table-responsive tabla">
                            <table id="tbl-ventas-creditos" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>SERIE</th>
                                        <th>TOTAL</th>
                                        <th>MONTO PAGADO</th>
                                        <th>DEUDA</th>
                                        <th>ESTADO</th>
                                        <th>FECHA</th>
                                        <th>OPCIONES</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7">No se ha seleccionado un cliente.</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Total</th>
                                        <th>0</th>
                                        <th>0</th>
                                        <th>0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5" id="boxPagos" style="display:block;">

                        <div class="table-responsive tabla">
                            <table id="tbl-pagos-montos" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>MEDIO</th>
                                        <th>MONTO</th>
                                        <th>MONEDA</th>
                                        <th>OPCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5">No se ha seleccionado una venta.</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total a Pagar</th>
                                        <th>0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-5" id="boxDetalles" style="display:none;">

                        <div class="table-responsive tabla">
                            <table id="tbl-detalles2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5">No se ha seleccionado una venta.</td>
                                    </tr>
                                </tbody>
                                <!-- <tfoot>
                                <tr>
                                    <th>Total a Pagar</th>
                                    <th>0</th>
                                </tr>
                            </tfoot>            -->
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                    <h3 class="page-header">Cuentas por Pagar</h3>
                    <div class="table-responsive">
                        <table id="tblCompras" class="display cell-border">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>RAZÓN SOCIAL</th>
                                    <th>RUC</th>
                                    <th>Deuda Total</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (is_array($objsCompras)) : ?>
                                    <?php foreach ($objsCompras as $key => $object) : ?>
                                        <?php if ($object['Deuda'] != null) : ?>
                                            <tr>
                                                <td><?php echo $object['id'] ?></td>
                                                <td><?php echo $object['razon_social'] ?></td>
                                                <td><?php echo $object['ruc'] ?></td>
                                                <td><?php echo number_format($object['Deuda'], 2, '.', '') ?></td>
                                                <?php if ($object['Deuda'] > 0) : ?>
                                                    <td><span class="label label-danger">Deuda Pendiente</span></td>
                                                <?php else : ?>
                                                    <td><span class="label label-success">Cancelado</span></td>
                                                <?php endif; ?>
                                                <td>
                                                    <button type="button" onclick="detalles(<?php echo $object['id'] ?>)" title="Ver Detalles" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>
                                                </td>

                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                    </div>
                    <div class="col-md-8">
                        <div class="table-responsive tabla">
                            <table id="tbl-detalles" class="table table-stripped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha Registro</th>
                                        <th>Monto Total</th>
                                        <th>Monto Pendiente</th>
                                        <th>Tipo Documento</th>
                                        <th>Número</th>
                                        <th>Próximo Pago</th>
                                        <th>Pagar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7">No se ha seleccionado un proveedor</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th>0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="myModalVentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabelVentas">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabelVentas">Registro de pagos de créditos</h4>
                    </div>
                    <div class="modal-body">
                        <form id="pagos_ventas">
                            <input type="hidden" name="id_venta_medio_pago" id="id_venta_medio_pago">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="monto_actual">Por Pagar: </label>
                                            <input type="number" class="form-control" name="monto_actual" id="monto_actual" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="monto_actual">Monto: </label>
                                            <input type="number" class="form-control" name="monto_pago" id="monto_pago">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Tipo Pago: </label>
                                            <select class="form-control" name="medio" id="medio">
                                                <option value="" selected>Seleccione Tipo Pago...</option>
                                                <option value="VISA">VISA</option>
                                                <option value="MASTERCARD">MASTERCARD</option>
                                                <option value="EFECTIVO">EFECTIVO</option>
                                                <option value="DEPOSITO">DEPOSITO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="form-pagos">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="myModalPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!--Inicio Modal-->
        <div class='modal fade' id='modal_pago' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Pagar</h4>
                    </div>
                    <div class='modal-body row'>
                        <input type="hidden" id="monto_pendiente_compra" name="monto_pendiente_compra">
                        <input type='hidden' id='id_pago' name='id_pago' value='0' />
                        <input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"]; ?>' />
                        <div class='control-group col-md-6'>
                            <label>Caja pago</label>
                            <select class='form-control' id='caja_pago' name='caja_pago'>
                                <option value='0'>Fondos Externos</option>
                                <?php
                                $cajas = $conn->consulta_matriz("Select * from caja where estado_fila = 1");
                                if (is_array($cajas)) {
                                    foreach ($cajas as $ca) {
                                        echo '<option value="' . $ca["id"] . '">' . $ca['nombre'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class='control-group col-md-4'>
                            <label>Monto Pago</label>
                            <input class='form-control' type='number' value='0.00' id='monto_pago2' name='monto_pago2' onchange="verifica_monto()" />
                        </div>
                        <div class='control-group col-md-4' id="prx" style="display: none;">
                            <label>Proximo Pago</label>
                            <input type="date" class='form-control' placeholder='AAAA-MM-DD' id='proximo_pago_p' name='proximo_pago_p' required />
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-success' onclick="finaliza_pago()">Pagar</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal-->

        <!--Inicio Modal VER PAGOS -->
        <div class='modal fade' id='modal_pago' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Ver Pagos</h4>
                    </div>
                    <div class='modal-body row'>
                        <table id="tb_show_pago">
                            <thead>
                                <tr>
                                    <th>Fecha Registro</th>
                                    <th>Monto Total</th>
                                    <th>Usuario</th>                                    
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal-->

        <?php
        $nombre_tabla = 'reporte_ventas_compras_totales';
        require_once('recursos/componentes/footer.php');
        ?>
        <script src="recursos/js/notify.js"></script>