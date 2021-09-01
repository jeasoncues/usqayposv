<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Configuracion impresion';
$titulo_sistema = 'Katsu';

require_once 'nucleo/include/MasterConexion.php';
$objcon = new MasterConexion();
$impresoras = $objcon->consulta_matriz("SELECT * FROM impresoras");

$row = null;
if (!empty($_GET['id'])){
    $row = $objcon->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE id = {$_GET['id']}");
}

if ($_SERVER['REQUEST_METHOD']=='POST'){
    

 
    switch ($_POST['option']){
        case 'agregar':{
            $name_impr=$_POST['impresora'];
            $new_nam_imp = str_replace("\\","\\\\",$name_impr);
            $objcon->consulta_simple("insert into configuracion_impresion (caja, opcion, impresora) VALUES ({$_COOKIE['id_caja']}, '{$_POST['opcion']}', '{$new_nam_imp}')");

            header('Location: configuracion_impresion.php');
            break;
        }
        case 'actualizar':{
            $name_impr=$_POST['impresora'];
            $new_nam_imp = str_replace("\\","\\\\",$name_impr);
            $objcon->consulta_simple("update configuracion_impresion set opcion='{$_POST['opcion']}', impresora='{$new_nam_imp}' where id = {$_GET['id']}");

           header('Location: configuracion_impresion.php');
            break;
        }
        case 'eliminar':{
            $name_impr=$_POST['impresora'];
            $new_nam_imp = str_replace("\\","\\\\",$name_impr);
            $objcon->consulta_simple("delete from  configuracion_impresion where id = {$_POST['id']}");
            header('Location: configuracion_impresion.php');
            break;
        }
        case 'margen':{
           // $name_impr=$_POST['impresora'];
          //  $new_nam_imp = str_replace("\\","\\\\",$name_impr);
            $name_impr2=$_POST['nombre_impresora'];
           $new_nam_imp2 = str_replace("\\","\\\\",$name_impr2);
            if (empty($_POST['id_margen'])){
                $objcon->consulta_simple("insert into margenes_impresion (nombre_impresora, margen) VALUES ('{$new_nam_imp2}', '{$_POST['margen']}')");
            }else{
                $objcon->consulta_simple("update margenes_impresion set margen = '{$_POST['margen']}' where nombre_impresora = '{$new_nam_imp2}'");
            }
            header('Location: configuracion_impresion.php');
            break;
        }
    }
}


$configs = $objcon->consulta_matriz("SELECT * FROM configuracion_impresion");

require_once('recursos/componentes/header.php');
?>
</form>
<form action="" method="post">
<div class="col-md-3">
    <label for="">Impresoras</label>
    <select name="impresora" id="" class="form-control">
        <?php 
        if(is_array($impresoras)):
        foreach ($impresoras as $impresora): ?>
            <option value="<?php echo $impresora['nombre'] ?>" <?php echo $row['impresora']===$impresora['nombre']?'selected':'' ?>><?php echo $impresora['nombre']; ?></option>
        <?php endforeach; endif;?>
    </select>
</div>
<div class="col-md-3">
    <label for="">Documento</label>
    <select name="opcion" id="" class="form-control">
        <option value="BOL" <?php echo $row['opcion']==='BOL'?'selected':'' ?>>Boleta</option>
        <option value="FAC" <?php echo $row['opcion']==='FAC'?'selected':'' ?>>Factura</option>
        <option value="NOT" <?php echo $row['opcion']==='NOT'?'selected':'' ?>>Nota de Venta</option>
        <option value="CIE" <?php echo $row['opcion']==='CIE'?'selected':'' ?>>Cierre</option>
        <option value="PAG" <?php echo $row['opcion']==='PAG'?'selected':'' ?>>Pago</option>
    </select>
</div>
<div>
    <input type="hidden" name="option" value="<?php echo is_null($row)? 'agregar':'actualizar' ?>">
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
</form>
<hr/>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Configuraciones</h3>
    </div>
    <div class="panel-body">
        <table class="table">
            <tr>
                <th>Caja</th>
                <th>Opcion</th>
                <th>Impresora</th>
                <th></th>
            </tr>
            
            <?php 
            if(is_array($configs)):
            foreach ($configs as $config):?>
                <tr>
                    <td><?php echo $config['caja'];?></td>
                    <td>
                        <?php
                        $label = '';
                        switch ($config['opcion']){
                            case 'NOT': $label = 'Nota de venta';break;
                            case 'BOL': $label = 'Boleta';break;
                            case 'FAC': $label = 'Factura';break;
                            case 'CIE': $label = 'Cierre';break;
                            case 'PAG': $label = 'Pagos';break;
                        }
                        echo $label;
                        ?>
                    </td>
                    <td>
                        <?php
                        $margen = $objcon->consulta_arreglo("SELECT * FROM margenes_impresion WHERE nombre_impresora = '{$config['impresora']}'");
                        ?>
                        <a href="#" class="modal-impresora" data-margen="<?php echo $margen['margen'];?>" data-id="<?php echo $margen['id'];?>" data-impresora="<?php echo $config['impresora'];?>" data-toggle="modal">
                            <?php echo $config['impresora']; ?>
                        </a>
                    </td>
                    <td>
                        <a href="?id=<?php echo $config['id']; ?>" class=" btn btn-xs btn-primary">Editar</a>
                        <form action="" method="post" style="display: inline-block">
                            <input type="hidden" name="option" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo $config['id'];?>">
                            <button type="submit" class="btn btn-danger btn-xs">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif;?>
        </table>
    </div>
</div>
<div class="modal" id="modal">
    <div class="modal-dialog">
        <form class="modal-content" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Porcentaje de ajuste</h4>
            </div>
            <div class="modal-body">
                <label for="" id="label-impresora"></label>
                <input type="number" name="margen" id="txtMargen" class="form-control" min="0" value="0" step="0.01">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <input type="hidden" name="option" value="margen">
                <input type="hidden" name="id_margen" id="id_margen">
                <input type="hidden" name="nombre_impresora" id="nombre_impresora">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class='contenedor-tabla' style="display: none !important;">
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>

        </thead>
        <tbody>
            <?php
            $nombre_tabla = 'configuracion';
            require_once('recursos/componentes/footer.php');
            ?>

            <script>
                $(function () {
                    $('.modal-impresora').click(function (e) {
                        e.preventDefault();

                        var impresora = $(this).data('impresora');
                        var margen = $(this).data('margen');
                        var id = $(this).data('id');

                        $('#label-impresora').text(impresora);
                        $('#nombre_impresora').val(impresora);
                        $('#txtMargen').val(margen);
                        $('#id_margen').val(id);

                        $('#modal').modal('show');
                    });
                });
            </script>
