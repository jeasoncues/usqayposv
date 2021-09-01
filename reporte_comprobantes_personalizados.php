<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte de Comprobante personalizados';
$titulo_sistema = 'Katsu';
include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/venta.php');
include_once('nucleo/producto_venta.php');
include_once('nucleo/servicio_venta.php');
include_once('nucleo/producto.php');
include_once('nucleo/usuario.php');
include_once('nucleo/servicio.php');
include_once('nucleo/caja.php');
include_once('nucleo/cliente.php');
include_once('nucleo/turno.php');

$conn = new MasterConexion();
$obj= new venta();


require_once('recursos/componentes/header.php');
?>
<style>
    .bordeado{
        border: 1px solid #000;
    }
</style>
<body>

    <?php

    $caja_actual = $_COOKIE["id_caja"];

    $zero = 0;
    $uno = 0;
    $dos = 0;
    $tres = 0;
    $cuatro = 0;
    $subtotalTable = 0;

    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d');
   // $fechaCierre=$obj->fechaCierre();
    if (isset($_GET['fecha_inicio'])){
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }

    if (isset($_GET['fecha_fin'])){
        $fechaFin = $_GET['fecha_fin'];
    }

    if(isset($_GET["caja"])){
        $caja_actual = $_GET["caja"];
    }

    $stockAnterior = 0.00;
    $stockIngreso = 0.00;
    $stockSalida = 0;
    $totalvendido=  number_format(0.000,3);
    $totalimpuestos=  number_format(0.000,3);


    $objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante <> -1 AND estado_fila IN (9,10) AND id_caja = '{$caja_actual}' ORDER BY id DESC");

    $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila IN (9,10) and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante <> -1 AND id_caja = '{$caja_actual}'");
    $totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila IN (9,10) and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante <> -1 AND id_caja = '{$_COOKIE["id_caja"]}'");
    $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila IN (9,10)  and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante <> -1 AND id_caja = '{$caja_actual}'");




    if (isset($_GET['opcion'])){
        $tipo = $_GET['opcion'];

        $objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND estado_fila IN (9,10) AND tipo_comprobante = ".$tipo." ORDER BY id DESC");

        $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila IN (9,10)  and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = ".$tipo);
        $totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila IN (9,10)  and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = ".$tipo);
        $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila IN (9,10)  and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante = ".$tipo);

    }else
      $tipo = '';




    //echo json_encode($objs)
if($objs > 0){
    for($i=0; $i<count($objs); $i++){
        if($objs[$i]['estado_fila'] == '1'){
            if($objs[$i]['tipo_comprobante'] == '0')
                $zero += $objs[$i]['total'];
            else if($objs[$i]['tipo_comprobante'] == '1')
                $uno += $objs[$i]['total'];
            else if($objs[$i]['tipo_comprobante'] == '2')
                $dos += $objs[$i]['total'];
        }
        else if($objs[$i]['estado_fila'] == '3')
            $tres += $objs[$i]['total'];
        else if($objs[$i]['estado_fila'] == '4')
            $cuatro += $objs[$i]['total'];
    }

}

