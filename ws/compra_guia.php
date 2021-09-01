<?php

require_once('../nucleo/compra_guia.php');
$objcompra_guia = new compra_guia();

require_once('../nucleo/compra.php');
$objcompra = new compra();

require_once('../nucleo/guia_producto.php');
$objguia_producto = new guia_producto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objcompra_guia->setVar('id', $_POST['id']);
            $objcompra_guia->setVar('id_compra', $_POST['id_compra']);
            $objcompra_guia->setVar('id_guia_producto', $_POST['id_guia_producto']);
            $objcompra_guia->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objcompra_guia->insertDB());
            break;
        
        case 'assoc':
            $return = 0;
            $objconn = new compra();
            $res = $objconn->consulta_arreglo("Select * from guia_producto where numero_guia = '".$_POST["numero_guia"]."' order by id DESC LIMIT 1");
            
            if(is_array($res)){     
                $objcompra_guia = new compra_guia();
                $objcompra_guia->setVar('id_compra', $_POST['id_compra']);
                $objcompra_guia->setVar('id_guia_producto', $res["id"]);
                $objcompra_guia->setVar('estado_fila',"1");
                $return = $objcompra_guia->insertDB();
            }

            echo json_encode($return);
            break;
        
        case 'assocCotos':
                $return = 0;
                $objconn = new compra();
                $res = $objconn->consulta_arreglo("Select * from guia_producto where id = '".$_POST["numero_guia"]."' order by id DESC LIMIT 1");
                
                if(is_array($res)){
                    $objcompra_guia = new compra_guia();
                    $objcompra_guia->setVar('id_compra', $_POST['id_compra']);
                    $objcompra_guia->setVar('id_guia_producto', $res["id"]);
                    $objcompra_guia->setVar('estado_fila',"1");
                    $return = $objcompra_guia->insertDB();
                }
    
                echo json_encode($return);
        break;

        case 'mod':
            $objcompra_guia->setVar('id', $_POST['id']);
            $objcompra_guia->setVar('id_compra', $_POST['id_compra']);
            $objcompra_guia->setVar('id_guia_producto', $_POST['id_guia_producto']);
            $objcompra_guia->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objcompra_guia->updateDB());
            break;

        case 'del':
            $objcompra_guia->setVar('id', $_POST['id']);
            echo json_encode($objcompra_guia->deleteDB());
            break;

        case 'get':
            $res = $objcompra_guia->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_compra'] = $objcompra->searchDB($res[0]['id_compra'], 'id', 1);
                $res[0]['id_compra'] = $res[0]['id_compra'][0];
                $res[0]['id_guia_producto'] = $objguia_producto->searchDB($res[0]['id_guia_producto'], 'id', 1);
                $res[0]['id_guia_producto'] = $res[0]['id_guia_producto'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objcompra_guia->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_compra'] = $objcompra->searchDB($act['id_compra'], 'id', 1);
                    $act['id_compra'] = $act['id_compra'][0];
                    $act['id_guia_producto'] = $objguia_producto->searchDB($act['id_guia_producto'], 'id', 1);
                    $act['id_guia_producto'] = $act['id_guia_producto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objcompra_guia->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_compra'] = $objcompra->searchDB($act['id_compra'], 'id', 1);
                    $act['id_compra'] = $act['id_compra'][0];
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