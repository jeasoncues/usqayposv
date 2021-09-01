<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}


$titulo_pagina = 'Detalles de Venta #'.$_GET["id"];
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');


// include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/venta.php');

// $conn = new MasterConexion();
$obj= new venta();

$objs = $obj->consulta_matriz("SELECT pv.id, id_venta, nombre, precio, cantidad, total 
    from producto_venta pv, producto p 
    WHERE pv.estado_fila=1 AND id_venta = ".$_GET["id"]." AND pv.id_producto = p.id
    UNION
    Select sv.id, id_venta, nombre, precio, cantidad, total 
    from servicio_venta sv, servicio s 
    where id_venta = ".$_GET["id"]." AND sv.id_servicio = s.id");

?>

<body>
    <div class="panel panel-primary" style="margin: 10px;">
        <div class="panel-heading">
            <h3 class="panel-title">Productos de la Venta</h3>
        </div>
        <div class="panel-body">
            <button type="button" class="btn btn-success" onclick="history.back()">◄ Volver</button>
            <hr>
            <div class='contenedor-tabla'>
                <table id='tb' class='display' cellspacing='0' class="display dataTable no-footer">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Venta</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (is_array($objs)):
                            foreach ($objs as $o):
                                ?>
                                <tr>
                                    <td><?php echo $o['id']; ?></td>
                                    <td><?php echo $o['id_venta']; ?></td>
                                    <td><?php echo $o['nombre']; ?></td>
                                    <td><?php echo $o['precio']; ?></td>
                                    <td><?php echo $o['cantidad']; ?></td>
                                    <td><?php echo $o['total']; ?></td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    $nombre_tabla = 'detalles_venta';
    require_once('recursos/componentes/footer.php');
    ?>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tb').dataTable();
    });
</script>
