<?php
    require_once('globales_sistema.php');
    if(!isset($_COOKIE['nombre_usuario'])){
        header('Location: index.php');
    }
    $titulo_pagina = 'Boleta';
    $titulo_sistema = 'Katsu';
    require_once('recursos/componentes/header.php');
    ?>
                <input type='hidden' id='id' name='id' value='0'/>
                
                    <div class='control-group col-md-4'>
                    <label>Venta</label>
                    <label class='form-control' id='txt_id_venta'>...</label>
                    <p class='help-block'><a href='#modal_id_venta' data-toggle='modal'>Seleccionar</a></p>
                    <input type='hidden' name='id_venta' id='id_venta' value=''/>
                    </div>
            <div class='control-group col-md-4'>
            <label>Token</label>
                <textarea class='form-control' rows='3' id='token' name='token' required></textarea>   
            </div>
            <div class='control-group col-md-4'>
            <label>Serie</label>
                <input class='form-control' placeholder='Serie' id='serie' name='serie' />
            </div><input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
    <div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
    </div>
    </form>
    <hr/>
    <?php
    include_once('nucleo/boleta.php');
    $obj = new boleta();
    $objs = $obj->listDB();
    
                    include_once('nucleo/venta.php');
                    
    ?>
    <div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
    <thead>
    <tr>
    <th>Id</th><th>Venta</th><th>Token</th><th>Serie</th>
    <th>OPC</th>
    </tr>
    </thead>
    <tbody>
    <?php
        if (is_array($objs)):
        foreach ($objs as $o):
    ?>
    <tr><td><?php echo $o['id']; ?></td><td>
                    <?php 
                    $objventa = new venta();
                    $objventa->setVar('id',$o['id_venta']);
                    $objventa->getDB();
                    echo  $objventa->getVar($gl_boleta_id_venta);
                    ?></td><td><?php echo $o['token']; ?></td><td><?php echo $o['serie']; ?></td>
    <td><a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><br/><a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a></td>
    </tr>
    <?php
        endforeach;
        endif;
    ?>
    <?php
    $nombre_tabla = 'boleta';
    require_once('recursos/componentes/footer.php');
    ?>    
                    <!--Inicio Modal-->
                    <div class='modal fade' id='modal_id_venta' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                    <div class='modal-content'>
                    <div class='modal-header'>
                      <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                      <h4 class='modal-title' id='myModalLabel'>Venta</h4>
                    </div>
                    <div class='modal-body'>
                    <div class='contenedor-tabla'>
                        <table id='tbl_modal_id_venta' class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr><th></th><th>Id</th><th>Subtotal</th><th>Total Impuestos</th><th>Total</th><th>Tipo Comprobante</th><th>Fecha Hora</th><th>Fecha Cierre</th><th>Turno</th><th>Usuario</th><th>Caja</th><th>Cliente</th></tr>
                            </thead>
                            <tbody id='data_tbl_modal_id_venta'>

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