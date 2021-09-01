<?php

require_once('../nucleo/turno.php');
$objturno = new turno();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objturno->setVar('id', $_POST['id']);
            $objturno->setVar('nombre', $_POST['nombre']);
            $objturno->setVar('inicio', $_POST['inicio']);
            $objturno->setVar('fin', $_POST['fin']);
            $objturno->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objturno->insertDB());
            break;

        case 'mod':
            $objturno->setVar('id', $_POST['id']);
            $objturno->setVar('nombre', $_POST['nombre']);
            $objturno->setVar('inicio', $_POST['inicio']);
            $objturno->setVar('fin', $_POST['fin']);
            $objturno->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objturno->updateDB());
            break;

        case 'del':
            $objturno->setVar('id', $_POST['id']);
            echo json_encode($objturno->deleteDB());
            break;

        case 'get':
            $res = $objturno->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objturno->listDB();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objturno->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>