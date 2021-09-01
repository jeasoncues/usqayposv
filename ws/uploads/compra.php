<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Compras';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');

require_once('nucleo/include/MasterConexion.php');

$objconn = new MasterConexion();

?>

        <input type='hidden' id='id' name='id' value='0'/>

<input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"];?>'/>

<div class='control-group col-md-4'>
    <label>Proveedor</label>
    <label class='form-control' id='txt_id_proveedor'>...</label>
    <p class='help-block'><a href='#modal_id_proveedor' data-toggle='modal'>Seleccionar</a></p>
    <input type='hidden' name='id_proveedor' id='id_proveedor' value=''/>
</div>
<div class='control-group col-md-4'>
    <label>Tipo Documento</label>
    <select class='form-control' id='categoria' name='categoria' >
        <option value='1'>Boleta</option>
        <option value='2'>Factura</option>
        <option value='3'>Nota de Venta</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Numero Documento</label>
    <input class='form-control' type='text' placeholder='Numero Documento' id='numero_documento' name='numero_documento' />
</div>

<div class='control-group col-md-4'>
    <label>Monto Total</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='monto_total' name='monto_total' />
</div>

<div class='control-group col-md-4'>
    <label>Fecha</label>
    <input class='form-control' placeholder='AAAA-MM-DD' id='fecha' name='fecha' />
</div>

<div class='control-group col-md-4'>
    <label>Caja pago</label>
    <select class='form-control' id='id_caja' name='id_caja' >
        <option value='0'>Fondos Externos</option>
        <?php 
            $cajas = $objconn->consulta_matriz("Select * from caja where estado_fila = 1");
            if(is_array($cajas)){
                foreach($cajas as $ca){
                    echo '<option value="'.$ca["id"].'">'.$ca['nombre'].'</option>';
                }
            }
        ?>
    </select>
</div>

<div class='control-group col-md-4'>
    <label>Monto Pendiente</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='monto_pendiente' name='monto_pendiente' required/>
</div>

<div class='control-group col-md-4'>
    <label>Proximo Pago</label>
    <input class='form-control' placeholder='AAAA-MM-DD' id='proximo_pago' name='proximo_pago' required/>
</div>

<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>
</form>
<hr/>
    

<?php
include_once('nucleo/compra.php');
$obj = new compra();
$objs = $obj->listDB();

include_once('nucleo/usuario.php');

include_once('nucleo/proveedor.php');

include_once('nucleo/caja.php');
?>

