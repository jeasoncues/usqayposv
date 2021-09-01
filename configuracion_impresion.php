<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Configuracion impresion';
$titulo_sistema = 'Katsu';

require_once 'nucleo/include/MasterConexion.php';
$objcon = new MasterConexion();
$impresoras = $objcon->consulta_matriz("SELECT * FROM impresoras");

$row = null;
if (!empty($_GET['id'])) {
    $row = $objcon->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE id = {$_GET['id']}");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['option']) {
        case 'agregar': {
                $objcon->consulta_simple("insert into configuracion_impresion (caja, opcion, impresora) VALUES ({$_COOKIE['id_caja']}, '{$_POST['opcion']}', '{$_POST['impresora']}')");

                header('Location: configuracion_impresion.php');
                break;
            }
        case 'actualizar': {
                $objcon->consulta_simple("update configuracion_impresion set opcion='{$_POST['opcion']}', impresora='{$_POST['impresora']}' where id = {$_GET['id']}");

                header('Location: configuracion_impresion.php');
                break;
            }
        case 'eliminar': {
                $objcon->consulta_simple("delete from  configuracion_impresion where id = {$_POST['id']}");
                header('Location: configuracion_impresion.php');
                break;
            }
        case 'margen': {

                if (intval($_POST['id_margen']) === 0) {

                    $objcon->consulta_simple("INSERT INTO margenes_impresion (id,nombre_impresora, margen) VALUES (NULL,'{$_POST['nombre_impresora']}', '{$_POST['margen']}')");
                } else {
                    $objcon->consulta_simple("update margenes_impresion set margen = '{$_POST['margen']}' where nombre_impresora = '{$_POST['nombre_impresora']}'");
                }

                // header('Location: configuracion_impresion.php');
                break;
            }

        case 'add_print': {
                $red = 0;
                $print_name = $_POST['print_name'];

                if (isset($_POST['red'])) {
                    $red = 1;
                }
                $r = $objcon->consulta_simple("insert into impresoras (nombre,red) VALUES ('{$print_name}', '{$red}')");

                header('Location: configuracion_impresion.php');
                break;
            }
    }
}


$configs = $objcon->consulta_matriz("SELECT * FROM configuracion_impresion");

require_once('recursos/componentes/header.php');
?>
</form>
<form action="" method="post">
    <div class="col-md-3">
        <label for="">Impresoras</label>
        <select name="impresora" id="" class="form-control">
            <?php
            if (is_array($impresoras)) :
                foreach ($impresoras as $impresora) : ?>
                    <?php if ($row != null) { ?>
                        <option value="<?php echo $impresora['nombre'] ?>" <?php echo $row['impresora'] === $impresora['nombre'] ? 'selected' : '' ?>><?php echo $impresora['nombre']; ?></option>
                    <?php } ?>

                    <?php if ($row == null) { ?>
                        <option value="<?php echo $impresora['nombre'] ?>"><?php echo $impresora['nombre']; ?></option>
                    <?php } ?>

            <?php endforeach;
            endif; ?>
        </select>
        <a href="#" data-toggle="modal" data-target="#myModal">
            <i class="fa fa-plus"></i> Agregar Impresora
        </a>
    </div>
    <div class="col-md-3">
        <label for="">Documento</label>
        <select name="opcion" id="" class="form-control">
            <?php if ($row != null) { ?>
                <option value="BOL" <?php echo $row['opcion'] === 'BOL' ? 'selected' : '' ?>>Boleta</option>
                <option value="FAC" <?php echo $row['opcion'] === 'FAC' ? 'selected' : '' ?>>Factura</option>
                <option value="NOT" <?php echo $row['opcion'] === 'NOT' ? 'selected' : '' ?>>Nota de Venta</option>
                <option value="CIE" <?php echo $row['opcion'] === 'CIE' ? 'selected' : '' ?>>Cierre</option>
                <option value="PAG" <?php echo $row['opcion'] === 'PAG' ? 'selected' : '' ?>>Pago</option>
                <option value="PED" <?php echo $row['opcion'] === 'PED' ? 'selected' : '' ?>>Cocina</option>
                <option value="COT" <?php echo $row['opcion'] === 'COT' ? 'selected' : '' ?>>Cotizacion</option>
                <option value="NOTCD" <?php echo $row['opcion'] === 'NOTCD' ? 'selected' : '' ?>>Nota Credito/Debito</option>
            <?php } ?>
            <?php if ($row == null) { ?>
                <option value="BOL">Boleta</option>
                <option value="FAC">Factura</option>
                <option value="NOT">Nota de Venta</option>
                <option value="CIE">Cierre</option>
                <option value="PAG">Pago</option>
                <option value="PED">Cocina</option>
                <option value="COT">Cotizacion</option>
                <option value="NOTCD">Nota Credito/Debito</option>
            <?php } ?>
        </select>
    </div>
    <div>
        <input type="hidden" name="option" value="<?php echo is_null($row) ? 'agregar' : 'actualizar' ?>">
        <button style="margin-top: 25px;" type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>
