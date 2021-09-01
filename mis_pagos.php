<?php

if (!$_COOKIE['id_usuario']) {
    header('Location: index.php');
}

$titulo_pagina = 'Mis pagos';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
include_once('nucleo/include/MasterConexion.php');

$con1 = new MasterConexion();

$empresa = $con1->consulta_arreglo("select * from configuracion limit 1");
$idEmpresa = $empresa['id_empresa'];

$con = new MasterConexion("usqay-cloud.com", "c3l", "coche", "usqay_reportes");
//$con = new MasterConexion("localhost", "root", "", "usqay_reportes");
$voucherPath = "recursos/uploads/vouchers/";
$remotePath = "static/vouchers/";
$usqayDomain = "usqay-cloud.com";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = $con->getConnection();
    $idPago = $conn->real_escape_string($_POST['pago']);
    if (sizeof($_FILES) > 0) {
        $tmp = $_FILES['img_voucher']['tmp_name'];
        $name = $_FILES['img_voucher']['name'];
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif'], true)) {
            $name = "{$idPago}.{$ext}";
            $path = "{$voucherPath}{$name}";

            move_uploaded_file($tmp, $path);

            //envio por ftp
            $conn_id = ftp_connect($usqayDomain) or die('Error de conexion FTP');
            $login = ftp_login($conn_id, "katsu@usqay-cloud.com", "12345") or die("Error de login");
            if (ftp_put($conn_id, "$remotePath{$name}", $path, FTP_BINARY)) {

                $ch = curl_init('http://usqay-cloud.com/admin/email_handler.php');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('id' => $idEmpresa));
//                $ch = curl_setopt($ch, CURLOPT_USERPWD, 'username:password');

                $result = curl_exec($ch);
                if ($result === FALSE) {
                    die(curl_error($ch));
                }

                //actualizacion en la base de datos
                $stmt = $conn->query("UPDATE pago_empresa SET voucher = '{$name}', solicitado = 1  WHERE id = {$idPago}");

            }
            ftp_close($conn_id);

        }
    }
}

$pagos = $con->consulta_matriz("SELECT * FROM pago_empresa WHERE id_empresa = {$idEmpresa} AND solicitado = TRUE");
$fechas = $con->consulta_matriz("SELECT * FROM pago_empresa WHERE id_empresa = {$idEmpresa} AND pagado = FALSE AND solicitado = FALSE");

?>
</form>
<?php if (is_array($fechas)): ?>
    <div class="col-md-12">
        <form action="" method="post" enctype="multipart/form-data" class="box box-primary">
            <div class="box-body">
                <div class="col-md-3">
                    <label for="">Voucher</label>
                    <input type="file" name="img_voucher" class="form-control" accept="image/*">
                </div>
                <div class="col-md-3">
                    <label for="pago">Fecha</label>
                    <select name="pago" id="pago" class="form-control">
                        <?php foreach ($fechas as $fecha): ?>
                            <option value="<?= $fecha['id'] ?>"><?= $fecha['fecha'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-success pull-right">Enviar</button>
            </div>
        </form>
    </div>
<?php endif; ?>
<div class="col-md-12">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Mis pagos</h3>
        </div>
        <div class="box-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Fecha de pago</th>
                    <th>Monto</th>
                    <th>Confirmado</th>
                    <th>Voucher</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (is_array($pagos)):
                    foreach ($pagos as $pago):
                        ?>
                        <tr>
                            <td><?= $pago['fecha'] ?></td>
                            <td><?= $pago['monto'] ?></td>
                            <td><?= $pago['pagado'] == 1 ? 'SI' : 'NO' ?></td>
                            <td>
                                <?php
                                if ($pago['solicitado'] == 1):?>
                                    <button class="btn btn-default btn-sm btnVoucher"
                                            data-img-path="<?= $remotePath.$pago['voucher'] ?>">
                                        Ver voucher
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modalVoucher">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Voucher de pago</h4>
            </div>
            <div class="modal-body">
                <img src="" alt="" id="imgVoucher" class="img-responsive">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<hr/>
<?php
$nombre_tabla = "";
require_once('recursos/componentes/footer.php');
?>
<script>
    $(function () {
        $('.btnVoucher').click(function () {
            var path = $(this).attr('data-img-path');
            $('#imgVoucher').attr('src', 'http://usqay-cloud.com/' + path).on('load', null, function () {
                $('#modalVoucher').modal('show');
            });
        });
    });
</script>