<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Usuario</th>
                <th>Proveedor</th>
                <th>Tipo Documento</th>
                <th>Numero Documento</th>
                <th>Monto Total</th>
                <th>Fecha</th>
                <th>Monto Pendiente</th>
                <th>Caja</th>
                <th>Proximo Pago</th>
                <th>Guias</th>
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
                        <td>
                        <?php
                        $objproveedor = new proveedor();
                        $objproveedor->setVar('id', $o['id_proveedor']);
                        $objproveedor->getDB();
                        echo $objproveedor->getRazonSocial();
                        ?></td>
                        <td><?php echo $o['numero_documento']; ?></td>
                        <td><?php switch(intval($o['categoria'])){
                            case 1: echo "Boleta";
                            break;
                        
                            case 2: echo "Factura";
                            break;
                        
                            case 3: echo "Nota de Venta";
                            break;
                        } ?></td>
                        <td><?php echo $o['monto_total']; ?></td>
                        <td><?php echo $o['fecha']; ?></td>
                        <td><?php echo $o['monto_pendiente']; ?></td>
                        <?php if($o['id_caja'] > 0){ ?>
                            <td>
                            <?php
                            $objcaja = new caja();
                            $objcaja->setVar('id', $o['id_caja']);
                            $objcaja->getDB();
                            echo '<span class="label label-danger">'.$objcaja->getNombre().'</span>';
                            ?>
                            </td>
                        <?php }else{ ?>
                            <td><span class="label label-info">FONDOS EXTERNOS</span></td>
                        <?php } ?>
                        <td><?php echo $o['proximo_pago']; ?></td>
                        <td>
                        <?php
                            $txt = "";
                            $guias = $objconn->consulta_matriz("Select g.* from compra_guia cg, guia_producto g where cg.id_compra = '".$o["id"]."' AND cg.id_guia_producto = g.id");
                            if(is_array($guias)){
                                foreach($guias as $g){
                                    $txt .= "<a href='movimiento_producto_guia.php?id=".$g["id"]."&numero=".$g["numero_guia"]."'>".$g["numero_guia"]."</a>,";
                                }
                                $txt = substr($txt,0,-1);
                                echo $txt;
                                echo "<br/>";
                            }         
                        ?>
                        <a href='#' onclick='addguia(<?php echo $o['id']; ?>)'><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                        </td>
                        <td>
                        
                        <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>

                        <a href='#' onclick='edit(<?php echo $o['id']; ?>)'><i class="fa fa-edit" aria-hidden="true"></i></a>
                        
                        <?php if(floatval($o["monto_pendiente"])>0){?>
                        <br/>
                        <a href='#' onclick='pagar(<?php echo $o['id']; ?>)'><i class="fa fa-usd" aria-hidden="true"></i></a>
                        <?php }?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'compra';
            require_once('recursos/componentes/footer.php');
            ?>       
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_proveedor' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
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
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_id_caja' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Caja</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_caja' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr><th></th><th>Id</th><th>Nombre</th><th>Ubicacion</th><th>Serie Impresora</th></tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_caja'>

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
        <div class='modal fade' id='modal_pago' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Pagar</h4>
                    </div>
                    <div class='modal-body row'>
                    <input type='hidden' id='id_pago' name='id_pago' value='0'/>
                    <div class='control-group col-md-6'>
                        <label>Caja pago</label>
                        <select class='form-control' id='caja_pago' name='caja_pago' >
                            <option value='0'>Fondos Externos</option>
                            <?php 
                                if(is_array($cajas)){
                                    foreach($cajas as $ca){
                                        echo '<option value="'.$ca["id"].'">'.$ca['nombre'].'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class='control-group col-md-4'>
                        <label>Monto Pago</label>
                        <input class='form-control' type='number' value='0.00' id='monto_pago' name='monto_pago' onchange="verifica_monto()"/>
                    </div>
                    <div class='control-group col-md-4' id="prx" style="display: none;">
                        <label>Proximo Pago</label>
                        <input type="date" class='form-control' placeholder='AAAA-MM-DD' id='proximo_pago_p' name='proximo_pago_p' required/>
                    </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-success' onclick="finaliza_pago()">Pagar</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal-->
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_guia' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Agregar Guia</h4>
                    </div>
                    <div class='modal-body'>
                        <input type='hidden' id='id_cmp' name='id_cmp' value='0'/>
                        <div class="row">
                            <div class='control-group col-md-12'>
                                <label for="numero_guia">Numero Guía: </label>
                                <select name="numero_guia" id="numero_guia" class="form-control">
                                    <?php
                                    $guias = $objconn->consulta_matriz(
                                        "SELECT numero_guia, date_format(fecha_registro, '%d-%m-%Y') as fecha_registro FROM guia_producto gp WHERE gp.numero_guia NOT IN(
                                                        SELECT gp.numero_guia FROM compra_guia cg
                                                        INNER JOIN guia_producto gp ON cg.id_guia_producto = gp.id )");
                                    foreach ($guias as $guia): ?>
                                        <option value="<?= $guia['numero_guia']; ?>"><?= "{$guia['numero_guia']} - [{$guia['fecha_registro']}]"; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!--                        <input class='form-control' placeholder='Numero Guía' id='numero_guia' name='numero_guia' required/>-->
                            </div>
                        </div>
                        <div class='control-group col-md-12' style="padding: 10px;">
                        <div class="alert alert-danger alert-dismissable" style="display:none;" id="error_guia">
                        La guía no se encuentra registrada
                        </div>
                        </div>
                        <div class='control-group col-md-12' style="height: 10px;"></div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-success' onclick="finaliza_guia()">Agregar</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal-->