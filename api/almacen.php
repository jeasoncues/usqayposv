<?php
header("Access-Control-Allow-Origin: *");
require_once('../nucleo/almacen.php');
$objalmacen = new almacen();

$res = $objalmacen->listDB();

if (is_array($res)) {
    echo json_encode($res);
} else {
    echo json_encode(0);
}
