<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once('../nucleo/taxonomiap.php');

$objtaxonomiap = new taxonomiap();

if (isset($_GET)) {
    // Obtengo solo categoria y taxonomias nuevas
    $res = $objtaxonomiap->whereWithLogicOperator([
        ["id", "=", 2, "or"],
        ["id", ">", 5, "and"],
        ["tipo_valor", "=", 2, null]
    ]);

    if (is_array($res)) {
        // echo json_encode($res);
        foreach ($res as &$act) {
            if ($act['padre'] > 0) {
                $act['padre'] = $objtaxonomiap->searchDB($act['padre'], 'id', 1);
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
