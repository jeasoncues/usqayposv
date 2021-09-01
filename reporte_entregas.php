<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Entregas Pendientes';
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

<body>

    <?php
    $config = $conn->consulta_arreglo("Select * from configuracion");
    $fechaInicio = $config["fecha_cierre"];
    $fechaFin = $config["fecha_cierre"];
    if (isset($_GET['fecha_inicio'])){
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }
    
    if (isset($_GET['fecha_fin']))
        $fechaFin = $_GET['fecha_fin'];
    

    if($_COOKIE["tipo_usuario"] != 1){
        $objs = $conn->consulta_matriz("
            SELECT e.id, e.id_venta, cliente, fecha, abono, estado, tipo_comprobante, comentarios, v.estado_fila, GROUP_CONCAT(nombre) as producto
            FROM entregas e
            INNER JOIN venta v ON e.id_venta = v.id
            INNER JOIN producto_venta pv ON v.id = pv.id_venta
            INNER JOIN producto p ON pv.id_producto = p.id
            WHERE v.fecha_cierre between '".$fechaInicio."' AND '".$fechaFin. "' AND v.id_usuario = ".$_COOKIE["id_usuario"]." AND v.estado_fila = 1 GROUP BY e.id_venta ORDER BY e.fecha ASC");
    }else{
        $objs = $conn->consulta_matriz("
            SELECT e.id, e.id_venta, cliente, fecha, abono, estado, tipo_comprobante, comentarios, v.estado_fila, GROUP_CONCAT(nombre) as producto
            FROM entregas e
            INNER JOIN venta v ON e.id_venta = v.id
            INNER JOIN producto_venta pv ON v.id = pv.id_venta
            INNER JOIN producto p ON pv.id_producto = p.id
            WHERE v.fecha_cierre between '".$fechaInicio."' AND '".$fechaFin. "' AND v.estado_fila = 1 GROUP BY e.id_venta ORDER BY e.fecha ASC");
    }

    ?>

    <div class="modal fade" id="myModalVentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabelVentas">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelVentas">Detalles del Pedido</h4>
            </div>
            <div class="modal-body">
                <form id="pagos_ventas">
                    <input type="hidden" name="id_venta_medio_pago" id="id_venta_medio_pago">
                    <div class="container-fluid">
                        <div class="row">
                            <table class="table table-striped">
                                <thead>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </thead>
                                <tbody id="DetallesEntrega">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
            </div>
        </div>
        </div>


        <div class="panel panel-primary" id='pfecha' style="margin: 10px;">
            <div class="panel-heading">
                <h3 class="panel-title">Filtros por fechas</h3>
            </div>
            <div class="panel-body">
                <div class='control-group' id="dinicio">
                    <label>Fecha Inicio</label>
                    <input type="date" id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                    <label>Fecha Fin</label>
                    <input  type=date id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                    <br>
                    <button type="button" onclick="buscar()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>

                    <br>
                </div>
            </div>
        </div>
        </form>
        <div class='contenedor-tabla'>
        <table id="tblKardex" title="Total de Ventas" class="display dataTable no-footer" >
            <thead>
                <tr>
                    <th>id</th>
                    <th># venta</th>
                    <th>Cliente</th>
                    <th>Fecha de Entrega</th>
                    <th>Productos</th>
                    <th>Comentarios</th>
                    <th>Abono</th>
                    <th>estado</th>
                    <th>OPC</th>
                </tr>
            </thead>

            <tbody>
           <?php
            if (is_array($objs)):
                foreach ($objs as $o):
                    ?>
                    <tr>                        
                        <td><?php  
                            echo $o["id"];
                        ?></td>
                        
                        <td><?php
                            echo $o["id_venta"];
                        ?></td>
                        <td><?php
                            echo $o["cliente"];
                        ?></td>
                        <td><?php
                            echo date("d-m-Y",strtotime($o['fecha']))
                        ?></td> 
                        <td><?php
                            echo "<b>[".$o["producto"]."]</b>";
                        ?></td> 
                        <td><?php
                            echo $o["comentarios"];
                        ?></td> 
                        <td><?php
                            if($o["estado_fila"] != 2 ):
                                if($o["tipo_comprobante"] == -1):
                                    echo "<span class='label label-danger'> Debe </span>";
                                else:
                                    echo "<span class='label label-success'>Pagado</span>";
                                endif;
                            else:
                                echo "<span class='label label-danger'>Anulado</span>";
                            endif;
                        ?></td>
                        <td><?php
                            if($o["estado_fila"] != 2 ):
                                if($o["estado"] == 1):
                                    echo "<span class='label label-warning'>Por Entregar</span>";
                                else:
                                    echo "<span class='label label-success'>Entregado</span>";
                                endif;
                            else:
                                echo "<span class='label label-danger'>Anulado</span>";
                            endif;
                        ?></td>
                         <td >
                            <?php if($o["estado"] == 1 && $o["estado_fila"] != 2): ?>
                            <div class="btn-group" role="group">
                                <a title="Entregar" class="btn btn-sm btn-success" onclick='Entregar(<?php echo $o['id']?>)'><i class="fa fa-check" aria-hidden="true"></i></span></a>
                                <a title="Eliminar" class="btn btn-sm btn-danger" onclick='del(<?php echo $o['id']?>)'><i class="fa fa-trash" aria-hidden="true"></i></span></a>
                                <a title="Detalles" class="btn btn-sm btn-info" onclick='detalles(<?php echo $o['id']?>)'><i class="fa fa-ellipsis-v" aria-hidden="true"></i></span></a>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php
                endforeach;?>
                    
           <?php endif;
            ?>

    <?php
            $nombre_tabla = 'dummy';
            require_once('recursos/componentes/footer.php');

            ?>
<script type="text/javascript">

$(document).ready(function() {

    $('#tblKardex').DataTable({
        responsive: true,
        dom: 'Bfrtip', 
        order: [3, 'asc'],
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });
});

function Entregar(id){
    if (confirm("¿Desea entregar este pedido?")) {
        $.post('ws/venta.php', {
            op: 'entregar',
            id: id
        }, function(data) {
            location.reload();
        }, 'json');
    }
}

function del(id){
    if (confirm("¿Desea eliminar esta entrega?")) {
        $.post('ws/venta.php', {
            op: 'delentregar2',
            id: id
        }, function(data) {
            location.reload();
        }, 'json');
    }
}

function detalles(id){
    $.post('ws/venta.php', {
        op: 'detallesentrega',
        id: id
    }, function(data) {
        let html = '';

        $.each(data, function(key, value) {
            html += '<tr>';
            html += '<td>'+value.nombre+'</td>';
            html += '<td>'+value.precio+'</td>';
            html += '<td>'+value.cantidad+'</td>';
            html += '<td>'+value.total+'</td>';
            html += '</tr>';
        });
        $('#DetallesEntrega').html(html);
        $('#myModalVentas').modal();
        // location.reload();
    }, 'json');
}

function buscar() {
        window.location.href = "reporte_entregas.php?fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
    }


</script>
