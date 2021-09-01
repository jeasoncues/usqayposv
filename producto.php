<?php
require_once('globales_sistema.php');

if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}

if( isset($_GET['list']) ){
    if( $_GET['list'] == 'all' ){
        unset($_COOKIE['producto_precio']);
        setcookie('producto_precio', '', time() - 3600, '/');
    }
}

if( isset($_COOKIE['producto_precio']) ){
    header("Location: productos_precios.php?id={$_COOKIE['producto_precio']}");
}

$titulo_pagina = 'Productos';
$titulo_sistema = 'pos';
require_once('recursos/componentes/header.php');
require_once('nucleo/include/MasterConexion.php');

$objconn = new MasterConexion();
?>
<div class="container-fluid">

<div class="panel col-md-12">
    <div class="panel-body">
    <input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Nombre</label>
    <input class='form-control' placeholder='Nombre' id='nombre' name='nombre' required />
</div>
<div class='control-group col-md-4'>
    <label>Unidad de Medida</label>
    <select class='form-control' id='unidad' name='unidad' >
       <?php  
        $unidades = $objconn->consulta_matriz("SELECT * FROM unidades ORDER by descripcion ");
            if(is_array($unidades)){
                foreach ($unidades as $und){
                  if  ($und["codigo"]=="NIU"){
                    echo "<option value='".$und["codigo"]."' selected>".$und["descripcion"]." [".$und["codigo"]."]</option>";
                    }else{
                    echo "<option value='".$und["codigo"]."'>".$und["descripcion"]." [".$und["codigo"]."]</option>";
                    }
                }
            }
        ?>
    </select>
</div>
<div class='control-group col-md-2'>
    <label>Precio Compra</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='precio_compra' name='precio_compra' required />
</div>

<div class='control-group col-md-2'>
    <label>Precio Venta</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='precio_venta' name='precio_venta' required/>
</div>
<div class='control-group col-md-2'>
    <label>Impuesto</label>
    <select class='form-control' id='incluye_impuesto' name='incluye_impuesto' >
        <option value='1'>GRAVADA</option>
        <option value='0'>INAFECTA</option>
        <option value='2'>EXONERADA</option>
        <option value='3'>GRATIUTA</option>
    </select>
</div>
<div class='control-group col-md-2' style="display: none;">
    <div style=" display: block;
    width: 100%;
    height: 34px;
    padding: 26px 12px;
    font-size: 14px;
    line-height: 1.42857143;">
        <div class="checkbox">
            <label>
            <input type="checkbox" id='icbper' name='icbper' disabled="true">
                <b>ICBPER</b>
            </label>
        </div>
    </div>
</div>

<div class='control-group col-md-4'>
    <label>Categoría</label>
    <select class='form-control' id='categoria' name='categoria' onchange="carga_tipo()">
    <?php
    $categorias = $objconn->consulta_matriz("Select * from taxonomiap_valor where id_taxonomiap = '2' and estado_fila = '1'");
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
        <input class='form-control' type="text" id="sunat-input" name="sunat-input" />
    </div>
</div>


<?php
$caracteristicas = $objconn->consulta_matriz("Select * from taxonomiap where estado_fila = 1 AND id > 3");
$total = 0;
if(is_array($caracteristicas)){
    $total = count($caracteristicas);
    foreach ($caracteristicas as $car){

    echo    "<div class='control-group col-md-4'>
             <label>".$car["nombre"]."</label>";



        $valores = $objconn->consulta_matriz("Select * from taxonomiap_valor where id_taxonomiap = '".$car["id"]."' and estado_fila = 1");
        if(is_array($valores)){
            echo    "<select class='form-control' id='".str_replace(" ", "_", strtolower($car["nombre"]))."' name='".str_replace(" ", "_",strtolower($car["nombre"]))."'>";
            foreach($valores as $val){
                echo "<option value='".$val["valor"]."'>".$val["valor"]."</option>";
            }
            echo    "</select>";
        }else{
            echo "<input class='form-control'  id='".str_replace(" ", "_",strtolower($car["nombre"]))."' name='".str_replace(" ", "_",strtolower($car["nombre"]))."' />";
        }
        echo    "</div>";
    }
}
?>

<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4' id="panel_save" style="margin-top: 22px;">

    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
    <!-- <button type='button' class='btn btn-success' onclick='actualizar()'>Actualizar</button> -->
</div>

</form>

</div>
  <!--  <div class='control-group col-md-4' style="margin-top: 0px;">
       
    </div>
    <div class='control-group col-md-4' style="margin-top: 0px;">
        <label>Valor</label>
        <input type="number"class='form-control' placeholder='' id='updateVal' name='updateVal' />
        <br>
    </div>
    <div class='control-group col-md-4' style="margin-top: 22px;">
    <button type='button' class='btn btn-warning' onclick='updateValor()'>CAMBIO</button>
       </div>
   -->
    
</div>
</div>


