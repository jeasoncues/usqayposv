<?php

require_once('../nucleo/venta_taxonomiav.php');
$objventa_taxonomiav = new venta_taxonomiav();

require_once('../nucleo/venta.php');
$objventa = new venta();

require_once('../nucleo/taxonomiav.php');
$objtaxonomiav = new taxonomiav();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objventa_taxonomiav->setVar('id', $_POST['id']);
            $objventa_taxonomiav->setVar('id_venta', $_POST['id_venta']);
            $objventa_taxonomiav->setVar('id_taxonomiav', $_POST['id_taxonomiav']);
            $objventa_taxonomiav->setVar('valor', $_POST['valor']);
            $objventa_taxonomiav->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objventa_taxonomiav->insertDB());
            break;

        case 'mod':
            $objventa_taxonomiav->setVar('id', $_POST['id']);
            $objventa_taxonomiav->setVar('id_venta', $_POST['id_venta']);
            $objventa_taxonomiav->setVar('id_taxonomiav', $_POST['id_taxonomiav']);
            $objventa_taxonomiav->setVar('valor', $_POST['valor']);
            $objventa_taxonomiav->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objventa_taxonomiav->updateDB());
            break;

        case 'del':
            $objventa_taxonomiav->setVar('id', $_POST['id']);
            echo json_encode($objventa_taxonomiav->deleteDB());
            break;

        case 'get':
            $res = $objventa_taxonomiav->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_venta'] = $objventa->searchDB($res[0]['id_venta'], 'id', 1);
                $res[0]['id_venta'] = $res[0]['id_venta'][0];
                $res[0]['id_taxonomiav'] = $objtaxonomiav->searchDB($res[0]['id_taxonomiav'], 'id', 1);
                $res[0]['id_taxonomiav'] = $res[0]['id_taxonomiav'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objventa_taxonomiav->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_taxonomiav'] = $objtaxonomiav->searchDB($act['id_taxonomiav'], 'id', 1);
                    $act['id_taxonomiav'] = $act['id_taxonomiav'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objventa_taxonomiav->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_taxonomiav'] = $objtaxonomiav->searchDB($act['id_taxonomiav'], 'id', 1);
                    $act['id_taxonomiav'] = $act['id_taxonomiav'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>