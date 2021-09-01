<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}

if( !isset($_COOKIE['producto_precio']) ){
    setcookie('producto_precio', $_GET['id'], time() + (86400 * 30), "/");
}


$titulo_sistema = 'Katsu';
$id_producto = $_GET['id'];
require_once('nucleo/include/MasterConexion.php');
$objconn = new MasterConexion();
$producto = $objconn->consulta_arreglo("SELECT * FROM producto where id = ".$id_producto);
$titulo_pagina = $producto['nombre'];
require_once('recursos/componentes/header.php');

?>

<?php //if (isset($_COOKIE['producto_precio'])): ?>
    <div class="container">
    <div class="row">
        <div class="col-md-12">
        <a href="#" id="showAll" class="btn btn-danger">
            Mostrar todo el listado
        </a>
        </div>
    </div>
    </div>
<?php //endif; ?>

<input type='hidden' id='id' name='id' value='0'/>
<input type='hidden' id='id_producto' name='id_producto' value='<?php echo $id_producto?>'/>
<div class='control-group col-md-4'>
    <label>Descripción</label>
    <input class='form-control' placeholder='Descripción' id='descripcion' name='descripcion' />
</div>
<div class='control-group col-md-2'>
    <label>Cantidad</label>
    <input class='form-control' placeholder='' id='cantidad' name='cantidad' />
</div>
<div class='control-group col-md-3'>
    <label>Precio Compra</label>
    <input class='form-control' type='number' step='0.01' id='precio_compra' name='precio_compra' value='<?php echo $producto["precio_compra"]?>' />
</div>
<div class='control-group col-md-3'>
    <label>Precio Venta</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='precio_venta' name='precio_venta' />
</div>
<div class='control-group col-md-4'>
    <label>Impuesto</label>
    <select class='form-control' id='incluye_impuesto' name='incluye_impuesto' >
        <option value='1'>GRAVADA</option>
        <option value='0'>INAFECTA</option>
        <option value='2'>EXONERADA</option>
        <option value='3'>GRATIUTA</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Codigo de Barra </label>
    <input class='form-control' type='text' id='barcode_price' name='barcode_price' />
</div>


<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4' id="panel_save" style="margin-top: 25px;">

    <button type='button' class='btn btn-primary' onclick='hola()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
    <!-- <button type='button' class='btn btn-success' onclick='actualizar()'>Actualizar</button> -->
</div>

