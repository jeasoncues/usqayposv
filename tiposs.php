<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Tipos Servicio';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Categoría</label>
    <label class='form-control' id='categoria'><?php echo urldecode($_GET["categoria"]);?></label>
    <input type='hidden' name='padre' id='padre' value='<?php echo $_GET["id"];?>'/>
</div>

<div class='control-group col-md-4'>
    <label>Valor</label>
    <input class='form-control' placeholder='Valor' id='valor' name='valor' />
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
include_once('nucleo/taxonomias_valor.php');
$obj = new taxonomias_valor();
$objs = $obj->consulta_matriz("Select * from taxonomias_valor where id_taxonomias = '3'  and padre = '".$_GET["id"]."' and estado_fila = 1");

include_once('nucleo/taxonomias.php');
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Categoría</th>
                <th>Tipo</th>
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
                        $objtaxonomias_valor = new taxonomias_valor();
                        $objtaxonomias_valor->setVar('id', $o['padre']);
                        $objtaxonomias_valor->getDB();
                        echo $objtaxonomias_valor->getValor();
                        ?></td>
                        <td><?php echo $o['valor']; ?></td>
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
            $nombre_tabla = 'tiposs';
            require_once('recursos/componentes/footer.php');
            ?>
            <!--Inicio Modal-->
            <div class='modal fade' id='modal_imagen' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
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
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="upload_image()">Subir Imagen</button>
                    </div>
                </div>
            </div>
            </div>
            <!--Fin Modal-->