<?php
// include_once('nucleo/producto.php');
// $obj = new producto();
// $objs = $obj->listDB();
?>

<div class='panel contenedor-tabla'>
    <div class="panel-body">
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Unidad</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <!-- <th>Incluye Impuesto</th> -->
                <th>Categoría Sunat</th>
                <th>Categoría</th>
                <th>Tipo</th>

                <?php
                if(is_array($caracteristicas)){
                    foreach ($caracteristicas as $car){
                        echo "<th>".$car["nombre"]."</th>";
                    }
                }
                ?>
                <th>OPC</th> 
            </tr>
        </thead>
        <tbody>

    </div>

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
            <script src="recursos/js/notify.js"></script>
            <script>
                jQuery.fn.reset = function () {
                $(this).each (function() { this.reset(); });
                };


                function actualizar(){
                    $.post('ws/producto.php', {op: 'act'}, function (data) {

                    }, 'json');
                }


                function carga_tipo(){
                    var categoria = $("#categoria").val();
                    $.post('ws/taxonomiap_valor.php', {op: 'listbypadre',valor: categoria}, function(data) {
                    if(data != 0){
                    $('#tipo').html('');
                    var ht = '';
                    console.log(data)
                    $.each(data, function(key, value) {
                        ht += '<option value="'+value.valor+'">'+value.valor+'</option>';
                    });
                    $('#tipo').html(ht);
                    }
                    }, 'json');
                }
                function updateValor(){
                   
                    var updateVal = $('#updateVal').val();
                   
                    $.post('ws/producto.php', {op: 'updateValor',valor:updateVal}, function(data) {
                        if(data != 0){
                            $('#updateVal').val("");
                            location.reload();
                            }
                    }, 'json');
                }
                function insert(){
                    var nombre = $('#nombre').val();

                    var unidad = $('#unidad').val();

                    var precio_compra = $('#precio_compra').val();

                    var precio_venta = $('#precio_venta').val();

                    var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

                    var estado_fila = $('#estado_fila').val();

                    var icbper = $('#icbper').is(":checked");

                    if(icbper){
                        bolsa = 1;
                    }else{
                        bolsa = 0;
                    }

                    $.post('ws/producto.php', {op: 'add',id:null,nombre:nombre,unidad:unidad,precio_compra:precio_compra,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila, icbper:bolsa}, function(data) {
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
                        var id_producto = data;

                        var estado_fila = 1;

                        var categoria = $("#categoria").val();
                        var tipo = $("#tipo").val();
                        var sunat = $('#sunat-input').val();

                        $.post('ws/producto_taxonomiap.php', {op: 'add',id:null,id_producto:id_producto,id_taxonomiap:2,valor:categoria,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');

                        $.post('ws/producto_taxonomiap.php', {op: 'add',id:null,id_producto:id_producto,id_taxonomiap:3,valor:tipo,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');

                        $.post('ws/producto_taxonomiap.php', {op: 'add_sunat',id:null,id_producto:id_producto,id_taxonomiap:-1,valor:sunat,estado_fila:estado_fila}, function(data) {
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
                                var ".str_replace(" ", "_",strtolower($car["nombre"]))." = $('#".str_replace(" ", "_",strtolower($car["nombre"]))."').val();
                                $.post('ws/producto_taxonomiap.php', {op: 'add',id:null,id_producto:id_producto,id_taxonomiap:".$car["id"].",valor:".str_replace(" ", "_",strtolower($car["nombre"])).",estado_fila:estado_fila}, function(data) {
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

                    var unidad = $('#unidad').val();

                    var precio_compra = $('#precio_compra').val();

                    var precio_venta = $('#precio_venta').val();

                    var incluye_impuesto = $('#incluye_impuesto').find('option:selected').val();

                    var estado_fila = $('#estado_fila').val();

                    var icbper = $('#icbper').is(":checked");
                    if(icbper){
                        bolsa = 1;
                    }else{
                        bolsa = 0;
                    }

                    $.post('ws/producto.php', {op: 'mod',id:id,nombre:nombre,unidad:unidad,precio_compra:precio_compra,precio_venta:precio_venta,incluye_impuesto:incluye_impuesto,estado_fila:estado_fila, icbper:bolsa}, function(data) {
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
                        var id_producto = id;

                        var estado_fila = 1;

                        var categoria = $("#categoria").val();
                        var tipo = $("#tipo").val();
                        var sunat = $('#sunat-input').val();

                        $.post('ws/producto_taxonomiap.php', {op: 'mod1',id_producto:id_producto,id_taxonomiap:2,valor:categoria,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');

                        $.post('ws/producto_taxonomiap.php', {op: 'mod1',id_producto:id_producto,id_taxonomiap:3,valor:tipo,estado_fila:estado_fila}, function(data) {
                            if(data === 0){
                                count = count + 1;
                            }
                            else{
                                count = count + 1;
                            }
                        }, 'json');

                        $.post('ws/producto_taxonomiap.php', {op: 'mod1_sunat',id_producto:id_producto,id_taxonomiap:-1,valor:sunat,estado_fila:estado_fila}, function(data) {
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
                                var ".str_replace(" ", "_",strtolower($car["nombre"]))." = $('#".str_replace(" ", "_",strtolower($car["nombre"]))."').val();
                                $.post('ws/producto_taxonomiap.php', {op: 'mod1',id_producto:id_producto,id_taxonomiap:".$car["id"].",valor:".str_replace(" ", "_",strtolower($car["nombre"])).",estado_fila:estado_fila}, function(data) {
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
                                // tbl.ajax.reload()
                                location.reload();
                            }
                        },500);


                    }
                    }, 'json');
                }
                function cls(){
                        $('#id').val('');
                        $('#nombre').val('');
                        $('#unidad').val('NIU');
                        $('#precio_compra').val('');
                        $('#precio_venta').val('');
                        $('#incluye_impuesto ').val('1');
                        $('#estado_fila').val('');

                }
                function sel(id){
                   // cls();
                    $.post('ws/producto.php', {op: 'get', id: id}, function(data) {
                    if(data !== 0){
                        $('#id').val(data.id);
                        $('#nombre').val(data.nombre);
                        $('#unidad option[value="'+data.unidad+'"]').attr('selected', true);
                        $('#precio_compra').val(data.precio_compra);
                        $('#precio_venta').val(data.precio_venta);
                        $('#incluye_impuesto option[value="'+data.incluye_impuesto+'"]').attr('selected', true);
                        $('#estado_fila').val(data.estado_fila);

                        //Ahora las taxonomias

                        $.post('ws/producto_taxonomiap.php', {op: 'getbytax', id_producto:id, id_taxonomiap: 2}, function(data0) {
                        if(data0 !== 0){
                            $("#categoria").val(data0.valor);
                            carga_tipo();
                        }
                        }, 'json');

                        $.post('ws/producto_taxonomiap.php', {op: 'getbytax', id_producto:id, id_taxonomiap: 3}, function(data1) {
                        if(data1 !== 0){

                            $("#tipo").val(data1.valor);
                        }
                        }, 'json');

                       /* $.post('ws/producto.php', {op: 'getBolsa', id_producto:id}, function(data1) {
                        if(data1 !== 0){

                            $('#icbper').prop('checked', true);
                        }
                        }, 'json');*/

                        $.post('ws/producto_taxonomiap.php', {op: 'getbytax', id_producto:id, id_taxonomiap: -1}, function(data2) {
                        $("#sunat-input").tokenInput("destroy");
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
                                $.post('ws/producto_taxonomiap.php', {op: 'getbytax', id_producto:id, id_taxonomiap: ".$car["id"]."}, function(data1) {
                                if(data1 !== 0){
                                    $('#".str_replace(" ", "_",strtolower($car["nombre"]))."').val(data1.valor);
                                }else{
                                    $('#".str_replace(" ", "_",strtolower($car["nombre"]))."').val('');
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
                        $.post('ws/producto.php', {op: 'del', id: id}, function (data) {
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

                    var search = localStorage.getItem('search') ? localStorage.getItem('search') : '';

                    var tbl = $('#tb').DataTable({
                    responsive: true,
                   // dom: 'Bfrtip',
                   
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
                    order: [0, 'asc'],
                    buttons: ['copyHtml5','excelHtml5','csvHtml5',{extend: 'pdf', orientation: 'landscape'}],  
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                    },
                        "processing": true,
                        "serverSide": true,
                        "ajax":{
                            url: "ws/producto2.php",
                            type: "post"
                        },
                        createdRow: function( row, data, dataIndex ) {
                        },
                        fnRowCallback: function( nRow, aData, iDisplayIndex ) {
                        },
                        initComplete: function(settings, json) {
                        }
                    });

                   /* tbl.on( 'search.dt', function () {
                       localStorage.setItem("search", tbl.search());
                    } );*/
                  //window.tbl=tbl

                    //$("#progress").hide();
                });
 

                function save(){
                    var vid = $('#id').val();
                    var codigo = $('#codigo_barra').val();

                       if(vid === '0'){
                             insert();
                     }
                     else{
                             update();
                         }

                  /* if(codigo != ""){

                       $.post('ws/producto.php', {op: 'codigovalidar', codigo: codigo}, 
                        function (data) {
                           
                             if (data === 0) {
                                if(vid === '0'){
                                    insert();
                                }
                                else{
                                     update();
                                }
                             }
                             else {
                                alert("Este codigo de barra ya pertenece a un producto");
                            }
                         }, 'json');
                    }else{
                         
                        if(vid === '0'){
                            insert();
                        }
                        else{
                            update();
                        }

               
                        
                   }    */           
}

                function finalizar(){
                    $('#frmall').reset();
                    location.reload();
                }

                function img(id){
                    $("#muestra").attr("src","recursos/uploads/productos/"+id+".png");
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
                        url: 'ws/producto.php',
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