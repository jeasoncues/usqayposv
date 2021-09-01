<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Movimiento Entre Almacenes';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
    <input type='hidden' id='id' name='id' value='0'/>
    <input type='hidden' name='id_usuario' id='id_usuario' value='<?php echo $_COOKIE["id_usuario"] ?>'/>
    <div class='control-group col-md-4'>
        <label>Fecha Realizacion</label>
        <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_realizada' name='fecha_realizada'/>
    </div>
    <div class='control-group col-md-4'>
        <label>Numero Guia Salida</label>
        <input class='form-control' placeholder='Numero Guia Salida' id='numero_guia_salida' name='numero_guia_salida'
               required/>
    </div>
    <div class='control-group col-md-4'>
        <label>Numero Guia Entrada</label>
        <input class='form-control' placeholder='Numero Guia Entrada' id='numero_guia_entrada'
               name='numero_guia_entrada' required/>
    </div>
    <input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
    <div class='control-group col-md-4' id="panel_save">
        <p></p>
        <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
        <button type='reset' class='btn'>Limpiar</button>
    </div>
    </form>
    <hr/>
<?php
include_once('nucleo/guia_producto.php');
$obj = new guia_producto();
$objs = $obj->consulta_matriz("Select * from movimiento_almacenes where estado_fila = 1");

include_once('nucleo/usuario.php');
?>
    <div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
    <thead>
    <tr>
        <th>Id</th>
        <th>Usuario</th>
        <th>Fecha Registro</th>
        <th>Fecha Realizada</th>
        <th>Guia Salida</th>
        <th>Guia Entrada</th>
        <th>Productos</th>
        <th>OPC</th>
    </tr>
    </thead>
    <tbody>
<?php
//Guia Entrada
$objentrada = new guia_producto();
//$objentrada->setId($o["entrada"]);
//$objentrada->getId();

//Guia Salida
$objsalida = new guia_producto();
//$objsalida->setId($o["salida"]);
//$objsalida->getDB();

$objusuario = new usuario();
//$objusuario->setVar('id', $objentrada->getIdUsuario());
//$objusuario->getDB();
if (is_array($objs)):
    foreach ($objs as $o):
        $entrada = $objentrada->consulta_arreglo("SELECT * FROM guia_producto WHERE id = {$o['entrada']}");
        $salida = $objsalida->consulta_arreglo("SELECT * FROM guia_producto WHERE id = {$o['salida']}");
        $usuario = $objusuario->consulta_arreglo("SELECT * FROM usuario WHERE id = {$entrada['id_usuario']}");
        ?>
        <tr>
            <td><?php echo $o['id']; ?></td>
            <td><?php echo $usuario['nombres_y_apellidos']; ?></td>
            <td>
                <?php
                //echo $objentrada->getFechaRegistro();
                echo $entrada['fecha_registro'];
                ?>
            </td>
            <td><?php
                //echo $objentrada->getFechaRealizada();
                echo $entrada['fecha_realizada'];
                ?>
            </td>
            <td><?php
                //echo $objsalida->getNumeroGuia();
                echo $salida['numero_guia'];
                ?>
            </td>
            <td><?php
                //echo $objentrada->getNumeroGuia();
                echo $entrada['numero_guia'];
                ?>
            </td>
            <td>
                <a href='movimiento_productos.php?id=<?php echo $o["id"]; ?>'><i class="fa fa-eye"
                                                                                 aria-hidden="true"></i></a>
            </td>
            <td>
                <a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o"
                                                                      aria-hidden="true"></i></a>
                <br/>
                <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
            </td>
        </tr>
        <?php
    endforeach;
endif;
?>
<?php
$nombre_tabla = 'movimiento_almacenes';
require_once('recursos/componentes/footer.php');
?>
<script src="recursos/js/notify.js"></script>