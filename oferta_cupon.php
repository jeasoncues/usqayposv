<?php

$titulo_pagina = 'Cupones en la oferta #'.$_GET['id'];
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
include_once('nucleo/include/MasterConexion.php');

$masterCon = new MasterConexion();
$con = $masterCon->getConnection();
$con->set_charset("utf8");

//$id_usuario = mysqli_real_escape_string($con, $_GET['id']);

//$res = $con->query("select * from usuario where id = {$id_usuario}");
//$usuario = $res->fetch_assoc();

?>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="ofertas.php" class="btn btn-sm btn-default">Volver</a>
        </div>
        <div class="panel body">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Serie</th>
                    <th>Utilizado</th>
                </tr>
                </thead>
                <tbody>
                <?php


                $cupones = $masterCon->consulta_matriz("SELECT * FROM cupon_oferta WHERE id_oferta = {$_GET['id']}");

                if (is_array($cupones)):
                foreach ($cupones as $k=> $cupon): ?>
                    <tr>
                        <td><?= $k+1; ?></td>
                        <td><?= $cupon['numero']; ?></td>
                        <td><?= $cupon['usado']?'SI':'NO'; ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</form>
<hr/>
<?php
$nombre_tabla = "";
require_once('recursos/componentes/footer.php');
?>
