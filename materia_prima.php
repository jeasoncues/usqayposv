<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Materia Prima';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Almacen Origen</label>
    <label class='form-control' id='txt_id_almacen_origen'>...</label>
    <p class='help-block'><a href='#modal_id_almacen_origen' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_almacen_origen' id='id_almacen_origen' value=''/>
</div>
<div class='control-group col-md-4'>
    <label>Producto Origen</label>
    <label class='form-control' id='txt_id_producto_origen'>...</label>
    <p class='help-block'><a href='#modal_id_producto_origen' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_producto_origen' id='id_producto_origen' value=''/>
</div>
<div class='control-group col-md-4'>
    <label>Producto Destino</label>
    <label class='form-control' id='txt_id_producto_destino'>...</label>
    <p class='help-block'><a href='#modal_id_producto_destino' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_producto_destino' id='id_producto_destino' value=''/>
</div>
<div class='control-group col-md-4'>
    <label>Almacen Destino</label>
    <label class='form-control' id='txt_id_almacen_destino'>...</label>
    <p class='help-block'><a href='#modal_id_almacen_destino' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_almacen_destino' id='id_almacen_destino' value=''/>
</div>
<div class='control-group col-md-4'>
    <label>Cantidad</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='cantidad' name='cantidad' />
</div>
<div class='control-group col-md-4'>
    <label>Merma</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='merma' name='merma' />
</div><input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>
</form>
<hr/>
<?php
include_once('nucleo/materia_prima.php');
$obj = new materia_prima();
$objs = $obj->listDB();

include_once('nucleo/almacen.php');

include_once('nucleo/producto.php');

?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Almacen Origen</th>
                <th>Producto Origen</th>
                <th>Producto Destino</th>
                <th>Almacen Destino</th>
                <th>Cantidad</th>
                <th>Merma</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_array($objs)):
                foreach ($objs as $o):
                    ?>
                    <tr>
                        <td><?php echo $o['id']; ?></td>
                        <td>
                        <?php
                        $objalmacen_origen = new almacen();
                        $objalmacen_origen->setVar('id', $o['id_almacen_origen']);
                        $objalmacen_origen->getDB();
                        echo $objalmacen_origen->getVar($gl_materia_prima_id_almacen_origen);
                        ?></td>
                        <td>
                        <?php
                        $objproducto_origen = new producto();
                        $objproducto_origen->setVar('id', $o['id_producto_origen']);
                        $objproducto_origen->getDB();
                        echo $objproducto_origen->getVar($gl_materia_prima_id_producto_origen);
                        ?></td>
                        <td>
                        <?php
                        $objproducto_destino = new producto();
                        $objproducto_destino->setVar('id', $o['id_producto_destino']);
                        $objproducto_destino->getDB();
                        echo $objproducto_destino->getVar($gl_materia_prima_id_producto_destino);
                        ?></td>
                        <td>
                        <?php
                        $objalmacen_destino = new almacen();
                        $objalmacen_destino->setVar('id', $o['id_almacen_destino']);
                        $objalmacen_destino->getDB();
                        echo $objalmacen_destino->getVar($gl_materia_prima_id_almacen_destino);
                        ?></td>
                        <td><?php echo $o['cantidad']; ?></td>
                        <td><?php echo $o['merma']; ?></td>
                        <td>
                            <a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <br/>
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'materia_prima';
            require_once('recursos/componentes/footer.php');
            ?>    
            <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_almacen_origen' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Almacen Origen</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_almacen_origen' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr><th></th><th>Id</th><th>Nombre</th><th>Ubicacion</th></tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_almacen_origen'>

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
        <!--Fin Modal-->    
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_producto_origen' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Producto Origen</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_producto_origen' class='display' cellspacing='0' width='100%'>
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
                                <tbody id='data_tbl_modal_id_producto_origen'>

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
        <!--Fin Modal-->    
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_producto_destino' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Producto Destino</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_producto_destino' class='display' cellspacing='0' width='100%'>
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
                                <tbody id='data_tbl_modal_id_producto_destino'>

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
        <!--Fin Modal-->    
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_almacen_destino' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Almacen Destino</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_almacen_destino' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr><th></th><th>Id</th><th>Nombre</th><th>Ubicacion</th></tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_almacen_destino'>

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
        <!--Fin Modal-->