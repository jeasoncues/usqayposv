<?php
require_once('../nucleo/movimiento_producto.php');
$objmovimiento_producto = new movimiento_producto();

require_once('../nucleo/materia_prima.php');
$objmateria_prima = new materia_prima();

require_once('../nucleo/almacen.php');
$objalmacen_origen = new almacen();

require_once('../nucleo/producto.php');
$objproducto_origen = new producto();

$objproducto_destino = new producto();

$objalmacen_destino = new almacen();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objmateria_prima->setVar('id', $_POST['id']);
            $objmateria_prima->setVar('id_almacen_origen', $_POST['id_almacen_origen']);
            $objmateria_prima->setVar('id_producto_origen', $_POST['id_producto_origen']);
            $objmateria_prima->setVar('id_producto_destino', $_POST['id_producto_destino']);
            $objmateria_prima->setVar('id_almacen_destino', $_POST['id_almacen_destino']);
            $objmateria_prima->setVar('cantidad', $_POST['cantidad']);
            $objmateria_prima->setVar('merma', $_POST['merma']);
            $objmateria_prima->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objmateria_prima->insertDB());
            break;

        case 'mod':
            $objmateria_prima->setVar('id', $_POST['id']);
            $objmateria_prima->setVar('id_almacen_origen', $_POST['id_almacen_origen']);
            $objmateria_prima->setVar('id_producto_origen', $_POST['id_producto_origen']);
            $objmateria_prima->setVar('id_producto_destino', $_POST['id_producto_destino']);
            $objmateria_prima->setVar('id_almacen_destino', $_POST['id_almacen_destino']);
            $objmateria_prima->setVar('cantidad', $_POST['cantidad']);
            $objmateria_prima->setVar('merma', $_POST['merma']);
            $objmateria_prima->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objmateria_prima->updateDB());
            break;

        case 'del':
            $objmateria_prima->setVar('id', $_POST['id']);
            echo json_encode($objmateria_prima->deleteDB());
            break;

        case 'get':
            $res = $objmateria_prima->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_almacen_origen'] = $objalmacen_origen->searchDB($res[0]['id_almacen_origen'], 'id', 1);
                $res[0]['id_almacen_origen'] = $res[0]['id_almacen_origen'][0];
                $res[0]['id_producto_origen'] = $objproducto_origen->searchDB($res[0]['id_producto_origen'], 'id', 1);
                $res[0]['id_producto_origen'] = $res[0]['id_producto_origen'][0];
                $res[0]['id_producto_destino'] = $objproducto_destino->searchDB($res[0]['id_producto_destino'], 'id', 1);
                $res[0]['id_producto_destino'] = $res[0]['id_producto_destino'][0];
                $res[0]['id_almacen_destino'] = $objalmacen_destino->searchDB($res[0]['id_almacen_destino'], 'id', 1);
                $res[0]['id_almacen_destino'] = $res[0]['id_almacen_destino'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objmateria_prima->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_almacen_origen'] = $objalmacen_origen->searchDB($act['id_almacen_origen'], 'id', 1);
                    $act['id_almacen_origen'] = $act['id_almacen_origen'][0];
                    $act['id_producto_origen'] = $objproducto_origen->searchDB($act['id_producto_origen'], 'id', 1);
                    $act['id_producto_origen'] = $act['id_producto_origen'][0];
                    $act['id_producto_destino'] = $objproducto_destino->searchDB($act['id_producto_destino'], 'id', 1);
                    $act['id_producto_destino'] = $act['id_producto_destino'][0];
                    $act['id_almacen_destino'] = $objalmacen_destino->searchDB($act['id_almacen_destino'], 'id', 1);
                    $act['id_almacen_destino'] = $act['id_almacen_destino'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objmateria_prima->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_almacen_origen'] = $objalmacen_origen->searchDB($act['id_almacen_origen'], 'id', 1);
                    $act['id_almacen_origen'] = $act['id_almacen_origen'][0];
                    $act['id_producto_origen'] = $objproducto_origen->searchDB($act['id_producto_origen'], 'id', 1);
                    $act['id_producto_origen'] = $act['id_producto_origen'][0];
                    $act['id_producto_destino'] = $objproducto_destino->searchDB($act['id_producto_destino'], 'id', 1);
                    $act['id_producto_destino'] = $act['id_producto_destino'][0];
                    $act['id_almacen_destino'] = $objalmacen_destino->searchDB($act['id_almacen_destino'], 'id', 1);
                    $act['id_almacen_destino'] = $act['id_almacen_destino'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>