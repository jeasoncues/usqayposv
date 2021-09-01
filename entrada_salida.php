<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Movimiento Caja';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');

include_once('nucleo/movimiento_caja.php');
// $obj = new movimiento_caja();
// $objs = $obj->listDB();

$objconn = new MasterConexion();
$config = $objconn->consulta_arreglo("SELECT * FROM configuracion");

if(isset($_GET["fechaInicio"])){
    // echo "SELECT mc.`id`, c.`nombre`, mc.`monto`, `tipo_movimiento`, descripcion, `fecha`, `fecha_cierre`, `id_turno`, `id_usuario`
    //     FROM `movimiento_caja` mc
    //     INNER JOIN caja c ON mc.id_caja = c.id
    //     INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
    //     WHERE fecha_cierre BETWEEN '".$_GET["fechaInicio"]."' AND '".$_GET["fechaFin"]."' AND id_caja = '{$_COOKIE["id_caja"]}' ";
        
    $objs = $objconn->consulta_matriz("SELECT mc.`id`, c.`nombre`, mc.`monto`, `tipo_movimiento`, descripcion, `fecha`, `fecha_cierre`, `id_turno`, `id_usuario`
        FROM `movimiento_caja` mc
        INNER JOIN caja c ON mc.id_caja = c.id
        INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
        WHERE fecha_cierre BETWEEN '".$_GET["fechaInicio"]."' AND '".$_GET["fechaFin"]."' AND id_caja = '{$_COOKIE["id_caja"]}' ");

    if(isset($objs[0]['id'])){
        $cant = $objs[0]['id'];
    }else{
        $cant='';
    }
}else{

$objs = $objconn->consulta_matriz("SELECT mc.`id`, c.`nombre`, mc.`monto`, `tipo_movimiento`, descripcion, `fecha`, `fecha_cierre`, `id_turno`, `id_usuario`
    FROM `movimiento_caja` mc
    INNER JOIN caja c ON mc.id_caja = c.id
    INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
    WHERE fecha_cierre = '".$config["fecha_cierre"]."' AND id_caja = '{$_COOKIE["id_caja"]}' ");
    
    if(isset($objs[0]['id'])){
        $cant = $objs[0]['id'];
    }else{
        $cant='';
    }

}

// echo $cant;
?>

<fieldset>
  <legend></legend>
    <div class='control-group col-md-5'>
        <label>Fecha Inicio </label>
        <input class='form-control' placeholder='AAAA-MM-DD' id='fecha' name='fecha' required value="<?php echo $config["fecha_cierre"];?>"/>
    </div>
    <div class='control-group col-md-5'>
        <label>Fecha Fin</label>
        <input class='form-control' placeholder='AAAA-MM-DD' id='fecha2' name='fecha2' required value="<?php echo $config["fecha_cierre"];?>"/>
    </div>
    <div class='control-group col-md-2'>
        <p></p>
        <br>
        <button type='button' class='btn btn-primary' onclick='buscar()'>Buscar</button>
    </div>
    <hr/>
    <hr/>
    <hr/>
</fieldset>
<fieldset>
  <legend></legend>
<input type='hidden' name='id_caja' id='id_caja' value='<?php echo $_COOKIE["id_caja"]?>'/>
<div class='control-group col-md-4'>
    <label>Monto</label>
    <input class='form-control' type='number' value='0.00' step='1.00' id='monto' name='monto' />
</div>
<div class='control-group col-md-4'>
    <label>Descripcion</label>
    <input class='form-control' type='text' placeholder="Descripcion" id='descripcion' name='descripcion' />
</div>
<div class='control-group col-md-4'>
    <label>Tipo</label>
    <select class='form-control' id='tipo_movimiento' name='tipo_movimiento' >
        <option value='INBX|PEN|EFECTIVO'>Ingreso</option>
        <option value='OUTBX|PEN|EFECTIVO|PF'>Pago Fijo</option>
        <option value='OUTBX|PEN|EFECTIVO|PD'>Pago Diario</option>
        <option value='OUTBX|PEN|EFECTIVO|PP'>Pago Personal</option>
        <!--<option value='OUTBX|PEN|EFECTIVO|S'>Salidas</option>-->
    </select>
</div>
<input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"]?>'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='guardar()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>

</fieldset>
</form>
<hr/>


<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>id_caja</th>
                <th>monto</th>
                <th>Tipo Movimiento</th>
                <th>Descripcion</th>
                <th>Fecha</th>
                <th>Fecha Cierre</th>
                <th>Turno</th>
                <th>Usuario</th>
                <!-- <th>Guias</th>-->
                <th>OPC</th>
            </tr>
        </thead>
        <tbody id="block_">
            <?php
                if($cant != ''){
                foreach ($objs as $o):
                ?>
                    <tr>
                        <td><?php echo $o['id']; ?></td>
                        <td><?php echo $o['nombre']; ?></td>
                        <td><?php echo $o['monto']; ?></td>
                        <td><?php
                            if($o['tipo_movimiento'] == 'INBX|PEN|EFECTIVO' ){
                                echo '<span class="label label-success">INGRESO</span>';
                            }else if($o['tipo_movimiento'] == 'OUTBX|PEN|EFECTIVO|PF'){
                                echo '<span class="label label-danger">PAGO FIJO</span>';
                            }else if($o['tipo_movimiento'] == 'OUTBX|PEN|EFECTIVO|PD'){
                                echo '<span class="label label-danger">PAGO DIARIO</span>';
                            }else if($o['tipo_movimiento'] == 'OUTBX|PEN|EFECTIVO|PP'){
                                echo '<span class="label label-danger">PAGO PERSONAL</span>';
                           // }else if($o['tipo_movimiento'] == 'OUTBX|PEN|EFECTIVO|S'){
                           //     echo '<span class="label label-danger">SALIDAS</span>';
                            }else{
                                echo '<span class="label label-danger">SALIDA VARIOS</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo $o['descripcion']; ?></td>
                        <td><?php echo $o['fecha']; ?></td>
                        <td><?php echo $o['fecha_cierre']; ?></td>
                        <td><?php echo $o['id_turno']; ?></td>
                        <td><?php echo $o['id_usuario']; ?></td>
                        <td><a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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

<?php
$nombre_tabla = 'dummy';
require_once('recursos/componentes/footer.php');
?>
<script>
    $(document).ready(function() {
        $('#fecha').datepicker({dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });  

        $('#fecha2').datepicker({dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });   
    });

    function buscar(){
        var fecha = $("#fecha").val();
        var fecha2 = $("#fecha2").val();
        location.href = "entrada_salida.php?fechaInicio="+fecha+"&fechaFin="+fecha2;
    }

    function guardar(){
        $("#modal_envio_anim").modal("show");
        var id_caja = $("#id_caja").val();
        var monto = $("#monto").val();
        var tipo_movimiento = $("#tipo_movimiento").val();
        var tipo = $('#tipo_movimiento option:selected').text();
        var descripcion = $("#descripcion").val();
        var id_usuario = $("#id_usuario").val();
        var monto_abs = monto;
        var n = tipo_movimiento.indexOf("OUTBX");
        if(n>=0){
            monto = parseFloat(monto)*-1;
        }
        $.post('ws/movimiento_caja.php', {op: 'addin',id_caja:id_caja,monto:monto,tipo_movimiento:tipo_movimiento,id_usuario:id_usuario,tipo:tipo,descripcion:descripcion,monto_abs:monto_abs}, function(data) {
            if(data !== 0){
                $('#frmall').reset();
                document.title = 'KATSU IMPRIMIENDO';
                setTimeout(function(){
                    location.href = "entrada_salida.php";
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
