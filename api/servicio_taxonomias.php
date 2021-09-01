<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once('../nucleo/servicio.php');
$objservicio = new servicio();

if (isset($_GET['q'])) {
    $q = $_GET['q'];
    $limit = 14;
    $page = $_GET['page'];
    $offset = $limit * $page;

    $query = "SELECT id, nombre, precio_venta, incluye_impuesto, estado_fila  
    FROM servicio 
    WHERE estado_fila = 1 AND nombre LIKE '%{$q}%'
    ORDER BY nombre LIMIT {$limit} OFFSET {$offset}";

    $res = $objservicio->consulta_matriz($query);

    echo json_encode($res);
}
