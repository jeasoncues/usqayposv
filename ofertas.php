<?php

if (!$_COOKIE['id_usuario']) {
    header('Location: index.php');
}

//require_once('globales_sistema.php');
$titulo_pagina = 'Ofertas';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
include_once('nucleo/include/MasterConexion.php');

$masterCon = new MasterConexion();
$con = $masterCon->getConnection();
$con->set_charset("utf8");

//$id_usuario = mysqli_real_escape_string($con, $_GET['id']);

//$res = $con->query("select * from usuario where id = {$id_usuario}");
//$usuario = $res->fetch_assoc();

?>
<div class="panel-body">
    <input type='hidden' id='id' name='id' value='0'/>
    <input type='hidden' id='id_modulo_componente'/>

    <div class='control-group col-md-2'>
        <label>Fecha de Inicio</label>
        <input type="text" id="fecha_inicio" class="form-control datepicker" readonly>
    </div>

    <div class='control-group col-md-2'>
        <label>Fecha de Fin</label>
        <input type="text" id="fecha_fin" class="form-control datepicker" readonly>
    </div>
    <div class='control-group col-md-4'>
        <label>Nombre</label>
        <input type="text" id="nombre" class="form-control">
    </div>
    <div class='control-group col-md-4'>
        <?php
        $stmt = $con->query("select * from tipo_oferta");

        $tipos = [];
        while ($row = $stmt->fetch_assoc()) {
            $tipos[] = $row;
        }

        $stmt->free();
        ?>
        <label>Tipo</label>
        <select name="" id="selectTipoOferta" class="form-control">
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= $tipo['id']; ?>"><?= $tipo['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4" id="of_x_cant">
        <div class='control-group'>
            <label>Cantidad de la oferta</label>
            <div class="input-group">
                <input type="number" class="form-control" min="0" id="cantidad_compra" placeholder="Compra"
                       style="z-index: 1!important;">
                <div class="input-group-addon">X</div>
                <input type="number" class="form-control" min="0" id="cantidad_paga" placeholder="Paga"
                       style="z-index: 1!important;">
            </div>
        </div>
    </div>

    <div class="col-md-4" id="tipo_desc_div">
        <div class='radio'>
            <label>
                <input type="radio" name="tipo_descuento" value="1" checked>Aplicado a la venta
            </label>
        </div>
        <div class='radio'>
            <label>
                <input type="radio" name="tipo_descuento" value="0">Aplicado al producto
            </label>
        </div>
    </div>

    <div class="col-md-4" id="nro_cupones_div">
        <div class='control-group'>
            <label>Cantidad de cupones</label>
            <input type="text" class="form-control" id="nro_cupones" placeholder="#">
        </div>
    </div>

    <div class="col-md-4" id="desc_div">
        <div class='control-group'>
            <label>% descuento</label>
            <input type="number" class="form-control" id="descuento" min="0" placeholder="#">
        </div>
    </div>

    <div class='control-group col-md-4' id="prod_div">
        <label>Producto</label>
        <label class='form-control' id='txt_id_producto'>...</label>
        <p class='help-block'><a href='#modal_id_producto' data-toggle='modal'>Seleccionar</a></p>
        <input type='hidden' name='id_producto' id='id_producto' value=''/>
    </div>

    <div class='control-group col-md-4'>
        <p></p>
        <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
    </div>
