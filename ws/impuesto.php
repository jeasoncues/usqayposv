<?php

require_once('../nucleo/impuesto.php');
$objimpuesto = new impuesto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objimpuesto->setVar('id', $_POST['id']);
            $objimpuesto->setVar('nombre', $_POST['nombre']);
            $objimpuesto->setVar('valor', $_POST['valor']);
            $objimpuesto->setVar('tipo', $_POST['tipo']);
            $objimpuesto->setVar('cargo', $_POST['cargo']);
            $objimpuesto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objimpuesto->insertDB());
            break;

        case 'mod':
            $objimpuesto->setVar('id', $_POST['id']);
            $objimpuesto->setVar('nombre', $_POST['nombre']);
            $objimpuesto->setVar('valor', $_POST['valor']);
            $objimpuesto->setVar('tipo', $_POST['tipo']);
            $objimpuesto->setVar('cargo', $_POST['cargo']);
            $objimpuesto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objimpuesto->updateDB());
            break;

        case 'del':
            $objimpuesto->setVar('id', $_POST['id']);
            echo json_encode($objimpuesto->deleteDB());
            break;

        case 'get':
            $res = $objimpuesto->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objimpuesto->listDB();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objimpuesto->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>