<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Detalles Movimiento #'.$_GET["id"];
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
include_once('nucleo/include/MasterConexion.php');


?>

<div class="container col-12">
    <button onclick="history.back();" type="button" class="btn btn-success btn-sm" > <i class="fa fa-arrow-left"></i> ATRAS</button>
</div>

<input type='hidden' id='id' name='id' value='0'/>
<input type='hidden' name='id_movimiento_almacenes' id='id_movimiento_almacenes' value='<?php echo $_GET["id"];?>'/>
<input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"]?>'/>
<div class='control-group col-md-4'>
    <label>Producto</label>
    <label class='form-control' id='txt_id_producto'>...</label>
    <!-- <p class='help-block'><a href='#modal_id_producto' onclick="mostrarTable()" data-toggle='modal'>Seleccionar</a></p> -->
    <p class='help-block'id="botton_abrir_modal_producto" onclick="mostrarTable()"  > <a href="#" > Seleccionar</a></p>
    <input type='hidden' name='id_producto' id='id_producto' value=''/>
</div>
<div class='control-group col-md-4' style="display: none;" >
    <label>Almacen Origen</label>
    <label class='form-control' id='txt_id_almacen_o'>...</label>
    <p class='help-block'><a href='#modal_id_almacen_o' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_almacen_o' id='id_almacen_o' value=''/>
</div>
<div class='control-group col-md-4' style="display: none;">
    <label>Almacen Destino</label>
    <label class='form-control' id='txt_id_almacen_d'>...</label>
    <p class='help-block'><a href='#modal_id_almacen_d' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_almacen_dc' id='id_almacen_dc' value=''/>
</div>

<div class='control-group col-md-4'>
    <label>Almacen Destino</label>
    <select class="form-control" name="id_almacen_d" id="id_almacen_d">

    </select>
</div>

<div class='control-group col-md-4'>
    <label>Cantidad</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='cantidad' name='cantidad' />
    <small class="text-danger" style="font-size:15px;" id="mensaje_cantidad_maxima"></small>
    <input type="hidden" name="cantidad_maxima" id="cantidad_maxima">
</div>
<div class='control-group col-md-4'>
    <label>Costo</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='costo' name='costo' required/>
</div>
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-12'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>
</form>
<hr/>
<?php
include_once('nucleo/movimiento_producto.php');
$obj = new movimiento_producto();

$objs = null;
if(isset($_GET["id"])){
    $objs = $obj->consulta_matriz("Select * from guia_movimiento_a where id_movimiento_almacenes = '".$_GET["id"]."'");
}
include_once('nucleo/producto.php');
include_once('nucleo/almacen.php');
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Producto</th>
                <th>Almacen Origen</th>
                <th>Almacen Destino</th>
                <th>Cantidad</th>
                <th>Costo</th>
                <th>Total</th>
                <th>Fecha Op</th>
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
                        //Salida
                        $ms = new movimiento_producto();
                        $ms->setId($o["salida"]);
                        $ms->getDB();
                        
                        //Entrada
                        $me = new movimiento_producto();
                        $me->setId($o["entrada"]);
                        $me->getDB();
                        
                        
                        $objproducto = new producto();
                        $objproducto->setVar('id', $ms->getIdProducto());
                        $objproducto->getDB();
                        echo $objproducto->getNombre();
                        ?></td>
                        <td>
                        <?php
                        $objao = new almacen();
                        $objao->setVar('id', $ms->getIdAlmacen());
                        $objao->getDB();
                        echo $objao->getNombre();
                        ?></td>
                        <td>
                        <?php
                        $objad = new almacen();
                        $objad->setVar('id', $me->getIdAlmacen());
                        $objad->getDB();
                        echo $objad->getNombre();
                        ?></td>
                        <td><?php echo $me->getCantidad(); ?></td>
                        <td>S./<?php echo $me->getCosto(); ?></td>
                        <td>S./<?php echo floatval($me->getCantidad())*floatval($me->getCosto());?></td>
                        <td><?php echo $ms->getFecha();?></td>
                        <td>
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'movimiento_productos';
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
                                        <th>Almacen</th>
                                        <th>Stock</th>
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
        <div class='modal fade' id='modal_id_almacen_o' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Almacen Origen</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_almacen_o' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr><th></th><th>Id</th><th>Nombre</th><th>Ubicacion</th></tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_almacen_o'>

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
        <div class='modal fade' id='modal_id_almacen_d' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Almacen Salida</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_almacen_d' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr><th></th><th>Id</th><th>Nombre</th><th>Ubicacion</th></tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_almacen_d'>

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

        <script src="recursos/js/notify.js"></script>