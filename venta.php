<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
include_once('nucleo/venta.php');
$obj = new venta();
$objs = $obj->consulta_matriz("Select * from venta where subtotal is NULL AND estado_fila = 1 AND id_caja = '{$_COOKIE["id_caja"]}'");
if(!is_array($objs)){
//    header('Location: dashboard_sistema.php');
    header('Location: pantalla_teclado.php');
}

$titulo_pagina = 'Venta';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
</form>
<?php


include_once('nucleo/turno.php');

include_once('nucleo/usuario.php');

include_once('nucleo/caja.php');

include_once('nucleo/cliente.php');
?>
<div class="panel">
    <div class="panel-body">
    <div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha y Hora</th>
                <th>Fecha Cierre</th>
                <th>Turno</th>
                <th>Usuario</th>
                <th>Caja</th>
                <th>Estado</th>
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
                        <td style="text-align: center;"><?php
                        if ($o['estado_fila']==1) { echo "<span class='label label-success'>Por Retomar</span>" ;}
                        else{echo "<span class='label label-danger'>Descartada</span>";}
                        ?></td>
                        <td>
<!--                            <a href='dashboard_sistema.php?id=--><?php //echo $o["id"];?><!--'><i class="fa fa-reply-all" aria-hidden="true"></i> Retomar</a>-->
                            <?php if ($o['estado_fila']==1) {?>
                                <a href='pantalla_teclado.php?id=<?php echo $o["id"];?>'><i class="fa fa-reply-all" aria-hidden="true"></i> Retomar</a>
                            <?php } ?>

                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
    </div>
</div>

            <?php
            $nombre_tabla = 'venta';
            require_once('recursos/componentes/footer.php');
            ?>