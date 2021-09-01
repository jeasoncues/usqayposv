<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte de Compras';
$titulo_sistema = 'Katsu';
include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/compra.php');
include_once('nucleo/usuario.php');
include_once('nucleo/proveedor.php');
include_once('nucleo/servicio.php');
include_once('nucleo/caja.php');

$conn = new MasterConexion();
$obj= new compra();

require_once('recursos/componentes/header.php');
?>

<style>
    .tabla{
        margin-top: 20px;
    }
    .table-bordered>thead>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>tfoot>tr>td {
        text-align: center !important;
    }
</style>

<body>
    <?php

        $objs = $conn->consulta_matriz(
            "SELECT p.id,p.razon_social,p.ruc,
            (SELECT SUM(monto_pendiente) FROM compra WHERE id_proveedor = p.id) AS 'Deuda' 
            FROM proveedor p");
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-11">
                <div class="panel">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="tblKardex" class="table table-bordered table-stripped">
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
                                <?php foreach($objs as $key => $object): ?>
                                    <?php if ($object['Deuda'] != null): ?>
                                        <tr>
                                            <td><?php echo $object['id']?></td>
                                            <td><?php echo $object['razon_social']?></td>
                                            <td><?php echo $object['ruc']?></td>
                                            <td><?php echo $object['Deuda']?></td>
                                            <?php if ($object['Deuda'] > 0): ?>
                                                <td><span class="label label-danger">Deuda Pendiente</span></td>
                                            <?php else : ?>
                                                <td><span class="label label-success">Cancelado</span></td>
                                            <?php endif; ?>
                                            <td>
                                                <button type="button" onclick="detalles(<?php echo $object['id']?>)" title="Ver Detalles" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-5">
                            <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive tabla">
                                <table id="tbl-detalles" class="table table-bordered table-stripped">
                                    <thead>
                                        <tr>
                                            <th>Fecha Registro</th>
                                            <th>Monto Total</th>
                                            <th>Monto Pendiente</th>
                                            <th>Tipo Documento</th>
                                            <th>Número</th>
                                            <th>Próximo Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="6">No se ha seleccionado un proveedor</td>
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
            
        </div>
        
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center" id="myModalLabel">Katsu Informa</h4>
        </div>
            <div class="modal-body text-center">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    </div>
    
<?php
    $nombre_tabla = 'reporte_compras_totales';
    require_once('recursos/componentes/footer.php');
?>
