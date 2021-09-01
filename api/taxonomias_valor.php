<?php
header("Access-Control-Allow-Origin: *");

require_once('../nucleo/taxonomias_valor.php');
$objtaxonomias_valor = new taxonomias_valor();

require_once('../nucleo/taxonomias.php');
$objtaxonomias = new taxonomias();

if (isset($_GET['padre'])) {
    $id = $_GET['padre'];
    switch ($_GET['q']) {
        case 'valores':
            $res = $objtaxonomias_valor->where(['id_taxonomias', '=', $id]);
            echo json_encode($res);
            break;

        default:
            # code...
            break;
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    switch ($_GET['q']) {
        case 'services':
            $valor = $_GET['valor'];
            $limit = 28;
            $page = $_GET['page'];
            $offset = $limit * $page;
            // $almacen = $_GET['almacen'] ? $_GET['almacen'] : null;

            $query = "SELECT servicio.id,servicio.nombre, servicio.precio_venta, servicio.incluye_impuesto, servicio.estado_fila 
                        FROM servicio_taxonimias 
                        INNER JOIN servicio on servicio_taxonimias.id_servicio = servicio.id
                        WHERE servicio_taxonimias.id_taxonomias = {$id} and servicio_taxonimias.valor = '{$valor}' and servicio_taxonimias.estado_fila = 1  
                        ORDER BY servicio.nombre LIMIT {$limit} OFFSET {$offset}";

            $res = $objtaxonomias_valor->consulta_matriz($query);
            echo json_encode($res);
            break;

        default:
            # code...
            break;
    }
}
