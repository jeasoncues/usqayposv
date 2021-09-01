<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Servicios';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
require_once('nucleo/include/MasterConexion.php');

$objconn = new MasterConexion();
?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Nombre</label>
    <input class='form-control' placeholder='Nombre' id='nombre' name='nombre' />
</div>
<div class='control-group col-md-4'>
    <label>Precio Venta</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='precio_venta' name='precio_venta' />
</div>
<div class='control-group col-md-4' style="display: none;" >
    <label>Impuesto</label>
    <select class='form-control' id='incluye_impuesto' name='incluye_impuesto'>
        <option value='1'>GRAVADA</option>
        <option value='0'>INAFECTA</option>
        <option value='2'>EXONERADA</option>
        <option value='3'>GRATIUTA</option>
    </select>
</div>
<!-- <input type='hidden' name='incluye_impuesto' id='incluye_impuesto' value='1'/> -->
<div class='control-group col-md-4'>
    <label>Categoría</label>
    <select class='form-control' id='categoria' name='categoria' onchange="carga_tipo()">
    <?php 
    $categorias = $objconn->consulta_matriz("Select * from taxonomias_valor where id_taxonomias = '2' and estado_fila = '1'");
    if(is_array($categorias)){
        foreach ($categorias as $cato){
            echo "<option value='".$cato["valor"]."'>".$cato["valor"]."</option>";
        }
    }
    ?>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Tipo</label>
    <select class='form-control' id='tipo' name='tipo'>
    </select>
</div>
<div class='control-group col-md-8'> 
    <label>Categoría Sunat</label>
    <div id="token-input">
    <input type="text" id="sunat-input" name="sunat-input" />
    </div>
    
    </select>
</div>

<?php
$caracteristicas = $objconn->consulta_matriz("Select * from taxonomias where estado_fila = 1 AND id > 3");
$total = 0;
if(is_array($caracteristicas)){
    $total = count($caracteristicas);
    foreach ($caracteristicas as $car){
    echo    "<div class='control-group col-md-4'>
             <label>".$car["nombre"]."</label>";

        $valores = $objconn->consulta_matriz("Select * from taxonomias_valor where id_taxonomias = '".$car["id"]."' and estado_fila = 1");
        if(is_array($valores)){
            echo    "<select class='form-control' id='".str_replace(" ", "_", strtolower($car["nombre"]))."' name='".str_replace(" ", "_", strtolower($car["nombre"]))."'>";
            foreach($valores as $val){
                echo "<option value='".$val["valor"]."'>".$val["valor"]."</option>";
            }
            echo    "</select>";
        }else{
            echo "<input class='form-control'  id='".str_replace(" ", "_", strtolower($car["nombre"]))."' name='".str_replace(" ", "_", strtolower($car["nombre"]))."' />";
        }
        echo    "</div>";
    }
}
?>

<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4' id="panel_save">
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>