</form>
<hr/>
<?php
include_once('nucleo/producto.php');
$obj = new producto();
$objs = $obj->consulta_matriz("SELECT * FROM productos_precios WHERE id_producto = ".$id_producto);
?>
<div class='contenedor-tabla'>
<table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Incluye Impuesto</th>
                <th>Codigo Barra</th>
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
                        <td><?php echo $o['descripcion']; ?></td>
                        <td><?php echo $o['cantidad']; ?></td>
                        <td><?php echo $o['precio_compra']; ?></td>
                        <td><?php echo $o['precio_venta']; ?></td>
                        <td><?php if($o['incluye_impuesto'] == '1'){
                                echo "<span class='label label-success'>SI</span>" ;
                            }else{
                                echo "<span class='label label-warning'>NO</span>";
                        } ?></td>

                       <td><?php echo $o['barcode']; ?></td>
                        <td>
                            <div class="btn-group" role="group">
                            <a class="btn btn-sm btn-default" href='#' onclick='sel(<?php echo $o["id"]; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>

                            <a  class="btn btn-sm btn-default" href='#' onclick='del(<?php echo $o["id"]; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                            <a  class="btn btn-sm btn-default" target="_blank" href="ws/producto_barcode.php?id=<?php echo $o['id']; ?>"><i class="fa fa-barcode"></i></a>

                            </div>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'dummy2';
            require_once('recursos/componentes/footer.php');
            ?>
            <!--Inicio Modal-->
            <div class='modal fade' id='modal_precios' data-keyboard="false" data-backdrop="static" tabindex='-1' role='dialog' aria-labelledby='myModalLabelPrecios' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabelPrecios'></h4>
                    </div>
                    <div class='modal-body'>

                        <form id="frm-precios">
                            <input type="hidden" name="op" value="add-precios">
                            <input type="hidden" id="id-precio" name="id-precio">
                            <div class="form-group">
                                <label for="precio-descripcion">Descripción</label>
                                <input type="text" class="form-control" name="precio-descripcion" id="precio-descripcion">
                            </div>
                            <div class="form-group">
                                <label for="precio-compra">Precio Compra</label>
                                <input type="number" class="form-control" name="precio-compra" id="precio-compra">
                            </div>
                            <div class="form-group">
                                <label for="precio-venta">Precio Venta</label>
                                <input type="number" class="form-control" name="precio-venta" id="precio-venta">
                            </div>

                        </form>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                        <button type="submit" form="frm-precios" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
            </div>
            <!--Fin Modal-->
            <!--Inicio Modal-->
            <div class='modal fade' id='modal_imagen' data-keyboard="false" data-backdrop="static" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Imagen</h4>
                    </div>
                    <div class='modal-body'>
                        <center>
                        <img src="" width="250" height="250" id="muestra"/>
                        <p></p>
                        <p>
                            <input type='hidden' id='idimg' name='idimg' value='0'/>
                            <input class='form-control' placeholder='Sube tu archivo' id='imge' name='imge' type="file" />
                        </p>
                        </center>
                        <div id="progress">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    Subiendo imagen
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="upload_image()">Subir Imagen</button>
                    </div>
                </div>
            </div>
            </div>
            <!--Fin Modal-->
            <!--Inicio Modal-->
            <div class='modal fade' id='modal_cargando' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='myModalLabel'>Cargando</h4>
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
            <script>

                function hola(){
                    var vid = $('#id').val();
                    if(vid === '0'){
                        insert();
                    }else{
                        update();
                    }
                }
                jQuery.fn.reset = function () {
                $(this).each (function() { this.reset(); });
                };

                function insert(){

                    var id_producto = $('#id_producto').val();

                    var descripcion = $('#descripcion').val();

                    var precio_compra = $('#precio_compra').val();

                    var precio_venta = $('#precio_venta').val();

                    var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

                    var estado_fila = $('#estado_fila').val();
                    var barcode = $('#barcode_price').val();
                    var cantidad = $('#cantidad').val();

                    $.post('ws/producto.php', {op: 'add-precios',id:null,id_producto:id_producto,descripcion:descripcion,precio_compra:precio_compra,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila,barcode:barcode,cantidad:cantidad}, function(data) {
                    if(data === 0){
                        $('body,html').animate({scrollTop: 0}, 800);
                        $('#merror').show('fast').delay(4000).hide('fast');
                    }
                    else{
                        $("#modal_cargando").modal("show");

                        var vartimer = setInterval(function(){

                                $("#modal_cargando").modal("hide");
                                clearInterval(vartimer);
                                $('#frmall').reset();
                                $('body,html').animate({scrollTop: 0}, 800);
                                $('#msuccess').show('fast').delay(4000).hide('fast');
                                location.reload();

                        },500);


                    }
                    }, 'json');
                }

                function update(){

                    var id = $('#id').val();

                   var id_producto = $('#id_producto').val();

                    var descripcion = $('#descripcion').val();

                    var precio_compra = $('#precio_compra').val();

                    var precio_venta = $('#precio_venta').val();

                    var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

                    var estado_fila = $('#estado_fila').val();
                    var barcode = $('#barcode_price').val();

                    var cant = $('#cantidad').val();

                    $.post('ws/producto.php', {op: 'mod_precios',id:id,id_producto:id_producto,descripcion:descripcion,precio_compra:precio_compra,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila,barcode:barcode,cantidad:cant}, function(data) {
                    if(data === 0){
                        $('body,html').animate({scrollTop: 0}, 800);
                        $('#merror').show('fast').delay(4000).hide('fast');
                    }
                    else{
                        $("#modal_cargando").modal("show");
                        //Contadores

                        var vartimer = setInterval(function(){

                                $("#modal_cargando").modal("hide");
                                clearInterval(vartimer);
                                $('#frmall').reset();
                                $('body,html').animate({scrollTop: 0}, 800);
                                $('#msuccess').show('fast').delay(4000).hide('fast');
                                location.reload();

                        },500);


                    }
                    }, 'json');
                }

                function sel(id){
                    $.post('ws/producto.php', {op: 'get_precio', id: id}, function(data) {
                    if(data !== 0){
                        $('#id').val(data.id);
                        $('#descripcion').val(data.descripcion);
                        $('#precio_compra').val(data.precio_compra);
                        $('#precio_venta').val(data.precio_venta);
                        $('#incluye_impuesto option[value="'+data.incluye_impuesto+'"]').attr('selected', true);
                        $('#estado_fila').val(data.estado_fila);
                        $('#barcode_price').val(data.barcode);
                        $('#cantidad').val(data.cantidad);


                    }
                    }, 'json');
                }

                function del(id){
                    if (confirm("¿Desea eliminar esta operación?")) {
                        $.post('ws/producto.php', {op: 'del_precio', id: id}, function (data) {
                            if (data === 0) {
                                $('body,html').animate({scrollTop: 0}, 800);
                                $('#merror').show('fast').delay(4000).hide('fast');
                            }
                            else {
                                $('body,html').animate({scrollTop: 0}, 800);
                                $('#msuccess').show('fast').delay(4000).hide('fast');
                                location.reload();
                            }
                        }, 'json');
                    }
                }



                $(document).ready(function() {

                    var tbl = $('#tb').DataTable({
                        responsive: true,
                        "order": [[ 0, "desc" ]],
                        dom: 'Bfrtip',
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ],
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                        }
                    });


                    $("#progress").hide();

                    $('#showAll').on('click', function(e){
                        e.preventDefault();
                        location.href = 'producto.php?list=all';
                    })

                });



                function finalizar(){
                    $('#frmall').reset();
                    location.reload();
                }




            </script>