<?php

require_once('../nucleo/guia_movimiento.php');
$objguia_movimiento = new guia_movimiento();

require_once('../nucleo/movimiento_producto.php');
$objmovimiento_producto = new movimiento_producto();

require_once('../nucleo/guia_producto.php');
$objguia_producto = new guia_producto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objguia_movimiento->setVar('id', $_POST['id']);
            $objguia_movimiento->setVar('id_movimiento_producto', $_POST['id_movimiento_producto']);
            $objguia_movimiento->setVar('id_guia_producto', $_POST['id_guia_producto']);
            $objguia_movimiento->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objguia_movimiento->insertDB());
            break;

        case 'mod':
            $objguia_movimiento->setVar('id', $_POST['id']);
            $objguia_movimiento->setVar('id_movimiento_producto', $_POST['id_movimiento_producto']);
            $objguia_movimiento->setVar('id_guia_producto', $_POST['id_guia_producto']);
            $objguia_movimiento->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objguia_movimiento->updateDB());
            break;

        case 'del':
            $objguia_movimiento->setVar('id', $_POST['id']);
            echo json_encode($objguia_movimiento->deleteDB());
            break;

        case 'get':
            $res = $objguia_movimiento->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_movimiento_producto'] = $objmovimiento_producto->searchDB($res[0]['id_movimiento_producto'], 'id', 1);
                $res[0]['id_movimiento_producto'] = $res[0]['id_movimiento_producto'][0];
                $res[0]['id_guia_producto'] = $objguia_producto->searchDB($res[0]['id_guia_producto'], 'id', 1);
                $res[0]['id_guia_producto'] = $res[0]['id_guia_producto'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objguia_movimiento->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_movimiento_producto'] = $objmovimiento_producto->searchDB($act['id_movimiento_producto'], 'id', 1);
                    $act['id_movimiento_producto'] = $act['id_movimiento_producto'][0];
                    $act['id_guia_producto'] = $objguia_producto->searchDB($act['id_guia_producto'], 'id', 1);
                    $act['id_guia_producto'] = $act['id_guia_producto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objguia_movimiento->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_movimiento_producto'] = $objmovimiento_producto->searchDB($act['id_movimiento_producto'], 'id', 1);
                    $act['id_movimiento_producto'] = $act['id_movimiento_producto'][0];
                    $act['id_guia_producto'] = $objguia_producto->searchDB($act['id_guia_producto'], 'id', 1);
                    $act['id_guia_producto'] = $act['id_guia_producto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>