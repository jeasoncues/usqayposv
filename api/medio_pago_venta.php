<?php

require_once('../nucleo/medio_pago_venta.php');

/** Cors */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

$objmedio_pago_venta = new medio_pago_venta();

$res = $objmedio_pago_venta->listDB();

if (is_array($res)) {
    echo json_encode($res);
} else {
    echo json_encode(0);
}
