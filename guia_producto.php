<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Guias Productos';
$titulo_sistema = 'pos';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>
<input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"]?>'/>
<div class='control-group col-md-4'>
    <label>Fecha Realizada</label>
    <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_realizada' name='fecha_realizada' value="<?php echo date('Y-m-d');?>"/>
</div>
<div class='control-group col-md-4'>
    <label>Tipo</label>
    <select class='form-control' id='tipo' name='tipo' >
        <option value='1'>Entrada</option>
        <option value='0'>Salida</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Numero Guia (Opcional)</label>
    <input class='form-control' placeholder='Numero Guia' id='numero_guia' name='numero_guia' value="<?php echo date('Ymdhi');?>" required/>
</div>
<div class='control-group col-md-4'>
    <label>Proveedor (Opcional)</label>
    <label class='form-control' id='txt_id_proveedor'>...</label>
    <p class='help-block'><a href='#modal_id_proveedor' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_proveedor' id='id_proveedor' value=''/>
</div>
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4' id="panel_save">
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>
<div class='control-group col-md-4' id="panel_end" style="display: none;">
    <p></p>
    <button type='button' class='btn btn-warning' onclick='finalizar()'>Finalizar</button>
</div>
</form>
<hr/>
<?php
include_once('nucleo/guia_producto.php');
$obj = new guia_producto();
$objs = $obj->listDB();

include_once('nucleo/usuario.php');
include_once('nucleo/proveedor.php');
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Usuario</th>
                <th>Fecha Registro</th>
                <th>Fecha Realizada</th>
                <th>Tipo</th>
                <th>Numero Guia</th>
                <th>Proveedor</th>
                <th>Productos</th>
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
                        $objusuario = new usuario();
                        $objusuario->setVar('id', $o['id_usuario']);
                        $objusuario->getDB();
                        echo $objusuario->getNombresYApellidos();
                        ?></td>
                        <td><?php echo $o['fecha_registro']; ?></td>
                        <td><?php echo $o['fecha_realizada']; ?></td>
                        <td><?php switch(intval($o['tipo'])){
                            case 1:
                                echo "Entrada";
                            break;
                        
                            case 0:
                                echo "Salida";
                            break;

                            case 2:
                                echo "Salida Almacenes";
                            break;

                            case 3:
                                echo "Entrada Almacenes";
                            break;
                        }?></td>
                        <td><?php echo $o['numero_guia']; ?></td>
                        <td>
                        <?php
                        if(!empty($o['id_proveedor'])){
                            $objproveedor = new proveedor();
                        $objproveedor->setVar('id', $o['id_proveedor']);
                        $objproveedor->getDB();
                        echo $objproveedor->getRazonSocial();   
                        }
                        ?></td>
                        <td>
                        <?php if(intval($o['tipo'])==1){?>
                            <a href='movimiento_producto_guia_old.php?id=<?php echo $o["id"];?>&numero=<?php echo $o["numero_guia"];?>'><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <?php } if(intval($o['tipo'])==0){?>
                            <a href='movimiento_producto_guia.php?id=<?php echo $o["id"];?>&numero=<?php echo $o["numero_guia"];?>'><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <?php } if(intval($o['tipo'])==2){?>
                            <a href='movimiento_producto_guia_almacenes.php?id=<?php echo $o["id"];?>&numero=<?php echo $o["numero_guia"];?>'><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <?php } if(intval($o['tipo'])==3){?>
                            <a href='movimiento_producto_guia_almacenes.php?id=<?php echo $o["id"];?>&numero=<?php echo $o["numero_guia"];?>'><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <?php } ?>
                        </td>
                        <td>

                        <?php if(intval($o['tipo'])==1){?>
                            <a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <br/>
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <?php } if(intval($o['tipo'])==0){?>
                            <a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <br/>
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <?php } if(intval($o['tipo'])==2){?>
                            <a href='#' onclick='aviso_edit_mea()'><i class="fa fa-ban" aria-hidden="true"></i></a>
                            <br/>
                            <a href='#' onclick='aviso_delete_mea()'><i class="fa fa-ban" aria-hidden="true"></i></a>
                        <?php } if(intval($o['tipo'])==3){?>
                            <a href='#' onclick='aviso_edit_mea()'><i class="fa fa-ban" aria-hidden="true"></i></a>
                            <br/>
                            <a href='#' onclick='aviso_delete_mea()'><i class="fa fa-ban" aria-hidden="true"></i></a>
                        <?php } ?>

                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'guia_producto';
            require_once('recursos/componentes/footer.php');
            ?>
            <!--Inicio Modal-->
            <div class='modal fade' id='modal_id_proveedor' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Proveedor</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_proveedor' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr><th></th><th>Id</th><th>Razon Social</th><th>Ruc</th><th>Direccion</th><th>Telefono</th></tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_proveedor'>

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