//die();
// $sucursal = UserLogin::get_pkSucursal();
    ?>
    </form>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="page-header">
                                <h3>Filtros por fechas</h3>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Fecha Inicio</label>
                                        <input type="date" id="txtfechaini" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Fecha Fin</label>
                                        <input  type=date id="txtfechafin" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo Comprobante</label>
                                        <select class="form-control" id="opc" name="opc">
                                            <option <?php if($tipo == '') echo"selected"; ?> value="">TODAS</option>
                                            <option <?php if($tipo == '0') echo"selected"; ?> value="0">NOTAS</option>
                                            <option <?php if($tipo == '1') echo"selected"; ?> value="1">BOLETA</option>
                                            <option <?php if($tipo == '2') echo"selected"; ?> value="2">FACTURA</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Caja</label>
                                        <select class="form-control" id="caja" name="caja">
                                            <?php
                                            $query_cajas = "Select * from caja where estado_fila = 1";
                                            $res_c = $conn->consulta_matriz($query_cajas);
                                            if (is_array($res_c)):
                                                foreach ($res_c as $cj):
                                                    ?>
                                                    <option value="<?php echo $cj["id"]; ?>" <?php if(intval($caja_actual) === intval($cj["id"])){echo "selected";}?>><?php echo $cj["nombre"]; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                    <button type="button" onclick="buscar()" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="panel bordeado">
                                        <div class="panel-body">
                                            <p><b>SUB TOTAL </b></p>
                                            <h3 class="text-center sb">S./ <?php echo number_format((floatval($subtotal['subtotal'])),2);?></h3>
                                            <p><b>TOTAL IMPUESTOS </b></p>
                                            <h3  class="text-center sb">S./ <?php echo number_format((floatval($totalimpuestos['impuestos'])),2);?></h3>
                                            <p><b>IMPORTE DE VENTA TOTAL </b></p>
                                            <h3  class="text-center sb">S./ <?php echo number_format((floatval($totalvendido['total'])),2);?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--<div class="col-md-5">
                    <div class="panel">
                        <div class="panel-body">
                            <input type="hidden" id="NOT" value="<?php echo $zero; ?>">
                            <input type="hidden" id="BOL" value="<?php echo $uno; ?>">
                            <input type="hidden" id="FAC" value="<?php echo $dos; ?>">
                            <input type="hidden" id="NOTC" value="<?php echo $tres; ?>">
                            <input type="hidden" id="NOTD" value="<?php echo $cuatro; ?>">
                            <div class="page-header">
                                <h3>Importe por Comprobantes</h3>
                            </div>
                            <div id="GraphBuy" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-body">
                            <div class='contenedor-tabla'>
                                <table id="tblKardex" title="Total de Ventas" class="display dataTable no-footer" >
                                    <thead>
                                        <tr>
                                            <th><center>Nro de Pedido</center></th>
                                            <th><center>Cliente</center></th>
                                            <th><center>Usuario</center></th>
                                            <th><center>Turno</center></th>
                                            <th><center>Caja</center></th>
                                            <th><center>Tipo de Comprobante</center></th>
                                            <th><center>Estado</center></th>
                                            <th><center>Fecha y hora de Pedido</center></th>
                                            <th><center>Fecha Cierre</center></th>
                                            <th>Dscto.</th>
                                            <th><center>SubTotal</center></th>
                                            <th><center>Total Impuestos</center></th>
                                            <th><center>Total</center></th>
                                            <th><center>Opciones</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_desc=0;
                                            if (is_array($objs)):
                                                foreach ($objs as $o):
                                                    $dscto = 0;
                                                    ?>
                                                    <tr>
                                                        <td style="text-align: center;"><?php echo $o['id']; ?></td>
                                                        <td style="text-align: center;"><?php
                                                        $objcliente = new cliente();
                                                        $objcliente->setVar('id', $o['id_cliente']);
                                                        $objcliente->getDB();
                                                        echo $objcliente->getNombre();
                                                        ?></td>
                                                        <td style="text-align: center;"><?php
                                                        $objusuario = new usuario();
                                                        $objusuario->setVar('id', $o['id_usuario']);
                                                        $objusuario->getDB();
                                                        echo $objusuario->getNombresYApellidos();
                                                        ?></td>
                                                        <td style="text-align: center;"><?php
                                                        $objoturno = new turno();
                                                        $objoturno->setVar('id', $o['id_turno']);
                                                        $objoturno->getDB();
                                                        echo $objoturno->getNombre();
                                                        ?></td>

                                                        <td style="text-align: center;"><?php
                                                        $objocaja = new caja();
                                                        $objocaja->setVar('id', $o['id_caja']);
                                                        $objocaja->getDB();
                                                        echo $objocaja->getNombre();
                                                        ?>

                                                        <td style="text-align: center;">
                                                        <?php
                                                        if ($o['tipo_comprobante']==1) {
                                                            echo "Boleta" ;
                                                        }
                                                        elseif ($o['tipo_comprobante']==2) {
                                                            echo "Factura";
                                                        }
                                                        elseif ($o['tipo_comprobante']==0) {
                                                            echo "Sin Comprobante";
                                                            ?>
                                                            <button onclick="generarComprobaten(<?php echo $o['id']; ?>)" class="btn btn-warning btn-xs">
                                                            Generar Comprobante</button>
                                                            <?php
                                                        }
                                                        ?></td>

                                                        <td style="text-align: center;">
                                                        <?php
                                                        if ($o['estado_fila']==10) {
                                                            echo "<span class='label label-success'>Emitida</span>";
                                                        }
                                                        else if ($o['estado_fila']==9){
                                                            echo "<span class='label label-danger'>Anulada</span>";}
                                                        ?></td>


                                                        <td style="text-align: center;"><?php echo ($o['fecha_hora']); ?></td>
                                                        <td style="text-align: center;"><?php echo $o['fecha_cierre']; ?></td>
                                                    </td>
                                                    <td style="text-align: center;"><?php
                                                        $descto = $conn->consulta_arreglo(
                                                            "SELECT ROUND(SUM(monto),2) AS dscto
                                                                        FROM venta_medio_pago
                                                                        WHERE id_venta = {$o['id']} AND medio = 'DESCUENTO'
                                                                        GROUP BY medio");

                                                        if (is_array($descto)) {
                                                            $dscto = $descto['dscto'];
                                                            $total_desc+=$dscto;
                                                        }
                                                        echo $dscto;
                                                        ?></td>
                                                    <td style="text-align: center;">
                                                        <?php echo number_format(floatval($o['subtotal']),2); ?>
                                                    </td>
                                                    <td style="text-align: center;"><?php echo  number_format(floatval($o['total_impuestos']),2); ?></td>
                                                    <td style="text-align: center;"><?php echo  number_format(floatval($o['total']-$dscto),2); ?></td>

                                                    <td style="text-align: center;">
                                                        <div class="btn-group">
                                                        <?php
                                                            if($o['estado_fila'] == 1){
                                                        ?>
                                                            <a title="Anular" class="btn btn-sm btn-danger" href='#' onclick="anula_venta(<?php echo $o['id']; ?>)"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                                        <?php
                                                            }

                                                            if($o['tipo_comprobante']!=0 && $o['estado_fila'] == 1){
                                                        ?>
                                                            <a title="Subir a SUNAT" class="btn btn-sm btn-success" href='#' onclick="subir_sunat(<?php echo $o['id']; ?> )"><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
                                                            <a title="Nota de Debito" class="btn btn-sm btn-info" href='#' onclick="notas(<?php echo $o['id']; ?>,'<?php echo $o['tipo_comprobante']; ?>','1' )"><i class="fa fa-user-times" aria-hidden="true"></i></a>
                                                                    <a title="Nota de Credito" class="btn btn-sm btn-default" href='#' onclick="notas(<?php echo $o['id']; ?>, '<?php echo $o['tipo_comprobante']; ?>','2',<?php echo $_COOKIE["id_caja"];?>)"><i class="fa fa-money" aria-hidden="true"></i></a>
                                                        <?php
                                                            }
                                                        ?>
                                                        <!-- <a title="Ver Medios de Pago" class="btn btn-sm btn-warning" href='detalles_venta_totales.php?id=<?php echo $o['id']; ?>'"><i class="fa fa-credit-card" aria-hidden="true"></i></a>
                                                        <a title="Ver Detalles de la Venta" class="btn btn-sm btn-primary" href='detalle_venta_productos.php?id=<?php echo $o['id']; ?>'"><i class="fa fa-file-text-o" aria-hidden="true"></i></a> -->
                                                         <?php
                                                            if($o['estado_fila'] == 1){
                                                        ?>
                                                        <a title="Reimprimir" class="btn btn-sm btn-default" href='#' onclick="reimprimir(<?php echo $o['id']; ?>,'<?php
                                                            if ($o['tipo_comprobante']==1) { echo "BOL" ;}
                                                            elseif ($o['tipo_comprobante']==2) {echo "FAC";}
                                                            elseif ($o['tipo_comprobante']==0) {echo "NOT";}
                                                            ?>',<?php echo $_COOKIE["id_caja"];?>)"><i class="fa fa-print" aria-hidden="true"></i></a>
                                                        <?php } ?>

                                                        </div>
                                                    </td>
                                                    </tr>
                                                    <?php
                                                endforeach;?>
                                                    <tr>
                                                    <td style="text-align: center;"><b>TOTALES<b></td>
                                                    <td style="text-align: center;"><b></b></td>
                                                    <td style="text-align: center;"><b></b></td>
                                                    <td style="text-align: center;"><b><b></b></td>
                                                    <td style="text-align: center;"><b><b></b></td>
                                                    <td style="text-align: center;"><b><b></b></td>
                                                    <td style="text-align: center;"><b><b></b></td>
                                                    <td style="text-align: center;"><b><b></b></td>
                                                    <td style="text-align: center;"><b><b></b></td>
                                                    <td style="text-align: center;"><b><?php echo number_format($total_desc, 2, '.', ' '); ?></b></td>
                                                    <td style="text-align: center;"><b><?php echo number_format(floatval($subtotal['subtotal']), 2, '.', ' '); ?></b></td>
                                                    <td style="text-align: center;"><b><?php echo number_format(floatval($totalimpuestos['impuestos']), 2, '.', ' '); ?></b></td>
                                                    <td style="text-align: center;"><b><?php echo number_format(floatval($totalvendido['total']-$total_desc), 2, '.', ' '); ?></b></td>
                                                    <td style="text-align: center;"><b><b></b></td>
                                                    </tr>

                                                            <?php endif;
                                                                ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
                                                <!-- Modal -->
        <div class="modal fade" id="myModalG" tabindex="-1" role="dialog" aria-labelledby="myModalLabelG">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelG">Generar Comprobante</h4>
            </div>
            <div class="modal-body">
                <form id="frm-gen" action="ws/venta.php">
                    <input type="hidden" id="id_v" name="id_v">
                    <input type="hidden" id="op" name="op" value="gen_comp_null">
                    <input type="hidden" id="total_v" name="total_v">
                    <input type="hidden" id="subtotal_v" name="subtotal_v">
                    <input type="hidden" id="igv_v" name="igv_v">
                    <div class="row">
                        <div class="col-md-12">

                            <label class="radio-inline">
                                <input type="radio" name="optComp" id="tipoc1" value="FAC"> Factura
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="optComp" id="tipoc2" value="BOL"> Boleta
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class='form-group'>
                                <label id="labelDoc">DNI: </label>
                                <input type="number" class='form-control' id='doc' name='doc' />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class='form-group'>
                                <label id="labelNombre">Nombre: </label>
                                <input type="text" class='form-control' id='nombre' name='nombre' />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class='form-group'>
                                <label id="labelDoc">Direccion: </label>
                                <input type="text" class='form-control' id='direccion' name='direccion' />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class='form-group'>
                                <label id="labelDoc">Correo Electronico: </label>
                                <input type="text" class='form-control' id='correo' name='correo' />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button form="frm-gen" type="submit" class="btn btn-primary">Genear</button>
            </div>
            </div>
        </div>
        </div>
                        <?php
                                $nombre_tabla = 'reporte_comprobantes_personalizados';
                                require_once('recursos/componentes/footer.php');
                                ?>

