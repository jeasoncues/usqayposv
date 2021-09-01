<?php

require_once('../nucleo/servicio_producto.php');
$objservicio_producto = new servicio_producto();

require_once('../nucleo/servicio.php');
$objservicio = new servicio();

require_once('../nucleo/producto.php');
$objproducto = new producto();

require_once('../nucleo/almacen.php');
$objalmacen = new almacen();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objservicio_producto->setVar('id', $_POST['id']);
            $objservicio_producto->setVar('id_servicio', $_POST['id_servicio']);
            $objservicio_producto->setVar('id_producto', $_POST['id_producto']);
            $objservicio_producto->setVar('cantidad', $_POST['cantidad']);
            $objservicio_producto->setVar('id_almacen', $_POST['id_almacen']);
            $objservicio_producto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objservicio_producto->insertDB());
            break;

        case 'mod':
            $objservicio_producto->setVar('id', $_POST['id']);
            $objservicio_producto->setVar('id_servicio', $_POST['id_servicio']);
            $objservicio_producto->setVar('id_producto', $_POST['id_producto']);
            $objservicio_producto->setVar('cantidad', $_POST['cantidad']);
            $objservicio_producto->setVar('id_almacen', $_POST['id_almacen']);
            $objservicio_producto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objservicio_producto->updateDB());
            break;

        case 'del':
            $objservicio_producto->setVar('id', $_POST['id']);
            echo json_encode($objservicio_producto->deleteDB());
            break;

        case 'get':
            $res = $objservicio_producto->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_servicio'] = $objservicio->searchDB($res[0]['id_servicio'], 'id', 1);
                $res[0]['id_servicio'] = $res[0]['id_servicio'][0];
                $res[0]['id_producto'] = $objproducto->searchDB($res[0]['id_producto'], 'id', 1);
                $res[0]['id_producto'] = $res[0]['id_producto'][0];
                $res[0]['id_almacen'] = $objalmacen->searchDB($res[0]['id_almacen'], 'id', 1);
                $res[0]['id_almacen'] = $res[0]['id_almacen'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objservicio_producto->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    $act['id_almacen'] = $objalmacen->searchDB($act['id_almacen'], 'id', 1);
                    $act['id_almacen'] = $act['id_almacen'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objservicio_producto->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    $act['id_almacen'] = $objalmacen->searchDB($act['id_almacen'], 'id', 1);
                    $act['id_almacen'] = $act['id_almacen'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>