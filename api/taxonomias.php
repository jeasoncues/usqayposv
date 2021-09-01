<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once('../nucleo/taxonomias.php');
$objtaxonomias = new taxonomias();

if (isset($_GET)) {
    // Obtengo solo categoria y taxonomias nuevas
    $res = $objtaxonomias->whereWithLogicOperator([
        ["id", "=", 2, "and"],
        ["id", "<>", -1, "or"],
        ["id", ">", 3, "and"],
        ["tipo_valor", "=", 2, null]
    ]);

    if (is_array($res)) {
        foreach ($res as &$act) {
            if ($act['padre'] > 0) {
                $act['padre'] = $objtaxonomias->searchDB($act['padre'], 'id', 1);
                $act['padre'] = $act['padre'][0];
            }            

            switch (intval($act['tipo_valor'])) {
                case 1:
                    $act['tipo_valor'] = "Abierto";
                    break;

                case 2:
                    $act['tipo_valor'] = "Rango";
                    break;
            }
        }
        echo json_encode($res);
    } else {
        echo json_encode(["res" => []]);
    }
}
