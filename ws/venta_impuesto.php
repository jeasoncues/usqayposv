<?php

require_once('../nucleo/venta_impuesto.php');
$objventa_impuesto = new venta_impuesto();

require_once('../nucleo/venta.php');
$objventa = new venta();

require_once('../nucleo/impuesto.php');
$objimpuesto = new impuesto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objventa_impuesto->setVar('id_venta', $_POST['id_venta']);
            $objventa_impuesto->setVar('id_impuesto', $_POST['id_impuesto']);
            $objventa_impuesto->setVar('monto', $_POST['monto']);
            $objventa_impuesto->setVar('estado_fila',"1");
            echo json_encode($objventa_impuesto->insertDB());
            break;

        case 'mod':
            $objventa_impuesto->setVar('id', $_POST['id']);
            $objventa_impuesto->setVar('id_venta', $_POST['id_venta']);
            $objventa_impuesto->setVar('id_impuesto', $_POST['id_impuesto']);
            $objventa_impuesto->setVar('monto', $_POST['monto']);
            $objventa_impuesto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objventa_impuesto->updateDB());
            break;

        case 'del':
            $objventa_impuesto->setVar('id', $_POST['id']);
            echo json_encode($objventa_impuesto->deleteDB());
            break;

        case 'get':
            $res = $objventa_impuesto->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_venta'] = $objventa->searchDB($res[0]['id_venta'], 'id', 1);
                $res[0]['id_venta'] = $res[0]['id_venta'][0];
                $res[0]['id_impuesto'] = $objimpuesto->searchDB($res[0]['id_impuesto'], 'id', 1);
                $res[0]['id_impuesto'] = $res[0]['id_impuesto'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objventa_impuesto->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_impuesto'] = $objimpuesto->searchDB($act['id_impuesto'], 'id', 1);
                    $act['id_impuesto'] = $act['id_impuesto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objventa_impuesto->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_impuesto'] = $objimpuesto->searchDB($act['id_impuesto'], 'id', 1);
                    $act['id_impuesto'] = $act['id_impuesto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>