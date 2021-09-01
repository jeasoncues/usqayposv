<?php

require_once('../nucleo/caja.php');
$objcaja = new caja();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objcaja->setVar('id', $_POST['id']);
            $objcaja->setVar('nombre', $_POST['nombre']);
            $objcaja->setVar('ubicacion', $_POST['ubicacion']);
            $objcaja->setVar('serie_impresora', $_POST['serie_impresora']);
            $objcaja->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objcaja->insertDB());
            break;

        case 'mod':
            $objcaja->setVar('id', $_POST['id']);
            $objcaja->setVar('nombre', $_POST['nombre']);
            $objcaja->setVar('ubicacion', $_POST['ubicacion']);
            $objcaja->setVar('serie_impresora', $_POST['serie_impresora']);
            $objcaja->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objcaja->updateDB());
            break;

        case 'del':
            $objcaja->setVar('id', $_POST['id']);
            echo json_encode($objcaja->deleteDB());
            break;

        case 'get':
            $res = $objcaja->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objcaja->listDB();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objcaja->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'cierreprint':
            $objconn = new caja();

            // echo $_POST["turno"].",".$_POST["caja"];
            $res = $objconn->consulta_simple("Insert into cola_impresion values(NULL,'".$_POST['fecha']."', 'CIE','".$_POST["id_caja"]."','".$_POST["turno"].",".$_POST["caja"].",".$_POST["id_usuario"]."', 1)");

            echo json_encode($res);
            break;
    }
}?>