</form>
<hr/>
<?php
include_once('nucleo/servicio.php');
$obj = new servicio();
$objs = $obj->listDB();
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Precio Venta</th>
                <!-- <th >Incluye Impuesto</th> -->
                <th>Categoría</th>
                <th>Tipo</th>
                <th>Categoría Sunat</th>
                <?php
                if(is_array($caracteristicas)){
                    foreach ($caracteristicas as $car){
                        echo "<th>".$car["nombre"]."</th>";
                    }
                }
                ?>
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
                        <td><?php echo $o['nombre']; ?></td>
                        <td><?php echo $o['precio_venta']; ?></td>
                        <!-- <td><?php if($o['incluye_impuesto'] == '1'){
                                echo "<span class='label label-success'>SI</span>" ;
                            }else{
                                echo "<span class='label label-warning'>NO</span>";
                        } ?></td> -->
                        <td><?php
                        $rc = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_taxonomias = 2 AND id_servicio = '".$o["id"]."'");
                        echo $rc["valor"];
                        ?></td>
                        <td><?php
                        $rt = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_taxonomias = 3 AND id_servicio = '".$o["id"]."'");
                        echo $rt["valor"];
                        ?></td>
                        <td><?php
                        $rt = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_taxonomias = -1 AND id_servicio = '".$o["id"]."'");
                        echo $rt["valor"];
                        ?></td>
                        <?php
                        if(is_array($caracteristicas)){
                            foreach ($caracteristicas as $car){
                                $rr = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_taxonomias = '".$car["id"]."' AND id_servicio = '".$o["id"]."'");
                                echo "<td>".$rr["valor"]."</td>";
                            }
                        }
                        ?>
                        <td><a href='servicio_producto.php?id=<?php echo $o["id"];?>'><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                        <td>
                            <a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <!--<a href='#' onclick='img(<?php echo $o['id']; ?>)'><i class="fa fa-file-image-o" aria-hidden="true"></i></a>-->
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'dummy';
            require_once('recursos/componentes/footer.php');
            ?>
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
                jQuery.fn.reset = function () {
                $(this).each (function() { this.reset(); });
                };


                function carga_tipo(){
                    var categoria = $("#categoria").val();
                    $.post('ws/taxonomias_valor.php', {op: 'listbypadre',valor: categoria}, function(data) {
                    if(data != 0){
                    $('#tipo').html('');
                    var ht = '';
                    $.each(data, function(key, value) {
                        ht += '<option value="'+value.valor+'">'+value.valor+'</option>';
                    });
                    $('#tipo').html(ht);
                    }
                    }, 'json');
                }
                
                function insert(){                
                    var nombre = $('#nombre').val();

                    var precio_venta = $('#precio_venta').val();

                    var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

                    var estado_fila = 1;

                    $.post('ws/servicio.php', {op: 'add',id:null,nombre:nombre,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila}, function(data) {
                    if(data === 0){
                        $('body,html').animate({scrollTop: 0}, 800);
                        $('#merror').show('fast').delay(4000).hide('fast');
                    }
                    else{
                        $("#modal_cargando").modal("show");
                        //Contadores
                        var total = <?php echo $total;?>;
                        total = total+2;
                        var count = 0;
                        //Categoria
                        var id_servicio = data;

                        var estado_fila = 1;
                        
                        var categoria = $("#categoria").val();
                        var tipo = $("#tipo").val();
                        var sunat = $('#sunat-input').val();
                        
                        $.post('ws/servicio_taxonimias.php', {op: 'add',id:null,id_servicio:id_servicio,id_taxonomias:2,valor:categoria,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');
                    
                        $.post('ws/servicio_taxonimias.php', {op: 'add',id:null,id_servicio:id_servicio,id_taxonomias:3,valor:tipo,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');

                        $.post('ws/servicio_taxonimias.php', {op: 'add_sunat',id:null,id_servicio:id_servicio,id_taxonomias:'-1',valor:sunat,estado_fila:estado_fila}, function(data) {
                            console.log(data);
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');
                        
                        <?php
                        if(is_array($caracteristicas)){
                            foreach ($caracteristicas as $car){
                                echo "
                                var ".str_replace(" ", "_", strtolower($car["nombre"]))." = $('#".str_replace(" ", "_", strtolower($car["nombre"]))."').val();
                                $.post('ws/servicio_taxonimias.php', {op: 'add',id:null,id_servicio:id_servicio,id_taxonomias:".$car["id"].",valor:".str_replace(" ", "_", strtolower($car["nombre"])).",estado_fila:estado_fila}, function(data) {
                                    if(data === 0){
                                        count = count + 1;
                                    }
                                    else{
                                        count = count + 1;
                                    }
                                }, 'json');";
                            }
                        }
                        ?>
                                    
                        var vartimer = setInterval(function(){ 
                            if(count >= total){
                                $("#modal_cargando").modal("hide");
                                clearInterval(vartimer);
                                $('#frmall').reset();
                                $('body,html').animate({scrollTop: 0}, 800);
                                $('#msuccess').show('fast').delay(4000).hide('fast');
                                location.reload();
                            }
                        },500);
                        
                        
                    }
                    }, 'json');
                }

                function update(){
                    var id = $('#id').val();
                    
                    var nombre = $('#nombre').val();

                    var precio_venta = $('#precio_venta').val();

                    var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

                    var estado_fila = 1;

                    $.post('ws/servicio.php', {op: 'mod',id:id,nombre:nombre,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila}, function(data) {
                    if(data === 0){
                        $('body,html').animate({scrollTop: 0}, 800);
                        $('#merror').show('fast').delay(4000).hide('fast');
                    }
                    else{
                        $("#modal_cargando").modal("show");
                        //Contadores
                        var total = <?php echo $total;?>;
                        total = total+2;
                        var count = 0;
                        //Categoria
                        var id_servicio = id;

                        var estado_fila = 1;
                        
                        var categoria = $("#categoria").val();
                        var tipo = $("#tipo").val();
                        var sunat = $('#sunat-input').val();
                        
                        $.post('ws/servicio_taxonimias.php', {op: 'mod1',id_servicio:id_servicio,id_taxonomias:2,valor:categoria,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');
                    
                        $.post('ws/servicio_taxonimias.php', {op: 'mod1',id_servicio:id_servicio,id_taxonomias:3,valor:tipo,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');

                        $.post('ws/servicio_taxonimias.php', {op: 'mod1_sunat',id_servicio:id_servicio,id_taxonomias:-1,valor:sunat,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');
                        
                        <?php
                        if(is_array($caracteristicas)){
                            foreach ($caracteristicas as $car){
                                echo "
                                var ".str_replace(" ", "_", strtolower($car["nombre"]))." = $('#".str_replace(" ", "_", strtolower($car["nombre"]))."').val();
                                $.post('ws/servicio_taxonimias.php', {op: 'mod1',id_servicio:id_servicio,id_taxonomias:".$car["id"].",valor:".str_replace(" ", "_", strtolower($car["nombre"])).",estado_fila:estado_fila}, function(data) {
                                    if(data === 0){
                                        count = count + 1;
                                    }
                                    else{
                                        count = count + 1;
                                    }
                                }, 'json');";
                            }
                        }
                        ?>
                                    
                        var vartimer = setInterval(function(){ 
                            if(count >= total){
                                $("#modal_cargando").modal("hide");
                                clearInterval(vartimer);
                                $('#frmall').reset();
                                $('body,html').animate({scrollTop: 0}, 800);
                                $('#msuccess').show('fast').delay(4000).hide('fast');
                                location.reload();
                            }
                        },500);
                        
                        
                    }
                    }, 'json');                
                }

                function sel(id){
                    $.post('ws/servicio.php', {op: 'get', id: id}, function(data) {
                    if(data !== 0){
                        $('#id').val(data.id);
                        $('#nombre').val(data.nombre);
                        $('#precio_venta').val(data.precio_venta);
                        $('#estado_fila').val(data.estado_fila);                        
                        $('#incluye_impuesto option[value="'+data.incluye_impuesto+'"]').attr('selected', true);
                        
                        //Ahora las taxonomias
                        
                        $.post('ws/servicio_taxonimias.php', {op: 'getbytax', id_servicio:id, id_taxonomias: 2}, function(data0) {
                        if(data0 !== 0){
                            $("#categoria").val(data0.valor);
                            // carga_tipo()
                            var categoria = $("#categoria").val();
                            $.post('ws/taxonomias_valor.php', {op: 'listbypadre',valor: categoria}, function(data) {
                                if(data != 0){
                                    $('#tipo').html('');
                                    var ht = '';
                                    $.each(data, function(key, value) {
                                        ht += '<option value="'+value.valor+'">'+value.valor+'</option>';
                                    });
                                    $('#tipo').html(ht);


                                    $.post('ws/servicio_taxonimias.php', {op: 'getbytax', id_servicio:id, id_taxonomias: 3}, function(data1) {
                                    if(data1 !== 0){                                        
                                        $("#tipo").val(data1.valor);
                                    }
                                    }, 'json');

                                }
                            }, 'json');
                        }
                        }, 'json');
                        
                        /* $.post('ws/servicio_taxonimias.php', {op: 'getbytax', id_servicio:id, id_taxonomias: 3}, function(data1) {
                        if(data1 !== 0){
                            console.log("data1",data1)
                            $("#tipo").val(data1.valor);
                        }
                        }, 'json'); */

                        $.post('ws/servicio_taxonimias.php', {op: 'getbytax', id_servicio:id, id_taxonomias: -1}, function(data2) {
                        $("#sunat-input").tokenInput("destroy");
                        console.log(data2);
                        if(data2 !== 0){
                            console.log(data2);
                            var valores = data2.valor.split("_");
 
                            $("#sunat-input").tokenInput("ws/taxonomia_sunat.php", {
                                theme: "facebook",
                                tokenLimit: 1,
                                searchingText: 'Buscando...',
                                minChars: 4,
                                prePopulate: [
                                    {id: valores[0], name: valores[1]},
                                ]
                            });
                        }else{
                            $("#sunat-input").tokenInput("ws/taxonomia_sunat.php", {
                                theme: "facebook",
                                tokenLimit: 1,
                                searchingText: 'Buscando...',
                                minChars: 4,
                                
                            });
                        }
                        }, 'json');
                        
                        <?php
                        if(is_array($caracteristicas)){
                            foreach ($caracteristicas as $car){
                                echo "
                                $.post('ws/servicio_taxonimias.php', {op: 'getbytax', id_servicio:id, id_taxonomias: ".$car["id"]."}, function(data1) {
                                if(data1 !== 0){
                                    $('#".str_replace(" ", "_", strtolower($car["nombre"]))."').val(data1.valor);
                                }
                                }, 'json');";
                            }
                        }
                        ?>
                    }
                    }, 'json');
                }

                function del(id){
                    if (confirm("¿Desea eliminar esta operación?")) {
                        $.post('ws/servicio.php', {op: 'del', id: id}, function (data) {
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
                    $("#sunat-input").tokenInput("ws/taxonomia_sunat.php", {
                        theme: "facebook",
                        tokenLimit: 1,
                        searchingText: 'Buscando...',
                        minChars: 4,
                    });
                    carga_tipo();
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
                });

                function save(){
                var vid = $('#id').val();
                if(vid === '0')
                {
                insert();
                }
                else
                {
                update();
                }
                }

                function finalizar(){
                    $('#frmall').reset();
                    location.reload();
                }

                function img(id){
                    $("#muestra").attr("src","recursos/uploads/servicios/"+id+".png");
                    $("#idimg").val(id);
                    $('#modal_imagen').modal('show');
                    $("#progress").hide();
                }

                function upload_image(){
                    $("#progress").show();
                    var id = $("#idimg").val();
                    var archivos = document.getElementById("imge");

                    var arc = 0;
                    try {
                        arc = archivos.files;
                    }
                    catch (err)
                    {
                    }

                    var data = new FormData();

                    for (i = 0; i <arc.length; i++) {
                        data.append('img', arc[i]);
                    }

                    data.append('op','img');
                    data.append('id',id);

                    var request = $.ajax({
                        url: 'ws/servicio.php',
                        type: 'POST',
                        contentType: false,
                        data: data,
                        processData: false,
                        cache: false
                    });
                    request.done(function() {
                        $("#imge").val("");
                        $('#modal_imagen').modal('hide');
                        $("#progress").hide();
                    });
                    request.fail(function() {
                        $("#imge").val("");
                        $('#modal_imagen').modal('hide');
                        $("#progress").hide();
                    });
                }

            </script>