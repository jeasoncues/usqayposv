<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Detalles Guia #'.$_GET["numero"];
$titulo_sistema = 'pos_planillas';
require_once('recursos/componentes/header.php');
include_once('nucleo/movimiento_producto.php');
$obj = new movimiento_producto();

 $gp = json_encode($obj->consulta_matriz("Select id_compra from compra_guia where id_guia_producto =".$_GET["id"]));

 $pp = json_decode($gp);

 if($pp == 0){
    $quitar = 'none';
 }else{
    $quitar = 'block';
 }

?>
<div class="container col-12">
    <button onclick="history.back();" type="button" class="btn btn-success btn-sm" > <i class="fa fa-arrow-left"></i> ATRAS</button>
</div>
<input type='hidden' id='id' name='id' value='0'/>
<input type='hidden' name='id_guia_producto' id='id_guia_producto' value='<?php echo $_GET["id"];?>'/>
<input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"]?>'/>
<!-- <div class='control-group col-md-6'>
    <label>Producto</label>
    <label class='form-control' id='txt_id_producto'>...</label>
    <p class='help-block'><a href='#modal_id_producto' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_producto' id='id_producto' value=''/>
</div>
<div class='control-group col-md-6'>
    <?php     
    $nombre_almacen = "...";
    $id_almacen = "";
    $query_almacen = "Select * from almacen where estado_fila = 1 order by ID ASC LIMIT 1";
    $re = $obj->consulta_arreglo($query_almacen);
    if(is_array($re)){
        $nombre_almacen = $re["nombre"];
        $id_almacen = $re["id"];
    }
    ?>
    <label>Almacen</label>
    <label class='form-control' id='txt_id_almacen'><?php echo $nombre_almacen;?></label>
    <p class='help-block'><a href='#modal_id_almacen' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_almacen' id='id_almacen' value='<?php echo $id_almacen;?>'/>
</div>
<div class='control-group col-md-2'>
    <label>Cantidad</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='cantidad' name='cantidad' />
</div>
<div class='control-group col-md-2'>
    <label>Costo</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='costo' name='costo' required/>
</div>

<div class='control-group col-md-4'>
    <label>Fecha de Vencimiento</label>
    <input class='form-control' type='text' id='fecha_vencimiento' name='fecha_vencimiento' />
</div>
<div class='control-group col-md-4'>
    <label>Lote</label>
    <input class='form-control' type='text' id='lote' name='lote' />
</div> -->
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<!-- <div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div> -->
<div class='control-group col-md-4 float-right'>
</div>
<div class='control-group col-md-4 float-right'>
    <p></p>
    <button type='button' class='btn btn-danger float-right' style="display: <?php echo $quitar ?>" onclick='desvincular(<?php echo $pp[0]->id_compra;?>, <?php echo $_GET["id"];?>)'> <i class="fa fa-unlink"></i> Desvincular Guia de la compra #<?php echo $pp[0]->id_compra;?></button>
</div>
</form>
<hr/>
<?php

$objs = null;
if(isset($_GET["id"])){
    
    // $objs = $obj->consulta_matriz("Select mp.* from movimiento_producto mp, guia_movimiento gm where gm.id_guia_producto = '".$_GET["id"]."' AND gm.id_movimiento_producto = mp.id AND mp.estado_fila = 1");
}

include_once('nucleo/producto.php');

include_once('nucleo/almacen.php');

include_once('nucleo/turno.php');
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Producto</th>
                <th>Almacen</th>
                <th>Cantidad</th>
                <th>Costo</th>
                <th>Total</th>
                <th>Fecha Op</th>
                <th>Fecha Venc.</th>
                <th>Lote</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_array($objs)):
                foreach ($objs as $o):
                    ?>
                    <tr>
                        <!-- <td><?php echo $o['id']; ?></td>
                        <td>
                        <?php
                        $objproducto = new producto();
                        $objproducto->setVar('id', $o['id_producto']);
                        $objproducto->getDB();
                        echo $objproducto->getNombre();
                        ?></td>
                        <td>
                        <?php
                        $objalmacen = new almacen();
                        $objalmacen->setVar('id', $o['id_almacen']);
                        $objalmacen->getDB();
                        echo $objalmacen->getNombre();
                        ?></td>
                        <td><?php echo abs($o['cantidad']); ?></td>
                        <td>S./<?php echo $o['costo']; ?></td>
                        <td>S./<?php echo floatval($o['costo'])*floatval(abs($o["cantidad"]));?></td>
                        <td><?php echo $o['fecha'];?></td>
                        <td><?php echo $o['fecha_vencimiento'];?></td>
                        <td><?php echo $o['lote'];?></td>
                        <td>
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td> -->
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'movimiento_producto_guia_almacenes';
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
                                        <th>Valor</th>
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
        <script src="recursos/js/notify.js"></script>