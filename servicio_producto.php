<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Productos en Servicio';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Servicio</label>
    <label class='form-control' id='txt_id_servicio'>...</label>
    <input type='hidden' name='id_servicio' id='id_servicio' value=''/>
</div>
<div class='control-group col-md-4'>
    <label>Producto</label>
    <label class='form-control' id='txt_id_producto'>...</label>
    <p class='help-block'><a href='#modal_id_producto' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_producto' id='id_producto' value=''/>
</div>
<div class='control-group col-md-4'>
    <label>Cantidad</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='cantidad' name='cantidad' />
</div>
<div class='control-group col-md-4'>
    <label>Almacen</label>
    <label class='form-control' id='txt_id_almacen'>...</label>
    <p class='help-block'><a href='#modal_id_almacen' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_almacen' id='id_almacen' value=''/>
</div><input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>
</form>
<hr/>
<?php
include_once('nucleo/servicio_producto.php');
$obj = new servicio_producto();
$objs = null;
if(isset($_GET["id"])){
    $objs = $obj->consulta_matriz("Select * from servicio_producto where id_servicio = '".$_GET["id"]."' and estado_fila = 1");
   
}

include_once('nucleo/servicio.php');

include_once('nucleo/producto.php');

include_once('nucleo/almacen.php');
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Servicio</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Almacen</th>
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
                        $objservicio = new servicio();
                        $objservicio->setVar('id', $o['id_servicio']);
                        $objservicio->getDB();
                        echo $objservicio->getVar($gl_servicio_producto_id_servicio);
                        ?></td>
                        <td>
                        <?php
                        $objproducto = new producto();
                        $objproducto->setVar('id', $o['id_producto']);
                        $objproducto->getDB();
                        echo $objproducto->getVar($gl_servicio_producto_id_producto);
                        ?></td>
                        <td><?php echo $o['cantidad']; ?></td>
                        <td>
                        <?php
                        $objalmacen = new almacen();
                        $objalmacen->setVar('id', $o['id_almacen']);
                        $objalmacen->getDB();
                        echo $objalmacen->getVar($gl_servicio_producto_id_almacen);
                        ?></td>
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
            $nombre_tabla = 'servicio_producto';
            require_once('recursos/componentes/footer.php');
            ?>    
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_producto' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
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
        <!--Fin Modal-->    
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_almacen' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Almacen</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_almacen' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr><th></th><th>Id</th><th>Nombre</th><th>Ubicacion</th></tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_almacen'>

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
        <?php if(isset($_GET["id"])):?>
        <script>
            $(document).ready(function() {
                sel_id_servicio(<?php echo $_GET["id"];?>);
            });
        </script>

        <?php endif;