</div>
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel body">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha de inicio</th>
                    <th>Fecha de fin</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>% descuento</th>
                    <th>Productos</th>
                    <th>Cupones</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $res = $con->query(
                    "SELECT o.*, t.nombre as tipo_oferta
                                FROM oferta o 
                                INNER JOIN tipo_oferta t ON o.id_tipo=t.id");
                $ofertas = [];
                while ($row = $res->fetch_assoc()) {
                    $ofertas[] = $row;
                }

                foreach ($ofertas as $oferta): ?>
                    <tr>
                        <td><?= $oferta['id']; ?></td>
                        <td><?= date('d/m/Y', strtotime($oferta['fecha_inicio'])); ?></td>
                        <td><?= date('d/m/Y', strtotime($oferta['fecha_fin'])); ?></td>
                        <td><?= $oferta['descripcion']; ?></td>
                        <td>
                            <?php
                            if ($oferta['id_tipo'] == 1) {
                                echo "{$oferta['tipo_oferta']} ({$oferta['compra']}X{$oferta['paga']})";
                            } else {
                                echo $oferta['tipo_oferta'];
                            }

                            if (!is_null($oferta['tipo_desc'])) {
                                if ($oferta['tipo_desc'] == 1) {
                                    echo "(DESC VENTA)";
                                } elseif ($oferta['tipo_desc'] == 0) {
                                    echo "(DESC PRODUCTO)";
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?=
                            empty($oferta['descuento']) ? "SIN DESCUENTO" : $oferta['descuento']; ?>
                        </td>
                        <td>
                            <?php
                            if ($oferta['tipo_desc'] != 1):
                                $nro = $masterCon->consulta_arreglo(
                                    "SELECT count(*) as productos 
                                        FROM oferta_producto WHERE id_oferta={$oferta['id']}");

                                echo $nro['productos'];
                                ?>
                                <a href="oferta_producto.php?id=<?= $oferta['id']; ?>">
                                    <i class="fa fa-list-ul"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            if ($oferta['id_tipo'] == 3) :
                                $nro = $masterCon->consulta_arreglo(
                                    "SELECT count(*) as cupones 
                                        FROM cupon_oferta WHERE id_oferta={$oferta['id']}");

                                echo $nro['cupones'];
                                ?>
                                <a href="oferta_cupon.php?id=<?= $oferta['id']; ?>" class="text-green"><i
                                            class="fa fa-tags"></i></a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="" class="text-red" onclick="eliminaOferta(<?= $oferta['id']; ?>)">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
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
</form>
<hr/>

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

    function eliminaOferta(id) {
        $.post('ws/ofertas.php', {
            op: 'delOferta',
            id: id
        }, function (response) {
            location.reload();
        });
    }

    $(function () {
        $(".datepicker").datepicker({
            dateFormat: 'yy-m-d'
        });

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

        var selectTipoOferta = $("#selectTipoOferta");
        selectTipoOferta.change(function () {
            var id = parseInt(selectTipoOferta.val());
            switch (id) {
                case 1: {
                    $("#of_x_cant").show();
                    $("#nro_cupones_div").hide();
                    $("#tipo_desc_div").hide();
                    $("#desc_div").hide();
                    $("#prod_div").hide();
                    break;
                }
                case 2: {
                    $("#of_x_cant").hide();
                    $("#nro_cupones_div").hide();
                    $("#tipo_desc_div").hide();
                    $("#desc_div").hide();
                    $("#prod_div").show();
                    break;
                }
                case 3: {
                    $("#of_x_cant").hide();
                    $("#nro_cupones_div").show();
                    $("#tipo_desc_div").show();
                    $("#desc_div").show();
                    $("#prod_div").hide();
                    break;
                }
            }
        });

        $("#nro_cupones_div").hide();
        selectTipoOferta.trigger('change');

        var radio_desc = $("input[name=tipo_descuento]");
        /*radio_desc.change(function () {
            if ($(this).is(':checked')) {
                if ($(this).val() == 1) {
                    $("#desc_div").show();
                } else {
                    $("#desc_div").hide();
                }
            }
        });*/

        $("#btnGuardar").click(function () {
            var tipo_desc = $("input[name=tipo_descuento]:checked").val();
            var desc = $("#descuento").val();
            var id_tipo = selectTipoOferta.val();
            var id_producto = $('#id_producto').val();
            var ok = true;

            if (id_tipo == 3 && !desc) {
                alert("Debe ingresar el descuento");
                ok = false;
            } else {
                if (id_tipo == 2 && !id_producto) {
                    alert("Debe seleccionar un producto");
                    ok = false;
                } else {
                    ok = true;
                }
            }

            if (ok == true) {
                $.post('ws/ofertas.php', {
                    op: 'add',
                    id_tipo: selectTipoOferta.val(),
                    fecha_inicio: $("#fecha_inicio").val(),
                    fecha_fin: $("#fecha_fin").val(),
                    descuento: desc,
                    nro_cupones: $("#nro_cupones").val(),
                    cantidad_compra: $("#cantidad_compra").val(),
                    cantidad_paga: $("#cantidad_paga").val(),
                    tipo_desc: tipo_desc,
                    producto: id_producto

                }, function (response) {
                    location.reload();
                });
            }

        });
    });
</script>
