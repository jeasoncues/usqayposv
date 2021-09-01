<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Cuadre general - Adelantos';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');

include_once('nucleo/movimiento_caja.php');
include_once('nucleo/usuario.php');
// $obj = new movimiento_caja();
// $objs = $obj->listDB();

$objconn = new MasterConexion();

$mes = date('m');
$año = date('Y');
$inicioMes = $año.'-'.$mes.'-01';

$config = $objconn->consulta_arreglo("Select * from configuracion where id=1");
$fecha_inicio =  isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $inicioMes;
$fecha_fin =  isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $config["fecha_cierre"];

$objs = $objconn->consulta_matriz("SELECT mc.`id`, c.`nombre`, mc.`monto`, `tipo_movimiento`, descripcion, `fecha`, `fecha_cierre`, `id_turno`, `id_usuario`
    FROM `movimiento_caja` mc
    INNER JOIN caja c ON mc.id_caja = c.id
    INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
    WHERE mc.fecha_cierre BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND mc.id_caja = '{$_COOKIE["id_caja"]}' AND mc.tipo_movimiento like '%ADV%'");

$usuarios = $objconn->consulta_matriz("SELECT * FROM usuario WHERE estado_fila = 1");
$cant = 0;
if(isset($objs[0]['id'])){
    $cant = $objs[0]['id'];
}

// echo $cant;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                <input type='hidden' name='id_caja' id='id_caja' value='<?php echo $_COOKIE["id_caja"]?>'/>
                <input type='hidden' name='id' id='id'/>
                <div class='control-group col-md-4'>
                    <label>Monto</label>
                    <input class='form-control' type='number' value='0.00' step='1.00' id='monto' name='monto' />
                </div>
                <div class='control-group col-md-4'>
                    <label>Descripcion</label>
                    <input class='form-control' type='text' placeholder="Descripcion" id='descripcion' name='descripcion' />
                </div>
                <div class='control-group col-md-4'>
                    <label>Usuario</label>
                    <select class="form-control" name="trabajador" id="trabajador">
                        <?php foreach($usuarios as $us): ?>
                            <option value="<?php echo $us['id']?>"><?php echo $us['nombres_y_apellidos']?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                
                <input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"]?>'/>
                <div class='control-group col-md-12'>
                    <p></p>
                    <button type='button' class='btn btn-primary' onclick='guardar()'>Guardar</button>
                    <button type='reset' class='btn'>Limpiar</button>
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
               
                    <div class='control-group col-md-4'>
                        
                            <label for="">Fecha Inicio</label>
                            <input type="date" class="form-control" name="inicio" id="inicio" 
                            value="<?php echo $fecha_inicio ?>">
                        
                
                    </div>
                    <div class='control-group col-md-4'>
                        
                            <label for="">Fecha Fin</label>
                            <input type="date" class="form-control" name="fin" id="fin" 
                            value="<?php echo $fecha_fin ?>">
                        
                    </div>

                    <div class='control-group col-md-4' style="margin-top: 23px">
                        <button id="buscar" type="button" class="btn btn-success">Buscar</button>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                
                <div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Trabajador</th>
                
                <th>Descripcion</th>
                <!-- <th>N° Comprobante</th> -->
                <th>Fecha</th>
                <!-- <th>Fecha Cierre</th> -->
                <!--<th>Turno</th>-->
                <!-- <th>Usuario</th> -->
                <th>Tipo Movimiento</th>
                <th>Monto</th>
                <!-- <th>Guias</th>-->
                <th>OPC</th>
            </tr>
        </thead>
        <tbody id="block_">
            <?php
                if($cant != ''){
                foreach ($objs as $o):
                    $num = explode("|",$o['tipo_movimiento']);
                ?>
                   
                    <tr>
                        <td><?php echo $o['id']; ?></td>
                        <td>
                            <?php 
                            
                                $obj_usuario = new usuario();
                                $obj_usuario->setVar('id', $num[4]);
                                $obj_usuario->getDB();
                                echo $obj_usuario->getNombresYApellidos(); 
                            
                            ?>
                        </td>
                        <td><?php echo $o['descripcion']; ?></td>
                        <td><?php echo $o['fecha']; ?></td>
                        <td>
                            <span class="label label-danger">Adelanto</span>
                        </td>
                        <td><?php echo abs($o['monto']); ?></td>
                        <td>
                            <?php
                                if( $_COOKIE['tipo_usuario'] == 1 ){
                                    ?>
                                        <a class="btn btn-default btn-sm" href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    <?php
                                }else if( $_COOKIE['tipo_usuario'] == 2 ){
                                    ?>
                                        <a class="btn btn-default btn-sm" href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    <?php
                                }
                            ?>
                        </td>
                    </tr>
                <?php
                endforeach;
                }
            ?>
            <?php
            $nombre_tabla = 'compra';
            require_once('recursos/componentes/footer.php');
            ?>
        </tbody>
    </table>
</div>
                </div>
            </div>
        </div>
    </div>
</div>





<?php
$nombre_tabla = 'dummy';
require_once('recursos/componentes/footer.php');
?>
<script>
    $(document).ready(function() {
        console.log('hola');
        $('#buscar').on('click', function(){
            var inicio = $('#inicio').val();
            var fin = $('#fin').val();
            location.href = `pagos.php?fecha_inicio=${inicio}&fecha_fin=${fin}`;
        })
    });

    function guardar(){
        if( $('#id').val() != '' ){
            edit_payment();
        }else{
            new_payment();
        }
        
    }
    

    function new_payment(){
        $("#modal_envio_anim").modal("show");
        var id_caja = $("#id_caja").val();
        var monto = $("#monto").val();
        var tipo_movimiento = "ADV|PEN|EFECTIVO|PX";
        var descripcion = $("#descripcion").val();
        var id_usuario = $("#id_usuario").val();
        var monto_abs = monto;
        monto = parseFloat(monto)*-1;
        
        $.post('ws/movimiento_caja.php', 
        {op: 'addpayment',
        id_caja:id_caja,
        monto:monto,
        tipo_movimiento:tipo_movimiento,
        id_usuario:id_usuario,
        descripcion:descripcion,
        monto_abs:monto_abs,
        comprobante: $("#trabajador").val()}, 
        function(data) {
            if(data !== 0){
                $('#frmall').reset();
                document.title = 'KATSU IMPRIMIENDO';
                setTimeout(function(){
                    location.href = "cierre_caja.php";
                }, 4000);
            }else{
                $("#modal_envio_anim").modal("hide");
                $('body,html').animate({
                   scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
        }, 'json');
    }

    function edit_payment(){
        $("#modal_envio_anim").modal("show");
        var id = $('#id').val();
        var id_caja = $("#id_caja").val();
        var monto = $("#monto").val();
        var tipo_movimiento = "ADV|PEN|EFECTIVO|PX";
        var descripcion = $("#descripcion").val();
        var id_usuario = $("#id_usuario").val();
        var monto_abs = monto;
        monto = parseFloat(monto)*-1;
        
        $.post('ws/movimiento_caja.php', 
        {op: 'editpayment',
        id: id,
        id_caja:id_caja,
        monto:monto,
        tipo_movimiento:tipo_movimiento,
        id_usuario:id_usuario,
        descripcion:descripcion,
        monto_abs:monto_abs, 
        comprobante: $("#trabajador").val()}, 
        function(data) {
            console.log(data);
            if(data !== 0){
                $('#frmall').reset();
                document.title = 'KATSU IMPRIMIENDO';
                location.reload();
            }else{
                $("#modal_envio_anim").modal("hide");
                $('body,html').animate({
                   scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
        }, 'json');
    }

    function sel(id){
        $.post('ws/movimiento_caja.php', { op:'get_', id: id }, function(response){
            console.log(response);
            var movimiento = response['tipo_movimiento'];
            $('#id').val(response['id']);
            $("#monto").val(Math.abs(Number(response['monto'])));
            $("#descripcion").val(response['descripcion']['descripcion']);
            $("#trabajador").val(movimiento.split("|")[4]);
            
        }, 'json')
    }

    function del(id){

    if (confirm("¿Desea eliminar esta operación?")){
        $.post('ws/movimiento_caja.php', {op: 'del', id: id}, function (data) {
            if (data === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
    }


    }
</script>

<!--Inicio Modal-->
<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Generando Impresión</h4>
            </div>
            <div class='modal-body'>
                <center>
                    <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
                </center>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal-->
