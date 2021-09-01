<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Detalles de Venta #'.$_GET["id"];
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');


// include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/venta.php');

// $conn = new MasterConexion();
$obj= new venta();

$objs = $obj->consulta_matriz("Select * from venta_medio_pago where id_venta = ".$_GET["id"]);

?>

<body>
    <div class="panel panel-primary" style="margin: 10px;">
        <div class="panel-heading">
            <h3 class="panel-title">Medios de Pago</h3>
        </div>
        <div class="panel-body">
            <button type="button" class="btn btn-success" onclick="history.back()">◄ Volver</button>
            <hr>
            <div class='contenedor-tabla'>
                <table id='tb' class='display' cellspacing='0' class="display dataTable no-footer">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Venta</th>
                            <th>Medio</th>
                            <th>Monto</th>
                            <th>vuelto</th>
                            <th>Moneda</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (is_array($objs)):
                            foreach ($objs as $o):
                                ?>
                                <tr>
                                    <td><?php echo $o['id']; ?></td>
                                    <td><?php echo $o['id_venta']; ?></td>
                                    <td><?php echo $o['medio']; ?></td>
                                    <td><?php echo $o['monto']; ?></td>
                                    <td><?php echo $o['vuelto']; ?></td>
                                    <td><?php echo $o['moneda']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning" id="btnregresar" onclick="exchange(<?php echo $o['id']?>)">
                                            <i class="fa fa-exchange" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    $nombre_tabla = 'detalles_venta';
    require_once('recursos/componentes/footer.php');
    ?>
    <!--Inicio Modal-->
    <div class='modal fade' id='modal_change' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title' id='myModalLabel'>Medios de Pago</h4>
                </div>
                <div class='modal-body'>
                    <!-- <form id="pagos_ventas"> -->
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="id_venta_medio_pago" id="id_venta_medio_pago">
                                        <label for="">Tipo Pago: </label>
                                        <select class="form-control" name="medio" id="medio">
                                            <option value="" selected>Seleccione Tipo Pago...</option>
                                            <option value="VISA">VISA</option>
                                            <option value="MASTERCARD">MASTERCARD</option>
                                            <option value="EFECTIVO">EFECTIVO</option>
                                            <option value="DEPOSITO">DEPOSITO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- </form> -->

                </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="form-pago">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!--Fin Modal-->
</body>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tb').dataTable();
        
            $('#form-pago').click(function(){
                if($('#medio').val() != ""){
                    $.ajax({
                        type: 'POST', 
                        url: 'ws/venta_medio_pago.php',
                        dataType: "json",
                        data: { 
                            op: 'exchange', 
                            medio: $('#medio').val(),
                            id: $('#id_venta_medio_pago').val()
                        },
                        success:function(response) {
                            location.reload();
                        },
                        error: function (err) {
                            // alert(JSON.stringify(err));

                        }
                    });
                }else{
                    alert("Selecciona un Medio de Pago");
                    return false;
                }

            });
    });

    function exchange(id){
        // alert(id);
        $('#id_venta_medio_pago').val(id);
        $("#modal_change").modal("show");
    }




</script>
