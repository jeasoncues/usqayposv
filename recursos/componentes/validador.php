<?php
session_start();
/**
 * Created by PhpStorm.
 * User: Eliu
 * Date: 10/25/2017
 * Time: 15:04
 */

/*require_once './nucleo/include/MasterConexion.php';
$conn = new MasterConexion();

$id_empresa = $conn->consulta_arreglo("SELECT * FROM configuracion")['id_empresa'];

//$con2 = new MasterConexion('localhost', 'root', '', 'usqay_reportes');
$con2 = new MasterConexion('usqay-cloud.com', 'c3l', 'coche', 'usqay_reportes');

$empresa = $con2->consulta_arreglo("SELECT e.id,
             pe.fecha AS prox_pago, 
             pe.monto AS monto, 
             IFNULL(pe.pagado, FALSE) AS pagado,
             e.activo AS activo
            FROM empresa e
            LEFT JOIN usuario u ON e.id_usuario = u.id
            LEFT JOIN pago_empresa pe ON pe.id = (SELECT id FROM pago_empresa WHERE id_empresa = e.id AND pagado = FALSE ORDER BY fecha ASC LIMIT 1)
            WHERE e.id = {$id_empresa}
            GROUP BY e.id");

if ($empresa['prox_pago']) {
    $now = date_create(date('Y-m-d'));
    date_create();
    $prox = date_create($empresa['prox_pago']);
    $resta = date_diff($now, $prox)->format("%a");
    $tipo = '';
    $link = '<a href="mis_pagos.php">Consultar mis pagos</a>';
    if ($now < $prox) {
        if ($resta > 5) {
            $tipo = 'info';
        } else {
            $tipo = 'warning';
        }

        $resta = "Su plazo de pago vence en {$resta} días {$link}";
    } else {
        if ($resta == 0) {
            $resta = "Su plazo de pago vence hoy {$link}";
            $tipo = 'warning';
        } else {
            $tipo = 'danger';

            if ($empresa['activo'] == false) {
                $resta = "Su servicio ha sido desabilitado y no podrá usarlo hasta que regularice sus pagos {$link}";
                if (strpos($_SERVER['SCRIPT_NAME'], 'mis_pagos.php') === false) {
                    header('Location: ./inicio.php');
                }
            } else {
                $resta = "Su fecha de pago ha vencido, por favor regularice sus pagos {$link}";
            }
        }
    }

    $_SESSION['mensaje']['tipo'] = $tipo;
    $_SESSION['mensaje']['texto'] = $resta;

} else {
    session_destroy();
}
*/
