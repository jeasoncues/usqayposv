<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Importacion almacen';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');

$objconn = new MasterConexion();

?>
</form>
<section class="row">

    <div class="col-md-12">
        <span>Para seleccionar mas de un producto presione Ctrl + clic en el producto.</span>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
        Importar
        </button>
        <div class="panel">
            <div class="panel-body">
            <div id="alert" class="alert alert-info" role="alert">Cargando productos...</div>
            <?php $productos = $objconn->consulta_matriz("SELECT p.id, p.nombre, al.id AS id_almacen, 
                            al.nombre AS almacen, GROUP_CONCAT(pt.valor) AS codigos
                            FROM producto p
                            LEFT JOIN producto_taxonomiap pt ON p.id = pt.id_producto
                            JOIN almacen al
                            WHERE pt.id_taxonomiap in(4,5)
                            GROUP BY p.id ");
                            ?>

                <table id="tb" hidden class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>PRODUCTO</th>
                            <th>ID ALMACEN</th>
                            <th>ALMACEN</th>
                            <th>CANTIDAD</th>
                            <th>CODIGO DE BARRAS</th>  
                            <th>CODIGO DE BARRA CLOUD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto) { ?>
                            <tr>
                                <td><?php echo $producto['id'] ?></td>
                                <td><?php echo $producto['nombre'] ?></td>
                                <td><?php echo $producto['id_almacen'] ?></td>
                                <td><?php echo $producto['almacen'] ?></td>
                                <td></td>
                                <?php $srt=explode(",",$producto['codigos']); ?>
                                <td><?php  echo $srt[0]; ?></td> 
                                <td><?php  if(empty($srt[1])) echo ""; else echo $srt[1]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Importar</h4>
            </div>
            <div class="modal-body">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw load"></i>
                <form id="form_import" enctype="multipart/form-data" method="POST" action="ws/producto.php">
                    <input type="hidden" name="op" value="import_seleccionados">
                    <div class="form-group">
                        <input type="file" name="file" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-file-excel-o"></i> Importar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
    $nombre_tabla = 'export_import';
    require_once('recursos/componentes/footer.php');
?>
<script>
    $(document).ready(function() {

        $('#tb').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'excelHtml5',
                {
                    extend: 'excelHtml5',
                    text: 'Exportar todos',
                    exportOptions: {
                        modifier: {
                            select: false
                        }
                    }
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            select: true
        });

        $('#tb').on('draw.dt', function() {
            $("#alert").hide();
            $("#tb").show();
        });
    });
</script>