<hr />

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Configuraciones</h3>
    </div>
    <div class="panel-body">
        <table class="table">
            <tr>
                <th>Caja</th>
                <th>Opcion</th>
                <th>Impresora</th>
                <th></th>
            </tr>

            <?php
            if (is_array($configs)) :
                foreach ($configs as $config) : ?>
                    <tr>
                        <td><?php echo $config['caja']; ?></td>
                        <td>
                            <?php
                            $label = '';
                            switch ($config['opcion']) {
                                case 'NOT':
                                    $label = 'Nota de venta';
                                    break;
                                case 'BOL':
                                    $label = 'Boleta';
                                    break;
                                case 'FAC':
                                    $label = 'Factura';
                                    break;
                                case 'CIE':
                                    $label = 'Cierre';
                                    break;
                                case 'PAG':
                                    $label = 'Pagos';
                                    break;
                                case 'PED':
                                    $label = 'Cocina';
                                    break;
                                case 'COT':
                                    $label = 'Cotizacion';
                                    break;
                                case 'NOTCD':
                                    $label = 'Nota Credito/Debito';
                                    break;
                            }
                            echo $label;
                            ?>
                        </td>
                        <td>
                            <?php
                            $margen = $objcon->consulta_arreglo("SELECT * FROM margenes_impresion WHERE nombre_impresora = '{$config['impresora']}'");
                            if (isset($margen['id'])) {
                            ?>
                                <a href="#" class="modal-impresora" data-margen="<?php echo $margen['margen']; ?>" data-id="<?php echo $margen['id']; ?>" data-impresora="<?php echo $config['impresora']; ?>" data-toggle="modal">
                                    <?php echo $config['impresora']; ?>
                                </a>
                            <?php
                            } else {
                            ?>
                                <a href="#" class="modal-impresora" data-margen="" data-id="0" data-impresora="<?php echo $config['impresora']; ?>" data-toggle="modal">
                                    <?php echo $config['impresora']; ?>
                                </a>

                            <?php
                            }
                            ?>
                        </td>
                        <td>
                            <a href="?id=<?php echo $config['id']; ?>" class=" btn btn-xs btn-primary">Editar</a>
                            <form action="" method="post" style="display: inline-block">
                                <input type="hidden" name="option" value="eliminar">
                                <input type="hidden" name="id" value="<?php echo $config['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-xs">Eliminar</button>
                            </form>
                        </td>
                    </tr>
            <?php endforeach;
            endif; ?>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Registro de Impresoras</h4>
            </div>
            <div class="modal-body">
                <form id="print_form" method="post">
                    <input type="hidden" name="option" value="add_print">
                    <div class="form-group">
                        <label for="print_name">Nombre o IP de Impesora*</label>
                        <input required placeholder="Nombre o IP de Impesora" type="text" name="print_name" id="print_name" class="form-control">
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="red" id="red" type="checkbox" value="1"> Imprimir por red
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button form="print_form" type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal">
    <div class="modal-dialog">
        <form class="modal-content" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Porcentaje de ajuste</h4>
            </div>
            <div class="modal-body">
                <label for="" id="label-impresora"></label>
                <input type="number" name="margen" id="txtMargen" class="form-control" min="0" value="0" step="0.01">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <input type="hidden" name="option" value="margen">
                <input type="hidden" name="id_margen" id="id_margen">
                <input type="hidden" name="nombre_impresora" id="nombre_impresora">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class='contenedor-tabla' style="display: none !important;">
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>

        </thead>
        <tbody>
            <?php
            $nombre_tabla = 'configuracion';
            require_once('recursos/componentes/footer.php');
            ?>

            <script>
                $(function() {
                    $('.modal-impresora').click(function(e) {
                        e.preventDefault();

                        var impresora = $(this).data('impresora');
                        var margen = $(this).data('margen');
                        var id = $(this).data('id');

                        $('#label-impresora').text(impresora);
                        $('#nombre_impresora').val(impresora);
                        $('#txtMargen').val(margen);
                        $('#id_margen').val(id);

                        $('#modal').modal('show');
                    });
                });
            </script>