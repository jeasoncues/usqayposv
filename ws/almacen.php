<?php

require_once('../nucleo/almacen.php');
$objalmacen = new almacen();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objalmacen->setVar('id', $_POST['id']);
            $objalmacen->setVar('nombre', $_POST['nombre']);
            $objalmacen->setVar('ubicacion', $_POST['ubicacion']);
            $objalmacen->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objalmacen->insertDB());
            break;

        case 'mod':
            $objalmacen->setVar('id', $_POST['id']);
            $objalmacen->setVar('nombre', $_POST['nombre']);
            $objalmacen->setVar('ubicacion', $_POST['ubicacion']);
            $objalmacen->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objalmacen->updateDB());
            break;

        case 'del':
            $objalmacen->setVar('id', $_POST['id']);
            echo json_encode($objalmacen->deleteDB());
            break;

        case 'get':
            $res = $objalmacen->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objalmacen->listDB();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objalmacen->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'verifica':
            if (file_exists("../".$_POST["ruta"])) {
                echo json_encode(1);
            }else{
                echo json_encode(0);
            }
            break;
            
        case 'desvincular':
            $desvincular = $objalmacen->consulta_simple("DELETE FROM compra_guia WHERE id_compra = ".$_POST['id']." AND id_guia_producto = ".$_POST['guia']);

            echo $desvincular;
            break;

        case 'list_cotos':
            $res = $objalmacen->consulta_matriz("SELECT * FROM almacen WHERE estado_fila=1 AND id<>".$_POST['id']);
            //$res = $objalmacen->consulta_matriz("SELECT * FROM almacen WHERE estado_fila=1");           
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
        break;   
    }
}?>