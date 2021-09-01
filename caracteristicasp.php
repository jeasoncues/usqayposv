<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Caracteristicas Producto';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>
<div class='control-group col-md-4'>
    <label>Característica</label>
    <input class='form-control' placeholder='Nombre' id='nombre' name='nombre' />
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
include_once('nucleo/taxonomiap.php');
$obj = new taxonomiap();
$objs = $obj->consulta_matriz("Select * from taxonomiap where estado_fila = 1 AND id > 3");
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Característica</th>
                <th>Valores</th>
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
                        <td>
                            <a href='valoresp.php?id=<?php echo $o["id"];?>'><i class="fa fa-eye" aria-hidden="true"></i></a>
                        </td>
                        <td>
                            <a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <br/>
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?> 
            <?php
            $nombre_tabla = 'caracteristicasp';
            require_once('recursos/componentes/footer.php');
            ?>