<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Valores Caracteristica';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>

<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Característica</label>
    <label class='form-control' id='txt_id_taxonomiap'>...</label>
    <input type='hidden' name='id_taxonomiap' id='id_taxonomiap' value=''/>
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
include_once('nucleo/taxonomiap_valor.php');
$obj = new taxonomiap_valor();
$objs = null;
if(isset($_GET["id"])){
    $objs = $obj->consulta_matriz("Select * from taxonomiap_valor where id_taxonomiap = '".$_GET["id"]."' and estado_fila = 1");
   
}
include_once('nucleo/taxonomiap.php');
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Atributo</th>
                <th>Valor</th>
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
                        $objtaxonomiap = new taxonomiap();
                        $objtaxonomiap->setVar('id', $o['id_taxonomiap']);
                        $objtaxonomiap->getDB();
                        echo $objtaxonomiap->getVar($gl_taxonomiap_valor_id_taxonomiap);
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
            $nombre_tabla = 'valoresp';
            require_once('recursos/componentes/footer.php');
            if(isset($_GET["id"])):?>
            <script>
                $(document).ready(function() {
                    sel_id_taxonomiap(<?php echo $_GET["id"];?>);
                });
            </script>
                    
            <?php endif;?>
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