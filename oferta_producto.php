<?php

if (!$_COOKIE['id_usuario']) {
    header('Location: index.php');
}

require_once('globales_sistema.php');
$titulo_pagina = 'Productos en la oferta #'.$_GET['id'];
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
include_once('nucleo/include/MasterConexion.php');

$masterCon = new MasterConexion();
$con = $masterCon->getConnection();
$con->set_charset("utf8");


$oferta = $masterCon->consulta_arreglo("SELECT * FROM oferta WHERE id = {$_GET['id']}");

//$id_usuario = mysqli_real_escape_string($con, $_GET['id']);

//$res = $con->query("select * from usuario where id = {$id_usuario}");
//$usuario = $res->fetch_assoc();

?>

<input type='hidden' id='id_oferta' value="<?= $_GET['id']; ?>"/>

<div class='control-group col-md-4'>
    <label>Producto</label>
    <label class='form-control' id='txt_id_producto'>...</label>
    <p class='help-block'><a href='#modal_id_producto' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_producto' id='id_producto' value=''/>
</div>
<?php if(false): ?>
<div class="col-md-4">
    <div class='control-group'>
        <label>% descuento
        </label>
        <input type="number" class="form-control" id="descuento" min="0" placeholder="#">
    </div>
</div>
<?php endif;?>

<div class='control-group col-md-4'>
    <p></p>
    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
</div>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="ofertas.php" class="btn btn-sm btn-default">Volver</a>
        </div>
        <div class="panel body">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>% descuento</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $productos = $masterCon->consulta_matriz(
                    "SELECT op.id, p.nombre as producto, o.descuento AS descuento, op.principal as principal
                            FROM oferta_producto op
                            INNER JOIN oferta o on op.id_oferta = o.id
                            INNER JOIN producto p on op.id_producto = p.id
                            WHERE id_oferta = {$_GET['id']}");
                /*$productos = [];
                while ($row = $res->fetch_assoc()) {
                    $productos[] = $row;
                }*/

                foreach ($productos as $k=> $producto): ?>
                    <tr>
                        <td><?= $k+1; ?></td>
                        <td><?= $producto['producto']; ?></td>
                        <td><?= $producto['descuento'] ? $producto['descuento']."%" : ""; ?></td>
                        <td>
                            <?php if(!$producto['principal']):?>
                            <a href="" class="text-red" onclick="elimina(<?= $producto['id']; ?>)">
                                <i class="fa fa-trash"></i>
                            </a>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</form>
<hr/>
<div class='modal fade' id='modal_id_producto' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'
     aria-hidden='true'>
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span
                            class='sr-only'>Cerrar</span></button>
                <h4 class='modal-title' id='myModalLabel'>Producto</h4>
            </div>
            <div class='modal-body'>
                <div class='contenedor-tabla'>
                    <table id='tbl_modal_id_producto' class='display' cellspacing='0' width='100%'>
                        <thead>
                        <tr>
                            <th></th>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Incluye Impuesto</th>
                        </tr>
                        </thead>
                        <tbody id='data_tbl_modal_id_producto'>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
            </div>
        </div>
    </div>
</div>
<?php
$nombre_tabla = "";
require_once('recursos/componentes/footer.php');
?>

<script>
    function sel_id_producto(id_e) {
        $.post('ws/producto.php', {op: 'get', id: id_e}, function (data) {
            if (data != 0) {
                $('#id_producto').val(data.id);
                $('#txt_id_producto').html(data.nombre);
                $("#costo").val(data.precio_compra);
                $('#modal_id_producto').modal('hide');
            }
        }, 'json');
    }

    function elimina(id) {
        if (confirm("¿Desea eliminar este elemento?")) {
            $.post('ws/ofertas.php', {
                op: 'delProducto',
                id: id
            }, function (response) {
                if (response == 1) {
                    location.reload(false);
                }
            }, 'json');
        }
    }

    $(function () {
        $.post('ws/producto.php', {op: 'list'}, function (data) {
            if (data != 0) {
                $('#data_tbl_modal_id_producto').html('');
                var ht = '';
                $.each(data, function (key, value) {
                    ht += '<tr>';
                    ht += '<td><a href="#" onclick="sel_id_producto(' + value.id + ')">SEL</a></td>';
                    ht += '<td>' + value.id + '</td>';
                    ht += '<td>' + value.nombre + '</td>';
                    ht += '<td>' + value.precio_compra + '</td>';
                    ht += '<td>' + value.precio_venta + '</td>';
                    ht += '<td>' + value.incluye_impuesto + '</td>';
                    ht += '</tr>';
                });
                $('#data_tbl_modal_id_producto').html(ht);
                $('#tbl_modal_id_producto').dataTable();
            }
        }, 'json');

        $("#btnGuardar").click(function () {
            var idProducto = $('#id_producto').val();
            var descuento = $('#descuento').val();
            descuento = descuento ? descuento : 0;

            if (idProducto) {
                $.post('ws/ofertas.php', {
                    op: 'addProducto',
                    idOferta: $("#id_oferta").val(),
                    idProducto: idProducto,
                    descuento: descuento
                }, function (response) {
                    location.reload(true);
                });
            } else {
                alert("Debe seleccionar al menos un producto");
            }
        });
    });
</script>
