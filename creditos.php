<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
include_once('nucleo/venta.php');
$obj = new venta();
$objs = $obj->consulta_matriz("Select * from venta where tipo_comprobante = -1 AND estado_fila = 1");
if(!is_array($objs)){
//    header('Location: dashboard_sistema.php');
    header('Location: pantalla_teclado.php');
}

$titulo_pagina = 'Venta';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>

</form>
<hr/>
<?php


include_once('nucleo/turno.php');

include_once('nucleo/usuario.php');

include_once('nucleo/caja.php');

include_once('nucleo/cliente.php');
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha y Hora</th>
                <th>Fecha Cierre</th>
                <th>Turno</th>
                <th>Usuario</th>
                <th>Caja</th>
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
                        <td><?php

                            $objcliente = new cliente();
                            $objcliente->setVar('id', $o['id_cliente']);
                            $objcliente->getDB();
                            echo $objcliente->getNombre();?>
                                
                            </td>
                        <td><?php echo $o['fecha_hora']; ?></td>
                        <td><?php echo $o['fecha_cierre']; ?></td>
                        <td>
                        <?php
                        $objturno = new turno();
                        $objturno->setVar('id', $o['id_turno']);
                        $objturno->getDB();
                        echo $objturno->getNombre();
                        ?></td>
                        <td>
                        <?php
                        $objusuario = new usuario();
                        $objusuario->setVar('id', $o['id_usuario']);
                        $objusuario->getDB();
                        echo $objusuario->getNombresYApellidos();
                        ?></td>
                        <td>
                        <?php
                        $objcaja = new caja();
                        $objcaja->setVar('id', $o['id_caja']);
                        $objcaja->getDB();
                        echo $objcaja->getNombre();
                        ?></td>
                        <td>
                            <a href='pantalla_teclado.php?id=<?php echo $o["id"];?>'><i class="fa fa-reply-all" aria-hidden="true"></i> Retomar</a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'venta';
            require_once('recursos/componentes/footer.php');
            ?>    